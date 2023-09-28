<?php
/**
 * ThemeREX Addons Custom post type: Properties (Taxonomy 'Type' support)
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
if ( ! defined('TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_TYPE') )
		define('TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_TYPE', trx_addons_cpt_param('properties', 'taxonomy_type'));

// Register post type and taxonomy
if (!function_exists('trx_addons_cpt_properties_taxonomy_type_init')) {
	add_action( 'init', 'trx_addons_cpt_properties_taxonomy_type_init', 9 );
	function trx_addons_cpt_properties_taxonomy_type_init() {

		register_taxonomy(
			TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_TYPE,
			TRX_ADDONS_CPT_PROPERTIES_PT,
			apply_filters('trx_addons_filter_register_taxonomy', 
				array(
					'post_type' 		=> TRX_ADDONS_CPT_PROPERTIES_PT,
					'hierarchical'      => true,
					'labels'            => array(
						'name'              => esc_html__( 'Type', 'trx_addons' ),
						'singular_name'     => esc_html__( 'Type', 'trx_addons' ),
						'search_items'      => esc_html__( 'Search Types', 'trx_addons' ),
						'all_items'         => esc_html__( 'All Types', 'trx_addons' ),
						'parent_item'       => esc_html__( 'Parent Type', 'trx_addons' ),
						'parent_item_colon' => esc_html__( 'Parent Type:', 'trx_addons' ),
						'edit_item'         => esc_html__( 'Edit Type', 'trx_addons' ),
						'update_item'       => esc_html__( 'Update Type', 'trx_addons' ),
						'add_new_item'      => esc_html__( 'Add New Type', 'trx_addons' ),
						'new_item_name'     => esc_html__( 'New Type Name', 'trx_addons' ),
						'menu_name'         => esc_html__( 'Types', 'trx_addons' ),
					),
					'show_ui'           => true,
					'show_admin_column' => true,
					'query_var'         => true,
					'capabilities'      => array(
						'manage_terms' => 'manage_property_terms',
						'edit_terms'   => 'edit_property_terms',
						'delete_terms' => 'delete_property_terms',
						'assign_terms' => 'assign_property_terms',
					),
					'rewrite'           => array(
												'slug'         => trx_addons_cpt_param('properties', 'taxonomy_type_slug'),
												'with_front'   => false,
												'hierarchical' => true
											)
				),
				TRX_ADDONS_CPT_PROPERTIES_PT,
				TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_TYPE
			)
		);
	}
}



// Replace standard theme templates
//-------------------------------------------------------------

// Change standard category template for properties categories (groups)
if ( !function_exists( 'trx_addons_cpt_properties_taxonomy_type_taxonomy_template' ) ) {
	add_filter('taxonomy_template',	'trx_addons_cpt_properties_taxonomy_type_taxonomy_template');
	function trx_addons_cpt_properties_taxonomy_type_taxonomy_template( $template ) {
		if ( is_tax(TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_TYPE) ) {
			if (($template = trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_CPT . 'properties/tpl.properties.archive_type.php')) == '') 
				$template = trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_CPT . 'properties/tpl.properties.archive.php');
		}
		return $template;
	}	
}



// Admin utils
// -----------------------------------------------------------------

// Show <select> with properties categories in the admin filters area
if (!function_exists('trx_addons_cpt_properties_taxonomy_type_admin_filters')) {
	add_action( 'restrict_manage_posts', 'trx_addons_cpt_properties_taxonomy_type_admin_filters' );
	function trx_addons_cpt_properties_taxonomy_type_admin_filters() {
		trx_addons_admin_filters(TRX_ADDONS_CPT_PROPERTIES_PT, TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_TYPE);
	}
}
  
// Clear terms cache on the taxonomy 'type' save
if (!function_exists('trx_addons_cpt_properties_taxonomy_type_admin_clear_cache_type')) {
	add_action( 'edited_'.TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_TYPE, 'trx_addons_cpt_properties_taxonomy_type_admin_clear_cache_type', 10, 1 );
	add_action( 'delete_'.TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_TYPE, 'trx_addons_cpt_properties_taxonomy_type_admin_clear_cache_type', 10, 1 );
	add_action( 'created_'.TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_TYPE, 'trx_addons_cpt_properties_taxonomy_type_admin_clear_cache_type', 10, 1 );
	function trx_addons_cpt_properties_taxonomy_type_admin_clear_cache_type( $term_id=0 ) {  
		trx_addons_admin_clear_cache_terms(TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_TYPE);
	}
}


// Additional parameters to the taxonomy
//--------------------------------------------------------------------------

// Save additional parameters
if (!function_exists('trx_addons_cpt_properties_taxonomy_type_save_custom_fields')) {
	add_action('edited_'.TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_TYPE,	'trx_addons_cpt_properties_taxonomy_type_save_custom_fields', 10, 1 );
	add_action('created_'.TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_TYPE,	'trx_addons_cpt_properties_taxonomy_type_save_custom_fields', 10, 1 );
	function trx_addons_cpt_properties_taxonomy_type_save_custom_fields($term_id) {
		if (isset($_POST['trx_addons_image'])) {
			trx_addons_set_term_meta(array(
											'term_id' => $term_id,
											'key' => 'image'
											),
										$_POST['trx_addons_image']
										);
		}
	}
}

// Display additional fields
if (!function_exists('trx_addons_cpt_properties_taxonomy_type_show_custom_fields')) {
	add_action(TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_TYPE.'_edit_form_fields', 'trx_addons_cpt_properties_taxonomy_type_show_custom_fields', 10, 1 );
	add_action(TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_TYPE.'_add_form_fields', 'trx_addons_cpt_properties_taxonomy_type_show_custom_fields', 10, 1 );
	function trx_addons_cpt_properties_taxonomy_type_show_custom_fields($term) {
		$term_id = !empty($term->term_id) ? $term->term_id : 0;
		// Image
		echo ((int) $term_id > 0 ? '<tr' : '<div') . ' class="form-field">'
			. ((int) $term_id > 0 ? '<th valign="top" scope="row">' : '<div>');
		?><label id="trx_addons_image_label" for="trx_addons_image"><?php esc_html_e('Map marker:', 'trx_addons'); ?></label><?php
		echo ((int) $term_id > 0 ? '</th>' : '</div>')
			. ((int) $term_id > 0 ? '<td valign="top">' : '<div>');
		$img = $term_id > 0 ? trx_addons_get_term_image( $term_id, TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_TYPE, TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_IMAGE_KEY ) : '';
		?><input type="hidden" id="trx_addons_image" class="trx_addons_image_selector_field" name="trx_addons_image" value="<?php echo esc_url($img); ?>"><?php
		if (empty($img)) $img = trx_addons_get_no_image();
		trx_addons_show_layout(trx_addons_options_show_custom_field('trx_addons_image_button', array(
								'type' => 'mediamanager',
								'linked_field_id' => 'trx_addons_image'
								), $img));
		echo (int) $term_id > 0 ? '</td></tr>' : '</div></div>';
	}
}

// Create additional column in the terms list
if (!function_exists('trx_addons_cpt_properties_taxonomy_type_add_custom_column')) {
	add_filter('manage_edit-'.TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_TYPE.'_columns',	'trx_addons_cpt_properties_taxonomy_type_add_custom_column', 9);
	function trx_addons_cpt_properties_taxonomy_type_add_custom_column( $columns ){
		$columns['marker'] = esc_html__('Map marker', 'trx_addons');
		return $columns;
	}
}

// Fill additional column in the terms list
if (!function_exists('trx_addons_cpt_properties_taxonomy_type_fill_custom_column')) {
	add_action('manage_'.TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_TYPE.'_custom_column',	'trx_addons_cpt_properties_taxonomy_type_fill_custom_column', 9, 3);
	function trx_addons_cpt_properties_taxonomy_type_fill_custom_column($output='', $column_name='', $term_id=0) {
		if ($column_name == 'marker') {
			$img = $term_id > 0 ? trx_addons_get_term_image( $term_id, TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_TYPE, TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_IMAGE_KEY ) : '';
			if (!empty($img)) {
				?><img class="trx_addons_image_selector_preview trx_addons_image_preview" src="<?php
							echo esc_url(trx_addons_add_thumb_size($img, trx_addons_get_thumb_size('tiny')));
						?>" alt="<?php esc_attr_e('Type preview', 'trx_addons'); ?>"><?php
			}
		}
	}
}
