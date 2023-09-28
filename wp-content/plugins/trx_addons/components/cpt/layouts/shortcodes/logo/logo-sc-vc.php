<?php
/**
 * Shortcode: Display site Logo (WPBakery support)
 *
 * @package ThemeREX Addons
 * @since v1.6.08
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


// Add [trx_sc_layouts_logo] in the VC shortcodes list
if (!function_exists('trx_addons_sc_layouts_logo_add_in_vc')) {
	function trx_addons_sc_layouts_logo_add_in_vc() {
		
		if (!trx_addons_cpt_layouts_sc_required()) return;

		if (!trx_addons_exists_vc()) return;
		
		vc_lean_map("trx_sc_layouts_logo", 'trx_addons_sc_layouts_logo_add_in_vc_params');
		class WPBakeryShortCode_Trx_Sc_Layouts_logo extends WPBakeryShortCode {}
	}
	add_action('init', 'trx_addons_sc_layouts_logo_add_in_vc', 15);
}

// Return params
if (!function_exists('trx_addons_sc_layouts_logo_add_in_vc_params')) {
	function trx_addons_sc_layouts_logo_add_in_vc_params() {
		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_sc_layouts_logo",
				"name" => esc_html__("Layouts: Logo", 'trx_addons'),
				"description" => wp_kses_data( __("Insert the site logo to the custom layout", 'trx_addons') ),
				"category" => esc_html__('Layouts', 'trx_addons'),
				"icon" => 'icon_trx_sc_layouts_logo',
				"class" => "trx_sc_layouts_logo",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array_merge(
					array(
						array(
							"param_name" => "type",
							"heading" => esc_html__("Layout", 'trx_addons'),
							"description" => wp_kses_data( __("Select shortcodes's layout", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							"admin_label" => true,
							"std" => "default",
							"value" => array_flip(apply_filters('trx_addons_sc_type', array(
								'default' => esc_html__('Default', 'trx_addons')
							), 'trx_sc_layouts_logo')),
							"type" => "dropdown"
						),
						array(
							"param_name" => "logo_height",
							"heading" => esc_html__("Max height", 'trx_addons'),
							"description" => wp_kses_data( __("Max height of the logo image. If empty - theme default value is used", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							"type" => "textfield"
						),
						array(
							"param_name" => "logo",
							"heading" => esc_html__("Logo", 'trx_addons'),
							"description" => wp_kses_data( __("Select or upload image for site's logo. If empty - theme-specific logo is used", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6 vc_new_row',
							"type" => "attach_image"
						),
						array(
							"param_name" => "logo_retina",
							"heading" => esc_html__("Logo Retina", 'trx_addons'),
							"description" => wp_kses_data( __("Select or upload image for site's logo on the Retina displays", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							"type" => "attach_image"
						),
						array(
							"param_name" => "logo_text",
							"heading" => esc_html__("Logo text", 'trx_addons'),
							"description" => wp_kses_data( __("Site name (used as logo if image is empty or as alt text if image is selected). If not specified - use blog name", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6 vc_new_row',
							"type" => "textfield"
						),
						array(
							"param_name" => "logo_slogan",
							"heading" => esc_html__("Logo slogan", 'trx_addons'),
							"description" => wp_kses_data( __("Slogan or description below site name (used if logo is empty). If not specified - use blog description", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							"type" => "textfield"
						)
					),
					trx_addons_vc_add_hide_param(),
					trx_addons_vc_add_id_param()
				)
			), 'trx_sc_layouts_logo');
	}
}
