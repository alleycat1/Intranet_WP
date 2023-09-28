<?php
/**
 * The style "map" of the Properties
 *
 * @package ThemeREX Addons
 * @since v1.6.22
 */

$map_type = trx_addons_get_option( 'properties_map', '' );
if ( empty( $map_type ) || trx_addons_is_off( $map_type ) ) {
	$map_type = function_exists('trx_addons_sc_googlemap')
				&& ( trx_addons_get_option('api_google') != '' || ( ! empty($args['count']) && $args['count'] == 1 ) )
					? 'google'
					: ( function_exists('trx_addons_sc_osmap')
						? 'openstreet' 
						: 'google'
						);
}
if ( ! empty( $map_type ) ) {
	$on_property_post = trx_addons_is_single() && get_post_type()==TRX_ADDONS_CPT_PROPERTIES_PT;
	$args = get_query_var('trx_addons_args_sc_properties');
	$query_args = trx_addons_cpt_properties_query_params_to_args(
					isset($_GET['properties_type']) 
					|| (trx_addons_is_single() && get_post_type()==TRX_ADDONS_CPT_PROPERTIES_PT)
					|| (trx_addons_is_single() && get_post_type()==TRX_ADDONS_CPT_AGENTS_PT)
					|| (is_post_type_archive(TRX_ADDONS_CPT_PROPERTIES_PT) && (int) trx_addons_get_value_gp('compare') == 1)
					|| is_tax(TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_NEIGHBORHOOD)
					|| is_tax(TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_CITY)
					|| is_tax(TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_STATE)
					|| is_tax(TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_COUNTRY)
					|| is_tax(TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_TYPE)
					|| is_tax(TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_STATUS)
					|| is_tax(TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_LABELS)
						? array()
						: array(
								'properties_type' => $args['properties_type'],
								'properties_status' => $args['properties_status'],
								'properties_labels' => $args['properties_labels'],
								'properties_country' => $args['properties_country'],
								'properties_state' => $args['properties_state'],
								'properties_city' => $args['properties_city'],
								'properties_neighborhood' => $args['properties_neighborhood'],
								'properties_order' => $args['orderby'] . '_' . $args['order']
								),
					true);
	if (!isset($_GET['properties_type'])) {
		if (trx_addons_is_single() && get_post_type()==TRX_ADDONS_CPT_PROPERTIES_PT) {
			$args['ids'] = get_the_ID();
			$query_args['post_type'] = TRX_ADDONS_CPT_PROPERTIES_PT;
		} else if (is_post_type_archive(TRX_ADDONS_CPT_PROPERTIES_PT) && (int) trx_addons_get_value_gp('compare') == 1) {
			$posts = array();
			$list = urldecode(trx_addons_get_value_gpc('trx_addons_properties_compare_list', ''));
			$list = !empty($list) ? json_decode($list, true) : array();
			if (is_array($list)) {
				foreach ($list as $k=>$v) {
					$id = (int) str_replace('id_', '', $k);
					if ($id > 0) $posts[] = $id;
				}
			}
			if (count($posts) > 0) {
				$args['ids'] = join(',', $posts);
			}
		}
// Attention! Parameter 'suppress_filters' is damage WPML-queries!
		$query_args['ignore_sticky_posts'] = true;
		if ( empty( $args['ids'] ) || count( explode( ',', $args['ids'] ) ) > $args['count'] ) {
			if (empty($query_args['posts_per_page'])) {
				$query_args['posts_per_page'] = $args['count'];
				$query_args['offset'] = $args['offset'];
			}
		} else {
			$query_args = trx_addons_query_add_posts_and_cats($query_args, $args['ids']);
		}
	}

	$query_args = apply_filters( 'trx_addons_filter_query_args', $query_args, 'sc_properties' );
	
	$query = new WP_Query( $query_args );
	
	if ($query->post_count > 0) {

		$args = apply_filters( 'trx_addons_filter_sc_prepare_atts_before_output', $args, $query_args, $query, 'properties.map' );

		//if ($args['count'] > $query->post_count) $args['count'] = $query->post_count;
		$posts_count = ($args['count'] > $query->post_count) ? $query->post_count : $args['count'];
		?><div<?php if (!empty($args['id'])) echo ' id="'.esc_attr($args['id']).'"'; ?> class="sc_properties sc_properties_<?php 
				echo esc_attr($args['type']);
				if (!empty($args['class'])) echo ' '.esc_attr($args['class']); 
				?>"<?php
			if (!empty($args['css'])) echo ' style="'.esc_attr($args['css']).'"';
			?>><?php
	
			trx_addons_sc_show_titles('sc_properties', $args);
			
			?><div class="sc_properties_content sc_item_content"><?php
	
			$markers = array();
			
			$default_icon = trx_addons_get_option('properties_marker');
			if (empty($default_icon)) $default_icon = trx_addons_remove_protocol(trx_addons_get_option("api_{$map_type}_marker"));
			if (empty($default_icon)) $default_icon = trx_addons_get_file_url(TRX_ADDONS_PLUGIN_CPT . 'properties/properties.png');
			
			$args['columns'] = 1;

			while ( $query->have_posts() ) { $query->the_post();
				$meta = (array)get_post_meta(get_the_ID(), 'trx_addons_options', true);
				if (($on_property_post && empty($meta['show_map'])) || empty($meta['location'])) continue;
				if (empty($meta['marker'])) {
					$terms = get_the_terms(get_the_ID(), TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_TYPE);
					if (is_array($terms) && count($terms)>0) {
						$term = trx_addons_array_get_first($terms, false);
						$term_id = $term->term_id;
						$icon = $term_id > 0 ? trx_addons_get_term_image( $term_id, TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_TYPE, TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_IMAGE_KEY ) : '';
					} else {
						$icon = '';
					}
				} else {
					$icon = $meta['marker'];
				}
				$latlng = explode(',', $meta['location']);
				$markers[] = array(
								'title' => get_the_title(),
								'description' => trx_addons_get_template_part_as_string(
													TRX_ADDONS_PLUGIN_CPT . 'properties/tpl.properties.default-item.php',
													'trx_addons_args_sc_properties',
													$args),
								'address' => trim($latlng[0]).','.trim($latlng[1]),
								'icon' => !empty($icon) ? $icon : $default_icon
								);
			}
			wp_reset_postdata();

			// Display map
			$map_args = array(
				"markers" => $markers,
				"zoom" => count($markers)>1 ? 0 : 16,
				"height" => max(100, $args['map_height']),
				"id" => !empty($args['id']) ? $args['id'].'_map' : "",
				"style" => $map_type == 'google'
							? trx_addons_array_get_first( trx_addons_get_list_sc_googlemap_styles() )
							: ( $map_type == 'openstreet'
								? trx_addons_array_get_first( trx_addons_get_list_sc_osmap_styles() )
								: 'default'
								)
			);

			if ( $map_type == 'google' ) {
				trx_addons_show_layout( trx_addons_sc_googlemap($map_args) );
			} else if ($map_type == 'openstreet' ) {
				trx_addons_show_layout( trx_addons_sc_osmap($map_args) );
			}

			?></div><?php		// .sc_properties_content
	
			trx_addons_sc_show_links('sc_properties', $args);
	
		?></div><?php
	}
}
