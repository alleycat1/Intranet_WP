<?php
/**
 * Elementor extension: Hide background image for Sections and Columns
 *
 * @package ThemeREX Addons
 * @since v2.18.4
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

if ( ! function_exists( 'trx_addons_elm_add_params_hide_bg_image' ) ) {
	add_action( 'elementor/element/before_section_end', 'trx_addons_elm_add_params_hide_bg_image', 10, 3 );
	/**
	 * Add a group of parameters 'Hide bg image on XXX' to the Elementor's sections and columns
	 * 
	 * @hooked elementor/element/before_section_end
	 * 
	 * @param object $element  Element object
	 * @param string $section_id  Section ID
	 * @param array $args  Section arguments
	 */
	function trx_addons_elm_add_params_hide_bg_image( $element, $section_id, $args ) {

		if ( ! is_object( $element ) ) {
			return;
		}
		
		$el_name = $element->get_name();

		// Add 'Hide bg image on XXX' to the rows
		if ( ( $el_name == 'section' && $section_id == 'section_background' )
			|| ( $el_name == 'column' && $section_id == 'section_style' )
		) {
			$element->add_control( 'hide_bg_image_on_desktop', array(
									'type' => \Elementor\Controls_Manager::SWITCHER,
									'label' => __( 'Hide bg image on the desktop', 'trx_addons' ),
									'label_on' => __( 'Hide', 'trx_addons' ),
									'label_off' => __( 'Show', 'trx_addons' ),
									'return_value' => 'desktop',
									'render_type' => 'template',
									'prefix_class' => 'hide_bg_image_on_',
								) );

			$element->add_control( 'hide_bg_image_on_tablet', array(
									'type' => \Elementor\Controls_Manager::SWITCHER,
									'label' => __( 'Hide bg image on the tablet', 'trx_addons' ),
									'label_on' => __( 'Hide', 'trx_addons' ),
									'label_off' => __( 'Show', 'trx_addons' ),
									'return_value' => 'tablet',
									'render_type' => 'template',
									'prefix_class' => 'hide_bg_image_on_',
								) );

			$element->add_control( 'hide_bg_image_on_mobile', array(
									'type' => \Elementor\Controls_Manager::SWITCHER,
									'label' => __( 'Hide bg image on the mobile', 'trx_addons' ),
									'label_on' => __( 'Hide', 'trx_addons' ),
									'label_off' => __( 'Show', 'trx_addons' ),
									'return_value' => 'mobile',
									'render_type' => 'template',
									'prefix_class' => 'hide_bg_image_on_',
								) );
		}
	}
}
