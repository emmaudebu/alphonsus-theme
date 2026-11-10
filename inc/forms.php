<?php
/**
 * Pre-order, contact and newsletter forms.
 *
 * Every submission is saved as a private "Form entry" (so nothing is lost if
 * email fails) and emailed to the notification address.
 *
 * @package Alphonsus
 */

defined( 'ABSPATH' ) || exit;

const ALPH_FORMS = array( 'preorder', 'contact', 'newsletter', 'enrol', 'volunteer' );

add_action(
	'init',
	function () {
		register_post_type(
			'alph_entry',
			array(
				'labels'          => array(
					'name'          => __( 'Form entries', 'alphonsus' ),
					'singular_name' => __( 'Form entry', 'alphonsus' ),
					'edit_item'     => __( 'View entry', 'alphonsus' ),
					'search_items'  => __( 'Search entries', 'alphonsus' ),
					'not_found'     => __( 'No entries yet.', 'alphonsus' ),
				),
				'public'          => false,
				'show_ui'         => true,
				'show_in_menu'    => true,
				'menu_icon'       => 'dashicons-feedback',
				'menu_position'   => 26,
				'supports'        => array( 'title' ),
				'capability_type' => 'post',
				'capabilities'    => array( 'create_posts' => 'do_not_allow' ),
				'map_meta_cap'    => true,
			)
		);
	}
);

/**
 * Field definitions per form: key => [label, required, type, options].
 *
 * Types: text, email, textarea, choice (one of options), list (several of options).
 * Options are the allowed values, so posted values outside them are discarded.
 */
function alph_form_fields( $form ) {
	switch ( $form ) {
		case 'preorder':
			return array(
				'first_name' => array( __( 'First name', 'alphonsus' ), true, 'text' ),
				'last_name'  => array( __( 'Last name', 'alphonsus' ), true, 'text' ),
				'email'      => array( __( 'Email', 'alphonsus' ), true, 'email' ),
				'phone'      => array( __( 'Phone', 'alphonsus' ), true, 'text' ),
				'books'      => array( __( 'Books', 'alphonsus' ), true, 'list', alph_lines( 'presale_books' ) ),
			);
		case 'contact':
			return array(
				'first_name' => array( __( 'First name', 'alphonsus' ), true, 'text' ),
				'last_name'  => array( __( 'Last name', 'alphonsus' ), false, 'text' ),
				'email'      => array( __( 'Email', 'alphonsus' ), true, 'email' ),
				'phone'      => array( __( 'Phone / WhatsApp', 'alphonsus' ), false, 'text' ),
				'subject'    => array( __( 'Subject', 'alphonsus' ), true, 'choice', alph_lines( 'ct_subjects' ) ),
				'channel'    => array( __( 'Preferred reply', 'alphonsus' ), false, 'choice', array( 'Email', 'WhatsApp' ) ),
				'message'    => array( __( 'Message', 'alphonsus' ), true, 'textarea' ),
			);
		case 'newsletter':
			return array(
				'email' => array( __( 'Email', 'alphonsus' ), true, 'email' ),
			);
		case 'enrol':
			return array(
				'first_name' => array( __( 'First name', 'alphonsus' ), true, 'text' ),
				'last_name'  => array( __( 'Last name', 'alphonsus' ), true, 'text' ),
				'email'      => array( __( 'Email', 'alphonsus' ), true, 'email' ),
				'phone'      => array( __( 'Phone / WhatsApp', 'alphonsus' ), true, 'text' ),
				'track'      => array( __( 'Level / track', 'alphonsus' ), true, 'choice', alph_enrol_options() ),
				'schedule'   => array( __( 'Preferred schedule', 'alphonsus' ), false, 'choice', alph_schedule_options() ),
				'message'    => array( __( 'Goals or questions', 'alphonsus' ), false, 'textarea' ),
			);
		case 'volunteer':
			return array(
				'name'     => array( __( 'Full name', 'alphonsus' ), true, 'text' ),
				'email'    => array( __( 'Email', 'alphonsus' ), true, 'email' ),
				'type'     => array( __( 'How you’d like to help', 'alphonsus' ), true, 'choice', alph_volunteer_options() ),
				'location' => array( __( 'Location', 'alphonsus' ), false, 'text' ),
				'message'  => array( __( 'Details', 'alphonsus' ), true, 'textarea' ),
			);
	}
	return array();
}

function alph_enrol_options() {
	$tracks = array();
	foreach ( alph_lines( 'sw_tracks', true ) as $t ) {
		$tracks[] = $t[0];
	}
	return $tracks ? array_merge( $tracks, array( __( 'Not sure — please assess me', 'alphonsus' ) ) ) : alph_lines( 'sw_levels' );
}

function alph_schedule_options() {
	return array( __( 'Weekday evenings', 'alphonsus' ), __( 'Weekends', 'alphonsus' ), __( 'Flexible', 'alphonsus' ) );
}

function alph_volunteer_options() {
	return array( __( 'Donate physical books', 'alphonsus' ), __( 'Volunteer as a reading mentor', 'alphonsus' ), __( 'Partner as a school or organisation', 'alphonsus' ), __( 'Something else', 'alphonsus' ) );
}

/**
 * Human label for a form key.
 */
function alph_form_label( $form ) {
	$labels = array(
		'preorder'   => __( 'Pre-order', 'alphonsus' ),
		'contact'    => __( 'Contact', 'alphonsus' ),
		'newsletter' => __( 'Newsletter', 'alphonsus' ),
		'enrol'      => __( 'Swedish School enrolment', 'alphonsus' ),
		'volunteer'  => __( 'Foundation volunteer', 'alphonsus' ),
	);
	return $labels[ $form ] ?? ucfirst( $form );
}

/**
 * Hidden fields every theme form needs.
 */
function alph_form_hidden( $form ) {
	echo '<input type="hidden" name="action" value="alph_form">';
	echo '<input type="hidden" name="alph_form" value="' . esc_attr( $form ) . '">';
	wp_nonce_field( 'alph_form_' . $form, '_alph_nonce' );
	// Honeypot: humans never see or fill this.
	echo '<p class="hp" aria-hidden="true"><label>Website <input type="text" name="website" tabindex="-1" autocomplete="off"></label></p>';
}

add_action( 'admin_post_nopriv_alph_form', 'alph_handle_form' );
add_action( 'admin_post_alph_form', 'alph_handle_form' );

function alph_handle_form() {
	$form     = isset( $_POST['alph_form'] ) ? sanitize_key( wp_unslash( $_POST['alph_form'] ) ) : '';
	$referer  = wp_get_referer() ? wp_get_referer() : home_url( '/' );
	$referer  = remove_query_arg( array( 'alph_sent', 'alph_error' ), $referer );
	$anchors  = array(
		'preorder'   => '#presale',
		'contact'    => '#contact-form',
		'newsletter' => '#newsletter',
		'enrol'      => '#enrol',
		'volunteer'  => '#volunteer',
	);
	$fail     = function () use ( $form, $referer, $anchors ) {
		wp_safe_redirect( add_query_arg( 'alph_error', $form, $referer ) . ( $anchors[ $form ] ?? '' ) );
		exit;
	};

	if ( ! in_array( $form, ALPH_FORMS, true ) ) {
		wp_safe_redirect( home_url( '/' ) );
		exit;
	}
	if ( ! isset( $_POST['_alph_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_alph_nonce'] ) ), 'alph_form_' . $form ) ) {
		$fail();
	}

	// Bots fill the honeypot; pretend success so they move on.
	$is_bot = ! empty( $_POST['website'] );

	// Simple per-IP throttle: 6 submissions per 10 minutes.
	$ip_key = 'alph_rate_' . md5( isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '' );
	$count  = (int) get_transient( $ip_key );
	if ( $count >= 6 ) {
		$fail();
	}
	set_transient( $ip_key, $count + 1, 10 * MINUTE_IN_SECONDS );

	$data = array();
	foreach ( alph_form_fields( $form ) as $key => $field ) {
		$required = $field[1];
		$type     = $field[2];
		$allowed  = $field[3] ?? array();
		$raw      = $_POST[ $key ] ?? ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput -- sanitized per type below.

		if ( 'list' === $type ) {
			$value = array_values( array_intersect( array_map( 'sanitize_text_field', (array) wp_unslash( $raw ) ), $allowed ) );
			$empty = empty( $value );
		} elseif ( 'choice' === $type ) {
			$value = sanitize_text_field( wp_unslash( is_array( $raw ) ? '' : $raw ) );
			$value = in_array( $value, $allowed, true ) ? $value : '';
			$empty = '' === $value;
		} elseif ( 'email' === $type ) {
			$value = sanitize_email( wp_unslash( $raw ) );
			$empty = ! is_email( $value );
		} elseif ( 'textarea' === $type ) {
			$value = sanitize_textarea_field( wp_unslash( $raw ) );
			$empty = '' === trim( $value );
		} else {
			$value = sanitize_text_field( wp_unslash( $raw ) );
			$empty = '' === trim( $value );
		}

		if ( $required && $empty ) {
			$fail();
		}
		$data[ $key ] = $value;
	}

	if ( ! $is_bot ) {
		alph_save_entry( $form, $data );
	}

	wp_safe_redirect( add_query_arg( 'alph_sent', $form, $referer ) . ( $anchors[ $form ] ?? '' ) );
	exit;
}

/**
 * Store an entry and email it.
 */
function alph_save_entry( $form, array $data, $date = null ) {
	if ( 'newsletter' === $form && alph_newsletter_exists( $data['email'] ) ) {
		return 0;
	}

	$name  = trim( ( $data['first_name'] ?? '' ) . ' ' . ( $data['last_name'] ?? '' ) );
	$name  = $name ? $name : ( $data['name'] ?? $data['email'] ?? '' );
	$label = alph_form_label( $form );

	$entry = array(
		'post_type'   => 'alph_entry',
		'post_status' => 'private',
		'post_title'  => $label . ' — ' . $name,
	);
	if ( $date ) {
		$entry['post_date'] = $date;
	}
	$id = wp_insert_post( $entry );
	if ( ! $id || is_wp_error( $id ) ) {
		return 0;
	}
	update_post_meta( $id, '_alph_form', $form );
	update_post_meta( $id, '_alph_email', $data['email'] ?? '' );
	update_post_meta( $id, '_alph_data', $data );

	if ( ! $date ) {
		$fields = alph_form_fields( $form );
		$lines  = array();
		foreach ( $data as $key => $value ) {
			$lines[] = ( $fields[ $key ][0] ?? $key ) . ': ' . ( is_array( $value ) ? implode( ', ', $value ) : $value );
		}
		$to      = alph_mod( 'notify_email' ) ? alph_mod( 'notify_email' ) : get_option( 'admin_email' );
		$headers = ! empty( $data['email'] ) && 'newsletter' !== $form ? array( 'Reply-To: ' . $name . ' <' . $data['email'] . '>' ) : array();
		/* translators: 1: form name, 2: site name. */
		wp_mail( $to, sprintf( __( '[%2$s] New %1$s submission', 'alphonsus' ), $label, get_bloginfo( 'name' ) ), implode( "\n", $lines ) . "\n\n" . admin_url( 'post.php?post=' . $id . '&action=edit' ), $headers );
	}

	return $id;
}

function alph_newsletter_exists( $email ) {
	$found = get_posts(
		array(
			'post_type'   => 'alph_entry',
			'post_status' => 'private',
			'numberposts' => 1,
			'fields'      => 'ids',
			'meta_query'  => array( // phpcs:ignore WordPress.DB.SlowDBQuery
				array(
					'key'   => '_alph_form',
					'value' => 'newsletter',
				),
				array(
					'key'   => '_alph_email',
					'value' => $email,
				),
			),
		)
	);
	return ! empty( $found );
}

/*
 * ---------------------------------------------------------------------------
 * Admin: list columns, filter by form, entry viewer and CSV export.
 * ---------------------------------------------------------------------------
 */

add_filter(
	'manage_alph_entry_posts_columns',
	function () {
		return array(
			'cb'         => '<input type="checkbox">',
			'title'      => __( 'Entry', 'alphonsus' ),
			'alph_form'  => __( 'Form', 'alphonsus' ),
			'alph_email' => __( 'Email', 'alphonsus' ),
			'alph_items' => __( 'Details', 'alphonsus' ),
			'date'       => __( 'Received', 'alphonsus' ),
		);
	}
);

add_action(
	'manage_alph_entry_posts_custom_column',
	function ( $column, $post_id ) {
		$data = (array) get_post_meta( $post_id, '_alph_data', true );
		switch ( $column ) {
			case 'alph_form':
				echo esc_html( alph_form_label( (string) get_post_meta( $post_id, '_alph_form', true ) ) );
				break;
			case 'alph_email':
				$email = (string) get_post_meta( $post_id, '_alph_email', true );
				echo $email ? '<a href="mailto:' . esc_attr( $email ) . '">' . esc_html( $email ) . '</a>' : '—';
				break;
			case 'alph_items':
				if ( ! empty( $data['books'] ) ) {
					echo esc_html( implode( ', ', (array) $data['books'] ) );
				} elseif ( ! empty( $data['track'] ) ) {
					echo esc_html( $data['track'] . ( ! empty( $data['schedule'] ) ? ' · ' . $data['schedule'] : '' ) );
				} elseif ( ! empty( $data['type'] ) ) {
					echo esc_html( $data['type'] );
				} elseif ( ! empty( $data['message'] ) ) {
					echo esc_html( wp_trim_words( $data['message'], 14 ) );
				} elseif ( ! empty( $data['phone'] ) ) {
					echo esc_html( $data['phone'] );
				}
				break;
		}
	},
	10,
	2
);

add_action(
	'restrict_manage_posts',
	function ( $post_type ) {
		if ( 'alph_entry' !== $post_type ) {
			return;
		}
		$current = isset( $_GET['alph_form'] ) ? sanitize_key( $_GET['alph_form'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
		echo '<select name="alph_form"><option value="">' . esc_html__( 'All forms', 'alphonsus' ) . '</option>';
		foreach ( ALPH_FORMS as $form ) {
			printf( '<option value="%1$s" %2$s>%3$s</option>', esc_attr( $form ), selected( $current, $form, false ), esc_html( alph_form_label( $form ) ) );
		}
		echo '</select>';
		$export = wp_nonce_url( add_query_arg( array( 'action' => 'alph_export', 'alph_form' => $current ), admin_url( 'admin-post.php' ) ), 'alph_export' );
		echo ' <a class="button" href="' . esc_url( $export ) . '">' . esc_html__( 'Export CSV', 'alphonsus' ) . '</a>';
	}
);

add_action(
	'pre_get_posts',
	function ( WP_Query $q ) {
		if ( ! is_admin() || ! $q->is_main_query() || 'alph_entry' !== $q->get( 'post_type' ) || empty( $_GET['alph_form'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			return;
		}
		$q->set( 'meta_key', '_alph_form' );
		$q->set( 'meta_value', sanitize_key( $_GET['alph_form'] ) ); // phpcs:ignore WordPress.Security.NonceVerification
	}
);

add_action(
	'admin_post_alph_export',
	function () {
		check_admin_referer( 'alph_export' );
		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_die( esc_html__( 'Sorry, you are not allowed to export entries.', 'alphonsus' ) );
		}
		$form = isset( $_GET['alph_form'] ) ? sanitize_key( $_GET['alph_form'] ) : '';
		$args = array(
			'post_type'   => 'alph_entry',
			'post_status' => 'private',
			'numberposts' => -1,
		);
		if ( $form ) {
			$args['meta_key']   = '_alph_form'; // phpcs:ignore WordPress.DB.SlowDBQuery
			$args['meta_value'] = $form; // phpcs:ignore WordPress.DB.SlowDBQuery
		}

		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=alphonsus-' . ( $form ? $form : 'entries' ) . '-' . gmdate( 'Y-m-d' ) . '.csv' );
		$out = fopen( 'php://output', 'w' ); // phpcs:ignore WordPress.WP.AlternativeFunctions
		fputcsv( $out, array( 'date', 'form', 'name', 'email', 'phone', 'books', 'subject', 'track', 'schedule', 'type', 'message' ) );
		foreach ( get_posts( $args ) as $entry ) {
			$d    = (array) get_post_meta( $entry->ID, '_alph_data', true );
			$name = trim( ( $d['first_name'] ?? '' ) . ' ' . ( $d['last_name'] ?? '' ) );
			// Prefix values that spreadsheets would treat as formulas.
			$row = array_map(
				function ( $v ) {
					$v = is_array( $v ) ? implode( '; ', $v ) : (string) $v;
					return preg_match( '/^[=+\-@]/', $v ) ? "'" . $v : $v;
				},
				array( $entry->post_date, get_post_meta( $entry->ID, '_alph_form', true ), $name ? $name : ( $d['name'] ?? '' ), $d['email'] ?? '', $d['phone'] ?? '', $d['books'] ?? '', $d['subject'] ?? '', $d['track'] ?? '', $d['schedule'] ?? '', $d['type'] ?? '', $d['message'] ?? '' )
			);
			fputcsv( $out, $row );
		}
		fclose( $out ); // phpcs:ignore WordPress.WP.AlternativeFunctions
		exit;
	}
);

/**
 * Read-only view of the submitted data on the entry edit screen.
 */
add_action(
	'add_meta_boxes_alph_entry',
	function () {
		add_meta_box(
			'alph_entry_data',
			__( 'Submitted details', 'alphonsus' ),
			function ( $post ) {
				$form   = get_post_meta( $post->ID, '_alph_form', true );
				$fields = alph_form_fields( $form );
				echo '<table class="widefat striped"><tbody>';
				foreach ( (array) get_post_meta( $post->ID, '_alph_data', true ) as $key => $value ) {
					printf(
						'<tr><th style="width:160px">%s</th><td>%s</td></tr>',
						esc_html( $fields[ $key ][0] ?? $key ),
						nl2br( esc_html( is_array( $value ) ? implode( ', ', $value ) : $value ) )
					);
				}
				echo '</tbody></table>';
			},
			'alph_entry',
			'normal',
			'high'
		);
	}
);

/*
 * ---------------------------------------------------------------------------
 * Shortcodes so the forms can be placed on any page in the block editor.
 * ---------------------------------------------------------------------------
 */

add_shortcode(
	'alphonsus_contact_form',
	function () {
		ob_start();
		get_template_part( 'template-parts/form', 'contact' );
		return ob_get_clean();
	}
);

add_shortcode(
	'alphonsus_preorder_form',
	function () {
		ob_start();
		get_template_part( 'template-parts/form', 'preorder' );
		return ob_get_clean();
	}
);

/**
 * Render one form field from the form's schema.
 *
 * @param string $form Form key.
 * @param string $key  Field key.
 * @param array  $opts placeholder, autocomplete, input (HTML input type), radios (bool), rows.
 */
function alph_field( $form, $key, array $opts = array() ) {
	$fields = alph_form_fields( $form );
	if ( ! isset( $fields[ $key ] ) ) {
		return;
	}
	$label    = $fields[ $key ][0];
	$required = $fields[ $key ][1];
	$type     = $fields[ $key ][2];
	$options  = $fields[ $key ][3] ?? array();
	$id       = 'f-' . $form . '-' . $key;
	$req      = $required ? ' required' : '';
	$star     = $required ? ' <span class="req">*</span>' : '';
	$ph       = isset( $opts['placeholder'] ) ? ' placeholder="' . esc_attr( $opts['placeholder'] ) . '"' : '';
	$ac       = isset( $opts['autocomplete'] ) ? ' autocomplete="' . esc_attr( $opts['autocomplete'] ) . '"' : '';

	if ( 'choice' === $type && ! empty( $opts['radios'] ) ) {
		echo '<fieldset class="field"><legend>' . esc_html( $label ) . $star . '</legend><div class="radios">'; // phpcs:ignore WordPress.Security.EscapeOutput
		foreach ( $options as $i => $option ) {
			printf(
				'<label class="radio"><input type="radio" name="%1$s" value="%2$s"%3$s%4$s> <span>%5$s</span></label>',
				esc_attr( $key ),
				esc_attr( $option ),
				0 === $i ? ' checked' : '',
				$req,
				esc_html( $option )
			);
		}
		echo '</div></fieldset>';
		return;
	}

	echo '<p class="field"><label for="' . esc_attr( $id ) . '">' . esc_html( $label ) . $star . '</label>'; // phpcs:ignore WordPress.Security.EscapeOutput
	if ( 'choice' === $type ) {
		echo '<span class="select"><select id="' . esc_attr( $id ) . '" name="' . esc_attr( $key ) . '"' . $req . '>'; // phpcs:ignore WordPress.Security.EscapeOutput
		echo '<option value="">' . esc_html( $opts['placeholder'] ?? __( 'Please select…', 'alphonsus' ) ) . '</option>';
		foreach ( $options as $option ) {
			echo '<option value="' . esc_attr( $option ) . '">' . esc_html( $option ) . '</option>';
		}
		echo '</select>' . alph_icon( 'chevron', 16 ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput
	} elseif ( 'textarea' === $type ) {
		echo '<textarea id="' . esc_attr( $id ) . '" name="' . esc_attr( $key ) . '" rows="' . (int) ( $opts['rows'] ?? 5 ) . '"' . $ph . $req . '></textarea>'; // phpcs:ignore WordPress.Security.EscapeOutput
	} else {
		$input = $opts['input'] ?? ( 'email' === $type ? 'email' : 'text' );
		echo '<input id="' . esc_attr( $id ) . '" name="' . esc_attr( $key ) . '" type="' . esc_attr( $input ) . '"' . $ph . $ac . $req . '>'; // phpcs:ignore WordPress.Security.EscapeOutput
	}
	echo '</p>';
}
