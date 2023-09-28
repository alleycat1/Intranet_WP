<?php
/*
 * DP Pro Event Calendar
 *
 * Copyright 2012, Diego Pereyra
 *
 * @Web: https://www.wpsleek.com
 * @Email: dpereyra90@gmail.com
 *
 * Base Class
 */

class DpProEventCalendar {
	
	public static $nonce;
	
	private static $is_admin = false;
	
	var $type = 'calendar';

	var $carousel_default_columns = 3;

	public static $special_dates_list = null;

	public static $special_date = array();
	
	public static $limit = 0;
	
	public static $limit_description = 0;
	
	public static $category = "";
	
	protected static $event_id = "";
	
	protected static $event = "";
	
	protected static $author = "";
	
	protected static $columns = 3;
	
	var $from = "";
	
	var $view = "";
	
	protected static $id_calendar = null;
	
	var $default_date = null;
	
	public static $calendar_obj;
	
	var $wpdb = null;
	
	var $eventsByCurrDate = null;
	
	public static $opts = array();
	
	var $hidden_editor_added = false;
	
	protected static $loaded_event = array();

	public static $datesObj;
	

	private $eventForm;


	private $sliderLayout;
	private $carouselLayout;
	private $cardLayout;
	private $countdownLayout;
	private $timelineLayout;
	private $yearlyLayout;
	private $gmapLayout;
	private $gridLayout;
	private $coverLayout;
	private $eventModal;
	private $bookingsUserLayout;


	public static $notifications;


	var $widget;

	protected static $time_format;
	protected static $init_id;
	
	public static $translation;
	public static $translation_orig;

	protected static $current_event;
	
	function __construct( 
		
		$opts = array() 
		
		) 
	{

		self::$opts = $opts;
		
		$is_admin = self::get_init_opts( 'is_admin', false );
		
		$id_calendar = self::get_init_opts( 'id_calendar', null );
		
		$defaultDate = self::get_init_opts( 'defaultDate', null );
		
		$translation = self::get_init_opts( 'translation', null );
		
		$widget = self::get_init_opts( 'widget' );
		
		$category = self::get_init_opts( 'category' );
		
		$event_id = self::get_init_opts( 'event_id' );
		
		$author = self::get_init_opts( 'author' );
		
		$event = self::get_init_opts( 'event' );
		
		$columns = self::get_init_opts( 'columns' );
		
		$from = self::get_init_opts( 'from' );
		
		$view = self::get_init_opts( 'view' );
		
		$limit_description = self::get_init_opts( 'limit_description', 0 );


		self::$translation = array( 
			'TXT_NO_EVENTS_FOUND' 		=> __('No Events were found.','dpProEventCalendar'),
			'TXT_EVENTS_FOUND' 			=> __('event(s) found.','dpProEventCalendar'),
			'TXT_ALL_DAY' 				=> __('All Day','dpProEventCalendar'),
			'TXT_ALL_EVENT_DATES' 		=> __('All Event Dates','dpProEventCalendar'),
			'TXT_COLOR_CODE' 			=> __('Color Code','dpProEventCalendar'),
			'TXT_LIST_VIEW'				=> __('List View','dpProEventCalendar'),
			'TXT_CALENDAR_VIEW'			=> __('Calendar View','dpProEventCalendar'),
			'TXT_ALL_CATEGORIES'		=> __('All Categories','dpProEventCalendar'),
			'TXT_ALL_LOCATIONS'			=> __('All Locations','dpProEventCalendar'),
			'TXT_ALL_SPEAKERS'			=> __('All Speakers','dpProEventCalendar'),
			'TXT_MONTHLY'				=> __('Monthly','dpProEventCalendar'),
			'TXT_DAILY'					=> __('Daily','dpProEventCalendar'),
			'TXT_WEEKLY'				=> __('Weekly','dpProEventCalendar'),
			'TXT_ALL_WORKING_DAYS'		=> __('All working days','dpProEventCalendar'),
			'TXT_SEARCH' 				=> __('Type and hit enter...','dpProEventCalendar'),
			'TXT_RESULTS_FOR' 			=> __('Results: ','dpProEventCalendar'),
			'TXT_VISIT_WEBSITE'			=> __('Visit Website', 'dpProEventCalendar'),
			'TXT_TO_BE_CONFIRMED'		=> __('To Be Confirmed','dpProEventCalendar'),
			'TXT_FULLY_BOOKED'			=> __('Fully Booked','dpProEventCalendar'),
			'TXT_AGE'					=> __('Age', 'dpProEventCalendar'),
			'TXT_AGE_RANGE'				=> __('Age Range', 'dpProEventCalendar'),
			'TXT_DATES'					=> __('Dates','dpProEventCalendar'),
			'TXT_MORE_DATES'			=> __('More Dates','dpProEventCalendar'),
			'TXT_STARTS_IN'				=> __('Starts in','dpProEventCalendar'),
			'TXT_ADD_TO_PERSONAL_CALENDAR'=> __('Add to personal calendar','dpProEventCalendar'),
			'TXT_ORGANIZED_BY' 			=> __('Organized By','dpProEventCalendar'),
			'TXT_ORGANIZER' 			=> __('Organizer','dpProEventCalendar'),
			'TXT_SPEAKER' 				=> __('Speaker','dpProEventCalendar'),
			'TXT_SPEAKERS' 				=> __('Speakers','dpProEventCalendar'),
			'TXT_BY' 					=> __('By','dpProEventCalendar'),
			'TXT_AUTHOR' 				=> __('Author','dpProEventCalendar'),
			'TXT_PHONE' 				=> __('Phone','dpProEventCalendar'),
			'TXT_VENUE'					=> __('Venue', 'dpProEventCalendar'),
			'TXT_OPEN_MAP'				=> __('Open Map', 'dpProEventCalendar'),
			'TXT_VISIT_FB_EVENT'		=> __('Visit Facebook Event','dpProEventCalendar'),
			'TXT_REMAINING' 			=> __('Remaining','dpProEventCalendar'),
			'TXT_YEAR' 					=> __('Year','dpProEventCalendar'),
			'TXT_YEARS'					=> __('Years','dpProEventCalendar'),
			'TXT_MONTH' 				=> __('Month','dpProEventCalendar'),
			'TXT_MONTHS' 				=> __('Months','dpProEventCalendar'),
			'TXT_PRINT'					=> __('Print','dpProEventCalendar'),
			'TXT_DAYS' 					=> __('Days','dpProEventCalendar'),
			'TXT_DAY' 					=> __('Day','dpProEventCalendar'),
			'TXT_HOURS' 				=> __('Hours','dpProEventCalendar'),
			'TXT_HOUR' 					=> __('Hour','dpProEventCalendar'),
			'TXT_MINUTES' 				=> __('Minutes','dpProEventCalendar'),
			'TXT_MINUTE' 				=> __('Minute','dpProEventCalendar'),
			'TXT_SECONDS' 				=> __('Seconds','dpProEventCalendar'),
			'TXT_FEATURED' 				=> __('Featured','dpProEventCalendar'),
			'TXT_CURRENT_DATE'			=> __('Current Date','dpProEventCalendar'),
			'TXT_TODAY'					=> __('Today','dpProEventCalendar'),
			'TXT_SELECT_TIMEZONE'		=> __('Select Timezone', 'dpProEventCalendar'),
			'TXT_BOOKINGS'				=> __('Bookings','dpProEventCalendar'),
			'TXT_BOOK_EVENT'			=> __('Book Event','dpProEventCalendar'),
			'TXT_BOOKED'				=> __('Booked','dpProEventCalendar'),
			'TXT_BOOK_EVENT_REMOVE'		=> __('Remove Booking','dpProEventCalendar'),
			'TXT_BOOK_EVENT_SAVED'		=> __('Booking saved successfully.','dpProEventCalendar'),
			'TXT_BOOK_EVENT_REMOVED'	=> __('Booking removed successfully.','dpProEventCalendar'),
			'TXT_BOOK_EVENT_SELECT_DATE'=> __('Select Date:','dpProEventCalendar'),
			'TXT_BOOK_EVENT_PICK_DATE'	=> __('Click to book on this date.','dpProEventCalendar'),
			'TXT_BOOK_TICKETS_REMAINING'=> __('Tickets Remaining','dpProEventCalendar'),
			'TXT_BOOK_ALREADY_BOOKED'	=> __('You have already booked this event date.','dpProEventCalendar'),
			'TXT_BOOK_EVENT_COMMENT'	=> __('Leave a comment (optional)','dpProEventCalendar'),
			'TXT_CATEGORY'				=> __('Category','dpProEventCalendar'),
			'TXT_SUBSCRIBE'				=> __('Subscribe','dpProEventCalendar'),
			'TXT_SUBSCRIBE_SUBTITLE'	=> __('Receive new events notifications in your email.','dpProEventCalendar'),
			'TXT_YOUR_NAME'				=> __('Your Name','dpProEventCalendar'),
			'TXT_YOUR_EMAIL'			=> __('Your Email','dpProEventCalendar'),
			'TXT_FIELDS_REQUIRED'		=> __('All fields are required.','dpProEventCalendar'),
			'TXT_FIELD_REQUIRED'		=> __('This field is required.','dpProEventCalendar'),
			'TXT_INVALID_EMAIL'			=> __('The Email is invalid.','dpProEventCalendar'),
			'TXT_SUBSCRIBE_THANKS'		=> __('Thanks for subscribing.','dpProEventCalendar'),
			'TXT_SENDING'				=> __('Sending...','dpProEventCalendar'),
			'TXT_SEND'					=> __('Send','dpProEventCalendar'),
			'TXT_CLOSE'					=> __('Close','dpProEventCalendar'),
			'TXT_EMAIL'					=> __('Email','dpProEventCalendar'),
			'TXT_ADD_EVENT'				=> __('New Event','dpProEventCalendar'),
			'TXT_EDIT_EVENT'			=> __('Edit Event','dpProEventCalendar'),
			'TXT_REMOVE_EVENT'			=> __('Remove Event','dpProEventCalendar'),
			'TXT_REMOVE_EVENT_CONFIRM'	=> __('Are you sure that you want to delete this event?','dpProEventCalendar'),
			'TXT_CANCEL_BOOKING_CONFIRM'=> __('Are you sure that you want to cancel this booking?','dpProEventCalendar'),
			'TXT_CANCEL'				=> __('Cancel','dpProEventCalendar'),
			'TXT_CANCEL_BOOKING'		=> __('Cancel Booking','dpProEventCalendar'),
			'TXT_COMPLETED'				=> __('Completed','dpProEventCalendar'),
			'TXT_PENDING'				=> __('Pending','dpProEventCalendar'),
			'TXT_CANCELED_BY_USER'		=> __('Canceled by user','dpProEventCalendar'),
			'TXT_CANCELED'				=> __('Cancelled','dpProEventCalendar'),
			'TXT_POSTPONED'				=> __('Postponed','dpProEventCalendar'),
			'TXT_YES'					=> __('Yes','dpProEventCalendar'),
			'TXT_NO'					=> __('No','dpProEventCalendar'),
			'TXT_EVENT_LOGIN'			=> __('You must be logged in to submit an event.','dpProEventCalendar'),
			'TXT_EVENT_THANKS'			=> __('Thanks for your event submission. It will be reviewed soon.','dpProEventCalendar'),
			'TXT_EVENT_THANKS_EDIT'		=> __('Event edited successfully.','dpProEventCalendar'),
			'TXT_EVENT_TITLE'			=> __('Title','dpProEventCalendar'),
			'TXT_EVENT_ADD_A_TITLE'		=> __('Add a Title','dpProEventCalendar'),
			'TXT_EVENT_ADD_DESCRIPTION'	=> __('Add a Description','dpProEventCalendar'),
			'TXT_EVENT_DESCRIPTION'		=> __('Event Description','dpProEventCalendar'),
			'TXT_EVENT_IMAGE'			=> __('Upload an Image (optional)','dpProEventCalendar'),
			'TXT_EVENT_LINK'			=> __('Link (optional)','dpProEventCalendar'),
			'TXT_EVENT_SHARE'			=> __('Text to share in social networks (optional)','dpProEventCalendar'),
			'TXT_EVENT_LOCATION'		=> __('Location (optional)','dpProEventCalendar'),
			'TXT_EXTRA_DATES'			=> __('Extra Dates (optional)','dpProEventCalendar'),
			'TXT_OTHER'					=> __('Other','dpProEventCalendar'),
			'TXT_EVENT_LOCATION_NAME'	=> __('Location Name','dpProEventCalendar'),
			'TXT_EVENT_ADDRESS'			=> __('Address','dpProEventCalendar'),
			'TXT_EVENT_PHONE'			=> __('Phone (optional)','dpProEventCalendar'),
			'TXT_EVENT_GOOGLEMAP'		=> __('Google Map (optional)','dpProEventCalendar'),
			'TXT_EVENT_START'			=> __('Start','dpProEventCalendar'),
			'TXT_EVENT_START_DATE'		=> __('Start Date','dpProEventCalendar'),
			'TXT_EVENT_ALL_DAY'			=> __('All day','dpProEventCalendar'),
			'TXT_EVENT_TIME'			=> __('Time','dpProEventCalendar'),
			'TXT_EVENT_START_TIME'		=> __('Start Time','dpProEventCalendar'),
			'TXT_EVENT_HIDE_TIME'		=> __('Hide Time','dpProEventCalendar'),
			'TXT_EVENT_END_TIME'		=> __('End Time','dpProEventCalendar'),
			'TXT_EVENT_FREQUENCY'		=> __('Frequency','dpProEventCalendar'),
			'TXT_NONE'					=> __('None','dpProEventCalendar'),
			'TXT_EVENT_DATE'			=> __('Date','dpProEventCalendar'),
			'TXT_EVENT_DAILY'			=> __('Daily','dpProEventCalendar'),
			'TXT_EVENT_WEEKLY'			=> __('Weekly','dpProEventCalendar'),
			'TXT_EVENT_MONTHLY'			=> __('Monthly','dpProEventCalendar'),
			'TXT_EVENT_YEARLY'			=> __('Yearly','dpProEventCalendar'),
			'TXT_EVENT_END'				=> __('End','dpProEventCalendar'),
			'TXT_EVENT_END_DATE'		=> __('End Date','dpProEventCalendar'),
			'TXT_MORE'					=> __('More', 'dpProEventCalendar'),
			'TXT_READ_MORE'				=> __('Read More', 'dpProEventCalendar'),
			'TXT_BACK'					=> __('Back', 'dpProEventCalendar'),
			'TXT_TO'					=> __('to', 'dpProEventCalendar'),
			'TXT_EVERY'					=> __('Every','dpProEventCalendar'),
			'TXT_REPEAT_EVERY'			=> __('Repeat every','dpProEventCalendar'),
			'TXT_SUBMIT_FOR_REVIEW'		=> __('Submit for Review','dpProEventCalendar'),
			'TXT_SUBMIT'				=> __('Submit','dpProEventCalendar'),
			'TXT_NEXT'					=> __('Next','dpProEventCalendar'),
			'TXT_WEEKS_ON'				=> __('week(s) on:','dpProEventCalendar'),
			'TXT_MONTHS_ON'				=> __('month(s) on:','dpProEventCalendar'),
			'TXT_RECURRING_OPTION'		=> __('Recurring Option','dpProEventCalendar'),
			'TXT_FIRST'					=> __('First','dpProEventCalendar'),
			'TXT_SECOND'				=> __('Second','dpProEventCalendar'),
			'TXT_THIRD'					=> __('Third','dpProEventCalendar'),
			'TXT_FOURTH'				=> __('Fourth','dpProEventCalendar'),
			'TXT_LAST'					=> __('Last','dpProEventCalendar'),
			'TXT_ALLOW_BOOKINGS'		=> __('Allow Bookings?', 'dpProEventCalendar'),
			'TXT_PRICE'					=> __('Price', 'dpProEventCalendar'),
			'TXT_BOOKING_LIMIT'			=> __('Booking Limit', 'dpProEventCalendar'),
			'TXT_BOOKING_BLOCK_HOURS'	=> __('Block Hours', 'dpProEventCalendar'),
			'TXT_SELECT_COLOR'			=> __('Select a color', 'dpProEventCalendar'),
			'TXT_QUANTITY'				=> __('Quantity', 'dpProEventCalendar'),
			'TXT_ATTENDEE'				=> __('Attendee', 'dpProEventCalendar'),
			'TXT_ATTENDEES'				=> __('Attendees', 'dpProEventCalendar'),
			'TXT_YOUR_PHONE'			=> __('Your Phone', 'dpProEventCalendar'),
			'TXT_DRAG_MARKER'			=> __('Drag the marker to set a specific position', 'dpProEventCalendar'),
			'TXT_MON'					=> __('Mon','dpProEventCalendar'),
			'TXT_TUE'					=> __('Tue','dpProEventCalendar'),
			'TXT_WED'					=> __('Wed','dpProEventCalendar'),
			'TXT_THU'					=> __('Thu','dpProEventCalendar'),
			'TXT_FRI'					=> __('Fri','dpProEventCalendar'),
			'TXT_SAT'					=> __('Sat','dpProEventCalendar'),
			'TXT_SUN'					=> __('Sun','dpProEventCalendar'),
			'PREV_MONTH' 				=> __('Prev Month','dpProEventCalendar'),
			'NEXT_MONTH'				=> __('Next Month','dpProEventCalendar'),
			'PREV_DAY' 					=> __('Prev Day','dpProEventCalendar'),
			'NEXT_DAY'					=> __('Next Day','dpProEventCalendar'),
			'PREV_WEEK'					=> __('Prev Week','dpProEventCalendar'),
			'NEXT_WEEK'					=> __('Next Week','dpProEventCalendar'),
			'DAY_SUNDAY' 				=> __('Sunday','dpProEventCalendar'),
			'DAY_MONDAY' 				=> __('Monday','dpProEventCalendar'),
			'DAY_TUESDAY' 				=> __('Tuesday','dpProEventCalendar'),
			'DAY_WEDNESDAY' 			=> __('Wednesday','dpProEventCalendar'),
			'DAY_THURSDAY' 				=> __('Thursday','dpProEventCalendar'),
			'DAY_FRIDAY' 				=> __('Friday','dpProEventCalendar'),
			'DAY_SATURDAY' 				=> __('Saturday','dpProEventCalendar'),
			'MONTHS' 					=> array(
											__('January','dpProEventCalendar'),
											__('February','dpProEventCalendar'),
											__('March','dpProEventCalendar'),
											__('April','dpProEventCalendar'),
											__('May','dpProEventCalendar'),
											__('June','dpProEventCalendar'),
											__('July','dpProEventCalendar'),
											__('August','dpProEventCalendar'),
											__('September','dpProEventCalendar'),
											__('October','dpProEventCalendar'),
											__('November','dpProEventCalendar'),
											__('December','dpProEventCalendar')
										)
	   );
	   
	   self::$translation_orig = self::$translation;


		$this->widget = $widget;
		
		if( $is_admin ) self::$is_admin = true;
		
		if( $view != "" ) $this->view = $view;

		if( is_numeric( $id_calendar ) ) $this->setCalendar( $id_calendar ); else $this->set_empty_calendar();
		
		if( ! isset( $defaultDate ) ) $defaultDate = $this->getDefaultDate();
		
		$this->defaultDate = $defaultDate;
		
		if( isset( $translation ) ) self::$translation = $translation;
		
		if( isset( $category ) ) self::$category = $category;
		
		if( isset( $event_id ) ) self::$event_id = $event_id;
		
		if( isset( $event ) ) self::$event = $event;
		
		if( is_numeric( $columns ) ? self::$columns = $columns : self::$columns = 1 );
		
		if( isset( $from ) ) $this->from = $from;
		
		if( isset( $author ) ) self::$author = $author;
		
		if( isset( $limit_description ) ) self::$limit_description = $limit_description;
		
		$time_format = get_option( 'time_format' );
		
		if( $time_format == "" ) 
			$time_format = "H:i:s";

		self::$time_format = $time_format;
		
		self::$nonce = rand();

		self::$init_id = 'dp_pec_id' . self::$nonce;

		self::$notifications = new DPPEC_Notifications();
		
		self::$datesObj = new DPPEC_Dates($defaultDate);
		
    }

    public static function get_init_opts ( $field, $default = '' )
    {

    	return isset( self::$opts[$field] ) ? self::$opts[$field] : $default;

    }

    function set_empty_calendar() 
	{

		self::$calendar_obj = new stdClass(); 

		self::$calendar_obj->allow_user_add_event = '';
		self::$calendar_obj->current_date_color = '';
		self::$calendar_obj->hide_old_dates = '';
		self::$calendar_obj->first_day = '';
		self::$calendar_obj->skin = 'light';
		self::$calendar_obj->daily_weekly_layout = '';
		self::$calendar_obj->id = '';
		self::$calendar_obj->view = '';
		self::$calendar_obj->show_search = '';
		self::$calendar_obj->date_range_start = '';
		self::$calendar_obj->show_titles_monthly = '';
		self::$calendar_obj->date_range_end = '';

	}
	
	function setCalendar($id) 
	{
	
		self::$id_calendar = $id;	
		
		$this->getCalendarData();
		
		if( ! self::$calendar_obj->enable_wpml ) 
		{
			
			$translation_fields = unserialize( self::$calendar_obj->translation_fields );

			if( is_array( $translation_fields ) ) 
			{
				
				if( ! is_array( self::$translation ) ) 
					self::$translation = array();

				foreach ( $translation_fields as $key => $value ) 
				{

					self::$translation[strtoupper($key)] = $value;

				}
			
			}
			
	   }
	
	}

	public function get_translations( $orig = false )
	{

		if( $orig ) 
			return self::$translation_orig;
		else
			return self::$translation;

	}

	public function get_calendar( )
	{

		return self::$calendar_obj;

	}
	
	function getNonce() 
	{
	
		if( ! is_numeric( self::$id_calendar ) ) return false;
		
		return self::$nonce;

	}

	protected function is_widget()
	{

		return $this->widget;

	}
	
	function getDefaultDate() 
	{
	
		global $wpdb;
		
		if( ! is_numeric( self::$id_calendar ) ) return time();
		
		$default_date;

		$querystr = $wpdb->prepare( "SELECT default_date FROM " . DP_PRO_EVENT_CALENDAR_TABLE_CALENDARS . " WHERE id = %d", self::$id_calendar );
		
		$calendar_obj = $wpdb->get_results($querystr, OBJECT);
		$calendar_obj = $calendar_obj[0];	

		if( ! empty( $calendar_obj ) ) 
			foreach($calendar_obj as $key=>$value) { $$key = $value; }

		if( $default_date == "" || $default_date == "0000-00-00" ) { $default_date = current_time('timestamp'); } else { $default_date = strtotime( $default_date ); }

		return $default_date;
	
	}
	
	function getCalendarName () 
	{
	
		global $wpdb;
		
		if( ! is_numeric( self::$id_calendar ) ) return "";

		$querystr = $wpdb->prepare( "SELECT title FROM " . DP_PRO_EVENT_CALENDAR_TABLE_CALENDARS . " WHERE id = %d", self::$id_calendar );
		
		$calendar_obj = $wpdb->get_row( $querystr, OBJECT );
		$title = $calendar_obj->title;	
		
		
		return $title;
	
	}
	
	function getCalendarData () 
	{
	
		global $wpdb;
		
		if( ! is_numeric( self::$id_calendar ) ) return;

		$querystr = $wpdb->prepare( "SELECT * FROM " . DP_PRO_EVENT_CALENDAR_TABLE_CALENDARS . " WHERE id = %d", self::$id_calendar );
		
		$calendar_obj = $wpdb->get_results($querystr, OBJECT);
		$calendar_obj = $calendar_obj[0];	

		self::$calendar_obj = $calendar_obj;

		// wrong calendar id?
		if( empty( self::$calendar_obj ) ) 
			return;
		
		if( $this->view != "" ) 
		{
		

			if($this->view == 'weekly-schedule')
			{

				$this->view = 'weekly';
				self::$calendar_obj->daily_weekly_layout = 'schedule';

			}

			if($this->view == 'weekly-list')
			{

				$this->view = 'weekly';
				self::$calendar_obj->daily_weekly_layout = 'list';

			}

			self::$calendar_obj->view = $this->view;
		
		}

		if( isset( self::$calendar_obj->link ) && self::$calendar_obj->link != "" ) 
		{
		
			if( substr( self::$calendar_obj->link, 0, 4 ) != "http" && substr( self::$calendar_obj->link, 0, 4 ) != "mail" ) 
				self::$calendar_obj->link = 'http://'.self::$calendar_obj->link;

		}
			
	}

	public function get_event_id_by_code( $code )
	{

		if( is_numeric( $code ) ) 
			return $code;
		
		if( strlen( $code ) != 11 )
			return false;

		$args = array(
			'posts_per_page' 	=> 1, 
			'post_type'			=> DP_PRO_EVENT_CALENDAR_EVENT_POST_TYPE,
		    'meta_query' => array(
		        array(
		           'key' => 'pec_code',
		           'value' => $code,
		           'compare' => '=',
		        )
		    )
		);

		$post = get_posts( $args );

		if( ! empty( $post ) )
			return $post[0]->ID;
		else 
			return false;


	}

	protected function set_current_event( $event_data )
	{
		self::$current_event = $event_data; 
	}

	protected function get_current_event( $field = '' )
	{
		if( $field != '' )
			return self::$current_event->$field; 

		return self::$current_event; 
	}
	
	public function getCalendarByEvent( $event_id, $set_calendar = false ) 
	{

		if( ! is_numeric( $event_id ) )
		{
			$event_id = $this->get_event_id_by_code( $event_id );
			if( ! $event_id ) return false;
		}

		$id_calendar = get_post_meta( $event_id, 'pec_id_calendar', true );

		$id_calendar = explode( ',', $id_calendar );
		$id_calendar = $id_calendar[0];

		if( $id_calendar == "" ) { $calendar_id = false; } else { $calendar_id = $id_calendar; }

		if( $set_calendar )
		$this->setCalendar( $calendar_id );

		return $calendar_id;
	
	}
	
	public static function getEventData( $event_id, $filter = 'none' ) 
	{

		if(isset(self::$loaded_event[$event_id])) 
		{
		
			return self::$loaded_event[$event_id];
		
		} else {

			$event_obj = new stdClass;
			
			$event_obj->id = $event_id;

			$event_obj->color = self::get_event_color($event_id);
			
			$event_obj->title = get_the_title($event_id);
			
			$event_obj->description = get_post_field('post_content', $event_id);
			
			$event_obj->id_calendar = get_post_meta($event_id, 'pec_id_calendar', true);
			
			$event_obj->code = get_post_meta($event_id, 'pec_code', true);

			$event_obj->date = get_post_meta($event_id, 'pec_date', true);
			
			$event_obj->orig_date = $event_obj->date;
			
			$event_obj->featured_event = get_post_meta($event_id, 'pec_featured_event', true);
			
			$event_obj->all_day = get_post_meta($event_id, 'pec_all_day', true);
			
			$event_obj->tbc = get_post_meta($event_id, 'pec_tbc', true);

			$event_obj->status = get_post_meta($event_id, 'pec_status', true);
			
			$event_obj->pec_daily_working_days = get_post_meta($event_id, 'pec_daily_working_days', true);
			
			$event_obj->pec_daily_every = get_post_meta($event_id, 'pec_daily_every', true);
			
			$event_obj->pec_weekly_every = get_post_meta($event_id, 'pec_weekly_every', true);
			
			$event_obj->pec_weekly_day = get_post_meta($event_id, 'pec_weekly_day', true);
			
			$event_obj->pec_monthly_every = get_post_meta($event_id, 'pec_monthly_every', true);
			
			$event_obj->pec_monthly_position = get_post_meta($event_id, 'pec_monthly_position', true);
			
			$event_obj->pec_monthly_day = get_post_meta($event_id, 'pec_monthly_day', true);
			
			$event_obj->pec_exceptions = get_post_meta($event_id, 'pec_exceptions', true);
			
			$event_obj->pec_extra_dates = get_post_meta($event_id, 'pec_extra_dates', true);
			
			$event_obj->end_date = get_post_meta($event_id, 'pec_end_date', true);
			
			$event_obj->link = get_post_meta($event_id, 'pec_link', true);
			
			$event_obj->age_range = get_post_meta($event_id, 'pec_age_range', true);

			$event_obj->pec_fb_event = get_post_meta($event_id, 'pec_fb_event', true);

			$event_obj->video = get_post_meta($event_id, 'pec_video', true);
			
			$event_obj->map = get_post_meta($event_id, 'pec_map', true);
			
			$event_obj->end_time_hh = get_post_meta($event_id, 'pec_end_time_hh', true);
			
			$event_obj->end_time_mm = get_post_meta($event_id, 'pec_end_time_mm', true);
			
			$event_obj->hide_time = get_post_meta($event_id, 'pec_hide_time', true);
			
			$event_obj->organizer = get_post_meta($event_id, 'pec_organizer', true);

			$event_obj->speaker = get_post_meta($event_id, 'pec_speaker', true);
			
			$event_obj->location = get_post_meta($event_id, 'pec_location', true);
			
			$event_obj->location_id = get_post_meta($event_id, 'pec_location', true);
			
			if(is_numeric($event_obj->location)) 
			{
			
				$event_obj->map = get_post_meta($event_obj->location, 'pec_venue_map', true);
			
				$event_obj->location_address = get_post_meta($event_obj->location, 'pec_venue_address', true);
			
				$event_obj->location = get_the_title($event_obj->location);
			
			}
			
			$event_obj->phone = get_post_meta($event_id, 'pec_phone', true);
			
			$event_obj->recurring_frecuency = get_post_meta($event_id, 'pec_recurring_frecuency', true);

			self::$loaded_event[$event_id] = $event_obj;

			return $event_obj;

		}

	}

	function get_single_page ( $content )
	{

		global $post, $wp_query;

		$post_id = $post->ID;
		

		$event_data = self::getEventData( $post_id );

		self::$event_id = $event_data->id;
						
		$max_upcoming_dates = self::$calendar_obj->booking_max_upcoming_dates;
		
		$start = substr( $event_data->date, 0, 10 ) . " 00:00:00";


		$event_dates = self::upcomingCalendarLayout( true, 500, '', null, null, true, false, true, false, false, '', true, $start, true );

		$valid_dates = array();

		$upcoming_single_date = '';

		if( is_array( $event_dates ) ) 
		{
		
			foreach( $event_dates as $ev_date ) 
			{
			
				$curDate = substr( $ev_date->date, 0, 10 );
				
				if( $event_data->pec_exceptions != "" ) 
				{
				
					$exceptions = explode( ',', $event_data->pec_exceptions );
					
					if( $event_data->recurring_frecuency != "" && in_array( $curDate, $exceptions ) ) 
						continue;
				
				}

				if( $event_data->pec_daily_working_days && $event_data->recurring_frecuency == 1 && (date('w', strtotime($curDate)) == "0" || date('w', strtotime($curDate)) == "6") ) 
				
					continue;
				
				if(!$event_data->pec_daily_working_days && $event_data->recurring_frecuency == 1 && $event_data->pec_daily_every > 1 && 
					( ((strtotime($curDate) - strtotime(substr($event_data->orig_date,0,11))) / (60 * 60 * 24)) % $event_data->pec_daily_every != 0 )
				) 

					continue;
				
				if($event_data->recurring_frecuency == 2 && $event_data->pec_weekly_every > 1 && 
					( ((strtotime($curDate) - strtotime(substr($event_data->orig_date,0,11))) / (60 * 60 * 24)) % ($event_data->pec_weekly_every * 7) != 0 )
				) {
					//continue;
				}
				
				if($event_data->recurring_frecuency == 3 && $event_data->pec_monthly_every > 1 && 
					( !is_int (((date('m', strtotime($curDate)) - date('m', strtotime(substr($event_data->orig_date,0,11))))) / ($event_data->pec_monthly_every)) )
				)
					continue;
				
				$valid_dates[] = $ev_date->date;

				if($upcoming_single_date == '' && strtotime($ev_date->date) >= current_time('timestamp', true)) 

					$upcoming_single_date = $ev_date->date;

			}

		}

		if( isset( $wp_query->query_vars['event_date'] ) && is_numeric( $wp_query->query_vars['event_date'] ) && in_array( date('Y-m-d H:i:s', $wp_query->query_vars['event_date']), $valid_dates ) ) 
		{
		
			$event_data->date = date( 'Y-m-d H:i:s', $wp_query->query_vars['event_date'] );
		
		} else {

			if( is_array( $valid_dates ) && ! empty( $valid_dates ) ) 
			{

				if( $upcoming_single_date != '' )
				
					$event_data->date = $upcoming_single_date;

				else
			
					$event_data->date = $valid_dates[0];
			
			}
		
		}


		// Limit the dates
		if( is_array( $valid_dates ) && ! empty( $valid_dates ) ) 
		{
			$date_slice_num = 20;
			$pos_date = array_search( $event_data->date, $valid_dates);
			$slice_start = 0;
			$slice_end = null;
			if( ( $pos_date - $date_slice_num ) >= 0 ) 
				$slice_start = $pos_date - $date_slice_num;

			if( ( $pos_date + $date_slice_num ) <= count( $valid_dates ) ) 
				$slice_end = ($date_slice_num * 2) + 1;

			$valid_dates = array_slice( $valid_dates, $slice_start, $slice_end );
		}




		$content = '
			<div id="dp_pec_single_content">
			<div id="dp_pec_single_top">' .
			$this->getFormattedEventData( 'phone', $post_id, $event_data ).
			$this->getFormattedEventData( 'link', $post_id, $event_data ).
			$this->getFormattedEventData( 'age_range', $post_id, $event_data ).
			$this->getFormattedEventData( 'attendees', $post_id, $event_data ).
			$this->getFormattedEventData( 'facebook_url', $post_id, $event_data ).
			$this->getFormattedEventData( 'custom_fields', $post_id, $event_data ).
			'</div>
			<div id="dp_pec_single_grid">' . 
			$this->getFormattedEventData( 'date', $post_id, $event_data, $valid_dates ).
			$this->getFormattedEventData( 'location', $post_id, $event_data ).
			'<div class="dp_pec_clear"></div>'.
			$this->getFormattedEventData( 'organizer', $post_id, $event_data ).
			$this->getFormattedEventData( 'speaker', $post_id, $event_data ).
			$this->getFormattedEventData( 'video', $post_id, $event_data ).
			'</div>' . 
			'<div class="dp_pec_clear"></div>'.
			$content.
			$this->getFormattedEventData( 'map', $post_id, $event_data ).'
			</div>';


		return $content;

	}
	
	function getFormattedEventData( $get = "", $post_id = "", $event_data = "", $valid_dates = array() ) 
	{
	
		global $post;

		$return = "";
		
		switch($get) 
		{
		
			case 'location':

				if( $event_data->location != "" ) 
				{
				
					$address = "";
					$phone = "";
					
					if( is_numeric( $event_data->location_id ) ) 
					{
					
						$address = get_post_meta($event_data->location_id, 'pec_venue_address', true);
						$phone = get_post_meta($event_data->location_id, 'pec_venue_phone', true);
					
					}
					
					$return = '<div class="pec_event_page_location dp_pec_single_item">';
					
					$return .= '<p class="pec_event_page_venue_lbl">' . self::$translation['TXT_VENUE'] . '</p>';

					$return .= '<p>' . $event_data->location . '</p>';

					if( $address != "" || $phone != "" ) 
					{

						$return .= '<hr />';

						if($address != "") 
						
							$return .= '<p class="pec_event_page_sub_p">' . $address . '</p>';
						
						if($phone != "") 
						
							$return .= '<p class="pec-location-phone">' . $phone . '</p>';

					}

					$return .= '</div>';

				}

				break;
			
			case 'phone':
			
				if( $event_data->phone == "" ) 
					break;

				$return = '<div class="pec_event_page_phone">';
				$return .= '<div class="pec_event_page_circle_icon"><i class="fa fa-phone"></i></div>';
				$return .= '<p>' . $event_data->phone . '</p>';
				$return .= '</div>';
				
				break;
			
			case 'video':

				if( $event_data->video == "" )
					break;

				$return = '<div class="pec_event_page_video dp_pec_single_item">';
				$return .= $this->convert_youtube( $event_data->video );
				$return .= '</div>';

				break;
			
			case 'facebook_url':
				
				if( $event_data->pec_fb_event == "" ) 
					break;
				
				$return = '<div class="pec_event_page_facebook_url">';
				$return .= '<div class="pec_event_page_circle_icon"><i class="fa fa-facebook"></i></div>';
				$return .= '<a href="' . $event_data->pec_fb_event . '" target="_blank">' . self::$translation['TXT_VISIT_FB_EVENT'] . '</a>';
				$return .= '</div>';
				
				break;

			case 'custom_fields':

				$return = self::display_custom_fields($post_id);

				break;

			case 'link':

				if( $event_data->link != "" ) 
				{
				
					$formated_link = str_replace( array( 'http://', 'https://' ), '', $event_data->link );

					if( strlen( $formated_link ) > 25 ) 
						$formated_link = substr( $formated_link, 0, 25 ) . '...';	
					
					if( substr( $event_data->link, 0, 4 ) != "http" && substr( $event_data->link, 0, 4 ) != "mail" ) 
						$event_data->link = 'http://' . $event_data->link;
					
					$return = '<div class="pec_event_page_link">';

					$return .= '<div class="pec_event_page_circle_icon"><i class="fa fa-link"></div></i>';

					$return .= '<a href="' . $event_data->link . '" target="_blank" rel="nofollow">' . $formated_link . '</a>';

					$return .= '</div>';
				
				}
				
				break;

			case 'organizer':

				if( $event_data->organizer != "" ) 
				{

					if( is_numeric( $event_data->organizer ) ) 
					{
					
						$organizer = get_the_title($event_data->organizer);

						$organizer_image_id = get_post_thumbnail_id( $event_data->organizer );
						$organizer_image = '';

						if( is_numeric( $organizer_image_id ) ) 
						
							$organizer_image = wp_get_attachment_image_src( $organizer_image_id, 'thumbnail' );

						$return = '<div class="pec_event_page_elem_v dp_pec_single_item">';
						
						$return .=	( is_array( $organizer_image ) ? '<div class="pec_event_page_elem_image" style="background-image:url(\'' . $organizer_image[0] . '\')"></div>' : '' );
						$return .=  '<p class="pec_event_page_elem_name">' . $organizer . '</p>';

						$return .=  '<hr />';

						$return .=  '<p class="pec-elem-lbl">' . self::$translation['TXT_ORGANIZER'] . '</p>';
						$return .=  '</div>';
					}
				
				}
				
				break;

			case 'speaker':

				if( $event_data->speaker != "" ) 
				{
				
					$speaker_list = explode( ',', $event_data->speaker );

					if( count( $speaker_list ) == 0 )
						return '';
							
					$return = '';

					if( is_array( $speaker_list ) ) 
					{

						foreach( $speaker_list as $speaker_id ) 
						{
							
							if( ! is_numeric( $speaker_id ) )
								continue;

							$speaker = get_the_title( $speaker_id );

							$speaker_image_id = get_post_thumbnail_id( $speaker_id );
							$speaker_image = '';

							if( is_numeric( $speaker_image_id ) ) 
								$speaker_image = wp_get_attachment_image_src( $speaker_image_id, 'thumbnail' );

							$return .= '<div class="pec_event_page_elem_v dp_pec_single_item">';
							$return .= 
								( is_array( $speaker_image ) ? '<div class="pec_event_page_elem_image" style="background-image:url(\'' . $speaker_image[0] . '\')"></div>' : '').
								
								'<p class="pec_event_page_elem_name">' . $speaker . '</p>';

							$return .= '<hr />';

							$return .= '<p class="pec-elem-lbl">' . self::$translation['TXT_SPEAKER'] . '</p>';

							$return .= '</div>';
								

						}
					}
				
				}
				
				break;
			
			case 'age_range':

				if( $event_data->age_range == "" ) 
					break;

				$return = '<div class="pec_event_page_gen_tag">'.
							'<strong>' . self::$translation['TXT_AGE'] . '</strong>'.
							'<p class="pec_event_page_sub_p">' . $event_data->age_range . '</p>'.
							'</div>';
				
				break;
			
			case 'categories':

				$category = get_the_terms( $post_id, 'pec_events_category' ); 
				$html = "";

				if( ! empty( $category ) ) 
				{
				
					$category_count = 0;
					$html .= '<div class="pec_event_page_categories">
						<p>';

					foreach ( $category as $cat)
					{
					
						if($category_count > 0) 
							$html .= " / ";	
						
						$html .= $cat->name;
						$category_count++;
					
					}
					
					$html .= '
						</p>
					</div>';

				}

				$return = $html;

				break;
			
			case 'frequency':

				if($event_data->recurring_frecuency != "") 
				{
				
					switch($event_data->recurring_frecuency) 
					{
					
						case 1:
							$return = self::$translation['TXT_EVENT_DAILY'];
							break;	
						case 2:
							$return = self::$translation['TXT_EVENT_WEEKLY'];
							break;	
						case 3:
							$return = self::$translation['TXT_EVENT_MONTHLY'];
							break;	
						case 4:
							$return = self::$translation['TXT_EVENT_YEARLY'];
							break;	
					
					}
				
				}
				
				break;
			
			case 'map':

				if( $event_data->map != "" || is_numeric( $event_data->location_id ) ) 
				{
					
					if( is_numeric( $event_data->location_id ) ) 
					{
					
						$event_data->map = get_post_meta($event_data->location_id, 'pec_venue_map_lnlat', true);
					
					} else {
					
						$event_data->map = get_post_meta($event_data->id, 'pec_map_lnlat', true);
					
					}

					$geocode = false;

					if( $event_data->map != "" ) 
					{
					
						$event_data->map = str_replace( " ", "", $event_data->map );
					
					} else {
					
						$geocode = true;
						if( is_numeric( $event_data->location_id ) ) 
						{
						
							$venue_address = get_post_meta( $event_data->location_id, 'pec_venue_address', true );
							if( $venue_address != "" ) 
							{
							
								$event_data->map = $venue_address;
							
							} else {
							
								$event_data->map = get_post_meta( $event_data->location_id, 'pec_venue_map', true );
							
							}
						
						} else {
						
							$event_data->map = get_post_meta( $event_data->id, 'pec_map', true );
						
						}
					
					}

					$map_id = $event_data->id . '_' . self::$nonce . '_' . rand();
					$return = self::get_map( $map_id, $event_data->map, $event_data->location_id, $geocode );

				}

				break;
			
			case 'rating':

				$rate = get_post_meta( $post_id, 'pec_rate', true );

				if( $rate != '' ) 
				{
				
					$return .= '
					<ul class="dp_pec_rate">
						<li><a href="#" '.($rate >= 1 ? 'class="dp_pec_rate_full"' : '').'></a></li>
						<li><a href="#" '.($rate >= 2 ? 'class="dp_pec_rate_full"' : '').' '.($rate > 1 && $rate < 2 ? 'class="dp_pec_rate_h"' : '').'></a></li>
						<li><a href="#" '.($rate >= 3 ? 'class="dp_pec_rate_full"' : '').' '.($rate > 2 && $rate < 3 ? 'class="dp_pec_rate_h"' : '').'></a></li>
						<li><a href="#" '.($rate >= 4 ? 'class="dp_pec_rate_full"' : '').' '.($rate > 3 && $rate < 4 ? 'class="dp_pec_rate_h"' : '').'></a></li>
						<li><a href="#" '.($rate >= 5 ? 'class="dp_pec_rate_full"' : '').' '.($rate > 4 && $rate < 5 ? 'class="dp_pec_rate_h"' : '').'></a></li>
					</ul>';
				
				}
				
				break;
			
			case 'date':
				
				$return = "";

				$return .= '<div class="pec_event_page_date dp_pec_single_item" style="' . ( $event_data->color != "" ? 'border-color:' . $event_data->color . ';' : '') . '">';

				// To Be Confirmed ?
				if( ! $event_data->tbc ) 
				{

					$time = self::date_i18n( self::$time_format, strtotime( $event_data->date ) );

					$end_datetime = self::get_end_datetime( $event_data );
					$end_date = $end_datetime['end_date'];
					$end_time = $end_datetime['end_time'];

					if( $event_data->all_day ) 
					{
					
						$time = self::$translation['TXT_ALL_DAY'];
						$end_time = "";
					
					}

					$status = self::get_status_label( $event_data->status );
					
					$all_working_days = '';
					if( $event_data->pec_daily_working_days && $event_data->recurring_frecuency == 1 )
						$all_working_days = self::$translation['TXT_ALL_WORKING_DAYS'];

					$event_timezone = dpProEventCalendar_getEventTimezone( $event_data->id );
					$event_time_line = $all_working_days . ' ' . (((self::$calendar_obj->show_time && ! $event_data->hide_time) || $event_data->all_day) ? $time.$end_time.(self::$calendar_obj->show_timezone && !$event_data->all_day && $status == '' ? ' ' . $event_timezone : '' ) : '' );

					$show_more_dates = false;

					if( is_array( $valid_dates ) && count( $valid_dates ) > 0 && count( $valid_dates ) !== 1 )
						$show_more_dates = true;
					
						
					if( strtotime( $event_data->date ) > current_time( 'timestamp', true ) && $status == '' ) 
					{
					
						$time_translate = array(
                            'year' => array(self::$translation['TXT_YEAR'], self::$translation['TXT_YEARS']),
                            'month' => array(self::$translation['TXT_MONTH'], self::$translation['TXT_MONTHS']),
                            'day' => array(self::$translation['TXT_DAY'], self::$translation['TXT_DAYS']),
                            'hour' => array(self::$translation['TXT_HOUR'], self::$translation['TXT_HOURS']),
                            'minute' => array(self::$translation['TXT_MINUTE'], self::$translation['TXT_MINUTES'])
                        );

                        $strtotime = strtotime( $event_data->date );

                        $return .= '<p class="pec_event_page_starts">' . self::$translation['TXT_STARTS_IN'] . '</p>';
					
						$return .= '<p class="pec_event_page_date_starts_in"> <span data-countdown-year="' . date('Y', $strtotime) . '" data-countdown-month="' . date('m', $strtotime) . '" data-countdown-day="' . date('d', $strtotime) . '" data-countdown-remaining="' . esc_attr__( self::$translation['TXT_REMAINING'] ) . '" data-countdown-hour="'.date('H', $strtotime).'" data-countdown-minute="' . date('i', $strtotime) . '" data-tzo="' . dpProEventCalendar_getEventTimezone($event_data->id, true) . '" class="pec_starts_in"></span></p>';

						$return .= '<hr />';
					
					}

					if( $status == '' )
					{
						
						$return .= '<p class="pec_event_page_startdate">' . self::date_i18n(get_option('date_format'), strtotime($event_data->date)) . $end_date . '</p>';
						$return .= '<p class="pec_event_page_time">' . $event_time_line . '</p>';

					} else 
						$return .= '<p class="pec_event_page_sub_p">' . $status . '</p>';

					$return .= '<div class="dp_pec_clear"></div>';

					//Get More Options
					$return .= self::get_more_options( $event_data );

					// Get Booking Button
					$booking_booked = self::getBookingBookedLabel( $event_data->id, $event_data->date );
					if( $booking_booked == "" ) 
						$return .= self::get_booking_button( $event_data->id, date('Y-m-d', strtotime($event_data->date) ), false, false, false );
					else
						$return .= $booking_booked;


					if( $show_more_dates && $status == '' ) 
					{
						
						$return .= '<div class="pec_event_page_action_wrap">';

						$return .= '<p class="pec_event_page_action" data-pec-tooltip="' . self::$translation['TXT_DATES'] . '"><i class="fa fa-plus"></i></p>';

						$return .= 			"<div class='pec_event_page_action_menu'>";

						

							$return .= 			"<ul>";
							$counter_valid_dates = 0;


							foreach($valid_dates as $key) 
							{
							
								//if($counter_valid_dates >= 10) 
								//	break;
								
								$return .= 			"<li".(substr($key, 0, 10) == substr($event_data->date, 0, 10) ? ' class="pec_event_page_action_menu_active"' : '')." data-pec-index='" . $counter_valid_dates . "'>";

								$return .= 				'<a href="' . self::get_permalink($event_data, $key) . '">' . self::date_i18n(get_option('date_format'), strtotime($key)) . '</a>';

								$return .= 			'</li>';

								$counter_valid_dates++;
							
							}

							$return .= 			"</ul>";
						
						$return .= 			"</div>";

						$return .= '</div> ';

					}


					$return .= '</div>';

				} else {
					
					$return .= '<p class="pec_event_page_date_time">' . self::$translation['TXT_TO_BE_CONFIRMED'] . '</p>';

					$return .= '</div>';

				}

				break;

			case 'author':

				if( self::$calendar_obj->show_author ) 
				{
					
					$author = get_userdata(get_post_field( 'post_author', $post_id ));
					$return = '<div class="pec_event_page_author">'.
					$return .= '<span class="pec_author">' . self::$translation['TXT_BY'] . ' ' . $author->display_name . '</span>';
					$return .= '</div>';
				
				}
				
				break;
			
			case 'attendees':

				if( get_post_meta( $post_id, 'pec_enable_booking', true ) || self::$calendar_obj->booking_enable ) 
				{

					if( self::$calendar_obj->booking_display_attendees ) 
					{
					
						$attendees_counter = self::getEventBookings(true, date('Y-m-d', strtotime($event_data->date)), $post_id);

						if( $attendees_counter == 0 ) break;

						$return .= '<div class="pec_event_page_attendees dp_pec_attendees_counter_' . $post_id . '"><div class="pec_event_page_circle_icon"><i class="fa fa-users"></i></div>'.
									'<p>'.$attendees_counter.' '.($attendees_counter == 1 ? self::$translation['TXT_ATTENDEE'] : self::$translation['TXT_ATTENDEES']) . '</p>';

						if ( self::$calendar_obj->booking_display_attendees_names && $attendees_counter > 0 ) 
						{

							$attendees_list = self::getEventBookings( false, date('Y-m-d', strtotime($event_data->date)), $post_id );

							$return .= "<div class='dp_pec_tooltip_list'>";

							$return .= "	<ul class='dp_pec_tooltip_list_ul'>";

							foreach( $attendees_list as $booking ) 
							{
							
								if( is_numeric( $booking->id_user ) && $booking->id_user > 0 ) 
								{

									$userdata = get_userdata($booking->id_user);
								
								} else {
								
									$userdata = new stdClass();
									$userdata->display_name = $booking->name;
									$userdata->user_email = $booking->email;	
								
								}

							
									$return .= "<li>".$userdata->display_name."</li>";

							}
							
							$return .= "	</ul>";

							$return .= "</div>";

						}

						$return .= "</div>";

					}

				}

				break;

			case 'time':
				
				$time = self::date_i18n(self::$time_format, strtotime($event_data->date));
													
				$end_datetime = self::get_end_datetime( $event_data, true );
				$end_time = $end_datetime['end_time'];

				if($event_data->all_day) 
				{
				
					$time = self::$translation['TXT_ALL_DAY'];
					$end_time = "";
				
				}

				$status = self::get_status_label( $event_data->status );
				if($status != '')
				{

					$time = $status;
					$end_time = "";

				}

				$event_timezone = dpProEventCalendar_getEventTimezone($event_data->id);

				$pec_time = ($all_working_days != '' ? $all_working_days.' ' : '').(((self::$calendar_obj->show_time && !$event_data->hide_time) || $event_data->all_day) ? $time.$end_time.(self::$calendar_obj->show_timezone && !$event_data->all_day && $status == '' ? ' '.$event_timezone : '') : '');
				if($pec_time != "") {
					$return .= '<div class="pec_event_page_date">'.
								'<p>'.$pec_time.'</p>'.
						   '</div>';
				}
				break;
		}
		
		return $return;

	}
	
	protected static function get_bookings_by_user( $user_id ) 
	{
	
		global $current_user, $wpdb;
		
		$string = "
            SELECT *
            FROM " . DP_PRO_EVENT_CALENDAR_TABLE_BOOKING . "
			WHERE id_user = %d AND event_date >= CURRENT_DATE AND status <> 'pending'
			ORDER BY event_date ASC ";

		if( is_numeric( self::$limit ) && self::$limit > 0 ) 
			$string .= "LIMIT " . self::$limit;

		$querystr = $wpdb->prepare( $string, $user_id );
        
        $bookings_obj = $wpdb->get_results( $querystr, OBJECT );
		
		return  $bookings_obj;
	
	}
	
	protected static function get_bookings_count($post_id, $date) 
	{
	
		global $current_user, $wpdb;
		
		if( ! is_numeric( $post_id ) ) 
			return 0;	

		$id_list = $post_id;
        if( function_exists( 'icl_object_id' ) ) 
        {
        
            global $sitepress;

            if( is_object( $sitepress ) ) 
            {
	        
	            $id_list_arr = array();
				$trid = $sitepress->get_element_trid($post_id, 'post_pec-events');
				$translation = $sitepress->get_element_translations($trid, 'post_pec-events');

				foreach($translation as $key) 
				{
				
					$id_list_arr[] = $key->element_id;
				
				}

				if( ! empty( $id_list_arr ) ) 
					$id_list = implode(",", $id_list_arr);
			
			}
		
		}
		
		$pec_booking_continuous = get_post_meta( $post_id, 'pec_booking_continuous', true );

		$querystr = "
            SELECT SUM(quantity) as counter
            FROM " . DP_PRO_EVENT_CALENDAR_TABLE_BOOKING . "
			WHERE id_event IN(".$id_list.") ";

		if( ! $pec_booking_continuous ) 
			$querystr .= "	AND event_date = '" . $date . "'";

		$querystr .= "
			AND status <> '" . DP_PRO_EVENT_CALENDAR_BOOKING_PENDING . "' AND status <> '" . DP_PRO_EVENT_CALENDAR_BOOKING_CANCELED_BY_USER . "' AND status <> '" .DP_PRO_EVENT_CALENDAR_BOOKING_CANCELED . "'";
        $bookings_obj = $wpdb->get_results( $querystr, OBJECT );
		
		return $bookings_obj[0]->counter;
	}
	
	function getBookingForm( $post_id, $date = '' ) 
	{

		global $dp_pec_payments, $dpProEventCalendar;
		
		$hide_buttons = false;
		$return = '';

		$calendar = self::$id_calendar;

		if( ! is_numeric( $calendar ) || $calendar == 0 ) 
		{
		
			$calendar = explode( ",", get_post_meta( $post_id, 'pec_id_calendar', true ) );
			$calendar = $calendar[0];
		
		}

		$event_single = "false";

		self::$event_id = $post_id;
		
		$max_upcoming_dates = self::$calendar_obj->booking_max_upcoming_dates;
		
		$pec_booking_block_hours = get_post_meta( $post_id, 'pec_booking_block_hours', true );
		$pec_booking_continuous = get_post_meta( $post_id, 'pec_booking_continuous', true );
		$start_date_from  = '';

		if( is_numeric( $pec_booking_block_hours) && $pec_booking_block_hours > 0 ) 
			$start_date_from  = date( 'Y-m-d H:i:s', strtotime( '+ '.$pec_booking_block_hours.' hours', current_time( 'timestamp' ) ) );

		$event_dates = self::upcomingCalendarLayout( true, ( is_numeric( $max_upcoming_dates ) && $max_upcoming_dates > 0 ? $max_upcoming_dates : 10 ), '', null, null, true, false, true, false, false, '', false, $start_date_from );

		$autoselected_date = "";
		
		$default_date = null;
		
		if( count( $event_dates ) == 1 ) 
		{
			
			if( ! is_array( $event_dates ) ) 
			
				$autoselected_date = substr( get_post_meta( $post_id, 'pec_date', true ), 0, 10 );
				
			else
			
				$autoselected_date = substr( $event_dates[0]->date, 0, 10 );
			
			
			$event_single = "true";
			
		}
		
		if( count( $event_dates ) > 0 || $date != '' ) 
		{
			
			if( ! is_array( $event_dates ) || $date != '' ) 
			{
			
				if( $date != "" ) 
					$default_date = $date;
				else
					$default_date = substr( get_post_meta( $post_id, 'pec_date', true ), 0, 10 );
			
			} else {
			
				$default_date = substr( $event_dates[0]->date, 0, 10 );
			
			}
			
		}

		$return .= '<div class="pec_book_select_date">';
		$return .= '<div class="pec_modal_wrap_content">';

		if( $event_single == "true" ) 
		{
			
			if( $this->user_has_bookings( $autoselected_date, $post_id ) ) 
			{
				
				$return .= '<p>' . self::$translation['TXT_BOOK_ALREADY_BOOKED'] . '</p>';
				
				$hide_buttons = true;
				
			}
		
		} elseif( $pec_booking_continuous ) {
			
			if( $this->user_has_bookings( "", $post_id ) ) 
			{
				
				$return .= '<p>' . self::$translation['TXT_BOOK_ALREADY_BOOKED'] . '</p>';
				
				$hide_buttons = true;
				
			}
		}

		if( ! is_user_logged_in() && ! $hide_buttons ) 
		{
		
			$return .= '<input type="text" value="" class="dpProEventCalendar_input dpProEventCalendar_from_name" id="pec_event_page_book_name" placeholder="'.self::$translation['TXT_YOUR_NAME'].'" />';	
			$return .= '<input type="email" value="" class="dpProEventCalendar_input dpProEventCalendar_from_email" id="pec_event_page_book_email" placeholder="'.self::$translation['TXT_YOUR_EMAIL'].'" />';	
		
		}
		
		if( self::$calendar_obj->booking_show_phone && ! $hide_buttons ) 
		{
		
			$return .= '<input type="tel" value="" class="dpProEventCalendar_input dpProEventCalendar_from_phone" id="pec_event_page_book_phone" placeholder="'.self::$translation['TXT_YOUR_PHONE'].'" />';		
		}

		$cal_booking_custom_fields = self::$calendar_obj->booking_custom_fields;
		$cal_booking_custom_fields_arr = explode( ',', $cal_booking_custom_fields );

		
		if( is_array( $dpProEventCalendar['booking_custom_fields_counter'] ) && !$hide_buttons ) 
		{
		
			$counter = 0;
			
			foreach( $dpProEventCalendar['booking_custom_fields_counter'] as $key ) 
			{
				
				if(!empty($cal_booking_custom_fields) && $cal_booking_custom_fields != "all" && $cal_booking_custom_fields != "" && !in_array($dpProEventCalendar['booking_custom_fields']['id'][$counter], $cal_booking_custom_fields_arr)) 
				{
				
					$counter++;
					continue;
				
				}

				if($dpProEventCalendar['booking_custom_fields']['type'][$counter] == "checkbox") 
				{

					$return .= '
					<div class="dp_pec_wrap_checkbox">
					<input type="checkbox" class="checkbox pec_event_page_book_extra_fields '.(!$dpProEventCalendar['booking_custom_fields']['optional'][$counter] ? 'pec_required' : '').'" value="1" id="pec_custom_'.$dpProEventCalendar['booking_custom_fields']['id'][$counter].'" name="pec_custom_'.$dpProEventCalendar['booking_custom_fields']['id'][$counter].'" /> <span>'.$dpProEventCalendar['booking_custom_fields']['placeholder'][$counter].'</span>
					</div>';
		
				} else {

					$return .= '
					<input type="text" class="dpProEventCalendar_input pec_event_page_book_extra_fields '.(!$dpProEventCalendar['booking_custom_fields']['optional'][$counter] ? 'pec_required' : '').'" value="" placeholder="'.$dpProEventCalendar['booking_custom_fields']['placeholder'][$counter].'" id="pec_custom_'.$dpProEventCalendar['booking_custom_fields']['id'][$counter].'" name="pec_custom_'.$dpProEventCalendar['booking_custom_fields']['id'][$counter].'" />';
					
				}
				
				$counter++;		
			
			}
		
		}
		
		$return .= '<style type="text/css">' . pec_setting( 'custom_css' ) . '</style>';
		
		$return .= '

			<input type="hidden" name="pec_event_page_book_event_id" id="pec_event_page_book_event_id" value="' . $post_id . '" />
			<input type="hidden" name="pec_event_page_book_calendar" id="pec_event_page_book_calendar" value="' . $calendar . '" />
			';
		
		self::$event_id = "";
		
		
		$return .= '<div class="dp_pec_clear"></div>';
		
		$return .= '</div>';
			
		if( ! $hide_buttons ) 
		{
			
			$return .= '<div class="dp_pec_clear"></div>

				<p class="pec_booking_date">
					<!--' . self::$translation['TXT_BOOK_EVENT_SELECT_DATE'] . '-->
					<select autocomplete="off" name="pec_event_page_book_date" id="pec_event_page_book_date"> 
					';
			
			$booking_limit = get_post_meta( $post_id, 'pec_booking_limit', true );
			if( ! is_numeric( $booking_limit ) )
				$booking_limit = 0;

			$booking_available_first = 0;
			
			$booking_max_quantity = self::$calendar_obj->booking_max_quantity;

			$count_event_dates = 0;
			if( ! is_array( $event_dates ) || count( $event_dates ) == 0 ) 
			{
				
				$return .= '<option value="">' . self::$translation['TXT_NO_EVENTS_FOUND'] . '</option>';	
				
			} else {

				foreach( $event_dates as $upcoming_date ) 
				{
				
					if( $upcoming_date->id == "" ) 
						$upcoming_date->id = $upcoming_date->ID;

					$upcoming_date = (object)array_merge( (array)self::getEventData( $upcoming_date->id ), (array)$upcoming_date );

					$curDate = substr( $upcoming_date->date, 0, 10 );

					if( $upcoming_date->pec_exceptions != "" ) 
					{
					
						$exceptions = explode( ',', $upcoming_date->pec_exceptions );
						
						if( $upcoming_date->recurring_frecuency != "" && in_array( $curDate, $exceptions ) ) 
						
							continue;
						
					}

					if( $upcoming_date->pec_daily_working_days && $upcoming_date->recurring_frecuency == 1 && (date('w', strtotime($curDate)) == "0" || date('w', strtotime($curDate)) == "6")) 
						continue;
					
					if( ! $upcoming_date->pec_daily_working_days && $upcoming_date->recurring_frecuency == 1 && $upcoming_date->pec_daily_every > 1 && 
						( ((strtotime($curDate) - strtotime(substr($upcoming_date->orig_date,0,11))) / (60 * 60 * 24)) % $upcoming_date->pec_daily_every != 0 )
					) continue;
					
					if($upcoming_date->recurring_frecuency == 2 && $upcoming_date->pec_weekly_every > 1 && 
						( ((strtotime($curDate) - strtotime(substr($upcoming_date->date,0,11))) / (60 * 60 * 24)) % ($upcoming_date->pec_weekly_every * 7) != 0 )
					) continue;
					
					if( $upcoming_date->recurring_frecuency == 3 && $upcoming_date->pec_monthly_every > 1 && 
						( ! is_int (((date('m', strtotime($curDate)) - date('m', strtotime(substr($upcoming_date->orig_date,0,11))))) / ($upcoming_date->pec_monthly_every)) )
					) continue;

					$booking_count = self::get_bookings_count( $upcoming_date->id, substr( $upcoming_date->date, 0, 10 ) );
					
					$booking_available = $booking_limit - $booking_count;
					
					if( $booking_available < 0 || ! is_numeric( $booking_available ) )
						$booking_available = 0;	
					
					if( $count_event_dates == 0 ) 
						$booking_available_first = $booking_available;	
					
					
					$option_value = ( $booking_limit > 0 && $booking_available == 0 ? '0' : substr( $upcoming_date->date, 0, 10 ) );
					
					if( $upcoming_date->end_time_hh != "" && $upcoming_date->end_time_mm != "" ) 
					{
						$end_time = str_pad($upcoming_date->end_time_hh, 2, "0", STR_PAD_LEFT).":".str_pad($upcoming_date->end_time_mm, 2, "0", STR_PAD_LEFT);

						$end_time = self::date_i18n(self::$time_format, strtotime("2000-01-01 ".$end_time.":00"));

						if($end_time == self::date_i18n(self::$time_format, strtotime($upcoming_date->date)))
							$end_time = "";	
						
					}

					$end_time = "";						

					$option_text = self::date_i18n(

						get_option('date_format') . (self::$calendar_obj->show_time && !$upcoming_date->hide_time ? ' ' . self::$time_format : ''), 
						strtotime($upcoming_date->date)

					).(self::$calendar_obj->show_time && !$upcoming_date->hide_time && $end_time != "" ? '-' . $end_time : '');

					if( $pec_booking_continuous )
						$option_text = self::$translation['TXT_ALL_EVENT_DATES'];
					

					if( self::$calendar_obj->booking_max_quantity == 1 && $this->user_has_bookings( $option_value, $upcoming_date->id ) ) 
					{
						$option_value = 0;
						$option_text .= " (".self::$translation['TXT_BOOK_ALREADY_BOOKED'].")";
					}

					$return .= '<option data-available="'.$booking_available.'" value="'.$option_value.'" '.($default_date == $option_value ? 'selected="selected"' : '').'>
					'.$option_text.' '.($booking_limit > 0 && self::$calendar_obj->booking_show_remaining ? '('.$booking_available.' '.self::$translation['TXT_BOOK_TICKETS_REMAINING'].')' : '').'</option>';	
					
					$count_event_dates++;

					if( $pec_booking_continuous )
						break;
				
				}
			}
			$return .= '</select></p><div class="dp_pec_clear"></div>';
			

			if ( is_plugin_active( 'woocommerce-dp-pec/woocommerce-dp-pec.php' ) ) 
			{

				$pec_booking_ticket = get_post_meta($post_id, 'pec_booking_ticket', true);
				$pec_booking_ticket = $pec_booking_ticket_arr = ($pec_booking_ticket != "" ? explode(",", $pec_booking_ticket) : '');
				
				if( is_array( $pec_booking_ticket ) && ! empty( $pec_booking_ticket ) ) 
				{
				
					$return .= '<p class="pec_booking_ticket">
							<!--'.self::$translation['TXT_BOOK_EVENT_SELECT_DATE'].'-->
							<select autocomplete="off" name="pec_event_page_book_ticket" id="pec_event_page_book_ticket"> 
							';
							foreach($pec_booking_ticket as $ticket) {
								if($ticket == "") { continue;}
								$return .= '<option value="'.$ticket.'">'.get_the_title($ticket).'</option>';	
							}

					$return .= '</select></p><div class="dp_pec_clear"></div>';
				}
			
			}

			$return .= '<div class="pec_booking_quantity">';

			$return .= '<select autocomplete="off" name="pec_event_page_book_quantity" id="pec_event_page_book_quantity">';

			

			//if(self::$calendar_obj->booking_max_quantity > $booking_available && is_numeric($booking_available) && $booking_available > 0) {
				//$booking_max_quantity = $booking_available;
			//}
			for($i = 1; $i <= $booking_max_quantity; $i++) {
				$return .= '<option value="'.$i.'">'.$i.'</option>';	
			}
			$return .= '</select>';

			$return .= '<span>'.self::$translation['TXT_QUANTITY'].'</span>';
			
			$price = get_post_meta($post_id, 'pec_booking_price', true);
			
			if( is_numeric( $price ) && $price > 0 )
				$return .= '<p class="dp_pec_payment_price">'.__('Total Price', 'dpProEventCalendar').' <span class="dp_pec_payment_price_wrapper"><span class="dp_pec_payment_price_value" data-price="'.$price. '" data-price-updated="'.$price. '">'.$price. '</span> ' .$dp_pec_payments['currency'].'</span></p>';
			

			$return .= '</div>';
				
			$return .= '<div class="dp_pec_clear"></div>';

			$form_bottom = apply_filters( 'pec_booking_form_bottom', $post_id );

			if( $form_bottom != $post_id && $form_bottom != "" )
				$return .= $form_bottom;

			if( self::$calendar_obj->booking_comment && ! $hide_buttons ) {
				$return .= '
				<textarea name="pec_event_page_book_comment" id="pec_event_page_book_comment" placeholder="'.self::$translation['TXT_BOOK_EVENT_COMMENT'].'" class="dpProEventCalendar_textarea"></textarea>';
			}

			$return .= '
				<div class="pec-add-footer">
					<button class="pec_event_page_send_booking" '.($booking_limit > 0 && $booking_available_first == 0 ? 'disabled="disabled"' : '').'>'.(get_post_meta($post_id, 'pec_booking_price', true) ? apply_filters('pec_payments_send', self::$translation['TXT_SEND']) : self::$translation['TXT_SEND'] ).'</button>
					';
			if( is_numeric( pec_setting( 'terms_conditions' ) ) ) 
			{
			
				$return .= '
					<p><input type="checkbox" required name="pec_event_page_book_terms_conditions" id="pec_event_page_book_terms_conditions" value="1" /> ' . sprintf(__('I\'ve read and accept the %s terms & conditions %s', 'dpProEventCalendar'), '<a href="' . dpProEventCalendar_get_permalink( pec_setting( 'terms_conditions' ) ) . '" target="_blank">', '</a>') . '</p>';
			}	
			$return .= '
					<div class="dp_pec_clear"></div>
				</div>';
		}
		$return .= '
			</div>
		</div>';

		return $return;

	}

	protected static function getBookingBookedLabel ( $post_id, $date ) 
	{
	
		$return = "";
		if( self::$calendar_obj->booking_display_fully_booked ) 
		{

			if( get_post_meta( $post_id, 'pec_enable_booking', true ) || self::$calendar_obj->booking_enable ) 
			{
				//Booking Available For Date
				$booking_limit = get_post_meta($post_id, 'pec_booking_limit', true);
				if( is_numeric( $booking_limit ) && $booking_limit > 0 ) 
				{
				
					$booking_count = self::get_bookings_count($post_id, substr($date, 0, 10));
						
					$booking_available = (int)$booking_limit - (int)$booking_count;
						
					if( $booking_available <= 0 || ! is_numeric( $booking_available ) )
						
						$return = '<span class="dp_pec_fully_booked">'.self::$translation['TXT_FULLY_BOOKED'].'</span>';
				}
			}
		}

		return $return;

	}

	protected static function get_booking_button( $post_id, $date = '', $clear = true, $show_attendees = true, $show_label = true ) 
	{
	
		global $dp_pec_payments;

		$return = '';
		
		if(post_password_required($post_id)) 
			return '';

		$extra_dates = array();
		$has_extra_dates = false;
		$pec_extra_dates = get_post_meta($post_id, 'pec_extra_dates', true);

		if($pec_extra_dates != "") 
		{
		
			$extra_dates = explode(",", $pec_extra_dates);
		
			foreach($extra_dates as $extra_date) 
			{
			
				if(strtotime($extra_date) > (int)current_time( 'timestamp' )) 
					$has_extra_dates = true;
		
			}
		
		}
		
		if(
			(is_user_logged_in() || self::$calendar_obj->booking_non_logged) 
			&& 
			(
				(get_post_meta($post_id, 'pec_enable_booking', true) || self::$calendar_obj->booking_enable) 
				&& 
				( 
					get_post_meta($post_id, 'pec_recurring_frecuency', true) > 0 
					|| 
					strtotime(get_post_meta($post_id, 'pec_date', true)) > (int)current_time( 'timestamp' ) 
					|| 
					$has_extra_dates
				)
			)
		) {

			if($date != '' && (strtotime($date. ' '.date('H:i:s', strtotime(get_post_meta($post_id, 'pec_date', true)))) < (int)current_time( 'timestamp' ) )) 
				return false;

			$pec_booking_block_hours = get_post_meta($post_id, 'pec_booking_block_hours', true);
			
			if( is_numeric( $pec_booking_block_hours ) && $pec_booking_block_hours > 0 ) 
			{
			
				$start_date_from  = date('Y-m-d H:i:s', strtotime('+ '.$pec_booking_block_hours.' hours', current_time('timestamp')));

				$event_dates = self::upcomingCalendarLayout( true, 3, '', null, null, true, false, true, false, false, '', false, $start_date_from );

				if( empty( $event_dates ) ) 
					return '';
				
			}

			$calendar = self::$id_calendar;

			if( ! is_numeric( $calendar ) || $calendar == 0 ) 
			{
			
				$calendar = explode(",", get_post_meta($post_id, 'pec_id_calendar', true));
				$calendar = $calendar[0];
			
			}

			if( $show_label )
				$return .= '<div class="pec_event_page_book_wrapper">';

			$return .= '<a href="#" class="pec_event_page_book" data-pec-tooltip="' . ( ! $show_label ? addslashes( self::$translation['TXT_BOOK_EVENT'] ) : '' ) . '" data-event-id="'.$post_id.'" data-calendar="'.$calendar.'" data-date="'.$date.'">';

			$return .= '<i class="fa fa-calendar"></i>';

			if( $show_label )
				$return .= '<strong>' . self::$translation['TXT_BOOK_EVENT'] . '</strong>' . (get_post_meta($post_id, 'pec_booking_price', true) > 0 && $dp_pec_payments['currency'] != "" ? ' <div class="pec_booking_price">'.get_post_meta($post_id, 'pec_booking_price', true). ' ' .$dp_pec_payments['currency'].'</div>' : '');

			$return .= '</a>';

			if( $clear )
				$return .= '<div class="dp_pec_clear"></div>';
			
			// Attendees Counter
						
			if( self::$calendar_obj->booking_display_attendees && $show_attendees ) 
			{
				
				$attendees_counter = self::getEventBookings( true, $date, $post_id );
				
				$return .= "<div class='dp_pec_attendees_counter dp_pec_tooltip_list_wrap dp_pec_attendees_counter_".$post_id."'>";

				$return .= "<span>" . $attendees_counter . '</span> ' . self::$translation['TXT_ATTENDEES'];

				if ( self::$calendar_obj->booking_display_attendees_names && $attendees_counter > 0 ) 
				{

					$attendees_list = self::getEventBookings( false, $date, $post_id );

					$return .= "<div class='dp_pec_tooltip_list'>";

					$return .= "	<ul class='dp_pec_tooltip_list_ul'>";

					foreach($attendees_list as $booking) 
					{
					
						if(is_numeric($booking->id_user) && $booking->id_user > 0) 
						{

							$userdata = get_userdata($booking->id_user);
					
						} else {
					
							$userdata = new stdClass();
					
							$userdata->display_name = $booking->name;
							$userdata->user_email = $booking->email;	
					
						}

					
							$return .= "<li>".$userdata->display_name."</li>";
					}
					
					$return .= "	</ul>";

					$return .= "</div>";

				}
				
				$return .= "</div>";
				
				if($clear)
					$return .= '<div class="dp_pec_clear"></div>';

			}
			
			if( $show_label )
				$return .= "</div>";

		}	
		
		return $return;
	}

	// ************************************* //
	// ****** Monthly Calendar Layout ****** //
	//************************************** //
	
	function monthlyCalendarLayout($compact = false) 
	{

		$month_search = self::$datesObj->currentYear.'-'.str_pad(self::$datesObj->currentMonth, 2, "0", STR_PAD_LEFT);

		$layoutCache = 'monthlyLayout';

		if($compact) 
		{
		
			$layoutCache = 'monthlyLayoutCompact';
		
		}

			$html = '';
			
			$html .= '<div class="dp_pec_monthly_grid">';

			if(self::$calendar_obj->first_day == 1) 
			{

				if(self::$datesObj->firstDayNum == 0) { self::$datesObj->firstDayNum == 7;  }

				self::$datesObj->firstDayNum--;
				
				$html .= '
					 <div class="dp_pec_dayname dp_pec_dayname_monday">
					 	<div class="dp_pec_dayname_item">
							<span>'.self::$translation['DAY_MONDAY'].'</span>
					 	</div>
					 </div>';
			} else {

				$html .= '
					 <div class="dp_pec_dayname dp_pec_dayname_sunday">
						<div class="dp_pec_dayname_item">
							<span>'.self::$translation['DAY_SUNDAY'].'</span>
					 	</div>
					 </div>
					 <div class="dp_pec_dayname dp_pec_dayname_monday">
						<div class="dp_pec_dayname_item">	
							<span>'.self::$translation['DAY_MONDAY'].'</span>
					 	</div>
					 </div>';
			}

			$html .= '
					 <div class="dp_pec_dayname dp_pec_dayname_tuesday">
						<div class="dp_pec_dayname_item">	
							<span>'.self::$translation['DAY_TUESDAY'].'</span>
					 	</div>
					 </div>
					 <div class="dp_pec_dayname dp_pec_dayname_wednesday">
						<div class="dp_pec_dayname_item">
							<span>'.self::$translation['DAY_WEDNESDAY'].'</span>
					 	</div>
					 </div>
					 <div class="dp_pec_dayname dp_pec_dayname_thursday">
						<div class="dp_pec_dayname_item">	
							<span>'.self::$translation['DAY_THURSDAY'].'</span>
					 	</div>
					 </div>
					 <div class="dp_pec_dayname dp_pec_dayname_friday">
						<div class="dp_pec_dayname_item">	
							<span>'.self::$translation['DAY_FRIDAY'].'</span>
					 	</div>
					 </div>
					 <div class="dp_pec_dayname dp_pec_dayname_saturday">
						<div class="dp_pec_dayname_item">	
							<span>'.self::$translation['DAY_SATURDAY'].'</span>
					 	</div>
					 </div>
					 ';

			if(self::$calendar_obj->first_day == 1) 
			{
			
				$html .= '
					 <div class="dp_pec_dayname dp_pec_dayname_sunday">
						<div class="dp_pec_dayname_item">	
							<span>'.self::$translation['DAY_SUNDAY'].'</span>
						</div> 
					 </div>';
			
			}

			
			$general_count = 0;
			
			
			if( self::$datesObj->firstDayNum != 6 ) 
			{
				
				for($i = (self::$datesObj->daysInPrevMonth - self::$datesObj->firstDayNum); $i <= self::$datesObj->daysInPrevMonth; $i++) 
				{

					$html .= '
							<div class="dp_pec_date disabled '.($general_count % 7 == 0 ? 'first-child' : '').'">
								<div class="dp_pec_date_item"><div class="dp_date_head"><span>'.str_pad($i, 2, "0", STR_PAD_LEFT).'</span></div></div>
							</div>';
					
					$general_count++;
				}
				
			}

			
			$month_number = str_pad((self::$datesObj->currentMonth), 2, "0", STR_PAD_LEFT);
			$year = self::$datesObj->currentYear;
			
			$start = $year."-".$month_number."-01 00:00:00";

			if((self::$calendar_obj->hide_old_dates || (isset(self::$opts['hide_old_dates']) && self::$opts['hide_old_dates'])) && date("Y-m") == $year."-".$month_number) 
			{
			
				$start = date("Y-m-d H:i:s");
			
			}
			
			if(!self::$is_admin) 
			{
			
				$list = self::upcomingCalendarLayout( true, 20, '', $start, $year."-".$month_number."-".self::$datesObj->daysInCurrentMonth." 23:59:59", true );
			
			}

			
			for($i = 1; $i <= self::$datesObj->daysInCurrentMonth; $i++) 
			{

				$result = array();

				$curDate = self::$datesObj->currentYear.'-'.str_pad(self::$datesObj->currentMonth, 2, "0", STR_PAD_LEFT).'-'.str_pad($i, 2, "0", STR_PAD_LEFT);
				$countEvents = 0;
				$eventsCurrDate = array();
				
				if(!self::$is_admin) 
				{

					if(is_array($list)) 
					{
					
						foreach ($list as $key) 
						{
					
							if(substr($key->date, 0, 10) == $curDate) 
							{
					
								$result[] = $key;
					
							}		
					
						}
					
					}

					//$result = $this->getEventsByDate($curDate);
					
					if(is_array($result)) 
					{
					
						foreach($result as $event) 
						{
							
							if($event->id == "") 
								$event->id = $event->ID;

							// Reset featured option
							unset($event->featured_event);
							
							$event = (object)array_merge((array)self::getEventData($event->id), (array)$event);

							if($event->pec_exceptions != "") {
								$exceptions = explode(',', $event->pec_exceptions);
								
								if($event->recurring_frecuency != "" && in_array($curDate, $exceptions)) {
									continue;
								}
							}

							if($event->pec_daily_working_days && $event->recurring_frecuency == 1 && (date('w', strtotime($curDate)) == "0" || date('w', strtotime($curDate)) == "6")) {
								continue;
							}
							
							if(!$event->pec_daily_working_days && $event->recurring_frecuency == 1 && $event->pec_daily_every > 1 && 
								( ((strtotime($curDate) - strtotime(substr($event->orig_date,0,11))) / (60 * 60 * 24)) % $event->pec_daily_every != 0 )
							) {
								continue;
							}
							
							if($event->recurring_frecuency == 2 && $event->pec_weekly_every > 1 && 
								( ((strtotime(substr($event->date,0,11)) - strtotime(substr($event->date,0,11))) / (60 * 60 * 24)) % ($event->pec_weekly_every * 7) != 0 )
							) {
								continue;
							}
							
							if($event->recurring_frecuency == 3 && $event->pec_monthly_every > 1 && 
								( !is_int (((date('m', strtotime($curDate)) - date('m', strtotime(substr($event->orig_date,0,11))))) / ($event->pec_monthly_every)) )
							) {
								continue;
							}
							
							if($event->featured_event) 
							{
							
								array_unshift($eventsCurrDate, $event);
							
							} else {
							
								$eventsCurrDate[] = $event;
							
							}

							$countEvents++;

						}
					}
				}
				
				//$countEvents = $this->getCountEventsByDate($curDate);
				if((self::$calendar_obj->hide_old_dates || (isset(self::$opts['hide_old_dates']) && self::$opts['hide_old_dates'])) || !empty(self::$event_id)) 
				{
				
					@self::$calendar_obj->date_range_start = date('Y-m-d');	
				
				}
				
				if((self::$calendar_obj->date_range_start != '0000-00-00' && self::$calendar_obj->date_range_start != NULL && (strtotime($curDate) < strtotime(self::$calendar_obj->date_range_start)) || ( self::$calendar_obj->date_range_end != '0000-00-00' && self::$calendar_obj->date_range_end != NULL && strtotime($curDate) > strtotime(self::$calendar_obj->date_range_end))) && !self::$is_admin) {
					

					$html .= '
						<div class="dp_pec_date disabled '.($general_count % 7 == 0 ? 'first-child' : '').'">
							<div class="dp_pec_date_item"><div class="dp_date_head"><span>'.str_pad($i, 2, "0", STR_PAD_LEFT).'</span></div></div>
						</div>';
				} else {
					$special_date = "";
					$special_date_obj = $this->getSpecialDates($curDate);
					$booked_date = false;
					$booking_remain = true;					
					
					$special_date_title = "";

					if(!empty(self::$event_id)) {
						
						$booking_limit = get_post_meta(self::$event_id, 'pec_booking_limit', true);
						$booking_count = self::get_bookings_count(self::$event_id, $curDate);
						
						if($booking_limit > 0 && ($booking_limit - $booking_count) <= 0) {
							$booking_remain = false;
						}

						if($this->user_has_bookings($curDate, self::$event_id)) {
							$special_date = "style='background-color: ".self::$calendar_obj->booking_event_color.";' ";
							$booked_date = true;
						}
						
					} else {
						
						if(is_object($special_date_obj) && $special_date_obj->color) {
							$special_date = "style='background-color: ".$special_date_obj->color.";' ";
						}
						if(is_object($special_date_obj) && $special_date_obj->title) {
							$special_date_title = $special_date_obj->title;
						}
						
						if($curDate == date("Y-m-d", current_time('timestamp'))) {
							$special_date_title = self::$translation['TXT_CURRENT_DATE'];
							//$special_date = "style='background-color: ".self::$calendar_obj->current_date_color.";' ";
						}
						
					}

					$html .= '
						<div class="dp_pec_date dp_pec_date_'.strtolower(date('l', strtotime($curDate))).' '.($countEvents > 0 && !$booked_date && $booking_remain ? 'pec_has_events' : '').' '.($general_count % 7 == 0 ? 'first-child' : '').' '.($special_date != "" ? 'dp_pec_special_date' : '').'" data-dppec-date="'.$curDate.'">
							<div class="dp_pec_date_item" '.$special_date.'><div class="dp_special_date_dot" '.$special_date.'>'.($special_date_title != "" ? '<div>'.$special_date_title.'</div>' : "").'</div><div class="dp_date_head"><span>'.str_pad($i, 2, "0", STR_PAD_LEFT).'</span></div>
							';

					$events_save_arr = array();

					if( empty(self::$event_id)) 
					{

						$style = '';
						
						if( ! self::$calendar_obj->show_titles_monthly )
							$style = " style='display:none;'";


						if( !$compact )
							$html .= '<div class="pec_monthlyDisplayEvents"' . $style . '>';
	
						$count_monthly_title = 0;

						$calendar_per_date = (isset(self::$opts['calendar_per_date']) &&is_numeric(self::$opts['calendar_per_date']) && self::$opts['calendar_per_date'] > 0 ? self::$opts['calendar_per_date'] : 3);

						foreach( $eventsCurrDate as $event ) 
						{
			
							if($event->id == "") 
								$event->id = $event->ID;
							
							$event = (object)array_merge((array)self::getEventData($event->id), (array)$event);

							$event->time = self::date_i18n(self::$time_format, strtotime($event->date));

							$orig_time = date( 'H:i:s', strtotime($event->date));
							
							$end_datetime = self::get_end_datetime( $event, true );
							$end_time = $end_datetime['end_time'];
													
							if(isset($event->all_day) && $event->all_day) 
							{
							
								$event->time = self::$translation['TXT_ALL_DAY'];
								$end_time = "";
							
							}

							$status = self::get_status_label( $event->status );
							if($status != '')
							{

								$event->time = $status;
								$end_time = "";

							}
							
							$title = wp_trim_words($event->title, 5);

							$event_timezone = dpProEventCalendar_getEventTimezone($event->id);

							if(self::$calendar_obj->show_time && !$event->hide_time) 
							
								$title = '<strong>'.$event->time.$end_time.(self::$calendar_obj->show_timezone && !$event->all_day && $status == '' ? ' '.$event_timezone : '').'</strong><br>'.$title;
							
							
							if(self::$calendar_obj->link_post) 
								$href = self::get_permalink ( $event, $curDate . ' ' . $orig_time );
							else
								$href = '#';

							if( !$compact )
							{
								$html .= '<a class="dp_daily_event" style="'.($event->color != "" ? 'border-color:'.$event->color.';' : '').($count_monthly_title >= $calendar_per_date ? 'display:none;' : '').'" data-dppec-event="'.$event->id.'" href="'.$href.'" '.($href != '#' ? 'target="'.self::$calendar_obj->link_post_target.'"' : '').' data-dppec-date="'.$curDate.'">'.$title;
								$html .= self::getBookingBookedLabel($event->id, $curDate);
								$html .= '</a>';
							}

							$events_save_arr[] = $event;

							$count_monthly_title++;
							
						}

						
						if( !$compact )
						{
							if($count_monthly_title > $calendar_per_date) 
							
								$html .= '<span class="dp_daily_event dp_daily_event_show_more"> + '.($count_monthly_title - $calendar_per_date).' '.self::$translation['TXT_MORE'].'</span>';

							$html .= '</div>';
						}
						
					}
					
					$html .= '
							' . ( $countEvents > 0 && ! $booked_date && $booking_remain ? ( self::$calendar_obj->show_x || ! empty(self::$event_id) ? ( ! empty(self::$event_id) ? '<span class="dp_book_event_radio"></span>' : '<span class="dp_count_events" '.(self::$calendar_obj->show_titles_monthly && empty(self::$event_id) && !$compact ? 'style="display:none"' : '').'>X</span>') : '<span class="dp_count_events" ' . ( self::$calendar_obj->show_titles_monthly && empty(self::$event_id) && ! $compact ? 'style="display:none"' : '' ) . '>' . $countEvents . '</span>') : '' );
						
					
					if( self::$is_admin ) 
					{
					
						$html .= '
							<div class="dp_manage_special_dates" style="display: none;">
								<div class="dp_manage_sd_head">Special Date</div>
								<select autocomplete="off">
									<option value="">None</option>';

									foreach( self::getSpecialDatesList() as $key ) 
									{
									
										$html .= '<option value="' . $key->id . ',' . $key->color . '" ' . ( is_object( $special_date_obj ) && ( $key->id == $special_date_obj->id ) ? 'selected' : '' ) . '>' . $key->title . '</option>';
									
									}

						$html .= '
								</select>	
							</div>';
					
					}
					
					if( $countEvents > 0 && ( self::$calendar_obj->show_preview || !empty( self::$event_id ) ) ) 
					{
					
						$html .= '
							<div class="eventsPreview">
								<ul>
							';
							if( ! empty( self::$event_id ) ) 
							{
							
								if($booked_date) 
									$html .= '<li>'.self::$translation['TXT_BOOK_ALREADY_BOOKED'].'</li>';
								else {

									$html .= '<li>';
									
									if( $booking_remain ) 
									
										$html .= self::$translation['TXT_BOOK_EVENT_PICK_DATE'];
									
									
									if( $booking_limit > 0 && self::$calendar_obj->booking_show_remaining ) 
									{
										
										if($booking_remain) 
									
											$html .= '<br>';
										
									
										$html .= '<strong>'.($booking_limit - $booking_count).' '.self::$translation['TXT_BOOK_TICKETS_REMAINING'].'.</strong>';
										
									}
									$html .= '</li>';

								}
								
							} else {

								if( is_array( $events_save_arr ) ) 
								{
								
									foreach( $events_save_arr as $event ) 
									{
										
																
										$html .= '<li data-dppec-event="'.$event->id.'">';
										
										if(self::$calendar_obj->show_time && !$event->hide_time) 
										
											$html .= '<span>'.$event->time.'</span>';

										$html .= '<h4>'.($event->color != "" ? '<div class="pec_preview_color" style="background-color:'.$event->color.';"></div>' : '').$event->title.'</h4>';


										$html .= '<div class="dp_pec_clear"></div>';

										$post_thumbnail_id = get_post_thumbnail_id( $event->id );
										$image_attributes = wp_get_attachment_image_src( $post_thumbnail_id, 'medium' );

										if( $post_thumbnail_id && ! $this->is_widget() ) 
										
											$html .= '	<div class="dp_pec_preview_photo" style="background-image: url('.(isset($image_attributes[0]) ? $image_attributes[0] : '').');"></div>';
										
										$html .= '</li>';

									}

								}

							}
						$html .= '
								</ul>
							</div>';
					}
					
					$html .= '</div>
						</div>';
				}
				
				$general_count++;
			}
			
			if( self::$datesObj->lastDayNum != (self::$calendar_obj->first_day == 1 ? 0 : 6) ) 
			{
				
				for($i = 1; $i <= ( (self::$calendar_obj->first_day == 1 ? 7 : 6) - self::$datesObj->lastDayNum ); $i++) 
				{

					$html .= '
							<div class="dp_pec_date disabled '.($general_count % 7 == 0 ? 'first-child' : '').'">
								<div class="dp_pec_date_item"><div class="dp_date_head"><span>'.str_pad($i, 2, "0", STR_PAD_LEFT).'</span></div></div>
							</div>';
					
					$general_count++;
					
				}
				
			}

			$html .= '</div>
				<div class="dp_pec_clear"></div>';
		
		return $html;

	}
	
	// ************************************* //
	// ****** Daily Calendar Layout ****** //
	//************************************** //
	
	function dailyCalendarLayout($curDate = null) 
	{

		$html = "";
		
		if( is_null( $curDate ) ) 
			$curDate = self::$datesObj->currentYear . '-' . str_pad( self::$datesObj->currentMonth, 2, "0", STR_PAD_LEFT ) . '-' . str_pad( self::$datesObj->currentDate, 2, "0", STR_PAD_LEFT );
		
		
		if( self::$calendar_obj->daily_weekly_layout == 'schedule' ) 
		{

			$result = $this->getEventsByDate($curDate);
			
			if( ! is_array( $result ) )
				$result = array();		

			$array_daily_events = array();

			foreach( $result as $event ) 
			{
				
				if( $event->id == "" ) 
					$event->id = $event->ID;
				
				$event = (object)array_merge((array)self::getEventData($event->id), (array)$event);

				$event_hour = date('G', strtotime($event->date));

				if( $event->all_day )
					$array_daily_events['all_day'][] = $event;
				else
					$array_daily_events[$event_hour][] = $event;

			}

			$counter_i = 0;


			$html .= '<div class="dp_pec_daily_grid">';

			$sp_date = $this->getSpecialDates( $curDate, true);

			if( isset( $sp_date->color ) && $sp_date->color ) 
				$special_date = " style='color: #fff; background-color: ".$sp_date->color.";' ";
			else
				$special_date = "";

			$html .= '
				 <div class="dp_pec_dayname">
				 	<div class="dp_pec_dayname_item">
						<span>'.$this->get_day_name(self::$datesObj->currentDayNumber).' <span class="dp_pec_daynumber" '.$special_date.'>'. str_pad(self::$datesObj->currentDate, 2, "0", STR_PAD_LEFT) .'</span></span>
				 	</div>
				 </div>';

					$disabled = "";
					
					if( (self::$calendar_obj->hide_old_dates || self::$opts['hide_old_dates']) && $curDate < current_time('Y-m-d') ) 
						$disabled = "disabled";
				
					$html .= '<div class="dp_pec_date dp_pec_date_all_day dp_pec_first_column dp_pec_date_'.strtolower(date('l', strtotime($curDate))).' '.$disabled.'" style="margin-left: 0;">';

					$html .= 	'<div class="dp_pec_date_item">';

					$html .= '
									<div class="dp_pec_date_weekly_time">
										<div class="dp_date_head"><span>'.self::$translation['TXT_ALL_DAY'].'</span></div>
									</div>';

					if(isset($array_daily_events['all_day']) && is_array($array_daily_events['all_day']))
					{

						foreach( $array_daily_events['all_day'] as $event)
						{

							if( self::$calendar_obj->link_post ) 
								$href = self::get_permalink ( $event, $curDate . ' ' . date('H:i:s', strtotime( $event->date ) ) );
							else
								$href = '#';
							

							$html .= '<a class="dp_daily_event" ' . ( $event->color != "" ? 'style="border-color:' . $event->color . '"' : '') . ' data-dppec-event="' . $event->id . '" href="' . $href . '" ' . ( $href != '#' ? 'target="' . self::$calendar_obj->link_post_target . '"' : '' ) . ' data-dppec-date="' . $curDate . '">' . $event->title;
							$html .= self::getBookingBookedLabel($event->id, $event->date);
							$html .= '</a>';

						}

					}

					
					// Add button
					$html .= self::add_event_button( true, strtotime( $curDate ) );
							

					$html .= 	'</div>';

					$html .= '</div>';


			for($i = self::$calendar_obj->limit_time_start; $i <= self::$calendar_obj->limit_time_end; $i++) 
			{
			
				$counter_i++;
				$min = '00';
				$hour = $i;

				if(dpProEventCalendar_is_ampm()) 
				{
				
					$min = ($i >= 12 ? __('PM', 'dpProEventCalendar') : __('AM', 'dpProEventCalendar'));
					$hour = ($i > 12 ? $i - 12 : $i);
				
				}

				$html .= '<div class="dp_pec_date dp_pec_first_column">
							<div class="dp_pec_date_item">';

				// Add button
				$html .= self::add_event_button( true, strtotime($curDate . ' ' . $i . ':00:00' ) );
					

				$html .= '<div class="dp_pec_date_weekly_time"><div class="dp_date_head"><span>'.$hour.'</span><span class="dp_pec_minutes">'.$min.'</span></div></div>';
				
				if(isset($array_daily_events[$i]) && is_array($array_daily_events[$i]))
				{
					
					foreach( $array_daily_events[$i] as $event)
					{
						

						$event_hour = date('G', strtotime($event->date));
						$event_hour_end = ltrim($event->end_time_hh, "0");
						$event_minute = date('i', strtotime($event->date));
						$event_minute_end = ltrim($event->end_time_mm, "0");

						if($event_hour_end <= $event_hour) 
						{
						
							$event_hour_end = $event_hour + 1;
						
						}
		
						
						$time = self::date_i18n( self::$time_format, strtotime( $event->date ) );

						if( ! self::$calendar_obj->show_time || $event->hide_time || $event->all_day ) 
							$time = '';

						$title = $event->title;
						
						if( self::$calendar_obj->link_post ) 
							$href = self::get_permalink ( $event, $curDate . ' ' . date('H:i:s', strtotime( $event->date ) ) );
						else
							$href = '#';
						

						// Calculate height

						$minutes_parsed = $this->parse_minutes( $event_minute );
						$minutes_parsed_end = $this->parse_minutes( $event_minute_end );

						if( $event_hour_end > ( self::$calendar_obj->limit_time_end + 1) )
							$event_hour_end = ( self::$calendar_obj->limit_time_end +1 );

						$height = ( $event_hour_end - $event_hour ) * 80 - ( 20 * $minutes_parsed );

						$height += (20 * $minutes_parsed_end);

						$height = 'height: '.$height.'px;';

						$top = 'margin-top: '.(20 * $minutes_parsed).'px;';

						$html .= '<a class="dp_daily_event" style="'.($event->color != "" ? 'border-color:'.$event->color.';' : '').$height.$top.'" data-dppec-event="'.$event->id.'" href="'.$href.'" '.($href != '#' ? 'target="'.self::$calendar_obj->link_post_target.'"' : '').' data-dppec-date="'.$curDate.'"><span>'.$time.'</span>' . $title;
						$html .= self::getBookingBookedLabel($event->id, $event->date);
						$html .= '</a>';

						
					}

				}
				$html .= '</div>';
				$html .= '</div>';
				
			}
			$html .= '</div>';

		} else {
			
			$html .= $this->eventsListLayout( $curDate, false );
				
		}
		
		$html .= '<div class="dp_pec_clear"></div>';
		return $html;
	}
	
	// ************************************* //
	// ****** Weekly Calendar Layout ****** //
	//************************************** //
	
	function weeklyCalendarLayout( $curDate = null ) 
	{
	
		$html = "";

		if( is_null( $curDate ) ) 
		
			$curDate = self::$datesObj->currentYear.'-'.str_pad(self::$datesObj->currentMonth, 2, "0", STR_PAD_LEFT).'-'.str_pad(self::$datesObj->currentDate, 2, "0", STR_PAD_LEFT);
		
		if( self::$calendar_obj->first_day == 1 ) 
		{
		
			$weekly_first_date = strtotime('last monday', ($this->defaultDate + (24* 60 * 60)));
			$weekly_last_date = strtotime('next sunday', ($this->defaultDate - (24* 60 * 60)));
		
		} else {
		
			$weekly_first_date = strtotime('last sunday', ($this->defaultDate + (24* 60 * 60)));
			$weekly_last_date = strtotime('next saturday', ($this->defaultDate - (24* 60 * 60)));
		
		}
		
		$week_days = array();
		$weekly_day = array();
		$sp_date = array();
		$special_date = array();

		for( $i = 0; $i <= 6; $i++ )
		{

			
			if( $i == 0 )
				$week_days[$i] = $weekly_first_date;
			else
				$week_days[$i] = strtotime('+'.$i.' day', $weekly_first_date);

			$weekly_day[$i] = self::date_i18n( 'd', $week_days[$i] );
			$sp_date[$i] = $this->getSpecialDates( date ( 'Y-m-d', $week_days[$i] ), true );

			if( isset( $sp_date[$i]->color ) && $sp_date[$i]->color ) 
			
				$special_date[$i] = " style='color: #fff; background-color: " . $sp_date[$i]->color . ";' ";
			
			else
			
				$special_date[$i] = "";
			
		}
		
		$html .= '<div class="dp_pec_monthly_grid' . (self::$calendar_obj->daily_weekly_layout == 'schedule' ? " dp_pec_monthly_grid_schedule" : "" ). '">';

		if(self::$calendar_obj->first_day == 1) 
		{
		
			if(self::$datesObj->firstDayNum == 0) { self::$datesObj->firstDayNum == 7;  }
			self::$datesObj->firstDayNum--;
			
			$html .= '
				 <div class="dp_pec_dayname dp_pec_dayname_monday">
				 	<div class="dp_pec_dayname_item">
						<span>' . mb_substr(self::$translation['DAY_MONDAY'], 0,3, 'UTF-8').' <span class="dp_pec_daynumber" '.$special_date[0].'>'.$weekly_day[0].'</span></span>
				 	</div>
				 </div>';
		
		} else {
		
			$html .= '
				 <div class="dp_pec_dayname dp_pec_dayname_sunday">
				 	<div class="dp_pec_dayname_item">
						<span>' . mb_substr(self::$translation['DAY_SUNDAY'], 0,3, 'UTF-8').' <span class="dp_pec_daynumber" '.$special_date[0].'>'.$weekly_day[0].'</span></span>
				 	</div>
				 </div>
				 <div class="dp_pec_dayname dp_pec_dayname_monday">
					<div class="dp_pec_dayname_item">
						<span>' . mb_substr(self::$translation['DAY_MONDAY'], 0,3, 'UTF-8').' <span class="dp_pec_daynumber" '.$special_date[1].'>'.$weekly_day[1].'</span></span>
				 	</div>
				 </div>';
		
		}
		
		$html .= '
				 <div class="dp_pec_dayname dp_pec_dayname_tuesday">
					<div class="dp_pec_dayname_item">
						<span>' . mb_substr(self::$translation['DAY_TUESDAY'], 0,3, 'UTF-8').' <span class="dp_pec_daynumber" '.(self::$calendar_obj->first_day == 1 ? $special_date[1].'>'.$weekly_day[1] : $special_date[2].'>'.$weekly_day[2]).'</span></span>
				 	</div>
				 </div>
				 <div class="dp_pec_dayname dp_pec_dayname_wednesday">
					<div class="dp_pec_dayname_item">
						<span>' . mb_substr(self::$translation['DAY_WEDNESDAY'], 0,3, 'UTF-8').' <span class="dp_pec_daynumber" '.(self::$calendar_obj->first_day == 1 ? $special_date[2].'>'.$weekly_day[2] : $special_date[3].'>'.$weekly_day[3]).'</span></span>
				 	</div>
				 </div>
				 <div class="dp_pec_dayname dp_pec_dayname_thursday">
					<div class="dp_pec_dayname_item">
						<span>' . mb_substr(self::$translation['DAY_THURSDAY'], 0,3, 'UTF-8').' <span class="dp_pec_daynumber" '.(self::$calendar_obj->first_day == 1 ? $special_date[3].'>'.$weekly_day[3] : $special_date[4].'>'.$weekly_day[4]).'</span></span>
				 	</div>
				 </div>
				 <div class="dp_pec_dayname dp_pec_dayname_friday">
					<div class="dp_pec_dayname_item">	
						<span>' . mb_substr(self::$translation['DAY_FRIDAY'], 0,3, 'UTF-8').' <span class="dp_pec_daynumber" '.(self::$calendar_obj->first_day == 1 ? $special_date[4].'>'.$weekly_day[4] : $special_date[5].'>'.$weekly_day[5]).'</span></span>
				 	</div>
				 </div>
				 <div class="dp_pec_dayname dp_pec_dayname_saturday">
					<div class="dp_pec_dayname_item">	
						<span>' . mb_substr(self::$translation['DAY_SATURDAY'], 0,3, 'UTF-8').' <span class="dp_pec_daynumber" '.(self::$calendar_obj->first_day == 1 ? $special_date[5].'>'.$weekly_day[5] : $special_date[6].'>'.$weekly_day[6]).'</span></span>
				 	</div>
				 </div>
				 ';

		if( self::$calendar_obj->first_day == 1 ) 
		{
		
			$html .= '
				 <div class="dp_pec_dayname dp_pec_dayname_sunday">
				 	<div class="dp_pec_dayname_item">
						<span>'.mb_substr(self::$translation['DAY_SUNDAY'], 0,3, 'UTF-8').' <span class="dp_pec_daynumber" '.$special_date[6].'>'.$weekly_day[6].'</span></span>
				 	</div>
				 </div>';
		
		}

		$weekly_events_view = array();
		
		for( $x = 1; $x <= 7; $x++ ) 
		{
			
			$html_tmp = "";
			$html_list = "";
			$disabled = "";
			$curDate = date( 'Y-m-d', $week_days[$x - 1] );
			
			
				if( !(self::$calendar_obj->hide_old_dates || self::$opts['hide_old_dates']) || $curDate >= current_time('Y-m-d') ) 

					$result = $this->getEventsByDate($curDate);

				if( (self::$calendar_obj->hide_old_dates || self::$opts['hide_old_dates']) && $curDate < current_time('Y-m-d') ) 
				
					$disabled = "disabled";

				if( ! is_array( $result ) ) 
				
					$result = array();	
				
				foreach( $result as $event ) 
				{
	
					if( $event->id == "" ) 
						$event->id = $event->ID;
					
					$event = (object)array_merge( (array)self::getEventData( $event->id ), (array)$event );

					if( $event->pec_daily_working_days && $event->recurring_frecuency == 1 && ( date( 'w', strtotime($curDate) ) == "0" || date('w', strtotime($curDate) ) == "6" ) ) 
						continue;
					
					$event_hour = date('G', strtotime($event->date));
					$event_minute = date('i', strtotime($event->date));
					$event_hour_end = ltrim($event->end_time_hh, "0");
					$event_minute_end = ltrim($event->end_time_mm, "0");
	
					if( $event_hour_end <= $event_hour ) 
						$event_hour_end = $event_hour + 1;
					
					$time = self::date_i18n( self::$time_format, strtotime($event->date) );
					
					$end_datetime = self::get_end_datetime( $event, true );
					$end_time = $end_datetime['end_time'];

					$title = $event->title;
					
					$event_timezone = dpProEventCalendar_getEventTimezone($event->id);

					if( self::$calendar_obj->show_time && ! $event->hide_time && ! $event->all_day ) 
					{
					
						if( self::$calendar_obj->daily_weekly_layout == 'list' )
							$title = '<strong>' . $time . $end_time . ( self::$calendar_obj->show_timezone && ! $event->all_day ? ' ' . $event_timezone : '' ) . '</strong></br>' . $title;
						else 
							$title = '<span>' . $time . '</span>' . $title;
					
					}
					
					$href = '';
					if( self::$calendar_obj->link_post ) 
						
						$href = self::get_permalink ( $event, $curDate . ' ' . date( 'H:i:s', strtotime( $event->date ) ) );

					else

						$href = '#';

					$height = '';
					$top = '';

					if( self::$calendar_obj->daily_weekly_layout == 'schedule' && ! $event->all_day ) 
					{

						// Calculate height

						$minutes_parsed = $this->parse_minutes( $event_minute );
						$minutes_parsed_end = $this->parse_minutes( $event_minute_end );

						if( $event_hour_end > ( self::$calendar_obj->limit_time_end + 1 ) )
							$event_hour_end = (self::$calendar_obj->limit_time_end +1);

						$height = ( $event_hour_end - $event_hour ) * 80 - ( 20 * $minutes_parsed );

						$height += ( 20 * $minutes_parsed_end );

						$height = 'height: ' . $height . 'px;';

						$top = 'margin-top: ' . ( 20 * $minutes_parsed ) . 'px;';


					}

					$html_tmp = '<a class="dp_daily_event" style="'.($event->color != "" ? 'border-color:'.$event->color.';' : '').$height.$top.'" data-dppec-event="'.$event->id.'" href="'.$href.'" target="'.self::$calendar_obj->link_post_target.'" data-dppec-date="'.$curDate.'">'.$title;
					
					$html_tmp .= self::getBookingBookedLabel($event->id, $event->date);

					$html_tmp .= '</a>';

					if( self::$calendar_obj->daily_weekly_layout == 'schedule' ) 
					{
						
						$z = $event_hour;
						if( $event->all_day )
							$z = 'all_day';

						$weekly_events_view[$x][$z][] = $html_tmp;

					}	
					
					$html_list .= $html_tmp;
					
				}
				

			// Responsive Layout
			$html .= '<div class="dp_pec_responsive_weekly">';

			$html .= '<div class="dp_pec_clear"></div>
						<div class="dp_pec_dayname">
							<div class="dp_pec_dayname_item">
								<span>' . self::$translation['DAY_'.strtoupper(date('l', strtotime($curDate)))] . ' ' . date('d', strtotime($curDate)) . '</span>
							</div>
					 </div>
					 <div class="dp_pec_clear"></div>';

			$html .= '<div class="dp_pec_date ' . $disabled . '"><div class="dp_pec_date_item">';

			if( $html_list != "" ) 
				$html .= $html_list;
			else 
				$html .= '<span class="pec_no_events_found"></span>';

			$html .= '</div></div>';
			$html .= '</div>';
				
			if( self::$calendar_obj->daily_weekly_layout == 'list' ) 
			{
				
				$html .= '<div class="dp_pec_date '.$disabled.'" '.($x == 1 ? 'style="margin-left: 0; margin-right: 0;"' : '').'>';
				$html .= '<div class="dp_pec_date_item">';
				
				if( $html_list != "" ) {

					$html .= '<div class="pec_monthlyDisplayEvents">';
					$html .= $html_list;
					$html .= '</div>';
				
				} else {
					
					$html .= '<span class="pec_no_events_found"></span>';
					
				}
				$html .= '</div>';
				$html .= '</div>';
			}
				
		
		}
		
		if( self::$calendar_obj->daily_weekly_layout == 'schedule' ) 
		{

			for( $x = 1; $x <= 7; $x++ ) 
			{

				$disabled = "";
				$curDate = date( 'Y-m-d', $week_days[$x - 1] );
				
				if( ( self::$calendar_obj->hide_old_dates || self::$opts['hide_old_dates'] ) && $curDate < current_time( 'Y-m-d' ) ) 
					$disabled = "disabled";
			
				$html .= '<div class="dp_pec_date dp_pec_date_all_day dp_pec_date_' . strtolower( date('l', strtotime($curDate))) . ' ' . $disabled . ($x == 1 ? ' dp_pec_first_column' : '').'" '.($x == 1 ? 'style="margin-left: 0;"' : '') . '>';

				if( $x == 1 ) 
				{
				
					$html .= '
							<div class="dp_pec_date_weekly_time">
								<div class="dp_date_head"><span>'.self::$translation['TXT_ALL_DAY'].'</span></div>
							</div>';

				}

				$html .= '<div class="dp_pec_date_item">';

				if( isset( $weekly_events_view[$x]['all_day'] ) && is_array( $weekly_events_view[$x]['all_day'] ) )
				{
					foreach( $weekly_events_view[$x]['all_day'] as $z ) 
					{
					
						$html .= $z;
					
					}
				}

				// Add button
				$html .= self::add_event_button( true, strtotime($curDate) );
					

				$html .= 	'</div>';

				$html .= '</div>';

			}
		
			for($i = self::$calendar_obj->limit_time_start; $i <= self::$calendar_obj->limit_time_end; $i++) 
			{
			
				$min = '00';
				$hour = $i;
				
				if( dpProEventCalendar_is_ampm() ) 
				{
				
					$min = ($i >= 12 ? __('PM', 'dpProEventCalendar') : __('AM', 'dpProEventCalendar'));
					$hour = ($i > 12 ? $i - 12 : $i);
				
				}
				
				for( $x = 1; $x <= 7; $x++ ) 
				{
				
					$disabled = "";
					$curDate = date('Y-m-d', $week_days[$x - 1]);
					
					if((self::$calendar_obj->hide_old_dates || self::$opts['hide_old_dates']) && $curDate < current_time('Y-m-d')) 
					
						$disabled = "disabled";

					

					if( isset( $weekly_events_view[$x][$i] ) ) 
					{
					
						$html .= '<div class="dp_pec_date dp_pec_date_'.strtolower(date('l', strtotime($curDate))).' '.$disabled. ($x == 1 ? ' dp_pec_first_column' : '').'" '.($x == 1 ? 'style="margin-left: 0;"' : '').'>';
						
						if( $x == 1 ) 
						{
						
							$html .= '
									<div class="dp_pec_date_weekly_time">
								<div class="dp_date_head"><span>'.$hour.'</span><span class="dp_pec_minutes">'.$min.'</span></div>
							</div>';

						}

						$html .= '<div class="dp_pec_date_item">';

						foreach( $weekly_events_view[$x][$i] as $z ) 
						{
						
							$html .= $z;
						
						}

						// Add button
						$html .= self::add_event_button( true, strtotime( $curDate . ' ' . $i . ':00:00' ) );
								
						
						$html .= 	'</div>';
						$html .= '</div>';
					
					} else {
	
						$html .= '<div class="dp_pec_date dp_pec_date_'.strtolower(date('l', strtotime($curDate))).' '.$disabled. ($x == 1 ? ' dp_pec_first_column' : '').'" '.($x == 1 ? 'style="margin-left: 0;"' : '').'>';

						if( $x == 1 ) 
						{
						
							$html .= '
									<div class="dp_pec_date_weekly_time">
								<div class="dp_date_head"><span>'.$hour.'</span><span class="dp_pec_minutes">'.$min.'</span></div>
							</div>';

						}

						$html .= '<div class="dp_pec_date_item">';

						// Add button
						$html .= self::add_event_button( true, strtotime($curDate . ' ' . $i . ':00:00' ) );
							

						$html .= '</div>';

						$html .= '</div>';
		
					}
				
				}
			}
		}
		$html .= '<div class="dp_pec_clear"></div>';
		$html .= '</div>';
		
		return $html;
	}
	
	function getSpecialDates( $date, $current = false ) 
	{
	
		global $wpdb;
		
		if( ! is_numeric( self::$id_calendar ) || ! isset( $date ) ) return false;
		
		$current_date = '';

		if( $current && $date == date( 'Y-m-d' ) )
		{

			$current_date =  new stdClass();
			$current_date->title = self::$translation['TXT_CURRENT_DATE'];
			$current_date->color = self::$calendar_obj->current_date_color;

			if( $current_date->color == '' )

				$current_date->color = DP_PRO_EVENT_CALENDAR_CURRENT_DATE_COLOR;

		}

		$querystr = "
		SELECT sp.id, sp.color, sp.title
		FROM " . DP_PRO_EVENT_CALENDAR_TABLE_SPECIAL_DATES . " sp
		INNER JOIN " . DP_PRO_EVENT_CALENDAR_TABLE_SPECIAL_DATES_CALENDAR . " spc ON spc.special_date = sp.`id`
		WHERE spc.calendar = %d AND spc.`date` = %s LIMIT 1 ";

		$result = $wpdb->get_row( $wpdb->prepare( $querystr,  self::$id_calendar, $date ), OBJECT );
		
		return $result ? $result : $current_date;
	
	}
	
	function setSpecialDates( $sp, $date ) 
	{
	
		global $wpdb;
		
		if( ! is_numeric( self::$id_calendar ) || ! isset( $date ) ) return false;

		$where = array( 'calendar' => self::$id_calendar, 'date' => $date );
		$where_format = array( '%d', '%s' );

		$wpdb->delete( DP_PRO_EVENT_CALENDAR_TABLE_SPECIAL_DATES_CALENDAR, $where, $where_format );
		
		if( is_numeric( $sp ) ) 
		{

			$data = array( 'special_date' => $sp, 'calendar' => self::$id_calendar, 'date' => $date );
			$format = array( '%d', '%d', '%s' );
			$wpdb->insert( DP_PRO_EVENT_CALENDAR_TABLE_SPECIAL_DATES_CALENDAR, $data, $format );

		}

		
		return;
	
	}
	
	protected static function getSpecialDatesList() 
	{
	
		global $wpdb;

		if( self::$special_dates_list != null )
			return self::$special_dates_list;

		$querystr = "SELECT * FROM " . DP_PRO_EVENT_CALENDAR_TABLE_SPECIAL_DATES;

		self::$special_dates_list = $wpdb->get_results( $querystr, OBJECT );
		
		return self::$special_dates_list;
	
	}
	
	protected static function getSpecialDatesById( $id ) 
	{
	
		global $wpdb;
		
		if( ! is_numeric( $id ) ) return "";

		if( isset( self::$special_date[ $id ] ) && self::$special_date[ $id ] != '' )
			return self::$special_date[ $id ]->color;

		$querystr = $wpdb->prepare( "SELECT * FROM " . DP_PRO_EVENT_CALENDAR_TABLE_SPECIAL_DATES . " sp WHERE sp.id = %d LIMIT 1", $id );

		$result = $wpdb->get_row( $querystr, OBJECT );
		
		if( empty( $result ) ) return "";

		self::$special_date[ $id ] = $result;
		
		return $result->color;
	
	}

	protected static function get_event_color( $id ) 
	{

		if( ! is_numeric( $id ) ) return "";

		$event_color = '';

		$category_list = get_the_terms( $id, 'pec_events_category' ); 
		$category_color = "";

		if( ! empty( $category_list ) ) 
		{
		
			foreach ( $category_list as $cat)
			{
				
				$cat_color = get_term_meta( $cat->term_id, 'color', true );

				if( isset( $cat_color ) && is_numeric( $cat_color ) ) 
				{
				
					$category_color = $cat_color;
					break;
				
				}
			
			}
		
		}

		$event_color_id = get_post_meta( $id, 'pec_color', true );

		if( $event_color_id == "" && $category_color != "" )
			$event_color_id = $category_color;

		$event_color = self::getSpecialDatesById( $event_color_id );

		return $event_color;


	}
	
	function getEventsByDate( $date, $count = false ) 
	{
		
		if( ! is_numeric( self::$id_calendar ) || ! isset( $date ) ) return false;
		
		self::$limit = 0;

		$event_list = self::upcomingCalendarLayout( true, 5, '', $date . ' 00:00:00', $date . ' 23:59:59' );
		
		return $event_list;
		
		$events = array();
		
		foreach( $events_obj as $event ) 
		{
			
			if($event->id == "") 
				$event->id = $event->ID;
				
			$event = (object)array_merge((array)self::getEventData($event->id), (array)$event);

			$pec_weekly_day = @unserialize( $event->pec_weekly_day );
			
			if( $event->recurring_frecuency == 2 && is_array( $pec_weekly_day ) ) 
			{
			
				$original_date = $event->date;
				foreach( $pec_weekly_day as $week_day ) 
				{
				
					$day = "";
					
					switch( $week_day ) 
					{
					
						case 1:
							$day = "Monday";
							break;	
					
						case 2:
							$day = "Tuesday";
							break;	
					
						case 3:
							$day = "Wednesday";
							break;	
					
						case 4:
							$day = "Thursday";
							break;	
					
						case 5:
							$day = "Friday";
							break;	
					
						case 6:
							$day = "Saturday";
							break;	
					
						case 7:
							$day = "Sunday";
							break;	
					
					}
					
					if( date( 'l', strtotime( $date ) ) == $day ) 
					{
					
						$original_date = date( "Y-m-d H:i:s", strtotime( "-1 day", strtotime( $original_date) ) );
						$event->date = date( "Y-m-d", strtotime( "next " . $day, strtotime( $original_date) ) ) . ' ' . date( "H:i:s", strtotime( $original_date ) );
						$events[] = $event;
						
					}
					
				}

			} elseif( $event->recurring_frecuency == 3 && $event->pec_monthly_day != "" && $event->pec_monthly_position != "" ) {

				$original_date = $event->date;
				//echo date('l', strtotime($date));

				if( strtolower( date( 'Y-m-d', strtotime( $date ) ) ) == date( 'Y-m-d', strtotime( $event->pec_monthly_position . ' ' . $event->pec_monthly_day . ' of ' . date( "F Y", strtotime( $date ) ) ) ) ) 
				{
				
					//$event->date = date("Y-m", strtotime($original_date)). '-'.date("d", strtotime($date)). ' '.date("H:i:s", strtotime($original_date));
					//die("OKKK");
					//$original_date = date("Y-m-d H:i:s", strtotime("-1 day", strtotime($original_date)));
					//$event->date = date('Y-m-d', strtotime($event->pec_monthly_position.' '.$event->pec_monthly_day.' of '.date("F Y", strtotime($date)))). ' '.date("H:i:s", strtotime($original_date));
					//echo date('Y-m-d', strtotime($event->pec_monthly_position.' '.$event->pec_monthly_day.' of '.date("F Y", strtotime($date)))). ' '.date("H:i:s", strtotime($original_date))." ".$event->pec_monthly_position.' '.$event->pec_monthly_day.' of '.date("F Y", strtotime($date));
					$events[] = $event;
					
				}

			} else {
				
				$events[] = $event;
			
			}
		
		}
		
		return $events;
	}

	function parse_minutes ( $event_minute ) 
	{

		$minutes_parsed = 0;

		if ( $event_minute >= 0 && $event_minute < 15 ) {
			$minutes_parsed = 0;
		} else if ( $event_minute >= 15 && $event_minute < 30 ) {
			$minutes_parsed = 1;
		} else if ( $event_minute >= 30 && $event_minute < 45 ) {
			$minutes_parsed = 2;
		} else if ( $event_minute >= 45 ) {
			$minutes_parsed = 3;
		}

		return $minutes_parsed;
	}

	function display_head ( $date, $return_btn = true ) 
	{

		$start_date = $this->parseMysqlDate( $date );
			
		$start_date_year = date( 'Y', strtotime( $date ) );
		$start_date_formatted = $start_date;

		$special_date = "";
		$special_date_obj = $this->getSpecialDates( $date );
		$special_date_title = "";

		if( is_object( $special_date_obj ) )
		{

			if( $special_date_obj->color ) 
				$special_date = " style='background-color: ".$special_date_obj->color.";' ";

			if( $special_date_obj->title ) 
				$special_date_title = $special_date_obj->title;

		}
		
		if( $date == date( "Y-m-d", current_time('timestamp') ) ) 
			$special_date_title = self::$translation['TXT_TODAY'];

		$html = '<div class="dp_pec_date_block_wrap dp_pec_date_event_wrap dp_pec_isotope">';

		$html .= '<div class="dp_pec_date_block_inner">';

		if( $special_date_title != '' ) 
			$html .= '<div class="dp_pec_date_block_sd">' . $special_date_title . '</div>';

		$html .= '<span' . $special_date . '><i class="fa fa-calendar"></i></span>';

		$html .= '<div class="dp_pec_date_block">' . $start_date_formatted . '</div>';

		$html .= '</div>';

		if($return_btn) 
			$html .= self::back_button();

		$html .= '<div class="dp_pec_clear"></div>';

		$html .= '</div>';

		$html .= '<div class="dp_pec_clear"></div>';

		return $html;

	}
	
	function display_event( $date , $event) 
	{

		$html = $this->display_head( $date );

		$result = self::getEventData( $event );
		
		$html .= $this->singleEventLayout( $result, false, $date, true, '', true );

		return $html;


	}

	function eventsListLayout($date, $return_btn = true) 
	{

		if( ! is_numeric( self::$id_calendar ) || ! isset( $date ) ) return false;
		
		$html = $this->display_head( $date, $return_btn );
		
		if( is_numeric( self::$columns ) && self::$columns > 1 ) 
			$html .= '<div class="dp_pec_date_event_wrap dp_pec_columns_' . self::$columns . '"></div>';
		
		$result = $this->getEventsByDate( $date );

		if( count( $result ) == 0 ) 
		
			$html .= self::no_events_found();

		else
			
			$html .= $this->singleEventLayout( $result, false, $date, true, '', true );
			
		return $html;

	}
	
	function getSearchResults( $key, $type = '' ) 
	{
	
		global $wpdb;

		if( ! is_numeric( self::$id_calendar ) || ! isset( $key ) ) return false;
		
		$html = '';

		if( $type == '' ) 
		{
		
			$html = '<div class="dp_pec_date_block_wrap dp_pec_date_event_search">';

			$html .= '<div class="dp_pec_date_block">'.self::$translation['TXT_RESULTS_FOR'].'</div>';

			$html .= self::back_button();

			$html .= '<div class="dp_pec_clear"></div>';

			$html .= '</div>';

			$html .= '<div class="dp_pec_clear"></div>';

			$html .= '<div class="dp_pec_search_results">';

		}

		if( $type == 'accordion' ) {

			$html .= $this->eventsMonthList(null, null, 10, $key);

		} else {

			$result = self::upcomingCalendarLayout( true, 10, '', null, null, true, false, true, false, false, $key );
			
			if( count( $result ) == 0 ) 
				$html .= self::no_events_found();
			else
				$html .= $this->singleEventLayout($result, true, null, true, $type);

		}

		if( $type == '' ) 
			$html .= '</div>';

		return $html;
	}
	
	function singleEventLayout( $result, $search = false, $selected_date = null, $show_end_date = true, $type = '', $force = false ) 
	{
		
		$html = "";
		$daily_events = array();

		$pagination = self::get_pagination_number();
		
		$i = 0;
		
		if( is_object( $result ) ) 
			$result = array( $result );
		
		if( ! is_array( $result ) ) 
			$result = array();	
		
		foreach( $result as $event ) 
		{
			
			if( $event->id == "" ) 
				$event->id = $event->ID;
			
			$event = (object)array_merge((array)self::getEventData( $event->id ), (array)$event);

			if( $event->recurring_frecuency == 1 )
			{
				
				if(in_array($event->id, $daily_events)) 
					continue;	
				
				$daily_events[] = $event->id;
			
			}

			$event_timezone = dpProEventCalendar_getEventTimezone($event->id);

			if( $selected_date != "" && $event->pec_exceptions != "" ) 
			{
			
				$exceptions = explode(',', $event->pec_exceptions);
				
				if($event->recurring_frecuency != "" && in_array($selected_date, $exceptions)) 
					continue;

			}
			
			if($selected_date != "" && $event->pec_daily_working_days && $event->recurring_frecuency == 1 && (date('w', strtotime($selected_date)) == "0" || date('w', strtotime($selected_date)) == "6")) 
				continue;
			
			if($selected_date != "" && !$event->pec_daily_working_days && $event->recurring_frecuency == 1 && $event->pec_daily_every > 1 && 
				( ((strtotime($selected_date) - strtotime(substr($event->orig_date,0,11))) / (60 * 60 * 24)) % $event->pec_daily_every != 0 )
			) 
				continue;
			
			if($selected_date != "" && !$force && $event->recurring_frecuency == 2 && $event->pec_weekly_every > 1 && 
				( ((strtotime($selected_date) - strtotime(substr($event->orig_date,0,11))) / (60 * 60 * 24)) % ($event->pec_weekly_every * 7) != 0)) 
				continue;
			
			if($selected_date != "" && $event->recurring_frecuency == 3 && $event->pec_monthly_every > 1 && 
				( !is_int (((date('m', strtotime($selected_date)) - date('m', strtotime(substr($event->orig_date,0,11))))) / ($event->pec_monthly_every)) )
			) 
				continue;

			$i++;
			
			if( self::$limit < $i && self::$limit > 0 ) break;
			
			$time = self::date_i18n( self::$time_format, strtotime( $event->date ) );

			$start_day = date( 'd', strtotime( $event->date ) );
			$start_month = date( 'n', strtotime( $event->date ) );
			$start_year = date( 'Y', strtotime( $event->date ) );
			
			$end_datetime = self::get_end_datetime( $event );

			$end_year = $end_datetime['end_year'];
			$end_date = $end_datetime['end_date'];
			$end_time = $end_datetime['end_time'];
			
			//$start_date = $start_day.' '.substr(self::$translation['MONTHS'][($start_month - 1)], 0, 3);
			$start_date = self::date_i18n( get_option('date_format'), strtotime( $event->date ) );
			
			
			if( $start_year != $end_year && $end_year != "" ) 
				$start_date .= ', '.$start_year;
			
			if( isset($event->all_day) && $event->all_day ) 
			{
			
				$time = self::$translation['TXT_ALL_DAY'];
				$end_time = "";
			
			}

			$status = self::get_status_label( $event->status );
			if( $status != '' )
			{

				$time = $status;
				$end_time = "";

			}
			
			$post_thumbnail_id = get_post_thumbnail_id( $event->id );
			$image_attributes = wp_get_attachment_image_src( $post_thumbnail_id, 'large' );
			
			$title = $event->title;
			$permalink = "";
			
			if( self::$calendar_obj->link_post ) 
			{

				$permalink = self::get_permalink( $event, $selected_date . ' ' .date('H:i:s', strtotime($event->date)) );

				if( $selected_date != "" ) 
					$title = '<a href="' . $permalink . '" target="' . self::$calendar_obj->link_post_target . '">' . $title . '</a>';	
				else
					$title = '<a href="' . $permalink . '" target="' . self::$calendar_obj->link_post_target . '">' . $title . $selected_date . '</a>';					
				
			}
			
			$all_working_days = '';

			if( $event->pec_daily_working_days && $event->recurring_frecuency == 1 ) 
				$all_working_days = self::$translation['TXT_ALL_WORKING_DAYS'];
			
			$post_thumbnail_id = get_post_thumbnail_id( $event->id );

			$html .= '
			<div class="dp_pec_date_event_wrap dp_pec_date_event'.($post_thumbnail_id ? ' dp_pec_date_event_has_image' : '').' '.($search ? 'dp_pec_date_eventsearch' : '').' dp_pec_isotope dp_pec_columns_'.self::$columns .'" data-event-number="' . $i . '" ' . ( $i > $pagination ? 'style="display:none;"' : '').'>';

					
					
			if( $post_thumbnail_id ) {
			
				$image_attributes = wp_get_attachment_image_src( $post_thumbnail_id, (is_numeric(self::$columns) && self::$columns > 2 ? 'medium' : 'full') );

				$image_attributes_full = wp_get_attachment_image_src( $post_thumbnail_id, 'full' );

				$html .= '<div class="dp_pec_date_event_image" style="background-image:url(' . $image_attributes[0] . ');">';
				$html .= '<div class="dp_pec_date_event_image_zoom" data-img-url="' . $image_attributes_full[0] . '"><i class="fa fa-search" aria-hidden="true"></i></div>';
				$html .= '</div>';
			
			}

			$html .= '<div class="dp_pec_date_event_data">';

			$html .= self::get_more_options( $event );

			// Display Featured Tag
			$featured_tag = self::display_featured_tag( $event, true, false, true );

			$categories = self::display_meta( $event, array( 'category' ) );

			$top_head = false;

			if( $featured_tag != '' || $categories != '' ) 
			{
				
				$top_head = true;

				$html .= '<div class="dp_pec_single_event_head">';

				$html .= $featured_tag;

				$html .= $categories;
				
				$html .= '</div>';

			}
			
			if( $search || ! empty( self::$event ) ) 
			{

				// To Be Confirmed ?
				if( $event->tbc ) 
				{ 
				
					$html .= '<span class="dp_pec_date_time"><i class="fa fa-calendar"></i>' . self::$translation['TXT_TO_BE_CONFIRMED'] . '</span>';
					$end_date = "";
				
				} else {
				
					$html .= '<span class="dp_pec_date_time"><i class="fa fa-calendar"></i>' . $start_date . $end_date . '</span>';
					$end_date = "";
				
				}
			
			}

			$html .= '<div class="dp_pec_clear"></div>';
				
			$pec_time = ($all_working_days != '' ? $all_working_days.' ' : '').(((self::$calendar_obj->show_time && !$event->hide_time) || $event->all_day) ? $time.$end_time.$end_date.(self::$calendar_obj->show_timezone && !$event->all_day && $status == '' ? ' '.$event_timezone : '') : '');

			if( ! post_password_required( $event->id ) ) 
			{
		
				if( $pec_time != "" && ! $event->tbc ) 
					$html .= '<span class="dp_pec_date_time">' . $pec_time . '</span>';
				
			}

			
			$html .= '<div class="dp_pec_clear"></div>';
			$html .= '<h2 class="dp_pec_event_title">' . $title . '</h2>';
			$html .= '<div class="dp_pec_clear"></div>';
			
			
			if( $this->type != 'compact' ) 
			{
			
				$booking_booked = self::getBookingBookedLabel($event->id, ($selected_date != null ? $selected_date : date('Y-m-d', strtotime($event->date))));
				
				if( $booking_booked == "" ) 
					$html .= self::get_booking_button($event->id, ($selected_date != null ? $selected_date : date('Y-m-d', strtotime($event->date))), false);
				else
					$html .= $booking_booked;
				
				// Display Event meta
				$html .= self::display_meta ( $event, array( 'location', 'speakers', 'organizer' ) );

				$html .= self::show_description( $event, $permalink );

				$html .= '</div>';

			} else {

				// Display Event meta
				$html .= self::display_meta ( $event, array( 'location', 'speakers', 'organizer' ) );

				$html .= '</div>';
				
			}

			$html .= '</div>';
		
		}
		
		if( $i > $pagination ) 
		
			$html .= '<a href="#" class="pec_action_btn dpProEventCalendar_load_more" data-total="'.$i.'" data-pagination="'.$pagination.'">'.self::$translation['TXT_MORE'].'</a>';
		
		return $html;
	}

	public static function upcomingCalendarLayout( 
		$return_data = false, 
		$limit = '', 
		$limit_description = '', 
		$events_month = null, 
		$events_month_end = null, 
		$show_end_date = true, 
		$filter_author = false, 
		$auto_limit = true, 
		$filter_map = false, 
		$past = false, 
		$keyword = '',
		$use_featured = true,
		$start_date_from  = '',
		$get_all_dates = false
	) {
		global $wpdb, $dpProEventCalendar_cache;
		
		$pec_cache_id = "";
		$html = "";
		
		$list_limit = self::$limit;
		if(is_numeric($limit)) {
			$list_limit = $limit;	
		}
		
		//if(is_numeric($limit_description)) {
		//	$this->trim_words = $limit_description;	
		//}

		if($start_date_from == '') {
			$current_time_from = current_time('mysql');
			$current_time_from_all_day = date('Y-m-d'). " 00:00:00";
		} else {
			$current_time_from = $start_date_from;
			$current_time_from_all_day = $start_date_from;
		}

		//$current_time_from = $current_time_from_all_day;
		$querystr = "SET SQL_BIG_SELECTS = 1";
		$wpdb->query($querystr);
		
		$args = array( 
			'posts_per_page' 	=> -1, 
			'post_type'			=> DP_PRO_EVENT_CALENDAR_EVENT_POST_TYPE,
			'post_status'		=> array('publish', 'private'),
			'meta_key'			=> 'pec_date',
			'order'				=> 'ASC',
			'lang'				=> (defined('ICL_LANGUAGE_CODE') ? ICL_LANGUAGE_CODE : strtolower(substr(get_locale(),3,2))),
			'orderby'			=> 'meta_value',
			'suppress_filters'  => false
		);
		
		if(!empty(self::$category)) {

			$cat_arr = explode(",",self::$category);
			$cat_slug = array();
			foreach($cat_arr as $key) {

				if(is_numeric($key)) {
					$category_slug = get_term_by('term_id', (int)$key, 'pec_events_category');
					$cat_slug[] = $category_slug->slug;
				}
			}

			$args['pec_events_category'] = implode(",", $cat_slug);

			//$args['taxonomy'] = "pec_events_category";	
			//$args['term'] = $category_slug->slug;
			
			/*$args['tax_query'] = array(
				array(
					'taxonomy' => 'pec_events_category',
					'field' => 'term_id',
					'term' => self::$category
				)
			);*/
		}
		
		if(!is_null($events_month)) {
			
			$args['meta_query'][] = array(
				'relation' => 'OR',
				array(
					'key'     => 'pec_date',
					'value'   => array($events_month, $events_month_end),
					'compare' => 'BETWEEN',
					'type'    => 'DATETIME'
				),
				array(
					'key'     => 'pec_recurring_frecuency',
					'value'   => 0,
					'compare' => '>',
					'type'    => 'NUMERIC'
				),
				array(
					'key'     => 'pec_extra_dates',
					'value'   => '',
					'compare' => '>'
				),
				
			);
			
			$args['meta_query'][] = array(
				'relation' => 'OR',
				array(
					'key'     => 'pec_end_date',
					'value'   => substr($events_month, 0, 10),
					'compare' => '>=',
					'type'    => 'DATETIME'
				),
				array(
					'key'     => 'pec_end_date',
					'value'   => '0000-00-00',
					'compare' => '=',
				),
				array(
					'key'     => 'pec_end_date',
					'value'   => '',
					'compare' => '=',
				),
				array(
					'key'     => 'pec_extra_dates',
					'value'   => '',
					'compare' => '>'
				),
			);

			// To Be Confirmed?
			$args['meta_query'][] = array(
				array(
					'key'     => 'pec_tbc',
					'compare' => 'NOT EXISTS'
				)
			);
			
		} elseif($past) {

			$args['meta_query'][] = array(
				'key'     => 'pec_date',
				'value'   => current_time('mysql'),
				'compare' => '<=',
				'type'    => 'DATETIME'
			);
			
		} elseif( ! $get_all_dates ) {
			
			$args['meta_query'][] = array(
				'relation' => 'OR',
				array(
					'key'     => 'pec_date',
					'value'   => $current_time_from,
					'compare' => '>=',
					'type'    => 'DATETIME'
				),
				array(
					'key'     => 'pec_extra_dates',
					'value'   => '',
					'compare' => '>'
				),
				array(
					'key'     => 'pec_recurring_frecuency',
					'value'   => 0,
					'compare' => '>',
					'type'    => 'NUMERIC'
				)
			);
			
			$args['meta_query'][] = array(
				'relation' => 'OR',
				array(
					'key'     => 'pec_end_date',
					'value'   => substr(current_time('mysql'),0, 10),
					'compare' => '>=',
					'type'    => 'DATETIME'
				),
				array(
					'key'     => 'pec_end_date',
					'value'   => '0000-00-00',
					'compare' => '=',
				),
				array(
					'key'     => 'pec_end_date',
					'value'   => '',
					'compare' => '=',
				),
				array(
					'key'     => 'pec_recurring_frecuency',
					'value'   => 0,
					'compare' => '=',
					'type'    => 'NUMERIC'
				)
			);
		}
		
		
		if(!empty(self::$event_id)) {
			
			$args['p'] = self::$event_id;
		}
		
		if(!empty(self::$author)) {
			
			$args['author'] = self::$author;
		}
		
		if($filter_author) {
			
			global $author_name, $author;

			if(is_author()) {
				
				$curauth = (isset($_GET['author_name'])) ? get_user_by('slug', $author_name) : get_userdata(intval($author));
			} else {
				$curauth = get_userdata(intval($author));
			}
			
			if(is_numeric(self::$author) && self::$author > 0) {			

				$args['author'] = self::$author;

			} else {

				$args['author'] = $curauth->ID;

			}
		}

		if($keyword != "") {

			$args['s'] = $keyword;

		}

		if( ! empty(self::$opts['location'] ) ) {
			$args['meta_query'][] = array(
				'key'     => 'pec_location',
				'value'   => self::$opts['location']
			);
		}

		if( ! empty(self::$opts['speaker'] ) ) {
			$args['meta_query'][] = array(
				'key'     => 'pec_speaker',
				'value'   => self::$opts['speaker'],
				'compare' => 'LIKE'
			);
		}

		if($filter_map) {

			$args['meta_query'][] = array(
				array(
					'key'     => 'pec_location',
					'value'   => 0,
					'compare' => '>',
					'type'	  => 'numeric'
				)
			);
			
		}
		
		// Check Calendar ID
		
		// XXXXXXXXXX
		if(!isset(self::$opts['include_all_events']) || self::$opts['include_all_events'] != 1) {
			$args['meta_query'][] = array(
				'relation' => 'OR',
				array(
					'key'     => 'pec_id_calendar',
					'value'   => self::$id_calendar,
					'compare' => 'LIKE'
				)
			);
		}

		$pec_cache_id = serialize($args);
		$pec_cache_id = md5($pec_cache_id);
		$loaded_from_cache = false;
		$order_events = array();
		$events_obj = array();


		if(!self::$is_admin &&
			!is_null($events_month) &&
			isset($dpProEventCalendar_cache['calendar_id_'.self::$id_calendar]) && 
			isset($dpProEventCalendar_cache['calendar_id_'.self::$id_calendar][$pec_cache_id]) && 
			self::$calendar_obj->cache_active) {
			
			$loaded_from_cache = true;
			if($return_data) {
				return $dpProEventCalendar_cache['calendar_id_'.self::$id_calendar][$pec_cache_id];
			} else {
				$order_events = $dpProEventCalendar_cache['calendar_id_'.self::$id_calendar][$pec_cache_id];
				$events_obj = $order_events;
			}

		}

		if(empty($events_obj)) {

			//$events_obj = $wpdb->get_results($querystr);
			$events_obj = get_posts( $args );
		}


		//echo '<!--';
		//echo '<pre>';
		//print_r($args);
		//echo '</pre>';
		//print_r($events_obj);
		//echo $GLOBALS['wp_query']->request;
		//echo '-->';
		if(count($events_obj) == 0) 
		{
			if($return_data)
			{

				return array();

			} else {

				$html .= self::no_events_found();

			}
			
		} else {

			if(empty($order_events)) {
				$daily_events_total = 0;


				foreach($events_obj as $event) {


					$event = self::getEventData($event->ID);

					$event->ID = $event->id;
					$is_featured = false;

					if ( get_post_status ( $event->ID ) == 'private' ) {
						if(is_user_logged_in()) {
						    $current_user = wp_get_current_user();
						    $is_author = false;
						    if($current_user->ID == get_post_field( 'post_author', $event->ID)) { $is_author = true; }

						    if( ! current_user_can('administrator') || ! $is_author ) {
						        continue;
						    }
						} else {
							continue;
						}


					}	

					$pec_calendars = explode(',', $event->id_calendar);	

					if(!in_array(self::$id_calendar, $pec_calendars) && self::$opts['include_all_events'] != 1) {
						continue;
					}
					
					$pec_extra_dates = explode(',', $event->pec_extra_dates);	
					if( ! is_array( $pec_extra_dates ) ) 
						$pec_extra_dates = array();

					if($event->recurring_frecuency > 0) {
						
						$enddate_orig = $event->end_date." 23:59:59";
						if(isset($event->all_day) && $event->all_day) {
							$startdate_orig = $current_time_from_all_day;
						} else {
							$startdate_orig = $current_time_from;	
						}
						if(!is_null($events_month)) {
							$startdate_orig = $events_month;
							$enddate_orig = $events_month_end;
						}
						if($past) {
							$startdate_orig = date('Y-m-d H:i:s', strtotime('-30 days'));
							$enddate_orig =  current_time('mysql');
							//$startdate_orig = $enddate_orig;
						}

						switch($event->recurring_frecuency) {
							case 1:
								$k = 1;
								
								$startdate = $event->date;
								
								if(strtotime($startdate) < strtotime($startdate_orig)) {
									
									$startdate = date('Y-m-d', strtotime($startdate_orig)). ' ' .date('H:i:s', strtotime($event->date));
										
								}
								
								
								
								$eventdate = date("Y-m-d H:i:s", mktime(date("H", strtotime($startdate)), date("i", strtotime($startdate)), 0, date("m", strtotime($startdate)), date("d", strtotime($startdate)) - 1 +$k, date("y", strtotime($startdate))));

								$i = 0;
								while((
											(strtotime($eventdate) <= strtotime($enddate_orig) && strtotime($eventdate) <= strtotime($event->end_date." 23:59:59")
										) 
										|| $event->end_date == '0000-00-00'
										|| $event->end_date == '') 
										&& (strtotime($eventdate) <= strtotime($enddate_orig))) {	
									
									$i++;

									if(is_null($events_month) && $i >= $list_limit) {
										break;
									}
								//echo "whie 1<br>";
								
								//for($i = 1; $i <= $list_limit; $i++) {
														
									$eventdate = date("Y-m-d H:i:s", mktime(date("H", strtotime($startdate)), date("i", strtotime($startdate)), 0, date("m", strtotime($startdate)), date("d", strtotime($startdate)) - 1 +$k, date("y", strtotime($startdate))));
									//echo "DONE DAILY";
									
									if(!$event->pec_daily_working_days && $event->pec_daily_every > 1 && 
										( ((strtotime(substr($eventdate,0,11)) - strtotime(substr($event->orig_date,0,11))) / (60 * 60 * 24)) % $event->pec_daily_every != 0 )
									) {
										$i--;
											$k++;
										continue;
									}
									
									if($eventdate != "" && $event->pec_exceptions != "") {
										$exceptions = explode(',', $event->pec_exceptions);
										if(in_array(substr($eventdate, 0, 10), $exceptions)) {
											$i--;
											$k++;
											continue;
										}
									}

									if(
										(
											(strtotime($eventdate) <= strtotime($enddate_orig) && strtotime($eventdate) <= strtotime($event->end_date." 23:59:59")
										) 
										|| $event->end_date == '0000-00-00'
										|| $event->end_date == '') 
										&& (strtotime($eventdate) >= strtotime($startdate_orig) && strtotime($eventdate) <= strtotime($enddate_orig))
									) {
										//&& (strtotime(substr($eventdate,0, 10)) >= strtotime(substr($startdate_orig,0, 10)) && strtotime($eventdate) <= strtotime($enddate_orig))
										$order_events[strtotime($eventdate).$event->ID] = new stdClass;
										//$order_events[strtotime($eventdate).$event->ID] = $event;
										$order_events[strtotime($eventdate).$event->ID]->id = $event->ID;
										$order_events[strtotime($eventdate).$event->ID]->date = $eventdate;

										$order_events[strtotime($eventdate).$event->ID]->featured_event = "";
										if(!$is_featured && $use_featured) {
											$order_events[strtotime($eventdate).$event->ID]->featured_event = $event->featured_event;
											$is_featured = true;
										}

										$daily_events_total++;
									} elseif(strtotime($eventdate) < strtotime($startdate_orig)) {
										$i--;
									}
									$k++;
								}
								break;
							case 2:
								
								$k = 1;
								$startdate = $event->date;
								$weeksdiff = 0;
								
								if(strtotime($startdate) < strtotime($startdate_orig)) {
									
									$weeksdiff = dpProEventCalendar_datediffInWeeks($startdate, $startdate_orig);
									//echo "weeksdiff : ".$weeksdiff;
									$startdate = date("Y-m-d H:i:s", strtotime('+'.($weeksdiff - 1).' weeks', strtotime($startdate)));
											
								}
								
								$pec_weekly_day = $event->pec_weekly_day;
								
								if(is_array($pec_weekly_day)) {
									
									$event_date = $startdate;
									$last_day = date("Y-m-d H:i:s", mktime(date("H", strtotime($event_date)), date("i", strtotime($event_date)), 0, date("m", strtotime($event_date)), date("d", strtotime($event_date)), date("y", strtotime($event_date))) - (86400 * 7));
									$original_date = $event->date;


									$original_date = date("Y-m-d H:i:s", strtotime("-1 day", strtotime($original_date)));
								
									//echo "DONE WEEKLY 1";
									
									$eventdate = 0;
									$i = 0;
									while((
												(strtotime($eventdate) <= strtotime($enddate_orig) && strtotime($eventdate) <= strtotime($event->end_date." 23:59:59")) 
												|| $event->end_date == '0000-00-00' 
												|| $event->end_date == ''
											) 
										) {	
										
										//echo "whie 2<br>";
										$i++;

										if(is_null($events_month) && $i >= $list_limit) {
											break;
										}


									//for ($i = 1; $i <= $list_limit; $i++) {
										foreach($pec_weekly_day as $week_day) {
											$day = "";
											switch($week_day) {
												case 1:
													$day = "Monday";
													break;	
												case 2:
													$day = "Tuesday";
													break;	
												case 3:
													$day = "Wednesday";
													break;	
												case 4:
													$day = "Thursday";
													break;	
												case 5:
													$day = "Friday";
													break;	
												case 6:
													$day = "Saturday";
													break;	
												case 7:
													$day = "Sunday";
													break;	
											}
											
											if($weeksdiff == 0 && $week_day > 1 && $week_day < date('N', strtotime($original_date))) {
												
												//continue;	
											}
											
											$event_date = date("Y-m-d H:i:s", strtotime("next ".$day, strtotime($original_date)));
											
											$eventdate = date("Y-m-d", strtotime($last_day.' next '.date("l", strtotime($event_date))));
											
											$eventdate = date("Y-m-d H:i:s", mktime(date("H", strtotime($last_day)), date("i", strtotime($last_day)), 0, date("m", strtotime($eventdate)), date("d", strtotime($eventdate)), date("y", strtotime($eventdate))));
											$last_day = $eventdate;										
											if((!is_null($events_month) || $past) && strtotime(($eventdate)) > strtotime($enddate_orig)) {
												break(2);	
											}
	
											if(strtotime(($eventdate)) < strtotime($startdate_orig)) {
												$i--;
												continue;	
											}

											if(strtotime(($eventdate)) < strtotime($event->date)) {
												$i--;
												continue;	
											}
											
											if($eventdate != "" && $event->pec_exceptions != "") {
												$exceptions = explode(',', $event->pec_exceptions);
												if(in_array(substr($eventdate, 0, 10), $exceptions)) {
													continue;
												}
											}
											

											if($event->pec_weekly_every > 1) {

												$weeksdiff2 = dpProEventCalendar_datediffInWeeks($eventdate, $event->date);

												if( $weeksdiff2 % ($event->pec_weekly_every) != 0 ) {
												//$i--;
												continue;
												}

											}
											
											
											if(
												(
													(strtotime($eventdate) <= strtotime($enddate_orig) && strtotime($eventdate) <= strtotime($event->end_date." 23:59:59")) 
													|| $event->end_date == '0000-00-00' 
													|| $event->end_date == ''
												) 
												&& (strtotime($eventdate) >= strtotime($startdate_orig))
											) {
												$order_events[strtotime($eventdate).$event->ID] = new stdClass;
												//$order_events[strtotime($eventdate).$event->ID] = $event;
												$order_events[strtotime($eventdate).$event->ID]->id = $event->ID;
												$order_events[strtotime($eventdate).$event->ID]->date = $eventdate;
												
												$order_events[strtotime($eventdate).$event->ID]->featured_event = "";
												if(!$is_featured && $use_featured) {
													$order_events[strtotime($eventdate).$event->ID]->featured_event = $event->featured_event;
													$is_featured = true;
												}

												
											}
											
										}

										$k++;
									}

								} else {
									$last_day = date("Y-m-d H:i:s", mktime(date("H", strtotime($startdate)), date("i", strtotime($startdate)), 0, date("m", strtotime($startdate)), date("d", strtotime($startdate)), date("y", strtotime($startdate))) - 86400);

									$eventdate = 0;
									$i = 0;
									while((
												(strtotime($eventdate) <= strtotime($enddate_orig) && strtotime($eventdate) <= strtotime($event->end_date." 23:59:59")) 
												|| $event->end_date == '0000-00-00' 
												|| $event->end_date == ''
											) 
										) {	
										
										//echo "whie 3<br>";
										$i++;

										if(is_null($events_month) && $i >= $list_limit) {
											break;
										}

									//for($i = 1; $i <= $list_limit; $i++) {
									//echo "DONE WEEKLY 2";
										$eventdate = date("Y-m-d", strtotime($last_day.' next '.date("l", strtotime($startdate))));
										$eventdate = date("Y-m-d H:i:s", mktime(date("H", strtotime($last_day)), date("i", strtotime($last_day)), 0, date("m", strtotime($eventdate)), date("d", strtotime($eventdate)), date("y", strtotime($eventdate))));
										$last_day = $eventdate;
										
												
										if((!is_null($events_month) || $past) && strtotime(($eventdate)) > strtotime($enddate_orig)) {
											break;	
										}
										
										if($eventdate != "" && $event->pec_exceptions != "") {
											$exceptions = explode(',', $event->pec_exceptions);
											
											if(in_array(substr($eventdate, 0, 10), $exceptions)) {
												$i--;
												continue;
											}
										}
		
										if(strtotime(($eventdate)) < strtotime($startdate_orig)) {
											$i--;
											continue;	
										}
										
										if($event->pec_weekly_every > 1 && 
											( ((strtotime(substr($eventdate,0,11)) - strtotime(substr($event->date,0,11))) / (60 * 60 * 24)) % ($event->pec_weekly_every * 7) != 0 )
										) {
											//echo "DATEDIFF: ".(strtotime(substr($eventdate,0,11)) - strtotime(substr($event->date,0,11))) / (60 * 60 * 24);
											//$i--;
											continue;
										}
		
										if(
											(
												(strtotime($eventdate) <= strtotime($enddate_orig) && strtotime($eventdate) <= strtotime($event->end_date." 23:59:59")) 
												|| $event->end_date == '0000-00-00' 
												|| $event->end_date == ''
											) 
											&& (strtotime($eventdate) >= strtotime($startdate_orig))
										) {

											$order_events[strtotime($eventdate).$event->ID] = new stdClass;
											//$order_events[strtotime($eventdate).$event->ID] = $event;
											$order_events[strtotime($eventdate).$event->ID]->id = $event->ID;
											$order_events[strtotime($eventdate).$event->ID]->date = $eventdate;

											$order_events[strtotime($eventdate).$event->ID]->featured_event = "";
											if(!$is_featured && $use_featured) {
												$order_events[strtotime($eventdate).$event->ID]->featured_event = $event->featured_event;
												$is_featured = true;
											}
										}
									}
									$k++;

								}

								break;
							case 3:
								$k = 1;
								$startdate = $event->date;
								
								if(strtotime($startdate) < strtotime($startdate_orig)) {
									
									$startdate = date("Y-m-d H:i:s", mktime(date("H", strtotime($event->date)), date("i", strtotime($event->date)), 0, date("m", strtotime($startdate_orig)), date("d", strtotime($event->date)), date("y", strtotime($startdate_orig))));
										
								}
								
								$counter_m = 1;
								if(isset($events_month) || ($event->pec_monthly_day != "" && $event->pec_monthly_position != "")) {
									$counter_m = 0;	
								}

								$eventdate = 0;
								$i = 0;
								while((
											(strtotime($eventdate) <= strtotime($enddate_orig) && strtotime($eventdate) <= strtotime($event->end_date." 23:59:59")) 
											|| $event->end_date == '0000-00-00' 
											|| $event->end_date == ''
										) 
									) {	
									
									$i++;

									if(is_null($events_month) && $i >= $list_limit) {
										break;
									}

								//for($i = 1; $i <= $list_limit; $i++) {
									//echo "DONE MONTHLY";
									$eventdate = date("Y-m-d H:i:s", mktime(date("H", strtotime($startdate)), date("i", strtotime($startdate)), 0, date("m", strtotime($startdate))+((strtotime($startdate) < time() && !isset($events_month)) || $k > 1 ? $counter_m : 0), date("d", strtotime($startdate)), date("y", strtotime($startdate))));
									
									//echo "whie 4 ".$eventdate." - ".$events_month."<br>";

									
									//$html .= $event->pec_monthly_day. " - " .$event->pec_monthly_position;
									if($event->pec_monthly_day != "" && $event->pec_monthly_position != "") {
										
										$eventdate = str_replace(substr($eventdate, 5, 5), date('m-d', strtotime($event->pec_monthly_position.' '.$event->pec_monthly_day.' of '.date("F Y", strtotime($eventdate)))), $eventdate);
										
										/*if($eventdate != "" && $event->pec_exceptions != "") {
											
											if(in_array(substr($eventdate, 0, 10), $exceptions)) {
												// X NO $i--;
												$counter_m++;
												continue;
											}
											
										}*/
										
										if(strtotime(($eventdate)) > strtotime($enddate_orig) && ($event->end_date != '0000-00-00' && $event->end_date != '')) {
											break;	
										}
										//$html .= $eventdate."XXX";

									}
									
									if($eventdate != "" && $event->pec_exceptions != "") {
										
										$exceptions = explode(',', $event->pec_exceptions);
										
										if(in_array(substr($eventdate, 0, 10), $exceptions)) {
											//X NO $i--;
											
											if(isset($events_month)) {
												break;
											}
											
											$counter_m++;
											continue;
										}
										
									}

									if((!is_null($events_month) || $past) && strtotime(($eventdate)) > strtotime($enddate_orig)) {
										break;	
									}
									
									if($event->pec_monthly_every > 1) {

										if(( !is_int (((date('m', strtotime($eventdate)) - date('m', strtotime(substr($event->date,0,11))))) / ($event->pec_monthly_every)))) {

											if(isset($events_month)) {
												break;
											}
											//No
											//$i--;
											$counter_m++;
											continue;
										}
									}
									
									if(strtotime($startdate) < current_time('timestamp') || $i > 1) {
										$counter_m++;
									}
									if(
										(
											(strtotime($eventdate) <= strtotime($enddate_orig) && strtotime($eventdate) <= strtotime($event->end_date." 23:59:59"))
											|| $event->end_date == '0000-00-00' 
											|| $event->end_date == ''
										) 
										&& (strtotime($eventdate) >= strtotime($startdate_orig))
									) {

										if(!isset($order_events[strtotime($eventdate).$event->ID])) {
											$order_events[strtotime($eventdate).$event->ID] = new stdClass;
											//$order_events[strtotime($eventdate).$event->ID] = $event;
											$order_events[strtotime($eventdate).$event->ID]->id = $event->ID;
											$order_events[strtotime($eventdate).$event->ID]->date = $eventdate;

											
											$order_events[strtotime($eventdate).$event->ID]->featured_event = "";
											
											if(!$is_featured && $use_featured) {
												
												$order_events[strtotime($eventdate).$event->ID]->featured_event = $event->featured_event;
												$is_featured = true;
											}
										}
									}
									$k++;
								}
								break;	
							case 4:
								$k = 1;
								$counter_y = 1;
								if(isset($events_month)) {
									$counter_y = 0;	
								}

								$eventdate = 0;
								$i = 0;
								while((
											(strtotime($eventdate) <= strtotime($enddate_orig) && strtotime($eventdate) <= strtotime($event->end_date." 23:59:59")) 
											|| $event->end_date == '0000-00-00' 
											|| $event->end_date == ''
										) 
									) {	
									
									//echo "whie 5 - ".$eventdate."<br>";
									$i++;

									if(is_null($events_month) && $i >= $list_limit) {
										break;
									}

								//for($i = 1; $i <= $list_limit; $i++) {
									$eventdate = date("Y-m-d H:i:s", mktime(date("H", strtotime($event->date)), date("i", strtotime($event->date)), 0, date("m", strtotime($event->date)), date("d", strtotime($event->date)), date("Y", strtotime($event->date))+((strtotime($event->date) < time() && !isset($events_month)) || $k > 1 ? $counter_y : 0)));
									//echo "K: ".$k."<br>";
									//echo "Event date : ".(date("Y", strtotime($event->date))+((strtotime($event->date) < time() && !isset($events_month)) || $k > 1 ? $counter_y : 0))."<br>";
									
									if($eventdate != "" && $event->pec_exceptions != "") {
										$exceptions = explode(',', $event->pec_exceptions);
										
										if(in_array(substr($eventdate, 0, 10), $exceptions)) {
											$i--;
											continue;
										}
									}
									
									if((!is_null($events_month) || $past) && strtotime(($eventdate)) > strtotime($enddate_orig)) {
										break;	
									}
								
									if(strtotime($event->date) < time() || $i > 1) {
										$counter_y++;
										//echo "Counter ".$counter_y." <br>";
										//echo $event->date. ' - '.$eventdate.'<br><br>';

									}
									if(((strtotime($eventdate) <= strtotime($enddate_orig) && strtotime($eventdate) <= strtotime($event->end_date." 23:59:59")) || $event->end_date == '0000-00-00' || $event->end_date == '') && (strtotime($eventdate) >= strtotime($startdate_orig))) {
										$order_events[strtotime($eventdate).$event->ID] = new stdClass;
										//$order_events[strtotime($eventdate).$event->ID] = $event;
										$order_events[strtotime($eventdate).$event->ID]->id = $event->ID;
										$order_events[strtotime($eventdate).$event->ID]->date = $eventdate;

										$order_events[strtotime($eventdate).$event->ID]->featured_event = "";
										if(!$is_featured && $use_featured) {
											$order_events[strtotime($eventdate).$event->ID]->featured_event = $event->featured_event;
											$is_featured = true;
										}
									}
									$k++;
								}
								
								break;
						}
						
					} else {

						$enddate_orig = $event->end_date." 23:59:59";
						if(isset($event->all_day) && $event->all_day) {
							$startdate_orig = $current_time_from_all_day;
						} else {
							$startdate_orig = $current_time_from;	
						}
						if(!is_null($events_month)) {
							$startdate_orig = $events_month;
							$enddate_orig = $events_month_end;
						}
						
						$continue = 0;
						
						if($past) {
							//$startdate_orig = date('Y-m-d H:i:s', strtotime('-30 days'));
							$enddate_orig =  current_time('mysql');
							$startdate_orig = $enddate_orig;
							
							if(strtotime(($event->date)) > strtotime($startdate_orig)) {
								$continue = 1;	
							}

						} else {
							
							if(!is_null($events_month)) {
								if(substr($event->date,0,10) < substr($startdate_orig,0,10) || substr($event->date,0,10) > substr($enddate_orig,0,10)) {
									//if(strtotime(substr($event->date, 0, 10)) < strtotime(substr($startdate_orig, 0, 10))) {
									$continue = 1;	
								}
							} else {
								if(strtotime(($event->date)) < strtotime($startdate_orig)) {
									//if(strtotime(substr($event->date, 0, 10)) < strtotime(substr($startdate_orig, 0, 10))) {
									$continue = 1;	
								}
							}

						}
						
						if(!$continue) {
							/*if($use_featured) {
								if($event->featured_event) {
									$is_featured = true;
								}
							} else {
								$event->featured_event = "";	
							}*/

							//$order_events[strtotime($event->date).$event->ID] = $event;
							$order_events[strtotime($event->date).$event->ID] = new stdClass;
							//$order_events[strtotime($event->date).$event->ID] = $event;
							$order_events[strtotime($event->date).$event->ID]->id = $event->ID;
							$order_events[strtotime($event->date).$event->ID]->date = $event->date;

							$order_events[strtotime($event->date).$event->ID]->featured_event = "";
							if($use_featured) {
								$order_events[strtotime($event->date).$event->ID]->featured_event = $event->featured_event;
								$is_featured = true;
							}
						}
					}
					

					if( ! empty( $pec_extra_dates ) ) 
					{
					
						foreach( $pec_extra_dates as $extra_date ) 
						{

							$extra_date = trim( $extra_date );
							if( $extra_date == "" )
								continue;
							
							if( strlen( trim( $extra_date ) ) <= 12 )
								$extra_date = $extra_date . ' ' . date( 'H:i:s', strtotime( $event->date ) );
							
							$enddate_orig = $event->end_date." 23:59:59";
							if(isset($event->all_day) && $event->all_day)
								$startdate_orig = $current_time_from_all_day;
							else
								$startdate_orig = $current_time_from;	
							
							if( ! is_null( $events_month ) ) {
								$startdate_orig = $events_month;
								$enddate_orig = $events_month_end;
							}
							
							if( $past ) 
							{
								//$startdate_orig = date('Y-m-d H:i:s', strtotime('-30 days'));
								$enddate_orig =  current_time('mysql');
								$startdate_orig = $enddate_orig;
								
								if( strtotime( ( $extra_date ) ) > strtotime( $startdate_orig ) )
									continue;	

							} else {
								
								if( ! is_null( $events_month ) ) 
								{
									
									if(substr($extra_date,0,10) < substr($startdate_orig,0,10) || substr($extra_date,0,10) > substr($enddate_orig,0,10)) {
										//if(strtotime(substr($event->date, 0, 10)) < strtotime(substr($startdate_orig, 0, 10))) {
										continue;	
									}
									
								} else {
									if( strtotime( ( $extra_date ) ) < strtotime( $startdate_orig ) ) {
										//if(strtotime(substr($event->date, 0, 10)) < strtotime(substr($startdate_orig, 0, 10))) {
										continue;	
									}	
								}
		
							}
							
							$order_events[strtotime($extra_date).$event->ID] = new stdClass;
							//$order_events[strtotime($extra_date).$event->ID] = $event;
							$order_events[strtotime($extra_date).$event->ID]->id = $event->ID;
							$order_events[strtotime($extra_date).$event->ID]->date = $extra_date;

							$order_events[strtotime($extra_date).$event->ID]->is_extra_date = 1;

							$order_events[strtotime($extra_date).$event->ID]->featured_event = "";
							if( ! $is_featured && $use_featured ) 
							{
								$order_events[strtotime($extra_date).$event->ID]->featured_event = $event->featured_event;
								$is_featured = true;
							}
						}
					}
				}
			}
			

			if(!function_exists('dp_pec_cmp')) {
				function dp_pec_cmp($a, $b) {

					if ($a->featured_event) {
						if ($b->featured_event) {
							$a = strtotime($a->date);
							$b = strtotime($b->date);

							if ($a == $b) {
								return 0;
							}
							return ($a < $b) ? -1 : 1;
						}
						return -1;
					}
					if ($b->featured_event) {

						return 1;
					}

					$a = strtotime($a->date);
					$b = strtotime($b->date);

					if ($a == $b) {
						return 0;
					}
					return ($a < $b) ? -1 : 1;
				}
			}
			
			if(!function_exists('dp_pec_cmp_reverse')) {
				function dp_pec_cmp_reverse($a, $b) {
					$a = strtotime($a->date);
					$b = strtotime($b->date);
					if ($a == $b) {
						return 0;
					}
					return ($a < $b) ? 1 : -1;
				}
			}
			
			if($past) {
				usort($order_events, "dp_pec_cmp_reverse");
			} else {
				usort($order_events, "dp_pec_cmp");
			}
			
			//ksort($order_events, SORT_NUMERIC);

			if(!self::$is_admin &&
				!$loaded_from_cache &&
				!is_null($events_month) &&
				isset($dpProEventCalendar_cache['calendar_id_'.self::$id_calendar]) && 
				!isset($dpProEventCalendar_cache['calendar_id_'.self::$id_calendar][$pec_cache_id]) && 
				self::$calendar_obj->cache_active) {

				$cache = array(
					'calendar_id_'.self::$id_calendar => array(
						$pec_cache_id => $order_events
					)
				);
				
				if(!$dpProEventCalendar_cache) {
					update_option( 'dpProEventCalendar_cache', $cache);
				} else {
				//} else if(!empty($order_events)) {
						
					//$dpProEventCalendar_cache[] = $cache;
					$dpProEventCalendar_cache['calendar_id_'.self::$id_calendar][$pec_cache_id] = $order_events;

					update_option( 'dpProEventCalendar_cache', $dpProEventCalendar_cache );
				}
			}


			if($return_data) {
				if($limit != '' && $auto_limit && is_null($events_month)) { $order_events = array_slice($order_events, 0, ($limit + $daily_events_total)); }

				return $order_events;
			}
			
			$pagination = self::get_pagination_number();

			$event_counter = 1;
			$event_columns_counter = 0;
			
			if(empty($order_events)) 
			{
			
				$html .= self::no_events_found();

			} else {

				$event_reg = array();
				
				$html .= "<div class='dp_pec_clear'></div>
				<div class='".(is_numeric(self::$columns) && self::$columns > 1 ? 'pec_upcoming_layout' : '')."'>";
				
				$last_date = "";
				$daily_events = array();
				foreach($order_events as $event) {
					
					if($event->id == "") 
						$event->id = $event->ID;

					$event = (object)array_merge((array)self::getEventData($event->id), (array)$event);

					if($event_counter > $list_limit && is_null($events_month)) { break; }

					if($event->recurring_frecuency == 1){
						
						if(in_array($event->id, $daily_events)) {
							continue;	
						}
						
						$daily_events[] = $event->id;
					}

					$all_working_days = '';
					if($event->pec_daily_working_days && $event->recurring_frecuency == 1) {
						$all_working_days = self::$translation['TXT_ALL_WORKING_DAYS'];
						$event->date = $event->orig_date;
					}
					
					if(self::$columns == "")  {
						
						$html .= "<div class='clear'></div>";
						
						$event_columns_counter = 0;
						
					}
					
					$time = self::date_i18n(self::$time_format, strtotime($event->date));
					
					$start_day = date('d', strtotime($event->date));
					$start_month = date('n', strtotime($event->date));
					
					$end_datetime = self::get_end_datetime( $event );
					$end_date = $end_datetime['end_date'];
					$end_time = $end_datetime['end_time'];
					

					//$start_date = $start_day.' '.substr(self::$translation['MONTHS'][($start_month - 1)], 0, 3);
					$start_date = self::date_i18n(get_option('date_format'), strtotime($event->date));
					
					//if($start_date == $end_day.' '.substr(self::$translation['MONTHS'][($end_month - 1)], 0, 3)) { $end_date = ""; }
					if($event->recurring_frecuency != 1) {
						$end_date = "";
					} elseif(in_array($event->id, $event_reg) && (self::$columns > 1)) {
						continue;	
					}
					
					if(isset($event->all_day) && $event->all_day) 
					{
					
						$time = self::$translation['TXT_ALL_DAY'];
						$end_time = "";
					
					}

					$status = self::get_status_label( $event->status );
					if($status != '')
					{

						$time = $status;
						$end_time = "";

					}
					
					if(date('Y-m-d', strtotime($event->date)) != $last_date) 
					{
					
						$last_date = date('Y-m-d', strtotime($event->date));
						$start_date_year = date('Y', strtotime($event->date));
						$start_date_formatted = $start_date;

						if(!is_numeric(self::$columns) || self::$columns <= 1) 
						{
						
							$html .= '
							<div class="dp_pec_columns_1 dp_pec_isotope dp_pec_date_event_wrap dp_pec_date_block_wrap" '.($event_counter > $pagination ? 'style="display:none;"' : '').'>
								
								<span><i class="fa fa-calendar"></i></span>

								<div class="dp_pec_date_block">'.$start_date_formatted.'</div>

								<div class="dp_pec_clear"></div>

							</div>
							<div class="dp_pec_clear"></div>';	
						
						}
					
					}
					
					$html .= '<div class="dp_pec_isotope dp_pec_date_event_wrap dp_pec_columns_' . self::$columns .'" data-event-number="'.$event_counter.'" '.($event_counter > $pagination ? 'style="display:none;"' : '').'>';

					$post_thumbnail_id = get_post_thumbnail_id( $event->id );

					$html .= '<div class="dp_pec_date_event dp_pec_upcoming'.($post_thumbnail_id ? ' dp_pec_date_event_has_image' : '').'">';



					
					
					if($post_thumbnail_id) {
					
						$image_attributes = wp_get_attachment_image_src( $post_thumbnail_id, (is_numeric(self::$columns) && self::$columns > 2 ? 'medium' : 'full') );

						$image_attributes_full = wp_get_attachment_image_src( $post_thumbnail_id, 'full' );

						$html .= '<div class="dp_pec_date_event_image" style="background-image:url('.$image_attributes[0].');">';
						$html .= '<div class="dp_pec_date_event_image_zoom" data-img-url="'.$image_attributes_full[0].'"><i class="fa fa-search" aria-hidden="true"></i></div>';
						$html .= '</div>';
					
					}

					$html .= '<div class="dp_pec_date_event_data">';

					$permalink = "";

					if(self::$calendar_obj->link_post) 
					{
					
						$permalink = self::get_permalink ( $event, $event->date );

						//$title = '<a href="'.$href.'" target="'.self::$calendar_obj->link_post_target.'">'.$title.'</a>';	
					}
					
										
					//<a href="'.dpProEventCalendar_get_permalink($event->id).'"></a>
					if($end_date == ' - '.$start_date) {
						$end_date = '';	
					}

					// Get more options
					$html .= self::get_more_options( $event );

					// Display Featured Tag
					$featured_tag = self::display_featured_tag( $event, true, false, true );

					$categories = self::display_meta( $event, array( 'category' ) );

					$top_head = false;

					if( $featured_tag != '' || $categories != '' ) 
					{
						
						$top_head = true;

						$html .= '<div class="dp_pec_single_event_head">';

						$html .= $featured_tag;

						$html .= $categories;
						
						$html .= '</div>';

					}
					
					if((is_numeric(self::$columns) && self::$columns > 1) || $event->tbc || ( isset ( self::$opts['force_dates'] ) && self::$opts['force_dates'] ) || ($event->recurring_frecuency == 1	)) 
					{
						if($event->tbc) 
							$html .= '<span class="dp_pec_date_time"><i class="fa fa-calendar"></i>'.self::$translation['TXT_TO_BE_CONFIRMED'].'</span>';
						else
							$html .= '<span class="dp_pec_date_time"><i class="fa fa-calendar"></i>'.$start_date.$end_date.'</span>';
					}

					$event_timezone = dpProEventCalendar_getEventTimezone($event->id);
					

					$pec_time = ($all_working_days != '' ? $all_working_days.' ' : '').(((self::$calendar_obj->show_time && !$event->hide_time) || $event->all_day) ?  $time.$end_time.(self::$calendar_obj->show_timezone && !$event->all_day && $status == '' ? ' '.$event_timezone : '') : '');
					if($pec_time != "" && !$event->tbc) {
						$html .= '<span class="dp_pec_date_time">'.$pec_time.'</span>';
					}
					
					
					$html .= self::display_title( $event );

					
					$booking_booked = self::getBookingBookedLabel($event->id, $event->date);

					if($booking_booked == "") 
					{

						$html .= self::get_booking_button($event->id, date('Y-m-d', strtotime($event->date)), false);
					
					} else {
					
						$html .= $booking_booked;
					
					}

					// Display Event meta
					$html .= self::display_meta ( $event, array( 'location', 'speakers', 'organizer' ) );
					
						
					$html .= self::show_description( $event, $permalink );

					$html .= '</div>';
					
					$html .= '
						</div>
					</div>';

					$event_reg[] = $event->id;
					$event_counter++;

				}
				
				$html .= "</div>";	
				
				if(($event_counter - 1) > $pagination) 
				{

					$html .= '<a href="#" class="pec_action_btn dpProEventCalendar_load_more" data-total="'.($event_counter - 1).'" data-pagination="'.$pagination.'">'.self::$translation['TXT_MORE'].'</a>
						<div class="dp_pec_clear"></div>
					';

				}
			}
		}

		return $html;
	}
	
	function parseMysqlDate( $date ) 
	{
		
		$newDate = self::date_i18n(get_option('date_format'), strtotime($date));
		return $newDate;
	}

	protected static function get_status_label ( $status ) 
	{

		$label = '';

		if(isset($status) && $status != '') 
		{
		
			if($status == 'cancelled')
			{

				$label = self::$translation['TXT_CANCELED'];

			}

			if($status == 'postponed')
			{

				$label = self::$translation['TXT_POSTPONED'];

			}
		
		}

		return $label;


	}

	function add_scripts_single( $print = true ) 
	{

		if(!is_singular( DP_PRO_EVENT_CALENDAR_EVENT_POST_TYPE ))
			return;

		$time_translate = array(
            'year' => array(self::$translation['TXT_YEAR'], self::$translation['TXT_YEARS']),
            'month' => array(self::$translation['TXT_MONTH'], self::$translation['TXT_MONTHS']),
            'day' => array(self::$translation['TXT_DAY'], self::$translation['TXT_DAYS']),
            'hour' => array(self::$translation['TXT_HOUR'], self::$translation['TXT_HOURS']),
            'minute' => array(self::$translation['TXT_MINUTE'], self::$translation['TXT_MINUTES'])
        );
		
		$script = '<script type="text/javascript">
		// <![CDATA[
		';

		$script .= 'jQuery(document).ready(function() {';

		$script .= 		'jQuery("#dp_pec_single_content").dpProEventCalendar({';

		$script .= 			'type: "single_page",';

		$script .=			'translate: {day : "'.$time_translate['day'][0].'", days : "'.$time_translate['day'][1].'", hour : "'.$time_translate['hour'][0].'", hours : "'.$time_translate['hour'][1].'", minute : "'.$time_translate['minute'][0].'", minutes : "'.$time_translate['minute'][1].'"}';
		
		$script .= 		'});';

		$script .= '});';

		$script .= '
		
		//]]>
		</script>';
		
		if($print)
			echo $script;	
		else
			return $script;
		
	}
	
	function addScripts( $print = false, $commented = false, $hidden = false ) 
	{

		$script = '';

		if( $commented ) 
			$script .= $this->addScripts(false, false, true);	
		
		$script .= '<script type="text/javascript">
		// <![CDATA[
		';

		if( $commented ) 
			$script .= ' /* PEC Commented Script';	
		
		$map_lat = 0;
		$map_lng = 0;

		if( pec_setting( 'map_default_latlng' ) != "" ) 
		{
			
			$map_lnlat = explode( ",", pec_setting( 'map_default_latlng' ) );
			if( is_numeric( $map_lnlat[0] ) && is_numeric( $map_lnlat[1] ) ) 
			{
			
				$map_lat = $map_lnlat[0];
				$map_lng = $map_lnlat[1];
		
			}
		
		}

		$min_sunday = $this->str_split_unicode( self::$translation['DAY_SUNDAY'], 3 );
		$min_monday = $this->str_split_unicode( self::$translation['DAY_MONDAY'], 3 );
		$min_tuesday = $this->str_split_unicode( self::$translation['DAY_TUESDAY'], 3 );
		$min_wednesday = $this->str_split_unicode( self::$translation['DAY_WEDNESDAY'], 3 );
		$min_thursday = $this->str_split_unicode( self::$translation['DAY_THURSDAY'], 3 );
		$min_friday = $this->str_split_unicode( self::$translation['DAY_FRIDAY'], 3 );
		$min_saturday = $this->str_split_unicode( self::$translation['DAY_SATURDAY'], 3 );

		$script .= '
		jQuery(document).ready(function() {
			
			function startProEventCalendar() {
				
				jQuery("#'.self::$init_id.'").dpProEventCalendar({
					nonce: "'.self::$init_id.'", 
					draggable: false,
					map_lat: '.$map_lat.',
					map_lng: '.$map_lng.',
					columns: "'.self::$columns.'",
					monthNames: new Array("'.self::$translation['MONTHS'][0].'", "'.self::$translation['MONTHS'][1].'", "'.self::$translation['MONTHS'][2].'", "'.self::$translation['MONTHS'][3].'", "'.self::$translation['MONTHS'][4].'", "'.self::$translation['MONTHS'][5].'", "'.self::$translation['MONTHS'][6].'", "'.self::$translation['MONTHS'][7].'", "'.self::$translation['MONTHS'][8].'", "'.self::$translation['MONTHS'][9].'", "'.self::$translation['MONTHS'][10].'", "'.self::$translation['MONTHS'][11].'"), ';
				if( self::$is_admin ) {
					$script .= '
					draggable: false,
					isAdmin: true,
					';
				}
				if( is_numeric( self::$id_calendar ) ) {
					$script .= '
					calendar: '.self::$id_calendar.',
					';	
				}
				if( isset( self::$calendar_obj->date_range_start) && self::$calendar_obj->date_range_start != NULL && !self::$is_admin && empty( self::$event_id ) ) {
					$script .= '
					dateRangeStart: "'.self::$calendar_obj->date_range_start.'",
					';	
				}
				if( isset( self::$calendar_obj->date_range_end) && self::$calendar_obj->date_range_end != NULL && !self::$is_admin && empty(self::$event_id)) {
					$script .= '
					dateRangeEnd: "'.self::$calendar_obj->date_range_end.'",
					';	
				}
				if( isset( self::$calendar_obj->skin ) && self::$calendar_obj->skin != "" && !self::$is_admin && empty( self::$event_id ) ) {
					if(self::$opts['skin'] == 'dark') {

						self::$calendar_obj->skin = 'dark';

					}
					
					$script .= '
					skin: "pec_skin_'.self::$calendar_obj->skin.'",
					';	
				}
				if( isset($this->type) ) {
					$script .= '
					type: "'.$this->type.'",
					';	
				}
				
				if( $hidden ) {
					$script .= '
					selectric: false,
					';	
				}

				if( self::$calendar_obj->hide_old_dates || ( isset( self::$opts['hide_old_dates'] ) && self::$opts['hide_old_dates'] ) ) {
					$script .= '
					hide_old_dates: true,
					';	
				}

				if( isset(self::$opts['include_all_events']) && self::$opts['include_all_events'] ) {
					$script .= '
					include_all_events: 1,
					';	
				}

				if( isset(self::$opts['modal']) && self::$opts['modal'] ) {
					$script .= '
					modal: 1,
					';	
				}

				if( is_numeric(self::$limit) ) {
					$script .= '
					limit: '.self::$limit.',
					';	
				}

				// Check if widget
				if( $this->is_widget() ) {
					$script .= '
					widget: 1,
					';	
				}
				


				if($commented || $hidden) {
					$script .= '
					show_current_date: false,
					';	
				}

				$isRTL = $this->is_rtl();
				
				$script .= '
					isRTL: '.$isRTL.',
					calendar_per_date: '.(isset(self::$opts['calendar_per_date']) && is_numeric(self::$opts['calendar_per_date']) && self::$opts['calendar_per_date'] > 0 ? self::$opts['calendar_per_date'] : 3).',
					allow_user_add_event: "'.self::$calendar_obj->allow_user_add_event.'",
					actualMonth: '.self::$datesObj->currentMonth.',
					actualYear: '.self::$datesObj->currentYear.',
					actualDay: '.self::$datesObj->currentDate.',
					defaultDate: "'.$this->defaultDate.'",
					defaultDateFormat: "'.date('Y-m-d', $this->defaultDate).'",
					datepicker_dateFormat: "'.$this->phpdate_to_datepicker().'",
					current_date_color: "'.self::$calendar_obj->current_date_color.'",
					category: "'.(self::$category != "" ? self::$category : '').'",
					location: "' . ( isset( self::$opts['location'] ) && self::$opts['location'] != "" ? self::$opts['location'] : '' ) . '",
					speaker: "' . ( isset( self::$opts['speaker'] ) && self::$opts['speaker'] != "" ? self::$opts['speaker'] : '' ) . '",
					event_id: "'.(self::$event_id != "" ? self::$event_id : '').'",
					author: "'.(self::$author != "" ? self::$author : '').'",
					lang_sending: "'.addslashes(self::$translation['TXT_SENDING']).'",
					lang_close: "'.addslashes(self::$translation['TXT_CLOSE']).'",
					lang_subscribe: "'.addslashes(self::$translation['TXT_SUBSCRIBE']).'",
					lang_subscribe_subtitle: "'.addslashes(self::$translation['TXT_SUBSCRIBE_SUBTITLE']).'",
					lang_remove_event: "'.addslashes(self::$translation['TXT_REMOVE_EVENT']).'",
					lang_your_name: "'.addslashes(self::$translation['TXT_YOUR_NAME']).'",
					lang_your_email: "'.addslashes(self::$translation['TXT_YOUR_EMAIL']).'",
					lang_fields_required: "'.addslashes(self::$translation['TXT_FIELDS_REQUIRED']).'",
					lang_invalid_email: "'.addslashes(self::$translation['TXT_INVALID_EMAIL']).'",
					lang_txt_subscribe_thanks: "'.addslashes(self::$translation['TXT_SUBSCRIBE_THANKS']).'",
					lang_book_event: "'.addslashes(self::$translation['TXT_BOOK_EVENT']).'",

					'.(self::$calendar_obj->first_day == 1 ? "firstDay: 1," : "firstDay: 0,").'
					monthNames: new Array("'.self::$translation['MONTHS'][0].'", "'.self::$translation['MONTHS'][1].'", "'.self::$translation['MONTHS'][2].'", "'.self::$translation['MONTHS'][3].'", "'.self::$translation['MONTHS'][4].'", "'.self::$translation['MONTHS'][5].'", "'.self::$translation['MONTHS'][6].'", "'.self::$translation['MONTHS'][7].'", "'.self::$translation['MONTHS'][8].'", "'.self::$translation['MONTHS'][9].'", "'.self::$translation['MONTHS'][10].'", "'.self::$translation['MONTHS'][11].'"),
					dayNamesMin: new Array("'.$min_sunday[0].'", "'.$min_monday[0].'", "'.$min_tuesday[0].'", "'.$min_wednesday[0].'", "'.$min_thursday[0].'", "'.$min_friday[0].'", "'.$min_saturday[0].'"),

					view: "'.(self::$is_admin || $this->type == "upcoming" || !empty(self::$event_id) ? 'monthly' : self::$calendar_obj->view).'"
				});

				';

				if(!self::$is_admin && empty(self::$event_id)) 
				{
				
					$script .= '
					jQuery("input, textarea", "#'.self::$init_id.'").placeholder();';
				
				}
				
				$script .= '
			
			}
			
			';
			if( ! $hidden ) 
			{
			
				$script .= '
				if(jQuery("#'.self::$init_id.'").parent().css("display") == "none") {
					jQuery("#'.self::$init_id.'").parent().onShowProCalendar(function(){
						startProEventCalendar();
					});
					return;
				}';
			}
			
			$script .= '
			startProEventCalendar();
		});
		
		jQuery(window).resize(function(){
			if(jQuery(".dp_pec_layout", "#'.self::$init_id.'").width() != null) {

	
				var instance = jQuery("#'.self::$init_id.'");
				
				if(instance.width() < 500) {
					jQuery(instance).addClass("dp_pec_400");
	
					jQuery(".dp_pec_dayname span", instance).each(function(i) {
						jQuery(this).html(jQuery(this).html().substr(0,3));
					});
					
					jQuery(".prev_month strong", instance).hide();
					jQuery(".next_month strong", instance).hide();
					jQuery(".prev_day strong", instance).hide();
					jQuery(".next_day strong", instance).hide();
					
				} else {
					jQuery(instance).removeClass("dp_pec_400");
					jQuery(".prev_month strong", instance).show();
					jQuery(".next_month strong", instance).show();
					jQuery(".prev_day strong", instance).show();
					jQuery(".next_day strong", instance).show();
					
				}
			}
		});
		';

		if( ! empty( self::$event_id ) ) 
		{
		
			$script .= '
			jQuery(".dp_pec_layout", "#'.self::$init_id.'").hide();
			jQuery(".dp_pec_options_nav", "#'.self::$init_id.'").hide();
			jQuery(".dp_pec_add_nav", "#'.self::$init_id.'").hide();';
		
		}
		
		if( $commented ) 
			$script .= ' PEC Commented Script */';	

		$script .= '
		
		//]]>
		</script>';
		
		if( $print )
			echo $script;	
		else
			return $script;
		
	}
	
	function output( $print = false ) 
	{
		global $dp_pec_payments, $wpdb;
		
		$html = "";

		$skin = "pec_no_skin";
			
		if( isset( self::$opts['skin'] ) && self::$opts['skin'] != "" ) 
		{
			
			$skin = 'pec_skin_' . self::$opts['skin'];

			if( self::$opts['skin'] == 'dark' )
				self::$calendar_obj->skin = 'dark';

		}

		if( $this->type == 'calendar' ) 
		{
			
			if( self::$is_admin ) 
				$html .= '<div class="dpProEventCalendar_ModalCalendar">';
			
			$html .= '<div class="dp_pec_calendar_default pec_skin_'.self::$calendar_obj->skin.' dp_pec_'.(self::$is_admin || !empty(self::$event_id) ? 'monthly' : self::$calendar_obj->view).' " id="'.self::$init_id.'">';

			$html .= self::get_top_nav();

			

			$html .= '<div class="dp_pec_wrapper dp_pec_dw_layout_'.self::$calendar_obj->daily_weekly_layout.' dp_pec_calendar_'.self::$calendar_obj->id.' dp_pec_'.(self::$is_admin || !empty(self::$event_id) ? 'monthly' : self::$calendar_obj->view).' '.$skin.' pec_skin_'.self::$calendar_obj->skin.'">';


			if( ! self::$is_admin && ( self::$calendar_obj->show_view_buttons || self::can_add_event() ) ) 
			{

				$html .= '<div class="dp_pec_options_nav">';

				// Add Monthly / Weekly / Daily Layouts buttons
				$html .= self::add_layout_buttons();

				// Add button
				$html .= self::add_event_button();

				$html .= '<div class="dp_pec_clear"></div>';

				$html .= '</div>';

			}
			
			
			

			$html .= '<div class="dp_pec_nav dp_pec_nav_monthly" '.(self::$calendar_obj->view == "monthly" || self::$is_admin || !empty(self::$event_id) ? "" : "style='display:none;'").'>';

					
			$html .= '<span class="next_month"><i class="fa fa-chevron-right"></i></span>';
			$html .= '<span class="prev_month"><i class="fa fa-chevron-left"></i></span>';

			$html .= self::get_month_dropdown();

			$html .= self::get_year_dropdown();

			
			$html .= '<div class="dp_pec_clear"></div>';

			$html .= '</div>';

			if( self::$calendar_obj->show_search ) 
			{

				$html .= '<form method="post" class="dp_pec_search_form dp_pec_nav">';
					$html .= '<input type="search" class="dp_pec_search" value="" placeholder="' . self::$translation['TXT_SEARCH'] . '">';
					$html .= '<span class="dp_pec_search_close"><i class="fas fa-times"></i></span>';
				$html .= '</form>';

			}
			
			$html .= '<div class="dp_pec_nav dp_pec_nav_daily" '.(self::$calendar_obj->view == "daily" && !self::$is_admin && empty(self::$event_id) ? "" : "style='display:none;'").'>';
			$html .= '<span class="next_day"><i class="fa fa-chevron-right"></i></span>';
			$html .= '<span class="prev_day"><i class="fa fa-chevron-left"></i></span>';

			$html .= 	'<span class="pec_today">' . self::$translation['TXT_TODAY'] . '</span>';

			$html .= '<span class="actual_day">'.self::date_i18n(get_option('date_format'), $this->defaultDate).'</span>';
			$html .= '<div class="dp_pec_clear"></div></div>';
			
			if( self::$calendar_obj->first_day == 1 ) 
			{
			
				$weekly_first_date = strtotime('last monday', ($this->defaultDate + (24* 60 * 60)));
				$weekly_last_date = strtotime('next sunday', ($this->defaultDate - (24* 60 * 60)));
			
			} else {
			
				$weekly_first_date = strtotime('last sunday', ($this->defaultDate + (24* 60 * 60)));
				$weekly_last_date = strtotime('next saturday', ($this->defaultDate - (24* 60 * 60)));
			
			}
			
			$weekly_format = get_option('date_format');
			$weekly_format = 'd F, Y';
			
			$weekly_txt = self::date_i18n('d F', $weekly_first_date).' - '.self::date_i18n($weekly_format, $weekly_last_date);
	
			if( date( 'm', $weekly_first_date ) == date( 'm', $weekly_last_date ) )
			
				$weekly_txt = date('d', $weekly_first_date) . ' - ' . self::date_i18n($weekly_format, $weekly_last_date);
				
			
			if( date( 'Y', $weekly_first_date ) != date( 'Y', $weekly_last_date ) )
					
				$weekly_txt = self::date_i18n($weekly_format, $weekly_first_date).' - '.self::date_i18n($weekly_format, $weekly_last_date);
				

			$html .= '<div class="dp_pec_nav dp_pec_nav_weekly" ' . ( self::$calendar_obj->view == "weekly" && ! self::$is_admin && empty( self::$event_id ) ? "" : "style='display:none;'" ) . '>';
			
			$html .= 	'<span class="next_week"><i class="fa fa-chevron-right"></i></span>';
			$html .= 	'<span class="prev_week"><i class="fa fa-chevron-left"></i></span>';

			$html .= 	'<span class="pec_today">' . self::$translation['TXT_TODAY'] . '</span>';

			$html .= 	'<span class="actual_week">'.$weekly_txt.'</span>';
			
			$html .= 	'<div class="dp_pec_clear"></div>';

			$html .= '</div>';
			
			if( ! self::$is_admin ) 
			{
				
				$html .= '<div class="dp_pec_layout">';
				

				$html .= self::get_categories_dropdown();

				$html .= self::get_location_dropdown();

				$html .= self::get_speaker_dropdown();

				$html .= '<div class="dp_pec_layout_right">';

				$html .= self::get_references();

				$html .= '<a href="#" class="dp_pec_view_all dp_pec_btnright" data-translation-list="'.self::$translation['TXT_LIST_VIEW'].'" data-translation-calendar="'.self::$translation['TXT_CALENDAR_VIEW'].'">';

				if( self::$calendar_obj->view == "monthly-all-events" ) 
				
					$html .= self::$translation['TXT_CALENDAR_VIEW'];
				
				else
				
					$html .= self::$translation['TXT_LIST_VIEW'];	
				
				
				$html .= '	</a>';

				if( self::$calendar_obj->show_search ) 
				{

					$html .= '<a href="#" class="dp_pec_search_btn dp_pec_btnright">';
					$html .= '<i class="fa fa-search"></i>';
					$html .= '</a>';

				}


				$html .= '</div>';
				
				$html .= '<div class="dp_pec_clear"></div>';
				$html .= '</div>';

			}

			$html .= '<div style="clear:both;"></div>';
				
			$html .= '<div class="dp_pec_content">';
					
			if( self::$calendar_obj->view == "monthly" || self::$is_admin || ! empty( self::$event_id ) ) 
				$html .= $this->monthlyCalendarLayout();
			
			if( self::$calendar_obj->view == "daily" && ! self::$is_admin && empty( self::$event_id ) ) 
				$html .= $this->dailyCalendarLayout();
			
			if( self::$calendar_obj->view == "weekly" && ! self::$is_admin && empty( self::$event_id ) )
				$html .= $this->weeklyCalendarLayout();
			
			$html .= 		'</div>';
			$html .= 	'</div>';
			$html .= '</div>';
			
			if(self::$is_admin) {
				$html .= '
				</div>';
			}
		} elseif( $this->type == 'upcoming' ) {

			$html .= '<div class="dp_pec_calendar_default dp_pec_calendar_upcoming pec_skin_' . self::$calendar_obj->skin . '" id="' . self::$init_id . '">';

			$html .= self::get_top_nav( true );
			
			$html .= '<div class="dp_pec_wrapper pec_skin_' . self::$calendar_obj->skin . ' dp_pec_calendar_' . self::$calendar_obj->id . '">';

			$html .= 	'<div style="clear:both;"></div>';
				
			$html .= 	'<div class="dp_pec_content">';
						
			$html .= 		self::upcomingCalendarLayout();
			
			$html .= 	'</div>';

			$html .= '</div>';

			$html .= '</div>';
			
		} elseif( $this->type == 'past' ) {
			
			$html .= '<div class="dp_pec_wrapper dp_pec_calendar_' . self::$calendar_obj->id . '" id="' . self::$init_id . '">';

			$html .= 	'<div style="clear:both;"></div>';
				
			$html .= 	'<div class="dp_pec_content">';
					
			if( empty( $this->from ) ) 
				$this->from = "1970-01-01";
			
			$html .= 		self::upcomingCalendarLayout( false, self::$limit, '', null, null, true, false, true, false, true );
			
			$html .= 	'</div>';

			$html .= '</div>';
			
		} elseif( $this->type == 'accordion' ) {
			
			$html .= '<div class="dp_pec_accordion_wrapper dp_pec_calendar_' . self::$calendar_obj->id . ' ' . $skin . '" id="' . self::$init_id . '">';
			
			$html .= self::get_top_nav( true );
			
			$html .= '<div class="dp_pec_clear"></div>';
				
			$html .= '<div class="dp_pec_content_header">';

			// Year / Month Dropdown
			$year_from = pec_setting( 'year_from', 2 );
			$year_until = pec_setting( 'year_until', 3 );
			
			$html .= '
				<div class="pec-month-wrap">
					<h2 class="actual_month">' . self::$translation['MONTHS'][(self::$datesObj->currentMonth - 1)] . ' ' . self::$datesObj->currentYear . '</h2>';

			
			$html .= '<a href="#" class="pec-dropdown-month"><i class="fa fa-chevron-down"></i></a>';

			$html .= '<div class="month_year_dd">';

			$html .= '<ul>';

			$index = 0;
			for( $i = date('Y') - $year_from; $i <= date('Y') + $year_until; $i++ ) 
			{
				
				
				foreach( self::$translation['MONTHS'] as $key ) 
				{
					
					$active = false;
					if( $key . '-' . $i == self::$translation['MONTHS'][(self::$datesObj->currentMonth - 1)] . '-' . self::$datesObj->currentYear )
						$active = true;

					$html .= '<li data-month="' . $key . '-' . $i . '"' . ( $active ? ' class="pec-active"' : '' ) . ' data-pec-index="' . $index . '">' . $key . ' ' . $i . '</li>';

					$index++;
				
				}

			}

			$html .= '</ul>';

			$html .= '</div>';

			$html .= '
				</div>

				<div class="month_arrows">
				
					<span class="events_loading"><i class="fa fa-cog fa-spin"></i></span>

					<span class="prev_month"><i class="fa fa-angle-left"></i></span>
					<span class="next_month"><i class="fa fa-angle-right"></i></span>
				
				</div>
				
				<span class="return_layout"><i class="fas fa-times"></i></span>

				<div class="dp_pec_clear"></div>
			
			</div>';

			$nav_tmp = '';

			$nav_tmp .= 	self::get_categories_dropdown();

			$nav_tmp .= 	self::get_location_dropdown();

			$nav_tmp .= 	self::get_speaker_dropdown();

			if( self::$calendar_obj->show_search ) 
			{

				$nav_tmp .= '<a href="#" class="dp_pec_search_btn dp_pec_btnright">';

				$nav_tmp .= '<i class="fa fa-search"></i>';

				$nav_tmp .= '</a>';

			}

			if( $nav_tmp != '' ) 
			{

				$html .= "<div class='dp_pec_nav'>";

				$html .= $nav_tmp;
				
				$html .= 	"<div class='dp_pec_clear'></div>";

				$html .= "</div>";

			}

			if( self::$calendar_obj->show_search ) 
			{
			
				$html .= '<div class="dp_pec_content_search dp_pec_search_form">';

				$html .= '	<a href="#" class="dp_pec_icon_search" data-results_lang="' . addslashes(self::$translation['TXT_RESULTS_FOR']) . '"><i class="fa fa-search"></i></a>';
				$html .= '	<input type="search" class="dp_pec_content_search_input" placeholder="' . self::$translation['TXT_SEARCH'] . '" />';
				

				$html .= '<div class="dp_pec_clear"></div>';
				$html .= '</div>';
			
			}
			
			$html .= '<div class="dp_pec_content">';

			$html .= 	'<div class="dp_pec_content_ajax ' . ( is_numeric( self::$columns ) && self::$columns > 1 ? 'pec_upcoming_layout' : '') . '">';

			$year = self::$datesObj->currentYear;

			$next_month_days = cal_days_in_month( CAL_GREGORIAN, str_pad( ( self::$datesObj->currentMonth ), 2, "0", STR_PAD_LEFT ), $year );

			$month_number = str_pad( self::$datesObj->currentMonth, 2, "0", STR_PAD_LEFT );

			$this_month_day = "01";

			if( ( self::$calendar_obj->hide_old_dates || self::$opts['hide_old_dates'] ) && self::$datesObj->currentMonth == date('m') && self::$datesObj->currentYear == date('Y')) 
				$this_month_day = str_pad(self::$datesObj->currentDate, 2, "0", STR_PAD_LEFT);

			$html_month_list = "";

			$limit = 40;
			if( $this->is_widget() && is_numeric( self::$limit ) && self::$limit > 0 ) 
				$limit = self::$limit;

			$html_month_list = $this->eventsMonthList( $year."-".$month_number."-".$this_month_day." 00:00:00", $year."-".$month_number."-".$next_month_days." 23:59:59", $limit );

			$html .= $html_month_list;
				
			$html .= '</div>
				</div>
				<div class="dp_pec_clear"></div>
			</div>';
			
		} elseif( $this->type == 'modern' ) {

			// Force to show titles monthly
			self::$calendar_obj->show_titles_monthly = 1;

			// Force link to post
			self::$calendar_obj->link_post = 1;

			$html .= '<div class="dp_pec_modern_wrapper dp_pec_calendar_' . self::$calendar_obj->id . ' ' . $skin . '" id="' . self::$init_id . '">';
			
			// Get Top Nav
			$html .= self::get_top_nav();
			
			$html .= 	'<div class="dp_pec_clear"></div>';
				
			$html .= 	'<div class="dp_pec_content_header">';

			$html .= 		'<span class="events_loading"><i class="fa fa-cog fa-spin"></i></span>';

			$html .= 		'<h2 class="actual_month">' . self::$translation['MONTHS'][(self::$datesObj->currentMonth - 1)] . ' ' . self::$datesObj->currentYear . '</h2>';

					
			$html .= 		'<div class="month_arrows">';
					
			$html .= 			'<span class="prev_month"><i class="fa fa-angle-left"></i></span>';
			$html .= 			'<span class="next_month"><i class="fa fa-angle-right"></i></span>';
					
			$html .= 		'</div>';
				
			$html .= 	'</div>';

			$html .= 	'<div class="dp_pec_nav">';

			$html .= 		self::get_categories_dropdown();

			$html .= 		self::get_location_dropdown();

			$html .= 		self::get_speaker_dropdown();

			$html .= 		'<div class="dp_pec_clear"></div>';
			$html .= 	'</div>';

				
			$html .= 	'<div class="dp_pec_clear"></div>';
			$html .= 	'<div class="dp_pec_content">';
			$html .= 		$this->monthlyCalendarLayout();
			$html .= 	'</div>';

			// References
			if( self::$calendar_obj->show_references ) 
			{

				$specialDatesList = self::getSpecialDatesList();

		    	$html .= '<ul class="dp_pec_modern_references">';

		    	$html .= '<li>';
				$html .= 	'<div class="dp_pec_modern_references_color" style="background-color: ' . self::$calendar_obj->current_date_color . '"></div>';
				$html .= 	'<span>' . self::$translation['TXT_CURRENT_DATE'] . '</span>';
				$html .= '</li>';

		    	if( count( $specialDatesList ) > 0 ) 
				{
				
					foreach( $specialDatesList as $key ) 
					{

						$html .= '<li><div class="dp_pec_modern_references_color" style="background-color: '.$key->color.'"></div> <span>'.$key->title.'</span></li>';

					}

				}

		    	$html .= '</ul>';

		    }

			$html .= '</div>';
			
		} elseif( $this->type == 'accordion-upcoming' ) {
			
			$html .= '<div class="dp_pec_accordion_wrapper dp_pec_calendar_' . self::$calendar_obj->id . ' ' . $skin . '" id="' . self::$init_id . '">';

			$html .= 	'<div style="clear:both;"></div>';
				
			$html .= 	'<div class="dp_pec_content">';

			$html .= 		'<div class="dp_pec_content_ajax ' . ( is_numeric(self::$columns) && self::$columns > 1 ? 'pec_upcoming_layout' : '' ) . '">';
				
			$html .= 			$this->eventsMonthList( null, null, self::$limit );

			$html .= 		'</div>';
			
			$html .= 	'</div>';
			
			$html .= '</div>';
		
		} elseif( $this->type == 'compact-upcoming' || $this->type == 'list-upcoming' ) {
			
			$past = false;

			if( self::$opts['scope'] == 'past' ) 
				$past = true;
			
			$event_list = self::upcomingCalendarLayout( true, (self::$limit + 1), '', null, null, true, false, false, false, $past );

			$html .= '<div class="dp_pec_wrapper dp_pec_compact_wrapper dp_pec_compact_upcoming_wrapper ' . ( $this->type == 'list-upcoming' ? 'dp_pec_list_upcoming' : '' ) . ' ' . $skin . '" id="' . self::$init_id . '">';

			$html .= '<div style="clear:both;"></div>';

			$html .= '<div class="dp_pec_content">';

				$event_count = 0;
				$daily_events = array();

				if( is_array( $event_list ) ) 
				{
				
					foreach ( $event_list as $event ) 
					{

						if( $event->id == "" ) 
							$event->id = $event->ID;
						
						$event = (object)array_merge((array)self::getEventData($event->id), (array)$event);
			
						if( $event_count >= self::$limit )
							break;

						if( $event->recurring_frecuency == 1 && self::$opts['group'] ) {
					
							if( in_array( $event->id, $daily_events))
								continue;
							
							$daily_events[] = $event->id;
						}

						$title = $event->title;
						$permalink = "";
						if( self::$calendar_obj->link_post ) 
						{
						
							$permalink = dpProEventCalendar_get_permalink($event->id);
							$title = '<a href="'.$permalink.'" target="'.self::$calendar_obj->link_post_target.'">'.$title.'</a>';	
						
						}
						

						$time = self::date_i18n( self::$time_format, strtotime( $event->date ) );

						$start_day = date( 'd', strtotime($event->date) );
						$start_month = date( 'n', strtotime($event->date) );
						$start_year = date( 'Y', strtotime($event->date) );
						
						$end_datetime = self::get_end_datetime( $event );
						$end_date = $end_datetime['end_date'];
						$end_time = $end_datetime['end_time'];
						
						$start_date = self::date_i18n( get_option('date_format'), strtotime($event->date) );
						
						
						/*if($start_year != $end_year && $end_year != "") {
							$start_date .= ', '.$start_year;
						}*/
						
						if( isset( $event->all_day ) && $event->all_day ) 
						{
						
							$time = self::$translation['TXT_ALL_DAY'];
							$end_time = "";
						
						}

						$status = self::get_status_label( $event->status );
						if( $status != '' )
						{

							$time = $status;
							$end_time = "";

						}

						$day_number = date("d", strtotime($event->date));
						$month = self::date_i18n("M", strtotime($event->date));

						$html .= '<div class="dp_pec_date_event" data-event-number="1">
									<div class="dp_pec_date_left">
										<div class="dp_pec_date_left_number">'.$day_number.'
											<div class="dp_pec_date_left_month">'.$month.'
											';

						if( $event->recurring_frecuency == 1 && self::$opts['group'] && $this->type == 'list-upcoming' && $end_date != "" )
							$html .= $end_date;

											$html .='</div>
										</div>
										
									</div>';
						$post_thumbnail_id = get_post_thumbnail_id( $event->id );
						$image_attributes = wp_get_attachment_image_src( $post_thumbnail_id, 'small' );

						if( ! empty( $post_thumbnail_id ) && ! $this->is_widget() )
							$html .= '	<div class="dp_pec_event_photo" style="background-image: url('.(isset($image_attributes[0]) ? $image_attributes[0] : '').');"></div>';

						$html .= '
									<div class="dp_pec_content_left">';
						
						// Featured ?
						$html .= self::display_featured_tag( $event, false );

						$html .= '
										<div class="dp_pec_clear"></div>';

						$all_working_days = '';
						if( $event->pec_daily_working_days && $event->recurring_frecuency == 1 )
							$all_working_days = self::$translation['TXT_ALL_WORKING_DAYS'];

						$event_timezone = dpProEventCalendar_getEventTimezone( $event->id );

						$pec_time = ($all_working_days != '' ? $all_working_days.' ' : '').(((self::$calendar_obj->show_time && !$event->hide_time) || $event->all_day) ? $time.$end_time.$end_date.(self::$calendar_obj->show_timezone && !$event->all_day && $status == '' ? ' '.$event_timezone : '') : '');
					
						
						
						$html .= '
										<div class="dp_pec_clear"></div>

										<h2 class="dp_pec_event_title">
											' . $title . '
										</h2>';



						$category = get_the_terms( $event->id, 'pec_events_category' ); 
						$category_list_html = '';
						if( ! empty( $category ) ) 
						{
							$category_count = 0;
							foreach ( $category as $cat )
							{
								if( $category_count > 0 )
									$category_list_html .= " / ";	

								$category_list_html .= $cat->name;
								$category_count++;
							}
						}
						
						if( $category_list_html != "" ) 
						{
							$html .= '
										<div class="dp_pec_compact_meta_category">
											<span>'.$category_list_html.'</span>
										</div>';
						}

						if( $pec_time != "" && !$event->tbc ) 
							$html .= '<span class="dp_pec_date_time"><i class="fa fa-clock"></i>'.$pec_time.'</span>';
						

						if( $event->link != '' ) 
						{
						
							$event->link = trim($event->link);
							if( substr( $event->link, 0, 4 ) != "http" && substr( $event->link, 0, 4 ) != "mail" ) 
								$event->link = 'http://'.$event->link;

							$html .= '
							<a class="dpProEventCalendar_feed" href="'.$event->link.'" rel="nofollow" target="_blank"><i class="fa fa-link"></i>';

							if( ! $this->is_widget() ) 
								$html .= $event->link;

							$html .= '</a>';
						
						}

						$html .= '
									</div>
									<div class="dp_pec_clear"></div>
								</div>';


						$event_count++;
					}
				}

			$html .= '
				</div>
				<div style="clear:both;"></div>
			</div>';

		
		} elseif( $this->type == 'grid-upcoming' ) {
			
			$this->gridLayout = new DPPEC_GridLayout();
			$html .= $this->gridLayout->display_layout();

		} elseif( $this->type == 'book-btn' ) {
			
			$html .= '<div class="dp_pec_btn" id="' . self::$init_id . '">';
			$html .= '<div style="clear:both;"></div>';
				
			$html .= '<div class="dp_pec_content">';

			$html .= self::get_booking_button( self::$event_id );

			$html .= '</div>';
			$html .= '</div>';

		} elseif( $this->type == 'card' ) {

			$this->cardLayout = new DPPEC_CardLayout();
			$html .= $this->cardLayout->display_layout();

		} elseif( $this->type == 'slider' || $this->type == 'slider-2' || $this->type == 'slider-3' ) {
			
			$this->sliderLayout = new DPPEC_SliderLayout( $this->type );
			$html .= $this->sliderLayout->display_layout();

		} elseif( $this->type == 'carousel' || $this->type == 'carousel-2' || $this->type == 'carousel-3' ) {
			
			$this->carouselLayout = new DPPEC_CarouselLayout( $this->type );
			$html .= $this->carouselLayout->display_layout();

		} elseif( $this->type == 'yearly' ) {
			
			$this->yearlyLayout = new DPPEC_YearlyLayout();
			$html .= $this->yearlyLayout->display_layout();

		} elseif( $this->type == 'countdown' ) {

			$this->countdownLayout = new DPPEC_CountdownLayout();
			$html .= $this->countdownLayout->display_layout();
			

		} elseif( $this->type == 'timeline' ) {

			$this->timelineLayout = new DPPEC_TimelineLayout();
			$html .= $this->timelineLayout->display_layout();
			

		} elseif( $this->type == 'compact' ) {

			if( self::$is_admin ) 
				$html .= '<div class="dpProEventCalendar_ModalCalendar">';

			$html .= '<div class="dp_pec_compact_wrapper dp_pec_wrapper ' . $skin . '" id="' . self::$init_id . '">';
			
			$year_from = pec_setting( 'year_from', 2 );
			$year_until = pec_setting( 'year_until', 3 );

			$html .= '
				<div class="dp_pec_nav">
					<span class="next_month"><i class="pec-default-arrow fa fa-chevron-right"></i><i style="display:none;" class="pec-rtl-arrow fa fa-chevron-left"></i></span>
					<span class="prev_month"><i class="pec-default-arrow fa fa-chevron-left"></i><i style="display:none;" class="pec-rtl-arrow fa fa-chevron-right"></i></span>
					<div class="dp_pec_wrap_month_year">
						<select autocomplete="off" class="pec_switch_month">
							';
							for( $i = date('Y') - $year_from; $i <= date('Y') + $year_until; $i++ ) 
							{
							
								foreach( self::$translation['MONTHS'] as $key ) 
								{
									$html .= '
										<option value="'.$key.'-'.$i.'" '.($key.'-'.$i == self::$translation['MONTHS'][(self::$datesObj->currentMonth - 1)].'-'.self::$datesObj->currentYear ? 'selected="selected"':'').'>'.$key.' '.$i.'</option>';
								}

							}
				$html .= '
						</select>
						<select autocomplete="off" class="pec_switch_year">';

							for( $i = date('Y') - $year_from; $i <= date('Y') + $year_until; $i++ ) 
							{
								$html .= '<option value="'.$i.'" '.($i == self::$datesObj->currentYear ? 'selected="selected"':'').'>'.$i.'</option>';
							}
				$html .= '
						</select>
					</div>
					<div class="dp_pec_clear"></div>
				</div>';

				$html .= '<div class="dp_pec_clear"></div>';
				$html .= '<div class="dp_pec_content">';
					$html .= $this->monthlyCalendarLayout(true);
				$html .= '</div>';
			$html .= '</div>';
			$html .= '<div class="dp_pec_clear"></div>';

			if( self::$is_admin ) 
				$html .= '</div>';
			

		} elseif( $this->type == 'cover' ) {

			$this->coverLayout = new DPPEC_CoverLayout();
			$html = $this->coverLayout->display_layout();

		} elseif( $this->type == 'gmap-upcoming' ) {
			
			$this->gmapLayout = new DPPEC_GmapLayout();
			$html .= $this->gmapLayout->display_layout();

		} elseif( $this->type == 'add-event' ) {

			$this->eventForm = new DPPEC_EventForm();
			$html .= $this->eventForm->display_form();
			
		} elseif( $this->type == 'list-author' ) {

			$html .= '<div class="dp_pec_wrapper dp_pec_calendar_' . self::$calendar_obj->id . '" id="' . self::$init_id . '">';

			$html .= '<div class="dp_pec_clear"></div>';
				
			$html .= '<div class="dp_pec_content">';
						
			$html .= self::upcomingCalendarLayout( false, 10, '', null, null, true, true );

			$html .= '</div>';

			$html .= '</div>';

		} elseif( $this->type == 'bookings-user' ) {
			
			$this->bookingsUserLayout = new DPPEC_BookingsUserLayout();
			$html .= $this->bookingsUserLayout->display_layout();

		} elseif( $this->type == 'today-events' ) {

			$html .= '<div class="dp_pec_wrapper dp_pec_calendar_' . self::$calendar_obj->id . ' dp_pec_today_events" id="' . self::$init_id . '">';

			$html .= '<div class="dp_pec_clear"></div>';
				
			$html .= '<div class="dp_pec_content">';

			$html .= $this->eventsListLayout( date('Y-m-d', $this->defaultDate), false );
			
			$html .= '</div>';

			$html .= '</div>';

		}
		
		
		if( $print )
			echo $html;	
		else
			return $html;
		
	}
	
	function eventsMonthList( $start_search = null, $end_search = null, $limit = 40, $keyword = '' ) 
	{
		
		$html = "";
		$daily_events = array();
		
		$pagination = self::get_pagination_number();

		$event_counter = 1;
		
		$event_list = self::upcomingCalendarLayout( true, $limit, '', $start_search, $end_search, true, false, true, false, false, $keyword );
		
		if( is_array( $event_list ) && count( $event_list ) > 0 ) 
		{
			
			foreach( $event_list as $event ) 
			{
				
				if( $event->id == "" ) 
					$event->id = $event->ID;
				
				$event = (object)array_merge((array)self::getEventData($event->id), (array)$event);

				if( $event_counter > $limit ) break;
				
				if( $event->recurring_frecuency == 1 && ( ! isset( $event->is_extra_date ) || ! $event->is_extra_date ) )
				{
					
					if( in_array( $event->id, $daily_events ) ) 
						continue;	

					$daily_events[] = $event->id;

					$event->date = $event->orig_date;

				}
				
				$all_working_days = '';
				if( $event->pec_daily_working_days && $event->recurring_frecuency == 1  && ( ! isset( $event->is_extra_date ) || ! $event->is_extra_date )  ) 
				{
				
					$all_working_days = self::$translation['TXT_ALL_WORKING_DAYS'];
					$event->date = $event->orig_date;
				
				}
				
				$time = self::date_i18n( self::$time_format, strtotime($event->date));

				$end_datetime = self::get_end_datetime( $event, false, true );
				$end_date = $end_datetime['end_date'];
				$end_time = $end_datetime['end_time'];

				if( isset( $event->all_day ) && $event->all_day ) 
				{
				
					$time = self::$translation['TXT_ALL_DAY'];
					$end_time = "";
				
				}

				$status = self::get_status_label( $event->status );
				if( $status != '' )
				{

					$time = $status;
					$end_time = "";

				}

				if( isset( $event->is_extra_date ) && $event->is_extra_date )
					$end_date = "";

				$title = $event->title;

				$permalink = "";

				if( self::$calendar_obj->link_post ) 
				{
				
					$permalink = self::get_permalink ( $event, $event->date );

					$title = '<a href="' . $permalink . '" target="' . self::$calendar_obj->link_post_target . '">' . $title . '</a>';	

				}
				
					
				$html .= '
				<div class="dp_pec_isotope dp_pec_date_event_wrap dp_pec_columns_' . self::$columns .'"  data-event-number="'.$event_counter.'" '.($event_counter > $pagination && false ? 'style="display:none;"' : '').'>
				
					<div class="dp_pec_accordion_event ' . (isset($category_slug) ? $category_slug : '') . '" style="' . ($event->color != "" ? 'border-color:' . $event->color . ';' : '') . '">';
					

					$post_thumbnail_id = get_post_thumbnail_id( $event->id );
					$image_attributes = wp_get_attachment_image_src( $post_thumbnail_id, 'large' );

					
					$html .= '<div class="dp_pec_accordion_event_inner">';

					if( $post_thumbnail_id ) 
					{
					
						$html .= '<div class="dp_pec_event_photo_wrap" style="background-image: url('.$image_attributes[0].');">';

						$html .= '<div class="dp_pec_event_photo_ol"></div>';
						
					}
					
					$featured_tag = self::display_featured_tag( $event, true, false, true );

					$categories = self::display_meta( $event, array( 'category' ) );

					$accordion_head = false;

					if( $featured_tag != '' || $categories != '' ) 
					{
						
						$accordion_head = true;

						$html .= '<div class="dp_pec_accordion_event_head">';

						$html .= $featured_tag;

						$html .= $categories;
						
						$html .= '</div>';

					}
					
					$html .= '<div class="dp_pec_accordion_event_main' . ( ! $accordion_head ? ' dp_pec_accordion_event_main_no_head' : '' ) . '">';

					$html .= '<h2>' . $title . '</h2>';

					$speakers = self::display_meta( $event, array( 'speakers' ) );
					$location = self::display_meta( $event, array( 'location_short' ) );
					$phone = self::display_meta( $event, array( 'phone' ) );
					$organizer = self::display_meta( $event, array( 'organizer' ) );
					$age_range = self::display_meta( $event, array( 'age_range' ) );

					$displayed_in_main = '';

					if( ! empty( $speakers ) ) {
						$html .= $speakers;
						$displayed_in_main = 'speakers';
					} else if( ! empty( $location ) ) {
						$html .= $location;
						$displayed_in_main = 'location';
					} else if( ! empty( $organizer ) ) {
						$html .= $organizer;
						$displayed_in_main = 'organizer';
					} else if( ! empty( $phone ) ) {
						$html .= $phone;
						$displayed_in_main = 'phone';
					} else if( ! empty( $age_range ) ) {
						$html .= $age_range;
						$displayed_in_main = 'age_range';
					} else if( ! empty( $author ) ) {
						$html .= $author;
						$displayed_in_main = 'author';
					}
					
					if( $post_thumbnail_id ) 
					{
						$html .= '</div>';
						$html .= '</div>';
					}

					$html .= '</div>';
					

					$html .= '<div class="dp_pec_clear"></div>';

					if( $post_thumbnail_id ) 
					{
					
						$html .= '<div class="dp_pec_accordion_event_inner">';
					
					}

					$event_timezone = dpProEventCalendar_getEventTimezone($event->id);

					if( $event->tbc ) 
					{
					
						$html .= '<div class="dp_pec_accordion_event_date">' . self::$translation['TXT_TO_BE_CONFIRMED'] . '</div>';
					
					} else {
					
						
						$pec_time = ($all_working_days != '' ? $all_working_days.' ' : '').(((self::$calendar_obj->show_time && !$event->hide_time) || $event->all_day) ?  $time.$end_time : '');
						
						if($pec_time != "") 
						{

							$html .= '<div class="dp_pec_accordion_event_bottom">';

							// Get more options
							$html .= self::get_more_options($event);

							// Get Booking Button
							$booking_booked = self::getBookingBookedLabel( $event->id, $event->date );
							if( $booking_booked == "" ) 
								$html .= self::get_booking_button( $event->id, date('Y-m-d', strtotime($event->date) ), false, false, false );
							else
								$html .= $booking_booked;

							$html .= '<span class="pec_time">'.$pec_time.'</span>';

							$html .= '<span class="pec_timezone">' . (self::$calendar_obj->show_timezone && !$event->all_day && $status == '' ? ' '.$event_timezone : '') . '</span>';

							$html .= '</div>';
						
						}
						

						$html .= '<div class="dp_pec_accordion_event_date">' . self::date_i18n( self::remove_year( get_option('date_format') ), strtotime($event->date) ) . $end_date . '</div>';
					
					}
					
					$html .= '<a href="javascript:void();" class="dp_pec_accordion_expand"><i class="fa fa-chevron-down"></i></a>';

					$html .= '<div class="dp_pec_clear"></div>';

						
						
				$html .= '
						<div class="pec_description">';

				if( $displayed_in_main != "location" && $location != "" ) 
				{

					$html .= '<div class="dp_pec_accordion_venue">';

					$html .= '<span class="dp_pec_accordion_lbl">' . self::$translation['TXT_VENUE'] . '</span>';
					
					$html .= $location;

					$html .= '</div>';

				}

				$html .= self::show_description( $event, $permalink );

				$html .= '
							</div>
						</div>
					</div>
				</div>';
				
				
				$event_counter++;
			}
			
			if( ( $event_counter - 1 ) > $pagination ) 
			{
			
				//$html .= '<a href="#" class="pec_action_btn dpProEventCalendar_load_more" data-total="'.($event_counter - 1).'" data-pagination="'.$pagination.'">'.self::$translation['TXT_MORE'].'</a><div class="dp_pec_clear"></div>';
			
			}

		} else {

			$html .= '<div class="dp_pec_isotope dp_pec_date_event_wrap dp_pec_columns_1">';
			$html .= 	'<div class="dp_pec_accordion_event dp_pec_accordion_no_events">';
			$html .= 		'<div class="dp_pec_accordion_event_inner">';
			$html .= 			'<div class="dp_pec_accordion_event"><span class="dp_pec_accordion_search_count">0</span> <span>' . self::$translation['TXT_EVENTS_FOUND'] . '</span></div>';
			$html .= 		'</div>';	
			$html .= 	'</div>';	
			$html .= '</div>';	

		}
		
		return $html;
	}

	
	function switchCalendarTo( $type, $limit = 5, $limit_description = 0, $category = 0, $author = 0, $event_id = 0, $location = 0 ) 
	{

		if( ! is_numeric( $limit ) ) $limit = 5;
		$this->type = $type;
		if( $type == 'carousel' && ! is_numeric( self::$columns ) ) 
			self::$columns = $this->carousel_default_columns;

		self::$limit = $limit;
		self::$limit_description = $limit_description;
		self::$category = $category;
		self::$opts['location'] = $location;
		self::$event_id = $event_id;
		self::$author = $author;

	}
	
	function calendarSubscription( $email, $name ) 
	{
	
		global $wpdb;

		if( stripslashes( $email ) != '' )
		{
			
			$exists = $wpdb->get_row( $wpdb->prepare( "SELECT COUNT(*) as counter FROM " . DP_PRO_EVENT_CALENDAR_TABLE_SUBSCRIBERS_CALENDAR . " WHERE calendar = %d AND email = %s", self::$id_calendar, $email ) );

			if( $exists->counter == 0 ) 
			{
				$wpdb->insert( 
					DP_PRO_EVENT_CALENDAR_TABLE_SUBSCRIBERS_CALENDAR, 
					array( 
						'name' => $name, 
						'email' => $email, 
						'calendar' => self::$id_calendar, 
						'subscription_date' => current_time('mysql')
					), 
					array( 
						'%s', 
						'%s', 
						'%d', 
						'%s' 
					) 
				);
			}
				
			if( self::$calendar_obj->mailchimp_api != "" && self::$calendar_obj->subscribe_active ) 
			{

				$data = array(
					'email_address' => $email,
					'status' => 'subscribed'
				);
				 
				$url = 'https://' . substr( self::$calendar_obj->mailchimp_api, strpos( self::$calendar_obj->mailchimp_api,'-')+1) . '.api.mailchimp.com/3.0/'.self::$calendar_obj->mailchimp_list.'/members/';
				
				$result = json_decode( dpProEventCalendar_mailchimp_curl_connect( $url, 'POST', self::$calendar_obj->mailchimp_api, $data) );
				die( 'ok' );

			}

		}

	}

	private static function get_top_nav ( $new_event = false ) 
	{

		if( is_admin() )
			return '';

		$html = '';
		$new_button = '';

		if( $new_event )
			// Get New Event button
			$new_button = self::add_event_button();

		// Get ical button
		$ical_button = self::ical_button();

		// Get RSS button
		$rss_button = self::rss_button();
		
		// Get subscribers button and form
		$subscribers_form = self::subscribers_form();

		if( $new_button != "" || $ical_button != "" || $rss_button != "" || $subscribers_form != "" )
		{

			$html .= '<div class="dp_pec_top_nav">';
			$html .= $new_button . $ical_button . $rss_button . $subscribers_form;
			$html .= '<div class="dp_pec_clear"></div>';
			$html .= '</div>';

			$html .= '<div class="dp_pec_clear"></div>';

		}

		return $html;

	}

	protected static function show_description ( $event, $selected_date = '', $only_short = false, $show_read_more = true )
	{


		$excerpt = get_post_meta( $event->id, 'pec_excerpt', true );
		$event_desc = self::remove_html_comments( $event->description );

		if( $event_desc == "" && $excerpt == "" ) return '';

		if( $excerpt != "" ) 
			$event_desc_short = $excerpt;
		else
			$event_desc_short = html_entity_decode( $event_desc );

		$permalink = "";
		
		if( self::$calendar_obj->link_post || $only_short ) 
		{

			if( $selected_date != '' ) 
				$permalink = $selected_date;
			else 
				$permalink = dpProEventCalendar_get_permalink( $event->id );

		}

		$html = '<div class="dp_pec_event_description">';

		if( post_password_required( $event->id ) ) 
		{
		
			$html .= get_the_password_form();
		
		} else {

			$html .= '<div class="dp_pec_event_description_short">';

			$html .= 	do_shortcode( $event_desc_short );

			$html .= '</div>';

			if( ! $only_short || ( $only_short && $permalink != "" ) && $show_read_more )
			{

				$html .= '<a href="' . ( $permalink == "" ? '#' : $permalink ) . '" ' . ( $permalink == "" ? '' : 'target="' . self::$calendar_obj->link_post_target . '"' ) . ' class="dp_pec_event_description_more">' . self::$translation['TXT_READ_MORE'] . '<i class="fas fa-angle-down"></i></a>';

			}
			
			
		}

		$html .= '</div>';

		return $html;

	}

	// Remove unwanted HTML comments
	protected static function remove_html_comments( $content = '' ) 
	{
		return preg_replace( '/<!--(.|\s)*?-->/', '', $content );
	}

	protected static function show_organizer ( $organizer_id )
	{

		$html = "";

		if( is_numeric( $organizer_id ) ) 
		{
		
			$organizer = get_the_title( $organizer_id );
			$html = '<span class="pec_organizer">' . $organizer . '</span>';
		
		}

		return $html;

	}

	protected static function display_featured_tag ( $event, $icon = true, $label = true, $tooltip = false )
	{

		$html = '';

		if( $event->featured_event ) {

			$html .= '<span class="pec_featured"' . ( $tooltip ? ' data-pec-tooltip="' . self::$translation['TXT_FEATURED'] . '"' : '' ) . '>';

			if( $icon )
				$html .= '<i class="fa fa-star"></i>';

			if( $label )
				$html .= self::$translation['TXT_FEATURED'];

			$html .= '</span>';

		}

		return $html;


	}

	static function get_permalink ( $event, $event_date = '', $return_if_no_custom_link = false ) 
	{

		$use_link = get_post_meta( $event->id, 'pec_use_link', true );
		if( $return_if_no_custom_link && ! $use_link )
			return;
		
		$permalink = dpProEventCalendar_get_permalink( $event->id );

		$date = $event->date;

		if( $date != '' )
			$date = $event_date;

		if( ! $use_link ) 
		{
		
			if ( get_option( 'permalink_structure' ) ) 
			{
			
				$permalink_format = rtrim($permalink, '/');

				if( strpos( $permalink, "?" ) !== false ) 
				{
				
					$permalink_query = substr( $permalink_format, (strpos($permalink, "?") ) );
				
				} else {
				
					$permalink_query = "";
				
				}

				$permalink_format = rtrim( str_replace( $permalink_query, "", $permalink_format ), '/' );
				$permalink = $permalink_format . '/' . strtotime( $date ) . $permalink_query;
			
			} else {
			
				$permalink = $permalink . ( strpos( $permalink, "?" ) === false ? "?" : "&" ) . 'event_date=' . strtotime( $date );
			
			}
		
		}

		return $permalink;

	}

	private static function display_title ( $event )
	{

		$title = $event->title;
		$permalink = "";

		if( self::$calendar_obj->link_post ) 
		{
		
			$permalink = dpProEventCalendar_get_permalink( $event->id );
			$title = '<a href="' . $permalink . '" target="' . self::$calendar_obj->link_post_target . '">'.$title . '</a>';	
		
		}

		$html = '<div class="dp_pec_clear"></div>';

		$html .= '<h2 class="dp_pec_event_title">' . $title . '</h2>';

		$html .= '<div class="dp_pec_clear"></div>';

		return $html;

	}

	protected static function display_location ( $event, $show_map = true, $show_address = true )
	{

		$html_tmp = "";

		if( $event->location != '' ) 
		{
		
			if( $event->location_address != "" && $show_address ) 
				$event->location .= '<br>'.$event->location_address;

			// Check Map

			$map_id = "";

			if( $show_map )
			{


				if( $event->map != '' || is_numeric( $event->location_id ) ) 
				{
				
					if( is_numeric( $event->location_id ) ) 
					{
					
						$event->map = get_post_meta($event->location_id, 'pec_venue_map_lnlat', true);
					
					} else {
					
						$event->map = get_post_meta($event->id, 'pec_map_lnlat', true);
					
					}

					$geocode = false;
					
					if( $event->map != "" ) 
					{
					
						$event->map = str_replace( " ", "", $event->map );
					
					} else {
					
						$geocode = true;
					
						if( is_numeric( $event->location_id ) ) 
						{
					
							$venue_address = get_post_meta( $event->location_id, 'pec_venue_address', true );
					
							if( $venue_address != "" ) 
					
								$event->map = $venue_address;
					
							else
					
								$event->map = get_post_meta( $event->location_id, 'pec_venue_map', true );
					
						} else {
						
							$event->map = get_post_meta( $event->id, 'pec_map', true );
						
						}
					
					}

					$map_id = $event->id . '_' . self::$nonce . '_' . rand();

				}

			}

			$html_tmp .= '<span class="dp_pec_event_location">' . $event->location;

			if( $map_id != "" ) 
			{

				$html_tmp .= '<a href="#" id="map_btn_' . $map_id . '" class="dp_pec_open_map" data-pec-tooltip="' . self::$translation['TXT_OPEN_MAP'] . '"><i class="fa fa-map-marker-alt"></i></a>';

				$html_tmp .= '<div class="dp_pec_open_map_wrap">';
				$html_tmp .= self::get_map( $map_id, $event->map, $event->location_id, $geocode, false );
				$html_tmp .= '</div>';
						
				

			}

			$html_tmp .= '</span>';
		
		}

		return $html_tmp;

	}

	protected static function display_phone ( $event )
	{

		if( $event->phone == '' ) 
			return '';

		return '<span class="dp_pec_event_phone"><h3>' . self::$translation['TXT_PHONE'] . '</h3>' . $event->phone . '</span>';

	}

	protected static function display_author ( $event )
	{

		if( ! self::$calendar_obj->show_author ) 
			return '';
		
		$author = get_userdata( get_post_field( 'post_author', $event->id ) );
		$html = '<span class="pec_author"><h3>' . self::$translation['TXT_AUTHOR'] . '</h3>' . $author->display_name . '</span>';
	
		return $html;

	}

	protected static function display_speakers ( $event )
	{

		$html = '';

		$speaker_list = explode( ',', $event->speaker );

		if( count( $speaker_list ) == 0 )
			return '';

		if( ! empty( $speaker_list ) ) 
		{
			
			foreach( $speaker_list as $speaker_id )
			{
				
				if( ! is_numeric( $speaker_id ) )
								continue;

				$speaker = get_the_title( $speaker_id );

				$speaker_image_id = get_post_thumbnail_id( $speaker_id );
				$speaker_image = '';

				if( is_numeric( $speaker_image_id ) ) 
					$speaker_image = wp_get_attachment_image_src( $speaker_image_id, 'thumbnail' );

				$html .= '<span class="dp_pec_event_speaker">' . ( is_array( $speaker_image ) ? '<div class="pec_event_page_speaker_image" style="background-image:url(\''.$speaker_image[0].'\')"></div>' : '') . $speaker . '</span>';
			
			}
		
		}

		if( $html != '' )
			$html = '<div>' . $html . '</div>';

		return $html;

	}

	protected static function display_organizer ( $event )
	{

		$html = '';

		if($event->organizer != "") 
		{
		
			if( is_numeric( $event->organizer ) ) 
			{
			
				$organizer = get_the_title( $event->organizer );

				$organizer_image_id = get_post_thumbnail_id( $event->organizer );
				$organizer_image = '';

				if( is_numeric( $organizer_image_id ) ) 
				
					$organizer_image = wp_get_attachment_image_src( $organizer_image_id, 'thumbnail' );

				$html .= '<span class="dp_pec_event_speaker dp_pec_tooltip_left" data-pec-tooltip="' . self::$translation['TXT_ORGANIZER'] . '">' . ( is_array( $organizer_image ) ? '<div class="pec_event_page_speaker_image" style="background-image:url(\''.$organizer_image[0].'\')"></div>' : '') . $organizer . '</span>';
			}
		
		}

		return $html;

	}

	protected static function display_age_range ( $event )
	{

		if( $event->age_range == "" ) 
			return '';

		$html = '<p>' . self::$translation['TXT_AGE_RANGE'] . ': ' . $event->age_range . '</p>';

		return $html;

	}

	protected static function display_meta ( $event, $sections = array( 'author', 'location', 'phone', 'category' ) )
	{

		$html = '';

		$html_tmp = '';

		if( in_array( 'author', $sections ) )
			$html_tmp .= self::display_author ( $event );
		
		if( in_array( 'location', $sections ) )
			$html_tmp .= self::display_location ( $event );

		if( in_array( 'location_short', $sections ) )
			return self::display_location ( $event, true, false );
		
		if( in_array( 'phone', $sections ) )		
			$html_tmp .= self::display_phone ( $event );

		if( in_array( 'speakers', $sections ) )		
			$html_tmp .= self::display_speakers ( $event );

		if( in_array( 'organizer', $sections ) )		
			$html_tmp .= self::display_organizer ( $event );

		if( in_array( 'age_range', $sections ) )		
			$html_tmp .= self::display_age_range ( $event );
		
		if( in_array( 'category', $sections ) )
		{
			$category = get_the_terms( $event->id, 'pec_events_category' ); 

			if( ! empty( $category ) ) 
			{
			
				$category_count = 0;
				$html_tmp .= '
					<span class="dp_pec_event_categories">';
					
				foreach ( $category as $cat )
				{
				
					if( $category_count > 0 ) 
						$html_tmp .= " / ";	

					$html_tmp .= $cat->name;
					$category_count++;
				
				}
				
				$html_tmp .= '</span>';
			
			}
		}

		if( $html_tmp != '' ) 
		{

			$html .= '<div class="dp_pec_event_meta">';
			$html .= $html_tmp;
			$html .= '</div>';


		}

		return $html;

	}

	private static function display_custom_fields ( $event_id )
	{

		global $dpProEventCalendar;

		$cal_form_custom_fields = self::$calendar_obj->form_custom_fields;
		$cal_form_custom_fields_arr = explode(',', $cal_form_custom_fields);

		$html = '';

		if( is_array( pec_setting( 'custom_fields_counter' ) ) ) 
		{
		
			$counter = 0;
			
			$html = '';

			$html .= '<div class="dp_pec_single_item pec_event_page_custom_fields">';

			foreach( $dpProEventCalendar['custom_fields_counter'] as $key ) 
			{

				if( ! empty($cal_form_custom_fields) && $cal_form_custom_fields != "all" && $cal_form_custom_fields != "" && !in_array($dpProEventCalendar['custom_fields']['id'][$counter], $cal_form_custom_fields_arr)) 
				{
				
					$counter++;
					continue;
				
				}

				$field_value = get_post_meta( $event_id, 'pec_custom_'.$dpProEventCalendar['custom_fields']['id'][$counter], true );
				$field_type = $dpProEventCalendar['custom_fields']['type'][$counter];

				if( is_array( $field_value ) )
					$field_value = implode(', ', $field_value);
				
				if( $field_value != "" ) 
				{

					$html .= '<div class="pec_event_page_gen_tag pec_customtype_' . $field_type . ' pec_custom_' . $dpProEventCalendar['custom_fields']['id'][$counter] . '">';

					$html .= '<strong>' . $dpProEventCalendar['custom_fields']['name'][$counter] . '</strong>';

					if( $field_type != 'checkbox' ) 
					
						$html .= '<p class="pec_event_page_sub_p">' . $field_value . '</p>';
					
					$html .= '</div>';
				
				}
				
				$counter++;		
			
			}

			$html .= '</div>';
		
		}

		return $html;

	}

	public static function remove_year( $format ) 
	{

		return str_replace( array( ', Y', ' Y', '/Y', 'Y-', 'Y' ), "", $format );

	}

	public static function date_i18n( $format, $timestamp = "", $no_default = false ) 
	{


		if( $timestamp == "" ) 
		{

			if( $no_default )
				return '';
		
			$timestamp = time();	
		
		}

		$i18n = date( $format, $timestamp );
		
		$i18n = str_replace( "January", self::$translation['MONTHS'][0], $i18n );
		$i18n = str_replace( "February", self::$translation['MONTHS'][1], $i18n );
		$i18n = str_replace( "March", self::$translation['MONTHS'][2], $i18n );
		$i18n = str_replace( "April", self::$translation['MONTHS'][3], $i18n );
		$i18n = str_replace( "May", self::$translation['MONTHS'][4], $i18n );
		$i18n = str_replace( "June", self::$translation['MONTHS'][5], $i18n );
		$i18n = str_replace( "July", self::$translation['MONTHS'][6], $i18n );
		$i18n = str_replace( "August", self::$translation['MONTHS'][7], $i18n );
		$i18n = str_replace( "September", self::$translation['MONTHS'][8], $i18n );
		$i18n = str_replace( "October", self::$translation['MONTHS'][9], $i18n );
		$i18n = str_replace( "November", self::$translation['MONTHS'][10], $i18n );
		$i18n = str_replace( "December", self::$translation['MONTHS'][11], $i18n );
		
		return $i18n;

	}

	protected static function back_button ()
	{

		return '<a href="#" class="dp_pec_date_event_back dp_pec_btnright">' . self::$translation['TXT_BACK'] . '</a>';

	}

	protected static function no_events_found () 
	{

		$html = '<div class="dp_pec_date_event dp_pec_date_eventsearch dp_pec_isotope dp_pec_date_event_wrap dp_pec_columns_1">';

		$html .=	'<p class="dp_pec_event_no_events">' . self::$translation['TXT_NO_EVENTS_FOUND'] . '</p>';

		$html .= '</div>';

		return $html;

	}

	public function event_modal ( $event_id, $date ) 
	{

		$this->eventModal = new DPPEC_EventModal( $event_id, $date );
		die( $this->eventModal->display_modal() );

	}
	
	protected static function get_rating( $eventid, $expand = true ) 
	{

		if( post_password_required( $eventid ) )
			return '';

		$html = "";
		$rate 		= get_post_meta( $eventid, 'pec_rate', true );
		$user_rate 	= get_post_meta( $eventid, 'pec_user_rate', true );
		

		if( $user_rate != "" ) {
			$star1 = count( get_post_meta( $eventid, 'pec_user_rate_1star' ) );
			$star2 = count( get_post_meta( $eventid, 'pec_user_rate_2star' ) );
			$star3 = count( get_post_meta( $eventid, 'pec_user_rate_3star' ) );
			$star4 = count( get_post_meta( $eventid, 'pec_user_rate_4star' ) );
			$star5 = count( get_post_meta( $eventid, 'pec_user_rate_5star' ) );
			
			$total_votes = $star1 + $star2 + $star3 + $star4 + $star5;
			
			if( $total_votes == 0 ) {
				$rate = 0;
			} else {
				$rate = ((
					$star1 +
					($star2 * 2) +
					($star3 * 3) +
					($star4 * 4) +
					($star5 * 5)) /
						$total_votes);
			}
		}
		
		if( $rate != '' || $rate === 0 ) 
		{
			
			$s1 = '<i class="far fa-star"></i>';
			if( $rate > 0 && $rate < 1 )
				$s1 = '<i class="fa fa-star-half"></i>';
			
			if( $rate >= 1 )
				$s1 = '<i class="fas fa-star"></i>';
			
			$s2 = '<i class="far fa-star"></i>';
			if( $rate > 1 && $rate < 2 )
				$s2 = '<i class="fa fa-star-half"></i>';
			
			if( $rate >= 2 )
				$s2 = '<i class="fas fa-star"></i>';
			
			$s3 = '<i class="far fa-star"></i>';
			if( $rate > 2 && $rate < 3 )
				$s3 = '<i class="fa fa-star-half"></i>';
			
			if( $rate >= 3 )
				$s3 = '<i class="fas fa-star"></i>';
			
			$s4 = '<i class="far fa-star"></i>';
			if( $rate > 3 && $rate < 4 )
				$s4 = '<i class="fa fa-star-half"></i>';
			
			if( $rate >= 4 )
				$s4 = '<i class="fas fa-star"></i>';
			
			$s5 = '<i class="far fa-star"></i>';
			if( $rate > 4 && $rate < 5 )
				$s5 = '<i class="fa fa-star-half"></i>';
			
			if( $rate >= 5 )
				$s5 = '<i class="fas fa-star"></i>';
			
			$html = '<div class="pec-rating">';

			$html .= '<span class="pec-rating-n">' . $rate . '<i class="fas fa-star"></i></span>';

			if( $expand )
			{
				$html .= '
				<ul class="dp_pec_rate ' . ( $user_rate != "" && is_user_logged_in() ? 'dp_pec_user_rate' : '' ) . '">
					<!--<li>
						' . ( $user_rate != "" && is_user_logged_in() ? '<a href="#" data-rate-val="1" data-event-id="'.$eventid.'">' . $s1 . '</a>' : '<span>' . $s1 . '</a>') . '
					</li>-->
					<li>
						' . ( $user_rate != "" && is_user_logged_in() ? '<a href="#" data-rate-val="1" data-event-id="'.$eventid.'">' . $s2 . '</a>' : '<span>' . $s2 . '</a>') . '
					</li>
					<li>
						' . ( $user_rate != "" && is_user_logged_in() ? '<a href="#" data-rate-val="1" data-event-id="'.$eventid.'">' . $s3 . '</a>' : '<span>' . $s3 . '</a>') . '
					</li>
					<li>
						' . ( $user_rate != "" && is_user_logged_in() ? '<a href="#" data-rate-val="1" data-event-id="'.$eventid.'">' . $s4 . '</a>' : '<span>' . $s4 . '</a>') . '
					</li>
					<li>
						' . ( $user_rate != "" && is_user_logged_in() ? '<a href="#" data-rate-val="1" data-event-id="'.$eventid.'">' . $s5 . '</a>' : '<span>' . $s5 . '</a>') . '
					</li>
				</ul>';
			}

			$html .= "</div>";
		}
		
		return $html;
	}

	protected static function get_references () 
	{

		if( ! self::$calendar_obj->show_references ) 
			return '';

		$specialDatesList = self::getSpecialDatesList();
	
		$html = '<a href="#" class="dp_pec_references dp_pec_btnright">' . self::$translation['TXT_COLOR_CODE'] . '</a>';
		$html .= '<div class="dp_pec_references_div">';
		$html .= '<a href="#" class="dp_pec_references_close"><i class="fa fa-times"></i></a>';

		$html .= '<ul class="dp_pec_references_div_sp">';
		$html .= '<li>';
		$html .= 	'<div class="dp_pec_references_color" style="background-color: ' . self::$calendar_obj->current_date_color . '"></div>';
		$html .= 	'<h4 class="dp_pec_references_title">' . self::$translation['TXT_CURRENT_DATE'] . '</h4>';
		$html .= 	'<div class="dp_pec_clear"></div>';
		$html .= '</li>';
	
		if( count( $specialDatesList ) > 0 ) 
		{
		
			foreach( $specialDatesList as $key ) 
			{
			
				$html .= '<li>';
				$html .= 	'<div class="dp_pec_references_color" style="background-color: ' . $key->color . '"></div>';
				$html .= 	'<h4 class="dp_pec_references_title">' . $key->title . '</h4>';
			
				$html .= '</li>';
			
			}
		
		}
		
		$html .= '</ul>';

		$html .= '</div>';

		return $html;

	}

	protected static function get_speaker_dropdown ()
	{

		$html = '';

		if( false && empty( self::$opts['speaker'] ) ) 
		{

			$html .= '<select autocomplete="off" name="pec_speaker" class="pec_speaker_list">';

			$html .= '<option value="">'.self::$translation['TXT_ALL_SPEAKERS'].'</option>';

			$args = array(
				'posts_per_page'   => -1,
				'post_type'        => DP_PRO_EVENT_CALENDAR_SPEAKERS_POST_TYPE,
				'post_status'      => 'publish',
				'order'			   => 'ASC', 
				'lang'			   => (defined('ICL_LANGUAGE_CODE') ? ICL_LANGUAGE_CODE : strtolower(substr(get_locale(),3,2))),
				'suppress_filters'  => false,
				'orderby' 		   => 'title' 
			);

			if( self::$calendar_obj->venue_filter_include != "" ) 
				$args['include'] = self::$calendar_obj->venue_filter_include;

			$list = get_posts( $args );

			foreach( $list as $key ) 
				$html .= '<option value="' . $key->ID . '">' . $key->post_title . '</option>';

			$html .= '</select>';

		}

		return $html;

	}

	protected static function get_location_dropdown ()
	{

		$html = '';

		if( self::$calendar_obj->show_location_filter && empty( self::$opts['location'] ) ) 
		{

			$html .= '<select autocomplete="off" name="pec_location" class="pec_location_list">';

			$html .= '<option value="">'.self::$translation['TXT_ALL_LOCATIONS'].'</option>';

			$args = array(
				'posts_per_page'   => -1,
				'post_type'        => DP_PRO_EVENT_CALENDAR_VENUES_POST_TYPE,
				'post_status'      => 'publish',
				'order'			   => 'ASC', 
				'lang'			   => (defined('ICL_LANGUAGE_CODE') ? ICL_LANGUAGE_CODE : strtolower(substr(get_locale(),3,2))),
				'suppress_filters'  => false,
				'orderby' 		   => 'title' 
			);

			if( self::$calendar_obj->venue_filter_include != "" ) 
				$args['include'] = self::$calendar_obj->venue_filter_include;

			$list = get_posts( $args );

			foreach( $list as $key ) 
				$html .= '<option value="' . $key->ID . '">' . $key->post_title . '</option>';

			$html .= '</select>';

		}

		return $html;

	}

	protected static function get_categories_dropdown ()
	{

		$html = '';

		if( self::$calendar_obj->show_category_filter && empty( self::$category ) ) 
		{

			$html .= '<select autocomplete="off" name="pec_categories" class="pec_categories_list">';

			$html .= '<option value="">' . self::$translation['TXT_ALL_CATEGORIES'] . '</option>';
					
			$cat_args = array(
				'taxonomy' => 'pec_events_category',
				'hide_empty' => 0
			);

			if( self::$calendar_obj->category_filter_include != "" ) 
				$cat_args['include'] = self::$calendar_obj->category_filter_include;

			$categories = get_categories($cat_args); 

			foreach ( $categories as $category ) 
			{
			
				$html .= '<option value="'.$category->term_id.'">';
				$html .= $category->cat_name;
				$html .= '</option>';
			
			}

			$html .= '</select>';

		}

		return $html;

	}

	protected static function get_month_dropdown ()
	{


		$html = '<select autocomplete="off" class="pec_switch_month">';


		foreach( self::$translation['MONTHS'] as $key ) 
			$html .= '<option value="' . $key . '" ' . ( $key == self::$translation['MONTHS'][(self::$datesObj->currentMonth - 1 )] ? 'selected="selected"':'') . '>' . $key . '</option>';
		

		$html .= '</select>';

		return $html;

	}

	protected static function get_year_dropdown ()
	{

		$html = '<select autocomplete="off" class="pec_switch_year">';

		$year_from = pec_setting( 'year_from', 2 );
		$year_until = pec_setting( 'year_until', 3 );

		for( $i = date('Y') - $year_from; $i <= date('Y') + $year_until; $i++ ) 
			$html .= '<option value="' . $i . '" ' . ( $i == self::$datesObj->currentYear ? 'selected="selected"' : '' ) . '>' . $i . '</option>';
		
		$html .= '</select>';


		return $html;


	}

	protected static function get_end_datetime( $event, $only_time = false, $remove_year = false ) 
	{

		$time = self::date_i18n( self::$time_format, strtotime( $event->date ) );

		$end_datetime = array();

		if( ! $only_time )
		{

			$end_date = '';
			$end_year = '';
			$end_day = '';
			$end_month = '';
			if( $event->end_date != "" && $event->end_date != "0000-00-00" && $event->recurring_frecuency == 1 ) 
			{
				$end_day = date( 'd', strtotime( $event->end_date ) );
				$end_month = date( 'n', strtotime( $event->end_date ) );
				$end_year = date( 'Y', strtotime( $event->end_date ) );

				// Get Date Format
				$date_format = get_option('date_format');

				// Remove Year
				if( $remove_year )
					$date_format = self::remove_year( $date_format );
				
				//$end_date = ' / <br />'.$end_day.' '.substr(self::$translation['MONTHS'][($end_month - 1)], 0, 3).', '.$end_year;
				$end_date = ' '.self::$translation['TXT_TO'].' '.self::date_i18n( $date_format, strtotime( $event->end_date ) );

				if( date( "Y-m-d", strtotime( $event->date ) ) == date( "Y-m-d", strtotime( $event->end_date ) ) ) 
					$end_date = '';
			
			}

			$end_datetime['end_day'] = $end_day;
			$end_datetime['end_month'] = $end_month;
			$end_datetime['end_year'] = $end_year;
			$end_datetime['end_date'] = $end_date;
		
		}

		$end_time = "";
		if( $event->end_time_hh != "" && $event->end_time_mm != "" ) { $end_time = str_pad( $event->end_time_hh, 2, "0", STR_PAD_LEFT ) . ":" . str_pad( $event->end_time_mm, 2, "0", STR_PAD_LEFT ); }
		
		if( $end_time != "" ) 
		{
			
			$end_time_tmp = self::date_i18n( self::$time_format, strtotime( "2000-01-01 ".$end_time.":00" ) );

			$end_time = " / " . $end_time_tmp;
			if( $end_time_tmp == $time ) 
				$end_time = "";	
			
		}

		$end_datetime['end_time'] = $end_time;

		return $end_datetime;

	}

	protected static function ical_button()
	{

		if( self::$calendar_obj->ical_active ) 
		
			$html = "<a class='dpProEventCalendar_feed pec-ical' href='" . str_replace( array('http', 'https'), 'webcal', site_url( '?' . DP_PRO_EVENT_CALENDAR_ICAL . '=' . self::$id_calendar ) ) . "'><i class='fa fa-calendar-plus-o'></i><span>iCal</span></a>";

		return ( $html ? $html : '' );


	}

	protected static function add_layout_buttons ()
	{

		if( self::$calendar_obj->show_view_buttons ) 
		{

			$html = '<a href="#" class="dp_pec_view dp_pec_view_action ' . (self::$calendar_obj->view == "monthly" || self::$calendar_obj->view == "monthly-all-events" ? "active" : "") . '" data-pec-view="monthly">' . self::$translation['TXT_MONTHLY'] . '</a>';

			$html .= '<a href="#" class="dp_pec_view dp_pec_view_action ' . (self::$calendar_obj->view == "weekly" ? "active" : "") . '" data-pec-view="weekly">' . self::$translation['TXT_WEEKLY'] . '</a>';

			$html .= '<a href="#" class="dp_pec_view dp_pec_view_action ' . (self::$calendar_obj->view == "daily" ? "active" : "") . '" data-pec-view="daily">' . self::$translation['TXT_DAILY'] . '</a>';
			
		}

		return ( $html ? $html : '' );


	}

	// Chceck if RSS is enabled
	public static function rss_enabled()
	{
		return self::$calendar_obj->rss_active;
	}

	protected static function rss_button()
	{

		if( self::rss_enabled() ) 
		
			$html = "<a class='dpProEventCalendar_feed pec-rss' target='_blank' href='" . site_url( '?' . DP_PRO_EVENT_CALENDAR_RSS . '=' . self::$id_calendar ) . "'><i class='fa fa-rss'></i><span>RSS</span></a>";
		
		return ( $html ? $html : '' );


	}

	protected static function subscribers_form() 
	{

		if( self::$calendar_obj->subscribe_active ) 
		{
		
			$html = "<a class='dpProEventCalendar_feed dpProEventCalendar_subscribe' href='#'>".self::$translation['TXT_SUBSCRIBE']."</a>";

			$html .= '<div class="dpProEventCalendar_subscribe_form">';

			$html .= 	'<h3>' . esc_attr__(self::$translation['TXT_SUBSCRIBE_SUBTITLE']) . '</h3>';

			$html .= 	'<form>';

			$html .= self::$notifications->message( 'success', self::$translation['TXT_SUBSCRIBE_THANKS'] );

			$html .= self::$notifications->message( 'error', self::$translation['TXT_FIELDS_REQUIRED'] );

			$html .= 	'<div class="dp_pec_clear"></div>';

			$html .= 	'<input type="text" name="your_name" id="dpProEventCalendar_your_name" class="dpProEventCalendar_input dpProEventCalendar_from_name" placeholder="' . esc_attr__(self::$translation['TXT_YOUR_NAME']) . '" />';

			$html .= 	'<input type="text" name="your_email" id="dpProEventCalendar_your_email" class="dpProEventCalendar_input dpProEventCalendar_from_email" placeholder="' . esc_attr__(self::$translation['TXT_YOUR_EMAIL']) . '" />';
			
			if( pec_setting( 'recaptcha_enable' ) && pec_setting( 'recaptcha_site_key' ) != "" ) 
			{
			
				$html .= '<div class="dp_pec_clear"></div>';
				$html .= '<div id="pec_subscribe_captcha"></div>';
			
			}
			
			$html .=	'<div class="dp_pec_clear"></div>';
			$html .=	'<div class="pec-add-footer">';
			$html .= 	'<input type="button" class="dpProEventCalendar_send dpProEventCalendar_action" name="" value="' . esc_attr__( self::$translation['TXT_SUBSCRIBE'] ) . '" />';
			
			$html .= 	'<span class="dpProEventCalendar_sending_email"></span>';

			$html .=	'<div class="dp_pec_clear"></div>';

			$html .=	'</div>';
			
			$html .= 	'</form>';

			$html .= '</div>';

			return $html;
		
		}

		return '';

	}
	
	function rateEvent( $eventid, $rate ) 
	{
		
		if( is_user_logged_in() && is_numeric( $eventid ) ) 
		{
			global $current_user;
			wp_get_current_user();
			
			delete_post_meta( $eventid, 'pec_user_rate_1star', $current_user->ID );
			delete_post_meta( $eventid, 'pec_user_rate_2star', $current_user->ID );
			delete_post_meta( $eventid, 'pec_user_rate_3star', $current_user->ID );
			delete_post_meta( $eventid, 'pec_user_rate_4star', $current_user->ID );
			delete_post_meta( $eventid, 'pec_user_rate_5star', $current_user->ID );
			
			add_post_meta( $eventid, 'pec_user_rate_' . $rate . 'star', $current_user->ID );
								
			return self::get_rating( $eventid );
		}
		
	}

	private function is_rtl()
	{

		$isRTL = 0;

		$rtl = pec_setting( 'rtl_support', 0 );

		if( $rtl || ( isset( self::$opts['rtl'] ) && self::$opts['rtl'] ) || is_rtl() )
			$isRTL = 1;

		return $isRTL;

	}

	/**
	 * Get Map
	 * 
	 * @return string
	 */
	private static function get_map( $map_id, $map, $location_id = "", $geocode = false, $overlay = true ) 
	{

		if( $map == "" ) 
			return '';

		$title = "";
		$image = "";
		$address = "";
		$phone = "";
		$link = "";

		$html = '';

		if( is_numeric( $location_id ) ) 
		{

			$title = get_the_title( $location_id );
			$image = esc_url( get_the_post_thumbnail_url( $location_id, 'medium' ) );
			if( $image != "" )
				$image = "background-image: url('" . $image . "')";
			$address = get_post_meta( $location_id, 'pec_venue_address', true );
			$phone = get_post_meta( $location_id, 'pec_venue_phone', true );
			$link = get_post_meta( $location_id, 'pec_venue_link', true );
			$map_lnlat = get_post_meta( $location_id, 'pec_venue_map_lnlat', true );
			
		}

		if( $overlay )
			$html = '<div class="dp_pec_date_event_map_overlay" onClick="style.pointerEvents=\'none\'"></div>';

		$html .= '<div id="mapCanvas_' . $map_id . '" class="dp_pec_date_event_map_canvas" style="height: 350px;"></div>';

		$html .= '

			<script type="text/javascript">

				function initialize_' . $map_id . '() {
					var marker,
						map;
					var image = "' . addslashes( $image ) . '";
					var title = "' . addslashes( $title ) . '";
					var address = "' . addslashes( $address ) . '";
					var phone = "' . addslashes( $phone ) . '";
					var link = "' . addslashes( $link ) . '";

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


					var div_class = "dp_pec_map_infowindow";
					if(image == "") {
						div_class += " dp_pec_map_no_img";
					} else {
						image = \'<div class="dp_pec_map_image" style="\' + image + \'"></div>\';
					}
					

					var content = \'<div class="\'+div_class+\'">\'
							+image
							+\'<span class="dp_pec_map_title">\'+title+\'</span>\';

						if(address != "") {
							content += \'<span class="dp_pec_map_location"><i class="fa fa-map-marker"></i>\'+address+\'</span>\';
						}

						if(phone != "") {
							content += \'<span class="dp_pec_map_phone"><i class="fa fa-phone"></i>\'+phone+\'</span>\';
						}

						if(link != "") {
							content += \'<span class="dp_pec_map_link"><i class="fa fa-link"></i><a href="\'+link+\'" target="_blank" rel="nofollow">'.self::$translation['TXT_VISIT_WEBSITE'].'<\/a></span>\';
						}

					content +=\'<div class="dp_pec_clear"></div>\'
						+\'</div>\';

					infoBubble.setContent(content);
						';

				if( $geocode || $map_lnlat == "" )
				{
				
					$html .= '
						var latLng;
						geocoder = new google.maps.Geocoder();
				 		geocoder.geocode( { "address": "'.$map.'"}, function(results, status) {

						   	latLng = results[0].geometry.location;

						   	map = new google.maps.Map(document.getElementById("mapCanvas_'.$map_id.'"), {
								zoom: ' . ( pec_setting( 'google_map_zoom' ) == "" ? 10 : pec_setting( 'google_map_zoom') ) . ',
								center: latLng,
								disableDefaultUI: false,
								mapTypeId: google.maps.MapTypeId.ROADMAP
							});

							marker = new google.maps.Marker({
								position: latLng,
								map: map,
								icon: "' . pec_setting( 'map_marker' ) . '"
							});
							

							infoBubble.open(map, marker);

					   });';
				
				} else {
				
					$html .= '
						var latLng = new google.maps.LatLng('.$map.');

						map = new google.maps.Map(document.getElementById("mapCanvas_'.$map_id.'"), {
							zoom: '.( pec_setting( 'google_map_zoom' ) == "" ? 10 : pec_setting( 'google_map_zoom' ) ).',
							center: latLng,
							disableDefaultUI: false,
							mapTypeId: google.maps.MapTypeId.ROADMAP
						});

						marker = new google.maps.Marker({
							position: latLng,
							map: map,
							icon: "' . pec_setting( 'map_marker' ) . '"
						});

						infoBubble.open(map, marker);
						';
				
				}
				
				$html .= '
					/*google.maps.event.addListenerOnce(map, \'idle\', function() {
					    google.maps.event.trigger(map, \'resize\');
					});*/
				}

				if(document.readyState === "complete") {
					initialize_'.$map_id.'();
				} else {
					// Onload handler to fire off the app.
					jQuery(document).ready(function() {
						if (typeof google !== "undefined") {
							google.maps.event.addDomListener(window, "load", initialize_'.$map_id.');
						}
					});
				}</script>';
	
		return $html;
	
	}
	
	/**
	 * Get More Options HTML
	 * 
	 * @return string
	 */
	protected static function get_more_options( $event, $options = array( 'book_event', 'ical', 'link', 'fb_event', 'edit_event', 'remove_event' ) ) {


		$html_list = "";

		if( in_array( 'book_event', $options ) )
		{

			$booking_booked = self::getBookingBookedLabel( $event->id, $event->date );

			if( $booking_booked == "" ) 
			{

				$html_list .= "<li>";
				$html_list .= self::get_booking_button($event->id, date('Y-m-d', strtotime($event->date)), false, false);
				$html_list .= "</li>";
			
			}
		}

		if( in_array( 'ical', $options ) && self::$calendar_obj->ical_active && ! post_password_required( $event->id ) ) 
		{
			
			$event_code = $event->id;
			if( $event->code != '' )
				$event_code = $event->code;

			$html_list .= "<li>";
			$html_list .= "<a href='" . site_url( '?' . DP_PRO_EVENT_CALENDAR_ICAL_EVENT . '=' . $event_code . '&d=' . strtotime( $event->date ) ) . "'>iCal</a>";
			$html_list .= "</li>";
		
		}

		if( in_array( 'link', $options ) && $event->link != '' ) 
		{
		
			$event->link = trim( $event->link );
			$text = self::$translation['TXT_EMAIL'];

			if( strpos( $event->link, "@" ) !== false ) 
				$event->link = 'mailto:' . $event->link;
			
			if( substr( $event->link, 0, 4 ) != "mail" ) 
			{
				
				$event->link = str_replace( 'http://', '', $event->link );

				$event->link = 'https://' . $event->link;
				$text = self::$translation['TXT_VISIT_WEBSITE'];
			
			}

			$html_list .= '<li><a href="' . $event->link . '" rel="nofollow" target="_blank">' . $text . '</a></li>';
		
		}

		if( in_array( 'fb_event', $options ) && $event->pec_fb_event != '' ) 
		{
		
			$event->pec_fb_event = trim( $event->pec_fb_event );
			$text = self::$translation['TXT_VISIT_FB_EVENT'];

			$html_list .= '<li><a href="' . $event->pec_fb_event . '" rel="nofollow" target="_blank">' . $text . '</a></li>';
		
		}
		
		
		if( ! isset( self::$opts['allow_user_edit_remove'] ) || self::$opts['allow_user_edit_remove'] == "" ) 
			self::$opts['allow_user_edit_remove'] = 1;
		
		if( is_user_logged_in() && (( self::$calendar_obj->allow_user_edit_event || self::$calendar_obj->allow_user_remove_event ) && self::$opts['allow_user_edit_remove'] ) ) 
		{
		
			global $current_user;
		
			wp_get_current_user();
			

			if( in_array( 'edit_event', $options ) && ( $current_user->ID == get_post_field( 'post_author', $event->id) || current_user_can('switch_themes') ) && (self::$calendar_obj->allow_user_edit_event && self::$opts['allow_user_edit_remove'])) 
			
				$html_list .= '<li><a href="#" title="'.self::$translation['TXT_EDIT_EVENT'].'" data-event-id="'.$event->id.'" class="pec_edit_event">'.self::$translation['TXT_EDIT_EVENT'].'</a></li>';	
			
			
			if( in_array( 'remove_event', $options ) && ($current_user->ID == get_post_field( 'post_author', $event->id) || current_user_can('switch_themes' )) && (self::$calendar_obj->allow_user_remove_event && self::$opts['allow_user_edit_remove'])) 
			{
			
				$html_list .= '<li><a href="#" title="'.self::$translation['TXT_REMOVE_EVENT'].'" class="pec_remove_event">'.self::$translation['TXT_REMOVE_EVENT'].'</a><div style="display:none;">
				<form enctype="multipart/form-data" method="post" class="dp_pec_new_event_wrapper add_new_event_form remove_event_form">
			
				<input type="hidden" value="'.self::$id_calendar.'" name="remove_event_calendar">
				<input type="hidden" value="'.$event->id.'" name="remove_event">
				<h3>'.self::$translation['TXT_REMOVE_EVENT_CONFIRM'].'</h3>
				<div class="dp_pec_clear"></div>
				<div class="pec-add-footer">
					<div class="pec-add-footer-wrap">
						<button class="dp_pec_remove_event">'.self::$translation['TXT_YES'].'</button>
						<button class="dp_pec_close pec_action_btn pec_action_btn_secondary">'.self::$translation['TXT_NO'].'</button>
					</div>
					<div class="dp_pec_clear"></div>
				</div>
				</form>
				</div></li>';	
				$html_list .= '';		
			
			}
		
		}

		if( $html_list == '' )
			return '';

		$html = '<div class="dp_pec_more_options_wrap">';

		$html .= '<a href="#" class="dp_pec_more_options">...</a>';

		$html .= '<div class="dp_pec_more_options_hidden">';

		$html .= '<ul>';

		$html .= $html_list;

		$html .= '</ul>';

		$html .= '</div>';

		$html .= '</div>';
		
		return $html;
	
	}

	/**
	 * Check if user has bookings
	 * 
	 * @return int
	 */
	function user_has_bookings ( $date = '', $event_id ) 
	{

		global $current_user, $wpdb;
		
		if( ! is_user_logged_in() ) 
			return false;	
		
		$id_list = $event_id;
        
        if( function_exists( 'icl_object_id' ) ) 
        {
        
            global $sitepress;

            if( is_object( $sitepress ) ) 
            {
	    
	            $id_list_arr = array();
				$trid = $sitepress->get_element_trid( $event_id, 'post_pec-events' );
				$translation = $sitepress->get_element_translations( $trid, 'post_pec-events' );

				foreach($translation as $key) 
				{
				
					$id_list_arr[] = $key->element_id;
				
				}

				if( ! empty( $id_list_arr ) ) 
					$id_list = implode(",", $id_list_arr);
			
			}
		
		}

		$querystr = "
            SELECT count(*) as counter
            FROM " . DP_PRO_EVENT_CALENDAR_TABLE_BOOKING . "
			WHERE id_event = (".$id_list.") 
			AND id_user = ".$current_user->ID." 
			";

		if( $date != "" ) 
		{
		
				$querystr .= "
			AND event_date = '".$date."' ";
		
		}

		$querystr .= "
			AND status <> '" . DP_PRO_EVENT_CALENDAR_BOOKING_PENDING . "' 
			AND status <> '" . DP_PRO_EVENT_CALENDAR_BOOKING_CANCELED_BY_USER . "' 
			AND status <> '" . DP_PRO_EVENT_CALENDAR_BOOKING_CANCELED . "'
            ";
        
        $bookings_obj = $wpdb->get_results( $querystr, OBJECT );
		
		return $bookings_obj[0]->counter;
	
	}

	/**
	 * Get Pagination Number
	 * 
	 * @return string
	 */
	protected static function get_pagination_number ()
	{

		$pagination = ( is_numeric( pec_setting( 'pagination' ) ) && pec_setting( 'pagination' ) > 0 ? pec_setting( 'pagination' ) : 10 );
		if( isset( self::$opts['pagination'] ) && is_numeric( self::$opts['pagination'] ) && self::$opts['pagination'] > 0 )
			$pagination = self::$opts['pagination'];

		return $pagination;

	}

	/**
	 * Get Frequency Name Translated
	 * 
	 * @return string
	 */
	protected function get_frequency_name ( $frequency ) 
	{

		switch ( $frequency ) {

			case 1:
				return self::$translation['TXT_EVENT_DAILY'];
				break;

			case 2:
				return self::$translation['TXT_EVENT_WEEKLY'];
				break;

			case 3:
				return self::$translation['TXT_EVENT_MONTHLY'];
				break;

			case 4:
				return self::$translation['TXT_EVENT_YEARLY'];
				break;

			default:
				return self::$translation['TXT_NONE'];
				break;

		}

	}

	/**
	 * Get Day Name translated
	 * 
	 * @return string
	 */
	protected function get_day_name ( $day ) 
	{

		switch ( $day ) {

			case 1:
				return self::$translation['DAY_MONDAY'];
				break;

			case 2:
				return self::$translation['DAY_TUESDAY'];
				break;

			case 3:
				return self::$translation['DAY_WEDNESDAY'];
				break;

			case 4:
				return self::$translation['DAY_THURSDAY'];
				break;

			case 5:
				return self::$translation['DAY_FRIDAY'];
				break;

			case 6:
				return self::$translation['DAY_SATURDAY'];
				break;

			case 7:
				return self::$translation['DAY_SUNDAY'];
				break;

		}

	}

	/**
	 * Add Event Button HTML
	 * 
	 * @return string
	 */
	private static function add_event_button ( $compact = false, $time = '' ) 
	{

		// Check if we should add the event button
		if( ! self::can_add_event() ) return '';

		if( $compact )
		{

			$html = '<a href="#" data-time="' . $time . '" title="'.str_replace('+', '', self::$translation['TXT_ADD_EVENT']).'" class="dp_pec_add_event">';
			$html .= '<i class="fa fa-plus-circle"></i>';
			$html .= '</a>';

		} else {

			$html = '<a href="#" title="' . str_replace('+', '', self::$translation['TXT_ADD_EVENT']) . '" class="dp_pec_view dp_pec_add_event pec_action_btn dp_pec_btnright">';
			$html .= '<i class="fa fa-plus-circle"></i><span>' . str_replace('+', '', self::$translation['TXT_ADD_EVENT']) . '</span>';
			$html .= '</a>';

		}

		return $html;


	}

	/**
	 * Check if user can add Event
	 * 
	 * @return void
	 */
	private static function can_add_event ()
	{

		if( isset( self::$opts['new_event'] ) && self::$opts['new_event'] == "0" )
			return false;

		$allow_user_add_event_roles = explode(',', self::$calendar_obj->allow_user_add_event_roles);
		$allow_user_add_event_roles = array_filter($allow_user_add_event_roles);
		
		if( ! is_array( $allow_user_add_event_roles ) || empty( $allow_user_add_event_roles ) || $allow_user_add_event_roles == "" )
			$allow_user_add_event_roles = array('all');	
		
		if( ( self::$calendar_obj->allow_user_add_event || self::$opts['new_event'] == "1" ) && 
			! self::$is_admin && 
				(in_array(dpProEventCalendar_get_user_role(), $allow_user_add_event_roles) || 
				 in_array('all', $allow_user_add_event_roles) || 
				 ( ! is_user_logged_in() && ! self::$calendar_obj->assign_events_admin)
			    )
		) {

			return true;

		}

		return false;


	}

	/**
	 * Get Event Bookings
	 * 
	 * @return array
	 */
	private static function getEventBookings( $counter = false, $date = "", $event_id ) 
	{
	
		global $wpdb;
		
		$id_list = $event_id;
        if( function_exists( 'icl_object_id' ) ) 
        {
        
            global $sitepress;

            if( is_object( $sitepress ) ) 
            {
	        
	            $id_list_arr = array();

				$trid = $sitepress->get_element_trid( $event_id, 'post_pec-events' );
				$translation = $sitepress->get_element_translations( $trid, 'post_pec-events' );

				foreach( $translation as $key ) 
				{
				
					$id_list_arr[] = $key->element_id;
				
				}

				if( ! empty( $id_list_arr ) ) 
					$id_list = implode( ",", $id_list_arr );
		
			}
		
		}

		$pec_booking_continuous = get_post_meta( $event_id, 'pec_booking_continuous', true );

		if( $counter ) 
			$querystr = "SELECT quantity";
		else
			$querystr = "SELECT *";
		
		$querystr .= "
            FROM " . DP_PRO_EVENT_CALENDAR_TABLE_BOOKING . "
			WHERE id_event IN(" . $id_list . ") AND status <> '" . DP_PRO_EVENT_CALENDAR_BOOKING_PENDING . "' AND status <> '" . DP_PRO_EVENT_CALENDAR_BOOKING_CANCELED_BY_USER . "' AND status <> '" . DP_PRO_EVENT_CALENDAR_BOOKING_CANCELED . "'
            ";
		
		if( ! empty($date) && !$pec_booking_continuous ) 
			$querystr .= "AND event_date = '".$date."'";	
		
		if( $counter ) 
		{
		
			$bookings_obj = $wpdb->get_results( $querystr, OBJECT );
			$counter = 0;
			foreach( $bookings_obj as $booking ) 
			{
			
				$counter += $booking->quantity;
			
			}
			
			return $counter;
		
		} else {
		
			$bookings_obj = $wpdb->get_results( $querystr, OBJECT );
			
			return $bookings_obj;
		
		}
	
	}

	/**
	 * Create Youtube Embed Link
	 * 
	 * @return string
	 */
	protected function convert_youtube( $string ) 
	{
	    return preg_replace(
	        "/\s*[a-zA-Z\/\/:\.]*youtu(be.com\/watch\?v=|.be\/)([a-zA-Z0-9\-_]+)([a-zA-Z0-9\/\*\-\_\?\&\;\%\=\.]*)/i",
	        "<iframe src=\"//www.youtube.com/embed/$2\" allowfullscreen></iframe>",
	        $string
	    );

	}

	/**
	 * Detect Link in text
	 * 
	 * @return string
	 */
	 protected function detect_link( $string ) 
	{

		$url = '~(?:(https?)://([^\s<]+)|(www\.[^\s<]+?\.[^\s<]+))(?<![\.,:])~i'; 
		$string = preg_replace( $url, '<a href="$0" target="_blank" title="$0">$0</a>', $string );

		return $string;

	}

	/**
	 * Detect Emails
	 * 
	 * @return string
	 */
	protected function detect_email( $str )
	{

	    //Detect and create email
	    $mail_pattern = "/([A-z0-9\._-]+\@[A-z0-9_-]+\.)([A-z0-9\_\-\.]{1,}[A-z])/";
	    $str = preg_replace( $mail_pattern, '<a href="mailto:$1$2">$1$2</a>', $str );

	    return $str;

	}

	/**
	 * Trim Words
	 * 
	 * @return string
	 */

	protected static function trim_words( $text, $limit ) 
	{

		$text = preg_replace('/\<[\/]{0,1}div[^\>]*\>|\<[\/]{0,1}span[^\>]*\>/i', '', $text);

		$text = htmlentities( $text );
		if ( str_word_count( $text, 0 ) > $limit ) 
		{
		
			$words = str_word_count($text, 2);
			$pos = array_keys($words);
			$text = substr( $text, 0, $pos[$limit] ) . '...';

			$start_tag = strrpos( $text, "&lt;a href=&quot;" );
			$end_tag = strrpos( $text, "&gt;" );
			//$text.= $start_tag."----".$end_tag;
			if( $start_tag > $end_tag ) 
				$text .= "&quot;&gt;";
			

			$start_tag = strrpos($text, "&lt;p style=&quot;");
			$end_tag = strrpos($text, "&gt;");
			//$text.= $start_tag."----".$end_tag;
			if( $start_tag > $end_tag ) 
				$text .= "&quot;&gt;";
			
		
		}

		return $text;

	}

	/**
	 * PHP Date to Datepicker
	 * 
	 * @return string
	 */

	protected function phpdate_to_datepicker () 
	{

		$date_format = get_option( 'date_format' );

		$chars = str_split( $date_format );
		$datepicker_date = '';

		foreach( $chars as $char ) 
		{    

		    switch( $char ) 
		    {

		    	case "d":
			    	$char = str_replace("d", "dd", $char);
			    	break;

			    case "j":
					$char = str_replace("j", "d", $char);
					break;

				case "l":
					$char = str_replace("l", "DD", $char);
					break;

				case "n":
					$char = str_replace("n", "m", $char);
					break;

				case "F":
					$char = str_replace("F", "MM", $char);
					break;

				case "m":
					$char = str_replace("m", "mm", $char);
					break;

				case "Y":
					$char = str_replace("Y", "yy", $char);
					break;
			}

			$datepicker_date .= $char;


		}

		return $datepicker_date;


	}

	/**
	 * String split unicode
	 * 
	 * @return array
	 */
	function str_split_unicode( $str, $l = 0 ) 
	{

	    if ( $l > 0 ) 
	    {
	        $ret = array();
	        $len = mb_strlen( $str, "UTF-8" );
	        for ( $i = 0; $i < $len; $i += $l ) {
	            $ret[] = mb_substr( $str, $i, $l, "UTF-8" );
	        }
	        return $ret;
	    }
	    
	    return preg_split( "//u", $str, -1, PREG_SPLIT_NO_EMPTY );

	}
	
}
?>