<?php
/**
 * The style "default" of the Widget "Custom Links"
 *
 * @package ThemeREX Addons
 * @since v1.6.46
 */

$args = get_query_var('trx_addons_args_widget_custom_links');
extract($args);
		
// Before widget (defined by themes)
trx_addons_show_layout($before_widget);
			
// Widget title if one was input (before and after defined by themes)
trx_addons_show_layout($title, $before_title, $after_title);
	
// Widget body
$icon_present = '';
$svg_present = false;
if (is_array($links) && count($links) > 0) {
	?><ul class="custom_links_list"><?php
		foreach ($links as $item) {
			if ( empty($item['title']) && empty($item['caption']) && empty($item['url']) ) {
				continue;
			}
			$target = !empty($item['new_window']) || !empty($item['url_extra']['is_external']) ? ' target="_blank"' : '';
			?><li class="custom_links_list_item<?php
				if (!empty($item['label_on_hover']) && (int)$item['label_on_hover'] > 0) {
					echo ' custom_links_list_item_label_hover';
				}
				if ((!empty($item['icon']) && !trx_addons_is_off($item['icon']))
					||
					(!empty($item['image']) && !trx_addons_is_off($item['image']))
				) {
					echo ' with_icon';
				}
			?>"><?php
				// Open link
				if (empty($item['url'])) {
					?><span class="custom_links_list_item_link"<?php
						if (!empty($item['color'])) echo ' style="color:'.esc_attr($item['color']).' !important;"';
					?>><?php
				} else {
					?><a class="custom_links_list_item_link" href="<?php echo esc_url($item['url']); ?>"<?php
						trx_addons_show_layout($target);
						if (!empty($item['nofollow']) || !empty($item['url_extra']['nofollow'])) echo ' rel="nofollow"';
						if (!empty($item['color'])) echo ' style="color:'.esc_attr($item['color']).' !important;"';
					?>><?php
				}
				// Image or Icon
				if (!empty($item['image']) && !trx_addons_is_off($item['image'])) {
					$item['image'] = trx_addons_get_attachment_url($item['image'], apply_filters('trx_addons_filter_thumb_size', trx_addons_get_thumb_size('tiny'), 'widget_custom_links'));
					if (!empty($item['image'])) {
						$attr = trx_addons_getimagesize($item['image']);
						?><img class="custom_links_list_item_image" src="<?php echo esc_url($item['image']); ?>" alt="<?php esc_attr_e("Icon", 'trx_addons'); ?>"<?php echo (!empty($attr[3]) ? ' '.trim($attr[3]) : ''); ?>><?php
					}
				} else if (!empty($item['icon']) && !trx_addons_is_off($item['icon'])) {
					if (empty($item['icon_type'])) $item['icon_type'] = 'icons';
					$icon = !empty($item['icon_type']) && !empty($item['icon_' . $item['icon_type']]) && $item['icon_' . $item['icon_type']] != 'empty' 
								? $item['icon_' . $item['icon_type']] 
								: (!empty($item['icon']) && strtolower($item['icon'])!='none' ? $item['icon'] : '');
					if (!empty($icon)) {
						$svg = $img = '';
						if (trx_addons_is_url($icon)) {
							if (strpos($icon, '.svg') !== false) {
								$svg = $icon;
								$item['icon_type'] = 'svg';
								$svg_present = $args['icons_animation'] > 0;
							} else {
								$img = $icon;
								$item['icon_type'] = 'images';
							}
							$icon = basename($icon);
						} else if ($args['icons_animation'] > 0 && ($svg = trx_addons_get_file_dir('css/icons.svg/'.trx_addons_clear_icon_name($icon).'.svg')) != '') {
							$item['icon_type'] = 'svg';
							$svg_present = true;
						} else if (!empty($item['icon_type']) && strpos($icon_present, $item['icon_type'])===false) {
							$icon_present .= (!empty($icon_present) ? ',' : '') . $item['icon_type'];
						}
						?><span<?php
							if ( $svg_present && !empty($args['id']) ) {
								?> id="<?php echo esc_attr($args['id'].'_'.trim($icon)); ?>"<?php
							}
							?>
							class="custom_links_list_item_icon sc_icon_type_<?php
								echo esc_attr($item['icon_type']);
								echo empty($svg) && empty($img) ? ' '.esc_attr($icon) : '';
								if ($svg_present) echo ' sc_icon_animation';
								?>"
						><?php
							if (!empty($svg)) {
								trx_addons_show_layout(trx_addons_get_svg_from_file($svg));
							} else if (!empty($img)) {
								$attr = trx_addons_getimagesize($img);
								?><img class="sc_icon_as_image" src="<?php echo esc_url($img); ?>" alt="<?php esc_attr_e("Icon", 'trx_addons'); ?>"<?php echo (!empty($attr[3]) ? ' '.trim($attr[3]) : ''); ?>><?php
							}
						?></span><?php
					 }
				}
				// Title
				if (!empty($item['title'])) {
					?><span class="custom_links_list_item_title"><?php echo esc_html($item['title']); ?></span><?php
				}
				// Label
				if (!empty($item['label'])) {
					$style = '';
					if (!empty($item['label_bg_color'])) {
						$style = ' style="'
									. 'border-color:' . esc_attr($item['label_bg_color']) . ';'
									. 'background-color:' . esc_attr($item['label_bg_color']) . ';'
									. '"';
					}
					?><span class="custom_links_list_item_label"<?php trx_addons_show_layout($style); ?>><?php echo esc_html($item['label']); ?></span><?php
				}
				// Close link (or button)
				if (empty($item['url'])) {
					?></span><?php
				} else {
					?></a><?php
				}
				// Description
				if (!empty($item['description'])) {
					?><span class="custom_links_list_item_description"><?php echo esc_html($item['description']); ?></span><?php
				}
				// Button
				if (!empty($item['url']) && !empty($item['caption'])) {
					?><a class="custom_links_list_item_button sc_button sc_button_simple" href="<?php echo esc_url($item['url']); ?>"<?php trx_addons_show_layout($target); ?>><?php
						echo esc_html($item['caption']); 
					?></a><?php
				}
			?></li><?php
		}
	?></ul><?php
}

// After widget (defined by themes)
trx_addons_show_layout($after_widget);

trx_addons_load_icons($icon_present);
if ( $svg_present ) {
	wp_enqueue_script( 'vivus', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_SHORTCODES . 'icons/vivus.js'), array('jquery'), null, true );
	wp_enqueue_script( 'trx_addons-sc_icons', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_SHORTCODES . 'icons/icons.js'), array('jquery'), null, true );
}
