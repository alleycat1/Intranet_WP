<?php
/**
 * Shortcode: Display WooCommerce Currency Switcher with items number and totals (WPBakery support)
 *
 * @package ThemeREX Addons
 * @since v1.6.14
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


// Add [trx_sc_layouts_currency] in the VC shortcodes list
if (!function_exists('trx_addons_sc_layouts_currency_add_in_vc')) {
	function trx_addons_sc_layouts_currency_add_in_vc() {
		
		if (!trx_addons_cpt_layouts_sc_required()) return;

		if (!trx_addons_exists_vc()) return;

		vc_lean_map("trx_sc_layouts_currency", 'trx_addons_sc_layouts_currency_add_in_vc_params');
		class WPBakeryShortCode_Trx_Sc_Layouts_Currency extends WPBakeryShortCode {}
	}

	add_action('init', 'trx_addons_sc_layouts_currency_add_in_vc', 15);
}

// Return params
if (!function_exists('trx_addons_sc_layouts_currency_add_in_vc_params')) {
	function trx_addons_sc_layouts_currency_add_in_vc_params() {
		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_sc_layouts_currency",
				"name" => esc_html__("Layouts: Currency", 'trx_addons'),
				"description" => wp_kses_data( __("Insert Currency Switcher", 'trx_addons') ),
				"category" => esc_html__('Layouts', 'trx_addons'),
				"icon" => 'icon_trx_sc_layouts_currency',
				"class" => "trx_sc_layouts_currency",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array_merge(
					array(
						array(
							"param_name" => "type",
							"heading" => esc_html__("Layout", 'trx_addons'),
							"description" => wp_kses_data( __("Select shortcodes's layout", 'trx_addons') ),
							"std" => "default",
							"value" => array_flip(apply_filters('trx_addons_sc_type', array(
								'default' => esc_html__('Default', 'trx_addons'),
							), 'trx_sc_layouts_currency')),
							"type" => "dropdown"
						)
					),
					trx_addons_vc_add_hide_param(),
					trx_addons_vc_add_id_param()
				)
			), 'trx_sc_layouts_currency');
	}
}
