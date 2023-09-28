<?php
/**
 * The template with loop of the cars archive
 *
 * @package ThemeREX Addons
 * @since v1.6.25
 */

$args = get_query_var('trx_addons_args_cars_loop');

$trx_addons_need_reset_query = false;
if (!empty($args['query_params'])) {
	$query_args = trx_addons_cpt_cars_query_params_to_args($args['query_params'], true);
	if (count($query_args) > 0) query_posts($query_args);
	$trx_addons_need_reset_query = true;
}

do_action('trx_addons_action_start_archive');

if (have_posts()) {

	$trx_addons_cars_style   = explode('_', !empty($args['blog_style'])
															? $args['blog_style']
															: trx_addons_get_option('cars_blog_style')
											);
	$trx_addons_cars_type    = $trx_addons_cars_style[0];
	$trx_addons_cars_columns = empty($trx_addons_cars_style[1]) ? 1 : max(1, $trx_addons_cars_style[1]);

	?><div class="sc_cars sc_cars_<?php echo esc_attr( $trx_addons_cars_type ); ?>">
		
		<div class="sc_cars_columns_wrap sc_cars_columns_<?php
			echo esc_attr($trx_addons_cars_columns);
			if ($trx_addons_cars_columns > 1) {
				echo ' ' . esc_attr(trx_addons_get_columns_wrap_class())
					. ' columns_padding_bottom'
					. esc_attr( trx_addons_add_columns_in_single_row( $trx_addons_cars_columns ) );
			}
		?>"><?php

			while ( have_posts() ) { the_post(); 
				trx_addons_get_template_part(array(
												TRX_ADDONS_PLUGIN_CPT . 'cars/tpl.cars.'.trim($trx_addons_cars_type).'-item.php',
												TRX_ADDONS_PLUGIN_CPT . 'cars/tpl.cars.default-item.php'
												),
												'trx_addons_args_sc_cars',
												array(
													'type' => $trx_addons_cars_type,
													'columns' => $trx_addons_cars_columns,
													'slider' => false
												)
											);
			}
			wp_reset_postdata();

		?></div><?php

    ?></div><?php

	trx_addons_show_pagination();

} else {

	trx_addons_get_template_part('templates/tpl.posts-none.php');

}

do_action('trx_addons_action_end_archive');

if ($trx_addons_need_reset_query) wp_reset_query();
