<?php
/**
 * Elementor extension: Gradient animation
 *
 * @package ThemeREX Addons
 * @since v2.18.4
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


if ( ! function_exists( 'trx_addons_elm_add_params_gradient_animation' ) ) {
	add_action( 'elementor/element/before_section_end', 'trx_addons_elm_add_params_gradient_animation', 10, 3 );
	/**
	 * Add a group of parameters 'Gradient animation' to the Elementor's sections and columns
	 * 
	 * @hooked elementor/element/before_section_end
	 * 
	 * @param object $element  Element object
	 * @param string $section_id  Section ID
	 * @param array $args  Section arguments
	 */
	function trx_addons_elm_add_params_gradient_animation( $element, $section_id, $args ) {

		if ( ! is_object( $element ) ) {
			return;
		}
		
		$el_name = $element->get_name();

		// Add 'Gradient animation'
		if ( ( $el_name == 'section' && $section_id == 'section_background' )
			|| ( $el_name == 'column' && $section_id == 'section_style' )
		) {

			$element->add_control( 'gradient_animation', array(
									'type' => \Elementor\Controls_Manager::SELECT,
									'label' => __("Gradient animation", 'trx_addons'),
									'options' => array(
										'none'       => __("None", 'trx_addons'),
										'horizontal' => __("Horizontal", 'trx_addons'),
										'vertical'   => __("Vertical", 'trx_addons'),
										'diagonal'   => __("Diagonal", 'trx_addons'),
									),
									'default' => 'none',
									'condition' => array(
										'background_background' => array( 'gradient' ),
									),
									'prefix_class' => 'sc_gradient_animation_',
								) );

			$element->add_control( 'gradient_animation_speed', array(
									'type' => \Elementor\Controls_Manager::SELECT,
									'label' => __("Animation speed", 'trx_addons'),
									'options' => array(
										'slow'   => __("Slow", 'trx_addons'),
										'normal' => __("Normal", 'trx_addons'),
										'fast'   => __("Fast", 'trx_addons'),
									),
									'default' => 'normal',
									'condition' => array(
										'background_background' => array( 'gradient' ),
										'gradient_animation!' => array( 'none' ),
									),
									'prefix_class' => 'sc_gradient_speed_',
								) );
		}
	}
}

if ( ! function_exists( 'trx_addons_elm_add_params_gradient_animation_common' ) ) {
	add_action( 'elementor/element/before_section_end', 'trx_addons_elm_add_params_gradient_animation_common', 10, 3 );
	/**
	 * Add a group of parameters 'Gradient animation' to the Elementor's common section for all elements
	 * 
	 * @hooked elementor/element/before_section_end
	 * 
	 * @param object $element  Element object
	 * @param string $section_id  Section ID
	 * @param array $args  Section arguments
	 */
	function trx_addons_elm_add_params_gradient_animation_common( $element, $section_id, $args ) {

		if ( ! is_object( $element ) ) {
			return;
		}
		
		$el_name = $element->get_name();

		// Add 'Gradient animation'
		if ( $el_name == 'common' && $section_id == '_section_background' ) {

			$element->add_control( 'gradient_animation', array(
									'type' => \Elementor\Controls_Manager::SELECT,
									'label' => __("Gradient animation", 'trx_addons'),
									'options' => array(
										'none'       => __("None", 'trx_addons'),
										'horizontal' => __("Horizontal", 'trx_addons'),
										'vertical'   => __("Vertical", 'trx_addons'),
										'diagonal'   => __("Diagonal", 'trx_addons'),
									),
									'default' => 'none',
									'condition' => array(
										'_background_background' => array( 'gradient' ),
									),
									'prefix_class' => 'sc_gradient_animation_',
								) );

			$element->add_control( 'gradient_animation_speed', array(
									'type' => \Elementor\Controls_Manager::SELECT,
									'label' => __("Animation speed", 'trx_addons'),
									'options' => array(
										'slow'   => __("Slow", 'trx_addons'),
										'normal' => __("Normal", 'trx_addons'),
										'fast'   => __("Fast", 'trx_addons'),
									),
									'default' => 'normal',
									'condition' => array(
										'_background_background' => array( 'gradient' ),
										'gradient_animation!' => array( 'none' ),
									),
									'prefix_class' => 'sc_gradient_speed_',
								) );
		}
	}
}
