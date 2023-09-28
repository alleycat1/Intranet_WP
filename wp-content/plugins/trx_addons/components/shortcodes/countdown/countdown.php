<?php
/**
 * Shortcode: Countdown
 *
 * @package ThemeREX Addons
 * @since v1.4.3
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}



// Load required styles and scripts for the frontend
if ( !function_exists( 'trx_addons_sc_countdown_load_scripts_front' ) ) {
	add_action( "wp_enqueue_scripts", 'trx_addons_sc_countdown_load_scripts_front', TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY );
	add_action( 'trx_addons_action_pagebuilder_preview_scripts', 'trx_addons_sc_countdown_load_scripts_front', 10, 1 );
	function trx_addons_sc_countdown_load_scripts_front( $force = false ) {
		trx_addons_enqueue_optimized( 'sc_countdown', $force, array(
			'css'  => array(
				'trx_addons-sc_countdown' => array( 'src' => TRX_ADDONS_PLUGIN_SHORTCODES . 'countdown/countdown.css' ),
			),
			'js'  => array(
				'jquery-plugin'           => array( 'src' => TRX_ADDONS_PLUGIN_SHORTCODES . 'countdown/jquery.plugin.js', 'deps' => 'jquery' ),
				'jquery-countdown'        => array( 'src' => TRX_ADDONS_PLUGIN_SHORTCODES . 'countdown/jquery.countdown.js', 'deps' => 'jquery' ),
				'trx_addons-sc_countdown' => array( 'src' => TRX_ADDONS_PLUGIN_SHORTCODES . 'countdown/countdown.js', 'deps' => 'jquery' ),
			),
			'check' => array(
				array( 'type' => 'sc',  'sc' => 'trx_sc_countdown' ),
				array( 'type' => 'gb',  'sc' => 'wp:trx-addons/countdown' ),
				array( 'type' => 'elm', 'sc' => '"widgetType":"trx_sc_countdown"' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[trx_sc_countdown' ),
			)
		) );
	}
}

// Enqueue responsive styles for frontend
if ( ! function_exists( 'trx_addons_sc_countdown_load_scripts_front_responsive' ) ) {
	add_action( 'wp_enqueue_scripts', 'trx_addons_sc_countdown_load_scripts_front_responsive', TRX_ADDONS_ENQUEUE_RESPONSIVE_PRIORITY );
	add_action( 'trx_addons_action_load_scripts_front_sc_countdown', 'trx_addons_sc_countdown_load_scripts_front_responsive', 10, 1 );
	function trx_addons_sc_countdown_load_scripts_front_responsive( $force = false ) {
		trx_addons_enqueue_optimized_responsive( 'sc_countdown', $force, array(
			'css'  => array(
				'trx_addons-sc_countdown-responsive' => array(
					'src' => TRX_ADDONS_PLUGIN_SHORTCODES . 'countdown/countdown.responsive.css',
					'media' => 'sm'
				),
			),
		) );
	}
}
	
// Merge shortcode's specific styles into single stylesheet
if ( !function_exists( 'trx_addons_sc_countdown_merge_styles' ) ) {
	add_filter("trx_addons_filter_merge_styles", 'trx_addons_sc_countdown_merge_styles');
	function trx_addons_sc_countdown_merge_styles($list) {
		$list[ TRX_ADDONS_PLUGIN_SHORTCODES . 'countdown/countdown.css' ] = false;
		return $list;
	}
}

// Merge shortcode's specific styles to the single stylesheet (responsive)
if ( !function_exists( 'trx_addons_sc_countdown_merge_styles_responsive' ) ) {
	add_filter("trx_addons_filter_merge_styles_responsive", 'trx_addons_sc_countdown_merge_styles_responsive');
	function trx_addons_sc_countdown_merge_styles_responsive($list) {
		$list[ TRX_ADDONS_PLUGIN_SHORTCODES . 'countdown/countdown.responsive.css' ] = false;
		return $list;
	}
}

// Merge countdown specific scripts into single file
if ( !function_exists( 'trx_addons_sc_countdown_merge_scripts' ) ) {
	add_action("trx_addons_filter_merge_scripts", 'trx_addons_sc_countdown_merge_scripts');
	function trx_addons_sc_countdown_merge_scripts($list) {
		$list[ TRX_ADDONS_PLUGIN_SHORTCODES . 'countdown/jquery.plugin.js' ] = false;
		$list[ TRX_ADDONS_PLUGIN_SHORTCODES . 'countdown/jquery.countdown.js' ] = false;
		$list[ TRX_ADDONS_PLUGIN_SHORTCODES . 'countdown/countdown.js' ] = false;
		return $list;
	}
}


// Load styles and scripts if present in the cache of the menu
if ( !function_exists( 'trx_addons_sc_countdown_check_in_html_output' ) ) {
//	add_filter( 'trx_addons_filter_get_menu_cache_html', 'trx_addons_sc_countdown_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_show_layout_from_cache', 'trx_addons_sc_countdown_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_check_page_content', 'trx_addons_sc_countdown_check_in_html_output', 10, 1 );
	function trx_addons_sc_countdown_check_in_html_output( $content = '' ) {
		$args = array(
			'check' => array(
				'class=[\'"][^\'"]*sc_countdown'
			)
		);
		if ( trx_addons_check_in_html_output( 'sc_countdown', $content, $args ) ) {
			trx_addons_sc_countdown_load_scripts_front( true );
		}
		return $content;
	}
}



// trx_sc_countdown
//-------------------------------------------------------------
/*
[trx_sc_countdown id="unique_id" date="2017-12-31" time="23:59:59"]
*/
if ( !function_exists( 'trx_addons_sc_countdown' ) ) {
	function trx_addons_sc_countdown($atts, $content=null){	
		$atts = trx_addons_sc_prepare_atts('trx_sc_countdown', $atts, trx_addons_sc_common_atts('id,title', array(
			// Individual params
			"type" => "default",
			"date" => "",
			"time" => "",
			"date_time" => "",
			"date_time_restart" => "",
			"count_to" => 1,
			"count_restart" => 0,
			"align" => "center",
			))
		);

		// Load shortcode-specific  scripts and styles
		trx_addons_sc_countdown_load_scripts_front( true );

		// Load template
		ob_start();
		trx_addons_get_template_part(array(
										TRX_ADDONS_PLUGIN_SHORTCODES . 'countdown/tpl.'.trx_addons_esc($atts['type']).'.php',
										TRX_ADDONS_PLUGIN_SHORTCODES . 'countdown/tpl.default.php'
										),
                                        'trx_addons_args_sc_countdown',
                                        $atts
                                    );
		$output = ob_get_contents();
		ob_end_clean();

		return apply_filters('trx_addons_sc_output', $output, 'trx_sc_countdown', $atts, $content);
	}
}


// Add shortcode [trx_sc_countdown]
if (!function_exists('trx_addons_sc_countdown_add_shortcode')) {
	function trx_addons_sc_countdown_add_shortcode() {
		add_shortcode("trx_sc_countdown", "trx_addons_sc_countdown");
	}
	add_action('init', 'trx_addons_sc_countdown_add_shortcode', 20);
}


// Add shortcodes
//----------------------------------------------------------------------------

// Add shortcodes to Elementor
if ( trx_addons_exists_elementor() && function_exists('trx_addons_elm_init') ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_SHORTCODES . 'countdown/countdown-sc-elementor.php';
}

// Add shortcodes to Gutenberg
if ( trx_addons_exists_gutenberg() && function_exists( 'trx_addons_gutenberg_get_param_id' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_SHORTCODES . 'countdown/countdown-sc-gutenberg.php';
}

// Add shortcodes to VC
if ( trx_addons_exists_vc() && function_exists( 'trx_addons_vc_add_id_param' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_SHORTCODES . 'countdown/countdown-sc-vc.php';
}
