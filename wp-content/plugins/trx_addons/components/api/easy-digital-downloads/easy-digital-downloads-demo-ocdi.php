<?php
/**
 * Plugin support: Easy Digital Downloads (OCDI Support)
 *
 * @package ThemeREX Addons
 * @since v1.6.29
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

if ( ! function_exists( 'trx_addons_edd_ocdi_set_options' ) ) {
	add_filter( 'trx_addons_filter_ocdi_options', 'trx_addons_edd_ocdi_set_options' );
	/**
	 * Add plugin's specific options to the list of options for the OCDI plugin
	 *
	 * @hooked trx_addons_filter_ocdi_options
	 *
	 * @param array $ocdi_options   OCDI options
	 *
	 * @return array                Modified options
	 */
	function trx_addons_edd_ocdi_set_options( $ocdi_options ) {
		$ocdi_options['import_easy_digital_downloads_file_url'] = 'easy-digital-downloads.txt';
		return $ocdi_options;
	}
}

if ( ! function_exists( 'trx_addons_edd_ocdi_export' ) ) {
	add_filter( 'trx_addons_filter_ocdi_export_files', 'trx_addons_edd_ocdi_export' );
	/**
	 * Export Easy Digital Downloads options to the file
	 *
	 * @hooked trx_addons_filter_ocdi_export_files
	 *
	 * @param array $output   Files list
	 *
	 * @return array          Modified files list
	 */
	function trx_addons_edd_ocdi_export( $output ) {
		$list = array();
		if ( trx_addons_exists_edd() && in_array('easy-digital-downloads', trx_addons_ocdi_options( 'required_plugins' ) ) ) {
			// Get plugin data from database
			$options = array( 'edd_settings' );
			$list = trx_addons_ocdi_export_options( $options, $list );
			
			// Save as file
			$file_path = TRX_ADDONS_PLUGIN_OCDI . "export/easy-digital-downloads.txt";
			trx_addons_fpc( trx_addons_get_file_dir( $file_path ), serialize( $list ) );
			
			// Return file path
			$output .= '<h4><a href="' . trx_addons_get_file_url( $file_path ) . '" download>' . esc_html__( 'Easy Digital Downloads', 'trx_addons' ) . '</a></h4>';
		}
		return $output;
	}
}

if ( ! function_exists( 'trx_addons_edd_ocdi_import_field' ) ) {
	add_filter( 'trx_addons_filter_ocdi_import_fields', 'trx_addons_edd_ocdi_import_field' );
	/**
	 * Add plugin to the import list for the OCDI plugin
	 *
	 * @hooked trx_addons_filter_ocdi_import_fields
	 *
	 * @param array $output   Fields list
	 *
	 * @return array          Modified fields list
	 */
	function trx_addons_edd_ocdi_import_field( $output ){
		$list = array();
		if ( trx_addons_exists_edd() && in_array( 'easy-digital-downloads', trx_addons_ocdi_options( 'required_plugins' ) ) ) {
			$output .= '<label><input type="checkbox" name="easy-digital-downloads" value="easy-digital-downloads">' . esc_html__( 'Easy Digital Downloads', 'trx_addons' ) . '</label><br/>';
		}
		return $output;
	}
}

if ( ! function_exists( 'trx_addons_edd_ocdi_import' ) ) {	
	add_action( 'trx_addons_action_ocdi_import_plugins', 'trx_addons_edd_ocdi_import', 10, 1 );
	/**
	 * Import Easy Digital Downloads data from the file
	 *
	 * @hooked trx_addons_action_ocdi_import_plugins
	 *
	 * @param array $import_plugins   Plugins list to import
	 *
	 * @return array                  Plugins list
	 */
	function trx_addons_edd_ocdi_import( $import_plugins ){		
		if ( trx_addons_exists_edd() && in_array( 'easy-digital-downloads', $import_plugins ) ) {
			trx_addons_ocdi_import_dump( 'easy_digital_downloads' );
			echo esc_html__( 'Easy Digital Downloads import complete.', 'trx_addons' ) . "\r\n";
		}
	}
}

if ( ! function_exists( 'trx_addons_edd_ocdi_post_meta' ) ) {
	add_filter( 'trx_addons_filter_ocdi_process_post_meta', 'trx_addons_edd_ocdi_post_meta', 10, 2 );
	/**
	 * Add plugin's specific post meta to the list of post meta for the OCDI plugin
	 *
	 * @hooked trx_addons_filter_ocdi_process_post_meta
	 *
	 * @param array $keys             Post meta keys
	 * @param array $import_plugins   Plugins list to import
	 *
	 * @return array        Modified post meta keys
	 */
	function trx_addons_edd_ocdi_post_meta( $keys, $import_plugins ){
		if ( trx_addons_exists_edd() && in_array( 'easy-digital-downloads', $import_plugins ) ) {
			return array_merge( $keys, array( 'edd_download_files' ) );
		} else {
			return $keys;
		}
	}
}
