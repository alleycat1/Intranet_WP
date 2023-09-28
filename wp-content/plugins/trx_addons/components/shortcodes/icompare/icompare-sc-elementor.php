<?php
/**
 * Shortcode: Images compare (Elementor support)
 *
 * @package ThemeREX Addons
 * @since v1.97.0
 */

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }




// Elementor Widget
//------------------------------------------------------
if ( ! function_exists( 'trx_addons_sc_icompare_add_in_elementor' ) ) {
	add_action( trx_addons_elementor_get_action_for_widgets_registration(), 'trx_addons_sc_icompare_add_in_elementor' );
	function trx_addons_sc_icompare_add_in_elementor() {
		
		if ( ! class_exists( 'TRX_Addons_Elementor_Widget' ) ) return;	

		class TRX_Addons_Elementor_Widget_Icompare extends TRX_Addons_Elementor_Widget {

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
					'image1' => 'url',
					'image2' => 'url',
					'handler_image' => 'url',
					'handler_pos' => 'size'
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
				return 'trx_sc_icompare';
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
				return __( 'Images Compare', 'trx_addons' );
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
				return 'eicon-image-before-after';
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
					'section_sc_icompare',
					[
						'label' => __( 'Images Compare', 'trx_addons' ),
					]
				);

				$this->add_control(
					'type',
					[
						'label' => __( 'Layout', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('sc', 'icompare'), 'trx_sc_icompare'),
						'default' => 'default'
					]
				);

				$this->add_control(
					'image1',
					[
						'label' => __( 'Image 1 (before)', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::MEDIA,
						'default' => [ 'url' => '' ]
					]
				);

				$this->add_control(
					'image2',
					[
						'label' => __( 'Image 2 (after)', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::MEDIA,
						'default' => [ 'url' => '' ]
					]
				);

				$this->add_control(
					'direction',
					[
						'label' => __( 'Direction', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_directions(),
						'default' => 'vertical'
					]
				);

				$this->add_control(
					'event',
					[
						'label' => __( 'Move on', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_mouse_events(),
						'default' => 'drag'
					]
				);

				$this->add_control(
					'handler',
					[
						'label' => __( 'Handler style', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_icompare_handlers(),
						'default' => 'round'
					]
				);

				$this->add_control(
					'handler_separator',
					[
						'label' => __( 'Show separator', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'return_value' => '1'
					]
				);

				$this->add_control(
					'handler_pos',
					[
						'label' => __( 'Handler position', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 50,
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
					]
				);

				$params = trx_addons_get_icon_param('icon');
				$params = trx_addons_array_get_first_value( $params );
				unset( $params['name'] );
				$this->add_control( 'icon', $params );

				$this->add_control(
					'handler_image',
					[
						'label' => __( 'Handler image', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::MEDIA,
						'default' => [ 'url' => '' ]
					]
				);

				$this->add_control(
					'before_text',
					[
						'label' => __( 'Text "Before"', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::TEXT,
						'default' => ''
					]
				);

				$this->add_control(
					'before_pos',
					[
						'label' => __( 'Position "Before"', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_positions(),
						'default' => 'tl'
					]
				);

				$this->add_control(
					'after_text',
					[
						'label' => __( 'Text "After"', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::TEXT,
						'default' => ''
					]
				);

				$this->add_control(
					'after_pos',
					[
						'label' => __( 'Position "After"', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_positions(),
						'default' => 'br'
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
				trx_addons_get_template_part(TRX_ADDONS_PLUGIN_SHORTCODES . "icompare/tpe.icompare.php",
										'trx_addons_args_sc_icompare',
										array('element' => $this)
									);
			}
		}
		
		// Register widget
		trx_addons_elm_register_widget( 'TRX_Addons_Elementor_Widget_Icompare' );
	}
}
