<?php
/**
 * Shortcode: Content container
 *
 * @package ThemeREX Addons
 * @since v1.2
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


// Load required styles and scripts for the frontend
if ( !function_exists( 'trx_addons_sc_content_load_scripts_front' ) ) {
	add_action( "wp_enqueue_scripts", 'trx_addons_sc_content_load_scripts_front', TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY );
	add_action( 'trx_addons_action_pagebuilder_preview_scripts', 'trx_addons_sc_content_load_scripts_front', 10, 1 );
	function trx_addons_sc_content_load_scripts_front( $force = false ) {
		trx_addons_enqueue_optimized( 'sc_content', $force, array(
			'css'  => array(
				'trx_addons-sc_content' => array( 'src' => TRX_ADDONS_PLUGIN_SHORTCODES . 'content/content.css' ),
			),
			'check' => array(
				array( 'type' => 'sc',  'sc' => 'trx_sc_content' ),
				array( 'type' => 'gb',  'sc' => 'wp:trx-addons/content' ),
				array( 'type' => 'elm', 'sc' => '"widgetType":"trx_sc_content"' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[trx_sc_content' ),
			)
		) );
	}
}

// Enqueue responsive styles for frontend
if ( ! function_exists( 'trx_addons_sc_content_load_scripts_front_responsive' ) ) {
	add_action( 'wp_enqueue_scripts', 'trx_addons_sc_content_load_scripts_front_responsive', TRX_ADDONS_ENQUEUE_RESPONSIVE_PRIORITY );
	add_action( 'trx_addons_action_load_scripts_front_sc_content', 'trx_addons_sc_content_load_scripts_front_responsive', 10, 1 );
	function trx_addons_sc_content_load_scripts_front_responsive( $force = false ) {
		trx_addons_enqueue_optimized_responsive( 'sc_content', $force, array(
			'css'  => array(
				'trx_addons-sc_content-responsive' => array(
					'src' => TRX_ADDONS_PLUGIN_SHORTCODES . 'content/content.responsive.css',
					'media' => 'xl'
				),
			),
		) );
	}
}

// Merge shortcode's specific styles to the single stylesheet
if ( !function_exists( 'trx_addons_sc_content_merge_styles' ) ) {
	add_filter("trx_addons_filter_merge_styles", 'trx_addons_sc_content_merge_styles');
	function trx_addons_sc_content_merge_styles($list) {
		$list[ TRX_ADDONS_PLUGIN_SHORTCODES . 'content/content.css' ] = false;
		return $list;
	}
}

// Merge shortcode's specific styles to the single stylesheet (responsive)
if ( !function_exists( 'trx_addons_sc_content_merge_styles_responsive' ) ) {
	add_filter("trx_addons_filter_merge_styles_responsive", 'trx_addons_sc_content_merge_styles_responsive');
	function trx_addons_sc_content_merge_styles_responsive($list) {
		$list[ TRX_ADDONS_PLUGIN_SHORTCODES . 'content/content.responsive.css' ] = false;
		return $list;
	}
}

// Load styles and scripts if present in the cache of the menu
if ( !function_exists( 'trx_addons_sc_content_check_in_html_output' ) ) {
	add_filter( 'trx_addons_filter_get_menu_cache_html', 'trx_addons_sc_content_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_show_layout_from_cache', 'trx_addons_sc_content_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_check_page_content', 'trx_addons_sc_content_check_in_html_output', 10, 1 );
	function trx_addons_sc_content_check_in_html_output( $content = '' ) {
		$args = array(
			'check' => array(
				'class=[\'"][^\'"]*sc_content'
			)
		);
		if ( trx_addons_check_in_html_output( 'sc_content', $content, $args ) ) {
			trx_addons_sc_content_load_scripts_front( true );
		}
		return $content;
	}
}


// trx_sc_content
//-------------------------------------------------------------
/*
[trx_sc_content id="unique_id" width="1/2"]
*/
if ( !function_exists( 'trx_addons_sc_content' ) ) {
	function trx_addons_sc_content($atts, $content=null){	
		$atts = trx_addons_sc_prepare_atts('trx_sc_content', $atts, trx_addons_sc_common_atts('id,title', array(
			// Individual params
			'type' => 'default',
			"width" => "",
			"size" => "none",
			"float" => 'center',
			"align" => "",
			"paddings" => "",
			"margins" => "",
			"push" => "",
			"push_hide_on_tablet" => 0,
			"push_hide_on_mobile" => 0,
			"pull" => "",
			"pull_hide_on_tablet" => 0,
			"pull_hide_on_mobile" => 0,
			"extra_bg" => "none",
			"extra_bg_mask" => 0,
			"shift_x" => "none",
			"shift_y" => "none",
			"number" => "",
			"number_position" => "br",
			"number_color" => "",
			))
		);

		if (empty($atts['width']) && !empty($atts['size'])) $atts['width'] = $atts['size'];
		if (empty($atts['width']) && !empty($atts['content_width'])) $atts['width'] = $atts['content_width'];
		
		$atts['content'] = do_shortcode($content);

		// Load shortcode-specific  scripts and styles
		trx_addons_sc_content_load_scripts_front( true );

		// Load template
		$output = '';
		
		if (!empty($atts['content']) || !empty($atts['title']) || !empty($atts['subtitle']) || !empty($atts['description'])) {

			ob_start();
			trx_addons_get_template_part(array(
											TRX_ADDONS_PLUGIN_SHORTCODES . 'content/tpl.'.trx_addons_esc($atts['type']).'.php',
											TRX_ADDONS_PLUGIN_SHORTCODES . 'content/tpl.default.php'
											),
                                            'trx_addons_args_sc_content', 
                                            $atts
                                        );
			$output = ob_get_contents();
			ob_end_clean();

		}
		
		return apply_filters('trx_addons_sc_output', $output, 'trx_sc_content', $atts, $content);
	}
}


// Add shortcode [trx_sc_content] and [trx_sc_content_inner]
if (!function_exists('trx_addons_sc_content_add_shortcode')) {
	function trx_addons_sc_content_add_shortcode() {
		add_shortcode("trx_sc_content", "trx_addons_sc_content");
		add_shortcode("trx_sc_content_inner", "trx_addons_sc_content");
	}
	add_action('init', 'trx_addons_sc_content_add_shortcode', 20);
}


// Add shortcodes
//----------------------------------------------------------------------------

// Add shortcodes to Gutenberg
if ( trx_addons_exists_gutenberg() && function_exists( 'trx_addons_gutenberg_get_param_id' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_SHORTCODES . 'content/content-sc-gutenberg.php';
}

// Add shortcodes to VC
if ( trx_addons_exists_vc() && function_exists( 'trx_addons_vc_add_id_param' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_SHORTCODES . 'content/content-sc-vc.php';
}
