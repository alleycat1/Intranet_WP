<?php
/**
 * Shortcode: Display WPML Language Selector (WPBakery support)
 *
 * @package ThemeREX Addons
 * @since v1.6.18
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}



// Add [trx_sc_layouts_language] in the VC shortcodes list
if (!function_exists('trx_addons_sc_layouts_language_add_in_vc')) {
	function trx_addons_sc_layouts_language_add_in_vc() {
		
		if (!trx_addons_cpt_layouts_sc_required()) return;

		if (!trx_addons_exists_vc()) return;

		vc_lean_map("trx_sc_layouts_language", 'trx_addons_sc_layouts_language_add_in_vc_params');
		class WPBakeryShortCode_Trx_Sc_Layouts_Language extends WPBakeryShortCode {}
	}

	add_action('init', 'trx_addons_sc_layouts_language_add_in_vc', 15);
}

// Return params
if (!function_exists('trx_addons_sc_layouts_language_add_in_vc_params')) {
	function trx_addons_sc_layouts_language_add_in_vc_params() {
		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_sc_layouts_language",
				"name" => esc_html__("Layouts: Language", 'trx_addons'),
				"description" => wp_kses_data( __("Insert WPML Language Selector", 'trx_addons') ),
				"category" => esc_html__('Layouts', 'trx_addons'),
				"icon" => 'icon_trx_sc_layouts_language',
				"class" => "trx_sc_layouts_language",
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
							), 'trx_sc_layouts_language')),
							"type" => "dropdown"
						),
						array(
							"param_name" => "flag",
							"heading" => esc_html__("Show flag", 'trx_addons'),
							"description" => wp_kses_data( __("Where do you want to show flag?", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
					        'save_always' => true,
							"std" => "both",
							"value" => array_flip(trx_addons_get_list_sc_layouts_language_positions()),
							"type" => "dropdown"
						),
						array(
							"param_name" => "title_link",
							"heading" => esc_html__("Show link's title", 'trx_addons'),
							"description" => wp_kses_data( __("Select link's title type", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"std" => "name",
							"value" => array_flip(trx_addons_get_list_sc_layouts_language_parts()),
							"type" => "dropdown"
						),
						array(
							"param_name" => "title_menu",
							"heading" => esc_html__("Show menu item's title", 'trx_addons'),
							"description" => wp_kses_data( __("Select menu item's title type", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"std" => "name",
							"value" => array_flip(trx_addons_get_list_sc_layouts_language_parts()),
							"type" => "dropdown"
						),
					),
					trx_addons_vc_add_hide_param(),
					trx_addons_vc_add_id_param()
				)
			), 'trx_sc_layouts_language');
	}
}
