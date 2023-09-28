<?php
/**
 * The template to display the portfolio details
 *
 * @package ThemeREX Addons
 * @since v1.71.0
 */

$args  = get_query_var('trx_addons_args_portfolio_details');
$meta  = $args['meta'];
$class = $args['class'];

if (!empty($meta['details']) && count($meta['details']) > 0 && !empty($meta['details'][0]['title'])) {
	?><div class="<?php echo esc_attr( $class ); ?>"><?php
		$count = 0;
		foreach($meta['details'] as $item) {
			if (empty($item['title']) || empty($item['value'])) continue;
			?><span class="<?php echo esc_attr( $class ); ?>_item"><?php
				// Title
				?><span class="<?php echo esc_attr( $class ); ?>_item_title"><?php echo esc_html($item['title']); ?></span><?php
				// Value
				if (!empty($item['link'])) {
					?><a href="<?php echo esc_url($item['link']); ?>"<?php
				} else {
					?><span<?php
				}
				?> class="<?php echo esc_attr( $class ); ?>_item_value"><?php
					// Icon
					if (!empty($item['icon'])) {
						$icon = $item['icon'];
						$img = $svg = '';
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
						?><span class="<?php echo esc_attr( $class ); ?>_item_icon sc_icon_type_<?php echo esc_attr($icon_type); ?> <?php echo esc_attr($icon); ?>"><?php
							if (!empty($svg)) {
								trx_addons_show_layout(trx_addons_get_svg_from_file($svg));
							} else if (!empty($img)) {
								$attr = trx_addons_getimagesize($img);
								?><img class="sc_icon_as_image" src="<?php echo esc_url($img); ?>" alt="<?php esc_attr_e('Icon', 'trx_addons'); ?>"<?php echo (!empty($attr[3]) ? ' '.trim($attr[3]) : ''); ?>><?php
							}
						?></span><?php
					}
					echo esc_html($item['value']);
				if (!empty($item['link'])) {
					?></a><?php
				} else {
					?></span><?php
				}
			?></span><?php
			$count++;
			if ( $args['count'] > 0 && $count >= $args['count'] ) {
				break;
			}
		}
		// Share
		if ( ! empty( $args['share'] ) ) {
			$trx_addons_output = trx_addons_get_share_links(array(
					'type' => 'list',
					'caption' => '',
					'echo' => false
				));
			if ($trx_addons_output) {
				?><span class="<?php echo esc_attr( $class ); ?>_item <?php echo esc_attr( $class ); ?>_share"><?php
					// Title
					?><span class="<?php echo esc_attr( $class ); ?>_item_title"><?php echo esc_html__('Share', 'trx_addons'); ?></span><?php
					// Value
					?><span class="<?php echo esc_attr( $class ); ?>_item_value"><?php trx_addons_show_layout($trx_addons_output); ?></span><?php
				?></span><?php
			}
		}
	?></div><?php
}
