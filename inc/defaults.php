<?php
/**
 * Every editable theme setting, grouped by Customizer section.
 *
 * Each field: [ label, default, control type ]. Control types: text, textarea,
 * url, email, image, number, datetime, lines (one item per line).
 *
 * @package Alphonsus
 */

defined( 'ABSPATH' ) || exit;

function alph_settings_schema() {
	static $schema = null;
	if ( null !== $schema ) {
		return $schema;
	}

	$schema = array(
		'alph_site'       => array(
			'title'  => __( 'Site details', 'alphonsus' ),
			'fields' => array(
				'phone'        => array( __( 'Call / WhatsApp number', 'alphonsus' ), '+234 816 076 4284', 'text' ),
				'email'        => array( __( 'Support email', 'alphonsus' ), 'Alphonsuslibrary@gmail.com', 'email' ),
				'address'      => array( __( 'Address', 'alphonsus' ), 'Admiralty Way, Lekki, Lagos, Nigeria', 'text' ),
				'topbar_msg_1' => array( __( 'Top bar message 1', 'alphonsus' ), 'Swedish & English Bilingual Curricula', 'text' ),
				'topbar_msg_2' => array( __( 'Top bar message 2', 'alphonsus' ), 'Curated Book Bundles to Inspire Learning • Worldwide Delivery Available', 'text' ),
				'donate_url'   => array( __( 'Donate link (default: the Foundation page donation form)', 'alphonsus' ), '/foundation/#donate', 'url' ),
				'notify_email' => array( __( 'Send form entries to (blank = site admin email)', 'alphonsus' ), '', 'email' ),
				'facebook'     => array( __( 'Facebook URL', 'alphonsus' ), 'https://www.facebook.com/Alphonsuslibrary', 'url' ),
				'instagram'    => array( __( 'Instagram URL', 'alphonsus' ), 'https://www.instagram.com/alphonsus_library', 'url' ),
				'youtube'      => array( __( 'YouTube URL', 'alphonsus' ), 'https://youtube.com/@alphonsuslibraryforchildren', 'url' ),
				'tiktok'       => array( __( 'TikTok URL', 'alphonsus' ), 'https://www.tiktok.com/@alphonsus_library', 'url' ),
				'footer_about' => array( __( 'Footer description', 'alphonsus' ), 'Empowering minds, building futures through accessible literature, early learning, and language education.', 'textarea' ),
				'payments'     => array( __( 'Footer payment badges', 'alphonsus' ), "Paystack\nVisa\nMastercard\nVerve", 'lines' ),
			),
		),
		'alph_hero'       => array(
			'title'  => __( 'Homepage: Hero', 'alphonsus' ),
			'fields' => array(
				'hero_badge'      => array( __( 'Campaign badge', 'alphonsus' ), 'Back to School Campaign', 'text' ),
				'hero_offer'      => array( __( 'Offer label (shown with countdown)', 'alphonsus' ), 'Special offer ends in', 'text' ),
				'hero_offer_end'  => array( __( 'Offer end date (YYYY-MM-DD HH:MM, blank hides countdown)', 'alphonsus' ), '', 'datetime' ),
				'hero_tagline'    => array( __( 'Tagline', 'alphonsus' ), 'The Right Books. The Bright Future.', 'text' ),
				'hero_title'      => array( __( 'Headline', 'alphonsus' ), 'Empowering Minds, Building Futures Through the Joy of Reading', 'textarea' ),
				'hero_text'       => array( __( 'Intro text', 'alphonsus' ), 'Curated book bundles, inspiring African & global children’s stories, and comprehensive Swedish language learning resources designed to nurture bilingual, resilient young leaders.', 'textarea' ),
				'hero_btn1'       => array( __( 'Primary button text', 'alphonsus' ), 'Shop Book Bundles', 'text' ),
				'hero_btn1_url'   => array( __( 'Primary button link', 'alphonsus' ), '/product-category/bundles/', 'url' ),
				'hero_btn1_price' => array( __( 'Primary button price chip', 'alphonsus' ), 'From ₦40,000', 'text' ),
				'hero_btn2'       => array( __( 'Secondary button text', 'alphonsus' ), 'Browse Story Books', 'text' ),
				'hero_btn2_url'   => array( __( 'Secondary button link', 'alphonsus' ), '/product-category/story-book/', 'url' ),
				'hero_image'      => array( __( 'Hero image', 'alphonsus' ), 0, 'image' ),
				'hero_card_title' => array( __( 'Image caption title', 'alphonsus' ), 'Curated Bundle Sets', 'text' ),
				'hero_card_text'  => array( __( 'Image caption text', 'alphonsus' ), 'Picture Dictionary, Golden Hands & Early Readers', 'text' ),
				'stats'           => array( __( 'Stats (value | label, one per line) — empty hides', 'alphonsus' ), '', 'lines' ),
				'features'        => array( __( 'Feature strip (title | subtitle, one per line)', 'alphonsus' ), "Curated for All Ages | Toddlers to polyglot youth\nQuality Books They’ll Love | Rich colours & tactile covers\nLearn Today, Lead Tomorrow | Global literacy minded\nGreat Value for Parents | Savings on curated bundles", 'lines' ),
			),
		),
		'alph_sections'   => array(
			'title'  => __( 'Homepage: Product sections', 'alphonsus' ),
			'fields' => array(
				'pills'          => array( __( 'Category pills (label | category slug or URL)', 'alphonsus' ), "All Books | books\nMagical Story Books | story-book\nSwedish Language School | sweden-book\nWellness Series | wellness\nMerch & Pre-Sales | merch", 'lines' ),
				'story_title'    => array( __( 'Story section title', 'alphonsus' ), 'Story Books', 'text' ),
				'story_sub'      => array( __( 'Story section subtitle', 'alphonsus' ), 'Explore Magical Stories for Curious Kids!', 'text' ),
				'story_cat'      => array( __( 'Story section category slug', 'alphonsus' ), 'story-book', 'text' ),
				'story_ids'      => array( __( 'Story section product IDs (optional, comma separated)', 'alphonsus' ), '3257,3255,2130,2121', 'text' ),
				'story_link'     => array( __( 'Story section link text', 'alphonsus' ), 'View all story collection', 'text' ),
				'swedish_title'  => array( __( 'Swedish section title', 'alphonsus' ), 'Swedish Books', 'text' ),
				'swedish_sub'    => array( __( 'Swedish section subtitle', 'alphonsus' ), 'Your Swedish Learning Starts Here — Curated Textbooks, Workbooks & Guides', 'text' ),
				'swedish_cat'    => array( __( 'Swedish section category slug', 'alphonsus' ), 'sweden-book', 'text' ),
				'swedish_ids'    => array( __( 'Swedish section product IDs (optional)', 'alphonsus' ), '3261,2142,2133,2127', 'text' ),
				'swedish_link'   => array( __( 'Swedish section note', 'alphonsus' ), 'Full course available (A1–B1)', 'text' ),
				'wellness_title' => array( __( 'Wellness section title', 'alphonsus' ), 'Wellness Session', 'text' ),
				'wellness_sub'   => array( __( 'Wellness section subtitle', 'alphonsus' ), 'Explore Our Wellness Session — Compassionate Guides on Health, Mind & Wellness', 'text' ),
				'wellness_cat'   => array( __( 'Wellness section category slug', 'alphonsus' ), 'wellness', 'text' ),
				'wellness_ids'   => array( __( 'Wellness section product IDs (optional)', 'alphonsus' ), '', 'text' ),
				'wellness_link'  => array( __( 'Wellness section note', 'alphonsus' ), 'Discreet & confident solutions', 'text' ),
				'default_author' => array( __( 'Default author shown on product cards', 'alphonsus' ), 'Chika Alphonsus', 'text' ),
			),
		),
		'alph_promo'      => array(
			'title'  => __( 'Homepage: Swedish promo', 'alphonsus' ),
			'fields' => array(
				'promo_badge'   => array( __( 'Badge', 'alphonsus' ), 'Swedish Language Programme', 'text' ),
				'promo_title'   => array( __( 'Title', 'alphonsus' ), 'Master Swedish With Our Beginner-Friendly Books', 'text' ),
				'promo_text'    => array( __( 'Text', 'alphonsus' ), 'From everyday vocabulary and native phonetics to interactive workbooks tailored for African and international learners. Start your Scandinavian fluency journey with confidence today.', 'textarea' ),
				'promo_product' => array( __( 'Featured product ID', 'alphonsus' ), 2124, 'number' ),
				'promo_label'   => array( __( 'Featured product label', 'alphonsus' ), 'Pronunciation & Grammar', 'text' ),
				'promo_levels'  => array( __( 'Level chips (comma separated)', 'alphonsus' ), 'A1, A2, B1', 'text' ),
				'promo_points'  => array( __( 'Checklist (one per line)', 'alphonsus' ), "Bilingual English/Swedish layout\nA1, A2 & B1 CEFR alignment\nPronunciation guides with native phonetics\nPractical grammar exercises & answers", 'lines' ),
				'promo_btn'     => array( __( 'Button text', 'alphonsus' ), 'Order The Books Now', 'text' ),
				'promo_btn_url' => array( __( 'Button link', 'alphonsus' ), '/product-category/sweden-book/', 'url' ),
			),
		),
		'alph_presale'    => array(
			'title'  => __( 'Homepage: Pre-sales', 'alphonsus' ),
			'fields' => array(
				'presale_badge'   => array( __( 'Badge', 'alphonsus' ), 'Collector Edition', 'text' ),
				'presale_title'   => array( __( 'Title', 'alphonsus' ), 'Hardcover Pre-Sales', 'text' ),
				'presale_text'    => array( __( 'Text', 'alphonsus' ), 'Secure your hardcover copies of our upcoming titles. Tell us which books you would like and we will contact you with prices and dates.', 'textarea' ),
				'presale_points'  => array( __( 'Highlights (one per line) — empty hides', 'alphonsus' ), '', 'lines' ),
				'presale_percent' => array( __( 'Allocation reserved (%) — 0 hides the bar', 'alphonsus' ), 0, 'number' ),
				'presale_books'   => array( __( 'Books offered in the form (one per line)', 'alphonsus' ), "Swedish A1–A2 workbooks & Textbook\nPronunciation book 1 & 2\nSwedish guide\nKofi's Magical Journey\nJafari's Big Heart\nThe Girl Who Spoke to Animals\nRainbow Colors of Africa\nThe Secret Garden of Limba\nIsland of Chocolate\nVagina book\nBreast book", 'lines' ),
			),
		),
		'alph_community'  => array(
			'title'  => __( 'Homepage: News, foundation & newsletter', 'alphonsus' ),
			'fields' => array(
				'news_eyebrow'     => array( __( 'News eyebrow', 'alphonsus' ), 'Stay Connected', 'text' ),
				'news_title'       => array( __( 'News title', 'alphonsus' ), 'Events and News', 'text' ),
				'found_badge'      => array( __( 'Foundation badge', 'alphonsus' ), 'Alphonsus Foundation', 'text' ),
				'found_title'      => array( __( 'Foundation title', 'alphonsus' ), 'Giving Every Child the Right to Read & Dream', 'text' ),
				'found_text'       => array( __( 'Foundation text', 'alphonsus' ), 'Through our community book outreach, we provide free storybooks, school supplies, and reading sessions to underserved primary schools across Nigeria and West Africa.', 'textarea' ),
				'found_points'     => array( __( 'Foundation highlights (one per line) — empty hides', 'alphonsus' ), '', 'lines' ),
				'found_btn'        => array( __( 'Foundation button text', 'alphonsus' ), 'Support The Foundation (Donate)', 'text' ),
				'newsletter_eyebrow' => array( __( 'Newsletter eyebrow', 'alphonsus' ), 'Lifelong literacy & polyglot futures', 'text' ),
				'newsletter_title' => array( __( 'Newsletter title', 'alphonsus' ), 'Join our young readers & language learners club', 'text' ),
				'newsletter_text'  => array( __( 'Newsletter text', 'alphonsus' ), 'Receive weekly educational recommendations, Swedish practice worksheets, early access to new imports, and community library updates.', 'textarea' ),
			),
		),
		'alph_demo'       => array(
			'title'  => __( 'Demo mode', 'alphonsus' ),
			'fields' => array(
				'demo_on'         => array( __( 'Turn on demo mode (logged-in staff are never timed or locked). Switching it off and on again restarts every visitor’s timer.', 'alphonsus' ), 0, 'checkbox' ),
				'demo_minutes'    => array( __( 'Demo length (minutes)', 'alphonsus' ), 30, 'number' ),
				'demo_lock_hours' => array( __( 'Lock the site for (hours) once the demo ends', 'alphonsus' ), 2, 'number' ),
				'demo_by_ip'      => array( __( 'Stop private-window restarts by also tracking the visitor’s network (people on the same Wi-Fi share one timer)', 'alphonsus' ), 1, 'checkbox' ),
				'demo_banner'     => array( __( 'Banner message', 'alphonsus' ), 'You’re viewing a demo of the Alphonsus Library website.', 'text' ),
				'demo_lock_title' => array( __( 'Lock screen title', 'alphonsus' ), 'Demo time is over', 'text' ),
				'demo_lock_text'  => array( __( 'Lock screen message', 'alphonsus' ), 'Thank you for exploring the Alphonsus Library demo. The preview is paused for now and will open again automatically when the timer below runs out.', 'textarea' ),
			),
		),
	);

	$schema += alph_pages_schema();

	return $schema;
}

/**
 * Flat key => [label, default, type] map.
 */
function alph_settings_fields() {
	static $fields = null;
	if ( null === $fields ) {
		$fields = array();
		foreach ( alph_settings_schema() as $section ) {
			$fields += $section['fields'];
		}
	}
	return $fields;
}

/**
 * Read a theme setting, falling back to its default.
 */
function alph_mod( $key ) {
	$fields  = alph_settings_fields();
	$default = isset( $fields[ $key ] ) ? $fields[ $key ][1] : '';
	return get_theme_mod( 'alph_' . $key, $default );
}

/**
 * Setting stored as one item per line; optionally split each line on "|".
 */
function alph_lines( $key, $split = false ) {
	$out = array();
	foreach ( preg_split( '/\r\n|\r|\n/', (string) alph_mod( $key ) ) as $line ) {
		$line = trim( $line );
		if ( '' === $line ) {
			continue;
		}
		$out[] = $split ? array_map( 'trim', array_pad( explode( '|', $line, 2 ), 2, '' ) ) : $line;
	}
	return $out;
}

/**
 * Resolve a setting URL; root-relative paths become absolute on this site.
 */
function alph_url( $key ) {
	$url = trim( (string) alph_mod( $key ) );
	if ( '' === $url ) {
		return '';
	}
	return 0 === strpos( $url, '/' ) ? home_url( $url ) : $url;
}

/**
 * Paragraphs from a textarea setting (blank line separates paragraphs).
 */
function alph_paragraphs( $key ) {
	$out = '';
	foreach ( preg_split( '/\R\s*\R/', trim( (string) alph_mod( $key ) ) ) as $para ) {
		if ( '' !== trim( $para ) ) {
			$out .= '<p>' . esc_html( trim( $para ) ) . '</p>';
		}
	}
	return $out;
}

/**
 * Comma-separated image IDs from a setting.
 */
function alph_image_ids( $key ) {
	return array_values( array_filter( array_map( 'absint', explode( ',', (string) alph_mod( $key ) ) ), 'wp_attachment_is_image' ) );
}

/**
 * True when a link points away from this site.
 */
function alph_is_external( $url ) {
	$host = wp_parse_url( $url, PHP_URL_HOST );
	return $host && wp_parse_url( home_url(), PHP_URL_HOST ) !== $host;
}
