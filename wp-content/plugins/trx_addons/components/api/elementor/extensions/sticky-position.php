<?php
/**
 * Elementor extension: Add the option 'Sticky' to the parameter 'Position' for all elements
 *
 * @package ThemeREX Addons
 * @since v2.21.2
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


if ( ! function_exists('trx_addons_elm_add_position_sticky') ) {
	add_action( 'elementor/element/after_section_end', 'trx_addons_elm_add_position_sticky', 10, 3 );
	/**
	 * Add 'Sticky' option to the 'Position' parameter of the 'Common' section for all Elementor's elements
	 * 
	 * @hooked elementor/element/after_section_end
	 *
	 * @param object $element  Elementor element object
	 * @param string $section_id  Section ID
	 * @param array $args  Array with additional arguments
	 */
	function trx_addons_elm_add_position_sticky( $element, $section_id='', $args='' ) {
		if ( ! is_object( $element ) ) {
			return;
		}
		if ( $element->get_name() == 'common' && $section_id == '_section_style' ) {
			$control_name = '_position';
			$controls = $element->get_controls( $control_name );
			if ( ! empty( $controls['options'] ) && is_array( $controls['options'] ) ) {
				$controls['options']['sticky'] = esc_html__( 'Sticky', 'trx_addons' );
				$element->update_control( $control_name, array( 'options' => $controls['options'] ) );
			}
		}
	}
}
