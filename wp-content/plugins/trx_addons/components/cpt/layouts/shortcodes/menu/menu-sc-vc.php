<?php
/**
 * Shortcode: Display menu in the Layouts Builder (WPBakery support)
 *
 * @package ThemeREX Addons
 * @since v1.6.08
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


// Add [trx_sc_layouts_menu] in the VC shortcodes list
if (!function_exists('trx_addons_sc_layouts_menu_add_in_vc')) {
	function trx_addons_sc_layouts_menu_add_in_vc() {
		
		//if (!trx_addons_cpt_layouts_sc_required()) return;
		
		if (!trx_addons_exists_vc()) return;
		
		vc_lean_map("trx_sc_layouts_menu", 'trx_addons_sc_layouts_menu_add_in_vc_params');
		class WPBakeryShortCode_Trx_Sc_Layouts_Menu extends WPBakeryShortCode {}
	}
	add_action('init', 'trx_addons_sc_layouts_menu_add_in_vc', 15);
}

// Return params
if (!function_exists('trx_addons_sc_layouts_menu_add_in_vc_params')) {
	function trx_addons_sc_layouts_menu_add_in_vc_params() {
		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_sc_layouts_menu",
				"name" => esc_html__("Layouts: Menu", 'trx_addons'),
				"description" => wp_kses_data( __("Insert any menu to the custom layout", 'trx_addons') ),
				"category" => esc_html__('Layouts', 'trx_addons'),
				"icon" => 'icon_trx_sc_layouts_menu',
				"class" => "trx_sc_layouts_menu",
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
							"value" => array_flip(apply_filters('trx_addons_sc_type', trx_addons_get_list_sc_layouts_menu(), 'trx_sc_layouts_menu')),
							"type" => "dropdown"
						),
						array(
							"param_name" => "direction",
							"heading" => esc_html__("Direction", 'trx_addons'),
							"description" => wp_kses_data( __("Select direction of the menu items", 'trx_addons') ),
							"admin_label" => true,
							'edit_field_class' => 'vc_col-sm-6',
							'dependency' => array(
								'element' => 'type',
								'value' => array('default')
							),
							"std" => "horizontal",
							"value" => array_flip(trx_addons_get_list_sc_directions()),
							"type" => "dropdown"
						),
						array(
							"param_name" => "location",
							"heading" => esc_html__("Location", 'trx_addons'),
							"description" => wp_kses_data( __("Select menu location to insert to the layout", 'trx_addons') ),
							"admin_label" => true,
							'edit_field_class' => 'vc_col-sm-6 vc_new_row',
					        'save_always' => true,
							"value" => array_flip(trx_addons_get_list_menu_locations()),
							"type" => "dropdown"
						),
						array(
							"param_name" => "menu",
							"heading" => esc_html__("Menu", 'trx_addons'),
							"description" => wp_kses_data( __("Select menu to insert to the layout. If empty - use menu assigned in the field 'Location'", 'trx_addons') ),
							"admin_label" => true,
							'edit_field_class' => 'vc_col-sm-6',
							'dependency' => array(
								'element' => 'location',
								'value' => 'none'
							),
					        'save_always' => true,
							"value" => array_flip(trx_addons_get_list_menus()),
							"type" => "dropdown"
						),
						array(
							"param_name" => "hover",
							"heading" => esc_html__("Hover", 'trx_addons'),
							"description" => wp_kses_data( __("Select the menu items hover", 'trx_addons') ),
							'dependency' => array(
								'element' => 'type',
								'value' => 'default'
							),
							'edit_field_class' => 'vc_col-sm-4 vc_new_row',
							"std" => "fade",
							"value" => array_flip(trx_addons_get_list_menu_hover()),
							"type" => "dropdown"
						),
						array(
							"param_name" => "animation_in",
							"heading" => esc_html__("Submenu animation in", 'trx_addons'),
							"description" => wp_kses_data( __("Select animation to show submenu", 'trx_addons') ),
							'dependency' => array(
								'element' => 'type',
								'value' => 'default'
							),
							'edit_field_class' => 'vc_col-sm-4',
							"std" => "fadeIn",
							"value" => array_flip(trx_addons_get_list_animations_in()),
							"type" => "dropdown"
						),
						array(
							"param_name" => "animation_out",
							"heading" => esc_html__("Submenu animation out", 'trx_addons'),
							"description" => wp_kses_data( __("Select animation to hide submenu", 'trx_addons') ),
							'dependency' => array(
								'element' => 'type',
								'value' => 'default'
							),
							'edit_field_class' => 'vc_col-sm-4',
							"std" => "fadeOut",
							"value" => array_flip(trx_addons_get_list_animations_out()),
							"type" => "dropdown"
						),
						array(
							"param_name" => "mobile_button",
							"heading" => esc_html__("Mobile button", 'trx_addons'),
							"description" => wp_kses_data( __("Replace the menu with a menu button on mobile devices. Open the menu when the button is clicked.", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4 vc_new_row',
							"std" => "0",
							"value" => array(esc_html__("Add button", 'trx_addons') => "1" ),
							"type" => "checkbox"
						),
						array(
							"param_name" => "mobile_menu",
							"heading" => esc_html__("Add to the mobile menu", 'trx_addons'),
							"description" => wp_kses_data( __("Use these menu items as a mobile menu (if mobile menu is not selected in the theme).", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"std" => "0",
							"value" => array(esc_html__("Use as mobile menu", 'trx_addons') => "1" ),
							"type" => "checkbox"
						),
						array(
							"param_name" => "hide_on_mobile",
							"heading" => esc_html__("Hide on mobile devices", 'trx_addons'),
							"description" => wp_kses_data( __("Hide this item on mobile devices", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							'dependency' => array(
								'element' => 'type',
								'value' => 'default'
							),
							"std" => "0",
							"value" => array(esc_html__("Hide on the mobile devices", 'trx_addons') => "1" ),
							"type" => "checkbox"
						),
					),
					trx_addons_vc_add_id_param()
				)
			), 'trx_sc_layouts_menu');
	}
}
