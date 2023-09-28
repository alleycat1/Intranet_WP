<?php
/**
 * ThemeREX Addons Custom post type: Cars (WPBakery support)
 *
 * @package ThemeREX Addons
 * @since v1.6.25
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


// Add [trx_sc_cars] in the VC shortcodes list
if (!function_exists('trx_addons_sc_cars_add_in_vc')) {
	function trx_addons_sc_cars_add_in_vc() {
		
		if (!trx_addons_exists_vc()) return;
		
		vc_lean_map("trx_sc_cars", 'trx_addons_sc_cars_add_in_vc_params');
		class WPBakeryShortCode_Trx_Sc_Cars extends WPBakeryShortCode {}
	}
	add_action('init', 'trx_addons_sc_cars_add_in_vc', 20);
}

// Return params
if (!function_exists('trx_addons_sc_cars_add_in_vc_params')) {
	function trx_addons_sc_cars_add_in_vc_params() {
		// If open params in VC Editor
		list($vc_edit, $vc_params) = trx_addons_get_vc_form_params('trx_sc_cars');
		// Prepare lists                                                          
		$maker = $vc_edit && !empty($vc_params['cars_maker']) ? $vc_params['cars_maker'] : 0;
		$model = $vc_edit && !empty($vc_params['cars_model']) ? $vc_params['cars_model'] : 0;
		// List of models
		$list_models = trx_addons_array_merge( array( 0 => trx_addons_get_not_selected_text( esc_html__( 'Model', 'trx_addons' ) ) ),
										$maker == 0
											? array()
											: trx_addons_get_list_terms(false, TRX_ADDONS_CPT_CARS_TAXONOMY_MODEL, array(
												'meta_key' => 'maker',
												'meta_value' => $maker
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
						"value" => array_flip(apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('cpt', 'cars', 'sc'), 'trx_sc_cars')),
						"type" => "dropdown"
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
						"param_name" => "more_text",
						"heading" => esc_html__("'More' text", 'trx_addons'),
						"description" => wp_kses_data( __("Specify caption of the 'Read more' button. If empty - hide button", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4',
						"std" => esc_html__('Read more', 'trx_addons'),
						"type" => "textfield"
					),
					array(
						"param_name" => "cars_type",
						"heading" => esc_html__("Type", 'trx_addons'),
						"description" => wp_kses_data( __("Select the type to show cars that have it", 'trx_addons') ),
						"admin_label" => true,
						'edit_field_class' => 'vc_col-sm-4',
						"value" => array_merge( array( trx_addons_get_not_selected_text( esc_html__( 'Type', 'trx_addons' ) ) => 0 ), array_flip( trx_addons_get_list_terms( false, TRX_ADDONS_CPT_CARS_TAXONOMY_TYPE ) ) ),
						"std" => "0",
						"type" => "dropdown"
					),
					array(
						"param_name" => "cars_maker",
						"heading" => esc_html__("Manufacturer", 'trx_addons'),
						"description" => wp_kses_data( __("Select car manufacturer", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4',
						"admin_label" => true,
				        'save_always' => true,
						"value" => array_merge( array( trx_addons_get_not_selected_text( esc_html__( 'Manufacturer', 'trx_addons' ) ) => 0 ), array_flip( trx_addons_get_list_terms( false, TRX_ADDONS_CPT_CARS_TAXONOMY_MAKER ) ) ),
						"std" => "0",
						"type" => "dropdown"
					),
					array(
						"param_name" => "cars_model",
						"heading" => esc_html__("Model", 'trx_addons'),
						"description" => wp_kses_data( __("Select car model", 'trx_addons') ),
						"admin_label" => true,
						'edit_field_class' => 'vc_col-sm-4',
				        'save_always' => true,
						"value" => array_flip($list_models),
						"std" => "0",
						"type" => "dropdown"
					),
					array(
						"param_name" => "cars_status",
						"heading" => esc_html__("Status", 'trx_addons'),
						"description" => wp_kses_data( __("Select the status to show cars that have it", 'trx_addons') ),
						"admin_label" => true,
						'edit_field_class' => 'vc_col-sm-4 vc_new_row',
						"value" => array_merge( array( trx_addons_get_not_selected_text( esc_html__( 'Status', 'trx_addons' ) ) => 0 ), array_flip( trx_addons_get_list_terms( false, TRX_ADDONS_CPT_CARS_TAXONOMY_STATUS ) ) ),
						"std" => "0",
						"type" => "dropdown"
					),
					array(
						"param_name" => "cars_labels",
						"heading" => esc_html__("Label", 'trx_addons'),
						"description" => wp_kses_data( __("Select the label to show cars that have it", 'trx_addons') ),
						"admin_label" => true,
						'edit_field_class' => 'vc_col-sm-4',
						"value" => array_merge( array( trx_addons_get_not_selected_text( esc_html__( 'Label', 'trx_addons' ) ) => 0 ), array_flip( trx_addons_get_list_terms( false, TRX_ADDONS_CPT_CARS_TAXONOMY_LABELS ) ) ),
						"std" => "0",
						"type" => "dropdown"
					),
					array(
						"param_name" => "cars_city",
						"heading" => esc_html__("City", 'trx_addons'),
						"description" => wp_kses_data( __("Select the city to show cars from it", 'trx_addons') ),
						"admin_label" => true,
						'edit_field_class' => 'vc_col-sm-4',
						"value" => array_merge( array( trx_addons_get_not_selected_text( esc_html__( 'City', 'trx_addons' ) ) => 0 ), array_flip( trx_addons_get_list_terms( false, TRX_ADDONS_CPT_CARS_TAXONOMY_CITY ) ) ),
						"std" => "0",
						"type" => "dropdown"
					),
					array(
						"param_name" => "cars_transmission",
						"heading" => esc_html__("Transmission", 'trx_addons'),
						"description" => wp_kses_data( __("Select type of the transmission", 'trx_addons') ),
						"admin_label" => true,
						'edit_field_class' => 'vc_col-sm-4 vc_new_row',
						"value" => array_merge( array( trx_addons_get_not_selected_text( esc_html__( 'Transmission', 'trx_addons' ) ) => 0 ), array_flip( trx_addons_cpt_cars_get_list_transmission() ) ),
						"std" => "none",
						"type" => "dropdown"
					),
					array(
						"param_name" => "cars_type_drive",
						"heading" => esc_html__("Type of drive", 'trx_addons'),
						"description" => wp_kses_data( __("Select type of drive", 'trx_addons') ),
						"admin_label" => true,
						'edit_field_class' => 'vc_col-sm-4',
						"value" => array_merge( array( trx_addons_get_not_selected_text( esc_html__( 'Type drive', 'trx_addons' ) ) => 0 ), array_flip( trx_addons_cpt_cars_get_list_type_of_drive() ) ),
						"std" => "none",
						"type" => "dropdown"
					),
					array(
						"param_name" => "cars_fuel",
						"heading" => esc_html__("Fuel", 'trx_addons'),
						"description" => wp_kses_data( __("Select type of the fuel", 'trx_addons') ),
						"admin_label" => true,
						'edit_field_class' => 'vc_col-sm-4',
						"value" => array_merge( array( trx_addons_get_not_selected_text( esc_html__( 'Fuel', 'trx_addons' ) ) => 0 ), array_flip( trx_addons_cpt_cars_get_list_fuel() ) ),
						"std" => "none",
						"type" => "dropdown"
					),
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
				"base" => "trx_sc_cars",
				"name" => esc_html__("Cars", 'trx_addons'),
				"description" => wp_kses_data( __("Display selected cars", 'trx_addons') ),
				"category" => esc_html__('ThemeREX', 'trx_addons'),
				"icon" => 'icon_trx_sc_cars',
				"class" => "trx_sc_cars",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => $params
			), 'trx_sc_cars' );
	}
}
