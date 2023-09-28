<?php
/**
 * Plugin support: Instagram Feed (OCDI support)
 *
 * @package ThemeREX Addons
 * @since v1.5
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

if ( ! function_exists( 'trx_addons_ocdi_instagram_feed_set_options' ) ) {
	add_filter( 'trx_addons_filter_ocdi_options', 'trx_addons_ocdi_instagram_feed_set_options' );
	/**
	 * Set plugin's specific importer options for OCDI
	 * 
	 * @hooked trx_addons_filter_ocdi_options
	 * 
	 * @param array $ocdi_options		Options to set
	 * 
	 * @return array					Modified options
	 */
	function trx_addons_ocdi_instagram_feed_set_options( $ocdi_options ) {
		$ocdi_options['import_instagram-feed_file_url'] = 'instagram-feed.txt';
		return $ocdi_options;		
	}
}

if ( ! function_exists( 'trx_addons_ocdi_instagram_feed_export' ) ) {
	add_filter( 'trx_addons_filter_ocdi_export_files', 'trx_addons_ocdi_instagram_feed_export' );
	/**
	 * Export Instagram Feed data
	 * 
	 * @hooked trx_addons_filter_ocdi_export_files
	 * 
	 * @param array $output		An HTML output with exported files
	 * 
	 * @return array			Modified output
	 */
	function trx_addons_ocdi_instagram_feed_export( $output ) {
		$list = array();
		if ( trx_addons_exists_instagram_feed() && in_array( 'instagram-feed', trx_addons_ocdi_options( 'required_plugins' ) ) ) {
			// Get plugin data from database
			$options = array( 'sb_instagram_settings' );
			$list = trx_addons_ocdi_export_options( $options, $list );
			
			// Save as file
			$file_path = TRX_ADDONS_PLUGIN_OCDI . "export/instagram-feed.txt";
			trx_addons_fpc( trx_addons_get_file_dir( $file_path ), serialize( $list ) );
			
			// Return file path
			$output .= '<h4><a href="' . trx_addons_get_file_url( $file_path ) . '" download>' . esc_html__( 'Instagram feed', 'trx_addons' ) . '</a></h4>';
		}
		return $output;
	}
}

if ( ! function_exists( 'trx_addons_ocdi_instagram_feed_import_field' ) ) {
	add_filter( 'trx_addons_filter_ocdi_import_fields', 'trx_addons_ocdi_instagram_feed_import_field' );
	/**
	 * Display checkbox to import Instagram Feed data in OCDI importer
	 * 
	 * @hooked trx_addons_filter_ocdi_import_fields
	 * 
	 * @param string $output		An HTML output with checkboxes
	 * 
	 * @return string				Modified output
	 */
	function trx_addons_ocdi_instagram_feed_import_field( $output ) {
		if ( trx_addons_exists_instagram_feed() && in_array( 'instagram-feed', trx_addons_ocdi_options( 'required_plugins' ) ) ) {
			$output .= '<label><input type="checkbox" name="instagram-feed" value="instagram-feed">' . esc_html__( 'Instagram Feed', 'trx_addons' ) . '</label><br/>';
		}
		return $output;
	}
}

if ( ! function_exists( 'trx_addons_ocdi_instagram_feed_import' ) ) {
	add_action( 'trx_addons_action_ocdi_import_plugins', 'trx_addons_ocdi_instagram_feed_import', 10, 1 );
	/**
	 * Import Instagram Feed data
	 * 
	 * @hooked trx_addons_action_ocdi_import_plugins
	 * 
	 * @param array $import_plugins		An array of plugins to import
	 */
	function trx_addons_ocdi_instagram_feed_import( $import_plugins ) {
		if ( trx_addons_exists_instagram_feed() && in_array( 'instagram-feed', $import_plugins ) ) {
			trx_addons_ocdi_import_dump('instagram-feed');
			echo esc_html__('Instagram Feed import complete.', 'trx_addons') . "\r\n";
		}
	}
}
