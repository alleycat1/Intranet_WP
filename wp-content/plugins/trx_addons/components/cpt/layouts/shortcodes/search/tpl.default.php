<?php
/**
 * The style "default" of the Search form
 *
 * @package ThemeREX Addons
 * @since v1.6.08
 */

$args = get_query_var('trx_addons_args_sc_layouts_search');

?><div<?php if (!empty($args['id'])) echo ' id="'.esc_attr($args['id']).'"'; ?> class="sc_layouts_search<?php
		trx_addons_cpt_layouts_sc_add_classes($args);
	?>"<?php
	if (!empty($args['css'])) echo ' style="'.esc_attr($args['css']).'"';
	trx_addons_sc_show_attributes('sc_layouts_search', $args, 'sc_wrapper');
?>><?php

	$args['class'] = ( !empty($args['class']) ? ' ' : '' ) . 'layouts_search';
	
	do_action('trx_addons_action_search', $args);
	
?></div><?php

trx_addons_sc_layouts_showed('search', true);
