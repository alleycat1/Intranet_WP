<?php
/**
 * Plugin support: Revolution Slider (OCDI support)
 *
 * @package ThemeREX Addons
 * @since v1.0
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

if ( ! function_exists( 'trx_addons_ocdi_revslider_set_options' ) ) {
	add_filter( 'trx_addons_filter_ocdi_options', 'trx_addons_ocdi_revslider_set_options' );
	/**
	 * Set plugin's specific importer options for OCDI
	 * 
	 * @hooked trx_addons_filter_ocdi_options
	 * 
	 * @param array $ocdi_options  OCDI options
	 * 
	 * @return array  		   Modified options
	 */
	function trx_addons_ocdi_revslider_set_options( $ocdi_options ) {
		$ocdi_options['import_revslider_file_url'] = 'revslider.txt';
		return $ocdi_options;		
	}
}

if ( ! function_exists( 'trx_addons_ocdi_revslider_import_field' ) ) {
	add_filter( 'trx_addons_filter_ocdi_import_fields', 'trx_addons_ocdi_revslider_import_field' );
	/**
	 * Add checkbox to the one-click importer to allow import Revolution sliders via OCDI
	 * 
	 * @hooked trx_addons_filter_ocdi_import_fields
	 * 
	 * @param string $output   HTML output with checkboxes
	 * 
	 * @return string  		   Modified output
	 */
	function trx_addons_ocdi_revslider_import_field( $output ) {
		if ( trx_addons_exists_revslider() && in_array( 'revslider', trx_addons_ocdi_options( 'required_plugins' ) ) ) {
			$output .= '<label><input type="checkbox" name="revslider" value="revslider">' . esc_html__( 'Revolution Sliders', 'trx_addons' ) . '</label><br/>';
		}
		return $output;
	}
}

if ( ! function_exists( 'trx_addons_ocdi_revslider_import' ) ) {
	add_action( 'trx_addons_action_ocdi_import_plugins', 'trx_addons_ocdi_revslider_import', 10, 1 );
	/**
	 * Import Revolution sliders via OCDI
	 * 
	 * @hooked trx_addons_action_ocdi_import_plugins
	 * 
	 * @param array $import_plugins   List of plugins to import
	 * 
	 * @return void
	 */
	function trx_addons_ocdi_revslider_import( $import_plugins ) {
		if ( trx_addons_exists_revslider() && in_array( 'revslider', $import_plugins ) ) {			
			if ( file_exists( WP_PLUGIN_DIR . '/revslider/revslider.php' ) ) {
				require_once WP_PLUGIN_DIR . '/revslider/revslider.php';
						
				// Delete all data from tables
				trx_addons_revslider_clear_tables();

				// Get a file with a list of sliders to import
				$files = trx_addons_fga( trx_addons_ocdi_options( 'import_revslider_file_url' ) );
				
				if ( $files ) {
					$demo_url = trx_addons_ocdi_options('demo_url');
					
					if ( ! is_array( $_FILES ) ) {
						$_FILES = array();
					}
					
					// Process next slider
					$slider = new RevSlider();
					
					// Read file by lines
					foreach ($files as $filename) {				
						$filename = str_replace(array("\r\n", "\n", "\r"), '', $filename);			
						$demo_zip = trailingslashit($demo_url) . $filename;
						
						// Download remote file
						if (substr($demo_zip, 0, 5)=='http:' || substr($demo_zip, 0, 6)=='https:') {							
							$upload_dir = (object) wp_upload_dir();
							$revslider_dir = $upload_dir->basedir . '/revslider/';
							$local_zip = $revslider_dir . basename($filename);
							
							// Delete zip if it already exists
							if (file_exists($local_zip)) {
								unlink($local_zip);
							} 
							
							// Upload zip to uploads/revslider folder
							trx_addons_fpc( $local_zip, trx_addons_fgc($demo_zip));
							
							// Import zip to slider
							$response = $slider->importSliderFromPost(true, true, $local_zip);  
							if ($response["success"] == false) {
								dfl(__('Revolution Slider import error.', 'trx_addons'));
							}

							// Delete zip if exists
							if (file_exists($local_zip)) {
								unlink($local_zip);
							} 
						}
					}
				}		
			}
		}
	}
}
