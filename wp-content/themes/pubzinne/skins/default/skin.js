/* global jQuery:false */
/* global PUBZINNE_STORAGE:false */

(function() {
	"use strict";

	// Init skin-specific actions on first run
	// Attention! Don't forget to add the class "inited" and check it to prevent re-initialize the elements
	jQuery( document ).on(
		'action.ready_pubzinne', function() {

		}
	);

	// Init skin-specific hidden elements when their parent container becomes visible
	// Attention! Don't forget to add the class "inited" and check it to prevent re-initialize the elements
	jQuery( document ).on(
		'action.init_hidden_elements', function() {

		}
	);

	// Skin-specific scroll actions
	jQuery( document ).on(
		'action.scroll_pubzinne', function() {

		}
	);

	// Skin-specific resize actions
	jQuery( document ).on(
		'action.resize_pubzinne', function() {

		}
	);

    jQuery( document ).on(
        'action.ready_pubzinne', function() {
            // Process Tribe Events view after it was reloaded by AJAX
            jQuery('.tribe-events-view').on( 'beforeAjaxComplete.tribeEvents beforeAjaxSuccess.tribeEvents beforeAjaxError.tribeEvents', pubzinne_tribe_events_after_ajax );
            function pubzinne_tribe_events_after_ajax( jqXHR, textStatus ) {
                setTimeout( function() {
                    // Set up event handler again because .tribe-events-view was recreated after AJAX
                    jQuery('.tribe-events-view').on( 'beforeAjaxComplete.tribeEvents beforeAjaxSuccess.tribeEvents beforeAjaxError.tribeEvents', pubzinne_tribe_events_after_ajax );
                    // ToDo: Any actions after the Tribe Events View is reloaded
                    if (PUBZINNE_STORAGE['button_hover'] && PUBZINNE_STORAGE['button_hover'] != 'default') {
						if (PUBZINNE_STORAGE['button_hover'] != 'arrow') {
							jQuery(
								'.tribe-events-c-ical__link:not([class*="sc_button_hover_"]),\
								.tribe-events-c-search__button:not([class*="sc_button_hover_"])\
							'
							).addClass( 'sc_button_hover_just_init sc_button_hover_' + PUBZINNE_STORAGE['button_hover'] );
						}
                    }
                }, 10 );
            }
        }
    );

})();
