<?php
/**
 * Widget: Downloads Search (Advanced search form) (Shortcodes)
 *
 * @package ThemeREX Addons
 * @since v1.6.34
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


// trx_widget_edd_search
//-------------------------------------------------------------

if ( ! function_exists( 'trx_addons_sc_widget_edd_search' ) ) {
	/**
	 * Shortcode [trx_widget_edd_search] to display widget Downloads Search (Advanced search form).
	 * Call the widget "Downloads Search (Advanced search form)" to display shortcode layout.
	 * 
	 * @trigger trx_addons_sc_output
	 *
	 * @param array $atts      Shortcode attributes
	 * @param string $content  Shortcode content
	 * 
	 * @return string  Widget content
	 */
	function trx_addons_sc_widget_edd_search( $atts, $content = null ){	
		$atts = trx_addons_sc_prepare_atts( 'trx_widget_edd_search', $atts, trx_addons_sc_common_atts( 'id', array(
			// Individual params
			"title" => "",
			"orderby" => "date",
			"order" => "desc",
		) ) );
		extract( $atts );
		$type = 'trx_addons_widget_edd_search';
		$output = '';
		global $wp_widget_factory;
		if ( is_object( $wp_widget_factory ) && isset( $wp_widget_factory->widgets, $wp_widget_factory->widgets[ $type ] ) ) {
			$output = '<div' . ( $id ? ' id="' . esc_attr( $id ) . '"' : '' )
							. ' class="widget_area sc_widget_edd_search' 
								. ( trx_addons_exists_vc() ? ' vc_widget_edd_search wpb_content_element' : '' )
								. ( ! empty( $class ) ? ' ' . esc_attr( $class ) : '' )
								. '"'
							. ( $css ? ' style="' . esc_attr( $css ) . '"' : '' )
						. '>';
			ob_start();
			the_widget( $type, $atts, trx_addons_prepare_widgets_args( $id ? $id . '_widget' : 'widget_edd_search', 'widget_edd_search' ) );
			$output .= ob_get_contents();
			ob_end_clean();
			$output .= '</div>';
		}
		return apply_filters( 'trx_addons_sc_output', $output, 'trx_widget_edd_search', $atts, $content );
	}
}

if ( ! function_exists( 'trx_addons_sc_widget_edd_search_add_shortcode' ) ) {
	add_action( 'init', 'trx_addons_sc_widget_edd_search_add_shortcode', 20 );
	/**
	 * Add shortcode [trx_widget_edd_search]
	 * 
	 * @hook init, 20
	 */
	function trx_addons_sc_widget_edd_search_add_shortcode() {
		add_shortcode( "trx_widget_edd_search", "trx_addons_sc_widget_edd_search" );
	}
}
