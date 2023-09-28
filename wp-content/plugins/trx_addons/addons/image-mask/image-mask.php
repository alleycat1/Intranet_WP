<?php
/**
 * Use an image as a mask for Elementor's elements
 *
 * @addon image-mask
 * @version 1.1
 *
 * @package ThemeREX Addons
 * @since v1.95.2
 */


// Add "Image mask" params to all elements
if (!function_exists('trx_addons_elm_add_params_image_mask')) {
	add_action( 'elementor/element/before_section_start', 'trx_addons_elm_add_params_image_mask', 10, 3 );
	add_action( 'elementor/widget/before_section_start', 'trx_addons_elm_add_params_image_mask', 10, 3 );
	function trx_addons_elm_add_params_image_mask($element, $section_id, $args) {

		if ( ! is_object($element) ) return;

		if ( in_array( $element->get_name(), array( 'section', 'column', 'common' ) ) && $section_id == '_section_responsive' ) {
			
			$element->start_controls_section( 'section_trx_image_mask', array(
																		'tab' => !empty($args['tab']) ? $args['tab'] : \Elementor\Controls_Manager::TAB_ADVANCED,
																		'label' => __( 'Image mask', 'trx_addons' )
																	) );

			$element->add_control( 'image_mask', array(
				'label' => __( 'Mask image', 'trx_addons' ),
				'type' => \Elementor\Controls_Manager::MEDIA,
				'default' => [
					'url' => '',
				],
				'selectors' => [
					'{{WRAPPER}}' => '-webkit-mask-image:url({{URL}});mask-image:url({{URL}});',
				],
			) );

			$element->add_control( 'image_mask_size', array(
				'type' => \Elementor\Controls_Manager::SELECT,
				'label' => __( 'Mask size', 'trx_addons' ),
				'label_block' => false,
				'options' => apply_filters( 'trx_addons_filter_image_mask_sizes', trx_addons_get_list_background_sizes() ),
				'condition' => array(
					'image_mask[url]!' => ''
				),
				'default' => 'contain',
				'selectors' => [
					'{{WRAPPER}}' => '-webkit-mask-size:{{VALUE}};mask-size:{{VALUE}};',
				],
			) );

			$element->add_control( 'image_mask_position', array(
				'type' => \Elementor\Controls_Manager::SELECT,
				'label' => __( 'Mask position', 'trx_addons' ),
				'label_block' => false,
				'options' => apply_filters( 'trx_addons_filter_image_mask_positions', trx_addons_get_list_background_positions( false, false ) ),
				'default' => 'center center',
				'condition' => array(
					'image_mask[url]!' => ''
				),
				'selectors' => [
					'{{WRAPPER}}' => '-webkit-mask-position:{{VALUE}};mask-position:{{VALUE}};',
				],
			) );

			$element->add_control( 'image_mask_repeat', array(
				'type' => \Elementor\Controls_Manager::SELECT,
				'label' => __( 'Mask repeat', 'trx_addons' ),
				'label_block' => false,
				'options' => apply_filters( 'trx_addons_filter_image_mask_repeats', trx_addons_get_list_background_repeats() ),
				'default' => 'no-repeat',
				'condition' => array(
					'image_mask[url]!' => ''
				),
				'selectors' => [
					'{{WRAPPER}}' => '-webkit-mask-repeat:{{VALUE}};mask-repeat:{{VALUE}};',
				],
			) );

			$element->end_controls_section();
		}
	}
}
