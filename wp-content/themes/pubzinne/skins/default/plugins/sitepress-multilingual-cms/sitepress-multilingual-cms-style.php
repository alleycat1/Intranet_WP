<?php
// Add plugin-specific colors and fonts to the custom CSS
if ( ! function_exists( 'pubzinne_wpml_get_css' ) ) {
	add_filter( 'pubzinne_filter_get_css', 'pubzinne_wpml_get_css', 10, 2 );
	function pubzinne_wpml_get_css( $css, $args ) {
		return $css;
	}
}

