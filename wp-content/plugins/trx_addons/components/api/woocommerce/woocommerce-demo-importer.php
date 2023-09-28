<?php
/**
 * Plugin support: WooCommerce (Importer support)
 *
 * @package ThemeREX Addons
 * @since v1.5
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

if ( ! function_exists( 'trx_addons_woocommerce_importer_required_plugins' ) ) {
	add_filter( 'trx_addons_filter_importer_required_plugins',	'trx_addons_woocommerce_importer_required_plugins', 10, 2 );
	/**
	 * Check if the required plugins are installed
	 * 
	 * @hooked trx_addons_filter_importer_required_plugins
	 *
	 * @param string $not_installed  Not installed plugins list as string
	 * @param string $list  	     Plugins list to check as string
	 * 
	 * @return string                Not installed plugins list as string
	 */
	function trx_addons_woocommerce_importer_required_plugins( $not_installed = '', $list = '' ) {
		if ( strpos( $list, 'woocommerce' ) !== false && ! trx_addons_exists_woocommerce() ) {
			$not_installed .= '<br>' . esc_html__('WooCommerce', 'trx_addons');
		}
		return $not_installed;
	}
}

if ( ! function_exists( 'trx_addons_woocommerce_importer_set_options' ) ) {
	add_filter( 'trx_addons_filter_importer_options', 'trx_addons_woocommerce_importer_set_options' );
	/**
	 * Set plugin's specific importer options
	 * 
	 * @hooked trx_addons_filter_importer_options
	 *
	 * @param array $options  Importer options
	 * 
	 * @return array          Modified options
	 */
	function trx_addons_woocommerce_importer_set_options( $options = array() ) {
		if ( trx_addons_exists_woocommerce() && in_array( 'woocommerce', $options['required_plugins'] ) ) {
			$options['additional_options'][]	= 'shop_%';					// Add slugs to export options for this plugin
			$options['additional_options'][]	= 'woocommerce_%';
			if ( is_array( $options['files'] ) && count( $options['files'] ) > 0 ) {
				foreach ( $options['files'] as $k => $v ) {
					$options['files'][$k]['file_with_woocommerce'] = str_replace( 'name.ext', 'woocommerce.txt', $v['file_with_'] );
				}
			}
		}
		return $options;
	}
}

if ( ! function_exists( 'trx_addons_woocommerce_importer_check_options' ) ) {
	add_filter( 'trx_addons_filter_import_theme_options', 'trx_addons_woocommerce_importer_check_options', 10, 4 );
	/**
	 * Prevent to import plugin's specific options if a plugin is not installed
	 * 
	 * @hooked trx_addons_filter_import_theme_options
	 * 
	 * @trigger trx_addons_filter_import_theme_options
	 *
	 * @param string $allow    Allow to import or not
	 * @param string $k        Option's key
	 * @param string $v        Option's value
	 * @param array  $options  Importer options
	 * 
	 * @return string          Allow to import or not
	 */
	function trx_addons_woocommerce_importer_check_options( $allow, $k, $v, $options ) {
		if ( $allow && ( strpos( $k, 'woocommerce_' ) === 0 || strpos( $k, 'shop_' ) === 0 ) ) {
			$allow = trx_addons_exists_woocommerce()
						&& in_array( 'woocommerce', $options['required_plugins'] )
						&& $k != 'woocommerce_queue_flush_rewrite_rules';
		}
		return $allow;
	}
}

if ( ! function_exists( 'trx_addons_woocommerce_importer_after_import_posts' ) ) {
	add_action( 'trx_addons_action_importer_after_import_posts', 'trx_addons_woocommerce_importer_after_import_posts', 10, 1 );
	/**
	 * Set WooCommerce pages after import posts is complete
	 * 
	 * @hooked trx_addons_action_importer_after_import_posts
	 *
	 * @param object $importer  Importer object
	 */
	function trx_addons_woocommerce_importer_after_import_posts( $importer ) {
		if ( trx_addons_exists_woocommerce() && in_array( 'woocommerce', $importer->options['required_plugins'] ) ) {
			$wooc_pages = array(						// Options slugs and pages titles for WooCommerce pages
				'woocommerce_shop_page_id' 				=> 'Shop',
				'woocommerce_cart_page_id' 				=> 'Cart',
				'woocommerce_checkout_page_id' 			=> 'Checkout',
				'woocommerce_pay_page_id' 				=> 'Checkout &#8594; Pay',
				'woocommerce_thanks_page_id' 			=> 'Order Received',
				'woocommerce_myaccount_page_id' 		=> 'My Account',
				'woocommerce_edit_address_page_id'		=> 'Edit My Address',
				'woocommerce_view_order_page_id'		=> 'View Order',
				'woocommerce_change_password_page_id'	=> 'Change Password',
				'woocommerce_logout_page_id'			=> 'Logout',
				'woocommerce_lost_password_page_id'		=> 'Lost Password'
			);
			foreach ( $wooc_pages as $woo_page_name => $woo_page_title ) {
				$woopage = trx_addons_get_page_by_title( $woo_page_title );
				if ( ! empty( $woopage->ID ) ) {
					update_option( $woo_page_name, $woopage->ID );
				}
			}
			// We no longer need to install pages
			delete_option( '_wc_needs_pages' );
			delete_transient( '_wc_activation_redirect' );
		}
	}
}

if ( ! function_exists( 'trx_addons_woocommerce_importer_after_import_end' ) ) {
	add_filter( 'trx_addons_action_importer_import_end', 'trx_addons_woocommerce_importer_after_import_end', 10, 1 );
	/**
	 * Setup WooCommerce options after import data is complete
	 * 
	 * @hooked trx_addons_action_importer_import_end
	 *
	 * @param object $importer  Importer object
	 */
	function trx_addons_woocommerce_importer_after_import_end( $importer ) {
		if ( trx_addons_exists_woocommerce()
				&& in_array( 'woocommerce', $importer->options['required_plugins'] )
				&& get_option( 'woocommerce_queue_flush_rewrite_rules' ) != 'yes'
		) {
			update_option( 'woocommerce_queue_flush_rewrite_rules', 'yes' );
		}
	}
}

if ( ! function_exists( 'trx_addons_woocommerce_importer_show_params' ) ) {
	add_action( 'trx_addons_action_importer_params', 'trx_addons_woocommerce_importer_show_params', 10, 1 );
	/**
	 * Add a checkbox "Import WooCommerce" to the one-click importer
	 * 
	 * @hooked trx_addons_action_importer_params
	 *
	 * @param object $importer  Importer object
	 */
	function trx_addons_woocommerce_importer_show_params( $importer ) {
		if ( trx_addons_exists_woocommerce() && in_array('woocommerce', $importer->options['required_plugins']) ) {
			$importer->show_importer_params( array(
				'slug' => 'woocommerce',
				'title' => esc_html__('Import WooCommerce', 'trx_addons'),
				'part' => 0
			) );
		}
	}
}

if ( ! function_exists( 'trx_addons_woocommerce_importer_import' ) ) {
	add_action( 'trx_addons_action_importer_import', 'trx_addons_woocommerce_importer_import', 10, 2 );
	/**
	 * Import WooCommerce data from the file
	 * 
	 * @hooked trx_addons_action_importer_import
	 *
	 * @param object $importer  Importer object
	 * @param string $action    Current importer action
	 */
	function trx_addons_woocommerce_importer_import( $importer, $action ) {
		if ( trx_addons_exists_woocommerce() && in_array( 'woocommerce', $importer->options['required_plugins'] ) ) {
			if ( $action == 'import_woocommerce' ) {
				$importer->response['start_from_id'] = 0;
				$importer->import_dump('woocommerce', esc_html__('WooCommerce meta', 'trx_addons'));
				delete_transient( 'wc_attribute_taxonomies' );
			}
		}
	}
}

if ( ! function_exists( 'trx_addons_woocommerce_importer_check_row' ) ) {
	add_filter( 'trx_addons_filter_importer_import_row', 'trx_addons_woocommerce_importer_check_row', 9, 4 );
	/**
	 * Check if the row will be imported
	 * 
	 * @hooked trx_addons_filter_importer_import_row
	 *
	 * @param boolean $flag   Allow to import or not
	 * @param string  $table  Table name
	 * @param array   $row    Row data
	 * @param string  $list   List of the required plugins slugs
	 * 
	 * @return boolean  	Allow to import or not
	 */
	function trx_addons_woocommerce_importer_check_row( $flag, $table, $row, $list ) {
		if ( $flag || strpos( $list, 'woocommerce' ) === false ) {
			return $flag;
		}
		if ( trx_addons_exists_woocommerce() ) {
			if ( $table == 'posts' ) {
				$flag = in_array( $row['post_type'], array( 'product', 'product_variation', 'shop_order', 'shop_order_refund',
															'shop_coupon', 'shop_webhook', 'scheduled-action' ) );
			}
		}
		return $flag;
	}
}

if ( ! function_exists( 'trx_addons_woocommerce_importer_import_fields' ) ) {
	add_action( 'trx_addons_action_importer_import_fields',	'trx_addons_woocommerce_importer_import_fields', 10, 1 );
	/**
	 * Display "WooCommerce" in the importer progress area
	 * 
	 * @hooked trx_addons_action_importer_import_fields
	 *
	 * @param object $importer  Importer object
	 */
	function trx_addons_woocommerce_importer_import_fields( $importer ) {
		if ( trx_addons_exists_woocommerce() && in_array( 'woocommerce', $importer->options['required_plugins'] ) ) {
			$importer->show_importer_fields( array(
				'slug'=>'woocommerce', 
				'title' => esc_html__('WooCommerce meta', 'trx_addons')
			) );
		}
	}
}

if ( ! function_exists( 'trx_addons_woocommerce_importer_export' ) ) {
	add_action( 'trx_addons_action_importer_export', 'trx_addons_woocommerce_importer_export', 10, 1 );
	/**
	 * Export WooCommerce data to the file
	 * 
	 * @hooked trx_addons_action_importer_export
	 * 
	 * @trigger trx_addons_filter_importer_export_tables
	 *
	 * @param object $importer  Importer object
	 */
	function trx_addons_woocommerce_importer_export( $importer ) {
		if ( trx_addons_exists_woocommerce() && in_array( 'woocommerce', $importer->options['required_plugins'] ) ) {
			trx_addons_fpc(
				$importer->export_file_dir( 'woocommerce.txt' ),
				serialize( apply_filters( 'trx_addons_filter_importer_export_tables', array(
					"woocommerce_attribute_taxonomies"				=> $importer->export_dump("woocommerce_attribute_taxonomies"),
					"woocommerce_downloadable_product_permissions"	=> $importer->export_dump("woocommerce_downloadable_product_permissions"),
					"woocommerce_order_itemmeta"					=> $importer->export_dump("woocommerce_order_itemmeta"),
					"woocommerce_order_items"						=> $importer->export_dump("woocommerce_order_items"),
					// Product and taxonomy caches to enable search after the demo data is installed
					"wc_category_lookup"							=> $importer->export_dump("wc_category_lookup"),
					"wc_customer_lookup"							=> $importer->export_dump("wc_customer_lookup"),
					"wc_order_coupon_lookup"						=> $importer->export_dump("wc_order_coupon_lookup"),
					"wc_order_product_lookup"						=> $importer->export_dump("wc_order_product_lookup"),
					"wc_order_tax_lookup"							=> $importer->export_dump("wc_order_tax_lookup"),
					"wc_product_attributes_lookup"					=> $importer->export_dump("wc_product_attributes_lookup"),
					"wc_product_meta_lookup"						=> $importer->export_dump("wc_product_meta_lookup"),
				), 'woocommerce' ) )
			);
		}
	}
}

if ( ! function_exists( 'trx_addons_woocommerce_importer_export_fields' ) ) {
	add_action( 'trx_addons_action_importer_export_fields',	'trx_addons_woocommerce_importer_export_fields', 10, 1 );
	/**
	 * Display WooCommerce in the list of export files
	 * 
	 * @hooked trx_addons_action_importer_export_fields
	 *
	 * @param object $importer  Importer object
	 */
	function trx_addons_woocommerce_importer_export_fields( $importer ) {
		if ( trx_addons_exists_woocommerce() && in_array( 'woocommerce', $importer->options['required_plugins'] ) ) {
			$importer->show_exporter_fields( array(
				'slug'	=> 'woocommerce',
				'title' => esc_html__('WooCommerce', 'trx_addons')
			) );
		}
	}
}
