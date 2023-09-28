<?php
/**
 * Plugin support: Contact Form 7 (Importer support)
 *
 * @package ThemeREX Addons
 * @since v1.5
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


if ( ! function_exists( 'trx_addons_cf7_importer_required_plugins' ) ) {
	add_filter( 'trx_addons_filter_importer_required_plugins',	'trx_addons_cf7_importer_required_plugins', 10, 2 );
	/**
	 * Check if this plugin is required and installed
	 * 
	 * @hooked trx_addons_filter_importer_required_plugins
	 *
	 * @param string $not_installed  Not installed plugins list
	 * @param string $list           List of required plugins
	 * 
	 * @return string                Not installed plugins list
	 */
	function trx_addons_cf7_importer_required_plugins( $not_installed = '', $list = '' ) {
		if ( strpos( $list, 'contact-form-7' ) !== false && ! trx_addons_exists_cf7() ) {
			$not_installed .= '<br>' . esc_html__( 'Contact Form 7', 'trx_addons' );
		}
		return $not_installed;
	}
}

if ( ! function_exists( 'trx_addons_cf7_importer_set_options' ) ) {
	add_filter( 'trx_addons_filter_importer_options', 'trx_addons_cf7_importer_set_options' );
	/**
	 * Add plugin's specific options to the export options list
	 * 
	 * @hooked trx_addons_filter_importer_options
	 *
	 * @param array $options		Options to export
	 * 
	 * @return array				Modified options
	 */
	function trx_addons_cf7_importer_set_options( $options = array() ) {
		if ( trx_addons_exists_cf7() && in_array( 'contact-form-7', $options['required_plugins'] ) ) {
			$options['additional_options'][] = 'wpcf7';
		}
		return $options;
	}
}

if ( ! function_exists( 'trx_addons_cf7_importer_check_options' ) ) {
	add_filter( 'trx_addons_filter_import_theme_options', 'trx_addons_cf7_importer_check_options', 10, 4 );
	/**
	 * Prevent to import plugin's specific options if plugin is not installed
	 * 
	 * @hooked trx_addons_filter_import_theme_options
	 *
	 * @param boolean $allow		Allow import or not
	 * @param string $k				Option name
	 * @param mixed $v				Option value. Not used in this hook
	 * @param array $options		Options of the current import
	 * 
	 * @return boolean				Allow import or not
	 */
	function trx_addons_cf7_importer_check_options( $allow, $k, $v, $options ) {
		if ( $allow && $k == 'wpcf7' ) {
			$allow = trx_addons_exists_cf7() && in_array( 'contact-form-7', $options['required_plugins'] );
		}
		return $allow;
	}
}

if ( ! function_exists( 'trx_addons_cf7_importer_show_params' ) ) {
	add_action( 'trx_addons_action_importer_params', 'trx_addons_cf7_importer_show_params', 10, 1 );
	/**
	 * Add plugin to the list with plugins for the importer
	 * 
	 * @hooked trx_addons_action_importer_params
	 *
	 * @param object $importer		Importer object
	 */
	function trx_addons_cf7_importer_show_params( $importer ) {
		if ( trx_addons_exists_cf7() && in_array( 'contact-form-7', $importer->options['required_plugins'] ) ) {
			$importer->show_importer_params( array(
				'slug' => 'contact-form-7',
				'title' => esc_html__('Import Contact Form 7', 'trx_addons'),
				'part' => 1
			) );
		}
	}
}

if ( ! function_exists( 'trx_addons_cf7_importer_check_row' ) ) {
	add_filter( 'trx_addons_filter_importer_import_row', 'trx_addons_cf7_importer_check_row', 9, 4 );
	/**
	 * Check if the row will be imported
	 * 
	 * @hooked trx_addons_filter_importer_import_row
	 *
	 * @param boolean $flag		Allow import or not
	 * @param string $table		Table name
	 * @param array $row		Row data
	 * @param array $list		List of the required plugins
	 * 
	 * @return boolean			Allow import or not
	 */
	function trx_addons_cf7_importer_check_row( $flag, $table, $row, $list ) {
		if ( $flag || strpos( $list, 'contact-form-7' ) === false ) {
			return $flag;
		}
		if ( trx_addons_exists_cf7() ) {
			if ( $table == 'posts' ) {
				$flag = $row['post_type'] == WPCF7_ContactForm::post_type;
			}
		}
		return $flag;
	}
}
