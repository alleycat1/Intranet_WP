<?php
/**
 * Widget: Posts or Revolution slider (WPBakery support)
 *
 * @package ThemeREX Addons
 * @since v1.0
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}



// Add [trx_widget_slider] in the VC shortcodes list
//---------------------------------------------------------------
if (!function_exists('trx_addons_sc_widget_slider_add_in_vc')) {
	function trx_addons_sc_widget_slider_add_in_vc() {

		if (!trx_addons_exists_vc()) return;
		
		vc_lean_map("trx_widget_slider", 'trx_addons_sc_widget_slider_add_in_vc_params');
		class WPBakeryShortCode_Trx_Widget_Slider extends WPBakeryShortCodesContainer {}
	}
	add_action('init', 'trx_addons_sc_widget_slider_add_in_vc', 20);
}

// Return params
if (!function_exists('trx_addons_sc_widget_slider_add_in_vc_params')) {
	function trx_addons_sc_widget_slider_add_in_vc_params() {
		// If open params in VC Editor
		list($vc_edit, $vc_params) = trx_addons_get_vc_form_params('trx_widget_slider');
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
		$tax_obj = get_taxonomy($taxonomy);

		$params = array(
					array(
						"param_name" => "title",
						"heading" => esc_html__("Widget title", 'trx_addons'),
						"description" => wp_kses_data( __("Title of the widget", 'trx_addons') ),
						"admin_label" => true,
						'edit_field_class' => 'vc_col-sm-4',
						"type" => "textfield"
					),
					array(
						"param_name" => "engine",
						"heading" => esc_html__("Slider engine", 'trx_addons'),
						"description" => wp_kses_data( __("Select engine to show slider", 'trx_addons') ),
						"admin_label" => true,
				        'save_always' => true,
						"value" => array_flip(trx_addons_get_list_sc_slider_engines()),
						'edit_field_class' => 'vc_col-sm-4',
						"type" => "dropdown"
					),
					array(
						"param_name" => "slides_type",
						"heading" => esc_html__("Type of the slides content", 'trx_addons'),
						"description" => wp_kses_data( __("Use images from slides as background (default) or insert it as tag inside each slide", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4',
						'dependency' => array(
							'element' => 'engine',
							'value' => array('swiper', 'elastistack')
						),
						"std" => "bg",
						"value" => array(
							esc_html__('Background', 'trx_addons') => 'bg',
							esc_html__('Image tag', 'trx_addons') => 'images'
						),
						"type" => "dropdown"
					)
				);
		if (trx_addons_exists_revslider()) {
			$params[] = array(
						"param_name" => "alias",
						"heading" => esc_html__("RevSlider alias", 'trx_addons'),
						"description" => wp_kses_data( __("Select previously created Revolution slider", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4',
						'dependency' => array(
							'element' => 'engine',
							'value' => 'revo'
						),
						"value" => array_flip(trx_addons_get_list_revsliders()),
				        'save_always' => true,
						"type" => "dropdown"
					);
		}
		$params = array_merge($params,
				array(		
					array(
						"param_name" => "noresize",
						"heading" => esc_html__("No resize slide's content", 'trx_addons'),
						"description" => wp_kses_data( __("Disable resize slide's content, stretch images to cover slide", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4 vc_new_row',
						'dependency' => array(
							'element' => 'engine',
							'value' => array('swiper', 'elastistack')
						),
						"std" => "0",
						"value" => array("No resize slide's content" => "1" ),
						"type" => "checkbox"
					),
					array(
						"param_name" => "slides_ratio",
						"heading" => esc_html__("Slides ratio", 'trx_addons'),
						"description" => wp_kses_data( __("Ratio to resize slides on tabs and mobile. If empty - 16:9", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4',
						'dependency' => array(
							'element' => 'noresize',
							'is_empty' => true
						),
						"std" => "16:9",
						"type" => "textfield"
					),
					array(
						"param_name" => "height",
						"heading" => esc_html__("Slider height", 'trx_addons'),
						"description" => wp_kses_data( __("Initial height of the slider. If empty - calculate from width and aspect ratio", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4',
						'dependency' => array(
							'element' => 'noresize',
							'not_empty' => true
						),
						"type" => "textfield"
					),
					array(
						"param_name" => "slider_style",
						"heading" => esc_html__("Swiper style", 'trx_addons'),
						"description" => wp_kses_data( __("Select style of the Swiper slider", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4 vc_new_row',
						"admin_label" => true,
						'dependency' => array(
							'element' => 'engine',
							'value' => 'swiper'
						),
						"value" => array_flip(trx_addons_components_get_allowed_layouts('widgets', 'slider')),
						"std" => "default",
						"type" => "dropdown"
					),
					array(
						"param_name" => "effect",
						"heading" => esc_html__("Swiper effect", 'trx_addons'),
						"description" => wp_kses_data( __("Select slides effect of the Swiper slider", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4',
						'dependency' => array(
							'element' => 'engine',
							'value' => 'swiper'
						),
						"value" => array_flip(trx_addons_get_list_sc_slider_effects()),
						"std" => "slide",
				        'save_always' => true,
						"type" => "dropdown"
					),
					array(
						"param_name" => "direction",
						"heading" => esc_html__("Direction", 'trx_addons'),
						"description" => wp_kses_data( __("Select direction to change slides", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4',
						'dependency' => array(
							'element' => 'engine',
							'value' => 'swiper'
						),
						"value" => array_flip(trx_addons_get_list_sc_slider_directions()),
						"std" => "horizontal",
				        'save_always' => true,
						"type" => "dropdown"
					),
					array(
						"param_name" => "slides_per_view",
						"heading" => esc_html__("Slides per view in the Swiper", 'trx_addons'),
						"description" => wp_kses_data( __("Specify slides per view in the Swiper", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4 vc_new_row',
						'dependency' => array(
							'element' => 'engine',
							'value' => 'swiper'
						),
						"std" => "1",
						"type" => "textfield"
					),
					array(
						"param_name" => "slides_space",
						"heading" => esc_html__("Space between slides in the Swiper", 'trx_addons'),
						"description" => wp_kses_data( __("Space between slides in the Swiper", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4',
						'dependency' => array(
							'element' => 'engine',
							'value' => 'swiper'
						),
						"value" => "0",
						"type" => "textfield"
					),
					array(
						"param_name" => "slides_parallax",
						"heading" => esc_html__("Parallax coeff", 'trx_addons'),
						"description" => wp_kses_data( __("Parallax coefficient from 0.0 to 1.0 to shift images while slides change", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4',
						'dependency' => array(
							'element' => 'effect',
							'value' => 'slide'
						),
						"value" => "0",
						"type" => "textfield"
					),
					array(
						"param_name" => "speed",
						"heading" => esc_html__("Slides change speed", 'trx_addons'),
						"description" => wp_kses_data( __("Specify slides change speed in the Swiper", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4',
						'dependency' => array(
							'element' => 'engine',
							'value' => 'swiper'
						),
						"std" => "600",
						"type" => "textfield"
					),
					array(
						"param_name" => "interval",
						"heading" => esc_html__("Interval between slides in the Swiper", 'trx_addons'),
						"description" => wp_kses_data( __("Specify interval between slides change in the Swiper", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4',
						'dependency' => array(
							'element' => 'engine',
							'value' => 'swiper'
						),
						"std" => "7000",
						"type" => "textfield"
					),
					array(
						"param_name" => "slides_centered",
						"heading" => esc_html__("Slides centered", 'trx_addons'),
						"description" => wp_kses_data( __("Center active slide", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4 vc_new_row',
						'dependency' => array(
							'element' => 'engine',
							'value' => array('swiper')
						),
						"std" => "0",
						"value" => array(esc_html__("Centered", 'trx_addons') => "1" ),
						"type" => "checkbox"
					),
					array(
						"param_name" => "slides_overflow",
						"heading" => esc_html__("Slides overflow visible", 'trx_addons'),
						"description" => wp_kses_data( __("Don't hide slides outside the borders of the viewport", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4',
						'dependency' => array(
							'element' => 'engine',
							'value' => array('swiper')
						),
						"std" => "0",
						"value" => array(esc_html__("Slides overflow visible", 'trx_addons') => "1" ),
						"type" => "checkbox"
					),
					array(
						"param_name" => "mouse_wheel",
						"heading" => esc_html__("Enable mouse wheel", 'trx_addons'),
						"description" => wp_kses_data( __("Enable mouse wheel to control slides", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4',
						'dependency' => array(
							'element' => 'engine',
							'value' => array('swiper')
						),
						"std" => "0",
						"value" => array(esc_html__("Enable", 'trx_addons') => "1" ),
						"type" => "checkbox"
					),
					array(
						"param_name" => "autoplay",
						"heading" => esc_html__("Enable autoplay", 'trx_addons'),
						"description" => wp_kses_data( __("Enable autoplay for this slider", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4',
						'dependency' => array(
							'element' => 'engine',
							'value' => array('swiper')
						),
						"std" => "1",
						"value" => array(esc_html__("Enable", 'trx_addons') => "1" ),
						"type" => "checkbox"
					),
					array(
						"param_name" => "free_mode",
						"heading" => esc_html__("Enable free mode", 'trx_addons'),
						"description" => wp_kses_data( __("Free mode - slides will not have fixed positions", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4',
						'dependency' => array(
							'element' => 'engine',
							'value' => array('swiper')
						),
						"std" => "0",
						"value" => array(esc_html__("Enable", 'trx_addons') => "1" ),
						"type" => "checkbox"
					),
					array(
						"param_name" => "controls",
						"heading" => esc_html__("Controls", 'trx_addons'),
						"description" => wp_kses_data( __("Do you want to show arrows to change slides?", 'trx_addons') ),
						"group" => esc_html__('Controls', 'trx_addons'),
						'edit_field_class' => 'vc_col-sm-4 vc_new_row',
						'dependency' => array(
							'element' => 'engine',
							'value' => array('swiper', 'elastistack')
						),
						"std" => "0",
						"value" => array(esc_html__("Show arrows", 'trx_addons') => "1" ),
						"type" => "checkbox"
					),
					array(
						"param_name" => "controls_pos",
						"heading" => esc_html__("Controls position", 'trx_addons'),
						"description" => wp_kses_data( __("Select controls position", 'trx_addons') ),
						"group" => esc_html__('Controls', 'trx_addons'),
						'edit_field_class' => 'vc_col-sm-4',
						'dependency' => array(
							'element' => 'controls',
							'not_empty' => true
						),
						"std" => "side",
				        'save_always' => true,
						"value" => array_flip(trx_addons_get_list_sc_slider_controls('')),
						"type" => "dropdown"
					),
					array(
						"param_name" => "label_prev",
						"heading" => esc_html__("Prev Slide", 'trx_addons'),
						"description" => wp_kses_data( __("Label of the 'Prev Slide' button in the Swiper (Modern style). Use '|' to break line", 'trx_addons') ),
						"group" => esc_html__('Controls', 'trx_addons'),
						'edit_field_class' => 'vc_col-sm-4 vc_col-sm-offset-4 vc_new_row',
						'dependency' => array(
								'element' => 'controls',
								'not_empty' => true
						),
						"std" => esc_html__('Prev|PHOTO', 'trx_addons'),
						"type" => "textfield"
					),
					array(
						"param_name" => "label_next",
						"heading" => esc_html__("Next Slide", 'trx_addons'),
						"description" => wp_kses_data( __("Label of the 'Next Slide' button in the Swiper (Modern style). Use '|' to break line", 'trx_addons') ),
						"group" => esc_html__('Controls', 'trx_addons'),
						'edit_field_class' => 'vc_col-sm-4',
						'dependency' => array(
								'element' => 'controls',
								'not_empty' => true
						),
						"std" => esc_html__('Next|PHOTO', 'trx_addons'),
						"type" => "textfield"
					),
					array(
						"param_name" => "pagination",
						"heading" => esc_html__("Pagination", 'trx_addons'),
						"description" => wp_kses_data( __("Do you want to show bullets to change slides?", 'trx_addons') ),
						"group" => esc_html__('Controls', 'trx_addons'),
						'edit_field_class' => 'vc_col-sm-4 vc_new_row',
						'dependency' => array(
							'element' => 'engine',
							'value' => 'swiper'
						),
						"std" => "0",
						"value" => array(esc_html__("Show pagination", 'trx_addons') => "1" ),
						"type" => "checkbox"
					),
					array(
						"param_name" => "pagination_type",
						"heading" => esc_html__("Pagination type", 'trx_addons'),
						"description" => wp_kses_data( __("Select type of the pagination", 'trx_addons') ),
						"group" => esc_html__('Controls', 'trx_addons'),
						'edit_field_class' => 'vc_col-sm-4',
						'dependency' => array(
							'element' => 'pagination',
							'not_empty' => true
						),
						"std" => "bullets",
				        'save_always' => true,
						"value" => array_flip(trx_addons_get_list_sc_slider_paginations_types()),
						"type" => "dropdown"
					),
					array(
						"param_name" => "pagination_pos",
						"heading" => esc_html__("Pagination position", 'trx_addons'),
						"description" => wp_kses_data( __("Select pagination position", 'trx_addons') ),
						"group" => esc_html__('Controls', 'trx_addons'),
						'edit_field_class' => 'vc_col-sm-4',
						'dependency' => array(
							'element' => 'pagination',
							'not_empty' => true
						),
						"std" => "bottom",
				        'save_always' => true,
						"value" => array_flip(trx_addons_get_list_sc_slider_paginations('')),
						"type" => "dropdown"
					),
					array(
						"param_name" => "noswipe",
						"heading" => esc_html__("Disable swipe", 'trx_addons'),
						"description" => wp_kses_data( __("Disable swipe guestures", 'trx_addons') ),
						"group" => esc_html__('Controls', 'trx_addons'),
						'dependency' => array(
							'element' => 'engine',
							'value' => array('swiper')
						),
						"std" => "0",
						"value" => array(esc_html__("Disable swipe guestures", 'trx_addons') => "1" ),
						"type" => "checkbox"
					),
					array(
						"param_name" => "slave_id",
						"heading" => esc_html__("Slave ID", 'trx_addons'),
						"description" => wp_kses_data( __("Specify ID of the controlled (slave) slider", 'trx_addons') ),
						'dependency' => array(
							'element' => 'engine',
							'value' => 'swiper'
						),
						"std" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "titles",
						"heading" => esc_html__("Titles in the Swiper", 'trx_addons'),
						"description" => wp_kses_data( __("Show post's titles and categories on the slides", 'trx_addons') ),
						"group" => esc_html__('Titles', 'trx_addons'),
						'edit_field_class' => 'vc_col-sm-4 vc_new_row',
						'dependency' => array(
							'element' => 'engine',
							'value' => array('swiper', 'elastistack')
						),
						"std" => "center",
				        'save_always' => true,
						"value" => array_flip(trx_addons_get_list_sc_slider_titles()),
						"type" => "dropdown"
					),
					array(
						"param_name" => "large",
						"heading" => esc_html__("Large titles", 'trx_addons'),
						"description" => wp_kses_data( __("Do you want use large titles?", 'trx_addons') ),
						"group" => esc_html__('Titles', 'trx_addons'),
						'edit_field_class' => 'vc_col-sm-4',
						'dependency' => array(
							'element' => 'engine',
							'value' => array('swiper', 'elastistack')
						),
						"std" => "0",
						"value" => array(esc_html__("Large titles", 'trx_addons') => "1" ),
						"type" => "checkbox"
					),

					array(
						"param_name" => "post_type",
						"heading" => esc_html__("Post type", 'trx_addons'),
						"description" => wp_kses_data( __("Select post type to get featured images from the posts", 'trx_addons') ),
						"group" => esc_html__('Slides', 'trx_addons'),
						'edit_field_class' => 'vc_col-sm-3',
						'dependency' => array(
							'element' => 'engine',
							'value' => array('swiper', 'elastistack')
						),
						"std" => 'post',
						"value" => array_flip(trx_addons_get_list_posts_types()),
						"type" => "dropdown"
					),
					array(
						"param_name" => "taxonomy",
						"heading" => esc_html__("Taxonomy", 'trx_addons'),
						"description" => wp_kses_data( __("Select taxonomy to get featured images from the posts", 'trx_addons') ),
						"group" => esc_html__('Slides', 'trx_addons'),
						'edit_field_class' => 'vc_col-sm-3',
						'dependency' => array(
							'element' => 'engine',
							'value' => array('swiper', 'elastistack')
						),
						"std" => 'category',
						"value" => array_flip($taxonomies),
						"type" => "dropdown"
					),
					array(
						"param_name" => "category",
						"heading" => esc_html__("Category", 'trx_addons'),
						"description" => wp_kses_data( __("Select category to get featured images from the posts", 'trx_addons') ),
						"group" => esc_html__('Slides', 'trx_addons'),
						'edit_field_class' => 'vc_col-sm-3',
						'dependency' => array(
							'element' => 'engine',
							'value' => array('swiper', 'elastistack')
						),
						"std" => 0,
						"value" => array_flip( trx_addons_array_merge(
													array( 0 => trx_addons_get_not_selected_text( ! empty( $tax_obj->label ) ? $tax_obj->label : __( '- Not Selected -', 'trx_addons' ) ) ),
													$taxonomy == 'category' 
													 	? trx_addons_get_list_categories() 
														: trx_addons_get_list_terms(false, $taxonomy)
											) ),
						"type" => "dropdown"
					),
					array(
						"param_name" => "posts",
						"heading" => esc_html__("Posts number", 'trx_addons'),
						"description" => wp_kses_data( __("Number of posts or comma separated post's IDs to show images", 'trx_addons') ),
						"group" => esc_html__('Slides', 'trx_addons'),
						'edit_field_class' => 'vc_col-sm-3',
						'dependency' => array(
							'element' => 'engine',
							'value' => array('swiper', 'elastistack')
						),
						"std" => "5",
						"type" => "textfield"
					),
					array(
						'param_name' => 'slides',
						'heading' => esc_html__( 'or create custom slides', 'trx_addons' ),
						"description" => wp_kses_data( __("Select icons, specify title and/or description for each item", 'trx_addons') ),
						"group" => esc_html__('Slides', 'trx_addons'),
						'dependency' => array(
							'element' => 'engine',
							'value' => array('swiper', 'elastistack')
						),
						'value' => '',
						'type' => 'param_group',
						'params' => apply_filters('trx_addons_sc_param_group_params', array(
							array(
								'param_name' => 'title',
								'heading' => esc_html__( 'Title', 'trx_addons' ),
								'description' => esc_html__( 'Enter title of this slide', 'trx_addons' ),
								'edit_field_class' => 'vc_col-sm-4',
								'admin_label' => true,
								'type' => 'textfield'
							),
							array(
								'param_name' => 'subtitle',
								'heading' => esc_html__( 'Subtitle', 'trx_addons' ),
								'description' => esc_html__( 'Enter subtitle of this slide', 'trx_addons' ),
								'edit_field_class' => 'vc_col-sm-4',
								'type' => 'textfield'
							),
							array(
								'param_name' => 'link',
								'heading' => esc_html__( 'Link', 'trx_addons' ),
								'description' => esc_html__( 'URL to link of this slide', 'trx_addons' ),
								'edit_field_class' => 'vc_col-sm-4',
								'type' => 'textfield'
							),
							array(
								"param_name" => "image",
								"heading" => esc_html__("Image", 'trx_addons'),
								"description" => wp_kses_data( __("Select or upload image or specify URL from other site", 'trx_addons') ),
								"type" => "attach_image"
							),
							array(
								'param_name' => 'video_url',
								'heading' => esc_html__( 'Video URL', 'trx_addons' ),
								'description' => esc_html__( 'Enter link to the video (Note: read more about available formats at WordPress Codex page)', 'trx_addons' ),
								'edit_field_class' => 'vc_col-sm-6',
								'type' => 'textfield'
							),
							array(
								'param_name' => 'video_embed',
								'heading' => esc_html__( 'Video embed code', 'trx_addons' ),
								'description' => esc_html__( 'or paste the HTML code to embed video in this slide', 'trx_addons' ),
								'edit_field_class' => 'vc_col-sm-6',
								'type' => 'textarea'
							)
						), 'trx_widget_slider')
					)
				),
				trx_addons_vc_add_id_param()
			);
		
		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_widget_slider",
				"name" => esc_html__("Widget: Slider", 'trx_addons'),
				"description" => wp_kses_data( __("Insert widget with slider", 'trx_addons') ),
				"category" => esc_html__('ThemeREX', 'trx_addons'),
				"icon" => 'icon_trx_widget_slider',
				"class" => "trx_widget_slider",
				"content_element" => true,
				'is_container' => true,
				'as_child' => array('except' => 'trx_widget_slider'),
				"js_view" => 'VcTrxAddonsContainerView',	//'VcColumnView',
				"as_parent" => array('only' => 'trx_slide'),
				"show_settings_on_create" => true,
				"params" => $params
			), 'trx_widget_slider' );
	}
}



// Add [trx_slide] in the VC shortcodes list
//------------------------------------------------------
if (!function_exists('trx_addons_sc_slide_add_in_vc')) {
	function trx_addons_sc_slide_add_in_vc() {

		if (!trx_addons_exists_vc()) return;
		
		vc_lean_map("trx_slide", 'trx_addons_sc_slide_add_in_vc_params');
		class WPBakeryShortCode_Trx_Slide extends WPBakeryShortCodesContainer {}
	}
	add_action('init', 'trx_addons_sc_slide_add_in_vc', 20);
}


// Return params
if (!function_exists('trx_addons_sc_slide_add_in_vc_params')) {
	function trx_addons_sc_slide_add_in_vc_params() {
		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_slide",
				"name" => esc_html__("Custom Slide", 'trx_addons'),
				"description" => wp_kses_data( __("Insert the custom slide in the slider", 'trx_addons') ),
				"category" => esc_html__('ThemeREX', 'trx_addons'),
				"icon" => 'icon_trx_slide',
				"class" => "trx_slide",
				"content_element" => true,
				'is_container' => true,
				"js_view" => 'VcTrxAddonsContainerView',	//'VcColumnView',
				"as_child" => array('only' => 'trx_widget_slider'),
				"as_parent" => array('except' => 'trx_widget_slider,trx_slide'),
				"show_settings_on_create" => true,
				"params" => array_merge(
					array(
						array(
							'param_name' => 'title',
							'heading' => esc_html__( 'Title', 'trx_addons' ),
							'description' => esc_html__( 'Enter title of this slide', 'trx_addons' ),
							'edit_field_class' => 'vc_col-sm-4',
							'admin_label' => true,
							'type' => 'textfield'
						),
						array(
							'param_name' => 'subtitle',
							'heading' => esc_html__( 'Subtitle', 'trx_addons' ),
							'description' => esc_html__( 'Enter subtitle of this slide', 'trx_addons' ),
							'edit_field_class' => 'vc_col-sm-4',
							'type' => 'textfield'
						),
						array(
							'param_name' => 'link',
							'heading' => esc_html__( 'Link', 'trx_addons' ),
							'description' => esc_html__( 'URL to link of this slide', 'trx_addons' ),
							'edit_field_class' => 'vc_col-sm-4',
							'type' => 'textfield'
						),
						array(
							"param_name" => "image",
							"heading" => esc_html__("Image", 'trx_addons'),
							"description" => wp_kses_data( __("Select or upload image or specify URL from other site", 'trx_addons') ),
							"type" => "attach_image"
						),
						array(
							'param_name' => 'video_url',
							'heading' => esc_html__( 'Video URL', 'trx_addons' ),
							'description' => esc_html__( 'Enter link to the video (Note: read more about available formats at WordPress Codex page)', 'trx_addons' ),
							'edit_field_class' => 'vc_col-sm-4',
							'type' => 'textfield'
						),
						array(
							'param_name' => 'video_embed',
							'heading' => esc_html__( 'Video embed code', 'trx_addons' ),
							'description' => esc_html__( 'or paste the HTML code to embed video in this slide', 'trx_addons' ),
							'edit_field_class' => 'vc_col-sm-8',
							'type' => 'textarea'
						)
					),
					trx_addons_vc_add_id_param()
				)
			), 'trx_slide' );
	}
}



// Add [trx_slider_controller] in the VC shortcodes list
//------------------------------------------------------------------
if (!function_exists('trx_addons_sc_slider_controller_add_in_vc')) {
	function trx_addons_sc_slider_controller_add_in_vc() {

		if (!trx_addons_exists_vc()) return;
		
		vc_lean_map("trx_slider_controller", 'trx_addons_sc_slider_controller_add_in_vc_params');
		class WPBakeryShortCode_Trx_Slider_Controller extends WPBakeryShortCode {}
	}
	add_action('init', 'trx_addons_sc_slider_controller_add_in_vc', 20);
}

// Return params
if (!function_exists('trx_addons_sc_slider_controller_add_in_vc_params')) {
	function trx_addons_sc_slider_controller_add_in_vc_params() {
		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_slider_controller",
				"name" => esc_html__("Slider Controller", 'trx_addons'),
				"description" => wp_kses_data( __("Insert controller for the specified slider", 'trx_addons') ),
				"category" => esc_html__('ThemeREX', 'trx_addons'),
				"icon" => 'icon_trx_slider_controller',
				"class" => "trx_slider_controller",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array_merge(
					array(
						array(
							"param_name" => "slider_id",
							"heading" => esc_html__("Slave slider ID", 'trx_addons'),
							"description" => wp_kses_data( __("ID of the slave slider", 'trx_addons') ),
							'admin_label' => true,
							'edit_field_class' => 'vc_col-sm-4',
							"type" => "textfield"
						),
						array(
							"param_name" => "height",
							"heading" => esc_html__("Controller height", 'trx_addons'),
							"description" => wp_kses_data( __("Controller height", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"type" => "textfield"
						),
						array(
							"param_name" => "controls",
							"heading" => esc_html__("Controls", 'trx_addons'),
							"description" => wp_kses_data( __("Do you want to show arrows to change slides?", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"std" => "0",
							"value" => array("Show arrows" => "1" ),
							"type" => "checkbox"
						),
						array(
							"param_name" => "controller_style",
							"heading" => esc_html__("Style", 'trx_addons'),
							"description" => wp_kses_data( __("Select style of the Controller", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4 vc_new_row',
							'admin_label' => true,
					        'save_always' => true,
							"value" => array_flip( trx_addons_get_list_sc_slider_controller_styles() ),
							"std" => "thumbs",
							"type" => "dropdown"
						),
						array(
							"param_name" => "effect",
							"heading" => esc_html__("Effect", 'trx_addons'),
							"description" => wp_kses_data( __("Select slides effect of the Controller", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"value" => array_flip(trx_addons_get_list_sc_slider_effects()),
							"std" => "slide",
							"type" => "dropdown"
						),
						array(
							"param_name" => "direction",
							"heading" => esc_html__("Direction", 'trx_addons'),
							"description" => wp_kses_data( __("Select direction to change slides", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"std" => 'horizontal',
							"value" => array_flip(trx_addons_get_list_sc_slider_directions()),
							"type" => "dropdown"
						),
						array(
							"param_name" => "slides_per_view",
							"heading" => esc_html__("Slides per view", 'trx_addons'),
							"description" => wp_kses_data( __("Specify slides per view in the Controller", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4 vc_new_row',
							"std" => "1",
							"type" => "textfield"
						),
						array(
							"param_name" => "slides_space",
							"heading" => esc_html__("Space between slides", 'trx_addons'),
							"description" => wp_kses_data( __("Space between slides in the Controller", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"std" => "0",
							"type" => "textfield"
						),
						array(
							"param_name" => "interval",
							"heading" => esc_html__("Interval between slides", 'trx_addons'),
							"description" => wp_kses_data( __("Specify interval between slides change in the Controller", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"std" => "7000",
							"type" => "textfield"
						),
					),
					trx_addons_vc_add_id_param()
				)
			), 'trx_slider_controller' );
	}
}



// Add [trx_slider_controls] in the VC shortcodes list
//----------------------------------------------------------------
if (!function_exists('trx_addons_sc_slider_controls_add_in_vc')) {
	function trx_addons_sc_slider_controls_add_in_vc() {

		if (!trx_addons_exists_vc()) return;
		
		vc_lean_map("trx_slider_controls", 'trx_addons_sc_slider_controls_add_in_vc_params');
		class WPBakeryShortCode_Trx_Slider_Controls extends WPBakeryShortCode {}
	}
	add_action('init', 'trx_addons_sc_slider_controls_add_in_vc', 20);
}

// Return params
if (!function_exists('trx_addons_sc_slider_controls_add_in_vc_params')) {
	function trx_addons_sc_slider_controls_add_in_vc_params() {
		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_slider_controls",
				"name" => esc_html__("Slider Controls", 'trx_addons'),
				"description" => wp_kses_data( __("Insert separate arrows for the specified slider", 'trx_addons') ),
				"category" => esc_html__('ThemeREX', 'trx_addons'),
				"icon" => 'icon_trx_slider_controls',
				"class" => "trx_slider_controls",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array_merge(
					array(
						array(
							"param_name" => "slider_id",
							"heading" => esc_html__("Slave slider ID", 'trx_addons'),
							"description" => wp_kses_data( __("ID of the slave slider", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							'admin_label' => true,
							"type" => "textfield"
						),
						array(
							"param_name" => "controls_style",
							"heading" => esc_html__("Style", 'trx_addons'),
							"description" => wp_kses_data( __("Select style of the arrows", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"value" => array_flip( trx_addons_get_list_sc_slider_controls_styles() ),
							"std" => "default",
							"type" => "dropdown"
						),
						array(
							"param_name" => "align",
							"heading" => esc_html__("Alignment", 'trx_addons'),
							"description" => wp_kses_data( __("Select alignment of the arrows", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"value" => array_flip(trx_addons_get_list_sc_aligns(false, false)),
							"std" => "left",
					        'save_always' => true,
							"type" => "dropdown"
						),
						array(
							"param_name" => "hide_prev",
							"heading" => esc_html__("Hide the button 'Prev'", 'trx_addons'),
							"description" => wp_kses_data( __("Do you want to hide arrow 'Prev'?", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4 vc_new_row',
							"std" => "0",
							"value" => array("Hide 'Prev'" => "1" ),
							"type" => "checkbox"
						),
						array(
							"param_name" => "title_prev",
							"heading" => esc_html__("Title of the button 'Prev'", 'trx_addons'),
							"description" => wp_kses_data( __("Specify title of the button 'Prev'. If empty - display arrow", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-8',
							'dependency' => array(
								'element' => 'hide_prev',
								'is_empty' => true
							),
							"std" => "",
							"type" => "textfield"
						),
						array(
							"param_name" => "hide_next",
							"heading" => esc_html__("Hide the button 'Next'", 'trx_addons'),
							"description" => wp_kses_data( __("Do you want to hide arrow 'Next'?", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4 vc_new_row',
							"std" => "0",
							"value" => array("Hide 'Next'" => "1" ),
							"type" => "checkbox"
						),
						array(
							"param_name" => "title_next",
							"heading" => esc_html__("Title of button 'Next'", 'trx_addons'),
							"description" => wp_kses_data( __("Specify title of the button 'Next'. If empty - display arrow", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-8',
							'dependency' => array(
								'element' => 'hide_next',
								'is_empty' => true
							),
							"std" => "",
							"type" => "textfield"
						),
						array(
							"param_name" => "pagination_style",
							"heading" => esc_html__("Show pagination", 'trx_addons'),
							"description" => wp_kses_data( __("Select type of a pagination of the specified slider?", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4 vc_new_row',
							'admin_label' => true,
							"value" => array_flip(trx_addons_get_list_sc_slider_controls_paginations_types()),
							"std" => "none",
							"type" => "dropdown"
						),
					),
					trx_addons_vc_add_id_param()
				)
			), 'trx_slider_controls' );
	}
}
