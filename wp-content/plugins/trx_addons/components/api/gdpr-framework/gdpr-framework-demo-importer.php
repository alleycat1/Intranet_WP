<?php
/**
 * Plugin support: The GDPR Framework (Importer support)
 *
 * @package ThemeREX Addons
 * @since v1.6.49
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

if ( ! function_exists( 'trx_addons_gdpr_framework_importer_required_plugins' ) ) {
	add_filter( 'trx_addons_filter_importer_required_plugins',	'trx_addons_gdpr_framework_importer_required_plugins', 10, 2 );
	/**
	 * Check if the required plugins are installed
	 * 
	 * @hooked trx_addons_filter_importer_required_plugins
	 *
	 * @param string $not_installed  Not installed plugins list
	 * @param string $list           Required plugins list
	 * 
	 * @return string                Not installed plugins list
	 */
	function trx_addons_gdpr_framework_importer_required_plugins( $not_installed = '', $list = '' ) {
		if ( strpos( $list, 'gdpr-framework' ) !== false && ! trx_addons_exists_gdpr_framework() ) {
			$not_installed .= '<br>' . esc_html__('The GDPR Framework', 'trx_addons');
		}
		return $not_installed;
	}
}

if ( ! function_exists( 'trx_addons_gdpr_framework_importer_set_options' ) ) {
	add_filter( 'trx_addons_filter_importer_options',	'trx_addons_gdpr_framework_importer_set_options' );
	/**
	 * Add plugin's specific options to the list for export
	 * 
	 * @hooked trx_addons_filter_importer_options
	 *
	 * @param array $options		Options to export
	 * 
	 * @return array				Modified options
	 */
	function trx_addons_gdpr_framework_importer_set_options( $options = array() ) {
		if ( trx_addons_exists_gdpr_framework() && in_array( 'gdpr-framework', $options['required_plugins'] ) ) {
			if ( is_array( $options ) ) {
				$options['additional_options'][] = 'gdpr_%';
			}
		}
		return $options;
	}
}

if ( ! function_exists( 'trx_addons_gdpr_framework_importer_check_options' ) ) {
	add_filter( 'trx_addons_filter_import_theme_options', 'trx_addons_gdpr_framework_importer_check_options', 10, 4 );
	/**
	 * Check if the row will be imported.
	 * Prevent import plugin's specific options if plugin is not installed
	 * 
	 * @hooked trx_addons_filter_import_theme_options
	 *
	 * @param boolean $allow		Allow import or not
	 * @param string $k				Option name
	 * @param string $v				Option value
	 * @param array $options		Importer options
	 * 
	 * @return boolean				Allow import or not
	 */
	function trx_addons_gdpr_framework_importer_check_options( $allow, $k, $v, $options ) {
		if ( $allow && strpos( $k, 'gdpr_' ) === 0 ) {
			$allow = trx_addons_exists_gdpr_framework() && in_array( 'gdpr-framework', $options['required_plugins'] );
		}
		return $allow;
	}
}
