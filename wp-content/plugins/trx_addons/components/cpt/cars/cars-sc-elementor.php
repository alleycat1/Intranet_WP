<?php
/**
 * ThemeREX Addons Custom post type: Cars (Elementor support)
 *
 * @package ThemeREX Addons
 * @since v1.6.25
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}




// Elementor Widget
if (!function_exists('trx_addons_sc_cars_add_in_elementor')) {
	
	// Load required styles and scripts for Elementor Editor mode
	if ( !function_exists( 'trx_addons_sc_cars_elm_editor_load_scripts' ) ) {
		add_action("elementor/editor/before_enqueue_scripts", 'trx_addons_sc_cars_elm_editor_load_scripts');
		function trx_addons_sc_cars_elm_editor_load_scripts() {
			wp_enqueue_script( 'trx_addons-sc_cars-elementor-editor', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_CPT . 'cars/cars.elementor.editor.js'), array('jquery'), null, true );
		}
	}
	
	// Register widgets
	add_action( trx_addons_elementor_get_action_for_widgets_registration(), 'trx_addons_sc_cars_add_in_elementor' );
	function trx_addons_sc_cars_add_in_elementor() {
		
		if (!class_exists('TRX_Addons_Elementor_Widget')) return;

		class TRX_Addons_Elementor_Widget_Cars extends TRX_Addons_Elementor_Widget {

			/**
			 * Retrieve widget name.
			 *
			 * @since 1.6.41
			 * @access public
			 *
			 * @return string Widget name.
			 */
			public function get_name() {
				return 'trx_sc_cars';
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
				return __( 'Cars', 'trx_addons' );
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
				return 'eicon-dashboard';
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
				$maker = !empty($params['cars_maker']) ? $params['cars_maker'] : 0;
				$model = !empty($params['cars_model']) ? $params['cars_model'] : 0;
				// List of models
				$list_models = ! $is_edit_mode ? array() : trx_addons_array_merge( array( 0 => trx_addons_get_not_selected_text( esc_html__( 'Model', 'trx_addons' ) ) ),
													$maker == 0
														? array()
														: trx_addons_get_list_terms(false, TRX_ADDONS_CPT_CARS_TAXONOMY_MODEL, array(
															'meta_key' => 'maker',
															'meta_value' => $maker
															))
													);

				$this->start_controls_section(
					'section_sc_cars',
					[
						'label' => __( 'Cars', 'trx_addons' ),
					]
				);

				$this->add_control(
					'type',
					[
						'label' => __( 'Layout', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('cpt', 'cars', 'sc'), 'trx_sc_cars'),
						'default' => 'default'
					]
				);

				$this->add_control(
					'cars_type',
					[
						'label' => __( 'Type', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_array_merge( array( 0 => trx_addons_get_not_selected_text( esc_html__( 'Type', 'trx_addons' ) ) ), trx_addons_get_list_terms( false, TRX_ADDONS_CPT_CARS_TAXONOMY_TYPE ) ),
						'default' => '0'
					]
				);

				$this->add_control(
					'cars_maker',
					[
						'label' => __( 'Manufacturer', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_array_merge( array( 0 => trx_addons_get_not_selected_text( esc_html__( 'Manufacturer', 'trx_addons' ) ) ), trx_addons_get_list_terms( false, TRX_ADDONS_CPT_CARS_TAXONOMY_MAKER ) ),
						'default' => '0'
					]
				);

				$this->add_control(
					'cars_model',
					[
						'label' => __( 'Model', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => $list_models,
						'default' => '0'
					]
				);

				$this->add_control(
					'cars_status',
					[
						'label' => __( 'Status', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_array_merge( array( 0 => trx_addons_get_not_selected_text( esc_html__( 'Status', 'trx_addons' ) ) ), trx_addons_get_list_terms( false, TRX_ADDONS_CPT_CARS_TAXONOMY_STATUS ) ),
						'default' => '0'
					]
				);

				$this->add_control(
					'cars_labels',
					[
						'label' => __( 'Label', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_array_merge( array( 0 => trx_addons_get_not_selected_text( esc_html__('Label', 'trx_addons' ) ) ), trx_addons_get_list_terms( false, TRX_ADDONS_CPT_CARS_TAXONOMY_LABELS ) ),
						'default' => '0'
					]
				);

				$this->add_control(
					'cars_city',
					[
						'label' => __( 'City', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_array_merge( array( 0 => trx_addons_get_not_selected_text( esc_html__( 'City', 'trx_addons' ) ) ), trx_addons_get_list_terms( false, TRX_ADDONS_CPT_CARS_TAXONOMY_CITY ) ),
						'default' => '0'
					]
				);

				$this->add_control(
					'cars_transmission',
					[
						'label' => __( 'Transmission', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_array_merge( array( 0 => trx_addons_get_not_selected_text( esc_html__( 'Transmission', 'trx_addons' ) ) ), trx_addons_cpt_cars_get_list_transmission() ),
						'default' => '0'
					]
				);

				$this->add_control(
					'cars_type_drive',
					[
						'label' => __( 'Type of drive', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_array_merge( array( 0 => trx_addons_get_not_selected_text( esc_html__( 'Type drive', 'trx_addons' ) ) ), trx_addons_cpt_cars_get_list_type_of_drive() ),
						'default' => '0'
					]
				);

				$this->add_control(
					'cars_fuel',
					[
						'label' => __( 'Fuel', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_array_merge( array( 0 => trx_addons_get_not_selected_text( esc_html__( 'Fuel', 'trx_addons' ) ) ), trx_addons_cpt_cars_get_list_fuel() ),
						'default' => '0'
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
				], TRX_ADDONS_CPT_CARS_PT);
				
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
		trx_addons_elm_register_widget( 'TRX_Addons_Elementor_Widget_Cars' );
	}
}


// Disable our widgets (shortcodes) to use in Elementor
// because we create special Elementor's widgets instead
if (!function_exists('trx_addons_sc_cars_black_list')) {
	add_action( 'elementor/widgets/black_list', 'trx_addons_sc_cars_black_list' );
	function trx_addons_sc_cars_black_list($list) {
		$list[] = 'trx_addons_widget_cars_compare';
		$list[] = 'trx_addons_widget_cars_search';
		$list[] = 'trx_addons_widget_cars_sort';
		return $list;
	}
}
