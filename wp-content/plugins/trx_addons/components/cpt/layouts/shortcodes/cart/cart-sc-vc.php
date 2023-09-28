<?php
/**
 * Shortcode: Display WooCommerce cart with items number and totals (WPBakery support)
 *
 * @package ThemeREX Addons
 * @since v1.6.08
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


// Add [trx_sc_layouts_cart] in the VC shortcodes list
if (!function_exists('trx_addons_sc_layouts_cart_add_in_vc')) {
	function trx_addons_sc_layouts_cart_add_in_vc() {
		
		if (!trx_addons_cpt_layouts_sc_required()) return;

		if (!trx_addons_exists_vc()) return;
	
		vc_lean_map("trx_sc_layouts_cart", 'trx_addons_sc_layouts_cart_add_in_vc_params');
		class WPBakeryShortCode_Trx_Sc_Layouts_Cart extends WPBakeryShortCode {}

	}
	add_action('init', 'trx_addons_sc_layouts_cart_add_in_vc', 15);
}

// Return params
if (!function_exists('trx_addons_sc_layouts_cart_add_in_vc_params')) {
	function trx_addons_sc_layouts_cart_add_in_vc_params() {
		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_sc_layouts_cart",
				"name" => esc_html__("Layouts: Cart", 'trx_addons'),
				"description" => wp_kses_data( __("Insert cart with items number and totals to the custom layout", 'trx_addons') ),
				"category" => esc_html__('Layouts', 'trx_addons'),
				"icon" => 'icon_trx_sc_layouts_cart',
				"class" => "trx_sc_layouts_cart",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array_merge(
					array(
						array(
							"param_name" => "type",
							"heading" => esc_html__("Layout", 'trx_addons'),
							"description" => wp_kses_data( __("Select shortcodes's layout", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							"value" => array_flip( apply_filters( 'trx_addons_sc_type', trx_addons_get_list_sc_layouts_cart_types(), 'trx_sc_layouts_cart' ) ),
							"std" => "default",
							"type" => "dropdown"
						),
						array(
							"param_name" => "market",
							"heading" => esc_html__("Market", 'trx_addons'),
							"description" => wp_kses_data( __("Select e-commerce plugin to show cart", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							"admin_label" => true,
							"value" => array_flip(apply_filters('trx_addons_sc_cart_market', array(
								'woocommerce' => esc_html__('WooCommerce', 'trx_addons'),
							), 'trx_sc_layouts_cart')),
							"std" => "woocommerce",
							"type" => "dropdown"
						),
						array(
							"param_name" => "text",
							"heading" => esc_html__("Cart text", 'trx_addons'),
							"description" => wp_kses_data( __("Text before cart", 'trx_addons') ),
							"admin_label" => true,
							"value" => "",
							"type" => "textfield"
						)
					),
					trx_addons_vc_add_hide_param(),
					trx_addons_vc_add_id_param()
				)
			), 'trx_sc_layouts_cart');
	}
}
