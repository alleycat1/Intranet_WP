<?php
/**
 * The style "square" of the Countdown
 *
 * @package ThemeREX Addons
 * @since v1.4.3
 */

$args = get_query_var('trx_addons_args_sc_countdown');
$link_color = apply_filters('trx_addons_filter_get_theme_accent_color', '#efa758');
if ( !empty($args['count_restart']) && !empty($args['date_time_restart']) ) {
	$tmp = array_slice( explode(':', $args['date_time_restart']), -4 );
	$args['date'] = count($tmp) > 3 ? $tmp[0] : '';
	$args['time'] = join(':', array_slice($tmp, -3));
} else if (empty($args['count_restart']) && !empty($args['date_time'])) {
	$tmp = explode(' ', $args['date_time']);
	$args['date'] = $tmp[0];
	if (!empty($tmp[1])) $args['time'] = $tmp[1];
} else {
	if (empty($args['date'])) {
		$args['date'] = date('Y-m-d');
	}
	if (empty($args['time'])) {
		$args['time'] = '00:00:00';
	}
}
?><div<?php
	if ( ! empty( $args['id'] ) ) {
		?> id="<?php echo esc_attr($args['id']); ?>"<?php
	}
	?>
	class="sc_countdown sc_countdown_<?php
			echo esc_attr($args['type']);
			if (!empty($args['class'])) echo ' '.esc_attr($args['class']);
			if (!empty($args['align']) && $args['align']!='none') echo ' align'.esc_attr($args['align']);
		?>"<?php
	if ($args['css']!='') echo ' style="'.esc_attr($args['css']).'"';
	?>
	data-date="<?php echo esc_attr($args['date']); ?>"
	data-time="<?php echo esc_attr($args['time']); ?>"
	data-count-to="<?php echo esc_attr(empty($args['count_to']) ? '0' : '1'); ?>"
	data-count-restart="<?php echo esc_attr(empty($args['count_restart']) ? '0' : '1'); ?>"
	<?php trx_addons_sc_show_attributes('sc_countdown', $args, 'sc_wrapper'); ?>
><?php

	trx_addons_sc_show_titles('sc_countdown', $args);

	?>
    <div class="sc_countdown_content sc_item_content"<?php trx_addons_sc_show_attributes('sc_countdown', $args, 'sc_items_wrapper'); ?>>

    	<div class="sc_countdown_inner"><?php
	
			// Days
			?><div class="sc_countdown_item sc_countdown_days"<?php trx_addons_sc_show_attributes('sc_countdown', $args, 'sc_item_wrapper'); ?>><?php
                if ($args['type'] == 'circle') { 
					?><canvas<?php
						if ( ! empty( $args['id'] ) ) {
							?> id="<?php echo esc_attr($args['id']); ?>_days"<?php
						}
						?>
						width="90" height="90"
						data-max-value="366"
						data-color="<?php echo esc_attr($link_color); ?>"></canvas><?php
				}
				?>
				<span class="sc_countdown_digits"><span></span><span></span><span></span></span>
				<span class="sc_countdown_label"><?php esc_html_e('Days', 'trx_addons'); ?></span>
			 </div><?php
			
			// Separator
			?><div class="sc_countdown_separator">:</div><?php
			 
			// Hours
			?><div class="sc_countdown_item sc_countdown_hours"<?php trx_addons_sc_show_attributes('sc_countdown', $args, 'sc_item_wrapper'); ?>><?php
				if ($args['type'] == 'circle') { 
					?><canvas<?php
						if ( ! empty( $args['id'] ) ) {
							?> id="<?php echo esc_attr($args['id']); ?>_hours"<?php
						}
						?>
						width="90" height="90"
						data-max-value="24"
						data-color="<?php echo esc_attr($link_color); ?>"></canvas><?php
				}
				?>
				<span class="sc_countdown_digits"><span></span><span></span></span>
				<span class="sc_countdown_label"><?php esc_html_e('Hours', 'trx_addons'); ?></span>
			</div><?php
			
			// Separator
			?><div class="sc_countdown_separator">:</div><?php
			
			// Minutes
			?><div class="sc_countdown_item sc_countdown_minutes"<?php trx_addons_sc_show_attributes('sc_countdown', $args, 'sc_item_wrapper'); ?>><?php
                if ($args['type'] == 'circle') {
					?><canvas<?php
						if ( ! empty( $args['id'] ) ) {
							?> id="<?php echo esc_attr($args['id']); ?>_minutes"<?php
						}
						?>
						width="90" height="90"
						data-max-value="60"
						data-color="<?php echo esc_attr($link_color); ?>"></canvas><?php
				}
				?>
				<span class="sc_countdown_digits"><span></span><span></span></span>
				<span class="sc_countdown_label"><?php esc_html_e('Minutes', 'trx_addons'); ?></span>
			</div><?php
			
			// Separator
			?><div class="sc_countdown_separator">:</div><?php
		
			// Seconds
			?><div class="sc_countdown_item sc_countdown_seconds"<?php trx_addons_sc_show_attributes('sc_countdown', $args, 'sc_item_wrapper'); ?>><?php
                if ($args['type'] == 'circle') {
					?><canvas<?php
						if ( ! empty( $args['id'] ) ) {
							?> id="<?php echo esc_attr($args['id']); ?>_seconds"<?php
						}
						?>
						width="90" height="90"
						data-max-value="60"
						data-color="<?php echo esc_attr($link_color); ?>"></canvas><?php
				}
				?>
				<span class="sc_countdown_digits"><span></span><span></span></span>
				<span class="sc_countdown_label"><?php esc_html_e('Seconds', 'trx_addons'); ?></span>
			</div><?php
			
			// Placeholder
			?><div class="sc_countdown_placeholder hide"></div>

         </div>
		
	</div><?php

	trx_addons_sc_show_links('sc_countdown', $args);

?></div>