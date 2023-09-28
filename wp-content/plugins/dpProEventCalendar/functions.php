<?php

/*
 * DP Pro Event Calendar - Payments Extension
 *
 * Copyright 2012, Diego Pereyra
 *
 * @Web: https://www.wpsleek.com
 * @Email: dpereyra90@gmail.com
 *
 * General functions WP Pro Event Calendar
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Parse the plugin settings data
 * 
 * @return string - requested field
 */
function pec_setting( $key, $default = '' )
{

	global $dpProEventCalendar;

	if( isset( $dpProEventCalendar[$key] ) )
		return $dpProEventCalendar[$key];

	return $default;

}

/**
 * Get plugin url
 * 
 * @return string - requested field
 */
function dpProEventCalendar_plugin_url( $path = '', $protocol = '' ) 
{

	global $wp_version;
	$return_url = "";

	if ( version_compare( $wp_version, '2.8', '<' ) ) 
	{ // Using WordPress 2.7
	
		$folder = dirname( plugin_basename( __FILE__ ) );
	
		if ( '.' != $folder )
			$path = path_join( ltrim( $folder, '/' ), $path );

		$return_url = plugins_url( $path );
	
		if($protocol != "") 
		
			$return_url = str_replace(array('http://', 'https://'), $protocol.'://', $return_url);
	
		return $return_url;
	
	}

	$return_url = plugins_url( $path, __FILE__ );
	if($protocol != "") 
		$return_url = str_replace(array('http://', 'https://'), $protocol.'://', $return_url);
	
	return $return_url;

}

// Fix links in the archives page for events

add_filter('the_permalink', 'dpProEventCalendar_archive_links');

add_filter('post_type_link', 'dpProEventCalendar_archive_links');

function dpProEventCalendar_archive_links( $post_link, $post = '' ) {

	if( $post == '' )
		global $post;

	if ( is_post_type_archive ( DP_PRO_EVENT_CALENDAR_EVENT_POST_TYPE ) && $post->post_type == 'pec-events' ) {

		global $pec_init;

		$dpProEventCalendar_class = $pec_init->init_base( );

		if( $post->id == "" ) 
			$post->id = $post->ID;
		
		$event = (object)array_merge((array)$dpProEventCalendar_class::getEventData( $post->ID ), (array)$post );
		
		$link = $dpProEventCalendar_class::get_permalink ( $event, $event->date, true );

		if( $link != '' )
			$post_link = $post_link = $link;

	}

	return $post_link;
}

function dpProEventCalendar_convertBytes( $value ) 
{

    if ( is_numeric( $value ) ) 
        return $value;
    else {
    
        $value_length = strlen($value);
        $qty = substr( $value, 0, $value_length - 1 );
        $unit = strtolower( substr( $value, $value_length - 1 ) );
        
        switch ( $unit ) 
        {
        
            case 'k':
                $qty *= 1024;
                break;
        
            case 'm':
                $qty *= 1048576;
                break;
        
            case 'g':
                $qty *= 1073741824;
                break;
        
        }

        return $qty;
    
    }

}

function dpProEventCalendar_wp_mail_from_name( $original_email_from )
{
	return get_bloginfo('name');
}

function dpProEventCalendar_wp_mail_from( $original_email_address ) 
{

	global $dpProEventCalendar;

	return ($dpProEventCalendar['wp_mail_from'] != "" ? $dpProEventCalendar['wp_mail_from'] : $original_email_address);

}

add_filter( 'pec_booking_email', 'dpProEventCalendar_bookingEmail', 10, 9 );

function dpProEventCalendar_bookingEmail( $template, $event_id, $user_name, $user_email, $event_date, $comment, $quantity, $user_phone = '', $extra_fields = '' ) 
{

	global $dpProEventCalendar;
	
	$template = str_replace( "#USERNAME#", $user_name, $template );
	
	$template = str_replace( "#COMMENT#", $comment, $template );
	
	$template = str_replace( "#USEREMAIL#", $user_email, $template );
	
	$template = str_replace( "#USERPHONE#", $user_phone, $template );
	
	$location_id = get_post_meta( $event_id, 'pec_location', true );
	$location = get_the_title( $location_id );
	$address = get_post_meta( $location_id, 'pec_venue_address', true );

	if($address != "") 
		$location .= " (".$address.")";

	$extra_fields = unserialize($extra_fields);

	if( !is_array( $extra_fields ) ) 
		$extra_fields = array();

	$custom_fields = '';
	
	foreach( $extra_fields as $key => $value ) 
	{

		$field_index = array_keys($dpProEventCalendar['booking_custom_fields']['id'], str_replace('pec_custom_', '', $key));
		
		if(is_array($field_index)) 
    		$field_index = $field_index[0];
    	else 
    		$field_index = '';
    	
		if($value != "" && is_numeric($field_index)) 
		{

			if($dpProEventCalendar['booking_custom_fields']['type'][$field_index] == 'checkbox') 

				$value = __('Yes', 'dpProEventCalendar');
			
			$custom_fields .= $dpProEventCalendar['booking_custom_fields']['name'][$field_index].": ".$value."\n\r";
		
		}

	}


	$template = str_replace("#EVENT_DETAILS#", "---------------------------\n\r".get_the_title($event_id)."\n\r".dpProEventCalendar_date_i18n(get_option('date_format'), strtotime($event_date)).' - '.date(get_option('time_format'), strtotime(get_post_meta($event_id, 'pec_date', true)))."\n\r".__("Quantity", "dpProEventCalendar").": ".$quantity."\n\r".$location."\n\r".$custom_fields."---------------------------\n\r", $template);

	$template = str_replace("#SITE_NAME#", get_bloginfo('name'), $template);
	
	return nl2br($template);
	
}

add_filter('pec_booking_email_cancel', 'dpProEventCalendar_bookingEmailCancel', 10, 2);

function dpProEventCalendar_bookingEmailCancel($template, $booking_id) 
{

	global $wpdb, $dpProEventCalendar;

	$booking_count = 0;
    $querystr = "
    SELECT *
    FROM " . DP_PRO_EVENT_CALENDAR_TABLE_BOOKING . "
	WHERE id = %d";

    $booking = $wpdb->get_row( $wpdb->prepare( $querystr, $booking_id), OBJECT );

    if( is_numeric( $booking->id_user ) && $booking->id_user > 0) {
		$userdata = get_userdata($booking->id_user);
	} else {
		$userdata = new stdClass();
		$userdata->display_name = $booking->name;
		$userdata->user_email = $booking->email;	
	}

	$event_id = $booking->id_event;
	$event_date = $booking->event_date;
	$quantity = $booking->quantity;
	$cancel_reason = $booking->cancel_reason;

	$template = str_replace("#USERNAME#", $userdata->display_name, $template);
	
	$template = str_replace("#COMMENT#", $booking->comment, $template);
	
	$template = str_replace("#USEREMAIL#", $userdata->user_email, $template);
	
	$template = str_replace("#USERPHONE#", $booking->phone, $template);
	
	$location_id = get_post_meta($event_id, 'pec_location', true);
	$location = get_the_title($location_id);
	$address = get_post_meta($location_id, 'pec_venue_address', true);
	if($address != "") 
		$location .= " (".$address.")";

	$template = str_replace("#EVENT_DETAILS#", "---------------------------\n\r".get_the_title($event_id)."\n\r".dpProEventCalendar_date_i18n(get_option('date_format'), strtotime($event_date)).' - '.date(get_option('time_format'), strtotime(get_post_meta($event_id, 'pec_date', true)))."\n\r".__("Quantity", "dpProEventCalendar").": ".$quantity."\n\r".$location."\n\r---------------------------\n\r", $template);
	
	$template = str_replace("#CANCEL_REASON#", $cancel_reason, $template);

	$template = str_replace("#SITE_NAME#", get_bloginfo('name'), $template);
	
	return nl2br($template);
	
}

add_filter('pec_new_event_published', 'dpProEventCalendar_eventPublished', 10, 6);

function dpProEventCalendar_eventPublished( $template, $event_title, $user_name ) 
{
	
	$template = str_replace("#USERNAME#", $user_name, $template);
	
	$template = str_replace("#EVENT_TITLE#", $event_title, $template);
		
	$template = str_replace("#SITE_NAME#", get_bloginfo('name'), $template);
	
	return html_entity_decode ( $template );
	
}

/*
function dpProEventCalendar_loadTemplate( $template ) {
	global $dpProEventCalendar;
	
	// assuming you have created a page/post entitled 'debug'
	if ($GLOBALS['post']->post_type == DP_PRO_EVENT_CALENDAR_EVENT_POST_TYPE && $dpProEventCalendar['event_single_enable']) {
		
		remove_filter( 'the_content', 'dpProEventCalendar_contentFilter' );
		remove_all_actions('wp_enqueue_scripts');
		return dirname( __FILE__ ) . '/templates/default/template.php';
		
	}
	
	return $template;
	
}

add_filter( 'template_include', 'dpProEventCalendar_loadTemplate', 100 );*/

if(!function_exists('cal_days_in_month')) 
{
	
	function cal_days_in_month($month, $year) { 
		return date('t', mktime(0, 0, 0, $month+1, 0, $year)); 
	}
	
}

function dpProEventCalendar_booking_reminder() 
{

	global $wpdb, $dpProEventCalendar, $pec_init;
	
	$days_reminder_setting = $dpProEventCalendar['days_reminders'];
	if( ! is_numeric( $days_reminder_setting ) || $days_reminder_setting == 0 ) 
		$days_reminder_setting = 3;
	
	$booking_search_date = date( 'Y-m-d', strtotime( '+'.$days_reminder_setting.' days' ) );
	$booking_search_id = array();
	
	//Search events with continuous bookings
	$args = array( 
			'posts_per_page' => -1, 
			'post_type'=> DP_PRO_EVENT_CALENDAR_EVENT_POST_TYPE, 
			'meta_query' => array(
		array(
			'key' => 'pec_booking_continuous',
			'value' => 1,
		))
	);

	$continuous_events = get_posts( $args );

	if( ! empty( $continuous_events ) ) 
	{

		foreach( $continuous_events as $event ) 
		{
			$id_calendar = get_post_meta( $event->ID, 'pec_id_calendar', true );
			$id_calendar = explode(',', $id_calendar);
			$id_calendar = $id_calendar[0];

			$opts = array();
			$opts['id_calendar'] = $id_calendar;
			$opts['event_id'] = $event->ID;
			$dpProEventCalendar_class = $pec_init->init_base( $opts );

			$event_dates = $dpProEventCalendar_class::upcomingCalendarLayout( true, 1, '', $booking_search_date." 00:00:00", $booking_search_date." 23:59:59" );
			
			if(is_array($event_dates) && !empty($event_dates)) 
				$booking_search_id[] = $event->ID;
			
		}
	}

	$querystr = "
	SELECT *
	FROM " . DP_PRO_EVENT_CALENDAR_TABLE_BOOKING . "
	WHERE (event_date = '".$booking_search_date."' ";
	if(is_array($booking_search_id) && !empty($booking_search_id)) {
		$querystr .= "OR id_event IN (".implode(",", $booking_search_id).")";
	}
	$querystr .= ") AND status <> '" . DP_PRO_EVENT_CALENDAR_BOOKING_PENDING . "' AND status <> '" . DP_PRO_EVENT_CALENDAR_BOOKING_CANCELED . "' AND status <> '" . DP_PRO_EVENT_CALENDAR_BOOKING_CANCELED_BY_USER . "'
	ORDER BY id DESC
	";
	
	$bookings_obj = $wpdb->get_results($querystr, OBJECT);
	$bookings_count = 0;
	if( ! empty( $bookings_obj ) ) 
	{
		
		foreach( $bookings_obj as $booking ) 
		{

			if( is_numeric( $booking->id_user ) && $booking->id_user > 0 ) {
				$userdata = get_userdata($booking->id_user);
			} else {
				$userdata = new stdClass();
				$userdata->display_name = $booking->name;
				$userdata->user_email = $booking->email;	
			}
			
			$opts = array();
			$opts['id_calendar'] = $booking->id_calendar;
			$dpProEventCalendar_class = $pec_init->init_base( $opts );

			$calendar_obj = $dpProEventCalendar_class->get_calendar();
			
			$booking_email_template_reminder_user = $calendar_obj->booking_email_template_reminder_user;
			if($booking_email_template_reminder_user == '') 
				$booking_email_template_reminder_user = "Hi #USERNAME#,\n\nWe would like to remind you the booking of the event:\n\n#EVENT_DETAILS#\n\nKind Regards.\n#SITE_NAME#";
			
			add_filter( 'wp_mail_from_name', 'dpProEventCalendar_wp_mail_from_name' );
			add_filter( 'wp_mail_from', 'dpProEventCalendar_wp_mail_from' );
			$headers = 'Content-Type: text/html; charset=UTF-8';

			// Email to User
			wp_mail( $userdata->user_email, get_bloginfo('name'), apply_filters('pec_booking_email', $booking_email_template_reminder_user, $booking->id_event, $userdata->display_name, $userdata->user_email, $booking_search_date, $booking->comment, $booking->quantity, $booking->phone, $booking->extra_fields), $headers );

			// Action hook
			do_action( 'pec_booking_reminder', $booking->id, $booking->id_event );

			$bookings_count++;
		
		}

	}
	
}


/**
 * On the scheduled action hook, run a function.
 */
function dpProEventCalendar_ical_sync( $calendar_id ) 
{
	
	global $wpdb;

	$querystr = "
		SELECT id as calendar_id, sync_ical_url, sync_fb_page, sync_ical_frequency, sync_ical_enable, sync_ical_category
		FROM " . DP_PRO_EVENT_CALENDAR_TABLE_CALENDARS . "
		WHERE id = %d";
	$calendar_obj = $wpdb->get_row( $wpdb->prepare( $querystr, $calendar_id ), OBJECT );
	
	if( empty( $calendar_obj ) ) return;

	$filename_ical = $calendar_obj->sync_ical_url;
	$fb_page = $calendar_obj->sync_fb_page;
	$category = $calendar_obj->sync_ical_category;

	if($category == 0)
		$category = '';

	$filename_ical_arr = explode(",", $filename_ical);
	
	foreach($filename_ical_arr as $url) {

		if($url != "") 
			dpProEventCalendar_importICS($calendar_id, $url, '', $category);
		
	}

	$fb_page_arr = explode(",", $fb_page);
	
	foreach($fb_page_arr as $url) {
		
		if($url != "") 
			dpProEventCalendar_importFB($calendar_id, $url, $category);
		
	}

}

function dpProEventCalendar_create_venue( $name, $address = '', $map = '', $lnlat = '', $link = '' ) 
{

	global $wpdb;

	$name = trim( stripslashes( $name ) );

	if( $name == "" )
		return '';

	$search_query = 'SELECT ID FROM '.$wpdb->posts.'
                         WHERE post_type = "pec-venues" 
                         AND post_status = "publish"
                         AND post_title = %s';

	$result = $wpdb->get_row( $wpdb->prepare( $search_query, str_replace( "&", "&amp;", $name ) ) );
	if(!is_array($result))
		$result = array();

	if(count($result) > 0) {
		return $result->ID;
	} else {
		$args = array( 
			'posts_per_page' => 1, 
			'post_type'=> DP_PRO_EVENT_CALENDAR_VENUES_POST_TYPE, 
			"meta_query" => array(
				array(
				   'key' => 'pec_venue_name_id',
				   'value' => $name
				)
			)
		);
		
		$result = get_posts( $args );

		if( ! empty( $result ) ) 
			return $result[0]->ID;
		
	}


	$venue_args = array(
	  'post_title'    => $name,
	  'post_status'   => 'publish',
	  'post_type'	  => DP_PRO_EVENT_CALENDAR_VENUES_POST_TYPE
	);

	if( ! is_user_logged_in() ) 
	{
	
		$users = get_users( 'role=administrator&number=1' ); 
		foreach ( $users as $user ) {
			$venue_args['post_author'] = $user->ID;
		}
	
	}

	$venue_id = wp_insert_post( $venue_args );

	
	update_post_meta( $venue_id, 'pec_venue_address', $address );

	update_post_meta( $venue_id, 'pec_venue_map_lnlat', $lnlat );
	
	update_post_meta( $venue_id, 'pec_venue_map', $map );

	update_post_meta( $venue_id, 'pec_venue_link', $link );

	update_post_meta( $venue_id, 'pec_venue_name_id', $name );

	return $venue_id;

}

function dpProEventCalendar_importFB( $calendar_id, $event_url, $category_ics = '', $event_option = 2, $offset = '' ) 
{

	global $dpProEventCalendar, $wpdb;

	
	$facebook = new PEC_Import_Events_Facebook();
	$event_data = array();
	

	$expire_after = $dpProEventCalendar['remove_expired_days'];
	if( $expire_after == '' || !is_numeric( $expire_after ) || $expire_after < 0 ) 
		$expire_after = 10;


	if( ! is_numeric( $event_url ) ) 
	{
		
		$event_url = str_replace("/?fref=nf", "", $event_url);
		$event_url = str_replace("/?ti=cl", "", $event_url);
		$event_url = str_replace("/timeline", "", $event_url);
		$event_url = substr($event_url, strrpos(rtrim($event_url, '/ '), '/') + 1);	

	}

	switch ( $event_option ) 
	{

		case 1:
			$event_data['import_by'] = 'facebook_event_id';
			break;
		
		case 2:
			$event_data['import_by'] = 'facebook_page';
			$event_data['page_username'] = $event_url;
			break;

	}

	$event_list = array();

	$event_data['event_ids'] = array( $event_url );
	$event_list = $facebook->import_events( $event_data );

	$events_imported = array();

	if( isset( $event_list->data ) ) 
	{

		$events_imported = $event_list->data;

	} else {

		if( !empty( $event_list ) ) 

			$events_imported[] = $event_list;

	}


	@set_time_limit(0);

	foreach( $events_imported as $graph_arr ) 
	{

		$extra_times = array();
		if(isset($graph_arr->event_times)) 
		{
			$event_times = $graph_arr->event_times;
			$event_times_arr = array();
			
			foreach($event_times as $key) {
				$event_times_arr[] = date('Y-m-d', strtotime($key['start_time']));
			}

			$extra_times = implode(',',$event_times_arr);
		}
		
		$picture = $graph_arr->cover;

		if( is_object( $picture ) ) 
			$picture = $picture->source;
		
		$ticket_uri = $graph_arr->ticket_uri;
		if( empty( $graph_arr->start_time ) ) continue;

		
		$args = array( 
			'posts_per_page' => 1, 
			'post_type'=> DP_PRO_EVENT_CALENDAR_EVENT_POST_TYPE, 
			"meta_query" => array(
				'relation' => 'AND',
				array(
				   'key' => 'pec_id_calendar',
				   'value' => $calendar_id,
				),
				array(
				   'key' => 'pec_fb_uid',
				   'value' => $graph_arr->id.'@pec-no-uid',
				)
			)
		);

		$imported_posts = get_posts( $args );

		$fb_event = array(
		  'post_title'    => $graph_arr->name,
		  'post_content'  => (isset($graph_arr->description) ? $graph_arr->description : ''),
		  'post_status'   => 'publish',
		  'post_category'  => array($category_ics),
		  'tax_input' 	  => array( 'pec_events_category' => $category_ics ),
		  'post_type'	  => DP_PRO_EVENT_CALENDAR_EVENT_POST_TYPE
		);

		// Set author if not logged in
		if( ! is_user_logged_in() ) 
		{
		
			$blogadmin = get_users( 'role=administrator' );

			$fb_event['post_author'] =  $blogadmin[0]->ID;
		
		}

		if( ! empty( $imported_posts ) ) 
		{
			$disable_sync = get_post_meta( $imported_posts[0]->ID, 'pec_disable_sync', true );
			if( $disable_sync ) 
				continue;

			$fb_event['ID'] = $imported_posts[0]->ID;
			$fb_event['post_author'] = $imported_posts[0]->post_author;
		}
		
		$tzid = "UTC";
		$setTimeZone = get_option( 'timezone_string' );

		if($setTimeZone == "") 
			$setTimeZone = timezone_name_from_abbr("", get_option('gmt_offset') * 3600, false);

		$start_date = new DateTime(date("Y-m-d H:i:s O", strtotime($graph_arr->start_time)), new DateTimeZone($tzid));
		
		$start_date->setTimeZone(new DateTimeZone($setTimeZone));
		
		$pec_date = $start_date->format('Y-m-d H:i:s');
		
		if( $offset != "" ) 
		{

			$end_date_hh = date('H', strtotime($offset.' hours', strtotime($end_date.' '.$end_date_hh.':'.$end_date_mm.':00')));
			$pec_date = date('Y-m-d H:i:s', strtotime($offset.' hours', strtotime($pec_date)));

		}

		$end_date = "";
		$end_date_hh = "";
		$end_date_mm = "";

		if( isset( $graph_arr->end_time ) ) 
		{
			$end_date = new DateTime(date("Y-m-d H:i:s O", strtotime($graph_arr->end_time)), new DateTimeZone($tzid));
			$end_date->setTimeZone(new DateTimeZone($setTimeZone));
			$end_date_hh = $end_date->format('H');
			$end_date_mm = $end_date->format('i');
			$end_date = $end_date->format('Y-m-d');
		}

		$location = $graph_arr->place->name;
		$venue_id = '';

		// Create Venue
		if( $location != "") 
		{

			$venue_address = "";
			$venue_map = "";

			if( isset($graph_arr->place->location->street)) {
				$venue_address = $graph_arr->place->location->street;
				$venue_map .= $venue_address;
			}
			
			if( isset($graph_arr->place->location->city)) 
				$venue_map .= ', '.$graph_arr->place->location->city;
			
			if( isset($graph_arr->place->location->country)) 
				$venue_map .= ', '.$graph_arr->place->location->country;

			$venue_lnlat = "";
			if( isset($graph_arr->place->location->latitude)) {
				$venue_lnlat .= $graph_arr->place->location->latitude;
				$venue_lnlat .= ', '.$graph_arr->place->location->longitude;
			}

			if( $venue_map == "") 
				$venue_map = $venue_address;

			if( $venue_map == "") 
				$venue_map = $location;

			$venue_id = dpProEventCalendar_create_venue( $location, $venue_address, $venue_map, $venue_lnlat );
		}

		$recurring_frecuency = '';
		
		if( $end_date != "" && substr( $pec_date, 0, 10 ) != $end_date )
			$recurring_frecuency = 1;

		if( isset( $dpProEventCalendar['remove_expired_enable'] ) && $dpProEventCalendar['remove_expired_enable'] && 
			(
				(
					$end_date != "" && 
					strtotime($end_date) < strtotime(current_time( 'Y-m-d' ) . ' -'.$expire_after.' days')
				) ||
				(
					$end_date == "" &&
					strtotime($pec_date) < strtotime(current_time( 'Y-m-d H:i:s' ) . ' -'.$expire_after.' days') &&
					$recurring_frecuency == ''
				)
			)
		) {
			continue;
		}

		$post_id = wp_insert_post( $fb_event, true );
		if( is_wp_error( $post_id ) ) 
			continue;

		if( $recurring_frecuency == 1 && $end_date != "" && date( "H", $pec_date ) > $end_date_hh )
			$recurring_frecuency = 0;

		if( ! empty( $extra_times ) ) 
		{
			$recurring_frecuency = 0;
			$end_date = '';
			update_post_meta( $post_id, 'pec_extra_dates', $extra_times );
		}

		wp_set_post_terms( $post_id, array( $category_ics ), 'pec_events_category' );

		$code = get_post_meta( $post_id, 'pec_code', true );
		if( ! isset( $code ) || $code == ""  )
		{

			$code = dpProeventCalendar_generate_code();
			update_post_meta( $post_id, 'pec_code', $code );

		}

		update_post_meta( $post_id, 'pec_id_calendar', $calendar_id );
		update_post_meta( $post_id, 'pec_date', $pec_date );
		update_post_meta( $post_id, 'pec_all_day', ($graph_arr->is_date_only ? '1' : '0') );
		update_post_meta( $post_id, 'pec_location', $venue_id );
		update_post_meta( $post_id, 'pec_end_date', $end_date );
		update_post_meta( $post_id, 'pec_link', $ticket_uri );
		update_post_meta( $post_id, 'pec_share', '' );
		update_post_meta( $post_id, 'pec_map', '' );
		update_post_meta( $post_id, 'pec_map_lnlat', '' );
		update_post_meta( $post_id, 'pec_recurring_frecuency', $recurring_frecuency );
		update_post_meta( $post_id, 'pec_end_time_hh', $end_date_hh );
		update_post_meta( $post_id, 'pec_end_time_mm', $end_date_mm );
		update_post_meta( $post_id, 'pec_hide_time', '' );
		update_post_meta( $post_id, 'pec_fb_event', 'https://www.facebook.com/events/'.$graph_arr->id );
		 
		update_post_meta( $post_id, 'pec_fb_uid', $graph_arr->id.'@pec-no-uid' );	
		update_post_meta( $post_id, 'pec_fb_uid_title', $graph_arr->name );

		if( $picture != "" && $picture != get_post_meta( $post_id, 'pec_fb_image', true ) )
			dpProEventCalendar_fetch_media( $picture, $post_id );
		
		update_post_meta( $post_id, 'pec_fb_image', $picture );	
		
	
	}
}

function dpProEventCalendar_importICS( $calendar_id, $filename, $tmp_filename = '', $category_ics = '', $offset = '' ) 
{

	global $dpProEventCalendar_cache, $dpProEventCalendar, $wpdb;

	$expire_after = $dpProEventCalendar['remove_expired_days'];
	if( $expire_after == '' || ! is_numeric( $expire_after ) || $expire_after < 0 )
		$expire_after = 10;

	if( $tmp_filename == "" )
		$tmp_filename = $filename;	
	
	$extension = strrchr( $tmp_filename, '.' ); 
	$extensions = array('.ics');
	//if(in_array($extension, $extensions)) {
		require_once( dirname(__FILE__) . '/includes/ical_parser.php' );

		$ical = new ICal($filename);
		$feed = $ical->cal;
		/*echo '<pre>';
		echo $filename;
			print_r($feed);
			echo '</pre>';
			die();*/
		if( ! empty( $feed ) ) 
		{

			set_time_limit(0);
			
			$all_uid = array();
			$count = 0;

			/*
			echo '<pre>';
			print_r($feed['VTIMEZONE']);
			print_r($feed['STANDARD']);
			echo '</pre>';*/

			if( is_array( $feed['VEVENT'] ) ) 
			{
			
				foreach( $feed['VEVENT'] as $key ) 
				{
					/*echo '<pre>';
					print_r($key);
					echo '</pre>';*/
					if( ! isset( $key['SUMMARY'] ) || $key['SUMMARY'] == "" ) 
					{
						$summary_arr = preg_grep( '/^SUMMARY/', array_keys($key) );
						$summary_arr = reset( $summary_arr );
						
						$key['SUMMARY'] = $key[$summary_arr];
						
					}
		
					if( $key['SUMMARY'] == "" ) continue;

					$count++;
					
					if(preg_match("/\p{Hebrew}/u", $key['DESCRIPTION']) || preg_match("/[ĄĆĘŁŃÓŚŹŻ]+/iu", $key['DESCRIPTION']) || preg_match("/\p{Cyrillic}/u", $key['DESCRIPTION'])) {
						$key['DESCRIPTION'] = str_replace("―", " - ", $key['DESCRIPTION']);
						$key['DESCRIPTION'] = str_replace("​", "", ltrim($key['DESCRIPTION']));
						

					} else {
						$key['DESCRIPTION'] = str_replace("―", " - ", $key['DESCRIPTION']);
						$key['DESCRIPTION'] = utf8_encode(utf8_decode(str_replace("​", "", ltrim($key['DESCRIPTION']))));
						
					}
					foreach($key as $k => $v) {
						$key[substr($k, 0, strpos($k, ';'))] = $v;	
					}

					//$key['UID'] = "";

					if($key['UID'] == "")
						$key['UID'] = $key['DTSTART'].$key['SUMMARY'].'@pec-no-uid';
					
					// XX 
					$args = array( 
						'posts_per_page' => 1, 
						'post_type'=> DP_PRO_EVENT_CALENDAR_EVENT_POST_TYPE, 
						"meta_query" => array(
							'relation' => 'AND',
							array(
							   'key' => 'pec_id_calendar',
							   'value' => $calendar_id,
							),
							array(
							   'key' => 'pec_ics_uid',
							   'value' => $key['UID'],
							)
						)
					);

					$imported_posts = get_posts( $args );
					
					// Create post object
					$ics_event = array(
					  'post_title'    => $key['SUMMARY'],
					  'post_content'  => $key['DESCRIPTION'],
					  'post_status'   => 'publish',
		  			  'post_category' => array($category_ics),
					  'tax_input' 	  => array( 'pec_events_category' => $category_ics ),
					  'post_type'	  => DP_PRO_EVENT_CALENDAR_EVENT_POST_TYPE
					);
					
					if( ! empty( $imported_posts ) ) 
					{
						
						$disable_sync = get_post_meta( $imported_posts[0]->ID, 'pec_disable_sync', true );
						if( $disable_sync ) continue;
						
						$ics_event['ID'] = $imported_posts[0]->ID;
					}

					$rrule_arr = "";
					if( $key['RRULE'] != "" ) 
					{

						$rrule = explode( ';', $key['RRULE'] );
						
						if( is_array( $rrule ) ) 
						{
							$rrule_arr = array();
							foreach( $rrule as $rule ) {
								$rrule_arr[substr($rule, 0, strpos($rule, '='))] = substr($rule, strrpos($rule, '=') + 1);
							}
						}
					}

					$tzid = "UTC";

					$setTimeZone = get_option( 'timezone_string' );
					if( $setTimeZone == "" ) 
						$setTimeZone = timezone_name_from_abbr( "", get_option('gmt_offset') * 3600, false );
					
					
					if( $feed['VTIMEZONE'][0]['TZID;X-RICAL-TZSOURCE=TZINFO'] != "" ) 
						$feed['VTIMEZONE'][0]['TZID'] = $feed['VTIMEZONE'][0]['TZID;X-RICAL-TZSOURCE=TZINFO'];
					

					if( $feed['VTIMEZONE'][0]['TZID'] != "" || $feed['VCALENDAR']['TZID'] != "" ) 
						$tzid = ($feed['VTIMEZONE'][0]['TZID'] != "" ? $feed['VTIMEZONE'][0]['TZID'] : $feed['VCALENDAR']['TZID']);
					
					if( isset($key['TZID']) && $key['TZID'] != "" ) 
						$tzid = $key['TZID'];
					
					
					if( isset( $feed['STANDARD'][0]['TZOFFSETTO'] ) ) 
					{
						$offset_to = substr_replace($feed['STANDARD'][0]['TZOFFSETTO'], ':', -2, 0);
						list( $hours, $minutes ) = explode( ':', $offset_to );
						$seconds = ( $hours * 60 * 60 ) + ( $minutes * 60 );
						// Get timezone name from seconds
						$tzid_tmp = timezone_name_from_abbr( '', $seconds, false );

						if( $tzid_tmp != "" )
							$tzid = $tzid_tmp;
					}

					if( $tzid == "Mountain Standard Time" ) 
						$tzid = "America/Denver";
					
					if( $tzid == "Eastern Standard Time" ) 
						$tzid = "America/New_York";
					
					if( $tzid == "Pacific Standard Time" ) 
						$tzid = "America/Los_Angeles";
					
					if( $tzid == "SE Asia Standard Time" ) 
						$tzid = "Asia/Vientiane";
					
					if( $tzid == "UTC+0" ) 
						$tzid = "UTC";
					
					
					$tzid = str_replace("America-", "America/", $tzid);
					$tzid = str_replace(";VALUE=DATE-TIME", "", $tzid);
					$tzid = str_replace(";VALUE=DATE", "", $tzid);

					$start_date = new DateTime($key['DTSTART'], new DateTimeZone($tzid));
					$start_date->setTimeZone(new DateTimeZone($setTimeZone));
					
					
					if( strlen($key['DTEND']) == 8 ) {
						$end_date = date("Y-m-d", strtotime($key['DTEND']));
						$end_date_hh = date("H", strtotime($key['DTEND']));
						$end_date_mm = date("i", strtotime($key['DTEND']));
					} else {
						$end_date = new DateTime($key['DTEND'], new DateTimeZone($tzid));
						$end_date->setTimeZone(new DateTimeZone($setTimeZone));
						$end_date_hh = $end_date->format('H');
						$end_date_mm = $end_date->format('i');
						$end_date = $end_date->format('Y-m-d');
						
					}

					$all_day = false;
					$start_date_formatted = "";
					$set_until = false;
					
					if( strlen( $key['DTSTART'] ) == 8 ) {
						$pec_date = date("Y-m-d", strtotime($key['DTSTART'])).' 00:00:00';
						
						$start_date_formatted = date("Y-m-d", strtotime($key['DTSTART']));
						$all_day = true;
						
						if(strlen($key['DTEND']) == 8) {
							$end_date = date("Y-m-d", strtotime($key['DTEND']) - 86400);
							if(strtotime($key['DTEND']) - strtotime($key['DTSTART']) <= 86400) {
								$end_date = '';
								$end_date_hh = '';
								$end_date_mm = '';
								$key['DTEND'] = '';
							}
						}
					} else {
						$pec_date = $start_date->format('Y-m-d H:i:s');
						
						if($offset != "")
							$pec_date = date('Y-m-d H:i:s', strtotime($offset.' hours', strtotime($pec_date)));

						$start_date_formatted = $start_date->format('Y-m-d');
					}

					$recurring_frecuency = '';
					$pec_daily_every = '';
					$pec_daily_working_days = '';
					$pec_weekly_every = '';
					$pec_weekly_day = '';
					$pec_monthly_every = '';
					$pec_monthly_position = '';
					$pec_monthly_day;

					if( is_array( $rrule_arr ) ) 
					{
						
						foreach($rrule_arr as $key2 => $value) 
						{
							
							if($key2 == 'FREQ') 
							{
								
								switch($value) {
									case 'DAILY':
										$recurring_frecuency = '1';
										break;
									case 'WEEKLY':
										$recurring_frecuency = '2';
										break;
									case 'MONTHLY':
										$recurring_frecuency = '3';
										break;
									case 'YEARLY':
										$recurring_frecuency = '4';
										break;
								}

							}
							
							if( $key2 == 'FREQ' && $value == 'DAILY' ) 
							{
								$pec_daily_every = $rrule_arr['INTERVAL'];
	
								$pec_daily_working_days = '';
							}
							
							if( $key2 == 'UNTIL' && $value != "" ) 
							{
								
								if( strlen( $value ) == 8 ) 
								{
									$end_date = date("Y-m-d", strtotime($value));
									//$end_date_hh = date("H", strtotime($value));
									//$end_date_mm = date("i", strtotime($value));
									
								} else {
									$end_date = new DateTime($value, new DateTimeZone($tzid));
									$end_date->setTimeZone(new DateTimeZone($setTimeZone));
									
									//$end_date_hh = $end_date->format('H');
									//$end_date_mm = $end_date->format('i');
									$end_date = $end_date->format('Y-m-d');
								}
								
								$set_until = true;
								
							}


							
							if( $key2 == 'COUNT' && $value != "" ) 
							{
								
								switch($recurring_frecuency) {
									case 1:
										$end_date = date("Y-m-d", strtotime("+".($value - 1)." days", strtotime($start_date_formatted)));
										break;
									case 2:
										$end_date = date("Y-m-d", strtotime("+".$value." weeks", strtotime($start_date_formatted)));
										break;
									case 3:
										$end_date = date("Y-m-d", strtotime("+".$value." months", strtotime($start_date_formatted)));
										break;
									case 4:
										$end_date = date("Y-m-d", strtotime("+".$value." years", strtotime($start_date_formatted)));
										break;	
								}
								
								$set_until = true;
							}
							
							if( $key2 == 'FREQ' && $value == 'WEEKLY' ) 
							{
								$day_arr = array();
								foreach( explode(',', $rrule_arr['BYDAY']) as $day ) 
								{
									switch( $day ) 
									{
										case 'MO':
											$day_arr[] = 1;
											break;
										case 'TU':
											$day_arr[] = 2;
											break;
										case 'WE':
											$day_arr[] = 3;
											break;
										case 'TH':
											$day_arr[] = 4;
											break;
										case 'FR':
											$day_arr[] = 5;
											break;
										case 'SA':
											$day_arr[] = 6;
											break;
										case 'SU':
											$day_arr[] = 7;
											break;
									}

									$pec_weekly_day = $day_arr;
								}

								$pec_weekly_every = $rrule_arr['INTERVAL'];
							}
							
							if( $key2 == 'FREQ' && $value == 'MONTHLY' ) 
							{
								
								$pec_monthly_every = $rrule_arr['INTERVAL']; 

								$setpos = "";
								switch( $rrule_arr['BYSETPOS'] ) 
								{
									case '1':
										$setpos = 'first';
										break;
									case '2':
										$setpos = 'second';
										break;
									case '3':
										$setpos = 'third';
										break;
	
									case '4':
										$setpos = 'fourth';
										break;
									case '-1':
										$setpos = 'last';
										break;
								}
								
								
								$day_arr = '';
								foreach( explode(',', $rrule_arr['BYDAY'] ) as $day ) {
									switch($day) {
										case 'MO':
											$day_arr = 'monday';
											break;
										case 'TU':
											$day_arr = 'tuesday';
											break;
										case 'WE':
											$day_arr = 'wednesday';
											break;
										case 'TH':
											$day_arr = 'thursday';
											break;
										case 'FR':
											$day_arr = 'friday';
											break;
										case 'SA':
											$day_arr = 'saturday';
											break;
										case 'SU':
											$day_arr = 'sunday';
											break;
										case '1MO':
											$day_arr = 'monday';
											$setpos = 'first';
											break;
										case '1TU':
											$day_arr = 'tuesday';
											$setpos = 'first';
											break;
										case '1WE':
											$day_arr = 'wednesday';
											$setpos = 'first';
											break;
										case '1TH':
											$day_arr = 'thursday';
											$setpos = 'first';
											break;
										case '1FR':
											$day_arr = 'friday';
											$setpos = 'first';
											break;
										case '1SA':
											$day_arr = 'saturday';
											$setpos = 'first';
											break;
										case '1SU':
											$day_arr = 'sunday';
											$setpos = 'first';
											break;
										case '2MO':
											$day_arr = 'monday';
											$setpos = 'second';
											break;
										case '2TU':
											$day_arr = 'tuesday';
											$setpos = 'second';
											break;
										case '2WE':
											$day_arr = 'wednesday';
											$setpos = 'second';
											break;
										case '2TH':
											$day_arr = 'thursday';
											$setpos = 'second';
											break;
										case '2FR':
											$day_arr = 'friday';
											$setpos = 'second';
											break;
										case '2SA':
											$day_arr = 'saturday';
											$setpos = 'second';
											break;
										case '2SU':
											$day_arr = 'sunday';
											$setpos = 'second';
											break;
										case '3MO':
											$day_arr = 'monday';
											$setpos = 'third';
											break;
										case '3TU':
											$day_arr = 'tuesday';
											$setpos = 'third';
											break;
										case '3WE':
											$day_arr = 'wednesday';
											$setpos = 'third';
											break;
										case '3TH':
											$day_arr = 'thursday';
											$setpos = 'third';
											break;
										case '3FR':
											$day_arr = 'friday';
											$setpos = 'third';
											break;
										case '3SA':
											$day_arr = 'saturday';
											$setpos = 'third';
											break;
										case '3SU':
											$day_arr = 'sunday';
											$setpos = 'third';
											break;
										case '4MO':
											$day_arr = 'monday';
											$setpos = 'fourth';
											break;
										case '4TU':
											$day_arr = 'tuesday';
											$setpos = 'fourth';
											break;
										case '4WE':
											$day_arr = 'wednesday';
											$setpos = 'fourth';
											break;
										case '4TH':
											$day_arr = 'thursday';
											$setpos = 'fourth';
											break;
										case '4FR':
											$day_arr = 'friday';
											$setpos = 'fourth';
											break;
										case '4SA':
											$day_arr = 'saturday';
											$setpos = 'fourth';
											break;
										case '4SU':
											$day_arr = 'sunday';
											$setpos = 'fourth';
											break;
									}
								}
								
								$pec_monthly_position = $setpos;
								
								$pec_monthly_day = $day_arr;
							}
						}
					} else if( $key['DTEND'] != "" && $end_date != $start_date_formatted && substr( $key['DTSTART'], 0, 10 ) != substr( $key['DTEND'], 0, 10 ) ) {
	
						$recurring_frecuency = 1;
					}

					if( is_array( $rrule_arr ) && ! $set_until )
						$end_date = "";

					if( $recurring_frecuency == '' || ( $end_date == $start_date_formatted && ! $set_until ) )
						$end_date = "";
					
					// Avoid expired events
					if( isset($dpProEventCalendar['remove_expired_enable']) && $dpProEventCalendar['remove_expired_enable'] && 
						(
							(
								$end_date != "" && 
								strtotime($end_date) < strtotime(current_time( 'Y-m-d' ) . ' -'.$expire_after.' days')
							) ||
							(
								$end_date == "" &&
								strtotime($pec_date) < strtotime(current_time( 'Y-m-d H:i:s' ) . ' -'.$expire_after.' days') &&
								$recurring_frecuency == ''
							)
						)
					)
						continue;

					// Insert the post into the database
					$post_id = wp_insert_post( $ics_event );
					
					wp_set_post_terms( $post_id, array( $category_ics ), 'pec_events_category' );

					if( $key['EXDATE'] != "" ) 
					{
						$exdate = wordwrap( $key['EXDATE'], 15, ",", true );
						$exdate_string = array();
						foreach( explode(',', $exdate) as $exception ) {
							$exdate_string[] = date( "Y-m-d", strtotime( $exception ) );
						}
						$exdate = implode( ',', $exdate_string );
						
						update_post_meta( $post_id, 'pec_exceptions', $exdate );	
						
					}

					update_post_meta( $post_id, 'pec_id_calendar', $calendar_id );

					if( $all_day )
						update_post_meta( $post_id, 'pec_all_day', 1 );
					else
						update_post_meta( $post_id, 'pec_all_day', 0 );

					update_post_meta( $post_id, 'pec_date', $pec_date );

					if( $offset != "" )  
						$end_date_hh = date( 'H', strtotime( $offset.' hours', strtotime( '1970-01-01 ' . $end_date_hh . ':' . $end_date_mm . ':00' ) ) );

					if( $recurring_frecuency == 1 && $end_date != "" && ! $all_day && date( "H", $pec_date ) > $end_date_hh ) 
						$recurring_frecuency = 0;

					update_post_meta( $post_id, 'pec_recurring_frecuency', $recurring_frecuency );

					
					$location = $key['LOCATION'];
					$venue_id = "";
					// Create Venue
					if( $location != "" )
						$venue_id = dpProEventCalendar_create_venue( $location, '', $location );

					$code = get_post_meta( $post_id, 'pec_code', true);
					if( ! isset( $code ) || $code == ""  )
					{

						$code = dpProeventCalendar_generate_code();
						update_post_meta( $post_id, 'pec_code', $code );

					}
					
					update_post_meta( $post_id, 'pec_daily_every', $pec_daily_every );
					update_post_meta( $post_id, 'pec_daily_working_days', $pec_daily_working_days );
					update_post_meta( $post_id, 'pec_weekly_every', $pec_weekly_every );
					update_post_meta( $post_id, 'pec_weekly_day', $pec_weekly_day );
					update_post_meta( $post_id, 'pec_monthly_every', $pec_monthly_every );
					update_post_meta( $post_id, 'pec_monthly_position', $pec_monthly_position );
					update_post_meta( $post_id, 'pec_monthly_day', $pec_monthly_day );

					update_post_meta( $post_id, 'pec_end_date', $end_date );
					update_post_meta( $post_id, 'pec_link', $key['URL'] );
					update_post_meta( $post_id, 'pec_share', '' );
					update_post_meta( $post_id, 'pec_end_time_hh', ($all_day ? '' : $end_date_hh) );
					update_post_meta( $post_id, 'pec_end_time_mm', ($all_day ? '' : $end_date_mm) );
					update_post_meta( $post_id, 'pec_hide_time', '' );
					update_post_meta( $post_id, 'pec_location', $venue_id );	
					update_post_meta( $post_id, 'pec_map', '' );	
					update_post_meta( $post_id, 'pec_map_lnlat', '' );	
					update_post_meta( $post_id, 'pec_ics_uid', $key['UID'] );		
					update_post_meta( $post_id, 'pec_ics_uid_title', $key['SUMMARY'] );
					update_post_meta( $post_id, 'pec_ics_filename', sha1( $filename ) );	

					$all_uid[] = $key['UID'];

				}
			}
			
			// Sync?
			
			if( filter_var( $filename, FILTER_VALIDATE_URL ) == $filename ) 
			{

				// Remove Not found events
				
				$args = array( 
					'posts_per_page' => -1, 
					'post_type'=> DP_PRO_EVENT_CALENDAR_EVENT_POST_TYPE, 
					"meta_query" => array(
						'relation' => 'AND',
						array(
						   'key' => 'pec_ics_filename',
						   'value' => sha1($filename)
						),
						array(
						   'key' => 'pec_ics_uid',
						   'value' => $all_uid,
						   'compare' => 'NOT IN'
						)
					)
				);
				
				$not_found_posts = get_posts( $args );
				
				foreach( $not_found_posts as $key ) {
					wp_delete_post( $key->ID );
				}
				
			}
			
			if( isset( $dpProEventCalendar_cache['calendar_id_'.$calendar_id] ) ) {
			   $dpProEventCalendar_cache['calendar_id_'.$calendar_id] = array();
			   update_option( 'dpProEventCalendar_cache', $dpProEventCalendar_cache );
		   }

		}
	//}	

}

if( ! function_exists( 'mb_substr' ) ) {
	function mb_substr( $string, $offset, $length, $encoding = '' ) 
	{
		$arr = preg_split( "//u", $string );
		$slice = array_slice( $arr, $offset + 1, $length );
	  	return implode( "", $slice );	
	}
}

function dpProEventCalendar_datediffInWeeks( $date1, $date2 )
{

    if( $date1 > $date2 ) return dpProEventCalendar_datediffInWeeks( $date2, $date1 );

	if( method_exists( 'DateTime','createFromFormat' ) ) 
	{
		$first = DateTime::createFromFormat( 'Y-m-d H:i:s', $date1 );
		$second = DateTime::createFromFormat( 'Y-m-d H:i:s', $date2 );
	} else {
		$first = dpProEventCalendar_create_from_format( 'Y-m-d H:i:s', $date1 );
		$second = dpProEventCalendar_create_from_format( 'Y-m-d H:i:s', $date2 );
	}

	if( ! is_object( $first ) || ! is_object( $second ) ) 
		return 1;
	
	return floor( ( $second->format( 'U' ) - $first->format( 'U' ) ) / ( 60*60*24 ) / 7 );

}

function dpProEventCalendar_get_date_diff( $time1, $time2, $precision = 2, $intervals = array(
                                                                            'year' => array('year', 'years'),
                                                                            'month' => array('month', 'months'),
                                                                            'day' => array('day', 'days'),
                                                                            'hour' => array('hour', 'hours'),
                                                                            'minute' => array('minute', 'minutes')
                                                                        ) )
{

	// If not numeric then convert timestamps
	if ( ! is_int( $time1 ) ) {
	    //$time1 = strtotime($time1);
	}
	if ( ! is_int( $time2 ) ) {
	    //$time2 = strtotime($time2);
	}
	
	// If time1 > time2 then swap the 2 values
	if ( $time1 > $time2 ) 
	    list( $time1, $time2 ) = array( $time2, $time1 );
	
	// Set up intervals and diffs arrays
	$diffs = array();

	foreach ( $intervals as $interval => $interval_label ) 
	{
	    // Create temp time from time1 and interval
	    $ttime = strtotime( '+1 ' . $interval, $time1 );
	    // Set initial values
	    $add = 1;
	    $looped = 0;
	    // Loop until temp time is smaller than time2
	    while ( $time2 >= $ttime ) 
	    {
	        // Create new temp time from time1 and interval
	        $add++;
	        $ttime = strtotime( "+" . $add . " " . $interval, $time1 );
	        $looped++;
	    }
	    $time1 = strtotime( "+" . $looped . " " . $interval, $time1 );
	    $diffs[$interval] = $looped;
	}

	$count = 0;
	$times = array();
	foreach ( $diffs as $interval => $value ) 
	{
	    // Break if we have needed precission
	    if ( $count >= $precision ) 
	        break;
	    
	    // Add value and interval if value is bigger than 0
	    if ( $value > 0 ) 
	    {
	        // Add value and interval to times array
	        $times[] = $value . " " . $intervals[$interval][$value == 1 ? 0 : 1];
	        $count++;
	    }
	}
	// Return string with times
	return implode( ", ", $times );
}

function dpProEventCalendar_create_from_format( $dformat, $dvalue )
{

	$ymd = sprintf(
		// This is a format string that takes six total decimal
		// arguments, then left-pads them with zeros to either
		// 4 or 2 characters, as needed
		'%04d-%02d-%02d %02d:%02d:%02d',
		date('Y', strtotime($dvalue)),  // This will be "111", so we need to add 1900.
		date('m', strtotime($dvalue)),      // This will be the month minus one, so we add one.
		date('d', strtotime($dvalue)), 
		date('H', strtotime($dvalue)), 
		date('i', strtotime($dvalue)), 
		date('s', strtotime($dvalue))
	);
	
	$new_schedule = new DateTime( $ymd );
	
	return $new_schedule;

}

add_filter( 'the_excerpt_rss', 'dpProEventCalendar_rss_feed' );
add_filter( 'the_content_feed', 'dpProEventCalendar_rss_feed' );

function dpProEventCalendar_rss_feed( $content )
{

	global $wp_query;
 
    $post_id = $wp_query->post->ID;
    $post_type = get_post_type( $post_id );
 
    if( $post_type == DP_PRO_EVENT_CALENDAR_EVENT_POST_TYPE ) 
    {

		$post_thumbnail_id = get_post_thumbnail_id( $post_id );
		$image_attributes = wp_get_attachment_image_src( $post_thumbnail_id, 'large' );
			
		$content = '<img src="'.$image_attributes[0].'" alt="" />' . $content;

	}

	return $content;

}

function dpProEventCalendar_fetch_media( $file_url, $post_id ) 
{

	require_once( ABSPATH . 'wp-admin/includes/image.php' );
	global $wpdb;

	if( ! $post_id )
		return false;

	//directory to import to	
	$artDir = '/importedmedia/';

	$uploads = wp_upload_dir();
	$upload_basedir = $uploads['basedir'];
	$upload_baseurl = $uploads['baseurl'];


	//if the directory doesn't exist, create it	
	if( !file_exists( $upload_basedir.$artDir ) )
		mkdir( $upload_basedir . $artDir );

	//rename the file... alternatively, you could explode on "/" and keep the original file name
	$file_url_tmp = strtok( $file_url, '?' );
	$ext = preg_replace( '/\?.*/', '', array_pop(explode(".", $file_url_tmp)) );

	if( $ext != "jpg" && $ext != "png" && $ext != "gif" ) 
		return false;

	$new_filename = 'event-' . $post_id . "." . $ext; //if your post has multiple files, you may need to add a random number to the file name to prevent overwrites

	$opts = array(
	    "ssl"=>array(
	        "cafile" => dirname(__FILE__)."/includes/Facebook/fb_ca_chain_bundle.crt",
	        "verify_peer"=> true,
	        "verify_peer_name"=> true,
	    ),
	);

	$context = stream_context_create( $opts );

	if( ini_get('allow_url_fopen') ) {
		if(!copy($file_url, $upload_basedir.$artDir.$new_filename, $context))
		{
			/*$errors= error_get_last();
			echo "COPY ERROR: ".$errors['type'];
			echo "<br />\n".$errors['message'];*/

		} else {
			//echo "File copied from remote!";
		}
	} else {
		//echo 'CURL';
		$ch = curl_init();
		$fp = fopen ( $upload_basedir . $artDir . $new_filename, 'w+' );
		$ch = curl_init( $file_url );
		curl_setopt( $ch, CURLOPT_TIMEOUT, 50 );
		curl_setopt( $ch, CURLOPT_FILE, $fp );
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1 );
		curl_setopt( $ch, CURLOPT_ENCODING, "" );
		curl_exec( $ch );
		curl_close( $ch );
		fclose( $fp );
	}

	$siteurl = get_option( 'siteurl' );
	$file_info = getimagesize( $upload_basedir . $artDir . $new_filename );

	//create an array of attachment data to insert into wp_posts table
	$artdata = array();
	$artdata = array(
		'post_date' => current_time('mysql'),
		'post_date_gmt' => current_time('mysql'),
		'post_title' => $new_filename, 
		'post_status' => 'inherit',
		'comment_status' => 'closed',
		'ping_status' => 'closed',
		'post_name' => sanitize_title_with_dashes(str_replace("_", "-", $new_filename)),											
		'post_modified' => current_time('mysql'),
		'post_modified_gmt' => current_time('mysql'),
		'post_parent' => $post_id,
		'post_type' => 'attachment',
		'guid' => $upload_baseurl.$artDir.$new_filename,
		'post_mime_type' => $file_info['mime'],
		'post_excerpt' => '',
		'post_content' => ''
	);

	$save_path = $uploads['basedir'] . '/importedmedia/' . $new_filename;

	//insert the database record
	$attach_id = wp_insert_attachment( $artdata, $save_path, $post_id );

	//generate metadata and thumbnails
	if( function_exists( "wp_generate_attachment_metadata" ) ) {
		if ( $attach_data = wp_generate_attachment_metadata( $attach_id, $save_path ) ) 
			wp_update_attachment_metadata( $attach_id, $attach_data );
		
	}
	
	//optional make it the featured image of the post it's attached to
	$rows_affected = $wpdb->insert( $wpdb->prefix . 'postmeta', array( 'post_id' => $post_id, 'meta_key' => '_thumbnail_id', 'meta_value' => $attach_id ) );

	return true;

}


function dpProEventCalendar_date_i18n( $format, $timestamp = "", $no_default = false ) 
{

	if( $timestamp == "" ) 
	{

		if($no_default)
			return '';
	
		$timestamp = time();	
	
	}

	$i18n = date( $format, $timestamp );
	
	$i18n = str_replace( "January", __('January', 'dpProEventCalendar' ), $i18n );
	$i18n = str_replace( "February", __('February', 'dpProEventCalendar' ), $i18n );
	$i18n = str_replace( "March", __('March', 'dpProEventCalendar' ), $i18n );
	$i18n = str_replace( "April", __('April', 'dpProEventCalendar' ), $i18n );
	$i18n = str_replace( "May", __('May', 'dpProEventCalendar' ), $i18n );
	$i18n = str_replace( "June", __('June', 'dpProEventCalendar' ), $i18n );
	$i18n = str_replace( "July", __('July', 'dpProEventCalendar' ), $i18n );
	$i18n = str_replace( "August", __('August', 'dpProEventCalendar' ), $i18n );
	$i18n = str_replace( "September", __('September', 'dpProEventCalendar' ), $i18n );
	$i18n = str_replace( "October", __('October', 'dpProEventCalendar' ), $i18n );
	$i18n = str_replace( "November", __('November', 'dpProEventCalendar' ), $i18n );
	$i18n = str_replace( "December", __('December', 'dpProEventCalendar' ), $i18n );
	
	return $i18n;

}

function dpProEventCalendar_archive_template( $archive_template ) 
{

     global $post, $dpProEventCalendar;

     if ( is_post_type_archive ( DP_PRO_EVENT_CALENDAR_EVENT_POST_TYPE ) ) 
     {

		  if($dpProEventCalendar['redirect_archive'] != "") {
	
			  wp_redirect($dpProEventCalendar['redirect_archive']);
	
			  die();
	
		  }
    
     }
    
     return $archive_template;

}

add_filter( 'archive_template', 'dpProEventCalendar_archive_template' ) ;


function dpProEventCalendar_get_permalink($id) 
{

	if( function_exists( 'icl_object_id' ) ) 

		$id = icl_object_id($id, get_post_type($id), true);

	$use_link = get_post_meta( $id, 'pec_use_link', true );
	$link = get_post_meta( $id, 'pec_link', true );

	if( $use_link && $link != "" ) 
	{

		if( substr( $link, 0, 4 ) != "http" && substr( $link, 0, 4 ) != "mail" ) 
		
			$link = 'https://'.trim( $link );
		
		return trim( $link );

	} else {

		return get_permalink( $id );

	}

}


function dpProEventCalendar_getEventTimezone( $event_id, $parse_offset = false ) 
{

	$event_timezone = get_post_meta( $event_id, 'pec_timezone', true );
		
	if( $event_timezone == "" ) 
	{

		$current_offset = get_option('gmt_offset');
		$tzstring = get_option('timezone_string');

		if ( empty($tzstring) ) 
		{ // Create a UTC+- zone if no timezone string exists
		
			$check_zone_info = false;
		
			if ( 0 == $current_offset )
				$tzstring = 'UTC+0';
			elseif ($current_offset < 0)
				$tzstring = 'UTC' . $current_offset;
			else
				$tzstring = 'UTC+' . $current_offset;
		
		}

		$event_timezone = $tzstring;
	
	}

	if($parse_offset)
	
		$event_timezone = dpProEventCalendar_parseOffset( $event_timezone );

	$event_timezone = str_replace(array('America/', 'Europe/', 'Africa/', 'Asia/', 'Antarctica/'), '', $event_timezone);
	
	$event_timezone = str_replace('/', ' / ', $event_timezone);
	$event_timezone = str_replace('_', ' ', $event_timezone);

	return $event_timezone;

}

function dpProEventCalendar_parseOffset ( $offset ) 
{

	if( strpos( $offset, "UTC" ) === false ) 
	{

		$dateTimeZone = new DateTimeZone($offset);

		$dateTime = new DateTime("now");
		//xxx
		$parsed = $dateTimeZone->getOffset($dateTime);

		if( is_numeric( $parsed ) ) 
		{
			$negative = false;

			if( strpos( $parsed, "-" ) !== false ) {
				$parsed = str_replace('-', '', $parsed);
				$negative = true;
			}

			$parsed = $parsed / 3600;

			$parsed = (double)$parsed * 60;

			if( $negative )
				$parsed = "+".$parsed;
			else
				$parsed = "-".$parsed;


		} else
			$parsed = 0;
		

	} else {

		$parsed = str_replace( array( 'UTC+', 'UTC-' ), '', $offset );

		$parsed = (double)$parsed * 60;

		if( strpos( $offset, "UTC-" ) !== false ) 
			$parsed = "+".$parsed;
		else
			$parsed = "-".$parsed;
		

	}

	return $parsed;
}

function dpProEventCalendar_is_ampm() 
{

	if( strpos( get_option('time_format'), "A")  !== false || strpos(get_option( 'time_format' ), "a" )  !== false ) 
		return true;
	else
		return false;

}

function dpProEventCalendar_removeExpiredEvents() 
{

	global $dpProEventCalendar, $dpProEventCalendar_cache;

	$post_status = $dpProEventCalendar['remove_expired_status'];
	if( $post_status == '' ) 
		$post_status = 'publish';

	$expire_after = $dpProEventCalendar['remove_expired_days'];
	if( $expire_after == '' || !is_numeric($expire_after) || $expire_after < 0 ) 
		$expire_after = 10;

	$force_removal = false;
	if( $dpProEventCalendar['remove_expired_completly'] == 'remove' ) 
		$force_removal = true;

	$expired_events = array();

	// Get Events which end date is set and it is in the past
	$args = array( 
		'posts_per_page' => -1, 
		'post_type'		 => DP_PRO_EVENT_CALENDAR_EVENT_POST_TYPE, 
		'post_status'    => $post_status,
		"meta_query" 	 => 
			array(
				array(
					'relation' => 'AND',
					array(
						'key'     => 'pec_end_date',
						'value'   => '',
						'compare' => '!='
					),
					array(
						'key'     => 'pec_end_date',
						'value'   => date( 'Y-m-d', strtotime( current_time( 'Y-m-d' ) . ' -'.$expire_after.' days' ) ),
						'compare' => '<',
						'type'    => 'DATETIME'
					)
				)
			)
		);
	
	$expired_events = array_merge( get_posts( $args ), $expired_events );

	// Get events that end date is not set and the date is in the past and is not recurrent
	$args = array( 
		'posts_per_page' => -1, 
		'post_type'		 => DP_PRO_EVENT_CALENDAR_EVENT_POST_TYPE, 
		'post_status'    => $post_status,
		"meta_query" 	 => 
			array(
				array(
					'relation' => 'AND',
					array(
						'key'     => 'pec_end_date',
						'value'   => '',
						'compare' => '='
					),
					array(
						'key'     => 'pec_date',
						'value'   => date('Y-m-d H:i:s', strtotime(current_time( 'Y-m-d H:i:s' ) . ' -'.$expire_after.' days')),
						'compare' => '<',
						'type'    => 'DATETIME'
					),
					array(
						'relation' => 'OR',
						array(
							'key'     => 'pec_recurring_frecuency',
							'value'   => '0',
							'compare' => '='
						),
						array(
							'key'     => 'pec_recurring_frecuency',
							'value'   => '',
							'compare' => '='
						)
					)
				)
			)
		);
	
	$expired_events = array_merge( get_posts( $args ), $expired_events );

	if( is_array( $expired_events ) ) 
	{
	
		foreach( $expired_events as $key ) {

			wp_delete_post( $key->ID, $force_removal );
			
		}

		if( isset( $dpProEventCalendar_cache ) ) {
		   $dpProEventCalendar_cache = array();
		   update_option( 'dpProEventCalendar_cache', $dpProEventCalendar_cache );
	   	}
	}

}

/**
 * Get the value of the Rest field
 *
 * @param array $object Details of current post.
 * @param string $field_name Name of field.
 * @param WP_REST_Request $request Current request
 *
 * @return mixed
 */
function dpProevEntCalendar_get_field_rest( $object, $field_name, $request ) 
{

	if($field_name == "pec_venue_map_lnlat" && $object['type'] == DP_PRO_EVENT_CALENDAR_EVENT_POST_TYPE) 
	{
	
		$location_id = get_post_meta( $object[ 'id' ], 'pec_location', true );
		if(is_numeric($location_id)) 
			return get_post_meta( $location_id, $field_name, true );
		
	}
    return get_post_meta( $object[ 'id' ], $field_name, true );

}

function dpProevEntCalendar_get_image_rest( $object, $field_name, $request ) 
{

	$post_thumbnail_id = get_post_thumbnail_id( $object[ 'id' ] );
	$image_attributes = wp_get_attachment_image_src( $post_thumbnail_id, 'large' );

	if(!empty($image_attributes))
	    return $image_attributes[0];
	else
		return '';

}

function dpProEventCalendar_tz_offset_to_name( $offset )
{

    $offset *= 3600; // convert hour offset to seconds
    $abbrarray = timezone_abbreviations_list();
    foreach ($abbrarray as $abbr)
    {
        foreach ($abbr as $city)
        {
            if ($city['offset'] == $offset)
                return $city['timezone_id'];
    
        }
    }

    return false;

}

function dpProEventCalendar_mailchimp_curl_connect( $url, $request_type, $api_key, $data = array() ) 
{

	if( $request_type == 'GET' )
		$url .= '?' . http_build_query($data);
 
	$mch = curl_init();
	$headers = array(
		'Content-Type: application/json',
		'Authorization: Basic '.base64_encode( 'user:'. $api_key )
	);
	curl_setopt($mch, CURLOPT_URL, $url );
	curl_setopt($mch, CURLOPT_HTTPHEADER, $headers);
	//curl_setopt($mch, CURLOPT_USERAGENT, 'PHP-MCAPI/2.0');
	curl_setopt($mch, CURLOPT_RETURNTRANSFER, true); // do not echo the result, write it into variable
	curl_setopt($mch, CURLOPT_CUSTOMREQUEST, $request_type); // according to MailChimp API: POST/GET/PATCH/PUT/DELETE
	curl_setopt($mch, CURLOPT_TIMEOUT, 10);
	curl_setopt($mch, CURLOPT_SSL_VERIFYPEER, false); // certificate verification for TLS/SSL connection
 
	if( $request_type != 'GET' ) 
	{
		curl_setopt($mch, CURLOPT_POST, true);
		curl_setopt($mch, CURLOPT_POSTFIELDS, json_encode($data) ); // send data in json
	}
 
	return curl_exec($mch);

}

add_action( 'admin_post_dpProEventCalendar_facebook_authorize_action', 'dpProEventCalendar_facebook_authorize_user' );
add_action( 'admin_post_dpProEventCalendar_facebook_authorize_callback', 'dpProEventCalendar_facebook_authorize_user_callback' );

function dpProEventCalendar_facebook_authorize_user () 
{

	global $dpProEventCalendar;

	//if ( ! empty( $_POST ) && isset( $_POST['dpProEventCalendar_facebook_authorize_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['dpProEventCalendar_facebook_authorize_nonce'] ) ), 'dpProEventCalendar_facebook_authorize_action' ) ) { // input var okay.

	$dpProEventCalendar_options       = $dpProEventCalendar;
	$app_id            = isset( $dpProEventCalendar_options['facebook_app_id'] ) ? $dpProEventCalendar_options['facebook_app_id'] : '';
	$app_secret        = isset( $dpProEventCalendar_options['facebook_app_secret'] ) ? $dpProEventCalendar_options['facebook_app_secret'] : '';
	$redirect_url      = admin_url( 'admin-post.php?action=dpProEventCalendar_facebook_authorize_callback' );
	$api_version       = 'v3.0';
	$param_url         = rawurlencode( $redirect_url );
	$dpProEventCalendar_session_state = md5( uniqid( rand(), true ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.rand_rand
	setcookie( 'dpProEventCalendar_session_state', $dpProEventCalendar_session_state, '0', '/' );

	if ( ! empty( $app_id ) && ! empty( $app_secret ) ) {

		$dialog_url = 'https://www.facebook.com/' . $api_version . '/dialog/oauth?client_id='
				. $app_id . '&redirect_uri=' . $param_url . '&state='
				. $dpProEventCalendar_session_state . '&scope=groups_access_member_info,user_events,pages_show_list';
				//die('action ok'.$dialog_url);
		header( 'Location: ' . $dialog_url );

	} else {
		die( esc_attr__( 'Please insert Facebook App ID and Secret.', 'dpProEventCalendar' ) );
	}
	//} else {
	//	die( esc_attr__( 'You have not access to do this operation.', 'dpProEventCalendar' ) );
	//}
}


function dpProEventCalendar_facebook_authorize_user_callback () 
{

	global $dpProEventCalendar;
	
	if ( isset( $_COOKIE['dpProEventCalendar_session_state'] ) && isset( $_REQUEST['state'] ) && ( sanitize_text_field( wp_unslash( $_REQUEST['state'] ) ) === $_COOKIE['dpProEventCalendar_session_state'] ) ) { // input var okay.
			// phpcs:ignore WordPress.Security.NonceVerification
			$code         = isset( $_GET['code'] ) ? sanitize_text_field( wp_unslash( $_GET['code'] ) ) : ''; // input var okay.
			$dpProEventCalendar_options  = $dpProEventCalendar;
			$app_id       = isset( $dpProEventCalendar_options['facebook_app_id'] ) ? $dpProEventCalendar_options['facebook_app_id'] : '';
			$app_secret   = isset( $dpProEventCalendar_options['facebook_app_secret'] ) ? $dpProEventCalendar_options['facebook_app_secret'] : '';
			$redirect_url = admin_url( 'admin-post.php?action=dpProEventCalendar_facebook_authorize_callback' );
			$api_version  = 'v3.0';
			$param_url    = rawurlencode( $redirect_url );

		if ( ! empty( $app_id ) && ! empty( $app_secret ) ) {

			$token_url = 'https://graph.facebook.com/' . $api_version . '/oauth/access_token?'
			. 'client_id=' . $app_id . '&redirect_uri=' . $param_url
			. '&client_secret=' . $app_secret . '&code=' . $code;

			$access_token           = '';
			$dpProEventCalendar_user_token_options = array();
			$dpProEventCalendar_fb_authorize_user  = array();
			$response               = wp_remote_get( $token_url );
			$body                   = wp_remote_retrieve_body( $response );
			$body_response          = json_decode( $body );

			if ( ! empty( $body ) && isset( $body_response->access_token ) ) {

				$access_token                               = $body_response->access_token;
				$dpProEventCalendar_user_token_options['authorize_status'] = 1;
				$dpProEventCalendar_user_token_options['access_token']     = sanitize_text_field( $access_token );
				update_option( 'dpProEventCalendar_user_token_options', $dpProEventCalendar_user_token_options );

				$profile_call = wp_remote_get( 'https://graph.facebook.com/' . $api_version . "/me?fields=id,name,picture&access_token=$access_token" );
				$profile      = wp_remote_retrieve_body( $profile_call );
				$profile      = json_decode( $profile );
				if ( isset( $profile->id ) && isset( $profile->name ) ) {
					$dpProEventCalendar_fb_authorize_user['ID']   = sanitize_text_field( $profile->id );
					$dpProEventCalendar_fb_authorize_user['name'] = sanitize_text_field( $profile->name );
					if ( isset( $profile->picture->data->url ) ) {
						$dpProEventCalendar_fb_authorize_user['avtar'] = esc_url_raw( $profile->picture->data->url );
					}
				}
				
				update_option( 'dpProEventCalendar_fb_authorize_user', $dpProEventCalendar_fb_authorize_user );

				$args          = array( 'timeout' => 15 );
				$accounts_call = wp_remote_get( 'https://graph.facebook.com/' . $api_version . "/me/accounts?access_token=$access_token&limit=100&offset=0", $args );
				$accounts      = wp_remote_retrieve_body( $accounts_call );
				$accounts      = json_decode( $accounts );
				$accounts      = isset( $accounts->data ) ? $accounts->data : array();
				if ( ! empty( $accounts ) ) {
					$pages = array();
					foreach ( $accounts as $account ) {
						$pages[ $account->id ] = array(
							'id'           => $account->id,
							'name'         => $account->name,
							'access_token' => $account->access_token,
						);
					}
					update_option( 'dpProEventCalendar_fb_user_pages', $pages );
				}

				$redirect_url = admin_url( 'admin.php?page=dpProEventCalendar-settings&authorize=1' );
				wp_safe_redirect( $redirect_url );
				exit();
			} else {
				$redirect_url = admin_url( 'admin.php?page=dpProEventCalendar-settings&authorize=0' );
				wp_safe_redirect( $redirect_url );
				exit();
			}
		} else {
			$redirect_url = admin_url( 'admin.php?page=dpProEventCalendar-settings&authorize=2' );
			wp_safe_redirect( $redirect_url );
			exit();
		}
	} else {
		die( esc_attr__( 'You have not access to do this operation.', 'dpProEventCalendar' ) );
	}
}

function dpProEventCalendar_has_authorized_user_token () 
{

	$dpProEventCalendar_user_token_options = get_option( 'dpProEventCalendar_user_token_options', array() );
	
	if ( ! empty( $dpProEventCalendar_user_token_options ) ) 
	{
	
		$authorize_status = isset( $dpProEventCalendar_user_token_options['authorize_status'] ) ? $dpProEventCalendar_user_token_options['authorize_status'] : 0;
		$access_token     = isset( $dpProEventCalendar_user_token_options['access_token'] ) ? $dpProEventCalendar_user_token_options['access_token'] : '';

		if ( 1 === $authorize_status && ! empty( $access_token ) ) 
			return true;
	
	}
	
	return false;

}

function dpProEventCalendar_get_user_role () 
{

	global $current_user;

	$user_roles = $current_user->roles;
	$user_role = array_shift($user_roles);

	return $user_role;

}

function dpProeventCalendar_generate_code( $length = 11 ) 
{

    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
 
    $password = '';
    for ( $i = 0; $i < $length; $i++ ) 
    {
    
        $password .= substr( $chars, wp_rand( 0, strlen( $chars ) - 1 ), 1 );
	
	}

	return $password;

}

function dpProEventCalendar_getBookingInfo( $booking_id ) 
{

	global $wpdb;

    $querystr = "
    SELECT *
    FROM " . DP_PRO_EVENT_CALENDAR_TABLE_BOOKING . "
	WHERE id = %d";

    $booking = $wpdb->get_row( $wpdb->prepare( $querystr, $booking_id ), OBJECT);

    return $booking;
	
}

?>