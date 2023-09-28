/* global jQuery:false */
/* global PUBZINNE_STORAGE:false */
/* global TRX_ADDONS_STORAGE:false */

(function() {
	"use strict";
	
	jQuery(document).on('action.add_googlemap_styles', pubzinne_trx_addons_add_googlemap_styles);
	jQuery(document).on('action.init_hidden_elements', pubzinne_trx_addons_init);
	
	// Add theme specific styles to the Google map
	function pubzinne_trx_addons_add_googlemap_styles(e) {
		if (typeof TRX_ADDONS_STORAGE == 'undefined') return;
		TRX_ADDONS_STORAGE['googlemap_styles']['dark'] = [{"featureType":"all","elementType":"labels.text.fill","stylers":[{"saturation":36},{"color":"#333333"},{"lightness":40}]},{"featureType":"all","elementType":"labels.text.stroke","stylers":[{"visibility":"on"},{"color":"#ffffff"},{"lightness":16}]},{"featureType":"all","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"administrative","elementType":"geometry.fill","stylers":[{"color":"#fefefe"},{"lightness":20}]},{"featureType":"administrative","elementType":"geometry.stroke","stylers":[{"color":"#fefefe"},{"lightness":17},{"weight":1.2}]},{"featureType":"landscape","elementType":"geometry","stylers":[{"lightness":20},{"color":"#13162b"}]},{"featureType":"poi","elementType":"geometry","stylers":[{"color":"#13162b"},{"lightness":21}]},{"featureType":"poi.park","elementType":"geometry","stylers":[{"color":"#5fc6ca"},{"lightness":21}]},{"featureType":"road","elementType":"all","stylers":[{"visibility":"simplified"},{"color":"#cccdd2"}]},{"featureType":"road","elementType":"geometry","stylers":[{"color":"#13162b"}]},{"featureType":"road","elementType":"geometry.fill","stylers":[{"color":"#ff0000"}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#13162b"},{"lightness":17}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#ffffff"},{"lightness":29},{"weight":0.2}]},{"featureType":"road.arterial","elementType":"geometry","stylers":[{"color":"#13162b"},{"lightness":18}]},{"featureType":"road.local","elementType":"geometry","stylers":[{"color":"#13162b"},{"lightness":16}]},{"featureType":"transit","elementType":"geometry","stylers":[{"color":"#13162b"},{"lightness":19}]},{"featureType":"water","elementType":"geometry","stylers":[{"color":"#f4f9fc"},{"lightness":17}]}];
	}
	
	
	function pubzinne_trx_addons_init(e, container) {
		if (arguments.length < 2) var container = jQuery('body');
		if (container===undefined || container.length === undefined || container.length == 0) return;
		container.find('.sc_countdown_item canvas:not(.inited)').addClass('inited').attr('data-color', PUBZINNE_STORAGE['alter_link_color']);
	}

})();