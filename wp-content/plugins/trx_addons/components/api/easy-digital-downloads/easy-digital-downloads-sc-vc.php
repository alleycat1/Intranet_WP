<?php
/**
 * Plugin support: Easy Digital Downloads (WPBakery support)
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

if ( ! function_exists( 'trx_addons_sc_edd_details_add_in_vc' ) ) {
	add_action( 'init', 'trx_addons_sc_edd_details_add_in_vc', 20 );
	/**
	 * Add shortcode [trx_sc_edd_details] to the VC shortcodes list
	 * 
	 * @hooked init, 20
	 */
	function trx_addons_sc_edd_details_add_in_vc() {
		if ( ! trx_addons_exists_vc() ) {
			return;
		}
		vc_lean_map( "trx_sc_edd_details", 'trx_addons_sc_edd_details_add_in_vc_params');
		class WPBakeryShortCode_Trx_Sc_Edd_Details extends WPBakeryShortCode {}
	}
}

if ( ! function_exists( 'trx_addons_sc_edd_details_add_in_vc_params' ) ) {
	/**
	 * Return shortcode's specific parameters for VC
	 * 
	 * @trigger trx_addons_sc_map
	 *
	 * @return array  Shortcode's parameters for VC
	 */
	function trx_addons_sc_edd_details_add_in_vc_params() {
		return apply_filters( 'trx_addons_sc_map', array(
				"base" => "trx_sc_edd_details",
				"name" => esc_html__("EDD Details", 'trx_addons'),
				"description" => wp_kses_data( __("Display current download's details", 'trx_addons') ),
				"category" => esc_html__('ThemeREX', 'trx_addons'),
				"icon" => 'icon_trx_sc_edd_details',
				"class" => "trx_sc_edd_details",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array_merge(
					array(
						array(
							"param_name" => "type",
							"heading" => esc_html__("Layout", 'trx_addons'),
							"description" => wp_kses_data( __("Select shortcode's layout", 'trx_addons') ),
							"admin_label" => true,
							"std" => "default",
					        'save_always' => true,
							"value" => array_flip(apply_filters('trx_addons_sc_type', array(
								'default' => esc_html__('Default', 'trx_addons')
							), 'trx_sc_edd_details')),
							"type" => "dropdown"
						)
					),
					trx_addons_vc_add_id_param()
				)
			), 'trx_sc_edd_details' );
	}
}


// trx_sc_edd_add_to_cart
//-------------------------------------------------------------

if ( ! function_exists( 'trx_addons_sc_edd_add_to_cart_add_in_vc' ) ) {
	add_action('init', 'trx_addons_sc_edd_add_to_cart_add_in_vc', 20);
	/**
	 * Add shortcode [trx_sc_edd_add_to_cart] to the VC shortcodes list
	 *
	 * @hooked init, 20
	 */
	function trx_addons_sc_edd_add_to_cart_add_in_vc() {
		if ( ! trx_addons_exists_vc() ) {
			return;
		}
		vc_lean_map( "trx_sc_edd_add_to_cart", 'trx_addons_sc_edd_add_to_cart_add_in_vc_params');
		class WPBakeryShortCode_Trx_Sc_Edd_Add_To_Cart extends WPBakeryShortCode {}
	}
}

if ( ! function_exists( 'trx_addons_sc_edd_add_to_cart_add_in_vc_params' ) ) {
	/**
	 * Return shortcode's specific parameters for VC
	 * 
	 * @trigger trx_addons_sc_map
	 *
	 * @return array  Shortcode's parameters for VC
	 */
	function trx_addons_sc_edd_add_to_cart_add_in_vc_params() {
		$list = trx_addons_get_list_posts( false, array(
														'post_type' => TRX_ADDONS_EDD_PT,
														'orderby' => 'title',
														'order' => 'ASC',
														'not_selected' => true
														) );
		return apply_filters( 'trx_addons_sc_map', array(
				"base" => "trx_sc_edd_add_to_cart",
				"name" => esc_html__("EDD Add to Cart", 'trx_addons'),
				"description" => wp_kses_data( __("Display 'Add to cart' block with current or specified download", 'trx_addons') ),
				"category" => esc_html__('ThemeREX', 'trx_addons'),
				"icon" => 'icon_trx_sc_edd_add_to_cart',
				"class" => "trx_sc_edd_add_to_cart",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array_merge(
					array(
						array(
							"param_name" => "type",
							"heading" => esc_html__("Layout", 'trx_addons'),
							"description" => wp_kses_data( __("Select shortcode's layout", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							"admin_label" => true,
							"std" => "default",
					        'save_always' => true,
							"value" => array_flip(apply_filters('trx_addons_sc_type', array(
								'default' => esc_html__('Default', 'trx_addons'),
								'promo' => esc_html__('Promo', 'trx_addons')
							), 'trx_sc_edd_add_to_cart')),
							"type" => "dropdown"
						),
						array(
							"param_name" => "download",
							"heading" => esc_html__("Download", 'trx_addons'),
							"description" => wp_kses_data( __("Select download to display 'Add to cart' block. If not selected - use current item (if we are on the single download page)", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							"admin_label" => true,
							'save_always' => true,
							"value" => array_flip($list),
							"type" => "dropdown"
						),
						array(
							'heading' => esc_html__( 'Info', 'trx_addons' ),
							"description" => wp_kses_data( __("Additional info after the price block", 'trx_addons') ),
							'param_name' => 'content',
							'value' => '',
							'holder' => 'div',
							'type' => 'textarea_html',
						)
					),
					trx_addons_vc_add_title_param(),
					trx_addons_vc_add_id_param()
				)
			), 'trx_sc_edd_add_to_cart' );
	}
}
