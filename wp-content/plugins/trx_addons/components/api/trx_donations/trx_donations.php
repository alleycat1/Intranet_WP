<?php
/**
 * Plugin support: ThemeREX Donations
 *
 * @package ThemeREX Addons
 * @since v1.5
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

if ( ! function_exists( 'trx_addons_exists_trx_donations' ) ) {
	/**
	 * Check if ThemeREX Donations is installed and activated
	 *
	 * @return bool  True if plugin is installed and activated
	 */
	function trx_addons_exists_trx_donations() {
		return class_exists('TRX_DONATIONS');
	}
}

if ( ! function_exists( 'trx_addons_is_trx_donations_page' ) ) {
	/**
	 * Check if current page is any ThemeREX Donations page
	 *
	 * @return bool  True if current page is any ThemeREX Donations page
	 */
	function trx_addons_is_trx_donations_page() {
		$rez = false;
		if ( trx_addons_exists_trx_donations() ) {
			$rez = ( trx_addons_is_single() && get_query_var('post_type') == TRX_DONATIONS::POST_TYPE ) 
					|| is_post_type_archive( TRX_DONATIONS::POST_TYPE )
					|| is_tax( TRX_DONATIONS::TAXONOMY );
		}
		return $rez;
	}
}


// Add shortcodes
//----------------------------------------------------------------------------

// Add shortcodes to Elementor
if ( trx_addons_exists_trx_donations() && trx_addons_exists_elementor() && function_exists('trx_addons_elm_init') ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'trx_donations/trx_donations-sc-elementor.php';
}

// Add shortcodes to VC
if ( trx_addons_exists_trx_donations() && trx_addons_exists_vc() && function_exists( 'trx_addons_vc_add_id_param' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'trx_donations/trx_donations-sc-vc.php';
}


// Demo data install
//----------------------------------------------------------------------------

// One-click import support
if ( is_admin() ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'trx_donations/trx_donations-demo-importer.php';
}

// OCDI support
if ( is_admin() && trx_addons_exists_trx_donations() && function_exists( 'trx_addons_exists_ocdi' ) && trx_addons_exists_ocdi() ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'trx_donations/trx_donations-demo-ocdi.php';
}
