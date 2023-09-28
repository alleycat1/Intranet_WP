<?php
/**
 * Shortcode: Countdown (Elementor support)
 *
 * @package ThemeREX Addons
 * @since v1.4.3
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}



// Elementor Widget
//------------------------------------------------------
if (!function_exists('trx_addons_sc_countdown_add_in_elementor')) {
	add_action( trx_addons_elementor_get_action_for_widgets_registration(), 'trx_addons_sc_countdown_add_in_elementor' );
	function trx_addons_sc_countdown_add_in_elementor() {
		
		if (!class_exists('TRX_Addons_Elementor_Widget')) return;	

		class TRX_Addons_Elementor_Widget_Countdown extends TRX_Addons_Elementor_Widget {

			/**
			 * Retrieve widget name.
			 *
			 * @since 1.6.41
			 * @access public
			 *
			 * @return string Widget name.
			 */
			public function get_name() {
				return 'trx_sc_countdown';
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
				return __( 'Countdown', 'trx_addons' );
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
				return 'eicon-countdown';
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
					'section_sc_countdown',
					[
						'label' => __( 'Countdown', 'trx_addons' ),
					]
				);

				$this->add_control(
					'type',
					[
						'label' => __( 'Layout', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('sc', 'countdown'), 'trx_sc_countdown'),
						'default' => 'default',
					]
				);

				$this->add_control(
					'align',
					[
						'label' => __( 'Alignment', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_aligns(),
						'default' => 'none'
					]
				);

				$this->add_control(
					'count_restart',
					[
						'label' => __( 'Restart counter', 'trx_addons' ),
						'label_block' => false,
						'description' => __( 'Restart count from/to time on each page loading', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'return_value' => '1'
					]
				);

				$this->add_control(
					'count_to',
					[
						'label' => __( 'Count to', 'trx_addons' ),
						'label_block' => false,
						'description' => __( 'To: the date below is a finish date. From: the date below is a start date', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'From', 'trx_addons' ),
						'label_on' => __( 'To', 'trx_addons' ),
						'default' => '1',
						'return_value' => '1'
					]
				);

				$this->add_control(
					'date_time',
					[
						'label' => __( 'Date & Time', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::DATE_TIME,
						'default' => date('Y-m-d H:i:s'),
						'condition' => array(
							'count_restart' => ''
						)
					]
				);

				$this->add_control(
					'date_time_restart',
					[
						'label' => __( 'Time to restart', 'trx_addons' ),
						'description' => __( 'Specify the start value of a timer in a format "DD:HH:MM:SS" (DD and SS are optional)', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::TEXT,
						'condition' => array(
							'count_restart' => '1'
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
				trx_addons_get_template_part(TRX_ADDONS_PLUGIN_SHORTCODES . "countdown/tpe.countdown.php",
										'trx_addons_args_sc_countdown',
										array('element' => $this)
									);
			}
		}
		
		// Register widget
		trx_addons_elm_register_widget( 'TRX_Addons_Elementor_Widget_Countdown' );
	}
}
