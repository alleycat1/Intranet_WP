<?php
/**
 * Shortcode: Action (WPBakery support)
 *
 * @package ThemeREX Addons
 * @since v1.2
 */

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }



// Add [trx_sc_action] in the VC shortcodes list
if (!function_exists('trx_addons_sc_action_add_in_vc')) {
	function trx_addons_sc_action_add_in_vc() {
		
		if (!trx_addons_exists_vc()) return;

		vc_lean_map("trx_sc_action", 'trx_addons_sc_action_add_in_vc_params');
		class WPBakeryShortCode_Trx_Sc_Action extends WPBakeryShortCode {}
	}
	add_action('init', 'trx_addons_sc_action_add_in_vc', 20);
}

// Return params
if (!function_exists('trx_addons_sc_action_add_in_vc_params')) {
	function trx_addons_sc_action_add_in_vc_params() {
		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_sc_action",
				"name" => esc_html__("Action", 'trx_addons'),
				"description" => wp_kses_data( __("Insert 'Call to action' or custom Events as slider or columns layout", 'trx_addons') ),
				"category" => esc_html__('ThemeREX', 'trx_addons'),
				"icon" => 'icon_trx_sc_action',
				"class" => "trx_sc_action",
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
							'save_always' => true,
							'edit_field_class' => 'vc_col-sm-3',
							"std" => "default",
							"value" => array_flip(apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('sc', 'action'), 'trx_sc_action')),
							"type" => "dropdown"
						),
						array(
							"param_name" => "columns",
							"heading" => esc_html__("Columns", 'trx_addons'),
							"description" => wp_kses_data( __("Specify the number of columns. If left empty or assigned the value '0' - auto detect by the number of items.", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-3',
							"type" => "textfield"
						),
						array(
							"param_name" => "full_height",
							"heading" => esc_html__("Full height", 'trx_addons'),
							"description" => wp_kses_data( __("Stretch the height of the element to the full screen's height", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-3',
							"admin_label" => true,
							"std" => 0,
							"value" => array(esc_html__("Full Height", 'trx_addons') => 1 ),
							"type" => "checkbox"
						),
						array(
							"param_name" => "min_height",
							"heading" => esc_html__("Height", 'trx_addons'),
							"description" => wp_kses_data( __("Specify the height of items. If left empty or assigned the value '0' - height is auto", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-3',
							"type" => "textfield"
						),
						array(
							'type' => 'param_group',
							'param_name' => 'actions',
							'heading' => esc_html__( 'Actions', 'trx_addons' ),
							"description" => wp_kses_data( __("Select icons, specify title and/or description for each item", 'trx_addons') ),
							'value' => urlencode( json_encode( apply_filters('trx_addons_sc_param_group_value', array(
											array(
												'position' => 'mc',
												'title' => esc_html__( 'One', 'trx_addons' ),
												'subtitle' => '',
												'date' => '',
												'info' => '',
												'description' => '',
												'link' => '',
												'link_text' => '',
												'color' => '',
												'bg_color' => '',
												'image' => '',
												'bg_image' => '',
												'icon' => '',
												'icon_fontawesome' => 'empty',
												'icon_openiconic' => 'empty',
												'icon_typicons' => 'empty',
												'icon_entypo' => 'empty',
												'icon_linecons' => 'empty'
											),
										), 'trx_sc_action') ) ),
							'params' => apply_filters('trx_addons_sc_param_group_params', array_merge(array(
									array(
										"param_name" => "position",
										"heading" => esc_html__("Text position", 'trx_addons'),
										"description" => wp_kses_data( __("Select position of the titles", 'trx_addons') ),
										"std" => "mc",
										"value" => array_flip(trx_addons_get_list_sc_positions()),
										"type" => "dropdown"
									),
									array(
										'param_name' => 'title',
										'heading' => esc_html__( 'Title', 'trx_addons' ),
										'description' => esc_html__( 'Enter title of the item', 'trx_addons' ),
										'admin_label' => true,
										'edit_field_class' => 'vc_col-sm-6 vc_new_row',
										'type' => 'textfield',
									),
									array(
										'param_name' => 'subtitle',
										'heading' => esc_html__( 'Subtitle', 'trx_addons' ),
										'description' => esc_html__( 'Enter subtitle of the item', 'trx_addons' ),
										'edit_field_class' => 'vc_col-sm-6',
										'type' => 'textfield',
									),
									array(
										'param_name' => 'date',
										'heading' => esc_html__( 'Date', 'trx_addons' ),
										'description' => esc_html__( 'Specify date (and/or time) of this event', 'trx_addons' ),
										'edit_field_class' => 'vc_col-sm-6 vc_new_row',
										'type' => 'textfield',
									),
									array(
										'param_name' => 'info',
										'heading' => esc_html__( 'Info', 'trx_addons' ),
										'description' => esc_html__( 'Additional info for this item', 'trx_addons' ),
										'edit_field_class' => 'vc_col-sm-6',
										'type' => 'textfield',
									),
									array(
										'param_name' => 'description',
										'heading' => esc_html__( 'Description', 'trx_addons' ),
										'description' => esc_html__( 'Enter short description of the item', 'trx_addons' ),
										'type' => 'textarea_safe'
									),
									array(
										'param_name' => 'link',
										'heading' => esc_html__( 'Link', 'trx_addons' ),
										'description' => esc_html__( 'URL to link this item', 'trx_addons' ),
										'edit_field_class' => 'vc_col-sm-6',
										'type' => 'textfield'
									),
									array(
										"param_name" => "link_text",
										"heading" => esc_html__("Link's text", 'trx_addons'),
										"description" => wp_kses_data( __("Caption of the link", 'trx_addons') ),
										'edit_field_class' => 'vc_col-sm-6',
										"type" => "textfield"
									),
									array(
										'param_name' => 'color',
										'heading' => esc_html__( 'Color', 'trx_addons' ),
										'description' => esc_html__( 'Select custom color of this item', 'trx_addons' ),
										'edit_field_class' => 'vc_col-sm-6',
										'type' => 'colorpicker'
									),
									array(
										'param_name' => 'bg_color',
										'heading' => esc_html__( 'Background Color', 'trx_addons' ),
										'description' => esc_html__( 'Select custom background color of this item', 'trx_addons' ),
										'edit_field_class' => 'vc_col-sm-6',
										'type' => 'colorpicker'
									),
									array(
										"param_name" => "image",
										"heading" => esc_html__("Image", 'trx_addons'),
										"description" => wp_kses_data( __("Select or upload image or specify URL from other site to use it as icon", 'trx_addons') ),
										'edit_field_class' => 'vc_col-sm-6',
										"type" => "attach_image"
									),
									array(
										"param_name" => "bg_image",
										"heading" => esc_html__("Background image", 'trx_addons'),
										"description" => wp_kses_data( __("Select or upload image or specify URL from other site to use it as background of this item", 'trx_addons') ),
										'edit_field_class' => 'vc_col-sm-6',
										"type" => "attach_image"
									)
								),
								trx_addons_vc_add_icon_param('')
							), 'trx_sc_action')
						)
					),
					trx_addons_vc_add_slider_param(),
					trx_addons_vc_add_title_param(),
					trx_addons_vc_add_id_param()
				)
			), 'trx_sc_action' );
	}
}
