<?php
/**
 * Shortcode: Blogger (WPBakery support)
 *
 * @package ThemeREX Addons
 * @since v1.2
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}



// Add [trx_sc_blogger] in the VC shortcodes list
if (!function_exists('trx_addons_sc_blogger_add_in_vc')) {
	add_action('init', 'trx_addons_sc_blogger_add_in_vc', 20);
	function trx_addons_sc_blogger_add_in_vc() {
		
		if (!trx_addons_exists_vc()) return;
		
		vc_lean_map("trx_sc_blogger", 'trx_addons_sc_blogger_add_in_vc_params');
		class WPBakeryShortCode_Trx_Sc_Blogger extends WPBakeryShortCode {}
	}
}


// Return params
if (!function_exists('trx_addons_sc_blogger_add_in_vc_params')) {
	function trx_addons_sc_blogger_add_in_vc_params() {
		// If open params in VC Editor
		list($vc_edit, $vc_params) = trx_addons_get_vc_form_params('trx_sc_blogger');
		// Prepare lists
		$post_type = $vc_edit && !empty($vc_params['post_type']) ? $vc_params['post_type'] : 'post';
		$taxonomy = $vc_edit && !empty($vc_params['taxonomy']) ? $vc_params['taxonomy'] : 'category';
		$tax_obj = get_taxonomy($taxonomy);
		$layouts = apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('sc', 'blogger'), 'trx_sc_blogger' );
		$templates = trx_addons_components_get_allowed_templates('sc', 'blogger', $layouts);
		$templates_params = array();
		if ( is_array($templates) ) {
			foreach ($templates as $k => $v) {
				$options = array();
				$default = '';
				if (is_array($v)) {
					foreach($v as $k1 => $v1) {
						$options[$k1] = !empty($v1['title']) ? $v1['title'] : ucfirst( str_replace( array('_', '-'), ' ', $k1 ) );
						if (empty($default)) $default = $k1;
					}
				}
				$templates_params[] = array(
					"param_name" => 'template_' . $k,
					"heading" => esc_html__("Template", 'trx_addons'),
					"group" => esc_html__("Details", 'trx_addons'),
					'dependency' => array(
						'element' => 'type',
						'value' => $k
					),
					"admin_label" => true,
					'save_always' => true,
					"std" => $default,
					"value" => array_flip($options),
					"type" => "dropdown"
				);
			}
		}
		$meta_parts = apply_filters('trx_addons_filter_get_list_meta_parts', array());

		$params = array(
				"base" => "trx_sc_blogger",
				"name" => esc_html__("Blogger", 'trx_addons'),
				"description" => wp_kses_data( __("Display posts from specified category in many styles", 'trx_addons') ),
				"category" => esc_html__('ThemeREX', 'trx_addons'),
				"icon" => 'icon_trx_sc_blogger',
				"class" => "trx_sc_blogger",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array_merge(
					array(
						// Attention! It's our custom param's type and it need values list as normal associative array 'key' => 'value'
						// not in VC-style 'value' => 'key'
						// 'style' => 'icons' | 'images'
						// 'mode' => 'inline' | 'dropdown'
						// 'return' => 'slug' | 'full'
/*
						array(
							"param_name" => "type",
							"heading" => esc_html__("Layout", 'trx_addons'),
							"description" => wp_kses_data( __("Select shortcode's layout", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-8',
							"admin_label" => true,
					        'save_always' => true,
							"std" => "default",
							"value" => apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('sc', 'blogger'), 'trx_sc_blogger' ),
							"mode" => 'inline',
							"return" => 'slug',
							"style" => "images",
							"type" => "icons"
						),
*/
						array(
							"param_name" => "type",
							"heading" => esc_html__("Layout", 'trx_addons'),
							"description" => wp_kses_data( __("Select shortcode's layout", 'trx_addons') ),
							"admin_label" => true,
							'save_always' => true,
							"std" => "default",
							"value" => array_flip($layouts),
							"type" => "dropdown"
						),
						array(
							"param_name" => "post_type",
							"heading" => esc_html__("Post type", 'trx_addons'),
							"description" => wp_kses_data( __("Select post type to show posts", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4 vc_new_row',
							"std" => 'post',
							"value" => array_flip(trx_addons_get_list_posts_types()),
							"type" => "dropdown"
						),
						array(
							"param_name" => "taxonomy",
							"heading" => esc_html__("Taxonomy", 'trx_addons'),
							"description" => wp_kses_data( __("Select taxonomy to show posts", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"std" => 'category',
							"value" => array_flip(trx_addons_get_list_taxonomies(false, $post_type)),
							"type" => "dropdown"
						),
						array(
							"param_name" => "cat",
							"heading" => esc_html__("Category", 'trx_addons'),
							"description" => wp_kses_data( __("Select category to show posts", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"std" => 0,
							"value" => array_flip( trx_addons_array_merge(
														array( 0 => trx_addons_get_not_selected_text( ! empty( $tax_obj->label ) ? $tax_obj->label : __( '- Not Selected -', 'trx_addons' ) ) ),
														$taxonomy == 'category' 
															? trx_addons_get_list_categories() 
															: trx_addons_get_list_terms(false, $taxonomy)
														) ),
							"multiple" => true,
							"type" => "select"
						),
					),
					trx_addons_vc_add_query_param(''),
					array(
						array(
							"param_name" => "pagination",
							"heading" => esc_html__("Pagination", 'trx_addons'),
							"description" => wp_kses_data( __("Add pagination links after posts. Attention! If slider is active, pagination is not allowed!", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"std" => 'none',
							"value" => array_flip(trx_addons_get_list_sc_paginations()),
							'dependency' => array(
								'element' => 'type',
								'not_equal' => 'cards'
							),
							"type" => "dropdown"
						),
						array(
							"param_name" => "filters_title",
							"heading" => esc_html__("Filters area title", 'trx_addons'),
							"group" => esc_html__("Filters", 'trx_addons'),
							'edit_field_class' => 'vc_col-sm-4 vc_new_row',
							"std" => '',
							"type" => "textfield"
						),
						array(
							"param_name" => "filters_subtitle",
							"heading" => esc_html__("Filters area subtitle", 'trx_addons'),
							"group" => esc_html__("Filters", 'trx_addons'),
							'edit_field_class' => 'vc_col-sm-4',
							"std" => '',
							"type" => "textfield"
						),
						array(
							"param_name" => "filters_title_align",
							"heading" => esc_html__("Filters titles position", 'trx_addons'),
							"group" => esc_html__("Filters", 'trx_addons'),
							'edit_field_class' => 'vc_col-sm-4',
							"std" => 'left',
							"value" => array_flip(trx_addons_get_list_sc_aligns(false, false)),
							"type" => "dropdown"
						),
						array(
							"param_name" => "show_filters",
							"heading" => esc_html__("Show filters tabs", 'trx_addons'),
							"group" => esc_html__("Filters", 'trx_addons'),
							'edit_field_class' => 'vc_col-sm-4 vc_new_row',
							"std" => "0",
							"value" => array(esc_html__("Show filters tabs", 'trx_addons') => "1" ),
							"type" => "checkbox"
						),
						array(
							"param_name" => "filters_tabs_position",
							"heading" => esc_html__("Filters tabs position", 'trx_addons'),
							"group" => esc_html__("Filters", 'trx_addons'),
							'edit_field_class' => 'vc_col-sm-4',
							"std" => 'top',
							"value" => array_flip(trx_addons_get_list_sc_tabs_positions()),
							'dependency' => array(
								'element' => 'show_filters',
								'value' => '1'
							),
							"type" => "dropdown"
						),
						array(
							"param_name" => "filters_tabs_on_hover",
							"heading" => esc_html__("Open tabs on hover", 'trx_addons'),
							"group" => esc_html__("Filters", 'trx_addons'),
							'edit_field_class' => 'vc_col-sm-4',
							"std" => "0",
							"value" => array(esc_html__("Open tabs on hover", 'trx_addons') => "1" ),
							'dependency' => array(
								'element' => 'show_filters',
								'value' => '1'
							),
							"type" => "checkbox"
						),
						array(
							"param_name" => "filters_taxonomy",
							"heading" => esc_html__("Filters taxonomy", 'trx_addons'),
							"group" => esc_html__("Filters", 'trx_addons'),
							'edit_field_class' => 'vc_col-sm-4',
							"std" => 'category',
							"value" => array_flip(trx_addons_get_list_taxonomies(false, $post_type)),
							'dependency' => array(
								'element' => 'show_filters',
								'value' => '1'
							),
							"type" => "dropdown"
						),
						array(
							"param_name" => "filters_ids",
							"heading" => esc_html__("Filters terms", 'trx_addons'),
							"description" => wp_kses_data( __("Comma separated list with term IDs or term names to show as filters. If empty - show all terms from filters taxonomy above", 'trx_addons') ),
							"group" => esc_html__("Filters", 'trx_addons'),
							'edit_field_class' => 'vc_col-sm-4',
							'dependency' => array(
								'element' => 'show_filters',
								'value' => '1'
							),
							"std" => '',
							"type" => "textfield"
						),
						array(
							"param_name" => "filters_all",
							"heading" => esc_html__('Display the "All" tab', 'trx_addons'),
							"group" => esc_html__("Filters", 'trx_addons'),
							'edit_field_class' => 'vc_col-sm-4',
							"std" => "1",
							"value" => array(esc_html__('Display the "All" tab', 'trx_addons') => "1" ),
							'dependency' => array(
								'element' => 'show_filters',
								'value' => '1'
							),
							"type" => "checkbox"
						),
						array(
							"param_name" => "filters_all_text",
							"heading" => esc_html__('"All" tab text', 'trx_addons'),
							"group" => esc_html__("Filters", 'trx_addons'),
							'edit_field_class' => 'vc_col-sm-4',
							'dependency' => array(
											'element' => 'show_filters',
											'value' => '1'
										),
							"std" => esc_html__('All','trx_addons'),
							"type" => "textfield"
						),
						array(
							"param_name" => "filters_more_text",
							"heading" => esc_html__("'More posts' text", 'trx_addons'),
							"group" => esc_html__("Filters", 'trx_addons'),
							'edit_field_class' => 'vc_col-sm-4',
							'dependency' => array(
								'element' => 'show_filters',
								'is_empty' => true
							),
							"std" => esc_html__('More posts', 'trx_addons'),
							"type" => "textfield"
						),
					),
					$templates_params,
					array(
						array(
							"param_name" => "image_position",
							"heading" => esc_html__("Image position", 'trx_addons'),
							"group" => esc_html__("Details", 'trx_addons'),
							'edit_field_class' => 'vc_col-sm-4 vc_new_row',
							'dependency' => array(
											'element' => 'type',
											'value' => array( 'default', 'wide', 'list', 'news' )
										),
							"std" => 'top',
							"value" => array_flip(trx_addons_get_list_sc_blogger_image_positions()),
							"type" => "dropdown"
						),
						array(
							"param_name" => "image_width",
							"heading" => esc_html__("Image width (in %)", 'trx_addons'),
							"group" => esc_html__("Details", 'trx_addons'),
							'edit_field_class' => 'vc_col-sm-4',
							'dependency' => array(
											'element' => 'image_position',
											'value' => array('left', 'right', 'alter')
										),
							"std" => '40',
							"value" => array_flip( array(
								"10" => __('10%', 'trx_addons'),
								"15" => __('15%', 'trx_addons'),
								"20" => __('20%', 'trx_addons'),
								"25" => __('25%', 'trx_addons'),
								"30" => __('30%', 'trx_addons'),
								"35" => __('35%', 'trx_addons'),
								"40" => __('40%', 'trx_addons'),
								"45" => __('45%', 'trx_addons'),
								"50" => __('50%', 'trx_addons'),
								"55" => __('55%', 'trx_addons'),
								"60" => __('60%', 'trx_addons'),
								"65" => __('65%', 'trx_addons'),
								"70" => __('70%', 'trx_addons'),
								"75" => __('75%', 'trx_addons'),
								"80" => __('80%', 'trx_addons'),
								"85" => __('85%', 'trx_addons'),
								"90" => __('90%', 'trx_addons'),
							) ),
							"type" => "dropdown"
						),
						array(
							"param_name" => "image_ratio",
							"heading" => esc_html__("Image ratio", 'trx_addons'),
							"group" => esc_html__("Details", 'trx_addons'),
							'edit_field_class' => 'vc_col-sm-4',
							'dependency' => array(
											'element' => 'type',
											'value' => array( 'default', 'wide', 'list', 'news', 'cards' )
										),
							"std" => 'none',
							"value" => array_flip( trx_addons_get_list_sc_image_ratio() ),
							"type" => "dropdown"
						),
						array(
							"param_name" => "hover",
							"heading" => esc_html__("Image hover", 'trx_addons'),
							"group" => esc_html__("Details", 'trx_addons'),
							'edit_field_class' => 'vc_col-sm-4',
							"std" => 'inherit',
							"value" => array_flip( trx_addons_get_list_sc_image_hover() ),
							"type" => "dropdown"
						),
						array(
							"param_name" => "meta_parts",
							"heading" => esc_html__("Choose meta parts", 'trx_addons'),
							"group" => esc_html__("Details", 'trx_addons'),
							'edit_field_class' => 'vc_col-sm-4 vc_new_row',
							'dependency' => array(
											'element' => 'type',
											'value' => array( 'default', 'wide', 'list', 'news' )
										),
							'multiple' => true,
							"std" => join( ',', array_keys($meta_parts) ),
							"value" => array_flip( $meta_parts ),
							"type" => "select"
						),
						array(
							"param_name" => "hide_excerpt",
							"heading" => esc_html__("Hide excerpt", 'trx_addons'),
							"group" => esc_html__("Details", 'trx_addons'),
							'edit_field_class' => 'vc_col-sm-4 vc_new_row',
							'dependency' => array(
											'element' => 'type',
											'value' => array( 'default', 'wide', 'list', 'news' )
										),
							"std" => "0",
							"value" => array(esc_html__("Hide excerpt", 'trx_addons') => "1" ),
							"type" => "checkbox"
						),
						array(
							"param_name" => "excerpt_length",
							"heading" => esc_html__("Text length (in words)", 'trx_addons'),
							"group" => esc_html__("Details", 'trx_addons'),
							'edit_field_class' => 'vc_col-sm-4',
							'dependency' => array(
								'element' => 'hide_excerpt',
								'is_empty' => true
							),
							"std" => '',
							"type" => "textfield"
						),
						array(
							"param_name" => "full_post",
							"heading" => esc_html__("Open full post", 'trx_addons'),
							"group" => esc_html__("Details", 'trx_addons'),
							'edit_field_class' => 'vc_col-sm-4',
							"std" => "0",
							"value" => array(esc_html__("Open full post", 'trx_addons') => "1" ),
							'dependency' => array(
								'element' => 'hide_excerpt',
								'is_empty' => true
							),
							"type" => "checkbox"
						),
						array(
							"param_name" => "no_links",
							"heading" => esc_html__("Disable links", 'trx_addons'),
							"group" => esc_html__("Details", 'trx_addons'),
							'edit_field_class' => 'vc_col-sm-4 vc_new_row',
							"std" => "0",
							"value" => array(esc_html__("Disable links", 'trx_addons') => "1" ),
							'dependency' => array(
								'element' => 'full_post',
								'is_empty' => true
							),
							"type" => "checkbox"
						),
						array(
							"param_name" => "more_button",
							"heading" => esc_html__("Show 'More' button", 'trx_addons'),
							"group" => esc_html__("Details", 'trx_addons'),
							'edit_field_class' => 'vc_col-sm-4',
							"std" => "1",
							"value" => array(esc_html__("Show 'More' button", 'trx_addons') => "1" ),
							'dependency' => array(
								'element' => 'no_links',
								'is_empty' => true
							),
							"type" => "checkbox"
						),
						array(
							"param_name" => "more_text",
							"heading" => esc_html__("'More' text", 'trx_addons'),
							"group" => esc_html__("Details", 'trx_addons'),
							'edit_field_class' => 'vc_col-sm-4',
							'dependency' => array(
								'element' => 'no_links',
								'is_empty' => true
							),
							"std" => esc_html__('Read more', 'trx_addons'),
							"type" => "textfield"
						),
						array(
							"param_name" => "on_plate",
							"heading" => esc_html__("On plate", 'trx_addons'),
							"group" => esc_html__("Details", 'trx_addons'),
							'edit_field_class' => 'vc_col-sm-4 vc_new_row',
							'dependency' => array(
											'element' => 'type',
											'value' => array( 'default', 'wide', 'list' )
										),
							"std" => "0",
							"value" => array(esc_html__("On plate", 'trx_addons') => "1" ),
							"type" => "checkbox"
						),
						array(
							"param_name" => "video_in_popup",
							"heading" => esc_html__("Video in the popup", 'trx_addons'),
							"description" => wp_kses_data( __("Open video in the popup window or insert it instead the cover image", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"group" => esc_html__('Details', 'trx_addons'),
							"std" => "0",
							"value" => array(esc_html__("Video in the popup", 'trx_addons') => "1" ),
							"type" => "checkbox"
						),
						array(
							"param_name" => "no_margin",
							"heading" => esc_html__("Remove margin", 'trx_addons'),
							"description" => wp_kses_data( __("Check if you want remove spaces between columns", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"std" => "0",
							"value" => array(esc_html__("Remove margin", 'trx_addons') => "1" ),
							"type" => "checkbox"
						),
						array(
							"param_name" => "text_align",
							"heading" => esc_html__("Text alignment", 'trx_addons'),
							"group" => esc_html__("Details", 'trx_addons'),
							'edit_field_class' => 'vc_col-sm-4',
							'dependency' => array(
											'element' => 'type',
											'value' => array( 'default', 'wide', 'list', 'news', 'cards' )
										),
							"std" => 'none',
							"value" => array_flip( trx_addons_get_list_sc_aligns() ),
							"type" => "dropdown"
						),
						array(
							"param_name" => "numbers",
							"heading" => esc_html__("Show numbers", 'trx_addons'),
							"group" => esc_html__("Details", 'trx_addons'),
							'edit_field_class' => 'vc_col-sm-4',
							'dependency' => array(
											'element' => 'type',
											'value' => array( 'list' )
										),
							"std" => "0",
							"value" => array(esc_html__("Show numbers", 'trx_addons') => "1" ),
							"type" => "checkbox"
						),
						array(
							"param_name" => "date_format",
							"heading" => esc_html__("Date format", 'trx_addons'),
							'description' => sprintf( __( 'See available formats %s', 'trx_addons' ), '<a href="//wordpress.org/support/article/formatting-date-and-time/" target="_blank">' . __( 'here', 'trx_addons') . '</a>' ),
							'edit_field_class' => 'vc_col-sm-4',
							"group" => esc_html__("Details", 'trx_addons'),
							'dependency' => array(
											'element' => 'type',
											'value' => array( 'default', 'wide', 'list', 'news', 'cards' )
										),
							"std" => '',
							"type" => "textfield"
						),
					),
					trx_addons_vc_add_slider_param(),
					trx_addons_vc_add_title_param(),
					trx_addons_vc_add_id_param()
				)
			);
		return apply_filters('trx_addons_sc_map', $params, 'trx_sc_blogger' );
	}
}
