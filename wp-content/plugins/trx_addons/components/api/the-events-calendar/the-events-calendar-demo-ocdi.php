<?php
/**
 * Plugin support: The Events Calendar (OCDI support)
 *
 * @package ThemeREX Addons
 * @since v1.2
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

if ( ! function_exists( 'trx_addons_ocdi_tribe_events_set_options' ) ) {
	add_filter( 'trx_addons_filter_ocdi_options', 'trx_addons_ocdi_tribe_events_set_options' );
	/**
	 * Set plugin's specific importer options for OCDI
	 * 
	 * @hooked trx_addons_filter_ocdi_options
	 * 
	 * @param array $ocdi_options    Importer options
	 * 
	 * @return array            Modified options
	 */
	function trx_addons_ocdi_tribe_events_set_options( $ocdi_options ) {
		$ocdi_options['import_tribe_events_file_url'] = 'tribe_events.txt';
		return $ocdi_options;		
	}
}

if ( ! function_exists( 'trx_addons_ocdi_tribe_events_export' ) ) {
	add_filter( 'trx_addons_filter_ocdi_export_files', 'trx_addons_ocdi_tribe_events_export' );
	/**
	 * Export Tribe Events Calendar data to the file
	 * 
	 * @hooked trx_addons_filter_ocdi_export_files
	 * 
	 * @param array $output     HTML list with exported files
	 * 
	 * @return array            Modified list
	 */
	function trx_addons_ocdi_tribe_events_export( $output ) {
		$list = array();
		if ( trx_addons_exists_tribe_events() && in_array( 'tribe_events', trx_addons_ocdi_options( 'required_plugins' ) ) ) {
			// Get plugin data from database
			$options = array( 'tribe_events_calendar_options' );
			$list = trx_addons_ocdi_export_options( $options, $list );
			
			// Save as file
			$file_path = TRX_ADDONS_PLUGIN_OCDI . "export/tribe_events.txt";
			trx_addons_fpc( trx_addons_get_file_dir( $file_path ), serialize( $list ) );
			
			// Return file path
			$output .= '<h4><a href="' . trx_addons_get_file_url( $file_path ) . '" download>' . esc_html__( 'Tribe Events Calendar', 'trx_addons' ) . '</a></h4>';
		}
		return $output;
	}
}

if ( ! function_exists( 'trx_addons_ocdi_tribe_events_import_field' ) ) {
	add_filter( 'trx_addons_filter_ocdi_import_fields', 'trx_addons_ocdi_tribe_events_import_field' );
	/**
	 * Add checkbox with a plugin name to the one-click importer
	 * 
	 * @hooked trx_addons_filter_ocdi_import_fields
	 * 
	 * @param array $output     HTML output
	 * 
	 * @return array            Modified output
	 */
	function trx_addons_ocdi_tribe_events_import_field( $output ) {
		if ( trx_addons_exists_tribe_events() && in_array( 'tribe_events', trx_addons_ocdi_options( 'required_plugins' ) ) ) {
			$output .= '<label><input type="checkbox" name="tribe_events" value="tribe_events">' . esc_html__( 'Tribe Events Calendar', 'trx_addons' ) . '</label><br/>';
		}
		return $output;
	}
}

if ( ! function_exists( 'trx_addons_ocdi_tribe_events_import' ) ) {
	add_action( 'trx_addons_action_ocdi_import_plugins', 'trx_addons_ocdi_tribe_events_import', 10, 1 );
	/**
	 * Import Tribe Events Calendar data from the file
	 * 
	 * @hooked trx_addons_action_ocdi_import_plugins
	 * 
	 * @param array $import_plugins    List of the plugins slugs to import
	 */
	function trx_addons_ocdi_tribe_events_import( $import_plugins){
		if ( trx_addons_exists_tribe_events() && in_array( 'tribe_events', $import_plugins ) ) {
			trx_addons_ocdi_import_dump( 'tribe_events' );
			echo esc_html__( 'Tribe Events Calendar import complete.', 'trx_addons' ) . "\r\n";
		}
	}
}
