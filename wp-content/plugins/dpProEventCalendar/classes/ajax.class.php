<?php

/*
 * DP Pro Event Calendar
 *
 * Copyright 2012, Diego Pereyra
 *
 * @Web: https://www.wpsleek.com
 * @Email: dpereyra90@gmail.com
 *
 * Init Class
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class DpProEventCalendar_Ajax {

	private $timestamp;
	private $calendar;
	private $category;
	private $columns;
	private $calendar_per_date;
	private $location;
	private $speaker;
	private $event_id;
	private $author;
	private $type;
	private $include_all_events;
	private $modal;
	private $hide_old_dates;
	private $is_admin;

	private $opts = array();

	/**
	 * Constructor - Load Ajax Hooks
	 * 
	 * @return void
	 */
	function __construct(  ) 
	{

		$this->load_hooks();

	}

	/**
	 * Init action hooks for ajax calls
	 * 
	 * @return void
	 */
	function load_hooks()
	{

		// Get Date
		add_action( 'wp_ajax_nopriv_getDate', array( $this, 'get_date' ) );
		add_action( 'wp_ajax_getDate', array( $this, 'get_date' ) );

		// Get Year
		add_action( 'wp_ajax_nopriv_getYear', array( $this, 'get_year' ) );
		add_action( 'wp_ajax_getYear', array( $this, 'get_year' ) );

		// Get Daily
		add_action( 'wp_ajax_nopriv_getDaily', array( $this, 'get_daily' ) );
		add_action( 'wp_ajax_getDaily', array( $this, 'get_daily' ) );

		// Get Weekly
		add_action( 'wp_ajax_nopriv_getWeekly', array( $this, 'get_weekly' ) );
		add_action( 'wp_ajax_getWeekly', array( $this, 'get_weekly' ) );

		// Get Modal
		add_action( 'wp_ajax_nopriv_getEventModal', array( $this, 'get_event_modal' ) );
		add_action( 'wp_ajax_getEventModal', array( $this, 'get_event_modal' ) );

		// Get Events
		add_action( 'wp_ajax_nopriv_getEvents', array( $this, 'get_events' ) );
		add_action( 'wp_ajax_getEvents', array( $this, 'get_events' ) );

		// Get Event
		add_action( 'wp_ajax_nopriv_getEvent', array( $this, 'get_event' ) );
		add_action( 'wp_ajax_getEvent', array( $this, 'get_event' ) );

		// Get Events Month
		add_action( 'wp_ajax_nopriv_getEventsMonth', array( $this, 'get_events_month' ) );
		add_action( 'wp_ajax_getEventsMonth', array( $this, 'get_events_month' ) );

		// Get Events Month List
		add_action( 'wp_ajax_nopriv_getEventsMonthList', array( $this, 'get_events_month_list' ) );
		add_action( 'wp_ajax_getEventsMonthList', array( $this, 'get_events_month_list' ) );

		// Submit Event
		add_action( 'wp_ajax_nopriv_submitEvent', array( $this, 'submit_event' ) );
		add_action( 'wp_ajax_submitEvent', array( $this, 'submit_event' ) );

		// New Subscriber
		add_action( 'wp_ajax_nopriv_ProEventCalendar_NewSubscriber', array( $this, 'new_subscriber' ) );
		add_action( 'wp_ajax_ProEventCalendar_NewSubscriber', array( $this, 'new_subscriber' ) );

		// Rate Event
		add_action( 'wp_ajax_nopriv_ProEventCalendar_RateEvent', array( $this, 'rate_event' ) );
		add_action( 'wp_ajax_ProEventCalendar_RateEvent', array( $this, 'rate_event' ) );

		// Cancel Booking
		add_action( 'wp_ajax_nopriv_cancelBooking', array( $this, 'cancel_booking' ) );
		add_action( 'wp_ajax_cancelBooking', array( $this, 'cancel_booking' ) );

		// Remove Event
		add_action( 'wp_ajax_removeEvent', array( $this, 'remove_event' ) );

		// Cancel Tour
		add_action( 'wp_ajax_cancelTour', array( $this, 'cancel_tour' ) );

		// Get Search Results
		add_action( 'wp_ajax_nopriv_getSearchResults', array( $this, 'get_search_results' ) );
		add_action( 'wp_ajax_getSearchResults', array( $this, 'get_search_results' ) );

		// Book Event
		add_action( 'wp_ajax_nopriv_bookEvent', array( $this, 'book_event' ) );
		add_action( 'wp_ajax_bookEvent', array( $this, 'book_event' ) );

		// Remove Booking
		add_action( 'wp_ajax_removeBooking', array( $this, 'remove_booking' ) );

		// Book Event Form
		add_action( 'wp_ajax_nopriv_getBookEventForm', array( $this, 'book_event_form' ) );
		add_action( 'wp_ajax_getBookEventForm', array( $this, 'book_event_form' ) );

		// Edit Event Form
		add_action( 'wp_ajax_nopriv_getEditEventForm', array( $this, 'edit_event_form' ) );
		add_action( 'wp_ajax_getEditEventForm', array( $this, 'edit_event_form' ) );

		// New Event Form
		add_action( 'wp_ajax_nopriv_getNewEventForm', array( $this, 'new_event_form' ) );
		add_action( 'wp_ajax_getNewEventForm', array( $this, 'new_event_form' ) );

		// Set Special Dates
		add_action( 'wp_ajax_setSpecialDates', array( $this, 'set_special_dates' ) );

		// Get Mailchimp Lists
		add_action( 'wp_ajax_pec_get_mclist', array( $this, 'pec_get_mclist' ) );

		// Book Event from admin
		add_action( 'wp_ajax_bookEventAdmin', array( $this, 'book_event_admin' ) );

		// Get More bookings
		add_action( 'wp_ajax_getMoreBookings', array( $this, 'get_more_bookings' ) );

	}

	function ajax_save_post ()
	{
		
		global $pec_init;

		$this->timestamp = $pec_init->post( 'date' );
		$this->calendar = $pec_init->post( 'calendar' );
		$this->category = $pec_init->post( 'category' );
		$this->columns = $pec_init->post( 'columns' );
		$this->calendar_per_date = $pec_init->post( 'calendar_per_date' );
		$this->location = $pec_init->post( 'location' );
		$this->speaker = $pec_init->post( 'speaker' );
		$this->event_id = $pec_init->post( 'event_id' );
		$this->author = $pec_init->post( 'author' );
		$this->type = $pec_init->post( 'type' );
		$this->include_all_events = $pec_init->post( 'include_all_events' );
		$this->modal = $pec_init->post( 'modal' );
		$this->hide_old_dates = $pec_init->post( 'hide_old_dates' );
		$this->is_admin = $pec_init->post( 'is_admin' );

		if ( $this->is_admin && strtolower( $this->is_admin ) !== "false" ) 
	      $this->is_admin = true;
	    else
	      $this->is_admin = false;

		$this->opts['id_calendar'] = $this->calendar;
		$this->opts['is_admin'] = $this->is_admin;
		if( $this->timestamp )
			$this->opts['defaultDate'] = $this->timestamp;
		$this->opts['category'] = $this->category;
		$this->opts['columns'] = $this->columns;
		$this->opts['event_id'] = $this->event_id;
		$this->opts['author'] = $this->author;
		$this->opts['location'] = $this->location;
		$this->opts['speaker'] = $this->speaker;
		$this->opts['include_all_events'] = $this->include_all_events;
		$this->opts['modal'] = $this->modal;
		$this->opts['hide_old_dates'] = $this->hide_old_dates;
		$this->opts['calendar_per_date'] = $this->calendar_per_date;

	}

	/**
	 * Print Date Calendar Layout
	 * 
	 * @return void
	 */
	function get_date() 
	{
		global $pec_init;

		header( "HTTP/1.1 200 OK" );
	    $nonce = $pec_init->post( 'postEventsNonce' );
		//if ( ! wp_verify_nonce( $nonce, 'ajax-get-events-nonce' ) )
	    //    die ( 'Busted!');
			
		if( ! is_numeric( $pec_init->post( 'date' ) ) ) die();

		// Save POST vars
		$this->ajax_save_post();
		
		$dpProEventCalendar_class = $pec_init->init_base( $this->opts );

		if( $this->type == 'modern' )
		{
			$dpProEventCalendar_class::$calendar_obj->show_titles_monthly = 1;

			$dpProEventCalendar_class::$calendar_obj->link_post = 1;
		}
		
		die( $dpProEventCalendar_class->monthlyCalendarLayout( ( $this->type == 'compact' ? true : false ) ) );

	}

	/**
	 * Print Year for Yearly layuot
	 * 
	 * @return void
	 */
	function get_year() 
	{
		global $pec_init;

		header( "HTTP/1.1 200 OK" );
	    $nonce = $pec_init->post( 'postEventsNonce' );
		//if ( ! wp_verify_nonce( $nonce, 'ajax-get-events-nonce' ) )
	    //    die ( 'Busted!');
			
		if( ! is_numeric( $pec_init->post( 'year' ) ) ) die();

		// Save POST vars
		$this->ajax_save_post();
		
		$year = $pec_init->post( 'year' );

		$this->opts['defaultDate'] = strtotime( $year . '-01-01' );
		
		$dpProEventCalendar_class = $pec_init->init_base( $this->opts );

		$yearlyLayout = new DPPEC_YearlyLayout();

		die( $yearlyLayout->yearlyCalendarLayout() );

	}

	/**
	 * Print Daily Layout
	 * 
	 * @return void
	 */
	function get_daily() 
	{
		global $pec_init;

		header( "HTTP/1.1 200 OK" );
	    $nonce = $pec_init->post( 'postEventsNonce' );
		//if ( ! wp_verify_nonce( $nonce, 'ajax-get-events-nonce' ) )
	    //    die ( 'Busted!');
			
		if( ! is_numeric( $pec_init->post( 'date' ) ) ) die();
		
		// Save POST vars
		$this->ajax_save_post();

		$dpProEventCalendar_class = $pec_init->init_base( $this->opts );
		
		echo "<!--" . $dpProEventCalendar_class::date_i18n( get_option('date_format'), $this->timestamp ) . ">!]-->";
		
		die( $dpProEventCalendar_class->dailyCalendarLayout() );

	}

	/**
	 * Print Weekly Layout
	 * 
	 * @return void
	 */
	function get_weekly()
	{
		global $pec_init;

		header( "HTTP/1.1 200 OK" );
	    $nonce = $pec_init->post( 'postEventsNonce' );
		//if ( ! wp_verify_nonce( $nonce, 'ajax-get-events-nonce' ) )
	    //    die ( 'Busted!');
			
		if( ! is_numeric( $pec_init->post( 'date' ) ) ) die();
		
		// Save POST vars
		$this->ajax_save_post();

		$dpProEventCalendar_class = $pec_init->init_base( $this->opts );
		
		if($dpProEventCalendar_class::$calendar_obj->first_day == 1) 
		{

			$weekly_first_date = strtotime( 'last monday', ( $this->timestamp + ( 24* 60 * 60 ) ) );
			$weekly_last_date = strtotime( 'next sunday', ( $this->timestamp - ( 24* 60 * 60 ) ) );

		} else {

			$weekly_first_date = strtotime( 'last sunday', ( $this->timestamp + ( 24* 60 * 60 ) ) );
			$weekly_last_date = strtotime( 'next saturday', ( $this->timestamp - ( 24* 60 * 60 ) ) );

		}

		$weekly_txt = $dpProEventCalendar_class::date_i18n( 'd F', $weekly_first_date ) . ' - ' . $dpProEventCalendar_class::date_i18n( 'd F, Y', $weekly_last_date );
		
		if( date( 'm', $weekly_first_date ) == $dpProEventCalendar_class::date_i18n( 'm', $weekly_last_date ) ) 
		
			$weekly_txt = $dpProEventCalendar_class::date_i18n('d', $weekly_first_date) . ' - ' . $dpProEventCalendar_class::date_i18n('d F, Y', $weekly_last_date);
			
		
		if( date( 'Y', $weekly_first_date ) != date( 'Y', $weekly_last_date ) ) 
				
			$weekly_txt = $dpProEventCalendar_class::date_i18n( get_option('date_format'), $weekly_first_date ).' - '.$dpProEventCalendar_class::date_i18n( get_option('date_format'), $weekly_last_date );
			

		echo "<!--" . $weekly_txt . ">!]-->";
		
		die( $dpProEventCalendar_class->weeklyCalendarLayout() );

	}

	/**
	 * Print Event Modal
	 * 
	 * @return void
	 */
	function get_event_modal() 
	{
		
		global $pec_init;

		header( "HTTP/1.1 200 OK" );
	    $nonce = $pec_init->post( 'postEventsNonce' );
		//if ( ! wp_verify_nonce( $nonce, 'ajax-get-events-nonce' ) )
	    //   die ( 'Busted!');
			
		if( ! is_numeric( $pec_init->post( 'event_id' ) ) ) die();
		
		// Save POST vars
		$this->ajax_save_post();
		
		$dpProEventCalendar_class = $pec_init->init_base( $this->opts );

		$dpProEventCalendar_class->event_modal( $this->event_id, $this->timestamp );


	}

	/**
	 * Get Events List
	 * 
	 * @return void
	 */
	function get_events() 
	{
		global $pec_init;

		header( "HTTP/1.1 200 OK" );
	    $nonce = $pec_init->post( 'postEventsNonce' );
		//if ( ! wp_verify_nonce( $nonce, 'ajax-get-events-nonce' ) )
	    //   die ( 'Busted!');
			
		if( $pec_init->post( 'date' ) == '' ) die();
		
		// Save POST vars
		$this->ajax_save_post();

		
		$dpProEventCalendar_class = $pec_init->init_base( $this->opts );

		$dpProEventCalendar_class->switchCalendarTo( $this->type, 5, 0, $this->category, $this->author, $this->event_id, $this->location );
		
		die( $dpProEventCalendar_class->eventsListLayout( $this->timestamp ) );

	}

	/**
	 * Get Specific Event Details
	 * 
	 * @return void
	 */
	function get_event() 
	{
		global $pec_init;

		header("HTTP/1.1 200 OK");
	    $nonce = $pec_init->post( 'postEventsNonce' );
		//if ( ! wp_verify_nonce( $nonce, 'ajax-get-events-nonce' ) )
	    //   die ( 'Busted!');
			
		if( $pec_init->post( 'event' ) == '' ) die();
		
		$event = $pec_init->post( 'event' );
		$calendar = $pec_init->post( 'calendar' );
		$date = $pec_init->post( 'date' );

		$opts = array();
		$opts['id_calendar'] = $calendar;
		$dpProEventCalendar_class = $pec_init->init_base( $opts );
		
		die( $dpProEventCalendar_class->display_event( $date, $event ) );

	}

	/**
	 * Submit new event form
	 * 
	 * @return void
	 */
	function submit_event() 
	{

		header( "HTTP/1.1 200 OK" );
		global $current_user, $dpProEventCalendar_cache, $dpProEventCalendar, $pec_init;

	    $nonce = $pec_init->post( 'postEventsNonce' );
		//if ( ! wp_verify_nonce( $nonce, 'ajax-get-events-nonce' ) )
	    //   die ( 'Error!');
			
		if( ! is_numeric( $pec_init->post( 'calendar' ) ) && ! is_numeric( $pec_init->post( 'edit_calendar' ) ) ) die();

		wp_get_current_user();
		
		$calendar = $pec_init->post( 'calendar' );
		if( ! is_numeric( $calendar ) ) 
		{
		
			$calendar = $pec_init->post( 'edit_calendar' );

			if( ! is_numeric( $pec_init->post( 'edit_event' ) ) ) 
				die();
			
			$calendar_arr = get_post_meta( $pec_init->post( 'edit_event' ), 'pec_id_calendar', true );

			$calendar_arr = explode(',', $calendar_arr);

			if( ! in_array( $calendar, $calendar_arr ) ) 
				die();
			
		}

		$opts = array();
		$opts['id_calendar'] = $calendar;
		$dpProEventCalendar_class = $pec_init->init_base( $opts );

		$dpProEventCalendar_class->getCalendarData();
		$calendar_obj = $dpProEventCalendar_class->get_calendar();
		
		if( !is_user_logged_in() && !$calendar_obj->assign_events_admin ) die();
		
		// ReCaptcha Validation
		if( pec_setting( 'recaptcha_enable' ) && pec_setting( 'recaptcha_site_key' ) != "" ) 
		{
		
			//set POST variables
			$url = 'https://www.google.com/recaptcha/api/siteverify';
			$fields = array(
					'secret' => pec_setting( 'recaptcha_secret_key' ),
					'response' => $pec_init->post( 'grecaptcharesponse' ),
					'remoteip' => $_SERVER['REMOTE_ADDR']
			);
			
			//url-ify the data for the POST
			foreach( $fields as $key=>$value ) { $fields_string .= $key.'='.$value.'&'; }
			rtrim( $fields_string, '&' );
			
			//open connection
			$ch = curl_init();
			
			//set the url, number of POST vars, POST data
			curl_setopt( $ch,CURLOPT_URL, $url );
			curl_setopt( $ch,CURLOPT_POST, true );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
			curl_setopt( $ch,CURLOPT_POSTFIELDS, $fields_string );
			
			//execute post
			$result = curl_exec( $ch );
			
			$result = json_decode( $result, true );
			
			//close connection
			curl_close( $ch );
			
			if( $result['success'] != true ) die('0 - captcha');

		}


		$category = array();
		$speaker = array();

		foreach ( $_POST as $key => $value ) 
		{
		
			if (strpos($key, 'category-') === 0) 
				$category[] = $value;

			if (strpos($key, 'speaker-') === 0) 
				$speaker[] = $value;
		
		}

		$speaker = implode( ',', $speaker );

		$new_event = array(
		  'post_title'    => $pec_init->post( 'title' ),
		  'post_content'  => $pec_init->post( 'description' ),
		  'post_category'  => $category,
		  'post_status'   => ( $calendar_obj->publish_new_event ? 'publish' : 'pending' ),
		  'post_type'	  => DP_PRO_EVENT_CALENDAR_EVENT_POST_TYPE
		);
		
		if( !is_user_logged_in() && $calendar_obj->assign_events_admin > 0 ) 
			$new_event['post_author'] = $calendar_obj->assign_events_admin;
		
		if( is_numeric( $pec_init->post( 'edit_calendar' ) ) && is_numeric( $pec_init->post( 'edit_event' ) ) ) 
		{
		
			$inserted = $pec_init->post( 'edit_event' );
			
			$event_edit = get_post($inserted);
			if( $event_edit->post_author == $current_user->ID || current_user_can( 'manage_options' ) ) 
			{
			
				$new_event['ID'] = $inserted;
				$new_event['post_status'] = $event_edit->post_status;
				
				wp_update_post($new_event);

				do_action('pec_action_edit_event', $inserted);

			} else 
				die();	
			
		} else {
			$inserted = wp_insert_post( $new_event );
			
			do_action( 'pec_action_new_event', $inserted );
		}
		
		if( ! is_numeric( $inserted ) ) die();
		
		wp_set_post_terms( $inserted, $category, 'pec_events_category' );
		
		if( is_array( $dpProEventCalendar['custom_fields_counter'] ) ) 
		{
		
			$counter = 0;
			
			foreach( $dpProEventCalendar['custom_fields_counter'] as $key ) 
			{
				update_post_meta( $inserted, "pec_custom_" . $dpProEventCalendar['custom_fields']['id'][$counter], $pec_init->post( 'pec_custom_' . $dpProEventCalendar['custom_fields']['id'][$counter] ) );
				$counter++;		
			}
		}

		$all_day = $pec_init->post( 'all_day' );

		if( $pec_init->post( 'time_hours' ) == "" && $pec_init->post( 'edit_event' ) == '' ) 
			$all_day = 1;
			

		// Set Location
		$location = $pec_init->post( 'location' );

		if($location == "-1") 
		{
		
			if( $pec_init->post( 'location_name' ) != "" )
				$location = dpProEventCalendar_create_venue( $pec_init->post( 'location_name' ), $pec_init->post( 'location_address' ), $pec_init->post( 'googlemap' ), $lnlat = $pec_init->post( 'map_lnlat' ) );
			else
				$location = "";

		}

		if( $pec_init->post( 'time_hours' ) != "" ) {
			$time_hours = $pec_init->post( 'time_hours' );
		} else {
			if( is_numeric( $pec_init->post( 'edit_event' ) ) ) {
				$pec_date_time = get_post_meta( $pec_init->post( 'edit_event' ), 'pec_date', true );
				$time_hours = date( 'h', strtotime( $pec_date_time ) );
			} else {
				$time_hours = '00';
			}
		}

		if( $pec_init->post( 'time_minutes' ) != "" ) {
			$time_minutes = $pec_init->post( 'time_minutes' );
		} else {
			if( is_numeric( $pec_init->post( 'edit_event' ) ) ) {
				$pec_date_time = get_post_meta( $pec_init->post( 'edit_event' ), 'pec_date', true );
				$time_minutes = date('i', strtotime($pec_date_time));
			} else {
				$time_minutes = '00';
			}
		}

		$end_time_hours = '';
		if( $pec_init->post( 'end_time_hh' ) != "" ) 
			$end_time_hours = $pec_init->post( 'end_time_hh' );

		$end_time_mins = '';
		if( $pec_init->post( 'end_time_mm' ) != "" ) 
			$end_time_mins = $pec_init->post( 'end_time_mm' );

		$end_date = '';
		if( $pec_init->post( 'end_date' ) != "" ) 
			$end_date = $pec_init->post( 'end_date' );
		
		if( $pec_init->post( 'end_date_hidden' ) != "") 
			$end_date = $pec_init->post( 'end_date_hidden' );
		

		$date = $pec_init->post( 'date' );
		if( $pec_init->post( 'date_hidden' ) != "") 
			$date = $pec_init->post( 'date_hidden' );
		

		$extra_dates = $pec_init->post( 'extra_dates' );
		if( $pec_init->post( 'extra_dates_hidden' ) != "") 
			$extra_dates = $pec_init->post( 'extra_dates_hidden' );
		
		$code = get_post_meta( $inserted, 'pec_code', true );
		if( ! isset( $code ) || $code == ""  )
		{

			$code = dpProeventCalendar_generate_code();
			update_post_meta( $inserted, 'pec_code', $code );

		}

		update_post_meta( $inserted, "pec_timezone", $pec_init->post( 'timezone' ) );
		update_post_meta( $inserted, "pec_extra_dates", $extra_dates );
		update_post_meta( $inserted, "pec_link", $pec_init->post( 'link' ) );
		update_post_meta( $inserted, "pec_share", $pec_init->post( 'share' ) );
		update_post_meta( $inserted, "pec_location", $location );
		update_post_meta( $inserted, "pec_speaker", $speaker );
		update_post_meta( $inserted, "pec_phone", $pec_init->post( 'phone' ) );
		update_post_meta( $inserted, "pec_color", $pec_init->post( 'color' ) );	
		update_post_meta( $inserted, 'pec_id_calendar', $calendar );

		update_post_meta( $inserted, 'pec_date', $date.' '.$time_hours.':'.$time_minutes.':00' );
		update_post_meta( $inserted, 'pec_all_day', $all_day );
		update_post_meta( $inserted, 'pec_recurring_frecuency', $pec_init->post( 'recurring_frecuency' ) );
		update_post_meta( $inserted, 'pec_end_date', $end_date );
		update_post_meta( $inserted, 'pec_end_time_hh', $end_time_hours );
		update_post_meta( $inserted, 'pec_end_time_mm', $end_time_mins );
		update_post_meta( $inserted, 'pec_hide_time', $pec_init->post( 'hide_time' ) );
		
		update_post_meta( $inserted, 'pec_daily_every', $pec_init->post( 'pec_daily_every' ) );
		update_post_meta( $inserted, 'pec_daily_working_days', $pec_init->post( 'pec_daily_working_days' ) );
		update_post_meta( $inserted, 'pec_weekly_day', $pec_init->post( 'pec_weekly_day' ) );
		update_post_meta( $inserted, 'pec_weekly_every', $pec_init->post( 'pec_weekly_every' ) );
		update_post_meta( $inserted, 'pec_monthly_every', $pec_init->post( 'pec_monthly_every' ) );
		update_post_meta( $inserted, 'pec_monthly_position', $pec_init->post( 'pec_monthly_position' ) );
		update_post_meta( $inserted, 'pec_monthly_day', $pec_init->post( 'pec_monthly_day' ) );
		update_post_meta( $inserted, 'pec_enable_booking', $pec_init->post( 'booking_enable' ) );
		update_post_meta( $inserted, 'pec_booking_limit', $pec_init->post( 'limit' ) );
		update_post_meta( $inserted, 'pec_booking_block_hours', $pec_init->post( 'block_hours' ) );
		update_post_meta( $inserted, 'pec_booking_price', $pec_init->post( 'price' ) );
		
		// Process image...
		if( isset( $_FILES['event_image'] ) )
		{

			$image = $_FILES['event_image'];
			$timestamp = time();
			
			$wp_filetype = wp_check_filetype( basename( $image['name'] ), null );
			if( strtolower($wp_filetype['ext']) == "jpeg" || strtolower($wp_filetype['ext']) == "png" || strtolower($wp_filetype['ext']) == "gif" || strtolower($wp_filetype['ext']) == "jpg" ) {
				$uploads = wp_upload_dir();
				
				$image['name'] = md5($image['name']).'.'.strtolower($wp_filetype['ext']);

				$filesize = (filesize($image['tmp_name']) / 1000);
				$maxFileSize = dpProEventCalendar_convertBytes(ini_get('upload_max_filesize')) / 1000;
				$maxFileSize_settings = pec_setting( 'max_file_size' );

				if( $maxFileSize_settings < $maxFileSize ) 
					$maxFileSize = $maxFileSize_settings;
				
				if( $maxFileSize != "" && $filesize > $maxFileSize ) 
					return false;
				
				if ( ! copy($image['tmp_name'], $uploads['path']."/".$current_user->ID."_".$timestamp."_".$image['name']) ) {
					//echo "Error copying file...<br>";
				} else {
					
					$attachment = array(
					 'guid' => $uploads['path'] . '/'.$current_user->ID."_".$timestamp."_" . basename( $image['name'] ), 
					 'post_mime_type' => $wp_filetype['type'],
					 'post_title' => '',
					 'post_content' => '',
					 'post_excerpt' => '',
					 'post_status' => 'inherit'
					);
					
					$attach_id = wp_insert_attachment( $attachment, $uploads['path'] . '/'.$current_user->ID."_".$timestamp."_" . basename( $image['name'] ) );
					
					require_once(ABSPATH . 'wp-admin/includes/image.php');
					$attach_data = wp_generate_attachment_metadata( $attach_id, $uploads['path'] . '/'.$current_user->ID."_".$timestamp."_" . basename( $image['name'] ) );
					wp_update_attachment_metadata( $attach_id, $attach_data );
					
					update_post_meta($inserted, "_thumbnail_id", $attach_id);
				}
			}

		}

		if( $calendar_obj->email_admin_new_event && ! is_numeric( $pec_init->post( 'edit_calendar' ) ) ) 
		{
		
			add_filter( 'wp_mail_from_name', 'dpProEventCalendar_wp_mail_from_name' );
			add_filter( 'wp_mail_from', 'dpProEventCalendar_wp_mail_from' );
			
			$message = __('A new event is waiting for approval:', 'dpProEventCalendar') . ' ' . $pec_init->post( 'title' ) . " ( " . get_admin_url() . "post.php?post=" . $inserted . "&action=edit )";
			
			$admin_email = ( $calendar_obj->admin_email != "" ? $calendar_obj->admin_email : get_bloginfo('admin_email') );
			$success_email = wp_mail( $admin_email, __('New Event', 'dpProEventCalendar'), $message );
			
		}

		// Action hook
		do_action( 'pec_event_new', $inserted );

		// Clear Cache
		if( isset( $dpProEventCalendar_cache['calendar_id_' . $calendar] ) ) 
		{
		   $dpProEventCalendar_cache['calendar_id_' . $calendar] = array();
		   update_option( 'dpProEventCalendar_cache', $dpProEventCalendar_cache );
	    }
		
		die();
	}

	/**
	 * Get Accordion Month
	 * 
	 * @return void
	 */
	function get_events_month() 
	{
		global $pec_init;

		header("HTTP/1.1 200 OK");
	    $nonce = $pec_init->post( 'postEventsNonce' );
		//if ( ! wp_verify_nonce( $nonce, 'ajax-get-events-nonce' ) )
	    //   die ( 'Busted!');
			
		if( $pec_init->post( 'month' ) == '' ) die();

		// Save POST vars
		$this->ajax_save_post();
		
		$month = $pec_init->post( 'month' );
		$year = $pec_init->post( 'year' );
		
		$dpProEventCalendar_class = $pec_init->init_base( $this->opts );
		
		$next_month_days = cal_days_in_month( CAL_GREGORIAN, str_pad( ( $month ), 2, "0", STR_PAD_LEFT ), $year );
		$month_number = str_pad( $month, 2, "0", STR_PAD_LEFT );
		
		$start = $year."-".$month_number."-01 00:00:00";

		if( $dpProEventCalendar_class::$calendar_obj->hide_old_dates && date("Y-m") == $year . "-" . $month_number ) 
			$start = date( "Y-m-d H:i:s" );
		
		die( $dpProEventCalendar_class::upcomingCalendarLayout( false, 20, '', $start, $year . "-" . $month_number . "-" . $next_month_days . " 23:59:59", true ) );

	}

	/**
	 * Get Events List
	 * 
	 * @return void
	 */
	function get_events_month_list() 
	{

		global $pec_init;

		header("HTTP/1.1 200 OK");
	    $nonce = $pec_init->post( 'postEventsNonce' );
		//if ( ! wp_verify_nonce( $nonce, 'ajax-get-events-nonce' ) )
	    //   die ( 'Busted!');
			
		if( ! is_numeric( $pec_init->post( 'month' ) ) ) die();
		
		// Save POST vars
		$this->ajax_save_post();

		$month = $pec_init->post( 'month' );
		$year = $pec_init->post( 'year' );
		
		$limit = $pec_init->post( 'limit' );
		$widget = $pec_init->post( 'widget' );
		
		$dpProEventCalendar_class = $pec_init->init_base( $this->opts );
		
		$next_month_days = cal_days_in_month(CAL_GREGORIAN, str_pad(($month), 2, "0", STR_PAD_LEFT), $year);
		$month_number = str_pad($month, 2, "0", STR_PAD_LEFT);
		$this_month_day = "01";
		
		if( $dpProEventCalendar_class::$calendar_obj->hide_old_dates && ( $month <= date('m') || $year < date('Y') ) ) 
		{
		
			if( $month == date('m') && $year == date('Y') ) 
			{
		
				$this_month_day = str_pad($dpProEventCalendar_class::$datesObj->currentDate, 2, "0", STR_PAD_LEFT);
		
			} elseif($year <= date('Y')) {
		
				$this_month_day = $next_month_days;
				$next_month_days = "01";
		
			}
		
		}

		$limit_events = 40;
		if( $widget && is_numeric( $limit ) && $limit > 0 ) 
			$limit_events = $limit;

		die( $dpProEventCalendar_class->eventsMonthList( $year . "-" . $month_number . "-" . $this_month_day . " 00:00:00", $year . "-" . $month_number . "-" . $next_month_days . " 23:59:59", $limit_events ) );

	}

	/**
	 * Submit New Subscriber Form
	 * 
	 * @return void
	 */
	function new_subscriber() 
	{

		global $pec_init;
		
		$your_name = stripslashes( $pec_init->post( 'your_name' ) );
		$your_email = stripslashes( $pec_init->post( 'your_email' ) );
		$calendar = stripslashes( $pec_init->post( 'calendar' ) );
		
		if( pec_setting( 'recaptcha_enable' ) && pec_setting( 'recaptcha_site_key' ) != "" ) 
		{
		
			//set POST variables
			$url = 'https://www.google.com/recaptcha/api/siteverify';
			$fields = array(
									'secret' => pec_setting( 'recaptcha_secret_key' ),
									'response' => $pec_init->post( 'grecaptcharesponse' ),
									'remoteip' => $_SERVER['REMOTE_ADDR']
							);
			
			//url-ify the data for the POST
			foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
			rtrim($fields_string, '&');
			
			//open connection
			$ch = curl_init();
			
			//set the url, number of POST vars, POST data
			curl_setopt( $ch,CURLOPT_URL, $url );
			curl_setopt( $ch,CURLOPT_POST, true );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
			curl_setopt( $ch,CURLOPT_POSTFIELDS, $fields_string );
			
			//execute post
			$result = curl_exec( $ch );
			
			$result = json_decode( $result, true );
			
			//close connection
			curl_close($ch);
			
			if( $result['success'] != true ) die(__("Failed Captcha", "dpProEventCalendar"));
		}
		
		$opts = array();
		$opts['id_calendar'] = $calendar;
		$opts['is_admin'] = true;
		$dpProEventCalendar_class = $pec_init->init_base( $opts );
		
		$dpProEventCalendar_class->calendarSubscription( $your_email, $your_name );

		// Action hook
		do_action( 'pec_subscriber_new', $calendar, $your_name, $your_email );
		
		die();	
	}

	/**
	 * Rate Event
	 * 
	 * @return void
	 */
	function rate_event() 
	{

		global $pec_init;
		
		if( ! is_user_logged_in() ) die();	
		
		$event_id = stripslashes( $pec_init->post( 'event_id') );
		$rate = stripslashes( $pec_init->post( 'rate' ) );
		$calendar = stripslashes( $pec_init->post( 'calendar' ) );
		
		$opts = array();
		$opts['id_calendar'] = $calendar;
		$opts['is_admin'] = true;
		$dpProEventCalendar_class = $pec_init->init_base( $opts );
		
		echo $dpProEventCalendar_class->rateEvent( $event_id, $rate );

		// Action hook
		do_action( 'pec_event_rated', $event_id, $rate, $calendar );
		
		die();	
	}

	/**
	 * Cancel Booking
	 * 
	 * @return void
	 */
	function cancel_booking() 
	{

		header("HTTP/1.1 200 OK");
	    global $current_user, $wpdb, $pec_init;

	    $nonce = $pec_init->post( 'postEventsNonce' );
		
		if ( ! wp_verify_nonce( $nonce, 'ajax-get-events-nonce' ) )
	       die ( 'Error!');
			
		if( ! is_numeric( $pec_init->post( 'calendar' ) ) || ! is_numeric( $pec_init->post( 'cancel_booking_id' ) ) || ! is_numeric( $pec_init->post( 'cancel_booking_event' ) ) ) die();

		wp_get_current_user();
		
		if( ! is_user_logged_in() ) die();

		$calendar = $pec_init->post( 'calendar' );
		$booking_id = $pec_init->post( 'cancel_booking_id' );
		$event_id = $pec_init->post( 'cancel_booking_event' );

		$querystr = "
	        UPDATE " . DP_PRO_EVENT_CALENDAR_TABLE_BOOKING . " SET status = '" . DP_PRO_EVENT_CALENDAR_BOOKING_CANCELED_BY_USER . "', cancel_date = '".current_time( 'Y-m-d H:i:s' )."' 
			WHERE id_event = %d AND id_user = %d AND id = %d";

	    $wpdb->query( $wpdb->prepare( $querystr, $event_id, $current_user->ID, $booking_id ) );

	    $calendar_data = $wpdb->get_row( 
			$wpdb->prepare("
				SELECT booking_cancel_email_template, booking_cancel_email_enable 
				FROM " . DP_PRO_EVENT_CALENDAR_TABLE_CALENDARS . " 
				WHERE id = %d", $calendar
			)
		);

	    $booking_cancel_email_template = $calendar_data->booking_cancel_email_template;

		if($booking_cancel_email_template == '') 
			$booking_cancel_email_template = "Hi #USERNAME#,\n\nThe following booking has been canceled:\n\n#EVENT_DETAILS#\n\n#CANCEL_REASON#\n\nPlease contact us if you have questions.\n\nKind Regards.\n#SITE_NAME#";
		

		$booking_cancel_email_template_admin = str_replace(" #USERNAME#", "", $booking_cancel_email_template);
		
		add_filter( 'wp_mail_from_name', 'dpProEventCalendar_wp_mail_from_name' );
		add_filter( 'wp_mail_from', 'dpProEventCalendar_wp_mail_from' );
		$headers = array('Content-Type: text/html; charset=UTF-8');

		// Email to User
		$booking_user_email = $current_user->user_email;
		
		wp_mail( $booking_user_email, get_bloginfo('name'), apply_filters('pec_booking_email_cancel', $booking_cancel_email_template, $booking_id), $headers );

		$event_author_id = get_post_field( 'post_author', $event_id );
		
		wp_mail( get_the_author_meta( 'user_email', $event_author_id ), get_bloginfo('name'), apply_filters('pec_booking_email_cancel', $booking_cancel_email_template, $booking_id), $headers );




		do_action('pec_action_cancel_booking', $inserted);

		die();
	}

	/**
	 * Remove Event
	 * 
	 * @return void
	 */
	function remove_event() 
	{

		header("HTTP/1.1 200 OK");

		global $current_user, $dpProEventCalendar_cache, $pec_init;

	    $nonce = $pec_init->post( 'postEventsNonce' );
		if ( ! wp_verify_nonce( $nonce, 'ajax-get-events-nonce' ) )
	       die ( 'Error!');
			
		if( ! is_numeric( $pec_init->post( 'calendar' ) ) ) die();
		
		
		wp_get_current_user();
		
		if( ! is_user_logged_in() ) die();

		$calendar = $pec_init->post( 'calendar' );

		$opts = array();
		$opts['id_calendar'] = $calendar;
		$dpProEventCalendar_class = $pec_init->init_base( $opts );

		$dpProEventCalendar_class->getCalendarData();
		
		if( is_numeric( $pec_init->post( 'remove_event_calendar' ) ) && is_numeric( $pec_init->post( 'remove_event' ) ) ) 
		{
			$event = $pec_init->post( 'remove_event' );
			
			$event_edit = get_post( $event );
			if( $event_edit->post_author == $current_user->ID && $event_edit->post_type == DP_PRO_EVENT_CALENDAR_EVENT_POST_TYPE ) {
							
				wp_delete_post( $event );
				do_action( 'pec_action_remove_event', $event );

				if( isset($dpProEventCalendar_cache['calendar_id_'.$calendar]) ) {
				   $dpProEventCalendar_cache['calendar_id_'.$calendar] = array();
				   update_option( 'dpProEventCalendar_cache', $dpProEventCalendar_cache );
			   }
			}
			
		}
		die();
	}

	/**
	 * Cancel Tour
	 * 
	 * @return void
	 */
	function cancel_tour() 
	{

		global $pec_init;
		
		header("HTTP/1.1 200 OK");;

	    $nonce = $pec_init->post( 'postEventsNonce' );
		if ( ! wp_verify_nonce( $nonce, 'ajax-get-events-nonce' ) )
	       die ( 'Error!');
			
		update_option( 'pec-hide-tour', 1 );
		die();

	}

	/**
	 * Get Search Results
	 * 
	 * @return void
	 */
	function get_search_results() 
	{

		global $pec_init;

		header("HTTP/1.1 200 OK");
	    $nonce = $pec_init->post( 'postEventsNonce' );
		//if ( ! wp_verify_nonce( $nonce, 'ajax-get-events-nonce' ) )
	    //    die ( 'Busted!');
			
		if( ! is_numeric( $pec_init->post( 'calendar' ) ) || empty( $pec_init->post( 'key' ) ) ) die();
		
		$calendar =$pec_init->post( 'calendar' );
		$key = $pec_init->post( 'key' );
		$type = $pec_init->post( 'type' );
		$author = $pec_init->post( 'author' );
		$columns = $pec_init->post( 'columns' );
		
		$opts = array();
		$opts['id_calendar'] = $calendar;
		$opts['author'] = $author;
		$opts['columns'] = $columns;
		$dpProEventCalendar_class = $pec_init->init_base( $opts );
		
		die( $dpProEventCalendar_class->getSearchResults( $key, $type ) );

	}

	/**
	 * Book Event Form
	 * 
	 * @return void
	 */
	function book_event() 
	{

		header("HTTP/1.1 200 OK");

		if( ! isset( $_SESSION ) ) 
			session_start();

		global $current_user, $wpdb, $pec_init;
		
	    $nonce = $pec_init->post( 'postEventsNonce' );
		//if ( ! wp_verify_nonce( $nonce, 'ajax-get-events-nonce' ) )
	    //    die ( 'Busted!');
			
		if( $pec_init->post( 'calendar' ) == "" || ! is_numeric( $pec_init->post( 'event_id' ) ) || $pec_init->post( 'event_date' ) == "" ) die();

		$calendar = $pec_init->post( 'calendar' );
		$comment = $pec_init->post( 'comment' );
		$quantity = $pec_init->post( 'quantity' );
		$id_event = $pec_init->post( 'event_id' );
		$id_coupon = $pec_init->post( 'pec_payment_discount_id' );
		$coupon = $pec_init->post( 'pec_payment_discount_coupon' );
		$name = $pec_init->post( 'name' );
		$phone = $pec_init->post( 'phone' );
		$email = $pec_init->post( 'email' );
		$event_date = $pec_init->post( 'event_date' );
		$return_url = $pec_init->post( 'return_url' );
		$ticket = $pec_init->post( 'ticket' );
		$extra_fields = serialize( $pec_init->post( 'extra_fields' ) );
		$price = get_post_meta( $id_event, 'pec_booking_price', true );
		$code = dpProeventCalendar_generate_code( 20 );
		$session = sha1( wp_salt( 'secure_auth' ) . session_id() );

		if( $price == '.00' || $price == '0' )
			$price = "";

		if( ! is_numeric( $quantity ) || $quantity < 1 )
			$quantity = 1;	
		
		$opts = array();
		$opts['id_calendar'] = $calendar;
		$dpProEventCalendar_class = $pec_init->init_base( $opts );
		
		$calendar_obj = $dpProEventCalendar_class->get_calendar();

		if( ! is_user_logged_in() && ! $calendar_obj->booking_non_logged )
			die();	
		
		// Check Coupon
		$coupon_discount = '';
		if( is_numeric( $id_coupon ) ) 
		{
		
			if( strtolower( get_the_title( $id_coupon ) ) == strtolower( $coupon ) ) 
				$coupon_discount = get_post_meta( $id_coupon, 'pec_coupon_amount', true );
			else 
				$id_coupon = "";
			
		}
		
		$wpdb->insert( 
			DP_PRO_EVENT_CALENDAR_TABLE_BOOKING, 
			array( 
				'id_calendar' 	=> $calendar, 
				'booking_date' 	=> date('Y-m-d H:i:s'),
				'event_date'	=> $event_date,
				'id_event'		=> $id_event,
				'id_user'		=> $current_user->ID,
				'id_coupon'		=> $id_coupon,
				'coupon_discount'=> $coupon_discount,
				'comment'		=> $comment,
				'quantity'		=> $quantity,
				'name'			=> $name,
				'phone'			=> $phone,
				'email'			=> $email,
				'extra_fields'	=> $extra_fields,
				'status'		=> (($price != '' || $ticket != "") ? 'pending' : ''),
				'code'			=> $code,
				'session_id'	=> $session
			), 
			array( 
				'%d', 
				'%s',
				'%s',
				'%d',
				'%d',
				'%d',
				'%s',
				'%s',
				'%d',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s'
			) 
		);
		
		$id_booking = $wpdb->insert_id;

		do_action('pec_action_book_event', $id_booking, $id_event, $current_user->ID);

		if($price == "" && $ticket == '') {
		
			// Send emails for free bookings
			
			if(is_user_logged_in()) {
				
				$userdata = get_userdata($current_user->ID);
				
			} else {
				
				$userdata = new stdClass();
				$userdata->display_name = $name;
				$userdata->user_email = $email;
					
			}
			
			if($calendar_obj->booking_email_template_user == '')
				$calendar_obj->booking_email_template_user = "Hi #USERNAME#,\n\nThanks for booking the event:\n\n#EVENT_DETAILS#\n\nPlease contact us if you have questions.\n\nKind Regards.\n#SITE_NAME#";
			
			if($calendar_obj->booking_email_template_admin == '')
				$calendar_obj->booking_email_template_admin = "The user #USERNAME# (#USEREMAIL#) booked the event:\n\n#EVENT_DETAILS#\n\n#COMMENT#\n\n#SITE_NAME#";
			
			add_filter( 'wp_mail_from_name', 'dpProEventCalendar_wp_mail_from_name' );
			add_filter( 'wp_mail_from', 'dpProEventCalendar_wp_mail_from' );
			$headers = 'Content-Type: text/html; charset=UTF-8';

			// Email to User

			wp_mail( $userdata->user_email, get_bloginfo('name'), apply_filters('pec_booking_email', $calendar_obj->booking_email_template_user, $id_event, $userdata->display_name, $userdata->user_email, $event_date, $comment, $quantity, $phone, $extra_fields), $headers );
			
			// Email to Author
			$event_author_id = get_post_field( 'post_author', $id_event );
			
			wp_mail( get_the_author_meta( 'user_email', $event_author_id ), get_bloginfo('name'), apply_filters('pec_booking_email', $calendar_obj->booking_email_template_admin, $id_event, $userdata->display_name, $userdata->user_email, $event_date, $comment, $quantity, $phone, $extra_fields), $headers );
		}
		
		$return = array(
			array(
				"book_btn" => $dpProEventCalendar_class::$translation['TXT_BOOK_EVENT_REMOVE'], 
				"notification" => $dpProEventCalendar_class::$notifications->message('success', $dpProEventCalendar_class::$translation['TXT_BOOK_EVENT_SAVED'], true),
				"gateway_screen" => apply_filters('pec_receipt_gateways', (is_numeric($ticket) ? $ticket : ''), $id_event, $event_date, $id_booking, $return_url, $quantity, $code)
			),
			array(
				"book_btn" => $dpProEventCalendar_class::$translation['TXT_BOOK_EVENT'], 
				"notification" => $dpProEventCalendar_class::$notifications->message('success', $dpProEventCalendar_class::$translation['TXT_BOOK_EVENT_REMOVED'], true)
			)
		);

		// Action Hook
		do_action( 'pec_booking_new', $id_booking, $id_event );
		
		die(json_encode($return[0]));

	}

	/**
	 * Remove Booking
	 * 
	 * @return void
	 */
	function remove_booking() 
	{

		header("HTTP/1.1 200 OK");

		global $current_user, $wpdb, $pec_init;
		
	    $nonce = $pec_init->post( 'postEventsNonce' );
		//if ( ! wp_verify_nonce( $nonce, 'ajax-get-events-nonce' ) )
	    //    die ( 'Busted!');
		
		if( ! is_user_logged_in() )
			die();	
		
		if( ! is_numeric( $pec_init->post( 'booking_id' ) ) ) die();
		
		$booking_id = $pec_init->post( 'booking_id' );

		// Action Hook
		do_action( 'pec_booking_removed', $booking_id );

		$wpdb->delete( DP_PRO_EVENT_CALENDAR_TABLE_BOOKING, array( 'id' => $booking_id ) );

	}

	/**
	 * Get Booking Form
	 * 
	 * @return void
	 */
	function book_event_form() 
	{
		header("HTTP/1.1 200 OK");
		global $pec_init;
		
		if( ! is_numeric( $pec_init->post( 'event_id' ) ) ) die();	
		
		$event_id = stripslashes( $pec_init->post( 'event_id' ) );
		$calendar = stripslashes( $pec_init->post( 'calendar' ) );
		$date = stripslashes( $pec_init->post( 'date' ) );

		$opts = array();
		$opts['id_calendar'] = $calendar;
		$dpProEventCalendar_class = $pec_init->init_base( $opts );
		
		echo $dpProEventCalendar_class->getBookingForm( $event_id, $date );
		
		die();	
	}

	/**
	 * Get Edit Form
	 * 
	 * @return void
	 */
	function edit_event_form() 
	{
		header("HTTP/1.1 200 OK");
		global $pec_init;
		
		if( ! is_user_logged_in() || ! is_numeric( $pec_init->post( 'event_id' ) ) ) die();	
		
		$event_id = stripslashes( $pec_init->post( 'event_id' ) );
		$id_calendar = get_post_meta( $event_id, 'pec_id_calendar', true );
		$id_calendar = explode( ',', $id_calendar );
		$id_calendar = $id_calendar[0];

		$opts = array();
		$opts['id_calendar'] = $id_calendar;
		$dpProEventCalendar_class = $pec_init->init_base( $opts );
		
		$eventForm = new DPPEC_EventForm($event_id, true);
		echo $eventForm->display_form();
		
		die();	
	}

	/**
	 * New Event Form
	 * 
	 * @return void
	 */
	function new_event_form() 
	{

		header( "HTTP/1.1 200 OK" );
		global $pec_init;
		
		if( ! is_numeric( $pec_init->post( 'calendar' ) ) ) die();	
		
		$id_calendar = stripslashes( $pec_init->post( 'calendar' ) );
		$time = '';
		if( is_numeric( $pec_init->post( 'time' ) ) )
			$time = $pec_init->post( 'time' );
		
		$opts = array();
		$opts['id_calendar'] = $id_calendar;
		$dpProEventCalendar_class = $pec_init->init_base( $opts );
		
		$eventForm = new DPPEC_EventForm( "", true, $time );
		die( $eventForm->display_form() );	

	}

	/**
	 * Set Special Dates from admin
	 * 
	 * @return void
	 */
	function set_special_dates() 
	{

		header("HTTP/1.1 200 OK");
		global $pec_init;

	    $nonce = $pec_init->post( 'postEventsNonce' );
		//if ( ! wp_verify_nonce( $nonce, 'ajax-get-events-nonce' ) )
	    //    die ( 'Busted!');
			
		if( ! is_numeric( $pec_init->post( 'calendar' ) ) || empty( $pec_init->post( 'date' ) ) ) die();
		

		$calendar = $pec_init->post( 'calendar' );
		$sp = $pec_init->post( 'sp' );
		$date = $pec_init->post( 'date' );
		
		$opts = array();
		$opts['id_calendar'] = $calendar;
		$dpProEventCalendar_class = $pec_init->init_base( $opts );
		
		$dpProEventCalendar_class->setSpecialDates( $sp, $date );
		
		die();

	}

	/**
	 * Get Mailchimp List
	 * 
	 * @return void
	 */
	function pec_get_mclist() 
	{

		global $pec_init;

		if( $pec_init->post( 'mailchimp_api' ) == "" )
			die();
		
		$api_key = $pec_init->post( 'mailchimp_api' );
		$return = '';

		$data = array(
			'fields' => 'lists' // total_items, _links
		);
		 
		$url = 'https://' . substr( $api_key, strpos( $api_key, '-' ) + 1 ) . '.api.mailchimp.com/3.0/lists/';
		$result = json_decode( dpProEventCalendar_mailchimp_curl_connect( $url, 'GET', $api_key, $data) );
		 
		if( ! empty($result->lists) ) 
		{
		
			$return .= '<select name="mailchimp_list">';
			foreach( $result->lists as $list ) {
		
				$return .= '<option value="' . $list->id . '">' . $list->name . '</option>';
		
			}	

			$return .= '</select>';

		} else {
			$return = "Error: No lists found";
		}
		
		die($return);

	}

	/**
	 * Create a booking from admin
	 * 
	 * @return void
	 */
	function book_event_admin() 
	{

		global $wpdb, $pec_init;
		
		if( ! is_numeric( $pec_init->post( 'eventid' ) ) ) die();

		$eventid = $pec_init->post( 'eventid' );
		$userid = $pec_init->post( 'userid' );
		$phone = $pec_init->post( 'phone' );
		$quantity = $pec_init->post( 'quantity' );
		$status = $pec_init->post( 'status' );
		$event_date = $pec_init->post( 'date' );
		$code = dpProeventCalendar_generate_code( 20 );
		$comment = '';

		$wpdb->insert( 
			DP_PRO_EVENT_CALENDAR_TABLE_BOOKING, 
			array( 
				'booking_date' 	=> date('Y-m-d H:i:s'),
				'event_date'	=> $event_date,
				'id_event'		=> $eventid,
				'id_user'		=> $userid,
				//'id_coupon'		=> $id_coupon,
				//'coupon_discount'=> $coupon_discount,
				'comment'		=> $comment,
				'quantity'		=> $quantity,
				//'name'			=> $name,
				'phone'			=> $phone,
				//'email'			=> $email,
				//'extra_fields'	=> $extra_fields,
				'status'		=> $status,
				'code'			=> $code
			), 
			array( 
				'%s',
				'%s',
				'%d',
				'%d',
				//'%d',
				//'%s',
				'%s',
				'%d',
				//'%s',
				'%s',
				//'%s',
				//'%s',
				'%s',
				'%s'
			) 
		);

		$id_booking = $wpdb->insert_id;

		$this->get_more_bookings( 1, 0, $eventid );
		die();

	}

	/**
	 * Get More Bookings
	 * 
	 * @return void
	 */
	function get_more_bookings( $limit = 30, $offset = '', $eventid = '', $event_date = '' ) 
	{

		global $wpdb, $dpProEventCalendar, $pec_init;
		
		$eventid = $pec_init->post( 'eventid' );
		
		$offset = $pec_init->post( 'offset' );
		
		if( ! is_numeric( $limit ) ) 
			$limit = 30;
		
		if( empty( $event_date ) ) 
			$event_date = $pec_init->post( 'event_date' );
		
		
		$id_list = $eventid;
	    if( function_exists( 'icl_object_id' ) ) 
	    {
	    
	        global $sitepress;

	        $id_list_arr = array();
			$trid = $sitepress->get_element_trid( $eventid, 'post_pec-events' );
			$translation = $sitepress->get_element_translations( $trid, 'post_pec-events' );

			foreach( $translation as $key ) 
			{
				$id_list_arr[] = $key->element_id;
			}

			if( ! empty( $id_list_arr ) ) 
				$id_list = implode( ",", $id_list_arr );
		
		}

		$querystr = "
	    SELECT COUNT(*) as count
	    FROM " . DP_PRO_EVENT_CALENDAR_TABLE_BOOKING . "
		WHERE id_event IN (" . $id_list . ")
		";
		if($event_date != "") {
			$querystr .= "
			AND event_date = '" . $event_date . "'";
		}
	    $counter = $wpdb->get_row( $querystr, OBJECT );

		$querystr = "
		SELECT *
		FROM " . DP_PRO_EVENT_CALENDAR_TABLE_BOOKING . "
		WHERE id_event IN (" . $id_list . ")
		";
		
		if( $event_date != "" ) 
		{
			$querystr .= "
			AND event_date = '" . $event_date . "'";
		}
		
		$querystr .= "
		ORDER BY id DESC
		LIMIT " . $offset . ", " . $limit . "
		";
		$bookings_obj = $wpdb->get_results( $querystr, OBJECT );

		ob_start();
		foreach( $bookings_obj as $booking ) 
		{
		
			if(is_numeric($booking->id_user) && $booking->id_user > 0) {
				$userdata = get_userdata($booking->id_user);
			} else {
				$userdata = new stdClass();
				$userdata->display_name = $booking->name;
				$userdata->user_email = $booking->email;	
			}
			?>
		<tr>
			<td width="200"><?php echo $userdata->display_name?> <br>
	        	<?php if($userdata->user_email != "") {?>
	        	<span class="dashicons dashicons-email-alt"></span><input type="text" readonly="readonly" name="pec_booking_email[<?php echo $booking->id?>]" class="pec_booking_text" value="<?php echo $userdata->user_email?>" /><br>
	        	<?php }?>

	        	<?php if($booking->phone != "") {?>
	        	<span class="dashicons dashicons-phone"></span><input type="text" readonly="readonly" name="pec_booking_phone[<?php echo $booking->id?>]"  class="pec_booking_text" value="<?php echo $booking->phone?>" />
	        	<?php }?>
	        </td>
	        <td><?php echo date_i18n(get_option('date_format') . ' '. get_option('time_format'), strtotime($booking->booking_date))?></td>
	        <td><?php echo date_i18n(get_option('date_format'), strtotime($booking->event_date))?></td>
	        <td><?php echo $booking->quantity?></td>
	        <td>
	        	<?php if( $booking->comment != "" ) {?>
	        	<span class="dashicons dashicons-admin-comments"></span> <?php echo nl2br($booking->comment)?> <hr>
	        	<?php }?>

	        	<?php
	        	$extra_fields = unserialize( $booking->extra_fields );
	        	if( ! is_array( $extra_fields ) ) 
	        		$extra_fields = array();

	        	$html = '';

	        	foreach( $extra_fields as $key=>$value ) 
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
	        			
						$html .= '<div class="pec_event_page_custom_fields">
									<strong>'.$dpProEventCalendar['booking_custom_fields']['name'][$field_index].': </strong>'.$value;
						$html .= '</div>';
					}

	        	}
	        	
	        	echo $html;
				?>
			</td>
	        <td>
	        	<select name="pec_booking_status[<?php echo $booking->id?>]">
					<option value="<?php echo DP_PRO_EVENT_CALENDAR_BOOKING_COMPLETED; ?>"><?php echo __( 'Completed', 'dpProEventCalendar' )?></option>
	                <option value="<?php echo DP_PRO_EVENT_CALENDAR_BOOKING_PENDING; ?>" <?php if($booking->status == DP_PRO_EVENT_CALENDAR_BOOKING_PENDING) {?> selected="selected" <?php }?>><?php echo __( 'Pending', 'dpProEventCalendar' )?></option>
	                <option value="<?php echo DP_PRO_EVENT_CALENDAR_BOOKING_CANCELED_BY_USER; ?>" <?php if( $booking->status == DP_PRO_EVENT_CALENDAR_BOOKING_CANCELED_BY_USER ) {?> selected="selected" <?php }?>><?php echo __( 'Canceled By User', 'dpProEventCalendar' )?></option>
	                <option value="<?php echo DP_PRO_EVENT_CALENDAR_BOOKING_CANCELED; ?>" <?php if($booking->status == DP_PRO_EVENT_CALENDAR_BOOKING_CANCELED) {?> selected="selected" <?php }?>><?php echo __( 'Canceled', 'dpProEventCalendar' )?></option>
	            </select>
	        </td>

			<td><input type="button" value="<?php echo __( 'Delete', 'dpProEventCalendar' )?>" name="delete_booking" class="button-primary" onclick="if(confirm('<?php echo __( 'Are you sure that you want to remove this booking?', 'dpProEventCalendar' )?>')) { pec_removeBooking(<?php echo $booking->id?>, this); }" /></td>
		</tr>
		<?php 
		}

		$page = ob_get_contents();
		ob_end_clean();
		$page = preg_replace( '/\s+/', ' ', trim( $page ) );


		$encode = json_encode( array( "html" => $page, "counter" => $counter->count ) );

		print_r( $encode );
		die();
				
	}

}

?>