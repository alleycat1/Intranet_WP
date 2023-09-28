<?php
/**
 * ThemeREX Addons Custom post type: Testimonials (Widget)
 *
 * @package ThemeREX Addons
 * @since v1.2
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}



// TRX_Addons Widget
//------------------------------------------------------
if ( ! class_exists('TRX_Addons_SOW_Widget_Testimonials') ) {

	class TRX_Addons_SOW_Widget_Testimonials extends TRX_Addons_Widget {
	
		function __construct() {
			$widget_ops = array('classname' => 'widget_testimonials', 'description' => esc_html__('Show Testimonials', 'trx_addons'));
			parent::__construct( 'trx_addons_sow_widget_testimonials', esc_html__('ThemeREX Testimonials', 'trx_addons'), $widget_ops );
		}
	
		// Show widget
		function widget($args, $instance) {
			extract($args);
	
			$widget_title = apply_filters('widget_title', isset($instance['widget_title']) ? $instance['widget_title'] : '', $instance, $this->id_base);
	
			$output = trx_addons_sc_testimonials(apply_filters('trx_addons_filter_widget_args',
																$instance,
																$instance, 'trx_addons_sow_widget_testimonials')
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
			$instance['slider'] = isset( $new_instance['slider'] ) && (int)$new_instance['slider'] > 0 ? 1 : 0;
			$instance['slider_pagination_thumbs'] = isset( $new_instance['slider_pagination_thumbs'] ) && (int)$new_instance['slider_pagination_thumbs'] > 0 ? 1 : 0;
			$instance['slides_centered'] = isset( $new_instance['slides_centered'] ) && (int)$new_instance['slides_centered'] > 0 ? 1 : 0;
			$instance['slides_overflow'] = isset( $new_instance['slides_overflow'] ) && (int)$new_instance['slides_overflow'] > 0 ? 1 : 0;
			$instance['slider_mouse_wheel'] = isset( $new_instance['slider_mouse_wheel'] ) && (int)$new_instance['slider_mouse_wheel'] > 0 ? 1 : 0;
			$instance['slider_autoplay'] = isset( $new_instance['slider_autoplay'] ) && (int)$new_instance['slider_autoplay'] > 0 ? 1 : 0;
			$instance['slider_loop'] = isset( $new_instance['slider_loop'] ) && (int)$new_instance['slider_loop'] > 0 ? 1 : 0;
			$instance['slider_free_mode'] = isset( $new_instance['slider_free_mode'] ) && (int)$new_instance['slider_free_mode'] > 0 ? 1 : 0;
			return apply_filters('trx_addons_filter_widget_args_update', $instance, $new_instance, 'trx_addons_sow_widget_testimonials');
		}
	
		// Displays the widget settings controls on the widget panel
		function form($instance) {
			// Set up some default widget settings
			$instance = wp_parse_args( (array) $instance, apply_filters('trx_addons_filter_widget_args_default', array(
				'widget_title' => '',
				'rating' => 0,
				"use_initials" => 0,
				// Layout params
				"type" => "default",
				// Query params
				"cat" => "",
				"columns" => "",
				"count" => 3,
				"offset" => 0,
				"orderby" => '',
				"order" => '',
				"ids" => '',
				// Slider params
				"slider" => 0,
				"slider_effect" => "slide",
				"slider_pagination" => "none",
				"slider_pagination_thumbs" => 0,
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
				"link_text" => esc_html__('Learn more', 'trx_addons'),
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
				), 'trx_addons_sow_widget_testimonials')
			);
		
			do_action('trx_addons_action_before_widget_fields', $instance, 'trx_addons_sow_widget_testimonials', $this);
			
			$this->show_field(array('name' => 'widget_title',
									'title' => __('Widget title:', 'trx_addons'),
									'value' => $instance['widget_title'],
									'type' => 'text'));
		
			do_action('trx_addons_action_after_widget_title', $instance, 'trx_addons_sow_widget_testimonials', $this);
			
			$this->show_field(array('name' => 'type',
									'title' => __('Layout:', 'trx_addons'),
									'value' => $instance['type'],
									'options' => apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('cpt', 'testimonials', 'sc'), 'trx_widget_testimonials'),
									'type' => 'select'));

			$this->show_field(array('name' => 'rating',
									'title' => __('Show rating', 'trx_addons'),
									'value' => (int) $instance['rating'],
									'type' => 'checkbox'));

			$this->show_field(array('name' => 'use_initials',
									'title' => __('Use initials', 'trx_addons'),
									'label' => __('If no avatar is present, the initials derived from the available username will be used.', 'trx_addons'),
									'value' => (int) $instance['use_initials'],
									'dependency' => array(
										'type' => array( 'default' )
									),
									'type' => 'checkbox'));
			
			$this->show_field(array('title' => __('Query parameters', 'trx_addons'),
									'type' => 'info'));

			$this->show_field(array('name' => 'cat',
									'title' => __('Testimonials Group:', 'trx_addons'),
									'value' => $instance['cat'],
									'options' => trx_addons_array_merge(
															array( 0 => trx_addons_get_not_selected_text( esc_html__( 'Select group', 'trx_addons' ) ) ),
															trx_addons_get_list_terms(false, TRX_ADDONS_CPT_TESTIMONIALS_TAXONOMY)
															),
									'type' => 'select'));
			
			$this->show_fields_query_param($instance, '');
			$this->show_fields_slider_param($instance, false, array(
									'slider_pagination_thumbs' => array(
										'name' => 'slider_pagination_thumbs',
										'title' => '',
										'label' => __('Show thumbs as pagination bullets', 'trx_addons'),
										'value' => (int) $instance['slider_pagination_thumbs'],
										'dependency' => array(
											'slider' => array( 1 ),
											'slider_pagination' => array( 'left', 'right', 'bottom', 'bottom_outside' )
										),
										'type' => 'checkbox'
									)));
			$this->show_fields_title_param($instance);
			$this->show_fields_id_param($instance);
		
			do_action('trx_addons_action_after_widget_fields', $instance, 'trx_addons_sow_widget_testimonials', $this);
		}
	}

	// Load widget
	if (!function_exists('trx_addons_sow_widget_testimonials_load')) {
		add_action( 'widgets_init', 'trx_addons_sow_widget_testimonials_load' );
		function trx_addons_sow_widget_testimonials_load() {
			register_widget('TRX_Addons_SOW_Widget_Testimonials');
		}
	}
}
