<?php
/**
 * Elementor extension: Animation type for any element
 *
 * @package ThemeREX Addons
 * @since v2.18.4
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


if ( ! function_exists( 'trx_addons_elm_add_params_animation_type' ) ) {
	add_action( 'elementor/element/before_section_end', 'trx_addons_elm_add_params_animation_type', 10, 3 );
	/**
	 * Add parameter 'Animation type' to the Elementor's sections, columns and all elements to the 'Effects' section.
	 * This parameter allows to animate whole block or split animation by items (if possible)
	 * 
	 * @hooked elementor/element/before_section_end
	 *
	 * @param object $element  Element object
	 * @param string $section_id  Section ID
	 * @param array $args  Section params
	 */
	function trx_addons_elm_add_params_animation_type( $element, $section_id, $args ) {

		if ( ! is_object( $element ) ) {
			return;
		}
		
		$el_name = $element->get_name();
		
		if ( $section_id == 'section_effects' && in_array( $el_name, array( 'section', 'column', 'common' ) ) ) {
			$element->add_control( '_animation_type', array(
				'type' => \Elementor\Controls_Manager::SELECT,
				'label' => __("Animation type", 'trx_addons'),
				'label_block' => false,
				'description' => __("Animate whole block or split animation by items (if possible)", 'trx_addons'),
				'options' => array(
					'block'     => __( 'Whole block', 'trx_addons' ),
					'sequental' => __( 'Item by item', 'trx_addons' ),
					'random'    => __( 'Random items', 'trx_addons' ),
				),
				'condition' => array(
					( $el_name == 'common' ? '_animation!' : 'animation!' ) => array( '', 'none' )
				),
				'default' => 'block',
				'prefix_class' => 'animation_type_'
			) );
		}
	}
}
