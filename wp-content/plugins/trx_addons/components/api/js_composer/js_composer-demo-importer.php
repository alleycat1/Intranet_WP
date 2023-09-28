<?php
/**
 * Plugin support: WPBakery PageBuilder (Importer support)
 *
 * @package ThemeREX Addons
 * @since v1.0
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

if ( ! function_exists( 'trx_addons_vc_importer_required_plugins' ) ) {
	add_filter( 'trx_addons_filter_importer_required_plugins',	'trx_addons_vc_importer_required_plugins', 10, 2 );
	/**
	 * Check if the required plugin is not installed
	 * 
	 * @hooked trx_addons_filter_importer_required_plugins
	 *
	 * @param string $not_installed  Not installed plugins list
	 * @param string $list           Required plugins list
	 * 
	 * @return string                Not installed plugins list
	 */
	function trx_addons_vc_importer_required_plugins( $not_installed = '', $list = '' ) {
		if ( strpos( $list, 'js_composer' ) !== false && ! trx_addons_exists_vc() ) {
			$not_installed .= '<br>' . esc_html__('WPBakery PageBuilder', 'trx_addons');
		}
		return $not_installed;
	}
}

if ( ! function_exists( 'trx_addons_vc_importer_set_options' ) ) {
	add_filter( 'trx_addons_filter_importer_options', 'trx_addons_vc_importer_set_options' );
	/**
	 * Set plugin's specific importer options
	 * 
	 * @hooked trx_addons_filter_importer_options
	 *
	 * @param array $options		Importer options
	 * 
	 * @return array				Modified options
	 */
	function trx_addons_vc_importer_set_options( $options = array() ) {
		if ( trx_addons_exists_vc() && in_array( 'js_composer', $options['required_plugins'] ) ) {
			$options['additional_options'][] = 'wpb_js_templates';		// Add slugs to export options for this plugin
		}
		return $options;
	}
}

if ( ! function_exists( 'trx_addons_vc_importer_check_options' ) ) {
	add_filter( 'trx_addons_filter_import_theme_options', 'trx_addons_vc_importer_check_options', 10, 4 );
	/**
	 * Prevent to import plugin's specific options if a plugin is not installed
	 * 
	 * @hooked trx_addons_filter_import_theme_options
	 *
	 * @param boolean $allow		Allow import or not
	 * @param string $k				Option name
	 * @param mixed $v				Option value
	 * @param array $options		Importer options list
	 * 
	 * @return boolean				Allow import or not
	 */
	function trx_addons_vc_importer_check_options( $allow, $k, $v, $options ) {
		if ( $allow && $k == 'wpb_js_templates' ) {
			$allow = trx_addons_exists_vc() && in_array( 'js_composer', $options['required_plugins'] );
		}
		return $allow;
	}
}

if ( ! function_exists( 'trx_addons_vc_importer_check_row' ) ) {
	add_filter( 'trx_addons_filter_importer_import_row', 'trx_addons_vc_importer_check_row', 9, 4 );
	/**
	 * Check if the row will be imported to the database table 'posts'
	 * 
	 * @hooked trx_addons_filter_importer_import_row
	 *
	 * @param boolean $flag		Allow import or not
	 * @param string $table		Table name
	 * @param array $row		Row data
	 * @param array $list		List of the importing plugins
	 * 
	 * @return boolean			Allow import or not
	 */
	function trx_addons_vc_importer_check_row( $flag, $table, $row, $list ) {
		if ( $flag || strpos( $list, 'js_composer' ) === false ) {
			return $flag;
		}
		if ( trx_addons_exists_vc() ) {
			if ( $table == 'posts' ) {
				$flag = $row['post_type']=='vc_grid_item';
			}
		}
		return $flag;
	}
}
