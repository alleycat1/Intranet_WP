<?php
/**
 * Plugin support: The Events Calendar (Elementor support)
 *
 * @package ThemeREX Addons
 * @since v1.2
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

if ( ! function_exists('trx_addons_sc_events_add_in_elementor')) {
	add_action( trx_addons_elementor_get_action_for_widgets_registration(), 'trx_addons_sc_events_add_in_elementor' );
	/**
	 * Register a widget for Elementor
	 * 
	 * @hooked elementor/widgets/register
	 */
	function trx_addons_sc_events_add_in_elementor() {

		if ( ! trx_addons_exists_tribe_events() || ! class_exists( 'TRX_Addons_Elementor_Widget' ) ) {
			return;
		}
		
		class TRX_Addons_Elementor_Widget_Events extends TRX_Addons_Elementor_Widget {

			/**
			 * Retrieve widget name.
			 *
			 * @since 1.6.41
			 * @access public
			 *
			 * @return string Widget name.
			 */
			public function get_name() {
				return 'trx_sc_events';
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
				return __( 'Events list', 'trx_addons' );
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
					'section_sc_events',
					[
						'label' => __( 'Events list', 'trx_addons' ),
					]
				);

				$this->add_control(
					'type',
					[
						'label' => __( 'Layout', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('api', 'the-events-calendar'), 'trx_sc_events'),
						'default' => 'default'
					]
				);

				$this->add_control(
					'past',
					[
						'label' => __( 'Past events', 'trx_addons' ),
						'label_block' => false,
						'description' => wp_kses_data( __("Show the past events if checked, else - show upcoming events", 'trx_addons') ),
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Upc.', 'trx_addons' ),
						'label_on' => __( 'Past', 'trx_addons' ),
						'return_value' => '1'
					]
				);

				$this->add_control(
					'post_type',
					[
						'type' => \Elementor\Controls_Manager::HIDDEN,
						'render_type' => 'none',
						'default' => Tribe__Events__Main::POSTTYPE
					]
				);
				$this->add_control(
					'taxonomy',
					[
						'type' => \Elementor\Controls_Manager::HIDDEN,
						'render_type' => 'none',
						'default' => Tribe__Events__Main::TAXONOMY
					]
				);

				$this->add_control(
					'cat',
					[
						'label' => __( 'Category', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_array_merge( array( 0 => trx_addons_get_not_selected_text( esc_html__('Select category', 'trx_addons' ) ) ), trx_addons_get_list_terms( false, Tribe__Events__Main::TAXONOMY ) ),
						'default' => '0'
					]
				);
				
				$this->add_query_param('', array(), Tribe__Events__Main::POSTTYPE);

				$this->end_controls_section();
				
				$this->add_slider_param();
				
				$this->add_title_param();
			}
		}
		
		// Register widget
		trx_addons_elm_register_widget( 'TRX_Addons_Elementor_Widget_Events' );
	}
}
