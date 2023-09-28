<?php
/**
 * ThemeREX Addons Custom post type: Certificates
 *
 * @package ThemeREX Addons
 * @since v1.5
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


// -----------------------------------------------------------------
// -- Custom post type registration
// -----------------------------------------------------------------

// Define Custom post type and taxonomy constants
if ( ! defined('TRX_ADDONS_CPT_CERTIFICATES_PT') ) define('TRX_ADDONS_CPT_CERTIFICATES_PT', trx_addons_cpt_param('certificates', 'post_type'));

// Register post type and taxonomy
if (!function_exists('trx_addons_cpt_certificates_init')) {
	add_action( 'init', 'trx_addons_cpt_certificates_init' );
	function trx_addons_cpt_certificates_init() {
		
		// Add Services parameters to the Meta Box support
		trx_addons_meta_box_register(TRX_ADDONS_CPT_CERTIFICATES_PT, array(
			"subtitle" => array(
				"title" => esc_html__("Item's subtitle",  'trx_addons'),
				"desc" => wp_kses_data( __("Certificate description or any other text", 'trx_addons') ),
				"std" => "",
				"type" => "text"
			)
		));
		
		// Register post type
		register_post_type(
			TRX_ADDONS_CPT_CERTIFICATES_PT,
			apply_filters('trx_addons_filter_register_post_type',
				array(
					'label'               => esc_html__( 'Certificate', 'trx_addons' ),
					'description'         => esc_html__( 'Certificate Description', 'trx_addons' ),
					'labels'              => array(
						'name'                => esc_html__( 'Certificates', 'trx_addons' ),
						'singular_name'       => esc_html__( 'Certificate', 'trx_addons' ),
						'menu_name'           => esc_html__( 'Certificates', 'trx_addons' ),
						'parent_item_colon'   => esc_html__( 'Parent Item:', 'trx_addons' ),
						'all_items'           => esc_html__( 'All Certificates', 'trx_addons' ),
						'view_item'           => esc_html__( 'View Certificate', 'trx_addons' ),
						'add_new_item'        => esc_html__( 'Add New Certificate', 'trx_addons' ),
						'add_new'             => esc_html__( 'Add New', 'trx_addons' ),
						'edit_item'           => esc_html__( 'Edit Certificate', 'trx_addons' ),
						'update_item'         => esc_html__( 'Update Certificate', 'trx_addons' ),
						'search_items'        => esc_html__( 'Search Certificate', 'trx_addons' ),
						'not_found'           => esc_html__( 'Not found', 'trx_addons' ),
						'not_found_in_trash'  => esc_html__( 'Not found in Trash', 'trx_addons' ),
					),
					'supports'            => trx_addons_cpt_param('certificates', 'supports'),
					'public'              => true,
					'publicly_queryable'  => false,
					'hierarchical'        => false,
					'has_archive'         => false,
					'can_export'          => true,
					'show_in_admin_bar'   => true,
					'show_ui'             => true,
					'show_in_menu'        => true,
					'exclude_from_search' => true,
					// From WordPress 5.3 'menu_position' must be only integer or null (default)!
					// 'menu_position'       => '52.2',
					'menu_icon'			  => 'dashicons-awards',
					'capability_type'     => 'post',
					'rewrite'             => array(
												'slug'         => trx_addons_cpt_param('certificates', 'post_type_slug'),
												'with_front'   => false,
												'hierarchical' => false
												)
				),
				TRX_ADDONS_CPT_CERTIFICATES_PT
			)
		);
	}
}


// Allow Gutenberg as main editor for this post type
if ( ! function_exists( 'trx_addons_cpt_certificates_add_pt_to_gutenberg' ) ) {
	add_filter( 'trx_addons_filter_add_pt_to_gutenberg', 'trx_addons_cpt_certificates_add_pt_to_gutenberg', 10, 2 );
	function trx_addons_cpt_certificates_add_pt_to_gutenberg( $allow, $post_type ) {
		return $allow || $post_type == TRX_ADDONS_CPT_CERTIFICATES_PT;
	}
}
