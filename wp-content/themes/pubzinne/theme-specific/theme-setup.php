<?php
/**
 * Setup theme-specific fonts and colors
 *
 * @package PUBZINNE
 * @since PUBZINNE 1.0.22
 */

// If this theme is a free version of premium theme
if ( ! defined( 'PUBZINNE_THEME_FREE' ) ) {
	define( 'PUBZINNE_THEME_FREE', false );
}
if ( ! defined( 'PUBZINNE_THEME_FREE_WP' ) ) {
	define( 'PUBZINNE_THEME_FREE_WP', false );
}

// If this theme support external updates
if ( ! defined( 'PUBZINNE_THEME_ALLOW_UPDATE' ) ) {
	define( 'PUBZINNE_THEME_ALLOW_UPDATE', true );
}

// If this theme uses skins
if ( ! defined( 'PUBZINNE_ALLOW_SKINS' ) ) {
	define( 'PUBZINNE_ALLOW_SKINS', true );
}
if ( ! defined( 'PUBZINNE_DEFAULT_SKIN' ) ) {
	define( 'PUBZINNE_DEFAULT_SKIN', 'default' );
}
if ( ! defined( 'PUBZINNE_REMEMBER_SKIN' ) ) {
	define( 'PUBZINNE_REMEMBER_SKIN', false );
}



// Theme storage
// Attention! Must be in the global namespace to compatibility with WP CLI
//-------------------------------------------------------------------------
$GLOBALS['PUBZINNE_STORAGE'] = array(

	// Theme-specific URLs (will be escaped in place of the output)
	'theme_upgrade_url'   => '//upgrade.themerex.net/',

	// Generate Personal token from Envato to automatic upgrade theme
	'upgrade_token_url'   => '//build.envato.com/create-token/?default=t&purchase:download=t&purchase:list=t',

	// Responsive resolutions
	// Parameters to create css media query: min, max
	'responsive'          => array(
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
		'tablet'     => array( 'min' =>  768, 'max' => 1279 ),
		'not_mobile' => array( 'min' =>  768 ),
		'mobile'     => array(                'max' =>  767 ),
	),
);


//------------------------------------------------------------------------
// One-click import support
//------------------------------------------------------------------------

// Set theme specific importer options
if ( ! function_exists( 'pubzinne_importer_set_options' ) ) {
	add_filter( 'trx_addons_filter_importer_options', 'pubzinne_importer_set_options', 9 );
	function pubzinne_importer_set_options( $options = array() ) {
		if ( is_array( $options ) ) {
			// Save or not installer's messages to the log-file
			$options['debug'] = false;
			// Allow import/export functionality
			$options['allow_import'] = true;
			$options['allow_export'] = false;
			// Prepare demo data
			$options['demo_url'] = esc_url( pubzinne_get_protocol() . ':' . pubzinne_storage_get( 'theme_demofiles_url' ) );
			// Required plugins
			$options['required_plugins'] = array_keys( pubzinne_storage_get( 'required_plugins' ) );
			// Set number of thumbnails (usually 3 - 5) to regenerate at once when its imported (if demo data was zipped without cropped images)
			// Set 0 to prevent regenerate thumbnails (if demo data archive is already contain cropped images)
			$options['regenerate_thumbnails'] = 0;
			// The array with theme-specific banners, displayed during demo-content import.
			// If array with banners is empty - the banners are uploaded directly from demo-content server.
			$options['banners'] = array();
		}
		return $options;
	}
}


//------------------------------------------------------------------------
// OCDI support
//------------------------------------------------------------------------

// Set theme specific OCDI options
if ( ! function_exists( 'pubzinne_ocdi_set_options' ) ) {
	add_filter( 'trx_addons_filter_ocdi_options', 'pubzinne_ocdi_set_options', 9 );
	function pubzinne_ocdi_set_options( $options = array() ) {
		if ( is_array( $options ) ) {
			// Prepare demo data
			$options['demo_url'] = esc_url( pubzinne_get_protocol() . ':' . pubzinne_storage_get( 'theme_demofiles_url' ) );
			// Required plugins
			$options['required_plugins'] = array_keys( pubzinne_storage_get( 'required_plugins' ) );
		}
		return $options;
	}
}


// Theme init priorities:
// Action 'after_setup_theme'
// 1 - register filters to add/remove lists items in the Theme Options
// 2 - create Theme Options
// 3 - add/remove Theme Options elements
// 5 - load Theme Options. Attention! After this step you can use only basic options (not overriden)
// 9 - register other filters (for installer, etc.)
//10 - standard Theme init procedures (not ordered)
// Action 'wp_loaded'
// 1 - detect override mode. Attention! Only after this step you can use overriden options (separate values for the shop, courses, etc.)

if ( ! function_exists( 'pubzinne_customizer_theme_setup1' ) ) {
	add_action( 'after_setup_theme', 'pubzinne_customizer_theme_setup1', 1 );
	function pubzinne_customizer_theme_setup1() {

		// -----------------------------------------------------------------
		// -- ONLY FOR PROGRAMMERS, NOT FOR CUSTOMER
		// -- Internal theme settings
		// -----------------------------------------------------------------
		pubzinne_storage_set(
			'settings', array(

				'duplicate_options'         => 'child',                 // none  - use separate options for the main and the child-theme
																		// child - duplicate theme options from the main theme to the child-theme only
																		// both  - sinchronize changes in the theme options between main and child themes

				'save_only_changed_options' => true,                    // Send to the server only changed fields or all fields in the form when saving Theme Options

				'options_tabs_position'     => 'vertical',              // Position of tabs in the Theme and ThemeREX Addons options

				'allow_subtabs'             => true,				    // Display sections as subtabs of panels in the Theme Options.
																		// If false - show sections as accordion.

				'customize_refresh'         => 'auto',                  // Refresh method for preview area in the Appearance - Customize:
																		// auto - refresh preview area on change each field with Theme Options
																		// manual - refresh only obn press button 'Refresh' at the top of Customize frame

				'max_load_fonts'            => 5,                       // Max fonts number to load from Google fonts or from uploaded fonts

				'override_option_single'    => 'post',					// Who can override option with value specified for the single post:
																		// 'all'  - any CPT
																		// 'post' - only 'post' type

				'comment_after_name'        => true,                    // Place 'comment' field after the 'name' and 'email'

				'icons_selector'            => 'internal',              // Icons selector in the shortcodes:
																		// vc (default) - standard VC (very slow) or Elementor's icons selector (not support images and svg)
																		// internal - internal popup with plugin's or theme's icons list (fast and support images and svg)

				'icons_type'                => 'icons',                 // Type of icons (if 'icons_selector' is 'internal'):
																		// icons  - use font icons to present icons
																		// images - use images from theme's folder trx_addons/css/icons.png
																		// svg    - use svg from theme's folder trx_addons/css/icons.svg

				'socials_type'              => 'icons',                 // Type of socials icons (if 'icons_selector' is 'internal'):
																		// icons  - use font icons to present social networks
																		// images - use images from theme's folder trx_addons/css/icons.png
																		// svg    - use svg from theme's folder trx_addons/css/icons.svg

				'check_min_version'         => true,                    // Check if exists a .min version of .css and .js and return path to it
																		// instead the path to the original file
																		// (if debug_mode is on and modification time of the original file < time of the .min file)

				'autoselect_menu'           => false,                   // Show any menu if no menu selected in the location 'main_menu'
																		// (for example, the theme is just activated)

				'wrap_menu_items_with_span' => true,                    // Wrap menu items with span (need for some menu hovers and language menu with flags)

				'remove_empty_menu_items'   => true,                    // Remove empty menu items (with no titles)

				'disable_jquery_ui'         => false,                   // Prevent loading custom jQuery UI libraries in the third-party plugins

				'use_mediaelements'         => true,                    // Load script "Media Elements" to play video and audio

				'tgmpa_upload'              => false,                   // Allow upload not pre-packaged plugins via TGMPA

				'allow_no_image'            => false,                   // Allow to use theme-specific image placeholder if no image present in the blog, related posts, post navigation, etc.

				'separate_schemes'          => true,                    // Save color schemes to the separate files __color_xxx.css (true) or append its to the __custom.css (false)

				'allow_fullscreen'          => false,                   // Allow cases 'fullscreen' and 'fullwide' for the body style in the Theme Options
																		// In the Page Options this styles are present always
																		// (can be removed if filter 'pubzinne_filter_allow_fullscreen' return false)

				'attachments_navigation'    => false,                   // Add arrows on the single attachment page to navigate to the prev/next attachment

				'thumbs_in_navigation'      => false,                   // Add thumbs to arrows on the single page to navigate to the prev/next post

				'gutenberg_safe_mode'       => array(),                 // 'vc', 'elementor' - Prevent simultaneous editing of posts for Gutenberg and other PageBuilders (VC, Elementor)

				'gutenberg_add_context'     => false,                   // Add context to the Gutenberg editor styles with our method (if true - use if any problem with editor styles) or use native Gutenberg way via add_editor_style() (if false - used by default)

				'modify_gutenberg_blocks'   => true,                    // Modify core blocks - add our parameters and classes

				'allow_gutenberg_blocks'    => true,                    // Allow our shortcodes and widgets as blocks in the Gutenberg (not ready yet - in the development now)

				'subtitle_above_title'      => true,                    // Put subtitle above the title in the shortcodes

				'add_hide_on_xxx'           => 'replace',               // Add our breakpoints to the Responsive section of each element
																		// 'add' - add our breakpoints after Elementor's
																		// 'replace' - add our breakpoints instead Elementor's
																		// 'none' - don't add our breakpoints (using only Elementor's)

				'fixed_blocks_sticky'       => false,                   // true  - CSS rules to fix sidebar and columns are used (ignores fixed rows in the header, not supported by IE)
																		// false - JS-script to fix sidebar and columns is used (consider the fixed rows in the header)

				'blog_filters_use_ajax'     => true,				    // Load posts on tabs select via ajax 

				'banners_show_effect'		=> false,					// Use 'slideDown' to show hidden banners
			)
		);
	}
}
