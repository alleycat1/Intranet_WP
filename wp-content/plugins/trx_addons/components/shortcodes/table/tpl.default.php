<?php
/**
 * The style "default" of the table
 *
 * @package ThemeREX Addons
 * @since v1.3
 */

$args = get_query_var('trx_addons_args_sc_table');

?><div<?php if (!empty($args['id'])) echo ' id="'.esc_attr($args['id']).'_wrap"'; ?> class="sc_table_wrap"><?php

	trx_addons_sc_show_titles('sc_table', $args);
	
	?><div<?php if (!empty($args['id'])) echo ' id="'.esc_attr($args['id']).'"'; ?>
		class="sc_table sc_table_<?php
				echo esc_attr($args['type']);
				echo (!trx_addons_is_off($args['align']) ? ' align'.esc_attr($args['align']) : '');
				echo (!empty($args['class']) ? ' '.esc_attr($args['class']) : '');
				if (!empty($args['width'])) echo ' ' . trx_addons_add_inline_css_class(trx_addons_get_css_dimensions_from_values($args['width']));
				?>"<?php
		if ($args['css']!='') echo ' style="'.esc_attr($args['css']).'"';
		trx_addons_sc_show_attributes('sc_table', $args, 'sc_wrapper');
	?>><?php
		trx_addons_show_layout($args['content']);
	?></div><?php

	trx_addons_sc_show_links('sc_table', $args);
	
?></div>