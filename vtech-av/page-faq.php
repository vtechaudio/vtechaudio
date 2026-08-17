<?php
/**
 * Template Name: FAQ
 * Comprehensive audio-visual FAQ for the Kenyan market, with FAQPage schema.
 *
 * @package VTECH_AV
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();

$faqs = array(
	array( 'How much does a professional sound system cost in Kenya?', 'Costs depend on venue size, coverage and equipment tier. A small meeting-room PA can start from around KES 120,000, while a full church or auditorium line-array system runs into the millions. After a free site survey we provide a fixed, itemised quote within 24 hours.' ),
	array( 'Do you cover the whole of Kenya?', 'Yes. We design, install and support AV systems across all 47 counties in Kenya, and we take on select projects across East Africa (Uganda, Tanzania, Rwanda and beyond).' ),
	array( 'How fast can you quote my AV project?', 'We provide a fixed written quotation within 24 hours of a free site survey in Nairobi, and within 48 hours upcountry.' ),
	array( 'Do you offer equipment hire as well as installation?', 'Yes. We hire out sound, lighting, LED screens and conferencing equipment for events, with delivery, setup and an on-site technician. You can also request a ready-made hire package.' ),
	array( 'What brands of equipment do you work with?', 'We specify reliable, warrantied equipment from leading global manufacturers including Shure, Sennheiser, Yamaha, JBL, QSC, Allen & Heath, Bosch and Blackmagic Design — matched to your budget and application.' ),
	array( 'Can you set up live streaming for our church or event?', 'Absolutely. We build live-streaming systems for Facebook, YouTube, Zoom and Microsoft Teams, including cameras, switching (Blackmagic, Roland, vMix), audio embedding and reliable internet bonding.' ),
	array( 'Do you handle church sound systems specifically?', 'Yes — church audio is one of our specialities. We tune systems for clear speech and music, manage choir and instrument mics, and add streaming, translation and hearing-assistance where needed.' ),
	array( 'What is included in a conference-room / boardroom AV solution?', 'Typically video conferencing (Zoom/Teams/Google Meet/Webex), ceiling or table microphones, an auto-tracking camera, displays or a projector, wireless presentation and one-touch room control.' ),
	array( 'Do you provide acoustic treatment and soundproofing?', 'Yes. We diagnose echo, reverberation, noise and sound leakage, then design acoustic panels, bass traps, diffusers and isolation appropriate to your room and construction.' ),
	array( 'Do you install LED screens and video walls?', 'We supply and install indoor and outdoor LED screens and video walls for churches, events, retail, corporate and advertising, including content and control.' ),
	array( 'Do you offer maintenance after installation?', 'Every installation includes a 12-month support window covering workmanship, and we offer Annual Maintenance Contracts (AMC) with priority and emergency support.' ),
	array( 'Can you work with our existing equipment?', 'Where possible we assess and integrate compatible existing systems to protect your investment, upgrading only what is necessary.' ),
	array( 'What payment terms do you offer?', 'Projects are typically confirmed with a deposit, with the balance per the quotation. We accept M-Pesa and bank transfer, and can structure phased payments for larger installations.' ),
	array( 'Do you handle government tenders and NGO procurement?', 'Yes. We are experienced with tender documentation, compliance and phased delivery for government, NGO, hospital, school and corporate procurement.' ),
	array( 'How do I get started?', 'Book a free consultation. Tell us about your project or event, we survey the site where needed, and you receive a tailored quotation and technical recommendation — usually within 24 hours.' ),
);
?>
<section class="about-hero">
	<div class="container">
		<?php if ( function_exists( 'vtech_breadcrumbs' ) ) { vtech_breadcrumbs(); } ?>
		<h1><?php esc_html_e( 'Frequently Asked Questions', 'vtech-av' ); ?></h1>
		<p><?php esc_html_e( 'Answers to common questions about audio-visual systems, installation, hire and support across Kenya and East Africa.', 'vtech-av' ); ?></p>
	</div>
</section>

<section class="section"><div class="container narrow">
	<div class="faq">
		<?php foreach ( $faqs as $f ) : ?>
			<details class="faq__item"><summary><?php echo esc_html( $f[0] ); ?></summary><div class="faq__answer"><?php echo esc_html( $f[1] ); ?></div></details>
		<?php endforeach; ?>
	</div>
</div></section>

<section class="section"><div class="container"><div class="cta-band section"><div class="cta-band__inner">
	<h2><?php esc_html_e( 'Still have questions?', 'vtech-av' ); ?></h2>
	<p><?php esc_html_e( 'Book a free consultation and get expert advice tailored to your project.', 'vtech-av' ); ?></p>
	<a class="btn btn--accent btn--lg" href="<?php echo esc_url( home_url( '/consultation/' ) ); ?>"><?php esc_html_e( 'Book a Consultation', 'vtech-av' ); ?></a>
</div></div></div></section>

<?php
// FAQPage schema for rich results.
$ld = array( '@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => array() );
foreach ( $faqs as $f ) {
	$ld['mainEntity'][] = array( '@type' => 'Question', 'name' => $f[0], 'acceptedAnswer' => array( '@type' => 'Answer', 'text' => $f[1] ) );
}
echo "\n<script type=\"application/ld+json\">" . wp_json_encode( $ld, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . "</script>\n";

get_footer();
