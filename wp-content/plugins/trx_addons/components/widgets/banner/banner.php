<?php
/**
 * Widget: Banner
 *
 * @package ThemeREX Addons
 * @since v1.0
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

// Load widget
if (!function_exists('trx_addons_widget_banner_load')) {
	add_action( 'widgets_init', 'trx_addons_widget_banner_load' );
	function trx_addons_widget_banner_load() {
		register_widget( 'trx_addons_widget_banner' );
	}
}

// Widget Class
class trx_addons_widget_banner extends TRX_Addons_Widget {

	function __construct() {
		$widget_ops = array( 'classname' => 'widget_banner', 'description' => esc_html__('Banner with image and/or any html and js code', 'trx_addons') );
		parent::__construct( 'trx_addons_widget_banner', esc_html__('ThemeREX Banner', 'trx_addons'), $widget_ops );
	}

	// Show widget
	function widget( $args, $instance ) {

		$instance['title'] = apply_filters( 'widget_title', isset($instance['title']) ? $instance['title'] : '', $instance, $this->id_base );
		if ( empty( $instance['banner_show'] ) ) {
			$instance['banner_show'] = 'permanent';
		}
		if ( empty( $instance['banner_image'] ) ) {
			if ( empty($instance['banner_link'] ) && empty( $instance['banner_code'] ) && trx_addons_is_singular() && ! trx_addons_sc_layouts_showed( 'featured' ) ) {
				$featured_image = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), !empty( $instance['from_shortcode'] ) ? 'full' : trx_addons_get_thumb_size('masonry') );
				$instance['banner_image'] = empty( $featured_image[0] ) ? '' : $featured_image[0];
			}
		} else {
			$instance['banner_image'] = trx_addons_get_attachment_url( $instance['banner_image'], ! empty( $instance['from_shortcode'] ) ? 'full' : trx_addons_get_thumb_size('masonry') );
		}

		// Load widget-specific scripts and styles
		trx_addons_widget_banner_load_scripts_front( true );

		// Load template
		trx_addons_get_template_part(TRX_ADDONS_PLUGIN_WIDGETS . 'banner/tpl.default.php',
									'trx_addons_args_widget_banner',
									apply_filters('trx_addons_filter_widget_args',
												array_merge( $args, $instance ),
												$instance, 'trx_addons_widget_banner')
									);
	}

	// Update the widget settings.
	function update( $new_instance, $instance ) {
		$instance = array_merge($instance, $new_instance);
		return apply_filters('trx_addons_filter_widget_args_update', $instance, $new_instance, 'trx_addons_widget_banner');
	}

	// Displays the widget settings controls on the widget panel.
	function form( $instance ) {

		// Set up some default widget settings
		$instance = wp_parse_args( (array) $instance, apply_filters('trx_addons_filter_widget_args_default', array(
			'title'        => '',
			'fullwidth'    => '1',
			'banner_show'  => 'permanent',
			'banner_image' => '',
			'banner_link'  => '',
			'banner_code'  => ''
			), 'trx_addons_widget_banner')
		);
		
		do_action('trx_addons_action_before_widget_fields', $instance, 'trx_addons_widget_banner', $this);
		
		$this->show_field(array('name' => 'title',
								'title' => __('Title:', 'trx_addons'),
								'value' => $instance['title'],
								'type' => 'text'));
		
		do_action('trx_addons_action_after_widget_title', $instance, 'trx_addons_widget_banner', $this);
		
		$this->show_field(array('name' => 'fullwidth',
								'title' => __('Widget size:', 'trx_addons'),
								'value' => $instance['fullwidth'],
								'options' => array(
													'1' => __('Fullwidth', 'trx_addons'),
													'0' => __('Boxed', 'trx_addons')
													),
								'type' => 'radio'));
		
		$this->show_field(array('name' => 'banner_show',
								'title' => __('Show on:', 'trx_addons'),
								'value' => $instance['banner_show'],
								'options' => trx_addons_get_list_sc_show_on(),
								'type' => 'radio'));
		
		$this->show_field(array('name' => 'banner_image',
								'title' => __('Image source URL:', 'trx_addons'),
								'value' => $instance['banner_image'],
								'type' => 'image'));
		
		$this->show_field(array('name' => 'banner_link',
								'title' => __('Image link URL:', 'trx_addons'),
								'value' => $instance['banner_link'],
								'type' => 'text'));

		$this->show_field(array('name' => 'banner_code',
								'title' => __('Paste HTML Code:', 'trx_addons'),
								'value' => $instance['banner_code'],
								'type' => 'textarea'));
		
		do_action('trx_addons_action_after_widget_fields', $instance, 'trx_addons_widget_banner', $this);
	}
}


// Load required styles and scripts for the frontend
if ( !function_exists( 'trx_addons_widget_banner_load_scripts_front' ) ) {
	add_action( "wp_enqueue_scripts", 'trx_addons_widget_banner_load_scripts_front', TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY );
	add_action( 'trx_addons_action_pagebuilder_preview_scripts', 'trx_addons_widget_banner_load_scripts_front', 10, 1 );
	function trx_addons_widget_banner_load_scripts_front( $force = false ) {
		trx_addons_enqueue_optimized( 'widget_banner', $force, array(
			'css'  => array(
				'trx_addons-widget_banner' => array( 'src' => TRX_ADDONS_PLUGIN_WIDGETS . 'banner/banner.css' ),
			),
			'check' => array(
				array( 'type' => 'sc',  'sc' => 'trx_widget_banner' ),
				array( 'type' => 'gb',  'sc' => 'wp:trx-addons/banner' ),
				array( 'type' => 'elm', 'sc' => '"widgetType":"trx_widget_banner"' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[trx_widget_banner' ),
			)
		) );
	}
}
	
// Merge widget specific styles into single stylesheet
if ( !function_exists( 'trx_addons_widget_banner_merge_styles' ) ) {
	add_filter("trx_addons_filter_merge_styles", 'trx_addons_widget_banner_merge_styles');
	function trx_addons_widget_banner_merge_styles($list) {
		$list[ TRX_ADDONS_PLUGIN_WIDGETS . 'banner/banner.css' ] = false;
		return $list;
	}
}

// Load styles and scripts if present in the cache of the menu
if ( !function_exists( 'trx_addons_widget_banner_check_in_html_output' ) ) {
	add_filter( 'trx_addons_filter_get_menu_cache_html', 'trx_addons_widget_banner_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_show_layout_from_cache', 'trx_addons_widget_banner_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_check_page_content', 'trx_addons_widget_banner_check_in_html_output', 10, 1 );
	function trx_addons_widget_banner_check_in_html_output( $content = '' ) {
		$args = array(
			'check' => array(
				'class=[\'"][^\'"]*widget_banner'
			)
		);
		if ( trx_addons_check_in_html_output( 'widget_banner', $content, $args ) ) {
			trx_addons_widget_banner_load_scripts_front( true );
		}
		return $content;
	}
}



// Add shortcodes
//----------------------------------------------------------------------------
require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_WIDGETS . 'banner/banner-sc.php';

// Add shortcodes to Elementor
if ( trx_addons_exists_elementor() && function_exists('trx_addons_elm_init') ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_WIDGETS . 'banner/banner-sc-elementor.php';
}

// Add shortcodes to Gutenberg
if ( trx_addons_exists_gutenberg() && function_exists( 'trx_addons_gutenberg_get_param_id' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_WIDGETS . 'banner/banner-sc-gutenberg.php';
}

// Add shortcodes to VC
if ( trx_addons_exists_vc() && function_exists( 'trx_addons_vc_add_id_param' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_WIDGETS . 'banner/banner-sc-vc.php';
}
