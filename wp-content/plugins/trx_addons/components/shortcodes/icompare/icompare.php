<?php
/**
 * Shortcode: Images compare
 *
 * @package ThemeREX Addons
 * @since v1.97.0
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


// Load required styles and scripts for the frontend
if ( ! function_exists( 'trx_addons_sc_icompare_load_scripts_front' ) ) {
	add_action( 'wp_enqueue_scripts', 'trx_addons_sc_icompare_load_scripts_front', TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY );
	add_action( 'trx_addons_action_pagebuilder_preview_scripts', 'trx_addons_sc_icompare_load_scripts_front', 10, 1 );
	function trx_addons_sc_icompare_load_scripts_front( $force = false ) {
		trx_addons_enqueue_optimized( 'sc_icompare', $force, array(
			'lib' => array(
				'js' => array(
					'jquery-ui-touch-punch' => array( 'src' => 'js/touch-punch/jquery.ui.touch-punch.min.js', 'deps' => array( 'jquery', 'jquery-ui-draggable' ) ),
				),
			),
			'css'  => array(
				'trx_addons-sc_icompare' => array( 'src' => TRX_ADDONS_PLUGIN_SHORTCODES . 'icompare/icompare.css' ),
			),
			'js' => array(
				'trx_addons-sc_icompare' => array( 'src' => TRX_ADDONS_PLUGIN_SHORTCODES . 'icompare/icompare.js', 'deps' => array( 'jquery', 'jquery-ui-draggable', 'jquery-ui-touch-punch' ) ),
			),
			'check' => array(
				array( 'type' => 'sc',  'sc' => 'trx_sc_icompare' ),
				array( 'type' => 'gb',  'sc' => 'wp:trx-addons/icompare' ),
				array( 'type' => 'elm', 'sc' => '"widgetType":"trx_sc_icompare"' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[trx_sc_icompare' ),
			)
		) );
	}
}

// Enqueue responsive styles for frontend
if ( ! function_exists( 'trx_addons_sc_icompare_load_scripts_front_responsive' ) ) {
	add_action( 'wp_enqueue_scripts', 'trx_addons_sc_icompare_load_scripts_front_responsive', TRX_ADDONS_ENQUEUE_RESPONSIVE_PRIORITY );
	add_action( 'trx_addons_action_load_scripts_front_sc_icompare', 'trx_addons_sc_icompare_load_scripts_front_responsive', 10, 1 );
	function trx_addons_sc_icompare_load_scripts_front_responsive( $force = false ) {
		trx_addons_enqueue_optimized_responsive( 'sc_icompare', $force, array(
			'css'  => array(
				'trx_addons-sc_icompare-responsive' => array(
					'src' => TRX_ADDONS_PLUGIN_SHORTCODES . 'icompare/icompare.responsive.css',
					'media' => 'lg'
				),
			),
		) );
	}
}
	
// Merge shortcode's specific styles to the single stylesheet
if ( ! function_exists( 'trx_addons_sc_icompare_merge_styles' ) ) {
	add_filter( 'trx_addons_filter_merge_styles', 'trx_addons_sc_icompare_merge_styles' );
	function trx_addons_sc_icompare_merge_styles( $list ) {
		$list[ TRX_ADDONS_PLUGIN_SHORTCODES . 'icompare/icompare.css' ] = false;
		return $list;
	}
}

// Merge shortcode's specific styles to the single stylesheet (responsive)
if ( ! function_exists( 'trx_addons_sc_icompare_merge_styles_responsive' ) ) {
	add_filter( 'trx_addons_filter_merge_styles_responsive', 'trx_addons_sc_icompare_merge_styles_responsive' );
	function trx_addons_sc_icompare_merge_styles_responsive( $list ) {
		$list[ TRX_ADDONS_PLUGIN_SHORTCODES . 'icompare/icompare.responsive.css' ] = false;
		return $list;
	}
}

// Merge shortcode's specific scripts into single file
if ( ! function_exists( 'trx_addons_sc_icompare_merge_scripts' ) ) {
	add_action( 'trx_addons_filter_merge_scripts', 'trx_addons_sc_icompare_merge_scripts' );
	function trx_addons_sc_icompare_merge_scripts( $list ) {
		$list[ TRX_ADDONS_PLUGIN_SHORTCODES . 'icompare/icompare.js' ] = false;
		return $list;
	}
}

// Load styles and scripts if present in the cache of the menu
if ( !function_exists( 'trx_addons_sc_icompare_check_in_html_output' ) ) {
	add_filter( 'trx_addons_filter_get_menu_cache_html', 'trx_addons_sc_icompare_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_show_layout_from_cache', 'trx_addons_sc_icompare_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_check_page_content', 'trx_addons_sc_icompare_check_in_html_output', 10, 1 );
	function trx_addons_sc_icompare_check_in_html_output( $content = '' ) {
		$args = array(
			'check' => array(
				'class=[\'"][^\'"]*sc_icompare'
			)
		);
		if ( trx_addons_check_in_html_output( 'sc_icompare', $content, $args ) ) {
			trx_addons_sc_icompare_load_scripts_front( true );
		}
		return $content;
	}
}


// trx_sc_icompare
//-------------------------------------------------------------
/*
[trx_sc_icompare id="unique_id" image1="image1_url" image2="image2_url" direction="vertical" handler="style1"]
*/
if ( ! function_exists( 'trx_addons_sc_icompare' ) ) {
	function trx_addons_sc_icompare( $atts, $content = null ) {	
		$atts = trx_addons_sc_prepare_atts( 'trx_sc_icompare', $atts, trx_addons_sc_common_atts( 'id,icon,title', array(
			// Individual params
			"type" => "default",
			"image1" => "",
			"image2" => "",
			"direction" => "vertical",
			"event" => "drag",
			"handler" => "round",
			"handler_image" => "",
			"handler_pos" => 50,
			"handler_separator" => 0,
			"before_text" => "",
			"before_pos" => "tl",
			"after_text" => "",
			"after_pos" => "br",
			) )
		);
		// Load shortcode-specific scripts and styles
		trx_addons_sc_icompare_load_scripts_front( true );
		// Load template
		$output = '';
		if ( ! empty( $atts['image1'] ) && ! empty( $atts['image2'] ) ) {
			ob_start();
			trx_addons_get_template_part( array(
											TRX_ADDONS_PLUGIN_SHORTCODES . 'icompare/tpl.' . trx_addons_esc( $atts['type'] ) . '.php',
											TRX_ADDONS_PLUGIN_SHORTCODES . 'icompare/tpl.default.php'
											),
											'trx_addons_args_sc_icompare',
											$atts
										);
			$output = ob_get_contents();
			ob_end_clean();
		}
		return apply_filters( 'trx_addons_sc_output', $output, 'trx_sc_icompare', $atts, $content );
	}
}


// Add shortcode [trx_sc_icompare]
if ( ! function_exists( 'trx_addons_sc_icompare_add_shortcode' ) ) {
	function trx_addons_sc_icompare_add_shortcode() {
		add_shortcode( 'trx_sc_icompare', 'trx_addons_sc_icompare' );
	}
	add_action( 'init', 'trx_addons_sc_icompare_add_shortcode', 20 );
}


// Add shortcodes
//----------------------------------------------------------------------------

// Add shortcodes to Elementor
if ( trx_addons_exists_elementor() && function_exists( 'trx_addons_elm_init' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_SHORTCODES . 'icompare/icompare-sc-elementor.php';
}

// Add shortcodes to Gutenberg
if ( trx_addons_exists_gutenberg() && function_exists( 'trx_addons_gutenberg_get_param_id' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_SHORTCODES . 'icompare/icompare-sc-gutenberg.php';
}
