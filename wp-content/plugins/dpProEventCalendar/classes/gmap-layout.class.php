<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


// Gmap Layout

class DPPEC_GmapLayout extends DpProEventCalendar {
	

	function __construct( ) 
	{
		
    }

    public function display_layout()
    {

    	global $dpProEventCalendar;

    	$html = '
			<div class="dp_pec_gmap_wrapper" id="dp_pec_id'.self::$nonce.'">
			';
			
			$event_list = self::upcomingCalendarLayout( true, self::$limit, '', null, null, true, false, true, true );
			$unique_events = array();
			$event_marker = "";
			$first_loc = "";
			
			if( is_array( $event_list ) ) 
			{
				
				foreach ( $event_list as $obj ) 
				{
				
					if( $obj->id == "" ) 
					{
					
						$obj->id = $obj->ID;
					
					}

					$obj = (object)array_merge((array)self::getEventData($obj->id), (array)$obj);

					if(is_numeric($obj->location_id)) 
					{

						$map_lnlat = get_post_meta($obj->location_id, 'pec_venue_map_lnlat', true);
					
						$venue_address = get_post_meta($obj->location_id, 'pec_venue_address', true);
						if($venue_address != "") 
						{
						
							$obj->map = $venue_address;
						
						} else {
						
							$obj->map = get_post_meta($obj->location_id, 'pec_venue_map', true);
						
						}

						if($obj->map == "" && $map_lnlat != "") 
						{
						
							$obj->map = $obj->location;
						
						}
					
					} else {
					
						$obj->map = get_post_meta($obj->id, 'pec_map', true);
						$map_lnlat = get_post_meta($obj->id, 'pec_map_lnlat', true);
					
					}

					if($obj->map == "") 
						continue;
					
					
					if(!isset($unique_events[$obj->id])) 
						$unique_events[$obj->id] = '';
					
					
					if(!is_object($unique_events[$obj->id])) 
					{
					
						$unique_events[$obj->id] = $obj;

						$time = self::date_i18n(self::$time_format, strtotime($obj->date));
		
						$end_datetime = self::get_end_datetime( $obj );
						$end_date = $end_datetime['end_date'];
						$end_time = $end_datetime['end_time'];
		
						if(isset($obj->all_day) && $obj->all_day) {
							$time = self::$translation['TXT_ALL_DAY'];
							$end_time = "";
						}
						
						$title = $obj->title;
						if(self::$calendar_obj->link_post) {
							$title = '<a href="'.dpProEventCalendar_get_permalink($obj->id).'" target="'.self::$calendar_obj->link_post_target.'">'.addslashes($title).'</a>';	
						}
						
						$category = get_the_terms( $obj->id, 'pec_events_category' ); 
						$category_list_html = '';
						if(!empty($category)) {
							$category_count = 0;
							foreach ( $category as $cat){
								if($category_count > 0) {
									$category_list_html .= " / ";	
								}
								$category_list_html .= $cat->name;
								$category_count++;
							}
						}
						
						$video = get_post_meta( $obj->id, 'pec_video', true );
						if( $video != '' ) 
						{
						
							$video = $this->convert_youtube( $video );
						
						}

						$event_timezone = dpProEventCalendar_getEventTimezone($obj->id);

						$event_time = self::date_i18n(get_option('date_format'), strtotime($obj->date)).$end_date.(((self::$calendar_obj->show_time && !$obj->hide_time) || $obj->all_day) ? ' - '.$time.$end_time.(self::$calendar_obj->show_timezone && !$obj->all_day ? ' '.$event_timezone : '') : '');

						if($obj->tbc) {
							$event_time = self::$translation['TXT_TO_BE_CONFIRMED'];
						}

						$image = esc_url( get_the_post_thumbnail_url( $obj->location_id, 'medium' ) );
						if( $image != "" )
							$image = "background-image: url(" . $image . ")";

						$event_marker .= 'pec_codeAddress("'.$obj->link.'", "'.$category_list_html.'", "'.$obj->phone.'",\''.$obj->map.'\', \''.addslashes($title).'\', \'' . $image . '\', \''.$event_time.'\', "'.$map_lnlat.'", \''.$video.'\'); ';
					}
					if($first_loc == "") {
						$first_loc = $obj->map;
					}
				}
				

			$html .= '
				<div style="clear:both;"></div>
				<div class="dp_pec_map_canvas" id="dp_pec_map_canvas'.self::$nonce.'"></div>
				
				<script type="text/javascript">
				jQuery(document).ready(function() {
					var geocoder, map, oms;

					function initialize' . self::$nonce . '() {
					 geocoder = new google.maps.Geocoder();
					 geocoder.geocode( { "address": "' . $first_loc . '"}, function(results, status) {
					 	if( status != "OVER_QUERY_LIMIT" && typeof results[0] != "undefined" ) {

						  var latlng = results[0].geometry.location;

						  } else {

						  	var latlng = ' . ( $dpProEventCalendar['map_default_latlng'] != "" ? 'new google.maps.LatLng(' . $dpProEventCalendar['map_default_latlng'] . ')' : 'new google.maps.LatLng(0,0)' ) . ';
						  }

						  
						  var mapOptions = {
							zoom: ' . ( $dpProEventCalendar['google_map_zoom'] == "" ? 10 : $dpProEventCalendar['google_map_zoom'] ) . ',
							center: latlng
						  }
						  map = new google.maps.Map(document.getElementById("dp_pec_map_canvas' . self::$nonce . '"), mapOptions);

  						  oms = new OverlappingMarkerSpiderfier(map, {markersWontMove: true, markersWontHide: true, keepSpiderfied: true});

  						  oms.addListener("click", function(marker) {
							infoBubble.close();
							  
						    infoBubble.setContent(marker.content);

					    	infoBubble.open(map, marker);
						  });
				';
				$html .= $event_marker;
				$html .= '
					 });
					  ';
				
				$html .= '
					}
					

					var infoBubble = new InfoBubble({
				        maxWidth: 290,
						maxHeight: 320,					
						shadowStyle: 0,
						padding: 0,
						backgroundColor: \'#fff\',
						borderRadius: 5,
						arrowSize: 20,
						borderWidth: 0,
						arrowPosition: 20,
						backgroundClassName: \'pec-infowindow\',
						arrowStyle: 2,
						hideCloseButton: true
				    });

					//var infowindow = new google.maps.InfoWindow();
					
					function getInfoWindowEvent(marker, content) {
						infowindow.close();
						infowindow.setContent(content);
						infowindow.open(map, marker);
					}

					var counter_run = 0;

					function pec_codeAddress(link, category, phone, address, title, image, eventdate, latlng, video) {
						
						var div_class = "dp_pec_map_infowindow";
						if(image == "") {
							div_class += " dp_pec_map_no_img";
						} else {
							image = \'<div class="dp_pec_map_image" style="\' + image + \'"></div>\';
						}

						var content = \'<div class="\'+div_class+\'">\'
							+(video != "" ? video : image)
							+\'<span class="dp_pec_map_date"><i class="fa fa-clock"></i>\'+eventdate+\'</span><div class="dp_pec_clear"></div>\'
							+\'<span class="dp_pec_map_title">\'+title+\'</span>\';

						if(address != "") {
							content += \'<span class="dp_pec_map_location"><i class="fa fa-map-marker"></i>\'+address+\'</span>\';
						}

						if(phone != "") {
							content += \'<span class="dp_pec_map_phone"><i class="fa fa-phone"></i>\'+phone+\'</span>\';
						}

						if(category != "") {
							content += \'<span class="dp_pec_map_category"><i class="fa fa-folder"></i>\'+category+\'</span>\';
						}

						if(link != "") {
							var link_ellipsy = link;

							if (link_ellipsy.length > 25) {
						        link_ellipsy = (link_ellipsy.substring(0, 25) + "...");
						    }
							content += \'<span class="dp_pec_map_link"><i class="fa fa-link"></i><a href="\'+link+\'" target="_blank" rel="nofollow">\'+link_ellipsy+\'</a></span>\';
						}
						content +=\'<div class="dp_pec_clear"></div>\'
							+\'</div>\';

						setTimeout(function() { 
						  if(latlng != "") {
							  latlng = latlng.split(",");
							  
							  var myLatlng = new google.maps.LatLng(latlng[0],latlng[1]);
							  var marker = new google.maps.Marker({
								  map: map,
								  position: myLatlng,
								  icon: "'.$dpProEventCalendar['map_marker'].'",
								  content: content,
								  animation: google.maps.Animation.DROP
							  });	

							  oms.addMarker(marker);

						  } else {
						  geocoder.geocode( { "address": address}, function(results, status) {
							if (status == google.maps.GeocoderStatus.OK) {
							  //map.setCenter(results[0].geometry.location);
							  var marker = new google.maps.Marker({
								  map: map,
								  position: results[0].geometry.location,
								  icon: "'.$dpProEventCalendar['map_marker'].'",
								  content: content,
								  animation: google.maps.Animation.DROP
							  });
							  
							  oms.addMarker(marker);
							} else {
								console.log("Geocode was not successful for the following reason: " + status);
							}
							
						  });
						  

						  }

						  if(!jQuery(".pec_infowindow_close", infoBubble.bubble_).length) {
						  	var close = jQuery(\'<a href="#" id="pec_infowindow_close" class="pec_infowindow_close"><i class="fa fa-close"></i></a>\');

						  	close.click(function(e) {
						  		e.preventDefault();
						  		infoBubble.close();
						  	});
						  	jQuery(infoBubble.bubble_).prepend(close);

						  }

						}, (counter_run < 10 ? 0 : (1000 * counter_run)) );
						  
						  counter_run++;
					}

					if (typeof google !== "undefined") {
						google.maps.event.addDomListener(window, "load", initialize'.self::$nonce.');
					}
					
				});
				</script>';
			} else {
				$html .= '<div class="dp_pec_accordion_event dp_pec_accordion_no_events"><span>'.self::$translation['TXT_NO_EVENTS_FOUND'].'</span></div>
				<div class="dp_pec_clear"></div>';	
			}
				
			$html .= '
			</div>';


		return $html;


    }
	
}
?>