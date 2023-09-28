<?php
/**
 * Elementor extension: Extend background for Sections, Columns and Text
 *
 * @package ThemeREX Addons
 * @since v2.18.4
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


if ( ! function_exists( 'trx_addons_elm_add_params_extend_bg' ) ) {
	add_action( 'elementor/element/before_section_end', 'trx_addons_elm_add_params_extend_bg', 10, 3 );
	/**
	 * Add a group of parameters 'Extend background' and 'Background mask' to the Elementor's sections, columns and text-editor
	 * to allow to extend background out of the element's area
	 * 
	 * @hooked elementor/element/before_section_end
	 *
	 * @param object $element  Element object
	 * @param string $section_id  Section ID
	 * @param array $args  Section params
	 */
	function trx_addons_elm_add_params_extend_bg( $element, $section_id, $args ) {

		if ( ! is_object( $element ) ) {
			return;
		}
		
		$el_name = $element->get_name();

		if (   ( $el_name == 'section'     && $section_id == 'section_background' )
			|| ( $el_name == 'column'      && $section_id == 'section_style' )
			|| ( $el_name == 'text-editor' && $section_id == 'section_background' )
		) {

			$is_edit_mode = trx_addons_elm_is_edit_mode();

			$element->add_control( 'extra_bg', array(
									'type' => \Elementor\Controls_Manager::SELECT,
									'label' => __("Extend background", 'trx_addons'),
									'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_content_extra_bg(''),
									'default' => '',
									'prefix_class' => 'sc_extra_bg_'
									) );

			$element->add_control( 'extra_bg_mask', array(
									'type' => \Elementor\Controls_Manager::SELECT,
									'label' => __("Background mask", 'trx_addons'),
									'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_content_extra_bg_mask(''),
									'default' => '',
									'prefix_class' => 'sc_bg_mask_'
									) );
		}
	}
}
