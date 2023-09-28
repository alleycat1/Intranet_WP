<?php
/**
 * Shortcode: Accordion posts
 *
 * @package ThemeREX Addons
 * @since v1.2
 */

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }


// Load required styles and scripts for the frontend
if ( !function_exists( 'trx_addons_sc_accordionposts_load_scripts_front' ) ) {
	add_action( "wp_enqueue_scripts", 'trx_addons_sc_accordionposts_load_scripts_front', TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY );
	add_action( 'trx_addons_action_pagebuilder_preview_scripts', 'trx_addons_sc_accordionposts_load_scripts_front', 10, 1 );
	function trx_addons_sc_accordionposts_load_scripts_front( $force = false ) {
		trx_addons_enqueue_optimized( 'sc_accordionposts', $force, array(
			'css'  => array(
				'trx_addons-sc_accordionposts' => array( 'src' => TRX_ADDONS_PLUGIN_SHORTCODES . 'accordionposts/accordionposts.css' ),
			),
			'js' => array(
				'trx_addons-sc_accordionposts' => array( 'src' => TRX_ADDONS_PLUGIN_SHORTCODES . 'accordionposts/accordionposts.js', 'deps' => 'jquery' ),
			),
			'check' => array(
				array( 'type' => 'sc',  'sc' => 'trx_sc_accordionposts' ),
				array( 'type' => 'gb',  'sc' => 'wp:trx-addons/accordionposts' ),
				array( 'type' => 'elm', 'sc' => '"widgetType":"trx_sc_accordionposts"' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[trx_sc_accordionposts' ),
			)
		) );
	}
}

// Enqueue responsive styles for frontend
if ( ! function_exists( 'trx_addons_sc_accordionposts_load_scripts_front_responsive' ) ) {
	add_action( 'wp_enqueue_scripts', 'trx_addons_sc_accordionposts_load_scripts_front_responsive', TRX_ADDONS_ENQUEUE_RESPONSIVE_PRIORITY );
	add_action( 'trx_addons_action_load_scripts_front_sc_accordionposts', 'trx_addons_sc_accordionposts_load_scripts_front_responsive', 10, 1 );
	function trx_addons_sc_accordionposts_load_scripts_front_responsive( $force = false ) {
		trx_addons_enqueue_optimized_responsive( 'sc_accordionposts', $force, array(
			'css'  => array(
				'trx_addons-sc_accordionposts-responsive' => array(
					'src' => TRX_ADDONS_PLUGIN_SHORTCODES . 'accordionposts/accordionposts.responsive.css',
					'media' => 'xs'
				),
			),
		) );
	}
}

// Merge shortcode's specific styles to the single stylesheet
if ( !function_exists( 'trx_addons_sc_accordionposts_merge_styles' ) ) {
	add_filter("trx_addons_filter_merge_styles", 'trx_addons_sc_accordionposts_merge_styles');
	function trx_addons_sc_accordionposts_merge_styles($list) {
		$list[ TRX_ADDONS_PLUGIN_SHORTCODES . 'accordionposts/accordionposts.css' ] = false;
		return $list;
	}
}

// Merge shortcode's specific styles to the single stylesheet (responsive)
if ( !function_exists( 'trx_addons_sc_accordionposts_merge_styles_responsive' ) ) {
	add_filter("trx_addons_filter_merge_styles_responsive", 'trx_addons_sc_accordionposts_merge_styles_responsive');
	function trx_addons_sc_accordionposts_merge_styles_responsive($list) {
		$list[ TRX_ADDONS_PLUGIN_SHORTCODES . 'accordionposts/accordionposts.responsive.css' ] = false;
		return $list;
	}
}

// Merge shortcode's specific scripts into single file
if ( !function_exists( 'trx_addons_sc_accordionposts_merge_scripts' ) ) {
	add_action("trx_addons_filter_merge_scripts", 'trx_addons_sc_accordionposts_merge_scripts');
	function trx_addons_sc_accordionposts_merge_scripts($list) {
		$list[ TRX_ADDONS_PLUGIN_SHORTCODES . 'accordionposts/accordionposts.js' ] = false;
		return $list;
	}
}

// Load styles and scripts if present in the cache of the menu
if ( !function_exists( 'trx_addons_sc_accordionposts_check_in_html_output' ) ) {
//	add_filter( 'trx_addons_filter_get_menu_cache_html', 'trx_addons_sc_accordionposts_check_in_html_output', 10, 1 );
//	add_action( 'trx_addons_action_show_layout_from_cache', 'trx_addons_sc_accordionposts_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_check_page_content', 'trx_addons_sc_accordionposts_check_in_html_output', 10, 1 );
	function trx_addons_sc_accordionposts_check_in_html_output( $content = '' ) {
		$args = array(
			'check' => array(
				'class=[\'"][^\'"]*sc_accordionposts'
			)
		);
		if ( trx_addons_check_in_html_output( 'sc_accordionposts', $content, $args ) ) {
			trx_addons_sc_accordionposts_load_scripts_front( true );
		}
		return $content;
	}
}


// trx_sc_accordionposts
//-------------------------------------------------------------
/*
[trx_sc_accordionposts id="unique_id" values="encoded_json_data"]
*/
if ( !function_exists( 'trx_addons_sc_accordionposts' ) ) {
	function trx_addons_sc_accordionposts($atts, $content=null) {
		$atts = trx_addons_sc_prepare_atts('trx_sc_accordionposts', $atts, trx_addons_sc_common_atts('id', array(
				// Individual params
				"type" => "default",
				"accordions" => "",
			))
		);
		if (function_exists('vc_param_group_parse_atts') && !is_array($atts['accordions'])) {
			$atts['accordions'] = (array) vc_param_group_parse_atts( $atts['accordions'] );
		}
		// Load shortcode-specific scripts and styles
		trx_addons_sc_accordionposts_load_scripts_front( true );
		// Load template
		$output = '';
		if (is_array($atts['accordions']) && count($atts['accordions']) > 0) {
			$output = trx_addons_get_template_part_as_string(array(
				TRX_ADDONS_PLUGIN_SHORTCODES . 'accordionposts/tpl.'.trx_addons_esc($atts['type']).'.php',
				TRX_ADDONS_PLUGIN_SHORTCODES . 'accordionposts/tpl.default.php'
			),
				'trx_addons_args_sc_accordionposts',
				$atts
			);
		}
		return apply_filters('trx_addons_sc_output', $output, 'trx_sc_accordionposts', $atts, $content);
	}
}


// Add shortcode [trx_sc_accordionposts]
if (!function_exists('trx_addons_sc_accordionposts_add_shortcode')) {
	function trx_addons_sc_accordionposts_add_shortcode() {
		add_shortcode("trx_sc_accordionposts", "trx_addons_sc_accordionposts");
	}
	add_action('init', 'trx_addons_sc_accordionposts_add_shortcode', 20);
}


// Add shortcodes
//----------------------------------------------------------------------------

// Add shortcodes to Elementor
if ( trx_addons_exists_elementor() && function_exists('trx_addons_elm_init') ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_SHORTCODES . 'accordionposts/accordionposts-sc-elementor.php';
}

// Add shortcodes to Gutenberg
if ( trx_addons_exists_gutenberg() && function_exists( 'trx_addons_gutenberg_get_param_id' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_SHORTCODES . 'accordionposts/accordionposts-sc-gutenberg.php';
}
