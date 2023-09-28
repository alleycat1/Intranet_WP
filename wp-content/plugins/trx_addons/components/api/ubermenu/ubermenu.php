<?php
/**
 * Plugin support: Uber Menu
 *
 * @package ThemeREX Addons
 * @since v1.5
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

if ( ! function_exists( 'trx_addons_exists_ubermenu' ) ) {
	/**
	 * Check if UberMenu is installed and activated
	 *
	 * @return bool  True if UberMenu is installed and activated
	 */
	function trx_addons_exists_ubermenu() {
		return class_exists('UberMenu');
	}
}
	
if ( ! function_exists( 'trx_addons_ubermenu_check_location' ) ) {
	/**
	 * Check if a menu location is assigned to UberMenu
	 *
	 * @param string $loc  Menu location
	 * @return bool  True if menu location is assigned to UberMenu
	 */
	function trx_addons_ubermenu_check_location($loc) {
		$rez = false;
		if ( trx_addons_exists_ubermenu() ) {
			$theme_loc = ubermenu_op( 'auto_theme_location', 'main' );
			$rez = ! empty( $theme_loc[ $loc ] );
		}
		return $rez;
	}
}

if ( ! function_exists( 'trx_addons_ubermenu_is_complex_menu' ) ) {
	add_filter( 'trx_addons_filter_is_complex_menu', 'trx_addons_ubermenu_is_complex_menu', 10, 2 );
	/**
	 * Check if a menu location is assigned to UberMenu
	 * 
	 * @hooked trx_addons_filter_is_complex_menu
	 *
	 * @param bool $rez    Is complex menu?
	 * @param string $loc  Menu location
	 * 
	 * @return bool        True if menu location is assigned to UberMenu
	 */
	function trx_addons_ubermenu_is_complex_menu($rez, $loc) {
		return $rez || trx_addons_ubermenu_check_location( $loc );
	}
}

if ( ! function_exists( 'trx_addons_ubermenu_use_menu_cache' ) ) {
	add_filter( 'trx_addons_add_menu_cache', 'trx_addons_ubermenu_use_menu_cache' );
	add_filter( 'trx_addons_get_menu_cache', 'trx_addons_ubermenu_use_menu_cache' );
	/**
	 * Disable cache for UberMenu
	 * 
	 * @param bool $use    Use cache?
	 * @param array $args  Additional arguments
	 * 
	 * @return bool       True if cache can be used
	 */
	function trx_addons_ubermenu_use_menu_cache( $use, $args = array() ) {
		if ( ! empty( $args['location'] ) && trx_addons_ubermenu_check_location( $args['location'] ) ) {
			$use = false;
		}
		return $use;
	}
}



// Demo data install
//----------------------------------------------------------------------------

// One-click import support
if ( is_admin() ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'ubermenu/ubermenu-demo-importer.php';
}

// OCDI support
if ( is_admin() && trx_addons_exists_ubermenu() && function_exists( 'trx_addons_exists_ocdi' ) && trx_addons_exists_ocdi() ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'ubermenu/ubermenu-demo-ocdi.php';
}
