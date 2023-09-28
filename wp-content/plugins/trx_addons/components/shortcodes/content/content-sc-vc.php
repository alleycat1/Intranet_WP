<?php
/**
 * Shortcode: Content container (WPBakery support)
 *
 * @package ThemeREX Addons
 * @since v1.2
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}



// Add [trx_sc_content] in the VC shortcodes list
if (!function_exists('trx_addons_sc_content_add_in_vc')) {
	function trx_addons_sc_content_add_in_vc() {

		if (!trx_addons_exists_vc()) return;
		
		vc_lean_map("trx_sc_content", 'trx_addons_sc_content_add_in_vc_params');
		class WPBakeryShortCode_Trx_Sc_Content extends WPBakeryShortCodesContainer {}
		
		vc_lean_map("trx_sc_content_inner", 'trx_addons_sc_content_inner_add_in_vc_params');
		class WPBakeryShortCode_Trx_Sc_Content_Inner extends WPBakeryShortCodesContainer {}
		
	}
	add_action('init', 'trx_addons_sc_content_add_in_vc', 20);
}

// Return params for 'section'
if (!function_exists('trx_addons_sc_content_inner_add_in_vc_params')) {
	function trx_addons_sc_content_inner_add_in_vc_params() {
		return trx_addons_sc_content_add_in_vc_params('content_inner');
	}
}

// Return params
if (!function_exists('trx_addons_sc_content_add_in_vc_params')) {
	function trx_addons_sc_content_add_in_vc_params($type='content') {
		$args = apply_filters('trx_addons_sc_map', array(
				"base" => "trx_sc_content",
				"name" => esc_html__("Content area", 'trx_addons'),
				"description" => wp_kses_data( __("Limit content width inside the fullwide rows", 'trx_addons') ),
				"category" => esc_html__('ThemeREX', 'trx_addons'),
				"icon" => 'icon_trx_sc_content',
				"class" => "trx_sc_content",
				'content_element' => true,
				'is_container' => true,
				'as_child' => array('except' => 'trx_sc_content,trx_sc_content_inner'),
				"js_view" => 'VcTrxAddonsContainerView',	//'VcColumnView',
				"show_settings_on_create" => true,
				"params" => array_merge(
					array(
						array(
							"param_name" => "type",
							"heading" => esc_html__("Layout", 'trx_addons'),
							"description" => wp_kses_data( __("Select shortcode's layout", 'trx_addons') ),
							"admin_label" => true,
							'edit_field_class' => 'vc_col-sm-6',
							"std" => "default",
							"value" => array_flip(apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('sc', 'content'), 'trx_sc_content')),
							"type" => "dropdown"
						),
						array(
							"param_name" => "size",	// Attention! Param 'width' is reserved by VC
							"heading" => esc_html__("Size", 'trx_addons'),
							"description" => wp_kses_data( __("Select size of the block", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							"admin_label" => true,
					        'save_always' => true,
							"value" => array_flip(trx_addons_get_list_sc_content_widths()),
							"std" => "none",
							"type" => "dropdown"
						),
						array(
							"param_name" => "paddings",
							"heading" => esc_html__("Inner paddings", 'trx_addons'),
							"description" => wp_kses_data( __("Select paddings around of the inner text in the block", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							"value" => array_flip(trx_addons_get_list_sc_content_paddings_and_margins()),
							"std" => "none",
							"type" => "dropdown"
						),
						array(
							"param_name" => "margins",
							"heading" => esc_html__("Outer margin", 'trx_addons'),
							"description" => wp_kses_data( __("Select margin around of the block", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							"value" => array_flip(trx_addons_get_list_sc_content_paddings_and_margins()),
							"std" => "none",
							"type" => "dropdown"
						),
						array(
							"param_name" => "float",
							"heading" => esc_html__("Block alignment", 'trx_addons'),
							"description" => wp_kses_data( __("Select alignment (floating position) of the block", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							"admin_label" => true,
							"value" => array_flip(trx_addons_get_list_sc_aligns()),
							"std" => "none",
							"type" => "dropdown"
						),
						array(
							"param_name" => "align",
							"heading" => esc_html__("Text alignment", 'trx_addons'),
							"description" => wp_kses_data( __("Select alignment of the inner text in the block", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							"admin_label" => true,
							"value" => array_flip(trx_addons_get_list_sc_aligns(false, true, true)),
							"std" => "none",
							"type" => "dropdown"
						),
						array(
							"param_name" => "push",
							"heading" => esc_html__("Push block up", 'trx_addons'),
							"description" => wp_kses_data( __("Push this block up, so that it partially covers the previous block", 'trx_addons') ),
							"group" => esc_html__('Push & Pull', 'trx_addons'),
							'edit_field_class' => 'vc_col-sm-6 vc_new_row',
							"value" => array_flip(trx_addons_get_list_sc_content_shift()),
							"std" => "none",
							"type" => "dropdown"
						),
						array(
							"param_name" => "push_hide_on_tablet",
							"heading" => esc_html__("On tablet", 'trx_addons'),
							"description" => wp_kses_data( __("Disable push on the tablets", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-3',
							"group" => esc_html__('Push & Pull', 'trx_addons'),
							"std" => "0",
							'dependency' => array(
								'element' => 'push',
								'value' => array('tiny', 'tiny_negative', 'small', 'small_negative', 'medium', 'medium_negative', 'large', 'large_negative')
							),
							"value" => array(esc_html__("Disable on tablet", 'trx_addons') => "1" ),
							"type" => "checkbox"
						),
						array(
							"param_name" => "push_hide_on_mobile",
							"heading" => esc_html__("On mobile", 'trx_addons'),
							"description" => wp_kses_data( __("Disable push on the mobile", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-3',
							"group" => esc_html__('Push & Pull', 'trx_addons'),
							"std" => "0",
							'dependency' => array(
								'element' => 'push',
								'value' => array('tiny', 'tiny_negative', 'small', 'small_negative', 'medium', 'medium_negative', 'large', 'large_negative')
							),
							"value" => array(esc_html__("Disable on mobile", 'trx_addons') => "1" ),
							"type" => "checkbox"
						),
						array(
							"param_name" => "pull",
							"heading" => esc_html__("Pull next block up", 'trx_addons'),
							"description" => wp_kses_data( __("Pull next block up, so that it partially covers this block", 'trx_addons') ),
							"group" => esc_html__('Push & Pull', 'trx_addons'),
							'edit_field_class' => 'vc_col-sm-6 vc_new_row',
							"value" => array_flip(trx_addons_get_list_sc_content_shift()),
							"std" => "none",
							"type" => "dropdown"
						),
						array(
							"param_name" => "pull_hide_on_tablet",
							"heading" => esc_html__("On tablet", 'trx_addons'),
							"description" => wp_kses_data( __("Disable pull on the tablets", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-3',
							"group" => esc_html__('Push & Pull', 'trx_addons'),
							"std" => "0",
							'dependency' => array(
								'element' => 'pull',
								'value' => array('^none')
							),
							"value" => array(esc_html__("Disable on tablet", 'trx_addons') => "1" ),
							"type" => "checkbox"
						),
						array(
							"param_name" => "pull_hide_on_mobile",
							"heading" => esc_html__("On mobile", 'trx_addons'),
							"description" => wp_kses_data( __("Disable pull on the mobile", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-3',
							"group" => esc_html__('Push & Pull', 'trx_addons'),
							"std" => "0",
							'dependency' => array(
								'element' => 'pull',
								'value' => array('^none')
							),
							"value" => array(esc_html__("Disable on mobile", 'trx_addons') => "1" ),
							"type" => "checkbox"
						),
						array(
							"param_name" => "shift_x",
							"heading" => esc_html__("The X-axis shift", 'trx_addons'),
							"description" => wp_kses_data( __("Shift this block along the X-axis", 'trx_addons') ),
							"group" => esc_html__('Push & Pull', 'trx_addons'),
							'edit_field_class' => 'vc_col-sm-6 vc_new_row',
							"value" => array_flip(trx_addons_get_list_sc_content_shift()),
							"std" => "none",
							"type" => "dropdown"
						),
						array(
							"param_name" => "shift_y",
							"heading" => esc_html__("The Y-axis shift", 'trx_addons'),
							"description" => wp_kses_data( __("Shift this block along the Y-axis", 'trx_addons') ),
							"group" => esc_html__('Push & Pull', 'trx_addons'),
							'edit_field_class' => 'vc_col-sm-6',
							"value" => array_flip(trx_addons_get_list_sc_content_shift()),
							"std" => "none",
							"type" => "dropdown"
						),
						array(
							"param_name" => "number",
							"heading" => esc_html__("Number", 'trx_addons'),
							"description" => wp_kses_data( __("Number to display in the corner of this area", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							"group" => esc_html__('Number', 'trx_addons'),
							"type" => "textfield"
						),
						array(
							"param_name" => "number_position",
							"heading" => esc_html__("Number position", 'trx_addons'),
							"description" => wp_kses_data( __("Select position to display number", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							"group" => esc_html__('Number', 'trx_addons'),
							"std" => "br",
					        'save_always' => true,
							"value" => array_flip(trx_addons_get_list_sc_positions()),
							"type" => "dropdown"
						),
						array(
							'param_name' => 'number_color',
							'heading' => esc_html__( 'Color of the number', 'trx_addons' ),
							'description' => esc_html__( 'Select custom color of the number', 'trx_addons' ),
							"group" => esc_html__('Number', 'trx_addons'),
							'type' => 'colorpicker'
						)
					),
					trx_addons_vc_add_title_param(),
					trx_addons_vc_add_id_param(),
					array(
						array(
							"param_name" => "extra_bg",
							"heading" => esc_html__("Entended background", 'trx_addons'),
							"description" => wp_kses_data( __("Extend background of this block", 'trx_addons') ),
							"group" => esc_html__('Design Options', 'trx_addons'),
							'edit_field_class' => 'vc_col-sm-6',
							"value" => array_flip(trx_addons_get_list_sc_content_extra_bg()),
							"std" => "none",
							"type" => "dropdown"
						),
						array(
							"param_name" => "extra_bg_mask",
							"heading" => esc_html__("Background mask", 'trx_addons'),
							"description" => wp_kses_data( __("Specify opacity of the background color to use it as mask for the background image", 'trx_addons') ),
							"group" => esc_html__('Design Options', 'trx_addons'),
							'edit_field_class' => 'vc_col-sm-6',
							"value" => array_flip(trx_addons_get_list_sc_content_extra_bg_mask()),
							"std" => "none",
							"type" => "dropdown"
						)
					)
				)
			), 'trx_sc_content' );
		if ($type == 'content_inner') {
			$args['base'] = 'trx_sc_content_inner';
			$args['name'] = esc_html__("Content area (inner)", 'trx_addons');
			$args['description'] = wp_kses_data( __("Inner content area (used inside other content area)", 'trx_addons') );
			$args['as_child'] = array('only' => 'trx_sc_content,vc_column_inner');
		}
		return $args;
	}
}
