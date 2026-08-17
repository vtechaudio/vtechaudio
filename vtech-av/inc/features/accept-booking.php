<?php
/**
 * VTECH: Accept Quote -> auto-create Booking (inventory lock).
 *
 * Adds a secure "Accept this quote" link to every quote. When a client accepts:
 *   1. Verifies the accept token.
 *   2. Re-checks live availability for the quoted equipment + dates.
 *   3. Creates a CONFIRMED booking (locks inventory) via vtech_create_booking().
 *   4. Auto-generates the deposit invoice from the quote.
 *   5. Emails the owner + client with the invoice + payment link.
 *   6. Marks the quote accepted (idempotent — cannot double-book).
 *
 * Depends on inventory.php + quote-invoice.php (loaded earlier).
 *
 * @package VTECH_AV
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/* Accept token (distinct from the view token). */
function vtech_accept_token( $quote_id ) {
	return substr( wp_hash( 'vtech_accept_' . $quote_id ), 0, 24 );
}
function vtech_accept_url( $quote_id ) {
	return add_query_arg( array( 'vtech_accept' => $quote_id, 'token' => vtech_accept_token( $quote_id ) ), home_url( '/' ) );
}

/**
 * Resolve which equipment units a quote reserves.
 * Priority: explicit 'booking_items' meta (equipment_id=>qty) if set,
 * else derive from the source request payload via the same rate-card keys
 * mapped to equipment posts by matching the item label.
 */
function vtech_quote_booking_items( $quote_id ) {
	$explicit = json_decode( (string) get_post_meta( $quote_id, 'booking_items', true ), true );
	if ( is_array( $explicit ) && $explicit ) { return array_map( 'intval', $explicit ); }

	$items = array();
	$req = (int) get_post_meta( $quote_id, 'source_request', true );
	if ( ! $req ) { return $items; }
	$payload = json_decode( (string) get_post_meta( $req, 'vtech_payload', true ), true ) ?: array();

	// Map rate-card keys -> quantity, then match each to an equipment post by title.
	$rates = apply_filters( 'vtech_rate_card', array() );
	foreach ( $rates as $key => $r ) {
		$qty = (int) ( $payload[ $key ] ?? 0 );
		if ( $qty < 1 ) { continue; }
		$label = $r[0];
		$mq = new WP_Query( array( 'post_type' => 'equipment', 'title' => $label, 'posts_per_page' => 1, 'fields' => 'ids', 'no_found_rows' => true ) );
		if ( ! empty( $mq->posts ) ) { $mid = (int) $mq->posts[0]; $items[ $mid ] = ( $items[ $mid ] ?? 0 ) + $qty; }
		wp_reset_postdata();
	}
	return $items;
}

/** Extract quote date range (from source request event/setup/collection dates). */
function vtech_quote_dates( $quote_id ) {
	$start = get_post_meta( $quote_id, 'start_date', true );
	$end   = get_post_meta( $quote_id, 'end_date', true );
	if ( $start ) { return array( $start, $end ?: $start ); }

	$req = (int) get_post_meta( $quote_id, 'source_request', true );
	if ( $req ) {
		$p = json_decode( (string) get_post_meta( $req, 'vtech_payload', true ), true ) ?: array();
		$s = $p['setup_date'] ?? ( $p['event_date'] ?? '' );
		$e = $p['collection_date'] ?? ( $p['event_date'] ?? $s );
		if ( $s ) { return array( $s, $e ?: $s ); }
	}
	// Default: today -> today (owner can adjust the booking).
	$today = gmdate( 'Y-m-d' );
	return array( $today, $today );
}

/* ---------------------------------------------------------------------------
 * Public acceptance endpoint.
 * ------------------------------------------------------------------------- */
add_action( 'template_redirect', function () {
	if ( empty( $_GET['vtech_accept'] ) ) { return; }
	$quote_id = (int) $_GET['vtech_accept'];
	$token    = sanitize_text_field( wp_unslash( $_GET['token'] ?? '' ) );

	if ( ! hash_equals( vtech_accept_token( $quote_id ), $token ) ) {
		wp_die( 'Invalid or expired acceptance link.' );
	}
	if ( 'vtech_quote' !== get_post_type( $quote_id ) ) { wp_die( 'Document not found.' ); }

	// Idempotent: if already accepted, show the existing confirmation.
	$existing_booking = (int) get_post_meta( $quote_id, 'accepted_booking', true );
	if ( $existing_booking ) {
		echo vtech_accept_confirmation_html( $quote_id, $existing_booking, (int) get_post_meta( $quote_id, 'accepted_invoice', true ), false ); // phpcs:ignore
		exit;
	}

	list( $start, $end ) = vtech_quote_dates( $quote_id );
	$items = vtech_quote_booking_items( $quote_id );

	// Availability re-check — never overbook at acceptance time.
	$conflicts = array();
	foreach ( $items as $eid => $qty ) {
		$avail = vtech_units_available( $eid, $start, $end );
		if ( $avail < $qty ) { $conflicts[] = get_the_title( $eid ) . ' (need ' . $qty . ', ' . $avail . ' free)'; }
	}
	if ( $conflicts ) {
		wp_die( 'Sorry — some items are no longer available for these dates: ' . esc_html( implode( '; ', $conflicts ) ) . '. Please contact us to adjust dates.' );
	}

	// Create the CONFIRMED booking (locks inventory).
	$client = json_decode( (string) get_post_meta( $quote_id, 'client', true ), true ) ?: array();
	$ref    = get_post_meta( $quote_id, 'ref', true );
	$booking_id = vtech_create_booking(
		'Booking for ' . $ref . ' — ' . ( $client['name'] ?? 'Client' ),
		$start, $end, $items, 'confirmed',
		array( 'source_quote' => $quote_id, 'client_email' => $client['email'] ?? '' )
	);

	// Auto-generate the deposit invoice from the quote's lines.
	$lines = json_decode( (string) get_post_meta( $quote_id, 'lines', true ), true ) ?: array();
	$inv   = vtech_create_quote( 'invoice', $client, $lines, array( 'source_quote' => $quote_id, 'source_request' => (int) get_post_meta( $quote_id, 'source_request', true ) ) );
	$invoice_id = is_array( $inv ) ? $inv['id'] : 0;

	// Persist links + status (idempotency).
	update_post_meta( $quote_id, 'status', 'accepted' );
	update_post_meta( $quote_id, 'accepted_at', current_time( 'mysql' ) );
	update_post_meta( $quote_id, 'accepted_booking', $booking_id );
	update_post_meta( $quote_id, 'accepted_invoice', $invoice_id );
	if ( $booking_id ) { update_post_meta( $booking_id, 'linked_invoice', $invoice_id ); }

	// Notify owner + client.
	$to = get_theme_mod( 'vtech_email', 'info@vtechaudio.co.ke' );
	$owner_body = "Quote {$ref} was ACCEPTED.\n\nBooking #{$booking_id} created and inventory locked for {$start} to {$end}.\nDeposit invoice generated: " . ( $invoice_id ? vtech_doc_url( $invoice_id ) : 'n/a' ) . "\n\nClient: " . ( $client['name'] ?? '' ) . " <" . ( $client['email'] ?? '' ) . ">";
	wp_mail( $to, "[ACCEPTED] {$ref} — booking confirmed", $owner_body, array( 'Content-Type: text/plain; charset=UTF-8' ) );

	if ( ! empty( $client['email'] ) && $invoice_id ) {
		$cbody = "Hello " . ( $client['name'] ?? '' ) . ",\n\nThank you for accepting quote {$ref}. Your booking is reserved for {$start} to {$end}.\n\nTo confirm, please pay the deposit shown on your invoice:\n" . vtech_doc_url( $invoice_id ) . "\n\nRegards,\nVTECH Audio Visual Solutions\n" . get_theme_mod( 'vtech_phone', '+254 728 135 246' );
		wp_mail( $client['email'], "Booking reserved — deposit invoice {$ref}", $cbody, array( 'Content-Type: text/plain; charset=UTF-8' ) );
	}

	echo vtech_accept_confirmation_html( $quote_id, $booking_id, $invoice_id, true ); // phpcs:ignore
	exit;
} );

function vtech_accept_confirmation_html( $quote_id, $booking_id, $invoice_id, $fresh ) {
	$ref = get_post_meta( $quote_id, 'ref', true );
	$primary = '#81007F';
	$inv_url = $invoice_id ? vtech_doc_url( $invoice_id ) : '';
	$pay = $invoice_id && function_exists( 'vtech_payment_button' ) ? vtech_payment_button( $invoice_id ) : '';
	ob_start(); ?>
<!doctype html><html><head><meta charset="utf-8"><title>Quote accepted</title>
<style>body{font-family:'Helvetica Neue',Arial,sans-serif;background:#faf6fa;color:#111;display:flex;min-height:100vh;align-items:center;justify-content:center;margin:0;padding:24px}
.card{background:#fff;border-radius:16px;box-shadow:0 20px 60px rgba(129,0,127,.15);max-width:520px;padding:40px;text-align:center}
h1{color:<?php echo esc_attr( $primary ); ?>;font-size:26px;margin:0 0 8px}
.ok{width:64px;height:64px;border-radius:50%;background:<?php echo esc_attr( $primary ); ?>;color:#fff;display:flex;align-items:center;justify-content:center;font-size:34px;margin:0 auto 20px}
.btn{display:inline-block;margin-top:20px;padding:12px 22px;background:<?php echo esc_attr( $primary ); ?>;color:#fff;border-radius:10px;text-decoration:none;font-weight:600}
.muted{color:#666;font-size:14px}</style></head><body>
<div class="card">
<div class="ok">&#10003;</div>
<h1><?php echo esc_html( $fresh ? 'Quote Accepted' : 'Already Accepted' ); ?></h1>
<p class="muted">Reference <strong><?php echo esc_html( $ref ); ?></strong> is confirmed and your equipment is reserved.</p>
<?php if ( $inv_url ) : ?><p><a class="btn" href="<?php echo esc_url( $inv_url ); ?>">View Deposit Invoice</a></p><?php endif; ?>
<?php echo $pay; // phpcs:ignore ?>
<p class="muted" style="margin-top:24px">Our team will be in touch shortly. Thank you for choosing VTECH Audio Visual Solutions.</p>
</div></body></html>
<?php
	return ob_get_clean();
}

/* ---------------------------------------------------------------------------
 * Surface the accept link in the admin (quote meta box) + on the quote doc.
 * ------------------------------------------------------------------------- */
add_action( 'add_meta_boxes', function () {
	add_meta_box( 'vtech_quote_accept', 'Acceptance & Booking', function ( $post ) {
		if ( 'quote' !== ( get_post_meta( $post->ID, 'doc_type', true ) ?: 'quote' ) ) {
			echo '<p>Invoices are generated from accepted quotes.</p>'; return;
		}
		$status = get_post_meta( $post->ID, 'status', true );
		$booking = (int) get_post_meta( $post->ID, 'accepted_booking', true );
		echo '<p><strong>Status:</strong> ' . esc_html( ucfirst( $status ?: 'sent' ) ) . '</p>';
		echo '<p><strong>Client accept link:</strong><br><input type="text" readonly style="width:100%" value="' . esc_attr( vtech_accept_url( $post->ID ) ) . '"></p>';
		echo '<p class="description">Send this link (or the quote) to the client. Accepting it auto-creates a confirmed booking, locks inventory, and issues the deposit invoice.</p>';
		if ( $booking ) {
			echo '<p><strong>Booking:</strong> <a href="' . esc_url( admin_url( 'post.php?post=' . $booking . '&action=edit' ) ) . '">#' . (int) $booking . '</a> (inventory locked)</p>';
		}
	}, 'vtech_quote', 'side', 'high' );
} );

/* Add an "Accept this quote" button onto the printable quote document. */
