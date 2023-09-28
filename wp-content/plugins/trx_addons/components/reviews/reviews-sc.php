<?php
/**
 * ThemeREX Addons Posts and Comments Reviews (Shortcodes)
 *
 * @package ThemeREX Addons
 * @since v1.6.47
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

	
	
// trx_sc_reviews
//-------------------------------------------------------------
/*
[trx_sc_reviews id="unique_id" type="default"]
*/
// Reviews are used on single posts and comments. Use parameter "post_id" if you want to show reviews in blog archive or shortcodes.
if ( !function_exists( 'trx_addons_sc_reviews' ) ) {
	function trx_addons_sc_reviews($atts, $content=null) {	
		$atts = trx_addons_sc_prepare_atts('trx_sc_reviews', $atts, trx_addons_sc_common_atts('id', array(
			// Individual params
			"type" => "short",
			"align" => "right"
			))
		);
		$output = '';
		$reviews_enable = trx_addons_get_option('reviews_enable');
		$reviews_post_types = trx_addons_get_option('reviews_post_types');
		global $TRX_ADDONS_STORAGE;
		if ( !isset($TRX_ADDONS_STORAGE['reviews_showed']) ) {
			$TRX_ADDONS_STORAGE['reviews_showed'] = array();
		}
		$gutenberg_preview = function_exists('trx_addons_gutenberg_is_preview') && trx_addons_gutenberg_is_preview() && !trx_addons_sc_stack_check('trx_sc_blogger');
		if ($reviews_enable
			&& empty($TRX_ADDONS_STORAGE['reviews_showed'][$atts['type']])
			&& ( ( trx_addons_is_singular()
					&& !empty($reviews_post_types)
					&& !empty($reviews_post_types[get_post_type()])
					)
					|| $gutenberg_preview
				)
		) {
			ob_start();
			trx_addons_get_template_part( array(
										TRX_ADDONS_PLUGIN_REVIEWS . 'tpl.'.trx_addons_esc($atts['type']).'.php',
										TRX_ADDONS_PLUGIN_REVIEWS . 'tpl.default.php'
										),
										'trx_addons_args_sc_reviews',
										$atts
										);
			$output = ob_get_contents();
			ob_end_clean();
			$TRX_ADDONS_STORAGE['reviews_showed'][$atts['type']] = !doing_action('the_content');	//true;
		}
		return apply_filters('trx_addons_sc_output', $output, 'trx_sc_reviews', $atts, $content);
	}
}


// Add shortcode [trx_sc_reviews]
if (!function_exists('trx_addons_sc_reviews_add_shortcode')) {
	function trx_addons_sc_reviews_add_shortcode() {

		if (!trx_addons_reviews_enable()) return;

		add_shortcode("trx_sc_reviews", "trx_addons_sc_reviews");
	}
	add_action('init', 'trx_addons_sc_reviews_add_shortcode', 20);
}
