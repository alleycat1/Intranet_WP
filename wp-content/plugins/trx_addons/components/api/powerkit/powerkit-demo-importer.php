<?php
/**
 * Plugin support: PowerKit (Importer supoort)
 *
 * @package ThemeREX Addons
 * @since v1.75.3
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


if ( ! function_exists( 'trx_addons_powerkit_importer_required_plugins' ) ) {
	add_filter( 'trx_addons_filter_importer_required_plugins',	'trx_addons_powerkit_importer_required_plugins', 10, 2 );
	/**
	 * Check if the required plugins are installed before import start
	 * 
	 * @hooked trx_addons_filter_importer_required_plugins
	 *
	 * @param string $not_installed  Not installed plugins list as HTML string
	 * @param string $list           List of the plugins to check
	 * 
	 * @return string                Modified not installed plugins list
	 */
	function trx_addons_powerkit_importer_required_plugins( $not_installed = '', $list = '' ) {
		if ( strpos( $list, 'powerkit' ) !== false && ! trx_addons_exists_powerkit() ) {
			$not_installed .= '<br>' . esc_html__('PowerKit', 'trx_addons');
		}
		return $not_installed;
	}
}

if ( ! function_exists( 'trx_addons_powerkit_importer_set_options' ) ) {
	add_filter( 'trx_addons_filter_importer_options', 'trx_addons_powerkit_importer_set_options' );
	/**
	 * Add plugin's specific options to the list for export
	 * 
	 * @hooked trx_addons_filter_importer_options
	 *
	 * @param array $options  Importer options
	 * 
	 * @return array          Modified options
	 */
	function trx_addons_powerkit_importer_set_options( $options = array() ) {
		if ( trx_addons_exists_powerkit() && in_array( 'powerkit', $options['required_plugins'] ) ) {
			$options['additional_options'][] = 'powerkit_enabled_%';		// Add slugs to export options for this plugin
		}
		return $options;
	}
}

if ( ! function_exists( 'trx_addons_powerkit_importer_check_options' ) ) {
	add_filter( 'trx_addons_filter_import_theme_options', 'trx_addons_powerkit_importer_check_options', 10, 4 );
	/**
	 * Prevent plugin's specific options to be imported if a plugin is not installed
	 * 
	 * @hooked trx_addons_filter_import_theme_options
	 *
	 * @param bool   $allow    Allow import or not
	 * @param string $k        Option name
	 * @param string $v        Option value
	 * @param array  $options  Importer options
	 * 
	 * @return bool           Modified allow flag
	 */
	function trx_addons_powerkit_importer_check_options( $allow, $k, $v, $options ) {
		if ( $allow && strpos( $k, 'powerkit_' ) === 0 ) {
			$allow = trx_addons_exists_powerkit() && in_array( 'powerkit', $options['required_plugins'] );
		}
		return $allow;
	}
}
