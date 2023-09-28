<?php
/**
 * Widget: WooCommerce Search (Advanced search form) (Shortcodes)
 *
 * @package ThemeREX Addons
 * @since v1.6.38
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


/*
[trx_widget_woocommerce_search id="unique_id" title="Widget title" orderby="price" order="desc"]
*/
if ( ! function_exists( 'trx_addons_sc_widget_woocommerce_search' ) ) {
	/**
	 * Shortcode [trx_widget_woocommerce_search] to display WooCommerce search form
	 * 
	 * @trigger trx_addons_sc_output
	 * 
	 * @param array $atts  Shortcode attributes
	 * @param string $content  Shortcode content
	 * 
	 * @return string  Shortcode output
	 */
	function trx_addons_sc_widget_woocommerce_search( $atts, $content = null ) {	
		$atts = trx_addons_sc_prepare_atts( 'trx_widget_woocommerce_search', $atts, trx_addons_sc_common_atts( 'id', array(
			// Individual params
			"title" => "",
			"type" => "inline",
			"ajax" => 1,
			"apply" => 1,
			"force_checkboxes" => 0,
			"show_counters" => 1,
			"show_selected" => 1,
			"expanded" => 0,
			"autofilters" => 1,
			"fields" => "",
			"last_text" => "",
			"button_text" => "",
		) ) );
		if ( function_exists( 'vc_param_group_parse_atts' ) && ! is_array( $atts['fields'] ) ) {
			$atts['fields'] = (array)vc_param_group_parse_atts( $atts['fields'] );
		}
		$wtype = 'trx_addons_widget_woocommerce_search';
		$output = '';
		global $wp_widget_factory;
		if ( is_object( $wp_widget_factory ) && isset( $wp_widget_factory->widgets, $wp_widget_factory->widgets[ $wtype ] ) ) {
			$output = '<div' . ( !empty($atts['id']) ? ' id="'.esc_attr($atts['id']).'"' : '')
							. ' class="widget_area sc_widget_woocommerce_search' 
								. (trx_addons_exists_vc() ? ' vc_widget_woocommerce_search wpb_content_element' : '') 
								. (!empty($atts['class']) ? ' ' . esc_attr($atts['class']) : '') 
								. '"'
							. ( !empty($atts['css']) ? ' style="'.esc_attr($atts['css']).'"' : '')
						. '>';
			ob_start();
			the_widget( $wtype, $atts, trx_addons_prepare_widgets_args(!empty($atts['id']) ? $atts['id'].'_widget' : 'widget_woocommerce_search', 'widget_woocommerce_search') );
			$output .= ob_get_contents();
			ob_end_clean();
			$output .= '</div>';
		}
		return apply_filters('trx_addons_sc_output', $output, 'trx_widget_woocommerce_search', $atts, $content);
	}
}

if ( ! function_exists( 'trx_addons_sc_widget_woocommerce_search_add_shortcode' ) ) {
	add_action( 'init', 'trx_addons_sc_widget_woocommerce_search_add_shortcode', 20 );
	/**
	 * Add shortcode [trx_widget_woocommerce_search]
	 * 
	 * @hooked init, 20
	 */
	function trx_addons_sc_widget_woocommerce_search_add_shortcode() {
		if ( ! trx_addons_exists_woocommerce() ) {
			return;
		}
		add_shortcode( "trx_widget_woocommerce_search", "trx_addons_sc_widget_woocommerce_search" );
	}
}
