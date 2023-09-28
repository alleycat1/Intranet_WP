/* global jQuery, elementor */

jQuery(document).ready(function() {
	"use strict";

	var tax_lists = {},
		fields_state = false,
		pmv = false;

	// Refresh states list when country is changed in Elementor editor
	jQuery('#elementor-panel')
		.on('change', 'select[data-setting="sport"]', function (e) {
			var slave_fld = jQuery(this).parents('.elementor-control').next().find('select[data-setting="competition"]');
			if (slave_fld.length > 0) {
				var slave_lbl = slave_fld.parents('.elementor-control').find('label.elementor-control-title');
				trx_addons_refresh_list('competitions', jQuery(this).val(), slave_fld, slave_lbl, false);
			}
			return false;
		});

	// Refresh cities list when state is changed in Elementor editor
	jQuery('#elementor-panel')
		.on('change', 'select[data-setting="competition"]', function (e) {
			var slave_fld = jQuery(this).parents('.elementor-control').next().find('select[data-setting="round"]');
			if (slave_fld.length > 0) {
				var slave_lbl = slave_fld.parents('.elementor-control').find('label.elementor-control-title');
				var sport_val = jQuery(this).parents('.elementor-control').prev().find('select').val();
				// Restore fields values when panel is just opened
				if (fields_state !== false 
					&& fields_state.sport_val == sport_val
				   	&& fields_state.round_val !== false) {
						jQuery(this).val(fields_state.round_val);
						fields_state.round_val = false;
						jQuery(this).trigger('change');
						return false;
				}
				trx_addons_refresh_list('rounds', jQuery(this).val(), slave_fld, slave_lbl, false);
			}
			return false;
		});

	// Store lists to restore it when shortcode params open again
	jQuery('#elementor-panel')
		.on('change', 'select[data-setting="round"]', function () {
			var competition_fld = jQuery(this).parents('#elementor-controls').find('select[data-setting="competition"]'),
				sport_fld = jQuery(this).parents('#elementor-controls').find('select[data-setting="sport"]');
			if (competition_fld.length > 0 && sport_fld.length > 0) {
				// Restore fields values when panel is just opened
				if (fields_state !== false && fields_state.competition_val == competition_fld.val()) {
					jQuery(this).val(fields_state.round_val);
					fields_state = false;
					jQuery(this).trigger('change');	// Refresh preview area
				} else {
					tax_lists[sport_fld.data('element-cid')] = {
						'competitions': competition_fld.html(),	//.data('items'),
						'rounds': jQuery(this).html()	//.data('items')
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
			if ( ! tab_chg) {
				pmv = false;
			}
			var sport_fld = panel.content.$el.find( 'select[data-setting="sport"]' ),
				competition_fld = panel.content.$el.find( 'select[data-setting="competition"]' ),
				round_fld = panel.content.$el.find( 'select[data-setting="round"]' );
			// If this widget haven't required fields - exit
			if ( sport_fld.length === 0 || competition_fld.length === 0 || round_fld.length === 0 ) {
				return;
			}
			// Save panel, model, view to use it when tabs are clicked
			if ( ! tab_chg ) {
				pmv = {'panel':panel, 'model': model, 'view':view};
			}
			// Add view.cid to the field 'post_type'
			var el_cid = view.cid;
			sport_fld.attr('data-element-cid', el_cid);
			var sport_val = sport_fld.val(),
				competition_val = model.getSetting(competition_fld.data('setting')),
				round_val = model.getSetting(round_fld.data('setting'));
			// If list of taxonomies is correct - exit
			if ( competition_fld.find('option[value="'+competition_val+'"]').length > 0
				&& round_fld.find('option[value="'+round_val+'"]').length > 0
			) {
				return;
			}
			// If we have stored list of items - use it
			if (tax_lists[el_cid] !== undefined) {
				competition_fld.html(tax_lists[el_cid].competitions).val(city_val);
				round_fld.html(tax_lists[el_cid].rounds).val(round_val);
			} else {
				fields_state = {'sport_val': sport_val, 'competition_val': competition_val, 'round_val': round_val};
				sport_fld.trigger('change');
			}
		}
	}
});
