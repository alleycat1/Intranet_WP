/*
 * Touch the field 'wpml_current_language' to enable store it to the theme_mods in the Customizer
 */

/* global jQuery:false */

( function( api ) {

	"use strict";

	// Set initial state of controls
	api.bind( 'ready', pubzinne_wpml_touch_language );
	api.bind( 'change', pubzinne_wpml_touch_language );

	function pubzinne_wpml_touch_language(obj) {
		if (typeof obj != 'undefined' && typeof obj.id != 'undefined' && obj.id == 'wpml_current_language') {
			return;
		}
		var fld = jQuery( '[data-customize-setting-link="wpml_current_language"]' );
		if (fld.length > 0) {
			var val = fld.val().split( '!' );
			val[1]  = (val.length > 1 ? Number( val[1] ) + 1 : 1);
			fld.val( val.join( '!' ) ).trigger( 'change' );
		}
	}

} )( wp.customize );
