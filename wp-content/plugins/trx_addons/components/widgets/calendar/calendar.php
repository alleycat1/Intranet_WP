<?php
/**
 * Widget: Calendar
 *
 * @package ThemeREX Addons
 * @since v1.0
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

// Load widget
if (!function_exists('trx_addons_widget_calendar_load')) {
	add_action( 'widgets_init', 'trx_addons_widget_calendar_load' );
	function trx_addons_widget_calendar_load() {
		register_widget('trx_addons_widget_calendar');
	}
}

// Widget Class
class trx_addons_widget_calendar extends TRX_Addons_Widget {

	function __construct() {
		$widget_ops = array('classname' => 'widget_calendar', 'description' => esc_html__('Standard WP Calendar with short week days', 'trx_addons'));
		parent::__construct( 'trx_addons_widget_calendar', esc_html__('ThemeREX Calendar', 'trx_addons'), $widget_ops );
	}

	// Show widget
	function widget($args, $instance) {
		extract($args);

		$title = apply_filters( 'widget_title', isset($instance['title']) ? $instance['title'] : '', $instance, $this->id_base );
		$weekdays = isset($instance['weekdays']) ? $instance['weekdays'] : 'short';
		
		$output = get_calendar($weekdays=='initial', false);

		if (!empty($output)) {
	
			// Before widget (defined by themes)
			trx_addons_show_layout($before_widget);
			
			// Display the widget title if one was input (before and after defined by themes)
			if ($title) trx_addons_show_layout($before_title . $title . $after_title);
	
			// Display widget body
			trx_addons_show_layout( apply_filters( 'trx_addons_filter_widget_output', $output, 'trx_addons_widget_calendar', $instance ) );
			
			// After widget (defined by themes)
			trx_addons_show_layout($after_widget);
		}
	}

	// Update the widget settings.
	function update($new_instance, $instance) {
		$instance = array_merge($instance, $new_instance);
		$instance['weekdays'] = ! empty( $new_instance['weekdays'] ) && $new_instance['weekdays'] == 'short' ? 'short' : 'initial';
		return $instance;
	}

	// Displays the widget settings controls on the widget panel.
	function form($instance) {

		// Set up some default widget settings
		$instance = wp_parse_args( (array) $instance, array(
			'title' => '',
			'weekdays' => 'short'
			)
		);
		
		do_action('trx_addons_action_before_widget_fields', $instance, 'trx_addons_widget_calendar', $this);

		$this->show_field(array('name' => 'title',
								'title' => __('Title:', 'trx_addons'),
								'value' => $instance['title'],
								'type' => 'text'));
		
		do_action('trx_addons_action_after_widget_title', $instance, 'trx_addons_widget_calendar', $this);

		$this->show_field(array('name' => 'weekdays',
								'title' => __('Week days:', 'trx_addons'),
								'value' => $instance['weekdays'],
								'options' => array(
													'short' => __('3 letters (Sun Mon Tue Wed Thu Fri Sat)', 'trx_addons'),
													'initial' => __('First letter (S M T W T F S)', 'trx_addons')
													),
								'dir' => 'vertical',
								'type' => 'radio'));
		
		do_action('trx_addons_action_after_widget_fields', $instance, 'trx_addons_widget_calendar', $this);
	}
}


// Add shortcodes
//----------------------------------------------------------------------------
require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_WIDGETS . 'calendar/calendar-sc.php';

// Add shortcodes to Elementor
if ( trx_addons_exists_elementor() && function_exists('trx_addons_elm_init') ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_WIDGETS . 'calendar/calendar-sc-elementor.php';
}

// Add shortcodes to Gutenberg
if ( trx_addons_exists_gutenberg() && function_exists( 'trx_addons_gutenberg_get_param_id' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_WIDGETS . 'calendar/calendar-sc-gutenberg.php';
}

// Add shortcodes to VC
if ( trx_addons_exists_vc() && function_exists( 'trx_addons_vc_add_id_param' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_WIDGETS . 'calendar/calendar-sc-vc.php';
}
