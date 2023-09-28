<?php
/**
 * Plugin support: Essential Grid (OCDI support)
 *
 * @package ThemeREX Addons
 * @since v1.5
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

if ( ! function_exists( 'trx_addons_ocdi_essential_grid_set_options' ) ) {
	add_filter( 'trx_addons_filter_ocdi_options', 'trx_addons_ocdi_essential_grid_set_options' );
	/**
	 * Set plugin's specific importer options for OCDI
	 * 
	 * @hooked trx_addons_filter_ocdi_options
	 * 
	 * @param array $ocdi_options  OCDI options
	 * 
	 * @return array  		   Modified options
	 */
	function trx_addons_ocdi_essential_grid_set_options( $ocdi_options ) {
		$ocdi_options['import_essential_grid_file_url'] = 'ess_grid.json';
		return $ocdi_options;
	}
}

if ( ! function_exists( 'trx_addons_ocdi_essential_grid_import_field' ) ) {
	add_filter( 'trx_addons_filter_ocdi_import_fields', 'trx_addons_ocdi_essential_grid_import_field' );
	/**
	 * Add plugin's name to the import list for OCDI
	 * 
	 * @hooked trx_addons_filter_ocdi_import_fields
	 * 
	 * @param string $output   HTML output with checkboxes
	 * 
	 * @return string  		   Modified output
	 */
	function trx_addons_ocdi_essential_grid_import_field( $output ) {
		if ( trx_addons_exists_essential_grid() && in_array( 'essential-grid', trx_addons_ocdi_options('required_plugins' ) ) ) {
			$output .= '<label><input type="checkbox" name="essential-grid" value="essential-grid">'
							. esc_html__( 'Essential Grid', 'trx_addons' )
						. '</label><br/>';
		}
		return $output;
	}
}

if ( ! function_exists( 'trx_addons_ocdi_essential_grid_import' ) ) {
	add_action( 'trx_addons_action_ocdi_import_plugins', 'trx_addons_ocdi_essential_grid_import', 10, 1 );
	/**
	 * Import Essential Grid data
	 * 
	 * @hooked trx_addons_action_ocdi_import_plugins
	 * 
	 * @param array $import_plugins  List of plugins to import
	 */
	function trx_addons_ocdi_essential_grid_import( $import_plugins ) {
		if ( trx_addons_exists_essential_grid() && in_array( 'essential-grid', $import_plugins ) ) {
			// Delete all data from tables
			trx_addons_essential_grid_clear_tables();
			
			// Get Essential Grid export file
			$json = trx_addons_ocdi_options( 'import_essential_grid_file_url' );
			
			// Read JSON file
			$txt = trx_addons_fgc( $json );
			trx_addons_essential_grid_import( $txt );
		}
	}
}
