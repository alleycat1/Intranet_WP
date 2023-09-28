<?php
/**
 * The style "default" of the Services
 *
 * @package ThemeREX Addons
 * @since v1.4
 */

$args = get_query_var('trx_addons_args_sc_services');
$query_args = array(
// Attention! Parameter 'suppress_filters' is damage WPML-queries!
	'post_status' => 'publish',
	'ignore_sticky_posts' => true
);
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
}

$query_args = trx_addons_query_add_sort_order($query_args, $args['orderby'], $args['order']);

$query_args = trx_addons_query_add_posts_and_cats($query_args, $args['ids'], $args['post_type'], $args['cat'], $args['taxonomy']);

// Exclude posts
if ( ! empty( $args['posts_exclude'] ) ) {
	$query_args['post__not_in'] = is_array( $args['posts_exclude'] )
									? $args['posts_exclude']
									: explode( ',', str_replace( array( ';', ' ' ), array( ',', '' ), $args['posts_exclude'] ) );
}

$query_args = apply_filters( 'trx_addons_filter_query_args', $query_args, 'sc_services' );

$query = new WP_Query( $query_args );

if ($query->post_count > 0) {

	$args = apply_filters( 'trx_addons_filter_sc_prepare_atts_before_output', $args, $query_args, $query, 'services.default' );

	//if ($args['count'] > $query->post_count) $args['count'] = $query->post_count;
	$posts_count = ($args['count'] > $query->post_count) ? $query->post_count : $args['count'];
	if ($args['columns'] < 1) $args['columns'] = $posts_count;
	$args['columns'] = max(1, min(12, (int) $args['columns']));
	if (!empty($args['columns_tablet'])) $args['columns_tablet'] = max(1, min(12, (int) $args['columns_tablet']));
	if (!empty($args['columns_mobile'])) $args['columns_mobile'] = max(1, min(12, (int) $args['columns_mobile']));
	$args['slider'] = $args['slider'] > 0 && $posts_count > $args['columns'];
	$args['slides_space'] = max(0, (int) $args['slides_space']);
	?><div<?php if (!empty($args['id'])) echo ' id="'.esc_attr($args['id']).'"'; ?> class="sc_services sc_services_<?php 
			echo esc_attr($args['type']);
			echo ' sc_services_featured_'.esc_attr($args['featured_position']);
			if (!empty($args['class'])) echo ' '.esc_attr($args['class']); 
			?>"<?php
		if (!empty($args['css'])) echo ' style="'.esc_attr($args['css']).'"';
		if ($args['type']=='timeline' && $args['featured_position']=='bottom') echo ' data-equal-height=".sc_services_item"';
		?>><?php

		trx_addons_sc_show_titles('sc_services', $args);
		
		if ($args['slider']) {
			$args['slides_min_width'] = $args['type']=='iconed' ? 250 : 200;
			trx_addons_sc_show_slider_wrap_start('sc_services', $args);
		} else if ($args['columns'] > 1) {
			?><div class="sc_services_columns_wrap sc_item_columns sc_item_posts_container sc_item_columns_<?php
							echo esc_attr($args['columns']);
							echo ' ' . esc_attr(trx_addons_get_columns_wrap_class());
							if ( $args['type'] != 'list' ) {
								echo ( empty( $args['no_margin'] ) ? ' columns_padding_bottom' : '' )
									. esc_attr( trx_addons_add_columns_in_single_row( $args['columns'], $query ) );
							}
							if ( ! empty( $args['no_margin'] ) ) echo ' no_margin';
						?>"><?php
		} else {
			?><div class="sc_services_content sc_item_content sc_item_posts_container sc_item_columns_1"><?php
		}	

		set_query_var('trx_addons_args_sc_services', $args);

		$trx_addons_number = $args['offset'] + ( $args['page'] > 1 ? $args['count'] * ( $args['page'] - 1 ) : 0 );

		$add_html = '';

		while ( $query->have_posts() ) { $query->the_post();
			$trx_addons_number++;
			
			trx_addons_get_template_part(array(
											TRX_ADDONS_PLUGIN_CPT . 'services/tpl.'.trx_addons_esc($args['type']).'-item.php',
                                            TRX_ADDONS_PLUGIN_CPT . 'services/tpl.default-item.php'
                                            ),
                                            'trx_addons_args_item_number',
                                            $trx_addons_number
                                        );
			if ( $args['type'] == 'panel' ) {
				$thumb_id = get_post_thumbnail_id( get_the_ID() );
				$thumb_class = 'without_image';
				if ( ! empty( $thumb_id )) {
					$image = wp_get_attachment_image_src( $thumb_id, 'full' );
					$thumb_class = empty( $image[0] ) ? '' : ( 'with_image ' . trx_addons_add_inline_css_class( 'background-image: url(' . esc_url( $image[0] ) . ');' ) );
				}
				if ( empty( $add_html ) ) {
					$thumb_class .= ' sc_panel_thumb_active';
				}
				$add_html .= '<div class="sc_panel_thumb ' . esc_attr( $thumb_class ) . '" data-thumb-number="' . esc_attr($trx_addons_number) . '"></div>';
			}
		}

		wp_reset_postdata();

		trx_addons_show_layout( $add_html, '<div class="sc_services_panel_thumbs">', '</div>' );

		?></div><?php		// .swiper-wrapper || .sc_services_columns || .sc_services_content

		if ($args['slider']) {
			trx_addons_sc_show_slider_wrap_end('sc_services', $args);
		}

		trx_addons_sc_show_pagination('sc_services', $args, $query);
		
		trx_addons_sc_show_links('sc_services', $args);

	?></div><?php
}
