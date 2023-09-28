<?php
/**
 * The style "default" of the Squeeze
 *
 * @package ThemeREX Addons
 * @since v2.21.2
 */

$args  = get_query_var('trx_addons_args_sc_squeeze');

// Get data from posts if custom slides are not set
if ( ! is_array( $args['slides'] ) || count( $args['slides'] ) == 0 || empty( $args['slides'][0]['image'] ) ) {
	
	$args['slides'] = array();

	$query_args = array(
		// Attention! Parameter 'suppress_filters' is damage WPML-queries!
		'post_status' => 'publish',
		'ignore_sticky_posts' => true
	);

	if ( empty( $args['ids'] ) ) {
		$query_args['posts_per_page'] = $args['count'];
		$query_args['offset'] = $args['offset'];
	}
	
	$query_args = trx_addons_query_add_sort_order( $query_args, $args['orderby'], $args['order'] );
	
	$query_args = trx_addons_query_add_posts_and_cats( $query_args, $args['ids'], $args['post_type'], $args['cat'], $args['taxonomy'] );
	
	// Exclude posts
	if ( ! empty( $args['posts_exclude'] ) ) {
		$query_args['post__not_in'] = is_array( $args['posts_exclude'] )
										? $args['posts_exclude']
										: explode( ',', str_replace( array( ';', ' ' ), array( ',', '' ), $args['posts_exclude'] ) );
	}
	
	$query_args = apply_filters( 'trx_addons_filter_query_args', $query_args, 'sc_squeeze' );
	
	$query = new WP_Query( $query_args );
	
	if ( $query->post_count > 0 ) {

		$args = apply_filters( 'trx_addons_filter_sc_prepare_atts_before_output', $args, $query_args, $query, 'squeeze.default' );

		while ( $query->have_posts() ) { $query->the_post();
			if ( has_post_thumbnail() ) {
				$terms = trx_addons_get_post_terms( ', ', get_the_ID(), trx_addons_get_post_type_taxonomy() );
				$args['slides'][] = apply_filters( 'trx_addons_filter_sc_squeeze_post_data', array(
					'title' => get_the_title(),
					'subtitle' => trx_addons_links_to_span( $terms ),
					'image' => wp_get_attachment_url( get_post_thumbnail_id() ),
					'link' => get_permalink(),
				) );
			}
		}

		wp_reset_postdata();
	}
}

$total = count( $args['slides'] );

if ( $total > 0 ) {
	?><div <?php if ( ! empty( $args['id'] ) ) echo ' id="' . esc_attr( $args['id'] ) . '"'; ?> 
		class="sc_squeeze sc_squeeze_<?php
			echo esc_attr( $args['type'] );
			if ( ! empty( $args['disable_on_mobile'] ) ) echo ' sc_squeeze_disable_on_mobile';
			if ( ! empty( $args['class'] ) ) echo ' ' . esc_attr( $args['class'] );
			?>"<?php
		if ( ! empty( $args['css'] ) ) echo ' style="' . esc_attr( $args['css'] ) . '"';
		trx_addons_sc_show_attributes('sc_squeeze', $args, 'sc_wrapper');
	?>>
		<div class="sc_squeeze_content sc_item_content <?php
				echo esc_attr( trx_addons_add_inline_css_class( 'height: calc( ' . $total . ' * ( 100vh - var(--fixed-rows-height) ) );' ) );
			?>"<?php
			trx_addons_sc_show_attributes('sc_squeeze', $args, 'sc_content');
		?>>
			<div class="sc_squeeze_viewport"><?php

				// Titles
				?><div class="sc_squeeze_titles"><?php
					foreach ( $args['slides'] as $slide ) {
						if ( ! empty( $slide['title'] ) ) {
							?><div class="sc_squeeze_title"><?php
								if ( ! empty( $slide['link'] ) ) {
									?><a href="<?php echo esc_url( $slide['link'] ); ?>" class="sc_squeeze_title_link"></a><?php
								}
								if ( ! empty( $slide['subtitle'] ) ) {
									?><span class="sc_squeeze_subtitle_text"><?php
										echo wp_kses_post( trx_addons_prepare_macros( $slide['subtitle'] ) );
									?></span><?php	
								}
								if ( ! empty( $slide['title'] ) ) {
									?><h3 class="sc_squeeze_title_text"><?php
										echo wp_kses_post( trx_addons_prepare_macros( $slide['title'] ) );
									?></h3><?php
								}
							?></div><?php
						}
					}
				?></div><?php

				// Links with images
				?><div class="sc_squeeze_wrap"><?php
					foreach ( $args['slides'] as $slide ) {
						if ( ! empty( $slide['image'] ) ) {
							if ( ! empty( $slide['link'] ) ) {
								?><a href="<?php echo esc_url( $slide['link'] ); ?>"<?php
							} else {
								?><span<?php
							}
							?> class="sc_squeeze_item <?php
									echo esc_attr( trx_addons_add_inline_css_class( 'background-image: url(' . esc_url( $slide['image'] ) . ');' ) );
							?>"><?php
							if ( ! empty( $slide['link'] ) ) {
								?></a><?php
							} else {
								?></span><?php
							}
						}
					}
				?></div><?php

				// Bullets (dots)
				if ( ! empty( $args['bullets'] ) ) {
					?><div class="sc_squeeze_bullets sc_squeeze_bullets_position_<?php echo esc_attr( $args['bullets_position'] ); ?>"><?php
						for ( $i = 0; $i < $total; $i++ ) {
							?><span class="sc_squeeze_bullet<?php if ( $i == 0 ) echo ' sc_squeeze_bullet_active'; ?>"></span><?php
						}
					?></div><?php
				}

				// Page numbers
				if ( ! empty( $args['numbers'] ) ) {
					?><div class="sc_squeeze_numbers sc_squeeze_numbers_position_<?php echo esc_attr( $args['numbers_position'] ); ?>"><?php
						?><span class="sc_squeeze_number_active">1</span><?php
						?><span class="sc_squeeze_number_delimiter"></span><?php
						?><span class="sc_squeeze_number_total"><?php echo (int)$total; ?></span><?php
					?></div><?php
				}

				// Progress bar
				if ( ! empty( $args['progress'] ) ) {
					?><div class="sc_squeeze_progress sc_squeeze_progress_position_<?php echo esc_attr( $args['progress_position'] ); ?>">
						<div class="sc_squeeze_progress_value"></div>
					</div><?php
				}

			?></div>
		</div>
	</div><?php
}
