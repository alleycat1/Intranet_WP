<?php
/**
 * ThemeREX Addons Custom post type: Properties (Taxonomy 'Neighborhood' support)
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
if ( ! defined('TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_NEIGHBORHOOD') )
		define('TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_NEIGHBORHOOD', trx_addons_cpt_param('properties', 'taxonomy_neighborhood'));

// Register post type and taxonomy
if (!function_exists('trx_addons_cpt_properties_taxonomy_neighborhood_init')) {
	add_action( 'init', 'trx_addons_cpt_properties_taxonomy_neighborhood_init', 9 );
	function trx_addons_cpt_properties_taxonomy_neighborhood_init() {
		
		register_taxonomy(
			TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_NEIGHBORHOOD,
			TRX_ADDONS_CPT_PROPERTIES_PT,
			apply_filters('trx_addons_filter_register_taxonomy', 
				array(
					'post_type' 		=> TRX_ADDONS_CPT_PROPERTIES_PT,
					'hierarchical'      => true,
					'labels'            => array(
						'name'              => esc_html__( 'Neighborhood', 'trx_addons' ),
						'singular_name'     => esc_html__( 'Neighborhood', 'trx_addons' ),
						'search_items'      => esc_html__( 'Search Neighborhoods', 'trx_addons' ),
						'all_items'         => esc_html__( 'All Neighborhoods', 'trx_addons' ),
						'parent_item'       => esc_html__( 'Parent Neighborhood', 'trx_addons' ),
						'parent_item_colon' => esc_html__( 'Parent Neighborhood:', 'trx_addons' ),
						'edit_item'         => esc_html__( 'Edit Neighborhood', 'trx_addons' ),
						'update_item'       => esc_html__( 'Update Neighborhood', 'trx_addons' ),
						'add_new_item'      => esc_html__( 'Add New Neighborhood', 'trx_addons' ),
						'new_item_name'     => esc_html__( 'New Neighborhood Name', 'trx_addons' ),
						'menu_name'         => esc_html__( 'Neighborhoods', 'trx_addons' ),
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
												'slug'         => trx_addons_cpt_param('properties', 'taxonomy_neighborhood_slug'),
												'with_front'   => false,
												'hierarchical' => true
											)
				),
				TRX_ADDONS_CPT_PROPERTIES_PT,
				TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_NEIGHBORHOOD
			)
		);
	}
}


// Replace standard theme templates
//-------------------------------------------------------------

// Change standard category template for properties categories (groups)
if ( !function_exists( 'trx_addons_cpt_properties_taxonomy_neighborhood_taxonomy_template' ) ) {
	add_filter('taxonomy_template',	'trx_addons_cpt_properties_taxonomy_neighborhood_taxonomy_template');
	function trx_addons_cpt_properties_taxonomy_neighborhood_taxonomy_template( $template ) {
		if ( is_tax(TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_NEIGHBORHOOD) ) {
			if (($template = trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_CPT . 'properties/tpl.properties.archive_neighborhood.php')) == '') 
				$template = trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_CPT . 'properties/tpl.properties.archive.php');
		}
		return $template;
	}	
}



// Admin utils
// -----------------------------------------------------------------

// Show <select> with properties categories in the admin filters area
if (!function_exists('trx_addons_cpt_properties_taxonomy_neighborhood_admin_filters')) {
	add_action( 'restrict_manage_posts', 'trx_addons_cpt_properties_taxonomy_neighborhood_admin_filters' );
	function trx_addons_cpt_properties_taxonomy_neighborhood_admin_filters() {
		trx_addons_admin_filters(TRX_ADDONS_CPT_PROPERTIES_PT, TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_NEIGHBORHOOD);
	}
}
  
// Clear terms cache on the taxonomy 'neighborhood' save
if (!function_exists('trx_addons_cpt_properties_taxonomy_neighborhood_admin_clear_cache_neighborhood')) {
	add_action( 'edited_'.TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_NEIGHBORHOOD, 'trx_addons_cpt_properties_taxonomy_neighborhood_admin_clear_cache_neighborhood', 10, 1 );
	add_action( 'delete_'.TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_NEIGHBORHOOD, 'trx_addons_cpt_properties_taxonomy_neighborhood_admin_clear_cache_neighborhood', 10, 1 );
	add_action( 'created_'.TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_NEIGHBORHOOD, 'trx_addons_cpt_properties_taxonomy_neighborhood_admin_clear_cache_neighborhood', 10, 1 );
	function trx_addons_cpt_properties_taxonomy_neighborhood_admin_clear_cache_neighborhood( $term_id=0 ) {  
		trx_addons_admin_clear_cache_terms(TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_NEIGHBORHOOD);
	}
}

// Get list neighborhoods by specified city
if ( !function_exists( 'trx_addons_cpt_properties_taxonomy_neighborhood_refresh_list_neighborhoods' ) ) {
	add_filter('trx_addons_filter_refresh_list_neighborhoods', 'trx_addons_cpt_properties_taxonomy_neighborhood_refresh_list_neighborhoods', 10, 3);
	function trx_addons_cpt_properties_taxonomy_neighborhood_refresh_list_neighborhoods($list, $city, $not_selected=false) {
		$rez = array();
		if ($not_selected) {
			$tax_obj = get_taxonomy(TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_NEIGHBORHOOD);
//			$rez[0]  = trx_addons_get_not_selected_text( ! empty( $tax_obj->label ) ? $tax_obj->label : __( '- Not Selected -', 'trx_addons' ) );
			$rez[''] = trx_addons_get_not_selected_text( ! empty( $tax_obj->label ) ? $tax_obj->label : __( '- Not Selected -', 'trx_addons' ) );
		}
		return trx_addons_array_merge(
					$rez,
					trx_addons_get_list_terms(false, TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_NEIGHBORHOOD, array(
						'meta_key' => 'city',
						'meta_value' => $city
						))
					);
	}
}


// Additional parameters to the taxonomy
//--------------------------------------------------------------------------

// Save additional parameters
if (!function_exists('trx_addons_cpt_properties_taxonomy_neighborhood_save_custom_fields')) {
	add_action('edited_'.TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_NEIGHBORHOOD,	'trx_addons_cpt_properties_taxonomy_neighborhood_save_custom_fields', 10, 1 );
	add_action('created_'.TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_NEIGHBORHOOD,	'trx_addons_cpt_properties_taxonomy_neighborhood_save_custom_fields', 10, 1 );
	function trx_addons_cpt_properties_taxonomy_neighborhood_save_custom_fields($term_id) {
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
		if (isset($_POST['trx_addons_city'])) {
			trx_addons_set_term_meta(array(
											'term_id' => $term_id,
											'key' => 'city'
											),
										$_POST['trx_addons_city']
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
if (!function_exists('trx_addons_cpt_properties_taxonomy_neighborhood_show_custom_fields')) {
	add_action(TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_NEIGHBORHOOD.'_edit_form_fields', 'trx_addons_cpt_properties_taxonomy_neighborhood_show_custom_fields', 10, 1 );
	add_action(TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_NEIGHBORHOOD.'_add_form_fields', 'trx_addons_cpt_properties_taxonomy_neighborhood_show_custom_fields', 10, 1 );
	function trx_addons_cpt_properties_taxonomy_neighborhood_show_custom_fields($term) {
		$term_id = !empty($term->term_id) ? $term->term_id : 0;

		// Country selector
		echo ((int) $term_id > 0 ? '<tr' : '<div') . ' class="form-field term-country-wrap' 
			. ((int) $term_id > 0 ? '' : ' trx_addons_column-1_3') 
			. '">'
			. ((int) $term_id > 0 ? '<th valign="top" scope="row">' : '<div>');
		?><label id="trx_addons_country_label" for="trx_addons_country"><?php esc_html_e('Country:', 'trx_addons'); ?></label><?php
		echo ((int) $term_id > 0 ? '</th>' : '</div>')
			. ((int) $term_id > 0 ? '<td valign="top">' : '<div>');
		$country_id = $term_id > 0 
						? trx_addons_get_term_meta(array(
													'taxonomy'	=> TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_NEIGHBORHOOD,
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
			. ((int) $term_id > 0 ? '' : ' trx_addons_column-1_3') 
			. '">'
			. ((int) $term_id > 0 ? '<th valign="top" scope="row">' : '<div>');
		?><label id="trx_addons_state_label" for="trx_addons_state"><?php esc_html_e('State:', 'trx_addons'); ?></label><?php
		echo ((int) $term_id > 0 ? '</th>' : '</div>')
			. ((int) $term_id > 0 ? '<td valign="top">' : '<div>');
		$state_id = $term_id > 0 
						? trx_addons_get_term_meta(array(
													'taxonomy'	=> TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_NEIGHBORHOOD,
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

		// City selector
		echo ((int) $term_id > 0 ? '<tr' : '<div') . ' class="form-field term-city-wrap' 
			. ((int) $term_id > 0 ? '' : ' trx_addons_column-1_3') 
			. '">'
			. ((int) $term_id > 0 ? '<th valign="top" scope="row">' : '<div>');
		?><label id="trx_addons_city_label" for="trx_addons_city"><?php esc_html_e('City:', 'trx_addons'); ?></label><?php
		echo ((int) $term_id > 0 ? '</th>' : '</div>')
			. ((int) $term_id > 0 ? '<td valign="top">' : '<div>');
		$city_id = $term_id > 0 
						? trx_addons_get_term_meta(array(
													'taxonomy'	=> TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_NEIGHBORHOOD,
													'term_id'	=> $term_id,
													'key'		=> 'city'
													)) 
						: '';
		if ($state_id > 0)
			$args = array(
							'meta_query' => array(
								array(
									'type' => 'NUMERIC',
									'key' => 'country',
									'value' => $country_id
								),
								array(
									'type' => 'NUMERIC',
									'key' => 'state',
									'value' => $state_id
								)
							)
						);
		else
			$args = array(
						'meta_key' => 'country',
						'meta_value' => $country_id
						);
		$list = trx_addons_get_list_terms(false, TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_CITY, $args);
		if (count($list) > 0) {
			?><select id="trx_addons_city" class="trx_addons_city_selector_field" name="trx_addons_city"><?php
			foreach ($list as $k=>$v) {
				?><option value="<?php echo esc_attr($k); ?>"<?php if ($k == $city_id) echo ' selected="selected"'; ?>><?php
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
		$img = $term_id > 0 ? trx_addons_get_term_image( $term_id, TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_NEIGHBORHOOD, TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_IMAGE_KEY ) : '';
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
if (!function_exists('trx_addons_cpt_properties_taxonomy_neighborhood_add_custom_column')) {
	add_filter('manage_edit-'.TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_NEIGHBORHOOD.'_columns',	'trx_addons_cpt_properties_taxonomy_neighborhood_add_custom_column', 9);
	function trx_addons_cpt_properties_taxonomy_neighborhood_add_custom_column( $columns ){
		$columns['location'] = esc_html__('Country/State', 'trx_addons');
		$columns['image'] = esc_html__('Image', 'trx_addons');
		return $columns;
	}
}

// Fill additional column in the terms list
if (!function_exists('trx_addons_cpt_properties_taxonomy_neighborhood_fill_custom_column')) {
	add_action('manage_'.TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_NEIGHBORHOOD.'_custom_column',	'trx_addons_cpt_properties_taxonomy_neighborhood_fill_custom_column', 9, 3);
	function trx_addons_cpt_properties_taxonomy_neighborhood_fill_custom_column($output='', $column_name='', $term_id=0) {
		if ($column_name == 'location') {
			$country_id = $term_id > 0 
						? trx_addons_get_term_meta(array(
													'taxonomy'	=> TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_NEIGHBORHOOD,
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
																'taxonomy'	=> TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_NEIGHBORHOOD,
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
						$city_id = $term_id > 0 
									? trx_addons_get_term_meta(array(
																'taxonomy'	=> TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_NEIGHBORHOOD,
																'term_id'	=> $term_id,
																'key'		=> 'city'
																)) 
									: '';
						if ($city_id > 0) {
							$city = get_term_by('id', $city_id, TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_CITY, OBJECT);
							if (!empty($city->name)) {
								?><span class="trx_addons_location_city trx_addons_meta_item"><?php echo esc_html($city->name); ?></span><?php
							}
						}
					?></div><?php
				}
			}
		} else if ($column_name == 'image') {
			$img = $term_id > 0 ? trx_addons_get_term_image( $term_id, TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_NEIGHBORHOOD, TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_IMAGE_KEY ) : '';
			if (!empty($img)) {
				?><img class="trx_addons_image_selector_preview trx_addons_image_preview" src="<?php
							echo esc_url(trx_addons_add_thumb_size($img, trx_addons_get_thumb_size('tiny')));
						?>" alt="<?php esc_attr_e('Neighborhood preview', 'trx_addons'); ?>"><?php
			}
		}
	}
}
