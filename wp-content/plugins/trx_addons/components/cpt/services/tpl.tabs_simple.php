<?php
/**
 * The style "tabs_simple" of the Services
 *
 * @package ThemeREX Addons
 * @since v1.6.14
 */

$args = get_query_var('trx_addons_args_sc_services');

$svg_present = false;

$featured_position = !empty($args['featured_position']) ? $args['featured_position'] : 'top';

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

	$args = apply_filters( 'trx_addons_filter_sc_prepare_atts_before_output', $args, $query_args, $query, 'services.tabs_simple' );

	//if ($args['count'] > $query->post_count) $args['count'] = $query->post_count;
	$posts_count = ($args['count'] > $query->post_count) ? $query->post_count : $args['count'];
	?><div <?php if (!empty($args['id'])) echo ' id="'.esc_attr($args['id']).'"'; ?>
			class="sc_services sc_services_<?php 
				echo esc_attr($args['type']);
				if (!empty($args['class'])) echo ' '.esc_attr($args['class']); 
	?>"><?php

		trx_addons_sc_show_titles('sc_services', $args);
		
		?><div class="sc_services_content sc_item_content sc_item_posts_container<?php if (!empty($args['no_links'])) echo ' no_links'; ?>"><?php

			// Prepare tab's titles and contents
			$tabs_list = array();
			$trx_addons_number = $args['offset'] + ( $args['page'] > 1 ? $args['count'] * ( $args['page'] - 1 ) : 0 );
			while ( $query->have_posts() ) { $query->the_post();
				$trx_addons_number++;
				
				$meta = (array)get_post_meta(get_the_ID(), 'trx_addons_options', true);
				$link = empty($args['no_links'])
							? (!empty($meta['link']) ? $meta['link'] : get_permalink())
							: '';
				// Prepare tab's title block
				$tabs_item = '<div data-post-id="<?php the_ID(); ?>" class="sc_services_item sc_services_tabs_simple_item sc_services_tabs_list_item' 
									. ($trx_addons_number-1 == $args['offset'] ? ' sc_services_tabs_list_item_active' : '') 
									. (empty($args['featured']) || $args['featured']=='image' 
											? ' with_image' 
											: ($args['featured']=='icon' 
												? ' with_icon' 
												: ($args['featured']=='pictogram' 
													? ' with_pictogram' 
													: ' with_number'))
										)
									. ' sc_services_item_featured_'.esc_attr($featured_position)
								. '">';
				// Featured image or icon
				if ( has_post_thumbnail() && (empty($args['featured']) || $args['featured']=='image')) {
					ob_start();
					trx_addons_get_template_part('templates/tpl.featured.php',
													'trx_addons_args_featured',
													apply_filters('trx_addons_filter_args_featured', array(
																	'class' => 'sc_services_item_thumb',
																	'thumb_size' => ! empty( $args['thumb_size'] )
																						? $args['thumb_size']
																						: apply_filters('trx_addons_filter_thumb_size', trx_addons_get_thumb_size($args['featured_position']=='top' ? 'medium' : 'tiny'), 'services-tabs-simple')
																	),
																'services-tabs-simple'
																)
												);
					$tabs_item .= ob_get_contents();
					ob_end_clean();
				} else if ($args['featured']=='icon' && !empty($meta['icon'])) {
					$svg = $img = '';
					if (trx_addons_is_url($meta['icon'])) {
						if (strpos($meta['icon'], '.svg') !== false) {
							$svg = $meta['icon'];
							$svg_present = !empty($args['icons_animation']);
						} else {
							$img = $meta['icon'];
						}
						$meta['icon'] = basename($meta['icon']);
					} else if (!empty($args['icons_animation']) && $args['icons_animation'] > 0 && ($svg = trx_addons_get_file_dir('css/icons.svg/'.trx_addons_clear_icon_name($meta['icon']).'.svg')) != '') {
						$svg_present = true;
					}
					$tabs_item .= '<span'
									. ( $svg_present && !empty($args['id'])
										? ' id="' . esc_attr( $args['id'].'_'.trim($meta['icon']).'_'.trim($trx_addons_number) ) . '"'
										: ''
										)
									. ' class="sc_services_item_icon'
											. ( $svg_present ? ' sc_icon_animation' : '' )
											. ( ! empty($svg) 
												? ' sc_icon_type_svg'
												: ( ! empty($img) 
													? ' sc_icon_type_images'
													: ' sc_icon_type_icons ' . esc_attr($meta['icon'])
													)
												)
												. '"'
											. ( ! empty($meta['icon_color'])
												? ' style="color:'.esc_attr($meta['icon_color']).'"'
												: ''
												)
									. '>'
										. ( ! empty($svg) 
											? trx_addons_get_svg_from_file($svg) 
											: ( ! empty($img)
												? '<img class="sc_icon_as_image" src="'.esc_url($img).'" alt="' . esc_attr__('Icon', 'trx_addons') . '">'
												: '')
											)
									. '</span>';
				} else if ($args['featured']=='pictogram' && !empty($meta['image'])) {
					$attr = trx_addons_getimagesize($meta['image']);
					$tabs_item .= '<span class="sc_services_item_pictogram"><img src="' . esc_url($meta['image']) . '" alt="' . esc_attr__('Icon', 'trx_addons') . '"'
								. (!empty($attr[3]) ? ' '.trim($attr[3]) : '') . '></span>';
				} else if ($args['featured']=='number') {
					$tabs_item .= sprintf('<span class="sc_services_item_number">%02d</span>', $trx_addons_number);
				}
				// Post title and subtitle
				$tabs_item .= '<div class="sc_services_item_info">'
								. '<h6 class="sc_services_item_title">' . get_the_title() . '</h6>'
								. '<div class="sc_services_item_subtitle">' 
									. trx_addons_get_post_terms(', ', get_the_ID(), trx_addons_get_post_type_taxonomy(), false) 
								. '</div>'
							. '</div>';
				$tabs_item .= '</div>';
				// Save to the list
				$tabs_list[] = array(
									'title' => $tabs_item,
									'content' => '<div class="sc_services_tabs_content_item'
													. ($trx_addons_number-1 == $args['offset'] ? ' sc_services_tabs_content_item_active' : '') 
													. '">'
														. '<div class="sc_services_tabs_content_item_text">'
															. get_the_excerpt()
														. '</div>'
														. (!empty($link) && !empty($args['more_text'])
															? '<div class="sc_services_item_button sc_item_button">'
																. '<a href="'.esc_url($link).'"'.(!empty($meta['link']) && trx_addons_is_external_url($meta['link']) ? ' target="_blank"' : '').' class="'.esc_attr(apply_filters('trx_addons_filter_sc_item_link_classes', 'sc_button', 'sc_services', $args)).'">'.esc_html($args['more_text']).'</a>'
																. '</div>'
															: '')
													. '</div>'
									);
			}
			wp_reset_postdata();
		
			if (count($tabs_list) > 0) {
				// Display titles
				?><div class="sc_services_tabs_list"><?php
					foreach($tabs_list as $item) trx_addons_show_layout($item['title']);
				?></div><?php
				// Display contents
				?><div class="sc_services_tabs_content"><?php
					foreach($tabs_list as $item) trx_addons_show_layout($item['content']);
				?></div><?php
			}
		?></div><?php

		trx_addons_sc_show_pagination('sc_services', $args, $query);

		trx_addons_sc_show_links('sc_services', $args);

	?></div><?php

	if ( $svg_present ) {
		wp_enqueue_script( 'vivus', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_SHORTCODES . 'icons/vivus.js'), array('jquery'), null, true );
		wp_enqueue_script( 'trx_addons-sc_icons', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_SHORTCODES . 'icons/icons.js'), array('jquery'), null, true );
	}
}
