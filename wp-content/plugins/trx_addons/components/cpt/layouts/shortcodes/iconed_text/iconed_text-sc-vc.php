<?php
/**
 * Shortcode: Display icons with two text lines (WPBakery support)
 *
 * @package ThemeREX Addons
 * @since v1.6.08
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}



// Add [trx_sc_layouts_iconed_text] in the VC shortcodes list
if (!function_exists('trx_addons_sc_layouts_iconed_text_add_in_vc')) {
	function trx_addons_sc_layouts_iconed_text_add_in_vc() {
		
		if (!trx_addons_cpt_layouts_sc_required()) return;

		if (!trx_addons_exists_vc()) return;

		vc_lean_map("trx_sc_layouts_iconed_text", 'trx_addons_sc_layouts_iconed_text_add_in_vc_params');
		class WPBakeryShortCode_Trx_Sc_Layouts_Iconed_Text extends WPBakeryShortCode {}
	}
	add_action('init', 'trx_addons_sc_layouts_iconed_text_add_in_vc', 15);
}

// Return params
if (!function_exists('trx_addons_sc_layouts_iconed_text_add_in_vc_params')) {
	function trx_addons_sc_layouts_iconed_text_add_in_vc_params() {
		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_sc_layouts_iconed_text",
				"name" => esc_html__("Layouts: Iconed text", 'trx_addons'),
				"description" => wp_kses_data( __("Insert icon with two text lines to the custom layout", 'trx_addons') ),
				"category" => esc_html__('Layouts', 'trx_addons'),
				"icon" => 'icon_trx_sc_layouts_iconed_text',
				"class" => "trx_sc_layouts_iconed_text",
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
							), 'trx_sc_layouts_iconed_text')),
							"type" => "dropdown"
						),
					),
					trx_addons_vc_add_icon_param(''),
					array(
						array(
							"param_name" => "text1",
							"heading" => esc_html__("Text line 1", 'trx_addons'),
							"description" => wp_kses_data( __("Text in the first line.", 'trx_addons') ),
							"admin_label" => true,
							'edit_field_class' => 'vc_col-sm-6',
							"type" => "textfield"
						),
						array(
							"param_name" => "text2",
							"heading" => esc_html__("Text line 2", 'trx_addons'),
							"description" => wp_kses_data( __("Text in the second line.", 'trx_addons') ),
							"admin_label" => true,
							'edit_field_class' => 'vc_col-sm-6',
							"type" => "textfield"
						),
						array(
							"param_name" => "link",
							"heading" => esc_html__("Link URL", 'trx_addons'),
							"description" => wp_kses_data( __("Specify link URL. If empty - show plain text without link", 'trx_addons') ),
							"type" => "textfield"
						)
					),
					trx_addons_vc_add_hide_param(),
					trx_addons_vc_add_id_param()
				)
			), 'trx_sc_layouts_iconed_text');
	}
}
