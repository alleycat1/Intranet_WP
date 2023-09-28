<?php
/**
 * ThemeREX Addons Custom post type: Properties (Elementor support)
 *
 * @package ThemeREX Addons
 * @since v1.6.22
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}



// Elementor Widget
//------------------------------------------------------
if (!function_exists('trx_addons_sc_properties_add_in_elementor')) {
	
	// Load required styles and scripts for Elementor Editor mode
	if ( !function_exists( 'trx_addons_sc_properties_elm_editor_load_scripts' ) ) {
		add_action("elementor/editor/before_enqueue_scripts", 'trx_addons_sc_properties_elm_editor_load_scripts');
		function trx_addons_sc_properties_elm_editor_load_scripts() {
			wp_enqueue_script( 'trx_addons-sc_properties-elementor-editor', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_CPT . 'properties/properties.elementor.editor.js'), array('jquery'), null, true );
		}
	}
	
	// Register widgets
	add_action( trx_addons_elementor_get_action_for_widgets_registration(), 'trx_addons_sc_properties_add_in_elementor' );
	function trx_addons_sc_properties_add_in_elementor() {
		
		if (!class_exists('TRX_Addons_Elementor_Widget')) return;	

		class TRX_Addons_Elementor_Widget_Properties extends TRX_Addons_Elementor_Widget {

			/**
			 * Widget base constructor.
			 *
			 * Initializing the widget base class.
			 *
			 * @since 1.6.41
			 * @access public
			 *
			 * @param array      $data Widget data. Default is an empty array.
			 * @param array|null $args Optional. Widget default arguments. Default is null.
			 */
			public function __construct( $data = [], $args = null ) {
				parent::__construct( $data, $args );
				$this->add_plain_params([
					'map_height' => 'size+unit'
				]);
			}

			/**
			 * Retrieve widget name.
			 *
			 * @since 1.6.41
			 * @access public
			 *
			 * @return string Widget name.
			 */
			public function get_name() {
				return 'trx_sc_properties';
			}

			/**
			 * Retrieve widget title.
			 *
			 * @since 1.6.41
			 * @access public
			 *
			 * @return string Widget title.
			 */
			public function get_title() {
				return __( 'Properties', 'trx_addons' );
			}

			/**
			 * Retrieve widget icon.
			 *
			 * @since 1.6.41
			 * @access public
			 *
			 * @return string Widget icon.
			 */
			public function get_icon() {
				return 'eicon-info-box';
			}

			/**
			 * Retrieve the list of categories the widget belongs to.
			 *
			 * Used to determine where to display the widget in the editor.
			 *
			 * @since 1.6.41
			 * @access public
			 *
			 * @return array Widget categories.
			 */
			public function get_categories() {
				return ['trx_addons-cpt'];
			}

			/**
			 * Register widget controls.
			 *
			 * Adds different input fields to allow the user to change and customize the widget settings.
			 *
			 * @since 1.6.41
			 * @access protected
			 */
			protected function register_controls() {
				// Detect edit mode
				$is_edit_mode = trx_addons_elm_is_edit_mode();
				// If open params in Elementor Editor
				$params = $this->get_sc_params();
				// Prepare lists                                                          
				$country = !empty($params['properties_country']) ? $params['properties_country'] : 0;
				$state = !empty($params['properties_state']) ? $params['properties_state'] : 0;
				$city = !empty($params['properties_city']) ? $params['properties_city'] : 0;
				$neighborhood = !empty($params['properties_neighborhood']) ? $params['properties_neighborhood'] : 0;
				// List of states
				$list_states = ! $is_edit_mode ? array() : trx_addons_array_merge( array( 0 => trx_addons_get_not_selected_text( esc_html__( 'State', 'trx_addons' ) ) ),
													$country == 0
														? array()
														: trx_addons_get_list_terms(false, TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_STATE, array(
															'meta_key' => 'country',
															'meta_value' => $country
															))
													);
				// List of cities
				$args = array();
				if ($state > 0) {
					$args = array(
								'meta_key' => 'state',
								'meta_value' => $state
								);
				} else if ($country > 0) {
					$args = array(
								'meta_key' => 'country',
								'meta_value' => $country
								);
				}
				$list_cities = ! $is_edit_mode ? array() : trx_addons_array_merge( array( 0 => trx_addons_get_not_selected_text( esc_html__( 'City', 'trx_addons' ) ) ),
													count($args) == 0
														? array()
														: trx_addons_get_list_terms(false, TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_CITY, $args)
													);
				// List of neighborhoods
				$list_neighborhoods = ! $is_edit_mode ? array() : trx_addons_array_merge( array( 0 => trx_addons_get_not_selected_text( esc_html__( 'Neighborhood', 'trx_addons' ) ) ),
													$city == 0
														? array()
														: trx_addons_get_list_terms(false, TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_NEIGHBORHOOD, array(
																'meta_key' => 'city',
																'meta_value' => $city
																))
													);

				$this->start_controls_section(
					'section_sc_properties',
					[
						'label' => __( 'Properties', 'trx_addons' ),
					]
				);

				$this->add_control(
					'type',
					[
						'label' => __( 'Layout', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('cpt', 'properties', 'sc'), 'trx_sc_properties'),
						'default' => 'default'
					]
				);

				$this->add_control(
					'more_text',
					[
						'label' => __( "'More' text", 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::TEXT,
						'default' => esc_html__('Read more', 'trx_addons'),
					]
				);

				$this->add_control(
					'pagination',
					[
						'label' => __( 'Pagination', 'trx_addons' ),
						'label_block' => false,
						'description' => wp_kses_data( __("Add pagination links after posts. Attention! Pagination is not allowed if the slider layout is used.", 'trx_addons') ),
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_paginations(),
						'default' => 'none'
					]
				);

				$this->add_control(
					'map_height',
					[
						'label' => __( 'Height', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 350,
							'unit' => 'px'
						],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 500
							],
							'em' => [
								'min' => 0,
								'max' => 50
							],
						],
						'size_units' => [ 'px', 'em' ],
						'condition' => [
							//'map' => '1'
							'type' => 'map'
						],
						'selectors' => [
							'{{WRAPPER}} .sc_googlemap,{{WRAPPER}} .sc_osmap' => 'height: {{SIZE}}{{UNIT}};',
						],
					]
				);

				$this->add_control(
					'properties_type',
					[
						'label' => __( 'Type', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_array_merge( array( 0 => trx_addons_get_not_selected_text( esc_html__( 'Type', 'trx_addons' ) ) ), trx_addons_get_list_terms( false, TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_TYPE ) ),
						'default' => '0'
					]
				);

				$this->add_control(
					'properties_status',
					[
						'label' => __( 'Status', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_array_merge( array( 0 => trx_addons_get_not_selected_text( esc_html__( 'Status', 'trx_addons' ) ) ), trx_addons_get_list_terms( false, TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_STATUS ) ),
						'default' => '0'
					]
				);

				$this->add_control(
					'properties_labels',
					[
						'label' => __( 'Label', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_array_merge( array( 0 => trx_addons_get_not_selected_text( esc_html__( 'Label', 'trx_addons' ) ) ), trx_addons_get_list_terms( false, TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_LABELS ) ),
						'default' => '0'
					]
				);

				$this->add_control(
					'properties_country',
					[
						'label' => __( 'Country', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_array_merge( array( 0 => trx_addons_get_not_selected_text( esc_html__( 'Country', 'trx_addons' ) ) ), trx_addons_get_list_terms( false, TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_COUNTRY ) ),
						'default' => '0'
					]
				);

				$this->add_control(
					'properties_state',
					[
						'label' => __( 'State', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => $list_states,
						'default' => '0'
					]
				);

				$this->add_control(
					'properties_city',
					[
						'label' => __( 'City', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => $list_cities,
						'default' => '0'
					]
				);

				$this->add_control(
					'properties_neighborhood',
					[
						'label' => __( 'Neighborhood', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => $list_neighborhoods,
						'default' => '0'
					]
				);

				$this->end_controls_section();
				
				$this->add_query_param(false, [
					'orderby' => [
								'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_query_orderby('none', 'none,ID,post_date,price,title,rand')
					],
					'columns' => [
								'condition' => [
									'type' => ['default', 'slider']
								]
					]
				], TRX_ADDONS_CPT_PROPERTIES_PT);
				
				$this->add_slider_param(false, [
					'slider' => [
								'condition' => [
									'type' => ['default', 'slider']
								]
					],
					/*
					'slider_pagination' => [
								'options' => ! $is_edit_mode ? array() : trx_addons_array_merge(trx_addons_get_list_sc_slider_paginations(), array(
																			'bottom_outside' => esc_html__('Bottom Outside', 'trx_addons')
																			))
					]
					*/
				]);
				
				$this->add_title_param();
			}
		}
		
		// Register widget
		trx_addons_elm_register_widget( 'TRX_Addons_Elementor_Widget_Properties' );
	}
}


// Disable our widgets (shortcodes) to use in Elementor
// because we create special Elementor's widgets instead
if (!function_exists('trx_addons_sc_properties_black_list')) {
	add_action( 'elementor/widgets/black_list', 'trx_addons_sc_properties_black_list' );
	function trx_addons_sc_properties_black_list($list) {
		$list[] = 'trx_addons_widget_properties_compare';
		$list[] = 'trx_addons_widget_properties_search';
		$list[] = 'trx_addons_widget_properties_sort';
		return $list;
	}
}
