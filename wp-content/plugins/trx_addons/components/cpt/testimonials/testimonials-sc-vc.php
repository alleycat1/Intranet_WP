<?php
/**
 * ThemeREX Addons Custom post type: Testimonials (WPBakery support)
 *
 * @package ThemeREX Addons
 * @since v1.2
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


// Add [trx_sc_testimonials] in the VC shortcodes list
if (!function_exists('trx_addons_sc_testimonials_add_in_vc')) {
	function trx_addons_sc_testimonials_add_in_vc() {
		
		if (!trx_addons_exists_vc()) return;

		vc_lean_map("trx_sc_testimonials", 'trx_addons_sc_testimonials_add_in_vc_params');
		class WPBakeryShortCode_Trx_Sc_Testimonials extends WPBakeryShortCode {}
	}
	add_action('init', 'trx_addons_sc_testimonials_add_in_vc', 20);
}

// Return params
if (!function_exists('trx_addons_sc_testimonials_add_in_vc_params')) {
	function trx_addons_sc_testimonials_add_in_vc_params() {
		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_sc_testimonials",
				"name" => esc_html__("Testimonials", 'trx_addons'),
				"description" => wp_kses_data( __("Display testimonials from specified group", 'trx_addons') ),
				"category" => esc_html__('ThemeREX', 'trx_addons'),
				"icon" => 'icon_trx_sc_testimonials',
				"class" => "trx_sc_testimonials",
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
							"std" => "default",
					        'save_always' => true,
							"value" => array_flip(apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('cpt', 'testimonials', 'sc'), 'trx_sc_testimonials')),
							"type" => "dropdown"
						),
						array(
							"param_name" => "rating",
							"heading" => esc_html__("Show rating", 'trx_addons'),
							"admin_label" => true,
							"std" => 0,
							"value" => array(esc_html__("Display rating stars", 'trx_addons') => 1 ),
							"type" => "checkbox"
						),
						array(
							"param_name" => "use_initials",
							"heading" => esc_html__("Use initials", 'trx_addons'),
							"description" => esc_html__("If no avatar is present, the initials derived from the available username will be used.", 'trx_addons'),
							"admin_label" => true,
							'edit_field_class' => 'vc_col-sm-12',
							"std" => 0,
							"value" => array(esc_html__("Use Initials", 'trx_addons') => 1 ),
							"type" => "checkbox"
						),
						array(
							"param_name" => "cat",
							"heading" => esc_html__("Group", 'trx_addons'),
							"description" => wp_kses_data( __("Testimonials group", 'trx_addons') ),
							"value" => array_merge( array( trx_addons_get_not_selected_text( esc_html__( 'Select category', 'trx_addons' ) ) => 0 ), array_flip( trx_addons_get_list_terms( false, TRX_ADDONS_CPT_TESTIMONIALS_TAXONOMY ) ) ),
							"std" => "0",
							"type" => "dropdown"
						)
					),
					trx_addons_vc_add_query_param(''),
					trx_addons_vc_add_slider_param(),
					array(
						array(
							"param_name" => "slider_pagination_thumbs",
							"heading" => esc_html__("Slider pagination", 'trx_addons'),
							"description" => wp_kses_data( __("Show thumbs as pagination bullets", 'trx_addons') ),
							'dependency' => array(
								'element' => 'slider_pagination',
								'value' => array('left', 'right', 'bottom', 'bottom_outside')
							),
							"group" => esc_html__('Slider', 'trx_addons'),
							"std" => "0",
							"value" => array(esc_html__("Pagination thumbs", 'trx_addons') => "1" ),
							"type" => "checkbox"
						)
					),
					trx_addons_vc_add_title_param(),
					trx_addons_vc_add_id_param()
				)
			), 'trx_sc_testimonials' );
	}
}
