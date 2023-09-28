<?php
/**
 * Widget: WooCommerce Title (Elementor support)
 *
 * @package ThemeREX Addons
 * @since v1.90.0
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

if ( ! function_exists( 'trx_addons_sc_widget_woocommerce_title_add_in_elementor' ) ) {
	add_action( trx_addons_elementor_get_action_for_widgets_registration(), 'trx_addons_sc_widget_woocommerce_title_add_in_elementor' );
	/**
	 * Register widget "WooCommerce Title" for Elementor
	 */
	function trx_addons_sc_widget_woocommerce_title_add_in_elementor() {

		if ( ! trx_addons_exists_woocommerce() || ! class_exists( 'TRX_Addons_Elementor_Widget' ) ) {
			return;
		}
		
		class TRX_Addons_Elementor_Widget_Woocommerce_Title extends TRX_Addons_Elementor_Widget {

			/**
			 * Retrieve widget name.
			 *
			 * @since 1.6.41
			 * @access public
			 *
			 * @return string Widget name.
			 */
			public function get_name() {
				return 'trx_widget_woocommerce_title';
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
				return __( 'Woocommerce Title', 'trx_addons' );
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
				return 'eicon-t-letter';
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
					'section_sc_woocommerce_title',
					[
						'label' => __( 'Woocommerce Title', 'trx_addons' ),
					]
				);

				$title_parts = trx_addons_get_list_woocommerce_title_parts();
				$this->add_control(
					'archive',
					[
						'label' => __( 'Products archive', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT2,
						'options' => $title_parts,
						'multiple' => true,
						'default' => array_keys($title_parts),
					]
				);

				$title_parts = trx_addons_get_list_woocommerce_title_parts( false );
				$this->add_control(
					'single',
					[
						'label' => __( 'Single product', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT2,
						'options' => $title_parts,
						'multiple' => true,
						'default' => array_keys($title_parts),
					]
				);

				$this->end_controls_section();
			}
		}
		
		// Register widget
		trx_addons_elm_register_widget( 'TRX_Addons_Elementor_Widget_Woocommerce_Title' );
	}
}

if ( ! function_exists( 'trx_addons_widget_woocommerce_title_black_list' ) ) {
	add_action( 'elementor/widgets/black_list', 'trx_addons_widget_woocommerce_title_black_list' );
	/**
	 * Disable our widgets (shortcodes) to use in Elementor because we create special Elementor's widgets instead
	 * 
	 * @hooked elementor/widgets/black_list
	 *
	 * @param array $list  List of blacklisted widgets
	 * 
	 * @return array     Modified list of blacklisted widgets
	 */
	function trx_addons_widget_woocommerce_title_black_list( $list ) {
		$list[] = 'trx_addons_widget_woocommerce_title';
		return $list;
	}
}
