<?php
/**
 * Shortcode: Blogger
 *
 * @package ThemeREX Addons
 * @since v1.2
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


// Load required styles and scripts for the frontend
if ( ! function_exists( 'trx_addons_sc_blogger_load_scripts_front' ) ) {
	add_action( "wp_enqueue_scripts", 'trx_addons_sc_blogger_load_scripts_front', TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY );
	add_action( 'trx_addons_action_pagebuilder_preview_scripts', 'trx_addons_sc_blogger_load_scripts_front', 10, 1 );
	function trx_addons_sc_blogger_load_scripts_front( $force = false ) {
		trx_addons_enqueue_optimized( 'sc_blogger', $force, array(
			'css'  => array(
				'trx_addons-sc_blogger' => array( 'src' => TRX_ADDONS_PLUGIN_SHORTCODES . 'blogger/blogger.css' ),
			),
			'check' => array(
				array( 'type' => 'sc',  'sc' => 'trx_sc_blogger' ),
				array( 'type' => 'gb',  'sc' => 'wp:trx-addons/blogger' ),
				array( 'type' => 'elm', 'sc' => '"widgetType":"trx_sc_blogger"' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[trx_sc_blogger' ),
			)
		) );
	}
}

// Enqueue responsive styles for frontend
if ( ! function_exists( 'trx_addons_sc_blogger_load_scripts_front_responsive' ) ) {
	add_action( 'wp_enqueue_scripts', 'trx_addons_sc_blogger_load_scripts_front_responsive', TRX_ADDONS_ENQUEUE_RESPONSIVE_PRIORITY );
	add_action( 'trx_addons_action_load_scripts_front_sc_blogger', 'trx_addons_sc_blogger_load_scripts_front_responsive', 10, 1 );
	function trx_addons_sc_blogger_load_scripts_front_responsive( $force = false ) {
		trx_addons_enqueue_optimized_responsive( 'sc_blogger', $force, array(
			'css'  => array(
				'trx_addons-sc_blogger-responsive' => array(
					'src' => TRX_ADDONS_PLUGIN_SHORTCODES . 'blogger/blogger.responsive.css',
					'media' => 'lg'
				),
			),
		) );
	}
}

// Merge shortcode's specific styles to the single stylesheet
if ( ! function_exists( 'trx_addons_sc_blogger_merge_styles' ) ) {
	add_filter("trx_addons_filter_merge_styles", 'trx_addons_sc_blogger_merge_styles');
	function trx_addons_sc_blogger_merge_styles($list) {
		$list[ TRX_ADDONS_PLUGIN_SHORTCODES . 'blogger/blogger.css' ] = false;
		return $list;
	}
}

// Merge shortcode's specific styles to the single stylesheet (responsive)
if ( ! function_exists( 'trx_addons_sc_blogger_merge_styles_responsive' ) ) {
	add_filter("trx_addons_filter_merge_styles_responsive", 'trx_addons_sc_blogger_merge_styles_responsive');
	function trx_addons_sc_blogger_merge_styles_responsive($list) {
		$list[ TRX_ADDONS_PLUGIN_SHORTCODES . 'blogger/blogger.responsive.css' ] = false;
		return $list;
	}
}

// Load styles and scripts if present in the cache of the menu
if ( !function_exists( 'trx_addons_sc_blogger_check_in_html_output' ) ) {
	add_filter( 'trx_addons_filter_get_menu_cache_html', 'trx_addons_sc_blogger_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_show_layout_from_cache', 'trx_addons_sc_blogger_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_check_page_content', 'trx_addons_sc_blogger_check_in_html_output', 10, 1 );
	function trx_addons_sc_blogger_check_in_html_output( $content = '' ) {
		$args = array(
			'check' => array(
				'class=[\'"][^\'"]*sc_blogger'
			)
		);
		if ( trx_addons_check_in_html_output( 'sc_blogger', $content, $args ) ) {
			trx_addons_sc_blogger_load_scripts_front( true );
		}
		return $content;
	}
}


// Load masonry scripts if current mode is Preview in the PageBuilder
if ( ! function_exists( 'trx_addons_sc_blogger_load_masonry_scripts' ) ) {
	add_action("trx_addons_action_pagebuilder_preview_scripts", 'trx_addons_sc_blogger_load_masonry_scripts', 10, 1);
	function trx_addons_sc_blogger_load_masonry_scripts($editor='') {
		trx_addons_enqueue_masonry();
	}
}


// Removes terms count in the string
if ( ! function_exists( 'trx_addons_sc_blogger_remove_terms_counter' ) ) {
	function trx_addons_sc_blogger_remove_terms_counter($str, $replacement = '' ) {
		return preg_replace( array( '/\(\d+\)$/', '/^\-/' ), $replacement, $str);
	}
}


// trx_sc_blogger
//-------------------------------------------------------------
/*
[trx_sc_blogger id="unique_id" type="default" cat="category_slug or id" count="3" columns="3" slider="0|1"]
*/
if ( ! function_exists( 'trx_addons_sc_blogger' ) ) {
	function trx_addons_sc_blogger($atts, $content=null) {	
		// Exit to prevent recursion
		if ( trx_addons_sc_stack_check( 'trx_sc_blogger' ) ) return '';
		$defa = trx_addons_sc_common_atts( 'id,title,slider,query', array(
			// Individual params
			"type" => 'default',
			// Query posts
			'post_type' => 'post',
			'taxonomy' => 'category',
			// Filters
			"show_filters" => 0,
			"filters_title" => '',
			"filters_subtitle" => '',
			"filters_title_align" => 'left',
			"filters_tabs_position" => 'top',
			"filters_tabs_on_hover" => 0,
			"filters_taxonomy" => 'category',
			"filters_active" => '',
			"filters_ids" => '',
			"filters_all" => '1',
			"filters_all_text" => '',
			"filters_more_text" => esc_html__('More posts', 'trx_addons'),
			// Post meta
			"meta_parts" => array(),
			// Output options
			"image_position" => 'top',
			"image_width" => '40',
			"image_ratio" => 'none',
			"thumb_size" => '',
			"hover" => '',
			"date_format" => '',
			"on_plate" => 0,
			"hide_excerpt" => 0,
			"excerpt_length" => '',
			"text_align" => 'left',
			"no_margin" => 0,
			"no_links" => 0,
			"full_post" => 0,
			"video_in_popup" => 0,
			"numbers" => 0,
			"more_button" => 1,
			"more_text" => esc_html__('Read more', 'trx_addons'),
			"pagination" => "none",
			"page" => 1,
			'posts_exclude' => '',	// comma-separated list of IDs to exclude from output
			// Gutenberg specific
			"align" => 'none'
			)
		);
		// 3-rd param - need extra queries to get custom blogger styles
		$layouts = apply_filters( 'trx_addons_sc_type', trx_addons_components_get_allowed_layouts('sc', 'blogger'), 'trx_sc_blogger', ! empty( $atts['type'] ) && strpos( $atts['type'], 'blog-custom-' ) === 0 );
		if ( is_array( $layouts ) ) {
			$templates = trx_addons_components_get_allowed_templates('sc', 'blogger', $layouts);
			foreach( $layouts as $k => $v ) {
				$defa["template_{$k}"] = isset($templates[$k]) ? trx_addons_array_get_first($templates[$k]) : '';
			}
		}

		$atts = trx_addons_sc_prepare_atts( 'trx_sc_blogger', $atts, $defa );

		if ( ! empty($atts['ids']) ) {
			if ( is_array( $atts['ids'] ) ) {
				$atts['ids'] = join(',', $atts['ids']);
			}
			$atts['ids'] = str_replace(array(';', ' '), array(',', ''), $atts['ids']);
			$ids_count = count( explode( ',', $atts['ids'] ) );
			if ( empty( $atts['count'] ) || $atts['count'] >= $ids_count || empty( $atts['pagination'] ) || trx_addons_is_off( $atts['pagination'] ) ) {
				$atts['count'] = $ids_count;
			}
		}
		if ( ! is_array($atts['filters_ids']) ) {
			$atts['filters_ids'] = trim($atts['filters_ids']);
			$atts['filters_ids'] = empty($atts['filters_ids']) ? array() : explode(',', $atts['filters_ids']);
		}

		$atts['count'] = $atts['count'] < 0 ? -1 : max(1, (int) $atts['count']);
		$atts['offset'] = max(0, (int) $atts['offset']);
		if (empty($atts['orderby'])) $atts['orderby'] = 'date';
		if (empty($atts['order'])) $atts['order'] = 'desc';
		$atts['slider'] = max(0, (int) $atts['slider']);
		if ($atts['slider'] > 0 && (int) $atts['slider_pagination'] > 0) $atts['slider_pagination'] = 'bottom';
		if ($atts['slider'] > 0) $atts['pagination'] = 'none';

		$atts['excerpt_length'] = !empty($atts['excerpt_length']) ? max(1, $atts['excerpt_length']) : '';

		if ( (int) $atts['more_button'] == 0 ) $atts['more_text'] = '';

		// Load shortcode-specific scripts and styles
		trx_addons_sc_blogger_load_scripts_front( true );

		// Load template
		ob_start();
		trx_addons_get_template_part(
			array(
				TRX_ADDONS_PLUGIN_SHORTCODES . 'blogger/tpl.'.trx_addons_esc($atts['type']).'.php',
				TRX_ADDONS_PLUGIN_SHORTCODES . 'blogger/tpl.default.php'
				),
			'trx_addons_args_sc_blogger', 
			$atts
		);
		$output = ob_get_contents();
		ob_end_clean();

		return apply_filters('trx_addons_sc_output', $output, 'trx_sc_blogger', $atts, $content);
	}
}


// Add shortcode [trx_sc_blogger]
if ( ! function_exists( 'trx_addons_sc_blogger_add_shortcode' ) ) {
	add_action( 'init', 'trx_addons_sc_blogger_add_shortcode', 20 );
	function trx_addons_sc_blogger_add_shortcode() {
		add_shortcode( 'trx_sc_blogger', 'trx_addons_sc_blogger' );
	}
}


// Add shortcodes
//----------------------------------------------------------------------------

// Add shortcodes to Elementor
if ( trx_addons_exists_elementor() && function_exists('trx_addons_elm_init') ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_SHORTCODES . 'blogger/blogger-sc-elementor.php';
}

// Add shortcodes to Gutenberg
if ( trx_addons_exists_gutenberg() && function_exists( 'trx_addons_gutenberg_get_param_id' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_SHORTCODES . 'blogger/blogger-sc-gutenberg.php';
}

// Add shortcodes to VC
if ( trx_addons_exists_vc() && function_exists( 'trx_addons_vc_add_id_param' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_SHORTCODES . 'blogger/blogger-sc-vc.php';
}

// Create our widget
require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_SHORTCODES . 'blogger/blogger-widget.php';
