<?php
/**
 * Elementor extension: Add parameters "Hide on XXX"
 *
 * @package ThemeREX Addons
 * @since v2.18.4
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

if ( ! function_exists( 'trx_addons_elm_get_hide_on_xxx' ) ) {
	/**
	 * Return hide_on_xxx setting for the Elementor widgets. If the setting is 'replace' and the custom breakpoints are set, then return 'add'
	 * 
	 * @return string   A current value of the setting
	 */
	function trx_addons_elm_get_hide_on_xxx() {
		$value = trx_addons_get_setting( 'add_hide_on_xxx' );
		if ( $value == 'replace'
			&& trx_addons_elm_is_experiment_active( 'additional_custom_breakpoints' )
			&& trx_addons_elm_has_custom_breakpoints()
		) {
			$value = 'add';
		}
		return $value;
	}
}

if ( ! function_exists( 'trx_addons_elm_localize_admin_scripts' ) ) {
	add_filter( 'trx_addons_filter_localize_script_admin',	'trx_addons_elm_localize_admin_scripts');
	/**
	 * Add Elementor specific vars to the localize array of the admin scripts.
	 * If the setting is 'replace', then remove the Elementor's parameters 'Hide on XXX' in JS
	 * 
	 * @hooked trx_addons_filter_localize_script_admin
	 * 
	 * @param array $vars List of vars
	 * 
	 * @return array    List of vars
	 */
	function trx_addons_elm_localize_admin_scripts( $vars = array() ) {
		$vars['add_hide_on_xxx'] = trx_addons_elm_get_hide_on_xxx();
		return $vars;
	}
}

if ( ! function_exists( 'trx_addons_elm_add_params_hide_on_xxx' ) ) {
	add_action( 'elementor/element/before_section_end', 'trx_addons_elm_add_params_hide_on_xxx', 10, 3 );
	/**
	 * Add a group of parameters 'Hide on XXX' to the Elementor's sections, columns and widgets to the section 'Responsive'.
	 * An internal plugin setting 'add_hide_on_xxx' is used to enable/disable this feature.
	 * 
	 * @hooked elementor/element/before_section_end
	 * 
	 * @param object $element  Element object
	 * @param string $section_id  Section ID
	 * @param array $args  Section arguments
	 */
	function trx_addons_elm_add_params_hide_on_xxx( $element, $section_id, $args ) {
		if ( ! is_object( $element ) ) {
			return;
		}
		// Add 'Hide on XXX' to the any elements if the internal plugin setting 'add_hide_on_xxx' is 'add' or 'replace'.
		$add_hide_on_xxx = trx_addons_elm_get_hide_on_xxx();
		if ( ! trx_addons_is_off( $add_hide_on_xxx ) && class_exists( 'TRX_Addons_Elementor_Widget' ) ) {
			if ( $section_id == '_section_responsive' ) { // && $el_name == 'section'
				$params = TRX_Addons_Elementor_Widget::get_hide_param( false );
				if ( is_array( $params ) ) {
					if ( $add_hide_on_xxx == 'add' ) {
						$element->add_control( 'trx_addons_responsive_heading', array(
							'label' => __( 'Theme-specific params', 'trx_addons' ),
							'type' => \Elementor\Controls_Manager::HEADING,
							'separator' => 'before',
						) );
						$element->add_control( 'trx_addons_responsive_description', array(
							'raw' => __( "Theme-specific parameters - you can use them instead of the Elementor's parameters above.", 'trx_addons' ),
							'type' => \Elementor\Controls_Manager::RAW_HTML,
							'content_classes' => 'elementor-descriptor',
						) );
					}
					foreach ( $params as $p ) {
						$element->add_control( $p['name'], array_merge( $p, array(
																				'return_value' => $p['name'],
																				'prefix_class' => 'sc_layouts_',
						) ) );
					}
				}
			}
		}
	}
}
