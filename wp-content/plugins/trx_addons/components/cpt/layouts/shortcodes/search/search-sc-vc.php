<?php
/**
 * Shortcode: Display Search form (WPBakery support)
 *
 * @package ThemeREX Addons
 * @since v1.6.08
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}



// Add [trx_sc_layouts_search] in the VC shortcodes list
if (!function_exists('trx_addons_sc_layouts_search_add_in_vc')) {
	function trx_addons_sc_layouts_search_add_in_vc() {
		
		if (!trx_addons_cpt_layouts_sc_required()) return;
		
		if (!trx_addons_exists_vc()) return;

		vc_lean_map("trx_sc_layouts_search", 'trx_addons_sc_layouts_search_add_in_vc_params');
		class WPBakeryShortCode_Trx_Sc_Layouts_Search extends WPBakeryShortCode {}
	}
	add_action('init', 'trx_addons_sc_layouts_search_add_in_vc', 15);
}

// Return params
if (!function_exists('trx_addons_sc_layouts_search_add_in_vc_params')) {
	function trx_addons_sc_layouts_search_add_in_vc_params() {

		$post_types = trx_addons_get_list_posts_types();

		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_sc_layouts_search",
				"name" => esc_html__("Layouts: Search form", 'trx_addons'),
				"description" => wp_kses_data( __("Insert search form to the custom layout", 'trx_addons') ),
				"category" => esc_html__('Layouts', 'trx_addons'),
				"icon" => 'icon_trx_sc_layouts_search',
				"class" => "trx_sc_layouts_search",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array_merge(
					array(
						array(
							"param_name" => "type",
							"heading" => esc_html__("Layout", 'trx_addons'),
							"description" => wp_kses_data( __("Select shortcodes's layout", 'trx_addons') ),
							"admin_label" => true,
							"std" => "default",
							"value" => array_flip(apply_filters('trx_addons_sc_type', array(
								'default' => esc_html__('Default', 'trx_addons'),
							), 'trx_sc_layouts_search')),
							"type" => "dropdown"
						),
						array(
							"param_name" => "style",
							"heading" => esc_html__("Style", 'trx_addons'),
							"description" => wp_kses_data( __("Select form's style", 'trx_addons') ),
							"admin_label" => true,
							"std" => "normal",
							"value" => array_flip(apply_filters('trx_addons_sc_style', trx_addons_get_list_sc_layouts_search(), 'trx_sc_layouts_search')),
							"type" => "dropdown"
						),
						array(
							"param_name" => "ajax",
							"heading" => esc_html__("AJAX search", 'trx_addons'),
							"description" => wp_kses_data( __("Use incremental AJAX search", 'trx_addons') ),
							"admin_label" => true,
							"std" => "0",
							"value" => array(esc_html__("AJAX search", 'trx_addons') => "1" ),
							"type" => "checkbox"
						),
						array(
							"param_name" => "post_types",
							"heading" => esc_html__("Search in post types", 'trx_addons'),
							"description" => wp_kses_data( __("Select the types of posts you want to search", 'trx_addons') ),
							"admin_label" => true,
							"std" => "",
							"multiple" => true,
							"value" => array_flip($post_types),
							"type" => "select"
						),
					),
					trx_addons_vc_add_hide_param(),
					trx_addons_vc_add_id_param()
				)
			), 'trx_sc_layouts_search');
	}
}
