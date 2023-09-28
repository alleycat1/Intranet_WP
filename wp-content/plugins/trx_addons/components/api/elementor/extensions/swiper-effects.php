<?php
/**
 * Elementor extension: Enable new effects for slider if an experiment "Latest Swiper" is active
 *
 * @package ThemeREX Addons
 * @since v2.19.0
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

if ( ! function_exists( 'trx_addons_elm_add_effects_to_slider' ) ) {
	add_filter( 'trx_addons_filter_get_list_sc_slider_effects', 'trx_addons_elm_add_effects_to_slider' );
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
	function trx_addons_elm_add_effects_to_slider( $list ) {
		if ( trx_addons_elm_is_experiment_active( 'e_swiper_latest' ) ) {
			$list['cards']    = esc_html__( 'Cards', 'trx_addons' );
//			$list['creative'] = esc_html__( 'Creative', 'trx_addons' );
		}
		return $list;
	}
}
