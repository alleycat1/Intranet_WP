<?php
/**
 * Shortcode: Button (WPBakery support)
 *
 * @package ThemeREX Addons
 * @since v1.2
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}



// Add [trx_sc_button] in the VC shortcodes list
if (!function_exists('trx_addons_sc_button_add_in_vc')) {
	function trx_addons_sc_button_add_in_vc() {
		
		if (!trx_addons_exists_vc()) return;

		vc_lean_map("trx_sc_button", 'trx_addons_sc_button_add_in_vc_params');
		class WPBakeryShortCode_Trx_Sc_Button extends WPBakeryShortCode {}
	}
	add_action('init', 'trx_addons_sc_button_add_in_vc', 20);
}

// Return params
if (!function_exists('trx_addons_sc_button_add_in_vc_params')) {
	function trx_addons_sc_button_add_in_vc_params() {
		return apply_filters('trx_addons_sc_map', array(
			"base" => "trx_sc_button",
			"name" => esc_html__("Button", 'trx_addons'),
			"description" => wp_kses_data( __("Insert button", 'trx_addons') ),
			"category" => esc_html__('ThemeREX', 'trx_addons'),
			'icon' => 'icon_trx_sc_button',
			"class" => "trx_sc_button",
			'content_element' => true,
			'is_container' => false,
			"show_settings_on_create" => true,
			"params" => array_merge(
				array(
					array(
						"param_name" => "align",
						"heading" => esc_html__("Button alignment", 'trx_addons'),
						"description" => wp_kses_data( __("Select button alignment", 'trx_addons') ),
						"value" => array_flip(trx_addons_get_list_sc_aligns()),
						"std" => "none",
						"type" => "dropdown"
					),
					array(
						'type' => 'param_group',
						'param_name' => 'buttons',
						'heading' => esc_html__( 'Buttons', 'trx_addons' ),
						'value' => urlencode( json_encode( apply_filters('trx_addons_sc_param_group_value', array(
										array(
											'type' => 'default',
											'link' => '',
											'new_window' => 0,
											'title' => esc_html__( 'Button', 'trx_addons' ),
											'subtitle' => '',
											'text_align' => 'none',
											'size' => 'normal',
											'back_image' => '',
											'icon_position' => 'left',
											'image' => '',
											'icon' => '',
											'icon_fontawesome' => 'empty',
											'icon_openiconic' => 'empty',
											'icon_typicons' => 'empty',
											'icon_entypo' => 'empty',
											'icon_linecons' => 'empty',
											'item_id' => '',	// 'id' not work in Elementor
											'class' => '',
											'css' => ''
										)
									), 'trx_sc_button') ) ),
						'params' => apply_filters('trx_addons_sc_param_group_params', array_merge(
							array(
								array(
									"param_name" => "type",
									"heading" => esc_html__("Layout", 'trx_addons'),
									"description" => wp_kses_data( __("Select shortcodes's layout", 'trx_addons') ),
									"admin_label" => true,
							        'save_always' => true,
									'edit_field_class' => 'vc_col-sm-4',
									"std" => "default",
									"value" => array_flip(apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('sc', 'button'), 'trx_sc_button')),
									"type" => "dropdown"
								),
								array(
									"param_name" => "size",
									"heading" => esc_html__("Size", 'trx_addons'),
									"description" => wp_kses_data( __("Size of the button", 'trx_addons') ),
									'edit_field_class' => 'vc_col-sm-4',
									"value" => array_flip(trx_addons_get_list_sc_button_sizes()),
									"std" => "normal",
									"type" => "dropdown"
								),
								array(
									"param_name" => "link",
									"heading" => esc_html__("Button URL", 'trx_addons'),
									"description" => wp_kses_data( __("Link URL for the button", 'trx_addons') ),
									'edit_field_class' => 'vc_col-sm-4 vc_new_row',
									"admin_label" => true,
									"type" => "textfield"
								),
								array(
									"param_name" => "new_window",
									"heading" => esc_html__("Open in the new tab", 'trx_addons'),
									"description" => wp_kses_data( __("Open this link in the new browser's tab", 'trx_addons') ),
									'edit_field_class' => 'vc_col-sm-4',
									"admin_label" => true,
									"std" => 0,
									"value" => array(esc_html__("Open in the new tab", 'trx_addons') => 1 ),
									"type" => "checkbox"
								),
								array(
									"param_name" => "title",
									"heading" => esc_html__("Title", 'trx_addons'),
									"description" => wp_kses_data( __("Title of the button.", 'trx_addons') ),
									'edit_field_class' => 'vc_col-sm-4 vc_new_row',
									"admin_label" => true,
									"type" => "textfield"
								),
								array(
									"param_name" => "subtitle",
									"heading" => esc_html__("Subtitle", 'trx_addons'),
									"description" => wp_kses_data( __("Subtitle for the button", 'trx_addons') ),
									'edit_field_class' => 'vc_col-sm-4',
									"type" => "textfield"
								),
								array(
									"param_name" => "text_align",
									"heading" => esc_html__("Text alignment", 'trx_addons'),
									"description" => wp_kses_data( __("Select text alignment", 'trx_addons') ),
									'edit_field_class' => 'vc_col-sm-4',
									"value" => array_flip(trx_addons_get_list_sc_aligns()),
									"std" => "none",
									"type" => "dropdown"
								),
								array(
									"param_name" => "back_image",		// Alter name for bg_image in VC (it broke bg_image)
									"heading" => esc_html__("Button's background image", 'trx_addons'),
									"description" => wp_kses_data( __("Select the image from the library for this button's background", 'trx_addons') ),
									'dependency' => array(
										'element' => 'type',
										'value' => 'default'
									),
									"type" => "attach_image"
								)
							),
							trx_addons_vc_add_icon_param(),
							array(
								array(
									"param_name" => "image",
									"heading" => esc_html__("or select an image", 'trx_addons'),
									"description" => wp_kses_data( __("Select the image instead the icon (if need)", 'trx_addons') ),
									"group" => esc_html__('Icons', 'trx_addons'),
									"type" => "attach_image"
								),
								array(
									"param_name" => "icon_position",
									"heading" => esc_html__("Icon position", 'trx_addons'),
									"description" => wp_kses_data( __("Place the icon (image) to the left or to the right or to the top side of the button", 'trx_addons') ),
									"group" => esc_html__('Icons', 'trx_addons'),
									'edit_field_class' => 'vc_col-sm-4',
							        'save_always' => true,
									"value" => array_flip(trx_addons_get_list_sc_icon_positions()),
									"std" => "left",
									"type" => "dropdown"
								),
								array(
									"param_name" => "item_id",	// 'id' not work in Elementor
									"heading" => esc_html__("CSS ID", 'trx_addons'),
									'edit_field_class' => 'vc_col-sm-4 vc_new_row',
									"type" => "textfield"
								),
								array(
									"param_name" => "class",
									"heading" => esc_html__("CSS Class", 'trx_addons'),
									'edit_field_class' => 'vc_col-sm-4',
									"type" => "textfield"
								),
								array(
									"param_name" => "css",
									"heading" => esc_html__("CSS Rules", 'trx_addons'),
									'edit_field_class' => 'vc_col-sm-4',
									"type" => "textfield"
								)
							)
						), 'trx_sc_button' )
					)
				),
				trx_addons_vc_add_id_param()
			)
		), 'trx_sc_button' );
	}
}
