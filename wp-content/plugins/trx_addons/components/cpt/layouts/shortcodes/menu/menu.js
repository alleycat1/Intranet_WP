/* global jQuery */

( function() {
	"use strict";

	var $document = jQuery(document);

	$document.on( 'action.before_ready_trx_addons', function() {

		// Init Superfish menu - global declaration to use in other scripts
		window.trx_addons_init_sfmenu = function( selector ) {

			jQuery( selector ).show().each( function() {

				var $self = jQuery( this );

				var is_touch_device = ( 'ontouchstart' in document.documentElement );

				var animation_in = $self.parent().data( 'animation-in' );
				if ( animation_in == undefined || is_touch_device ) {
					animation_in = "none";
				}
				var animation_out = $self.parent().data( 'animation-out' );
				if ( animation_out == undefined || is_touch_device ) {
					animation_out = "none";
				}

				var sf_init = {

					delay:		300,
					speed: 		animation_in  != 'none' ? 500 : 200,
					speedOut:	animation_out != 'none' ? 300 : 200,
					autoArrows: false,
					dropShadows:false,

					onBeforeShow: function() {
						jQuery( this ).each( function() {
							var menu_item = jQuery( this ).data( 'menu-state', 'before-show' );
							if ( menu_item.hasClass('sc_layouts_submenu') && ! menu_item.hasClass('layouts_inited') && menu_item.find('.slider_container').length > 0 ) {
								menu_item.addClass('sc_layouts_submenu_prepare');
							} else {
								trx_addons_do_action( 'trx_addons_action_menu_on_before_show', menu_item );
								trx_addons_before_show_menu(menu_item);
							}
						} );
					},

					onShow: function() {
						jQuery( this ).each( function() {
							var menu_item = jQuery( this );
							if ( menu_item.data( 'menu-state' ) != 'before-show' ) {
								trx_addons_do_action( 'trx_addons_action_menu_on_show', menu_item );
								trx_addons_before_show_menu(menu_item);
							}
							menu_item.data('menu-state', 'show');
							trx_addons_do_action( 'trx_addons_action_menu_after_show', menu_item );
							trx_addons_after_show_menu(menu_item);

						} );
					},

					onBeforeHide: function() {
						jQuery( this ).each( function() {
							var menu_item = jQuery( this );
							if ( menu_item.data( 'menu-state' ) == 'show' ) {
								menu_item.data('menu-state', 'before-hide');
								trx_addons_do_action( 'trx_addons_action_menu_on_before_hide', menu_item );
								trx_addons_before_hide_menu(menu_item);
							}
						} );
					},

					onHide: function() {
						jQuery(this).each( function() {
							var menu_item = jQuery( this ).data('menu-state', 'hide');
							trx_addons_do_action( 'trx_addons_action_menu_on_hide', menu_item );
							trx_addons_after_hide_menu(menu_item);
						} );
					},

					onHandleTouch: function() {
						// Hack for iPad - disallow hide parents submenus on open nested level to prevent blink effect
						var $ul = jQuery( this ).parents('ul');
						if ( trx_addons_browser_is_ios() && $ul.length > 1 ) {
							$ul.addClass('sc_layouts_submenu_freeze');
							setTimeout( function() {
								$ul.removeClass('sc_layouts_submenu_freeze');
							}, 1000 );
						}
					}
				};

				if ( animation_in == 'none' ) {
					sf_init.animation = {
						opacity: 'show'
					};
				}
				if ( animation_out == 'none' ) {
					sf_init.animationOut = {
						opacity: 'hide'
					};
				}

				// Prevent close a submenu with layout '.sc_layouts_submenu' after click on any element with tabindex (can got focus)
				// and next click on the empty place inside the submenu layout.
				$self.find( '.sc_layouts_submenu_wrap' ).on( 'focusout', function( e ) {
					if ( e.currentTarget && jQuery( e.currentTarget ).hasClass( 'sc_layouts_submenu_wrap' ) ) {
						e.stopPropagation();
						return false;
					}
				} );

				// Init SuperFish
				$self.addClass('inited').superfish( trx_addons_apply_filters( 'trx_addons_filter_menu_init_args', sf_init ) );

				// Before show submenu
				function trx_addons_before_show_menu(menu_item) {
					// Disable show submenus in the vertical menu on the mobile screen
					//if (jQuery(window).width() < 768 && menu_item.parents(".sc_layouts_menu_dir_vertical").length > 0)
					//	return false;

					var in_columns = menu_item.parents('li[class*="columns-"]').length > 0
										&& ( ! menu_item.parent().attr('class')
											|| menu_item.parent().attr('class').indexOf('columns-') == -1
											);

					if ( ! in_columns ) {

						var window_width = jQuery(window).width(),
							page_wrap = jQuery(trx_addons_apply_filters( 'trx_addons_filter_page_wrap_class', TRX_ADDONS_STORAGE['page_wrap_class'] ? TRX_ADDONS_STORAGE['page_wrap_class'] : '.page_wrap', 'menu-before-show' )).eq(0),
							page_wrap_width = page_wrap.length > 0 ? page_wrap.width() : window_width,
							page_wrap_offset = page_wrap.length > 0 ? page_wrap.offset().left : 0,
							par = menu_item.parents("ul").eq(0),
							par_offset = par.length > 0 ? par.offset().left : 0,
							par_width  = par.length > 0 ? par.outerWidth() : 0,
							ul_width   = menu_item.outerWidth(),
							rtl = jQuery( 'body' ).hasClass( 'rtl' );

						// Detect horizontal position (left | right)
						if ( menu_item.parents("ul").length > 1 ) {
							if ( ( ! rtl && (
											( par_offset + par_width + ul_width > page_wrap_offset + page_wrap_width - 10 && par_offset - ul_width > page_wrap_offset )
											||
											( par_offset + par_width + ul_width > window_width && par_offset - ul_width > 0 )
											)
									)
								||
								( rtl && (
											( par_offset - ul_width < page_wrap_offset + 10 && par_offset + par_width + ul_width < page_wrap_offset + page_wrap_width )
											||
											( par_offset - ul_width < 0 && par_offset + par_width + ul_width < window_width )
										)
									)
							) {
								menu_item.addClass('submenu_left');
							} else {
								menu_item.removeClass('submenu_left');
							}
						}

						// Shift submenu in the main menu (if submenu is going out of the window)
						if (menu_item.parents('.top_panel').length > 0) {
							// Stretch submenu
							var wide = trx_addons_stretch_submenu(menu_item);

							// Shift horizontal
							if ( ! wide ) {
								var ul_pos = menu_item.data('ul_pos');
								if (ul_pos === undefined) {
									ul_pos = parseFloat( menu_item.css( menu_item.hasClass('submenu_left') ? 'right' : 'left' ) );
								}
								if ( isNaN(ul_pos) ) {
									ul_pos = 0;
								}
								var ul_offset = menu_item.parents("ul").length > 1
													? par_offset + ul_pos	// menu_item.offset().left
													: menu_item.parent().offset().left;
								if ( menu_item.hasClass('submenu_left') ) {
									if (ul_offset < 0) {
										if (menu_item.data('ul_pos') == undefined) {
											menu_item.data('ul_pos', ul_pos);
										}
										menu_item.css( {
											'right': ul_pos + ul_offset + 'px'
										} );
									}
								} else {
									if (ul_offset + ul_width >= window_width) {
										if (menu_item.data('ul_pos') == undefined) {
											menu_item.data('ul_pos', ul_pos);
										}
										menu_item.css( {
											'left': ( ul_pos - ( ul_offset + ul_width - window_width ) ) + 'px'
										} );
									}
								}

								// Shift vertical
								var ul_height = menu_item.outerHeight(),
									w_height = jQuery(window).height(),
									menu = menu_item.parents('.sc_layouts_menu').eq(0),
									row_offset = menu.length ? menu.offset().top - jQuery(window).scrollTop() : 0,
									row_height = 0,
									par_top = 0;
								par = menu_item.parent();
								par_offset = 0;
								while ( par.length > 0 ) {
									par_top = par.position().top;
									par_offset += par_top + par.parent().position().top;
									row_height = par.outerHeight();
									if (par_top === 0) break;
									par = par.parents('li').eq(0);
								}
								if (row_offset + par_offset + ul_height > w_height) {
									if (par_offset > ul_height) {
										menu_item.css( {
											'top': 'auto',
											'bottom': '-' + ( menu_item.css('padding-bottom') || 0 )
										} );
									} else {
										menu_item.css( {
											'top': '-' + ( par_offset - row_height - 2 ) + 'px',
											'bottom': 'auto'
										} );
									}
								}
							}
						}

						// Animation in
						var animated = false;
						trx_addons_do_action( 'trx_addons_action_menu_before_animation_in', menu_item, animation_in, animation_out );								
						if ( animation_in != 'none' ) {	// && ! menu_item.hasClass('sc_layouts_submenu_freeze')) {
							// To allow theme make own animation - filter handler must return true
							animated = trx_addons_apply_filters( 'trx_addons_filter_menu_animation_in', false, menu_item, animation_in, animation_out );
							if ( ! animated ) {
								if ( menu_item.hasClass('animated') && menu_item.hasClass(animation_out) ) {
									menu_item.removeClass('animated faster '+animation_out);
								}
								menu_item.addClass('animated fast '+animation_in);
								animated = true;
							}
						}

						// Trigger action
						$document.trigger('action.before_show_submenu', [menu_item] );
					}

					return animated;
				}

				// After show submenu
				function trx_addons_after_show_menu(menu_item) {

					// Init layouts
					if ( menu_item.hasClass('sc_layouts_submenu') ) {
						if ( ! menu_item.hasClass('layouts_inited') ) {
							trx_addons_stretch_submenu(menu_item);
							$document.trigger( 'action.init_hidden_elements', [menu_item] );
							if (menu_item.find('.slider_container').length > 0) {
								$document.on('action.slider_inited', function(e, slider, id) {
									trx_addons_before_show_menu(menu_item);
									menu_item
										.removeClass('sc_layouts_submenu_prepare')
										.addClass('layouts_inited');
								});
							} else {
								menu_item.addClass('layouts_inited');
							}
						}
						// Trigger 'resize' action
						$document.trigger('action.resize_trx_addons', [menu_item]);
					}

					// Trigger action
					$document.trigger('action.after_show_submenu', [menu_item] );
				}

				// Before hide submenu
				function trx_addons_before_hide_menu(menu_item) {
					// Remove video
					menu_item.find('.trx_addons_video_player.with_cover.video_play').removeClass('video_play').find('.video_embed').empty();
					
					// Disable show submenus in the vertival menu on the mobile screen
					//if (jQuery(window).width() < 768 && menu_item.parents(".sc_layouts_menu_dir_vertical").length > 0)
					//	return false;
					
					// Animation out
					var animated = false;
					trx_addons_do_action( 'trx_addons_action_menu_before_animation_out', menu_item, animation_in, animation_out );								
					if ( animation_out!='none' ) {	// && ! menu_item.hasClass('sc_layouts_submenu_freeze') ) {
						// To allow theme make own animation - filter handler must return true
						animated = trx_addons_apply_filters( 'trx_addons_filter_menu_animation_out', false, menu_item, animation_in, animation_out );
						if ( ! animated ) {
							if (menu_item.parents('[class*="columns-"]').length === 0 ) {
								if ( menu_item.hasClass('animated') && menu_item.hasClass(animation_in) ) {
									menu_item.removeClass('animated fast '+animation_in);
								}
								if ( menu_item.data('menu-state') == 'show' || menu_item.data('menu-state') == 'before-hide' ) {
									menu_item.addClass('animated faster '+animation_out);
									animated = true;
								}
							}
						}
					}

					// Trigger action
					$document.trigger('action.before_hide_submenu', [menu_item] );

					return animated;
				}

				// After hide submenu
				function trx_addons_after_hide_menu(menu_item) {
					// Restore submenu position after 0.5s (if hidden)
					setTimeout( function() {
						if ( menu_item.data('menu-state') == 'hide' ) {
							// Menu: Delete styles that are set programmatically
							menu_item.removeAttr( 'style' );
							// Menu bg: Delete styles that are set programmatically
							//          and restore background color
							var bg = menu_item.find('> .sc_layouts_menu_stretch_bg');
							if ( bg.length ) {
								bg.removeAttr( 'style' )		
									.css( {
										'background-color': menu_item.css('background-color')
									} );
							}
							// Inner container: Delete styles that are set programmatically
							var container = menu_item.data( 'reset-style' );
							if ( container ) {
								menu_item.find( container ).removeAttr( 'style' );
							}
							// Trigger action
							$document.trigger('action.after_hide_submenu', [menu_item] );
						}
					}, 500 );
				}

				// Stretch submenu with layouts
				function trx_addons_stretch_submenu(menu_item) {
					var done = false;
					if ( menu_item.length
						&& TRX_ADDONS_STORAGE['menu_stretch'] == 1
						&& ! menu_item.hasClass('trx_addons_no_stretch')
						&& ! menu_item.parents('.sc_layouts_menu').hasClass('sc_layouts_menu_dir_vertical')
						&& trx_addons_apply_filters( 'trx_addons_filter_stretch_menu',
														menu_item.hasClass('sc_layouts_submenu') || menu_item.parent().attr('class').indexOf('columns-') != -1,
														menu_item
													)
					) {
						var menu = menu_item.parents("ul");
						if ( menu.length == 1 ) {
							var $body = jQuery('body'),
								li = menu_item.parents("li").eq(0),
								stretch_to = trx_addons_apply_filters( 'trx_addons_filter_stretch_menu_to',
												li.hasClass( 'trx_addons_stretch_window' )
													? 'window'
													: ( li.hasClass( 'trx_addons_stretch_window_boxed' )
														? 'window_boxed'
														: 'content'
														),
												menu_item
											),
								content_wrap_selector = trx_addons_apply_filters( 'trx_addons_filter_stretch_menu_content_wrap_selector', '.content_wrap', menu_item ),
								content_wrap = jQuery( content_wrap_selector ).eq(0);
							if ( ! content_wrap.length ) {
								$body.append( trx_addons_apply_filters( 'trx_addons_filter_stretch_menu_content_wrap_html',
												'<div class="content_wrap" style="height:0;visibility:hidden;"></div>',
												menu_item
											) );
								content_wrap = jQuery( content_wrap_selector ).eq(0);
								if ( ! content_wrap.length ) {
									content_wrap = trx_addons_apply_filters( 'trx_addons_filter_stretch_menu_content_wrap', content_wrap, menu_item );
								}
							}
							if ( content_wrap.length == 1 ) {
								var bw = $body.innerWidth(),
									cw = trx_addons_apply_filters( 'trx_addons_filter_stretch_menu_content_wrap_width', content_wrap.innerWidth(), menu_item, content_wrap ),
									cw_offset = content_wrap.offset().left,
									li_offset = li.offset().left;
								menu_item
									.css( {
										'width': ( stretch_to == 'window' ? bw : cw ) + 'px',
										'max-width': 'none',
										'left': -li_offset + ( stretch_to == 'window' ? 0 : cw_offset ) + 'px',
										'right': 'auto'
									} );
								if ( stretch_to == 'window' ) {
									menu_item
										.data( 'reset-style', '.elementor-section-boxed > .elementor-container' )
										.find( '.elementor-section-boxed > .elementor-container' ).css( {'max-width': 'none' } );
								} else if ( stretch_to == 'window_boxed' ) {
									var bg = menu_item.find('> .sc_layouts_menu_stretch_bg');
									if ( bg.length === 0 ) {
										menu_item.append( '<span class="sc_layouts_menu_stretch_bg"></span>' );
										bg = menu_item.find('> .sc_layouts_menu_stretch_bg');
										bg.css( {
											'background-color': menu_item.css('background-color')
										} );
									}
									bg.css( {
										'left': -(cw_offset + 1) + 'px',
										'right': -(bw - cw_offset - cw + 1) + 'px'
									} );
								}
								done = true;
								$document.trigger('action.resize_trx_addons', [menu_item] );
							}
						}
					}
					return done;
				}
			});
		};

		// Init superfish menus
//		trx_addons_init_sfmenu('.sc_layouts_menu:not(.inited):not(.sc_layouts_menu_dir_vertical.sc_layouts_submenu_dropdown) > ul:not(.inited)');
		trx_addons_init_sfmenu('.sc_layouts_menu:not(.inited):not(.sc_layouts_submenu_dropdown) > ul:not(.inited)');

		// Check if menu need collapse (before menu showed)
		trx_addons_menu_collapse();

		// Show menu		
		jQuery('.sc_layouts_menu:not(.inited)').each(function() {
			if (jQuery(this).find('>ul.inited').length == 1) jQuery(this).addClass('inited');
		});
	
		// Slide effect for menu
		jQuery('.menu_hover_slide_line:not(.slide_inited),.menu_hover_slide_box:not(.slide_inited)').each(function() {
			var menu = jQuery(this).addClass('slide_inited');
			var style = menu.hasClass('menu_hover_slide_line') ? 'line' : 'box';
			setTimeout(function() {
				if (jQuery.fn.spasticNav !== undefined) {
					menu.find('>ul').spasticNav({
						style: style,
						//color: '',
						colorOverride: false
					});
				}
			}, 500);
		});
	
		// Burger with popup
		jQuery('.sc_layouts_menu_mobile_button_burger:not(.inited)').each(function() {
			var burger = jQuery(this);
			var popup = burger.find('.sc_layouts_menu_popup');
			if (popup.length == 1) {
				burger.addClass('inited').on('click', '>a', function(e) {
					popup.toggleClass('opened').slideToggle();
					e.preventDefault();
					return false;
				});
				popup.on('click', 'a', function(e) {
					var $item = jQuery(this);
					if ( $item.next().hasClass('sub-menu') ) {
						$item.parent().siblings().find( '>.sub-menu' ).fadeOut();
						$item.next().fadeToggle();
						e.preventDefault();
						return false;
					}
				});
				$document.on('click', function(e) {
					jQuery('.sc_layouts_menu_popup.opened').removeClass('opened').slideUp();
				});
			}
		});
	
	});
	

	// Collapse menu on resize
	$document.on('action.resize_trx_addons', function() {
		trx_addons_menu_collapse();
	});
	
	// Collapse menu items
	function trx_addons_menu_collapse() {
		if ( TRX_ADDONS_STORAGE['menu_collapse'] == 0 ) {
			return;
		}
		jQuery('.sc_layouts_menu:not(.sc_layouts_menu_no_collapse):not(.sc_layouts_menu_dir_vertical)').each( function() {
			var nav = jQuery( this );
			if ( nav.parents('div:hidden,section:hidden,article:hidden').length > 0 ) {
				return;
			}
			var ul = nav.find( '>ul:not(.sc_layouts_menu_no_collapse).inited' );
			if ( ul.length === 0 ) {		//|| ul.find('> li').length < 2
				return;
			}
			// Check if an item is a one of supported menu wrappers
			function check_menu_wrapper( item ) {
				var allow_any_wrapper = trx_addons_apply_filters( 'trx_addons_filter_menu_collapse_allow_any_wrapper', true );
				var rez = allow_any_wrapper;
				// Check for supported wrapper
				if ( ! allow_any_wrapper ) {
					var wrappers_list = trx_addons_apply_filters(
											'trx_addons_filter_menu_collapse_wrapper_classes',
											[
												'sc_layouts_column',		// Hardcoded column
												'wpb_wrapper',				// VC column
												'elementor-widget-wrap',	// Elementor widget wrapper
												'wp-block-column',			// Gutenberg column
												'kt-inside-inner-col'		// Kadence blocks column
											]
										);
					for (var i = 0; i < wrappers_list.length; i++ ){
						if ( item.hasClass( wrappers_list[i] ) ) {
							rez = true;
							break;
						}
					}
				}
				return rez;
			}
			// Check if an item is a one of allowed delimiters
			function check_item_delimiter( item ) {
				var delimiters_list = trx_addons_apply_filters(
											'trx_addons_filter_menu_collapse_delimiter_classes',
											[
												'vc_empty_space',					// VC Spacer
												'vc_separator',						// VC Separator
												'elementor-widget-spacer',			// Elementor Spacer
												'elementor-widget-divider',			// Elementor Divider
												'wp-block-spacer',					// Gutenberg Spacer
												'wp-block-separator',				// Gutenberg Separator
												'wp-block-kadence-spacer',			// Kadence Spacer
												'wp-block-coblocks-shape-divider'	// CoBlocks Divider
											]
										);
				var rez = false;
				for (var i = 0; i < delimiters_list.length; i++ ){
					if ( item.hasClass( delimiters_list[i] ) ) {
						rez = true;
						break;
					}
				}
				return rez;
			}
			var sc_layouts_item_wrapper = nav.parents('.sc_layouts_item').eq(0),
				sc_layouts_item = sc_layouts_item_wrapper.length > 0 ? sc_layouts_item_wrapper : nav,
				sc_layouts_item_parent = sc_layouts_item.parent();
			if ( ! check_menu_wrapper( sc_layouts_item_parent ) ) {
				return;
			}
			// Calculate max free space for menu
			var w_max = sc_layouts_item_parent.width()
						- ( Math.ceil( parseFloat( sc_layouts_item.css('marginLeft') ) ) + Math.ceil( parseFloat( sc_layouts_item.css('marginRight') ) ) )
						- 2;	// Leave additional 2px empty
			var w_siblings = 0, in_group = 0, ul_id = ul.attr('id');
			sc_layouts_item_parent.find('>div:not(.elementor-background-overlay)').each( function() {
				if ( in_group > 1 ) {
					return;
				}
				var $self = jQuery(this);
				if ( check_item_delimiter( $self ) ) {
					if ( in_group == 1 ) {
						in_group = 2;
					} else {
						w_siblings = 0;
					}
				} else {
					if ( $self.find( '#' + ul_id ).length > 0 ) {
						in_group = 1;
					} else {
						w_siblings += ( $self.outerWidth() + Math.ceil(parseFloat( $self.css('marginLeft') ) ) + Math.ceil( parseFloat( $self.css('marginRight') ) ) );
					}
				}
			});
			w_max -= w_siblings;
			// Add collapse item if not exists
			var w_all = 0;
			var move = false;
			var li_collapse = ul.find('li.menu-item.menu-collapse');
			if ( li_collapse.length === 0 ) {
				ul.append('<li class="menu-item menu-collapse"><a href="#" class="sf-with-ul '+TRX_ADDONS_STORAGE['menu_collapse_icon']+'"></a><ul class="submenu"></ul></li>');
				li_collapse = ul.find('li.menu-item.menu-collapse');
			}
			var li_collapse_ul = li_collapse.find('> ul');
			// Check if need to move items
			ul.find('> li').each( function( idx ) {
				var cur_item = jQuery( this );
				cur_item.data( 'index', idx );
				if ( move || cur_item.attr('id') == 'blob' ) {
					return;
				}
				w_all += ! cur_item.hasClass('menu-collapse') || cur_item.css('display') != 'none' 
							? cur_item.outerWidth()
								+ Math.ceil( parseFloat( cur_item.css( 'marginLeft' ) ) )
								+ Math.ceil( parseFloat( cur_item.css( 'marginRight' ) ) )
							: 0;
				if ( w_all > w_max ) {
					move = true;
				}
			} );
			// If need to move items to the collapsed item
			if ( move ) {
				w_all = li_collapse.outerWidth()
							+ Math.ceil( parseFloat( li_collapse.css( 'marginLeft' ) ) )
							+ Math.ceil( parseFloat( li_collapse.css( 'marginRight' ) ) );
				ul.find( "> li:not('.menu-collapse')" ).each( function( idx ) {
					var cur_item = jQuery( this );
					var cur_width = cur_item.outerWidth()
										+ Math.ceil( parseFloat( cur_item.css( 'marginLeft' ) ) )
										+ Math.ceil( parseFloat( cur_item.css( 'marginRight' ) ) );
					if ( w_all <= w_max ) {
						w_all += cur_width;
					}
					if ( w_all > w_max ) {
						var moved = false;
						li_collapse_ul.find( '>li' ).each( function() {
							if ( ! moved && Number( jQuery( this ).data( 'index' ) ) > idx ) {
								cur_item.attr( 'data-width', cur_width ).insertBefore( jQuery( this ) );
								moved = true;
							}
						} );
						if ( ! moved ) {
							cur_item.attr( 'data-width', cur_width ).appendTo( li_collapse_ul );
						}
					}
				} );
				li_collapse.show();
				
			// Else - move items to the menu again
			} else {
				var items = li_collapse_ul.find( '>li' );
				var cnt = 0;
				move = true;
				//w_all += 20; 	// Leave 20px empty
				items.each( function() {
					if ( ! move ) {
						return;
					}
					if ( items.length - cnt == 1 ) {
						w_all -= ( li_collapse.outerWidth()
									+ Math.ceil( parseFloat( li_collapse.css( 'marginLeft' ) ) )
									+ Math.ceil( parseFloat( li_collapse.css( 'marginRight' ) ) )
									);
					}
					w_all += parseFloat( jQuery( this ).data( 'width' ) );
					if ( w_all < w_max ) {
						jQuery( this ).insertBefore( li_collapse );
						cnt++;
					} else {
						move = false;
					}
				} );
				if ( items.length - cnt === 0 ) {
					li_collapse.hide();
				}
			}
		} );
	}

} )();