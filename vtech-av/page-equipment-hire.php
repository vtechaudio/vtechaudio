<?php
/**
 * Template Name: Equipment Hire Guide
 * Full professional equipment-hire resource page (nested under About):
 * hero, equipment categories, how-hiring-works process, why-VTECH,
 * three in-depth articles, FAQ, and CTAs to packages + hire request.
 *
 * @package VTECH_AV
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();
$img = VTECH_URI . '/assets/img/';

$categories = array(
	array( 'Sound & PA Systems', 'Line arrays, powered tops, subwoofers, mixers, wireless mics and stage monitors for events of any size.' ),
	array( 'LED Screens & Video', 'Indoor and outdoor LED walls, projectors, screens, video switchers and confidence monitors.' ),
	array( 'Stage & Lighting', 'Stage decks, trussing, moving heads, wash and par lighting, hazers and DMX control.' ),
	array( 'Live Streaming', 'Cameras, switchers and encoders to stream to Facebook, YouTube, Zoom or Teams.' ),
	array( 'Conferencing', 'Delegate microphone systems, interpretation, projectors and displays for AGMs and summits.' ),
	array( 'Power & Support', 'Silent generators, cabling, distribution, and trained technicians on site throughout your event.' ),
);

$steps = array(
	array( '1', 'Tell us about your event', 'Share your dates, venue, guest numbers and programme via the hire request form or a quick call.' ),
	array( '2', 'Get a same-day quote', 'We size the right system and send an itemised quotation, including delivery, setup and technicians.' ),
	array( '3', 'We deliver & set up', 'Our crew delivers, installs and tests everything before your event begins, and stays on standby.' ),
	array( '4', 'We strike & collect', 'After your event we dismantle and collect, so you never lift a cable or chase logistics.' ),
);

$faqs = array(
	array( 'How far in advance should I book?', 'For weekends and peak seasons we recommend booking at least one to two weeks ahead. For large events, earlier is better so we can reserve the exact equipment and crew.' ),
	array( 'Do you deliver outside Nairobi?', 'Yes. We deliver, set up and support events across Kenya. Transport is quoted based on distance and included transparently in your quotation.' ),
	array( 'Are technicians included?', 'Technicians are strongly recommended for anything beyond a simple PA and can be added to any hire. A sound engineer, lighting technician or camera operator makes a visible difference.' ),
	array( 'Is a deposit or ID required?', 'Yes. A security deposit and valid identification may be required. Hired equipment remains the property of VTECH at all times and is your responsibility from delivery to collection.' ),
	array( 'What if something fails during my event?', 'We build in redundancy and our on-site technicians carry spares. If an issue arises, we resolve it fast so your event keeps running.' ),
);
?>
<section class="about-hero">
	<div class="container">
		<?php if ( function_exists( 'vtech_breadcrumbs' ) ) { vtech_breadcrumbs(); } ?>
		<h1><?php esc_html_e( 'Equipment Hire in Kenya', 'vtech-av' ); ?></h1>
		<p><?php esc_html_e( 'Hire professional audio, video, lighting, stage and streaming equipment for your event — delivered, set up and supported by trained technicians anywhere in Kenya.', 'vtech-av' ); ?></p>
		<div class="hero__cta" style="margin-top:1.6rem">
			<a class="btn btn--accent btn--lg" href="<?php echo esc_url( home_url( '/equipment-hire-request/' ) ); ?>"><?php esc_html_e( 'Start a Hire Request', 'vtech-av' ); ?></a>
			<a class="btn btn--ghost btn--lg" href="<?php echo esc_url( get_post_type_archive_link( 'hire_package' ) ?: home_url( '/hire-packages/' ) ); ?>"><?php esc_html_e( 'View Hire Packages', 'vtech-av' ); ?></a>
		</div>
	</div>
</section>

<!-- Equipment categories -->
<section class="section"><div class="container">
	<h2 class="section__title"><?php esc_html_e( 'What You Can Hire', 'vtech-av' ); ?></h2>
	<p class="section__lead"><?php esc_html_e( 'A complete inventory of event-grade equipment, available individually or as ready-made packages.', 'vtech-av' ); ?></p>
	<div class="feature-grid">
		<?php foreach ( $categories as $c ) : ?>
		<div class="feature-card">
			<h3><?php echo esc_html( $c[0] ); ?></h3>
			<p><?php echo esc_html( $c[1] ); ?></p>
		</div>
		<?php endforeach; ?>
	</div>
</div></section>

<!-- How hiring works -->
<section class="section section--surface"><div class="container">
	<h2 class="section__title"><?php esc_html_e( 'How Equipment Hire Works', 'vtech-av' ); ?></h2>
	<div class="step-grid">
		<?php foreach ( $steps as $s ) : ?>
		<div class="step-card">
			<span class="step-card__num"><?php echo esc_html( $s[0] ); ?></span>
			<h3><?php echo esc_html( $s[1] ); ?></h3>
			<p><?php echo esc_html( $s[2] ); ?></p>
		</div>
		<?php endforeach; ?>
	</div>
</div></section>

<!-- Why VTECH -->
<section class="section"><div class="container">
	<h2 class="section__title"><?php esc_html_e( 'Why Hire From VTECH', 'vtech-av' ); ?></h2>
	<div class="feature-grid">
		<div class="feature-card"><h3><?php esc_html_e( 'Event-grade, well-maintained gear', 'vtech-av' ); ?></h3><p><?php esc_html_e( 'Professional brands, tested before every hire so it performs when it matters.', 'vtech-av' ); ?></p></div>
		<div class="feature-card"><h3><?php esc_html_e( 'Delivery, setup & collection', 'vtech-av' ); ?></h3><p><?php esc_html_e( 'We handle the logistics end to end so you can focus on your event.', 'vtech-av' ); ?></p></div>
		<div class="feature-card"><h3><?php esc_html_e( 'Trained technicians', 'vtech-av' ); ?></h3><p><?php esc_html_e( 'Optional on-site sound, lighting and video crew who keep everything running.', 'vtech-av' ); ?></p></div>
		<div class="feature-card"><h3><?php esc_html_e( 'Transparent pricing', 'vtech-av' ); ?></h3><p><?php esc_html_e( 'Itemised quotes in Kenya Shillings — no hidden extras, no surprises.', 'vtech-av' ); ?></p></div>
	</div>
</div></section>

<!-- Article 1 -->
<article class="section section--surface"><div class="container narrow">
	<figure class="pkg-single__media"><img src="<?php echo esc_url( $img . 'article-event-setup.webp' ); ?>" alt="Professional event AV setup in Kenya" loading="lazy" decoding="async"></figure>
	<h2 class="section__title"><?php esc_html_e( 'How to Choose the Right Sound System for Your Event', 'vtech-av' ); ?></h2>
	<p><?php esc_html_e( 'The single biggest factor in event audio is matching the system to the space and the audience. A garden wedding for 150 guests has very different needs from a 2,000-seat crusade or a corporate AGM in a hotel ballroom. Start with three questions: how many guests, indoor or outdoor, and is it speech, music, or both?', 'vtech-av' ); ?></p>
	<p><?php esc_html_e( 'For speech-led events, clarity and even coverage matter more than raw power — a well-placed pair of full-range tops with a couple of delay speakers will outperform a bigger, poorly positioned rig. For music and worship, you add subwoofers for low-end and stage monitors so performers can hear themselves. Outdoor events almost always need more power and, often, a generator, because there are no walls to contain and reinforce the sound.', 'vtech-av' ); ?></p>
	<p><?php esc_html_e( 'Our advice: never hire on speaker count alone. Tell us the venue, guest numbers and programme, and we will spec a system that is properly sized — with a technician on site so it sounds right from the first minute.', 'vtech-av' ); ?></p>
</div></article>

<!-- Article 2 -->
<article class="section"><div class="container narrow">
	<figure class="pkg-single__media"><img src="<?php echo esc_url( $img . 'article-church.webp' ); ?>" alt="Church sound system in Kenya" loading="lazy" decoding="async"></figure>
	<h2 class="section__title"><?php esc_html_e( 'LED Screens vs Projectors: What to Hire for Visibility', 'vtech-av' ); ?></h2>
	<p><?php esc_html_e( 'Projectors are cost-effective for indoor, low-ambient-light settings such as conference rooms and dim halls. But in bright halls, tents, or any daytime outdoor event, a projected image washes out. That is where LED screens win: they are bright enough to read in direct sunlight, come in modular sizes, and deliver a far more premium look for stages, product launches and concerts.', 'vtech-av' ); ?></p>
	<p><?php esc_html_e( 'For live events we also recommend a confidence monitor for presenters and a video switcher when you are mixing cameras, slides and lyrics. If you are streaming, the same feed can be sent to Facebook, YouTube or Zoom with the right switching hardware.', 'vtech-av' ); ?></p>
	<p><?php esc_html_e( 'The right choice depends on light levels, viewing distance and budget — we will recommend the most cost-effective option that still looks world-class for your audience.', 'vtech-av' ); ?></p>
</div></article>

<!-- Article 3 -->
<article class="section section--surface"><div class="container narrow">
	<figure class="pkg-single__media"><img src="<?php echo esc_url( $img . 'article-boardroom.webp' ); ?>" alt="Corporate boardroom AV in Nairobi" loading="lazy" decoding="async"></figure>
	<h2 class="section__title"><?php esc_html_e( 'A Practical Checklist Before Your Event Day', 'vtech-av' ); ?></h2>
	<p><?php esc_html_e( 'The smoothest events are the ones planned early. Confirm your equipment list and delivery, setup and collection dates at least a week ahead. Check power: is mains available, how far is it from the stage, and do you need a backup generator? Walk the venue for truck access, stairs, lift availability and the floor level — these affect setup time and crew.', 'vtech-av' ); ?></p>
	<p><?php esc_html_e( 'Decide early whether you need technicians on site. A sound engineer, lighting technician or camera operators make a visible difference and are strongly recommended for anything beyond a simple PA. Finally, agree who is responsible for overnight security if equipment stays on site.', 'vtech-av' ); ?></p>
	<p><?php esc_html_e( 'Use our hire request form to capture all of this in one place — we will review it and return a same-day quotation with everything itemised.', 'vtech-av' ); ?></p>
</div></article>

<!-- FAQ -->
<section class="section"><div class="container narrow">
	<h2 class="section__title"><?php esc_html_e( 'Equipment Hire FAQs', 'vtech-av' ); ?></h2>
	<div class="faq-list">
		<?php foreach ( $faqs as $q ) : ?>
		<details class="faq-item">
			<summary><?php echo esc_html( $q[0] ); ?></summary>
			<div class="faq-item__a"><p><?php echo esc_html( $q[1] ); ?></p></div>
		</details>
		<?php endforeach; ?>
	</div>
</div></section>

<section class="section section--surface"><div class="container"><div class="cta-band section"><div class="cta-band__inner">
	<h2><?php esc_html_e( 'Ready to hire equipment for your event?', 'vtech-av' ); ?></h2>
	<p><?php esc_html_e( 'Complete our detailed hire request form and get a same-day quotation with delivery, setup and technician included.', 'vtech-av' ); ?></p>
	<a class="btn btn--accent btn--lg" href="<?php echo esc_url( home_url( '/equipment-hire-request/' ) ); ?>"><?php esc_html_e( 'Start a Hire Request', 'vtech-av' ); ?></a>
</div></div></div></section>

<?php get_footer();
