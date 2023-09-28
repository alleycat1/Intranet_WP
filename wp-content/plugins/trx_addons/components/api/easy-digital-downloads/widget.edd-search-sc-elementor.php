<?php
/**
 * Widget: Downloads Search (Advanced search form) (Elementor support)
 *
 * @package ThemeREX Addons
 * @since v1.6.34
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

if ( ! function_exists( 'trx_addons_sc_widget_edd_search_add_in_elementor' ) ) {
	add_action( trx_addons_elementor_get_action_for_widgets_registration(), 'trx_addons_sc_widget_edd_search_add_in_elementor' );
	/**
	 * Register widget "Themes Search" for Elementor
	 * 
	 * @hooked elementor/widgets/register
	 */
	function trx_addons_sc_widget_edd_search_add_in_elementor() {
		
		if (!class_exists('TRX_Addons_Elementor_Widget')) return;	

		class TRX_Addons_Elementor_Widget_edd_search extends TRX_Addons_Elementor_Widget {

			/**
			 * Retrieve widget name.
			 *
			 * @since 1.6.41
			 * @access public
			 *
			 * @return string Widget name.
			 */
			public function get_name() {
				return 'trx_widget_edd_search';
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
				return __( 'Themes Search', 'trx_addons' );
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
				return 'eicon-search';
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
					'section_sc_edd_search',
					[
						'label' => __( 'Themes Search', 'trx_addons' ),
					]
				);

				$this->add_control(
					'title',
					[
						'label' => __( 'Widget title', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::TEXT,
						'placeholder' => __( "Title", 'trx_addons' ),
						'default' => ''
					]
				);

				$this->add_control(
					'orderby',
					[
						'label' => __( 'Order by', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_query_orderby('', 'date,price,title,rand'),
						'default' => 'date'
					]
				);

				$this->add_control(
					'order',
					[
						'label' => __( 'Order', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_query_orders(),
						'default' => 'desc'
					]
				);

				$this->end_controls_section();
			}
		}
		
		// Register widget
		trx_addons_elm_register_widget( 'TRX_Addons_Elementor_Widget_edd_search' );
	}
}

if ( ! function_exists( 'trx_addons_widget_edd_search_black_list' ) ) {
	add_action( 'elementor/widgets/black_list', 'trx_addons_widget_edd_search_black_list' );
	/**
	 * Disable our widgets (shortcodes) to use in Elementor because we create special Elementor's widgets instead (see above)
	 * 
	 * @hooked elementor/widgets/black_list
	 *
	 * @param array $list  List of widgets to disable
	 * 
	 * @return array     Modified list
	 */
	function trx_addons_widget_edd_search_black_list( $list ) {
		$list[] = 'trx_addons_widget_edd_search';
		return $list;
	}
}
