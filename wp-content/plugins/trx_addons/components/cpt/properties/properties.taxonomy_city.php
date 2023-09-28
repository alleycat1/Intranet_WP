<?php
/**
 * ThemeREX Addons Custom post type: Properties (Taxonomy 'City' support)
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
if ( ! defined('TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_CITY') )
		define('TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_CITY', trx_addons_cpt_param('properties', 'taxonomy_city'));

// Register post type and taxonomy
if (!function_exists('trx_addons_cpt_properties_taxonomy_city_init')) {
	add_action( 'init', 'trx_addons_cpt_properties_taxonomy_city_init', 9 );
	function trx_addons_cpt_properties_taxonomy_city_init() {
		
		register_taxonomy(
			TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_CITY,
			TRX_ADDONS_CPT_PROPERTIES_PT,
			apply_filters('trx_addons_filter_register_taxonomy', 
				array(
					'post_type' 		=> TRX_ADDONS_CPT_PROPERTIES_PT,
					'hierarchical'      => true,
					'labels'            => array(
						'name'              => esc_html__( 'City', 'trx_addons' ),
						'singular_name'     => esc_html__( 'City', 'trx_addons' ),
						'search_items'      => esc_html__( 'Search Cities', 'trx_addons' ),
						'all_items'         => esc_html__( 'All Cities', 'trx_addons' ),
						'parent_item'       => esc_html__( 'Parent City', 'trx_addons' ),
						'parent_item_colon' => esc_html__( 'Parent City:', 'trx_addons' ),
						'edit_item'         => esc_html__( 'Edit City', 'trx_addons' ),
						'update_item'       => esc_html__( 'Update City', 'trx_addons' ),
						'add_new_item'      => esc_html__( 'Add New City', 'trx_addons' ),
						'new_item_name'     => esc_html__( 'New City Name', 'trx_addons' ),
						'menu_name'         => esc_html__( 'Cities', 'trx_addons' ),
					),
					'show_ui'           => true,
					'show_admin_column' => true,
					'meta_box_cb'		=> false,
					'query_var'         => true,
					'capabilities'      => array(
						'manage_terms' => 'manage_property_terms',
						'edit_terms'   => 'edit_property_terms',
						'delete_terms' => 'delete_property_terms',
						'assign_terms' => 'assign_property_terms',
					),
					'rewrite'           => array(
												'slug' => trx_addons_cpt_param('properties', 'taxonomy_city_slug'),
												'with_front'   => false,
												'hierarchical' => true
											)
				),
				TRX_ADDONS_CPT_PROPERTIES_PT,
				TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_CITY
			)
		);
	}
}


// Replace standard theme templates
//-------------------------------------------------------------

// Change standard category template for properties categories (groups)
if ( !function_exists( 'trx_addons_cpt_properties_taxonomy_city_taxonomy_template' ) ) {
	add_filter('taxonomy_template',	'trx_addons_cpt_properties_taxonomy_city_taxonomy_template');
	function trx_addons_cpt_properties_taxonomy_city_taxonomy_template( $template ) {
		if ( is_tax(TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_CITY) ) {
			if (($template = trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_CPT . 'properties/tpl.properties.archive_city.php')) == '') 
				$template = trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_CPT . 'properties/tpl.properties.archive.php');
		}
		return $template;
	}	
}



// Admin utils
// -----------------------------------------------------------------

// Show <select> with properties categories in the admin filters area
if (!function_exists('trx_addons_cpt_properties_taxonomy_city_admin_filters')) {
	add_action( 'restrict_manage_posts', 'trx_addons_cpt_properties_taxonomy_city_admin_filters' );
	function trx_addons_cpt_properties_taxonomy_city_admin_filters() {
		trx_addons_admin_filters(TRX_ADDONS_CPT_PROPERTIES_PT, TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_CITY);
	}
}
  
// Clear terms cache on the taxonomy 'city' save
if (!function_exists('trx_addons_cpt_properties_taxonomy_city_admin_clear_cache_city')) {
	add_action( 'edited_'.TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_CITY, 'trx_addons_cpt_properties_taxonomy_city_admin_clear_cache_city', 10, 1 );
	add_action( 'delete_'.TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_CITY, 'trx_addons_cpt_properties_taxonomy_city_admin_clear_cache_city', 10, 1 );
	add_action( 'created_'.TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_CITY, 'trx_addons_cpt_properties_taxonomy_city_admin_clear_cache_city', 10, 1 );
	function trx_addons_cpt_properties_taxonomy_city_admin_clear_cache_city( $term_id=0 ) {  
		trx_addons_admin_clear_cache_terms(TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_CITY);
	}
}

// Get list cities by specified country and state
if ( !function_exists( 'trx_addons_cpt_properties_taxonomy_city_refresh_list_cities' ) ) {
	add_filter('trx_addons_filter_refresh_list_cities', 'trx_addons_cpt_properties_taxonomy_city_refresh_list_cities', 10, 3);
	function trx_addons_cpt_properties_taxonomy_city_refresh_list_cities($list, $country_and_state, $not_selected=false) {
		$rez = array();
		if ($not_selected) {
			$tax_obj = get_taxonomy(TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_CITY);
//			$rez[0]  = trx_addons_get_not_selected_text( ! empty( $tax_obj->label ) ? $tax_obj->label : __( '- Not Selected -', 'trx_addons' ) );
			$rez[''] = trx_addons_get_not_selected_text( ! empty( $tax_obj->label ) ? $tax_obj->label : __( '- Not Selected -', 'trx_addons' ) );
		}
		return trx_addons_array_merge(
					$rez,
					trx_addons_get_list_terms(false, TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_CITY, array(
									'meta_key' => $country_and_state['state'] > 0 ? 'state' : 'country',
									'meta_value' => $country_and_state['state'] > 0 ? $country_and_state['state'] : $country_and_state['country']
								))
					);
	}
}


// Additional parameters to the taxonomy
//--------------------------------------------------------------------------

// Save additional parameters
if (!function_exists('trx_addons_cpt_properties_taxonomy_city_save_custom_fields')) {
	add_action('edited_'.TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_CITY,	'trx_addons_cpt_properties_taxonomy_city_save_custom_fields', 10, 1 );
	add_action('created_'.TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_CITY,	'trx_addons_cpt_properties_taxonomy_city_save_custom_fields', 10, 1 );
	function trx_addons_cpt_properties_taxonomy_city_save_custom_fields($term_id) {
		if (isset($_POST['trx_addons_country'])) {
			trx_addons_set_term_meta(array(
											'term_id' => $term_id,
											'key' => 'country'
											),
										$_POST['trx_addons_country']
										);
		}
		if (isset($_POST['trx_addons_state'])) {
			trx_addons_set_term_meta(array(
											'term_id' => $term_id,
											'key' => 'state'
											),
										$_POST['trx_addons_state']
										);
		}
		if (isset($_POST['trx_addons_image'])) {
			trx_addons_set_term_meta(array(
											'term_id' => $term_id,
											'key' => TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_IMAGE_KEY 
											),
										$_POST['trx_addons_image']
										);
		}
	}
}

// Display additional fields
if (!function_exists('trx_addons_cpt_properties_taxonomy_city_show_custom_fields')) {
	add_action(TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_CITY.'_edit_form_fields', 'trx_addons_cpt_properties_taxonomy_city_show_custom_fields', 10, 1 );
	add_action(TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_CITY.'_add_form_fields', 'trx_addons_cpt_properties_taxonomy_city_show_custom_fields', 10, 1 );
	function trx_addons_cpt_properties_taxonomy_city_show_custom_fields($term) {
		$term_id = !empty($term->term_id) ? $term->term_id : 0;

		// Country selector
		echo ((int) $term_id > 0 ? '<tr' : '<div') . ' class="form-field term-country-wrap' 
			. ((int) $term_id > 0 ? '' : ' trx_addons_column-1_2') 
			. '">'
			. ((int) $term_id > 0 ? '<th valign="top" scope="row">' : '<div>');
		?><label id="trx_addons_country_label" for="trx_addons_country"><?php esc_html_e('Country:', 'trx_addons'); ?></label><?php
		echo ((int) $term_id > 0 ? '</th>' : '</div>')
			. ((int) $term_id > 0 ? '<td valign="top">' : '<div>');
		$country_id = $term_id > 0 
						? trx_addons_get_term_meta(array(
													'taxonomy'	=> TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_CITY,
													'term_id'	=> $term_id,
													'key'		=> 'country'
													)) 
						: '';
		$list = trx_addons_get_list_terms(false, TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_COUNTRY);
		if (count($list) > 0) {
			?><select id="trx_addons_country" class="trx_addons_country_selector_field" name="trx_addons_country"><?php
			foreach ($list as $k=>$v) {
				if (empty($country_id)) $country_id = $k;
				?><option value="<?php echo esc_attr($k); ?>"<?php if ($k == $country_id) echo ' selected="selected"'; ?>><?php
					echo esc_html($v);
				?></option><?php
			}
			?></select><?php
		}
		echo (int) $term_id > 0 ? '</td></tr>' : '</div></div>';

		// State selector
		echo ((int) $term_id > 0 ? '<tr' : '<div') . ' class="form-field term-state-wrap' 
			. ((int) $term_id > 0 ? '' : ' trx_addons_column-1_2') 
			. '">'
			. ((int) $term_id > 0 ? '<th valign="top" scope="row">' : '<div>');
		?><label id="trx_addons_state_label" for="trx_addons_state"><?php esc_html_e('State:', 'trx_addons'); ?></label><?php
		echo ((int) $term_id > 0 ? '</th>' : '</div>')
			. ((int) $term_id > 0 ? '<td valign="top">' : '<div>');
		$state_id = $term_id > 0 
						? trx_addons_get_term_meta(array(
													'taxonomy'	=> TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_CITY,
													'term_id'	=> $term_id,
													'key'		=> 'state'
													)) 
						: '';
		$list = trx_addons_get_list_terms(false, TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_STATE, array(
											'meta_key' => 'country',
											'meta_value' => $country_id
											));
		if (count($list) > 0) {
			?><select id="trx_addons_state" class="trx_addons_state_selector_field" name="trx_addons_state" data-not-selected="true"><?php
				$tax_obj = get_taxonomy(TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_STATE);
				?><option value="0"><?php echo esc_html( trx_addons_get_not_selected_text( ! empty( $tax_obj->label ) ? $tax_obj->label : __( '- Not Selected -', 'trx_addons' ) ) ); ?></option><?php
				foreach ($list as $k=>$v) {
					?><option value="<?php echo esc_attr($k); ?>"<?php if ($k == $state_id) echo ' selected="selected"'; ?>><?php
						echo esc_html($v);
					?></option><?php
				}
			?></select><?php
		}
		echo (int) $term_id > 0 ? '</td></tr>' : '</div></div>';

		// Image
		echo ((int) $term_id > 0 ? '<tr' : '<div') . ' class="form-field">'
			. ((int) $term_id > 0 ? '<th valign="top" scope="row">' : '<div>');
		?><label id="trx_addons_image_label" for="trx_addons_image"><?php esc_html_e('Image URL:', 'trx_addons'); ?></label><?php
		echo ((int) $term_id > 0 ? '</th>' : '</div>')
			. ((int) $term_id > 0 ? '<td valign="top">' : '<div>');
		$img = $term_id > 0 ? trx_addons_get_term_image( $term_id, TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_CITY, TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_IMAGE_KEY ) : '';
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
if (!function_exists('trx_addons_cpt_properties_taxonomy_city_add_custom_column')) {
	add_filter('manage_edit-'.TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_CITY.'_columns',	'trx_addons_cpt_properties_taxonomy_city_add_custom_column', 9);
	function trx_addons_cpt_properties_taxonomy_city_add_custom_column( $columns ){
		$columns['location'] = esc_html__('Country/State', 'trx_addons');
		$columns['image'] = esc_html__('Image', 'trx_addons');
		return $columns;
	}
}

// Fill additional column in the terms list
if (!function_exists('trx_addons_cpt_properties_taxonomy_city_fill_custom_column')) {
	add_action('manage_'.TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_CITY.'_custom_column',	'trx_addons_cpt_properties_taxonomy_city_fill_custom_column', 9, 3);
	function trx_addons_cpt_properties_taxonomy_city_fill_custom_column($output='', $column_name='', $term_id=0) {
		if ($column_name == 'location') {
			$country_id = $term_id > 0 
						? trx_addons_get_term_meta(array(
													'taxonomy'	=> TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_CITY,
													'term_id'	=> $term_id,
													'key'		=> 'country'
													)) 
						: '';
			if ($country_id > 0) {
				$country = get_term_by('id', $country_id, TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_COUNTRY, OBJECT);
				if (!empty($country->name)) {
					?><div class="trx_addons_location trx_addons_meta_row">
						<span class="trx_addons_location_country trx_addons_meta_item"><?php echo esc_html($country->name); ?></span><?php
						$state_id = $term_id > 0 
									? trx_addons_get_term_meta(array(
																'taxonomy'	=> TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_CITY,
																'term_id'	=> $term_id,
																'key'		=> 'state'
																)) 
									: '';
						if ($state_id > 0) {
							$state = get_term_by('id', $state_id, TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_STATE, OBJECT);
							if (!empty($state->name)) {
								?><span class="trx_addons_location_state trx_addons_meta_item"><?php echo esc_html($state->name); ?></span><?php
							}
						}
					?></div><?php
				}
			}
		} else if ($column_name == 'image') {
			$img = $term_id > 0 ? trx_addons_get_term_image( $term_id, TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_CITY, TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_IMAGE_KEY ) : '';
			if (!empty($img)) {
				?><img class="trx_addons_image_selector_preview trx_addons_image_preview" src="<?php
							echo esc_url(trx_addons_add_thumb_size($img, trx_addons_get_thumb_size('tiny')));
						?>" alt="<?php esc_attr_e('City preview', 'trx_addons'); ?>"><?php
			}
		}
	}
}
