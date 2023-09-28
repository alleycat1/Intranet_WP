/**
 * Widget Recent News: Categories dropdown
 *
 * @package ThemeREX Addons
 * @since v1.0
 */

/* global jQuery */

// Init handlers
jQuery(document).on('action.ready_trx_addons', function() {

	"use strict";
	
	jQuery('.sc_recent_news_header_category_item_more:not(.inited)')
		.addClass('inited')
		.on('click', function() {
			jQuery(this).toggleClass('opened').find('.sc_recent_news_header_more_categories').slideToggle();
		});
});
