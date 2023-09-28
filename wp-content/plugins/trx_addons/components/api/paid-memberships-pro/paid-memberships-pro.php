<?php
/**
 * Plugin support: Paid Memberships Pro
 *
 * @package ThemeREX Addons
 * @since v2.24.0
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

if ( ! function_exists( 'trx_addons_exists_pmpro' ) ) {
	/**
	 * Check if plugin 'paid-memberships-pro' is installed and activated
	 *
	 * @return bool  True if plugin is installed and activated
	 */
	function trx_addons_exists_pmpro() {
		return defined( 'PMPRO_VERSION' ) && function_exists( 'pmpro_getAllLevels' );
	}
}

if ( ! function_exists( "trx_addons_pmpro_get_user_levels" ) ) {
	/**
	 * Return the list of the user levels for the plugin "Paid Memberships Pro"
	 *
	 * @return array  The list of the levels
	 */
	function trx_addons_pmpro_get_user_levels() {
		$levels = array();
		if ( trx_addons_exists_pmpro() ) {
			$levels = pmpro_getAllLevels( true, true );
		}
		return $levels;
	}
}

if ( ! function_exists( 'trx_addons_pmpro_list_user_levels' ) ) {
	add_filter( 'trx_addons_filter_sc_igenerator_list_user_levels', 'trx_addons_pmpro_list_user_levels', 10, 1 );
	add_filter( 'trx_addons_filter_sc_tgenerator_list_user_levels', 'trx_addons_pmpro_list_user_levels', 10, 1 );
	add_filter( 'trx_addons_filter_sc_chat_list_user_levels', 'trx_addons_pmpro_list_user_levels', 10, 1 );
	/**
	 * Add the list of the user levels for the plugin "Paid Memberships Pro" to the AI Helper
	 * 
	 * @hooked 'trx_addons_filter_sc_igenerator_list_user_levels'
	 * 
	 * @param array $list  The list of the levels
	 * 
	 * @return array  The modified list of the levels
	 */
	function trx_addons_pmpro_list_user_levels( $list ) {
		static $levels = false;
		if ( trx_addons_exists_pmpro() ) {
			if ( $levels === false ) {
				$levels = trx_addons_pmpro_get_user_levels();
			}
			if ( is_array( $levels ) && count( $levels ) > 0 ) {
				foreach( $levels as $level ) {
					$list[ $level->id ] = $level->name;
				}
			}
		}
		return $list;
	}
}

if ( ! function_exists( 'trx_addons_pmpro_get_user_level' ) ) {
	add_filter( 'trx_addons_filter_sc_igenerator_user_level', 'trx_addons_pmpro_get_user_level', 10, 2 );
	add_filter( 'trx_addons_filter_sc_tgenerator_user_level', 'trx_addons_pmpro_get_user_level', 10, 2 );
	add_filter( 'trx_addons_filter_sc_chat_user_level', 'trx_addons_pmpro_get_user_level', 10, 2 );
	/**
	 * Return the user level for the plugin "Paid Memberships Pro"
	 * 
	 * @hooked 'trx_addons_filter_ai_helper_get_user_level'
	 * 
	 * @param int $level  The current level
	 * @param int $user   The user ID
	 * 
	 * @return int  The modified level
	 */
	function trx_addons_pmpro_get_user_level( $level, $user ) {
		if ( trx_addons_exists_pmpro() ) {
			$levels = trx_addons_pmpro_get_user_levels();
			if ( is_array( $levels ) && count( $levels ) > 0 ) {
				foreach( $levels as $l ) {
					if ( pmpro_hasMembershipLevel( $l->id, $user ) ) {
						$level = $l->id;
						break;
					}
				}
			}
		}
		return $level;
	}
}
