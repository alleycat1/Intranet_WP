<?php

/*
 * DP Pro Event Calendar
 *
 * Copyright 2012, Diego Pereyra
 *
 * @Web: https://www.wpsleek.com
 * @Email: dpereyra90@gmail.com
 *
 * Update Database if needed
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $dpProEventCalendar, $wpdb;
	
if( ! isset( $dpProEventCalendar['booking_codex'] ) ) {
		$dpProEventCalendar['booking_codex'] = true;
		
		$sql = "ALTER TABLE ".DP_PRO_EVENT_CALENDAR_TABLE_BOOKING." ADD (code varchar(20) NULL);";
		$wpdb->query($sql);

		$sql = "ALTER TABLE ".DP_PRO_EVENT_CALENDAR_TABLE_BOOKING." ADD (session_id varchar(255) NULL);";
		$wpdb->query($sql);
		
		update_option('dpProEventCalendar_options',$dpProEventCalendar);
	}

if( ! isset( $dpProEventCalendar['form_customization'] ) ) 
{

	if( ! isset( $dpProEventCalendar['form_customization'] ) ) {
		$dpProEventCalendar['form_customization'] = true;
		
		$sql = "ALTER TABLE " . DP_PRO_EVENT_CALENDAR_TABLE_CALENDARS . " ADD (form_customization text NULL);";
		$wpdb->query($sql);
		
		update_option('dpProEventCalendar_options',$dpProEventCalendar);
	}

	if( ! isset( $dpProEventCalendar['booking_code'] ) ) {
		$dpProEventCalendar['booking_code'] = true;
		
		$sql = "ALTER TABLE ".DP_PRO_EVENT_CALENDAR_TABLE_BOOKING." ADD (code varchar(20) NULL);";
		$wpdb->query($sql);

		$sql = "ALTER TABLE ".DP_PRO_EVENT_CALENDAR_TABLE_BOOKING." ADD (session_id varchar(255) NULL);";
		$wpdb->query($sql);
		
		update_option('dpProEventCalendar_options',$dpProEventCalendar);
	}

	if(!isset($dpProEventCalendar['admin_email'])) {
		$dpProEventCalendar['admin_email'] = true;
		
		$sql = "ALTER TABLE ".DP_PRO_EVENT_CALENDAR_TABLE_CALENDARS." ADD (admin_email varchar(255) NULL);";
		$wpdb->query($sql);
		
		update_option('dpProEventCalendar_options',$dpProEventCalendar);
	}

	if(!isset($dpProEventCalendar['update_sync_ical_type_'])) {
		$dpProEventCalendar['update_sync_ical_type_'] = true;
		
		$sql = "ALTER TABLE ".DP_PRO_EVENT_CALENDAR_TABLE_CALENDARS." MODIFY sync_ical_category int(11) NOT NULL DEFAULT 0;";
		$wpdb->query($sql);
		
		update_option('dpProEventCalendar_options',$dpProEventCalendar);
	}

	if(!isset($dpProEventCalendar['form_show_booking_block_hours'])) {
		$dpProEventCalendar['form_show_booking_block_hours'] = true;
		
		$sql = "ALTER TABLE ".DP_PRO_EVENT_CALENDAR_TABLE_CALENDARS." ADD (form_show_booking_block_hours TINYINT(1) NOT NULL DEFAULT 0);";
		$wpdb->query($sql);
		update_option('dpProEventCalendar_options',$dpProEventCalendar);
	}

	if(!isset($dpProEventCalendar['display_fully_booked'])) {
		$dpProEventCalendar['display_fully_booked'] = true;
		
		$sql = "ALTER TABLE ".DP_PRO_EVENT_CALENDAR_TABLE_CALENDARS." ADD (booking_display_fully_booked TINYINT(1) NOT NULL DEFAULT 0);";
		$wpdb->query($sql);
		update_option('dpProEventCalendar_options',$dpProEventCalendar);
	}
	
	if(!isset($dpProEventCalendar['venue_filter_include'])) {
		$dpProEventCalendar['venue_filter_include'] = true;
		
		$sql = "ALTER TABLE ".DP_PRO_EVENT_CALENDAR_TABLE_CALENDARS." ADD (venue_filter_include text NULL);";
		$wpdb->query($sql);
		
		update_option('dpProEventCalendar_options',$dpProEventCalendar);
	}

	if(!isset($dpProEventCalendar['form_show_extra_dates'])) {
		$dpProEventCalendar['form_show_extra_dates'] = true;
		
		$sql = "ALTER TABLE ".DP_PRO_EVENT_CALENDAR_TABLE_CALENDARS." ADD (form_show_extra_dates TINYINT(1) NOT NULL DEFAULT 0);";
		$wpdb->query($sql);
		
		update_option('dpProEventCalendar_options',$dpProEventCalendar);
	}

	if(!isset($dpProEventCalendar['booking_custom_fields_calendar'])) {
		$dpProEventCalendar['booking_custom_fields_calendar'] = true;
		
		$sql = "ALTER TABLE ".DP_PRO_EVENT_CALENDAR_TABLE_CALENDARS." ADD (booking_custom_fields text NULL);";
		$wpdb->query($sql);

		$sql = "ALTER TABLE ".DP_PRO_EVENT_CALENDAR_TABLE_CALENDARS." ADD (form_custom_fields text NULL);";
		$wpdb->query($sql);
		
		update_option('dpProEventCalendar_options',$dpProEventCalendar);
	}

	if(!isset($dpProEventCalendar['form_show_timezone'])) {
		$dpProEventCalendar['form_show_timezone'] = true;
		
		$sql = "ALTER TABLE ".DP_PRO_EVENT_CALENDAR_TABLE_CALENDARS." ADD (form_show_timezone TINYINT(1) NOT NULL DEFAULT 0);";
		$wpdb->query($sql);

		update_option('dpProEventCalendar_options',$dpProEventCalendar);
	}
	
	if(!isset($dpProEventCalendar['booking_remaining'])) {
		$dpProEventCalendar['booking_remaining'] = true;

		$sql = "ALTER TABLE ".DP_PRO_EVENT_CALENDAR_TABLE_CALENDARS." ADD (booking_show_remaining TINYINT(1) NOT NULL DEFAULT 1);";
		$wpdb->query($sql);
		
		update_option('dpProEventCalendar_options',$dpProEventCalendar);
	}

	if(!isset($dpProEventCalendar['booking_cancel_email_enable'])) {

		$dpProEventCalendar['booking_cancel_email_enable'] = true;
		
		$sql = "ALTER TABLE ".DP_PRO_EVENT_CALENDAR_TABLE_CALENDARS." ADD (booking_cancel_email_enable TINYINT(1) NOT NULL DEFAULT 0);";
		$wpdb->query($sql);

		$sql = "ALTER TABLE ".DP_PRO_EVENT_CALENDAR_TABLE_CALENDARS." ADD (booking_cancel_email_template TEXT NOT NULL);";
		$wpdb->query($sql);
		
		update_option('dpProEventCalendar_options',$dpProEventCalendar);
	}

	if(!isset($dpProEventCalendar['new_event_email_enable'])) {
		$dpProEventCalendar['new_event_email_enable'] = true;
		
		$sql = "ALTER TABLE ".DP_PRO_EVENT_CALENDAR_TABLE_CALENDARS." ADD (new_event_email_enable TINYINT(1) NOT NULL DEFAULT 1);";
		$wpdb->query($sql);
		
		update_option('dpProEventCalendar_options',$dpProEventCalendar);
	}

	if(!isset($dpProEventCalendar['booking_cancel'])) {
		$dpProEventCalendar['booking_cancel'] = true;
		
		$sql = "ALTER TABLE ".DP_PRO_EVENT_CALENDAR_TABLE_CALENDARS." ADD (booking_cancel TINYINT(1) NOT NULL DEFAULT 0);";
		$wpdb->query($sql);
		
		update_option('dpProEventCalendar_options',$dpProEventCalendar);
	}

	if(!isset($dpProEventCalendar['link_post_target'])) {
		$dpProEventCalendar['link_post_target'] = true;
		
		$sql = "ALTER TABLE ".DP_PRO_EVENT_CALENDAR_TABLE_CALENDARS." ADD (link_post_target varchar(80) NULL DEFAULT '_self');";
		$wpdb->query($sql);
		update_option('dpProEventCalendar_options',$dpProEventCalendar);

	}

	if(!isset($dpProEventCalendar['form_show_location_options'])) {
		$dpProEventCalendar['form_show_location_options'] = true;
		
		$sql = "ALTER TABLE ".DP_PRO_EVENT_CALENDAR_TABLE_CALENDARS." ADD (form_show_location_options TINYINT(1) NOT NULL DEFAULT 0);";
		$wpdb->query($sql);
		update_option('dpProEventCalendar_options',$dpProEventCalendar);
	}

	if(!isset($dpProEventCalendar['show_location_filter'])) {
		$dpProEventCalendar['show_location_filter'] = true;
		
		$sql = "ALTER TABLE ".DP_PRO_EVENT_CALENDAR_TABLE_CALENDARS." ADD (show_location_filter TINYINT(1) NOT NULL DEFAULT 0);";
		$wpdb->query($sql);
		update_option('dpProEventCalendar_options',$dpProEventCalendar);
	}

	if(!isset($dpProEventCalendar['display_attendees_names'])) {
		$dpProEventCalendar['display_attendees_names'] = true;
		
		$sql = "ALTER TABLE ".DP_PRO_EVENT_CALENDAR_TABLE_CALENDARS." ADD (booking_display_attendees_names TINYINT(1) NOT NULL DEFAULT 0);";
		$wpdb->query($sql);
		update_option('dpProEventCalendar_options',$dpProEventCalendar);
	}

	if(!isset($dpProEventCalendar['form_show_end_time'])) {
		$dpProEventCalendar['form_show_end_time'] = true;
		
		$sql = "ALTER TABLE ".DP_PRO_EVENT_CALENDAR_TABLE_CALENDARS." ADD (form_show_end_date TINYINT(1) NOT NULL DEFAULT 1);";
		$wpdb->query($sql);

		$sql = "ALTER TABLE ".DP_PRO_EVENT_CALENDAR_TABLE_CALENDARS." ADD (form_show_start_time TINYINT(1) NOT NULL DEFAULT 1);";
		$wpdb->query($sql);

		$sql = "ALTER TABLE ".DP_PRO_EVENT_CALENDAR_TABLE_CALENDARS." ADD (form_show_end_time TINYINT(1) NOT NULL DEFAULT 1);";
		$wpdb->query($sql);
		
		update_option('dpProEventCalendar_options',$dpProEventCalendar);
	}

	if(!isset($dpProEventCalendar['show_timezone_update'])) {
		$dpProEventCalendar['show_timezone_update'] = true;
		
		$sql = "ALTER TABLE ".DP_PRO_EVENT_CALENDAR_TABLE_CALENDARS." ADD (show_timezone TINYINT(1) NOT NULL DEFAULT 0);";
		$wpdb->query($sql);
		
		update_option('dpProEventCalendar_options',$dpProEventCalendar);
	}

	if(!isset($dpProEventCalendar['sync_ical_category'])) {
		$dpProEventCalendar['sync_ical_category'] = true;
		
		$sql = "ALTER TABLE ".DP_PRO_EVENT_CALENDAR_TABLE_CALENDARS." ADD (sync_ical_category INT(11) NOT NULL DEFAULT 0);";
		$wpdb->query($sql);
		
		update_option('dpProEventCalendar_options',$dpProEventCalendar);
	}

	if(!isset($dpProEventCalendar['translation_fields'])) {
		$dpProEventCalendar['translation_fields'] = true;
		
		$sql = "ALTER TABLE ".DP_PRO_EVENT_CALENDAR_TABLE_CALENDARS." ADD (translation_fields TEXT NULL DEFAULT '');";
		$wpdb->query($sql);
		
		update_option('dpProEventCalendar_options',$dpProEventCalendar);
	}

	if(!isset($dpProEventCalendar['sync_fb_page'])) {
		$dpProEventCalendar['sync_fb_page'] = true;
		
		$sql = "ALTER TABLE ".DP_PRO_EVENT_CALENDAR_TABLE_CALENDARS." ADD (sync_fb_page TEXT NOT NULL DEFAULT '');";
		$wpdb->query($sql);
		
		update_option('dpProEventCalendar_options',$dpProEventCalendar);
	}

	if(!isset($dpProEventCalendar['booking_email_template_reminder_user'])) {
		$dpProEventCalendar['booking_email_template_reminder_user'] = true;
		
		$sql = "ALTER TABLE ".DP_PRO_EVENT_CALENDAR_TABLE_CALENDARS." ADD (booking_email_template_reminder_user TEXT NOT NULL);";
		$wpdb->query($sql);
		
		update_option('dpProEventCalendar_options',$dpProEventCalendar);
	}

	if(!isset($dpProEventCalendar['form_show_color'])) {
		$dpProEventCalendar['form_show_color'] = true;
		
		$sql = "ALTER TABLE ".DP_PRO_EVENT_CALENDAR_TABLE_CALENDARS." ADD (form_show_color TINYINT(1) NOT NULL DEFAULT 0);";
		$wpdb->query($sql);

		update_option('dpProEventCalendar_options',$dpProEventCalendar);
	}

	if(!isset($dpProEventCalendar['booking_max_upcoming_dates'])) {
		$dpProEventCalendar['booking_max_upcoming_dates'] = true;
		
		$sql = "ALTER TABLE ".DP_PRO_EVENT_CALENDAR_TABLE_CALENDARS." ADD (booking_max_upcoming_dates INT(11) NOT NULL DEFAULT 10);";
		$wpdb->query($sql);
		update_option('dpProEventCalendar_options',$dpProEventCalendar);
	}

	if(!isset($dpProEventCalendar['form_bookings'])) {
		$dpProEventCalendar['form_bookings'] = true;
		
		$sql = "ALTER TABLE ".DP_PRO_EVENT_CALENDAR_TABLE_CALENDARS." ADD (form_show_booking_enable TINYINT(1) NOT NULL DEFAULT 0);";
		$wpdb->query($sql);
		$sql = "ALTER TABLE ".DP_PRO_EVENT_CALENDAR_TABLE_CALENDARS." ADD (form_show_booking_limit TINYINT(1) NOT NULL DEFAULT 0);";
		$wpdb->query($sql);
		$sql = "ALTER TABLE ".DP_PRO_EVENT_CALENDAR_TABLE_CALENDARS." ADD (form_show_booking_price TINYINT(1) NOT NULL DEFAULT 0);";
		$wpdb->query($sql);
		update_option('dpProEventCalendar_options',$dpProEventCalendar);
	}
					
	if(!isset($dpProEventCalendar['form_text_editor'])) {
		$dpProEventCalendar['form_text_editor'] = true;
		
		$sql = "ALTER TABLE ".DP_PRO_EVENT_CALENDAR_TABLE_CALENDARS." ADD (form_text_editor TINYINT(1) NOT NULL DEFAULT 1);";
		$wpdb->query($sql);
		update_option('dpProEventCalendar_options',$dpProEventCalendar);
	}

	if(!isset($dpProEventCalendar['booking_max_quantity'])) {
		$dpProEventCalendar['booking_max_quantity'] = true;
		
		$sql = "ALTER TABLE ".DP_PRO_EVENT_CALENDAR_TABLE_CALENDARS." ADD (booking_max_quantity INT(11) NOT NULL DEFAULT 3);";
		$wpdb->query($sql);
		update_option('dpProEventCalendar_options',$dpProEventCalendar);
	}

	if(!isset($dpProEventCalendar['new_event_template_published'])) {
		$dpProEventCalendar['new_event_template_published'] = true;
		
		$sql = "ALTER TABLE ".DP_PRO_EVENT_CALENDAR_TABLE_CALENDARS." ADD (new_event_email_template_published TEXT NOT NULL);";
		$wpdb->query($sql);
		
		update_option('dpProEventCalendar_options',$dpProEventCalendar);
	}

	if(!isset($dpProEventCalendar['display_attendees'])) {
		$dpProEventCalendar['display_attendees'] = true;
		
		$sql = "ALTER TABLE ".DP_PRO_EVENT_CALENDAR_TABLE_CALENDARS." ADD (booking_display_attendees TINYINT(1) NOT NULL DEFAULT 0);";
		$wpdb->query($sql);
		update_option('dpProEventCalendar_options',$dpProEventCalendar);
	}

	if(!isset($dpProEventCalendar['allow_user_add_event_roles'])) {
		$dpProEventCalendar['allow_user_add_event_roles'] = true;
		
		$sql = "ALTER TABLE ".DP_PRO_EVENT_CALENDAR_TABLE_CALENDARS." ADD (allow_user_add_event_roles text NULL);";
		$wpdb->query($sql);
		
		update_option('dpProEventCalendar_options',$dpProEventCalendar);
	}

	if(!isset($dpProEventCalendar['daily_weekly_layout'])) {
		$dpProEventCalendar['daily_weekly_layout'] = true;
		
		$sql = "ALTER TABLE ".DP_PRO_EVENT_CALENDAR_TABLE_CALENDARS." ADD (daily_weekly_layout VARCHAR(80) NOT NULL DEFAULT 'schedule');";
		$wpdb->query($sql);
		update_option('dpProEventCalendar_options',$dpProEventCalendar);
	}

	if(!isset($dpProEventCalendar['show_titles_monthly'])) {
		$dpProEventCalendar['show_titles_monthly'] = true;
		
		$sql = "ALTER TABLE ".DP_PRO_EVENT_CALENDAR_TABLE_CALENDARS." ADD (show_titles_monthly TINYINT(1) NOT NULL DEFAULT 0);";
		$wpdb->query($sql);
		update_option('dpProEventCalendar_options',$dpProEventCalendar);
	}

}