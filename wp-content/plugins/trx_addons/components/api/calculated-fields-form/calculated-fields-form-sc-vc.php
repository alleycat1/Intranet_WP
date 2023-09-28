<?php
/**
 * Plugin support: Calculated Fields Form (WPBakery support)
 *
 * @package ThemeREX Addons
 * @since v1.5
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

if ( ! function_exists( 'trx_addons_sc_calculated_fields_form_add_in_vc' ) ) {
	add_action('init', 'trx_addons_sc_calculated_fields_form_add_in_vc', 20);
	/**
	 * Add shortcode [CP_CALCULATED_FIELDS] to the VC shortcodes list
	 * 
	 * @hooked init, 20
	 */
	function trx_addons_sc_calculated_fields_form_add_in_vc() {

		if ( ! trx_addons_exists_vc() || ! trx_addons_exists_calculated_fields_form() ) {
			return;
		}

		vc_lean_map( "CP_CALCULATED_FIELDS", 'trx_addons_sc_calculated_fields_form_add_in_vc_params' );
		class WPBakeryShortCode_Cp_Calculated_Fields extends WPBakeryShortCode {}
	}
}

if ( ! function_exists( 'trx_addons_sc_calculated_fields_form_add_in_vc_params' ) ) {
	/**
	 * Return a list with parameters for shortcode [CP_CALCULATED_FIELDS] for VC
	 * 
	 * @trigger trx_addons_sc_map
	 *
	 * @return array  Shortcode parameters list in VC format
	 */
	function trx_addons_sc_calculated_fields_form_add_in_vc_params() {
		return apply_filters( 'trx_addons_sc_map', array(
				"base" => "CP_CALCULATED_FIELDS",
				"name" => esc_html__( "Calculated fields form", "trx_addons" ),
				"description" => esc_html__( "Insert Calculated Fields Form", "trx_addons" ),
				"category" => esc_html__( 'Content', 'trx_addons' ),
				'icon' => 'icon_trx_sc_calcfields',
				"class" => "trx_sc_single trx_sc_calcfields",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array(
					array(
						"param_name" => "id",
						"heading" => esc_html__( "Form ID", "trx_addons" ),
						"description" => esc_html__( "Select Form to insert to the current page", "trx_addons" ),
						"admin_label" => true,
				        'save_always' => true,
						"value" => array_flip( trx_addons_get_list_calculated_fields_form() ),
						"type" => "dropdown"
					)
				)
			), 'CP_CALCULATED_FIELDS' );
	}
}
