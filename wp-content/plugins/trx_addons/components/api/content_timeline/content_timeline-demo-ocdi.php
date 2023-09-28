<?php
/**
 * Plugin support: Content Timeline (OCDI Support)
 *
 * @package ThemeREX Addons
 * @since v1.0
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

if ( ! function_exists( 'trx_addons_ocdi_content_timeline_set_options' ) ) {
	add_filter( 'trx_addons_filter_ocdi_options', 'trx_addons_ocdi_content_timeline_set_options' );
	/**
	 * Set plugin's specific importer options for OCDI
	 *
	 * @hooked trx_addons_filter_ocdi_options
	 *
	 * @param array $ocdi_options    OCDI options
	 *
	 * @return array                Modified options
	 */
	function trx_addons_ocdi_content_timeline_set_options( $ocdi_options ) {
		$ocdi_options['import_content_timeline_file_url'] = 'content-timeline.txt';
		return $ocdi_options;
	}
}

if ( ! function_exists( 'trx_addons_ocdi_content_timeline_export' ) ) {
	add_filter( 'trx_addons_filter_ocdi_export_files', 'trx_addons_ocdi_content_timeline_export' );
	/**
	 * Export Content Timelines tables
	 *
	 * @hooked trx_addons_filter_ocdi_export_files
	 *
	 * @param array $output    OCDI exported files list
	 *
	 * @return array           Modified files list
	 */
	function trx_addons_ocdi_content_timeline_export( $output ){
		$list = array();
		if ( trx_addons_exists_content_timeline() && in_array( 'content_timeline', trx_addons_ocdi_options( 'required_plugins' ) ) ) {
			// Get plugin data from database			
			$tables = array( 'ctimelines' );
			$list = trx_addons_ocdi_export_tables( $tables, $list );
			
			// Save as file
			$file_path = TRX_ADDONS_PLUGIN_OCDI . "export/content-timeline.txt";
			trx_addons_fpc( trx_addons_get_file_dir( $file_path ), serialize( $list ) );
			
			// Return file path
			$output .= '<h4><a href="' . trx_addons_get_file_url( $file_path ) . '" download>' . esc_html__( 'Content Timeline', 'trx_addons' ) . '</a></h4>';
		}
		return $output;
	}
}

if ( ! function_exists( 'trx_addons_ocdi_content_timeline_import_field' ) ) {
	add_filter( 'trx_addons_filter_ocdi_import_fields', 'trx_addons_ocdi_content_timeline_import_field' );
	/**
	 * Add plugin to import list for OCDI
	 *
	 * @hooked trx_addons_filter_ocdi_import_fields
	 *
	 * @param string $output    OCDI fields HTML
	 *
	 * @return string           Modified fields HTML
	 */
	function trx_addons_ocdi_content_timeline_import_field( $output ) {
		if ( trx_addons_exists_content_timeline() && in_array( 'content_timeline', trx_addons_ocdi_options( 'required_plugins' ) ) ) {
			$output .= '<label><input type="checkbox" name="content_timeline" value="content_timeline">' . esc_html__( 'Content Timeline', 'trx_addons' ) . '</label><br/>';
		}
		return $output;
	}
}

if ( ! function_exists( 'trx_addons_ocdi_content_timeline_import' ) ) {
	add_action( 'trx_addons_action_ocdi_import_plugins', 'trx_addons_ocdi_content_timeline_import', 10, 1 );
	/**
	 * Import Content Timelines tables
	 *
	 * @hooked trx_addons_action_ocdi_import_plugins
	 *
	 * @param array $import_plugins    List of the plugins slugs to import
	 */
	function trx_addons_ocdi_content_timeline_import( $import_plugins ) {
		if ( trx_addons_exists_content_timeline() && in_array( 'content_timeline', $import_plugins ) ) {
			trx_addons_ocdi_import_dump( 'content_timeline' );
			echo esc_html__( 'Content Timeline import complete.', 'trx_addons' ) . "\r\n";
		}
	}
}
