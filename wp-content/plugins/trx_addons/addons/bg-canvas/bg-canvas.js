/* global jQuery */

(function() {

	"use strict";

	var $window   = jQuery( window ),
		$document = jQuery( document ),
		$body     = jQuery( 'body' );

	var $lazy_images;

	// Update links and values after the new post added
	$document.on( 'action.got_ajax_response', update_jquery_links );
	$document.on( 'action.init_hidden_elements', update_jquery_links );
	var first_run = true;
	function update_jquery_links(e) {
		if ( first_run && e && e.namespace == 'init_hidden_elements' ) {
			first_run = false;
			return; 
		}
		$lazy_images = jQuery('img[loading="lazy"]');
	}
	update_jquery_links();

	// Init bg canvas
	var _bg_canvas = false;

	$document.on('action.init_trx_addons', function() {

		var cnt = 0;

		jQuery( '[data-bg-canvas-type="start"]' ).each( function() {
			var $start = jQuery( this ),
				id = $start.data('bg-canvas-id'),
				$end = $body.find( '[data-bg-canvas-id="' + id + '"][data-bg-canvas-type="end"]' ),
				effect_start = $start.data('bg-canvas-effect') ? $start.data('bg-canvas-effect') : 'round',
				effect_end = $end.data('bg-canvas-effect') ? $end.data('bg-canvas-effect') : 'round',
				color_start = $start.data('bg-canvas-color'),
				color_end = $end.data('bg-canvas-color') ? $end.data('bg-canvas-color') : color_start;

			if ( id && color_start && $end.length > 0 ) {
				if ( _bg_canvas === false ) {
					_bg_canvas = [];
				}
				$body
					.addClass( 'with_bg_canvas' )
					.append( '<div id="bg_canvas_' + id + '" class="bg_canvas"></div>' );

				_bg_canvas.push( {
					canvas: {
							obj: $body.find( '#bg_canvas_' + id )
							},
					start: {
							obj: $start,
							color: color_start,
							effect: effect_start,
							size: $start.data('bg-canvas-size') ? $start.data('bg-canvas-size') : 0,
							shift: $start.data('bg-canvas-shift') ? $start.data('bg-canvas-shift') : 0
							},
					end: {
							obj: $end,
							color: color_end,
							effect: effect_end,
							size: $end.data('bg-canvas-size') ? $end.data('bg-canvas-size') : 0,
							shift: $end.data('bg-canvas-shift') ? $end.data('bg-canvas-shift') : 0
						}
				} );

				if ( effect_start == 'round' || effect_end == 'round' ) {
					_bg_canvas[cnt].canvas.cnv = document.createElement("canvas");
					_bg_canvas[cnt].canvas.ctx = _bg_canvas[cnt].canvas.cnv.getContext("2d");
					_bg_canvas[cnt].canvas.obj.append( _bg_canvas[cnt].canvas.cnv );
				}

				if ( effect_start == 'fade' || effect_end == 'fade' ) {
					_bg_canvas[cnt].canvas.obj.css( {
						'background-color': color_start,
						'opacity': 0
					} );
				}

				cnt++;
			}
		} );

		if ( _bg_canvas !== false ) {

			if ( _bg_canvas.length > 1 ) {
				$body.addClass( 'multi_bg_canvas' );
			}

			// Recalc positions on resize
			$document.on( 'action.resize_trx_addons', trx_addons_debounce( trx_addons_bg_canvas_set_positions, 500 ) );

			// Recalc positions on new posts are loaded
			$document.on( 'action.got_ajax_response', function() {
				trx_addons_bg_canvas_set_positions();
				setTimeout( function() {
					trx_addons_bg_canvas_set_positions();
				}, 1000 );
			} );

			// Recalcs positions on internal lazy images are loaded
			$document.on( 'action.init_lazy_load_elements', function() {
				trx_addons_bg_canvas_set_positions();
			} );

			// Recalcs positions on external lazy images are loaded
			var check_lazy_images = trx_addons_debounce( function() {
				var chg = false;
				$lazy_images.each( function() {
					var img = jQuery(this);
					if ( ! img.data('lazy-complete') && img.get(0).complete ) {
						chg = true;
						img.data('lazy-complete', true);
					}
				} );
				if ( chg ) {
					trx_addons_bg_canvas_set_positions();
				}
			}, 100 );

			// Draw background on scroll
			$document.on( 'action.scroll_trx_addons', function() {
				// Some browsers hide address bar on scroll (window height is increased, but resize event is not generated)
				if ( trx_addons_window_height() != $window.height() || Math.abs( _bg_canvas[0].canvas.obj.height() - trx_addons_window_height() ) > 1 ) {
					trx_addons_window_height( _bg_canvas[0].canvas.obj.height() );	//$window.height();
					trx_addons_bg_canvas_set_positions();
				}
				// Check lazy images on load
				check_lazy_images();
				// Draw element
				trx_addons_bg_canvas_draw( false );
			} );

			trx_addons_bg_canvas_set_positions();

			$document.trigger( 'action.trx_addons_bg_canvas', [_bg_canvas] );
		}

		function trx_addons_bg_canvas_draw( force ) {
			for (var i=0; i < _bg_canvas.length; i++ ) {
				trx_addons_bg_canvas_draw_item( _bg_canvas[i], force );
			}
		}

		function trx_addons_bg_canvas_set_positions() {
			for (var i=0; i < _bg_canvas.length; i++ ) {
				trx_addons_bg_canvas_item_set_position( _bg_canvas[i] );
			}
			trx_addons_bg_canvas_draw( true );
		}

		function trx_addons_bg_canvas_item_set_position( item ) {
			var o,
				is_mobile = trx_addons_browser_is_mobile() || jQuery('body').hasClass('ua_mobile');

			jQuery( item.canvas.cnv )
				.css( {
					width:  is_mobile ? '100vw' : trx_addons_window_width() + 'px',
					height: is_mobile ? '100vh' : trx_addons_window_height() + 'px'
				} )
				.attr( {
					width: trx_addons_window_width(),
					height: trx_addons_window_height()
				} );

			item.w = trx_addons_window_width();
			item.h = trx_addons_window_height();
			item.size = ( is_mobile ? 1.5 : 1 ) * Math.sqrt(Math.pow(trx_addons_window_width(), 2) + Math.pow(trx_addons_window_height() + Math.abs( Math.min(0, item.start.shift, item.end.shift) ) * trx_addons_window_height() / 100, 2));

			o = item.start.obj.offset();
			item.start.w = Math.max(1, item.start.obj.width());
			item.start.h = Math.max(1, item.start.obj.height());
			item.start.left = o.left;
			item.start.top  = o.top;

			o = item.end.obj.offset();
			item.end.w = Math.max(1, item.end.obj.width());
			item.end.h = Math.max(1, item.end.obj.height());
			item.end.left = o.left;
			item.end.top  = o.top;
		}

		function trx_addons_bg_canvas_set_coords( item ) {
			
			var t  = item.start.top - trx_addons_window_scroll_top(),
				dt = trx_addons_window_height() * item.start.shift / 100,
				rt = 1 - Math.max( 0, Math.min( 1, ( t + dt ) / trx_addons_window_height() ) ),
				b  = item.end.top + item.end.h - trx_addons_window_scroll_top() - trx_addons_window_height(),
				db = trx_addons_window_height() * item.end.shift / 100,
				rb = Math.max( 0, Math.min( 1, ( b + db ) / trx_addons_window_height() ) );
			item.progress = Math.min(rt, rb);
			item.distance = rt == 1 && rb == 1 ? -t / ( -t - dt + b + db - trx_addons_window_height() ) : 0;
			item.coords = item.end.top - trx_addons_window_scroll_top() + db < trx_addons_window_height() * 1.5 || rb < 1
							? {
								x: item.end.left + item.end.w / 2,
								y: item.end.top + item.end.h / 2 - trx_addons_window_scroll_top(),
								r: item.end.size,
								d: 'end'
								}
							: {
								x: item.start.left + item.start.w / 2,
								y: Math.max(item.start.top + item.start.h / 2 - trx_addons_window_scroll_top(), trx_addons_window_height() / 2),
								r: item.start.size,
								d: 'start'
								};
		}

		function trx_addons_bg_canvas_draw_item( item, force ) {
			trx_addons_bg_canvas_set_coords( item );
			if ( force || ! item.done || ( item.progress > 0 && item.progress < 1 ) ) {
				var effect = item.coords.d == 'start' ? item.start.effect : item.end.effect,
					color = item.coords.d == 'start' ? item.start.color : item.end.color;
				if ( item.start.color != item.end.color && item.distance > 0 ) {
					var rgb_start = trx_addons_hex2rgb( item.start.color ),
						rgb_end = trx_addons_hex2rgb( item.end.color );
					rgb_start['r'] += Math.round( ( rgb_end['r'] - rgb_start['r'] ) * item.distance );
					rgb_start['g'] += Math.round( ( rgb_end['g'] - rgb_start['g'] ) * item.distance );
					rgb_start['b'] += Math.round( ( rgb_end['b'] - rgb_start['b'] ) * item.distance );
					color = trx_addons_components2hex(rgb_start['r'],rgb_start['g'],rgb_start['b']);
				}
				if ( effect == 'round' ) {
					if ( item.effect != effect ) {
						item.effect = effect;
						item.canvas.obj.css( {
							'background-color': 'transparent',
							'opacity': 1
						} );
						jQuery( item.canvas.cnv ).show();
					}
					var t = item.progress * item.size;
					t = t < item.coords.r + 10 ? item.coords.r : t;
					item.canvas.ctx.clearRect(0, 0, item.w, item.h);
					item.canvas.ctx.beginPath();
					item.canvas.ctx.fillStyle = color;
					item.canvas.ctx.ellipse(item.coords.x, item.coords.y, t, t, 0, 0, 2 * Math.PI);
					item.canvas.ctx.closePath();
					item.canvas.ctx.fill();
				} else if ( effect == 'fade' ) {
					if ( item.effect != effect || item.start.color != item.end.color ) {
						item.canvas.obj.css( {
							'background-color': color
						} );
					}
					if ( item.effect != effect ) {
						item.effect = effect;
						jQuery( item.canvas.cnv ).hide();
					}
					item.canvas.obj.css( {
						'opacity': item.progress
					} );
				} else {
					$document.trigger( 'action.trx_addons_bg_canvas_draw', [item] );
				}
			}
			item.done = ( item.progress === 0 || item.progress == 1 )
						&& item.start.color == item.end.color
						&& ( item.end.top + item.end.h / 2 + item.end.size < trx_addons_window_scroll_top() || item.end.top > trx_addons_window_scroll_top() + trx_addons_window_height() )
						&& item.start.shift === 0;
		}
	} );


	// Mark Elementor sections between start & end with class 'bg_canvas_covered'
	$document.on( 'action.trx_addons_bg_canvas', function(e, _bg_canvas) {
		if ( _bg_canvas !== false ) {
			for (var i=0; i < _bg_canvas.length; i++ ) {
				var start = _bg_canvas[i].start.obj.parents( '.elementor-section:not(.elementor-inner-section)' ).eq(0),
					end   = _bg_canvas[i].end.obj.parents( '.elementor-section:not(.elementor-inner-section)' ).eq(0);
				if ( start.length > 0 && end.length > 0 ) {
					do {
						start.addClass( 'bg_canvas_covered' );
						if ( start.data('id') != end.data('id') ) {
							start = start.next( '.elementor-section' ).eq(0);
						} else {
							break;
						}
					} while ( start.length > 0 );
				}
			}
		}
	} );

})();