<?php
/**
 * The style "event" of the Action
 *
 * @package ThemeREX Addons
 * @since v1.2.2
 */

$args = get_query_var('trx_addons_args_sc_action');

$icon_present = '';

?><div <?php if (!empty($args['id'])) echo ' id="'.esc_attr($args['id']).'"'; ?> 
	class="sc_action sc_action_event<?php
		if (!empty($args['class'])) echo ' '.esc_attr($args['class']); ?>"<?php
	if (!empty($args['css'])) echo ' style="'.esc_attr($args['css']).'"';
	trx_addons_sc_show_attributes('sc_action', $args, 'sc_wrapper');
	?>><?php

	trx_addons_sc_show_titles('sc_action', $args);

	if ($args['slider']) {
		$args['slides_min_width'] = 250;
		trx_addons_sc_show_slider_wrap_start('sc_action', $args);
	} else if ($args['columns'] > 1) {
		?><div class="sc_action_columns_wrap sc_item_columns <?php
			echo esc_attr(trx_addons_get_columns_wrap_class())
				. ' columns_padding_bottom'
				. esc_attr( trx_addons_add_columns_in_single_row( $args['columns'], $args['actions'] ) );
		?>"<?php trx_addons_sc_show_attributes('sc_action', $args, 'sc_items_wrapper'); ?>><?php
	} else {
		?><div class="sc_action_content sc_item_content"<?php trx_addons_sc_show_attributes('sc_action', $args, 'sc_items_wrapper'); ?>><?php
	}	

	foreach ($args['actions'] as $item) {
		$item['color'] = !empty($item['color']) ? $item['color'] : '';
		if ($args['slider']) {
			?><div class="slider-slide swiper-slide"><?php
		} else if ($args['columns'] > 1) {
			?><div class="<?php echo esc_attr(trx_addons_get_column_class(1, $args['columns'], !empty($args['columns_tablet']) ? $args['columns_tablet'] : '', !empty($args['columns_mobile']) ? $args['columns_mobile'] : '')); ?>"><?php
		}
		if (!empty($item['bg_image'])) {
			$item['bg_image'] = trx_addons_get_attachment_url($item['bg_image'], apply_filters('trx_addons_filter_thumb_size', trx_addons_get_thumb_size('huge'), 'action-event-bg'));
		}
		?><div class="<?php
			echo apply_filters(
					'trx_addons_filter_sc_item_classes',
					'sc_action_item sc_action_item_event'
						. ( ! empty( $item['bg_image'] ) || ! empty( $item['bg_color'] ) ? ' with_image' : '' )
						. ( ! empty( $item['bg_color'] ) ? ' with_bg_color' : '' )
						. ( (int)$args['full_height'] == 1 ? ' trx_addons_stretch_height' : '' )
						. ( ! empty( $args['min_height'] ) && (int)$args['min_height'] > 0 && (int)$args['full_height'] == 0 ? ' sc_action_fixed_height ' . trx_addons_add_inline_css_class( trx_addons_get_css_dimensions_from_values( array( 'min-height' => $args['min_height'] ) ) . ';' ) : '' )
						. ( ! empty( $item['position'] ) ? ' sc_action_item_' . esc_attr( $item['position'] ) : '' ),
					'sc_action',
					$item
					);
			?>"<?php
			trx_addons_sc_show_attributes('sc_action', $args, 'sc_item_wrapper');
			if (!empty($item['bg_image']) || !empty($item['bg_color'])) 
				echo ' style="'
							. (!empty($item['bg_color']) ? 'background-color:'.esc_attr($item['bg_color']).';' : '')
							. (!empty($item['bg_image']) ? 'background-image:url('.esc_url($item['bg_image']).');' : '')
							. '"'; 
		?>>
			<?php
			if (!empty($item['bg_image']) || !empty($item['bg_color'])) {
				?><div class="sc_action_item_inner"><?php
			}
			if (!empty($item['image'])) {
				$item['image'] = trx_addons_get_attachment_url($item['image'], apply_filters('trx_addons_filter_thumb_size', trx_addons_get_thumb_size('medium'), 'action-event'));
				if (!empty($item['image'])) {
					$attr = trx_addons_getimagesize($item['image']);
					?><div class="sc_action_item_image"><img src="<?php echo esc_url($item['image']); ?>" alt="<?php esc_attr_e("Action's image", 'trx_addons'); ?>"<?php echo (!empty($attr[3]) ? ' '.trim($attr[3]) : ''); ?>></div><?php
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
					?><div class="sc_action_item_icon sc_action_item_icon_type_<?php echo esc_attr($item['icon_type']); ?> <?php echo esc_attr($icon); ?>"
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
			if (!empty($item['subtitle'])) {
				$item['subtitle'] = explode('|', $item['subtitle']);
				?><h6 class="sc_action_item_subtitle"<?php if (!empty($item['color_2'])) echo ' style="color: '.esc_attr($item['color_2']).'"'; ?>><?php
					foreach ($item['subtitle'] as $str) {
						?><span><?php echo esc_html($str); ?></span><?php
					}
				?></h6><?php
			}
			if (!empty($item['title'])) {
				$item['title'] = explode('|', $item['title']);
				?><h3 class="sc_action_item_title"<?php if (!empty($item['color'])) echo ' style="color: '.esc_attr($item['color']).'"'; ?>><?php
					foreach ($item['title'] as $str) {
						?><span><?php echo esc_html($str); ?></span><?php
					}
				?></h3><?php
			}
			if (!empty($item['date'])) {
				?><div class="sc_action_item_date"<?php
					if (!empty($item['color']) || !empty($item['color_4'])) {
						echo ' style="' . ( !empty($item['color']) ? 'color: ' . esc_attr($item['color']) . ';' : '' )
										. ( !empty($item['color_4']) ? 'border-color: '.esc_attr($item['color_4']).';' : '' )
										. '"';
					}
				?>><?php echo esc_html($item['date']); ?></div><?php
			}
			if (!empty($item['description'])) {
				$item['description'] = explode('|', str_replace("\n", '|', $item['description']));
				?><div class="sc_action_item_description"<?php if (!empty($item['color_3'])) echo ' style="color: '.esc_attr($item['color_3']).'"'; ?>><?php
					foreach ($item['description'] as $str) {
						?><span><?php trx_addons_show_layout($str); ?></span><?php
					}
				?></div><?php
			}
			if (!empty($item['link']) && !empty($item['link_text'])) {
				?><a href="<?php echo esc_url($item['link']); ?>" class="<?php
					echo esc_attr(apply_filters('trx_addons_filter_sc_item_link_classes', 'sc_action_item_link sc_button sc_button_size_small', 'sc_action', $args, $item));
				?>"<?php
					if (!empty($item['new_window']) || !empty($item['link_extra']['is_external'])) echo ' target="_blank"';
					if (!empty($item['nofollow']) || !empty($item['link_extra']['nofollow'])) echo ' rel="nofollow"';
				?>><?php 
					echo esc_html($item['link_text']); 
				?></a><?php
			}
			if (!empty($item['info'])) {
				$item['info'] = explode('|', $item['info']);
				?><div class="sc_action_item_info"<?php
					if (!empty($item['color']) || !empty($item['color_4'])) {
						echo ' style="' . ( !empty($item['color']) ? 'color: '.esc_attr($item['color']).';' : '' )
										. ( !empty($item['color_4']) ? 'border-color: '.esc_attr($item['color_4']).';' : '' )
										. '"';
					}
				?>><?php
					foreach ($item['info'] as $str) {
						?><span><?php trx_addons_show_layout($str); ?></span><?php
					}
				?></div><?php
			}
			if (!empty($item['bg_image']) || !empty($item['bg_color'])) {
				?></div><?php
				if (!empty($item['link']) && empty($item['link_text'])) {
					?><a href="<?php echo esc_url($item['link']); ?>" class="<?php
						echo esc_attr(apply_filters('trx_addons_filter_sc_item_link_classes', 'sc_action_item_link sc_action_item_link_over', 'sc_action', $args, $item));
					?>"<?php
						if (!empty($item['new_window']) || !empty($item['link_extra']['is_external'])) echo ' target="_blank"';
						if (!empty($item['nofollow']) || !empty($item['link_extra']['nofollow'])) echo ' rel="nofollow"';
					?>></a><?php
				}
			}
		?></div><?php

		if ($args['slider'] || $args['columns'] > 1) {
			?></div><?php
		}
	}

	?></div><?php

	if ($args['slider']) {
		trx_addons_sc_show_slider_wrap_end('sc_action', $args);
	}

	trx_addons_sc_show_links('sc_action', $args);


?></div><?php

trx_addons_load_icons($icon_present);
