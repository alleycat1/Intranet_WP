<?php
/* Contact Form 7 support functions
------------------------------------------------------------------------------- */

// Theme init priorities:
// 9 - register other filters (for installer, etc.)
if ( ! function_exists( 'pubzinne_cf7_theme_setup9' ) ) {
	add_action( 'after_setup_theme', 'pubzinne_cf7_theme_setup9', 9 );
	function pubzinne_cf7_theme_setup9() {
		if ( pubzinne_exists_cf7() ) {
			add_action( 'wp_enqueue_scripts', 'pubzinne_cf7_frontend_scripts', 1100 );
			add_filter( 'pubzinne_filter_merge_scripts', 'pubzinne_cf7_merge_scripts' );
		}
		if ( is_admin() ) {
			add_filter( 'pubzinne_filter_tgmpa_required_plugins', 'pubzinne_cf7_tgmpa_required_plugins' );
			add_filter( 'pubzinne_filter_theme_plugins', 'pubzinne_cf7_theme_plugins' );
		}
	}
}

// Filter to add in the required plugins list
if ( ! function_exists( 'pubzinne_cf7_tgmpa_required_plugins' ) ) {
	//Handler of the add_filter('pubzinne_filter_tgmpa_required_plugins',	'pubzinne_cf7_tgmpa_required_plugins');
	function pubzinne_cf7_tgmpa_required_plugins( $list = array() ) {
		if ( pubzinne_storage_isset( 'required_plugins', 'contact-form-7' ) && pubzinne_storage_get_array( 'required_plugins', 'contact-form-7', 'install' ) !== false ) {
			// CF7 plugin
			$list[] = array(
				'name'     => pubzinne_storage_get_array( 'required_plugins', 'contact-form-7', 'title' ),
				'slug'     => 'contact-form-7',
				'required' => false,
			);
		}
		return $list;
	}
}

// Filter theme-supported plugins list
if ( ! function_exists( 'pubzinne_cf7_theme_plugins' ) ) {
	//Handler of the add_filter( 'pubzinne_filter_theme_plugins', 'pubzinne_cf7_theme_plugins' );
	function pubzinne_cf7_theme_plugins( $list = array() ) {
		if ( ! empty( $list['contact-form-7']['group'] ) ) {
			foreach ( $list as $k => $v ) {
				if ( substr( $k, 0, 15 ) == 'contact-form-7-' ) {
					if ( empty( $v['group'] ) ) {
						$list[ $k ]['group'] = $list['contact-form-7']['group'];
					}
					if ( empty( $v['logo'] ) ) {
						$logo = pubzinne_get_file_url( "plugins/contact-form-7/{$k}.png" );
						$list[ $k ]['logo'] = empty( $logo )
												? ( ! empty( $list['contact-form-7']['logo'] )
													? ( strpos( $list['contact-form-7']['logo'], '//' ) !== false
														? $list['contact-form-7']['logo']
														: pubzinne_get_file_url( "plugins/contact-form-7/{$list['contact-form-7']['logo']}" )
														)
													: ''
													)
												: $logo;
					}
				}
			}
		}
		return $list;
	}
}

if ( ! function_exists( 'pubzinne_cf7_autop_or_not' ) ) {
    add_filter( 'wpcf7_autop_or_not', 'pubzinne_cf7_autop_or_not' );
    function pubzinne_cf7_autop_or_not() {
        return false;
    }
}

// Check if cf7 installed and activated
if ( ! function_exists( 'pubzinne_exists_cf7' ) ) {
	function pubzinne_exists_cf7() {
		return class_exists( 'WPCF7' );
	}
}

// Enqueue custom scripts
if ( ! function_exists( 'pubzinne_cf7_frontend_scripts' ) ) {
	//Handler of the add_action( 'wp_enqueue_scripts', 'pubzinne_cf7_frontend_scripts', 1100 );
	function pubzinne_cf7_frontend_scripts() {
		if ( pubzinne_is_on( pubzinne_get_theme_option( 'debug_mode' ) ) ) {
			$pubzinne_url = pubzinne_get_file_url( 'plugins/contact-form-7/contact-form-7.js' );
			if ( '' != $pubzinne_url ) {
				wp_enqueue_script( 'pubzinne-contact-form-7', $pubzinne_url, array( 'jquery' ), null, true );
			}
		}
	}
}

// Merge custom scripts
if ( ! function_exists( 'pubzinne_cf7_merge_scripts' ) ) {
	//Handler of the add_filter('pubzinne_filter_merge_scripts', 'pubzinne_cf7_merge_scripts');
	function pubzinne_cf7_merge_scripts( $list ) {
		$list[] = 'plugins/contact-form-7/contact-form-7.js';
		return $list;
	}
}
