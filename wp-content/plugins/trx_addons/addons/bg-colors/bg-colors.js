/* global jQuery */

(function() {

	"use strict";

	var $window             = jQuery( window ),
		$document           = jQuery( document ),
		$body               = jQuery( 'body' );

	var bg_colors_sections = [],
		bg_colors_selectors = [];

	var bg_colors_calc_sections_pos = trx_addons_debounce( function() {
		if ( bg_colors_sections.length ) {
			var offset = false;
			for ( var i = 0; i < bg_colors_sections.length; i++ ) {
				offset = bg_colors_sections[i].offset();
				bg_colors_sections[i].data( {
					'trx-bg-colors-section-top': offset.top,
					'trx-bg-colors-section-middle': offset.top + bg_colors_sections[i].outerHeight() / 2
				} );
			}
		}
	}, 100 );

	// Update global values
	$document.on( 'action.resize_trx_addons', function() {
		bg_colors_calc_sections_pos();
	} );

	// Init bg colors
	$document.on('action.init_trx_addons', function() {

		var smoke_present = $body.hasClass( 'trx_addons_smoke_present' ),
			smoke_selector = '.trx_addons_smoke_place_body';

		jQuery( '[data-trx-bg-colors-color]' ).each( function() {
			var $self = jQuery( this ),
				selector = $self.data('trx-bg-colors-selector')
							|| ( ! smoke_present ? TRX_ADDONS_STORAGE['bg_colors_selector'] : smoke_selector );
			if ( ! selector ) {
				var id = ('trx_bg_colors_' + Math.random()).replace('.', '');
				$body.append( '<div id="' + id + '" class="trx_bg_colors"></div>' );
				TRX_ADDONS_STORAGE['bg_colors_selector'] = selector = 'body > #' + id;
			}
			if ( selector ) {
				var $target = jQuery( selector ),
					offset  = $self.offset();
				if ( $target.length ) {
					$self.data( {
						'trx-bg-colors-section-top': offset.top,
						'trx-bg-colors-section-middle': offset.top + $self.outerHeight() / 2,
						'trx-bg-colors-target-selector': selector,
						'trx-bg-colors-target': $target
					} );
					bg_colors_sections.push( $self );
					if ( bg_colors_selectors.indexOf( selector ) < 0 ) {
						bg_colors_selectors.push( selector );
					}
				} else {
					$self.removeAttr( 'data-trx-bg-colors-color' );
					$self.removeAttr( 'data-trx-bg-colors-selector' );
				}
			}
			if ( bg_colors_sections.length ) {
				$body.addClass( 'with_bg_colors' );
			}
		} );

		// Draw background on scroll
		$document.on( 'action.scroll_trx_addons', function() {
			bg_colors_draw();
		} );

		// Draw background on init hidden elements
		$document.on( 'action.got_ajax_response action.init_hidden_elements', function() {
			bg_colors_calc_sections_pos();
			bg_colors_draw();
		} );

	} );


	// Set background color
	function bg_colors_draw() {

		var wh = trx_addons_window_height(),
			wm = trx_addons_window_scroll_top() + wh / 2,
			alpha,
			start_color, start_middle, end_color, end_middle,
			cur_color, cur_middle, cur_selector, $cur_target,
			smoke_selector = '.trx_addons_smoke_place_body',
			theme_bg_color = typeof TRX_ADDONS_STORAGE['theme_bg_color'] != 'undefined' ? TRX_ADDONS_STORAGE['theme_bg_color'] : '';

		for ( var s = 0; s < bg_colors_selectors.length; s++ ) {
			cur_selector = bg_colors_selectors[s];
			alpha = 255;
			start_color = '';
			start_middle = 0;
			end_color = '';
			end_middle = 0;
			cur_color = '';
			cur_middle = 0;
			$cur_target = '';
			for ( var i = 0; i < bg_colors_sections.length; i++ ) {
				if ( cur_selector != bg_colors_sections[i].data( 'trx-bg-colors-target-selector' ) ) continue;
				cur_middle  = bg_colors_sections[i].data( 'trx-bg-colors-section-middle' );
				cur_color   = bg_colors_sections[i].data( 'trx-bg-colors-color' );
				$cur_target = bg_colors_sections[i].data( 'trx-bg-colors-target' );
				if ( cur_middle < wm ) {
					start_middle = cur_middle;
					start_color  = cur_color;
				} else {
					if ( ! start_color ) {
						start_middle = Math.max( 0, Math.min( cur_middle - wh, wh / 2 ) );
						start_color  = theme_bg_color ? theme_bg_color : cur_color;
						alpha  = 0;
					}
					end_middle = cur_middle;
					end_color  = cur_color;
					break;
				}
			}
			if ( start_color && ! end_color ) {
				end_middle = cur_middle + wh;
				end_color  = theme_bg_color ? theme_bg_color : cur_color;
				alpha  = 1;
			}
			if ( start_color && end_color && ( start_color != end_color || alpha === 0 || alpha === 1 ) ) {
				var rgb_start = trx_addons_hex2rgb( start_color ),
					rgb_end   = trx_addons_hex2rgb( end_color ),
					distance  = Math.max( 0, Math.min( 1, ( wm - start_middle ) / ( end_middle - start_middle ) ) );
				rgb_start['r'] += Math.round( ( rgb_end['r'] - rgb_start['r'] ) * distance );
				rgb_start['g'] += Math.round( ( rgb_end['g'] - rgb_start['g'] ) * distance );
				rgb_start['b'] += Math.round( ( rgb_end['b'] - rgb_start['b'] ) * distance );
				cur_color = trx_addons_components2hex( rgb_start['r'], rgb_start['g'], rgb_start['b'],
								theme_bg_color
									? false
									: ( alpha === 0
										? Math.round( 255 * distance )
										: ( alpha === 1
											? Math.round( 255 * 1 - distance )
											: false
											)
										)
							);
			}
			if ( cur_color ) {
				if ( cur_selector == smoke_selector ) {
					if ( typeof window.trx_addons_smoke_set_bg_color != 'undefined' ) {
						trx_addons_smoke_set_bg_color( cur_color );
					}
				} else {
					$cur_target.css( 'background-color', cur_color );
				}
			}
		}
	}

})();