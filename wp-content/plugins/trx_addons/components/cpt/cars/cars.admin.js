/* global jQuery, TRX_ADDONS_STORAGE */

jQuery(document).ready(function() {
	"use strict";
	
	// Field "Maker" is changed - refresh states
	//--------------------------------------------------------
	jQuery('body').on('change', 'select.cars_maker,select#trx_addons_maker,select[name="trx_addons_options_field_maker"],select[name*="cars_maker"]', function () {
		var fld = jQuery(this);
		var slave_fld = fld.hasClass('cars_maker')
							? fld.parents('.vc_edit-form-tab').find('select.cars_model')												// VC
							: (fld.parents('[class*="widget_field_type_"]').length > 0
								? fld.parents('[class*="widget_field_type_"]').next().find('select')									// Widget
								: (fld.attr('name')=='trx_addons_options_field_maker'
									? fld.parents('.trx_addons_options_section').find('select[name="trx_addons_options_field_model"]')	// TRX_Addons Options
									: fld.parents('form').find('select#trx_addons_model')												// Meta fields
									)
								);
		if (slave_fld.length > 0) {
			var slave_lbl = fld.hasClass('cars_maker')
							? slave_fld.parent().prev()																		// VC
							: (slave_fld.parents('[class*="widget_field_type_"]').length > 0
								? slave_fld.parents('[class*="widget_field_type_"]').find('label.widget_field_title')		// Widget
								: (fld.attr('name')=='trx_addons_options_field_maker'
									? slave_fld.parents('.trx_addons_options_item').find('.trx_addons_options_item_title')	// TRX_Addons Options
									: slave_fld.parents('form').find('label#trx_addons_model_label')						// Meta fields
									)
								);
			trx_addons_refresh_list('models', fld.val(), slave_fld, slave_lbl, true);
		}
	});

	// Hide an admin menu item and button 'Add new'
	//--------------------------------------------------------
	if ( TRX_ADDONS_STORAGE['hide_add_new_cars_agent'] ) {
		jQuery('#menu-posts-cpt_cars_agents a[href="post-new.php?post_type=cpt_cars_agents"]').parent().hide();
		jQuery('#wpbody-content a[href*="post-new.php?post_type=cpt_cars_agents"]').hide();
	}
	
});