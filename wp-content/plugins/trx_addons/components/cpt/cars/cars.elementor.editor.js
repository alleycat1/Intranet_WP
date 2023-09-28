/* global jQuery, elementor */

jQuery(document).ready(function() {
	"use strict";

	var tax_lists = {},
		fields_state = false,
		pmv = false;

	// Refresh models list when maker is changed in Elementor editor
	jQuery('#elementor-panel')
		.on('change', 'select[data-setting="cars_maker"]', function (e) {
			var model_fld = jQuery(this).parents('.elementor-control').next().find('select[data-setting="cars_model"]');
			if (model_fld.length > 0) {
				var model_lbl = model_fld.parents('.elementor-control').find('label.elementor-control-title');
				trx_addons_refresh_list('models', jQuery(this).val(), model_fld, model_lbl, true);
			}
			return false;
		});

	// Store lists to restore it when shortcode params open again
	jQuery('#elementor-panel')
		.on('change', 'select[data-setting="cars_model"]', function () {
			var tax_fld = jQuery(this).parents('.elementor-control').prev().find('select[data-setting="cars_maker"]');
			if (tax_fld.length > 0) {
				// Restore fields values when panel is just opened
				if (fields_state !== false && fields_state.tax_val == tax_fld.val()) {
					jQuery(this).val(fields_state.terms_val);
					fields_state = false;
					jQuery(this).trigger('change');	// Refresh preview area
				} else {
					tax_lists[tax_fld.data('element-cid')] = {
						'terms': jQuery(this).html()	//.data('items')
					};
				}
			}
		})
		.on('click', '.elementor-panel-navigation-tab', function() {
			if (pmv !== false)
				trx_addons_elementor_open_panel(pmv.panel, pmv.model, pmv.view, true);
		});
	
	
	// Add Elementor's hooks and elements
	if (window.elementor !== undefined && window.elementor.hooks !== undefined) {
		// Add hook on panel open
		elementor.hooks.addAction( 'panel/open_editor/widget', trx_addons_elementor_open_panel);
	}

	// Store taxonomies and terms to restore it when shortcode params open again
	function trx_addons_elementor_open_panel( panel, model, view, tab_chg ) {
		if ( panel.content !== undefined ) {
			//Reset panel, model, view
			if ( ! tab_chg ) {
				pmv = false;
			}
			var tax_fld = panel.content.$el.find( 'select[data-setting="cars_maker"]' );
			var terms_fld = panel.content.$el.find( 'select[data-setting="cars_model"]' );
			// If this widget haven't fields 'cars_maker' or 'cars_model' - exit
			if ( tax_fld.length === 0 || terms_fld.length === 0) {
				return;
			}
			// Save panel, model, view to use it when tabs are clicked
			if ( ! tab_chg ) {
				pmv = {'panel':panel, 'model': model, 'view':view};
			}
			// Add view.cid to the field 'post_type'
			var el_cid = view.cid;
			tax_fld.attr('data-element-cid', el_cid);
			var tax_val = tax_fld.val();
			var terms_val = model.getSetting(terms_fld.data('setting'));
			// If list of taxonomies is correct - exit
			if ( terms_fld.find('option[value="'+terms_val+'"]').length > 0 ) {
				return;
			}
			// If we have stored list of items - use it
			if (tax_lists[el_cid] !== undefined) {
				terms_fld.html(tax_lists[el_cid].terms).val(terms_val);
			} else {
				fields_state = {'tax_val': tax_val, 'terms_val': terms_val};
				tax_fld.trigger('change');
			}
		}
	}
});