<?php
/**
 * Elementor extension: Add new shapes to the parameter "Shape divider"
 *
 * @package ThemeREX Addons
 * @since v2.18.4
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

if ( ! function_exists('trx_addons_elm_add_new_shape_dividers')) {
	add_filter( 'elementor/shapes/additional_shapes', 'trx_addons_elm_add_new_shape_dividers' );
	/**
	 * Add new custom shapes to the widget "Shape divider". The shapes are stored in the folder 'css/shapes/'
	 *
	 * @hooked elementor/shapes/additional_shapes
	 *
	 * @param array $shapes List of shapes
	 * 
	 * @return array  	List of shapes with new shapes
	 */
	function trx_addons_elm_add_new_shape_dividers( $shapes ) {
		global $TRX_ADDONS_STORAGE;
		if ( ! empty( $TRX_ADDONS_STORAGE['shapes_list'] ) && is_array( $TRX_ADDONS_STORAGE['shapes_list'] ) ) {
			foreach( $TRX_ADDONS_STORAGE['shapes_list'] as $k => $shape ) {
				$shape_name = pathinfo( $shape, PATHINFO_FILENAME );
				$shapes[ "trx_addons_{$shape_name}" ] = array(
					'title' => ucfirst( str_replace( '_', ' ', $shape_name ) ),
					'has_negative' => false,
					'has_flip' => true,
					'url' => ! empty( $TRX_ADDONS_STORAGE['shapes_urls'][ $k ] ) ? $TRX_ADDONS_STORAGE['shapes_urls'][ $k ] : '',
					'path' => $shape
				);
			}
		}
		return $shapes;
	}
}
