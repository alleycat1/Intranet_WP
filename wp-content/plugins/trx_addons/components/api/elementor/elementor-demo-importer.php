<?php
/**
 * Plugin support: Elementor (Importer support)
 *
 * @package ThemeREX Addons
 * @since v1.0
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

if ( ! function_exists( 'trx_addons_elm_importer_required_plugins' ) ) {
	add_filter( 'trx_addons_filter_importer_required_plugins',	'trx_addons_elm_importer_required_plugins', 10, 2 );
	/**
	 * Check if the required plugins are installed
	 * 
	 * @hooked trx_addons_filter_importer_required_plugins
	 *
	 * @param string $not_installed  Not installed plugins list
	 * @param string $list           Plugins list
	 * 
	 * @return string                Not installed plugins list with current plugin added (if need)
	 */
	function trx_addons_elm_importer_required_plugins( $not_installed = '', $list = '' ) {
		if ( strpos( $list, 'elementor' ) !== false && ! trx_addons_exists_elementor() ) {
			$not_installed .= '<br>' . esc_html__( 'Elementor (free PageBuilder)', 'trx_addons' );
		}
		return $not_installed;
	}
}

if ( ! function_exists( 'trx_addons_elm_importer_set_options' ) ) {
	add_filter( 'trx_addons_filter_importer_options', 'trx_addons_elm_importer_set_options' );
	/**
	 * Add plugin's specific options to the list for export
	 * 
	 * @hooked trx_addons_filter_importer_options
	 *
	 * @param array $options		Options to set
	 * 
	 * @return array				Modified options
	 */
	function trx_addons_elm_importer_set_options( $options = array() ) {
		if ( trx_addons_exists_elementor() && in_array( 'elementor', $options['required_plugins'] ) ) {
			$options['additional_options'][] = 'elementor%';		// Add slugs to export options for this plugin
			$options['skip_options'][] = 'elementor_log';			// Skip slugs (do not export options for this plugin)
		}
		return $options;
	}
}

if ( ! function_exists( 'trx_addons_elm_importer_check_options' ) ) {
	add_filter( 'trx_addons_filter_import_theme_options', 'trx_addons_elm_importer_check_options', 10, 4 );
	/**
	 * Prevent to import plugin's specific options if a plugin is not installed
	 * 
	 * @hooked trx_addons_filter_import_theme_options
	 *
	 * @param boolean $allow		Allow to import option or not
	 * @param string $k				Option name
	 * @param string $v				Option value. Not used
	 * @param array $options		All importer options
	 * 
	 * @return boolean				Allow to import option or not
	 */
	function trx_addons_elm_importer_check_options( $allow, $k, $v, $options ) {
		if ( $allow && strpos( $k, 'elementor' ) === 0 ) {
			$allow = trx_addons_exists_elementor() && in_array( 'elementor', $options['required_plugins'] );
		}
		return $allow;
	}
}

if ( ! function_exists( 'trx_addons_elm_importer_theme_options_data' ) ) {
	add_filter( 'trx_addons_filter_import_theme_options_data', 'trx_addons_elm_importer_theme_options_data', 10, 1 );
	/**
	 * Fix for Elementor 3.3.0+ - move options 'blogname' and 'blogdescription'
	 * to the end of the list (after all 'elementor_%' options)
	 * 
	 * @hooked trx_addons_filter_import_theme_options_data
	 *
	 * @param array $data		Options to set
	 * 
	 * @return array			Modified options
	 */
	function trx_addons_elm_importer_theme_options_data( $data ) {
		if ( isset( $data['blogname'] ) ) {
			$val = $data['blogname'];
			unset( $data['blogname'] );
			$data['blogname'] = $val;
		}
		if ( isset( $data['blogdescription'] ) ) {
			$val = $data['blogdescription'];
			unset( $data['blogdescription'] );
			$data['blogdescription'] = $val;
		}
		return $data;
	}
}
