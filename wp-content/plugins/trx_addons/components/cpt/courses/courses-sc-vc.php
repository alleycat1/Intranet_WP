<?php
/**
 * ThemeREX Addons Custom post type: Courses (WPBakery support)
 *
 * @package ThemeREX Addons
 * @since v1.2
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


// Add [trx_sc_courses] in the VC shortcodes list
if (!function_exists('trx_addons_sc_courses_add_in_vc')) {
	function trx_addons_sc_courses_add_in_vc() {
		
		if (!trx_addons_exists_vc()) return;

		vc_lean_map( "trx_sc_courses", 'trx_addons_sc_courses_add_in_vc_params');
		class WPBakeryShortCode_Trx_Sc_Courses extends WPBakeryShortCode {}
	}
	add_action('init', 'trx_addons_sc_courses_add_in_vc', 20);
}

// Return params
if (!function_exists('trx_addons_sc_courses_add_in_vc_params')) {
	function trx_addons_sc_courses_add_in_vc_params() {
		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_sc_courses",
				"name" => esc_html__("Courses", 'trx_addons'),
				"description" => wp_kses_data( __("Display courses from specified group", 'trx_addons') ),
				"category" => esc_html__('ThemeREX', 'trx_addons'),
				"icon" => 'icon_trx_sc_courses',
				"class" => "trx_sc_courses",
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
							"value" => array_flip(apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('cpt', 'courses', 'sc'), 'trx_sc_courses')),
							"type" => "dropdown"
						),
						array(
							"param_name" => "past",
							"heading" => esc_html__("Past courses", 'trx_addons'),
							"description" => wp_kses_data( __("Show the past courses if checked, otherwise - show upcoming courses.", 'trx_addons') ),
							"admin_label" => true,
							'edit_field_class' => 'vc_col-sm-6',
							"std" => "0",
							"value" => array(esc_html__("Show past courses", 'trx_addons') => "1" ),
							"type" => "checkbox"
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
							"std" => esc_html__('More info', 'trx_addons'),
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
							"param_name" => "cat",
							"heading" => esc_html__("Group", 'trx_addons'),
							"description" => wp_kses_data( __("Courses group", 'trx_addons') ),
							"value" => array_merge( array( trx_addons_get_not_selected_text( esc_html__( 'Select category', 'trx_addons' ) ) => 0 ), array_flip(trx_addons_get_list_terms( false, TRX_ADDONS_CPT_COURSES_TAXONOMY ) ) ),
							"std" => "0",
							"type" => "dropdown"
						)
					),
					trx_addons_vc_add_query_param(''),
					trx_addons_vc_add_slider_param(),
					trx_addons_vc_add_title_param(),
					trx_addons_vc_add_id_param()
				)
			), 'trx_sc_courses' );
	}
}
