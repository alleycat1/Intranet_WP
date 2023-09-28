<?php
/**
 * Widget: Flickr (Shortcodes)
 *
 * @package ThemeREX Addons
 * @since v1.1
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}



// trx_widget_flickr
//-------------------------------------------------------------
/*
[trx_widget_flickr id="unique_id" title="Widget title" flickr_count="6" flickr_username="Flickr@23"]
*/
if ( !function_exists( 'trx_addons_sc_widget_flickr' ) ) {
	function trx_addons_sc_widget_flickr($atts, $content=null){	
		$atts = trx_addons_sc_prepare_atts('trx_widget_flickr', $atts, trx_addons_sc_common_atts('id', array(
			// Individual params
			"title"			=> "",
			'flickr_count'	=> 8,
			'flickr_columns' => 4,
			'flickr_columns_gap' => 0,
			'flickr_username' => '',
			'flickr_api_key' => '',
			))
		);
		extract($atts);
		$type = 'trx_addons_widget_flickr';
		$output = '';
		if ( (int) $atts['flickr_count'] > 0 && !empty($atts['flickr_username']) ) {
			global $wp_widget_factory;
			if ( is_object( $wp_widget_factory ) && isset( $wp_widget_factory->widgets, $wp_widget_factory->widgets[ $type ] ) ) {
				$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '')
								. ' class="widget_area sc_widget_flickr' 
									. (trx_addons_exists_vc() ? ' vc_widget_flickr wpb_content_element' : '') 
									. (!empty($class) ? ' ' . esc_attr($class) : '') 
								. '"'
							. ($css ? ' style="'.esc_attr($css).'"' : '')
						. '>';
				ob_start();
				the_widget( $type, $atts, trx_addons_prepare_widgets_args($id ? $id.'_widget' : 'widget_flickr', 'widget_flickr') );
				$output .= ob_get_contents();
				ob_end_clean();
				$output .= '</div>';
			}
		}
		return apply_filters('trx_addons_sc_output', $output, 'trx_widget_flickr', $atts, $content);
	}
}


// Add shortcode [trx_widget_flickr]
if (!function_exists('trx_addons_widget_flickr_reg_shortcodes')) {
	function trx_addons_widget_flickr_reg_shortcodes() {
		add_shortcode("trx_widget_flickr", "trx_addons_sc_widget_flickr");
	}
	add_action('init', 'trx_addons_widget_flickr_reg_shortcodes', 20);
}
