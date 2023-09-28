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

class DpProEventCalendar_Init {

	public $admin = false;
	public $base = false;

	/**
	 * Class Constructor // install / uninstall / init hook
	 * 
	 * @return void
	 */
	function __construct(  ) 
	{
		
		global $dpProEventCalendar, $dpProEventCalendar_cache;

		$dpProEventCalendar = get_option('dpProEventCalendar_options');
		$dpProEventCalendar_cache = get_option( 'dpProEventCalendar_cache');

		// Load Text domain on 'plugins loaded' hook
		add_action( 'plugins_loaded', array( $this, 'load_textdomain') );

		// on install
		register_activation_hook( DP_PRO_EVENT_CALENDAR_PLUGIN_FILE, array( $this, 'checkMU_install') );

		// on uninstall
		register_uninstall_hook( DP_PRO_EVENT_CALENDAR_PLUGIN_FILE, array( 'DpProEventCalendar_Init', 'checkMU_uninstall') );

		// Install plugin when a new child site is created
		add_action( 'wpmu_new_blog', array( $this, 'new_blog' ), 10, 6); 	

		// Include plugin files
		$this->include_files();

		// Rewrite rules
		$this->rewrite_rules();

		// Actions on init hook
		add_action( 'init', array( $this, 'init_action' ) );

		// Content Filter
		add_filter( 'the_content', array( $this, 'content_filter' ) );

		// Register Rest API
		add_action( 'rest_api_init', array( $this, 'register_rest' ) );

		// Footer Stuff
		add_action( 'wp_footer', array($this, 'footer_stuff' ), 100 );

		//Add Featured Image Support
		add_action( 'after_setup_theme', array( $this, 'add_featured_image_support' ), 11 );

		/*
		if(is_plugin_active('dpliteeventcalendar/dpliteeventcalendar.php')) 
		{

			trigger_error('Please deactivate the Lite version of this plugin first, and try again.', E_USER_ERROR);

		}*/


	}

	/**
	 * Actions on init hook
	 * 
	 * @return void
	 */
	function init_action ()
	{

		// Setup post types
		$this->init_post_types();

		// Register Taxonomies
		$this->register_taxonomies();

		// Setup ical Sync
		$this->setup_ical_sync();

		// Setup Booking Reminder
		$this->setup_booking_reminder();

		// Setup expired events
		$this->setup_expired_events();

		if ( ! is_admin() )
		{ 

			// RSS feed
			if( isset( $_GET['pec-rss'] ) && is_numeric( $_GET['pec-rss'] ) )
				$this->rss_feed();

			// iCal feed
			if( isset( $_GET[ DP_PRO_EVENT_CALENDAR_ICAL ] ) && is_numeric( $_GET[ DP_PRO_EVENT_CALENDAR_ICAL ] ) )
				$this->ical_feed( 'calendar' );

			if( isset( $_GET[ DP_PRO_EVENT_CALENDAR_ICAL_EVENT ] ) && strlen( $_GET[ DP_PRO_EVENT_CALENDAR_ICAL_EVENT ] ) <= 11 )
				$this->ical_feed( 'event' );

			// Enqueue styles
			$this->enqueue_styles();
		}

	}

	/**
	 * Rewrite Rules if enabled
	 * 
	 * @return void
	 */
	function rewrite_rules() 
	{
		global $dpProEventCalendar;

		if(!isset($dpProEventCalendar['disable_rewrite_rules']) || !$dpProEventCalendar['disable_rewrite_rules']) 
		{

			register_deactivation_hook( DP_PRO_EVENT_CALENDAR_PLUGIN_FILE, 'flush_rewrite_rules' );
			register_activation_hook( DP_PRO_EVENT_CALENDAR_PLUGIN_FILE, array( $this, 'flush_rewrites' ) );

		}

	}

	/**
	 * Flush Old URLs
	 * 
	 * @return void
	 */
	function flush_rewrites() 
	{
		
		$this->init_post_types();
		flush_rewrite_rules();
	
	}

	/**
	 * Include required files for each section,
	 * 
	 * @return void
	 */
	function include_files()
	{

		include_once ( ABSPATH . 'wp-admin/includes/plugin.php' );

		require_once ( DP_PRO_EVENT_CALENDAR_PLUGIN_DIRNAME . 'functions.php' );

		// Admin
		if ( is_admin() && ! wp_doing_ajax() )
		{ 

			require_once ( DP_PRO_EVENT_CALENDAR_CLASSES_DIR . 'admin.class.php' );
			require_once ( DP_PRO_EVENT_CALENDAR_CLASSES_DIR . 'admin.form.class.php' );
			$this->admin = new DPPEC_Admin();

		}

		// Misc
		require_once ( DP_PRO_EVENT_CALENDAR_INCLUDES_DIR . 'core.php' );

		// Widgets
		require_once ( DP_PRO_EVENT_CALENDAR_WIDGETS_DIR . 'default.php' );
		require_once ( DP_PRO_EVENT_CALENDAR_WIDGETS_DIR . 'upcoming.php' );
		require_once ( DP_PRO_EVENT_CALENDAR_WIDGETS_DIR . 'accordion.php' );
		require_once ( DP_PRO_EVENT_CALENDAR_WIDGETS_DIR . 'add-event.php' );
		require_once ( DP_PRO_EVENT_CALENDAR_WIDGETS_DIR . 'today.php' );

		// Base class
		require_once ( DP_PRO_EVENT_CALENDAR_CLASSES_DIR . 'dates.class.php' );
		require_once ( DP_PRO_EVENT_CALENDAR_CLASSES_DIR . 'base.class.php' );

		// Notification
		require_once ( DP_PRO_EVENT_CALENDAR_CLASSES_DIR . 'notifications.class.php' );

		// Layouts
		require_once ( DP_PRO_EVENT_CALENDAR_CLASSES_DIR . 'default-layout.class.php' );
		require_once ( DP_PRO_EVENT_CALENDAR_CLASSES_DIR . 'event-form.class.php' );
		require_once ( DP_PRO_EVENT_CALENDAR_CLASSES_DIR . 'slider-layout.class.php' );
		require_once ( DP_PRO_EVENT_CALENDAR_CLASSES_DIR . 'carousel-layout.class.php' );
		require_once ( DP_PRO_EVENT_CALENDAR_CLASSES_DIR . 'card-layout.class.php' );
		require_once ( DP_PRO_EVENT_CALENDAR_CLASSES_DIR . 'countdown-layout.class.php' );
		require_once ( DP_PRO_EVENT_CALENDAR_CLASSES_DIR . 'timeline-layout.class.php' );
		require_once ( DP_PRO_EVENT_CALENDAR_CLASSES_DIR . 'gmap-layout.class.php' );
		require_once ( DP_PRO_EVENT_CALENDAR_CLASSES_DIR . 'grid-layout.class.php' );
		require_once ( DP_PRO_EVENT_CALENDAR_CLASSES_DIR . 'cover-layout.class.php' );
		require_once ( DP_PRO_EVENT_CALENDAR_CLASSES_DIR . 'yearly-layout.class.php' );
		require_once ( DP_PRO_EVENT_CALENDAR_CLASSES_DIR . 'rss.class.php' );
		require_once ( DP_PRO_EVENT_CALENDAR_CLASSES_DIR . 'ical.class.php' );
		require_once ( DP_PRO_EVENT_CALENDAR_CLASSES_DIR . 'bookings-user-layout.class.php' );

		// Ajax
		if( wp_doing_ajax() ) 
		{

			// Include Ajax Class
			require_once ( DP_PRO_EVENT_CALENDAR_CLASSES_DIR . 'ajax.class.php' );
			// Init Ajax class
			new DpProEventCalendar_Ajax();
		
		}

		// Modal
		require_once ( DP_PRO_EVENT_CALENDAR_CLASSES_DIR . 'event-modal.class.php' );

		// check db updates
		require_once ( DP_PRO_EVENT_CALENDAR_INCLUDES_DIR . 'db_updates.php' );

	}

	/**
	 * Init Base Class
	 * 
	 * @return DpProEventCalendar base class
	 */
	function init_base( $args = array() ) {

		$this->base = new DpProEventCalendar( $args );
		return $this->base;

	}

	/**
	 * Add Footer Stuff
	 * 
	 * @return void
	 */
	function footer_stuff( )
	{

		$this->footer_event_schema();
		
		// jQuery Code for single pages
		
		if( !is_singular( DP_PRO_EVENT_CALENDAR_EVENT_POST_TYPE ) )
			return;

		$base = $this->init_base();

		echo $base->add_scripts_single();

	}

	/**
	 * Add Footer Event Google Schema
	 * 
	 * @return void
	 */
	function footer_event_schema() 
	{

		global $wp_query, $pec_init;

		if( get_post_type() != DP_PRO_EVENT_CALENDAR_EVENT_POST_TYPE ) 
			return;
		

		$id = get_the_ID();
		$url = get_post_meta($id, 'pec_link', true);
		$calendar = get_post_meta($id, 'pec_id_calendar', true);
		$title = get_the_title();
		$startDate = get_post_meta($id, 'pec_date', true);
		$status = get_post_meta($id, 'pec_status', true);
		$attendance = get_post_meta($id, 'pec_attendance', true);
		
		if(isset($wp_query->query_vars['event_date']) && $wp_query->query_vars['event_date'] != "") {

			$startDate = date('Y-m-d H:i:s', (int)$wp_query->query_vars['event_date']);

		} elseif(strtotime($startDate) < time()) {

			$opts = array();
			$opts['id_calendar'] = $calendar;
			$opts['event_id'] = $id;
			$dpProEventCalendar_class = $pec_init->init_base( $opts );

			$event_dates = $dpProEventCalendar_class::upcomingCalendarLayout( true, 1 );

			if(is_array($event_dates) && !empty($event_dates)) 

				$startDate = $event_dates[0]->date;

		}

		$location = get_post_meta($id, 'pec_location', true);
		$age_range = get_post_meta($id, 'pec_age_range', true);
		$objDateTime = new DateTime($startDate);
		$isoDate = $objDateTime->format(DateTime::ISO8601);

		//Status
		$eventStatus = '';

		switch($status)
		{

			case 'cancelled':

				$eventStatus = '"eventStatus": "https://schema.org/EventCancelled",';
				break;

			case 'moved_online':

				$eventStatus = '"eventStatus": "https://schema.org/EventMovedOnline",';
				$attendance = 'online';
				break;

			case 'postponed':

				$eventStatus = '"eventStatus": "https://schema.org/EventPostponed",';
				break;

			case 'rescheduled':

				$eventStatus = '"eventStatus": "https://schema.org/EventRescheduled",';
				break;

		}

		// Attendance
		$eventAttendance = '';

		switch( $attendance )
		{

			case 'online':
				$eventAttendance = '"eventAttendanceMode": "https://schema.org/OnlineEventAttendanceMode",';
				break;

			case 'mixed':
				$eventAttendance = '"eventAttendanceMode": "https://schema.org/MixedEventAttendanceMode",';
				break;

		}

		echo '<script type="application/ld+json">
		{
		  "@context": "http://schema.org",
		  "@type": "Event",
		  "name": "'.addSlashes($title).'",
		  '.$eventStatus.'
		  '.$eventAttendance.'
		  "startDate" : "'.$isoDate.'",
		  "url" : "'.$url.'",
		  "typicalAgeRange" : "'.addSlashes($age_range).'"';
		if($location != "") {
			$address = $location;
			$venue_name = $location;
			$venue_link = '';

			if(is_numeric($location)) {
				$address = get_post_meta($location, 'pec_venue_address', true);
				$venue_name = get_the_title($location);
				$venue_link = get_post_meta($location, 'pec_venue_link', true);
			}
		echo ',
		  "location" : {
		    "@type" : "Place",
		    "address" : "'.addSlashes($address).'",
		    "sameAs" : "'.addSlashes($venue_link).'",
		    "name" : "'.addSlashes($venue_name).'"
		  }';
		}
		echo '
		}
		</script>';
	}

	/**
	 * Check if Multi Site to Install on each child.
	 * 
	 * @return void
	 */
	function checkMU_install( $network_wide ) 
	{

		global $wpdb;

		if ( $network_wide ) 
		{

			$blog_list = get_blog_list( 0, 'all' );

			foreach ($blog_list as $blog) 
			{
			
				switch_to_blog($blog['blog_id']);
				$this->install();
			
			}

			switch_to_blog($wpdb->blogid);

		} else {

			$this->install();

		}

	}

	/**
	 * Install DB tables and settings
	 * 
	 * @return void
	 */
	function install() 
	{

		global $wpdb;

		$table_name_booking = DP_PRO_EVENT_CALENDAR_TABLE_BOOKING;
		$table_name_calendars = DP_PRO_EVENT_CALENDAR_TABLE_CALENDARS;
		$table_name_special_dates = DP_PRO_EVENT_CALENDAR_TABLE_SPECIAL_DATES;
		$table_name_special_dates_calendar = DP_PRO_EVENT_CALENDAR_TABLE_SPECIAL_DATES_CALENDAR;
		$table_name_subscribers_calendar = DP_PRO_EVENT_CALENDAR_TABLE_SUBSCRIBERS_CALENDAR;
		
		if($wpdb->get_var("show tables like '$table_name_booking'") != $table_name_booking) {
			$sql = "CREATE TABLE $table_name_booking (
						id int(11) NOT NULL AUTO_INCREMENT,
						id_calendar int(11) NOT NULL,
						id_event int(11) NOT NULL,
						id_coupon int(11) NULL,
						code varchar(20) NULL,
						session_id varchar(255) NULL,
						coupon_discount int(11) NULL,
						booking_date datetime NOT NULL,
						cancel_date datetime NULL,
						event_date date NOT NULL,
						id_user int(11) NOT NULL,
						quantity int(11) NOT NULL DEFAULT 1,
						comment text NULL,
						cancel_reason text NULL,
						status varchar(255) NOT NULL,
						name varchar(255) NOT NULL,
						email varchar(255) NOT NULL,
						phone varchar(80) NULL,
						extra_fields TEXT NULL DEFAULT '',
						UNIQUE KEY id(id)
					) DEFAULT CHARSET utf8 COLLATE utf8_general_ci;";
			$rs = $wpdb->query($sql);
		}
		
		if($wpdb->get_var("show tables like '$table_name_calendars'") != $table_name_calendars) {
			$sql = "CREATE TABLE $table_name_calendars (
						id int(11) NOT NULL AUTO_INCREMENT,
						active tinyint(1) NOT NULL,
						title varchar(80) NOT NULL,
						description varchar(255) NOT NULL,
						admin_email varchar(255) NULL,
						width char(5) NOT NULL,
						width_unity char(2) NOT NULL DEFAULT 'px',
						default_date date NULL,
						date_range_start date NULL,
						date_range_end date NULL,
						ical_active tinyint(1) NOT NULL,
						ical_limit varchar(80) NOT NULL,
						rss_active tinyint(1) NOT NULL,
						rss_limit varchar(80) NOT NULL,
						link_post tinyint(1) NOT NULL,
						link_post_target varchar(80) NULL DEFAULT '_self',
						booking_display_attendees tinyint(1) NOT NULL DEFAULT 0,
						booking_display_attendees_names tinyint(1) NOT NULL DEFAULT 0,
						booking_display_fully_booked tinyint(1) NOT NULL DEFAULT 0,
						email_admin_new_event tinyint(1) NOT NULL,
						hide_old_dates TINYINT(1) NOT NULL DEFAULT 0,
						limit_time_start TINYINT(2) NOT NULL DEFAULT 0,
						limit_time_end TINYINT(2) NOT NULL DEFAULT 0,
						view VARCHAR(80) NOT NULL DEFAULT 'monthly',
						format_ampm TINYINT(1) NOT NULL DEFAULT 0,
						show_time TINYINT(1) NOT NULL DEFAULT 1,
						show_timezone TINYINT(1) NOT NULL DEFAULT 0,
						show_preview TINYINT(1) NOT NULL DEFAULT 0,
						show_titles_monthly TINYINT(1) NOT NULL DEFAULT 0,
						show_references TINYINT(1) NOT NULL DEFAULT 1,
						show_author TINYINT(1) NOT NULL DEFAULT 0,
						show_search TINYINT(1) NOT NULL DEFAULT 0,
						show_category_filter TINYINT(1) NOT NULL DEFAULT 0,
						show_location_filter TINYINT(1) NOT NULL DEFAULT 0,
						booking_enable TINYINT(1) NOT NULL DEFAULT 0,
						booking_non_logged TINYINT(1) NOT NULL DEFAULT 0,
						booking_cancel TINYINT(1) NOT NULL DEFAULT 0,
						booking_email_template_user TEXT NOT NULL,
						booking_email_template_admin TEXT NOT NULL,
						booking_email_template_reminder_user TEXT NOT NULL,
						booking_cancel_email_enable TINYINT(1) NOT NULL DEFAULT 0,
						booking_cancel_email_template TEXT NOT NULL,
						new_event_email_template_published TEXT NOT NULL,
						booking_comment TINYINT(1) NULL DEFAULT 0,
						booking_event_color VARCHAR(80) NOT NULL DEFAULT '#e14d43',
						category_filter_include text NULL,
						venue_filter_include text NULL,
						allow_user_add_event_roles text NULL,
						booking_custom_fields text NULL,
						form_custom_fields text NULL,
						article_share TINYINT(1) NOT NULL DEFAULT 0,
						cache_active TINYINT(1) NOT NULL DEFAULT 0,
						allow_user_add_event TINYINT(1) NOT NULL DEFAULT 0,
						publish_new_event TINYINT(1) NOT NULL DEFAULT 0,
						new_event_email_enable TINYINT(1) NOT NULL DEFAULT 1,
						form_customization text NULL,
						form_text_editor TINYINT(1) NOT NULL DEFAULT 1,
						show_x TINYINT(1) NOT NULL DEFAULT 1,
						allow_user_edit_event TINYINT(1) NOT NULL DEFAULT 0,
						allow_user_remove_event TINYINT(1) NOT NULL DEFAULT 0,
						show_view_buttons TINYINT(1) NOT NULL DEFAULT 1,
						assign_events_admin INT(11) NOT NULL DEFAULT 0,
						first_day tinyint(1) NOT NULL DEFAULT '0',
						current_date_color VARCHAR(10) NOT NULL DEFAULT '#C4C5D1',
						subscribe_active tinyint(1) NOT NULL DEFAULT 0,
						mailchimp_api varchar(80) NULL,
						mailchimp_list varchar(80) NULL,
						translation_fields TEXT NULL DEFAULT '',
						skin varchar(80) NOT NULL,
						enable_wpml TINYINT(1) NOT NULL DEFAULT 0,
						sync_ical_enable TINYINT(1) NOT NULL DEFAULT 0,
						sync_ical_url TEXT NOT NULL DEFAULT '',
						sync_ical_frequency VARCHAR(80) NOT NULL DEFAULT '',
						sync_ical_category INT(11) NOT NULL DEFAULT 0,
						sync_fb_page TEXT NOT NULL DEFAULT '',
						daily_weekly_layout VARCHAR(80) NOT NULL DEFAULT 'list',
						booking_max_quantity INT(11) NOT NULL DEFAULT 3,
						booking_max_upcoming_dates INT(11) NOT NULL DEFAULT 10,
						booking_show_phone TINYINT(1) NOT NULL DEFAULT 0,
						booking_show_remaining TINYINT(1) NOT NULL DEFAULT 1,
						UNIQUE KEY id(id)
					) DEFAULT CHARSET utf8 COLLATE utf8_general_ci;";
			$rs = $wpdb->query($sql);
		}
		
		if($wpdb->get_var("show tables like '$table_name_special_dates'") != $table_name_special_dates) {
			$sql = "CREATE TABLE $table_name_special_dates (
						id int(11) NOT NULL AUTO_INCREMENT,
						title varchar(80) NOT NULL,
						color varchar(10) NOT NULL,
						UNIQUE KEY id(id)
					) DEFAULT CHARSET utf8 COLLATE utf8_general_ci;";
			$rs = $wpdb->query($sql);
		}
		
		if($wpdb->get_var("show tables like '$table_name_special_dates_calendar'") != $table_name_special_dates_calendar) {
			$sql = "CREATE TABLE $table_name_special_dates_calendar (
						special_date int(11) NOT NULL,
						calendar int(11) NOT NULL,
						date date NOT NULL,
						PRIMARY KEY (special_date,calendar,date)
					) DEFAULT CHARSET utf8 COLLATE utf8_general_ci;";
			$rs = $wpdb->query($sql);
		}
		
		if($wpdb->get_var("show tables like '$table_name_subscribers_calendar'") != $table_name_subscribers_calendar) {
			$sql = "CREATE TABLE $table_name_subscribers_calendar (
						id int(11) NOT NULL AUTO_INCREMENT,
						calendar int(11) NOT NULL,
						name varchar(80) NOT NULL,
						email varchar(80) NOT NULL,
						subscription_date datetime NOT NULL,
						UNIQUE KEY id(id)
					) DEFAULT CHARSET utf8 COLLATE utf8_general_ci;";
			$rs = $wpdb->query($sql);
		}

	   $default_events = array();
	   $default_events = array(

	   						   'version' 				=> 		DP_PRO_EVENT_CALENDAR_VER,
	   						   'disable_rewrite_rules'  => 		0,
							   'user_roles'				=>		array(),						   
							   'article_share'			=>		true,
							   'category_filter_include'=>		true,
							   'assign_events_admin'	=>		true,
							   'all_working_days'		=>		true,
							   'hide_old_dates'			=>		true,
							   'limit_time_start'		=>		true,
							   'form_show_fields'		=>		true,
							   'allow_users_edit_event'	=>		true,
							   'show_author'			=>		true,
							   'remove_events'			=>		true,
							   'cache_active'			=>		true,
							   'booking'				=>		true,
							   'booking_lang'			=>		true,
							   'enable_wpml'			=>		true,
							   'booking_status'			=>		true,
							   'booking_non_logged'		=>		true,
							   'booking_non_logged_options' => 	true,
							   'weekly_view'			=>		true,
							   'tickets_remaining'		=>		true,
							   'sync_ical'				=>		true,
							   'updatebookingtable'		=>      true,
							   'show_titles_monthly'	=>		true,
							   'daily_weekly_layout'	=> 		true,
							   'allow_user_add_event_roles' =>  true,
							   'sync_ical_url_text'		=>		true,
							   'display_attendees'		=> 		true,
							   'new_event_template_published' => true,
							   'booking_quantity'		=>		true,
							   'booking_max_quantity' 	=> 		true,
							   'form_text_editor'		=>		true,
							   'form_bookings'			=>		true,
							   'booking_max_upcoming_dates' => 	true,
							   'booking_phone'			=> 		true,
							   'form_show_color'		=> 		true,
							   'booking_email_template_reminder_user'	=> true,
							   'booking_extra_fields'	=> 		true,
							   'sync_fb_page'			=>		true,
							   'translation_fields'		=>		true,
							   'sync_ical_category'		=>		true,
							   'show_timezone_update'	=> 		true,
							   'form_show_end_time'		=> 		true,
							   'display_attendees_names' => 	true,
							   'show_location_filter'	=>		true,
							   'form_show_location_options'	=>	true,
							   'link_post_target'			=> 	true,
							   'booking_cancel'			=>		true,
							   'booking_cancel_date'	=> 		true,
							   'new_event_email_enable' => 		true,
							   'booking_cancel_reason' 	=> 		true,
							   'booking_cancel_email_enable' => true,
							   'booking_coupon'			=> 		true,
							   'booking_remaining'		=> 		true,
							   'form_show_timezone'		=> 		true,
							   'form_show_extra_dates'	=> 		true,
							   'booking_custom_fields_calendar'	=> true,
							   'venue_filter_include'	=> 		true,
							   'display_fully_booked'	=> 		true,
							   'form_show_booking_block_hours' => true,
							   'update_sync_ical_type_'	=> 		true,
							   'admin_email'			=> 		true,
							   'booking_code'			=> 		true,
							   'form_customization'		=> 		true
				              );
	   
		$dpProEventCalendar = get_option('dpProEventCalendar_options');
		
		if( ! $dpProEventCalendar ) 
			$dpProEventCalendar = array();
		
		foreach( $default_events as $key => $value ) 
		{
		
		  if( ! isset( $dpProEventCalendar[$key] ) ) 
			 $dpProEventCalendar[$key] = $value;
		
		}
		
		delete_option('dpProEventCalendar_options');	  
		update_option('dpProEventCalendar_options',$dpProEventCalendar);

	}

	/**
	 * Check if Multi Site to Uninstall on each child.
	 * 
	 * @return void
	 */
	function checkMU_uninstall($network_wide) {
		global $wpdb;
		if ( $network_wide ) {
			$blog_list = get_blog_list( 0, 'all' );
			foreach ($blog_list as $blog) {
				switch_to_blog($blog['blog_id']);
				DpProEventCalendar_Init::uninstall();
			}
			switch_to_blog($wpdb->blogid);
		} else {
			DpProEventCalendar_Init::uninstall();
		}
	}

	/**
	 * Uninstall the plugin removing DB and settings
	 * 
	 * @return void
	 */
	static function uninstall() 
	{

		global $wpdb;
		delete_option('dpProEventCalendar_options'); 
		
		$sql = "DROP TABLE " . DP_PRO_EVENT_CALENDAR_TABLE_CALENDARS;
		$wpdb->query($sql);
		
		$sql = "DROP TABLE " . DP_PRO_EVENT_CALENDAR_TABLE_BOOKING;
		$wpdb->query($sql);
		
		$sql = "DROP TABLE " . DP_PRO_EVENT_CALENDAR_TABLE_SPECIAL_DATES;
		$wpdb->query($sql);
		
		$sql = "DROP TABLE " . DP_PRO_EVENT_CALENDAR_TABLE_SPECIAL_DATES_CALENDAR;
		$wpdb->query($sql);
		
		$sql = "DROP TABLE " . DP_PRO_EVENT_CALENDAR_TABLE_SUBSCRIBERS_CALENDAR;
		$wpdb->query($sql);

	}

	/**
	 * Load Textdomain, language.
	 * 
	 * @return void
	 */
	function load_textdomain () 
	{

		// Create Text Domain For Translations
		
		$domain = 'dpProEventCalendar';
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( 'dpProEventCalendar', false, dirname( DP_PRO_EVENT_CALENDAR_PLUGIN_BASENAME ) . '/languages/' );
		
	}

	/**
	 * Install plugin on new child site
	 * 
	 * @return void
	 */
	function new_blog ( $blog_id, $user_id, $domain, $path, $site_id, $meta ) 
	{

		global $wpdb;
	 
		if (is_plugin_active_for_network('dpProEventCalendar/dpProEventCalendar.php')) 
		{
		
			$old_blog = $wpdb->blogid;
			switch_to_blog($blog_id);
			$this->install();
			switch_to_blog($old_blog);
		
		}

	}

	/**
	 * Init all the post types
	 * 
	 * @return void
	 */
	function init_post_types () 
	{

		global $dpProEventCalendar;


		if( !isset( $dpProEventCalendar['events_slug'] ) ) 
			$dpProEventCalendar['events_slug'] = '';

		$events_slug = ( $dpProEventCalendar['events_slug'] != "" ? $dpProEventCalendar['events_slug'] : _x('pec-events', 'events slug', 'dpProEventCalendar'));


		add_rewrite_tag( '%event_date%','([^&]+)' );

		add_rewrite_rule(
		    '^'.$events_slug.'/([^/]*)/([^/]*)/?',
		    'index.php?pec-events=$matches[1]&event_date=$matches[2]',
		    'top' );

		/*  add_rewrite_rule(
		    '^'.$events_slug.'/([^/]*)/?',
		    'index.php?pec-events=$matches[1]',
		    'top' );*/

		$labels = array(
			'name' => __('Pro Event Calendar', 'dpProEventCalendar'),
			'singular_name' => __('Events', 'dpProEventCalendar'),
			'add_new' => __('Add New', 'dpProEventCalendar'),
			'add_new_item' => __('Add New Event', 'dpProEventCalendar'),
			'edit_item' => __('Edit Event', 'dpProEventCalendar'),
			'new_item' => __('New Event', 'dpProEventCalendar'),
			'all_items' => __('All Events', 'dpProEventCalendar'),
			'view_item' => __('View Event', 'dpProEventCalendar'),
			'search_items' => __('Search Events', 'dpProEventCalendar'),
			'not_found' =>  __('No Events Found', 'dpProEventCalendar'),
			'not_found_in_trash' => __('No Events Found in Trash', 'dpProEventCalendar'), 
			'parent_item_colon' => '',
			'menu_name' => __('Events', 'dpProEventCalendar')
		);

		$args = array(
			'labels' => $labels,
			'public' => true,
			'publicly_queryable' => true,
			'exclude_from_search' => false,
			'show_ui' => true, 
			'show_in_menu' => true, 
			'query_var' => true,
			'rewrite' => array( 'slug' => $events_slug, 'with_front' => false),
			'capability_type' => 'post',
			'has_archive' => true, 
			'hierarchical' => false,
			'show_in_menu' => 'dpProEventCalendar-admin',
			'menu_position' => null,
			'show_in_rest'       => true,
			'menu_icon' => 'dashicons-calendar-alt',
			'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'comments', 'publicize', 'custom-fields' ),
			'taxonomies' => array('pec_events_category', 'post_tag')
		); 

		register_post_type( DP_PRO_EVENT_CALENDAR_EVENT_POST_TYPE, $args );

		$labels = array(
			'name' => __('Venues', 'dpProEventCalendar'),
			'singular_name' => __('Venue', 'dpProEventCalendar'),
			'add_new' => __('Add New', 'dpProEventCalendar'),
			'add_new_item' => __('Add New Venue', 'dpProEventCalendar'),
			'edit_item' => __('Edit Venue', 'dpProEventCalendar'),
			'new_item' => __('New Venue', 'dpProEventCalendar'),
			'all_items' => __('Venues', 'dpProEventCalendar'),
			'view_item' => __('View Venue', 'dpProEventCalendar'),
			'search_items' => __('Search Venues', 'dpProEventCalendar'),
			'not_found' =>  __('No Venues Found', 'dpProEventCalendar'),
			'not_found_in_trash' => __('No Venues Found in Trash', 'dpProEventCalendar'), 
			'parent_item_colon' => '',
			'menu_name' => __('Venues', 'dpProEventCalendar')
		);

		$args = array(
			'labels' => $labels,
			'public' => false,
			'publicly_queryable' => true,
			'exclude_from_search' => true,
			'show_ui' => true, 
			'show_in_menu' => true, 
			'query_var' => true,
			'rewrite' => array( 'slug' => _x('pec-venues', 'venues slug', 'dpProEventCalendar'), 'with_front' => false ),
			'capability_type' => 'post',
			'has_archive' => false, 
			'hierarchical' => false,
			'show_in_menu' => 'dpProEventCalendar-admin',
			'menu_position' => null,
			'show_in_rest'       => true,
			'menu_icon' => 'dashicons-calendar-alt',
			'supports' => array( 'title', 'thumbnail' ),
			'taxonomies' => array()
		); 

		register_post_type( DP_PRO_EVENT_CALENDAR_VENUES_POST_TYPE, $args );

		$labels = array(
			'name' => __('Organizers', 'dpProEventCalendar'),
			'singular_name' => __('Organizer', 'dpProEventCalendar'),
			'add_new' => __('Add New', 'dpProEventCalendar'),
			'add_new_item' => __('Add New Organizer', 'dpProEventCalendar'),
			'edit_item' => __('Edit Organizer', 'dpProEventCalendar'),
			'new_item' => __('New Organizer', 'dpProEventCalendar'),
			'all_items' => __('Organizers', 'dpProEventCalendar'),
			'view_item' => __('View Organizer', 'dpProEventCalendar'),
			'search_items' => __('Search Organizers', 'dpProEventCalendar'),
			'not_found' =>  __('No Organizer Found', 'dpProEventCalendar'),
			'not_found_in_trash' => __('No Organizers Found in Trash', 'dpProEventCalendar'), 
			'parent_item_colon' => '',
			'menu_name' => __('Organizers', 'dpProEventCalendar')
		);

		$args = array(
			'labels' => $labels,
			'public' => false,
			'publicly_queryable' => true,
			'exclude_from_search' => true,
			'show_ui' => true, 
			'show_in_menu' => true, 
			'query_var' => true,
			'rewrite' => array( 'slug' => _x( 'pec-organizers', 'organizers slug', 'dpProEventCalendar'), 'with_front' => false ),
			'capability_type' => 'post',
			'has_archive' => false, 
			'hierarchical' => false,
			'show_in_menu' => 'dpProEventCalendar-admin',
			'menu_position' => null,
			'show_in_rest'       => true,
			'menu_icon' => 'dashicons-calendar-alt',
			'supports' => array( 'title', 'thumbnail' ),
			'taxonomies' => array()
		); 

		register_post_type( DP_PRO_EVENT_CALENDAR_ORGANIZERS_POST_TYPE, $args );


		$labels = array(
			'name' => __('Speakers', 'dpProEventCalendar'),
			'singular_name' => __('Speaker', 'dpProEventCalendar'),
			'add_new' => __('Add New', 'dpProEventCalendar'),
			'add_new_item' => __('Add New Speaker', 'dpProEventCalendar'),
			'edit_item' => __('Edit Speaker', 'dpProEventCalendar'),
			'new_item' => __('New Speaker', 'dpProEventCalendar'),
			'all_items' => __('Speakers', 'dpProEventCalendar'),
			'view_item' => __('View Speaker', 'dpProEventCalendar'),
			'search_items' => __('Search Speakers', 'dpProEventCalendar'),
			'not_found' =>  __('No Speaker Found', 'dpProEventCalendar'),
			'not_found_in_trash' => __('No Speakers Found in Trash', 'dpProEventCalendar'), 
			'parent_item_colon' => '',
			'menu_name' => __('Speakers', 'dpProEventCalendar')
		);

		$args = array(
			'labels' => $labels,
			'public' => false,
			'publicly_queryable' => true,
			'exclude_from_search' => true,
			'show_ui' => true, 
			'show_in_menu' => true, 
			'query_var' => true,
			'rewrite' => array( 'slug' => _x('pec-speakers', 'speakers slug', 'dpProEventCalendar'), 'with_front' => false ),
			'capability_type' => 'post',
			'has_archive' => false, 
			'hierarchical' => false,
			'show_in_menu' => 'dpProEventCalendar-admin',
			'menu_position' => null,
			'show_in_rest'       => true,
			'menu_icon' => 'dashicons-calendar-alt',
			'supports' => array( 'title', 'thumbnail', 'editor' ),
			'taxonomies' => array()
		); 

		register_post_type( DP_PRO_EVENT_CALENDAR_SPEAKERS_POST_TYPE, $args );
		//flush_rewrite_rules();
	  
	}


	/**
	 * Register all taxonomies
	 * 
	 * @return void
	 */
	function register_taxonomies() 
	{

		global $dpProEventCalendar;
	  
		if( !isset($dpProEventCalendar['categories_slug'] ) ) 
			$dpProEventCalendar['categories_slug'] = '';
		

		// Add new taxonomy, make it hierarchical (like categories)
		$labels = array(
			'name'                => _x( 'Event Categories', 'taxonomy general name' ),
			'singular_name'       => _x( 'Category', 'taxonomy singular name' ),
			'search_items'        => __( 'Search Categories' ),
			'all_items'           => __( 'All Categories' ),
			'parent_item'         => __( 'Parent Category' ),
			'parent_item_colon'   => __( 'Parent Category:' ),
			'edit_item'           => __( 'Edit Category' ), 
			'update_item'         => __( 'Update Category' ),
			'add_new_item'        => __( 'Add New Category' ),
			'new_item_name'       => __( 'New Category Name' ),
			'menu_name'           => __( 'Category' )
		); 	

		$args = array(
			'hierarchical'        => true,
			'labels'              => $labels,
			'show_ui'             => true,
			'show_admin_column'   => true,
			'query_var'           => true,
			'show_in_rest'        => true,
			'rewrite'             => array( 'with_front' => false, 'slug' => ( $dpProEventCalendar['categories_slug'] != "" ? $dpProEventCalendar['categories_slug'] : _x('pec_events_category', 'event category slug', 'dpProEventCalendar')) )
		);

		register_taxonomy( 'pec_events_category', array( DP_PRO_EVENT_CALENDAR_EVENT_POST_TYPE ), $args );

	}

	// Setup Ical Sync

	function setup_ical_sync() 
	{

		global $wpdb;

		$querystr = "SELECT id as calendar_id, sync_ical_url, sync_fb_page, sync_ical_frequency, sync_ical_enable, sync_ical_category
			FROM " . DP_PRO_EVENT_CALENDAR_TABLE_CALENDARS;

		$calendars_obj = $wpdb->get_results( $querystr, OBJECT );

		foreach( $calendars_obj as $key ) 
		{
			
			if( $key->sync_ical_enable && ( $key->sync_ical_url != "" || $key->sync_fb_page != "" ) ) 
			{

				//Schedule

				if ( ! wp_next_scheduled( 'pecsyncical'.$key->calendar_id, array($key->calendar_id) ) ) 
				{
				
					$scheduled = wp_schedule_event( time(), $key->sync_ical_frequency, 'pecsyncical'.$key->calendar_id, array($key->calendar_id));
					//die($scheduled.'<br>'.$key->calendar_id);
				
				}
				
				add_action( 'pecsyncical'.$key->calendar_id, 'dpProEventCalendar_ical_sync', 10 ,1 );
						/*if($_GET['pec_debug']) {
							echo $key->calendar_id . ' - '.$key->sync_fb_page.'<br>';
							dpProEventCalendar_ical_sync($key->calendar_id);
						}
							/*
							dpProEventCalendar_ical_sync($key->calendar_id);
						}*/
			} else {

				// Unschedule
				wp_clear_scheduled_hook( 'pecsyncical'.$key->calendar_id, array($key->calendar_id) );

			}

		}
	}

	/**
	 * Content filter for all event single pages
	 * 
	 * @return void
	 */
	function content_filter( $content ) 
	{

		global $dpProEventCalendar, $wp_query, $pec_init;

		if( is_array( $GLOBALS ) && null !== $GLOBALS['post'] ) 
		{
			
			if ( $GLOBALS['post']->post_type == DP_PRO_EVENT_CALENDAR_EVENT_POST_TYPE && $wp_query->is_single && !post_password_required( $GLOBALS['post']->ID ) ) 
			{

				$rtl = get_post_meta( $GLOBALS['post']->ID, 'pec_rtl', true );

				dpProEventCalendar_enqueue_scripts( $rtl );
				
				$calendar_id = explode( ",", get_post_meta( $GLOBALS['post']->ID, 'pec_id_calendar', true ) ); 
				
				$calendar_id = $calendar_id[0];	

				// Get Single page JS script
				$opts = array();
				$opts['id_calendar'] = $calendar_id;
				$dpProEventCalendar_class = $pec_init->init_base( $opts );
				
				$content = $dpProEventCalendar_class->get_single_page( $content );
							
				if( pec_setting( 'custom_css' ) != "" ) 
					
					$content .= '<style type="text/css">'.$dpProEventCalendar['custom_css'].'</style>';

			}

		}

		// otherwise returns the database content

		return $content;

	}

	/**
	 * Setup Reminders for bookings
	 * 
	 * @return void
	 */
	function setup_booking_reminder() 
	{
	
		global $dpProEventCalendar;

		if( !isset( $dpProEventCalendar['disable_reminders'] ) || !$dpProEventCalendar['disable_reminders'] ) 
		{
		
			if ( ! wp_next_scheduled( 'pecbookingreminder' ) ) 

				$scheduled = wp_schedule_event( time(), 'daily', 'pecbookingreminder' );
			
			add_action( 'pecbookingreminder', 'dpProEventCalendar_booking_reminder', 10 );

		} else

			wp_clear_scheduled_hook( 'pecbookingreminder' );

	}

	/**
	 * Setup cron for expired events
	 * 
	 * @return void
	 */
	function setup_expired_events() 
	{

		global $dpProEventCalendar;

		if( isset( $dpProEventCalendar['remove_expired_enable'] ) && $dpProEventCalendar['remove_expired_enable'] ) 
		{
			
			if ( ! wp_next_scheduled( 'pecexpiredevents' ) ) 
			
				$scheduled = wp_schedule_event( time(), 'daily', 'pecexpiredevents');
			
			add_action( 'pecexpiredevents', 'dpProEventCalendar_removeExpiredEvents', 10 );

		} else {

			// Unschedule
			wp_clear_scheduled_hook( 'pecexpiredevents' );

		}

	}

	/**
	 * Register Rest API data
	 * 
	 * @return void
	 */
	function register_rest() 
	{

		if(function_exists('register_rest_field')) 
		{
			
			register_rest_field( DP_PRO_EVENT_CALENDAR_VENUES_POST_TYPE,
		        'pec_venue_map_lnlat',
		        array(
		            'get_callback'    => 'dpProevEntCalendar_get_field_rest',
		            'update_callback' => null,
		            'schema'          => null,
		        )
		    );

		    register_rest_field( DP_PRO_EVENT_CALENDAR_EVENT_POST_TYPE,
		        'pec_date',
		        array(
		            'get_callback'    => 'dpProevEntCalendar_get_field_rest',
		            'update_callback' => null,
		            'schema'          => null,
		        )
		    );

		    register_rest_field( DP_PRO_EVENT_CALENDAR_EVENT_POST_TYPE,
		        'pec_venue_map_lnlat',
		        array(
		            'get_callback'    => 'dpProevEntCalendar_get_field_rest',
		            'update_callback' => null,
		            'schema'          => null,
		        )
		    );

		    register_rest_field( DP_PRO_EVENT_CALENDAR_EVENT_POST_TYPE,
		        'pec_featured_image',
		        array(
		            'get_callback'    => 'dpProevEntCalendar_get_image_rest',
		            'update_callback' => null,
		            'schema'          => null,
		        )
		    );

		    register_rest_field( DP_PRO_EVENT_CALENDAR_EVENT_POST_TYPE,
		        'pec_id_calendar',
		        array(
		            'get_callback'    => 'dpProevEntCalendar_get_field_rest',
		            'update_callback' => null,
		            'schema'          => null,
		        )
		    );

		    register_rest_field( DP_PRO_EVENT_CALENDAR_EVENT_POST_TYPE,
		        'pec_featured_event',
		        array(
		            'get_callback'    => 'dpProevEntCalendar_get_field_rest',
		            'update_callback' => null,
		            'schema'          => null,
		        )
		    );

		    register_rest_field( DP_PRO_EVENT_CALENDAR_EVENT_POST_TYPE,
		        'pec_all_day',
		        array(
		            'get_callback'    => 'dpProevEntCalendar_get_field_rest',
		            'update_callback' => null,
		            'schema'          => null,
		        )
		    );

		    register_rest_field( DP_PRO_EVENT_CALENDAR_EVENT_POST_TYPE,
		        'pec_tbc',
		        array(
		            'get_callback'    => 'dpProevEntCalendar_get_field_rest',
		            'update_callback' => null,
		            'schema'          => null,
		        )
		    );

		    register_rest_field( DP_PRO_EVENT_CALENDAR_EVENT_POST_TYPE,
		        'pec_daily_working_days',
		        array(
		            'get_callback'    => 'dpProevEntCalendar_get_field_rest',
		            'update_callback' => null,
		            'schema'          => null,
		        )
		    );

		    register_rest_field( DP_PRO_EVENT_CALENDAR_EVENT_POST_TYPE,
		        'pec_daily_every',
		        array(
		            'get_callback'    => 'dpProevEntCalendar_get_field_rest',
		            'update_callback' => null,
		            'schema'          => null,
		        )
		    );

		    register_rest_field( DP_PRO_EVENT_CALENDAR_EVENT_POST_TYPE,
		        'pec_weekly_every',
		        array(
		            'get_callback'    => 'dpProevEntCalendar_get_field_rest',
		            'update_callback' => null,
		            'schema'          => null,
		        )
		    );

		    register_rest_field( DP_PRO_EVENT_CALENDAR_EVENT_POST_TYPE,
		        'pec_weekly_day',
		        array(
		            'get_callback'    => 'dpProevEntCalendar_get_field_rest',
		            'update_callback' => null,
		            'schema'          => null,
		        )
		    );

		    register_rest_field( DP_PRO_EVENT_CALENDAR_EVENT_POST_TYPE,
		        'pec_monthly_every',
		        array(
		            'get_callback'    => 'dpProevEntCalendar_get_field_rest',
		            'update_callback' => null,
		            'schema'          => null,
		        )
		    );

		    register_rest_field( DP_PRO_EVENT_CALENDAR_EVENT_POST_TYPE,
		        'pec_monthly_position',
		        array(
		            'get_callback'    => 'dpProevEntCalendar_get_field_rest',
		            'update_callback' => null,
		            'schema'          => null,
		        )
		    );

		    register_rest_field( DP_PRO_EVENT_CALENDAR_EVENT_POST_TYPE,
		        'pec_monthly_day',
		        array(
		            'get_callback'    => 'dpProevEntCalendar_get_field_rest',
		            'update_callback' => null,
		            'schema'          => null,
		        )
		    );

		    register_rest_field( DP_PRO_EVENT_CALENDAR_EVENT_POST_TYPE,
		        'pec_exceptions',
		        array(
		            'get_callback'    => 'dpProevEntCalendar_get_field_rest',
		            'update_callback' => null,
		            'schema'          => null,
		        )
		    );

		    register_rest_field( DP_PRO_EVENT_CALENDAR_EVENT_POST_TYPE,
		        'pec_extra_dates',
		        array(
		            'get_callback'    => 'dpProevEntCalendar_get_field_rest',
		            'update_callback' => null,
		            'schema'          => null,
		        )
		    );

		    register_rest_field( DP_PRO_EVENT_CALENDAR_EVENT_POST_TYPE,
		        'pec_end_date',
		        array(
		            'get_callback'    => 'dpProevEntCalendar_get_field_rest',
		            'update_callback' => null,
		            'schema'          => null,
		        )
		    );

		    register_rest_field( DP_PRO_EVENT_CALENDAR_EVENT_POST_TYPE,
		        'pec_link',
		        array(
		            'get_callback'    => 'dpProevEntCalendar_get_field_rest',
		            'update_callback' => null,
		            'schema'          => null,
		        )
		    );

		    register_rest_field( DP_PRO_EVENT_CALENDAR_EVENT_POST_TYPE,
		        'pec_age_range',
		        array(
		            'get_callback'    => 'dpProevEntCalendar_get_field_rest',
		            'update_callback' => null,
		            'schema'          => null,
		        )
		    );

		    register_rest_field( DP_PRO_EVENT_CALENDAR_EVENT_POST_TYPE,
		        'pec_map',
		        array(
		            'get_callback'    => 'dpProevEntCalendar_get_field_rest',
		            'update_callback' => null,
		            'schema'          => null,
		        )
		    );

		    register_rest_field( DP_PRO_EVENT_CALENDAR_EVENT_POST_TYPE,
		        'pec_end_time_hh',
		        array(
		            'get_callback'    => 'dpProevEntCalendar_get_field_rest',
		            'update_callback' => null,
		            'schema'          => null,
		        )
		    );

		    register_rest_field( DP_PRO_EVENT_CALENDAR_EVENT_POST_TYPE,
		        'pec_end_time_mm',
		        array(
		            'get_callback'    => 'dpProevEntCalendar_get_field_rest',
		            'update_callback' => null,
		            'schema'          => null,
		        )
		    );

		    register_rest_field( DP_PRO_EVENT_CALENDAR_EVENT_POST_TYPE,
		        'pec_hide_time',
		        array(
		            'get_callback'    => 'dpProevEntCalendar_get_field_rest',
		            'update_callback' => null,
		            'schema'          => null,
		        )
		    );

		    register_rest_field( DP_PRO_EVENT_CALENDAR_EVENT_POST_TYPE,
		        'pec_organizer',
		        array(
		            'get_callback'    => 'dpProevEntCalendar_get_field_rest',
		            'update_callback' => null,
		            'schema'          => null,
		        )
		    );

		    register_rest_field( DP_PRO_EVENT_CALENDAR_EVENT_POST_TYPE,
		        'pec_phone',
		        array(
		            'get_callback'    => 'dpProevEntCalendar_get_field_rest',
		            'update_callback' => null,
		            'schema'          => null,
		        )
		    );

		    register_rest_field( DP_PRO_EVENT_CALENDAR_EVENT_POST_TYPE,
		        'pec_recurring_frecuency',
		        array(
		            'get_callback'    => 'dpProevEntCalendar_get_field_rest',
		            'update_callback' => null,
		            'schema'          => null,
		        )
		    );

		    register_rest_field( DP_PRO_EVENT_CALENDAR_EVENT_POST_TYPE,
		        'pec_location',
		        array(
		            'get_callback'    => 'dpProevEntCalendar_get_field_rest',
		            'update_callback' => null,
		            'schema'          => null,
		        )
		    );
		}
	}

	/**
	 * Include main css file
	 * 
	 * @return void
	 */
	function enqueue_styles() 
	{	

	  	$plugin_css = 'dpProEventCalendar.min.css';

		if( DP_PRO_EVENT_CALENDAR_DEBUG )
			$plugin_css = 'dpProEventCalendar.css';
	  
		wp_enqueue_style( 'dpProEventCalendar_headcss', dpProEventCalendar_plugin_url( 'css/' . $plugin_css ),
			false, DP_PRO_EVENT_CALENDAR_VER );

	  
	}

	/**
	 * Add Featured Image Support
	 * 
	 * @return void
	 */
	function add_featured_image_support()
	{

	    $supportedTypes = get_theme_support( 'post-thumbnails' );

	    if( $supportedTypes === false )
	        add_theme_support( 'post-thumbnails', array( DP_PRO_EVENT_CALENDAR_EVENT_POST_TYPE ) );               
	    elseif( is_array( $supportedTypes ) )
	    {
	        $supportedTypes[0][] = DP_PRO_EVENT_CALENDAR_EVENT_POST_TYPE;
	        add_theme_support( 'post-thumbnails', $supportedTypes[0] );
	    }

	}

	/**
	 * RSS Feed
	 * 
	 * @return void
	 */
	function rss_feed() 
	{
		
		// Init RSS class
		new DPPEC_RSS();

	}

	/**
	 * iCal Feed
	 * 
	 * @return void
	 */
	function ical_feed( $type ) 
	{
		
		// Init iCal class
		new DPPEC_iCal( $type );

	}

	/**
	 * Check if vars are set and return default value if not
	 * 
	 * @return void
	 */
    function get( $var, $default = '' )
    {

    	if( isset( $_GET[$var] ) )
			return $_GET[$var];

		return $default;

    }

    function post( $var, $default = '' )
    {

    	if( isset( $_POST[$var] ) )
			return $_POST[$var];

		return $default;

    }
	
}
?>