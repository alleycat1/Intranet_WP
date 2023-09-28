<?php
/* Revolution Slider support functions
------------------------------------------------------------------------------- */

// Theme init priorities:
// 9 - register other filters (for installer, etc.)
if ( ! function_exists( 'pubzinne_revslider_theme_setup9' ) ) {
	add_action( 'after_setup_theme', 'pubzinne_revslider_theme_setup9', 9 );
	function pubzinne_revslider_theme_setup9() {
		if ( is_admin() ) {
			add_filter( 'pubzinne_filter_tgmpa_required_plugins', 'pubzinne_revslider_tgmpa_required_plugins' );
		}
	}
}

// Filter to add in the required plugins list
if ( ! function_exists( 'pubzinne_revslider_tgmpa_required_plugins' ) ) {
	//Handler of the add_filter('pubzinne_filter_tgmpa_required_plugins',	'pubzinne_revslider_tgmpa_required_plugins');
	function pubzinne_revslider_tgmpa_required_plugins( $list = array() ) {
		if ( pubzinne_storage_isset( 'required_plugins', 'revslider' ) && pubzinne_storage_get_array( 'required_plugins', 'revslider', 'install' ) !== false && pubzinne_is_theme_activated() ) {
			$path = pubzinne_get_plugin_source_path( 'plugins/revslider/revslider.zip' );
			if ( ! empty( $path ) || pubzinne_get_theme_setting( 'tgmpa_upload' ) ) {
				$list[] = array(
					'name'     => pubzinne_storage_get_array( 'required_plugins', 'revslider', 'title' ),
					'slug'     => 'revslider',
					'source'   => ! empty( $path ) ? $path : 'upload://revslider.zip',
					'version'  => '6.6.8',
					'required' => false,
				);
			}
		}
		return $list;
	}
}

// Check if RevSlider installed and activated
if ( ! function_exists( 'pubzinne_exists_revslider' ) ) {
	function pubzinne_exists_revslider() {
		return function_exists( 'rev_slider_shortcode' );
	}
}
