<?php
/**
 * Widget: Popular posts (Elementor support)
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
if (!function_exists('trx_addons_sc_widget_popular_posts_add_in_elementor')) {
	add_action( trx_addons_elementor_get_action_for_widgets_registration(), 'trx_addons_sc_widget_popular_posts_add_in_elementor' );
	function trx_addons_sc_widget_popular_posts_add_in_elementor() {
		
		if (!class_exists('TRX_Addons_Elementor_Widget')) return;	

		class TRX_Addons_Elementor_Widget_Popular_Posts extends TRX_Addons_Elementor_Widget {

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
					'number' => 'size'
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
				return 'trx_widget_popular_posts';
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
				return __( 'Widget: Popular Posts', 'trx_addons' );
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
				return 'eicon-post-list';
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
				// If open params in Elementor Editor
				$params = $this->get_sc_params();
				// Prepare lists
				$post_type_1 = !empty($params['post_type_1']) ? $params['post_type_1'] : 'post';
				$taxonomy_1 = !empty($params['taxonomy_1']) ? $params['taxonomy_1'] : 'category';
				$tax_obj_1 = get_taxonomy($taxonomy_1);
				$post_type_2 = !empty($params['post_type_2']) ? $params['post_type_2'] : 'post';
				$taxonomy_2 = !empty($params['taxonomy_2']) ? $params['taxonomy_2'] : 'category';
				$tax_obj_2 = get_taxonomy($taxonomy_2);
				$post_type_3 = !empty($params['post_type_3']) ? $params['post_type_3'] : 'post';
				$taxonomy_3 = !empty($params['taxonomy_3']) ? $params['taxonomy_3'] : 'category';
				$tax_obj_3 = get_taxonomy($taxonomy_3);

				$this->start_controls_section(
					'section_sc_popular_posts',
					[
						'label' => __( 'Widget: Popular Posts', 'trx_addons' ),
					]
				);

				$this->add_control(
					'title',
					[
						'label' => __( 'Title', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::TEXT,
						'placeholder' => __( "Widget title", 'trx_addons' ),
						'default' => ''
					]
				);

				$this->add_control(
					'number',
					[
						'label' => __( 'Number posts to show', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 4,
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
					'details',
					[
						'label' => __( 'Details', 'elementor' ),
						'type' => \Elementor\Controls_Manager::HEADING,
						'separator' => 'before',
					]
				);

				$this->add_control(
					'show_image',
					[
						'label' => __( "Show post's image", 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Hide', 'trx_addons' ),
						'label_on' => __( 'Show', 'trx_addons' ),
						'default' => '1',
						'return_value' => '1'
					]
				);

				$this->add_control(
					'show_author',
					[
						'label' => __( "Show post's author", 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Hide', 'trx_addons' ),
						'label_on' => __( 'Show', 'trx_addons' ),
						'default' => '1',
						'return_value' => '1'
					]
				);

				$this->add_control(
					'show_date',
					[
						'label' => __( "Show post's date", 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Hide', 'trx_addons' ),
						'label_on' => __( 'Show', 'trx_addons' ),
						'default' => '1',
						'return_value' => '1'
					]
				);

				$this->add_control(
					'show_counters',
					[
						'label' => __( "Show post's counters", 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Hide', 'trx_addons' ),
						'label_on' => __( 'Show', 'trx_addons' ),
						'default' => '1',
						'return_value' => '1'
					]
				);

				$this->add_control(
					'show_categories',
					[
						'label' => __( "Show post's categories", 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Hide', 'trx_addons' ),
						'label_on' => __( 'Show', 'trx_addons' ),
						'default' => '1',
						'return_value' => '1'
					]
				);

				$this->end_controls_section();

				$this->start_controls_section(
					'section_sc_popular_posts_tabs',
					[
						'label' => __( 'Tabs', 'trx_addons' ),
						'tab' => \Elementor\Controls_Manager::TAB_LAYOUT
					]
				);

				$this->add_control(
					'tab_1',
					[
						'label' => __( 'Tab 1', 'elementor' ),
						'type' => \Elementor\Controls_Manager::HEADING,
						'separator' => 'none',
					]
				);

				$this->add_control(
					'title_1',
					[
						'label' => __( 'Tab title', 'trx_addons' ),
						'label_block' => false,
						'description' => __( 'If empty - tab is not showed!', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::TEXT,
						'placeholder' => __( "Popular", 'trx_addons' ),
						'default' => __( "Popular", 'trx_addons' )
					]
				);

				$this->add_control(
					'orderby_1',
					[
						'label' => __( 'Order by', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_widget_query_orderby(),
						'default' => 'views'
					]
				);

				$this->add_control(
					'post_type_1',
					[
						'label' => __( 'Post type', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_posts_types(),
						'default' => 'post'
					]
				);

				$this->add_control(
					'taxonomy_1',
					[
						'label' => __( 'Taxonomy', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_taxonomies(false, $post_type_1),
						'default' => 'category'
					]
				);

				$this->add_control(
					'cat_1',
					[
						'label' => __( 'Category', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode
										? array()
										: trx_addons_array_merge(
											array( 0 => trx_addons_get_not_selected_text( ! empty( $tax_obj_1->label ) ? $tax_obj_1->label : __( '- Not Selected -', 'trx_addons' ) ) ),
											$taxonomy_1 == 'category' 
												? trx_addons_get_list_categories() 
												: trx_addons_get_list_terms(false, $taxonomy_1)
											),
						'default' => '0'
					]
				);

				$this->add_control(
					'tab_2',
					[
						'label' => __( 'Tab 2', 'elementor' ),
						'type' => \Elementor\Controls_Manager::HEADING,
						'separator' => 'before',
					]
				);

				$this->add_control(
					'title_2',
					[
						'label' => __( 'Tab title', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::TEXT,
						'placeholder' => __( "Commented", 'trx_addons' ),
						'default' => __( "Commented", 'trx_addons' )
					]
				);

				$this->add_control(
					'orderby_2',
					[
						'label' => __( 'Order by', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_widget_query_orderby(),
						'default' => 'comments'
					]
				);

				$this->add_control(
					'post_type_2',
					[
						'label' => __( 'Post type', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_posts_types(),
						'default' => 'post'
					]
				);

				$this->add_control(
					'taxonomy_2',
					[
						'label' => __( 'Taxonomy', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_taxonomies(false, $post_type_2),
						'default' => 'category'
					]
				);

				$this->add_control(
					'cat_2',
					[
						'label' => __( 'Category', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode
										? array()
										: trx_addons_array_merge(
											array( 0 => trx_addons_get_not_selected_text( ! empty( $tax_obj_2->label ) ? $tax_obj_2->label : __( '- Not Selected -', 'trx_addons' ) ) ),
											$taxonomy_2 == 'category' 
												? trx_addons_get_list_categories() 
												: trx_addons_get_list_terms(false, $taxonomy_2)
											),
						'default' => '0'
					]
				);

				$this->add_control(
					'tab_3',
					[
						'label' => __( 'Tab 3', 'elementor' ),
						'type' => \Elementor\Controls_Manager::HEADING,
						'separator' => 'before',
					]
				);

				$this->add_control(
					'title_3',
					[
						'label' => __( 'Tab title', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::TEXT,
						'placeholder' => __( "Liked", 'trx_addons' ),
						'default' => __( "Liked", 'trx_addons' )
					]
				);

				$this->add_control(
					'orderby_3',
					[
						'label' => __( 'Order by', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_widget_query_orderby(),
						'default' => 'likes'
					]
				);

				$this->add_control(
					'post_type_3',
					[
						'label' => __( 'Post type', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_posts_types(),
						'default' => 'post'
					]
				);

				$this->add_control(
					'taxonomy_3',
					[
						'label' => __( 'Taxonomy', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_taxonomies(false, $post_type_3),
						'default' => 'category'
					]
				);

				$this->add_control(
					'cat_3',
					[
						'label' => __( 'Category', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode
										? array()
										: trx_addons_array_merge(
											array( 0 => trx_addons_get_not_selected_text( ! empty( $tax_obj_3->label ) ? $tax_obj_3->label : __( '- Not Selected -', 'trx_addons' ) ) ),
											$taxonomy_3 == 'category' 
												? trx_addons_get_list_categories() 
												: trx_addons_get_list_terms(false, $taxonomy_3)
											),
						'default' => '0'
					]
				);

				$this->end_controls_section();
			}
		}
		
		// Register widget
		trx_addons_elm_register_widget( 'TRX_Addons_Elementor_Widget_Popular_Posts' );
	}
}


// Disable our widgets (shortcodes) to use in Elementor
// because we create special Elementor's widgets instead
if (!function_exists('trx_addons_widget_popular_posts_black_list')) {
	add_action( 'elementor/widgets/black_list', 'trx_addons_widget_popular_posts_black_list' );
	function trx_addons_widget_popular_posts_black_list($list) {
		$list[] = 'trx_addons_widget_popular_posts';
		return $list;
	}
}
