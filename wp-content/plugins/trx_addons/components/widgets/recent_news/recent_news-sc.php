<?php
/**
 * Widget: Recent News (Shortcodes)
 *
 * @package ThemeREX Addons
 * @since v1.0
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}



// trx_widget_recent_news
//-------------------------------------------------------------
/*
[trx_widget_recent_news id="unique_id" columns="2" count="5" featured="1" style="news-1" title="Block title" subtitle="xxx" category="id|slug" show_categories="0|1"]
*/
if ( !function_exists( 'trx_addons_sc_recent_news' ) ) {
	function trx_addons_sc_recent_news($atts, $content=null){	
		$atts = trx_addons_sc_prepare_atts('trx_widget_recent_news', $atts, trx_addons_sc_common_atts('id', array(
			// Individual params
			"style" => "news-magazine",
			"count" => 3,
			"featured" => 3,
			"columns" => 3,
			"columns_tablet" => "",
			"columns_mobile" => "",
			"ids" => "",
			"category" => 0,
			"offset" => 0,
			"orderby" => "date",
			"order" => "desc",
			"widget_title" => "",
			"title" => "",
			"subtitle" => "",
			"show_categories" => 0,
			))
		);
		extract($atts);

		set_query_var('trx_addons_inside_sc', true);
		add_filter( 'excerpt_length', 'trx_addons_recent_news_excerpt_length' );
		
		if (!empty($ids)) {
			if ( is_array( $ids ) ) {
				$ids = join(',', $ids);
			}
			$posts = explode(',', $ids);
			$count = count($posts);
		}
		$count = max(1, (int) $count);
		$featured = max(0, min($count, (int) $featured));
		$columns = max(1, min(12, (int) $columns));
		if (in_array($style, array('news-announce', 'news-excerpt'))) $columns = 1;
		if ($featured > 0) $columns = min($featured+1, $columns);		// Columns <= Featured + 1
		$category = max(0, (int) $category);

		// Get categories list
		if ( !empty($title) && trx_addons_is_on($show_categories)) {
			if ( ($cats = get_query_var('categories_'.$category)) == '' ) {
				$cats = get_categories( array(
					'orderby' => 'name',
					'parent' => $category
					)
				);
				set_query_var('categories_'.$category, $cats);
			}
		}

		// Load widget-specific scripts and styles
		trx_addons_widget_recent_news_load_scripts_front( true );

		// Load template
		$output = '';
		
		// If insert with VC as widget
		if (!empty($widget_title)) {
			$widget_args = trx_addons_prepare_widgets_args($id ? $id.'_widget' : 'widget_recent_news', 'widget_recent_news');
			$output .= '<div' . ($id ? ' id="'.esc_attr($id).'"' : '')
							. ' class="widget_area sc_recent_news_wrap' 
								. (trx_addons_exists_vc() ? ' vc_recent_news wpb_content_element' : '') 
						. '">'
							. $widget_args['before_widget']
							. $widget_args['before_title'] .esc_html($widget_title). $widget_args['after_title'];
		}
		
		// Wrapper
		$output .= '<div' . ($id ? ' id="'.esc_attr($id).'"' : '') 
						. ' class="sc_recent_news'
							. ' sc_recent_news_style_'.esc_attr($style)
							. ($featured > 0 ? ' sc_recent_news_with_accented' : ' sc_recent_news_without_accented')
							. (!empty($class) ? ' '.esc_attr($class) : '')
							. '"'
						. ($css!='' ? ' style="'.esc_attr($css).'"' : '') 
					. '>';

		// Header
		if ( !empty($title) ) {	// || !empty($subtitle) || (trx_addons_is_on($show_categories) && !empty($cats)) ) {
			$output	.= '<div class="sc_recent_news_header'.(trx_addons_is_on($show_categories) && !empty($cats) ? ' sc_recent_news_header_split' : '').'">'
							. ( !empty($title) || !empty($subtitle)
								? '<div class="sc_recent_news_header_captions">'
										. (!empty($title) ? '<h3 class="sc_recent_news_title">' . esc_html($title) . '</h3>' : '')
										. (!empty($subtitle) ? '<h6 class="sc_recent_news_subtitle">' . esc_html($subtitle) . '</h6>' : '')
									. '</div>'
								: '');

			// Categories list
			if (trx_addons_is_on($show_categories) && !empty($cats)) {
				$output .= '<div class="sc_recent_news_header_categories">';
				if (is_array($cats) && count($cats) > 0) {
					$output .= '<a href="' . esc_url( $category == 0 
						? ( get_option('show_on_front')=='page' 
							? get_permalink(get_option('page_for_posts')) 
							: home_url('/')
							)
						: get_category_link($category) ) . '" class="sc_recent_news_header_category_item">'.esc_html__('All News', 'trx_addons').'</span>';
					$number = 0;
					$number_max = 3;
					foreach ($cats as $cat) {
						$number++;
						if ($number == $number_max)
							$output .= '<span class="sc_recent_news_header_category_item sc_recent_news_header_category_item_more">'.esc_html__('More', 'trx_addons')
										. '<span class="sc_recent_news_header_more_categories">';
						$output .= '<a href="'.esc_url(get_category_link( $cat->term_id )).'" class="sc_recent_news_header_category_item">'.esc_html($cat->name).'</a>';
					}
					if ($number >= $number_max)
						$output .= '</span></span>';
				}
				$output .= '</div>';
			}
	
			$output .= '</div>';
		}
		
		// Columns
		if ($columns > 1)
			$output .= '<div class="sc_recent_news_columns_wrap '.esc_attr(trx_addons_get_columns_wrap_class()).'">';
	
		$args = array(
			'post_type' => 'post',
			'post_status' => 'publish',
			'posts_per_page' => $count,
			'ignore_sticky_posts' => true,
			'order' => $order=='asc' ? 'asc' : 'desc'
		);
		
		if ($offset > 0 && empty($ids)) {
			$args['offset'] = $offset;
		}
		
		$args = trx_addons_query_add_sort_order($args, $orderby, $order);
		$args = trx_addons_query_add_posts_and_cats($args, $ids, 'post', $category, 'category');

		$args = apply_filters( 'trx_addons_filter_query_args', $args, 'widget_recent_news' );
		
		$query = new WP_Query( $args );
	
		$count = min($count, $query->found_posts);
		$featured = max(0, min($count, (int) $featured));
		$columns = max(1, min(12, (int) $columns));
		if (in_array($style, array('news-announce', 'news-excerpt'))) $columns = 1;
		if ($featured > 0) $columns = min($featured+1, $columns);		// Columns <= Featured + 1
		
		$post_number = 0;
				
		while ( $query->have_posts() ) { $query->the_post();
			$post_number++;
			ob_start();
			trx_addons_get_template_part( array(
											TRX_ADDONS_PLUGIN_WIDGETS . 'recent_news/tpl.'.trx_addons_esc($style).'.php',
											TRX_ADDONS_PLUGIN_WIDGETS . 'recent_news/tpl.excerpt.php'
											),
											'trx_addons_args_recent_news',
											apply_filters( 'trx_addons_filter_sc_recent_news_template_args',
												array(
													'style' => $style,
													'number' => $post_number,
													'count' => $count,
													'columns' => $columns,
													'featured' => $featured
												),
												$atts
											)
										);
			$output .= ob_get_contents();
			ob_end_clean();
		}
		wp_reset_postdata();
	
		if ($columns > 1) $output .= '</div>';

		$output .=  '</div>';

		if (!empty($widget_title)) $output .=  $widget_args['after_widget'] . '</div>';
	
		// Add template specific scripts and styles
		do_action('trx_addons_action_blog_scripts', $style);
	
		remove_filter( 'excerpt_length', 'trx_addons_recent_news_excerpt_length' );
		set_query_var('trx_addons_inside_sc', false);

		return apply_filters('trx_addons_sc_output', $output, 'trx_widget_recent_news', $atts, $content);
	}
}

// Return excerpt length (in words) for the widget Recent News
if ( !function_exists('trx_addons_recent_news_excerpt_length') ) {
	function trx_addons_recent_news_excerpt_length( $length ) {
		return apply_filters( 'trx_addons_filter_sc_recent_news_excerpt_length', 25 );
	}
}


// Add shortcode [trx_widget_recent_news]
if (!function_exists('trx_addons_sc_recent_news_add_shortcode')) {
	function trx_addons_sc_recent_news_add_shortcode() {
		add_shortcode("trx_widget_recent_news", "trx_addons_sc_recent_news");
	}
	add_action('init', 'trx_addons_sc_recent_news_add_shortcode', 20);
}
