<?php
/**
 * Widget: Twitter
 *
 * @package ThemeREX Addons
 * @since v1.0
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

// Load widget
if (!function_exists('trx_addons_widget_twitter_load')) {
	add_action( 'widgets_init', 'trx_addons_widget_twitter_load' );
	function trx_addons_widget_twitter_load() {
		register_widget('trx_addons_widget_twitter');
	}
}

// Widget Class
class trx_addons_widget_twitter extends TRX_Addons_Widget {

	function __construct() {
		$widget_ops = array( 'classname' => 'widget_twitter', 'description' => esc_html__('Last Twitter Updates. Version for new Twitter API 1.1', 'trx_addons') );
		parent::__construct( 'trx_addons_widget_twitter', esc_html__('ThemeREX Twitter', 'trx_addons'), $widget_ops );
	}

	// Show widget
	function widget( $args, $instance ) {

		if ( empty( $instance['twitter_username'] ) ) {
			return;
		}

		if ( empty( $instance['twitter_api'] ) ) {
			$instance['twitter_api'] = 'token';
		}

		if ( $instance['twitter_api'] == 'token'
			&& (   empty($instance['twitter_consumer_key'])
				|| empty($instance['twitter_consumer_secret'])
				|| empty($instance['twitter_token_key'])
				|| empty($instance['twitter_token_secret'])
				)
		) {
			return;
		}

		if ( $instance['twitter_api'] == 'bearer' && empty( $instance['twitter_bearer'] ) ) {
			return;
		}

		if ( $instance['twitter_api'] == 'token' ) {
			$data = trx_addons_get_twitter_data(array(
				'mode'            => 'user_timeline',
				'consumer_key'    => $instance['twitter_consumer_key'],
				'consumer_secret' => $instance['twitter_consumer_secret'],
				'token'           => $instance['twitter_token_key'],
				'secret'          => $instance['twitter_token_secret']
				)
			);
			if ( ! $data || ! isset( $data[0]['text'] ) ) {
				return;
			}
			$instance['data'] = $data;

		} else if ( $instance['twitter_api'] == 'bearer' ) {
			$data = trx_addons_get_twitter_data_v2( array(
				'mode'     => 'user_timeline',
				'bearer'   => $instance['twitter_bearer'],
				'username' => $instance['twitter_username'],
				'count'    => min( 100, max( 5, (int) $instance['twitter_count'] ) )
				)
			);
			if ( ! $data || ! isset( $data[0]['text'] ) ) {
				return;
			}
			$instance['data'] = $data;
		}

		extract( $args );

		/* Our variables from the widget settings. */
		$layout = $instance['type'] = isset($instance['type']) ? $instance['type'] : 'list';
		$title = apply_filters( 'widget_title', isset($instance['title']) ? $instance['title'] : '', $instance, $this->id_base );
		$bg_image = isset($instance['bg_image']) ? $instance['bg_image'] : '';
		
		// Before widget (defined by themes)
		if ( ! empty( $bg_image ) ) {
			$bg_image = trx_addons_get_attachment_url( $bg_image, trx_addons_get_thumb_size( 'avatar' ) );
			if ( ! empty( $bg_image ) ) {
				$before_widget = str_replace(
					'class="widget ',
					'style="background-image:url(' . esc_url( $bg_image ) . ');"'
						. ' class="widget widget_bg_image ',
					$before_widget
				);
			}
		}

		// Before widget (defined by themes)
		trx_addons_show_layout($before_widget);
			
		// Display the widget title if one was input (before and after defined by themes)
		trx_addons_show_layout($title, $before_title, $after_title);

		// Load widget-specific scripts and styles
		trx_addons_widget_twitter_load_scripts_front( true );

		// Load template
		trx_addons_get_template_part(array(
										TRX_ADDONS_PLUGIN_WIDGETS . 'twitter/tpl.'.trx_addons_esc($layout).'.php',
										TRX_ADDONS_PLUGIN_WIDGETS . 'twitter/tpl.default.php'
										),
										'trx_addons_args_widget_twitter', 
										apply_filters('trx_addons_filter_widget_args',
											$instance,
											$instance, 'trx_addons_widget_twitter')
									);
			
		// After widget (defined by themes). */
		trx_addons_show_layout($after_widget);
	}

	// Update the widget settings.
	function update( $new_instance, $instance ) {
		$instance = array_merge($instance, $new_instance);
		$instance['twitter_count'] = max( 1, (int) $new_instance['twitter_count'] );
		$instance['follow'] = isset( $new_instance['follow'] ) && (int)$new_instance['follow'] > 0 ? 1 : 0;
		$instance['embed_header'] = isset( $new_instance['embed_header'] ) && (int)$new_instance['embed_header'] > 0 ? 1 : 0;
		$instance['embed_footer'] = isset( $new_instance['embed_footer'] ) && (int)$new_instance['embed_footer'] > 0 ? 1 : 0;
		$instance['embed_borders'] = isset( $new_instance['embed_borders'] ) && (int)$new_instance['embed_borders'] > 0 ? 1 : 0;
		$instance['embed_scrollbar'] = isset( $new_instance['embed_scrollbar'] ) && (int)$new_instance['embed_scrollbar'] > 0 ? 1 : 0;
		$instance['embed_transparent'] = isset( $new_instance['embed_transparent'] ) && (int)$new_instance['embed_transparent'] > 0 ? 1 : 0;
		return apply_filters('trx_addons_filter_widget_args_update', $instance, $new_instance, 'trx_addons_widget_twitter');
	}

	// Displays the widget settings controls on the widget panel.
	function form( $instance ) {

		// Set up some default widget settings
		$instance = wp_parse_args( (array) $instance, apply_filters('trx_addons_filter_widget_args_default', array(
			'title' => '',
			'bg_image' => '',
			'twitter_api' => 'bearer',
			'twitter_username' => '',
			'twitter_consumer_key' => '',
			'twitter_consumer_secret' => '',
			'twitter_token_key' => '',
			'twitter_token_secret' => '',
			'twitter_bearer' => '',
			'twitter_count' => 2,
			'follow' => 1,
			'embed_header' => 1,
			'embed_footer' => 1,
			'embed_borders' => 1,
			'embed_scrollbar' => 1,
			'embed_transparent' => 1
			), 'trx_addons_widget_twitter')
		);
		
		do_action('trx_addons_action_before_widget_fields', $instance, 'trx_addons_widget_twitter', $this);
		
		$this->show_field(array('name' => 'title',
								'title' => __('Title:', 'trx_addons'),
								'value' => $instance['title'],
								'type' => 'text'));
		
		do_action('trx_addons_action_after_widget_title', $instance, 'trx_addons_widget_twitter', $this);

		$this->show_field(array('name' => 'twitter_api',
								'title' => __('Twitter API:', 'trx_addons'),
								'value' => ! empty( $instance['twitter_api'] ) ? $instance['twitter_api'] : 'bearer',
								'options' => trx_addons_get_list_sc_twitter_api(),
								'type' => 'select'));

		$this->show_field(array('name' => 'twitter_count',
								'title' => __('Tweets number:', 'trx_addons'),
								'value' => max(1, (int) $instance['twitter_count']),
								'type' => 'text'));

		$this->show_field(array('name' => 'twitter_username',
								'title' => __('Username in Twitter:', 'trx_addons'),
								'value' => $instance['twitter_username'],
								'type' => 'text'));


		// Params for API == 'bearer'

		$this->show_field( array( 'name' => 'twitter_bearer',
								'title' => __('Bearer:', 'trx_addons'),
								'dependency' => array(
									'twitter_api' => array( 'bearer' )
								),
								'value' => $instance['twitter_bearer'],
								'type' => 'text'));

		// End params for API == 'bearer'

		// Params for API == 'embed'

		$this->show_field(array('name' => 'embed_header',
								'title' => __('Show embed header', 'trx_addons'),
								'dependency' => array(
									'twitter_api' => array( 'embed' )
								),
								'value' => 1,
								'type' => 'checkbox'));

		$this->show_field(array('name' => 'embed_footer',
								'title' => __('Show embed footer', 'trx_addons'),
								'dependency' => array(
									'twitter_api' => array( 'embed' )
								),
								'value' => 1,
								'type' => 'checkbox'));

		$this->show_field(array('name' => 'embed_borders',
								'title' => __('Show embed borders', 'trx_addons'),
								'dependency' => array(
									'twitter_api' => array( 'embed' )
								),
								'value' => 1,
								'type' => 'checkbox'));

		$this->show_field(array('name' => 'embed_scrollbar',
								'title' => __('Show embed scrollbar', 'trx_addons'),
								'dependency' => array(
									'twitter_api' => array( 'embed' )
								),
								'value' => 1,
								'type' => 'checkbox'));

		$this->show_field(array('name' => 'embed_transparent',
								'title' => __('Make embed bg transparent', 'trx_addons'),
								'dependency' => array(
									'twitter_api' => array( 'embed' )
								),
								'value' => 1,
								'type' => 'checkbox'));

		// End params for API == 'embed'

		// Params for API == 'token'

		$this->show_field(array('name' => 'twitter_consumer_key',
								'title' => __('Consumer Key:', 'trx_addons'),
								'dependency' => array(
									'twitter_api' => array( 'token' )
								),
								'value' => $instance['twitter_consumer_key'],
								'type' => 'text'));
		
		$this->show_field(array('name' => 'twitter_consumer_secret',
								'title' => __('Consumer Secret:', 'trx_addons'),
								'dependency' => array(
									'twitter_api' => array( 'token' )
								),
								'value' => $instance['twitter_consumer_secret'],
								'type' => 'text'));
		
		$this->show_field(array('name' => 'twitter_token_key',
								'title' => __('Token Key:', 'trx_addons'),
								'dependency' => array(
									'twitter_api' => array( 'token' )
								),
								'value' => $instance['twitter_token_key'],
								'type' => 'text'));
		
		$this->show_field(array('name' => 'twitter_token_secret',
								'title' => __('Token Secret:', 'trx_addons'),
								'dependency' => array(
									'twitter_api' => array( 'token' )
								),
								'value' => $instance['twitter_token_secret'],
								'type' => 'text'));

		// End params for API == 'token'

		$this->show_field(array('name' => 'follow',
								'title' => '',
								'label' => __('Show "Follow us"', 'trx_addons'),
								'value' => (int) $instance['follow'],
								'type' => 'checkbox'));

		$this->show_field(array('name' => 'bg_image',
								'title' => __('Background image:', 'trx_addons'),
								'value' => $instance['bg_image'],
								'type' => 'image'));
		
		do_action('trx_addons_action_after_widget_fields', $instance, 'trx_addons_widget_twitter', $this);
	}
}


// Return an embed html-layout with tweets
if ( !function_exists( 'trx_addons_widget_twitter_show_embed_layout' ) ) {
	function trx_addons_widget_twitter_show_embed_layout( $args ) {
		// Allowed parameters (see details on //developer.twitter.com/en/docs/twitter-for-websites/timelines/overview)
		// data-width="300" - max width (180 - 520)
		// data-height="300" - max height
		// data-chrome="noheader nofooter noborders noscrollbar transparent"
		$data = array();
		if ( isset( $args['embed_header'] ) && (int)$args['embed_header'] == 0 ) $data[] = 'noheader';
		if ( isset( $args['embed_footer'] ) && (int)$args['embed_footer'] == 0 ) $data[] = 'nofooter';
		if ( isset( $args['embed_borders'] ) && (int)$args['embed_borders'] == 0 ) $data[] = 'noborders';
		if ( isset( $args['embed_scrollbar'] ) && (int)$args['embed_scrollbar'] == 0 ) $data[] = 'noscrollbar';
		if ( isset( $args['embed_transparent'] ) && (int)$args['embed_transparent'] == 1 ) $data[] = 'transparent';
		?><a class="twitter-timeline" href="https://twitter.com/<?php echo urlencode( $args['twitter_username'] ); ?>"
			data-chrome="<php echo join( ' ', $data ); ?>"
			data-tweet-limit="<?php echo esc_attr( $args['twitter_count'] ); ?>"
		><?php
			echo esc_html( sprintf( __( 'Tweets by %s', 'trx_addons' ), $args['twitter_username'] ) );
		?></a><?php
		wp_enqueue_script( 'twitter-widgets', 'https://platform.twitter.com/widgets.js', array(), null, true );
	}
}

// Load required styles and scripts for the frontend
if ( !function_exists( 'trx_addons_widget_twitter_load_scripts_front' ) ) {
	add_action( "wp_enqueue_scripts", 'trx_addons_widget_twitter_load_scripts_front', TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY );
	add_action( 'trx_addons_action_pagebuilder_preview_scripts', 'trx_addons_widget_twitter_load_scripts_front', 10, 1 );
	function trx_addons_widget_twitter_load_scripts_front( $force = false ) {
		trx_addons_enqueue_optimized( 'widget_twitter', $force, array(
			'css'  => array(
				'trx_addons-widget_twitter' => array( 'src' => TRX_ADDONS_PLUGIN_WIDGETS . 'twitter/twitter.css' ),
			),
			'check' => array(
				array( 'type' => 'sc',  'sc' => 'trx_widget_twitter' ),
				array( 'type' => 'gb',  'sc' => 'wp:trx-addons/twitter' ),
				array( 'type' => 'elm', 'sc' => '"widgetType":"trx_widget_twitter"' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[trx_widget_twitter' ),
			)
		) );
	}
}
	
// Merge widget specific styles into single stylesheet
if ( !function_exists( 'trx_addons_widget_twitter_merge_styles' ) ) {
	add_filter("trx_addons_filter_merge_styles", 'trx_addons_widget_twitter_merge_styles');
	function trx_addons_widget_twitter_merge_styles($list) {
		$list[ TRX_ADDONS_PLUGIN_WIDGETS . 'twitter/twitter.css' ] = false;
		return $list;
	}
}

// Load styles and scripts if present in the cache of the menu
if ( !function_exists( 'trx_addons_widget_twitter_check_in_html_output' ) ) {
//	add_filter( 'trx_addons_filter_get_menu_cache_html', 'trx_addons_widget_twitter_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_show_layout_from_cache', 'trx_addons_widget_twitter_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_check_page_content', 'trx_addons_widget_twitter_check_in_html_output', 10, 1 );
	function trx_addons_widget_twitter_check_in_html_output( $content = '' ) {
		$args = array(
			'check' => array(
				'class=[\'"][^\'"]*widget_twitter'
			)
		);
		if ( trx_addons_check_in_html_output( 'widget_twitter', $content, $args ) ) {
			trx_addons_widget_twitter_load_scripts_front( true );
		}
		return $content;
	}
}


// Add shortcodes
//----------------------------------------------------------------------------
require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_WIDGETS . 'twitter/twitter-sc.php';

// Add shortcodes to Elementor
if ( trx_addons_exists_elementor() && function_exists('trx_addons_elm_init') ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_WIDGETS . 'twitter/twitter-sc-elementor.php';
}

// Add shortcodes to Gutenberg
if ( trx_addons_exists_gutenberg() && function_exists( 'trx_addons_gutenberg_get_param_id' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_WIDGETS . 'twitter/twitter-sc-gutenberg.php';
}

// Add shortcodes to VC
if ( trx_addons_exists_vc() && function_exists( 'trx_addons_vc_add_id_param' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_WIDGETS . 'twitter/twitter-sc-vc.php';
}
