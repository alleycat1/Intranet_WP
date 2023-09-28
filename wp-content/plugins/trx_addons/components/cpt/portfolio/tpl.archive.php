<?php
/**
 * The template to display the portfolio archive
 *
 * @package ThemeREX Addons
 * @since v1.5
 */

get_header(); 

do_action('trx_addons_action_start_archive');

if ( have_posts() ) {

	$trx_addons_portfolio_style   = explode('_', trx_addons_get_option('portfolio_style'));
	$trx_addons_portfolio_type    = $trx_addons_portfolio_style[0];
	$trx_addons_portfolio_columns = empty($trx_addons_portfolio_style[1]) ? 1 : max(1, $trx_addons_portfolio_style[1]);
	$trx_addons_portfolio_masonry = trx_addons_is_on( trx_addons_get_option( 'portfolio_use_masonry' ) );
	$trx_addons_portfolio_gallery = trx_addons_is_on( trx_addons_get_option( 'portfolio_use_gallery' ) );

	?><div class="sc_portfolio sc_portfolio_<?php echo esc_attr( $trx_addons_portfolio_type ); ?>">
		<?php
		if ( $trx_addons_portfolio_masonry ) {
			?>
			<div class="sc_portfolio_masonry_wrap <?php
				echo esc_attr( trx_addons_add_columns_in_single_row( $trx_addons_portfolio_columns ) );
				?>"
				data-gallery="<?php echo esc_attr( (int) $trx_addons_portfolio_gallery ); ?>"
			><?php
		} else {
			?>
			<div class="sc_portfolio_columns_wrap <?php
				echo esc_attr(trx_addons_get_columns_wrap_class())
					. ' columns_padding_bottom'
					. esc_attr( trx_addons_add_columns_in_single_row( $trx_addons_portfolio_columns ) );
				?>"
				data-gallery="<?php echo esc_attr( (int) $trx_addons_portfolio_gallery ); ?>"
			><?php
		}

			while ( have_posts() ) { the_post(); 
				trx_addons_get_template_part(array(
												TRX_ADDONS_PLUGIN_CPT . 'portfolio/tpl.'.trim($trx_addons_portfolio_type).'-item.php',
												TRX_ADDONS_PLUGIN_CPT . 'portfolio/tpl.default-item.php'
												),
												'trx_addons_args_sc_portfolio',
												array(
													'type' => $trx_addons_portfolio_type,
													'columns' => $trx_addons_portfolio_columns,
													'slider' => false,
													'use_masonry' => $trx_addons_portfolio_masonry,
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
