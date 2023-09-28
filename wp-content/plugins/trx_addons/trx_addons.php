<?php
/*
Plugin Name: ThemeREX Addons
Plugin URI: http://themerex.net
Description: Add many widgets, shortcodes and custom post types for your theme
Version: 2.25.1
Author: ThemeREX
Author URI: http://themerex.net
Text Domain: trx_addons
Domain Path: /languages
*/

// Don't load directly
if ( ! defined( 'ABSPATH' ) ) exit;

// Current version
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) define( 'TRX_ADDONS_VERSION', '2.25.1' );

// Hooks order for the plugin and theme on action 'after_setup_theme'
// 1 - plugin's components and/or theme register hooks for next filters
//     'trx_addons_filter_options' - to add/remove plugin options array
//     'trx_addons_cpt_list' - to enable/disable plugin's CPT
//     'trx_addons_sc_list' - to enable/disable plugin's shortcodes
//     'trx_addons_widgets_list' - to enable/disable plugin's widgets
//     'trx_addons_cv_enable' - to enable/disable plugin's CV functionality
// 3 - plugin do apply_filters('trx_addons_filter_options', $options) and load options
// 4 - plugin save options (if on the ThemeREX Addons Options page)
// 6 - plugin include components (shortcodes, widgets, CPT, etc.) filtered by theme hooks

// Plugin's storage
if ( ! defined('TRX_ADDONS_PLUGIN_DIR') )				define('TRX_ADDONS_PLUGIN_DIR', plugin_dir_path(__FILE__));
if ( ! defined('TRX_ADDONS_PLUGIN_URL') )				define('TRX_ADDONS_PLUGIN_URL', plugin_dir_url(__FILE__));
if ( ! defined('TRX_ADDONS_PLUGIN_BASE') )				define('TRX_ADDONS_PLUGIN_BASE',dirname(plugin_basename(__FILE__)));

if ( ! defined('TRX_ADDONS_PLUGIN_DIR_INCLUDES') )		define('TRX_ADDONS_PLUGIN_DIR_INCLUDES', TRX_ADDONS_PLUGIN_DIR . 'includes/');

// Pluggable components
if ( ! defined('TRX_ADDONS_PLUGIN_COMPONENTS') )		define('TRX_ADDONS_PLUGIN_COMPONENTS',		'components/');
if ( ! defined('TRX_ADDONS_PLUGIN_DIR_COMPONENTS') )	define('TRX_ADDONS_PLUGIN_DIR_COMPONENTS', TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_COMPONENTS);

// Theme-specific addons
if ( ! defined('TRX_ADDONS_PLUGIN_ADDONS') )			define('TRX_ADDONS_PLUGIN_ADDONS',		'addons/');
if ( ! defined('TRX_ADDONS_PLUGIN_DIR_ADDONS') )		define('TRX_ADDONS_PLUGIN_DIR_ADDONS', TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_ADDONS);

// Enqueue frontend scripts and styles priority
if ( ! defined( 'TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY' ) ) define( 'TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY', 20 );

// Enqueue responsive styles priority
if ( ! defined( 'TRX_ADDONS_ENQUEUE_RESPONSIVE_PRIORITY' ) ) define( 'TRX_ADDONS_ENQUEUE_RESPONSIVE_PRIORITY', 2000 );

// Disable using class WP_Filesystem for regular file operations.
// Attention! WordPress is not recommended use this class for regular file operations.
// Below is a message from WordPress "Theme Check" plugin:
//    WP_Filesystem sould only be used for theme upgrade operations, not for all file operations.
//    Consider using file_get_contents(), scandir() or glob()
if ( ! defined( 'TRX_ADDONS_USE_WP_FILESYSTEM' ) )		 define( 'TRX_ADDONS_USE_WP_FILESYSTEM', false );

// Need to declare next var as global to compatibility with WP-CLI
if ( defined( 'WP_CLI' ) ) {
	global $TRX_ADDONS_STORAGE;
}
$TRX_ADDONS_STORAGE = array(
	// Plugin's custom post types
	'post_types' => array(),
	// Plugin's messages with last operation's result
	'admin_message' => array( 'error' => '', 'success' => ''),
	'front_message' => array( 'error' => '', 'success' => ''),
	// Arguments to register widgets
	'widgets_args' => array(
		'before_widget' => '<aside class="widget %2$s">',	// %1$s - id, %2$s - class
		'after_widget'  => '</aside>',
		'before_title'  => '<h5 class="widget_title">',
		'after_title'   => '</h5>',
	),
	// Responsive resolutions
	'responsive' => array(
		// By size
		'xxl'        => array(                'max' => 1679 ),
		'xl'         => array(                'max' => 1439 ),
		'lg'         => array(                'max' => 1279 ),
		'md_lg'      => array( 'min' =>  768, 'max' => 1279 ),
		'md_over'    => array( 'min' => 1024 ),
		'md'         => array(                'max' => 1023 ),
		'wp_fix'     => array( 'min' =>  601, 'max' =>  782 ),
		'sm'         => array(                'max' =>  767 ),
		'sm_wp'      => array(                'max' =>  600 ),
		'xs'         => array(                'max' =>  479 ),
		// By device
		'wide'       => array( 'min' => 2160 ),
		'desktop'    => array( 'min' => 1680, 'max' => 2159 ),
		'notebook'   => array( 'min' => 1280, 'max' => 1679 ),
		'not_tablet' => array( 'min' => 1280 ),
		'tablet'     => array( 'min' =>  768, 'max' => 1279 ),
		'not_mobile' => array( 'min' =>  768 ),
		'mobile'     => array(                'max' =>  767 ),
		'not_small'  => array( 'min' =>  480 ),
		'small'      => array(                'max' =>  479 ),
	),
	// Components
	'components_list' => array(),
	// Addons
	'addons_list'     => array(),
	// Shortcodes stack
	'sc_stack'        => array(),
	'sc_stack_data'   => array(),
	// Current page template
	'_wp_page_template' => '',
	// CSS and JS enqueue list
	'enqueue_list'    => array(),
	// Catch output sections
	'catch_output'    => array()
);


//-------------------------------------------------------
//-- Plugin init
//-------------------------------------------------------

// Plugin activate hook
if ( ! function_exists( 'trx_addons_activate' ) ) {
	register_activation_hook( __FILE__, 'trx_addons_activate' );
	function trx_addons_activate() {
		// Set this flag to flush rewrite rules on first init
		update_option( 'trx_addons_just_activated', 'yes' );
	}
}


// Add user's roles and capabilities
if ( ! function_exists( 'trx_addons_role_and_caps_init' ) ) {
	add_action( 'init', 'trx_addons_role_and_caps_init', 1 );
	function trx_addons_role_and_caps_init() {
		// Register user's roles once
		if ( (int) get_option( 'trx_addons_roles_and_caps_added' ) < 1 ) {
			do_action( 'trx_addons_action_add_roles_and_caps' );
			update_option( 'trx_addons_roles_and_caps_added', 1 );
		}
	}
}


// Return a list with plugin-specific thumb sizes
if ( ! function_exists( 'trx_addons_get_thumb_sizes' ) ) {
	function trx_addons_get_thumb_sizes() {
		return apply_filters('trx_addons_filter_add_thumb_sizes', array(
			'trx_addons-thumb-huge'			=> array(1170,658, true),
			'trx_addons-thumb-big'			=> array(760, 428, true),
			'trx_addons-thumb-medium'		=> array(370, 208, true),
			'trx_addons-thumb-small'		=> array(270, 152, true),
			'trx_addons-thumb-portrait'		=> array(370, 493, true),
			'trx_addons-thumb-avatar'		=> array(370, 370, true),
			'trx_addons-thumb-tiny'			=> array( 75,  75, true),
			'trx_addons-thumb-masonry-big'	=> array(760,   0, false),	// Only downscale, not crop
			'trx_addons-thumb-masonry'		=> array(370,   0, false)	// Only downscale, not crop
			)
		);
	}
}


// Plugin init (after init custom post types and after all other plugins)
if ( ! function_exists( 'trx_addons_init' ) ) {
	add_action( 'init', 'trx_addons_init', 11 );
	function trx_addons_init() {
		// Add thumb sizes
		$thumb_sizes = trx_addons_get_thumb_sizes();
		$mult = trx_addons_get_option('retina_ready', 1);
		foreach ($thumb_sizes as $k=>$v) {
			// Add Original dimensions
			add_image_size( $k, $v[0], $v[1], $v[2]);
			// Add Retina dimensions
			if ($mult > 1) add_image_size( $k.'-@retina', $v[0]*$mult, $v[1]*$mult, $v[2]);
		}

		// If this is first run
		if ( get_option( 'trx_addons_just_activated' ) == 'yes' ) {
			do_action( 'trx_addons_first_run' );
			update_option( 'trx_addons_just_activated', 'no' );
		}
	}
}

// Flush rewrite rules on first run
if ( !function_exists('trx_addons_flush_rewrite_rules') ) {
	add_action( 'trx_addons_first_run', 'trx_addons_flush_rewrite_rules' );
	function trx_addons_flush_rewrite_rules() {
		flush_rewrite_rules();
	}
}


//-------------------------------------------------------
//-- Featured images
//-------------------------------------------------------
if ( !function_exists('trx_addons_image_sizes') ) {
	add_filter( 'image_size_names_choose', 'trx_addons_image_sizes' );
	function trx_addons_image_sizes( $sizes ) {
		$thumb_sizes = apply_filters('trx_addons_filter_add_thumb_names', array(
			'trx_addons-thumb-big'		=> esc_html__( 'Large image', 'trx_addons' ),
			'trx_addons-thumb-med'		=> esc_html__( 'Medium image', 'trx_addons' ),
			'trx_addons-thumb-small'	=> esc_html__( 'Small image', 'trx_addons' ),
			'trx_addons-thumb-portrait'	=> esc_html__( 'Portrait', 'trx_addons' ),
			'trx_addons-thumb-avatar'	=> esc_html__( 'Big square avatar', 'trx_addons' ),
			'trx_addons-thumb-tiny'		=> esc_html__( 'Small square avatar', 'trx_addons' ),
			'trx_addons-thumb-masonry'	=> esc_html__( 'Masonry (scaled)', 'trx_addons' )
			)
		);
		$mult = trx_addons_get_option('retina_ready', 1);
		foreach($thumb_sizes as $k=>$v) {
			$sizes[$k] = $v;
			if ($mult > 1) $sizes[$k.'-@retina'] = $v.' '.esc_html__('@2x', 'trx_addons' );
		}
		return $sizes;
	}
}


//-------------------------------------------------------
//-- Body classes
//-------------------------------------------------------

// Add plugin-specific classes to the body tag
if ( ! function_exists('trx_addons_add_body_classes') ) {
	add_filter( 'body_class', 'trx_addons_add_body_classes' );
	function trx_addons_add_body_classes( $classes ) {
		if ( (int) trx_addons_get_option('hide_fixed_rows') > 0 ) {
			$classes[] = 'hide_fixed_rows_enabled';
		}
		return $classes;
	}
}


//-------------------------------------------------------
//-- Load scripts and styles
//-------------------------------------------------------

// Redirect browser 'Safari mobile' from iframe-version to the whole page version
// because it incorrectly detect height of the window in the iframe
if ( !function_exists( 'trx_addons_safari_to_top' ) ) {
	add_action('wp_head', 'trx_addons_safari_to_top', 0);
	function trx_addons_safari_to_top() {
		if ( wp_is_mobile() ) {	// && isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'themeforest')) {
			?><script>navigator.userAgent.match(/iPad|iPhone|iPod/i) != null && window.name != '' && top.location != window.location && (top.location.href = window.location.href);</script><?php
		}
	}
}

	
// Load required styles and scripts in the frontend
if ( !function_exists( 'trx_addons_load_scripts_external' ) ) {
	add_action("wp_enqueue_scripts", 'trx_addons_load_scripts_external');
	function trx_addons_load_scripts_external() {
		// Load Popup script and styles
		trx_addons_enqueue_popup();
		// TweenMax library
		if ( trx_addons_get_option( 'smooth_scroll' ) || apply_filters( 'trx_addons_filter_load_tweenmax', false ) ) {
			trx_addons_enqueue_tweenmax( array(
				'ScrollTo' => trx_addons_get_option( 'smooth_scroll' ),
			) );
		}
	}
}

// Add styles to the list to future load
if ( ! function_exists( 'trx_addons_load_frontend_scripts' ) ) {
	add_action( 'trx_addons_action_load_scripts_front', 'trx_addons_load_frontend_scripts', 1, 3 );
	function trx_addons_load_frontend_scripts( $force = false, $slug = '', $value = 1 ) {
		global $TRX_ADDONS_STORAGE;
		$TRX_ADDONS_STORAGE['enqueue_list'][$slug] = $value;
		do_action( "trx_addons_action_load_scripts_front_{$slug}", $force );
	}
}

// Check if need to load styles
if ( ! function_exists( 'trx_addons_need_frontend_scripts' ) ) {
	function trx_addons_need_frontend_scripts( $slug, $value = 1 ) {
		global $TRX_ADDONS_STORAGE;
		return isset( $TRX_ADDONS_STORAGE['enqueue_list'][$slug] ) && $TRX_ADDONS_STORAGE['enqueue_list'][$slug] == $value;
	}
}


// Font with icons must be loaded before main stylesheet
if ( !function_exists( 'trx_addons_load_icons_front' ) ) {
	add_action("wp_enqueue_scripts", 'trx_addons_load_icons_front', 0);
	function trx_addons_load_icons_front() {
		wp_enqueue_style( 'trx_addons-icons', trx_addons_get_file_url('css/font-icons/css/trx_addons_icons.css'), array(), null );
	}
}


// Load required styles and scripts in the frontend
if ( !function_exists( 'trx_addons_load_scripts_front' ) ) {
	add_action("wp_enqueue_scripts", 'trx_addons_load_scripts_front', TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY);
	function trx_addons_load_scripts_front() {

		// If 'debug_mode' is off - load merged styles and scripts
		if ( trx_addons_is_off(trx_addons_get_option('debug_mode')) ) {
			wp_enqueue_style( 'trx_addons', trx_addons_get_file_url('css/__styles' . ( trx_addons_is_preview() || trx_addons_is_off( trx_addons_get_option( 'optimize_css_and_js_loading' ) ) ? '-full' : '' ) . '.css'), array(), null );
			wp_enqueue_script( 'trx_addons', trx_addons_get_file_url('js/__scripts' . ( trx_addons_is_preview() || trx_addons_is_off( trx_addons_get_option( 'optimize_css_and_js_loading' ) ) ? '-full' : '' ) . '.js'), apply_filters( 'trx_addons_filter_script_deps', array('jquery') ), null, true );

		// else load all scripts separate
		} else {
			wp_enqueue_style( 'trx_addons', trx_addons_get_file_url('css/trx_addons.front.css'), array(), null );
			wp_enqueue_style( 'trx_addons-hovers', trx_addons_get_file_url('css/trx_addons.hovers.css'), array(), null );
			wp_enqueue_script( 'trx_addons-utils', trx_addons_get_file_url('js/trx_addons.utils.js'), array('jquery'), null, true );
			wp_enqueue_script( 'trx_addons', trx_addons_get_file_url('js/trx_addons.front.js'), array('jquery'), null, true );
		}

		// Conditions to load animations.css
		if ( ! apply_filters( 'trx_addons_filter_disable_load_animation', false ) && ( ! wp_is_mobile() || ! apply_filters( 'trx_addons_filter_disable_animation_on_mobile', false ) ) ) {
			wp_enqueue_style( 'trx_addons-animations',	trx_addons_get_file_url('css/trx_addons.animations.css'), array(), null );
		}
	}
}

// Load responsive styles
if ( !function_exists( 'trx_addons_load_scripts_responsive' ) ) {
	add_action('wp_enqueue_scripts', 'trx_addons_load_scripts_responsive', TRX_ADDONS_ENQUEUE_RESPONSIVE_PRIORITY);
	function trx_addons_load_scripts_responsive() {
		// If 'debug_mode' is off - load merged styles and scripts
		if ( trx_addons_is_off(trx_addons_get_option('debug_mode')) ) {
			wp_enqueue_style( 
				'trx_addons-responsive', 
				trx_addons_get_file_url('css/__responsive' . ( trx_addons_is_preview() || trx_addons_is_off( trx_addons_get_option( 'optimize_css_and_js_loading' ) ) ? '-full' : '' ) . '.css'),
				array(),
				null,
				trx_addons_media_for_load_css_responsive( 'main', 'xl' )
			);
		} else {
			wp_enqueue_style(
				'trx_addons-responsive', 
				trx_addons_get_file_url('css/trx_addons.responsive.css'), 
				array(), 
				null, 
				trx_addons_media_for_load_css_responsive( 'main', 'xl' )
			);
		}
	}
}

// Media for load responsive CSS
if ( !function_exists( 'trx_addons_media_for_load_css_responsive' ) ) {
	function trx_addons_media_for_load_css_responsive( $slug = 'main', $media = 'all' ) {
		global $TRX_ADDONS_STORAGE;
		$condition = 'all';
		$media = apply_filters( 'trx_addons_filter_media_for_load_css_responsive', $media, $slug );
		if ( ! empty( $TRX_ADDONS_STORAGE['responsive'][ $media ]['max'] ) ) {
			$condition = sprintf( '(max-width:%dpx)', $TRX_ADDONS_STORAGE['responsive'][ $media ]['max'] );
		} 
		return apply_filters( 'trx_addons_filter_condition_for_load_css_responsive', $condition, $slug );
	}
}

if ( ! function_exists( 'trx_addons_media_for_load_css_responsive_callback' ) ) {
	add_filter( 'trx_addons_filter_media_for_load_css_responsive', 'trx_addons_media_for_load_css_responsive_callback', 10, 2 );
	/**
	 * Return a maximum 'media' slug to use as a default value for all responsive css-files
	 * (if corresponding media is not detected by a specified slug).
	 *
	 * Hooks: add_filter( 'trx_addons_filter_media_for_load_css_responsive', 'trx_addons_media_for_load_css_responsive_callback', 10, 2 );
	 *
	 * @param string $media  A current media descriptor.
	 * @param string $slug   A current slug to detect a media descriptor. Not used in this function.
	 *
	 * @return string        A default media descriptor, if media stay equal to 'all' after all previous hooks.
	 */
	function trx_addons_media_for_load_css_responsive_callback( $media, $slug ) {
		return 'all' == $media ? 'xxl' : $media;
	}
}

// Add variables to the frontend
if ( !function_exists( 'trx_addons_localize_scripts_front' ) ) {
	add_action("wp_footer", 'trx_addons_localize_scripts_front');
	function trx_addons_localize_scripts_front() {
		wp_localize_script( 'trx_addons', 'TRX_ADDONS_STORAGE', apply_filters('trx_addons_filter_localize_script', array(
			'admin_mode' => false,
			// AJAX parameters
			'ajax_url'	=> esc_url(admin_url('admin-ajax.php')),
			'ajax_nonce'=> esc_attr(wp_create_nonce(admin_url('admin-ajax.php'))),
			// Site base url
			'site_url'	=> esc_url(get_home_url()),
			// Is single page/post
			'post_id' => get_the_ID(),
			// VC frontend edit mode
			'vc_edit_mode'	=> function_exists('trx_addons_vc_is_frontend') && trx_addons_vc_is_frontend(),
			// Is preview mode
			'is_preview'	=> trx_addons_is_preview(),
			'is_preview_gb'	=> trx_addons_is_preview( 'gutenberg' ),
			'is_preview_elm'=> trx_addons_is_preview( 'elementor' ),
			// Popup engine
			'popup_engine'=> trx_addons_get_option('popup_engine'),
			// Show scroll progress
			'scroll_progress' => trx_addons_is_singular( 'post' ) ? trx_addons_get_option('scroll_progress') : 'hide',
			// Hide fixed rows on scroll down
			'hide_fixed_rows' => trx_addons_get_option('hide_fixed_rows'),
			// Smooth scroll
			'smooth_scroll' => trx_addons_get_option( 'smooth_scroll' ) && ! trx_addons_is_preview(),
			// Animate to the inner links
			'animate_inner_links' => trx_addons_get_option('animate_inner_links'),
			// Animations on mobile
			'disable_animation_on_mobile' => apply_filters( 'trx_addons_filter_disable_animation_on_mobile', true ),
			// Open external links in a new window
			'add_target_blank' => trx_addons_get_option('add_target_blank'),
			// Use menu collapse
			'menu_collapse' => trx_addons_get_option('menu_collapse'),
			'menu_collapse_icon' => trx_addons_get_option('menu_collapse_icon'),
			// Stretch menu layouts
			'menu_stretch' => trx_addons_get_option('menu_stretch'),
			// Resize video and iframe
			'resize_tag_video' => false,
			'resize_tag_iframe' => true,
			// User logged in
			'user_logged_in' => is_user_logged_in(),
			// Current theme slug
			'theme_slug' => get_template(),
			// Theme  colors
			'theme_bg_color' => apply_filters('trx_addons_filter_get_theme_bg_color', ''),
			'theme_accent_color' => apply_filters('trx_addons_filter_get_theme_accent_color', '#efa758'),
			// Theme-specific page wrap class
			'page_wrap_class' => trx_addons_get_option('page_wrap_class'),
			// Columns class template
			'columns_wrap_class' => trx_addons_get_columns_wrap_class(),
			'columns_in_single_row_class' => 'columns_in_single_row',
			'column_class_template' => trx_addons_get_column_class_template(),
			// E-mail mask to validate forms
			'email_mask' => '^([a-zA-Z0-9_\\-]+\\.)*[a-zA-Z0-9_\\-]+@[a-zA-Z0-9_\\-]+(\\.[a-zA-Z0-9_\\-]+)*\\.[a-zA-Z0-9]{2,12}$',
			// Mobile breakpoints for JS (if window width less then)
			'mobile_breakpoint_fixedrows_off' => 768,
			'mobile_breakpoint_fixedcolumns_off' => 768,
			'mobile_breakpoint_stacksections_off' => 768,
			'mobile_breakpoint_scroll_lag_off' => 768,
			'mobile_breakpoint_fullheight_off' => 1025,
			'mobile_breakpoint_mousehelper_off' => 1025,

			// JS Messages for translation
			'msg_caption_yes'           => addslashes(esc_html__( 'Yes', 'trx_addons' )),
			'msg_caption_no'            => addslashes(esc_html__( 'No', 'trx_addons' )),
			'msg_caption_ok'            => addslashes(esc_html__( 'OK', 'trx_addons' )),
			'msg_caption_apply'         => addslashes(esc_html__( 'Apply', 'trx_addons' )),
			'msg_caption_cancel'        => addslashes(esc_html__( 'Cancel', 'trx_addons' )),
			'msg_caption_attention'     => addslashes(esc_html__( 'Attention!', 'trx_addons' )),
			'msg_caption_warning'       => addslashes(esc_html__( 'Warning!', 'trx_addons' )),
			'msg_ajax_error'			=> addslashes(esc_html__('Invalid server answer!', 'trx_addons')),
			'msg_magnific_loading'		=> addslashes(esc_html__('Loading image', 'trx_addons')),
			'msg_magnific_error'		=> addslashes(esc_html__('Error loading image', 'trx_addons')),
			'msg_magnific_close'		=> addslashes(esc_html__('Close (Esc)', 'trx_addons')),
			'msg_error_like'			=> addslashes(esc_html__('Error saving your like! Please, try again later.', 'trx_addons')),
			'msg_field_name_empty'		=> addslashes(esc_html__("The name can't be empty", 'trx_addons')),
			'msg_field_email_empty'		=> addslashes(esc_html__('Too short (or empty) email address', 'trx_addons')),
			'msg_field_email_not_valid'	=> addslashes(esc_html__('Invalid email address', 'trx_addons')),
			'msg_field_text_empty'		=> addslashes(esc_html__("The message text can't be empty", 'trx_addons')),
			'msg_search_error'			=> addslashes(esc_html__('Search error! Try again later.', 'trx_addons')),
			'msg_send_complete'			=> addslashes(esc_html__("Send message complete!", 'trx_addons')),
			'msg_send_error'			=> addslashes(esc_html__('Transmit failed!', 'trx_addons')),
			'msg_validation_error'		=> addslashes(esc_html__('Error data validation!', 'trx_addons')),
			'msg_name_empty' 			=> addslashes(esc_html__("The name can't be empty", 'trx_addons')),
			'msg_name_long'				=> addslashes(esc_html__('Too long name', 'trx_addons')),
			'msg_email_empty'			=> addslashes(esc_html__('Too short (or empty) email address', 'trx_addons')),
			'msg_email_long'			=> addslashes(esc_html__('Too long email address', 'trx_addons')),
			'msg_email_not_valid'		=> addslashes(esc_html__('Invalid email address', 'trx_addons')),
			'msg_text_empty'			=> addslashes(esc_html__("The message text can't be empty", 'trx_addons')),
			'msg_copied'				=> addslashes(esc_html__("Copied!", 'trx_addons')),
		) ) );
	}
}


// Reorder plugin-specific scripts - put all our scripts after the script with the slug 'trx_addons'
// Call after 'trx_addons_action_check_page_content' on priority 18
// Call before 'wp_print_footer_scripts' on priority 20
if ( ! function_exists( 'trx_addons_reorder_scripts' ) ) {
	add_action( 'wp_footer', 'trx_addons_reorder_scripts', 19 );
	function trx_addons_reorder_scripts() {
		global $wp_scripts;
		if ( ! empty( $wp_scripts->queue ) && is_array( $wp_scripts->queue ) 
			&& ! empty( $wp_scripts->registered ) && is_array( $wp_scripts->registered )
		) {
			$our = array();
			$queue = array();
			$start = false;
			foreach ( $wp_scripts->registered as $slug => $data ) {
				if ( $start && ! empty( $wp_scripts->registered[$slug] ) ) {
					if ( ! empty( $data->src ) && is_string( $data->src ) && strpos( strtolower( $data->src ), 'trx_addons' ) > 0 ) {
						$our[ $slug ] = $data;
						unset( $wp_scripts->registered[ $slug ] );
						foreach( $wp_scripts->queue as $k => $v ) {
							if ( $v == $slug ) {
								$queue[] = $slug;
								unset( $wp_scripts->queue[ $k ] );
							}
						}
					}
				} if ( $slug == 'trx_addons' ) {
					$start = true;
				}
			}
			if ( count( $our ) > 0 ) {
				if ( isset( $wp_scripts->registered['trx_addons'] ) ) {
					trx_addons_array_insert_after( $wp_scripts->registered, 'trx_addons', $our );
				} else {
					$wp_scripts->registered = trx_addons_array_merge( $wp_scripts->registered, $our );
				}
			}
			if ( count( $queue ) > 0 ) {
				$found = false;
				foreach( $wp_scripts->queue as $k => $v ) {
					if ( $v == 'trx_addons' ) {
						array_splice( $wp_scripts->queue, $k + 1, 0, $queue );
						$found = true;
						break;
					}
				}
				if ( ! $found ) {
					$wp_scripts->queue = array_merge( $wp_scripts->queue, $queue );
				}
			}
		}
	}
}


// Move other scripts to the footer
if ( ! function_exists( 'trx_addons_move_scripts_down' ) ) {
	add_action( 'wp_enqueue_scripts', 'trx_addons_move_scripts_down', 9999 );
	function trx_addons_move_scripts_down($src) {
		global $wp_scripts;
		if ( trx_addons_is_on( trx_addons_get_option( 'move_scripts_to_footer' ) )
			&& ! empty( $wp_scripts->queue ) && is_array( $wp_scripts->queue ) 
			&& ! empty( $wp_scripts->registered ) && is_array( $wp_scripts->registered )
		) {
			$skip_list = array_merge(
							apply_filters( 'trx_addons_filter_skip_move_scripts_down',
								array(
									'modernizr',
									'jquery.js',
									'jquery.min.js',
									'jquery-migrate',
									'core.js',
									'core.min.js',
									'underscore',
									'wp-polyfill',
									'wp-backbone',
									'wp-util',
									'dom-ready',
									'i18n',
									'a11y',
									'js/dist',
									'tweenmax',
									'tinymce',
									)
							),
							array_map( 'trim', explode( ',', trx_addons_get_option('move_scripts_to_footer_exclude') ) )
						);
			foreach ( $wp_scripts->registered as $slug => $data ) {
				if ( ! empty( $wp_scripts->registered[$slug] ) ) {
					if ( trx_addons_is_on( trx_addons_get_option( 'remove_ver_from_url' ) )
						&& isset( $wp_scripts->registered[$slug]->ver )
					) {
						$wp_scripts->registered[$slug]->ver = null;
					}
					$skip = false;
					foreach ( $skip_list as $s ) {
						if ( empty( $s ) ) continue;
						if ( strpos( strtolower( $slug ), $s ) === 0 || ( !empty( $data->src ) && is_string( $data->src ) && strpos( strtolower( $data->src ), $s ) > 0 ) ) {
							$skip = true;
							break;
						}
					}
					if ( ! $skip ) {
						if ( empty( $wp_scripts->registered[$slug]->extra ) ) {
							$wp_scripts->registered[$slug]->extra = array( 'group' => 1 );
						} else {	// if (empty($wp_scripts->registered[$slug]->extra['group'])) {
							$wp_scripts->registered[$slug]->extra['group'] = 1;
						}
					}
				}
			}
		}
	}
}


// Add attribute 'defer' to the scripts
if ( ! function_exists( 'trx_addons_async_scripts_load' ) ) {
	add_action( 'script_loader_tag', 'trx_addons_async_scripts_load', 9999, 2 );
	function trx_addons_async_scripts_load($tag, $slug) {
		global $wp_scripts;
		if ( trx_addons_is_on( trx_addons_get_option('async_scripts_load') )
			&& ! is_admin()
			&& ! is_customize_preview()
			&& ! empty($wp_scripts->queue) && is_array($wp_scripts->queue) 
			&& ! empty($wp_scripts->registered) && is_array($wp_scripts->registered)
		) {
			$skip_list = array_merge(
							apply_filters( 'trx_addons_filter_skip_async_scripts_load',
								array(
									'modernizr',
									'jquery.js',
									'jquery.min.js',
									'jquery-migrate',
									'core.js',
									'core.min.js',
									'underscore',
									'wp-polyfill',
									'wp-backbone',
									'wp-util',
									'dom-ready',
									'i18n',
									'a11y',
									'js/dist',
									'tweenmax',
									'tinymce',
									)
							),
							array_map( 'trim', explode( ',', trx_addons_get_option('async_scripts_exclude') ) )
						);
			$skip = false;
			foreach ($skip_list as $s) {
				if ( empty( $s ) ) continue;
				if ( strpos( strtolower( $slug ), $s ) === 0 || ( !empty( $wp_scripts->registered[$slug]->src ) && is_string( $wp_scripts->registered[$slug]->src ) && strpos( strtolower( $wp_scripts->registered[$slug]->src ), $s ) > 0 ) ) {
					$skip = true;
					break;
				}
			}
			if ( $skip ) {
				// Remove param 'defer'
				$tag = str_replace( array( 'defer="defer"', 'defer' ), '', $tag );
			} else if ( strpos( $tag, 'defer' ) === false ) {
				// Add param 'defer'
				$tag = str_replace( ' src', ' defer="defer" src', $tag );
			}
		}
		return $tag;
	}
}


// Remove WordPress version parameter from styles and scripts
if (!function_exists('trx_addons_remove_version')) {
	add_filter( 'style_loader_src', 'trx_addons_remove_version', 9999 );
	add_filter( 'script_loader_src', 'trx_addons_remove_version', 9999 );
	function trx_addons_remove_version($src) {
		if ( trx_addons_is_on(trx_addons_get_option('remove_ver_from_url')) && ! is_admin() && strpos( $src, 'ver=') ) {
			$src = remove_query_arg( 'ver', $src );
		}
		return $src;
	}
}

// Disable cache in the debug mode
if ( !function_exists( 'trx_addons_disable_cache_in_the_debug_mode' ) ) {
	add_filter( 'style_loader_src', 'trx_addons_disable_cache_in_the_debug_mode', 9999 );
	add_filter( 'script_loader_src', 'trx_addons_disable_cache_in_the_debug_mode', 9999 );
	function trx_addons_disable_cache_in_the_debug_mode($src) {
		if ( trx_addons_is_on( trx_addons_get_option('disable_css_and_js_cache') )
				&& trx_addons_is_on( trx_addons_get_option('debug_mode') )
// Uncomment if not need to disable cache for the admin mode
//				&& ! is_admin()
				&& ( strpos( $src, 'trx_addons/' ) !== false
					|| strpos( $src, trailingslashit( get_stylesheet() ) ) !== false
					|| strpos( $src, trailingslashit( get_template() ) ) !== false
				)
		) {
			$src = add_query_arg( 'reload', mt_rand(), $src );
		}
		return $src;
	}
}


//-----------------------------------------
//--  Merge JS and CSS
//-----------------------------------------

// Plugin 'trx_addons' activation hook
if ( ! function_exists( 'trx_addons_itself_activated' ) ) {
	register_activation_hook( __FILE__, 'trx_addons_itself_activated' );
	function trx_addons_itself_activated() {
		// Set this flag to regenerate styles and scripts on first run
		if ( apply_filters( 'trx_addons_filter_regenerate_merged_files_after_activate_trx_addons', true ) ) {
			trx_addons_merge_styles_schedule();
		}
	}
}

// Any plugin activated/deactivated - regenerate merged files on next load
if ( ! function_exists( 'trx_addons_any_plugin_change_state' ) ) {
	add_action( 'activated_plugin', 'trx_addons_any_plugin_change_state', 10, 2 );
	add_action( 'deactivated_plugin', 'trx_addons_any_plugin_change_state', 10, 2 );
	function trx_addons_any_plugin_change_state( $plugin, $network_activation ) {
		// Set this flag to regenerate styles and scripts on first run
		if ( apply_filters( 'trx_addons_filter_regenerate_merged_files_after_activate_plugins', true ) ) {
			trx_addons_merge_styles_schedule();
		}
	}
}

// A plugin or a theme was updated via core update screen
if ( ! function_exists( 'trx_addons_update' ) ) {
	add_action( 'upgrader_process_complete', 'trx_addons_update', 10, 2 );
	function trx_addons_update( $upgrader, $hook_extra ) {
		if (   ! empty( $hook_extra['action'] )  && $hook_extra['action'] == 'update'
			&& ! empty( $hook_extra['type'] )    && in_array( $hook_extra['type'], array( 'plugin', 'theme' ) )
			&& ! empty( $hook_extra['bulk'] )
		) {
			if ( ! empty( $hook_extra['plugins'] ) && is_array( $hook_extra['plugins'] ) ) {
				if ( apply_filters( 'trx_addons_filter_regenerate_merged_files_after_update_trx_addons', true ) ) {
					foreach( $hook_extra['plugins'] as $plugin ) {
						if ( strpos( $plugin, 'trx_addons' ) !== false ) {
							// Set this flag to regenerate styles and scripts on first run
							trx_addons_merge_styles_schedule();
							break;
						}
					}
				}
			} else if ( ! empty( $hook_extra['themes'] ) && is_array( $hook_extra['themes'] ) ) {
				if ( apply_filters( 'trx_addons_filter_regenerate_merged_files_after_update_theme', true ) ) {
					$slug = get_template();
					foreach( $hook_extra['themes'] as $theme ) {
						if ( strpos( $theme, $slug ) !== false ) {
							// Set this flag to regenerate styles and scripts on first run
							trx_addons_merge_styles_schedule();
							break;
						}
					}
				}
			}
		}
	}
}

// A plugin or a theme was updated via 'trx_updater'
if ( ! function_exists( 'trx_addons_update2' ) ) {
	add_action( 'trx_updater_action_after_plugin_upgrade', 'trx_addons_update2', 10, 1 );
	add_action( 'trx_updater_action_after_theme_upgrade', 'trx_addons_update2', 10, 1 );
	function trx_addons_update2( $slug ) {
		$theme_slug = get_template();
		if ( ( current_action() == 'trx_updater_action_after_plugin_upgrade' && $slug == 'trx_addons' && apply_filters( 'trx_addons_filter_regenerate_merged_files_after_update_trx_addons', true ) )
			||
			( current_action() == 'trx_updater_action_after_theme_upgrade' && $slug == $theme_slug && apply_filters( 'trx_addons_filter_regenerate_merged_files_after_update_theme', true ) )
		) {
			// Set this flag to regenerate styles and scripts on first run
			trx_addons_merge_styles_schedule();
		}
	}
}

// Shedule action to merge all separate styles and scripts to the single file on next run
if ( !function_exists( 'trx_addons_merge_styles_schedule' ) ) {
	add_action( 'trx_addons_action_importer_import_end', 'trx_addons_merge_styles_schedule');
	function trx_addons_merge_styles_schedule() {
   		// Set this flag to regenerate styles and scripts on first run
		update_option('trx_addons_action', 'trx_addons_action_save_options');
	}
}

// Merge all separate styles and scripts to the single file to increase page upload speed
if ( !function_exists( 'trx_addons_merge_styles' ) ) {
	add_action( 'trx_addons_action_save_options', 'trx_addons_merge_styles', 20 );
	function trx_addons_merge_styles() {
		// Merge styles
		// CSS list must be in the next format:
		// 'relative url for css-file' => true | false
		//     true - merge this file always (to the __styles and to the __styles-full),
		//     false - not merge this file for optimized mode (only to the __styles-full)
		$css_list = apply_filters( 'trx_addons_filter_merge_styles', array(
																		'css/trx_addons.front.css' => true,
																		'css/trx_addons.hovers.css' => true
																		)
								);
		trx_addons_merge_css( 'css/__styles.css', array_keys( $css_list, true ) );
		trx_addons_merge_css( 'css/__styles-full.css', array_keys( $css_list ) );

		// Merge responsive styles
		$css_list = apply_filters( 'trx_addons_filter_merge_styles_responsive', array(
																					'css/trx_addons.responsive.css' => true
																					)
								);
		trx_addons_merge_css( 'css/__responsive.css', array_keys( $css_list, true ), true );
		trx_addons_merge_css( 'css/__responsive-full.css', array_keys( $css_list ), true );

		// Merge scripts
		// JS list must be in the next format:
		// 'relative url for js-file' => true | false
		//     true - merge this file always (to the __scripts and to the __scripts-full),
		//     false - not merge this file for optimized mode (only to the __scripts-full)
		$js_list = apply_filters( 'trx_addons_filter_merge_scripts', array(
																		'js/trx_addons.utils.js' => true,
																		'js/trx_addons.front.js' => true
																		)
								);
		trx_addons_merge_js( 'js/__scripts.js', array_keys( $js_list, true ) );
		trx_addons_merge_js( 'js/__scripts-full.js', array_keys( $js_list ) );
	}
}

// Convert an array items with numeric keys to the new format
// ( to compatibility with old themes )
if ( !function_exists( 'trx_addons_merge_styles_convert_keys' ) ) {
	add_action( 'trx_addons_filter_merge_styles', 'trx_addons_merge_styles_convert_keys', 9999, 1 );
	add_action( 'trx_addons_filter_merge_styles_responsive', 'trx_addons_merge_styles_convert_keys', 9999, 1 );
	add_action( 'trx_addons_filter_merge_scripts', 'trx_addons_merge_styles_convert_keys', 9999, 1 );
	function trx_addons_merge_styles_convert_keys( $list ) {
		if ( is_array( $list ) ) {
			$new_list = array();
			foreach( $list as $k => $v ) {
				if ( is_numeric( $k ) ) {
					$new_list[ $v ] = true;
				} else {
					$new_list[ $k ] = $v;
				}
			}
			$list = $new_list;
			unset( $new_list );
		}
		return $list;
	}
}


//-------------------------------------------------------
//-- Add Google analitics and Google remarketing code
//-------------------------------------------------------

// Add Google Analytics code (before </head>)
if ( ! function_exists( 'trx_addons_add_google_analitics' ) ) {
	add_action( 'wp_head', 'trx_addons_add_google_analitics', 1000 );
	function trx_addons_add_google_analitics() {
		// To prevent call again
		static $done = false;
		if ( $done ) return;
		$done = true;

		$ga = trx_addons_get_option( 'api_google_analitics', '', false );
		if ( ! empty( $ga ) ) {
			trx_addons_show_layout( $ga );
		}
	}
}

// Add Google Remarketing code (before </body>)
if ( ! function_exists( 'trx_addons_add_google_remarketing' ) ) {
	add_action( 'wp_footer', 'trx_addons_add_google_remarketing', 1000 );
	function trx_addons_add_google_remarketing() {
		// To prevent call again
		static $done = false;
		if ( $done ) return;
		$done = true;

		$gr = trx_addons_get_option( 'api_google_remarketing', '', false );
		if ( ! empty( $gr ) ) {
			trx_addons_show_layout( $gr );
		}
	}
}


//-------------------------------------------------------
//-- Load inline html and css
//-------------------------------------------------------

// Load inline html
// Attention! Priority 9 is used because inline html must be loaded before moved scripts
// (see below on action 'wp_enqueue_scripts', 9999), called inside the action 'wp_footer' with the default priority 10
if ( ! function_exists( 'trx_addons_put_inline_html' ) ) {
	add_action( 'wp_footer', 'trx_addons_put_inline_html', 9 );
	add_action( 'admin_footer', 'trx_addons_put_inline_html', 9 );
	function trx_addons_put_inline_html() {
		// To prevent call again
		static $done = false;
		if ( $done ) return;
		$done = true;

		// Put custom html/js, prepared in shortcodes or any other output blocks
		trx_addons_show_layout( apply_filters( 'trx_addons_filter_inline_html', trx_addons_get_inline_html() ) );
	}
}

// Load inline styles
if ( ! function_exists( 'trx_addons_put_inline_css' ) ) {
	add_action( 'wp_footer', 'trx_addons_put_inline_css', 1000 );
	add_action( 'admin_footer', 'trx_addons_put_inline_css', 1000 );
	function trx_addons_put_inline_css() {
		// To prevent call again
		static $done = false;
		if ( $done ) return;
		$done = true;
		// Attention! Don't change id in the tag 'style' - need to properly work the 'view more' script
		trx_addons_show_layout( apply_filters( 'trx_addons_filter_inline_css', trx_addons_get_inline_css() ),
								'<style type="text/css" id="trx_addons-inline-styles-inline-css">',
								'</style>'
								);
	}
}


// Check if print_footer_scripts() was called manually ( not in the action 'wp_footer' )
if ( ! function_exists( 'trx_addons_check_print_footer_scripts' ) ) {
	add_filter( 'print_footer_scripts', 'trx_addons_check_print_footer_scripts', 9999, 1 );
	function trx_addons_check_print_footer_scripts( $doit = true ) {
		static $done = false;
		if ( ! $done && ! doing_action('wp_footer') && ! doing_action('admin_footer') ) {
			$done = true;
			trx_addons_put_inline_html();
			if ( ! is_admin() ) {
				trx_addons_localize_scripts_front();
				trx_addons_check_page_content_for_used_styles_and_scripts();
				trx_addons_reorder_scripts();
			}
			trx_addons_put_inline_css();
		}
		return $doit;
	}
}


//-------------------------------------------------------
//-- Capture 'head' and 'body' output
//-------------------------------------------------------

// Check if capture is enabled
if ( ! function_exists( 'trx_addons_grab_output_allowed' ) ) {
	function trx_addons_grab_output_allowed() {
		return
			// Enable on a page requested via AJAX from frontend
			( ! is_admin() || wp_doing_ajax() )
			// Disable when a REST API is requested (for example, a block "legacy widget" from a new Widgets Editor)
			&& ! trx_addons_check_url( '/wp-json/' )
			// Allow a theme and 3rd-party plugins to disable a page capturing
			&& apply_filters( 'trx_addons_filter_grab_output_allowed', true );
	}
}

// Capture a whole page html output
//-------------------------------------------------------

// New way: Add a hook to start capturing a page.
// A priority 3 is used because an API support are loaded on priority 2
if ( ! function_exists( 'trx_addons_grab_page_start_hook' ) ) {
	add_action( 'after_setup_theme', 'trx_addons_grab_page_start_hook', 3 );
	function trx_addons_grab_page_start_hook() {

		$grab_page_start_action = apply_filters( 'trx_addons_filter_grab_page_start_action', 'after_setup_theme' );	// Was 'wp_head' and 0
		$grab_page_start_priority = apply_filters( 'trx_addons_filter_grab_page_start_priority', 4 );				// and has conflict with the block 'Legacy Widget'

		if ( ! empty( $grab_page_start_action ) ) {
			add_action( $grab_page_start_action, 'trx_addons_grab_page_start', $grab_page_start_priority );
		}
	}
}

// Start to capture an output of the page
if ( ! function_exists( 'trx_addons_grab_page_start' ) ) {
	function trx_addons_grab_page_start() {

		if ( ! trx_addons_grab_output_allowed() ) return;

		global $TRX_ADDONS_STORAGE;
		$TRX_ADDONS_STORAGE['capture_page'] = true;

		ob_start( 'trx_addons_grab_page_end' );
	}
}

// End to capture an output of the page
// and move styles from the 'body' to the 'head'
if ( ! function_exists( 'trx_addons_grab_page_end' ) ) {
	function trx_addons_grab_page_end( $html = '' ) {

		global $TRX_ADDONS_STORAGE;
		$TRX_ADDONS_STORAGE['capture_page'] = false;

		// Parse a raw html output for the head and the body
		$TRX_ADDONS_STORAGE['capture_head_html'] = '';
		$TRX_ADDONS_STORAGE['capture_body_html'] = '';
		if ( ! empty( $html ) ) {
			$pos = strpos( $html, '</head>' );
			if ( $pos !== false ) {
				$TRX_ADDONS_STORAGE['capture_head_html'] = substr( $html, 0, $pos );
				$TRX_ADDONS_STORAGE['capture_body_html'] = substr( $html, $pos );
			}
		}

		// Prepare and output a head and a body (if present)
		if ( ! empty( $TRX_ADDONS_STORAGE['capture_head_html'] ) && ! empty( $TRX_ADDONS_STORAGE['capture_body_html'] ) ) {

			// Move styles from the folder 'trx_addons' from the body to the head
			trx_addons_move_own_styles_to_head();

			// Move inline styles of 'trx_addons' from the body to the head
			trx_addons_move_own_inline_styles_to_head();

			// Move styles from the theme and child-theme folders from the body to the head
			trx_addons_move_theme_styles_to_head();

			// Move other styles and links to the head
			trx_addons_move_other_styles_to_head();

			// Replace a buffer with the modified content of the head and the body
			$html = apply_filters( 'trx_addons_filter_page_head', $TRX_ADDONS_STORAGE['capture_head_html'] )
					. apply_filters( 'trx_addons_filter_page_content', $TRX_ADDONS_STORAGE['capture_body_html'] );

			$TRX_ADDONS_STORAGE['capture_head_html'] = '';
			$TRX_ADDONS_STORAGE['capture_body_html'] = '';

		}

		return $html;
	}
}

// Move tags 'link' from the folder 'trx_addons' from the body to the head
if ( ! function_exists( 'trx_addons_move_own_styles_to_head' ) ) {
	function trx_addons_move_own_styles_to_head() {
		global $TRX_ADDONS_STORAGE;
		$TRX_ADDONS_STORAGE['capture_links'] = '';
		$TRX_ADDONS_STORAGE['capture_body_html'] = preg_replace_callback(
			'/<link [^>]*(href=[\'"][^\'"]*trx_addons[^\'"]*[\'"])[^>]*>/',
			function( $matches ) {
				if ( ! empty( $matches[0] ) ) {
					global $TRX_ADDONS_STORAGE;
					$TRX_ADDONS_STORAGE['capture_links'] .= "\n" . $matches[0];
				}
				return '';
			},
			$TRX_ADDONS_STORAGE['capture_body_html']
		);
		if ( ! empty( $TRX_ADDONS_STORAGE['capture_links'] ) ) {
			$TRX_ADDONS_STORAGE['capture_links_moved'] = false;
			$TRX_ADDONS_STORAGE['capture_head_html'] = preg_replace_callback(
				// Insert styles after the tag below
				'/<link [^>]*(href=[\'"][^\'"]*trx_addons[^\'"]*'
								. ( trx_addons_is_on( trx_addons_get_option( 'debug_mode' ) )
									? 'shortcodes.css'
									: '__styles.css'
									)
								. '[^\'"]*[\'"])[^>]*>/',
				function( $matches ) {
					if ( ! empty( $matches[0] ) ) {
						global $TRX_ADDONS_STORAGE;
						$TRX_ADDONS_STORAGE['capture_links_moved'] = true;
						return $matches[0] . $TRX_ADDONS_STORAGE['capture_links'];
					}
					return '';
				},
				$TRX_ADDONS_STORAGE['capture_head_html']
			);
			// If links present, but not moved yet - append its to the end of the head output
			if ( ! $TRX_ADDONS_STORAGE['capture_links_moved'] ) {
				$TRX_ADDONS_STORAGE['capture_head_html'] .= $TRX_ADDONS_STORAGE['capture_links'];
			}
		}
	}
}

// Move tag 'style' with inline styles of 'trx_addons' from the body to the end of the tag <head> (after all other styles)
if ( ! function_exists( 'trx_addons_move_own_inline_styles_to_head' ) ) {
	function trx_addons_move_own_inline_styles_to_head() {
		global $TRX_ADDONS_STORAGE;
		$TRX_ADDONS_STORAGE['capture_links'] = '';
		$TRX_ADDONS_STORAGE['capture_body_html'] = preg_replace_callback(
			'/<style[^<]*id=[\'"]trx_addons-inline-styles-inline-css[\'"][^<]*>[\s\S]*<\/style>/Uix',
			function( $matches ) {
				if ( ! empty( $matches[0] ) ) {
					global $TRX_ADDONS_STORAGE;
					$TRX_ADDONS_STORAGE['capture_links'] .= "\n" . $matches[0];
				}
				return '';
			},
			$TRX_ADDONS_STORAGE['capture_body_html']
		);
		// If inline styles are present - append its to the end of the head output
		if ( ! empty( $TRX_ADDONS_STORAGE['capture_links'] ) ) {
			$TRX_ADDONS_STORAGE['capture_head_html'] .= $TRX_ADDONS_STORAGE['capture_links'];
		}
	}
}

// Move tags 'link' from the theme and child-theme folders from the body to the head
if ( ! function_exists( 'trx_addons_move_theme_styles_to_head' ) ) {
	function trx_addons_move_theme_styles_to_head() {
		global $TRX_ADDONS_STORAGE;
		$TRX_ADDONS_STORAGE['capture_links'] = '';
		$theme_reg = str_replace( '/', '\\/', get_template() != get_stylesheet()
													? '(' . get_template() . '|' . get_stylesheet() . ')'
													: get_template()
								);
		$TRX_ADDONS_STORAGE['capture_body_html'] = preg_replace_callback(
			'/<link [^>]*(href=[\'"][^\'"]*' . $theme_reg . '[^\'"]*[\'"])[^>]*>/',
			function( $matches ) {
				if ( ! empty( $matches[0] ) ) {
					global $TRX_ADDONS_STORAGE;
					$TRX_ADDONS_STORAGE['capture_links'] .= "\n" . $matches[0];
				}
				return '';
			},
			$TRX_ADDONS_STORAGE['capture_body_html']
		);
		if ( ! empty( $TRX_ADDONS_STORAGE['capture_links'] ) ) {
			$TRX_ADDONS_STORAGE['capture_links_moved'] = false;
			$is_style = preg_match( '/<style [^>]*(id=[\'"][^\'"]*' . $theme_reg . '-custom-inline-css[^\'"]*[\'"])[^>]*>/Uix', $TRX_ADDONS_STORAGE['capture_head_html'] );
			$TRX_ADDONS_STORAGE['capture_head_html_base_tag'] = '';
			$TRX_ADDONS_STORAGE['capture_head_html'] = preg_replace_callback(
				// Insert styles after the tag below
				$is_style
					? '/<style [^>]*(id=[\'"][^\'"]*' . $theme_reg . '-custom-inline-css[^\'"]*[\'"])[^>]*>/Uix'
					: '/<link [^>]*(href=[\'"][^\'"]*' . $theme_reg . '[^\'"]*__custom\.css[^\'"]*[\'"])[^>]*>/',
				function( $matches ) {
					if ( ! empty( $matches[0] ) ) {
						global $TRX_ADDONS_STORAGE;
						$is_style = substr( $matches[0], 0, 7 ) == '<style ';
						if ( $is_style ) {
							$TRX_ADDONS_STORAGE['capture_head_html_base_tag'] = $matches[0];
						} else {
							$TRX_ADDONS_STORAGE['capture_links_moved'] = true;
						}
						return $matches[0]
									. ( $is_style
										? ''			// Styles are already inserted after the base tag <style>
										: $TRX_ADDONS_STORAGE['capture_links']	// Append styles after the base tag <link>
										);
					}
					return '';
				},
				$TRX_ADDONS_STORAGE['capture_head_html']
			);
			// Append styles after base tag <style>
			if ( $is_style && ! empty( $TRX_ADDONS_STORAGE['capture_head_html_base_tag'] ) ) {
				// Find a base tag <style>
				$pos = strpos( $TRX_ADDONS_STORAGE['capture_head_html'], $TRX_ADDONS_STORAGE['capture_head_html_base_tag'] );
				if ( $pos ) {
					// Find end of a base tag
					$pos = strpos( $TRX_ADDONS_STORAGE['capture_head_html'], '</style>', $pos );
					if ( $pos ) {
						// Insert styles after the end of a base tag
						$pos += 8;
						$TRX_ADDONS_STORAGE['capture_head_html'] = 
							substr( $TRX_ADDONS_STORAGE['capture_head_html'], 0, $pos )
							. $TRX_ADDONS_STORAGE['capture_links']
							. substr( $TRX_ADDONS_STORAGE['capture_head_html'], $pos );
						$TRX_ADDONS_STORAGE['capture_links_moved'] = true;
					}
				}
			}
			// If links are present, but not moved yet -  append its to the end of the head output
			if ( ! $TRX_ADDONS_STORAGE['capture_links_moved'] ) {
				$TRX_ADDONS_STORAGE['capture_head_html'] .= $TRX_ADDONS_STORAGE['capture_links'];
			}
		}
	}
}

// Move other styles and links to the head
if ( ! function_exists( 'trx_addons_move_other_styles_to_head' ) ) {
	function trx_addons_move_other_styles_to_head() {
		global $TRX_ADDONS_STORAGE;
		// A filter below is used instead an option
		// if ( trx_addons_is_on( trx_addons_get_option( 'move_styles_to_head' ) ) ) {
		if ( apply_filters( 'trx_addons_filter_move_styles_to_head', true ) ) {
			// Collect rest tags <link> and <style> from the body html
			$TRX_ADDONS_STORAGE['capture_links'] = '';
			$TRX_ADDONS_STORAGE['capture_body_html'] = preg_replace_callback(
				"/<style[^<]*>[\s\S]*<\/style>/Uix",
				function( $matches ) {
					if ( ! empty( $matches[0] ) ) {
						global $TRX_ADDONS_STORAGE;
						$TRX_ADDONS_STORAGE['capture_links'] .= "\n" . $matches[0];
					}
					return '';
				},
				$TRX_ADDONS_STORAGE['capture_body_html']
			);
			$TRX_ADDONS_STORAGE['capture_body_html'] = preg_replace_callback(
				"/<link [^>]*>/",
				function( $matches ) {
					if ( ! empty( $matches[0] ) ) {
						global $TRX_ADDONS_STORAGE;
						$TRX_ADDONS_STORAGE['capture_links'] .= "\n" . $matches[0];
					}
					return '';
				},
				$TRX_ADDONS_STORAGE['capture_body_html']
			);
			// Move rest styles before the theme styles
			if ( ! empty( $TRX_ADDONS_STORAGE['capture_links'] ) ) {
				$TRX_ADDONS_STORAGE['capture_links_moved'] = false;
				if ( apply_filters( 'trx_addons_filter_move_3rd_party_styles_before_theme_styles', true ) ) {
					$theme_reg = str_replace( '/', '\\/', get_template() );
					// Try to insert styles before the main theme tag
					$TRX_ADDONS_STORAGE['capture_head_html'] = preg_replace_callback(
						'/<link [^>]*(id=[\'"]' . $theme_reg . '-style-css[\'"])[^>]*>/',
						function( $matches ) {
							if ( ! empty( $matches[0] ) ) {
								global $TRX_ADDONS_STORAGE;
								$TRX_ADDONS_STORAGE['capture_links_moved'] = true;
								return $TRX_ADDONS_STORAGE['capture_links']	// Prepend styles before the base tag <link>
										. "\n" . $matches[0];
							}
							return '';
						},
						$TRX_ADDONS_STORAGE['capture_head_html']
					);
				}
				// If links are present, but not moved yet - append its to the end of the head output
				if ( ! $TRX_ADDONS_STORAGE['capture_links_moved'] ) {
					$TRX_ADDONS_STORAGE['capture_head_html'] .= $TRX_ADDONS_STORAGE['capture_links'];
				}
			}
		}
	}
}


// A last chance to add scripts and styles for blocks, used on this page
// Call before 'wp_print_footer_scripts' on priority 20
if ( ! function_exists( 'trx_addons_check_page_content_for_used_styles_and_scripts' ) ) {
	add_action( 'wp_footer', 'trx_addons_check_page_content_for_used_styles_and_scripts', 18 );
	function trx_addons_check_page_content_for_used_styles_and_scripts() {

		if ( ! trx_addons_grab_output_allowed() ) return;

		global $TRX_ADDONS_STORAGE;
		// Get (only preview, not finish capture) a captured body output
		if ( ! empty( $TRX_ADDONS_STORAGE['capture_page'] ) ) {
			$html = ob_get_contents();
			if ( ! empty( $html ) ) {
				do_action( 'trx_addons_action_check_page_content', $html );
			}
		}
	}
}


//-------------------------------------------------------
//-- Translations
//-------------------------------------------------------

// Load plugin's translation file
// Attention! It must be loaded before the first call of any translation function
if ( !function_exists( 'trx_addons_load_plugin_textdomain' ) ) {
	add_action( 'plugins_loaded', 'trx_addons_load_plugin_textdomain');
	function trx_addons_load_plugin_textdomain() {
		static $loaded = false;
		if ( $loaded ) return true;
		$domain = 'trx_addons';
		if ( is_textdomain_loaded( $domain ) && ! is_a( $GLOBALS['l10n'][ $domain ], 'NOOP_Translations' ) ) return true;
		$loaded = true;
		load_plugin_textdomain( $domain, false, TRX_ADDONS_PLUGIN_BASE . '/languages' );
	}
}


//-------------------------------------------------------
//-- Delayed action from previous session
//-- (after save options)
//-- to save new CSS, etc.
//-------------------------------------------------------
if ( !function_exists('trx_addons_do_delayed_action') ) {
	add_action( 'after_setup_theme', 'trx_addons_do_delayed_action' );
	function trx_addons_do_delayed_action() {
		// If a delayed action is present
		$action = get_option( 'trx_addons_action' );
		if ( ! empty( $action ) ) {
		    do_action( $action );
			update_option( 'trx_addons_action', '' );
		}
		// If the plugin was updated manually
		$version = get_option( 'trx_addons_version' );
		if ( $version != TRX_ADDONS_VERSION ) {
			// Regenerate combined CSS and JS if not do it on delayed action
			if ( $action != 'trx_addons_action_save_options' ) {
				do_action( 'trx_addons_action_save_options' );
			}
			// Trigger action for a new version
			do_action( 'trx_addons_action_is_new_version_of_plugin', TRX_ADDONS_VERSION, $version );
			// Save current version
			update_option( 'trx_addons_version', TRX_ADDONS_VERSION );
		}
	}
}


// Next files must be loaded before options
require_once TRX_ADDONS_PLUGIN_DIR_INCLUDES . 'plugin.socials.php';
require_once TRX_ADDONS_PLUGIN_DIR_INCLUDES . 'plugin.files.php';
require_once TRX_ADDONS_PLUGIN_DIR_INCLUDES . 'plugin.cache.php';
require_once TRX_ADDONS_PLUGIN_DIR_INCLUDES . 'plugin.uploads.php';

// Plugin's internal utilities
require_once TRX_ADDONS_PLUGIN_DIR_INCLUDES . 'plugin.debug.php';
require_once TRX_ADDONS_PLUGIN_DIR_INCLUDES . 'plugin.utils.php';
require_once TRX_ADDONS_PLUGIN_DIR_INCLUDES . 'plugin.messages.php';
require_once TRX_ADDONS_PLUGIN_DIR_INCLUDES . 'plugin.media.php';
require_once TRX_ADDONS_PLUGIN_DIR_INCLUDES . 'plugin.wp.php';
require_once TRX_ADDONS_PLUGIN_DIR_INCLUDES . 'plugin.lists.php';
require_once TRX_ADDONS_PLUGIN_DIR_INCLUDES . 'plugin.html.php';
require_once TRX_ADDONS_PLUGIN_DIR_INCLUDES . 'plugin.users.php';
require_once TRX_ADDONS_PLUGIN_DIR_INCLUDES . 'plugin.banners.php';
require_once TRX_ADDONS_PLUGIN_DIR_INCLUDES . 'plugin.compatibilities.php';

// Admin
require_once TRX_ADDONS_PLUGIN_DIR_INCLUDES . 'plugin.admin.php';

// Plugin's options
require_once TRX_ADDONS_PLUGIN_DIR_INCLUDES . 'plugin.options.php';
require_once TRX_ADDONS_PLUGIN_DIR_INCLUDES . 'plugin.options.components.php';
require_once TRX_ADDONS_PLUGIN_DIR_INCLUDES . 'plugin.options.meta-box.php';

// After WordPress update 5.8+ we need some functions not only in the admin mode
if ( true || is_admin() ) {
	require_once TRX_ADDONS_PLUGIN_DIR_INCLUDES . 'plugin.options.customizer.php';
}

// Pluggable modules
require_once TRX_ADDONS_PLUGIN_DIR_COMPONENTS . 'components.php';

// Theme-specific modules
require_once TRX_ADDONS_PLUGIN_DIR_ADDONS . 'addons.php';
