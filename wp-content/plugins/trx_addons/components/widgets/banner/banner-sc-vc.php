<?php
/**
 * Widget: Banner (WPBakery support)
 *
 * @package ThemeREX Addons
 * @since v1.0
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


// Add [trx_widget_banner] in the VC shortcodes list
if (!function_exists('trx_addons_sc_widget_banner_add_in_vc')) {
	function trx_addons_sc_widget_banner_add_in_vc() {
		
		if (!trx_addons_exists_vc()) return;
		
		vc_lean_map("trx_widget_banner", 'trx_addons_sc_widget_banner_add_in_vc_params');
		class WPBakeryShortCode_Trx_Widget_Banner extends WPBakeryShortCode {}
	}
	add_action('init', 'trx_addons_sc_widget_banner_add_in_vc', 20);
}

// Return params
if (!function_exists('trx_addons_sc_widget_banner_add_in_vc_params')) {
	function trx_addons_sc_widget_banner_add_in_vc_params() {
		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_widget_banner",
				"name" => esc_html__("Widget: Banner", 'trx_addons'),
				"description" => wp_kses_data( __("Insert widget with banner or any HTML and/or Javascript code", 'trx_addons') ),
				"category" => esc_html__('ThemeREX', 'trx_addons'),
				"icon" => 'icon_trx_widget_banner',
				"class" => "trx_widget_banner",
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
							"param_name" => "image",
							"heading" => esc_html__("Image", 'trx_addons'),
							"description" => wp_kses_data( __("Select or upload image or write URL from other site for the banner (leave empty if you paste banner code)", 'trx_addons') ),
							"type" => "attach_image"
						),
						array(
							"param_name" => "link",
							"heading" => esc_html__("Banner's link", 'trx_addons'),
							"description" => wp_kses_data( __("Link URL for the banner (leave empty if you paste banner code)", 'trx_addons') ),
							"type" => "textfield"
						),
						array(
							"param_name" => "code",
							"heading" => esc_html__("or paste HTML Code", 'trx_addons'),
							"description" => wp_kses_data( __("Widget's HTML and/or JS code", 'trx_addons') ),
							"type" => "textarea_safe"
						),
						array(
							"param_name" => "show",
							"heading" => esc_html__("Show on:", 'trx_addons'),
							"description" => wp_kses_data( __("Always display the widget or hide it on load and show when scrolled to viewport", 'trx_addons') ),
							"options" => array_flip( trx_addons_get_list_sc_show_on() ),
							"type" => "select"
						)
					),
					trx_addons_vc_add_id_param()
				)
			), 'trx_widget_banner' );
	}
}
