/**
 * Shortcode Switcher
 *
 * @package ThemeREX Addons
 * @since v2.6.0
 */

/* global jQuery, TRX_ADDONS_STORAGE */


jQuery( document ).on( 'action.init_hidden_elements', function() {

	"use strict";

	jQuery( '.sc_switcher:not(.sc_switcher_inited)' ).each( function(nth) {

		var $self = jQuery( this ).addClass( 'sc_switcher_inited' ),
			$slider = $self.find( '.sc_switcher_slider' ),
			$sections = $self.find( '.sc_switcher_section' );

		// Type 'Default'
		if ( $self.hasClass( 'sc_switcher_default' ) ) {
			var $toggle = $self.find( '.sc_switcher_controls_toggle' );
			// Click on toggle
			$toggle.on( 'click', function() {
				sc_switcher_toggle_state(0);
			} );
			// Click on the left title
			$self.find('.sc_switcher_controls_section1').on( 'click', function() {
				sc_switcher_toggle_state(1);
			} );
			// Click on the right title
			$self.find('.sc_switcher_controls_section2').on( 'click', function() {
				sc_switcher_toggle_state(2);
			} );

		// Type 'Tabs'
		} else {
			var $tabs = $self.find( '.sc_switcher_tab' );
			$tabs.find( '.sc_switcher_tab_link' ).on( 'click', function( e ) {
				var $tab = jQuery( this ).parent(),
					idx = $tab.index();
				$tabs.removeClass( 'sc_switcher_tab_active' );
				$tab.addClass( 'sc_switcher_tab_active' );
				$sections
					.removeClass( 'sc_switcher_section_active' )
					.eq( idx ).addClass( 'sc_switcher_section_active' );
				$slider.get(0).style.setProperty( '--trx-addons-switcher-slide-active', idx );
				e.preventDefault();
				return false;
			} );
		}

		// Toggle state (for type 'Default')
		function sc_switcher_toggle_state( state ) {
			if ( $toggle.hasClass( 'sc_switcher_controls_toggle_on' ) ) {
				if ( state === 0 || state == 2 ) {
					$toggle.removeClass( 'sc_switcher_controls_toggle_on' );
					$sections.eq(0).removeClass( 'sc_switcher_section_active' );
					$sections.eq(1).addClass( 'sc_switcher_section_active' );
					//$slider.animate( { left: '50%' }, 300 );
					$slider.get(0).style.setProperty( '--trx-addons-switcher-slide-active', 1 );
				}
			} else {
				if ( state === 0 || state == 1 ) {
					$toggle.addClass( 'sc_switcher_controls_toggle_on' );
					$sections.eq(0).addClass( 'sc_switcher_section_active' );
					$sections.eq(1).removeClass( 'sc_switcher_section_active' );
					$slider.get(0).style.setProperty( '--trx-addons-switcher-slide-active', 0 );
				}				
			}
		}

	} );

} );