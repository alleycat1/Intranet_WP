/**
 * JS utilities
 *
 * @package ThemeREX Addons
 * @since v1.0
 */

/* global jQuery, TRX_ADDONS_STORAGE */

(function() {
	"use strict";

	var $window   = jQuery( window ),
		$document = jQuery( document ),
		$html_dom = document.querySelector('html'),
		$adminbar = jQuery( '#wpadminbar' ),
		$body     = jQuery( 'body' );


	/* Dev shorthands
	---------------------------------------------------------------- */

	window.dcl === undefined && ( window.dcl = function(s) { console.log(s); } );
	window.dcs === undefined && ( window.dcs = function() { console.trace(); } );
	window.dcts === undefined && ( window.dcts = function( name ) { console.time( name ? name : 'timer' ); } );
	window.dctl === undefined && ( window.dctl = function( name ) { console.timeLog( name ? name : 'timer' ); } );
	window.dcte === undefined && ( window.dcte = function( name ) { console.timeEnd( name ? name : 'timer' ); } );


	/* Cookies manipulations
	---------------------------------------------------------------- */
	
	window.trx_addons_get_cookie = function(name) {
		var defa = arguments[1] !== undefined ? arguments[1] : null;
		var start = document.cookie.indexOf(name + '=');
		var len = start + name.length + 1;
		if ((!start) && (name != document.cookie.substring(0, name.length))) {
			return defa;
		}
		if (start == -1) {
			return defa;
		}
		var end = document.cookie.indexOf(';', len);
		if (end == -1) {
			end = document.cookie.length;
		}
		return decodeURIComponent(document.cookie.substring(len, end));
	};
	
	
	window.trx_addons_set_cookie = function(name, value) {
		var expires  = arguments[2] !== undefined ? arguments[2] : 0;  // In ms ( sec * 1000 ). If 0 (by default) - store the value for the current session only
		var path     = arguments[3] !== undefined ? arguments[3] : '/';
		var domain   = arguments[4] !== undefined ? arguments[4] : '';
		var secure   = arguments[5] !== undefined ? arguments[5] : '';
		var samesite = arguments[6] !== undefined ? arguments[6] : 'strict';  // strict | lax | none
		var today    = new Date();
		today.setTime(today.getTime());
		var expires_date = new Date(today.getTime() + (expires * 1));
		document.cookie = encodeURIComponent(name) + '='
				+ encodeURIComponent(value)
				+ (expires ? ';expires=' + expires_date.toGMTString() : '')
				+ (path    ? ';path=' + path : '')
				+ (domain  ? ';domain=' + domain : '')
				+ (secure  ? ';secure' : '')
				+ (samesite  ? ';samesite=' + samesite : '');
	};
	
	
	window.trx_addons_del_cookie = function(name) {
		var path     = arguments[1] !== undefined ? arguments[1] : '/';
		var domain   = arguments[2] !== undefined ? arguments[2] : '';
		var secure   = arguments[3] !== undefined ? arguments[3] : '';
		var samesite = arguments[4] !== undefined ? arguments[4] : 'strict';  // strict | lax | none
		if ( trx_addons_get_cookie(name) ) {
			document.cookie = encodeURIComponent(name) + '='
				+ ';expires=Thu, 01-Jan-1970 00:00:01 GMT'
				+ (path ? ';path=' + path : '')
				+ (domain ? ';domain=' + domain : '')
				+ (secure  ? ';secure' : '')
				+ (samesite  ? ';samesite=' + samesite : '');
		}
	};

	
	/* Local storage manipulations
	---------------------------------------------------------------- */

	window.trx_addons_is_local_storage_exists = function() {
		try {
			return 'localStorage' in window && window['localStorage'] !== null;
		} catch (e) {
			return false;
		}		
	};
	
	window.trx_addons_get_storage = function(name) {
		var defa = arguments[1] !== undefined ? arguments[1] : null;
		var val = null;
		if (trx_addons_is_local_storage_exists()) {
			val = window['localStorage'].getItem(name);
			if (val === null) val = defa;
		} else {
			val = trx_addons_get_cookie(name, defa);
		}
		return val;
	};
	
	window.trx_addons_set_storage = function(name, value) {
		if (trx_addons_is_local_storage_exists())
			window['localStorage'].setItem(name, value);
		else 
			trx_addons_set_cookie(name, value, 365 * 24 * 60 * 60 * 1000);	// 1 year
	};
	
	window.trx_addons_del_storage = function(name) {
		if (trx_addons_is_local_storage_exists())
			window['localStorage'].removeItem(name);
		else 
			trx_addons_del_cookie(name);
	};
	
	window.trx_addons_clear_storage = function() {
		if (trx_addons_is_local_storage_exists())
			window['localStorage'].clear();
	};



	/* Clipboard manipulations
	---------------------------------------------------------------- */

	window.trx_addons_copy_to_clipboard = function(str, strip_tags) {
		var selected = document.getSelection().rangeCount > 0       // Check if there is any content selected previously
						? document.getSelection().getRangeAt(0)     // Store selection if found
						: false;                                    // Mark as false to know no selection existed before
		if ( typeof str == 'object' && str.length ) {
			str = str.is( 'textarea' ) ? str.val() : str.html();
		}
		if ( strip_tags ) {
			str = str.replace(/<[^>]+>/g, '');
		}
		var el = document.createElement('textarea');	// Create a <textarea> element
		el.value = str;									// Set its value to the string that you want copied
		el.setAttribute('readonly', '');				// Make it readonly to be tamper-proof
		el.style.position = 'absolute';                 // Move outside the screen to make it invisible
		el.style.left = '-9999px';
		document.body.appendChild(el);                  // Append the <textarea> element to the HTML document
		el.select();                                    // Select the <textarea> content
		document.execCommand('copy');                   // Copy - only works as a result of a user action (e.g. click events)
		document.body.removeChild(el);                  // Remove the <textarea> element
		if (selected) {                                 // If a selection existed before copying
			document.getSelection().removeAllRanges();  // Unselect everything on the HTML document
			document.getSelection().addRange(selected); // Restore the original selection
		}
	};



	/* ListBox and ComboBox manipulations
	---------------------------------------------------------------- */
	
	window.trx_addons_clear_listbox = function(box) {
		for (var i=box.options.length-1; i >= 0; i--)
			box.options[i] = null;
	};
	
	window.trx_addons_add_listbox_item = function(box, val, text) {
		var item = new Option();
		item.value = val;
		item.text = text;
		box.options.add(item);
	};
	
	window.trx_addons_del_listbox_item_by_value = function(box, val) {
		for (var i=0; i < box.options.length; i++) {
			if (box.options[i].value == val) {
				box.options[i] = null;
				break;
			}
		}
	};
	
	window.trx_addons_del_listbox_item_by_text = function(box, txt) {
		for (var i=0; i < box.options.length; i++) {
			if (box.options[i].text == txt) {
				box.options[i] = null;
				break;
			}
		}
	};
	
	window.trx_addons_find_listbox_item_by_value = function(box, val) {
		var idx = -1;
		for (var i=0; i < box.options.length; i++) {
			if (box.options[i].value == val) {
				idx = i;
				break;
			}
		}
		return idx;
	};
	
	window.trx_addons_find_listbox_item_by_text = function(box, txt) {
		var idx = -1;
		for (var i=0; i < box.options.length; i++) {
			if (box.options[i].text == txt) {
				idx = i;
				break;
			}
		}
		return idx;
	};
	
	window.trx_addons_select_listbox_item_by_value = function(box, val) {
		for (var i = 0; i < box.options.length; i++) {
			box.options[i].selected = (val == box.options[i].value);
		}
	};
	
	window.trx_addons_select_listbox_item_by_text = function(box, txt) {
		for (var i = 0; i < box.options.length; i++) {
			box.options[i].selected = (txt == box.options[i].text);
		}
	};
	
	window.trx_addons_get_listbox_values = function(box) {
		var delim = arguments[1] ? arguments[1] : ',';
		var str = '';
		for (var i=0; i < box.options.length; i++) {
			str += (str ? delim : '') + box.options[i].value;
		}
		return str;
	};
	
	window.trx_addons_get_listbox_texts = function(box) {
		var delim = arguments[1] ? arguments[1] : ',';
		var str = '';
		for (var i=0; i < box.options.length; i++) {
			str += (str ? delim : '') + box.options[i].text;
		}
		return str;
	};
	
	window.trx_addons_sort_listbox = function(box)  {
		var temp_opts = new Array();
		var temp = new Option();
		for(var i=0; i<box.options.length; i++)  {
			temp_opts[i] = box.options[i].clone();
		}
		for(var x=0; x<temp_opts.length-1; x++)  {
			for(var y=(x+1); y<temp_opts.length; y++)  {
				if (temp_opts[x].text > temp_opts[y].text)  {
					temp = temp_opts[x];
					temp_opts[x] = temp_opts[y];
					temp_opts[y] = temp;
				}  
			}  
		}
		for(i=0; i<box.options.length; i++)  {
			box.options[i] = temp_opts[i].clone();
		}
	};
	
	window.trx_addons_get_listbox_selected_index = function(box) {
		for (var i = 0; i < box.options.length; i++) {
			if (box.options[i].selected) {
				return i;
			}
		}
		return -1;
	};
	
	window.trx_addons_get_listbox_selected_value = function(box) {
		for (var i = 0; i < box.options.length; i++) {
			if (box.options[i].selected) {
				return box.options[i].value;
			}
		}
		return null;
	};
	
	window.trx_addons_get_listbox_selected_text = function(box) {
		for (var i = 0; i < box.options.length; i++) {
			if (box.options[i].selected) {
				return box.options[i].text;
			}
		}
		return null;
	};
	
	window.trx_addons_get_listbox_selected_option = function(box) {
		for (var i = 0; i < box.options.length; i++) {
			if (box.options[i].selected) {
				return box.options[i];
			}
		}
		return null;
	};
	
	
	
	/* Radio buttons manipulations
	---------------------------------------------------------------- */
	
	window.trx_addons_get_radio_value = function(radioGroupObj) {
		for (var i=0; i < radioGroupObj.length; i++) {
			if (radioGroupObj[i].checked) {
				return radioGroupObj[i].value;
			}
		}
		return null;
	};
	
	window.trx_addons_set_radio_checked_by_num = function(radioGroupObj, num) {
		for (var i=0; i < radioGroupObj.length; i++) {
			if (radioGroupObj[i].checked && i!=num) {
				radioGroupObj[i].checked=false;
			} else if (i==num) {
				radioGroupObj[i].checked=true;
			}
		}
	};
	
	window.trx_addons_set_radio_checked_by_value = function(radioGroupObj, val) {
		for (var i=0; i < radioGroupObj.length; i++) {
			if (radioGroupObj[i].checked && radioGroupObj[i].value!=val) {
				radioGroupObj[i].checked=false;
			} else if (radioGroupObj[i].value==val) {
				radioGroupObj[i].checked=true;
			}
		}
	};
	
	
	
	/* Form validation
	---------------------------------------------------------------- */
	
	/*
	// Usage example:
	var error = trx_addons_form_validate(jQuery(form_selector), {	// -------- Options ---------
		error_message_show: true,									// Display or not error message
		error_message_time: 5000,									// Time to display error message
		success_message_class: 'trx_addons_message_box_success',	// Should be removed from the error message block
		error_message_class: 'trx_addons_message_box_error',		// Class, appended to error message block
		error_message_text: 'Global error text',					// Global error message text (if not specify message in the checked field)
		error_fields_class: 'trx_addons_field_error',				// Class, appended to error fields
		exit_after_first_error: false,								// Cancel validation and exit after first error
		rules: [
			{
				field: 'author',																// Checking field name
				min_length: { value: 1,	 message: 'The author name can\'t be empty' },			// Min character count (0 - don't check), message - if error occurs
				max_length: { value: 60, message: 'Too long author name'}						// Max character count (0 - don't check), message - if error occurs
			},
			{
				field: 'email',
				min_length: { value: 7,	 message: 'Too short (or empty) email address' },
				max_length: { value: 60, message: 'Too long email address'},
				mask: { value: '^([a-zA-Z0-9_\\-]+\\.)*[a-zA-Z0-9_\\-]+@[a-zA-Z0-9_\\-]+(\\.[a-zA-Z0-9_\\-]+)*\\.[a-zA-Z0-9]{2,6}$', message: 'Invalid email address'}
			},
			{
				field: 'comment',
				min_length: { value: 1,	 message: 'The comment text can\'t be empty' },
				max_length: { value: 200, message: 'Too long comment'}
			},
			{
				field: 'pwd1',
				min_length: { value: 5,	 message: 'The password can\'t be less then 5 characters' },
				max_length: { value: 20, message: 'Too long password'}
			},
			{
				field: 'pwd2',
				equal_to: { value: 'pwd1',	 message: 'The passwords in both fields must be equals' }
			}
		]
	});
	*/
	
	window.trx_addons_form_validate = function(form, opt) {
		// Default options
		if (typeof(opt.error_message_show)=='undefined')		opt.error_message_show = true;
		if (typeof(opt.error_message_time)=='undefined')		opt.error_message_time = 5000;
		if (typeof(opt.error_message_class)=='undefined')		opt.error_message_class = 'trx_addons_message_box_error';
		if (typeof(opt.success_message_class)=='undefined')		opt.success_message_class = 'trx_addons_message_box_success';
		if (typeof(opt.error_message_text)=='undefined')		opt.error_message_text = 'Incorrect data in the fields!';
		if (typeof(opt.error_fields_class)=='undefined')		opt.error_fields_class = 'trx_addons_field_error';
		if (typeof(opt.exit_after_first_error)=='undefined')	opt.exit_after_first_error = false;
		// Validate fields
		var error_msg = '';
		form.find(":input").each(function() {
			if (error_msg !== '' && opt.exit_after_first_error) return;
			for (var i = 0; i < opt.rules.length; i++) {
				if (jQuery(this).attr("name") == opt.rules[i].field) {
					var val = jQuery(this).val();
					var error = false;
					if (typeof(opt.rules[i].min_length) == 'object') {
						if (opt.rules[i].min_length.value > 0 && val.length < opt.rules[i].min_length.value) {
							if (error_msg === '') jQuery(this).get(0).focus();
							error_msg += '<p class="trx_addons_error_item">' + (typeof(opt.rules[i].min_length.message)!='undefined' ? opt.rules[i].min_length.message : opt.error_message_text ) + '</p>';
							error = true;
						}
					}
					if ((!error || !opt.exit_after_first_error) && typeof(opt.rules[i].max_length) == 'object') {
						if (opt.rules[i].max_length.value > 0 && val.length > opt.rules[i].max_length.value) {
							if (error_msg === '') jQuery(this).get(0).focus();
							error_msg += '<p class="trx_addons_error_item">' + (typeof(opt.rules[i].max_length.message)!='undefined' ? opt.rules[i].max_length.message : opt.error_message_text ) + '</p>';
							error = true;
						}
					}
					if ((!error || !opt.exit_after_first_error) && typeof(opt.rules[i].mask) == 'object') {
						if (opt.rules[i].mask.value !== '') {
							var regexp = new RegExp(opt.rules[i].mask.value);
							if (!regexp.test(val)) {
								if (error_msg === '') jQuery(this).get(0).focus();
								error_msg += '<p class="trx_addons_error_item">' + (typeof(opt.rules[i].mask.message)!='undefined' ? opt.rules[i].mask.message : opt.error_message_text ) + '</p>';
								error = true;
							}
						}
					}
					if ((!error || !opt.exit_after_first_error) && typeof(opt.rules[i].state) == 'object') {
						if (opt.rules[i].state.value=='checked' && !jQuery(this).get(0).checked) {
							if (error_msg === '') jQuery(this).get(0).focus();
							error_msg += '<p class="trx_addons_error_item">' + (typeof(opt.rules[i].state.message)!='undefined' ? opt.rules[i].state.message : opt.error_message_text ) + '</p>';
							error = true;
						}
					}
					if ((!error || !opt.exit_after_first_error) && typeof(opt.rules[i].equal_to) == 'object') {
						if (opt.rules[i].equal_to.value !== '' && val!=jQuery(jQuery(this).get(0).form[opt.rules[i].equal_to.value]).val()) {
							if (error_msg === '') jQuery(this).get(0).focus();
							error_msg += '<p class="trx_addons_error_item">' + (typeof(opt.rules[i].equal_to.message)!='undefined' ? opt.rules[i].equal_to.message : opt.error_message_text ) + '</p>';
							error = true;
						}
					}
					if (opt.error_fields_class !== '') jQuery(this).toggleClass(opt.error_fields_class, error);
				}
	
			}
		});
		if (error_msg !== '' && opt.error_message_show) {
			var error_message_box = form.find(".trx_addons_message_box");
			if (error_message_box.length === 0) error_message_box = form.parent().find(".trx_addons_message_box");
			if (error_message_box.length === 0) {
				form.append('<div class="trx_addons_message_box"></div>');
				error_message_box = form.find(".trx_addons_message_box");
			} else
				error_message_box.removeClass(opt.success_message_class);
			if (opt.error_message_class) error_message_box.addClass(opt.error_message_class);
			error_message_box.html(error_msg).fadeIn();
			setTimeout(function() { error_message_box.fadeOut(); }, opt.error_message_time);
		}
		return error_msg !== '';
	};
	
	
	
	// Fill (refresh) list in specified field when parent fields is changed
	// -------------------------------------------------------------------------------------
	window.trx_addons_refresh_list = function(parent_type, parent_val, list_fld, list_lbl, list_not_selected) {

		// Need '- Select ... -'
		if (list_not_selected === undefined) {
			list_not_selected = list_fld.data('not-selected') === true 								// field has data-not-selected="true"
								|| list_fld.parents('.vc_edit-form-tab').length > 0					// or field in the VC shortcodes form
								|| list_fld.parents('#elementor-controls').length > 0				// or field in the Elementor shortcodes form
								|| list_fld.parents('[class*="widget_field_type_"]').length > 0		// or field in the new SOW form
								|| list_fld.parents('.widget-liquid-right').length > 0				// or field in the Widgets panel
								|| list_fld.parents('.widgets-holder-wrap').length > 0				// or field in the Widgets panel
								|| list_fld.parents('.customize-control-widget_form').length > 0;	// or field in the Widget in the Customizer
		}

		var list_val = list_fld.val();
		if (list_lbl.find('.trx_addons_refresh').length === 0) {
			list_lbl.append('<span class="trx_addons_refresh trx_addons_icon-spin3 animate-spin"></span>');
		}

		if ( parent_val ) {
			// Prepare data
			var data = {
				action: 'trx_addons_refresh_list',
				nonce: TRX_ADDONS_STORAGE['ajax_nonce'],
				parent_type: parent_type,
				parent_value: parent_val,
				list_not_selected: list_not_selected
			};

			jQuery.post(TRX_ADDONS_STORAGE['ajax_url'], data, function(response) {
				var rez = {};
				try {
					rez = JSON.parse(response);
				} catch (e) {
					rez = { error: TRX_ADDONS_STORAGE['msg_ajax_error'] };
					console.log(response);
				}
				if (rez.error === '') {
					trx_addons_refresh_field_items( rez.data );
				}
			});
		} else {
			var args = [];
			if ( list_not_selected ) {
				var list_type = list_fld.prop('tagName').toLowerCase();
				if ( list_type == 'select' ) {
					var opt = list_fld.find( 'option' ).eq(0);
					if ( opt.length > 0 ) {
						args[0] = {
							key: opt.val(),
							value: opt.text() 
						};
					}
				}
			}
			trx_addons_refresh_field_items( args );
		}

		function trx_addons_refresh_field_items( data ) {
			var opt_list = '';
			var list_type = list_fld.prop('tagName').toLowerCase();
			var list_name = list_type == 'select' ? list_fld.attr('name') : list_fld.data('field_name');
			if ( data.length === 0 ) {
				data = [ { key: 0, value: '' } ];
			}
			for (var i in data) {
				if (list_type != 'select' && data[i]['key'] == 0) continue;
				opt_list += list_type == 'select'
					? '<option class="'+data[i]['key']+'"'
								+ ' value="'+data[i]['key']+'"'
								+ (data[i]['key']==list_val ? ' selected="selected"' : '')
								+ '>'
								+ data[i]['value']
								+ '</option>'
					: '<label><input type="checkbox"'
								+ ' value="' + data[i]['key'] + '"'
								+ ' name="' + list_name + '"'
								+ '>'
							+ data[i]['value']
						+ '</label>';
			}
			list_fld
				.html(opt_list);				// Replace list of the elements in the selector
				//.data('items', data);		// Store items list for future usage
			if (list_type == 'select' && list_fld.find('option:selected').length === 0 && list_fld.find('option').length > 0) {
				list_fld.find('option').get(0).selected = true;
			}
			list_lbl.find('span.trx_addons_refresh').remove();
			list_fld.trigger('change');
		}
		return false;
	};
	
	
	
	/* Document manipulations
	---------------------------------------------------------------- */

	// Detect window width, height and scroll
	var _window_width = $window.width(),
		_window_height = $window.height(),
		_window_scroll_top = $window.scrollTop(),
		_window_scroll_left = $window.scrollLeft();
	$window.on( 'resize', function() {
		_window_width = $window.width();
		_window_height = $window.height();
		_window_scroll_top = $window.scrollTop();
		_window_scroll_left = $window.scrollLeft();	
	} );
	$window.on( 'scroll', function() {
		_window_scroll_top = $window.scrollTop();
		_window_scroll_left = $window.scrollLeft();	
	} );
	window.trx_addons_window_width = function( val ) {
		if ( val ) _window_width = val;
		return _window_width;
	};
	window.trx_addons_window_height = function( val ) {
		if ( val ) _window_height = val;
		return _window_height;
	};
	window.trx_addons_window_scroll_top = function() {
		return _window_scroll_top;
	};
	window.trx_addons_window_scroll_left = function() {
		return _window_scroll_left;
	};

	// Detect document height
	var	_document_height;
	var _document_height_first_run = true;
	var _update_document_height = function( e ) {
		if ( _document_height_first_run && e && e.namespace == 'init_hidden_elements' ) {
			_document_height_first_run = false;
			return; 
		}
		_document_height = $document.height();
	};
	$document.ready( _update_document_height );
	$document.on( 'action.init_hidden_elements action.got_ajax_response',    // Maybe need for ' action.sc_layouts_row_fixed_on action.sc_layouts_row_fixed_off'
				_update_document_height
				);
	$window.on( 'resize', _update_document_height );
	window.trx_addons_document_height = function() {
		return _document_height;
	};

	// Detect adminbar height (if present and fixed)
	var _adminbar_height = 0;
	var _update_adminbar_height = function() {
		_adminbar_height = trx_addons_adminbar_height_calc();
	};
	$document.ready( _update_adminbar_height );
	$window.on( 'resize', _update_adminbar_height );
	window.trx_addons_adminbar_height_calc = function() {
		return trx_addons_apply_filters( 'trx_addons_filter_adminbar_height',
					$adminbar.length === 0
						|| $adminbar.css( 'display' ) == 'none'
						|| $adminbar.css( 'position' ) == 'absolute'
							? 0
							: $adminbar.height()
				);
	};
	window.trx_addons_adminbar_height = function() {
		return _adminbar_height;
	};

	// Detect fixed rows height
	var $fixed_rows = false,
		_fixed_rows_height = 0;
	var _update_fixed_rows = function() {
		if ( $fixed_rows === false ) {
			$fixed_rows = jQuery( '.sc_layouts_row_fixed');
		}
		_fixed_rows_height = trx_addons_fixed_rows_height_calc();
		$html_dom.style.setProperty( '--fixed-rows-height', ( _fixed_rows_height + trx_addons_adminbar_height() ) + 'px' );
	};
	$document.ready( _update_fixed_rows );
	$document.on('action.ready_trx_addons action.sc_layouts_row_fixed_on action.sc_layouts_row_fixed_off', _update_fixed_rows );
	$window.on( 'resize', _update_fixed_rows );
	window.trx_addons_fixed_rows_height_calc = function() {
		var oft = 0;
		if ( $fixed_rows.length > 0 ) {
			var $fixed_on = $fixed_rows.filter( '.sc_layouts_row_fixed_on' );
			if ( $fixed_on.length > 0
				&& ! $body.hasClass( 'hide_fixed_rows' )
//				&& ! $body.hasClass( 'hide_fixed_rows_enabled' )
//				&& ! $body.hasClass( 'header_position_over' )
			) {
				$fixed_on.each( function() {
					var $row = jQuery( this );
					if ( $row.css( 'position' ) == 'fixed' ) {
//						if ( ! $body.hasClass( 'header_position_over' ) && ! $body.hasClass( 'header_position_under' ) || $row.parents( '.top_panel' ).length === 0 ) {
							oft += $row.outerHeight();
//						}
					}
				} );
			}
		}
		return trx_addons_apply_filters( 'trx_addons_filter_fixed_rows_height', oft );
	};
	window.trx_addons_fixed_rows_height = function() {
		var with_admin_bar  = arguments.length > 0 ? arguments[0] : true,
			with_fixed_rows = arguments.length > 1 ? arguments[1] : true;
		return ( with_admin_bar ? trx_addons_adminbar_height() : 0 )
				+ ( with_fixed_rows ? _fixed_rows_height : 0 );
	};

	// Animated scroll to selected id
	window.trx_addons_document_animate_to_busy = false;
	window.trx_addons_document_animate_to = function(id, callback) {
		var split_animation = true;
		var oft = !isNaN(id) ? Number(id) : 0,
		    oft2 = -1;
		var obj = null;
		if ( isNaN(id) ) {
			if ( typeof id == 'object' ) {
				obj = id;
			} else {
				if ( id.substring(0, 1) != '#' && id.substring(0, 1) != '.' ) {
					id = '#' + id;
				}
				obj = jQuery(id).eq(0);
				if ( obj.length === 0 ) {
					return;
				}
			}
			oft = split_animation ? obj.offset().top : Math.max( 0, obj.offset().top - trx_addons_fixed_rows_height() );
			if ( split_animation ) {
				oft2 = Math.max( 0, oft - trx_addons_fixed_rows_height() );
			}
		}
		var speed = Math.min(1000, Math.max(300, Math.round(Math.abs( (oft2 < 0 ? oft : oft2) - jQuery(window).scrollTop()) / jQuery(window).height() * 300)));
		// Recalc offset always (after the half animation time) to detect change size of the fullheight rows
		window.trx_addons_document_animate_to_busy = true;
		if ( oft2 >= 0 ) {
			setTimeout( function() {
				if (isNaN(id)) oft = obj.offset().top;
				oft2 = Math.max( 0, oft - trx_addons_fixed_rows_height() );
				jQuery('body,html').stop(true).animate( {scrollTop: oft2}, Math.floor(speed/2), 'linear', function() {
					_window_scroll_top = $window.scrollTop();
					window.trx_addons_document_animate_to_busy = false;
					if ( callback ) callback( id, speed );
				} );
			}, Math.floor(speed/2) );
		} else {
			oft2 = oft;
		}
		if ( speed > 0 ) {
			jQuery('body,html').stop(true).animate( {scrollTop: oft2}, speed, 'linear', function() {
				_window_scroll_top = $window.scrollTop();
				window.trx_addons_document_animate_to_busy = false;
				if ( callback ) callback( id, speed );
			} );
		} else {
			jQuery( 'body,html' ).stop( true ).scrollTop( oft2 );
			_window_scroll_top = $window.scrollTop();
			window.trx_addons_document_animate_to_busy = false;
			if ( callback ) callback( id, speed );
		}
	};

	// Return a requestAnimationFrame function
	window.trx_addons_request_animation_frame = function() {
		return window.requestAnimationFrame
			|| window.webkitRequestAnimationFrame
			|| window.mozRequestAnimationFrame
			|| window.oRequestAnimationFrame
			|| window.msRequestAnimationFrame
			|| null;
	};

	// Change browser address without reload page
	window.trx_addons_document_set_location = function( curLoc, state ) {
		if ( history.pushState === undefined || navigator.userAgent.match(/MSIE\s[6-9]/i) !== null ) {
			return;
		}
		try {
			history.pushState( state ? state : { url: curLoc }, null, curLoc );
			return;
		} catch( e ) {}
		location.href = curLoc;
	};

	// Add/Change arguments to the url address
	window.trx_addons_add_to_url = function(loc, prm) {
		var ignore_empty = arguments[2] !== undefined ? arguments[2] : true,
			q = loc.split('?'),
			attr = q.length > 1 ? trx_addons_parse_query_string(q[1]) : {},
			i = 0;
		for (var p in prm) {
			attr[p] = prm[p];
		}
		loc = q[0] + '?';
		i = 0;
		for (p in attr) {
			if (ignore_empty && attr[p] === '') continue;
			loc += (i++ > 0 ? '&' : '') + encodeURIComponent(p) + '=' + encodeURIComponent(attr[p]);
		}
		return loc;
	};

	// Add extra parameters to all links on page
	window.trx_addons_add_extra_args_to_links = function( args, cont ) {
		if ( ! cont ) cont = $body;
		cont.find( 'a' ).each( function() {
			var link = jQuery( this ),
				href = link.attr( 'href' );
			if ( href && href != '#' && ! trx_addons_is_local_link( href ) ) {
				var loc = window.location.href,
					page_valid = true;
				for ( var i = 0; i < args.length; i++ ) {
					page_valid = true;
					if ( args[i].page ) {
						page_valid = false;
						if ( typeof args[i].page == 'object' ) {
							for ( var pg in args[i].page ) {
								page_valid = loc.indexOf( args[i].page[pg] ) >= 0;
								if ( page_valid ) break;
							}
						} else {
							page_valid = loc.indexOf( args[i].page ) >= 0;
						}
					}
					if ( page_valid && ( ! args[i].mask || href.indexOf( args[i].mask ) >= 0 ) ) {
						href = typeof args[i].link != 'undefined'
									? args[i].link
									: trx_addons_add_to_url( href, args[i].args );
					}
				}
				link.attr( 'href', href );
			}
		} );
	};

	// Return value of URL parameter
	window.trx_addons_get_value_gp = function(prm) {
		var urlParams = new URLSearchParams(window.location.search),
			value = urlParams.get(prm);
		return decodeURIComponent( value ? value : '');
	};

	// Parse query string
	window.trx_addons_parse_query_string = function(qs) {
		var query = {},
			pairs = ( qs.indexOf('?') >= 0 ? qs.substring( qs.indexOf('?') + 1 ) : qs).split('&'),
			pair = [];
		for (var i = 0; i < pairs.length; i++) {
			pair = pairs[i].split('=');
			query[decodeURIComponent(pair[0])] = decodeURIComponent(pair[1] || '');
		}
		return query;
	};

	// Check if url is page-inner (local) link
	window.trx_addons_is_local_link = function(url) {
		var rez = url!==undefined;
		if (rez) {
			var url_pos = url.indexOf('#');
			if (url_pos === 0 && url.length == 1) {
				rez = false;
			} else {
				if (url_pos < 0) url_pos = url.length;
				var loc = window.location.href;
				var loc_pos = loc.indexOf('#');
				if (loc_pos > 0) loc = loc.substring(0, loc_pos);
				rez = url_pos === 0;
				if (!rez) rez = loc == url.substring(0, url_pos);
			}
		}
		return rez;
	};

	// Check if the specified path is url
	window.trx_addons_is_url = function( url ) {
		return url.indexOf( '//' ) === 0 || url.indexOf( '://' ) > 0;
	};
	
	// Get embed code from video URL
	window.trx_addons_get_embed_from_url = function(url, autoplay, mute, loop ) {
		if (autoplay === undefined) {
			autoplay = true;
		}
		if (mute === undefined) {
			mute = true;
		}
		if (loop === undefined) {
			loop = true;
		}
		var embed = '';
		if ( url.indexOf( '.mp4' ) > 0 || trx_addons_is_local_link( url ) ) {
			embed = '<video class="trx_addons_video_video" playsinline disablepictureinpicture'
						+ ( autoplay ? ' autoplay="autoplay"' : '' )
						+ ( loop ? ' loop="loop"' : '' )
						+ ( mute ? ' muted="muted"' : '' )
						+ '>'
							+ '<source src="' + url + '" type="video/mp4" />'
					+ '</video>';
		} else {
			url = url.replace('/watch?v=', '/embed/')
					 .replace('/youtu.be/', '/www.youtube.com/embed/')
					 .replace('/vimeo.com/', '/player.vimeo.com/video/')
					 .replace('/dai.ly/', '/dailymotion.com/embed/video/')
					 .replace('/dailymotion.com/video/', '/dailymotion.com/embed/video/');
			if (autoplay) {
				url += (url.indexOf('?') > 0 ? '&' : '?') + 'autoplay=1';
			}
			if (mute) {
				url += (url.indexOf('?') > 0 ? '&' : '?') + 'muted=1';
			}
			embed = '<iframe src="'+url+'" border="0" width="1280" height="720"' + (autoplay ? ' allow="autoplay"' : '') + '></iframe>';
		}
		return embed;
	};
			
	// Turn on autoplay for videos in the container
	window.trx_addons_set_autoplay = function(container, value) {
		if (value === undefined) {
			value = 1;
		}
		container.find('.video_frame > iframe, iframe').each(function () {
			if (value) {
				jQuery(this).attr('allow', 'autoplay');
			}
			var src = jQuery(this).data('src');
			if (src) {
				jQuery(this).attr('src', src);
			} else {
				src = jQuery(this).attr('src');
				if (src === undefined) {
					src = '';
				}
				if (src.indexOf('youtube')>=0 || src.indexOf('vimeo')>=0) {
					jQuery(this).attr('src', trx_addons_add_to_url(src, {'autoplay': value}));
				}
			}
		});
	};

	// Insert an iframe with a video player after click on the preview image
	// If a video with autoplay and it hosted on Youtube - for iOS a Youtube API script is used instead an iframe
	window.trx_addons_insert_video_iframe = function( $cont, iframe_html ) {
		if ( $cont.length === 0 ) {
			return;
		}
		// If we are in iOS and the video from Youtube - try to create player and start the video
		if ( trx_addons_browser_is_ios() && iframe_html.indexOf( 'youtu' ) > 0 && iframe_html.indexOf( 'autoplay=1' ) > 0 && typeof YT != 'undefined' ) {
			var id = 'trx_addons_yt_player_' + Math.floor( Math.random() * 100000 );
			$cont.html( iframe_html.replace( /<iframe[\s]+[\s\S]+<\/iframe>/, '<div class="trx_addons_yt_player" id="' + id + '"></div>' ) );
			var src = iframe_html.split('?');
			var video_id = src[0].substring( src[0].indexOf('/embed/') + 7 );
			var player = new YT.Player( id, {
				videoId: video_id,
				suggestedQuality: 'hd720',
				//height: '390',
				//width: '640',
				playerVars: {
					autoplay: 1,
					autohide: 0,
					modestbranding: 1,
					rel: 0,
					showinfo: 0,
					controls: 1,
					disablekb: 1,
					enablejsapi: 1,
					iv_load_policy: 3,
					playsinline: 1,
					loop: 0
				},
				events: {
					'onReady': function onReady(e) {
						player.playVideo();
					}
				}
			} );

		// Otherwise insert the iframe with a video to the block .video_embed
		} else {
			$cont.html( iframe_html );
		}
	};


	/* Browsers detection
	---------------------------------------------------------------- */
	window.trx_addons_browser_is_support = function( prop, value ) {
		var prefixes = ' -webkit- -moz- -o- -ms- '.split(' ');
		var el = document.createElement('a');
		var style = el.style;
		if ( prop.slice( -1 ) != ':' ) {
			prop += ':';
		}
		style.cssText = prop + prefixes.join( value + ';' + prop ).slice( 0, - prop.length );
  		return style.position.indexOf( value ) !== -1;
	};

	window.trx_addons_browser_is_support_css_sticky = function() {
		return trx_addons_browser_is_support( 'position', 'sticky' );
	};

	window.trx_addons_browser_is_touch = function() {
		return 'ontouchstart' in document.documentElement;
	};

	window.trx_addons_browser_is_pointer_events = function() {
		return !!window.PointerEvent && ('maxTouchPoints' in window.navigator) && window.navigator.maxTouchPoints >= 0;
	};

	window.trx_addons_browser_is_mobile = function() {
		var check = false;
		( function(a) {
			if ( /(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od|ad)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test( a.substr(0,4) ) ) {
				check = true;
			}
		} )( navigator.userAgent || navigator.vendor || window.opera );
		return check;
	};
	
	window.trx_addons_browser_is_ios = function() {
		return navigator.userAgent.match(/iPad|iPhone|iPod/i) !== null
					? true
					: false;
	};
	
	window.trx_addons_browser_is_iphone = function() {
		return navigator.userAgent.match(/iPhone/i) !== null
					? true
					: false;
	};

	window.trx_addons_browser_is_ipad = function() {
		return navigator.userAgent.match(/iPad/i) !== null
					? true
					: false;
	};

	window.trx_addons_browser_is_ipod = function() {
		return navigator.userAgent.match(/iPod/i) !== null
					? true
					: false;
	};

	window.trx_addons_is_retina = function() {
		var mediaQuery = '(-webkit-min-device-pixel-ratio: 1.5), (min--moz-device-pixel-ratio: 1.5), (-o-min-device-pixel-ratio: 3/2), (min-resolution: 1.5dppx)';
		return (window.devicePixelRatio > 1) || (window.matchMedia && window.matchMedia(mediaQuery).matches);
	};

	window.trx_addons_browser_classes = function() {
		var userAgent = navigator.userAgent,
			matchUserAgent = function( ua ) {
				return userAgent.indexOf( ua ) >= 0;
			},
			classes = {
				// Platform
				'mobile': trx_addons_browser_is_mobile(),
				'mac': matchUserAgent('Macintosh'),
				'ios': trx_addons_browser_is_ios(),
				'iphone': trx_addons_browser_is_iphone() || trx_addons_browser_is_ipad() || trx_addons_browser_is_ipod(),
				'touch': trx_addons_browser_is_touch(),
				'retina': trx_addons_is_retina(),
				// Browser
				'firefox': matchUserAgent( 'Firefox' ),												// Firefox 1.0+
				'opera': !!window.opr && !!opr.addons || !!window.opera || matchUserAgent(' OPR/'),	// Opera 8.0+
				'safari': /^((?!chrome|android).)*safari/i.test(userAgent)							// Safari 3.0+ "[object HTMLElementConstructor]"
							|| /constructor/i.test(window.HTMLElement)
							|| (p => { return '[object SafariRemoteNotification]' === p.toString(); })(!window.safari || typeof safari !== 'undefined' && safari.pushNotification),
				'ie': /Trident|MSIE/.test(userAgent) && ( false || !!document.documentMode ),		// Internet Explorer 6-11
				'blink': matchUserAgent('Chrome') && !!window.CSS,									// Blink engine
				'webkit': matchUserAgent('AppleWebKit')
			};
		classes['edge'] = ! classes['ie'] && !!window.StyleMedia || matchUserAgent('Edg');			// Edge 20+
		classes['chrome'] = !!window.chrome && matchUserAgent('Chrome') && ! classes['edge'] && ! classes['opera'];// Google Chrome (Not accurate)
		classes['applewebkit'] = matchUserAgent('AppleWebKit') && ! classes['blink'];				// Apple Webkit engine
		classes['gecko'] = matchUserAgent('Gecko') && classes['firefox'];							// Gecko
		return classes;
	};


	/* File functions
	---------------------------------------------------------------- */
	
	window.trx_addons_get_file_name = function(path) {
		path = path.replace(/\\/g, '/');
		if ( path.indexOf('?') > 0 ) {
			path = path.substr( 0, path.indexOf('?') );
		}
		var pos = path.lastIndexOf('/');
		if (pos >= 0)
			path = path.substr(pos+1);
		return path;
	};
	
	window.trx_addons_get_file_ext = function(path) {
		if ( path.indexOf('?') > 0 ) {
			path = path.substr( 0, path.indexOf('?') );
		}
		var pos = path.lastIndexOf('.');
		path = pos >= 0 ? path.substr(pos+1) : '';
		return path;
	};
	
	window.trx_addons_get_basename = function(path) {
		return trx_addons_get_file_name(path).replace('.'+trx_addons_get_file_ext(path), '');
	};
	
	
	
	/* Image functions
	---------------------------------------------------------------- */
	
	// Return true, if all images in the specified container are loaded
	window.trx_addons_is_images_loaded = function(cont) {
		var complete = true;
		cont.find('img').each(function() {
			// If any of previous images is not loaded - skip rest
			if ( ! complete ) {
				return;
			}
			var img = jQuery(this).get(0);
			if (typeof img.complete == 'boolean') {
				// See if "complete" property is available
				complete = img.complete;
			} else if (typeof img.naturalWidth == 'number' && typeof img.naturalHeight == 'number') {
				// See if "naturalWidth" and "naturalHeight" properties are available
				complete = !(img.naturalWidth == 0 && img.naturalHeight == 0);
			}
		});
		return complete;
	};
	
	// Call function when all images in the specified container are loaded
	window.trx_addons_when_images_loaded = function(cont, callback, max_delay) {
		if (max_delay === undefined) {
			max_delay = 3000;
		}
		if (max_delay <= 0 || trx_addons_is_images_loaded(cont)) {
			callback( cont );
		} else {
			setTimeout(function(){
				trx_addons_when_images_loaded(cont, callback, max_delay - 200);
			}, 200);
		}
	};

	// Fetch content from URL
	window.trx_addons_fetch_url = function( url, callback ) {
		if ( typeof window.fetch == 'function' ) {
			fetch( url )
				.then( function( response ) {
					return response.ok ? response.text() : '';
				} )
				.then( function( data ) {
					if ( callback ) {
						callback( data );
					}
				} );
		} else {
			jQuery.get( url )
				.done( function( response ) {
					if ( typeof response == 'object'
						&& typeof response.childElementCount != 'undefined' && response.childElementCount > 0
						&& typeof response.children != 'undefined' && typeof response.children[0] != 'undefined'
					) {
						response = response.children[0].outerHTML;
					}
					if ( callback ) {
						callback( response );
					}
				} )
				.fail( function() {
					if ( callback ) {
						callback( '' );
					}
				} );
		}
	};

	// Return cached SVG-file content or fetch a file if not in a cache
	var inline_svg = [];
	window.trx_addons_get_inline_svg = function( svg_url, view ) {
		var html = '';
		for( var i = 0; i < inline_svg.length; i++ ) {
			if ( inline_svg[i].url == svg_url ) {
				html = inline_svg[i].html;
				break;
			}
		}
		if ( html === '' ) {
			trx_addons_fetch_url( svg_url, function( html ) {
				inline_svg.push( {
					url: svg_url,
					html: html
				} );
				if ( view ) {
					view.render();
				}
			} );
		}
		return html;
	};

	// Legacy function to compatibility with the old themes
	if ( ! window.get_inline_svg ) {
		window.get_inline_svg = window.trx_addons_get_inline_svg;
	}

	
	/* Numbers functions
	---------------------------------------------------------------- */
	
	// Round number to specified precision. 
	// For example: num=1.12345, prec=2,  rounded=1.12
	//              num=12345,   prec=-2, rounded=12300
	window.trx_addons_round_number = function(num) {
		var precision = arguments[1]!==undefined ? arguments[1] : 0;
		var p = Math.pow(10, precision);
		return Math.round(num*p)/p;
	};
	
	// Format money:
	// For example: (123456789.12345).formatMoney(2, '.', ',');
	Number.prototype.formatMoney = function(c, d, t) {
		var n = this, 
			c = c == undefined ? 2 : (isNaN(c = Math.abs(c)) ? 2 : c),
			d = d == undefined ? "." : d, 
			t = t == undefined ? "," : t, 
			s = n < 0 ? "-" : "", 
			i = String(parseInt(n = Math.abs(Number(n) || 0).toFixed(c))), 
			j = (j = i.length) > 3 ? j % 3 : 0;
		return s
				+ (j ? i.substring(0, j) + t : "") 
				+ i.substring(j).replace(/(\d{3})(?=\d)/g, "$1" + t) 
				+ (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
	};

	// Return random integer from a to b
	window.trx_addons_random = function(a, b) {
		return Math.floor( Math.random() * ( b - a ) ) + a;
	};

	
	/* Strings
	---------------------------------------------------------------- */

	// Make first letter of every word
	window.trx_addons_proper = function(str) {
		return str.replace(/(\b\w)/gi, function(m) { return m.toUpperCase(); });
	};

	// Replicate string several times
	window.trx_addons_replicate = function(str, num) {
		var rez = '';
		for (var i=0; i < num; i++) {
			rez += str;
		}
		return rez;
	};

	// Split a string by delimiter, but not split if delimiter inside quotes
	window.trx_addons_split = function(str, delimiter) {
		var rez = [],
			quotes = false,
			pos = 0;
		for ( var i = 0; i < str.length; i++ ) {
			if ( str[i] == delimiter && ! quotes ) {
				rez.push( str.substring( pos, i ) );
				pos = i + 1;
			} else if ( str[ i ] == '"' ) {
				quotes = ! quotes;
			}
		}
		rez.push( str.substring( pos, i ) );
		return rez;
	};
	
	// Parse a string with arguments to the object
	window.trx_addons_parse_atts = function(str, delimiter) {
		var obj = {};
		if ( ! delimiter ) {
			delimiter = ' ';
		}
		if ( str !== undefined ) {
			if ( delimiter == '&' ) {
				str = str.replace(/&amp;/g, '&');
			}
			var pairs = trx_addons_split( str, delimiter );
			for ( var i in pairs ) {
				if ( pairs[i].indexOf('=') != -1 ) {
					var pair = pairs[i].split('=');
					obj[ pair[0] ] = ('' + pair[1]).slice(0, 1) == '"' && ('' + pair[1]).slice(-1) == '"'
									|| ( '' + pair[1]).slice(0, 1) == "'" && ('' + pair[1]).slice(-1) == "'"
										? pair[1].slice( 1, -1 )
										: pair[1];
				}
			}
		}
		return obj;
	};

	// Replace:
	// {{..}} to the <i>..</i>
	// ((..)) to the <b>..</b>
	// ||     to the <br>
	// ^N     to the <sup>N</sup>
	window.trx_addons_prepare_macros = function(str) {
		if ( ! str || typeof str != 'string' ) {
			return str;
		}
		// Replace shortcodes
		if ( str.indexOf('[') >= 0 && str.indexOf(']') >= 0 ) {
			str = str.replace(
				/([\[])([\[\]\S]+?)[\s]+([^\[\]]+)?([\]])/g,
				function( match, p1, p2, p3, p4 ) {
					// Allowed CSS properties as attributes in format:
					// 'attribute': 'css_property' or 'attribute': { 'rule': 'css_property', 'default': 'default_value' }
					var allowed_css = trx_addons_apply_filters( 'trx_addons_filter_prepare_macros_allowed_css', {
								'valign': 'vertical-align',
								'color': 'color',
								'bgcolor': 'background-color',
								'bdcolor': 'border-color',
								'border': 'border-width',
								'radius': 'border-radius',
								'padding': 'padding',
								'margin': 'margin'
							},
							'common'
						),
						image_css = trx_addons_apply_filters( 'trx_addons_filter_prepare_macros_allowed_css', {
								'size': { 'rule': 'max-height', 'default': '1em' },
							},
							'image'
						),
						icon_css = trx_addons_apply_filters( 'trx_addons_filter_prepare_macros_allowed_css', {
								'size': { 'rule': 'font-size', 'default': '1em' },
							},
							'icon'
						),
						atts = false;
					if ( p2 == 'image' ) {
						atts = p3 ? trx_addons_parse_atts( p3 ) : {};
						// Fetch an image url by id or replace it by default image
						if ( ! atts['url'] ) {
							// Default image
							atts['url'] = TRX_ADDONS_STORAGE['no_image'];
							// Get url with specified size
							var get_url_by_size = function( data, thumb_size ) {
								var url = '';
								for ( var i in data['media_details']['sizes'] ) {
									if ( ('' + i).indexOf( thumb_size ) >= 0 ) {
										url = data['media_details']['sizes'][i]['source_url'];
										break;
									}
								}
								return url;
							};
							// Fetch image by id
							if ( atts['id'] ) {
								if ( TRX_ADDONS_STORAGE['fetch_images' ] === undefined ) {
									TRX_ADDONS_STORAGE['fetch_images'] = {};
								}
								if ( ! TRX_ADDONS_STORAGE['fetch_images'][ atts['id'] ] ) {
									jQuery.get( TRX_ADDONS_STORAGE['rest_url'] + 'wp/v2/media/' + atts['id'] + '/', function(response) {
										if ( response && response['media_details'] && response['media_details']['sizes'] ) {
											TRX_ADDONS_STORAGE['fetch_images'][ atts['id'] ] = response;
										}
									} );
								} else {
									atts['url'] = get_url_by_size( TRX_ADDONS_STORAGE['fetch_images'][ atts['id'] ], atts['thumb'] ? '-' + atts['thumb'] : 'full' );
								}
							}
						}
						return atts['url']
								? '<img src="' + atts['url'] + '"'
									+ ( atts['id'] ? ' id="trx_addons_image_' + atts['id'] + '"' : '' )
									+ ( atts['alt'] ? ' alt="' + atts['alt'] + '"' : '' )
									+ ' style="'
										+ trx_addons_get_css_from_atts( atts, trx_addons_object_merge( allowed_css, image_css ) )
										+ ( atts['css'] ? atts['css'] : '' )
									+ '"'
									+ '>'
								: '';
					} else if ( p2 == 'icon' ) {
						atts = p3 ? trx_addons_parse_atts( p3 ) : {};
						if ( atts['name'] && atts['name'].slice( 0, 5 ) != 'icon-' ) {
							atts['name'] = 'icon-' + atts['name'];
						}
						return atts['name']
								? '<span class="' + atts['name'] + '"'
									+ ' style="'
										+ trx_addons_get_css_from_atts( atts, trx_addons_object_merge( allowed_css, icon_css ) )
										+ ( atts['css'] ? atts['css'] : '' )
									+ '"'
									+ '></span>'
								: '';
					} else if ( p2.slice( 0, 1 ) != '&' ) {
						return p1 + p2 + p4;
					} else {
						return match;
					}
				}
			);
		}
		// Replace simple macros
		return str
			// Replace all shortcodes with its short variant - names only, without attributes
			// Commented because it's already done in the callback above
			//.replace(/([\[])([\[\]\S]+?)[\s]+([^\[\]]+)?([\]])/g, '$1$2$4')
			.replace(/\{\{/g, "<i>")
			.replace(/\}\}/g, "</i>")
			.replace(/\(\(/g, "<b>")
			.replace(/\)\)/g, "</b>")
			.replace(/\|\|/g, "<br>")
			.replace(/(\^(\d+))/g, "<sup>$2</sup>");
	};
	window.trx_addons_remove_macros = function(str) {
		return str
			.replace(/[^\[]([\[][^\[\]]+[\]])[^\]]/g, "")
			.replace(/\{\{/g, "")
			.replace(/\}\}/g, "")
			.replace(/\(\(/g, "")
			.replace(/\)\)/g, "")
			.replace(/\|\|/g, "");
	};

	// Replace {{ and }} with < and >
	window.trx_addons_parse_codes = function(text, tag_start, tag_end) {
		if (tag_start === undefined) tag_start = '{{';
		if (tag_end === undefined) tag_end = '}}';
		var r1 = new RegExp(tag_start, 'g');
		var r2 = new RegExp(tag_end, 'g');
		return text.replace(r1, '<').replace(r2, '>');
	};

	// Check value for "on" | "off" | "inherit" values
	window.trx_addons_is_on = function(prm) {
		return prm>0 || ['true', 'on', 'yes', 'show'].indexOf((''+prm).toLowerCase()) >= 0;
	};
	window.trx_addons_is_off = function(prm) {
		return prm === undefined || prm === '' || prm === 0 || ['false', 'off', 'no', 'none', 'hide'].indexOf((''+prm).toLowerCase()) >= 0;
	};
	window.trx_addons_is_inherit = function(prm) {
		return ['inherit'].indexOf((''+prm).toLowerCase()) >= 0;
	};

	// Check for an object is empty
	window.trx_addons_is_empty = function(prm) {
		return prm === undefined || prm === '' || prm === 0 || prm === false || ( typeof prm == 'object' && Object.keys( prm ).length === 0 );
	};

	// Return class by prefix from classes string
	window.trx_addons_get_class_by_prefix = function(classes, prefix) {
		var rez = '';
		if ( classes ) {
			classes = classes.split(' ');
			for (var i=0; i < classes.length; i++) {
				if (classes[i].indexOf(prefix) >= 0) {
					rez = classes[i].replace(/[\s]+/g, '');			// Remove \t\r\n and spaces from the new class
					break;
				}
			}
		}
		return rez;
	};

	// Replace class by prefix with new value
	window.trx_addons_chg_class_by_prefix = function(classes, prefix, new_value) {
		var chg = false;
		if ( ! classes ) classes = '';
		classes = classes.replace(/[\s]+/g, ' ').split(' ');	// Replace groups \t\r\n and spaces with the single space
		new_value = new_value.replace(/[\s]+/g, '');			// Remove \t\r\n and spaces from the new class
		if ( typeof prefix == 'string' ) {
			prefix = [prefix];
		}
		for (var i=0; i < classes.length; i++) {
			for (var j = 0; j < prefix.length; j++ ) {
				if (classes[i].indexOf( prefix[j] ) >= 0) {
					classes[i] = new_value;
					chg = true;
					break;
				}
			}
			if ( chg ) break;
		}
		if ( ! chg && new_value ) {
			if (classes.length == 1 && classes[0] === '')
				classes[0] = new_value;
			else
				classes.push( new_value );
		}
		return classes.join(' ').replace(/[\s]{2,}/g, ' ');
	};

	// Return icon class from classes string
	window.trx_addons_get_icon_class = function(classes) {
		if ( ! classes ) classes = '';
		return trx_addons_get_class_by_prefix(classes, 'icon-');
	};

	// Replace icon's class with new value
	window.trx_addons_chg_icon_class = function(classes, icon, prefix) {
		var chg        = false,
			icon_parts = icon.split( '-' );
		if ( prefix === undefined ) {
			prefix = ['none', 'icon-', 'image-'];
		}
		prefix.push( icon_parts[0] + '-' );
		if ( ! classes ) classes = '';
		classes = classes.split(' ');
		for (var i=0; i < classes.length; i++) {
			for (var j = 0; j < prefix.length; j++ ) {
				if (classes[i].indexOf( prefix[j] ) >= 0) {
					classes[i] = [ 'none', 'image-none' ].indexOf( icon ) != -1 ? '' : icon;
					chg = true;
					break;
				}
			}
			if ( chg ) break;
		}
		if ( ! chg && [ 'none', 'image-none' ].indexOf( icon ) == -1 ) {
			if ( classes.length == 1 && classes[0] === '' ) {
				classes[0] = icon;
			} else {
				classes.push( icon );
			}
		}
		return classes.join(' ');
	};

	// Return column class by number of columns
	window.trx_addons_get_column_class = function( num, all, all_tablet, all_mobile ) {
		var column_class_tpl = TRX_ADDONS_STORAGE['column_class_template'];
		var column_class = column_class_tpl.replace( '$1', num ).replace( '$2', all );
		if ( all_tablet ) {
			column_class += ' ' + column_class_tpl.replace( '$1', num ).replace( '$2', all_tablet ) + '-tablet';
		}
		if ( all_mobile ) {
			column_class += ' ' + column_class_tpl.replace( '$1', num ).replace( '$2', all_mobile ) + '-mobile';
		}
		return column_class;
	};

	// Return a list of the responsive classes for the shortcode's attribute
	window.trx_addons_get_responsive_classes = function( prefix, atts, param, default_value ) {
		var list = [];
		// Push a general or default value
		if ( atts[ param ] ) {
			list.push( prefix + atts[ param ] );
		} else if ( default_value ) {
			list.push( prefix + default_value );
		}
		// Add responsive values (if present)
		if ( TRX_ADDONS_STORAGE['elementor_breakpoints'] ) {
			for ( var bp_name in TRX_ADDONS_STORAGE['elementor_breakpoints'] ) {
				if ( atts[ param + '_' + bp_name ] ) {
					list.push( prefix + atts[ param + '_' + bp_name ]  +  '_' + bp_name );
				}
			}
		}
		// Apply filters to allow 3rd-party plugins modify the list of the responsive classes
		list = trx_addons_apply_filters( 'trx_addons_filter_responsive_classes', list, prefix, atts, param );
		return list.length ? list.join( ' ' ) : '';
	};

	// Wrap each word to the specified tag
	window.trx_addons_wrap_words = function( txt, before, after ) {
		var rez = '', ch = '', in_tag = false, in_word = false;
		for ( var i = 0; i < txt.length; i++ ) {
			ch = txt.substring( i, i + 1 );
			if ( ch == '<' ) {
				in_tag = true;
				if ( in_word ) {
					rez += after;
					in_word = false;
				}
			}
			if ( ! in_tag ) {
				if ( ch == ' ' ) {
					if ( in_word ) {
						rez += after;
						in_word = false;
					}
				} else {
					if ( ! in_word ) {
						rez += before;
						in_word = true;
					}
				}
			}
			rez += ch;
			if ( ! in_tag && in_word && i == txt.length - 1 ) {
				rez += after;
			}
			if ( in_tag && ch == '>' ) {
				in_tag = false;
			}
		}
		return rez;
	};

	// Wrap each character to the specified tag
	window.trx_addons_wrap_chars = function( txt, before, after, before_word, after_word ) {
		var rez = '', ch = '', in_tag = false, in_word = false;
		if ( before_word === undefined ) before_word = '';
		if ( after_word === undefined ) after_word = '';
		for ( var i = 0; i < txt.length; i++ ) {
			ch = txt.substring( i, i + 1 );
			if ( ch == '<' ) {
				in_tag = true;
				if ( in_word ) {
					rez += after_word;
					in_word = false;
				}
			}
			if ( before_word && after_word && ! in_tag ) {
				if ( ch == ' ' ) {
					if ( in_word ) {
						rez += after_word;
						in_word = false;
					}
				} else {
					if ( ! in_word ) {
						rez += before_word;
						in_word = true;
					}
				}
			}
			rez += in_tag 
					? ch
					: before + ( ch == ' ' ? '&nbsp;' : ch ) + after;
			if ( ! in_tag && in_word && i == txt.length - 1 ) {
				rez += after_word;
			}
			if ( in_tag && ch == '>' ) {
				in_tag = false;
			}
		}
		return rez;
	};

	// Function to clear string from tags
	window.trx_addons_clear_tags = function( str ) {
		return str.replace( /<\/?[^>]+>/g, '' );
	};

	// Function to escape string for use it in HTML
	window.trx_addons_esc_html = function( str ) {
		// 1st case: use browser's built-in functionality to quickly and safely escape strings
		// var textarea = document.createElement( 'textarea' );
		// textarea.textContent = str;
		// return textarea.innerHTML;

		// 2nd case: use jQuery to escape strings
		// return jQuery('<div>').text(str).html();

		// 3rd case: use our own function to escape strings
		return str
				.replace( /&/g, '&amp;' )
				.replace( /</g, '&lt;' )
				.replace( />/g, '&gt;' )
				.replace( /"/g, '&quot;' )
				.replace( /'/g, '&#039;' );
	};
	

	/* Colors functions
	---------------------------------------------------------------- */
	
	window.trx_addons_hex2rgb = function(hex) {
		hex = hex.indexOf('#') > -1 ? hex.substring(1) : hex;
		if ( hex.length == 3 ) {
			hex = hex.slice(0,1).repeat(2) + hex.slice(1,2).repeat(2) + hex.slice(2,3).repeat(2);
		}
		var num = parseInt( hex, 16 );
		if ( hex.length > 6 ) {
			return {r: ( num >> 24 ) + 256, g: (num & 0x00FF0000) >> 16, b: (num & 0x0000FF00) >> 8, a: (num & 0x000000FF)};
		} else {
			return {r: num >> 16, g: (num & 0x00FF00) >> 8, b: (num & 0x0000FF)};
		}
	};

	window.trx_addons_hex2rgba = function(hex, alpha) {
		var rgb = trx_addons_hex2rgb(hex);
		return 'rgba('+rgb.r+','+rgb.g+','+rgb.b+','+alpha+')';
	};

	window.trx_addons_rgb2hex = function(color) {
		var aRGB;
		color = color.replace(/\s/g,"").toLowerCase();
		if (color=='rgba(0,0,0,0)' || color=='rgba(0%,0%,0%,0%)') {
			color = 'transparent';
		}
		if (color.indexOf('rgba(')==0)
			aRGB = color.match(/^rgba\((\d{1,3}[%]?),(\d{1,3}[%]?),(\d{1,3}[%]?),(\d{1,3}[%]?)\)$/i);
		else	
			aRGB = color.match(/^rgb\((\d{1,3}[%]?),(\d{1,3}[%]?),(\d{1,3}[%]?)\)$/i);
		
		if(aRGB) {
			color = '';
			for (var i=1; i <= 3; i++) 
				color += Math.round((aRGB[i][aRGB[i].length-1]=="%"?2.55:1)*parseInt(aRGB[i])).toString(16).replace(/^(.)$/,'0$1');
		} else {
			color = color.replace(/^#?([\da-f])([\da-f])([\da-f])$/i, '$1$1$2$2$3$3');
		}
		return (color.substr(0,1)!='#' ? '#' : '') + color;
	};
	
	window.trx_addons_components2hex = function(r,g,b,a) {
		return '#'+
			Number(r).toString(16).toUpperCase().replace(/^(.)$/,'0$1') +
			Number(g).toString(16).toUpperCase().replace(/^(.)$/,'0$1') +
			Number(b).toString(16).toUpperCase().replace(/^(.)$/,'0$1') +
			( a || a === 0
				? Number(a).toString(16).toUpperCase().replace(/^(.)$/,'0$1')
				: ''
				);
	};
	
	window.trx_addons_rgb2components = function(color) {
		color = trx_addons_rgb2hex(color);
		var matches = color.match(/^#?([\dabcdef]{2})([\dabcdef]{2})([\dabcdef]{2})$/i);
		if (!matches) return false;
		for (var i=1, rgb = new Array(3); i <= 3; i++) {
			rgb[i-1] = parseInt(matches[i],16);
		}
		return rgb;
	};
	
	window.trx_addons_hex2hsb = function(hex) {
		var h = arguments[1]!==undefined ? arguments[1] : 0;
		var s = arguments[2]!==undefined ? arguments[2] : 0;
		var b = arguments[3]!==undefined ? arguments[3] : 0;
		var hsb = trx_addons_rgb2hsb(trx_addons_hex2rgb(hex));
		hsb.h = Math.min(359, Math.max( 0, hsb.h + h));
		hsb.s = Math.min(100, Math.max( 0, hsb.s + s));
		hsb.b = Math.min(100, Math.max( 0, hsb.b + b));
		return hsb;
	};
	
	window.trx_addons_hsb2hex = function(hsb) {
		var rgb = trx_addons_hsb2rgb(hsb);
		return trx_addons_components2hex(rgb.r, rgb.g, rgb.b);
	};
	
	window.trx_addons_rgb2hsb = function(rgb) {
		var hsb = {};
		hsb.b = Math.max(Math.max(rgb.r,rgb.g),rgb.b);
		hsb.s = (hsb.b <= 0) ? 0 : Math.round(100*(hsb.b - Math.min(Math.min(rgb.r,rgb.g),rgb.b))/hsb.b);
		hsb.b = Math.round((hsb.b /255)*100);
		if ((rgb.r==rgb.g) && (rgb.g==rgb.b))  hsb.h = 0;
		else if (rgb.r>=rgb.g && rgb.g>=rgb.b) hsb.h = 60*(rgb.g-rgb.b)/(rgb.r-rgb.b);
		else if (rgb.g>=rgb.r && rgb.r>=rgb.b) hsb.h = 60  + 60*(rgb.g-rgb.r)/(rgb.g-rgb.b);
		else if (rgb.g>=rgb.b && rgb.b>=rgb.r) hsb.h = 120 + 60*(rgb.b-rgb.r)/(rgb.g-rgb.r);
		else if (rgb.b>=rgb.g && rgb.g>=rgb.r) hsb.h = 180 + 60*(rgb.b-rgb.g)/(rgb.b-rgb.r);
		else if (rgb.b>=rgb.r && rgb.r>=rgb.g) hsb.h = 240 + 60*(rgb.r-rgb.g)/(rgb.b-rgb.g);
		else if (rgb.r>=rgb.b && rgb.b>=rgb.g) hsb.h = 300 + 60*(rgb.r-rgb.b)/(rgb.r-rgb.g);
		else 								   hsb.h = 0;
		hsb.h = Math.round(hsb.h);
		return hsb;
	};
	
	window.trx_addons_hsb2rgb = function(hsb) {
		var rgb = {};
		var h = Math.round(hsb.h);
		var s = Math.round(hsb.s*255/100);
		var v = Math.round(hsb.b*255/100);
		if (s == 0) {
			rgb.r = rgb.g = rgb.b = v;
		} else {
			var t1 = v;
			var t2 = (255-s)*v/255;
			var t3 = (t1-t2)*(h%60)/60;
			if (h==360) h = 0;
			if (h<60) 		{ rgb.r=t1;	rgb.b=t2;   rgb.g=t2+t3; }
			else if (h<120) { rgb.g=t1; rgb.b=t2;	rgb.r=t1-t3; }
			else if (h<180) { rgb.g=t1; rgb.r=t2;	rgb.b=t2+t3; }
			else if (h<240) { rgb.b=t1; rgb.r=t2;	rgb.g=t1-t3; }
			else if (h<300) { rgb.b=t1; rgb.g=t2;	rgb.r=t2+t3; }
			else if (h<360) { rgb.r=t1; rgb.g=t2;	rgb.b=t1-t3; }
			else 			{ rgb.r=0;  rgb.g=0;	rgb.b=0;	 }
		}
		return { r:Math.round(rgb.r), g:Math.round(rgb.g), b:Math.round(rgb.b) };
	};
	
	window.trx_addons_color_picker = function(){
		var id = arguments[0]!==undefined ? arguments[0] : "iColorPicker"+Math.round(Math.random()*1000);
		var colors = arguments[1]!==undefined ? arguments[1] : 
		'#f00,#ff0,#0f0,#0ff,#00f,#f0f,#fff,#ebebeb,#e1e1e1,#d7d7d7,#cccccc,#c2c2c2,#b7b7b7,#acacac,#a0a0a0,#959595,'
		+'#ee1d24,#fff100,#00a650,#00aeef,#2f3192,#ed008c,#898989,#7d7d7d,#707070,#626262,#555,#464646,#363636,#262626,#111,#000,'
		+'#f7977a,#fbad82,#fdc68c,#fff799,#c6df9c,#a4d49d,#81ca9d,#7bcdc9,#6ccff7,#7ca6d8,#8293ca,#8881be,#a286bd,#bc8cbf,#f49bc1,#f5999d,'
		+'#f16c4d,#f68e54,#fbaf5a,#fff467,#acd372,#7dc473,#39b778,#16bcb4,#00bff3,#438ccb,#5573b7,#5e5ca7,#855fa8,#a763a9,#ef6ea8,#f16d7e,'
		+'#ee1d24,#f16522,#f7941d,#fff100,#8fc63d,#37b44a,#00a650,#00a99e,#00aeef,#0072bc,#0054a5,#2f3192,#652c91,#91278f,#ed008c,#ee105a,'
		+'#9d0a0f,#a1410d,#a36209,#aba000,#588528,#197b30,#007236,#00736a,#0076a4,#004a80,#003370,#1d1363,#450e61,#62055f,#9e005c,#9d0039,'
		+'#790000,#7b3000,#7c4900,#827a00,#3e6617,#045f20,#005824,#005951,#005b7e,#003562,#002056,#0c004b,#30004a,#4b0048,#7a0045,#7a0026';
		var colorsList = colors.split(',');
		var tbl = '<table class="colorPickerTable"><thead>';
		for (var i=0; i < colorsList.length; i++) {
			if (i%16==0) tbl += (i>0 ? '</tr>' : '') + '<tr>';
			tbl += '<td style="background-color:'+colorsList[i]+'">&nbsp;</td>';
		}
		tbl += '</tr></thead><tbody>'
			+ '<tr style="height:60px;">'
			+ '<td colspan="8" id="'+id+'_colorPreview" style="vertical-align:middle;text-align:center;border:1px solid #000;background:#fff;">'
			+ '<input style="width:55px;color:#000;border:1px solid rgb(0, 0, 0);padding:5px;background-color:#fff;font:11px Arial, Helvetica, sans-serif;" maxlength="7" />'
			+ '<a href="#" id="'+id+'_moreColors" class="iColorPicker_moreColors"></a>'
			+ '</td>'
			+ '<td colspan="8" id="'+id+'_colorOriginal" style="vertical-align:middle;text-align:center;border:1px solid #000;background:#fff;">'
			+ '<input style="width:55px;color:#000;border:1px solid rgb(0, 0, 0);padding:5px;background-color:#fff;font:11px Arial, Helvetica, sans-serif;" readonly="readonly" />'
			+ '</td>'
			+ '</tr></tbody></table>';
	
		jQuery(document.createElement("div"))
			.attr("id", id)
			.css('display','none')
			.html(tbl)
			.appendTo("body")
			.addClass("iColorPickerTable")
			.on('mouseover', 'thead td', function(){
				var aaa = trx_addons_rgb2hex(jQuery(this).css('background-color'));
				jQuery('#'+id+'_colorPreview').css('background',aaa);
				jQuery('#'+id+'_colorPreview input').val(aaa);
			})
			.on('keypress', '#'+id+'_colorPreview input', function(key){
				var aaa = jQuery(this).val();
				if (key.which===13 && (aaa.length===4 || aaa.length===7)) {
					var fld  = jQuery('#'+id).data('field');
					var func = jQuery('#'+id).data('func');
					if (func!=null && func!='undefined') {
						func(fld, aaa);
					} else {
						fld.val(aaa).css('backgroundColor', aaa).trigger('change');
					}
					jQuery('#'+id+'_Bg').fadeOut(500);
					jQuery('#'+id).fadeOut(500);
					key.preventDefault();
					return false;
				}
			})
			.on('change', '#'+id+'_colorPreview input', function(key){
				var aaa = jQuery(this).val();
				if (aaa.substr(0,1)==='#' && (aaa.length===4 || aaa.length===7)) {
					jQuery('#'+id+'_colorPreview').css('background',aaa);
				}
			})
			.on('click', 'thead td', function(e){
				var fld  = jQuery('#'+id).data('field');
				var func = jQuery('#'+id).data('func');
				var aaa  = trx_addons_rgb2hex(jQuery(this).css('background-color'));
				if (func!=null && func!='undefined') {
					func(fld, aaa);
				} else {
					fld.val(aaa).css('backgroundColor', aaa).trigger('change');
				}
				jQuery('#'+id+'_Bg').fadeOut(500);
				jQuery('#'+id).fadeOut(500);
				e.preventDefault();
				return false;
			})
			.on('click', 'tbody .iColorPicker_moreColors', function(e){
				var thead  = jQuery(this).parents('table').find('thead');
				var out = '';
				if (thead.hasClass('more_colors')) {
					for (var i=0; i < colorsList.length; i++) {
						if (i%16==0) out += (i>0 ? '</tr>' : '') + '<tr>';
						out += '<td style="background-color:'+colorsList[i]+'">&nbsp;</td>';
					}
					thead.removeClass('more_colors').empty().html(out+'</tr>');
					jQuery('#'+id+'_colorPreview').attr('colspan', 8);
					jQuery('#'+id+'_colorOriginal').attr('colspan', 8);
				} else {
					var rgb=[0,0,0], i=0, j=-1;	// Set j=-1 or j=0 - show 2 different colors layouts
					while (rgb[0]<0xF || rgb[1]<0xF || rgb[2]<0xF) {
						if (i%18==0) out += (i>0 ? '</tr>' : '') + '<tr>';
						i++;
						out += '<td style="background-color:'+trx_addons_components2hex(rgb[0]*16+rgb[0],rgb[1]*16+rgb[1],rgb[2]*16+rgb[2])+'">&nbsp;</td>';
						rgb[2]+=3;
						if (rgb[2]>0xF) {
							rgb[1]+=3;
							if (rgb[1]>(j===0 ? 6 : 0xF)) {
								rgb[0]+=3;
								if (rgb[0]>0xF) {
									if (j===0) {
										j=1;
										rgb[0]=0;
										rgb[1]=9;
										rgb[2]=0;
									} else {
										break;
									}
								} else {
									rgb[1]=(j < 1 ? 0 : 9);
									rgb[2]=0;
								}
							} else {
								rgb[2]=0;
							}
						}
					}
					thead.addClass('more_colors').empty().html(out+'<td  style="background-color:#ffffff" colspan="8">&nbsp;</td></tr>');
					jQuery('#'+id+'_colorPreview').attr('colspan', 9);
					jQuery('#'+id+'_colorOriginal').attr('colspan', 9);
				}
				jQuery('#'+id+' table.colorPickerTable thead td')
					.css({
						'width':'12px',
						'height':'14px',
						'border':'1px solid #000',
						'cursor':'pointer'
					});
				e.preventDefault();
				return false;
			});
		jQuery(document.createElement("div"))
			.attr("id", id+"_Bg")
			.on('click', function(e) {
				jQuery("#"+id+"_Bg").fadeOut(500);
				jQuery("#"+id).fadeOut(500);
				e.preventDefault();
				return false;
			})
			.appendTo("body");
		jQuery('#'+id+' table.colorPickerTable thead td')
			.css({
				'width':'12px',
				'height':'14px',
				'border':'1px solid #000',
				'cursor':'pointer'
			});
		jQuery('#'+id+' table.colorPickerTable')
			.css({'border-collapse':'collapse'});
		jQuery('#'+id)
			.css({
				'border':'1px solid #ccc',
				'background':'#333',
				'padding':'5px',
				'color':'#fff'
			});
		jQuery('#'+id+'_colorPreview')
			.css({'height':'50px'});
		return id;
	};
	
	window.trx_addons_color_picker_show = function(id, fld, func) { 
		if (id === null || id === '') {
			id = jQuery('.iColorPickerTable').attr('id');
		}
		var eICP = fld.offset();
		var w = jQuery('#'+id).width();
		var h = jQuery('#'+id).height();
		var l = eICP.left + w < jQuery(window).width()-10 ? eICP.left : jQuery(window).width()-10 - w;
		var t = eICP.top + fld.outerHeight() + h < jQuery(document).scrollTop() + jQuery(window).height()-10 ? eICP.top + fld.outerHeight() : eICP.top - h - 13;
		jQuery("#"+id)
			.data({field: fld, func: func})
			.css({
				'top':t+"px",
				'left':l+"px",
				'position':'absolute',
				'z-index':999999
			})
			.fadeIn(500);
		jQuery("#"+id+"_Bg")
			.css({
				'position':'fixed',
				'z-index':999998,
				'top':0,
				'left':0,
				'width':'100%',
				'height':'100%'
			})
			.fadeIn(500);
		var def = fld.val().substr(0, 1)=='#' ? fld.val() : trx_addons_rgb2hex(fld.css('backgroundColor'));
		jQuery('#'+id+'_colorPreview input,#'+id+'_colorOriginal input').val(def);
		jQuery('#'+id+'_colorPreview,#'+id+'_colorOriginal').css('background',def);
	};
	
	
	/* Utils
	---------------------------------------------------------------- */

	// Global callback to catch Googlemap loaded to prevent a warning from googlemap api
	window.trx_addons_googlemap_loaded = function() {};

	// Add unit to css value
	window.trx_addons_prepare_css_value = function(val) {
		if (val !== '' && val != 'inherit') {
			var parts = ('' + val).split( ' ' );
			for ( var i = 0; i < parts.length; i++ ) {
				if ( parts[i] === '' ) {
					continue;
				}
				var ed = ('' + parts[i]).slice( -1 );
				if ('0' <= ed && ed <= '9') {
					parts[i] += 'px';
				}
			}
			val = parts.join( ' ' );
		}
		return val;
	};

	// Convert CSS units to px
	window.trx_addons_units2px = function(val, block, dir) {
		var value = parseFloat( val );
		var unit = ('' + val).replace( ('' + value), '' ).toLowerCase();
		if ( unit ) {
			if ( unit == 'vw' ) {
				value = Math.round( value * trx_addons_window_width() / 100 );
			} else if ( unit == 'vh' ) {
				value = Math.round( value * trx_addons_window_height() / 100 );
			} else if ( unit == '%' && block && block.length ) {
				value = Math.round( value * ( dir == 'x' ? block.eq(0).outerWidth() : block.eq(0).outerHeight() ) / 100 );
			} else if ( unit == 'em' && block && block.length ) {
				value = parseFloat( getComputedStyle( block.get(0) )['fontSize'] ) * value;
			} else if ( unit == 'rem' ) {
				value = parseFloat( getComputedStyle( $body.get(0) )['fontSize'] ) * value;
			}
		}
		return value;
	};

	// Return string with CSS rules from the shortcode's attributes
	window.trx_addons_get_css_from_atts = function( atts, allowed ) {
		var css = '',
			rule = '',
			atts_with_units = trx_addons_apply_filters( 'trx_addons_filter_atts_with_units', [ 'margin', 'padding', 'border-radius', 'border-width', 'font-size', 'line-height', 'letter-spacing', 'width', 'height', 'top', 'right', 'bottom', 'left' ] );
		for ( var k in allowed ) {
			if ( allowed[ k ] && ( atts.hasOwnProperty( k ) || typeof allowed[ k ] == 'object' && allowed[ k ].hasOwnProperty( 'default' ) ) ) {
				rule = typeof allowed[ k ] == 'object' ? allowed[ k ]['rule'] : allowed[ k ];
				if ( typeof allowed[ k ] == 'object' ) {
					css += rule + ':' + ( atts[ k ]
												? ( atts_with_units.indexOf( rule ) >= 0 ? trx_addons_prepare_css_value( atts[ k ] ) : atts[ k ] )
												: ( atts_with_units.indexOf( rule ) >= 0 ? trx_addons_prepare_css_value( allowed[ k ]['default'] ) : allowed[ k ]['default'] )
												)
									+ ';';
				} else {
					css += rule + ':' + ( atts_with_units.indexOf( rule ) >= 0 ? trx_addons_prepare_css_value( atts[ k ] ) : atts[ k ] ) + ';';
				}
			}
		}
		return css;
	};
	
	// Return nested property of the object
	window.trx_addons_get_object_property = function(obj, property, defa) {
		var rez = defa === undefined ? false : defa,
			props = property.split('.'),
			cur = obj;
		if ( typeof cur == 'object' ) {
			for (var i = 0; i < props.length; i++) {
				if ( cur.hasOwnProperty( props[i] ) ) {
					cur = cur[ props[i] ];
					if ( i == props.length - 1 ) {
						rez = cur;
					}
				} else {
					break;
				}
			}
		}
		return rez;
	};
	
	// Clone objects. Handle the 3 simple types, and null or undefined
	window.trx_addons_object_clone = function(obj) {
		var copy;
		// Handle null
		if (null === obj || "object" != typeof obj) {
			return obj;
		}
		// Handle Date
		if (obj instanceof Date) {
			copy = new Date();
			copy.setTime(obj.getTime());
			return copy;
		}
		// Handle Array
		if (obj instanceof Array) {
			copy = [];
			for (var i = 0, len = obj.length; i < len; i++) {
				copy[i] = trx_addons_object_clone(obj[i]);
			}
			return copy;
		}
		// Handle Object
		if (obj instanceof Object) {
			copy = {};
			for (var attr in obj) {
				if (obj.hasOwnProperty(attr)) {
					copy[attr] = trx_addons_object_clone(obj[attr]);
				}
			}
			return copy;
		}
		return obj;
	};

	// Merge objects
	window.trx_addons_object_merge = function(o1, o2) {
		for (var i=1; i<arguments.length; i++) {
			if ( arguments[i] ) {
				for (var prop in arguments[i]) {
					if ( arguments[i].hasOwnProperty(prop) ) {
						o1[prop] = arguments[i][prop];
					}
				}
			}
		}
		return o1;
	};

	// Return true if specified var is an object
	window.trx_addons_is_object = function( o ) {
		return typeof o === 'object' && o !== null && o.constructor && o.constructor === Object;
	};

	// Extend an object with properties other objects
	window.trx_addons_object_extend = function() {
		var args = [], total = arguments.length;
		while ( total-- ) {
			args[ total ] = arguments[ total ];
		}
		var to = Object( args[0] );
		for ( var i = 1; i < args.length; i++ ) {
			var nextSource = args[i];
			if ( nextSource !== undefined && nextSource !== null ) {
				var keysArray = Object.keys( Object( nextSource ) );
				for ( var nextIndex = 0, len = keysArray.length; nextIndex < len; nextIndex++ ) {
					var nextKey = keysArray[ nextIndex ];
					var desc = Object.getOwnPropertyDescriptor( nextSource, nextKey );
					if ( desc !== undefined && desc.enumerable ) {
						if ( trx_addons_is_object( to[ nextKey ] ) && trx_addons_is_object( nextSource[ nextKey ] ) ) {
							trx_addons_object_extend( to[ nextKey ], nextSource[ nextKey ] );
						} else if ( ! trx_addons_is_object( to[ nextKey ] ) && trx_addons_is_object( nextSource[ nextKey ] ) ) {
							to[ nextKey ] = {};
							trx_addons_object_extend( to[ nextKey ], nextSource[ nextKey ] );
						} else {
							to[ nextKey ] = nextSource[ nextKey ];
						}
					}
				}
			}
		}
		return to;
	};

	// Merge two arrays
	window.trx_addons_array_merge = function(a1, a2) {
		if ( a2 ) {
			for ( var i in a2 ) {
				a1[i] = a2[i];
			}
		}
		return a1;
	};

	// Get first key from array
	window.trx_addons_array_first_key = function(arr) {
		var rez = null;
		for (var i in arr) {
			rez = i;
			break;
		}
		return rez;
	};

	// Get first value from array
	window.trx_addons_array_first_value = function(arr) {
		var rez = null;
		for (var i in arr) {
			rez = arr[i];
			break;
		}
		return rez;
	};

	// Returns the name of the class of an object
	window.trx_addons_get_class = function(obj) {
		if (obj instanceof Object && !(obj instanceof Array) && !(obj instanceof Function) && obj.constructor) {
			var arr = obj.constructor.toString().match(/function\s*(\w+)/);
			if (arr && arr.length == 2) return arr[1];
		}
		return false;
	};

	// Generates a storable representation of a value
	window.trx_addons_serialize = function(mixed_val) {
		var obj_to_array = arguments.length==1 || argument[1]===true;
	
		switch ( typeof(mixed_val) ) {
	
			case "number":
				if ( isNaN(mixed_val) || !isFinite(mixed_val) )
					return false;
				else
					return (Math.floor(mixed_val) == mixed_val ? "i" : "d") + ":" + mixed_val + ";";
	
			case "string":
				return "s:" + mixed_val.length + ":\"" + mixed_val + "\";";
	
			case "boolean":
				return "b:" + (mixed_val ? "1" : "0") + ";";
	
			case "object":
				if (mixed_val == null)
					return "N;";
				else if (mixed_val instanceof Array) {
					var idxobj = { idx: -1 };
					var map = [];
					for (var i=0; i < mixed_val.length; i++) {
						idxobj.idx++;
						var ser = trx_addons_serialize(mixed_val[i]);
						if (ser)
							map.push(trx_addons_serialize(idxobj.idx) + ser);
					}                                      
					return "a:" + mixed_val.length + ":{" + map.join("") + "}";
				} else {
					var class_name = trx_addons_get_class(mixed_val);
					if (class_name == undefined)
						return false;
					var props = new Array();
					for (var prop in mixed_val) {
						var ser = trx_addons_serialize(mixed_val[prop]);
						if (ser)
							props.push(trx_addons_serialize(prop) + ser);
					}
					if (obj_to_array)
						return "a:" + props.length + ":{" + props.join("") + "}";
					else
						return "O:" + class_name.length + ":\"" + class_name + "\":" + props.length + ":{" + props.join("") + "}";
				}
	
			case "undefined":
				return "N;";
		}
		return false;
	};

	// Encode / Decode Unicode string to / from single-byte characters
	( function( $ ) {
		var b64 = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/",
		a256 = '',
		r64 = [256],
		r256 = [256],
		i = 0;

		var UTF8 = {

			/**
			 * Encode multi-byte Unicode string into utf-8 multiple single-byte characters
			 * (BMP / basic multilingual plane only)
			 *
			 * Chars in range U+0080 - U+07FF are encoded in 2 chars, U+0800 - U+FFFF in 3 chars
			 *
			 * @param {String} strUni Unicode string to be encoded as UTF-8
			 * @returns {String} encoded string
			 */
			encode: function(strUni) {
				// use regular expressions & String.replace callback function for better efficiency than procedural approaches
				var strUtf = strUni
								.replace(
									/[\u0080-\u07ff]/g, // U+0080 - U+07FF => 2 bytes 110yyyyy, 10zzzzzz
									function(c) {
										var cc = c.charCodeAt(0);
										return String.fromCharCode(0xc0 | cc >> 6, 0x80 | cc & 0x3f);
									}
								)
								.replace(
									/[\u0800-\uffff]/g, // U+0800 - U+FFFF => 3 bytes 1110xxxx, 10yyyyyy, 10zzzzzz
									function(c) {
										var cc = c.charCodeAt(0);
										return String.fromCharCode(0xe0 | cc >> 12, 0x80 | cc >> 6 & 0x3F, 0x80 | cc & 0x3f);
									}
								);
				return strUtf;
			},

			/**
			* Decode utf-8 encoded string back into multi-byte Unicode characters
			*
			* @param {String} strUtf UTF-8 string to be decoded back to Unicode
			* @returns {String} decoded string
			*/
			decode: function(strUtf) {
				// note: decode 3-byte chars first as decoded 2-byte strings could appear to be 3-byte char!
				var strUni = strUtf
								.replace(
									/[\u00e0-\u00ef][\u0080-\u00bf][\u0080-\u00bf]/g, // 3-byte chars
									function(c) { // (note parentheses for precence)
										var cc = ((c.charCodeAt(0) & 0x0f) << 12) | ((c.charCodeAt(1) & 0x3f) << 6) | (c.charCodeAt(2) & 0x3f);
										return String.fromCharCode(cc);
									}
								)
								.replace(
									/[\u00c0-\u00df][\u0080-\u00bf]/g, // 2-byte chars
									function(c) { // (note parentheses for precence)
										var cc = (c.charCodeAt(0) & 0x1f) << 6 | c.charCodeAt(1) & 0x3f;
										return String.fromCharCode(cc);
									}
								);
				return strUni;
			}
		};

		while( i < 256 ) {
			var c = String.fromCharCode(i);
			a256 += c;
			r256[i] = i;
			r64[i] = b64.indexOf(c);
			++i;
		}

		function code(s, discard, alpha, beta, w1, w2) {
			s = String(s);
			var buffer = 0,
				i = 0,
				length = s.length,
				result = '',
				bitsInBuffer = 0;

			while (i < length) {
				var c = s.charCodeAt(i);
				c = c < 256 ? alpha[c] : -1;

				buffer = (buffer << w1) + c;
				bitsInBuffer += w1;

				while (bitsInBuffer >= w2) {
					bitsInBuffer -= w2;
					var tmp = buffer >> bitsInBuffer;
					result += beta.charAt(tmp);
					buffer ^= tmp << bitsInBuffer;
				}
				++i;
			}
			if ( ! discard && bitsInBuffer > 0) {
				result += beta.charAt(buffer << (w2 - bitsInBuffer));
			}
			return result;
		}

		var Plugin = $.trx_addons_encoder = function(dir, input, encode) {
			return input ? Plugin[dir](input, encode) : dir ? null : this;
		};

		Plugin.btoa = Plugin.encode = function(plain, utf8encode) {
			plain = Plugin.raw === false || Plugin.utf8encode || utf8encode
						? UTF8.encode(plain)
						: plain;
			plain = code(plain, false, r256, b64, 8, 6);
			return plain + '===='.slice((plain.length % 4) || 4);
		};

		Plugin.atob = Plugin.decode = function(coded, utf8decode) {
			coded = String(coded).split('=');
			var i = coded.length;
			do {
				--i;
				coded[i] = code(coded[i], true, r64, a256, 6, 8);
			} while (i > 0);
			coded = coded.join('');
			return Plugin.raw === false || Plugin.utf8decode || utf8decode
					? UTF8.decode(coded)
					: coded;
		};
	}(jQuery) );


	/* Timing functions
	---------------------------------------------------------------- */

	// Make a first call of the 'func' immediatelly and the second call not closer then 'wait' (in ms)
	window.trx_addons_debounce = function(func, wait, first_call) {
		var timeout;
		if ( first_call === undefined ) {
			first_call = true;
		}
		return function () {
			var context = this, args = arguments;
			var later = function later() {
				timeout = null;
				func.apply(context, args);
			};
			var callNow = !timeout && first_call;
			clearTimeout(timeout);
			timeout = setTimeout(later, wait);
			if (callNow) {
				func.apply(context, args);
			}
		};
	};

	// First call a 'func' must be not closer then 'wait' (in ms) after the last call
	// a throttled function
	window.trx_addons_throttle = function(func, wait, debounce) {
		var timeout;
		return function () {
			var context = this, args = arguments;
			var throttler = function () {
				timeout = null;
				func.apply(context, args);
			};
			if (debounce) clearTimeout(timeout);
			if (debounce || !timeout) timeout = setTimeout(throttler, wait);
		};
	};
	
	// Linear interpolation
	window.trx_addons_lerp = function(start, end, amt){
		return (1 - amt) * start + amt * end;
	};

	// Change value in specified time
	window.trx_addons_tween_value = function( args ) {
		// Defaults
		if ( args.start == args.end ) {
			return null;
		}
		if ( ! args.time ) {
			args.time = 1;	// 1s
		}
		var t = {
			value: args.start
		};
		// Use TweenMax (if loaded)
		if ( typeof TweenMax != 'undefined' ) {
			return TweenMax.to( t, args.time, {
							overwrite: true,
							value: args.end,
							ease: args.ease ? args.ease : Power2.easeOut,
							onUpdate: function() {
								args.callbacks.onUpdate( t.value );
							},
							onComplete: function() {
								if ( args.callbacks.onComplete ) {
									args.callbacks.onComplete();
								}
							}
						} );
		// Internal tween manager
		} else {
			var amount = 0.1;
			var interval = Math.min( args.time * 1000 / 20, Math.max( 1, Math.round( args.time * 1000 / ( Math.abs( args.end - args.start ) / amount ) ) ) );
			return setInterval( function() {
				t.value = trx_addons_lerp( t.value, args.end, amount );
				args.callbacks.onUpdate( t.value );
				if (  Math.abs(t.value - args.end) < 0.0001 ) {
					t.value = args.end;
					args.callbacks.onUpdate( t.value );
					if ( args.callbacks.onComplete ) {
						args.callbacks.onComplete();
					}
				}
			}, interval );
		}
	};

	// Stop changing value
	window.trx_addons_tween_stop = function( handler ) {
		// Use TweenMax (if loaded)
		if ( typeof TweenMax != 'undefined' ) {
			if ( handler ) handler.kill();
		// Internal tween manager
		} else {
			if ( handler ) clearTimeout( handler );

		}
	};


	// jQuery animate() easings
	//--------------------------------------------------
	( function( $ ) {

		// Exit if easings are already extended
		if ( typeof $.easing['easeInSine'] != 'undefined' ) return;

		// Based on easing equations from Robert Penner (http://www.robertpenner.com/easing)
		var baseEasings = {};
		$.each( [ "Quad", "Cubic", "Quart", "Quint", "Expo" ], function( i, name ) {
			baseEasings[ name ] = function( p ) {
				return Math.pow( p, i + 2 );
			};
		} );

		$.extend( baseEasings, {
			Sine: function( p ) {
				return 1 - Math.cos( p * Math.PI / 2 );
			},
			Circ: function( p ) {
				return 1 - Math.sqrt( 1 - p * p );
			},
			Elastic: function( p ) {
				return p === 0 || p === 1 ? p : -Math.pow( 2, 8 * ( p - 1 ) ) * Math.sin( ( ( p - 1 ) * 80 - 7.5 ) * Math.PI / 15 );
			},
			Back: function( p ) {
				return p * p * ( 3 * p - 2 );
			},
			Bounce: function( p ) {
				var pow2,
					bounce = 4;
				while ( p < ( ( pow2 = Math.pow( 2, --bounce ) ) - 1 ) / 11 ) {}
				return 1 / Math.pow( 4, 3 - bounce ) - 7.5625 * Math.pow( ( pow2 * 3 - 2 ) / 22 - p, 2 );
			}
		} );

		$.each( baseEasings, function( name, easeIn ) {
			$.easing[ "easeIn" + name ] = easeIn;
			$.easing[ "easeOut" + name ] = function( p ) {
				return 1 - easeIn( 1 - p );
			};
			$.easing[ "easeInOut" + name ] = function( p ) {
				return p < 0.5 ? easeIn( p * 2 ) / 2 : 1 - easeIn( p * -2 + 2 ) / 2;
			};
		} );

	} )( jQuery );

	// CSS transitions and animations listener
	//--------------------------------------------------

	// Detect a name of the event "transition end"
	window.trx_addons_transition_end = function() {
		var e = document.createElement("transitionDetector"),
			t = {
				WebkitTransition: "webkitTransitionEnd",
				MozTransition: "transitionend",
				transition: "transitionend"
				},
			r = "transitionend";
		for ( var n in t ) {
			if ( undefined !== e.style[n] ) {
				r = t[n];
				break;
			}
		}
		return r;
	};
	
	// Detect a name of the event "animation end"
	window.trx_addons_animation_end = function() {
		var e = document.createElement("animationDetector"),
			t = {
				animation: "animationend",
				OAnimation: "oAnimationEnd",
				MozAnimation: "animationend",
				WebkitAnimation: "webkitAnimationEnd"
			},
			r = "animationend";
		for ( var n in t ) {
			if ( undefined !== e.style[n] ) {
				r = t[n];
				break;
			}
		}
		return r;
	};

	var support = {
			transitions: window.Modernizr ? Modernizr.csstransitions : false,
			animations: window.Modernizr ? Modernizr.cssanimations : false
		},
		trans_end_event_names = { 'WebkitTransition': 'webkitTransitionEnd', 'MozTransition': 'transitionend', 'OTransition': 'oTransitionEnd', 'msTransition': 'MSTransitionEnd', 'transition': 'transitionend' },
		trans_end_event_name  = window.Modernizr ? trans_end_event_names[ Modernizr.prefixed( 'transition' ) ] : trx_addons_transition_end(),
		anima_end_event_names = { 'WebkitAnimation': 'webkitAnimationEnd', 'MozAnimation': 'animationend', 'OAnimation': 'oAnimationEnd', 'msAnimation': 'MSAnimationEnd', 'animation': 'animationend' },
		anima_end_event_name  = window.Modernizr ? anima_end_event_names[ Modernizr.prefixed( 'animation' ) ] : trx_addons_animation_end();

	window.trx_addons_on_end_transition = function( el, callback, timeout ) {
		var on_end_callback = function( e ) {
			if ( support.transitions ) {
				if ( e.target != this ) {
					return;
				}
				this.removeEventListener( trans_end_event_name, on_end_callback );
			}
			if ( callback && typeof callback === 'function' ) {
				callback.call( this );
			}
		};
		if ( support.transitions ) {
			el.addEventListener( trans_end_event_name, on_end_callback, false );
		} else {
			setTimeout( function() {
				if ( callback && typeof callback === 'function' ) {
					callback.call( this );
				}
			}, timeout || 0 );
		}
	};

	window.trx_addons_on_end_animation = function( el, callback, timeout ) {
		var on_end_callback = function( e ) {
			if ( support.animations ) {
				if ( e.target != this ) {
					return;
				}
				this.removeEventListener( anima_end_event_name, on_end_callback );
			}
			if ( callback && typeof callback === 'function' ) {
				callback.call( this );
			}
		};
		if ( support.animations ) {
			el.addEventListener( anima_end_event_name, on_end_callback, false );
		} else {
			setTimeout( function() {
				if ( callback && typeof callback === 'function' ) {
					callback.call( this );
				}
			}, timeout || 0 );
		}
	};


	/* Mutation observers
	---------------------------------------------------------------- */
	var trx_addons_observers = {};

	// Create mutations observer
	window.trx_addons_create_observer = function( id, obj, callback, args ) {
		if ( typeof window.MutationObserver !== 'undefined' && obj && obj.length ) {
			if ( typeof trx_addons_observers[ id ] == 'undefined' ) {
				var defa = {
						attributes: false,
						childList: true,
						subtree: true
					};
				if ( args ) {
					defa = trx_addons_object_merge( defa, args );
				}
				trx_addons_observers[ id ] = {
					observer: new MutationObserver( callback ),
					obj: obj.get(0)
				};
				trx_addons_observers[ id ].observer.observe( trx_addons_observers[ id ].obj, defa );
			}
			return true;
		}
		return false;
	};

	// Remove mutations observer
	window.trx_addons_remove_observer = function( id ) {
		if ( typeof window.MutationObserver !== 'undefined' ) {
			if ( typeof trx_addons_observers[ id ] !== 'undefined' ) {
				trx_addons_observers[ id ].observer.disconnect(
//					trx_addons_observers[ id ].obj
				);
				delete trx_addons_observers[ id ];
			}
			return true;
		}
		return false;
	};

	// Check mutations for selector
	window.trx_addons_check_mutations = function( mutations, selector, action ) {
		var rez = false;
		if ( typeof mutations != 'object' || ! mutations.hasOwnProperty( 'length' ) || ! mutations.length ) {
			return rez;
		}
		var nodes = false;
		for ( var i = 0; i < mutations.length; i++ ) {
			nodes = action == 'add' ? mutations[i].addedNodes : mutations[i].removedNodes;
			for ( var n = 0; n < nodes.length; n++ ) {
				var $node = jQuery( nodes[n] );
				if ( selector.charAt(0) == '.' && $node.hasClass( selector.slice( 1 ) )
					|| selector.charAt(0) == '#' && $node.attr( 'id' ) == selector.slice( 1 )
				) {
					rez = true;
					break;
				}
			}
			if ( rez ) break;
		}
		return rez;
	};


	/* Sticky observers
	---------------------------------------------------------------- */
	var trx_addons_sticky_observers = {};

	// Create sticky observer
	window.trx_addons_sticky_observer_create = function( id, obj, callback, args ) {
		if ( typeof window.IntersectionObserver !== 'undefined' && obj && obj.length ) {
			if ( typeof trx_addons_sticky_observers[ id ] == 'undefined' ) {
				var defa = {
					root: null,
					rootMargin: ( 1 + trx_addons_fixed_rows_height() ) + 'px 0px 0px 0px',
					threshold: 1	//[1]

				};
				if ( args ) {
					defa = trx_addons_object_merge( defa, args );
				}
				trx_addons_sticky_observers[ id ] = {
					observer: new IntersectionObserver( function( entries ) {
						entries.forEach( function( entry ) {
							var is_sticky = entry.isIntersecting && entry.intersectionRatio >= 1;
							jQuery( entry.target ).toggleClass( 'trx_addons_is_sticky', is_sticky );
							if ( callback && typeof callback === 'function' ) {
								callback( entry, is_sticky );
							}
						} );
					}, defa ),
					obj: obj.get(0)
				};
				trx_addons_sticky_observers[ id ].observer.observe( trx_addons_sticky_observers[ id ].obj );
			}
			return true;
		}
		return false;
	};

	window.trx_addons_sticky_observer_remove = function( id ) {
		if ( typeof window.IntersectionObserver !== 'undefined' ) {
			if ( typeof trx_addons_sticky_observers[ id ] !== 'undefined' ) {
				// trx_addons_sticky_observers[ id ].observer.unobserve( trx_addons_sticky_observers[ id ].obj );
				trx_addons_sticky_observers[ id ].observer.disconnect();
				delete trx_addons_sticky_observers[ id ];
			}
			return true;
		}
		return false;
	};


	/* Wordpress-style functions
	   Attention! wp.hooks available only in the admin area
	---------------------------------------------------------------- */

	var filters = {};

	// Add filter's handler
	window.trx_addons_add_filter = function( filter, callback, priority ) {
		if ( priority === undefined ) priority = 10;
		if ( typeof wp != 'undefined' && typeof wp.hooks != 'undefined' ) {
			wp.hooks.addFilter( filter, 'trx_addons', callback, priority );
		} else {
			if ( ! filters[filter] ) filters[filter] = {};
			if ( ! filters[filter][priority] ) filters[filter][priority] = [];
			filters[filter][priority].push( callback );
		}
	};

	// Apply filter's handlers
	window.trx_addons_apply_filters = function( filter, arg1, arg2, arg3, arg4, arg5, arg6, arg7, arg8, arg9 ) {
		if ( typeof wp != 'undefined' && typeof wp.hooks != 'undefined' && typeof wp.hooks.applyFilters != 'undefined' ) {
			arg1 = wp.hooks.applyFilters( filter, arg1, arg2, arg3, arg4, arg5, arg6, arg7, arg8, arg9 );
		} else if ( typeof filters[filter] == 'object' ) {
			var keys = Object.keys(filters[filter]).sort();
			for (var i=0; i < keys.length; i++ ) {
				for (var j=0; j < filters[filter][keys[i]].length; j++ ) {
					if ( typeof filters[filter][keys[i]][j] == 'function' ) {
						arg1 = filters[filter][keys[i]][j](arg1, arg2, arg3, arg4, arg5, arg6, arg7, arg8, arg9);
					}
				}
			}
		}
		return arg1;
	};

	// Add action's handler
	window.trx_addons_add_action = function( action, callback, priority ) {
		if ( typeof wp != 'undefined' && typeof wp.hooks != 'undefined' ) {
			wp.hooks.addAction( action, 'trx_addons', callback, priority == undefined ? 10 : priority );
		} else {
			trx_addons_add_filter( action, callback, priority );
		}
	};

	// Do action's handlers
	window.trx_addons_do_action = function( action, arg1, arg2, arg3, arg4, arg5, arg6, arg7, arg8, arg9 ) {
		if ( typeof wp != 'undefined' && typeof wp.hooks != 'undefined' && typeof wp.hooks.doActions != 'undefined' ) {
			wp.hooks.doActions( action, arg1, arg2, arg3, arg4, arg5, arg6, arg7, arg8, arg9 );
		} else {
			trx_addons_apply_filters( action, arg1, arg2, arg3, arg4, arg5, arg6, arg7, arg8, arg9 );
		}
	};

})();