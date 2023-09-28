<?php
/**
 * The style "default" of the Googlemap
 *
 * @package ThemeREX Addons
 * @since v1.2
 */

$args = get_query_var('trx_addons_args_sc_googlemap');

?><div<?php if (!empty($args['id'])) echo ' id="'.esc_attr($args['id']).'_wrap"'; ?> class="sc_googlemap_wrap"><?php

	trx_addons_sc_show_titles('sc_googlemap', $args);

	if ($args['content']) {
		?><div class="sc_googlemap_content_wrap"><?php
	}
	?><div<?php if (!empty($args['id'])) echo ' id="'.esc_attr($args['id']).'"'; ?>
			class="sc_item_content sc_map sc_googlemap sc_googlemap_<?php
				echo esc_attr($args['type']);
				if ( (int)$args['prevent_scroll'] > 0 ) echo ' sc_googlemap_prevent_scroll';
				echo (!empty($args['class']) ? ' '.esc_attr($args['class']) : '');
			?>"
			<?php
			echo ($args['css']!='' ? ' style="'.esc_attr($args['css']).'"' : '');
			trx_addons_sc_show_attributes('sc_googlemap', $args, 'sc_wrapper');
			?>
			data-zoom="<?php echo esc_attr($args['zoom']); ?>"
			data-center="<?php echo esc_attr($args['center']); ?>"
			data-style="<?php echo esc_attr($args['style']); ?>"
			data-cluster-icon="<?php echo esc_attr($args['cluster']); ?>"
			><?php
			$cnt = 0;
			foreach ($args['markers'] as $marker) {
				$cnt++;
				// If Google API key is present - make our layout
				if ( trx_addons_get_option('api_google') != '' ) {
					?><div<?php if (!empty($args['id'])) echo ' id="'.esc_attr($args['id']).'_'.intval($cnt).'"'; ?>
							class="sc_googlemap_marker"
							data-address="<?php echo esc_attr(!empty($marker['address']) ? $marker['address'] : ''); ?>"
							data-description="<?php echo esc_attr(!empty($marker['description']) ? $marker['description'] : ''); ?>"
							data-title="<?php echo esc_attr(!empty($marker['title']) ? $marker['title'] : ''); ?>"
							data-animation="<?php echo esc_attr(!empty($marker['animation']) ? $marker['animation'] : ''); ?>"
							data-html="<?php echo esc_attr(!empty($marker['html']) ? $marker['html'] : ''); ?>"
							data-url="<?php echo esc_attr(!empty($marker['url']) ? $marker['url'] : ''); ?>"
							data-icon="<?php echo esc_attr(!empty($marker['icon']) ? $marker['icon'] : ''); ?>"
							data-icon_shadow="<?php echo esc_attr(!empty($marker['icon']) && !empty($marker['icon_shadow']) ? $marker['icon_shadow'] : ''); ?>"
							data-icon_width="<?php echo esc_attr(!empty($marker['icon']) && !empty($marker['icon_width']) ? $marker['icon_width'] : ''); ?>"
							data-icon_height="<?php echo esc_attr(!empty($marker['icon']) && !empty($marker['icon_height']) ? $marker['icon_height'] : ''); ?>"
					></div><?php

				// If Google API key unknown - make iframe
				} else {
					?><iframe
						src="https://maps.google.com/maps?t=m&output=embed&iwloc=near&z=<?php
							echo esc_attr($args['zoom'] > 0 ? $args['zoom'] : 14);
							?>&q=<?php
							echo esc_attr(!empty($marker['address']) ? urlencode($marker['address']) : '');
							?>"
						aria-label="<?php echo esc_attr(!empty($marker['title']) ? $marker['title'] : ''); ?>"></iframe><?php
					// Remove 'break' if you want display separate 'iframe' for each marker
					// Otherwise, only first marker will be shown
					break;
				}
			}
	?></div><?php
	
	if ($args['content']) {
		?>
			<div class="sc_googlemap_content sc_googlemap_content_<?php echo esc_attr($args['type']); ?>"><?php trx_addons_show_layout($args['content']); ?></div>
		</div>
		<?php
	}

	trx_addons_sc_show_links('sc_googlemap', $args);
	
?></div>