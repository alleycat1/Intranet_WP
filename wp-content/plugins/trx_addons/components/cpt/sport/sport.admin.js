/* global jQuery, TRX_ADDONS_STORAGE */

jQuery(document).ready(function() {
	"use strict";
	
	// Add 'sport' to the 'Add New' link's URL in the competitions
	if (jQuery('body').hasClass('post-type-cpt_competitions')) {
		var sport = jQuery('select#cpt_competitions_sports').length == 1 ? jQuery('select#cpt_competitions_sports > option:selected').val() : jQuery('input#cpt_competitions_sports').val();
		var add_new = jQuery('a.page-title-action');
		if (add_new.length > 0) {
			var href = jQuery('a.page-title-action').attr('href');
			jQuery('a.page-title-action').attr('href', trx_addons_add_to_url(href, {'cpt_competitions_sports': sport}));
		}

	// Add 'competition' to the 'Add New' link's URL  in the rounds and players
	} else if (jQuery('body').hasClass('post-type-cpt_rounds') || jQuery('body').hasClass('post-type-cpt_players')) {
		var competition = jQuery('select#competition').length == 1 ? jQuery('select#competition > option:selected').val() : jQuery('input#competition').val();
		var add_new = jQuery('a.page-title-action');
		if (add_new.length > 0) {
			var href = jQuery('a.page-title-action').attr('href');
			jQuery('a.page-title-action').attr('href', trx_addons_add_to_url(href, {'competition': competition}));
		}

	// Add 'competition' and 'round' to the 'Add New' link's URL in the matches
	} else if (jQuery('body').hasClass('post-type-cpt_matches')) {
		var competition = jQuery('select#competition').length == 1 ? jQuery('select#competition > option:selected').val() : jQuery('input#competition').val();
		var cur_round = jQuery('select#round').length == 1 ? jQuery('select#round > option:selected').val() : jQuery('input#round').val();
		var add_new = jQuery('a.page-title-action');
		if (add_new.length > 0) {
			var href = jQuery('a.page-title-action').attr('href');
			jQuery('a.page-title-action').attr('href', trx_addons_add_to_url(href, {'competition': competition, 'round': cur_round}));
		}
		
	}
	
	// Field "Sport" is changed - refresh competitions
	//--------------------------------------------------------
	jQuery('body').on('change', 'select.sport,select[name*="sport"]', function () {
		var fld = jQuery(this);
		var comp_fld = fld.hasClass('sport')
							? fld.parents('.vc_edit-form-tab').find('select.competition')			// VC
							: fld.parents('[class*="widget_field_type_"]').next().find('select');	// Widget
		if (comp_fld.length > 0) {
			var comp_lbl = fld.hasClass('sport')
							? comp_fld.parent().prev()																// VC
							: comp_fld.parents('[class*="widget_field_type_"]').find('label.widget_field_title');	// Widget
			trx_addons_refresh_list('competitions', fld.val(), comp_fld, comp_lbl);
		}
	});

	// Field "Competition" is changed - refresh rounds
	//--------------------------------------------------------
	jQuery('body').on('change', 'select.competition,select[name*="competition"]', function () {
		var fld = jQuery(this);
		var round_fld = fld.hasClass('competition')
							? fld.parents('.vc_edit-form-tab').find('select.round')					// VC
							: fld.parents('[class*="widget_field_type_"]').next().find('select');	// Widget
		if (round_fld.length > 0) {
			var round_lbl = fld.hasClass('competition')
							? round_fld.parent().prev()																// VC
							: round_fld.parents('[class*="widget_field_type_"]').find('label.widget_field_title');	// Widget
			trx_addons_refresh_list('rounds', fld.val(), round_fld, round_lbl);
		}
	});
	
});