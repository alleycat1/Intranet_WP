<?php
/**
 * Plugin support: PowerKit (OCDI support)
 *
 * @package ThemeREX Addons
 * @since v1.75.3
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

if ( ! function_exists( 'trx_addons_ocdi_powerkit_set_options' ) ) {
	add_filter( 'trx_addons_filter_ocdi_options', 'trx_addons_ocdi_powerkit_set_options' );
	/**
	 * Set plugin's specific importer options for OCDI
	 *
	 * @hooked trx_addons_filter_ocdi_options
	 *
	 * @param array $ocdi_options  OCDI options
	 *
	 * @return array               Modified options
	 */
	function trx_addons_ocdi_powerkit_set_options( $ocdi_options ) {
		$ocdi_options['import_powerkit_file_url'] = 'powerkit.txt';
		return $ocdi_options;		
	}
}

if ( ! function_exists( 'trx_addons_ocdi_powerkit_export' ) ) {
	add_filter( 'trx_addons_filter_ocdi_export_files', 'trx_addons_ocdi_powerkit_export' );
	/**
	 * Export PowerKit data to the file
	 *
	 * @hooked trx_addons_filter_ocdi_export_files
	 *
	 * @param string $output  Export data
	 *
	 * @return string         Modified output
	 */
	function trx_addons_ocdi_powerkit_export( $output ) {
		$list = array();
		if ( trx_addons_exists_powerkit() && in_array( 'powerkit', trx_addons_ocdi_options( 'required_plugins' ) ) ) {
			// Get plugin data from database
			$options = array('powerkit_enabled_%');
			$list = trx_addons_ocdi_export_options( $options, $list );
			
			// Save as file
			$file_path = TRX_ADDONS_PLUGIN_OCDI . "export/powerkit.txt";
			trx_addons_fpc( trx_addons_get_file_dir( $file_path ), serialize( $list ) );
			
			// Return file path
			$output .= '<h4><a href="' . trx_addons_get_file_url( $file_path ) . '" download>' . esc_html__( 'PowerKit', 'trx_addons' ) . '</a></h4>';
		}
		return $output;
	}
}

if ( ! function_exists( 'trx_addons_ocdi_powerkit_import_field' ) ) {
	add_filter( 'trx_addons_filter_ocdi_import_fields', 'trx_addons_ocdi_powerkit_import_field' );
	/**
	 * Add checkbox to the one-click importer to allow import PowerKit data
	 *
	 * @hooked trx_addons_filter_ocdi_import_fields
	 *
	 * @param string $output  Import fields HTML output
	 *
	 * @return string         Modified output
	 */
	function trx_addons_ocdi_powerkit_import_field( $output ) {
		if ( trx_addons_exists_powerkit() && in_array( 'powerkit', trx_addons_ocdi_options( 'required_plugins' ) ) ) {
			$output .= '<label><input type="checkbox" name="powerkit" value="powerkit">' . esc_html__( 'PowerKit', 'trx_addons' ) . '</label><br/>';
		}
		return $output;
	}
}

if ( ! function_exists( 'trx_addons_ocdi_powerkit_import' ) ) {
	add_action( 'trx_addons_action_ocdi_import_plugins', 'trx_addons_ocdi_powerkit_import', 10, 1 );
	/**
	 * Import PowerKit data from the file
	 *
	 * @param array $import_plugins  List of plugins to import
	 */
	function trx_addons_ocdi_powerkit_import( $import_plugins ) {
		if ( trx_addons_exists_powerkit() && in_array( 'powerkit', $import_plugins ) ) {
			trx_addons_ocdi_import_dump('powerkit');
			echo esc_html__('PowerKit import complete.', 'trx_addons') . "\r\n";
		}
	}
}
