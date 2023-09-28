/**
 * Shortcode AI Chat
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
		container.find( '.sc_chat:not(.sc_chat_inited)' ).each( function() {

			var $sc = jQuery( this ).addClass( 'sc_chat_inited' ),
				$form = $sc.find( '.sc_chat_form' ),
				$prompt = $sc.find( '.sc_chat_form_field_prompt_text' ),
				$button = $sc.find( '.sc_chat_form_field_prompt_button' ),
				$result = $sc.find( '.sc_chat_list' ),
				$start_new = $sc.find( '.sc_chat_form_start_new' ),
				chat = [],
				chat_position = 0;

			// Enable/disable button "Generate"
			$prompt.on( 'change keyup', function(e) {
				$button.toggleClass( 'sc_chat_form_field_prompt_button_disabled', $prompt.val() == '' );
			} )
			.trigger( 'change' );

			// Close a message popup on click on the close button
			$sc.on( 'click', '.sc_chat_message_close', function(e) {
				e.preventDefault();
				$form.find( '.sc_chat_message' ).slideUp();
				return false;
			} );

			// Start a new chat
			$start_new.on( 'click', function(e) {
				e.preventDefault();
				chat = [];
				chat_position = 0;
				$start_new.addClass( 'trx_addons_hidden' );
				$result.empty();
				$form.find( '.sc_chat_message' ).slideUp();
				$prompt.val( '' ).trigger( 'change' );
				$prompt.get(0).focus();
				return false;
			} );

			// Show previous/next message
			$prompt.on( 'keydown', function(e) {
				var i;
				if ( e.keyCode == 38 ) {
					e.preventDefault();
					if ( chat_position > 0 ) {
						for ( i = chat_position - 1; i >= 0; i-- ) {
							if ( chat[i].role == 'user' ) {
								$prompt.val( chat[i].content ).trigger( 'change' );
								chat_position = i;
								break;
							}
						}
					}
				} else if ( e.keyCode == 40 ) {
					e.preventDefault();
					if ( chat_position < chat.length - 1 ) {
						for ( i = chat_position + 1; i <= chat.length; i++ ) {
							if ( i == chat.length ) {
								$prompt.val( '' ).trigger( 'change' );
								chat_position = i;
								break;
							} else if ( chat[i].role == 'user' ) {
								$prompt.val( chat[i].content ).trigger( 'change' );
								chat_position = i;
								break;
							}
						}
					}
				}
			} );

			// Generate answer
			$prompt.on( 'keypress', function(e) {
				if ( e.keyCode == 13 ) {
					e.preventDefault();
					$button.trigger( 'click' );
				}
			} );
			$button.on( 'click', function(e) {
				e.preventDefault();
				var prompt = $prompt.val(),
					settings = $form.data( 'chat-settings' );

				if ( ! prompt || ! checkLimits() ) {
					return;
				}

				// Add prompt to the chat
				add_to_chat( prompt, 'user' );

				// Display loading animation
				show_loading();

				// Display the link "Start new chat"
				$start_new.removeClass( 'trx_addons_hidden' );

				// Send request via AJAX
				jQuery.post( TRX_ADDONS_STORAGE['ajax_url'], {
					nonce: TRX_ADDONS_STORAGE['ajax_nonce'],
					action: 'trx_addons_ai_helper_chat',
					count: ( trx_addons_get_cookie( 'trx_addons_ai_helper_chat_count' ) || 0 ) * 1 + 1,
					chat: JSON.stringify( chat ),
					settings: settings,
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

					// Hide loading animation
					hide_loading();
					
					// Show result
					$prompt.focus();
					if ( ! rez.error && rez.data.text ) {
						add_to_chat( rez.data.text, 'assistant' );
						$prompt.val( '' ).trigger( 'change' );
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

			// Return a layout of the chat list item
			function get_chat_list_item( message, role ) {
				var dt = new Date(),
					hours = dt.getHours(),
					minutes = dt.getMinutes() < 10 ? '0' + dt.getMinutes() : dt.getMinutes(),
					am = hours < 12 ? 'AM' : 'PM',
					use_am = trx_addons_apply_filters( 'trx_addons_filter_sc_chat_time_use_am', true ),
					hours = use_am && hours > 12 ? hours - 12 : hours;
				return trx_addons_apply_filters(
					'trx_addons_filter_sc_chat_list_item',
					'<li class="sc_chat_list_item sc_chat_list_item_' + role + '">'
						+ '<span class="sc_chat_list_item_wrap">'
							+ '<span class="sc_chat_list_item_content">' + message + '</span>'
							+ ( role != 'loading' ? '<span class="sc_chat_list_item_time">' + hours + ':' + minutes + ( use_am ? ' ' + am : '' ) + '</span>' : '' )
						+ '</span>'
					+ '</li>',
					message, role
				);
			}

			// Add a new message to the chat and display it
			function add_to_chat( message, role ) {
				// Add to the list of messages
				if ( chat.length === 0 || chat[chat.length-1].role != role || chat[chat.length-1].content != message ) {
					chat.push( {
						'role': role,
						'content': message
					} );
					chat_position = chat.length;
					// Display message
					$result.append( get_chat_list_item( message, role ) );
					// Display chat if it's hidden
					if ( chat.length == 1 ) {
						$result.parent().slideDown( function() {
							scroll_to_bottom();
						});
					} else {
						scroll_to_bottom();
					}
				}
			}

			// Show loading animation
			function show_loading() {
				$form.addClass( 'sc_chat_form_loading' );
				// Add loading animation to the chat
				$result.append( get_chat_list_item( '<span class="sc_chat_list_item_loading_dot"></span><span class="sc_chat_list_item_loading_dot"></span><span class="sc_chat_list_item_loading_dot"></span>', 'loading' ) );
				// Scroll chat to the bottom
				scroll_to_bottom();
			}

			// Hide loading animation
			function hide_loading() {
				$form.removeClass( 'sc_chat_form_loading' );
				$result.find( '.sc_chat_list_item_loading' ).remove();
			}

			// Scroll the chat to the bottom
			function scroll_to_bottom() {
				$result.parent().animate( { scrollTop: $result.parent().prop( 'scrollHeight' ) }, 500 );
			}

			// Show message
			function showMessage( msg, type ) {
				$form
					.find( '.sc_chat_message_inner' )
						.html( msg )
						.parent()
							.toggleClass( 'sc_chat_message_type_error', type == 'error' )
							.toggleClass( 'sc_chat_message_type_info', type == 'info' )
							.toggleClass( 'sc_chat_message_type_success', type == 'success' )
							.addClass( 'sc_chat_message_show' )
							.slideDown();
			}

			// Check limits for generation images
			function checkLimits() {
				// Block the button if the limits are exceeded
				var total, used;
				// Check limits for the generation requests from all users
				var $limit_total = $form.find( '.sc_chat_limits_total_value' ),
					$limit_used  = $form.find( '.sc_chat_limits_used_value' );
				if ( $limit_total.length && $limit_used.length ) {
					total = parseInt( $limit_total.text(), 10 );
					used  = parseInt( $limit_used.text(), 10 );
					if ( ! isNaN( total ) && ! isNaN( used ) ) {
						if ( used >= total ) {
							$button.toggleClass( 'sc_chat_form_field_prompt_button_disabled', true );
							$prompt.attr( 'disabled', 'disabled' );
							showMessage( $form.data( 'chat-limit-exceed' ), 'error' );
							return false;
						}
					}
				}
				// Check limits for the generation requests from the current user
				var $requests_total = $form.find( '.sc_chat_limits_total_requests' ),
					$requests_used  = $form.find( '.sc_chat_limits_used_requests' );
				if ( $requests_total.length && $requests_used.length ) {
					total = parseInt( $requests_total.text(), 10 );
					//used  = parseInt( $requests_used.text(), 10 );
					used = ( trx_addons_get_cookie( 'trx_addons_ai_helper_chat_count' ) || 0 ) * 1;
					if ( ! isNaN( total ) && ! isNaN( used ) ) {
						if ( used >= total ) {
							$button.toggleClass( 'sc_chat_form_field_prompt_button_disabled', true );
							$prompt.attr( 'disabled', 'disabled' );
							showMessage( $form.data( 'chat-limit-exceed' ), 'error' );
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
				var $limit_total = $form.find( '.sc_chat_limits_total_value' ),
					$limit_used  = $form.find( '.sc_chat_limits_used_value' );
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
				var $requests_total = $form.find( '.sc_chat_limits_total_requests' ),
					$requests_used  = $form.find( '.sc_chat_limits_used_requests' );
				if ( $requests_total.length && $requests_used.length ) {
					total = parseInt( $requests_total.text(), 10 );
					// used  = parseInt( $requests_used.text(), 10 );
					used = ( trx_addons_get_cookie( 'trx_addons_ai_helper_chat_count' ) || 0 ) * 1;
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
				var count = trx_addons_get_cookie( 'trx_addons_ai_helper_chat_count' ) || 0,
					limit = 60 * 60 * 1000 * 1,	// 1 hour
					expired = limit - ( new Date().getTime() % limit );

				trx_addons_set_cookie( 'trx_addons_ai_helper_chat_count', ++count, expired );
			}


		} );

	} );

} );