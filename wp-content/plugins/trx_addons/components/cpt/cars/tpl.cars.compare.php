<?php
/**
 * The template to display the cars compare table
 *
 * @package ThemeREX Addons
 * @since v1.6.25
 */

get_header(); 

do_action('trx_addons_action_start_compare_list');

if (have_posts()) {
	$output = array(
		'thumb'			=> array(''),
		'title'			=> array(__('Name', 'trx_addons')),
		'price'			=> array(__('Price', 'trx_addons')),
		'type'			=> array(__('Type', 'trx_addons')),
		'maker'			=> array(__('Manufacturer', 'trx_addons')),
		'model'			=> array(__('Model', 'trx_addons')),
		'status'		=> array(__('Status', 'trx_addons')),
		'city'			=> array(__('City', 'trx_addons')),
		'fuel'			=> array(__('Fuel', 'trx_addons')),
		'transmission'	=> array(__('Transmission', 'trx_addons')),
		'type_drive'	=> array(__('Type of drive', 'trx_addons')),
		'engine_type'	=> array(__('Engine type', 'trx_addons')),
		'engine_size'	=> array(__('Engine size', 'trx_addons')),
		'engine_power_horses'	=> array(__('Engine power (in horses)', 'trx_addons')),
		'engine_power_wt'		=> array(__('Engine power (in watts)', 'trx_addons')),
		'mileage'		=> array(__('Mileage', 'trx_addons')),
		'produced'		=> array(__('Produced', 'trx_addons')),
		'id'			=> array(__('Car ID', 'trx_addons'))
	);
	$features = trx_addons_get_list_terms(false, TRX_ADDONS_CPT_CARS_TAXONOMY_FEATURES);
	if (is_array($features)) {
		foreach ($features as $id=>$title)
			$output['feature_'.trim($id)] = array($title);
	}

	$links_title = __('Show all cars from %s', 'trx_addons');

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
								? '<span class="cars_price_delimiter"></span>'
								: '')
							. (!empty($meta['price2'])
								? trx_addons_format_price($meta['price2'])
								: '')
							. (!empty($meta['after_price'])
								? ' '.trx_addons_prepare_macros($meta['after_price'])
								: '');
		$output['type'][]  = trx_addons_get_post_terms(', ', get_the_ID(), TRX_ADDONS_CPT_CARS_TAXONOMY_TYPE);
		$output['status'][]= trx_addons_get_post_terms(', ', get_the_ID(), TRX_ADDONS_CPT_CARS_TAXONOMY_STATUS);
		$output['maker'][] = trx_addons_get_post_terms(', ', get_the_ID(), TRX_ADDONS_CPT_CARS_TAXONOMY_MAKER);
		$output['model'][] = trx_addons_get_post_terms(', ', get_the_ID(), TRX_ADDONS_CPT_CARS_TAXONOMY_MODEL);
		$output['city'][]  = trx_addons_get_post_terms(', ', get_the_ID(), TRX_ADDONS_CPT_CARS_TAXONOMY_CITY);
		$output['fuel'][]  = trx_addons_get_option_title(TRX_ADDONS_CPT_CARS_PT, 'fuel', $meta['fuel']);
		$output['transmission'][]= trx_addons_get_option_title(TRX_ADDONS_CPT_CARS_PT, 'transmission', $meta['transmission']);
		$output['type_drive'][]  = trx_addons_get_option_title(TRX_ADDONS_CPT_CARS_PT, 'type_drive', $meta['type_drive']); 
		$output['engine_type'][] = $meta['engine_type'];
		$output['engine_size'][] = $meta['engine_size'] . ($meta['engine_size_prefix'] 
														? ' ' . trx_addons_prepare_macros($meta['engine_size_prefix'])
														: '');
		$output['engine_power_horses'][] = $meta['engine_power_horses'];
		$output['engine_power_wt'][] = $meta['engine_power_wt'];
		$output['mileage'][] = trx_addons_num2kilo($meta['mileage']). ($meta['mileage_prefix'] 
														? ' ' . trx_addons_prepare_macros($meta['mileage_prefix'])
														: '');
		$output['produced'][] = $meta['produced'];
		$output['id'][] = $meta['id'];
		// Add features
		$features = get_the_terms(get_the_ID(), TRX_ADDONS_CPT_CARS_TAXONOMY_FEATURES);
		$features_in_prop = array();
		if ( !empty( $features ) ) {
			foreach( $features as $term )
				$features_in_prop['feature_'.trim($term->term_id)][] = 1;
		}
		foreach( $output as $k=>$v ) {
			if (substr($k, 0, 8) == 'feature_') {
				$output[$k][] = !empty($features_in_prop[$k])
									? '<span class="cars_feature_present trx_addons_icon-ok"></span>'
									: '';
			}
		}
	}
	wp_reset_postdata();

	?><div class="sc_cars sc_cars_compare">
		<table class="sc_cars_compare_table" border="0" cellpadding="0" cellspacing="0" width="100%">
			<?php
			foreach( $output as $k=>$v ) {
				?><tr class="sc_cars_compare_<?php echo esc_attr($k); ?>"><?php
					for ($i=0; $i<count($v); $i++) {
						?><td class="sc_cars_compare_<?php echo ($i==0 ? 'title' : 'data'); ?>"><?php
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
