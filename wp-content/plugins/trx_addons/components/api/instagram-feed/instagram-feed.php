<?php
/**
 * Plugin support: Instagram Feed
 *
 * @package ThemeREX Addons
 * @since v1.5
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

if ( ! function_exists( 'trx_addons_exists_instagram_feed' ) ) {
	/**
	 * Check if Instagram Feed is installed and activated
	 *
	 * @return bool  True if plugin is installed and activated
	 */
	function trx_addons_exists_instagram_feed() {
		return defined( 'SBIVER' );
	}
}

if ( ! function_exists( 'trx_addons_instagram_feed_load_scripts_front' ) ) {
	add_action( "wp_enqueue_scripts", 'trx_addons_instagram_feed_load_scripts_front', TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY );
	add_action( 'trx_addons_action_pagebuilder_preview_scripts', 'trx_addons_instagram_feed_load_scripts_front', 10, 1 );
	/**
	 * Enqueue custom styles and scripts for Instagram Feed on frontend
	 * 
	 * @hooked wp_enqueue_scripts
	 * @hooked trx_addons_action_pagebuilder_preview_scripts
	 * 
	 * @trigger trx_addons_action_load_scripts_front
	 * 
	 * @param bool $force  Force load scripts
	 */
	function trx_addons_instagram_feed_load_scripts_front( $force = false ) {
		if ( ! trx_addons_exists_instagram_feed() ) {
			return;
		}
		trx_addons_enqueue_optimized( 'instagram_feed', $force, array(
			'check' => array(
				array( 'type' => 'sc',  'sc' => 'instagram-feed' ),
				array( 'type' => 'gb',  'sc' => 'wp:sbi/sbi-feed-block' ),
				array( 'type' => 'elm', 'sc' => '"widgetType":"wp-widget-instagram-feed' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[instagram-feed' ),
			)
		) );
	}
}

if ( ! function_exists( 'trx_addons_instagram_feed_check_in_html_output' ) ) {
	add_filter( 'trx_addons_filter_get_menu_cache_html', 'trx_addons_instagram_feed_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_show_layout_from_cache', 'trx_addons_instagram_feed_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_check_page_content', 'trx_addons_instagram_feed_check_in_html_output', 10, 1 );
	/**
	 * Check if Instagram Feed is in the specified HTML content (page output, menu cache, layout cache)
	 * and force load scripts if it is present
	 * 
	 * @hooked trx_addons_filter_get_menu_cache_html
	 * @hooked trx_addons_action_show_layout_from_cache
	 * @hooked trx_addons_action_check_page_content
	 * 
	 * @trigger trx_addons_filter_check_in_html
	 * 
	 * @param string $content  HTML content to check
	 * 
	 * @return string  HTML content to check
	 */
	function trx_addons_instagram_feed_check_in_html_output( $content = '' ) {
		if ( ! trx_addons_exists_instagram_feed() ) {
			return $content;
		}
		$args = array(
			'check' => array(
				'class=[\'"][^\'"]*sbi_item',
				'id=[\'"][^\'"]*sbi_',
				'id=[\'"][^\'"]*sb_instagram'
			)
		);
		if ( trx_addons_check_in_html_output( 'instagram_feed', $content, $args ) ) {
			trx_addons_instagram_feed_load_scripts_front( true );
		}
		return $content;
	}
}

if ( ! function_exists( 'trx_addons_instagram_feed_filter_head_output' ) ) {
	add_filter( 'trx_addons_filter_page_head', 'trx_addons_instagram_feed_filter_head_output', 10, 1 );
	/**
	 * Remove plugin-specific styles and scripts from the page head if it is present and not need
	 * if an option 'Optimize CSS/JS loading' is set to 'Full'
	 * 
	 * @hooked trx_addons_filter_page_head
	 * 
	 * @trigger trx_addons_filter_remove_3rd_party_styles
	 * 
	 * @param string $content  Page head content
	 * 
	 * @return string  Page head content
	 */
	function trx_addons_instagram_feed_filter_head_output( $content = '' ) {
		if ( ! trx_addons_exists_instagram_feed() ) {
			return $content;
		}
		return trx_addons_filter_head_output( 'instagram_feed', $content, array(
			'check' => array(
				'#<link[^>]*href=[\'"][^\'"]*/instagram-feed/[^>]*>#'
			)
		) );
	}
}

if ( ! function_exists( 'trx_addons_instagram_feed_filter_body_output' ) ) {
	add_filter( 'trx_addons_filter_page_content', 'trx_addons_instagram_feed_filter_body_output', 10, 1 );
	/**
	 * Remove plugin-specific styles and scripts from the page body if it is present and not need
	 * if an option 'Optimize CSS/JS loading' is set to 'Full'
	 * 
	 * @hooked trx_addons_filter_page_content
	 * 
	 * @trigger trx_addons_filter_remove_3rd_party_styles
	 * 
	 * @param string $content  Page body content
	 * 
	 * @return string  Page body content
	 */
	function trx_addons_instagram_feed_filter_body_output( $content = '' ) {
		if ( ! trx_addons_exists_instagram_feed() ) {
			return $content;
		}
		return trx_addons_filter_body_output( 'instagram_feed', $content, array(
			'check' => array(
				'#<link[^>]*href=[\'"][^\'"]*/instagram-feed/[^>]*>#',
				'#<script[^>]*src=[\'"][^\'"]*/instagram-feed/[^>]*>[\\s\\S]*</script>#U'
			)
		) );
	}
}


// Demo data install
//----------------------------------------------------------------------------

// One-click import support
if ( is_admin() ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'instagram-feed/instagram-feed-demo-importer.php';
}

// OCDI support
if ( is_admin() && trx_addons_exists_instagram_feed() && function_exists( 'trx_addons_exists_ocdi' ) && trx_addons_exists_ocdi() ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'instagram-feed/instagram-feed-demo-ocdi.php';
}
