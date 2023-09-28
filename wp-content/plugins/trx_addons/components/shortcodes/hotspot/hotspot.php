<?php
/**
 * Shortcode: Hotspot
 *
 * @package ThemeREX Addons
 * @since v1.94.0
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


// Load required styles and scripts for the frontend
if ( ! function_exists( 'trx_addons_sc_hotspot_load_scripts_front' ) ) {
	add_action( 'wp_enqueue_scripts', 'trx_addons_sc_hotspot_load_scripts_front', TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY );
	add_action( 'trx_addons_action_pagebuilder_preview_scripts', 'trx_addons_sc_hotspot_load_scripts_front', 10, 1 );
	function trx_addons_sc_hotspot_load_scripts_front( $force = false ) {
		trx_addons_enqueue_optimized( 'sc_hotspot', $force, array(
			'css'  => array(
				'trx_addons-sc_hotspot' => array( 'src' => TRX_ADDONS_PLUGIN_SHORTCODES . 'hotspot/hotspot.css' ),
			),
			'js'  => array(
				'trx_addons-sc_hotspot' => array( 'src' => TRX_ADDONS_PLUGIN_SHORTCODES . 'hotspot/hotspot.js', 'deps' => 'jquery' ),
			),
			'check' => array(
				array( 'type' => 'sc',  'sc' => 'trx_sc_hotspot' ),
				array( 'type' => 'gb',  'sc' => 'wp:trx-addons/hotspot' ),
				array( 'type' => 'elm', 'sc' => '"widgetType":"trx_sc_hotspot"' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[trx_sc_hotspot' ),
			)
		) );
	}
}

// Enqueue responsive styles for frontend
if ( ! function_exists( 'trx_addons_sc_hotspot_load_scripts_front_responsive' ) ) {
	add_action( 'wp_enqueue_scripts', 'trx_addons_sc_hotspot_load_scripts_front_responsive', TRX_ADDONS_ENQUEUE_RESPONSIVE_PRIORITY );
	add_action( 'trx_addons_action_load_scripts_front_sc_hotspot', 'trx_addons_sc_hotspot_load_scripts_front_responsive', 10, 1 );
	function trx_addons_sc_hotspot_load_scripts_front_responsive( $force = false ) {
		trx_addons_enqueue_optimized_responsive( 'sc_hotspot', $force, array(
			'css'  => array(
				'trx_addons-sc_hotspot-responsive' => array(
					'src' => TRX_ADDONS_PLUGIN_SHORTCODES . 'hotspot/hotspot.responsive.css',
					'media' => 'lg'
				),
			),
		) );
	}
}
	
// Merge shortcode's specific styles to the single stylesheet
if ( ! function_exists( 'trx_addons_sc_hotspot_merge_styles' ) ) {
	add_filter( 'trx_addons_filter_merge_styles', 'trx_addons_sc_hotspot_merge_styles' );
	function trx_addons_sc_hotspot_merge_styles( $list ) {
		$list[ TRX_ADDONS_PLUGIN_SHORTCODES . 'hotspot/hotspot.css' ] = false;
		return $list;
	}
}

// Merge shortcode's specific styles to the single stylesheet (responsive)
if ( ! function_exists( 'trx_addons_sc_hotspot_merge_styles_responsive' ) ) {
	add_filter( 'trx_addons_filter_merge_styles_responsive', 'trx_addons_sc_hotspot_merge_styles_responsive' );
	function trx_addons_sc_hotspot_merge_styles_responsive( $list ) {
		$list[ TRX_ADDONS_PLUGIN_SHORTCODES . 'hotspot/hotspot.responsive.css' ] = false;
		return $list;
	}
}

// Merge shortcode's specific scripts into single file
if ( ! function_exists( 'trx_addons_sc_hotspot_merge_scripts' ) ) {
	add_action( 'trx_addons_filter_merge_scripts', 'trx_addons_sc_hotspot_merge_scripts' );
	function trx_addons_sc_hotspot_merge_scripts( $list ) {
		$list[ TRX_ADDONS_PLUGIN_SHORTCODES . 'hotspot/hotspot.js' ] = false;
		return $list;
	}
}

// Load styles and scripts if present in the cache of the menu
if ( !function_exists( 'trx_addons_sc_hotspot_check_in_html_output' ) ) {
//	add_filter( 'trx_addons_filter_get_menu_cache_html', 'trx_addons_sc_hotspot_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_show_layout_from_cache', 'trx_addons_sc_hotspot_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_check_page_content', 'trx_addons_sc_hotspot_check_in_html_output', 10, 1 );
	function trx_addons_sc_hotspot_check_in_html_output( $content = '' ) {
		$args = array(
			'check' => array(
				'class=[\'"][^\'"]*sc_hotspot'
			)
		);
		if ( trx_addons_check_in_html_output( 'sc_hotspot', $content, $args ) ) {
			trx_addons_sc_hotspot_load_scripts_front( true );
		}
		return $content;
	}
}


// trx_sc_hotspot
//-------------------------------------------------------------
/*
[trx_sc_hotspot id="unique_id" image="image_url" spots="encoded_json_data"]
*/
if ( ! function_exists( 'trx_addons_sc_hotspot' ) ) {
	function trx_addons_sc_hotspot( $atts, $content = null ) {	
		$atts = trx_addons_sc_prepare_atts( 'trx_sc_hotspot', $atts, trx_addons_sc_common_atts( 'id,title', array(
			// Individual params
			"type" => "default",
			"image" => "",
			"image_link" => "",
			"spots" => ""
			) )
		);
		if ( function_exists( 'vc_param_group_parse_atts' ) && ! is_array( $atts['spots'] ) ) {
			$atts['spots'] = (array) vc_param_group_parse_atts( $atts['spots'] );
		}
		// Load shortcode-specific scripts and styles
		trx_addons_sc_hotspot_load_scripts_front( true );
		// Load template
		$output = '';
		if ( ! empty( $atts['image'] ) ) {
			if ( is_array( $atts['spots'] ) && count( $atts['spots'] ) > 0 ) {
				foreach ( $atts['spots'] as $k => $v ) {
					if ( ! empty( $v['description'] ) ) {
						$atts['spots'][$k]['description'] = preg_replace(
																'/\\[(.*)\\]/',
																'<b>$1</b>',
																function_exists( 'vc_value_from_safe' )
																	? vc_value_from_safe( $v['description'] )
																	: $v['description']
																);
					}
				}
			}
			ob_start();
			trx_addons_get_template_part( array(
											TRX_ADDONS_PLUGIN_SHORTCODES . 'hotspot/tpl.' . trx_addons_esc( $atts['type'] ) . '.php',
											TRX_ADDONS_PLUGIN_SHORTCODES . 'hotspot/tpl.default.php'
											),
											'trx_addons_args_sc_hotspot',
											$atts
										);
			$output = ob_get_contents();
			ob_end_clean();
		}
		return apply_filters( 'trx_addons_sc_output', $output, 'trx_sc_hotspot', $atts, $content );
	}
}


// Add shortcode [trx_sc_hotspot]
if ( ! function_exists( 'trx_addons_sc_hotspot_add_shortcode' ) ) {
	function trx_addons_sc_hotspot_add_shortcode() {
		add_shortcode( 'trx_sc_hotspot', 'trx_addons_sc_hotspot' );
	}
	add_action( 'init', 'trx_addons_sc_hotspot_add_shortcode', 20 );
}


// Add shortcodes
//----------------------------------------------------------------------------

// Add shortcodes to Elementor
if ( trx_addons_exists_elementor() && function_exists( 'trx_addons_elm_init' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_SHORTCODES . 'hotspot/hotspot-sc-elementor.php';
}

// Add shortcodes to Gutenberg
if ( trx_addons_exists_gutenberg() && function_exists( 'trx_addons_gutenberg_get_param_id' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_SHORTCODES . 'hotspot/hotspot-sc-gutenberg.php';
}
