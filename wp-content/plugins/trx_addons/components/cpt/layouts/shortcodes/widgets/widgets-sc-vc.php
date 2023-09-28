<?php
/**
 * Shortcode: Display selected widgets area (Gutenberg support)
 *
 * @package ThemeREX Addons
 * @since v1.6.19
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


// Add [trx_sc_layouts_widgets] in the VC shortcodes list
if (!function_exists('trx_addons_sc_layouts_widgets_add_in_vc')) {
	function trx_addons_sc_layouts_widgets_add_in_vc() {
		
		if (!trx_addons_cpt_layouts_sc_required()) return;
		
		if (!trx_addons_exists_vc()) return;

		vc_lean_map("trx_sc_layouts_widgets", 'trx_addons_sc_layouts_widgets_add_in_vc_params');
		class WPBakeryShortCode_Trx_Sc_Layouts_Widgets extends WPBakeryShortCode {}
	}
	add_action('init', 'trx_addons_sc_layouts_widgets_add_in_vc', 15);
}

// Return params
if (!function_exists('trx_addons_sc_layouts_widgets_add_in_vc_params')) {
	function trx_addons_sc_layouts_widgets_add_in_vc_params() {
		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_sc_layouts_widgets",
				"name" => esc_html__("Layouts: Widgets", 'trx_addons'),
				"description" => wp_kses_data( __("Insert selected widgets area", 'trx_addons') ),
				"category" => esc_html__('Layouts', 'trx_addons'),
				"icon" => 'icon_trx_sc_layouts_widgets',
				"class" => "trx_sc_layouts_widgets",
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
								'default' => esc_html__('Default', 'trx_addons')
							), 'trx_sc_layouts_widgets')),
							"type" => "dropdown"
						),
						array(
							"param_name" => "widgets",
							"heading" => esc_html__("Widgets", 'trx_addons'),
							"description" => wp_kses_data( __("Select previously filled widgets area", 'trx_addons') ),
							"admin_label" => true,
							'edit_field_class' => 'vc_col-sm-6',
							"type" => "widgetised_sidebars"
						),
						array(
							"param_name" => "columns",
							"heading" => esc_html__("Columns", 'trx_addons'),
							"description" => wp_kses_data( __("Select the number of columns for widgets display. If the chosen value is 0, autodetect by the number of widgets.", 'trx_addons') ),
							"admin_label" => true,
							'edit_field_class' => 'vc_col-sm-6',
							"value" => array(0,1,2,3,4,5,6),
							"std" => "0",
							"type" => "dropdown"
						)
					),
					trx_addons_vc_add_hide_param(),
					trx_addons_vc_add_id_param()
				)
			), 'trx_sc_layouts_widgets');
	}
}
