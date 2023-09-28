<?php
/**
 * Shortcode: Display icons with two text lines (Elementor support)
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
if (!function_exists('trx_addons_sc_layouts_iconed_text_add_in_elementor')) {
	add_action( trx_addons_elementor_get_action_for_widgets_registration(), 'trx_addons_sc_layouts_iconed_text_add_in_elementor' );
	function trx_addons_sc_layouts_iconed_text_add_in_elementor() {
		
		if (!class_exists('TRX_Addons_Elementor_Layouts_Widget')) return;	

		class TRX_Addons_Elementor_Widget_Layouts_Iconed_Text extends TRX_Addons_Elementor_Layouts_Widget {

			/**
			 * Retrieve widget name.
			 *
			 * @since 1.6.41
			 * @access public
			 *
			 * @return string Widget name.
			 */
			public function get_name() {
				return 'trx_sc_layouts_iconed_text';
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
				return __( 'Layouts: Iconed Text', 'trx_addons' );
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
				return 'eicon-call-to-action';
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
					'section_sc_layouts_iconed_text',
					[
						'label' => __( 'Layouts: Iconed Text', 'trx_addons' ),
					]
				);

				$this->add_control(
					'type',
					[
						'label' => __( 'Layout', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => apply_filters('trx_addons_sc_type', array(
								'default' => esc_html__('Default', 'trx_addons'),
							), 'trx_sc_layouts_iconed_text'),
						'default' => 'default'
					]
				);

				$this->add_icon_param('');

				$this->add_control(
					'text1',
					[
						'label' => __( 'Text line 1', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::TEXT,
						'default' => ''
					]
				);

				$this->add_control(
					'text2',
					[
						'label' => __( 'Text line 2', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::TEXT,
						'default' => ''
					]
				);

				$this->add_control(
					'link', 
					[
						'label' => __("Link", 'trx_addons'),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::URL,
						'default' => [
							'url' => ''
						]
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
				trx_addons_get_template_part(TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . "iconed_text/tpe.iconed_text.php",
										'trx_addons_args_sc_layouts_iconed_text',
										array('element' => $this)
									);
			}
		}
		
		// Register widget
		trx_addons_elm_register_widget( 'TRX_Addons_Elementor_Widget_Layouts_Iconed_Text' );
	}
}
