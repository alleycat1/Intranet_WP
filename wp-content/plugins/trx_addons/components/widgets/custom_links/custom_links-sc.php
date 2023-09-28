<?php
/**
 * Widget: Custom links (Shortcodes)
 *
 * @package ThemeREX Addons
 * @since v1.0.46
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


// trx_widget_custom_links
//-------------------------------------------------------------
/*
[trx_widget_custom_links id="unique_id" title="Widget title" fullwidth="0|1" image="image_url" link="Image_link_url" code="html & js code"]
*/
if ( !function_exists( 'trx_addons_sc_widget_custom_links' ) ) {
	function trx_addons_sc_widget_custom_links($atts, $content=null){	
		$atts = trx_addons_sc_prepare_atts('trx_widget_custom_links', $atts, trx_addons_sc_common_atts('id', array(
			// Individual params
			"title" => "",
			"icons_animation" => "0",
			"links" => "",
			))
		);
		if (function_exists('vc_param_group_parse_atts') && !is_array($atts['links']))
			$atts['links'] = (array) vc_param_group_parse_atts( $atts['links'] );
		if (is_array($atts['links']) && count($atts['links']) > 0) {
			foreach ($atts['links'] as $k=>$v) {
				if (!empty($v['description']))
					$atts['links'][$k]['description'] = preg_replace( '/\\[(.*)\\]/', '<b>$1</b>', function_exists('vc_value_from_safe') ? vc_value_from_safe( $v['description'] ) : $v['description'] );
			}
		}
		extract($atts);
		// Clear generated id to prevent duplicate it in the menu
		if ( strpos($id, 'trx_widget_custom_links_') !== false ) {
			$id = '';
		}
		$type = 'trx_addons_widget_custom_links';
		$output = '';
		global $wp_widget_factory;
		if ( is_object( $wp_widget_factory ) && isset( $wp_widget_factory->widgets, $wp_widget_factory->widgets[ $type ] ) ) {
			$atts['from_shortcode'] = true;
			$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '')
							. ' class="widget_area sc_widget_custom_links' 
								. (trx_addons_exists_vc() ? ' vc_widget_custom_links wpb_content_element' : '') 
								. (!empty($class) ? ' ' . esc_attr($class) : '') 
								. '"'
							. ($css ? ' style="'.esc_attr($css).'"' : '')
						. '>';
			ob_start();
			the_widget( $type, $atts, trx_addons_prepare_widgets_args($id ? $id.'_widget' : 'widget_custom_links', 'widget_custom_links') );
			$output .= ob_get_contents();
			ob_end_clean();
			$output .= '</div>';
		}
		return apply_filters('trx_addons_sc_output', $output, 'trx_widget_custom_links', $atts, $content);
	}
}


// Add shortcode [trx_widget_custom_links]
if (!function_exists('trx_addons_sc_widget_custom_links_add_shortcode')) {
	function trx_addons_sc_widget_custom_links_add_shortcode() {
		add_shortcode("trx_widget_custom_links", "trx_addons_sc_widget_custom_links");
	}
	add_action('init', 'trx_addons_sc_widget_custom_links_add_shortcode', 20);
}
