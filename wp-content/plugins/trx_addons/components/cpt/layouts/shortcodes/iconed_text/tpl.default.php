<?php
/**
 * The style "default" of the Iconed text
 *
 * @package ThemeREX Addons
 * @since v1.6.08
 */

$args = get_query_var('trx_addons_args_sc_layouts_iconed_text');

?><div<?php if (!empty($args['id'])) echo ' id="'.esc_attr($args['id']).'"'; ?> class="sc_layouts_iconed_text<?php
		trx_addons_cpt_layouts_sc_add_classes($args);
	?>"<?php
	if (!empty($args['css'])) echo ' style="'.esc_attr($args['css']).'"';
	trx_addons_sc_show_attributes('sc_layouts_iconed_text', $args, 'sc_wrapper');
?>><?php

	// Open link
	if (!empty($args['link'])) {
		?><a href="<?php echo esc_url($args['link']); ?>" class="sc_layouts_item_link sc_layouts_iconed_text_link"<?php
			if (!empty($args['new_window']) || !empty($args['link_extra']['is_external'])) echo ' target="_blank"';
			if (!empty($args['nofollow']) || !empty($args['link_extra']['nofollow'])) echo ' rel="nofollow"';
		?>><?php
	}
	
	// Icon or Image
	if (!empty($args['icon'])) {
		$img = $svg = '';
		$icon = $args['icon'];
		$icon_type = 'icons';
		if (trx_addons_is_url($icon)) {
			if (strpos($icon, '.svg') !== false) {
				$svg = $icon;
				$icon_type = 'svg';
			} else {
				$img = $icon;
				$icon_type = 'images';
			}
			$icon = basename($icon);
		}
		?><span class="sc_layouts_item_icon sc_layouts_iconed_text_icon <?php echo esc_attr($icon); ?> sc_icon_type_<?php echo esc_attr($icon_type); ?>"><?php
			if (!empty($svg)) {
				trx_addons_show_layout(trx_addons_get_svg_from_file($svg));
			} else if (!empty($img)) {
				$attr = trx_addons_getimagesize($img);
				?><img class="sc_icon_as_image" src="<?php echo esc_url($img); ?>" alt="<?php esc_attr_e('Icon', 'trx_addons'); ?>"<?php echo (!empty($attr[3]) ? ' '.trim($attr[3]) : ''); ?>><?php
			}
		?></span><?php
	}
	if (!empty($args['text1']) || !empty($args['text2'])) {
		?><span class="sc_layouts_item_details sc_layouts_iconed_text_details"><?php
			if (!empty($args['text1'])) {
				?><span class="sc_layouts_item_details_line1 sc_layouts_iconed_text_line1"><?php echo esc_html($args['text1']); ?></span><?php
			}
			if (!empty($args['text2'])) {
				?><span class="sc_layouts_item_details_line2 sc_layouts_iconed_text_line2"><?php echo esc_html($args['text2']); ?></span><?php
			}
		?></span><?php
	}

	// Close link
	if (!empty($args['link'])) {
		?></a><?php
	}
?></div><?php

trx_addons_sc_layouts_showed('iconed_text', true);
