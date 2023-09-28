<?php
/**
 * Plugin support: WPBakery PageBuilder (OCDI support)
 *
 * @package ThemeREX Addons
 * @since v1.0
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

if ( ! function_exists( 'trx_addons_ocdi_vc_set_options' ) ) {
	add_filter( 'trx_addons_filter_ocdi_options', 'trx_addons_ocdi_vc_set_options' );
	/**
	 * Set plugin's specific importer options for OCDI
	 * 
	 * @hooked trx_addons_filter_ocdi_options
	 *
	 * @param array $ocdi_options		Importer options
	 * 
	 * @return array				Modified options
	 */
	function trx_addons_ocdi_vc_set_options( $ocdi_options ) {
		$ocdi_options['import_js_composer_file_url'] = 'js_composer.txt';
		return $ocdi_options;		
	}
}

if ( ! function_exists( 'trx_addons_ocdi_vc_export' ) ) {
	add_filter( 'trx_addons_filter_ocdi_export_files', 'trx_addons_ocdi_vc_export' );
	/**
	 * Export WPBakery PageBuilder data to the file via OCDI
	 * 
	 * @hooked trx_addons_filter_ocdi_export_files
	 *
	 * @param string $output		Export data
	 * 
	 * @return string				Modified output
	 */
	function trx_addons_ocdi_vc_export( $output ) {
		$list = array();
		if ( trx_addons_exists_vc() && in_array( 'js_composer', trx_addons_ocdi_options( 'required_plugins' ) ) ) {
			// Get plugin data from database
			$options = array('wpb_js_templates');
			$list = trx_addons_ocdi_export_options( $options, $list );
			
			// Save as file
			$file_path = TRX_ADDONS_PLUGIN_OCDI . "export/js_composer.txt";
			trx_addons_fpc( trx_addons_get_file_dir( $file_path ), serialize( $list ) );
			
			// Return file path
			$output .= '<h4><a href="' . trx_addons_get_file_url( $file_path ) . '" download>' . esc_html__( 'WPBakery PageBuilder', 'trx_addons' ) . '</a></h4>';
		}
		return $output;
	}
}

if ( ! function_exists( 'trx_addons_ocdi_vc_import_field' ) ) {
	add_filter( 'trx_addons_filter_ocdi_import_fields', 'trx_addons_ocdi_vc_import_field' );
	/**
	 * Add a plugin name to the fields for OCDI
	 * 
	 * @hooked trx_addons_filter_ocdi_import_fields
	 *
	 * @param string $output		Import fields HTML
	 * 
	 * @return string				Modified output
	 */
	function trx_addons_ocdi_vc_import_field( $output ) {
		if ( trx_addons_exists_vc() && in_array( 'js_composer', trx_addons_ocdi_options( 'required_plugins' ) ) ) {
			$output .= '<label><input type="checkbox" name="js_composer" value="js_composer">' . esc_html__( 'WPBakery PageBuilder', 'trx_addons' ) . '</label><br/>';
		}
		return $output;
	}
}

if ( ! function_exists( 'trx_addons_ocdi_vc_import' ) ) {
	add_action( 'trx_addons_action_ocdi_import_plugins', 'trx_addons_ocdi_vc_import', 10, 1 );
	/**
	 * Import WPBakery PageBuilder data from the file
	 * 
	 * @hooked trx_addons_action_ocdi_import_plugins
	 *
	 * @param array $import_plugins		List of the plugins slugs to import
	 */
	function trx_addons_ocdi_vc_import( $import_plugins ) {
		if ( trx_addons_exists_vc() && in_array( 'js_composer', $import_plugins ) ) {
			trx_addons_ocdi_import_dump( 'js_composer' );
			echo esc_html__('WPBakery PageBuilder import complete.', 'trx_addons') . "\r\n";
		}
	}
}
