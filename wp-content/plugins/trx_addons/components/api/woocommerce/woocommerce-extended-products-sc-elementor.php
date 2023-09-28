<?php
/**
 * Shortcode: Extended Products (Elementor support)
 *
 * @package ThemeREX Addons
 * @since v1.85.0
 */

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }




// Elementor Widget
//------------------------------------------------------
if ( ! function_exists( 'trx_addons_sc_extended_products_add_in_elementor' ) ) {
	add_action( trx_addons_elementor_get_action_for_widgets_registration(), 'trx_addons_sc_extended_products_add_in_elementor' );
	/**
	 * Register widget "Extended Products" for Elementor
	 */
	function trx_addons_sc_extended_products_add_in_elementor() {
		
		if ( ! class_exists( 'TRX_Addons_Elementor_Widget' ) ) {
			return;
		}

		class TRX_Addons_Elementor_Widget_Extended_Products extends TRX_Addons_Elementor_Widget {

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
					'per_page' => 'size',
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
				return 'trx_sc_extended_products';
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
				return __( 'Extended Products', 'trx_addons' );
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
				return 'eicon-products';
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
				return ['trx_addons-support'];
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
					'section_sc_extended_products',
					[
						'label' => __( 'Extended Products', 'trx_addons' ),
					]
				);

				$this->add_control(
					'type',
					[
						'label' => __( 'Layout', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : apply_filters('trx_addons_sc_type', trx_addons_woocommerce_extended_products_get_list_styles(), 'trx_sc_extended_products'),
						'default' => 'default'
					]
				);

				$this->add_control(
					'style',
					[
						'label' => __( 'Type', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_woocommerce_extended_products_get_list_types(),
						'default' => 'products'
					]
				);

				$this->add_control(
					'per_page',
					[
						'label' => __( 'Number', 'trx_addons' ),
						'description' => wp_kses_data( __("How many products to show on the page", 'trx_addons') ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 3,
							'unit' => 'px'
						],
						'size_units' => [ 'px' ],
						'range' => [
							'px' => [
								'min' => 1,
								'max' => 50
							]
						]
					]
				);

				$this->add_control(
					'columns',
					[
						'label' => __( 'Columns', 'trx_addons' ),
						'description' => wp_kses_data( __("Specify the number of columns", 'trx_addons') ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 3,
							'unit' => 'px'
						],
						'size_units' => [ 'px' ],
						'range' => [
							'px' => [
								'min' => 2,
								'max' => 6
							]
						]
					]
				);

				$list = ! is_admin()
							? array()
							: trx_addons_get_list_terms( false,
														'product_cat',
														array(
															'hide_empty' => 1,
															'return_key' => 'slug'
															)
														);

				$this->add_control(
					'category',
					[
						'label' => __( 'Category', 'trx_addons' ),
						'label_block' => true,
						'type' => \Elementor\Controls_Manager::SELECT2,
						'placeholder' => __( 'Select category', 'trx_addons' ),
						'multiple' => true,
						'options' => $list,
						'default' => ''
					]
				);

				$this->add_control(
					'ids',
					[
						'label' => __( 'IDs', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::TEXT,
						'default' => ''
					]
				);

				$this->add_control(
					'orderby',
					[
						'label' => __( 'Order by', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => [
							'date' => esc_html__('Date', 'trx_addons'), 
							'title' => esc_html__('Title', 'trx_addons'), 
						],
						'default' => 'date'
					]
				);

				$this->add_control(
					'order',
					[
						'label' => __( 'Order', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_query_orders(),
						'default' => 'desc'
					]
				);

				$this->end_controls_section();

				$this->add_slider_param();

				$this->add_title_param();
			}
		}
		
		// Register widget
		trx_addons_elm_register_widget( 'TRX_Addons_Elementor_Widget_Extended_Products' );
	}
}
