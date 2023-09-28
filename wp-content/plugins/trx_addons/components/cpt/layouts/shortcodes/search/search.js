/* global jQuery, TRX_ADDONS_STORAGE */

jQuery(document).on('action.ready_trx_addons', function() {
	"use strict";

	if (jQuery('.search_wrap:not(.inited)').length > 0) {
		jQuery('.search_wrap:not(.inited)').each(function() {
			var search_wrap = jQuery(this).addClass('inited'),
				search_field = search_wrap.find('.search_field'),
				search_button = search_wrap.find('.search_submit');
			var ajax_timer = null;

			search_field.on('keyup', function(e) {
				// ESC is pressed
				if (e.keyCode == 27) {
					search_field.val('');
					trx_addons_search_close(search_wrap);
					e.preventDefault();
					return;
				}
				// AJAX search
				if (search_wrap.hasClass('search_ajax')) {
					var s = search_field.val();
					if (ajax_timer) {
						clearTimeout(ajax_timer);
						ajax_timer = null;
					}
					if (s.length >= 4) {
						ajax_timer = setTimeout(function() {
							search_wrap.addClass('search_progress');
							jQuery.post(TRX_ADDONS_STORAGE['ajax_url'], {
								action: 'ajax_search',
								nonce: TRX_ADDONS_STORAGE['ajax_nonce'],
								text: s,
								post_types: search_wrap.find('input[name="post_types"]').val()
							}).done(function(response) {
								clearTimeout(ajax_timer);
								ajax_timer = null;
								var rez = {};
								if (response=='' || response==0) {
									rez = { error: TRX_ADDONS_STORAGE['msg_search_error'] };
								} else {
									try {
										rez = JSON.parse(response);
									} catch (e) {
										rez = { error: TRX_ADDONS_STORAGE['msg_search_error'] };
										console.log(response);
									}
								}
								var msg = rez.error === '' ? rez.data : rez.error;
								search_wrap.removeClass('search_progress');
								search_field.parents('.search_ajax').find('.search_results_content').empty().append(msg);
								search_field.parents('.search_ajax').find('.search_results').fadeIn();
								jQuery( document ).trigger( 'action.got_ajax_response', {
									action: 'ajax_search',
									result: rez
								});
							});
						}, 500);
					}
				}
			});

			// Click "Search submit"
			search_wrap.find('.search_submit').on('click', function(e) {
				e.preventDefault();
				if ( (search_wrap.hasClass('search_style_expand') || search_wrap.hasClass('search_style_fullscreen')) && !search_wrap.hasClass('search_opened') ) {
					var duration = trx_addons_apply_filters( 'trx_addons_filter_search_fullscreen_fade_duration', 0 );
					if ( search_wrap.hasClass('search_style_fullscreen') ) {
						jQuery('body').addClass('sc_layouts_search_opened');
						search_wrap.hide().addClass('search_opened').fadeIn( duration );
					} else {
						search_wrap.addClass('search_opened');
					}
					setTimeout( function() {
						search_field.get(0).focus();
					}, duration + 200 );
				} else if ( search_field.val() === '' ) {
					if ( search_wrap.hasClass('search_opened') && search_wrap.hasClass('search_style_expand') ) {
						trx_addons_search_close(search_wrap);
					} else {
						search_field.get(0).focus();
					}
				} else {
					search_wrap.find('form').get(0).submit();
				}
				return false;
			});
			// Click "Search close"
			search_wrap.find('.search_close').on('click', function(e) {
				e.preventDefault();
				trx_addons_search_close(search_wrap);
				return false;
			});
			// Click "Close search results"
			search_wrap.find('.search_results_close').on('click', function(e) {
				e.preventDefault();
				jQuery(this).parent().fadeOut();
				return false;
			});
			// Click "More results"
			search_wrap.on('click', '.search_more', function(e) {
				e.preventDefault();
				if (search_field.val() !== '') {
					search_wrap.find('form').get(0).submit();
				}
				return false;
			});
		});
	}
	
	// Close search field (remove class 'search_opened' and close search results)
	function trx_addons_search_close(search_wrap) {
		var duration = trx_addons_apply_filters( 'trx_addons_filter_search_fullscreen_fade_duration', 0 );
		search_wrap.find('.search_field').get(0).blur();
		if ( search_wrap.hasClass('search_style_fullscreen') ) {
			jQuery('body').removeClass('sc_layouts_search_opened');
			search_wrap.find('.search_results').fadeOut( duration );
			search_wrap.fadeTo( duration / 3 * 2, 0.33, function() {
				search_wrap
					.removeClass( 'search_opened' )
					.removeAttr( 'style' )
					.show();
			} );
		} else {
			search_wrap
				.removeClass('search_opened')
				.find('.search_results')
					.fadeOut();
		}
	}

});