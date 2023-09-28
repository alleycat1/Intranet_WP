<?php
/**
 * The style "list" of the Blogger
 *
 * @package ThemeREX Addons
 * @since v1.6.54
 */

$args = get_query_var('trx_addons_args_sc_blogger');

$post_format = get_post_format();
$post_format = empty($post_format) ? 'standard' : str_replace('post-format-', '', $post_format);
$post_link = empty($args['no_links']) ? apply_filters( 'trx_addons_filter_get_post_link', get_permalink() ) : '';
$post_title = get_the_title();

$meta_parts = !empty($args['meta_parts'])
				? (is_array($args['meta_parts'])
					? $args['meta_parts']
					: explode(',', $args['meta_parts'])
					)
				: array();
$meta_parts_original = $meta_parts;

$args['image_width'] = max(20, min(80, (int) $args['image_width'])) . '%';
$args['text_width'] = 'calc(100% - ' . $args['image_width'] . ')';

// Prepare parts of template
$templates = trx_addons_components_get_allowed_templates('sc', 'blogger');
if (isset($templates[$args['type']][$args['template_'.$args['type']]])) {
	$template  = $templates[$args['type']][$args['template_'.$args['type']]];
	$template_parts = trx_addons_array_get_values($template['layout']);
	usort($template_parts, function($a, $b) {
		return substr($a, 0, 4) == 'meta' || $a < $b
					? -1
					: (
						substr($b, 0, 4) == 'meta' || $a > $b
							? 1
							: 0
					);
	});
	$template_parts = array_flip($template_parts);
	foreach( $template_parts as $k=>$v ) {
		$template_parts[$k] = '';
		if ( substr( $k, 0, 4 ) == 'meta') {
			$meta_key = substr( $k, 0, 5 ) == 'meta_' ? substr($k, 5) : '';
			if ( !empty($meta_key) && !in_array($meta_key, $meta_parts) ) continue;
			$template_parts[$k] = trx_addons_sc_show_post_meta('sc_blogger', apply_filters('trx_addons_filter_post_meta_args', array(
				'components' => !empty($meta_key) ? $meta_key : join(',', $meta_parts),
				'date_format' => $args['date_format'],
				'theme_specific' => false,
				'class' => "sc_blogger_item_meta post_{$k}",
				'echo' => false
				), 'sc_blogger_'.$args['type'], $args['columns'])
			);
			if (empty($post_link)) {
				$template_parts[$k] = trx_addons_links_to_span($template_parts[$k]);
			}
			if ( !empty($meta_key) ) {
				$meta_parts = array_flip($meta_parts);
				unset($meta_parts[$meta_key]);
				$meta_parts = array_flip($meta_parts);
			}
		} else if ( $k == 'title' ) {
			ob_start();
			the_title( '<h6 class="sc_blogger_item_title entry-title"'
						. ' data-item-number="' . esc_attr($args['item_number']) . '"'
						. '>'
							. (!empty($post_link)
								? sprintf( '<a href="%s" rel="bookmark">', esc_url( $post_link ) )
								: ''),
						(!empty($post_link) ? '</a>' : '') . '</h6>' );
			$template_parts[$k] = ob_get_contents();
			ob_end_clean();
		} else if ( $k == 'price' ) {
			$meta = (array)get_post_meta(get_the_ID(), 'trx_addons_options', true);
			if (!is_array($meta)) $meta = array();
			$meta['price'] = apply_filters( 'trx_addons_filter_custom_meta_value', !empty($meta['price']) ? $meta['price'] : '', 'price' );
			if (!empty($meta['price'])) {
				$template_parts[$k] = '<div class="sc_blogger_item_price sc_item_price">' . wp_kses( $meta['price'], 'trx_addons_kses_content' ) . '</div>';
			}
		} else {
			$val = apply_filters( 'trx_addons_filter_custom_meta_value', '', $k );
			if (!empty($val)) {
				$template_parts[$k] = '<div class="sc_blogger_item_'.esc_attr($k).' sc_item_'.esc_attr($k).'">' . esc_html($val) . '</div>';
			}
		}
	}

	if (empty($args['grid'])) {
		if ($args['slider']) {
			?><div class="slider-slide swiper-slide"<?php
				trx_addons_sc_show_attributes('sc_blogger', $args, 'sc_item_list');
			?>><?php
		} else if ($args['columns'] > 1) {
			?><div class="<?php
					echo esc_attr(trx_addons_get_column_class(1, $args['columns'], !empty($args['columns_tablet']) ? $args['columns_tablet'] : '', !empty($args['columns_mobile']) ? $args['columns_mobile'] : ''));
				?>"<?php
				trx_addons_sc_show_attributes('sc_blogger', $args, 'sc_item_list');
			?>><?php
		}
	}

	?><div data-post-id="<?php the_ID(); ?>" data-item-number="<?php echo esc_attr($args['item_number']); ?>" <?php
		post_class(
			'sc_blogger_item sc_item_container post_container'
			. ' sc_blogger_item_'.esc_attr($args['type'])
			. ' sc_blogger_item_'.esc_attr($args['type']).'_'.esc_attr($args['template_'.$args['type']])
			. ' sc_blogger_item_'.esc_attr($args['item_number'] % 2 == 0 ? 'even' : 'odd')
			. ' sc_blogger_item_align_'.esc_attr($args['text_align'])
			. ' post_format_'.esc_attr($post_format)
			. ( empty($post_link) ? ' no_links' : '' )
			. ( isset($template['layout']['featured']) && has_post_thumbnail() ? ' sc_blogger_item_with_image' : '' )
			. (! empty($args['numbers'] ) ? ' sc_blogger_item_with_numbers' : '')
			. ( (int) $args['on_plate'] > 0 ? ' sc_blogger_item_on_plate' : '' )
			. ' sc_blogger_item_image_position_' . esc_attr($args['image_position'])
		);
		trx_addons_sc_show_attributes('sc_blogger', $args, 'sc_item_wrapper');
	?>><?php

		do_action( 'trx_addons_action_sc_blogger_item_start', $args );

		// Header
		if ( !empty($template['layout']['header']) ) {

			do_action( 'trx_addons_action_sc_blogger_item_before_header', $args );

			?><div class="sc_blogger_item_header entry-header"><?php

				do_action( 'trx_addons_action_sc_blogger_item_header_start', $args );

				foreach ($template['layout']['header'] as $element) {
					if (!empty($template_parts[$element])) {
						trx_addons_show_layout( apply_filters( 'trx_addons_action_sc_blogger_item_element', $template_parts[$element], $element, 'header', $args ) );
					}
				}		

				do_action( 'trx_addons_action_sc_blogger_item_header_end', $args );

			?></div><?php

			do_action( 'trx_addons_action_sc_blogger_item_after_header', $args );

		}

		?><div class="sc_blogger_item_body"><?php

			do_action( 'trx_addons_action_sc_blogger_item_body_start', $args );

			// Featured image
			if ( isset($template['layout']['featured']) ) {

				do_action( 'trx_addons_action_sc_blogger_item_before_featured', $args );

				$featured_args = array(
									'class' => 'sc_item_featured sc_blogger_item_featured'
													. ( in_array($args['image_position'], array('left', 'right', 'alter')) && !empty($template['layout']['content'])
														? ' '.esc_attr(trx_addons_add_inline_css_class('width:'.$args['image_width']))
														: ''
														),
									'data' => array(
										'item-number' => $args['item_number']
									),
									//'hover' => 'zoomin',
									'no_links' => empty($post_link),
									'meta_parts' => $meta_parts_original,
									'thumb_bg' => !in_array($args['image_ratio'], array('', 'none', 'masonry')),
									'thumb_ratio' => $args['image_ratio'],
									'thumb_size' => apply_filters( 'trx_addons_filter_thumb_size', 
														trx_addons_get_thumb_size(
																	! empty( $args['thumb_size'] )
																		? $args['thumb_size']
																		: ( ! in_array( $args['image_ratio'], array('', 'none') )
																			? 'masonry'
																			: 'medium'
																			)
																),
														'blogger-'.$args['type']
													),
									);
				if ( ! empty( $args['hover'] ) ) {
					$featured_args['hover'] = trx_addons_is_off( $args['hover'] ) ? '' : '!' . $args['hover'];
				}
				trx_addons_get_template_part('templates/tpl.featured.php',
											'trx_addons_args_featured',
											apply_filters('trx_addons_filter_args_featured', $featured_args, 'blogger-'.$args['type'])
										);

				do_action( 'trx_addons_action_sc_blogger_item_after_featured', $args );

			}

			// Content
			if ( !empty($template['layout']['content']) ) {

				do_action( 'trx_addons_action_sc_blogger_item_before_content', $args );

				?><div class="sc_blogger_item_content entry-content<?php
					echo isset($template['layout']['featured']) && in_array($args['image_position'], array('left', 'right', 'alter'))
						? ' '.esc_attr(trx_addons_add_inline_css_class('width:'.$args['text_width']))
						: '';
				?>"><?php

					do_action( 'trx_addons_action_sc_blogger_item_content_start', $args );

					foreach ($template['layout']['content'] as $element) {
						if (!empty($template_parts[$element])) {
							trx_addons_show_layout( apply_filters( 'trx_addons_action_sc_blogger_item_element', $template_parts[$element], $element, 'content', $args ) );
						}
					}		

					do_action( 'trx_addons_action_sc_blogger_item_content_end', $args );

				?></div><?php

				do_action( 'trx_addons_action_sc_blogger_item_after_content', $args );

			}

			do_action( 'trx_addons_action_sc_blogger_item_body_end', $args );

		?></div><?php

		// Footer
		if ( !empty($template['layout']['footer']) ) {
			do_action( 'trx_addons_action_sc_blogger_item_before_footer', $args );
			?><div class="sc_blogger_item_footer entry-footer"><?php
				do_action( 'trx_addons_action_sc_blogger_item_footer_start', $args );
				foreach ($template['layout']['footer'] as $element) {
					if (!empty($template_parts[$element])) {
						trx_addons_show_layout( apply_filters( 'trx_addons_action_sc_blogger_item_element', $template_parts[$element], $element, 'footer', $args ) );
					}
				}		
				do_action( 'trx_addons_action_sc_blogger_item_footer_end', $args );
			?></div><?php
			do_action( 'trx_addons_action_sc_blogger_item_after_footer', $args );
		}

		do_action( 'trx_addons_action_sc_blogger_item_end', $args );

	?></div><?php

	if (empty($args['grid']) && ($args['slider'] || $args['columns'] > 1)) {
		?></div><?php
	}
}