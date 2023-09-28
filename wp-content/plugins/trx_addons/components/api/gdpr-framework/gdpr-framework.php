<?php
/**
 * Plugin support: The GDPR Framework
 *
 * @package ThemeREX Addons
 * @since v1.6.49
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

if ( ! function_exists( 'trx_addons_exists_gdpr_framework' ) ) {
	/**
	 * Check if plugin 'The GDPR Framework' is installed and activated
	 *
	 * @return bool  True, if plugin is installed and activated
	 */
	function trx_addons_exists_gdpr_framework() {
		return defined( 'GDPR_FRAMEWORK_VERSION' );
	}
}


// Demo data install
//----------------------------------------------------------------------------

// One-click import support
if ( is_admin() ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'gdpr-framework/gdpr-framework-demo-importer.php';
}

// OCDI support
if ( is_admin() && trx_addons_exists_gdpr_framework() && function_exists( 'trx_addons_exists_ocdi' ) && trx_addons_exists_ocdi() ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'gdpr-framework/gdpr-framework-demo-ocdi.php';
}
