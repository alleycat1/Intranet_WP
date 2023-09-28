<?php
/**
 * Widget: Banner (Shortcodes)
 *
 * @package ThemeREX Addons
 * @since v1.0
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}



// trx_widget_banner
//-------------------------------------------------------------
/*
[trx_widget_banner id="unique_id" title="Widget title" fullwidth="0|1" image="image_url" link="Image_link_url" code="html & js code"]
*/
if ( !function_exists( 'trx_addons_sc_widget_banner' ) ) {
	function trx_addons_sc_widget_banner($atts, $content=null){	
		$atts = trx_addons_sc_prepare_atts('trx_widget_banner', $atts, trx_addons_sc_common_atts('id', array(
			// Individual params
			"title" => "",
			"image" => "",
			"link" => "",
			"code" => "",
			"fullwidth" => "off",
			"show" => "permanent"
			))
		);
		extract($atts);
		$type = 'trx_addons_widget_banner';
		$output = '';
		global $wp_widget_factory;
		if ( is_object( $wp_widget_factory ) && isset( $wp_widget_factory->widgets, $wp_widget_factory->widgets[ $type ] ) ) {
			$atts['from_shortcode'] = true;
			$atts['banner_show'] = $show;
			$atts['banner_image'] = $image;
			$atts['banner_link'] = $link;
			$atts['banner_code'] = !empty($code) 
										? ( function_exists('vc_value_from_safe') 
												? trim( vc_value_from_safe( $code ) ) 
												: trim( $code )
											)
										: '';
			$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '')
							. ' class="widget_area sc_widget_banner' 
								. (trx_addons_exists_vc() ? ' vc_widget_banner wpb_content_element' : '') 
								. (!empty($class) ? ' ' . esc_attr($class) : '') 
								. '"'
							. ($css ? ' style="'.esc_attr($css).'"' : '')
						. '>';
			ob_start();
			the_widget( $type, $atts, trx_addons_prepare_widgets_args($id ? $id.'_widget' : 'widget_banner', 'widget_banner') );
			$output .= ob_get_contents();
			ob_end_clean();
			$output .= '</div>';
		}
		return apply_filters('trx_addons_sc_output', $output, 'trx_widget_banner', $atts, $content);
	}
}


// Add shortcode [trx_widget_banner]
if (!function_exists('trx_addons_sc_widget_banner_add_shortcode')) {
	function trx_addons_sc_widget_banner_add_shortcode() {
		add_shortcode("trx_widget_banner", "trx_addons_sc_widget_banner");
	}
	add_action('init', 'trx_addons_sc_widget_banner_add_shortcode', 20);
}
