<?php
/**
 * ThemeREX Addons Custom post type: Services (Widget)
 *
 * @package ThemeREX Addons
 * @since v1.4
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}



// TRX_Addons Widget
//------------------------------------------------------
if ( ! class_exists('TRX_Addons_SOW_Widget_Services') ) {

	class TRX_Addons_SOW_Widget_Services extends TRX_Addons_Widget {
	
		function __construct() {
			$widget_ops = array('classname' => 'widget_services', 'description' => esc_html__('Show services items', 'trx_addons'));
			parent::__construct( 'trx_addons_sow_widget_services', esc_html__('ThemeREX Services', 'trx_addons'), $widget_ops );
		}
	
		// Show widget
		function widget($args, $instance) {
			extract($args);
	
			$widget_title = apply_filters('widget_title', isset($instance['widget_title']) ? $instance['widget_title'] : '', $instance, $this->id_base);
	
			$output = trx_addons_sc_services(apply_filters('trx_addons_filter_widget_args',
														$instance,
														$instance, 'trx_addons_sow_widget_services')
														);
	
			if (!empty($output)) {
		
				// Before widget (defined by themes)
				trx_addons_show_layout($before_widget);
				
				// Display the widget title if one was input (before and after defined by themes)
				if ($widget_title) trx_addons_show_layout($before_title . $widget_title . $after_title);
		
				// Display widget body
				trx_addons_show_layout($output);
				
				// After widget (defined by themes)
				trx_addons_show_layout($after_widget);
			}
		}
	
		// Update the widget settings
		function update($new_instance, $instance) {
			$instance = array_merge($instance, $new_instance);
			$instance['hide_excerpt'] = isset( $new_instance['hide_excerpt'] ) && (int)$new_instance['hide_excerpt'] > 0 ? 1 : 0;
			$instance['no_margin'] = isset( $new_instance['no_margin'] ) && (int)$new_instance['no_margin'] > 0 ? 1 : 0;
			$instance['no_links'] = isset( $new_instance['no_links'] ) && (int)$new_instance['no_links'] > 0 ? 1 : 0;
			$instance['hide_bg_image'] = isset( $new_instance['hide_bg_image'] ) && (int)$new_instance['hide_bg_image'] > 0 ? 1 : 0;
			$instance['icons_animation'] = isset( $new_instance['icons_animation'] ) && (int)$new_instance['icons_animation'] > 0 ? 1 : 0;
			$instance['popup'] = isset( $new_instance['popup'] ) && (int)$new_instance['popup'] > 0 ? 1 : 0;
			$instance['slider'] = isset( $new_instance['slider'] ) && (int)$new_instance['slider'] > 0 ? 1 : 0;
			$instance['slides_centered'] = isset( $new_instance['slides_centered'] ) && (int)$new_instance['slides_centered'] > 0 ? 1 : 0;
			$instance['slides_overflow'] = isset( $new_instance['slides_overflow'] ) && (int)$new_instance['slides_overflow'] > 0 ? 1 : 0;
			$instance['slider_mouse_wheel'] = isset( $new_instance['slider_mouse_wheel'] ) && (int)$new_instance['slider_mouse_wheel'] > 0 ? 1 : 0;
			$instance['slider_autoplay'] = isset( $new_instance['slider_autoplay'] ) && (int)$new_instance['slider_autoplay'] > 0 ? 1 : 0;
			$instance['slider_loop'] = isset( $new_instance['slider_loop'] ) && (int)$new_instance['slider_loop'] > 0 ? 1 : 0;
			$instance['slider_free_mode'] = isset( $new_instance['slider_free_mode'] ) && (int)$new_instance['slider_free_mode'] > 0 ? 1 : 0;
			return apply_filters('trx_addons_filter_widget_args_update', $instance, $new_instance, 'trx_addons_sow_widget_services');
		}
	
		// Displays the widget settings controls on the widget panel
		function form($instance) {
			// Set up some default widget settings
			$instance = wp_parse_args( (array) $instance, apply_filters('trx_addons_filter_widget_args_default', array(
				'widget_title' => '',
				// Layout params
				"type" => "default",
				"featured" => "image",
				"featured_position" => "top",
				"tabs_effect" => "fade",
				"hide_excerpt" => 0,
				"hide_bg_image" => 0,
				"icons_animation" => 0,
				"popup" => 0,
				"no_margin" => 0,
				"no_links" => 0,
				"more_text" => __('Read more', 'trx_addons'),
				// Query params
				'pagination' => 'none',
				'post_type' => TRX_ADDONS_CPT_SERVICES_PT,
				'taxonomy' => TRX_ADDONS_CPT_SERVICES_TAXONOMY,
				"cat" => '',
				"count" => 3,
				"columns" => '',
				"offset" => 0,
				"orderby" => 'date',
				"order" => 'desc',
				"ids" => '',
				// Slider params
				"slider" => 0,
				"slider_effect" => "slide",
				"slider_pagination" => "none",
				"slider_controls" => "none",
				"slides_space" => 0,
				"slides_centered" => 0,
				"slides_overflow" => 0,
				"slider_mouse_wheel" => 0,
				"slider_autoplay" => 1,
				"slider_loop" => 1,
				"slider_free_mode" => 0,
				// Title params
				"title" => "",
				"subtitle" => "",
				"subtitle_align" => "none",
				"subtitle_position" => trx_addons_get_setting('subtitle_above_title') ? 'above' : 'below',
				"description" => "",
				"link" => '',
				"link_style" => 'default',
				"link_image" => '',
				"link_text" => __('Learn more', 'trx_addons'),
				"title_align" => "left",
				"title_style" => "default",
				"title_tag" => '',
				"title_color" => '',
				"title_color2" => '',
				"gradient_fill" => 'block',
				"gradient_direction" => '',
				// Common params
				"id" => "",
				"class" => "",
				"css" => ""
				), 'trx_addons_sow_widget_services')
			);
		
			do_action('trx_addons_action_before_widget_fields', $instance, 'trx_addons_sow_widget_services', $this);

			$this->show_field(array('name' => 'widget_title',
									'title' => __('Widget title:', 'trx_addons'),
									'value' => $instance['widget_title'],
									'type' => 'text'));
		
			do_action('trx_addons_action_after_widget_title', $instance, 'trx_addons_sow_widget_services', $this);
			
			$this->show_field(array('title' => __('Layout parameters', 'trx_addons'),
									'type' => 'info'));
			
			$this->show_field(array('name' => 'type',
									'title' => __('Layout:', 'trx_addons'),
									'value' => $instance['type'],
									'options' => apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('cpt', 'services', 'sc'), 'trx_widget_services'),
									'type' => 'select'));
			
			$this->show_field(array('name' => 'featured',
									'title' => __('Featured element:', 'trx_addons'),
									'value' => $instance['featured'],
									'options' => trx_addons_get_list_sc_services_featured(),
									'dependency' => array(
										'type' => array( 'default', 'callouts', 'hover', 'light', 'list', 'iconed', 'tabs', 'tabs_simple', 'timeline' )
									),
									'type' => 'radio'));
			
			$this->show_field(array('name' => 'featured_position',
									'title' => __('Featured position:', 'trx_addons'),
									'description' => wp_kses_data( __("Select the position of the featured element. Attention! Use 'Bottom' only with 'Callouts' or 'Timeline'", 'trx_addons') ),
									'value' => $instance['featured_position'],
									'options' => trx_addons_get_list_sc_services_featured_positions(),
									'dependency' => array(
										'featured' => array( 'image', 'icon', 'number', 'pictogram' )
									),
									'type' => 'radio'));

			$this->show_field(array('name' => 'hide_excerpt',
									'title' => '',
									'label' => __('Hide excerpt', 'trx_addons'),
									'value' => (int) $instance['hide_excerpt'],
									'type' => 'checkbox'));

			$this->show_field(array('name' => 'no_margin',
									'title' => '',
									'label' => __('Remove margin between columns', 'trx_addons'),
									'value' => (int) $instance['no_margin'],
									'type' => 'checkbox'));

			$this->show_field(array('name' => 'no_links',
									'title' => '',
									'label' => __('Disable links', 'trx_addons'),
									'value' => (int) $instance['no_links'],
									'type' => 'checkbox'));
			
			$this->show_field(array('name' => 'more_text',
									'title' => __("'More' text", 'trx_addons'),
									'value' => (int) $instance['more_text'],
									'dependency' => array(
										'no_links' => array( 0 )
									),
									'type' => 'text'));

			$this->show_field(array('name' => 'hide_bg_image',
									'title' => '',
									'label' => __('Hide bg image on "Hover" style', 'trx_addons'),
									'value' => (int) $instance['hide_bg_image'],
									'type' => 'checkbox'));

			$this->show_field(array('name' => 'icons_animation',
									'title' => '',
									'description' => __('Attention! Animation is enabled only if there is an .SVG  icon in your theme with the same name as the selected icon.', 'trx_addons'),
									'label' => __('Animate icons', 'trx_addons'),
									'value' => (int) $instance['icons_animation'],
									'type' => 'checkbox'));

			$this->show_field(array('name' => 'popup',
									'title' => '',
									'label' => __('Details in the popup', 'trx_addons'),
									'value' => (int) $instance['popup'],
									'type' => 'checkbox'));

			$this->show_field(array('title' => __('Query parameters', 'trx_addons'),
									'type' => 'info'));

			$this->show_field(array('name' => 'pagination',
									'title' => __('Pagination:', 'trx_addons'),
									'value' => $instance['pagination'],
									'options' => trx_addons_get_list_sc_paginations(),
									'type' => 'select'));

			$this->show_field(array('name' => 'post_type',
									'title' => __('Post type:', 'trx_addons'),
									'value' => $instance['post_type'],
									'options' => trx_addons_get_list_posts_types(),
									'class' => 'trx_addons_post_type_selector',
									'type' => 'select'));
			
			$this->show_field(array('name' => 'taxonomy',
									'title' => __('Taxonomy:', 'trx_addons'),
									'value' => $instance['taxonomy'],
									'options' => trx_addons_get_list_taxonomies(false, $instance['post_type']),
									'class' => 'trx_addons_taxonomy_selector',
									'type' => 'select'));


			$tax_obj = get_taxonomy($instance['taxonomy']);

			$this->show_field(array('name' => 'cat',
									'title' => __('Services Group:', 'trx_addons'),
									'value' => $instance['cat'],
									'options' => trx_addons_array_merge(
													array( 0 => trx_addons_get_not_selected_text( ! empty( $tax_obj->label ) ? $tax_obj->label : __( '- Not Selected -', 'trx_addons' ) ) ),
													trx_addons_get_list_terms( false, $instance['taxonomy'], array('pad_counts' => true ) )
												),
									'class' => 'trx_addons_terms_selector',
									'type' => 'select'));
			
			$this->show_fields_query_param($instance, '');
			$this->show_fields_slider_param($instance);
			$this->show_fields_title_param($instance);
			$this->show_fields_id_param($instance);
		
			do_action('trx_addons_action_after_widget_fields', $instance, 'trx_addons_sow_widget_services', $this);
		}
	}

	// Load widget
	if (!function_exists('trx_addons_sow_widget_services_load')) {
		add_action( 'widgets_init', 'trx_addons_sow_widget_services_load' );
		function trx_addons_sow_widget_services_load() {
			register_widget('TRX_Addons_SOW_Widget_Services');
		}
	}
}
