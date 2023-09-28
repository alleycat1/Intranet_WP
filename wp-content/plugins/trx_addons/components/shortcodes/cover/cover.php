<?php
/**
 * Shortcode: Cover link
 *
 * @package ThemeREX Addons
 * @since v1.86.0
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


// Load required styles and scripts for the frontend
if ( !function_exists( 'trx_addons_sc_cover_load_scripts_front' ) ) {
	add_action( "wp_enqueue_scripts", 'trx_addons_sc_cover_load_scripts_front', TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY );
	add_action( 'trx_addons_action_pagebuilder_preview_scripts', 'trx_addons_sc_cover_load_scripts_front', 10, 1 );
	function trx_addons_sc_cover_load_scripts_front( $force = false ) {
		trx_addons_enqueue_optimized( 'sc_cover', $force, array(
			'css'  => array(
				'trx_addons-sc_cover' => array( 'src' => TRX_ADDONS_PLUGIN_SHORTCODES . 'cover/cover.css' ),
			),
			'check' => array(
				array( 'type' => 'sc',  'sc' => 'trx_sc_cover' ),
				array( 'type' => 'gb',  'sc' => 'wp:trx-addons/cover' ),
				array( 'type' => 'elm', 'sc' => '"widgetType":"trx_sc_cover"' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[trx_sc_cover' ),
			)
		) );
	}
}
	
// Merge shortcode's specific styles into single stylesheet
if ( !function_exists( 'trx_addons_sc_cover_merge_styles' ) ) {
	add_filter("trx_addons_filter_merge_styles", 'trx_addons_sc_cover_merge_styles');
	function trx_addons_sc_cover_merge_styles($list) {
		$list[ TRX_ADDONS_PLUGIN_SHORTCODES . 'cover/cover.css' ] = false;
		return $list;
	}
}

// Load styles and scripts if present in the cache of the menu
if ( !function_exists( 'trx_addons_sc_cover_check_in_html_output' ) ) {
	add_filter( 'trx_addons_filter_get_menu_cache_html', 'trx_addons_sc_cover_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_show_layout_from_cache', 'trx_addons_sc_cover_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_check_page_content', 'trx_addons_sc_cover_check_in_html_output', 10, 1 );
	function trx_addons_sc_cover_check_in_html_output( $content = '' ) {
		$args = array(
			'check' => array(
				'class=[\'"][^\'"]*sc_cover'
			)
		);
		if ( trx_addons_check_in_html_output( 'sc_cover', $content, $args ) ) {
			trx_addons_sc_cover_load_scripts_front( true );
		}
		return $content;
	}
}



// trx_sc_cover
//-------------------------------------------------------------
/*
[trx_sc_cover id="unique_id" type="cover" title="Block title" subtitle="" link="#" icon="icon-cog" image="path_to_image"]
*/
if (!function_exists('trx_addons_sc_cover')) {	
	function trx_addons_sc_cover($atts, $content=null){
		$atts = trx_addons_sc_prepare_atts('trx_sc_cover', $atts, trx_addons_sc_common_atts('id', array(
			// Individual params
			"type" => "default",
			"place" => "row",	// "row" - cover the parent row
								// "column" - cover the parent column
								// "pN" - cover the N-level parent tag (where N is equals to 1, 2 or 3)
			"url" => ""
			))
		);
		// Load shortcode-specific  scripts and styles
		trx_addons_sc_cover_load_scripts_front( true );
		// Load template
		$output = '';
		if ( ! empty($atts['url']) ) {
			ob_start();
			trx_addons_get_template_part( array(
											TRX_ADDONS_PLUGIN_SHORTCODES . 'cover/tpl.'.trx_addons_esc($atts['type']).'.php',
											TRX_ADDONS_PLUGIN_SHORTCODES . 'cover/tpl.default.php'
											),
											'trx_addons_args_sc_cover', 
											$atts
										);
			$output = ob_get_contents();
			ob_end_clean();
		}		
		return apply_filters('trx_addons_sc_output', $output, 'trx_sc_cover', $atts, $content);
	}
}



// Add shortcode [trx_sc_cover]
if (!function_exists('trx_addons_sc_cover_add_shortcode')) {
	function trx_addons_sc_cover_add_shortcode() {
		add_shortcode("trx_sc_cover", "trx_addons_sc_cover");
	}
	add_action('init', 'trx_addons_sc_cover_add_shortcode', 20);
}


// Add shortcodes
//----------------------------------------------------------------------------

// Add shortcodes to Elementor
if ( trx_addons_exists_elementor() && function_exists('trx_addons_elm_init') ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_SHORTCODES . 'cover/cover-sc-elementor.php';
}

// Add shortcodes to Gutenberg
if ( trx_addons_exists_gutenberg() && function_exists( 'trx_addons_gutenberg_get_param_id' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_SHORTCODES . 'cover/cover-sc-gutenberg.php';
}
