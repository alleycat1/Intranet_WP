<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


// RSS Feed

class DPPEC_RSS extends DpProEventCalendar_Init {
	
	function __construct( ) 
	{

		global $dpProEventCalendar, $pec_init;

		if( isset( $_GET['pec-rss'] ) && is_numeric( $_GET['pec-rss'] ) ) 
			$calendar_id = $_GET['pec-rss'];
		else
			die();

		$all_events = '';
		if( isset( $_GET['all'] ) && $_GET['all'] == 1 ) 
			$all_events = 1;

		// Parse Vars
		$opts = array();
		$opts['id_calendar'] = $calendar_id;
		$opts['include_all_events'] = $all_events;
		$dpProEventCalendar_class = $pec_init->init_base( $opts );

		$calendar_obj = $dpProEventCalendar_class->get_calendar();

		// Is RSS Active?
		if( ! $dpProEventCalendar_class::rss_enabled() ) 
			die();

		// Limit RSS
		$limit = $calendar_obj->rss_limit;
		if( !is_numeric($limit) || $limit <= 0 ) 
			$limit = 99;	

		// Get Events
		$cal_events = $dpProEventCalendar_class::upcomingCalendarLayout( true, $limit, '', null, null, true, false, true, false, false, '', false );
		$blog_desc = ent2ncr(convert_chars(strip_tags(get_bloginfo()))) . " - " . __('Calendar','dpProEventCalendar');

		// Timezone
		$tz = get_option('timezone_string'); // get current PHP timezone
		$gmt_offset = get_option('gmt_offset');
		$minutes_offset = "0";
		if($gmt_offset != "") {
			$minutes_offset = floor($gmt_offset * 60);
			if($minutes_offset < 0) 
				$minutes_offset = "+".str_replace("-", "", $minutes_offset);
			else 
				$minutes_offset = "-".$minutes_offset;
			
		}

		if( $tz == "" ) 
			$tz = date_default_timezone_get();	
		else 
			date_default_timezone_set( $tz ); // set the PHP timezone to match WordPress

		// Feed
		$rssfeed = '<?xml version="1.0" encoding="UTF-8"?>
		<rss version="2.0" xmlns:georss="http://www.georss.org/georss" xmlns:atom="http://www.w3.org/2005/Atom">
		<channel>
		<title>'.$blog_desc.'</title>
		<link>'.home_url().'</link>
		<atom:link type="application/rss+xml" href="https://'.$_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"].'" rel="self"/>
		<description>'.$blog_desc.'</description>
		<language>en-us</language>
		<ttl>40</ttl>';

		// Parse Events
		if(is_array($cal_events)) 
		{

			foreach ( $cal_events as $event ) 
			{
			
				if($event->id == "") 
					$event->id = $event->ID;
				
				$event = (object)array_merge((array)$dpProEventCalendar_class::getEventData($event->id), (array)$event);
				
				
										
				if ( get_option('permalink_structure') ) 
					$link = rtrim(get_permalink($event->id), '/').'/'.strtotime($event->date);
				else 
					$link = get_permalink($event->id).(strpos(get_permalink($event->id), "?") === false ? "?" : "&").'event_date='.strtotime($event->date);
				

				if(get_post_meta($event->id, 'pec_use_link', true) && get_post_meta($event->id, 'pec_link', true) != "") 
					$link = get_post_meta($event->id, 'pec_link', true);
				

				$post_thumbnail_id = get_post_thumbnail_id( $event->id );

				if(is_numeric($post_thumbnail_id)) 
				{
				
					$image_attributes = wp_get_attachment_image_src( $post_thumbnail_id, 'large' );
						
					$event->description = '<img src="'.$image_attributes[0].'" alt="" />'.$event->description;
				
				}

				$rssfeed .= '
				<item>
				<title><![CDATA[' . $event->title . ']]></title>
				<description><![CDATA[' . $event->description . ']]></description>
				<link>'.$link .'</link>
				<guid>'.$link .'</guid>
				<pubDate>' . date("D, d M Y H:i:s O", strtotime($event->date)) . '</pubDate>
				</item>';

			}

		}

		$rssfeed .= '
		</channel>
		</rss>';

		// Headers
		header("Content-Type: application/rss+xml; charset=UTF-8");
		header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");

		echo $rssfeed;
		die();

    }

    
	
}
?>