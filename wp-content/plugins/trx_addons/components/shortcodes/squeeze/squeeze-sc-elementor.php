<?php
/**
 * Shortcode: Squeeze (Elementor support)
 *
 * @package ThemeREX Addons
 * @since v2.21.2
 */

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }




// Elementor Widget
//------------------------------------------------------
if ( ! function_exists( 'trx_addons_sc_squeeze_add_in_elementor' ) ) {
	add_action( trx_addons_elementor_get_action_for_widgets_registration(), 'trx_addons_sc_squeeze_add_in_elementor' );
	function trx_addons_sc_squeeze_add_in_elementor() {
		
		if ( ! class_exists( 'TRX_Addons_Elementor_Widget' ) ) return;	

		class TRX_Addons_Elementor_Widget_Squeeze extends TRX_Addons_Elementor_Widget {

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
					'image' => 'url'
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
				return 'trx_sc_squeeze';
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
				return __( 'Squeeze images', 'trx_addons' );
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
				return 'eicon-accordion';
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
				return ['trx_addons-elements'];
			}

			/**
			 * Widget preview refresh button.
			 *
			 * @since 2.6.0
			 * @access public
			 */
			/*
			public function is_reload_preview_required() {
				return true;
			}
			*/

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
				$post_type = ! empty( $params['post_type'] ) ? $params['post_type'] : 'post';
				$taxonomy = !empty($params['taxonomy']) ? $params['taxonomy'] : 'category';
				$tax_obj = get_taxonomy($taxonomy);

				// Register controls
				$this->start_controls_section(
					'section_sc_squeeze',
					[
						'label' => __( 'Squeeze images', 'trx_addons' ),
					]
				);

				$this->add_control(
					'type',
					[
						'label' => __( 'Layout', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('sc', 'squeeze'), 'trx_sc_squeeze'),
						'default' => 'default'
					]
				);

				$this->add_control(
					'post_type',
					[
						'label' => __( 'Post type', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_posts_types(),
						'default' => 'post'
					]
				);

				$this->add_control(
					'taxonomy',
					[
						'label' => __( 'Taxonomy', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_taxonomies( false, $post_type ),
						'default' => 'category'
					]
				);

				$this->add_control(
					'cat',
					[
						'label' => __( 'Category', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT2,
						'multiple' => true,
						'options' => ! $is_edit_mode
										? array()
											// Make keys as string (add a space after the number) to preserve the order in the list
											// (otherwise the keys will be converted to numbers in the JS and the order will be broken)
										: trx_addons_array_make_string_keys(
												trx_addons_array_merge(
													array( '' => trx_addons_get_not_selected_text( ! empty( $tax_obj->label ) ? $tax_obj->label : __( '- Not Selected -', 'trx_addons' ) ) ),
													$taxonomy == 'category' 
														? trx_addons_get_list_categories() 
														: trx_addons_get_list_terms(false, $taxonomy)
												)
											),
						'default' => ''
					]
				);

				add_filter( 'trx_addons_filter_elementor_add_query_param', array( $this, 'remove_columns_from_query_params' ) );
				$this->add_query_param( '' );
				remove_filter( 'trx_addons_filter_elementor_add_query_param', array( $this, 'remove_columns_from_query_params' ) );

				// Custom slides
				$this->add_control(
					'heading_slides',
					[
						'label' => __( 'Custom slides', 'elementor' ),
						'type' => \Elementor\Controls_Manager::HEADING,
						'separator' => 'before',
					]
				);

				$this->add_control(
					'slides',
					[
						'label' => '',
						'type' => \Elementor\Controls_Manager::REPEATER,
						'fields' => apply_filters('trx_addons_sc_param_group_params', [
							[
								'name' => 'title', 
								'label' => __("Title", 'trx_addons'),
								'label_block' => false,
								'type' => \Elementor\Controls_Manager::TEXT,
								'default' => ''
							],
							[
								'name' => 'subtitle', 
								'label' => __("Subtitle", 'trx_addons'),
								'label_block' => false,
								'type' => \Elementor\Controls_Manager::TEXT,
								'default' => ''
							],
							[
								'name' => 'image',
								'label' => __( 'Image', 'trx_addons' ),
								'type' => \Elementor\Controls_Manager::MEDIA,
								'default' => [
									'url' => '',
								]
							],
							[
								'name' => 'link',
								'label' => __( 'URL', 'trx_addons' ),
								'type' => \Elementor\Controls_Manager::URL,
								'label_block' => false,
								'placeholder' => __( '//your-link.com', 'trx_addons' ),
							]
						], 'trx_sc_squeeze' ),
						'title_field'        => '{{ title }}',
//						'prevent_empty'      => false,
//						'frontend_available' => true
					]
				);

				// Paginations
				$this->add_control(
					'heading_paginations',
					[
						'label' => __( 'Paginations', 'elementor' ),
						'type' => \Elementor\Controls_Manager::HEADING,
						'separator' => 'before',
					]
				);

				$this->add_control(
					'bullets',
					[
						'label' => __( 'Bullets', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'return_value' => '1',
					]
				);

				$this->add_control(
					'bullets_position',
					[
						'label' => __( 'Position', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => trx_addons_get_list_sc_squeeze_bullets_positions(),
						'default' => 'left',
						'condition' => [
							'bullets' => '1'
						]
					]
				);

				$this->add_control(
					'numbers',
					[
						'label' => __( 'Page numbers', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'return_value' => '1',
					]
				);

				$this->add_control(
					'numbers_position',
					[
						'label' => __( 'Position', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => trx_addons_get_list_sc_squeeze_numbers_positions(),
						'default' => 'center',
						'condition' => [
							'numbers' => '1'
						]
					]
				);

				$this->add_control(
					'progress',
					[
						'label' => __( 'Progress Bar', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'return_value' => '1',
					]
				);

				$this->add_control(
					'progress_position',
					[
						'label' => __( 'Position', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => trx_addons_get_list_sc_squeeze_progress_positions(),
						'default' => 'left',
						'condition' => [
							'progress' => '1'
						]
					]
				);

				$this->end_controls_section();
			}

			public function remove_columns_from_query_params( $params ) {
				return trx_addons_array_delete_by_subkey( $params, 'name', 'columns' );
			}

		}
		
		// Register widget
		trx_addons_elm_register_widget( 'TRX_Addons_Elementor_Widget_Squeeze' );
	}
}
