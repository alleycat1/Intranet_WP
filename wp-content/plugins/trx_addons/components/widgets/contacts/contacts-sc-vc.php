<?php
/**
 * Widget: Display Contacts info (WPBakery support)
 *
 * @package ThemeREX Addons
 * @since v1.0
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


// Add [trx_widget_contacts] in the VC shortcodes list
if (!function_exists('trx_addons_sc_widget_contacts_add_in_vc')) {
	function trx_addons_sc_widget_contacts_add_in_vc() {
		
		if (!trx_addons_exists_vc()) return;
		
		vc_lean_map("trx_widget_contacts", 'trx_addons_sc_widget_contacts_add_in_vc_params');
		class WPBakeryShortCode_Trx_Widget_Contacts extends WPBakeryShortCodesContainer {}
	}
	add_action('init', 'trx_addons_sc_widget_contacts_add_in_vc', 20);
}

// Return params
if (!function_exists('trx_addons_sc_widget_contacts_add_in_vc_params')) {
	function trx_addons_sc_widget_contacts_add_in_vc_params() {
		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_widget_contacts",
				"name" => esc_html__("Widget: Contacts", 'trx_addons'),
				"description" => wp_kses_data( __("Insert widget with logo, short description and contacts", 'trx_addons') ),
				"category" => esc_html__('ThemeREX', 'trx_addons'),
				"icon" => 'icon_trx_widget_contacts',
				"class" => "trx_widget_contacts",
				"content_element" => true,
				'is_container' => true,
				'as_child' => array('except' => 'trx_widget_contacts'),
				"js_view" => 'VcTrxAddonsContainerView',	//'VcColumnView',
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
							"param_name" => "logo",
							"heading" => esc_html__("Logo", 'trx_addons'),
							"description" => wp_kses_data( __("Select or upload image or write URL from other site for site's logo.", 'trx_addons') ),
							"type" => "attach_image"
						),
						array(
							"param_name" => "logo_retina",
							"heading" => esc_html__("Logo Retina", 'trx_addons'),
							"description" => wp_kses_data( __("Select or upload image or write URL from other site: site's logo for the Retina display.", 'trx_addons') ),
							"type" => "attach_image"
						),
						array(
							"param_name" => "description",
							"heading" => esc_html__("Description", 'trx_addons'),
							"description" => wp_kses_data( __("Short description about user. If empty - get description of the first registered blog user", 'trx_addons') ),
							"type" => "textarea"
						),
						array(
							"param_name" => "address",
							"heading" => esc_html__("Address", 'trx_addons'),
							"description" => wp_kses_data( __("Address string. Use '|' to split this string on two parts", 'trx_addons') ),
							"admin_label" => true,
							"type" => "textfield"
						),
						array(
							"param_name" => "phone",
							"heading" => esc_html__("Phone", 'trx_addons'),
							"description" => wp_kses_data( __("Your phone", 'trx_addons') ),
							"admin_label" => true,
							"type" => "textfield"
						),
						array(
							"param_name" => "email",
							"heading" => esc_html__("E-mail", 'trx_addons'),
							"description" => wp_kses_data( __("Your e-mail address", 'trx_addons') ),
							"admin_label" => true,
							"type" => "textfield"
						),
						array(
							"param_name" => "columns",
							"heading" => esc_html__("Break into columns", 'trx_addons'),
							"description" => wp_kses_data( __("Break contact information into two columns with the address being displayed on the left hand side and phone/email - on the right.", 'trx_addons') ),
							"std" => "0",
							"value" => array("Break on columns" => "1" ),
							"type" => "checkbox"
						),
						array(
							"param_name" => "map",
							"heading" => esc_html__("Show map", 'trx_addons'),
							"description" => wp_kses_data( __("Do you want to display map with address above", 'trx_addons') ),
							"std" => "0",
							"value" => array("Show map" => "1" ),
							"type" => "checkbox"
						),
						array(
							"param_name" => "map_height",
							"heading" => esc_html__("Map height", 'trx_addons'),
							"description" => wp_kses_data( __("Height of the map", 'trx_addons') ),
							'dependency' => array(
								'element' => 'map',
								'value' => '1',
							),
							"type" => "textfield"
						),
						array(
							"param_name" => "map_position",
							"heading" => esc_html__("Map position", 'trx_addons'),
							"description" => wp_kses_data( __("Select position of the map", 'trx_addons') ),
							'dependency' => array(
								'element' => 'map',
								'value' => '1',
							),
							"std" => "top",
							"value" => array(
								esc_html__('Top', 'trx_addons') => 'top',
								esc_html__('Left', 'trx_addons') => 'left',
								esc_html__('Right', 'trx_addons') => 'right'
							),
							"type" => "dropdown"
						),
						array(
							"param_name" => "socials",
							"heading" => esc_html__("Show Social Icons", 'trx_addons'),
							"description" => wp_kses_data( __("Do you want to display icons with links on your profiles in the Social networks?", 'trx_addons') ),
							"std" => "0",
							"value" => array("Show Social Icons" => "1" ),
							"type" => "checkbox"
						)
					),
					trx_addons_vc_add_id_param()
				)
			), 'trx_widget_contacts');
	}
}
