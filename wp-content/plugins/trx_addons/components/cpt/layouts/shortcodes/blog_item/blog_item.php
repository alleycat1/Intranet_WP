<?php
/**
 * Shortcode: Blog item parts
 *
 * @package ThemeREX Addons
 * @since v1.6.50
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}
	
// Load required styles and scripts for the frontend
if ( !function_exists( 'trx_addons_cpt_layouts_blog_item_load_scripts_front' ) ) {
	add_action("wp_enqueue_scripts", 'trx_addons_cpt_layouts_blog_item_load_scripts_front', TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY);
	function trx_addons_cpt_layouts_blog_item_load_scripts_front() {
		if (trx_addons_exists_page_builder() && trx_addons_is_on(trx_addons_get_option('debug_mode'))) {
			wp_enqueue_style( 'trx_addons-sc_layouts-blog_item', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'blog_item/blog_item.css'), array(), null );
		}
	}
}
	
// Merge shortcode specific styles into single stylesheet
if ( !function_exists( 'trx_addons_sc_layouts_blog_item_merge_styles' ) ) {
	add_filter("trx_addons_filter_merge_styles", 'trx_addons_sc_layouts_blog_item_merge_styles');
	add_filter("trx_addons_filter_merge_styles_layouts", 'trx_addons_sc_layouts_blog_item_merge_styles');
	function trx_addons_sc_layouts_blog_item_merge_styles($list) {
		$list[ TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'blog_item/blog_item.css' ] = true;
		return $list;
	}
}


// trx_sc_layouts_blog_item
//-------------------------------------------------------------
/*
[trx_sc_layouts_blog_item id="unique_id"]
*/
if ( !function_exists( 'trx_addons_sc_layouts_blog_item' ) ) {
	function trx_addons_sc_layouts_blog_item($atts, $content=null){
		$atts = trx_addons_sc_prepare_atts('trx_sc_layouts_blog_item', $atts, trx_addons_sc_common_atts('id', array(
				// Individual params
				"type" => "",
				"thumb_bg" => 0,
				"thumb_ratio" => "16:9",
				"thumb_mask" => '#000',
				"thumb_mask_opacity" => 0.3,
				"thumb_hover_mask" => '#000',
				"thumb_hover_opacity" => 0.1,
				"thumb_size" => "full",
				"title_tag" => "h4",
				"meta_parts" => "",
				"custom_meta_key" => "",
				"button_text" => __("Read more", 'trx_addons'),
				"button_link" => "post",
				"button_type" => "default",
				"seo" => "",
				"position" => "static",
				"hide_overflow" => 0,
				"animation_in" => 'none',
				"animation_in_delay" => 0,
				"animation_out" => 'none',
				"animation_out_delay" => 0,
				"text_color" => '',
				"text_hover" => '',
				"font_zoom" => 1,
				"post_type" => array(),
			))
		);
		
		$output = '';

		$is_preview = ( trx_addons_is_preview() || get_post_type() == '' ) && ! trx_addons_sc_stack_check( 'trx_sc_blogger' );

		if (!is_array($atts['post_type'])) {
			$atts['post_type'] = !empty($atts['post_type']) ? explode(',', $atts['post_type']) : array();
		}
		if ($is_preview) {
			$atts['post_type'][] = TRX_ADDONS_CPT_LAYOUTS_PT;
		}

		if ( empty($atts['post_type']) || get_post_type()=='' || in_array( get_post_type(), $atts['post_type'] ) ) {
			ob_start();
			trx_addons_get_template_part( array(
												TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'blog_item/tpl.'.trx_addons_esc($atts['type']).'.php',
												TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'blog_item/tpl.default.php'
											),
											'trx_addons_args_sc_layouts_blog_item',
											$atts
										);
			$output = ob_get_contents();
			ob_end_clean();
		}
		return apply_filters('trx_addons_sc_output', $output, 'trx_sc_layouts_blog_item', $atts, $content);
	}
}


// Add shortcode [trx_sc_layouts_cart]
if (!function_exists('trx_addons_sc_layouts_cart_add_shortcode')) {
	function trx_addons_sc_layouts_cart_add_shortcode() {
		
		if (!trx_addons_cpt_layouts_sc_required()) return;
		
		add_shortcode("trx_sc_layouts_cart", "trx_addons_sc_layouts_cart");

	}
	add_action('init', 'trx_addons_sc_layouts_cart_add_shortcode', 15);
}



// Add shortcodes
//----------------------------------------------------------------------------

// Add shortcodes to Elementor
if ( trx_addons_exists_elementor() && function_exists('trx_addons_elm_init') ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'blog_item/blog_item-sc-elementor.php';
}

// Add shortcodes to Gutenberg
if ( trx_addons_exists_gutenberg() && function_exists( 'trx_addons_gutenberg_get_param_id' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'blog_item/blog_item-sc-gutenberg.php';
}
