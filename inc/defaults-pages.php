<?php
/**
 * Editable settings for the inner page templates (Customizer → Alphonsus Pages).
 *
 * Content rule: descriptive copy ships with sensible defaults. Anything that is
 * a factual claim — statistics, ratings, testimonials, named people or partners,
 * accreditations, addresses, opening hours, specific programmes — defaults to
 * empty, and its section stays hidden until the site owner fills it in.
 *
 * List fields ("lines") take one item per line, with parts separated by "|".
 *
 * @package Alphonsus
 */

defined( 'ABSPATH' ) || exit;

function alph_pages_schema() {
	return array(
		/* ------------------------------------------------------------------ */
		'alph_p_shop'       => array(
			'title'  => __( 'Book Shop', 'alphonsus' ),
			'panel'  => 'alph_pages_panel',
			'fields' => array(
				'shop_eyebrow'    => array( __( 'Eyebrow', 'alphonsus' ), 'Curated Scandinavian & West African volumes', 'text' ),
				'shop_title'      => array( __( 'Title', 'alphonsus' ), 'The Alphonsus Book Shop', 'text' ),
				'shop_text'       => array( __( 'Intro', 'alphonsus' ), 'Immersive early learning literature, Swedish curriculum textbooks, and mindful wellness guides. Built to kindle cultural empathy, dual-language confidence, and joyful bedtime habits.', 'textarea' ),
				'shop_info'       => array( __( 'Info cards (title | text) — use {count} for the live number of books', 'alphonsus' ), "Worldwide delivery | Dispatched from Lagos to readers everywhere\n{count} titles in stock | Story books, Swedish curricula & wellness guides", 'lines' ),
				'shop_bulk_title' => array( __( 'Sidebar box title', 'alphonsus' ), 'Bulk school orders', 'text' ),
				'shop_bulk_text'  => array( __( 'Sidebar box text', 'alphonsus' ), 'Equipping a classroom or community reading room? Ask us about bundle pricing for schools.', 'textarea' ),
				'shop_ed_eyebrow' => array( __( 'Editorial eyebrow', 'alphonsus' ), 'Curator’s essay & philosophy', 'text' ),
				'shop_ed_title'   => array( __( 'Editorial title', 'alphonsus' ), 'Why Bilingual Literacy Matters in an Interconnected World', 'text' ),
				'shop_ed_text'    => array( __( 'Editorial text', 'alphonsus' ), 'At Alphonsus Library, we believe that books are passport stamps for the imagination. When children encounter the natural beauty of West African folklore alongside the elegant precision of the Swedish tongue, they don’t merely learn nouns and verbs — they build bridges of cultural nuance and lifelong empathy.', 'textarea' ),
				'shop_ed_points'  => array( __( 'Editorial points (title | text)', 'alphonsus' ), "Phonetic memory | Dual-text storybooks sharpen auditory discrimination and word recognition.\nRooted identity | Children see their own heritage celebrated with elegance and literary pride.", 'lines' ),
				'shop_ed_image'   => array( __( 'Editorial image', 'alphonsus' ), 0, 'image' ),
			),
		),

		/* ------------------------------------------------------------------ */
		'alph_p_foundation' => array(
			'title'  => __( 'Foundation page', 'alphonsus' ),
			'panel'  => 'alph_pages_panel',
			'fields' => array(
				'fd_badge'          => array( __( 'Hero badge', 'alphonsus' ), 'Alphonsus Library’s Educational Foundation', 'text' ),
				'fd_title'          => array( __( 'Hero title', 'alphonsus' ), 'Giving Every Child the Right to Read, Dream, and Flourish.', 'text' ),
				'fd_text'           => array( __( 'Hero text', 'alphonsus' ), 'Igniting a renaissance of childhood literacy — putting good books, reading spaces and caring mentors within reach of children across Nigeria and West Africa.', 'textarea' ),
				'fd_btn1'           => array( __( 'Primary button', 'alphonsus' ), 'Support the mission • Donate', 'text' ),
				'fd_btn2'           => array( __( 'Secondary button', 'alphonsus' ), 'Explore our pillars', 'text' ),
				'fd_stats'          => array( __( 'Hero stats (value | label) — leave empty to hide', 'alphonsus' ), '', 'lines' ),
				'fd_image'          => array( __( 'Hero image', 'alphonsus' ), 3095, 'image' ),
				'fd_caption_label'  => array( __( 'Image caption label', 'alphonsus' ), 'Active outreach campaign', 'text' ),
				'fd_caption_title'  => array( __( 'Image caption title (empty hides caption)', 'alphonsus' ), '', 'text' ),
				'fd_caption_text'   => array( __( 'Image caption text', 'alphonsus' ), '', 'text' ),
				'fd_don_eyebrow'    => array( __( 'Donate eyebrow', 'alphonsus' ), 'Direct impact philanthropy', 'text' ),
				'fd_don_title'      => array( __( 'Donate title', 'alphonsus' ), 'Support The Foundation — Donate Today', 'text' ),
				'fd_don_text'       => array( __( 'Donate text', 'alphonsus' ), 'Every gift directly finances physical books, community reading nooks and support for the educators who run them.', 'textarea' ),
				'fd_tiers'          => array( __( 'Donation tiers (amount in ₦ | title | text | tag)', 'alphonsus' ), "5000 | Phonics Literacy Pair | Bilingual early-reader books and a vocabulary workbook for young readers. |\n20000 | Classroom Book Chest | A sturdy chest of storybooks and Swedish dual-texts for a classroom. | Most needed\n100000 | Permanent Village Reading Nook | Shelving, rugs and a starter library for a community reading corner. |", 'lines' ),
				'fd_pillars_eyebrow'=> array( __( 'Pillars eyebrow', 'alphonsus' ), 'Our strategic architecture', 'text' ),
				'fd_pillars_title'  => array( __( 'Pillars title', 'alphonsus' ), 'The Pillars of Literary Equity', 'text' ),
				'fd_pillars_text'   => array( __( 'Pillars intro', 'alphonsus' ), 'Rather than simply dropping off old paperbacks, we build lasting reading infrastructure, nourish homegrown authors, and bring books directly to children.', 'textarea' ),
				'fd_pillars'        => array( __( 'Pillars (title | text | image ID | impact note) — empty hides section', 'alphonsus' ), '', 'lines' ),
				'fd_hubs'           => array( __( 'Impact hubs (name | location | text) — empty hides section', 'alphonsus' ), '', 'lines' ),
				'fd_alloc'          => array( __( 'Fund allocation (label | percent | text) — empty hides section', 'alphonsus' ), '', 'lines' ),
				'fd_alloc_report'   => array( __( 'Financial report URL (optional)', 'alphonsus' ), '', 'url' ),
				'fd_partners'       => array( __( 'Partners (one name per line) — empty hides section', 'alphonsus' ), '', 'lines' ),
				'fd_champions'      => array( __( 'Champions (name | location & role | quote) — empty hides section', 'alphonsus' ), '', 'lines' ),
				'fd_gift_title'     => array( __( 'Gift books form title', 'alphonsus' ), 'Gift Books or Your Time', 'text' ),
				'fd_gift_text'      => array( __( 'Gift books form text', 'alphonsus' ), 'Have gently-read children’s books to spare? Or wish to help run weekend read-aloud sessions? Tell us how you’d like to help and our team will get in touch.', 'textarea' ),
				'fd_gift_points'    => array( __( 'Gift books highlights (title | text) — empty hides', 'alphonsus' ), '', 'lines' ),
				'fd_gift_rules'     => array( __( 'Book acceptance guidelines — empty hides', 'alphonsus' ), '', 'textarea' ),
			),
		),

		/* ------------------------------------------------------------------ */
		'alph_p_about'      => array(
			'title'  => __( 'About page', 'alphonsus' ),
			'panel'  => 'alph_pages_panel',
			'fields' => array(
				'ab_badge'         => array( __( 'Hero badge', 'alphonsus' ), 'Our story', 'text' ),
				'ab_title'         => array( __( 'Hero title', 'alphonsus' ), 'Bridging Continents Through Literature, Heritage, & Language.', 'text' ),
				'ab_text'          => array( __( 'Hero text', 'alphonsus' ), 'Alphonsus Library was conceived as a bridge between West Africa’s vibrant oral storytelling traditions and the warm, egalitarian pedagogy of Scandinavia — kindling curiosity and linguistic confidence across borders.', 'textarea' ),
				'ab_stats'         => array( __( 'Hero stats (value | label) — empty hides', 'alphonsus' ), '', 'lines' ),
				'ab_image'         => array( __( 'Hero image', 'alphonsus' ), 92, 'image' ),
				'ab_story_eyebrow' => array( __( 'Story eyebrow', 'alphonsus' ), 'The genesis & philosophy', 'text' ),
				'ab_story_title'   => array( __( 'Story title', 'alphonsus' ), 'Uniting West African Folktales with Scandinavian Educational Rigor', 'text' ),
				'ab_story_text'    => array( __( 'Story text (blank line = new paragraph)', 'alphonsus' ), "Growing up between the lyrical storytelling evenings of West Africa and the quiet, structured reading sanctuaries of Northern Europe, our founder saw an unexpected kinship: both cultures prize imaginative honesty, ecological kinship and the sacred nature of communal learning.\n\nToo often, language learning materials feel sterile and detached from the lived heritage of African children. Alphonsus Library exists to change that — pairing the mythic resonance of West African fables with modern Swedish communicative methods, so that children grow into confident, empathetic global citizens.", 'textarea' ),
				'ab_story_images'  => array( __( 'Story images (comma-separated image IDs)', 'alphonsus' ), '', 'text' ),
				'ab_pillars'       => array( __( 'Core pillars (title | text)', 'alphonsus' ), "Cultural Synthesis | Honouring ancestral folk wisdom alongside modern global connectivity — literature that treats West African heritage and Nordic ethics as natural conversation partners.\nPolyglot Empowerment | Making European languages approachable for African learners through culturally rooted materials, phonetic guides and joyful practice.\nAccessible Literacy | Thoughtfully priced bundles and community reading initiatives, because quality literature should never be a luxury reserved for the few.", 'lines' ),
				'ab_impact'        => array( __( 'Impact numbers (value | label | text) — empty hides', 'alphonsus' ), '', 'lines' ),
				'ab_founder_name'  => array( __( 'Founder name', 'alphonsus' ), 'Chika Alphonsus', 'text' ),
				'ab_founder_role'  => array( __( 'Founder role', 'alphonsus' ), 'Author, educator & founder', 'text' ),
				'ab_founder_quote' => array( __( 'Founder quote (empty hides the quote)', 'alphonsus' ), '', 'textarea' ),
				'ab_founder_bio'   => array( __( 'Founder bio (blank line = new paragraph)', 'alphonsus' ), "Chika Alphonsus is a visionary educator, author, and entrepreneur transforming how Africans learn, grow, and build wealth. As the founder of Alphonsus Swedish School, she is the first African woman to teach Swedish to a global audience, helping thousands embrace new opportunities abroad through language, culture, and confidence.\n\nBeyond language, she has a diploma in child psychology. Chika’s mission expands into entrepreneurship, real estate, and women’s wellness, where she empowers individuals to create freedom through knowledge and discipline. Her books and courses are rooted in authenticity, faith, and purpose.", 'textarea' ),
				'ab_founder_image' => array( __( 'Founder photo', 'alphonsus' ), 3076, 'image' ),
				'ab_timeline'      => array( __( 'Milestones (year | title | text) — empty hides', 'alphonsus' ), '', 'lines' ),
				'ab_gallery'       => array( __( 'Gallery (comma-separated image IDs) — empty hides', 'alphonsus' ), '', 'text' ),
				'ab_cta_title'     => array( __( 'Partner banner title', 'alphonsus' ), 'Partner with Our Academy or Sponsor a Library Corner.', 'text' ),
				'ab_cta_text'      => array( __( 'Partner banner text', 'alphonsus' ), 'Whether you represent a school seeking bilingual Swedish curricula, or a philanthropic patron hoping to endow a reading space, we would love to hear from you.', 'textarea' ),
			),
		),

		/* ------------------------------------------------------------------ */
		'alph_p_contact'    => array(
			'title'  => __( 'Contact page', 'alphonsus' ),
			'panel'  => 'alph_pages_panel',
			'fields' => array(
				'ct_eyebrow'  => array( __( 'Hero eyebrow', 'alphonsus' ), 'Support & concierge desk', 'text' ),
				'ct_title'    => array( __( 'Hero title', 'alphonsus' ), 'Get in Touch with Our Curators & Educators', 'text' ),
				'ct_text'     => array( __( 'Hero text', 'alphonsus' ), 'We are here to guide your reading journey, answer questions about your Swedish studies, and track your parcels around the world.', 'textarea' ),
				'ct_image'    => array( __( 'Hero image', 'alphonsus' ), 92, 'image' ),
				'ct_depts'    => array( __( 'Department cards (title | text | email | phone | link)', 'alphonsus' ), "Book Orders & Logistics | Help with orders, bundles, invoices and worldwide delivery. | Alphonsuslibrary@gmail.com | +234 816 076 4284 |\nSwedish School | Level guidance, cohorts and enrolment requests. | | | /swedish-school/\nAlphonsus Foundation | Donations, book drives and literacy partnerships. | | | /foundation/\nVisit Us | Admiralty Way, Lekki, Lagos, Nigeria | | |", 'lines' ),
				'ct_subjects' => array( __( 'Form subject options (one per line)', 'alphonsus' ), "Book order or delivery\nSwedish School\nFoundation & donations\nWellness books\nBulk / school orders\nSomething else", 'lines' ),
				'ct_place'    => array( __( 'Location card title', 'alphonsus' ), 'Alphonsus Library, Lagos', 'text' ),
				'ct_hours'    => array( __( 'Opening hours (day | hours) — empty hides', 'alphonsus' ), '', 'lines' ),
				'ct_phones'   => array( __( 'Other phone numbers (one per line)', 'alphonsus' ), "+233 53 277 6020\n+90 548 850 63 38", 'lines' ),
				'ct_faq'      => array( __( 'FAQ (question | answer)', 'alphonsus' ), "Do you deliver outside Nigeria? | Yes — worldwide delivery is available. Delivery options and costs are shown at checkout before you pay.\nCan I cancel an order or get a refund? | All sales are final once an order is confirmed. If an item arrives damaged or materially different from what you ordered, contact us promptly and we will find a suitable remedy. See our No Refund & Cancellation Policy for details.\nHow do I receive digital books? | Digital products are delivered electronically to the email address used at checkout once payment is complete.\nCan schools order books in bulk? | Send us a message with the titles and quantities you need and we will get back to you with options.", 'lines' ),
			),
		),

		/* ------------------------------------------------------------------ */
		'alph_p_swedish'    => array(
			'title'  => __( 'Swedish School page', 'alphonsus' ),
			'panel'  => 'alph_pages_panel',
			'fields' => array(
				'sw_badge'     => array( __( 'Hero badge (e.g. accreditation) — empty hides', 'alphonsus' ), '', 'text' ),
				'sw_eyebrow'   => array( __( 'Hero eyebrow', 'alphonsus' ), 'CEFR A1 • A2 • B1 • Relocation prep', 'text' ),
				'sw_title'     => array( __( 'Hero title', 'alphonsus' ), 'Master Svenska with Bilingual Nordic & African Pedagogy', 'text' ),
				'sw_text'      => array( __( 'Hero text', 'alphonsus' ), 'Learn Swedish with warm, structured mentorship and books written for African and international learners — from your first greetings to confident everyday conversation and relocation readiness.', 'textarea' ),
				'sw_stats'     => array( __( 'Hero stats (value | label) — empty hides', 'alphonsus' ), '', 'lines' ),
				'sw_image'     => array( __( 'Hero image', 'alphonsus' ), 3263, 'image' ),
				'sw_quote'     => array( __( 'Image quote — empty hides', 'alphonsus' ), '', 'text' ),
				'sw_features'  => array( __( 'Feature cards (title | text) — empty hides', 'alphonsus' ), '', 'lines' ),
				'sw_tracks_intro' => array( __( 'Course tracks intro', 'alphonsus' ), 'Progressive tracks structured around the Common European Framework of Reference for Languages (CEFR).', 'textarea' ),
				'sw_tracks'    => array( __( 'Course tracks (name | level | text | features separated by ; | duration | price | tag) — empty hides', 'alphonsus' ), '', 'lines' ),
				'sw_timetable' => array( __( 'Timetable (track | days | time | mode | seats) — empty hides', 'alphonsus' ), '', 'lines' ),
				'sw_faculty'   => array( __( 'Faculty (name | role | bio | image ID) — empty hides', 'alphonsus' ), '', 'lines' ),
				'sw_results'   => array( __( 'Results (label | percent) — empty hides', 'alphonsus' ), '', 'lines' ),
				'sw_testimonials' => array( __( 'Testimonials (quote | name | detail) — empty hides', 'alphonsus' ), '', 'lines' ),
				'sw_levels'    => array( __( 'Enrolment form: level / track options (one per line)', 'alphonsus' ), "A1 — Beginner\nA2 — Elementary\nB1 — Intermediate\nNot sure — please assess me", 'lines' ),
				'sw_faq'       => array( __( 'FAQ (question | answer) — empty hides', 'alphonsus' ), '', 'lines' ),
			),
		),

		/* ------------------------------------------------------------------ */
		'alph_p_wellness'   => array(
			'title'  => __( 'Wellness page', 'alphonsus' ),
			'panel'  => 'alph_pages_panel',
			'fields' => array(
				'wl_eyebrow'   => array( __( 'Hero eyebrow', 'alphonsus' ), 'Sanctuary of healing literature', 'text' ),
				'wl_title'     => array( __( 'Hero title', 'alphonsus' ), 'Nurturing Mind, Body, and Wholeness with Compassionate Health Literature.', 'text' ),
				'wl_text'      => array( __( 'Hero text', 'alphonsus' ), 'Gentle, practical guides on women’s and men’s health, recovery after childbirth and everyday wellbeing — written to inform without shame.', 'textarea' ),
				'wl_stats'     => array( __( 'Hero stats (value | label) — empty hides', 'alphonsus' ), '', 'lines' ),
				'wl_image'     => array( __( 'Hero image', 'alphonsus' ), 0, 'image' ),
				'wl_feature'   => array( __( 'Spotlight product ID (bundle)', 'alphonsus' ), 3339, 'number' ),
				'wl_ethos_title' => array( __( 'Ethos band title', 'alphonsus' ), 'Safe, Discreet, Judgment-Free Health Literacy.', 'text' ),
				'wl_ethos_text'  => array( __( 'Ethos band text', 'alphonsus' ), 'We believe taboo thrives in secrecy and dissolves in compassionate understanding. Our wellness library helps African and diaspora families talk openly about health with dignity.', 'textarea' ),
				'wl_ethos_points' => array( __( 'Ethos points (title | text) — empty hides', 'alphonsus' ), '', 'lines' ),
				'wl_quote'     => array( __( 'Curator quote — empty hides', 'alphonsus' ), '', 'textarea' ),
				'wl_printables' => array( __( 'Free printables (title | text | file URL) — empty hides', 'alphonsus' ), '', 'lines' ),
				'wl_testimonials' => array( __( 'Reader words (quote | name | detail) — empty hides', 'alphonsus' ), '', 'lines' ),
				'wl_trust'     => array( __( 'Trust bar (title | text) — empty hides', 'alphonsus' ), '', 'lines' ),
			),
		),

		/* ------------------------------------------------------------------ */
		'alph_p_merch'      => array(
			'title'  => __( 'Merch page', 'alphonsus' ),
			'panel'  => 'alph_pages_panel',
			'fields' => array(
				'mc_eyebrow'  => array( __( 'Hero eyebrow', 'alphonsus' ), 'Atelier & goods', 'text' ),
				'mc_title'    => array( __( 'Hero title', 'alphonsus' ), 'Wear Your Love for Literature & Nordic-African Heritage.', 'text' ),
				'mc_text'     => array( __( 'Hero text', 'alphonsus' ), 'Story-inspired umbrellas, socks and exercise books featuring the characters children love from our African story collection.', 'textarea' ),
				'mc_stats'    => array( __( 'Hero stats (value | label) — empty hides', 'alphonsus' ), '', 'lines' ),
				'mc_image'    => array( __( 'Hero image', 'alphonsus' ), 0, 'image' ),
				'mc_cat'      => array( __( 'Merch category slug', 'alphonsus' ), 'merch', 'text' ),
				'mc_feature'  => array( __( 'Spotlight bundle product ID — empty hides', 'alphonsus' ), 0, 'number' ),
				'mc_pledges'  => array( __( 'Pledge cards (title | text) — empty hides', 'alphonsus' ), '', 'lines' ),
				'mc_trust'    => array( __( 'Trust bar (title | text) — empty hides', 'alphonsus' ), '', 'lines' ),
			),
		),
	);
}
