/* global jQuery */

(function() {
	"use strict";
	jQuery('.vc_edit_form_elements').on('change', '.trx_addons_vc_param_radio input[type="radio"]', function() {
		var field = jQuery(this).parents('.trx_addons_vc_param_radio');
		if (field.length > 0)
			field.find('input[type="hidden"]').val(field.find('input[type="radio"]:checked').val()).trigger('change');
	});

})();