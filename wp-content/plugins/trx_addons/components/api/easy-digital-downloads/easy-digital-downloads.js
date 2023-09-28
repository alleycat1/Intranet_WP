/**
 * Themes market: Admin utils
 *
 * @package ThemeREX Addons
 * @since v1.6.34
 */

jQuery(document).ready(function() {
	"use strict";
	
	// Change price options in the mode 'Multi'
	jQuery('.edd_price_options.edd_multi_mode input[type="checkbox"]').on('change', function() {
		var opt_list = jQuery(this).parents('.edd_price_options');
		// Disable check both 'Regular' and 'Extended'
		var li = jQuery(this).parents('li'),
			li_id = li.attr('id');
		if (li_id && li_id.indexOf('_regular') > 0 || li_id.indexOf('_extended') > 0) {
			var price2 = opt_list.find('li[id*="'+(li_id.indexOf('_regular') > 0 ? '_extended' : '_regular')+'"] input[type="checkbox"]:checked');
			if (price2.length > 0) {
				price2.get(0).checked = false;
			}
		}
		// Disable uncheck all elements
		if (opt_list.find('input[type="checkbox"]:checked').length == 0) {
			opt_list.find('li:first-child input[type="checkbox"]').get(0).checked = true;
		}
		// Recalc subtotals on options change
		var total = 0;
		var curr = opt_list.find('.edd_price_option_price').eq(0).html().replace(/[0-9a-zA-Z\/\.,<>]/g, '');
		// Calc subtotals
		opt_list.find('input[type="checkbox"]:checked').each(function() {
			var price = jQuery(this).data('price');
			if (price === undefined) 
				price = Number(jQuery(this).parents('li').find('.edd_price_option_price:not(.regular_price)').eq(0).html().replace(/[^0-9\.,]/g, ''));
			price = isNaN(price) ? 0 : Number(price);
			total += price;
		});
		opt_list.find('.trx_addons_edd_purchase_subtotal_value').html(curr+total.formatMoney().replace('.00', ''));
	});
	
	// Show/Hide price selector
	jQuery('.sc_edd_add_to_cart .trx_addons_edd_purchase_price_selector .trx_addons_edd_purchase_price_label').on('click', function(e) {
		jQuery(this).next().slideToggle();
	});
	// Select position in the price selector
	jQuery('.sc_edd_add_to_cart .trx_addons_edd_purchase_price_selector .trx_addons_edd_purchase_price_list_item').on('click', function(e) {
		var selector = jQuery(this).parents('.trx_addons_edd_purchase_price_selector'),
			opt_list = selector.siblings('.edd_price_options'),
			chk = opt_list.find('li').eq(jQuery(this).index()).find('input[type="checkbox"]'),
			rad = opt_list.find('li').eq(jQuery(this).index()).find('input[type="radio"]');
		selector.find('.trx_addons_edd_purchase_price_label').html(jQuery(this).find('.trx_addons_edd_purchase_price_list_item_label').html());
		selector.find('.trx_addons_edd_purchase_price_list').slideUp();
		if (chk.length > 0) {
			chk.get(0).checked = true;
			chk.trigger('change');
		} else if (rad.length > 0) {
			opt_list.find('input[type="radio"]:checked').get(0).checked = false;
			rad.get(0).checked = true;
			rad.trigger('change');
		}
	});

});