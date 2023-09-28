<?php
/**
 * Widget: Instagram (WPBakery support)
 *
 * @package ThemeREX Addons
 * @since v1.6.47
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


// Add [trx_widget_instagram] in the VC shortcodes list
if (!function_exists('trx_addons_widget_instagram_reg_shortcodes_vc')) {
	function trx_addons_widget_instagram_reg_shortcodes_vc() {
		
		if (!trx_addons_exists_vc()) return;
		
		vc_lean_map("trx_widget_instagram", 'trx_addons_widget_instagram_reg_shortcodes_vc_params');
		class WPBakeryShortCode_Trx_Widget_Instagram extends WPBakeryShortCode {}
	}
	add_action('init', 'trx_addons_widget_instagram_reg_shortcodes_vc', 20);
}

// Return params
if (!function_exists('trx_addons_widget_instagram_reg_shortcodes_vc_params')) {
	function trx_addons_widget_instagram_reg_shortcodes_vc_params() {
		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_widget_instagram",
				"name" => esc_html__("Widget: Instagram", 'trx_addons'),
				"description" => wp_kses_data( __("Display the latest photos from instagram account by hashtag", 'trx_addons') ),
				"category" => esc_html__('ThemeREX', 'trx_addons'),
				"icon" => 'icon_trx_widget_instagram',
				"class" => "trx_widget_instagram",
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
							'edit_field_class' => 'vc_col-sm-6',
							"class" => "",
							"value" => "",
							"type" => "textfield"
						),
						array(
							"param_name" => "type",
							"heading" => esc_html__("Layout", 'trx_addons'),
							"description" => wp_kses_data( __("Select widget's layout", 'trx_addons') ),
							"admin_label" => true,
							'edit_field_class' => 'vc_col-sm-6',
							"std" => "default",
							"value" => array_flip(apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('widgets', 'instagram'), 'trx_widget_instagram')),
							"type" => "dropdown"
						),
						array(
							"param_name" => "demo",
							"heading" => esc_html__('Demo mode', 'trx_addons'),
							"description" => wp_kses_data( __('Show demo images', 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							'save_always' => true,
							"std" => "0",
							"value" => array(
								esc_html__( "Demo mode", 'trx_addons' ) => "1"
							),
							"type" => "checkbox"
						),
						array(
							"param_name" => "demo_thumb_size",
							"heading" => esc_html__("Thumb size", 'trx_addons'),
							"description" => wp_kses_data( __("Select a thumb size to show images", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							"std" => apply_filters( 'trx_addons_filter_thumb_size',
													trx_addons_get_thumb_size( 'avatar' ),
													'trx_addons_widget_instagram',
													array()
												),
							"value" => array_flip( trx_addons_get_list_thumbnail_sizes() ),
							'dependency' => array(
								'element' => 'demo',
								'value' => '1'
							),
							"type" => "dropdown"
						),
						array(
							'type'        => 'param_group',
							'param_name'  => 'demo_files',
							'heading'     => esc_html__( 'Demo images', 'trx_addons' ),
							'description' => wp_kses_data( __( 'Specify values for each media item', 'trx_addons' ) ),
							'value'       => urlencode(
								json_encode(
									apply_filters(
										'trx_addons_sc_param_group_value', array(
											array(
												'title' => '',
												'image' => '',
												'video' => '',
											),
										), 'trx_widget_instagram'
									)
								)
							),
							'params'      => apply_filters(
								'trx_addons_sc_param_group_params', array_merge(
									array(
										array(
											'param_name'  => 'image',
											'heading'     => esc_html__( 'Image', 'trx_addons' ),
											'description' => wp_kses_data( __( 'Select or upload an image or write URL from other site', 'trx_addons' ) ),
											'admin_label' => true,
											'edit_field_class' => 'vc_col-sm-6',
											'type'        => 'attach_image',
										),
										array(
											"param_name" => "video",
											"heading" => esc_html__("Video URL", 'trx_addons'),
											"description" => wp_kses_data( __("Specify URL of the demo video", 'trx_addons') ),
											'admin_label' => true,
											'edit_field_class' => 'vc_col-sm-6',
											"type" => "textfield"
										),
									)
								), 'trx_widget_instagram'
							),
							'dependency' => array(
								'element' => 'demo',
								'value' => '1'
							),
						),
						array(
							"param_name" => "hashtag",
							"heading" => esc_html__("Hashtag or Username", 'trx_addons'),
							"description" => wp_kses_data( __("Hashtag to filter your photos", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							"class" => "",
							'dependency' => array(
								'element' => 'demo',
								'is_empty' => true
							),
							"value" => "",
							"type" => "textfield"
						),
						array(
							"param_name" => "count",
							"heading" => esc_html__("Number of photos", 'trx_addons'),
							"description" => wp_kses_data( __("How many photos to be displayed?", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							'dependency' => array(
								'element' => 'demo',
								'is_empty' => true
							),
							"class" => "",
							"value" => "8",
							"type" => "textfield"
						),
						array(
							"param_name" => "columns",
							"heading" => esc_html__("Columns", 'trx_addons'),
							"description" => wp_kses_data( __("Columns number", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							"class" => "",
							"value" => "4",
							"type" => "textfield"
						),
						array(
							"param_name" => "columns_gap",
							"heading" => esc_html__("Columns gap", 'trx_addons'),
							"description" => wp_kses_data( __("Gap between images", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							"class" => "",
							"value" => "0",
							"type" => "textfield"
						),
						array(
							"param_name" => "links",
							"heading" => esc_html__("Link images to", 'trx_addons'),
							"description" => wp_kses_data( __("Where to send a visitor after clicking on the picture", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							"class" => "",
							"std" => "instagram",
							"value" => array_flip(trx_addons_get_list_sc_instagram_redirects()),
							"type" => "dropdown"
						),
						array(
							"param_name" => "ratio",
							"heading" => esc_html__("Image ratio", 'trx_addons'),
							"description" => wp_kses_data( __("Select a ratio to show images. Default leave original ratio for each image", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							"class" => "",
							"std" => "none",
							"value" => array_flip( trx_addons_get_list_sc_image_ratio( false ) ),
							"type" => "dropdown"
						),
						array(
							"param_name" => "follow",
							"heading" => esc_html__('Show button "Follow me"', 'trx_addons'),
							"description" => wp_kses_data( __('Add button "Follow me" after images', 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							"std" => "1",
							'save_always' => true,
							"value" => array("Show Follow Me" => "1" ),
							"type" => "checkbox"
						),
						array(
							"param_name" => "follow_link",
							"heading" => esc_html__("Follow link", 'trx_addons'),
							"description" => wp_kses_data( __("URL for the Follow link", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							"class" => "",
							"value" => "",
							'dependency' => array(
								'element' => 'follow',
								'value' => '1'
							),
							"type" => "textfield"
						),
					),
					trx_addons_vc_add_id_param()
				)
			), 'trx_widget_instagram');
	}
}
