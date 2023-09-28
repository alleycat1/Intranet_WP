<?php
/**
 * Plugin support: The GDPR Framework (OCDI support)
 *
 * @package ThemeREX Addons
 * @since v1.6.49
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

if ( ! function_exists( 'trx_addons_ocdi_gdpr_framework_feed_set_options' ) ) {
	add_filter( 'trx_addons_filter_ocdi_options', 'trx_addons_ocdi_gdpr_framework_feed_set_options' );
	/**
	 * Set plugin's specific importer options for OCDI
	 * 
	 * @hooked trx_addons_filter_ocdi_options
	 *
	 * @param array $ocdi_options		Options to export
	 * 
	 * @return array					Modified options
	 */
	function trx_addons_ocdi_gdpr_framework_feed_set_options( $ocdi_options ) {
		$ocdi_options['import_gdpr-framework_file_url'] = 'gdpr-framework.txt';
		return $ocdi_options;		
	}
}

if ( ! function_exists( 'trx_addons_ocdi_gdpr_framework_export' ) ) {
	add_filter( 'trx_addons_filter_ocdi_export_files', 'trx_addons_ocdi_gdpr_framework_export' );
	/**
	 * Export plugin's specific data
	 * 
	 * @hooked trx_addons_filter_ocdi_export_files
	 *
	 * @param array $output		List of files to export
	 * 
	 * @return array			Modified list
	 */
	function trx_addons_ocdi_gdpr_framework_export( $output ) {
		$list = array();
		if ( trx_addons_exists_gdpr_framework() && in_array( 'gdpr-framework', trx_addons_ocdi_options('required_plugins' ) ) ) {
			// Get plugin data from database
			$options = array( 'gdpr_%' );
			$list = trx_addons_ocdi_export_options( $options, $list );
			
			// Save as file
			$file_path = TRX_ADDONS_PLUGIN_OCDI . "export/gdpr-framework.txt";
			trx_addons_fpc( trx_addons_get_file_dir( $file_path ), serialize( $list ) );
			
			// Return file path
			$output .= '<h4><a href="' . trx_addons_get_file_url( $file_path ) . '" download>' . esc_html__( 'The GDPR Framework', 'trx_addons' ) . '</a></h4>';
		}
		return $output;
	}
}

if ( ! function_exists( 'trx_addons_ocdi_gdpr_framework_import_field' ) ) {
	add_filter( 'trx_addons_filter_ocdi_import_fields', 'trx_addons_ocdi_gdpr_framework_import_field' );
	/**
	 * Add a plugin's name to the list of the required plugins to import for OCDI
	 * 
	 * @hooked trx_addons_filter_ocdi_import_fields
	 *
	 * @param array $output		List of plugins to import
	 * 
	 * @return array			Modified list
	 */
	function trx_addons_ocdi_gdpr_framework_import_field( $output ) {
		if ( trx_addons_exists_gdpr_framework() && in_array( 'gdpr-framework', trx_addons_ocdi_options('required_plugins' ) ) ) {
			$output .= '<label><input type="checkbox" name="gdpr-framework" value="gdpr-framework">'
							. esc_html__( 'The GDPR Framework', 'trx_addons' )
						. '</label><br/>';
		}
		return $output;
	}
}

if ( ! function_exists( 'trx_addons_ocdi_gdpr_framework_import' ) ) {
	add_action( 'trx_addons_action_ocdi_import_plugins', 'trx_addons_ocdi_gdpr_framework_import', 10, 1 );
	/**
	 * Import plugin's specific data
	 * 
	 * @hooked trx_addons_action_ocdi_import_plugins
	 *
	 * @param array $import_plugins		List of plugins to import
	 * 
	 * @return array					Modified list
	 */
	function trx_addons_ocdi_gdpr_framework_import( $import_plugins ) {
		if ( trx_addons_exists_gdpr_framework() && in_array( 'gdpr-framework', $import_plugins ) ) {
			trx_addons_ocdi_import_dump( 'gdpr-framework' );
			echo esc_html__( 'The GDPR Framework import complete.', 'trx_addons' ) . "\r\n";
		}
	}
}
