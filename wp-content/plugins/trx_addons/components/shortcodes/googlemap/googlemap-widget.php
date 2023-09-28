<?php
/**
 * Shortcode: Google Map (Widget)
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

if ( ! class_exists('TRX_Addons_SOW_Widget_Googlemap') ) {

	class TRX_Addons_SOW_Widget_Googlemap extends TRX_Addons_Widget {
	
		function __construct() {
			$widget_ops = array('classname' => 'widget_googlemap', 'description' => esc_html__('Show Google map with specified address', 'trx_addons'));
			parent::__construct( 'trx_addons_sow_widget_googlemap', esc_html__('ThemeREX Google map', 'trx_addons'), $widget_ops );
		}
	
		// Show widget
		function widget($args, $instance) {
			extract($args);
	
			$widget_title = apply_filters('widget_title', isset($instance['widget_title']) ? $instance['widget_title'] : '', $instance, $this->id_base);
	
			$output = '';
			if (!empty($instance['marker_address']) || !empty($instance['marker_latlng']))
				$output = trx_addons_sc_googlemap(apply_filters('trx_addons_filter_widget_args',
						array_merge($instance, array(
							'markers' => array(
											array(
												'address' => !empty($instance['marker_address']) ? $instance['marker_address'] : '',
												'icon' => !empty($instance['marker_icon']) ? $instance['marker_icon'] : '',
												'title' => !empty($instance['marker_title']) ? $instance['marker_title'] : '',
												'description' => !empty($instance['marker_description']) ? $instance['marker_description'] : '',
												)
											)
							)),
							$instance, 'trx_addons_sow_widget_googlemap')
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
			return apply_filters('trx_addons_filter_widget_args_update', $instance, $new_instance, 'trx_addons_sow_widget_googlemap');
		}
	
		// Displays the widget settings controls on the widget panel
		function form($instance) {
			// Set up some default widget settings
			$instance = wp_parse_args( (array) $instance, apply_filters('trx_addons_filter_widget_args_default', array(
				'widget_title' => '',
				// Layout params
				"type" => "default",
				"zoom" => 16,
				"style" => 'default',
				"marker_address" => '',
				"marker_icon" => '',
				"marker_title" => '',
				"marker_description" => '',
				"width" => "100%",
				"height" => "400",
				// Title params
				"title" => '',
				"subtitle" => '',
				"subtitle_align" => "none",
				"subtitle_position" => trx_addons_get_setting('subtitle_above_title') ? 'above' : 'below',
				"description" => '',
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
				), 'trx_addons_sow_widget_googlemap')
			);
		
			do_action('trx_addons_action_before_widget_fields', $instance, 'trx_addons_sow_widget_googlemap', $this);
			
			$this->show_field(array('name' => 'widget_title',
									'title' => __('Widget title:', 'trx_addons'),
									'value' => $instance['widget_title'],
									'type' => 'text'));
		
			do_action('trx_addons_action_after_widget_title', $instance, 'trx_addons_sow_widget_googlemap', $this);
			
			$this->show_field(array('title' => __('Layout parameters', 'trx_addons'),
									'type' => 'info'));
			
			$this->show_field(array('name' => 'type',
									'title' => __('Layout:', 'trx_addons'),
									'value' => $instance['type'],
									'options' => apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('sc', 'googlemap'), 'trx_sc_googlemap'),
									'type' => 'select'));
			
			$this->show_field(array('name' => 'style',
									'title' => __('Style:', 'trx_addons'),
									'value' => $instance['style'],
									'options' => trx_addons_get_list_sc_googlemap_styles(),
									'type' => 'select'));
			
			$this->show_field(array('name' => 'zoom',
									'title' => __('Zoom:', 'trx_addons'),
									"description" => wp_kses_data( __("Map zoom factor on a scale from 1 to 20. If assigned the value '0' or left empty, fit the bounds to markers.", 'trx_addons') ),
									'value' => (int) $instance['zoom'],
									"std" => 16,
									"options" => trx_addons_get_list_range(0, 21),
									"type" => 'select'));

			$this->show_field(array('name' => 'width',
									'title' => __('Width:', 'trx_addons'),
									"description" => wp_kses_data( __("Width of the map. Any CSS measurement units are allowed. If unit is not specified - use 'px'", 'trx_addons') ),
									'value' => $instance['width'],
									"std" => "100%",
									"type" => 'text'));

			$this->show_field(array('name' => 'height',
									'title' => __('Height:', 'trx_addons'),
									"description" => wp_kses_data( __("Height of the map. Any CSS measurement units are allowed. If unit is not specified - use 'px'", 'trx_addons') ),
									'value' => $instance['height'],
									"std" => "350",
									"type" => 'text'));
			
			$this->show_field(array('title' => __('Marker', 'trx_addons'),
									'type' => 'info'));

			$this->show_field(array('name' => 'marker_address',
									'title' => __('Address or Lat,Lng:', 'trx_addons'),
									"description" => wp_kses_data( __("Specify the address or comma separated coordinates to place marker on the map.", 'trx_addons') ),
									'value' => $instance['marker_address'],
									"std" => "",
									"type" => 'text'));

			$this->show_field(array('name' => 'marker_icon',
									'title' => __('Marker image:', 'trx_addons'),
									"description" => wp_kses_data( __("Select or upload image for this marker", 'trx_addons') ),
									'value' => $instance['marker_icon'],
									"std" => "",
									"type" => 'image'));

			$this->show_field(array('name' => 'marker_title',
									'title' => __('Title:', 'trx_addons'),
									"description" => wp_kses_data( __("Title of the marker", 'trx_addons') ),
									'value' => $instance['marker_title'],
									"std" => "",
									"type" => 'text'));

			$this->show_field(array('name' => 'marker_description',
									'title' => __('Description:', 'trx_addons'),
									"description" => wp_kses_data( __("Description of the marker", 'trx_addons') ),
									'value' => $instance['marker_description'],
									"std" => "",
									"type" => 'textarea'));

			$this->show_fields_title_param($instance);
			$this->show_fields_id_param($instance);
		
			do_action('trx_addons_action_after_widget_fields', $instance, 'trx_addons_sow_widget_googlemap', $this);
		}
	}

	// Load widget
	if (!function_exists('trx_addons_sow_widget_googlemap_load')) {
		add_action( 'widgets_init', 'trx_addons_sow_widget_googlemap_load' );
		function trx_addons_sow_widget_googlemap_load() {
			register_widget('TRX_Addons_SOW_Widget_Googlemap');
		}
	}
}
