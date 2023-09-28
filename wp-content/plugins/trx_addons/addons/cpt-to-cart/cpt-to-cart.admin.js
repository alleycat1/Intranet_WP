/* global jQuery */

jQuery(document).ready(function() {
	"use strict";
	
	var $options = jQuery( '.cpt_to_cart_options' );
	
	$options.find( 'tr[data-cpt_name]' ).each( function() {
		var $row = jQuery( this ),
			$popup = $row.find( '.cpt_to_cart_options_popup' );
		
		// Enable/disable button 'Options' on the checkbox 'Allow' is switched
		$row.find( '.cpt_to_cart_options_field_allow' ).on( 'change', function() {
			$row.find( '.cpt_to_cart_options_button_popup' ).get(0).disabled = ! jQuery(this).get(0).checked;
		} );

		// Show popup with options
		$row.find( '.cpt_to_cart_options_button_popup' ).on( 'click', function( e ) {
			e.preventDefault();
			$popup.addClass( 'cpt_to_cart_options_popup_opened' );
		} );
		// Hide popup with options
		$row.find( '.cpt_to_cart_options_popup_close,.cpt_to_cart_options_screen_shadow' ).on( 'click', function( e ) {
			e.preventDefault();
			$popup.removeClass( 'cpt_to_cart_options_popup_opened' );
			return false;
		} );

		// Add new listener
		$row.find( '.cpt_to_cart_options_popup_add_listener' ).on( 'click', function( e ) {
			var $self = jQuery( this ),
				html  = $self.data( 'group' ),
				idx   = $self.data( 'index' );
			$self.data( 'index', idx + 1 );
			$self.prev()
				.append( html.replace( /\[\-1\]/g, '[' + idx + ']') )
				.find( '.cpt_to_cart_options_popup_field:last-child' )
					.slideDown( function() {
						jQuery( this ).find( '.cpt_to_cart_options_popup_group_item:first-child select' ).get(0).focus();
					} );
		} );
		// Remove listener
		$row.find( '.cpt_to_cart_options_popup_event_listeners' ).on( 'click', '.cpt_to_cart_options_popup_remove_listener', function( e ) {
			jQuery( this ).parents( '.cpt_to_cart_options_popup_field' ).eq(0).slideUp( function() {
				jQuery( this ).remove();
			} );
		} );

		// Enable/disable field 'Place to' on the event type is changed
		$row.find( '.cpt_to_cart_options_popup_group_field_event_type select' ).on( 'change', function() {
			$row.find( '.cpt_to_cart_options_popup_group_field_place select' ).get(0).disabled = jQuery(this).find('option:selected').attr('value') != 'filter';
		} );

	} );
} );