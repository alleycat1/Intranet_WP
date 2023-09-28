<?php
/**
 * Shortcode: Smoke (Elementor support)
 *
 * @package ThemeREX Addons
 * @addon Smoke
 * @since v2.9.0
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}



// Elementor Widget
//------------------------------------------------------
if ( ! function_exists( 'trx_addons_sc_smoke_add_in_elementor' ) ) {
	add_action( trx_addons_elementor_get_action_for_widgets_registration(), 'trx_addons_sc_smoke_add_in_elementor' );
	function trx_addons_sc_smoke_add_in_elementor() {
		
		if ( ! class_exists( 'TRX_Addons_Elementor_Widget' ) ) return;	

		class TRX_Addons_Elementor_Widget_Smoke extends TRX_Addons_Elementor_Widget {

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
				$this->add_plain_params( [
					'image_repeat' => 'size',
					'cursor' => 'url',
					'smoke_curls' => 'size',
					'smoke_density' => 'size',
					'smoke_velosity' => 'size',
					'smoke_pressure' => 'size',
					'smoke_iterations' => 'size',
					'smoke_slap' => 'size',
					'pos_x' => 'size',
					'pos_y' => 'size',
					'shape' => 'size',
					'scale' => 'size',
					'rotation' => 'size',
				] );
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
				return 'trx_sc_smoke';
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
				return __( 'Smoke', 'trx_addons' );
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
				return 'eicon-animation';
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
					'section_sc_smoke',
					[
						'label' => __( 'Smoke', 'trx_addons' ),
					]
				);

				$this->add_control(
					'type',
					[
						'label' => __( 'Type', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => apply_filters( 'trx_addons_sc_type', trx_addons_smoke_list_types(), 'trx_sc_smoke' ),
						'default' => 'smoke',
					]
				);

				$this->add_control(
					'place',
					[
						'label' => __( 'Place', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => trx_addons_smoke_list_places(),
						'default' => 'body',
					]
				);

				$this->add_control(
					'bg_color',
					[
						'label' => __( 'Background Color', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '#000000',
						'global' => array(
							'active' => false,
						),
					]
				);

				$this->add_control(
					'tint_color',
					[
						'label' => __( 'Tint Color', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
						'global' => array(
							'active' => false,
						),
						'condition' => array(
							'type' => array('smoke', 'fog')
						)
					]
				);

				$this->add_control(
					'smoke_curls',
					[
						'label' => __( 'Curls', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 5,
							'unit' => 'px'
						],
						'size_units' => [ 'px' ],
						'range' => [
							'px' => [
								'min' => 1,
								'max' => 20
							]
						],
						'condition' => array(
							'type' => 'smoke'
						)
					]
				);

				$this->add_control(
					'smoke_density',
					[
						'label' => __( 'Density', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 0.97,
							'unit' => 'px'
						],
						'size_units' => [ 'px' ],
						'range' => [
							'px' => [
								'min' => 0.1,
								'max' => 1.0,
								'step' => 0.01
							]
						],
						'condition' => array(
							'type' => 'smoke'
						)
					]
				);

				$this->add_control(
					'smoke_velosity',
					[
						'label' => __( 'Velosity', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 0.98,
							'unit' => 'px'
						],
						'size_units' => [ 'px' ],
						'range' => [
							'px' => [
								'min' => 0.1,
								'max' => 1.0,
								'step' => 0.01
							]
						],
						'condition' => array(
							'type' => 'smoke'
						)
					]
				);

				$this->add_control(
					'smoke_pressure',
					[
						'label' => __( 'Pressure', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 0.8,
							'unit' => 'px'
						],
						'size_units' => [ 'px' ],
						'range' => [
							'px' => [
								'min' => 0.1,
								'max' => 1.0,
								'step' => 0.01
							]
						],
						'condition' => array(
							'type' => 'smoke'
						)
					]
				);

				$this->add_control(
					'smoke_iterations',
					[
						'label' => __( 'Iterations', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 10,
							'unit' => 'px'
						],
						'size_units' => [ 'px' ],
						'range' => [
							'px' => [
								'min' => 1,
								'max' => 20,
								'step' => 1
							]
						],
						'condition' => array(
							'type' => 'smoke'
						)
					]
				);

				$this->add_control(
					'smoke_slap',
					[
						'label' => __( 'Slap radius', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 0.6,
							'unit' => 'px'
						],
						'size_units' => [ 'px' ],
						'range' => [
							'px' => [
								'min' => 0.1,
								'max' => 1.0,
								'step' => 0.01
							]
						],
						'condition' => array(
							'type' => 'smoke'
						)
					]
				);

				$this->add_control(
					'use_image',
					[
						'label' => __( 'Use image', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'return_value' => '1',
						'condition' => array(
							'type' => array('smoke', 'fog')
						)
					]
				);

				$this->add_control(
					'image',
					[
						'label' => __( 'Image', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::MEDIA,
						'default' => array(
							'url' => '',
						),
						'condition' => array(
							'type' => array('smoke', 'fog'),
							'use_image!' => ''
						)
					]
				);

				$this->add_control(
					'image_repeat',
					[
						'label' => __( 'Repeater', 'trx_addons' ),
						'description' => wp_kses_data( __("Specify the number of repeated images to create a fog", 'trx_addons') ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 5,
							'unit' => 'px'
						],
						'size_units' => [ 'px' ],
						'range' => [
							'px' => [
								'min' => 1,
								'max' => 20
							]
						],
						'condition' => array(
							'type' => 'fog',
							'use_image!' => ''
						)
					]
				);

				$this->add_control(
					'cursor',
					[
						'label' => __( 'Cursor image', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::MEDIA,
						'default' => array(
							'url' => '',
						),
						'condition' => array(
							'type' => array('smoke', 'fog'),
						)
					]
				);

				$this->add_control(
					'spots',
					[
						'label' => '',
						'type' => \Elementor\Controls_Manager::REPEATER,
						'default' => apply_filters('trx_addons_sc_param_group_value', [
							[
								'motion'   => 0,
								'shape'    => 1,
								'color_1'  => '',
								'color_2'  => '',
								'pos_x'    => array( 'size' => 0, 'unit' => '%' ),
								'pos_y'    => array( 'size' => 0, 'unit' => '%' ),
								'scale'    => array( 'size' => 2, 'unit' => 'px' ),
								'rotation' => array( 'size' => 0, 'unit' => 'px' ),
							],
						], 'trx_sc_smoke'),
						'fields' => apply_filters('trx_addons_sc_param_group_params', [
							[
								'name' => 'motion',
								'label' => __( 'Motion', 'trx_addons' ),
								'label_block' => false,
								'type' => \Elementor\Controls_Manager::SELECT,
								'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_smoke_spot_motions(),
								'default' => 0
							],
							[
								'name' => 'pos_x',
								'label' => __( 'Position X', 'trx_addons' ),
								'label_block' => false,
								'type' => \Elementor\Controls_Manager::SLIDER,
								'default' => [
									'size' => 0,
									'unit' => '%'
								],
								'size_units' => [ '%' ],
								'range' => [
									'%' => [
										'min' => 0,
										'max' => 100
									]
								]
							],
							[
								'name' => 'pos_y',
								'label' => __( 'Position Y', 'trx_addons' ),
								'label_block' => false,
								'type' => \Elementor\Controls_Manager::SLIDER,
								'default' => [
									'size' => 0,
									'unit' => '%'
								],
								'size_units' => [ '%' ],
								'range' => [
									'%' => [
										'min' => 0,
										'max' => 100
									]
								]
							],
							[
								'name' => 'shape',
								'label' => __( 'Shape', 'trx_addons' ),
								'label_block' => false,
								'type' => \Elementor\Controls_Manager::SLIDER,
								'default' => [
									'size' => 1,
									'unit' => 'px'
								],
								'size_units' => [ 'px' ],
								'range' => [
									'px' => [
										'min' => 0.1,
										'max' => 5,
										'step' => 0.1
									]
								]
							],
							[
								'name' => 'scale',
								'label' => __( 'Scale', 'trx_addons' ),
								'label_block' => false,
								'type' => \Elementor\Controls_Manager::SLIDER,
								'default' => [
									'size' => 2,
									'unit' => 'px'
								],
								'size_units' => [ 'px' ],
								'range' => [
									'px' => [
										'min' => 0.5,
										'max' => 10,
										'step' => 0.1
									]
								]
							],
							[
								'name' => 'rotation',
								'label' => __( 'Rotation', 'trx_addons' ),
								'label_block' => false,
								'type' => \Elementor\Controls_Manager::SLIDER,
								'default' => [
									'size' => 0,
									'unit' => 'px'
								],
								'size_units' => [ 'px' ],
								'range' => [
									'px' => [
										'min' => -1,
										'max' => 1,
										'step' => 0.01
									]
								]
							],
							[
								'name' => 'color_1',
								'label' => __( 'Color 1', 'trx_addons' ),
								'type' => \Elementor\Controls_Manager::COLOR,
								'default' => '',
//									'global' => array(
//										'active' => false,
//									),
							],
							[
								'name' => 'color_2',
								'label' => __( 'Color 2', 'trx_addons' ),
								'type' => \Elementor\Controls_Manager::COLOR,
								'default' => '',
//									'global' => array(
//										'active' => false,
//									),
							],
						], 'trx_sc_spots' ),
						'title_field' => '{{{ (motion==0 ? "static" : (motion==1 ? "slow" : "fast")) + ": " + pos_x.size + "%, " + pos_y.size + "%" }}}',
						'condition' => array(
							'type' => 'spots'
						)
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
				$this->sc_show_placeholder( array(
					'title_field' => 'type',
				) );
			}
		}
		
		// Register widget
		trx_addons_elm_register_widget( 'TRX_Addons_Elementor_Widget_Smoke' );
	}
}
