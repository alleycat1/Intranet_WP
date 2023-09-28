<?php
/**
 * Plugin support: Content Timeline
 *
 * @package ThemeREX Addons
 * @since v1.0
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

if ( ! function_exists( 'trx_addons_exists_content_timeline' ) ) {
	/**
	 * Check if plugin 'Content Timeline' is installed and activated
	 * 
	 * @return bool  True if plugin is installed and activated
	 */
	function trx_addons_exists_content_timeline() {
		return class_exists( 'ContentTimelineAdmin' );
	}
}

if ( ! function_exists( 'trx_addons_get_list_content_timelines' ) ) {
	/**
	 * Return list of timelines
	 * 
	 * @param bool $prepend_inherit  Add inherit item in the beginning
	 * 
	 * @return array  List of timelines, where key is timeline ID and value is timeline title
	 */
	function trx_addons_get_list_content_timelines($prepend_inherit=false) {
		static $list = false;
		if ( $list === false ) {
			$list = array();
			if ( trx_addons_exists_content_timeline() ) {
				global $wpdb;
				$rows = $wpdb->get_results( "SELECT id, name FROM " . esc_sql($wpdb->prefix . 'ctimelines') );
				if ( is_array( $rows ) && count( $rows ) > 0 ) {
					foreach ( $rows as $row ) {
						$list[ $row->id ] = $row->name;
					}
				}
			}
		}
		return $prepend_inherit ? array_merge( array( 'inherit' => esc_html__( "Inherit", 'trx_addons' ) ), $list ) : $list;
	}
}

if ( ! function_exists( 'trx_addons_content_timeline_load_scripts_front' ) ) {
	add_action( "wp_enqueue_scripts", 'trx_addons_content_timeline_load_scripts_front', TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY );
	add_action( 'trx_addons_action_pagebuilder_preview_scripts', 'trx_addons_content_timeline_load_scripts_front', 10, 1 );
	/**
	 * Enqueue styles and scripts for frontend
	 * 
	 * @hooked wp_enqueue_scripts
	 * @hooked trx_addons_action_pagebuilder_preview_scripts
	 * 
	 * @param bool $force  Force enqueue scripts
	 */
	function trx_addons_content_timeline_load_scripts_front( $force = false ) {
		if ( ! trx_addons_exists_content_timeline() ) {
			return;
		}
		trx_addons_enqueue_optimized( 'content_timeline', $force, array(
			'check' => array(
				array( 'type' => 'sc',  'sc' => 'content_timeline' ),
				array( 'type' => 'elm', 'sc' => '"widgetType":"trx_sc_content_timeline"' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[content_timeline' ),
			)
		) );
	}
}

if ( ! function_exists( 'trx_addons_content_timeline_check_in_html_output' ) ) {
//	add_filter( 'trx_addons_filter_get_menu_cache_html', 'trx_addons_content_timeline_check_in_html_output', 10, 1 );
//	add_action( 'trx_addons_action_show_layout_from_cache', 'trx_addons_content_timeline_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_check_page_content', 'trx_addons_content_timeline_check_in_html_output', 10, 1 );
	/**
	 * Load styles and scripts if present in the page output
	 *
	 * @param string $content  Page content
	 * 
	 * @return string  Page content
	 */
	function trx_addons_content_timeline_check_in_html_output( $content = '' ) {
		if ( ! trx_addons_exists_content_timeline() ) {
			return $content;
		}
		$args = array(
			'check' => array(
				'class=[\'"][^\'"]*timeline ',
				'<!-- BEGIN TIMELINE -->'
			)
		);
		if ( trx_addons_check_in_html_output( 'content_timeline', $content, $args ) ) {
			trx_addons_content_timeline_load_scripts_front( true );
		}
		return $content;
	}
}


// Add shortcodes
//----------------------------------------------------------------------------

// Add shortcodes to Elementor
if ( trx_addons_exists_content_timeline() && trx_addons_exists_elementor() && function_exists('trx_addons_elm_init') ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'content_timeline/content_timeline-sc-elementor.php';
}

// Add shortcodes to VC
if ( trx_addons_exists_content_timeline() && trx_addons_exists_vc() && function_exists( 'trx_addons_vc_add_id_param' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'content_timeline/content_timeline-sc-vc.php';
}


// Demo data install
//----------------------------------------------------------------------------

// One-click import support
if ( is_admin() ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'content_timeline/content_timeline-demo-importer.php';
}

// OCDI support
if ( is_admin() && trx_addons_exists_content_timeline() && function_exists( 'trx_addons_exists_ocdi' ) && trx_addons_exists_ocdi() ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'content_timeline/content_timeline-demo-ocdi.php';
}
