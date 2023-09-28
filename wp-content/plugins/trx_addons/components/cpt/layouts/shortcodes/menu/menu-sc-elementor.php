<?php
/**
 * Shortcode: Display menu in the Layouts Builder (Elementor support)
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
if (!function_exists('trx_addons_sc_layouts_menu_add_in_elementor')) {
	add_action( trx_addons_elementor_get_action_for_widgets_registration(), 'trx_addons_sc_layouts_menu_add_in_elementor' );
	function trx_addons_sc_layouts_menu_add_in_elementor() {
		
		if (!class_exists('TRX_Addons_Elementor_Layouts_Widget')) return;	

		class TRX_Addons_Elementor_Widget_Layouts_Menu extends TRX_Addons_Elementor_Layouts_Widget {

			/**
			 * Retrieve widget name.
			 *
			 * @since 1.6.41
			 * @access public
			 *
			 * @return string Widget name.
			 */
			public function get_name() {
				return 'trx_sc_layouts_menu';
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
				return __( 'Layouts: Menu', 'trx_addons' );
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
				return 'eicon-nav-menu';
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
					'section_sc_layouts_menu',
					[
						'label' => __( 'Layouts: Menu', 'trx_addons' ),
					]
				);

				$this->add_control(
					'type',
					[
						'label' => __( 'Layout', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : apply_filters('trx_addons_sc_type', trx_addons_get_list_sc_layouts_menu(), 'trx_sc_layouts_menu'),
						'default' => 'default'
					]
				);

				$this->add_control(
					'direction',
					[
						'label' => __( 'Direction', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_directions(),
						'default' => 'horizontal',
						'condition' => [
							'type' => 'default'
						]
					]
				);

				$this->add_control(
					'submenu_style',
					[
						'label' => __( 'Submenu style', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_submenu_styles(),
						'default' => 'popup',
						'condition' => [
							'type' => 'default',
							'direction' => 'vertical',
						]
					]
				);

				$this->add_control(
					'location',
					[
						'label' => __( 'Location', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_menu_locations(),
						'default' => 'none'
					]
				);

				$this->add_control(
					'menu',
					[
						'label' => __( 'Menu', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_menus(),
						'default' => 'none',
						'condition' => [
							'location' => 'none'
						]
					]
				);

				$this->add_control(
					'hover',
					[
						'label' => __( 'Hover', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_menu_hover(),
						'default' => 'fade',
						'condition' => [
							'type' => 'default'
						]
					]
				);

				$this->add_control(
					'animation_in',
					[
						'label' => __( 'Submenu animation in', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_animations_in(),
						'default' => 'fadeIn',
						'condition' => [
							'type' => 'default'
						]
					]
				);

				$this->add_control(
					'animation_out',
					[
						'label' => __( 'Submenu animation out', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_animations_out(),
						'default' => 'fadeOut',
						'condition' => [
							'type' => 'default'
						]
					]
				);

				$this->add_control(
					'mobile_button',
					[
						'label' => __( 'Mobile button', 'trx_addons' ),
						'label_block' => false,
						'description' => wp_kses_data( __("Replace the menu with a menu button on mobile devices. Open the menu when the button is clicked.", 'trx_addons') ),
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'return_value' => '1'
					]
				);

				$this->add_control(
					'mobile_menu',
					[
						'label' => __( 'Add to the mobile menu', 'trx_addons' ),
						'label_block' => false,
						'description' => wp_kses_data( __("Use these menu items as a mobile menu (if mobile menu is not selected in the theme).", 'trx_addons') ),
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'return_value' => '1'
					]
				);
/*
				$this->add_control(
					'hide_on_mobile',
					[
						'label' => __( 'Hide on mobile devices', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'return_value' => '1'
					]
				);
*/
				
				$this->end_controls_section();
			}
		}
		
		// Register widget
		trx_addons_elm_register_widget( 'TRX_Addons_Elementor_Widget_Layouts_Menu' );
	}
}
