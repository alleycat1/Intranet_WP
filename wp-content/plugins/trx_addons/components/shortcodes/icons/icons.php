<?php
/**
 * Shortcode: Icons
 *
 * @package ThemeREX Addons
 * @since v1.2
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


// Load required styles and scripts for the frontend
if ( !function_exists( 'trx_addons_sc_icons_load_scripts_front' ) ) {
	add_action( "wp_enqueue_scripts", 'trx_addons_sc_icons_load_scripts_front', TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY );
	add_action( 'trx_addons_action_pagebuilder_preview_scripts', 'trx_addons_sc_icons_load_scripts_front', 10, 1 );
	function trx_addons_sc_icons_load_scripts_front( $force = false ) {
		trx_addons_enqueue_optimized( 'sc_icons', $force, array(
			'css'  => array(
				'trx_addons-sc_icons' => array( 'src' => TRX_ADDONS_PLUGIN_SHORTCODES . 'icons/icons.css' ),
			),
			'check' => array(
				array( 'type' => 'sc',  'sc' => 'trx_sc_icons' ),
				array( 'type' => 'gb',  'sc' => 'wp:trx-addons/icons' ),
				array( 'type' => 'elm', 'sc' => '"widgetType":"trx_sc_icons"' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[trx_sc_icons' ),
)
		) );
	}
}

// Enqueue responsive styles for frontend
if ( ! function_exists( 'trx_addons_sc_icons_load_scripts_front_responsive' ) ) {
	add_action( 'wp_enqueue_scripts', 'trx_addons_sc_icons_load_scripts_front_responsive', TRX_ADDONS_ENQUEUE_RESPONSIVE_PRIORITY );
	add_action( 'trx_addons_action_load_scripts_front_sc_icons', 'trx_addons_sc_icons_load_scripts_front_responsive', 10, 1 );
	function trx_addons_sc_icons_load_scripts_front_responsive( $force = false ) {
		trx_addons_enqueue_optimized_responsive( 'sc_icons', $force, array(
			'css'  => array(
				'trx_addons-sc_icons-responsive' => array(
					'src' => TRX_ADDONS_PLUGIN_SHORTCODES . 'icons/icons.responsive.css',
					'media' => 'lg'
				),
			),
		) );
	}
}
	
// Merge shortcode's specific styles to the single stylesheet
if ( !function_exists( 'trx_addons_sc_icons_merge_styles' ) ) {
	add_filter("trx_addons_filter_merge_styles", 'trx_addons_sc_icons_merge_styles');
	function trx_addons_sc_icons_merge_styles($list) {
		$list[ TRX_ADDONS_PLUGIN_SHORTCODES . 'icons/icons.css' ] = false;
		return $list;
	}
}


// Merge shortcode's specific styles to the single stylesheet (responsive)
if ( !function_exists( 'trx_addons_sc_icons_merge_styles_responsive' ) ) {
	add_filter("trx_addons_filter_merge_styles_responsive", 'trx_addons_sc_icons_merge_styles_responsive');
	function trx_addons_sc_icons_merge_styles_responsive($list) {
		$list[ TRX_ADDONS_PLUGIN_SHORTCODES . 'icons/icons.responsive.css' ] = false;
		return $list;
	}
}

// Load styles and scripts if present in the cache of the menu
if ( !function_exists( 'trx_addons_sc_icons_check_in_html_output' ) ) {
	add_filter( 'trx_addons_filter_get_menu_cache_html', 'trx_addons_sc_icons_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_show_layout_from_cache', 'trx_addons_sc_icons_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_check_page_content', 'trx_addons_sc_icons_check_in_html_output', 10, 1 );
	function trx_addons_sc_icons_check_in_html_output( $content = '' ) {
		$args = array(
			'check' => array(
				'class=[\'"][^\'"]*sc_icons'
			)
		);
		if ( trx_addons_check_in_html_output( 'sc_icons', $content, $args ) ) {
			trx_addons_sc_icons_load_scripts_front( true );
		}
		return $content;
	}
}

// Load scripts for SVG animation if present in the cache of the menu
if ( !function_exists( 'trx_addons_sc_icons_animation_check_in_html_output' ) ) {
	add_filter( 'trx_addons_filter_get_menu_cache_html', 'trx_addons_sc_icons_animation_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_show_layout_from_cache', 'trx_addons_sc_icons_animation_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_check_page_content', 'trx_addons_sc_icons_animation_check_in_html_output', 10, 1 );
	function trx_addons_sc_icons_animation_check_in_html_output( $content = '' ) {
		$args = array(
			'check' => array(
				'class=[\'"][^\'"]*sc_icon_type_svg[^\'"]*sc_icon_animation',
				'class=[\'"][^\'"]*sc_icon_animation[^\'"]*sc_icon_type_svg'
			)
		);
		if ( trx_addons_check_in_html_output( 'sc_icons_animation', $content, $args ) ) {
			wp_enqueue_script( 'vivus', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_SHORTCODES . 'icons/vivus.js'), array('jquery'), null, true );
			wp_enqueue_script( 'trx_addons-sc_icons', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_SHORTCODES . 'icons/icons.js'), array('jquery'), null, true );
		}
		return $content;
	}
}


// Set script variables
if ( !function_exists( 'trx_addons_sc_icons_localize_script' ) ) {
	add_filter("trx_addons_filter_localize_script", 'trx_addons_sc_icons_localize_script');
	function trx_addons_sc_icons_localize_script($vars) {
		$vars['sc_icons_animation_speed'] = 50;
		return $vars;
	}
}


// trx_sc_icons
//-------------------------------------------------------------
/*
[trx_sc_icons id="unique_id" columns="2" values="encoded_json_data"]
*/
if ( !function_exists( 'trx_addons_sc_icons' ) ) {
	function trx_addons_sc_icons($atts, $content=null) {	
		$atts = trx_addons_sc_prepare_atts('trx_sc_icons', $atts, trx_addons_sc_common_atts('id,title,slider', array(
			// Individual params
			"type" => "default",
			"align" => "center",
			"size" => "medium",
			"color" => "",
			"item_title_color" => "",
			"item_text_color" => "",
			"columns" => "",
			"columns_tablet" => "",
			"columns_mobile" => "",
			"icons" => "",
			"icons_animation" => "0",
			))
		);
		if (function_exists('vc_param_group_parse_atts') && !is_array($atts['icons'])) {
			$atts['icons'] = (array) vc_param_group_parse_atts( $atts['icons'] );
		}

		$output = '';
		if ( is_array($atts['icons']) && count($atts['icons']) > 0 ) {
			if (empty($atts['columns'])) $atts['columns'] = count($atts['icons']);
			$atts['columns'] = max(1, min(count($atts['icons']), $atts['columns']));
			if (!empty($atts['columns_tablet'])) $atts['columns_tablet'] = max(1, min(count($atts['icons']), (int) $atts['columns_tablet']));
			if (!empty($atts['columns_mobile'])) $atts['columns_mobile'] = max(1, min(count($atts['icons']), (int) $atts['columns_mobile']));
			$atts['slider'] = $atts['slider'] > 0 && count($atts['icons']) > $atts['columns'];
			$atts['slides_space'] = max(0, (int) $atts['slides_space']);
			if ($atts['slider'] > 0 && (int) $atts['slider_pagination'] > 0) $atts['slider_pagination'] = 'bottom';
	
			foreach ($atts['icons'] as $k=>$v) {
				if (!empty($v['description']))
					$atts['icons'][$k]['description'] = preg_replace( '/\\[(.*)\\]/', '<b>$1</b>', function_exists('vc_value_from_safe') ? vc_value_from_safe( $v['description'] ) : $v['description'] );
				if ( ( empty( $v['icon'] ) || $v['icon'] == 'none' ) && ! empty( $v['svg']['url'] ) ) {
					$atts['icons'][$k]['icon_type'] = 'svg';
					$atts['icons'][$k]['icon_svg'] = $v['svg']['url'];
				}
			}

			// Load shortcode-specific scripts and styles
			trx_addons_sc_icons_load_scripts_front( true );

			// Load template
			ob_start();
			trx_addons_get_template_part(array(
											TRX_ADDONS_PLUGIN_SHORTCODES . 'icons/tpl.'.trx_addons_esc($atts['type']).'.php',
											TRX_ADDONS_PLUGIN_SHORTCODES . 'icons/tpl.default.php'
											),
											'trx_addons_args_sc_icons', 
											$atts
										);
			$output = ob_get_contents();
			ob_end_clean();
		}
		return apply_filters('trx_addons_sc_output', $output, 'trx_sc_icons', $atts, $content);
	}
}


// Add shortcode [trx_sc_icons]
if (!function_exists('trx_addons_sc_icons_add_shortcode')) {
	function trx_addons_sc_icons_add_shortcode() {
		add_shortcode("trx_sc_icons", "trx_addons_sc_icons");
	}
	add_action('init', 'trx_addons_sc_icons_add_shortcode', 20);
}


// Add shortcodes
//----------------------------------------------------------------------------

// Add shortcodes to Elementor
if ( trx_addons_exists_elementor() && function_exists('trx_addons_elm_init') ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_SHORTCODES . 'icons/icons-sc-elementor.php';
}

// Add shortcodes to Gutenberg
if ( trx_addons_exists_gutenberg() && function_exists( 'trx_addons_gutenberg_get_param_id' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_SHORTCODES . 'icons/icons-sc-gutenberg.php';
}

// Add shortcodes to VC
if ( trx_addons_exists_vc() && function_exists( 'trx_addons_vc_add_id_param' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_SHORTCODES . 'icons/icons-sc-vc.php';
}
