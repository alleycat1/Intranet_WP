<?php
/**
 * Plugin support: Elementor (OCDI support)
 *
 * @package ThemeREX Addons
 * @since v1.0
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

if ( ! function_exists( 'trx_addons_ocdi_elementor_set_options' ) ) {
	add_filter( 'trx_addons_filter_ocdi_options', 'trx_addons_ocdi_elementor_set_options' );
	/**
	 * Set plugin's specific importer options for OCDI
	 * 
	 * @hooked trx_addons_filter_ocdi_options
	 * 
	 * @param array $ocdi_options		Options to set
	 * 
	 * @return array					Modified options
	 */
	function trx_addons_ocdi_elementor_set_options( $ocdi_options ) {
		$ocdi_options['import_elementor_file_url'] = 'elementor.txt';
		return $ocdi_options;
	}
}

if ( ! function_exists( 'trx_addons_ocdi_elementor_export' ) ) {
	add_filter( 'trx_addons_filter_ocdi_export_files', 'trx_addons_ocdi_elementor_export' );
	/**
	 * Export Elementor data (callback for OCDI)
	 * 
	 * @hooked trx_addons_filter_ocdi_export_files
	 * 
	 * @param string $output		Exported files list
	 * 
	 * @return string				Modified files list
	 */
	function trx_addons_ocdi_elementor_export( $output ) {
		$list = array();
		if ( trx_addons_exists_elementor() && in_array( 'elementor', trx_addons_ocdi_options( 'required_plugins' ) ) ) {
			// Get plugin data from database
			$options = array('elementor%');
			$list = trx_addons_ocdi_export_options( $options, $list );
		
			// Save as file
			$file_path = TRX_ADDONS_PLUGIN_OCDI . "export/elementor.txt";
			trx_addons_fpc( trx_addons_get_file_dir( $file_path ), serialize( $list ) );
			
			// Return file path
			$output .= '<h4><a href="' . trx_addons_get_file_url( $file_path ) . '" download>'
							. esc_html__( 'Elementor (free PageBuilder)', 'trx_addons' )
						. '</a></h4>';
		}
		return $output;
	}
}

if ( ! function_exists( 'trx_addons_ocdi_elementor_import_field' ) ) {
	add_filter( 'trx_addons_filter_ocdi_import_fields', 'trx_addons_ocdi_elementor_import_field' );
	/**
	 * Add checkbox with a plugin name to the OCDI list
	 * 
	 * @hooked trx_addons_filter_ocdi_import_fields
	 * 
	 * @param string $output		Import fields HTML
	 * 
	 * @return string				Modified fields HTML
	 */
	function trx_addons_ocdi_elementor_import_field( $output ) {
		$list = array();
		if ( trx_addons_exists_elementor() && in_array( 'elementor', trx_addons_ocdi_options( 'required_plugins' ) ) ) {
			$output .= '<label><input type="checkbox" name="elementor" value="elementor">'
							. esc_html__( 'Elementor (free PageBuilder)', 'trx_addons' )
						. '</label><br/>';
		}
		return $output;
	}
}

if ( ! function_exists( 'trx_addons_ocdi_elementor_import' ) ) {
	add_action( 'trx_addons_action_ocdi_import_plugins', 'trx_addons_ocdi_elementor_import', 10, 1 );
	/**
	 * Import Elementor data
	 * 
	 * @hooked trx_addons_action_ocdi_import_plugins
	 * 
	 * @param array $import_plugins		List of plugins to import
	 */
	function trx_addons_ocdi_elementor_import( $import_plugins ) {
		if ( trx_addons_exists_elementor() && in_array( 'elementor', $import_plugins ) ) {
			trx_addons_ocdi_import_dump( 'elementor' );
			echo esc_html__( 'Elementor import complete.', 'trx_addons' ) . "\r\n";
		}
	}
}

if ( ! function_exists( 'trx_addons_ocdi_elementor_post_meta' ) ) {
	add_filter( 'trx_addons_filter_ocdi_process_post_meta', 'trx_addons_ocdi_elementor_post_meta', 10, 2 );
	/**
	 * Add Elementor's post meta keys to the list of the keys to export
	 * 
	 * @hooked trx_addons_filter_ocdi_process_post_meta
	 * 
	 * @param array $keys			List of post meta keys to export
	 * @param array $import_plugins	List of supported plugins
	 * 
	 * @return array				Modified list of post meta keys to export
	 */
	function trx_addons_ocdi_elementor_post_meta( $keys, $import_plugins ) {
		if ( trx_addons_exists_elementor() && in_array( 'elementor', $import_plugins ) ) {
			return array_merge( $keys, array( '_elementor_data', '_elementor_css', '_elementor_page_settings' ) );
		} else {
			return $keys;
		}
	}
}
