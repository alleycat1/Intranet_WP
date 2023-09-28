/**
 * Shortcode Contact form
 *
 * @package ThemeREX Addons
 * @since v1.2
 */

/* global jQuery, TRX_ADDONS_STORAGE */

jQuery(document).on( 'action.init_hidden_elements', function(e, container) {

	"use strict";
	
	// Contact form validate and submit
	if (container.find('.sc_form_form:not(.sc_form_custom):not(.inited)').length > 0) {
		container.find('.sc_form_form:not(.sc_form_custom):not(.inited)')
			.addClass('inited')
			.on( 'submit', function( e ) {
				sc_form_validate( jQuery( this ) );
				e.preventDefault();
				return false;
			} );
	}

	// Mark field as 'filled' and remove error message
	jQuery(
		 'input[type="text"]:not(.fill_inited),'
		+'input[type="number"]:not(.fill_inited),'
		+'input[type="search"]:not(.fill_inited),'
		+'input[type="password"]:not(.fill_inited),'
		+'input[type="email"]:not(.fill_inited),'
		+'input[type="tel"]:not(.fill_inited),'
		+'input[type="url"]:not(.fill_inited),'
		+'textarea:not(.fill_inited),'
		+'select:not(.fill_inited)'
		)
	.each(function() {
		var $fld = jQuery( this );
		sc_form_mark_filled( $fld );
		$fld.addClass( 'fill_inited' )
			.on( 'blur change', function() {
				var $self = jQuery( this );
				sc_form_mark_filled( $self );
				if ( $self.hasClass( 'filled' ) ) {
					$self.removeClass( 'trx_addons_field_error wpcf7-not-valid' );
				}
			} );
	} );
	
	
	// Mark fields as 'filled'
	function sc_form_mark_filled(field) {
		if (field.val() !== '')
			field.addClass('filled');
		else
			field.removeClass('filled');
	}
	
	// Validate form
	function sc_form_validate(form){
		var url = form.attr('action');
		if (url == '') return false;
		form.find('input').removeClass('trx_addons_error_field');
		var error = trx_addons_form_validate(form, {
				rules: [
					{
						field: "name",
						min_length: { value: 1,	 message: TRX_ADDONS_STORAGE['msg_field_name_empty'] },
					},
					{
						field: "email",
						min_length: { value: 1,	 message: TRX_ADDONS_STORAGE['msg_field_email_empty'] },
						mask: { value: TRX_ADDONS_STORAGE['email_mask'], message: TRX_ADDONS_STORAGE['msg_field_email_not_valid'] }
					},
					{
						field: "message",
						min_length: { value: 1,  message: TRX_ADDONS_STORAGE['msg_field_text_empty'] },
					}
				]
			});
	
		if (!error && url!='#') {
			jQuery.post(url, {
				action: "send_sc_form",
				nonce: TRX_ADDONS_STORAGE['ajax_nonce'],
				data: form.serialize()
			}).done(function(response) {
				var rez = {};
				try {
					rez = JSON.parse(response);
				} catch(e) {
					rez = { error: TRX_ADDONS_STORAGE['msg_ajax_error'] };
					console.log(response);
				}
				var result = form.find(".trx_addons_message_box").removeClass("trx_addons_message_box_error").removeClass("trx_addons_message_box_success");
				if (rez.error === '') {
					form.get(0).reset();
					result.addClass("trx_addons_message_box_success").html(TRX_ADDONS_STORAGE['msg_send_complete']);
				} else {
					result.addClass("trx_addons_message_box_error").html(TRX_ADDONS_STORAGE['msg_send_error'] + ' ' + rez.error);
				}
				result.fadeIn().delay(3000).fadeOut();
				jQuery( document ).trigger( 'action.got_ajax_response', {
					action: 'send_sc_form',
					result: rez
				});
			});
		}
		return !error;
	}
});