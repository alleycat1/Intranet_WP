<?php
/**
 * Shortcode: Display Login link (WPBakery support)
 *
 * @package ThemeREX Addons
 * @since v1.6.08
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}



// Add [trx_sc_layouts_login] in the VC shortcodes list
if (!function_exists('trx_addons_sc_layouts_login_add_in_vc')) {
	function trx_addons_sc_layouts_login_add_in_vc() {
		
		if (!trx_addons_cpt_layouts_sc_required()) return;

		if (!trx_addons_exists_vc()) return;

		vc_lean_map("trx_sc_layouts_login", 'trx_addons_sc_layouts_login_add_in_vc_params');
		class WPBakeryShortCode_Trx_Sc_Layouts_Login extends WPBakeryShortCode {}
	}
	add_action('init', 'trx_addons_sc_layouts_login_add_in_vc', 15);
}

// Return params
if (!function_exists('trx_addons_sc_layouts_login_add_in_vc_params')) {
	function trx_addons_sc_layouts_login_add_in_vc_params() {
		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_sc_layouts_login",
				"name" => esc_html__("Layouts: Login link", 'trx_addons'),
				"description" => wp_kses_data( __("Insert Login/Logout link to the custom layout", 'trx_addons') ),
				"category" => esc_html__('Layouts', 'trx_addons'),
				"icon" => 'icon_trx_sc_layouts_login',
				"class" => "trx_sc_layouts_login",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array_merge(
					array(
						array(
							"param_name" => "type",
							"heading" => esc_html__("Layout", 'trx_addons'),
							"description" => wp_kses_data( __("Select shortcodes's layout", 'trx_addons') ),
							"admin_label" => true,
							'edit_field_class' => 'vc_col-sm-6',
							"std" => "default",
							"value" => array_flip(apply_filters('trx_addons_sc_type', array(
								'default' => esc_html__('Default', 'trx_addons'),
							), 'trx_sc_layouts_login')),
							"type" => "dropdown"
						),
						array(
							"param_name" => "user_menu",
							"heading" => esc_html__("User menu", 'trx_addons'),
							"description" => wp_kses_data( __("Show user menu on mouse hover", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							"std" => "0",
							"value" => array(esc_html__("Show user menu", 'trx_addons') => "1" ),
							"type" => "checkbox"
						),
						array(
							"param_name" => "text_login",
							"heading" => esc_html__("Login text", 'trx_addons'),
							"description" => wp_kses_data( __("Text of the Login link", 'trx_addons') ),
							"admin_label" => true,
							'edit_field_class' => 'vc_col-sm-6 vc_new_row',
							"type" => "textfield"
						),
						array(
							"param_name" => "text_logout",
							"heading" => esc_html__("Logout text", 'trx_addons'),
							"description" => wp_kses_data( __("Text of the Logout link", 'trx_addons') ),
							"admin_label" => true,
							'edit_field_class' => 'vc_col-sm-6',
							"type" => "textfield"
						)
					),
					trx_addons_vc_add_hide_param(),
					trx_addons_vc_add_id_param()
				)
			), 'trx_sc_layouts_login');
	}
}
