//-------------------------------------------
// Options handlers
//-------------------------------------------

/* global jQuery, TRX_ADDONS_STORAGE */


// Add 'sticky' behaviour to the options header
//------------------------------------------------------------
jQuery( window ).on( 'scroll', function() {

	"use strict";

	var header = jQuery( '.trx_addons_options_header' );
	if ( header.length !== 0 ) {
		var placeholder = jQuery( '.trx_addons_options_header_placeholder' );
		if ( jQuery( '.trx_addons_options_header_placeholder' ).length === 0 ) {
			jQuery( '.trx_addons_options_header' ).before( '<div class="trx_addons_options_header_placeholder"></div>' );
			placeholder = jQuery( '.trx_addons_options_header_placeholder' );
		}
		if ( placeholder.length !== 0 ) {
			header.toggleClass( 'sticky', placeholder.offset().top < jQuery( window ).scrollTop() + jQuery( '#wpadminbar' ).height() );
		}
	}
} );


// Init options
//----------------------------------------------------------------
jQuery(document).ready( function() {

	"use strict";

	var trx_addons_options_changed_state = false;

	// Set a new options state or return a current state (if no param specified)
	function trx_addons_options_changed( state ) {
		if ( state !== undefined ) {
			trx_addons_options_changed_state = state;
		}
		return trx_addons_options_changed_state;
	}

	// Check to exit while options changed
	jQuery( window ).on( 'beforeunload', function( e ) {
		if ( trx_addons_options_changed()
			&& ( jQuery( '#trx_addons_theme_panel' ).length === 0 || jQuery( '#trx_addons_theme_panel_section_qsetup' ).is( ':visible' ) )
		) {
			e.preventDefault();
			return e.returnValue = TRX_ADDONS_STORAGE[ 'msg_exit_not_saved_options' ];
		}
	} );

	// Set a global state 'changed' on any field is changed
	setTimeout( function() {
		jQuery('.trx_addons_options .trx_addons_options_item_field [name^="trx_addons_options_field_"]').on('change', function () {
			trx_addons_options_changed( true );
		});
	}, 600 );

	// --------------------------- SAVE / RESET & EXPORT / IMPORT OPTIONS ------------------------------

	// Save options
	jQuery('.trx_addons_options_button_submit')
		.on('click', function( e ) {
			trx_addons_options_changed( false );
			jQuery( this ).parents( '.trx_addons_options' ).find( 'form' ).submit();
			e.preventDefault();
			return false;
		} );

	// Reset options
	jQuery( '.trx_addons_options_button_reset' )
		.on( 'click', function( e ) {
			var form = jQuery( this ).parents( '.trx_addons_options' ).find( 'form' );
			if ( typeof trx_addons_msgbox_confirm != 'undefined' ) {
				trx_addons_msgbox_agree(
					TRX_ADDONS_STORAGE[ 'msg_reset_confirm' ],
					TRX_ADDONS_STORAGE[ 'msg_reset' ],
					function( btn ) {
						if ( btn === 1 ) {
							trx_addons_options_changed( false );
							form.find( 'input[name="trx_addons_options_field_reset_options"]' ).val( 1 );
							form.submit();
						}
					}
				);
			} else if ( confirm( TRX_ADDONS_STORAGE[ 'msg_reset_confirm' ] ) ) {
				form.find( 'input[name="trx_addons_options_field_reset_options"]' ).val( 1 ).end().submit();
			}
			e.preventDefault();
			return false;
		} );

	// Export options
	jQuery( '.trx_addons_options_button_export' )
		.on( 'click', function( e ) {
			var form = jQuery( this ).parents( '.trx_addons_options' ).find( 'form' ),
				data = '';
			form.find('[data-param]').each( function() {
				form
					.find('[name="trx_addons_options_field_' + jQuery(this).data('param') + '"],[name^="trx_addons_options_field_' + jQuery(this).data('param') + '["]')
					.each(function() {
						var fld = jQuery(this),
							fld_name = fld.attr('name'),
							fld_type = fld.attr('type') ? fld.attr('type') : fld.get(0).tagName.toLowerCase();
						if ( fld_type == 'checkbox' ) {
							data += ( data ? '&' : '' ) + fld_name + '=' + encodeURIComponent( fld.get(0).checked ? fld.val() : 0 );
						} else if ( fld_type != 'radio' || fld.get(0).checked ) {
							data += ( data ? '&' : '' ) + fld_name + '=' + encodeURIComponent( fld.val() );
						}
					});
			});
			if ( typeof trx_addons_msgbox_info != 'undefined' ) {
				trx_addons_msgbox_info(
					jQuery.trx_addons_encoder.encode( data ),
					TRX_ADDONS_STORAGE[ 'msg_export' ] + ': ' + TRX_ADDONS_STORAGE[ 'msg_export_options' ],
					'info',
					0
				);
			} else {
				alert( TRX_ADDONS_STORAGE[ 'msg_export_options' ] + ':\n\n' + jQuery.trx_addons_encoder.encode( data ) );
			}
			e.preventDefault();
			return false;
		} );

	// Import options
	jQuery( '.trx_addons_options_button_import' )
		.on( 'click', function( e ) {
			var form = jQuery( this ).parents( '.trx_addons_options' ).find( 'form' ),
				data = '';
			if ( typeof trx_addons_msgbox_dialog != 'undefined' ) {
				trx_addons_msgbox_dialog(
					'<textarea rows="10" cols="100"></textarea>',
					TRX_ADDONS_STORAGE[ 'msg_import' ] + ': ' + TRX_ADDONS_STORAGE[ 'msg_import_options' ],
					null,
					function(btn, box) {
						if ( btn === 1 ) {
							trx_addons_options_import_data( box.find('textarea').val() );
						}
					}
				);
			} else if ( ( data = prompt( TRX_ADDONS_STORAGE[ 'msg_import_options' ], '' ) ) !== '' ) {
				trx_addons_options_import_data( data );
			}

			function trx_addons_options_import_data( data ) {
				if ( data ) {
					data = jQuery.trx_addons_encoder.decode( data ).split( '&' );
					for ( var i in data ) {
						var param = data[i].split('=');
						if ( param.length == 2 && param[0].substr(-6) != '_nonce' ) {
							var fld = form.find('[name="'+param[0]+'"]'),
								val = decodeURIComponent(param[1]);
							if ( fld.attr('type') == 'radio' || fld.attr('type') == 'checkbox' ) {
								fld.removeAttr( 'checked' );
								fld.each( function() {
									var item = jQuery(this);
									if ( item.val() == val ) {
										item.get(0).checked = true;
										item.attr('checked', 'checked');
									}
								} );
							} else {
								fld.val( val );
							}
						}
					}
					trx_addons_options_changed( false );
					form.submit();
				} else {
					if ( typeof trx_addons_msgbox_warning != 'undefined' ) {
						trx_addons_msgbox_warning(
							TRX_ADDONS_STORAGE[ 'msg_import_error' ],
							TRX_ADDONS_STORAGE[ 'msg_import' ]
						);
					}
				}
			}

			e.preventDefault();
			return false;

		} );

	// Init fields
	trx_addons_options_init_fields();
	jQuery( document ).on( 'action.init_hidden_elements', trx_addons_options_init_fields );
	jQuery( document ).on( 'tinymce-editor-init', function() {
		trx_addons_options_init_fields( 'tinymce' );
	} );

	// Init fields at first run and after clone group
	function trx_addons_options_init_fields(e, container) {
		
		if (container === undefined) container = jQuery('.trx_addons_options,body').eq(0);

		// Init checkbox
		container.find( '.trx_addons_options_item_checkbox:not(.inited)' ).addClass( 'inited' )
			.on( 'keydown', '.trx_addons_options_item_holder', function( e ) {
				// If 'Enter' or 'Space' is pressed - switch state of the checkbox
				if ( [ 13, 32 ].indexOf( e.which ) >= 0 ) {
					jQuery( this ).prev().get( 0 ).checked = ! jQuery( this ).prev().get( 0 ).checked;
					e.preventDefault();
					return false;
				}
				return true;
			});
		
		// Init switch
		container.find( '.trx_addons_options_item_switch:not(.inited)' ).addClass( 'inited' )
			.on( 'keydown', '.trx_addons_options_item_holder', function( e ) {
				// If 'Enter', 'Space', 'Left' or 'Right' arrow is pressed - switch state of the checkbox
				if ( [ 13, 32, 37, 39 ].indexOf( e.which ) >= 0 ) {
					jQuery( this ).prev().get( 0 ).checked = ! jQuery( this ).prev().get( 0 ).checked;
					e.preventDefault();
					return false;
				}
				return true;
			});
		
		// Init radio
		container.find( '.trx_addons_options_item_radio:not(.inited)' ).addClass( 'inited' )
			.on( 'keydown', '.trx_addons_options_item_holder', function( e ) {
				// If 'Enter' or 'Space' is pressed - switch state of the checkbox
				if ( [ 13, 32 ].indexOf( e.which ) >= 0 ) {
					jQuery( this ).parents( 'trx_addons_options_item_field' ).find( 'input:checked' ).each( function() {
						this.checked = false;
					});
					jQuery( this ).prev().get( 0 ).checked = true;
					e.preventDefault();
					return false;
				}
				return true;
			});

		// Init checklist
		container.find('.trx_addons_options_item_choises:not(.inited)').addClass('inited')
			.on('change', 'input[type="checkbox"]', function() {
				var choises = '';
				var cont = jQuery(this).parents('.trx_addons_options_item_choises');
				cont.find('input[type="checkbox"]').each(function() {
					choises += (choises ? '|' : '') + jQuery(this).data('name') + '=' + (jQuery(this).get(0).checked ? jQuery(this).val() : '0');
				});
				cont.find('input[type="hidden"]').eq(0).val(choises).trigger('change');
			})
			.each(function() {
				if (jQuery.ui.sortable && jQuery(this).hasClass('trx_addons_options_sortable')) {
					var id = jQuery(this).attr('id');
					if (id === undefined) {
						jQuery(this).attr('id', 'trx_addons_options_sortable_'+(''+Math.random()).replace('.', ''));
					}
					jQuery(this).sortable({
						items: ".trx_addons_options_item_sortable",
						placeholder: ' trx_addons_options_item_label trx_addons_options_sortable_placeholder',
						update: function(event, ui) {
							var choises = '';
							ui.item.parent().find('input[type="checkbox"]').each(function() {
								choises += (choises ? '|' : '') 
										+ jQuery(this).data('name') + '=' + (jQuery(this).get(0).checked ? jQuery(this).val() : '0');
							});
							ui.item.siblings('input[type="hidden"]').eq(0).val(choises).trigger('change');
						}
					})
					.disableSelection();
				}
			});
		
		// Init socials selector
		container.find('[data-param*="socials["] .trx_addons_icon_selector:not(.icons_inited),[data-param*="share["] .trx_addons_icon_selector:not(.icons_inited)').addClass('icons_inited')
			.siblings('input[type="hidden"]')
			.on('change', function() {
				var icon = jQuery(this).val().replace('trx_addons_icon-', '').replace('icon-', ''),
					parts = jQuery(this).attr('name').split('['),
					idx = -1;
				if (parts.length > 1) {
					parts = parts[1].split(']');
					idx = Number(parts[0]);
					if (isNaN(idx)) idx = -1;
				}
				if (idx >= 0) {
					var fields_set = jQuery(this).parents('.trx_addons_options_fields_set').eq(0),
						title_fld = fields_set.find('[data-param="socials['+idx+'][title]"] > input,[data-param="share['+idx+'][title]"] > input'),
						url_fld = fields_set.find('[data-param="share['+idx+'][url]"] > input');
					if (title_fld.length > 0) {		// && title_fld.val() == '') {
						title_fld.val( icon.charAt(0).toUpperCase() + icon.substring(1).toLowerCase() );
					}
					if (url_fld.length > 0) {		// && url_fld.val() == '') {
						url_fld.val(
							typeof TRX_ADDONS_SOCIAL_SHARE !== 'undefined' && typeof TRX_ADDONS_SOCIAL_SHARE[icon] !== 'undefined'
								? TRX_ADDONS_SOCIAL_SHARE[icon]
								: ''
						);
					}
				}
			});


		// Init Select2
		if (jQuery.fn && jQuery.fn.select2) {
			container.find('.trx_addons_options_item_select2 select:not(.inited)').addClass('inited').select2();
		}
	
		// Init datepicker
		if (jQuery.ui.datepicker) {
			container.find('.trx_addons_options_item_date input[type="text"]:not(.inited)').addClass('inited')
				.each(function () {
					var curDate = jQuery(this).val();
					jQuery(this).datepicker({
						dateFormat: jQuery(this).data('format'),
						numberOfMonths: jQuery(this).data('months'),
						gotoCurrent: true,
						changeMonth: true,
						changeYear: true,
						defaultDate: curDate,
						onSelect: function (text, ui) {
							ui.input.trigger('change');
						}
					});
				});
		}

		// Init range slider
		if (jQuery.ui && jQuery.ui.slider) {
			container.find('.trx_addons_range_slider:not(.inited)').each(function () {
				// Get parameters
				var range_slider = jQuery(this);
				var linked_field = range_slider.data('linked_field');
				if ( linked_field === undefined ) {
					linked_field = range_slider.prev('input[type="hidden"],input[type="text"]');
				} else {
					linked_field = jQuery('#'+linked_field);
				}
				if ( linked_field.length == 0 ) {
					return;
				}
				linked_field.on(
					'change', function() {
						var minimum = range_slider.data( 'min' );
						if ( minimum === undefined ) {
							minimum = 0;
						} else {
							minimum = Number( ( '' + minimum ).replace( ',', '.' ) );
						}
						var maximum = range_slider.data( 'max' );
						if ( maximum === undefined ) {
							maximum = 0;
						} else {
							maximum = Number( ( '' + maximum ).replace( ',', '.' ) );
						}
						var values = jQuery( this ).val().split( ',' );
						for (var i = 0; i < values.length; i++) {
							if (isNaN( values[i] )) {
								value[i] = minimum;
							}
							values[i] = Math.max( minimum, Math.min( maximum, Number( values[i] ) ) );
							if (values.length == 1) {
								range_slider.slider( 'value', values );
							} else {
								range_slider.slider( 'values', i, values[i] );
							}
						}
						update_cur_values( values );
						jQuery( this ).val( values.join( ',' ) );
					}
				);
				var range_slider_cur = range_slider.find('> .trx_addons_range_slider_label_cur');
				var range_slider_type = range_slider.data('range');
				if ( range_slider_type === undefined ) {
					range_slider_type = 'min';
				}
				var values = linked_field.val().split(',');
				var minimum = range_slider.data('min');
				if ( minimum === undefined ) {
					minimum = 0;
				} else {
					minimum = Number( ( '' + minimum ).replace( ',', '.' ) );
				}
				var maximum = range_slider.data('max');
				if ( maximum === undefined ) {
					maximum = 0;
				} else {
					maximum = Number( ( '' + maximum ).replace( ',', '.' ) );
				}
				var step = range_slider.data('step');
				if ( step === undefined ) {
					step = 1;
				} else {
					step = Number( ( '' + step ).replace( ',', '.' ) );
				}
				// Init range slider
				var init_obj = {
					range: range_slider_type,
					min: minimum,
					max: maximum,
					step: step,
					slide: function(event, ui) {
						var cur_values = range_slider_type === 'min' ? [ui.value] : ui.values;
						linked_field.val(cur_values.join(',')).trigger('change');
						update_cur_values( cur_values );
					},
					create: function(event, ui) {
						update_cur_values( values );
					}
				};
				function update_cur_values(cur_values) {
					for (var i = 0; i < cur_values.length; i++) {
						range_slider_cur.eq( i )
							.html( cur_values[i] )
							.css( 'left', Math.max( 0, Math.min( 100, ( cur_values[i] - minimum ) * 100 / ( maximum - minimum ) ) ) + '%' );
					}
				}
				if ( range_slider_type === true ) {
					init_obj.values = values;
				} else {
					init_obj.value = values[0];
				}
				range_slider.addClass('inited').slider(init_obj);
			});
		}
	
		// Init masked input
		container.find('.trx_addons_options_item input[data-mask]:not(.inited)').addClass('inited')
			.each(function () {
				if (jQuery.fn && jQuery.fn.mask) jQuery(this).mask(''+jQuery(this).data('mask'));
			});

		// Init text editor (save editors content to the hidden field)
		container.find('.trx_addons_text_editor:not(.inited)')
			.each(function () {
				var $self  = jQuery( this );
				if ( e === 'tinymce' ) {
					TRX_ADDONS_STORAGE['tinymce'] = true;
				}
				if ( ! TRX_ADDONS_STORAGE['tinymce'] ) return;
				$self.addClass('inited');
				var tArea  = $self.find( '.wp-editor-area' ),
					id     = tArea.attr( 'id' ),
					input  = tArea.parents( '.trx_addons_text_editor' ).prev(),
					editor = tinyMCE.get( id ),
					content;
				// Duplicate content from TinyMCE editor
				if (editor) {
					editor.on(
						'change', function () {
							this.save();
							content = editor.getContent();
							input.val( content ).trigger( 'change' );
						}
					);
				}
				// Duplicate content from HTML editor
				tArea.css(
					{
						visibility: 'visible'
					}
				).on(
					'keyup', function(){
						content = tArea.val();
						input.val( content ).trigger( 'change' );
					}
				);
			});

		// Button with action
		container.find('.trx_addons_options_item_button input[type="button"]:not(.inited),.trx_addons_options_item_button .trx_addons_button:not(.inited)').addClass('inited')
			.on('click', function(e) {
				var button = jQuery(this),
					cb = button.data('callback');
				if (cb !== undefined && typeof window[cb] !== 'undefined') {
					window[cb]();
				} else {
					jQuery.post(TRX_ADDONS_STORAGE['ajax_url'], {
						action: button.data('action'),
						nonce: TRX_ADDONS_STORAGE['ajax_nonce']
					}).done(function(response) {
						var rez = {};
						if (response === '' || response === 0) {
							rez = { error: TRX_ADDONS_STORAGE['msg_ajax_error'] };
						} else {
							try {
								rez = JSON.parse(response);
							} catch (e) {
								rez = { error: TRX_ADDONS_STORAGE['msg_ajax_error'] };
								console.log(response);
							}
						}
						alert(rez.error ? rez.error : rez.success);
					});
				}
				e.preventDefault();
				return false;
			});


		// Init cloned fields
		//--------------------------------------
		trx_addons_options_clone_toggle_buttons(container);
		container.find('.trx_addons_options_group:not(.inited)').addClass('inited').each(function() {
			jQuery(this)
				// Button 'Add new'
				.on('click', '.trx_addons_options_clone_button_add', function (e) {
					var clone_obj = jQuery(this).parents('.trx_addons_options_clone_buttons').prev('.trx_addons_options_clone').eq(0),
						group = clone_obj.parents('.trx_addons_options_group');
					// Clone fields
					trx_addons_options_clone(clone_obj);
					// Enable/Disable clone buttons
					trx_addons_options_clone_toggle_buttons(group);
					// Prevent bubble event
					e.preventDefault();
					return false;
				})
				// Button 'Clone'
				.on('click', '.trx_addons_options_clone > .trx_addons_options_clone_control_add', function (e) {
					var clone_obj = jQuery(this).parents('.trx_addons_options_clone'),
						group = clone_obj.parents('.trx_addons_options_group');
					// Clone fields
					trx_addons_options_clone(clone_obj);
					// Enable/Disable clone buttons
					trx_addons_options_clone_toggle_buttons(group);
					// Prevent bubble event
					e.preventDefault();
					return false;
				})
				// Button 'Delete'
				.on('click', '.trx_addons_options_clone > .trx_addons_options_clone_control_delete', function (e) {
					var clone_obj = jQuery(this).parents('.trx_addons_options_clone'),
						clone_idx = clone_obj.prevAll('.trx_addons_options_clone').length,
						group = clone_obj.parents('.trx_addons_options_group');
					// Delete clone
					clone_obj.remove();
					// Change fields index
					trx_addons_options_clone_change_index(group, clone_idx);
					// Enable/Disable clone buttons
					trx_addons_options_clone_toggle_buttons(group);
					// Prevent bubble event
					e.preventDefault();
					return false;
				});
			// Sort clones
			if (jQuery.ui.sortable) {
				var id = jQuery(this).attr('id');
				if (id === undefined) {
					jQuery(this).attr('id', 'trx_addons_options_sortable_'+(''+Math.random()).replace('.', ''));
				}
				jQuery(this)
					.sortable({
						items: '.trx_addons_options_clone',
						handle: '.trx_addons_options_clone_control_move',
						placeholder: ' trx_addons_options_clone trx_addons_options_clone_placeholder',
						start: function (event, ui) {
							// Make the placeholder has the same height as dragged item
							ui.placeholder.height(ui.item.height());
						},
						update: function(event, ui) {
							// Change fields index
							trx_addons_options_clone_change_index(ui.item.parents('.trx_addons_options_group'), 0);
						}
					});
			}
		});
		
		// Check clone controls for enable/disable
		function trx_addons_options_clone_toggle_buttons(container) {
			if ( ! container.hasClass('trx_addons_options_group') ) {
				container = container.find('.trx_addons_options_group');
			}
			container.each( function() {
				var group = jQuery(this);
				if ( group.find('.trx_addons_options_clone').length > 1 ) {
					group.find('.trx_addons_options_clone_control_delete,.trx_addons_options_clone_control_move').show();
				} else {
					group.find('.trx_addons_options_clone_control_delete,.trx_addons_options_clone_control_move').hide();
				}
			} );
		}
		
		// Replace number in the param's name like 'floor_plans[0][image]'
		function trx_addons_options_clone_replace_index(name, idx_new) {
			name = name.replace(/\[\d{1,2}\]/, '['+idx_new+']');
			return name;
		}
		
		// Change index in each field in the clone
		function trx_addons_options_clone_change_index(group, from_idx) {
			group.find('.trx_addons_options_clone').each( function(idx) {
				if ( idx < from_idx ) return;
				jQuery(this).find('.trx_addons_options_item_field').each( function() {
					var field = jQuery(this),
						param_old = field.data('param'),
						param_old_id = param_old.replace(/\[/g, '_').replace(/\]/g, ''),
						param_new = trx_addons_options_clone_replace_index( param_old, idx ),
						param_new_id = param_new.replace(/\[/g, '_').replace(/\]/g, '');
					// Change data-param
					field.attr('data-param', param_new );
					// Change name and id in inputs
					field.find(':input').each(function() {
						var input = jQuery(this),
							id = input.attr('id'),
							name = input.attr('name');
						if ( ! name ) return;
						name = trx_addons_options_clone_replace_index(name, idx);
						input.attr('name', name);
						if ( id ) {
							var id_new = name.replace(/\[/g, '_').replace(/\]/g, '');
							input.attr('id', id_new);
							var linked_field = field.find('[data-linked-field="'+id+'"]');
							if ( linked_field.length > 0 ) {
								linked_field.attr('data-linked-field', id_new);
								if ( linked_field.attr('id') ) {
									linked_field.attr('id', linked_field.attr('id').replace(id, id_new));
								}
							}
						}
					} );
					// Change name and id in any tags
					field.find('[id*="'+param_old_id+'"],[name*="'+param_old_id+'"]').each( function() {
						var $self = jQuery(this),
							name = $self.attr('name'),
							id = $self.attr('id'),
							data_id = $self.data( 'wp-editor-id' );
						if ( name ) {
							$self.attr( 'name', name.replace( param_old_id, param_new_id ) );
						}
						if ( id ) {
							$self.attr( 'id', id.replace( param_old_id, param_new_id ) );
						}
						if ( data_id ) {
							$self.attr( 'data-wp-editor-id', data_id.replace( param_old_id, param_new_id ) );
						}
					} );
				});
			});
		}
		
		// Clone set of the fields
		function trx_addons_options_clone(obj) {
			var group = obj.parent(),
				clone = obj.clone(),
				obj_idx = obj.prevAll('.trx_addons_options_clone').length;
			// Remove class 'inited' from all elements
			clone.find('.inited').removeClass('inited');
			clone.find('.inited_media_selector').removeClass('inited_media_selector');
			clone.find('.icons_inited').removeClass('icons_inited');
			// Reset text editor area
			var editor = clone.find('.trx_addons_text_editor');
			if ( editor.length ) {
				editor.html( editor.data( 'editor-html' ) );
			}
			// Reset value for fields
			clone.find('.trx_addons_options_item_field :input').each(function() {
				var input = jQuery(this),
					std = input.data('std');
				if (input.is(':radio') || input.is(':checkbox')) {
					input.prop( 'checked', std !== undefined && std == input.val() );
				} else if (input.is('select')) {
					input.prop( 'selectedIndex', -1 );
					if ( std !== undefined ) {
						var opt = input.find('option[value="'+std+'"]');
						if ( opt.length > 0 ) {
							input.prop('selectedIndex', opt.index());
						}
					}
				} else if (!input.is(':button')) {
					input.val( std !== undefined ? std : '' );
				}
				// Remove image preview
				input.parents('.trx_addons_options_item_field').find('.trx_addons_media_selector_preview').empty();
				// Remove class 'inited' from selectors
				input.next('[class*="_selector"].inited').removeClass('inited');
			});
			//Remove UI sliders
			clone.find('.ui-slider-range, .ui-slider-handle').remove();
			// Remove color picker wrapper
			clone.find('.wp-picker-container').each( function() {
				var $wrapper = jQuery(this),
					$field = $wrapper.find('.trx_addons_color_selector');
				if ( $field.length > 0 ) {
					$wrapper.after( $field );
					$wrapper.remove();
				}
			} );
			// Insert Clone
			clone.insertAfter(obj);
			// Change fields index. Must run before trigger clone event
			trx_addons_options_clone_change_index(group, obj_idx);
			// Init of the cloned text editor
			if ( editor.length && typeof tinymce !== 'undefined' ) {
				var old_id = group.find( '.wp-editor-area' ).eq(0).attr('id'),
					new_id = editor.find( '.wp-editor-area' ).attr( 'id' ),
					init   = typeof tinyMCEPreInit != 'undefined' && typeof tinyMCEPreInit.mceInit != 'undefined' && typeof tinyMCEPreInit.mceInit[ old_id ] != 'undefined'
								? tinyMCEPreInit.mceInit[ old_id ]
								: { tinymce: true };
				if ( init.body_class ) {
					init.body_class = init.body_class.replace( old_id, new_id );
				}
				if ( init.selector ) {
					init.selector = init.selector.replace( old_id, new_id );
				}
				if ( typeof tinyMCEPreInit != 'undefined' ) {
					tinyMCEPreInit.mceInit[ new_id ] = init;
				}

				var $wrap;

				if ( typeof tinymce !== 'undefined' ) {
					if ( tinymce.Env.ie && tinymce.Env.ie < 11 ) {
						tinymce.$( '.wp-editor-wrap ' ).removeClass( 'tmce-active' ).addClass( 'html-active' );
					} else {
						$wrap = tinymce.$( '#wp-' + new_id + '-wrap' );
						if ( ( $wrap.hasClass( 'tmce-active' ) || ! tinyMCEPreInit.qtInit.hasOwnProperty( new_id ) ) && ! init.wp_skip_init ) {
							tinymce.init( init );
							if ( ! window.wpActiveEditor ) {
								window.wpActiveEditor = new_id;
							}
						}

						if ( typeof quicktags !== 'undefined' && tinyMCEPreInit.qtInit.hasOwnProperty( new_id ) ) {
							quicktags( tinyMCEPreInit.qtInit[new_id] );
							if ( ! window.wpActiveEditor ) {
								window.wpActiveEditor = new_id;
							}
						}
					}
				}

				//wp.editor.initialize( new_id, init );
			}
			// Fire init actions for cloned fields
			jQuery(document).trigger( 'action.init_hidden_elements', [clone.parents('.trx_addons_options')] );
		}
	}

});


// Check fields dependencies
//--------------------------------------------------------------

// Check for external dependencies (for example, "Page template" in the page edit mode)
jQuery( window ).on( 'load', function() {
	"use strict";

	var attempts = 5,
		attempts_timer = setInterval( function() {
			if ( --attempts < 0 && attempts_timer ) {
				clearInterval( attempts_timer );
				attempts_timer = null;
				return;
			}
			jQuery( '.trx_addons_options .trx_addons_options_section' ).each( function () {
				trx_addons_options_check_dependencies( jQuery( this ) );
			} );
		}, 3000 );
} );

// Check for internal dependencies
jQuery( document ).ready( function() {
	"use strict";
	
	// Check all inner dependencies
	jQuery( '.trx_addons_options .trx_addons_options_section' ).each( function () {
		trx_addons_options_check_dependencies( jQuery( this ) );
	} );

	// Check dependencies on any field change
	jQuery( '.trx_addons_options .trx_addons_options_item_field [name^="trx_addons_options_field_"]' ).on( 'change', function () {
		trx_addons_options_check_dependencies(jQuery(this).parents('.trx_addons_options_section'));
	} );

	// Check dependencies on a field with a page template is appear
	jQuery( document ).on( 'trx_addons_action_page_template_selector_appear', function() {			
		jQuery( '.trx_addons_options .trx_addons_options_section' ).each( function () {
			trx_addons_options_check_dependencies( jQuery( this ) );
		});
	} );
} );

// Check for dependencies
function trx_addons_options_check_dependencies(cont) {
	if ( typeof TRX_ADDONS_DEPENDENCIES == 'undefined' ) {
		return;
	}
	cont.find('.trx_addons_options_item_field,.trx_addons_options_group[data-param]').each( function() {
		var ctrl = jQuery(this),
			id = ctrl.data('param');
		if (id == undefined) {
			return;
		}
		var depend = false;
		for (var fld in TRX_ADDONS_DEPENDENCIES) {
			if (fld == id) {
				depend = TRX_ADDONS_DEPENDENCIES[id];
				break;
			}
		}
		if (depend) {
			var dep_cnt    = 0, dep_all = 0;
			var dep_cmp    = typeof depend.compare != 'undefined' ? depend.compare.toLowerCase() : 'and';
			var dep_strict = typeof depend.strict != 'undefined';
			var fld        = null, val='', name='', subname='', i;
			var parts      = '', parts2 = '';
			for (i in depend) {
				if (i == 'compare' || i == 'strict') {
					continue;
				}
				dep_all++;
				name    = i;
				subname = '';
				if (name.indexOf('[') > 0) {
					parts   = name.split('[');
					name    = parts[0];
					subname = parts[1].replace(']', '');
				}
				if (name.charAt( 0 ) == '#' || name.charAt( 0 ) == '.') {
					fld = jQuery(name);
					if ( fld.length > 0 ) {
						var panel = fld.closest('.edit-post-sidebar');
						if ( panel.length === 0 ) {
							if ( ! fld.hasClass('trx_addons_inited') ) {
								fld.addClass('trx_addons_inited').on('change', function () {
									jQuery('.trx_addons_options .trx_addons_options_section').each( function () {
										trx_addons_options_check_dependencies(jQuery(this));
									} );
								} );
							}
						} else {
							if ( ! panel.hasClass('trx_addons_inited') ) {
								panel.addClass('trx_addons_inited').on('change', fld, function () {
									jQuery('.trx_addons_options .trx_addons_options_section').each( function () {
										trx_addons_options_check_dependencies(jQuery(this));
									} );
								} );
							}
						}
					}
				} else {
					fld = cont.find('[name="trx_addons_options_field_'+name+'"]');
				}
				if (fld.length > 0) {
					val = trx_addons_options_get_field_value(fld);
					if (subname !== '') {
						parts = val.split('|');
						for (var p=0; p < parts.length; p++) {
							parts2 = parts[p].split('=');
							if (parts2[0]==subname) {
								val = parts2[1];
							}
						}
					}
					if ( typeof depend[i] != 'object' && typeof depend[i] != 'array' ) {
						depend[i] = { '0': depend[i] };
					}
					for (var j in depend[i]) {
						if ( 
							   (depend[i][j]=='not_empty' && val !== '')	// Main field value is not empty - show current field
							|| (depend[i][j]=='is_empty' && val === '')		// Main field value is empty - show current field
							|| (val !== '' && (!isNaN(depend[i][j]) 		// Main field value equal to specified value - show current field
												? val==depend[i][j]
												: (dep_strict 
														? val==depend[i][j]
														: (''+val).indexOf(depend[i][j])==0
													)
											)
								)
							|| ( val !== '' && ( '' + depend[i][j]).charAt(0) == '^' && ( '' + val ).indexOf( depend[i][j].substr(1) ) == -1 )
																		// Main field value not equal to specified value - show current field
						) {
							dep_cnt++;
							break;
						}
					}
				} else {
					dep_all--;
				}
				if ( dep_cnt > 0 && dep_cmp == 'or' ) {
					break;
				}
			}
			if ( ! ctrl.hasClass('trx_addons_options_group') ) {
				ctrl = ctrl.parents('.trx_addons_options_item');
			}
			var section = ctrl.parents('.trx_addons_tabs_section'),
				tab = jQuery( '[aria-labelledby="' + section.attr('aria-labelledby') + '"]' );
			if ( ( ( dep_cnt > 0 || dep_all == 0 ) && dep_cmp == 'or' ) || ( dep_cnt == dep_all && dep_cmp == 'and' ) ) {
				ctrl.slideDown().removeClass('trx_addons_options_no_use');
				if ( section.find('>.trx_addons_options_item:not(.trx_addons_options_item_info),>.trx_addons_options_group[data-param]').length != section.find('.trx_addons_options_no_use').length ) {
					if ( tab.hasClass( 'trx_addons_options_item_hidden' ) ) {
						tab.removeClass('trx_addons_options_item_hidden');
					}
				}
			} else {
				ctrl.slideUp().addClass('trx_addons_options_no_use');
				if ( section.find('>.trx_addons_options_item:not(.trx_addons_options_item_info),>.trx_addons_options_group[data-param]').length == section.find('.trx_addons_options_no_use:not(.trx_addons_options_item_info)').length ) {
					if ( ! tab.hasClass( 'trx_addons_options_item_hidden' ) ) {
						tab.addClass('trx_addons_options_item_hidden');
						if ( tab.hasClass('ui-state-active') ) {
							tab.parents('.trx_addons_tabs').find(' > ul > li:not(.trx_addons_options_item_hidden)').eq(0).find('> a').trigger('click');
						}
					}
				}
			}
		}
	} );
}

// Return value of the field
function trx_addons_options_get_field_value(fld) {
	var ctrl = fld.parents('.trx_addons_options_item_field');
	var val = fld.attr('type')=='checkbox' || fld.attr('type')=='radio' 
				? (ctrl.find('[name^="trx_addons_options_field_"]:checked').length > 0
					? (ctrl.find('[name^="trx_addons_options_field_"]:checked').val() !== ''
						&& ''+ctrl.find('[name^="trx_addons_options_field_"]:checked').val() !== '0'
							? ctrl.find('[name^="trx_addons_options_field_"]:checked').val()
							: 1
						)
					: 0
					)
				: fld.val();
	if ( val === undefined || val === null ) {
		val = '';
	}
	return val;
}
