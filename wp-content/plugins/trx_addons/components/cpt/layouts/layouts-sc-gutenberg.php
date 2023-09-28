<?php
/**
 * ThemeREX Addons Layouts: Gutenberg utilities
 *
 * @package ThemeREX Addons
 * @since v1.6.51
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


// Add shortcode's specific lists to the JS storage
if ( ! function_exists( 'trx_addons_cpt_layouts_gutenberg_sc_params' ) ) {
	add_filter( 'trx_addons_filter_gutenberg_sc_params', 'trx_addons_cpt_layouts_gutenberg_sc_params' );
	function trx_addons_cpt_layouts_gutenberg_sc_params( $vars = array() ) {

		// If editor is active now
		$is_edit_mode = trx_addons_is_post_edit();

		// Return list of allowed layouts (moved to api/gutenberg/gutenberg.php)
		//$vars['sc_layouts'] = !$is_edit_mode ? array() : apply_filters( 'trx_addons_filter_gutenberg_sc_layouts', array() );

		// Prepare list of layouts
		$vars['list_layouts'] = !$is_edit_mode ? array() : trx_addons_get_list_layouts();

		return $vars;
	}
}


// Generate content to show layout
//------------------------------------------------------------------------
if ( !function_exists( 'trx_addons_cpt_layouts_gutenberg_layout_content' ) ) {
	add_filter( 'trx_addons_filter_sc_layout_content', 'trx_addons_cpt_layouts_gutenberg_layout_content', 11, 2 );
	function trx_addons_cpt_layouts_gutenberg_layout_content($content, $post_id = 0) {
		// Check if this post built with Gutenberg
		if ( function_exists('trx_addons_gutenberg_is_content_built') && trx_addons_gutenberg_is_content_built($content) ) {
			trx_addons_sc_stack_push('show_layout_gutenberg');
			$content = apply_filters( 'trx_addons_filter_sc_layout_content_from_builder', do_shortcode( do_blocks( $content ) ), $post_id, 'gutenberg' );
			trx_addons_sc_stack_pop();
		}
		return $content;
	}
}
