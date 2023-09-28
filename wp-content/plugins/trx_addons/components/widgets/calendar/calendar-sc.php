<?php
/**
 * Widget: Calendar (Shortcodes)
 *
 * @package ThemeREX Addons
 * @since v1.0
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


// trx_widget_calendar
//-------------------------------------------------------------
/*
[trx_widget_calendar id="unique_id" title="Widget title" weekdays="short|initial"]
*/
if ( !function_exists( 'trx_addons_sc_widget_calendar' ) ) {
	function trx_addons_sc_widget_calendar($atts, $content=null){	
		$atts = trx_addons_sc_prepare_atts('trx_widget_calendar', $atts, trx_addons_sc_common_atts('id', array(
			// Individual params
			"title" => "",
			"weekdays" => "short",
			))
		);
		if ($atts['weekdays']=='') $atts['weekdays'] = 'short';
		extract($atts);
		$type = 'trx_addons_widget_calendar';
		$output = '';
		global $wp_widget_factory;
		if ( is_object( $wp_widget_factory ) && isset( $wp_widget_factory->widgets, $wp_widget_factory->widgets[ $type ] ) ) {
			$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '')
							. ' class="widget_area sc_widget_calendar' 
								. (trx_addons_exists_vc() ? ' vc_widget_calendar wpb_content_element' : '') 
								. (!empty($class) ? ' ' . esc_attr($class) : '') 
								. '"'
							. ($css ? ' style="'.esc_attr($css).'"' : '')
						. '>';
			ob_start();
			the_widget( $type, $atts, trx_addons_prepare_widgets_args($id ? $id.'_widget' : 'widget_calendar', 'widget_calendar') );
			$output .= ob_get_contents();
			ob_end_clean();
			$output .= '</div>';
		}
		return apply_filters('trx_addons_sc_output', $output, 'trx_widget_calendar', $atts, $content);
	}
}


// Add shortcode [trx_widget_calendar]
if (!function_exists('trx_addons_sc_widget_calendar_add_shortcode')) {
	function trx_addons_sc_widget_calendar_add_shortcode() {
		add_shortcode("trx_widget_calendar", "trx_addons_sc_widget_calendar");
	}
	add_action('init', 'trx_addons_sc_widget_calendar_add_shortcode', 20);
}
