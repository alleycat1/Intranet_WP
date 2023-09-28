<?php
/**
 * Shortcode: Hotspot (Elementor support)
 *
 * @package ThemeREX Addons
 * @since v1.94.0
 */

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }




// Elementor Widget
//------------------------------------------------------
if ( ! function_exists( 'trx_addons_sc_hotspot_add_in_elementor' ) ) {
	add_action( trx_addons_elementor_get_action_for_widgets_registration(), 'trx_addons_sc_hotspot_add_in_elementor' );
	function trx_addons_sc_hotspot_add_in_elementor() {
		
		if ( ! class_exists( 'TRX_Addons_Elementor_Widget' ) ) return;	

		class TRX_Addons_Elementor_Widget_Hotspot extends TRX_Addons_Elementor_Widget {

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
					'spot_x' => 'size+unit',
					'spot_y' => 'size+unit',
					'spot_image' => 'url',
					'image_link' => 'url'
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
				return 'trx_sc_hotspot';
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
				return __( 'Hotspot', 'trx_addons' );
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
					'section_sc_hotspot',
					[
						'label' => __( 'Hotspot', 'trx_addons' ),
					]
				);

				$this->add_control(
					'type',
					[
						'label' => __( 'Layout', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('sc', 'hotspot'), 'trx_sc_hotspot'),
						'default' => 'default'
					]
				);

				$this->add_control(
					'image',
					[
						'label' => __( 'Image', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::MEDIA,
						'default' => [ 'url' => '' ]
					]
				);

				$this->add_control(
					'image_link',
					[
						'label' => __( 'Image link', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::URL,
						'placeholder' => __( '//your-link.com', 'trx_addons' ),
						'default' => ['url' => '']
					]
				);

				$icon_param = $this->get_icon_param();
				foreach( $icon_param as $k => $v ) {
					$icon_param[ $k ] = array_merge( $v, [
															'condition' => [
																'spot_symbol' => [ 'icon' ]
															],
														] );
				}

				$this->add_control(
					'spots',
					[
						'label' => '',
						'type' => \Elementor\Controls_Manager::REPEATER,
						'default' => apply_filters('trx_addons_sc_param_group_value', [
							[
								// Spot
								'spot_visible' => 'always',
								'spot_x' => '50%',
								'spot_y' => '50%',
								'spot_symbol' => 'none',
								'spot_icon' => trx_addons_elementor_set_settings_icon( 'icon-plus' ),
								'spot_char' => '',
								'spot_image' => ['url' => ''],
								'spot_color' => '',
								'spot_bg_color' => '',
								'spot_sonar_color' => '',
								// Popup
								'align' => 'center',
								'source' => 'custom',
								'post' => 'none',
								'parts' => [],
								'image' => ['url' => ''],
								'title' => esc_html__( 'First spot', 'trx_addons' ),
								'subtitle' => $this->get_default_subtitle(),
								'description' => $this->get_default_description(),
								'price' => '',
								'link' => ['url' => ''],
								'link_text' => '',
								'position' => 'bc'
							]
						], 'trx_sc_hotspot'),
						'fields' => apply_filters('trx_addons_sc_param_group_params', array_merge(
							[
								[
									'name' => 'spot_visible',
									'label' => __( 'Visible', 'trx_addons' ),
									'label_block' => false,
									'type' => \Elementor\Controls_Manager::SWITCHER,
									'label_off' => __( 'Hover', 'trx_addons' ),
									'label_on' => __( 'Always', 'trx_addons' ),
									'return_value' => '1',
									'default' => '1',
								],
								[
									'name' => 'spot_x',
									'label' => __( 'X position', 'trx_addons' ),
									'type' => \Elementor\Controls_Manager::SLIDER,
									'default' => [
										'size' => 0,
										'unit' => '%'
									],
									'range' => [
										'%' => [
											'min' => 0,
											'max' => 100,
											'step' => 0.1
										],
									],
									'size_units' => [ '%' ]
								],
								[
									'name' => 'spot_y',
									'label' => __( 'Y position', 'trx_addons' ),
									'type' => \Elementor\Controls_Manager::SLIDER,
									'default' => [
										'size' => 0,
										'unit' => '%'
									],
									'range' => [
										'%' => [
											'min' => 0,
											'max' => 100,
											'step' => 0.1
										],
									],
									'size_units' => [ '%' ]
								],
								[
									'name' => 'spot_symbol',
									'label' => __( 'Spot symbol', 'trx_addons' ),
									'type' => \Elementor\Controls_Manager::SELECT,
									'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_hotspot_symbols(),
									'default' => 'none',
								],
								[
									'name' => 'spot_image',
									'label' => __( 'Image', 'trx_addons' ),
									'type' => \Elementor\Controls_Manager::MEDIA,
									'condition' => [
										'spot_symbol' => [ 'image' ]
									],
									'default' => [ 'url' => '' ],
								],
							],
							$icon_param,
							[
								[
									'name' => 'spot_char',
									'label' => __( 'Character', 'trx_addons' ),
									'type' => \Elementor\Controls_Manager::TEXT,
									'condition' => [
										'spot_symbol' => [ 'custom' ]
									],
									'default' => '',
								],
								[
									'name' => 'spot_color',
									'label' => __( 'Spot icon color', 'trx_addons' ),
									'type' => \Elementor\Controls_Manager::COLOR,
									'default' => '',
//									'global' => array(
//										'active' => false,
//									),
									'condition' => [
										'spot_symbol!' => [ 'none' ]
									],
								],
								[
									'name' => 'spot_bg_color',
									'label' => __( 'Spot bg color', 'trx_addons' ),
									'type' => \Elementor\Controls_Manager::COLOR,
									'default' => '',
//									'global' => array(
//										'active' => false,
//									),
								],
								[
									'name' => 'spot_sonar_color',
									'label' => __( 'Spot sonar color', 'trx_addons' ),
									'type' => \Elementor\Controls_Manager::COLOR,
									'default' => '',
//									'global' => array(
//										'active' => false,
//									),
								],
								[
									'name' => 'position',
									'label' => __( 'Popup position', 'trx_addons' ),
									'label_block' => false,
									'type' => \Elementor\Controls_Manager::SELECT,
									'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_positions(),
									'default' => 'bc',
									'responsive' => true,
								],
								[
									'name' => 'align',
									'label' => esc_html__( 'Popup alignment', 'elementor' ),
									'label_block' => false,
									'type' => \Elementor\Controls_Manager::CHOOSE,
									'options' => trx_addons_get_list_sc_aligns_for_elementor(),
									'default' => 'center',
								],
								[
									'name' => 'open',
									'label' => __( 'Popup open on', 'trx_addons' ),
									'label_block' => false,
									'type' => \Elementor\Controls_Manager::SWITCHER,
									'label_off' => __( 'Hover', 'trx_addons' ),
									'label_on' => __( 'Click', 'trx_addons' ),
									'return_value' => '1',
									'default' => '1',
								],
								[
									'name' => 'opened',
									'label' => __( 'Open on load', 'trx_addons' ),
									'label_block' => false,
									'type' => \Elementor\Controls_Manager::SWITCHER,
									'return_value' => '1',
									'default' => '',
								],
								[
									'name' => 'source',
									'label' => __( 'Data source', 'trx_addons' ),
									'label_block' => false,
									'type' => \Elementor\Controls_Manager::SELECT,
									'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_hotspot_sources(),
									'default' => 'custom',
								],
								[
									'name' => 'post_parts',
									'label' => __( 'Show parts', 'trx_addons' ),
									'label_block' => false,
									'type' => \Elementor\Controls_Manager::SELECT2,
									'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_hotspot_post_parts(),
									'multiple' => true,
									'default' => array( 'image', 'title', 'category', 'price' ),
									'condition' => [
										'source' => [ 'post' ],
									]
								],
								array_merge(
									[
										'name' => 'post',
										'label' => __( 'Post', 'trx_addons' ),
										'label_block' => true,
										'type' => \Elementor\Controls_Manager::SELECT2,
										'options' => ! $is_edit_mode ? array() : trx_addons_get_list_posts( false, array(
																						'post_type' => 'any',
																						'order' => 'asc',
																						'orderby' => 'title'
																						)
																					),
										'default' => '',
										'condition' => [
											'source' => [ 'post' ]
										]
									],
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
								[
									'name' => 'image',
									'label' => __( 'Image', 'trx_addons' ),
									'type' => \Elementor\Controls_Manager::MEDIA,
									'default' => [
										'url' => '',
									],
									'condition' => [
										'source' => [ 'custom' ]
									]
								],
								[
									'name' => 'title',
									'label' => __( 'Title', 'trx_addons' ),
									'label_block' => false,
									'type' => \Elementor\Controls_Manager::TEXT,
									'placeholder' => __( "Item's title", 'trx_addons' ),
									'default' => '',
									'condition' => [
										'source' => [ 'custom' ]
									]
								],
								[
									'name' => 'subtitle',
									'label' => __( 'Subtitle', 'trx_addons' ),
									'label_block' => false,
									'type' => \Elementor\Controls_Manager::TEXT,
									'placeholder' => __( "Item's subtitle", 'trx_addons' ),
									'default' => '',
									'condition' => [
										'source' => [ 'custom' ]
									]
								],
								[
									'name' => 'price',
									'label' => __( 'Price or other meta', 'trx_addons' ),
									'label_block' => false,
									'type' => \Elementor\Controls_Manager::TEXT,
									'placeholder' => __( "Price or meta", 'trx_addons' ),
									'default' => '',
									'condition' => [
										'source' => [ 'custom' ]
									]
								],
								[
									'name' => 'description',
									'label' => __( 'Description', 'trx_addons' ),
									'label_block' => true,
									'type' => \Elementor\Controls_Manager::TEXTAREA,
									'placeholder' => __( "Short description of this item", 'trx_addons' ),
									'default' => '',
									'separator' => 'none',
									'rows' => 10,
									'show_label' => false,
									'condition' => [
										'source' => [ 'custom' ]
									]
								],
								[
									'name' => 'link',
									'label' => __( 'Link', 'trx_addons' ),
									'label_block' => false,
									'type' => \Elementor\Controls_Manager::URL,
									'placeholder' => __( '//your-link.com', 'trx_addons' ),
									'default' => ['url' => ''],
									'condition' => [
										'source' => [ 'custom' ]
									]
								],
								[
									'name' => 'link_text',
									'label' => __( "Link's text", 'trx_addons' ),
									'label_block' => false,
									'type' => \Elementor\Controls_Manager::TEXT,
									'placeholder' => __( "Link's text", 'trx_addons' ),
									'default' => ''
								],
							] ),
						'trx_sc_hotspot' ),
						'title_field' => '{{{ title }}}'
					]
				);

				$this->end_controls_section();

				$this->add_title_param();
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
				trx_addons_get_template_part(TRX_ADDONS_PLUGIN_SHORTCODES . "hotspot/tpe.hotspot.php",
										'trx_addons_args_sc_hotspot',
										array('element' => $this)
									);
			}
		}
		
		// Register widget
		trx_addons_elm_register_widget( 'TRX_Addons_Elementor_Widget_Hotspot' );
	}
}
