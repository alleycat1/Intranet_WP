<?php
/**
 * The template to display the services archive
 *
 * @package ThemeREX Addons
 * @since v1.4
 */

get_header(); 

do_action('trx_addons_action_start_archive');

if (have_posts()) {

	$trx_addons_services_style   = explode('_', trx_addons_get_option('services_style'));
	$trx_addons_services_type    = $trx_addons_services_style[0];
	$trx_addons_services_columns = empty($trx_addons_services_style[1]) ? 1 : max(1, $trx_addons_services_style[1]);

	?><div class="sc_services sc_services_<?php echo esc_attr($trx_addons_services_type); ?>">
		
		<div class="sc_services_columns_wrap sc_item_columns sc_item_columns_<?php
							echo esc_attr($trx_addons_services_columns);
							echo ' '.esc_attr(trx_addons_get_columns_wrap_class())
								. ' columns_padding_bottom'
								. esc_attr( trx_addons_add_columns_in_single_row( $trx_addons_services_columns ) )
								. ( $trx_addons_services_type=='chess' ? ' no_margin' : '' );
					?>"><?php
			$trx_addons_item_number = 0;
			while ( have_posts() ) { the_post(); 
				$trx_addons_item_number++;
				set_query_var('trx_addons_args_item_number', $trx_addons_item_number);
				trx_addons_get_template_part(array(
												TRX_ADDONS_PLUGIN_CPT . 'services/tpl.'.trim($trx_addons_services_type).'-item.php',
												TRX_ADDONS_PLUGIN_CPT . 'services/tpl.default-item.php'
												),
												'trx_addons_args_sc_services',
												array(
													'type' => $trx_addons_services_type,
													'columns' => $trx_addons_services_columns,
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
