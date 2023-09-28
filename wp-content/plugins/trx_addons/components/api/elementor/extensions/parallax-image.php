<?php
/**
 * Elementor extension: Parallax effect for a core widget "Image"
 *
 * @package ThemeREX Addons
 * @since v2.18.4
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


if ( ! function_exists( 'trx_addons_elm_add_params_parallax_to_image' ) ) {
	add_action( 'elementor/element/before_section_end', 'trx_addons_elm_add_params_parallax_to_image', 10, 3 );
	/**
	 * Add parallax parameters to the Elementor's widget 'Image' to allow to shift image on scroll
	 * 
	 * @hooked elementor/element/before_section_end
	 * 
	 * @param object $element Current element
	 * @param string $section_id Section ID
	 * @param array $args Section arguments
	 */
	function trx_addons_elm_add_params_parallax_to_image( $element, $section_id, $args ) {
		if ( is_object( $element ) ) {
			$el_name = $element->get_name();
			if ( 'image' == $el_name && 'section_image' === $section_id ) {
				$element->add_control(
					'parallax_heading',
					array(
						'label' => esc_html__( 'Shift image on scroll', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::HEADING,
						'separator' => 'before',
					)
				);
				$element->add_control(
					'parallax_speed',
					array(
						'label' => __( 'Shift speed', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => array(
							'size' => 0
						),
						'range' => array(
							'px' => array(
								'min' => -50,
								'max' => 50
							)
						),
					)
				);
				$element->add_responsive_control(
					'parallax_height',
					array(
						'label' => __( 'Max.height', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => array(
							'size' => 150,
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
							'parallax_speed[size]!' => 0,
						),						
						'selectors' => array(
							'{{WRAPPER}} .elementor-image, {{WRAPPER}} .elementor-image > .wp-caption' => 'display: flex; align-items: center; justify-content: center; overflow: hidden; max-height: {{SIZE}}{{UNIT}};',
							'{{WRAPPER}} .elementor-image > .wp-caption > img' => 'width: 100%;'
						),
					)
				);
			}
		}
	}
}

if ( ! function_exists( 'trx_addons_elm_add_params_parallax_to_image_before_render' ) ) {
	// Before Elementor 2.1.0
	add_action( 'elementor/frontend/element/before_render', 'trx_addons_elm_add_params_parallax_to_image_before_render', 10, 1 );
	// After Elementor 2.1.0
	add_action( 'elementor/frontend/widget/before_render', 'trx_addons_elm_add_params_parallax_to_image_before_render', 10, 1 );
	/**
	 * Add attribute data-parallax and class 'sc_parallax_wrap' to the Elementor's widget 'Image'
	 * to allow to shift image on scroll via JavaScript
	 * 
	 * @hooked elementor/frontend/element/before_render (before Elementor 2.1.0)
	 * @hooked elementor/frontend/widget/before_render (after Elementor 2.1.0)
	 * 
	 * @param object $element Current element
	 */
	function trx_addons_elm_add_params_parallax_to_image_before_render( $element ) {
		if ( is_object( $element ) ) {
			$el_name = $element->get_name();
			if ( 'image' == $el_name ) {
				//$settings = trx_addons_elm_prepare_global_params( $element->get_settings() );
				$parallax_speed = $element->get_settings( 'parallax_speed' );
				if ( ! empty( $parallax_speed['size'] ) ) {
					$parallax_height = $element->get_settings( 'parallax_height' );
					if ( ! empty( $parallax_height['size'] ) && ! empty( $parallax_height['unit'] ) ) {
						trx_addons_enqueue_parallax();
						$element->add_render_attribute( 'wrapper', 'class', 'sc_parallax_wrap' );
						$element->add_render_attribute( 'wrapper', 'data-parallax', $parallax_speed['size'] );
					}
				}
			}
		}
	}
}
