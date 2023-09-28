<?php
/**
 * Background slideshow for Elementor's sections
 *
 * @addon bg-slides
 * @version 1.1
 *
 * @package ThemeREX Addons
 * @since v1.95.2
 */


// Load required styles and scripts for the frontend
if ( ! function_exists( 'trx_addons_bg_slides_load_scripts_front' ) ) {
	add_action( 'wp_enqueue_scripts', 'trx_addons_bg_slides_load_scripts_front', TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY );
	add_action( 'trx_addons_action_pagebuilder_preview_scripts', 'trx_addons_bg_slides_load_scripts_front', 10, 1 );
	function trx_addons_bg_slides_load_scripts_front( $force = false ) {
		trx_addons_enqueue_optimized( 'sc_charts', $force, array(
			'lib' => array(
				'js' => array(
					'modernizr' => array( 'src' => TRX_ADDONS_PLUGIN_ADDONS . 'bg-slides/modernizr.min.js', 'deps' => 'jquery', 'footer' => false ),
				)
			),
			'css'  => array(
				'trx_addons-bg-slides' => array( 'src' => TRX_ADDONS_PLUGIN_ADDONS . 'bg-slides/bg-slides.css' ),
			),
			'js' => array(
				'trx_addons-bg-slides' => array( 'src' => TRX_ADDONS_PLUGIN_ADDONS . 'bg-slides/bg-slides.js', 'deps' => 'jquery' ),
			),
		) );
	}
}

	
// Merge styles to the single stylesheet
if ( ! function_exists( 'trx_addons_bg_slides_merge_styles' ) ) {
	add_filter("trx_addons_filter_merge_styles", 'trx_addons_bg_slides_merge_styles');
	function trx_addons_bg_slides_merge_styles($list) {
		$list[ TRX_ADDONS_PLUGIN_ADDONS . 'bg-slides/bg-slides.css' ] = false;
		return $list;
	}
}

	
// Merge specific scripts into single file
if ( ! function_exists( 'trx_addons_bg_slides_merge_scripts' ) ) {
	add_action("trx_addons_filter_merge_scripts", 'trx_addons_bg_slides_merge_scripts');
	function trx_addons_bg_slides_merge_scripts($list) {
		$list[ TRX_ADDONS_PLUGIN_ADDONS . 'bg-slides/bg-slides.js' ] = false;
		return $list;
	}
}

// Load styles and scripts if present in the cache of the menu or layouts or finally in the whole page output
if ( !function_exists( 'trx_addons_bg_slides_check_in_html_output' ) ) {
//	add_filter( 'trx_addons_filter_get_menu_cache_html', 'trx_addons_bg_slides_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_show_layout_from_cache', 'trx_addons_bg_slides_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_check_page_content', 'trx_addons_bg_slides_check_in_html_output', 10, 1 );
	function trx_addons_bg_slides_check_in_html_output( $content = '' ) {
		$args = array(
			'check' => array(
				'data-trx-addons-bg-slides'
			)
		);
		if ( trx_addons_check_in_html_output( 'bg_slides', $content, $args ) ) {
			trx_addons_bg_slides_load_scripts_front( true );
		}
		return $content;
	}
}

// Add a default mask image to the list with JS vars
if ( !function_exists( 'trx_addons_bg_slides_localize_script' ) ) {
	add_action("trx_addons_filter_localize_script", 'trx_addons_bg_slides_localize_script');
	function trx_addons_bg_slides_localize_script( $vars ) {
		$vars['bg_slides_mask_svg'] = trx_addons_get_svg_from_file( trx_addons_get_file_dir( TRX_ADDONS_PLUGIN_ADDONS . 'bg-slides/images/mask-round.svg' ) );
		return $vars;
	}
}

// Add "Bg Slides" params to all elements
if ( ! function_exists( 'trx_addons_elm_add_params_bg_slides' ) ) {
	add_action( 'elementor/element/before_section_start', 'trx_addons_elm_add_params_bg_slides', 10, 3 );
	function trx_addons_elm_add_params_bg_slides($element, $section_id, $args) {

		if ( !is_object($element) ) return;

		if ( in_array( $element->get_name(), array( 'section' ) ) && $section_id == 'section_border' ) {	//_section_responsive

			$tab = ! empty( $args['tab'] ) ? $args['tab'] : \Elementor\Controls_Manager::TAB_STYLE;

			// Register controls
			$element->start_controls_section( 'section_trx_bg_slides', array(
				'tab' => $tab,
				'label' => __( 'Background Slideshow & Mask', 'trx_addons' )
			) );

			$element->add_control( 'bg_slides_allow', array(
				'label' => __( 'Allow slides', 'trx_addons' ),
				'label_on' => __( 'On', 'trx_addons' ),
				'label_off' => __( 'Off', 'trx_addons' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => '1'
			) );

			$element->add_control( 'bg_slides_overlay_color', array(
				'label' => __( 'Overlay color', 'trx_addons' ),
				'label_block' => false,
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '',
				// Not used, because global colors are not transparent
				'global' => array(
					'active' => false,
				),
				'selectors' => array(
					'{{WRAPPER}} .trx_addons_bg_slides_overlay' => 'background-color: {{VALUE}};',
				),
				'condition' => array(
					'bg_slides_allow' => '1'
				)
			) );

			$element->add_control( 'bg_slides_animation_duration', array(
				'label' => __( 'Animation duration', 'trx_addons' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'default' => array(
					'size' => 6.5,
					'unit' => 'px'
				),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 20,
						'step' => 0.1
					),
				),
				'size_units' => array( 'px' ),
				'condition' => array(
					'bg_slides_allow' => '1'
				),
			) );

			$repeater = new \Elementor\Repeater();

			$repeater->add_control( 'slide', array(
				'label' => __( 'Image', 'trx_addons' ),
				'type' => \Elementor\Controls_Manager::MEDIA,
				'tab' => $tab,
				'default' => array(
					'url' => '',
				),
			) );

			$repeater->add_control( 'slide_size', array(
				'label' => __( 'Slide size', 'trx_addons' ),
				'label_block' => false,
				'type' => \Elementor\Controls_Manager::SELECT,
				'tab' => $tab,
				'options' => apply_filters( 'trx_addons_filter_bg_slides_sizes', array(
					'cover'   => esc_html__( 'Cover', 'trx_addons' ),
					'contain' => esc_html__( 'Contain', 'trx_addons' ),
					'fill'    => esc_html__( 'Fill', 'trx_addons' ),
				) ),
				'default' => 'cover',
			) );

			$repeater->add_control( 'slide_effect', array(
				'label' => __( 'Slide effect', 'trx_addons' ),
				'label_block' => false,
				'type' => \Elementor\Controls_Manager::SELECT,
				'tab' => $tab,
				'options' => apply_filters( 'trx_addons_filter_bg_slides_effects', array(
					'none'         => esc_html__( 'None', 'trx_addons' ),
					'fade'         => esc_html__( 'Fade', 'trx_addons' ),
					'zoom_in'      => esc_html__( 'Zoom In', 'trx_addons' ),
					'zoom_out'     => esc_html__( 'Zoom Out', 'trx_addons' ),
					'infinite_in'  => esc_html__( 'Infinite In', 'trx_addons' ),
					'infinite_out' => esc_html__( 'Infinite Out', 'trx_addons' ),
				) ),
				'default' => 'zoom_in'
			) );

			$repeater->add_control( 'slide_origin', array(
				'label' => __( 'Slide origin', 'trx_addons' ),
				'label_block' => false,
				'type' => \Elementor\Controls_Manager::SELECT,
				'tab' => $tab,
				'options' => apply_filters( 'trx_addons_filter_bg_slides_origins', array(
					'lt' => esc_html__( 'Top Left', 'trx_addons' ),
					'ct' => esc_html__( 'Top Center', 'trx_addons' ),
					'rt' => esc_html__( 'Top Right', 'trx_addons' ),
					'lc' => esc_html__( 'Middle Left', 'trx_addons' ),
					'cc' => esc_html__( 'Middle Center', 'trx_addons' ),
					'rc' => esc_html__( 'Middle Right', 'trx_addons' ),
					'lb' => esc_html__( 'Bottom Left', 'trx_addons' ),
					'cb' => esc_html__( 'Bottom Center', 'trx_addons' ),
					'rb' => esc_html__( 'Bottom Right', 'trx_addons' ),
				) ),
				'default' => 'cc',
				'condition' => array(
					'slide_effect!' => ['none','fade']
				)
			) );

			$element->add_control( 'bg_slides', array(
				'type' => \Elementor\Controls_Manager::REPEATER,
				'label' => __( 'Images', 'trx_addons' ),
				'fields' => apply_filters('trx_addons_sc_param_group_params', $repeater->get_controls(), 'trx_sc_bg_slides' ),
				'title_field' => sprintf( __( 'Effect: %s', 'trx_addons' ), '{{{ slide_effect }}}' ),
				'condition' => array(
					'bg_slides_allow' => '1'
				)
			) );

			$element->add_control( 'bg_slides_mask', array(
				'label' => __( 'Allow mask', 'trx_addons' ),
				'label_on' => __( 'On', 'trx_addons' ),
				'label_off' => __( 'Off', 'trx_addons' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => '1'
			) );

			$element->add_control( 'bg_slides_mask_svg', array(
				'label' => __( 'Mask image', 'trx_addons' ),
				'type' => \Elementor\Controls_Manager::MEDIA,
				'default' => array(
					'url' => '',
				),
				'condition' => array(
					'bg_slides_mask' => '1'
				)
			) );

			$element->add_control( 'bg_slides_mask_color', array(
				'label' => __( 'Mask color', 'trx_addons' ),
				'label_block' => false,
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '',
				// Not used, because global colors are not transparent
				'global' => array(
					'active' => false,
				),
				'selectors' => array(
					'{{WRAPPER}} .trx_addons_mask_in_svg' => 'fill: {{VALUE}};',
				),
				'condition' => array(
					'bg_slides_mask' => '1'
				)
			) );

			$element->add_control( 'bg_slides_mask_delay', array(
				'label' => __( 'Mask delay', 'trx_addons' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'default' => array(
					'size' => 8,
					'unit' => 'px'
				),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 20,
						'step' => 1
					),
				),
				'size_units' => array( 'px' ),
				'condition' => array(
					'bg_slides_mask' => '1'
				)
			) );

			$element->add_control( 'bg_slides_mask_zoom', array(
				'label' => __( 'Mask zoom', 'trx_addons' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'default' => array(
					'size' => 1.0,
					'unit' => 'px'
				),
				'range' => array(
					'px' => array(
						'min' => 1.0,
						'max' => 3.0,
						'step' => 0.1
					),
				),
				'size_units' => array( 'px' ),
				'condition' => array(
					'bg_slides_mask' => '1'
				)
			) );

			$element->end_controls_section();
		}
	}
}

// Add "data-bg-slides" to the wrapper of the row
if ( ! function_exists( 'trx_addons_elm_add_bg_slides_data' ) ) {
	// Before Elementor 2.1.0
	add_action( 'elementor/frontend/element/before_render',  'trx_addons_elm_add_bg_slides_data', 10, 1 );
	// After Elementor 2.1.0
	add_action( 'elementor/frontend/section/before_render', 'trx_addons_elm_add_bg_slides_data', 10, 1 );
	function trx_addons_elm_add_bg_slides_data( $element ) {
		if ( is_object( $element ) && in_array( $element->get_name(), array( 'section' ) ) ) {
			//$settings = trx_addons_elm_prepare_global_params( $element->get_settings() );
			$bg_slides_allow = $element->get_settings( 'bg_slides_allow' );
			$bg_mask_allow = $element->get_settings( 'bg_slides_mask' );
			if ( (int)$bg_slides_allow > 0 || (int)$bg_mask_allow > 0 ) {
				$settings = $element->get_settings();
				if ( ! empty( $settings['bg_slides'][0]['slide']['url'] ) || (int)$bg_mask_allow > 0 ) {
					// Load scripts and styles
					trx_addons_bg_slides_load_scripts_front( true );
					// Add class to the section wrapper
					$element->add_render_attribute( '_wrapper', 'class', 'trx_addons_has_bg_slides' );
					// Add data-parameters to the section wrapper
					$element->add_render_attribute( '_wrapper', 'data-trx-addons-bg-slides', json_encode( array(
						'bg_slides_allow'              => (int) $settings['bg_slides_allow'],
						'bg_slides'                    => $settings['bg_slides'],
						'bg_slides_overlay_color'      => $settings['bg_slides_overlay_color'],
						'bg_slides_animation_duration' => $settings['bg_slides_animation_duration']['size'],
						'bg_slides_mask'               => (int)$settings['bg_slides_mask'],
						'bg_slides_mask_svg'           => trx_addons_get_svg_from_file(
															! empty( $settings['bg_slides_mask_svg']['url'] )
																? $settings['bg_slides_mask_svg']['url']
																: trx_addons_get_file_dir( TRX_ADDONS_PLUGIN_ADDONS . 'bg-slides/images/mask-round.svg' )
															),
						'bg_slides_mask_delay'         => max( 1, (int)$settings['bg_slides_mask_delay']['size'] ),
						'bg_slides_mask_zoom'          => max( 1, (float)$settings['bg_slides_mask_zoom']['size'] ),
						)
					) );
				}
			}
		}
	}
}
