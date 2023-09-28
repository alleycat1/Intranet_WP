<?php
/**
 * Widget: Cars Search (Advanced search form) (Shortcodes)
 *
 * @package ThemeREX Addons
 * @since v1.6.25
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}



// trx_widget_cars_search
//-------------------------------------------------------------
/*
[trx_widget_cars_search id="unique_id" title="Widget title" orderby="price" order="desc"]
*/
if ( !function_exists( 'trx_addons_sc_widget_cars_search' ) ) {
	function trx_addons_sc_widget_cars_search($atts, $content=null){	
		$atts = trx_addons_sc_prepare_atts('trx_widget_cars_search', $atts, trx_addons_sc_common_atts('id', array(
			// Individual params
			"type" => 'horizontal',
			"title" => "",
			"orderby" => "date",
			"order" => "desc",
			))
		);
		extract($atts);
		$wtype = 'trx_addons_widget_cars_search';
		$output = '';
		global $wp_widget_factory;
		if ( is_object( $wp_widget_factory ) && isset( $wp_widget_factory->widgets, $wp_widget_factory->widgets[ $wtype ] ) ) {
			$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '')
							. ' class="widget_area sc_widget_cars_search' 
								. (trx_addons_exists_vc() ? ' vc_widget_cars_search wpb_content_element' : '') 
								. (!empty($class) ? ' ' . esc_attr($class) : '') 
								. '"'
							. ($css ? ' style="'.esc_attr($css).'"' : '')
						. '>';
			ob_start();
			the_widget( $wtype, $atts, trx_addons_prepare_widgets_args($id ? $id.'_widget' : 'widget_cars_search', 'widget_cars_search') );
			$output .= ob_get_contents();
			ob_end_clean();
			$output .= '</div>';
		}
		return apply_filters('trx_addons_sc_output', $output, 'trx_widget_cars_search', $atts, $content);
	}
}


// Add shortcode [trx_widget_cars_search]
if (!function_exists('trx_addons_sc_widget_cars_search_add_shortcode')) {
	function trx_addons_sc_widget_cars_search_add_shortcode() {
		add_shortcode("trx_widget_cars_search", "trx_addons_sc_widget_cars_search");
	}
	add_action('init', 'trx_addons_sc_widget_cars_search_add_shortcode', 20);
}
