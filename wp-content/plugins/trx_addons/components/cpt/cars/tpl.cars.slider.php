<?php
/**
 * The style "slider" of the Cars
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
	$query_args['offset'] = $args['offset'];
} else {
	$query_args = trx_addons_query_add_posts_and_cats($query_args, $args['ids']);
}

$query_args = apply_filters( 'trx_addons_filter_query_args', $query_args, 'sc_cars' );

$query = new WP_Query( $query_args );

if ($query->post_count > 0) {

	$args = apply_filters( 'trx_addons_filter_sc_prepare_atts_before_output', $args, $query_args, $query, 'cars.slider' );

	//if ($args['count'] > $query->post_count) $args['count'] = $query->post_count;
	$posts_count = ($args['count'] > $query->post_count) ? $query->post_count : $args['count'];
	$args['slides_space'] = max(0, (int) $args['slides_space']);
	?><div<?php if (!empty($args['id'])) echo ' id="'.esc_attr($args['id']).'"'; ?> class="sc_cars sc_cars_<?php 
			echo esc_attr($args['type']);
			if (!empty($args['class'])) echo ' '.esc_attr($args['class']); 
			?>"<?php
		if (!empty($args['css'])) echo ' style="'.esc_attr($args['css']).'"';
		?>><?php

		trx_addons_sc_show_titles('sc_cars', $args);
		
		$images = array();
		
		while ( $query->have_posts() ) { $query->the_post();
				$images[] = apply_filters('trx_addons_filter_slider_content', array(
						'url'  => trx_addons_get_attachment_url(get_post_thumbnail_id(get_the_ID()), trx_addons_get_thumb_size('big')),
						'title'=> get_the_title(),
						'cats' => trx_addons_get_post_terms(', ', get_the_ID(), TRX_ADDONS_CPT_CARS_TAXONOMY_TYPE),
						'date' => apply_filters('trx_addons_filter_get_post_date', get_the_date()),
						'link' => get_permalink()
						),
						$args);
		}

		wp_reset_postdata();
	
		?><div class="sc_cars_content sc_item_content"><?php

			trx_addons_show_layout(trx_addons_get_slider_layout(array(
				'mode' => 'custom',
				'noresize' => 'yes',
				'effect' => ! empty( $args['slider_effect'] ) ? $args['slider_effect'] : 'slide',
				'controls' => !trx_addons_is_off($args['slider_controls']) ? 'yes' : 'no',
				'controls_pos' => $args['slider_controls'],
				'pagination' => !trx_addons_is_off($args['slider_pagination']) ? 'yes' : 'no',
				'pagination_pos' => $args['slider_pagination'],
				'slides_space' => $args['slides_space'] > 0 ? $args['slides_space'] : 0,
				'slides_centered' => $args['slides_centered'] > 0 ? 'yes' : 'no',
				'slides_overflow' => $args['slides_overflow'] > 0 ? 'yes' : 'no',
				'mouse_wheel' => $args['slider_mouse_wheel'] > 0 ? 'yes' : 'no',
				'autoplay' => $args['slider_autoplay'] > 0 ? 'yes' : 'no',
				'loop' => $args['slider_loop'] > 0 ? 'yes' : 'no',
				'free_mode' => $args['slider_free_mode'] > 0 ? 'yes' : 'no',
				'id' => !empty($args['id']) ? $args['id'].'_slider' : ''
				), $images));

		?></div><?php
		
		trx_addons_sc_show_links('sc_cars', $args);

	?></div><?php
}
