/**
 * Shortcode Images Compare
 *
 * @package ThemeREX Addons
 * @since v1.97.0
 */

/* global jQuery, TRX_ADDONS_STORAGE */


jQuery( document ).on( 'action.init_hidden_elements', function() {

	"use strict";

	jQuery( '.sc_icompare:not(.sc_icompare_inited)' ).each( function() {

		var $self    = jQuery( this ).addClass( 'sc_icompare_inited' ),
			st       = 0,
			sw       = 0,
			sh       = 0,
			ox       = 0,
			oy       = 0,
			$content = $self.find( '.sc_icompare_content' ),
			$handler = $self.find( '.sc_icompare_handler' ),
			hsize    = $handler.width(),
			hpos     = $handler.data( 'handler-pos' ),
			dir      = $self.hasClass('sc_icompare_direction_vertical') ? 'v' : 'h',
			$img1    = $self.find( '.sc_icompare_image1' ),
			$img2    = $self.find( '.sc_icompare_image2' ),
			$txt1    = $self.find( '.sc_icompare_text_before' ),
			$txt2    = $self.find( '.sc_icompare_text_after' ),
			tx1      = 0,
			ty1      = 0,
			tw1      = 0,
			th1      = 0,
			tx2      = 0,
			ty2      = 0,
			tw2      = 0,
			th2      = 0;

		jQuery( document ).on( 'action.resize_trx_addons', function() { get_offset(); get_dimensions(); handler_move_to( hpos ); } );
		jQuery( document ).on( 'action.scroll_trx_addons', function() { get_offset(); } );

		// Show shortcode after images are loaded
		if ( ! $self.hasClass( 'sc_icompare_images_loaded' ) ) {
			trx_addons_when_images_loaded( $self, function() {
				get_offset();
				get_dimensions();
				handler_move_to( hpos );
				$self.addClass( 'sc_icompare_images_loaded' );
			} );
		}

		// Get current dimensions
		function get_dimensions() {
			sw    = $content.outerWidth();
			sh    = $content.outerHeight();
			hsize = $handler.outerWidth();
			if ( $txt1.length ) {
				tw1 = $txt1.outerWidth();
				th1 = $txt1.outerHeight();
			}
			if ( $txt2.length ) {
				tw2 = $txt2.outerWidth();
				th2 = $txt2.outerHeight();
			}
		}

		// Get current offset
		function get_offset() {
			var off = $content.offset();
			ox = off.left;
			oy = off.top;
			if ( $txt1.length ) {
				off = $txt1.offset();
				tx1 = off.left - ox;
				ty1 = off.top - oy;
			}
			if ( $txt2.length ) {
				off = $txt2.offset();
				tx2 = off.left - ox;
				ty2 = off.top - oy;
			}
			st = jQuery( window ).scrollTop();
		}

		// Move handler and clip images
		function handler_move_to( pos, move ) {
			var x = sw * ( dir == 'v' ? pos : 50 ) / 100,
				y = sh * ( dir == 'h' ? pos : 50 ) / 100;
			// Move handler (if need)
			if ( move === undefined || move ) {
				$handler.css( {
					left: x + 'px',
					top:  y + 'px'
				} );
			}
			hpos = pos;
			$handler.data( 'handler-pos', pos );
			// Clip image 1 (before)
			$img1.css( {
				clip: 'rect(' + ( dir == 'v'
									? '0px, ' + x + 'px, ' + sh + 'px, 0px'
									: '0px, ' + sw + 'px, ' + y + 'px, 0px'
									)
						+ ')'
			} );
			// Clip image 2 (after)
			$img2.css( {
				clip: 'rect(' + ( dir == 'v'
									? '0px, ' + sw + 'px, ' + sh + 'px, ' + x + 'px'
									: y + 'px, ' + sw + 'px, ' + sh + 'px, 0px'
									)
						+ ')'
			} );
			// Hide/Show text
			if (  dir == 'v' ) {
				if ( x < tx1 + tw1 ) {
					if ( ! $txt1.hasClass( 'sc_icompare_text_hidden' ) ) {
						$txt1.addClass( 'sc_icompare_text_hidden' );
					}
				} else {
					if ( $txt1.hasClass( 'sc_icompare_text_hidden' ) ) {
						$txt1.removeClass( 'sc_icompare_text_hidden' );
					}
				}
				if ( x > tx2 ) {
					if ( ! $txt2.hasClass( 'sc_icompare_text_hidden' ) ) {
						$txt2.addClass( 'sc_icompare_text_hidden' );
					}
				} else {
					if ( $txt2.hasClass( 'sc_icompare_text_hidden' ) ) {
						$txt2.removeClass( 'sc_icompare_text_hidden' );
					}
				}
			} else {
				if ( y < ty1 + th1 ) {
					if ( ! $txt1.hasClass( 'sc_icompare_text_hidden' ) ) {
						$txt1.addClass( 'sc_icompare_text_hidden' );
					}
				} else {
					if ( $txt1.hasClass( 'sc_icompare_text_hidden' ) ) {
						$txt1.removeClass( 'sc_icompare_text_hidden' );
					}
				}
				if ( y > ty2 ) {
					if ( ! $txt2.hasClass( 'sc_icompare_text_hidden' ) ) {
						$txt2.addClass( 'sc_icompare_text_hidden' );
					}
				} else {
					if ( $txt2.hasClass( 'sc_icompare_text_hidden' ) ) {
						$txt2.removeClass( 'sc_icompare_text_hidden' );
					}
				}
			}
		}

		// Make handler draggable
		if ( $self.hasClass( 'sc_icompare_event_drag' ) ) {
			$handler.draggable({
				axis: dir == 'v' ? 'x' : 'y',
				//'containment': 'parent',
				drag: function( e, ui ) {
					var pos = 0;
					if ( dir == 'v' ) {
						if ( ui.position.left >= sw ) {
							ui.position.left = sw - 1;
						}
						if ( ui.position.left < 1 ) {
							ui.position.left = 0;
						}
						pos = ui.position.left * 100 / sw;
					} else {
						if ( ui.position.top >= sh ) {
							ui.position.top = sh - 1;
						}
						if ( ui.position.top < 1 ) {
							ui.position.top = 0;
						}
						pos = ui.position.top * 100 / sh;
					}
					handler_move_to( pos, false );
				}
			});
		}

		// Move handler on mouse move
		if ( $self.hasClass( 'sc_icompare_event_move' ) ) {
			$content.on( 'mousemove', function( e ) {
				if ( e.clientX !== undefined ) {
					var pos = dir == 'v'
									? ( e.clientX - ox ) * 100 / sw
									: ( e.clientY + st - oy ) * 100 / sh;
					handler_move_to( pos );
				}
			} );
		}

	} );

} );