/* global jQuery, TRX_ADDONS_STORAGE */

jQuery( document ).ready( function() {

	"use strict";

	// Download a free addon
	jQuery( '#trx_addons_theme_panel_section_addons a.trx_addons_image_block_link_download_addon' ).on(
		'click', function(e) {
			var link = jQuery( this );
			trx_addons_msgbox_confirm(
				TRX_ADDONS_STORAGE['msg_download_addon'],
				TRX_ADDONS_STORAGE['msg_download_addon_caption'],
				function(btn) {
					if ( btn != 1 ) return;
					trx_addons_addons_action( 'download', link.data( 'addon' ), '', link );
				}
			);
			e.preventDefault();
			return false;
		}
	);

	// Update addon
	jQuery( '#trx_addons_theme_panel_section_addons a.trx_addons_image_block_link_update_addon' ).on(
		'click', function(e) {
			var link = jQuery( this );
			trx_addons_msgbox_confirm(
				TRX_ADDONS_STORAGE['msg_update_addon'],
				TRX_ADDONS_STORAGE['msg_update_addon_caption'],
				function(btn) {
					if ( btn != 1 ) return;
					trx_addons_addons_action( 'update', link.data( 'addon' ), '', link );
				}
			);
			e.preventDefault();
			return false;
		}
	);

	// Dectivate addon
	jQuery( '#trx_addons_theme_panel_section_addons a.trx_addons_image_block_link_deactivate_addon' ).on(
		'click', function(e) {
			var link = jQuery( this );
			trx_addons_addons_action( 'deactivate', link.data( 'addon' ), '', link );
			e.preventDefault();
			return false;
		}
	);

	// Activate addon
	jQuery( '#trx_addons_theme_panel_section_addons a.trx_addons_image_block_link_activate_addon' ).on(
		'click', function(e) {
			var link = jQuery( this );
			trx_addons_addons_action( 'activate', link.data( 'addon' ), '', link );
			e.preventDefault();
			return false;
		}
	);

	// Update addons from 'update-core' screen
	var need_update = false;
	jQuery( '.trx_addons_upgrade_addons_button:not([disabled])' ).on(
		'click', function(e) {
			var button = jQuery(this),
				checked = button.parents( '.trx_addons_upgrade_addons' ).find( 'input[name="checked[]"]:checked' );
			if ( checked.length > 0 ) {
				if ( need_update === false ) {
					need_update = checked.length;
				}
				jQuery( '.trx_addons_upgrade_addons_button' ).attr( 'disabled', 'disabled' );
				checked.each( function() {
					var chk = jQuery(this);
					if ( chk.get(0).checked ) {
						chk.hide().after( '<div class="trx_addons_upgrade_addons_status_wrap"><span class="trx_addons_upgrade_addons_status trx_addons_upgrade_addons_status_progress"></span></div>' );
						trx_addons_addons_action( 'update', chk.val(), '', '', function(addon, action, rez) {
							need_update--;
							chk.get(0).checked = false;
							chk.eq(0).removeAttr('checked');
							chk.next().find('.trx_addons_upgrade_addons_status')
								.removeClass( 'trx_addons_upgrade_addons_status_progress' )
								.addClass( 'trx_addons_upgrade_addons_status_' + ( rez.error ? 'error' : 'success' ) );
							button.trigger( 'click' );
						} );
					}
				});
			} else {
				if ( need_update === 0 ) {
					var success = button.parents( '.trx_addons_upgrade_addons' ).find( '.trx_addons_upgrade_addons_status_success' ).length,
						failed  = button.parents( '.trx_addons_upgrade_addons' ).find( '.trx_addons_upgrade_addons_status_error' ).length,
						result  = success > 0 && failed === 0 ? 'success' : ( success === 0 && failed > 0 ? 'error' : 'warning' );
					jQuery( '.trx_addons_upgrade_addons' ).after(
						'<div class="trx_addons_info_box trx_addons_info_box_' + result + '">'
							+ TRX_ADDONS_STORAGE['msg_update_addons_' + result ]
						+ '</div>'
					);
					jQuery( '.trx_addons_upgrade_addons_button' ).removeAttr( 'disabled' );
				}
			}
			e.preventDefault();
			return false;
		}
	);


	// Callback when addon is loaded successful
	function trx_addons_addons_action( action, addon, code, button, callback ){
		if ( button ) {
			button.addClass( 'trx_addons_loading' );
		}
		jQuery.post(
			TRX_ADDONS_STORAGE['ajax_url'], {
				'action': 'trx_addons_'+action+'_addon',
				'addon': addon,
				'code': code === undefined ? '' : code,
				'nonce': TRX_ADDONS_STORAGE['ajax_nonce']
			},
			function(response){
				var rez = {};
				if ( button ) {
					button.removeClass( 'trx_addons_loading' );
				}
				if (response === '' || response === 0) {
					rez = { error: TRX_ADDONS_STORAGE['msg_ajax_error'] };
				} else {
					try {
						rez = JSON.parse( response );
					} catch (e) {
						rez = { error: TRX_ADDONS_STORAGE['msg_ajax_error'] };
						console.log( response );
					}
				}
				if ( callback !== undefined ) {
					callback(addon, action, rez);
				}
				// Show result
				if (jQuery('.trx_addons_theme_panel').length > 0) {
					if ( rez.error ) {
						trx_addons_msgbox_warning( rez.error, TRX_ADDONS_STORAGE['msg_'+action+'_addon_error_caption'] );
					} else {
						trx_addons_msgbox_success( TRX_ADDONS_STORAGE['msg_'+action+'_addon_success'], TRX_ADDONS_STORAGE['msg_'+action+'_addon_success_caption'] );
					}
					// Reload current page after the addon is switched (if success)
					if (rez.error === '') {
						if (jQuery('.trx_addons_theme_panel .trx_addons_tabs').hasClass('trx_addons_panel_wizard')) {
							trx_addons_set_cookie('trx_addons_theme_panel_wizard_section', 'trx_addons_theme_panel_section_general');
						} else {
							if ( location.hash != 'trx_addons_theme_panel_section_addons' ) {
								trx_addons_document_set_location( location.href.split('#')[0] + '#' + 'trx_addons_theme_panel_section_addons' );
							}
						}
						location.reload( true );
					}
				}
			}
		);
	}

} );
