<?php
/**
 * Widget: Downloads Search (Advanced search form) (WPBakery support)
 *
 * @package ThemeREX Addons
 * @since v1.6.34
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

if ( ! function_exists( 'trx_addons_sc_widget_edd_search_add_in_vc' ) ) {
	add_action( 'init', 'trx_addons_sc_widget_edd_search_add_in_vc', 20 );
	/**
	 * Add shortcode [trx_widget_edd_search] to the VC shortcodes list
	 * 
	 * @hooked init, 20
	 */
	function trx_addons_sc_widget_edd_search_add_in_vc() {
		if ( ! trx_addons_exists_vc() ) {
			return;
		}
		vc_lean_map("trx_widget_edd_search", 'trx_addons_sc_widget_edd_search_add_in_vc_params');
		class WPBakeryShortCode_Trx_Widget_edd_search extends WPBakeryShortCode {}
	}
}

if ( ! function_exists( 'trx_addons_sc_widget_edd_search_add_in_vc_params' ) ) {
	/**
	 * Return parameters for [trx_widget_edd_search] in the VC format
	 * 
	 * @trigger trx_addons_sc_map
	 *
	 * @return array  Parameters for shortcode in VC format
	 */
	function trx_addons_sc_widget_edd_search_add_in_vc_params() {
		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_widget_edd_search",
				"name" => esc_html__("Themes Search", 'trx_addons'),
				"description" => wp_kses_data( __("Insert advanced form for search themes", 'trx_addons') ),
				"category" => esc_html__('ThemeREX', 'trx_addons'),
				"icon" => 'icon_trx_widget_edd_search',
				"class" => "trx_widget_edd_search",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array_merge(
					array(
						array(
							"param_name" => "title",
							"heading" => esc_html__("Widget title", 'trx_addons'),
							"description" => wp_kses_data( __("Title of the widget", 'trx_addons') ),
							"admin_label" => true,
							"type" => "textfield"
						),
						array(
							"param_name" => "orderby",
							"heading" => esc_html__("Order by", 'trx_addons'),
							"description" => wp_kses_data( __("Select sorting type of search results", 'trx_addons') ),
							"std" => "date",
							'save_always' => true,
							"value" => array_flip(trx_addons_get_list_sc_query_orderby('', 'date,price,title,rand')),
							"type" => "dropdown"
						),
						array(
							"param_name" => "order",
							"heading" => esc_html__("Order", 'trx_addons'),
							"description" => wp_kses_data( __("Select sorting order of search results", 'trx_addons') ),
							"std" => "desc",
							'save_always' => true,
							"value" => array_flip(trx_addons_get_list_sc_query_orders()),
							"type" => "dropdown"
						)
					),
					trx_addons_vc_add_id_param()
				)
			), 'trx_widget_edd_search' );
	}
}
