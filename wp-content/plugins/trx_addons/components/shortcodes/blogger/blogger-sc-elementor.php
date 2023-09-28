<?php
/**
 * Shortcode: Blogger (Elementor support)
 *
 * @package ThemeREX Addons
 * @since v1.2
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}



// Elementor Widget
//------------------------------------------------------
if (!function_exists('trx_addons_sc_blogger_add_in_elementor')) {
	add_action( trx_addons_elementor_get_action_for_widgets_registration(), 'trx_addons_sc_blogger_add_in_elementor' );
	function trx_addons_sc_blogger_add_in_elementor() {
		
		if (!class_exists('TRX_Addons_Elementor_Widget')) return;	

		class TRX_Addons_Elementor_Widget_Blogger extends TRX_Addons_Elementor_Widget {

			/**
			 * Widget base constructor.
			 *
			 * Initializing the widget base class.
			 *
			 * @since 1.6.54
			 * @access public
			 *
			 * @param array      $data Widget data. Default is an empty array.
			 * @param array|null $args Optional. Widget default arguments. Default is null.
			 */
			public function __construct( $data = [], $args = null ) {
				parent::__construct( $data, $args );
				$this->add_plain_params([
					'image_width' => 'size'
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
				return 'trx_sc_blogger';
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
				return __( 'Blogger', 'trx_addons' );
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
				return 'eicon-image-box';
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

				$layouts = ! $is_edit_mode ? array() : apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('sc', 'blogger'), 'trx_sc_blogger' );
				$templates = ! $is_edit_mode ? array() : trx_addons_components_get_allowed_templates('sc', 'blogger', $layouts);

				// Section: Blogger (General)
				$this->start_controls_section(
					'section_sc_blogger',
					[
						'label' => __( 'Blogger', 'trx_addons' ),
					]
				);

				$this->add_control(
					'type',
					[
						'label' => __( 'Layout', 'trx_addons' ),
						'label_block' => false,

//						This way to show param as images set
//						'show_label' => false,
//						'type' => 'trx_icons',
//						"mode" => 'inline',
//						"return" => 'slug',
//						"style" => "images",

//						Default way - show select with shortcode's layouts
						'type' => \Elementor\Controls_Manager::SELECT,						
						'options' => $layouts,
						'default' => 'default',
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
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_taxonomies(false, $post_type),
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

				$this->add_query_param('');

				$this->add_control(
					'pagination',
					[
						'label' => __( 'Pagination', 'trx_addons' ),
						'label_block' => false,
						'description' => wp_kses_data( __("Add pagination links after posts. Attention! If slider is active, pagination is not allowed!", 'trx_addons') ),
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_paginations(),
						'default' => 'none',
						'condition' => [
							'type!' => [ 'cards' ]
						]
					]
				);

				// Filters
				$this->add_control(
					'heading_filters',
					[
						'label' => __( 'Filters', 'elementor' ),
						'type' => \Elementor\Controls_Manager::HEADING,
						'separator' => 'before',
					]
				);

				$this->add_control(
					'filters_title',
					[
						'type' => \Elementor\Controls_Manager::TEXT,
						'label' => __( "Filters area title", 'trx_addons' ),
						'placeholder' => __( "Title", 'trx_addons' ),
						'default' => '',
					]
				);

				$this->add_control(
					'filters_subtitle',
					[
						'type' => \Elementor\Controls_Manager::TEXT,
						'label' => __( "Filters area subtitle", 'trx_addons' ),
						'placeholder' => __( "Subtitle", 'trx_addons' ),
						'default' => '',
					]
				);

				$this->add_control(
					'filters_title_align',
					[
						'type' => \Elementor\Controls_Manager::SELECT,
						'label' => __( 'Filters titles position', 'trx_addons' ),
						'label_block' => false,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_aligns(false, false),
						'default' => 'left',
					]
				);

				$this->add_control(
					'show_filters',
					[
						'label' => __( 'Show filters tabs', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'return_value' => '1',
					]
				);

				$this->add_control(
					'filters_tabs_position',
					[
						'type' => \Elementor\Controls_Manager::SELECT,
						'label' => __( 'Filters tabs position', 'trx_addons' ),
						'label_block' => false,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_tabs_positions(),
						'default' => 'top',
						'condition' => ['show_filters' => '1'],
					]
				);

				$this->add_control(
					'filters_tabs_on_hover',
					[
						'label' => __( 'Open tabs on hover', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'condition' => ['show_filters' => '1'],
						'return_value' => '1',
					]
				);

				$this->add_control(
					'filters_taxonomy',
					[
						'label' => __( 'Filters taxonomy', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_taxonomies(false, $post_type),
						'default' => 'category',
						'condition' => ['show_filters' => '1'],
					]
				);


				$this->add_control(
					'filters_ids',
					[
						'label' => __( 'Filters terms', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::TEXT,
						"description" => wp_kses_data( __("Comma separated list with term IDs or term names to show as filters. If empty - show all terms from filters taxonomy above", 'trx_addons') ),
						'default' => '',
						'placeholder' => __( "Terms to show", 'trx_addons' ),
						'condition' => [ 'show_filters' => '1']
					]
				);

				$this->add_control(
					'filters_all',
					[
						'label' => __( 'Display the "All" tab', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'default' => '1',
						'return_value' => '1',
						'condition' => ['show_filters' => '1']
					]
				);

				$this->add_control(
					'filters_all_text',
					[
						'label' => __( '"All" tab text', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::TEXT,
						'placeholder' => __( "All", 'trx_addons' ),
						'default' => '',
						'condition' => [
							'show_filters' => '1',
							'filters_all' => '1',
						]
					]
				);

				$this->add_control(
					'filters_more_text',
					[
						'label' => __( "'More posts' text", 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::TEXT,
						'description' => __( "'More posts' text. If empty - no link is shown", 'trx_addons' ),
						'default' => esc_html__('More posts', 'trx_addons'),
						'condition' => [ 'show_filters' => '' ]
					]
				);

				$this->end_controls_section();

				// Section: Details
				$this->start_controls_section(
					'section_sc_blogger_details',
					[
						'label' => __( 'Details', 'trx_addons' ),
						'description' => __( 'Attention! The settings in this section do not apply to custom layouts created in Layouts Builder.', 'trx_addons' ),
						'tab' => \Elementor\Controls_Manager::TAB_LAYOUT
					]
				);

				if ( is_array($templates) ) {
					foreach ($templates as $k => $v) {
						$options = array();
						$default = '';
						if (is_array($v)) {
							foreach($v as $k1 => $v1) {
								$options[$k1] = !empty($v1['title']) ? $v1['title'] : ucfirst( str_replace( array('_', '-'), ' ', $k1 ) );
								if (empty($default)) $default = $k1;
							}
						}
						$this->add_control(
							'template_' . $k,
							[
								'label' => __( 'Template', 'trx_addons' ),
								'label_block' => false,
								'type' => \Elementor\Controls_Manager::SELECT,						
								'options' => $options,
								'default' => $default,
								'condition' => [
									'type' => [ $k ]
								]
							]
						);
					}
				}

				$this->add_control(
					'image_position',
					[
						'type' => \Elementor\Controls_Manager::SELECT,
						'label' => __( 'Image position', 'trx_addons' ),
						'label_block' => false,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_blogger_image_positions(),
						'default' => 'top',
						'condition' => [
							'type' => [ 'default', 'wide', 'list', 'news' ],
						]
					]
				);

				$this->add_control(
					'image_width',
					[
						'label' => __( 'Image width', 'trx_addons' ),
						'description' => wp_kses_data( __("Specify image_width (in %)", 'trx_addons') ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 40,
							'unit' => 'px'
						],
						'size_units' => [ 'px' ],
						'range' => [
							'px' => [
								'min' => 10,
								'max' => 90
							]
						],
						'condition' => [
							'type' => [ 'default', 'wide', 'list', 'news' ],
							'image_position' => ['left', 'right', 'alter']
						]
					]
				);

				$this->add_control(
					'image_ratio',
					[
						'type' => \Elementor\Controls_Manager::SELECT,
						'label' => __( 'Image ratio', 'trx_addons' ),
						'label_block' => false,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_image_ratio(),
						'default' => 'none',
						'condition' => [
							'type' => [ 'default', 'wide', 'list', 'news', 'cards' ],
						]
					]
				);

				$this->add_control(
					'thumb_size',
					[
						'type' => \Elementor\Controls_Manager::SELECT,
						'label' => __( 'Image size', 'trx_addons' ),
						'label_block' => false,
						'description' => wp_kses_data( __("Leave 'Default' to use default size defined in the shortcode template or any registered size to override thumbnail size with the selected value.", 'trx_addons') ),
						'options' => ! $is_edit_mode ? array() : array_merge( array( '' => __( 'Default', 'trx_addons' ) ), trx_addons_get_list_thumbnail_sizes() ),
						'default' => '',
						'condition' => [
							'type!' => [ 'news' ],
						]
					]
				);

				$this->add_control(
					'hover',
					[
						'type' => \Elementor\Controls_Manager::SELECT,
						'label' => __( 'Image hover', 'trx_addons' ),
						'label_block' => false,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_image_hover(),
						'default' => 'inherit',
					]
				);
				$meta_parts = apply_filters('trx_addons_filter_get_list_meta_parts', array());
				$this->add_control(
					'meta_parts',
					[
						'label' => __( 'Choose meta parts', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT2,
						'options' => $meta_parts,
						'multiple' => true,
						'default' => array_keys($meta_parts),
/*
						'condition' => [
							'type' => [ 'default', 'wide', 'list', 'news' ],
						]
*/
					]
				);

				$this->add_control(
					'hide_excerpt',
					[
						'label' => __( 'Hide excerpt', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'return_value' => '1',
					]
				);

				$this->add_control(
					'excerpt_length',
					[
						'label' => __( "Text length (in words)", 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::TEXT,
						'default' => '',
						'condition' => [
							'hide_excerpt' => '',
						],
					]
				);

				$this->add_control(
					'full_post',
					[
						'label' => __( 'Open full post', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'default' => '',
						'return_value' => '1',
						'condition' => [
							'type!' => [ 'cards' ],
							'hide_excerpt' => '1',
						],
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
					'no_links',
					[
						'label' => __( 'Disable links', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'return_value' => '1',
						'condition' => [
							'full_post' => ''
						],
					]
				);

				$this->add_control(
					'more_button',
					[
						'label' => __( "Show 'More' button", 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'default' => '1',
						'return_value' => '1',
						'condition' => [
							'no_links' => '',
							'full_post' => ''
						]
					]
				);

				$this->add_control(
					'more_text',
					[
						'label' => __( "'More' text", 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::TEXT,
						'default' => esc_html__('Read more', 'trx_addons'),
						'condition' => [
							'more_button' => '1',
							'no_links' => ''
						]
					]
				);

				$this->add_control(
					'text_align',
					[
						'type' => \Elementor\Controls_Manager::SELECT,
						'label' => __( 'Text alignment', 'trx_addons' ),
						'label_block' => false,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_aligns(),
						'default' => 'none',
						'condition' => [
							'type' => [ 'default', 'wide', 'list', 'news', 'cards' ],
						]
					]
				);

				$this->add_control(
					'on_plate',
					[
						'label' => __( 'On plate', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'return_value' => '1',
						'condition' => [
							'type' => [ 'default', 'wide', 'list', 'news' ],
						]
					]
				);

				$this->add_control(
					'video_in_popup',
					[
						'label' => __( 'Video in the popup', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'return_value' => '1',
					]
				);

				$this->add_control(
					'numbers',
					[
						'label' => __( 'Show numbers', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'return_value' => '1',
						'condition' => [
							'type' => [ 'list' ],
						]
					]
				);

				$this->add_control(
					'date_format',
					[
						'label' => __( "Date format", 'trx_addons' ),
						'label_block' => false,
						'description' => sprintf( __( 'See available formats %s', 'trx_addons' ), '<a href="//wordpress.org/support/article/formatting-date-and-time/" target="_blank">' . __( 'here', 'trx_addons') . '</a>' ),
						'type' => \Elementor\Controls_Manager::TEXT,
						'default' => '',
						'condition' => [
							'type' => [ 'default', 'wide', 'list', 'news', 'cards' ],
						]
					]
				);

				$this->end_controls_section();

				$this->add_slider_param();
				$this->add_title_param();
			}
		}
		
		// Register widget
		trx_addons_elm_register_widget( 'TRX_Addons_Elementor_Widget_Blogger' );
	}
}

// Disable our widgets (shortcodes) to use in Elementor
// because we create special Elementor's widgets instead
if (!function_exists('trx_addons_sc_blogger_black_list')) {
	add_action( 'elementor/widgets/black_list', 'trx_addons_sc_blogger_black_list' );
	function trx_addons_sc_blogger_black_list($list) {
		$list[] = 'TRX_Addons_SOW_Widget_Blogger';
		return $list;
	}
}
