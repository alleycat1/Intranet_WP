<?php
/**
 * ThemeREX Addons Custom post type: Services (WPBakery support)
 *
 * @package ThemeREX Addons
 * @since v1.4
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


// Add [trx_sc_services] in the VC shortcodes list
if (!function_exists('trx_addons_sc_services_add_in_vc')) {
	function trx_addons_sc_services_add_in_vc() {
		
		if (!trx_addons_exists_vc()) return;
		
		vc_lean_map("trx_sc_services", 'trx_addons_sc_services_add_in_vc_params');
		class WPBakeryShortCode_Trx_Sc_Services extends WPBakeryShortCode {}
	}
	add_action('init', 'trx_addons_sc_services_add_in_vc', 20);
}

// Return params
if (!function_exists('trx_addons_sc_services_add_in_vc_params')) {
	function trx_addons_sc_services_add_in_vc_params() {
		// If open params in VC Editor
		list($vc_edit, $vc_params) = trx_addons_get_vc_form_params('trx_sc_services');
		// Prepare lists
		$post_type = $vc_edit && !empty($vc_params['post_type']) ? $vc_params['post_type'] : TRX_ADDONS_CPT_SERVICES_PT;
		$taxonomy = $vc_edit && !empty($vc_params['taxonomy']) ? $vc_params['taxonomy'] : TRX_ADDONS_CPT_SERVICES_TAXONOMY;
		$tax_obj = get_taxonomy($taxonomy);
		$params = array_merge(
				array(
					array(
						"param_name" => "type",
						"heading" => esc_html__("Layout", 'trx_addons'),
						"description" => wp_kses_data( __("Select shortcode's layout", 'trx_addons') ),
						"admin_label" => true,
						'edit_field_class' => 'vc_col-sm-6',
						"std" => "default",
				        'save_always' => true,
						"value" => array_flip(apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('cpt', 'services', 'sc'), 'trx_sc_services')),
						"type" => "dropdown"
					),
					array(
						"param_name" => "tabs_effect",
						"heading" => esc_html__("Tabs change effect", 'trx_addons'),
						"description" => wp_kses_data( __("Select the tabs change effect", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-6',
						'dependency' => array(
							'element' => 'type',
							'value' => 'tabs'
						),
						"std" => "fade",
				        'save_always' => true,
						"value" => array_flip(trx_addons_get_list_sc_services_tabs_effects()),
						"type" => "dropdown"
					),
					array(
						"param_name" => "featured",
						"heading" => esc_html__("Featured", 'trx_addons'),
						"description" => wp_kses_data( __("What to use as featured element?", 'trx_addons') ),
						"admin_label" => true,
						'edit_field_class' => 'vc_col-sm-6 vc_new_row',
						'dependency' => array(
							'element' => 'type',
							'value' => array('default', 'callouts', 'hover', 'light', 'list', 'iconed', 'tabs', 'tabs_simple', 'timeline')
						),
						"std" => "image",
				        'save_always' => true,
						"value" => array_flip(trx_addons_get_list_sc_services_featured()),
						"type" => "dropdown"
					),
					array(
						"param_name" => "featured_position",
						"heading" => esc_html__("Featured position", 'trx_addons'),
						"description" => wp_kses_data( __("Select the position of the featured element. Attention! Use 'Bottom' only with 'Callouts' or 'Timeline'", 'trx_addons') ),
						"admin_label" => true,
						'edit_field_class' => 'vc_col-sm-6',
						'dependency' => array(
							'element' => 'featured',
							'value' => array('image', 'icon', 'number', 'pictogram')
						),
						"std" => "top",
				        'save_always' => true,
						"value" => array_flip(trx_addons_get_list_sc_services_featured_positions()),
						"type" => "dropdown"
					),
					array(
						"param_name" => "no_links",
						"heading" => esc_html__("Disable links", 'trx_addons'),
						"description" => wp_kses_data( __("Check if you want disable links to the single posts", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4 vc_new_row',
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
						'edit_field_class' => 'vc_col-sm-4 vc_new_row',
						"std" => "0",
						"value" => array(esc_html__("Hide excerpt", 'trx_addons') => "1" ),
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
						"param_name" => "icons_animation",
						"heading" => esc_html__("Icons animation", 'trx_addons'),
						"description" => wp_kses_data( __("Attention! Animation is enabled only if there is an .SVG  icon in your theme with the same name as the selected icon.", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4',
						'dependency' => array(
							'element' => 'featured',
							'value' => array('icon')
						),
						"std" => "0",
						"value" => array(esc_html__("Animate icons", 'trx_addons') => "1" ),
						"type" => "checkbox"
					),
					array(
						"param_name" => "hide_bg_image",
						"heading" => esc_html__("Hide bg image", 'trx_addons'),
						"description" => wp_kses_data( __("Toggle to hide the background image on the front item.", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4',
						'dependency' => array(
							'element' => 'type',
							'value' => 'hover'
						),
						"std" => "0",
						"value" => array(esc_html__("Hide bg image", 'trx_addons') => "1" ),
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
						"param_name" => "post_type",
						"heading" => esc_html__("Post type", 'trx_addons'),
						"description" => wp_kses_data( __("Select post type to show posts", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4 vc_new_row',
						'group' => esc_html__('Query', 'trx_addons'),
						"std" => TRX_ADDONS_CPT_SERVICES_PT,
						"value" => array_flip(trx_addons_get_list_posts_types()),
						"type" => "dropdown"
					),
					array(
						"param_name" => "taxonomy",
						"heading" => esc_html__("Taxonomy", 'trx_addons'),
						"description" => wp_kses_data( __("Select taxonomy to show posts", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4',
						'group' => esc_html__('Query', 'trx_addons'),
						"std" => TRX_ADDONS_CPT_SERVICES_TAXONOMY,
						"value" => array_flip(trx_addons_get_list_taxonomies(false, $post_type)),
						"type" => "dropdown"
					),
					array(
						"param_name" => "cat",
						"heading" => esc_html__("Group", 'trx_addons'),
						"description" => wp_kses_data( __("Services group", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4',
						'group' => esc_html__('Query', 'trx_addons'),
						"value" => array_flip( trx_addons_array_merge(
													array( 0 => trx_addons_get_not_selected_text( ! empty( $tax_obj->label ) ? $tax_obj->label : __( '- Not Selected -', 'trx_addons' ) ) ),
													$taxonomy == 'category' 
														? trx_addons_get_list_categories() 
														: trx_addons_get_list_terms(false, $taxonomy)
													) ),
						"std" => "0",
						"type" => "dropdown"
					)
				),
				trx_addons_vc_add_query_param(),
				trx_addons_vc_add_slider_param(),
				trx_addons_vc_add_title_param(),
				trx_addons_vc_add_id_param()
		);
		
		// Add dependencies to params
		$params = trx_addons_vc_add_param_option($params, 'columns', array( 
																	'dependency' => array(
																		'element' => 'type',
																		'value' => array('default','callouts','light','list','iconed','hover','chess','timeline')
																		)
																	)
												);
		$params = trx_addons_vc_add_param_option($params, 'slider', array( 
																	'dependency' => array(
																		'element' => 'type',
																		'value' => array('default','callouts','light','list','iconed','hover','chess','timeline')
																		)
																	)
												);
												
		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_sc_services",
				"name" => esc_html__("Services", 'trx_addons'),
				"description" => wp_kses_data( __("Display services from specified group", 'trx_addons') ),
				"category" => esc_html__('ThemeREX', 'trx_addons'),
				"icon" => 'icon_trx_sc_services',
				"class" => "trx_sc_services",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => $params
			), 'trx_sc_services' );
	}
}
