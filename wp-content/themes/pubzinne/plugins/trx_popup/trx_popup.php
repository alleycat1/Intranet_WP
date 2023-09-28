<?php
/* ThemeREX Popup support functions
------------------------------------------------------------------------------- */


// Theme init priorities:
// 9 - register other filters (for installer, etc.)
if ( ! function_exists( 'pubzinne_trx_popup_theme_setup9' ) ) {
	add_action( 'after_setup_theme', 'pubzinne_trx_popup_theme_setup9', 9 );
	function pubzinne_trx_popup_theme_setup9() {
		if ( is_admin() ) {
			add_filter( 'pubzinne_filter_tgmpa_required_plugins', 'pubzinne_trx_popup_tgmpa_required_plugins' );
		}
	}
}

// Filter to add in the required plugins list
if ( ! function_exists( 'pubzinne_trx_popup_tgmpa_required_plugins' ) ) {
	//Handler of the add_filter( 'pubzinne_filter_tgmpa_required_plugins',	'pubzinne_trx_popup_tgmpa_required_plugins' );
	function pubzinne_trx_popup_tgmpa_required_plugins( $list = array() ) {
		if ( pubzinne_storage_isset( 'required_plugins', 'trx_popup' ) && pubzinne_storage_get_array( 'required_plugins', 'trx_popup', 'install' ) !== false && pubzinne_is_theme_activated() ) {
			$path = pubzinne_get_plugin_source_path( 'plugins/trx_popup/trx_popup.zip' );
			if ( ! empty( $path ) || pubzinne_get_theme_setting( 'tgmpa_upload' ) ) {
				$list[] = array(
					'name'     => pubzinne_storage_get_array( 'required_plugins', 'trx_popup', 'title' ),
					'slug'     => 'trx_popup',
					'source'   => ! empty( $path ) ? $path : 'upload://trx_popup.zip',
					'version'  => '1.1.3',
					'required' => false,
				);
			}
		}
		return $list;
	}
}

// Check if plugin installed and activated
if ( ! function_exists( 'pubzinne_exists_trx_popup' ) ) {
	function pubzinne_exists_trx_popup() {
		return defined( 'TRX_POPUP_URL' );
	}
}
