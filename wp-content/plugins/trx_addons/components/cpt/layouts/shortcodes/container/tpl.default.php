<?php
/**
 * The style "default" of the Container
 *
 * @package ThemeREX Addons
 * @since v1.6.28
 */

$args = get_query_var('trx_addons_args_sc_layouts_container');

?><div<?php if (!empty($args['id'])) echo ' id="'.esc_attr($args['id']).'"'; ?> class="sc_layouts_container<?php
		trx_addons_cpt_layouts_sc_add_classes($args);
	?>"<?php
	if (!empty($args['css'])) echo ' style="'.esc_attr($args['css']).'"';
	trx_addons_sc_show_attributes('sc_layouts_container', $args, 'sc_wrapper');
?>><?php
	trx_addons_show_layout($args['content']);
?></div>