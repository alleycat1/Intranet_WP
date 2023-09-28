<?php
/**
 * Widget: Socials (WPBakery support)
 *
 * @package ThemeREX Addons
 * @since v1.0
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


// Add [trx_widget_socials] in the VC shortcodes list
if (!function_exists('trx_addons_sc_widget_socials_add_in_vc')) {
	function trx_addons_sc_widget_socials_add_in_vc() {
		
		if (!trx_addons_exists_vc()) return;
		
		vc_lean_map("trx_widget_socials", 'trx_addons_sc_widget_socials_add_in_vc_params');
		class WPBakeryShortCode_Trx_Widget_Socials extends WPBakeryShortCode {}
	}
	add_action('init', 'trx_addons_sc_widget_socials_add_in_vc', 20);
}

// Return params
if (!function_exists('trx_addons_sc_widget_socials_add_in_vc_params')) {
	function trx_addons_sc_widget_socials_add_in_vc_params() {
		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_widget_socials",
				"name" => esc_html__("Widget: Socials", 'trx_addons'),
				"description" => wp_kses_data( __("Insert widget with social icons, that specified in the Theme Customizer", 'trx_addons') ),
				"category" => esc_html__('ThemeREX', 'trx_addons'),
				"icon" => 'icon_trx_widget_socials',
				"class" => "trx_widget_socials",
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
							"param_name" => "type",
							"heading" => esc_html__("Icons type", 'trx_addons'),
							"description" => wp_kses_data( __("Select links type: to the social profiles or share links", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							"std" => "socials",
							"value" => array_flip(trx_addons_get_list_sc_socials_types()),
							"type" => "dropdown"
						),
						array(
							"param_name" => "align",
							"heading" => esc_html__("Icons align", 'trx_addons'),
							"description" => wp_kses_data( __("Select alignment of this item", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							"std" => "left",
							"value" => array_flip(trx_addons_get_list_sc_aligns()),
							"type" => "dropdown"
						),
						array(
							"param_name" => "description",
							"heading" => esc_html__("Description", 'trx_addons'),
							"description" => wp_kses_data( __("Short description about user", 'trx_addons') ),
							"type" => "textarea"
						),
					),
					trx_addons_vc_add_id_param()
				)
			), 'trx_widget_socials' );
	}
}
