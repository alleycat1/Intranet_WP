<?php
/**
 * The template to display any sport's type archive
 *
 * @package ThemeREX Addons
 * @since v1.6.17
 */

get_header(); 

do_action('trx_addons_action_start_archive');

if (have_posts()) {
	
	global $post;
	$trx_addons_slug    = trx_addons_cpt_param($post->post_type, 'post_type_slug');

	$trx_addons_style   = explode('_', trx_addons_get_option('competitions_style'));
	$trx_addons_type    = $trx_addons_style[0];
	$trx_addons_columns = empty($trx_addons_style[1]) ? 1 : max(1, $trx_addons_style[1]);

	?><div class="sc_sport sc_sport_default<?php if (!empty($trx_addons_slug)) echo ' sc_'.esc_attr($trx_addons_slug).' sc_'.esc_attr($trx_addons_slug).'_default'; ?>">

		<div class="sc_sport_columns<?php
			if ( !empty($trx_addons_slug) ) {
				echo ' sc_'.esc_attr($trx_addons_slug).'_columns'
					. ' ' . esc_attr(trx_addons_get_columns_wrap_class())
					. ' columns_padding_bottom'
				 	. esc_attr( trx_addons_add_columns_in_single_row( $trx_addons_columns ) );	

			}
		?>"><?php
			
			while ( have_posts() ) { the_post(); 
				trx_addons_get_template_part(array(
												TRX_ADDONS_PLUGIN_CPT . 'sport/tpl.'.trim($trx_addons_slug).'.'.trim($trx_addons_type).'-item.php',
												TRX_ADDONS_PLUGIN_CPT . 'sport/tpl.'.trim($trx_addons_slug).'.archive-item.php',
												TRX_ADDONS_PLUGIN_CPT . 'sport/tpl.archive-item.php'
												),
												'trx_addons_args_sc_sport', 
												array(
													'type' => $trx_addons_type,
													'columns' => $trx_addons_columns,
													'slug' => $trx_addons_slug,
													'hide_excerpt' => false,
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
