<?php
/**
 * Plugin support: Mail Chimp
 *
 * @package ThemeREX Addons
 * @since v1.5
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

if ( ! defined( 'TRX_ADDONS_MC4FORM_REPLACE_AUTO_SCROLL' ) ) define( 'TRX_ADDONS_MC4FORM_REPLACE_AUTO_SCROLL', true );

if ( ! function_exists( 'trx_addons_exists_mailchimp' ) ) {
	/**
	 * Check if MailChimp plugin is installed and activated
	 *
	 * @return bool  true if plugin is installed and activated
	 */
	function trx_addons_exists_mailchimp() {
		return function_exists('__mc4wp_load_plugin') || defined('MC4WP_VERSION');
	}
}

if ( ! function_exists( 'trx_addons_mailchimp_scroll_to_form' ) ) {
	add_filter( 'mc4wp_form_auto_scroll', 'trx_addons_mailchimp_scroll_to_form' );
	/**
	 * Disable auto scroll to form because it broke layout in the Chrome
	 *
	 * @param bool $scroll  true - scroll to form, false - don't scroll
	 *
	 * @return bool  	 false - don't scroll
	 */
	function trx_addons_mailchimp_scroll_to_form( $scroll ) {
		return ! TRX_ADDONS_MC4FORM_REPLACE_AUTO_SCROLL;
	}
}

if ( ! function_exists( 'trx_addons_mailchimp_load_scripts_front' ) ) {
	add_action( "wp_enqueue_scripts", 'trx_addons_mailchimp_load_scripts_front', TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY );
	add_action( 'trx_addons_action_pagebuilder_preview_scripts', 'trx_addons_mailchimp_load_scripts_front', 10, 1 );
	/**
	 * Enqueue custom styles and scripts for MailChimp
	 * 
	 * @hooked wp_enqueue_scripts
	 * @hooked trx_addons_action_pagebuilder_preview_scripts
	 * 
	 * @trigger trx_addons_action_load_scripts_front
	 * 
	 * @param bool $force  true - load scripts always, false - load only if plugin installed and activated 
	 * 					   and its shortcode is present in the page content
	 */
	function trx_addons_mailchimp_load_scripts_front( $force = false ) {
		if ( ! trx_addons_exists_mailchimp() ) {
			return;
		}
		trx_addons_enqueue_optimized( 'mailchimp', $force, array(
			'js' => array(
				'trx_addons-mailchimp' => array( 'src' => TRX_ADDONS_PLUGIN_API . 'mailchimp-for-wp/mailchimp-for-wp.js', 'deps' => 'jquery' ),
			),
			'check' => array(
				array( 'type' => 'sc',  'sc' => 'mc4wp_form' ),
				array( 'type' => 'sc',  'sc' => 'mc4wp_checkbox' ),
				array( 'type' => 'gb',  'sc' => 'wp:mailchimp-for-wp/form' ),
				array( 'type' => 'elm', 'sc' => '"widgetType":"wp-widget-mc4wp_form"' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[mc4wp_form' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[mc4wp_checkbox' ),
			)
		) );
	}
}

if ( ! function_exists( 'trx_addons_mailchimp_merge_scripts' ) ) {
	add_action( "trx_addons_filter_merge_scripts", 'trx_addons_mailchimp_merge_scripts' );
	/**
	 * Merge custom scripts from this plugin to the single file
	 *
	 * @param array $list  List of JS files to merge.
	 * 					   Key - a file path, value - flag to merge this file both large files:
	 * 					   with all scripts (used in the preview mode) and with some scripts, who are not loaded
	 * 					   separately when an option 'Optimize CSS/JS loading' is on (used in the front mode)
	 * 
	 * @return array       Modified list
	 */
	function trx_addons_mailchimp_merge_scripts( $list ) {
		if ( trx_addons_exists_mailchimp() ) {
			$list[ TRX_ADDONS_PLUGIN_API . 'mailchimp-for-wp/mailchimp-for-wp.js' ] = false;
		}
		return $list;
	}
}

if ( ! function_exists( 'trx_addons_mailchimp_localize_script' ) ) {
	add_filter( "trx_addons_filter_localize_script", 'trx_addons_mailchimp_localize_script' );
	/**
	 * Add Mailchimp specific vars to the localize array for the frontend scripts
	 * 
	 * @hooked trx_addons_filter_localize_script
	 * 
	 * @param array $vars List of vars
	 * 
	 * @return array    List of vars
	 */
	function trx_addons_mailchimp_localize_script( $vars ) {
		$vars['animate_to_mc4wp_form_submitted'] = TRX_ADDONS_MC4FORM_REPLACE_AUTO_SCROLL;
		return $vars;
	}
}

if ( ! function_exists( 'trx_addons_mailchimp_check_in_html_output' ) ) {
	add_filter( 'trx_addons_filter_get_menu_cache_html', 'trx_addons_mailchimp_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_show_layout_from_cache', 'trx_addons_mailchimp_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_check_page_content', 'trx_addons_mailchimp_check_in_html_output', 10, 1 );
	/**
	 * Check if the page output, a menu cache or a layout cache contains data from the MailChimp
	 * and force to load scripts and styles
	 * 
	 * @hooked trx_addons_filter_get_menu_cache_html
	 * @hooked trx_addons_action_show_layout_from_cache
	 * @hooked trx_addons_action_check_page_content
	 *
	 * @param string $content   Content to check
	 * 
	 * @return string           Checked content
	 */
	function trx_addons_mailchimp_check_in_html_output( $content = '' ) {
		if ( ! trx_addons_exists_mailchimp() ) {
			return $content;
		}
		$args = array(
			'check' => array(
				'class=[\'"][^\'"]*mc4wp',
				'id=[\'"][^\'"]*mc4wp'
			)
		);
		if ( trx_addons_check_in_html_output( 'mailchimp', $content, $args ) ) {
			trx_addons_mailchimp_load_scripts_front( true );
		}
		return $content;
	}
}


// Demo data install
//----------------------------------------------------------------------------

// One-click import support
if ( is_admin() ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'mailchimp-for-wp/mailchimp-for-wp-demo-importer.php';
}

// OCDI support
if ( is_admin() && trx_addons_exists_mailchimp() && function_exists( 'trx_addons_exists_ocdi' ) && trx_addons_exists_ocdi() ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'mailchimp-for-wp/mailchimp-for-wp-demo-ocdi.php';
}
