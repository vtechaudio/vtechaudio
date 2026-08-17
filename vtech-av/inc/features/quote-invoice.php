<?php
/**
 * VTECH Quote & Invoice generation.
 *
 * Generates branded, printable quotes and invoices from a hire request or a
 * package. Two output modes:
 *   1. HTML print view (styled for A4, one-click "Save as PDF" via browser) —
 *      works everywhere with zero dependencies.
 *   2. If a PDF library (Dompdf) is present, streams a true PDF automatically.
 *
 * Documents are created as a private "vtech_quote" CPT so they are re-openable,
 * carry a reference (VTA-QUOTE / VTA-INV), and can be converted quote -> invoice.
 *
 * @package VTECH_AV
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

add_action( 'init', function () {
	register_post_type( 'vtech_quote', array(
		'labels'          => array( 'name' => 'Quotes & Invoices', 'singular_name' => 'Quote', 'menu_name' => 'Quotes & Invoices' ),
		'public'          => false,
		'show_ui'         => true,
		'show_in_menu'    => true,
		'menu_icon'       => 'dashicons-media-document',
		'menu_position'   => 30,
		'capability_type' => 'post',
		'supports'        => array( 'title' ),
	) );
} );

/**
 * Create a quote/invoice record.
 * $lines = array( array( 'desc'=>, 'qty'=>, 'unit'=>, 'amount'=> ), ... )
 */
function vtech_create_quote( $type, $client, $lines, $meta = array() ) {
	$is_invoice = ( 'invoice' === $type );
	$ref = vtech_next_reference( $is_invoice ? 'INV' : 'QUOTE' );

	$subtotal = 0.0;
	foreach ( $lines as $l ) { $subtotal += (float) ( $l['amount'] ?? 0 ); }
	$vat_rate = (float) get_theme_mod( 'vtech_vat_rate', 16 );
	$vat      = round( $subtotal * $vat_rate / 100, 2 );
	$total    = $subtotal + $vat;
	$deposit_pct = (float) ( $meta['deposit_pct'] ?? get_theme_mod( 'vtech_deposit_pct', 50 ) );
	$deposit  = round( $total * $deposit_pct / 100, 2 );

	$post_id = wp_insert_post( array(
		'post_type'   => 'vtech_quote',
		'post_status' => 'private',
		'post_title'  => $ref . ' — ' . ( $client['name'] ?? 'Client' ),
	) );
	if ( is_wp_error( $post_id ) || ! $post_id ) { return 0; }

	update_post_meta( $post_id, 'doc_type', $is_invoice ? 'invoice' : 'quote' );
	update_post_meta( $post_id, 'ref', $ref );
	update_post_meta( $post_id, 'client', wp_json_encode( $client ) );
	update_post_meta( $post_id, 'lines', wp_json_encode( array_values( $lines ) ) );
	update_post_meta( $post_id, 'subtotal', $subtotal );
	update_post_meta( $post_id, 'vat_rate', $vat_rate );
	update_post_meta( $post_id, 'vat', $vat );
	update_post_meta( $post_id, 'total', $total );
	update_post_meta( $post_id, 'deposit_pct', $deposit_pct );
	update_post_meta( $post_id, 'deposit', $deposit );
	foreach ( $meta as $k => $v ) { if ( ! in_array( $k, array( 'deposit_pct' ), true ) ) { update_post_meta( $post_id, sanitize_key( $k ), $v ); } }

	return array( 'id' => $post_id, 'ref' => $ref, 'total' => $total, 'deposit' => $deposit );
}

/** Build line items from a hire_request payload (best-effort auto-pricing). */
function vtech_lines_from_request( $request_id ) {
	$payload = json_decode( (string) get_post_meta( $request_id, 'vtech_payload', true ), true );
	if ( ! is_array( $payload ) ) { $payload = array(); }
	$lines = array();
	// Map known quantity fields -> a rate card (admin-editable via theme mod later).
	$rates = apply_filters( 'vtech_rate_card', array(
		'spk_tops' => array( 'Main PA Speakers', 4000 ),
		'spk_subs' => array( 'Subwoofers', 4500 ),
		'spk_monitors' => array( 'Stage Monitors', 3000 ),
		'mic_hh_wl' => array( 'Wireless Handheld Mics', 1500 ),
		'mic_lapel' => array( 'Lapel Mics', 1500 ),
		'lt_par' => array( 'PAR Lights', 800 ),
		'lt_moving' => array( 'Moving Heads', 2500 ),
		'vid_projectors' => array( 'Projectors', 8000 ),
		'vid_ptz' => array( 'PTZ Cameras', 12000 ),
	) );
	foreach ( $rates as $key => $r ) {
		$qty = (int) ( $payload[ $key ] ?? 0 );
		if ( $qty > 0 ) {
			$lines[] = array( 'desc' => $r[0], 'qty' => $qty, 'unit' => $r[1], 'amount' => $qty * $r[1] );
		}
	}
	if ( empty( $lines ) ) {
		$lines[] = array( 'desc' => 'AV services (as per request) — to be itemised', 'qty' => 1, 'unit' => 0, 'amount' => 0 );
	}
	return $lines;
}

/* ---------------------------------------------------------------------------
 * Render the printable A4 document (HTML). Streams a PDF if Dompdf exists.
 * URL: /?vtech_doc=ID&token=HASH
 * ------------------------------------------------------------------------- */
function vtech_doc_token( $id ) {
	return substr( wp_hash( 'vtech_doc_' . $id ), 0, 20 );
}

add_action( 'template_redirect', function () {
	if ( empty( $_GET['vtech_doc'] ) ) { return; }
	$id = (int) $_GET['vtech_doc'];
	$token = sanitize_text_field( wp_unslash( $_GET['token'] ?? '' ) );
	$is_admin = current_user_can( 'edit_posts' );
	if ( ! $is_admin && ! hash_equals( vtech_doc_token( $id ), $token ) ) {
		wp_die( 'Invalid or expired document link.' );
	}
	// Real downloadable PDF via the bundled self-contained writer.
	if ( isset( $_GET['pdf'] ) || isset( $_GET['download'] ) ) {
		if ( class_exists( 'VTECH_PDF' ) ) {
			vtech_stream_pdf( $id, ! isset( $_GET['inline'] ) );
			exit;
		}
	}
	// HTML view (also offers browser Save-as-PDF).
	echo vtech_render_doc_html( $id ); // phpcs:ignore
	exit;
} );

function vtech_render_doc_html( $id ) {
	$type  = get_post_meta( $id, 'doc_type', true ) ?: 'quote';
	$ref   = get_post_meta( $id, 'ref', true );
	$client= json_decode( (string) get_post_meta( $id, 'client', true ), true ) ?: array();
	$lines = json_decode( (string) get_post_meta( $id, 'lines', true ), true ) ?: array();
	$subtotal = (float) get_post_meta( $id, 'subtotal', true );
	$vat_rate = (float) get_post_meta( $id, 'vat_rate', true );
	$vat   = (float) get_post_meta( $id, 'vat', true );
	$total = (float) get_post_meta( $id, 'total', true );
	$deposit = (float) get_post_meta( $id, 'deposit', true );
	$deposit_pct = (float) get_post_meta( $id, 'deposit_pct', true );

	$company = 'VTECH Audio Visual Solutions';
	$addr    = get_theme_mod( 'vtech_address', 'Mpaka Plaza, Mpaka Road, Nairobi' );
	$phone   = get_theme_mod( 'vtech_phone', '+254 728 135 246' );
	$email   = get_theme_mod( 'vtech_email', 'info@vtechaudio.co.ke' );
	$primary = '#81007F';
	$title   = ( 'invoice' === $type ) ? 'INVOICE' : 'QUOTATION';
	$kes = function ( $n ) { return 'KES ' . number_format( (float) $n, 2 ); };

	$rows = '';
	foreach ( $lines as $l ) {
		$rows .= '<tr><td>' . esc_html( $l['desc'] ?? '' ) . '</td><td class="c">' . esc_html( $l['qty'] ?? '' ) . '</td><td class="r">' . esc_html( $kes( $l['unit'] ?? 0 ) ) . '</td><td class="r">' . esc_html( $kes( $l['amount'] ?? 0 ) ) . '</td></tr>';
	}

	$pay = get_theme_mod( 'vtech_pay_instructions', "M-Pesa Paybill: 000000, Account: " . $ref . "\nBank: VTECH Audio Visual Solutions — provide on request." );

	ob_start(); ?>
<!doctype html><html><head><meta charset="utf-8"><title><?php echo esc_html( $ref ); ?></title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Helvetica Neue',Arial,sans-serif;color:#111;font-size:13px;line-height:1.5;padding:32px}
.doc{max-width:800px;margin:auto}
.head{display:flex;justify-content:space-between;align-items:flex-start;border-bottom:3px solid <?php echo esc_attr( $primary ); ?>;padding-bottom:16px;margin-bottom:24px}
.brand{font-size:22px;font-weight:800;color:<?php echo esc_attr( $primary ); ?>}
.brand small{display:block;font-size:11px;color:#555;font-weight:400}
.doctype{text-align:right}
.doctype h1{font-size:26px;letter-spacing:2px;color:<?php echo esc_attr( $primary ); ?>}
.doctype .ref{font-size:12px;color:#555;margin-top:4px}
.meta{display:flex;justify-content:space-between;margin-bottom:24px;gap:24px}
.meta div{font-size:12px}
.meta h3{font-size:11px;text-transform:uppercase;letter-spacing:1px;color:#888;margin-bottom:4px}
table{width:100%;border-collapse:collapse;margin-bottom:16px}
th{background:<?php echo esc_attr( $primary ); ?>;color:#fff;text-align:left;padding:10px;font-size:11px;text-transform:uppercase;letter-spacing:.5px}
td{padding:10px;border-bottom:1px solid #eee}
.c{text-align:center}.r{text-align:right}
.totals{width:280px;margin-left:auto}
.totals tr td{border:0;padding:6px 10px}
.totals .grand{font-weight:800;font-size:15px;border-top:2px solid <?php echo esc_attr( $primary ); ?>}
.deposit{background:#f3e6f2;border-radius:8px;padding:12px 16px;margin:16px 0}
.pay{background:#fafafa;border:1px solid #eee;border-radius:8px;padding:12px 16px;margin-top:16px;white-space:pre-line;font-size:12px}
.foot{margin-top:32px;font-size:11px;color:#888;border-top:1px solid #eee;padding-top:12px}
.print-btn{margin:0 0 20px;padding:10px 18px;background:<?php echo esc_attr( $primary ); ?>;color:#fff;border:0;border-radius:8px;font-size:13px;cursor:pointer}
@media print{.print-btn{display:none}body{padding:0}}
</style></head><body>
<div class="doc">
<a class="print-btn" href="?vtech_doc=<?php echo (int) $id; ?>&token=<?php echo esc_attr( vtech_doc_token( $id ) ); ?>&pdf=1" style="text-decoration:none;display:inline-block">Download PDF</a> <button class="print-btn" onclick="window.print()" style="margin-left:8px">Print</button>
<div class="head">
	<div class="brand"><?php echo esc_html( $company ); ?><small><?php echo esc_html( $addr ); ?> · <?php echo esc_html( $phone ); ?> · <?php echo esc_html( $email ); ?></small></div>
	<div class="doctype"><h1><?php echo esc_html( $title ); ?></h1><div class="ref"><?php echo esc_html( $ref ); ?><br>Date: <?php echo esc_html( date_i18n( 'j M Y' ) ); ?></div></div>
</div>
<div class="meta">
	<div><h3>Bill To</h3><strong><?php echo esc_html( $client['name'] ?? '' ); ?></strong><br><?php echo esc_html( $client['company'] ?? '' ); ?><br><?php echo esc_html( $client['email'] ?? '' ); ?><br><?php echo esc_html( $client['phone'] ?? '' ); ?></div>
	<div><h3><?php echo esc_html( 'invoice' === $type ? 'Payment Due' : 'Valid Until' ); ?></h3><?php echo esc_html( date_i18n( 'j M Y', strtotime( '+30 days' ) ) ); ?></div>
</div>
<table>
	<thead><tr><th>Description</th><th class="c">Qty</th><th class="r">Unit (KES)</th><th class="r">Amount (KES)</th></tr></thead>
	<tbody><?php echo $rows; // phpcs:ignore ?></tbody>
</table>
<table class="totals">
	<tr><td>Subtotal</td><td class="r"><?php echo esc_html( $kes( $subtotal ) ); ?></td></tr>
	<tr><td>VAT (<?php echo esc_html( $vat_rate ); ?>%)</td><td class="r"><?php echo esc_html( $kes( $vat ) ); ?></td></tr>
	<tr class="grand"><td>Total</td><td class="r"><?php echo esc_html( $kes( $total ) ); ?></td></tr>
</table>
<div class="deposit"><strong>Deposit to confirm booking (<?php echo esc_html( $deposit_pct ); ?>%): <?php echo esc_html( $kes( $deposit ) ); ?></strong></div>
<div class="pay"><strong>Payment instructions</strong>
<?php echo esc_html( str_replace( '{REF}', $ref, $pay ) ); ?></div>
<div class="foot">This <?php echo esc_html( strtolower( $title ) ); ?> is subject to VTECH Audio Visual Solutions' terms &amp; conditions. A booking is confirmed only on acceptance and receipt of the required deposit. Prices in Kenya Shillings.</div>
</div>
<?php if ( isset( $_GET['print'] ) ) : ?><script>window.onload=function(){window.print()}</script><?php endif; ?>
</body></html>
<?php
	return ob_get_clean();
}

/** Public URL to view/download a doc. */
function vtech_doc_url( $id, $download = false ) {
	$args = array( 'vtech_doc' => $id, 'token' => vtech_doc_token( $id ) );
	if ( $download ) { $args['download'] = 1; }
	return add_query_arg( $args, home_url( '/' ) );
}

/**
 * Compose and stream a real PDF for a quote/invoice using the bundled writer.
 */
function vtech_stream_pdf( $id, $download = true ) {
	$type   = get_post_meta( $id, 'doc_type', true ) ?: 'quote';
	$ref    = get_post_meta( $id, 'ref', true );
	$client = json_decode( (string) get_post_meta( $id, 'client', true ), true ) ?: array();
	$lines  = json_decode( (string) get_post_meta( $id, 'lines', true ), true ) ?: array();
	$subtotal = (float) get_post_meta( $id, 'subtotal', true );
	$vat_rate = (float) get_post_meta( $id, 'vat_rate', true );
	$vat    = (float) get_post_meta( $id, 'vat', true );
	$total  = (float) get_post_meta( $id, 'total', true );
	$deposit = (float) get_post_meta( $id, 'deposit', true );
	$deposit_pct = (float) get_post_meta( $id, 'deposit_pct', true );
	$title  = ( 'invoice' === $type ) ? 'INVOICE' : 'QUOTATION';
	$kes = function ( $n ) { return 'KES ' . number_format( (float) $n, 2 ); };

	$pdf = new VTECH_PDF();
	$pdf->text( 'VTECH Audio Visual Solutions', 18, true, array( 0.505, 0.0, 0.498 ) );
	$pdf->text( get_theme_mod( 'vtech_address', 'Mpaka Plaza, Mpaka Road, Nairobi' ) . '  |  ' . get_theme_mod( 'vtech_phone', '+254 728 135 246' ) . '  |  ' . get_theme_mod( 'vtech_email', 'info@vtechaudio.co.ke' ), 9 );
	$pdf->rule();
	$pdf->heading( $title . '   ' . $ref );
	$pdf->text( 'Date: ' . date_i18n( 'j M Y' ) . '   |   ' . ( 'invoice' === $type ? 'Due: ' : 'Valid until: ' ) . date_i18n( 'j M Y', strtotime( '+30 days' ) ), 10 );
	$pdf->spacer( 6 );
	$pdf->text( 'Bill To:', 11, true );
	$pdf->text( trim( ( $client['name'] ?? '' ) . '   ' . ( $client['company'] ?? '' ) ), 10 );
	$pdf->text( trim( ( $client['email'] ?? '' ) . '   ' . ( $client['phone'] ?? '' ) ), 10 );
	$pdf->spacer( 8 );
	$pdf->row( 'Description', 'Qty', 'Unit', 'Amount', true );
	$pdf->rule();
	foreach ( $lines as $l ) {
		$pdf->row( substr( (string) ( $l['desc'] ?? '' ), 0, 52 ), (string) ( $l['qty'] ?? '' ), number_format( (float) ( $l['unit'] ?? 0 ) ), number_format( (float) ( $l['amount'] ?? 0 ) ) );
	}
	$pdf->rule();
	$pdf->row( '', '', 'Subtotal', $kes( $subtotal ) );
	$pdf->row( '', '', 'VAT (' . $vat_rate . '%)', $kes( $vat ) );
	$pdf->row( '', '', 'TOTAL', $kes( $total ), true );
	$pdf->spacer( 8 );
	$pdf->text( 'Deposit to confirm booking (' . $deposit_pct . '%): ' . $kes( $deposit ), 11, true, array( 0.505, 0.0, 0.498 ) );
	$pdf->spacer( 8 );
	$pay = get_theme_mod( 'vtech_pay_instructions', 'M-Pesa Paybill: 000000, Account: {REF}' );
	$pdf->text( 'Payment instructions:', 10, true );
	foreach ( explode( "\n", str_replace( '{REF}', $ref, $pay ) ) as $pl ) { if ( trim( $pl ) !== '' ) { $pdf->text( trim( $pl ), 10 ); } }
	$pdf->spacer( 10 );
	$pdf->text( "Subject to VTECH Audio Visual Solutions' terms & conditions. Prices in Kenya Shillings.", 8 );

	$pdf->output( $ref . '.pdf', $download );
}
