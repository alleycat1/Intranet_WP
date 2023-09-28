<?php
/**
 * Elementor extension: Improve core widgets "Spacer" and "Divider"
 *
 * @package ThemeREX Addons
 * @since v2.18.4
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

if ( ! function_exists( 'trx_addons_elm_add_params_alter_height' ) ) {
	add_action( 'elementor/element/before_section_end', 'trx_addons_elm_add_params_alter_height', 10, 3 );
	/**
	 * Add parameters 'Alter height/gap' to the Elementor widgets 'Spacer' and 'Divider'
	 * to add the ability to change the height/gap on fixed values
	 * 
	 * @hooked elementor/element/before_section_end
	 *
	 * @param object $element     Elementor element
	 * @param string $section_id  Section ID
	 * @param array $args         Section arguments
	 */
	function trx_addons_elm_add_params_alter_height( $element, $section_id, $args ) {

		if ( ! is_object( $element ) ) {
			return;
		}
		
		$el_name = $element->get_name();

		if ( ( $el_name == 'spacer' && $section_id == 'section_spacer' )
			  || ( $el_name == 'divider' && $section_id == 'section_divider' )
		) {
			$is_edit_mode = trx_addons_elm_is_edit_mode();
			$element->add_control( 'alter_height', array(
									'type' => \Elementor\Controls_Manager::SELECT,
									'label' => $el_name == 'divider' ? __("Alter gap", 'trx_addons') : __("Alter height", 'trx_addons'),
									'label_block' => true,
									'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_empty_space_heights(''),
									'default' => '',
									'prefix_class' => 'sc_height_'
									) );
		}
	}
}
