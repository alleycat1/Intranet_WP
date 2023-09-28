<?php
/**
 * Widget: Cars Sort (WPBakery support)
 *
 * @package ThemeREX Addons
 * @since v1.6.25
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}



// Add [trx_widget_cars_sort] in the VC shortcodes list
if (!function_exists('trx_addons_sc_widget_cars_sort_add_in_vc')) {
	function trx_addons_sc_widget_cars_sort_add_in_vc() {
		
		if (!trx_addons_exists_vc()) return;
		
		vc_lean_map("trx_widget_cars_sort", 'trx_addons_sc_widget_cars_sort_add_in_vc_params');
		class WPBakeryShortCode_Trx_Widget_Cars_Sort extends WPBakeryShortCode {}
	}
	add_action('init', 'trx_addons_sc_widget_cars_sort_add_in_vc', 20);
}

// Return params
if (!function_exists('trx_addons_sc_widget_cars_sort_add_in_vc_params')) {
	function trx_addons_sc_widget_cars_sort_add_in_vc_params() {
		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_widget_cars_sort",
				"name" => esc_html__("Cars Sort", 'trx_addons'),
				"description" => wp_kses_data( __("Insert widget to sort cars by price, date or title", 'trx_addons') ),
				"category" => esc_html__('ThemeREX', 'trx_addons'),
				"icon" => 'icon_trx_widget_cars_sort',
				"class" => "trx_widget_cars_sort",
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
							"description" => wp_kses_data( __("Select the sorting type for cars list", 'trx_addons') ),
							"std" => "date",
							'save_always' => true,
							"value" => array_flip(trx_addons_get_list_sc_query_orderby('', 'date,price,title')),
							"type" => "dropdown"
						),
						array(
							"param_name" => "order",
							"heading" => esc_html__("Order", 'trx_addons'),
							"description" => wp_kses_data( __("Select the sorting order for cars list", 'trx_addons') ),
							"std" => "desc",
							'save_always' => true,
							"value" => array_flip(trx_addons_get_list_sc_query_orders()),
							"type" => "dropdown"
						)
					),
					trx_addons_vc_add_id_param()
				)
			), 'trx_widget_cars_sort' );
	}
}
