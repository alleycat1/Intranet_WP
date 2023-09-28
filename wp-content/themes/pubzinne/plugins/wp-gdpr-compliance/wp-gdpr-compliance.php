<?php
/* Cookie Information support functions
------------------------------------------------------------------------------- */

// Theme init priorities:
// 9 - register other filters (for installer, etc.)
if ( ! function_exists( 'pubzinne_wp_gdpr_compliance_theme_setup9' ) ) {
	add_action( 'after_setup_theme', 'pubzinne_wp_gdpr_compliance_theme_setup9', 9 );
	function pubzinne_wp_gdpr_compliance_theme_setup9() {
		if ( is_admin() ) {
			add_filter( 'pubzinne_filter_tgmpa_required_plugins', 'pubzinne_wp_gdpr_compliance_tgmpa_required_plugins' );
		}
	}
}

// Filter to add in the required plugins list
if ( ! function_exists( 'pubzinne_wp_gdpr_compliance_tgmpa_required_plugins' ) ) {
	//Handler of the add_filter('pubzinne_filter_tgmpa_required_plugins',	'pubzinne_wp_gdpr_compliance_tgmpa_required_plugins');
	function pubzinne_wp_gdpr_compliance_tgmpa_required_plugins( $list = array() ) {
		if ( pubzinne_storage_isset( 'required_plugins', 'wp-gdpr-compliance' ) && pubzinne_storage_get_array( 'required_plugins', 'wp-gdpr-compliance', 'install' ) !== false ) {
			$list[] = array(
				'name'     => pubzinne_storage_get_array( 'required_plugins', 'wp-gdpr-compliance', 'title' ),
				'slug'     => 'wp-gdpr-compliance',
				'required' => false,
			);
		}
		return $list;
	}
}

// Check if this plugin installed and activated
if ( ! function_exists( 'pubzinne_exists_wp_gdpr_compliance' ) ) {
	function pubzinne_exists_wp_gdpr_compliance() {
		return defined( 'WP_GDPR_C_ROOT_FILE' ) || defined( 'WPGDPRC_ROOT_FILE' );
	}
}
