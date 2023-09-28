<?php
/* ThemeREX Updater support functions
------------------------------------------------------------------------------- */


// Theme init priorities:
// 9 - register other filters (for installer, etc.)
if ( ! function_exists( 'pubzinne_trx_updater_theme_setup9' ) ) {
	add_action( 'after_setup_theme', 'pubzinne_trx_updater_theme_setup9', 9 );
	function pubzinne_trx_updater_theme_setup9() {
		if ( is_admin() ) {
			add_filter( 'pubzinne_filter_tgmpa_required_plugins', 'pubzinne_trx_updater_tgmpa_required_plugins', 8 );
		}
	}
}

// Filter to add in the required plugins list
// Priority 8 is used to add this plugin before all other plugins
if ( ! function_exists( 'pubzinne_trx_updater_tgmpa_required_plugins' ) ) {
	//Handler of the add_filter( 'pubzinne_filter_tgmpa_required_plugins',	'pubzinne_trx_updater_tgmpa_required_plugins', 8 );
	function pubzinne_trx_updater_tgmpa_required_plugins( $list = array() ) {
		if ( pubzinne_storage_isset( 'required_plugins', 'trx_updater' ) && pubzinne_storage_get_array( 'required_plugins', 'trx_updater', 'install' ) !== false && pubzinne_is_theme_activated() ) {
			$path = pubzinne_get_plugin_source_path( 'plugins/trx_updater/trx_updater.zip' );
			if ( ! empty( $path ) || pubzinne_get_theme_setting( 'tgmpa_upload' ) ) {
				$list[] = array(
					'name'     => pubzinne_storage_get_array( 'required_plugins', 'trx_updater', 'title' ),
					'slug'     => 'trx_updater',
					'source'   => ! empty( $path ) ? $path : 'upload://trx_updater.zip',
					'version'  => '2.0.0',
					'required' => false,
				);
			}
		}
		return $list;
	}
}

// Check if plugin installed and activated
if ( ! function_exists( 'pubzinne_exists_trx_updater' ) ) {
	function pubzinne_exists_trx_updater() {
		return defined( 'TRX_UPDATER_VERSION' );
	}
}
