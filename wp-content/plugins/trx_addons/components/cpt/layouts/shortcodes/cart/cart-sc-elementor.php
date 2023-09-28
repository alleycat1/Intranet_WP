<?php
/**
 * Shortcode: Display WooCommerce cart with items number and totals (Elementor support)
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
if (!function_exists('trx_addons_sc_layouts_cart_add_in_elementor')) {
	add_action( trx_addons_elementor_get_action_for_widgets_registration(), 'trx_addons_sc_layouts_cart_add_in_elementor' );
	function trx_addons_sc_layouts_cart_add_in_elementor() {
		
		if (!class_exists('TRX_Addons_Elementor_Layouts_Widget')) return;	

		class TRX_Addons_Elementor_Widget_Layouts_Cart extends TRX_Addons_Elementor_Layouts_Widget {

			/**
			 * Retrieve widget name.
			 *
			 * @since 1.6.41
			 * @access public
			 *
			 * @return string Widget name.
			 */
			public function get_name() {
				return 'trx_sc_layouts_cart';
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
				return __( 'Layouts: Cart', 'trx_addons' );
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
				return 'eicon-cart';
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
					'section_sc_layouts_cart',
					[
						'label' => __( 'Layouts: Cart', 'trx_addons' ),
					]
				);

				$this->add_control(
					'type',
					[
						'label' => __( 'Layout', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => apply_filters('trx_addons_sc_type', trx_addons_get_list_sc_layouts_cart_types(), 'trx_sc_layouts_cart'),
						'default' => 'default'
					]
				);

				$this->add_control(
					'market',
					[
						'label' => __( 'Market', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => apply_filters('trx_addons_sc_cart_market', array(
								'woocommerce' => esc_html__('WooCommerce', 'trx_addons'),
							), 'trx_sc_layouts_cart'),
						'default' => 'woocommerce'
					]
				);

				$this->add_control(
					'text',
					[
						'label' => __( 'Cart text', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::TEXT,
						'default' => ''
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
				trx_addons_get_template_part(TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . "cart/tpe.cart.php",
										'trx_addons_args_sc_layouts_cart',
										array('element' => $this)
									);
			}
		}
		
		// Register widget
		trx_addons_elm_register_widget( 'TRX_Addons_Elementor_Widget_Layouts_Cart' );
	}
}
