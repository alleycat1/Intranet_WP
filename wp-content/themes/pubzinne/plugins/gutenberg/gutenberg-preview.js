/* global jQuery:false */
/* global PUBZINNE_STORAGE:false */

jQuery( window ).load(function() {
	"use strict";
	pubzinne_gutenberg_first_init();
	// Create the observer to reinit visual editor after switch from code editor to visual editor
	var pubzinne_observers = {};
	if (typeof window.MutationObserver !== 'undefined') {
		pubzinne_create_observer('check_visual_editor', jQuery('.block-editor').eq(0), function(mutationsList) {
			var gutenberg_editor = jQuery('.edit-post-visual-editor:not(.pubzinne_inited)').eq(0);
			if (gutenberg_editor.length > 0) pubzinne_gutenberg_first_init();
		});
	}

	function pubzinne_gutenberg_first_init() {
		var gutenberg_editor = jQuery( '.edit-post-visual-editor:not(.pubzinne_inited)' ).eq( 0 );

		if ( 0 == gutenberg_editor.length ) {
			return;
		}
		
		// Add color scheme to the wrapper (instead '.editor-block-list__layout')
		jQuery( '.block-editor-writing-flow' ).addClass( 'scheme_' + PUBZINNE_STORAGE['color_scheme'] );
		gutenberg_editor.addClass( 'scheme_' + PUBZINNE_STORAGE['color_scheme'] );
		
		// Decorate sidebar placeholder
		gutenberg_editor.addClass( 'sidebar_position_' + PUBZINNE_STORAGE['sidebar_position'] );
		gutenberg_editor.addClass( PUBZINNE_STORAGE['expand_content'] + '_content' );
		if ( PUBZINNE_STORAGE['sidebar_position'] == 'left' ) {
			gutenberg_editor.prepend( '<div class="editor-post-sidebar-holder"></div>' );
		} else if ( PUBZINNE_STORAGE['sidebar_position'] == 'right' ) {
			gutenberg_editor.append( '<div class="editor-post-sidebar-holder"></div>' );
		}

		gutenberg_editor.addClass('pubzinne_inited');
	}

	// Create mutations observer
	function pubzinne_create_observer(id, obj, callback) {
		if (typeof window.MutationObserver !== 'undefined' && obj.length > 0) {
			if (typeof pubzinne_observers[id] == 'undefined') {
				pubzinne_observers[id] = new MutationObserver(callback);
				pubzinne_observers[id].observe(obj.get(0), { attributes: false, childList: true, subtree: true });
			}
			return true;
		}
		return false;
	}
} );
