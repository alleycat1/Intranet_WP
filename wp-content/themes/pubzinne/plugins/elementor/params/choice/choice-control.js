/*
global: elementor, jQuery;
*/
jQuery(document).ready(function(){
	"use strict";
	// Generate Elementor-specific event when field is changed:
	// input	for @ui.input, @ui.textarea
	// change	for @ui.checkbox, @ui.radio, @ui.select
	// click	for @ui.responsiveSwitchers
	jQuery('#elementor-panel').on('change', '.pubzinne_param_choice input', function(e) {
		jQuery(this).trigger('input');
	});
});
