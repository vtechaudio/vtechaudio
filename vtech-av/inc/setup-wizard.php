<?php
/**
 * One-click theme setup — runs after activation.
 *
 * This is what makes the theme "install-and-go": on first activation it shows
 * an admin notice with a "Set up my website" button. Clicking it (or calling
 * vtech_run_setup()) will:
 *   1. Create every page (Home, About, Services index, Projects, Industries,
 *      Equipment Hire, Blog, FAQ, Contact, Privacy, Terms) with block content.
 *   2. Compose the Home page from the bundled patterns (which reference the
 *      theme's own bundled images — no uploads needed).
 *   3. Seed Services, Industries, Projects, Testimonials and FAQs, each with a
 *      featured image copied from /assets/img into the media library.
 *   4. Set the static front page + posts page.
 *   5. Build and assign the primary + footer menus.
 *   6. Set the custom logo from the bundled logo.
 * After running, the site looks like the live demo. The user just edits text
 * and swaps images like any other WordPress theme.
 *
 * @package VTECH_AV
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * PERMANENT top-level admin menu — this is the stable home for setup so it can
 * NEVER disappear (the old dismissible notice was the only entrance and vanished
 * when clicked). "VTECH Setup" now always shows in the left admin sidebar.
 */
add_action( 'admin_menu', function () {
	add_menu_page(
		'VTECH Setup',
		'VTECH Setup',
		'manage_options',
		'vtech-setup',
		'vtech_setup_page',
		'dashicons-megaphone',
		2 // near the top, right under Dashboard
	);
} );

/**
 * Welcome notice. NOT dismissible while setup is incomplete, so it can't be
 * accidentally closed and lock you out. It always points at the permanent
 * VTECH Setup page. Once setup runs, the notice stops showing on its own.
 */
add_action( 'admin_notices', function () {
	if ( get_option( 'vtech_setup_done' ) ) { return; }
	if ( ! current_user_can( 'manage_options' ) ) { return; }
	// Don't stack the notice on top of the setup page itself.
	$screen = get_current_screen();
	if ( $screen && 'toplevel_page_vtech-setup' === $screen->id ) { return; }
	$url = admin_url( 'admin.php?page=vtech-setup' );
	echo '<div class="notice notice-info"><h2 style="margin-top:.6em">Welcome to VTECH Audio Visual</h2>'
		. '<p>Build your complete website in one click — pages, homepage, services, industries, projects and images are created automatically. You can edit everything afterwards.</p>'
		. '<p><a class="button button-primary button-hero" href="' . esc_url( $url ) . '">Go to VTECH Setup</a></p>'
		. '<p style="color:#666">You can always find this later under <strong>VTECH Setup</strong> in the left menu.</p></div>';
} );

function vtech_setup_page() {
	// The build runs on POST (form submit) — more reliable than a GET link and
	// immune to the wrong-base-URL bug that stopped it firing before.
	if ( isset( $_POST['vtech_do_setup'] ) && check_admin_referer( 'vtech_setup_action', 'vtech_setup_nonce' ) ) {
		$force = ! empty( $_POST['vtech_force'] );
		if ( $force ) { delete_option( 'vtech_setup_done' ); }
		$log = vtech_run_setup();
		echo '<div class="wrap"><h1>VTECH setup complete</h1>'
			. '<div class="notice notice-success"><p>Your website has been built successfully.</p></div>'
			. '<p><a class="button button-primary button-hero" href="' . esc_url( home_url( '/' ) ) . '" target="_blank">View your site</a> '
			. '<a class="button" href="' . esc_url( admin_url( 'customize.php' ) ) . '">Customise it</a></p>'
			. '<p><strong>Next:</strong> confirm your phone number in Customize &rarr; VTECH Theme Options, then edit text and swap images.</p>'
			. '<hr><details><summary>What was created</summary><pre style="background:#fff;padding:1em;border:1px solid #ddd;overflow:auto">' . esc_html( $log ) . '</pre></details>'
			. '</div>';
		return;
	}

	$done = get_option( 'vtech_setup_done' );

	// Detect missing plugins so we can warn (but never block) the build.
	if ( ! function_exists( 'is_plugin_active' ) ) { require_once ABSPATH . 'wp-admin/includes/plugin.php'; }
	$acf = is_plugin_active( 'advanced-custom-fields/acf.php' ) || is_plugin_active( 'advanced-custom-fields-pro/acf.php' ) || class_exists( 'ACF' );
	$cf7 = is_plugin_active( 'contact-form-7/wp-contact-form-7.php' );

	echo '<div class="wrap"><h1>VTECH Setup</h1>';

	if ( ! $acf || ! $cf7 ) {
		echo '<div class="notice notice-warning"><p><strong>Recommended plugins not detected:</strong> '
			. ( ! $acf ? 'Advanced Custom Fields ' : '' ) . ( ! $cf7 ? 'Contact Form 7' : '' )
			. '. Setup will still build the whole site (content is stored so it works even without ACF), but installing these unlocks the editable field UI and forms. '
			. '<a href="' . esc_url( admin_url( 'plugin-install.php?tab=search&type=term&s=advanced+custom+fields' ) ) . '">Install ACF</a> · '
			. '<a href="' . esc_url( admin_url( 'plugin-install.php?tab=search&type=term&s=contact+form+7' ) ) . '">Install CF7</a></p></div>';
	}

	echo '<p>Click below to build your complete website — pages, homepage, services, industries, projects, testimonials, FAQs, images, logo and menus.</p>';
	echo '<form method="post" action="">';
	wp_nonce_field( 'vtech_setup_action', 'vtech_setup_nonce' );
	echo '<input type="hidden" name="vtech_do_setup" value="1">';
	if ( $done ) {
		echo '<div class="notice notice-info inline"><p>Setup has already run once. Tick the box to rebuild (safe — it updates existing items, never duplicates).</p></div>';
		echo '<p><label><input type="checkbox" name="vtech_force" value="1" checked> Force re-run setup</label></p>';
	}
	echo '<p><button type="submit" class="button button-primary button-hero">Set up my website</button></p>';
	echo '</form></div>';
}

/**
 * Render a pattern PHP file to its real block-HTML string.
 * The pattern files echo block markup; we capture it so the Home page stores
 * actual blocks (not an unexpanded wp:pattern reference, which does not
 * reliably render on the front end).
 */
function vtech_render_pattern_file( $file ) {
	$path = VTECH_DIR . '/patterns/' . $file;
	if ( ! file_exists( $path ) ) { return ''; }
	ob_start();
	include $path;
	return trim( ob_get_clean() );
}

/**
 * Copy a bundled theme image into the media library and return its attachment ID.
 * Cached so re-running setup does not duplicate images.
 */
function vtech_import_image( $filename, $title ) {
	// Reuse an existing import ONLY when it came from the same source file AND
	// the same theme version. This guarantees that when a bundled image file is
	// swapped (same name, new content) or a CPT is re-pointed to a different
	// file, a Force re-run actually re-imports the correct image instead of
	// silently reusing a stale attachment matched by title.
	$ver = defined( 'VTECH_VERSION' ) ? VTECH_VERSION : '1';
	$existing = get_posts( array(
		'post_type'      => 'attachment',
		'post_status'    => 'inherit',
		'posts_per_page' => 1,
		'fields'         => 'ids',
		'meta_query'     => array(
			'relation' => 'AND',
			array( 'key' => '_vtech_src', 'value' => $filename ),
			array( 'key' => '_vtech_ver', 'value' => $ver ),
		),
	) );
	if ( ! empty( $existing ) ) { return $existing[0]; }

	$src = VTECH_DIR . '/assets/img/' . $filename;
	if ( ! file_exists( $src ) ) { return 0; }

	// Remove older imports from this same source file (previous versions) so we
	// do not accumulate duplicates on repeated re-runs.
	$stale = get_posts( array(
		'post_type'      => 'attachment',
		'post_status'    => 'inherit',
		'posts_per_page' => -1,
		'fields'         => 'ids',
		'meta_query'     => array( array( 'key' => '_vtech_src', 'value' => $filename ) ),
	) );
	foreach ( (array) $stale as $sid ) { wp_delete_attachment( (int) $sid, true ); }

	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	$upload = wp_upload_dir();
	$dest   = trailingslashit( $upload['path'] ) . $filename;
	copy( $src, $dest );

	$filetype = wp_check_filetype( $filename, null );
	$attach_id = wp_insert_attachment( array(
		'post_mime_type' => $filetype['type'],
		'post_title'     => $title,
		'post_status'    => 'inherit',
	), $dest );
	$meta = wp_generate_attachment_metadata( $attach_id, $dest );
	wp_update_attachment_metadata( $attach_id, $meta );
	update_post_meta( $attach_id, '_wp_attachment_image_alt', $title );
	update_post_meta( $attach_id, '_vtech_src', $filename );
	update_post_meta( $attach_id, '_vtech_ver', $ver );
	return $attach_id;
}

/** Create or update a page by slug. */
function vtech_upsert_page( $slug, $title, $content = '', $template = '' ) {
	$existing = get_page_by_path( $slug );
	$args = array(
		'post_title'   => $title,
		'post_name'    => $slug,
		'post_content' => $content,
		'post_status'  => 'publish',
		'post_type'    => 'page',
	);
	if ( $existing ) { $args['ID'] = $existing->ID; }
	$id = wp_insert_post( $args );
	if ( $template ) { update_post_meta( $id, '_wp_page_template', $template ); }
	return $id;
}

/** Create a CPT item with a featured image. */
function vtech_upsert_cpt( $type, $slug, $title, $content, $excerpt, $img_file, $meta = array(), $terms = array() ) {
	$existing = get_page_by_path( $slug, OBJECT, $type );
	$args = array(
		'post_title'   => $title,
		'post_name'    => $slug,
		'post_content' => $content,
		'post_excerpt' => $excerpt,
		'post_status'  => 'publish',
		'post_type'    => $type,
	);
	if ( $existing ) { $args['ID'] = $existing->ID; }
	$id = wp_insert_post( $args );

	if ( $img_file ) {
		$att = vtech_import_image( $img_file, $title );
		if ( $att ) { set_post_thumbnail( $id, $att ); }
	}
	foreach ( $meta as $k => $v ) { update_post_meta( $id, $k, $v ); }
	foreach ( $terms as $tax => $names ) { wp_set_object_terms( $id, $names, $tax ); }
	return $id;
}

/** The main setup routine. Idempotent — safe to run more than once. Returns a log string. */
function vtech_run_setup() {
	$log = array();
	$log[] = 'Starting VTECH setup at ' . current_time( 'mysql' );

	// --- Logo ---
	$logo = vtech_import_image( 'logo.png', 'VTECH Logo' );
	if ( $logo ) { set_theme_mod( 'custom_logo', $logo ); $log[] = 'Logo set (attachment #' . $logo . ')'; }
	else { $log[] = 'Logo NOT set — assets/img/logo.png missing?'; }

	// --- Hero image theme mod (bundled) ---
	set_theme_mod( 'vtech_hero_img', VTECH_URI . '/assets/img/hero.webp' );
	set_theme_mod( 'vtech_og_default', VTECH_URI . '/assets/img/og-default.jpg' );

	// --- Services (with images + ACF) ---
	$services = array(
		array( 'sound-systems', 'Professional Sound Systems', 'Line arrays, PA systems and mixing tuned for churches, halls, stadiums and events across Kenya.', 'sound-systems.webp', 'sound', 120000, 1 ),
		array( 'led-screens', 'LED Screens & Video Walls', 'Indoor and outdoor LED display supply, installation and content for events, retail and corporate.', 'led-screens.webp', 'led', 350000, 2 ),
		array( 'conference-systems', 'Conference & Boardroom Systems', 'Video conferencing, room automation and one-touch meeting rooms for corporates and government.', 'conference-systems.webp', 'conference', 180000, 3 ),
		array( 'lighting', 'Stage & Architectural Lighting', 'Event and permanent lighting design, rigging and control.', 'stage-lighting.webp', 'lighting', 220000, 4 ),
		array( 'acoustic-solutions', 'Acoustic Design & Soundproofing', 'Room acoustics, treatment and soundproofing for clean, clear audio.', 'acoustic-solutions.webp', 'acoustic', 90000, 5 ),
		array( 'video-systems', 'CCTV & Digital Signage', 'Security integration and digital signage networks.', 'video-systems.webp', 'cctv', 0, 6 ),
		array( 'consultation', 'AV Consultation & System Design', 'Free, no-obligation site survey, needs analysis and a tailored technical design and quotation for your space.', 'consultation.webp', 'consult', 0, 7 ),
	);
	$svc_bodies = array( 'consultation' => '<!-- wp:paragraph --><p><strong>Great AV starts with the right design, not the biggest budget.</strong> Our consultation is where every successful VTECH project begins. We come to your space, understand how you actually use it, and engineer a system that fits your room, your workflow and your budget before a single cable is bought.</p><!-- /wp:paragraph --><!-- wp:heading {&quot;level&quot;:3} --><h3>What a VTECH consultation includes</h3><!-- /wp:heading --><!-- wp:list --><ul><li><strong>On-site survey</strong> we measure your room, assess acoustics, sightlines, power and mounting, and photograph existing infrastructure.</li><li><strong>Needs analysis</strong> we sit with your team to understand goals: worship, corporate meetings, live events, retail, teaching or hybrid use.</li><li><strong>System design</strong> our engineers produce a tailored equipment schedule, signal-flow plan and, where useful, a room layout.</li><li><strong>Transparent quotation</strong> an itemised, fixed-scope quote in Kenya Shillings with no hidden extras.</li><li><strong>Technical recommendation</strong> clear advice on phasing, future-proofing and what to prioritise if budget is staged.</li></ul><!-- /wp:list --><!-- wp:heading {&quot;level&quot;:3} --><h3>How it works</h3><!-- /wp:heading --><!-- wp:list {&quot;ordered&quot;:true} --><ol><li>You book a consultation and tell us about your space.</li><li>We schedule a site visit, usually within 48 hours in Nairobi and its environs.</li><li>Our engineers survey the space and discuss your requirements.</li><li>You receive a tailored design and quotation, typically within 24 hours of the visit.</li><li>We refine the design with you until it is exactly right.</li></ol><!-- /wp:list --><!-- wp:heading {&quot;level&quot;:3} --><h3>Why it is free</h3><!-- /wp:heading --><!-- wp:paragraph --><p>We would rather earn your trust with genuinely useful advice than pressure you into a sale. The consultation is free and carries no obligation. If we are the right fit, the design becomes the blueprint for your installation. If not, you keep a professional assessment of your space at no cost.</p><!-- /wp:paragraph --><!-- wp:paragraph --><p><strong>Serving churches, corporates, government, hospitality, education and event organisers across Kenya.</strong></p><!-- /wp:paragraph -->',
		'sound-systems' => '<!-- wp:paragraph --><p><strong>Clear, powerful, reliable sound for any space in Kenya.</strong> From churches and conference halls to stadiums and outdoor events, VTECH designs and installs sound systems tuned to your room and your purpose. We do not sell generic kit; we engineer coverage, clarity and headroom for your exact space.</p><!-- /wp:paragraph --><!-- wp:heading {&quot;level&quot;:3} --><h3>What we deliver</h3><!-- /wp:heading --><!-- wp:list --><ul><li>Line-array and point-source PA systems sized to your venue.</li><li>Subwoofers, stage monitors and in-ear monitoring for live music and worship.</li><li>Digital mixing consoles with saved scenes for speech and music.</li><li>Wireless microphone systems with reliable, interference-free coverage.</li><li>Full cabling, rigging and power distribution to a professional standard.</li></ul><!-- /wp:list --><!-- wp:heading {&quot;level&quot;:3} --><h3>Who it is for</h3><!-- /wp:heading --><!-- wp:paragraph --><p>Churches, event organisers, hotels, schools, stadiums and corporates across Kenya. Whether it is speech-first clarity for preaching and presentations or full concert-grade audio for live bands, we size the system correctly so every seat hears clearly.</p><!-- /wp:paragraph -->',
		'led-screens' => '<!-- wp:paragraph --><p><strong>Bright, high-impact LED screens and video walls, supplied and installed across Kenya.</strong> LED displays stay crisp and readable even in bright halls, tents and daylight where projectors wash out, giving your stage, event or lobby a premium, modern look.</p><!-- /wp:paragraph --><!-- wp:heading {&quot;level&quot;:3} --><h3>What we deliver</h3><!-- /wp:heading --><!-- wp:list --><ul><li>Indoor and outdoor LED video walls in modular sizes.</li><li>Fine-pitch LED for boardrooms, control rooms and lobbies.</li><li>Event and stage screens for concerts, launches and conferences.</li><li>Video switching, content playback and live camera integration.</li><li>Digital signage networks for retail, malls and corporate spaces.</li></ul><!-- /wp:list --><!-- wp:heading {&quot;level&quot;:3} --><h3>Why LED over projection</h3><!-- /wp:heading --><!-- wp:paragraph --><p>LED walls are brighter, more durable and far more visible in Kenyan daytime and high-ambient-light conditions. They scale to any size and deliver a world-class visual experience for events, retail and corporate environments.</p><!-- /wp:paragraph -->',
		'conference-systems' => '<!-- wp:paragraph --><p><strong>One-touch meeting rooms and conference systems for Kenyan corporates and government.</strong> We standardise your rooms so anyone can walk in, press one button, and start a clear, professional Teams or Zoom meeting, no cables, no delays, no IT call-outs.</p><!-- /wp:paragraph --><!-- wp:heading {&quot;level&quot;:3} --><h3>What we deliver</h3><!-- /wp:heading --><!-- wp:list --><ul><li>Video conferencing with auto-framing cameras and clear room audio.</li><li>Ceiling microphones and speakers for natural voice pickup anywhere at the table.</li><li>One-touch room control that ties display, audio and video together.</li><li>Wireless and wired presentation and screen sharing.</li><li>Delegate microphone systems for boardrooms, chambers and summits.</li></ul><!-- /wp:list --><!-- wp:heading {&quot;level&quot;:3} --><h3>Who it is for</h3><!-- /wp:heading --><!-- wp:paragraph --><p>Corporates, government, NGOs and institutions that want reliable, standardised meeting rooms. We make hybrid meetings effortless and cut the IT support burden with a consistent experience across every room.</p><!-- /wp:paragraph -->',
		'lighting' => '<!-- wp:paragraph --><p><strong>Stage and architectural lighting design, supply and control across Kenya.</strong> Great lighting transforms a stage, a building or an event. We design and rig lighting that looks stunning and performs reliably, from a single church stage to a full concert production.</p><!-- /wp:paragraph --><!-- wp:heading {&quot;level&quot;:3} --><h3>What we deliver</h3><!-- /wp:heading --><!-- wp:list --><ul><li>Stage lighting design for churches, events and concerts.</li><li>Moving-head, wash and par lighting with professional control.</li><li>Trussing and rigging, permanent or event-based.</li><li>Architectural and ambient lighting for venues and facades.</li><li>DMX lighting control and programming, with training.</li></ul><!-- /wp:list --><!-- wp:heading {&quot;level&quot;:3} --><h3>Who it is for</h3><!-- /wp:heading --><!-- wp:paragraph --><p>Churches, event organisers, hotels, theatres and venues that want their stage and space to look world-class, whether for a weekly service or a headline event.</p><!-- /wp:paragraph -->',
		'acoustic-solutions' => '<!-- wp:paragraph --><p><strong>Acoustic design and soundproofing for clean, clear audio in Kenya.</strong> Even the best sound system struggles in a room that fights it. We treat acoustics so speech is intelligible, music is clean, and noise stays where it belongs.</p><!-- /wp:paragraph --><!-- wp:heading {&quot;level&quot;:3} --><h3>What we deliver</h3><!-- /wp:heading --><!-- wp:list --><ul><li>Room acoustic assessment and design.</li><li>Acoustic panels, diffusers and bass traps, elegantly finished.</li><li>Soundproofing to contain or block unwanted noise.</li><li>Treatment for studios, boardrooms, auditoriums and worship spaces.</li><li>Integration with your sound system for the best possible result.</li></ul><!-- /wp:list --><!-- wp:heading {&quot;level&quot;:3} --><h3>Who it is for</h3><!-- /wp:heading --><!-- wp:paragraph --><p>Churches, studios, boardrooms, auditoriums and any space where echo, noise or poor clarity is a problem. Good acoustics protect your investment in sound and make every word count.</p><!-- /wp:paragraph -->',
		'video-systems' => '<!-- wp:paragraph --><p><strong>CCTV security and digital signage networks across Kenya.</strong> Protect your premises and communicate with your audience through professionally installed camera systems and bright, managed digital displays.</p><!-- /wp:paragraph --><!-- wp:heading {&quot;level&quot;:3} --><h3>What we deliver</h3><!-- /wp:heading --><!-- wp:list --><ul><li>CCTV and IP camera systems with remote viewing.</li><li>Access control and security integration.</li><li>Digital signage displays for retail, malls, offices and lobbies.</li><li>Centrally managed content and playback across multiple screens.</li><li>Reliable network infrastructure and ongoing support.</li></ul><!-- /wp:list --><!-- wp:heading {&quot;level&quot;:3} --><h3>Who it is for</h3><!-- /wp:heading --><!-- wp:paragraph --><p>Retailers, malls, corporates, institutions and facilities that need security coverage, clear digital communication, or both, installed and supported by one accountable partner.</p><!-- /wp:paragraph -->',
	);
	foreach ( $services as $s ) {
		$meta = array( 'tagline' => $s[2], '_tagline' => 'svc_tagline', 'icon' => $s[4], '_icon' => 'svc_icon' );
		if ( $s[5] ) { $meta['price_from'] = $s[5]; $meta['_price_from'] = 'svc_price_from'; }
		$svc_body = isset( $svc_bodies[ $s[0] ] ) ? $svc_bodies[ $s[0] ] : '<!-- wp:paragraph --><p>' . esc_html( $s[2] ) . '</p><!-- /wp:paragraph -->';
		$id = vtech_upsert_cpt( 'service', $s[0], $s[1], $svc_body, $s[2], $s[3], $meta );
		wp_update_post( array( 'ID' => $id, 'menu_order' => $s[6] ) );
	}

	// --- Industries ---
	$industries = array(
		array( 'churches', 'AV Solutions for Churches in Kenya', 'Church sound systems, LED and streaming across Kenya — speech-first, volunteer-friendly.', 'industry-churches.webp' ),
		array( 'hotels', 'Hotel Audio Visual Solutions in Kenya', 'Flexible conference and banqueting AV for hotels across Kenya.', 'industry-hotels.webp' ),
		array( 'education', 'School PA Systems in Kenya', 'PA, assembly and lecture-hall AV for schools and universities in Kenya.', 'industry-education.webp' ),
		array( 'corporate', 'Boardroom AV Solutions in Nairobi', 'One-touch meeting rooms and conferencing for corporates.', 'industry-corporate.webp' ),
		array( 'government', 'Conference Systems for Government', 'Delegate microphone and conference systems for government.', 'industry-government.webp' ),
		array( 'media', 'Broadcast & Media House AV', 'Studio, broadcast and live-streaming AV for media houses.', 'industry-media.webp' ),
		array( 'healthcare', 'Hospital & Healthcare AV in Kenya', 'Paging, nurse-call-friendly PA and clear audio for hospitals and clinics across Kenya.', 'industry-healthcare.webp' ),
		array( 'conference-centres', 'Conference Centre AV Solutions in Kenya', 'Scalable sound, displays and delegate systems for conference and convention centres.', 'industry-conference-centres.webp' ),
		array( 'events', 'Event Sound & Lighting Hire in Kenya', 'Professional sound, lighting and LED hire for events, concerts and functions across Kenya.', 'industry-events.webp' ),
	);
	$ind_bodies = array(
		'churches' => '<!-- wp:paragraph --><p><strong>AV built for worship, across Kenya.</strong> Church sound is unforgiving: the same room has to carry a quiet prayer, a full worship band and a preacher who moves around the stage, all run by volunteers who change every few weeks. We understand that reality. VTECH designs speech-first, music-capable systems that your team can actually operate, from intimate assemblies to 2,000-seat sanctuaries.</p><!-- /wp:paragraph --><!-- wp:heading {&quot;level&quot;:3} --><h3>Where we help churches</h3><!-- /wp:heading --><!-- wp:list --><ul><li><strong>Main sanctuary sound</strong> — even, intelligible coverage from the front row to the back balcony.</li><li><strong>Worship &amp; band audio</strong> — clean mixing, stage monitors or in-ears, and saved scenes for preaching vs praise.</li><li><strong>LED walls &amp; projection</strong> — lyrics, sermon points, giving info and live camera relay.</li><li><strong>Live streaming</strong> — reach members at home and grow your online congregation with clean audio and multi-camera video.</li><li><strong>Overflow &amp; children&#039;s halls</strong> — distributed audio and displays to satellite rooms.</li></ul><!-- /wp:list --><!-- wp:heading {&quot;level&quot;:3} --><h3>Why churches choose VTECH</h3><!-- /wp:heading --><!-- wp:list --><ul><li>Systems tuned for <strong>speech intelligibility first</strong>, then music impact.</li><li><strong>Volunteer-friendly</strong> presets and hands-on training so any team member can run a service.</li><li>Phased designs that let you <strong>start within budget</strong> and expand later.</li><li>Ongoing support and maintenance so Sunday never depends on luck.</li></ul><!-- /wp:list --><!-- wp:paragraph --><p><em>Book a free site survey and we will assess your sanctuary&#039;s acoustics before quoting.</em></p><!-- /wp:paragraph -->',
		'hotels' => '<!-- wp:paragraph --><p><strong>Flexible AV that grows your events business.</strong> For hotels, AV is not a cost centre, it is a revenue driver. The venues that win MICE, weddings and gala business are the ones that can reconfigure a single hall for a 20-person board meeting in the morning and a 400-guest dinner at night, without calling in outside technicians. VTECH equips your spaces to do exactly that.</p><!-- /wp:paragraph --><!-- wp:heading {&quot;level&quot;:3} --><h3>Where we help hotels</h3><!-- /wp:heading --><!-- wp:list --><ul><li><strong>Conference &amp; banqueting halls</strong> — zoned audio so one hall splits into independent event spaces.</li><li><strong>Meetings &amp; boardrooms</strong> — presentation displays, video conferencing and wireless sharing.</li><li><strong>Weddings &amp; gala dinners</strong> — background music, speeches, MC mics and stage lighting.</li><li><strong>Outdoor &amp; poolside events</strong> — weather-ready sound and power for garden functions.</li><li><strong>Digital signage</strong> — wayfinding and event boards across the property.</li></ul><!-- /wp:list --><!-- wp:heading {&quot;level&quot;:3} --><h3>Why hotels choose VTECH</h3><!-- /wp:heading --><!-- wp:list --><ul><li><strong>Staff-operable</strong> systems, no need for a hired technician at every event.</li><li>Zoned control so <strong>multiple functions run simultaneously</strong>.</li><li>Professional presentation that helps you <strong>close more event bookings</strong>.</li><li>Nationwide install and support, from Nairobi to the coast.</li></ul><!-- /wp:list --><!-- wp:paragraph --><p><em>Turn your function spaces into a competitive advantage, book a free site survey.</em></p><!-- /wp:paragraph -->',
		'education' => '<!-- wp:paragraph --><p><strong>PA and AV for schools and universities across Kenya.</strong> Education AV has to do two things at once: be clear enough that a message reaches every student across a noisy compound, and be rugged and simple enough to survive years of daily use by non-technical staff. VTECH designs durable, low-maintenance systems for assemblies, lecture halls and modern hybrid classrooms.</p><!-- /wp:paragraph --><!-- wp:heading {&quot;level&quot;:3} --><h3>Where we help schools &amp; universities</h3><!-- /wp:heading --><!-- wp:list --><ul><li><strong>Public address &amp; bell systems</strong> — clear announcements across classrooms, halls and grounds.</li><li><strong>Assembly &amp; parade sound</strong> — reliable coverage for large open-air gatherings.</li><li><strong>Lecture halls &amp; auditoriums</strong> — voice reinforcement, projection or LED, and recording.</li><li><strong>Hybrid &amp; recording-ready classrooms</strong> — cameras, mics and displays for blended learning.</li><li><strong>Chapel &amp; events AV</strong> — sound and visuals for school services and functions.</li></ul><!-- /wp:list --><!-- wp:heading {&quot;level&quot;:3} --><h3>Why institutions choose VTECH</h3><!-- /wp:heading --><!-- wp:list --><ul><li><strong>Rugged, low-maintenance</strong> equipment built for daily institutional use.</li><li>Simple controls so <strong>any staff member</strong> can operate the system.</li><li>Zoned and expandable, add blocks and rooms as the campus grows.</li><li>Support contracts that keep announcements and lectures running term after term.</li></ul><!-- /wp:list --><!-- wp:paragraph --><p><em>From a single hall to a full campus PA, book a free site survey and fixed quote.</em></p><!-- /wp:paragraph -->',
		'corporate' => '<!-- wp:paragraph --><p><strong>One-touch boardrooms for Nairobi corporates.</strong> Every minute spent fighting cables at the start of a meeting is a minute of expensive people&#039;s time lost, and it happens in most offices, every day. VTECH standardises your meeting rooms so every space works the same way: walk in, press one button, and you are in a professional hybrid meeting.</p><!-- /wp:paragraph --><!-- wp:heading {&quot;level&quot;:3} --><h3>Where we help corporates</h3><!-- /wp:heading --><!-- wp:list --><ul><li><strong>Boardrooms &amp; meeting rooms</strong> — one-touch video conferencing for Teams and Zoom.</li><li><strong>Ceiling audio</strong> — microphones and speakers for natural, clear voice pickup.</li><li><strong>Wireless presentation</strong> — share from any laptop or phone without cables.</li><li><strong>Training &amp; town-hall spaces</strong> — larger-room sound, displays and streaming.</li><li><strong>Room booking &amp; signage</strong> — occupancy panels and digital signage.</li></ul><!-- /wp:list --><!-- wp:heading {&quot;level&quot;:3} --><h3>Why corporates choose VTECH</h3><!-- /wp:heading --><!-- wp:list --><ul><li><strong>Standardised</strong> designs, every room behaves identically.</li><li><strong>Fewer IT tickets</strong> and meetings that start on time.</li><li>Scalable rollouts across floors, branches and campuses.</li><li>Clean, certified installation with full documentation and training.</li></ul><!-- /wp:list --><!-- wp:paragraph --><p><em>Standardise your rooms and reclaim wasted meeting time, book a free site survey.</em></p><!-- /wp:paragraph -->',
		'government' => '<!-- wp:paragraph --><p><strong>Conference and delegate systems for government in Kenya.</strong> Chambers, assemblies and summits demand AV that is dignified, dependable and on the record. When proceedings must be heard, minuted and often streamed, there is no room for feedback squeal or a dead microphone. VTECH delivers standards-compliant delegate and conference systems built for public institutions.</p><!-- /wp:paragraph --><!-- wp:heading {&quot;level&quot;:3} --><h3>Where we help government</h3><!-- /wp:heading --><!-- wp:list --><ul><li><strong>Delegate &amp; discussion systems</strong> — microphone units with request-to-speak and chairman control.</li><li><strong>Council &amp; assembly chambers</strong> — clear reinforcement and camera-ready displays.</li><li><strong>Recording &amp; streaming</strong> — capture and broadcast proceedings for the public record.</li><li><strong>Summits &amp; press conferences</strong> — interpretation-ready audio and press feeds.</li><li><strong>Training &amp; briefing rooms</strong> — presentation and conferencing across facilities.</li></ul><!-- /wp:list --><!-- wp:heading {&quot;level&quot;:3} --><h3>Why institutions choose VTECH</h3><!-- /wp:heading --><!-- wp:list --><ul><li><strong>Reliable and serviceable</strong> systems built for years of official use.</li><li>Standards-compliant, well-documented installations.</li><li>Recording and streaming that protect the public record.</li><li>Discreet, professional support that respects the setting.</li></ul><!-- /wp:list --><!-- wp:paragraph --><p><em>Equip your chamber with confidence, book a free site survey and specification.</em></p><!-- /wp:paragraph -->',
		'media' => '<!-- wp:paragraph --><p><strong>Studio, broadcast and streaming AV for media houses.</strong> In media, the technical backbone is the product. Audiences forgive a lot, but not muddy audio or a dropped stream. VTECH builds and integrates the studio audio, lighting, multi-camera production and streaming infrastructure that lets media teams produce and broadcast with confidence.</p><!-- /wp:paragraph --><!-- wp:heading {&quot;level&quot;:3} --><h3>Where we help media houses</h3><!-- /wp:heading --><!-- wp:list --><ul><li><strong>Studio audio</strong> — treated acoustics, microphones and mixing for clean broadcast sound.</li><li><strong>Studio &amp; set lighting</strong> — flattering, consistent lighting for camera.</li><li><strong>Multi-camera production</strong> — switching, tally and signal distribution.</li><li><strong>Live streaming &amp; encoding</strong> — reliable delivery to broadcast and online platforms.</li><li><strong>Podcast &amp; content spaces</strong> — compact, professional recording setups.</li></ul><!-- /wp:list --><!-- wp:heading {&quot;level&quot;:3} --><h3>Why media houses choose VTECH</h3><!-- /wp:heading --><!-- wp:list --><ul><li>Broadcast-grade <strong>reliability</strong> where downtime is not an option.</li><li>Clean signal distribution and robust, serviceable builds.</li><li>Scalable from a single podcast booth to a full studio.</li><li>Responsive support to keep you on air.</li></ul><!-- /wp:list --><!-- wp:paragraph --><p><em>Build a technical backbone you can trust, book a free site survey.</em></p><!-- /wp:paragraph -->',
		'healthcare' => '<!-- wp:paragraph --><p><strong>Clear, dependable audio for hospitals and clinics across Kenya.</strong> In healthcare, audio is a safety and dignity issue: staff need to page and announce clearly, patients need to hear instructions, and waiting areas need calm, intelligible sound, all in acoustically difficult, always-on environments. VTECH designs reliable, low-maintenance PA and AV built for medical facilities.</p><!-- /wp:paragraph --><!-- wp:heading {&quot;level&quot;:3} --><h3>Where we help healthcare facilities</h3><!-- /wp:heading --><!-- wp:list --><ul><li><strong>Public address &amp; paging</strong> — clear announcements across wards, corridors and waiting areas.</li><li><strong>Waiting-room audio &amp; signage</strong> — calm background sound and digital queue/information displays.</li><li><strong>Training &amp; conference rooms</strong> — presentation, video conferencing and CME/lecture AV.</li><li><strong>Board &amp; admin rooms</strong> — one-touch meeting and hybrid-call systems.</li><li><strong>Zoned control</strong> — independent audio for different departments and floors.</li></ul><!-- /wp:list --><!-- wp:heading {&quot;level&quot;:3} --><h3>Why healthcare providers choose VTECH</h3><!-- /wp:heading --><!-- wp:list --><ul><li><strong>Reliable, always-on</strong> systems built for 24/7 environments.</li><li>Clear, intelligible audio even in hard, reverberant spaces.</li><li>Low-maintenance, serviceable installs with support contracts.</li><li>Discreet, professional work that respects a clinical setting.</li></ul><!-- /wp:list --><!-- wp:paragraph --><p><em>Book a free site survey and we will assess your facility before quoting.</em></p><!-- /wp:paragraph -->',
		'conference-centres' => '<!-- wp:paragraph --><p><strong>Scalable AV for conference and convention centres in Kenya.</strong> A conference centre lives or dies on its technical reputation. Delegates remember whether they could hear the panel, whether the screens were readable, and whether the changeover between sessions was seamless. VTECH equips centres with flexible, professional systems that handle everything from a breakout to a plenary.</p><!-- /wp:paragraph --><!-- wp:heading {&quot;level&quot;:3} --><h3>Where we help conference centres</h3><!-- /wp:heading --><!-- wp:list --><ul><li><strong>Plenary &amp; main-hall sound</strong> — even, intelligible coverage for large audiences.</li><li><strong>Delegate &amp; discussion systems</strong> — microphone units with chairman control for summits and panels.</li><li><strong>Large-format displays &amp; LED</strong> — readable screens and stage visuals.</li><li><strong>Breakout &amp; syndicate rooms</strong> — standardised, easy-to-run AV.</li><li><strong>Recording &amp; streaming</strong> — capture and broadcast sessions to remote delegates.</li></ul><!-- /wp:list --><!-- wp:heading {&quot;level&quot;:3} --><h3>Why conference centres choose VTECH</h3><!-- /wp:heading --><!-- wp:list --><ul><li><strong>Scalable</strong> designs that flex from small rooms to full plenaries.</li><li>Fast, seamless changeovers between sessions.</li><li>Professional presentation that wins repeat event bookings.</li><li>On-site support during major events.</li></ul><!-- /wp:list --><!-- wp:paragraph --><p><em>Make your centre the venue organisers trust, book a free site survey.</em></p><!-- /wp:paragraph -->',
		'events' => '<!-- wp:paragraph --><p><strong>Professional sound, lighting and LED hire for events across Kenya.</strong> For event organisers, the AV is the experience. VTECH provides reliable, well-operated sound, lighting and LED hire, delivered, set up and run by technicians, so your concerts, launches, weddings and corporate functions look and sound world-class, without you owning or troubleshooting the gear.</p><!-- /wp:paragraph --><!-- wp:heading {&quot;level&quot;:3} --><h3>Where we help event organisers</h3><!-- /wp:heading --><!-- wp:list --><ul><li><strong>Sound system hire</strong> — from small functions to concert-scale line arrays.</li><li><strong>Stage &amp; event lighting</strong> — wash, moving heads and effects for atmosphere.</li><li><strong>LED screens &amp; video</strong> — indoor and outdoor screens with live camera relay.</li><li><strong>Microphones &amp; DJ/band support</strong> — wireless mics, monitors and backline.</li><li><strong>On-site technicians</strong> — delivery, setup, operation and pack-down.</li></ul><!-- /wp:list --><!-- wp:heading {&quot;level&quot;:3} --><h3>Why organisers choose VTECH</h3><!-- /wp:heading --><!-- wp:list --><ul><li><strong>Turnkey hire</strong> — we deliver, set up, run and collect.</li><li>Well-maintained, gig-ready equipment.</li><li>Experienced technicians who keep the show running.</li><li>Packages that scale to any guest count and budget.</li></ul><!-- /wp:list --><!-- wp:paragraph --><p><em>Tell us about your event and get a fixed quote within 24 hours.</em></p><!-- /wp:paragraph -->',
	);
	foreach ( $industries as $i ) {
		$ind_body = isset( $ind_bodies[ $i[0] ] ) ? $ind_bodies[ $i[0] ] : '<!-- wp:paragraph --><p>' . esc_html( $i[2] ) . '</p><!-- /wp:paragraph -->';
		vtech_upsert_cpt( 'industry', $i[0], $i[1], $ind_body, $i[2], $i[3] );
	}

	// --- Projects ---
	$projects = array(
		array( 'nairobi-church-auditorium', 'Auditorium Sound & LED Upgrade — Nairobi Church', 'Line array + indoor LED wall for a 1,200-seat congregation.', 'project-01.webp', 'churches' ),
		array( 'corporate-boardroom-rollout', 'Boardroom AV Rollout — Corporate HQ', 'Eight standardised one-touch conference rooms for a Nairobi HQ.', 'project-boardroom.webp', 'corporate' ),
		array( 'mombasa-hotel-conference', 'Conference Hall Sound — Coastal Hotel', 'Zoned conference and banqueting AV for a coastal hotel.', 'project-03.webp', 'hotels' ),
		array( 'university-lecture-hall-av', 'Lecture Hall & Campus PA — University', 'Voice reinforcement, projection and campus-wide public address for a Kenyan university.', 'project-university.webp', 'education' ),
		array( 'county-assembly-delegate-system', 'Delegate Conference System — County Assembly', 'Microphone discussion system with recording and streaming for a county assembly chamber.', 'industry-government.webp', 'government' ),
		array( 'media-house-studio-buildout', 'Studio Audio & Streaming Build — Media House', 'Studio sound, lighting and multi-camera live streaming for a Nairobi media house.', 'industry-media.webp', 'media' ),
		array( 'hospital-paging-pa-install', 'Hospital Paging & PA System — Nairobi Hospital', 'Zoned paging and clear public address across wards, corridors and waiting areas.', 'project-hospital.webp', 'healthcare' ),
		array( 'convention-centre-plenary-av', 'Plenary & Delegate AV — Conference Centre', 'Scalable plenary sound, delegate microphones, LED and session streaming for a Nairobi conference centre.', 'industry-government.webp', 'conference-centres' ),
		array( 'concert-sound-lighting-hire', 'Concert Sound & Lighting Hire — Outdoor Event', 'Line-array sound, stage lighting and LED screens hired and operated for a large outdoor event.', 'industry-media.webp', 'events' ),
	);
	$proj_bodies = array(
		'nairobi-church-auditorium' => '<!-- wp:paragraph --><p><strong>Line-array sound and an indoor LED wall for a 1,200-seat sanctuary.</strong> A growing Nairobi congregation had outgrown its ageing sound system. With a full worship band and a large, reflective auditorium, speech was muffled at the back and music lacked clarity and impact. VTECH designed and installed a modern AV system serving both preaching and praise, without disrupting weekend services.</p><!-- /wp:paragraph --><!-- wp:heading {&quot;level&quot;:3} --><h3>The challenge</h3><!-- /wp:heading --><!-- wp:list --><ul><li>Poor speech intelligibility, especially in the balcony and rear seats.</li><li>An old PA that could not handle a live band cleanly.</li><li>A large, acoustically challenging room with hard reflective surfaces.</li><li>Work had to fit around a busy service schedule.</li></ul><!-- /wp:list --><!-- wp:heading {&quot;level&quot;:3} --><h3>Our solution</h3><!-- /wp:heading --><!-- wp:list --><ul><li>A line-array PA for even, full-coverage sound front to back.</li><li>Subwoofers for a rich, controlled low end.</li><li>Stage monitors so the worship team could hear clearly.</li><li>A digital mixing console with saved scenes for preaching vs music.</li><li>An indoor LED wall for lyrics, sermon points and live camera.</li><li>Acoustic adjustments to tame reflections.</li></ul><!-- /wp:list --><!-- wp:heading {&quot;level&quot;:3} --><h3>The result</h3><!-- /wp:heading --><!-- wp:paragraph --><p>Crisp, even sound in every seat, clear speech for preaching and powerful, balanced audio for worship. The LED wall transformed the visual experience, and the volunteer team runs the system confidently thanks to saved presets and hands-on training.</p><!-- /wp:paragraph --><!-- wp:paragraph --><p><em>Every seat hears clearly now, and the team can run it without an engineer on site.</em></p><!-- /wp:paragraph -->',
		'corporate-boardroom-rollout' => '<!-- wp:paragraph --><p><strong>Eight standardised one-touch conference rooms for a Nairobi head office.</strong> Different rooms had mismatched, unreliable equipment, and staff wasted time at the start of every meeting fighting with cables. VTECH standardised and simplified AV across eight boardrooms and meeting rooms.</p><!-- /wp:paragraph --><!-- wp:heading {&quot;level&quot;:3} --><h3>The challenge</h3><!-- /wp:heading --><!-- wp:list --><ul><li>Inconsistent, ageing equipment across rooms, no two the same.</li><li>Meetings starting late due to connection and setup issues.</li><li>Poor audio and video on hybrid Teams and Zoom calls.</li><li>IT team overloaded with AV support tickets.</li></ul><!-- /wp:list --><!-- wp:heading {&quot;level&quot;:3} --><h3>Our solution</h3><!-- /wp:heading --><!-- wp:list --><ul><li>One-touch conferencing, walk in, press one button, ready to meet.</li><li>Professional displays sized correctly for each room.</li><li>Ceiling microphones and speakers for clear, natural voice pickup.</li><li>HD conference cameras with auto-framing.</li><li>Room automation controlling display, audio and video together.</li><li>Consistent cabling and connectivity in every room.</li></ul><!-- /wp:list --><!-- wp:heading {&quot;level&quot;:3} --><h3>The result</h3><!-- /wp:heading --><!-- wp:paragraph --><p>Meetings now start on time, every time. Staff move between rooms and get the same simple, reliable experience. Hybrid calls are clear and professional, and AV support tickets have dropped dramatically.</p><!-- /wp:paragraph --><!-- wp:paragraph --><p><em>Eight rooms, one experience, walk in, press once, and meet.</em></p><!-- /wp:paragraph -->',
		'mombasa-hotel-conference' => '<!-- wp:paragraph --><p><strong>Zoned conference and banqueting AV for a coastal hotel.</strong> A Mombasa hotel wanted to grow its meetings and events business, but its facilities lacked flexible, professional AV. VTECH designed a zoned system that hosts everything from small breakout meetings to large gala dinners, often at the same time.</p><!-- /wp:paragraph --><!-- wp:heading {&quot;level&quot;:3} --><h3>The challenge</h3><!-- /wp:heading --><!-- wp:list --><ul><li>A large hall used for very different events.</li><li>Need to split the space into zones with independent audio.</li><li>Background music, speeches and presentations all required.</li><li>Staff needed to operate it without a technician for every event.</li></ul><!-- /wp:list --><!-- wp:heading {&quot;level&quot;:3} --><h3>Our solution</h3><!-- /wp:heading --><!-- wp:list --><ul><li>Zoned audio, the hall divides into independent areas.</li><li>Distributed speakers for even music and clear speech.</li><li>Presentation displays for conferences and launches.</li><li>Wireless microphones for speeches and panels.</li><li>A simple control interface for hotel staff.</li></ul><!-- /wp:list --><!-- wp:heading {&quot;level&quot;:3} --><h3>The result</h3><!-- /wp:heading --><!-- wp:paragraph --><p>The hotel now confidently markets itself for conferences, weddings and corporate events, hosting multiple functions simultaneously with independent, professional AV in each zone.</p><!-- /wp:paragraph --><!-- wp:paragraph --><p><em>One flexible hall, many events, professional AV in every zone.</em></p><!-- /wp:paragraph -->',
		'university-lecture-hall-av' => '<!-- wp:paragraph --><p><strong>Voice reinforcement, projection and campus-wide public address for a Kenyan university.</strong> Lectures in large halls were hard to hear, and announcements did not reach students spread across the campus. VTECH delivered a rugged, easy-to-run AV and PA system built for daily institutional use.</p><!-- /wp:paragraph --><!-- wp:heading {&quot;level&quot;:3} --><h3>The challenge</h3><!-- /wp:heading --><!-- wp:list --><ul><li>Poor voice clarity in large, reverberant lecture halls.</li><li>Announcements not reaching the whole campus.</li><li>Equipment needed to survive heavy daily use by non-technical staff.</li></ul><!-- /wp:list --><!-- wp:heading {&quot;level&quot;:3} --><h3>Our solution</h3><!-- /wp:heading --><!-- wp:list --><ul><li>Lecture-hall voice reinforcement with wireless lapel and handheld mics.</li><li>Projection and display for teaching, with recording-ready inputs.</li><li>Zoned campus public address for classrooms, halls and grounds.</li><li>Simple, rugged controls and staff training.</li></ul><!-- /wp:list --><!-- wp:heading {&quot;level&quot;:3} --><h3>The result</h3><!-- /wp:heading --><!-- wp:paragraph --><p>Lecturers are heard clearly in every seat, announcements reach the whole campus, and staff run the system with confidence, day after day.</p><!-- /wp:paragraph -->',
		'county-assembly-delegate-system' => '<!-- wp:paragraph --><p><strong>A delegate discussion system with recording and streaming for a county assembly chamber.</strong> Debate could not be heard clearly, and there was no reliable way to record or broadcast proceedings. VTECH installed a dignified, standards-compliant conference system fit for the public record.</p><!-- /wp:paragraph --><!-- wp:heading {&quot;level&quot;:3} --><h3>The challenge</h3><!-- /wp:heading --><!-- wp:list --><ul><li>Members could not be heard clearly across the chamber.</li><li>No orderly way to manage who was speaking.</li><li>Proceedings needed to be recorded and streamed for transparency.</li></ul><!-- /wp:list --><!-- wp:heading {&quot;level&quot;:3} --><h3>Our solution</h3><!-- /wp:heading --><!-- wp:list --><ul><li>Delegate microphone units with request-to-speak and chairman control.</li><li>Clear, feedback-free reinforcement throughout the chamber.</li><li>Recording and live streaming of all proceedings.</li><li>Documented, serviceable installation.</li></ul><!-- /wp:list --><!-- wp:heading {&quot;level&quot;:3} --><h3>The result</h3><!-- /wp:heading --><!-- wp:paragraph --><p>Debate is now orderly and clearly heard, and every session is recorded and streamed, strengthening transparency and the public record.</p><!-- /wp:paragraph -->',
		'media-house-studio-buildout' => '<!-- wp:paragraph --><p><strong>Studio sound, lighting and multi-camera live streaming for a Nairobi media house.</strong> The team needed a reliable technical backbone to produce and broadcast professional content. VTECH designed and integrated the studio audio, lighting and streaming infrastructure end to end.</p><!-- /wp:paragraph --><!-- wp:heading {&quot;level&quot;:3} --><h3>The challenge</h3><!-- /wp:heading --><!-- wp:list --><ul><li>Inconsistent studio audio and lighting quality.</li><li>No reliable multi-camera production or streaming workflow.</li><li>Downtime on air was unacceptable.</li></ul><!-- /wp:list --><!-- wp:heading {&quot;level&quot;:3} --><h3>Our solution</h3><!-- /wp:heading --><!-- wp:list --><ul><li>Treated studio audio with broadcast-grade microphones and mixing.</li><li>Consistent, camera-flattering studio lighting.</li><li>Multi-camera switching with reliable signal distribution.</li><li>Live streaming and encoding to broadcast and online platforms.</li></ul><!-- /wp:list --><!-- wp:heading {&quot;level&quot;:3} --><h3>The result</h3><!-- /wp:heading --><!-- wp:paragraph --><p>The media house now produces and broadcasts with confidence, clean audio, professional visuals and dependable streaming, on air every day.</p><!-- /wp:paragraph -->',
		'hospital-paging-pa-install' => '<!-- wp:paragraph --><p><strong>Zoned paging and clear public address for a Nairobi hospital.</strong> Announcements were muffled in corridors and inconsistent between departments. VTECH installed a reliable, zoned PA and paging system built for a 24/7 clinical environment.</p><!-- /wp:paragraph --><!-- wp:heading {&quot;level&quot;:3} --><h3>The challenge</h3><!-- /wp:heading --><!-- wp:list --><ul><li>Muffled, uneven announcements across wards and waiting areas.</li><li>No way to page specific zones independently.</li><li>System had to run reliably around the clock.</li></ul><!-- /wp:list --><!-- wp:heading {&quot;level&quot;:3} --><h3>Our solution</h3><!-- /wp:heading --><!-- wp:list --><ul><li>Zoned public address with clear, intelligible coverage.</li><li>Independent paging to departments and floors.</li><li>Calm background audio and information displays in waiting areas.</li><li>Serviceable, low-maintenance install with support.</li></ul><!-- /wp:list --><!-- wp:heading {&quot;level&quot;:3} --><h3>The result</h3><!-- /wp:heading --><!-- wp:paragraph --><p>Staff page clearly to the right areas, patients hear announcements and instructions, and waiting areas feel calmer, reliably, day and night.</p><!-- /wp:paragraph -->',
		'convention-centre-plenary-av' => '<!-- wp:paragraph --><p><strong>Scalable plenary and delegate AV for a Nairobi conference centre.</strong> The centre needed to host everything from breakout sessions to full plenaries with seamless changeovers. VTECH delivered flexible sound, delegate microphones, LED and streaming.</p><!-- /wp:paragraph --><!-- wp:heading {&quot;level&quot;:3} --><h3>The challenge</h3><!-- /wp:heading --><!-- wp:list --><ul><li>One venue hosting very different session sizes.</li><li>Need for orderly, hearable panel discussions.</li><li>Sessions had to be recorded and streamed to remote delegates.</li></ul><!-- /wp:list --><!-- wp:heading {&quot;level&quot;:3} --><h3>Our solution</h3><!-- /wp:heading --><!-- wp:list --><ul><li>Even plenary sound for large audiences.</li><li>Delegate microphone system with chairman control.</li><li>Large-format LED and readable displays.</li><li>Recording and live streaming of sessions.</li></ul><!-- /wp:list --><!-- wp:heading {&quot;level&quot;:3} --><h3>The result</h3><!-- /wp:heading --><!-- wp:paragraph --><p>Delegates hear every session clearly, changeovers are seamless, and organisers return because the centre simply works.</p><!-- /wp:paragraph -->',
		'concert-sound-lighting-hire' => '<!-- wp:paragraph --><p><strong>Line-array sound, lighting and LED hired and operated for a large outdoor event.</strong> The organiser wanted a world-class production without owning or troubleshooting equipment. VTECH delivered, set up and ran the full technical package.</p><!-- /wp:paragraph --><!-- wp:heading {&quot;level&quot;:3} --><h3>The challenge</h3><!-- /wp:heading --><!-- wp:list --><ul><li>Large outdoor crowd requiring powerful, even sound.</li><li>Stage impact through lighting and LED needed.</li><li>Organiser wanted a fully managed, turnkey setup.</li></ul><!-- /wp:list --><!-- wp:heading {&quot;level&quot;:3} --><h3>Our solution</h3><!-- /wp:heading --><!-- wp:list --><ul><li>Line-array PA with subwoofers for concert-grade coverage.</li><li>Stage lighting rig with wash and moving heads.</li><li>LED screens with live camera relay.</li><li>On-site engineers and technicians throughout the event.</li></ul><!-- /wp:list --><!-- wp:heading {&quot;level&quot;:3} --><h3>The result</h3><!-- /wp:heading --><!-- wp:paragraph --><p>The event looked and sounded professional from the first act to the last, with VTECH handling delivery, operation and pack-down end to end.</p><!-- /wp:paragraph -->',
	);
	foreach ( $projects as $p ) {
		$proj_body = isset( $proj_bodies[ $p[0] ] ) ? $proj_bodies[ $p[0] ] : '<!-- wp:paragraph --><p>' . esc_html( $p[2] ) . '</p><!-- /wp:paragraph -->';
		vtech_upsert_cpt( 'project', $p[0], $p[1], $proj_body, $p[2], $p[3], array(), array( 'industry' => array( $p[4] ) ) );
	}

	// --- Testimonials ---
	vtech_upsert_cpt( 'testimonial', 'testimonial-church', 'Church Media Lead', 'VTECH surveyed our sanctuary before quoting, and it showed. Every seat hears clearly now.', '', '', array( 'author' => 'Media Lead', '_author' => 'tst_author', 'role' => 'Church, Nairobi', '_role' => 'tst_role', 'rating' => 5, '_rating' => 'tst_rating' ) );
	vtech_upsert_cpt( 'testimonial', 'testimonial-facilities', 'Facilities Manager', 'We got a fixed quote in a day and a clean install with zero surprises. Our boardrooms finally just work.', '', '', array( 'author' => 'Facilities Manager', '_author' => 'tst_author', 'role' => 'Corporate HQ, Nairobi', '_role' => 'tst_role', 'rating' => 5, '_rating' => 'tst_rating' ) );

	// --- FAQs ---
	vtech_upsert_cpt( 'faq', 'faq-quote', 'How fast can you quote my AV project?', 'A fixed written quote within 24 hours of a free site survey in Nairobi, and within 48 hours upcountry.', '', '', array(), array( 'faq_topic' => array( 'General' ) ) );
	vtech_upsert_cpt( 'faq', 'faq-coverage', 'Do you cover the whole of Kenya?', 'Yes — all 47 counties, plus select projects across East Africa.', '', '', array(), array( 'faq_topic' => array( 'General' ) ) );
	vtech_upsert_cpt( 'faq', 'faq-hire', 'Do you hire out equipment as well as install?', 'Yes — sound, lighting, LED and conferencing hire with delivery, setup and a technician.', '', '', array(), array( 'faq_topic' => array( 'General' ) ) );

	// --- Pages ---
	// Render each pattern file to its REAL block HTML (do NOT store unexpanded
	// wp:pattern references — those don't reliably expand on the front end).
	$home_content = vtech_render_pattern_file( 'home-hero.php' )
		. "\n" . vtech_render_pattern_file( 'home-services.php' )
		. "\n" . vtech_render_pattern_file( 'home-industries.php' )
		. "\n" . vtech_render_pattern_file( 'home-stats.php' )
		. "\n" . vtech_render_pattern_file( 'home-cta-faq.php' );
	$log[] = 'Home content length: ' . strlen( $home_content ) . ' bytes (rendered from patterns)';
	// NOTE: do NOT assign a page template to Home. Leaving it default lets
	// front-page.php render the full coded homepage (a page template would
	// override front-page.php in the WP hierarchy).
	$home = vtech_upsert_page( 'home', 'Home', $home_content, '' );
	// Clear any previously-assigned template from earlier versions.
	delete_post_meta( $home, '_wp_page_template' );

	$about = vtech_upsert_page( 'about', 'About', '', 'page-about.php' );
	$contact = vtech_upsert_page( 'contact', 'Contact', '', 'page-contact.php' );
	$blog = vtech_upsert_page( 'blog', 'Blog', '' );
	$equip = vtech_upsert_page( 'equipment-hire', 'Equipment Hire', '', 'page-equipment-hire.php' );
	$consult = vtech_upsert_page( 'consultation', 'Book a Consultation', '', 'page-consultation.php' );
	$hirereq = vtech_upsert_page( 'equipment-hire-request', 'Equipment Hire Request', '', 'page-hire-request.php' );

	// --- Blog starter articles ---
	$blog_posts = array(
		array( 'church-sound-system-cost-kenya', 'How Much Does a Church Sound System Cost in Kenya?', 'blog-church-cost.webp', '<!-- wp:paragraph --><p>One of the first questions Kenyan churches ask us is simple: how much does a good sound system cost? The honest answer is that it depends on your building, your congregation size and how you worship. A small assembly of 100 people in a bare hall has very different needs from a 1,500-seat sanctuary with a full worship band.</p><!-- /wp:paragraph --><!-- wp:heading {&quot;level&quot;:3} --><h3>What drives the price</h3><!-- /wp:heading --><!-- wp:list --><ul><li><strong>Room size and acoustics</strong> hard, echoey rooms need more careful speaker placement and treatment.</li><li><strong>Speech vs music</strong> a band needs monitors, more inputs and subwoofers; preaching-led services need clarity and even coverage.</li><li><strong>Coverage</strong> balconies, overflow rooms and outdoor crusades add speakers and amplification.</li></ul><!-- /wp:list --><!-- wp:paragraph --><p>As a guide, a clean speech-first system for a small church often starts in the low hundreds of thousands of shillings, while a full band-ready sanctuary system scales from there. The smartest first step is a free site survey so we size it correctly rather than over- or under-spending.</p><!-- /wp:paragraph --><!-- wp:paragraph --><p><strong>Talk to us for a tailored quote for your sanctuary.</strong></p><!-- /wp:paragraph -->' ),
		array( 'led-screen-vs-projector-events', 'LED Screen vs Projector: Which Should You Hire?', 'blog-led-projector.webp', '<!-- wp:paragraph --><p>Should you hire an LED wall or a projector for your event? It is the most common visibility question we get, and the right answer usually comes down to one thing: light.</p><!-- /wp:paragraph --><!-- wp:heading {&quot;level&quot;:3} --><h3>Choose a projector when</h3><!-- /wp:heading --><!-- wp:list --><ul><li>You are indoors with controllable, low ambient light.</li><li>Budget is tight and the room is a conference hall or dim auditorium.</li></ul><!-- /wp:list --><!-- wp:heading {&quot;level&quot;:3} --><h3>Choose an LED screen when</h3><!-- /wp:heading --><!-- wp:list --><ul><li>Your event is outdoors, in a bright tent, or during daytime.</li><li>You want a premium, high-impact look for a stage, launch or concert.</li><li>You need the image readable from a distance and in direct sun.</li></ul><!-- /wp:list --><!-- wp:paragraph --><p>LED walls are brighter, modular and far more visible in Kenyan daytime conditions, while projectors remain a cost-effective choice indoors. Tell us your venue and lighting and we will recommend the option that looks world-class without overspending.</p><!-- /wp:paragraph -->' ),
		array( 'av-planning-corporate-agm-kenya', 'Planning AV for Your Corporate AGM in Kenya', 'blog-corporate-agm.webp', '<!-- wp:paragraph --><p>An AGM or corporate conference lives or dies on whether people can hear and see clearly. Poor audio in a hotel ballroom instantly undermines an otherwise professional event, so plan the AV early.</p><!-- /wp:paragraph --><!-- wp:heading {&quot;level&quot;:3} --><h3>The essentials</h3><!-- /wp:heading --><!-- wp:list --><ul><li><strong>Clear speech reinforcement</strong> podium and lapel microphones plus evenly-placed speakers so every delegate hears the resolutions.</li><li><strong>Screens that everyone can read</strong> LED or large projection for presentations, results and voting.</li><li><strong>Delegate microphones</strong> for questions and formal contributions in larger AGMs.</li><li><strong>Recording or streaming</strong> for members who cannot attend in person.</li></ul><!-- /wp:list --><!-- wp:paragraph --><p>We handle corporate and government events across Kenya end to end, from delegate mic systems to streaming and technicians on the day. Book a consultation and we will design the right setup for your venue and agenda.</p><!-- /wp:paragraph -->' ),
		array( 'live-streaming-events-kenya-guide', 'Live Streaming Your Event in Kenya: A Practical Guide', 'blog-live-streaming.webp', '<!-- wp:paragraph --><p>Live streaming has become essential for churches, conferences and events that want to reach people beyond the room. Getting it right is less about expensive gear and more about a reliable, well-planned workflow.</p><!-- /wp:paragraph --><!-- wp:heading {&quot;level&quot;:3} --><h3>What you actually need</h3><!-- /wp:heading --><!-- wp:list --><ul><li><strong>Cameras</strong> one or more, positioned for clear framing of the stage or speaker.</li><li><strong>A video switcher</strong> to cut between cameras, slides and lyrics live.</li><li><strong>Clean audio</strong> taken directly from the sound desk, not a camera mic.</li><li><strong>A stable internet connection</strong> and an encoder to push to Facebook, YouTube, Zoom or Teams.</li></ul><!-- /wp:list --><!-- wp:paragraph --><p>The most common mistake is treating streaming as an afterthought. Plan it alongside your sound and video, and test before you go live. We can supply the equipment and operators, or design a permanent streaming setup for your venue.</p><!-- /wp:paragraph -->' ),
	);
	foreach ( $blog_posts as $bp ) {
		$existing_bp = get_page_by_path( $bp[0], OBJECT, 'post' );
		$bp_args = array(
			'post_title'   => $bp[1],
			'post_name'    => $bp[0],
			'post_content' => $bp[3],
			'post_status'  => 'publish',
			'post_type'    => 'post',
		);
		if ( $existing_bp ) { $bp_args['ID'] = $existing_bp->ID; }
		$bp_id = wp_insert_post( $bp_args );
		if ( $bp[2] ) {
			$bp_att = vtech_import_image( $bp[2], $bp[1] );
			if ( $bp_att ) { set_post_thumbnail( $bp_id, $bp_att ); }
		}
	}

	// --- Clean up previously-seeded placeholder clients ---
	// Earlier versions seeded sample client names. Real clients are now managed
	// in the dashboard, so remove those known placeholders (only when they have
	// no uploaded logo, i.e. still a bare seed) so they no longer show beside
	// the real ones. Clients you added yourself are left untouched.
	$vtc_placeholders = array( 'Safaricom', 'KCB Group', 'Serena Hotels', 'Kenya Airways', 'Nation Media', 'Equity Bank', 'Strathmore University', 'CITAM' );
	foreach ( $vtc_placeholders as $vtc_ph ) {
		$vtc_ex = get_page_by_path( sanitize_title( $vtc_ph ), OBJECT, 'vtech_client' );
		if ( $vtc_ex && ! has_post_thumbnail( $vtc_ex->ID ) ) { wp_delete_post( $vtc_ex->ID, true ); }
	}

	// --- Force equipment-hire template even on pre-existing pages ---
	if ( $equip ) { update_post_meta( $equip, '_wp_page_template', 'page-equipment-hire.php' ); }
	$faq  = vtech_upsert_page( 'faq', 'FAQ', '<!-- wp:heading --><h2>Frequently Asked Questions</h2><!-- /wp:heading -->' );
	vtech_upsert_page( 'privacy-policy', 'Privacy Policy', '<!-- wp:heading --><h2>Privacy Policy</h2><!-- /wp:heading --><!-- wp:paragraph --><p><em>Last updated: 2026. This policy explains how VTECH Audio Visual Solutions collects, uses and protects your personal data, in line with the Kenya Data Protection Act, 2019.</em></p><!-- /wp:paragraph --><!-- wp:heading {"level":3} --><h3>1. Who we are</h3><!-- /wp:heading --><!-- wp:paragraph --><p>VTECH Audio Visual Solutions is the data controller for personal data collected through this website. Contact: info@vtechaudio.co.ke, Mpaka Plaza, Mpaka Road, Nairobi, Kenya.</p><!-- /wp:paragraph --><!-- wp:heading {"level":3} --><h3>2. What we collect</h3><!-- /wp:heading --><!-- wp:paragraph --><p>We collect information you provide through our quote and contact forms (name, email, phone, organisation, location and project details) and standard technical data (such as IP address and browser type) via cookies and analytics.</p><!-- /wp:paragraph --><!-- wp:heading {"level":3} --><h3>3. How we use it</h3><!-- /wp:heading --><!-- wp:paragraph --><p>We use your data to respond to enquiries, prepare quotations, deliver our services, and — where you consent — to send occasional updates. We do not sell your personal data.</p><!-- /wp:paragraph --><!-- wp:heading {"level":3} --><h3>4. Legal basis</h3><!-- /wp:heading --><!-- wp:paragraph --><p>We process data on the basis of your consent, to take steps at your request before entering a contract, and for our legitimate business interests.</p><!-- /wp:paragraph --><!-- wp:heading {"level":3} --><h3>5. Sharing</h3><!-- /wp:heading --><!-- wp:paragraph --><p>We share data only with trusted service providers (e.g. email and hosting) who process it on our behalf under confidentiality, and where required by law.</p><!-- /wp:paragraph --><!-- wp:heading {"level":3} --><h3>6. Retention</h3><!-- /wp:heading --><!-- wp:paragraph --><p>We keep personal data only as long as necessary for the purposes above and to meet legal obligations.</p><!-- /wp:paragraph --><!-- wp:heading {"level":3} --><h3>7. Your rights</h3><!-- /wp:heading --><!-- wp:paragraph --><p>Under the Kenya Data Protection Act you may request access to, correction or deletion of your data, and may withdraw consent at any time. Contact info@vtechaudio.co.ke to exercise these rights.</p><!-- /wp:paragraph --><!-- wp:heading {"level":3} --><h3>8. Cookies</h3><!-- /wp:heading --><!-- wp:paragraph --><p>This site uses cookies for essential functionality and analytics. You can control cookies through your browser settings.</p><!-- /wp:paragraph --><!-- wp:heading {"level":3} --><h3>9. Contact</h3><!-- /wp:heading --><!-- wp:paragraph --><p>Questions about your privacy? Email info@vtechaudio.co.ke or call +254 728135246.</p><!-- /wp:paragraph -->' );
	vtech_upsert_page( 'terms', 'Terms & Conditions', '<!-- wp:heading --><h2>Terms &amp; Conditions</h2><!-- /wp:heading --><!-- wp:paragraph --><p><em>Last updated: 2026. These Terms &amp; Conditions govern your use of the VTECH Audio Visual Solutions website and the supply of our products and services.</em></p><!-- /wp:paragraph --><!-- wp:heading {"level":3} --><h3>1. About us</h3><!-- /wp:heading --><!-- wp:paragraph --><p>VTECH Audio Visual Solutions ("VTECH", "we", "us") is an audio-visual integrator based at Mpaka Plaza, Mpaka Road, Nairobi, Kenya. You can reach us at info@vtechaudio.co.ke or +254 728135246.</p><!-- /wp:paragraph --><!-- wp:heading {"level":3} --><h3>2. Quotations &amp; pricing</h3><!-- /wp:heading --><!-- wp:paragraph --><p>All quotations are valid for 30 days from the date of issue unless stated otherwise. Prices are quoted in Kenya Shillings (KES) and, unless expressly stated, exclude VAT, delivery and installation outside the quoted scope. Written quotations issued after a site survey are fixed for the quoted scope of works.</p><!-- /wp:paragraph --><!-- wp:heading {"level":3} --><h3>3. Orders &amp; payment</h3><!-- /wp:heading --><!-- wp:paragraph --><p>Orders are confirmed on acceptance of our quotation and receipt of any required deposit. Payment terms are as stated on the quotation or invoice. Title to goods passes only on full payment; risk passes on delivery.</p><!-- /wp:paragraph --><!-- wp:heading {"level":3} --><h3>4. Installation &amp; site access</h3><!-- /wp:heading --><!-- wp:paragraph --><p>The client is responsible for providing safe, timely site access, adequate mains power and any required structural or civil works, unless included in our scope. Delays caused by site readiness may affect timelines and cost.</p><!-- /wp:paragraph --><!-- wp:heading {"level":3} --><h3>5. Equipment hire</h3><!-- /wp:heading --><!-- wp:paragraph --><p>Hired equipment remains the property of VTECH at all times. The client is responsible for the equipment from delivery to collection and for any loss or damage beyond fair wear and tear. A security deposit and valid identification may be required.</p><!-- /wp:paragraph --><!-- wp:heading {"level":3} --><h3>6. Warranty &amp; support</h3><!-- /wp:heading --><!-- wp:paragraph --><p>Installations include a 12-month support window covering workmanship. Equipment carries the manufacturer\'s warranty. Annual maintenance contracts are available separately.</p><!-- /wp:paragraph --><!-- wp:heading {"level":3} --><h3>7. Liability</h3><!-- /wp:heading --><!-- wp:paragraph --><p>To the extent permitted by Kenyan law, our total liability for any claim is limited to the value of the goods or services giving rise to the claim. We are not liable for indirect or consequential loss.</p><!-- /wp:paragraph --><!-- wp:heading {"level":3} --><h3>8. Governing law</h3><!-- /wp:heading --><!-- wp:paragraph --><p>These terms are governed by the laws of the Republic of Kenya, and disputes are subject to the exclusive jurisdiction of the Kenyan courts.</p><!-- /wp:paragraph --><!-- wp:heading {"level":3} --><h3>9. Contact</h3><!-- /wp:heading --><!-- wp:paragraph --><p>Questions about these terms? Email info@vtechaudio.co.ke or call +254 728135246.</p><!-- /wp:paragraph -->' );


	// --- Sample Hire Packages ---
	$packages = array(
		array( 'bronze-package', 'Bronze Package', 'Perfect for small meetings, workshops, private functions and small church services.', '50', 15000, array( '2 x Powered PA speakers on stands', '2 x Wired microphones', '1 x Audio mixer', 'All cabling and power extensions', '1 x On-site technician (setup, operation, pack-down)', 'Delivery, setup and collection within Nairobi' ), 'package-bronze.webp' ),
		array( 'silver-package', 'Silver Package', 'Great for mid-size events, weddings, conferences and growing church services.', '150', 35000, array( '4 x Full-range speakers on stands', '2 x Subwoofers for richer bass', '4 x Wireless microphones (handheld / lapel)', '1 x Digital mixing console', '1 x Stage monitor', 'All cabling, stands and power distribution', '1 x Professional sound engineer', 'Delivery, setup and collection' ), 'package-silver.webp' ),
		array( 'gold-package', 'Gold Package', 'Full-scale sound for large events, crusades, corporate functions and concerts.', '500', 85000, array( '8 x Speakers (mains + fills for even coverage)', '4 x Subwoofers', '8 x Wireless microphones', '1 x Digital mixing console', 'Stage monitors for performers', 'Full cabling, staging power and distribution', '1 x Sound engineer + 1 x assistant technician', 'Delivery, setup and collection (Nairobi & upcountry)' ), 'package-gold.webp' ),
		array( 'platinum-package', 'Platinum Package', 'Premium line-array production for major events, concerts and conferences.', '1000', 250000, array( 'Professional line-array PA system', '8 x Subwoofers for concert-grade low end', '12 x Wireless microphones', '1 x Digital mixing console', 'Stage monitors + in-ear monitoring option', '1 x Indoor/outdoor LED screen', 'Stage & event lighting rig', 'Full cabling, rigging and power distribution', '2 x Sound engineers + lighting technician', 'Delivery, setup, on-site support and collection' ), 'equip-line-array.webp' ),
	);
	foreach ( $packages as $pk ) {
		$pk_img = isset( $pk[6] ) ? $pk[6] : 'sound-systems.webp';
		$pid = vtech_upsert_cpt( 'hire_package', $pk[0], $pk[1], '<!-- wp:paragraph --><p>' . esc_html( $pk[2] ) . '</p><!-- /wp:paragraph -->', $pk[2], $pk_img );
		// Plain, ACF-independent meta the single-package template reads directly.
		update_post_meta( $pid, 'vtech_pkg_equipment', $pk[5] );
		update_post_meta( $pid, 'vtech_pkg_services', array( 'Delivery', 'Setup', 'Professional technician / engineer' ) );
		update_post_meta( $pid, 'vtech_pkg_capacity', $pk[3] );
		update_post_meta( $pid, 'vtech_pkg_price', $pk[4] );
		update_post_meta( $pid, 'price', $pk[4] );
		update_post_meta( $pid, '_price', 'pkg_price' );
		update_post_meta( $pid, 'capacity', $pk[3] );
		update_post_meta( $pid, '_capacity', 'pkg_capacity' );
		update_post_meta( $pid, 'deposit_pct', 50 );
		update_post_meta( $pid, '_deposit_pct', 'pkg_deposit' );
		// Equipment repeater (ACF format) — this is the detailed 'What's included' list.
		update_post_meta( $pid, 'equipment', count( $pk[5] ) );
		update_post_meta( $pid, '_equipment', 'pkg_equipment' );
		foreach ( $pk[5] as $ei => $etext ) {
			update_post_meta( $pid, 'equipment_' . $ei . '_qty', '' );
			update_post_meta( $pid, '_equipment_' . $ei . '_qty', 'pkg_eq_qty' );
			update_post_meta( $pid, 'equipment_' . $ei . '_item', $etext );
			update_post_meta( $pid, '_equipment_' . $ei . '_item', 'pkg_eq_item' );
		}
		// Sidebar highlights (short version = first 4 equipment items).
		$hl_items = array_slice( $pk[5], 0, 4 );
		update_post_meta( $pid, 'highlights', count( $hl_items ) );
		update_post_meta( $pid, '_highlights', 'pkg_highlights' );
		foreach ( $hl_items as $hi => $htext ) {
			update_post_meta( $pid, 'highlights_' . $hi . '_text', $htext );
			update_post_meta( $pid, '_highlights_' . $hi . '_text', 'pkg_hl' );
		}
		update_post_meta( $pid, 'services', array( 'Delivery', 'Setup', 'Professional technician / engineer' ) );
		update_post_meta( $pid, '_services', 'pkg_services' );
	}

	// --- Front page + posts page ---
	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $home );
	update_option( 'page_for_posts', $blog );

	// --- Menus ---
	$loc = array();
	$primary = wp_get_nav_menu_object( 'Primary Menu' );
	if ( ! $primary ) { $primary_id = wp_create_nav_menu( 'Primary Menu' ); } else { $primary_id = $primary->term_id; }
	// Clear + rebuild primary.
	foreach ( (array) wp_get_nav_menu_items( $primary_id ) as $it ) { wp_delete_post( $it->ID, true ); }
	$nav = array(
		array( 'Home', home_url( '/' ) ),
		array( 'Services', get_post_type_archive_link( 'service' ) ),
		array( 'Industries', get_post_type_archive_link( 'industry' ) ),
		array( 'Projects', get_post_type_archive_link( 'project' ) ),
		array( 'Hire Packages', get_post_type_archive_link( 'hire_package' ) ),
		array( 'About', get_permalink( $about ) ),
		array( 'Consultation', get_permalink( $consult ) ),
	);
	foreach ( $nav as $n ) {
		if ( ! $n[1] ) { continue; }
		wp_update_nav_menu_item( $primary_id, 0, array( 'menu-item-title' => $n[0], 'menu-item-url' => $n[1], 'menu-item-status' => 'publish' ) );
	}
	$loc['primary'] = $primary_id;

	// Footer menu = key services.
	$footer = wp_get_nav_menu_object( 'Footer Menu' );
	$footer_id = $footer ? $footer->term_id : wp_create_nav_menu( 'Footer Menu' );
	foreach ( (array) wp_get_nav_menu_items( $footer_id ) as $it ) { wp_delete_post( $it->ID, true ); }
	foreach ( array( 'Sound Systems' => '/services/sound-systems/', 'LED Screens' => '/services/led-screens/', 'Conference Systems' => '/services/conference-systems/', 'Contact' => '/contact/' ) as $t => $u ) {
		wp_update_nav_menu_item( $footer_id, 0, array( 'menu-item-title' => $t, 'menu-item-url' => home_url( $u ), 'menu-item-status' => 'publish' ) );
	}
	$loc['footer'] = $footer_id;
	set_theme_mod( 'nav_menu_locations', $loc );

	// --- Remove the default WP "Sample Page" + "Hello World" so the demo is clean ---
	$sample = get_page_by_path( 'sample-page' );
	if ( $sample ) { wp_delete_post( $sample->ID, true ); }
	$hello = get_post( 1 );
	if ( $hello && 'post' === $hello->post_type && 'Hello world!' === $hello->post_title ) { wp_trash_post( 1 ); }

	flush_rewrite_rules();
	update_option( 'vtech_setup_done', 1 );

	// --- Tally what exists now (proof the build worked) ---
	$log[] = 'Services: ' . wp_count_posts( 'service' )->publish;
	$log[] = 'Industries: ' . wp_count_posts( 'industry' )->publish;
	$log[] = 'Projects: ' . wp_count_posts( 'project' )->publish;
	$log[] = 'Testimonials: ' . wp_count_posts( 'testimonial' )->publish;
	$log[] = 'FAQs: ' . wp_count_posts( 'faq' )->publish;
	$log[] = 'Pages: ' . wp_count_posts( 'page' )->publish;
	$log[] = 'Front page set to: Home (#' . (int) $home . ')';
	$log[] = 'Primary menu items: ' . count( (array) wp_get_nav_menu_items( $primary_id ) );
	$log[] = 'Done. Visit Settings -> Permalinks -> Save Changes once, then hard-refresh the site.';
	return implode( "\n", $log );
}
