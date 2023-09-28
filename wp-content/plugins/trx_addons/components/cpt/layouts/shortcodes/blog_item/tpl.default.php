<?php
/**
 * The template to display block with post data
 *
 * @package ThemeREX Addons
 * @since v1.6.50
 */

$args = get_query_var('trx_addons_args_sc_layouts_blog_item');

$post_built_in = apply_filters( 'trx_addons_filter_post_built_in', trx_addons_exists_gutenberg() ? 'gutenberg' : 'tinymce' );

$need_wrap = $args['position'] != 'static'
				&& in_array( $post_built_in, array( 'gutenberg', 'vc' ) )
				&& !trx_addons_sc_stack_check('show_layout_elementor')
				&& !trx_addons_sc_stack_check('show_layout_vc');

if ( $need_wrap ) {
	$gutenberg_preview = function_exists('trx_addons_gutenberg_is_preview') && trx_addons_gutenberg_is_preview() && !trx_addons_sc_stack_check('trx_sc_blogger');
	?><div<?php
		if ( ! empty( $args['id'] ) ) {
			?> id="<?php echo esc_attr($args['id']); ?>_wrap"<?php
		}
		?>
		class="sc_layouts_blog_item_wrap sc_layouts_blog_item_wrap_<?php
			echo esc_attr($args['type']);
			if (!$gutenberg_preview) {
				echo ' sc_layouts_blog_item_position_' . esc_attr($args['position']);
			}
		?>"<?php
		if ($gutenberg_preview) {
			echo ' data-blog-item-position="' . esc_attr($args['position']) . '"';
		}
	?>><?php
}

if ( in_array($args['type'], array('title', 'excerpt', 'meta', 'custom', 'button')) ) {
	if ( !empty($args['text_color']) ) {
		$args['class'] .= ' ' . trx_addons_add_inline_css_class(
									'color:'.esc_attr($args['text_color']).' !important;',
									in_array($args['type'], array('title', 'meta', 'custom', 'button'))
										? '.post_' . esc_attr($args['type']).' > span,.post_' . esc_attr($args['type']).' > span:before,.post_' . esc_attr($args['type']).' > span:after,.post_' . esc_attr($args['type']).' > span *'
											. ',.post_' . esc_attr($args['type']).' > a,.post_' . esc_attr($args['type']).' > a:before,.post_' . esc_attr($args['type']).' > a:after,.post_' . esc_attr($args['type']).' > a *'
										: '> *'
								);
	}
	if ( !empty($args['text_hover']) ) {
		$args['class'] .= ' ' . trx_addons_add_inline_css_class(
									'color:'.esc_attr($args['text_hover']).' !important;',
									in_array($args['type'], array('title', 'meta', 'custom', 'button'))
										? '.post_' . esc_attr($args['type']).' > span:hover,.post_' . esc_attr($args['type']).' > span:hover:before,.post_' . esc_attr($args['type']).' > span:hover:after,.post_' . esc_attr($args['type']).' > span:hover *'
											. ',.post_' . esc_attr($args['type']).' > a:hover,.post_' . esc_attr($args['type']).' > a:hover:before,.post_' . esc_attr($args['type']).' > a:hover:after,.post_' . esc_attr($args['type']).' > a:hover *'
										: ':hover > *'
								);
	}
}
if ($args['type'] == 'featured' && !empty($args['thumb_mask'])) {
	$args['class'] .= ' sc_layouts_blog_item_featured_mask'
					 . ' ' . trx_addons_add_inline_css_class(
								'opacity:'.esc_attr(min(1, max(0, (float) $args['thumb_mask_opacity']))) . ';'
								. 'background-color:'.(empty($args['thumb_mask']) ? 'transparent' : esc_attr($args['thumb_mask'])) . ';',
							'.post_featured:after');
	$args['class'] .= ' ' . trx_addons_add_inline_css_class(
								'opacity:'.esc_attr(min(1, max(0, (float) $args['thumb_hover_opacity']))) . ';'
								. 'background-color:'.(empty($args['thumb_hover_mask']) ? 'transparent' : esc_attr($args['thumb_hover_mask'])) . ';',
							':hover .post_featured:after');
}

$post_link = empty($args['no_links']) ? get_permalink() : '';

?><div<?php
	if ( ! empty( $args['id'] ) ) {
		?> id="<?php echo esc_attr($args['id']); ?>"<?php
	}
	?>
	class="sc_layouts_blog_item sc_layouts_blog_item_<?php
		echo esc_attr($args['type']);
		//echo ' post_' . esc_attr(str_replace(array('title', 'featured'), array('header', 'featured_wrap'), $args['type']));
		if (!empty($args['class'])) echo ' '.esc_attr($args['class']);
?>"<?php
	if (!wp_is_mobile() && (!trx_addons_is_off($args['animation_in']) || !trx_addons_is_off($args['animation_out']))) {
		echo ' data-hover-animation="animated fast"';
		if (!trx_addons_is_off($args['animation_in'])) {
			echo ' data-animation-in="'.esc_attr($args['animation_in']).'"'
				. ' data-animation-in-delay="'.esc_attr($args['animation_in_delay']).'"';
		}
		if (!trx_addons_is_off($args['animation_out'])) {
			echo ' data-animation-out="'.esc_attr($args['animation_out']).'"'
				. ' data-animation-out-delay="'.esc_attr($args['animation_out_delay']).'"';
		}
	}
	if (!empty($args['css'])) echo ' style="'.esc_attr($args['css']).'"';
	trx_addons_sc_show_attributes('sc_layouts_blog_item', $args, 'sc_item_wrapper');
?>>
	<?php
	$is_preview = ( trx_addons_is_preview() || get_post_type() == '' ) && ! trx_addons_sc_stack_check( 'trx_sc_blogger' );
	if ($args['type'] == 'title') {
		$open = '<' . esc_attr($args['title_tag']) . ' class="post_title entry-title' . ( (int) $args['hide_overflow'] == 1 ? ' hide_overflow' : '' ) . '">'
				. (!empty($post_link) ? sprintf( '<a href="%s" rel="bookmark">', esc_url( $post_link ) ) : '');
		$close = (!empty($post_link) ? '</a>' : '')
				. '</' . esc_attr($args['title_tag']) . '>';
		if ( $is_preview ) {
			trx_addons_show_layout( __('Title of the post', 'trx_addons'), $open, $close );
		} else {
			the_title( $open, $close );
		}

	} else if ($args['type'] == 'excerpt') {
		if ( $is_preview ) {
			trx_addons_show_layout( __('Short content of the post (excerpt). While browsing the tape or blogging Blogger shortcode instead of this text you will see the actual contents of the post.', 'trx_addons') );
		} else {
			trx_addons_show_post_content( $args );
		}

	} else if ($args['type'] == 'content') {
		if ( $is_preview ) {
			trx_addons_show_layout( __('Short content of the post (excerpt). While browsing the tape or blogging Blogger shortcode instead of this text you will see the actual contents of the post.', 'trx_addons') );
		} else {
			trx_addons_show_layout( wpautop( trx_addons_filter_post_content( get_the_content() ) ) );
		}

	} else if ($args['type'] == 'meta') {
		if ( $is_preview ) {
			if (!is_array($args['meta_parts'])) {
				$args['meta_parts'] = explode(',', $args['meta_parts']);
			}
			$args['meta_parts'] = array_map(function($item) {
				return sprintf('<span class="post_meta_item">%s</span>', $item);
			}, $args['meta_parts']);
			$post_meta = implode(' / ', $args['meta_parts']);
			$post_meta = '<div class="post_meta">' . ( !empty($post_meta) && trim(str_replace( array('<span class="post_meta_item">', '</span>'), '', $post_meta)) != '' ? $post_meta : __('Post meta', 'trx_addons') ) . '</div>';
		} else {
			$post_meta = trx_addons_sc_show_post_meta('sc_layouts_meta', apply_filters('trx_addons_filter_post_meta_args', array(
					'components' => is_array($args['meta_parts']) ? implode(',', $args['meta_parts']) : $args['meta_parts'],
					'seo' => false,
					'theme_specific' => false,
					'class' => $args['hide_overflow'] == 1 ? ' hide_overflow' : '',
					'echo' => false
					), 'sc_layouts_blog_item', 1)
				);
			if (empty($post_link)) {
				$post_meta = trx_addons_links_to_span($post_meta);
			}
		}
		trx_addons_show_layout($post_meta);

	} else if ($args['type'] == 'featured') {
		$ratio = '';
		if (!empty($args['thumb_bg'])) {
			if (empty($args['thumb_ratio'])) $args['thumb_ratio'] = '16:9';
			$args['thumb_ratio'] = str_replace(array(';',','), ':', $args['thumb_ratio']);
			$ratio = explode(':', $args['thumb_ratio']);
			if (count($ratio) < 2 || $ratio[0]<=0 || $ratio[1]<=0) $ratio = array(16, 9);
			$ratio = ' ' . trx_addons_add_inline_css_class('padding-top:' . round($ratio[1]/$ratio[0]*100, 2) . '%', ':before');
		}
		trx_addons_get_template_part('templates/tpl.featured.php',
									'trx_addons_args_featured',
									apply_filters('trx_addons_filter_args_featured', array(
														'class' => 'sc_blog_item_featured' . esc_attr($ratio),
														'hover' => !empty($args['thumb_mask']) ? '' : 'zoomin',
														'no_links' => empty($post_link),
														'show_no_image' => true, //$is_preview,
														'thumb_bg' => !$is_preview && !empty($args['thumb_bg']),
														'thumb_ratio' => $args['thumb_ratio'],
														'thumb_size' => apply_filters('trx_addons_filter_thumb_size', $args['thumb_size'], 'blog-item-default')
														), 'blog-item-default')
								);

	} else if ($args['type'] == 'custom' && !empty($args['custom_meta_key'])) {
		if ( $is_preview ) {
			$meta = $args['custom_meta_key'];
		} else {
			if ( ($meta = apply_filters('trx_addons_filter_custom_meta_value', '', $args['custom_meta_key'])) == '') {
				$meta = get_post_meta(get_the_ID(), $args['custom_meta_key'], true);
			}
		}
		if (!empty($meta)) {
			?><div class="post_custom post_meta post_meta_custom_key_<?php echo esc_attr($args['custom_meta_key']); ?>"><?php
				trx_addons_show_layout($meta, '<span class="post_meta_item">', '</span>');
			?></div><?php
		}

	} else if ($args['type'] == 'button') {
		if ( ($output = apply_filters('trx_addons_filter_blog_item_button', '', $args)) == '') {
			$link = '';
			if ($args['button_link'] == 'product') {
				$meta = (array)get_post_meta(get_the_ID(), 'trx_addons_options', true);
				if (is_array($meta) && !empty($meta['product'])) {
					$link = get_permalink($meta['product']);
				}
			} else if ($args['button_link'] == 'post') {
				$link = get_permalink();
			} else if ( $is_preview ) {
				$link = '#';
			}
			if ( !empty($link) ) {
				$output = trx_addons_sc_button(array(
					'type' => $args['button_type'],
					'title' => !empty($args['button_text']) ? $args['button_text'] : __('Read more', 'trx_addons'),
					'link' => $link 
				));
			}
		}
		if ( !empty($output) ) {
			trx_addons_show_layout(
				trim($output),
				'<div class="' . esc_attr(apply_filters('trx_addons_filter_blog_item_button_class', 'post_button', $args)) . '">',
				'</div>'
			);
		}

	}
?></div><?php
if ( $need_wrap ) {
	?></div><?php
}
// Don't close PHP tag because this items can be used as columns ( inline-block elements )
