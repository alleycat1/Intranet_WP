<?php
/**
 * Widget: Recent News
 *
 * @package ThemeREX Addons
 * @since v1.0
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


// Load widget
if (!function_exists('trx_addons_widget_recent_news_load')) {
	add_action( 'widgets_init', 'trx_addons_widget_recent_news_load' );
	function trx_addons_widget_recent_news_load() {
		register_widget('trx_addons_widget_recent_news');
	}
}


// Widget Class
//------------------------------------------------------
class trx_addons_widget_recent_news extends TRX_Addons_Widget {

	function __construct() {
		$widget_ops = array('classname' => 'widget_recent_news', 'description' => esc_html__('Show recent news in many styles', 'trx_addons'));
		parent::__construct( 'trx_addons_widget_recent_news', esc_html__('ThemeREX Recent News', 'trx_addons'), $widget_ops );
	}

	// Show widget
	function widget($args, $instance) {
		extract($args);

		$widget_title = apply_filters( 'widget_title', isset($instance['widget_title']) ? $instance['widget_title'] : '', $instance, $this->id_base );

		$output = trx_addons_sc_recent_news( apply_filters('trx_addons_filter_widget_args',
							array(
								'title' 			=> isset($instance['title']) ? $instance['title'] : '',
								'subtitle'			=> isset($instance['subtitle']) ? $instance['subtitle'] : '',
								'style'				=> isset($instance['style']) ? $instance['style'] : 'news-magazine',
								'count'				=> isset($instance['count']) ? (int) $instance['count'] : 3,
								'featured'			=> isset($instance['featured']) ? (int) $instance['featured'] : 0,
								'columns'			=> isset($instance['columns']) ? (int) $instance['columns'] : 1,
								'category'			=> isset($instance['category']) ? (int) $instance['category'] : 0,
								'show_categories'	=> isset($instance['show_categories']) ? (int) $instance['show_categories'] : 0
								),
							$instance, 'trx_addons_widget_recent_news')
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
		$instance['count'] = max(1, (int) $new_instance['count']);
		$instance['featured'] = max(0, min($instance['count'], (int) $new_instance['featured']));
		$instance['columns'] = max(1, min($instance['featured']+1, (int) $new_instance['columns']));		//	Columns <= Featured+1
		$instance['category'] = max(0, (int) $new_instance['category']);
		$instance['show_categories'] = (int) $new_instance['show_categories'] > 0 ? 1 : 0;
		return apply_filters('trx_addons_filter_widget_args_update', $instance, $new_instance, 'trx_addons_widget_recent_news');
	}

	// Displays the widget settings controls on the widget panel
	function form($instance) {
		// Set up some default widget settings
		$instance = wp_parse_args( (array) $instance, apply_filters('trx_addons_filter_widget_args_default', array(
			'widget_title' => '',
			'title' => '',
			'subtitle' => '',
			'style' => 'news-magazine',
			'count' => 3,
			'featured' => 3,
			'columns' => 1,
			'category' => 0,
			'show_categories' => 1
			), 'trx_addons_widget_recent_news')
		);
		
		do_action('trx_addons_action_before_widget_fields', $instance, 'trx_addons_widget_recent_news', $this);
		
		$this->show_field(array('name' => 'widget_title',
								'title' => __('Widget title:', 'trx_addons'),
								'value' => $instance['widget_title'],
								'type' => 'text'));
		
		do_action('trx_addons_action_after_widget_title', $instance, 'trx_addons_widget_recent_news', $this);
		
		$this->show_field(array('name' => 'title',
								'title' => __('Block title:', 'trx_addons'),
								'value' => $instance['title'],
								'type' => 'text'));
		
		$this->show_field(array('name' => 'subtitle',
								'title' => __('Block subtitle:', 'trx_addons'),
								'value' => $instance['subtitle'],
								'type' => 'text'));
		
		$this->show_field(array('name' => 'style',
								'title' => __('Style:', 'trx_addons'),
								'value' => $instance['style'],
								'options' => trx_addons_components_get_allowed_layouts('widgets', 'recent_news'),
								'type' => 'select'));
		
		$this->show_field(array('name' => 'count',
								'title' => __('Number of displayed posts:', 'trx_addons'),
								'value' => (int) $instance['count'],
								'type' => 'text'));
		
		$this->show_field(array('name' => 'featured',
								'title' => __('Number of featured posts:', 'trx_addons'),
								'value' => (int) $instance['featured'],
								'type' => 'text'));
		
		$this->show_field(array('name' => 'columns',
								'title' => __('Number of columns:', 'trx_addons'),
								'value' => (int) $instance['columns'],
								'type' => 'text'));
		
		$this->show_field(array('name' => 'category',
								'title' => __('Parent category:', 'trx_addons'),
								'value' => (int) $instance['category'],
								'options' => trx_addons_array_merge( array( trx_addons_get_not_selected_text( __( 'All categories', 'trx_addons' ) ) ), trx_addons_get_list_categories(false)),
								'type' => 'select'));

		$this->show_field(array('name' => 'show_categories',
								'title' => __("Show categories dropdown:", 'trx_addons'),
								'value' => (int) $instance['show_categories'],
								'options' => trx_addons_get_list_show_hide(false, true),
								'type' => 'radio'));
		
		do_action('trx_addons_action_after_widget_fields', $instance, 'trx_addons_widget_recent_news', $this);
	}
}

	
// Load required styles and scripts for the frontend
if ( !function_exists( 'trx_addons_widget_recent_news_load_scripts_front' ) ) {
	add_action( "wp_enqueue_scripts", 'trx_addons_widget_recent_news_load_scripts_front', TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY );
	add_action( 'trx_addons_action_pagebuilder_preview_scripts', 'trx_addons_widget_recent_news_load_scripts_front', 10, 1 );
	function trx_addons_widget_recent_news_load_scripts_front( $force = false ) {
		trx_addons_enqueue_optimized( 'widget_recent_news', $force, array(
			'css'  => array(
				'trx_addons-widget_recent_news' => array( 'src' => TRX_ADDONS_PLUGIN_WIDGETS . 'recent_news/recent_news.css' ),
			),
			'js'  => array(
				'trx_addons-widget_recent_news' => array( 'src' => TRX_ADDONS_PLUGIN_WIDGETS . 'recent_news/recent_news.js', 'deps' => 'jquery' ),
			),
			'check' => array(
				array( 'type' => 'sc',  'sc' => 'trx_sc_recent_news' ),
				array( 'type' => 'sc',  'sc' => 'trx_widget_recent_news' ),
				array( 'type' => 'gb',  'sc' => 'wp:trx-addons/recent-news' ),
				array( 'type' => 'elm', 'sc' => '"widgetType":"trx_sc_recent_news"' ),
				array( 'type' => 'elm', 'sc' => '"widgetType":"trx_widget_recent_news"' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[trx_sc_recent_news' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[trx_widget_recent_news' ),
			)
		) );
	}
}

// Enqueue responsive styles for frontend
if ( ! function_exists( 'trx_addons_widget_recent_news_load_scripts_front_responsive' ) ) {
	add_action( 'wp_enqueue_scripts', 'trx_addons_widget_recent_news_load_scripts_front_responsive', TRX_ADDONS_ENQUEUE_RESPONSIVE_PRIORITY );
	add_action( 'trx_addons_action_load_scripts_front_widget_recent_news', 'trx_addons_widget_recent_news_load_scripts_front_responsive', 10, 1 );
	function trx_addons_widget_recent_news_load_scripts_front_responsive( $force = false ) {
		trx_addons_enqueue_optimized_responsive( 'widget_recent_news', $force, array(
			'css'  => array(
				'trx_addons-widget_recent_news-responsive' => array(
					'src' => TRX_ADDONS_PLUGIN_WIDGETS . 'recent_news/recent_news.responsive.css',
					'media' => 'xl'
				),
			),
		) );
	}
}
	
// Merge widget specific styles into single stylesheet
if ( !function_exists( 'trx_addons_widget_recent_news_merge_styles' ) ) {
	add_filter("trx_addons_filter_merge_styles", 'trx_addons_widget_recent_news_merge_styles');
	function trx_addons_widget_recent_news_merge_styles($list) {
		$list[ TRX_ADDONS_PLUGIN_WIDGETS . 'recent_news/recent_news.css' ] = false;
		return $list;
	}
}

// Merge widget's specific styles to the single stylesheet (responsive)
if ( !function_exists( 'trx_addons_widget_recent_news_merge_styles_responsive' ) ) {
	add_filter("trx_addons_filter_merge_styles_responsive", 'trx_addons_widget_recent_news_merge_styles_responsive');
	function trx_addons_widget_recent_news_merge_styles_responsive($list) {
		$list[ TRX_ADDONS_PLUGIN_WIDGETS . 'recent_news/recent_news.responsive.css' ] = false;
		return $list;
	}
}

// Merge widget specific scripts into single file
if ( !function_exists( 'trx_addons_widget_recent_news_merge_scripts' ) ) {
	add_action("trx_addons_filter_merge_scripts", 'trx_addons_widget_recent_news_merge_scripts');
	function trx_addons_widget_recent_news_merge_scripts($list) {
		$list[ TRX_ADDONS_PLUGIN_WIDGETS . 'recent_news/recent_news.js' ] = false;
		return $list;
	}
}

// Load styles and scripts if present in the cache of the menu
if ( !function_exists( 'trx_addons_widget_recent_news_check_in_html_output' ) ) {
	add_filter( 'trx_addons_filter_get_menu_cache_html', 'trx_addons_widget_recent_news_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_show_layout_from_cache', 'trx_addons_widget_recent_news_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_check_page_content', 'trx_addons_widget_recent_news_check_in_html_output', 10, 1 );
	function trx_addons_widget_recent_news_check_in_html_output( $content = '' ) {
		$args = array(
			'check' => array(
				'class=[\'"][^\'"]*widget_recent_news',
				'class=[\'"][^\'"]*sc_recent_news'
			)
		);
		if ( trx_addons_check_in_html_output( 'widget_recent_news', $content, $args ) ) {
			trx_addons_widget_recent_news_load_scripts_front( true );
		}
		return $content;
	}
}


// Add shortcodes
//----------------------------------------------------------------------------
require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_WIDGETS . 'recent_news/recent_news-sc.php';

// Add shortcodes to Elementor
if ( trx_addons_exists_elementor() && function_exists('trx_addons_elm_init') ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_WIDGETS . 'recent_news/recent_news-sc-elementor.php';
}

// Add shortcodes to Gutenberg
if ( trx_addons_exists_gutenberg() && function_exists( 'trx_addons_gutenberg_get_param_id' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_WIDGETS . 'recent_news/recent_news-sc-gutenberg.php';
}

// Add shortcodes to VC
if ( trx_addons_exists_vc() && function_exists( 'trx_addons_vc_add_id_param' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_WIDGETS . 'recent_news/recent_news-sc-vc.php';
}
