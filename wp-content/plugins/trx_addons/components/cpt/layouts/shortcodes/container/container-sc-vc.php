<?php
/**
 * Shortcode: Container for other shortcodes (WPBakery support)
 *
 * @package ThemeREX Addons
 * @since v1.6.28
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


// Add [trx_sc_layouts_container] in the VC shortcodes list
if (!function_exists('trx_addons_sc_layouts_container_add_in_vc')) {
	function trx_addons_sc_layouts_container_add_in_vc() {
		
		if (!trx_addons_cpt_layouts_sc_required()) return;

		if (!trx_addons_exists_vc()) return;

		vc_lean_map("trx_sc_layouts_container", 'trx_addons_sc_layouts_container_add_in_vc_params');
		class WPBakeryShortCode_Trx_Sc_Layouts_Container extends WPBakeryShortCodesContainer {}

	}
	add_action('init', 'trx_addons_sc_layouts_container_add_in_vc', 15);
}

// Return params
if (!function_exists('trx_addons_sc_layouts_container_add_in_vc_params')) {
	function trx_addons_sc_layouts_container_add_in_vc_params() {
		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_sc_layouts_container",
				"name" => esc_html__("Layouts: Container", 'trx_addons'),
				"description" => wp_kses_data( __("Container for other blocks in layouts", 'trx_addons') ),
				"category" => esc_html__('Layouts', 'trx_addons'),
				"icon" => 'icon_trx_sc_layouts_container',
				"class" => "trx_sc_layouts_container",
				"content_element" => true,
				'is_container' => true,
				'as_child' => array('except' => 'trx_sc_layouts_container'),
				"js_view" => 'VcTrxAddonsContainerView',	//'VcColumnView',
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
							), 'trx_sc_layouts_container')),
							"type" => "dropdown"
						),
						array(
							"param_name" => "align",
							"heading" => esc_html__("Content alignment", 'trx_addons'),
							"description" => wp_kses_data( __("Select alignment of the inner content in this block", 'trx_addons') ),
							"admin_label" => true,
							"value" => array(
								esc_html__('Inherit', 'trx_addons') => 'inherit',
								esc_html__('Left', 'trx_addons') => 'left',
								esc_html__('Center', 'trx_addons') => 'center',
								esc_html__('Right', 'trx_addons') => 'right'
							),
							"std" => "inherit",
							"type" => "dropdown"
						)
					),
					trx_addons_vc_add_hide_param(false, true),
					trx_addons_vc_add_id_param()
				)
			), 'trx_sc_layouts_container');
	}
}
