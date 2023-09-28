<?php
/**
 * ThemeREX Addons Custom post type: Team (WPBakery support)
 *
 * @package ThemeREX Addons
 * @since v1.2
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


// Add [trx_sc_team] in the VC shortcodes list
if (!function_exists('trx_addons_sc_team_add_in_vc')) {
	function trx_addons_sc_team_add_in_vc() {
		
		if (!trx_addons_exists_vc()) return;
		
		vc_lean_map("trx_sc_team", 'trx_addons_sc_team_add_in_vc_params');
		class WPBakeryShortCode_Trx_Sc_Team extends WPBakeryShortCode {}
	}
	add_action('init', 'trx_addons_sc_team_add_in_vc', 20);
}

// Return params
if (!function_exists('trx_addons_sc_team_add_in_vc_params')) {
	function trx_addons_sc_team_add_in_vc_params() {
		// If open params in VC Editor
		list($vc_edit, $vc_params) = trx_addons_get_vc_form_params('trx_sc_team');
		// Prepare lists
		$post_type = $vc_edit && !empty($vc_params['post_type']) ? $vc_params['post_type'] : TRX_ADDONS_CPT_TEAM_PT;
		$taxonomy = $vc_edit && !empty($vc_params['taxonomy']) ? $vc_params['taxonomy'] : TRX_ADDONS_CPT_TEAM_TAXONOMY;
		$tax_obj = get_taxonomy($taxonomy);
		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_sc_team",
				"name" => esc_html__("Team", 'trx_addons'),
				"description" => wp_kses_data( __("Display team members from specified group", 'trx_addons') ),
				"category" => esc_html__('ThemeREX', 'trx_addons'),
				"icon" => 'icon_trx_sc_team',
				"class" => "trx_sc_team",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array_merge(
					array(
						array(
							"param_name" => "type",
							"heading" => esc_html__("Layout", 'trx_addons'),
							"description" => wp_kses_data( __("Select shortcode's layout", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							"admin_label" => true,
							"std" => "default",
					        'save_always' => true,
							"value" => array_flip(apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('cpt', 'team', 'sc'), 'trx_sc_team')),
							"type" => "dropdown"
						),
						array(
							"param_name" => "pagination",
							"heading" => esc_html__("Pagination", 'trx_addons'),
							"description" => wp_kses_data( __("Add pagination links after posts. Attention! Pagination is not allowed if the slider layout is used.", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							"std" => 'none',
							"value" => array_flip(trx_addons_get_list_sc_paginations()),
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
							"param_name" => "no_links",
							"heading" => esc_html__("Disable links", 'trx_addons'),
							"description" => wp_kses_data( __("Check if you want disable links to the single posts", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"std" => "0",
							"value" => array(esc_html__("Disable links", 'trx_addons') => "1" ),
							"type" => "checkbox"
						),
						array(
							"param_name" => "more_text",
							"heading" => esc_html__("'More' text", 'trx_addons'),
							"description" => wp_kses_data( __("Specify caption of the 'Read more' button. If empty - hide button", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							'dependency' => array(
								'element' => 'no_links',
								'is_empty' => true
							),
							"std" => esc_html__('Read more', 'trx_addons'),
							"type" => "textfield"
						),
						array(
							"param_name" => "post_type",
							"heading" => esc_html__("Post type", 'trx_addons'),
							"description" => wp_kses_data( __("Select post type to show posts", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4 vc_new_row',
							'group' => esc_html__('Query', 'trx_addons'),
							"std" => TRX_ADDONS_CPT_TEAM_PT,
							"value" => array_flip(trx_addons_get_list_team_posts_types()),
							"type" => "dropdown"
						),
						array(
							"param_name" => "taxonomy",
							"heading" => esc_html__("Taxonomy", 'trx_addons'),
							"description" => wp_kses_data( __("Select taxonomy to show posts", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							'group' => esc_html__('Query', 'trx_addons'),
							"std" => TRX_ADDONS_CPT_TEAM_TAXONOMY,
							"value" => array_flip(trx_addons_get_list_taxonomies(false, $post_type)),
							"type" => "dropdown"
						),
						array(
							"param_name" => "cat",
							"heading" => esc_html__("Group", 'trx_addons'),
							"description" => wp_kses_data( __("Team group", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"value" => array_flip( trx_addons_array_merge(
														array( 0 => trx_addons_get_not_selected_text( $tax_obj->label ) ),
														$taxonomy == 'category' 
															? trx_addons_get_list_categories() 
															: trx_addons_get_list_terms(false, $taxonomy)
														) ),
							"std" => "0",
							"type" => "dropdown"
						)
					),
					trx_addons_vc_add_query_param(''),
					trx_addons_vc_add_slider_param(),
					trx_addons_vc_add_title_param(),
					trx_addons_vc_add_id_param()
				)
			), 'trx_sc_team' );
	}
}
