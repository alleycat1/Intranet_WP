/* global jQuery, TRX_ADDONS_STORAGE */

jQuery(document).ready(function() {
	"use strict";

	// Field "Country" is changed - refresh states
	//--------------------------------------------------------
	jQuery('body').on('change', 'select.properties_country,select#trx_addons_country,select[name="trx_addons_options_field_country"],select[name*="properties_country"]', function () {
		var fld = jQuery(this);
		var slave_fld = fld.hasClass('properties_country')
							? fld.parents('.vc_edit-form-tab').find('select.properties_state')											// VC
							: (fld.parents('[class*="widget_field_type_"]').length > 0
								? fld.parents('[class*="widget_field_type_"]').next().find('select')									// Widget
								: (fld.attr('name')=='trx_addons_options_field_country'
									? fld.parents('.trx_addons_options_section').find('select[name="trx_addons_options_field_state"]')	// TRX_Addons Options
									: fld.parents('form').find('select#trx_addons_state')												// Meta fields
									)
								);
		if (slave_fld.length > 0) {
			var slave_lbl = fld.hasClass('properties_country')
							? slave_fld.parent().prev()																		// VC
							: (slave_fld.parents('[class*="widget_field_type_"]').length > 0
								? slave_fld.parents('[class*="widget_field_type_"]').find('label.widget_field_title')		// Widget
								: (fld.attr('name')=='trx_addons_options_field_country'
									? slave_fld.parents('.trx_addons_options_item').find('.trx_addons_options_item_title')	// TRX_Addons Options
									: slave_fld.parents('form').find('label#trx_addons_state_label')						// Meta fields
									)
								);
			trx_addons_refresh_list('states', fld.val(), slave_fld, slave_lbl, true);
		}
	});

	// Field "State" is changed - refresh cities
	//--------------------------------------------------------
	jQuery('body').on('change', 'select.properties_state,select#trx_addons_state,select[name="trx_addons_options_field_state"],select[name*="properties_state"]', function () {
		var fld = jQuery(this);
		var slave_fld = fld.hasClass('properties_state')
							? fld.parents('.vc_edit-form-tab').find('select.properties_city')											// VC
							: (fld.parents('[class*="widget_field_type_"]').length > 0
								? fld.parents('[class*="widget_field_type_"]').next().find('select')									// Widget
								: (fld.attr('name')=='trx_addons_options_field_state'
									? fld.parents('.trx_addons_options_section').find('select[name="trx_addons_options_field_city"]')	// TRX_Addons Options
									: fld.parents('form').find('select#trx_addons_city')												// Meta fields
									)
								);
		if (slave_fld.length > 0) {
			var slave_lbl = fld.hasClass('properties_state')
							? slave_fld.parent().prev()																		// VC
							: (slave_fld.parents('[class*="widget_field_type_"]').length > 0
								? slave_fld.parents('[class*="widget_field_type_"]').find('label.widget_field_title')		// Widget
								: (fld.attr('name')=='trx_addons_options_field_state'
									? slave_fld.parents('.trx_addons_options_item').find('.trx_addons_options_item_title')	// TRX_Addons Options
									: slave_fld.parents('form').find('label#trx_addons_city_label')							// Meta fields
									)
								);
			var country = 0;
			if (fld.val() == 0) {
				country = fld.hasClass('properties_state')
								? fld.parents('.vc_edit-form-tab').find('select.properties_country').val()					// VC
								: (fld.parents('[class*="widget_field_type_"]').length > 0
									? fld.parents('[class*="widget_field_type_"]').prev().find('select').val()				// Widget
									: (fld.attr('name')=='trx_addons_options_field_state'
										? fld.parents('.trx_addons_options_section').find('select[name="trx_addons_options_field_country"]').val()	// TRX_Addons Options
										: fld.parents('form').find('select#trx_addons_country').val()						// Meta fields
										)
									);
			}
			trx_addons_refresh_list('cities', {'state': fld.val(), 'country': country}, slave_fld, slave_lbl, true);
		}
	});

	// Field "City" is changed - refresh neighborhoods
	//--------------------------------------------------------
	jQuery('body').on('change', 'select.properties_city,select#trx_addons_city,select[name="trx_addons_options_field_city"],select[name*="properties_city"]', function () {
		var fld = jQuery(this);
		var slave_fld = fld.hasClass('properties_city')
							? fld.parents('.vc_edit-form-tab').find('select.properties_neighborhood')						// VC
							: (fld.parents('[class*="widget_field_type_"]').length > 0
								? fld.parents('[class*="widget_field_type_"]').next().find('select')						// Widget
								: (fld.attr('name')=='trx_addons_options_field_city'
									? fld.parents('.trx_addons_options_section').find('select[name="trx_addons_options_field_neighborhood"]')	// TRX_Addons Options
									: fld.parents('form').find('select#trx_addons_neighborhood')							// Meta fields
									)
								);
		if (slave_fld.length > 0) {
			var slave_lbl = fld.hasClass('properties_city')
							? slave_fld.parent().prev()																		// VC
							: (slave_fld.parents('[class*="widget_field_type_"]').length > 0
								? slave_fld.parents('[class*="widget_field_type_"]').find('label.widget_field_title')		// Widget
								: (fld.attr('name')=='trx_addons_options_field_city'
									? slave_fld.parents('.trx_addons_options_item').find('.trx_addons_options_item_title')	// TRX_Addons Options
									: slave_fld.parents('form').find('label#trx_addons_neighborhood_label')					// Meta fields
									)
								);
			trx_addons_refresh_list('neighborhoods', fld.val(), slave_fld, slave_lbl, true);
		}
	});

	// Hide an admin menu item and button 'Add new'
	//--------------------------------------------------------
	if ( TRX_ADDONS_STORAGE['hide_add_new_agent'] ) {
		jQuery('#menu-posts-cpt_agents a[href="post-new.php?post_type=cpt_agents"]').parent().hide();
		jQuery('#wpbody-content a[href*="post-new.php?post_type=cpt_agents"]').hide();
	}

});