<?php
/**
 * Widget: Calendar (WPBakery support)
 *
 * @package ThemeREX Addons
 * @since v1.0
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


// Add [trx_widget_calendar] in the VC shortcodes list
if (!function_exists('trx_addons_sc_widget_calendar_add_in_vc')) {
	function trx_addons_sc_widget_calendar_add_in_vc() {
		
		if (!trx_addons_exists_vc()) return;
		
		vc_lean_map("trx_widget_calendar", 'trx_addons_sc_widget_calendar_add_in_vc_params');
		class WPBakeryShortCode_Trx_Widget_Calendar extends WPBakeryShortCode {}
	}
	add_action('init', 'trx_addons_sc_widget_calendar_add_in_vc', 20);
}

// Return params
if (!function_exists('trx_addons_sc_widget_calendar_add_in_vc_params')) {
	function trx_addons_sc_widget_calendar_add_in_vc_params() {
		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_widget_calendar",
				"name" => esc_html__("Widget: Calendar", 'trx_addons'),
				"description" => wp_kses_data( __("Insert standard WP Calendar, but allow user select week day's captions", 'trx_addons') ),
				"category" => esc_html__('ThemeREX', 'trx_addons'),
				"icon" => 'icon_trx_widget_calendar',
				"class" => "trx_widget_calendar",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array_merge(
					array(
						array(
							"param_name" => "title",
							"heading" => esc_html__("Widget title", 'trx_addons'),
							"description" => wp_kses_data( __("Title of the widget", 'trx_addons') ),
							"admin_label" => true,
							"type" => "textfield"
						),
						array(
							"param_name" => "weekdays",
							"heading" => esc_html__("Week days", 'trx_addons'),
							"description" => wp_kses_data( __("Show captions for the week days as three letters (Sun, Mon, etc.) or as one initial letter (S, M, etc.)", 'trx_addons') ),
							"value" => array("Initial letter" => "initial" ),
							"type" => "checkbox"
						)
					),
					trx_addons_vc_add_id_param()
				)
			), 'trx_widget_calendar' );
	}
}
