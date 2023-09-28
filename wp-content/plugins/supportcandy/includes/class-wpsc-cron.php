<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_Cron' ) ) :

	final class WPSC_Cron {

		/**
		 * Initialize this class
		 *
		 * @return void
		 */
		public static function init() {

			// Add custom cron intervals.
			add_filter( 'cron_schedules', array( __CLASS__, 'custom_interval' ) ); //phpcs:ignore

			// Schedule cron jobs.
			add_action( 'init', array( __CLASS__, 'schedule_events' ) );

			// cron event callbacks.
			add_action( 'wpsc_auto_delete_closed_tickets', array( __CLASS__, 'auto_delete_closed_tickets' ) );
			add_action( 'wpsc_permenently_delete_tickets', array( __CLASS__, 'permenently_delete_tickets' ) );

			// run background processes.
			add_action( 'wp_ajax_wpsc_run_ajax_background_process', array( __CLASS__, 'run_background_process' ) );
			add_action( 'wp_ajax_nopriv_wpsc_run_ajax_background_process', array( __CLASS__, 'run_background_process' ) );
		}

		/**
		 * Custom cron job intervals for SupportCandy
		 *
		 * @param array $schedules - schedule time.
		 * @return array
		 */
		public static function custom_interval( $schedules ) {

			$schedules['wpsc_1min'] = array(
				'interval' => 60,
				'display'  => esc_attr__( 'Every one minute', 'supportcandy' ),
			);

			$schedules['wpsc_5min'] = array(
				'interval' => 300,
				'display'  => esc_attr__( 'Every five minutes', 'supportcandy' ),
			);

			return $schedules;
		}

		/**
		 * Schedule cron job events for SupportCandy
		 *
		 * @return void
		 */
		public static function schedule_events() {

			// Schedule cron job for every minute events.
			if ( ! wp_next_scheduled( 'wpsc_cron_one_minute' ) ) {
				wp_schedule_event(
					time(),
					'wpsc_1min',
					'wpsc_cron_one_minute'
				);
			}

			// Schedule cron job for every five minute events.
			if ( ! wp_next_scheduled( 'wpsc_cron_five_minute' ) ) {
				wp_schedule_event(
					time(),
					'wpsc_5min',
					'wpsc_cron_five_minute'
				);
			}

			// Schedule cron job for daily events.
			if ( ! wp_next_scheduled( 'wpsc_cron_daily' ) ) {
				wp_schedule_event(
					self::get_midnight_timestamp(),
					'daily',
					'wpsc_cron_daily'
				);
			}

			// license checker.
			if ( ! wp_next_scheduled( 'wpsc_license_checker' ) ) {
				wp_schedule_event(
					self::get_midnight_timestamp(),
					'daily',
					'wpsc_license_checker'
				);
			}

			// Auto-delete closed tickets.
			if ( ! wp_next_scheduled( 'wpsc_auto_delete_closed_tickets' ) ) {
				wp_schedule_event(
					time(),
					'hourly',
					'wpsc_auto_delete_closed_tickets'
				);
			}

			// Permenently delete tickets.
			if ( ! wp_next_scheduled( 'wpsc_permenently_delete_tickets' ) ) {
				wp_schedule_event(
					time(),
					'hourly',
					'wpsc_permenently_delete_tickets'
				);
			}

			// Attachment garbage collector.
			if ( ! wp_next_scheduled( 'wpsc_attach_garbage_collector' ) ) {
				wp_schedule_event(
					time(),
					'hourly',
					'wpsc_attach_garbage_collector'
				);
			}
		}

		/**
		 * Remove existing scheduled events.
		 * Can be used while deactivation of plugin or resetting schedules after an update etc.
		 *
		 * @return void
		 */
		public static function unschedule_events() {

			// Remove every minute cron.
			$timestamp = wp_next_scheduled( 'wpsc_cron_one_minute' );
			if ( $timestamp ) {
				wp_unschedule_event( $timestamp, 'wpsc_cron_one_minute' );
			}

			// Remove every five minute cron.
			$timestamp = wp_next_scheduled( 'wpsc_cron_five_minute' );
			if ( $timestamp ) {
				wp_unschedule_event( $timestamp, 'wpsc_cron_five_minute' );
			}

			// Remove daily cron.
			$timestamp = wp_next_scheduled( 'wpsc_cron_daily' );
			if ( $timestamp ) {
				wp_unschedule_event( $timestamp, 'wpsc_cron_daily' );
			}
		}

		/**
		 * Provide mid-night unix timestamp
		 *
		 * @return String
		 */
		public static function get_midnight_timestamp() {

			$tz   = wp_timezone();
			$date = new DateTime( 'now', $tz );
			$date->setTime( 0, 0, 0 );
			$date->add( new DateInterval( 'P1D' ) );
			return $date->getTimestamp();
		}

		/**
		 * Auto delete closed ticket after x days/months/years
		 *
		 * @return void
		 */
		public static function auto_delete_closed_tickets() {

			$tz = wp_timezone();
			$today = new DateTime( 'now', $tz );
			$transient_label = 'wpsc_auto_delete_closed_tickets_cron_' . $today->format( 'Y-m-d' );
			$cron_status = get_transient( $transient_label );
			if ( false === $cron_status ) {
				$cron_status = 'active';
			}

			// return if today's tickets finished checking.
			if ( $cron_status == 'finished' ) {
				return;
			}

			$ad_settings = get_option( 'wpsc-tl-ms-advanced' );
			$ms_settings = get_option( 'wpsc-ms-advanced-settings' );

			if ( ! $ms_settings['auto-delete-tickets-time'] ) {
				return;
			}

			$age = clone $today;
			switch ( $ms_settings['auto-delete-tickets-unit'] ) {

				case 'days':
					$age->sub( new DateInterval( 'P' . $ms_settings['auto-delete-tickets-time'] . 'D' ) );
					break;

				case 'month':
					$age->sub( new DateInterval( 'P' . $ms_settings['auto-delete-tickets-time'] . 'M' ) );
					break;

				case 'year':
					$age->sub( new DateInterval( 'P' . $ms_settings['auto-delete-tickets-time'] . 'Y' ) );
					break;
			}

			$tickets = WPSC_Ticket::find(
				array(
					'items_per_page' => 20,
					'orderby'        => 'date_closed',
					'order'          => 'ASC',
					'meta_query'     => array(
						'relation' => 'AND',
						array(
							'slug'    => 'status',
							'compare' => 'IN',
							'val'     => $ad_settings['closed-ticket-statuses'],
						),
						array(
							'slug'    => 'date_closed',
							'compare' => '<',
							'val'     => $age->format( 'Y-m-d' ),
						),
					),
				)
			);

			// update cron status.
			delete_transient( $transient_label );
			$cron_status = $tickets['has_next_page'] ? 'active' : 'finished';
			set_transient( $transient_label, $cron_status, MINUTE_IN_SECONDS * 60 * 24 );

			// delete applicable tickets.
			if ( $tickets['total_items'] > 0 ) {
				foreach ( $tickets['results'] as $ticket ) {
					WPSC_Individual_Ticket::$ticket = $ticket;
					WPSC_Individual_Ticket::delete_ticket();
				}
			}
		}

		/**
		 * Permenently delete tickets after x days/months/years
		 *
		 * @return void
		 */
		public static function permenently_delete_tickets() {

			$tz = wp_timezone();
			$today = new DateTime( 'now', $tz );
			$transient_label = 'wpsc_permenently_delete_tickets_cron_' . $today->format( 'Y-m-d' );
			$cron_status = get_transient( $transient_label );
			if ( false === $cron_status ) {
				$cron_status = 'active';
			}

			// return if today's tickets finished checking.
			if ( $cron_status == 'finished' ) {
				return;
			}

			$ms_settings = get_option( 'wpsc-ms-advanced-settings' );

			if ( ! $ms_settings['permanent-delete-tickets-time'] ) {
				return;
			}

			$age = clone $today;
			switch ( $ms_settings['permanent-delete-tickets-unit'] ) {

				case 'days':
					$age->sub( new DateInterval( 'P' . $ms_settings['permanent-delete-tickets-time'] . 'D' ) );
					break;

				case 'month':
					$age->sub( new DateInterval( 'P' . $ms_settings['permanent-delete-tickets-time'] . 'M' ) );
					break;

				case 'year':
					$age->sub( new DateInterval( 'P' . $ms_settings['permanent-delete-tickets-time'] . 'Y' ) );
					break;
			}

			$tickets = WPSC_Ticket::find(
				array(
					'items_per_page' => 5,
					'orderby'        => 'date_closed',
					'order'          => 'ASC',
					'is_active'      => 0,
					'meta_query'     => array(
						'relation' => 'AND',
						array(
							'slug'    => 'date_updated',
							'compare' => '<',
							'val'     => $age->format( 'Y-m-d' ),
						),
					),
				)
			);

			// update cron status.
			delete_transient( $transient_label );
			$cron_status = $tickets['has_next_page'] ? 'active' : 'finished';
			set_transient( $transient_label, $cron_status, MINUTE_IN_SECONDS * 60 * 24 );

			// delete applicable tickets.
			if ( $tickets['total_items'] > 0 ) {
				foreach ( $tickets['results'] as $ticket ) {
					WPSC_Individual_Ticket::$ticket = $ticket;
					WPSC_Individual_Ticket::delete_permanently();
				}
			}
		}

		/**
		 * Execute background processes
		 *
		 * @return void
		 */
		public static function run_background_process() {

			do_action( 'wpsc_run_ajax_background_process' );
			wp_die();
		}
	}
endif;

WPSC_Cron::init();
