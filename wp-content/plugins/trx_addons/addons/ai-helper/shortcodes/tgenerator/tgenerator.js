/**
 * Shortcode TGenerator - Generate texts with AI
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

		// Init TGenerator
		container.find( '.sc_tgenerator:not(.sc_tgenerator_inited)' ).each( function() {

			var $sc = jQuery( this ).addClass( 'sc_tgenerator_inited' ),
				$form = $sc.find( '.sc_tgenerator_form' ),
				$prompt = $sc.find( '.sc_tgenerator_form_field_prompt_text' ),
				$button = $sc.find( '.sc_tgenerator_form_field_prompt_button' ),
				$write = $sc.find( '.sc_tgenerator_form_field_write' ),
				$process = $sc.find( '.sc_tgenerator_form_field_process' ),
				$tone = $sc.find( '.sc_tgenerator_form_field_tone' ),
				$language = $sc.find( '.sc_tgenerator_form_field_language' ),
				$label_to = $sc.find( '.sc_tgenerator_form_field_tags_label.sc_tgenerator_form_field_hidden' ),
				$text = $sc.find( '.sc_tgenerator_text' ),
				$result = $sc.find( '.sc_tgenerator_result' );

			// Open/close select
			$sc.find( '.sc_tgenerator_form_field_select_label' )
				.on( 'click', function(e) {
					var $select = jQuery( this ).parent(),
						opened  = $select.hasClass( 'sc_tgenerator_form_field_select_opened' );
					$select.parent().find( '.sc_tgenerator_form_field_select' ).removeClass( 'sc_tgenerator_form_field_select_opened' );
					$select.toggleClass( 'sc_tgenerator_form_field_select_opened', ! opened );
					$select.find( '.sc_tgenerator_form_field_select_option:first-child' ).get(0).focus();
				} )
				.on( 'keypress', function(e) {
					if ( e.keyCode == 13 ) {
						jQuery( this ).trigger( 'click' );
					}
				} );

			// Close select on click outside
			$document.on( 'click', function(e) {
				if ( jQuery( e.target ).parents( '.sc_tgenerator_form_field_select' ).length === 0 ) {
					$sc.find( '.sc_tgenerator_form_field_select_opened .sc_tgenerator_form_field_select_label' ).trigger( 'click' );
				}
			} );

			// Select option via click or keyboard
			$sc.find( '.sc_tgenerator_form_field_select_option' )
				.on( 'click', function(e) {
					var $self = jQuery( this ),
						value = $self.data( 'value' ),
						prompt = $self.data( 'prompt' ) || '',
						variations = $self.data( 'variations' ) || '',
						$select = $self.parents( '.sc_tgenerator_form_field_select' ),
						$label = $select.find( '.sc_tgenerator_form_field_select_label' );
					if ( prompt ) {
						$prompt.val( prompt ).trigger( 'change' );
					}
					$select
						.removeClass( 'sc_tgenerator_form_field_select_opened' )
						.data( 'value', value )
						.data( 'prompt', prompt )
						.data( 'variations', variations )
						.trigger( 'change' );
					$label.html( $self.html() );
					if ( value ) {
						if ( $select.is( $write ) ) {
							$process.find( '.sc_tgenerator_form_field_select_option:first-child' ).trigger( 'click' );
						} else if ( $select.is( $process ) ) {
							$write.find( '.sc_tgenerator_form_field_select_option:first-child' ).trigger( 'click' );
						}
					}
					$label.get(0).focus();
				} )
				.on( 'keydown', function(e) {
					var $self = jQuery( this ),
						processed = false;
					if ( e.keyCode == 13 ) {
						$self.trigger( 'click' );
						processed = true;
					} else if ( e.keyCode == 27 ) {
						$self.parents( '.sc_tgenerator_form_field_select' ).removeClass( 'sc_tgenerator_form_field_select_opened' );
						processed = true;
					} else if ( e.keyCode == 38 ) {
						if ( $self.index() > 0) {
							$self.prev().focus();
						}
						processed = true;
					} else if ( e.keyCode == 40 ) {
						if ( $self.index() < $self.parent().children().length - 1 ) {
							$self.next().focus();
						}
						processed = true;
					}
					if ( processed ) {
						e.preventDefault();
						return false;
					}
					return true;
				} );

			// Change prompt on "Tone" or "Language" change
			$tone.on( 'change', function(e) {
				var value = jQuery( this ).data( 'value' );
				$prompt.val( $process.find( '[data-value="process_tone"]' ).data( 'prompt' ).replace( '%tone%', value ) ).trigger( 'change' );
			} );
			$language.on( 'change', function(e) {
				var value = jQuery( this ).data( 'value' );
				$prompt.val( $process.find( '[data-value="process_translate"]' ).data( 'prompt' ).replace( '%language%', value ) ).trigger( 'change' );
			} );

			// Change fields visibility on "Write" or "Process" change
			$process.on( 'change', function(e) {
				var value = jQuery( this ).data( 'value' );
				$tone.toggleClass( 'sc_tgenerator_form_field_visible', value == 'process_tone' );
				if ( value == 'process_tone' ) {
					$tone.trigger( 'change' );
				}
				$language.toggleClass( 'sc_tgenerator_form_field_visible', value == 'process_translate' );
				if ( value == 'process_translate' ) {

					$language.trigger( 'change' );
				}
				$label_to.toggleClass( 'sc_tgenerator_form_field_visible', ['process_tone', 'process_translate'].indexOf( value ) >= 0 );
				$text.toggleClass( 'sc_tgenerator_form_field_visible', value != '' );
			} );

			// Copy text to clipboard
			$result.find( '.sc_tgenerator_result_copy .sc_button' ).on( 'click', function(e) {
				var $self = jQuery( this );
				e.preventDefault();
				trx_addons_copy_to_clipboard( $result.find( '.sc_tgenerator_result_content' ), true );	// true - strip tags from the text
				$self.addClass( 'sc_button_copied' );
				setTimeout( function() {
					$self.removeClass( 'sc_button_copied' );
				}, 3000 );
				return false;
			} );

			// Close a message popup on click on the close button
			$sc.on( 'click', '.sc_tgenerator_message_close', function(e) {
				e.preventDefault();
				$form.find( '.sc_tgenerator_message' ).slideUp();
				return false;
			} );

			// Enable/disable button "Generate"
			$prompt.on( 'change keyup', function(e) {
				$button.toggleClass( 'sc_tgenerator_form_field_prompt_button_disabled', $prompt.val() == '' );
			} )
			.trigger( 'change' );

			// Generate text
			$prompt.on( 'keypress', function(e) {
				if ( e.keyCode == 13 ) {
					e.preventDefault();
					$button.trigger( 'click' );
				}
			} );
			$button.on( 'click', function(e) {
				e.preventDefault();
				var prompt = $prompt.val(),
					settings = $form.data( 'tgenerator-settings' );

				if ( ! prompt || ! checkLimits() ) {
					return;
				}

				$result.hide();

				$form.addClass( 'sc_tgenerator_form_loading' );

				// Send request via AJAX
				jQuery.post( TRX_ADDONS_STORAGE['ajax_url'], {
					nonce: TRX_ADDONS_STORAGE['ajax_nonce'],
					action: 'trx_addons_ai_helper_tgenerator',
					prompt: prompt,
					count: ( trx_addons_get_cookie( 'trx_addons_ai_helper_tgenerator_count' ) || 0 ) * 1 + 1,
					command: $write.data( 'value' ) || $process.data( 'value' ),
					language: $language.data( 'value' ),
					tone: $tone.data( 'value' ),
					content: $text.val(),
					settings: settings
				}, function( response ) {
					// Prepare response
					var rez = {};
					if ( response == '' || response == 0 ) {
						rez = { error: TRX_ADDONS_STORAGE['msg_ai_helper_error'] };
					} else if ( typeof response == 'string' ) {
						try {
							rez = JSON.parse( response );
						} catch (e) {
							rez = { error: TRX_ADDONS_STORAGE['msg_ai_helper_error'] };
							console.log( response );
						}
					} else {
						rez = response;
					}

					$form.removeClass( 'sc_tgenerator_form_loading' );

					// Show result
					if ( ! rez.error && rez.data.text ) {
						$result.find( '.sc_tgenerator_result_content' ).html( typeof rez.data.text == 'object' ? rez.data.text.join( '<br>' ) : rez.data.text );
						$result.fadeIn();
						updateLimitsCounter();
						updateRequestsCounter();
					}
					if ( rez.error ) {
						showMessage( rez.error, 'error' );
					} else if ( rez.data.message ) {
						showMessage( rez.data.message, 'info' );
					}
				} );
			} );

			// Set padding for the prompt field to avoid overlapping the button
			if ( $button.css( 'position' ) == 'absolute' ) {
				var set_prompt_padding = ( function() {
					$prompt.css( 'padding-right', ( Math.ceil( $button.outerWidth() ) + 10 ) + 'px' );
				} )();
				$window.on( 'resize', set_prompt_padding );
			}

			// Show message
			function showMessage( msg, type ) {
				$form
					.find( '.sc_tgenerator_message_inner' )
						.html( msg )
						.parent()
							.toggleClass( 'sc_tgenerator_message_type_error', type == 'error' )
							.toggleClass( 'sc_tgenerator_message_type_info', type == 'info' )
							.toggleClass( 'sc_tgenerator_message_type_success', type == 'success' )
							.addClass( 'sc_tgenerator_message_show' )
							.slideDown();
			}

			// Check limits for generation images
			function checkLimits() {
				// Block the button if the limits are exceeded
				var total, used;
				// Check limits for the generation requests from all users
				var $limit_total = $form.find( '.sc_tgenerator_limits_total_value' ),
					$limit_used  = $form.find( '.sc_tgenerator_limits_used_value' );
				if ( $limit_total.length && $limit_used.length ) {
					total = parseInt( $limit_total.text(), 10 );
					used  = parseInt( $limit_used.text(), 10 );
					if ( ! isNaN( total ) && ! isNaN( used ) ) {
						if ( used >= total ) {
							$button.toggleClass( 'sc_tgenerator_form_field_prompt_button_disabled', true );
							$prompt.attr( 'disabled', 'disabled' );
							showMessage( $form.data( 'tgenerator-limit-exceed' ), 'error' );
							return false;
						}
					}
				}
				// Check limits for the generation requests from the current user
				var $requests_total = $form.find( '.sc_tgenerator_limits_total_requests' ),
					$requests_used  = $form.find( '.sc_tgenerator_limits_used_requests' );
				if ( $requests_total.length && $requests_used.length ) {
					total = parseInt( $requests_total.text(), 10 );
					//used  = parseInt( $requests_used.text(), 10 );
					used = ( trx_addons_get_cookie( 'trx_addons_ai_helper_tgenerator_count' ) || 0 ) * 1;
					if ( ! isNaN( total ) && ! isNaN( used ) ) {
						if ( used >= total ) {
							$button.toggleClass( 'sc_tgenerator_form_field_prompt_button_disabled', true );
							$prompt.attr( 'disabled', 'disabled' );
							showMessage( $form.data( 'tgenerator-limit-exceed' ), 'error' );
							return false;
						}
					}
				}
				return true;
			}
			
			// Update a counter of requests inside a limits text
			function updateLimitsCounter() {
				var total, used;
				// Update a counter of the total requests
				var $limit_total = $form.find( '.sc_tgenerator_limits_total_value' ),
					$limit_used  = $form.find( '.sc_tgenerator_limits_used_value' );
				if ( $limit_total.length && $limit_used.length ) {
					total = parseInt( $limit_total.text(), 10 );
					used  = parseInt( $limit_used.text(), 10 );
					if ( ! isNaN( total ) && ! isNaN( used ) ) {
						if ( used < total ) {
							used = Math.min( used + 1, total );
							$limit_used.text( used );
						}
					}
				}
				// Update a counter of the user requests
				var $requests_total = $form.find( '.sc_tgenerator_limits_total_requests' ),
					$requests_used  = $form.find( '.sc_tgenerator_limits_used_requests' );
				if ( $requests_total.length && $requests_used.length ) {
					total = parseInt( $requests_total.text(), 10 );
					// used  = parseInt( $requests_used.text(), 10 );
					used = ( trx_addons_get_cookie( 'trx_addons_ai_helper_tgenerator_count' ) || 0 ) * 1;
					if ( ! isNaN( total ) && ! isNaN( used ) ) {
						if ( used < total ) {
							used = Math.min( used + 1, total );
							$requests_used.text( used );
						}
					}
				}
			}

			// Update a counter of the generation requests
			function updateRequestsCounter() {
				// Save a number of requests to the client storage
				var count = trx_addons_get_cookie( 'trx_addons_ai_helper_tgenerator_count' ) || 0,
					limit = 60 * 60 * 1000 * 1,	// 1 hour
					expired = limit - ( new Date().getTime() % limit );

				trx_addons_set_cookie( 'trx_addons_ai_helper_tgenerator_count', ++count, expired );
			}

		} );

	} );

} );
