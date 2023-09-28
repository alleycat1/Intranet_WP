<?php
/**
 * Shortcode: AI chat_topics Topics
 *
 * @package ThemeREX Addons
 * @since v2.22.0
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

// Load required styles and scripts for the frontend
if ( ! function_exists( 'trx_addons_sc_chat_topics_load_scripts_front' ) ) {
	add_action( "wp_enqueue_scripts", 'trx_addons_sc_chat_topics_load_scripts_front', TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY );
	add_action( 'trx_addons_action_pagebuilder_preview_scripts', 'trx_addons_sc_chat_topics_load_scripts_front', 10, 1 );
	function trx_addons_sc_chat_topics_load_scripts_front( $force = false ) {
		trx_addons_enqueue_optimized( 'sc_chat_topics', $force, array(
			'css'  => array(
				'trx_addons-sc_chat_topics' => array( 'src' => TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/chat_topics/chat_topics.css' ),
			),
			'js' => array(
				'trx_addons-sc_chat_topics' => array( 'src' => TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/chat_topics/chat_topics.js', 'deps' => 'jquery' ),
			),
			'check' => array(
				array( 'type' => 'sc',  'sc' => 'trx_sc_chat_topics' ),
				array( 'type' => 'gb',  'sc' => 'wp:trx-addons/chat_topics' ),
				array( 'type' => 'elm', 'sc' => '"widgetType":"trx_sc_chat_topics"' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[trx_sc_chat_topics' ),
			)
		) );
	}
}

// Enqueue responsive styles for frontend
/*
if ( ! function_exists( 'trx_addons_sc_chat_topics_load_scripts_front_responsive' ) ) {
	add_action( 'wp_enqueue_scripts', 'trx_addons_sc_chat_topics_load_scripts_front_responsive', TRX_ADDONS_ENQUEUE_RESPONSIVE_PRIORITY );
	add_action( 'trx_addons_action_load_scripts_front_sc_chat_topics', 'trx_addons_sc_chat_topics_load_scripts_front_responsive', 10, 1 );
	function trx_addons_sc_chat_topics_load_scripts_front_responsive( $force = false  ) {
		trx_addons_enqueue_optimized_responsive( 'sc_chat_topics', $force, array(
			'css'  => array(
				'trx_addons-sc_chat_topics-responsive' => array(
					'src' => TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/chat_topics/chat_topics.responsive.css',
					'media' => 'sm'
				),
			),
		) );
	}
}
*/

// Merge shortcode's specific styles to the single stylesheet
if ( ! function_exists( 'trx_addons_sc_chat_topics_merge_styles' ) ) {
	add_filter( "trx_addons_filter_merge_styles", 'trx_addons_sc_chat_topics_merge_styles' );
	function trx_addons_sc_chat_topics_merge_styles( $list ) {
		$list[ TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/chat_topics/chat_topics.css' ] = false;
		return $list;
	}
}

// Merge shortcode's specific styles to the single stylesheet (responsive)
/*
if ( ! function_exists( 'trx_addons_sc_chat_topics_merge_styles_responsive' ) ) {
	add_filter("trx_addons_filter_merge_styles_responsive", 'trx_addons_sc_chat_topics_merge_styles_responsive' );
	function trx_addons_sc_chat_topics_merge_styles_responsive( $list ) {
		$list[ TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/chat_topics/chat_topics.responsive.css' ] = false;
		return $list;
	}
}
*/

// Merge shortcode's specific scripts into single file
if ( ! function_exists( 'trx_addons_sc_chat_topics_merge_scripts' ) ) {
	add_action("trx_addons_filter_merge_scripts", 'trx_addons_sc_chat_topics_merge_scripts');
	function trx_addons_sc_chat_topics_merge_scripts($list) {
		$list[ TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/chat_topics/chat_topics.js' ] = false;
		return $list;
	}
}

// Load styles and scripts if present in the cache of the menu
if ( ! function_exists( 'trx_addons_sc_chat_topics_check_in_html_output' ) ) {
	add_filter( 'trx_addons_filter_get_menu_cache_html', 'trx_addons_sc_chat_topics_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_show_layout_from_cache', 'trx_addons_sc_chat_topics_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_check_page_content', 'trx_addons_sc_chat_topics_check_in_html_output', 10, 1 );
	function trx_addons_sc_chat_topics_check_in_html_output( $content = '' ) {
		$args = array(
			'check' => array(
				'class=[\'"][^\'"]*sc_chat_topics'
			)
		);
		if ( trx_addons_check_in_html_output( 'sc_chat_topics', $content, $args ) ) {
			trx_addons_sc_chat_topics_load_scripts_front( true );
		}
		return $content;
	}
}


// trx_sc_chat_topics
//-------------------------------------------------------------
/*
[trx_sc_chat_topics id="unique_id" prompt="prompt text for ai" command="blog-post"]
*/
if ( ! function_exists( 'trx_addons_sc_chat_topics' ) ) {
	function trx_addons_sc_chat_topics( $atts, $content = null ) {	
		$atts = trx_addons_sc_prepare_atts( 'trx_sc_chat_topics', $atts, trx_addons_sc_common_atts( 'id,title', array(
			// Individual params
			"type" => "default",
			"number" => "",
			"chat_id" => "",
			"topics" => ""
		) ) );

		// Load shortcode-specific scripts and styles
		trx_addons_sc_chat_topics_load_scripts_front( true );

		// Check atts
		if ( empty( $atts['number'] ) ) {
			$atts['number'] = 5;
		}
		$atts['number'] = max( 1, min( apply_filters( 'trx_addons_filter_sc_chat_topics_max', 20 ), (int) $atts['number'] ) );

		// Load template
		$output = '';

		ob_start();
		trx_addons_get_template_part( array(
										TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/chat_topics/tpl.' . trx_addons_esc( $atts['type'] ) . '.php',
										TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/chat_topics/tpl.default.php'
										),
										'trx_addons_args_sc_chat_topics',
										$atts
									);
		$output = ob_get_contents();
		ob_end_clean();
		return apply_filters( 'trx_addons_sc_output', $output, 'trx_sc_chat_topics', $atts, $content );
	}
}

// Add shortcode [trx_sc_chat_topics]
if ( ! function_exists( 'trx_addons_sc_chat_topics_add_shortcode' ) ) {
	add_action( 'init', 'trx_addons_sc_chat_topics_add_shortcode', 20 );
	function trx_addons_sc_chat_topics_add_shortcode() {
		add_shortcode( "trx_sc_chat_topics", "trx_addons_sc_chat_topics" );
	}
}

// Get a list with saved topics
if ( ! function_exists( 'trx_addons_sc_chat_topics_get_saved_topics' ) ) {
	function trx_addons_sc_chat_topics_get_saved_topics( $number = 0 ) {
		$topics = get_option( 'trx_addons_sc_chat_topics' );
		if ( ! is_array( $topics ) ) {
			$topics = array();
		}
		return $number == 0 ? $topics : array_slice( $topics, 0, $number );
	}
}


// Add shortcodes
//----------------------------------------------------------------------------

// Add shortcodes to Elementor
if ( trx_addons_exists_elementor() && function_exists('trx_addons_elm_init') ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/chat_topics/chat_topics-sc-elementor.php';
}

// Add shortcodes to Gutenberg
if ( trx_addons_exists_gutenberg() && function_exists( 'trx_addons_gutenberg_get_param_id' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/chat_topics/chat_topics-sc-gutenberg.php';
}
