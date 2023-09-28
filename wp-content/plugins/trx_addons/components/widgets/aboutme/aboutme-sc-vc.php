<?php
/**
 * Widget: About Me (WPBakery support)
 *
 * @package ThemeREX Addons
 * @since v1.0
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


// Add [trx_widget_aboutme] in the VC shortcodes list
if (!function_exists('trx_addons_sc_widget_aboutme_add_in_vc')) {
	function trx_addons_sc_widget_aboutme_add_in_vc() {
		
		if (!trx_addons_exists_vc()) return;
		
		vc_lean_map("trx_widget_aboutme", 'trx_addons_sc_widget_aboutme_add_in_vc_params');
		class WPBakeryShortCode_Trx_Widget_Aboutme extends WPBakeryShortCode {}
	}
	add_action('init', 'trx_addons_sc_widget_aboutme_add_in_vc', 20);
}

// Return params
if (!function_exists('trx_addons_sc_widget_aboutme_add_in_vc_params')) {
	function trx_addons_sc_widget_aboutme_add_in_vc_params() {
		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_widget_aboutme",
				"name" => esc_html__("Widget: About Me", 'trx_addons'),
				"description" => wp_kses_data( __("Insert widget with blog owner's name, avatar and short description", 'trx_addons') ),
				"category" => esc_html__('ThemeREX', 'trx_addons'),
				"icon" => 'icon_trx_widget_aboutme',
				"class" => "trx_widget_aboutme",
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
							"param_name" => "avatar",
							"heading" => esc_html__("Avatar", 'trx_addons'),
							"description" => wp_kses_data( __("Select or upload image or write URL from other site for user's avatar. If empty - get gravatar from user's e-mail", 'trx_addons') ),
							"type" => "attach_image"
						),
						array(
							"param_name" => "username",
							"heading" => esc_html__("User name", 'trx_addons'),
							"description" => wp_kses_data( __("User display name. If empty - get display name of the first registered blog user", 'trx_addons') ),
							"admin_label" => true,
							"type" => "textfield"
						),
						array(
							"param_name" => "description",
							"heading" => esc_html__("Description", 'trx_addons'),
							"description" => wp_kses_data( __("Short description about user. If empty - get description of the first registered blog user", 'trx_addons') ),
							"type" => "textarea"
						)
					),
					trx_addons_vc_add_id_param()
				)
			), 'trx_widget_aboutme' );
	}
}
