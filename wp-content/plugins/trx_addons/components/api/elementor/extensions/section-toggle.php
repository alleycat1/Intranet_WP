<?php
/**
 * Elementor extension: Add a toggle behaviour for sections
 *
 * @package ThemeREX Addons
 * @since v2.18.4
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


if ( ! function_exists('trx_addons_elm_section_toggle_add_params' ) ) {
	add_action( 'elementor/element/before_section_end', 'trx_addons_elm_section_toggle_add_params', 10, 3 );
	/**
	 * Add toggle parameters to the Elementor's section to allow to toggle it on click on the any link with href='#section-id'
	 * 
	 * @hooked elementor/element/before_section_end
	 * 
	 * @param object $element Current element
	 * @param string $section_id Section ID
	 * @param array $args Section arguments
	 */
	function trx_addons_elm_section_toggle_add_params( $element, $section_id, $args ) {

		if ( ! is_object( $element ) ) {
			return;
		}
		
		$el_name = $element->get_name();

		if ( $el_name == 'section' && $section_id == 'section_layout' ) {

			$is_edit_mode = trx_addons_elm_is_edit_mode();

			$element->add_control( 'trx_section_toggle', array(
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'separator' => 'before',
				'label' => __( 'Toggle behaviour', 'trx_addons' ),
				'label_block' => false,
				'label_off' => __( 'Off', 'trx_addons' ),
				'label_on' => __( 'On', 'trx_addons' ),
				'prefix_class' => 'sc_section_toggle_',
				'return_value' => 'on'
			) );

			$element->add_control( 'trx_section_toggle_close', array(
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label' => __( 'Add "Close" button', 'trx_addons' ),
				'label_block' => false,
				'label_off' => __( 'Off', 'trx_addons' ),
				'label_on' => __( 'On', 'trx_addons' ),
				'return_value' => 'on',
				'condition' => array(
					'trx_section_toggle' => 'on'
				),
				'prefix_class' => 'sc_section_toggle_close_',
			) );

			$element->add_control( 'trx_section_toggle_state', array(
				'type' => \Elementor\Controls_Manager::SELECT,
				'label' => __( 'Initial state', 'trx_addons' ),
				'label_block' => false,
				'options' => array(
					'show' => __( 'Show', 'trx_addons' ),
					'hide' => __( 'Hide', 'trx_addons' ),
				),
				'default' => 'show',
				'condition' => array(
					'trx_section_toggle' => 'on'
				),
				'prefix_class' => 'sc_section_toggle_state_',
			) );
		}
	}
}
