<?php
/**
 * Shortcode: Layouts (Widget)
 *
 * @package ThemeREX Addons
 * @since v1.6.52
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


// TRX_Addons Widget
//------------------------------------------------------
if ( ! class_exists('TRX_Addons_SOW_Widget_Layouts') ) {

	class TRX_Addons_SOW_Widget_Layouts extends TRX_Addons_Widget {
	
		function __construct() {
			$widget_ops = array('classname' => 'widget_layouts', 'description' => esc_html__('Display any previously created layout', 'trx_addons'));
			parent::__construct( 'trx_addons_sow_widget_layouts', esc_html__('ThemeREX Layouts', 'trx_addons'), $widget_ops );
		}
	
		// Show widget
		function widget($args, $instance) {
			extract($args);
	
			$widget_title = apply_filters('widget_title', isset($instance['widget_title']) ? $instance['widget_title'] : '', $instance, $this->id_base);
	
			$output = trx_addons_sc_layouts(apply_filters('trx_addons_filter_widget_args',
															$instance,
															$instance, 'trx_addons_sow_widget_layouts')
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
			return apply_filters('trx_addons_filter_widget_args_update', $instance, $new_instance, 'trx_addons_sow_widget_layouts');
		}
	
		// Displays the widget settings controls on the widget panel
		function form($instance) {
			// Set up some default widget settings
			$instance = wp_parse_args( (array) $instance, apply_filters('trx_addons_filter_widget_args_default', array(
				'widget_title' => '',
				// Layout params
				"layout" => 0,
				// Common params
				"id" => "",
				"class" => "",
				"css" => ""
				), 'trx_addons_sow_widget_layouts')
			);

			$layouts = trx_addons_array_merge(	array(
												0 => trx_addons_get_not_selected_text( __( 'Use content', 'trx_addons' ) )
											),
											trx_addons_get_list_layouts()
										);
			$layout = !empty($instance['layout']) ? $instance['layout'] : 0;
		
			do_action('trx_addons_action_before_widget_fields', $instance, 'trx_addons_sow_widget_layouts', $this);
			
			$this->show_field(array('name' => 'widget_title',
									'title' => __('Widget title:', 'trx_addons'),
									'value' => $instance['widget_title'],
									'type' => 'text'));
		
			do_action('trx_addons_action_after_widget_title', $instance, 'trx_addons_sow_widget_layouts', $this);
			

			$this->show_field(array('name' => 'layout',
									'title' => __('Layout:', 'trx_addons'),
									'description' => wp_kses( __("Select any previously created layout to insert to this sidebar", 'trx_addons')
															. '<br>'
															. sprintf('<a href="%1$s" class="trx_addons_post_editor'.(intval($layout)==0 ? ' trx_addons_hidden' : '').'" target="_blank">%2$s</a>',
																		admin_url( sprintf( "post.php?post=%d&amp;action=edit", $layout ) ),
																		__("Open selected layout in a new tab to edit", 'trx_addons')
																	),
														'trx_addons_kses_content'
														),
									'value' => !empty($instance['layout']) ? $instance['layout'] : trx_addons_array_get_first($layouts),
									'options' => $layouts,
									'type' => 'select'));
		
			do_action('trx_addons_action_after_widget_fields', $instance, 'trx_addons_sow_widget_layouts', $this);
		}
	}

	// Load widget
	if (!function_exists('trx_addons_sow_widget_layouts_load')) {
		add_action( 'widgets_init', 'trx_addons_sow_widget_layouts_load' );
		function trx_addons_sow_widget_layouts_load() {
			register_widget('TRX_Addons_SOW_Widget_Layouts');
		}
	}
}
