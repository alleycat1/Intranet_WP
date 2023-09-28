/*
 * jQuery DP Pro Event Calendar v3
 *
 * Copyright 2012, Diego Pereyra
 *
 * @Web: http://www.dpereyra.com
 * @Email: info@dpereyra.com
 *
 * Depends:
 * jquery.js
 */
  
(function ($) {
	function DPProEventCalendar(element, options) {
		this.calendar = $(element);
		this.eventDates = $('.dp_pec_date', this.calendar);
		
		/* Setting vars*/
		this.settings = $.extend({}, $.fn.dpProEventCalendar.defaults, options); 
		this.orig_settings = options;
		this.category = '';
		this.location = '';

		this.cache = new Array;
		this.cache_param = '';

		this.view = "monthly";
		this.grid = undefined;
		this.monthlyView = "calendar";
		this.type = 'calendar';
		this.defaultDate = 0;
		this.startTime = 0;
		
		this.init();
	}
	
	DPProEventCalendar.prototype = {
		init : function(){
			var instance = this;

			instance.init_tooltips();

			$(document).on('click', '.dpProEventCalendar_close_modal_btn', function(e) {
			
				$('.dpProEventCalendarModal, .dpProEventCalendarOverlay').fadeOut('fast');
				$('body, html').css('overflow', '');
				
			});

			if(instance.settings.type == 'single_page') 
			{

				instance.create_overlay();
				instance.create_book_event();

				instance.create_event_modal(true);
				instance.make_event_form_work(instance.calendar);

				instance.open_options();
				instance.remove_event();

				instance.more_dates();

				instance.starts_in();

				return;

			}
			
			var pec_new_event_captcha;

			instance.view = instance.settings.view;
			instance.defaultDate = instance.settings.defaultDate;
			
			if(instance.settings.type == 'compact') {
				instance.view = "monthly";
			}

			if(instance.settings.type == 'carousel-2' || instance.settings.type == 'carousel-3')
			{
				instance.settings.type = 'carousel';
			}

			if(instance.settings.type == 'slider-2' || instance.settings.type == 'slider-3')
			{
				instance.settings.type = 'slider';
			}

			if(instance.settings.type == 'countdown') {
				if($('.dp_pec_countdown_event', instance.calendar).length) {
					$('.dp_pec_countdown_event', instance.calendar).each(function() {
						var launchDateFix = new Date(
							$(this).data('countdown-year'), 
							($(this).data('countdown-month') - 1), 
							$(this).data('countdown-day'), 
							$(this).data('countdown-hour'), 
							$(this).data('countdown-minute')
						);

						var currentDate = new Date(
							$(this).data('current-year'), 
							($(this).data('current-month') - 1), 
							$(this).data('current-day'), 
							$(this).data('current-hour'), 
							$(this).data('current-minute'),
							$(this).data('current-second')
						);

						instance._setup_countdown(launchDateFix, currentDate, this, $(this).data('countdown-tzo'));
					});
				}
			}

			$(instance.calendar).not('.dp_pec_compact_wrapper').addClass( instance.settings.skin );
			instance._makeResponsive();

			instance.create_overlay();
			instance.create_book_event();
			
			
			if( $('.pec_upcoming_layout', instance.calendar).length ) 
			{
			
				instance.create_isotope( '.pec_upcoming_layout' );

				instance.create_event_modal(true);

			}
			
			$(instance.calendar).on('click', '.prev_month', function(e) { instance._prevMonth(instance); });
			if(instance.settings.dateRangeStart && instance.settings.dateRangeStart.substr(0, 7) == instance.settings.actualYear+"-"+instance._str_pad(instance.settings.actualMonth, 2, "0", 'STR_PAD_LEFT') && !instance.settings.isAdmin) {
				$('.prev_month', instance.calendar).hide();
			}
			
			$(instance.calendar).on('click', '.next_month', function(e) { instance._nextMonth(instance); });
			if(instance.settings.dateRangeEnd && instance.settings.dateRangeEnd.substr(0, 7) == instance.settings.actualYear+"-"+instance._str_pad(instance.settings.actualMonth, 2, "0", 'STR_PAD_LEFT') && !instance.settings.isAdmin) {
				$('.next_month', instance.calendar).hide();
			}
			
			$('.prev_day', instance.calendar).click(function(e) { instance._prevDay(instance); });
			$('.next_day', instance.calendar).click(function(e) { instance._nextDay(instance); });
			
			$('.prev_week', instance.calendar).click(function(e) { instance._prevWeek(instance); });
			$('.next_week', instance.calendar).click(function(e) { instance._nextWeek(instance); });

			$('.pec_today', instance.calendar).click(function(e) { instance._today(instance); });
						
			if(instance.settings.type == "add-event") {

				instance.make_event_form_work(instance.calendar);
				instance.create_datepicker(instance.calendar);

				
			}
			
			if(instance.settings.type == "grid-upcoming") {
				// Functions for Grid Layout
				$(instance.calendar).on('click', '.dp_pec_grid_event', function() {
					
					//$('.dp_pec_grid_link_image', $(this).closest('.dp_pec_grid_event')).attr('target', '_blank');
					$('.dp_pec_grid_link_image', $(this))[0].click();
					
				})	
			}

			if(instance.settings.type == "accordion") {

				instance.create_event_modal(true);
				instance.make_event_form_work(instance.calendar);

				instance.accordion_month_dropdown();

			}

			if(instance.settings.type == "list-author") {

				instance.create_event_modal(true);
				instance.make_event_form_work(instance.calendar);

			}

			if(instance.settings.type == "yearly") {
				// Functions for Grid Layout

				$(instance.calendar).on('click', '.dp_pec_filter', function(event) {

					event.preventDefault();

					instance.toggle_filter( $(this) );

				});


				$(instance.calendar).on('click', '.next_year, .prev_year', function(event) {
					
					event.preventDefault();

					var year_header = $('h3', $(this).closest('.dp_pec_yearly_header'));

					if($(this).hasClass('next_year')) {
						year_header.html(parseInt(instance.settings.actualYear) + 1);
						instance.settings.actualYear++;
					} else {
						year_header.html(parseInt(instance.settings.actualYear) - 1);
						instance.settings.actualYear--;
					}

					instance._changeYear();
					
					return false;
					
				});

				$(instance.calendar).on('click', '.dp_pec_yearly_calendar_month_date.pec_has_events', function(event) {
					var position = $(this).closest('.dp_pec_yearly_calendar_month_date').offset();

					var eventsPreview = $(this).find('.pec_yearly_eventsPreview');
					eventsPreview.removeClass('previewRight');
					if(position && position.left + eventsPreview.outerWidth() >= $( window ).width()) {
						eventsPreview.addClass('previewRight');
					}
				 	eventsPreview.toggle();
				 });

				$(document).mouseup(function(e) 
				{
				    var container = $(".pec_yearly_eventsPreview", instance.calendar);

				    container.hide();
				
				});

			}

			if(instance.settings.type == "carousel") {

				// Functions for carousel layout
				var carousel_items = $('.dp_pec_carousel_item', instance.calendar);
				
				const carousel_columns = parseInt(instance.settings.columns);

				$(instance.calendar).on('click', '.dp_pec_carousel_nav a', function(e) {
					e.preventDefault();

					const nav_last = $('.dp_pec_carousel_active', instance.calendar).data('pec-nav');

					$('.dp_pec_carousel_nav a', instance.calendar).removeClass('dp_pec_carousel_active');
					$(this).addClass('dp_pec_carousel_active');

					const nav = $(this).data('pec-nav');
					const nav_start = nav * carousel_columns;
					const nav_start_first = (nav + 1) * carousel_columns;

					var visible_carousel_item = $('.dp_pec_carousel_item_visible', instance.calendar);
					
					

					visible_carousel_item.removeClass('dp_pec_carousel_item_visible dp_pec_carousel_item_animation_left dp_pec_carousel_item_animation_right');
					
					if( nav < nav_last) {

						var item_to_show = carousel_items.slice( (nav_start_first - carousel_columns), nav_start_first );

						item_to_show.addClass('dp_pec_carousel_item_visible dp_pec_carousel_item_animation_right');

					} else {

						var item_to_show = carousel_items.slice( nav_start, (nav_start + carousel_columns) );

						item_to_show.addClass('dp_pec_carousel_item_visible dp_pec_carousel_item_animation_left');

					}

				})

				instance.create_event_modal(true);
				instance.make_event_form_work(instance.calendar);

			}

			if(instance.settings.type == "slider") {
				// Functions for Slider layout
				var slider_items = $('.dp_pec_slider_item', instance.calendar);
				var slider_loop = $(instance.calendar).hasClass('dp_pec_slider_loop');

				$(instance.calendar).on('click', '.dp_pec_slider_prev', function() {
					$('.dp_pec_slider_next', instance.calendar).show();
					var visible_slider_item = $('.dp_pec_slider_item_visible', instance.calendar);
					var slider_item_prev = visible_slider_item.prev();
					
					// Loop?
					if(slider_loop) {
						if(typeof slider_item_prev[0] == 'undefined') {
						
							slider_item_prev = slider_items.last();
						} 
					} else {
						if(slider_item_prev.is(':first-child'))
							$(this).hide();
					}
					

					slider_item_prev.addClass('dp_pec_slider_item_visible dp_pec_slider_item_animation_right');
					visible_slider_item.removeClass('dp_pec_slider_item_visible dp_pec_slider_item_animation_left dp_pec_slider_item_animation_right');
				})

				$(instance.calendar).on('click', '.dp_pec_slider_next', function() {
					$('.dp_pec_slider_prev', instance.calendar).show();
					var visible_slider_item = $('.dp_pec_slider_item_visible', instance.calendar);
					var slider_item_next = visible_slider_item.next();
					
					// Loop?

					if(slider_loop) {
						if(typeof slider_item_next[0] == 'undefined') {
							slider_item_next = slider_items.first();
						} 
					} else {

						if(slider_item_next.is(':last-child'))
							$(this).hide();
					}

					slider_item_next.addClass('dp_pec_slider_item_visible dp_pec_slider_item_animation_left');
					visible_slider_item.removeClass('dp_pec_slider_item_visible dp_pec_slider_item_animation_left dp_pec_slider_item_animation_right');

				
				})

				instance.create_event_modal(true);
				instance.make_event_form_work(instance.calendar);
			}

			if(instance.settings.type == "timeline") {
				// Functions for Card layout
				var tl_left = 0;
				var tl_direction = '';

				if( !instance.isMobile() ) {

					$(".dp_pec_timeline_drag", instance.calendar).draggable({ 

						axis: "x", 
						cancel: ".dp_pec_timeline_date_mark, .dp_pec_timeline_date_popup",
						drag: function( event, ui) {
					        
					        var position = ui.position;
					        //ui.originalPosition.left = 0;

					        if( position.left < tl_left ) {
					        	tl_direction = 'left';
					        } else if( position.left > tl_left ) {
					        	tl_direction = 'right';
					        }

					        tl_left = position.left;

					        $( this ).draggable( 'option',  'revert', false );

					        if(tl_left > 0) {
					        	//$(this).css('left', 0);
					        	ui.originalPosition.left = -20;
					        	$( this ).draggable( 'option',  'revert', true ).trigger( 'mouseup' );


					        }

					        var outerWidth = $(this).outerWidth();
					        var outerWidth_parent = $(this).parent().outerWidth();

					        if(outerWidth_parent + ( tl_left * -1 ) > outerWidth) {

					        	ui.originalPosition.left = tl_left + 50;
					        	$( this ).draggable( 'option',  'revert', true ).trigger( 'mouseup' );

					        }

					    },
					    stop: function () {
					    	//console.log('end' + tl_direction);
					    }

				  	});

			  } else {

					var	pos,
							isDragging = false;

				  	$(instance.calendar).pec_touch({

						// Turn on document tracking for smoother dragging.
							trackDocument: true,

						// Set drag threshold to zero for maximum drag sensitivity.
							dragThreshold: 0,

						// Set drag delay to zero for fastest drag response.
							dragDelay: 0,

						// Delegate touch events to items.
							delegateSelector: '.dp_pec_timeline_drag',

						// Lower tap and hold delay.
							tapAndHoldDelay: 0,

						// Prevent default events for drag events.
							preventDefault: {
								drag: false
							}

					}).on('dragStart tapAndHold', '.dp_pec_timeline_drag', function(event) {

						// Stop propagation.
							event.stopPropagation();

						// Vars.
							var $this = $(this);
							if(typeof pos == 'undefined')
							pos = $this.offset();

						// Already dragging? Bail.
							if (isDragging)
								return;

						// Set dragging state.
							isDragging = true;

						// Mark as dragging.
							$this.addClass('pec-is-dragging');

					})
					.on('drag', '.dp_pec_timeline_drag', function(event, o) {

						// Stop propagation.
							event.stopPropagation();

						// Vars.
							var $this = $(this);

							var tl_left = (( o.x - o.exStart) - (pos.left) );

				        //$( this ).draggable( 'option',  'revert', false );

				        if(tl_left > 0) {
				        	//$(this).css('left', 0);
				        	//ui.originalPosition.left = -20;
				        	//$( this ).draggable( 'option',  'revert', true ).trigger( 'mouseup' );
				        	$( this ).trigger( 'mouseup' );

				        	if( !instance.isMobile() ) {
								$this.animate({
							    left: "-=20"
							  }, 500);
							}
				        	

				        	return false;


				        }

				        var outerWidth = $(this).outerWidth();
				        var outerWidth_parent = $(this).parent().outerWidth();

				        if(outerWidth_parent + ( tl_left * -1 ) > outerWidth) {

				        	//ui.originalPosition.left = tl_left + 50;
				        	//$( this ).draggable( 'option',  'revert', true ).trigger( 'mouseup' );
				        	$( this ).trigger( 'mouseup' );
							
							if( !instance.isMobile() ) {
					        	$this.animate({
								    left: "+=50"
								  }, 500);
					        }

				        	return false;

				        	

				        }


						// Update position.
							$this
								.css('left', tl_left  + 'px');

					}).on('dragEnd tapAndHoldEnd', '.dp_pec_timeline_drag', function(event) {

						// Stop propagation.
							event.stopPropagation();

						// Vars.
							var $this = $(this);
						// Clear dragging state.
							isDragging = false;

						// Unmark as dragging.
							$this.removeClass('pec-is-dragging');

					});

				}

				$(instance.calendar).on('click', '.dp_pec_timeline_date_separator', function( e ) {

					if($(e.target).hasClass('dp_pec_timeline_date_mark')) {
						$elem = $(this).closest('.dp_pec_timeline_date_separator');
					} else {
						$elem = $(this);
					}

					const has_class = $elem.hasClass('dp_pec_timeline_date_active');

					$('.dp_pec_timeline_date_separator', instance.calendar).removeClass('dp_pec_timeline_date_active');

					if(!has_class) {

						$elem.addClass('dp_pec_timeline_date_active');

					}

				});

			

			}

			if(instance.settings.type == "card") {
				// Functions for Card layout
				$(instance.calendar).on('click', '.dp_pec_card_event:not(.dp_pec_card_active)', function() {
					$('.dp_pec_card_event', $(this).parent()).removeClass('dp_pec_card_active');
					$(this).addClass('dp_pec_card_active');

					// Open card Data

					$('.dp_pec_card_selected h3', $(this).closest('.dp_pec_card_wrapper')).text($(this).data('event-title'));
					var location =$(this).data('event-location');
					if(location == ""){
						$('.dp_pec_card_selected .dp_pec_card_location', $(this).closest('.dp_pec_card_wrapper')).hide();
					} else {
						$('.dp_pec_card_selected .dp_pec_card_location', $(this).closest('.dp_pec_card_wrapper')).show();
					}
					$('.dp_pec_card_selected .dp_pec_card_location span', $(this).closest('.dp_pec_card_wrapper')).text(location);
					$('.dp_pec_card_selected .dp_pec_card_time span', $(this).closest('.dp_pec_card_wrapper')).text($(this).data('event-time'));
					$('.dp_pec_card_selected', $(this).closest('.dp_pec_card_wrapper')).css('background-image', 'url(' + $(this).data('event-background') + ')');

					$('.dp_pec_card_selected_foot a', $(this).closest('.dp_pec_card_wrapper')).attr('href', $(this).data('event-link'));
					$('.dp_pec_card_selected', $(this).closest('.dp_pec_card_wrapper')).css('opacity', 0).stop().animate( {opacity:1}, {duration:500});
					
				})	
			}
			
			$(instance.calendar).on('click', '.dp_pec_event_description_more', function(e) {
				if( $(this).attr('href') == '#' ) {
					e.preventDefault();
					
					$(this).closest('.dp_pec_event_description').addClass('dp_pec_event_description_full');
					
					if(typeof instance.grid != "undefined") {
						instance.grid.isotope('layout');
					}

				}
				
			});
			
			
			if( !instance.settings.isAdmin ) {
				
				$( instance.calendar ).on({
					mouseenter:
					   function(e)
					   {
						   
						   if($('.dp_pec_content', instance.calendar).hasClass('dp_pec_content_loading'))
								return;
							
						   if(!$('.eventsPreviewDiv').length) {
								$('body').append($('<div />').addClass('eventsPreviewDiv'));
						   }
						  
						   $('.eventsPreviewDiv').removeClass('pec_skin_light pec_skin_dark').addClass(instance.settings.skin);
						   
							$('.eventsPreviewDiv').html($('.eventsPreview', $(this)).html());
							
							/*$(this).off( "mouseenter mouseenter", ".dp_daily_event:not(.dp_daily_event_show_more)");
							$(this).off( "mouseenter mouseenter", ".dp_daily_event.dp_daily_event_show_more");
							*/
							
							
							if($('.eventsPreviewDiv').html() != "" && !$('.dp_daily_event', instance.calendar).is(':visible')) {
								$('.eventsPreviewDiv').fadeIn('fast');
							}
							
							$('.eventsPreviewDiv ul li').removeClass('dp_pec_preview_event').show();
							
					   },
					mouseleave:
					   function()
					   {
							if(!$('.dp_daily_event', instance.calendar).is(':visible')) {
								$('.eventsPreviewDiv').html('').stop().hide();
							}
							/*
							$(this).off( "mouseenter mouseenter", ".dp_daily_event:not(.dp_daily_event_show_more)");
							$(this).off( "mouseenter mouseenter", ".dp_daily_event.dp_daily_event_show_more");
							*/
					   }
				   }, '.dp_pec_date:not(.disabled)'
				).bind('mousemove', function(e){
						
					if($('.eventsPreviewDiv').html() != "") {
						var body_pos = $("body").css('position');
						if(body_pos == "relative") {
							$("body").css('position', 'static');
						}
						$('.eventsPreviewDiv').removeClass('previewRight');
						
						var position = $(e.target).closest('.dp_pec_date').offset();
						var target_height = $(e.target).closest('.dp_pec_date').height();
						if(typeof position != "undefined") {
							$('.eventsPreviewDiv').css({
								left: position.left,
								top: position.top,
								marginTop: (target_height + 12) + "px",
								marginLeft: (position.left + $('.eventsPreviewDiv').outerWidth() >= $( window ).width() ? -($('.eventsPreviewDiv').outerWidth() - 30) + "px" : 0)
							});
						}
						
						if(position && position.left + $('.eventsPreviewDiv').outerWidth() >= $( window ).width()) {
							$('.eventsPreviewDiv').addClass('previewRight');
						}
					}
				});

				$(instance.calendar).on({
					mouseenter:
						function(e)
						{

							if($('.dp_pec_content', instance.calendar).hasClass('dp_pec_content_loading'))
								return;

							$('.eventsPreviewDiv ul li').hide();

							var event_id = $(e.target).data('dppec-event');
							
							if(typeof event_id == "undefined") {
								event_id = $(e.target).closest('.dp_daily_event').data('dppec-event')
							}

							$(".eventsPreviewDiv ul").find("li[data-dppec-event='" + event_id + "']").addClass('dp_pec_preview_event').show();

							if($('.eventsPreviewDiv').html() != "") {
								$('.eventsPreviewDiv').fadeIn('fast');
							}
						},
					mouseleave:
						function() 
						{
							$('.eventsPreviewDiv ul li').removeClass('dp_pec_preview_event').show();
							$('.eventsPreviewDiv').stop().hide();
						}
				}, '.dp_daily_event:not(.dp_daily_event_show_more)');
				
				$(instance.calendar).on({
					mouseenter:
						function(e)
						{

							if($('.dp_pec_content', instance.calendar).hasClass('dp_pec_content_loading'))
								return;

							$('.eventsPreviewDiv ul li').hide();
							$(".eventsPreviewDiv ul li:gt("+( instance.settings.calendar_per_date - 1 )+")").addClass('dp_pec_preview_event').show();

							if($('.eventsPreviewDiv').html() != "") {
								$('.eventsPreviewDiv').fadeIn('fast');
							}
						},
					mouseleave:
						function() 
						{
							$('.eventsPreviewDiv ul li').removeClass('dp_pec_preview_event').show();
							$('.eventsPreviewDiv').stop().hide();
						}
				}, '.dp_daily_event.dp_daily_event_show_more');
				
				
			

				
				$(instance.calendar).on('mouseup', '.dp_pec_date:not(.disabled)', function(event) {
					
					if(($(event.target).hasClass('dp_daily_event') || $(event.target).parent().hasClass('dp_daily_event') ) && (!$(event.target).hasClass('dp_daily_event_show_more'))) { return; }
					
					if(instance.settings.type == "modern") { 
						if($(event.target).hasClass('dp_daily_event_show_more')) {
							
							$('.dp_daily_event', $(this)).show();
							$('.dp_daily_event_show_more', $(this)).hide();

						}
						return; 
					}

					if(instance.calendar.hasClass('dp_pec_daily')) { return; }
					if(instance.calendar.hasClass('dp_pec_weekly')) { return; }

					if(instance.settings.event_id != '' && $('.dp_pec_form_desc').length) {
						if( !$(this).find('.dp_book_event_radio').length ) {
							return;	
						}
						
						$('.dp_book_event_radio', instance.calendar).removeClass('dp_book_event_radio_checked');
						$(this).find('.dp_book_event_radio').addClass('dp_book_event_radio_checked');
						$('#pec_event_page_book_date', '.dpProEventCalendarModal').val($(this).data('dppec-date'));
						
						return;
					}
					
					if(!$('.dp_pec_content', instance.calendar).hasClass('isDragging') && (event.which === 1 || event.which === 0)) {
						
						//instance._goToByScroll($(instance.calendar));

						instance._removeElements();

						var params = { 
							date: $(this).data('dppec-date'), 
							calendar: instance.settings.calendar, 
							category: (instance.settings.category != "" ? instance.settings.category : $('select.pec_categories_list', instance.calendar).val()), 
							location: (instance.settings.location != "" ? instance.settings.location : $('select.pec_location_list', instance.calendar).val()), 
							speaker: (instance.settings.speaker != "" ? instance.settings.speaker : $('select.pec_speaker_list', instance.calendar).val()), 
							event_id: instance.settings.event_id, 
							calendar_per_date: instance.settings.calendar_per_date,
							author: instance.settings.author, 
							include_all_events: instance.settings.include_all_events,
							modal: instance.settings.modal,
							hide_old_dates: instance.settings.hide_old_dates,
							type: instance.settings.type, 
							action: 'getEvents', 
							postEventsNonce : ProEventCalendarAjax.postEventsNonce 
						};

						instance.cache_param = JSON.stringify(params);

						if(typeof instance.cache[instance.cache_param] != 'undefined') {

							instance._get_events( instance.cache[instance.cache_param] );

						} else {

							$.post(ProEventCalendarAjax.ajaxurl, params, function(data) { instance._get_events(data); });

						}

					}
	
				});

				

				$(instance.calendar).on('click', '.dp_pec_date_event_image_zoom', function(event) {
					if((event.which === 1 || event.which === 0)) {

						if(typeof $(this).data('img-url') != 'undefined') {

							$('body').append($('<img>').addClass('dpProEventCalendar_Image').attr('src', $(this).data('img-url')).fadeIn(300).click(function(event) {

								if((event.which === 1 || event.which === 0)) {

									instance.close_modal();

								}

							}));

							instance.show_overlay();

						}

					}

				});
				
				$(instance.calendar).on('click', '.dp_daily_event:not(.dp_daily_event_show_more)', function(event) {
					if((event.which === 1 || event.which === 0)) {
						
						if(instance.settings.modal)
						{

							event.preventDefault();

							instance._open_event( $(this).data('dppec-event'), $(this).data('dppec-date'));
							return false;

						}

						if($(this).attr('href') != "javascript:void(0);" && $(this).attr('href') != "#") {
							
							//event.preventDefault();
							//return false;
							
						} else {
							
							instance._removeElements();

							var params = { 
								event: $(this).data('dppec-event'), 
								calendar: instance.settings.calendar, 
								date:$(this).data('dppec-date'),  
								action: 'getEvent', 
								postEventsNonce : ProEventCalendarAjax.postEventsNonce 
							};
						
							instance.cache_param = JSON.stringify(params);

							if( instance.isMobile() ) {
								instance._goToByScroll( instance.calendar );
							}

							if(typeof instance.cache[instance.cache_param] != 'undefined') {

								instance._get_event( instance.cache[instance.cache_param] );

							} else {

								$.post(ProEventCalendarAjax.ajaxurl, params, function(data) { instance._get_event(data); });

							}

							event.preventDefault();
							return false;

						}
					}
					
				});
			}
			
			$(instance.calendar).on('click', '.dp_pec_date_event_back', function(event) {
				event.preventDefault();
				instance._removeElements();
				
				instance._changeLayout();
			});
			
			$(instance.calendar).on({
				'mouseenter': function(i) {

					$('.dp_pec_user_rate li a').addClass('is-off');
	
					for(var x = $(this).data('rate-val'); x > 0; x--) {
						$('.dp_pec_user_rate li a[data-rate-val="'+x+'"]', instance.calendar).removeClass('is-off').addClass('is-on');
					}

				},
				'mouseleave': function() {
					$('.dp_pec_user_rate li a', instance.calendar).removeClass('is-on');
					$('.dp_pec_user_rate li a', instance.calendar).removeClass('is-off');
				},
				'click': function() {
					
					$('.dp_pec_user_rate', instance.calendar).replaceWith($('<div>').addClass('dp_pec_loading').attr({ id: 'dp_pec_loading_rating' }));
					
					jQuery.post(ProEventCalendarAjax.ajaxurl, { 
							event_id: $(this).data('event-id'), 
							rate: $(this).data('rate-val'), 
							calendar: instance.settings.calendar,
							action: 'ProEventCalendar_RateEvent', 
							postEventsNonce : ProEventCalendarAjax.postEventsNonce 
						},
						function(data) {
							$('#dp_pec_loading_rating', instance.calendar).replaceWith(data);
						}
					);	

					return false;
				}
			}, '.dp_pec_user_rate li a');
			
			
			
			$('.dpProEventCalendar_subscribe', instance.calendar).click(function(e) {
				e.preventDefault();

				$('body, html').css('overflow', 'hidden');

				var mailform = $(this).next('.dpProEventCalendar_subscribe_form');
				
				if($('.dpProEventCalendarModal').length) {
					$('.dpProEventCalendarModal').remove()
				}

				if(!$('.dpProEventCalendarModal').length) {
					$('body').append(
						$('<div>').addClass('dpProEventCalendarModal').prepend(
							$('<h2>').text(instance.settings.lang_subscribe).append(
								$('<a>').addClass('dpProEventCalendarClose').attr({ 'href': '#' }).html('<i class="fa fa-times"></i>')
							)
						).append(
							$('<div>').addClass('dpProEventCalendar_mailform').html( mailform.html() )
						).show()
					);
					
					instance.show_overlay();
					
					//dpShareLoadEvents();
				} else {
					
					$('.dpProEventCalendarModal .pec_book_select_date, .dpProEventCalendarModal .dpProEventCalendar_mailform').remove();
					
					$('.dpProEventCalendarModal').append(
						$('<div>').addClass('dpProEventCalendar_mailform').html(mailform.html())
					)

					$('.dpProEventCalendarModal, .dpProEventCalendarOverlay').removeAttr('style').show();

				}

				var $modal = $('.dpProEventCalendarModal');

				$('.dpProEventCalendar_subscribe_form', '.dpProEventCalendarModal').show();

				//Close modals
				instance.close_button($modal);
				
				if(ProEventCalendarAjax.recaptcha_enable && ProEventCalendarAjax.recaptcha_site_key != "") {
					var pec_subscribe_captcha;
					pec_subscribe_captcha = grecaptcha.render($('#pec_subscribe_captcha', '.dpProEventCalendarModal')[0], {
					  'sitekey' : ProEventCalendarAjax.recaptcha_site_key
					});
				}
				
				$('.dpProEventCalendar_send', '.dpProEventCalendarModal').click(function(e) {
					e.preventDefault();
					$(this).prop('disabled', true);
					$('.dpProEventCalendar_sending_email', '.dpProEventCalendarModal').css('display', 'inline-block');
					
					var post_obj = {
						your_name: $('#dpProEventCalendar_your_name', '.dpProEventCalendarModal').val(), 
						your_email: $('#dpProEventCalendar_your_email', '.dpProEventCalendarModal').val(),
						calendar: instance.settings.calendar,
						action: 'ProEventCalendar_NewSubscriber', 
						postEventsNonce : ProEventCalendarAjax.postEventsNonce 
					}

					var captcha_error = false;
					if(ProEventCalendarAjax.recaptcha_enable && ProEventCalendarAjax.recaptcha_site_key != "") {
						post_obj.grecaptcharesponse = grecaptcha.getResponse(pec_subscribe_captcha);
						if(post_obj.grecaptcharesponse == "") {
							captcha_error = true;
						}
					}

					if( $('#dpProEventCalendar_your_name', '.dpProEventCalendarModal').val() != ""
						&& $('#dpProEventCalendar_your_email', '.dpProEventCalendarModal').val() != ""
						&& !captcha_error) {
						
						jQuery.post(ProEventCalendarAjax.ajaxurl, post_obj,
							function(data) {
								$('.dpProEventCalendar_send', '.dpProEventCalendarModal').prop('disabled', false);
								$('.dpProEventCalendar_sending_email', '.dpProEventCalendarModal').hide();
								
								$('.dp_pec_notification_event_succesfull, .dp_pec_notification_event_error', '.dpProEventCalendarModal').hide();
								$('.dp_pec_notification_event_succesfull', '.dpProEventCalendarModal').css('display', 'inline-block');
								$('form', '.dpProEventCalendarModal')[0].reset();

								if (typeof grecaptcha !== 'undefined') {
									grecaptcha.reset(pec_subscribe_captcha);
								}
							}
						);	
					} else {
						$(this).prop('disabled', false);
						$('.dpProEventCalendar_sending_email', '.dpProEventCalendarModal').hide();
						
						$('.dp_pec_notification_event_succesfull, .dp_pec_notification_event_error', '.dpProEventCalendarModal').hide();
						$('.dp_pec_notification_event_error', '.dpProEventCalendarModal').css('display', 'inline-block');
					}
				});
				
				$("input, textarea", '.dpProEventCalendarModal').placeholder();
				
				
			});

			$('.dp_pec_references', instance.calendar).click(function(e) {
				e.preventDefault();
				if(!$(this).hasClass('active')) {
					$(this).addClass('active');
					$('.dp_pec_references_div', instance.calendar).slideDown('fast');
				} else {
					$(this).removeClass('active');
					$('.dp_pec_references_div', instance.calendar).slideUp('fast');
				}
				
			});

			
			$(instance.calendar).on('click', '.dp_pec_open_map', function(e) {

				e.preventDefault();
				
				instance.show_overlay();

				instance.open_map(this);
				
			});


			instance.open_options();
			
			if(instance.monthlyView == "calendar") {
				var dppec_date = $('.dp_pec_content', instance.calendar).find(".dp_pec_date[data-dppec-date='" + instance.settings.defaultDateFormat + "']");
				
				if(typeof dppec_date.attr('style') == 'undefined' && instance.settings.show_current_date && instance.settings.current_date_color != "") {
					dppec_date.addClass('dp_pec_special_date');
					$('.dp_pec_date_item, .dp_special_date_dot', dppec_date).css('background-color', instance.settings.current_date_color);
				}
			}
			
			$('.dp_pec_view_all', instance.calendar).click(function(event) {
				event.preventDefault();

				if(!$('.dp_pec_content', instance.calendar).hasClass('isDragging') && (event.which === 1 || event.which === 0)) {
					if(instance.monthlyView == "calendar") {
						$(this).html($(this).data('translation-calendar'));
						instance.monthlyView = "list";
					} else {
						$(this).html($(this).data('translation-list'));
						instance.monthlyView = "calendar";
					}
					
					instance._changeMonth();
					
				}
			});

			$('.dp_pec_search_btn', instance.calendar).click(function(event) {
				event.preventDefault();

				if((event.which === 1 || event.which === 0)) {
					
					instance.show_search(false);
					
				}
			});

			$('.dp_pec_search_close', instance.calendar).click(function(event) {
				event.preventDefault();

				if((event.which === 1 || event.which === 0)) 
				{


					instance.show_search(false);

				}
			
			});

			
			if( instance.settings.selectric ) {
				$('.dp_pec_layout select, .dp_pec_add_form select, .dp_pec_nav select, .dp_pec_accordion_wrapper select', instance.calendar).selectric( { disableOnMobile: false });
			}

			$(window).bind('unload', function(e){
			    $('.dp_pec_nav .selectric-wrapper', instance.calendar).remove();

			});
						
			if(instance.view == "monthly-all-events" 
				&& instance.settings.type != "accordion" 
				&& instance.settings.type != "accordion-upcoming" 
				&& instance.settings.type != "add-event" 
				&& instance.settings.type != "list-author" 
				&& instance.settings.type != "grid" 
				&& instance.settings.type != "grid-upcoming"
				&& instance.settings.type != "card" 
				&& instance.settings.type != "slider" 
				&& instance.settings.type != "carousel" 
				&& instance.settings.type != "compact-upcoming" 
				&& instance.settings.type != "list-upcoming" 
				&& instance.settings.type != "gmaps-upcoming" 
				&& instance.settings.type != "today-events" 
				&& instance.settings.type != "bookings-user" 
				&& instance.settings.type != "past"
				&& instance.settings.type != "compact"
				&& instance.settings.type != "modern"
				&& instance.settings.type != "countdown") 
			{
				$('.dp_pec_view_all', instance.calendar).addClass('active');
				instance.monthlyView = "list";
				
				instance._changeMonth();
			}

			$('.dp_pec_references_close', instance.calendar).click(function(e) {
				e.preventDefault();
				$('.dp_pec_references', instance.calendar).removeClass('active');
				$('.dp_pec_references_div', instance.calendar).slideUp('fast');
			});
			
			$('.dp_pec_search', instance.calendar).one('click', function(event) {
				$(this).val("");
			});
			
			if($('.dp_pec_accordion_event', instance.calendar).length) {
				$(instance.calendar).on('click', '.dp_pec_accordion_event', function(e) {
					
					if( $(e.target).hasClass('dp_pec_more_options') || $(e.target).hasClass('pec_event_page_book') || $(e.target).hasClass('fa-calendar') || $(e.target).hasClass('dp_pec_open_map') || $(e.target).hasClass('fa-map-marker-alt') )
						return;

					if(!$(this).hasClass('visible')) {
						if(e.target.className != "dp_pec_date_event_close" && e.target.className != "fa fa-close") {
							$('.dp_pec_accordion_event', instance.calendar).removeClass('visible');
							$(this).addClass('visible');

							//instance._goToByScroll($(this));

							if(typeof instance.grid != "undefined") {
								instance.grid.isotope('layout');
							}
						}
					} else {
						//$(this).removeClass('visible');
					}
				});
				
				$(instance.calendar).on('click', '.dp_pec_date_event_close', function(e) {
					
					$('.dp_pec_accordion_event', instance.calendar).removeClass('visible');
					
					if(typeof instance.grid != "undefined") {
						instance.grid.isotope('layout');
					}

				});
			}
			
			if($('.dp_pec_view_action', instance.calendar).length) {
				$('.dp_pec_view_action', instance.calendar).click(function(e) {
					e.preventDefault();
					$('.dp_pec_view_action', instance.calendar).removeClass('active');
					$(this).addClass('active');
					
					if(instance.view != $(this).data('pec-view')) {
						instance.view = $(this).data('pec-view');
						
						instance._changeLayout();
					}
				});
			}
			
			instance.create_event_modal(false);

					
			
			
			
			if($('.dp_pec_cancel_event', instance.calendar).length) {
				$('.dp_pec_cancel_event', instance.calendar).click(function(e) {
					e.preventDefault();
					$(this).hide();
					$('.dp_pec_add_event', instance.calendar).show();
					
					$('.dp_pec_add_form', instance.calendar).slideUp('fast');
					$('.dp_pec_notification_event_succesfull', instance.calendar).hide();
					
				});
			}
			
			if($('.event_image', instance.calendar).length) {

				$(instance.calendar).on('change', '.event_image', function() 
				{
					$('#event_image_lbl', $(this).parent()).val($(this).val().replace(/^.*[\\\/]/, ''));
				});

			}
			
			
			instance.remove_event();


			$(instance.calendar).on('click', '.pec_cancel_booking', function(e) {
				e.preventDefault();

				$('body, html').css('overflow', 'hidden');
				
				if(!$('.dpProEventCalendarModalEditEvent').length) {
			
					$('body').append(
						$('<div>').addClass('dpProEventCalendarModalEditEvent dpProEventCalendarModalSmall dp_pec_new_event_wrapper').prepend(
							$('<h2>').text($(this).text()).append(
								$('<a>').addClass('dpProEventCalendarClose').attr({ 'href': '#' }).html('<i class="fa fa-times"></i>')
							)
						).append(
							$('<div>').addClass('dpProEventCalendar_eventform').append($(this).next().children().clone(true))
						).show()
					);
					
					instance.show_overlay();
					
				} else {
					$('.dpProEventCalendar_eventform').html($(this).next().html());
					$('.dpProEventCalendarModalEditEvent').addClass('dpProEventCalendarModalSmall');
					$('.dpProEventCalendarModalEditEvent h2').text($(this).text()).append(
						$('<a>').addClass('dpProEventCalendarClose').attr({ 'href': '#' }).html('<i class="fa fa-times"></i>')
					);
					$('.dpProEventCalendarModalEditEvent, .dpProEventCalendarOverlay').show();
				}

				$modal = $('.dpProEventCalendarModalEditEvent');

				instance.close_button($modal);
				
				$('.dpProEventCalendarModalEditEvent').on('click', '.dp_pec_cancel_booking', function(e) {
					e.preventDefault();
					$(this).addClass('dp_pec_disabled');
					var form = $(this).closest(".add_new_event_form");
					
					var origName = $(this).html();
					$(this).html(instance.settings.lang_sending);
					var me = this;
					var form = $(this).closest('form');
					var post_obj = {
						calendar: instance.settings.calendar, 
						action: 'cancelBooking',
						postEventsNonce : ProEventCalendarAjax.postEventsNonce
					}

					$(this).closest(".add_new_event_form").ajaxForm({
						url: ProEventCalendarAjax.ajaxurl,
						data: post_obj,
						success:function(data){
							$(me).html(origName);
							location.reload();	

							$(me).removeClass('dp_pec_disabled');

						}
					}).submit();
				});		
				
			});
			
			function pec_createWindowNotification(text) {
				if(!$('.dpProEventCalendar_windowNotification').length) {
					$('body').append(
						$('<div>').addClass('dpProEventCalendar_windowNotification').text(text).show()
					);
				} else {
					$('.dpProEventCalendar_windowNotification').removeClass('fadeOutDown').text(text).show();
				}
				
				setTimeout(function() { $('.dpProEventCalendar_windowNotification').addClass('fadeOutDown'); }, 3000)
			}
			
			//if($('.dp_pec_submit_event', instance.calendar).length) {
				//$([instance.calendar, '.dpProEventCalendarModalEditEvent']).each(function() {
				
				instance.submit_event_hook(instance.calendar);
				//});
			//}
			
			$('.dp_pec_search_form', instance.calendar).submit(function() {
				if($(this).find('.dp_pec_search').val() != "" && !$('.dp_pec_content', instance.calendar).hasClass( 'dp_pec_content_loading' )) {
					instance._removeElements();
					
					$.post(ProEventCalendarAjax.ajaxurl, { 
						key: $(this).find('.dp_pec_search').val(), 
						calendar: instance.settings.calendar, 
						columns: instance.settings.columns, 
						calendar_per_date: instance.settings.calendar_per_date,
						author: instance.settings.author, 
						action: 'getSearchResults', 
						postEventsNonce : ProEventCalendarAjax.postEventsNonce 
					},
						function(data) {
							
							$('.dp_pec_content', instance.calendar).removeClass( 'dp_pec_content_loading' ).empty().html(data);

							instance.create_event_modal(true);
							
							instance.eventDates = $('.dp_pec_date', instance.calendar);
							
							instance.eventDates.hide().fadeIn(500);

							instance.create_isotope( '.dp_pec_search_results' );

						}
					);	
				}
				return false;
			});
			
			$('.dp_pec_icon_search', instance.calendar).click(function(e) {
				e.preventDefault();

				if($(this).parent().find('.dp_pec_content_search_input').val() != "" && !$('.dp_pec_content', instance.calendar).hasClass( 'dp_pec_content_loading' )) {
					instance._removeElements();
					var results_lang = $(this).data('results_lang');
					$('.events_loading', instance.calendar).show();
					
					$.post(ProEventCalendarAjax.ajaxurl, { 
						key: $(this).parent().find('.dp_pec_content_search_input').val(), 
						type: 'accordion', 
						calendar: instance.settings.calendar,
						calendar_per_date: instance.settings.calendar_per_date, 
						columns: instance.settings.columns, 
						author: instance.settings.author, 
						action: 'getSearchResults', 
						postEventsNonce : ProEventCalendarAjax.postEventsNonce 
					},
						function(data) {
							

							instance.accordion_update_content( data );

							$('.dp_pec_content', instance.calendar).removeClass( 'dp_pec_content_loading' );

							$('.actual_month', instance.calendar).text(results_lang);
							$('.return_layout', instance.calendar).show();
							$('.month_arrows', instance.calendar).hide();
							$('.events_loading', instance.calendar).hide();
							
						}
					);	
				}
				return false;
			});
			
			$('.return_layout', instance.calendar).click(function() {
				$(this).hide();
				$('.month_arrows', instance.calendar).show();
				$('.dp_pec_content_search_input', instance.calendar).val('');
				
				instance._changeMonth();
			});
			
			$(instance.calendar).on('click', '.dpProEventCalendar_load_more', function() {
				
				var items = $(this).parent().find('.dp_pec_isotope:not(.dp_pec_date_event_head,.dp_pec_date_block_wrap):hidden').slice(0,$(this).data('pagination'));
				
				items.show();
				
				$(this).parent().find('.dp_pec_isotope:not(.dp_pec_date_event_head):visible:last').prevAll('.dp_pec_date_block_wrap').show();	
				
				/*$.each($(this).parent().find('.dp_pec_isotope:not(.dp_pec_date_event_head)'), function(index) {
					
					if($(this).is(':visible')) {
						$(this).prevAll('.dp_pec_date_block_wrap').show();	
					}
				});*/
				
				if($(this).data('total') <= $(this).parent().find('.dp_pec_isotope:not(.dp_pec_date_event_head,.dp_pec_date_block_wrap):visible').length) {
					$(this).hide();
				}
				
				if(typeof instance.grid != "undefined") {
					instance.grid.isotope('appended', items);
				}

				return false;
				
			});
			
			$('.dp_pec_content_search_input', instance.calendar).keyup(function (e) 
			{
			
				if (e.keyCode == 13) 
				{
				
					// Do something
					$('.dp_pec_icon_search', instance.calendar).trigger('click');
				
				}
			
			});

			if( $('.pec_categories_list', instance.calendar).length )
			{

				instance.category = $('.pec_categories_list', instance.calendar).val();
			
				$('.pec_categories_list', instance.calendar).on('change', function() 
				{

					if(instance.category == $(this).val())
						return false;

					instance.category = $(this).val();

					$('.dp_pec_search_form', instance.calendar).find('.dp_pec_search').val('');
					
					instance._changeLayout();
					
					return false;

				});

			}

			if( $('.pec_speaker_list', instance.calendar).length )
			{

				instance.speaker = $('.pec_speaker_list', instance.calendar).val();

				$('.pec_speaker_list', instance.calendar).on('change', function() 
				{

					if( instance.speaker == $(this).val() )
						return false;

					instance.speaker = $(this).val();
				
					$('.dp_pec_search_form', instance.calendar).find('.dp_pec_search').val('');

					instance._changeLayout();
					
					return false;

				});

			}

			if( $('.pec_location_list', instance.calendar).length )
			{

				instance.location = $('.pec_location_list', instance.calendar).val();

				$('.pec_location_list', instance.calendar).on('change', function() 
				{

					if( instance.location == $(this).val() )
						return false;

					instance.location = $(this).val();
				
					$('.dp_pec_search_form', instance.calendar).find('.dp_pec_search').val('');

					instance._changeLayout();
					
					return false;

				});

			}
			
			$('.dp_pec_nav select.pec_switch_year', instance.calendar).on('change', function() 
			{
			
				$('.dp_pec_search_form', instance.calendar).find('.dp_pec_search').val('');
				instance.settings.actualYear = $(this).val();
				instance._changeMonth();
				return false;
			
			});
			
			$('.dp_pec_nav select.pec_switch_month', instance.calendar).on('change', function() 
			{
			
				instance.switch_month( $(this).val() )
				return false;
			
			});

			$('.month_year_dd li', instance.calendar).on('click', function() 
			{
			
				instance.switch_month( $(this).data( 'month' ) );
				return false;
			
			});
			
		},

		switch_month : function ( val ) {

			var instance = this;

			$('.dp_pec_search_form', instance.calendar).find('.dp_pec_search').val('');

			var changed_month = val;
		
			if(changed_month.indexOf('-') !== -1) 
			{
			
				var changed_month_split = changed_month.split('-');
				instance.settings.actualYear = parseInt(changed_month_split[1], 10);
				changed_month = changed_month_split[0];

			}

			for(i = 0; i < instance.settings.monthNames.length; i++) 
			{
			
				if(instance.settings.monthNames[i] == changed_month) 
				{
				
					instance.settings.actualMonth = i + 1;
				
				}
			
			}
			
			instance._changeMonth();

		},

		_open_event : function ( event_id, date ) {

			var instance = this;

			instance.show_overlay();

			if($('.dpProEventCalendarEventModal').length) 
			{
			
				$('.dpProEventCalendarEventModal').remove()
			
			}

			$('body').append(
						
				$('<div>').addClass('dpProEventCalendarEventModal').prepend(
				
					$('<a>').addClass('dp_pec_close').attr({ 'href': '#' }).html(instance.settings.lang_close)
				
				).show()
			
			);

			const $modal = $('.dpProEventCalendarEventModal');

			//Close modals
			instance.close_button($modal);

			var params = { 
				calendar: instance.settings.calendar, 
				event_id: event_id, 
				date: date, 
				action: 'getEventModal', 
				postEventsNonce : ProEventCalendarAjax.postEventsNonce 
			};

			instance.cache_param = JSON.stringify(params);

			if(typeof instance.cache[instance.cache_param] != 'undefined') {

				instance._get_event_modal( instance.cache[instance.cache_param], $modal );

			} else {

				$.post(ProEventCalendarAjax.ajaxurl, params, function(data) { instance._get_event_modal(data, $modal); });

			}

		},

		isMobile : function () { return ('ontouchstart' in document.documentElement); },

		_get_event_modal : function (data, modal) {

			var instance = this;

			if(typeof instance.cache[instance.cache_param] == 'undefined')
				instance.cache[instance.cache_param] = data;

			modal.append( data );

		},

		_goToByScroll : function(id){

		      // Scroll
		    $('html,body').animate({
		        scrollTop: (id.offset().top - 30)},
		        'slow');
		},

		detectItemVisibility: function( list, index ) {
	      var instance = this;
	      var $filteredLi = list.find('li');
	      var $ul = list.find('ul');

	      var itemsHeight = $ul.outerHeight();

	      var liHeight = $filteredLi.eq(index).outerHeight();
	      var liTop = $filteredLi[index].offsetTop;
	      var itemsScrollTop = $ul.scrollTop();
	      var scrollT = liTop + liHeight * 2;
	      
	      $ul.scrollTop(
	        scrollT > itemsScrollTop + itemsHeight ? scrollT - itemsHeight :
	          liTop - liHeight < itemsScrollTop ? liTop - liHeight :
	            itemsScrollTop
	      );
	    },
		
		_pec_update_location : function(val, elem) {
			
			var instance = this;

			jQuery('.pec_location_options', elem).hide();
			
			switch(val) {

				case "-1":
					jQuery(".pec_location_options", elem).show();
					break;	

			}
			

		},

		_pec_update_frequency : function(val, elem) {
			
			jQuery('.pec_daily_frequency', elem).hide();
			jQuery('.pec_weekly_frequency', elem).hide();
			jQuery('.pec_monthly_frequency', elem).hide();
			
			switch(val) {
				case "1":
					jQuery(".pec_daily_frequency", elem).show();
					jQuery(".pec_weekly_frequency", elem).hide();
					jQuery(".pec_monthly_frequency", elem).hide();
					break;	
				case "2":
					jQuery(".pec_daily_frequency", elem).hide();
					jQuery(".pec_weekly_frequency", elem).show();
					jQuery(".pec_monthly_frequency", elem).hide();
					break;	
				case "3":
					jQuery(".pec_daily_frequency", elem).hide();
					jQuery(".pec_weekly_frequency", elem).hide();
					jQuery(".pec_monthly_frequency", elem).show();
					break;	
				case "4":
					jQuery(".pec_daily_frequency", elem).hide();
					jQuery(".pec_weekly_frequency", elem).hide();
					jQuery(".pec_monthly_frequency", elem).hide();
					break;	
			}
		},
						
		_makeResponsive : function() {
			var instance = this;
			
			if(instance.calendar.width() < 500) {

				$(instance.calendar).addClass('dp_pec_400');

				$('.dp_pec_dayname span', instance.calendar).each(function(i) {
					if($(this).closest('.dp_pec_responsive_weekly').length == 0)
						$(this).html($(this).html().substr(0,3));
				});
				
				$('.prev_month strong', instance.calendar).hide();
				$('.next_month strong', instance.calendar).hide();
				$('.prev_day strong', instance.calendar).hide();
				$('.next_day strong', instance.calendar).hide();
				
			} else {
				$(instance.calendar).removeClass('dp_pec_400');

				$('.prev_month strong', instance.calendar).show();
				$('.next_month strong', instance.calendar).show();
				$('.prev_day strong', instance.calendar).show();
				$('.next_day strong', instance.calendar).show();
				
			}
		},
		_removeElements : function () {
			var instance = this;

			$('.dpProEventCalendar_load_more', instance.calendar).hide();
			
			$('.dp_pec_date, .dp_pec_date_weekly_time, .dp_pec_daily_grid, .dp_pec_dayname, .dp_pec_isotope', instance.calendar).fadeTo(500, .1);
			$('.dp_pec_monthly_row, .dp_pec_monthly_row_space, .dp_pec_responsive_weekly', instance.calendar).hide();
			$('.dp_pec_content', instance.calendar).addClass( 'dp_pec_content_loading' );
			$('.eventsPreviewDiv').html('').hide();

		},
		
		_prevMonth : function (instance) {
			if(!$('.dp_pec_content', instance.calendar).hasClass( 'dp_pec_content_loading' )) {
				instance.settings.actualMonth--;
				instance.settings.actualMonth = instance.settings.actualMonth == 0 ? 12 : (instance.settings.actualMonth);
				instance.settings.actualYear = instance.settings.actualMonth == 12 ? instance.settings.actualYear - 1 : instance.settings.actualYear;
				
				instance._changeMonth();
			}
		},
		
		_nextMonth : function (instance) {

			if(!$('.dp_pec_content', instance.calendar).hasClass( 'dp_pec_content_loading' )) {
				instance.settings.actualMonth++;
				instance.settings.actualMonth = instance.settings.actualMonth == 13 ? 1 : (instance.settings.actualMonth);
			
				instance.settings.actualYear = instance.settings.actualMonth == 1 ? instance.settings.actualYear + 1 : instance.settings.actualYear;
				
				instance._changeMonth();
			}
		},
		
		_prevDay : function (instance) {
			if(!$('.dp_pec_content', instance.calendar).hasClass( 'dp_pec_content_loading' )) {
				instance.settings.actualDay--;
				//instance.settings.actualDay = instance.settings.actualDay == 0 ? 12 : (instance.settings.actualDay);
				
				instance._changeDay();
			}
		},
		
		_nextDay : function (instance) {
			if(!$('.dp_pec_content', instance.calendar).hasClass( 'dp_pec_content_loading' )) {
				instance.settings.actualDay++;
				//instance.settings.actualDay = instance.settings.actualDay == 13 ? 1 : (instance.settings.actualDay);
	
				instance._changeDay();
			}
		},
		
		_prevWeek : function (instance) {
			if(!$('.dp_pec_content', instance.calendar).hasClass( 'dp_pec_content_loading' )) {
				instance.settings.actualDay -= 7;
				//instance.settings.actualDay = instance.settings.actualDay == 0 ? 12 : (instance.settings.actualDay);
				
				instance._changeWeek();
			}
		},
		
		_nextWeek : function (instance) {

			if(!$('.dp_pec_content', instance.calendar).hasClass( 'dp_pec_content_loading' )) {
				instance.settings.actualDay += 7;
				//instance.settings.actualDay = instance.settings.actualDay == 13 ? 1 : (instance.settings.actualDay);
	
				instance._changeWeek();
			}
		},

		_today : function (instance) {

			if( ! $('.dp_pec_content', instance.calendar).hasClass( 'dp_pec_content_loading' ) ) {
				instance.settings.actualDay = instance.orig_settings.actualDay;
				instance.settings.actualMonth = instance.orig_settings.actualMonth;
				instance.settings.actualYear = instance.orig_settings.actualYear;
	
				instance._changeLayout();
			}
		},

		_changeYear : function () {

			var instance = this;

			$('.dp_pec_yearly_calendar', instance.calendar).addClass( 'dp_pec_content_loading' );

			var params = { 
				year: instance.settings.actualYear, 
				calendar: instance.settings.calendar, 
				category: (instance.settings.category != "" ? instance.settings.category : $('select.pec_categories_list', instance.calendar).val()), 
				location: (instance.settings.location != "" ? instance.settings.location : $('select.pec_location_list', instance.calendar).val()), 
				speaker: (instance.settings.speaker != "" ? instance.settings.speaker : $('select.pec_speaker_list', instance.calendar).val()), 
				is_admin: instance.settings.isAdmin, 
				event_id: instance.settings.event_id, 
				author: instance.settings.author, 
				include_all_events: instance.settings.include_all_events,
				modal: instance.settings.modal,
				hide_old_dates: instance.settings.hide_old_dates,
				type: instance.settings.type, 
				action: 'getYear', 
				postEventsNonce : ProEventCalendarAjax.postEventsNonce 
			};

			instance.cache_param = JSON.stringify(params);

			if(typeof instance.cache[instance.cache_param] != 'undefined') {

				instance._get_year( instance.cache[instance.cache_param] );

			} else {

				$.post(ProEventCalendarAjax.ajaxurl, params, function(data) { instance._get_year(data); });

			}
		},
		
		_changeMonth : function () {
			var instance = this;
			
			$('.dp_pec_nav_monthly', instance.calendar).show();
			$('.actual_month', instance.calendar).html( instance.settings.monthNames[(instance.settings.actualMonth - 1)] + ' ' + instance.settings.actualYear );
			
			if($('.dp_pec_nav select.pec_switch_month', instance.calendar).length) {

				var month_val = $('.dp_pec_nav select.pec_switch_month', instance.calendar).val();
				if( month_val.indexOf('-') !== -1 ) {

					$('.dp_pec_nav select.pec_switch_month', instance.calendar).val(instance.settings.monthNames[(instance.settings.actualMonth - 1)] + '-' + instance.settings.actualYear);

				} else {

					$('.dp_pec_nav select.pec_switch_month', instance.calendar).val(instance.settings.monthNames[(instance.settings.actualMonth - 1)]);

				}
			}

			$('.dp_pec_nav select.pec_switch_year', instance.calendar).val(instance.settings.actualYear);
			$('.dp_pec_nav select', instance.calendar).selectric('refresh');
			
			instance._removeElements();

			//Hide search
			instance.show_search(true);
			
			if(instance.settings.dateRangeStart && instance.settings.dateRangeStart.substr(0, 7) == instance.settings.actualYear+"-"+instance._str_pad(instance.settings.actualMonth, 2, "0", 'STR_PAD_LEFT') && !instance.settings.isAdmin) {
				$('.prev_month', instance.calendar).hide();
			} else {
				$('.prev_month', instance.calendar).show();
			}

			if(instance.settings.dateRangeEnd && instance.settings.dateRangeEnd.substr(0, 7) == instance.settings.actualYear+"-"+instance._str_pad(instance.settings.actualMonth, 2, "0", 'STR_PAD_LEFT') && !instance.settings.isAdmin) {
				$('.next_month', instance.calendar).hide();
			} else {
				$('.next_month', instance.calendar).show();
			}
			
			var date_timestamp = Date.UTC(instance.settings.actualYear, (instance.settings.actualMonth - 1), 15) / 1000;
			
			if(instance.settings.type == "accordion") {

				if( $('.pec-month-wrap ul', instance.calendar).length ) {
					$month_list = $('.pec-month-wrap ul', instance.calendar);
					$month_list.find( 'li.pec-active' ).removeClass( 'pec-active' );
					$month_list.find( 'li[data-month=' + instance.settings.monthNames[(instance.settings.actualMonth - 1)]+'-'+instance.settings.actualYear + ']' ).addClass( 'pec-active' );

				}

				$('.events_loading', instance.calendar).show();

				var params = { 
					month: instance.settings.actualMonth, 
					year: instance.settings.actualYear, 
					calendar: instance.settings.calendar, 
					columns: instance.settings.columns, 
					limit: instance.settings.limit, 
					widget: instance.settings.widget, 
					category: (instance.settings.category != "" ? instance.settings.category : $('select.pec_categories_list', instance.calendar).val()),
					location: (instance.settings.location != "" ? instance.settings.location : $('select.pec_location_list', instance.calendar).val()), 
					speaker: (instance.settings.speaker != "" ? instance.settings.speaker : $('select.pec_speaker_list', instance.calendar).val()), 
					event_id: instance.settings.event_id, 
					author: instance.settings.author, 
					include_all_events: instance.settings.include_all_events,
					modal: instance.settings.modal,
					hide_old_dates: instance.settings.hide_old_dates,
					action: 'getEventsMonthList', 
					postEventsNonce : ProEventCalendarAjax.postEventsNonce 
				};

				instance.cache_param = JSON.stringify(params);

				if(typeof instance.cache[instance.cache_param] != 'undefined') {

					instance._get_events_month_list( instance.cache[instance.cache_param] );

				} else {

					$.post(ProEventCalendarAjax.ajaxurl, params, function(data) { instance._get_events_month_list(data); });

				}

			} else {
				if(instance.monthlyView == "calendar") {
					var start = new Date().getTime(); // note getTime()

					var params = { 
						date: date_timestamp, 
						calendar: instance.settings.calendar, 
						category: (instance.settings.category != "" ? instance.settings.category : $('select.pec_categories_list', instance.calendar).val()), 
						location: (instance.settings.location != "" ? instance.settings.location : $('select.pec_location_list', instance.calendar).val()), 
						speaker: (instance.settings.speaker != "" ? instance.settings.speaker : $('select.pec_speaker_list', instance.calendar).val()), 
						is_admin: instance.settings.isAdmin, 
						event_id: instance.settings.event_id, 
						calendar_per_date: instance.settings.calendar_per_date,
						author: instance.settings.author, 
						include_all_events: instance.settings.include_all_events,
						modal: instance.settings.modal,
						hide_old_dates: instance.settings.hide_old_dates,
						type: instance.settings.type, 
						action: 'getDate', 
						postEventsNonce : ProEventCalendarAjax.postEventsNonce 
					};

					instance.cache_param = JSON.stringify(params);

					if(typeof instance.cache[instance.cache_param] != 'undefined') {

						instance._get_date( instance.cache[instance.cache_param] );

					} else {

						$.post(ProEventCalendarAjax.ajaxurl, params, function(data) { instance._get_date(data); });

					}
					
				} else {

					var params = { 
						month: instance.settings.actualMonth, 
						year: instance.settings.actualYear, 
						calendar: instance.settings.calendar, 
						category: (instance.settings.category != "" ? instance.settings.category : $('select.pec_categories_list', instance.calendar).val()), 
						location: (instance.settings.location != "" ? instance.settings.location : $('select.pec_location_list', instance.calendar).val()), 
						speaker: (instance.settings.speaker != "" ? instance.settings.speaker : $('select.pec_speaker_list', instance.calendar).val()), 
						event_id: instance.settings.event_id, 
						calendar_per_date: instance.settings.calendar_per_date,
						author: instance.settings.author, 
						include_all_events: instance.settings.include_all_events,
						modal: instance.settings.modal,
						hide_old_dates: instance.settings.hide_old_dates,
						action: 'getEventsMonth', 
						postEventsNonce : ProEventCalendarAjax.postEventsNonce 
					}; 

					instance.cache_param = JSON.stringify(params);

					if(typeof instance.cache[instance.cache_param] != 'undefined') {

						instance._get_events_month( instance.cache[instance.cache_param] );

					} else {

						$.post(ProEventCalendarAjax.ajaxurl, params, function(data) { instance._get_events_month(data); });

					}
				
				}
			}
			
			
		},

		_get_events_month : function (data) {

			var instance = this;

			if(typeof instance.cache[instance.cache_param] == 'undefined')
				instance.cache[instance.cache_param] = data;

			instance.create_event_modal(true);

			$('.dp_pec_content', instance.calendar).removeClass( 'dp_pec_content_loading' ).empty().html(data);
			$(instance.calendar).removeClass('dp_pec_daily');
			$(instance.calendar).removeClass('dp_pec_weekly');
			$(instance.calendar).addClass('dp_pec_'+instance.view);

			$('.dp_pec_wrapper', instance.calendar).removeClass('dp_pec_daily');
			$('.dp_pec_wrapper', instance.calendar).removeClass('dp_pec_weekly');
			$('.dp_pec_wrapper', instance.calendar).addClass('dp_pec_'+instance.view);
			
			instance.eventDates = $('.dp_pec_date', instance.calendar);
			
			$('.dp_pec_date', instance.calendar).hide().fadeIn(500);
			instance._makeResponsive();

		},

		_get_events_month_list : function (data) {

			var instance = this;

			if(typeof instance.cache[instance.cache_param] == 'undefined')
				instance.cache[instance.cache_param] = data;

			$('.events_loading', instance.calendar).hide();
						
			instance.accordion_update_content( data );

			$('.dp_pec_content', instance.calendar).removeClass( 'dp_pec_content_loading' );

		},

		_get_date : function (data) {

			var instance = this;

			if(typeof instance.cache[instance.cache_param] == 'undefined')
				instance.cache[instance.cache_param] = data;

			$('.dp_pec_content', instance.calendar).removeClass( 'dp_pec_content_loading' ).empty().html(data);
							
			var dppec_date = $('.dp_pec_content', instance.calendar).find(".dp_pec_date[data-dppec-date='" + instance.settings.defaultDateFormat + "']");
			
			var dppec_date_item = dppec_date.find('.dp_pec_date_item');
			if(typeof dppec_date_item.attr('style') == 'undefined' && instance.settings.show_current_date && instance.settings.current_date_color != "") {
				dppec_date.addClass('dp_pec_special_date');
				dppec_date_item.css('background-color', instance.settings.current_date_color);
				$('.dp_special_date_dot', dppec_date_item).css('background-color', instance.settings.current_date_color);
			}

			$(instance.calendar).removeClass('dp_pec_daily');
			$(instance.calendar).removeClass('dp_pec_weekly');
			$(instance.calendar).addClass('dp_pec_'+instance.view);

			$('.dp_pec_wrapper', instance.calendar).removeClass('dp_pec_daily');
			$('.dp_pec_wrapper', instance.calendar).removeClass('dp_pec_weekly');
			$('.dp_pec_wrapper', instance.calendar).addClass('dp_pec_'+instance.view);

			instance.eventDates = $('.dp_pec_date', instance.calendar);
			
			
			// Load time debug
	        //console.log( end - start );
			
			$('.dp_pec_date', instance.calendar).hide().show();
			instance._makeResponsive();

		},

		_get_year : function (data) {

			var instance = this;

			if(typeof instance.cache[instance.cache_param] == 'undefined')
				instance.cache[instance.cache_param] = data;

			$('.dp_pec_yearly_calendar', instance.calendar).removeClass( 'dp_pec_content_loading' ).empty().html(data);
			instance._makeResponsive();
		
		},

		_get_events : function (data) {

			var instance = this;

			if(typeof instance.cache[instance.cache_param] == 'undefined')
				instance.cache[instance.cache_param] = data;

			instance.display_results_events( data );

		},

		_get_event : function (data) {

			var instance = this;

			if(typeof instance.cache[instance.cache_param] == 'undefined')
				instance.cache[instance.cache_param] = data;

			instance.display_results_events( data );

		},
		
		_changeDay : function () {
			var instance = this;
			
			$('.dp_pec_nav_daily', instance.calendar).show();
						
			//$('span.actual_month', instance.calendar).html( instance.settings.monthNames[(instance.settings.actualMonth - 1)] + ' ' + instance.settings.actualYear );

			instance._removeElements();
						
			var date_timestamp = Date.UTC(instance.settings.actualYear, (instance.settings.actualMonth - 1), (instance.settings.actualDay)) / 1000;

			const daily_param = { 

				date: date_timestamp, 
				calendar: instance.settings.calendar, 
				category: (instance.settings.category != "" ? instance.settings.category : $('select.pec_categories_list', instance.calendar).val()), 
				location: (instance.settings.location != "" ? instance.settings.location : $('select.pec_location_list', instance.calendar).val()), 
				speaker: (instance.settings.speaker != "" ? instance.settings.speaker : $('select.pec_speaker_list', instance.calendar).val()), 
				event_id: instance.settings.event_id, 
				calendar_per_date: instance.settings.calendar_per_date,
				author: instance.settings.author, 
				columns: instance.settings.columns, 
				include_all_events: instance.settings.include_all_events,
				modal: instance.settings.modal,
				hide_old_dates: instance.settings.hide_old_dates,
				is_admin: instance.settings.isAdmin, 
				action: 'getDaily', 
				postEventsNonce : ProEventCalendarAjax.postEventsNonce 

			}

			instance.cache_param = JSON.stringify(daily_param);

			if(typeof instance.cache[instance.cache_param] != 'undefined') {

				instance._get_daily( instance.cache[instance.cache_param] );

			} else {

				$.post(ProEventCalendarAjax.ajaxurl, daily_param, function(data) { instance._get_daily(data); });

			}

			
			
			
		},

		_get_daily : function(data) {

			var instance = this;

			if(typeof instance.cache[instance.cache_param] == 'undefined')
				instance.cache[instance.cache_param] = data;

			var newDate = data.substr(0, data.indexOf(">!]-->")).replace("<!--", "");
			$('span.actual_day', instance.calendar).html( newDate );
			
			$('.dp_pec_content', instance.calendar).removeClass( 'dp_pec_content_loading' ).empty().html(data);
			$(instance.calendar).removeClass('dp_pec_monthly');
			$(instance.calendar).removeClass('dp_pec_weekly');
			$(instance.calendar).addClass('dp_pec_daily');

			$('.dp_pec_wrapper', instance.calendar).removeClass('dp_pec_monthly');
			$('.dp_pec_wrapper', instance.calendar).removeClass('dp_pec_weekly');
			$('.dp_pec_wrapper', instance.calendar).addClass('dp_pec_daily');

			instance.eventDates = $('.dp_pec_date', instance.calendar);
			
			$('.dp_pec_date', instance.calendar).hide().show();
			instance._makeResponsive();

		},
		
		_changeWeek : function () {
			var instance = this;
			
			$('.dp_pec_nav_weekly', instance.calendar).show();
						
			//$('span.actual_month', instance.calendar).html( instance.settings.monthNames[(instance.settings.actualMonth - 1)] + ' ' + instance.settings.actualYear );

			instance._removeElements();
						
			var date_timestamp = Date.UTC(instance.settings.actualYear, (instance.settings.actualMonth - 1), (instance.settings.actualDay)) / 1000;

			const weekly_param = { 
				date: date_timestamp, 
				calendar: instance.settings.calendar, 
				category: (instance.settings.category != "" ? instance.settings.category : $('select.pec_categories_list', instance.calendar).val()), 
				location: (instance.settings.location != "" ? instance.settings.location : $('select.pec_location_list', instance.calendar).val()), 
				speaker: (instance.settings.speaker != "" ? instance.settings.speaker : $('select.pec_speaker_list', instance.calendar).val()), 
				event_id: instance.settings.event_id, 
				author: instance.settings.author, 
				calendar_per_date: instance.settings.calendar_per_date,
				include_all_events: instance.settings.include_all_events,
				modal: instance.settings.modal,
				hide_old_dates: instance.settings.hide_old_dates,
				is_admin: instance.settings.isAdmin, 
				action: 'getWeekly', 
				postEventsNonce : ProEventCalendarAjax.postEventsNonce 
			};

			instance.cache_param = JSON.stringify(weekly_param);

			if(typeof instance.cache[instance.cache_param] != 'undefined') {

				instance._get_weekly( instance.cache[instance.cache_param] );

			} else {

				$.post(ProEventCalendarAjax.ajaxurl, weekly_param, function(data) { instance._get_weekly(data); });

			}
			
			
		},

		_get_weekly : function(data) {

			var instance = this;

			if(typeof instance.cache[instance.cache_param] == 'undefined')
				instance.cache[instance.cache_param] = data;

			var newDate = data.substr(0, data.indexOf(">!]-->")).replace("<!--", "");
			$('span.actual_week', instance.calendar).html( newDate );
			
			$('.dp_pec_content', instance.calendar).removeClass( 'dp_pec_content_loading' ).empty().html(data);

			$('.dp_daily_event', instance.calendar).each(function () {
				if($(this).width() > 0 && $(this).width() < 80)
					$(this).find('span').hide();
			});

			$(instance.calendar).removeClass('dp_pec_monthly');
			$(instance.calendar).removeClass('dp_pec_daily');
			$(instance.calendar).addClass('dp_pec_weekly');

			$('.dp_pec_wrapper', instance.calendar).removeClass('dp_pec_monthly');
			$('.dp_pec_wrapper', instance.calendar).removeClass('dp_pec_daily');
			$('.dp_pec_wrapper', instance.calendar).addClass('dp_pec_weekly');

			instance.eventDates = $('.dp_pec_date', instance.calendar);
			
			$('.dp_pec_date', instance.calendar).hide().show();
			instance._makeResponsive();

		},
		
		toggle_filter : function ( $btn ) {

			var instance = this;

			if( $btn.hasClass( 'dp_pec_btn_active' ) )
				$($btn).removeClass( 'dp_pec_btn_active' );
			else
				$($btn).addClass( 'dp_pec_btn_active' );

			$('.dp_pec_nav', instance.calendar).toggle();

		},

		_changeLayout : function () {
			var instance = this;
			
			instance._removeElements();
			
			//Hide search
			instance.show_search(true);

			if( instance.settings.type != 'compact' && instance.settings.type != 'modern' && instance.settings.type != 'accordion' && instance.settings.type != 'yearly' ) 
				$('.dp_pec_nav', instance.calendar).hide();

			if(instance.settings.type == "yearly") {
				instance._changeYear();
				return;
			}

			if(instance.view == "monthly" || instance.view == "monthly-all-events") {
				instance._changeMonth();
				return;
			}
			
			if(instance.view == "daily") {
				instance._changeDay();
				return;
			}
			
			if(instance.view == "weekly") {
				instance._changeWeek();
				return;
			}
			
		},

		display_results_events: function ( data )
		{

			var instance = this;

			$('.dp_pec_content', instance.calendar).removeClass( 'dp_pec_content_loading' ).empty().html(data);
			
			instance.eventDates = $('.dp_pec_date', instance.calendar);
			
			$('.dp_pec_date', instance.calendar).hide().fadeIn(500);

			instance.create_event_modal(true);


		},

		accordion_month_dropdown: function()
		{

			var instance = this;

			$('.pec-dropdown-month', instance.calendar).click(function(e) {
				e.preventDefault();

				var $list = $(this).next( '.month_year_dd' );

				if( !$(this).hasClass('pec_active') ) {
					$(this).addClass('pec_active');

					
					var index = $list.find('.pec-active').data('pec-index');
					$list.show();

					instance.detectItemVisibility( $list, index );

				} else {
					$(this).removeClass('pec_active');
					$list.hide();
				}
				
			});

		},

		more_dates: function()
		{

			var instance = this;

			$('.pec_event_page_action', instance.calendar).click(function(e) {
				e.preventDefault();

				var $list = $('.pec_event_page_action_menu', instance.calendar);

				if(!$(this).hasClass('active')) {
					$(this).addClass('active');

					var index = $list.find('.pec_event_page_action_menu_active').data('pec-index');
					$list.show();

					instance.detectItemVisibility( $list, index );

				} else {
					$(this).removeClass('active');
					$list.hide();
				}
				
			});

		},

		starts_in: function ()
		{

			var instance = this;

			var elem = $('.pec_event_page_date', instance.calendar).find('span.pec_starts_in');

			var launchDate = new Date(
				elem.data('countdown-year'), 
				(elem.data('countdown-month') - 1), 
				elem.data('countdown-day'), 
				elem.data('countdown-hour'), 
				elem.data('countdown-minute')
			);

			var remaining_text = elem.data('countdown-remaining');

			var myTZO = elem.data('tzo');

			var currentTime = new Date(), differenceTime;
			//var currentTime = new Date(currentDate.getTime()), differenceTime;
			currentTime_getTime = currentTime.getTime();
			differenceTime = new Date(launchDate.getTime() - currentTime.getTime() + (1000 * 60 * ( myTZO - currentTime.getTimezoneOffset())));
			//differenceTime = new Date(launchDate.getTime() - currentTime_getTime );

			var d = Math.floor(Math.abs((launchDate.getTime() - currentTime.getTime() + (1000 * 60 * ( myTZO - currentTime.getTimezoneOffset()))) / (24*60*60*1000)));
			//var d = Math.floor(Math.abs((launchDate.getTime() - currentTime_getTime ) / (24*60*60*1000)));
			var h = differenceTime.getUTCHours();
			var m = differenceTime.getUTCMinutes();

			var started = false;
			
			if( differenceTime.getTime() < 0 ) {
				d = 0;
				h = 0;
				m = 0;

				started = true;

			}

			if( started ) {

				$('.pec_event_page_date p.pec_event_page_starts', instance.calendar).hide();
				$('.pec_event_page_date p.pec_event_page_date_starts_in', instance.calendar).hide();

			} else {


				var string_format = "";

				if(d > 0) {

					var days_txt = instance.settings.translate.days;
					if(d == 1) {
						days_txt = instance.settings.translate.day;
					} 

					string_format += '<strong>' + d + '</strong> '+ days_txt +' ';

				}

				if(h > 0 && d < 3) {

					var hour_txt = instance.settings.translate.hours;
					if(h == 1) {
						hour_txt = instance.settings.translate.hour;
					} 

					string_format += '<strong>' + h + '</strong> '+hour_txt+' ';

				}

				if(d == 0) {

					var minutes_txt = instance.settings.translate.minutes;
					if(m == 1) {
						minutes_txt = instance.settings.translate.minute;
					} 

					string_format += '<strong>' + m + '</strong> '+minutes_txt;

				}

				//string_format += ' ' + remaining_text;

				$('.pec_event_page_date', instance.calendar).find('span.pec_starts_in').html( string_format.toLowerCase() );
			}

		},

		_setup_countdown: function (launchDate, currentDate, element, myTZO) {
			var instance = this;
			var seconds_sum = 0;
			setInterval(function(){
				seconds_sum += 1000;

				//var currentTime = new Date(), differenceTime;
				var currentTime = new Date(currentDate.getTime()), differenceTime;
				currentTime_getTime = currentTime.getTime() + seconds_sum;
				//differenceTime = new Date(launchDate.getTime() - currentTime.getTime() + (1000 * 60 * ( myTZO - currentTime.getTimezoneOffset())));
				differenceTime = new Date(launchDate.getTime() - currentTime_getTime );

				//var d = Math.floor(Math.abs((launchDate.getTime() - currentTime.getTime() + (1000 * 60 * ( myTZO - currentTime.getTimezoneOffset()))) / (24*60*60*1000)));
				var d = Math.floor(Math.abs((launchDate.getTime() - currentTime_getTime ) / (24*60*60*1000)));
				var h = differenceTime.getUTCHours();
				var m = differenceTime.getUTCMinutes();
				var s = differenceTime.getUTCSeconds();
				
				if( differenceTime.getTime() < 0 ) {
					d = 0;
					h = 0;
					m = 0;
					s = 0;
				}
				//console.log(differenceTime.getTime());

				$('.dp_pec_countdown .dp_pec_countdown_days', $(element)).html(instance._str_pad(d, 2, "0", "STR_PAD_LEFT"));
				if(d == 0) {
					$('.dp_pec_countdown .dp_pec_countdown_days_wrap, .dp_pec_countdown .dp_pec_countdown_days_wrap *', $(element)).hide();
				}
				if(d == 1) { 
					$('.dp_pec_countdown .dp_pec_countdown_days_wrap .dp_pec_countdown_days_txt', $(element)).text($('.dp_pec_countdown .dp_pec_countdown_days_wrap .dp_pec_countdown_days_txt').data('day'));
				} else {
					$('.dp_pec_countdown .dp_pec_countdown_days_wrap .dp_pec_countdown_days_txt', $(element)).text($('.dp_pec_countdown .dp_pec_countdown_days_wrap .dp_pec_countdown_days_txt').data('days'));
				}

				$('.dp_pec_countdown .dp_pec_countdown_hours', $(element)).html(instance._str_pad(h, 2, "0", "STR_PAD_LEFT"));

				if(h == 1) { 
					$('.dp_pec_countdown .dp_pec_countdown_hours_wrap .dp_pec_countdown_hours_txt', $(element)).text($('.dp_pec_countdown .dp_pec_countdown_hours_wrap .dp_pec_countdown_hours_txt').data('hour'));
				} else {
					$('.dp_pec_countdown .dp_pec_countdown_hours_wrap .dp_pec_countdown_hours_txt', $(element)).text($('.dp_pec_countdown .dp_pec_countdown_hours_wrap .dp_pec_countdown_hours_txt').data('hours'));
				}

				$('.dp_pec_countdown .dp_pec_countdown_minutes', $(element)).html(instance._str_pad(m, 2, "0", "STR_PAD_LEFT"));
				$('.dp_pec_countdown .dp_pec_countdown_seconds', $(element)).html(instance._str_pad(s, 2, "0", "STR_PAD_LEFT"));
				

			},1000);

		},

		init_tooltips: function() {

			var instance = this;
			$( '[data-pec-tooltip]', instance.calendar ).each( function() {
				var $tooltip = $( this );

				if( $tooltip.find( '.dp-pec-tooltip' ).length )
					return;

				var text = $tooltip.data( 'pec-tooltip' );

				if( text != "" )
					$tooltip.append( $( '<div>' ).addClass( 'dp-pec-tooltip' ).text( text ) );

			});

		},
		
		_str_pad: function (input, pad_length, pad_string, pad_type) {
			
			var half = '',
				pad_to_go;
		 
			var str_pad_repeater = function (s, len) {
				var collect = '',
					i;
		 
				while (collect.length < len) {
					collect += s;
				}
				collect = collect.substr(0, len);
		 
				return collect;
			};
		 
			input += '';
			pad_string = pad_string !== undefined ? pad_string : ' ';
		 
			if (pad_type != 'STR_PAD_LEFT' && pad_type != 'STR_PAD_RIGHT' && pad_type != 'STR_PAD_BOTH') {
				pad_type = 'STR_PAD_RIGHT';
			}
			if ((pad_to_go = pad_length - input.length) > 0) {
				if (pad_type == 'STR_PAD_LEFT') {
					input = str_pad_repeater(pad_string, pad_to_go) + input;
				} else if (pad_type == 'STR_PAD_RIGHT') {
					input = input + str_pad_repeater(pad_string, pad_to_go);
				} else if (pad_type == 'STR_PAD_BOTH') {
					half = str_pad_repeater(pad_string, Math.ceil(pad_to_go / 2));
					input = half + input + half;
					input = input.substr(0, pad_length);
				}
			}
		 
			return input;
		},

		remove_event: function ( )
		{

			var instance = this;

			$(instance.calendar).on('click', '.pec_remove_event', function(e) {

				$('body, html').css('overflow', 'hidden');
				
				if(!$('.dpProEventCalendarModalEditEvent').length) {
			
					$('body').append(
						$('<div>').addClass('dpProEventCalendarModalEditEvent dpProEventCalendarModalSmall dp_pec_new_event_wrapper').prepend(
							$('<h2>').text($(this).text()).append(
								$('<a>').addClass('dpProEventCalendarClose').attr({ 'href': '#' }).html('<i class="fa fa-times"></i>')
							)
						).append(
							$('<div>').addClass('dpProEventCalendar_eventform').append($(this).next().children().clone(true))
						).show()
					);
					
					instance.show_overlay();
					
				} else {
					$('.dpProEventCalendar_eventform').html($(this).next().html());
					$('.dpProEventCalendarModalEditEvent').addClass('dpProEventCalendarModalSmall');
					$('.dpProEventCalendarModalEditEvent h2').text($(this).text()).append(
						$('<a>').addClass('dpProEventCalendarClose').attr({ 'href': '#' }).html('<i class="fa fa-times"></i>')
					);
					$('.dpProEventCalendarModalEditEvent, .dpProEventCalendarOverlay').show();
				}

				$modal = $('.dpProEventCalendarModalEditEvent');

				instance.close_button($modal);
				
				$('.dpProEventCalendarModalEditEvent').on('click', '.dp_pec_remove_event', function(e) {

					e.preventDefault();
					$(this).addClass('dp_pec_disabled');
					var form = $(this).closest(".add_new_event_form");
					
					var origName = $(this).html();
					$(this).html(instance.settings.lang_sending);
					var me = this;
					var form = $(this).closest('form');
					var post_obj = {
						calendar: instance.settings.calendar, 
						action: 'removeEvent',
						postEventsNonce : ProEventCalendarAjax.postEventsNonce
					}

					$(this).closest(".add_new_event_form").ajaxForm({
						url: ProEventCalendarAjax.ajaxurl,
						data: post_obj,
						success:function(data){
							$(me).html(origName);
							location.reload();	

							$(me).removeClass('dp_pec_disabled');

						}
					}).submit();
				});		

				return false;

			});

		},

		open_options: function ( )
		{

			var instance = this;
			
			$(instance.calendar).on('click', '.dp_pec_more_options', function(e) {

				e.preventDefault();
				
				if(!$(this).hasClass('dp_pec_active')) 
				{
				
					$(this).addClass('dp_pec_active');
					$(this).next('.dp_pec_more_options_hidden').slideDown(200);
				
				} else {
				
					$(this).removeClass('dp_pec_active');
					$(this).next('.dp_pec_more_options_hidden').slideUp(200);
				
				}
				
			});

			$(document).mouseup(function(e) 
			{
				if($(e.target).hasClass('dp_pec_more_options')) { return; }

			    $('.dp_pec_more_options', instance.calendar).removeClass('dp_pec_active');
				$('.dp_pec_more_options_hidden', instance.calendar).slideUp(200);

				// For Single Page
				if( $( '.pec_event_page_action' ).hasClass( 'active' ) && !$(e.target).hasClass('pec_event_page_action') && !$(e.target).hasClass('fa-plus') )
					$( '.pec_event_page_action' ).trigger( 'click' );

				// For Accordion layout
				if( instance.type = 'accordion' && $( '.pec-dropdown-month' ).hasClass( 'pec_active' ) && !$(e.target).hasClass('pec-dropdown-month') && !$(e.target).hasClass('fa-chevron-down') )
					$( '.pec-dropdown-month.pec_active' ).trigger( 'click' );
			
			});

		},

		close_modal: function ( $close_btn ) 
		{

			var instance = this;

			if($('.dpProEventCalendarMapModal').is(':visible')) {

				const modal = $('.dpProEventCalendarMapModal');
				const modal_canvas = modal.find('.dp_pec_date_event_map_canvas');

				$('#'+modal.data('button'), instance.calendar).next('.dp_pec_open_map_wrap').prepend(modal_canvas);

			}

			if($('.dpProEventCalendar_Image').is(':visible')) {

				$('.dpProEventCalendar_Image').remove();

			}
			

			$('.dpProEventCalendarModalEditEvent, .dpProEventCalendarModal, .dpProEventCalendarMapModal, .dpProEventCalendarEventModal, .dpProEventCalendarOverlay').fadeOut('fast');

			$('body, html').css('overflow', '');

			if( typeof $close_btn != 'undefined' )
			{
	
				$( $close_btn ).off('click', '.dpProEventCalendarClose, .dp_pec_close');

			}

		},

		close_button: function( $modal ) {

			var instance = this;

			$( $modal ).on('click', '.dpProEventCalendarClose, .dp_pec_close', function(e) {
				e.preventDefault();
				

				instance.close_modal( $modal );


			});



		},

		accordion_update_content: function  ( data ) 
		{

			var instance = this;


			if(typeof instance.grid != "undefined") {
								
				//instance.grid.isotope( 'appended', data )
				instance.grid.isotope( 'remove', $('.dp_pec_content_ajax .dp_pec_isotope', instance.calendar) );

				var toAppend = []; //array containing promises 
				var tasks    = [];

				var data_noscript = data;
				$('script', data_noscript).remove();
				
				  var element = $(data_noscript);
				  if( element.length == 0 )
				  {
				    return;
				  }
				  //toAppend only contains non-empty elements
				  $.each(element, function(i, el){
				    toAppend.push(el);
				  });

				//console.log(toAppend);
				instance.grid.isotope('insert', toAppend);
				instance.grid.isotope('layout');

				$('script', data).appendTo(instance.grid);

			} else {

				$('.dp_pec_content_ajax', instance.calendar).empty().html(data);

			}

			instance.init_tooltips();


		},

		submit_event_hook: function ( el ) {

			var instance = this;
			
			$(el).off('click', '.dp_pec_submit_event');
			$(el).on('click', '.dp_pec_submit_event', function(e) {
				e.preventDefault();
				if(typeof tinyMCE != "undefined") {
					tinyMCE.triggerSave();
				}

				//var form = $(this).closest(".add_new_event_form");
				
				var origName = $(this).html();
				
				var me = this;
				var form = $(this).closest('form');
				var post_obj = {
					calendar: instance.settings.calendar, 
					action: 'submitEvent',
					postEventsNonce : ProEventCalendarAjax.postEventsNonce
				}
				
				var is_valid = true;
				$('.pec_required', form).each(function() {
					
					$(this).removeClass('dp_pec_validation_error');



					if($(this).is(':checkbox')) {
						if($(this).is( ":checked" ) == false) {
							
							$(this).addClass('dp_pec_validation_error');
							$(this).next('.dp_pec_new_event_validation_msg').show();
							
							is_valid = false;
							return;
						}
					} else {
						if($(this).val() == "") {
							
							if($(this).is('select')) {
								$(this).closest('.selectric-pec_required').removeClass('dp_pec_validation_error');
								$(this).closest('.selectric-pec_required').addClass('dp_pec_validation_error');
							}

							$(this).addClass('dp_pec_validation_error');
							$(this).next('.dp_pec_new_event_validation_msg').show();
							
							is_valid = false;
							return;
						}
					}

				});

				if(!is_valid) {
					return false;
				}

				if(ProEventCalendarAjax.recaptcha_enable && ProEventCalendarAjax.recaptcha_site_key != "") {
					post_obj.grecaptcharesponse = grecaptcha.getResponse(pec_new_event_captcha);
					if(post_obj.grecaptcharesponse == "") {
						return false;
					}
				}

				$(this).addClass('dp_pec_disabled');
				

				form.fadeTo('fast', .5);
				
				$(this).html($(this).data('lang-sending'));	

				$(this).closest(".add_new_event_form").ajaxForm({
					url: ProEventCalendarAjax.ajaxurl,
					data: post_obj,
					success:function(data){
						$(me).html(origName);
						if(!form.hasClass('edit_event_form')) {
							
							$(form)[0].reset();

						}
						//} else {
						//	location.reload();	
						//}
						$('select', form).selectric('refresh');

						$('.dp_pec_form_title', form).removeClass('dp_pec_validation_error');
						$(me).removeClass('dp_pec_disabled');
						$('.dp_pec_notification_event_succesfull', form.parent()).show();

						//instance._goToByScroll($('.dp_pec_notification_event_succesfull', form.parent()));


						instance.goToFirstStep(el);
						form.fadeTo('fast', 1);
					
					}
				}).submit();
			});		
		},

		create_event_modal: function( edit ) {

			var instance = this;

			var button = '.dp_pec_add_event';
			var btn_action = 'getNewEventForm';

			if(edit)
			{

				button = '.pec_edit_event';

				btn_action = 'getEditEventForm';

			}

			$(instance.calendar).off('click', button);

			$(instance.calendar).on('click', button, function(e) {
					
				$('body, html').css('overflow', 'hidden');

				var $btn = $(this);

				if(!$('.dpProEventCalendarModalEditEvent').length) {
			
					$('body').append(
						$('<div>').addClass('dpProEventCalendarModalEditEvent dp_pec_new_event_wrapper').attr('id', 'dpProEventCalendarModal_'+instance.settings.calendar ).prepend(
							$('<h2>').text($btn.attr('title')).append(
								$('<a>').addClass('dpProEventCalendarClose').attr({ 'href': '#' }).html('<i class="fa fa-times"></i>')
							)
						).append(
							$('<div>').addClass('dpProEventCalendar_eventform dp_pec_content_loading')
						)
					);

					$('.dpProEventCalendarModalEditEvent').show();
					
					instance.show_overlay();
					
				} else {
					$('.dpProEventCalendar_eventform').empty().addClass('dp_pec_content_loading');
					$('.dpProEventCalendarModalEditEvent').attr('id', 'dpProEventCalendarModal_' + instance.settings.calendar  ).removeClass('dpProEventCalendarModalSmall');
					$('.dpProEventCalendarModalEditEvent h2').text($btn.attr('title')).append(
								$('<a>').addClass('dpProEventCalendarClose').attr({ 'href': '#' }).html('<i class="fa fa-times"></i>')
							);
					$('.dpProEventCalendarModalEditEvent, .dpProEventCalendarOverlay').show();
				}

				var $modal = $('.dpProEventCalendarModalEditEvent');

				//Close modals
				instance.close_button($modal);
				

				$('.dpProEventCalendarModalEditEvent').off('change', '.event_image');
				$('.dpProEventCalendarModalEditEvent').on('change', '.event_image', function() 
				{
					$('#event_image_lbl', $(this).parent()).val($(this).val().replace(/^.*[\\\/]/, ''));
				});

				var opts = { 
						event_id: (edit ? $btn.data('event-id') : 0),
						calendar: instance.settings.calendar,
						action: btn_action, 
						postEventsNonce : ProEventCalendarAjax.postEventsNonce 
					};

				if( typeof $(this).data( 'time' ) != 'undefined' )
					opts.time = $(this).data( 'time' );

				$.post(ProEventCalendarAjax.ajaxurl, 
					opts,
					function(data) {
						
						$('.dpProEventCalendar_eventform').removeClass('dp_pec_content_loading').empty().html(data);

						instance.make_event_form_work($modal);

						instance.create_datepicker($modal);

						instance.submit_event_hook($modal);

					}
				);	
				return false;
				
			});

		},

		set_other_location: function ( elem )
		{
			
			var instance = this;

			jQuery("#pec_map_address", elem).on('focus', function () {

				$('.map_lnlat_wrap', elem).show();
				
				jQuery("#pec_map_address", elem).off('focus');
				
				var geocoder = new google.maps.Geocoder();
				var	map;
				var marker;
									
				function geocodePosition(pos) {
				  geocoder.geocode({
					latLng: pos
				  }, function(responses) {
					if (responses && responses.length > 0) {
					  updateMarkerAddress(responses[0].formatted_address);
					} else {
					  //updateMarkerAddress("Cannot determine address at this location.");
					}
				  });
				}
				
				function updateMarkerPosition(latLng) {
				  jQuery("#map_lnlat", elem).val([
					latLng.lat(),
					latLng.lng()
				  ].join(", "));
				}
				
				function updateMarkerAddress(str) {
				  jQuery("#pec_map_address", elem).val(str);
				}
				
				function pec_map_initialize() {

				  var latLng = new google.maps.LatLng(instance.settings.map_lat,instance.settings.map_lng);
				  map = new google.maps.Map(jQuery("#pec_mapCanvas", elem)[0], {
					zoom: (instance.settings.map_lat != 0 ? 12 : 3),
					center: latLng,
					mapTypeId: google.maps.MapTypeId.ROADMAP
				  });
				  marker = new google.maps.Marker({
					position: latLng,
					title: "Location",
					map: map,
					draggable: true
				  });
				  
				  // Update current position info.
				  updateMarkerPosition(latLng);
				  //geocodePosition(latLng);
				  
				  // Add dragging event listeners.
				  google.maps.event.addListener(marker, "dragstart", function() {
					updateMarkerAddress("");
				  });
				  
				  google.maps.event.addListener(marker, "drag", function() {
					updateMarkerPosition(marker.getPosition());
				  });
				  
				  google.maps.event.addListener(marker, "dragend", function() {
					geocodePosition(marker.getPosition());
				  });
				}
				
				var timeout;

				jQuery("#pec_map_address", elem).on('keyup', function () {
				  clearTimeout( timeout );
				  timeout = setTimeout(function() {
					  geocoder.geocode( { "address": jQuery("#pec_map_address", elem).val()}, function(results, status) {
						  if(status != "OVER_QUERY_LIMIT") {
							  var latlng = results[0].geometry.location;
							  marker.setPosition(latlng);
							  
							 // var listener = google.maps.event.addListener(map, "idle", function() { 
								  if (map.getZoom() < 12) map.setZoom(12); 
								  map.setCenter(latlng);
								  //google.maps.event.removeListener(listener); 
								//});
								
								updateMarkerPosition(latlng);
						  }
					 });
				 }, 1000);
				});
				
				// Onload handler to fire off the app.
				pec_map_initialize();
				//google.maps.event.addDomListener(window, "load", pec_map_initialize);
			});

		},

		goToFirstStep: function (elem) 
		{
			var instance = this;

			var visible_step = $('.dp_pec_form_step_visible', elem);
			var first_step = $('.dp_pec_new_event_steps:first-child', elem);
			
			$('.pec_form_back, .dp_pec_submit_event', elem).hide();
			$('.pec_form_next', elem).show();
			

			first_step.addClass('dp_pec_form_step_visible dp_pec_form_step_animation_right');
			visible_step.removeClass('dp_pec_form_step_visible dp_pec_form_step_animation_left dp_pec_form_step_animation_right');

		},

		create_overlay: function ( )
		{

			var instance = this;

			if(!$('.dpProEventCalendarOverlay').length) {
		
				$('body').append(
					$('<div>').addClass('dpProEventCalendarOverlay').click(function() { 

						if($('.dpProEventCalendarMapModal').is(':visible')) {


							$('.dpProEventCalendarMapModal').find('.dp_pec_close').trigger('click');


						} else {

							instance.close_modal();

						}

					})
				);
		
			}

		},

		show_overlay: function ( )
		{

			$('body, html').css('overflow', 'hidden');

			$('.dpProEventCalendarOverlay').fadeIn('fast');

		},



		open_map: function ( elem )
		{

			var instance = this;

			if( $('.dpProEventCalendarMapModal').length ) 
				$('.dpProEventCalendarMapModal').remove();

			const map_modal = $(elem).next('.dp_pec_open_map_wrap').find('.dp_pec_date_event_map_canvas');

			$('body').append(
				
				$('<div>').addClass('dpProEventCalendarMapModal').data('button', $(elem).attr('id')).prepend(
				
					map_modal
				
				).prepend(
				
					$('<a>').addClass('dp_pec_close').attr({ 'href': '#' }).html( instance.settings.lang_close )
				
				)
			
			);

			const $modal = $('.dpProEventCalendarMapModal');
			$modal.show();

			//Close modals
			instance.close_button($modal);

			

		},

		create_book_event: function ( )
		{

			var instance = this;

			$(document).off('click', '.pec_event_page_book');
			$(document).on('click', '.pec_event_page_book', function(e) {
			
				if($(window).width() > 720) {
					$('body, html').css('overflow', 'hidden');
				}

				var text = $(this).find('strong').text();
				if( text == '' )
					text = $(this).data( 'pec-tooltip' );
				
				if($('.dpProEventCalendarModal').length) {
					$('.dpProEventCalendarModal').remove()
				}

				if(!$('.dpProEventCalendarModal').length) {
					$('body').append(
						$('<div>').addClass('dpProEventCalendarModal').prepend(
							$('<h2>').text( text ).append(
								$('<a>').addClass('dpProEventCalendarClose').attr({ 'href': '#' }).html('<i class="fa fa-times"></i>')
							)
						).show()
					);
					
					instance.show_overlay();
					
				}
				
				const $modal = $('.dpProEventCalendarModal');

				$modal.show();

				//Close modals
				instance.close_button($modal);

				$('.dpProEventCalendarModal').addClass('dpProEventCalendarModal_Preload');
				
				
				$.post(ProEventCalendarAjax.ajaxurl, 
					{ 
						event_id: $(this).data('event-id'),
						calendar: $(this).data('calendar'),
						date: $(this).data('date'),
						action: 'getBookEventForm', 
						postEventsNonce : ProEventCalendarAjax.postEventsNonce 
					},
					function(data) {
						
						$('.dpProEventCalendarModal').removeClass('dpProEventCalendarModal_Preload').append(data);
						
						$('select', '.dpProEventCalendarModal').selectric();

						$('#pec_event_page_book_date', '.dpProEventCalendarModal').trigger('change');

						$("input, textarea", '.dpProEventCalendarModal').placeholder();
					}
				);	

				return false;

			});


		},

		create_isotope: function ( div )
		{

			var instance = this;

			instance.grid = $(div, instance.calendar).isotope({
			  itemSelector: '.dp_pec_date_event_wrap',
			  layoutMode: 'masonry',
			  isOriginLeft : (instance.settings.isRTL ? false : true),
			  masonry: {
			  	gutter: 20
			  }
		
			});

		},

		show_search: function ( force_hide )
		{
			var instance = this;

			if(!$('.dp_pec_search_form', instance.calendar).is(':visible') && !force_hide) {
				$('.dp_pec_search_btn', instance.calendar).addClass('active');
				$('.dp_pec_search_form', instance.calendar).show();
				$('.dp_pec_search_form input[type=search]', instance.calendar).focus();

				// Hide pther navs
				$('.dp_pec_nav.dp_pec_nav_monthly', instance.calendar).hide();
			} else {
				$('.dp_pec_search_btn', instance.calendar).removeClass('active');
				$('.dp_pec_search_form', instance.calendar).hide();
				$('.dp_pec_nav.dp_pec_nav_monthly', instance.calendar).show();
			}


		},

		create_datepicker: function ( elem )
		{

			var instance = this;
			var multiple_dates = new Array();
			var multiple_dates_hidden = new Array();

			if($(".dp_pec_extra_dates", elem).length && $(".dp_pec_extra_dates", elem).val() != "")
			{
			
				multiple_dates = $(".dp_pec_extra_dates", elem).data('extra-dates-parsed').split(' /// ');
				multiple_dates_hidden = $(".dp_pec_extra_dates_hidden", elem).val().split(',');

			}

			$(".dp_pec_date_input, .dp_pec_end_date_input, .dp_pec_extra_dates", elem).datepicker({

				beforeShow: function(input, inst) {
				   
				   $("#ui-datepicker-div").removeClass("dp_pec_datepicker");
				   $("#ui-datepicker-div").addClass("dp_pec_datepicker");
				   
			   },

			   onSelect: function (dateText, inst) {

			        if($(inst.input).hasClass("dp_pec_extra_dates")) {

			        	var dates = $(inst.input).val();
			        	var index = jQuery.inArray(dateText, multiple_dates);
					    if (index >= 0) 
					        multiple_dates.splice(index, 1);
					    else 
					        multiple_dates.push(dateText);

					    var printExtraDates = new String;
					    multiple_dates.forEach(function (val) {
					        printExtraDates += val + ", ";
					    });

					    $(inst.input).val(printExtraDates.slice(0, -2));

					    if($(this).prev(".dp_pec_extra_dates_hidden").length) {

					    	var hidden = $(this).prev(".dp_pec_extra_dates_hidden");

					    	var dates = hidden.val();
					    	var date_month = (inst.selectedMonth + 1).toString();
					    	var format_date = inst.selectedYear + "-" + (date_month.padStart(2, "0")) + "-" + inst.selectedDay;
					    	
				        	var index = jQuery.inArray(format_date, multiple_dates_hidden);

						    if (index >= 0) 
						        multiple_dates_hidden.splice(index, 1);
						    else 
						        multiple_dates_hidden.push(format_date);

						    var printExtraDates = new String;
						    multiple_dates_hidden.forEach(function (val) {
						        printExtraDates += val + ", ";
						    });

						    $(hidden).val(printExtraDates.slice(0, -2));


					    }

			        }
			    },
				showOn: "button",
				isRTL: instance.settings.isRTL,
				buttonText: "<i class=\"far fa-calendar-plus\"></i>",
				buttonImageOnly: false,
				minDate: 0,
				monthNames: instance.settings.monthNames,
				dayNamesMin: instance.settings.dayNamesMin,
				firstDay: instance.settings.firstDay,
				dateFormat: instance.settings.datepicker_dateFormat
			});


		},
		

		make_event_form_work: function (elem) {

			var instance = this;

			if($('.pec-editor-test', elem).length && elem.hasClass('dpProEventCalendarModalEditEvent'))
			{

				var editor_id = $('.pec-editor-test', elem).attr('id');
				
		        //init tinymce
		        if(typeof tinyMCE != "undefined") {

		            tinymce.init(
		            	{ 
		            		selector: editor_id, 
		            		toolbar: "bold italic underline blockquote strikethrough | bullist numlist | alignleft aligncenter alignright | undo redo | link unlink"
		            	}
		            ); 
		            tinyMCE.execCommand('mceAddEditor', false, editor_id);
				}


			}


			$(elem)
				.off('click', '.pec_form_back')
				.on('click', '.pec_form_back', function() 
			{
				
			
				$('.pec_form_next', elem).show();
				$('.dp_pec_submit_event', elem).hide();
				var visible_step = $('.dp_pec_form_step_visible', elem);
				var step_prev = visible_step.prev();
				
				if(step_prev.is(':first-child'))
					$(this).hide();
				

				step_prev.addClass('dp_pec_form_step_visible dp_pec_form_step_animation_right');
				visible_step.removeClass('dp_pec_form_step_visible dp_pec_form_step_animation_left dp_pec_form_step_animation_right');
				
			})	

			$(elem)
				.off('click', '.pec_form_next')
				.on('click', '.pec_form_next', function() 
			{
				
				var visible_step = $('.dp_pec_form_step_visible', elem);

				const is_first_step = visible_step.is(':first-child');

				//Validation

				var is_valid = true;
				var form = $(this).closest('form');

				$('.pec_required', form).each(function() {
					
					$(this).removeClass('dp_pec_validation_error');
					$(this).next('.dp_pec_new_event_validation_msg').hide();

					if( is_first_step ) {

						if( $(this).hasClass('dp_pec_form_title') && $(this).val() == '' ) {

							$(this).addClass('dp_pec_validation_error');
							$(this).next('.dp_pec_new_event_validation_msg').show();

							is_valid = false;

						}

						return;
					}

					if($(this).is(':checkbox')) {
						if($(this).is( ":checked" ) == false) {
							
							$(this).addClass('dp_pec_validation_error');
							$(this).next('.dp_pec_new_event_validation_msg').show();
							
							is_valid = false;
							return;
						}
					} else {
						if( $(this).val() == "" ) {
							
							if($(this).is('select')) {
								$(this).closest('.selectric-pec_required').removeClass('dp_pec_validation_error');
								$(this).closest('.selectric-pec_required').addClass('dp_pec_validation_error');
							}

							$(this).addClass('dp_pec_validation_error');

							$(this).next('.dp_pec_new_event_validation_msg').show();
							
							is_valid = false;
							return;
						}
					}

				});

				if( ! is_valid ) {
					return false;
				}

				$('.pec_form_back', elem).show();

				var step_next = visible_step.next();
				
				if(step_next.is(':last-child')) {
					$(this).hide();
					$(this).next().show();
				}

				step_next.addClass('dp_pec_form_step_visible dp_pec_form_step_animation_left');
				visible_step.removeClass('dp_pec_form_step_visible dp_pec_form_step_animation_left dp_pec_form_step_animation_right');

				// Remove notifications

				$('.dp_pec_notification_event_succesfull', elem).hide();

				// Update Description if editor

				if (typeof tinymce !== 'undefined') {

					$('.dp_pec_event_form_preview_description', elem).html(tinymce.get( $('.pec-editor-test', elem).attr('id') ).getContent());

				}
				
			})	

			$(elem)
				.off('click', '.dp_pec_event_form_options_item')
				.on('click', '.dp_pec_event_form_options_item', function(e) 
			{
				
				$('.dp_pec_event_form_options_item', elem).removeClass('dp_pec_event_form_options_item_open');

				$(this).addClass('dp_pec_event_form_options_item_open');

				if($(this).find('input').length && !$(e.target).hasClass('dp_pec_new_event_text'))
					$(this).find('input')[0].focus();


				if($(this).has('.dp_pec_date_input').length) {

					$('.dp_pec_date_input', elem).datepicker("option", "onSelect", function(dateText, inst){
						
						$(this).prev('.dp_pec_date_input_hidden').val(inst.selectedYear + "-" + (instance._str_pad((inst.selectedMonth + 1), 2, "0", 'STR_PAD_LEFT')) + "-" + (instance._str_pad(inst.selectedDay, 2, "0", 'STR_PAD_LEFT')));
						$('.dp_pec_event_form_options_item_date_start', elem).html(dateText);
					});

				}

				if($(this).has('.dp_pec_end_date_input').length) {

					$('.dp_pec_end_date_input', elem).datepicker("option", "onSelect", function(dateText, inst){

						$(this).prev('.dp_pec_end_date_input_hidden').val(inst.selectedYear + "-" + (instance._str_pad((inst.selectedMonth + 1), 2, "0", 'STR_PAD_LEFT')) + "-" + (instance._str_pad(inst.selectedDay, 2, "0", 'STR_PAD_LEFT')));
						$('.dp_pec_event_form_options_item_date_end', elem).html(dateText);
					});

				}
				
				
			})	

			$('.add_new_event_form', document).mouseup(function(e) 
			{

			    const container = $(".dp_pec_event_form_options_item", elem);
			    const closest = $(e.target).closest('.dp_pec_event_form_options_item_hidden');
			    const is_datepicker = $(e.target).closest('.dp_pec_datepicker');

			    if(!closest.length && !is_datepicker.length)
				    container.removeClass('dp_pec_event_form_options_item_open');
			
			});


			$(elem)
				.off('focusout', '.dp_pec_form_title')
				.on('focusout', '.dp_pec_form_title', function() 
			{
			
				$('.dp_pec_event_form_preview_title', elem).html($(this).val());
			
			});

			$(elem)
				.off('focusout', '.dp_pec_form_description')
				.on('focusout', '.dp_pec_form_description', function() 
			{
			
				$('.dp_pec_event_form_preview_description', elem).html($(this).val());
			
			});


			if($('.dp_pec_clear_end_date', elem).length) {

				$('.dp_pec_clear_end_date', elem).click(function(e) {

					e.preventDefault();

					$('.dp_pec_end_date_input', elem).val('');

					if($('.dp_pec_event_form_options_item_date_end', elem).length)
						$('.dp_pec_event_form_options_item_date_end', elem).html('');
					
				});
				
			}

			
			// Category Items
			////////////////////////////////


			$(elem)
				.off('ifChanged', '.dp_pec_event_form_options_item_category input[type="checkbox"]')
				.on('ifChanged', '.dp_pec_event_form_options_item_category input[type="checkbox"]', function(event)
			{

				let category_string = new Array;


				$.each( $('.dp_pec_event_form_options_item_category input[type="checkbox"]', elem), function( key, value ) {

					const $this_checkbox = $(value)[0];

					if($this_checkbox.checked) 
					{
						
						category_string.push($($this_checkbox).data('cat-name'));

					}

				});

				$('.dp_pec_event_form_options_item_category .dp_pec_event_form_options_item_sub', elem).html(category_string.join(', '));

			});

			// Speaker Items
			////////////////////////////////


			$(elem)
				.off('ifChanged', '.dp_pec_event_form_options_item_speakers input[type="checkbox"]')
				.on('ifChanged', '.dp_pec_event_form_options_item_speakers input[type="checkbox"]', function(event)
			{

				let speaker_string = new Array;


				$.each( $('.dp_pec_event_form_options_item_speakers input[type="checkbox"]', elem), function( key, value ) {

					const $this_checkbox = $(value)[0];

					if($this_checkbox.checked) 
					{
						
						speaker_string.push($($this_checkbox).data('speaker-name'));

					}

				});

				$('.dp_pec_event_form_options_item_speakers .dp_pec_event_form_options_item_sub', elem).html(speaker_string.join(', '));

			});

			// Frequency Items
			////////////////////////////////

			$(elem)
				.off('change', 'select.pec_recurring_frequency')
				.on('change', 'select.pec_recurring_frequency', function(event)
			{

				$('.dp_pec_event_form_options_item_frequency .dp_pec_event_form_options_item_sub', elem).html($(this).children("option:selected").text());

				instance._pec_update_frequency(this.value, elem);
			

			});

			// Time Items
			////////////////////////////////

			$(elem)
				.off('change', '.dp_pec_event_form_options_item_time .dp_pec_new_event_time')
				.on('change', '.dp_pec_event_form_options_item_time .dp_pec_new_event_time', function(event)
			{

				const item_time = $(this).closest('.dp_pec_event_form_options_item_time');

				const $time = item_time.find('.dp_pec_start_time_hh').children("option:selected").text() + ':' + item_time.find('.dp_pec_start_time_mm').children("option:selected").text();

				$('.dp_pec_event_form_options_item_time .dp_pec_event_form_options_item_time_start', elem).html($time);

				if(item_time.find('.dp_pec_end_time_hh').length) 
				{

					const $end_time = item_time.find('.dp_pec_end_time_hh').children("option:selected").text() + ':' + item_time.find('.dp_pec_end_time_mm').children("option:selected").text();

					$('.dp_pec_event_form_options_item_time .dp_pec_event_form_options_item_time_end', elem).html($end_time);

				}

			});

			// Location Items
			////////////////////////////////

			$(elem)
				.off('change', 'select.pec_location_form')
				.on('change', 'select.pec_location_form', function(event)
			{

				$('.dp_pec_event_form_options_item_location .dp_pec_event_form_options_item_sub', elem).html( $(this).children("option:selected").text() );

				instance._pec_update_location(this.value, elem);
				

			});

			instance.set_other_location(elem);

			// Custom Fields
			////////////////////////////////

			$(elem)
				.off('ifChanged', '.dp_pec_event_form_options_item_custom_multiple input[type="checkbox"]')
				.on('ifChanged', '.dp_pec_event_form_options_item_custom_multiple input[type="checkbox"]', function(event)
			{

				let value_string = new Array;
				const parent_div = $(this).closest('.dp_pec_event_form_options_item_custom_multiple');


				$.each( $('input[type="checkbox"]', parent_div), function( key, value ) {

					const $this_checkbox = $(value)[0];

					if($this_checkbox.checked) 
					{
						
						value_string.push($($this_checkbox).val());

					}

				});

				$('.dp_pec_event_form_options_item_sub', parent_div).html(value_string.join(', '));

			});


			$('.dp_pec_new_event_wrapper select').selectric();
			$('.dp_pec_new_event_wrapper input.new_event_checkbox').iCheck({
				checkboxClass: 'icheckbox_flat',
				radioClass: 'iradio_flat',
				increaseArea: '20%' // optional
			});

			if(ProEventCalendarAjax.recaptcha_enable && ProEventCalendarAjax.recaptcha_site_key != "") {
				$(window).load(function() {
					pec_new_event_captcha = grecaptcha.render($('#pec_new_event_captcha', elem)[0], {
					  'sitekey' : ProEventCalendarAjax.recaptcha_site_key
					});
				});
			}


		},			
		
		
		/**
		* Returns a MS time stamp of the current time
		*/
		getTimeStamp : function() {
			var now = new Date();
			return now.getTime();
		}
	}
	
	$.fn.dpProEventCalendar = function(options){  

		var dpProEventCalendar;
		this.each(function(){
			
			dpProEventCalendar = new DPProEventCalendar($(this), options);
			
			$(this).data("dpProEventCalendar", dpProEventCalendar);
			
		});
		
		return this;

	}
	
  	/* Default Parameters and Events */
	$.fn.dpProEventCalendar.defaults = {  
		monthNames : new Array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'),
		actualMonth : '',
		actualYear : '',
		actualDay : '',
		defaultDate : '',
		lang_sending: 'Sending...',
		skin : 'pec_skin_light',
		view: 'monthly',
		type: 'calendar',
		limit: '',
		widget: 0,
		selectric: true,
		lockVertical: true,
		include_all_events: false,
		modal: false,
		hide_old_dates: 0,
		calendar: null,
		show_current_date: true,
		dateRangeStart: null,
		dateRangeEnd: null,
		draggable: true,
		isAdmin: false,
		dragOffset: 50,
		recaptcha_enable : false,
		allowPageScroll: "vertical",
		fingers: 1
	};  
	
	$.fn.dpProEventCalendar.settings = {}
	
})(jQuery);

/* onShowProCalendar custom event */
 (function($){
  $.fn.extend({ 
    onShowProCalendar: function(callback, unbind){
      return this.each(function(){
        var obj = this;
        var bindopt = (unbind==undefined)?true:unbind; 
        if($.isFunction(callback)){
          if($(this).is(':hidden')){
            var checkVis = function(){
              if($(obj).is(':visible')){
                callback.call();
                if(bindopt){
                  $('body').unbind('click keyup keydown', checkVis);
                }
              }                         
            }
            $('body').bind('click keyup keydown', checkVis);
          }
          else{
            callback.call();
          }
        }
      });
    }
  });
})(jQuery);

(function($) {
/**
 * Used for version test cases.
 *
 * @param {string} left A string containing the version that will become
 *        the left hand operand.
 * @param {string} oper The comparison operator to test against. By
 *        default, the "==" operator will be used.
 * @param {string} right A string containing the version that will
 *        become the right hand operand. By default, the current jQuery
 *        version will be used.
 *
 * @return {boolean} Returns the evaluation of the expression, either
 *         true or false.
 */
	$.proCalendar_isVersion = function(version1, version2){
		if ('undefined' === typeof version1) {
		  throw new Error("$.versioncompare needs at least one parameter.");
		}
		version2 = version2 || $.fn.jquery;
		if (version1 == version2) {
		  return 0;
		}
		var v1 = normalize(version1);
		var v2 = normalize(version2);
		var len = Math.max(v1.length, v2.length);
		for (var i = 0; i < len; i++) {
		  v1[i] = v1[i] || 0;
		  v2[i] = v2[i] || 0;
		  if (v1[i] == v2[i]) {
			continue;
		  }
		  return v1[i] > v2[i] ? 1 : 0;
		}
		return 0;
	};
	function normalize(version){
	return $.map(version.split('.'), function(value){
	  return parseInt(value, 10);
	});

	}
	
	$(document).ready(function() {
		

		
		$(document).on('change', '#pec_event_page_book_date', function(e) {
			
			jQuery('#pec_event_page_book_quantity option').removeAttr('disabled');

			$('#pec_event_page_book_quantity', '.dpProEventCalendarModal').val(1).change();
			$('#pec_event_page_book_quantity', '.dpProEventCalendarModal').selectric('refresh');

			if(this.value == 0) { 
				jQuery('.pec_event_page_send_booking').prop('disabled', true); 
			} else { 
				jQuery('.pec_event_page_send_booking').prop('disabled', false); 
	
				$('#pec_event_page_book_quantity option:gt('+(jQuery(this).find(':selected').data('available') - 1)+')', '.dpProEventCalendarModal').attr('disabled', 'disabled');

				$('#pec_event_page_book_quantity', '.dpProEventCalendarModal').selectric('refresh');

			}

		});

		$(document).on('change', '#pec_event_page_book_quantity', function(e) {
			
			if($('.dp_pec_payment_price').length) {
				var new_price = ($('.dp_pec_payment_price').find('span.dp_pec_payment_price_value').data('price') * $(this).val()).toFixed(2);
				$('.dp_pec_payment_price').find('span.dp_pec_payment_price_value').text( new_price );
				$('.dp_pec_payment_price').find('span.dp_pec_payment_price_value').data( 'price-updated', new_price );

				if($('#pec_payment_discount_value', '.dpProEventCalendarModal').length) {
					var coupon_value = $('#pec_payment_discount_value', '.dpProEventCalendarModal').val();
					if(coupon_value != "") {

						var result = ((100 - coupon_value) / 100) * new_price;

						$('.dp_pec_payment_price', '.dpProEventCalendarModal').find('span.dp_pec_payment_price_value').text( 
							result.toFixed(2)
						);

					}
				}
			}

		});

		$(document).on('submit', '.dp_pec_coupon_form', function() {
			
			if($(this).find('.dp_pec_coupon').val() != "") {

				var coupon_inp = $(this).find('.dp_pec_coupon');
				
				coupon_inp.removeClass('dp_pec_validation_error');
				if(coupon_inp.hasClass('dp_pec_validation_correct')) {
					return false;
				}

				$.post(ProEventCalendarAjax.ajaxurl, { 
					code: $(this).find('.dp_pec_coupon').val(), 
					action: 'getCoupon', 
					postEventsNonce : ProEventCalendarAjax.postEventsNonce 
				},
					function(data) {
						//coupon_inp.val("");
						if(data == "null") {

							coupon_inp.addClass('dp_pec_validation_error');

						} else {

							data = jQuery.parseJSON(data);

							if($('.dp_pec_payment_price', '.dpProEventCalendarModal').length) {
								var result = ((100 - data.discount) / 100) * $('.dp_pec_payment_price', '.dpProEventCalendarModal').find('span.dp_pec_payment_price_value').data('price-updated');

								$('.dp_pec_payment_price', '.dpProEventCalendarModal').find('span.dp_pec_payment_price_value').text( 
									result.toFixed(2)
								);

								$('#pec_payment_discount_id', '.dpProEventCalendarModal').val(data.id);
								$('#pec_payment_discount_coupon', '.dpProEventCalendarModal').val(data.coupon);
								$('#pec_payment_discount_value', '.dpProEventCalendarModal').val(data.discount);

								coupon_inp.addClass('dp_pec_validation_correct');
								coupon_inp.closest('.dp_pec_coupon_form').addClass('dp_pec_validation_form_correct');
								coupon_inp.prop("readonly", true);
							}
						}
					}
				);	
			}
			return false;
		});

		$(document).on('keyup', '.dp_pec_coupon', function (e) {

			if (e.keyCode == 13) {
				// Do something
				$('.dp_pec_coupon_go', '.dpProEventCalendarModal').trigger('click');
			}
		});

		/*if($('.pec_event_page_action_menu').length) {
			$(document).on('touchstart click', function(e) {

				$('.pec_event_page_action_menu').each(function(e) {

					var $parent = $(this).parent();

					if($(this).is(':visible') && !$(this).is(':animated')) {
						$('.pec_event_page_action', $parent).trigger('click');
					}	
				})
				
			});
		}*/

		$(document).on('click', '.dp_pec_notification_close', function(e) {
			e.preventDefault();

			$(this).closest('.dp_pec_notification_event').fadeOut('fast');

		});

		

		$(document).on('click', '.pec_event_page_send_booking', function(e) {
			var instance = this;

			if($('#pec_event_page_book_name', '.dpProEventCalendarModal').length) {
				
				$('#pec_event_page_book_name, #pec_event_page_book_email', '.dpProEventCalendarModal').removeClass('dp_pec_validation_error');
				
				if($('#pec_event_page_book_name', '.dpProEventCalendarModal').val() == '') {
					$('#pec_event_page_book_name', '.dpProEventCalendarModal').addClass('dp_pec_validation_error');
					
					return false;
				}
				
				var re = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;

				if($('#pec_event_page_book_email', '.dpProEventCalendarModal').val() == '' || !re.test($('#pec_event_page_book_email', '.dpProEventCalendarModal').val())) {
					$('#pec_event_page_book_email', '.dpProEventCalendarModal').addClass('dp_pec_validation_error');
					
					return false;
				}
				
			}
			
			if($('#pec_event_page_book_phone', '.dpProEventCalendarModal').length) {
				
				$('#pec_event_page_book_phone', '.dpProEventCalendarModal').removeClass('dp_pec_validation_error');
				
				if($('#pec_event_page_book_phone', '.dpProEventCalendarModal').val() == '') {
					$('#pec_event_page_book_phone', '.dpProEventCalendarModal').addClass('dp_pec_validation_error');
					
					return false;
				}
				
			}

			var is_valid = true;
			
			$('.pec_required', '.dpProEventCalendarModal').each(function() {
				
				$(this).removeClass('dp_pec_validation_error');

				if($(this).is(':checkbox')) {

					$(this).closest('.dp_pec_wrap_checkbox').removeClass('dp_pec_validation_error');
					
					if($(this).is( ":checked" ) == false) {
						
						$(this).closest('.dp_pec_wrap_checkbox').addClass('dp_pec_validation_error');

						is_valid = false;
						return;
					}
				} else {
					if($(this).val() == "") {

						$(this).addClass('dp_pec_validation_error');
						
						is_valid = false;
						return;
					}
				}

			});

			if(!is_valid) {
				return false;
			}
			
			if($('#pec_event_page_book_terms_conditions', '.dpProEventCalendarModal').length) {
				
				if($('#pec_event_page_book_terms_conditions', '.dpProEventCalendarModal').is( ":checked" ) == false) {

					$('#pec_event_page_book_terms_conditions', '.dpProEventCalendarModal').focus();
					
					return false;
				}

			}

			var extra_fields = {};
			if($('.pec_event_page_book_extra_fields', '.dpProEventCalendarModal').length) {

				$('.pec_event_page_book_extra_fields', '.dpProEventCalendarModal').each(function( index ) {
				  
				  if($(this).attr('type') == 'checkbox') {
				  	if($(this).is(':checked')) {
				  		extra_fields[$(this).attr('name')] = 1;
				    }
				  } else {
					  extra_fields[$(this).attr('name')] = $(this).val();
				  }
				});

			}
			var $btn_booking = $(this);
			$btn_booking.prop('disabled', true);
			$btn_booking.css('opacity', .6);
			var event_id = $('#pec_event_page_book_event_id', '.dpProEventCalendarModal').val();
			var quantity = $('#pec_event_page_book_quantity', '.dpProEventCalendarModal').val();
			$.post(ProEventCalendarAjax.ajaxurl, 
				{ 
					event_date: $('#pec_event_page_book_date', '.dpProEventCalendarModal').val(), 
					ticket: $('#pec_event_page_book_ticket', '.dpProEventCalendarModal').val(), 
					event_id: event_id, 
					calendar: $('#pec_event_page_book_calendar', '.dpProEventCalendarModal').val(), 
					comment: $('#pec_event_page_book_comment', '.dpProEventCalendarModal').val(), 
					quantity: quantity, 
					name: ($('#pec_event_page_book_name', '.dpProEventCalendarModal').length ? $('#pec_event_page_book_name', '.dpProEventCalendarModal').val() : ''), 
					email: ($('#pec_event_page_book_email', '.dpProEventCalendarModal').length ? $('#pec_event_page_book_email', '.dpProEventCalendarModal').val() : ''), 
					phone: ($('#pec_event_page_book_phone', '.dpProEventCalendarModal').length ? $('#pec_event_page_book_phone', '.dpProEventCalendarModal').val() : ''), 
					pec_payment_discount_id: ($('#pec_payment_discount_id', '.dpProEventCalendarModal').length ? $('#pec_payment_discount_id', '.dpProEventCalendarModal').val() : ''), 
					pec_payment_discount_coupon: ($('#pec_payment_discount_coupon', '.dpProEventCalendarModal').length ? $('#pec_payment_discount_coupon', '.dpProEventCalendarModal').val() : ''), 
					extra_fields: extra_fields,
					return_url: window.location.href,
					action: 'bookEvent', 
					postEventsNonce : ProEventCalendarAjax.postEventsNonce 
				},
				function(data) {
					data = jQuery.parseJSON(data);
					
					$('#pec_event_page_book_comment', '.dpProEventCalendarModal').val('');
					$btn_booking.prop('disabled', false);	
					$btn_booking.css('opacity', 1);
					
					if(data.gateway_screen != "") {
						
						$('.pec_book_select_date', '.dpProEventCalendarModal').html(data.gateway_screen);
						
						$('.pec_gateway_form select', '.dpProEventCalendarModal').selectric();
						
					} else {

						$('.pec_book_select_date', '.dpProEventCalendarModal').html(data.notification);
						//$('.dp_pec_attendees_counter_'+ event_id +' span', instance.calendar).text( parseInt($('.dp_pec_attendees_counter_'+ event_id +' span', instance.calendar).text(), 10) + parseInt(quantity, 10) );
					}
				}
			);	
			
		});
		
	});
})(jQuery);