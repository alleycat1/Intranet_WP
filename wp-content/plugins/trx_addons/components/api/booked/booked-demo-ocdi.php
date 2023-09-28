<?php
/**
 * Plugin support: Booked Appointments (OCDI support)
 *
 * @package ThemeREX Addons
 * @since v1.5
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

if ( ! function_exists( 'trx_addons_ocdi_booked_set_options' ) ) {
	add_filter( 'trx_addons_filter_ocdi_options', 'trx_addons_ocdi_booked_set_options' );
	/**
	 * Set plugin's specific importer options
	 *
	 * @hooked trx_addons_filter_ocdi_options
	 *
	 * @param array $ocdi_options		Importer options
	 *
	 * @return array					Modified options
	 */
	function trx_addons_ocdi_booked_set_options( $ocdi_options ) {
		$ocdi_options['import_booked_file_url'] = 'booked.txt';
		return $ocdi_options;		
	}
}

if ( ! function_exists( 'trx_addons_ocdi_booked_export' ) ) {
	add_filter( 'trx_addons_filter_ocdi_export_files', 'trx_addons_ocdi_booked_export' );
	/**
	 * Export Booked Calendar data to the file via OCDI
	 *
	 * @hooked trx_addons_filter_ocdi_export_files
	 *
	 * @param array $output		HTML output with the list of files to export
	 *
	 * @return array			Modified output
	 */
	function trx_addons_ocdi_booked_export( $output ) {
		$list = array();
		if ( trx_addons_exists_booked() && in_array( 'booked', trx_addons_ocdi_options( 'required_plugins' ) ) ) {
			// Get plugin data from database
			$options = array('booked_%');
			$list = trx_addons_ocdi_export_options( $options, $list );

			// Save as file
			$file_path = TRX_ADDONS_PLUGIN_OCDI . "export/booked.txt";
			trx_addons_fpc( trx_addons_get_file_dir( $file_path ), serialize( $list ) );

			// Return file path
			$output .= '<h4><a href="' . trx_addons_get_file_url( $file_path ) . '" download>' . esc_html__('Booked Calendar', 'trx_addons') . '</a></h4>';
		}
		return $output;
	}
}

if ( ! function_exists( 'trx_addons_ocdi_booked_import_field' ) ) {
	add_filter( 'trx_addons_filter_ocdi_import_fields', 'trx_addons_ocdi_booked_import_field' );
	/**
	 * Add checkbox to the one-click importer to allow import Booked Calendar data
	 *
	 * @hooked trx_addons_filter_ocdi_import_fields
	 *
	 * @param string $output		HTML output with the list of checkboxes
	 *
	 * @return string				Modified output
	 */
	function trx_addons_ocdi_booked_import_field( $output ){
		$list = array();
		if ( trx_addons_exists_booked() && in_array( 'booked', trx_addons_ocdi_options( 'required_plugins' ) ) ) {
			$output .= '<label><input type="checkbox" name="booked" value="booked">' . esc_html__( 'Booked Calendar', 'trx_addons' ) . '</label><br/>';
		}
		return $output;
	}
}

if ( ! function_exists( 'trx_addons_ocdi_booked_import' ) ) {
	add_action( 'trx_addons_action_ocdi_import_plugins', 'trx_addons_ocdi_booked_import', 10, 1 );
	/**
	 * Import Booked Calendar data from the file
	 *
	 * @hooked trx_addons_action_ocdi_import_plugins
	 *
	 * @param array $import_plugins		List of plugins to import
	 */
	function trx_addons_ocdi_booked_import( $import_plugins){
		if ( trx_addons_exists_booked() && in_array( 'booked', $import_plugins ) ) {
			trx_addons_ocdi_import_dump('booked');
			echo esc_html__('Booked Calendar import complete.', 'trx_addons') . "\r\n";
		}
	}
}
