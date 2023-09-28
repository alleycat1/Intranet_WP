<?php
/**
 * Shortcode: Price block (WPBakery support)
 *
 * @package ThemeREX Addons
 * @since v1.2
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}



// Add [trx_sc_price] in the VC shortcodes list
if (!function_exists('trx_addons_sc_price_add_in_vc')) {
	function trx_addons_sc_price_add_in_vc() {
		
		if (!trx_addons_exists_vc()) return;
		
		vc_lean_map("trx_sc_price", 'trx_addons_sc_price_add_in_vc_params');
		class WPBakeryShortCode_Trx_Sc_Price extends WPBakeryShortCode {}
	}
	add_action('init', 'trx_addons_sc_price_add_in_vc', 20);
}

// Return params
if (!function_exists('trx_addons_sc_price_add_in_vc_params')) {
	function trx_addons_sc_price_add_in_vc_params() {
		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_sc_price",
				"name" => esc_html__("Price", 'trx_addons'),
				"description" => wp_kses_data( __("Add block with prices", 'trx_addons') ),
				"category" => esc_html__('ThemeREX', 'trx_addons'),
				"icon" => 'icon_trx_sc_price',
				"class" => "trx_sc_price",
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
							"std" => "default",
							"value" => array_flip(apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('sc', 'price'), 'trx_sc_price')),
							"type" => "dropdown"
						),
						array(
							"param_name" => "columns",
							"heading" => esc_html__("Columns", 'trx_addons'),
							"description" => wp_kses_data( __("Specify the number of columns. If left empty or assigned the value '0' - auto detect by the number of items.", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							"type" => "textfield"
						),
						array(
							'type' => 'param_group',
							'param_name' => 'prices',
							'heading' => esc_html__( 'Prices', 'trx_addons' ),
							"description" => wp_kses_data( __("Select icon, specify price, title and/or description for each item", 'trx_addons') ),
							'value' => urlencode( json_encode( apply_filters('trx_addons_sc_param_group_value', array(
											array(
												'title' => esc_html__( 'One', 'trx_addons' ),
												'subtitle' => '',
												'description' => '',
												'details' => '',
												'link' => '',
												'link_text' => '',
												'label' => '',
												'price' => '',
												'before_price' => '',
												'after_price' => '',
												'image' => '',
												'bg_color' => '',
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
										'param_name' => 'title',
										'heading' => esc_html__( 'Title', 'trx_addons' ),
										'description' => esc_html__( 'Title of the price', 'trx_addons' ),
										'edit_field_class' => 'vc_col-sm-4',
										'admin_label' => true,
										'type' => 'textfield',
									),
									array(
										'param_name' => 'subtitle',
										'heading' => esc_html__( 'Subtitle', 'trx_addons' ),
										'description' => esc_html__( 'Subtitle of the price', 'trx_addons' ),
										'edit_field_class' => 'vc_col-sm-4',
										'type' => 'textfield',
									),
									array(
										'param_name' => 'label',
										'heading' => esc_html__( 'Label', 'trx_addons' ),
										'description' => esc_html__( 'If not empty, a colored band with this text is shown at the top corner of the price block.', 'trx_addons' ),
										'edit_field_class' => 'vc_col-sm-4',
										'type' => 'textfield',
									),
									array(
										'param_name' => 'description',
										'heading' => esc_html__( 'Description', 'trx_addons' ),
										'description' => esc_html__( 'Price description', 'trx_addons' ),
										'type' => 'textfield',
									),
									array(
										'param_name' => 'before_price',
										'heading' => esc_html__( 'Before price', 'trx_addons' ),
										'description' => esc_html__( 'Any text before the price value', 'trx_addons' ),
										'edit_field_class' => 'vc_col-sm-4 vc_new_row',
										'type' => 'textfield',
									),
									array(
										'param_name' => 'price',
										'heading' => esc_html__( 'Price', 'trx_addons' ),
										'description' => esc_html__( 'Price value', 'trx_addons' ),
										'admin_label' => true,
										'edit_field_class' => 'vc_col-sm-4',
										'type' => 'textfield',
									),
									array(
										'param_name' => 'after_price',
										'heading' => esc_html__( 'After price', 'trx_addons' ),
										'description' => esc_html__( 'Any text after the price value', 'trx_addons' ),
										'edit_field_class' => 'vc_col-sm-4',
										'type' => 'textfield',
									),
									array(
										'param_name' => 'details',
										'heading' => esc_html__( 'Details', 'trx_addons' ),
										'description' => esc_html__( 'Price details', 'trx_addons' ),
										'type' => 'textarea',
									),
									array(
										'param_name' => 'link',
										'heading' => esc_html__( 'Link', 'trx_addons' ),
										'description' => esc_html__( 'Specify URL of the button under details', 'trx_addons' ),
										'admin_label' => true,
										'edit_field_class' => 'vc_col-sm-4 vc_new_row',
										'type' => 'textfield',
									),
									array(
										'param_name' => 'link_text',
										'heading' => esc_html__( 'Link text', 'trx_addons' ),
										'description' => esc_html__( 'Specify caption of the button under details', 'trx_addons' ),
										'dependency' => array(
											'element' => 'link',
											'not_empty' => true,
										),
										'edit_field_class' => 'vc_col-sm-4',
										'admin_label' => true,
										'type' => 'textfield',
									),
									array(
										"param_name" => "new_window",
										"heading" => esc_html__("Open in the new tab", 'trx_addons'),
										"description" => wp_kses_data( __("Open this link in the new browser's tab", 'trx_addons') ),
										'edit_field_class' => 'vc_col-sm-4',
										"std" => 0,
										"value" => array(esc_html__("Open in the new tab", 'trx_addons') => 1 ),
										"type" => "checkbox"
									)
								),
								trx_addons_vc_add_icon_param(''),
								array(
									array(
										"param_name" => "image",
										"heading" => esc_html__("Image", 'trx_addons'),
										"description" => wp_kses_data( __("Select or upload image to display it at top of this item", 'trx_addons') ),
										'edit_field_class' => 'vc_col-sm-4 vc_new_row',
										"type" => "attach_image"
									),
									array(
										"param_name" => "bg_image",
										"heading" => esc_html__("Background image", 'trx_addons'),
										"description" => wp_kses_data( __("Select or upload image to use it as background of this item", 'trx_addons') ),
										'edit_field_class' => 'vc_col-sm-4',
										"type" => "attach_image"
									),
									array(
										'param_name' => 'bg_color',
										'heading' => esc_html__( 'Background Color', 'trx_addons' ),
										'description' => esc_html__( 'Select custom background color of this item', 'trx_addons' ),
										'edit_field_class' => 'vc_col-sm-4',
										'type' => 'colorpicker'
									),
								)
							), 'trx_sc_price')
						)
					),
					trx_addons_vc_add_slider_param(),
					trx_addons_vc_add_title_param(),
					trx_addons_vc_add_id_param()
				)
			), 'trx_sc_price' );
	}
}
