<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


// RSS Feed

class DPPEC_iCal extends DpProEventCalendar_Init {
	
	function __construct( $type ) 
	{

		if( $type == 'calendar' )
			$this->ical_calendar();

		if( $type == 'event' )
			$this->ical_event();

		die();

    }

    function ical_event()
    {

    	global $pec_init;

    	if( isset( $_GET[ DP_PRO_EVENT_CALENDAR_ICAL_EVENT ] ) && strlen($_GET[ DP_PRO_EVENT_CALENDAR_ICAL_EVENT ]) <= 11 ) 
			$event_id = $_GET[ DP_PRO_EVENT_CALENDAR_ICAL_EVENT ];
		else
			die();

		$date = $_GET['d'];

		$event_date = date( 'Y-m-d', $date );

		// Init Base
		$pec_init->init_base();
		$base = $pec_init->base;

		$event_id = $base->get_event_id_by_code( $event_id );

		$calendar_id = $base->getCalendarByEvent( $event_id );

		if( ! $calendar_id )
			die();

		$base->setCalendar( $calendar_id );

		if( ! $base::$calendar_obj->ical_active ) 
			die();
			
		$limit = $base::$calendar_obj->ical_limit;
		if( ! is_numeric( $limit ) || $limit <= 0 ) 
			$limit = '';	

		$event = $base::getEventData( $event_id );
		$event_date .= ' ' . substr( $event->date, 11 );

		//timezone
		$tz = get_option('timezone_string'); // get current PHP timezone

		$gmt_offset = get_option('gmt_offset');
		$minutes_offset = "0";
		if( $gmt_offset != "" ) 
		{
			
			$minutes_offset = floor( $gmt_offset * 60 );

			if( $minutes_offset < 0 ) 
				$minutes_offset = "+" . str_replace( "-", "", $minutes_offset );
			else 
				$minutes_offset = "-" . $minutes_offset;
			
		}

		if( $tz == "" ) 
			$tz = date_default_timezone_get();	
		else 
			date_default_timezone_set( $tz ); // set the PHP timezone to match WordPress

		//send headers
		header( 'Content-type: text/calendar; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename="events.ics"' );
					
		echo "BEGIN:VCALENDAR\n";
		echo "VERSION:2.0\n";
		echo "PRODID:-//DP Pro Event Calendar//" . DP_PRO_EVENT_CALENDAR_VER . "//EN\n";

				/* @var $event event */
				//date_default_timezone_set('UTC'); // set the PHP timezone to UTC, we already calculated event    

				$dateStamp	= ':' . get_the_date( 'Ymd\THis', $event->id );
				
				if( $event->all_day ) {
					$dateStart	= ';VALUE=DATE:' . date( 'Ymd',strtotime( $event_date ) ); //all day
					$dateEnd	= ';VALUE=DATE:' . date( 'Ymd',strtotime( $event_date ) + 86400 ); //add one day
				} else {
					$dateStart	= ':'.date( 'Ymd\THis',strtotime( $event_date ) );
					if( $event->end_time_hh != "" && $event->end_time_mm != "" ) 
						$dateEnd = ':' . date( 'Ymd\THis', strtotime( substr( $event_date, 0, 11 ) . ' ' . $event->end_time_hh . ':' . $event->end_time_mm . ':00' ) );
					else 
						$dateEnd = ':' . date( 'Ymd\THis', strtotime( $event_date ) );
					
					//&& !$detect->isMobile() 
					if( $this->timezoneDoesDST( $tz, substr( $event_date, 0, 11 ) ) && ( $tz == "UTC" || $tz == "Europe/London" ) ) 
					{
						$dateStart = ':' . date( 'Ymd\THis', strtotime ( '-1 hour' , strtotime( $event_date ) ) );
						if($event->end_time_hh != "" && $event->end_time_mm != "") 
							$dateEnd = ':' . date( 'Ymd\THis',strtotime( '-1 hour' , strtotime( substr( $event_date, 0, 11 ) . ' ' . $event->end_time_hh . ':' . $event->end_time_mm . ':00' ) ) );
						else
							$dateEnd = ':' . date( 'Ymd\THis',strtotime( '-1 hour' , strtotime( $event_date ) ) );
						
					}

					if( ( $tz == "UTC" || $tz == "Europe/London" ) && $minutes_offset != "0" ) 
					{
						$dateStart = ':' . date( 'Ymd\THis', strtotime ( $minutes_offset . ' minutes' , strtotime( $event->date ) ) );
						if( $event->end_time_hh != "" && $event->end_time_mm != "" ) 
							$dateEnd = ':' . date( 'Ymd\THis',strtotime( $minutes_offset . ' minutes' , strtotime( substr( $endDate, 0, 11 ) . ' ' . $event->end_time_hh . ':' . $event->end_time_mm . ':00' ) ) );
						else 
							$dateEnd = ':' . date( 'Ymd\THis',strtotime( $minutes_offset . ' minutes' , strtotime( substr( $endDate, 0, 11 ) . ' 23:59:00' ) ) );
						
					}
				}
				//echo "strtotime ".strtotime($event_date);
				date_default_timezone_set( $tz ); // set the PHP timezone to match WordPress
				
				//formats
				$endash = html_entity_decode('&#x2013;', ENT_COMPAT, 'UTF-8');

				$summary = $event->title;
				$summary = str_replace( '&#8211;', '-', $summary );
				$summary = str_replace( '&#038;', '&', $summary );
				$summary = str_replace( '&#8217;', "'", $summary );
				$summary = str_replace( "\n", '', $summary );
				$summary = str_replace( "\r", '', $summary );
				$summary = str_replace( "\\","\\\\", strip_tags( nl2br( $summary ) ) );
				$summary = str_replace( ';','\;',$summary );
				$summary = str_replace( ',','\,',$summary );
				
				$description = $event->description;
				//$description = str_replace("\n", '', $description);
				//$description = str_replace("\r", '', $description);
				$description = str_replace( "\\","\\\\", strip_tags( $description ) );
				$description = str_replace( "\n", '\n ', $description );
				$description = str_replace( "\r", '\r ', $description );
				$description = str_replace( ';','\;', $description );
				$description = str_replace( ',','\,', $description );
				
				$address = "";
				$location = $event->location;
				$location_id = $event->location_id;

				if( is_numeric( $location_id ) ) 
				{
				
					$address = get_post_meta( $location_id, 'pec_venue_address', true );

					if( $address != "" ) 
						$location = $address;
					
				}

				$location = str_replace( '&#8211;', '-', $location );
				$location = str_replace( '&#038;', '&', $location );
				$location = str_replace( '&#8217;', "'", $location );
				$location = str_replace( "\n", '', $location );
				$location = str_replace( "\r", '', $location );
				$location = str_replace( "<br>", ' ', $location );
				$location = str_replace( "\\","\\\\", strip_tags( nl2br( $location ) ) );
				$location = str_replace( ';','\;',$location );
				$location = str_replace( ',','\,',$location );
				
				$link = $event->link;
				
				$UID = sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
					// 32 bits for "time_low"
					mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
					// 16 bits for "time_mid"
					mt_rand( 0, 0xffff ),
					// 16 bits for "time_hi_and_version",
					// four most significant bits holds version number 4
					mt_rand( 0, 0x0fff ) | 0x4000,
					// 16 bits, 8 bits for "clk_seq_hi_res",
					// 8 bits for "clk_seq_low",
					// two most significant bits holds zero and one for variant DCE1.1
					mt_rand( 0, 0x3fff ) | 0x8000,
					// 48 bits for "node"
					mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
				);
				
				$post_thumbnail_id = get_post_thumbnail_id( $event->id );
				$image_attributes = wp_get_attachment_image_src( $post_thumbnail_id, 'large' );
				$event_bg = "";
				
				if( $post_thumbnail_id ) 
					$event_bg = 'ATTACH;FMTTYPE=image/jpeg:' . $image_attributes[0];

				$rrule = "";
				if( $event->recurring_frecuency == 3 ) 
				{
					$interval = get_post_meta( $event->id, 'pec_monthly_every', true );
					$pec_monthly_position = get_post_meta( $event->id, 'pec_monthly_position', true );
					$pec_monthly_day = get_post_meta( $event->id, 'pec_monthly_day', true );
					$rrule = "RRULE:FREQ=MONTHLY;";
					$rrule .= "INTERVAL=" . $interval . ";";

					switch( $pec_monthly_position ) 
					{
						case 'first':
							$rrule .= "BYSETPOS=1";
							break;
						case 'second':
							$rrule .= "BYSETPOS=2";
							break;
						case 'third':
							$rrule .= "BYSETPOS=3";
							break;
						case 'fourth':
							$rrule .= "BYSETPOS=4";
							break;
						case 'last':
							$rrule .= "BYSETPOS=-1";
							break;
					}

					switch( $pec_monthly_day ) 
					{
						case 'monday':
							$rrule .= "BYDAY=MO";
							break;
						case 'tuesday':
							$rrule .= "BYDAY=TU";
							break;
						case 'wednesday':
							$rrule .= "BYDAY=WE";
							break;
						case 'thursday':
							$rrule .= "BYDAY=TH";
							break;
						case 'friday':
							$rrule .= "BYDAY=FR";
							break;
						case 'saturday':
							$rrule .= "BYDAY=SA";
							break;
						case 'sunday':
							$rrule .= "BYDAY=SU";
							break;
					}
					
				}
				
		echo "BEGIN:VEVENT\n";
		echo "UID:{$UID}\n";
		echo "DTSTART;TZID={$tz}{$dateStart}\n";
		echo "DTEND;TZID={$tz}{$dateEnd}\n";
		echo "DTSTAMP{$dateStamp}Z\n";
		echo "SUMMARY:{$summary}\n";
		echo "DESCRIPTION:{$description}\n";
		echo "LOCATION:{$location}\n";
		echo "URL:{$link}\n";
		if( $rrule != "" )
			echo $rrule;

		if( $event_bg != "" ) 
			echo "{$event_bg}\n";
		
		echo "END:VEVENT\n";

		echo "END:VCALENDAR";

		date_default_timezone_set( $tz ); // set the PHP timezone back the way it was

    }

    function ical_calendar()
    {

    	global $pec_init;

		if( isset( $_GET[ DP_PRO_EVENT_CALENDAR_ICAL ] ) && is_numeric( $_GET[ DP_PRO_EVENT_CALENDAR_ICAL ] ) ) 
			$calendar_id = $_GET[ DP_PRO_EVENT_CALENDAR_ICAL ];
		else
			die();

		if( isset( $_GET['all'] ) && $_GET['all'] == 1 )  
			$all_events = 1;

		$category = '';
		if( isset($_GET['cat'] ) && is_numeric($_GET['cat'] ) ) 
			$category = $_GET['cat'];

		$opts = array();
		$opts['id_calendar'] = $calendar_id;
		$opts['category'] = $category;
		$opts['include_all_events'] = $all_events;
		$dpProEventCalendar_class = $pec_init->init_base( $opts );

		$calendar_obj = $dpProEventCalendar_class->get_calendar();

		if( ! $calendar_obj->ical_active ) 
			die();
			
		$limit = $calendar_obj->ical_limit;
		if( ! is_numeric( $limit ) || $limit <= 0 ) 
			$limit = 99;	


		$cal_events = $dpProEventCalendar_class::upcomingCalendarLayout( true, $limit, '', null, null, true, false, true, false, false, '', false );

		//timezone
		$tz = get_option( 'timezone_string' ); // get current PHP timezone
		$gmt_offset = get_option( 'gmt_offset' );
		$minutes_offset = "0";
		if( $gmt_offset != "" ) 
		{
			$minutes_offset = floor( $gmt_offset * 60 );
			if( $minutes_offset < 0 ) 
				$minutes_offset = "+" . str_replace( "-", "", $minutes_offset );
			else
				$minutes_offset = "-" . $minutes_offset;
			
		}

		if( $tz == "" ) 
			$tz = date_default_timezone_get();	

		date_default_timezone_set( get_option('timezone_string' ) ); // set the PHP timezone to match WordPress

		//send headers 

		header( 'Content-type: text/calendar; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename="events.ics"' );

		echo "BEGIN:VCALENDAR\n";
		echo "VERSION:2.0\n";
		echo "PRODID:-//DP Pro Event Calendar//" . DP_PRO_EVENT_CALENDAR_VER . "//EN\n";

		/*
		echo "
		BEGIN:VTIMEZONE
		TZID:".$tz."
		X-LIC-LOCATION:".$tz."
		BEGIN:DAYLIGHT
		TZOFFSETFROM:-0800
		TZOFFSETTO:-0700
		TZNAME:PDT
		DTSTART:19700308T020000
		RRULE:FREQ=YEARLY;BYMONTH=3;BYDAY=2SU
		END:DAYLIGHT
		BEGIN:STANDARD
		TZOFFSETFROM:-0700
		TZOFFSETTO:-0800
		TZNAME:PST
		DTSTART:19701101T020000
		RRULE:FREQ=YEARLY;BYMONTH=11;BYDAY=1SU
		END:STANDARD
		END:VTIMEZONE";
		*/

		$processed = array();

		if( is_array( $cal_events ) ) 
		{

			foreach ( $cal_events as $key => $event ) 
			{
			
				if( $event->id == "" ) 
					$event->id = $event->ID;

				$event = (object)array_merge((array)$dpProEventCalendar_class::getEventData($event->id), (array)$event);  
				
				// To Be Confirmed ?
				if( $event->tbc ) 
					continue;

				if( $event->end_date != "" && $event->end_date != "0000-00-00" ) 
					$endDate = $event->end_date;
				else
					$endDate = $event->date;
				
				if( $event->recurring_frecuency == 1 && in_array( $event->id, $processed ) ) 
					continue;
				
				
				if( $event->recurring_frecuency == 2 || $event->recurring_frecuency == 3 ) 
					$endDate = $event->date;
				
				
				$processed[] = $event->id;
				
				$dateStamp	= ':' . get_the_date( 'Ymd\THis', $event->id );

				if( $event->all_day ){
					$dateStart	= ';VALUE=DATE:' . date( 'Ymd',strtotime( $event->date ) ); //all day
					$dateEnd	= ';VALUE=DATE:' . date( 'Ymd',strtotime( $endDate ) + 86400 ); //add one day
				} else {
					$dateStart	= ':' . date( 'Ymd\THis', strtotime( $event->date ) );

					if( $event->end_time_hh != "" && $event->end_time_mm != "" ) {
						$dateEnd = ':' . date( 'Ymd\THis', strtotime( substr( $endDate, 0, 11 ) . ' ' . $event->end_time_hh . ':' . $event->end_time_mm .':00' ) );
					} else {
						$dateEnd = ':' . date( 'Ymd\THis', strtotime( substr( $endDate, 0, 11 ) . ' 23:59:00' ) );
					}
					//&& !$detect->isMobile() 
					if( $this->timezoneDoesDST( $tz, substr( $event_date, 0, 11 ) ) && ( $tz == "UTC" || $tz == "Europe/London" ) ) {
						$dateStart = ':' . date( 'Ymd\THis', strtotime ( '-1 hour' , strtotime( $event->date ) ) );
						if( $event->end_time_hh != "" && $event->end_time_mm != "" ) {
							$dateEnd = ':' . date( 'Ymd\THis', strtotime( '-1 hour' , strtotime( substr( $endDate, 0, 11 ) . ' ' . $event->end_time_hh . ':' . $event->end_time_mm .':00' ) ) );
						} else {
							$dateEnd = ':' . date( 'Ymd\THis', strtotime( '-1 hour' , strtotime( substr( $endDate, 0, 11 ) . ' 23:59:00' ) ) );
						}
					}

					if( ( $tz == "UTC" || $tz == "Europe/London" ) && $minutes_offset != "0" ) {
						$dateStart = ':' . date( 'Ymd\THis', strtotime ( $minutes_offset . ' minutes' , strtotime( $event->date ) ) );
						if( $event->end_time_hh != "" && $event->end_time_mm != "" ) {
							$dateEnd = ':' . date( 'Ymd\THis', strtotime( $minutes_offset . ' minutes' , strtotime( substr( $endDate, 0, 11) . ' ' . $event->end_time_hh . ':' . $event->end_time_mm .':00' ) ) );
						} else {
							$dateEnd = ':' . date( 'Ymd\THis', strtotime( $minutes_offset . ' minutes' , strtotime( substr( $endDate, 0, 11) . ' 23:59:00' ) ) );
						}
					}

				}
				
				
				date_default_timezone_set( get_option( 'timezone_string' ) ); // set the PHP timezone to match WordPress
				
				//formats
				$summary = $event->title;
				$summary = str_replace( '&#8211;', '-', $summary );
				$summary = str_replace( '&#038;', '&', $summary );
				$summary = str_replace( '&#8217;', "'", $summary );
				$summary = str_replace( "\n", '', $summary );
				$summary = str_replace( "\r", '', $summary );
				$summary = str_replace( "\\","\\\\", strip_tags( nl2br( $summary ) ) );
				$summary = str_replace( ';','\;',$summary );
				$summary = str_replace( ',','\,',$summary );
				
				$description = $event->description;
				//$description = str_replace("\n", '', $description);
				//$description = str_replace("\r", '', $description);
				$description = str_replace( "\\","\\\\", strip_tags( $description ) );
				$description = str_replace( "\n\r", '\n', $description );
				$description = str_replace( "\n", '\n ', $description );
				$description = str_replace( "\r", '', $description );
				$description = str_replace( ';','\;', $description );
				$description = str_replace( ',','\,', $description );
				
				$address = "";
				$location = $event->location;
				$location_id = $event->location_id;
				if( is_numeric( $location_id ) ) 
				{
				
					$address = get_post_meta( $location_id, 'pec_venue_address', true );

					if( $address != "" ) 
						$location = $address;
					
				}

				$location = str_replace( '&#8211;', '-', $location );
				$location = str_replace( '&#038;', '&', $location );
				$location = str_replace( '&#8217;', "'", $location );
				$location = str_replace( "\n", '', $location );
				$location = str_replace( "\r", '', $location );
				$location = str_replace( "<br>", ' ', $location );
				$location = str_replace( "\\","\\\\", strip_tags( nl2br( $location ) ) );
				$location = str_replace( ';','\;',$location );
				$location = str_replace( ',','\,',$location );
				
				$link = $event->link;
				
				$UID = sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
					// 32 bits for "time_low"
					mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
					// 16 bits for "time_mid"
					mt_rand( 0, 0xffff ),
					// 16 bits for "time_hi_and_version",
					// four most significant bits holds version number 4
					mt_rand( 0, 0x0fff ) | 0x4000,
					// 16 bits, 8 bits for "clk_seq_hi_res",
					// 8 bits for "clk_seq_low",
					// two most significant bits holds zero and one for variant DCE1.1
					mt_rand( 0, 0x3fff ) | 0x8000,
					// 48 bits for "node"
					mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
				);
				
				$post_thumbnail_id = get_post_thumbnail_id( $event->id );
				$image_attributes = wp_get_attachment_image_src( $post_thumbnail_id, 'large' );
				$event_bg = "";
				
				if( $post_thumbnail_id ) 
					$event_bg = 'ATTACH;FMTTYPE=image/jpeg:' . $image_attributes[0];
				

				$rrule = "";
				if( $event->recurring_frecuency == 3 ) {
					$interval = get_post_meta( $event->id, 'pec_monthly_every', true );
					$pec_monthly_position = get_post_meta( $event->id, 'pec_monthly_position', true );
					$pec_monthly_day = get_post_meta( $event->id, 'pec_monthly_day', true );
					$rrule = "RRULE:FREQ=MONTHLY;";
					$rrule .= "INTERVAL=" . $interval . ";";
					switch( $pec_monthly_position ) {
						case 'first':
							$rrule .= "BYSETPOS=1";
							break;
						case 'second':
							$rrule .= "BYSETPOS=2";
							break;
						case 'third':
							$rrule .= "BYSETPOS=3";
							break;
						case 'fourth':
							$rrule .= "BYSETPOS=4";
							break;
						case 'last':
							$rrule .= "BYSETPOS=-1";
							break;
					}

					switch( $pec_monthly_day ) {
						case 'monday':
							$rrule .= "BYDAY=MO";
							break;
						case 'tuesday':
							$rrule .= "BYDAY=TU";
							break;
						case 'wednesday':
							$rrule .= "BYDAY=WE";
							break;
						case 'thursday':
							$rrule .= "BYDAY=TH";
							break;
						case 'friday':
							$rrule .= "BYDAY=FR";
							break;
						case 'saturday':
							$rrule .= "BYDAY=SA";
							break;
						case 'sunday':
							$rrule .= "BYDAY=SU";
							break;
					}
					
				}

		echo "BEGIN:VEVENT\n";
		echo "UID:{$UID}\n";
		if( $tz != "UTC" ) {
			echo "DTSTART;TZID={$tz}{$dateStart}\n";
			echo "DTEND;TZID={$tz}{$dateEnd}\n";
		} elseif( $event->all_day ) {
			echo "DTSTART{$dateStart}\n";
			echo "DTEND{$dateEnd}\n";
		} else {
			echo "DTSTART{$dateStart}Z\n";
			echo "DTEND{$dateEnd}Z\n";
		}
		/*
		if($rrule != "") {
			echo  "
			".$rrule;
		}*/
		echo "DTSTAMP{$dateStamp}Z\n";
		echo "SUMMARY:{$summary}\n";
		echo "DESCRIPTION:{$description}\n";
		echo "LOCATION:{$location}\n";
		echo "URL:{$link}\n";
		if( $event_bg != "" )
			echo "{$event_bg}\n";
		
		echo "END:VEVENT\n";
			}
		}
		echo "END:VCALENDAR";
		date_default_timezone_set($tz); // set the PHP timezone back the way it was

    }

    function timezoneDoesDST( $tzId, $time = "" ) 
    {

		if( class_exists( 'DateTimeZone' ) && $tzId != "" ) 
		{
			$tz = new DateTimeZone( $tzId );
			$date = new DateTime( $time != "" ? $time : "now", $tz );  
			$trans = $tz->getTransitions();
			foreach ( $trans as $k => $t ) 
			{
				if ( $t["ts"] > $date->format('U') ) 
					return $trans[$k-1]['isdst'];    
				
			}
		} else 
			return false;	
		
	}

    
	
}
?>