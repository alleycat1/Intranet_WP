<?php
/**
 * Reference colors for sections by which the background of the page will change when scrolling
 *
 * @addon bg-colors
 * @version 1.1
 *
 * @package ThemeREX Addons
 * @since v2.8.0
 */


// Load required styles and scripts for the frontend
if ( ! function_exists( 'trx_addons_bg_colors_load_scripts_front' ) ) {
	add_action( 'wp_enqueue_scripts', 'trx_addons_bg_colors_load_scripts_front', TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY );
	add_action( 'trx_addons_action_pagebuilder_preview_scripts', 'trx_addons_bg_colors_load_scripts_front', 10, 1 );
	function trx_addons_bg_colors_load_scripts_front( $force = false ) {
		trx_addons_enqueue_optimized( 'bg_colors', $force, array(
			'css'  => array(
				'trx_addons-bg-colors' => array( 'src' => TRX_ADDONS_PLUGIN_ADDONS . 'bg-colors/bg-colors.css' ),
			),
			'js' => array(
				'trx_addons-bg-colors' => array( 'src' => TRX_ADDONS_PLUGIN_ADDONS . 'bg-colors/bg-colors.js', 'deps' => 'jquery' ),
			)
		) );
	}
}

	
// Merge styles to the single stylesheet
if ( ! function_exists( 'trx_addons_bg_colors_merge_styles' ) ) {
	add_filter("trx_addons_filter_merge_styles", 'trx_addons_bg_colors_merge_styles');
	function trx_addons_bg_colors_merge_styles($list) {
		$list[ TRX_ADDONS_PLUGIN_ADDONS . 'bg-colors/bg-colors.css' ] = false;
		return $list;
	}
}

	
// Merge specific scripts into single file
if ( ! function_exists( 'trx_addons_bg_colors_merge_scripts' ) ) {
	add_action("trx_addons_filter_merge_scripts", 'trx_addons_bg_colors_merge_scripts');
	function trx_addons_bg_colors_merge_scripts($list) {
		$list[ TRX_ADDONS_PLUGIN_ADDONS . 'bg-colors/bg-colors.js' ] = false;
		return $list;
	}
}

// Load styles and scripts if present in the cache of the menu or layouts or finally in the whole page output
if ( ! function_exists( 'trx_addons_bg_colors_check_in_html_output' ) ) {
//	add_filter( 'trx_addons_filter_get_menu_cache_html', 'trx_addons_audio_effects_check_in_html_output', 10, 1 );
//	add_action( 'trx_addons_action_show_layout_from_cache', 'trx_addons_audio_effects_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_check_page_content', 'trx_addons_bg_colors_check_in_html_output', 10, 1 );
	function trx_addons_bg_colors_check_in_html_output( $content = '' ) {
		$args = array(
			'check' => array(
				'data-trx-bg-colors'
			)
		);
		if ( trx_addons_check_in_html_output( 'bg_colors', $content, $args ) ) {
			trx_addons_bg_colors_load_scripts_front( true );
		}
		return $content;
	}
}

// Add a default selector for apply bg colors to the list with JS vars
if ( !function_exists( 'trx_addons_bg_colors_localize_script' ) ) {
	add_action("trx_addons_filter_localize_script", 'trx_addons_bg_colors_localize_script');
	function trx_addons_bg_colors_localize_script( $vars ) {
		$vars['bg_colors_selector'] = trx_addons_get_page_background_selector();
		return $vars;
	}
}


// Add "Bg Colors" params to all elements
if ( ! function_exists( 'trx_addons_elm_add_params_bg_colors' ) ) {
	add_action( 'elementor/element/before_section_start', 'trx_addons_elm_add_params_bg_colors', 10, 3 );
	add_action( 'elementor/widget/before_section_start', 'trx_addons_elm_add_params_bg_colors', 10, 3 );
	function trx_addons_elm_add_params_bg_colors($element, $section_id, $args) {

		if ( ! is_object( $element ) ) return;

		if ( $element->get_name() == 'common' && $section_id == '_section_responsive' ) {
			
			$element->start_controls_section( 'section_trx_bg_colors', array(
																		'tab' => !empty($args['tab']) ? $args['tab'] : \Elementor\Controls_Manager::TAB_ADVANCED,
																		'label' => __( 'Background Key Color', 'trx_addons' )
																	) );
			$element->add_control( 'trx_bg_colors_color', array(
				'label' => __( 'Background key color', 'trx_addons' ),
				'label_block' => false,
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '',
				// Not used, because global colors are not compatible with fade
				'global' => array(
					'active' => false,
				),
			) );
			$element->add_control( 'trx_bg_colors_selector', array(
				'type' => \Elementor\Controls_Manager::TEXT,
				'label' => __( 'Target Selector', 'trx_addons' ),
				'label_block' => false,
				'description' => __( 'Specify an object selector from the current page to which the background color will be applied. If empty, the background will be changed for the entire page.', 'trx_addons' ),
				'condition' => array(
					'trx_bg_colors_color!' => ''
				),
			) );

			$element->end_controls_section();
		}
	}
}

// Add "data-trx-bg-colors" to the wrapper of the row
if ( !function_exists( 'trx_addons_elm_add_bg_colors_data' ) ) {
	// Before Elementor 2.1.0
	add_action( 'elementor/frontend/element/before_render',  'trx_addons_elm_add_bg_colors_data', 10, 1 );
	// After Elementor 2.1.0
	add_action( 'elementor/frontend/widget/before_render', 'trx_addons_elm_add_bg_colors_data', 10, 1 );
	function trx_addons_elm_add_bg_colors_data($element) {
		if ( is_object( $element ) ) {
			//$settings = trx_addons_elm_prepare_global_params( $element->get_settings() );
			$bg_color = $element->get_settings( 'trx_bg_colors_color' );
			if ( ! empty( $bg_color ) ) {
				// Load scripts and styles
				trx_addons_bg_colors_load_scripts_front( true );
				// Add data-parameters to the element wrapper
				$bg_color_selector = $element->get_settings( 'trx_bg_colors_selector' );
				$element->add_render_attribute( '_wrapper', array(
					'data-trx-bg-colors-color'    => $bg_color,
					'data-trx-bg-colors-selector' => $bg_color_selector
				) );
			}
		}
	}
}
