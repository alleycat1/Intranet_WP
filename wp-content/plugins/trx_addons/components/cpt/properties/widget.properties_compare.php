<?php
/**
 * Widget: Properties Compare
 *
 * @package ThemeREX Addons
 * @since v1.6.24
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

// Load widget
if (!function_exists('trx_addons_widget_properties_compare_load')) {
	add_action( 'widgets_init', 'trx_addons_widget_properties_compare_load' );
	function trx_addons_widget_properties_compare_load() {
		register_widget('trx_addons_widget_properties_compare');
	}
}

// Widget Class
class trx_addons_widget_properties_compare extends TRX_Addons_Widget {

	function __construct() {
		$widget_ops = array('classname' => 'widget_properties_compare', 'description' => esc_html__('Compare selected properties', 'trx_addons'));
		parent::__construct( 'trx_addons_widget_properties_compare', esc_html__('ThemeREX Properties Compare', 'trx_addons'), $widget_ops );
	}

	// Show widget
	function widget($args, $instance) {
		$title = apply_filters('widget_title', isset($instance['title']) ? $instance['title'] : '', $instance, $this->id_base);

		$list = urldecode(trx_addons_get_value_gpc('trx_addons_properties_compare_list', ''));
		$list = !empty($list) ? json_decode($list, true) : array();

		trx_addons_cpt_properties_load_scripts_front( true );

		trx_addons_get_template_part(TRX_ADDONS_PLUGIN_CPT . 'properties/tpl.widget.properties_compare.php',
										'trx_addons_args_widget_properties_compare',
										apply_filters('trx_addons_filter_widget_args',
											array_merge($args, compact('title', 'list')),
											$instance, 'trx_addons_widget_properties_compare')
									);
	}

	// Update the widget settings.
	function update($new_instance, $instance) {
		$instance = array_merge($instance, $new_instance);
		return apply_filters('trx_addons_filter_widget_args_update', $instance, $new_instance, 'trx_addons_widget_properties_compare');
	}

	// Displays the widget settings controls on the widget panel.
	function form($instance) {

		// Set up some default widget settings
		$instance = wp_parse_args( (array) $instance, apply_filters('trx_addons_filter_widget_args_default', array(
			'title' => ''
			), 'trx_addons_widget_properties_compare')
		);
		
		do_action('trx_addons_action_before_widget_fields', $instance, 'trx_addons_widget_properties_compare', $this);
		
		$this->show_field(array('name' => 'title',
								'title' => __('Widget title:', 'trx_addons'),
								'value' => $instance['title'],
								'type' => 'text'));
		
		do_action('trx_addons_action_after_widget_title', $instance, 'trx_addons_widget_properties_compare', $this);
		
		do_action('trx_addons_action_after_widget_fields', $instance, 'trx_addons_widget_properties_compare', $this);
	}
}


// Add shortcodes
//----------------------------------------------------------------------------
require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_CPT . 'properties/widget.properties_compare-sc.php';

// Add shortcodes to Elementor
if ( trx_addons_exists_elementor() && function_exists('trx_addons_elm_init') ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_CPT . 'properties/widget.properties_compare-sc-elementor.php';
}

// Add shortcodes to VC
if ( trx_addons_exists_vc() && function_exists( 'trx_addons_vc_add_id_param' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_CPT . 'properties/widget.properties_compare-sc-vc.php';
}
