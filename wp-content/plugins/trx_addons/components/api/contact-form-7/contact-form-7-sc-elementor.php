<?php
/**
 * Plugin support: Contact Form 7 (Elementor support)
 *
 * @package ThemeREX Addons
 * @since v1.6.59
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

if ( ! function_exists( 'trx_addons_sc_cf7_add_in_elementor' ) ) {
	add_action( trx_addons_elementor_get_action_for_widgets_registration(), 'trx_addons_sc_cf7_add_in_elementor' );
	/**
	 * Register shortcode '[contact-form-7]' as the Elementor widget
	 * 
	 * @hooked trx_addons_elementor_get_action_for_widgets_registration
	 */
	function trx_addons_sc_cf7_add_in_elementor() {

		if ( ! trx_addons_exists_cf7() || ! class_exists( 'TRX_Addons_Elementor_Widget' ) ) {
			return;
		}
		
		class TRX_Addons_Elementor_Widget_Contact_Form_7 extends TRX_Addons_Elementor_Widget {

			/**
			 * Retrieve widget name.
			 *
			 * @since 1.6.41
			 * @access public
			 *
			 * @return string Widget name.
			 */
			public function get_name() {
				return 'trx_sc_contact_form_7';
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
				return __( 'Contact Form 7', 'trx_addons' );
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
				return ['trx_addons-support'];
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
					'section_sc_calculated_fields_form',
					[
						'label' => __( 'Contact Form 7', 'trx_addons' ),
					]
				);

				$forms = ! is_admin()
							? array()
							: trx_addons_array_merge( array( 0 => trx_addons_get_not_selected_text( esc_html__( 'Select form', 'trx_addons' ) ) ), trx_addons_get_list_cf7() );

				$this->add_control(
					'form_id',
					[
						'label' => __( 'Form', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => $forms,
						'default' => 0
					]
				);
				
				$this->end_controls_section();
			}

			// Return widget's layout
			public function render() {
				if ( shortcode_exists( 'contact-form-7' ) ) {
					$atts = $this->sc_prepare_atts( $this->get_settings(), $this->get_sc_name() );
					if ( ! empty( $atts['form_id'] ) ) {
						trx_addons_show_layout(
							do_shortcode(
								sprintf( '[contact-form-7 id="%s"]', $atts['form_id'] )
							)
						);
					}
				} else {
					$this->shortcode_not_exists('contact-form-7', 'Contact Form 7');
				}
			}
		}
		
		// Register widget
		trx_addons_elm_register_widget( 'TRX_Addons_Elementor_Widget_Contact_Form_7' );
	}
}
