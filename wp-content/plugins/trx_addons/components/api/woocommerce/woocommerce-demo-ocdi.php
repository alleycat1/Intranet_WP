<?php
/**
 * Plugin support: WooCommerce (OCDI support)
 *
 * @package ThemeREX Addons
 * @since v1.5
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

if ( ! function_exists( 'trx_addons_ocdi_woocommerce_set_options' ) ) {
	add_filter( 'trx_addons_filter_ocdi_options', 'trx_addons_ocdi_woocommerce_set_options' );
	/**
	 * Set plugin's specific importer options for OCDI
	 * 
	 * @trigger trx_addons_filter_ocdi_options
	 * 
	 * @param array $ocdi_options  OCDI options
	 */
	function trx_addons_ocdi_woocommerce_set_options( $ocdi_options ) {
		$ocdi_options['import_woocommerce_file_url'] = 'woocommerce.txt';
		return $ocdi_options;		
	}
}

if ( ! function_exists( 'trx_addons_ocdi_woocommerce_export' ) ) {
	add_filter( 'trx_addons_filter_ocdi_export_files', 'trx_addons_ocdi_woocommerce_export' );
	/**
	 * Export WooCommerce data to the file
	 * 
	 * @trigger trx_addons_filter_ocdi_export_files
	 * 
	 * @param string $output  Export files list
	 */
	function trx_addons_ocdi_woocommerce_export( $output ) {
		$list = array();
		if ( trx_addons_exists_woocommerce() && in_array( 'woocommerce', trx_addons_ocdi_options( 'required_plugins' ) ) ) {
			// Get plugin data from database	
			$tables = array(
							'woocommerce_attribute_taxonomies',
							'woocommerce_downloadable_product_permissions',
							'woocommerce_order_itemmeta',
							'woocommerce_order_items',
							'woocommerce_termmeta'
						);
			$list = trx_addons_ocdi_export_tables( $tables, $list );

			$options = array( 'shop_%', 'woocommerce_%' );
			$list = trx_addons_ocdi_export_options( $options, $list );
			
			// Save as file
			$file_path = TRX_ADDONS_PLUGIN_OCDI . "export/woocommerce.txt";
			trx_addons_fpc( trx_addons_get_file_dir( $file_path ), serialize( $list ) );
			
			// Return file path
			$output .= '<h4><a href="' . trx_addons_get_file_url( $file_path ) . '" download>' . esc_html__( 'WooCommerce', 'trx_addons' ) . '</a></h4>';
		}
		return $output;
	}
}

if ( ! function_exists( 'trx_addons_ocdi_woocommerce_import_field' ) ) {
	add_filter( 'trx_addons_filter_ocdi_import_fields', 'trx_addons_ocdi_woocommerce_import_field' );
	/**
	 * Add checkbox with a plugin name to the one-click importer
	 * 
	 * @trigger trx_addons_filter_ocdi_import_fields
	 * 
	 * @param string $output  Import fields HTML
	 * 
	 * @return string  Modified HTML
	 */
	function trx_addons_ocdi_woocommerce_import_field( $output ) {
		if ( trx_addons_exists_woocommerce() && in_array( 'woocommerce', trx_addons_ocdi_options( 'required_plugins' ) ) ) {
			$output .= '<label><input type="checkbox" name="woocommerce" value="woocommerce">' . esc_html__( 'WooCommerce', 'trx_addons' ) . '</label><br/>';
		}
		return $output;
	}
}

if ( ! function_exists( 'trx_addons_ocdi_woocommerce_import' ) ) {
	add_action( 'trx_addons_action_ocdi_import_plugins', 'trx_addons_ocdi_woocommerce_import', 10, 1 );
	/**
	 * Import WooCommerce data from the file
	 * 
	 * @trigger trx_addons_action_ocdi_import_plugins
	 * 
	 * @param array $import_plugins  List of plugins to import
	 */
	function trx_addons_ocdi_woocommerce_import( $import_plugins){
		if ( trx_addons_exists_woocommerce() && in_array( 'woocommerce', $import_plugins ) ) {
			trx_addons_ocdi_import_dump( 'woocommerce' );
			echo esc_html__('WooCommerce import complete.', 'trx_addons') . "\r\n";	
		}
	}
}
