<?php
/**
 * The style "button" of the Cart
 *
 * @package ThemeREX Addons
 * @since v1.95.0
 */

$args = get_query_var('trx_addons_args_sc_layouts_cart');

$show_cart = trx_addons_is_preview( 'elementor' ) && get_post_type()==TRX_ADDONS_CPT_LAYOUTS_PT ? 'preview' : '';

$cart_items = $cart_summa = 0;

if ( empty( $show_cart ) ) {
	// If it's a WooCommerce Cart
	if ( $args['market'] == 'woocommerce' && trx_addons_exists_woocommerce() && ! is_cart() && ! is_checkout() && ! empty( WC()->cart ) ) {
		$cart_items = WC()->cart->get_cart_contents_count();
		$cart_summa = strip_tags( WC()->cart->get_cart_subtotal() );
		$show_cart = 'woocommerce';

	// If it's a EDD Cart
	} else if ( $args['market'] == 'edd' && trx_addons_exists_edd() ) {
		$cart_items = edd_get_cart_quantity();
		$cart_summa = edd_currency_filter( edd_format_amount( edd_get_cart_total() ) );
		$show_cart = 'edd';
	}
}

if ( ! empty( $show_cart ) ) {

	$button_id = $args['type']  == 'button'
					? ( ! empty( $args['id'] )
						? $args['id'] . '_button'
						: 'sc_layouts_cart_button_' . mt_rand()
						)
					: '';
	$button_link_data  = ! empty( $button_id ) ? ' data-button-id="' . esc_attr( $button_id ) . '"' : '';
	$cart_url = $show_cart == 'woocommerce' && function_exists( 'wc_get_cart_url' )
					? wc_get_cart_url()
					: '';

	?><div<?php if ( ! empty( $args['id'] ) ) echo ' id="' . esc_attr( $args['id'] ) . '"'; ?> class="sc_layouts_cart<?php
				trx_addons_cpt_layouts_sc_add_classes( $args );
		?>"<?php
		if ( ! empty( $args['css'] ) ) echo ' style="' . esc_attr( $args['css'] ) . '"';
		trx_addons_sc_show_attributes( 'sc_layouts_cart', $args, 'sc_wrapper' );
	?>><?php

		if ( ! empty( $cart_url ) ) {
			?><a href="<?php echo esc_url( $cart_url ); ?>" class="sc_layouts_cart_link"><?php
		}

		?><span class="sc_layouts_item_icon sc_layouts_cart_icon sc_icons_type_icons trx_addons_icon-basket"<?php
			echo wp_kses( $button_link_data, 'trx_addons_kses_content' );
		?>></span>
		<span class="sc_layouts_item_details sc_layouts_cart_details"<?php
			echo wp_kses( $button_link_data, 'trx_addons_kses_content' );
		?>>
			<?php if ( ! empty( $args['text'] ) ) { ?>
			<span class="sc_layouts_item_details_line1 sc_layouts_cart_label"><?php echo esc_html( $args['text'] ); ?></span>
			<?php } ?>
			<span class="sc_layouts_item_details_line2 sc_layouts_cart_totals">
				<span class="sc_layouts_cart_items" data-item="<?php esc_attr_e( 'item', 'trx_addons' ); ?>" data-items="<?php esc_attr_e( 'items', 'trx_addons' ); ?>"><?php
					echo esc_html( $cart_items ) . ' ' . esc_html( _n( 'item', 'items', $cart_items, 'trx_addons' ) );
				?></span>
				- 
				<span class="sc_layouts_cart_summa"><?php trx_addons_show_layout( $cart_summa ); ?></span>
			</span>
		</span>
		<span class="sc_layouts_cart_items_short"><?php echo esc_html( $cart_items ); ?></span><?php

		if ( ! empty( $cart_url ) ) {
			?></a><?php
		}

		// If 'type' == 'button'
		if ( $args['type']  == 'button' ) {
			ob_start();
		}

		?><div class="sc_layouts_cart_widget widget_area">
			<span class="sc_layouts_cart_widget_close trx_addons_button_close"><span class="sc_layouts_cart_widget_close_icon trx_addons_button_close_icon"></span></span>
			<?php
			// Show WooCommerce Cart
			do_action( 'trx_addons_action_before_cart', $show_cart, $args );
			if ( $show_cart == 'woocommerce' ) {
				the_widget( 'WC_Widget_Cart', 'title=&hide_if_empty=0' );

			// Show EDD Cart
			} else if ( $show_cart == 'edd' ) {
				the_widget( 'edd_cart_widget', 'title=&hide_on_checkout=0&hide_on_empty=0' );

			// Show preview Cart
			} else {
				?><div class="sc_layouts_cart_preview"><?php esc_html_e('Placeholder for Cart items'); ?></div><?php
			}
			do_action( 'trx_addons_action_after_cart', $show_cart, $args );
		?></div><?php

		// If 'type' == 'button'
		if ( $args['type'] == 'button' ) {
			$output = ob_get_contents();
			ob_end_clean();
			$output = apply_filters( 'trx_addons_filter_sc_layouts_cart_button_html',
							sprintf( '<div id="%1$s" class="sc_layouts_cart_button_wrap">'
										. '<span class="sc_layouts_cart_button_sonar"></span>'
										. ( ! empty( $cart_url ) ? '<a href="' . esc_url( $cart_url ) . '"' : '<span' ) . ' class="sc_layouts_cart_button">'
											. '<span class="sc_layouts_cart_button_icon sc_icons_type_icons trx_addons_icon-basket"></span>'
											. '<span class="sc_layouts_cart_items_short">%2$d</span>'
										. ( ! empty( $cart_url ) ? '</a>' : '</span>' )
										. '%3$s'
									. '</div>',
									esc_attr( $button_id ),
									esc_html( $cart_items ),
									$output
							),
							$args
						);
			trx_addons_add_inline_html( $output );
		}

	?></div><?php

	trx_addons_sc_layouts_showed('cart', true);
}
