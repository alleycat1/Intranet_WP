<?php
/**
 * Shortcode: Socials (WPBakery support)
 *
 * @package ThemeREX Addons
 * @since v1.2
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


// Add [trx_sc_socials] in the VC shortcodes list
if (!function_exists('trx_addons_sc_socials_add_in_vc')) {
	function trx_addons_sc_socials_add_in_vc() {
		
		if (!trx_addons_exists_vc()) return;
		
		vc_lean_map("trx_sc_socials", 'trx_addons_sc_socials_add_in_vc_params');
		class WPBakeryShortCode_Trx_Sc_Socials extends WPBakeryShortCode {}
	}
	add_action('init', 'trx_addons_sc_socials_add_in_vc', 20);
}

// Return params
if (!function_exists('trx_addons_sc_socials_add_in_vc_params')) {
	function trx_addons_sc_socials_add_in_vc_params() {
		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_sc_socials",
				"name" => esc_html__("Socials", 'trx_addons'),
				"description" => wp_kses_data( __("Insert social icons with links on your profiles", 'trx_addons') ),
				"category" => esc_html__('ThemeREX', 'trx_addons'),
				"icon" => 'icon_trx_sc_socials',
				"class" => "trx_sc_socials",
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
							'edit_field_class' => 'vc_col-sm-4',
							"std" => "default",
							"value" => array_flip(apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('sc', 'socials'), 'trx_sc_socials')),
							"type" => "dropdown"
						),
						array(
							"param_name" => "icons_type",
							"heading" => esc_html__("Icons type", 'trx_addons'),
							"description" => wp_kses_data( __("Select links type: to the social profiles or share links", 'trx_addons') ),
							"admin_label" => true,
							'edit_field_class' => 'vc_col-sm-4',
							"std" => "socials",
							"value" => array_flip(trx_addons_get_list_sc_socials_types()),
							"type" => "dropdown"
						),
						array(
							"param_name" => "align",
							"heading" => esc_html__("Icons alignment", 'trx_addons'),
							"description" => wp_kses_data( __("Select alignment of the icons", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"std" => "none",
							"value" => array_flip(trx_addons_get_list_sc_aligns()),
							"type" => "dropdown"
						),
						array(
							'type' => 'param_group',
							'param_name' => 'icons',
							'heading' => esc_html__( 'Icons', 'trx_addons' ),
							"description" => wp_kses_data( __("Select social icons and specify link for each item", 'trx_addons') ),
							'value' => urlencode( json_encode( apply_filters('trx_addons_sc_param_group_value', array(
								array(
									'title' => '',
									'link' => '',
									'icon_image' => '',
									'icon' => '',
									'icon_fontawesome' => 'empty',
									'icon_openiconic' => 'empty',
									'icon_typicons' => 'empty',
									'icon_entypo' => 'empty',
									'icon_linecons' => 'empty'
								),
							), 'trx_sc_socials') ) ),
							'params' => apply_filters('trx_addons_sc_param_group_params',
									array_merge(
										array(
											array(
												'param_name' => 'title',
												'heading' => esc_html__( 'Title', 'trx_addons' ),
												'description' => esc_html__( 'Name of the social network', 'trx_addons' ),
												'edit_field_class' => 'vc_col-sm-6',
												'admin_label' => true,
												'type' => 'textfield',
											),
											array(
												'param_name' => 'link',
												'heading' => esc_html__( 'Link', 'trx_addons' ),
												'description' => esc_html__( 'URL to your profile', 'trx_addons' ),
												'edit_field_class' => 'vc_col-sm-6',
												'admin_label' => true,
												'type' => 'textfield',
											)
										),
										trx_addons_vc_add_icon_param('', true)
									),
									'trx_sc_socials')
						)
					),
					trx_addons_vc_add_title_param(),
					trx_addons_vc_add_id_param()
				)
			), 'trx_sc_socials' );
	}
}
