// Popup msgbox
//-----------------------------------------------------------------
(function() {
	"use strict";

	var msgbox_callback = null,
		msgbox_timeout = 5000;

	jQuery('body').on('click', '#trx_addons_modal_bg:not(.trx_addons_dialog_bg),.trx_addons_msgbox .trx_addons_msgbox_close', function (e) {
		trx_addons_msgbox_destroy();
		if (msgbox_callback) {
			msgbox_callback(0);
			msgbox_callback = null;
		}
		e.preventDefault();
		return false;
	});


	// Warning
	window.trx_addons_msgbox_warning = function(msg) {
		var hdr  = arguments.length > 1 ? arguments[1] : '';
		var icon = arguments.length > 2 ? arguments[2] : 'delete';
		var delay = arguments.length > 3 ? arguments[3] : msgbox_timeout;
		var buttons = arguments.length > 4 ? arguments[4] : [];
		return trx_addons_msgbox({
			msg: msg,
			hdr: hdr,
			icon: icon,
			type: 'warning',
			delay: delay,
			buttons: buttons,
			callback: null
		});
	};

	// Success
	window.trx_addons_msgbox_success = function(msg) {
		var hdr  = arguments.length > 1 ? arguments[1] : '';
		var icon = arguments.length > 2 ? arguments[2] : 'ok';
		var delay = arguments.length > 3 ? arguments[3] : msgbox_timeout;
		var buttons = arguments.length > 4 ? arguments[4] : [];
		return trx_addons_msgbox({
			msg: msg,
			hdr: hdr,
			icon: icon,
			type: 'success',
			delay: delay,
			buttons: buttons,
			callback: null
		});
	};

	// Info
	window.trx_addons_msgbox_info = function(msg) {
		var hdr  = arguments.length > 1 ? arguments[1] : '';
		var icon = arguments.length > 2 ? arguments[2] : 'info';
		var delay = arguments.length > 3 ? arguments[3] : msgbox_timeout;
		var buttons = arguments.length > 4 ? arguments[4] : [];
		return trx_addons_msgbox({
			msg: msg,
			hdr: hdr,
			icon: icon,
			type: 'info',
			delay: delay,
			buttons: buttons,
			callback: null
		});
	};

	// Regular
	window.trx_addons_msgbox_regular = function(msg) {
		var hdr  = arguments.length > 1 ? arguments[1] : '';
		var icon = arguments.length > 2 ? arguments[2] : 'quote-right';
		var delay = arguments.length > 3 ? arguments[3] : msgbox_timeout;
		var buttons = arguments.length > 4 ? arguments[4] : [];
		return trx_addons_msgbox({
			msg: msg,
			hdr: hdr,
			icon: icon,
			type: 'regular',
			delay: delay,
			buttons: buttons,
			callback: null
		});
	};

	// YesNo dialog
	window.trx_addons_msgbox_yesno = function(msg) {
		var hdr  = arguments.length > 1 ? arguments[1] : '';
		var callback = arguments.length > 2 ? arguments[2] : null;
		var buttons = arguments.length > 3 ? arguments[3] : [ TRX_ADDONS_STORAGE['msg_caption_yes'], TRX_ADDONS_STORAGE['msg_caption_no'] ];
		return trx_addons_msgbox({
			msg: msg,
			hdr: hdr,
			icon: 'help',
			type: 'regular',
			delay: 0,
			buttons: buttons,
			callback: callback
		});
	};

	// Confirm dialog
	window.trx_addons_msgbox_confirm = function(msg) {
		var hdr  = arguments.length > 1 ? arguments[1] : '';
		var callback = arguments.length > 2 ? arguments[2] : null;
		var buttons = arguments.length > 3 ? arguments[3] : [ TRX_ADDONS_STORAGE['msg_caption_ok'], TRX_ADDONS_STORAGE['msg_caption_cancel'] ];
		return trx_addons_msgbox({
			msg: msg,
			hdr: hdr,
			icon: 'attention',
			type: 'regular',
			delay: 0,
			buttons: buttons,
			callback: callback
		});
	};

	// Agree dialog
	window.trx_addons_msgbox_agree = function(msg) {
		var hdr  = arguments.length > 1 ? arguments[1] : '';
		var callback = arguments.length > 2 ? arguments[2] : null;
		var buttons = arguments.length > 3 ? arguments[3] : [ TRX_ADDONS_STORAGE['msg_caption_ok'], TRX_ADDONS_STORAGE['msg_caption_cancel'] ];
		return trx_addons_msgbox({
			msg: msg,
			hdr: hdr,
			icon: 'help',
			type: 'warning',
			delay: 0,
			buttons: buttons,
			callback: callback
		});
	};

	// Modal dialog
	window.trx_addons_msgbox_dialog = function(content) {
		var hdr  = arguments.length > 1 ? arguments[1] : '';
		var init = arguments.length > 2 ? arguments[2] : null;
		var callback = arguments.length > 3 ? arguments[3] : null;
		var buttons = arguments.length > 4 ? arguments[4] : [ TRX_ADDONS_STORAGE['msg_caption_apply'], TRX_ADDONS_STORAGE['msg_caption_cancel'] ];
		var icon = arguments.length > 5 ? arguments[5] : 'cog';
		return trx_addons_msgbox({
			msg: content,
			hdr: hdr,
			icon: icon,
			type: 'regular',
			delay: 0,
			buttons: buttons,
			init: init,
			callback: callback
		});
	};

	// General msgbox window
	window.trx_addons_msgbox = function(opt) {
		var msg = opt.msg != undefined ? opt.msg : '';
		var hdr  = opt.hdr != undefined ? opt.hdr : '';
		var icon = opt.icon != undefined ? opt.icon : '';
		var type = opt.type != undefined ? opt.type : 'regular';
		var delay = opt.delay != undefined ? opt.delay : msgbox_timeout;
		var buttons = opt.buttons != undefined ? opt.buttons : [];
		var init = opt.init != undefined ? opt.init : null;
		var callback = opt.callback != undefined ? opt.callback : null;
		// Modal bg
		if (jQuery('#trx_addons_modal_bg').length === 0) {
			jQuery('body').append('<div id="trx_addons_modal_bg"></div>');
		}
		jQuery('#trx_addons_modal_bg').toggleClass('trx_addons_dialog_bg', buttons.length > 0).fadeIn();
		// Popup window
		jQuery('.trx_addons_msgbox').remove();
		var html = '<div class="trx_addons_msgbox trx_addons_msgbox_' + type
				+ (buttons.length > 0 ? ' trx_addons_msgbox_dialog' : '')
				+ (icon && !hdr ? ' trx_addons_msgbox_simple' : '')
			+ '">'
			+ '<span class="trx_addons_msgbox_close trx_addons_icon-cancel-2"></span>'
			+ (hdr ? '<h5 class="trx_addons_msgbox_header">'+hdr+'</h5>' : '')
			+ (icon ? '<span class="trx_addons_msgbox_icon trx_addons_icon-'+icon+'"></span>' : '')
			+ '<div class="trx_addons_msgbox_body">' + msg + '</div>';
		if (buttons.length > 0) {
			html += '<div class="trx_addons_msgbox_buttons">';
			for (var i=0; i<buttons.length; i++) {
				html += '<span class="trx_addons_msgbox_button">'+buttons[i]+'</span>';
			}
			html += '</div>';
		}
		html += '</div>';
		// Add msgbox to body
		jQuery('body').append(html);
		var msgbox = jQuery('body .trx_addons_msgbox').eq(0);
		// Prepare callback on buttons click
		if ( callback !== null ) {
			msgbox_callback = callback;
		}
		jQuery('.trx_addons_msgbox_button').on('click', function(e) {
			var btn = jQuery(this).index();
			if ( callback ) {
				callback( btn+1, msgbox );
				msgbox_callback = null;
			}
			trx_addons_msgbox_destroy();
		});
		// Call init function
		if (init !== null) init(msgbox);
		// Show (animate) msgbox
		setTimeout( function() {
			msgbox.addClass('show');
		}, 10 );
		// Delayed destroy (if need)
		if (delay > 0) {
			setTimeout( function() { trx_addons_msgbox_destroy(); }, delay );
		}
		return msgbox;
	};

	// Destroy msgbox window
	window.trx_addons_msgbox_destroy = function() {
		jQuery('#trx_addons_modal_bg').fadeOut();
		jQuery('.trx_addons_msgbox').removeClass('show');
		setTimeout( function() {
			jQuery('#trx_addons_modal_bg').remove();
			jQuery('.trx_addons_msgbox').remove();
		}, 500 );
	};

})();
