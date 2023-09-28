<?php
/**
 * Plugin support: WPML (OCDI support)
 *
 * @package ThemeREX Addons
 * @since v1.6.38
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


if ( ! function_exists( 'trx_addons_ocdi_wpml_set_options' ) ) {
	add_filter( 'trx_addons_filter_ocdi_options', 'trx_addons_ocdi_wpml_set_options' );
	/**
	 * Set plugin's specific importer options for OCDI
	 * 
	 * @hooked trx_addons_filter_ocdi_options
	 *
	 * @param array $ocdi_options		Importer options
	 * 
	 * @return array				Modified options
	 */
	function trx_addons_ocdi_wpml_set_options( $ocdi_options ) {
		$ocdi_options['import_sitepress-multilingual-cms_file_url'] = 'sitepress-multilingual-cms.txt';
		return $ocdi_options;		
	}
}

if ( ! function_exists( 'trx_addons_ocdi_wpml_export' ) ) {
	add_filter( 'trx_addons_filter_ocdi_export_files', 'trx_addons_ocdi_wpml_export' );
	/**
	 * Export WPML settings to the file for OCDI
	 * 
	 * @hooked trx_addons_filter_ocdi_export_files
	 *
	 * @param string $output	Export output - list of files to allow download
	 * 
	 * @return string			Modified output
	 */
	function trx_addons_ocdi_wpml_export( $output ) {
		$list = array();
		if ( trx_addons_exists_wpml() && in_array( 'sitepress-multilingual-cms', trx_addons_ocdi_options( 'required_plugins' ) ) ) {
			// Get plugin data from database
			$options = array('icl_sitepress_settings');
			$list = trx_addons_ocdi_export_options($options, $list);
			
			// Save as file
			$file_path = TRX_ADDONS_PLUGIN_OCDI . "export/sitepress-multilingual-cms.txt";
			trx_addons_fpc( trx_addons_get_file_dir( $file_path ), serialize( $list ) );
			
			// Return file path
			$output .= '<h4><a href="' . trx_addons_get_file_url( $file_path ) . '" download>' . esc_html__( 'WPML - Sitepress Multilingual CMS', 'trx_addons' ) . '</a></h4>';
		}
		return $output;
	}
}

if ( ! function_exists( 'trx_addons_ocdi_wpml_import_field' ) ) {
	add_filter( 'trx_addons_filter_ocdi_import_fields', 'trx_addons_ocdi_wpml_import_field' );
	/**
	 * Add checkbox with a plugin name to the list to allow import it's data
	 * 
	 * @hooked trx_addons_filter_ocdi_import_fields
	 *
	 * @param string $output	HTML layout with a list of checkboxes
	 * 
	 * @return string			Modified output
	 */
	function trx_addons_ocdi_wpml_import_field( $output ) {
		if ( trx_addons_exists_wpml() && in_array( 'sitepress-multilingual-cms', trx_addons_ocdi_options( 'required_plugins' ) ) ) {
			$output .= '<label><input type="checkbox" name="sitepress-multilingual-cms" value="sitepress-multilingual-cms">' . esc_html__( 'WPML - Sitepress Multilingual CMS', 'trx_addons' ) . '</label><br/>';
		}
		return $output;
	}
}

if ( ! function_exists( 'trx_addons_ocdi_wpml_import' ) ) {
	add_action( 'trx_addons_action_ocdi_import_plugins', 'trx_addons_ocdi_wpml_import', 10, 1 );
	/**
	 * Import WPML settings from the file via OCDI
	 * 
	 * @hooked trx_addons_action_ocdi_import_plugins
	 *
	 * @param array $import_plugins		List of plugins to import
	 */
	function trx_addons_ocdi_wpml_import( $import_plugins ) {
		if ( trx_addons_exists_wpml() && in_array( 'sitepress-multilingual-cms', $import_plugins ) ) {
			trx_addons_ocdi_import_dump( 'sitepress-multilingual-cms' );
			echo esc_html__('WPML - Sitepress Multilingual CMS import complete.', 'trx_addons') . "\r\n";
		}
	}
}
