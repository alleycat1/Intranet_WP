/* global jQuery:false, elementor:false */

jQuery( document ).ready(
	function() {
		"use strict";

		// Add color_style to the titles
		pubzinne_add_filter('trx_addons_filter_sc_classes', function(classes, settings) {
			if (typeof settings.scheme != 'undefined' && settings.scheme != 'inherit' ) {
				classes += ' scheme_' + settings.scheme;
			}
			if (typeof settings.color_style != 'undefined') {
				classes += ' color_style_' + settings.color_style;
			}
			return classes;
		});

		// Reload preview after any page setting is changed
		if (window.elementor !== undefined) {
			var timer        = null;
			var save_options = _.throttle( function() { elementor.saver.doAutoSave(); }, 3000, {leading: false} );
			jQuery( '#elementor-panel' )
				.on( 'input change', '[data-setting*="pubzinne_options_"]', function (e) {
					var tab         = jQuery( '.elementor-panel-navigation-tab.elementor-active' ),
					tab_name        = tab.length > 0 ? tab.data( 'tab' ) : '',
					section         = jQuery( this ).parents( '.elementor-control' ).prevAll( '.elementor-control-type-section' ),
					section_classes = section.length > 0 ? section.attr( 'class' ).split( ' ' ) : [],
					section_name    = '';
					for (var i = 0; i < section_classes.length; i++) {
						if (section_classes[i].indexOf( 'elementor-control-section_' ) >= 0) {
							section_name = section_classes[i].replace( 'elementor-control-', '' );
							break;
						}
					}

					// Trigger Elementor's save procedure
					save_options();					// Save options after 3sec
					//elementor.saver.doAutoSave();	// Save immediately

					// Refresh Preview area and restore active tab
					if (tab.length > 0 && section_name !== '') {
						if (timer !== null) {
							clearTimeout( timer );
						}
						timer = setTimeout(
							function() {
								elementor.reloadPreview();
								elementor.once(
									'preview:loaded', function() {
										// Restore panel with the 'Page settings'
										var panel = jQuery( '#elementor-panel-footer-settings' );
										if (panel.length > 0) {
											panel.trigger( 'click' );
										}

										// Trigger 'click' on the last opened tab (if not first)
										tab = jQuery( '.elementor-panel-navigation-tab[data-tab="' + tab_name + '"]' );
										if (tab.length > 0 && tab.parent().find( '.elementor-panel-navigation-tab' ).eq( 0 ).data( 'tab' ) != tab_name) {
											tab.find( 'a' ).trigger( 'click' );
										}

										// Trigger 'click' on the last opened section (if not first)
										section = jQuery( '.elementor-control-' + section_name );
										if (section.length > 0 && ! section.parent().find( '.elementor-control' ).eq( 0 ).hasClass( 'elementor-control-' + section_name )) {
											section.trigger( 'click' );
										}
									}
								);
							}, 4500
						);	// Reload page after the AJAX-call 'Save page options' appear (Elementor call save options after 3000ms)
					}

					// Refresh link 'xxx_post_editor'
					var link = jQuery( this ).parents( '.elementor-control' ).find( 'a.pubzinne_post_editor' );
					if ( link.length > 0 ) {
						pubzinne_change_post_edit_link_elementor( link );
					}
				} )
				.on( 'click', '.pubzinne_post_editor', function(e) {
					pubzinne_change_post_edit_link_elementor( jQuery(this) );
					if (jQuery(this).hasClass('pubzinne_hidden' )) {
						e.preventDefault();
						return false;
					}
				});
		}

		function pubzinne_change_post_edit_link_elementor(a) {
			if (a.length > 0) {
				var sel = a.parents('.elementor-control').find('select'),
					val = sel.val();
				if (sel.length == 0 || val == null || val == 'inherit') {
					a.addClass( 'pubzinne_hidden' );
				} else {
					var id = ('' + val).split( '-' ).pop();
					a.attr( 'href', a.attr( 'href' ).replace( /post=[0-9]{1,5}/, "post=" + id ) );
					if (id == 0 || id == 'none') {
						a.addClass( 'pubzinne_hidden' );
					} else {
						a.removeClass( 'pubzinne_hidden' );
					}
				}
			}
		}
	}
);
