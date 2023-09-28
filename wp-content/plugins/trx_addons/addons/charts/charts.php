<?php
/**
 * Charts, based on chart.js library
 *
 * @addon charts
 * @version 1.1
 *
 * @package ThemeREX Addons
 * @since v2.8.0
 */

if ( ! defined( 'TRX_ADDONS_CHARTS_DATASETS_TOTAL' ) ) define( 'TRX_ADDONS_CHARTS_DATASETS_TOTAL', 3 );

// Load required styles and scripts for the frontend
if ( ! function_exists( 'trx_addons_charts_load_scripts_front' ) ) {
	add_action( 'wp_enqueue_scripts', 'trx_addons_charts_load_scripts_front', TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY );
	add_action( 'trx_addons_action_pagebuilder_preview_scripts', 'trx_addons_charts_load_scripts_front', 10, 1 );
	function trx_addons_charts_load_scripts_front( $force = false ) {
		trx_addons_enqueue_optimized( 'sc_charts', $force, array(
			'lib' => array(
				'js' => array(
					'chart' => array( 'src' => TRX_ADDONS_PLUGIN_ADDONS . 'charts/chart/chart.min.js' ),
				)
			),
			'css'  => array(
				'trx_addons-sc_charts' => array( 'src' => TRX_ADDONS_PLUGIN_ADDONS . 'charts/charts.css' ),
			),
			'js' => array(
				'trx_addons-sc_charts' => array( 'src' => TRX_ADDONS_PLUGIN_ADDONS . 'charts/charts.js', 'deps' => 'jquery' ),
			),
			'check' => array(
				array( 'type' => 'sc',  'sc' => 'trx_sc_charts' ),
				array( 'type' => 'gb',  'sc' => 'wp:trx-addons/charts' ),
				array( 'type' => 'elm', 'sc' => '"widgetType":"trx_sc_charts"' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[trx_sc_charts' ),
			)
		) );
	}
}


// Merge styles to the single stylesheet
if ( ! function_exists( 'trx_addons_charts_merge_styles' ) ) {
	add_filter("trx_addons_filter_merge_styles", 'trx_addons_charts_merge_styles');
	function trx_addons_charts_merge_styles($list) {
		$list[ TRX_ADDONS_PLUGIN_ADDONS . 'charts/charts.css' ] = false;
		return $list;
	}
}

	
// Merge specific scripts to the single file
if ( !function_exists( 'trx_addons_charts_merge_scripts' ) ) {
	add_action("trx_addons_filter_merge_scripts", 'trx_addons_charts_merge_scripts');
	function trx_addons_charts_merge_scripts($list) {
		$list[ TRX_ADDONS_PLUGIN_ADDONS . 'charts/charts.js' ] = false;
		return $list;
	}
}

// Load styles and scripts if present in the cache of the menu or layouts or finally in the whole page output
if ( !function_exists( 'trx_addons_charts_check_in_html_output' ) ) {
//	add_filter( 'trx_addons_filter_get_menu_cache_html', 'trx_addons_charts_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_show_layout_from_cache', 'trx_addons_charts_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_check_page_content', 'trx_addons_charts_check_in_html_output', 10, 1 );
	function trx_addons_charts_check_in_html_output( $content = '' ) {
		$args = array(
			'check' => array(
				'class=[\'"][^\'"]*sc_charts'
			)
		);
		if ( trx_addons_check_in_html_output( 'charts', $content, $args ) ) {
			trx_addons_charts_load_scripts_front( true );
		}
		return $content;
	}
}

// Add a datasets total to the list with JS vars
if ( !function_exists( 'trx_addons_charts_localize_script' ) ) {
	add_action("trx_addons_filter_localize_script", 'trx_addons_charts_localize_script');
	function trx_addons_charts_localize_script( $vars ) {
		$vars['charts_datasets_total'] = apply_filters( 'trx_addons_filter_charts_datasets_total', TRX_ADDONS_CHARTS_DATASETS_TOTAL );
		return $vars;
	}
}

// Return list of chart types
if ( ! function_exists( 'trx_addons_charts_list_types' ) ) {
	function trx_addons_charts_list_types() {
		return apply_filters( 'trx_addons_filter_charts_types', array(
										'line'      => esc_html__( 'Line', 'trx_addons' ),
										'bar'       => esc_html__( 'Bar', 'trx_addons' ),
										'radar'     => esc_html__( 'Radar', 'trx_addons' ),
										'pie'       => esc_html__( 'Pie', 'trx_addons' ),
										'polarArea' => esc_html__( 'Polar Area', 'trx_addons' ),
									) );
	}
}

// Return list of chart legend positions
if ( ! function_exists( 'trx_addons_charts_list_legend_positions' ) ) {
	function trx_addons_charts_list_legend_positions() {
		return apply_filters( 'trx_addons_filter_charts_legend_positions', array(
										'none'   => esc_html__( 'Hide', 'trx_addons' ),
										'top'    => esc_html__( 'Top', 'trx_addons' ),
										'bottom' => esc_html__( 'Bottom', 'trx_addons' ),
										'left'   => esc_html__( 'Left', 'trx_addons' ),
										'right'  => esc_html__( 'Right', 'trx_addons' ),
									) );
	}
}

// Return list of chart point styles
if ( ! function_exists( 'trx_addons_charts_list_point_styles' ) ) {
	function trx_addons_charts_list_point_styles() {
		return apply_filters( 'trx_addons_filter_charts_point_styles', array(
										'circle'      => esc_html__( 'Circle', 'trx_addons' ),
										'cross'       => esc_html__( 'Cross', 'trx_addons' ),
										'crossRot'    => esc_html__( 'Cross Rotated', 'trx_addons' ),
										'dash'        => esc_html__( 'Dash', 'trx_addons' ),
										'line'        => esc_html__( 'Line', 'trx_addons' ),
										'rect'        => esc_html__( 'Rectangle', 'trx_addons' ),
										'rectRounded' => esc_html__( 'Rectangle Rounded', 'trx_addons' ),
										'rectRot'     => esc_html__( 'Rectangle Rotated', 'trx_addons' ),
										'star'        => esc_html__( 'Asterisk', 'trx_addons' ),
										'triangle'    => esc_html__( 'Triangle', 'trx_addons' ),
									) );
	}
}

// Return list of chart border join styles
if ( ! function_exists( 'trx_addons_charts_list_border_join_styles' ) ) {
	function trx_addons_charts_list_border_join_styles() {
		return apply_filters( 'trx_addons_filter_charts_border_join_styles', array(
										'round' => esc_html__( 'Round', 'trx_addons' ),
										'bevel' => esc_html__( 'Bevel', 'trx_addons' ),
										'miter' => esc_html__( 'Miter', 'trx_addons' ),
									) );
	}
}


// trx_sc_charts
//-------------------------------------------------------------
/*
[trx_sc_charts id="unique_id" type="pie" cutout="99" values="encoded json data"]
*/
if ( ! function_exists( 'trx_addons_sc_charts' ) ) {
	function trx_addons_sc_charts( $atts, $content = null ){	
		$defa = trx_addons_sc_common_atts( 'id,title', array(
			// Individual params
			"type" => "line",
			"legend" => 'top',
			"from_zero" => false,
			"cutout" => 0,
			"hover_offset" => 4,
		) );

		$total = apply_filters( 'trx_addons_filter_charts_datasets_total', TRX_ADDONS_CHARTS_DATASETS_TOTAL );

		for ( $i = 1; $i <= $total; $i++ ) {
			$defa["dataset{$i}"] = "";
			$defa["dataset{$i}_enable"] = $i == 1 ? true : false;
			$defa["dataset{$i}_title"] = "";
			$defa["dataset{$i}_bg_color"] = "";
			$defa["dataset{$i}_border_color"] = "";
			$defa["dataset{$i}_border_width"] = 1;
			$defa["dataset{$i}_border_join"] = "miter";
			$defa["dataset{$i}_point_size"] = 3;
			$defa["dataset{$i}_point_style"] = "circle";
			$defa["dataset{$i}_fill"] = false;
			$defa["dataset{$i}_tension"] = 0;
		}

		$atts = trx_addons_sc_prepare_atts( 'trx_sc_charts', $atts, $defa );

		for ( $i = 1; $i <= $total; $i++ ) {
			if ( function_exists( 'vc_param_group_parse_atts' ) && ! is_array( $atts["dataset{$i}"] ) ) {
				$atts["dataset{$i}"] = (array) vc_param_group_parse_atts( $atts["dataset{$i}"] );
			}
		}

		$output = '';

		if ( is_array( $atts["dataset1"] ) && count( $atts["dataset1"] ) > 0) {

			if ( trx_addons_is_on( trx_addons_get_option( 'debug_mode' ) ) ) {
				wp_enqueue_script( 'trx_addons-sc_charts', trx_addons_get_file_url( TRX_ADDONS_PLUGIN_ADDONS . 'charts/charts.js'), array( 'jquery' ), null, true );
			}
	
			// Load shortcode-specific scripts and styles
			trx_addons_charts_load_scripts_front( true );

			// Load template
			ob_start();
			trx_addons_get_template_part( array(
											TRX_ADDONS_PLUGIN_ADDONS . 'charts/tpl.' . trx_addons_esc( $atts['type'] ) . '.php',
											TRX_ADDONS_PLUGIN_ADDONS . 'charts/tpl.charts.php'
											),
											'trx_addons_args_sc_charts', 
											$atts
										);
			$output = ob_get_contents();
			ob_end_clean();
		}
		return apply_filters('trx_addons_sc_output', $output, 'trx_sc_charts', $atts, $content);
	}
}


// Add shortcode [trx_sc_charts]
if (!function_exists('trx_addons_sc_charts_add_shortcode')) {
	function trx_addons_sc_charts_add_shortcode() {
		add_shortcode("trx_sc_charts", "trx_addons_sc_charts");
	}
	add_action('init', 'trx_addons_sc_charts_add_shortcode', 20);
}


// Add shortcodes
//----------------------------------------------------------------------------

// Add shortcodes to Elementor
if ( trx_addons_exists_elementor() && function_exists('trx_addons_elm_init') ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_ADDONS . 'charts/charts-sc-elementor.php';
}

// Add shortcodes to Gutenberg
if ( trx_addons_exists_gutenberg() && function_exists( 'trx_addons_gutenberg_get_param_id' ) ) {
//	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_ADDONS . 'charts/charts-sc-gutenberg.php';
}

// Add shortcodes to VC
if ( trx_addons_exists_vc() && function_exists( 'trx_addons_vc_add_id_param' ) ) {
//	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_ADDONS . 'charts/charts-sc-vc.php';
}
