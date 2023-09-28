<?php
/**
 * The template to display the properties compare table
 *
 * @package ThemeREX Addons
 * @since v1.6.24
 */

get_header(); 

do_action('trx_addons_action_start_compare_list');

if (have_posts()) {

	$output = array(
		'thumb'			=> array(''),
		'title'			=> array(__('Name', 'trx_addons')),
		'price'			=> array(__('Price', 'trx_addons')),
		'type'			=> array(__('Type', 'trx_addons')),
		'status'		=> array(__('Status', 'trx_addons')),
		'address'		=> array(__('Address', 'trx_addons')),
		'neighborhood'	=> array(__('Neighborhood', 'trx_addons')),
		'city'			=> array(__('City', 'trx_addons')),
		'state'			=> array(__('State', 'trx_addons')),
		'country'		=> array(__('Country', 'trx_addons')),
		'area'			=> array(__('Area size', 'trx_addons')),
		'land'			=> array(__('Land size', 'trx_addons')),
		'bedrooms'		=> array(__('Bedrooms', 'trx_addons')),
		'bathrooms'		=> array(__('Bathrooms', 'trx_addons')),
		'garages'		=> array(__('Garages', 'trx_addons')),
		'built'			=> array(__('Year built', 'trx_addons')),
		'id'			=> array(__('Property ID', 'trx_addons'))
	);
	$features = trx_addons_get_list_terms(false, TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_FEATURES);
	if (is_array($features)) {
		foreach ($features as $id=>$title)
			$output['feature_'.trim($id)] = array($title);
	}

	$links_title = __('Show all properties from %s', 'trx_addons');

	while ( have_posts() ) { the_post(); 
		$meta = (array)get_post_meta(get_the_ID(), 'trx_addons_options', true);
		$output['thumb'][] = has_post_thumbnail() 
									? get_the_post_thumbnail(get_the_ID(), trx_addons_get_thumb_size('medium'), array(
																												'alt' => get_the_title()
																												))
									: '';
		$output['title'][] = '<a href="'.esc_url(get_permalink()).'">' . esc_html(get_the_title()) . '</a>'; 
		$output['price'][] = (!empty($meta['before_price'])
								? trx_addons_prepare_macros($meta['before_price']).' '
								: '')
							. (!empty($meta['price'])
								? trx_addons_format_price($meta['price'])
								: '')
							. (!empty($meta['price']) && !empty($meta['price2'])
								? '<span class="properties_price_delimiter"></span>'
								: '')
							. (!empty($meta['price2'])
								? trx_addons_format_price($meta['price2'])
								: '')
							. (!empty($meta['after_price'])
								? ' '.trx_addons_prepare_macros($meta['after_price'])
								: '');
		$output['type'][] = trx_addons_get_post_terms(', ', get_the_ID(), TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_TYPE);
		$output['status'][] = trx_addons_get_post_terms(', ', get_the_ID(), TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_STATUS);
		$output['address'][] = $meta['address'];
		$output['neighborhood'][] = empty($meta['neighborhood']) 
										? '' 
										: trx_addons_get_term_link( (int)$meta['neighborhood'],
											TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_NEIGHBORHOOD,
											array('echo'=>false, 'title'=>$links_title)
										);
		$output['city'][] = empty($meta['city']) 
										? '' 
										: trx_addons_get_term_link( (int)$meta['city'],
											TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_CITY,
											array('echo'=>false, 'title'=>$links_title)
										);
		$output['state'][] = empty($meta['state']) 
										? '' 
										: trx_addons_get_term_link( (int)$meta['state'],
											TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_STATE,
											array('echo'=>false, 'title'=>$links_title)
										);
		$output['country'][] = empty($meta['country']) 
										? '' 
										: trx_addons_get_term_link( (int)$meta['country'],
											TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_COUNTRY,
											array('echo'=>false, 'title'=>$links_title)
										);
		$output['area'][] = $meta['area_size'] . ($meta['area_size_prefix'] 
														? ' ' . trx_addons_prepare_macros($meta['area_size_prefix'])
														: ''
													);
		$output['land'][] = $meta['land_size'] . ($meta['land_size_prefix'] 
														? ' ' . trx_addons_prepare_macros($meta['land_size_prefix'])
														: ''
													);
		$output['bedrooms'][] = $meta['bedrooms'];
		$output['bathrooms'][] = $meta['bathrooms'];
		$output['garages'][] = $meta['garages'] . ($meta['garage_size'] 
															? ' (' . trx_addons_prepare_macros($meta['garage_size']) . ')'
															: ''
													);
		$output['built'][] = $meta['built'];
		$output['id'][] = $meta['id'];
		// Add features
		$features = get_the_terms(get_the_ID(), TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_FEATURES);
		$features_in_prop = array();
		if ( !empty( $features ) ) {
			foreach( $features as $term )
				$features_in_prop['feature_'.trim($term->term_id)][] = 1;
		}
		foreach( $output as $k=>$v ) {
			if (substr($k, 0, 8) == 'feature_') {
				$output[$k][] = !empty($features_in_prop[$k])
									? '<span class="properties_feature_present trx_addons_icon-ok"></span>'
									: '';
			}
		}
	}
	wp_reset_postdata();

	?><div class="sc_properties sc_properties_compare">
		<table class="sc_properties_compare_table" border="0" cellpadding="0" cellspacing="0" width="100%">
			<?php
			foreach( $output as $k=>$v ) {
				?><tr class="sc_properties_compare_<?php echo esc_attr($k); ?>"><?php
					for ($i=0; $i<count($v); $i++) {
						?><td class="sc_properties_compare_<?php echo ($i==0 ? 'title' : 'data'); ?>"><?php
							trx_addons_show_layout($v[$i]);
						?></td><?php
					}
				?></tr><?php
			}
			?>
		</table>
	</div><?php

} else {

	trx_addons_get_template_part('templates/tpl.posts-none.php');

}

do_action('trx_addons_action_end_compare_list');

get_footer();
