<?php
/**
 * Plugin support: ThemeREX Pop-Up
 *
 * @package ThemeREX Addons
 * @since v1.82.0
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


if ( ! function_exists( 'trx_addons_exists_trx_popup' ) ) {
	/**
	 * Check if ThemeREX Pop-Up is installed and activated
	 *
	 * @return bool  True if plugin is installed and activated
	 */
	function trx_addons_exists_trx_popup() {
		return defined('TRX_POPUP_URL');
	}
}

// Demo data install
//----------------------------------------------------------------------------

// One-click import support
if ( is_admin() ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'trx_popup/trx_popup-demo-importer.php';
}

// OCDI support
if ( is_admin() && trx_addons_exists_trx_popup() && function_exists( 'trx_addons_exists_ocdi' ) && trx_addons_exists_ocdi() ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'trx_popup/trx_popup-demo-ocdi.php';
}
