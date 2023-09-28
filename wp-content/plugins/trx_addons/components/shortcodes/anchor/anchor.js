/**
 * Shortcode Anchor
 *
 * @package ThemeREX Addons
 * @since v1.2
 */

/* global jQuery, TRX_ADDONS_STORAGE */


jQuery( document ).ready( function() {

	"use strict";

	var $window             = jQuery( window ),
		$document           = jQuery( document ),
		$body               = jQuery( 'body' );

	$document.on( 'action.init_hidden_elements', function(e, container) {

		// Build toc menu
		var toc_menu = jQuery('#toc_menu');
		if (toc_menu.length === 0) {
			trx_addons_build_page_toc();
			toc_menu = jQuery('#toc_menu');
			if (toc_menu.length === 0) return;
		} else if (toc_menu.hasClass('inited')) {
			return;
		}
		toc_menu.addClass('inited');

		var toc_menu_items = toc_menu.find('.toc_menu_item');

		// One page mode for menu links (scroll to anchor)
		// Old case: toc_menu.on('click', 'a', function(e) {
		// New case (allow add class 'toc_menu_item' in any menu to enable scroll):
		jQuery('.toc_menu_item > a').on('click', function(e) {
			var link_obj = jQuery(this),
				href = link_obj.attr('href'),
				pos = href.indexOf('#');
			if (pos >= 0 && href.length > 1 && trx_addons_is_local_link(href)) {
				e.preventDefault();
				$document.trigger( 'action.trx_addons_inner_links_click', [link_obj, e] );
				trx_addons_scroll_to_anchor(jQuery(this), true);
				return false;
			}
		});

		// Mouse wheel handler
		var wheel_busy = false,
			wheel_time = 0,
			wheel_stop = false;

		if ( TRX_ADDONS_STORAGE['scroll_to_anchor'] == 1 && !TRX_ADDONS_STORAGE['scroll_to_anchor_wheel'] ) {
			TRX_ADDONS_STORAGE['scroll_to_anchor_wheel'] = true;
			$document.on('action.stop_wheel_handlers', function(e) {
				wheel_stop = true;
			});
			$document.on('action.start_wheel_handlers', function(e) {
				wheel_stop = false;
			});
			
			// To smoothing scroll in the Chrome 56+ use native event with parameter 'passive=false'
			// to enable calls e.preventDefault inside the event's handler
			window.addEventListener('mousewheel', trx_addons_mouse_wheel, { passive: false } );				// WebKit: native event
			//$window.on('mousewheel', trx_addons_mouse_wheel);										// WebKit: jquery wrapper

			// Mozilla Firefox already have a smooth scroll - use jQuery
			//window.addEventListener('DOMMouseScroll', trx_addons_mouse_wheel, { passive: false } );		// Mozilla Firefox: native event
			$window.on('DOMMouseScroll', trx_addons_mouse_wheel);									// Mozilla Firefox: jquery wrapper
		}

		function trx_addons_mouse_wheel(e) {
			if (screen.width < 960 || trx_addons_window_width() < 960 || wheel_stop || trx_addons_browser_is_ios()) {
				return true;
			}
			if (wheel_busy || wheel_time == e.timeStamp) {
				e.preventDefault();
				return false;
			}
			wheel_time = e.timeStamp;
			var wheel_dir = e.originalEvent
								? e.originalEvent.wheelDelta > 0 || e.originalEvent.detail < 0 ? -1 : 1
								: e.wheelDelta > 0 || e.detail < 0 ? -1 : 1;

			toc_menu_items = jQuery('#toc_menu .toc_menu_item');
			var items = trx_addons_detect_active_toc(true);
			if ( items ) {
				var doit = false;
				var scroll_offset = parseInt(trx_addons_window_scroll_top(), 10);
				var wh = trx_addons_window_height();
				var ah = trx_addons_fixed_rows_height();
				if (wheel_dir == -1) {			// scroll up
					doit = true;
					setTimeout( function() {
						if (items.prev >= 0 && items.prevOffset >= scroll_offset-wh-ah) {
							trx_addons_scroll_to_anchor( toc_menu_items.eq(items.prev).find('a'), false );
						} else {
							trx_addons_document_animate_to( Math.max(0, scroll_offset-wh) );
						}
					}, 10 );
				} else {						// scroll down
					doit = true;
					setTimeout( function() {
						if (items.next >= 0 && items.nextOffset <= scroll_offset+wh+ah) {
							trx_addons_scroll_to_anchor( toc_menu_items.eq(items.next).find('a'), false );
						} else {
							trx_addons_document_animate_to( Math.min( trx_addons_document_height(), scroll_offset+wh ) );
						}
					}, 10 );
				}
				// Set busy flag while animating
				if (doit) {
					wheel_busy = true;
					setTimeout( function() { wheel_busy = false; }, trx_addons_browser_is_ios() ? 1200 : 100 );
					e.preventDefault();
					return false;
				}
			}
		}

		// Detect active TOC item
		var last_scroll_offset = -1;

		function trx_addons_detect_active_toc( force ) {
			var scroll_offset = parseInt(trx_addons_window_scroll_top(), 10);
			if ( !force && scroll_offset == last_scroll_offset ) {
				return false;
			} else {
				last_scroll_offset = scroll_offset;
			}
			var items = {
				loc: '',
				current: [],
				prev: -1,
				prevOffset: -1,
				next: -1,
				nextOffset: -1
			};

			toc_menu_items.each(function(idx) {
				var id = '#'+jQuery(this).data('id');
				var pos = id.indexOf('#');
				if (pos < 0 || id.length == 1) return;
				var href = jQuery(this).find('a').attr('href');
				if (!trx_addons_is_local_link(href) || jQuery(id).length==0) return;
				var off = jQuery(id).offset().top;
				var id_next  = jQuery(this).next().find('a').attr('href');
				var off_next = id_next && idx < toc_menu_items.length-1 && jQuery(id_next).length > 0 
										? parseInt(jQuery(id_next).offset().top, 10) 
										: 1000000;
				if (off > scroll_offset + trx_addons_fixed_rows_height() + 20) {
					if (items.next < 0) {
						items.next = idx;
						items.nextOffset = off;
					}
				} else if (off < scroll_offset - trx_addons_fixed_rows_height() - 20) {
					items.prev = idx;
					items.prevOffset = off;
				}
				if (off < scroll_offset + trx_addons_window_height()*0.8 && scroll_offset < off_next - trx_addons_fixed_rows_height()) {
					items.current.push(idx);
					if (items.loc == '') {
						var loc = window.location.href;
						var loc_pos = loc.indexOf('#');
						if (loc_pos > 0) loc = loc.substring(0, loc_pos);
						items.loc = href.indexOf('#')==0 ? loc + id : id;
					}
				}
			});
			return items;
		}

		// Change active element then page is scrolled
		$document.on( 'action.scroll_trx_addons', function() {
			// Mark current item
			trx_addons_mark_active_toc();
		});
		setTimeout( function() {
			trx_addons_mark_active_toc();
		}, 500);
		trx_addons_mark_active_toc();

		// Mark active TOC item
		function trx_addons_mark_active_toc() {
			var items = trx_addons_detect_active_toc();
			if ( items ) {
				toc_menu_items.removeClass('toc_menu_item_active');
				for (var i=0; i<items.current.length; i++) {
					toc_menu_items.eq(items.current[i]).addClass('toc_menu_item_active');
					// Comment next line if on your device page jump when scrolling
					if (items.loc !== '' && TRX_ADDONS_STORAGE['update_location_from_anchor']==1 && !trx_addons_browser_is_mobile() && !trx_addons_browser_is_ios() && !wheel_busy) {
						trx_addons_document_set_location(items.loc);
					}
				}
				$document.trigger('action.toc_menu_item_active', [items.current]);
			}
		}

		// Scroll to the anchor
		function trx_addons_scroll_to_anchor(link_obj, click_event) {
			var href = click_event ? link_obj.attr('href') : '#'+link_obj.parent().data('id');
			var pos = href.indexOf('#');
			if (pos >= 0 && href.length > 1 && trx_addons_is_local_link(href)) {
				wheel_busy = true;
				setTimeout(function() { wheel_busy = false; }, trx_addons_browser_is_ios() ? 1200 : 100);
				trx_addons_document_animate_to(href.substr(pos), function() {
					if (TRX_ADDONS_STORAGE['update_location_from_anchor']==1) {
						var loc = window.location.href;
						var loc_pos = loc.indexOf('#');
						if (loc_pos > 0) loc = loc.substring(0, loc_pos);
						trx_addons_document_set_location(pos==0 ? loc + href : href); 
					}
				});
				return true;
			}
			return false;
		}


		// Build page TOC from the tag's id
		function trx_addons_build_page_toc() {
		
			var toc = '', toc_count = 0;
		
			jQuery( '[id^="toc_menu_"],.sc_anchor').each(function(idx) {
				var obj = jQuery(this);
				var obj_id = obj.attr('id') || ('sc_anchor_'+Math.random()).replace('.', '');
				var row = obj.closest('.wpb_row,.elementor-row,.elementor-section').eq(0);	// .elementor-row is deprecated from Elementor 3.0+
				if (row.length == 0) row = !obj.parent().hasClass('entry-content') ? obj.parent() : obj;
				var row_id = row.length>0 && row.attr('id') !== undefined && row.attr('id') !== '' ? row.attr('id') : '';
				var id = row_id || obj_id.substr(10);
				if (row.length>0 && row_id == '') {
					row.attr('id', id);
				}
				var url = obj.data('url');
				var icon = obj.data('vc-icon') || 'toc_menu_icon_default';
				var title = obj.attr('title');
				var description = obj.data('description');
				var separator = obj.data('separator');
				toc_count++;
				toc += '<div class="toc_menu_item' + (separator == 'yes' ? ' toc_menu_separator' : '') + '"'
							+ ' data-id="' + id + '"'
						+ '>'
							+ (title || description 
								? '<a href="' + (url ? url : '#'+id) + '" class="toc_menu_description">'
										+ (title ? '<span class="toc_menu_description_title">' + title + '</span>' : '')
										+ (description ? '<span class="toc_menu_description_text">' + description + '</span>' : '')
									+ '</a>' 
								: '')
							+ '<a href="' + (url ? url : '#'+id) + '" class="toc_menu_icon '+icon+'"></a>'
					+ '</div>';
			} );
		
			if (toc_count > 0) {
				$body.append('<div id="toc_menu" class="toc_menu"><div class="toc_menu_inner">'+toc+'</div></div>');
				$document.trigger('action.build_page_toc');
			}
		}

	} );

} );