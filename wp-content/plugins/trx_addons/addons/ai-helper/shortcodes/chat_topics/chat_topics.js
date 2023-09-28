/**
 * Shortcode AI Chat Topics
 *
 * @package ThemeREX Addons
 * @since v2.22.0
 */

/* global jQuery, TRX_ADDONS_STORAGE */


jQuery( document ).ready( function() {

	"use strict";

	var $window             = jQuery( window ),
		$document           = jQuery( document ),
		$body               = jQuery( 'body' );

	$document.on( 'action.init_hidden_elements', function(e, container) {

		if ( container === undefined ) {
			container = $body;
		}

		// Init AI Chat
		container.find( '.sc_chat_topics:not(.sc_chat_topics_inited)' ).each( function() {

			var $topics = jQuery( this ).addClass( 'sc_chat_topics_inited' ).find( '.sc_chat_topics_item > a' );

			if ( ! $topics.length ) {
				return;
			}

			var chat_id = $topics.eq(0).data( 'chat-id' ) || '',
				$chat_prompt = jQuery( ( chat_id ? '#' + chat_id + ' ' : '' ) + '.sc_chat_form_field_prompt_text' ).eq(0);
			
			if ( ! $chat_prompt.length ) {
				return;
			}

			$topics
				.on( 'keypress', function(e) {
					if ( e.keyCode == 13 ) {
						e.preventDefault();
						jQuery(this).trigger( 'click' );
					}
				} )
				.on( 'click', function(e) {
					$chat_prompt.val( jQuery(this).text() ).trigger( 'change' );
				} );

		} );

	} );

} );