<?php
/**
 * Shortcode: Promo block (WPBakery support)
 *
 * @package ThemeREX Addons
 * @since v1.2
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}



// Add [trx_sc_promo] in the VC shortcodes list
if (!function_exists('trx_addons_sc_promo_add_in_vc')) {
	function trx_addons_sc_promo_add_in_vc() {
		
		if (!trx_addons_exists_vc()) return;
		
		vc_lean_map("trx_sc_promo", 'trx_addons_sc_promo_add_in_vc_params');
		class WPBakeryShortCode_Trx_Sc_Promo extends WPBakeryShortCodesContainer {}
	}
	add_action('init', 'trx_addons_sc_promo_add_in_vc', 20);
}

// Return params
if (!function_exists('trx_addons_sc_promo_add_in_vc_params')) {
	function trx_addons_sc_promo_add_in_vc_params() {
		return apply_filters('trx_addons_sc_map', array(
			"base" => "trx_sc_promo",
			"name" => esc_html__("Promo", 'trx_addons'),
			"description" => wp_kses_data( __("Insert promo block", 'trx_addons') ),
			"category" => esc_html__('ThemeREX', 'trx_addons'),
			'icon' => 'icon_trx_sc_promo',
			"class" => "trx_sc_promo",
			'content_element' => true,
			'is_container' => true,
			'as_child' => array('except' => 'trx_sc_promo'),
			"js_view" => 'VcTrxAddonsContainerView',	//'VcColumnView',
			"show_settings_on_create" => true,
			"params" => array_merge(
				array(
					array(
						"param_name" => "type",
						"heading" => esc_html__("Layout", 'trx_addons'),
						"description" => wp_kses_data( __("Select shortcodes's layout", 'trx_addons') ),
						"admin_label" => true,
						"std" => "default",
						"value" => array_flip(apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('sc', 'promo'), 'trx_sc_promo')),
						"type" => "dropdown"
					)
				),
				trx_addons_vc_add_icon_param(),
				trx_addons_vc_add_title_param(''),
				array(
					array(
						"param_name" => "link2",
						"heading" => esc_html__("Button 2 URL", 'trx_addons'),
						"description" => wp_kses_data( __("URL for the second button (at the side of the image)", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4 vc_new_row',
						'dependency' => array(
							'element' => 'type',
							'value' => 'modern'
						),
						"type" => "textfield"
					),
					array(
						"param_name" => "link2_text",
						"heading" => esc_html__("Button 2 text", 'trx_addons'),
						"description" => wp_kses_data( __("Caption for the second button (at the side of the image)", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4',
						'dependency' => array(
							'element' => 'type',
							'value' => 'modern'
						),
						"type" => "textfield"
					),
					array(
						"param_name" => "link2_style",
						"heading" => esc_html__("Button 2 style", 'trx_addons'),
						"description" => wp_kses_data( __("Select the style (layout) of the second button", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4',
						'dependency' => array(
							'element' => 'type',
							'value' => 'modern'
						),
				        'save_always' => true,
						"std" => "default",
						"value" => array_flip(apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('sc', 'button'), 'trx_sc_button')),
						"type" => "dropdown"
					),
					array(
						'param_name' => 'icon_color',
						'heading' => esc_html__( 'Icon color', 'trx_addons' ),
						'description' => esc_html__( 'Select icon color', 'trx_addons' ),
						'edit_field_class' => 'vc_col-sm-6',
						'type' => 'colorpicker',
					),
					array(
						'param_name' => 'text_bg_color',
						'heading' => esc_html__( 'Text bg color', 'trx_addons' ),
						'description' => esc_html__( 'Select custom color, used as background of the text area', 'trx_addons' ),
						'edit_field_class' => 'vc_col-sm-6',
						'type' => 'colorpicker',
					),
					array(
						"param_name" => "image",
						"heading" => esc_html__("Image", 'trx_addons'),
						"description" => wp_kses_data( __("Select the promo image from the library for this section. Show slider if you select 2+ images", 'trx_addons') ),
						"group" => esc_html__('Image', 'trx_addons'),
						"type" => "attach_images"
					),
					array(
						'param_name' => 'image_bg_color',
						'heading' => esc_html__( 'Image bg color', 'trx_addons' ),
						'description' => esc_html__( 'Select custom color, used as background of the image', 'trx_addons' ),
						'dependency' => array(
							'element' => 'image',
							'not_empty' => true
						),
						"group" => esc_html__('Image', 'trx_addons'),
						'edit_field_class' => 'vc_col-sm-6',
						'type' => 'colorpicker',
					),
					array(
						"param_name" => "image_cover",
						"heading" => esc_html__("Image cover area", 'trx_addons'),
						"description" => wp_kses_data( __("Fit an image into the area or cover it.", 'trx_addons') ),
						'dependency' => array(
							'element' => 'image',
							'not_empty' => true
						),
						"group" => esc_html__('Image', 'trx_addons'),
						'edit_field_class' => 'vc_col-sm-6',
						"std" => "1",
						"value" => array(esc_html__("Image cover area", 'trx_addons') => "1" ),
						"type" => "checkbox"
					),
					array(
						"param_name" => "image_position",
						"heading" => esc_html__("Image position", 'trx_addons'),
						"description" => wp_kses_data( __("Place the image to the left or to the right from the text block", 'trx_addons') ),
						'dependency' => array(
							'element' => 'image',
							'not_empty' => true
						),
						"group" => esc_html__('Image', 'trx_addons'),
						'edit_field_class' => 'vc_col-sm-6',
						"value" => array_flip(trx_addons_get_list_sc_promo_positions()),
				        'save_always' => true,
						"std" => "left",
						"type" => "dropdown"
					),
					array(
						"param_name" => "image_width",
						"heading" => esc_html__("Image width", 'trx_addons'),
						"description" => wp_kses_data( __("Specify width of the image. If left empty or assigned the value '0', the columns will be equal.", 'trx_addons') ),
						'dependency' => array(
							'element' => 'image',
							'not_empty' => true
						),
						"group" => esc_html__('Image', 'trx_addons'),
						'edit_field_class' => 'vc_col-sm-6',
						"value" => "50%",
						"type" => "textfield"
					),
					array(
						'param_name' => 'video_url',
						'heading' => esc_html__( 'Video URL', 'trx_addons' ),
						'description' => esc_html__( 'Enter link to the video (Note: read more about available formats at WordPress Codex page)', 'trx_addons' ),
						'dependency' => array(
							'element' => 'image',
							'not_empty' => true
						),
						"group" => esc_html__('Image', 'trx_addons'),
						'edit_field_class' => 'vc_col-sm-6',
						'type' => 'textfield'
					),
					array(
						'param_name' => 'video_embed',
						'heading' => esc_html__( 'Video embed code', 'trx_addons' ),
						'description' => esc_html__( 'or paste the HTML code to embed video in this block', 'trx_addons' ),
						'dependency' => array(
							'element' => 'image',
							'not_empty' => true
						),
						"group" => esc_html__('Image', 'trx_addons'),
						'edit_field_class' => 'vc_col-sm-6',
						'type' => 'textarea'
					),
					array(
						"param_name" => "video_in_popup",
						"heading" => esc_html__("Video in the popup", 'trx_addons'),
						"description" => wp_kses_data( __("Open video in the popup window or insert it instead the cover image", 'trx_addons') ),
						'dependency' => array(
							'element' => 'image',
							'not_empty' => true
						),
						"group" => esc_html__('Image', 'trx_addons'),
						"std" => "0",
						"value" => array(esc_html__("Video in the popup", 'trx_addons') => "1" ),
						"type" => "checkbox"
					),
					array(
						"param_name" => "size",
						"heading" => esc_html__("Size", 'trx_addons'),
						"description" => wp_kses_data( __("Size of the promo block: normal - one in the row, tiny - only image and title, small - insize two or greater columns, large - fullscreen height", 'trx_addons') ),
						"group" => esc_html__('Dimensions', 'trx_addons'),
						'edit_field_class' => 'vc_col-sm-6',
						"admin_label" => true,
				        'save_always' => true,
						"value" => array_flip(trx_addons_get_list_sc_promo_sizes()),
						"std" => "normal",
						"type" => "dropdown"
					),
					array(
						"param_name" => "full_height",
						"heading" => esc_html__("Full height", 'trx_addons'),
						"description" => wp_kses_data( __("Stretch the height of the element to the full screen's height", 'trx_addons') ),
						"admin_label" => true,
						"group" => esc_html__('Dimensions', 'trx_addons'),
						'edit_field_class' => 'vc_col-sm-6',
						"std" => "0",
						"value" => array(esc_html__("Full Height", 'trx_addons') => "1" ),
						"type" => "checkbox"
					),
					array(
						"param_name" => "text_width",
						"heading" => esc_html__("Text width", 'trx_addons'),
						"description" => wp_kses_data( __("Select width of the text block", 'trx_addons') ),
						"group" => esc_html__('Dimensions', 'trx_addons'),
						"admin_label" => true,
						'edit_field_class' => 'vc_col-sm-4',
						"value" => array_flip(trx_addons_get_list_sc_promo_widths()),
						"std" => "none",
						"type" => "dropdown"
					),
					array(
						"param_name" => "text_float",
						"heading" => esc_html__("Text block floating", 'trx_addons'),
						"description" => wp_kses_data( __("Select alignment (floating position) of the text block", 'trx_addons') ),
						"group" => esc_html__('Dimensions', 'trx_addons'),
						"admin_label" => true,
						'edit_field_class' => 'vc_col-sm-4',
						"value" => array_flip(trx_addons_get_list_sc_aligns()),
						"std" => "none",
						"type" => "dropdown"
					),
					array(
						"param_name" => "text_align",
						"heading" => esc_html__("Text alignment", 'trx_addons'),
						"description" => wp_kses_data( __("Align text to the left or to the right side inside the block", 'trx_addons') ),
						"group" => esc_html__('Dimensions', 'trx_addons'),
						'edit_field_class' => 'vc_col-sm-4',
						"value" => array_flip(trx_addons_get_list_sc_aligns()),
						"std" => "none",
						"type" => "dropdown"
					),
					array(
						"param_name" => "text_paddings",
						"heading" => esc_html__("Text paddings", 'trx_addons'),
						"description" => wp_kses_data( __("Add horizontal paddings from the text block", 'trx_addons') ),
						"group" => esc_html__('Dimensions', 'trx_addons'),
						'edit_field_class' => 'vc_col-sm-4',
						"std" => "0",
						"value" => array(esc_html__("With paddings", 'trx_addons') => "1" ),
						"type" => "checkbox"
					),
					array(
						"param_name" => "text_margins",
						"heading" => esc_html__("Text margins", 'trx_addons'),
						"description" => wp_kses_data( __("Margins for the all sides of the text block (Example: 30px 10px 40px 30px = top right botton left OR 30px = equal for all sides)", 'trx_addons') ),
						"group" => esc_html__('Dimensions', 'trx_addons'),
						'edit_field_class' => 'vc_col-sm-4',
						"type" => "textfield"
					),
					array(
						"param_name" => "gap",
						"heading" => esc_html__("Gap", 'trx_addons'),
						"description" => wp_kses_data( __("Gap between text and image (in percent)", 'trx_addons') ),
						"group" => esc_html__('Dimensions', 'trx_addons'),
						'edit_field_class' => 'vc_col-sm-4',
						"admin_label" => true,
						"type" => "textfield"
					)
				),
				trx_addons_vc_add_id_param()
			)

		), 'trx_sc_promo' );
	}
}
