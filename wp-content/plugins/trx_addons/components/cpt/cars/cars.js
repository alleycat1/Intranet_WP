/* global jQuery, TRX_ADDONS_STORAGE */

// Init cars functionality
jQuery(document).on('action.ready_trx_addons', function() {

	"use strict";
	
	// Single Car's page: Change featured image on click on the gallery image
	jQuery('.cars_page_gallery:not(.inited)')
		.addClass('inited')
		.on('click', '.cars_page_gallery_item', function(e) {
			if (jQuery(this).hasClass('cars_page_gallery_item_active')) return;
			jQuery(this).siblings().removeClass('cars_page_gallery_item_active');
			jQuery(this).addClass('cars_page_gallery_item_active');
			var image = jQuery(this).data('image');
			if (!image) return;
			var featured = jQuery(this).parent().prev('.cars_page_featured');
			var img_old = featured.find('img');
			var h = img_old.height();
			featured.height(h);
			img_old.animate({'opacity': 0}, 300, function() {
				jQuery(this).attr({
					'src': image,
					'srcset': ''
					}).animate({'opacity': 1}, 400, function() {
						featured.height('auto');
					});
				setTimeout(function() {
					featured.height(img_old.height());
				}, 100);
			});
			e.preventDefault();
			return false;
		});
	
	// Cars Compare: add item
	function trx_addons_cars_compare_list_add(id, cmp_icon) {
		var title = cmp_icon.siblings('.sc_cars_item_title').text();
		var list = trx_addons_get_cookie('trx_addons_cars_compare_list');
		if (!list) list = {};
		else list = JSON.parse(list);
		list['id_'+id] = encodeURIComponent(title);
		trx_addons_set_cookie('trx_addons_cars_compare_list', JSON.stringify(list), 24 * 60 * 60 * 1000);	// 24 hours
		cmp_icon.addClass('in_compare_list');
		var widget_list = jQuery('.widget_cars_compare .cars_compare_list');
		if (widget_list.length > 0) {
			widget_list.append('<li data-car-id="'+id+'">'+title+'</li>');
			if (widget_list.find('li').length < 2)
				widget_list.addClass('cars_compare_list_empty');
			else
				widget_list.toggleClass('cars_compare_list_empty', false);
		}
	}
	
	// Cars Compare: remove item
	function trx_addons_cars_compare_list_remove(id) {
		var list = trx_addons_get_cookie('trx_addons_cars_compare_list');
		if (!list) list = {};
		else list = JSON.parse(list);
		if (typeof list['id_'+id] != 'undefined') {
			delete list['id_'+id];
		}
		trx_addons_set_cookie('trx_addons_cars_compare_list', JSON.stringify(list), 24 * 60 * 60 * 1000);	// 24 hours
		jQuery('.sc_cars_item_compare.in_compare_list[data-car-id="'+id+'"]').removeClass('in_compare_list');
		var widget_list = jQuery('.widget_cars_compare .cars_compare_list');
		widget_list.find('li[data-car-id="'+id+'"]').remove();
		if (widget_list.find('li').length < 2)
			widget_list.addClass('cars_compare_list_empty');
		else
			widget_list.toggleClass('cars_compare_list_empty', false);
	}
	
	// Cars archive: Compare button
	jQuery('.sc_cars:not(.inited)')
		.addClass('inited')
		.on('click', '.sc_cars_item_compare', function(e) {
			// Remove item from the Compare list
			if (jQuery(this).hasClass('in_compare_list')) {
				trx_addons_cars_compare_list_remove(jQuery(this).data('car-id'));

			// Add item to the Compare list
			} else {
				trx_addons_cars_compare_list_add(jQuery(this).data('car-id'), jQuery(this));
			}
			e.preventDefault();
			return false;
		});

	// Widget "Cars Compare": Remove item from the list
	jQuery('.widget_cars_compare .cars_compare_list:not(.inited)')
		.addClass('inited')
		.on('click', 'li', function(e) {
			trx_addons_cars_compare_list_remove(jQuery(this).data('car-id'));
			e.preventDefault();
			return false;
		});
	
	// Widget "Cars Order": Submit form on change sorting field
	jQuery('select[name="cars_order"]:not(.inited)')
		.addClass('inited')
		.on('change', function(e) {
			jQuery(this).parents('form').submit();
			e.preventDefault();
			return false;
		});
	
	// Widget "Cars Advanced Search": Show/Hide Advanced fields
	jQuery('.cars_search_show_advanced:not(.inited)')
		.addClass('inited')
		.on('click', function () {
			jQuery(this).parents('.cars_search').toggleClass('cars_search_opened');
		});
	
	// Widget "Cars Advanced Search": Field "Maker" is changed - refresh models
	jQuery('select[name="cars_maker"]:not(.inited)')
		.addClass('inited')
		.on('change', function () {
			var fld = jQuery(this);
			var slave_fld = fld.parents('form').find('select[name="cars_model"]');
			if (slave_fld.length > 0) {
				var slave_lbl = slave_fld.parents('label');
				trx_addons_refresh_list('models', fld.val(), slave_fld, slave_lbl);
			}
		});

});