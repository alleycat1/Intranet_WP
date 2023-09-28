/* global jQuery */

(function() {

	"use strict";

	var requestAnimationFrame = trx_addons_request_animation_frame();

	var $window   = jQuery( window ),
		$document = jQuery( document );

	var mouseX = null, mouseY = null,
		realX  = null, realY  = null;

	var trx_addons_bg_slides_get_mouse_state = function(e) {
		if ( e !== undefined && e.clientX !== undefined ) {
			mouseX = e.clientX;
			mouseY = e.clientY;
		}
		if ( mouseX !== null ) {
			realX = mouseX + trx_addons_window_scroll_left();
			realY = mouseY + trx_addons_window_scroll_top();
		}
	};

	$document.one( 'mouseover', function(e) {
		trx_addons_bg_slides_get_mouse_state(e);
	} );

	$document.on('action.init_hidden_elements', function( e, cont ) {
		if ( cont.hasClass( 'elementor-section' ) ) {
			cont.find('.trx_addons_bg_slides').remove();
			trx_addons_elementor_add_bg_slides_to_row( cont );
		} else {
			jQuery( ( typeof window.elementorFrontend !== 'undefined' && elementorFrontend.isEditMode()
						? '.elementor-section.elementor-element-edit-mode'
						: '.trx_addons_has_bg_slides'
						)
					+ ':not(.trx_addons_has_bg_slides_inited)'
			).each( function() {
				trx_addons_elementor_add_bg_slides_to_row( jQuery( this ) );
			} );
		}
	} );

	// Add background slides to the single section
	function trx_addons_elementor_add_bg_slides_to_row( row ) {
		var data = row.data('trx-addons-bg-slides'),
			cid = '';
		if ( ! data ) {
			cid  = row.data('model-cid');
			if ( cid ) {
				data = trx_addons_elementor_get_settings_by_cid( cid, ['bg_slides'] );
			}
		}
		if ( ! data ) {
			return;
		}

		// Mask moving vars
		var mask_delay = data['bg_slides_mask_delay'] > 0 ? Math.max( 1, data['bg_slides_mask_delay'] ) : 8;
		var destX = 0, destY = 0;

		// Create slides
		if ( ( data['bg_slides_allow'] > 0 && data['bg_slides'].length > 0 ) || data['bg_slides_mask'] > 0 ) {
			if ( ! row.hasClass( 'trx_addons_has_bg_slides' ) ) {
				row.addClass( 'trx_addons_has_bg_slides' );
			}
			var row_cont = row.addClass('trx_addons_has_bg_slides_inited');	//.find('.elementor-container').eq(0);
			var output = '',
				duration = typeof data['bg_slides_animation_duration'] == 'object'
							? data['bg_slides_animation_duration']['size']
							: data['bg_slides_animation_duration'];
			if ( duration ) {
				duration = Math.max( 1, duration * 1 );
				row_cont.get(0).style.setProperty( '--trx-addons-bg-slides-animation-duration', duration+'s' );
			} else {
				duration = 6.5;
			}
			if ( data['bg_slides_allow'] > 0 && data['bg_slides'].length > 0 ) {
				for( var i = 0; i < data['bg_slides'].length; i++ ) {
					if ( data['bg_slides'][i]['slide']['url'] ) {
						output += '<img'
										+ ' src="' + data['bg_slides'][i]['slide']['url'] + '"'
										+ ' class="trx_addons_bg_slides_img'
												+ ' trx_addons_bg_slides_img_' + data['bg_slides'][i]['slide_size']
												+ ( i === 0 ? ' trx_addons_bg_slides_active' : '' )
												+ ( duration > 0 && data['bg_slides'][i]['slide_effect'] != 'none'
													? (' trx_addons_bg_slides_animation_origin_' + data['bg_slides'][i]['slide_origin']
														+ ( i === 0 ? ' trx_addons_bg_slides_animation_' + data['bg_slides'][i]['slide_effect'] : '' )
														)
													: ' trx_addons_bg_slides_static'
													)
												+ '"'
										+ ( duration > 0
											? ' data-trx-addons-bg-slides-animation="' + data['bg_slides'][i]['slide_effect'] + '"'
											: ''
											)
										+ '>';
					}
				}
			}

			var bg_slides_present = output !== '',
				bg_mask_present = data['bg_slides_mask'] > 0;

			// Add an overlay color layer
			if ( data['bg_slides_overlay_color'] ) {
				output += '<div class="trx_addons_bg_slides_overlay" style="background-color:' + data['bg_slides_overlay_color'] + '"></div>';
			}

			// Add a mask layer
			if ( data['bg_slides_mask'] > 0 ) {
				var svg = '';
				if ( ! data['bg_slides_mask_svg'] || ( typeof data['bg_slides_mask_svg'] == 'object' && ! data['bg_slides_mask_svg']['url'] ) ) {
					svg = TRX_ADDONS_STORAGE['bg_slides_mask_svg'];
				} else if ( typeof data['bg_slides_mask_svg'] == 'object' ) {
					svg = '<img src="' + data['bg_slides_mask_svg']['url'] + '">';
				} else {
					svg = data['bg_slides_mask_svg'];
				}
				output += '<div class="trx_addons_bg_slides_mask">' + svg + '</div>';
			}
			
			// Insert a layout to the section
			if ( output ) {
				row_cont.prepend(
					'<div class="trx_addons_bg_slides'
						+ ( data['bg_slides_mask'] > 0
							? ' trx_addons_bg_slides_with_mask'
							: ''
							)
						+ '"'
					+ '>'
						+ output
					+ '</div>'
				);

				var $wrap = row_cont.find( '.trx_addons_bg_slides' );

				var trx_addons_bg_slides_set_wrap_dimensions = function() {
					$wrap.data( {
								'trx-addons-bg-slides-offset': $wrap.hasClass( 'trx_addons_bg_slides_fixed' )
																	? { 'left': 0, 'top': trx_addons_fixed_rows_height() }
																	: $wrap.offset(),
								'trx-addons-bg-slides-width': $wrap.outerWidth(),
								'trx-addons-bg-slides-height': $wrap.outerHeight()
					} );
					row_cont.data( {
								'trx-addons-bg-slides-offset': row_cont.offset(),
								'trx-addons-bg-slides-width': row_cont.outerWidth(),
								'trx-addons-bg-slides-height': row_cont.outerHeight()
					} );
				};
				trx_addons_bg_slides_set_wrap_dimensions();

				// Animate slides
				if ( bg_slides_present && duration > 0 ) {
					var images = row_cont.find( '.trx_addons_bg_slides_img' ),
						active_slide = row_cont.find( '.trx_addons_bg_slides_active' );
					var active_slide_changer = function( idx ) {
						var active_slide = row_cont.find( '.trx_addons_bg_slides_active' ),
							active_idx = active_slide.length ? active_slide.index() : 0,
							next_idx = idx !== undefined
											? idx
											: ( active_idx + 1 >= images.length
												? 0
												: active_idx + 1
												);
//						active_slide.removeClass( 'trx_addons_bg_slides_active trx_addons_bg_slides_animation_' + data['bg_slides'][active_idx]['slide_effect'] );
						active_slide.removeClass( 'trx_addons_bg_slides_active' );
						if ( data['bg_slides'][active_idx]['slide_effect'] != 'none' ) {
							setTimeout( function() {
								active_slide.removeClass( 'trx_addons_bg_slides_animation_' + data['bg_slides'][active_idx]['slide_effect'] );
							}, 500 );
						}
						images.eq(next_idx).addClass( 'trx_addons_bg_slides_active'
							+ ( data['bg_slides'][active_idx]['slide_effect'] != 'none'
								? ' trx_addons_bg_slides_animation_' + data['bg_slides'][next_idx]['slide_effect']
								: ''
								)
						);
						if ( ! images.eq(next_idx).hasClass( 'trx_addons_bg_slides_static' ) ) {
							active_slide_timer( next_idx );
						}
					};
					var active_slide_timer = function( idx ) {
						if ( ['none', 'fade'].indexOf( data['bg_slides'][idx]['slide_effect'] ) == -1 ) {
							trx_addons_on_end_animation( images.get(idx), active_slide_changer, duration * 1000 );
						} else {
							setTimeout( function() {
								active_slide_changer();
							}, duration * 1000 );
						}
					};

					// Change slides on effect transition end
					if ( ! images.eq(active_slide.index()).hasClass( 'trx_addons_bg_slides_static' ) ) {
						active_slide_timer( active_slide.index() );

					// Change slides on window scrolled
					} else {
						$document.on( 'action.scroll_trx_addons', function() {
							var wrap_height = $wrap.data('trx-addons-bg-slides-height');
							var row_offset = row_cont.data('trx-addons-bg-slides-offset'),
								row_top = row_offset.top,
								row_height = row_cont.data('trx-addons-bg-slides-height');
							var delta = row_height / data['bg_slides'].length;
							var slide_num = Math.max( 0, Math.min( data['bg_slides'].length - 1, Math.round( ( trx_addons_window_scroll_top() - row_top ) / delta ) ) );
							if ( ! images.eq(slide_num).hasClass( 'trx_addons_bg_slides_active' ) ) {
								active_slide_changer( slide_num );
							}
						} );
					}
				}

				// Move mask on mouse move
				if ( bg_mask_present ) {
					var $mask = row_cont.find( '.trx_addons_bg_slides_mask' ),
						$mask_in_svg = $mask.find( '.trx_addons_mask_in_svg' );

					var trx_addons_bg_slides_mask_check_active = function() {
						var offset = row_cont.data('trx-addons-bg-slides-offset'),
							left = offset.left,
							top = offset.top,
							width = row_cont.data('trx-addons-bg-slides-width'),
							height = row_cont.data('trx-addons-bg-slides-height');
						return left <= realX && realX < left + width && top <= realY && realY < top + height;
					};

					var trx_addons_bg_slides_mask_move = function() {
						cancelAnimationFrame( trx_addons_bg_slides_mask_move );
						if ( trx_addons_window_width() >= TRX_ADDONS_STORAGE['mobile_breakpoint_mousehelper_off']
							&& null !== mouseX
							&& ( destX != mouseX || destY != mouseY )
							&& mask_delay > 1
						) {
							if ( $wrap.hasClass( 'trx_addons_bg_slides_mask_active' ) ) {
								destX += (mouseX - destX) / mask_delay;
								destY += (mouseY - destY) / mask_delay;
								trx_addons_bg_slides_mask_update();
							}
						}
						requestAnimationFrame(trx_addons_bg_slides_mask_move);
					};
					requestAnimationFrame(trx_addons_bg_slides_mask_move);

					var trx_addons_bg_slides_mask_update = function() {
						var offset = $wrap.data('trx-addons-bg-slides-offset'),
							left = offset.left,
							top = offset.top,
							width = $wrap.data('trx-addons-bg-slides-width'),
							height = $wrap.data('trx-addons-bg-slides-height'),
							dx = ( destX + ( $wrap.hasClass( 'trx_addons_bg_slides_fixed' ) ? 0 : trx_addons_window_scroll_left() ) - left ) / width * 100,
							dy = ( destY + ( $wrap.hasClass( 'trx_addons_bg_slides_fixed' ) ? 0 : trx_addons_window_scroll_top() ) - top ) / height * 100;
						$mask.css( "transform", "translate(" + dx + "%," + dy + "%)");
					};

					var trx_addons_bg_slides_fix_wrap = function() {
						var wrap_offset = $wrap.data('trx-addons-bg-slides-offset'),
							wrap_top = wrap_offset.top,
							wrap_left = wrap_offset.left,
							wrap_width = $wrap.data('trx-addons-bg-slides-width'),
							wrap_height = $wrap.data('trx-addons-bg-slides-height');
						var row_offset = row_cont.data('trx-addons-bg-slides-offset'),
							row_top = row_offset.top,
							row_height = row_cont.data('trx-addons-bg-slides-height');
						if ( trx_addons_window_width() >= TRX_ADDONS_STORAGE['mobile_breakpoint_mousehelper_off']
							&& row_height > trx_addons_window_height()
						) {
							// Fix/unfix slides wrap
							if ( trx_addons_window_scroll_top() + trx_addons_fixed_rows_height() >= row_top
								&& trx_addons_window_scroll_top() + trx_addons_window_height() <= row_top + row_height
							) {
								if ( ! $wrap.hasClass( 'trx_addons_bg_slides_fixed' ) ) {
									$wrap
										.addClass( 'trx_addons_bg_slides_fixed' )
										.css( {
											'top': trx_addons_fixed_rows_height(),	//0
											'left': wrap_left,
											'width': wrap_width
										} );
									trx_addons_bg_slides_set_wrap_dimensions();
								}
							} else {
								if ( $wrap.hasClass( 'trx_addons_bg_slides_fixed' ) ) {
									$wrap
										.removeClass( 'trx_addons_bg_slides_fixed' )
										.css( {
											'top': trx_addons_window_scroll_top() + trx_addons_fixed_rows_height() < row_top ? 0 : row_height - wrap_height,
											'left': 0,
											'width': '100%'
										} );
									trx_addons_bg_slides_set_wrap_dimensions();
								}
							}
						} else if ( $wrap.hasClass( 'trx_addons_bg_slides_fixed' ) ) {
							$wrap
								.removeClass( 'trx_addons_bg_slides_fixed' )
								.css( {
									'top': 0,
									'left': 0,
									'width': '100%'
								} );
							trx_addons_bg_slides_set_wrap_dimensions();
						}
						// Zoom mask
						var zoom_min = data['bg_slides_mask_zoom'] > 0 ? Math.max( 1.0, data['bg_slides_mask_zoom'] ) : 1.0,
							zoom_max = 7.0,
							zoom = trx_addons_window_scroll_top() + trx_addons_fixed_rows_height() > row_top
									? zoom_min + Math.min( zoom_max, ( trx_addons_window_scroll_top() + trx_addons_fixed_rows_height() - row_top ) / ( row_height - trx_addons_window_height() / 6 ) * zoom_max )
									: zoom_min;
							$mask_in_svg.css( { 'transform': 'scale(' + zoom + ')' } );
					};

					var trx_addons_bg_slides_mask_mouse_move_handler = function(e) {
						trx_addons_bg_slides_get_mouse_state(e);
						$wrap.toggleClass( 'trx_addons_bg_slides_mask_active', trx_addons_bg_slides_mask_check_active() );
						if ( $wrap.hasClass( 'trx_addons_bg_slides_mask_active' ) ) {
							if ( mask_delay < 2 ) {
								destX = mouseX;
								destY = mouseY;
								trx_addons_bg_slides_mask_update();
							}
						}
					};
					trx_addons_bg_slides_mask_mouse_move_handler();

					$document
						.on( 'action.resize_trx_addons', function() {
							trx_addons_bg_slides_set_wrap_dimensions();
						} )
						.on( 'action.scroll_trx_addons', function() {
							trx_addons_bg_slides_mask_mouse_move_handler();
							trx_addons_bg_slides_fix_wrap();
						} )
						.on( 'mousemove', function(e) {
							trx_addons_bg_slides_mask_mouse_move_handler(e);
						} );
				}
			}
		}
	}
})();