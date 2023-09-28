<?php
/* Instagram Feed support functions
------------------------------------------------------------------------------- */

// Theme init priorities:
// 9 - register other filters (for installer, etc.)
if ( ! function_exists( 'pubzinne_instagram_feed_theme_setup9' ) ) {
	add_action( 'after_setup_theme', 'pubzinne_instagram_feed_theme_setup9', 9 );
	function pubzinne_instagram_feed_theme_setup9() {
		if ( is_admin() ) {
			add_filter( 'pubzinne_filter_tgmpa_required_plugins', 'pubzinne_instagram_feed_tgmpa_required_plugins' );
		}
	}
}

// Filter to add in the required plugins list
if ( ! function_exists( 'pubzinne_instagram_feed_tgmpa_required_plugins' ) ) {
	//Handler of the add_filter('pubzinne_filter_tgmpa_required_plugins',	'pubzinne_instagram_feed_tgmpa_required_plugins');
	function pubzinne_instagram_feed_tgmpa_required_plugins( $list = array() ) {
		if ( pubzinne_storage_isset( 'required_plugins', 'instagram-feed' ) && pubzinne_storage_get_array( 'required_plugins', 'instagram-feed', 'install' ) !== false ) {
			$list[] = array(
				'name'     => pubzinne_storage_get_array( 'required_plugins', 'instagram-feed', 'title' ),
				'slug'     => 'instagram-feed',
				'required' => false,
			);
		}
		return $list;
	}
}

// Check if Instagram Feed installed and activated
if ( ! function_exists( 'pubzinne_exists_instagram_feed' ) ) {
	function pubzinne_exists_instagram_feed() {
		return defined( 'SBIVER' );
	}
}


