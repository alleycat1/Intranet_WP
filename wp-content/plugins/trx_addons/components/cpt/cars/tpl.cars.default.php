<?php
/**
 * The style "default" of the Cars
 *
 * @package ThemeREX Addons
 * @since v1.6.25
 */

$args = get_query_var('trx_addons_args_sc_cars');
$query_args = trx_addons_cpt_cars_query_params_to_args(array(
				'cars_type' => $args['cars_type'],
				'cars_status' => $args['cars_status'],
				'cars_labels' => $args['cars_labels'],
				'cars_maker' => $args['cars_maker'],
				'cars_model' => $args['cars_model'],
				'cars_city' => $args['cars_city'],
				'cars_fuel' => $args['cars_fuel'],
				'cars_type_drive' => $args['cars_type_drive'],
				'cars_transmission' => $args['cars_transmission'],
				'cars_order' => $args['orderby'] . '_' . $args['order']
				), true);

// Attention! Parameter 'suppress_filters' is damage WPML-queries!
$query_args['ignore_sticky_posts'] = true;

if ( empty( $args['ids'] ) || count( explode( ',', $args['ids'] ) ) > $args['count'] ) {
	$query_args['posts_per_page'] = $args['count'];
	if ( !trx_addons_is_off($args['pagination']) && $args['page'] > 1 ) {
		if ( empty( $args['offset'] ) ) {
			$query_args['paged'] = $args['page'];
		} else {
			$query_args['offset'] = $args['offset'] + $args['count'] * ( $args['page'] - 1 );
		}
	} else {
		$query_args['offset'] = $args['offset'];
	}
} else {
	$query_args = trx_addons_query_add_posts_and_cats($query_args, $args['ids']);
}

// Exclude posts
if ( ! empty( $args['posts_exclude'] ) ) {
	$query_args['post__not_in'] = is_array( $args['posts_exclude'] )
									? $args['posts_exclude']
									: explode( ',', str_replace( array( ';', ' ' ), array( ',', '' ), $args['posts_exclude'] ) );
}

$query_args = apply_filters( 'trx_addons_filter_query_args', $query_args, 'sc_cars' );

$query = new WP_Query( $query_args );

if ($query->post_count > 0) {

	$args = apply_filters( 'trx_addons_filter_sc_prepare_atts_before_output', $args, $query_args, $query, 'cars.default' );

	//if ($args['count'] > $query->post_count) $args['count'] = $query->post_count;
	$posts_count = ($args['count'] > $query->post_count) ? $query->post_count : $args['count'];
	if ($args['columns'] < 1) $args['columns'] = $posts_count;
	$args['columns'] = max(1, min(12, (int) $args['columns']));
	if (!empty($args['columns_tablet'])) $args['columns_tablet'] = max(1, min(12, (int) $args['columns_tablet']));
	if (!empty($args['columns_mobile'])) $args['columns_mobile'] = max(1, min(12, (int) $args['columns_mobile']));
	$args['slider'] = $args['slider'] > 0 && $posts_count > $args['columns'];
	$args['slides_space'] = max(0, (int) $args['slides_space']);
	?><div<?php if (!empty($args['id'])) echo ' id="'.esc_attr($args['id']).'"'; ?> class="sc_cars sc_cars_<?php 
			echo esc_attr($args['type']);
			if (!empty($args['class'])) echo ' '.esc_attr($args['class']); 
			?>"<?php
		if (!empty($args['css'])) echo ' style="'.esc_attr($args['css']).'"';
		?>><?php

		trx_addons_sc_show_titles('sc_cars', $args);
		
		if ($args['slider']) {
			$args['slides_min_width'] = 200;
			trx_addons_sc_show_slider_wrap_start('sc_services', $args);
		} else if ($args['columns'] > 1) {
			?><div class="sc_cars_columns_wrap sc_item_columns sc_item_posts_container sc_item_columns_<?php
							echo esc_attr($args['columns']);
							echo ' ' . esc_attr(trx_addons_get_columns_wrap_class())
								. ' columns_padding_bottom'
								. esc_attr( trx_addons_add_columns_in_single_row( $args['columns'], $query ) );
						?>"><?php
		} else {
			?><div class="sc_cars_content sc_item_content sc_item_posts_container sc_cars_columns_1 sc_item_columns_1"><?php
		}

		while ( $query->have_posts() ) { $query->the_post();
			trx_addons_get_template_part(array(
											TRX_ADDONS_PLUGIN_CPT . 'cars/tpl.'.trx_addons_esc($args['type']).'-item.php',
											TRX_ADDONS_PLUGIN_CPT . 'cars/tpl.cars.default-item.php'
											),
											'trx_addons_args_sc_cars',
											$args
										);
		}

		wp_reset_postdata();
	
		?></div><?php		// .swiper-wrapper || .sc_cars_columns || .sc_cars_content

		if ($args['slider']) {
			trx_addons_sc_show_slider_wrap_end('sc_cars', $args);
		}
		
		trx_addons_sc_show_pagination('sc_cars', $args, $query);

		trx_addons_sc_show_links('sc_cars', $args);

	?></div><?php
}
