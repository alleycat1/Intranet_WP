<?php
/**
 * Shortcode: Single Post Meta (WPBakery support)
 *
 * @package ThemeREX Addons
 * @since v1.6.49
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}
	


// Add [trx_sc_layouts_meta] in the VC shortcodes list
if (!function_exists('trx_addons_sc_layouts_meta_add_in_vc')) {
	function trx_addons_sc_layouts_meta_add_in_vc() {
		
		if (!trx_addons_cpt_layouts_sc_required()) return;
		
		if (!trx_addons_exists_vc()) return;
		
		vc_lean_map("trx_sc_layouts_meta", 'trx_addons_sc_layouts_meta_add_in_vc_params');
		class WPBakeryShortCode_Trx_Sc_Layouts_Meta extends WPBakeryShortCode {}
	}
	add_action('init', 'trx_addons_sc_layouts_meta_add_in_vc', 15);
}

// Return params
if (!function_exists('trx_addons_sc_layouts_meta_add_in_vc_params')) {
	function trx_addons_sc_layouts_meta_add_in_vc_params() {

		$components = apply_filters('trx_addons_filter_get_list_meta_parts', array());

		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_sc_layouts_meta",
				"name" => esc_html__("Layouts: Single Post Meta", 'trx_addons'),
				"description" => wp_kses_data( __("Add post meta", 'trx_addons') ),
				"category" => esc_html__('ThemeREX', 'trx_addons'),
				"icon" => 'icon_trx_sc_layouts_meta',
				"class" => "trx_sc_layouts_meta",
				'content_element' => true,
				'is_container' => false,
				"show_settings_on_create" => true,
				"params" => array_merge(
					array(
						array(
							"param_name" => "type",
							"heading" => esc_html__("Layout", 'trx_addons'),
							"description" => wp_kses_data( __("Select shortcodes's layout", 'trx_addons') ),
							"admin_label" => true,
							'save_always' => true,
							'edit_field_class' => 'vc_col-sm-6',
							"std" => "default",
							"value" => array_flip(apply_filters('trx_addons_sc_type', trx_addons_get_list_sc_layouts_meta(), 'trx_sc_layouts_meta')),
							"type" => "dropdown"
						),
						array(
							"param_name" => "components",
							"heading" => esc_html__("Choose components", 'trx_addons'),
							"description" => wp_kses_data( __("Display specified post meta elements", 'trx_addons') ),
							"admin_label" => true,
							'save_always' => true,
							'edit_field_class' => 'vc_col-sm-6',
							"std" => trx_addons_array_get_first($components),
							"value" => array_flip( $components ),
							"multiple" => true,
							"type" => "select"
						),
						array(
							"param_name" => "share_type",
							"heading" => esc_html__("Share style", 'trx_addons'),
							"description" => wp_kses_data( __("Style of the share list", 'trx_addons') ),
							'save_always' => true,
							'edit_field_class' => 'vc_col-sm-6',
							"std" => 'drop',
							"value" => array_flip( trx_addons_get_list_sc_share_types() ),
							"type" => "select"
						),
					),
					trx_addons_vc_add_id_param()
				)
			), 'trx_sc_layouts_meta' );
	}
}
