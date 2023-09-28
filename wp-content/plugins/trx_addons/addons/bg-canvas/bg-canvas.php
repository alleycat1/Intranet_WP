<?php
/**
 * Dynamic background for Elementor's sections
 *
 * @addon bg-canvas
 * @version 1.3
 *
 * @package ThemeREX Addons
 * @since v1.84.0
 */


// Load required styles and scripts for the frontend
if ( ! function_exists( 'trx_addons_bg_canvas_load_scripts_front' ) ) {
	add_action( 'wp_enqueue_scripts', 'trx_addons_bg_canvas_load_scripts_front', TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY );
	add_action( 'trx_addons_action_pagebuilder_preview_scripts', 'trx_addons_bg_canvas_load_scripts_front', 10, 1 );
	function trx_addons_bg_canvas_load_scripts_front( $force = false ) {
		trx_addons_enqueue_optimized( 'bg_canvas', $force, array(
			'css'  => array(
				'trx_addons-bg-canvas' => array( 'src' => TRX_ADDONS_PLUGIN_ADDONS . 'bg-canvas/bg-canvas.css' ),
			),
			'js' => array(
				'trx_addons-bg-canvas' => array( 'src' => TRX_ADDONS_PLUGIN_ADDONS . 'bg-canvas/bg-canvas.js', 'deps' => 'jquery' ),
			)
		) );
	}
}

	
// Merge styles to the single stylesheet
if ( ! function_exists( 'trx_addons_bg_canvas_merge_styles' ) ) {
	add_filter("trx_addons_filter_merge_styles", 'trx_addons_bg_canvas_merge_styles');
	function trx_addons_bg_canvas_merge_styles($list) {
		$list[ TRX_ADDONS_PLUGIN_ADDONS . 'bg-canvas/bg-canvas.css' ] = false;
		return $list;
	}
}

	
// Merge specific scripts into single file
if ( ! function_exists( 'trx_addons_bg_canvas_merge_scripts' ) ) {
	add_action("trx_addons_filter_merge_scripts", 'trx_addons_bg_canvas_merge_scripts');
	function trx_addons_bg_canvas_merge_scripts($list) {
		$list[ TRX_ADDONS_PLUGIN_ADDONS . 'bg-canvas/bg-canvas.js' ] = false;
		return $list;
	}
}

// Load styles and scripts if present in the cache of the menu or layouts or finally in the whole page output
if ( ! function_exists( 'trx_addons_bg_canvas_check_in_html_output' ) ) {
//	add_filter( 'trx_addons_filter_get_menu_cache_html', 'trx_addons_audio_effects_check_in_html_output', 10, 1 );
//	add_action( 'trx_addons_action_show_layout_from_cache', 'trx_addons_audio_effects_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_check_page_content', 'trx_addons_bg_canvas_check_in_html_output', 10, 1 );
	function trx_addons_bg_canvas_check_in_html_output( $content = '' ) {
		$args = array(
			'check' => array(
				'data-bg-canvas-'
			)
		);
		if ( trx_addons_check_in_html_output( 'bg_canvas', $content, $args ) ) {
			trx_addons_bg_canvas_load_scripts_front( true );
		}
		return $content;
	}
}

// Add "Bg Canvas" params to all elements
if ( ! function_exists( 'trx_addons_elm_add_params_bg_canvas' ) ) {
	add_action( 'elementor/element/before_section_start', 'trx_addons_elm_add_params_bg_canvas', 10, 3 );
	add_action( 'elementor/widget/before_section_start', 'trx_addons_elm_add_params_bg_canvas', 10, 3 );
	function trx_addons_elm_add_params_bg_canvas($element, $section_id, $args) {

		if ( !is_object($element) ) return;

		if ( $element->get_name() == 'common' && $section_id == '_section_responsive' ) {
			
			$element->start_controls_section( 'section_trx_bg_canvas', array(
																		'tab' => !empty($args['tab']) ? $args['tab'] : \Elementor\Controls_Manager::TAB_ADVANCED,
																		'label' => __( 'Dynamic Background', 'trx_addons' )
																	) );
			$element->add_control( 'bg_canvas_type', array(
				'type' => \Elementor\Controls_Manager::SELECT,
				'label' => __( 'Breakpoint type', 'trx_addons' ),
				'label_block' => false,
				'options' => apply_filters( 'trx_addons_filter_bg_canvas_effects', array(
					'none' => esc_html__( 'None', 'trx_addons' ),
					'start' => esc_html__( 'Start', 'trx_addons' ),
					'end'  => esc_html__( 'End', 'trx_addons' ),
				) ),
				'default' => 'none',
			) );
			$element->add_control( 'bg_canvas_id', array(
				'type' => \Elementor\Controls_Manager::TEXT,
				'label' => __( 'Breakpoint ID', 'trx_addons' ),
				'label_block' => false,
				'condition' => array(
					'bg_canvas_type!' => 'none'
				),
			) );
			$element->add_control( 'bg_canvas_effect', array(
				'type' => \Elementor\Controls_Manager::SELECT,
				'label' => __( 'Background effect', 'trx_addons' ),
				'label_block' => false,
				'options' => apply_filters( 'trx_addons_filter_bg_canvas_effects', array(
					'round' => esc_html__( 'Round', 'trx_addons' ),
					'fade'  => esc_html__( 'Fade', 'trx_addons' ),
				) ),
				'default' => 'round',
				'condition' => array(
					'bg_canvas_type!' => 'none'
				),
			) );
			$element->add_control( 'bg_canvas_color', array(
				'label' => __( 'Background color', 'trx_addons' ),
				'label_block' => false,
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '',
				// Not used, because global colors are not compatible with fade
				'global' => array(
					'active' => false,
				),
				'condition' => array(
					'bg_canvas_type!' => 'none',
				),
			) );
			$element->add_control( 'bg_canvas_size', array(
				'label' => __( 'Min.size', 'trx_addons' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'default' => array(
					'size' => '',
					'unit' => 'px'
				),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 300
					),
				),
				'size_units' => array( 'px' ),
				'condition' => array(
					'bg_canvas_type!' => 'none',
					'bg_canvas_effect' => 'round',
				),
			) );
			$element->add_control( 'bg_canvas_shift', array(
				'label' => __( 'Shift', 'trx_addons' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'default' => array(
					'size' => '',
					'unit' => 'px'
				),
				'range' => array(
					'px' => array(
						'min' => -200,
						'max' => 200
					),
				),
				'size_units' => array( 'px' ),
				'condition' => array(
					'bg_canvas_type!' => 'none'
				),
			) );

			$element->end_controls_section();
		}
	}
}

// Add "data-bg-canvas" to the wrapper of the row
if ( !function_exists( 'trx_addons_elm_add_bg_canvas_data' ) ) {
	// Before Elementor 2.1.0
	add_action( 'elementor/frontend/element/before_render',  'trx_addons_elm_add_bg_canvas_data', 10, 1 );
	// After Elementor 2.1.0
	add_action( 'elementor/frontend/widget/before_render', 'trx_addons_elm_add_bg_canvas_data', 10, 1 );
	function trx_addons_elm_add_bg_canvas_data($element) {
		if ( is_object($element) ) {
			//$settings = trx_addons_elm_prepare_global_params( $element->get_settings() );
			$bg_canvas_type = $element->get_settings( 'bg_canvas_type' );
			if ( ! empty( $bg_canvas_type ) && ! trx_addons_is_off( $bg_canvas_type ) ) {
				// Load scripts and styles
				trx_addons_bg_canvas_load_scripts_front( true );
				// Add data-parameters to the element wrapper
				$settings = $element->get_settings();
				$element->add_render_attribute( '_wrapper', array(
					'data-bg-canvas-id'     => $settings['bg_canvas_id'],
					'data-bg-canvas-type'   => $settings['bg_canvas_type'],
					'data-bg-canvas-effect' => $settings['bg_canvas_effect'],
					'data-bg-canvas-size'   => $settings['bg_canvas_size']['size'],
					'data-bg-canvas-shift'  => $settings['bg_canvas_shift']['size'],
					'data-bg-canvas-color'  => $settings['bg_canvas_color']
				) );
			}
		}
	}
}
