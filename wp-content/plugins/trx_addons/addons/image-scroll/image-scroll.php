<?php
/**
 * Scroll behaviour for Elementor's images
 *
 * @addon image-scroll
 * @version 1.1
 *
 * @package ThemeREX Addons
 * @since v1.97.0
 */


// Load required styles and scripts for the frontend
if ( ! function_exists( 'trx_addons_image_scroll_load_scripts_front' ) ) {
	add_action( 'wp_enqueue_scripts', 'trx_addons_image_scroll_load_scripts_front', TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY );
	add_action( 'trx_addons_action_pagebuilder_preview_scripts', 'trx_addons_image_scroll_load_scripts_front', 10, 1 );
	function trx_addons_image_scroll_load_scripts_front( $force = false ) {
		trx_addons_enqueue_optimized( 'image_scroll', $force, array(
			'css'  => array(
				'trx_addons-image-scroll' => array( 'src' => TRX_ADDONS_PLUGIN_ADDONS . 'image-scroll/image-scroll.css' ),
			),
			'js' => array(
				'trx_addons-image-scroll' => array( 'src' => TRX_ADDONS_PLUGIN_ADDONS . 'image-scroll/image-scroll.js', 'deps' => 'jquery' ),
			),
		) );
	}
}

	
// Merge styles to the single stylesheet
if ( ! function_exists( 'trx_addons_image_scroll_merge_styles' ) ) {
	add_filter( 'trx_addons_filter_merge_styles', 'trx_addons_image_scroll_merge_styles' );
	function trx_addons_image_scroll_merge_styles( $list ) {
		$list[ TRX_ADDONS_PLUGIN_ADDONS . 'image-scroll/image-scroll.css' ] = false;
		return $list;
	}
}

	
// Merge specific scripts into single file
if ( ! function_exists( 'trx_addons_image_scroll_merge_scripts' ) ) {
	add_action( 'trx_addons_filter_merge_scripts', 'trx_addons_image_scroll_merge_scripts' );
	function trx_addons_image_scroll_merge_scripts( $list ) {
		$list[ TRX_ADDONS_PLUGIN_ADDONS . 'image-scroll/image-scroll.js' ] = false;
		return $list;
	}
}


// Return list of image scroll directions
if ( ! function_exists( 'trx_addons_image_scroll_directions' ) ) {
	function trx_addons_image_scroll_directions( $add_none = true ) {
		$list = apply_filters( 'trx_addons_filter_image_scroll_directions', array(
									'up'    => esc_html__( 'Up', 'trx_addons' ),
									'down'  => esc_html__( 'Down', 'trx_addons' ),
									'left'  => esc_html__( 'Left', 'trx_addons' ),
									'right' => esc_html__( 'Right', 'trx_addons' ),
								) );
		return $add_none ? trx_addons_array_merge( array( '' => esc_html__( 'None', 'trx_addons' ) ), $list ) : $list;
	}
}


// Return list of image scroll events
if ( ! function_exists( 'trx_addons_image_scroll_events' ) ) {
	function trx_addons_image_scroll_events() {
		return apply_filters( 'trx_addons_filter_image_scroll_events', array(
									'hover' => esc_html__( 'Mouse hover', 'trx_addons' ),
									'wheel' => esc_html__( 'Mouse wheel', 'trx_addons' ),
								) );
	}
}


// Add addon params to the 'Image'
if ( ! function_exists( 'trx_addons_elm_add_params_image_scroll' ) ) {
	add_action( 'elementor/element/before_section_end', 'trx_addons_elm_add_params_image_scroll', 10, 3 );
	add_action( 'elementor/widget/before_section_end', 'trx_addons_elm_add_params_image_scroll', 10, 3 );
	function trx_addons_elm_add_params_image_scroll( $element, $section_id, $args ) {

		if ( ! is_object( $element ) ) return;

		if ( 'image' == $element->get_name() && 'section_image' === $section_id ) {
			$element->add_control(
				'image_scroll_heading',
				array(
					'label' => esc_html__( 'Scroll image on mouse hover', 'trx_addons' ),
					'type' => \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				)
			);
			$element->add_control(
				'image_scroll_direction',
				array(
					'label' => __( 'Scroll direction', 'trx_addons' ),
					'options' => trx_addons_image_scroll_directions(),
					'default' => '',
					'prefix_class' => 'trx_addons_image_scroll_direction_',
					'type' => \Elementor\Controls_Manager::SELECT,
				)
			);
			$element->add_control(
				'image_scroll_event',
				array(
					'label' => __( 'Scroll event', 'trx_addons' ),
					'options' => trx_addons_image_scroll_events(),
					'default' => 'hover',
					'condition' => array(
						'image_scroll_direction' => array( 'up', 'down' ),
					),						
					'prefix_class' => 'trx_addons_image_scroll_event_',
					'type' => \Elementor\Controls_Manager::SELECT,
				)
			);
			$element->add_responsive_control(
				'image_scroll_height',
				array(
					'label' => __( 'Max.height', 'trx_addons' ),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'default' => array(
						'size' => 200,
						'unit' => 'px'
					),
					'size_units' => array( 'px', 'em', 'vh' ),
					'range' => array(
						'px' => array(
							'min' => 50,
							'max' => 1000
						),
						'em' => array(
							'min' => 1,
							'max' => 100
						),
						'vh' => array(
							'min' => 1,
							'max' => 100
						),
					),
					'condition' => array(
						'image_scroll_direction!' => '',
					),						
					'selectors' => array(
						'{{WRAPPER}}' => '--trx-addons-image-scroll-height:{{SIZE}}{{UNIT}};',
					),
				)
			);
			$element->add_responsive_control(
				'image_scroll_pos',
				array(
					'label' => __( 'Start position (%)', 'trx_addons' ),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'default' => array(
						'size' => 0,
						'unit' => '%'
					),
					'size_units' => array( '%' ),
					'range' => array(
						'%' => array(
							'min' => 0,
							'max' => 100
						),
					),
					'condition' => array(
						'image_scroll_direction!' => '',
						'image_scroll_event' => 'hover'
					),						
					'selectors' => array(
						'{{WRAPPER}}' => '--trx-addons-image-scroll-start-pos:{{SIZE}}{{UNIT}};',
					),
				)
			);
			$element->add_responsive_control(
				'image_scroll_duration',
				array(
					'label' => __( 'Duration (s)', 'trx_addons' ),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'default' => array(
						'size' => 3,
						'unit' => 'px'
					),
					'size_units' => array( 'px' ),
					'range' => array(
						'px' => array(
							'min' => 0,
							'max' => 10,
							'step' => 0.1
						),
					),
					'condition' => array(
						'image_scroll_direction!' => '',
						'image_scroll_event' => 'hover'
					),						
					'selectors' => array(
						'{{WRAPPER}}' => '--trx-addons-image-scroll-duration:{{SIZE}}s;',
					),
				)
			);
		}
	}
}


// Load scripts and styles
if ( ! function_exists( 'trx_addons_image_scroll_before_render_elements' ) ) {
	// Before Elementor 2.1.0
	add_action( 'elementor/frontend/element/before_render',  'trx_addons_image_scroll_before_render_elements', 10, 1 );
	// After Elementor 2.1.0
	add_action( 'elementor/frontend/widget/before_render', 'trx_addons_image_scroll_before_render_elements', 10, 1 );
	function trx_addons_image_scroll_before_render_elements( $element ) {
		if ( is_object( $element ) ) {
			$el_name = $element->get_name();
			if ( 'image' == $el_name ) {
				//$settings = trx_addons_elm_prepare_global_params( $element->get_settings() );
				$image_scroll = $element->get_settings( 'image_scroll_direction' );
				if ( ! empty( $image_scroll ) ) {
					trx_addons_image_scroll_load_scripts_front( true );
				}
			}
		}
	}
}
