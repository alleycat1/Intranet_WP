<?php
/**
 * Widget: Recent News (WPBakery support)
 *
 * @package ThemeREX Addons
 * @since v1.0
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}



// Add [trx_recent_news] in the VC shortcodes list
if (!function_exists('trx_addons_sc_recent_news_add_in_vc')) {
	function trx_addons_sc_recent_news_add_in_vc() {
		
		if (!trx_addons_exists_vc()) return;
		
		vc_lean_map("trx_widget_recent_news", 'trx_addons_sc_recent_news_add_in_vc_params');
		class WPBakeryShortCode_Trx_Recent_News extends WPBakeryShortCode {}
	}
	add_action('init', 'trx_addons_sc_recent_news_add_in_vc', 20);
}

// Return params
if (!function_exists('trx_addons_sc_recent_news_add_in_vc_params')) {
	function trx_addons_sc_recent_news_add_in_vc_params() {
		$list_sort = array(
			"none" 		=> esc_html__('None', 'trx_addons'),
			"ID" 		=> esc_html__('Post ID', 'trx_addons'),
			"date"		=> esc_html__("Date", 'trx_addons'),
			"title"		=> esc_html__("Alphabetically", 'trx_addons'),
			"views"		=> esc_html__("Popular (views count)", 'trx_addons'),
			"comments"	=> esc_html__("Most commented (comments count)", 'trx_addons'),
			"random"	=> esc_html__("Random", 'trx_addons')
		);
		$list_order = array(
			"asc"  => esc_html__("Ascending", 'trx_addons'),
			"desc" => esc_html__("Descending", 'trx_addons')
		);
		
		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_widget_recent_news",
				"name" => esc_html__("Widget: Recent News", 'trx_addons'),
				"description" => wp_kses_data( __("Insert recent news list", 'trx_addons') ),
				"category" => esc_html__('ThemeREX', 'trx_addons'),
				"icon" => 'icon_trx_widget_recent_news',
				"class" => "trx_widget_recent_news",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array_merge(
					array(
						array(
							"param_name" => "widget_title",
							"heading" => esc_html__("Widget Title", 'trx_addons'),
							"description" => wp_kses_data( __("Title of the widget (fill this field only if you want to use shortcode as widget)", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"admin_label" => true,
							"type" => "textfield"
						),
						array(
							"param_name" => "title",
							"heading" => esc_html__("Title", 'trx_addons'),
							"description" => wp_kses_data( __("Title of the block", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"admin_label" => true,
							"type" => "textfield"
						),
						array(
							"param_name" => "subtitle",
							"heading" => esc_html__("Subtitle", 'trx_addons'),
							"description" => wp_kses_data( __("Subtitle of the block", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"type" => "textfield"
						),
						array(
							"param_name" => "style",
							"heading" => esc_html__("List style", 'trx_addons'),
							"description" => wp_kses_data( __("Select style to display news list", 'trx_addons') ),
							"admin_label" => true,
							'edit_field_class' => 'vc_col-sm-6',
							"std" => 'news-magazine',
					        'save_always' => true,
							"value" => array_flip(apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('widgets', 'recent_news'), 'trx_widget_recent_news')),
							"type" => "dropdown"
						),
						array(
							"param_name" => "show_categories",
							"heading" => esc_html__("Show categories", 'trx_addons'),
							"description" => wp_kses_data( __("Show categories in the shortcode's header", 'trx_addons') ),
							"std" => "0",
							'edit_field_class' => 'vc_col-sm-6',
							"value" => array("Show categories" => 1 ),
							"type" => "checkbox"
						),
						array(
							"param_name" => "ids",
							"heading" => esc_html__("List IDs", 'trx_addons'),
							"description" => wp_kses_data( __("Comma separated list of IDs list to display. If not empty, parameters 'cat', 'offset' and 'count' are ignored!", 'trx_addons') ),
							"group" => esc_html__('Query', 'trx_addons'),
							"type" => "textfield"
						),
						array(
							"param_name" => "category",
							"heading" => esc_html__("Category", 'trx_addons'),
							"description" => wp_kses_data( __("Select a category to display. If empty - select news from any category or from the IDs list.", 'trx_addons') ),
							"group" => esc_html__('Query', 'trx_addons'),
							'edit_field_class' => 'vc_col-sm-4',
							'dependency' => array(
								'element' => 'ids',
								'is_empty' => true
							),
							"std" => 0,
							"value" => array_flip( trx_addons_array_merge( array( 0 => trx_addons_get_not_selected_text( esc_html__( 'Select category', 'trx_addons' ) ) ), trx_addons_get_list_categories() ) ),
							"type" => "dropdown"
						),
						array(
							"param_name" => "count",
							"heading" => esc_html__("Total posts", 'trx_addons'),
							"description" => wp_kses_data( __("The number of displayed posts. If IDs are used, this parameter is ignored.", 'trx_addons') ),
							"admin_label" => true,
							'edit_field_class' => 'vc_col-sm-4',
							'dependency' => array(
								'element' => 'ids',
								'is_empty' => true
							),
							"group" => esc_html__('Query', 'trx_addons'),
							"value" => "3",
							"type" => "textfield"
						),
						array(
							"param_name" => "columns",
							"heading" => esc_html__("Columns", 'trx_addons'),
							"description" => wp_kses_data( __("How many columns use to show news list", 'trx_addons') ),
							"group" => esc_html__('Query', 'trx_addons'),
							"admin_label" => true,
							'edit_field_class' => 'vc_col-sm-4',
							'dependency' => array(
								'element' => 'style',
								'value' => array('news-magazine', 'news-portfolio'),
							),
							"value" => "3",
							"type" => "textfield"
						),
						array(
							"param_name" => "offset",
							"heading" => esc_html__("Offset before select posts", 'trx_addons'),
							"description" => wp_kses_data( __("Skip posts before select next part.", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6 vc_new_row',
							'dependency' => array(
								'element' => 'ids',
								'is_empty' => true
							),
							"group" => esc_html__('Query', 'trx_addons'),
							"value" => "0",
							"type" => "textfield"
						),
						array(
							"param_name" => "featured",
							"heading" => esc_html__("Featured posts", 'trx_addons'),
							"description" => wp_kses_data( __("How many posts will be displayed as featured?", 'trx_addons') ),
							"admin_label" => true,
							"group" => esc_html__('Query', 'trx_addons'),
							'edit_field_class' => 'vc_col-sm-6',
							'dependency' => array(
								'element' => 'style',
								'value' => 'news-magazine'
							),
							"value" => "3",
							"type" => "textfield"
						),
						array(
							"param_name" => "orderby",
							"heading" => esc_html__("Posts sorting", 'trx_addons'),
							"description" => wp_kses_data( __("Select desired posts sorting method", 'trx_addons') ),
							"group" => esc_html__('Query', 'trx_addons'),
							'edit_field_class' => 'vc_col-sm-6 vc_new_row',
							"value" => array_flip($list_sort),
					        'save_always' => true,
							"type" => "dropdown"
						),
						array(
							"param_name" => "order",
							"heading" => esc_html__("Posts order", 'trx_addons'),
							"description" => wp_kses_data( __("Select desired posts order", 'trx_addons') ),
							"group" => esc_html__('Query', 'trx_addons'),
							'edit_field_class' => 'vc_col-sm-6',
							"value" => array_flip($list_order),
					        'save_always' => true,
							"type" => "dropdown"
						),
					),
					trx_addons_vc_add_id_param()
				)
			), 'trx_widget_recent_news' );
		}
}
