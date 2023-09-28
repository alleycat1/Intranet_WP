<?php
/**
 * ThemeREX Addons Custom post type: Dishes (WPBakery support)
 *
 * @package ThemeREX Addons
 * @since v1.6.09
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}




// Add [trx_sc_dishes] in the VC shortcodes list
if (!function_exists('trx_addons_sc_dishes_add_in_vc')) {
	function trx_addons_sc_dishes_add_in_vc() {

		if (!trx_addons_exists_vc()) return;

		vc_lean_map( "trx_sc_dishes", 'trx_addons_sc_dishes_add_in_vc_params');
		class WPBakeryShortCode_Trx_Sc_Dishes extends WPBakeryShortCode {}

	}
	add_action('init', 'trx_addons_sc_dishes_add_in_vc', 20);
}

// Return params
if (!function_exists('trx_addons_sc_dishes_add_in_vc_params')) {
	function trx_addons_sc_dishes_add_in_vc_params() {
		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_sc_dishes",
				"name" => esc_html__("Dishes", 'trx_addons'),
				"description" => wp_kses_data( __("Display dishes from specified group", 'trx_addons') ),
				"category" => esc_html__('ThemeREX', 'trx_addons'),
				"icon" => 'icon_trx_sc_dishes',
				"class" => "trx_sc_dishes",
				"content_element" => true,
				"is_container" => false,
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
					        'save_always' => true,
							"value" => array_flip(apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('cpt', 'dishes', 'sc'), 'trx_sc_dishes')),
							"type" => "dropdown"
						),
						array(
							"param_name" => "featured_position",
							"heading" => esc_html__("Featured position", 'trx_addons'),
							"description" => wp_kses_data( __("Select the position of the featured element", 'trx_addons') ),
							"admin_label" => true,
							'edit_field_class' => 'vc_col-sm-6',
							"std" => "top",
					        'save_always' => true,
							"value" => array_flip(trx_addons_get_list_sc_dishes_positions()),
							"type" => "dropdown"
						),
						array(
							"param_name" => "no_margin",
							"heading" => esc_html__("Remove margin", 'trx_addons'),
							"description" => wp_kses_data( __("Check if you want remove spaces between columns", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"std" => "0",
							"value" => array(esc_html__("Remove margin", 'trx_addons') => "1" ),
							"type" => "checkbox"
						),
						array(
							"param_name" => "more_text",
							"heading" => esc_html__("'More' text", 'trx_addons'),
							"description" => wp_kses_data( __("Specify caption of the 'Read more' button. If empty - hide button", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"std" => esc_html__('Read more', 'trx_addons'),
							"type" => "textfield"
						),
						array(
							"param_name" => "pagination",
							"heading" => esc_html__("Pagination", 'trx_addons'),
							"description" => wp_kses_data( __("Add pagination links after posts. Attention! Pagination is not allowed if the slider layout is used.", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"std" => 'none',
							"value" => array_flip(trx_addons_get_list_sc_paginations()),
							"type" => "dropdown"
						),
						array(
							"param_name" => "hide_excerpt",
							"heading" => esc_html__("Excerpt", 'trx_addons'),
							"description" => wp_kses_data( __("Toggle this option to hide the excerpt.", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							'dependency' => array(
								'element' => 'type',
								'value' => array('default', 'float')
							),
							"std" => "0",
							"value" => array(esc_html__("Hide excerpt", 'trx_addons') => "1" ),
							"type" => "checkbox"
						),
						array(
							"param_name" => "popup",
							"heading" => esc_html__("Open in the popup", 'trx_addons'),
							"description" => wp_kses_data( __("Open details in the popup or navigate to the single post (default)", 'trx_addons') ),
							"admin_label" => true,
							'edit_field_class' => 'vc_col-sm-4',
							"std" => "0",
							"value" => array(esc_html__("Popup", 'trx_addons') => "1" ),
							"type" => "checkbox"
						),
						array(
							"param_name" => "cat",
							"heading" => esc_html__("Group", 'trx_addons'),
							"description" => wp_kses_data( __("Dishes group", 'trx_addons') ),
							"value" => array_merge( array( trx_addons_get_not_selected_text( esc_html__( 'Select category', 'trx_addons' ) ) => 0 ), array_flip( trx_addons_get_list_terms( false, TRX_ADDONS_CPT_DISHES_TAXONOMY ) ) ),
							"std" => "0",
							"type" => "dropdown"
						)
					),
					trx_addons_vc_add_query_param(''),
					trx_addons_vc_add_slider_param(),
					trx_addons_vc_add_title_param(),
					trx_addons_vc_add_id_param()
				)
			), 'trx_sc_dishes' );
	}
}
