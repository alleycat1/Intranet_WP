/* global jQuery:false */
/* global PUBZINNE_STORAGE:false */

jQuery( document ).ready( function() {

	"use strict";

	// Switch an active skin
	jQuery( '#trx_addons_theme_panel_section_skins a.trx_addons_image_block_link_choose_skin' ).on(
		'click', function(e) {
			var link = jQuery( this );
			trx_addons_msgbox_confirm(
				PUBZINNE_STORAGE['msg_switch_skin'],
				PUBZINNE_STORAGE['msg_switch_skin_caption'],
				function(btn) {
					if ( btn != 1 ) return;
					pubzinne_skins_action( 'switch', link.data( 'skin' ) );
				}
			);
			e.preventDefault();
			return false;
		}
	);

	// Download a free skin
	jQuery( '#trx_addons_theme_panel_section_skins a.trx_addons_image_block_link_download_skin' ).on(
		'click', function(e) {
			var link = jQuery( this );
			trx_addons_msgbox_confirm(
				PUBZINNE_STORAGE['msg_download_skin'],
				PUBZINNE_STORAGE['msg_download_skin_caption'],
				function(btn) {
					if ( btn != 1 ) return;
					pubzinne_skins_action( 'download', link.data( 'skin' ), '', link );
				}
			);
			e.preventDefault();
			return false;
		}
	);

	// Download a prepaid skin
	jQuery( '#trx_addons_theme_panel_section_skins a.trx_addons_image_block_link_buy_skin' ).on(
		'click', function(e) {
			var link = jQuery( this );
			trx_addons_msgbox_dialog(
				'<p>' + PUBZINNE_STORAGE['msg_buy_skin'].replace('#', link.data('buy')) + '</p>'
				+ '<p><label><input class="pubzinne_skin_code" type="text" placeholder="' + PUBZINNE_STORAGE['msg_buy_skin_placeholder'] + '"></label></p>',
				PUBZINNE_STORAGE['msg_buy_skin_caption'],
				null,
				function(btn, dialog) {
					if ( btn != 1 ) return;
					pubzinne_skins_action( 'buy', link.data( 'skin' ), dialog.find('.pubzinne_skin_code').val(), link );
				}
			);
			e.preventDefault();
			return false;
		}
	);

	// Update skin
	jQuery( '#trx_addons_theme_panel_section_skins a.trx_addons_image_block_link_update_skin' ).on(
		'click', function(e) {
			var link = jQuery( this );
			trx_addons_msgbox_confirm(
				PUBZINNE_STORAGE['msg_update_skin'],
				PUBZINNE_STORAGE['msg_update_skin_caption'],
				function(btn) {
					if ( btn != 1 ) return;
					pubzinne_skins_action( 'update', link.data( 'skin' ), '', link );
				}
			);
			e.preventDefault();
			return false;
		}
	);

	// Update skins from 'update-core' screen
	var need_update = false;
	jQuery( '.pubzinne_upgrade_skins_button:not([disabled])' ).on(
		'click', function(e) {
			var button = jQuery(this),
				checked = button.parents( '.pubzinne_upgrade_skins' ).find( 'input[name="checked[]"]:checked' );
			if ( checked.length > 0 ) {
				if ( need_update === false ) {
					need_update = checked.length;
				}
				jQuery( '.pubzinne_upgrade_skins_button' ).attr( 'disabled', 'disabled' );
				checked.each( function() {
					var chk = jQuery(this);
					if ( chk.get(0).checked ) {
						chk.hide().after( '<div class="pubzinne_upgrade_skins_status_wrap"><span class="pubzinne_upgrade_skins_status pubzinne_upgrade_skins_status_progress"></span></div>' );
						pubzinne_skins_action( 'update', chk.val(), '', '', function(skin, action, rez) {
							need_update--;
							chk.get(0).checked = false;
							chk.eq(0).removeAttr('checked');
							chk.next().find('.pubzinne_upgrade_skins_status')
								.removeClass( 'pubzinne_upgrade_skins_status_progress' )
								.addClass( 'pubzinne_upgrade_skins_status_' + ( rez.error ? 'error' : 'success' ) );
							button.trigger( 'click' );
						} );
					}
				});
			} else {
				if ( need_update === 0 ) {
					jQuery( '.pubzinne_upgrade_skins' ).after(
						'<div class="trx_addons_info_box trx_addons_info_box">'
							+ PUBZINNE_STORAGE['msg_update_skins_result']
						+ '</div>'
					);
					jQuery( '.pubzinne_upgrade_skins_button' ).removeAttr( 'disabled' );
				}
			}
			e.preventDefault();
			return false;
		}
	);


	// Callback when skin is loaded successful
	function pubzinne_skins_action( action, skin, code, button, callback ){
		if ( button ) {
			button.addClass( 'trx_addons_loading' );
		}
		jQuery.post(
			PUBZINNE_STORAGE['ajax_url'], {
				'action': 'pubzinne_'+action+'_skin',
				'skin': skin,
				'code': code === undefined ? '' : code,
				'nonce': PUBZINNE_STORAGE['ajax_nonce']
			},
			function(response){
				var rez = {};
				if ( button ) {
					button.removeClass( 'trx_addons_loading' );
				}
				if (response === '' || response === 0) {
					rez = { error: PUBZINNE_STORAGE['msg_ajax_error'] };
				} else {
					try {
						rez = JSON.parse( response );
					} catch (e) {
						rez = { error: PUBZINNE_STORAGE['msg_ajax_error'] };
						console.log( response );
					}
				}
				if ( callback !== undefined ) {
					callback(skin, action, rez);
				}
				// Show result
				if (jQuery('.trx_addons_theme_panel').length > 0) {
					if ( rez.error ) {
						trx_addons_msgbox_warning( rez.error, PUBZINNE_STORAGE['msg_'+action+'_skin_error_caption'] );
					} else {
						trx_addons_msgbox_success( PUBZINNE_STORAGE['msg_'+action+'_skin_success'], PUBZINNE_STORAGE['msg_'+action+'_skin_success_caption'] );
					}
					// Reload current page after the skin is switched (if success)
					if (rez.error === '') {
						if (jQuery('.trx_addons_theme_panel .trx_addons_tabs').hasClass('trx_addons_panel_wizard')) {
							trx_addons_set_cookie('trx_addons_theme_panel_wizard_section', 'trx_addons_theme_panel_section_skins');
						} else {
							if ( location.hash != 'trx_addons_theme_panel_section_skins' ) {
								pubzinne_document_set_location( location.href.split('#')[0] + '#' + 'trx_addons_theme_panel_section_skins' );
							}
						}
						location.reload( true );
					}
				}
			}
		);
	}

} );
