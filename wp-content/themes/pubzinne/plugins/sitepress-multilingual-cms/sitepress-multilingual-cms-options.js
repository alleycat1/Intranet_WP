/*
 * Touch the field 'wpml_current_language' to enable store it to the theme_mods in the Theme Options
 */

/* global jQuery:false */

jQuery( document ).ready(
	function() {
		"use strict";
		var fld = jQuery( 'input[name*="wpml_current_language"]' );
		if (fld.length > 0) {
			fld.val( fld.val() + '!' );
		}
	}
);
