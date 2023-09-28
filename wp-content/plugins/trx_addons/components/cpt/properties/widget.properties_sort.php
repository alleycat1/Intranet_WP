<?php
/**
 * Widget: Properties Sort
 *
 * @package ThemeREX Addons
 * @since v1.6.22
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

// Load widget
if (!function_exists('trx_addons_widget_properties_sort_load')) {
	add_action( 'widgets_init', 'trx_addons_widget_properties_sort_load' );
	function trx_addons_widget_properties_sort_load() {
		register_widget('trx_addons_widget_properties_sort');
	}
}

// Widget Class
class trx_addons_widget_properties_sort extends TRX_Addons_Widget {

	function __construct() {
		$widget_ops = array('classname' => 'widget_properties_sort', 'description' => esc_html__('Sort properties list by date, price or title', 'trx_addons'));
		parent::__construct( 'trx_addons_widget_properties_sort', esc_html__('ThemeREX Properties Sort', 'trx_addons'), $widget_ops );
	}

	// Show widget
	function widget($args, $instance) {
		$title = apply_filters('widget_title', isset($instance['title']) ? $instance['title'] : '', $instance, $this->id_base);

		$orderby = isset($instance['orderby']) ? $instance['orderby'] : 'date';
		$order = isset($instance['order']) ? $instance['order'] : 'desc';

		trx_addons_cpt_properties_load_scripts_front( true );

		trx_addons_get_template_part(TRX_ADDONS_PLUGIN_CPT . 'properties/tpl.widget.properties_sort.php',
										'trx_addons_args_widget_properties_sort',
										apply_filters('trx_addons_filter_widget_args',
											array_merge($args, compact('title', 'orderby', 'order')),
											$instance, 'trx_addons_widget_properties_sort')
									);
	}

	// Update the widget settings.
	function update($new_instance, $instance) {
		$instance = array_merge($instance, $new_instance);
		return apply_filters('trx_addons_filter_widget_args_update', $instance, $new_instance, 'trx_addons_widget_properties_sort');
	}

	// Displays the widget settings controls on the widget panel.
	function form($instance) {

		// Set up some default widget settings
		$instance = wp_parse_args( (array) $instance, apply_filters('trx_addons_filter_widget_args_default', array(
			'title' => '',
			'orderby' => 'date',
			'order' => 'desc'
			), 'trx_addons_widget_properties_sort')
		);
		
		do_action('trx_addons_action_before_widget_fields', $instance, 'trx_addons_widget_properties_sort', $this);
		
		$this->show_field(array('name' => 'title',
								'title' => __('Widget title:', 'trx_addons'),
								'value' => $instance['title'],
								'type' => 'text'));
		
		do_action('trx_addons_action_after_widget_title', $instance, 'trx_addons_widget_properties_sort', $this);

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
		
		do_action('trx_addons_action_after_widget_fields', $instance, 'trx_addons_widget_properties_sort', $this);
	}
}


// Add shortcodes
//----------------------------------------------------------------------------
require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_CPT . 'properties/widget.properties_sort-sc.php';

// Add shortcodes to Elementor
if ( trx_addons_exists_elementor() && function_exists('trx_addons_elm_init') ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_CPT . 'properties/widget.properties_sort-sc-elementor.php';
}

// Add shortcodes to VC
if ( trx_addons_exists_vc() && function_exists( 'trx_addons_vc_add_id_param' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_CPT . 'properties/widget.properties_sort-sc-vc.php';
}
