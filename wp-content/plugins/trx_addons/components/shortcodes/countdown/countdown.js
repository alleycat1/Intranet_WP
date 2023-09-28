/**
 * Shortcode Countdown
 *
 * @package ThemeREX Addons
 * @since v1.4.3
 */

/* global jQuery, TRX_ADDONS_STORAGE */

// Countdown init
jQuery(document).on( 'action.init_hidden_elements', function(e, container) {

	"use strict";

	if (container === undefined) container = jQuery('body');

	container.find('.sc_countdown:not(.inited)').each(function () {
		var $self = jQuery(this).addClass('inited'),
			id = $self.attr('id'),
			interval = 1,	//$self.data('interval'),
			countDate = false,
			countTo = $self.data('count-to') > 0,
			countRestart = $self.data('count-restart') > 0,
			endDateStr = '' + $self.data('date'),
			endDateParts = endDateStr.split('-'),
			endTimeStr = '' + $self.data('time'),
			endTimeParts = endTimeStr.split(':');

		if (endTimeParts.length < 3) endTimeParts[2] = '00';

		if ( countRestart ) {
			var dt = new Date();
			countDate = new Date( dt.getTime() + ( endDateParts[0] * 3600 * 24 + endTimeParts[0] * 3600 + endTimeParts[1] * 60 + endTimeParts[2] * 1 ) * 1000 * ( countTo ? 1 : -1 ) );
		} else {
			countDate = new Date(endDateParts[0], endDateParts[1]-1, endDateParts[2], endTimeParts[0], endTimeParts[1], endTimeParts[2]);
		}

		var countInit = {
			tickInterval: interval,
			onTick: trx_addons_sc_countdown,
			alwaysExpire: true,
			onExpiry: function() {
				trx_addons_sc_countdown([0,0,0,0,0,0]);
			}
		};
		if ( countTo ) {
			countInit.until = countDate;
		} else {
			countInit.since = countDate;
		}
		$self.find('.sc_countdown_placeholder').countdown( countInit );
	});

	// Countdown update
	function trx_addons_sc_countdown(dt) {
		var counter = jQuery(this).parent();
		for (var i=3; i < dt.length; i++) {
			var v = (dt[i]<10 ? '0' : '') + dt[i];
			var item = counter.find('.sc_countdown_item').eq(i-3);
			var digits = item.find('.sc_countdown_digits span').addClass('hide');
			for (var ch=v.length-1; ch >= 0; ch--) {
				digits.eq(ch + (i==3 && v.length<3 ? 1 : 0)).removeClass('hide').text(v.substr(ch, 1));
			}
			trx_addons_draw_arc_on_canvas(item, dt[i]);
		}
	}

} );
