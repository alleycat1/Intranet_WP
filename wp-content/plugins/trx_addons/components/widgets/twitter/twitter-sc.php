<?php
/**
 * Widget: Twitter (Shortcodes)
 *
 * @package ThemeREX Addons
 * @since v1.0
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}



// trx_widget_twitter
//-------------------------------------------------------------
/*
[trx_widget_twitter id="unique_id" title="Widget title" bg_image="image_url" number="3" follow="0|1"]
*/
if ( !function_exists( 'trx_addons_sc_widget_twitter' ) ) {
	function trx_addons_sc_widget_twitter($atts, $content=null){	
		$atts = trx_addons_sc_prepare_atts('trx_widget_twitter', $atts, trx_addons_sc_common_atts('id,slider', array(
			// Individual params
			"type" => 'list',
			"title" => "",
			"username" => "",
			"bg_image" => "",
			'back_image' => '',			// Alter name for 'bg_image' in VC (it broke bg_image)
			"count" => 2,
			"columns" => 1,
			"columns_tablet" => '',
			"columns_mobile" => '',
			"follow" => 1,
			'embed_header' => 1,
			'embed_footer' => 1,
			'embed_borders' => 1,
			'embed_scrollbar' => 1,
			'embed_transparent' => 1,
			"consumer_key" => "",
			"consumer_secret" => "",
			"bearer" => "",
			"token_key" => "",
			"token_secret" => "",
			"twitter_api" => "token"
			))
		);
		if ($atts['follow']=='') $atts['follow'] = 0;
		if ($atts['embed_header']=='') $atts['embed_header'] = 0;
		if ($atts['embed_footer']=='') $atts['embed_footer'] = 0;
		if ($atts['embed_borders']=='') $atts['embed_borders'] = 0;
		if ($atts['embed_scrollbar']=='') $atts['embed_scrollbar'] = 0;
		if ($atts['embed_transparent']=='') $atts['embed_transparent'] = 0;
		$atts['slider'] = max(0, (int) $atts['slider']);
		if ($atts['slider'] > 0 && (int) $atts['slider_pagination'] > 0) $atts['slider_pagination'] = 'bottom';
		if (empty($atts['bg_image'])) $atts['bg_image'] = $atts['back_image'];

		extract($atts);

		$type = 'trx_addons_widget_twitter';
		$output = '';
		global $wp_widget_factory;
		if ( is_object( $wp_widget_factory ) && isset( $wp_widget_factory->widgets, $wp_widget_factory->widgets[ $type ] ) ) {
			$atts['twitter_username'] = $username;
			$atts['twitter_consumer_key'] = $consumer_key;
			$atts['twitter_consumer_secret'] = $consumer_secret;
			$atts['twitter_token_key'] = $token_key;
			$atts['twitter_token_secret'] = $token_secret;
			$atts['twitter_bearer'] = $bearer;
			$atts['twitter_count'] = max(1, (int) $count);
			$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '')
							. ' class="widget_area sc_widget_twitter' 
								. (trx_addons_exists_vc() ? ' vc_widget_twitter wpb_content_element' : '') 
								. (!empty($class) ? ' ' . esc_attr($class) : '') 
								. '"'
							. ($css ? ' style="'.esc_attr($css).'"' : '')
						. '>';
			ob_start();
			the_widget( $type, $atts, trx_addons_prepare_widgets_args($id ? $id.'_widget' : 'widget_twitter', 'widget_twitter') );
			$output .= ob_get_contents();
			ob_end_clean();
			$output .= '</div>';
		}
		return apply_filters('trx_addons_sc_output', $output, 'trx_widget_twitter', $atts, $content);
	}
}


// Add shortcode [trx_widget_twitter]
if (!function_exists('trx_addons_sc_widget_twitter_add_shortcode')) {
	function trx_addons_sc_widget_twitter_add_shortcode() {
		add_shortcode("trx_widget_twitter", "trx_addons_sc_widget_twitter");
	}
	add_action('init', 'trx_addons_sc_widget_twitter_add_shortcode', 20);
}
