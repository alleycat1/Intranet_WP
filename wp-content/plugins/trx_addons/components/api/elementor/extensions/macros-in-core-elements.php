<?php
/**
 * Elementor extension: Prepare macroses in the core elements
 *
 * @package ThemeREX Addons
 * @since v2.22.2
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

// Prepare macroses in the core elements in the Elementor
if ( ! function_exists( 'trx_addons_macros_before_render_heading_in_elementor' ) ) {
	// Before Elementor 2.1.0
	add_action( 'elementor/frontend/element/before_render', 'trx_addons_macros_before_render_heading_in_elementor', 10, 1 );
	// After Elementor 2.1.0
	add_action( 'elementor/frontend/widget/before_render', 'trx_addons_macros_before_render_heading_in_elementor', 10, 1 );
	// For AJAX requests to render widget after its parameters are changed
	if ( wp_doing_ajax() ) {
		add_action( 'elementor/widget/before_render_content', 'trx_addons_macros_before_render_heading_in_elementor', 10, 1 );
	}
	function trx_addons_macros_before_render_heading_in_elementor( $element ) {
		if ( is_object( $element ) ) {
			$sc_list = apply_filters( 'trx_addons_filter_prepare_macros_in_core_elements', array(
				'accordion' => array( 'tabs[tab_title]', 'tabs[tab_content]' ),
				'button' => array( 'text' ),
				'heading' => array( 'title' ),
				'icon-box' => array( 'title_text', 'description_text' ),
				'icon-list' => array( 'icon_list[text]' ),
				'image' => array( 'caption' ),
				'image-box' => array( 'title_text', 'description_text' ),
				'progress' => array( 'title', 'inner_text' ),
				'read-more' => array( 'link_text' ),
				//'star-rating' => array( 'title' ),	// This shortcode isn't support tags in the title
				'tabs' => array( 'tabs[tab_title]', 'tabs[tab_content]' ),
				'testimonial' => array( 'testimonial_content', 'testimonial_name', 'testimonial_job' ),
				//'text-editor' => array( 'editor' ),	// The editor's content already support tags and shortcodes
				'toggle' => array( 'tabs[tab_title]', 'tabs[tab_content]' ),
			) );
			$el_name = $element->get_name();
			if ( ! empty( $sc_list[ $el_name ] ) ) {
				foreach( $sc_list[ $el_name ] as $param ) {
					$changed = false;
					if ( strpos( $param, '[' ) ) {
						$parts = explode( '[', $param );
						$param = $parts[0];
						$field = trim( str_replace( ']', '', $parts[1] ) );
						$value = $element->get_settings( $param );
						if ( is_array( $value ) ) {
							foreach( $value as $k => $v ) {
								if ( is_array( $v ) && ! empty( $v[ $field ] ) ) {
									$value[ $k ][ $field ] = trx_addons_prepare_macros( $v[ $field ] );
									$changed = true;
								}
							}
						}
					} else {
						$value = $element->get_settings( $param );
						if ( is_string( $value ) ) {
							$value = trx_addons_prepare_macros( $value );
							$changed = true;
						}
					}
					if ( $changed ) {
						$element->set_settings( $param, $value );
					}
				}
			}
		}
	}
}
