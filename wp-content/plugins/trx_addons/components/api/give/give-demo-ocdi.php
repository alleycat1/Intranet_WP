<?php
/**
 * Plugin support: Give (OCDI support)
 *
 * @package ThemeREX Addons
 * @since v1.6.50
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

if ( ! function_exists( 'trx_addons_ocdi_give_set_options' ) ) {
	add_filter( 'trx_addons_filter_ocdi_options', 'trx_addons_ocdi_give_set_options' );
	/**
	 * Set plugin's specific importer options for OCDI
	 * 
	 * @hooked trx_addons_filter_ocdi_options
	 *
	 * @param array $ocdi_options Importer options
	 * 
	 * @return array Modified options
	 */
	function trx_addons_ocdi_give_set_options( $ocdi_options ) {
		$ocdi_options['import_give_file_url'] = 'give.txt';
		return $ocdi_options;		
	}
}

if ( ! function_exists( 'trx_addons_ocdi_give_export' ) ) {
	add_filter( 'trx_addons_filter_ocdi_export_files', 'trx_addons_ocdi_give_export' );
	/**
	 * Export Give (Donation Form) data to the file
	 *
	 * @hooked trx_addons_filter_ocdi_export_files
	 *
	 * @param array $output  HTML with a list of files with exported data
	 * 
	 * @return array         Modified HTML
	 */
	function trx_addons_ocdi_give_export( $output ) {
		$list = array();
		if ( trx_addons_exists_give() && in_array( 'give', trx_addons_ocdi_options( 'required_plugins' ) ) ) {
			// Export tables
			$tables = array(
				'give_formmeta',
				'give_donors',
				'give_donormeta',
				'give_logs',
				'give_logmeta',
				'give_paymentmeta',
				'give_sequental_ordering'
			);
			$list = trx_addons_ocdi_export_tables( $tables, $list );

			// Export options
			$options = array( 'give_settings' );
			$list = trx_addons_ocdi_export_options( $options, $list );
			
			// Save to the file
			$file_path = TRX_ADDONS_PLUGIN_OCDI . "export/give.txt";
			trx_addons_fpc( trx_addons_get_file_dir( $file_path ), serialize( $list ) );
			
			// Add a link to the file to the output
			$output .= '<h4><a href="' . trx_addons_get_file_url( $file_path ) . '" download>' . esc_html__( 'Give (Donation Form)', 'trx_addons' ) . '</a></h4>';
		}
		return $output;
	}
}

if ( ! function_exists( 'trx_addons_ocdi_give_import_field' ) ) {
	add_filter( 'trx_addons_filter_ocdi_import_fields', 'trx_addons_ocdi_give_import_field' );
	/**
	 * Add a name of the pluginto the list for import
	 *
	 * @hooked trx_addons_filter_ocdi_import_fields
	 *
	 * @param array $output  HTML with a list of files
	 * 
	 * @return array         Modified HTML
	 */
	function trx_addons_ocdi_give_import_field( $output ) {
		if ( trx_addons_exists_give() && in_array( 'give', trx_addons_ocdi_options( 'required_plugins' ) ) ) {
			$output .= '<label><input type="checkbox" name="give" value="give">' . esc_html__( 'Give (Donation Form)', 'trx_addons' ) . '</label><br/>';
		}
		return $output;
	}
}

if ( ! function_exists( 'trx_addons_ocdi_give_import' ) ) {
	add_action( 'trx_addons_action_ocdi_import_plugins', 'trx_addons_ocdi_give_import', 10, 1 );
	/**
	 * Import Give (Donation Form) data from the file
	 *
	 * @hooked trx_addons_action_ocdi_import_plugins
	 *
	 * @param array $import_plugins  List of plugins to import
	 * 
	 * @return array                 Modified list of plugins
	 */
	function trx_addons_ocdi_give_import( $import_plugins ) {
		if ( trx_addons_exists_give() && in_array( 'give', $import_plugins ) ) {
			trx_addons_ocdi_import_dump( 'give' );
			echo esc_html__( 'Give (Donation Form) import complete.', 'trx_addons' ) . "\r\n";
		}
	}
}
