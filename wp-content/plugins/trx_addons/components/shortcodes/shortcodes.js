/**
 * Shortcodes common scripts
 *
 * @package ThemeREX Addons
 * @since v1.2
 */

/* global jQuery, TRX_ADDONS_STORAGE */


jQuery( document ).ready(function() {

	"use strict";

	var $window              = jQuery( window ),
		$document            = jQuery( document ),
		$body                = jQuery( 'body' );
	
	var $equal_height,
		$pagination_infinite,
		$infinite_posts,
		$typed_entry;

	// Update links and values after the new post added
	$document.on( 'action.init_hidden_elements', update_jquery_links );
	$document.on( 'action.got_ajax_response', update_jquery_links );
	var first_run = true;
	function update_jquery_links(e) {
		if ( first_run && e && e.namespace == 'init_hidden_elements' ) {
			first_run = false;
			return; 
		}
		$equal_height        = jQuery( '[data-equal-height],.trx_addons_equal_height' );
		$pagination_infinite = jQuery( '.sc_item_pagination_infinite' );
		$infinite_posts      = $pagination_infinite.siblings('.sc_item_posts_container');
		$typed_entry         = jQuery('.sc_typed_entry');
	}
	update_jquery_links();

	if ( typeof TRX_ADDONS_STORAGE != 'undefined' ) {
		TRX_ADDONS_STORAGE['pagination_busy'] = false;
	}

	// Init elements
	//------------------------------------------
	$document.on( 'action.init_hidden_elements', function() {

		// Trigger click on tab hover
		var last_link = false,
			last_link_timer = null;
		jQuery('.sc_item_filters_tabs_open_on_hover:not(.inited)')
			.addClass('inited')
			.on('mouseenter', 'a', function(e) {
				last_link = jQuery(this);
				if ( last_link_timer !== null ) {
					clearTimeout( last_link_timer );
				}
				last_link_timer = setTimeout( function() {
					last_link.trigger( 'click' );
					last_link_timer = null;
				}, 300 );
			} );

		// Save/Restore sc_layouts_popup inner html on tabs switched
		var popup_html = {}; 
		function trx_addons_save_popup_html( $obj, restore ) {
			$obj.each( function( idx ) {
				var id = $obj.eq( idx ).attr( 'id' );
				if ( ! id ) {
					$id = 'sc_popup_' + ('' + Math.random()).replace('.', '');
					$obj.eq( idx ).attr( 'id', id );
				}
				if ( ! popup_html.hasOwnProperty( id ) ) {
					popup_html[ id ] = $obj.eq( idx ).html();
				} else if ( restore ) {
					$obj.eq( idx ).html( popup_html[ id ] );
				}
			} );
		}

		// Load next page by AJAX		
		jQuery('.sc_item_filters:not(.inited),.sc_item_pagination:not(.inited)')
			.addClass('inited')
			.each( function() {
				var $self = jQuery( this );
				if ( $self.hasClass( 'sc_item_filters' ) ) {
					trx_addons_save_popup_html( $self.parent().find('.sc_layouts_popup') );
				}
			} )
			.on('click', 'a', function(e) {
				var link = jQuery(this);
				if ( ! link.hasClass( 'active' ) && ! TRX_ADDONS_STORAGE['pagination_busy'] ) {
					var link_wrap = link.parents('.sc_item_filters,.sc_item_pagination'),
						load_more = link_wrap.hasClass('sc_item_pagination_load_more'),
						sc = link_wrap.parent(),
						set_min_height = trx_addons_apply_filters( 'trx_addons_filter_set_min_height_on_switch_tabs', true, sc ),
						posts = sc.find('.sc_item_posts_container,.sc_item_slider'),
						pagination_wrap = sc.find('.sc_item_pagination');

					// Save posts to the tab's link
					if ( link_wrap.hasClass('sc_item_filters') ) {
						var link_active = link_wrap.find('a.active');
						if ( ! link_active.data( 'posts' ) ) {
							link_active.data( 'posts', posts.html() );
						}
						link_active.data( 'pagination', pagination_wrap.length > 0 ? pagination_wrap.clone() : false );
					}

					// Show saved posts
					if ( link_wrap.hasClass('sc_item_filters') && link.data('posts') !== undefined ) {
						if ( set_min_height ) {
							sc.data( 'min-height', sc.css( 'min-height' ) )
								.css( 'min-height', sc.height() );
						}
						link_active.removeClass( 'active' ).parent().removeClass( 'sc_item_filters_tabs_active' );
						link.addClass('active').parent().addClass( 'sc_item_filters_tabs_active' );
						if ( pagination_wrap.length > 0 ) {
							pagination_wrap.fadeOut();
						}
						posts.animate( { opacity: 0 }, 200, function() {
							$document.trigger( 'action.before_remove_content', [sc] );
							trx_addons_replace_posts( posts, link.data('posts') );
							trx_addons_replace_pagination( pagination_wrap, link.data('pagination'), posts );
							$document.trigger( 'action.after_add_content', [sc] );
							posts.find('.inited').removeClass('inited');
							posts.find('.popup_inited').removeClass('popup_inited');
							posts.find('.swiper-container-initialized').removeClass('swiper-container-initialized');
							pagination_wrap.find('.inited').removeClass('inited');
							$document.trigger( 'action.init_hidden_elements', [sc] );
							$window.trigger( 'resize' );
							setTimeout( function() {
								posts.animate( { opacity: 1 }, 200, function() {
									if ( sc.data('min-height') && set_min_height ) {
										sc.css( 'min-height', sc.data('min-height') );
									}
								} );
							}, 400 );
						} );

					// First time load posts to the tab
					} else {
						if ( load_more ) {
							var page     = Number( link.data( 'page' ) );
							var max_page = Number( link.data( 'max-page' ) );
							if (page > max_page) {
								pagination_wrap.addClass( 'all_items_loaded' ).hide();
								return false;
							}
							link_wrap.addClass('loading');
						} else {
							posts.append('<div class="trx_addons_loading"></div>');
						}
						TRX_ADDONS_STORAGE['pagination_busy'] = true;
						jQuery.post(TRX_ADDONS_STORAGE['ajax_url'], {
							action: 'trx_addons_item_pagination',
							nonce: TRX_ADDONS_STORAGE['ajax_nonce'],
							params: pagination_wrap.length > 0 ? pagination_wrap.data('params') : link_wrap.data('params'),
							page: link.parents('.sc_item_filters').length > 0 ? 1 : link.data('page'),
							filters_active: link.parents('.sc_item_filters').length > 0 
												? link.data('tab') 
												: (link_wrap.siblings('.sc_item_filters').find('a.active').length > 0
													? link_wrap.siblings('.sc_item_filters').find('a.active').data('tab')
													: 'all'
													)
						}).done(function(response) {
							var rez = {};
							if (response==='' || response===0) {
								rez = { error: TRX_ADDONS_STORAGE['msg_ajax_error'] };
							} else {
								try {
									rez = JSON.parse(response);
								} catch (e) {
									rez = { error: TRX_ADDONS_STORAGE['msg_ajax_error'] };
									console.log(response);
								}
							}
							if (rez.error === '') {
								// Add inline styles
								if (rez.css !== '') {
									var	selector = 'trx_addons-inline-styles-inline-css',
										inline_css = jQuery('#'+selector);
									if (inline_css.length === 0)
										jQuery('body').append('<style id="'+selector+'" type="text/css">' + rez.css + '</style>');
									else
										inline_css.append(rez.css);
								}
								// Append posts
								if (load_more) {
									// Remove 'single_row' class
									posts.removeClass( 'columns_in_single_row' );
									// Append posts to the tabs container
									if (posts.find('[class*="_tabs_list_item"]').length > 0) {
										posts.find('[class*="_tabs_list_item"]').parent().append(jQuery(rez.data).find('.sc_item_posts_container [class*="_tabs_list_item"]').parent().html());
										posts.find('[class$="_tabs_content"]').append(jQuery(rez.data).find('.sc_item_posts_container [class$="_tabs_content"]').html());
										// Remove active classes in appended items
										posts.find('[class*="_tabs_list_item_active"]').each(function(idx) {
											if (idx > 0) {
												var classes = jQuery(this).attr('class').split(' '),
													found = false;
												for (var i=0; i<classes.length; i++) {
													if (classes[i].indexOf('_tabs_list_item_active') > 0) {
														classes[i] = '';
														found = true;
														break;
													}
												}
												if (found) jQuery(this).attr('class', classes.join(' '));
											}
										});
										posts.find('[class$="_tabs_content"] [class*="_item_active"]').each(function(idx) {
											if (idx > 0) {
												var classes = jQuery(this).attr('class').split(' '),
													found = false;
												for (var i=0; i<classes.length; i++) {
													if (classes[i].indexOf('_item_active') > 0) {
														classes[i] = '';
														found = true;
														break;
													}
												}
												if (found) jQuery(this).attr('class', classes.join(' '));
											}
										});

									// Append regular posts
									} else {
										if (posts.hasClass('masonry_wrap')) {
											var items = jQuery(rez.data).find('.sc_item_posts_container .masonry_item');
											if (items.length > 0) {
												items.addClass( 'just_loaded_items hidden' );
												posts.append( items );
												var just_loaded_items = posts.find( '.just_loaded_items' );
												trx_addons_when_images_loaded(
													just_loaded_items, function() {
														just_loaded_items.removeClass( 'hidden' );
														posts.masonry( 'appended', items ).masonry();
													}
												);
												setTimeout(function() {
													just_loaded_items.removeClass( 'just_loaded_items hidden' );
												}, 1000);
											}
										} else {
											posts.append(jQuery(rez.data).find('.sc_item_posts_container').html());
										}
									}
									// Save popup containers
									trx_addons_save_popup_html( posts.find('.sc_layouts_popup') );
									// Trigger actions to init added items
									$document.trigger( 'action.after_add_content', [posts] );
									$document.trigger( 'action.init_hidden_elements', [posts] );
									$window.trigger( 'resize' );
									// Update current page in the pagination link
									link.data('page', Number(link.data('page')) + 1);
									if (link.data('page') > link.data('max-page')) {
										pagination_wrap.addClass( 'all_items_loaded' ).fadeOut();
									}
									// Replace shortcode params in the pagination wrapper
									if ( pagination_wrap.length > 0 ) {
										var new_params = jQuery(rez.data).find('.sc_item_pagination').data('params');
										if ( new_params ) {
											pagination_wrap.data( 'params', new_params );
										}
									}

								// Replace posts
								} else {
									if ( set_min_height ) {
										sc.data( 'min-height', sc.css( 'min-height' ) )
											.css( 'min-height', sc.height() );
									}
									if ( link_wrap.hasClass('sc_item_filters') ) {
										link_active.removeClass( 'active' ).parent().removeClass( 'sc_item_filters_tabs_active' );
										link.addClass('active').parent().addClass( 'sc_item_filters_tabs_active' );
									} else {
										if ( pagination_wrap.length > 0 ) {
											pagination_wrap.fadeOut();
										}
									}
									posts.animate( { opacity: 0 }, 200, function() {
										var items = jQuery(rez.data).find('.sc_item_posts_container,.sc_item_slider');
										// Add trx_addons_columns_wrap if need
										if ( items.find('[class*="trx_addons_column-"]').length > 0 ) {
											if ( ! posts.hasClass( 'trx_addons_columns_wrap' ) ) {
												posts.addClass( 'trx_addons_columns_wrap' );
											}
										} else {
											posts.removeClass( 'trx_addons_columns_wrap' );
										}
										// Add columns_wrap if need
										if ( items.find('[class*="column-"]').length > 0 ) {
											if ( ! posts.hasClass( 'columns_wrap' ) ) {
												posts.addClass( 'columns_wrap' );
											}
										} else {
											posts.removeClass( 'columns_wrap' );
										}
										$document.trigger('action.before_remove_content', [sc]);
										trx_addons_replace_posts( posts, items.html() );
										trx_addons_replace_pagination( pagination_wrap, jQuery(rez.data).find('.sc_item_pagination'), posts );
										$document.trigger( 'action.after_add_content', [sc] );
										$document.trigger( 'action.init_hidden_elements', [sc] );
										$window.trigger('resize');
										setTimeout( function() {
											posts.animate( { opacity: 1 }, 200, function() {
												if ( sc.data('min-height') && set_min_height ) {
													sc.css( 'min-height', sc.data('min-height') );
													$window.trigger( 'resize' );
													$window.trigger( 'scroll' );
												}
											} );
										}, 400 );
									} );
								}
								posts.find('.trx_addons_loading').fadeOut( function() {
									jQuery( this ).remove();
								} );

							} else {
								alert(rez.error);
								posts.find('.trx_addons_loading').remove();
							}

							if (load_more) pagination_wrap.removeClass('loading');

							TRX_ADDONS_STORAGE['pagination_busy'] = false;

							$document.trigger( 'action.got_ajax_response', {
								action: 'trx_addons_item_pagination',
								result: rez
							});
						});
					}
				}
				e.preventDefault();
				return false;
			});
	
		function trx_addons_replace_posts( posts, posts_new ) {
			if ( posts.hasClass('masonry_wrap') ) {
				var items = posts.find('.masonry_item');
				posts.masonry( 'remove', items );
				posts.html( posts_new );
				posts.find( '.inited' ).removeClass( 'inited' );
				posts.find( '.popup_inited' ).removeClass( 'popup_inited' );
				posts.find( '.swiper-container-initialized').removeClass('swiper-container-initialized' );
				items = posts.find('.masonry_item');
				if (items.length > 0) {
					posts.masonry( 'appended', items ).masonry();
				}
			} else {
				posts.html( posts_new );
				if ( posts.find('>.slider_container').length > 0 ) {
					posts.removeClass( 'trx_addons_columns_wrap columns_wrap' );
				} else if ( posts.find('>[class*="trx_addons_column-"]').length > 0 ) {
					posts.toggleClass( 'trx_addons_columns_wrap', true );
				} else if ( posts.find('>[class*="column-"]').length > 0 ) {
					posts.toggleClass( 'columns_wrap', true );
				}
			}
			// Save/restore popup containers
			trx_addons_save_popup_html( posts.find('.sc_layouts_popup'), true );
		}

		function trx_addons_replace_pagination( pagination_wrap, pagination_new, posts ) {
			if ( pagination_new.length > 0 ) {
				pagination_new.addClass( 'trx_addons_invisible' );
				if ( pagination_wrap.length > 0 ) {
					pagination_wrap
						.after( pagination_new )
						.next().removeClass('inited')
						.end().remove();
				} else {
					posts
						.after( pagination_new )
						.next().removeClass('inited');
				}
				setTimeout( function() {
					pagination_new.fadeIn().removeClass('trx_addons_invisible');
				}, 400 );
			} else {
				if ( pagination_wrap.length > 0 ) {
					pagination_wrap.remove();
				}
			}
		}


		// Load post's details by AJAX and show in the popup
		jQuery('.sc_post_details_popup:not(.inited)')
			.addClass('inited')
			.on('click', 'a', function(e) {
				trx_addons_show_post_details(jQuery(this).parents('[data-post_id]'), true);
				e.preventDefault();
				return false;
			});
		if (jQuery('.sc_post_details_popup.inited').length > 0) {
			jQuery('body:not(.sc_post_details_popup_inited)')
				.addClass('sc_post_details_popup_inited')
				.on('click', '#trx_addons_post_details_popup_overlay, .trx_addons_post_details_popup_close', function(e) {
					jQuery('#trx_addons_post_details_popup').fadeOut();
					jQuery('#trx_addons_post_details_popup_overlay').fadeOut();
				})
				.on('click', '.trx_addons_post_details_popup_prev,.trx_addons_post_details_popup_next', function(e) {
					var popup = jQuery('#trx_addons_post_details_popup');
					var post_item = popup.data('post_item');
					if (!post_item || post_item.length === 0) return;
					var posts_items = post_item.parents('.sc_item_columns,.sc_item_slider').find('[data-post_id]');
					var cur_idx = -1;
					posts_items.each(function(idx) {
						if (jQuery(this).data('post_id') == post_item.data('post_id')) cur_idx = idx;
					});
					if (cur_idx == -1) return;
					post_item = jQuery(this).hasClass('trx_addons_post_details_popup_prev') 
									? (cur_idx > 0 ? posts_items.eq(cur_idx-1) : false)
									: (cur_idx < posts_items.length-1 ? posts_items.eq(cur_idx+1) : false);
					if (!post_item || post_item.length === 0) return;
					popup.fadeOut();
					trx_addons_show_post_details(post_item, false);
				});
		}
		
		function trx_addons_show_post_details(post_item, show_overlay) {
			jQuery.post(TRX_ADDONS_STORAGE['ajax_url'], {
				action: 'trx_addons_post_details_in_popup',
				nonce: TRX_ADDONS_STORAGE['ajax_nonce'],
				post_id: post_item.data('post_id'),
				post_type: post_item.data('post_type')
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
				var msg = rez.error === '' ? rez.data : rez.error;
				var popup = jQuery('#trx_addons_post_details_popup');
				var overlay = jQuery('#trx_addons_post_details_popup_overlay');
				if ( popup.length === 0 ) {
					jQuery('body').append(
						'<div id="trx_addons_post_details_popup_overlay"></div>'
						+ '<div id="trx_addons_post_details_popup">'
							+ '<div class="trx_addons_post_details_content"></div>'
							+ '<span class="trx_addons_post_details_popup_close trx_addons_icon-cancel"></span>'
							+ '<span class="trx_addons_post_details_popup_prev trx_addons_icon-left"></span>'
							+ '<span class="trx_addons_post_details_popup_next trx_addons_icon-right"></span>'
						+ '</div>');
					popup = jQuery('#trx_addons_post_details_popup');
					overlay = jQuery('#trx_addons_post_details_popup_overlay');
				}
				popup.data('post_item', post_item).find('.trx_addons_post_details_content').html(msg);
				// Load styles and scripts for the popup content
				var handle, $head = jQuery( 'head' );
				if ( rez.error === '' ) {
					// Load styles (add to the head)
					if ( rez['css'] ) {
						for ( handle in rez['css'] ) {
							if ( jQuery( '#' + handle + '-css' ).length === 0 ) {
								if ( rez['css'][handle].hasOwnProperty( 'url' ) ) {
									$head.append( '<link id="' + handle + '-css"'
													+ ' type="text/css"'
													+ ' property="stylesheet"'
													+ ' rel="stylesheet"'
													+ ' href="' + rez['css'][handle]['url'] + '"'
													+ ' media="all">'
												);
								} else if ( rez['css'][handle].hasOwnProperty( 'code' ) ) {
									$head.append( '<style id="' + handle + '-css" media="all">'
													+ rez['css'][handle]['code']
													+ '</style>'
												);

								}
							}
						}
					}
					// Load scripts (add to the footer)
					if ( rez['js'] ) {
						for ( handle in rez['js'] ) {
							if ( jQuery( '#' + handle + '-js' ).length === 0 ) {
								if ( rez['js'][handle].hasOwnProperty( 'url' ) ) {
									$body.append( '<script id="' + handle + '-js"'
													+ ' type="text/javascript"'
													+ ' src="' + rez['js'][handle]['url'] + '"'
													+ '></script>'
												);
								} else if ( rez['js'][handle].hasOwnProperty( 'code' ) ) {
									$body.append( '<script id="' + handle + '-js" type="text/javascript">'
													+ rez['js'][handle]['code']
													+ '</script>'
												);
								}
							}
						}
					}
				}
				// Show the popup
				if (show_overlay) overlay.fadeIn();
				popup.fadeIn( function() {
					if ( Event ) {
						document.dispatchEvent( new Event( 'DOMContentLoaded' ) );
					}
					$document.trigger( 'action.init_hidden_elements', [popup] );
				} );
				$document.trigger( 'action.got_ajax_response', {
					action: 'trx_addons_post_details_in_popup',
					result: rez
				});
			});
		}

		// Featured image as panel
		jQuery('.sc_blogger_panel .sc_blogger_item:not(.switch_panel_inited),.sc_services_panel .sc_services_item:not(.switch_panel_inited)')
			.addClass('switch_panel_inited')
			.on('mouseenter', function() {
				var $self = jQuery(this),
					num = $self.data('item-number'),
					$posts = $self.parents('.sc_item_posts_container'),
					$old_panel = $posts.find('.sc_panel_thumb_active').removeClass('sc_panel_thumb_active'),
					$new_panel = $posts.find('.sc_panel_thumb[data-thumb-number="' + num + '"]').addClass('sc_panel_thumb_active');
				$document.trigger( 'action.init_hidden_elements', [$new_panel] );
			});


		// Cover links
		jQuery('.sc_cover:not(.inited)').each( function() {
			var $self = jQuery(this).addClass('inited');
			if ( $self.parents('.elementor-editor-active').length ) {
				return;
			}
			var $wrap = $self.parent().hasClass('elementor-widget-container') ? $self.parents('.elementor-widget').eq(0) : $self,
				wrap_z = $wrap.hasClass('elementor-widget') && $wrap.css('z-index') >0 ? $wrap.css('z-index') : '',
				place = $self.data('place'),
				$placeholder = false;
			if ( wrap_z > 0 ) {
				$self.css( 'z-index', wrap_z );
			}
			if ( place == 'p1' ) {
				if ( ! $wrap.hasClass('sc_cover') ) {
					$placeholder = $wrap.parent();
				}
			} else if ( place == 'p2' ) {
				$placeholder = $wrap.parent().parent();
			} else if ( place == 'p3' ) {
				$placeholder = $wrap.parent().parent().parent();
			} else if ( place == 'row' ) {
				$placeholder = $wrap.parents( trx_addons_apply_filters( 'trx_addons_filter_section_selectors', '.wp-block-columns,.elementor-section' ) ).eq(0);
			} else if ( place == 'column' ) {
				$placeholder = $wrap.parents( trx_addons_apply_filters( 'trx_addons_filter_column_selectors', '.wp-block-column,.elementor-column' ) ).eq(0);
			}
			if ( $placeholder && $placeholder.length ) {
				if ( $placeholder.css('position') == 'static' ) {
					$placeholder.addClass('sc_cover_link_wrap');
				}
				$self.prependTo( $placeholder.addClass('sc_cover_link_present') );
			} else {
				$placeholder = $wrap.parent().addClass('sc_cover_link_present');
				if ( $placeholder.css('position') == 'static' ) {
					$placeholder.addClass('sc_cover_link_wrap');
				}
			}
		} );

	});

	// Infinite scroll in the shortcodes
	$document.on( 'action.resize_trx_addons', function() {
		if ( $infinite_posts.length > 0 ) {
			$infinite_posts.each( function(idx) {
				var $self = $infinite_posts.eq(idx);
				$self.data( {
					'offset-top': $self.offset().top,
					'height': $self.height()
				} );
			});
		}
	} );
	$document.on( 'action.scroll_trx_addons', function(e) {
		if ( TRX_ADDONS_STORAGE['pagebuilder_preview_mode'] || $pagination_infinite.length === 0 ) {
			return;
		}
		var done = false;
		$pagination_infinite.each( function(idx) {
			if ( done ) return;
			var $self = $pagination_infinite.eq(idx);
			if ( $self.hasClass('all_items_loaded') ) return;
			var posts = $infinite_posts.eq(idx);
			if ( posts.data('offset-top') + posts.data('height') < trx_addons_window_scroll_top() + trx_addons_window_height() * 1.5) {
				$self.find( 'a' ).trigger( 'click' );
				done = true;
			}
		} );
	});


	// Typed feature for titles
	$document.on('action.scroll_trx_addons', function() {
		if ( $typed_entry.length === 0 ) {
			return;
		}
		var wt = trx_addons_window_scroll_top(),
			wh = trx_addons_window_height();

		$typed_entry.each(function(idx) {
			var obj = $typed_entry.eq(idx);
			if ( obj.hasClass('sc_typed_inited') ) return;

			var ot = obj.offset().top,
				oh = obj.height();
			if ( wt <= ot + oh && wt + wh >= ot + oh ) {
				obj.addClass('sc_typed_inited').typed({
					contentType: "html",
					strings: obj.data('strings'),
					loop: obj.data('loop') == 1,
					showCursor: obj.data('cursor') == 1,
					cursorChar: obj.data('cursor-char') != undefined ? obj.data('cursor-char') : '|',
					typeSpeed: obj.data('speed') > 0 ? (11 - Math.max(1, Math.min(10, obj.data('speed')))) * 10 : 50,
					backDelay: obj.data('delay') > 0 ? Math.max(0, Math.min(10, obj.data('delay'))) * 1000 : 1000
				});
			}
		});
	});

	// Equal height elements
	$document.on('action.resize_trx_addons', function (e, container) {
		if ( $equal_height.length === 0 ) {
			return;
		}
		$equal_height.each( function () {
			var eh_wrap = jQuery(this);
			var eh_items_selector = eh_wrap.data('equal-height');
			if (eh_items_selector === undefined) {
				eh_items_selector = '>*';
			}
			var max_h = 0;
			var items = [];
			var row_y = 0;
			var i = 0;

			eh_wrap.find(eh_items_selector).each(function() {
				var el = jQuery(this);
				el.css('visibility', 'hidden').height('auto');
				var el_height = el.height();
				var el_offset = el.offset().top;
				if (row_y === 0) row_y = el_offset;
				if (row_y < el_offset) {
					if (items.length > 0) {
						if (max_h > 0) {
							for (i = 0; i < items.length; i++)
								items[i].css('visibility', 'visible').height(max_h);
						}
						items = [];
						max_h = 0;
					}
					row_y = el_offset;
				}
				if (el_height > max_h) max_h = el_height;
				items.push(el);
			});
			if (items.length > 0) {
				for (i = 0; i < items.length; i++) {
					items[i].css('visibility', 'visible');
					if (max_h > 0) items[i].height(max_h);
				}
			}
		} );
	} );

} );