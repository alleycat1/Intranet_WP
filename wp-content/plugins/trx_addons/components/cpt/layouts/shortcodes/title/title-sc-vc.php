<?php
/**
 * Shortcode: Display site meta and/or title and/or breadcrumbs (WPBakery support)
 *
 * @package ThemeREX Addons
 * @since v1.6.08
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


// Add [trx_sc_layouts_title] in the VC shortcodes list
if (!function_exists('trx_addons_sc_layouts_title_add_in_vc')) {
	function trx_addons_sc_layouts_title_add_in_vc() {
		
		if (!trx_addons_cpt_layouts_sc_required()) return;
		
		if (!trx_addons_exists_vc()) return;

		vc_lean_map("trx_sc_layouts_title", 'trx_addons_sc_layouts_title_add_in_vc_params');
		class WPBakeryShortCode_Trx_Sc_Layouts_Title extends WPBakeryShortCode {}
	}
	add_action('init', 'trx_addons_sc_layouts_title_add_in_vc', 15);
}

// Return params
if (!function_exists('trx_addons_sc_layouts_title_add_in_vc_params')) {
	function trx_addons_sc_layouts_title_add_in_vc_params() {
		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_sc_layouts_title",
				"name" => esc_html__("Layouts: Title and Breadcrumbs", 'trx_addons'),
				"description" => wp_kses_data( __("Insert post meta and/or title and/or breadcrumbs", 'trx_addons') ),
				"category" => esc_html__('Layouts', 'trx_addons'),
				"icon" => 'icon_trx_sc_layouts_title',
				"class" => "trx_sc_layouts_title",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array_merge(
					array(
						array(
							"param_name" => "type",
							"heading" => esc_html__("Layout", 'trx_addons'),
							"description" => wp_kses_data( __("Select shortcodes's layout", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"std" => "default",
							"value" => array_flip(apply_filters('trx_addons_sc_type', array(
								'default' => esc_html__('Default', 'trx_addons')
							), 'trx_sc_layouts_title')),
							"type" => "dropdown"
						),
						array(
							"param_name" => "align",
							"heading" => esc_html__("Alignment", 'trx_addons'),
							"description" => wp_kses_data( __("Select alignment of the inner content in this block", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"admin_label" => true,
							"value" => array_flip(trx_addons_get_list_sc_aligns(true, false)),
							"std" => "inherit",
							"type" => "dropdown"
						),
						array(
							"param_name" => "title",
							"heading" => esc_html__("Show post title", 'trx_addons'),
							"description" => wp_kses_data( __("Show post/page title", 'trx_addons') ),
							"admin_label" => true,
							'edit_field_class' => 'vc_col-sm-4 vc_new_row',
							"std" => "0",
							"value" => array(esc_html__("Show", 'trx_addons') => "1" ),
							"type" => "checkbox"
						),
						array(
							"param_name" => "meta",
							"heading" => esc_html__("Show post meta", 'trx_addons'),
							"description" => wp_kses_data( __("Show post meta: date, author, categories list, etc.", 'trx_addons') ),
							"admin_label" => true,
							'edit_field_class' => 'vc_col-sm-4',
							"std" => "0",
							"value" => array(esc_html__("Show", 'trx_addons') => "1" ),
							"type" => "checkbox"
						),
						array(
							"param_name" => "breadcrumbs",
							"heading" => esc_html__("Show breadcrumbs", 'trx_addons'),
							"description" => wp_kses_data( __("Show breadcrumbs under the title", 'trx_addons') ),
							"admin_label" => true,
							'edit_field_class' => 'vc_col-sm-4',
							"std" => "0",
							"value" => array(esc_html__("Show", 'trx_addons') => "1" ),
							"type" => "checkbox"
						),
						array(
							"param_name" => "image",
							"heading" => esc_html__("Background image", 'trx_addons'),
							"description" => wp_kses_data( __("Background image of the block", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4 vc_new_row',
							"type" => "attach_image"
						),
						array(
							"param_name" => "use_featured_image",
							"heading" => esc_html__("Post featured image", 'trx_addons'),
							"description" => wp_kses_data( __("Use post's featured image as background of the block instead image above (if present)", 'trx_addons') ),
							"admin_label" => true,
							'edit_field_class' => 'vc_col-sm-4',
							"std" => "0",
							"value" => array(esc_html__("Replace with featured image", 'trx_addons') => "1" ),
							"type" => "checkbox"
						),
						array(
							"param_name" => "height",
							"heading" => esc_html__("Height of the block", 'trx_addons'),
							"description" => wp_kses_data( __("Specify height of this block. If empty - use default height", 'trx_addons') ),
							/*
							'dependency' => array(
								'element' => 'use_featured_image',
								'value' => '1'
							),
							*/
							'edit_field_class' => 'vc_col-sm-4',
							"admin_label" => true,
							"type" => "textfield"
						)
					),
					trx_addons_vc_add_hide_param(false, true),
					trx_addons_vc_add_id_param()
				)
			), 'trx_sc_layouts_title');
	}
}
