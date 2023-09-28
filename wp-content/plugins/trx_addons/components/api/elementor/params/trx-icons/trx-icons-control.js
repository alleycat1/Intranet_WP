/*
global: elementor, jQuery;
*/
jQuery(document).ready(function(){
	"use strict";
	// Generate Elementor-specific event when field is changed:
	// input	for @ui.input, @ui.textarea
	// change	for @ui.checkbox, @ui.radio, @ui.select
	// click	for @ui.responsiveSwitchers
	jQuery('#elementor-panel').on('change', '.trx_addons_vc_param_icons input', function(e) {
		jQuery(this).trigger('input');
	});
});
