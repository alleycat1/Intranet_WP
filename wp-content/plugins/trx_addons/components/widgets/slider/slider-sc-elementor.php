<?php
/**
 * Widget: Posts or Revolution slider (Elementor support)
 *
 * @package ThemeREX Addons
 * @since v1.0
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}



// Elementor Widget: Slider
//------------------------------------------------------
if (!function_exists('trx_addons_sc_slider_add_in_elementor')) {
	add_action( trx_addons_elementor_get_action_for_widgets_registration(), 'trx_addons_sc_slider_add_in_elementor' );
	function trx_addons_sc_slider_add_in_elementor() {
		
		if (!class_exists('TRX_Addons_Elementor_Widget')) return;	

		class TRX_Addons_Elementor_Widget_Slider extends TRX_Addons_Elementor_Widget {

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
					'height' => 'size+unit',
					'slides_per_view' => 'size',
					'slides_space' => 'size',
					'slides_parallax' => 'size',
					'speed' => 'size',
					'interval' => 'size',
					'controller_per_view' => 'size',
					'controller_space' => 'size',
					'controller_height' => 'size+unit',
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
				return 'trx_widget_slider';
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
				return __( 'Widget: Slider', 'trx_addons' );
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
				return 'eicon-slides';
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
					'section_sc_slider',
					[
						'label' => __( 'Slider', 'trx_addons' ),
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
					'engine',
					[
						'label' => __( 'Slider engine', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_slider_engines(),
						'default' => 'swiper'
					]
				);
				
				if (trx_addons_exists_revslider()) {
					$this->add_control(
						'alias',
						[
							'label' => __( 'RevSlider alias', 'trx_addons' ),
							'label_block' => false,
							'type' => \Elementor\Controls_Manager::SELECT,
							'options' => ! $is_edit_mode ? array() : trx_addons_get_list_revsliders(),
							'default' => '',
							'condition' => [
								'engine' => 'revo'
							]
						]
					);
				}

				$this->add_control(
					'slider_style',
					[
						'label' => __( 'Swiper style', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => trx_addons_components_get_allowed_layouts('widgets', 'slider'),
						'default' => 'default',
						'condition' => [
							'engine' => 'swiper'
						]
					]
				);

				$this->add_control(
					'effect',
					[
						'label' => __( 'Swiper effect', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_slider_effects(),
						'default' => 'slide',
						'condition' => [
							'engine' => 'swiper'
						]
					]
				);

				$this->add_control(
					'direction',
					[
						'label' => __( 'Slides change direction', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_slider_directions(),
						'default' => 'horizontal',
						'condition' => [
							'engine' => 'swiper',
							'effect' => ['slide', 'coverflow', 'swap']
						]
					]
				);

				$this->add_control(
					'slides_per_view',
					[
						'label' => __( 'Slides per view', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 1
						],
						'range' => [
							'px' => [
								'min' => 1,
								'max' => 10
							],
						],
						'condition' => [
							'engine' => 'swiper',
							'effect' => ['slide', 'coverflow', 'swap', 'cards', 'creative']
						]
					]
				);

				$this->add_control(
					'slides_space',
					[
						'label' => __( 'Space between slides', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 0
						],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 100
							],
						],
						'condition' => [
							'engine' => 'swiper',
							'effect' => ['slide', 'coverflow', 'swap', 'cards', 'creative']
						]
					]
				);

				$this->add_control(
					'slides_parallax',
					[
						'label' => __( 'Parallax coeff', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 0
						],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 1,
								'step' => 0.01
							],
						],
						'condition' => [
							'engine' => 'swiper',
							'effect' => ['slide'],
							'slides_per_view' => 1
						]
					]
				);

/*
				// Deprecated! Use Elementor's param "CSS ID" instead
				$this->add_control(
					'slider_id',
					[
						'label' => __( 'Slider ID', 'trx_addons' ),
						'label_block' => false,
						'description' => wp_kses_data(__('Specify ID if you want control this slider from Slider Controller or Slider Controls', 'trx_addons')),
						'type' => \Elementor\Controls_Manager::TEXT,
						'placeholder' => __( "Slider ID", 'trx_addons' ),
						'default' => ''
					]
				);
*/
				
				$this->add_control(
					'section_sc_slider_query',
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
						'condition' => [
							'engine' => ['swiper', 'elastistack']
						]
					]
				);

				$this->add_control(
					'taxonomy',
					[
						'label' => __( 'Taxonomy', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode
										? array()
										: trx_addons_get_list_taxonomies(false, $post_type),
						'default' => 'category',
						'condition' => [
							'engine' => ['swiper', 'elastistack']
						]
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
						'condition' => [
							'engine' => ['swiper', 'elastistack']
						]
					]
				);

				$this->add_control(
					'posts',
					[
						'label' => __( 'Posts number', 'trx_addons' ),
						'description' => wp_kses_data( __("Number of posts or comma separated post's IDs to show images", 'trx_addons') ),
						'type' => \Elementor\Controls_Manager::TEXT,
						'default' => '5',
						'condition' => [
							'engine' => ['swiper', 'elastistack']
						]
					]
				);
				
				$this->add_control(
					'slides',
					[
						'label' => '<b>' . __( 'or create custom slides', 'trx_addons' ) . '</b>',
						'separator' => 'before',
						'type' => \Elementor\Controls_Manager::REPEATER,
						'condition' => [
							'engine' => ['swiper', 'elastistack']
						],
						'fields' => apply_filters('trx_addons_sc_param_group_params',
							[
								[
									'name' => 'title',
									'label' => __( 'Title', 'trx_addons' ),
									'label_block' => false,
									'type' => \Elementor\Controls_Manager::TEXT,
									'placeholder' => __( "Slide's title", 'trx_addons' ),
									'default' => ''
								],
								[
									'name' => 'subtitle',
									'label' => __( 'Subtitle', 'trx_addons' ),
									'label_block' => false,
									'type' => \Elementor\Controls_Manager::TEXT,
									'placeholder' => __( "Slide's subtitle", 'trx_addons' ),
									'default' => ''
								],
								[
									'name' => 'meta',
									'label' => __( 'Meta', 'trx_addons' ),
									'label_block' => false,
									'type' => \Elementor\Controls_Manager::TEXT,
									'placeholder' => __( "Slide's meta", 'trx_addons' ),
									'default' => ''
								],
								[
									'name' => 'content',
									'label' => __( 'Content', 'trx_addons' ),
									'label_block' => true,
									'type' => \Elementor\Controls_Manager::WYSIWYG,
									'placeholder' => __( "Slide's content", 'trx_addons' ),
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
									'label' => __( 'Image', 'trx_addons' ),
									'type' => \Elementor\Controls_Manager::MEDIA,
									'default' => [
										'url' => '',
									],
								],
								[
									'name' => 'bg_color',
									'label' => __( 'Background Color', 'trx_addons' ),
									'type' => \Elementor\Controls_Manager::COLOR,
									'default' => '',
//									'global' => array(
//										'active' => false,
//									),
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
									'description' => __( 'or paste the HTML code to embed video in this slide', 'trx_addons' ),
									'type' => \Elementor\Controls_Manager::TEXTAREA,
									'rows' => 10,
									'separator' => 'none',
									'default' => '',
								]
							],
							'trx_widget_slider'
						),
						'title_field' => '{{{ title }}}',
					]
				);

				$this->add_control(
					'section_sc_slider_slave',
					[
						'label' => __( 'Slave slider', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::HEADING,
						'separator' => 'before'
					]
				);

				$this->add_control(
					'slave_id',
					[
						'label' => __( 'Slave ID', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::TEXT,
						'placeholder' => __( "Controlled slider ID", 'trx_addons' ),
						'default' => '',
						'condition' => [
							'engine' => 'swiper'
						]
					]
				);

				$this->end_controls_section();



				$this->start_controls_section(
					'section_sc_slider_layout',
					[
						'label' => __( 'Layout', 'trx_addons' ),
						'tab' => \Elementor\Controls_Manager::TAB_LAYOUT
					]
				);

				$this->add_control(
					'slides_type',
					[
						'label' => __( 'Type of the slides content', 'trx_addons' ),
						'label_block' => false,
						'description' => wp_kses_data( __("Use images from slides as background (default) or insert it as tag inside each slide", 'trx_addons') ),
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => array(
							'bg' => esc_html__('Background', 'trx_addons'),
							'images' => esc_html__('Image tag', 'trx_addons')
						),
						'default' => 'bg',
						'condition' => [
							'engine' => ['swiper', 'elastistack']
						]
					]
				);

				$this->add_control(
					'noresize',
					[
						'label' => __( "No resize slide's content", 'trx_addons' ),
						'label_block' => false,
						'description' => wp_kses_data( __("Disable resize slide's content, stretch images to cover slide", 'trx_addons') ),
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'return_value' => '1',
						'condition' => [
							'engine' => ['swiper', 'elastistack']
						]
					]
				);

				$this->add_control(
					'height',
					[
						'label' => __( 'Slider height', 'trx_addons' ),
						'description' => wp_kses_data( __("Initial height of the slider. If empty - calculate from width and aspect ratio", 'trx_addons') ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => '',
							'unit' => 'px'
						],
						'range' => [
							'px' => [
								'min' => 50,
								'max' => 1000
							],
							'em' => [
								'min' => 2,
								'max' => 100
							],
							'vh' => [
								'min' => 0,
								'max' => 100
							]
						],
						'size_units' => [ 'px', 'em', 'vh' ],
						'condition' => [
							'noresize' => '1'
						]
					]
				);

				$this->add_control(
					'slides_ratio',
					[
						'label' => __( 'Slides ratio', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::TEXT,
						'placeholder' => __( "Ratio", 'trx_addons' ),
						'default' => '16:9',
						'condition' => [
							'noresize' => ''
						]
					]
				);

				$this->add_control(
					'slides_centered',
					[
						'label' => __( 'Slides centered', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'return_value' => '1',
						'condition' => [
							'engine' => ['swiper']
						]
					]
				);

				$this->add_control(
					'slides_overflow',
					[
						'label' => __( 'Slides overflow visible', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'return_value' => '1',
						'condition' => [
							'engine' => ['swiper']
						]
					]
				);

				$this->add_control(
					'titles',
					[
						'label' => __( 'Titles in the slides', 'trx_addons' ),
						'label_block' => false,
						'description' => wp_kses_data( __("Show post's titles and categories on the slides", 'trx_addons') ),
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_slider_titles(),
						'default' => 'center',
						'condition' => [
							'engine' => ['swiper', 'elastistack']
						]
					]
				);

				$this->add_control(
					'large',
					[
						'label' => __( 'Large titles', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'return_value' => '1',
						'condition' => [
							'engine' => ['swiper', 'elastistack']
						]
					]
				);

				$this->end_controls_section();


				$this->start_controls_section(
					'section_sc_slider_controls',
					[
						'label' => __( 'Controls', 'trx_addons' ),
						'tab' => \Elementor\Controls_Manager::TAB_LAYOUT
					]
				);

				$this->add_control(
					'controls',
					[
						'label' => __( 'Controls', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'return_value' => '1',
						'condition' => [
							'engine' => ['swiper', 'elastistack']
						]
					]
				);

				$this->add_control(
					'controls_pos',
					[
						'label' => __( 'Controls position', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_slider_controls(''),
						'default' => 'side',
						'condition' => [
							'engine' => ['swiper'],
							'controls' => '1'
						]
					]
				);

				$this->add_control(
					'label_prev',
					[
						'label' => __( 'Prev Slide', 'trx_addons' ),
						'label_block' => false,
						'description' => wp_kses_data( __("Label of the 'Prev Slide' button in the Swiper (Modern style). Use '|' to break line", 'trx_addons') ),
						'type' => \Elementor\Controls_Manager::TEXT,
						'placeholder' => __( "Prev Slide", 'trx_addons' ),
						'default' => esc_html__('Prev|PHOTO', 'trx_addons'),
						'condition' => [
							'controls' => '1',
							'slider_style' => 'modern'
						]
					]
				);

				$this->add_control(
					'label_next',
					[
						'label' => __( 'Next Slide', 'trx_addons' ),
						'label_block' => false,
						'description' => wp_kses_data( __("Label of the 'Next Slide' button in the Swiper (Modern style). Use '|' to break line", 'trx_addons') ),
						'type' => \Elementor\Controls_Manager::TEXT,
						'placeholder' => __( "Next Slide", 'trx_addons' ),
						'default' => esc_html__('Next|PHOTO', 'trx_addons'),
						'condition' => [
							'controls' => '1',
							'slider_style' => 'modern'
						]
					]
				);

				$this->add_control(
					'pagination',
					[
						'label' => __( 'Pagination', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'return_value' => '1',
						'condition' => [
							'engine' => ['swiper']
						]
					]
				);

				$this->add_control(
					'pagination_type',
					[
						'label' => __( 'Pagination type', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_slider_paginations_types(),
						'default' => 'bullets',
						'condition' => [
							'engine' => ['swiper'],
							'pagination' => '1'
						]
					]
				);

				$this->add_control(
					'pagination_pos',
					[
						'label' => __( 'Pagination position', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_slider_paginations(''),
						'default' => 'bottom',
						'condition' => [
							'engine' => ['swiper'],
							'pagination' => '1'
						]
					]
				);

				$this->add_control(
					'noswipe',
					[
						'label' => __( 'Disable swipe', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'return_value' => '1',
						'condition' => [
							'engine' => ['swiper']
						]
					]
				);

				$this->add_control(
					'mouse_wheel',
					[
						'label' => __( 'Enable mouse wheel', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'return_value' => '1',
						'condition' => [
							'engine' => ['swiper']
						]
					]
				);

				$this->add_control(
					'free_mode',
					[
						'label' => __( 'Enable free mode', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'return_value' => '1',
						'condition' => [
							'engine' => ['swiper']
						]
					]
				);

				$this->add_control(
					'loop',
					[
						'label' => __( 'Enable loop mode', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'default' => '1',
						'return_value' => '1',
						'condition' => [
							'engine' => ['swiper'],
							'slides_overflow!' => '1'
						]
					]
				);

				$this->add_control(
					'autoplay',
					[
						'label' => __( 'Enable autoplay', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'default' => '1',
						'return_value' => '1',
						'condition' => [
							'engine' => ['swiper']
						]
					]
				);

				$this->add_control(
					'speed',
					[
						'label' => __( 'Slides change speed', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 600
						],
						'range' => [
							'px' => [
								'min' => 300,
								'max' => 3000,
								'step' => 50
							],
						],
						'condition' => [
							'engine' => 'swiper'
						]
					]
				);

				$this->add_control(
					'interval',
					[
						'label' => __( 'Interval between slides change', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 7000
						],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 10000,
								'step' => 100
							],
						],
						'condition' => [
							'engine' => 'swiper'
						]
					]
				);

				$this->end_controls_section();


				$this->start_controls_section(
					'section_sc_slider_controller',
					[
						'label' => __( 'Table of contents', 'trx_addons' ),
						'tab' => \Elementor\Controls_Manager::TAB_LAYOUT
					]
				);

				$this->add_control(
					'controller',
					[
						'label' => __( 'Show TOC', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'return_value' => '1',
						'condition' => [
							'engine' => ['swiper']
						]
					]
				);

				$this->add_control(
					'controller_style',
					[
						'label' => __( 'Style of the TOC', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_slider_toc_styles(),
						'default' => 'default',
						'condition' => [
							'controller' => '1'
						]
					]
				);

				$this->add_control(
					'controller_pos',
					[
						'label' => __( 'Position of the TOC', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_slider_toc_positions(),
						'default' => 'right',
						'condition' => [
							'controller' => '1'
						]
					]
				);

				$this->add_control(
					'controller_controls',
					[
						'label' => __( 'Show arrows', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'return_value' => '1',
						'condition' => [
							'controller' => '1'
						]
					]
				);

				$this->add_control(
					'controller_effect',
					[
						'label' => __( 'Effect for change items', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_slider_effects(),
						'default' => 'slide',
						'condition' => [
							'controller' => '1'
						]
					]
				);

				$this->add_control(
					'controller_per_view',
					[
						'label' => __( 'Items per view', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 3
						],
						'range' => [
							'px' => [
								'min' => 1,
								'max' => 10,
								'step' => 1
							],
						],
						'condition' => [
							'controller' => '1',
							'controller_effect' => ['slide','coverflow', 'swap', 'cards', 'creative']
						]
					]
				);

				$this->add_control(
					'controller_space',
					[
						'label' => __( 'Space between items', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 0
						],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 100,
								'step' => 1
							],
						],
						'condition' => [
							'controller' => '1'
						]
					]
				);

				$this->add_control(
					'controller_height',
					[
						'label' => __( 'Height of the TOC', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 0,
							'unit' => 'px'
						],
						'range' => [
							'px' => [
								'min' => 50,
								'max' => 300
							],
							'em' => [
								'min' => 2,
								'max' => 20
							],
						],
						'size_units' => [ 'px', 'em' ],
						'condition' => [
							'controller' => '1',
							'controller_pos' => [ 'bottom' ],
						]
					]
				);

				$this->end_controls_section();
			}
		}
		
		// Register widget
		trx_addons_elm_register_widget( 'TRX_Addons_Elementor_Widget_Slider' );
	}
}


// Disable our widgets (shortcodes) to use in Elementor
// because we create special Elementor's widgets instead
if (!function_exists('trx_addons_widget_slider_black_list')) {
	add_action( 'elementor/widgets/black_list', 'trx_addons_widget_slider_black_list' );
	function trx_addons_widget_slider_black_list($list) {
		$list[] = 'trx_addons_widget_slider';
		return $list;
	}
}



// Elementor Widget: Slider Controller
//------------------------------------------------------
if (!function_exists('trx_addons_sc_slider_controller_add_in_elementor')) {
	add_action( trx_addons_elementor_get_action_for_widgets_registration(), 'trx_addons_sc_slider_controller_add_in_elementor' );
	function trx_addons_sc_slider_controller_add_in_elementor() {
		
		if (!class_exists('TRX_Addons_Elementor_Widget')) return;	

		class TRX_Addons_Elementor_Widget_Slider_Controller extends TRX_Addons_Elementor_Widget {

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
					'height' => 'size+unit',
					'slides_per_view' => 'size',
					'slides_space' => 'size',
					'interval' => 'size'
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
				return 'trx_sc_slider_controller';
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
				return __( 'Slider Controller', 'trx_addons' );
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
				return 'eicon-slider-device';
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
					'section_sc_slider_controller',
					[
						'label' => __( 'Slider Controller', 'trx_addons' ),
					]
				);
				
				$this->add_control(
					'slider_id',
					[
						'label' => __( 'Slave slider ID', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::TEXT,
						'placeholder' => __( "Controlled ID", 'trx_addons' ),
						'default' => ''
					]
				);

				$this->add_control(
					'height',
					[
						'label' => __( 'Controller height', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 0,
							'unit' => 'px'
						],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 300
							],
							'em' => [
								'min' => 0,
								'max' => 20
							]
						],
						'size_units' => ['px', 'em']
					]
				);

				$this->add_control(
					'controls',
					[
						'label' => __( 'Controls', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'return_value' => '1'
					]
				);

				$this->add_control(
					'controller_style',
					[
						'label' => __( 'Style', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_slider_controller_styles(),
						'default' => 'thumbs'
					]
				);

				$this->add_control(
					'effect',
					[
						'label' => __( 'Swiper effect', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_slider_effects(),
						'default' => 'slide'
					]
				);

				$this->add_control(
					'direction',
					[
						'label' => __( 'Slides change direction', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_slider_directions(),
						'default' => 'horizontal',
						'condition' => [
							'effect' => ['slide']
						]
					]
				);

				$this->add_control(
					'slides_per_view',
					[
						'label' => __( 'Slides per view', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 3
						],
						'range' => [
							'px' => [
								'min' => 1,
								'max' => 10
							],
						],
						'condition' => [
							'effect' => ['slide', 'coverflow', 'swap', 'cards', 'creative']
						]
					]
				);

				$this->add_control(
					'slides_space',
					[
						'label' => __( 'Space between slides', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 0
						],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 100
							],
						],
						'condition' => [
							'effect' => ['slide', 'coverflow', 'swap', 'cards', 'creative']
						]
					]
				);

				$this->add_control(
					'interval',
					[
						'label' => __( 'Interval between slides change', 'trx_addons' ),
						'description' => __( 'If empty or 0 - do not apply autoplay to the controller', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 7000
						],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 10000,
								'step' => 100
							],
						]
					]
				);
				
				$this->end_controls_section();
			}

			/**
			 * Render widget's template for the editor.
			 *
			 * Written as a Backbone JavaScript template and used to generate the live preview.
			 *
			 * @since 1.6.41
			 * @access protected
			 */
			protected function content_template() {
				trx_addons_get_template_part(TRX_ADDONS_PLUGIN_WIDGETS . "slider/tpe.slider_controller.php",
										'trx_addons_args_widget_slider_controller',
										array('element' => $this)
									);
			}
		}
		
		// Register widget
		trx_addons_elm_register_widget( 'TRX_Addons_Elementor_Widget_Slider_Controller' );
	}
}




// Elementor Widget: Slider Controls
//------------------------------------------------------
if (!function_exists('trx_addons_sc_slider_controls_add_in_elementor')) {
	add_action( trx_addons_elementor_get_action_for_widgets_registration(), 'trx_addons_sc_slider_controls_add_in_elementor' );
	function trx_addons_sc_slider_controls_add_in_elementor() {
		
		if (!class_exists('TRX_Addons_Elementor_Widget')) return;	

		class TRX_Addons_Elementor_Widget_Slider_Controls extends TRX_Addons_Elementor_Widget {

			/**
			 * Retrieve widget name.
			 *
			 * @since 1.6.41
			 * @access public
			 *
			 * @return string Widget name.
			 */
			public function get_name() {
				return 'trx_sc_slider_controls';
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
				return __( 'Slider Controls', 'trx_addons' );
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
				return 'eicon-post-navigation';
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
					'section_sc_slider_controls',
					[
						'label' => __( 'Slider Controls', 'trx_addons' ),
					]
				);
				
				$this->add_control(
					'slider_id',
					[
						'label' => __( 'Slave slider ID', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::TEXT,
						'placeholder' => __( "Controlled ID", 'trx_addons' ),
						'default' => ''
					]
				);

				$this->add_control(
					'controls_style',
					[
						'label' => __( 'Style', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_slider_controls_styles(),
						'default' => 'default'
					]
				);

				$this->add_control(
					'align',
					[
						'label' => __( 'Alignment', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_aligns(false, false),
						'default' => 'left'
					]
				);

				$this->add_control(
					'hide_prev',
					[
						'label' => __( "Hide the button 'Prev'", 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'return_value' => '1'
					]
				);

				$this->add_control(
					'title_prev',
					[
						'label' => __( "Title of the button 'Prev'", 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::TEXT,
						'placeholder' => __( "Prev", 'trx_addons' ),
						'default' => '',
						'condition' => [
							'hide_prev' => ''
						]
					]
				);

				$this->add_control(
					'hide_next',
					[
						'label' => __( "Hide the button 'Next'", 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'return_value' => '1'
					]
				);

				$this->add_control(
					'title_next',
					[
						'label' => __( "Title of the button 'Next'", 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::TEXT,
						'placeholder' => __( "Next", 'trx_addons' ),
						'default' => '',
						'condition' => [
							'hide_next' => ''
						]
					]
				);

				$this->add_control(
					'pagination_style',
					[
						'label' => __( "Show pagination", 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_slider_controls_paginations_types(),
						'default' => 'none'
					]
				);

				$this->end_controls_section();
			}

			/**
			 * Render widget output in the editor.
			 *
			 * Written as a Backbone JavaScript template and used to generate the live preview.
			 *
			 * @since 1.6.41
			 * @access protected
			 */
			protected function content_template() {
				trx_addons_get_template_part(TRX_ADDONS_PLUGIN_WIDGETS . "slider/tpe.slider_controls.php",
										'trx_addons_args_widget_slider_controls',
										array('element' => $this)
									);
			}
		}
		
		// Register widget
		trx_addons_elm_register_widget( 'TRX_Addons_Elementor_Widget_Slider_Controls' );
	}
}
