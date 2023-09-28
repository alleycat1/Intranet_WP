<?php
/**
 * Plugin support: WP GDPR Compliance (OCDI support)
 *
 * @package ThemeREX Addons
 * @since v1.6.49
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

if ( ! function_exists( 'trx_addons_ocdi_wp_gdpr_compliance_feed_set_options' ) ) {
	add_filter( 'trx_addons_filter_ocdi_options', 'trx_addons_ocdi_wp_gdpr_compliance_feed_set_options' );
	/**
	 * Set plugin's specific importer options for OCDI
	 * 
	 * @hooked trx_addons_filter_ocdi_options
	 *
	 * @param array $ocdi_options	Importer options
	 * 
	 * @return array				Modified options
	 */
	function trx_addons_ocdi_wp_gdpr_compliance_feed_set_options( $ocdi_options ) {
		$ocdi_options['import_wp-gdpr-compliance_file_url'] = 'wp-gdpr-compliance.txt';
		return $ocdi_options;		
	}
}

if ( ! function_exists( 'trx_addons_ocdi_wp_gdpr_compliance_export' ) ) {
	add_filter( 'trx_addons_filter_ocdi_export_files', 'trx_addons_ocdi_wp_gdpr_compliance_export' );
	/**
	 * Export WP GDPR Compliance data to the file (for OCDI)
	 * 
	 * @hooked trx_addons_filter_ocdi_export_files
	 *
	 * @param array $output		HTML list of export files
	 * 
	 * @return array			Modified output
	 */
	function trx_addons_ocdi_wp_gdpr_compliance_export( $output ) {
		$list = array();
		if ( trx_addons_exists_wp_gdpr_compliance() && in_array( 'wp-gdpr-compliance', trx_addons_ocdi_options( 'required_plugins' ) ) ) {
			// Get plugin data from database
			$options = array( 'wpgdprc_%' );
			$list = trx_addons_ocdi_export_options( $options, $list );
			
			// Save as file
			$file_path = TRX_ADDONS_PLUGIN_OCDI . "export/wp-gdpr-compliance.txt";
			trx_addons_fpc( trx_addons_get_file_dir( $file_path ), serialize( $list ) );
			
			// Return file path
			$output .= '<h4><a href="' . trx_addons_get_file_url( $file_path ) . '" download>' . esc_html__( 'WP GDPR Compliance', 'trx_addons' ) . '</a></h4>';
		}
		return $output;
	}
}

if ( ! function_exists( 'trx_addons_ocdi_wp_gdpr_compliance_import_field' ) ) {
	add_filter( 'trx_addons_filter_ocdi_import_fields', 'trx_addons_ocdi_wp_gdpr_compliance_import_field' );
	/**
	 * Add checkbox with a plugin name to the one-click importer (OCDI)
	 * 
	 * @hooked trx_addons_filter_ocdi_import_fields
	 *
	 * @param array $output		HTML output
	 * 
	 * @return array			Modified output
	 */
	function trx_addons_ocdi_wp_gdpr_compliance_import_field( $output ) {
		if ( trx_addons_exists_wp_gdpr_compliance() && in_array( 'wp-gdpr-compliance', trx_addons_ocdi_options( 'required_plugins' ) ) ) {
			$output .= '<label><input type="checkbox" name="wp-gdpr-compliance" value="wp-gdpr-compliance">' . esc_html__( 'WP GDPR Compliance', 'trx_addons' ) . '</label><br/>';
		}
		return $output;
	}
}

if ( ! function_exists( 'trx_addons_ocdi_wp_gdpr_compliance_import' ) ) {
	add_action( 'trx_addons_action_ocdi_import_plugins', 'trx_addons_ocdi_wp_gdpr_compliance_import', 10, 1 );
	/**
	 * Import WP GDPR Compliance data from the file (for OCDI)
	 * 
	 * @hooked trx_addons_action_ocdi_import_plugins
	 *
	 * @param array $import_plugins		List of plugins to import
	 */
	function trx_addons_ocdi_wp_gdpr_compliance_import( $import_plugins){
		if ( trx_addons_exists_wp_gdpr_compliance() && in_array( 'wp-gdpr-compliance', $import_plugins ) ) {
			trx_addons_ocdi_import_dump( 'wp-gdpr-compliance' );
			echo esc_html__( 'WP GDPR Compliance import complete.', 'trx_addons' ) . "\r\n";
		}
	}
}
