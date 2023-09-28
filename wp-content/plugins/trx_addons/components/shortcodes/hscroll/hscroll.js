/**
 * Shortcode HScroll
 *
 * @package ThemeREX Addons
 * @since v2.5.2
 */

/* global jQuery, TRX_ADDONS_STORAGE */


jQuery( document ).on( 'action.init_hidden_elements', function() {

	"use strict";

//	if ( screen.width < 768 ) return;

	var is_touch = trx_addons_browser_is_touch(),
		is_sticky = trx_addons_browser_is_support_css_sticky(),
		use_sticky = is_sticky && ! is_touch;	// Safary on mobile return 'sticky', but really is don't support it

	var $window            = jQuery( window ),
		$document          = jQuery( document ),
		$body              = jQuery( 'body' );

	jQuery( '.sc_hscroll:not(.sc_hscroll_inited)' ).each( function(nth) {

		if ( nth === 0 && ! $body.hasClass( 'sc_stack_section_present' ) ) {
			$body.addClass( 'sc_stack_section_present' );
		}

		var $self = jQuery( this ).addClass( 'sc_hscroll_inited' ),
			$wrap = $self.find( '.sc_hscroll_wrap' ),
			$scroller = $self.find( '.sc_hscroll_scroller' ),
			$section = $self.find( '.sc_hscroll_section' ).eq(0),
			$bullets = $self.find( '.sc_hscroll_bullets' ),
			$numbers = $self.find( '.sc_hscroll_numbers' ),
			$progress = $self.find( '.sc_hscroll_progress' ),
			$bg_layers = $self.find( '.sc_hscroll_background' ),
			$parent = $self.parent(),
			$spacer = $self.find( '.sc_hscroll_spacer' ),
			total = $self.data( 'total' ),
			reverse = $self.hasClass( 'sc_hscroll_reverse' ),
			parent_in = false;

		// Watch to the parent come in/out to the viewport
		trx_addons_intersection_observer_add( $parent, function( item, enter, entry ) {
			parent_in = enter;
		} );

		var scroller_width, parent_top, section_width, section_height, spacer_height, dx, dy;

		function trx_addons_sc_hscroller_calc() {
			scroller_width = $scroller.outerWidth();
			parent_top = $parent.offset().top;
			section_width = $section.outerWidth();
			section_height = $section.outerHeight();
			spacer_height = $spacer.height();
			dx = scroller_width - section_width;
			dy = spacer_height - section_height;	//( total - 1 ) * section_height
			// Add data to compatibility with Parallax for blocks inside hscroll sections
			$section.siblings().each( function( idx ) {
				//jQuery( this ).data( 'hscroll-section-offset', ( reverse ? -1 : 1 ) * ( idx + 1 ) * section_height );
				jQuery( this ).data( 'hscroll-section-offset', ( reverse ? -1 : 1 ) * ( idx + 1 ) * spacer_height / total );
			} );
		}

		trx_addons_sc_hscroller_calc();

		//$window.on( 'resize', trx_addons_sc_hscroller_calc );
		$document.on( 'action.resize_trx_addons', trx_addons_sc_hscroller_calc );
		
		$document.on( 'action.sc_layouts_row_fixed_on action.sc_layouts_row_fixed_off', function() {
			parent_top = $parent.offset().top;
			section_height = $section.outerHeight();
			dy = $spacer.height() - section_height;	//( total - 1 ) * section_height
		} );

		var first_run = true;

		function trx_addons_sc_hscroller_update() {
			if ( ! parent_in && ! first_run ) return;
			var duration = 0.3;
			var reset  = true;
			var offset = Math.min( Math.max( parent_top - $window.scrollTop() - trx_addons_fixed_rows_height(), -dy ), 0 );
			var offset2 = reverse ? -dy - offset : offset;
			var is_fixed = offset < 0 && offset > -dy;
			if ( first_run || is_fixed ) {
				$scroller
					// Way 1: Translate scroller via CSS (not smooth in Chrome)
					// .css( 'transform', 'translate3d(' + offset2 / dy * dx + 'px, 0, 0)' )
					// Add data to compatibility with Parallax for blocks inside hscroll sections
					.data( {
						'hscroll-offset': ( reverse ? -1 : 1 ) * offset2
					} );
				// Way 2: Translate scroller via TweenMax (more smooth in Chrome)
				TweenMax.to( $scroller, duration, {
					overwrite: true,
					ease: Power2.easeOut,
					x: offset2 / dy * dx + 'px'
				} );

				if ( is_fixed && ! $scroller.data( 'animated' ) ) {
					$scroller.data( 'animated', true );
					$self.addClass( 'sc_hscroll_animated' );
					if ( ! use_sticky ) {
						$wrap.css( { 'position': 'fixed', 'bottom': 'unset', 'top': 'var(--fixed-rows-height)' } );
					}
				}
				reset = first_run;
			}
			if ( reset ) {
				if ( offset >= 0 ) {
					offset2 = reverse ? dy : 0;
					if ( first_run || $scroller.data( 'animated' ) ) {
						$scroller
							// Way 1: Translate scroller via CSS (not smooth in Chrome)
							// .css( 'transform', 'translate3d(' + ( reverse ? -dx : 0 ) + 'px, 0, 0)' )
							.data( 'animated', false )
							// Add data to compatibility with Parallax for blocks inside hscroll sections
							.data( {
								'hscroll-offset': ( reverse ? -1 : 1 ) * ( -offset2 )
							} );
						// Way 2: Translate scroller via TweenMax (more smooth in Chrome)
						TweenMax.to( $scroller, duration, {
							overwrite: true,
							ease: Power2.easeOut,
							x: (reverse ? -dx : 0) + 'px'
						} );

						$self.removeClass( 'sc_hscroll_animated' );
						if ( ! use_sticky ) {
							$wrap.css( { 'position': 'absolute', 'bottom': 'unset', 'top': '0' } );
						}
					}
				} else {
					offset2 = reverse ? 0 : dy;
					if ( first_run || $scroller.data( 'animated' ) ) {
						$scroller
							// Way 1: Translate scroller via CSS (not smooth in Chrome)
							// .css( 'transform', 'translate3d(' + ( reverse ? 0 : -dx ) + 'px, 0, 0)' )
							.data( 'animated', false )
							// Add data to compatibility with Parallax for blocks inside hscroll sections
							.data( {
								'hscroll-offset': ( reverse ? -1 : 1 ) * ( -offset2 )
							} );
						// Way 2: Translate scroller via TweenMax (more smooth in Chrome)
						TweenMax.to( $scroller, duration, {
							overwrite: true,
							ease: Power2.easeOut,
							x: (reverse ? 0 : -dx) + 'px'
						} );
						$self.removeClass( 'sc_hscroll_animated' );
						if ( ! use_sticky ) {
							$wrap.css( { 'position': 'absolute', 'bottom': '0', 'top': 'unset' } );
						}
					}
				}
			}
			// Calc current page
			var coef = ( reverse ? dy - Math.abs( offset2 ) : Math.abs( offset2 ) ) / dy;
			var page = Math.round( coef * dx / section_width ) + 1;
			// Update progress
			if ( $progress.length ) {
				if ( $progress.hasClass( 'sc_hscroll_progress_position_top' ) || $progress.hasClass( 'sc_hscroll_progress_position_bottom' ) ) {
					$progress.find( '.sc_hscroll_progress_value' ).width( coef * 100 + '%' );
				} else {
					$progress.find( '.sc_hscroll_progress_value' ).height( coef * 100 + '%' );
				}
			}
			if ( $scroller.data( 'last-page' ) != page ) {
				$scroller.data( 'last-page', page );
				// Update bullets
				if ( $bullets.length ) {
					$bullets
						.find( '.sc_hscroll_bullet' )
						.removeClass( 'sc_hscroll_bullet_active' )
						.eq( page - 1 )
						.addClass( 'sc_hscroll_bullet_active' );
				}
				// Update numbers
				if ( $numbers.length ) {
					$numbers.find( '.sc_hscroll_number_active' ).text( page );
				}
				// Update bg layers
				if ( $bg_layers.length ) {
					var curr = -1,
						next = -1;
					$bg_layers.each( function( idx ) {
						if ( jQuery( this ).hasClass( 'sc_hscroll_background_active' ) ) {
							curr = idx;
						}
						if ( idx <= page - 1 ) {
							next = idx;
						}
					} );
					if ( next == -1 ) {
						next = curr;
					}
					$bg_layers
						.removeClass( 'sc_hscroll_background_active' )
						.eq(next).addClass( 'sc_hscroll_background_active' );
				}
			}
			if ( first_run ) {
				first_run = false;
				$document.trigger( 'action.hscroll_inited', [$self] );
			}
		}

		$window.on( 'scroll', trx_addons_sc_hscroller_update );
		$document.on( 'action.resize_trx_addons', trx_addons_sc_hscroller_update );

		// Click on bullets
		$bullets.on( 'click', '.sc_hscroll_bullet:not(.sc_hscroll_bullet_active)', function() {
			var page = jQuery( this ).index();
			var offset = parent_top - trx_addons_fixed_rows_height() + page * section_height;
			trx_addons_document_animate_to( offset );
		} );

	} );

} );