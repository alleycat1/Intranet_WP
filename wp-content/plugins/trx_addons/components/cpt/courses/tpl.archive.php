<?php
/**
 * The template to display the courses archive
 *
 * @package ThemeREX Addons
 * @since v1.2
 */

get_header(); 

do_action('trx_addons_action_start_archive');

if (have_posts()) {

	$trx_addons_courses_style   = explode('_', trx_addons_get_option('courses_style'));
	$trx_addons_courses_type    = $trx_addons_courses_style[0];
	$trx_addons_courses_columns = empty($trx_addons_courses_style[1]) ? 1 : max(1, $trx_addons_courses_style[1]);

	?><div class="sc_courses sc_courses_<?php echo esc_attr( $trx_addons_courses_type ); ?>">

		<div class="sc_courses_columns_wrap <?php
			echo esc_attr(trx_addons_get_columns_wrap_class())
				.' columns_padding_bottom'
				. esc_attr( trx_addons_add_columns_in_single_row( $trx_addons_courses_columns ) );
		?>"><?php

			while ( have_posts() ) { the_post(); 
				trx_addons_get_template_part(array(
												TRX_ADDONS_PLUGIN_CPT . 'courses/tpl.'.trim($trx_addons_courses_type).'-item.php',
												TRX_ADDONS_PLUGIN_CPT . 'courses/tpl.default-item.php'
												),
												'trx_addons_args_sc_courses',
												array(
													'type' => $trx_addons_courses_type,
													'columns' => $trx_addons_courses_columns,
													'hide_excerpt' => false,
													'more_text' => __('Learn more', 'trx_addons'),
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
?>