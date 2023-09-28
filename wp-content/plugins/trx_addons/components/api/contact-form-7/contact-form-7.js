/* global jQuery */

(function() {
	"use strict";

	jQuery(document).on( 'action.init_hidden_elements', trx_addons_cf7_init );
	
	function trx_addons_cf7_init(e, container) {
		if (container === undefined) container = jQuery('body');
		if (container.length === undefined || container.length === 0) return;
		
		container.find('.wpcf7:not(.alert_inited)').each( function() {
			var $form = jQuery(this);
			// Decorate messages
			$form
				.addClass('alert_inited')
				.on('wpcf7:submit wpcf7submit', function(e, details) {
					var response = $form.find('.wpcf7-response-output');
					if ( response.length ) {
						response
							.addClass('trx_addons_message_box')
							.removeClass('trx_addons_message_box_info trx_addons_message_box_error trx_addons_message_box_success');
						if ( typeof e == 'object' && typeof e.detail == 'object' && typeof e.detail.status != 'undefined' ) {
							if ( e.detail.status == 'validation_failed' ) {
								response.addClass('trx_addons_message_box_error');
							} else if ( e.detail.status == 'mail_sent' ) {
								response.addClass('trx_addons_message_box_success');
							} else {
								response.addClass('trx_addons_message_box_info');
							}
						}
						response.fadeIn();
					}
				})
				.on('click keypress change', function() {
					$form.find('.wpcf7-response-output:visible').fadeOut();
				} );
			// Remove validation tip and class on any key pressed in the field
			$form
				.on('change', 'input,select,textarea', function() {
					var $self = jQuery(this),
						$wrap = $self.parents('.wpcf7-not-valid');
					if ( $self.val() !== '' ) {
						if ( $self.hasClass( 'wpcf7-not-valid' ) ) {
							$self.removeClass('wpcf7-not-valid');
						} else if ( $wrap.length > 0 ) {
							$wrap.removeClass('wpcf7-not-valid');
						}
					}
				});
		} );
	}
	
})();
