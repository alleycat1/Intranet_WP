<?php
/**
 * Shortcode: AI Chat (Elementor support)
 *
 * @package ThemeREX Addons
 * @since v2.22.0
 */

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }

// Elementor Widget
//------------------------------------------------------
if ( ! function_exists('trx_addons_sc_chat_add_in_elementor')) {
	add_action( trx_addons_elementor_get_action_for_widgets_registration(), 'trx_addons_sc_chat_add_in_elementor' );
	function trx_addons_sc_chat_add_in_elementor() {
		
		if ( ! class_exists( 'TRX_Addons_Elementor_Widget' ) ) return;	

		class TRX_Addons_Elementor_Widget_Chat extends TRX_Addons_Elementor_Widget {

			/**
			 * Retrieve widget name.
			 *
			 * @since 1.6.41
			 * @access public
			 *
			 * @return string Widget name.
			 */
			public function get_name() {
				return 'trx_sc_chat';
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
				return __( 'AI Helper Chat', 'trx_addons' );
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
				return 'eicon-text';
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
					'section_sc_chat',
					[
						'label' => __( 'AI Helper Chat', 'trx_addons' ),
					]
				);

				$this->add_control(
					'type',
					[
						'label' => __( 'Layout', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => apply_filters('trx_addons_sc_type', array( 'default' => __( 'Default', 'trx_addons' ) ), 'trx_sc_chat'),
						'default' => 'default'
					]
				);

				$this->add_control(
					'premium',
					[
						'label' => __( 'Premium Mode', 'trx_addons' ),
						'label_block' => false,
						'description' => __( 'Enables you to set a broader range of limits for text generation, which can be used for a paid text generation service. The limits are configured in the global settings.', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'return_value' => '1',
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
					'prompt',
					[
						'label' => __( 'Default prompt', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::TEXT,
						'default' => ''
					]
				);

				$this->add_control(
					'button_text',
					[
						'label' => __( 'Button text', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::TEXT,
						'default' => ''
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
				trx_addons_get_template_part(TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/chat/tpe.chat.php',
										'trx_addons_args_sc_chat',
										array('element' => $this)
									);
			}
		}
		
		// Register widget
		trx_addons_elm_register_widget( 'TRX_Addons_Elementor_Widget_Chat' );
	}
}
