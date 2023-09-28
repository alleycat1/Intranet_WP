<?php
/**
 * Plugin support: Booked Appointments (Elementor support)
 *
 * @package ThemeREX Addons
 * @since v1.5
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


if ( ! function_exists( 'trx_addons_sc_booked_add_in_elementor_ba' ) ) {
	add_action( trx_addons_elementor_get_action_for_widgets_registration(), 'trx_addons_sc_booked_add_in_elementor_ba' );
	/**
	 * Register "Booked Appointments" widget in Elementor as wrapper for shortcode [booked_appointments]
	 * 
	 * @hooked elementor/widgets/register
	 */
	function trx_addons_sc_booked_add_in_elementor_ba() {

		if ( ! trx_addons_exists_booked() || ! class_exists( 'TRX_Addons_Elementor_Widget' ) ) {
			return;
		}
		
		class TRX_Addons_Elementor_Widget_Booked_Appontments extends TRX_Addons_Elementor_Widget {

			/**
			 * Retrieve widget name.
			 *
			 * @since 1.6.41
			 * @access public
			 *
			 * @return string Widget name.
			 */
			public function get_name() {
				return 'trx_sc_booked_appointments';
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
				return __( 'Booked Appointments', 'trx_addons' );
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
				return 'eicon-checkbox';
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
					'section_sc_booked_appointments',
					[
						'label' => __( 'ThemeREX Booked Appoinments', 'trx_addons' ),
					]
				);
				$this->add_control( 'sc_booked_appointments_heading', array(
										'type' => \Elementor\Controls_Manager::HEADING,
										'label' => esc_html__( 'This widget has no parameters!', 'trx_addons' ),
										'separator' => 'none',
				) );
				
				$this->end_controls_section();
			}

			// Return widget's layout
			public function render() {
				if (shortcode_exists('booked-appointments')) {
					if ( is_user_logged_in() ) {
						trx_addons_show_layout( do_shortcode( '[booked-appointments]' ) );
					}
				} else {
					$this->shortcode_not_exists( 'booked-appointments', 'Booked Appointment' );
				}
			}
		}
		
		// Register widget
		trx_addons_elm_register_widget( 'TRX_Addons_Elementor_Widget_Booked_Appontments' );
	}
}


if ( ! function_exists( 'trx_addons_sc_booked_add_in_elementor_bp' ) ) {
	add_action( trx_addons_elementor_get_action_for_widgets_registration(), 'trx_addons_sc_booked_add_in_elementor_bp' );
	/**
	 * Register "Booked Profile" widget in Elementor as wrapper for shortcode [booked-profile]
	 * 
	 * @hooked elementor/widgets/register
	 */
	function trx_addons_sc_booked_add_in_elementor_bp() {

		if ( ! trx_addons_exists_booked() || ! class_exists( 'TRX_Addons_Elementor_Widget' ) ) {
			return;
		}
		
		class TRX_Addons_Elementor_Widget_Booked_Profile extends TRX_Addons_Elementor_Widget {

			/**
			 * Retrieve widget name.
			 *
			 * @since 1.6.41
			 * @access public
			 *
			 * @return string Widget name.
			 */
			public function get_name() {
				return 'trx_sc_booked_profile';
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
				return __( 'Booked Profile', 'trx_addons' );
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
				return 'eicon-lock-user';
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
					'section_sc_booked_profile',
					[
						'label' => __( 'ThemeREX Booked Profile', 'trx_addons' ),
						'description' => __( 'This widget has no parameters!', 'trx_addons' ),
					]
				);
				$this->add_control( 'sc_booked_profile_heading', array(
										'type' => \Elementor\Controls_Manager::HEADING,
										'label' => esc_html__( 'This widget has no parameters!', 'trx_addons' ),
										'separator' => 'none',
				) );
				$this->end_controls_section();
			}

			// Return widget's layout
			public function render() {
				if (shortcode_exists('booked-profile')) {
					if ( is_user_logged_in() ) {
						trx_addons_show_layout( do_shortcode( '[booked-profile]' ) );
					}
				} else {
					$this->shortcode_not_exists( 'booked-profile', 'Booked Profile' );
				}
			}
		}
		
		// Register widget
		trx_addons_elm_register_widget( 'TRX_Addons_Elementor_Widget_Booked_Profile' );
	}
}

if ( ! function_exists( 'trx_addons_sc_booked_add_in_elementor_bc' ) ) {
	add_action( trx_addons_elementor_get_action_for_widgets_registration(), 'trx_addons_sc_booked_add_in_elementor_bc' );
	/**
	 * Register "Booked Calendar" widget in Elementor as wrapper for shortcode [booked-calendar]
	 * 
	 * @hooked elementor/widgets/register
	 */
	function trx_addons_sc_booked_add_in_elementor_bc() {

		if ( ! trx_addons_exists_booked() || ! class_exists( 'TRX_Addons_Elementor_Widget' ) ) {
			return;
		}
		
		class TRX_Addons_Elementor_Widget_Booked_Calendar extends TRX_Addons_Elementor_Widget {

			/**
			 * Retrieve widget name.
			 *
			 * @since 1.6.41
			 * @access public
			 *
			 * @return string Widget name.
			 */
			public function get_name() {
				return 'trx_sc_booked_calendar';
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
				return __( 'Booked Calendar', 'trx_addons' );
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
				return 'eicon-calendar';
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

				// Detect edit mode
				$is_edit_mode = trx_addons_elm_is_edit_mode();

				// Register controls
				$this->start_controls_section(
					'section_sc_booked',
					[
						'label' => __( 'ThemeREX Booked Calendar', 'trx_addons' ),
					]
				);

				$this->add_control(
					'style',
					[
						'label' => __( 'Layout', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => [
									'calendar' => esc_html__('Calendar', 'trx_addons'),
									'list' => esc_html__('List', 'trx_addons')
									],
						'default' => 'calendar'
					]
				);

				$this->add_control(
					'calendar',
					[
						'label' => __( 'Calendar', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_array_merge( array( 0 => trx_addons_get_not_selected_text( esc_html__( 'Select calendar', 'trx_addons' ) ) ), trx_addons_get_list_terms(false, 'booked_custom_calendars' ) ),
						'default' => '0'
					]
				);

				$this->add_control(
					'month',
					[
						'label' => __( 'Month', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_array_merge( array( 0 => trx_addons_get_not_selected_text( esc_html__( 'Current month', 'trx_addons' ) ) ), trx_addons_get_list_months() ),
						'default' => '0'
					]
				);

				$this->add_control(
					'year',
					[
						'label' => __( 'Year', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_array_merge( array( 0 => trx_addons_get_not_selected_text( esc_html__( 'Current year', 'trx_addons' ) ) ), trx_addons_get_list_range( date('Y'), date('Y') + 25 ) ),
						'default' => '0'
					]
				);
				
				$this->end_controls_section();
			}

			// Return widget's layout
			public function render() {
				if ( shortcode_exists('booked-calendar') ) {
					$atts = $this->sc_prepare_atts( $this->get_settings(), $this->get_sc_name() );
					trx_addons_show_layout(
						do_shortcode(
							sprintf( '[booked-calendar style="%1$s" calendar="%2$s" month="%3$s" year="%4$s"]',
																	$atts['style'],
																	$atts['calendar'],
																	$atts['month'],
																	$atts['year']
							)
						)
					);
				} else {
					$this->shortcode_not_exists( 'booked-calendar', 'Booked Calendar' );
				}
			}
		}
		
		// Register widget
		trx_addons_elm_register_widget( 'TRX_Addons_Elementor_Widget_Booked_Calendar' );
	}
}
