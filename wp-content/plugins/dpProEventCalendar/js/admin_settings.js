jQuery(function($) {
	
	var geocoder = '';	
	if($('#mapCanvas').length || $('#pec_map').length) {
		var geocoder = new google.maps.Geocoder();
	}
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
		if(latLng.lat() == 0 && latLng.lng() == 0) return;
		
	  document.getElementById("pec_map_lnlat").value = [
		latLng.lat(),
		latLng.lng()
	  ].join(", ");
	}
	
	function updateMarkerAddress(str) {
	  document.getElementById("pec_map").value = str;
	}
	
	function initialize() {

	  var latLng = new google.maps.LatLng($('#mapCanvas').data('map-lat'),$('#mapCanvas').data('map-lng'));
	  map = new google.maps.Map(document.getElementById("mapCanvas"), {
		zoom: ($('#mapCanvas').data('map-lat') != 0 ? 12 : 3),
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
	  //updateMarkerPosition(latLng);
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
	  
	  if($('#pec_map').val() != "" && $('#mapCanvas').data('map-lat') == 0) {
		$('#pec_map').trigger('keyup');
	  }

	  if($('#pec_map_lnlat').val() != "") {
	  	var lnlat = $('#pec_map_lnlat').val().split(',');
	  	var latlng_obj = new google.maps.LatLng(lnlat[0],lnlat[1]);
	  	if (map.getZoom() < 12) map.setZoom(12); 
		marker.setPosition(latlng_obj);
		map.setCenter(latlng_obj);
	  }
	}
	
	var timeout;
	
	$('#pec_map').on('keyup', function () {
	  clearTimeout( timeout );
	  timeout = setTimeout(function() {
		  geocoder.geocode( { "address": $('#pec_map').val()}, function(results, status) {
			  if(status != "OVER_QUERY_LIMIT" && typeof results[0] != "undefined") {
			  	
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
	if($('#mapCanvas').length) {
		google.maps.event.addDomListener(window, "load", initialize);
	}

	$overlay = $('<div>').addClass('dpProEventCalendar_Overlay').click(function(){
		hideOverlay();
	});
	
	$('#dp_ui_content select[multiple!="multiple"], #dpProEventCalendar_events_meta select[multiple!="multiple"], #dpProEventCalendar_events_side_meta select[multiple!="multiple"]').not('.dp_manage_special_dates select, #custom_field_new select, #booking_custom_field_new select').selectric();
	
	if($("#dpProEventCalendar_SpecialDates").length) {
		var $specialDates = $("#dpProEventCalendar_SpecialDates");
		
		jQuery('.btn_add_special_date').click(function(event) {

			showOverlay();

			$specialDates.fadeIn('fast');
		});

		jQuery('.dpProEventCalendar_Modal_close', '.dpProEventCalendar_ModalManager').click(function(event) {
			event.preventDefault();
			hideOverlay();
		});


	}
	
	if($("#dpProEventCalendar_SpecialDatesEdit").length) {
		var $specialDatesEdit = $("#dpProEventCalendar_SpecialDatesEdit");
		
		jQuery('.btn_edit_special_date').click(function(event) {
			$('#dpPEC_special_id').val($(this).data('special-date-id'));
			$('#dpPEC_special_title').val($(this).data('special-date-title'));
			$('#dpPEC_special_color').val($(this).data('special-date-color'));
			$('#specialDate_colorSelector_Edit div').css('backgroundColor', $(this).data('special-date-color'));

			showOverlay();

			$specialDatesEdit.fadeIn('fast');
		});
	}
	
	if($("#settings_maps_title").length) {	
		
		$('#settings_maps_title').click(function(event) {
			initialize();

		});
		
	}

	if($(".pec_ticket_list .dashicons-dismiss").length) {	
		
		$(document).on('click', '.pec_ticket_list .dashicons-dismiss', function(event) {
			
			$(this).parent().fadeOut('fast', function() { $(this).remove(); });

			var ticket_arr = $('#pec_booking_ticket').val().split(',');
			var removeItem = $(this).parent().data('ticket-id');
			var ticket_list = jQuery.grep(ticket_arr, function(value) {
			  return value != removeItem;
			});
			
			$('#pec_booking_ticket').val(ticket_list.join(','));

		});

	}

	if($(".pec-tour-msg .pec-tour-cancel").length) {	
		
		$(document).on('click', '.pec-tour-msg .pec-tour-cancel', function(event) {
			
			event.preventDefault();

			// Hide Tour message
			$('.pec-tour-msg').slideUp('fast');

			// Don't show again

			$.post(ProEventCalendarAjax.ajaxurl, { 
				action: 'cancelTour', 
				postEventsNonce : ProEventCalendarAjax.postEventsNonce },
				function(data) {
				}
			);	
			

		});

	}

	if($(".pec-tour-msg .pec-tour-start").length) {	
		
		$(document).on('click', '.pec-tour-msg .pec-tour-start', function(event) {
			
			//event.preventDefault();

			// Start Tour
			

		});

	}

	if($("#pec_fb_event_option").length) {	
		
		$('#pec_fb_event_option').change(function(event) {

			if($(this).val() == "1") 
			{
				$('#pec_fb_event_url_wrap').show();
				$('#pec_fb_event_page_wrap').hide();
			}

			if($(this).val() == "2") 
			{
				$('#pec_fb_event_url_wrap').hide();
				$('#pec_fb_event_page_wrap').show();
			}

		});

	}

	if($("#pec_booking_ticket_select").length) {	
		
		$('#pec_booking_ticket_select').change(function(event) {
			if($(this).val() != "") {
				var ticket_list = $('#pec_booking_ticket').val();

				if(!$(".pec_ticket_list_wrap").find("[data-ticket-id='" + $(this).val() + "']").length) {
					$('#pec_booking_ticket').val(ticket_list + "," + $(this).val());

					$('.pec_ticket_list_wrap').prepend('<span class="pec_ticket_list" data-ticket-id="'+$(this).val()+'">'+$('option:selected', this).text()+' <span class="dashicons dashicons-dismiss"></span></span>');

				}
			}

		});

	}

	if($(".dp_pec_custom_field_type").length) {	
		
		$(document).on('change', '.dp_pec_custom_field_type', function(event) {
			
			if($(this).val() == "dropdown" || $(this).val() == "multiple_checkbox") {

				$(this).closest('tr').find('.dp_pec_custom_field_add_option').show();
			} else {

				$(this).closest('tr').find('.dp_pec_custom_field_add_option').hide();

			}

		});

	}

	$(document).on('click', '.dp_pec_custom_field_add_option', function(event) {

		$(this).next('input').clone().val('').appendTo( $(this).closest('td') ).focus();

		event.preventDefault();
		return false;

	});

	if($("#pec_get_mclist").length) {	
		
		$('#pec_get_mclist').click(function(event) {

			jQuery('#div_mailchimp_list').hide();
		
			if(jQuery('#mailchimp_api_key').val() != "") {
				
				$.post(ProEventCalendarAjax.ajaxurl, { 
					mailchimp_api: jQuery('#mailchimp_api_key').val(),
					action: 'pec_get_mclist', 
					postEventsNonce : ProEventCalendarAjax.postEventsNonce },
					function(data) {

						jQuery('#mailchimp_list').html(data);
					 	jQuery('#div_mailchimp_list').show();
						
					}
				);	

			}

		});

	}

	if($("#pec_new_booking_show").length) {	
		
		$('#pec_new_booking_show').click(function(event) {
			
			$('#pec_new_booking').slideDown('fast');

			$(this).hide();

		});

		$('#pec_new_booking_submit').click(function(event) {
			
			$.post(ProEventCalendarAjax.ajaxurl, { 
				eventid: $(this).data('dppec-eventid'), 
				userid: $('#pec_new_booking_user').val(), 
				phone: $('#pec_new_booking_phone').val(), 
				date: $('#pec_new_booking_date').val(), 
				quantity: $('#pec_new_booking_quantity').val(), 
				status: $('#pec_new_booking_status').val(), 
				action: 'bookEventAdmin', 
				postEventsNonce : ProEventCalendarAjax.postEventsNonce },
				function(data) {

					data = JSON.parse( data );

					if($('#pec_booking_list_zero').length) 
						$('#pec-booking-list').html(data.html);
					else
						$('#pec-booking-list').prepend(data.html);

					$('#pec_new_booking_show').show();
					$('#pec_new_booking').slideUp('fast');
				}
			);	

		});

		$('#pec_new_booking_cancel').click(function(event) {
			
			$('#pec_new_booking_show').show();
			$('#pec_new_booking').slideUp('fast');

		});
		
	}

	if($(".pec-load-more").length) {	
		
		$('.pec-load-more').click(function(event) {
			var $btn = $(this);
			
			$.post(ProEventCalendarAjax.ajaxurl, { 
				offset: $(this).data('dppec-offset'), 
				eventid: $(this).data('dppec-eventid'), 
				event_date: $('#pec_booking_filter_date').val(), 
				action: 'getMoreBookings', 
				postEventsNonce : ProEventCalendarAjax.postEventsNonce 
			},
				function(data) {
					data = JSON.parse(data);
					$('#pec-booking-list').append(data.html);
					var total = $btn.find('span').text() - 30;
					
					$btn.data('dppec-offset', $btn.data('dppec-offset') + 30);

					if(total <= 0) {
						$btn.hide();
					} else {
						$btn.show();
						$btn.find('span').text(total);
					}
				}
			);	

		});
		
	}

	if($(".pec_error_featured").length) {	

		jQuery(".pec_error_featured").on("focus", function () {
		    jQuery(this).selectText();
		});
		jQuery(".pec_error_featured").on("click", function () {
		    jQuery(this).selectText();
		});
	}

	if($("#pec_booking_filter").length) {	
		
		$('#pec_booking_filter').click(function(event) {
			var $btn = $('.pec-load-more');
			
			$.post(ProEventCalendarAjax.ajaxurl, { 
				offset: 0, 
				eventid: $(this).data('dppec-eventid'), 
				event_date: $('#pec_booking_filter_date').val(), 
				action: 'getMoreBookings', 
				postEventsNonce : ProEventCalendarAjax.postEventsNonce 
			},
				function(data) {
					data = JSON.parse(data);
					$('#pec-booking-list').empty().append(data.html);
					$btn.find('span').text(data.counter);
					var total = data.counter - 30;
					
					$btn.data('dppec-offset', 30);

					if(total <= 0) {
						$btn.hide();
					} else {
						$btn.show();
						$btn.find('span').text(total);
					}
				}
			);	

		});
		
	}
	
	if($(".dpProEventCalendar_ModalCalendar").length) {	

		$('.pec_calendar_actions_open').click(function(event) {
			
			var $list = $(this).closest('.pec_calendar_actions').find('.pec_calendar_actions_list');
			var container = $(".pec_calendar_actions_list").not($list);
			
			container.hide();
			$list.toggle();

		});

		$(document).mouseup(function(e) 
		{
		    var container = $(".pec_calendar_actions_list");
		    var container_open = $(".pec_calendar_actions_open");

		    // if the target of the click isn't the container nor a descendant of the container
		    if (!container_open.is(e.target) && container_open.has(e.target).length === 0) 
		    {
		        container.hide();
		    }
		});

		$('.dpProEventCalendar_btn_getDate').click(function(event) {

			showOverlay();
			
			$('.dp_pec_date:not(.disabled)', '.dpProEventCalendar_ModalCalendar').click(function(event) {
				
				hideOverlay();
				$('#dpProEventCalendar_default_date').val($(this).data('dppec-date'));
				$('.dp_pec_date:not(.disabled)', '.dpProEventCalendar_ModalCalendar').off('click');
				
			});
		});
		
		$('.dpProEventCalendar_btn_getFromDate').click(function(event) {
			
			showOverlay();
			
			$('.dp_pec_date:not(.disabled)', '.dpProEventCalendar_ModalCalendar').click(function(event) {
				
				hideOverlay();
				$('#pec_custom_shortcode_from').val($(this).data('dppec-date'));
				pec_updateShortcode();
				$('.dp_pec_date:not(.disabled)', '.dpProEventCalendar_ModalCalendar').off('click');
				
			});
		});
		
		$('.dpProEventCalendar_btn_getBookingEventDate').click(function(event) {
			
			showOverlay();
			
			$('.dp_pec_date:not(.disabled)', '.dpProEventCalendar_ModalCalendar').click(function(event) {
				
				hideOverlay();
				$('#pec_new_booking_date').val($(this).data('dppec-date'));
				$('.dp_pec_date:not(.disabled)', '.dpProEventCalendar_ModalCalendar').off('click');
				
			});
		});

		$('.dpProEventCalendar_btn_getEventDate').click(function(event) {
			
			showOverlay();
			
			$('.dp_pec_date:not(.disabled)', '.dpProEventCalendar_ModalCalendar').click(function(event) {
				
				hideOverlay();
				$('#pec_date').val($(this).data('dppec-date'));
				$('.dp_pec_date:not(.disabled)', '.dpProEventCalendar_ModalCalendar').off('click');
				
			});
		});
		
		$('.dpProEventCalendar_btn_getEventEndDate').click(function(event) {
			
			showOverlay();
			
			$('.dp_pec_date:not(.disabled)', '.dpProEventCalendar_ModalCalendar').click(function(event) {
				
				hideOverlay();
				$('#pec_end_date').val($(this).data('dppec-date'));
				$('.dp_pec_date:not(.disabled)', '.dpProEventCalendar_ModalCalendar').off('click');
				
			});
		});
		
		$('.dpProEventCalendar_btn_getDateRangeStart').click(function(event) {
			
			showOverlay();
			
			$('.dp_pec_date:not(.disabled)', '.dpProEventCalendar_ModalCalendar').click( function(event) {
				
				hideOverlay();
				$('#dpProEventCalendar_date_range_start').val($(this).data('dppec-date'));
				$('.dp_pec_date:not(.disabled)', '.dpProEventCalendar_ModalCalendar').off('click');
				
			});
		});
		
		$('.dpProEventCalendar_btn_getDateRangeEnd').click(function(event) {
			
			showOverlay();
			
			$('.dp_pec_date:not(.disabled)', '.dpProEventCalendar_ModalCalendar').click(function(event) {
				
				hideOverlay();
				$('#dpProEventCalendar_date_range_end').val($(this).data('dppec-date'));
				$('.dp_pec_date:not(.disabled)', '.dpProEventCalendar_ModalCalendar').off('click');
				
			});
		});
		
		$('.btn_manage_export_calendar').click(function(event) {

			const calendar = $(this).data('calendar-id');

			location.href = "admin.php?page=dpProEventCalendar-admin&export_calendar=" + calendar;

		});

		$('.btn_manage_duplicate').click(function(event) {

			const calendar = $(this).data('calendar-id');

			location.href = "admin.php?page=dpProEventCalendar-admin&duplicate=" + calendar;

		});

		$('.btn_manage_special_dates').click(function(event) {
			var nonce = $(this).data('calendar-nonce');
			var calendar = $(this).data('calendar-id');
			$('.dp_pec_calendar_default', '.dpProEventCalendar_ModalCalendar').hide();
			$('#dp_pec_id'+nonce, '.dpProEventCalendar_ModalCalendar').show();
			
			showOverlay();
			
			$('.dpProEventCalendar_ModalCalendar').on("click", '.dp_pec_date:not(.disabled)', function(event) {
				if($(this).data('sp_date_active')) { return false; }
				$('.dp_pec_date:not(.disabled)', '.dpProEventCalendar_ModalCalendar').data('sp_date_active', false);
				$(this).data('sp_date_active', true);
				
				$('.dp_pec_content', '.dpProEventCalendar_ModalCalendar').css({'overflow': 'visible'});
				
				$('.dp_manage_special_dates', '.dpProEventCalendar_ModalCalendar').hide().parent().css('z-index', 2);
				$('.dp_manage_special_dates', this).show().parent().css('z-index', 3);
				
			});
			
			$('.dpProEventCalendar_ModalCalendar').on('change', '.dp_pec_date:not(.disabled) select', function() 
			{
			   changeSpecialDate($(this), calendar);
			});
		});
		
		$('#btn_manage_special_dates').click(function(event) {
			
			showOverlay();
			
			$('.dpProEventCalendar_ModalCalendar').on("click", '.dp_pec_date:not(.disabled)', function(event) {
				if($(this).data('sp_date_active')) { return false; }
				$('.dp_pec_date:not(.disabled)', '.dpProEventCalendar_ModalCalendar').data('sp_date_active', false);
				$(this).data('sp_date_active', true);
				
				$('.dp_pec_content', '.dpProEventCalendar_ModalCalendar').css({'overflow': 'visible'});
				
				$('.dp_manage_special_dates', '.dpProEventCalendar_ModalCalendar').hide().parent().css('z-index', 2);
				$('.dp_manage_special_dates', this).show().parent().css('z-index', 3);
				
			});
			
			$('.dpProEventCalendar_ModalCalendar').on('change', '.dp_pec_date:not(.disabled) select', function() 
			{
			   changeSpecialDate($(this));
			});
		});
		
	}
	
	function changeSpecialDate(obj, calendar) {
		if($(obj).val() == '') { 

			$(obj).parent().parent().css('background-color', '#fff');

			$(obj).closest('.dp_pec_date').removeClass('dp_pec_special_date');

		} else {
			obj_arr = $(obj).val().split(',');
			var color = obj_arr[1];
			var sp = obj_arr[0];
			$(obj).parent().parent().css('background-color', color);

			$(obj).closest('.dp_pec_date').addClass('dp_pec_special_date');
		}
		
		$.post(ProEventCalendarAjax.ajaxurl, { date: $(obj).parent().parent().parent().data('dppec-date'), sp : sp, calendar: calendar, action: 'setSpecialDates', postEventsNonce : ProEventCalendarAjax.postEventsNonce },
			function(data) {

			}
		);	
	}
	
	function showOverlay() {
		if($(".dpProEventCalendar_Overlay").length) {
			$($overlay).fadeIn('fast');
		} else {
			$('body').append($overlay);
		}
		$(".dpProEventCalendar_ModalCalendar").css({ display: 'none', visibility: 'visible' }).fadeIn('fast');	
	}

	// Add Speakers in event edit page

	if( $( "#pec_speaker" ).length ) {	

		var speakers_list = $( '#pec_speakers_list' ).val();
		var speakers_list_arr = speakers_list.split(',');
		
		$( '#pec_speaker' ).change( function( event ) {	

			var val = $( this ).val();
			var text = $("option:selected", this).text();

			$( this ).val('').selectric('refresh');

			if( $.inArray( val, speakers_list_arr ) != -1 )
				return;
			
			if( val != "" ) 
			{
				$('.pec-component-tag-wrap')
					.append( 
						$('<span />').addClass('pec-component-tag')
						.append(

							$('<span />').addClass('pec-component-tag-text').text( text )
						)
						.append(

							$('<button />').data('speaker_id', val).attr('type', 'button').addClass('').html('<span class="dashicons dashicons-dismiss"></span>')
						)
					);
			}

			if( speakers_list != "" )
				speakers_list += ',' + val;
			else
				speakers_list = val;

			speakers_list_arr = speakers_list.split(',');
			
			$( '#pec_speakers_list' ).val( speakers_list );

			

		});

		$('.pec-component-tag-wrap').on('click', 'button', function() {
			
			var speaker_id = $(this).data('speaker_id').toString();
			speakers_list_arr.splice( $.inArray(speaker_id, speakers_list_arr), 1 );
			

			speakers_list = speakers_list_arr.join( ',' );


			$( '#pec_speakers_list' ).val( speakers_list );

			$(this).closest('.pec-component-tag').remove();
		});

	}
	
	function hideOverlay() {
		$(".dpProEventCalendar_ModalCalendar").fadeOut('fast', function() { $(this).css({ display: 'block', visibility: 'hidden' }) } );	
		$(".dpProEventCalendar_Overlay, .dpProEventCalendar_ModalManager").fadeOut('fast');
		
		$('.dp_manage_special_dates', '.dpProEventCalendar_ModalCalendar').slideUp('fast').parent().css('z-index', 2);
		$('.dp_pec_date:not(.disabled)', '.dpProEventCalendar_ModalCalendar').off('click');	
		$('.dp_pec_date:not(.disabled) select', '.dpProEventCalendar_ModalCalendar').off('change');	
	}
			
}); 

function pec_removeBooking(booking_id, parent_el) {
	jQuery(parent_el).closest('tr').css('opacity', .6);
	jQuery.post(ProEventCalendarAjax.ajaxurl, { booking_id: booking_id, action: 'removeBooking', postEventsNonce : ProEventCalendarAjax.postEventsNonce },
		function(data) {
			jQuery(parent_el).closest('tr').remove();
		}
	);	
}

 