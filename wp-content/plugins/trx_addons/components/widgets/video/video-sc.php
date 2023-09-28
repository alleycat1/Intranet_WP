<?php
/**
 * Widget: Video player for Youtube, Vimeo, etc. embeded video (Shortcodes)
 *
 * @package ThemeREX Addons
 * @since v1.1
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}



// trx_widget_video
//-------------------------------------------------------------
/*
[trx_widget_video id="unique_id" title="Widget title" embed="HTML code" cover="image url"]
*/
if ( ! function_exists( 'trx_addons_sc_widget_video' ) ) {
	function trx_addons_sc_widget_video( $atts, $content = null ) {	
		$atts = trx_addons_sc_prepare_atts( 'trx_widget_video', $atts, trx_addons_sc_common_atts( 'id', array(
			// Individual params
			'title' => '',
			'subtitle' => '',
			'ratio' => '16:9',
			'type' => 'default',
			'cover' => '',
			'link' => '',
			'embed' => '',
			'media_from_post' => 0,
			'popup' => 0,
			'autoplay' => 0,
			'mute' => 0,
		) ) );
		if ( ! empty( $atts['embed'] ) && function_exists('vc_value_from_safe') ) {
			$atts['embed'] = trim( vc_value_from_safe( $atts['embed'] ) );
		}
		if ( ! empty( $atts['autoplay'] ) || empty( $atts['cover'] ) ) {
			$atts['popup'] = 0;
		}
		extract( $atts );
		$widget_type = 'trx_addons_widget_video';
		$output = '';
		global $wp_widget_factory;
		if ( is_object( $wp_widget_factory ) && isset( $wp_widget_factory->widgets, $wp_widget_factory->widgets[ $widget_type ] ) ) {
			$output = '<div' . ( $id ? ' id="'.esc_attr($id).'"' : '' )
							. ' class="widget_area sc_widget_video' 
								. ( trx_addons_exists_vc() ? ' vc_widget_video wpb_content_element' : '' )
								. ( ! empty( $class ) ? ' ' . esc_attr( $class ) : '' )
								. '"'
							. ( ! empty( $css ) ? ' style="' . esc_attr( $css ) . '"' : '' )
						. '>';
			ob_start();
			the_widget( $widget_type, $atts, trx_addons_prepare_widgets_args( $id ? $id . '_widget' : 'widget_video', 'widget_video' ) );
			$output .= ob_get_contents();
			ob_end_clean();
			$output .= '</div>';
		}
		return apply_filters( 'trx_addons_sc_output', $output, 'trx_widget_video', $atts, $content );
	}
}


// Add shortcode [trx_widget_video]
if ( ! function_exists( 'trx_addons_sc_widget_video_add_shortcode' ) ) {
	function trx_addons_sc_widget_video_add_shortcode() {
		add_shortcode( 'trx_widget_video', 'trx_addons_sc_widget_video' );
	}
	add_action( 'init', 'trx_addons_sc_widget_video_add_shortcode', 20 );
}
