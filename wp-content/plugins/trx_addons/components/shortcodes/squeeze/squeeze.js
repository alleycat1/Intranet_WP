/**
 * Shortcode Squeeze
 *
 * @package ThemeREX Addons
 * @since v2.21.2
 */

/* global jQuery, TRX_ADDONS_STORAGE */


jQuery( document ).on( 'action.init_hidden_elements', function() {

	"use strict";

	// if ( screen.width < 768 ) return;

	var $window   = jQuery( window ),
		$document = jQuery( document ),
		$body     = jQuery( 'body' );

	var is_touch = trx_addons_browser_is_touch(),
		is_sticky = trx_addons_browser_is_support_css_sticky(),
		use_sticky = is_sticky && ( ! is_touch || ! $body.hasClass( 'ua_safari' ) );	// Safary on mobile return 'sticky', but really is don't support it

	jQuery( '.sc_squeeze:not(.sc_squeeze_inited)' ).each( function(nth) {

		// if ( nth === 0 && ! $body.hasClass( 'sc_stack_section_present' ) ) {
		// 	$body.addClass( 'sc_stack_section_present' );
		// }

		var $self = jQuery( this ).addClass( 'sc_squeeze_inited' ),
			$scroller = $self.find( '.sc_squeeze_content' ),
			$viewport = $self.find( '.sc_squeeze_viewport' ),
			$wrap = $self.find( '.sc_squeeze_wrap' ),
			$items = $self.find( '.sc_squeeze_item' ),
			$bullets = $self.find( '.sc_squeeze_bullets' ),
			$numbers = $self.find( '.sc_squeeze_numbers' ),
			$progress = $self.find( '.sc_squeeze_progress' ),
			$titles = $self.find( '.sc_squeeze_titles' ),
			in_viewport = false,
			last_call = false;

		// Watch to the parent come in/out to the viewport
		trx_addons_intersection_observer_add( $self, function( item, enter, entry ) {
			last_call = in_viewport && ! enter;
			in_viewport = enter;
		} );

		var scroller_height, scroller_top, item_height;

		function trx_addons_sc_squeeze_calc() {
			scroller_height = $scroller.outerHeight();
			scroller_top = $scroller.offset().top;
			item_height = $items.eq(0).outerHeight();
		}

		trx_addons_sc_squeeze_calc();

		//$window.on( 'resize', trx_addons_sc_squeeze_calc );
		$document.on( 'action.resize_trx_addons', trx_addons_sc_squeeze_calc );
		
		$document.on( 'action.sc_layouts_row_fixed_on action.sc_layouts_row_fixed_off', trx_addons_sc_squeeze_calc );

		var first_run = true;

		function trx_addons_sc_squeeze_update() {
			if ( ! in_viewport && ! first_run && ! last_call ) return;
			var offset = scroller_top - trx_addons_window_scroll_top() - trx_addons_fixed_rows_height();
			var is_fixed = offset < 0 && offset > -scroller_height + item_height;
			offset = Math.max( -scroller_height + item_height, Math.min( 0, offset ) );
			if ( first_run || last_call || is_fixed ) {
				$wrap.css( 'transform', 'translateY(' + offset + 'px)' );
				$items.each( function( idx ) {
					var $item = jQuery( this ),
						item_top = $item.offset().top - scroller_top,
						scale = Math.max( 0, Math.min( 1, 1 - ( item_top - Math.abs( offset ) ) / item_height ) );
					$item.css( 'transform', 'scaleY(' + scale + ')' );
				} );
			}
			if ( is_fixed ) {
				if ( ! $self.hasClass( 'sc_squeeze_fixed' ) ) {
					$self.addClass( 'sc_squeeze_fixed' );
					if ( ! use_sticky ) {
						$viewport.css( { 'position': 'fixed' } );
					}
				}
			} else {
				if ( $self.hasClass( 'sc_squeeze_fixed' ) ) {
					$self.removeClass( 'sc_squeeze_fixed' );
					if ( ! use_sticky ) {
						$viewport.css( { 'position': 'sticky' } );
					}
				}
			}
			first_run = false;

			// Calc current page
			var coef = Math.max( 0, Math.min( 1, -offset / ( scroller_height - item_height ) ) );
			var page = Math.max( 1, Math.min( $items.length, Math.floor( -offset / item_height + 0.5 ) + 1 ) );
			// Update progress
			if ( $progress.length ) {
				if ( $progress.hasClass( 'sc_squeeze_progress_position_top' ) || $progress.hasClass( 'sc_squeeze_progress_position_bottom' ) ) {
					$progress.find( '.sc_squeeze_progress_value' ).width( coef * 100 + '%' );
				} else {
					$progress.find( '.sc_squeeze_progress_value' ).height( coef * 100 + '%' );
				}
			}
			// Update bullets, numbers and titles
			if ( $scroller.data( 'last-page' ) != page ) {
				$scroller.data( 'last-page', page );
				// Update bullets
				if ( $bullets.length ) {
					$bullets
						.find( '.sc_squeeze_bullet' )
							.removeClass( 'sc_squeeze_bullet_active' )
							.eq( page - 1 )
							.addClass( 'sc_squeeze_bullet_active' );
				}
				// Update numbers
				if ( $numbers.length ) {
					$numbers.find( '.sc_squeeze_number_active' ).text( page );
				}
				// Update titles
				if ( $titles.length ) {
					$titles
						.find( '.sc_squeeze_title' )
							.removeClass( 'sc_squeeze_title_active' )
							.eq( page - 1 )
							.addClass( 'sc_squeeze_title_active' );
				}
			}
		}

		$window.on( 'scroll', trx_addons_sc_squeeze_update);
		$document.on( 'action.resize_trx_addons', trx_addons_sc_squeeze_update );

		// Click on bullets
		$bullets.on( 'click', '.sc_squeeze_bullet:not(.sc_squeeze_bullet_active)', function() {
			var page = jQuery( this ).index();
			var offset = scroller_top - trx_addons_fixed_rows_height() + page * item_height;
			trx_addons_document_animate_to( offset );
		} );

	} );

} );
