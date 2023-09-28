<?php
$args = get_query_var('trx_addons_args_sc_supertitle_args');
if (!empty($args['text'])) {
	if (trx_addons_is_off($args['tag'])) $args['tag'] = 'h2';
	?><<?php echo esc_attr($args['tag']); ?> class="sc_supertitle_text<?php
		if (!empty($args['inline'])) {
			echo ' sc_supertitle_display_inline';
		}
		if (!empty($args['color'])) {
			if (empty($args['color2']) && empty($args['link'])) {
				echo ' ' . trx_addons_add_inline_css_class('color: ' . esc_attr($args['color']) . ' !important;');
			}
		}
	?>"><?php
		if (!empty($args['link'])) {
			?><a href="<?php echo esc_url($args['link']); ?>"<?php
				if (!empty($args['color']) && empty($args['color2'])) echo ' class="' . trx_addons_add_inline_css_class('color: ' . esc_attr($args['color']) . ' !important;') . '"';
				if (!empty($args['new_window']) || !empty($args['link_extra']['is_external'])) echo ' target="_blank"';
				if (!empty($args['nofollow']) || !empty($args['link_extra']['nofollow'])) echo ' rel="nofollow"';
			?>><?php
		}

		if (!empty($args['color']) && !empty($args['color2'])) {
			echo '<span class="trx_addons_text_gradient" style="'
					. 'color:' . esc_attr($args['color']) . ';'
					. 'background:linear-gradient(' 
							. max(0, min(360, (int) $args['gradient_direction'])) . 'deg,'
							. esc_attr(!empty($args['color2']) ? $args['color2'] : 'transparent') . ','
							. esc_attr($args['color']) . ');'
				. '">' 
					. wp_kses_data($args['text'])
				. '</span>';
		} else {
			trx_addons_show_layout($args['text']);
		}

		if (!empty($args['link'])) {
			?></a><?php
		}
	?></<?php echo esc_attr($args['tag']); ?>><?php
}