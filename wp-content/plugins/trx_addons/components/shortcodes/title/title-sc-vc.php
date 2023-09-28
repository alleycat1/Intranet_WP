<?php
/**
 * Shortcode: Content container (WPBakery support)
 *
 * @package ThemeREX Addons
 * @since v1.4.3
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}



// Add [trx_sc_content] in the VC shortcodes list
if (!function_exists('trx_addons_sc_title_add_in_vc')) {
	function trx_addons_sc_title_add_in_vc() {
		
		if (!trx_addons_exists_vc()) return;
		
		vc_lean_map("trx_sc_title", 'trx_addons_sc_title_add_in_vc_params');
		class WPBakeryShortCode_Trx_Sc_Title extends WPBakeryShortCode {}
	}
	add_action('init', 'trx_addons_sc_title_add_in_vc', 20);
}

// Return params
if (!function_exists('trx_addons_sc_title_add_in_vc_params')) {
	function trx_addons_sc_title_add_in_vc_params() {
		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_sc_title",
				"name" => esc_html__("Title", 'trx_addons'),
				"description" => wp_kses_data( __("Add title, subtitle and description", 'trx_addons') ),
				"category" => esc_html__('ThemeREX', 'trx_addons'),
				"icon" => 'icon_trx_sc_title',
				"class" => "trx_sc_title",
				'content_element' => true,
				'is_container' => false,
				"show_settings_on_create" => true,
				"params" => array_merge(
					trx_addons_vc_add_title_param(''),
					trx_addons_vc_add_id_param()
				)
			), 'trx_sc_title' );
	}
}
