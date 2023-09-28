<?php
/**
 * Widget: Categories list (Elementor support)
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
if (!function_exists('trx_addons_sc_widget_categories_list_add_in_elementor')) {
	add_action( trx_addons_elementor_get_action_for_widgets_registration(), 'trx_addons_sc_widget_categories_list_add_in_elementor' );
	function trx_addons_sc_widget_categories_list_add_in_elementor() {
		
		if (!class_exists('TRX_Addons_Elementor_Widget')) return;	

		class TRX_Addons_Elementor_Widget_Categories_List extends TRX_Addons_Elementor_Widget {

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
				return 'trx_widget_categories_list';
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
				return __( 'Widget: Categories List', 'trx_addons' );
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
				return 'eicon-posts-grid';
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
				$post_type = !empty($params['post_type']) ? $params['post_type'] : 'post';
				$taxonomy = !empty($params['taxonomy']) ? $params['taxonomy'] : 'category';
				$tax_obj = get_taxonomy($taxonomy);

				$this->start_controls_section(
					'section_sc_categories_list',
					[
						'label' => __( 'Widget: Categories List', 'trx_addons' ),
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
					'style',
					[
						'label' => __( 'Style', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('widgets', 'categories_list'), 'trx_widget_categories_list'),
						'default' => 1,
					]
				);

				$this->add_control(
					'post_type',
					[
						'label' => __( 'Post type', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_posts_types(),
						'default' => 'post'
					]
				);

				$this->add_control(
					'taxonomy',
					[
						'label' => __( 'Taxonomy', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_taxonomies(false, $post_type),
						'default' => 'category'
					]
				);

				$this->add_control(
					'cat_list',
					[
						'label' => __( 'Categories', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SELECT2,
						'options' => ! $is_edit_mode
										? array()
											// Make keys as string (add a space after the number) to preserve the order in the list
											// (otherwise the keys will be converted to numbers in the JS and the order will be broken)
										: trx_addons_array_make_string_keys(
												trx_addons_array_merge(
													array( 0 => trx_addons_get_not_selected_text( ! empty( $tax_obj->label ) ? $tax_obj->label : __( '- Not Selected -', 'trx_addons' ) ) ),
													$taxonomy == 'category' 
														? trx_addons_get_list_categories() 
														: trx_addons_get_list_terms(false, $taxonomy)
												)
											),
						'multiple' => true,
						'default' => []
					]
				);
				
				$this->add_control(
					'number',
					[
						'label' => __( 'Number', 'trx_addons' ),
						'description' => wp_kses_data( __("Specify the number of categories to show", 'trx_addons') ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 0
						],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 12
							]
						]
					]
				);

				$this->add_responsive_control(
					'columns',
					[
						'label' => __( 'Columns', 'trx_addons' ),
						'description' => wp_kses_data( __("Specify the number of columns. If left empty or assigned the value '0' - auto detect by the number of items.", 'trx_addons') ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 0
						],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 12
							]
						]
					]
				);

				$this->add_control(
					'show_thumbs',
					[
						'label' => __( 'Show thumbs', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'default' => '1',
						'return_value' => '1'
					]
				);

				$this->add_control(
					'show_posts',
					[
						'label' => __( 'Show posts number', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'default' => '1',
						'return_value' => '1'
					]
				);

				$this->add_control(
					'show_children',
					[
						'label' => __( 'Show children', 'trx_addons' ),
						'label_block' => false,
						'description' => wp_kses_data( __("Show only children of the current category", 'trx_addons') ),
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'default' => '0',
						'return_value' => '1'
					]
				);

				$this->end_controls_section();

				$this->add_slider_param();
			}
		}
		
		// Register widget
		trx_addons_elm_register_widget( 'TRX_Addons_Elementor_Widget_Categories_List' );
	}
}


// Disable our widgets (shortcodes) to use in Elementor
// because we create special Elementor's widgets instead
if (!function_exists('trx_addons_widget_categories_list_black_list')) {
	add_action( 'elementor/widgets/black_list', 'trx_addons_widget_categories_list_black_list' );
	function trx_addons_widget_categories_list_black_list($list) {
		$list[] = 'trx_addons_widget_categories_list';
		return $list;
	}
}
