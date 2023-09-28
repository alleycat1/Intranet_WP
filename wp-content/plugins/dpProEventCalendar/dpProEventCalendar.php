<?php
/**
 * Plugin Name:       DP Pro Event Calendar
 * Description:       The Pro Event Calendar plugin adds a professional and sleek calendar to your posts or pages. 100% Responsive, also you can use it inside a widget.
 * Version:           3.2.6
 * Author:            DPereyra
 * Author URI:        https://wpsleek.com
 * Text Domain:       dpProEventCalendar
 * Domain Path:       /languages
 *
 */


// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Debug mode?
define ( 'DP_PRO_EVENT_CALENDAR_DEBUG', 0 );

if ( DP_PRO_EVENT_CALENDAR_DEBUG ) 

	error_reporting( E_ALL );

else 

	@error_reporting( E_ALL & ~E_DEPRECATED & ~E_NOTICE & ~E_STRICT );


// Define Constants
global $table_prefix;

define( 'DP_PRO_EVENT_CALENDAR_VER', '3.2.6' ); // Current Version of this plugin
define( 'DP_PRO_EVENT_CALENDAR_VERSION_CHECKER', 'http://wpsleek.com/proeventcalendar.json' );

// DB
define( 'DP_PRO_EVENT_CALENDAR_TABLE_CALENDARS', $table_prefix . 'dpProEventCalendar_calendars' ); 
define( 'DP_PRO_EVENT_CALENDAR_TABLE_BOOKING', $table_prefix . 'dpProEventCalendar_booking' ); 
define( 'DP_PRO_EVENT_CALENDAR_TABLE_SPECIAL_DATES', $table_prefix .'dpProEventCalendar_special_dates' ); 
define( 'DP_PRO_EVENT_CALENDAR_TABLE_SPECIAL_DATES_CALENDAR', $table_prefix . 'dpProEventCalendar_special_dates_calendar' ); 
define( 'DP_PRO_EVENT_CALENDAR_TABLE_SUBSCRIBERS_CALENDAR', $table_prefix . 'dpProEventCalendar_subscribers_calendar' );

define( 'DP_PRO_EVENT_CALENDAR_PAYMENTS_URL', 'https://codecanyon.net/item/wordpress-pro-event-calendar-payment-extension/9492899' );
define( 'DP_PRO_EVENT_CALENDAR_FONT_AWESOME_JS', 'https://kit.fontawesome.com/f174d8b622.js' );

// Plugin Constants
define( 'DP_PRO_EVENT_CALENDAR_TITLE', __('Pro Event Calendar','dpProEventCalendar') );
define( 'DP_PRO_EVENT_CALENDAR_DOCUMENTATION', 'https://wpsleek.com/pro-event-calendar-documentation/' );

// Post Types
define( 'DP_PRO_EVENT_CALENDAR_EVENT_POST_TYPE', 'pec-events' );
define( 'DP_PRO_EVENT_CALENDAR_VENUES_POST_TYPE', 'pec-venues' );
define( 'DP_PRO_EVENT_CALENDAR_ORGANIZERS_POST_TYPE', 'pec-organizers' );
define( 'DP_PRO_EVENT_CALENDAR_SPEAKERS_POST_TYPE', 'pec-speakers' );

// Query Strings
define( 'DP_PRO_EVENT_CALENDAR_RSS', 'pec-rss' );
define( 'DP_PRO_EVENT_CALENDAR_ICAL', 'pec-ical' );
define( 'DP_PRO_EVENT_CALENDAR_ICAL_EVENT', 'pec-ical-event' );

// Booking Status
define( 'DP_PRO_EVENT_CALENDAR_BOOKING_COMPLETED', '' );
define( 'DP_PRO_EVENT_CALENDAR_BOOKING_PENDING', 'pending' );
define( 'DP_PRO_EVENT_CALENDAR_BOOKING_CANCELED_BY_USER', 'canceled_by_user' );
define( 'DP_PRO_EVENT_CALENDAR_BOOKING_CANCELED', 'canceled' );

// Default Current Date Color
define( 'DP_PRO_EVENT_CALENDAR_CURRENT_DATE_COLOR', '#575757' );

// General Constants
define( 'DP_PRO_EVENT_CALENDAR_PLUGIN_FILE', __FILE__ );
define( 'DP_PRO_EVENT_CALENDAR_PLUGIN_DIRNAME', dirname ( __FILE__ ) . '/' );
define( 'DP_PRO_EVENT_CALENDAR_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

// Directories
define( 'DP_PRO_EVENT_CALENDAR_CLASSES_DIR', DP_PRO_EVENT_CALENDAR_PLUGIN_DIRNAME . 'classes/' );
define( 'DP_PRO_EVENT_CALENDAR_SETTINGS_DIR', DP_PRO_EVENT_CALENDAR_PLUGIN_DIRNAME . 'settings/' );
define( 'DP_PRO_EVENT_CALENDAR_INCLUDES_DIR', DP_PRO_EVENT_CALENDAR_PLUGIN_DIRNAME . 'includes/' );
define( 'DP_PRO_EVENT_CALENDAR_WIDGETS_DIR', DP_PRO_EVENT_CALENDAR_INCLUDES_DIR . 'widgets/' );


// Init

require_once ( DP_PRO_EVENT_CALENDAR_CLASSES_DIR . 'init.class.php' );

$pec_init = new DpProEventCalendar_Init ( ) ;

$pec_admin = $pec_init->admin;

?>