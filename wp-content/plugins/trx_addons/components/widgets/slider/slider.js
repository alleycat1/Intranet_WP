/**
 * Init and resize sliders
 *
 * @package ThemeREX Addons
 * @since v1.1
 */

/* global jQuery, TRX_ADDONS_STORAGE */

(function() {

	"use strict";
	
	var $window = jQuery(window),
		$document = jQuery(document);

	var init_attempts = 0;

	// Call action after 1s after last call
	var init_hidden_elements_after_all_sliders_inited = trx_addons_throttle( function() {
															$document.trigger( 'action.init_hidden_elements', [jQuery('body')] );
														}, 300, true );

	// Init previously hidden sliders
	$document.on( 'action.init_hidden_elements', function(e, container) {
		// Init sliders in this container
		trx_addons_init_sliders(e, container);
		// Resize sliders (commented, because resize handler called after the slider is inited)
		//trx_addons_resize_sliders(e, container);
	} );

	// Return the data parameter 'slider-per-view' given the current size of the window
	function trx_addons_get_slides_per_view( $slider ) {
		var per_view = $slider.data( 'slides-per-view' ) || 1;
		var bp = $slider.data( 'slides-per-view-breakpoints' );
		if ( bp ) {
			var ww = trx_addons_window_width();
			for ( var max in bp ) {
				if ( ww <= max ) {
					per_view = bp[ max ];
					break;
				}
			}
		}
		return per_view;
	}

	// Init sliders with engine=swiper
	var attempts = 50;
	function trx_addons_init_sliders(e, container) {

		// Wait for the script 'swiper.js' is loaded
		if ( ! window.Swiper && attempts-- > 0 ) {
			setTimeout( function() {
				trx_addons_init_sliders(e, container);
			}, 100 );
		}
	
		if ( window.Swiper ) {

			// Create Swiper Controllers
			container.find( '.sc_slider_controller:not(.inited)' ).each( function () {
				var controller = jQuery(this).addClass('inited');
				if ( controller.find('.slider_style_controller').length > 0 ) return;
				var slider_id = controller.data('slider-id');
				if ( ! slider_id ) return;

				var controller_id = controller.attr('id');
				if (controller_id == undefined) {
					controller_id = 'sc_slider_controller_'+Math.random();
					controller_id = controller_id.replace('.', '');
					controller.attr('id', controller_id);
				}

				var slider_cont = jQuery('#'+slider_id+' .slider_container:not(.slider_controller_container)').eq(0);
				if ( ! slider_cont.attr('data-controller') ) {
					slider_cont.attr('data-controller', controller_id);
				}
				var controller_style = controller.data('style');
				var controller_effect = controller.data('effect');
				var controller_direction = controller.data('direction');
				var controller_interval = controller.data('interval');
// Moved to CSS var
//					var controller_height = controller.data('height');
				var controller_per_view = trx_addons_get_slides_per_view( controller );
				var controller_space = controller.data('slides-space');
				var controller_controls = controller.data('controls');

				var controller_html = '';

				slider_cont.find( '> .slider-wrapper > .swiper-slide' ).each( function ( idx ) {
					var slide = jQuery(this);
					var image = '';
					if ( controller_style.indexOf('thumbs') != -1 ) {
						if ( slide.data('image') ) {
							image = slide.data('image');
						} else if ( slide.find('.post_thumb_bg').length > 0 ) {
							image = slide.find('.post_thumb_bg').css('background-image').replace('url(', '').replace(')', '').replace(';', '').replace('"', '');
						} else if ( slide.css('background-image') && slide.css('background-image') != 'none' ) {
							image = slide.css('background-image').replace('url(', '').replace(')', '').replace(';', '').replace('"', '');
						} else if ( slide.find('img').length > 0 ) {
							image = slide.find('img').eq(0).attr('src');
						}
					}
					var title = controller_style.indexOf('titles') == -1
									? ''
									: ( slide.data('title')
											? slide.data('title')
											: slide.find('[class*="_item_title"]').text()
										);
					var cats = slide.data('cats');
					var date = slide.data('date');
					controller_html += trx_addons_apply_filters(
											'trx_addons_filter_slider_controller_slide_html',
											'<div class="slider-slide swiper-slide"'
												+ ' style="'
													+ (image !== undefined && image!=='' ? 'background-image: url('+image+');' : '')
													+ '"'
												+ '>'
												+ '<div class="sc_slider_controller_info">'
													+ '<span class="sc_slider_controller_info_number">'+(idx < 9 ? '0' : '')+(idx+1)+'</span>'
													+ '<span class="sc_slider_controller_info_title">'+(title ? title : 'Slide '+(idx+1))+'</span>'
												+ '</div>'
											+ '</div>',
											slide,
											controller
											);
				} );

				controller.html( trx_addons_apply_filters(
									'trx_addons_filter_slider_controller_html',
									'<div id="'+controller_id+'_outer"'
									+ ' class="slider_outer slider_swiper_outer slider_style_controller'
												+ ' slider_outer_' + (controller_controls == 1 ? 'controls slider_outer_controls_side' : 'nocontrols')
												+ ' slider_outer_nopagination'
												+ ' slider_outer_' + (controller_per_view==1 ? 'one' : 'multi')
												+ ' slider_outer_direction_' + (controller_direction=='vertical' ? 'vertical' : 'horizontal')
												+ '"'
									+ '>'
										+ '<div id="'+controller_id+'_swiper"'
											+' class="slider_container slider_controller_container slider_swiper swiper-slider-container'
													+ ' slider_' + (controller_controls == 1 ? 'controls slider_controls_side' : 'nocontrols')
													+ ' slider_nopagination'
													+ ' slider_notitles'
													+ ' slider_noresize'
													+ ' slider_' + (controller_per_view==1 ? 'one' : 'multi')
													+ ' slider_direction_' + (controller_direction=='vertical' ? 'vertical' : 'horizontal')
													+ '"'
											+ ' data-slides-min-width="' + trx_addons_apply_filters( 'trx_addons_filter_slider_controller_slide_width', 150 ) + '"'
											+ ' data-controlled-slider="'+slider_id+'"'
											+ ' data-direction="' + (controller_direction=='vertical' ? 'vertical' : 'horizontal') + '"'
											+ ' data-loop="1"'
											+ ' data-autoplay="' + ( controller_interval > 0 ? '1' : '0' ) + '"'
											+ (controller_effect !== undefined ? ' data-effect="' + controller_effect + '"' : '')
											+ (controller_interval !== undefined ? ' data-interval="' + controller_interval + '"' : '')
											+ (controller_per_view !== undefined ? ' data-slides-per-view="' + controller_per_view + '"' : '')
											+ (controller_space !== undefined ? ' data-slides-space="' + controller_space + '"' : '')
// Moved to CSS var
//												+ (controller_height !== undefined ? ' style="height:'+controller_height+'"' : '')
										+ '>'
											+ '<div class="slider-wrapper swiper-wrapper">'
												+ controller_html
											+ '</div>'
										+ '</div>'
										+ (controller_controls == 1
											? '<div class="slider_controls_wrap"><a class="slider_prev swiper-button-prev" href="#"></a><a class="slider_next swiper-button-next" href="#"></a></div>'
											: ''
											)
									+ '</div>',
									controller
								)
				);
			} );

			// Create Swiper Controls
			container.find( '.sc_slider_controls:not(.inited)' ).each( function () {
				var controls = jQuery(this).addClass('inited'),
					slider_id = controls.data('slider-id'),
					pagination_style = controls.data('pagination-style');
				if ( ! slider_id ) return;
				slider_id = jQuery('#'+slider_id+' .slider_swiper').attr('id');
				if ( ! slider_id ) return;
				controls.on('click', 'a', function(e) {
					var s = TRX_ADDONS_STORAGE['swipers'][slider_id];
					if (jQuery(this).hasClass('slider_next'))
						s.slideNext();
					else
						s.slidePrev();
					e.preventDefault();
					return false;
				});
				// Add pagination
				var s = typeof TRX_ADDONS_STORAGE['swipers'] != 'undefined' && typeof TRX_ADDONS_STORAGE['swipers'][slider_id] != 'undefined' ? TRX_ADDONS_STORAGE['swipers'][slider_id] : false,
					slides = jQuery('#'+slider_id+' .swiper-slide'),
					spv = s 
							? ( s.params.loop
									? s.loopedSlides
									: 0
								)
							: 0;
				var total = s 
							? ( s.params.loop
									? Math.ceil((s.slides.length - s.loopedSlides * 2) / s.params.slidesPerGroup)
									: s.snapGrid.length
								)
							: slides.length;
				var html = '';
				if ( pagination_style == 'thumbs' ) {
					slides.each(function(idx){
						if ( idx < spv || idx >= slides.length - spv ) return;
						var slide = jQuery(this);
						var image = slide.data('image');
						html += trx_addons_apply_filters(
									'trx_addons_filter_slider_controls_html_thumb',
									'<span class="slider_pagination_button_wrap swiper-pagination-button-wrap" style="width: ' + Math.round(100/total, 2) + '%;">'
									+ '<span class="slider_pagination_button swiper-pagination-button"'
											+ (image !== undefined ? ' style="background-image: url('+image+');"' : '')
									+ '></span>'
								+ '</span>',
								slide,
								controls
								);
					});
					controls.find('.slider_pagination_wrap').html(
						trx_addons_apply_filters( 'trx_addons_filter_slider_controls_html_thumbs', html, controls )
					);

				} else if ( pagination_style == 'fraction' ) {
					controls.find('.slider_pagination_wrap').html(
						trx_addons_apply_filters( 'trx_addons_filter_slider_controls_html_fraction',
							'<span class="slider_pagination_current swiper-pagination-current">1</span>'
							+ '/'
							+ '<span class="slider_pagination_total swiper-pagination-total">'+total+'</span>',
							controls
						)
					);

				} else if ( pagination_style == 'bullets' ) {
					slides.each(function(idx){
						if ( idx < spv || idx >= slides.length - spv ) return;
						html += trx_addons_apply_filters(
									'trx_addons_filter_slider_controls_html_bullet',
									'<span class="slider_pagination_bullet swiper-pagination-bullet" data-slide-number="'+(s ? jQuery(this).data('slide-number') : idx)+'"></span>',
									idx,
									controls
									);
					});
					controls.find('.slider_pagination_wrap').html(
						trx_addons_apply_filters( 'trx_addons_filter_slider_controls_html_bullets', html, controls )
					);
				}
				if ( pagination_style != 'none' ) {
					if (controls.find('.slider_progress_bar').length > 0) {
						var bar = controls.find('.slider_progress_bar');
						bar.parent().on('click', function(e) {
							var s = TRX_ADDONS_STORAGE['swipers'][slider_id];
							var total = s.params.loop ? Math.ceil((s.slides.length - s.loopedSlides * 2) / s.params.slidesPerGroup) : s.snapGrid.length;
							var slide_number = Math.max(0, Math.min(total-1, Math.floor(total * e.offsetX / jQuery(this).width())));
							var slide_idx = jQuery('#'+slider_id).find('[data-slide-number="'+slide_number+'"]:not(.swiper-slide-duplicate)').index();
							s.slideTo(slide_idx);
							e.preventDefault();
							return false;
						});
					} else {
						controls.find('.slider_pagination_button_wrap,.slider_pagination_bullet').on('click', function(e) {
							var s = TRX_ADDONS_STORAGE['swipers'][slider_id];
							var slide_idx = jQuery('#'+slider_id).find('[data-slide-number="'+jQuery(this).index()+'"]:not(.swiper-slide-duplicate)').index();
							s.slideTo(slide_idx);
							e.preventDefault();
							return false;
						});							
					}
					jQuery('#'+slider_id).on('slider_init slide_change_start', function(e) {
						if (TRX_ADDONS_STORAGE['swipers'][slider_id]) {
							var s = TRX_ADDONS_STORAGE['swipers'][slider_id];
							var current = jQuery(s.slides[s.activeIndex]).data('slide-number') + 1,
								total = s.params.loop ? Math.ceil((s.slides.length - s.loopedSlides * 2) / s.params.slidesPerGroup) : s.snapGrid.length;
							if (total > 0) {
								if (pagination_style == 'progressbar') {
									bar.width(Math.ceil(current/total*100)+'%');
								} else if (pagination_style == 'thumbs') {
									controls.find('.slider_pagination_button')
										.removeClass('slider_pagination_button_active')
										.eq(current-1)
										.addClass('slider_pagination_button_active');
								} else if (pagination_style == 'bullets') {
									controls.find('.slider_pagination_bullet')
										.removeClass('slider_pagination_bullet_active swiper-pagination-bullet-active')
										.eq(current-1)
										.addClass('slider_pagination_bullet_active swiper-pagination-bullet-active');
								} else if (pagination_style == 'fraction') {
									controls.find('.slider_pagination_current').text(current);
								}
							}
						}
					});
				}
			} );

			// Swiper Slider
			container.find( '.slider_swiper:not(.inited)' ).each( function () {

				var slider = jQuery(this);

				// If slider inside the invisible block - exit
				if ( slider.parents('div:hidden,article:hidden').length > 0 ) {
					return;
				}

				// Wait for all inner images are loaded
				var loaded = true;
				slider.find('img').each( function() {
					var $self = jQuery(this);
					if ( ! $self.get(0).complete && $self.attr('loading') != 'lazy' ) loaded = false;
				} );
				if ( ! loaded && init_attempts++ < 20 ) {
					setTimeout( function() {
						trx_addons_init_sliders(e, container);
					}, 100 );
					return;
				}

				// Check attr id for slider. If not exists - generate it
				var id = slider.attr('id');
				if (id == undefined) {
					id = 'swiper_'+Math.random();
					id = id.replace('.', '');
					slider.attr('id', id);
				}
				var cont = slider.parent().hasClass('slider_swiper_outer')
								? slider.parent().attr('id', id+'_outer')
								: slider;
				var cont_id = cont.attr('id');

				// Slave slider
				var slave_id = slider.data('slave-id') || '';
				if (slave_id != '') {
					var slave_slider = jQuery('#'+slave_id+' .slider_container:not(.slider_controller_container)').eq(0);
					if ( slave_slider.length ) {
						slider.attr( 'data-controlled-slider', slave_id );
						if ( ! slave_slider.attr('data-controller') ) {
							slave_slider.attr('data-controller', id);
						}
					} else {
						slave_id = '';
					}
				}
	
				// If this slider is controller for the other slider
				var is_controller = slider.parents('.sc_slider_controller').length > 0 || slave_id;
				// If this slider is controlled by other slider
				var controller_id = slider.data('controller');

				// Enum all slides
				slider.find('.swiper-slide').each( function(idx) {
					jQuery(this).attr('data-slide-number', idx);
				} );

				// Show slider, but make it invisible
				slider
					.css( {
						'display': 'block',
						'opacity': 0
					} )
					.addClass(id)
					.addClass('inited')
					.data('settings', {mode: 'horizontal'});		// VC hook

				// Slides effect
				var effect = slider.data('effect') ? slider.data('effect') : 'slide';

				// Loop slides
				var loop = slider.data('loop');
				if ( loop === undefined || isNaN( loop ) ) loop = 1;

				// Speed
				var speed = slider.data('speed');
				if ( speed === undefined || isNaN( speed ) ) speed = 600;

				// Free mode
				var free_mode = slider.data('free-mode');

				// Direction of slides change
				var direction = slider.data('direction');
				if (direction != 'vertical') direction = 'horizontal';

				// Min width of the slides in swiper (used for validate slides_per_view on small screen)
				var smw = slider.data('slides-min-width');
				if ( smw === undefined ) {
					smw = 150;
					slider.attr('data-slides-min-width', smw);
				}

				// Validate Slides per view on small screen
				var spv = trx_addons_get_slides_per_view( slider );
				if ( spv == undefined || slider.parents('.widget_nav_menu').length > 0 ) {
					spv = 1;
					slider.attr('data-slides-per-view', spv);
				}
				var width = slider.width();
				if ( width === 0 ) {
					width = slider.parent().width();
				}
				if ( direction == 'horizontal' ) {
					if ( width / spv < smw ) {
						spv = Math.max( 1, Math.floor( width / smw ) );
					}
				}

				// Space between slides
				var space = slider.data('slides-space');
				if ( space == undefined ) space = 0;

				// Parallax while slides change
				var slides_parallax = slider.data('slides-parallax');
				slides_parallax = effect == 'slide' && spv == 1 ? Math.max( 0, Math.min( 1, slides_parallax || 0 ) ) : 0;

				// Correct slider height if slider vertical with no resize
				if ( direction == 'vertical' && slider.hasClass('slider_height_auto') && slider.hasClass('slider_noresize') ) {
					var height = 0;
					slider.find('.swiper-slide').each( function(idx) {
						if ( idx >= spv ) return;
						height += jQuery(this).height() + ( idx > 0 ? space : 0 );
					} );
					if ( height > 0 ) slider.height( height );
				}

				// Autoplay interval
				var interval = slider.data('interval');
				if ( interval === undefined ) {
					interval = Math.round( 5000 * ( 1 + Math.random() ) );
				} else if ( isNaN( interval ) ) {
					interval = 0;
				} else {
					interval = parseInt( interval, 10 );
				}

				// Allow swipe guestures
				var noswipe = slider.hasClass('slider_noswipe')
								|| slider.parents('.slider_noswipe,.elementor-edit-mode').length > 0
								|| jQuery('body').hasClass('block-editor-page');

				// Slider in grid - fix width
				if (slider.parents('[class*="_grid_wrap"]').length > 0) {
					slider.css( {
						'max-width': width+'px'
					} );
				}

				if (TRX_ADDONS_STORAGE['swipers'] === undefined) {
					TRX_ADDONS_STORAGE['swipers'] = {};
				}

				TRX_ADDONS_STORAGE['swipers'][id] = new Swiper('.'+id, trx_addons_apply_filters( 'trx_addons_filter_slider_init_args', {
					freeMode: free_mode > 0,
					direction: direction,
					initialSlide: 0,
					speed: speed,
					loop: loop > 0 && slider.data('slides-overflow') != 1,			//!is_controller
					loopedSlides: spv,
					slidesPerView: spv,
					spaceBetween: space,
					centeredSlides: slider.data('slides-centered') == 1,		//is_controller,
					mousewheel: slider.data('mouse-wheel') == 1
									? {
										//sensitivity: slides_parallax > 0 ? 0.25 : 1,
										releaseOnEdges: true
										} 
									: false,
					grabCursor: ! is_controller && ! noswipe,
					slideToClickedSlide: is_controller,
					touchRatio: is_controller ? 0.2 : 1,
					autoHeight: false,		//!slider.hasClass('slider_height_fixed'),
					lazy: false,
					preloadImages: true,
					updateOnImagesReady: true,
					roundLengths: TRX_ADDONS_STORAGE['slider_round_lengths']	// To prevent blurry texts in Chrome (Firefox rendering texts fine with any value)
									&& ! is_controller							// Disable roundLengths for controller
									&& spv == 1,								// Disable roundLengths to prevent incorrect calcs when slidesPerView > 1
					effect: effect,
					//watchSlidesProgress: slides_parallax > 0,
					parallax: {
					 	enabled: slides_parallax > 0
					},
					swipeHandler: noswipe ? '.slider_controls_wrap,.slider_pagination_wrap' : null,
					//---Pagination (old way):
					//pagination: slider.hasClass('slider_pagination') ? '#'+cont_id+' .slider_pagination_wrap' : false,
					//paginationClickable: slider.hasClass('slider_pagination') ? '#'+cont_id+' .slider_pagination_wrap' : false,
					//paginationType: slider.hasClass('slider_pagination') && slider.data('pagination') ? slider.data('pagination') : 'bullets',
					//---Pagination (new way):
					pagination: {
						el: slider.hasClass('slider_pagination') ? '#'+cont_id+'>.slider_pagination_wrap,#'+cont_id+' > .slider_swiper > .slider_pagination_wrap' : null,
						clickable: slider.hasClass('slider_pagination') ? '#'+cont_id+' .slider_pagination_wrap' : false,
						type: slider.hasClass('slider_pagination') && slider.data('pagination') ? slider.data('pagination') : 'bullets',
						progressbarOpposite: slider.data('pagination') == 'progressbar'
												&& (
													( slider.data('direction')=='vertical' && ( slider.hasClass('slider_pagination_pos_bottom') || slider.hasClass('slider_pagination_pos_bottom_outside') ) )
													||
													( slider.data('direction')=='horizontal' && ( slider.hasClass('slider_pagination_pos_left') || slider.hasClass('slider_pagination_pos_right') ) )
													)
					},
					//---Navigation (old way):
					//nextButton: slider.hasClass('slider_controls') ? '#'+cont_id+' .slider_next' : false,
					//prevButton: slider.hasClass('slider_controls') ? '#'+cont_id+' .slider_prev' : false,
					//---Navigation (new way):
					navigation: {
						nextEl: slider.hasClass('slider_controls') ? '#'+id+'>.slider_controls_wrap>.slider_next,#'+id+'~.slider_controls_wrap>.slider_next' : null,
						prevEl: slider.hasClass('slider_controls') ? '#'+id+'>.slider_controls_wrap>.slider_prev,#'+id+'~.slider_controls_wrap>.slider_prev' : null
					},
					//---Autoplay (old way):
					//autoplay: slider.hasClass('slider_noautoplay') || interval==0 ? false : parseInt(interval, 10),
					//autoplayDisableOnInteraction: true,
					//---Autoplay (new way):
					autoplay: slider.hasClass('slider_noautoplay')
								|| (typeof slider.data('autoplay')!=='undefined' && slider.data('autoplay') == 0)
//									|| interval == 0
									? false 
									: {
										delay: interval,
										disableOnInteraction: true
										},
					//---Events (new way):
					on: {
						slideChangeTransitionStart: function () {
							var swiper = this,
								slide  = jQuery(swiper.slides[swiper.activeIndex]),
								slide_number = slide.data('slide-number');

							// Mark active custom pagination bullet
							cont.find('.swiper-pagination-custom > span')
								.removeClass('swiper-pagination-button-active')
								.eq(slide_number)
								.addClass('swiper-pagination-button-active');
							// Change outside title
							cont.find('.slider_titles_outside_wrap .active').removeClass('active').fadeOut();
							// Update controller or controlled slider
							var slaves = is_controller 
											? jQuery('#'+slider.data('controlled-slider'))
											: jQuery('[data-slider-id="'+id.replace('_sc_', '_')+'"]');
							if ( slaves.length === 0 && ! is_controller ) {
								slaves = jQuery('[data-slider-id="'+id.replace('_sc_', '_').replace('_swiper', '')+'"]');
								// Compatibility with sliders from shortcodes Blogger, Properties, Cars, etc.
								if ( slaves.length === 0 ) {
									slaves = jQuery('[data-slider-id="'+id.replace('_sc_slider_swiper', '')+'"]');
								}
							}
							if ( slaves.length > 0 ) {
								slaves.each( function() {
									var controlled_slider = jQuery(this).hasClass('slider_swiper') ? jQuery(this) : jQuery(this).find( '.slider_swiper' ).eq(0);
									var controlled_id = controlled_slider.attr('id');
									if ( controlled_id && TRX_ADDONS_STORAGE['swipers'][controlled_id] && jQuery('#'+controlled_id).attr('data-busy') != 1 ) {
										slider.attr('data-busy', 1);
										setTimeout( function() {
											slider.attr('data-busy', 0);
										}, 300 );
										var slide_number = jQuery(swiper.slides[swiper.activeIndex]).data('slide-number');
										var slide_idx = controlled_slider.find('[data-slide-number="'+slide_number+'"]:not(.swiper-slide-duplicate)').index();
										TRX_ADDONS_STORAGE['swipers'][controlled_id].slideTo(slide_idx);
									}
								});
							}
							slider.trigger('slide_change_start', [slider]);
						},
						slideChangeTransitionEnd: function () {
							var swiper = this,
								slide  = jQuery(swiper.slides[swiper.activeIndex]),
								slide_prev   = jQuery(swiper.slides[swiper.lastOpenedIndex !== undefined ? swiper.lastOpenedIndex : swiper.previousIndex]),
								slide_number = slide.data('slide-number');
							swiper.lastOpenedIndex = swiper.activeIndex;
							// Change outside title
							var titles = cont.find('.slider_titles_outside_wrap .slide_info');
							if (titles.length > 0) {
								//titles.eq((swiper.activeIndex-1)%titles.length).addClass('active').fadeIn();
								titles.eq(slide_number).addClass('active').fadeIn(300);
							}
							// Remove video (if autoplay is not active)
							var video = slide_prev.find('.trx_addons_video_player:not(.with_video_autoplay)');
							if ( video.length > 0 ) {
								if ( video.hasClass('with_cover') ) {
									if ( video.hasClass('video_play') ) {
										video.removeClass('video_play').find('.video_embed').empty();
									}
								} else {
									var embed = video.find('.video_embed'),
										html  = embed.html();
									embed.empty().html( html );
								}
							}
							// Unlock slider/controller
							slider.attr('data-busy', 0);
							slider.trigger('slide_change_end', [slider]);
						},
						touchStart: function() {
							slider.trigger( 'swiper_touch_start' );
						},
						touchEnd: function() {
							slider.trigger( 'swiper_touch_end' );
						},
/*
						progress: function() {
							if ( slides_parallax > 0 ) {
								var swiper = this;
								var axis = direction == 'vertical' ? 'Y' : 'X';
								var k = slides_parallax;
								for ( var i = 0; i < swiper.slides.length; i++ ) {
									var offset = swiper.slides[i].progress * k * ( direction == 'vertical' ? swiper.height : swiper.width );
									swiper.slides[i].querySelector('.slide_parallax_wrapper').style.transform = "translate" + axis + "(" + offset + "px)";
								}
							}
						},
						setTransition: function(e) {
							if ( slides_parallax > 0 ) {
								var swiper = this;
								for (var i = 0; i < swiper.slides.length; i++ ) {
									swiper.slides[i].style.transition = e + "ms";
									swiper.slides[i].querySelector( '.slide_parallax_wrapper' ).style.transition = e + "ms";
								}
							}
						}
*/
					}
				}, slider ) );

				// Sticky slider with mouse wheel - allow wheel only in 'sticky' position
				if ( slider.data('mouse-wheel') == 1 ) {
					var slider_sticky_wrapper = slider.parents( '.elementor-sticky' );
					if ( slider_sticky_wrapper.length ) {
						TRX_ADDONS_STORAGE['swipers'][id].mousewheel.disable();
						var init_sticky_observer = function() {
							var slider_sticky_wrapper_top = slider_sticky_wrapper.css( 'top' ) || '0px',
							custom_top = Math.abs( parseFloat( slider_sticky_wrapper_top ) - trx_addons_fixed_rows_height() ) > 1;
							trx_addons_sticky_observer_remove( id );
							trx_addons_sticky_observer_create( id, slider_sticky_wrapper, function( entry, is_sticky ) {
								if ( is_sticky ) {
									TRX_ADDONS_STORAGE['swipers'][id].mousewheel.enable();
								} else {
									TRX_ADDONS_STORAGE['swipers'][id].mousewheel.disable();
								}
							}, {
								rootMargin: ( - ( custom_top ? parseFloat( slider_sticky_wrapper_top ) : trx_addons_fixed_rows_height() ) ) + 'px 0px 0px 0px'
							} );
						};
						init_sticky_observer();
						$document.on( 'action.resize_trx_addons action.sc_layouts_row_fixed_on action.sc_layouts_row_fixed_off', trx_addons_debounce( init_sticky_observer, 500 ) );
					}
				}

				slider.trigger('slider_init', [slider]);

				// Custom pagination
				cont.find('.swiper-pagination-custom').on('click', '>span', function(e) {
					jQuery(this).siblings().removeClass('swiper-pagination-button-active');
					var t = jQuery(this).addClass('swiper-pagination-button-active').index() * TRX_ADDONS_STORAGE['swipers'][id].params.slidesPerGroup;
					TRX_ADDONS_STORAGE['swipers'][id].params.loop && (t += TRX_ADDONS_STORAGE['swipers'][id].loopedSlides);
					TRX_ADDONS_STORAGE['swipers'][id].slideTo(t);
					e.preventDefault();
					return false;
				});

				// Activate first title in the outside block
				cont.find('.slider_titles_outside_wrap .slide_info').eq(0).addClass('active').fadeIn(300);

				slider.attr('data-busy', 1).animate({'opacity':1}, 'fast', function() {
					slider.attr('data-busy', 0); 
					trx_addons_set_controller_height( is_controller ? slider.parents('[data-slider-id]').eq(0).attr('id') : controller_id,
													  is_controller ? jQuery('#'+slider.parents('[data-slider-id]').eq(0).data('slider-id')) : slider
													);
					slider.trigger('slider_inited', [slider]);
					$document.trigger('action.slider_inited', [slider, id]);
				});

				// Generate 'action.resize_trx_addons' event after the slider is showed
				init_hidden_elements_after_all_sliders_inited();

				// Generate 'action.resize_trx_addons' event after the slider is showed
				$document.trigger('action.resize_trx_addons');

				// Generate 'scroll' event after the slider is showed
				$window.trigger('scroll');

			} );
		}
				
	
		// ElastiStack Slider
		if ( window.ElastiStack ) {
			container.find('.slider_elastistack:not(.inited)').each(function () {

				// If slider inside the invisible block - exit
				if (jQuery(this).parents('div:hidden,article:hidden').length > 0 || typeof window.ElastiStack == 'undefined')
					return;
				
				// Check attr id for slider. If not exists - generate it
				var slider = jQuery(this);
				var id = slider.attr('id');
				if (id == undefined) {
					id = 'elastistack_'+Math.random();
					id = id.replace('.', '');
					slider.attr('id', id);
				}
				var cont = slider.parent().hasClass('slider_outer') ? slider.parent().attr('id', id+'_outer') : slider;
				var cont_id = cont.attr('id');
				var images = slider.find('ul.stack__images').attr('id', id+'_images');
				var images_id = images.attr('id');
				
				slider.css({
					'display': 'block',
					'opacity': 0
					})
					.addClass(id)
					.addClass('inited')
					.data('settings', {mode: 'horizontal'});		// VC hook
				
				// Set height for images container before init to make stack
				trx_addons_resize_sliders(e, cont);
				
				var stack = new ElastiStack( images.get(0), {
						onUpdateStack : function(idx) {
							// Change outside title
							var titles = cont.find('.slider_titles_outside_wrap');
							if (titles.length > 0) {
								titles.find('.active').removeClass('active').hide();
								titles.find('.slide_info').eq(idx).addClass('active').fadeIn(300);
							}
							// Remove video
							cont.find('.trx_addons_video_player.with_cover.video_play').removeClass('video_play').find('.video_embed').empty();
							slider.trigger('slide_change_end', [slider]);
						}
					});

				// Next button
				cont.find('.slider_next').on('click', function(e) {
					stack.nextItem( { transform : 'translate3d(0, -60px, 400px)' } );
					e.preventDefault();
					return false;
				} );
				
				// Activate first title in the outside block
				cont.find('.slider_titles_outside_wrap .slide_info').eq(0).addClass('active').fadeIn(300);

				// Show slider
				slider.animate({'opacity':1}, 'fast', function() {
					stack._setStackStyle();
				} );
			} );
		}
	}
	
	// Sliders: Resize
	$document.on('action.resize_trx_addons', trx_addons_resize_sliders);
	function trx_addons_resize_sliders(e, container) {
		if (container === undefined) {
			container = jQuery('body');
		}
		container.find('.slider_container.inited').each(function() {
			var slider = jQuery(this);
			if (slider.parents('div:hidden,article:hidden').length > 0) return;
			var id = slider.attr('id');
			var direction = slider.data('direction');
			if (direction != 'vertical') direction = 'horizontal';
			var on_resize = false;	// Need to call onResize handlers
			//var max_width = jQuery('.content').length = 1 ? jQuery('.content').width() : jQuery('body').width();
			var max_width = slider.closest(
								'.post_featured'
								+ ',' + '.swiper-slide'
								+ ',' + '.elementor-widget'
								+ ',' + '.vc_column-inner'
								+ ',' + trx_addons_apply_filters( 'trx_addons_filter_content_class', '.content', 'slider-resize' )
								+ ',' + trx_addons_apply_filters( 'trx_addons_filter_page_wrap_class', TRX_ADDONS_STORAGE['page_wrap_class'] ? TRX_ADDONS_STORAGE['page_wrap_class'] : '.page_wrap', 'slider-resize' )
								+ ',' + 'body'
								).width();	//jQuery('body').width();
			var slider_width = slider.width();
			if (slider_width > max_width) {
				slider_width = Math.min( slider_width, max_width );
				slider.width( slider_width );
				on_resize = true;
			}
			var last_width = slider.data('last-width');
			if (isNaN(last_width)) {
				last_width = 0;
			}
			if (last_width === 0 || last_width != slider_width) {
				if (direction != 'vertical') slider.data('last-width', slider_width);
				// Detect space between slides
				var space = slider.data('slides-space');
				if (space == undefined) {
					space = 0;
				}
				if ( slider.hasClass('slider_swiper')
					&& typeof TRX_ADDONS_STORAGE['swipers'] != 'undefined'
					&& typeof TRX_ADDONS_STORAGE['swipers'][id] == 'object'
					&& typeof TRX_ADDONS_STORAGE['swipers'][id].params == 'object'
					&& typeof TRX_ADDONS_STORAGE['swipers'][id].params.spaceBetween != 'undefined'
				) {
					var gap = space;
					// Detect grid space on the small screen
					if ( trx_addons_window_width() < 1440 ) {
						var grid_element = jQuery(
													'.elementor-column-gap-extended > .elementor-row > .elementor-column > .elementor-element-populated,'	// Elm 2.9-
													+ '.elementor-column-gap-extended > .elementor-column > .elementor-element-populated'					// Elm 3.0+
												 ).eq(0),
							grid_gap = grid_element.length ? parseInt( grid_element.css( 'padding-left' ), 10 ) : 0;
						if ( grid_gap >= 10 ) {
							gap = grid_gap * 2;
						}
					}
					if ( gap > 0 && TRX_ADDONS_STORAGE['swipers'][id].params.spaceBetween > gap ) {
						TRX_ADDONS_STORAGE['swipers'][id].params.spaceBetween = gap;
						space = gap;
					}
				}
				// Change slides_per_view
				var spv = trx_addons_get_slides_per_view( slider );
				if (spv == undefined || slider.parents('.widget_nav_menu').length > 0) {
					spv = 1;
				}
				if ( slider.hasClass('slider_swiper')
					&& typeof TRX_ADDONS_STORAGE['swipers'] != 'undefined'
					&& typeof TRX_ADDONS_STORAGE['swipers'][id] == 'object'
					&& typeof TRX_ADDONS_STORAGE['swipers'][id].params == 'object'
					&& typeof TRX_ADDONS_STORAGE['swipers'][id].params.slidesPerView != 'undefined'
				) {
					if (TRX_ADDONS_STORAGE['swipers'][id].params.slidesPerView != 'auto') {
						if (direction=='horizontal') {
							var smw = slider.data('slides-min-width');
							if (slider_width / spv < smw) {
								spv = Math.max(1, Math.floor(slider_width / smw));
							}
							if (TRX_ADDONS_STORAGE['swipers'][id].params.slidesPerView != spv) {
								TRX_ADDONS_STORAGE['swipers'][id].params.slidesPerView = spv;
								TRX_ADDONS_STORAGE['swipers'][id].params.loopedSlides = spv;
								//TRX_ADDONS_STORAGE['swipers'][id].reInit();
							}
						}
						on_resize = true;
					}
				}
				// Change slider height
				if ( ! slider.hasClass('slider_noresize') || slider.height()===0 ) {
					var slider_height = slider.height();
					var slide = slider.find('.slider-slide').eq(0);

					// Old way: Swiper core script recalc slide dimensions before this function
					// var slide_width = slide.width();
					// var slide_height = slide.height();

					// New way: Swiper core script now recalc slide dimensions after this function
					//          We must calc slide dimensions manually
					var slide_width = direction == 'horizontal'
										? (slider_width - (spv-1) * space ) / spv
										: slider_width;
					var slide_height = direction == 'vertical'
										? (slider_height - (spv-1) * space ) / spv
										: slider_height;
					var ratio = slider.data('ratio');
					if ( ratio === undefined || (''+ratio).indexOf(':') < 1 ) {
						ratio = slide_height > 0 ? slide_width+':'+slide_height : "16:9";
						slider.attr('data-ratio', ratio);
					}
					ratio = ratio.split(':');
					var ratio_x = !isNaN(ratio[0]) ? Number(ratio[0]) : 16;
					var ratio_y = !isNaN(ratio[1]) ? Number(ratio[1]) : 9;

					var height = Math.floor( ( spv == 1 ? slider_width : slide_width ) / ratio_x * ratio_y);
					slider.height( direction == 'vertical' ? height * spv + (spv-1) * space : height);
					on_resize = true;
					if (slider.hasClass('slider_elastistack')) {
						slider.find('.slider-wrapper,.stack__images,.slider-slide').height(height);
					}
					// Change controller height
					trx_addons_set_controller_height(slider.data('controller'), slider, e);
				}
				// Call onResize handlers
				if (on_resize && (slider.hasClass('slider_swiper') || slider.hasClass('slider_swiper_outer'))) {
					if (   typeof TRX_ADDONS_STORAGE['swipers'] != 'undefined'
						&& typeof TRX_ADDONS_STORAGE['swipers'][id] == 'object'
						&& typeof TRX_ADDONS_STORAGE['swipers'][id].resize == 'object'
						&& typeof TRX_ADDONS_STORAGE['swipers'][id].resize.resizeHandler == 'function'
					) {
						TRX_ADDONS_STORAGE['swipers'][id].resize.resizeHandler(e);
					}
				}
			}
		});
	}

	// Set controller height
	function trx_addons_set_controller_height(controller_id, slider, e) {
		if ( !controller_id && typeof TRX_ADDONS_STORAGE['pagebuilder_preview_mode'] != 'undefined' && TRX_ADDONS_STORAGE['pagebuilder_preview_mode'] ) {
			var slider_id = slider.attr('id').replace('_sc_slider', '');
			if (slider_id) {
				controller_id = jQuery('[data-slider-id="' + slider_id + '"]').eq(0).attr('id');
			}
		}
		if ( !controller_id ) return;
		var controller = jQuery('#'+controller_id);
		if (controller.length > 0 
			&& controller.hasClass('sc_slider_controller_vertical') 
			&& controller.hasClass('sc_slider_controller_height_auto')
		) {
			var controller_slider = controller.hasClass('slider_container') ? controller : controller.find('.slider_container'),
				controller_slider_id = controller_slider.attr('id');
			var paddings = parseFloat(controller.css('paddingTop'));
			if (isNaN(paddings)) paddings = 0;
			var controller_spv = trx_addons_get_slides_per_view( controller );
			if (isNaN(controller_spv)) controller_spv = 1;
			controller_slider.height( Math.max(
											( slider.parent().hasClass('slider_outer') ? slider.parent().outerHeight() : slider.height() ) - 2 * paddings,
											controller_spv * trx_addons_apply_filters( 'trx_addons_filter_slider_controller_min_height', 80 )
											) );
			// Call onResize handlers
			if ((controller_slider.hasClass('slider_swiper') || controller_slider.hasClass('slider_swiper_outer'))) {
				if (typeof TRX_ADDONS_STORAGE['swipers'][controller_slider_id] == 'object' && typeof TRX_ADDONS_STORAGE['swipers'][controller_slider_id].resize == 'object' && typeof TRX_ADDONS_STORAGE['swipers'][controller_slider_id].resize.resizeHandler == 'function') {
					TRX_ADDONS_STORAGE['swipers'][controller_slider_id].resize.resizeHandler(e);
				}
			}
		}
	}




	/*
	 * Add a new effects to the Swiper
	 */
	$document.on( 'action.ready_trx_addons', function(e) {

		if ( ! window.Swiper || typeof Swiper.use != 'function' ) {
			return;
		}

		// Utility functions to add new effects to the Swiper
		//------------------------------------------------------------
		const $ = Swiper.$;

		// Create shadow elements
		function createShadow( params, $slideEl, side ) {
			const shadowClass = `swiper-slide-shadow${side ? `-${side}` : ''}`;
			const $shadowContainer = params.transformEl ? $slideEl.find( params.transformEl ) : $slideEl;
			let $shadowEl = $shadowContainer.children(`.${shadowClass}`);
		
			if ( ! $shadowEl.length ) {
				$shadowEl = $(`<div class="swiper-slide-shadow${side ? `-${side}` : ''}"></div>`);
				$shadowContainer.append($shadowEl);
			}

			return $shadowEl;
		}
		
		// Add/init a new effect
		function effectInit( params ) {

			const {
				effect,
				swiper,
				on,
				setTranslate,
				setTransition,
				overwriteParams,
				perspective,
				recreateShadows,
				getEffectParams
			} = params;

			on( 'beforeInit', () => {

				if ( swiper.params.effect !== effect ) {
					return;
				}

				swiper.classNames.push( `${swiper.params.containerModifierClass}${effect}` );

				if ( perspective && perspective() ) {
					swiper.classNames.push( `${swiper.params.containerModifierClass}3d` );
				}

				const overwriteParamsResult = overwriteParams ? overwriteParams() : {};
				Object.assign( swiper.params, overwriteParamsResult );
				Object.assign( swiper.originalParams, overwriteParamsResult );
			} );

			on( 'setTranslate', () => {
				if ( swiper.params.effect !== effect ) {
					return;
				}
				setTranslate();
			} );

			on( 'setTransition', ( _s, duration ) => {
				if ( swiper.params.effect !== effect ) {
					return;
				}
				setTransition( duration );
			} );

			on( 'transitionEnd', () => {
				if ( swiper.params.effect !== effect ) {
					return;
				}

				if ( recreateShadows ) {
					if ( ! getEffectParams || ! getEffectParams().slideShadows ) {
						return;
					}
					swiper.slides.each( slideEl => {
						const $slideEl = swiper.$( slideEl );
						$slideEl.find( '.swiper-slide-shadow-top, .swiper-slide-shadow-right, .swiper-slide-shadow-bottom, .swiper-slide-shadow-left' ).remove();
					} );
					recreateShadows();
				}
			} );

			let requireUpdateOnVirtual,
				requestAnimationFrame = trx_addons_request_animation_frame();

			on( 'virtualUpdate', () => {
				if ( swiper.params.effect !== effect ) {
					return;
				}
				if ( ! swiper.slides.length ) {
					requireUpdateOnVirtual = true;
				}
				requestAnimationFrame( () => {
					if ( requireUpdateOnVirtual && swiper.slides && swiper.slides.length ) {
						setTranslate();
						requireUpdateOnVirtual = false;
					}
				} );
			} );
		}

		// Add effect 'Swap'
		//------------------------------------------------------
		if ( trx_addons_apply_filters( 'trx_addons_filter_add_effect_to_swiper', true, 'swap' ) ) {

			// Common modules for effect 'Swap'
			const effectSwapModules = {
				setTranslate: function setTranslate( swiper ) {
					var swiperWidth = swiper.width;
					var swiperHeight = swiper.height;
					var $wrapperEl = swiper.$wrapperEl;
					var slides = swiper.slides;
					var slidesSizesGrid = swiper.slidesSizesGrid;
					var params = swiper.params.swapEffect;
					var isHorizontal = swiper.isHorizontal();
					var transform = swiper.translate;
					var center = isHorizontal ? -transform + ( swiperWidth / 2 ) : -transform + ( swiperHeight / 2 );
					var rotate = isHorizontal ? params.rotate : -params.rotate;
					var spaceBetween = swiper.params.spaceBetween;
					var perView = swiper.params.slidesPerView;
					var levelCenter = ( perView - 1 ) / 2;

					// Each slide offset from center
					for (var i = 0, length = slides.length; i < length; i += 1) {
						var $slideEl = slides.eq(i);
						var slideSize = slidesSizesGrid[i];
						var slideOffsetOrig = $slideEl[0].swiperSlideOffset;
						var slideOffset = slideOffsetOrig + slideSize / 2;
						var distance = center - slideOffset;
						var slideOffsetNum = Math.abs( distance ) < 2
												? 0
												: Math.ceil( Math.abs( distance ) / ( slideSize + spaceBetween ) );
						if ( perView % 2 === 0 ) {
							slideOffsetNum = Math.max( 0, slideOffsetNum - 0.5 );
						}
						var place = distance < -1 * Math.max( 1, spaceBetween )
									? 1
									: ( distance > Math.max( 1, spaceBetween )
										? -1
										: 0
										);
						distance = distance + place * slideOffsetNum * spaceBetween;
						var offsetMultiplier = distance / slideSize;
						var delta = Math.max( 0, Math.abs( offsetMultiplier ) - levelCenter );
						var allow = delta > 0;
						var rotateX = isHorizontal ? 0 : ( allow ? -place * Math.min( rotate, rotate * delta ) : 0 );
						var rotateY = isHorizontal ? ( allow ? -place * Math.min( rotate, rotate * delta ) : 0 ) : 0;
						// var rotateZ = 0
						var offset = allow
									? distance + place * levelCenter * slideSize
									: 0;
						var translateX = isHorizontal ? ( allow ? offset : 0 ) : 0;
						var translateY = isHorizontal ? 0 : ( allow ? offset : 0 );
						var translateZ = -slideSize * ( allow ? delta : 0 );

						var scale = 1 - ( 1 - params.scale ) * Math.abs( offsetMultiplier );

						// Fix for ultra small values
						if ( Math.abs(translateX) < 0.001 )	translateX = 0;
						if ( Math.abs(translateY) < 0.001 )	translateY = 0;
						if ( Math.abs(translateZ) < 0.001 )	translateZ = 0;
						if ( Math.abs(rotateY) < 0.001 )	rotateY = 0;
						if ( Math.abs(rotateX) < 0.001 )	rotateX = 0;
						if ( Math.abs(scale) < 0.001 )		scale = 0;

						var slideTransform = "translate3d(" + translateX + "px," + translateY + "px," + translateZ + "px)"
											+ " rotateX(" + rotateX + "deg)"
											+ " rotateY(" + rotateY + "deg)"
											+ " scale(" + scale + ")";

						$slideEl.transform( slideTransform );
						$slideEl[0].style.zIndex = -Math.abs( Math.round( offsetMultiplier ) ) + 1;
						$slideEl[0].style.opacity = perView > 1 ? Math.max( 0, 1 - delta ) : 1;

						if ( params.slideShadows ) {
							let $shadowBeforeEl = isHorizontal ? $slideEl.find('.swiper-slide-shadow-left') : $slideEl.find('.swiper-slide-shadow-top');
							let $shadowAfterEl = isHorizontal ? $slideEl.find('.swiper-slide-shadow-right') : $slideEl.find('.swiper-slide-shadow-bottom');
					
							if ( $shadowBeforeEl.length === 0 ) {
								$shadowBeforeEl = createShadow( params, $slideEl, isHorizontal ? 'left' : 'top' );
							}
					
							if ( $shadowAfterEl.length === 0 ) {
								$shadowAfterEl = createShadow( params, $slideEl, isHorizontal ? 'right' : 'bottom' );
							}
					
							if ( $shadowBeforeEl.length ) {
								$shadowBeforeEl[0].style.opacity = offsetMultiplier > 0 ? offsetMultiplier : 0;
							}
							if ( $shadowAfterEl.length ) {
								$shadowAfterEl[0].style.opacity = -offsetMultiplier > 0 ? -offsetMultiplier : 0;
							}
						}
					}

					// Set correct perspective for IE10
					if ( trx_addons_browser_is_pointer_events() ) {
						var ws = $wrapperEl[0].style;
						ws.perspectiveOrigin = center + "px 50%";
					}
				},

				setTransition: function setTransition( swiper, duration, changeCssTransition ) {
					const {
						transformEl
					} = swiper.params.swapEffect;
					const $transitionElements = transformEl ? swiper.slides.find( transformEl ) : swiper.slides;
					$transitionElements.transition( duration );
					if ( changeCssTransition ) {
						$transitionElements.css( {
							'webkitTransitionProperty': 'transform, opacity, z-index',
							'transitionProperty': 'transform, opacity, z-index',
							'webkitTransitionDuration': duration + 'ms',
							'transitionDuration': duration + 'ms'
						} );
					}
					if ( swiper.params.slideShadows ) {
						const $shadow = $transitionElements.find('.swiper-slide-shadow-top, .swiper-slide-shadow-right, .swiper-slide-shadow-bottom, .swiper-slide-shadow-left');
						if ( $shadow && $shadow.length ) {
							$shadow.transition( duration );
							if ( changeCssTransition ) {
								$shadow.css( {
									'webkitTransitionProperty': 'transform, opacity, z-index',
									'transitionProperty': 'transform, opacity, z-index',
									'webkitTransitionDuration': duration + 'ms',
									'transitionDuration': duration + 'ms'
								} );
							}
						}
					}
				}
			};

			// Old way: Before Swiper v6+
			if ( typeof Swiper.prototype.modules != 'undefined' && typeof Swiper.prototype.modules["effect-fade"] != 'undefined' ) {

				Swiper.__proto__.Swap = {
					setTranslate: function setTranslate() {
						effectSwapModules.setTranslate( this );
					},
					setTransition: function setTransition( duration ) {
						effectSwapModules.setTransition( this, duration, false );
					}
				};

				Swiper.__proto__.EffectSwap = {
					name: 'effect-swap',
					params: {
						swapEffect: {
							rotate: 50,
							scale: 1
						}
					},
					create: function create() {
						var swiper = this;
						trx_addons_object_extend( swiper, {
							swapEffect: {
								setTranslate: Swiper.Swap.setTranslate.bind( swiper ),
								setTransition: Swiper.Swap.setTransition.bind( swiper )
							}
						} );
					},
					on: {
						beforeInit: function beforeInit() {
							var swiper = this;
							if ( swiper.params.effect !== 'swap' ) {
								return;
							}
							swiper.classNames.push( swiper.params.containerModifierClass + "swap" );
							swiper.classNames.push( swiper.params.containerModifierClass + "3d" );

							swiper.params.watchSlidesProgress = true;
							swiper.originalParams.watchSlidesProgress = true;
						},
						setTranslate: function setTranslate() {
							var swiper = this;
							if ( swiper.params.effect !== 'swap' ) {
								return;
							}
							swiper.swapEffect.setTranslate();
						},
						setTransition: function setTransition( duration ) {
							var swiper = this;
							if ( swiper.params.effect !== 'swap' ) {
								return;
							}
							swiper.swapEffect.setTransition(duration);
						}
					}
				};

				Swiper.use( [Swiper.EffectSwap] );

			// New way: After Swiper v6+
			} else {

				// Register module		
				const EffectSwap = function( _ref ) {
					let {
						swiper,
						extendParams,
						on
					} = _ref;
					extendParams( {
						swapEffect: {
							rotate: 50,
							scale: 1,
	//						stretch: 0,
	//						depth: 100,
	//						modifier: 1,
	//						slideShadows: true,
							transformEl: null
						}
					} );
				
					const setTranslate = () => {
						effectSwapModules.setTranslate( swiper );
					};
				
					const setTransition = duration => {
						effectSwapModules.setTransition( swiper, duration, true );
					};
				
					effectInit( {
						effect: 'swap',
						swiper,
						on,
						setTranslate,
						setTransition,
						perspective: () => true,
						overwriteParams: () => ( {
							watchSlidesProgress: true
						} )
					} );
				};

				Swiper.use( [EffectSwap] );
			}
		}

	} );

})();