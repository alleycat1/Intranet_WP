<?php
/**
 * Shortcode: Charts (Elementor support)
 *
 * @package ThemeREX Addons
 * @addon Charts
 * @since v2.8.0
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}



// Elementor Widget
//------------------------------------------------------
if ( ! function_exists( 'trx_addons_sc_charts_add_in_elementor' ) ) {
	add_action( trx_addons_elementor_get_action_for_widgets_registration(), 'trx_addons_sc_charts_add_in_elementor' );
	function trx_addons_sc_charts_add_in_elementor() {
		
		if ( ! class_exists( 'TRX_Addons_Elementor_Widget' ) ) return;	

		class TRX_Addons_Elementor_Widget_Charts extends TRX_Addons_Elementor_Widget {

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
				$plain_params = [ 'cutout' => 'size' ];
				$total = apply_filters( 'trx_addons_filter_charts_datasets_total', TRX_ADDONS_CHARTS_DATASETS_TOTAL );
				for ( $i = 1; $i <= $total; $i++ ) {
					$plain_params["dataset{$i}_border_width"] = 'size';
					$plain_params["dataset{$i}_point_size"] = 'size';
					$plain_params["dataset{$i}_value"] = 'size';
					$plain_params["dataset{$i}_tension"] = 'size';
				}
				$this->add_plain_params( $plain_params );
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
				return 'trx_sc_charts';
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
				return __( 'Charts', 'trx_addons' );
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
				return 'eicon-skill-bar';
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
					'section_sc_charts',
					[
						'label' => __( 'Charts', 'trx_addons' ),
					]
				);

				$this->add_control(
					'type',
					[
						'label' => __( 'Layout', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => apply_filters( 'trx_addons_sc_type', trx_addons_charts_list_types(), 'trx_sc_charts' ),
						'default' => 'line',
					]
				);

				$this->add_control(
					'legend',
					[
						'label' => __( 'Legend', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => trx_addons_charts_list_legend_positions(),
						'default' => 'none',
//						'condition' => [
//							'type' => ['radar','pie','polarArea']
//						]
					]
				);

				$this->add_control(
					'from_zero',
					[
						'label' => __( 'Start values from 0', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'return_value' => '1',
					]
				);

				$this->add_control(
					'cutout',
					[
						'label' => __( 'Cutout', 'trx_addons' ),
						'description' => wp_kses_data( __("Specify the pie cutout radius (in %)", 'trx_addons') ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 0
						],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 100
							]
						],
						'condition' => [
							'type' => ['pie','polarArea']
						]
					]
				);

				$this->add_control(
					'hover_offset',
					[
						'label' => __( 'Hover offset', 'trx_addons' ),
						'description' => wp_kses_data( __("Offset (in px) to shift an arc on mouse over", 'trx_addons') ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 4
						],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 100
							]
						],
						'condition' => [
							'type' => ['pie','polarArea']
						]
					]
				);

				// Datasets
				$dataset = [
					'label' => '',
					'type' => \Elementor\Controls_Manager::REPEATER,
					'default' => apply_filters('trx_addons_sc_param_group_value', [
						[
							'title' => esc_html__( 'First item', 'trx_addons' ),
							'value' => '60',
							'bg_color' => '',
							'border_color' => '',
						],
						[
							'title' => esc_html__( 'Second item', 'trx_addons' ),
							'value' => '80',
							'bg_color' => '',
							'border_color' => '',
						],
						[
							'title' => esc_html__( 'Third item', 'trx_addons' ),
							'value' => '75',
							'bg_color' => '',
							'border_color' => '',
						]
					], 'trx_sc_charts'),
					'fields' => apply_filters('trx_addons_sc_param_group_params',
								[
									[
										'name' => 'title',
										'label' => __( 'Title', 'trx_addons' ),
										'label_block' => false,
										'type' => \Elementor\Controls_Manager::TEXT,
										'placeholder' => __( "Item's title", 'trx_addons' ),
										'default' => ''
									],
									[
										'name' => 'value',
										'label' => __( "Item's value", 'trx_addons' ),
										'label_block' => false,
										'type' => \Elementor\Controls_Manager::TEXT,
										'placeholder' => __( "Item's value", 'trx_addons' ),
										'default' => ''
									],
									[
										'name' => 'bg_color',
										'label' => __( 'Background color', 'trx_addons' ),
										'type' => \Elementor\Controls_Manager::COLOR,
										'default' => '',
//										'global' => array(
//											'active' => false,
//										),
									],
									[
										'name' => 'border_color',
										'label' => __( 'Border color', 'trx_addons' ),
										'type' => \Elementor\Controls_Manager::COLOR,
										'default' => '',
//										'global' => array(
//											'active' => false,
//										),
									],
							],
							'trx_sc_charts'
					),
					'title_field' => '{{{ title }}}: {{{ value }}}',
				];

				$total = apply_filters( 'trx_addons_filter_charts_datasets_total', TRX_ADDONS_CHARTS_DATASETS_TOTAL );

				for ( $i = 1; $i <= $total; $i++ ) {
					$this->add_control(
						"dataset{$i}_separator",
						[
							'label' => sprintf( __( 'Dataset %d', 'trx_addons' ), $i ),
							'type' => \Elementor\Controls_Manager::HEADING,
							'separator' => 'before',
						]
					);

					$this->add_control(
						"dataset{$i}_enable",
						[
							'label' => __( 'Enable', 'trx_addons' ),
							'label_block' => false,
							'type' => \Elementor\Controls_Manager::SWITCHER,
							'label_off' => __( 'Off', 'trx_addons' ),
							'label_on' => __( 'On', 'trx_addons' ),
							'default' => $i == 1 ? '1' : '',
							'return_value' => '1',
						]
					);

					$this->add_control(
						"dataset{$i}_title",
						[
							'label' => __( 'Title', 'trx_addons' ),
							'label_block' => false,
							'type' => \Elementor\Controls_Manager::TEXT,
							'placeholder' => __( "Dataset title", 'trx_addons' ),
							'default' => sprintf( __( 'Dataset %d', 'trx_addons' ), $i ),
							'condition' => [
								"dataset{$i}_enable!" => ''
							]
						]
					);

					$this->add_control(
						"dataset{$i}_fill",
						[
							'label' => __( 'Fill chart', 'trx_addons' ),
							'label_block' => false,
							'type' => \Elementor\Controls_Manager::SWITCHER,
							'label_off' => __( 'Off', 'trx_addons' ),
							'label_on' => __( 'On', 'trx_addons' ),
							'return_value' => '1',
							'condition' => [
								"dataset{$i}_enable!" => '',
								'type' => ['line','radar']
							]
						]
					);

					$this->add_control(
						"dataset{$i}_point_size",
						[
							'label' => __( 'Point size', 'trx_addons' ),
							'type' => \Elementor\Controls_Manager::SLIDER,
							'default' => [
								'size' => 3
							],
							'range' => [
								'px' => [
									'min' => 0,
									'max' => 20
								]
							],
							'condition' => [
								"dataset{$i}_enable!" => '',
								'type' => ['line','radar']
							]
						]
					);

					$this->add_control(
						"dataset{$i}_point_style",
						[
							'label' => __( 'Point style', 'trx_addons' ),
							'label_block' => false,
							'type' => \Elementor\Controls_Manager::SELECT,
							'options' => trx_addons_charts_list_point_styles(),
							'default' => 'circle',
							'condition' => [
								"dataset{$i}_enable!" => '',
//								'type' => ['line','radar']
							]
						]
					);

					$this->add_control(
						"dataset{$i}_bg_color",
						[
							'label' => __( 'Background color', 'trx_addons' ),
							'label_block' => false,
							'type' => \Elementor\Controls_Manager::COLOR,
							'default' => '',
	//						'global' => array(
	//							'active' => false,
	//						),
							'condition' => [
								"dataset{$i}_enable!" => '',
							]
						]
					);

					$this->add_control(
						"dataset{$i}_border_width",
						[
							'label' => __( 'Border width', 'trx_addons' ),
							'type' => \Elementor\Controls_Manager::SLIDER,
							'default' => [
								'size' => 1
							],
							'range' => [
								'px' => [
									'min' => 0,
									'max' => 10
								]
							],
							'condition' => [
								"dataset{$i}_enable!" => '',
							]
						]
					);

					$this->add_control(
						"dataset{$i}_border_color",
						[
							'label' => __( 'Border color', 'trx_addons' ),
							'label_block' => false,
							'type' => \Elementor\Controls_Manager::COLOR,
							'default' => '',
	//						'global' => array(
	//							'active' => false,
	//						),
							'condition' => [
								"dataset{$i}_enable!" => '',
								"dataset{$i}_border_width[size]!" => ['','0',0]
							]
						]
					);

					$this->add_control(
						"dataset{$i}_border_join",
						[
							'label' => __( 'Border join', 'trx_addons' ),
							'label_block' => false,
							'type' => \Elementor\Controls_Manager::SELECT,
							'options' => trx_addons_charts_list_border_join_styles(),
							'default' => 'miter',
							'condition' => [
								"dataset{$i}_enable!" => '',
								'type' => ['line','radar','pie','polarArea']
							]
						]
					);

					$this->add_control(
						"dataset{$i}_tension",
						[
							'label' => __( 'Tension', 'trx_addons' ),
							'description' => wp_kses_data( __("Bezier curves coefficient 0.0 - 1.0", 'trx_addons') ),
							'type' => \Elementor\Controls_Manager::SLIDER,
							'default' => [
								'size' => 0
							],
							'range' => [
								'px' => [
									'min' => 0,
									'max' => 1,
									'step' => 0.01
								]
							],
							'condition' => [
								'type' => ['line','radar']
							]
						]
					);

/*
					// Remove title for second and more datasets
					if ( $i == 2 ) {
						if ( ! empty( $dataset['default'] ) && is_array( $dataset['default'] ) ) {
							for ( $d = 0; $d < count( $dataset['default'] ); $d++ ) {
								if ( isset( $dataset['default'][$d]['title'] ) ) {
									unset( $dataset['default'][$d]['title'] );
								}
							}
						}
						if ( ! empty( $dataset['fields'] ) && is_array( $dataset['fields'] ) ) {
							for ( $d = 0; $d < count( $dataset['fields'] ); $d++ ) {
								if ( isset( $dataset['fields'][$d]['name'] ) && $dataset['fields'][$d]['name'] == 'title' ) {
									unset( $dataset['fields'][$d] );
								}
							}
						}
					}
*/
					// Add condition
					$dataset['condition'] = array(
						"dataset{$i}_enable!" => '',
					);
					// Add field
					$this->add_control(
						"dataset{$i}",
						$dataset
					);
				}

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
				trx_addons_get_template_part( TRX_ADDONS_PLUGIN_ADDONS . "charts/tpe.charts.php",
												'trx_addons_args_sc_charts',
												array( 'element' => $this )
											);
			}
		}
		
		// Register widget
		trx_addons_elm_register_widget( 'TRX_Addons_Elementor_Widget_Charts' );
	}
}
