<?php
/**
 * Plugin support: Content Timeline (WPBakery support)
 *
 * @package ThemeREX Addons
 * @since v1.0
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

if ( ! function_exists( 'trx_addons_sc_content_timeline_add_in_vc' ) ) {
	add_action('init', 'trx_addons_sc_content_timeline_add_in_vc', 20);
	/**
	 * Add shortcode [content_timeline] to the VC shortcodes list
	 * 
	 * @hooked init, 20
	 */
	function trx_addons_sc_content_timeline_add_in_vc() {

		if ( ! trx_addons_exists_vc() || ! trx_addons_exists_content_timeline() ) {
			return;
		}

		vc_lean_map( "content_timeline", 'trx_addons_sc_content_timeline_add_in_vc_params');
		class WPBakeryShortCode_Content_Timeline extends WPBakeryShortCode {}
	}
}

if ( ! function_exists( 'trx_addons_sc_content_timeline_add_in_vc_params' ) ) {
	/**
	 * Return parameters for the shortcode [content_timeline] to use in VC
	 * 
	 * @trigger trx_addons_sc_map
	 *
	 * @return array  Shortcode parameters
	 */
	function trx_addons_sc_content_timeline_add_in_vc_params() {
		return apply_filters( 'trx_addons_sc_map', array(
				"base" => "content_timeline",
				"name" => esc_html__("Content Timeline", 'trx_addons'),
				"description" => esc_html__("Insert Content timeline", 'trx_addons'),
				"category" => esc_html__('Content', 'trx_addons'),
				'icon' => 'icon_trx_sc_content_timeline',
				"class" => "trx_sc_content_timeline",
				"show_settings_on_create" => true,
				"params" => array(
					array(
						"param_name" => "id",
						"heading" => esc_html__("Timeline", 'trx_addons'),
						"description" => esc_html__("Select Timeline to insert to the current page", 'trx_addons'),
						"admin_label" => true,
				        'save_always' => true,
						"value" => array_flip(trx_addons_get_list_content_timelines()),
						"type" => "dropdown"
					)
				)
			), 'content_timeline' );
	}
}
