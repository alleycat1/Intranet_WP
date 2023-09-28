<?php
/**
 * The style "default" of the OpenStreetMap
 *
 * @package ThemeREX Addons
 * @since v1.6.63
 */

$args = get_query_var('trx_addons_args_sc_osmap');

?><div<?php if (!empty($args['id'])) echo ' id="'.esc_attr($args['id']).'_wrap"'; ?> class="sc_osmap_wrap"><?php

	trx_addons_sc_show_titles('sc_osmap', $args);

	if ($args['content']) {
		?><div class="sc_osmap_content_wrap"><?php
	}
	?><div<?php if (!empty($args['id'])) echo ' id="'.esc_attr($args['id']).'"'; ?>
			class="sc_item_content sc_map sc_osmap sc_osmap_<?php
				echo esc_attr($args['type']);
				if ( (int)$args['prevent_scroll'] > 0 ) echo ' sc_osmap_prevent_scroll';
				echo (!empty($args['class']) ? ' '.esc_attr($args['class']) : '');
			?>"
			<?php
			echo ($args['css']!='' ? ' style="'.esc_attr($args['css']).'"' : '');
			trx_addons_sc_show_attributes('sc_osmap', $args, 'sc_wrapper');
			?>
			data-zoom="<?php echo esc_attr($args['zoom']); ?>"
			data-center="<?php echo esc_attr($args['center']); ?>"
			data-style="<?php echo esc_attr($args['style']); ?>"
			data-cluster-icon="<?php echo esc_attr($args['cluster']); ?>"
			><?php
			$cnt = 0;
			foreach ($args['markers'] as $marker) {
				$cnt++;
				?><div<?php if (!empty($args['id'])) echo ' id="'.esc_attr($args['id']).'_'.intval($cnt).'"'; ?>
						class="sc_osmap_marker"
						data-address="<?php echo esc_attr(!empty($marker['address']) ? $marker['address'] : ''); ?>"
						data-description="<?php echo esc_attr(!empty($marker['description']) ? $marker['description'] : ''); ?>"
						data-title="<?php echo esc_attr(!empty($marker['title']) ? $marker['title'] : ''); ?>"
						data-icon="<?php echo esc_attr(!empty($marker['icon']) ? $marker['icon'] : ''); ?>"
						data-icon_shadow="<?php echo esc_attr(!empty($marker['icon']) && !empty($marker['icon_shadow']) ? $marker['icon_shadow'] : ''); ?>"
						data-icon_width="<?php echo esc_attr(!empty($marker['icon']) && !empty($marker['icon_width']) ? $marker['icon_width'] : ''); ?>"
						data-icon_height="<?php echo esc_attr(!empty($marker['icon']) && !empty($marker['icon_height']) ? $marker['icon_height'] : ''); ?>"
				></div><?php
			}
	?></div><?php
	
	if ($args['content']) {
		?>
			<div class="sc_osmap_content sc_osmap_content_<?php echo esc_attr($args['type']); ?>"><?php trx_addons_show_layout($args['content']); ?></div>
		</div>
		<?php
	}

	trx_addons_sc_show_links('sc_osmap', $args);
	
?></div>