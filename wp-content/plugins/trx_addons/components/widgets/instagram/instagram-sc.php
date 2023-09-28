<?php
/**
 * Widget: Instagram (Shortcodes)
 *
 * @package ThemeREX Addons
 * @since v1.6.47
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


// trx_widget_instagram
//-------------------------------------------------------------
/*
[trx_widget_instagram id="unique_id" title="Widget title" count="6" columns="3" hashtag="my_hash"]
*/
if ( !function_exists( 'trx_addons_sc_widget_instagram' ) ) {
	function trx_addons_sc_widget_instagram($atts, $content=null){	
		$atts = trx_addons_sc_prepare_atts('trx_widget_instagram', $atts, trx_addons_sc_common_atts('id', array(
			// Individual params
			"title" => "",
			"type" => "default",
			'count'	=> 8,
			'columns' => 4,
			"columns_tablet" => "",
			"columns_mobile" => "",
			'columns_gap' => 0,
			'ratio' => 'none',
			'demo' => 0,
			'demo_files' => '',
			'demo_thumb_size' => apply_filters( 'trx_addons_filter_thumb_size',
													trx_addons_get_thumb_size( 'avatar' ),
													'trx_addons_widget_instagram',
													$atts
												),
			'hashtag' => '',
			'links' => 'instagram',
			'follow' => 0,
			'follow_link' => '',
			))
		);
		if ( ! is_array($atts['demo_files']) && function_exists('vc_param_group_parse_atts')) {
			$atts['demo_files'] = (array) vc_param_group_parse_atts( $atts['demo_files'] );
		}
		extract($atts);
		$type = 'trx_addons_widget_instagram';
		$output = '';
		if ( (int) $atts['count'] > 0 ) {
			global $wp_widget_factory;
			if ( is_object( $wp_widget_factory ) && isset( $wp_widget_factory->widgets, $wp_widget_factory->widgets[ $type ] ) ) {
				$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '')
								. ' class="widget_area sc_widget_instagram' 
									. (trx_addons_exists_vc() ? ' vc_widget_instagram wpb_content_element' : '') 
									. (!empty($class) ? ' ' . esc_attr($class) : '') 
								. '"'
							. ($css ? ' style="'.esc_attr($css).'"' : '')
						. '>';
				ob_start();
				the_widget( $type, $atts, trx_addons_prepare_widgets_args($id ? $id.'_widget' : 'widget_instagram', 'widget_instagram') );
				$output .= ob_get_contents();
				ob_end_clean();
				$output .= '</div>';
			}
		}
		return apply_filters('trx_addons_sc_output', $output, 'trx_widget_instagram', $atts, $content);
	}
}


// Add shortcode [trx_widget_instagram]
if (!function_exists('trx_addons_widget_instagram_reg_shortcodes')) {
	function trx_addons_widget_instagram_reg_shortcodes() {
		add_shortcode("trx_widget_instagram", "trx_addons_sc_widget_instagram");
	}
	add_action('init', 'trx_addons_widget_instagram_reg_shortcodes', 20);
}
