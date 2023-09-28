<?php
/**
 * ThemeREX Addons Custom post type: Properties (Taxonomy 'State' support)
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
if ( ! defined('TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_STATE') )
		define('TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_STATE', trx_addons_cpt_param('properties', 'taxonomy_state'));

// Register post type and taxonomy
if (!function_exists('trx_addons_cpt_properties_taxonomy_state_init')) {
	add_action( 'init', 'trx_addons_cpt_properties_taxonomy_state_init', 9 );
	function trx_addons_cpt_properties_taxonomy_state_init() {
		
		register_taxonomy(
			TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_STATE,
			TRX_ADDONS_CPT_PROPERTIES_PT,
			apply_filters('trx_addons_filter_register_taxonomy', 
				array(
					'post_type' 		=> TRX_ADDONS_CPT_PROPERTIES_PT,
					'hierarchical'      => true,
					'labels'            => array(
						'name'              => esc_html__( 'County/State', 'trx_addons' ),
						'singular_name'     => esc_html__( 'County/State', 'trx_addons' ),
						'search_items'      => esc_html__( 'Search States', 'trx_addons' ),
						'all_items'         => esc_html__( 'All States', 'trx_addons' ),
						'parent_item'       => esc_html__( 'Parent State', 'trx_addons' ),
						'parent_item_colon' => esc_html__( 'Parent State:', 'trx_addons' ),
						'edit_item'         => esc_html__( 'Edit State', 'trx_addons' ),
						'update_item'       => esc_html__( 'Update State', 'trx_addons' ),
						'add_new_item'      => esc_html__( 'Add New State', 'trx_addons' ),
						'new_item_name'     => esc_html__( 'New State Name', 'trx_addons' ),
						'menu_name'         => esc_html__( 'States', 'trx_addons' ),
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
												'slug'         => trx_addons_cpt_param('properties', 'taxonomy_state_slug'),
												'with_front'   => false,
												'hierarchical' => true
											)
				),
				TRX_ADDONS_CPT_PROPERTIES_PT,
				TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_STATE
			)
		);
	}
}



// Replace standard theme templates
//-------------------------------------------------------------

// Change standard category template for properties categories (groups)
if ( !function_exists( 'trx_addons_cpt_properties_taxonomy_state_taxonomy_template' ) ) {
	add_filter('taxonomy_template',	'trx_addons_cpt_properties_taxonomy_state_taxonomy_template');
	function trx_addons_cpt_properties_taxonomy_state_taxonomy_template( $template ) {
		if ( is_tax(TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_STATE) ) {
			if (($template = trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_CPT . 'properties/tpl.properties.archive_state.php')) == '') 
				$template = trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_CPT . 'properties/tpl.properties.archive.php');
		}
		return $template;
	}	
}



// Admin utils
// -----------------------------------------------------------------

// Show <select> with properties categories in the admin filters area
if (!function_exists('trx_addons_cpt_properties_taxonomy_state_admin_filters')) {
	add_action( 'restrict_manage_posts', 'trx_addons_cpt_properties_taxonomy_state_admin_filters' );
	function trx_addons_cpt_properties_taxonomy_state_admin_filters() {
		trx_addons_admin_filters(TRX_ADDONS_CPT_PROPERTIES_PT, TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_STATE);
	}
}
  
// Clear terms cache on the taxonomy 'state' save
if (!function_exists('trx_addons_cpt_properties_taxonomy_state_admin_clear_cache_state')) {
	add_action( 'edited_'.TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_STATE, 'trx_addons_cpt_properties_taxonomy_state_admin_clear_cache_state', 10, 1 );
	add_action( 'delete_'.TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_STATE, 'trx_addons_cpt_properties_taxonomy_state_admin_clear_cache_state', 10, 1 );
	add_action( 'created_'.TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_STATE, 'trx_addons_cpt_properties_taxonomy_state_admin_clear_cache_state', 10, 1 );
	function trx_addons_cpt_properties_taxonomy_state_admin_clear_cache_state( $term_id=0 ) {  
		trx_addons_admin_clear_cache_terms(TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_STATE);
	}
}

// Get list state by specified country
if ( !function_exists( 'trx_addons_cpt_properties_taxonomy_state_refresh_list_states' ) ) {
	add_filter('trx_addons_filter_refresh_list_states', 'trx_addons_cpt_properties_taxonomy_state_refresh_list_states', 10, 3);
	function trx_addons_cpt_properties_taxonomy_state_refresh_list_states($list, $country, $not_selected=false) {
		$rez = array();
		if ($not_selected) {
			$tax_obj = get_taxonomy(TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_STATE);
//			$rez[0]  = trx_addons_get_not_selected_text( ! empty( $tax_obj->label ) ? $tax_obj->label : __( '- Not Selected -', 'trx_addons' ) );
			$rez[''] = trx_addons_get_not_selected_text( ! empty( $tax_obj->label ) ? $tax_obj->label : __( '- Not Selected -', 'trx_addons' ) );
		}
		return trx_addons_array_merge(
					$rez,
					trx_addons_get_list_terms(false, TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_STATE, array(
											'meta_key' => 'country',
											'meta_value' => $country
											))
					);
	}
}


// Additional parameters to the taxonomy
//--------------------------------------------------------------------------

// Save additional parameters
if (!function_exists('trx_addons_cpt_properties_taxonomy_state_save_custom_fields')) {
	add_action('edited_'.TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_STATE,	'trx_addons_cpt_properties_taxonomy_state_save_custom_fields', 10, 1 );
	add_action('created_'.TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_STATE,	'trx_addons_cpt_properties_taxonomy_state_save_custom_fields', 10, 1 );
	function trx_addons_cpt_properties_taxonomy_state_save_custom_fields($term_id) {
		if (isset($_POST['trx_addons_country'])) {
			trx_addons_set_term_meta(array(
											'term_id' => $term_id,
											'key' => 'country'
											),
										$_POST['trx_addons_country']
										);
		}
	}
}

// Display additional fields
if (!function_exists('trx_addons_cpt_properties_taxonomy_state_show_custom_fields')) {
	add_action(TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_STATE.'_edit_form_fields', 'trx_addons_cpt_properties_taxonomy_state_show_custom_fields', 10, 1 );
	add_action(TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_STATE.'_add_form_fields', 'trx_addons_cpt_properties_taxonomy_state_show_custom_fields', 10, 1 );
	function trx_addons_cpt_properties_taxonomy_state_show_custom_fields($term) {
		$term_id = !empty($term->term_id) ? $term->term_id : 0;
		// Country selector
		echo ((int) $term_id > 0 ? '<tr' : '<div') . ' class="form-field term-country-wrap">'
			. ((int) $term_id > 0 ? '<th valign="top" scope="row">' : '<div>');
		?><label id="trx_addons_country_label" for="trx_addons_country"><?php esc_html_e('Country:', 'trx_addons'); ?></label><?php
		echo ((int) $term_id > 0 ? '</th>' : '</div>')
			. ((int) $term_id > 0 ? '<td valign="top">' : '<div>');
		$country_id = $term_id > 0 
						? trx_addons_get_term_meta(array(
													'taxonomy'	=> TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_STATE,
													'term_id'	=> $term_id,
													'key'		=> 'country'
													)) 
						: '';
		$list = trx_addons_get_list_terms(false, TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_COUNTRY);
		if (count($list) > 0) {
			?><select id="trx_addons_country" class="trx_addons_country_selector_field" name="trx_addons_country"><?php
			foreach ($list as $k=>$v) {
				?><option value="<?php echo esc_attr($k); ?>"<?php if ($k == $country_id) echo ' selected="selected"'; ?>><?php
					echo esc_html($v);
				?></option><?php
			}
			?></select><?php
		}
		echo (int) $term_id > 0 ? '</td></tr>' : '</div></div>';
	}
}

// Create additional column in the terms list
if (!function_exists('trx_addons_cpt_properties_taxonomy_state_add_custom_column')) {
	add_filter('manage_edit-'.TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_STATE.'_columns',	'trx_addons_cpt_properties_taxonomy_state_add_custom_column', 9);
	function trx_addons_cpt_properties_taxonomy_state_add_custom_column( $columns ){
		$columns['location'] = esc_html__('Country', 'trx_addons');
		return $columns;
	}
}

// Fill additional column in the terms list
if (!function_exists('trx_addons_cpt_properties_taxonomy_state_fill_custom_column')) {
	add_action('manage_'.TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_STATE.'_custom_column',	'trx_addons_cpt_properties_taxonomy_state_fill_custom_column', 9, 3);
	function trx_addons_cpt_properties_taxonomy_state_fill_custom_column($output='', $column_name='', $term_id=0) {
		if ($column_name == 'location') {
			$country_id = $term_id > 0 
						? trx_addons_get_term_meta(array(
													'taxonomy'	=> TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_STATE,
													'term_id'	=> $term_id,
													'key'		=> 'country'
													)) 
						: '';
			if ($country_id > 0) {
				$country = get_term_by('id', $country_id, TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_COUNTRY, OBJECT);
				if (!empty($country->name)) {
					?><div class="trx_addons_location trx_addons_meta_row">
						<span class="trx_addons_location_country trx_addons_meta_item"><?php echo esc_html($country->name); ?></span>
					</div><?php
				}
			}
		}
	}
}
