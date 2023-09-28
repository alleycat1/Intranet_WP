<?php
/**
 * Shortcode: Display site meta and/or title and/or breadcrumbs (Elementor support)
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
if (!function_exists('trx_addons_sc_layouts_title_add_in_elementor')) {
	add_action( trx_addons_elementor_get_action_for_widgets_registration(), 'trx_addons_sc_layouts_title_add_in_elementor' );
	function trx_addons_sc_layouts_title_add_in_elementor() {
		
		if (!class_exists('TRX_Addons_Elementor_Layouts_Widget')) return;	

		class TRX_Addons_Elementor_Widget_Layouts_Title extends TRX_Addons_Elementor_Layouts_Widget {

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
					'height' => 'size+unit'
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
				return 'trx_sc_layouts_title';
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
				return __( 'Layouts: Title', 'trx_addons' );
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
				return 'eicon-heading';
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
				$this->start_controls_section(
					'section_sc_layouts_title',
					[
						'label' => __( 'Layouts: Title', 'trx_addons' ),
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
							), 'trx_sc_layouts_title'),
						'default' => 'default'
					]
				);

				$this->add_control(
					'align', 
					[
						'label' => __("Content alignment", 'trx_addons'),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_aligns(true, false),
						'default' => 'inherit'
					]
				);

				$this->add_control(
					'title',
					[
						'label' => __( 'Show post title', 'trx_addons' ),
						'label_off' => __( 'Hide', 'trx_addons' ),
						'label_on' => __( 'Show', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'return_value' => '1'
					]
				);

				$this->add_control(
					'meta',
					[
						'label' => __( 'Show post meta', 'trx_addons' ),
						'label_off' => __( 'Hide', 'trx_addons' ),
						'label_on' => __( 'Show', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'return_value' => '1'
					]
				);

				$this->add_control(
					'breadcrumbs',
					[
						'label' => __( 'Show breadcrumbs', 'trx_addons' ),
						'label_off' => __( 'Hide', 'trx_addons' ),
						'label_on' => __( 'Show', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'return_value' => '1'
					]
				);

				$this->add_control(
					'image',
					[
						'label' => __( 'Background image', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::MEDIA,
						'default' => [
							'url' => '',
						],
						'selectors' => [
							'{{WRAPPER}} .sc_layouts_title' => 'background-image: url({{URL}});',
						],
					]
				);

				$this->add_control(
					'use_featured_image',
					[
						'label' => __( 'Replace with featured image', 'trx_addons' ),
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'return_value' => '1'
					]
				);

				$this->add_responsive_control(
					'height',
					[
						'label' => __( 'Height of the block', 'trx_addons' ),
						'description' => wp_kses_data( __("Specify height of this block. If empty - use default height", 'trx_addons') ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 0,
							'unit' => 'px'
						],
						'size_units' => ['px', 'em'],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 800
							],
							'em' => [
								'min' => 0,
								'max' => 50
							]
						],
						'selectors' => [
							'{{WRAPPER}} .sc_layouts_title' => 'min-height: {{SIZE}}{{UNIT}};',
						],
						/*
						'condition' => [
							'use_featured_image' => '1'
						]
						*/
					]
				);

				$this->add_control(
					'hide_on_frontpage',
					[
						'label' => __( 'Hide on Frontpage', 'trx_addons' ),
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'return_value' => '1'
					]
				);

				$this->add_control(
					'hide_on_singular',
					[
						'label' => __( 'Hide on single posts and pages', 'trx_addons' ),
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'return_value' => '1'
					]
				);

				$this->add_control(
					'hide_on_other',
					[
						'label' => __( 'Hide on other pages', 'trx_addons' ),
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'return_value' => '1'
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
				trx_addons_get_template_part(TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . "title/tpe.title.php",
										'trx_addons_args_sc_layouts_title',
										array('element' => $this)
									);
			}
		}
		
		// Register widget
		trx_addons_elm_register_widget( 'TRX_Addons_Elementor_Widget_Layouts_Title' );
	}
}
