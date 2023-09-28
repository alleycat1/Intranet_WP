<?php
/**
 * Plugin support: ThemeREX Donations (Importer support)
 *
 * @package ThemeREX Addons
 * @since v1.5
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

if ( ! function_exists( 'trx_addons_trx_donations_importer_required_plugins' ) ) {
	add_filter( 'trx_addons_filter_importer_required_plugins', 'trx_addons_trx_donations_importer_required_plugins', 10, 2 );
	/**
	 * Check if the required plugins are installed
	 * 
	 * @hooked trx_addons_filter_importer_required_plugins
	 *
	 * @param string $not_installed  Not installed plugins list
	 * @param string $list           List of the plugins to check
	 * 
	 * @return string                Not installed plugins list
	 */
	function trx_addons_trx_donations_importer_required_plugins( $not_installed = '', $list = '' ) {
		if ( strpos( $list, 'trx_donations' ) !== false && ! trx_addons_exists_trx_donations() ) {
			$not_installed .= '<br>' . esc_html__('trx_donations', 'trx_addons');
		}
		return $not_installed;
	}
}

if ( ! function_exists( 'trx_addons_trx_donations_importer_set_options' ) ) {
	add_filter( 'trx_addons_filter_importer_options', 'trx_addons_trx_donations_importer_set_options' );
	/**
	 * Set plugin's specific importer options
	 * 
	 * @hooked trx_addons_filter_importer_options
	 *
	 * @param array $options		Options to set
	 * 
	 * @return array				Modified options
	 */
	function trx_addons_trx_donations_importer_set_options( $options = array() ) {
		if ( trx_addons_exists_trx_donations() && in_array( 'trx_donations', $options['required_plugins'] ) ) {
			$options['additional_options'][] = 'trx_donations_options';
		}
		return $options;
	}
}

if ( ! function_exists( 'trx_addons_trx_donations_importer_check_options' ) ) {
	add_filter( 'trx_addons_filter_import_theme_options', 'trx_addons_trx_donations_importer_check_options', 10, 4 );
	/**
	 * Prevent to import plugin's specific options if plugin is not installed
	 * 
	 * @hooked trx_addons_filter_import_theme_options
	 *
	 * @param boolean $allow		Allow to import options or not
	 * @param string $k				Option name
	 * @param mixed $v				Option value
	 * @param array $options		Importer options
	 * 
	 * @return boolean				Allow to import or not
	 */
	function trx_addons_trx_donations_importer_check_options( $allow, $k, $v, $options ) {
		if ( $allow && $k == 'trx_donations_options' ) {
			$allow = trx_addons_exists_trx_donations() && in_array( 'trx_donations', $options['required_plugins'] );
		}
		return $allow;
	}
}

if ( ! function_exists( 'trx_addons_trx_donations_importer_check_row' ) ) {
	add_filter( 'trx_addons_filter_importer_import_row', 'trx_addons_trx_donations_importer_check_row', 9, 4 );
	/**
	 * Check if the row will be imported
	 * 
	 * @hooked trx_addons_filter_importer_import_row
	 *
	 * @param boolean $flag		Allow to import or not
	 * @param string $table		Table name
	 * @param array $row		Row data
	 * @param array $list		Required plugins list
	 * 
	 * @return boolean			Allow to import or not
	 */
	function trx_addons_trx_donations_importer_check_row( $flag, $table, $row, $list ) {
		if ( $flag || strpos( $list, 'trx_donations' ) === false ) {
			return $flag;
		}
		if ( trx_addons_exists_trx_donations() ) {
			if ( $table == 'posts' ) {
				$flag = $row['post_type'] == TRX_DONATIONS::POST_TYPE;
			}
		}
		return $flag;
	}
}
