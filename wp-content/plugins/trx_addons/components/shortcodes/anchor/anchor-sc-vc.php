<?php
/**
 * Shortcode: Anchor (WPBakery support)
 *
 * @package ThemeREX Addons
 * @since v1.2
 */

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }



// Add [trx_sc_anchor] in the VC shortcodes list
if (!function_exists('trx_addons_sc_anchor_add_in_vc')) {
	function trx_addons_sc_anchor_add_in_vc() {

		if (!trx_addons_exists_vc()) return;
		
		vc_lean_map("trx_sc_anchor", 'trx_addons_sc_anchor_add_in_vc_params');
		class WPBakeryShortCode_Trx_Sc_Anchor extends WPBakeryShortCode {}
	}
	add_action('init', 'trx_addons_sc_anchor_add_in_vc', 20);
}

// Return params
if (!function_exists('trx_addons_sc_anchor_add_in_vc_params')) {
	function trx_addons_sc_anchor_add_in_vc_params() {
		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_sc_anchor",
				"name" => esc_html__("Anchor", 'trx_addons'),
				"description" => wp_kses_data( __("Insert anchor for the inner page navigation", 'trx_addons') ),
				"category" => esc_html__('ThemeREX', 'trx_addons'),
				"icon" => 'icon_trx_sc_anchor',
				"class" => "trx_sc_anchor",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array_merge( array(
					array(
						"param_name" => "id",
						"heading" => esc_html__("Anchor ID", 'trx_addons'),
						"description" => wp_kses_data( __("ID of this anchor", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-6',
						"admin_label" => true,
						"type" => "textfield"
					), 
					array(
						'param_name' => 'title',
						'heading' => esc_html__( 'Title', 'trx_addons' ),
						'description' => esc_html__( 'Anchor title', 'trx_addons' ),
						'edit_field_class' => 'vc_col-sm-6',
						'type' => 'textfield',
					),
					array(
						'param_name' => 'url',
						'heading' => esc_html__( 'URL to navigate', 'trx_addons' ),
						'description' => esc_html__( "URL to navigate. If empty - use id to create anchor", 'trx_addons' ),
						'type' => 'textfield',
					 ) ),

					trx_addons_vc_add_icon_param('')
				)
			), 'trx_sc_anchor' );
	}
}
