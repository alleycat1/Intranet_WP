<?php
/**
 * The template to display the agents archive
 *
 * @package ThemeREX Addons
 * @since v1.6.22
 */

get_header(); 

do_action('trx_addons_action_start_archive');

if (have_posts()) {

	$trx_addons_agents_style   = explode('_', trx_addons_get_option('agents_style'));
	$trx_addons_agents_type    = $trx_addons_agents_style[0];
	$trx_addons_agents_columns = empty($trx_addons_agents_style[1]) ? 1 : max(1, $trx_addons_agents_style[1]);

	?><div class="sc_agents sc_agents_default sc_team sc_team_default">
		
		<div class="sc_agents_columns_wrap sc_team_columns_wrap sc_agents_columns_<?php
			echo esc_attr($trx_addons_agents_columns);
			if ($trx_addons_agents_columns > 1) {
				echo ' ' . esc_attr(trx_addons_get_columns_wrap_class())
					. ' columns_padding_bottom'
					. esc_attr( trx_addons_add_columns_in_single_row( $trx_addons_agents_columns ) );
			}
		?>"><?php
		
			while ( have_posts() ) { the_post(); 
				trx_addons_get_template_part(array(
												TRX_ADDONS_PLUGIN_CPT . 'properties/tpl.agents.'.trim($trx_addons_agents_type).'-item.php',
												TRX_ADDONS_PLUGIN_CPT . 'properties/tpl.agents.default-item.php'
												),
												'trx_addons_args_sc_agents',
												array(
													'type' => $trx_addons_agents_type,
													'columns' => $trx_addons_agents_columns,
													'slider' => false
												)
											);
			}
	
		?></div><?php

    ?></div><?php

	trx_addons_show_pagination();

} else {

	trx_addons_get_template_part('templates/tpl.posts-none.php');

}

do_action('trx_addons_action_end_archive');

get_footer();
