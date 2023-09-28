<?php
/**
 * Widget: Flickr (WPBakery support)
 *
 * @package ThemeREX Addons
 * @since v1.1
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}



// Add [trx_widget_flickr] in the VC shortcodes list
if (!function_exists('trx_addons_widget_flickr_reg_shortcodes_vc')) {
	function trx_addons_widget_flickr_reg_shortcodes_vc() {
		
		if (!trx_addons_exists_vc()) return;
		
		vc_lean_map("trx_widget_flickr", 'trx_addons_widget_flickr_reg_shortcodes_vc_params');
		class WPBakeryShortCode_Trx_Widget_Flickr extends WPBakeryShortCode {}
	}
	add_action('init', 'trx_addons_widget_flickr_reg_shortcodes_vc', 20);
}

// Return params
if (!function_exists('trx_addons_widget_flickr_reg_shortcodes_vc_params')) {
	function trx_addons_widget_flickr_reg_shortcodes_vc_params() {
		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_widget_flickr",
				"name" => esc_html__("Widget: Flickr", 'trx_addons'),
				"description" => wp_kses_data( __("Display the latest photos from Flickr account", 'trx_addons') ),
				"category" => esc_html__('ThemeREX', 'trx_addons'),
				"icon" => 'icon_trx_widget_flickr',
				"class" => "trx_widget_flickr",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array_merge(
					array(
						array(
							"param_name" => "title",
							"heading" => esc_html__("Widget title", 'trx_addons'),
							"description" => wp_kses_data( __("Title of the widget", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"admin_label" => true,
							"class" => "",
							"value" => "",
							"type" => "textfield"
						),
						array(
							"param_name" => "flickr_api_key",
							"heading" => esc_html__("Flickr API key", 'trx_addons'),
							"description" => wp_kses_data( __("Specify API key from your Flickr application", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"class" => "",
							"value" => "",
							"type" => "textfield"
						),
						array(
							"param_name" => "flickr_username",
							"heading" => esc_html__("Flickr username", 'trx_addons'),
							"description" => wp_kses_data( __("Your Flickr username", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"class" => "",
							"value" => "",
							"type" => "textfield"
						),
						array(
							"param_name" => "flickr_count",
							"heading" => esc_html__("Number of photos", 'trx_addons'),
							"description" => wp_kses_data( __("How many photos to be displayed?", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4 vc_new_row',
							"class" => "",
							"value" => "8",
							"type" => "textfield"
						),
						array(
							"param_name" => "flickr_columns",
							"heading" => esc_html__("Columns", 'trx_addons'),
							"description" => wp_kses_data( __("Columns number", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"class" => "",
							"value" => "4",
							"type" => "textfield"
						),
						array(
							"param_name" => "flickr_columns_gap",
							"heading" => esc_html__("Columns gap", 'trx_addons'),
							"description" => wp_kses_data( __("Gap between images", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"class" => "",
							"value" => "0",
							"type" => "textfield"
						),
					),
					trx_addons_vc_add_id_param()
				)
			), 'trx_widget_flickr');
	}
}
