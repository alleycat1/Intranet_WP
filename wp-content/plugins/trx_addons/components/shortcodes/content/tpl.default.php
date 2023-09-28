<?php
/**
 * The style "default" of the Content Block
 *
 * @package ThemeREX Addons
 * @since v1.2
 */

$args = get_query_var('trx_addons_args_sc_content');

if (!empty($args['push']) || !empty($args['pull'])) {
	?><div class="sc_content_wrap<?php
				if (!empty($args['push']) && !trx_addons_is_off($args['push'])) 
					echo ' sc_push_'.esc_attr($args['push'])
						. (!empty($args['push_hide_on_tablet']) ? ' sc_push_hide_on_tablet' : '')
						. (!empty($args['push_hide_on_mobile']) ? ' sc_push_hide_on_mobile' : '');
				if (!empty($args['pull']) && !trx_addons_is_off($args['pull'])) 
					echo ' sc_pull_'.esc_attr($args['pull'])
						. (!empty($args['pull_hide_on_tablet']) ? ' sc_pull_hide_on_tablet' : '')
						. (!empty($args['pull_hide_on_mobile']) ? ' sc_pull_hide_on_mobile' : '');
				?>"><?php
}
?><div<?php
	if ( ! empty( $args['id'] ) ) {
		?> id="<?php echo esc_attr($args['id']); ?>"<?php
	}
	?>
	class="sc_content sc_content_<?php echo esc_attr($args['type']);
		if (!empty($args['width'])    && !trx_addons_is_off($args['width']))    echo ' sc_content_width_'.esc_attr(str_replace('/', '_', $args['width']));
		if (!empty($args['float'])    && !trx_addons_is_off($args['float']))    echo ' sc_float_'.esc_attr($args['float']);
		if (!empty($args['align'])    && !trx_addons_is_off($args['align']))    echo ' sc_align_'.esc_attr($args['align']);
		if (!empty($args['paddings']) && !trx_addons_is_off($args['paddings'])) echo ' sc_padding_'.esc_attr($args['paddings']);
		if (!empty($args['margins'])  && !trx_addons_is_off($args['margins']))  echo ' sc_margin_'.esc_attr($args['margins']);
		if (!empty($args['extra_bg']) && !trx_addons_is_off($args['extra_bg'])) echo ' sc_extra_bg_'.esc_attr($args['extra_bg']);
		if (!empty($args['extra_bg_mask']) && !trx_addons_is_off($args['extra_bg_mask'])) echo ' sc_bg_mask_'.esc_attr($args['extra_bg_mask']);
		if (!empty($args['shift_x'])  && !trx_addons_is_off($args['shift_x']))  echo ' sc_shift_x_'.esc_attr($args['shift_x']);
		if (!empty($args['shift_y'])  && !trx_addons_is_off($args['shift_y']))  echo ' sc_shift_y_'.esc_attr($args['shift_y']);
		if (!empty($args['class'])) 											echo ' '.esc_attr($args['class']);
	?>"<?php
	if ($args['css']!='') echo ' style="'.esc_attr($args['css']).'"';
	trx_addons_sc_show_attributes('sc_content', $args, 'sc_wrapper');
?>><?php

	trx_addons_sc_show_titles('sc_content', $args);
	
	?><div class="sc_content_container<?php if (!empty($args['number'])) echo ' with_number'; ?>"<?php
		trx_addons_sc_show_attributes('sc_content', $args, 'sc_items_wrapper');
	?>><?php
		if (!empty($args['number'])) {
			?><span class="sc_content_number sc_content_number_<?php echo esc_attr($args['number_position']); ?>"<?php
				if (!empty($args['number_color'])) echo ' style="color:'.esc_attr($args['number_color']).';"';
				?>><?php
					echo esc_html($args['number']);
			?></span><?php
		}
		trx_addons_show_layout($args['content']);
	?></div><?php

	trx_addons_sc_show_links('sc_content', $args);

?></div><?php
if (!empty($args['push']) || !empty($args['pull'])) {
	?></div><?php
}