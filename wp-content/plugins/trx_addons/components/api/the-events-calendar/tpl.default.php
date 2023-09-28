<?php
/**
 * The style "default" of the Tribe Events
 *
 * @package ThemeREX Addons
 * @since v1.2
 */

$args = get_query_var('trx_addons_args_sc_events');

$query_args = array(
// Attention! Parameter 'suppress_filters' is damage WPML-queries!
	'post_type' => Tribe__Events__Main::POSTTYPE,
	'post_status' => 'publish',
	'ignore_sticky_posts' => true,
	'tribe_suppress_query_filters' => true,   // Disable all filters from Tribe Events plugin
);

if ( empty( $args['ids'] ) || count( explode( ',', $args['ids'] ) ) > $args['count'] ) {
	$query_args['posts_per_page'] = $args['count'];
	$query_args['offset'] = $args['offset'];
	if ( (int) $args['past'] == 1 ) {
		$query_args['meta_query'] = array(
			'relation' => 'OR',
			array(
				'key' => '_EventEndDate',
				'type' => 'DATETIME',
				'value' => date('Y-m-d'),
				'compare' => '<'
			)
		);
	} else {
		$query_args['meta_query'] = array(
			'relation' => 'OR',
			array(
				'key' => '_EventStartDate',
				'type' => 'DATETIME',
				'value' => date('Y-m-d'),
				'compare' => '>='
			),
			array(
				'relation' => 'AND',
				array(
					'key' => '_EventStartDate',
					'type' => 'DATETIME',
					'value' => date('Y-m-d'),
					'compare' => '<'
				),
				array(
					'key' => '_EventEndDate',
					'type' => 'DATETIME',
					'value' => date('Y-m-d'),
					'compare' => '>='
				)
			)
		);		
	}
}

$query_args = trx_addons_query_add_sort_order($query_args, $args['orderby'], $args['order']);
$query_args = trx_addons_query_add_posts_and_cats($query_args, $args['ids'], Tribe__Events__Main::POSTTYPE, $args['cat'], Tribe__Events__Main::TAXONOMY);

$query_args = apply_filters( 'trx_addons_filter_query_args', $query_args, 'sc_events' );

$query = new WP_Query( apply_filters( 'trx_addons_filter_tribe_events_query_args', $query_args ) );

if ($query->post_count > 0) {

	$args = apply_filters( 'trx_addons_filter_sc_prepare_atts_before_output', $args, $query_args, $query, 'tribe_events.default' );

	//if ($args['count'] > $query->post_count) $args['count'] = $query->post_count;
	$posts_count = ($args['count'] > $query->post_count) ? $query->post_count : $args['count'];
	if ($args['columns'] < 1) $args['columns'] = $posts_count;
	$args['columns'] = max(1, min(12, (int) $args['columns']));
	if (!empty($args['columns_tablet'])) $args['columns_tablet'] = max(1, min(12, (int) $args['columns_tablet']));
	if (!empty($args['columns_mobile'])) $args['columns_mobile'] = max(1, min(12, (int) $args['columns_mobile']));
	$args['slider'] = $args['slider'] > 0 && $posts_count > $args['columns'];
	$args['slides_space'] = max(0, (int) $args['slides_space']);
	?><div <?php if (!empty($args['id'])) echo ' id="'.esc_attr($args['id']).'"'; ?>
		class="sc_events sc_events_<?php
			echo esc_attr($args['type']);
			if (!empty($args['class'])) echo ' '.esc_attr($args['class']); 
			?>"<?php
		if (!empty($args['css'])) echo ' style="'.esc_attr($args['css']).'"';
	?>><?php
		
		trx_addons_sc_show_titles('sc_events', $args);
		
		if ($args['slider']) {
			trx_addons_sc_show_slider_wrap_start('sc_events', $args);
		} else if ($args['columns'] > 1) {
			?><div class="sc_events_columns sc_item_columns<?php
				echo ' ' . esc_attr(trx_addons_get_columns_wrap_class())
					. ' columns_padding_bottom'
					. esc_attr( trx_addons_add_columns_in_single_row( $args['columns'], $query ) );
			?>"><?php
		} else {
			?><div class="sc_events_content sc_item_content"><?php
		}	

		while ( $query->have_posts() ) {
			$query->the_post();
			trx_addons_get_template_part(array(
											TRX_ADDONS_PLUGIN_API . 'the-events-calendar/tpl.'.trx_addons_esc($args['type']).'-item.php',
											TRX_ADDONS_PLUGIN_API . 'the-events-calendar/tpl.default-item.php'
											),
											'trx_addons_args_sc_events',
											$args
										);
		}

		wp_reset_postdata();
	
		?></div><?php

		if ($args['slider']) {
			trx_addons_sc_show_slider_wrap_end('sc_events', $args);
		}
		
		trx_addons_sc_show_links('sc_events', $args);

	?></div><?php
}
