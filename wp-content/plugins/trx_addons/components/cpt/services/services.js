/* global jQuery, TRX_ADDONS_STORAGE */

// Switch tabs content in the services
jQuery(document).on( 'action.init_hidden_elements', function() {

	"use strict";
	
	// Tabs with side titles and effects
	jQuery('.sc_services_tabs:not(.inited)')
		.addClass('inited')
		.on('click', '.sc_services_tabs_list_item:not(.sc_services_tabs_list_item_active)', function(e) {
			jQuery(this).siblings().removeClass('sc_services_tabs_list_item_active');
			jQuery(this).addClass('sc_services_tabs_list_item_active');
			var content = jQuery(this).parent().siblings('.sc_services_tabs_content');
			var items = content.find('.sc_services_item');
			content.find('.sc_services_item_active').addClass('sc_services_item_flip').removeClass('sc_services_item_active');
			items.eq(jQuery(this).index()).addClass('sc_services_item_active');
			setTimeout(function() {
				content.find('.sc_services_item_flip').addClass('trx_addons_hidden').removeClass('sc_services_item_flip');
				items.removeClass('sc_services_item_flipping');
				setTimeout(function() {
					items.removeClass('trx_addons_hidden');
				}, 600);
			}, 600);
			// Patch for Webkit - after the middle motion add class 'flipping' to move active item above old item
			// Attention! Latest versions of Firefox also need this patch!
			if (true || /Chrome/.test(navigator.userAgent) && /Google Inc/.test(navigator.vendor)) {
				setTimeout(function() {
					content.find('.sc_services_item_active').addClass('sc_services_item_flipping');
				}, 250);
			}
			e.preventDefault();
			return false;
		});

	// Simple Tabs with top titles and excerpt
	jQuery('.sc_services_tabs_simple:not(.inited)')
		.addClass('inited')
		.on('click', '.sc_services_tabs_list_item:not(.sc_services_tabs_list_item_active)', function(e) {
			jQuery(this).siblings().removeClass('sc_services_tabs_list_item_active');
			jQuery(this).addClass('sc_services_tabs_list_item_active');
			var content = jQuery(this).parent().siblings('.sc_services_tabs_content');
			var items = content.find('.sc_services_tabs_content_item');
			content.find('.sc_services_tabs_content_item_active').addClass('sc_services_item_flip').removeClass('sc_services_tabs_content_item_active');
			items.eq(jQuery(this).index()).addClass('sc_services_tabs_content_item_active');
			setTimeout(function() {
				content.find('sc_services_item_flip').removeClass('sc_services_item_flip');
			}, 600);
			e.preventDefault();
			return false;
		});
} );