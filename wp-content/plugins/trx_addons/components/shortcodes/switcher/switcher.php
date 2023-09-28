<?php
/**
 * Shortcode: Switcher
 *
 * @package ThemeREX Addons
 * @since v2.6.0
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


// Load required styles and scripts for the frontend
if ( ! function_exists( 'trx_addons_sc_switcher_load_scripts_front' ) ) {
	add_action( 'wp_enqueue_scripts', 'trx_addons_sc_switcher_load_scripts_front', TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY );
	add_action( 'trx_addons_action_pagebuilder_preview_scripts', 'trx_addons_sc_switcher_load_scripts_front', 10, 1 );
	function trx_addons_sc_switcher_load_scripts_front( $force = false ) {
		trx_addons_enqueue_optimized( 'sc_switcher', $force, array(
			'css'  => array(
				'trx_addons-sc_switcher' => array( 'src' => TRX_ADDONS_PLUGIN_SHORTCODES . 'switcher/switcher.css' ),
			),
			'js'  => array(
				'trx_addons-sc_switcher' => array( 'src' => TRX_ADDONS_PLUGIN_SHORTCODES . 'switcher/switcher.js', 'deps' => 'jquery' ),
			),
			'check' => array(
				array( 'type' => 'sc',  'sc' => 'trx_sc_switcher' ),
				array( 'type' => 'gb',  'sc' => 'wp:trx-addons/switcher' ),
				array( 'type' => 'elm', 'sc' => '"widgetType":"trx_sc_switcher"' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[trx_sc_switcher' ),
			)
		) );
	}
}

// Enqueue responsive styles for frontend
if ( ! function_exists( 'trx_addons_sc_switcher_load_scripts_front_responsive' ) ) {
	add_action( 'wp_enqueue_scripts', 'trx_addons_sc_switcher_load_scripts_front_responsive', TRX_ADDONS_ENQUEUE_RESPONSIVE_PRIORITY );
	add_action( 'trx_addons_action_load_scripts_front_sc_switcher', 'trx_addons_sc_switcher_load_scripts_front_responsive', 10, 1 );
	function trx_addons_sc_switcher_load_scripts_front_responsive( $force = false ) {
		trx_addons_enqueue_optimized_responsive( 'sc_switcher', $force, array(
			'css'  => array(
				'trx_addons-sc_switcher-responsive' => array(
					'src' => TRX_ADDONS_PLUGIN_SHORTCODES . 'switcher/switcher.responsive.css',
					'media' => 'md'
				),
			),
		) );
	}
}

// Merge shortcode's specific styles to the single stylesheet
if ( ! function_exists( 'trx_addons_sc_switcher_merge_styles' ) ) {
	add_filter( 'trx_addons_filter_merge_styles', 'trx_addons_sc_switcher_merge_styles' );
	function trx_addons_sc_switcher_merge_styles( $list ) {
		$list[ TRX_ADDONS_PLUGIN_SHORTCODES . 'switcher/switcher.css' ] = false;
		return $list;
	}
}

// Merge shortcode's specific styles to the single stylesheet (responsive)
if ( ! function_exists( 'trx_addons_sc_switcher_merge_styles_responsive' ) ) {
	add_filter( 'trx_addons_filter_merge_styles_responsive', 'trx_addons_sc_switcher_merge_styles_responsive' );
	function trx_addons_sc_switcher_merge_styles_responsive( $list ) {
		$list[ TRX_ADDONS_PLUGIN_SHORTCODES . 'switcher/switcher.responsive.css' ] = false;
		return $list;
	}
}

// Merge shortcode's specific scripts into single file
if ( ! function_exists( 'trx_addons_sc_switcher_merge_scripts' ) ) {
	add_action( 'trx_addons_filter_merge_scripts', 'trx_addons_sc_switcher_merge_scripts' );
	function trx_addons_sc_switcher_merge_scripts( $list ) {
		$list[ TRX_ADDONS_PLUGIN_SHORTCODES . 'switcher/switcher.js' ] = false;
		return $list;
	}
}

// Load styles and scripts if present in the cache of the menu
if ( !function_exists( 'trx_addons_sc_switcher_check_in_html_output' ) ) {
//	add_filter( 'trx_addons_filter_get_menu_cache_html', 'trx_addons_sc_switcher_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_show_layout_from_cache', 'trx_addons_sc_switcher_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_check_page_content', 'trx_addons_sc_switcher_check_in_html_output', 10, 1 );
	function trx_addons_sc_switcher_check_in_html_output( $content = '' ) {
		$args = array(
			'check' => array(
				'class=[\'"][^\'"]*sc_switcher'
			)
		);
		if ( trx_addons_check_in_html_output( 'sc_switcher', $content, $args ) ) {
			trx_addons_sc_switcher_load_scripts_front( true );
		}
		return $content;
	}
}


// trx_sc_switcher
//-------------------------------------------------------------
/*
[trx_sc_switcher id="unique_id" ]
*/
if ( ! function_exists( 'trx_addons_sc_switcher' ) ) {
	function trx_addons_sc_switcher( $atts, $content = null ) {	
		$atts = trx_addons_sc_prepare_atts( 'trx_sc_switcher', $atts, trx_addons_sc_common_atts( 'id,title', array(
			// Individual params
			"type" => "default",
			"effect" => "swap",
			"slide1_type" => "",
			"slide1_title" => "",
			"slide1_title_color" => "",
			"slide1_switcher_color" => "",
			"slide1_section" => "",
			"slide1_layout" => "",
			"slide1_template" => "",
			"slide1_content" => "",
			"slide2_type" => "",
			"slide2_title" => "",
			"slide2_title_color" => "",
			"slide2_switcher_color" => "",
			"slide2_section" => "",
			"slide2_layout" => "",
			"slide2_template" => "",
			"slide2_content" => "",
			"slides" => "",
			) )
		);
		// Load shortcode-specific scripts and styles
		trx_addons_sc_switcher_load_scripts_front( true );
		// Load template
		$output = '';
		ob_start();
		trx_addons_get_template_part( array(
										TRX_ADDONS_PLUGIN_SHORTCODES . 'switcher/tpl.' . trx_addons_esc( $atts['type'] ) . '.php',
										TRX_ADDONS_PLUGIN_SHORTCODES . 'switcher/tpl.default.php'
										),
										'trx_addons_args_sc_switcher',
										$atts
									);
		$output = ob_get_contents();
		ob_end_clean();
		return apply_filters( 'trx_addons_sc_output', $output, 'trx_sc_switcher', $atts, $content );
	}
}


// Add shortcode [trx_sc_switcher]
if ( ! function_exists( 'trx_addons_sc_switcher_add_shortcode' ) ) {
	function trx_addons_sc_switcher_add_shortcode() {
		add_shortcode( 'trx_sc_switcher', 'trx_addons_sc_switcher' );
	}
	add_action( 'init', 'trx_addons_sc_switcher_add_shortcode', 20 );
}


// Add shortcodes
//----------------------------------------------------------------------------

// Add shortcodes to Elementor
if ( trx_addons_exists_elementor() && function_exists( 'trx_addons_elm_init' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_SHORTCODES . 'switcher/switcher-sc-elementor.php';
}
