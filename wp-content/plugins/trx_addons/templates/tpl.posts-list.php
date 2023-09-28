<?php
/**
 * The template to display the posts list
 *
 * Used for widgets Recent Posts, Popular Posts.
 *
 * @package ThemeREX Addons
 * @since v1.0
 */

$trx_addons_post_id    = get_the_ID();
$trx_addons_post_date  = apply_filters('trx_addons_filter_get_post_date', get_the_date());
$trx_addons_post_title = get_the_title();
$trx_addons_post_link  = get_permalink();
$trx_addons_post_author_id   = get_the_author_meta('ID');
$trx_addons_post_author_name = get_the_author_meta('display_name');
$trx_addons_post_author_url  = get_author_posts_url($trx_addons_post_author_id, '');

$trx_addons_args = get_query_var('trx_addons_args_widgets_posts');

if ( isset( $trx_addons_args['show_date'] ) && ( $trx_addons_args['show_date'] === true || $trx_addons_args['show_date'] === 'true' ) ) $trx_addons_args['show_date'] = 1;
if ( isset( $trx_addons_args['show_image'] ) && ( $trx_addons_args['show_image'] === true || $trx_addons_args['show_image'] === 'true' ) ) $trx_addons_args['show_image'] = 1;
if ( isset( $trx_addons_args['show_author'] ) && ( $trx_addons_args['show_author'] === true || $trx_addons_args['show_author'] === 'true' ) ) $trx_addons_args['show_author'] = 1;
if ( isset( $trx_addons_args['show_counters'] ) && ( $trx_addons_args['show_counters'] === true || $trx_addons_args['show_counters'] === 'true' ) ) $trx_addons_args['show_counters'] = 1;
if ( isset( $trx_addons_args['show_categories'] ) && ( $trx_addons_args['show_categories'] === true || $trx_addons_args['show_categories'] === 'true' ) ) $trx_addons_args['show_categories'] = 1;
if ( isset( $trx_addons_args['show_rating'] ) && ( $trx_addons_args['show_rating'] === true || $trx_addons_args['show_rating'] === 'true' ) ) $trx_addons_args['show_rating'] = 1;

$trx_addons_show_date = isset($trx_addons_args['show_date']) ? (int) $trx_addons_args['show_date'] : 1;
$trx_addons_show_image = isset($trx_addons_args['show_image']) ? (int) $trx_addons_args['show_image'] : 1;
$trx_addons_show_author = isset($trx_addons_args['show_author']) ? (int) $trx_addons_args['show_author'] : 1;
$trx_addons_show_counters = isset($trx_addons_args['show_counters']) ? (int) $trx_addons_args['show_counters'] : 1;
$trx_addons_show_categories = isset($trx_addons_args['show_categories']) ? (int) $trx_addons_args['show_categories'] : 1;
$trx_addons_show_rating = isset($trx_addons_args['show_rating']) ? (int) $trx_addons_args['show_rating'] : 0;

$trx_addons_output = get_query_var('trx_addons_output_widgets_posts');

$trx_addons_post_counters_output = '';
if ( $trx_addons_show_counters && !empty($trx_addons_args['components']) ) {
	$trx_addons_post_counters_output = '<span class="post_info_item post_info_counters">'
											. trx_addons_sc_show_post_meta('posts_list', apply_filters('trx_addons_filter_post_meta_args', array(
												'tag' => 'span',
												'components' => $trx_addons_args['components'],
												'theme_specific' => false,
												'rating_type' => isset($trx_addons_args['mark_type']) ? $trx_addons_args['mark_type'] : 'post',
												'seo' => false,
												'echo' => false
												), 'posts-list', 1)
											)
										. '</span>';
}

$trx_addons_post_categories_output = $trx_addons_show_categories ? trx_addons_get_post_categories() : '';

$trx_addons_output .= '<article class="post_item with_thumb">';

if ($trx_addons_show_image) {
	$trx_addons_post_thumb = get_the_post_thumbnail($trx_addons_post_id, 
													apply_filters('trx_addons_filter_posts_list_thumb_size', 
																	trx_addons_get_thumb_size('tiny'), 
																	$trx_addons_args), 
													array(
														'alt' => get_the_title()
														));
	if ($trx_addons_post_thumb)
		$trx_addons_output .= '<div class="post_thumb">' 
									. ($trx_addons_post_link ? '<a href="' . esc_url($trx_addons_post_link) . '">' : '') 
									. ($trx_addons_post_thumb) 
									. ($trx_addons_post_link ? '</a>' : '')
								. '</div>';
}

$trx_addons_output .= '<div class="post_content">'
			. ( !empty($trx_addons_post_categories_output)
					? '<div class="post_categories">'
						. trim($trx_addons_post_categories_output)
						. trim($trx_addons_post_counters_output)
						. '</div>' 
					: '')
			. '<h6 class="post_title">' . ($trx_addons_post_link ? '<a href="' . esc_url($trx_addons_post_link) . '">' : '') . ($trx_addons_post_title) . ($trx_addons_post_link ? '</a>' : '') . '</h6>'
			. ($trx_addons_show_rating && function_exists('trx_addons_reviews_show_stars')
					? trx_addons_reviews_show_stars( $trx_addons_post_id, array( 'type' => isset($trx_addons_args['mark_type']) ? $trx_addons_args['mark_type'] : 'post', 'echo' => false ) )
					: ''
					)
			. '<div class="post_info">'
				. ($trx_addons_show_date 
					? '<span class="post_info_item post_info_posted">'
						. ($trx_addons_post_link ? '<a href="' . esc_url($trx_addons_post_link) . '" class="post_info_date">' : '') 
						. ($trx_addons_post_date) 
						. ($trx_addons_post_link ? '</a>' : '')
						. '</span>'
					: '')
				. ($trx_addons_show_author 
					? '<span class="post_info_item post_info_posted_by">' 
						. esc_html__('by', 'trx_addons') . ' ' 
						. ($trx_addons_post_link ? '<a href="' . esc_url($trx_addons_post_author_url) . '" class="post_info_author">' : '') 
						. ($trx_addons_post_author_name) 
						. ($trx_addons_post_link ? '</a>' : '') 
						. '</span>'
					: '')
				. (!$trx_addons_show_categories && $trx_addons_post_counters_output
					? $trx_addons_post_counters_output
					: '')
			. '</div>'
		. '</div>'
	. '</article>';
set_query_var('trx_addons_output_widgets_posts', $trx_addons_output);
