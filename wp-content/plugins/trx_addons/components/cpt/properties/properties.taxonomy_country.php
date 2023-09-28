<?php
/**
 * ThemeREX Addons Custom post type: Properties (Taxonomy 'Country' support)
 *
 * @package ThemeREX Addons
 * @since v1.6.22
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


// -----------------------------------------------------------------
// -- Custom post type registration
// -----------------------------------------------------------------

// Define Custom post type and taxonomy constants for 'Properties'
if ( ! defined('TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_COUNTRY') )
		define('TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_COUNTRY', trx_addons_cpt_param('properties', 'taxonomy_country'));


// Register post type and taxonomy
if (!function_exists('trx_addons_cpt_properties_taxonomy_country_init')) {
	add_action( 'init', 'trx_addons_cpt_properties_taxonomy_country_init', 9 );
	function trx_addons_cpt_properties_taxonomy_country_init() {
		
		register_taxonomy(
			TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_COUNTRY,
			TRX_ADDONS_CPT_PROPERTIES_PT,
			apply_filters('trx_addons_filter_register_taxonomy', 
				array(
					'post_type' 		=> TRX_ADDONS_CPT_PROPERTIES_PT,
					'hierarchical'      => true,
					'labels'            => array(
						'name'              => esc_html__( 'Country', 'trx_addons' ),
						'singular_name'     => esc_html__( 'Country', 'trx_addons' ),
						'search_items'      => esc_html__( 'Search Countries', 'trx_addons' ),
						'all_items'         => esc_html__( 'All Countries', 'trx_addons' ),
						'parent_item'       => esc_html__( 'Parent Country', 'trx_addons' ),
						'parent_item_colon' => esc_html__( 'Parent Country:', 'trx_addons' ),
						'edit_item'         => esc_html__( 'Edit Country', 'trx_addons' ),
						'update_item'       => esc_html__( 'Update Country', 'trx_addons' ),
						'add_new_item'      => esc_html__( 'Add New Country', 'trx_addons' ),
						'new_item_name'     => esc_html__( 'New Country Name', 'trx_addons' ),
						'menu_name'         => esc_html__( 'Countries', 'trx_addons' ),
					),
					'show_ui'           => true,
					'show_admin_column' => false,
					'meta_box_cb'		=> false,
					'query_var'         => true,
					'capabilities'      => array(
						'manage_terms' => 'manage_property_terms',
						'edit_terms'   => 'edit_property_terms',
						'delete_terms' => 'delete_property_terms',
						'assign_terms' => 'assign_property_terms',
					),
					'rewrite'           => array(
												'slug'         => trx_addons_cpt_param('properties', 'taxonomy_country_slug'),
												'with_front'   => false,
												'hierarchical' => true
											)
				),
				TRX_ADDONS_CPT_PROPERTIES_PT,
				TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_COUNTRY
			)
		);
	}
}



// Replace standard theme templates
//-------------------------------------------------------------

// Change standard category template for properties categories (groups)
if ( !function_exists( 'trx_addons_cpt_properties_taxonomy_country_taxonomy_template' ) ) {
	add_filter('taxonomy_template',	'trx_addons_cpt_properties_taxonomy_country_taxonomy_template');
	function trx_addons_cpt_properties_taxonomy_country_taxonomy_template( $template ) {
		if ( is_tax(TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_COUNTRY) ) {
			if (($template = trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_CPT . 'properties/tpl.properties.archive_country.php')) == '') 
				$template = trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_CPT . 'properties/tpl.properties.archive.php');
		}
		return $template;
	}	
}



// Admin utils
// -----------------------------------------------------------------

// Show <select> with properties categories in the admin filters area
if (!function_exists('trx_addons_cpt_properties_taxonomy_country_admin_filters')) {
	add_action( 'restrict_manage_posts', 'trx_addons_cpt_properties_taxonomy_country_admin_filters' );
	function trx_addons_cpt_properties_taxonomy_country_admin_filters() {
		trx_addons_admin_filters(TRX_ADDONS_CPT_PROPERTIES_PT, TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_COUNTRY);
	}
}
  
// Clear terms cache on the taxonomy 'country' save
if (!function_exists('trx_addons_cpt_properties_taxonomy_country_admin_clear_cache_country')) {
	add_action( 'edited_'.TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_COUNTRY, 'trx_addons_cpt_properties_taxonomy_country_admin_clear_cache_country', 10, 1 );
	add_action( 'delete_'.TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_COUNTRY, 'trx_addons_cpt_properties_taxonomy_country_admin_clear_cache_country', 10, 1 );
	add_action( 'created_'.TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_COUNTRY, 'trx_addons_cpt_properties_taxonomy_country_admin_clear_cache_country', 10, 1 );
	function trx_addons_cpt_properties_taxonomy_country_admin_clear_cache_country( $term_id=0 ) {  
		trx_addons_admin_clear_cache_terms(TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_COUNTRY);
	}
}
