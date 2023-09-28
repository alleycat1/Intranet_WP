<?php
/**
 * Widget: Instagram
 *
 * @package ThemeREX Addons
 * @since v1.6.47
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

// Load widget
if (!function_exists('trx_addons_widget_instagram_load')) {
	add_action( 'widgets_init', 'trx_addons_widget_instagram_load' );
	function trx_addons_widget_instagram_load() {
		register_widget('trx_addons_widget_instagram');
	}
}

// Widget Class
class trx_addons_widget_instagram extends TRX_Addons_Widget {

	function __construct() {
		$widget_ops = array( 'classname' => 'widget_instagram', 'description' => esc_html__('Last Instagram photos.', 'trx_addons') );
		parent::__construct( 'trx_addons_widget_instagram', esc_html__('ThemeREX Instagram Feed', 'trx_addons'), $widget_ops );
	}

	// Show widget
	function widget( $args, $instance ) {

		$title = apply_filters( 'widget_title', isset($instance['title']) ? $instance['title'] : '', $instance, $this->id_base );
		$type = isset($instance['type']) ? $instance['type'] : 'default';
		$demo = isset($instance['demo']) ? $instance['demo'] : 0;
		$demo_files = isset($instance['demo_files']) ? $instance['demo_files'] : array();
		$demo_thumb_size = ! empty($instance['demo_thumb_size'])
								? $instance['demo_thumb_size']
								: apply_filters( 'trx_addons_filter_thumb_size',
													trx_addons_get_thumb_size('avatar'),
													'trx_addons_widget_instagram',
													$instance
												);
		$links = isset($instance['links']) ? $instance['links'] : 'instagram';
		$follow = isset($instance['follow']) ? $instance['follow'] : 0;
		$follow_link = isset($instance['follow_link']) ? $instance['follow_link'] : '';
		$hashtag = isset($instance['hashtag']) ? $instance['hashtag'] : '';
		$count = isset($instance['count']) ? max(1, $instance['count']) : 1;
		$ratio = isset($instance['ratio']) ? $instance['ratio'] : 'none';
		if ( ! empty($demo) ) {
			if ( $links == 'instagram' ) {
				$links = 'popup';
			}
			if ( ! empty( $demo_files ) && is_string( $demo_files ) ) {
				// If images list from Gutenbers
				if ( strpos( $demo_files, '"image_url":' ) !== false ) {
					$demo_files = json_decode( $demo_files, true );

				// Else - images from widget or shortcode
				} else {
					$tmp = explode( '|', $demo_files );
					$demo_files = array();
					foreach( $tmp as $item ) {
						if ( ! empty( $item ) ) {
							$demo_files[] = array( 'image' => $item );
						}
					}
				}
			}
			if ( ! is_array( $demo_files ) ) {
				$demo_files = array();
			}
			$count = count( $demo_files );			
		}
		$columns = isset($instance['columns']) ? max( 1, min( $count, (int) $instance['columns'] ) ) : 1;
		$columns_gap = isset($instance['columns_gap']) ? max( 0, $instance['columns_gap'] ) : 0;

		// Load widget-specific scripts and styles
		trx_addons_widget_instagram_load_scripts_front( true );

		// Load template
		trx_addons_get_template_part(array(
										TRX_ADDONS_PLUGIN_WIDGETS . 'instagram/tpl.'.trx_addons_esc($type).'.php',
										TRX_ADDONS_PLUGIN_WIDGETS . 'instagram/tpl.default.php'
									),
									'trx_addons_args_widget_instagram', 
									apply_filters('trx_addons_filter_widget_args',
												array_merge( $args, compact( 'title',
																			'type',
																			'links',
																			'follow',
																			'follow_link',
																			'demo',
																			'demo_files',
																			'demo_thumb_size',
																			'hashtag',
																			'count',
																			'columns',
																			'columns_gap',
																			'ratio'
																			)
															),
												$instance,
												'trx_addons_widget_instagram')
									);
	}

	// Update the widget settings.
	function update( $new_instance, $instance ) {
		$instance = array_merge($instance, $new_instance);
		$instance['follow'] = isset( $new_instance['follow'] ) && (int) $new_instance['follow'] > 0 ? 1 : 0;
		$instance['follow_link'] = ! empty( $new_instance['follow_link'] ) ? strip_tags( $new_instance['follow_link'] ) : '';
		$instance['demo'] = isset( $new_instance['demo'] ) && (int) $new_instance['demo'] ? 1 : 0;
		$instance['demo_thumb_size'] = ! empty( $new_instance['demo_thumb_size'] ) ? strip_tags( $new_instance['demo_thumb_size'] ) : '';
		$instance['count'] = (int) $new_instance['count'];
		$instance['columns'] = (int) $new_instance['columns'];
		$instance['columns_gap'] = max(0, $new_instance['columns_gap']);
		$instance['ratio'] = ! empty( $new_instance['ratio'] ) ? strip_tags( $new_instance['ratio'] ) : 'none';
		return apply_filters('trx_addons_filter_widget_args_update', $instance, $new_instance, 'trx_addons_widget_instagram');
	}

	// Displays the widget settings controls on the widget panel.
	function form( $instance ) {

		// Set up some default widget settings
		$instance = wp_parse_args( (array) $instance, apply_filters('trx_addons_filter_widget_args_default', array(
			'type' => 'default',
			'title' => '', 
			'follow' => 0,
			'follow_link' => '',
			'demo' => 0,
			'demo_files' => '',
			'demo_thumb_size' => apply_filters( 'trx_addons_filter_thumb_size',
													trx_addons_get_thumb_size( 'avatar' ),
													'trx_addons_widget_instagram',
													$instance
												), 
			'links' => 'instagram',
			'hashtag' => '', 
			'count' => 8,
			'columns' => 4,
			'columns_gap' => 0,
			'ratio' => 'none'
			), 'trx_addons_widget_instagram')
		);
		
		do_action('trx_addons_action_before_widget_fields', $instance, 'trx_addons_widget_instagram', $this);
		
		$this->show_field(array('name' => 'title',
								'title' => __('Title:', 'trx_addons'),
								'value' => $instance['title'],
								'type' => 'text'));
		
		do_action('trx_addons_action_after_widget_title', $instance, 'trx_addons_widget_instagram', $this);
		
		$this->show_field(array('name' => 'type',
								'title' => __('Type', 'trx_addons'),
								'options' => apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('widgets', 'instagram'), 'trx_widget_instagram'),
								'value' => $instance['type'],
								'type' => 'select'));

		$this->show_field(array('name' => 'demo',
								'title' => __('Demo mode', 'trx_addons'),
								'description' => __('Show demo images', 'trx_addons'),
								'value' => (int) $instance['demo'],
								'type' => 'checkbox'));

		$this->show_field(array('name' => 'demo_thumb_size',
								'title' => __('Thumb size', 'trx_addons'),
								'description' => __('Select a thumb size to show images', 'trx_addons'),
								'value' => $instance['demo_thumb_size'],
								'options' => is_admin() ? trx_addons_get_list_thumbnail_sizes() : array(),
								'dependency' => array(
									'demo' => '1'
								),
								'type' => 'select'));

		$this->show_field(array('name' => 'demo_files',
								'title' => __('Demo images', 'trx_addons'),
								'dependency' => array(
									'demo' => '1'
								),
								'value' => $instance['demo_files'],
								'multiple' => true,
								'type' => 'image'));

		$this->show_field(array('name' => 'hashtag',
								'title' => __('Hash tag', 'trx_addons'),
								'description' => __('Filter photos by hashtag. If empty - display all recent photos', 'trx_addons'),
								'value' => $instance['hashtag'],
								'dependency' => array(
									'demo' => '0'
								),
								'type' => 'text'));
		
		$this->show_field(array('name' => 'count',
								'title' => __('Number of photos', 'trx_addons'),
								'dependency' => array(
									'demo' => '0'
								),
								'value' => max(1, min(30, (int) $instance['count'])),
								'type' => 'text'));
		
		$this->show_field(array('name' => 'columns',
								'title' => __('Columns', 'trx_addons'),
								'value' => max(1, min(12, (int) $instance['columns'])),
								'type' => 'text'));

		$this->show_field(array('name' => 'columns_gap',
								'title' => __('Columns gap', 'trx_addons'),
								'value' => max(0, (int) $instance['columns_gap']),
								'type' => 'text'));
		
		$this->show_field(array('name' => 'links',
								'title' => __('Link images to', 'trx_addons'),
								'description' => __('Where to send a visitor after clicking on the picture', 'trx_addons'),
								'value' => $instance['links'],
								'options' => trx_addons_get_list_sc_instagram_redirects(),
								'dependency' => array(
									'demo' => '0'
								),
								'type' => 'select'));
		
		$this->show_field(array('name' => 'ratio',
								'title' => __('Image ratio', 'trx_addons'),
								'description' => __('Select a ratio to show images. Default leave original ratio for each image', 'trx_addons'),
								'value' => $instance['ratio'],
								'options' => trx_addons_get_list_sc_image_ratio( false ),
								'type' => 'select'));

		$this->show_field(array('name' => 'follow',
								'title' => __('Show button "Follow me"', 'trx_addons'),
								'description' => __('Add button "Follow me" after images', 'trx_addons'),
								'value' => (int) $instance['follow'],
								'type' => 'checkbox'));

		$this->show_field(array('name' => 'follow_link',
								'title' => __('Follow link', 'trx_addons'),
								'value' => $instance['follow_link'],
								'dependency' => array(
									'follow' => '1'
								),
								'type' => 'text'));
		
		do_action('trx_addons_action_after_widget_fields', $instance, 'trx_addons_widget_instagram', $this);
	}
}


// Load required styles and scripts for the frontend
if ( !function_exists( 'trx_addons_widget_instagram_load_scripts_front' ) ) {
	add_action( "wp_enqueue_scripts", 'trx_addons_widget_instagram_load_scripts_front', TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY );
	add_action( 'trx_addons_action_pagebuilder_preview_scripts', 'trx_addons_widget_instagram_load_scripts_front', 10, 1 );
	function trx_addons_widget_instagram_load_scripts_front( $force = false ) {
		trx_addons_enqueue_optimized( 'widget_instagram', $force, array(
			'css'  => array(
				'trx_addons-widget_instagram' => array( 'src' => TRX_ADDONS_PLUGIN_WIDGETS . 'instagram/instagram.css' ),
			),
			'check' => array(
				array( 'type' => 'sc',  'sc' => 'trx_widget_instagram' ),
				array( 'type' => 'gb',  'sc' => 'wp:trx-addons/instagram' ),
				array( 'type' => 'elm', 'sc' => '"widgetType":"trx_widget_instagram"' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[trx_widget_instagram' ),
			)
		) );
	}
}

// Enqueue responsive styles for frontend
if ( ! function_exists( 'trx_addons_widget_instagram_load_scripts_front_responsive' ) ) {
	add_action( 'wp_enqueue_scripts', 'trx_addons_widget_instagram_load_scripts_front_responsive', TRX_ADDONS_ENQUEUE_RESPONSIVE_PRIORITY );
	add_action( 'trx_addons_action_load_scripts_front_widget_instagram', 'trx_addons_widget_instagram_load_scripts_front_responsive', 10, 1 );
	function trx_addons_widget_instagram_load_scripts_front_responsive( $force = false ) {
		trx_addons_enqueue_optimized_responsive( 'widget_instagram', $force, array(
			'css'  => array(
				'trx_addons-widget_instagram-responsive' => array(
					'src' => TRX_ADDONS_PLUGIN_WIDGETS . 'instagram/instagram.responsive.css',
					'media' => 'md'
				),
			),
		) );
	}
}

// Merge widget specific styles to the single stylesheet
if ( !function_exists( 'trx_addons_widget_instagram_merge_styles' ) ) {
	add_filter("trx_addons_filter_merge_styles", 'trx_addons_widget_instagram_merge_styles');
	function trx_addons_widget_instagram_merge_styles($list) {
		$list[ TRX_ADDONS_PLUGIN_WIDGETS . 'instagram/instagram.css' ] = false;
		return $list;
	}
}

// Merge widget specific styles to the single stylesheet (responsive)
if ( !function_exists( 'trx_addons_widget_instagram_merge_styles_responsive' ) ) {
	add_filter("trx_addons_filter_merge_styles_responsive", 'trx_addons_widget_instagram_merge_styles_responsive');
	function trx_addons_widget_instagram_merge_styles_responsive($list) {
		$list[ TRX_ADDONS_PLUGIN_WIDGETS . 'instagram/instagram.responsive.css' ] = false;
		return $list;
	}
}

// Load styles and scripts if present in the cache of the menu
if ( !function_exists( 'trx_addons_widget_instagram_check_in_html_output' ) ) {
	add_filter( 'trx_addons_filter_get_menu_cache_html', 'trx_addons_widget_instagram_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_show_layout_from_cache', 'trx_addons_widget_instagram_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_check_page_content', 'trx_addons_widget_instagram_check_in_html_output', 10, 1 );
	function trx_addons_widget_instagram_check_in_html_output( $content = '' ) {
		$args = array(
			'check' => array(
				'class=[\'"][^\'"]*widget_instagram'
			)
		);
		if ( trx_addons_check_in_html_output( 'widget_instagram', $content, $args ) ) {
			trx_addons_widget_instagram_load_scripts_front( true );
		}
		return $content;
	}
}


// Load required styles and scripts for the admin
if ( !function_exists( 'trx_addons_widget_instagram_load_scripts_admin' ) ) {
	add_action("admin_enqueue_scripts", 'trx_addons_widget_instagram_load_scripts_admin');
	function trx_addons_widget_instagram_load_scripts_admin() {
		wp_enqueue_script( 'trx_addons-widget_instagram', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_WIDGETS . 'instagram/instagram_admin.js'), array('jquery'), null, true );
	}
}

// Localize admin scripts
if ( !function_exists( 'trx_addons_widget_instagram_localize_script_admin' ) ) {
	add_action("trx_addons_filter_localize_script_admin", 'trx_addons_widget_instagram_localize_script_admin');
	function trx_addons_widget_instagram_localize_script_admin($vars) {
		$nonce = get_transient( 'trx_addons_instagram_nonce' );
		if ( empty( $nonce ) ) {
			$nonce = md5( mt_rand() );
			set_transient( 'trx_addons_instagram_nonce', $nonce, 60*60 );
		}
		$client_id  = trx_addons_get_option('api_instagram_client_id');
		$vars['api_instagram_get_code_uri'] = 'https://api.instagram.com/oauth/authorize/'
												. '?client_id=' . urlencode( trx_addons_widget_instagram_get_client_id() )
												. '&scope=user_profile,user_media'		//basic,public_content
												. '&response_type=code'
												. '&redirect_uri=' . urlencode( trx_addons_widget_instagram_rest_get_redirect_url() )
												. '&state=' . urlencode( $nonce . ( empty( $client_id ) ? '|' . trx_addons_widget_instagram_rest_get_return_url() : '' ) );
		return $vars;
	}
}

// Return Client ID from Instagram Application
if ( !function_exists( 'trx_addons_widget_instagram_get_client_id' ) ) {
	function trx_addons_widget_instagram_get_client_id() {
		$id = trx_addons_get_option('api_instagram_client_id');
		if ( empty( $id ) ) {
			$id = '106292364902857';
		}
		return $id;
	}
}

// Return Client Secret from Instagram Application
if ( !function_exists( 'trx_addons_widget_instagram_get_client_secret' ) ) {
	function trx_addons_widget_instagram_get_client_secret() {
		return trx_addons_get_option('api_instagram_client_secret');
	}
}


require_once trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_WIDGETS . "instagram/instagram_rest_api.php");


// Add shortcodes
//----------------------------------------------------------------------------
require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_WIDGETS . 'instagram/instagram-sc.php';

// Add shortcodes to Elementor
if ( trx_addons_exists_elementor() && function_exists('trx_addons_elm_init') ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_WIDGETS . 'instagram/instagram-sc-elementor.php';
}

// Add shortcodes to Gutenberg
if ( trx_addons_exists_gutenberg() && function_exists( 'trx_addons_gutenberg_get_param_id' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_WIDGETS . 'instagram/instagram-sc-gutenberg.php';
}

// Add shortcodes to VC
if ( trx_addons_exists_vc() && function_exists( 'trx_addons_vc_add_id_param' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_WIDGETS . 'instagram/instagram-sc-vc.php';
}
