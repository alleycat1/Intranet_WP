<?php
/**
 * Elementor extension: Fly (absolute position) for all elements
 *
 * @package ThemeREX Addons
 * @since v2.18.4
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

if ( ! function_exists( 'trx_addons_elm_add_params_fly' ) ) {
	add_action( 'elementor/element/before_section_start', 'trx_addons_elm_add_params_fly', 10, 3 );
	add_action( 'elementor/widget/before_section_start', 'trx_addons_elm_add_params_fly', 10, 3 );
	/**
	 * Add a group of parameters 'Fly' (absolute position) to the Elementor's section, column and common controls.
	 * This parameters allow to change position of the element in the absolute coordinates in the parent element.
	 * 
	 * @hooked elementor/element/before_section_start
	 * @hooked elementor/widget/before_section_start
	 *
	 * @param object $element  Elementor's element object
	 * @param string $section_id  Section ID to place new controls before (default: '_section_responsive')
	 * @param array $args  Additional params
	 */
	function trx_addons_elm_add_params_fly( $element, $section_id, $args ) {

		if ( ! is_object( $element ) ) {
			return;
		}

		if ( in_array( $element->get_name(), array( 'section', 'column', 'common' ) ) && $section_id == '_section_responsive' ) {

			// Detect edit mode
			$is_edit_mode = trx_addons_elm_is_edit_mode();

			// Register controls
			$element->start_controls_section( 'section_trx_fly', array(
																		'tab' => !empty($args['tab']) ? $args['tab'] : \Elementor\Controls_Manager::TAB_ADVANCED,
																		'label' => __( 'Fly', 'trx_addons' )
																	) );
			$element->add_responsive_control(
				'fly',
				array(
					'label' => __( 'Fly', 'trx_addons' ),
					'label_block' => false,
					'type' => \Elementor\Controls_Manager::SELECT,
					'options' => ! $is_edit_mode ? array() : array_merge(
									array('static' => __('Static', 'trx_addons')),
									array('custom' => __('Custom', 'trx_addons')),
									trx_addons_get_list_sc_positions()
								),
					'default' => 'static',
					'prefix_class' => 'sc%s_fly_',
				)
			);
			$coord = array(
							'label' => __( 'Left', 'trx_addons' ),
							'type' => \Elementor\Controls_Manager::SLIDER,
							'default' => array(
								'size' => '',
								'unit' => 'px'
							),
							'size_units' => array( 'px', 'em', '%' ),
							'range' => array(
								'px' => array(
									'min' => -500,
									'max' => 500
								),
								'em' => array(
									'min' => -50,
									'max' => 50
								),
								'%' => array(
									'min' => -100,
									'max' => 100
								)
							),
							'condition' => array(
								'fly' => array( 'custom', 'tl', 'tr', 'bl', 'br' )
							),
							'selectors' => array(
								'{{WRAPPER}}' => 'left: {{SIZE}}{{UNIT}};'
							),
						);
			$element->add_responsive_control( 'fly_left', $coord );
			$coord['label'] = __( 'Right', 'trx_addons' );
			$coord['selectors'] = array( '{{WRAPPER}}' => 'right: {{SIZE}}{{UNIT}};' );
			$element->add_responsive_control( 'fly_right', $coord );
			$coord['label'] = __( 'Top', 'trx_addons' );
			$coord['selectors'] = array( '{{WRAPPER}}' => 'top: {{SIZE}}{{UNIT}};' );
			$element->add_responsive_control( 'fly_top', $coord );
			$coord['label'] = __( 'Bottom', 'trx_addons' );
			$coord['selectors'] = array( '{{WRAPPER}}' => 'bottom: {{SIZE}}{{UNIT}};' );
			$element->add_responsive_control( 'fly_bottom', $coord );

			$element->add_responsive_control( 'fly_scale', array(
													'label' => __( 'Scale', 'trx_addons' ),
													'type' => \Elementor\Controls_Manager::SLIDER,
													'default' => array(
														'size' => '',
														'unit' => 'px'
													),
													'size_units' => array( 'px' ),
													'range' => array(
														'px' => array(
															'min' => 0,
															'max' => 10,
															'step' => 0.1
														)
													),
													'selectors' => array(
														'{{WRAPPER}} .elementor-widget-container' => 'transform: scale({{SIZE}}, {{SIZE}}) rotate({{fly_rotate.SIZE}}deg);'
													),
									) );

			$element->add_responsive_control( 'fly_rotate', array(
													'label' => __( 'Rotation (in deg)', 'trx_addons' ),
													'type' => \Elementor\Controls_Manager::SLIDER,
													'default' => array(
														'size' => '',
														'unit' => 'px'
													),
													'size_units' => array( 'px' ),
													'range' => array(
														'px' => array(
															'min' => -360,
															'max' => 360,
															'step' => 1
														)
													),
													'selectors' => array(
														'{{WRAPPER}} .elementor-widget-container' => 'transform: rotate({{SIZE}}deg) scale({{fly_scale.SIZE}}, {{fly_scale.SIZE}}) ;'
													),
									) );

			$element->end_controls_section();
		}
	}
}
