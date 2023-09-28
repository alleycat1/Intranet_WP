<?php
/**
 * Widget: Flickr
 *
 * @package ThemeREX Addons
 * @since v1.1
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

// Load widget
if (!function_exists('trx_addons_widget_flickr_load')) {
	add_action( 'widgets_init', 'trx_addons_widget_flickr_load' );
	function trx_addons_widget_flickr_load() {
		register_widget('trx_addons_widget_flickr');
	}
}

// Widget Class
class trx_addons_widget_flickr extends TRX_Addons_Widget {

	function __construct() {
		$widget_ops = array( 'classname' => 'widget_flickr', 'description' => esc_html__('Last flickr photos.', 'trx_addons') );
		parent::__construct( 'trx_addons_widget_flickr', esc_html__('ThemeREX Flickr photos', 'trx_addons'), $widget_ops );
	}

	// Show widget
	function widget( $args, $instance ) {

		$title = apply_filters( 'widget_title', isset($instance['title']) ? $instance['title'] : '', $instance, $this->id_base );
		$flickr_api_key = isset($instance['flickr_api_key']) ? $instance['flickr_api_key'] : '';
		$flickr_username = isset($instance['flickr_username']) ? $instance['flickr_username'] : '';
		$flickr_count = isset($instance['flickr_count']) ? $instance['flickr_count'] : 1;
		$flickr_columns = isset($instance['flickr_columns']) ? min($flickr_count, (int) $instance['flickr_columns']) : 1;
		$flickr_columns_gap = isset($instance['flickr_columns_gap']) ? max(0, $instance['flickr_columns_gap']) : 0;

		// Load widget-specific scripts and styles
		trx_addons_widget_flickr_load_scripts_front( true );

		// Load template
		trx_addons_get_template_part(TRX_ADDONS_PLUGIN_WIDGETS . 'flickr/tpl.default.php',
									'trx_addons_args_widget_flickr', 
									apply_filters('trx_addons_filter_widget_args',
												array_merge($args, compact('title', 'flickr_api_key', 'flickr_username', 'flickr_count', 'flickr_columns', 'flickr_columns_gap')),
												$instance, 'trx_addons_widget_flickr')
									);
	}

	// Update the widget settings.
	function update( $new_instance, $instance ) {
		$instance = array_merge($instance, $new_instance);
		$instance['flickr_count'] = (int) $new_instance['flickr_count'];
		$instance['flickr_columns'] = min($instance['flickr_count'], (int) $new_instance['flickr_columns']);
		$instance['flickr_columns_gap'] = max(0, $new_instance['flickr_columns_gap']);
		return apply_filters('trx_addons_filter_widget_args_update', $instance, $new_instance, 'trx_addons_widget_flickr');
	}

	// Displays the widget settings controls on the widget panel.
	function form( $instance ) {
		
		// Set up some default widget settings
		$instance = wp_parse_args( (array) $instance, apply_filters('trx_addons_filter_widget_args_default', array(
			'title' => '', 
			'flickr_api_key' => '', 
			'flickr_username' => '', 
			'flickr_count' => 8,
			'flickr_columns' => 4,
			'flickr_columns_gap' => 0
			), 'trx_addons_widget_flickr')
		);
		
		do_action('trx_addons_action_before_widget_fields', $instance, 'trx_addons_widget_flickr', $this);
		
		$this->show_field(array('name' => 'title',
								'title' => __('Title:', 'trx_addons'),
								'value' => $instance['title'],
								'type' => 'text'));
		
		do_action('trx_addons_action_after_widget_title', $instance, 'trx_addons_widget_flickr', $this);
		
		$this->show_field(array('name' => 'flickr_api_key',
								'title' => __('Flickr API key:', 'trx_addons'),
								'value' => $instance['flickr_api_key'],
								'type' => 'text'));
		
		$this->show_field(array('name' => 'flickr_username',
								'title' => __('Flickr ID:', 'trx_addons'),
								'value' => $instance['flickr_username'],
								'type' => 'text'));
		
		$this->show_field(array('name' => 'flickr_count',
								'title' => __('Number of photos:', 'trx_addons'),
								'value' => max(1, min(30, (int) $instance['flickr_count'])),
								'type' => 'text'));
		
		$this->show_field(array('name' => 'flickr_columns',
								'title' => __('Columns:', 'trx_addons'),
								'value' => max(1, min(12, (int) $instance['flickr_columns'])),
								'type' => 'text'));
		
		$this->show_field(array('name' => 'flickr_columns_gap',
								'title' => __('Columns gap:', 'trx_addons'),
								'value' => max(0, (int) $instance['flickr_columns_gap']),
								'type' => 'text'));
		
		do_action('trx_addons_action_after_widget_fields', $instance, 'trx_addons_widget_flickr', $this);
	}
}


// Load required styles and scripts for the frontend
if ( !function_exists( 'trx_addons_widget_flickr_load_scripts_front' ) ) {
	add_action( "wp_enqueue_scripts", 'trx_addons_widget_flickr_load_scripts_front', TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY );
	add_action( 'trx_addons_action_pagebuilder_preview_scripts', 'trx_addons_widget_flickr_load_scripts_front', 10, 1 );
	function trx_addons_widget_flickr_load_scripts_front( $force = false ) {
		trx_addons_enqueue_optimized( 'widget_flickr', $force, array(
			'css'  => array(
				'trx_addons-widget_flickr' => array( 'src' => TRX_ADDONS_PLUGIN_WIDGETS . 'flickr/flickr.css' ),
			),
			'check' => array(
				array( 'type' => 'sc',  'sc' => 'trx_widget_flickr' ),
				array( 'type' => 'gb',  'sc' => 'wp:trx-addons/flickr' ),
				array( 'type' => 'elm', 'sc' => '"widgetType":"trx_widget_flickr"' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[trx_widget_flickr' ),
			)
		) );
	}
}
	
// Merge widget specific styles into single stylesheet
if ( !function_exists( 'trx_addons_widget_flickr_merge_styles' ) ) {
	add_filter("trx_addons_filter_merge_styles", 'trx_addons_widget_flickr_merge_styles');
	function trx_addons_widget_flickr_merge_styles($list) {
		$list[ TRX_ADDONS_PLUGIN_WIDGETS . 'flickr/flickr.css' ] = false;
		return $list;
	}
}

// Load styles and scripts if present in the cache of the menu
if ( !function_exists( 'trx_addons_widget_flickr_check_in_html_output' ) ) {
	add_filter( 'trx_addons_filter_get_menu_cache_html', 'trx_addons_widget_flickr_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_show_layout_from_cache', 'trx_addons_widget_flickr_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_check_page_content', 'trx_addons_widget_flickr_check_in_html_output', 10, 1 );
	function trx_addons_widget_flickr_check_in_html_output( $content = '' ) {
		$args = array(
			'check' => array(
				'class=[\'"][^\'"]*widget_flickr'
			)
		);
		if ( trx_addons_check_in_html_output( 'widget_flickr', $content, $args ) ) {
			trx_addons_widget_flickr_load_scripts_front( true );
		}
		return $content;
	}
}


// Add shortcodes
//----------------------------------------------------------------------------
require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_WIDGETS . 'flickr/flickr-sc.php';

// Add shortcodes to Elementor
if ( trx_addons_exists_elementor() && function_exists('trx_addons_elm_init') ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_WIDGETS . 'flickr/flickr-sc-elementor.php';
}

// Add shortcodes to Gutenberg
if ( trx_addons_exists_gutenberg() && function_exists( 'trx_addons_gutenberg_get_param_id' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_WIDGETS . 'flickr/flickr-sc-gutenberg.php';
}

// Add shortcodes to VC
if ( trx_addons_exists_vc() && function_exists( 'trx_addons_vc_add_id_param' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_WIDGETS . 'flickr/flickr-sc-vc.php';
}
