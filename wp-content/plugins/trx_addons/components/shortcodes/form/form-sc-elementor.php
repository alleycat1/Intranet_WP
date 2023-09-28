<?php
/**
 * Shortcode: Form (Elementor support)
 *
 * @package ThemeREX Addons
 * @since v1.2
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}



// Elementor Widget
//------------------------------------------------------
if (!function_exists('trx_addons_sc_form_add_in_elementor')) {
	add_action( trx_addons_elementor_get_action_for_widgets_registration(), 'trx_addons_sc_form_add_in_elementor' );
	function trx_addons_sc_form_add_in_elementor() {
		
		if (!class_exists('TRX_Addons_Elementor_Widget')) return;	

		class TRX_Addons_Elementor_Widget_Form extends TRX_Addons_Elementor_Widget {

			/**
			 * Retrieve widget name.
			 *
			 * @since 1.6.41
			 * @access public
			 *
			 * @return string Widget name.
			 */
			public function get_name() {
				return 'trx_sc_form';
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
				return __( 'Form', 'trx_addons' );
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
				return 'eicon-form-horizontal';
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
					'section_sc_form',
					[
						'label' => __( 'Form', 'trx_addons' ),
					]
				);

				$this->add_control(
					'type',
					[
						'label' => __( 'Layout', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('sc', 'form'), 'trx_sc_form'),
						'default' => 'default',
					]
				);

				$this->add_control(
					'style',
					[
						'label' => __( 'Style', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_input_hover(true),
						'default' => 'inherit',
					]
				);

				$this->add_control(
					'align',
					[
						'label' => __( 'Fields alignment', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_aligns(),
						'default' => 'none',
					]
				);

				$this->add_control(
					'email',
					[
						'label' => __( 'Your E-mail', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::TEXT,
						'placeholder' => __( "E-mail", 'trx_addons' ),
						'description' => __( 'Specify your E-mail for the detailed form. This address will be used to send you filled form data. If empty - admin e-mail will be used', 'trx_addons' ),
						'default' => ''
					]
				);

				$this->add_control(
					'phone',
					[
						'label' => __( 'Your phone', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::TEXT,
						'placeholder' => __( "Phone number", 'trx_addons' ),
						'condition' => array('type' => array('modern', 'detailed')),
						'default' => ''
					]
				);

				$this->add_control(
					'address',
					[
						'label' => __( 'Your address', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::TEXT,
						'placeholder' => __( "Address", 'trx_addons' ),
						'condition' => array('type' => array('modern', 'detailed')),
						'default' => ''
					]
				);

				$this->add_control(
					'button_caption',
					[
						'label' => __( 'Button caption', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::TEXT,
						'placeholder' => __( "Caption", 'trx_addons' ),
						'default' => ''
					]
				);

				$this->add_control(
					'labels',
					[
						'label' => __( 'Field labels', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'return_value' => '1'
					]
				);

				$this->end_controls_section();

				$this->add_title_param(false, array('button' => false));
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
				trx_addons_get_template_part(TRX_ADDONS_PLUGIN_SHORTCODES . "form/tpe.form.php",
										'trx_addons_args_sc_form',
										array('element' => $this)
									);
			}
		}
		
		// Register widget
		trx_addons_elm_register_widget( 'TRX_Addons_Elementor_Widget_Form' );
	}
}
