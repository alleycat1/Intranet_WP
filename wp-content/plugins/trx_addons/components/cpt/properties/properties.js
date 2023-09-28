/* global jQuery, TRX_ADDONS_STORAGE */

// Init properties functionality
jQuery(document).on('action.ready_trx_addons', function() {

	"use strict";
	
	// Single Property's page: Change featured image on click on the gallery image
	jQuery('.properties_page_gallery:not(.inited)')
		.addClass('inited')
		.on('click', '.properties_page_gallery_item', function(e) {
			if (jQuery(this).hasClass('properties_page_gallery_item_active')) return false;
			jQuery(this).siblings().removeClass('properties_page_gallery_item_active');
			jQuery(this).addClass('properties_page_gallery_item_active');
			var image = jQuery(this).data('image');
			if (!image) return false;
			var featured = jQuery(this).parent().prev('.properties_page_featured');
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
	
	// Properties Compare: add item
	function trx_addons_properties_compare_list_add(id, cmp_icon) {
		var title = cmp_icon.siblings('.sc_properties_item_title').text();
		var list = trx_addons_get_cookie('trx_addons_properties_compare_list');
		if (!list) list = {};
		else list = JSON.parse(list);
		list['id_'+id] = encodeURIComponent(title);
		trx_addons_set_cookie('trx_addons_properties_compare_list', JSON.stringify(list), 24 * 60 * 60 * 1000);	// 24 hours
		cmp_icon.addClass('in_compare_list');
		var widget_list = jQuery('.widget_properties_compare .properties_compare_list');
		if (widget_list.length > 0) {
			widget_list.append('<li data-property-id="'+id+'">'+title+'</li>');
			if (widget_list.find('li').length < 2)
				widget_list.addClass('properties_compare_list_empty');
			else
				widget_list.toggleClass('properties_compare_list_empty', false);
		}
	}
	
	// Properties Compare: remove item
	function trx_addons_properties_compare_list_remove(id) {
		var list = trx_addons_get_cookie('trx_addons_properties_compare_list');
		if (!list) list = {};
		else list = JSON.parse(list);
		if (typeof list['id_'+id] != 'undefined') {
			delete list['id_'+id];
		}
		trx_addons_set_cookie('trx_addons_properties_compare_list', JSON.stringify(list), 24 * 60 * 60 * 1000);	// 24 hours
		jQuery('.sc_properties_item_compare.in_compare_list[data-property-id="'+id+'"]').removeClass('in_compare_list');
		var widget_list = jQuery('.widget_properties_compare .properties_compare_list');
		widget_list.find('li[data-property-id="'+id+'"]').remove();
		if (widget_list.find('li').length < 2)
			widget_list.addClass('properties_compare_list_empty');
		else
			widget_list.toggleClass('properties_compare_list_empty', false);
	}
	
	// Properties archive: Compare button
	jQuery('.sc_properties:not(.inited)')
		.addClass('inited')
		.on('click', '.sc_properties_item_compare', function(e) {
			// Remove item from the Compare list
			if (jQuery(this).hasClass('in_compare_list')) {
				trx_addons_properties_compare_list_remove(jQuery(this).data('property-id'));

			// Add item to the Compare list
			} else {
				trx_addons_properties_compare_list_add(jQuery(this).data('property-id'), jQuery(this));
			}
			e.preventDefault();
			return false;
		});

	// Widget "Properties Compare": Remove item from the list
	jQuery('.widget_properties_compare .properties_compare_list:not(.inited)')
		.addClass('inited')
		.on('click', 'li', function(e) {
			trx_addons_properties_compare_list_remove(jQuery(this).data('property-id'));
			e.preventDefault();
			return false;
		});
	
	// Widget "Properties Order": Submit form on change sorting field
	jQuery('select[name="properties_order"]:not(.inited)')
		.addClass('inited')
		.on('change', function(e) {
			jQuery(this).parents('form').submit();
			e.preventDefault();
			return false;
		});
	
	// Widget "Properties Advanced Search": Show/Hide Advanced fields
	jQuery('.properties_search_show_advanced:not(.inited)')
		.addClass('inited')
		.on('click', function () {
			jQuery(this).parents('.properties_search').toggleClass('properties_search_opened');
		});
	
	// Widget "Properties Advanced Search": Field "Country" is changed - refresh states
	jQuery('select[name="properties_country"]:not(.inited)')
		.addClass('inited')
		.on('change', function () {
			var fld = jQuery(this);
			var slave_fld = fld.parents('form').find('select[name="properties_state"]');
			if (slave_fld.length > 0) {
				var slave_lbl = slave_fld.parents('label');
				trx_addons_refresh_list('states', fld.val(), slave_fld, slave_lbl);
			}
		});

	// Widget "Properties Advanced Search": Field "State" is changed - refresh cities
	jQuery('select[name="properties_state"]:not(.inited)')
		.addClass('inited')
		.on('change', function () {
			var fld = jQuery(this);
			var slave_fld = fld.parents('form').find('select[name="properties_city"]');
			if (slave_fld.length > 0) {
				var slave_lbl = slave_fld.parents('label');
				var country = 0;
				if (fld.val() == 0) country = fld.parents('form').find('select[name="properties_country"]').val();
				trx_addons_refresh_list('cities', {'state': fld.val(), 'country': country}, slave_fld, slave_lbl);
			}
		});

	// Widget "Properties Advanced Search": Field "City" is changed - refresh neighborhoods
	jQuery('select[name="properties_city"]:not(.inited)')
		.addClass('inited')
		.on('change', function () {
			var fld = jQuery(this);
			var slave_fld = fld.parents('form').find('select[name="properties_neighborhood"]');
			if (slave_fld.length > 0) {
				var slave_lbl = slave_fld.parents('label');
				trx_addons_refresh_list('neighborhoods', fld.val(), slave_fld, slave_lbl);
			}
		});

});