/* global jQuery */

(function() {

	"use strict";

	var $window   = jQuery( window ),
		$document = jQuery( document ),
		$body     = jQuery( 'body' );

	var $images_scroll;

	// Update global values
	$document.on( 'action.resize_trx_addons', function() {
		images_scroll_detect_width();
	} );

	// Update links and values after the new post added
	$document.on( 'action.got_ajax_response', update_jquery_links );
	$document.on( 'action.init_hidden_elements', update_jquery_links );
	var first_run = true;
	function update_jquery_links(e) {
		if ( first_run && e && e.namespace == 'init_hidden_elements' ) {
			first_run = false;
			return; 
		}
		$images_scroll = jQuery('.trx_addons_image_scroll_direction_left,.trx_addons_image_scroll_direction_right');
		images_scroll_detect_width();
	}
	update_jquery_links();

	function images_scroll_detect_width() {
		$images_scroll.each( function() {
			var $img = jQuery(this);
			$img.get(0).style.setProperty( '--trx-addons-image-scroll-width', $img.width()+'px' );
		} );
	}

})();