<?php
/**
 * Plugin support: Essential Grid
 *
 * @package ThemeREX Addons
 * @since v1.5
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

if ( ! function_exists( 'trx_addons_exists_essential_grid' ) ) {
	/**
	 * Check if plugin 'Essential Grid' is installed and activated
	 *
	 * @return bool  True if plugin is installed and activated
	 */
	function trx_addons_exists_essential_grid() {
		return defined('EG_PLUGIN_PATH') || defined( 'ESG_PLUGIN_PATH' );
	}
}
	
if ( ! function_exists( 'trx_addons_essential_grid_not_defer_scripts' ) ) {
	add_filter( "trx_addons_filter_skip_move_scripts_down", 'trx_addons_essential_grid_not_defer_scripts' );
	add_filter( "trx_addons_filter_skip_async_scripts_load", 'trx_addons_essential_grid_not_defer_scripts' );
	/**
	 * Add a plugin-specific scripts to the list with the scripts which are not deferred or async
	 * 
	 * @hooked trx_addons_filter_skip_move_scripts_down
	 * @hooked trx_addons_filter_skip_async_scripts_load
	 *
	 * @param array $list  List of the scripts which are not deferred or async
	 * 
	 * @return array       Modified list
	 */
	function trx_addons_essential_grid_not_defer_scripts( $list ) {
		if ( trx_addons_exists_essential_grid() ) {
			$list[] = 'essential-grid';
		}
		return $list;
	}
}

if ( ! function_exists( 'trx_addons_essential_grid_load_scripts_front' ) ) {
	add_action( "wp_enqueue_scripts", 'trx_addons_essential_grid_load_scripts_front', TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY );
	add_action( 'trx_addons_action_pagebuilder_preview_scripts', 'trx_addons_essential_grid_load_scripts_front', 10, 1 );
	/**
	 * Load required styles and scripts for the frontend
	 * 
	 * @hooked wp_enqueue_scripts
	 * @hooked trx_addons_action_pagebuilder_preview_scripts
	 * 
	 * @trigger trx_addons_action_load_scripts_front
	 *
	 * @param bool $force  Load scripts always or check conditions
	 */
	function trx_addons_essential_grid_load_scripts_front( $force = false ) {
		if ( ! trx_addons_exists_essential_grid() ) {
			return;
		}
		trx_addons_enqueue_optimized( 'essential_grid', $force, array(
			'check' => array(
				array( 'type' => 'sc',  'sc' => 'widget_ess_grid' ),
				array( 'type' => 'sc',  'sc' => 'ess_grid' ),
				array( 'type' => 'sc',  'sc' => 'ess_grid_ajax_target' ),
				array( 'type' => 'sc',  'sc' => 'ess_grid_nav' ),
				array( 'type' => 'sc',  'sc' => 'ess_grid_search' ),
				array( 'type' => 'gb',  'sc' => 'wp:themepunch/essgrid' ),
				array( 'type' => 'elm', 'sc' => '"widgetType":"wp-widget-ess-grid' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[ess_grid' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[widget_ess_grid' ),
			)
		) );
	}
}

if ( ! function_exists( 'trx_addons_essential_grid_check_in_html_output' ) ) {
//	add_filter( 'trx_addons_filter_get_menu_cache_html', 'trx_addons_essential_grid_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_show_layout_from_cache', 'trx_addons_essential_grid_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_check_page_content', 'trx_addons_essential_grid_check_in_html_output', 10, 1 );
	/**
	 * Check if the Essential Grid is in the page HTML-output or in the cached layout 
	 * 
	 * @hooked trx_addons_action_show_layout_from_cache
	 * @hooked trx_addons_action_check_page_content
	 *
	 * @trigger trx_addons_filter_check_in_html
	 * 
	 * @param string $content  Page content or cached layout content
	 * 
	 * @return bool  		True if the Essential Grid is found in the content
	 */
	function trx_addons_essential_grid_check_in_html_output( $content = '' ) {
		if ( ! trx_addons_exists_essential_grid() ) {
			return $content;
		}
		$args = array(
			'check' => array(
				'id=[\'"][^\'"]*ess\\-grid\\-',
				'class=[\'"][^\'"]*(ess\\-grid\\-|widget_ess_grid)',
				'class=[\'"][^\'"]*type\\-' . apply_filters( 'essgrid_PunchPost_custom_post_type', 'essential_grid' ),
				'class=[\'"][^\'"]*' . apply_filters( 'essgrid_PunchPost_category', 'essential_grid_category' ) . '\\-',
			)
		);
		if ( trx_addons_check_in_html_output( 'essential_grid', $content, $args ) ) {
			trx_addons_essential_grid_load_scripts_front( true );
		}
		return $content;
	}
}

if ( ! function_exists( 'trx_addons_essential_grid_filter_head_output' ) ) {
	add_filter( 'trx_addons_filter_page_head', 'trx_addons_essential_grid_filter_head_output', 10, 1 );
	/**
	 * Remove plugin-specific styles and scripts from the page head if the plugin not need in the current page
	 * and the optimization mode 'Full' is used
	 * 
	 * @hooked trx_addons_filter_page_head
	 * 
	 * @param string $content  Page head content
	 * 
	 * @return string		   Modified page head content
	 */
	function trx_addons_essential_grid_filter_head_output( $content = '' ) {
		if ( ! trx_addons_exists_essential_grid() ) {
			return $content;
		}
		return trx_addons_filter_head_output( 'essential_grid', $content, array(
			'check' => array(
				'#<link[^>]*href=[\'"][^\'"]*/essential-grid/[^>]*>#'
			)
		) );
	}
}

if ( ! function_exists( 'trx_addons_essential_grid_filter_body_output' ) ) {
	add_filter( 'trx_addons_filter_page_content', 'trx_addons_essential_grid_filter_body_output', 10, 1 );
	/**
	 * Remove plugin-specific styles and scripts from the page body if the plugin not need in the current page
	 * and the optimization mode 'Full' is used
	 * 
	 * @hooked trx_addons_filter_page_content
	 * 
	 * @param string $content  Page body content
	 * 
	 * @return string		   Modified page body content
	 */
	function trx_addons_essential_grid_filter_body_output( $content = '' ) {
		if ( ! trx_addons_exists_essential_grid() ) {
			return $content;
		}
		return trx_addons_filter_body_output( 'essential_grid', $content, array(
			'allow' => ! trx_addons_need_frontend_scripts( 'revslider' ),	// RevSlider may use some scripts from Essential Grid (tools.js)
			'check' => array(
				'#<link[^>]*href=[\'"][^\'"]*/essential-grid/[^>]*>#',
				'#<script[^>]*src=[\'"][^\'"]*/essential-grid/[^>]*>[\\s\\S]*</script>#U'
			)
		) );
	}
}


// Demo data install
//----------------------------------------------------------------------------

// One-click import support
if ( is_admin() ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'essential-grid/essential-grid-demo-importer.php';
}

// OCDI support
if ( is_admin() && trx_addons_exists_essential_grid() && function_exists( 'trx_addons_exists_ocdi' ) && trx_addons_exists_ocdi() ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'essential-grid/essential-grid-demo-ocdi.php';
}
