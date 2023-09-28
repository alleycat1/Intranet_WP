/**
 * Init scripts
 *
 * @package ThemeREX Addons
 * @since v1.0
 */

/* global jQuery, TRX_ADDONS_STORAGE */

(function() {

	"use strict";

	// Start on DOM ready
	jQuery( document ).ready( function() {

		var ready_busy = true;

		var vc_init_counter = 0;

		var parallax_controller = null;

		var requestAnimationFrame = trx_addons_request_animation_frame();

		var $window             = jQuery( window ),
			$document           = jQuery( document ),
			$html               = jQuery( 'html' ),
			$body               = jQuery( 'body' );

		var $page_preloader     = jQuery('#page_preloader'),
			$scroll_to_top      = jQuery('.trx_addons_scroll_to_top'),
			$scroll_progress    = $scroll_to_top.find('.trx_addons_scroll_progress');

		var _video_sticky_fade  = true;

		var $show_on_scroll,
			$banner_placeholder,
			$animated_elements,
			$animated_hover,
			$video_sticky,
			$fixed_columns,
			$stack_sections,
			$parallax_wrap,
			$video_tags,
			$iframe_tags,
			$video_autoplay,
			$video_autoplay_yt,
			$video_hovers_yt = false;


		// Set body classes according User Agent
		var classes = trx_addons_browser_classes();
		for ( var ua in classes ) {
			if ( $body.hasClass( 'ua_' + ua ) ) {
				if ( ! classes[ua] ) {
					$body.removeClass( 'ua_' + ua );
				}
			} else if ( classes[ua] ) {
				$body.addClass( 'ua_' + ua );
			}
		}

		// Update links and values after the new post added
		$document.on( 'action.got_ajax_response', update_jquery_links );
		$document.on( 'action.init_hidden_elements', update_jquery_links );
		var first_run = true;
		function update_jquery_links(e) {
			if ( first_run && e && e.namespace == 'init_hidden_elements' ) {
				first_run = false;
				return; 
			}
			$show_on_scroll     = jQuery('.trx_addons_show_on_scroll');
			$banner_placeholder = jQuery('.trx_addons_banner_placeholder');
			$animated_elements  = jQuery('[data-post-animation^="animated"]:not(.animated)');
			$animated_hover     = jQuery('[data-hover-animation^="animated"]:not(.animated)');
			$video_sticky       = jQuery('.trx_addons_video_sticky');
			$fixed_columns      = jQuery('.sc_column_fixed');
//			if ( $body.hasClass( 'fixed_blocks_sticky' ) && $fixed_columns.length > 0 ) {
//				$fixed_columns.each(function() {
//					jQuery(this).parents('.elementor-section,.vc_row').eq(0).toggleClass( 'trx_addons_has_fixed_columns', true );
//				} );
//			}
			$stack_sections     = jQuery('.sc_stack_section_on:not(.elementor-element-edit-mode)');
			if ( $stack_sections.length > 0 ) {
				$body.addClass( 'sc_stack_section_present ' + ( window.trx_addons_browser_is_ios() ? 'ua_ios' : 'ua_not_ios' ) );
			}
			$parallax_wrap      = jQuery('.sc_parallax_wrap' );
			$video_tags         = jQuery('video');
			$iframe_tags        = jQuery('iframe');	// ',.video_frame iframe'
			// Load Youtube API to enable autoplay videos from Youtube in the iframe on click on the button 'Play' on iOS devices
			if ( trx_addons_browser_is_ios() ) {
				$video_hovers_yt = jQuery('.video_hover[data-video*="youtu"],.post_video_hover[data-video*="youtu"],.trx_addons_video_list_controller_item[data-video*="youtu"]');
				if ( $video_hovers_yt.length > 0 ) {
					embedYoutubeAPI();
				}
			}
			trx_addons_find_video_autoplay();
		}
		update_jquery_links();

		// Update autoplay videos
		function trx_addons_find_video_autoplay() {
			$video_autoplay    = jQuery('.with_video_autoplay');
			$video_autoplay_yt = $video_autoplay.find('iframe[src*="youtu"]');
		}


		// Page preloader
		//-----------------------------------------

		// Show preloader
		window.trx_addons_show_preloader = function() {
			if ( $page_preloader.length > 0 && ( ! jQuery.browser || ! jQuery.browser.safari ) && ! $body.hasClass( 'ua_safari' ) ) {
				// Trigger action to allow the theme redefine a preloader
				$page_preloader.data('done', false);
				$document.trigger( 'action.page_preloader', [$page_preloader] );
				// If preloader not redefined - display it
				if ( ! $page_preloader.data('done') ) {
					$page_preloader
						.css( {
							display: 'block',
							opacity: 0
						} )
						.animate( {
							opacity: $page_preloader.data('opacity')
						}, 300);
					setTimeout( trx_addons_hide_preloader, 5000 );
				}
			}
		};

		// Hide preloader
		window.trx_addons_hide_preloader = function() {
			if ( $page_preloader.length > 0 ) {
				$page_preloader.data('done', false);
				$document.trigger( 'action.page_preloader_hide', [$page_preloader] );
				// If preloader not redefined - hide it
				if ( ! $page_preloader.data('done') && $page_preloader.css('opacity') > 0 ) {
					$page_preloader.animate(
						{
							opacity: 0
						},
						800,
						function() {
							jQuery(this).css( { display: 'none' } );
						}
					);
				}
			}
		};

		if ( $page_preloader.length > 0 ) {
			$page_preloader.data('opacity', $page_preloader.css('opacity'));

			// Show preloader
			$window.on( 'beforeunload', function(e) {
				if ( typeof e.srcElement == 'undefined'
					|| typeof e.srcElement.activeElement == 'undefined'
					|| typeof e.srcElement.activeElement.href == 'undefined'
					|| e.srcElement.activeElement.href.indexOf('//') === 0
					|| e.srcElement.activeElement.href.indexOf('http:') === 0
					|| e.srcElement.activeElement.href.indexOf('https:') === 0
				) {
					trx_addons_show_preloader();
				}
			} );
			$document.on( 'action.before_new_page_content', function( e, $link, show ) {
				if ( show || show === undefined ) {
					trx_addons_show_preloader();
				}
			} );
			$document.on( 'action.after_new_page_content', function( e, $link ) {
				trx_addons_hide_preloader();
			} );


			// Disable preloader on the links with 'tel:' or 'mailto:' or similar (not 'http:' or 'https:')
			$document.on( 'click', 'a', function() {
				var href = jQuery(this).attr('href');
				if ( href !== undefined && href.indexOf('//') !== 0 && href.indexOf('http:') !== 0 && href.indexOf('https:') !== 0 ) {
					setTimeout( function() {
						if ( $page_preloader.css( 'display' ) == 'block' ) {
							$page_preloader.css( {
								display: 'none',
								opacity: 0
							} );
						}
					}, 1 );
				}
			} );
		}

		// Page first load actions
		//==============================================
		$document.on('action.init_trx_addons', function() {
			// Close panel on ESC is pressed
			$document.on('keyup', function(e) {
				if (e.keyCode === 27) {
					jQuery( '.sc_layouts_panel_opened' ).each( function() {
						trx_addons_close_panel( jQuery(this) );
					} );
				}
			});
		} );

		// Init intersection observer
		trx_addons_intersection_observer_init();

		// Init all other actions
		trx_addons_init_actions();


		// Init actions
		//--------------------------------------
		function trx_addons_init_actions() {
			if (typeof TRX_ADDONS_STORAGE == 'undefined') {
				window.TRX_ADDONS_STORAGE = {
											'vc_edit_mode': false,
											'popup_engine': 'magnific'
										};
			}
			if (TRX_ADDONS_STORAGE['vc_edit_mode'] && jQuery('.vc_empty-placeholder').length === 0 && vc_init_counter++ < 30) {
				setTimeout(trx_addons_init_actions, 200);
				return;
			}

			$document.trigger('action.before_init_trx_addons');

			// Hide preloader
			trx_addons_hide_preloader();
			
			// Show system message
			var msg = jQuery('.trx_addons_message_box_system'),
				msg_delay = 5000;
			if (msg.length > 0) {
				setTimeout(function() {
					msg.fadeIn().delay(msg_delay).fadeOut();
				}, 1000);
				var login = jQuery('.trx_addons_login_link');
				if (msg.hasClass('trx_addons_message_box_error') && login.length > 0) {
					setTimeout(function() {
						login.trigger('click');
					}, 2000+msg_delay);
				}
			}

			// Shift page down to display hash-link if menu is fixed
			// (except WooCommerce product page, because WooCommerce has own handler of the hash url)
			if (typeof TRX_ADDONS_STORAGE['animate_to_hash']=='undefined' && !$body.hasClass('single-product')) {
				TRX_ADDONS_STORAGE['animate_to_hash'] = true;
				setTimeout(function() {
					var $mc4form = false;

					// Hack for MailChimp - use our scroll to form, because his method damage layouts in the Chrome
					if (window.mc4wp_forms_config && window.mc4wp_forms_config.submitted_form && window.mc4wp_forms_config.submitted_form.element_id) {
						trx_addons_document_animate_to(window.mc4wp_forms_config.submitted_form.element_id);

					} else if ( TRX_ADDONS_STORAGE['animate_to_mc4wp_form_submitted'] && ( $mc4form = jQuery( '.mc4wp-form-submitted' ) ).length ) {
						trx_addons_document_animate_to( $mc4form );
					
					// Shift page down on fixed rows height
					} else if ( location.hash !== '' && location.hash != '#' && location.hash.indexOf('/') == -1 ) {
						var obj = jQuery(location.hash);
						if (obj.length > 0) {
							var off = obj.offset().top;
							if ( ! isNaN( off )
								&& ( ( trx_addons_fixed_rows_height() > 0 && off - trx_addons_window_scroll_top() < trx_addons_fixed_rows_height() + 60 )
									|| trx_addons_window_scroll_top() === 0
									)
							) {
								trx_addons_document_animate_to(off - trx_addons_fixed_rows_height() - 60);
							}
						}
					}
				}, 600);
			}
			
			// Check for Retina display
			trx_addons_set_cookie('trx_addons_is_retina', trx_addons_is_retina() ? 1 : 0);

			// On switch to mobile layout: hide hover animations
			$document.on( 'action.switch_to_mobile_layout', function() {
				jQuery('[data-hover-animation^="animated"]').each(function() {
					var $self = jQuery(this);
					var animation = $self.data('hover-animation');
					var animation_out = $self.data('animation-out');
					if (animation_out === undefined) animation_out = "none";
					$self.removeClass(animation + ' ' + animation_out);
				});
			});

			// Init core elements
			trx_addons_ready_actions();

			// Call plugins specific action (if exists)
			//----------------------------------------------
			$document.trigger('action.before_ready_trx_addons');
			$document.trigger('action.ready_trx_addons');
			$document.trigger('action.after_ready_trx_addons');

			// Add ready actions to the hidden elements actions
			$document.on( 'action.init_hidden_elements', function( e, cont ) {
				trx_addons_ready_actions(e, cont);

				// Generate 'resize' event after hidden elements are inited
				//$window.trigger('resize');

				// Generate 'scroll' event after hidden elements are inited
				$window.trigger('scroll');
			} );

			// Add our handlers after the VC init
			var vc_js = false;
			$document.on('vc_js', function() {
				if ( ! vc_js )	{
					vc_js = true;
					trx_addons_add_handlers();
				}
			});

			// Add our handlers if VC is no activated
			setTimeout(function() {
				if ( ! vc_js )	{
					trx_addons_add_handlers();
				}
			}, 1);
			
			// Add our handlers
			function trx_addons_add_handlers() {		

				// Resize handlers
				//-----------------------
				// First run
				trx_addons_resize_actions();
				// Add handler
				$window.on( 'resize', function() {
					trx_addons_resize_actions();
				} );

				// Scroll handlers
				//-----------------------
				function trx_addons_scroll_start( force ) {
					if ( requestAnimationFrame && ! force ) {
						if ( ! TRX_ADDONS_STORAGE['scroll_busy'] ) {
							TRX_ADDONS_STORAGE['scroll_busy'] = true;
							requestAnimationFrame( trx_addons_scroll_actions );
						}
					} else {
						TRX_ADDONS_STORAGE['scroll_busy'] = true;
						trx_addons_scroll_actions();
					}
				}
				// First run
				trx_addons_scroll_start();
				// Add handler
				$window.on( 'scroll', function() {
					trx_addons_scroll_start();
				} );

				// Smooth scroll via GSAP TweenMax (if loaded)
				if ( TRX_ADDONS_STORAGE['smooth_scroll'] > 0 && typeof TweenMax != 'undefined' ) {
					var $scroll_target = $window;
					var scroll_time = trx_addons_apply_filters( 'trx_addons_filter_smooth_scroll_time', 0.8 );           // 0.6
					var scroll_distance = trx_addons_apply_filters( 'trx_addons_filter_smooth_scroll_distance', 400 );   // 400
					var ie_mobile = -1 !== navigator.userAgent.indexOf("IEMobile"),
						is_mobile = trx_addons_browser_is_mobile() || $body.hasClass('ua_mobile');
					var scroll_busy = false;
					var scroll_coef = trx_addons_apply_filters( 'trx_addons_filter_smooth_scroll_coef', {
						start: 0.2,
						multi: 1.5,
						max: 1.5,
						value: 0.2
					} );
					var scroll_delta = 0;
					var scroll_tween = null;
					var clear_scroll_busy = trx_addons_throttle( function() {
						scroll_busy = false;
						if ( scroll_tween ) {
							scroll_tween.kill();
							scroll_tween = false;
						}
					}, scroll_time * 1000 + 10, true );


					window.smooth_scroll_listener = function( event ) {
						if ( trx_addons_window_width() < 768 ) {
							return;
						}
						if ( ! scroll_busy ) {
							var depth = 8;
							var $target = jQuery( event.target );
							$scroll_target = $window;
 							while( depth-- >= 0 ) {
								if ( [ 'scroll', 'auto' ].indexOf( $target.css('overflow-y') ) >= 0 ) {
									$scroll_target = $target;
									break;
								}
								$target = $target.parents( 'div,section,nav,ul' ).eq(0);
							}
						}
						if ( $scroll_target != $window ) {
							scroll_busy = true;
							setTimeout( function() { scroll_busy = false; }, scroll_time + 10 );
							return;
						}
						event.preventDefault();
						var delta = event.wheelDelta / 120 || -event.detail / 3;
						if ( trx_addons_browser_is_ios() ) delta = Math.max( -1, Math.min( 1, delta ) );
						scroll_coef.value = scroll_busy && scroll_delta * delta > 0 ? Math.min( scroll_coef.max, scroll_coef.value * scroll_coef.multi ) : scroll_coef.start;
						scroll_delta = delta;
						var scroll_top = $scroll_target.scrollTop();
						var scroll_to = scroll_top - parseInt( delta * scroll_coef.value * scroll_distance, 10 );
						scroll_busy = true;
						if ( scroll_tween ) {
							scroll_tween.kill();
						}
						scroll_tween = TweenMax.to( $scroll_target, scroll_time, {
							onStart: function() {
								scroll_busy = true;
								clear_scroll_busy();
								trx_addons_do_action( 'trx_addons_action_smooth_scroll_start', this, $scroll_target, scroll_to, scroll_time );
							},
							onInterrupt: function() {
								scroll_busy = false;
								scroll_tween = null;
								trx_addons_do_action( 'trx_addons_action_smooth_scroll_interrupt', this, $scroll_target, scroll_to, scroll_time );
							},
							onComplete: function() {
								scroll_busy = false;
								scroll_tween = null;
								trx_addons_do_action( 'trx_addons_action_smooth_scroll_complete', this, $scroll_target, scroll_to, scroll_time );
							},
							onUpdate: function() {
								trx_addons_do_action( 'trx_addons_action_smooth_scroll_update', this, $scroll_target, scroll_to, scroll_time );
							},
							scrollTo: {
								y: scroll_to,
								autoKill: true
							},
							ease: trx_addons_apply_filters( 'trx_addons_filter_smooth_scroll_ease', Power1.easeOut ),   // Power1 (easest) - Power4 (sharpest)
//							autoKill: true,
							overwrite: true, // 5
							tween_value: 100
						} );
					};

					window.smooth_scroll_disable = function() {
						if (typeof smooth_scroll_listener !== 'undefined') {
							window.removeEventListener( 'mousewheel', smooth_scroll_listener, {
								passive: false
							} );
							window.removeEventListener( 'DOMMouseScroll', smooth_scroll_listener, {
								passive: false
							} );
						}
					};

					window.smooth_scroll_enable = function() {
						if (typeof smooth_scroll_listener !== 'undefined') {
							window.addEventListener( 'mousewheel', smooth_scroll_listener, {
								passive: false
							} );
							window.addEventListener( 'DOMMouseScroll', smooth_scroll_listener, {
								passive: false
							} );
						}
					};

					// Add listener if is not a touch device
					if ( ! ie_mobile && ! $html.hasClass( 'touch' ) ) {
						smooth_scroll_enable();
					}
				}

				// Inject our code to the VC function wpb_prepare_tab_content()
				// to init our elements on the new VC tabs, tour and accordion activation
				typeof window.wpb_prepare_tab_content == "function"
					&& typeof window.wpb_prepare_tab_content_old == "undefined"
					&& (window.wpb_prepare_tab_content_old = window.wpb_prepare_tab_content)
					&& (window.wpb_prepare_tab_content = function(e, ui) {
						// Call ThemeREX Addons actions
						if (typeof ui.newPanel !== 'undefined' && ui.newPanel.length > 0) {
							$document.trigger( 'action.init_hidden_elements', [ui.newPanel] );
						} else if (typeof ui.panel !== 'undefined' && ui.panel.length > 0) {
							$document.trigger( 'action.init_hidden_elements', [ui.panel] );
						}
						// Call old VC handler
						window.wpb_prepare_tab_content_old(e, ui);
					} );
				// Inject our code in the VC function vc_accordionActivate()
				// to init our elements on the old VC accordion activation
				typeof window.vc_accordionActivate == "function"
					&& typeof window.vc_accordionActivate_old == "undefined"
					&& (window.vc_accordionActivate_old = window.vc_accordionActivate)
					&& (window.vc_accordionActivate = function(e, ui) {
						// Call ThemeREX Addons actions
						if (typeof ui.newPanel !== 'undefined' && ui.newPanel.length > 0) {
							$document.trigger( 'action.init_hidden_elements', [ui.newPanel] );
						} else if (typeof ui.panel !== 'undefined' && ui.panel.length > 0) {
							$document.trigger( 'action.init_hidden_elements', [ui.panel] );
						}
						// Call old VC handler
						window.vc_accordionActivate_old(e, ui);
					} );
			}
			$document.trigger('action.init_trx_addons');
			$document.trigger('action.after_init_trx_addons');
		}	// trx_addons_init_actions

		// Init components on DOM ready
		//==============================================
		function trx_addons_ready_actions(e, container) {

			if (container === undefined) container = $body;
			// Maybe comment ? (because this function is called on 'action.init_hidden_elements')
			//update_jquery_links();

			// Animate to the page-inner links
			//----------------------------------------------
			if (TRX_ADDONS_STORAGE['animate_inner_links'] > 0 && !container.hasClass('animate_to_inited')) {
				container
					.addClass('animate_to_inited')
					.on('click', 'a', function(e) {
						var link_obj = jQuery(this);
						var link_parent = link_obj.parent();
						// Skip tabs and accordions
						if (link_parent.parent().hasClass('trx_addons_tabs_titles')	// trx_addons_tabs
							|| link_obj.hasClass('trx_addons_panel_link')			// sc_layouts_panel
							|| link_obj.hasClass('trx_addons_popup_link')			// sc_layouts_popup
							|| link_parent.hasClass('vc_tta-tab') 					// new VC tabs, old VC tabs, new VC tour
							|| link_obj.hasClass('vc_pagination-trigger')			// pagination in VC tabs
							|| link_obj.hasClass('ui-tabs-anchor') 					// old VC tour
							|| link_parent.hasClass('vc_tta-panel-title')			// new VC accordion
							|| link_parent.hasClass('wpb_accordion_header') 		// old VC accordion
							|| link_parent.parents('.wc-tabs').length > 0 			// WooCommerce tabs on the single product page
							|| link_parent.hasClass('elementor-tab-title')			// Elementor's tabs
							|| link_parent.parents('ul[class*="tabs"]').length > 0 	// All other tabs
						) {
							return true;
						}
						var href = link_obj.attr('href');
						if ( ! href || href == '#' ) return true;
						if ( trx_addons_is_local_link(href) ) {
							var pos = href.indexOf('#'),
								offset = 0;
							if ( pos >= 0 ) {
								href = href.substr( pos );
								if ( jQuery(href).length > 0 ) {
									// Removing class before menu closing animations started
									// is need to animate inner links and scrolling to the anchor
									// Attention! Is a temporary (theme-specific) solution -
									//            it must be placed to the theme's init script.
									if ( $body.hasClass( 'menu_mobile_opened' ) ) {
										$body.removeClass( 'menu_mobile_opened' );
									}
									$document.trigger( 'action.trx_addons_inner_links_click', [ link_obj, e ] );
									trx_addons_document_animate_to( href );
									e.preventDefault();
									return false;
								}
							}
						}
					});
			}

			// Add parameters target="_blank" and rel="nofollow" to all external links
			//------------------------------------------------------------------------
			if (TRX_ADDONS_STORAGE['add_target_blank'] > 0) {
				jQuery('a').filter(function() {
					return this.hostname
							&& this.hostname !== location.hostname
							&& this.pathname
							&& ['.png', '.jpg', '.gif'].indexOf( this.pathname.slice( -4 ) ) < 0;
				}).each( function() {
					var link = jQuery(this),
						rel  = link.attr('rel');
					if ( link.attr('target') != '_blank' ) {
						link.attr('target', '_blank');
					}
					if ( ! rel || rel.indexOf('nofollow') == -1 ) {
						link.attr('rel', ( rel ? rel + ' ' : '' ) + 'nofollow');
					}
				});
			}

			// Hide empty figcaptions
			jQuery('figcaption').each( function() {
				var $self = jQuery(this);
				if ( $self.text() === '' ) {
					$self.hide();
				}
			});

			// Tabs
			//------------------------------------
			if (jQuery.ui && jQuery.ui.tabs) {
				var $tabs = container.find('.trx_addons_tabs:not(.inited)');
				if ( $tabs.length > 0) {
					$tabs.each( function() {
						var $self = jQuery(this);
						// Get initially opened tab
						var init = $self.data('active');
						if (isNaN(init)) {
							init = 0;
							var active = $self.find('> ul > li[data-active="true"]').eq(0);
							if (active.length > 0) {
								init = active.index();
								if (isNaN(init) || init < 0) init = 0;
							}
						} else {
							init = Math.max(0, init);
						}
						// Get disabled tabs
						var disabled = [];
						$self.find('> ul > li[data-disabled="true"]').each( function() {
							disabled.push(jQuery(this).index());
						});
						// Init tabs
						$self.addClass('inited').tabs({
							active: init,
							disabled: disabled,
							show: {
								effect: 'fadeIn',
								duration: 300
							},
							hide: {
								effect: 'fadeOut',
								duration: 300
							},
							create: function( event, ui ) {
								if ( ui.panel.length > 0 && ! ready_busy ) {
									$document.trigger( 'action.create_tab', [ui.panel] );
									$document.trigger( 'action.init_hidden_elements', [ui.panel] );
								}
							},
							activate: function( event, ui ) {
								if ( ui.oldPanel.length > 0 && ! ready_busy ) {
									$document.trigger( 'action.deactivate_tab', [ui.oldPanel] );
								}
								if ( ui.newPanel.length > 0 && ! ready_busy ) {
									$document.trigger( 'action.activate_tab', [ui.newPanel] );
									$document.trigger( 'action.init_hidden_elements', [ui.newPanel] );
									$window.trigger( 'resize' );
								}
							}
						});
					});
				}
			}
		
		
			// Accordion
			//------------------------------------
			if (jQuery.ui && jQuery.ui.accordion) {
				var $accordion = container.find('.trx_addons_accordion:not(.inited)');
				if ( $accordion.length > 0) {
					$accordion.each(function () {
						// Get headers selector
						var accordion = jQuery(this);
						var headers = accordion.data('headers') || 'h5';
						// Get height style
						var height_style = accordion.data('height-style') || 'content';
						// Get collapsible
						var collapsible = accordion.data('collapsible') || false;
						// Get initially opened tab
						var init = accordion.data('active');
						var active = false;
						if ( isNaN( init ) ) {
							init = 0;
							active = accordion.find( headers + '[data-active="true"]' ).eq(0);
							if ( active.length > 0 ) {
								while ( ! active.parent().hasClass( 'trx_addons_accordion' ) ) {
									active = active.parent();
								}
								init = active.index();
								if ( isNaN( init ) || init < 0 ) init = 0;
							}
						} else {
							init = Math.max( 0, init );
						}
						// Init accordion
						accordion.addClass('inited').accordion({
							active: init,
							collapsible: collapsible,
							header: headers,
							heightStyle: height_style,
							create: function( event, ui ) {
								if ( ui.panel.length > 0 && ! ready_busy ) {
									$document.trigger( 'action.create_accordion', [ui.panel] );
									$document.trigger( 'action.init_hidden_elements', [ui.panel] );
								} else if ( active !== false && active.length > 0 ) {
									// If headers and panels wrapped into div
									active.find('>'+headers).trigger('click');
								}
							},
							activate: function( event, ui ) {
								if (ui.oldPanel.length > 0 && ! ready_busy) {
									$document.trigger( 'action.deactivate_accordion', [ui.oldPanel] );
								}
								if (ui.newPanel.length > 0 && ! ready_busy) {
									$document.trigger( 'action.activate_accordion', [ui.newPanel] );
									$document.trigger( 'action.init_hidden_elements', [ui.newPanel] );
									$window.trigger( 'resize' );
								}
							}
						});
					});
				}
			}
		
			// Color Picker
			//----------------------------------------------------------------
			var cp = container.find('.trx_addons_color_selector:not(.inited)'),
				cp_created = false;
			if (cp.length > 0) {
				cp.addClass('inited').each( function() {
					var $self = jQuery(this);
					// Internal ColorPicker
					if ( $self.hasClass('iColorPicker') ) {
						if (!cp_created) {
							trx_addons_color_picker();
							cp_created = true;
						}
						trx_addons_change_field_colors($self);
						$self
							.on('focus', function (e) {
								trx_addons_color_picker_show(null, jQuery(this), function(fld, clr) {
									fld.val(clr).trigger('change');
									trx_addons_change_field_colors(fld);
								});
							})
							.on('change', function(e) {
								trx_addons_change_field_colors(jQuery(this));
							});
						
					// WP ColorPicker - Iris
					} else if (typeof jQuery.fn.wpColorPicker != 'undefined') {
						$self.wpColorPicker({
							// you can declare a default color here,
							// or in the data-default-color attribute on the input
							//defaultColor: false,
					
							// hide the color picker controls on load
							//hide: true,
					
							// show a group of common colors beneath the square
							// or, supply an array of colors to customize further
							//palettes: true,
					
							// a callback to fire whenever the color changes to a valid color
							change: function(e, ui){
								jQuery(e.target).val(ui.color).trigger('change');
							},
					
							// a callback to fire when the input is emptied or an invalid color
							clear: function(e) {
								jQuery(e.target).prev().trigger('change');
							}
						});
					}
				});
			}
		
			// Change colors of the field
			function trx_addons_change_field_colors(fld) {
				var clr = fld.val(),
					hsb = trx_addons_hex2hsb(clr);
				fld.css({
					'backgroundColor': clr,
					'color': hsb['b'] < 70 ? '#fff' : '#000'
				});
			}


			// Range Slider
			//------------------------------------
			if (jQuery.ui && jQuery.ui.slider) {
				var $range_slider = container.find('.trx_addons_range_slider:not(.inited)');
				if ($range_slider.length > 0) {
					$range_slider.each( function () {
						// Get parameters
						var range_slider = jQuery(this);
						var linked_field = range_slider.data('linked_field');
						if (linked_field===undefined) linked_field = range_slider.prev('input[type="hidden"]');
						else linked_field = jQuery('#'+linked_field);
						if (linked_field.length == 0) return;
						var range_slider_cur = range_slider.find('> .trx_addons_range_slider_label_cur');
						var range_slider_type = range_slider.data('range');
						if (range_slider_type===undefined) range_slider_type = 'min';
						var values = linked_field.val().split(',');
						var minimum = range_slider.data('min');
						if (minimum===undefined) minimum = 0;
						var maximum = range_slider.data('max');
						if (maximum===undefined) maximum = 0;
						var step = range_slider.data('step');
						if (step===undefined) step = 1;
						// Init range slider
						var init_obj = {
							range: range_slider_type,
							min: minimum,
							max: maximum,
							step: step,
							slide: function(event, ui) {
								trx_addons_range_slider_update_current_values_position(ui, range_slider_type === 'min' ? [ui.value] : ui.values);
							},
							change: function(event, ui) {
								trx_addons_range_slider_update_current_values_position(ui, range_slider_type === 'min' ? [ui.value] : ui.values);
							},
							create: function(event, ui) {
								trx_addons_range_slider_update_current_values_position(ui, values);
							}
						};
						if (range_slider_type === true) {
							init_obj.values = values;
						} else {
							init_obj.value = values[0];
						}
						range_slider.addClass('inited').slider(init_obj);

						// Update position of labels with the current values
						function trx_addons_range_slider_update_current_values_position(ui, cur_values) {
							linked_field.val( cur_values.join(',') ).trigger('change');
							for (var i=0; i < cur_values.length; i++) {
								range_slider_cur.eq(i)
										.html(cur_values[i])
										.css('left', Math.max(0, Math.min(100, (cur_values[i]-minimum)*100/(maximum-minimum)))+'%');
							}
						}
					});
				}
			}
		
		
			// Select2
			//------------------------------------
			if (jQuery.fn && jQuery.fn.select2) {
				container.find('.trx_addons_select2:not(.inited)').addClass('inited').select2();
			}


			// Video player
			//----------------------------------------------

			// Play video on hover (for desktops) or on touchstart (for mobile devices)
			var $play_on_hover = container.find( '.trx_addons_video_hover:not(.inited)' );
			var touchstart_just_fired = false;
			if ( $play_on_hover.length > 0 ) {
				$play_on_hover
					.addClass( 'inited' )
					.on( 'mouseenter touchstart', function(e) {
						var $self = jQuery( this );
						if ( ! $self.hasClass( 'trx_addons_video_hover_play' ) && ( e.type != 'touchstart' || ! touchstart_just_fired ) ) {
							$self
								.data( 'trx-addons-user-actions', 1 )
								.removeClass( 'trx_addons_video_hover_pause' )
								.addClass( 'trx_addons_video_hover_play' )
								.find( 'video' ).get(0).play();
							touchstart_just_fired = true;
							setTimeout( function() {
								touchstart_just_fired = false;
							}, 300 );
						}
					} )
					.on( 'mouseleave touchstart', function(e) {
						var $self = jQuery( this );
						if ( $self.hasClass( 'trx_addons_video_hover_play' ) && ( e.type != 'touchstart' || ! touchstart_just_fired ) ) {
							$self
								.data( 'trx-addons-user-actions', 1 )
								.removeClass( 'trx_addons_video_hover_play' )
								.addClass( 'trx_addons_video_hover_pause' )
								.find( 'video' ).get(0).pause();
							$self
								.find( '.trx_addons_video_subtitle_text' )
									.one( typeof window.trx_addons_transition_end != 'undefined' ? trx_addons_transition_end() : 'transitionend', function() {
										$self.removeClass( 'trx_addons_video_hover_pause' );
									} );
							touchstart_just_fired = true;
							setTimeout( function() {
								touchstart_just_fired = false;
							}, 300 );
						}
					} );
				// Autoplay/pause video on enter/leave to the viewport (if an option "autoplay" is on)
				var $play_on_hover_autoplay = $play_on_hover.find( 'video[data-autoplay="1"]' );
				if ( $play_on_hover_autoplay.length > 0 ) {
					trx_addons_intersection_observer_add( $play_on_hover_autoplay, function( item, enter ) {
						if ( item.data( 'trx-addons-user-actions' ) ) {
							trx_addons_intersection_observer_remove( item );
						} else if ( enter ) {
							item
								.addClass( 'trx_addons_video_hover_play' )
								.get(0).play();
						} else {
							item
								.removeClass( 'trx_addons_video_hover_play' )
								.get(0).pause();
						}
					} );
				}
			}

			// Video frame 'Play' button
			var $video_hover = container.find('.trx_addons_video_player.with_cover .video_hover:not(.inited)');
			if ( $video_hover.length > 0 ) {
				$video_hover
					.addClass( 'inited' )
					.on( 'click', function(e) {
						var $self = jQuery( this );

						// If video in the popup
						if ( $self.hasClass( 'trx_addons_popup_link' ) ) {
							return true;
						}

						// Replace a content of the container with the video player
						trx_addons_insert_video_iframe( $self.parents('.trx_addons_video_player').eq(0).addClass('video_play').find('.video_embed'), $self.data('video') );
		
						// If video in the slide
						var slider = $self.parents('.slider_swiper').eq(0);
						if ( slider.length > 0 ) {
							var id = slider.attr('id');
							if ( typeof TRX_ADDONS_STORAGE['swipers'][id].autoplay != 'undefined' ) {
								TRX_ADDONS_STORAGE['swipers'][id].autoplay.stop();
								// If slider have controller - stop it too
								id = slider.data('controller');
								if ( id && TRX_ADDONS_STORAGE['swipers'][id+'_swiper'] ) {
									TRX_ADDONS_STORAGE['swipers'][id+'_swiper'].autoplay.stop();
								}
							}
						} else {
							$self.fadeOut();
						}
		
						e.preventDefault();
						
						$document.trigger( 'action.init_hidden_elements', [$self.parents('.trx_addons_video_player').eq(0)] );
						$window.trigger('resize');
						
						return false;
					})
					.parents('.trx_addons_video_player')
					.on( 'click', function(e) {
						var $self = jQuery(this);
						if ( ! $self.hasClass('video_play') ) {
							jQuery(this).find('.video_hover').trigger('click');
							e.preventDefault();
							return false;
						}
					} );				
			}

			// Video player controller
			var $video_controller = container.find('.trx_addons_video_list_controller_wrap:not(.inited)');
			if ($video_controller.length > 0) {
				// Init controller
				$video_controller
					.addClass('inited')
					.on('click', '.trx_addons_video_list_controller_item > a[href="#"]', function(e) {

						e.preventDefault();

						var item = jQuery(this).parent(),
							video = item.data('video'),
							title = item.data('title'),
							video_wrap = item.parents('.trx_addons_video_list').find('.trx_addons_video_list_video_wrap .trx_addons_video_player').parent();

						if ( ! item.hasClass('trx_addons_video_list_controller_item_active') && video && video_wrap.length == 1 ) {
							item.parent().find('.trx_addons_video_list_controller_item_active').removeClass('trx_addons_video_list_controller_item_active');
							item.addClass('trx_addons_video_list_controller_item_active');
							var autoplay = video_wrap.find('.with_video_autoplay');
							if ( autoplay.length ) {
								autoplay
									.removeClass( 'with_video_autoplay video_autoplay_inited video_autoplay_started' )
									.find('video_frame_controls').remove();
								trx_addons_intersection_observer_remove( autoplay );
								trx_addons_find_video_autoplay();
							}
							video_wrap
								.fadeTo( 300, 0, function() {
									video_wrap.height( video_wrap.height() );
									// Replace a content of the container with the video player
									trx_addons_insert_video_iframe( video_wrap, video );
									// Add a title
									if ( title ) {
										video_wrap.append( title );
									}
									video_wrap.find('video').removeAttr('width').removeAttr('height');
									$document.trigger( 'action.init_hidden_elements', [video_wrap] );
									$window.trigger( 'resize' );
									video_wrap.height('auto');
								} )
								.fadeTo( 300, 1, function() {
									if ( item.data( 'autoplay' ) > 0 && video_wrap.find( '.trx_addons_video_player .video_hover').length > 0 ) {
										video_wrap.find( '.trx_addons_video_player .video_hover').eq(0).trigger( 'click' );
									}
								} );
						}

						return false;
					});
				// Preload cover images
				setTimeout( function() {
					$video_controller.find('[data-video]').each( function() {
						var video = jQuery(this).data('video');
						if ( video ) {
							var img = jQuery('img', video);
							if ( img.length ) {
								var obj = new Image();
								obj.src = img.attr('src');
							}
						}
					} );
				}, trx_addons_apply_filters('trx_addons_filter_video_controller_preload_images_timeout', 0) );
			}

			// Video sticky
			$video_sticky.each( function() {
				
				var video = jQuery(this);

				// Close button
				video
					.find( '.trx_addons_video_sticky_close:not(.inited)' )
					.addClass( 'inited' )
					.on( 'click', function( e ) {

						e.preventDefault();

						jQuery(this).hide();

						trx_addons_intersection_observer_remove( video );

						if ( _video_sticky_fade ) {
							video.addClass('trx_addons_video_sticky_on_fade').stop().animate({opacity:0}, 300, function() {
								video.parents('.post_featured').removeClass( 'with_video_sticky_on' );
								video.height('auto').removeClass( 'trx_addons_video_sticky trx_addons_video_sticky_on trx_addons_video_sticky_on_fade' ).stop().animate({'opacity': 1}, 500);
								$video_sticky = jQuery('.trx_addons_video_sticky');
								$window.trigger('resize');
							});
						} else {
							video.parents('.post_featured').removeClass( 'with_video_sticky_on' );
							video.height('auto').removeClass('trx_addons_video_sticky trx_addons_video_sticky_on');
							$video_sticky = jQuery('.trx_addons_video_sticky');
							$window.trigger('resize');
						}

						return false;
					} );

				// Fix / Unfix sticky video to the bottom of the viewport (window)
				trx_addons_intersection_observer_add( video, function( item, enter, entry ) {
					var video = item,
						video_top = video.offset().top,
						video_height = video.height();
					if ( ! enter ) {
						if ( ( typeof entry != 'object' || entry.boundingClientRect.top < 0 )
								&& ! video.hasClass('trx_addons_video_sticky_on')
						) {
							// Stick video only if it present in .video_frame
							if ( video.find( '.video_frame' ).html().trim().length > 30 ) {
								video.parents('.post_featured').addClass( 'with_video_sticky_on' );
								if ( _video_sticky_fade ) {
									video.height( video_height ).css('opacity', 0).addClass( 'trx_addons_video_sticky_on' ).stop().animate({opacity:1}, 500);
								} else {
									video.height( video_height ).addClass( 'trx_addons_video_sticky_on' );
								}
							}
						}
					} else {
						if ( video.hasClass('trx_addons_video_sticky_on') && ! video.hasClass('trx_addons_video_sticky_on_fade') ) {
							if ( _video_sticky_fade ) {
								video.addClass('trx_addons_video_sticky_on_fade').stop().animate({opacity:0}, 300, function() {
									video.parents('.post_featured').removeClass( 'with_video_sticky_on' );
									video.height('auto').removeClass( 'trx_addons_video_sticky_on trx_addons_video_sticky_on_fade' ).stop().animate({'opacity': 1}, 500);
									$window.trigger('resize');
								});
							} else {
								video.parents('.post_featured').removeClass( 'with_video_sticky_on' );
								video.height('auto').removeClass( 'trx_addons_video_sticky_on' );
								$window.trigger('resize');
							}
						}
					}
				} );
			} );

			// Show autoplaing videos from not controlled sources
			$video_autoplay.each( function() {
				var $self = jQuery(this);
				if ( $self.find('iframe[src*="youtu"]').length === 0 ) {
					$self.find('.video_frame').addClass('video_frame_visible');
				}
			} );

		
			// Popups & Panels
			//----------------------------------------------

			// PrettyPhoto Engine
			if (TRX_ADDONS_STORAGE['popup_engine'] == 'pretty') {
				// Display lightbox on click on the image
				container
					.find( trx_addons_apply_filters( 'pretty-init-images',
							'a[href$="jpg"]:not(.inited):not([target="_blank"]):not([download])'
							+',a[href$="jpeg"]:not(.inited):not([target="_blank"]):not([download])'
							+',a[href$="png"]:not(.inited):not([target="_blank"]):not([download])'
							+',a[href$="gif"]:not(.inited):not([target="_blank"]):not([download])'
							)
						)
					.each( function() {
						var $self = jQuery(this);
						if ( ! $self.parent().hasClass('woocommerce-product-gallery__image')) {
							$self.attr('rel', 'prettyPhoto[slideshow]');
						}
					});
				var images = container.find( trx_addons_apply_filters( 'pretty-init-images-selector',
												'a[rel*="prettyPhoto"]'
												+ ':not(.inited)'
												+ ':not(.esgbox)'
												+ ':not(.fancybox)'
												+ ':not([target="_blank"])'
												+ ':not([data-rel*="pretty"])'
												+ ':not([rel*="magnific"])'
												+ ':not([data-rel*="magnific"])'
												+ ':not([data-elementor-lightbox-slideshow])'
												+ ':not([data-elementor-open-lightbox="yes"])'
												+ ':not([data-elementor-open-lightbox="default"])'
												)
											).addClass('inited');
				if ( images.length > 0 ) {
					try {
						images.prettyPhoto( trx_addons_apply_filters( 'pretty-init-images-params', {
							social_tools: '',
							theme: 'facebook',
							deeplinking: false
						} ) );
					} catch (e) {}
				}
			
			// or Magnific Popup Engine
			} else if (TRX_ADDONS_STORAGE['popup_engine']=='magnific' && typeof jQuery.fn.magnificPopup != 'undefined') {
				// Display lightbox on click on the image
				container
					.find( trx_addons_apply_filters( 'mfp-init-images',
							 'a[href$="jpg"]:not(.inited):not([target="_blank"]):not([download])'
							+',a[href$="jpeg"]:not(.inited):not([target="_blank"]):not([download])'
							+',a[href$="png"]:not(.inited):not([target="_blank"]):not([download])'
							+',a[href$="gif"]:not(.inited):not([target="_blank"]):not([download])'
							)
						)
					.each( function() {
						var $self = jQuery(this);
						if ( trx_addons_apply_filters( 'mfp-init-images-allow',
								$self.closest('.cq-dagallery').length === 0
									&& $self.closest('.woocommerce-product-gallery__image').length === 0
									&& ! $self.hasClass('prettyphoto')
									&& ! $self.hasClass('esgbox'),
								$self
								)
						) {
							$self.attr('rel', 'magnific');
						}
					});
				var images = container.find( trx_addons_apply_filters( 'mfp-init-images-selector',
												'a[rel*="magnific"]'
												+ ':not(.inited)'
												+ ':not(.esgbox)'
												+ ':not(.fancybox)'
												+ ':not([target="_blank"])'
												+ ':not([download])'
												+ ':not(.prettyphoto)'
												+ ':not([rel*="pretty"])'
												+ ':not([data-rel*="pretty"])'
												+ ':not([data-elementor-lightbox-slideshow])'
												+ ':not([data-elementor-open-lightbox="yes"])'
												+ ':not([data-elementor-open-lightbox="default"])'
												)
											).addClass('inited');
				if ( images.length > 0 ) {
					// Unbind prettyPhoto
					setTimeout(function() {	images.off('click.prettyphoto'); }, 100);
					// Bind Magnific
					try {
						images.magnificPopup( trx_addons_apply_filters( 'mfp-init-images-params', {
							type: 'image',
							mainClass: 'mfp-img-mobile',
							closeOnContentClick: true,
							closeBtnInside: true,
							fixedContentPos: true,
							midClick: true,
							//removalDelay: 500, 
							preloader: true,
							tLoading: TRX_ADDONS_STORAGE['msg_magnific_loading'],
							tClose: TRX_ADDONS_STORAGE['msg_magnific_close'],
							closeMarkup: '<button title="%title%" aria-label="%title%" type="button" class="mfp-close"><span class="mfp-close-icon">&#215;</span></button>',
							gallery:{
								enabled: true
							},
							image: {
								tError: TRX_ADDONS_STORAGE['msg_magnific_error'],
								verticalFit: true,
								titleSrc: function(item) {
									var title = '',
										$el = typeof item.el != 'undefined' ? jQuery( item.el ) : null;
									if ( $el && $el.length > 0 ) {
										var $next = $el.next();
										if ( $next.length > 0 && $next.get(0).tagName == 'FIGCAPTION' ) {
											title = $next.text();
										} else if ( $el.attr( 'title' ) ) {
											title = $el.attr( 'title' );
										} else {
											var $img = $el.find( 'img' );
											if ( $img.length > 0 ) {
												title = $img.attr( 'alt' );
												if ( ! title ) {
													title = $img.data('caption');
												}
											}
										}
									}
									return title;
								}
							},
							zoom: {
								enabled: true,
								duration: 300,
								easing: 'ease-in-out',
								opener: function(openerElement) {
									// openerElement is the element on which popup was initialized, in this case its <a> tag
									// you don't need to add "opener" option if this code matches your needs, it's defailt one.
									if (!openerElement.is('img')) {
										if (openerElement.parents('.trx_addons_hover').find('img').length > 0)
											openerElement = openerElement.parents('.trx_addons_hover').find('img');
										else if (openerElement.find('img').length > 0)
											 openerElement = openerElement.find('img');
										else if (openerElement.siblings('img').length > 0)
											 openerElement = openerElement.siblings('img');
										else if (openerElement.parent().parent().find('img').length > 0)
											 openerElement = openerElement.parent().parent().find('img');
									}
									return openerElement; 
								}
							},
							callbacks: {
								beforeClose: function(){
									jQuery('.mfp-figure figcaption').hide();
									jQuery('.mfp-figure .mfp-arrow').hide();
								}
							}
						} ) );
					} catch (e) {}
				}

				// Prepare links to popups & panels
				//----------------------------------------
				var on_leaving_site = [],
					in_page_edit_mode = $body.hasClass('elementor-editor-active')
										|| $body.hasClass('wp-admin')
										|| $body.hasClass('block-editor-page');
				// Init popups and panels links
				container.find('.sc_layouts_popup:not(.inited),.sc_layouts_panel:not(.inited)').each( function() {
					var $self = jQuery(this),
						id = $self.attr('id'),
						show = false;
					if (!id) return;
					var is_panel = $self.hasClass('sc_layouts_panel'),
						link = jQuery('a[href="#'+id+'"],' + ( is_panel ? '.trx_addons_panel_link[data-panel-id="'+id+'"]' : '.trx_addons_popup_link[data-popup-id="'+id+'"]' ) );
					if (link.length === 0) {
						$body.append('<a href="#'+id+'" class="trx_addons_hidden"></a>');
						link = jQuery('a[href="#'+id+'"]');
					}
					if ($self.hasClass('sc_layouts_show_on_page_load')) {
						show = true;
					} else if ($self.hasClass('sc_layouts_show_on_page_load_once') && trx_addons_get_cookie('trx_addons_show_on_page_load_once_'+id) != '1') {
						trx_addons_set_cookie('trx_addons_show_on_page_load_once_'+id, '1');
						show = true;
					} else if ($self.hasClass('sc_layouts_show_on_page_close') && trx_addons_get_cookie('trx_addons_show_on_page_close_'+id) != '1') {
						on_leaving_site.push({
							link: link,
							id: id
						});
					}
					if (show) {
						// Display popups (panels) on the page (site) load
						if ( ! in_page_edit_mode ) {
							setTimeout( function() {
								link.trigger('click');
							}, $self.data('delay') > 0 ? $self.data('delay') * 1000 : 0 );
						}
					}
					link
						.addClass(is_panel ? 'trx_addons_panel_link' : 'trx_addons_popup_link')
						.data('panel', $self);
					$self
						.addClass('inited')
						.on('click', '.sc_layouts_panel_close', function(e) {
							trx_addons_close_panel($self);
							e.preventDefault();
							return false;
						});
				});
				
				// Display popup when user leaving site
				if ( on_leaving_site.length > 0 && ! in_page_edit_mode ) {
					var showed = false;
					$window.on( 'mousemove', function(e) {
						if ( showed ) return;
						var y = typeof e.clientY != 'undefined' ? e.clientY : 999;
						if ( y < trx_addons_adminbar_height() + 15 ) {
							showed = true;
							on_leaving_site.forEach( function(item) {
								item.link.trigger('click');
								trx_addons_set_cookie('trx_addons_show_on_page_close_'+item.id, '1');
							});
						}
					} );
				}

				// Display lightbox on click on the popup link
				container.find( trx_addons_apply_filters( 'mfp-init-popup-selector', ".trx_addons_popup_link:not(.popup_inited)" ) )
					.addClass('popup_inited')
					.magnificPopup( trx_addons_apply_filters( 'mfp-init-popup-params', {
						type: 'inline',
						focus: 'input',
						removalDelay: trx_addons_apply_filters('trx_addons_filter_close_popup_timeout', 0),
						tLoading: TRX_ADDONS_STORAGE['msg_magnific_loading'],
						tClose: TRX_ADDONS_STORAGE['msg_magnific_close'],
						closeBtnInside: true,
						closeMarkup: '<button title="%title%" aria-label="%title%" type="button" class="mfp-close"><span class="mfp-close-icon">&#215;</span></button>',
						callbacks: {
							// Will fire when this before popup is opened
							// this - is Magnific Popup object
							beforeAppend: function () {
								var $mfp = this;
								// Prepare content for the popup
								$document.trigger('action.prepare_popup_elements', [$mfp.content, $mfp]);
							},
							beforeOpen: function() {
								var $mfp = this;
								$document.trigger('action.open_popup_elements', [$mfp.content]);
								// Add in animation (separately popup and bg overlay)
								var wrap_animation_in = mfp_get_animation( $mfp, 'wrap', 'open' ),
									bg_animation_in   = mfp_get_animation( $mfp, 'bg', 'open' );
								if ( bg_animation_in ) {
									$mfp.bgOverlay.addClass(bg_animation_in);
								}
								if ( wrap_animation_in ) {
									$mfp.wrap.addClass(wrap_animation_in);
								}
							},
							// Will fire when this exact popup is opened
							// this - is Magnific Popup object
							open: function () {
								// Get saved content or store it (if first open occured)
								trx_addons_prepare_popup_content(this.content, true);
							},
							beforeClose: function() {
								var $mfp = this;
								$document.trigger('action.close_popup_elements', [$mfp.content]);
								// Add out animation (separately popup and bg overlay)
								var wrap_animation_in  = mfp_get_animation( $mfp, 'wrap', 'open' ),
									bg_animation_in    = mfp_get_animation( $mfp, 'bg', 'open' ),
									wrap_animation_out = mfp_get_animation( $mfp, 'wrap', 'close' ),
									bg_animation_out   = mfp_get_animation( $mfp, 'bg', 'close' ),
									delay              = wrap_animation_out 
															? trx_addons_apply_filters( 'mfp-init-popup-animations-duration',
																wrap_animation_out.indexOf('faster') != -1
																	? 300
																	: ( wrap_animation_out.indexOf('fast') != -1
																		? 500
																		: (	wrap_animation_out.indexOf('normal') != -1
																			? 800
																			: ( wrap_animation_out.indexOf('slow') != -1
																				? 2000
																				: ( wrap_animation_out.indexOf('slower') != -1
																					? 3000
																					: 1000
																					)
																				)
																			)
																		),
																wrap_animation_out
																)
															: 0;
								$mfp.st.removalDelay += delay;
								if ( bg_animation_out ) {
									setTimeout( function() {
										$mfp.bgOverlay.removeClass(bg_animation_in).addClass(bg_animation_out);
									}, $mfp.st.removalDelay - delay );
								}
								if ( wrap_animation_out ) {
									setTimeout( function() {
										$mfp.wrap.removeClass(wrap_animation_in).addClass(wrap_animation_out);
									}, $mfp.st.removalDelay - delay );
								}
							},
							close: function () {
								var $mfp = this;
								// Save and remove content before closing
								// if its contain video, audio or iframe
								trx_addons_close_panel($mfp.content);
							},
							// resize event triggers only when height is changed or layout forced
							resize: function () {
								var $mfp = this;
								trx_addons_resize_actions(jQuery($mfp.content));
							}
						}
					} ) );

				// Return animation name for popup
				var mfp_get_animation = function( mfp, item, event, defa ) {
					var defaults = trx_addons_apply_filters( 'mfp-init-popup-animations', {
						'wrap_open':  'fadeIn animated fast',
						'wrap_close': 'fadeOut animated fast',
						'bg_open':    'fadeIn animated fast',
						'bg_close':   'fadeOut animated fast'
					} );
					return mfp.st.el.attr('data-popup-'+item+'-'+event+'-animation')
								? mfp.st.el.attr('data-popup-'+item+'-'+event+'-animation')
								: trx_addons_apply_filters( 'mfp-init-popup-'+item+'-'+event+'-animation', defa ? defa : defaults[item+'_'+event] );
				};

				// Open panel on click on the panel link
				container.find( trx_addons_apply_filters( 'init-panel-selector', ".trx_addons_panel_link:not(.panel_inited)" ) )
					.addClass('panel_inited')
					.on('click', function(e) {
						var panel = jQuery(this).data('panel');
						if ( ! panel.hasClass( 'sc_layouts_panel_opened' ) ) {
							$document.trigger('action.prepare_popup_elements', [panel]);
							trx_addons_prepare_popup_content(panel, true);
							panel.addClass('sc_layouts_panel_opened');
							$document.trigger('action.opened_popup_elements', [panel]);
							if (panel.prev().hasClass('sc_layouts_panel_hide_content')) panel.prev().addClass('sc_layouts_panel_opened');
							$body.addClass('sc_layouts_panel_opened sc_layouts_panel_opened_' + panel.data('panel-position'));
							var panel_class = panel.data('panel-class');
							if ( panel_class ) {
								$body.addClass( panel_class + '_opened' );
							}
						} else {
							trx_addons_close_panel(panel);
						}
						e.preventDefault();
						return false;
					});

				// Close panel on click on the modal cover
				container.find('.sc_layouts_panel_hide_content:not(.inited)')
					.addClass('inited')
					.on('click', function(e) {
						trx_addons_close_panel(jQuery(this).next());
						e.preventDefault();
						return false;
					});

				// Close panel
				window.trx_addons_close_panel = function(panel) {
					if ( panel.hasClass('sc_layouts_panel') ) {
						$document.trigger('action.close_popup_elements', [panel]);
					}
					setTimeout( function() {
						panel.removeClass('sc_layouts_panel_opened');
						if (panel.prev().hasClass('sc_layouts_panel_hide_content')) {
							panel.prev().removeClass('sc_layouts_panel_opened');
						}
						$body.removeClass('sc_layouts_panel_opened sc_layouts_panel_opened_left sc_layouts_panel_opened_right sc_layouts_panel_opened_top sc_layouts_panel_opened_bottom');
						var panel_class = panel.data('panel-class');
						if ( panel_class ) {
							$body.removeClass( panel_class + '_opened' );
						}
						if ( panel.data('popup-content') !== undefined ) {
							setTimeout( function() { panel.empty(); }, 500 );
						}
					}, trx_addons_apply_filters('trx_addons_filter_close_panel_timeout', panel.hasClass('sc_layouts_panel') && panel.data('animation-delay') !== undefined ? panel.data('animation-delay') : 0, panel) );
				};

				// Get saved content for panel or popup or store it (if first open occured)
				window.trx_addons_prepare_popup_content = function(container, autoplay) {
					var wrapper = jQuery(container);
					// Store popup content to the data-param or restore it when popup open again (second time)
					// if popup contains audio or video or iframe
					if (wrapper.data('popup-content') === undefined) {
						var iframe = wrapper.find('iframe');
						if ( wrapper.find('audio').length
							|| wrapper.find('video').length
							|| ( iframe.length
								&& ( ( iframe.data('src') && iframe.data('src').search(/(youtu|vimeo|daily|facebook)/i) > 0 )
									|| 
									 ( iframe.attr('src') && iframe.attr('src').search(/(youtu|vimeo|daily|facebook)/i) > 0 )
									)
								)
						) {
							wrapper.data( 'popup-content', wrapper.html() );
						}
					} else {
						wrapper.html( wrapper.data('popup-content') );
						// Remove class 'inited' to reinit elements
						wrapper.find('.inited').removeClass('inited');
					}
					// Replace src with data-src
					wrapper.find('[data-src]').each(function() {
						jQuery(this).attr( 'src', jQuery(this).data('src') );
					});
					// Init hidden elements
					$document.trigger( 'action.init_hidden_elements', [wrapper] );
					// Init third-party plugins in the popup
					$document.trigger( 'action.init_popup_elements', [wrapper] );
					// If popup contain embedded video - add autoplay
					if (autoplay) trx_addons_set_autoplay(wrapper);
					// If popup contain essential grid
					var frame = wrapper.find('.esg-grid');
					if ( frame.length > 0 ) {
						var wrappers = [".esg-tc.eec", ".esg-lc.eec", ".esg-rc.eec", ".esg-cc.eec", ".esg-bc.eec"];
						for (var i = 0; i < wrappers.length; i++) {
							frame.find(wrappers[i]+'>'+wrappers[i]).unwrap();
						}
					}
					// Call resize actions for the new content
					$window.trigger('resize');
				};
			}


			// Views counter via AJAX
			//--------------------------------------
			if ( TRX_ADDONS_STORAGE['ajax_views'] && ! TRX_ADDONS_STORAGE['post_views_counter_inited'] ) {
				TRX_ADDONS_STORAGE['post_views_counter_inited'] = true;
				$document.on( 'action.ready_trx_addons', function() {
					setTimeout( function() {
						jQuery.post( TRX_ADDONS_STORAGE['ajax_url'], {
							action: 'post_counter',
							nonce: TRX_ADDONS_STORAGE['ajax_nonce'],
							post_id: TRX_ADDONS_STORAGE['post_id'],
							views: 1
						} ).done( function( response ) {
							var rez = {};
							try {
								rez = JSON.parse(response);
							} catch (e) {
								rez = { error: TRX_ADDONS_STORAGE['ajax_error'] };
								console.log(response);
							}
							if ( rez.error === '' ) {
								jQuery('.post_meta_single .post_meta_views .post_meta_number,.sc_layouts_title_meta .post_meta_views .post_meta_number').html(rez.counter);
							}
							$document.trigger( 'action.got_ajax_response', {
								action: 'post_counter',
								result: rez
							} );
						} );
					}, 10 );
				} );
			}


			// Likes counter
			//---------------------------------------------
			var $likes = container.find('a.post_meta_likes:not(.inited),a.comment_counters_likes:not(.inited)');
			if ($likes.length > 0) {
				var likes_busy = false;
				$likes
					.addClass('inited')
					.on('click', function(e) {
						if ( ! likes_busy) {
							likes_busy = true;
							var button = jQuery(this);
							var inc = button.hasClass('enabled') ? 1 : -1;
							var post_id = button.hasClass('post_meta_likes') ? button.data('postid') :  button.data('commentid');
							var cookie_likes = trx_addons_get_cookie(button.hasClass('post_meta_likes') ? 'trx_addons_likes' : 'trx_addons_comment_likes');
							if (cookie_likes === undefined || cookie_likes===null) cookie_likes = '';
							jQuery.post( TRX_ADDONS_STORAGE['ajax_url'], {
								action: button.hasClass('post_meta_likes') ? 'post_counter' : 'comment_counter',
								nonce: TRX_ADDONS_STORAGE['ajax_nonce'],
								post_id: post_id,
								likes: inc
							}).done(function(response) {
								var rez = {};
								try {
									rez = JSON.parse(response);
								} catch (e) {
									rez = { error: TRX_ADDONS_STORAGE['msg_ajax_error'] };
									console.log(response);
								}
								if (rez.error === '') {
									var counter = rez.counter;
									if (inc == 1) {
										var title = button.data('title-dislike');
										button.removeClass('enabled trx_addons_icon-heart-empty').addClass('disabled trx_addons_icon-heart');
										cookie_likes += (cookie_likes.substr(-1)!=',' ? ',' : '') + post_id + ',';
									} else {
										var title = button.data('title-like');
										button.removeClass('disabled trx_addons_icon-heart').addClass('enabled trx_addons_icon-heart-empty');
										cookie_likes = cookie_likes.replace(','+post_id+',', ',');
									}
									button.data('likes', counter).attr('title', title).find(button.hasClass('post_meta_likes') ? '.post_meta_number' : '.comment_counters_number').html(counter);
									trx_addons_set_cookie( button.hasClass('post_meta_likes') ? 'trx_addons_likes' : 'trx_addons_comment_likes', cookie_likes, 365 * 24 * 60 * 60 * 1000);
								} else {
									alert(TRX_ADDONS_STORAGE['msg_error_like']);
								}
								likes_busy = false;
								$document.trigger( 'action.got_ajax_response', {
									action: button.hasClass('post_meta_likes') ? 'post_counter' : 'comment_counter',
									result: rez
								});
							});
						}
						e.preventDefault();
						return false;
					});
			}
		
		
			// Emotions counter
			//---------------------------------------------
			var $emotions = container.find('.trx_addons_emotions:not(.inited)');
			if ($emotions.length > 0) {
				var emotions_busy = false;
				$emotions
					.addClass('inited')
					.on('click', '.trx_addons_emotions_item', function(e) {
						if (!emotions_busy) {
							emotions_busy = true;
							var button = jQuery(this);
							var button_active = button.parent().find('.trx_addons_emotions_active');
							var post_id = button.data('postid');
							jQuery.post(TRX_ADDONS_STORAGE['ajax_url'], {
								action: 'post_counter',
								nonce: TRX_ADDONS_STORAGE['ajax_nonce'],
								post_id: post_id,
								emotion_inc: button.data('slug'),
								emotion_dec: button_active.length > 0 ? button_active.data('slug') : '',
							}).done(function(response) {
								var rez = {};
								try {
									rez = JSON.parse(response);
								} catch (e) {
									rez = { error: TRX_ADDONS_STORAGE['msg_ajax_error'] };
									console.log(response);
								}
								if (rez.error === '') {
									var cookie_likes = trx_addons_get_cookie('trx_addons_emotions'),
										cookie_likes_new = ',';
									if (cookie_likes) {
										cookie_likes = cookie_likes.split(',');
										for (var i=0; i<cookie_likes.length; i++) {
											if (cookie_likes[i] === '') continue;
											var tmp = cookie_likes[i].split('=');
											if (tmp[0] != post_id) cookie_likes_new += cookie_likes[i] + ',';
										}
									}
									cookie_likes = cookie_likes_new;
									if (button_active.length > 0) {
										button_active.removeClass('trx_addons_emotions_active');
									}
									if (button_active.length == 0 || button.data('slug') != button_active.data('slug')) {
										button.addClass('trx_addons_emotions_active');
										cookie_likes += (cookie_likes.substr(-1)!=',' ? ',' : '') + post_id + '=' + button.data('slug') + ',';
									}
									for (var i in rez.counter) {
										button.parent().find('[data-slug="'+i+'"] .trx_addons_emotions_item_number').html(rez.counter[i]);
									}
									trx_addons_set_cookie('trx_addons_emotions', cookie_likes, 365 * 24 * 60 * 60 * 1000);
								} else {
									alert(TRX_ADDONS_STORAGE['msg_error_like']);
								}
								emotions_busy = false;
								$document.trigger( 'action.got_ajax_response', {
									action: 'post_counter',
									result: rez
								});
							});
						}
						e.preventDefault();
						return false;
					});
			}
		
		
			// Socials share
			//----------------------------------------------
			var $share_caption = container.find('.socials_share .socials_caption:not(.inited)');
			if ($share_caption.length > 0) {
				$share_caption.each(function() {
					jQuery(this).addClass('inited').on('click', function(e) {
						jQuery(this).siblings('.social_items').slideToggle();	//.toggleClass('opened');
						e.preventDefault();
						return false;
					});
				});
			}
			var $share_items = container.find('.socials_share .social_items:not(.inited)');
			if ($share_items.length > 0) {
				$share_items.each(function() {
					jQuery(this)
						.addClass('inited')
						.on('click', '.social_item_popup', function(e) {
							var url = jQuery(this).data('link');
							window.open(url, '_blank', 'scrollbars=0, resizable=1, menubar=0, left=100, top=100, width=480, height=400, toolbar=0, status=0');
							e.preventDefault();
							return false;
						})
						.on('click', '.social_item[data-copy-link-url]', function(e) {
							var $self = jQuery(this),
								url = $self.data('copy-link-url');
							if ( url != '' ) {
								trx_addons_copy_to_clipboard( url );
								var msg = $self.data('message') ? $self.data('message') : TRX_ADDONS_STORAGE['msg_copied'];
								if ( msg ) {
									$self.attr('data-tooltip-text', msg);
									setTimeout( function() {
										$self.removeAttr('data-tooltip-text');
									}, 3000 );
								}
							}
							e.preventDefault();
							return false;
						});
				});
			}


			// Banners
			//-----------------------------------------------
			$banner_placeholder.each( function() {
				var item = jQuery(this);
				if ( item.data('banner-show') == 'permanent' ) {
					// Show banner on page load
					if ( ! item.hasClass( 'inited' ) ) {
						item.addClass( 'inited' );
						setTimeout( function() {
							item.after( item.data( 'banner' ) );
							var banner = item.next();
							item.remove();
							if ( banner.hasClass( 'banner_hidden' ) ) {
								trx_addons_when_images_loaded( banner, function() {
									banner.slideDown();
								} );
							}
						}, item.data('banner-delay') ? item.data('banner-delay') : 0 );
					}
				} else if ( item.data('banner-show') == 'scroll' ) {
					// Show banner on enter in the viewport
					trx_addons_intersection_observer_add( item, function( item, enter ) {
						if ( ! item.hasClass('inited') && enter ) {
							item.addClass('inited');
							trx_addons_intersection_observer_remove( item );
							setTimeout( function() {
								item.after( item.data( 'banner' ) );
								var banner = item.next();
								item.remove();
								if ( banner.hasClass( 'banner_hidden' ) ) {
									trx_addons_when_images_loaded( banner, function() {
										banner.slideDown();
									} );
								}
							}, item.data('banner-delay') ? item.data('banner-delay') : 0 );
						}
					} );
				}
			} );


			// Widgets decoration
			//----------------------------------------------
		
			// Decorate nested lists in widgets and side panels
			container.find('.widget ul > li').each(function() {
				var $self = jQuery(this);
				if ($self.find('ul').length > 0) {
					$self.addClass('has_children');
				}
			});
		
			// Archive widget decoration
			container.find('.widget_archive a:not(.inited)').each(function() {
				var $self = jQuery(this).addClass('inited');
				var val = $self.html().split(' ');
				if (val.length > 1) {
					val[val.length-1] = '<span>' + val[val.length-1] + '</span>';
					$self.html(val.join(' '));
				}
			});
		
		
			// Menu
			//----------------------------------------------
			// Prepare menus (if menu cache is used)
			jQuery('.sc_layouts_menu_nav:not(.inited_cache)').each(function() {
				var $self = jQuery(this).addClass('inited_cache');
				if ($self.find('.current-menu-item').length == 0 || $body.hasClass('blog_template')) {
					if (TRX_ADDONS_STORAGE['menu_cache'] === undefined) TRX_ADDONS_STORAGE['menu_cache'] = [];
					var id = $self.attr('id');
					if (id === undefined) {
						id = ('sc_layouts_menu_nav_' + Math.random()).replace('.', '');
						$self.attr('id', id);
					}
					TRX_ADDONS_STORAGE['menu_cache'].push('#'+id);
				}
			});
			if (TRX_ADDONS_STORAGE['menu_cache'] && TRX_ADDONS_STORAGE['menu_cache'].length > 0) {
				// Mark the current menu item and its parent items in the cached menus
				var href = window.location.href;
				if ( href.slice(-1) == '/' ) {
					href = href.slice( 0, -1 );
				}
				var href2 = href.indexOf( '#' ) == -1 && href.indexOf( '?' ) == -1 ? href + '/' : '';
				for (var i = 0; i < TRX_ADDONS_STORAGE['menu_cache'].length; i++) {
					var menu = jQuery( TRX_ADDONS_STORAGE['menu_cache'][i]+':not(.prepared)' );
					if ( menu.length === 0 ) {
						continue;
					}
					menu.addClass( 'prepared' );
					menu.find( 'li' ).removeClass( 'current-menu-ancestor current-menu-parent current-menu-item current_page_item' );
					menu.find( 'a[href="'+href+'"]' + ( href2 ? ',a[href="'+href2+'"]' : '' ) ).each( function( idx ) {
						var li = jQuery(this).parent();
						li.addClass( 'current-menu-item' );
						if ( li.hasClass( 'menu-item-object-page' ) ) {
							li.addClass('current_page_item');
						}
						// Mark all parent items as 'current-menu-ancestor' and closest as 'current-menu-parent'
						li = li.parents( 'li' );
						for ( var j = 0; j < li.length; j++ ) {
							li.addClass( 'current-menu-ancestor' + ( j == 0 ? ' current-menu-parent' : '' ) );
						}
					} );
				}
			}
		
		
			// Forms
			//------------------------------------
			
			// Remove 'error_field' class on any key pressed in the field
			jQuery("form:not([data-inited-validation])")
				.attr('data-inited-validation', 1)
				.on('change', 'input,select,textarea', function() {
					var $self = jQuery(this),
						$wrap = $self.parents('.error_field');
					if ( $self.val() !== '' ) {
						if ( $self.hasClass( 'error_field' ) ) {
							$self.removeClass('error_field');
						} else if ( $wrap.length > 0 ) {
							$wrap.removeClass('error_field');
						}
					}
				});
		
			// Comment form
			jQuery("form#commentform:not(.inited_validation)")
				.addClass( 'inited_validation' )
				.on( 'submit', function(e) {
					var rez = trx_addons_comments_validate( jQuery(this) );
					if (!rez) {
						e.preventDefault();
					}
					return rez;
				} );
			
			// Comments form
			function trx_addons_comments_validate(form) {
				form.find('input').removeClass('error_field');
				var comments_args = {
					error_message_text: TRX_ADDONS_STORAGE['msg_validation_error'],		// Global error message text (if don't write in checked field)
					error_message_show: true,											// Display or not error message
					error_message_time: 4000,											// Error message display time
					error_message_class: 'trx_addons_message_box trx_addons_message_box_error',	// Class appended to error message block
					error_fields_class: 'error_field',									// Class appended to error fields
					exit_after_first_error: false,										// Cancel validation and exit after first error
					rules: [
						{
							field: 'comment',
							min_length: { value: 1, message: TRX_ADDONS_STORAGE['msg_text_empty'] }
						}
					]
				};
				if (form.find('.comments_author input[aria-required="true"]').length > 0) {
					comments_args.rules.push(
						{
							field: 'author',
							min_length: { value: 1, message: TRX_ADDONS_STORAGE['msg_name_empty']},
							max_length: { value: 60, message: TRX_ADDONS_STORAGE['msg_name_long']}
						}
					);
				}
				if (form.find('.comments_email input[aria-required="true"]').length > 0) {
					comments_args.rules.push(
						{
							field: 'email',
							min_length: { value: 1, message: TRX_ADDONS_STORAGE['msg_email_empty']},
							max_length: { value: 60, message: TRX_ADDONS_STORAGE['msg_email_long']},
							mask: { value: TRX_ADDONS_STORAGE['email_mask'], message: TRX_ADDONS_STORAGE['msg_email_not_valid']}
						}
					);
				}
				var error = trx_addons_form_validate(form, comments_args);
				return !error;
			}


			// Show "on scroll" blocks
			//------------------------------------
			if ( $show_on_scroll.length > 0 ) {
				trx_addons_intersection_observer_add( $show_on_scroll );
			}


			// Animations
			//------------------------------------

			// Disable animations on mobile
			if ( TRX_ADDONS_STORAGE['disable_animation_on_mobile'] && $body.hasClass('ua_mobile') ) {
				// jQuery('[data-animation^="animated"]').removeAttr('data-animation');
				jQuery('[data-post-animation^="animated"]').removeAttr('data-post-animation');
				jQuery('[data-hover-animation^="animated"]').removeAttr('data-hover-animation');
				// Update global lists
				$animated_elements = jQuery('[data-post-animation^="animated"]:not(.animated)');
				$animated_hover    = jQuery('[data-hover-animation^="animated"]:not(.animated)');
			}

			// Animate elements after its enter to the viewport
			if ( $animated_elements.length > 0 ) {
				trx_addons_intersection_observer_add( $animated_elements, function( item, enter ) {
					if ( ! item.hasClass('inited_animation') && enter ) {
						item.addClass('inited_animation');
						trx_addons_intersection_observer_remove( item );
						var animation = item.data('post-animation');
						if ( ! animation ) animation = item.data('animation');
						setTimeout( function() {
							// Add class with animation
							item.addClass( animation );
							// Remove attribute 'data-post-animation' to prevent hiding items from 3rd-party plugins
							item.removeAttr('data-post-animation');
							// Update global list
							$animated_elements = jQuery('[data-post-animation^="animated"]:not(.animated)');
							// Generate action after element is animated
							$document.trigger('action.start_item_animation', [item]);
						}, 100 * trx_addons_random(0, 10) );
					}
				} );			
			}

			// Animated hover
			container
				.find('[data-hover-animation^="animated"]')
				.closest('.elementor-column,.post_layout_custom')
				.each(function() {
					var $self = jQuery(this);
					if ($self.hasClass('hover-animation-inited')) return;
					$self.addClass('hover-animation-inited').hover(
						// Mouse in
						function(e) {
							jQuery(this).find('[data-hover-animation^="animated"]').each(function() {
								var obj = jQuery(this);
								var animation = obj.data('hover-animation');
								var animation_in = obj.data('animation-in');
								if (animation_in == undefined) animation_in = "none";
								var animation_in_delay = obj.data('animation-in-delay');
								if (animation_in_delay == undefined) animation_in_delay = 0;
								var animation_out = obj.data('animation-out');
								if (animation_out == undefined) animation_out = "none";
								if (animation_in != 'none') {
									setTimeout(function() {
										obj.removeClass(animation + ' ' + animation_out);
										obj.addClass(animation + ' ' + animation_in);
									}, animation_in_delay);
								}
							});
						},
						// Mouse out
						function(e) {
							jQuery(this).find('[data-hover-animation^="animated"]').each(function() {
								var obj = jQuery(this);
								var animation = obj.data('hover-animation');
								var animation_in = obj.data('animation-in');
								if (animation_in == undefined) animation_in = "none";
								var animation_out = obj.data('animation-out');
								if (animation_out == undefined) animation_out = "none";
								var animation_out_delay = obj.data('animation-out-delay');
								if (animation_out_delay == undefined) animation_out_delay = 0;
								if (animation_out != 'none') {
									setTimeout(function() {
										obj.removeClass(animation + ' ' + animation_in);
										obj.addClass(animation + ' ' + animation_out);
									}, animation_out_delay);
								}
							});
						}
					);
				});


			// Init Parallax controller
			//-----------------------------------
			if ( typeof ScrollMagic != 'undefined' ) {
				if ( parallax_controller === null ) {
					parallax_controller = new ScrollMagic.Controller( {
																		globalSceneOptions: {
																							triggerHook: "onEnter",
																							duration: "200%"
																							}
																		} );
				}
				// Build Parallax scenes
				$parallax_wrap.each( function() {
					var $self = jQuery( this ),
						id = $self.attr( 'id' ),
						speed = $self.data( 'parallax' ) ? Number( $self.data( 'parallax' ) ) : 0;
					if ( speed !== 0 && ! $self.hasClass( 'parallax_inited' ) ) {
						$self.addClass( 'parallax_inited' );
						if ( ! id ) {
							id = 'sc_parallax_wrap_' + ( '' + Math.random() ).replace( '.', '' );
							$self.attr( 'id', id );
						}
						var selector = '#' + id + ( $self.find( '> .wp-caption' ).length > 0 ? '>.wp-caption' : '' ) + '>img';
						new ScrollMagic
							.Scene( { triggerElement: '#' + id } ) 
							.setTween( selector, { y: speed + "%", ease: Linear.easeNone } )
							.addTo( parallax_controller );
					}
				});
			}


			// Other settings
			//------------------------------------

			// Scroll to top button
			if ( ! $scroll_to_top.hasClass('inited') ) {
				$scroll_to_top
					.addClass('inited')
					.on( 'click', function(e) {
						jQuery('html,body').animate( {
							scrollTop: 0
						}, 'slow' );
						e.preventDefault();
						return false;
					} );
				// Scroll progress
				if ( $scroll_progress.length ) {
					$document.on( 'action.scroll_trx_addons', function() {
						var prc = trx_addons_document_height() > trx_addons_window_height() ? Math.min(100, Math.max(0, trx_addons_window_scroll_top() / ( trx_addons_document_height() - trx_addons_window_height() ) * 100)) : 100;
						if ( $scroll_progress.hasClass('trx_addons_scroll_progress_type_vertical') ) {
							$scroll_progress.height(prc+'%');
						} else if ( $scroll_progress.hasClass('trx_addons_scroll_progress_type_horizontal') ) {
							$scroll_progress.width(prc+'%');
						} else if ( $scroll_progress.hasClass('trx_addons_scroll_progress_type_box') || $scroll_progress.hasClass('trx_addons_scroll_progress_type_round') ) {
							var $bar = $scroll_progress.find('.trx_addons_scroll_progress_bar');
							if ( $bar.length === 0 ) {
								$scroll_progress.append( '<svg viewBox="0 0 50 50">'
															+ ( $scroll_progress.hasClass('trx_addons_scroll_progress_type_round')
																? '<circle class="trx_addons_scroll_progress_bar" cx="25" cy="25" r="22"></circle>'
																: '<rect class="trx_addons_scroll_progress_bar" x="3" y="3" width="44" height="44"></rect>'
																)
														+ '</svg>' );
								$bar = $scroll_progress.find('.trx_addons_scroll_progress_bar');
							}
							var bar_max = parseFloat( $bar.css('stroke-dasharray') );
							$bar.css( 'stroke-dashoffset', '' + Math.min( 1, 1 - prc / 100 ) * bar_max );
						}
					} );
				}
			}

		} // trx_addons_ready_actions


		// Init intersection observer
		//----------------------------------
		function trx_addons_intersection_observer_init() {

			if ( typeof TRX_ADDONS_STORAGE == 'undefined' ) return;

			if ( typeof IntersectionObserver != 'undefined' ) {
				// Create observer
				if ( typeof TRX_ADDONS_STORAGE['intersection_observer'] == 'undefined' ) {
					TRX_ADDONS_STORAGE['intersection_observer'] = new IntersectionObserver( function(entries) {
						entries.forEach( function( entry ) {
							trx_addons_intersection_observer_in_out( jQuery(entry.target), entry.isIntersecting || entry.intersectionRatio > 0 ? 'in' : 'out', entry );
						});
					}, {
						root: null,			// avoiding 'root' or setting it to 'null' sets it to default value: viewport
						rootMargin: '0px',	// increase (if positive) or decrease (if negative) root area
						threshold: 0		// 0.0 - 1.0: 0.0 - fired when top of the object enter in the viewport
											//            0.5 - fired when half of the object enter in the viewport
											//            1.0 - fired when the whole object enter in the viewport
					} );
				}
			} else {
				// Emulate IntersectionObserver behaviour
				$window.on( 'scroll', function() {
					if ( typeof TRX_ADDONS_STORAGE['intersection_observer_items'] != 'undefined' ) {
						for ( var i in TRX_ADDONS_STORAGE['intersection_observer_items'] ) {
							if ( ! TRX_ADDONS_STORAGE['intersection_observer_items'][i] || TRX_ADDONS_STORAGE['intersection_observer_items'][i].length === 0 ) {
								continue;
							}
							var item = TRX_ADDONS_STORAGE['intersection_observer_items'][i],
								item_top = item.offset().top,
								item_height = item.height();
							trx_addons_intersection_observer_in_out( item, item_top + item_height > trx_addons_window_scroll_top() && item_top < trx_addons_window_scroll_top() + trx_addons_window_height() ? 'in' : 'out' );
						}
					}
				} );
			}

			// Change state of the entry
			window.trx_addons_intersection_observer_in_out = function( item, state, entry ) {
				var callback = '';
				if ( state == 'in' ) {
					if ( ! item.hasClass( 'trx_addons_in_viewport' ) ) {
						item.addClass( 'trx_addons_in_viewport' );
						callback = item.data('trx-addons-intersection-callback');
						if ( callback ) {
							callback( item, true, entry );
						}
					}
				} else {
					if ( item.hasClass( 'trx_addons_in_viewport' ) ) {
						item.removeClass( 'trx_addons_in_viewport' );
						callback = item.data('trx-addons-intersection-callback');
						if ( callback ) {
							callback( item, false, entry );
						}
					}
				}
			};

			// Add elements to the observer
			window.trx_addons_intersection_observer_add = function( items, callback ) {
				items.each( function() {
					var $self = jQuery( this ),
						id = $self.attr( 'id' );
					if ( ! $self.hasClass( 'trx_addons_intersection_inited' ) ) {
						if ( ! id ) {
							id = 'io-' + ( '' + Math.random() ).replace('.', '');
							$self.attr( 'id', id );
						}
						$self.addClass( 'trx_addons_intersection_inited' );
						if ( callback ) {
							$self.data( 'trx-addons-intersection-callback', callback );
						}
						if ( typeof TRX_ADDONS_STORAGE['intersection_observer_items'] == 'undefined' ) {
							TRX_ADDONS_STORAGE['intersection_observer_items'] = {};
						}
						TRX_ADDONS_STORAGE['intersection_observer_items'][id] = $self;
						if ( typeof TRX_ADDONS_STORAGE['intersection_observer'] !== 'undefined' ) {
							TRX_ADDONS_STORAGE['intersection_observer'].observe( $self.get(0) );
						}
					}
				} );
			};

			// Remove elements from the observer
			window.trx_addons_intersection_observer_remove = function( items ) {
				items.each( function() {
					var $self = jQuery( this ),
						id = $self.attr( 'id' );
					if ( $self.hasClass( 'trx_addons_intersection_inited' ) ) {
						$self.removeClass( 'trx_addons_intersection_inited' );
						delete TRX_ADDONS_STORAGE['intersection_observer_items'][id];
						if ( typeof TRX_ADDONS_STORAGE['intersection_observer'] !== 'undefined' ) {
							TRX_ADDONS_STORAGE['intersection_observer'].unobserve( $self.get(0) );
						}
					}
				} );
			};
		}	// trx_addons_intersection_observer_init

		
		// Scroll actions
		//==============================================

		// Do actions when page scrolled
		window.trx_addons_scroll_actions = function() {
			// Add class to 'body' if scroll_top > 0
			if ( trx_addons_window_scroll_top() > 0 ) {
				if ( ! $body.hasClass( 'trx_addons_page_scrolled' ) ) {
					$body.addClass( 'trx_addons_page_scrolled' );
				}
			} else if ( $body.hasClass( 'trx_addons_page_scrolled' ) ) {
				$body.removeClass( 'trx_addons_page_scrolled' );
			}
			// Scroll to top button show/hide
			if ( $scroll_to_top.length > 0 ) {
				if ( trx_addons_window_scroll_top() > 100 ) {
					if ( ! $scroll_to_top.hasClass( 'show' ) ) {
						$scroll_to_top.addClass('show');
						$body.addClass( 'trx_addons_scroll_to_top_show' );
					}
				} else {
					if ( $scroll_to_top.hasClass( 'show' ) ) {
						$scroll_to_top.removeClass('show');
						$body.removeClass( 'trx_addons_scroll_to_top_show' );
					}
				}
			}

			// Display scroll progress
			if ( ['top', 'bottom', 'fixed'].indexOf( TRX_ADDONS_STORAGE['scroll_progress'] ) >= 0 ) {
				trx_addons_show_scroll_progress();
			}

			// Show "on scroll" blocks
			$show_on_scroll.each( function() {
				var item = jQuery(this);
				if ( item.hasClass( 'trx_addons_in_viewport' ) ){
					if ( item.offset().top < trx_addons_window_scroll_top() + trx_addons_window_height() * 0.75 ) {
						item.removeClass( 'trx_addons_show_on_scroll' ).addClass( 'trx_addons_showed_on_scroll' );
						trx_addons_intersection_observer_remove( item );
						// Update list of elements
						$show_on_scroll = jQuery('.trx_addons_show_on_scroll');
					}
				}
			} );

			// Call theme/plugins specific action (if exists)
			//----------------------------------------------
			$document.trigger('action.before_scroll_trx_addons');
			$document.trigger('action.scroll_trx_addons');
			$document.trigger('action.after_scroll_trx_addons');

			// Set flag about scroll actions are finished
			TRX_ADDONS_STORAGE['scroll_busy'] = false;
		};


		// Display scroll progress
		function trx_addons_show_scroll_progress() {
			if ( TRX_ADDONS_STORAGE['scroll_progress_status'] == undefined ) {
				$body.append('<div class="scroll_progress_wrap scroll_progress_'+TRX_ADDONS_STORAGE['scroll_progress']+'"><span class="scroll_progress_status"></span></div>');
				TRX_ADDONS_STORAGE['scroll_progress_status'] = jQuery( '.scroll_progress_wrap .scroll_progress_status' );
				trx_addons_get_scroll_posts();
				// Animate to relative page part on click
				TRX_ADDONS_STORAGE['scroll_progress_status'].on('click', function(e) {
					var prc = e.pageX / jQuery(this).parent().width();
					if ( TRX_ADDONS_STORAGE['scroll_posts'] != undefined && TRX_ADDONS_STORAGE['scroll_posts'].length > 0 ) {
						var cur_post = trx_addons_detect_current_scroll_post(),
							pt = cur_post.data('post-top'),
							ph = cur_post.data('post-height');
						trx_addons_document_animate_to( Math.round( ph * prc + pt - wh / 2 ) );
					} else {
						trx_addons_document_animate_to( Math.round( ( trx_addons_document_height() - trx_addons_window_height() ) * prc ) );
					}
					e.preventDefault();
					return false;
				});
			}
			var st = trx_addons_window_scroll_top(),
				wh = trx_addons_window_height(),
				new_width = '0%';
			if ( TRX_ADDONS_STORAGE['scroll_posts'] !== undefined && TRX_ADDONS_STORAGE['scroll_posts'].length > 0 ) {
				var cur_post = trx_addons_detect_current_scroll_post(),
					pt = cur_post.data('post-top'),
					ph = cur_post.data('post-height');
				new_width = ( st < 10 ? 0 : Math.min( 100, Math.round( ( st + wh / 2 - pt ) * 100 / ph ) ) ) + '%';
				TRX_ADDONS_STORAGE['scroll_progress_status'].width( new_width );
			} else {
				new_width = Math.min( 100, Math.round( st * 100 / ( trx_addons_document_height() - wh ) ) ) + '%';
				TRX_ADDONS_STORAGE['scroll_progress_status'].width( new_width );
			}
		}

		function trx_addons_detect_current_scroll_post() {
			var cur_post = false;
			TRX_ADDONS_STORAGE['scroll_posts'].each( function() {
				var post = jQuery(this),
					st   = trx_addons_window_scroll_top(),
					wh   = trx_addons_window_height(),
					pt   = post.data('post-top'),
					ph   = post.data('post-height');
				if ( pt < st + wh / 2 ) {
					cur_post = post;
				}
			});
			if ( ! cur_post ) {
				cur_post = TRX_ADDONS_STORAGE['scroll_posts'].eq( TRX_ADDONS_STORAGE['scroll_posts'].length - 1 );
			}
			return cur_post;
		}

		$document.on('action.new_post_added', trx_addons_get_scroll_posts);
		function trx_addons_get_scroll_posts() {
			TRX_ADDONS_STORAGE['scroll_posts'] = ( TRX_ADDONS_STORAGE['scroll_posts'] !== undefined && TRX_ADDONS_STORAGE['scroll_posts'].length > 0 )
													|| jQuery('.nav-links-single-scroll').length > 0
														? jQuery('.post_item_single')
														: false;
			trx_addons_get_scroll_posts_dimensions();
		}

		$document.on('action.resize_trx_addons', trx_addons_get_scroll_posts_dimensions);
		function trx_addons_get_scroll_posts_dimensions() {
			if ( TRX_ADDONS_STORAGE['scroll_posts'] !== undefined && TRX_ADDONS_STORAGE['scroll_posts'].length > 0 ) {
				TRX_ADDONS_STORAGE['scroll_posts'].each( function() {
					var post = jQuery(this);
					post.data('post-height', post.height())
						.data('post-top', post.offset().top);
				} );
			}
		}


		// Fix columns
		//---------------------------------------------------------------

		// Fix column only if css sticky behaviour is not used
		if ( ! $body.hasClass( 'fixed_blocks_sticky' ) ) {
			$document.on('action.resize_trx_addons', trx_addons_fix_column);
			$document.on('action.scroll_trx_addons', trx_addons_fix_column);

			var trx_addons_fix_column = function (e, cont) {

				if ( $fixed_columns.length === 0 ) {
					return;
				}

				var force = e.namespace == 'resize_trx_addons';

				$fixed_columns.each(function() {
					var col = jQuery(this),
						row = col.parent();
					
					// Exit if non-standard responsive is used for this columns
					if ( col.attr('class').indexOf('vc_col-lg-') != -1 || col.attr('class').indexOf('vc_col-md-') != -1 ) {
						return;

					// Unfix on mobile layout (all columns are fullwidth)
					} else if ( trx_addons_window_width() < TRX_ADDONS_STORAGE['mobile_breakpoint_fixedcolumns_off'] ) {
						var old_style = col.data('old_style');
						if (old_style !== undefined) {
							col.attr('style', old_style).removeAttr('data-old_style');
						}
				
					} else {
				
						var col_height = col.outerHeight();
						var row_height = row.outerHeight();
						var row_top = row.offset().top;
			
						// If column shorter then content and page scrolled below the content's top
						if (col_height < row_height && trx_addons_window_scroll_top() + trx_addons_fixed_rows_height() > row_top) {
							
							var col_init = {
								'position': 'undefined',
								'top': 'auto',
								'bottom' : 'auto'
							};
							
							if (typeof TRX_ADDONS_STORAGE['scroll_offset_last'] == 'undefined') {
								TRX_ADDONS_STORAGE['col_top_last'] = row_top;
								TRX_ADDONS_STORAGE['scroll_offset_last'] = trx_addons_window_scroll_top();
								TRX_ADDONS_STORAGE['scroll_dir_last'] = 1;
							}
							var scroll_dir = trx_addons_window_scroll_top() - TRX_ADDONS_STORAGE['scroll_offset_last'];
							scroll_dir = scroll_dir == 0
											? TRX_ADDONS_STORAGE['scroll_dir_last']
											: ( scroll_dir > 0 ? 1 : -1 );

							var col_big = col_height + 30 >= trx_addons_window_height() - trx_addons_fixed_rows_height(),
								col_top = col.offset().top;

							if (col_top < 0) {
								col_top = TRX_ADDONS_STORAGE['col_top_last'];
							}


							// If column height greater then window height
							if (col_big) {
			
								// If change scrolling dir
								if (scroll_dir != TRX_ADDONS_STORAGE['scroll_dir_last'] && col.css('position') == 'fixed') {
									col_init.top = col_top - row_top;
									col_init.position = 'absolute';
			
								// If scrolling down
								} else if (scroll_dir > 0) {
									if (trx_addons_window_scroll_top() + trx_addons_window_height() >= row_top + row_height + 30) {
										col_init.bottom = 0;
										col_init.position = 'absolute';
									} else if (trx_addons_window_scroll_top() + trx_addons_window_height() >= (col.css('position') == 'absolute' ? col_top : row_top) + col_height + 30) {
										col_init.bottom = 30;
										col_init.position = 'fixed';
									}
							
								// If scrolling up
								} else {
									if (trx_addons_window_scroll_top() + trx_addons_fixed_rows_height() <= col_top) {
										col_init.top = trx_addons_fixed_rows_height();
										col_init.position = 'fixed';
									}
								}
							
							// If column height less then window height
							} else {
								if (trx_addons_window_scroll_top() + trx_addons_fixed_rows_height() >= row_top + row_height - col_height) {
									col_init.bottom = 0;
									col_init.position = 'absolute';
								} else {
									col_init.top = trx_addons_fixed_rows_height();
									col_init.position = 'fixed';
								}
							}

							if (force && col_init.position == 'undefined' && col.css('position') == 'absolute') {
								col_init.position = 'absolute';
								if (col.css('top') != 'auto') {
									col_init.top = col.css('top');
								} else {
									col_init.bottom = col.css('bottom');
								}
							}

							if (col_init.position != 'undefined') {
								// Insert placeholder before this column
								var style = col.attr('style');
								if ( ! style ) style = '';
								if ( ! col.prev().hasClass('sc_column_fixed_placeholder') ) {
									col.css(col_init);
									TRX_ADDONS_STORAGE['scroll_dir_last'] = 0;
									col.before('<div class="sc_column_fixed_placeholder '+col.attr('class').replace('sc_column_fixed', '')+'"'
												+ (col.data('col') ? ' data-col="' + col.data('col') + '"' : '')
												+ '></div>');
								}
								// Detect horizontal position
								col_init.left = col_init.position == 'fixed' ? col.prev().offset().left : col.prev().position().left;
								col_init.width = col.prev().width() + parseFloat(col.prev().css('paddingLeft')) + parseFloat(col.prev().css('paddingRight'));
								// Set position
								if ( force 
									|| col.css('position') != col_init.position 
									|| TRX_ADDONS_STORAGE['scroll_dir_last'] != scroll_dir
									|| col.width() != col_init.width
								) {
									if (col.data('old_style') === undefined) {
										col.attr('data-old_style', style);
									}
									col.css(col_init);
								}
							}

							TRX_ADDONS_STORAGE['col_top_last'] = col_top;
							TRX_ADDONS_STORAGE['scroll_offset_last'] = trx_addons_window_scroll_top();
							TRX_ADDONS_STORAGE['scroll_dir_last'] = scroll_dir;
			
						} else {
			
							// Unfix when page scrolling to top
							var old_style = col.data('old_style');
							if (old_style !== undefined) {
								col.attr('style', old_style).removeAttr('data-old_style');
								if ( col.prev().hasClass('sc_column_fixed_placeholder') ) {
									col.prev().remove();
								}
							}
						}
					}
				});
			};
		}

		// Stack sections
		$document.on('action.resize_trx_addons', trx_addons_stack_section);
		$document.on('action.scroll_trx_addons', trx_addons_stack_section);
		function trx_addons_stack_section(e, cont) {

			if ( $stack_sections.length === 0 ) return;

			var force = e.namespace == 'resize_trx_addons',
				wso = trx_addons_window_scroll_top() + trx_addons_fixed_rows_height();

			$stack_sections.each( function( idx ) {

				var row = jQuery(this),
					row_holder = false,
					row_height = 0,
					row_top = 0,
					use_sticky = $body.hasClass( 'fixed_blocks_sticky' ) && row.hasClass('sc_stack_section_effect_slide');

				// Unfix on mobile layout (all columns are fullwidth)
				if ( trx_addons_window_width() < TRX_ADDONS_STORAGE['mobile_breakpoint_stacksections_off'] ) {

					if ( row.hasClass('sc_stack_section_fixed') ) {
						row.removeClass('sc_stack_section_fixed').prev().remove();
						if ( ! use_sticky ) row.css( { top: row.data('old-top') } );
					}
			
				} else {
			
					// If section is fixed
					if ( row.hasClass('sc_stack_section_fixed') ) {
						row_holder = row.prev();
						row_height = use_sticky ? row.outerHeight() : row_holder.outerHeight();
						row_top    = row_holder.offset().top;
						// Unfix row
						if ( row_top > wso ) {
							row.removeClass('sc_stack_section_fixed');
							if ( ! use_sticky ) {
								row.css( { top: row.data('old-top') } );
							}
							if ( row.hasClass( 'sc_stack_section_effect_fade' ) ) {
								row.css( { 'opacity': 0 } );
							}
							row_holder.remove();

						// Set holder height
						} else {
							if ( force ) {
								row_height = row.outerHeight();
								if ( ! use_sticky ) {
									row_holder.height(row_height);
									row.css( { top: trx_addons_fixed_rows_height() + 'px !important' } );
								}
							}
							if ( row.hasClass( 'sc_stack_section_effect_fade' ) ) {
								if ( wso - row_top <= row_height ) {
									row.css( { 'opacity': Math.max(0, Math.min( 1, ( wso - row_top ) / row_height ) ) } );
								} else {
									row.css( { 'opacity': 1 } );
								}
							}
						}

					// If section is not fixed
					} else {
						row_top = row.offset().top;
						if ( row_top <= wso ) {
							if ( ! use_sticky ) {
								row_height = row.outerHeight();
								row
									.data( 'old-top', row.css('top') )
									.css( { top: trx_addons_fixed_rows_height() + 'px'} );
							}
							row
								.before('<div class="sc_stack_section_placeholder"' + ( ! use_sticky ? ' style="height:'+row_height+'px;"' : '' ) + '></div>')
								.addClass('sc_stack_section_fixed');
						}
					}
				}

			} );
		}

		// Add zoom effect to the stack sections
		function trx_addons_stack_section_zoom() {
			$stack_sections.each( function() {
				var targetElement = jQuery(this);
				if ( ! targetElement.hasClass( 'sc_stack_section_zoom_on' ) ) {
					return;
				}
				var triggerElement = targetElement.next();
				if ( ! triggerElement.length || ( triggerElement.hasClass( 'sc_stack_section_on' ) && ! triggerElement.hasClass( 'sc_stack_section_effect_slide' ) ) ) {
					return;
				}
				//Register Scroll Trigger library
				if ( ! TRX_ADDONS_STORAGE['GSAP_Plugin_ScrollTrigger'] ) {
					TRX_ADDONS_STORAGE['GSAP_Plugin_ScrollTrigger'] = true;
					gsap.registerPlugin( ScrollTrigger );
				}
				var timeline = triggerElement.data( 'stack-section-timeline' ) ? triggerElement.data( 'stack-section-timeline' ) : null;
				// Reinit a timeline if already inited (for example, after sliders or masonry are inited - a page content shift down)
				if ( timeline ) {
					timeline.kill();
				}
				// Init a timeline
				timeline = gsap.timeline( {
					scrollTrigger: {
						trigger: triggerElement,
						start: "top 75%",
						end: "top top",
						scrub: 1
					}
				} );
				timeline.fromTo(
					targetElement,
					{
						scale: "1",
						duration: 1
					},
					{
						scale: "0.8",
						duration: 1
					}
				);
				triggerElement.data( 'stack-section-timeline', timeline );
			} );
		}
		if ( $stack_sections.length && window.gsap ) {
			trx_addons_stack_section_zoom();
			$document.on( 'action.resize_trx_addons', trx_addons_stack_section_zoom );
		}


		// Resize actions
		//==============================================

		// Do actions when window is resized
		window.trx_addons_resize_actions = function(cont) {

			// Check touch device
			if ( trx_addons_browser_is_touch() ) {
				if ( ! $body.hasClass( 'ua_touch' ) ) {
					$body.addClass( 'ua_touch' );
				}
			} else {
				if ( $body.hasClass( 'ua_touch' ) ) {
					$body.removeClass( 'ua_touch' );
				}
			}

			if (cont===undefined) cont = $body;

			// Call theme/plugins specific action (if exists)
			$document.trigger('action.before_resize_trx_addons', [cont] );
			$document.trigger('action.resize_trx_addons', [cont] );
			$document.trigger('action.after_resize_trx_addons', [cont] );
		};

		// Fit video frames to document width
		$document.on('action.resize_trx_addons', trx_addons_resize_video);
		function trx_addons_resize_video(e, cont) {
			// Resize tag 'video'
			if ( $video_tags.length > 0 ) {
				$video_tags.each(function() {
					var $self = jQuery(this),
						classes = $self.attr( 'class' );
					// If item now invisible
					if ( ( ! TRX_ADDONS_STORAGE['resize_tag_video'] && $self.parents('.mejs-mediaelement').length === 0 )
						|| $self.hasClass('trx_addons_noresize')
						|| classes.indexOf('_resize') > 0
						|| classes.indexOf('_noresize') > 0
						|| $self.parents('div:hidden,section:hidden,article:hidden').length > 0
					) {
						return;
					}
					var video = $self.addClass('trx_addons_resize').eq(0);
					var ratio = (video.data('ratio') !== undefined ? video.data('ratio').split(':') : [16,9]);
					ratio = ratio.length!=2 || ratio[0]==0 || ratio[1]==0 ? 16/9 : ratio[0]/ratio[1];
					var mejs_cont = video.parents('.mejs-video').eq(0);
					var mfp_cont  = video.parents( '.mfp-content' ).eq(0);
					var w_attr = video.data('width');
					var h_attr = video.data('height');
					if (!w_attr || !h_attr) {
						w_attr = video.attr('width');
						h_attr = video.attr('height');
						if ((!w_attr || !h_attr) && mejs_cont.length > 0) {
							w_attr = Math.ceil( mejs_cont.width() );
							h_attr = Math.ceil( mejs_cont.height() );
						}
						if (!w_attr || !h_attr) return;
						video.data({'width': w_attr, 'height': h_attr});
					}
					var percent = (''+w_attr).substr(-1) == '%';
					w_attr      = parseInt( w_attr, 10 );
					h_attr      = parseInt( h_attr, 10 );
					var w_real  = Math.ceil( mejs_cont.length > 0 
												? Math.min( percent ? 10000 : w_attr, mejs_cont.parents('div,article').eq(0).width() ) 
												: Math.min( percent ? 10000 : w_attr, video.parents('div,article').eq(0).width() ) 
										   );
					if ( mfp_cont.length > 0 ) {
						w_real  = Math.max( Math.ceil( mfp_cont.width() ), w_real );
					}
					var h_real  = Math.ceil( percent ? w_real/ratio : w_real/w_attr*h_attr );
					if ( parseInt( video.attr('data-last-width'), 10) == w_real ) {
						return;
					}
					if ( percent ) {
						video.height( h_real );
					} else if ( video.parents('.wp-video-playlist').length > 0 ) {
						if ( mejs_cont.length === 0 ) {
							video.attr({'width': w_real, 'height': h_real});
						}
					} else {
						video.attr({'width': w_real, 'height': h_real}).css({'width': w_real+'px', 'height': h_real+'px'});
						if (mejs_cont.length > 0) {
							trx_addons_set_mejs_player_dimensions(video, w_real, h_real);
						}
					}
					video.attr('data-last-width', w_real);
				});
			}

			// Resize tag 'iframe'
			if ( TRX_ADDONS_STORAGE['resize_tag_iframe'] && $iframe_tags.length > 0 ) {
				$iframe_tags.each(function() {
					var $self = jQuery(this);
					// If item now invisible
					if ($self.addClass('trx_addons_resize').parents('div:hidden,section:hidden,article:hidden').length > 0 || $self.hasClass('trx_addons_noresize')) {
						return;
					}
					var iframe = $self.eq(0),
						iframe_src = iframe.attr('src') ? iframe.attr('src') : iframe.data('src');
					if (iframe_src === undefined || iframe_src.indexOf('soundcloud') > 0) return;
					var w_attr = iframe.attr('width');
					var h_attr = iframe.attr('height');
					if ( ! w_attr || ! h_attr || w_attr <= trx_addons_apply_filters( 'trx_addons_filter_noresize_iframe_width', 325 ) ) {
						return;
					}
					var ratio = iframe.data('ratio') !== undefined 
									? iframe.data('ratio').split(':') 
									: ( iframe.parent().data('ratio') !== undefined 
										? iframe.parent().data('ratio').split(':') 
										: ( iframe.find('[data-ratio]').length>0 
											? iframe.find('[data-ratio]').data('ratio').split(':') 
											: [w_attr, h_attr]
											)
										);
					ratio      = ratio.length != 2 || ratio[0] === 0 || ratio[1] === 0 ? 16 / 9 : ratio[0] / ratio[1];
					var percent   = ( '' + w_attr ).slice(-1) == '%';
					w_attr        = parseInt( w_attr, 10 );
					h_attr        = parseInt( h_attr, 10 );
					var par       = iframe.parents('div,section').eq(0),
						contains   = iframe.data('contains-in-parent')=='1' || iframe.hasClass('contains-in-parent'),
						nostretch = iframe.data('no-stretch-to-parent')=='1' || iframe.hasClass('no-stretch-to-parent'),
						pw        = Math.ceil( par.width() ),
						ph        = Math.ceil( par.height() ),
						w_real    = nostretch ? Math.min( w_attr, pw ) : pw,
						h_real    = Math.ceil( percent ? w_real/ratio : w_real/w_attr*h_attr );
					if ( contains && par.css('position') == 'absolute' && h_real > ph ) {
						h_real = ph;
						w_real = Math.ceil( percent ? h_real*ratio : h_real*w_attr/h_attr );
					}
					if ( parseInt(iframe.attr('data-last-width'), 10) == w_real ) return;
					iframe.css({'width': w_real+'px', 'height': h_real+'px'});
					iframe.attr('data-last-width', w_real);
				});
			}
		}	// trx_addons_resize_video
		
		
		// Set Media Elements player dimensions
		function trx_addons_set_mejs_player_dimensions(video, w, h) {
			if (mejs) {
				for (var pl in mejs.players) {
					if (mejs.players[pl].media.src == video.attr('src')) {
						if (mejs.players[pl].media.setVideoSize) {
							mejs.players[pl].media.setVideoSize(w, h);
						} else if (mejs.players[pl].media.setSize) {
							mejs.players[pl].media.setSize(w, h);
						}
						mejs.players[pl].setPlayerSize(w, h);
						mejs.players[pl].setControlsSize();
					}
				}
			}
		}


		// Autoplay video from YouTube
		//---------------------------------
		var initAPI = false;
		var initEvents = false;
		var process = false;
		var players = [];
		var attrs = [];
		var oldAPI = window.onYouTubeIframeAPIReady;
		var YTdeferred = jQuery.Deferred();

		// Detect if element is in viewport
		jQuery.fn.isInViewport = function() {
			var $self = jQuery(this);
			var $panel = $self.data( 'sc-panel-thumb' );
			if ( ! $panel ) {
				$panel = $self.parents('.sc_panel_thumb');
				$self.data( 'sc-panel-thumb', $panel );
			}
			var rez = trx_addons_apply_filters( 'trx_addons_filter_element_in_viewport', $panel.length === 0 || $panel.hasClass('sc_panel_thumb_active'), $self );
			if ( rez ) {
				var elementTop = $self.offset().top;
				var elementBottom = elementTop + $self.outerHeight();
				var viewportTop = trx_addons_window_scroll_top();
				var viewportBottom = viewportTop + trx_addons_window_height();
				rez = elementTop >= viewportTop && elementTop <= viewportBottom
						||
					elementBottom >= viewportTop && elementBottom <= viewportBottom;
			}
			return rez;
		};

		function embedYoutubeAPI() {
			if ( ! initAPI ) {
				var tag = document.createElement('script');
				tag.src = 'https://www.youtube.com/iframe_api';
				var firstScriptTag = document.getElementsByTagName('script')[0];
				firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
				initAPI = true;
			}
		}

		window.onYouTubePlayerAPIReady = function() {
			if ( oldAPI && typeof oldAPI == 'function' ) oldAPI();
			YTdeferred.resolve(window.YT);
		};

		function initYoutubePlayer() {
			if (process) return;
			process = true;
			// Load Youtube API
			if ( $video_autoplay_yt.length ) {
				embedYoutubeAPI();
			}

			if ( ! initAPI ) {
				process = false;
				return;
			}

			if ( typeof YTdeferred != 'undefined' ) {
				YTdeferred.done( function(YT) {
					$video_autoplay_yt.each( function() {
						var $self = jQuery(this);
						if ( $self.parents('.sc_layouts_submenu:not(.layouts_inited):not(:visible)').length ) return;

						var $frame = $self.parents('.video_frame').eq(0),
							$wrap = $self.parents('.with_video_autoplay').eq(0),
							$sticky = $self.parents('.trx_addons_video_sticky_inner').eq(0),
							isInit = $wrap.hasClass('video_autoplay_inited'),
							isInView = $wrap.isInViewport(),
							id = ! isInit ? Math.random().toString(36).substr(2, 9) : $wrap.attr('data-uid');

						if ( ! isInit ) {
							$self.attr( 'id', id );
							$wrap
								.addClass('video_autoplay_inited')
								.attr('data-uid', id);
							var videoID = $wrap.data('video-id');
							if ( ! videoID ) {
								var src = ( $self.data('src') ? $self.data('src') : $self.attr('src') ).split('?');
								videoID = src[0].substring( src[0].indexOf('/embed/') + 7 );
								$wrap.data('video-id', videoID);
							}
							if ( ! videoID ) return;
							$frame.append(
								'<span class="video_frame_overlay"></span>'
								+ '<span class="video_frame_controls">'
									+ '<a class="video_frame_control_stop video_frame_link" href="https://youtube.com/watch?v='+videoID+'" target="_blank"></a>'
									+ '<span class="video_frame_control_volume video_frame_control_volume_mute"></span>'
									+ '<span class="video_frame_control_state video_frame_control_state_'
										+ ( $self.attr( 'allow' ) && $self.attr( 'allow' ).indexOf( 'autoplay' ) >= 0 ? 'pause' : 'play' )
									+ '"></span>'
								 + '</span>'
							);

							// Play/Pause video on enter/leave viewport
							trx_addons_intersection_observer_add( $wrap, function( item, enter ) {
								initYoutubePlayer();
							} );

							// Play/Pause video
							$frame.find('.video_frame_control_state').on('click', function() {
								var $self = jQuery(this);
								$self.toggleClass('video_frame_control_state_play video_frame_control_state_pause');
								if ( $self.hasClass('video_frame_control_state_play') ) {
									$self.removeClass('video_frame_control_state_upause');
									if ( typeof players[id].playVideo == 'function' ) players[id].playVideo();
								} else {
									$self.addClass('video_frame_control_state_upause');
									if ( typeof players[id].pauseVideo == 'function' ) players[id].pauseVideo();
								}
							} );

							// Stop video (on go to Youtube)
							$frame.find('.video_frame_control_stop').on('click', function() {
								var $self = jQuery(this);
								$self.siblings('.video_frame_control_state').removeClass('video_frame_control_state_play').addClass('video_frame_control_state_pause');
								$self.addClass('video_frame_control_state_upause');
								if ( typeof players[id].pauseVideo == 'function' ) players[id].pauseVideo();
							} );

							// Mute/Unmute video
							$frame.find('.video_frame_control_volume').on('click', function() {
								var $self = jQuery(this);
								$self.toggleClass('video_frame_control_volume_mute video_frame_control_volume_unmute');
								if ( $self.hasClass('video_frame_control_volume_unmute') ) {
									if ( typeof players[id].unMute == 'function' ) players[id].unMute();
								} else {
									if ( typeof players[id].mute == 'function' ) players[id].mute();
								}
							} );

							attrs[id] = {
								'videoId': videoID,
								'startSeconds': $self.data('video-start') || trx_addons_apply_filters( 'trx_addons_filter_youtube_autoplay_start_seconds', -1 ),
								'suggestedQuality': 'hd720'
							};
							if ( $self.data('video-end') ) {
								attrs[id]['endSeconds'] = $self.data('video-end');
							}
							players[id] = new YT.Player( this, {
								playerVars: {
									autoplay: 0,
									autohide: 1,
									modestbranding: 1,
									rel: 0,
									showinfo: 0,
									controls: 0,
									disablekb: 1,
									enablejsapi: 1,
									iv_load_policy: 3,
									playsinline: 1,
									loop: 1
								},
								events: {
									'onReady': function onReady(e) {
										//players[id].loadVideoById( attrs[id] );
										players[id].mute();
									},
									'onStateChange': function onStateChange(e) {
										if (e.data === 1) {
											$wrap.addClass('video_autoplay_started');
										} else if (e.data === 0) {
											if ( attrs[id].startSeconds >= 0 ) {
												players[id].seekTo(attrs[id].startSeconds);
											}
										}
									}
								}
							} );
							// Save a reference to the player object in the frame's data store
							// to allow 3rd party scripts to control the video programmatically
							$frame.data('video-player', players[id]);
						}
						// FadeIn video
						if ( isInit && isInView && ! $frame.hasClass('.video_frame_visible') ) {
							setTimeout( function() {
								$frame.fadeTo( 500, 1.0, function() {
									$frame.addClass('video_frame_visible');
								});
							}, trx_addons_apply_filters( 'trx_addons_filter_video_frame_timeout', 0 ) );
						}
						// Play/Pause on window scroll
						var control = $wrap.find('.video_frame_control_state');
						if ( isInit && typeof players[id].playVideo == 'function' && ! control.hasClass('video_frame_control_state_upause') && $sticky.length === 0 ) {
							if ( isInView && control.hasClass('video_frame_control_state_pause') ) {
								control.removeClass('video_frame_control_state_pause').addClass('video_frame_control_state_play');
								players[id].playVideo();
							}
							if ( ! isInView && control.hasClass('video_frame_control_state_play') ) {
								control.removeClass('video_frame_control_state_play').addClass('video_frame_control_state_pause');
								players[id].pauseVideo();
							}
						}
					} );

				} );
			}

			process = false;

		}	// initYoutubePlayer

		initYoutubePlayer();

		// Init hidden and loaded elements
		if ( ! initEvents ) {
			initEvents = true;
			$document.on( 'action.init_hidden_elements action.got_ajax_response action.after_show_submenu action.after_hide_submenu', trx_addons_debounce( function( e ) {
				initYoutubePlayer();
			}, 50 ) );
			$document.on( 'action.start_item_animation', function() {
				setTimeout( function() {
					initYoutubePlayer();
				}, 10 );
			} );
		}

		ready_busy = false;

	} );	// document.ready


	// Global functions
	//-------------------------------------

	// Paint arc on canvas with digits (used in the shortcodes and reviews)
	window.trx_addons_draw_arc_on_canvas = function(item, value) {
	
		var canvas = item.find('canvas');
		if (canvas.length === 0) return;
		
		var digits = canvas.next();
		var brd = parseInt(digits.css('border-top-width'), 10);
		var w = Math.ceil(digits.width()+2*brd);
	
		var needRepaint = false;
		if (canvas.attr('width') != w) {
			needRepaint = true;
			canvas.attr({
				'width': w,
				'height': w
			});
		}
	
		if (item.data('old-value') == value && !needRepaint) return;
		item.data('old-value', value);
		
		var percent = value * 100 / canvas.data('max-value');
		var angle = 360 * percent / 100;
		var Ar = angle * Math.PI / 180;
	
		var canvas_dom = canvas.get(0);
		var context = canvas_dom.getContext('2d');
		var r = (w - brd) / 2;
		var cx = w / 2;
		var cy = w / 2;
	
		context.beginPath();
		context.clearRect(0, 0, w, w);
		context.arc(cx, cy, r, 0, Ar, false);
		context.imageSmoothingEnabled= true;
		context.lineWidth = brd;
		context.strokeStyle = canvas.data('color');
		context.stroke();
	};

})();