<?php
/**
 * Widget: Video player for Youtube, Vimeo, etc. embeded video
 *
 * @package ThemeREX Addons
 * @since v1.1
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

// Load widget
if ( ! function_exists( 'trx_addons_widget_video_load' ) ) {
	add_action( 'widgets_init', 'trx_addons_widget_video_load' );
	function trx_addons_widget_video_load() {
		register_widget( 'trx_addons_widget_video' );
	}
}


// Widget 'Video' Class
//-------------------------------------------------------
class trx_addons_widget_video extends TRX_Addons_Widget {

	function __construct() {
		$widget_ops = array( 'classname' => 'widget_video', 'description' => esc_html__('Show video from Youtube, Vimeo, etc.', 'trx_addons') );
		parent::__construct( 'trx_addons_widget_video', esc_html__('ThemeREX Video player', 'trx_addons'), $widget_ops );
	}

	// Show widget
	function widget( $args, $instance ) {

		extract( $args );

		$title = apply_filters( 'widget_title', isset($instance['title']) ? $instance['title'] : '', $instance, $this->id_base );
		$subtitle  = isset($instance['subtitle']) ? $instance['subtitle'] : '';
		$type  = isset($instance['type']) ? $instance['type'] : 'default';
		$ratio  = isset($instance['ratio']) ? $instance['ratio'] : '16:9';
		$embed = isset($instance['embed']) ? $instance['embed'] : '';
		$link  = isset($instance['link']) ? $instance['link'] : '';
		$cover = isset($instance['cover']) ? $instance['cover'] : '';
//		$autoplay = isset($instance['autoplay']) && ( ! isset( $instance['type'] ) || $instance['type'] != 'hover' ) ? $instance['autoplay'] : 0;
		$autoplay = isset($instance['autoplay']) ? $instance['autoplay'] : 0;
		if ( $type == 'hover' && ! empty( $cover ) ) {
			$autoplay = 0;
		}
		$mute = isset($instance['mute']) ? $instance['mute'] : 0;
		if ( $type != 'hover' && ! empty( $autoplay ) ) {
			$mute = 1;
		}
		$popup = isset($instance['popup']) && ! $autoplay && $type != 'hover' ? $instance['popup'] : 0;
		$media_from_post = isset( $instance['media_from_post'] ) ? $instance['media_from_post'] : 0;

		if ( empty( $embed ) && empty( $link ) && (int)$media_from_post == 0 ) {
			return;
		}

		// Get video from post if parameter is empty
		if ( empty( $link ) && empty( $embed ) && (int) $media_from_post > 0 ) {
			$video_from_post = trx_addons_get_post_video_list_first();
			if ( ! empty( $video_from_post ) ) {
				if ( ! empty( $video_from_post['video_url'] ) ) {
					$link = $video_from_post['video_url'];
				} else if ( ! empty( $video_from_post['video_embed'] ) ) {
					$embed = $video_from_post['video_embed'];
				}
				if ( empty( $cover ) && ! empty( $video_from_post['image'] ) ) {
					$cover = $video_from_post['image'];
				}
			} else {
				$video_from_post = trx_addons_get_post_video();
				if ( ! empty( $video_from_post ) ) {
					$link = $video_from_post;
				}
			}
		}

		// Load widget-specific scripts and styles
		trx_addons_widget_video_load_scripts_front( true );

		// Load template
		trx_addons_get_template_part( array(
											TRX_ADDONS_PLUGIN_WIDGETS . 'video/tpl.' . trx_addons_esc( $type ) . '.php',
											TRX_ADDONS_PLUGIN_WIDGETS . 'video/tpl.default.php'
										),
										'trx_addons_args_widget_video',
										apply_filters('trx_addons_filter_widget_args',
											array_merge( $args, compact( 'type', 'title', 'subtitle', 'ratio', 'embed', 'link', 'cover', 'popup', 'autoplay', 'mute' ) ),
											$instance,
											'trx_addons_widget_video'
										)
									);
	}

	// Update the widget settings.
	function update( $new_instance, $instance ) {
		$instance = array_merge( $instance, $new_instance );
		$instance['popup'] = isset( $new_instance['popup'] ) && (int) $new_instance['popup'] > 0 ? 1 : 0;
		$instance['autoplay'] = isset( $new_instance['autoplay'] ) && (int) $new_instance['autoplay'] > 0 ? 1 : 0;
		$instance['mute'] = isset( $new_instance['mute'] ) && (int) $new_instance['mute'] > 0 ? 1 : 0;
		return apply_filters( 'trx_addons_filter_widget_args_update', $instance, $new_instance, 'trx_addons_widget_video' );
	}

	// Displays the widget settings controls on the widget panel.
	function form( $instance ) {
		// Set up some default widget settings
		$instance = wp_parse_args( (array)$instance, apply_filters( 'trx_addons_filter_widget_args_default', array(
				'title' => '',
				'subtitle' => '',
				'type' => 'default',
				'ratio' => '16:9',
				'cover' => '',
				'link' => '',
				'embed' => '',
				'popup' => 0,
				'autoplay' => 0,
				'mute' => 0
			),
			'trx_addons_widget_video'
		) );
		
		do_action( 'trx_addons_action_before_widget_fields', $instance, 'trx_addons_widget_video', $this );
		
		$this->show_field( array(
							'name' => 'title',
							'title' => __('Title:', 'trx_addons'),
							'value' => $instance['title'],
							'type' => 'text'
						) );
		
		do_action('trx_addons_action_after_widget_title', $instance, 'trx_addons_widget_video', $this);

		$this->show_field( array(
							'name' => 'ratio',
							'title' => __('Ratio', 'trx_addons'),
							'value' => $instance['ratio'],
							'options' => trx_addons_get_list_sc_image_ratio( false, false ),
							'dependency' => array(
								'type' => array( 'hover' ),
							),
							'type' => 'select'
						) );

		$this->show_field( array(
							'name' => 'type',
							'title' => __('Layout', 'trx_addons'),
							'value' => $instance['type'],
							'options' => trx_addons_get_list_widget_video_layouts(),
							'type' => 'select'
						) );

		$this->show_field( array(
							'name' => 'subtitle',
							'title' => __('Subtitle:', 'trx_addons'),
							'value' => $instance['subtitle'],
							'dependency' => array(
								'type' => array( 'hover' ),
							),
							'type' => 'text'
						) );
		
		$this->show_field( array(
							'name' => 'link',
							'title' => __('Link to video:', 'trx_addons'),
							'value' => $instance['link'],
							'type' => 'text'
						) );

		$this->show_field( array(
							'name' => 'embed',
							'title' => __('or paste HTML code to embed video:', 'trx_addons'),
							'dependency' => array(
								'type' => array( '^hover' ),
							),
							'value' => $instance['embed'],
							'type' => 'textarea'
						) );

		$this->show_field( array(
							'name' => 'cover',
							'title' => __('Cover image URL:<br>(leave empty if you not need the cover)', 'trx_addons'),
							'value' => $instance['cover'],
							'type' => 'image'
						) );

		$this->show_field( array(
							'name' => 'autoplay',
							'title' => '',
							'label' => __('Autoplay on load', 'trx_addons'),
							'dependency' => array(
//								'type' => array( '^hover' ),
								'cover' => array( '' ),
							),
							'value' => (int) $instance['autoplay'],
							'type' => 'checkbox'
						) );

		$this->show_field( array(
							'name' => 'mute',
							'title' => '',
							'label' => __('Mute', 'trx_addons'),
//							'dependency' => array(
//								'autoplay' => array( 0 ),
//							),
							'value' => (int) $instance['mute'],
							'type' => 'checkbox'
						) );

		$this->show_field( array(
							'name' => 'popup',
							'title' => '',
							'label' => __('Video in the popup', 'trx_addons'),
							'value' => (int) $instance['popup'],
							'dependency' => array(
								'type' => array( '^hover' ),
								'cover' => array( 'not_empty' ),
							),
							'type' => 'checkbox'
						) );
		
		do_action( 'trx_addons_action_after_widget_fields', $instance, 'trx_addons_widget_video', $this );
	}
}


// Load required styles and scripts for the frontend
if ( !function_exists( 'trx_addons_widget_video_load_scripts_front' ) ) {
	add_action( "wp_enqueue_scripts", 'trx_addons_widget_video_load_scripts_front', TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY );
	add_action( 'trx_addons_action_pagebuilder_preview_scripts', 'trx_addons_widget_video_load_scripts_front', 10, 1 );
	function trx_addons_widget_video_load_scripts_front( $force = false ) {
		trx_addons_enqueue_optimized( 'widget_video', $force, array(
			'css'  => array(
				'trx_addons-widget_video' => array( 'src' => TRX_ADDONS_PLUGIN_WIDGETS . 'video/video.css' ),
			),
			'check' => array(
				array( 'type' => 'sc',  'sc' => 'trx_widget_video' ),
				array( 'type' => 'gb',  'sc' => 'wp:trx-addons/video' ),
				array( 'type' => 'elm', 'sc' => '"widgetType":"trx_widget_video"' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[trx_widget_video' ),
			)
		) );
	}
}
	
// Merge widget specific styles into single stylesheet
if ( !function_exists( 'trx_addons_widget_video_merge_styles' ) ) {
	add_filter("trx_addons_filter_merge_styles", 'trx_addons_widget_video_merge_styles');
	function trx_addons_widget_video_merge_styles($list) {
		$list[ TRX_ADDONS_PLUGIN_WIDGETS . 'video/video.css' ] = false;
		return $list;
	}
}

// Load styles and scripts if present in the cache of the menu
if ( !function_exists( 'trx_addons_widget_video_check_in_html_output' ) ) {
	add_filter( 'trx_addons_filter_get_menu_cache_html', 'trx_addons_widget_video_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_show_layout_from_cache', 'trx_addons_widget_video_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_check_page_content', 'trx_addons_widget_video_check_in_html_output', 10, 1 );
	function trx_addons_widget_video_check_in_html_output( $content = '' ) {
		$args = array(
			'check' => array(
				'class=[\'"][^\'"]*widget_video',
				'class=[\'"][^\'"]*trx_addons_video_player'
			)
		);
		if ( trx_addons_check_in_html_output( 'widget_video', $content, $args ) ) {
			trx_addons_widget_video_load_scripts_front( true );
		}
		return $content;
	}
}


// Add shortcodes
//----------------------------------------------------------------------------
require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_WIDGETS . 'video/video-sc.php';

// Add shortcodes to Elementor
if ( trx_addons_exists_elementor() && function_exists('trx_addons_elm_init') ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_WIDGETS . 'video/video-sc-elementor.php';
}

// Add shortcodes to Gutenberg
if ( trx_addons_exists_gutenberg() && function_exists( 'trx_addons_gutenberg_get_param_id' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_WIDGETS . 'video/video-sc-gutenberg.php';
}

// Add shortcodes to VC
if ( trx_addons_exists_vc() && function_exists( 'trx_addons_vc_add_id_param' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_WIDGETS . 'video/video-sc-vc.php';
}
