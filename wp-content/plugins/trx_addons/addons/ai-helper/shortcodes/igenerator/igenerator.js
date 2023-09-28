/**
 * Shortcode IGenerator - Generate images with AI
 *
 * @package ThemeREX Addons
 * @since v2.20.2
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

		var animation_out = trx_addons_apply_filters( 'trx_addons_filter_sc_igenerator_animation_out', 'fadeOutDownSmall animated normal' ),
			animation_in = trx_addons_apply_filters( 'trx_addons_filter_sc_igenerator_animation_in', 'fadeInUpSmall animated normal' );

		// Init IGenerator
		container.find( '.sc_igenerator:not(.sc_igenerator_inited)' ).each( function() {

			var $sc = jQuery( this ).addClass( 'sc_igenerator_inited' ),
				$form = $sc.find( '.sc_igenerator_form' ),
				$prompt = $sc.find( '.sc_igenerator_form_field_prompt_text' ),
				$button = $sc.find( '.sc_igenerator_form_field_prompt_button' ),
				$settings = $sc.find( '.sc_igenerator_form_settings' ),
				$settings_button = $sc.find( '.sc_igenerator_form_settings_button' ),
				settings_light = ! $settings.hasClass( 'sc_igenerator_form_settings_full' ),
				$model = $settings.find( '[name="sc_igenerator_form_settings_field_model"]' ),
				$style = $settings.find( '[name="sc_igenerator_form_settings_field_style"]' ),
				$size = $settings.find( '[name="sc_igenerator_form_settings_field_size"]' ),
				$width = $settings.find( '[name="sc_igenerator_form_settings_field_width"]' ),
				$height = $settings.find( '[name="sc_igenerator_form_settings_field_height"]' ),
				$preview = $sc.find( '.sc_igenerator_images' ),
				fetch_img = '';

			// Show/hide settings popup
			$settings_button.on( 'click', function(e) {
				e.preventDefault();
				$settings.toggleClass( 'sc_igenerator_form_settings_show' );
				return false;
			} );
			// Hide popup on click outside
			$document.on( 'click', function(e) {
				if ( $settings.hasClass( 'sc_igenerator_form_settings_show' ) && ! jQuery( e.target ).closest( '.sc_igenerator_form_settings' ).length ) {
					$settings.removeClass( 'sc_igenerator_form_settings_show' );
				}
			} );
			// Hide popup on a model selected by click (not by arrow keys) if settings are in the light mode (single field with model selector only)
			if ( settings_light ) {
				$model.on( 'click', function(e) {
					setTimeout( function() {
						$settings.removeClass( 'sc_igenerator_form_settings_show' );
					}, 200 );
				} );
			}

			// Show/hide a select 'Style' on change the model
			if ( ! settings_light ) {
				$model.on( 'change', function(e) {
					var model = $model.val();
					$style.parents( '.sc_igenerator_form_settings_field' ).toggleClass( 'trx_addons_hidden', model.indexOf( 'stability-ai/' ) < 0 );
				} );
			}

			// Show/hide options in the field 'size' on change the model
			if ( ! settings_light ) {
				$model.on( 'change', function(e) {
					var model = $model.val();
					$size.find( 'option' ).each( function() {
						var $option = jQuery( this ),
							val = $option.val();
						$option.toggleClass( 'trx_addons_hidden', model.indexOf( 'openai/' ) >= 0 && ! TRX_ADDONS_STORAGE['ai_helper_sc_igenerator_openai_sizes'][ val ] );
						if ( $option.is( ':selected' ) && $option.hasClass( 'trx_addons_hidden' ) ) {
							$size.val( '256x256' ).trigger( 'change' );
						}
					} );
				} );
			}

			// Show/hide fields 'width' and 'height' on change the 'size' field value to the 'custom'
			if ( ! settings_light ) {
				$size.on( 'change', function() {
					$width.parents( '.sc_igenerator_form_settings_field' ).toggleClass( 'trx_addons_hidden', $size.val() != 'custom' );
					$height.parents( '.sc_igenerator_form_settings_field' ).toggleClass( 'trx_addons_hidden', $size.val() != 'custom' );
				} ).trigger( 'change' );
			}

			// Inc/Dec the 'width' and 'height' fields on click on the arrows
			if ( ! settings_light ) {
				$settings
					.find( '.sc_igenerator_form_settings_field_numeric_wrap_button_inc,.sc_igenerator_form_settings_field_numeric_wrap_button_dec' )
						.on( 'click', function(e) {
							e.preventDefault();
							var $self = jQuery( this ),
								$field = $self.parents( '.sc_igenerator_form_settings_field' ),
								$input = $field.find( 'input' ),
								val = $input.val() || 0,
								step = $input.attr( 'step' ) || 1,
								min = $input.attr( 'min' ) || 0,
								max = $input.attr( 'max' ) || 1024;
							if ( $self.hasClass( 'sc_igenerator_form_settings_field_numeric_wrap_button_inc' ) ) {
								val = Math.min( max * 1, val * 1 + step * 1 );
							} else {
								val = Math.max( min * 1, val * 1 - step * 1 );
							}
							$input.val( val ).trigger( 'change' );
							return false;
						} );
			}

			// Change the prompt text on click on the tag
			$sc.on( 'click', '.sc_igenerator_form_field_tags_item,.sc_igenerator_message_translation', function(e) {
				e.preventDefault();
				if ( ! $prompt.attr( 'disabled' ) ) {
					$prompt.val( jQuery( this ).data( 'tag-prompt' ) ).trigger( 'change' );
				}
				return false;
			} );

			// Close a message popup on click on the close button
			$sc.on( 'click', '.sc_igenerator_message_close', function(e) {
				e.preventDefault();
				$form.find( '.sc_igenerator_message' ).slideUp();
				return false;
			} );

			// Enable/disable the button on change the prompt text
			$prompt.on( 'change keyup', function(e) {
				$button.toggleClass( 'sc_igenerator_form_field_prompt_button_disabled', $prompt.attr( 'disabled' ) == 'disabled' || $prompt.val() == '' );
			} )
			.trigger( 'change' );

			// Trigger the button on Enter key
			$prompt.on( 'keydown', function(e) {
				if ( e.keyCode == 13 ) {
					e.preventDefault();
					$button.trigger( 'click' );
					return false;
				}
			} );

			// Send request via AJAX to generate images
			$button.on( 'click', function(e) {
				e.preventDefault();
				var prompt = $prompt.val(),
					model = settings_light ? $model.filter(':checked').val() : $model.val(),
					settings = $form.data( 'igenerator-settings' );

				if ( ! prompt || ! checkLimits() ) {
					return;
				}

				$form.addClass( 'sc_igenerator_form_loading' );

				// Send request via AJAX
				var data = {
					nonce: TRX_ADDONS_STORAGE['ajax_nonce'],
					action: 'trx_addons_ai_helper_igenerator',
					settings: settings,
					prompt: prompt,
					model: model,
					count: ( trx_addons_get_cookie( 'trx_addons_ai_helper_igenerator_count' ) || 0 ) * 1 + 1
				};
				if ( ! settings_light ) {
					data.size = $size.val();
					if ( data.size == 'custom' ) {
						data.width = $width.val();
						data.height = $height.val();
					}
					data.style = $style.val();
				}
				jQuery.post( TRX_ADDONS_STORAGE['ajax_url'], data, function( response ) {
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

					$form.removeClass( 'sc_igenerator_form_loading' );

					// Show images
					if ( ! rez.error && rez.data ) {
						var i = 0;
						// If need to fetch images after timeout
						if ( rez.data.fetch_id ) {
							for ( i = 0; i < rez.data.fetch_number; i++ ) {
								rez.data.images.push( {
									url: rez.data.fetch_img
								} );
							}
							if ( ! fetch_img ) {
								fetch_img = rez.data.fetch_img;
							}
							var time = rez.data.fetch_time ? rez.data.fetch_time : 2000;
							setTimeout( function() {
								fetchImages( rez.data );
							}, time );
						}
						if ( rez.data.images.length > 0 ) {
							if ( ! rez.data.demo ) {
								updateLimitsCounter( rez.data.images.length );
								updateRequestsCounter();
							}
							var $images = $preview.find( '.sc_igenerator_image' );
							if ( animation_in || animation_out ) {
								$preview.css( {
									'height': $images.length ? $preview.height() + 'px' : '36vh',
								} );
							}
							if ( ! $images.length ) {
								$preview.show();
							} else if ( animation_out ) {
								$images.removeClass( animation_in ).addClass( animation_out );
							}
							setTimeout( function() {
								var currentDate = new Date();
								var timestamp = currentDate.getTime();
								var html = '<div class="sc_igenerator_columns_wrap sc_item_columns '
												+ TRX_ADDONS_STORAGE['columns_wrap_class']
												+ ' columns_padding_bottom'
												+ ( rez.data.columns >= rez.data.number ? ' ' + TRX_ADDONS_STORAGE['columns_in_single_row_class'] : '' )
												+ '">';
								for ( var i = 0; i < rez.data.images.length; i++ ) {
									html += '<div class="sc_igenerator_image ' + trx_addons_get_column_class( 1, rez.data.columns, rez.data.columns_tablet, rez.data.columns_mobile )
												+ ( rez.data.fetch_id ? ' sc_igenerator_image_fetch' : '' )
												+ ( animation_in ? ' ' + animation_in : '' )
											+ '">'
												+ '<div class="sc_igenerator_image_inner">'
													+ '<img src="' + rez.data.images[i].url + '" alt=""' + ( rez.data.fetch_id ? ' id="fetch-' + rez.data.fetch_id + '"' : '' ) + '>'
													+ ( rez.data.fetch_id
														? '<span class="sc_igenerator_image_fetch_info">'
																+ '<span class="sc_igenerator_image_fetch_msg">' + rez.data.fetch_msg + '</span>'
																+ '<span class="sc_igenerator_image_fetch_progress">'
																	+ '<span class="sc_igenerator_image_fetch_progressbar"></span>'
																+ '</span>'
															+ '</span>'
														: ''
														)
													+ ( ! rez.data.demo && rez.data.show_download
														? '<a href="' + getDownloadLink( rez.data.images[i].url ) + '"'
															+ ' download="' + prompt.replace( /[\s]+/g, '-' ).toLowerCase() + '"'
															+ ' data-expired="' + ( ( rez.data.fetch_id ? 0 : timestamp ) + rez.data.show_download * 1000 ) + '"'
															//+ ' target="_blank"'
															+ ' class="sc_igenerator_image_link sc_button sc_button_default sc_button_size_small sc_button_with_icon sc_button_icon_left"'
															+ ' data-elementor-open-lightbox="no"'
															+ '>'
																+ '<span class="sc_button_icon"><span class="trx_addons_icon-download"></span></span>'
																+ '<span class="sc_button_text"><span class="sc_button_title">' + TRX_ADDONS_STORAGE['msg_ai_helper_download'] + '</span></span>'
															+ '</a>'
														: ''
														)
												+ '</div>'
											+ '</div>';
								}
								html += '</div>';
								$preview.html( html );
								setTimeout( function() {
									$preview.css( 'height', 'auto' );
									prepareImagesForPopup();
								}, animation_in ? 700 : 0 );
								// Check if download links are expired
								$preview.find( '.sc_igenerator_image_link' ).on( 'click', function( e ) {
									var currentDate = new Date();
									var timestamp = currentDate.getTime();
									var $link = jQuery( this );
									if ( $link.attr( 'data-expired' ) && parseInt( $link.attr( 'data-expired' ), 10 ) < timestamp ) {
										e.preventDefault();
										if ( typeof trx_addons_msgbox_warning == 'function' ) {
											trx_addons_msgbox_warning(
												TRX_ADDONS_STORAGE['msg_ai_helper_download_expired'],
												TRX_ADDONS_STORAGE['msg_ai_helper_download_error'],
												'attention',
												0,
												[ TRX_ADDONS_STORAGE['msg_caption_ok'] ]
											);
										} else {
											//alert( TRX_ADDONS_STORAGE['msg_ai_helper_download_expired'].replace( /<br>/g, "\n" ) );
											showMessage( TRX_ADDONS_STORAGE['msg_ai_helper_download_expired'], 'error' );
										}
										return false;
									}
								} );
							}, $images.length && animation_out ? 700 : 0 );
						}
						if ( rez.data.message ) {
							showMessage( rez.data.message, rez.data.message_type );
						}
					} else {
						if ( typeof trx_addons_msgbox_warning == 'function' ) {
							trx_addons_msgbox_warning(
								rez.error,
								TRX_ADDONS_STORAGE['msg_ai_helper_download_error'],
								'attention',
								0,
								[ TRX_ADDONS_STORAGE['msg_caption_ok'] ]
							);
						} else {
							//alert( rez.error );
							showMessage( rez.error, 'error' );
						}
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
					.find( '.sc_igenerator_message_inner' )
						.html( msg )
						.parent()
							.toggleClass( 'sc_igenerator_message_type_error', type == 'error' )
							.toggleClass( 'sc_igenerator_message_type_info', type == 'info' )
							.toggleClass( 'sc_igenerator_message_type_success', type == 'success' )
							.addClass( 'sc_igenerator_message_show' )
							.slideDown();
			}

			// Check limits for generation images
			function checkLimits() {
				// Block the button if the limits are exceeded only if the demo images are not selected in the shortcode params
				if ( ! $form.data( 'igenerator-demo-images' ) ) {
					var total, used, number;
					// Check limits for the image generation
					var $limit_total = $form.find( '.sc_igenerator_limits_total_value' ),
						$limit_used  = $form.find( '.sc_igenerator_limits_used_value' );
					if ( $limit_total.length && $limit_used.length ) {
						total = parseInt( $limit_total.text(), 10 );
						used  = parseInt( $limit_used.text(), 10 );
						number = parseInt( $form.data( 'igenerator-number' ), 10 );
						if ( ! isNaN( total ) && ! isNaN( used ) && ! isNaN( number ) ) {
							if ( used >= total ) {
								$button.toggleClass( 'sc_igenerator_form_field_prompt_button_disabled', true );
								$prompt.attr( 'disabled', 'disabled' );
								showMessage( $form.data( 'igenerator-limit-exceed' ), 'error' );
								return false;
							}
						}
					}
					// Check limits for the generation requests
					var $requests_total = $form.find( '.sc_igenerator_limits_total_requests' ),
						$requests_used  = $form.find( '.sc_igenerator_limits_used_requests' );
					if ( $requests_total.length && $requests_used.length ) {
						total = parseInt( $requests_total.text(), 10 );
						//used  = parseInt( $requests_used.text(), 10 );
						used = ( trx_addons_get_cookie( 'trx_addons_ai_helper_igenerator_count' ) || 0 ) * 1;
						if ( ! isNaN( total ) && ! isNaN( used ) ) {
							if ( used >= total ) {
								$button.toggleClass( 'sc_igenerator_form_field_prompt_button_disabled', true );
								$prompt.attr( 'disabled', 'disabled' );
								showMessage( $form.data( 'igenerator-limit-exceed' ), 'error' );
								return false;
							}
						}
					}
				}
				return true;
			}
			
			// Update a counter of generated images inside a limits text
			function updateLimitsCounter( number ) {
				var total, used;
				// Update a counter of the generated images
				var $limit_total = $form.find( '.sc_igenerator_limits_total_value' ),
					$limit_used  = $form.find( '.sc_igenerator_limits_used_value' );
				if ( $limit_total.length && $limit_used.length ) {
					total = parseInt( $limit_total.text(), 10 );
					used  = parseInt( $limit_used.text(), 10 );
					if ( ! isNaN( total ) && ! isNaN( used ) && ! isNaN( number ) ) {
						if ( used < total ) {
							used = Math.min( used + number, total );
							$limit_used.text( used );
						}
					}
				}
				// Update a counter of the generation requests
				var $requests_total = $form.find( '.sc_igenerator_limits_total_requests' ),
					$requests_used  = $form.find( '.sc_igenerator_limits_used_requests' );
				if ( $requests_total.length && $requests_used.length ) {
					total = parseInt( $requests_total.text(), 10 );
					// used  = parseInt( $requests_used.text(), 10 );
					used = ( trx_addons_get_cookie( 'trx_addons_ai_helper_igenerator_count' ) || 0 ) * 1;
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
				var count = trx_addons_get_cookie( 'trx_addons_ai_helper_igenerator_count' ) || 0,
					limit = 60 * 60 * 1000 * 1,	// 1 hour
					expired = limit - ( new Date().getTime() % limit );

				trx_addons_set_cookie( 'trx_addons_ai_helper_igenerator_count', ++count, expired );
			}

			// Return an URL to download the image
			function getDownloadLink( url ) {
				return trx_addons_add_to_url( TRX_ADDONS_STORAGE['site_url'], {
					'action': 'trx_addons_ai_helper_igenerator_download',
					'image': trx_addons_get_file_name( url )
				} );
			}

			// Wrap the image into the link to open it in the popup
			function prepareImagesForPopup() {
				var popup = $form.data( 'igenerator-popup' );
				if ( popup ) {
					var found = false;
					$preview.find( '.sc_igenerator_image:not(.sc_igenerator_image_fetch) img' ).each( function() {
						var $img = jQuery( this ),
							$wrap = $img.parent();
						if ( $wrap.is( 'a' ) ) {
							$wrap.attr( 'href', $img.attr( 'src' ) );
						} else {
							$img.wrap( '<a href="' + $img.attr( 'src' ) + '" rel="' + ( TRX_ADDONS_STORAGE['popup_engine'] == 'pretty' ? 'prettyPhoto[slideshow]' : 'magnific' ) + '" ></a>' );
						}
						found = true;
					} );
					if ( found ) {
						$document.trigger( 'action.init_hidden_elements', [ $preview ] );
					}
				}
			}

			// Fetch images
			function fetchImages(data) {
				jQuery.post( TRX_ADDONS_STORAGE['ajax_url'], {
					nonce: TRX_ADDONS_STORAGE['ajax_nonce'],
					action: 'trx_addons_ai_helper_fetch_images',
					fetch_id: data.fetch_id,
					fetch_model: data.fetch_model
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
					if ( ! rez.error ) {
						if ( rez.data && rez.data.images && rez.data.images.length > 0 ) {
							var images = rez.data.images,
								$fetch = $preview.find( 'img#fetch-' + data.fetch_id );
							// Fade out fetch placeholders
							if ( animation_out ) {
								for ( var i = 0; i < images.length; i++ ) {
									$fetch.eq( i ).parents( '.sc_igenerator_image_fetch' )
										.removeClass( animation_in )
										.addClass( animation_out );
								}
							}
							// Replace fetch placeholders with real images
							setTimeout( function() {
								var $download_link;
								var currentDate = new Date();
								var timestamp = currentDate.getTime();
								for ( var i = 0; i < images.length; i++ ) {
									$fetch.eq( i ).attr( 'src', images[i].url );
									$download_link = $fetch.eq( i ).parent().find( '.sc_igenerator_image_link' );
									$download_link.attr( 'href', getDownloadLink( images[i].url ) );
									$download_link.attr( 'data-expired', parseInt( $download_link.attr( 'data-expired' ), 10 ) + timestamp );
								}
							}, animation_out ? 300 : 0 );
							// Fade in real images
							setTimeout( function() {
								for ( var i = 0; i < images.length; i++ ) {
									$fetch.eq( i )
										.parents( '.sc_igenerator_image_fetch' )
											.removeClass( 'sc_igenerator_image_fetch' )
											.find( '.sc_igenerator_image_fetch_info')
												.remove();
									if ( animation_in ) {
										trx_addons_when_images_loaded( $fetch.eq( i ).parents( '.sc_igenerator_image' ), function( $img ) {
											$img
												.removeClass( animation_out )
												.addClass( animation_in );
										} );
									}
								}
								prepareImagesForPopup();
							}, animation_out ? 800 : 0 );
						} else {
							setTimeout( function() {
								fetchImages( data );
							}, data.fetch_time ? data.fetch_time : 4000 );
						}
					} else {
						$preview.empty();
						//alert( rez.error );
						showMessage( rez.error, 'error' );
					}
				} );
			}

		} );

	} );

} );