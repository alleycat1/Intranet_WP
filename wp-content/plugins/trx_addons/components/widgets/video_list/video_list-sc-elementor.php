<?php
/**
 * Widget: Video list for Youtube, Vimeo, etc. embeded video (Elementor support)
 *
 * @package ThemeREX Addons
 * @since v1.78.0
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}



// Elementor Widget
//------------------------------------------------------
if (!function_exists('trx_addons_sc_widget_video_list_add_in_elementor')) {
	add_action( trx_addons_elementor_get_action_for_widgets_registration(), 'trx_addons_sc_widget_video_list_add_in_elementor' );
	function trx_addons_sc_widget_video_list_add_in_elementor() {
		
		if (!class_exists('TRX_Addons_Elementor_Widget')) return;	

		class TRX_Addons_Elementor_Widget_video_list extends TRX_Addons_Elementor_Widget {

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
					'controller_height' => 'size+unit'
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
				return 'trx_widget_video_list';
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
				return __( 'Widget: Video List', 'trx_addons' );
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
				return 'eicon-youtube';
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
					'section_sc_video_list',
					[
						'label' => __( 'Widget: Video List', 'trx_addons' ),
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
					'autoplay',
					[
						'label' => __( 'Autoplay the first video', 'trx_addons' ),
						'label_block' => false,
						'label_on' => __( 'On', 'trx_addons' ),
						'label_off' => __( 'Off', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'return_value' => '1',
						'default' => '',
					]
				);

				$this->add_control(
					'section_sc_video_list_query',
					[
						'label' => __( 'Query params', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::HEADING,
						'separator' => 'before'
					]
				);

				$this->add_control(
					'post_type',
					[
						'label' => __( 'Post type', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_posts_types(),
						'default' => 'post',
					]
				);

				$this->add_control(
					'taxonomy',
					[
						'label' => __( 'Taxonomy', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_taxonomies(false, $post_type),
						'default' => 'category',
					]
				);

				$this->add_control(
					'category',
					[
						'label' => __( 'Category', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
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
						'default' => '0',
					]
				);

				$this->add_query_param( '', array('columns' => false) );

				$this->add_control(
					'videos',
					[
						'label' => esc_html__( 'or create custom videos', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::REPEATER,
						'fields' => apply_filters('trx_addons_sc_param_group_params',
							[
								[
									'name' => 'title',
									'label' => __( 'Title', 'trx_addons' ),
									'label_block' => false,
									'type' => \Elementor\Controls_Manager::TEXT,
									'placeholder' => __( "Video's title", 'trx_addons' ),
									'default' => ''
								],
								[
									'name' => 'subtitle',
									'label' => __( 'Subtitle', 'trx_addons' ),
									'label_block' => false,
									'type' => \Elementor\Controls_Manager::TEXT,
									'placeholder' => __( "Video's subtitle", 'trx_addons' ),
									'default' => ''
								],
								[
									'name' => 'meta',
									'label' => __( 'Meta', 'trx_addons' ),
									'label_block' => false,
									'type' => \Elementor\Controls_Manager::TEXT,
									'placeholder' => __( "Video's meta", 'trx_addons' ),
									'default' => ''
								],
								[
									'name' => 'link',
									'label' => __( 'Link', 'trx_addons' ),
									'label_block' => false,
									'type' => \Elementor\Controls_Manager::URL,
									'default' => ['url' => ''],
									'placeholder' => __( '//your-link.com', 'trx_addons' ),
								],
								[
									'name' => 'image',
									'label' => __( 'Cover image', 'trx_addons' ),
									'type' => \Elementor\Controls_Manager::MEDIA,
									'default' => [
										'url' => '',
									],
								],
								[
									'name' => 'video_url',
									'label' => __( 'Video URL', 'trx_addons' ),
									'label_block' => false,
									'description' => __( 'Enter link to the video (Note: read more about available formats at WordPress Codex page)', 'trx_addons' ),
									'type' => \Elementor\Controls_Manager::TEXT,
									'default' => '',
								],
								[
									'name' => 'video_embed',
									'label' => __( 'Video embed code', 'trx_addons' ),
									'label_block' => true,
									'description' => __( 'or paste the HTML code to embed video', 'trx_addons' ),
									'type' => \Elementor\Controls_Manager::TEXTAREA,
									'rows' => 10,
									'separator' => 'none',
									'default' => '',
								]
							],
							'trx_widget_video_list'
						),
						'title_field' => '{{{ title }}}',
					]
				);

				$this->end_controls_section();


				$this->start_controls_section(
					'section_sc_video_list_controller',
					[
						'label' => __( 'Table of contents (TOC)', 'trx_addons' ),
						'tab' => \Elementor\Controls_Manager::TAB_LAYOUT
					]
				);

				$this->add_control(
					'controller_style',
					[
						'label' => __( 'Style of the TOC', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_video_list_controller_styles(),
						'default' => 'default',
					]
				);

				$this->add_control(
					'controller_pos',
					[
						'label' => __( 'Position of the TOC', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_video_list_controller_positions(),
						'default' => 'right',
					]
				);

				$this->add_control(
					'controller_height',
					[
						'label' => __( 'Max. height of the TOC', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 0,
							'unit' => 'px'
						],
						'range' => [
							'px' => [
								'min' => 50,
								'max' => 200
							],
							'em' => [
								'min' => 2,
								'max' => 20
							],
						],
						'size_units' => [ 'px', 'em' ],
						'condition' => [
							'controller_pos' => [ 'bottom' ],
						]
					]
				);

				$this->add_control(
					'controller_autoplay',
					[
						'label' => __( 'Autoplay the selected video', 'trx_addons' ),
						'label_block' => false,
						'label_on' => __( 'On', 'trx_addons' ),
						'label_off' => __( 'Off', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'return_value' => '1',
						'default' => '1',
					]
				);

				$this->add_control(
					'controller_link',
					[
						'label' => __( 'Show the video or go to the post', 'trx_addons' ),
						'label_block' => false,
						'label_on' => __( 'Video', 'trx_addons' ),
						'label_off' => __( 'Post', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'return_value' => '1',
						'default' => '1',
					]
				);

				$this->end_controls_section();
			}
		}
		
		// Register widget
		trx_addons_elm_register_widget( 'TRX_Addons_Elementor_Widget_video_list' );
	}
}


// Disable our widgets (shortcodes) to use in Elementor
// because we create special Elementor's widgets instead
if (!function_exists('trx_addons_widget_video_list_black_list')) {
	add_action( 'elementor/widgets/black_list', 'trx_addons_widget_video_list_black_list' );
	function trx_addons_widget_video_list_black_list($list) {
		$list[] = 'trx_addons_widget_video_list';
		return $list;
	}
}
