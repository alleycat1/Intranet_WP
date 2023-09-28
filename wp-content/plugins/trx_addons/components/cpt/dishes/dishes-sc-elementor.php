<?php
/**
 * ThemeREX Addons Custom post type: Dishes (Elementor support)
 *
 * @package ThemeREX Addons
 * @since v1.6.09
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}



// Elementor Widget
//------------------------------------------------------
if (!function_exists('trx_addons_sc_dishes_add_in_elementor')) {
	add_action( trx_addons_elementor_get_action_for_widgets_registration(), 'trx_addons_sc_dishes_add_in_elementor' );
	function trx_addons_sc_dishes_add_in_elementor() {
		
		if (!class_exists('TRX_Addons_Elementor_Widget')) return;	

		class TRX_Addons_Elementor_Widget_Dishes extends TRX_Addons_Elementor_Widget {

			/**
			 * Retrieve widget name.
			 *
			 * @since 1.6.41
			 * @access public
			 *
			 * @return string Widget name.
			 */
			public function get_name() {
				return 'trx_sc_dishes';
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
				return __( 'Dishes', 'trx_addons' );
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
				return 'eicon-menu-toggle';
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

				// Register controls
				$this->start_controls_section(
					'section_sc_dishes',
					[
						'label' => __( 'Dishes', 'trx_addons' ),
					]
				);

				$this->add_control(
					'type',
					[
						'label' => __( 'Layout', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('cpt', 'dishes', 'sc'), 'trx_sc_dishes'),
						'default' => 'default'
					]
				);

				$this->add_control(
					'featured_position',
					[
						'label' => __( 'Featured position', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_dishes_positions(),
						'default' => 'top'
					]
				);

				$this->add_control(
					'no_margin',
					[
						'label' => __( 'Remove margin', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'return_value' => '1'
					]
				);

				$this->add_control(
					'hide_excerpt',
					[
						'label' => __( 'Excerpt', 'trx_addons' ),
						'label_block' => false,
						'description' => wp_kses_data( __("Toggle this option to hide the excerpt.", 'trx_addons') ),
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Show', 'trx_addons' ),
						'label_on' => __( 'Hide', 'trx_addons' ),
						'return_value' => '1',
						'condition' => [
							'type' => ['default', 'float']
						]
					]
				);

				$this->add_control(
					'popup',
					[
						'label' => __( 'Open in the popup', 'trx_addons' ),
						'label_block' => false,
						'description' => wp_kses_data( __("Open details in the popup or navigate to the single post (default)", 'trx_addons') ),
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'return_value' => '1'
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
					'post_type',
					[
						'type' => \Elementor\Controls_Manager::HIDDEN,
						'render_type' => 'none',
						'default' => TRX_ADDONS_CPT_DISHES_PT
					]
				);
				$this->add_control(
					'taxonomy',
					[
						'type' => \Elementor\Controls_Manager::HIDDEN,
						'render_type' => 'none',
						'default' => TRX_ADDONS_CPT_DISHES_TAXONOMY
					]
				);

				$this->add_control(
					'cat',
					[
						'label' => __( 'Group', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_array_merge( array( 0 => trx_addons_get_not_selected_text( esc_html__( 'Select category', 'trx_addons' ) ) ), trx_addons_get_list_terms( false, TRX_ADDONS_CPT_DISHES_TAXONOMY ) ),
						'default' => '0'
					]
				);
				
				$this->add_query_param('', array(), TRX_ADDONS_CPT_DISHES_PT);

				$this->end_controls_section();
				
				$this->add_slider_param();
				
				$this->add_title_param();
			}
		}
		
		// Register widget
		trx_addons_elm_register_widget( 'TRX_Addons_Elementor_Widget_Dishes' );
	}
}
