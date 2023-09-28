<?php
/**
 * Shortcode: Blog item parts (Elementor support)
 *
 * @package ThemeREX Addons
 * @since v1.6.50
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}
	

// Elementor Widget
//------------------------------------------------------
if (!function_exists('trx_addons_sc_layouts_blog_item_add_in_elementor')) {
	add_action( trx_addons_elementor_get_action_for_widgets_registration(), 'trx_addons_sc_layouts_blog_item_add_in_elementor' );
	function trx_addons_sc_layouts_blog_item_add_in_elementor() {
		
		if (!class_exists('TRX_Addons_Elementor_Widget')) return;	

		class TRX_Addons_Elementor_Widget_Layouts_Blog_Item extends TRX_Addons_Elementor_Widget {

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
					'thumb_mask_opacity' => 'size',
					'thumb_hover_opacity' => 'size',
					'font_zoom' => 'size',
					'animation_in_delay' => 'size',
					'animation_out_delay' => 'size',
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
				return 'trx_sc_layouts_blog_item';
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
				return __( 'Layouts: Blog item parts', 'trx_addons' );
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
				return ['trx_addons-layouts'];
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
				$post_types = ! $is_edit_mode ? array() : trx_addons_get_list_posts_types();
				$meta_parts = ! $is_edit_mode ? array() : apply_filters('trx_addons_filter_get_list_meta_parts', array());

				$this->start_controls_section(
					'section_sc_layouts_blog_item',
					[
						'label' => __( 'Blog item part', 'trx_addons' ),
					]
				);

				$this->add_control(
					'type',
					[
						'label' => __( 'Layout', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_layouts_blog_item_parts(),
						'default' => 'title',
					]
				);

				// Featured params
				$this->add_control(
					'thumb_bg',
					[
						'label' => __( 'Use as background', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'return_value' => '1',
						'condition' => [
							'type' => 'featured'
						]
					]
				);

				$this->add_control(
					'thumb_ratio',
					[
						'label' => __( 'Image ratio', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::TEXT,
						'default' => '16:9',
						'condition' => [
							'thumb_bg' => '1'
						]
					]
				);

				$this->add_control(
					'thumb_size',
					[
						'label' => __( 'Image size', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_thumbnail_sizes(),
						'default' => 'full',
						'condition' => [
							'type' => 'featured',
							'thumb_bg' => ''
						]
					]
				);

				$this->add_control(
					'thumb_mask',
					[
						'label' => __( 'Image mask color', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'condition' => [
							'type' => 'featured'
						]
					]
				);

				$this->add_control(
					'thumb_mask_opacity',
					[
						'label' => __( 'Image mask opacity', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 0.3,
							'unit' => 'px'
						],
						'size_units' => [ 'px' ],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 1,
								'step' => 0.05
							],
						],
						'condition' => [
							'type' => 'featured'
						]
					]
				);

				$this->add_control(
					'thumb_hover_mask',
					[
						'label' => __( 'Hovered mask color', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'condition' => [
							'type' => 'featured'
						]
					]
				);

				$this->add_control(
					'thumb_hover_opacity',
					[
						'label' => __( 'Hovered mask opacity', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 0.1,
							'unit' => 'px'
						],
						'size_units' => [ 'px' ],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 1,
								'step' => 0.05
							],
						],
						'condition' => [
							'type' => 'featured'
						]
					]
				);

				// Title params
				$this->add_control(
					'title_tag',
					[
						'label' => __( 'Title tag', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_title_tags(),
						'default' => 'h4',
						'condition' => [
							'type' => 'title'
						]
					]
				);

				// Meta params
				$this->add_control(
					'meta_parts',
					[
						'label' => __( 'Choose meta parts', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT2,
						'options' => $meta_parts,
						'multiple' => true,
						'default' => trx_addons_array_get_first($meta_parts),
						'condition' => [
							'type' => 'meta'
						]
					]
				);

				// Custom meta params
				$this->add_control(
					'custom_meta_key',
					[
						'label' => __( 'Name of the custom meta', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::TEXT,
						'default' => '',
						'condition' => [
							'type' => 'custom'
						]
					]
				);

				// Button params
				$this->add_control(
					'button_type',
					[
						'label' => __( 'Button type', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('sc', 'button'), 'trx_sc_button'),
						'default' => 'default',
						'condition' => [
							'type' => 'button'
						]
					]
				);

				$this->add_control(
					'button_link',
					[
						'label' => __( 'Button link to', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => [
							'post' => __('Single post', 'trx_addons'),
							'product' => __('Linked product', 'trx_addons'),
							'cart' => __('Add to cart', 'trx_addons'),
						],
						'default' => 'post',
						'condition' => [
							'type' => 'button'
						]
					]
				);

				$this->add_control(
					'button_text',
					[
						'label' => __( 'Button caption', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::TEXT,
						'default' => __('Read more', 'trx_addons'),
						'condition' => [
							'type' => 'button'
						]
					]
				);


				// Common params
				$this->add_control(
					'position',
					[
						'label' => __( 'Position', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : array_merge( array('static' => __('Static', 'trx_addons')), trx_addons_get_list_sc_positions()),
						'default' => 'static',
						'condition' => [
							'type' => ['title', 'meta', 'excerpt', 'custom', 'button'],
						],
						'prefix_class' => 'sc_layouts_blog_item_position_',
					]
				);

				$this->add_control(
					'font_zoom',
					[
						'label' => __( 'Zoom font size', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 1,
							'unit' => 'px'
						],
						'size_units' => [ 'px' ],
						'range' => [
							'px' => [
								'min' => 0.3,
								'max' => 3,
								'step' => 0.1
							],
						],
						'condition' => [
							'type' => ['title', 'excerpt', 'content', 'meta', 'custom', 'button'],
						],
						'selectors' => [
							'{{WRAPPER}} .sc_layouts_blog_item' => 'font-size: {{SIZE}}em;',
						]
					]
				);

				$this->add_control(
					'hide_overflow',
					[
						'label' => __( 'Hide overflow', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'return_value' => '1',
						'condition' => [
							'type' => ['title', 'meta', 'custom'],
							'position' => array_keys(trx_addons_get_list_sc_positions())
						]
					]
				);

				$this->add_control(
					'animation_in',
					[
						'label' => __( 'Hover animation in', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => trx_addons_get_list_animations_in(),
						'default' => 'none',
						'condition' => [
							'type' => ['title', 'meta', 'excerpt', 'custom', 'button'],
							'position' => array_keys(trx_addons_get_list_sc_positions())
						]
					]
				);

				$this->add_control(
					'animation_in_delay',
					[
						'label' => __( 'Animation delay', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 0,
							'unit' => 'px'
						],
						'size_units' => [ 'px' ],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 2000,
								'step' => 100
							],
						],
						'condition' => [
							'animation_in!' => ['none']
						]
					]
				);

				$this->add_control(
					'animation_out',
					[
						'label' => __( 'Hover animation out', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => trx_addons_get_list_animations_out(),
						'default' => 'none',
						'condition' => [
							'type' => ['title', 'meta', 'excerpt', 'custom', 'button'],
							'position' => array_keys(trx_addons_get_list_sc_positions())
						]
					]
				);

				$this->add_control(
					'animation_out_delay',
					[
						'label' => __( 'Animation delay', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 0,
							'unit' => 'px'
						],
						'size_units' => [ 'px' ],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 2000,
								'step' => 100
							],
						],
						'condition' => [
							'animation_out!' => ['none']
						]
					]
				);

				$this->add_control(
					'text_color',
					[
						'label' => __( 'Text color', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'condition' => [
							'type' => ['title', 'meta', 'excerpt', 'custom', 'button']
						]
					]
				);

				$this->add_control(
					'text_hover',
					[
						'label' => __( 'Text color (hovered)', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'condition' => [
							'type' => ['title', 'meta', 'excerpt', 'custom', 'button']
						]
					]
				);

				$this->add_control(
					'post_type',
					[
						'label' => __( 'Supported post types', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT2,
						'options' => $post_types,
						'multiple' => true,
						'default' => ''
					]
				);
				
				$this->end_controls_section();
			}
			
			/**
			 * Get stack.
			 *
			 * Retrieve the widget stack of controls.
			 *
			 * @since 1.9.2
			 * @access public
			 *
			 * @param bool $with_common_controls Optional. Whether to include the common controls. Default is true.
			 *
			 * @return array Widget stack of controls.
			 */
			public function get_stack( $with_common_controls = true ) {
				$stack = parent::get_stack($with_common_controls);
				if ($with_common_controls) {
					// Apply styles to .sc_layouts_blog_item instead .elementor-widget-container
					$controls_to_change = [
						'_padding', '_padding_tablet', '_padding_mobile',
						'_background_color', '_background_gradient_angle', '_background_gradient_position',
						'_background_image', '_background_position', '_background_attachment', '_background_repeat',
						'_background_size', '_background_video_fallback',
						'_background_hover_color', '_background_hover_gradient_angle', '_background_hover_gradient_position',
						'_background_hover_image', '_background_hover_position', '_background_hover_attachment', '_background_hover_repeat',
						'_background_hover_size', '_background_hover_video_fallback',
						'_border_border', '_border_width', '_border_color', '_border_radius', '_box_shadow_box_shadow',
						'_border_hover_border', '_border_hover_width', '_border_hover_color', '_border_radius_hover', '_box_shadow_hover_box_shadow',
						'_border_hover_transition'
					];
					foreach ($controls_to_change as $control_name) {
						if (!isset($stack['controls'][$control_name])) continue;
						if (isset($stack['controls'][$control_name]['selectors']) && is_array($stack['controls'][$control_name]['selectors'])) {
							$new_selectors = array();
							foreach ($stack['controls'][$control_name]['selectors'] as $k=>$v) {
								$new_selectors[str_replace('.elementor-widget-container', '.elementor-widget-container .sc_layouts_blog_item', $k)] = $v;
							}
							$stack['controls'][$control_name]['selectors'] = $new_selectors;
						}
					}
					// Add unit 'rem' to paddings and margins
					$controls_to_change = [
						'_padding', '_padding_tablet', '_padding_mobile',
						'_margin', '_margin_tablet', '_margin_mobile',
					];
					foreach ($controls_to_change as $control_name) {
						if (!isset($stack['controls'][$control_name])) continue;
						if (isset($stack['controls'][$control_name]['size_units']) && is_array($stack['controls'][$control_name]['size_units'])) {
							$stack['controls'][$control_name]['size_units'][] = 'rem';
						}
					}
				}
				return $stack;
			}
		}
		
		// Register widget
		trx_addons_elm_register_widget( 'TRX_Addons_Elementor_Widget_Layouts_Blog_Item' );
	}
}


// Change widget's controls behaviour before render
if ( !function_exists( 'trx_addons_sc_layouts_blog_item_change_elm_params' ) ) {
	add_action( 'elementor/frontend/widget/before_render', 'trx_addons_sc_layouts_blog_item_change_elm_params', 10, 1 );
	function trx_addons_sc_layouts_blog_item_change_elm_params($widget) {
		if ( $widget->get_name() == 'trx_sc_layouts_blog_item' ) {
/*
			// Add animations to the wrapper
			// Deprecated! Animation have been applied to the child node '.sc_layouts_blog_item' to avoid conflict CSS tansformations
			$args = $widget->get_settings();
			if ( !wp_is_mobile()
					&& (
						( !empty($args['animation_in']) && !trx_addons_is_off($args['animation_in']) )
						||
						( !empty($args['animation_out']) && !trx_addons_is_off($args['animation_out']) )
						)
			) {
				$widget->add_render_attribute( '_wrapper', 'data-hover-animation', 'animated fast' );
				if ( !empty($args['animation_in']) && !trx_addons_is_off($args['animation_in']) ) {
					$widget->add_render_attribute( '_wrapper', 'data-animation-in', $args['animation_in'] );
				}
				if ( !empty($args['animation_out']) && !trx_addons_is_off($args['animation_out']) ) {
					$widget->add_render_attribute( '_wrapper', 'data-animation-out', $args['animation_out'] );
				}
			}
*/
		}
	}
}
