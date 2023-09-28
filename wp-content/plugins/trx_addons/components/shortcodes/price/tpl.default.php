<?php
/**
 * The style "default" of the Price block
 *
 * @package ThemeREX Addons
 * @since v1.2
 */

$args = get_query_var('trx_addons_args_sc_price');

$icon_present = '';

?><div <?php if (!empty($args['id'])) echo ' id="'.esc_attr($args['id']).'"'; ?> 
	class="sc_price sc_price_<?php
		echo esc_attr($args['type']);
		if (!empty($args['class'])) echo ' '.esc_attr($args['class']); 
		?>"<?php
	if (!empty($args['css'])) echo ' style="'.esc_attr($args['css']).'"';
	trx_addons_sc_show_attributes('sc_price', $args, 'sc_wrapper');
?>><?php

	trx_addons_sc_show_titles('sc_price', $args);

	if ($args['slider']) {
		$args['slides_min_width'] = 250;
		trx_addons_sc_show_slider_wrap_start('sc_price', $args);
	} else if ($args['columns'] > 1) {
		?><div class="sc_price_columns_wrap sc_item_columns <?php
			echo esc_attr(trx_addons_get_columns_wrap_class())
				. ' columns_padding_bottom'
				. esc_attr( trx_addons_add_columns_in_single_row( $args['columns'], $args['prices'] ) );
		?>"<?php trx_addons_sc_show_attributes('sc_price', $args, 'sc_items_wrapper'); ?>><?php
	} else {
		?><div class="sc_price_content sc_item_content"<?php trx_addons_sc_show_attributes('sc_price', $args, 'sc_items_wrapper'); ?>><?php
	}	

	foreach ($args['prices'] as $item) {
		if (empty($item['color'])) $item['color'] = '';
		if (empty($item['position'])) $item['position'] = 'mc';
		if ($args['slider']) {
			?><div class="slider-slide swiper-slide"><?php
		} else if ($args['columns'] > 1) {
			?><div class="<?php echo esc_attr(trx_addons_get_column_class(1, $args['columns'], !empty($args['columns_tablet']) ? $args['columns_tablet'] : '', !empty($args['columns_mobile']) ? $args['columns_mobile'] : '')); ?>"><?php
		}
		if (!empty($item['bg_image'])) {
			$item['bg_image'] = trx_addons_get_attachment_url($item['bg_image'], apply_filters('trx_addons_filter_thumb_size', trx_addons_get_thumb_size('huge'), 'price-default-bg'));
		}
		?><div class="<?php
			echo apply_filters(
					'trx_addons_filter_sc_item_classes',
					'sc_price_item sc_price_item_' . esc_attr( $args['type'] )
						. ( ! empty( $item['bg_image'] ) || ! empty( $item['bg_color'] ) ? ' with_image' : '' )
						. ( ! empty( $item['bg_color'] ) ? ' with_bg_color' : '' ),
					'sc_price',
					$item
				);
			?>"<?php
			if (!empty($item['bg_image']) || !empty($item['bg_color'])) {
				echo ' style="'
							. (!empty($item['bg_color']) ? 'background-color:'.esc_attr($item['bg_color']).';' : '')
							. (!empty($item['bg_image']) ? 'background-image:url('.esc_url($item['bg_image']).');' : '')
							. '"'; 
			}
			trx_addons_sc_show_attributes('sc_price', $args, 'sc_item_wrapper');
		?>>
			<?php
			// Label
			if (!empty($item['label'])) {
				?><div class="sc_price_item_label"><?php echo esc_html($item['label']); ?></div><?php
			}
			// Mask for bg image
			if (!empty($item['bg_image']) || !empty($item['bg_color'])) {
				?><div class="sc_price_item_inner"><?php
			}
			if (!empty($item['image'])) {
				$item['image'] = trx_addons_get_attachment_url($item['image'], apply_filters('trx_addons_filter_thumb_size', trx_addons_get_thumb_size('medium'), 'price-default'));
				if (!empty($item['image'])) {
					$attr = trx_addons_getimagesize($item['image']);
					?><div class="sc_price_item_image"><img src="<?php echo esc_url($item['image']); ?>" alt="<?php esc_attr_e('Icon', 'trx_addons'); ?>"<?php echo (!empty($attr[3]) ? ' '.trim($attr[3]) : ''); ?>></div><?php
				}
			} else {
				if (empty($item['icon_type'])) $item['icon_type'] = '';
				$icon = !empty($item['icon_type']) && !empty($item['icon_' . $item['icon_type']]) && $item['icon_' . $item['icon_type']] != 'empty' 
							? $item['icon_' . $item['icon_type']] 
							: '';
				if (!empty($icon)) {
					if (strpos($icon_present, $item['icon_type'])===false)
						$icon_present .= (!empty($icon_present) ? ',' : '') . $item['icon_type'];
				} else {
					if (!empty($item['icon']) && strtolower($item['icon'])!='none') $icon = $item['icon'];
				}
			
				if (!empty($icon)) {
					$img = $svg = '';
					if (trx_addons_is_url($icon)) {
						if (strpos($icon, '.svg') !== false) {
							$svg = $icon;
							$item['icon_type'] = 'svg';
						} else {
							$img = $icon;
							$item['icon_type'] = 'images';
						}
						$icon = basename($icon);
					}
					?><div class="sc_price_item_icon sc_price_item_icon_type_<?php echo esc_attr($item['icon_type']); ?> <?php echo esc_attr($icon); ?>"
						<?php if (!empty($item['color'])) echo ' style="color: '.esc_attr($item['color']).'"'; ?>
						><?php
						if (!empty($svg)) {
							?><span class="sc_icon_type_<?php echo esc_attr($item['icon_type']); ?> <?php echo esc_attr($icon); ?>"><?php
								trx_addons_show_layout(trx_addons_get_svg_from_file($svg));
							?></span><?php
						} else if (!empty($img)) {
							$attr = trx_addons_getimagesize($img);
							?><img class="sc_icon_as_image" src="<?php echo esc_url($img); ?>" alt="<?php esc_attr_e('Icon', 'trx_addons'); ?>"<?php echo (!empty($attr[3]) ? ' '.trim($attr[3]) : ''); ?>><?php
						} else {
							?><span class="sc_icon_type_<?php echo esc_attr($item['icon_type']); ?> <?php echo esc_attr($icon); ?>"
								<?php if (!empty($item['color'])) echo ' style="color: '.esc_attr($item['color']).'"'; ?>
							></span><?php
						}
					?></div><?php
				 }
			}
			?><div class="sc_price_item_info"><?php
				if (!empty($item['subtitle'])) {
					$item['subtitle'] = explode('|', $item['subtitle']);
					?><h6 class="sc_price_item_subtitle"><?php
						foreach ($item['subtitle'] as $str) {
							?><span><?php echo esc_html($str); ?></span><?php
						}
					?></h6><?php
				}
				if (!empty($item['title'])) {
					$item['title'] = explode('|', $item['title']);
					?><h3 class="sc_price_item_title"><?php
						foreach ($item['title'] as $str) {
							?><span><?php echo esc_html($str); ?></span><?php
						}
					?></h3><?php
				}
				if (!empty($item['description'])) {
					$item['description'] = explode('|', str_replace("\n", '|', $item['description']));
					?><div class="sc_price_item_description"><?php
						foreach ($item['description'] as $str) {
							?><span><?php echo trx_addons_parse_codes( $str ); ?></span><?php
						}
					?></div><?php
				}
				if (!empty($item['price'])) {
					$parts = explode('.', trx_addons_parse_codes($item['price']));
					?><div class="sc_price_item_price"><?php
						if (!empty($item['before_price'])) {
							?><span class="sc_price_item_price_before"><?php echo trx_addons_parse_codes( $item['before_price'] ); ?></span><?php
						}
						?><span class="sc_price_item_price_value"><?php echo $parts[0]; ?></span><?php
						if (count($parts) > 1 && $parts[1]!='') {
							?><span class="sc_price_item_price_decimals"><?php echo $parts[1]; ?></span><?php
						}
						if (!empty($item['after_price'])) {
							?><span class="sc_price_item_price_after"><?php echo trx_addons_parse_codes( $item['after_price'] ); ?></span><?php
						}
					?></div><?php
				}
				if (!empty($item['details'])) {
					?><div class="sc_price_item_details"><?php trx_addons_show_layout($item['details']); ?></div><?php
				}
				if (!empty($item['link']) && !empty($item['link_text'])) {
					?><a href="<?php echo esc_url($item['link']); ?>" class="<?php
							echo esc_attr(apply_filters('trx_addons_filter_sc_item_link_classes', 'sc_price_item_link sc_button', 'sc_price', $args, $item));
					?>"<?php
						if (!empty($item['new_window']) || !empty($item['link_extra']['is_external'])) echo ' target="_blank"';
						if (!empty($item['nofollow']) || !empty($item['link_extra']['nofollow'])) echo ' rel="nofollow"';
					?>><?php
						echo esc_html($item['link_text']);
					?></a><?php
				}
			?></div><?php
			if (!empty($item['bg_image']) || !empty($item['bg_color'])) {
				?></div><?php
			}
			// Link over whole block - if 'link' is not empty and 'link_text' is empty
			if (!empty($item['link']) && empty($item['link_text'])) {
				?><a href="<?php echo esc_url($item['link']); ?>" class="<?php
						echo esc_attr(apply_filters('trx_addons_filter_sc_item_link_classes', 'sc_price_item_link sc_price_item_link_over', 'sc_price', $args, $item));
				?>"<?php
					if (!empty($item['new_window']) || !empty($args['link_extra']['is_external'])) echo ' target="_blank"';
					if (!empty($item['nofollow']) || !empty($item['link_extra']['nofollow'])) echo ' rel="nofollow"';
				?>></a><?php
			}
		?></div><?php

		if ($args['slider'] || $args['columns'] > 1) {
			?></div><?php
		}
	}

	?></div><?php

	if ($args['slider']) {
		trx_addons_sc_show_slider_wrap_end('sc_price', $args);
	}

	trx_addons_sc_show_links('sc_price', $args);

?></div><?php

trx_addons_load_icons($icon_present);
?>