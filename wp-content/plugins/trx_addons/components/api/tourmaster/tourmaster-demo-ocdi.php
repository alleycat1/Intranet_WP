<?php
/**
 * Plugin support: Tour Master (OCDI support)
 *
 * @package ThemeREX Addons
 * @since v1.6.38
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

if ( ! function_exists( 'trx_addons_ocdi_tourmaster_set_options' ) ) {
	add_filter( 'trx_addons_filter_ocdi_options', 'trx_addons_ocdi_tourmaster_set_options' );
	/**
	 * Set plugin's specific importer options for OCDI
	 *
	 * @hooked trx_addons_filter_ocdi_options
	 *
	 * @param array $ocdi_options	Options to set
	 * 
	 * @return array				Modified options
	 */
	function trx_addons_ocdi_tourmaster_set_options( $ocdi_options ) {
		$ocdi_options['import_tourmaster_file_url'] = 'tourmaster.txt';
		return $ocdi_options;		
	}
}

if ( ! function_exists( 'trx_addons_ocdi_tourmaster_export' ) ) {
	add_filter( 'trx_addons_filter_ocdi_export_files', 'trx_addons_ocdi_tourmaster_export' );
	/**
	 * Export Tour Master data to the file
	 * 
	 * @hooked trx_addons_filter_ocdi_export_files
	 *
	 * @param string $output  HTML layout with the list of files with exported data
	 * 
	 * @return string         Modified output
	 */
	function trx_addons_ocdi_tourmaster_export( $output ) {
		$list = array();
		if ( trx_addons_exists_tourmaster() && in_array( 'tourmaster', trx_addons_ocdi_options( 'required_plugins' ) ) ) {
			// Get plugin data from database
			$options = array( 'tourmaster_general', 'tourmaster_color', 'tourmaster_plugin' );
			$list = trx_addons_ocdi_export_options( $options, $list );
			
			$tables = array( 'tourmaster_order', 'tourmaster_review' );
			$list = trx_addons_ocdi_export_tables( $tables, $list );
			
			// Save as file
			$file_path = TRX_ADDONS_PLUGIN_OCDI . "export/tourmaster.txt";
			trx_addons_fpc( trx_addons_get_file_dir( $file_path ), serialize( $list ) );
			
			// Return file path
			$output .= '<h4><a href="' . trx_addons_get_file_url( $file_path ) . '" download>' . esc_html__( 'Tourmaster', 'trx_addons' ) . '</a></h4>';
		}
		return $output;
	}
}

if ( ! function_exists( 'trx_addons_ocdi_tourmaster_import_field' ) ) {
	add_filter( 'trx_addons_filter_ocdi_import_fields', 'trx_addons_ocdi_tourmaster_import_field' );
	/**
	 * Add plugin to the import list
	 * 
	 * @hooked trx_addons_filter_ocdi_import_fields
	 *
	 * @param string $output  HTML layout with the list of checkboxes
	 * 
	 * @return string         Modified output
	 */
	function trx_addons_ocdi_tourmaster_import_field( $output ) {
		if ( trx_addons_exists_tourmaster() && in_array( 'tourmaster', trx_addons_ocdi_options( 'required_plugins' ) ) ) {
			$output .= '<label><input type="checkbox" name="tourmaster" value="tourmaster">' . esc_html__( 'Tourmaster', 'trx_addons' ) . '</label><br/>';
		}
		return $output;
	}
}

if ( ! function_exists( 'trx_addons_ocdi_tourmaster_import' ) ) {
	add_action( 'trx_addons_action_ocdi_import_plugins', 'trx_addons_ocdi_tourmaster_import', 10, 1 );
	/**
	 * Import Tour Master data from the file
	 * 
	 * @hooked trx_addons_action_ocdi_import_plugins
	 *
	 * @param array $import_plugins  List of the plugins to import
	 */
	function trx_addons_ocdi_tourmaster_import( $import_plugins ) {
		if ( trx_addons_exists_tourmaster() && in_array( 'tourmaster', $import_plugins ) ) {
			trx_addons_ocdi_import_dump( 'tourmaster' );
			echo esc_html__('Tourmaster import complete.', 'trx_addons') . "\r\n";
		}
	}
}
