<?php
/**
 * Plugin support: Give (WPBakery support)
 *
 * @package ThemeREX Addons
 * @since v1.6.50
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

if ( ! function_exists( 'trx_addons_sc_give_add_in_vc' ) ) {
	add_action( 'init', 'trx_addons_sc_give_add_in_vc', 20 );
	/**
	 * Add shortcode '[give_form]' to the VC shortcodes list.
	 */
	function trx_addons_sc_give_add_in_vc() {

		if ( ! trx_addons_exists_vc() || ! trx_addons_exists_give() ) {
			return;
		}

		vc_lean_map( "give_form", 'trx_addons_sc_give_add_in_vc_params' );
		class WPBakeryShortCode_Trx_Addons_Give_Form extends WPBakeryShortCode {}
	}
}

if ( ! function_exists( 'trx_addons_sc_give_add_in_vc_params' ) ) {
	/**
	 * Add shortcode's specific parameters to the VC shortcodes list for the shortcode '[give_form]'
	 * 
	 * @trigger trx_addons_sc_map
	 * 
	 * @return array  	 Shortcode's specific parameters
	 */
	function trx_addons_sc_give_add_in_vc_params() {
		return apply_filters( 'trx_addons_sc_map', array(
				"base" => "give_form",
				"name" => esc_html__("Give donation form", "trx_addons"),
				"description" => esc_html__("Insert Give Donation form", "trx_addons"),
				"category" => esc_html__('Content', 'trx_addons'),
				'icon' => 'icon_trx_sc_give_forms',
				"class" => "trx_sc_single trx_sc_give_forms",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array(
					array(
						"param_name" => "id",
						"heading" => esc_html__("Donation form", "trx_addons"),
						"description" => esc_html__("Select Form to insert to the current page", "trx_addons"),
						"admin_label" => true,
				        'save_always' => true,
						"value" => array_flip( trx_addons_get_list_give_forms() ),
						"type" => "dropdown"
					)
				)
			), 'trx_addons_give_form' );
			
	}
}
