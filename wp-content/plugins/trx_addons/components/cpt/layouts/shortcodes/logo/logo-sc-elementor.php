<?php
/**
 * Shortcode: Display site Logo (Elementor support)
 *
 * @package ThemeREX Addons
 * @since v1.6.08
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


// Elementor Widget
//------------------------------------------------------
if (!function_exists('trx_addons_sc_layouts_logo_add_in_elementor')) {
	add_action( trx_addons_elementor_get_action_for_widgets_registration(), 'trx_addons_sc_layouts_logo_add_in_elementor' );
	function trx_addons_sc_layouts_logo_add_in_elementor() {
		
		if (!class_exists('TRX_Addons_Elementor_Layouts_Widget')) return;	

		class TRX_Addons_Elementor_Widget_Layouts_Logo extends TRX_Addons_Elementor_Layouts_Widget {

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
					'logo_height' => 'size+unit',
					'logo' => 'url',
					'logo_retina' => 'url'
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
				return 'trx_sc_layouts_logo';
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
				return __( 'Layouts: Logo', 'trx_addons' );
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
				return 'eicon-logo';
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
				$this->start_controls_section(
					'section_sc_layouts_logo',
					[
						'label' => __( 'Layouts: Logo', 'trx_addons' ),
					]
				);

				$this->add_control(
					'type',
					[
						'label' => __( 'Layout', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => apply_filters('trx_addons_sc_type', array(
								'default' => esc_html__('Default', 'trx_addons')
							), 'trx_sc_layouts_logo'),
						'default' => 'default'
					]
				);

				$this->add_responsive_control(
					'logo_height',
					[
						'label' => __( 'Max height', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 80,
							'unit' => 'px'
						],
						'size_units' => ['px', 'em'],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 200
							],
							'em' => [
								'min' => 0,
								'max' => 20
							]
						],
						'selectors' => [
							'{{WRAPPER}} .logo_image' => 'max-height: {{SIZE}}{{UNIT}};',
						]
					]
				);

				$this->add_control(
					'logo',
					[
						'label' => __( 'Logo', 'trx_addons' ),
						'description' => wp_kses_data( __("Select or upload image for site's logo. If empty - theme-specific logo is used", 'trx_addons') ),
						'type' => \Elementor\Controls_Manager::MEDIA,
						'default' => [
							'url' => '',
						]
					]
				);

				$this->add_control(
					'logo_retina',
					[
						'label' => __( 'Logo Retina', 'trx_addons' ),
						'description' => wp_kses_data( __("Select or upload image for site's logo on the Retina displays", 'trx_addons') ),
						'type' => \Elementor\Controls_Manager::MEDIA,
						'default' => [
							'url' => '',
						]
					]
				);

				$this->add_control(
					'logo_text',
					[
						'label' => __( 'Logo text', 'trx_addons' ),
						'label_block' => false,
						'description' => wp_kses_data( __("Site name (used if logo is empty). If not specified - use blog name", 'trx_addons') ),
						'type' => \Elementor\Controls_Manager::TEXT,
						'default' => ''
					]
				);

				$this->add_control(
					'logo_slogan',
					[
						'label' => __( 'Logo slogan', 'trx_addons' ),
						'label_block' => false,
						'description' => wp_kses_data( __("Slogan or description below site name (used if logo is empty). If not specified - use blog description", 'trx_addons') ),
						'type' => \Elementor\Controls_Manager::TEXT,
						'default' => ''
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
				trx_addons_get_template_part(TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . "logo/tpe.logo.php",
										'trx_addons_args_sc_layouts_logo',
										array('element' => $this)
									);
			}
		}
		
		// Register widget
		trx_addons_elm_register_widget( 'TRX_Addons_Elementor_Widget_Layouts_Logo' );
	}
}
