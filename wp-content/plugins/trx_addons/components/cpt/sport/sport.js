/* global jQuery, TRX_ADDONS_STORAGE */

jQuery(document).on('action.ready_trx_addons', function() {
	"use strict";

	jQuery('.sport_page_list:not(.inited)').addClass('inited').on('click', '.sport_page_round_time', function(e) {
		var rnd = jQuery(this).data('round');
		var list = jQuery(this).parents('.sport_page_list');
		list.find('[data-round="'+rnd+'"]').toggleClass('closed');
		list.find(rnd).slideToggle();
		e.preventDefault();
		return false;
	});

});