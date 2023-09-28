<?php
/**
 * Plugin support: WPBakery PageBuilder Extensions Bundle
 *
 * @package ThemeREX Addons
 * @since v1.5
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

if ( ! function_exists( 'trx_addons_exists_vc_extensions' ) ) {
	/**
	 * Check if WPBakery PageBuilder Extensions Bundle is installed and activated
	 *
	 * @return bool  True if WPBakery PageBuilder Extensions Bundle is installed and activated
	 */
	function trx_addons_exists_vc_extensions() {
		return class_exists('Vc_Manager') && class_exists('VC_Extensions_CQBundle');
	}
}


// Demo data install
//----------------------------------------------------------------------------

// One-click import support
if ( is_admin() ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'vc-extensions-bundle/vc-extensions-bundle-demo-importer.php';
}
