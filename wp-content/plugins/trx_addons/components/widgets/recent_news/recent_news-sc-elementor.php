<?php
/**
 * Widget: Recent News (Elementor support)
 *
 * @package ThemeREX Addons
 * @since v1.0
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}



// Elementor Widget
//------------------------------------------------------
if (!function_exists('trx_addons_sc_widget_recent_news_add_in_elementor')) {
	add_action( trx_addons_elementor_get_action_for_widgets_registration(), 'trx_addons_sc_widget_recent_news_add_in_elementor' );
	function trx_addons_sc_widget_recent_news_add_in_elementor() {
		
		if (!class_exists('TRX_Addons_Elementor_Widget')) return;	

		class TRX_Addons_Elementor_Widget_Recent_News extends TRX_Addons_Elementor_Widget {

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
					'offset' => 'size',
					'featured' => 'size'
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
				return 'trx_sc_recent_news';
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
				return __( 'Widget: Recent News', 'trx_addons' );
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
				return 'eicon-gallery-group';
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
				return ['trx_addons-widgets'];
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
					'section_sc_recent_news',
					[
						'label' => __( 'Widget: Recent News', 'trx_addons' ),
					]
				);
				
				$this->add_control(
					'widget_title',
					[
						'label' => __( 'Widget Title', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::TEXT,
						'placeholder' => __( "Widget title", 'trx_addons' ),
						'default' => ''
					]
				);
				
				$this->add_control(
					'title',
					[
						'label' => __( 'Title', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::TEXT,
						'placeholder' => __( "Title of the block", 'trx_addons' ),
						'default' => ''
					]
				);
				
				$this->add_control(
					'subtitle',
					[
						'label' => __( 'Subtitle', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::TEXT,
						'placeholder' => __( "Subtitle of the block", 'trx_addons' ),
						'default' => ''
					]
				);

				$this->add_control(
					'style',
					[
						'label' => __( 'List style', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('widgets', 'recent_news'), 'trx_widget_recent_news'),
						'default' => 'news-magazine'
					]
				);

				$this->add_control(
					'show_categories',
					[
						'label' => __( 'Show categories as dropdown', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Hide', 'trx_addons' ),
						'label_on' => __( 'Show', 'trx_addons' ),
						'return_value' => '1'
					]
				);

				$this->end_controls_section();

				$this->start_controls_section(
					'section_sc_recent_news_query',
					[
						'label' => __( 'Query details', 'trx_addons' ),
					]
				);
				
				$this->add_control(
					'ids',
					[
						'label' => __( 'List IDs', 'trx_addons' ),
						'label_block' => false,
						'description' => wp_kses_data( __("Comma separated list of IDs list to display. If not empty, parameters 'cat', 'offset' and 'count' are ignored!", 'trx_addons') ),
						'type' => \Elementor\Controls_Manager::TEXT,
						'placeholder' => __( "Posts ID ...", 'trx_addons' ),
						'default' => ''
					]
				);

				$this->add_control(
					'category',
					[
						'label' => __( 'Category', 'trx_addons' ),
						'label_block' => false,
						'description' => wp_kses_data( __("Select a category to display. If empty - select news from any category or from the IDs list.", 'trx_addons') ),
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_array_merge( array( 0 => trx_addons_get_not_selected_text( esc_html__( 'Select category', 'trx_addons' ) ) ), trx_addons_get_list_categories() ),
						'default' => '',
						'condition' => [
							'ids' => ''
						]
					]
				);

				$this->add_control(
					'count',
					[
						'label' => __( 'Total posts', 'trx_addons' ),
						'description' => wp_kses_data( __("The number of displayed posts. If IDs are used, this parameter is ignored.", 'trx_addons') ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 3
						],
						'range' => [
							'px' => [
								'min' => 1,
								'max' => 30
							],
						],
						'condition' => [
							'ids' => ''
						]
					]
				);

				$this->add_control(
					'columns',
					[
						'label' => __( 'Columns', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 3
						],
						'range' => [
							'px' => [
								'min' => 1,
								'max' => 12
							],
						],
						'condition' => [
							'style' => ['news-magazine', 'news-portfolio']
						]
					]
				);

				$this->add_control(
					'offset',
					[
						'label' => __( 'Offset before select posts', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 0
						],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 30
							],
						],
						'condition' => [
							'ids' => ''
						]
					]
				);

				$this->add_control(
					'featured',
					[
						'label' => __( 'Featured posts', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 3
						],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 30
							],
						],
						'condition' => [
							'style' => ['news-magazine']
						]
					]
				);

				$this->add_control(
					'orderby',
					[
						'label' => __( 'Posts sorting', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => array(
							"none" 		=> esc_html__('None', 'trx_addons'),
							"ID" 		=> esc_html__('Post ID', 'trx_addons'),
							"date"		=> esc_html__("Date", 'trx_addons'),
							"title"		=> esc_html__("Alphabetically", 'trx_addons'),
							"views"		=> esc_html__("Popular (views count)", 'trx_addons'),
							"comments"	=> esc_html__("Most commented (comments count)", 'trx_addons'),
							"random"	=> esc_html__("Random", 'trx_addons')
						),
						'default' => 'none'
					]
				);

				$this->add_control(
					'order',
					[
						'label' => __( 'Posts order', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => array(
							"asc"  => esc_html__("Ascending", 'trx_addons'),
							"desc" => esc_html__("Descending", 'trx_addons')
						),
						'default' => 'asc'
					]
				);

				$this->end_controls_section();
			}
		}
		
		// Register widget
		trx_addons_elm_register_widget( 'TRX_Addons_Elementor_Widget_Recent_News' );
	}
}


// Disable our widgets (shortcodes) to use in Elementor
// because we create special Elementor's widgets instead
if (!function_exists('trx_addons_widget_recent_news_black_list')) {
	add_action( 'elementor/widgets/black_list', 'trx_addons_widget_recent_news_black_list' );
	function trx_addons_widget_recent_news_black_list($list) {
		$list[] = 'trx_addons_widget_recent_news';
		return $list;
	}
}
