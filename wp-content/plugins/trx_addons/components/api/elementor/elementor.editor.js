/* global jQuery, elementor */

jQuery(document).ready(function() {
	"use strict";

	var $elementor_panel = jQuery('#elementor-panel');

	// Hide Elementor's responsive breakpoints
	if (TRX_ADDONS_STORAGE['add_hide_on_xxx'] == 'replace') {
		jQuery('body').addClass('replace_hide_on_xxx');
	}

	// Refresh taxonomies when post type is changed
	//-------------------------------------------------------------------------------
	var tax_lists = {},
		fields_state = false,
		pmv = false;

	// Refresh taxonomies and terms lists when post type is changed in Elementor editor
	$elementor_panel
		.on('change', 'select[data-setting*="post_type"],select[data-setting*="taxonomy"]', function (e) {
			var refresh_obj = jQuery(this),
				refresh_post_type = refresh_obj.data('setting').indexOf('post_type') === 0,
				refresh_taxonomy = refresh_obj.data('setting').indexOf('taxonomy') === 0,
				prev_post_type = '',
				cat_flds = [];

			if (refresh_post_type) {
				cat_flds = refresh_obj.parents('.elementor-control').nextAll().filter(function () {
					return jQuery('select[data-setting*="taxonomy"]', jQuery(this)).length > 0;
				});
				cat_flds = cat_flds.find('select');
			} else if (refresh_taxonomy) {
				prev_post_type = refresh_obj.parents('.elementor-control').prev().find('select').val();
				cat_flds = [refresh_obj.parents('.elementor-control').next().find('select')];
			}
			if (cat_flds.length > 0) {
				var num = 0;
				jQuery.each(cat_flds, function(index, cat_fld){
					if (cat_fld.length === 0) return;
					cat_fld = jQuery(cat_fld);
					var cat_wrapper = cat_fld.parents('.elementor-control-field').eq(0),
						cat_lbl = jQuery('label.elementor-control-title', cat_wrapper);
					// Restore fields values when panel is just opened
					if (fields_state !== false 
						&& refresh_taxonomy
						&& fields_state.post_type == prev_post_type
					   	&& fields_state.tax_val !== false
					) {
							refresh_obj.val(fields_state.tax_val);
							fields_state.tax_val = false;
							refresh_obj.trigger('change');
							return;
					}
					setTimeout(function() {
						trx_addons_refresh_list(
								cat_fld.data('setting').indexOf('taxonomy')>=0 ? 'taxonomies' : 'terms',
								refresh_obj.val(),
								cat_fld,
								cat_lbl
						);
					}, 300*num);
					num++;
				});
			}
			return false;
		});

	// Store taxonomies and terms to restore it when shortcode params open again
	$elementor_panel
		.on('change', 'select[data-setting*="cat"],select[data-setting*="category"]', function () {
			var tax_fld = jQuery(this).parents('.elementor-control').prev().find('select[data-setting*="taxonomy"]');
			if (tax_fld.length > 0) {
				var post_fld = tax_fld.parents('.elementor-control').prev().find('select[data-setting*="post_type"]');
				if (post_fld.length > 0) {
					// Restore fields values when panel is just opened
					if (fields_state !== false && fields_state.post_type == post_fld.val()) {
						jQuery(this).val(fields_state.terms_val);
						fields_state = false;
						jQuery(this).trigger('change');	// Refresh preview area
					} else {
						tax_lists[post_fld.data('element-cid')] = {
							'taxonomies': tax_fld.html(),	//.data('items'),
							'terms': jQuery(this).html()	//.data('items')
						};
					}
				}
			}
		})
		.on('click', '.elementor-panel-navigation-tab', function() {
			if (pmv !== false) {
				trx_addons_elementor_open_panel(pmv.panel, pmv.model, pmv.view, true);
			}
		})
		.on('change', 'input[data-setting="_animation_delay"]'
					+ ',select[data-setting="_animation"]'
					+ ',select[data-setting="animation_duration"]'
					+ ',select[data-setting="_animation_type"]',
			function( e ) {
				trx_addons_elementor_change_animation_type( jQuery(this) );
			}
		);

	// Move animations
	function trx_addons_elementor_change_animation_type( fld ) {
		var preview  = elementor.$preview[0].contentWindow;
		if ( typeof preview.trx_addons_elementor_prepare_animate_items != 'undefined' ) {
			setTimeout( function() {
				preview.trx_addons_elementor_prepare_animate_items( true );
			}, 10);
			
		}
	}
	
	// Add Elementor's hooks and elements
	if (window.elementor !== undefined && window.elementor.hooks !== undefined) {
		// Add hook on panel open to refresh taxonomies
		elementor.hooks.addAction( 'panel/open_editor/widget', trx_addons_elementor_open_panel);
		// Add hook on panel open to refresh 'layout editor' link
		elementor.hooks.addAction( 'panel/open_editor/widget', trx_addons_elementor_refresh_layout_selector_link);
		// Add hook on panel open to set data handler on the select2
		elementor.hooks.addAction( 'panel/open_editor/widget', trx_addons_elementor_add_data_to_select2);
	}

	// Store taxonomies and terms to restore it when shortcode params open again
	function trx_addons_elementor_open_panel( panel, model, view, tab_chg ) {
		if ( panel.content !== undefined ) {
			//Reset panel, model, view
			if ( ! tab_chg ) {
				pmv = false;
			}
			var post_fld = panel.content.$el.find( 'select[data-setting*="post_type"]' );
			var tax_fld = panel.content.$el.find( 'select[data-setting*="taxonomy"]' );
			var terms_fld = panel.content.$el.find( 'select[data-setting*="cat"],select[data-setting*="category"]' );
			// If this widget haven't fields 'post_type', 'taxonomy' or 'cat' - exit
			if ( post_fld.length === 0 || tax_fld.length === 0 || terms_fld.length === 0 ) {
				return;
			}
			// Save panel, model, view to use it when tabs are clicked
			if ( ! tab_chg ) {
				pmv = {'panel':panel, 'model': model, 'view':view};
			}
			// Add view.cid to the field 'post_type'
			var el_cid = view.cid;
			post_fld.attr('data-element-cid', el_cid);
			var post_type = post_fld.val();
			var tax_val = model.getSetting(tax_fld.data('setting'));
			var terms_val = model.getSetting(terms_fld.data('setting'));
			// If list of taxonomies is correct - exit
			if ( tax_fld.find('option[value="'+tax_val+'"]').length > 0 && terms_fld.find('option[value="'+terms_val+'"]').length > 0 ) {
				return;
			}
			// If we have stored list of items - use it
			if (tax_lists[el_cid] !== undefined) {
				tax_fld.html(tax_lists[el_cid].taxonomies).val(tax_val);
				terms_fld.html(tax_lists[el_cid].terms).val(terms_val);
			} else {
				fields_state = {'post_type': post_type, 'tax_val': tax_val, 'terms_val': terms_val};
				post_fld.trigger('change');
			}
		}
	}

	// Add data to the select2 options on the field IDS
	function trx_addons_elementor_add_data_to_select2( panel, model, view, tab_chg ) {
		if (panel.content !== undefined) {
			var ids_fld = panel.content.$el.find( 'select[data-setting="ids"][type="select2"],select[data-setting="post"][type="select2"]' );
			if ( ids_fld.length > 0 ) {
				ids_fld.each( function() {
					var fld = jQuery(this);
					// Add extra params to the AJAX query (if used)
					if ( typeof fld.data('select2').dataAdapter.ajaxOptions !== 'undefined' ) {
						fld.data('select2').dataAdapter.ajaxOptions.data = function( params ) {
							var controls = fld.parents('#elementor-controls'),
								terms = controls.find('select[data-setting="category"]').length > 0 ? controls.find('select[data-setting="category"]').val() : controls.find('select[data-setting="cat"]').val(),
								tax = controls.find('[data-setting="taxonomy"]').val(),
								pt  = controls.find('[data-setting="post_type"]').val();
							return trx_addons_object_merge( {
										nonce: TRX_ADDONS_STORAGE['ajax_nonce'],
										post_type: pt ? pt : '',
										taxonomy: tax ? tax : '',
										terms: terms ? ( typeof terms == 'array' || typeof terms == 'object' ? terms.join(',') : terms ) : ''
										}, params );
						};
					}
				} );
			}
		}
	}

	// Refresh 'Edit layout' link on panel creation
	function trx_addons_elementor_refresh_layout_selector_link( panel, model, view, tab_chg ) {
		if (panel.content !== undefined) {
			panel.content.$el.find( 'a.trx_addons_post_editor' ).each(function() {
				trx_addons_layout_selector_refresh_link(jQuery(this));
			});
		}
	}

	// Scroll active category to top of the panel
	//---------------------------------------------------------
	$elementor_panel
		.on('click', '.elementor-panel-category-title', function(e) {
			var cat = jQuery(this).closest('.elementor-panel-category').eq(0);
			setTimeout(function(){
				if (cat.hasClass('elementor-active')) {
					var height = jQuery('#elementor-panel-elements-navigation').outerHeight()
								+ jQuery('#elementor-panel-elements-search-area').outerHeight(),
						skip = false;
					jQuery('#elementor-panel-categories > .elementor-panel-category').each(function() {
						if (skip || cat.attr('id') == jQuery(this).attr('id')) {
							skip = true;
							return;
						}
						height += jQuery(this).outerHeight() + 2;
					});
					jQuery('#elementor-panel-content-wrapper').scrollTop(height);
				}
			}, 300);
		});



});


(function() {
	"use strict";

	// Add extra parameters to all links on panel in the Elementor Editor
	//---------------------------------------------------------
	jQuery( window ).on( 'elementor:init', function() {
		if ( TRX_ADDONS_STORAGE['add_to_links_url'] && window.elementor && window.elementor.hooks ) {
			var $elementor_panel = jQuery('#elementor-panel');
			if ( $elementor_panel.length ) {
				var trx_addons_add_extra_args_to_links_throttle = trx_addons_throttle( function( $cont ) {
					if ( $cont === undefined ) $cont = $elementor_panel;
					trx_addons_add_extra_args_to_links( TRX_ADDONS_STORAGE['add_to_links_url'], $cont );
				}, 500, true );
				function trx_addons_add_extra_args_to_links_hook( arg ) {
					trx_addons_add_extra_args_to_links_throttle();
					return arg;
				}
				// Filters on open different panels
				elementor.hooks.addFilter( 'panel/elements/regionViews', trx_addons_add_extra_args_to_links_hook );
				elementor.hooks.addFilter( 'editor/style/styleText', trx_addons_add_extra_args_to_links_hook );
				elementor.hooks.addFilter( 'controls/base/behaviors', trx_addons_add_extra_args_to_links_hook );
				// Button 'Site settings' - 'Back'
				$elementor_panel.on( 'mousedown touchstart click', '.elementor-header-button', function( e ) {
					trx_addons_add_extra_args_to_links_throttle();
				} );
				// Tab 'Global'
				$elementor_panel.on( 'mousedown touchstart click', '.elementor-panel-navigation-tab', function( e ) {
					trx_addons_add_extra_args_to_links_throttle();
				} );
			}
			// Add params after ajax complete
			if ( typeof TRX_ADDONS_STORAGE['add_to_links_url'] != 'undefined' ) {
				if ( typeof jQuery( document ).ajaxComplete != 'undefined' ) {
					jQuery( document ).ajaxComplete( function( event, xhr, settings ) {
						if ( typeof settings == 'object'
							&& typeof settings.url == 'string' && settings.url === TRX_ADDONS_STORAGE['ajax_url']
							&& typeof settings.data == 'string' && settings.data.indexOf( 'get_library_data' ) > 0
						) {
							// Add aff to links 'GO PRO'
							setTimeout( function() {
								var $library = jQuery( '#elementor-template-library-modal' );
								if ( $library.length ) {
									trx_addons_add_extra_args_to_links( TRX_ADDONS_STORAGE['add_to_links_url'], $library );
									if ( ! $library.hasClass( 'trx_addons_aff_inited' ) ) {
										$library
											.addClass( 'trx_addons_aff_inited' )
											.on( 'click touchstart mousedown', '.elementor-template-library-menu-item,'
														+ '.elementor-template-library-template-preview,'
														+ '#elementor-template-library-header-preview-back',
												function() {
													trx_addons_add_extra_args_to_links_throttle( $library );
												}
											);
									}
								}
							}, 3000 );
						}
					} );
				}
			}
		}
	} );


	// Return layout with social icons
	//--------------------------------------------------------------------
	window.trx_addons_get_settings_icon = function(icon) {
		return typeof icon == 'object'
						? ( typeof icon['icon'] != 'undefined'
							? icon['icon']
							: ''
							)
						: icon;
	};

	window.trx_addons_get_socials_links = function(icons, style, show) {
		var output = '',
			show_icons = show.indexOf('icons') >= 0,
			show_names = show.indexOf('names') >= 0;
		if (icons.length > 0 && typeof icons[0].name != 'undefined') {
			var sn='', fn='', title='', url='';
			for (var i=0; i<icons.length; i++) {
				sn = trx_addons_get_settings_icon( icons[i].name );
				fn = style=='icons' ? sn.replace('trx_addons_icon-', '').replace('icon-', '') : trx_addons_get_basename(sn);
				title = icons[i].title !== '' ? icons[i].title : trx_addons_proper(fn);
				url = icons[i].url;
				if (trx_addons_is_off(url)) continue;
				output += '<a target="_blank" href="' + url + '"'
								+ ' class="social_item social_item_style_' + style + ' sc_icon_type_' + style + ' social_item_type_' + show + '">'
							+ (show_icons
								? '<span class="social_icon social_icon_' + fn + '"'
									+ (style=='bg' ? ' style="background-image: url(' + sn + ');"' : '')
									+ '>'
										+ (style=='icons' 
											? '<span class="' + sn + '"></span>' 
											: (style == 'svg'
												? '<object type="image/svg+xml" data="' + sn + '" border="0"></object>'
												: (style=='images' 
													? '<img src="' + sn + '" alt="' + title + '" />' 
													: '<span class="social_hover" style="background-image: url(' + sn + ');"></span>'
													)
												)
										 	)
									+ '</span>'
								: '')
							+ (show_names
								? '<span class="social_name social_' + fn + '">' + title + '</span>'
								: '')
						+ '</a>';
			}
		}
		return output;
	};


	// Global colors processing
	//-------------------------------------------------------

	// Prepare global atts for the new Elementor version: add array keys by 'name' from __globals__
	// After the update Elementor 3.0+ (or later) for settings with type ::COLOR global selector appears
	// Color value from this selects is not placed to the appropriate settings
	window.trx_addons_elm_prepare_global_params = function( args, clear ) {
		for ( var k in args ) {
			if ( typeof args[k] == 'object' ) {
				if ( k == '__globals__' ) {
					for ( var k1 in args[k] ) {
						if ( args[k][k1] ) {
							args[k1] = trx_addons_apply_filters( 'trx_addons_filter_prepare_global_param', args[k][k1], k1, clear );
						}
					}
				} else {
					args[k] = trx_addons_elm_prepare_global_params( args[k], clear );
				}
			}
		}
		return args;
	};

	// Return CSS-var from global color key, i.e. 'globals/colors?id=1855627f'
	trx_addons_add_filter( 'trx_addons_filter_prepare_global_param', function( value, key, clear ) {
		var prefix = 'globals/colors?id=';
		if ( value.indexOf( prefix ) === 0 ) {
			if ( clear ) {
				value = '';
			} else {
				var id = value.replace( prefix, '' );
				value = 'var(--e-global-color-' + id + ')';
			}
		}
		return value;
	} );

	// Restore original values for atts with global settings for the new Elementor version: clear array keys by 'name' from __globals__
	// After the update Elementor 3.0+ (or later) for settings with type ::COLOR global selector appears
	// Color value from this selects is not placed to the appropriate settings
	window.trx_addons_elm_restore_global_params = function( args ) {
		return trx_addons_elm_prepare_global_params( args, true );
	};

})();