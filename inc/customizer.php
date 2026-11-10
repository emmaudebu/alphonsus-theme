<?php
/**
 * Customizer: every homepage/site text, link and image is editable here.
 *
 * @package Alphonsus
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'customize_register',
	function ( WP_Customize_Manager $wp_customize ) {
		$wp_customize->add_panel(
			'alph_pages_panel',
			array(
				'title'       => __( 'Alphonsus Pages', 'alphonsus' ),
				'description' => __( 'Text, images and lists for the Book Shop, Foundation, About, Contact, Swedish School, Wellness and Merch pages. Sections whose list is empty stay hidden on the site.', 'alphonsus' ),
				'priority'    => 31,
			)
		);

		$wp_customize->add_panel(
			'alph_panel',
			array(
				'title'       => __( 'Alphonsus Homepage & Site', 'alphonsus' ),
				'description' => __( 'Edit the homepage sections, contact details and social links. Product sections pull live products from WooCommerce.', 'alphonsus' ),
				'priority'    => 30,
			)
		);

		$sanitizers = array(
			'text'     => 'sanitize_text_field',
			'textarea' => 'sanitize_textarea_field',
			'lines'    => 'sanitize_textarea_field',
			'url'      => 'esc_url_raw',
			'email'    => 'sanitize_email',
			'image'    => 'absint',
			'number'   => 'absint',
			'datetime' => 'sanitize_text_field',
		);

		foreach ( alph_settings_schema() as $section_id => $section ) {
			$wp_customize->add_section(
				$section_id,
				array(
					'title' => $section['title'],
					'panel' => $section['panel'] ?? 'alph_panel',
				)
			);

			foreach ( $section['fields'] as $key => $field ) {
				list( $label, $default, $type ) = $field;
				$setting_id                     = 'alph_' . $key;

				$wp_customize->add_setting(
					$setting_id,
					array(
						'default'           => $default,
						'sanitize_callback' => $sanitizers[ $type ],
					)
				);

				if ( 'image' === $type ) {
					$wp_customize->add_control(
						new WP_Customize_Media_Control(
							$wp_customize,
							$setting_id,
							array(
								'label'     => $label,
								'section'   => $section_id,
								'mime_type' => 'image',
							)
						)
					);
					continue;
				}

				$wp_customize->add_control(
					$setting_id,
					array(
						'label'   => $label,
						'section' => $section_id,
						'type'    => in_array( $type, array( 'textarea', 'lines' ), true ) ? 'textarea' : ( 'number' === $type ? 'number' : ( 'email' === $type ? 'email' : 'text' ) ),
					)
				);
			}
		}
	}
);
