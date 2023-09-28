<?php
/**
 * Widget: WooCommerce Search (Advanced search form) (WPBakery support)
 *
 * @package ThemeREX Addons
 * @since v1.6.38
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

if ( ! function_exists( 'trx_addons_sc_widget_woocommerce_search_add_in_vc' ) ) {
	add_action('init', 'trx_addons_sc_widget_woocommerce_search_add_in_vc', 20);
	/**
	 * Add [trx_widget_woocommerce_search] to the VC shortcodes list
	 */
	function trx_addons_sc_widget_woocommerce_search_add_in_vc() {
		if ( ! trx_addons_exists_woocommerce() ) {
			return;
		}
		if ( ! trx_addons_exists_vc() ) {
			return;
		}
		vc_lean_map( "trx_widget_woocommerce_search", 'trx_addons_sc_widget_woocommerce_search_add_in_vc_params' );
		class WPBakeryShortCode_Trx_Widget_Woocommerce_Search extends WPBakeryShortCode {}
	}
}

if ( ! function_exists( 'trx_addons_sc_widget_woocommerce_search_add_in_vc_params' ) ) {
	/**
	 * Return parameters for shortcode [trx_widget_woocommerce_search] in the VC format
	 * 
	 * @trigger trx_addons_sc_map
	 *
	 * @return array Parameters for shortcode
	 */
	function trx_addons_sc_widget_woocommerce_search_add_in_vc_params() {
		return apply_filters( 'trx_addons_sc_map', array(
				"base" => "trx_widget_woocommerce_search",
				"name" => esc_html__("WooCommerce Search", 'trx_addons'),
				"description" => wp_kses_data( __("Insert advanced form for search products", 'trx_addons') ),
				"category" => esc_html__('ThemeREX', 'trx_addons'),
				"icon" => 'icon_trx_widget_woocommerce_search',
				"class" => "trx_widget_woocommerce_search",
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
							'edit_field_class' => 'vc_col-sm-6',
							"type" => "textfield"
						),
						array(
							"param_name" => "type",
							"heading" => esc_html__("Type", 'trx_addons'),
							"description" => wp_kses_data( __("Type of the widget", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							"std" => "inline",
							"value" => array_flip(trx_addons_get_list_woocommerce_search_types()),
							"type" => "dropdown"
						),
						array(
							"param_name" => "ajax",
							"heading" => esc_html__("Use AJAX to reload products", 'trx_addons'),
							'description' => wp_kses_data( __("Use AJAX to refresh the product list in the background instead of reloading the entire page.", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							"std" => "1",
							"value" => array(esc_html__("Use AJAX", 'trx_addons') => "1" ),
							'dependency' => array(
								'element' => 'type',
								'value' => 'filter'
							),
							"type" => "checkbox"
						),
						array(
							"param_name" => "apply",
							"heading" => esc_html__('Use "Apply" Button for Filtering', 'trx_addons'),
							'description' => wp_kses_data( __("Select multiple filter values without the page reloading.", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							"std" => "1",
							"value" => array(esc_html__('Use "Apply" Button', 'trx_addons') => "1" ),
							'dependency' => array(
								'element' => 'type',
								'value' => 'filter'
							),
							"type" => "checkbox"
						),
						array(
							"param_name" => "force_checkboxes",
							"heading" => esc_html__('Simple view', 'trx_addons'),
							'description' => wp_kses_data( __('Display colors, images and buttons as checkboxes.', 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							"std" => "1",
							"value" => array(esc_html__('Simple fields', 'trx_addons') => "1" ),
							'dependency' => array(
								'element' => 'type',
								'value' => 'filter'
							),
							"type" => "checkbox"
						),
						array(
							"param_name" => "show_counters",
							"heading" => esc_html__('Show counters', 'trx_addons'),
							'description' => wp_kses_data( __("Show product counters after each item.", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							"std" => "1",
							"value" => array(esc_html__('Show counters', 'trx_addons') => "1" ),
							'dependency' => array(
								'element' => 'type',
								'value' => 'filter'
							),
							"type" => "checkbox"
						),
						array(
							"param_name" => "show_selected",
							"heading" => esc_html__('Show selected items', 'trx_addons'),
							'description' => wp_kses_data( __('Show selected items counter and "Clear all" button.', 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							"std" => "1",
							"value" => array(esc_html__('Show selected', 'trx_addons') => "1" ),
							'dependency' => array(
								'element' => 'type',
								'value' => 'filter'
							),
							"type" => "checkbox"
						),
						array(
							"param_name" => "expanded",
							"heading" => esc_html__('Initial toggle state', 'trx_addons'),
							'description' => esc_html__('For sidebar placement ONLY!', 'trx_addons'),
							'edit_field_class' => 'vc_col-sm-6',
							"value" => array_flip(trx_addons_get_list_woocommerce_search_expanded()),
							"std" => "0",
							'dependency' => array(
								'element' => 'type',
								'value' => 'filter'
							),
							"type" => "dropdown"
						),
						array(
							"param_name" => "autofilters",
							"heading" => esc_html__("Auto filters in categories", 'trx_addons'),
							'description' => wp_kses_data( __("Use product attributes as filters for current category.", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							"std" => "0",
							"value" => array(esc_html__("Auto filters", 'trx_addons') => "1" ),
							'dependency' => array(
								'element' => 'type',
								'value' => 'filter'
							),
							"type" => "checkbox"
						),
						array(
							'type' => 'param_group',
							'param_name' => 'fields',
							'heading' => esc_html__( 'Fields', 'trx_addons' ),
							"description" => wp_kses_data( __("Specify text and select filter for each item", 'trx_addons') ),
							'value' => urlencode( json_encode( apply_filters('trx_addons_sc_param_group_value', array(
											array(
												'text' => '',
												'filter' => ''
											),
										), 'trx_widget_woocommerce_search') ) ),
							'params' => apply_filters('trx_addons_sc_param_group_params', array(
								array(
									"param_name" => "text",
									"heading" => esc_html__("Field text", 'trx_addons'),
									"description" => '',
									"admin_label" => true,
									'edit_field_class' => 'vc_col-sm-6',
									"type" => "textfield"
								),
								array(
									"param_name" => "filter",
									"heading" => esc_html__("Field filter", 'trx_addons'),
									"description" => '',
									'edit_field_class' => 'vc_col-sm-6',
									"admin_label" => true,
									"std" => "none",
									"value" => array_flip(trx_addons_get_list_woocommerce_search_filters()),
									"type" => "dropdown"
								)
							), 'trx_widget_woocommerce_search')
						),
						array(
							"param_name" => "last_text",
							"heading" => esc_html__("Last text", 'trx_addons'),
							"description" => wp_kses_data( __("Text after the last filter", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							"type" => "textfield"
						),
						array(
							"param_name" => "button_text",
							"heading" => esc_html__("Button text", 'trx_addons'),
							"description" => wp_kses_data( __("Text of the button after all filters", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							"type" => "textfield"
						),
					),
					trx_addons_vc_add_id_param()
				)
			), 'trx_widget_woocommerce_search' );
	}
}
