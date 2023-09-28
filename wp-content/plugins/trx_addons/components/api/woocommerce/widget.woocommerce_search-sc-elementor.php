<?php
/**
 * Widget: WooCommerce Search (Advanced search form) (Elementor support)
 *
 * @package ThemeREX Addons
 * @since v1.6.38
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

if ( ! function_exists( 'trx_addons_sc_widget_woocommerce_search_add_in_elementor' ) ) {
	add_action( trx_addons_elementor_get_action_for_widgets_registration(), 'trx_addons_sc_widget_woocommerce_search_add_in_elementor' );
	/**
	 * Register widget 'WooCommerce Search' in the Elementor
	 * 
	 * @hooked elementor/widgets/register
	 */
	function trx_addons_sc_widget_woocommerce_search_add_in_elementor() {

		if ( ! trx_addons_exists_woocommerce() || ! class_exists( 'TRX_Addons_Elementor_Widget' ) ) {
			return;
		}
		
		class TRX_Addons_Elementor_Widget_Woocommerce_Search extends TRX_Addons_Elementor_Widget {

			/**
			 * Retrieve widget name.
			 *
			 * @since 1.6.41
			 * @access public
			 *
			 * @return string Widget name.
			 */
			public function get_name() {
				return 'trx_widget_woocommerce_search';
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
				return __( 'Woocommerce Search', 'trx_addons' );
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
					'section_sc_woocommerce_search',
					[
						'label' => __( 'Woocommerce Search', 'trx_addons' ),
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
					'type',
					[
						'label' => __( 'Type', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_woocommerce_search_types(),
						'default' => 'inline',
					]
				);

				$this->add_control(
					'ajax',
					[
						'label' => __( 'Use AJAX to reload products', 'trx_addons' ),
						'label_block' => false,
						'description' => wp_kses_data( __("Use AJAX to refresh the product list in the background instead of reloading the entire page.", 'trx_addons') ),
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'condition' => [
										'type' => 'filter'
										],
						'default' => '1',
						'return_value' => '1'
					]
				);

				$this->add_control(
					'apply',
					[
						'label' => __( 'Use "Apply" Button for Filtering', 'trx_addons' ),
						'label_block' => false,
						'description' => wp_kses_data( __("Select multiple filter values without the page reloading.", 'trx_addons') ),
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'condition' => [
										'type' => 'filter'
										],
						'default' => '1',
						'return_value' => '1'
					]
				);

				$this->add_control(
					'force_checkboxes',
					[
						'label' => __( 'Simple view', 'trx_addons' ),
						'label_block' => false,
						'description' => wp_kses_data( __('Display colors, images and buttons as checkboxes.', 'trx_addons') ),
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'condition' => [
										'type' => 'filter'
										],
						'default' => '',
						'return_value' => '1'
					]
				);

				$this->add_control(
					'show_counters',
					[
						'label' => __( 'Show counters', 'trx_addons' ),
						'label_block' => false,
						'description' => wp_kses_data( __("Show product counters after each item.", 'trx_addons') ),
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'condition' => [
										'type' => 'filter'
										],
						'default' => '1',
						'return_value' => '1'
					]
				);

				$this->add_control(
					'show_selected',
					[
						'label' => __( 'Show selected items', 'trx_addons' ),
						'label_block' => false,
						'description' => wp_kses_data( __('Show selected items counter and "Clear all" button.', 'trx_addons') ),
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'condition' => [
										'type' => 'filter'
										],
						'default' => '1',
						'return_value' => '1'
					]
				);

				$this->add_control(
					'expanded',
					[
						'label' => __( 'Initial toggle state', 'trx_addons' ),
						'label_block' => false,
						'description' => wp_kses_data( __('For sidebar placement ONLY!', 'trx_addons') ),
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_woocommerce_search_expanded(),
						'condition' => [
										'type' => 'filter'
										],
						'default' => 0,
					]
				);

				$this->add_control(
					'autofilters',
					[
						'label' => __( 'Auto filters in categories', 'trx_addons' ),
						'label_block' => false,
						'description' => wp_kses_data( __("Use product attributes as filters for current category.", 'trx_addons') ),
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'condition' => [
										'type' => 'filter'
										],
						'default' => '',
						'return_value' => '1'
					]
				);

				$this->add_control(
					'fields',
					[
						'label' => '',
						'type' => \Elementor\Controls_Manager::REPEATER,
						'default' => apply_filters('trx_addons_sc_param_group_value', [
							[
								'text' => '',
								'filter' => ''
							]
						], 'trx_widget_woocommerce_search'),
						'fields' => apply_filters('trx_addons_sc_param_group_params',
							[
								[
									'name' => 'text',
									'label' => __( 'Field text', 'trx_addons' ),
									'label_block' => false,
									'type' => \Elementor\Controls_Manager::TEXT,
									'placeholder' => __( "Text", 'trx_addons' ),
									'default' => ''
								],
								[
									'name' => 'filter',
									'label' => __( 'Field filter', 'trx_addons' ),
									'label_block' => false,
									'type' => \Elementor\Controls_Manager::SELECT,
									'options' => ! $is_edit_mode ? array() : trx_addons_get_list_woocommerce_search_filters(),
									'default' => 'none'
								]
							],
							'trx_widget_woocommerce_search'
						),
						'title_field' => '{{{ text }}} -> {{{ filter }}}',
					]
				);

				$this->add_control(
					'last_text',
					[
						'label' => __( 'Last text', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::TEXT,
						'placeholder' => __( "Last text", 'trx_addons' ),
						'default' => ''
					]
				);

				$this->add_control(
					'button_text',
					[
						'label' => __( 'Button text', 'trx_addons' ),
						'label_block' => false,
						'description' => wp_kses_data( __("Text of the button after all filters", 'trx_addons') ),
						'type' => \Elementor\Controls_Manager::TEXT,
						'placeholder' => __( "Last text", 'trx_addons' ),
						'default' => ''
					]
				);
				$this->end_controls_section();
			}
		}
		
		// Register widget
		trx_addons_elm_register_widget( 'TRX_Addons_Elementor_Widget_Woocommerce_Search' );
	}
}

if ( ! function_exists( 'trx_addons_widget_woocommerce_search_black_list' ) ) {
	add_action( 'elementor/widgets/black_list', 'trx_addons_widget_woocommerce_search_black_list' );
	/**
	 * Disable our widgets (shortcodes) to use in Elementor because we create special Elementor's widgets instead
	 * 
	 * @hooked elementor/widgets/black_list
	 *
	 * @param array $list  List of the widget's classes to avoid adding to Elementor
	 * 
	 * @return array     Modified list
	 */
	function trx_addons_widget_woocommerce_search_black_list( $list ) {
		$list[] = 'trx_addons_widget_woocommerce_search';
		return $list;
	}
}
