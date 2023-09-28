<?php
/**
 * Plugin support: Booked Appointments (Importer support)
 *
 * @package ThemeREX Addons
 * @since v1.5
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


if ( ! function_exists( 'trx_addons_booked_importer_required_plugins' ) ) {
	add_filter( 'trx_addons_filter_importer_required_plugins',	'trx_addons_booked_importer_required_plugins', 10, 2 );
	/**
	 * Check if Booked Appointments is installed and add it to the required plugins list
	 * 
	 * @hooked trx_addons_filter_importer_required_plugins
	 *
	 * @param string $not_installed Not installed plugins list
	 * @param string $list          Plugins list
	 * 
	 * @return string			    Not installed plugins list
	 */
	function trx_addons_booked_importer_required_plugins( $not_installed = '', $list = '' ) {
		if ( strpos( $list, 'booked' ) !== false && ! trx_addons_exists_booked() ) {
			$not_installed .= '<br>' . esc_html__('Booked Appointments', 'trx_addons');
		}
		return $not_installed;
	}
}

if ( ! function_exists( 'trx_addons_booked_importer_set_options' ) ) {
	add_filter( 'trx_addons_filter_importer_options', 'trx_addons_booked_importer_set_options', 10, 1 );
	/**
	 * Add plugin's specific options to the export options list
	 * 
	 * @hooked trx_addons_filter_importer_options
	 *
	 * @param array $options		Importer options
	 * 
	 * @return array				Modified options
	 */
	function trx_addons_booked_importer_set_options( $options = array() ) {
		if ( trx_addons_exists_booked() && in_array( 'booked', $options['required_plugins'] ) ) {
			$options['additional_options'][] = 'booked_%';				// Add slugs to export options of this plugin
		}
		return $options;
	}
}

if ( ! function_exists( 'trx_addons_booked_importer_check_options' ) ) {
	add_filter( 'trx_addons_filter_import_theme_options', 'trx_addons_booked_importer_check_options', 10, 4 );
	/**
	 * Prevent to import plugin's specific options if plugin is not installed
	 * 
	 * @hooked trx_addons_filter_import_theme_options
	 *
	 * @param boolean $allow		Allow import or not
	 * @param string  $k			Option name
	 * @param string  $v			Option value
	 * @param array   $list			List of required plugins
	 * 
	 * @return boolean				Allow import or not
	 */
	function trx_addons_booked_importer_check_options( $allow, $k, $v, $options ) {
		if ( $allow && strpos( $k, 'booked_' ) === 0 ) {
			$allow = trx_addons_exists_booked() && in_array( 'booked', $options['required_plugins'] );
		}
		return $allow;
	}
}

if ( ! function_exists( 'trx_addons_booked_importer_check_row' ) ) {
	add_filter( 'trx_addons_filter_importer_import_row', 'trx_addons_booked_importer_check_row', 9, 4 );
	/**
	 * Check if row will be imported to the table 'wp_posts'
	 * 
	 * @hooked trx_addons_filter_importer_import_row
	 *
	 * @param boolean $flag		Allow import or not
	 * @param string  $table	Table name
	 * @param array   $row		Row data
	 * @param array   $list		List of required plugins
	 * 
	 * @return boolean			Allow import or not
	 */
	function trx_addons_booked_importer_check_row( $flag, $table, $row, $list ) {
		if ( $flag || strpos( $list, 'booked' ) === false ) {
			return $flag;
		}
		if ( trx_addons_exists_booked() ) {
			if ( $table == 'posts' ) {
				$flag = $row['post_type'] == 'booked_appointments';
			}
		}
		return $flag;
	}
}

if ( ! function_exists( 'trx_addons_booked_export_options' ) ) {
	add_filter( 'trx_addons_filter_export_options', 'trx_addons_booked_export_options' );
	/**
	 * Export Booked Appointments options
	 *
	 * @param array $options		Options to export
	 * 
	 * @return array				Modified options
	 */
	function trx_addons_booked_export_options( $options ) {
		$options['booked_welcome_screen'] = false;
		return $options;
	}
}
