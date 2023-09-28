<?php
/**
 * Shortcode: Price block
 *
 * @package ThemeREX Addons
 * @since v1.2
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


// Load required styles and scripts for the frontend
if ( !function_exists( 'trx_addons_sc_price_load_scripts_front' ) ) {
	add_action( "wp_enqueue_scripts", 'trx_addons_sc_price_load_scripts_front', TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY );
	add_action( 'trx_addons_action_pagebuilder_preview_scripts', 'trx_addons_sc_price_load_scripts_front', 10, 1 );
	function trx_addons_sc_price_load_scripts_front( $force = false ) {
		trx_addons_enqueue_optimized( 'sc_price', $force, array(
			'css'  => array(
				'trx_addons-sc_price' => array( 'src' => TRX_ADDONS_PLUGIN_SHORTCODES . 'price/price.css' ),
			),
			'check' => array(
				array( 'type' => 'sc',  'sc' => 'trx_sc_price' ),
				array( 'type' => 'gb',  'sc' => 'wp:trx-addons/price' ),
				array( 'type' => 'elm', 'sc' => '"widgetType":"trx_sc_price"' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[trx_sc_price' ),
			)
		) );
	}
}

// Enqueue responsive styles for frontend
if ( ! function_exists( 'trx_addons_sc_price_load_scripts_front_responsive' ) ) {
	add_action( 'wp_enqueue_scripts', 'trx_addons_sc_price_load_scripts_front_responsive', TRX_ADDONS_ENQUEUE_RESPONSIVE_PRIORITY );
	add_action( 'trx_addons_action_load_scripts_front_sc_price', 'trx_addons_sc_price_load_scripts_front_responsive', 10, 1 );
	function trx_addons_sc_price_load_scripts_front_responsive( $force = false ) {
		trx_addons_enqueue_optimized_responsive( 'sc_price', $force, array(
			'css'  => array(
				'trx_addons-sc_price-responsive' => array(
					'src' => TRX_ADDONS_PLUGIN_SHORTCODES . 'price/price.responsive.css',
					'media' => 'md'
				),
			),
		) );
	}
}
	
// Merge shortcode's specific styles into single stylesheet
if ( !function_exists( 'trx_addons_sc_price_merge_styles' ) ) {
	add_filter("trx_addons_filter_merge_styles", 'trx_addons_sc_price_merge_styles');
	function trx_addons_sc_price_merge_styles($list) {
		$list[ TRX_ADDONS_PLUGIN_SHORTCODES . 'price/price.css' ] = false;
		return $list;
	}
}

// Merge shortcode's specific styles to the single stylesheet (responsive)
if ( !function_exists( 'trx_addons_sc_price_merge_styles_responsive' ) ) {
	add_filter("trx_addons_filter_merge_styles_responsive", 'trx_addons_sc_price_merge_styles_responsive');
	function trx_addons_sc_price_merge_styles_responsive($list) {
		$list[ TRX_ADDONS_PLUGIN_SHORTCODES . 'price/price.responsive.css' ] = false;
		return $list;
	}
}

// Load styles and scripts if present in the cache of the menu
if ( !function_exists( 'trx_addons_sc_price_check_in_html_output' ) ) {
//	add_filter( 'trx_addons_filter_get_menu_cache_html', 'trx_addons_sc_price_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_show_layout_from_cache', 'trx_addons_sc_price_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_check_page_content', 'trx_addons_sc_price_check_in_html_output', 10, 1 );
	function trx_addons_sc_price_check_in_html_output( $content = '' ) {
		$args = array(
			'check' => array(
				'class=[\'"][^\'"]*sc_price'
			)
		);
		if ( trx_addons_check_in_html_output( 'sc_price', $content, $args ) ) {
			trx_addons_sc_price_load_scripts_front( true );
		}
		return $content;
	}
}



// trx_sc_price
//-------------------------------------------------------------
/*
[trx_sc_price id="unique_id" title="Our plan" link="#" link_text="Buy now"]Description[/trx_sc_price]
*/
if ( !function_exists( 'trx_addons_sc_price' ) ) {
	function trx_addons_sc_price($atts, $content=null){	
		$atts = trx_addons_sc_prepare_atts('trx_sc_price', $atts, trx_addons_sc_common_atts('id,title,slider', array(
			// Individual params
			"type" => 'default',
			"columns" => "",
			"columns_tablet" => "",
			"columns_mobile" => "",
			"prices" => "",
			))
		);

		if (function_exists('vc_param_group_parse_atts') && !is_array($atts['prices'])) {
			$atts['prices'] = (array) vc_param_group_parse_atts( $atts['prices'] );
		}

		$output = '';
		if (is_array($atts['prices']) && count($atts['prices']) > 0) {
			if (empty($atts['columns'])) $atts['columns'] = count($atts['prices']);
			$atts['columns'] = max(1, min(count($atts['prices']), $atts['columns']));
			if (!empty($atts['columns_tablet'])) $atts['columns_tablet'] = max(1, min(count($atts['prices']), (int) $atts['columns_tablet']));
			if (!empty($atts['columns_mobile'])) $atts['columns_mobile'] = max(1, min(count($atts['prices']), (int) $atts['columns_mobile']));
			$atts['slider'] = $atts['slider'] > 0 && count($atts['prices']) > $atts['columns'];
			$atts['slides_space'] = max(0, (int) $atts['slides_space']);
			if ($atts['slider'] > 0 && (int) $atts['slider_pagination'] > 0) $atts['slider_pagination'] = 'bottom';
	
			foreach ($atts['prices'] as $k=>$v) {
				if (!empty($v['description'])) 
					$atts['prices'][$k]['description'] = preg_replace( '/\\[(.*)\\]/', '<b>$1</b>', function_exists('vc_value_from_safe') ? vc_value_from_safe( $v['description'] ) : $v['description'] );
			}
	
			// Load shortcode-specific scripts and styles
			trx_addons_sc_price_load_scripts_front( true );

			// Load template
			ob_start();
			trx_addons_get_template_part(array(
											TRX_ADDONS_PLUGIN_SHORTCODES . 'price/tpl.'.trx_addons_esc($atts['type']).'.php',
											TRX_ADDONS_PLUGIN_SHORTCODES . 'price/tpl.default.php'
											),
											'trx_addons_args_sc_price',
											$atts
										);
			$output = ob_get_contents();
			ob_end_clean();
		}
		return apply_filters('trx_addons_sc_output', $output, 'trx_sc_price', $atts, $content);
	}
}


// Add shortcode [trx_sc_price]
if (!function_exists('trx_addons_sc_price_add_shortcode')) {
	function trx_addons_sc_price_add_shortcode() {
		add_shortcode("trx_sc_price", "trx_addons_sc_price");
	}
	add_action('init', 'trx_addons_sc_price_add_shortcode', 20);
}



// Add shortcodes
//----------------------------------------------------------------------------

// Add shortcodes to Elementor
if ( trx_addons_exists_elementor() && function_exists('trx_addons_elm_init') ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_SHORTCODES . 'price/price-sc-elementor.php';
}

// Add shortcodes to Gutenberg
if ( trx_addons_exists_gutenberg() && function_exists( 'trx_addons_gutenberg_get_param_id' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_SHORTCODES . 'price/price-sc-gutenberg.php';
}

// Add shortcodes to VC
if ( trx_addons_exists_vc() && function_exists( 'trx_addons_vc_add_id_param' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_SHORTCODES . 'price/price-sc-vc.php';
}
