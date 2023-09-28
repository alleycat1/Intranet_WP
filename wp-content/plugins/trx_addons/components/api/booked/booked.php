<?php
/**
 * Plugin support: Booked Appointments
 *
 * @package ThemeREX Addons
 * @since v1.5
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

if ( ! function_exists( 'trx_addons_exists_booked' ) ) {
	/**
	 * Check if Booked Appointments is installed and activated
	 *
	 * @return bool  True if plugin is installed and activated
	 */
	function trx_addons_exists_booked() {
		return class_exists( 'booked_plugin' );
	}
}

if ( ! function_exists( 'trx_addons_booked_disable_clear_update_info' ) ) {
	add_action( 'init', 'trx_addons_booked_disable_clear_update_info', 9999 );
	/**
	 * Disable clear update info for the Booked plugin.
	 * This plugin has a problem while checking for updates from its site and it clears update info
	 * and blocks the update process from our upgrade site.
	 */
	function trx_addons_booked_disable_clear_update_info() {
		if ( trx_addons_exists_booked() ) {
			trx_addons_remove_filter( 'site_transient_update_plugins', 'injectUpdate', 'PluginUpdateChecker_2_3' ); //WP 3.0+
			trx_addons_remove_filter( 'transient_update_plugins', 'injectUpdate', 'PluginUpdateChecker_2_3' );      //WP 2.8+
		}
	}
}

if ( ! function_exists( 'trx_addons_booked_create_empty_post_on_404' ) ) {
	add_action( 'wp_head', 'trx_addons_booked_create_empty_post_on_404', 1 );
	add_filter( 'display_post_states', 'trx_addons_booked_create_empty_post_on_404', 1 );
	/**
	 * Create empty post (global variable) on 404 page to prevent errors in the Booked plugin
	 *
	 * @param bool $states  Current state
	 * 
	 * @return bool  	    Not modified state
	 */
	function trx_addons_booked_create_empty_post_on_404($states=false) {
		if ( trx_addons_exists_booked() && ( is_404() || current_filter() == 'display_post_states' ) ) {
			// Create empty object 'post'
			if ( ! isset( $GLOBALS['post'] ) ) {
				$GLOBALS['post'] = new stdClass();
				$GLOBALS['post']->ID = 0;
				$GLOBALS['post']->post_type = 'unknown';
				$GLOBALS['post']->post_content = '';
			}
			// Add 'post_status' to the object 'post' if it not exists
			if ( ! isset( $GLOBALS['post']->post_status ) ) {
				$GLOBALS['post']->post_status = 'unknown';
			}
		}
		return $states;
	}
}
	
if ( ! function_exists( 'trx_addons_booked_not_defer_scripts' ) ) {
	add_filter( "trx_addons_filter_skip_move_scripts_down", 'trx_addons_booked_not_defer_scripts' );
	add_filter( "trx_addons_filter_skip_async_scripts_load", 'trx_addons_booked_not_defer_scripts' );
	/**
	 * Add plugin-specific slugs to the list of scripts that should not be deferred or loaded asynchronously
	 * 
	 * @hooked trx_addons_filter_skip_move_scripts_down
	 * @hooked trx_addons_filter_skip_async_scripts_load
	 *
	 * @param array $list List of scripts to skip defer/async
	 * 
	 * @return array      Modified list
	 */
	function trx_addons_booked_not_defer_scripts( $list ) {
		if ( trx_addons_exists_booked() ) {
			$list[] = 'spin.min.js';
		}
		return $list;
	}
}

if ( ! function_exists( 'trx_addons_booked_load_scripts_front' ) ) {
	add_action( "wp_enqueue_scripts", 'trx_addons_booked_load_scripts_front', TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY );
	add_action( 'trx_addons_action_pagebuilder_preview_scripts', 'trx_addons_booked_load_scripts_front', 10, 1 );
	/**
	 * Enqueue custom styles and scripts for Booked Appointments plugin on the front
	 * 
	 * @hooked wp_enqueue_scripts
	 * @hooked trx_addons_action_pagebuilder_preview_scripts
	 * 
	 * @trigger trx_addons_action_load_scripts_front
	 * 
	 * @param bool $force  Force load scripts
	 */
	function trx_addons_booked_load_scripts_front( $force = false ) {
		if ( ! trx_addons_exists_booked() ) {
			return;
		}
		trx_addons_enqueue_optimized( 'booked', $force, array(
			'check' => array(
				array( 'type' => 'sc',  'sc' => 'booked-calendar' ),
				array( 'type' => 'sc',  'sc' => 'booked-login' ),
				array( 'type' => 'sc',  'sc' => 'booked-profile' ),
				array( 'type' => 'sc',  'sc' => 'booked-appointments' ),
				//array( 'type' => 'gb',  'sc' => 'wp:trx-addons/events' ),	// This sc is not exists for GB
				array( 'type' => 'elm', 'sc' => '"widgetType":"wp-widget-booked_calendar"' ),
				array( 'type' => 'elm', 'sc' => '"widgetType":"trx_sc_booked_calendar"' ),
				array( 'type' => 'elm', 'sc' => '"widgetType":"trx_sc_booked_login"' ),
				array( 'type' => 'elm', 'sc' => '"widgetType":"trx_sc_booked_profile"' ),
				array( 'type' => 'elm', 'sc' => '"widgetType":"trx_sc_booked_appointments"' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[booked-' ),
)
		) );
	}
}

if ( ! function_exists( 'trx_addons_booked_check_in_html_output' ) ) {
//	add_filter( 'trx_addons_filter_get_menu_cache_html', 'trx_addons_booked_check_in_html_output', 10, 1 );
//	add_action( 'trx_addons_action_show_layout_from_cache', 'trx_addons_booked_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_check_page_content', 'trx_addons_booked_check_in_html_output', 10, 1 );
	/**
	 * Check if the output html contains specific shortcode and load required styles and scripts if found
	 * 
	 * @hooked trx_addons_action_check_page_content
	 *
	 * @param string $content   Page content
	 * 
	 * @return string  	   Page content
	 */
	function trx_addons_booked_check_in_html_output( $content = '' ) {
		if ( ! trx_addons_exists_booked() ) {
			return $content;
		}
		$args = array(
			'check' => array(
				'class=[\'"][^\'"]*booked-',
				'class=[\'"][^\'"]*type\\-booked_appointments',
				'class=[\'"][^\'"]*booked_custom_calendars\\-',
			)
		);
		if ( trx_addons_check_in_html_output( 'bbpress', $content, $args ) ) {
			trx_addons_booked_load_scripts_front( true );
		}
		return $content;
	}
}

if ( ! function_exists( 'trx_addons_booked_filter_head_output' ) ) {
	add_filter( 'trx_addons_filter_page_head', 'trx_addons_booked_filter_head_output', 10, 1 );
	/**
	 * Remove plugin-specific styles from the page head output if optimize CSS loading is 'full'
	 * 
	 * @hooked trx_addons_filter_page_head
	 *
	 * @param string $content   Page head content
	 * 
	 * @return string  	        Modified page head content
	 */
	function trx_addons_booked_filter_head_output( $content = '' ) {
		if ( ! trx_addons_exists_booked() ) {
			return $content;
		}
		return trx_addons_filter_head_output( 'booked', $content, array(
			'check' => array(
				'#<link[^>]*href=[\'"][^\'"]*/booked/[^>]*>#'
			)
		) );
	}
}

if ( ! function_exists( 'trx_addons_booked_filter_body_output' ) ) {
	add_filter( 'trx_addons_filter_page_content', 'trx_addons_booked_filter_body_output', 10, 1 );
	/**
	 * Remove plugin-specific styles from the page body output if optimize CSS loading is 'full'
	 * 
	 * @hooked trx_addons_filter_page_content
	 *
	 * @param string $content   Page body content
	 * 
	 * @return string  	        Modified page body content
	 */
	function trx_addons_booked_filter_body_output( $content = '' ) {
		if ( ! trx_addons_exists_booked() ) {
			return $content;
		}
		return trx_addons_filter_body_output( 'booked', $content, array(
			'check' => array(
				'#<link[^>]*href=[\'"][^\'"]*/booked/[^>]*>#',
				'#<script[^>]*src=[\'"][^\'"]*/booked/[^>]*>[\\s\\S]*</script>#U',
				'#<script[^>]*id=[\'"]booked-[^>]*>[\\s\\S]*</script>#U'
			)
		) );
	}
}


// Add shortcodes
//----------------------------------------------------------------------------

// Add shortcodes to Elementor
if ( trx_addons_exists_booked() && trx_addons_exists_elementor() && function_exists('trx_addons_elm_init') ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'booked/booked-sc-elementor.php';
}

// Add shortcodes to VC
if ( trx_addons_exists_booked() && trx_addons_exists_vc() && function_exists( 'trx_addons_vc_add_id_param' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'booked/booked-sc-vc.php';
}


// Demo data install
//----------------------------------------------------------------------------

// One-click import support
if ( is_admin() ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'booked/booked-demo-importer.php';
}

// OCDI support
if ( is_admin() && trx_addons_exists_booked() && function_exists( 'trx_addons_exists_ocdi' ) && trx_addons_exists_ocdi() ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'booked/booked-demo-ocdi.php';
}
