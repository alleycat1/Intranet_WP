<?php
/**
 * Widget: Users list
 *
 * @package ThemeREX Addons
 * @since v1.84.0
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


// TRX_Addons Widget
//------------------------------------------------------
if ( ! class_exists('TRX_Addons_SOW_Widget_Users') ) {

	class TRX_Addons_SOW_Widget_Users extends TRX_Addons_Widget {

		function __construct() {
			$widget_ops = array('classname' => 'widget_users', 'description' => esc_html__('List of registered users', 'trx_addons'));
			parent::__construct( 'trx_addons_widget_users', esc_html__('ThemeREX Users', 'trx_addons'), $widget_ops );
		}

		// Show widget
		function widget($args, $instance) {

			extract($args);
	
			$widget_title = apply_filters('widget_title', isset($instance['widget_title']) ? $instance['widget_title'] : '', $instance, $this->id_base);
	
			$output = trx_addons_sc_users(apply_filters('trx_addons_filter_widget_args',
															$instance,
															$instance,
															'trx_addons_sow_widget_users')
															);
	
			if ( ! empty($output) ) {
		
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
			$instance['roles']  = ! empty( $new_instance['roles'] )
									? ( is_array( $new_instance['roles'] ) 
										? array_map( 'strip_tags', $new_instance['roles'] )
										: strip_tags( $new_instance['roles'] )
										)
									: '';
			$instance['number'] = (int)$new_instance['number'];
			$instance['columns'] = (int)$new_instance['columns'];
			$instance['slider'] = isset( $new_instance['slider'] ) && (int)$new_instance['slider'] > 0 ? 1 : 0;
			$instance['slides_centered'] = isset( $new_instance['slides_centered'] ) && (int)$new_instance['slides_centered'] > 0 ? 1 : 0;
			$instance['slides_overflow'] = isset( $new_instance['slides_overflow'] ) && (int)$new_instance['slides_overflow'] > 0 ? 1 : 0;
			$instance['slider_mouse_wheel'] = isset( $new_instance['slider_mouse_wheel'] ) && (int)$new_instance['slider_mouse_wheel'] > 0 ? 1 : 0;
			$instance['slider_autoplay'] = isset( $new_instance['slider_autoplay'] ) && (int)$new_instance['slider_autoplay'] > 0 ? 1 : 0;
			$instance['slider_loop'] = isset( $new_instance['slider_loop'] ) && (int)$new_instance['slider_loop'] > 0 ? 1 : 0;
			$instance['slider_free_mode'] = isset( $new_instance['slider_free_mode'] ) && (int)$new_instance['slider_free_mode'] > 0 ? 1 : 0;
			$instance['slides_space'] = (int) $new_instance['slides_space'];
			return apply_filters('trx_addons_filter_widget_args_update', $instance, $new_instance, 'trx_addons_widget_users');
		}

		// Displays the widget settings controls on the widget panel
		function form($instance) {
			// Set up some default widget settings
			$instance = wp_parse_args( (array) $instance, apply_filters('trx_addons_filter_widget_args_default', array(
				'widget_title' => '',
				// Layout params
				'type' => 'default',
				'roles' => array( 'author' ),
				'number' => 3,
				'columns' => 0,
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
				), 'trx_addons_sow_widget_users')
			);
			
			do_action('trx_addons_action_before_widget_fields', $instance, 'trx_addons_sow_widget_users', $this);
			
			$this->show_field(array('name' => 'widget_title',
									'title' => __('Widget title:', 'trx_addons'),
									'value' => $instance['widget_title'],
									'type' => 'text'));
			
			do_action('trx_addons_action_after_widget_title', $instance, 'trx_addons_sow_widget_users', $this);

			$this->show_field(array('title' => __('Layout parameters', 'trx_addons'),
									'type' => 'info'));

			$layouts = apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('sc', 'users'), 'trx_sc_users');
			$this->show_field(array('name' => 'type',
									'title' => __('Layout', 'trx_addons'),
									'value' => $instance['type'],
									'options' => $layouts,
									'type' => 'select'));

			$this->show_field(array('name' => 'roles',
									'title' => __("Roles to display", 'trx_addons'),
									'value' => $instance['roles'],
									'options' => trx_addons_get_list_users_roles(),
									'type' => 'checklist'));

			$this->show_field(array('name' => 'number',
									'title' => __('Number of users', 'trx_addons'),
									'value' => max(1, (int) $instance['number']),
									'type' => 'text'));

			$this->show_field(array('name' => 'columns',
									'title' => __('Columns', 'trx_addons'),
									'value' => max(1, (int) $instance['columns']),
									'type' => 'text'));

			$this->show_fields_slider_param($instance);

			$this->show_fields_title_param($instance);

			$this->show_fields_id_param($instance);

			do_action('trx_addons_action_after_widget_fields', $instance, 'trx_addons_sow_widget_users', $this);
		}
	}

	// Load widget
	if (!function_exists('trx_addons_sow_widget_users_load')) {
		add_action( 'widgets_init', 'trx_addons_sow_widget_users_load' );
		function trx_addons_sow_widget_users_load() {
			register_widget('TRX_Addons_SOW_Widget_Users');
		}
	}
}