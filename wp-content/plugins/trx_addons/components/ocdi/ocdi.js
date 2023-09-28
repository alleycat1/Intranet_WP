jQuery(document).ready(function() {
    'use strict';

    // Change import type	
	jQuery( '.js-ocdi-import-data' ).clone().appendTo('.ocdi__button-container').addClass('js-ocdi-import-data-not-inited').removeClass('js-ocdi-import-data');
	jQuery( '.js-ocdi-import-data' ).hide();
	
	jQuery( '.js-ocdi-import-data-not-inited' ).live( 'click', function() { 
		var ocdi_button = jQuery(this);		
        var demo_type = jQuery('.ocdi_demo_type select.demo_type').val();
		var import_elements = 'none';
        var import_type = '';
		
		jQuery(ocdi_button).addClass('disable');
		
        jQuery('.ocdi_import_components input[name="import_type"]').each(function() {
            var value = jQuery(this).attr('value');
            if (jQuery(this).is(":checked"))
                import_type = value;
        });
		
		jQuery('.ocdi_import_components input[type="checkbox"]').each(function() {
			var name = jQuery(this).attr('name');
			if (jQuery(this).is(":checked"))
				import_elements += '|' + name;
		});
		
		jQuery.post(TRX_ADDONS_STORAGE['ajax_url'], {
			action: 'trx_addons_ocdi_demo_settings_change',
			nonce: TRX_ADDONS_STORAGE['ajax_nonce'],
			trx_addons_ocdi_demo_type: demo_type,
			trx_addons_ocdi_import_type: import_type,
			trx_addons_ocdi_import_elements: import_elements
		}).done(function(response) {
			var rez = {};
			try {
				rez = JSON.parse(response);
			} catch (e) {
				rez = { error: TRX_ADDONS_STORAGE['ajax_error']+':<br>'+response };
				console.log(response);
			}
			if (rez.error === '') {
				jQuery(ocdi_button).removeClass('disable').remove();
				jQuery( '.js-ocdi-import-data' ).show().click();
			}
			else {
				console.log(rez.error);
			}
		});
	});
});