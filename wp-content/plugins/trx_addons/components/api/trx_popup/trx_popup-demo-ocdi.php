<?php
/**
 * Plugin support: ThemeREX Pop-Up
 *
 * @package ThemeREX Addons
 * @since v1.5
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

if ( ! function_exists( 'trx_addons_ocdi_trx_popup_set_options' ) ) {
	add_filter( 'trx_addons_filter_ocdi_options', 'trx_addons_ocdi_trx_popup_set_options' );
	/**
	 * Set plugin's specific importer options for OCDI
	 * 
	 * @hooked trx_addons_filter_ocdi_options
	 *
	 * @param array $ocdi_options    Importer options
	 * 
	 * @return array            Modified options
	 */
	function trx_addons_ocdi_trx_popup_set_options( $ocdi_options ) {
		$ocdi_options['import_trx_popup_file_url'] = 'trx_popup.txt';
		return $ocdi_options;		
	}
}

if ( ! function_exists( 'trx_addons_ocdi_trx_popup_export' ) ) {
	add_filter( 'trx_addons_filter_ocdi_export_files', 'trx_addons_ocdi_trx_popup_export' );
	/**
	 * Export ThemeREX Pop-Up data to the file
	 * 
	 * @hooked trx_addons_filter_ocdi_export_files
	 *
	 * @param array $output    List of export files as HTML links to download
	 * 
	 * @return array           Modified list
	 */
	function trx_addons_ocdi_trx_popup_export( $output ) {
		$list = array();
		if ( trx_addons_exists_trx_popup() && in_array( 'trx_popup', trx_addons_ocdi_options( 'required_plugins' ) ) ) {
			// Get plugin data from database
			$options = array( 'trx-popup-options' );
			$list = trx_addons_ocdi_export_options( $options, $list );
			
			// Save as file
			$file_path = TRX_ADDONS_PLUGIN_OCDI . "export/trx_popup.txt";
			trx_addons_fpc( trx_addons_get_file_dir( $file_path ), serialize( $list ) );
			
			// Return file path
			$output .= '<h4><a href="' . trx_addons_get_file_url( $file_path ) . '" download>' . esc_html__( 'ThemeREX Pop-Up', 'trx_addons' ) . '</a></h4>';
		}
		return $output;
	}
}

if ( ! function_exists( 'trx_addons_ocdi_trx_popup_import_field' ) ) {
	add_filter( 'trx_addons_filter_ocdi_import_fields', 'trx_addons_ocdi_trx_popup_import_field' );
	/**
	 * Add checkbox to the one-click importer to allow import ThemeREX Pop-Up data
	 * 
	 * @hooked trx_addons_filter_ocdi_import_fields
	 *
	 * @param array $output    List of checkboxes
	 * 
	 * @return array           Modified list
	 */
	function trx_addons_ocdi_trx_popup_import_field( $output ) {
		if ( trx_addons_exists_trx_popup() && in_array( 'trx_popup', trx_addons_ocdi_options( 'required_plugins' ) ) ) {
			$output .= '<label><input type="checkbox" name="trx_popup" value="trx_popup">' . esc_html__( 'ThemeREX Pop-Up', 'trx_addons' ) . '</label><br/>';
		}
		return $output;
	}
}

if ( ! function_exists( 'trx_addons_ocdi_trx_popup_import' ) ) {
	add_action( 'trx_addons_action_ocdi_import_plugins', 'trx_addons_ocdi_trx_popup_import', 10, 1 );
	/**
	 * Import ThemeREX Pop-Up data from the file
	 * 
	 * @hooked trx_addons_action_ocdi_import_plugins
	 *
	 * @param array $import_plugins    List of the plugins slugs to import
	 */
	function trx_addons_ocdi_trx_popup_import( $import_plugins ) {
		if ( trx_addons_exists_trx_popup() && in_array( 'trx_popup', $import_plugins ) ) {
			trx_addons_ocdi_import_dump('trx_popup');
			echo esc_html__('ThemeREX Pop-Up import complete.', 'trx_addons') . "\r\n";
		}
	}
}