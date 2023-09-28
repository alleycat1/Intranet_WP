<?php
/**
 * The "Announce" template to show post's content
 *
 * Used in the widget Recent News.
 *
 * @package ThemeREX Addons
 * @since v1.0
 */
 
$widget_args = get_query_var('trx_addons_args_recent_news');
$style = $widget_args['style'];
$number = min(8, $widget_args['number']);
$count = min(8, $widget_args['count']);
$post_format = get_post_format();
$post_format = empty($post_format) ? 'standard' : str_replace('post-format-', '', $post_format);
$animation = apply_filters('trx_addons_blog_animation', '');
$grid = array(
	array('full'),
	array('big', 'big'),
	array('big', 'medium', 'medium'),
	array('big', 'medium', 'small', 'small'),
	array('big', 'small', 'small', 'small', 'small'),
	array('medium', 'medium', 'small', 'small', 'small', 'small'),
	array('medium', 'small', 'small', 'small', 'small', 'small', 'small'),
	array('small', 'small', 'small', 'small', 'small', 'small', 'small', 'small')
);
$thumb_size = $grid[$count-$number >= 8 ? 8 : ($count-1)%8][($number-1)%8];
?><article 
	<?php post_class( 'post_item post_layout_'.esc_attr($style)
					.' post_format_'.esc_attr($post_format)
					.' post_size_'.esc_attr($thumb_size)
					); ?>
	<?php echo (!empty($animation) ? ' data-post-animation="'.esc_attr($animation).'"' : ''); ?>
	>

	<?php
	if ( is_sticky() && is_home() && !is_paged() ) {
		?><span class="post_label label_sticky"></span><?php
	}
	
	trx_addons_get_template_part('templates/tpl.featured.php',
								'trx_addons_args_featured',
								apply_filters( 'trx_addons_filter_args_featured', array(
										'post_info' => apply_filters('trx_addons_filter_post_info',
														'<div class="post_info">'
															. '<span class="post_categories">'.trx_addons_get_post_categories().'</span>'
															. '<h5 class="post_title entry-title"><a href="'.esc_url(get_permalink()).'" rel="bookmark">'.get_the_title().'</a></h5>'
															. ( in_array( get_post_type(), array( 'post', 'attachment' ) ) 
																	? trx_addons_sc_show_post_meta('recent_news_announce', apply_filters('trx_addons_filter_post_meta_args', array(
																			'components' => 'author,date',
																			'seo' => false,
																			'echo' => false,
																			'theme_specific' => false,
																			), 'recent_news_announce', 1)
																		)
																	: '')
															. '</div>',
														'recent_news-announce', $widget_args ),
										'thumb_bg' => true,
										'thumb_size' => apply_filters('trx_addons_filter_thumb_size', trx_addons_get_thumb_size('big'), 'recent_news-announces', $widget_args )
										),
										'recent_news-announce', $widget_args )
								);
	?>
</article>