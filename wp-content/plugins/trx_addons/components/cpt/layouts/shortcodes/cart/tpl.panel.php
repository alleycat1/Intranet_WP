<?php
/**
 * The style "panel" of the Cart
 *
 * @package ThemeREX Addons
 * @since v1.95.0
 */

$args = get_query_var( 'trx_addons_args_sc_layouts_cart' );

$show_cart = trx_addons_is_preview( 'elementor' ) && get_post_type() == TRX_ADDONS_CPT_LAYOUTS_PT ? 'preview' : '';

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
	$panel_id = $args['type']  == 'panel'
					? ( ! empty( $args['id'] )
						? $args['id'] . '_panel'
						: 'sc_layouts_cart_panel_' . mt_rand()
						)
					: '';
	$panel_link_class = ! empty( $panel_id ) ? ' trx_addons_panel_link' : '';
	$panel_link_data  = ! empty( $panel_id ) ? ' data-panel-id="' . esc_attr( $panel_id ) . '"' : '';

	?><div<?php if ( ! empty( $args['id'] ) ) echo ' id="' . esc_attr( $args['id'] ) . '"'; ?> class="sc_layouts_cart<?php
			trx_addons_cpt_layouts_sc_add_classes( $args );
		?>"<?php
		if ( ! empty( $args['css'] ) ) echo ' style="' . esc_attr( $args['css'] ) . '"';
		trx_addons_sc_show_attributes( 'sc_layouts_cart', $args, 'sc_wrapper' );
	?>>
		<span class="sc_layouts_item_icon sc_layouts_cart_icon sc_icons_type_icons trx_addons_icon-basket<?php echo esc_attr( $panel_link_class ) ?>"<?php
			echo wp_kses( $panel_link_data, 'trx_addons_kses_content' );
		?>></span>
		<span class="sc_layouts_item_details sc_layouts_cart_details<?php echo esc_attr( $panel_link_class ) ?>"<?php
			echo wp_kses( $panel_link_data, 'trx_addons_kses_content' );
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

		// If 'type' == 'panel'
		if ( $args['type']  == 'panel' ) {
			ob_start();
		}
		?><div class="<?php echo ! empty( $panel_id ) ? 'sc_layouts_cart_panel_widget' : 'sc_layouts_cart_widget'; ?> widget_area"><?php
			// Show panel header
			if ( ! empty( $panel_id ) ) {
				?><div class="sc_layouts_cart_panel_header"><?php
					?><h5 class="sc_layouts_cart_panel_title"><?php
						?><span class="sc_layouts_cart_panel_title_text"><?php esc_html_e( 'Cart', 'trx_addons' ); ?></span><?php
						?><span class="sc_layouts_cart_items_short"><?php echo esc_html( $cart_items ); ?></span><?php
					?></h5><?php
				?></div><?php
			}
			// Show WooCommerce Cart
			do_action( 'trx_addons_action_before_cart', $show_cart, $args );
			if ($show_cart == 'woocommerce') {
				the_widget( 'WC_Widget_Cart', 'title=&hide_if_empty=0' );

			// Show EDD Cart
			} else if ($show_cart == 'edd') {
				the_widget( 'edd_cart_widget', 'title=&hide_on_checkout=0&hide_on_empty=0' );

			// Show preview Cart
			} else {
				?><div class="sc_layouts_cart_preview"><?php esc_html_e( 'Placeholder for Cart items', 'trx_addons' ); ?></div><?php
			}
			do_action( 'trx_addons_action_after_cart', $show_cart, $args );
		?></div><?php

		// If 'type' == 'panel'
		if ( $args['type'] == 'panel' ) {
			$output = ob_get_contents();
			ob_end_clean();
			trx_addons_sc_layouts( apply_filters( 'trx_addons_filter_sc_layouts_cart_panel_args', array(
				'type' => 'panel',
				'size' => 440,
				'effect' => 'slide',		// slide | flip | flipout
				"position" => "right",		// left | right
				"modal" => 1,				// 0 | 1
				"shift_page" => 0,			// 0 | 1
				'content' => $output,
				'id' => $panel_id,
				'class' => 'sc_layouts_cart_panel'
			) ) );
		}
	?></div><?php

	trx_addons_sc_layouts_showed('cart', true);
}
