<?php
/**
 * Shortcode: Form (WPBakery support)
 *
 * @package ThemeREX Addons
 * @since v1.2
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}



// Add [trx_sc_form] in the VC shortcodes list
if (!function_exists('trx_addons_sc_form_add_in_vc')) {
	function trx_addons_sc_form_add_in_vc() {
		
		if (!trx_addons_exists_vc()) return;
		
		vc_lean_map("trx_sc_form", 'trx_addons_sc_form_add_in_vc_params');
		class WPBakeryShortCode_Trx_Sc_Form extends WPBakeryShortCode {}
	}
	add_action('init', 'trx_addons_sc_form_add_in_vc', 20);
}

// Return params
if (!function_exists('trx_addons_sc_form_add_in_vc_params')) {
	function trx_addons_sc_form_add_in_vc_params() {
		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_sc_form",
				"name" => esc_html__("Form", 'trx_addons'),
				"description" => wp_kses_data( __("Insert simple or detailed form", 'trx_addons') ),
				"category" => esc_html__('ThemeREX', 'trx_addons'),
				"icon" => 'icon_trx_sc_form',
				"class" => "trx_sc_form",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array_merge(
					array(
						array(
							"param_name" => "type",
							"heading" => esc_html__("Layout", 'trx_addons'),
							"description" => wp_kses_data( __("Select form's layout", 'trx_addons') ),
							"admin_label" => true,
							'edit_field_class' => 'vc_col-sm-4',
					        'save_always' => true,
							"std" => "default",
							"value" => array_flip(apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('sc', 'form'), 'trx_sc_form')),
							"type" => "dropdown"
						),
						array(
							"param_name" => "style",
							"heading" => esc_html__("Style", 'trx_addons'),
							"description" => wp_kses_data( __("Select input's style", 'trx_addons') ),
							"admin_label" => true,
							'edit_field_class' => 'vc_col-sm-4',
							"std" => "inherit",
							"value" => array_flip(trx_addons_get_list_input_hover(true)),
							"type" => "dropdown"
						),
						array(
							"param_name" => "align",
							"heading" => esc_html__("Fields alignment", 'trx_addons'),
							"description" => wp_kses_data( __("Select alignment of the field's text", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"std" => "default",
							"value" => trx_addons_get_list_sc_aligns(),
							"type" => "dropdown"
						),
						array(
							'param_name' => 'email',
							'heading' => esc_html__( 'Your E-mail', 'trx_addons' ),
							'description' => esc_html__( 'Specify your E-mail for the detailed form. This address will be used to send you filled form data. If empty - admin e-mail will be used', 'trx_addons' ),
							'edit_field_class' => 'vc_col-sm-4 vc_new_row',
							'type' => 'textfield',
						),
						array(
							'param_name' => 'phone',
							'heading' => esc_html__( 'Your phone', 'trx_addons' ),
							'description' => esc_html__( 'Specify your phone for the detailed form', 'trx_addons' ),
							'edit_field_class' => 'vc_col-sm-4',
							'dependency' => array(
								'element' => 'type',
								'value' => array('modern', 'detailed')
							),
							'type' => 'textfield',
						),
						array(
							'param_name' => 'address',
							'heading' => esc_html__( 'Your address', 'trx_addons' ),
							'description' => esc_html__( 'Specify your address for the detailed form', 'trx_addons' ),
							'edit_field_class' => 'vc_col-sm-4',
							'dependency' => array(
								'element' => 'type',
								'value' => array('modern', 'detailed')
							),
							'type' => 'textfield',
						),
						array(
							'param_name' => 'button_caption',
							'heading' => esc_html__( 'Button caption', 'trx_addons' ),
							'description' => esc_html__( 'Caption of the "Send" button', 'trx_addons' ),
							'edit_field_class' => 'vc_col-sm-4 vc_new_row',
							'type' => 'textfield',
						),
						array(
							"param_name" => "labels",
							"heading" => esc_html__("Field labels", 'trx_addons'),
							"description" => wp_kses_data( __("Show field's labels", 'trx_addons') ),
							"admin_label" => true,
							'edit_field_class' => 'vc_col-sm-4',
							"std" => "0",
							"value" => array(esc_html__("Show labels", 'trx_addons') => "1" ),
							"type" => "checkbox"
						),
					),
					trx_addons_vc_add_title_param(false, false),
					trx_addons_vc_add_id_param()
				)
			), 'trx_sc_form' );
	}
}
