<?php
/**
 * Plugin support: PowerKit
 *
 * @package ThemeREX Addons
 * @since v1.75.3
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

if ( ! function_exists( 'trx_addons_exists_powerkit' ) ) {
	/**
	 * Check if PowerKit plugin is installed and activated
	 *
	 * @return bool  True if plugin is installed and activated
	 */
	function trx_addons_exists_powerkit() {
		return class_exists( 'Powerkit' );
	}
}


// Demo data install
//----------------------------------------------------------------------------

// One-click import support
if ( is_admin() ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'powerkit/powerkit-demo-importer.php';
}

// OCDI support
if ( is_admin() && trx_addons_exists_powerkit() && function_exists( 'trx_addons_exists_ocdi' ) && trx_addons_exists_ocdi() ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'powerkit/powerkit-demo-ocdi.php';
}
