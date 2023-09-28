<?php
/**
 * Plugin support: Easy Digital Downloads (Shortcodes)
 *
 * @package ThemeREX Addons
 * @since v1.6.29
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


// trx_sc_edd_details
//-------------------------------------------------------------

if ( ! function_exists( 'trx_addons_sc_edd_details' ) ) {
	/**
	 * Shortcode [trx_sc_edd_details] to display EDD product details
	 * 
	 * @trigger trx_addons_sc_output
	 *
	 * @param array $atts Shortcode attributes
	 * @param string $content Shortcode content
	 * 
	 * @return string Shortcode output
	 */
	function trx_addons_sc_edd_details( $atts, $content = null ) {	
		$atts = trx_addons_sc_prepare_atts( 'trx_sc_edd_details', $atts, trx_addons_sc_common_atts( 'id', array(
			// Individual params
			"type" => "default",
		) ) );

		$atts['class'] .= ($atts['class'] ? ' ' : '') . 'sc_edd_details';

		$output = '';
		if ( trx_addons_is_single() && get_post_type() == TRX_ADDONS_EDD_PT ) {
			ob_start();
			trx_addons_get_template_part( TRX_ADDONS_PLUGIN_API . 'easy-digital-downloads/tpl.edd-details.' . trx_addons_esc( $atts['type'] ) . '.php',
										'trx_addons_args_sc_edd_details',
										$atts
										);
			$output = ob_get_contents();
			ob_end_clean();
		}
		
		return apply_filters( 'trx_addons_sc_output', $output, 'trx_sc_edd_details', $atts, $content );
	}
}

if ( ! function_exists( 'trx_addons_sc_edd_details_add_shortcode' ) ) {
	add_action( 'init', 'trx_addons_sc_edd_details_add_shortcode', 20 );
	/**
	 * Add shortcode [trx_sc_edd_details]
	 * 
	 * @hooked init, 20
	 */
	function trx_addons_sc_edd_details_add_shortcode() {
		add_shortcode( "trx_sc_edd_details", "trx_addons_sc_edd_details" );
	}
}


// trx_sc_edd_add_to_cart
//-------------------------------------------------------------

if ( ! function_exists( 'trx_addons_sc_edd_add_to_cart' ) ) {
	/**
	 * Shortcode [trx_sc_edd_add_to_cart] to display EDD product add to cart button
	 * 
	 * @trigger trx_addons_sc_output
	 *
	 * @param array $atts Shortcode attributes
	 * @param string $content Shortcode content
	 * 
	 * @return string Shortcode output
	 */
	function trx_addons_sc_edd_add_to_cart( $atts, $content = null ) {	
		$atts = trx_addons_sc_prepare_atts( 'trx_sc_edd_add_to_cart', $atts, trx_addons_sc_common_atts( 'id,title', array(
			// Individual params
			"type" => "default",
			"download" => 0,
			"content" => "",
		) ) );
		$output = '';
		if ( $atts['download'] > 0 || trx_addons_is_single() && get_post_type() == TRX_ADDONS_EDD_PT ) {

			if ( empty( $atts['content'] ) && ! empty( $content ) ) {
				$atts['content'] = do_shortcode( $content );
			}
			$atts['class'] .= ( $atts['class'] ? ' ' : '' ) . 'sc_edd_add_to_cart sc_edd_add_to_cart_' . esc_attr( $atts['type'] );

			ob_start();
			trx_addons_get_template_part( TRX_ADDONS_PLUGIN_API . 'easy-digital-downloads/tpl.edd-add-to-cart.' . trx_addons_esc( $atts['type'] ) . '.php',
										'trx_addons_args_sc_edd_add_to_cart',
										$atts
										);
			$output = ob_get_contents();
			ob_end_clean();
		}
		
		return apply_filters( 'trx_addons_sc_output', $output, 'trx_sc_edd_add_to_cart', $atts, $content );
	}
}

if ( ! function_exists( 'trx_addons_sc_edd_add_to_cart_add_shortcode' ) ) {
	add_action( 'init', 'trx_addons_sc_edd_add_to_cart_add_shortcode', 20 );
	/**
	 * Add shortcode [trx_sc_edd_add_to_cart]
	 * 
	 * @hooked init, 20
	 */
	function trx_addons_sc_edd_add_to_cart_add_shortcode() {
		add_shortcode("trx_sc_edd_add_to_cart", "trx_addons_sc_edd_add_to_cart");
	}
}
