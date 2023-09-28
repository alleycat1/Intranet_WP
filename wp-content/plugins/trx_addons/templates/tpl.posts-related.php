<?php
/**
 * The template to show related posts for the single post
 *
 * @package ThemeREX Addons
 * @since v1.6.25
 */

$trx_addons_args = get_query_var('trx_addons_args_related');

if ($trx_addons_args['posts_per_page'] > 0) {

	$trx_addons_args['columns'] = max(1, $trx_addons_args['columns']);

	$query_args = array(
// Attention! Parameter 'suppress_filters' is damage WPML-queries!
			'ignore_sticky_posts' => true,
			'posts_per_page' => $trx_addons_args['posts_per_page'],
			'orderby' => !empty($trx_addons_args['orderby']) ? $trx_addons_args['orderby'] : 'rand',
			'order' => !empty($trx_addons_args['order']) ? $trx_addons_args['order'] : 'DESC',
			'post_type' => !empty($trx_addons_args['post_type']) ? $trx_addons_args['post_type'] : '',
			'post_status' => 'publish',
			'post__not_in' => array(),
			'category__in' => array()
			);
	
	if (!empty($trx_addons_args['post_type'])) {
		$query_args['post_type'] = $trx_addons_args['post_type'];
	}
	
	if (!empty($trx_addons_args['author'])) {
		if ( (int) $trx_addons_args['author'] > 0 ) {
			$query_args['author'] = $trx_addons_args['author'];
		} else {
			$query_args['author_name'] = $trx_addons_args['author'];			
		}
	}
	
	$query_args['post__not_in'][] = get_the_ID();
		
	if (!empty($trx_addons_args['taxonomies'])) {
		$query_args['tax_query'] = array(
									'relation' => 'AND'
									);
		foreach($trx_addons_args['taxonomies'] as $taxonomy) {
			$terms = get_the_terms(get_the_ID(), $taxonomy);
			if ( !empty( $terms ) ) {
				$terms_list = array();
				foreach( $terms as $term ) {
					$terms_list[] = $term->term_id;
				}
				$query_args['tax_query'][] = array(
											'taxonomy' => $taxonomy,
											'terms' => $terms_list,
											'field' => 'term_id'
											);
			}
		}
	}
	if (!empty($trx_addons_args['meta'])) {
		$query_args['meta_query'] = array(
									'relation' => 'OR'
									);
		foreach($trx_addons_args['meta'] as $meta_key=>$meta_value)
			$query_args['meta_query'][] = array(
											'key' => $meta_key,
											'value' => $meta_value
											);
	}

	$query_args = apply_filters( 'trx_addons_filter_query_args', $query_args, 'related_posts' );

	$query = new WP_Query( apply_filters('trx_addons_filter_query_args_related', $query_args) );

	if ($query->post_count > 0) {

		do_action( 'trx_addons_action_before_related_wrap' );

		?><section class="related_wrap<?php if (!empty($trx_addons_args['class'])) echo ' '.esc_attr($trx_addons_args['class']); ?>">

			<?php do_action( 'trx_addons_action_before_related_wrap_title' ); ?>

			<h3 class="section_title related_wrap_title"><?php
					if (!empty($trx_addons_args['title']))
						echo esc_html($trx_addons_args['title']);
					else
						esc_html_e('You May Also Like', 'trx_addons');
			?></h3><?php

			do_action( 'trx_addons_action_after_related_wrap_title' );

			$trx_addons_args['slider'] = !empty($trx_addons_args['slider']) && $trx_addons_args['columns'] < max(1, min($trx_addons_args['posts_per_page'], $query->post_count));
			if ( $trx_addons_args['slider'] ) {
				$slider_args = $trx_addons_args;
				$slider_args['count'] = max(1, $query->post_count);
				$slider_args['slides_min_width'] = 250;
				$slider_args['slider_effect'] = !empty($trx_addons_args['slider_effect']) ? $trx_addons_args['slider_effect'] : 'slide';
				$slider_args['slides_space'] = !empty($trx_addons_args['slides_space']) ? $trx_addons_args['slides_space'] : 0;
				$slider_args['slider_controls'] = !empty($trx_addons_args['slider_controls']) ? $trx_addons_args['slider_controls'] : 'none';
				$slider_args['slider_pagination'] = !empty($trx_addons_args['slider_pagination']) ? $trx_addons_args['slider_pagination'] : 'bottom';
				$slider_args['slides_centered'] = !empty($trx_addons_args['slides_centered']) ? (int) $trx_addons_args['slides_centered'] : 0;
				$slider_args['slides_overflow'] = !empty($trx_addons_args['slides_overflow']) ? (int) $trx_addons_args['slides_overflow'] : 0;
				$slider_args['slider_mouse_wheel'] = !empty($trx_addons_args['slider_mouse_wheel']) ? (int) $trx_addons_args['slider_mouse_wheel'] : 0;
				$slider_args['slider_autoplay'] = !empty($trx_addons_args['slider_autoplay']) ? (int) $trx_addons_args['slider_autoplay'] : (!isset($trx_addons_args['slider_autoplay']) ? 1 : 0);
				$slider_args['slider_loop'] = !empty($trx_addons_args['slider_loop']) ? (int) $trx_addons_args['slider_loop'] : (!isset($trx_addons_args['slider_loop']) ? 1 : 0);
				$slider_args['slider_free_mode'] = !empty($trx_addons_args['slider_free_mode']) ? (int) $trx_addons_args['slider_free_mode'] : 0;
				$slider_args = apply_filters( 'trx_addons_related_posts_slider_args', $slider_args, $query );
				?><div class="related_wrap_slider"><?php
				trx_addons_sc_show_slider_wrap_start('related_posts_wrap', $slider_args);
			} else {
				?><div class="related_columns related_columns_<?php
									echo esc_attr($trx_addons_args['columns'])
										. ' ' . esc_attr(trx_addons_get_columns_wrap_class())
										. ' columns_padding_bottom'
										. esc_attr( trx_addons_add_columns_in_single_row( $trx_addons_args['columns'], $query ) );
								?>"><?php
			}
				while ( $query->have_posts() ) { $query->the_post();
					trx_addons_get_template_part($trx_addons_args['template'],
												$trx_addons_args['template_args_name'],
												$trx_addons_args,
                                                !empty($trx_addons_args['template_get_file_dir']) ? $trx_addons_args['template_get_file_dir'] : ''
												);
				}
			?></div><?php		// .swiper-wrapper || .columns_wrap
			if ( $trx_addons_args['slider'] ) {
				trx_addons_sc_show_slider_wrap_end('related_posts_wrap', $slider_args);
				?></div><?php
			}
			wp_reset_postdata();
		?></section><?php

		do_action( 'trx_addons_action_after_related_wrap' );

	}
}