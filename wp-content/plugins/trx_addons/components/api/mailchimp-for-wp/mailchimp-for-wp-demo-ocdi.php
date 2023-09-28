<?php
/**
 * Plugin support: Mail Chimp (OCDI support)
 *
 * @package ThemeREX Addons
 * @since v1.5
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

if ( ! function_exists( 'trx_addons_ocdi_mailchimp_set_options' ) ) {
	add_filter( 'trx_addons_filter_ocdi_options', 'trx_addons_ocdi_mailchimp_set_options' );
	/**
	 * Set plugin's specific importer options for OCDI
	 * 
	 * @hooked trx_addons_filter_ocdi_options
	 *
	 * @param array $options		Options to set
	 * 
	 * @return array				Modified options
	 */
	function trx_addons_ocdi_mailchimp_set_options( $ocdi_options ) {
		$ocdi_options['import_mailchimp_file_url'] = 'mailchimp.txt';
		return $ocdi_options;		
	}
}

if ( ! function_exists( 'trx_addons_ocdi_mailchimp_export' ) ) {
	add_filter( 'trx_addons_filter_ocdi_export_files', 'trx_addons_ocdi_mailchimp_export' );
	/**
	 * Export MailChimp for WP data
	 * 
	 * @hooked trx_addons_filter_ocdi_export_files
	 *
	 * @param array $output		List of the exported files as an HTML output
	 * 
	 * @return array			Modified output
	 */
	function trx_addons_ocdi_mailchimp_export( $output ) {
		$list = array();
		if ( trx_addons_exists_mailchimp() && in_array( 'mailchimp-for-wp', trx_addons_ocdi_options( 'required_plugins' ) ) ) {
			// Get plugin data from database
			$options = array( 'mc4wp_default_form_id', 'mc4wp_form_stylesheets', 'mc4wp_flash_messages', 'mc4wp_integrations' );
			$list = trx_addons_ocdi_export_options( $options, $list );
			
			// Save as file
			$file_path = TRX_ADDONS_PLUGIN_OCDI . "export/mailchimp.txt";
			trx_addons_fpc( trx_addons_get_file_dir( $file_path ), serialize( $list ) );
			
			// Return file path
			$output .= '<h4><a href="' . trx_addons_get_file_url( $file_path ) . '" download>' . esc_html__( 'MailChimp for WP', 'trx_addons' ) . '</a></h4>';
		}
		return $output;
	}
}

if ( ! function_exists( 'trx_addons_ocdi_mailchimp_import_field' ) ) {
	add_filter( 'trx_addons_filter_ocdi_import_fields', 'trx_addons_ocdi_mailchimp_import_field' );
	/**
	 * Add checkbox to the one-click importer to allow import MailChimp for WP
	 * 
	 * @hooked trx_addons_filter_ocdi_import_fields
	 *
	 * @param array $output		List of the checkboxes as an HTML output
	 * 
	 * @return array			Modified output
	 */
	function trx_addons_ocdi_mailchimp_import_field( $output ) {
		if ( trx_addons_exists_mailchimp() && in_array( 'mailchimp-for-wp', trx_addons_ocdi_options( 'required_plugins' ) ) ) {
			$output .= '<label><input type="checkbox" name="mailchimp" value="mailchimp">' . esc_html__( 'MailChimp for WP', 'trx_addons' ) . '</label><br/>';
		}
		return $output;
	}
}

if ( ! function_exists( 'trx_addons_ocdi_mailchimp_import' ) ) {
	add_action( 'trx_addons_action_ocdi_import_plugins', 'trx_addons_ocdi_mailchimp_import', 10, 1 );
	/**
	 * Import MailChimp for WP data from the file
	 * 
	 * @hooked trx_addons_action_ocdi_import_plugins
	 *
	 * @param array $import_plugins		List of the plugins slugs to import
	 */
	function trx_addons_ocdi_mailchimp_import( $import_plugins ) {
		if ( trx_addons_exists_mailchimp() && in_array( 'mailchimp-for-wp', $import_plugins ) ) {
			trx_addons_ocdi_import_dump( 'mailchimp' );
			echo esc_html__( 'MailChimp for WP import complete.', 'trx_addons' ) . "\r\n";
		}
	}
}
