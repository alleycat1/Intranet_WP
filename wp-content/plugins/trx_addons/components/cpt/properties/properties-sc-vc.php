<?php
/**
 * ThemeREX Addons Custom post type: Properties (WPBakery support)
 *
 * @package ThemeREX Addons
 * @since v1.6.22
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


// Add [trx_sc_properties] in the VC shortcodes list
if (!function_exists('trx_addons_sc_properties_add_in_vc')) {
	function trx_addons_sc_properties_add_in_vc() {
		
		if (!trx_addons_exists_vc()) return;
		
		vc_lean_map("trx_sc_properties", 'trx_addons_sc_properties_add_in_vc_params');
		class WPBakeryShortCode_Trx_Sc_Properties extends WPBakeryShortCode {}
	}
	add_action('init', 'trx_addons_sc_properties_add_in_vc', 20);
}

// Return params
if (!function_exists('trx_addons_sc_properties_add_in_vc_params')) {
	function trx_addons_sc_properties_add_in_vc_params() {
		// If open params in VC Editor
		list($vc_edit, $vc_params) = trx_addons_get_vc_form_params('trx_sc_properties');
		// Prepare lists
		$country = $vc_edit && !empty($vc_params['properties_country']) ? $vc_params['properties_country'] : 0;
		$state = $vc_edit && !empty($vc_params['properties_state']) ? $vc_params['properties_state'] : 0;
		$city = $vc_edit && !empty($vc_params['properties_city']) ? $vc_params['properties_city'] : 0;
		$neighborhood = $vc_edit && !empty($vc_params['properties_neighborhood']) ? $vc_params['properties_neighborhood'] : 0;
		// List of states
		$list_states = trx_addons_array_merge( array( 0 => trx_addons_get_not_selected_text( esc_html__( 'State', 'trx_addons' ) ) ),
										$country == 0
											? array()
											: trx_addons_get_list_terms(false, TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_STATE, array(
												'meta_key' => 'country',
												'meta_value' => $country
												))
										);
		// List of cities
		$args = array();
		if ($state > 0)
			$args = array(
						'meta_key' => 'state',
						'meta_value' => $state
						);
		else if ($country > 0)
			$args = array(
						'meta_key' => 'country',
						'meta_value' => $country
						);
		$list_cities = trx_addons_array_merge( array( 0 => trx_addons_get_not_selected_text( esc_html__( 'City', 'trx_addons' ) ) ),
										count($args) == 0
											? array()
											: trx_addons_get_list_terms(false, TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_CITY, $args)
										);
		// List of neighborhoods
		$list_neighborhoods = trx_addons_array_merge( array( 0 => trx_addons_get_not_selected_text( esc_html__( 'Neighborhood', 'trx_addons' ) ) ),
										$city == 0
											? array()
											: trx_addons_get_list_terms(false, TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_NEIGHBORHOOD, array(
													'meta_key' => 'city',
													'meta_value' => $city
													))
										);
		// Prepare shortcode params
		$params = array_merge(
				array(
					array(
						"param_name" => "type",
						"heading" => esc_html__("Layout", 'trx_addons'),
						"description" => wp_kses_data( __("Select shortcode's layout", 'trx_addons') ),
						"admin_label" => true,
						'edit_field_class' => 'vc_col-sm-4',
						"std" => "default",
				        'save_always' => true,
						"value" => array_flip(apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('cpt', 'properties', 'sc'), 'trx_sc_properties')),
						"type" => "dropdown"
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
						"param_name" => "map_height",
						"heading" => esc_html__("Map height", 'trx_addons'),
						"description" => wp_kses_data( __("Specify height of the map with properties", 'trx_addons') ),
						'dependency' => array(
							'element' => 'type',
							'value' => array('map')
						),
						"std" => "350",
				        'save_always' => true,
						"type" => "textfield"
					),
					array(
						"param_name" => "properties_type",
						"heading" => esc_html__("Type", 'trx_addons'),
						"description" => wp_kses_data( __("Select the type to show properties that have it", 'trx_addons') ),
						"admin_label" => true,
						'edit_field_class' => 'vc_col-sm-4 vc_new_row',
						"value" => array_merge( array( trx_addons_get_not_selected_text( esc_html__( 'Type', 'trx_addons' ) ) => 0 ), array_flip( trx_addons_get_list_terms( false, TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_TYPE ) ) ),
						"std" => "0",
						"type" => "dropdown"
					),
					array(
						"param_name" => "properties_status",
						"heading" => esc_html__("Status", 'trx_addons'),
						"description" => wp_kses_data( __("Select the status to show properties that have it", 'trx_addons') ),
						"admin_label" => true,
						'edit_field_class' => 'vc_col-sm-4',
						"value" => array_merge( array( trx_addons_get_not_selected_text( esc_html__( 'Status', 'trx_addons' ) ) => 0 ), array_flip( trx_addons_get_list_terms( false, TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_STATUS ) ) ),
						"std" => "0",
						"type" => "dropdown"
					),
					array(
						"param_name" => "properties_labels",
						"heading" => esc_html__("Label", 'trx_addons'),
						"description" => wp_kses_data( __("Select the label to show properties that have it", 'trx_addons') ),
						"admin_label" => true,
						'edit_field_class' => 'vc_col-sm-4',
						"value" => array_merge( array( trx_addons_get_not_selected_text( esc_html__( 'Label', 'trx_addons' ) ) => 0 ), array_flip( trx_addons_get_list_terms( false, TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_LABELS ) ) ),
						"std" => "0",
						"type" => "dropdown"
					),
					array(
						"param_name" => "properties_country",
						"heading" => esc_html__("Country", 'trx_addons'),
						"description" => wp_kses_data( __("Select the country to show properties from", 'trx_addons') ),
						"admin_label" => true,
						'edit_field_class' => 'vc_col-sm-3 vc_new_row',
				        'save_always' => true,
						"value" => array_merge( array( trx_addons_get_not_selected_text( esc_html__( 'Country', 'trx_addons' ) ) => 0 ), array_flip( trx_addons_get_list_terms( false, TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_COUNTRY ) ) ),
						"std" => "0",
						"type" => "dropdown"
					),
					array(
						"param_name" => "properties_state",
						"heading" => esc_html__("State", 'trx_addons'),
						"description" => wp_kses_data( __("Select the county/state to show properties from", 'trx_addons') ),
						"admin_label" => true,
						'edit_field_class' => 'vc_col-sm-3',
				        'save_always' => true,
						"value" => array_flip($list_states),
						"std" => "0",
						"type" => "dropdown"
					),
					array(
						"param_name" => "properties_city",
						"heading" => esc_html__("City", 'trx_addons'),
						"description" => wp_kses_data( __("Select the city to show properties from", 'trx_addons') ),
						"admin_label" => true,
						'edit_field_class' => 'vc_col-sm-3',
				        'save_always' => true,
						"value" => array_flip($list_cities),
						"std" => "0",
						"type" => "dropdown"
					),
					array(
						"param_name" => "properties_neighborhood",
						"heading" => esc_html__("Neighborhood", 'trx_addons'),
						"description" => wp_kses_data( __("Select the neighborhood to show properties from", 'trx_addons') ),
						"admin_label" => true,
						'edit_field_class' => 'vc_col-sm-3',
				        'save_always' => true,
						"value" => array_flip($list_neighborhoods),
						"std" => "0",
						"type" => "dropdown"
					)
				),
				trx_addons_vc_add_query_param(''),
				trx_addons_vc_add_slider_param(),
				trx_addons_vc_add_title_param(),
				trx_addons_vc_add_id_param()
		);
		
		// Add dependencies to params
		$params = trx_addons_vc_add_param_option($params, 'orderby', array( 
																		"value" => array_flip(trx_addons_get_list_sc_query_orderby('none', 'none,ID,post_date,price,title,rand'))
																		)
												);
		$params = trx_addons_vc_add_param_option($params, 'columns', array( 
																		'dependency' => array(
																			'element' => 'type',
																			'value' => array('default', 'slider')
																			)
																		)
												);
		$params = trx_addons_vc_add_param_option($params, 'slider', array( 
																		'dependency' => array(
																			'element' => 'type',
																			'value' => array('default', 'slider')
																			)
																		)
												);
		/*
		$params = trx_addons_vc_add_param_option($params, 'slider_pagination', array(
																			"value" => array_flip(array_merge(trx_addons_get_list_sc_slider_paginations(), array(
																				'bottom_outside' => esc_html__('Bottom Outside', 'trx_addons')
																			)))
																		)
												);
		*/
												
		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_sc_properties",
				"name" => esc_html__("Properties", 'trx_addons'),
				"description" => wp_kses_data( __("Display selected properties", 'trx_addons') ),
				"category" => esc_html__('ThemeREX', 'trx_addons'),
				"icon" => 'icon_trx_sc_properties',
				"class" => "trx_sc_properties",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => $params
			), 'trx_sc_properties' );
	}
}
