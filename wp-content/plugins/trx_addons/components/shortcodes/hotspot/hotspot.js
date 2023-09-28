/**
 * Shortcode Hotspot
 *
 * @package ThemeREX Addons
 * @since v1.94.0
 */

/* global jQuery, TRX_ADDONS_STORAGE */


jQuery( document ).on( 'action.init_hidden_elements', function() {

	"use strict";

	jQuery( '.sc_hotspot:not(.sc_hotspot_inited)' ).each( function() {

		var $self  = jQuery( this ).addClass( 'sc_hotspot_inited' ),
			$image = $self.find( '.sc_hotspot_image' );

		// Show hotspots after image loaded
		if ( ! $image.hasClass( 'sc_hotspot_image_loaded' ) ) {
			trx_addons_when_images_loaded( $image, function() {
				$image.addClass( 'sc_hotspot_image_loaded' );
			} );
		}

		// Hide all opened popups
		function sc_hotspot_hide_all_popups() {
			$self.find( '.sc_hotspot_item_opened' ).removeClass( 'sc_hotspot_item_opened' );
		}

		// Hotspot hovered
		$self.find('.sc_hotspot_item_open_hover')
			// Hide other popups on hotspot hovered
			.on( 'click mouseenter touchstart', '.sc_hotspot_item_icon', function(e) {
				sc_hotspot_hide_all_popups();
			} );

		// Add class on hotspot hovered
		$self.find('.sc_hotspot_item')
			.on( 'mouseenter', function(e) {
				jQuery( this ).addClass( 'sc_hotspot_item_hovered' );
			} )
			.on( 'mouseleave', function(e) {
				jQuery( this ).removeClass( 'sc_hotspot_item_hovered' );
			} );

		// Open popup on hotspot clicked
		$self.find('.sc_hotspot_item').on( 'click', '.sc_hotspot_item_icon', function(e) {
			var $item = jQuery( this ).parents( '.sc_hotspot_item' ),
				opened = $item.hasClass( 'sc_hotspot_item_opened' );
			if ( $item.hasClass( 'sc_hotspot_item_open_click' ) || screen.width < 1280 ) {
				sc_hotspot_hide_all_popups();
				if ( ! opened ) {
					$item.addClass( 'sc_hotspot_item_opened' );
				}
			}
			e.preventDefault();
			return false;
		} );

		// Close popup on close button clicked
		$self.find('.sc_hotspot_item_popup_close').on( 'click', function(e) {
			sc_hotspot_hide_all_popups();
			e.preventDefault();
			return false;
		} );

		// Hide popups on image click
		$image.on( 'click', function(e) {
			if ( e.target && jQuery( e.target ).hasClass( 'sc_hotspot_image' ) ) {
				sc_hotspot_hide_all_popups();
			}
		} );

	} );

} );