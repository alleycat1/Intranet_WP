<?php
/**
 * Widget: Flickr (Elementor support)
 *
 * @package ThemeREX Addons
 * @since v1.1
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}



// Elementor Widget
//------------------------------------------------------
if (!function_exists('trx_addons_sc_widget_flickr_add_in_elementor')) {
	add_action( trx_addons_elementor_get_action_for_widgets_registration(), 'trx_addons_sc_widget_flickr_add_in_elementor' );
	function trx_addons_sc_widget_flickr_add_in_elementor() {
		
		if (!class_exists('TRX_Addons_Elementor_Widget')) return;	

		class TRX_Addons_Elementor_Widget_Flickr extends TRX_Addons_Elementor_Widget {

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
					'flickr_count' => 'size',
					'flickr_columns' => 'size',
					'flickr_columns_gap' => 'size+unit'
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
				return 'trx_widget_flickr';
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
				return __( 'Widget: Flickr', 'trx_addons' );
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
				$this->start_controls_section(
					'section_sc_flickr',
					[
						'label' => __( 'Widget: Flickr', 'trx_addons' ),
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
					'flickr_api_key',
					[
						'label' => __( 'API key', 'trx_addons' ),
						'label_block' => false,
						'description' => wp_kses_data(__( 'API key from your Flickr application', 'trx_addons' )),
						'type' => \Elementor\Controls_Manager::TEXT,
						'placeholder' => __( "API key", 'trx_addons' ),
						'default' => ''
					]
				);
				
				$this->add_control(
					'flickr_username',
					[
						'label' => __( 'User name', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::TEXT,
						'placeholder' => __( "User name", 'trx_addons' ),
						'default' => ''
					]
				);
				
				$this->add_control(
					'flickr_count',
					[
						'label' => __( 'Number of photos', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 8
						],
						'range' => [
							'px' => [
								'min' => 1,
								'max' => 30
							]
						]
					]
				);
				
				$this->add_control(
					'flickr_columns',
					[
						'label' => __( 'Columns', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 4
						],
						'range' => [
							'px' => [
								'min' => 1,
								'max' => 12
							]
						],
						'selectors' => [
							'{{WRAPPER}} .flickr_images > a' => 'width:calc(100%/{{SIZE}});',
						],
					]
				);

				$this->add_control(
					'flickr_columns_gap',
					[
						'label' => __( 'Gap between columns', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 0,
							'unit' => 'px'
						],
						'size_units' => ['px', 'em'],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 100
							],
							'em' => [
								'min' => 0,
								'max' => 10,
								'step' => 0.1
							]
						],
						'selectors' => [
							'{{WRAPPER}} .widget_instagram_images' => 'margin-right: -{{SIZE}}{{UNIT}};',
							'{{WRAPPER}} .widget_instagram_images .widget_instagram_images_item_wrap' => 'padding: 0 {{SIZE}}{{UNIT}} {{SIZE}}{{UNIT}} 0;',
						],
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
				trx_addons_get_template_part(TRX_ADDONS_PLUGIN_WIDGETS . "flickr/tpe.flickr.php",
										'trx_addons_args_widget_flickr',
										array('element' => $this)
									);
			}
		}
		
		// Register widget
		trx_addons_elm_register_widget( 'TRX_Addons_Elementor_Widget_Flickr' );
	}
}


// Disable our widgets (shortcodes) to use in Elementor
// because we create special Elementor's widgets instead
if (!function_exists('trx_addons_widget_flickr_black_list')) {
	add_action( 'elementor/widgets/black_list', 'trx_addons_widget_flickr_black_list' );
	function trx_addons_widget_flickr_black_list($list) {
		$list[] = 'trx_addons_widget_flickr';
		return $list;
	}
}
