<?php
/**
 * Widget: Categories list (WPBakery support)
 *
 * @package ThemeREX Addons
 * @since v1.0
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


// Add [trx_widget_categories_list] in the VC shortcodes list
if (!function_exists('trx_addons_sc_widget_categories_list_add_in_vc')) {
	function trx_addons_sc_widget_categories_list_add_in_vc() {
		
		if (!trx_addons_exists_vc()) return;
		
		vc_lean_map("trx_widget_categories_list", 'trx_addons_sc_widget_categories_list_add_in_vc_params');
		class WPBakeryShortCode_Trx_Widget_Categories_List extends WPBakeryShortCode {}
	}
	add_action('init', 'trx_addons_sc_widget_categories_list_add_in_vc', 20);
}

// Return params
if (!function_exists('trx_addons_sc_widget_categories_list_add_in_vc_params')) {
	function trx_addons_sc_widget_categories_list_add_in_vc_params() {
		// If open params in VC Editor
		list($vc_edit, $vc_params) = trx_addons_get_vc_form_params('trx_widget_categories_list');
		// Prepare lists
		$post_type = $vc_edit && !empty($vc_params['post_type']) ? $vc_params['post_type'] : 'post';
		$taxonomy = $vc_edit && !empty($vc_params['taxonomy']) ? $vc_params['taxonomy'] : 'category';
		$taxonomies_objects = get_object_taxonomies($post_type, 'objects');
		$taxonomies = array();
		if (is_array($taxonomies_objects)) {
			foreach ($taxonomies_objects as $slug=>$taxonomy_obj) {
				$taxonomies[$slug] = $taxonomy_obj->label;
			}
		}

		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_widget_categories_list",
				"name" => esc_html__("Widget: Categories List", 'trx_addons'),
				"description" => wp_kses_data( __("Insert a categories list with icons or images", 'trx_addons') ),
				"category" => esc_html__('ThemeREX', 'trx_addons'),
				"icon" => 'icon_trx_widget_categories_list',
				"class" => "trx_widget_categories_list",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array_merge(
					array(
						array(
							"param_name" => "title",
							"heading" => esc_html__("Widget title", 'trx_addons'),
							"description" => wp_kses_data( __("Title of the widget", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							"admin_label" => true,
							"type" => "textfield"
						),
						array(
							"param_name" => "style",
							"heading" => esc_html__("Style", 'trx_addons'),
							"description" => wp_kses_data( __("Select a style to display the categories list", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							"std" => 1,
							"value" => array_flip(apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('widgets', 'categories_list'), 'trx_widget_categories_list')),
							"type" => "dropdown"
						),
						array(
							"param_name" => "post_type",
							"heading" => esc_html__("Post type", 'trx_addons'),
							"description" => wp_kses_data( __("Select a post type to get taxonomies from", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-3',
							"admin_label" => true,
							"std" => 'post',
							"value" => array_flip(trx_addons_get_list_posts_types()),
							"type" => "dropdown"
						),
						array(
							"param_name" => "taxonomy",
							"heading" => esc_html__("Taxonomy", 'trx_addons'),
							"description" => wp_kses_data( __("Select taxonomy to get terms from", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-3',
							"admin_label" => true,
							"std" => 'category',
							"value" => array_flip($taxonomies),
							"type" => "dropdown"
						),
						array(
							"param_name" => "cat_list",
							"heading" => esc_html__("List of the terms", 'trx_addons'),
							"description" => wp_kses_data( __("Comma separated list of the term's slugs to show. If empty - show 'number' terms (see the field below)", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							"admin_label" => true,
							"type" => "textfield"
						),
						array(
							"param_name" => "number",
							"heading" => esc_html__("Number of categories to show", 'trx_addons'),
							"description" => wp_kses_data( __("How many categories display in widget?", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							"admin_label" => true,
							"value" => "5",
							"type" => "textfield"
						),
						array(
							"param_name" => "columns",
							"heading" => esc_html__("Columns number to show", 'trx_addons'),
							"description" => wp_kses_data( __("How many columns use to display categories list?", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							"admin_label" => true,
							"value" => "5",
							"type" => "textfield"
						),
						array(
							"param_name" => "show_thumbs",
							"heading" => esc_html__("Show thumbs", 'trx_addons'),
							"description" => wp_kses_data( __("Do you want display term's thumbnails (if exists)?", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"std" => "1",
							"value" => array("Show thumbs" => "1" ),
							"type" => "checkbox"
						),
						array(
							"param_name" => "show_posts",
							"heading" => esc_html__("Show posts number", 'trx_addons'),
							"description" => wp_kses_data( __("Do you want display posts number?", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"std" => "1",
							"value" => array("Show posts number" => "1" ),
							"type" => "checkbox"
						),
						array(
							"param_name" => "show_children",
							"heading" => esc_html__("Show children", 'trx_addons'),
							"description" => wp_kses_data( __("Show only children of the current category", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"std" => "0",
							"value" => array("Show children" => "1" ),
							"type" => "checkbox"
						)
					),
					trx_addons_vc_add_slider_param(),
					trx_addons_vc_add_id_param()
				)
			), 'trx_widget_categories_list');
	}
}
