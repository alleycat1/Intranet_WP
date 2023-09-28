/* global jQuery */

( function() {

	"use strict";

	var $window   = jQuery( window ),
		$document = jQuery( document ),
		$body     = jQuery( 'body' );

	var spa_preloader = {};

	var spa_mode = TRX_ADDONS_SPA_SETTINGS['spa_mode'];
	var preload_selector = TRX_ADDONS_SPA_SETTINGS['preload_selector'];
	var theme_slug = TRX_ADDONS_SPA_SETTINGS['theme_slug'];

	var $page_wrapper = jQuery( TRX_ADDONS_SPA_SETTINGS['replace_selector'] );

	var current_href = location.href;


	// Add handlers to all links on the first page load
	spa_prepare_links();

	// Add handlers to all links on the page
	function spa_prepare_links() {
		jQuery( spa_mode == 'selector' ? preload_selector : 'a[href]:not([href^="#"])' ).each( function() {
			var $link = jQuery( this );
			if ( ! $link.hasClass( 'trx_spa_inited' ) ) {
				$link
					.addClass( 'trx_spa_inited' )
					.on( 'mouseenter', function() {
						spa_preload_page( $link );
					} )
					.on( 'click', function( e ) {
						spa_show_page( $link );
						e.preventDefault();
					} );
			}
		} );
	}

	// Catch the 'Back' and 'Forward' buttons in the browser
	$window.on( 'popstate', function( e ) {
		var target = e.target.location.href,
			state = e.originalEvent.state;
		if ( target != current_href													// go to another page
			&& ( spa_preloader.hasOwnProperty( target )								// and page is already preloaded
				|| spa_mode != 'selector'											//     or need to preload all links
				|| jQuery( 'a[href="' + target + '"]' ).is( preload_selector )		//     or the url of the page is matched to preloader selector
				)
		) {
			// Preload (if need) and show the page
			spa_show_page( 
				jQuery( '<a href="' + target + '"'
							+ ( spa_preloader.hasOwnProperty( target ) ? ' class="trx_spa_preloaded"' : '' )
							+ '></a>'
				)
			);
		} else if ( ! state ) {
			// This page is not need to be preloaded
			location.reload( true );
		}
	} );

	// Preload page on the link hover
	function spa_preload_page( $link ) {
		var href = $link.attr( 'href' );
		if ( ! $link.hasClass( 'trx_spa_preloaded' ) && href && href.indexOf( window.location.hostname) != -1 ) {
			$link.addClass( 'trx_spa_preloaded' );
			//spa_clear_preloader_show();
			if ( ! spa_preloader.hasOwnProperty( href ) ) {
				spa_preloader[ href ] = {
					html: '',
					show: false
				};
				jQuery.get( href, function( response ) {
					spa_preloader[ href ].html = response;
					if ( spa_preloader[ href ].show ) {
						spa_show_page( $link );
					}
					$document.trigger( 'action.got_ajax_response', {
						action: 'trx_spa_page_loaded',
						result: response
					} );
				} );
			}
		}
	}

	// Clear a flag 'show' on all hrefs
	function spa_clear_preloader_show() {
		for (var i = 0; i < spa_preloader.length; i++ ) {
			spa_preloader[i].show = false;
		}
	}

	// Replace the page content
	function spa_show_page( $link ) {
		var href = $link.attr( 'href' );
		$document.trigger( 'action.before_new_page_content', [$link, ! spa_preloader.hasOwnProperty( href ) || ! spa_preloader[ href ].show] );
		if ( ! spa_preloader.hasOwnProperty( href ) || ! $link.hasClass( 'trx_spa_preloaded' ) ) {
			spa_preload_page( $link );
			spa_preloader[ href ].show = true;
		} else if ( spa_preloader[ href ].html ) {
			$document.trigger( 'action.before_replace_page_content', [$link, spa_preloader[ href ].html] );
			setTimeout( function() {
				current_href = href;
				spa_preloader[ href ].show = false;
				spa_replace_page( href );
				spa_reinit_page();
				$document.trigger( 'action.after_new_page_content', [$link] );
			}, trx_addons_apply_filters( 'trx_addons_filter_spa_timeout', 0 ) );
		} else {
			spa_preloader[ href ].show = true;
		}
	}

	// Replace the page content
	function spa_replace_page( href ) {
		var $html = jQuery( spa_preloader[ href ].html );
		var $html_wrapper = $html.find( TRX_ADDONS_SPA_SETTINGS['replace_selector'] );
		if ( $html_wrapper.length == 1 ) {
			// Get inline styles and add to the page styles
			spa_import_inline_styles( spa_preloader[ href ].html, trx_addons_apply_filters( 'trx_addons_filter_spa_import_all_inline_css', true ) );
			// Get tags 'link' from response and add its to the 'head'
			spa_import_tags_link( spa_preloader[ href ].html );
			// Replace the page content
			$document.trigger( 'action.before_remove_content', [$page_wrapper] );
			$page_wrapper.html( $html_wrapper.html() );
			$document.trigger( 'action.after_add_content', [$page_wrapper] );
			// Replace the body classes
			var new_classes = spa_preloader[ href ].html.match( /<body[^>]*class="([^"]*)"/ );
			if ( new_classes ) {
				$body.attr( 'class', new_classes[1] );
			}
			// Replace the page title
			var title = $html.find( '.sc_layouts_title_caption,head title' ).eq(0).text() || '';
			jQuery( '.sc_layouts_title_caption,head title' ).html( title );
			// Replace a location href
			if ( href != location.href ) {
				trx_addons_document_set_location( href, { trx_spa_preloaded: true } );
			}
		}
	}

	// Get all or only filtered inline styles and append to the head or replace existing styles in the head
	function spa_import_inline_styles( html, import_all ) {
		var $head = jQuery( 'head' );
		// Get an inline styles from the tag 'style' with the specified id
		// and replace its in the existing tag 'style' with same id
		// or append a new tag style with same id to the head and put new styles inside
		function spa_import_styles_with_id( id ) {
			var p1, p2, inline_css, $inline_css_tag;
			p1 = html.indexOf( id );
			if ( p1 > 0 ) {
				p1 = html.indexOf( '>', p1 ) + 1;
				p2 = html.indexOf( '</style>', p1 ),
				inline_css = html.substring( p1, p2 ),
				$inline_css_tag = jQuery( '#' + id );
				if ( $inline_css_tag.length === 0 ) {
					// Append styles to the HEAD (if not loaded to the current page)
					$head.append( '<style id="' + id + '" type="text/css">' + inline_css + '</style>' );
				} else {
					// Replace styles inside the existing tag
					$inline_css_tag.html( inline_css );
				}
			}
		}
		if ( import_all ) {
			// Import all tags 'style' with inline styles
			var styles = html.match( /<style[^>]*id=['"]([^'"]+-inline-css)['"][^>]*>/g );
			if ( styles ) {
				for ( var i = 0; i < styles.length; i++ ) {
					var matches = styles[i].match( /<style[^>]*id=['"]([^'"]+-inline-css)['"][^>]*>/ );
					if ( matches && matches.length && matches.length > 1 && matches[1] ) {
						spa_import_styles_with_id( matches[1] );
					}
				}
			}
		} else {
			// Import only filtered tags 'style' with inline styles
			var selectors = trx_addons_apply_filters( 'trx_addons_filter_spa_inline_css_selectors', [
								theme_slug + '-inline-styles-inline-css',
								'trx_addons-inline-styles-inline-css',
								'elementor-frontend-inline-css',
								'woocommerce-inline-inline-css',
								'wpgdprc-front-css-inline-css',
							] );
			for ( var i = 0; i < selectors.length; i++ ) {
				spa_import_styles_with_id( selectors[ i ] );
			}
		}
	}

	// Get all tags 'link' from an html and add its to the 'head'
	function spa_import_tags_link( html ) {
		// Get all tags 'link' with stylesheets
		var links = html.match( /<link[^>]*rel=['"]stylesheet['"][^>]*id=['"]([^'"]+)['"][^>]*>/g );
		if ( links ) {
			var $head = jQuery( 'head' );
			for ( var i = 0; i < links.length; i++ ) {
				var matches = links[i].match( /<link[^>]*rel=['"]stylesheet['"][^>]*id=['"]([^'"]+)['"][^>]*>/ );
				if ( matches && matches.length && matches.length > 1 && matches[1] ) {
					if ( jQuery( '#' + matches[1].replace('.', '\\.') ).length === 0 ) {
						// Prepend its to the HEAD (if not loaded to the current page)
						$head.prepend( links[i] );
					}
				}
			}
		}
	}

	// Init all page elements after loaded
	function spa_reinit_page() {
		// Remove TOC if exists (rebuild on init_hidden_elements)
		jQuery( '#toc_menu' ).remove();
		// Add handlers to all links on the first page load
		spa_prepare_links();
		// Trigger actions to init our new elements
		window[theme_slug.toUpperCase() + '_STORAGE']['init_all_mediaelements'] = true;
		$document
			.trigger( 'action.new_post_added', [$page_wrapper] )
			.trigger( 'action.new_page_content', [$page_wrapper] )
			.trigger( 'action.init_hidden_elements', [$page_wrapper] );
		// Trigger resize after init
		$window.trigger( 'resize' );
	}


	// Init Elementor's animations
	//---------------------------------------------------------------
	if ( typeof elementorModules != 'undefined' && typeof elementorFrontend != 'undefined' ) {
		$document.on( 'action.new_page_content', function( e, cont ) {
			// Init elements handles (commented, because a page is not reloaded)
			// elementorFrontend.elementsHandler.init();
			// Rerun 'ready' handlers for all elements added to the page
			cont.find( '[data-id][data-element_type]' ).each( function() {
				elementorFrontend.elementsHandler.runReadyTrigger( this );
			} );
		} );
	}

} )();