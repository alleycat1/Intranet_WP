<?php
/**
 * The template's part to display the property's Googple Place info
 *
 * @package ThemeREX Addons
 * @since v2.6.2
 */

$trx_addons_args = get_query_var('trx_addons_args_properties_place');
$trx_addons_meta = $trx_addons_args['meta'];
$info = $trx_addons_args['info'];

// Place details
?><div class="properties_page_google_reviews_info"><?php
	?><h5 class="properties_page_google_reviews_title"><?php
		if ( ! empty( $info['icon'] ) ) {
			?><span class="properties_page_google_reviews_icon"><img src="<?php echo esc_url( $info['icon'] ); ?>"></span><?php
		}
		?><span class="properties_page_google_reviews_name_text"><?php
			echo esc_html( $info['name'] );
		?></span><?php
	?></h5><?php
	?><ul class="properties_page_google_reviews_info_list"><?php
		if ( ! empty( $info['address'] ) ) {
			?><li>
				<span class="properties_page_google_reviews_info_list_caption"><?php
					esc_html_e( 'Post address:', 'trx_addons' );
				?></span>
				<span class="properties_page_google_reviews_info_list_data"><?php
					echo esc_html( $info['address'] );
				?></span>
			</li><?php
		}
		if ( ! empty( $info['phone'] ) ) {
			?><li>
				<span class="properties_page_google_reviews_info_list_caption"><?php
					esc_html_e( 'Phone:', 'trx_addons' );
				?></span>
				<a href="tel:<?php echo esc_attr( trx_addons_get_phone_link( $info['phone'] ) ); ?>" class="properties_page_google_reviews_info_list_data"><?php
					echo esc_html( $info['phone'] );
				?></a>
			</li><?php
		}
		if ( ! empty( $info['website'] ) ) {
			?><li>
				<span class="properties_page_google_reviews_info_list_caption"><?php
					esc_html_e( 'Web-site:', 'trx_addons' );
				?></span>
				<a href="<?php echo esc_url( $info['website'] ); ?>" target="_blank" class="properties_page_google_reviews_info_list_data"><?php
					echo esc_html( $info['website'] );
				?></a>
			</li><?php
		}
	?></ul><?php
	// Opening hours
	?><h5 class="properties_page_google_reviews_title"><?php esc_html_e( 'Opening hours:', 'trx_addons' ); ?></h5><?php
	?><ul class="properties_page_google_reviews_opened"><?php
		if ( is_array( $info['opening_hours'] ) && is_array( $info['opening_hours']['weekday_text'] ) ) {
			foreach( $info['opening_hours']['weekday_text'] as $w ) {
				?><li><?php echo esc_html( $w ); ?></li><?php
			}
		}
	?></ul><?php
	// Links to Google maps
	$buttons = apply_filters( 'trx_addons_filter_google_reviews_buttons', array(
		array(
			"type" => "default",
			"size" => "normal",
			"title" => esc_html__( 'View on Google Map', 'trx_addons' ),
			"link" => trx_addons_google_places_get_map_url( $trx_addons_meta['google_place_id'] )
		),
	), 'map_link' );
	trx_addons_show_layout( trx_addons_sc_button( array(
								'buttons' => $buttons,
								'align' => 'none'
							) ),
							'<div class="properties_page_google_reviews_button_view_map">',
							'</div>'
						);
?></div><?php

// Place rating
?><div class="properties_page_google_reviews_rating"><?php
	?><h5 class="properties_page_google_reviews_title"><?php esc_html_e( 'Google Rating:', 'trx_addons' ); ?></h5><?php
	trx_addons_reviews_show_round( 'p0', array(
		'mark' => $info['rating'],
		'mark_max' => 5,
		'mark_decimals' => 1,
		'size' => 1
	) );
	?><p class="properties_page_google_reviews_total"><?php
		echo esc_html( sprintf( __( 'by %d visitor rates' ), $info['ratings_total'] ) );
	?></p><?php
	// Links to Google maps
	$buttons = apply_filters( 'trx_addons_filter_google_reviews_buttons', array(
		array(
			"type" => "default",
			"size" => "normal",
			"title" => esc_html__( 'Rate this place', 'trx_addons' ),
			"link" => trx_addons_google_places_get_add_review_url( $trx_addons_meta['google_place_id'] )
		)
	), 'rate_link' );
	trx_addons_show_layout( trx_addons_sc_button( array(
								'buttons' => $buttons,
								'align' => 'center'
							) ),
							'<div class="properties_page_google_reviews_button_add_review">',
							'</div>'
						);
?></div><?php

// Place reviews
?><div class="properties_page_google_reviews_list"><?php
	if ( is_array( $info['reviews'] ) ) {
		$args = array(
					'slider' => 1,
					'columns' => 1,
					'count' => count( $info['reviews'] ),
					'slides_space' => 0,
					'slides_min_width' => 200,
					'slider_controls' => 'none',
					'slider_pagination' => 'bottom',
					'slider_pagination_type' => 'bullets',	//fraction
				);
		?><h5 class="properties_page_google_reviews_title"><?php esc_html_e( 'Related reviews:', 'trx_addons' ); ?></h5><?php
		if ( $args['slider'] ) {
			trx_addons_sc_show_slider_wrap_start( 'cpt_properties_google_reviews', $args );
		} else {
			?><ul class="properties_page_google_reviews_authors"><?php
		}
		// Sort reviews by time
		usort( $info['reviews'], function( $a, $b ) {
			return $a['time'] < $b['time'] ? 1 : ( $a['time'] > $b['time'] ? -1 : 0 );
		} );
		// Display review list
		foreach ( $info['reviews'] as $review ) {
			if ( $args['slider'] ) {
				?><div class="slider-slide swiper-slide"><?php
			} else {
				?><li><?php
			}
			?><div class="properties_page_google_reviews_author_data"><?php
				// Author photo
				if ( ! empty( $review['profile_photo_url'] ) ) {
					?><img src="<?php echo esc_url( $review['profile_photo_url'] ); ?>" class="properties_page_google_reviews_author_photo"><?php
				}
				// Author name
				if ( ! empty( $review['author_name'] ) ) {
					?><h6 class="properties_page_google_reviews_author_name"><?php echo esc_html( $review['author_name'] ); ?></h6><?php
				}
				// Stars rating
				if ( ! empty( $review['rating'] ) ) {
					trx_addons_reviews_show_stars( 'p0', array(
						'mark' => $review['rating'],
						'mark_max' => 5,
						'mark_decimals' => 0,
					) );
				}
				// Time
				if ( ! empty( $review['relative_time_description'] ) ) {
					?><p class="properties_page_google_reviews_time"><?php
						echo esc_html( $review['relative_time_description'] );
					?></p><?php
				}
				// Review
				if ( ! empty( $review['text'] ) ) {
					?><p class="properties_page_google_reviews_text"><?php
						echo esc_html( trx_addons_strwords(
										$review['text'],
										apply_filters( 'trx_addons_filter_google_reviews_length', 25 )
									) );
					?></p><?php
				}
			?></div><?php
			if ( $args['slider'] ) {
				?></div><?php
			} else {
				?></li><?php
			}
		}
		if ( $args['slider'] ) {
			?></div><?php 	// .slides
			trx_addons_sc_show_slider_wrap_end('cpt_properties_google_reviews', $args);
		} else {
			?></ul><?php
		}
		// Links to Google maps
		$buttons = apply_filters( 'trx_addons_filter_google_reviews_buttons', array(
			array(
				"type" => "default",
				"size" => "normal",
				"title" => esc_html__( 'More reviews', 'trx_addons' ),
				"link" => trx_addons_google_places_get_reviews_url( $trx_addons_meta['google_place_id'] )
			),
		), 'reviews_link' );
		trx_addons_show_layout( trx_addons_sc_button( array(
									'buttons' => $buttons,
									'align' => 'center'
								) ),
								'<div class="properties_page_google_reviews_button_view_all">',
								'</div>'
							);
	}
?></div>
