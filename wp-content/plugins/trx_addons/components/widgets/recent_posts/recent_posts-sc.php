<?php
/**
 * Widget: Recent posts (Shortcodes)
 *
 * @package ThemeREX Addons
 * @since v1.0
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}



// trx_widget_recent_posts
//-------------------------------------------------------------
/*
[trx_widget_recent_posts id="unique_id" title="Widget title" number="4" show_date="0|1" show_image="0|1" show_author="0|1" show_counters="0|1" show_categories="0|1"]
*/
if ( !function_exists( 'trx_addons_sc_widget_recent_posts' ) ) {
	function trx_addons_sc_widget_recent_posts($atts, $content=null){	
		$atts = trx_addons_sc_prepare_atts('trx_widget_recent_posts', $atts, trx_addons_sc_common_atts('id', array(
			// Individual params
			"title" => "",
			"number" => 4,
			"show_date" => 1,
			"show_image" => 1,
			"show_author" => 1,
			"show_counters" => 1,
			"show_categories" => 1,
			))
		);
		if ($atts['show_date']=='') $atts['show_date'] = 0;
		if ($atts['show_image']=='') $atts['show_image'] = 0;
		if ($atts['show_author']=='') $atts['show_author'] = 0;
		if ($atts['show_counters']=='') $atts['show_counters'] = 0;
		if ($atts['show_categories']=='') $atts['show_categories'] = 0;
		extract($atts);
		$type = 'trx_addons_widget_recent_posts';
		$output = '';
		global $wp_widget_factory;
		if ( is_object( $wp_widget_factory ) && isset( $wp_widget_factory->widgets, $wp_widget_factory->widgets[ $type ] ) ) {
			$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '')
							. ' class="widget_area sc_widget_recent_posts' 
								. (trx_addons_exists_vc() ? ' vc_widget_recent_posts wpb_content_element' : '') 
								. (!empty($class) ? ' ' . esc_attr($class) : '') 
								. '"'
							. ($css ? ' style="'.esc_attr($css).'"' : '')
						. '>';
			ob_start();
			the_widget( $type, $atts, trx_addons_prepare_widgets_args($id ? $id.'_widget' : 'widget_recent_posts', 'widget_recent_posts') );
			$output .= ob_get_contents();
			ob_end_clean();
			$output .= '</div>';
		}
		return apply_filters('trx_addons_sc_output', $output, 'trx_widget_recent_posts', $atts, $content);
	}
}


// Add shortcode [trx_widget_recent_posts]
if (!function_exists('trx_addons_sc_widget_recent_posts_add_shortcode')) {
	function trx_addons_sc_widget_recent_posts_add_shortcode() {
		add_shortcode("trx_widget_recent_posts", "trx_addons_sc_widget_recent_posts");
	}
	add_action('init', 'trx_addons_sc_widget_recent_posts_add_shortcode', 20);
}
