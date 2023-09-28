<?php
/**
 * Plugin support: Tour Master (Elementor support)
 *
 * @package ThemeREX Addons
 * @since v1.6.38
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


// Elementor Widgets
//------------------------------------------------------

if ( ! function_exists( 'trx_addons_sc_tourmaster_add_in_elementor_ttc' ) ) {
	add_action( trx_addons_elementor_get_action_for_widgets_registration(), 'trx_addons_sc_tourmaster_add_in_elementor_ttc' );
	/**
	 * Register Tourmaster Tour Category widget in Elementor
	 * 
	 * @hooked elementor/widgets/register
	 */
	function trx_addons_sc_tourmaster_add_in_elementor_ttc() {

		if ( ! trx_addons_exists_tourmaster() || ! class_exists( 'TRX_Addons_Elementor_Widget' ) ) {
			return;
		}
		
		class TRX_Addons_Elementor_Widget_Tourmaster_Tour_Category extends TRX_Addons_Elementor_Widget {

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
					'num-fetch' => 'size',
					'column-size' => 'size'
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
				return 'tourmaster_tour_category';
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
				return __( 'Tourmaster Categories', 'trx_addons' );
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
				return 'eicon-image-hotspot';
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
					'section_sc_tourmaster_category',
					[
						'label' => __( 'Tour Category', 'trx_addons' ),
					]
				);

				$this->add_control(
					'filter-type',
					[
						'label' => __( 'Filter Type', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_taxonomies(false, TRX_ADDONS_TOURMASTER_CPT_TOUR),
						'default' => TRX_ADDONS_TOURMASTER_TAX_TOUR_CATEGORY
					]
				);
				
				$this->add_control(
					'num-fetch',
					[
						'label' => __( 'Display Number', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 3
						],
						'range' => [
							'px' => [
								'min' => 1,
								'max' => 12
							]
						]
					]
				);
				
				$this->add_control(
					'column-size',
					[
						'label' => __( 'Columns', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 3
						],
						'range' => [
							'px' => [
								'min' => 1,
								'max' => 5
							]
						]
					]
				);
/*
				$this->add_control(
					'column-size',
					[
						'label' => __( 'Columns', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => [ '1' => "60", '2' => "30", '3' => "20", '4' => "15", '5' => "12" ],
						'default' => '3'
					]
				);
*/
				$this->add_control(
					'style',
					[
						'label' => __( 'Style', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => [
							'widget' => esc_html__('Widget', 'trx_addons'),
							'grid' => esc_html__('Grid', 'trx_addons'),
							'grid-2' => esc_html__('Grid 2', 'trx_addons'),
						],
						'default' => 'widget'
					]
				);

				$this->add_control(
					'thumbnail-size',
					[
						'label' => __( 'Thumbnail Size', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_thumbnail_sizes(),
						'default' => 'thumbnail'
					]
				);

				$this->add_control(
					'orderby',
					[
						'label' => __( 'Order by', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => [
							'name' => esc_html__('Name', 'trx_addons'), 
							'slug' => esc_html__('Slug', 'trx_addons'), 
							'term_id' => esc_html__('Term ID', 'trx_addons'), 
						],
						'default' => 'name'
					]
				);

				$this->add_control(
					'order',
					[
						'label' => __( 'Order', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_query_orders(),
						'default' => 'asc'
					]
				);

				$this->end_controls_section();
			}

			// Return widget's layout
			public function render() {
				if ( shortcode_exists( 'tourmaster_tour_category' ) ) {
					$atts = $this->sc_prepare_atts( $this->get_settings(), $this->get_sc_name() );
					trx_addons_show_layout(
						do_shortcode(
							sprintf( '[tourmaster_tour_category'
										. ' filter-type="%1$s"'
										. ' num-fetch="%2$s"'
										. ' column-size="%3$s"'
										. ' style="%4$s"'
										. ' thumbnail-size="%5$s"'
										. ' orderby="%6$s"'
										. ' order="%7$s"'
										. ']',
										$atts['filter-type'],
										$atts['num-fetch'],
										$atts['column-size'],
										$atts['style'],
										$atts['thumbnail-size'],
										$atts['orderby'],
										$atts['order']
							)
						)
					);
				} else
					$this->shortcode_not_exists('tourmaster_tour_category', 'Tourmaster');
			}
		}
		
		// Register widget
		trx_addons_elm_register_widget( 'TRX_Addons_Elementor_Widget_Tourmaster_Tour_Category' );
	}
}

if ( ! function_exists( 'trx_addons_sc_tourmaster_add_in_elementor_tt' ) ) {
	add_action( trx_addons_elementor_get_action_for_widgets_registration(), 'trx_addons_sc_tourmaster_add_in_elementor_tt' );
	/**
	 * Add Tourmaster Tour widget to Elementor
	 *
	 * @hooked elementor/widgets/register
	 */
	function trx_addons_sc_tourmaster_add_in_elementor_tt() {

		if ( ! trx_addons_exists_tourmaster() || ! class_exists( 'TRX_Addons_Elementor_Widget' ) ) {
			return;
		}
		
		class TRX_Addons_Elementor_Widget_Tourmaster_Tour extends TRX_Addons_Elementor_Widget {

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
					'num-fetch' => 'size',
					'column-size' => 'size',
					'excerpt-number' => 'size'
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
				return 'tourmaster_tour';
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
				return __( 'Tourmaster Tour', 'trx_addons' );
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
				return 'eicon-image-hotspot';
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
					'section_sc_tourmaster_tour',
					[
						'label' => __( 'Tourmaster Tour', 'trx_addons' ),
					]
				);

				$this->add_control(
					'category',
					[
						'label' => __( 'Category', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT2,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_terms(false, TRX_ADDONS_TOURMASTER_TAX_TOUR_CATEGORY, array(
															'hide_empty' => 1, 'return_key' => 'slug')),
						'multiple' => true
					]
				);

				$this->add_control(
					'tag',
					[
						'label' => __( 'Tag', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT2,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_terms(false, TRX_ADDONS_TOURMASTER_TAX_TOUR_TAG, array(
															'hide_empty' => 1, 'return_key' => 'slug')),
						'multiple' => true
					]
				);

				$this->add_control(
					'discount-status',
					[
						'label' => __( 'Discount Status', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => [
									'all' => esc_html__('All', 'trx_addons'), 
									'discount' => esc_html__('Discounted Tour (tour with discount text filled)', 'trx_addons'), 
									],
						'default' => 'all'
					]
				);
				
				$this->add_control(
					'num-fetch',
					[
						'label' => __( 'Display Number', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 3
						],
						'range' => [
							'px' => [
								'min' => 1,
								'max' => 12
							]
						]
					]
				);

				$this->add_control(
					'orderby',
					[
						'label' => __( 'Order by', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => [
							'date' => esc_html__('Publish Date', 'trx_addons'), 
							'title' => esc_html__('Title', 'trx_addons'), 
							'rand' => esc_html__('Random', 'trx_addons'), 
							'menu_order' => esc_html__('Menu Order', 'trx_addons'), 
							'price' => esc_html__('Price', 'trx_addons'), 
							'duration' => esc_html__('Duration', 'trx_addons'), 
							'popularity' => esc_html__('Popularity ( View Count )', 'trx_addons'), 
							'rating' => esc_html__('Rating ( Score )', 'trx_addons'), 
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

				$this->add_control(
					'pagination',
					[
						'label' => __( 'Pagination', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => [
							'none' => esc_html__('None', 'trx_addons'), 
							'page' => esc_html__('Page', 'trx_addons'), 
							'load-more' => esc_html__('Load More', 'trx_addons'), 
						],
						'default' => 'none'
					]
				);

				$this->add_control(
					'pagination-style',
					[
						'label' => __( 'Pagination Style', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => [
							'default' => esc_html__('Default', 'trx_addons'),
							'plain' => esc_html__('Plain', 'trx_addons'),
							'rectangle' => esc_html__('Rectangle', 'trx_addons'),
							'rectangle-border' => esc_html__('Rectangle Border', 'trx_addons'),
							'round' => esc_html__('Round', 'trx_addons'),
							'round-border' => esc_html__('Round Border', 'trx_addons'),
							'circle' => esc_html__('Circle', 'trx_addons'),
							'circle-border' => esc_html__('Circle Border', 'trx_addons'),
						],
						'default' => 'default'
					]
				);

				$this->add_control(
					'pagination-align',
					[
						'label' => __( 'Pagination Align', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_aligns(false, false),
						'default' => 'left',
						'condition' => [
							'pagination' => 'page'
						]
					]
				);

				$this->end_controls_section();
				
				$this->start_controls_section(
					'section_sc_tourmaster_tour_filterer',
					[
						'label' => __( 'Filterer', 'trx_addons' ),
					]
				);

				$this->add_control(
					'enable-order-filterer',
					[
						'label' => __( 'Order Filterer', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => [
							'enable' => esc_html__('Enable', 'trx_addons'), 
							'disable' => esc_html__('Disable', 'trx_addons'), 
						],
						'default' => 'disable'
					]
				);

				$this->add_control(
					'order-filterer-list-style',
					[
						'label' => __( 'Order Filterer List Style', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => [
							'none' => esc_html__('None', 'trx_addons'),
							'full' => esc_html__('Full', 'trx_addons'),
							'full-with-frame' => esc_html__('Full With Frame', 'trx_addons'),
							'medium' => esc_html__('Medium', 'trx_addons'),
							'medium-with-frame' => esc_html__('Medium With Frame', 'trx_addons'),
							'widget' => esc_html__('Widget', 'trx_addons'),
						],
						'default' => 'none',
						/* IDs with not '-' are not allowed in Elementor's conditions
						'condition' => [
							'enable-order-filterer' => 'enable'
						]
						*/
					]
				);

				$this->add_control(
					'order-filterer-list-style-thumbnail',
					[
						'label' => __( 'Order Filterer List Style Thumbnail', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_thumbnail_sizes(),
						'default' => 'thumbnail',
						/* IDs with not '-' are not allowed in Elementor's conditions
						'condition' => [
							'enable-order-filterer' => 'enable'
						]
						*/
					]
				);

				$this->add_control(
					'order-filterer-grid-style',
					[
						'label' => __( 'Order Filterer Grid Style', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => [
							'none' => esc_html__('None', 'trx_addons'),
							'modern' => esc_html__('Modern', 'trx_addons'),
							'modern-no-space' => esc_html__('Modern No Space', 'trx_addons'),
							'grid' => esc_html__('Grid', 'trx_addons'),
							'grid-with-frame' => esc_html__('Grid With Frame', 'trx_addons'),
							'grid-no-space' => esc_html__('Grid No Space', 'trx_addons'),
						],
						'default' => 'none',
						/* IDs with not '-' are not allowed in Elementor's conditions
						'condition' => [
							'enable-order-filterer' => 'enable'
						]
						*/
					]
				);

				$this->add_control(
					'order-filterer-grid-style-thumbnail',
					[
						'label' => __( 'Order Filterer Grid Style Thumbnail', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_thumbnail_sizes(),
						'default' => 'thumbnail',
						/* IDs with not '-' are not allowed in Elementor's conditions
						'condition' => [
							'enable-order-filterer' => 'enable'
						]
						*/
					]
				);

				$this->end_controls_section();
				
				$this->start_controls_section(
					'section_sc_tourmaster_tour_style',
					[
						'label' => __( 'Style', 'trx_addons' ),
						'tab' => \Elementor\Controls_Manager::TAB_STYLE
					]
				);

				$this->add_control(
					'tour-style',
					[
						'label' => __( 'Tour Style', 'trx_addons' ),
						'label_block' => true,
						'type' => 'trx_icons',
						'mode' => 'inline',
						'return' => 'slug',
						'style' => 'images',
						'options' => apply_filters('trx_addons_sc_type', array(
														'full' => TOURMASTER_URL . '/images/tour-style/full.jpg',
														'full-with-frame' => TOURMASTER_URL . '/images/tour-style/full-with-frame.jpg',
														'medium' => TOURMASTER_URL . '/images/tour-style/medium.jpg',
														'medium-with-frame' => TOURMASTER_URL . '/images/tour-style/medium-with-frame.jpg',
														'modern' => TOURMASTER_URL . '/images/tour-style/modern.jpg',
														'modern-no-space' => TOURMASTER_URL . '/images/tour-style/modern-no-space.jpg',
														'grid' => TOURMASTER_URL . '/images/tour-style/grid.jpg',
														'grid-with-frame' => TOURMASTER_URL . '/images/tour-style/grid-with-frame.jpg',
														'grid-no-space' => TOURMASTER_URL . '/images/tour-style/grid-no-space.jpg',
														'widget' => TOURMASTER_URL . '/images/tour-style/widget.jpg',
														), 'tourmaster_tour' ),
						'default' => 'full'
					]
				);

				$this->add_control(
					'column-size',
					[
						'label' => __( 'Columns', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 3
						],
						'range' => [
							'px' => [
								'min' => 1,
								'max' => 5
							]
						],
						/* IDs with not '-' are not allowed in Elementor's conditions
						'condition' => [
							'tour-style' => ['modern', 'modern-no-space', 'grid', 'grid-with-frame', 'grid-no-space']
						]
						*/
					]
				);

				$this->add_control(
					'thumbnail-size',
					[
						'label' => __( 'Thumbnail Size', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_thumbnail_sizes(),
						'default' => 'thumbnail',
						/* IDs with not '-' are not allowed in Elementor's conditions
						'condition' => [
							'tour-style' => ['modern', 'modern-no-space', 'grid', 'grid-with-frame', 'grid-no-space', 'full', 'full-with-frame', 'medium', 'medium-with-frame']
						]
						*/
					]
				);

				$this->add_control(
					'layout',
					[
						'label' => __( 'Layout', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => [
							'fitrows' => esc_html__('Fit Rows', 'trx_addons'),
							'carousel' => esc_html__('Carousel', 'trx_addons'),
							'masonry' => esc_html__('Masonry', 'trx_addons'),
						],
						'default' => 'fitrows',
						/* IDs with not '-' are not allowed in Elementor's conditions
						'condition' => [
							'tour-style' => ['modern', 'modern-no-space', 'grid', 'grid-with-frame', 'grid-no-space']
						]
						*/
					]
				);

				$this->add_control(
					'price-position',
					[
						'label' => __( 'Price Display Position', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => [
							'right-title' => esc_html__('Right Side Of The Title', 'trx_addons'),
							'bottom-title' => esc_html__('Bottom Of The Title', 'trx_addons'),
							'bottom-bar' => esc_html__('As Bottom Bar', 'trx_addons'),
						],
						'default' => 'right-title',
						/* IDs with not '-' are not allowed in Elementor's conditions
						'condition' => [
							'tour-style' => ['grid', 'grid-with-frame', 'grid-no-space']
						]
						*/
					]
				);

				$this->add_control(
					'carousel-autoslide',
					[
						'label' => __( 'Autoslide Carousel', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => [
							'enable' => esc_html__('Enable', 'trx_addons'),
							'disable' => esc_html__('Disable', 'trx_addons')
						],
						'default' => 'enable',
						'condition' => [
							'layout' => 'carousel'
						]
					]
				);

				$this->add_control(
					'carousel-navigation',
					[
						'label' => __( 'Carousel Navigation', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => [
							'none' => esc_html__('None', 'trx_addons'),
							'navigation' => esc_html__('Only Navigation', 'trx_addons'),
							'bullet' => esc_html__('Only Bullet', 'trx_addons'),
							'both' => esc_html__('Both Navigation and Bullet', 'trx_addons'),
						],
						'default' => 'navigation',
						'condition' => [
							'layout' => 'carousel'
						]
					]
				);

				$this->add_control(
					'tour-info',
					[
						'label' => __( 'Tour Info', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT2,
						'options' => [
							'duration-text' => esc_html__('Duration', 'trx_addons'),
							'availability' => esc_html__('Availability', 'trx_addons'),
							'departure-location' => esc_html__('Departure Location', 'trx_addons'),
							'return-location' => esc_html__('Return Location', 'trx_addons'),
							'minimum-age' => esc_html__('Minimum Age', 'trx_addons'),
							'maximum-people' => esc_html__('Maximum People', 'trx_addons'),
							'custom-excerpt' => esc_html__('Custom Excerpt ( In Tour Option )', 'trx_addons'),
						],
						'multiple' => true,						
						'default' => ['duration-text'],
						/* IDs with not '-' are not allowed in Elementor's conditions
						'condition' => [
							'tour-style' => ['modern', 'modern-no-space', 'grid', 'grid-with-frame', 'grid-no-space', 'full', 'full-with-frame', 'medium', 'medium-with-frame']
						]
						*/
					]
				);

				$this->add_control(
					'tour-rating',
					[
						'label' => __( 'Tour Rating', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => [
							'enable' => esc_html__('Enable', 'trx_addons'),
							'disable' => esc_html__('Disable', 'trx_addons'),
						],
						'default' => 'enable',
						/* IDs with not '-' are not allowed in Elementor's conditions
						'condition' => [
							'tour-style' => ['full', 'full-with-frame', 'medium', 'medium-with-frame', 'grid', 'grid-with-frame', 'grid-no-space']
						]
						*/
					]
				);

				$this->add_control(
					'excerpt',
					[
						'label' => __( 'Excerpt Type', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => [
							'specify-number' => esc_html__('Specify Number', 'trx_addons'),
							'show-all' => esc_html__('Show All ( use <!--more--> tag to cut the content )', 'trx_addons'),
							'none' => esc_html__('Disable Exceprt', 'trx_addons'),
						],
						'default' => 'specify-number',
						/* IDs with not '-' are not allowed in Elementor's conditions
						'condition' => [
							'tour-style' => ['full', 'full-with-frame', 'medium', 'medium-with-frame', 'grid', 'grid-with-frame', 'grid-no-space']
						]
						*/
					]
				);

				$this->add_control(
					'excerpt-number',
					[
						'label' => __( 'Excerpt Words', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 55
						],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 200
							]
						],
						'condition' => [
							'excerpt' => 'specify-number'
						]
					]
				);

				$this->end_controls_section();
			}

			// Return widget's layout
			public function render() {
				if (shortcode_exists('tourmaster_tour')) {
					$atts = $this->sc_prepare_atts($this->get_settings(), $this->get_sc_name());
					trx_addons_show_layout(do_shortcode(sprintf('[tourmaster_tour'
																	. ' category="%1$s"'
																	. ' tag="%2$s"'
																	. ' discount-status="%3$s"'
																	. ' num-fetch="%4$s"'
																	. ' orderby="%5$s"'
																	. ' order="%6$s"'
																	. ' pagination="%7$s"'
																	. ' pagination-style="%8$s"'
																	. ' pagination-align="%9$s"'
																	. ' enable-order-filterer="%10$s"'
																	. ' order-filterer-list-style="%11$s"'
																	. ' order-filterer-list-style-thumbnail="%12$s"'
																	. ' order-filterer-grid-style="%13$s"'
																	. ' order-filterer-grid-style-thumbnail="%14$s"'
																	. ' tour-style="%15$s"'
																	. ' column-size="%16$s"'
																	. ' thumbnail-size="%17$s"'
																	. ' layout="%18$s"'
																	. ' price-position="%19$s"'
																	. ' carousel-autoslide="%20$s"'
																	. ' carousel-navigation="%21$s"'
																	. ' tour-info="%22$s"'
																	. ' tour-rating="%23$s"'
																	. ' excerpt="%24$s"'
																	. ' excerpt-number="%25$s"'
																	
																. ']',
																is_array($atts['category']) ? join(',', $atts['category']) : '',
																is_array($atts['tag']) ? join(',', $atts['tag']) : '',
																$atts['discount-status'],
																$atts['num-fetch'],
																$atts['orderby'],
																$atts['order'],
																$atts['pagination'],
																$atts['pagination-style'],
																$atts['pagination-align'],
																$atts['enable-order-filterer'],
																$atts['order-filterer-list-style'],
																$atts['order-filterer-list-style-thumbnail'],
																$atts['order-filterer-grid-style'],
																$atts['order-filterer-grid-style-thumbnail'],
																$atts['tour-style'],
																$atts['column-size'],
																$atts['thumbnail-size'],
																$atts['layout'],
																$atts['price-position'],
																$atts['carousel-autoslide'],
																$atts['carousel-navigation'],
																is_array($atts['tour-info']) ? join(',', $atts['tour-info']) : '',
																$atts['tour-rating'],
																$atts['excerpt'],
																$atts['excerpt-number']
															)));
				} else
					$this->shortcode_not_exists('tourmaster_tour', 'Tourmaster');
			}
		}
		
		// Register widget
		trx_addons_elm_register_widget( 'TRX_Addons_Elementor_Widget_Tourmaster_Tour' );
	}
}

if ( ! function_exists( 'trx_addons_sc_tourmaster_add_in_elementor_tts' ) ) {
	add_action( trx_addons_elementor_get_action_for_widgets_registration(), 'trx_addons_sc_tourmaster_add_in_elementor_tts' );
	/**
	 * Add shortcode Tourmaster Tour Search to the Elementor
	 *
	 * @hooked elementor/widgets/register
	 */
	function trx_addons_sc_tourmaster_add_in_elementor_tts() {

		if ( ! trx_addons_exists_tourmaster() || ! class_exists( 'TRX_Addons_Elementor_Widget' ) ) {
			return;
		}
		
		class TRX_Addons_Elementor_Widget_Tourmaster_Tour_Search extends TRX_Addons_Elementor_Widget {

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
					'padding-bottom' => 'size+unit',
					'frame-background-image' => 'url'
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
				return 'tourmaster_tour_search';
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
				return __( 'Tourmaster Search', 'trx_addons' );
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
				return 'eicon-image-hotspot';
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
					'section_sc_tourmaster_search',
					[
						'label' => __( 'Tour Search', 'trx_addons' ),
					]
				);

				$this->add_control(
					'title',
					[
						'label' => __( 'Title', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::TEXT,
						'default' => ''
					]
				);

				$this->add_control(
					'style',
					[
						'label' => __( 'Style', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => [
							'column' => esc_html__('Column', 'trx_addons'),
							'half' => esc_html__('Half', 'trx_addons'),
							'full' => esc_html__('Full', 'trx_addons'),
						],
						'default' => 'column'
					]
				);

				$this->add_control(
					'fields',
					[
						'label' => __( 'Fields', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT2,
						'options' => [
							'keywords' => esc_html__('Keywords', 'trx_addons'),
							'tour_category' => esc_html__('Category', 'trx_addons'),
							'tour_tag' => esc_html__('Tag', 'trx_addons'),
							'duration' => esc_html__('Duration', 'trx_addons'),
							'date' => esc_html__('Date', 'trx_addons'),
							'min-price' => esc_html__('Min Price', 'trx_addons'),
							'max-price' => esc_html__('Max Price', 'trx_addons'),
						],
						'multiple' => true,
						'default' => ['keywords','duration','date']
					]
				);
				
				$this->add_control(
					'padding-bottom',
					[
						'label' => __( 'Padding bottom', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 30,
							'unit' => 'px'
						],
						'size_units' => ['px', 'em'],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 150
							],
							'em' => [
								'min' => 0,
								'max' => 10
							]
						]
					]
				);

				$this->add_control(
					'enable-rating-field',
					[
						'label' => __( 'Enable Rating', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => array(
							'enable' => esc_html__('Enable', 'trx_addons'),
							'disable' => esc_html__('Disable', 'trx_addons'),
						),
						'default' => 'disable',
						'condition' => [
							'style' => 'full'
						]
					]
				);

				$this->add_control(
					'with-frame',
					[
						'label' => __( 'Item Frame', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => array(
							'disable' => esc_html__('Disable', 'trx_addons'),
							'enable' => esc_html__('Color Background', 'trx_addons'),
							'image' => esc_html__('Image Background', 'trx_addons'),
						),
						'default' => 'enable'
					]
				);

				$this->add_control(
					'frame-background-color',
					[
						'label' => __( 'Frame Background Color', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						/* IDs with not '-' are not allowed in Elementor's conditions
						'condition' => [
							'with-frame' => 'enable'
						]
						*/
					]
				);

				$this->add_control(
					'frame-background-image',
					[
						'label' => __( 'Frame Background Image', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::MEDIA,
						'default' => [
							'url' => '',
						],
						/* IDs with not '-' are not allowed in Elementor's conditions
						'condition' => [
							'with-frame' => 'image'
						]
						*/
					]
				);

				$this->end_controls_section();
			}

			// Return widget's layout
			public function render() {
				if (shortcode_exists('tourmaster_tour_search')) {
					$atts = $this->sc_prepare_atts($this->get_settings(), $this->get_sc_name());
					trx_addons_show_layout(do_shortcode(sprintf('[tourmaster_tour_search'
																	. ' title="%1$s"'
																	. ' style="%2$s"'
																	. ' fields="%3$s"'
																	. ' padding-bottom="%4$s"'
																	. ' enable-rating-field="%5$s"'
																	. ' with-frame="%6$s"'
																	. ' frame-background-color="%7$s"'
																	. ' frame-background-image="%8$s"'
																. ']',
																$atts['title'],
																$atts['style'],
																is_array($atts['fields']) ? join(',', $atts['fields']) : '',
																$atts['padding-bottom'],
																$atts['enable-rating-field'],
																$atts['with-frame'],
																$atts['frame-background-color'],
																$atts['frame-background-image']
															)));
				} else
					$this->shortcode_not_exists('tourmaster_tour_search', 'Tourmaster');
			}
		}
		
		// Register widget
		trx_addons_elm_register_widget( 'TRX_Addons_Elementor_Widget_Tourmaster_Tour_Search' );
	}
}
