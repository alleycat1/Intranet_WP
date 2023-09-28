<?php
/**
 * The style "default" of the Matches
 *
 * @package ThemeREX Addons
 * @since v1.6.17
 */

$args = get_query_var('trx_addons_args_sc_matches');

$round = $args['round'];
if ($round == 'last' || $round == 'next') {
	$rounds = trx_addons_get_list_posts(false, array(
													'post_type' => TRX_ADDONS_CPT_ROUNDS_PT,
													'post_parent' => $args['competition'],
													'posts_per_page' => 1,
													'meta_key' => 'trx_addons_round_date',
													'meta_value' => date('Y-m-d H:i'),
													'meta_compare' => $round == 'last' ? '<=' : '>',
													'orderby' => 'meta_value',
													'order' => $round == 'last' ? 'DESC' : 'ASC',
													'not_selected' => false
													));
	if (count($rounds)>0) {
		foreach ($rounds as $k => $v) {
			$round = $k;
			break;
		}
	}
}

if ((int) $round > 0) {
	$query_args = array(
	// Attention! Parameter 'suppress_filters' is damage WPML-queries!
		'post_type' => TRX_ADDONS_CPT_MATCHES_PT,
		'post_status' => 'publish',
		'ignore_sticky_posts' => true,
	);
	if ( empty( $args['ids'] ) || count( explode( ',', $args['ids'] ) ) > $args['count'] ) {
		$query_args['post_parent'] = $round;
		$query_args['posts_per_page'] = $args['count'] > 0 ? $args['count'] : -1;
		$query_args['offset'] = $args['offset'];
	}

	$query_args = trx_addons_query_add_sort_order($query_args, str_replace('post_date', 'match_date', $args['orderby']), $args['order']);
	$query_args = trx_addons_query_add_posts_and_cats($query_args, $args['ids']);

	$query_args = apply_filters( 'trx_addons_filter_query_args', $query_args, 'sc_matches' );

	$query = new WP_Query( $query_args );
	
	if ($query->post_count > 0) {

		$args = apply_filters( 'trx_addons_filter_sc_prepare_atts_before_output', $args, $query_args, $query, 'matches.default' );

		// Prepare matches
		$matches = array(
			'main' => array(),
			'other' => array()
		);
		while ( $query->have_posts() ) { $query->the_post();
			$meta = (array)get_post_meta(get_the_ID(), 'trx_addons_options', true);
			$matches[!empty($args['main_matches']) && !empty($meta['main_match']) && !empty($meta['player1']) ? 'main' : 'other'][] = array(
				'id' => get_the_ID(),
				'title' => get_the_title(),
				'link' => get_permalink(),
				'meta' => $meta
			);
		}
		wp_reset_postdata();
		// Show matches
		$main_matches = count($matches['main']);
		if ($main_matches == 0) $args['position'] = 'top';
		$other_matches = count($matches['other']);
		$args['slider'] = $args['slider'] > 0 && $main_matches > 1;
		?><div <?php if (!empty($args['id'])) echo ' id="'.esc_attr($args['id']).'"'; ?>
			class="sc_matches sc_matches_<?php
				echo esc_attr($args['type']);
				echo ' sc_matches_main_'.esc_attr($args['position']);
				if (!empty($args['class'])) echo ' '.esc_attr($args['class']); 
				?>"<?php
			if (!empty($args['css'])) echo ' style="'.esc_attr($args['css']).'"';
			?>><?php
			
			trx_addons_sc_show_titles('sc_matches', $args);

			// Additional container for 'left' or 'right' main position
			if ($args['position'] != 'top') {
				?><div class="sc_matches_content sc_item_content">
					<div class="sc_matches_side"><?php
			}
			
			// Main matches
			if ($main_matches > 0) {
				?><div class="sc_matches_main<?php
					if ($args['position'] == 'top') echo " sc_matches_content sc_item_content";
				?>"><?php

				if ($args['slider']) {
					$args['slider_pagination'] = 'bottom';
					$args['slider_controls'] = 'side';
					trx_addons_sc_show_slider_wrap_start('sc_matches', $args);
				}	
				
				foreach ($matches['main'] as $match) {
					if ($args['slider']) {
						?><div class="slider-slide swiper-slide"><?php
					}
					trx_addons_get_template_part(TRX_ADDONS_PLUGIN_CPT . 'sport/tpl.sc_matches.default-main.php',
												'trx_addons_args_sc_matches_item',
												$match
												);
					if ($args['slider']) {
						?></div><?php
					}
				}

				if ($args['slider']) {
					?></div><?php		// .swiper-wrapper
					trx_addons_sc_show_slider_wrap_end('sc_matches', $args);
				}

				?></div><?php

			}

			// Additional container for 'left' or 'right' main position
			if ($args['position'] != 'top') {

				trx_addons_sc_show_links('sc_matches', $args);

				?></div><div class="sc_matches_side"><?php
			}
			
			// Other Matches
			if ($other_matches > 0) {
				?><div class="sc_matches_other<?php
					if ($main_matches == 0 && $args['position'] == 'top') echo " sc_matches_content sc_item_content";
					?>"><?php

				foreach ($matches['other'] as $match) {
					$match['position'] = $args['position'];
					trx_addons_get_template_part(TRX_ADDONS_PLUGIN_CPT . 'sport/tpl.sc_matches.default-other.php',
													'trx_addons_args_sc_matches_item',
													$match
												);
				}
			
				?></div><?php
			}
			
			// Additional container for 'left' or 'right' main position
			if ($args['position'] != 'top') {
				?></div></div><?php
			} else
				trx_addons_sc_show_links('sc_matches', $args);

		?></div><?php
	}
}
