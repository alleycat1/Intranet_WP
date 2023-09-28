<?php
/* Elegro Crypto Payment support functions
------------------------------------------------------------------------------- */

// Theme init priorities:
// 9 - register other filters (for installer, etc.)
if ( ! function_exists( 'pubzinne_elegro_payment_theme_setup9' ) ) {
	add_action( 'after_setup_theme', 'pubzinne_elegro_payment_theme_setup9', 9 );
	function pubzinne_elegro_payment_theme_setup9() {
		if ( pubzinne_exists_elegro_payment() ) {
			add_action( 'wp_enqueue_scripts', 'pubzinne_elegro_payment_frontend_scripts', 1100 );
			add_filter( 'pubzinne_filter_merge_styles', 'pubzinne_elegro_payment_merge_styles' );
		}
		if ( is_admin() ) {
			add_filter( 'pubzinne_filter_tgmpa_required_plugins', 'pubzinne_elegro_payment_tgmpa_required_plugins' );
		}
	}
}

// Filter to add in the required plugins list
if ( ! function_exists( 'pubzinne_elegro_payment_tgmpa_required_plugins' ) ) {
	//Handler of the add_filter('pubzinne_filter_tgmpa_required_plugins',	'pubzinne_elegro_payment_tgmpa_required_plugins');
	function pubzinne_elegro_payment_tgmpa_required_plugins( $list = array() ) {
		if ( pubzinne_storage_isset( 'required_plugins', 'woocommerce' ) && pubzinne_storage_isset( 'required_plugins', 'elegro-payment' ) && pubzinne_storage_get_array( 'required_plugins', 'elegro-payment', 'install' ) !== false ) {
			$list[] = array(
				'name'     => pubzinne_storage_get_array( 'required_plugins', 'elegro-payment', 'title' ),
				'slug'     => 'elegro-payment',
				'required' => false,
			);
		}
		return $list;
	}
}

// Check if this plugin installed and activated
if ( ! function_exists( 'pubzinne_exists_elegro_payment' ) ) {
	function pubzinne_exists_elegro_payment() {
		return class_exists( 'WC_Elegro_Payment' );
	}
}


// Enqueue styles for frontend
if ( ! function_exists( 'pubzinne_elegro_payment_frontend_scripts' ) ) {
	//Handler of the add_action( 'wp_enqueue_scripts', 'pubzinne_elegro_payment_frontend_scripts', 1100 );
	function pubzinne_elegro_payment_frontend_scripts() {
		if ( pubzinne_is_on( pubzinne_get_theme_option( 'debug_mode' ) ) ) {
			$pubzinne_url = pubzinne_get_file_url( 'plugins/elegro-payment/elegro-payment.css' );
			if ( '' != $pubzinne_url ) {
				wp_enqueue_style( 'pubzinne-elegro-payment', $pubzinne_url, array(), null );
			}
		}
	}
}


// Merge custom styles
if ( ! function_exists( 'pubzinne_elegro_payment_merge_styles' ) ) {
	//Handler of the add_filter('pubzinne_filter_merge_styles', 'pubzinne_elegro_payment_merge_styles');
	function pubzinne_elegro_payment_merge_styles( $list ) {
		$list[] = 'plugins/elegro-payment/elegro-payment.css';
		return $list;
	}
}


// Add plugin-specific colors and fonts to the custom CSS
if ( pubzinne_exists_elegro_payment() ) {
	require_once pubzinne_get_file_dir( 'plugins/elegro-payment/elegro-payment-style.php' );
}
