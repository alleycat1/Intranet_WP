/* global jQuery, TRX_ADDONS_STORAGE */

// Init portfolio gallery
jQuery( document ).on( 'action.init_hidden_elements', function( e, cont ) {

	"use strict";
	
	cont.find( '.sc_portfolio_masonry_wrap,[class*="portfolio_page_gallery_type_masonry_"]').each( function() {
		var portfolio = jQuery(this);
		if ( portfolio.parents( 'div:hidden,article:hidden' ).length > 0 ) return;
		if ( ! portfolio.hasClass( 'inited' ) ) {
			portfolio.addClass( 'inited' );
			trx_addons_when_images_loaded( portfolio, function() {
				setTimeout( function() {
					portfolio.masonry( {
						itemSelector: portfolio.hasClass( 'sc_portfolio_masonry_wrap' ) ? '.sc_portfolio_masonry_item' : '.portfolio_page_gallery_item',
						columnWidth: portfolio.hasClass( 'sc_portfolio_masonry_wrap' ) ? '.sc_portfolio_masonry_item' : '.portfolio_page_gallery_item',
						percentPosition: true
					} );
					// Trigger events after masonry layout is finished
					setTimeout( function() {
						jQuery( window ).trigger( 'resize' );
						jQuery( window ).trigger( 'scroll' );
					}, 100 );
				}, 0 );
			});
		} else {
			// Relayout after 
			//setTimeout( function() { portfolio.masonry(); }, 310 );
		}
	});

});