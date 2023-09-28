/* global jQuery:false */
/* global PUBZINNE_STORAGE:false */

(function() {
	"use strict";

	jQuery( document ).on(
		'action.ready_pubzinne', function() {

			// CF7 checkboxes and radio - add class to correct check/uncheck pseudoelement when input at right side of the label
			jQuery( '.wpcf7-checkbox > .wpcf7-list-item > .wpcf7-list-item-label,.wpcf7-radio > .wpcf7-list-item > .wpcf7-list-item-label' ).each(
				function() {
					if (jQuery( this ).next( 'input[type="checkbox"],input[type="radio"]' ).length > 0) {
						jQuery( this ).addClass( 'wpcf7-list-item-right' );
					}
				}
			);
			jQuery( '.wpcf7-checkbox > .wpcf7-list-item > .wpcf7-list-item-label,.wpcf7-radio > .wpcf7-list-item > .wpcf7-list-item-label,.wpcf7-wpgdprc > .wpcf7-list-item > .wpcf7-list-item-label' ).on(
				'click', function() {
					var chk = jQuery( this ).siblings( 'input[type="checkbox"],input[type="radio"]' );
					if (chk.attr( 'type' ) == 'radio') {
						jQuery( this ).parents( '.wpcf7-radio' )
						.find( '.wpcf7-list-item-label' ).removeClass( 'wpcf7-list-item-checked' )
						.find( 'input[type="radio"]' ).each(
							function(){
								this.checked = false;
							}
						);
					}
					if (chk.length > 0) {
						chk.get( 0 ).checked = chk.get( 0 ).checked ? false : true;
						jQuery( this ).toggleClass( 'wpcf7-list-item-checked', chk.get( 0 ).checked );
						chk.trigger('change');
					}
				}
			);
		}
	);

})();
