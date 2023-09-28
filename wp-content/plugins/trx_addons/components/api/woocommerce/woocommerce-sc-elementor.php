<?php
/**
 * Plugin support: WooCommerce (Elementor support)
 *
 * @package ThemeREX Addons
 * @since v1.6.52.2
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

if ( ! function_exists( 'trx_addons_woocommerce_elm_widgets_class' ) ) {
	add_filter( 'elementor/widget/render_content', 'trx_addons_woocommerce_elm_widgets_class', 10, 2 );
	/**
	 * Add class 'woocommerce' to the Elementor's widgets
	 * 
	 * @hooked elementor/widget/render_content
	 *
	 * @param string $content  Widget content
	 * @param object $widget   Widget object
	 * 
	 * @return string  	  Modified content
	 */
	function trx_addons_woocommerce_elm_widgets_class( $content, $widget = null ) {
		if ( is_object( $widget ) && strpos( $widget->get_name(), 'wp-widget-woocommerce' ) !== false ) {
			$content = str_replace( 'class="widget wp-widget-woocommerce', 'class="widget woocommerce wp-widget-woocommerce', $content );
		}
		return $content;
	}
}

// Add featured image output to the Elementor's edit mode
// ( by default, WooCommerce is not include file wc-template-hooks.php while Elementor loading a preview area )
// Commented, because a complete frontend hack is applied (see below)
//---------------------------------------------------------------------------------------
/*
if ( ! function_exists( 'trx_addons_woocommerce_elm_add_product_thumbnails_in_edit_mode')) {
	add_action( 'woocommerce_before_shop_loop_item_title', 'trx_addons_woocommerce_elm_add_product_thumbnails_in_edit_mode', 1 );
	function trx_addons_woocommerce_elm_add_product_thumbnails_in_edit_mode() {
		if ( trx_addons_elm_is_preview()
			&& ! has_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail' )
			&& function_exists( 'woocommerce_template_loop_product_thumbnail' )
		) {
			add_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );
		}
	}
}
*/

if ( ! function_exists( 'trx_addons_woocommerce_elm_load_frontend_includes_in_edit_mode')) {
	add_action( 'init', 'trx_addons_woocommerce_elm_load_frontend_includes_in_edit_mode' );
	/**
	 * Hack for WooCommerce in Elementor: if any WooCommerce shortcode are present on the page
	 * and user reload this page in the Elemetor Editor ( press F5 ) - shortcode layout are not complete
	 * because Elementor Editor is an admin area and WooCommerce do not include their hooks in this mode
	 * to show title, price, etc.
	 * 
	 * @hooked init
	 */
	function trx_addons_woocommerce_elm_load_frontend_includes_in_edit_mode() {
		static $loaded = false;
		if ( ! $loaded
			&& trx_addons_elm_is_edit_mode()
			&& trx_addons_exists_woocommerce()
			&& function_exists( 'WC' )
			&& ! isset( $GLOBALS['wp_filter']['woocommerce_shop_loop_item_title'] )
		) {
			$loaded = true;
			WC()->frontend_includes();
		}
	}
}
