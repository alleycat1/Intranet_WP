<?php
/**
 * Elementor extension: Shift and Push sections and columns
 *
 * @package ThemeREX Addons
 * @since v2.18.4
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

if ( ! function_exists( 'trx_addons_elm_add_columns_position' ) ) {
	add_action( 'elementor/element/before_section_start', 'trx_addons_elm_add_columns_position', 10, 3 );
	/**
	 * Add new controls section to the columns and sections with the parameters 'Shift block', 'Push block', 'Pull block'
	 * and 'Fix column' to allow to shift, push and pull the block to any direction and fix the column when page scrolling.
	 * 'Shift block' - shift the block to any direction (left, right, top, bottom) without changing the sibling blocks position.
	 * 'Push block'  - push the block to any direction (left, right, top, bottom) and change the sibling blocks position.
	 * 'Pull block'  - don't change the block position and change the sibling blocks position.
	 *
	 * @hooked elementor/element/before_section_start
	 *
	 * @param object $element  Element object
	 * @param string $section_id  Section ID
	 * @param array $args  Section arguments
	 */
	function trx_addons_elm_add_columns_position( $element, $section_id, $args ) {

		if ( ! is_object( $element ) ) {
			return;
		}
		
		if ( in_array( $element->get_name(), array( 'section', 'column' ) ) && $section_id == '_section_responsive' ) {
			
			$element->start_controls_section( 'section_trx_layout',	array(
																		'tab' => ! empty( $args['tab'] ) ? $args['tab'] : \Elementor\Controls_Manager::TAB_ADVANCED,
																		'label' => __( 'Position', 'trx_addons' )
																	) );

			// Detect edit mode
			$is_edit_mode = trx_addons_elm_is_edit_mode();

			// Add 'Fix column' to the columns
			if ( $element->get_name() == 'column' ) {
				$element->add_control( 'fix_column', array(
									'type' => \Elementor\Controls_Manager::SWITCHER,
									'label' => __( 'Fix column', 'trx_addons' ),
									'description' => wp_kses_data( __("Fix this column when page scrolling. Attention! At least one column in the row must have a greater height than this column", 'trx_addons') ),
									'label_on' => __( 'Fix', 'trx_addons' ),
									'label_off' => __( 'No', 'trx_addons' ),
									'return_value' => 'fixed',
									'render_type' => 'template',
									'prefix_class' => 'sc_column_',
									) );
			}

			// Add 'Shift block' to the sections and columns
			$element->add_control( 'shift_x', array(
									'type' => \Elementor\Controls_Manager::SELECT,
									'label' => __("Shift block along the X-axis", 'trx_addons'),
									'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_content_shift(''),
									'default' => '',
									'prefix_class' => 'sc_shift_x_'
									) );

			$element->add_control( 'shift_y', array(
									'type' => \Elementor\Controls_Manager::SELECT,
									'label' => __("Shift block along the Y-axis", 'trx_addons'),
									'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_content_shift(''),
									'default' => '',
									'prefix_class' => 'sc_shift_y_'
									) );
			
			// Add 'Push block' to the sections and columns
			$element->add_control( 'push_x', array(
									'type' => \Elementor\Controls_Manager::SELECT,
									'label' => __("Push block along the X-axis", 'trx_addons'),
									'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_content_shift(''),
									'default' => '',
									'prefix_class' => 'sc_push_x_'
									) );

			$element->add_control( 'push_y', array(
									'type' => \Elementor\Controls_Manager::SELECT,
									'label' => __("Push block along the Y-axis", 'trx_addons'),
									'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_content_shift(''),
									'default' => '',
									'prefix_class' => 'sc_push_y_'
									) );

			// Add 'Pull block' to the sections and columns
			$element->add_control( 'pull_x', array(
									'type' => \Elementor\Controls_Manager::SELECT,
									'label' => __("Pull next block along the X-axis", 'trx_addons'),
									'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_content_shift(''),
									'default' => '',
									'prefix_class' => 'sc_pull_x_'
									) );

			$element->add_control( 'pull_y', array(
									'type' => \Elementor\Controls_Manager::SELECT,
									'label' => __("Pull next block along the Y-axis", 'trx_addons'),
									'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_content_shift(''),
									'default' => '',
									'prefix_class' => 'sc_pull_y_'
									) );

			$element->end_controls_section();
		}
	}
}
