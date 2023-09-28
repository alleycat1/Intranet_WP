<?php
/**
 * ThemeREX Addons Custom post type: Cars (Taxonomy 'Model' support)
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
if ( ! defined('TRX_ADDONS_CPT_CARS_TAXONOMY_MODEL') )
		define('TRX_ADDONS_CPT_CARS_TAXONOMY_MODEL', trx_addons_cpt_param('cars', 'taxonomy_model'));

// Register post type and taxonomy
if (!function_exists('trx_addons_cpt_cars_taxonomy_model_init')) {
	add_action( 'init', 'trx_addons_cpt_cars_taxonomy_model_init', 9 );
	function trx_addons_cpt_cars_taxonomy_model_init() {
		
		register_taxonomy(
			TRX_ADDONS_CPT_CARS_TAXONOMY_MODEL,
			TRX_ADDONS_CPT_CARS_PT,
			apply_filters('trx_addons_filter_register_taxonomy', 
				array(
					'post_type' 		=> TRX_ADDONS_CPT_CARS_PT,
					'hierarchical'      => true,
					'labels'            => array(
						'name'              => esc_html__( 'Models', 'trx_addons' ),
						'singular_name'     => esc_html__( 'Model', 'trx_addons' ),
						'search_items'      => esc_html__( 'Search Models', 'trx_addons' ),
						'all_items'         => esc_html__( 'All Models', 'trx_addons' ),
						'parent_item'       => esc_html__( 'Parent Model', 'trx_addons' ),
						'parent_item_colon' => esc_html__( 'Parent Model:', 'trx_addons' ),
						'edit_item'         => esc_html__( 'Edit Model', 'trx_addons' ),
						'update_item'       => esc_html__( 'Update Model', 'trx_addons' ),
						'add_new_item'      => esc_html__( 'Add New Model', 'trx_addons' ),
						'new_item_name'     => esc_html__( 'New Model Name', 'trx_addons' ),
						'menu_name'         => esc_html__( 'Models', 'trx_addons' ),
					),
					'show_ui'           => true,
					'show_admin_column' => false,
					'meta_box_cb'		=> false,
					'query_var'         => true,
					'capabilities'      => array(
						'manage_terms' => 'manage_car_terms',
						'edit_terms'   => 'edit_car_terms',
						'delete_terms' => 'delete_car_terms',
						'assign_terms' => 'assign_car_terms',
					),
					'rewrite'           => array(
												'slug'         => trx_addons_cpt_param('cars', 'taxonomy_model_slug'),
												'with_front'   => false,
												'hierarchical' => true
											)
				),
				TRX_ADDONS_CPT_CARS_PT,
				TRX_ADDONS_CPT_CARS_TAXONOMY_MODEL
			)
		);
	}
}



// Replace standard theme templates
//-------------------------------------------------------------

// Change standard category template for cars categories (groups)
if ( !function_exists( 'trx_addons_cpt_cars_taxonomy_model_taxonomy_template' ) ) {
	add_filter('taxonomy_template',	'trx_addons_cpt_cars_taxonomy_model_taxonomy_template');
	function trx_addons_cpt_cars_taxonomy_model_taxonomy_template( $template ) {
		if ( is_tax(TRX_ADDONS_CPT_CARS_TAXONOMY_MODEL) ) {
			if (($template = trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_CPT . 'cars/tpl.cars.archive_model.php')) == '') 
				$template = trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_CPT . 'cars/tpl.cars.archive.php');
		}
		return $template;
	}	
}



// Admin utils
// -----------------------------------------------------------------

// Show <select> with cars categories in the admin filters area
if (!function_exists('trx_addons_cpt_cars_taxonomy_model_admin_filters')) {
	add_action( 'restrict_manage_posts', 'trx_addons_cpt_cars_taxonomy_model_admin_filters' );
	function trx_addons_cpt_cars_taxonomy_model_admin_filters() {
		trx_addons_admin_filters(TRX_ADDONS_CPT_CARS_PT, TRX_ADDONS_CPT_CARS_TAXONOMY_MODEL);
	}
}
  
// Clear terms cache on the taxonomy 'model' save
if (!function_exists('trx_addons_cpt_cars_taxonomy_model_admin_clear_cache_model')) {
	add_action( 'edited_'.TRX_ADDONS_CPT_CARS_TAXONOMY_MODEL, 'trx_addons_cpt_cars_taxonomy_model_admin_clear_cache_model', 10, 1 );
	add_action( 'delete_'.TRX_ADDONS_CPT_CARS_TAXONOMY_MODEL, 'trx_addons_cpt_cars_taxonomy_model_admin_clear_cache_model', 10, 1 );
	add_action( 'created_'.TRX_ADDONS_CPT_CARS_TAXONOMY_MODEL, 'trx_addons_cpt_cars_taxonomy_model_admin_clear_cache_model', 10, 1 );
	function trx_addons_cpt_cars_taxonomy_model_admin_clear_cache_model( $term_id=0 ) {  
		trx_addons_admin_clear_cache_terms(TRX_ADDONS_CPT_CARS_TAXONOMY_MODEL);
	}
}

// Get list model by specified maker
if ( !function_exists( 'trx_addons_cpt_cars_taxonomy_model_refresh_list_models' ) ) {
	add_filter('trx_addons_filter_refresh_list_models', 'trx_addons_cpt_cars_taxonomy_model_refresh_list_models', 10, 3);
	function trx_addons_cpt_cars_taxonomy_model_refresh_list_models($list, $maker, $not_selected=false) {
		$rez = array();
		if ($not_selected) {
			$tax_obj = get_taxonomy(TRX_ADDONS_CPT_CARS_TAXONOMY_MODEL);
//			$rez[0]  = trx_addons_get_not_selected_text( ! empty( $tax_obj->label ) ? $tax_obj->label : __( '- Not Selected -', 'trx_addons' ) );
			$rez[''] = trx_addons_get_not_selected_text( ! empty( $tax_obj->label ) ? $tax_obj->label : __( '- Not Selected -', 'trx_addons' ) );
		}
		return trx_addons_array_merge(
					$rez,
					trx_addons_get_list_terms(false, TRX_ADDONS_CPT_CARS_TAXONOMY_MODEL, array(
											'meta_key' => 'maker',
											'meta_value' => $maker
											))
					);
	}
}


// Additional parameters to the taxonomy
//--------------------------------------------------------------------------

// Save additional parameters
if (!function_exists('trx_addons_cpt_cars_taxonomy_model_save_custom_fields')) {
	add_action('edited_'.TRX_ADDONS_CPT_CARS_TAXONOMY_MODEL,	'trx_addons_cpt_cars_taxonomy_model_save_custom_fields', 10, 1 );
	add_action('created_'.TRX_ADDONS_CPT_CARS_TAXONOMY_MODEL,	'trx_addons_cpt_cars_taxonomy_model_save_custom_fields', 10, 1 );
	function trx_addons_cpt_cars_taxonomy_model_save_custom_fields($term_id) {
		if (isset($_POST['trx_addons_maker'])) {
			trx_addons_set_term_meta(array(
											'term_id' => $term_id,
											'key' => 'maker'
											),
										$_POST['trx_addons_maker']
										);
		}
	}
}

// Display additional fields
if (!function_exists('trx_addons_cpt_cars_taxonomy_model_show_custom_fields')) {
	add_action(TRX_ADDONS_CPT_CARS_TAXONOMY_MODEL.'_edit_form_fields', 'trx_addons_cpt_cars_taxonomy_model_show_custom_fields', 10, 1 );
	add_action(TRX_ADDONS_CPT_CARS_TAXONOMY_MODEL.'_add_form_fields', 'trx_addons_cpt_cars_taxonomy_model_show_custom_fields', 10, 1 );
	function trx_addons_cpt_cars_taxonomy_model_show_custom_fields($term) {
		$term_id = !empty($term->term_id) ? $term->term_id : 0;
		// Maker selector
		echo ((int) $term_id > 0 ? '<tr' : '<div') . ' class="form-field term-maker-wrap">'
			. ((int) $term_id > 0 ? '<th valign="top" scope="row">' : '<div>');
		?><label id="trx_addons_maker_label" for="trx_addons_maker"><?php esc_html_e('Manufacturer:', 'trx_addons'); ?></label><?php
		echo ((int) $term_id > 0 ? '</th>' : '</div>')
			. ((int) $term_id > 0 ? '<td valign="top">' : '<div>');
		$maker_id = $term_id > 0 
						? trx_addons_get_term_meta(array(
													'taxonomy'	=> TRX_ADDONS_CPT_CARS_TAXONOMY_MODEL,
													'term_id'	=> $term_id,
													'key'		=> 'maker'
													)) 
						: '';
		$list = trx_addons_get_list_terms(false, TRX_ADDONS_CPT_CARS_TAXONOMY_MAKER);
		if (count($list) > 0) {
			?><select id="trx_addons_maker" class="trx_addons_maker_selector_field" name="trx_addons_maker"><?php
			foreach ($list as $k=>$v) {
				?><option value="<?php echo esc_attr($k); ?>"<?php if ($k == $maker_id) echo ' selected="selected"'; ?>><?php
					echo esc_html($v);
				?></option><?php
			}
			?></select><?php
		}
		echo (int) $term_id > 0 ? '</td></tr>' : '</div></div>';
	}
}

// Create additional column in the terms list
if (!function_exists('trx_addons_cpt_cars_taxonomy_model_add_custom_column')) {
	add_filter('manage_edit-'.TRX_ADDONS_CPT_CARS_TAXONOMY_MODEL.'_columns',	'trx_addons_cpt_cars_taxonomy_model_add_custom_column', 9);
	function trx_addons_cpt_cars_taxonomy_model_add_custom_column( $columns ){
		$columns['maker'] = esc_html__('Manufacturer', 'trx_addons');
		return $columns;
	}
}

// Fill additional column in the terms list
if (!function_exists('trx_addons_cpt_cars_taxonomy_model_fill_custom_column')) {
	add_action('manage_'.TRX_ADDONS_CPT_CARS_TAXONOMY_MODEL.'_custom_column',	'trx_addons_cpt_cars_taxonomy_model_fill_custom_column', 9, 3);
	function trx_addons_cpt_cars_taxonomy_model_fill_custom_column($output='', $column_name='', $term_id=0) {
		if ($column_name == 'maker') {
			$maker_id = $term_id > 0 
						? trx_addons_get_term_meta(array(
													'taxonomy'	=> TRX_ADDONS_CPT_CARS_TAXONOMY_MODEL,
													'term_id'	=> $term_id,
													'key'		=> 'maker'
													)) 
						: '';
			if ($maker_id > 0) {
				$maker = get_term_by('id', $maker_id, TRX_ADDONS_CPT_CARS_TAXONOMY_MAKER, OBJECT);
				if (!empty($maker->name)) {
					?><div class="trx_addons_maker trx_addons_meta_row">
						<span class="trx_addons_maker_name trx_addons_meta_item"><?php echo esc_html($maker->name); ?></span>
					</div><?php
				}
			}
		}
	}
}
