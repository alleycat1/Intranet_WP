<?php
/**
 * Widget: Video list for Youtube, Vimeo, etc. embeded video (Shortcodes)
 *
 * @package ThemeREX Addons
 * @since v1.78.0
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}



// trx_widget_video_list
//-------------------------------------------------------------
if ( !function_exists( 'trx_addons_sc_widget_video_list' ) ) {
	function trx_addons_sc_widget_video_list($atts, $content=null){	
		$atts = trx_addons_sc_prepare_atts('trx_widget_video_list', $atts, trx_addons_sc_common_atts('id,query', array(
			// Individual params
			'title' => '',
			'autoplay' => 0,				// Autoplay first video on page load
			'post_type' => 'post',
			'taxonomy' => 'category',
			'category' => '0',
			'controller_style' => 'default',// Style of controller
			'controller_pos' => 'right',	// left | right | bottom - position of the slider controller
			'controller_height' => '', 		// Height of the the controller
			'controller_autoplay' => 1,		// Autoplay video on click on the the controller item
			'controller_link' => 1,			// Switch video or Go to the post on click on the the controller item
			'videos' => '',
			))
		);
		if ( ! is_array($atts['videos']) && function_exists('vc_param_group_parse_atts')) {
			$atts['videos'] = (array) vc_param_group_parse_atts( $atts['videos'] );
		}

		$type = 'trx_addons_widget_video_list';
		$output = '';
		global $wp_widget_factory;
		if ( is_object( $wp_widget_factory ) && isset( $wp_widget_factory->widgets, $wp_widget_factory->widgets[ $type ] ) ) {
			$output = '<div' . (!empty($atts['id']) ? ' id="'.esc_attr($atts['id']).'"' : '')
							. ' class="widget_area sc_widget_video_list' 
								. (trx_addons_exists_vc() ? ' vc_widget_video_list wpb_content_element' : '') 
								. (!empty($atts['class']) ? ' ' . esc_attr($atts['class']) : '') 
								. '"'
							. ($atts['css'] ? ' style="'.esc_attr($atts['css']).'"' : '')
						. '>';
			ob_start();
			the_widget( $type, $atts, trx_addons_prepare_widgets_args(!empty($atts['id']) ? $atts['id'] . '_widget' : 'widget_video_list', 'widget_video_list') );
			$output .= ob_get_contents();
			ob_end_clean();
			$output .= '</div>';
		}
		return apply_filters('trx_addons_sc_output', $output, 'trx_widget_video_list', $atts, $content);
	}
}


// Add shortcode [trx_widget_video]
if (!function_exists('trx_addons_sc_widget_video_list_add_shortcode')) {
	function trx_addons_sc_widget_video_list_add_shortcode() {
		add_shortcode("trx_widget_video_list", "trx_addons_sc_widget_video_list");
	}
	add_action('init', 'trx_addons_sc_widget_video_list_add_shortcode', 20);
}
