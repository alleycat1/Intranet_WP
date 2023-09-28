/* global jQuery */

jQuery(document).on('action.ready_trx_addons', function() {
	"use strict";
	var count_mc4wp = 0;
	jQuery( '.mc4wp-form-fields' ).each( function () {
		count_mc4wp ++;
		if ( count_mc4wp > 1 ) {
			jQuery(this).find('input[type="checkbox"]').each(function () {
				var id = jQuery(this).attr( 'id' );
				jQuery(this)
					.attr('id', id+'_'+count_mc4wp)
					.next('label').attr('for', id+'_'+count_mc4wp);
			});
		}
	});
});
