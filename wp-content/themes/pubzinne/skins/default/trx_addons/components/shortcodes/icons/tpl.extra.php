<?php
/**
 * The style "default" of the Icons
 *
 * @package ThemeREX Addons
 * @since v1.2
 */

$args = get_query_var('trx_addons_args_sc_icons');

$icon_present = '';
$svg_present = false;

?><div id="<?php echo esc_attr($args['id']); ?>" 
	class="sc_icons sc_icons_<?php
		echo esc_attr($args['type']);
		echo ' sc_icons_size_' . esc_attr($args['size']);
		if (!empty($args['align'])) echo ' sc_align_'.esc_attr($args['align']);
		if (!empty($args['class'])) echo ' '.esc_attr($args['class']);
	?>"<?php
	if (!empty($args['css'])) echo ' style="'.esc_attr($args['css']).'"';
?>><?php

	trx_addons_sc_show_titles('sc_icons', $args);

	if ((int) $args['columns'] > 1) {
		?><div class="sc_icons_columns_wrap sc_item_columns <?php
			echo esc_attr(trx_addons_get_columns_wrap_class())
				. ' columns_padding_bottom'
				. esc_attr( trx_addons_add_columns_in_single_row( $args['columns'], $args['icons'] ) );
		?>"><?php
	}

	foreach ($args['icons'] as $item) {
		$item['color'] = !empty($item['color']) ? $item['color'] : $args['color'];
		if ((int) $args['columns'] > 1) {
			?><div class="<?php echo esc_attr(trx_addons_get_column_class(1, $args['columns'], !empty($args['columns_tablet']) ? $args['columns_tablet'] : '', !empty($args['columns_mobile']) ? $args['columns_mobile'] : '')); ?>"><?php
		}
		?><div class="sc_icons_item<?php echo !empty($item['link']) ? ' sc_icons_item_linked' : ''; ?>"><?php
			if (!empty($item['char'])) {
				?><div class="sc_icons_icon sc_icons_char" data-char="<?php echo esc_attr($item['char']); ?>"<?php
						if (!empty($item['color'])) echo ' style="color: '.esc_attr($item['color']).'"';
					?>><span data-char="<?php echo esc_attr($item['char']); ?>"<?php
						if (!empty($item['color'])) echo ' style="color: '.esc_attr($item['color']).'"';
						?>></span></div><?php
			} else if (!empty($item['image'])) {
				$item['image'] = trx_addons_get_attachment_url($item['image'], apply_filters('trx_addons_filter_thumb_size', trx_addons_get_thumb_size('medium'), 'icons-default'));
				if (!empty($item['image'])) {
					$attr = trx_addons_getimagesize($item['image']);
					?><div class="sc_icons_image"><img src="<?php echo esc_url($item['image']); ?>" alt="<?php esc_attr_e('Icon', 'pubzinne'); ?>"<?php echo (!empty($attr[3]) ? ' '.trim($attr[3]) : ''); ?>></div><?php
				}
			} else {
				if (empty($item['icon_type'])) $item['icon_type'] = '';
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
					?><div class="sc_icons_icon sc_icon_type_<?php
						echo esc_attr($item['icon_type']);
						echo empty($svg) && empty($img) ? ' '.esc_attr($icon) : '';
						if ($svg_present) echo ' sc_icon_animation';
						?>"<?php
						if (!empty($item['color'])) echo ' style="color: '.esc_attr($item['color']).'"';
					?>><?php
						if (!empty($svg)) {
							trx_addons_show_layout(trx_addons_get_svg_from_file($svg));
						} else if (!empty($img)) {
							$attr = trx_addons_getimagesize($img);
							?><img class="sc_icon_as_image" src="<?php echo esc_url($img); ?>" alt="<?php esc_attr_e('Icon', 'pubzinne'); ?>"<?php echo (!empty($attr[3]) ? ' '.trim($attr[3]) : ''); ?>><?php
						} else {
							?><span class="sc_icon_type_<?php echo esc_attr($item['icon_type']); ?> <?php echo esc_attr($icon); ?>"<?php
								if (!empty($item['color'])) echo ' style="color: '.esc_attr($item['color']).'"'; 
							?>></span><?php
                            if (!empty($item['title'])) {
                                $rest = substr($item['title'], 0, 2);
                                ?> <div class="sc_icon_extra_title"><?php
                                    pubzinne_show_layout($rest);
                                ?> </div><?php
                            }

						}
					?></div><?php
				 }
			}
			?><div class="sc_icons_item_details"><?php
				if (!empty($item['title'])) {

					$item['title'] = explode('|', $item['title']);
					?><h4 class="sc_icons_item_title"><?php
						foreach ($item['title'] as $str) {
							?><span><?php echo esc_html($str); ?></span><?php
						}
					?></h4><?php
				}
				if (!empty($item['description'])) {
					?><div class="sc_icons_item_description"><?php
						if (strpos($item['description'], '<p>') === false) {
							$item['description'] = explode('|', str_replace("\n", '|', $item['description']));
							foreach ($item['description'] as $str) {
								?><span><?php trx_addons_show_layout($str); ?></span><?php
							}
						} else
							trx_addons_show_layout($item['description']);
					?></div><?php
				}
				if (!empty($item['link'])) {
					?><a href="<?php echo esc_url($item['link']); ?>"
						class="<?php echo esc_attr(apply_filters('trx_addons_filter_sc_item_link_classes', 'sc_icons_item_link', 'sc_icons', $args));
					?>"<?php
						if (!empty($item['new_window']) || !empty($item['link_extra']['is_external'])) echo ' target="_blank"';
						if (!empty($item['nofollow']) || !empty($item['link_extra']['nofollow'])) echo ' rel="nofollow"';
					?>></a><?php
				}
			?></div><?php
		?></div><?php
		if ((int) $args['columns'] > 1) {
			?></div><?php
		}
	}

	if ((int) $args['columns'] > 1) {
		?></div><?php
	}

	trx_addons_sc_show_links('sc_icons', $args);

?></div><!-- /.sc_icons --><?php

trx_addons_load_icons($icon_present);
if (trx_addons_is_on(trx_addons_get_option('debug_mode')) && $svg_present) {
	wp_enqueue_script( 'vivus', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_SHORTCODES . 'icons/vivus.js'), array('jquery'), null, true );
	wp_enqueue_script( 'trx-addons-sc-icons', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_SHORTCODES . 'icons/icons.js'), array('jquery'), null, true );
}
?>