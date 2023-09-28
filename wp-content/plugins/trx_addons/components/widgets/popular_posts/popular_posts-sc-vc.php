<?php
/**
 * Widget: Popular posts (WPBakery support)
 *
 * @package ThemeREX Addons
 * @since v1.0
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}



// Add [trx_widget_popular_posts] in the VC shortcodes list
if (!function_exists('trx_addons_sc_widget_popular_posts_add_in_vc')) {
	function trx_addons_sc_widget_popular_posts_add_in_vc() {
		
		if (!trx_addons_exists_vc()) return;
		
		vc_lean_map("trx_widget_popular_posts", 'trx_addons_sc_widget_popular_posts_add_in_vc_params');
		class WPBakeryShortCode_Trx_Widget_Popular_Posts extends WPBakeryShortCode {}
	}
	add_action('init', 'trx_addons_sc_widget_popular_posts_add_in_vc', 20);
}

// Return params
if (!function_exists('trx_addons_sc_widget_popular_posts_add_in_vc_params')) {
	function trx_addons_sc_widget_popular_posts_add_in_vc_params() {

		// If open params in VC Editor
		list($vc_edit, $vc_params) = trx_addons_get_vc_form_params('trx_widget_popular_posts');
		// Prepare lists
		$post_type_1 = $vc_edit && !empty($vc_params['post_type_1']) ? $vc_params['post_type_1'] : 'post';
		$taxonomy_1 = $vc_edit && !empty($vc_params['taxonomy_1']) ? $vc_params['taxonomy_1'] : 'category';
		$tax_obj_1 = get_taxonomy($taxonomy_1);
		$post_type_2 = $vc_edit && !empty($vc_params['post_type_2']) ? $vc_params['post_type_2'] : 'post';
		$taxonomy_2 = $vc_edit && !empty($vc_params['taxonomy_2']) ? $vc_params['taxonomy_2'] : 'category';
		$tax_obj_2 = get_taxonomy($taxonomy_2);
		$post_type_3 = $vc_edit && !empty($vc_params['post_type_3']) ? $vc_params['post_type_3'] : 'post';
		$taxonomy_3 = $vc_edit && !empty($vc_params['taxonomy_3']) ? $vc_params['taxonomy_3'] : 'category';
		$tax_obj_3 = get_taxonomy($taxonomy_3);

		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_widget_popular_posts",
				"name" => esc_html__("Widget: Popular Posts", 'trx_addons'),
				"description" => wp_kses_data( __("Insert popular posts list with thumbs, post's meta and category", 'trx_addons') ),
				"category" => esc_html__('ThemeREX', 'trx_addons'),
				"icon" => 'icon_trx_widget_popular_posts',
				"class" => "trx_widget_popular_posts",
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
							'edit_field_class' => 'vc_col-sm-8',
							"type" => "textfield"
						),
						array(
							"param_name" => "number",
							"heading" => esc_html__("Number posts to show", 'trx_addons'),
							"description" => wp_kses_data( __("How many posts display in widget?", 'trx_addons') ),
							"admin_label" => true,
							'edit_field_class' => 'vc_col-sm-4',
							"value" => "4",
							"type" => "textfield"
						),

						array(
							"param_name" => "show_image",
							"heading" => esc_html__("Show post's image", 'trx_addons'),
							"description" => wp_kses_data( __("Do you want display post's featured image?", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4 vc_new_row',
							"std" => "1",
							"value" => array("Show image" => "1" ),
							"type" => "checkbox"
						),
						array(
							"param_name" => "show_author",
							"heading" => esc_html__("Show post's author", 'trx_addons'),
							"description" => wp_kses_data( __("Do you want display post's author?", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"std" => "1",
							"value" => array("Show author" => "1" ),
							"type" => "checkbox"
						),
						array(
							"param_name" => "show_date",
							"heading" => esc_html__("Show post's date", 'trx_addons'),
							"description" => wp_kses_data( __("Do you want display post's publish date?", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"std" => "1",
							"value" => array("Show date" => "1" ),
							"type" => "checkbox"
						),
						array(
							"param_name" => "show_counters",
							"heading" => esc_html__("Show post's counters", 'trx_addons'),
							"description" => wp_kses_data( __("Do you want display post's counters?", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4 vc_new_row',
							"std" => "1",
							"value" => array("Show counters" => "1" ),
							"type" => "checkbox"
						),
						array(
							"param_name" => "show_categories",
							"heading" => esc_html__("Show post's categories", 'trx_addons'),
							"description" => wp_kses_data( __("Do you want display post's categories?", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"std" => "1",
							"value" => array("Show categories" => "1" ),
							"type" => "checkbox"
						),

						array(
							"param_name" => "title_1",
							"heading" => esc_html__("Title", 'trx_addons'),
							"description" => wp_kses_data( __("Tab 1 title", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-8',
							"group" => esc_html__("Tab 1", 'trx_addons'),
							"admin_label" => true,
							"type" => "textfield"
						),
						array(
							"param_name" => "orderby_1",
							"heading" => esc_html__("Order by", 'trx_addons'),
							"description" => wp_kses_data( __("Select posts order", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"group" => esc_html__("Tab 1", 'trx_addons'),
							"std" => 'post',
							"value" => array_flip(trx_addons_get_list_widget_query_orderby()),
							"type" => "dropdown"
						),
						array(
							"param_name" => "post_type_1",
							"heading" => esc_html__("Post type", 'trx_addons'),
							"description" => wp_kses_data( __("Select post type to show posts", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4 vc_new_row',
							"group" => esc_html__("Tab 1", 'trx_addons'),
							"std" => 'post',
							"value" => array_flip(trx_addons_get_list_posts_types()),
							"type" => "dropdown"
						),
						array(
							"param_name" => "taxonomy_1",
							"heading" => esc_html__("Taxonomy", 'trx_addons'),
							"description" => wp_kses_data( __("Select taxonomy to show posts", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"group" => esc_html__("Tab 1", 'trx_addons'),
							"std" => 'category',
							"value" => array_flip(trx_addons_get_list_taxonomies(false, $post_type_1)),
							"type" => "dropdown"
						),
						array(
							"param_name" => "cat_1",
							"heading" => esc_html__("Category", 'trx_addons'),
							"description" => wp_kses_data( __("Select category to show posts", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"group" => esc_html__("Tab 1", 'trx_addons'),
							"value" => array_flip( trx_addons_array_merge(
														array( 0 => trx_addons_get_not_selected_text( ! empty( $tax_obj_1->label ) ? $tax_obj_1->label : __( '- Not Selected -', 'trx_addons' ) ) ),
														$taxonomy_1 == 'category' 
															? trx_addons_get_list_categories() 
															: trx_addons_get_list_terms(false, $taxonomy_1)
													) ),
							"std" => "0",
							"type" => "dropdown"
						),

						array(
							"param_name" => "title_2",
							"heading" => esc_html__("Title", 'trx_addons'),
							"description" => wp_kses_data( __("Tab 2 title", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-8',
							"admin_label" => true,
							"group" => esc_html__("Tab 2", 'trx_addons'),
							"type" => "textfield"
						),
						array(
							"param_name" => "orderby_2",
							"heading" => esc_html__("Order by", 'trx_addons'),
							"description" => wp_kses_data( __("Select posts order", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"group" => esc_html__("Tab 2", 'trx_addons'),
							"std" => 'post',
							"value" => array_flip(trx_addons_get_list_widget_query_orderby()),
							"type" => "dropdown"
						),
						array(
							"param_name" => "post_type_2",
							"heading" => esc_html__("Post type", 'trx_addons'),
							"description" => wp_kses_data( __("Select post type to show posts", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4 vc_new_row',
							"group" => esc_html__("Tab 2", 'trx_addons'),
							"std" => 'post',
							"value" => array_flip(trx_addons_get_list_posts_types()),
							"type" => "dropdown"
						),
						array(
							"param_name" => "taxonomy_2",
							"heading" => esc_html__("Taxonomy", 'trx_addons'),
							"description" => wp_kses_data( __("Select taxonomy to show posts", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"group" => esc_html__("Tab 2", 'trx_addons'),
							"std" => 'category',
							"value" => array_flip(trx_addons_get_list_taxonomies(false, $post_type_2)),
							"type" => "dropdown"
						),
						array(
							"param_name" => "cat_2",
							"heading" => esc_html__("Category", 'trx_addons'),
							"description" => wp_kses_data( __("Select category to show posts", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"group" => esc_html__("Tab 2", 'trx_addons'),
							"value" => array_flip( trx_addons_array_merge(
														array( 0 => trx_addons_get_not_selected_text( ! empty( $tax_obj_2->label ) ? $tax_obj_2->label : __( '- Not Selected -', 'trx_addons' ) ) ),
														$taxonomy_2 == 'category' 
															? trx_addons_get_list_categories() 
															: trx_addons_get_list_terms(false, $taxonomy_2)
													) ),
							"std" => "0",
							"type" => "dropdown"
						),
						
						array(
							"param_name" => "title_3",
							"heading" => esc_html__("Title", 'trx_addons'),
							"description" => wp_kses_data( __("Tab 3 title", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-8',
							"admin_label" => true,
							"group" => esc_html__("Tab 3", 'trx_addons'),
							"type" => "textfield"
						),
						array(
							"param_name" => "orderby_3",
							"heading" => esc_html__("Order by", 'trx_addons'),
							"description" => wp_kses_data( __("Select posts order", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"group" => esc_html__("Tab 3", 'trx_addons'),
							"std" => 'post',
							"value" => array_flip(trx_addons_get_list_widget_query_orderby()),
							"type" => "dropdown"
						),
						array(
							"param_name" => "post_type_3",
							"heading" => esc_html__("Post type", 'trx_addons'),
							"description" => wp_kses_data( __("Select post type to show posts", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4 vc_new_row',
							"group" => esc_html__("Tab 3", 'trx_addons'),
							"std" => 'post',
							"value" => array_flip(trx_addons_get_list_posts_types()),
							"type" => "dropdown"
						),
						array(
							"param_name" => "taxonomy_3",
							"heading" => esc_html__("Taxonomy", 'trx_addons'),
							"description" => wp_kses_data( __("Select taxonomy to show posts", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"group" => esc_html__("Tab 3", 'trx_addons'),
							"std" => 'category',
							"value" => array_flip(trx_addons_get_list_taxonomies(false, $post_type_3)),
							"type" => "dropdown"
						),
						array(
							"param_name" => "cat_3",
							"heading" => esc_html__("Category", 'trx_addons'),
							"description" => wp_kses_data( __("Select category to show posts", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"group" => esc_html__("Tab 3", 'trx_addons'),
							"value" => array_flip( trx_addons_array_merge(
														array( 0 => trx_addons_get_not_selected_text( ! empty( $tax_obj_3->label ) ? $tax_obj_3->label : __( '- Not Selected -', 'trx_addons' ) ) ),
														$taxonomy_3 == 'category' 
															? trx_addons_get_list_categories() 
															: trx_addons_get_list_terms(false, $taxonomy_3)
													) ),
							"std" => "0",
							"type" => "dropdown"
						)
					),
					trx_addons_vc_add_id_param()
				)
			), 'trx_widget_popular_posts');
	}
}
