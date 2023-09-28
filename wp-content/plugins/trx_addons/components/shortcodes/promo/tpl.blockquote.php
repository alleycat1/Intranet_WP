<?php
/**
 * The style "default" of the Promo block
 *
 * @package ThemeREX Addons
 * @since v1.2
 */

$args = get_query_var('trx_addons_args_sc_promo');

$css_image = (!empty($args['image']) && !is_array($args['image']) ? 'background-image:url(' . esc_url($args['image']) . ');' : '')
			 . (!empty($args['image_width']) ? 'width:'.trim($args['image_width']).';' : '')
			 . (!empty($args['image_position']) ? $args['image_position'].': 0;' : '');

$css_text = 'width: '.esc_attr($args['text_width']).';'
			. (!empty($args['image']) ? 'float: '.($args['image_position']=='left' ? 'right' : 'left').';' : '')
			. (!empty($args['text_margins']) ? ' margin:'.esc_attr($args['text_margins']).';' : '');

?><div <?php if (!empty($args['id'])) echo ' id="'.esc_attr($args['id']).'"'; ?> 
	class="sc_promo sc_promo_blockquote<?php
		if (!empty($args['class'])) echo ' '.esc_attr($args['class']); 
		if (!empty($args['size'])) echo ' sc_promo_size_'.esc_attr($args['size']);
		if (empty($args['text_paddings'])) echo ' sc_promo_no_paddings';
		if (!empty($args['image']) && empty($args['image_cover'])) echo ' sc_promo_image_fit';
		if (!empty($args['image']) && !empty($args['image_position'])) echo ' sc_promo_image_position_'.esc_attr($args['image_position']);
		if (empty($args['image'])) echo ' sc_promo_no_image';
		?>"
		<?php
		if (!empty($args['css'])) echo ' style="'.esc_attr($args['css']).'"';
		trx_addons_sc_show_attributes('sc_promo', $args, 'sc_wrapper');
		?>
><?php
	
	// Image
	if (!empty($args['image'])) {
		$video = array('','');
		if (!empty($args['video_url']) || !empty($args['video_embed'])) {
			$video = explode(
						'<!-- .sc_layouts_popup -->',
						trx_addons_get_video_layout( apply_filters( 'trx_addons_filter_get_video_layout_args', array(
							'link' => isset($args['video_url']) ? $args['video_url'] : '',
							'embed' => isset($args['video_embed']) ? $args['video_embed'] : '',
							'cover' => $args['image'],
							'show_cover' => false,
							'popup' => !empty($args['video_in_popup'])
						), 'promo.blockquote' ) )
					);
		} else if (is_array($args['image'])) {
			$images = array();
			foreach ( $args['image'] as $v ) {
				$images[] = array('url' => trx_addons_get_attachment_url($v, 'full'));
			}
		}
		?><div class="sc_promo_image" style="<?php echo esc_attr($css_image); ?>"<?php trx_addons_sc_show_attributes('sc_promo', $args, 'sc_image_wrapper'); ?>><?php
			if (!empty($video[0])) {
				trx_addons_show_layout($video[0]);
			} else if (is_array($args['image'])) {
				trx_addons_show_layout(trx_addons_get_slider_layout(apply_filters('trx_addons_sc_promo_slider', array(
					'mode' => 'custom',
					'noresize' => 'yes',
					'controls' => 'yes',
					'controls_pos' => 'side',
					'pagination' => 'yes',
					'pagination_pos' => 'bottom',
					'slides_space' => 0
					)), $images));
			}
		?></div><?php
		if (!empty($video[1])) trx_addons_show_layout($video[1]);
	}
	if (!empty($args['title']) || !empty($args['subtitle']) || !empty($args['description']) || !empty($args['content']) 
		|| (!empty($args['link']) && !empty($args['link_text'])) || !empty($args['link_image'])) {
		?><blockquote class="sc_promo_text trx_addons_blockquote_style_1<?php
			echo !empty($args['text_centered']) ? ' sc_promo_text_centered' : '';
			?> sc_align_<?php echo esc_attr($args['text_align']); ?>" style="<?php echo esc_attr($css_text); ?>"<?php
			trx_addons_sc_show_attributes('sc_promo', $args, 'sc_text_wrapper');
		?>><?php
			if (!empty($args['description'])) {
				?><p><?php echo do_shortcode(trx_addons_prepare_macros($args['description'])); ?></p><?php
			}
			if (!empty($args['content']) && 'tiny' != $args['size']) {
				echo do_shortcode($args['content']);
			}
			if (!empty($args['link']) && !empty($args['link_text'])) {
				?><p><a href="<?php echo esc_url($args['link']); ?>"<?php
					if (!empty($args['new_window']) || !empty($args['link_extra']['is_external'])) echo ' target="_blank"';
					if (!empty($args['nofollow']) || !empty($args['link_extra']['nofollow'])) echo ' rel="nofollow"';
				?>><?php echo esc_html($args['link_text']); ?></a></p><?php
			}
		?></blockquote><?php
	}
	if ( 'tiny' == $args['size'] ) {
		if (!empty($args['link'])) {
			?><a href="<?php echo esc_url($args['link']); ?>"
				class="<?php echo esc_attr(apply_filters('trx_addons_filter_sc_item_link_classes', 'sc_promo_link', 'sc_promo', $args));
			?>"<?php
				if (!empty($args['link_extra']['is_external'])) echo ' target="_blank"';
			?>></a><?php
		}
	}
?></div>