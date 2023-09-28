<?php
/**
 * Plugin support: Elementor ( Define class to create our modules (widgets) )
 *
 * @package ThemeREX Addons
 * @since v1.0
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

if ( class_exists('\Elementor\Widget_Base') && ! class_exists('TRX_Addons_Elementor_Widget') ) {

	/**
	 * Define a base class (abstract) for our shortcodes and widgets.
	 * This class will be extended by the class for each shortcode/widget.
	 * This class extends Elementor's Widget_Base class.
	 */
	abstract class TRX_Addons_Elementor_Widget extends \Elementor\Widget_Base {

		/**
		 * List of shortcodes params, that must be plain and get its value from the elementor's arrays
		 * 'size', 'url', etc.
		 * 
		 * @var array $plain_params  List of params, that must be plain.
		 * 							 Format: 'param_name' => "key from the elementor's array".
		 * 							 Example: 'link' => 'url', 'image' => 'url',
		 * 									  'columns' => 'size', 'count' => 'size', 'offset' => 'size', etc.
		 *
		 * @access private
		 */
		private $plain_params = array(
			'url' => 'url',
			'link' => 'url',
			'image' => 'url',
			'bg_image' => 'url',
			'link_image' => 'url',					
			'columns' => 'size',
			'columns_widescreen' => 'size',
			'columns_desktop' => 'size',
			'columns_laptop' => 'size',
			'columns_tablet_extra' => 'size',
			'columns_tablet' => 'size',
			'columns_mobile_extra' => 'size',
			'columns_mobile' => 'size',
			'count' => 'size',
			'offset' => 'size',
			'slides_space' => 'size',
			'gradient_direction' => 'size',
			'gradient_direction2' => 'size',
			'typed_speed' => 'size',
			'typed_delay' => 'size',
		);
		
		/**
		 * Set a new list of shortcodes params, that must be plain and get its value from the elementor's arrays
		 * 'size', 'url', etc.
		 * Format: 'param_name' => "key from the elementor's array".
		 * Example: 'link' => 'url', 'image' => 'url', 'columns' => 'size', 'count' => 'size', 'offset' => 'size', etc.
		 *
		 * @access protected
		 * 
		 * @param array $list  List of params, that must be plain.
		 */
		protected function set_plain_params( $list ) {
			$this->plain_params = $list;
		}
		
		/**
		 * Add new params to the list of shortcodes params, that must be plain and get its value from the elementor's arrays
		 * 'size', 'url', etc.
		 * Format: 'param_name' => "key from the elementor's array".
		 * Example: 'link' => 'url', 'image' => 'url', 'columns' => 'size', 'count' => 'size', 'offset' => 'size', etc.
		 *
		 * @access public
		 * 
		 * @param array $list  List of params, that must be plain.
		 */
		public function add_plain_params( $list ) {
			$this->plain_params = array_merge( $this->plain_params, $list );
		}

		/**
		 * Return string with a default subtitle
		 *
		 * @access protected
		 * 
		 * @return string Default subtitle
		 */
		protected function get_default_subtitle() {
			return __('Subtitle', 'trx_addons');
		}

		/**
		 * Return string with a default description
		 *
		 * @access protected
		 * 
		 * @return string Default description
		 */
		protected function get_default_description() {
			return __('Some description text for this item', 'trx_addons');
		}

		/**
		 * Retrieve the list of scripts the widget depended on.
		 *
		 * Used to set scripts dependencies required to run the widget.
		 *
		 * @since 1.6.41
		 *
		 * @access public
		 *
		 * @return array Widget scripts dependencies.
		 */
		public function get_script_depends() {
			return array( 'trx_addons-elementor-preview' );
		}
		
		/**
		 * Get all elements from specified post from the Elementor's array in the post meta '_elementor_data'
		 *
		 * @access protected
		 * 
		 * @param int $post_id  Post ID
		 * 
		 * @return array  List of elements from the Elementor's array in the post meta '_elementor_data'
		 */
		protected function get_post_elements( $post_id = 0 ) {
			$meta = array();
			if ( $post_id == 0 ) {
				if ( trx_addons_get_value_gp( 'action' ) == 'elementor' ) {
					$post_id = trx_addons_get_value_gp( 'post' );
				} else if ( trx_addons_get_value_gp( 'action' ) == 'elementor_ajax' ) {
					$post_id = trx_addons_get_value_gp( 'editor_post_id' );
				}
			}
			if ( $post_id > 0 ) {
				$meta = get_post_meta( $post_id, '_elementor_data', true );
				if ( substr( $meta, 0, 1 ) == '[' ) {
					$meta = json_decode( $meta, true );
				}
			}
			return $meta;
		}
		
		/**
		 * Get a shortcode parameters from the current post or from the specified post meta (2-nd parameter)
		 *
		 * @access protected
		 * 
		 * @param string $sc   Shortcode name
		 * @param array $meta  List of elements from the Elementor's array in the post meta '_elementor_data'
		 * 
		 * @return array       List of shortcode parameters
		 */
		protected function get_sc_params( $sc = '', $meta = false ) {
			if ( $meta === false ) {
				$meta = $this->get_post_elements();
			}
			if ( empty( $sc ) ) {
				$sc = $this->get_name();
			}
			$params = false;
			if ( is_array( $meta ) ) {
				foreach( $meta as $v ) {
					if ( ! empty( $v['widgetType'] ) && $v['widgetType'] == $sc ) {
						$params = $v['settings'];
						break;
					} else if ( ! empty( $v['elements'] ) && count( $v['elements'] ) > 0 ) {
						$params = $this->get_sc_params( $sc, $v['elements'] );
						if ( $params !== false ) {
							break;
						}
					}
				}
			}
			return $params;
		}

		/**
		 * Return a shortcode's name
		 *
		 * @access public
		 * 
		 * @return string  Shortcode's name
		 */
		public function get_sc_name() {
			return $this->get_name();
		}

		/**
		 * Return a shortcode's function name by the shortcode's name
		 *
		 * @access public
		 * 
		 * @return string  Shortcode's function name
		 */
		public function get_sc_function() {
			return sprintf( "trx_addons_%s", str_replace( array( 'trx_sc_', 'trx_widget_' ), array( 'sc_', 'sc_widget_' ), $this->get_sc_name() ) );
		}

		
		// ADD CONTROLS FOR COMMON PARAMETERS
		// Attention! You can use next tabs to create sections inside:
		// TAB_CONTENT | TAB_STYLE | TAB_ADVANCED | TAB_RESPONSIVE | TAB_LAYOUT | TAB_SETTINGS
		//------------------------------------------------------------

		/**
		 * Add common groups of controls, like 'id', 'title', 'query', 'slider', etc.
		 * to the our shortcode's settings for Elementor
		 * 
		 * @access protected
		 * 
		 * @param array $group       Group of parameters
		 * @param array $params      List of parameters
		 * @param array $add_params  List of additional parameters. Used to add/remove/change some parameters from the list $params
		 */
		protected function add_common_controls( $group, $params, $add_params ) {
			if ( ! empty( $group['label'] ) ) {
				$this->start_controls_section( 'section_' . $group['section'] . '_params', array(
					'label' => $group['label'],
					'tab' => empty( $group['tab'] ) 
								? \Elementor\Controls_Manager::TAB_CONTENT 
								: $group['tab']
				) );
			}
			foreach ( $params as $param ) {
				if ( isset( $add_params[ $param['name'] ] ) ) {
					if ( empty( $add_params[ $param['name'] ] ) ) {
						continue;
					} else {
						$param = array_merge( $param, $add_params[ $param['name'] ] );
					}
					unset( $add_params[ $param['name'] ] );
				}
				if ( ! empty( $param['responsive'] ) ) {
					$this->add_responsive_control( $param['name'], $param );
				} else if ( ! empty( $param['group'] ) ) {
					$this->add_group_control( $param['group'], $param );
				} else {
					$this->add_control( $param['name'], $param );
				}
			}
			if ( count( $add_params ) > 0 ) {
				foreach ( $add_params as $k => $v ) {
					if ( ! empty( $v ) && is_array( $v ) ) {
						if ( ! empty( $v['responsive'] ) ) {
							$this->add_responsive_control( $k, $v );
						} else if ( ! empty( $param['group'] ) ) {
							$this->add_group_control( $param['group'], $param );
						} else {
							$this->add_control( $k, $v );
						}
					}
				}
			}
			if ( ! empty( $group['label'] ) ) {
				$this->end_controls_section();
			}
		}



		// Icon selector
		//------------------------------------------------------------
		
		/**
		 * Return 'icon' parameters for the control with icons selector
		 *
		 * @access protected
		 * 
		 * @param bool   $only_socials  If true - return only socials icons
		 * @param string $style         Icon's style: images | icons | svg
		 * 
		 * @return array  List of parameters
		 */
		protected function get_icon_param( $only_socials = false, $style = '' ) {
			return trx_addons_get_icon_param( 'icon', $only_socials, $style );
		}

		/**
		 * Create a control with icons selector
		 *
		 * @access protected
		 * 
		 * @param string|boolean $group  Name of the group. If false - use 'Icon' as a group name
		 * @param array  $add_params  List of additional parameters. Used to add/remove/change some parameters from the list $params
		 * @param string $style       Icon's style: images | icons | svg
		 */
		protected function add_icon_param( $group = '', $add_params = array(), $style = '' ) {
			$this->add_common_controls(
				array(
					'label' => $group === false ? __('Icon', 'trx_addons') : $group,
					'section' => 'icon'
				),
				$this->get_icon_param( ! empty( $add_params['only_socials'] ), $style ),
				$add_params
			);
		}


		// Slider parameters
		//------------------------------------------------------------

		/**
		 * Return a group of 'slider' parameters for some shortcodes, like 'blogger', 'services', etc.
		 *
		 * @access protected
		 * 
		 * @trigger trx_addons_filter_elementor_add_slider_param
		 * 
		 * @return array  List of parameters
		 */
		protected function get_slider_param() {

			// Detect edit mode
			$is_edit_mode = trx_addons_elm_is_edit_mode();

			$params = array(
				array(
					'name' => 'slider',
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'label' => __('Slider', 'trx_addons'),
					'label_off' => __( 'Off', 'trx_addons' ),
					'label_on' => __( 'On', 'trx_addons' ),
					'return_value' => '1'
				),

				array(
					'name' => 'slider_effect',
					'type' => \Elementor\Controls_Manager::SELECT,
					'label' => __( 'Effect', 'trx_addons' ),
					'label_block' => false,
					'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_slider_effects(),
					'default' => 'slide',
					'condition' => array(
						'slider' => '1'
					)
				),
				array(
					'name' => 'slides_space',
					'type' => \Elementor\Controls_Manager::SLIDER,
					'label' => __('Space', 'trx_addons'),
					"description" => wp_kses_data( __('Space between slides', 'trx_addons') ),
					'condition' => array(
						'slider' => '1',
					),
					'default' => array(
						'size' => 0
					),
					'range' => array(
						'px' => array(
							'min' => 0,
							'max' => 100
						)
					)
				),
				array(
					'name' => 'slider_controls',
					'type' => \Elementor\Controls_Manager::SELECT,
					'label' => __( 'Slider controls', 'trx_addons' ),
					'label_block' => false,
					'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_slider_controls(),
					'condition' => array(
						'slider' => '1',
					),
					'default' => 'none',
				),
				array(
					'name' => 'slider_pagination',
					'type' => \Elementor\Controls_Manager::SELECT,
					'label' => __( 'Slider pagination', 'trx_addons' ),
					'label_block' => false,
					'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_slider_paginations(),
					'condition' => array(
						'slider' => '1'
					),
					'default' => 'none',
				),
				array(
					'name' => 'slider_pagination_type',
					'type' => \Elementor\Controls_Manager::SELECT,
					'label' => __( 'Slider pagination type', 'trx_addons' ),
					'label_block' => false,
					'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_slider_paginations_types(),
					'condition' => array(
						'slider' => '1',
						'slider_pagination!' => 'none'
					),
					'default' => 'bullets',
				),
				array(
					'name' => 'slides_centered',
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'label' => __('Slides centered', 'trx_addons'),
					'label_off' => __( 'Off', 'trx_addons' ),
					'label_on' => __( 'On', 'trx_addons' ),
					'return_value' => '1',
					'condition' => array(
						'slider' => '1',
					),
				),
				array(
					'name' => 'slides_overflow',
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'label' => __('Slides overflow visible', 'trx_addons'),
					'label_off' => __( 'Off', 'trx_addons' ),
					'label_on' => __( 'On', 'trx_addons' ),
					'return_value' => '1',
					'condition' => array(
						'slider' => '1',
					),
				),
				array(
					'name' => 'slider_mouse_wheel',
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'label' => __('Mouse wheel enabled', 'trx_addons'),
					'label_off' => __( 'Off', 'trx_addons' ),
					'label_on' => __( 'On', 'trx_addons' ),
					'return_value' => '1',
					'condition' => array(
						'slider' => '1',
					),
				),
				array(
					'name' => 'slider_autoplay',
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'label' => __('Enable autoplay', 'trx_addons'),
					'label_off' => __( 'Off', 'trx_addons' ),
					'label_on' => __( 'On', 'trx_addons' ),
					'default' => '1',
					'return_value' => '1',
					'condition' => array(
						'slider' => '1',
					),
				),
				array(
					'name' => 'slider_loop',
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'label' => __('Loop', 'trx_addons'),
					'label_off' => __( 'Off', 'trx_addons' ),
					'label_on' => __( 'On', 'trx_addons' ),
					'default' => '1',
					'return_value' => '1',
					'condition' => array(
						'slider' => '1',
						'slides_overflow!' => '1'
					),
				),
				array(
					'name' => 'slider_free_mode',
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'label' => __('Free mode', 'trx_addons'),
					'label_off' => __( 'Off', 'trx_addons' ),
					'label_on' => __( 'On', 'trx_addons' ),
					'return_value' => '1',
					'condition' => array(
						'slider' => '1',
					),
				),
			);
			return apply_filters( 'trx_addons_filter_elementor_add_slider_param', $params );
		}
		
		/**
		 * Add 'Slider' controls to our shortcodes
		 * 
		 * @access protected
		 *
		 * @param string|boolean $group  Group name. If false - use 'Slider' as group name
		 * @param array $add_params  Additional parameters to add or change some default 'slider' parameters
		 */
		protected function add_slider_param( $group = false, $add_params = array() ) {
			$this->add_common_controls(
				array(
					'label' => $group === false ? __( 'Slider', 'trx_addons' ) : $group,
					'section' => 'slider',
					'tab' => \Elementor\Controls_Manager::TAB_LAYOUT
				),
				$this->get_slider_param(),
				$add_params
			);
		}


		// 'Title' parameters
		//----------------------------------------------------------------------

		/**
		 * Return common 'Title' parameters for the shortcode's controls
		 * 
		 * @access protected
		 * 
		 * @trigger trx_addons_filter_elementor_add_title_param
		 *
		 * @param boolean $button  Add a button to the title block
		 * 
		 * @return array  	   List of parameters
		 */
		protected function get_title_param( $button = true ) {

			// Detect edit mode
			$is_edit_mode = trx_addons_elm_is_edit_mode();

			$params = array(
				// Common
				array(
					'name' => 'title_style',
					'type' => \Elementor\Controls_Manager::SELECT,
					'label' => __( 'Title style', 'trx_addons' ),
					'label_block' => false,
					'options' => ! $is_edit_mode ? array() : apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('sc', 'title'), 'trx_sc_title'),
					'default' => 'default',
				),
				array(
					'name' => 'title_tag',
					'type' => \Elementor\Controls_Manager::SELECT,
					'label' => __( 'Title tag', 'trx_addons' ),
					'label_block' => false,
					'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_title_tags(),
					'default' => 'none',
				),
				array(
					'name' => 'title_align',
					'type' => \Elementor\Controls_Manager::SELECT,
					'label' => __( 'Title alignment', 'trx_addons' ),
					'label_block' => false,
					'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_aligns(),
					'default' => 'none',
				),
				// Title
				array(
					'name' => 'title_heading',
					'type' => \Elementor\Controls_Manager::HEADING,
					'label' => esc_html__( 'Title settings', 'trx_addons' ),
					'separator' => 'before',
				),
				array(
					'name' => 'title',
					'type' => \Elementor\Controls_Manager::TEXTAREA,
					'label' => __( "Title", 'trx_addons' ),
					"description" => wp_kses_data( __("Title of the block. Enclose any words in {{ and }} to make them italic or in (( and )) to make them bold. If title style is 'accent' - bolded element styled as shadow, italic - as a filled circle", 'trx_addons') )
									. '<br />'
									. wp_kses_data( __("You can also enclose the title part with [[ and ]] to apply styles from the 'Title 2' section to it (see below)", 'trx_addons') ),
					'placeholder' => __( "Title", 'trx_addons' ),
					'separator' => 'before',
					'default' => ''
				),
				array(
					'name' => 'title_color',
					'label' => __( 'Title color', 'trx_addons' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'default' => '',
					'description' => '',
					'selectors' => [
						'{{WRAPPER}} .sc_item_title' => 'color: {{VALUE}};',
					],
//					'global' => array(
//						'active' => false,
//					),
				),
				array(
					'name' => 'title_color2',
					'label' => __( 'Title color 2', 'trx_addons' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'default' => '',
					'description' => '',
//					'global' => array(
//						'active' => false,
//					),
					'condition' => array(
						'title_style' => 'gradient'
					),
				),
				array(
					'name' => 'gradient_fill',
					'type' => \Elementor\Controls_Manager::SELECT,
					'label' => __( 'Gradient fill', 'trx_addons' ),
					'label_block' => false,
					'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_title_gradient_fills(),
					'default' => 'block',
					'condition' => array(
						'title_style' => 'gradient'
					),
				),
				array(
					'name' => 'gradient_direction',
					'label' => __( 'Gradient direction', 'trx_addons' ),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'default' => array(
						'size' => 0,
						'unit' => 'px'
					),
					'size_units' => array( 'px' ),
					'range' => array(
						'px' => array(
							'min' => 0,
							'max' => 360
						)
					),
					'condition' => array(
						'title_style' => 'gradient'
					),
				),
				array(
					'name' => 'title_border_color',
					'label' => __( 'Title border color', 'trx_addons' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'default' => '',
					'description' => '',
//					'global' => array(
//						'active' => false,
//					),
					'selectors' => array(
						'{{WRAPPER}} .sc_item_title_text' => '-webkit-text-stroke-color:{{VALUE}};'
					)
				),
				array(
					'name' => 'title_border_width',
					'label' => __( 'Title border width', 'trx_addons' ),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'default' => array(
						'size' => 0,
						'unit' => 'px'
					),
					'size_units' => array( 'px' ),
					'range' => array(
						'px' => array(
							'min' => 0,
							'max' => 10
						)
					),
					'selectors' => array(
						'{{WRAPPER}} .sc_item_title_text' => '-webkit-text-stroke-width:{{SIZE}}px;'
					)
				),
				array(
					'name' => 'title_bg_image',
					'type' => \Elementor\Controls_Manager::MEDIA,
					'label' => __( 'Title bg image', 'trx_addons' ),
					'default' => array(
						'url' => '',
					),
					'selectors' => array(
						'{{WRAPPER}} .sc_item_title_text' => '-webkit-text-fill-color:transparent;-webkit-background-clip:text;background-clip:text;background-image:url({{URL}});background-position:center center;background-size:cover;'
					)
				),
				array(
					'name' => 'title_typography',
					'label' => __( 'Title typography', 'trx_addons' ),
					'group' => \Elementor\Group_Control_Typography::get_type(),
//					'global' => [
//						'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY,
//					],
					'selector' => '{{WRAPPER}} .sc_item_title_text'
				),
				array(
					'name' => 'title_shadow',
					'label' => __( 'Title shadow', 'trx_addons' ),
					'group' => \Elementor\Group_Control_Text_Shadow::get_type(),
					'selector' => '{{WRAPPER}} .sc_item_title_text'
				),
				array(
					'name' => "typed",
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'label' => __("Use autotype", 'trx_addons'),
					'label_off' => __( 'Off', 'trx_addons' ),
					'label_on' => __( 'On', 'trx_addons' ),
					'return_value' => '1',
					'condition' => array(
						'title!' => '',
					),
				),
				array(
					'name' => "typed_loop",
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'label' => __("Autotype loop", 'trx_addons'),
					'label_off' => __( 'Off', 'trx_addons' ),
					'label_on' => __( 'On', 'trx_addons' ),
					'return_value' => '1',
					'default' => '1',
					'condition' => array(
						'typed' => '1',
					),
				),
				array(
					'name' => "typed_cursor",
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'label' => __("Autotype cursor", 'trx_addons'),
					'label_off' => __( 'Off', 'trx_addons' ),
					'label_on' => __( 'On', 'trx_addons' ),
					'return_value' => '1',
					'default' => '1',
					'condition' => array(
						'typed' => '1',
					),
				),
				array(
					'name' => 'typed_strings',
					'type' => \Elementor\Controls_Manager::TEXTAREA,
					'label' => __( 'Alternative strings', 'trx_addons' ),
					'label_block' => true,
					'description' => __( "Alternative strings to type. Attention! First string must be equal of the part of the title.", 'trx_addons' ),
					'default' => '',
					'separator' => 'none',
					'rows' => 5,
					'show_label' => true,
					'condition' => array(
						'typed' => '1',
					),
				),
				array(
					'name' => 'typed_color',
					'label' => __( 'Autotype color', 'trx_addons' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'default' => '',
					'description' => '',
//					'global' => array(
//						'active' => false,
//					),
					'condition' => array(
						'typed' => '1',
					),
				),
				array(
					'name' => "typed_speed",
					'type' => \Elementor\Controls_Manager::SLIDER,
					'label' => __('Autotype speed', 'trx_addons'),
					'condition' => array(
						'typed' => '1',
					),
					'default' => array(
						'size' => 6
					),
					'step' => 0.5,
					'range' => array(
						'px' => array(
							'min' => 1,
							'max' => 10
						)
					)
				),
				array(
					'name' => "typed_delay",
					'type' => \Elementor\Controls_Manager::SLIDER,
					'label' => __('Autotype delay (in sec.)', 'trx_addons'),
					'separator' => 'after',
					'condition' => array(
						'typed' => '1',
					),
					'default' => array(
						'size' => 1
					),
					'step' => 0.5,
					'range' => array(
						'px' => array(
							'min' => 0,
							'max' => 10
						)
					)
				),
				// Title 2 (dual title)
				array(
					'name' => 'title2_heading',
					'type' => \Elementor\Controls_Manager::HEADING,
					'label' => esc_html__( 'Dual heading', 'trx_addons' ),
					"description" => wp_kses_data( __("Allows to separate title on parts with different color, border or background (if your theme support this functionality)", 'trx_addons') )
									. '<br />'
									. wp_kses_data( __("You could also enclose part of the title in the field 'Title' with [[ and ]]", 'trx_addons') ),
					'separator' => 'before',
				),
				array(
					'name' => 'title2',
					'type' => \Elementor\Controls_Manager::TEXT,
					'label' => __( "Title part 2", 'trx_addons' ),
					"description" => wp_kses_data( __("Use this parameter if you want to separate title parts with different color, border or background", 'trx_addons') ),
					'placeholder' => __( "Title part 2", 'trx_addons' ),
					'separator' => 'before',
					'default' => ''
				),
				array(
					'name' => 'title2_color',
					'label' => __( 'Title 2 color', 'trx_addons' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'default' => '',
					'description' => '',
//					'global' => array(
//						'active' => false,
//					),
					'selectors' => array(
						'{{WRAPPER}} .sc_item_title_text2' => 'color:{{VALUE}};'
					)
				),
				array(
					'name' => 'title2_color2',
					'label' => __( 'Title 2 color 2', 'trx_addons' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'default' => '',
					'description' => '',
//					'global' => array(
//						'active' => false,
//					),
					'condition' => array(
						'title_style' => 'gradient'
					),
				),
				array(
					'name' => 'gradient_fill2',
					'type' => \Elementor\Controls_Manager::SELECT,
					'label' => __( 'Gradient fill', 'trx_addons' ),
					'label_block' => false,
					'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_title_gradient_fills(),
					'default' => 'block',
					'condition' => array(
						'title_style' => 'gradient'
					),
				),
				array(
					'name' => 'gradient_direction2',
					'label' => __( 'Gradient direction', 'trx_addons' ),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'default' => array(
						'size' => 0,
						'unit' => 'px'
					),
					'size_units' => array( 'px' ),
					'range' => array(
						'px' => array(
							'min' => 0,
							'max' => 360
						)
					),
					'condition' => array(
						'title_style' => 'gradient'
					),
				),
				array(
					'name' => 'title2_border_color',
					'label' => __( 'Title 2 border color', 'trx_addons' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'default' => '',
					'description' => '',
//					'global' => array(
//						'active' => false,
//					),
					'selectors' => array(
						'{{WRAPPER}} .sc_item_title_text2' => '-webkit-text-stroke-color:{{VALUE}};'
					)
				),
				array(
					'name' => 'title2_border_width',
					'label' => __( 'Title 2 border width', 'trx_addons' ),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'default' => array(
						'size' => 0,
						'unit' => 'px'
					),
					'size_units' => array( 'px' ),
					'range' => array(
						'px' => array(
							'min' => 0,
							'max' => 10
						)
					),
					'selectors' => array(
						'{{WRAPPER}} .sc_item_title_text2' => '-webkit-text-stroke-width:{{SIZE}}px;'
					)
				),
				array(
					'name' => 'title2_bg_image',
					'type' => \Elementor\Controls_Manager::MEDIA,
					'label' => __( 'Title 2 bg image', 'trx_addons' ),
					'default' => array(
						'url' => '',
					),
					'selectors' => array(
						'{{WRAPPER}} .sc_item_title_text2' => '-webkit-text-fill-color:transparent;-webkit-background-clip:text;background-clip:text;background-image:url({{URL}});background-position:center center;background-size:cover;'
					)
				),
				array(
					'name' => 'title2_typography',
					'label' => __( 'Title 2 typography', 'trx_addons' ),
					'group' => \Elementor\Group_Control_Typography::get_type(),
//					'global' => [
//						'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
//					],
					'selector' => '{{WRAPPER}} .sc_item_title_text2'
				),
				array(
					'name' => 'title2_shadow',
					'label' => __( 'Title 2 shadow', 'trx_addons' ),
					'group' => \Elementor\Group_Control_Text_Shadow::get_type(),
					'selector' => '{{WRAPPER}} .sc_item_title_text2'
				),

				// Subtitle
				array(
					'name' => 'title_subtitle_heading',
					'type' => \Elementor\Controls_Manager::HEADING,
					'label' => esc_html__( 'Subtitle settings', 'trx_addons' ),
					'separator' => 'before',
				),
				array(
					'name' => 'subtitle',
					'type' => \Elementor\Controls_Manager::TEXT,
					'label' => __( "Subtitle", 'trx_addons' ),
					'placeholder' => __( "Title text", 'trx_addons' ),
					'default' => '',
					'separator' => 'before',
				),
				array(
					'name' => 'subtitle_align',
					'type' => \Elementor\Controls_Manager::SELECT,
					'label' => __( 'Subtitle alignment', 'trx_addons' ),
					'label_block' => false,
					'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_aligns(),
					'default' => 'none',
				),
				array(
					'name' => 'subtitle_position',
					'type' => \Elementor\Controls_Manager::SELECT,
					'label' => __( 'Subtitle position', 'trx_addons' ),
					'label_block' => false,
					'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_subtitle_positions(),
					'default' => trx_addons_get_setting('subtitle_above_title') ? 'above' : 'below',
				),
				array(
					'name' => 'subtitle_color',
					'label' => __( 'Subtitle color', 'trx_addons' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'default' => '',
					'description' => '',
					'selectors' => [
						'{{WRAPPER}} .sc_item_subtitle' => 'color: {{VALUE}};',
					],
//					'global' => array(
//						'active' => false,
//					),
				),

				// Description
				array(
					'name' => 'title_description_heading',
					'type' => \Elementor\Controls_Manager::HEADING,
					'label' => esc_html__( 'Description', 'trx_addons' ),
					'separator' => 'before',
				),
				array(
					'name' => 'description',
					'type' => \Elementor\Controls_Manager::TEXTAREA,
					'label' => __( 'Description', 'trx_addons' ),
					'label_block' => true,
					'placeholder' => __( "Short description of this block", 'trx_addons' ),
					'default' => '',
					'separator' => 'none',
					'rows' => 10,
					'show_label' => false,
				),
				array(
					'name' => 'description_color',
					'label' => __( 'Description color', 'trx_addons' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'default' => '',
					'description' => '',
					'selectors' => [
						'{{WRAPPER}} .sc_item_descr' => 'color: {{VALUE}};',
					],
//					'global' => array(
//						'active' => false,
//					),
				),
			);

			// Add button's params
			if ( $button ) {
				$params[] = array(
								'name' => 'title_link_heading',
								'type' => \Elementor\Controls_Manager::HEADING,
								'label' => esc_html__( 'Button settings', 'trx_addons' ),
								'separator' => 'before',
							);
				$params[] = array(
								'name' => 'link',
								'type' => \Elementor\Controls_Manager::URL,
								'label' => __( "Button's Link", 'trx_addons' ),
								'label_block' => false,
								'placeholder' => __( '//your-link.com', 'trx_addons' ),
								'separator' => 'before',
							);
				$params[] = array(
								'name' => 'link_text',
								'type' => \Elementor\Controls_Manager::TEXT,
								'label' => __( "Button's text", 'trx_addons' ),
								'label_block' => false,
								'placeholder' => __( "Link's text", 'trx_addons' ),
								'default' => ''
							);
				$params[] = array(
								'name' => 'link_size',
								'type' => \Elementor\Controls_Manager::SELECT,
								'label' => __( "Button's size", 'trx_addons' ),
								'label_block' => false,
								'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_button_sizes(),
								'default' => 'normal',
							);
				$params[] = array(
								'name' => 'link_style',
								'type' => \Elementor\Controls_Manager::SELECT,
								'label' => __( "Button's style", 'trx_addons' ),
								'label_block' => false,
								'options' => ! $is_edit_mode ? array() : apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('sc', 'button'), 'trx_sc_button'),
								'default' => 'default',
							);
				$params[] = array(
								'name' => 'link_image',
								'type' => \Elementor\Controls_Manager::MEDIA,
								'label' => __( "Button's image", 'trx_addons' ),
								'default' => array(
									'url' => '',
								),
							);
			}
			return apply_filters('trx_addons_filter_elementor_add_title_param', $params);
		}
		
		/**
		 * Add 'Title' controls to our shortcodes
		 * 
		 * @access protected
		 *
		 * @param boolean|string $group  A section's name or false to use a default name 'Title, Description & Button'
		 * @param array $add_params  Additional parameters to add or replace some default 'title' parameters
		 */
		protected function add_title_param( $group = false, $add_params = array() ) {
			$this->add_common_controls(
				array(
					'label' => $group === false ? __('Title, Description & Button', 'trx_addons') : $group,
					'section' => 'title',
					'tab' => \Elementor\Controls_Manager::TAB_LAYOUT
				),
				$this->get_title_param( ! isset( $add_params['button'] ) || $add_params['button'] ),
				$add_params
			);
		}


		// 'Query' parameters
		//------------------------------------------------------

		/**
		 * Return 'Query' parameters (ids, count, columns, offset, etc.) for our shortcodes
		 *
		 * @access protected
		 * 
		 * @trigger trx_addons_filter_elementor_add_query_param
		 *
		 * @param string $pt  Post type
		 *
		 * @return array  List of parameters
		 */
		protected function get_query_param( $pt ) {

			// Detect edit mode
			$is_edit_mode = trx_addons_elm_is_edit_mode();

			$params = array(
				trx_addons_get_option( 'sc_ids_type' ) == 'simple'
					? array(
							'name' => 'ids',
							'type' => \Elementor\Controls_Manager::TEXT,
							'label' => __( "IDs to show", 'trx_addons' ),
							"description" => wp_kses_data( __("Comma separated list of IDs to display. If not empty, parameters 'cat', 'offset' and 'count' are ignored!", 'trx_addons') ),
							'placeholder' => __( "IDs list", 'trx_addons' ),
							'default' => ''
						)
					: array_merge(
							array(
								'name' => 'ids',
								'type' => \Elementor\Controls_Manager::SELECT2,
								'label' => __( "Posts to show", 'trx_addons' ),
								"description" => wp_kses_data( __("List of posts to display. If not empty, parameters 'cat', 'offset' and 'count' are ignored!", 'trx_addons') ),
								'placeholder' => __( "Posts list", 'trx_addons' ),
								'multiple' => true,
								'options' => ! $is_edit_mode ? array() : trx_addons_get_list_posts( false, array(
																				'post_type' => $pt,
																				'not_selected' => false,
																				'orderby' => 'title',
																				'order' => 'asc'
																				)
																			),
								'default' => '',
							),
							trx_addons_is_on( trx_addons_get_option( 'use_ajax_to_get_ids' ) )
								? array(
										'select2options' => array(
																'ajax' => array(
																				'delay' => 600,
																				'type' => 'post',
																				'dataType' => 'json',
																				'url' => esc_url( trx_addons_add_to_url( admin_url('admin-ajax.php'), array(
																							'action' => 'ajax_sc_posts_search'
																						) ) ),
																				)
																),
									)
								: array()
						),
				array(
					'name' => "count",
					'type' => \Elementor\Controls_Manager::SLIDER,
					'label' => __('Count', 'trx_addons'),
					"description" => wp_kses_data( __("The number of displayed posts. If 'Posts to show' is not empty, this parameter is ignored.", 'trx_addons') ),
/*
					'condition' => array(
						'ids' => '',
					),
*/
					'default' => array(
						'size' => 3
					),
					'range' => array(
						'px' => array(
							'min' => 1,
							'max' => 100
						)
					)
				),
				array(
					'name' => "columns",
					'type' => \Elementor\Controls_Manager::SLIDER,
					'responsive' => true,
					'label' => __('Columns', 'trx_addons'),
					"description" => wp_kses_data( __("Specify the number of columns. If left empty or assigned the value '0' - auto detect by the number of items.", 'trx_addons') ),
					'default' => array(
						'size' => 0
					),
					'range' => array(
						'px' => array(
							'min' => 0,
							'max' => 12
						)
					)
				),
				array(
					'name' => "offset",
					'type' => \Elementor\Controls_Manager::SLIDER,
					'label' => __('Offset', 'trx_addons'),
					"description" => wp_kses_data( __("Specify the number of items to be skipped before the displayed items.", 'trx_addons') ),
/*
					'condition' => array(
						'ids' => '',
					),
*/
					'default' => array(
						'size' => 0
					),
					'range' => array(
						'px' => array(
							'min' => 0,
							'max' => 100
						)
					)
				),
				array(
					'name' => 'orderby',
					'type' => \Elementor\Controls_Manager::SELECT,
					'label' => __( 'Order by', 'trx_addons' ),
					'label_block' => false,
					'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_query_orderby(),
					'default' => 'none',
				),
				array(
					'name' => 'order',
					'type' => \Elementor\Controls_Manager::SELECT,
					'label' => __( 'Order', 'trx_addons' ),
					'label_block' => false,
					'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_query_orders(),
					'default' => 'asc',
				)
			);
			return apply_filters( 'trx_addons_filter_elementor_add_query_param', $params );
		}
		
		/**
		 * Add 'Query' section with controls to the shortcode's params
		 * 
		 * @access protected
		 *
		 * @param boolean|string $group  Group name. If false - add controls to the 'Query' section
		 * @param array $add_params  Additional parameters to add or override default 'query' parameters
		 * @param string $pt         Post type
		 */
		protected function add_query_param( $group = false, $add_params = array(), $pt = 'any' ) {
			$this->add_common_controls(
				array(
					'label' => $group === false ? __('Query', 'trx_addons') : $group,
					'section' => 'query'
				),
				$this->get_query_param( $pt ),
				$add_params
			);
		}


		// 'Hide' parameters
		//-------------------------------------------------------------

		/**
		 * Return 'Hide' parameters list for our shortcodes
		 *
		 * @access public
		 * 
		 * @trigger trx_addons_filter_elementor_add_hide_param
		 * 
		 * @param boolean $hide_on_frontpage  Add 'Hide on front page' parameter
		 * 
		 * @return array  				 List of parameters
		 */
		static function get_hide_param( $hide_on_frontpage = false ) {
			$params = array(
				array(
					'name' => 'hide_on_wide',
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'label' => __( 'Hide on wide screens', 'trx_addons' ),
					'label_off' => __( 'Off', 'trx_addons' ),
					'label_on' => __( 'On', 'trx_addons' ),
					'return_value' => '1'
				),
				array(
					'name' => 'hide_on_desktop',
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'label' => __( 'Hide on desktops', 'trx_addons' ),
					'label_off' => __( 'Off', 'trx_addons' ),
					'label_on' => __( 'On', 'trx_addons' ),
					'return_value' => '1'
				),
				array(
					'name' => 'hide_on_notebook',
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'label' => __( 'Hide on notebooks', 'trx_addons' ),
					'label_off' => __( 'Off', 'trx_addons' ),
					'label_on' => __( 'On', 'trx_addons' ),
					'return_value' => '1'
				),
				array(
					'name' => 'hide_on_tablet',
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'label' => __( 'Hide on tablets', 'trx_addons' ),
					'label_off' => __( 'Off', 'trx_addons' ),
					'label_on' => __( 'On', 'trx_addons' ),
					'return_value' => '1'
				),
				array(
					'name' => 'hide_on_mobile',
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'label' => __( 'Hide on mobile devices', 'trx_addons' ),
					'label_off' => __( 'Off', 'trx_addons' ),
					'label_on' => __( 'On', 'trx_addons' ),
					'return_value' => '1'
				)
			);
			if ( $hide_on_frontpage ) {
				$params[] = array(
					'name' => 'hide_on_frontpage',
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'label' => __( 'Hide on Frontpage', 'trx_addons' ),
					'label_off' => __( 'Off', 'trx_addons' ),
					'label_on' => __( 'On', 'trx_addons' ),
					'return_value' => '1'
				);
				$params[] = array(
					'name' => 'hide_on_singular',
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'label' => __( 'Hide on single posts and pages', 'trx_addons' ),
					'label_off' => __( 'Off', 'trx_addons' ),
					'label_on' => __( 'On', 'trx_addons' ),
					'return_value' => '1'
				);
				$params[] = array(
					'name' => 'hide_on_other',
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'label' => __( 'Hide on other pages', 'trx_addons' ),
					'label_off' => __( 'Off', 'trx_addons' ),
					'label_on' => __( 'On', 'trx_addons' ),
					'return_value' => '1'
				);
			}
			return apply_filters( 'trx_addons_filter_elementor_add_hide_param', $params );
		}
		
		/**
		 * Add 'Hide' controls to the shortcode's settings
		 * 
		 * @access protected
		 *
		 * @param boolean|string $group  Group name. If false - add controls to the 'Hide' section
		 * @param array $add_params      Additional parameters to add or replace default 'hide' parameters
		 */
		protected function add_hide_param( $group = false, $add_params = array() ) {
			$this->add_common_controls(
				array(
					'label' => $group === false ? __('Hide', 'trx_addons') : $group,
					'section' => 'hide',
					'tab' => \Elementor\Controls_Manager::TAB_LAYOUT
				),
				$this->get_hide_param( ! empty( $add_params['hide_on_frontpage'] ) ),
				$add_params
			);
		}

		
		// RENDER SHORTCODE'S CONTENT
		//------------------------------------------------------------

		/**
		 * Render Elementor widget.
		 * Call shortcode's function to display it's output in Elementor.
		 * 
		 * @access public
		 */
		public function render() {
			$sc_func = $this->get_sc_function();
			if ( function_exists( $sc_func ) ) {
				trx_addons_sc_stack_push( 'trx_sc_layouts' );		// To prevent wrap shortcodes output to the '<div class="sc_layouts_item"></div>'
				$output = call_user_func( $sc_func, $this->sc_prepare_atts( $this->get_settings(), $this->get_sc_name() ) );
				trx_addons_sc_stack_pop();
				trx_addons_show_layout( $output );
			}
		}

		/**
		 * Show message ( placeholder layout ) about not existing shortcode
		 * 
		 * @access public
		 *
		 * @param string $sc        Shortcode name
		 * @param string $plugin    Plugin name
		 */
		public function shortcode_not_exists( $sc, $plugin ) {
			?><div class="trx_addons_sc_not_exists">
				<h5 class="trx_addons_sc_not_exists_title"><?php echo esc_html( sprintf( __( 'Shortcode %s is not available!', 'trx_addons' ), $sc ) ); ?></h5>
				<div class="trx_addons_sc_not_exists_description">
					<p><?php echo esc_html( sprintf( __( 'Shortcode "%1$s" from plugin "%2$s" is not available in Elementor Editor!', 'trx_addons' ), $sc, $plugin ) ); ?></p>
					<p><?php esc_html_e( 'Possible causes:', 'trx_addons' ); ?></p>
					<ol class="trx_addons_sc_not_exists_causes">
						<li><?php echo esc_html( sprintf( __( 'Plugin "%s" is not installed or not active', 'trx_addons' ), $plugin ) ); ?></li>
						<li><?php esc_html_e( 'The plugin registers a shortcode later than it asks for Elementor Editor', 'trx_addons' ); ?></li>
					</ol>
					<p><?php esc_html_e( "So you see this message instead the shortcode in the editor. To see the real shortcode's output - save the changes and open this page in Frontend", 'trx_addons' ); ?></p>
				</div>
			</div><?php
		}

		/**
		 * Prepare shortcode's attributes for Elementor:
		 * - Convert array to string for attributes, added to the array 'plain_params'.
		 * - Sinchronize Elementor's setting '_element_id' with the shortcode's parameter 'id'
		 * - Add this object to the parameters with the key 'sc_elementor_object' to use it in the shortcode's templates
		 * - and more...
		 * 
		 * @access protected
		 * 
		 * @trigger trx_addons_filter_elementor_sc_prepare_atts
		 * @trigger trx_addons_filter_sc_generate_id
		 *
		 * @param array $atts       Shortcode's attributes
		 * @param string $sc        Shortcode name
		 * @param int $level        Recursion level
		 *
		 * @return array            Prepared attributes
		 */
		protected function sc_prepare_atts( $atts, $sc = '', $level = 0 ) {
			if ( is_array( $atts ) ) {
				foreach( $atts as $k => $v ) {
					// If current element is group (repeater) - call this function recursively
					if ( is_array( $v ) && isset( $v[0] ) && is_array( $v[0] ) ) {
						foreach ( $v as $k1 => $v1 ) {
							$atts[$k][$k1] = $this->sc_prepare_atts( $v1, $sc, $level + 1 );
						}

					// Current element is single control
					} else {
						// Make 'xxx' as plain string
						// and add 'xxx_extra' for each plain param
						if ( in_array( $k, array_keys( $this->plain_params ) ) ) {
							$prm = explode( '+', $this->plain_params[$k] );
							$atts["{$k}_extra"] = $v;
							if ( isset( $v[ $prm[0] ] ) ) {
								$atts[$k] = $v = $v[ $prm[0] ] . ( ! empty( $v[ $prm[0] ] ) && ! empty( $prm[1] ) && isset( $v[ $prm[1] ] ) ? $v[ $prm[1] ] : '' );
							}
						}
						// Sinchronize 'id' and '_element_id'
						if ( $k == '_element_id' ) {
							if ( empty( $atts['id'] ) ) {
								$atts['id'] = ! empty( $v ) 
												? $v . '_sc' // original '_element_id' is already applied to element's wrapper
												: ( apply_filters( 'trx_addons_filter_sc_generate_id', false )
													? trx_addons_generate_id( $this->get_sc_name() . '_' )
													: ''
													);
							}
/*
						// Sinchronize 'class' and '_css_classes'
						// Not used, because 'class' is already applied to element's wrapper
						} else if ($k == '_css_classes') {
							if ( empty( $atts['class'] ) ) {
								$atts['class'] = $v;
							}
*/
						// Add icon_type='elementor' if attr 'icon' is present and equal to the 'fa fa-xxx'
						// After update Elementor 2.6.0 'icon' is array (was a string in the previous versions) - convert it to string again
						} else if ( $k == 'icon' ) {
							if ( is_array( $v ) ) {
								$atts['icon_extra'] = $v;
								$atts['icon'] = $v = ! empty( $v['icon'] ) ? $v['icon'] : '';
							}
							if ( trx_addons_is_elementor_icon( $v ) ) {
								$atts['icon_type'] = 'elementor';
							}
						}
					}
				}
			}

			if ( $level == 0 ) {
				if ( trx_addons_get_setting( 'add_render_attributes' ) ) {
					$atts[ 'sc_elementor_object' ] = $this;	// Add shortcode object instance to the attributes array
				}
				$atts = apply_filters( 'trx_addons_filter_elementor_sc_prepare_atts', $atts, $sc );
			}

			return $atts;
		}

		
		// DISPLAY TEMPLATE'S PARTS
		//------------------------------------------------------------
		
		/**
		 * Display title, subtitle and description for some shortcodes. Use the template 'templates/tpe.sc_titles.php'
		 * 
		 * @access public
		 *
		 * @param string $sc    Shortcode name
		 * @param string $size  Size of the title: 'tiny', 'small', 'medium', 'large', 'huge', 'super'
		 */
		public function sc_show_titles( $sc, $size = '' ) {
			trx_addons_get_template_part( 'templates/tpe.sc_titles.php',
									'trx_addons_args_sc_show_titles',
									array( 'sc' => $sc, 'size' => $size, 'element' => $this )
								);
			
		}

		/**
		 * Display a link button for some shortcodes. Use the template 'templates/tpe.sc_links.php'
		 * 
		 * @access public
		 *
		 * @param string $sc    Shortcode name
		 */
		public function sc_show_links( $sc ) {
			trx_addons_get_template_part( 'templates/tpe.sc_links.php',
									'trx_addons_args_sc_show_links',
									array( 'sc' => $sc, 'element' => $this )
								);
		}

		/**
		 * Display a button for some shortcodes. Use the template 'shortcodes/button/tpe.sc_button.php'
		 * 
		 * @access public
		 *
		 * @param string $sc    Shortcode name
		 */
		public function sc_show_button( $sc ) {
			?><# 
			var settings_sc_button_old = settings;
			settings = {
				'title': settings.link_text,
				'link': settings.link,
				'type': settings.link_style,
				'size': settings.link_size,
				'class': 'sc_item_button sc_item_button_' + settings.link_style + ' sc_item_button_size_' + settings.link_size + ' <?php echo esc_attr($sc); ?>_button',
				'align': settings.title_align ? settings.title_align : 'none'
			};
			#><?php
			trx_addons_get_template_part( TRX_ADDONS_PLUGIN_SHORTCODES . 'button/tpe.button.php',
									'trx_addons_args_sc_show_button',
									array( 'sc' => $sc, 'element' => $this )
								);
			?><#
			settings = settings_sc_button_old;
			#><?php
		}

		/**
		 * Display a start portion of a slider layout (open a slider wrapper) for some shortcodes.
		 * Use the template 'templates/tpe.sc_slider_start.php'
		 * 
		 * @access public
		 * 
		 * @trigger trx_addons_filter_sc_show_slider_args
		 *
		 * @param string $sc    Shortcode name
		 */
		public function sc_show_slider_wrap_start( $sc ) {
			trx_addons_get_template_part( 'templates/tpe.sc_slider_start.php',
									'trx_addons_args_sc_show_slider_wrap',
									apply_filters( 'trx_addons_filter_sc_show_slider_args', array( 'sc' => $sc, 'element' => $this ) )
								);
		}

		/**
		 * Display an end portion of a slider layout (close a slider wrapper) for some shortcodes.
		 * Use the template 'templates/tpe.sc_slider_end.php'
		 * 
		 * @access public
		 * 
		 * @trigger trx_addons_filter_sc_show_slider_args
		 *
		 * @param string $sc    Shortcode name
		 */
		public function sc_show_slider_wrap_end( $sc ) {
			trx_addons_get_template_part( 'templates/tpe.sc_slider_end.php',
									'trx_addons_args_sc_show_slider_wrap',
									apply_filters( 'trx_addons_filter_sc_show_slider_args', array( 'sc' => $sc, 'element' => $this ) )
								);
		}

		/**
		 * Display a placeholder for a shortcode. Use the template 'templates/tpe.sc_placeholder.php'
		 * 
		 * @access public
		 * 
		 * @triggr trx_addons_filter_sc_placeholder_args
		 * 
		 * @param array $args   Array of parameters
		 */
		public function sc_show_placeholder( $args = array() ) {
			$args = array_merge(
						array(
							'sc' => $this->get_sc_name(),
							'title_field' => '',
							'element' => $this
						),
						$args
			);
			trx_addons_get_template_part( 'templates/tpe.sc_placeholder.php',
									'trx_addons_args_sc_placeholder',
									apply_filters( 'trx_addons_filter_sc_placeholder_args', $args )
								);
		}
	}
}
