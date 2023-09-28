<?php
/**
 * Widget "WooCommerce Title" - default layout
 *
 * @package ThemeREX Addons
 * @since v1.90.0
 */

$trx_addons_args = get_query_var( 'trx_addons_args_widget_woocommerce_title' );
extract( $trx_addons_args );

// Before widget (defined by themes)
trx_addons_show_layout( $before_widget );
	
// Widget body
?><div <?php if ( ! empty( $trx_addons_args['id'] ) ) echo ' id="' . esc_attr( $trx_addons_args['id'] ) . '"'; ?>
	class="trx_addons_woocommerce_title<?php 
		if ( ! empty( $trx_addons_args['class'] ) ) echo ' ' . esc_attr( $trx_addons_args['class'] );
	?>"<?php
	if ( ! empty( $trx_addons_args['css'] ) ) echo ' style="' . esc_attr( $trx_addons_args['css'] ) . '"'; 
?>><?php

	foreach( $is_archive ? $archive : $single as $item ) {
		// Breadcrumbs
		if ( $item == 'breadcrumbs' ) {
			if ( function_exists( 'woocommerce_breadcrumb' )
					&& apply_filters( 'trx_addons_filter_woocommerce_title_show_breadcrumbs',
						( is_array( $trx_addons_args['archive'] ) && in_array( 'breadcrumbs', $trx_addons_args['archive'] ) && $is_archive )
						||
						( is_array( $trx_addons_args['single'] ) && in_array( 'breadcrumbs', $trx_addons_args['single'] ) && $is_single ),
						$trx_addons_args
						)
			) {
				woocommerce_breadcrumb();
				$widget->set_breadcrumbs_showed();
			}

		// Title
		} else if ( $item == 'title' ) {

			if ( function_exists( 'woocommerce_page_title' )
					&& function_exists( 'woocommerce_template_single_title' )
					&& apply_filters( 'trx_addons_filter_woocommerce_title_show_title',
						( is_array( $trx_addons_args['archive'] ) && in_array( 'title', $trx_addons_args['archive'] ) && $is_archive )
						||
						( is_array( $trx_addons_args['single'] ) && in_array( 'title', $trx_addons_args['single'] ) && $is_single ),
						$trx_addons_args
						)
			) {
				if ( $is_archive ) {
					?><h1 class="woocommerce-products-header__title page-title"><?php woocommerce_page_title(); ?></h1><?php
					$widget->set_title_showed();
				} else if ( $is_single ) {
					woocommerce_template_single_title();
					$widget->set_title_showed();
				}
			}

		// Description
		} else if ( $item == 'description' ) {
			if ( apply_filters( 'trx_addons_filter_woocommerce_title_show_description',
					( is_array( $trx_addons_args['archive'] ) && in_array( 'description', $trx_addons_args['archive'] ) && $is_archive )
					||
					( is_array( $trx_addons_args['single'] ) && in_array( 'description', $trx_addons_args['single'] ) && $is_single ),
					$trx_addons_args
				)
			) {
				if ( $is_archive ) {
					do_action( 'woocommerce_archive_description' );
				} else {
					do_action( 'woocommerce_single_description' );
				}
				$widget->set_description_showed();
			}
		}
	}

?></div><?php

// After widget (defined by themes)
trx_addons_show_layout($after_widget);
