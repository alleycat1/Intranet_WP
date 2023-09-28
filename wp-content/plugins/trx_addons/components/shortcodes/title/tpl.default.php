<?php
/**
 * The style "default" of the Title block
 *
 * @package ThemeREX Addons
 * @since v1.4.3
 */

$args = get_query_var('trx_addons_args_sc_title');

?><div<?php if (!empty($args['id'])) echo ' id="'.esc_attr($args['id']).'"'; ?>
		class="sc_title sc_title_<?php
			echo esc_attr($args['type']);
			if (!empty($args['class'])) echo ' '.esc_attr($args['class']);
			?>"<?php
		if ($args['css']!='') echo ' style="'.esc_attr($args['css']).'"';
		trx_addons_sc_show_attributes('sc_title', $args, 'sc_wrapper');
?>><?php

	trx_addons_sc_show_titles('sc_title', $args);
	trx_addons_sc_show_links('sc_title', $args);
	
?></div>