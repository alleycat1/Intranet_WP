<?php
/**
 * Widget: Video list for Youtube, Vimeo, etc. embeded video
 *
 * @package ThemeREX Addons
 * @since v1.78.0
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

// Load widget
if (!function_exists('trx_addons_widget_video_list_load')) {
	add_action( 'widgets_init', 'trx_addons_widget_video_list_load' );
	function trx_addons_widget_video_list_load() {
		register_widget( 'trx_addons_widget_video_list' );
	}
}


// Widget 'Video List' Class
//---------------------------------------------------------------
class trx_addons_widget_video_list extends TRX_Addons_Widget {

	function __construct() {
		$widget_ops = array( 'classname' => 'widget_video_list', 'description' => esc_html__('Show video list with videos from posts or from the custom list', 'trx_addons') );
		parent::__construct( 'trx_addons_widget_video_list', esc_html__('ThemeREX Video list', 'trx_addons'), $widget_ops );
	}

	// Show widget
	function widget( $args, $instance ) {

		$instance['title'] = apply_filters( 'widget_title', isset($instance['title']) ? $instance['title'] : '', $instance, $this->id_base );
		$instance['autoplay'] = isset($instance['autoplay']) ? $instance['autoplay'] : 0;
		$instance['post_type'] = isset($instance['post_type']) ? $instance['post_type'] : 'post';
		$instance['taxonomy'] = isset($instance['taxonomy']) ? $instance['taxonomy'] : 'category';
		$instance['category'] = isset($instance['category']) ? (int) $instance['category'] : 0;
		$instance['ids'] = isset($instance['ids']) ? $instance['ids'] : '';
		$instance['count'] = isset($instance['count']) ? (int) $instance['count'] : 5;
		$instance['offset'] = isset($instance['offset']) ? (int) $instance['offset'] : 0;
		$instance['orderby'] = isset($instance['orderby']) ? $instance['orderby'] : 'none';
		$instance['order'] = isset($instance['order']) ? $instance['order'] : 'asc';
		$instance['controller_style'] = isset($instance['controller_style']) ? $instance['controller_style'] : "default";
		$instance['controller_pos'] = isset($instance['controller_pos']) ? $instance['controller_pos'] : "right";
		$instance['controller_height'] = isset($instance['controller_height']) ? $instance['controller_height'] : '';
		$instance['controller_autoplay'] = isset($instance['controller_autoplay']) ? $instance['controller_autoplay'] : 0;
		$instance['controller_link'] = isset($instance['controller_link']) ? $instance['controller_link'] : '';
		$instance['videos'] = isset($instance['videos']) ? $instance['videos'] : array();

		// Get Videos from posts
		if ( ! is_array($instance['videos']) 
			|| count($instance['videos']) == 0
			|| count($instance['videos'][0]) == 0
			|| ( empty($instance['videos'][0]['image'])
				&& empty($instance['videos'][0]['video_url'])
				&& empty($instance['videos'][0]['video_embed']) 
				)
		) {
			if ( ! empty($instance['ids'])) {
				if ( is_array( $instance['ids'] ) ) {
					$instance['ids'] = join(',', $instance['ids']);
				}
				$posts = explode(',', $instance['ids']);
				$instance['count'] = count($posts);
			}
		
			$q_args = array(
				'post_type' => $instance['post_type'],
				'post_status' => 'publish',
				'posts_per_page' => $instance['count'],
				'ignore_sticky_posts' => true,
				'offset' => $instance['offset']
			);
	
			if ( $instance['post_type'] == 'post' ) {
				$q_args = trx_addons_query_add_filters( $q_args, 'video' );
			}
			$q_args = trx_addons_query_add_posts_and_cats( $q_args, $instance['ids'], $instance['post_type'], $instance['category'], $instance['taxonomy'] );
			$q_args = trx_addons_query_add_sort_order($q_args, $instance['orderby'], $instance['order']);

			$q_args = apply_filters( 'trx_addons_filter_query_args', $q_args, 'widget_video_list' );
			
			$query  = new WP_Query( apply_filters( 'trx_addons_filter_video_list_query_args', $q_args, $instance ) );

			$num = 0;
			
			$instance['videos'] = array();
			while ( $query->have_posts() ) { $query->the_post();
				$rez = trx_addons_extract_post_video();
				if ( ! empty( $rez['video_url'] ) || ! empty( $rez['video_embed'] ) ) {
					$num++;
					$instance['videos'][] = apply_filters('trx_addons_filter_video_list_content', array(
						'image'  => get_post_thumbnail_id( get_the_ID() ),
						'title'=> get_the_title(),
						'subtitle' => trx_addons_get_post_terms(', ', get_the_ID(), $instance['taxonomy']),	//get_the_category_list(', '),
						'meta' => apply_filters('trx_addons_filter_get_post_date', get_the_date()),
						'link' => get_permalink(),
						'video_url' => ! empty( $rez['video_url'] ) ? $rez['video_url'] : '',
						'video_embed' => ! empty( $rez['video_embed'] ) ? $rez['video_embed'] : '',
						),
						$instance);
					if ( $num >= $instance['count'] ) break;
				}
			}
			wp_reset_postdata();
		}

		// Show player
		if ( is_array($instance['videos']) 
				&& count($instance['videos']) > 0
				&& count($instance['videos'][0]) > 0
				&& ( ! empty($instance['videos'][0]['image'])
						|| ! empty($instance['videos'][0]['video_url'])
						|| ! empty($instance['videos'][0]['video_embed']) 
					)
		) {
			// Load widget-specific scripts and styles
			trx_addons_widget_video_list_load_scripts_front( true );

			// Load template
			trx_addons_get_template_part(TRX_ADDONS_PLUGIN_WIDGETS . 'video_list/tpl.default.php',
										'trx_addons_args_widget_video_list',
										apply_filters('trx_addons_filter_widget_args',
											array_merge($args, $instance),
											$instance, 'trx_addons_widget_video_list')
									);
		}
	}

	// Update the widget settings.
	function update( $new_instance, $instance ) {
		$instance = array_merge($instance, $new_instance);
		$instance['autoplay'] = isset( $new_instance['autoplay'] ) && (int)$new_instance['autoplay'] > 0 ? 1 : 0;
		$instance['category'] = (int) $new_instance['category'];
		$instance['count'] = (int) $new_instance['count'];
		$instance['offset'] = (int) $new_instance['offset'];
		$instance['controller_autoplay'] = isset( $new_instance['controller_autoplay'] ) && (int)$new_instance['controller_autoplay'] > 0 ? 1 : 0;
		$instance['controller_link'] = isset( $new_instance['controller_link'] ) && (int)$new_instance['controller_link'] > 0 ? 1 : 0;
		return apply_filters('trx_addons_filter_widget_args_update', $instance, $new_instance, 'trx_addons_widget_video_list');
	}

	// Displays the widget settings controls on the widget panel.
	function form( $instance ) {
		// Set up some default widget settings
		$instance = wp_parse_args( (array) $instance, apply_filters('trx_addons_filter_widget_args_default', array(
			'title' => '',
			'autoplay' => 0,
			'post_type' => 'post',
			'taxonomy' => 'category',
			'category' => '0',
			'ids' => '',
			'count' => '5',
			'offset' => '0',
			'orderby' => 'none',
			'order' => 'asc',
			'controller_pos' => 'right',
			'controller_style' => 'default',
			'controller_height' => '',
			'controller_autoplay' => 1,
			'controller_link' => 1,
			), 'trx_addons_widget_video_list')
		);
		
		do_action('trx_addons_action_before_widget_fields', $instance, 'trx_addons_widget_video_list', $this);
		
		$this->show_field(array('name' => 'title',
								'title' => __('Title:', 'trx_addons'),
								'value' => $instance['title'],
								'type' => 'text'));

		$this->show_field(array('name' => 'autoplay',
								'title' => '',
								'label' => __('Autoplay first video on load', 'trx_addons'),
								'value' => (int) $instance['autoplay'],
								'type' => 'checkbox'));
		
		do_action('trx_addons_action_after_widget_title', $instance, 'trx_addons_widget_video_list', $this);


		// Query parameters
		$this->show_field(array('name' => 'video_list_query_info',
								'title' => __('Query params', 'trx_addons'),
								'type' => 'info'));

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
		$this->show_field(array('name' => 'category',
								'title' => __('Category:', 'trx_addons'),
								'value' => $instance['category'],
								'options' => trx_addons_array_merge(
												array( 0 => trx_addons_get_not_selected_text( ! empty( $tax_obj->label ) ? $tax_obj->label : __( '- Not Selected -', 'trx_addons' ) ) ),
												trx_addons_get_list_terms( false, $instance['taxonomy'], array( 'pad_counts' => true ) )
											),
								'class' => 'trx_addons_terms_selector',
								'type' => 'select'));

		$this->show_fields_query_param( $instance, '', array( 'columns' => false ) );

		// Controller
		$this->show_field(array('name' => 'video_list_controler_info',
								'title' => __('Table of contents', 'trx_addons'),
								'type' => 'info'));

		$this->show_field(array('name' => 'controller_style',
								'title' => __('Style of the TOC:', 'trx_addons'),
								'value' => $instance['controller_style'],
								'options' => trx_addons_get_list_sc_video_list_controller_styles(),
								'type' => 'select'));

		$this->show_field(array('name' => 'controller_pos',
								'title' => __('Position of the TOC:', 'trx_addons'),
								'value' => $instance['controller_pos'],
								'options' => trx_addons_get_list_sc_video_list_controller_positions(),
								'type' => 'select'));

		$this->show_field(array('name' => 'controller_height',
								'title' => __('Height of the TOC:', 'trx_addons'),
								'value' => $instance['controller_height'],
								'dependency' => array(
									'controller_pos' => array( 'bottom' ),
								),
								'type' => 'text'));

		$this->show_field(array('name' => 'controller_autoplay',
								'title' => '',
								'label' => __('Autoplay selected video', 'trx_addons'),
								'value' => (int) $instance['controller_autoplay'],
								'type' => 'checkbox'));

		$this->show_field(array('name' => 'controller_link',
								'title' => 'Show video or go to the post',
								'label' => __('Show video', 'trx_addons'),
								'value' => (int) $instance['controller_link'],
								'type' => 'checkbox'));

		do_action('trx_addons_action_after_widget_fields', $instance, 'trx_addons_widget_video_list', $this);
	}
}


// Load required styles and scripts for the frontend
if ( !function_exists( 'trx_addons_widget_video_list_load_scripts_front' ) ) {
	add_action( "wp_enqueue_scripts", 'trx_addons_widget_video_list_load_scripts_front', TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY );
	add_action( 'trx_addons_action_pagebuilder_preview_scripts', 'trx_addons_widget_video_list_load_scripts_front', 10, 1 );
	function trx_addons_widget_video_list_load_scripts_front( $force = false ) {
		trx_addons_enqueue_optimized( 'widget_video_list', $force, array(
			'css'  => array(
				'trx_addons-widget_video_list' => array( 'src' => TRX_ADDONS_PLUGIN_WIDGETS . 'video_list/video_list.css' ),
			),
			'check' => array(
				array( 'type' => 'sc',  'sc' => 'trx_widget_video_list' ),
				array( 'type' => 'gb',  'sc' => 'wp:trx-addons/video-player' ),
				array( 'type' => 'elm', 'sc' => '"widgetType":"trx_widget_video_list"' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[trx_widget_video_list' ),
			)
		) );
	}
}

// Enqueue responsive styles for frontend
if ( ! function_exists( 'trx_addons_widget_video_list_load_scripts_front_responsive' ) ) {
	add_action( 'wp_enqueue_scripts', 'trx_addons_widget_video_list_load_scripts_front_responsive', TRX_ADDONS_ENQUEUE_RESPONSIVE_PRIORITY );
	add_action( 'trx_addons_action_load_scripts_front_widget_video_list', 'trx_addons_widget_video_list_load_scripts_front_responsive', 10, 1 );
	function trx_addons_widget_video_list_load_scripts_front_responsive( $force = false ) {
		trx_addons_enqueue_optimized_responsive( 'widget_video_list', $force, array(
			'css'  => array(
				'trx_addons-widget_video_list-responsive' => array(
					'src' => TRX_ADDONS_PLUGIN_WIDGETS . 'video_list/video_list.responsive.css',
					'media' => 'lg'
				),
			),
		) );
	}
}

// Merge widget specific styles into single stylesheet
if ( !function_exists( 'trx_addons_widget_video_list_merge_styles' ) ) {
	add_filter("trx_addons_filter_merge_styles", 'trx_addons_widget_video_list_merge_styles');
	function trx_addons_widget_video_list_merge_styles($list) {
		$list[ TRX_ADDONS_PLUGIN_WIDGETS . 'video_list/video_list.css' ] = false;
		return $list;
	}
}

// Merge widget's specific styles to the single stylesheet (responsive)
if ( !function_exists( 'trx_addons_widget_video_list_merge_styles_responsive' ) ) {
	add_filter("trx_addons_filter_merge_styles_responsive", 'trx_addons_widget_video_list_merge_styles_responsive');
	function trx_addons_widget_video_list_merge_styles_responsive($list) {
		$list[ TRX_ADDONS_PLUGIN_WIDGETS . 'video_list/video_list.responsive.css' ] = false;
		return $list;
	}
}

// Load styles and scripts if present in the cache of the menu
if ( !function_exists( 'trx_addons_widget_video_list_check_in_html_output' ) ) {
//	add_filter( 'trx_addons_filter_get_menu_cache_html', 'trx_addons_widget_video_list_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_show_layout_from_cache', 'trx_addons_widget_video_list_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_check_page_content', 'trx_addons_widget_video_list_check_in_html_output', 10, 1 );
	function trx_addons_widget_video_list_check_in_html_output( $content = '' ) {
		$args = array(
			'check' => array(
				'class=[\'"][^\'"]*widget_video_list',
				'class=[\'"][^\'"]*trx_addons_video_list'
			)
		);
		if ( trx_addons_check_in_html_output( 'widget_video_list', $content, $args ) ) {
			trx_addons_widget_video_list_load_scripts_front( true );
		}
		return $content;
	}
}


// Add shortcodes
//----------------------------------------------------------------------------
require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_WIDGETS . 'video_list/video_list-sc.php';

// Add shortcodes to Elementor
if ( trx_addons_exists_elementor() && function_exists('trx_addons_elm_init') ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_WIDGETS . 'video_list/video_list-sc-elementor.php';
}

// Add shortcodes to Gutenberg
if ( trx_addons_exists_gutenberg() && function_exists( 'trx_addons_gutenberg_get_param_id' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_WIDGETS . 'video_list/video_list-sc-gutenberg.php';
}
