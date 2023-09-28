<?php
/**
 * Widget: Cars Search (Advanced search form)
 *
 * @package ThemeREX Addons
 * @since v1.6.25
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

// Load widget
if (!function_exists('trx_addons_widget_cars_search_load')) {
	add_action( 'widgets_init', 'trx_addons_widget_cars_search_load' );
	function trx_addons_widget_cars_search_load() {
		register_widget('trx_addons_widget_cars_search');
	}
}

// Widget Class
class trx_addons_widget_cars_search extends TRX_Addons_Widget {

	function __construct() {
		$widget_ops = array('classname' => 'widget_cars_search', 'description' => esc_html__('Advanced search form for cars', 'trx_addons'));
		parent::__construct( 'trx_addons_widget_cars_search', esc_html__('ThemeREX Cars Search', 'trx_addons'), $widget_ops );
	}

	// Show widget
	function widget($args, $instance) {
		$title = apply_filters('widget_title', isset($instance['title']) ? $instance['title'] : '', $instance, $this->id_base);
		
		$style = isset($instance['style']) ? $instance['style'] : 'default';
		$type = isset($instance['type']) ? $instance['type'] : 'horizontal';
		$orderby = isset($instance['orderby']) ? $instance['orderby'] : 'date';
		$order = isset($instance['order']) ? $instance['order'] : 'desc';

		trx_addons_cpt_cars_load_scripts_front( true );

		trx_addons_get_template_part( array(
										TRX_ADDONS_PLUGIN_CPT . 'cars/tpl.widget.cars_search.'.trx_addons_esc( $type ).'.php',
										TRX_ADDONS_PLUGIN_CPT . 'cars/tpl.widget.cars_search.php'
										),
										'trx_addons_args_widget_cars_search',
										apply_filters(
											'trx_addons_filter_widget_args',
											array_merge(
												$args,
												compact('title', 'orderby', 'order', 'type', 'style')
											),
											$instance,
											'trx_addons_widget_cars_search'
										)
									);
	}

	// Update the widget settings.
	function update($new_instance, $instance) {
		$instance = array_merge($instance, $new_instance);
		return apply_filters('trx_addons_filter_widget_args_update', $instance, $new_instance, 'trx_addons_widget_cars_search');
	}

	// Displays the widget settings controls on the widget panel.
	function form($instance) {

		// Set up some default widget settings
		$instance = wp_parse_args( (array) $instance, apply_filters('trx_addons_filter_widget_args_default', array(
			'type' => 'horizontal',
			'title' => '',
			'orderby' => 'date',
			'order' => 'desc'
			), 'trx_addons_widget_cars_search')
		);
		
		do_action('trx_addons_action_before_widget_fields', $instance, 'trx_addons_widget_cars_search', $this);
		
		$this->show_field(array('name' => 'title',
								'title' => __('Widget title:', 'trx_addons'),
								'value' => $instance['title'],
								'type' => 'text'));
		
		do_action('trx_addons_action_after_widget_title', $instance, 'trx_addons_widget_cars_search', $this);

		$this->show_field(array('name' => 'type',
								'title' => __('Widget layout:', 'trx_addons'),
								'value' => $instance['type'],
								'options' => trx_addons_get_list_sc_directions(),
								'type' => 'radio'));

		$this->show_field(array('name' => 'orderby',
								'title' => __('Order search results by:', 'trx_addons'),
								'value' => $instance['orderby'],
								'options' => trx_addons_get_list_sc_query_orderby('', 'date,price,title'),
								'type' => 'select'));

		$this->show_field(array('name' => 'order',
								'title' => __('Order:', 'trx_addons'),
								'value' => $instance['order'],
								'options' => trx_addons_get_list_sc_query_orders(),
								'type' => 'radio'));
		
		do_action('trx_addons_action_after_widget_fields', $instance, 'trx_addons_widget_cars_search', $this);
	}
}

	

// Load required styles and scripts in the frontend
if ( !function_exists( 'trx_addons_widget_cars_search_load_scripts_front' ) ) {
	add_action("wp_enqueue_scripts", 'trx_addons_widget_cars_search_load_scripts_front', TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY);
	function trx_addons_widget_cars_search_load_scripts_front() {

		// Load animations for ontello icons
		if (is_search() || trx_addons_is_cars_page() || trx_addons_is_cars_agents_page())
			wp_enqueue_style( 'trx_addons-icons-animation', trx_addons_get_file_url('css/font-icons/css/animation.css'), array(), null );
	}
}



// Add shortcodes
//----------------------------------------------------------------------------
require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_CPT . 'cars/widget.cars_search-sc.php';

// Add shortcodes to Elementor
if ( trx_addons_exists_elementor() && function_exists('trx_addons_elm_init') ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_CPT . 'cars/widget.cars_search-sc-elementor.php';
}

// Add shortcodes to VC
if ( trx_addons_exists_vc() && function_exists( 'trx_addons_vc_add_id_param' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_CPT . 'cars/widget.cars_search-sc-vc.php';
}
