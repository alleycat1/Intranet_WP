<?php
/**
 * Shortcode: IGenerator (Elementor support)
 *
 * @package ThemeREX Addons
 * @since v2.20.2
 */

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }

use TrxAddons\AiHelper\Lists;
use TrxAddons\AiHelper\Utils;

// Elementor Widget
//------------------------------------------------------
if ( ! function_exists('trx_addons_sc_igenerator_add_in_elementor')) {
	add_action( trx_addons_elementor_get_action_for_widgets_registration(), 'trx_addons_sc_igenerator_add_in_elementor' );
	function trx_addons_sc_igenerator_add_in_elementor() {
		
		if ( ! class_exists( 'TRX_Addons_Elementor_Widget' ) ) return;	

		class TRX_Addons_Elementor_Widget_IGenerator extends TRX_Addons_Elementor_Widget {

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
					'number' => 'size',
					'prompt_width' => 'size',
					'width' => 'size',
					'height' => 'size',
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
				return 'trx_sc_igenerator';
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
				return __( 'AI Helper Image Generator', 'trx_addons' );
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
				return 'eicon-image';
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
				$models = ! $is_edit_mode ? array() : Lists::get_list_ai_image_models();
				$models_sd = ! $is_edit_mode ? array() : array_values( array_filter( array_keys( $models ), function( $key ) { return Utils::is_model_support_image_dimensions( $key ); } ) );
				$models_stability = ! $is_edit_mode ? array() : array_values( array_filter( array_keys( $models ), function( $key ) { return Utils::is_stability_ai_model( $key ); } ) );

				// Register controls
				$this->start_controls_section(
					'section_sc_igenerator',
					[
						'label' => __( 'AI Helper Image Generator', 'trx_addons' ),
					]
				);

				$this->add_control(
					'type',
					[
						'label' => __( 'Layout', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => apply_filters('trx_addons_sc_type', array( 'default' => __( 'Default', 'trx_addons' ) ), 'trx_sc_igenerator'),
						'default' => 'default'
					]
				);

				$this->add_control(
					'prompt',
					[
						'label' => __( 'Default prompt', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::TEXT,
						'default' => ''
					]
				);

				$this->add_control(
					'show_prompt_translated',
					[
						'label' => __( 'Show "Prompt translated"', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'default' => '1',
						'return_value' => '1',
					]
				);

				$this->add_responsive_control(
					'prompt_width',
					[
						'label' => __( 'Prompt field width', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 100,
							'unit' => 'px'
						],
						'size_units' => [ 'px' ],
						'range' => [
							'px' => [
								'min' => 50,
								'max' => 100
							]
						],
						'selectors' => [
							'{{WRAPPER}} .sc_igenerator_form_inner' => 'width: {{SIZE}}%;',
							'{{WRAPPER}} .sc_igenerator_message' => 'max-width: {{SIZE}}%;',
							'{{WRAPPER}} .sc_igenerator_limits' => 'max-width: {{SIZE}}%;',
						],
					]
				);

				$this->add_responsive_control(
					'align',
					[
						'label' => esc_html__( 'Alignment', 'elementor' ),
						'type' => \Elementor\Controls_Manager::CHOOSE,
						'options' => trx_addons_get_list_sc_flex_aligns_for_elementor(),
						'default' => '',
						'render_type' => 'template',
						'selectors' => [
							'{{WRAPPER}} .sc_igenerator_form' => 'align-items: {{VALUE}};',
							'{{WRAPPER}} .sc_igenerator_form_inner' => 'align-items: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'button_text',
					[
						'label' => __( 'Caption for "Generate"', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::TEXT,
						'default' => ''
					]
				);

				$this->add_control(
					'tags_label',
					[
						'label' => __( 'Tags label', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::TEXT,
						'default' => __( 'Popular Tags:', 'trx_addons' )
					]
				);

				$this->add_control(
					'tags',
					[
						'label' => __( 'Tags', 'trx_addons' ),
						'label_block' => true,
						'type' => \Elementor\Controls_Manager::REPEATER,
						'default' => apply_filters('trx_addons_sc_param_group_value', [
							[
								'title' => esc_html__( 'Creative', 'trx_addons' ),
								'prompt' => esc_html__( 'creative images with ...', 'trx_addons' ),
							],
							[
								'title' => esc_html__( 'Design', 'trx_addons' ),
								'prompt' => esc_html__( 'design of the ...', 'trx_addons' ),
							],
							[
								'title' => esc_html__( 'Illustration', 'trx_addons' ),
								'prompt' => esc_html__( 'illustration about ...', 'trx_addons' ),
							],
						], 'trx_sc_igenerator'),
						'fields' => apply_filters('trx_addons_sc_param_group_params', [
							[
								'name' => 'title',
								'label' => __( 'Title', 'trx_addons' ),
								'label_block' => false,
								'type' => \Elementor\Controls_Manager::TEXT,
								'placeholder' => __( "Tag's title", 'trx_addons' ),
								'default' => ''
							],
							[
								'name' => 'prompt',
								'label' => __( 'Prompt', 'trx_addons' ),
								'label_block' => false,
								'type' => \Elementor\Controls_Manager::TEXT,
								'placeholder' => __( "Prompt", 'trx_addons' ),
								'default' => ''
							],
						], 'trx_sc_igenerator' ),
						'title_field' => '{{{ title }}}'
					]
				);

				$this->end_controls_section();

				// Section: Generator settings
				$this->start_controls_section(
					'section_sc_igenerator_settings',
					[
						'label' => __( 'Generator Settings', 'trx_addons' ),
					]
				);

				$this->add_control(
					'premium',
					[
						'label' => __( 'Premium Mode', 'trx_addons' ),
						'label_block' => false,
						'description' => __( 'Enables you to set a broader range of limits for image generation, which can be used for a paid image generation service. The limits are configured in the global settings.', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'return_value' => '1',
					]
				);

				$this->add_control(
					'model',
					[
						'label' => __( 'Default model', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => $models,
						'default' => Utils::get_default_image_model()
					]
				);

				$this->add_control(
					'style',
					[
						'label' => __( 'Default style', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => Lists::get_list_stability_ai_styles(),
						'default' => '',
						'condition' => [
							'model' => $models_stability,
						]
					]
				);

				$this->add_control(
					'show_settings',
					[
						'label' => __( 'Show button "Settings"', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'return_value' => '1',
					]
				);

				$this->add_control(
					'show_settings_size',
					[
						'label' => __( 'Image dimensions picker', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'return_value' => '1',
						'condition' => [
							'show_settings' => '1'
						]
					]
				);

				$this->add_control(
					'show_limits',
					[
						'label' => __( 'Show limits', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'return_value' => '1',
					]
				);

				$this->add_control(
					'show_download',
					[
						'label' => __( 'Show button "Download"', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'return_value' => '1',
					]
				);

				$this->add_control(
					'show_popup',
					[
						'label' => __( 'Open images in the popup', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'return_value' => '1',
					]
				);

				$this->add_control(
					'number',
					[
						'label' => __( 'Generate at once', 'trx_addons' ),
						'description' => wp_kses_data( __("Specify the number of images to be generated at once (from 1 to 10)", 'trx_addons') ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 3,
							'unit' => 'px'
						],
						'size_units' => [ 'px' ],
						'range' => [
							'px' => [
								'min' => 1,
								'max' => 10
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
							'size' => 0,
							'unit' => 'px'
						],
						'size_units' => [ 'px' ],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 12
							]
						]
					]
				);

				$this->add_control(
					'size',
					[
						'label' => __( 'Image size', 'trx_addons' ),
						'label_block' => false,
						'description' => wp_kses_data( __("Select the size of generated images.", 'trx_addons') ),
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => Lists::get_list_ai_image_sizes(),
						'default' => '256x256',
					]
				);

				$this->add_control(
					'width',
					[
						'label' => __( 'Image width', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 0,
							'unit' => 'px'
						],
						'size_units' => [ 'px' ],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => Utils::get_max_image_width(),
								'step' => 8

							]
						],
						'condition' => [
							'model' => $models_sd,
							'size' => 'custom'
						]
					]
				);

				$this->add_control(
					'height',
					[
						'label' => __( 'Image height', 'trx_addons' ),
						'description' => wp_kses_data( __("Specify the image width and height for Stable Diffusion models only. If 0 or empty - a size from the field above will be used.", 'trx_addons') ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 0,
							'unit' => 'px'
						],
						'size_units' => [ 'px' ],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => Utils::get_max_image_height(),
								'step' => 8
							]
						],
						'condition' => [
							'model' => $models_sd,
							'size' => 'custom'
						]
					]
				);

				// $this->add_control(
				// 	'upscale',
				// 	[
				// 		'label' => __( 'Upscale image', 'trx_addons' ),
				// 		'label_block' => false,
				// 		'type' => \Elementor\Controls_Manager::SWITCHER,
				// 		'return_value' => '1',
				// 		'condition' => [
				// 			'model' => $models_sd
				// 		]
				// 	]
				// );

				// $this->add_control(
				// 	'quality',
				// 	[
				// 		'label' => __( 'High quality', 'trx_addons' ),
				// 		'label_block' => false,
				// 		'type' => \Elementor\Controls_Manager::SWITCHER,
				// 		'return_value' => '1',
				// 		'condition' => [
				// 			'model' => $models_sd
				// 		]
				// 	]
				// );

				// $this->add_control(
				// 	'panorama',
				// 	[
				// 		'label' => __( 'Panorama', 'trx_addons' ),
				// 		'label_block' => false,
				// 		'type' => \Elementor\Controls_Manager::SWITCHER,
				// 		'return_value' => '1',
				// 		'condition' => [
				// 			'model' => $models_sd
				// 		]
				// 	]
				// );

				$this->end_controls_section();

				// Section: Demo images
				$this->start_controls_section(
					'section_sc_igenerator_demo',
					[
						'label' => __( 'Demo Images', 'trx_addons' ),
					]
				);

				$this->add_control(
					'demo_images',
					[
						'label' => '',
						'description' => wp_kses_data( __("Selected images will be used instead of the image generator as a demo mode when limits are reached", 'trx_addons') ),
						'type' => \Elementor\Controls_Manager::GALLERY,
						'default' => [],
					]
				);

				$this->add_control(
					'demo_thumb_size',
					[
						'label' => __( 'Demo thumb size', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_thumbnail_sizes(),
						'default' => apply_filters( 'trx_addons_filter_thumb_size',
													trx_addons_get_thumb_size( 'avatar' ),
													'trx_addons_sc_igenerator',
													array()
												)
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
				trx_addons_get_template_part(TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/igenerator/tpe.igenerator.php',
										'trx_addons_args_sc_igenerator',
										array('element' => $this)
									);
			}
		}
		
		// Register widget
		trx_addons_elm_register_widget( 'TRX_Addons_Elementor_Widget_IGenerator' );
	}
}
