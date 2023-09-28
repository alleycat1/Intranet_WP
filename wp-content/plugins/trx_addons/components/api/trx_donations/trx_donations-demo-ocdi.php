<?php
/**
 * Plugin support: ThemeREX Donations (OCDI support)
 *
 * @package ThemeREX Addons
 * @since v1.5
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

if ( ! function_exists( 'trx_addons_ocdi_trx_donations_set_options' ) ) {
	add_filter( 'trx_addons_filter_ocdi_options', 'trx_addons_ocdi_trx_donations_set_options' );
	/**
	 * Set plugin's specific importer options for OCDI
	 * 
	 * @hooked trx_addons_filter_ocdi_options
	 *
	 * @param array $ocdi_options		Options to set
	 * 
	 * @return array				Modified options
	 */
	function trx_addons_ocdi_trx_donations_set_options( $ocdi_options ) {
		$ocdi_options['import_trx_donations_file_url'] = 'trx_donations.txt';
		return $ocdi_options;
	}
}

if ( ! function_exists( 'trx_addons_ocdi_trx_donations_export' ) ) {
	add_filter( 'trx_addons_filter_ocdi_export_files', 'trx_addons_ocdi_trx_donations_export' );
	/**
	 * Export ThemeREX Donations data to the file
	 * 
	 * @hooked trx_addons_filter_ocdi_export_files
	 *
	 * @param array $output		HTML list of export files
	 * 
	 * @return array			Modified output
	 */
	function trx_addons_ocdi_trx_donations_export( $output ) {
		$list = array();
		if ( trx_addons_exists_trx_donations() && in_array( 'trx_donations', trx_addons_ocdi_options( 'required_plugins' ) ) ) {
			// Get plugin data from database
			$options = array('trx_donations_options');
			$list = trx_addons_ocdi_export_options( $options, $list );
			
			// Save as file
			$file_path = TRX_ADDONS_PLUGIN_OCDI . "export/trx_donations.txt";
			trx_addons_fpc( trx_addons_get_file_dir( $file_path ), serialize( $list ) );
			
			// Return file path
			$output .= '<h4><a href="' . trx_addons_get_file_url( $file_path ) . '" download>' . esc_html__( 'ThemeREX Donations', 'trx_addons' ) . '</a></h4>';
		}
		return $output;
	}
}

if ( ! function_exists( 'trx_addons_ocdi_trx_donations_import_field' ) ) {
	add_filter( 'trx_addons_filter_ocdi_import_fields', 'trx_addons_ocdi_trx_donations_import_field' );
	/**
	 * Add checkbox with a plugin name to the list to import
	 * 
	 * @hooked trx_addons_filter_ocdi_import_fields
	 *
	 * @param array $output		HTML output with checkboxes
	 * 
	 * @return array			Modified output
	 */
	function trx_addons_ocdi_trx_donations_import_field( $output ) {
		if ( trx_addons_exists_trx_donations() && in_array( 'trx_donations', trx_addons_ocdi_options( 'required_plugins' ) ) ) {
			$output .= '<label><input type="checkbox" name="trx_donations" value="trx_donations">' . esc_html__( 'ThemeREX Donations', 'trx_addons' ) . '</label><br/>';
		}
		return $output;
	}
}

if ( ! function_exists( 'trx_addons_ocdi_trx_donations_import' ) ) {
	add_action( 'trx_addons_action_ocdi_import_plugins', 'trx_addons_ocdi_trx_donations_import', 10, 1 );
	/**
	 * Import ThemeREX Donations data from the file via OCDI
	 * 
	 * @hooked trx_addons_action_ocdi_import_plugins
	 *
	 * @param array $import_plugins		List of the plugins to import
	 */
	function trx_addons_ocdi_trx_donations_import( $import_plugins ) {
		if ( trx_addons_exists_trx_donations() && in_array( 'trx_donations', $import_plugins ) ) {
			trx_addons_ocdi_import_dump( 'trx_donations' );
			echo esc_html__( 'ThemeREX Donations import complete.', 'trx_addons' ) . "\r\n";
		}
	}
}
