<?php
/* Elegro Crypto Payment support functions
------------------------------------------------------------------------------- */

// Theme init priorities:
// 9 - register other filters (for installer, etc.)
if ( ! function_exists( 'pubzinne_restaurant_reservations_theme_setup9' ) ) {
    add_action( 'after_setup_theme', 'pubzinne_restaurant_reservations_theme_setup9', 9 );
    function pubzinne_restaurant_reservations_theme_setup9() {
        if ( is_admin() ) {
            add_filter( 'pubzinne_filter_tgmpa_required_plugins', 'pubzinne_restaurant_reservations_tgmpa_required_plugins' );
        }
    }
}

// Filter to add in the required plugins list
if ( ! function_exists( 'pubzinne_restaurant_reservations_tgmpa_required_plugins' ) ) {
    //Handler of the add_filter('pubzinne_filter_tgmpa_required_plugins',	'pubzinne_restaurant_reservations_tgmpa_required_plugins');
    function pubzinne_restaurant_reservations_tgmpa_required_plugins( $list = array() ) {
        if ( pubzinne_storage_isset( 'required_plugins', 'restaurant-reservations' ) && pubzinne_storage_get_array( 'required_plugins', 'restaurant-reservations', 'install' ) !== false ) {
            $list[] = array(
                'name'     => pubzinne_storage_get_array( 'required_plugins', 'restaurant-reservations', 'title' ),
                'slug'     => 'restaurant-reservations',
                'required' => false,
            );
        }
        return $list;
    }
}

// Check if this plugin installed and activated
if ( ! function_exists( 'pubzinne_exists_restaurant_reservations' ) ) {
    function pubzinne_exists_restaurant_reservations() {
        return class_exists( 'rtbInit' );
    }
}

