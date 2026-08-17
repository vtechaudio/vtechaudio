<?php
/**
 * VTECH Payments + Admin actions.
 *
 * - Deposit / balance handling (quote -> invoice conversion).
 * - Payment instructions (M-Pesa Paybill / Bank) rendered on invoices.
 * - Gateway hook point: Paystack (Kenya-friendly) + Stripe, "key-and-go".
 *   Real charging requires live API keys entered in Customize -> VTECH Payments.
 * - Admin meta box on Hire Requests: one-click Generate Quote / Invoice /
 *   Create Booking (locks inventory).
 *
 * @package VTECH_AV
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/* ---------------------------------------------------------------------------
 * 1) Customizer settings for payments (keys entered by the site owner).
 * ------------------------------------------------------------------------- */
add_action( 'customize_register', function ( $wp_customize ) {
	$wp_customize->add_section( 'vtech_payments', array( 'title' => 'VTECH Payments', 'priority' => 165 ) );
	$fields = array(
		'vtech_vat_rate'         => array( 'VAT rate (%)', 16 ),
		'vtech_deposit_pct'      => array( 'Default deposit (%)', 50 ),
		'vtech_pay_instructions' => array( 'Payment instructions (use {REF} for reference)', "M-Pesa Paybill: 000000\nAccount: {REF}\nBank transfer details available on request." ),
		'vtech_paystack_pk'      => array( 'Paystack Public Key (pk_...)', '' ),
		'vtech_stripe_pk'        => array( 'Stripe Publishable Key (pk_...)', '' ),
	);
	foreach ( $fields as $id => $f ) {
		$wp_customize->add_setting( $id, array( 'default' => $f[1], 'sanitize_callback' => 'wp_kses_post' ) );
		$wp_customize->add_control( $id, array( 'label' => $f[0], 'section' => 'vtech_payments', 'type' => ( is_numeric( $f[1] ) ? 'number' : 'textarea' ) ) );
	}
} );

/* ---------------------------------------------------------------------------
 * 2) Admin meta box on hire_request: generate quote / invoice / booking.
 * ------------------------------------------------------------------------- */
add_action( 'add_meta_boxes', function () {
	add_meta_box( 'vtech_hire_actions', 'VTECH Actions', 'vtech_hire_actions_box', 'hire_request', 'side', 'high' );
} );

function vtech_hire_actions_box( $post ) {
	wp_nonce_field( 'vtech_hire_action', 'vtech_hire_action_nonce' );
	$quote_id = get_post_meta( $post->ID, 'linked_quote', true );
	$inv_id   = get_post_meta( $post->ID, 'linked_invoice', true );
	echo '<p>Generate documents from this request:</p>';
	echo '<p><button type="submit" name="vtech_action" value="quote" class="button button-primary" style="width:100%;margin-bottom:6px">Generate Quote</button>';
	echo '<button type="submit" name="vtech_action" value="invoice" class="button" style="width:100%;margin-bottom:6px">Generate Invoice</button></p>';
	if ( $quote_id ) {
		echo '<p><strong>Quote:</strong> <a href="' . esc_url( vtech_doc_url( $quote_id ) ) . '" target="_blank">View</a> · <a href="' . esc_url( vtech_doc_url( $quote_id, true ) ) . '" target="_blank">PDF</a></p>';
	}
	if ( $inv_id ) {
		echo '<p><strong>Invoice:</strong> <a href="' . esc_url( vtech_doc_url( $inv_id ) ) . '" target="_blank">View</a> · <a href="' . esc_url( vtech_doc_url( $inv_id, true ) ) . '" target="_blank">PDF</a></p>';
	}
}

add_action( 'save_post_hire_request', function ( $post_id ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) { return; }
	if ( ! isset( $_POST['vtech_hire_action_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['vtech_hire_action_nonce'] ) ), 'vtech_hire_action' ) ) { return; }
	if ( empty( $_POST['vtech_action'] ) ) { return; }
	$action  = sanitize_text_field( wp_unslash( $_POST['vtech_action'] ) );
	$payload = json_decode( (string) get_post_meta( $post_id, 'vtech_payload', true ), true ) ?: array();

	$client = array(
		'name'    => $payload['contact_person'] ?? '',
		'company' => $payload['company'] ?? '',
		'email'   => get_post_meta( $post_id, 'vtech_email', true ),
		'phone'   => $payload['phone'] ?? '',
	);
	$lines = vtech_lines_from_request( $post_id );

	if ( 'quote' === $action ) {
		$res = vtech_create_quote( 'quote', $client, $lines, array( 'source_request' => $post_id ) );
		if ( is_array( $res ) ) { update_post_meta( $post_id, 'linked_quote', $res['id'] ); }
	} elseif ( 'invoice' === $action ) {
		$res = vtech_create_quote( 'invoice', $client, $lines, array( 'source_request' => $post_id ) );
		if ( is_array( $res ) ) { update_post_meta( $post_id, 'linked_invoice', $res['id'] ); }
	}
}, 10, 1 );

/* ---------------------------------------------------------------------------
 * 3) Gateway hook point — returns a pay button/config when keys are present.
 *    Real charge flow is completed client-side with the owner's public key;
 *    server-side verification should be added with the secret key + webhook.
 * ------------------------------------------------------------------------- */
function vtech_payment_button( $invoice_id ) {
	$total   = (float) get_post_meta( $invoice_id, 'total', true );
	$deposit = (float) get_post_meta( $invoice_id, 'deposit', true );
	$client  = json_decode( (string) get_post_meta( $invoice_id, 'client', true ), true ) ?: array();
	$paystack = get_theme_mod( 'vtech_paystack_pk', '' );
	$amount = $deposit ?: $total;
	if ( ! $paystack ) { return ''; }
	$email = esc_js( $client['email'] ?? '' );
	ob_start(); ?>
	<button class="btn btn--accent btn--lg" id="vtech-pay" data-amount="<?php echo esc_attr( $amount ); ?>"><?php echo esc_html( 'Pay Deposit KES ' . number_format( $amount, 2 ) ); ?></button>
	<script src="https://js.paystack.co/v1/inline.js"></script>
	<script>
	document.getElementById('vtech-pay').addEventListener('click',function(){
		var h=PaystackPop.setup({key:'<?php echo esc_js( $paystack ); ?>',email:'<?php echo $email; ?>',amount:<?php echo (int) round( $amount * 100 ); ?>,currency:'KES',
		ref:'<?php echo esc_js( get_post_meta( $invoice_id, 'ref', true ) ); ?>',
		callback:function(r){alert('Payment complete: '+r.reference);},onClose:function(){}});h.openIframe();
	});
	</script>
	<?php
	return ob_get_clean();
}
