/* global jQuery */

(function() {
	"use strict";
	jQuery('.vc_edit_form_elements').on('change', '.trx_addons_vc_param_select select', function() {
		var field = jQuery(this).parents('.trx_addons_vc_param_select');
		if (field.length > 0) {
			var checked = '';
			field.find('option:checked').each(function() {
				checked += (checked.length > 0 ? ',' : '') + jQuery(this).val();
			});
			field.find('input[type="hidden"]').val(checked).trigger('change');
		}
	});

})();