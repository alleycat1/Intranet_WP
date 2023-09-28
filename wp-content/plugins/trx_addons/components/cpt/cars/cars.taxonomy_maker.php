<?php
/**
 * ThemeREX Addons Custom post type: Cars (Taxonomy 'Manufacturer' support)
 *
 * @package ThemeREX Addons
 * @since v1.6.25
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


// -----------------------------------------------------------------
// -- Custom post type registration
// -----------------------------------------------------------------

// Define Custom post type and taxonomy constants for 'Cars'
if ( ! defined('TRX_ADDONS_CPT_CARS_TAXONOMY_MAKER') )
		define('TRX_ADDONS_CPT_CARS_TAXONOMY_MAKER', trx_addons_cpt_param('cars', 'taxonomy_maker'));

// Register post type and taxonomy
if (!function_exists('trx_addons_cpt_cars_taxonomy_maker_init')) {
	add_action( 'init', 'trx_addons_cpt_cars_taxonomy_maker_init', 9 );
	function trx_addons_cpt_cars_taxonomy_maker_init() {
		
		register_taxonomy(
			TRX_ADDONS_CPT_CARS_TAXONOMY_MAKER,
			TRX_ADDONS_CPT_CARS_PT,
			apply_filters('trx_addons_filter_register_taxonomy', 
				array(
					'post_type' 		=> TRX_ADDONS_CPT_CARS_PT,
					'hierarchical'      => true,
					'labels'            => array(
						'name'              => esc_html__( 'Manufacturer', 'trx_addons' ),
						'singular_name'     => esc_html__( 'Manufacturer', 'trx_addons' ),
						'search_items'      => esc_html__( 'Search Manufacturer', 'trx_addons' ),
						'all_items'         => esc_html__( 'All Manufacturers', 'trx_addons' ),
						'parent_item'       => esc_html__( 'Parent Manufacturer', 'trx_addons' ),
						'parent_item_colon' => esc_html__( 'Parent Manufacturer:', 'trx_addons' ),
						'edit_item'         => esc_html__( 'Edit Manufacturer', 'trx_addons' ),
						'update_item'       => esc_html__( 'Update Manufacturer', 'trx_addons' ),
						'add_new_item'      => esc_html__( 'Add New Manufacturer', 'trx_addons' ),
						'new_item_name'     => esc_html__( 'New Manufacturer Name', 'trx_addons' ),
						'menu_name'         => esc_html__( 'Manufacturers', 'trx_addons' ),
					),
					'show_ui'           => true,
					'show_admin_column' => true,
					'meta_box_cb'		=> false,
					'query_var'         => true,
					'capabilities'      => array(
						'manage_terms' => 'manage_car_terms',
						'edit_terms'   => 'edit_car_terms',
						'delete_terms' => 'delete_car_terms',
						'assign_terms' => 'assign_car_terms',
					),
					'rewrite'           => array(
												'slug'         => trx_addons_cpt_param('cars', 'taxonomy_maker_slug'),
												'with_front'   => false,
												'hierarchical' => true
											)
				),
				TRX_ADDONS_CPT_CARS_PT,
				TRX_ADDONS_CPT_CARS_TAXONOMY_MAKER
			)
		);
	}
}


// Replace standard theme templates
//-------------------------------------------------------------

// Change standard category template for cars categories (groups)
if ( !function_exists( 'trx_addons_cpt_cars_taxonomy_template' ) ) {
	add_filter('taxonomy_template',	'trx_addons_cpt_cars_taxonomy_template');
	function trx_addons_cpt_cars_taxonomy_template( $template ) {
		if ( is_tax(TRX_ADDONS_CPT_CARS_TAXONOMY_MAKER) ) {
			if (($template = trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_CPT . 'cars/tpl.cars.archive_maker.php')) == '') 
				$template = trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_CPT . 'cars/tpl.cars.archive.php');
		}
		return $template;
	}	
}



// Admin utils
// -----------------------------------------------------------------

// Show <select> with cars categories in the admin filters area
if (!function_exists('trx_addons_cpt_cars_taxonomy_maker_admin_filters')) {
	add_action( 'restrict_manage_posts', 'trx_addons_cpt_cars_taxonomy_maker_admin_filters' );
	function trx_addons_cpt_cars_taxonomy_maker_admin_filters() {
		trx_addons_admin_filters(TRX_ADDONS_CPT_CARS_PT, TRX_ADDONS_CPT_CARS_TAXONOMY_MAKER);
	}
}
  
// Clear terms cache on the taxonomy 'maker' save
if (!function_exists('trx_addons_cpt_cars_taxonomy_maker_admin_clear_cache_maker')) {
	add_action( 'edited_'.TRX_ADDONS_CPT_CARS_TAXONOMY_MAKER, 'trx_addons_cpt_cars_taxonomy_maker_admin_clear_cache_maker', 10, 1 );
	add_action( 'delete_'.TRX_ADDONS_CPT_CARS_TAXONOMY_MAKER, 'trx_addons_cpt_cars_taxonomy_maker_admin_clear_cache_maker', 10, 1 );
	add_action( 'created_'.TRX_ADDONS_CPT_CARS_TAXONOMY_MAKER, 'trx_addons_cpt_cars_taxonomy_maker_admin_clear_cache_maker', 10, 1 );
	function trx_addons_cpt_cars_taxonomy_maker_admin_clear_cache_maker( $term_id=0 ) {  
		trx_addons_admin_clear_cache_terms(TRX_ADDONS_CPT_CARS_TAXONOMY_MAKER);
	}
}


// Additional parameters to the taxonomy
//--------------------------------------------------------------------------

// Save additional parameters
if (!function_exists('trx_addons_cpt_cars_taxonomy_maker_save_custom_fields')) {
	add_action('edited_'.TRX_ADDONS_CPT_CARS_TAXONOMY_MAKER,	'trx_addons_cpt_cars_taxonomy_maker_save_custom_fields', 10, 1 );
	add_action('created_'.TRX_ADDONS_CPT_CARS_TAXONOMY_MAKER,	'trx_addons_cpt_cars_taxonomy_maker_save_custom_fields', 10, 1 );
	function trx_addons_cpt_cars_taxonomy_maker_save_custom_fields($term_id) {
		if (isset($_POST['trx_addons_image'])) {
			trx_addons_set_term_meta(array(
											'term_id' => $term_id,
											'key' => TRX_ADDONS_CPT_CARS_TAXONOMY_IMAGE_KEY
											),
										$_POST['trx_addons_image']
										);
		}
	}
}

// Display additional fields
if (!function_exists('trx_addons_cpt_cars_taxonomy_maker_show_custom_fields')) {
	add_action(TRX_ADDONS_CPT_CARS_TAXONOMY_MAKER.'_edit_form_fields', 'trx_addons_cpt_cars_taxonomy_maker_show_custom_fields', 10, 1 );
	add_action(TRX_ADDONS_CPT_CARS_TAXONOMY_MAKER.'_add_form_fields', 'trx_addons_cpt_cars_taxonomy_maker_show_custom_fields', 10, 1 );
	function trx_addons_cpt_cars_taxonomy_maker_show_custom_fields($term) {
		$term_id = !empty($term->term_id) ? $term->term_id : 0;

		// Image
		echo ((int) $term_id > 0 ? '<tr' : '<div') . ' class="form-field">'
			. ((int) $term_id > 0 ? '<th valign="top" scope="row">' : '<div>');
		?><label id="trx_addons_image_label" for="trx_addons_image"><?php esc_html_e('Logo:', 'trx_addons'); ?></label><?php
		echo ((int) $term_id > 0 ? '</th>' : '</div>')
			. ((int) $term_id > 0 ? '<td valign="top">' : '<div>');
		$img = $term_id > 0 ? trx_addons_get_term_image( $term_id, TRX_ADDONS_CPT_CARS_TAXONOMY_MAKER, TRX_ADDONS_CPT_CARS_TAXONOMY_IMAGE_KEY ) : '';
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
if (!function_exists('trx_addons_cpt_cars_taxonomy_maker_add_custom_column')) {
	add_filter('manage_edit-'.TRX_ADDONS_CPT_CARS_TAXONOMY_MAKER.'_columns',	'trx_addons_cpt_cars_taxonomy_maker_add_custom_column', 9);
	function trx_addons_cpt_cars_taxonomy_maker_add_custom_column( $columns ){
		$columns['image'] = esc_html__('Logo', 'trx_addons');
		return $columns;
	}
}

// Fill additional column in the terms list
if (!function_exists('trx_addons_cpt_cars_taxonomy_maker_fill_custom_column')) {
	add_action('manage_'.TRX_ADDONS_CPT_CARS_TAXONOMY_MAKER.'_custom_column',	'trx_addons_cpt_cars_taxonomy_maker_fill_custom_column', 9, 3);
	function trx_addons_cpt_cars_taxonomy_maker_fill_custom_column($output='', $column_name='', $term_id=0) {
		if ($column_name == 'image') {
			$img = $term_id > 0 ? trx_addons_get_term_image( $term_id, TRX_ADDONS_CPT_CARS_TAXONOMY_MAKER, TRX_ADDONS_CPT_CARS_TAXONOMY_IMAGE_KEY ) : '';
			if (!empty($img)) {
				?><img class="trx_addons_image_selector_preview trx_addons_image_preview" src="<?php
							echo esc_url(trx_addons_add_thumb_size($img, trx_addons_get_thumb_size('tiny')));
						?>" alt="<?php esc_attr_e('Maker logo', 'trx_addons'); ?>"><?php
			}
		}
	}
}
