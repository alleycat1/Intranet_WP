/* global jQuery, elementor */

jQuery(document).ready(function() {
	"use strict";

	var tax_lists = {},
		fields_state = false,
		pmv = false;

	// Refresh states list when country is changed in Elementor editor
	jQuery('#elementor-panel')
		.on('change', 'select[data-setting="properties_country"]', function (e) {
			var slave_fld = jQuery(this).parents('.elementor-control').next().find('select[data-setting="properties_state"]');
			if (slave_fld.length > 0) {
				var slave_lbl = slave_fld.parents('.elementor-control').find('label.elementor-control-title');
				trx_addons_refresh_list('states', jQuery(this).val(), slave_fld, slave_lbl, true);
			}
			return false;
		});

	// Refresh cities list when state is changed in Elementor editor
	jQuery('#elementor-panel')
		.on('change', 'select[data-setting="properties_state"]', function (e) {
			var slave_fld = jQuery(this).parents('.elementor-control').next().find('select[data-setting="properties_city"]');
			if (slave_fld.length > 0) {
				var slave_lbl = slave_fld.parents('.elementor-control').find('label.elementor-control-title');
				var country_val = jQuery(this).parents('.elementor-control').prev().find('select').val();
				// Restore fields values when panel is just opened
				if (fields_state !== false 
					&& fields_state.country_val == country_val
				   	&& fields_state.state_val !== false) {
						jQuery(this).val(fields_state.state_val);
						fields_state.state_val = false;
						jQuery(this).trigger('change');
						return false;
				}
				trx_addons_refresh_list('cities', {'state': jQuery(this).val(), 'country': country_val}, slave_fld, slave_lbl, true);
			}
			return false;
		});

	// Refresh neighborhoods list when city is changed in Elementor editor
	jQuery('#elementor-panel')
		.on('change', 'select[data-setting="properties_city"]', function (e) {
			var slave_fld = jQuery(this).parents('.elementor-control').next().find('select[data-setting="properties_neighborhood"]');
			if (slave_fld.length > 0) {
				var slave_lbl = slave_fld.parents('.elementor-control').find('label.elementor-control-title');
				// Restore fields values when panel is just opened
				if (fields_state !== false 
					&& fields_state.state_val == jQuery(this).parents('.elementor-control').prev().find('select').val()
				   	&& fields_state.city_val !== false) {
						jQuery(this).val(fields_state.city_val);
						fields_state.city_val = false;
						jQuery(this).trigger('change');
						return false;
				}
				trx_addons_refresh_list('neighborhoods', jQuery(this).val(), slave_fld, slave_lbl, true);
			}
			return false;
		});

	// Store lists to restore it when shortcode params open again
	jQuery('#elementor-panel')
		.on('change', 'select[data-setting="properties_neighborhood"]', function () {
			var city_fld = jQuery(this).parents('#elementor-controls').find('select[data-setting="properties_city"]'),
				state_fld = jQuery(this).parents('#elementor-controls').find('select[data-setting="properties_state"]'),
				country_fld = jQuery(this).parents('#elementor-controls').find('select[data-setting="properties_country"]');
			if (city_fld.length > 0 && state_fld.length > 0 && country_fld.length > 0) {
				// Restore fields values when panel is just opened
				if (fields_state !== false && fields_state.city_val == city_fld.val()) {
					jQuery(this).val(fields_state.neighborhood_val);
					fields_state = false;
					jQuery(this).trigger('change');	// Refresh preview area
				} else {
					tax_lists[country_fld.data('element-cid')] = {
						'states': state_fld.html(),	//.data('items'),
						'cities': city_fld.html(),	//.data('items'),
						'neighborhoods': jQuery(this).html()	//.data('items')
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
			var country_fld = panel.content.$el.find( 'select[data-setting="properties_country"]' ),
				state_fld = panel.content.$el.find( 'select[data-setting="properties_state"]' ),
				city_fld = panel.content.$el.find( 'select[data-setting="properties_city"]' ),
				neighborhood_fld = panel.content.$el.find( 'select[data-setting="properties_neighborhood"]' );
			// If this widget haven't required fields - exit
			if ( country_fld.length === 0 || state_fld.length === 0 || city_fld.length === 0 || neighborhood_fld.length === 0 ) {
				return;
			}
			// Save panel, model, view to use it when tabs are clicked
			if ( ! tab_chg ) {
				pmv = {'panel':panel, 'model': model, 'view':view};
			}
			// Add view.cid to the field 'post_type'
			var el_cid = view.cid;
			country_fld.attr('data-element-cid', el_cid);
			var country_val = country_fld.val(),
				state_val = model.getSetting(state_fld.data('setting')),
				city_val = model.getSetting(city_fld.data('setting')),
				neighborhood_val = model.getSetting(neighborhood_fld.data('setting'));
			// If list of taxonomies is correct - exit
			if ( state_fld.find('option[value="'+state_val+'"]').length > 0
				&& city_fld.find('option[value="'+city_val+'"]').length > 0
				&& neighborhood_fld.find('option[value="'+neighborhood_val+'"]').length > 0
			) {
				return;
			}
			// If we have stored list of items - use it
			if (tax_lists[el_cid] !== undefined) {
				state_fld.html(tax_lists[el_cid].states).val(state_val);
				city_fld.html(tax_lists[el_cid].cities).val(city_val);
				neighborhood_fld.html(tax_lists[el_cid].neighborhoods).val(neighborhood_val);
			} else {
				fields_state = {'country_val': country_val, 'state_val': state_val, 'city_val': city_val, 'neighborhood_val': neighborhood_val};
				country_fld.trigger('change');
			}
		}
	}
});