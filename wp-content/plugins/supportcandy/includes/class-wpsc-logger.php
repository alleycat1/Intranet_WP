<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_Logger' ) ) :

	final class WPSC_Logger {

		/**
		 * Initialize this class
		 *
		 * @return void
		 */
		public static function init() {

			// Ticket logs.
			add_action( 'wpsc_change_ticket_status', array( __CLASS__, 'change_status' ), 10, 4 );
			add_action( 'wpsc_change_ticket_category', array( __CLASS__, 'change_category' ), 10, 4 );
			add_action( 'wpsc_change_ticket_priority', array( __CLASS__, 'change_priority' ), 10, 4 );
			add_action( 'wpsc_change_raised_by', array( __CLASS__, 'change_raised_by' ), 10, 4 );
			add_action( 'wpsc_change_assignee', array( __CLASS__, 'change_assignee' ), 10, 4 );
			add_action( 'wpsc_change_ticket_fields', array( __CLASS__, 'change_custom_fields' ), 10, 3 );
			add_action( 'wpsc_change_agentonly_fields', array( __CLASS__, 'change_custom_fields' ), 10, 3 );
			add_action( 'wpsc_change_ticket_add_recipients', array( __CLASS__, 'change_add_recipients' ), 10, 4 );
			add_action( 'wpsc_change_ticket_subject', array( __CLASS__, 'change_subject' ), 10, 4 );
			add_action( 'wpsc_change_ticket_rating', array( __CLASS__, 'change_rating' ), 10, 4 );
			add_action( 'wpsc_change_usergroup', array( __CLASS__, 'change_usergroup' ), 10, 4 );
		}

		/**
		 * Log for change ticket status
		 *
		 * @param WPSC_ticket $ticket - ticket object.
		 * @param string      $prev - previous status.
		 * @param string      $new -  new status.
		 * @param int         $customer_id - cust id.
		 * @return void
		 */
		public static function change_status( $ticket, $prev, $new, $customer_id ) {

			$current_date = self::get_log_datetime();
			$thread       = WPSC_Thread::insert(
				array(
					'ticket'       => $ticket->id,
					'customer'     => $customer_id,
					'type'         => 'log',
					'body'         => wp_json_encode(
						array(
							'slug' => 'status',
							'prev' => $prev,
							'new'  => $new,
						)
					),
					'date_created' => $current_date,
					'date_updated' => $current_date,
				)
			);
		}

		/**
		 * Log for change ticket category
		 *
		 * @param WPSC_ticket $ticket - ticket object.
		 * @param string      $prev - previous status.
		 * @param string      $new -  new status.
		 * @param int         $customer_id - cust id.
		 * @return void
		 */
		public static function change_category( $ticket, $prev, $new, $customer_id ) {

			$current_date = self::get_log_datetime();
			$thread       = WPSC_Thread::insert(
				array(
					'ticket'       => $ticket->id,
					'customer'     => $customer_id,
					'type'         => 'log',
					'body'         => wp_json_encode(
						array(
							'slug' => 'category',
							'prev' => $prev,
							'new'  => $new,
						)
					),
					'date_created' => $current_date,
					'date_updated' => $current_date,
				)
			);
		}

		/**
		 * Log for change ticket priority
		 *
		 * @param WPSC_ticket $ticket - ticket object.
		 * @param string      $prev - previous status.
		 * @param string      $new -  new status.
		 * @param int         $customer_id - cust id.
		 * @return void
		 */
		public static function change_priority( $ticket, $prev, $new, $customer_id ) {

			$current_date = self::get_log_datetime();
			$thread       = WPSC_Thread::insert(
				array(
					'ticket'       => $ticket->id,
					'customer'     => $customer_id,
					'type'         => 'log',
					'body'         => wp_json_encode(
						array(
							'slug' => 'priority',
							'prev' => $prev,
							'new'  => $new,
						)
					),
					'date_created' => $current_date,
					'date_updated' => $current_date,
				)
			);
		}

		/**
		 * Change raised by
		 *
		 * @param WPSC_ticket $ticket - ticket object.
		 * @param string      $prev - previous status.
		 * @param string      $new -  new status.
		 * @param int         $customer_id - cust id.
		 * @return void
		 */
		public static function change_raised_by( $ticket, $prev, $new, $customer_id ) {

			$current_date = self::get_log_datetime();
			$thread       = WPSC_Thread::insert(
				array(
					'ticket'       => $ticket->id,
					'customer'     => $customer_id,
					'type'         => 'log',
					'body'         => wp_json_encode(
						array(
							'slug' => 'customer',
							'prev' => $prev->id,
							'new'  => $new->id,
						)
					),
					'date_created' => $current_date,
					'date_updated' => $current_date,
				)
			);
		}

		/**
		 * Change assignee
		 *
		 * @param WPSC_ticket $ticket - ticket object.
		 * @param string      $prev - previous status.
		 * @param string      $new -  new status.
		 * @param int         $customer_id - cust id.
		 * @return void
		 */
		public static function change_assignee( $ticket, $prev, $new, $customer_id ) {

			$current_date = self::get_log_datetime();

			$prev_ids = array();
			foreach ( $prev as $agent ) {
				$prev_ids[] = $agent->id;
			}

			$new_ids = array();
			foreach ( $new as $agent ) {
				$new_ids[] = $agent->id;
			}

			$thread = WPSC_Thread::insert(
				array(
					'ticket'       => $ticket->id,
					'customer'     => $customer_id,
					'type'         => 'log',
					'body'         => wp_json_encode(
						array(
							'slug' => 'assigned_agent',
							'prev' => $prev_ids,
							'new'  => $new_ids,
						)
					),
					'date_created' => $current_date,
					'date_updated' => $current_date,
				)
			);
		}

		/**
		 * Change custom fields (ticket/agentonly)
		 *
		 * @param WPSC_Ticket $prev - ticket object before changes.
		 * @param WPSC_Ticket $new - ticket object after changes.
		 * @param int         $customer_id - customer id.
		 * @return void
		 */
		public static function change_custom_fields( $prev, $new, $customer_id = null ) {

			if ( $prev == $new ) {
				return;
			}

			$current_date = self::get_log_datetime();

			if ( $customer_id === null ) {
				$customer_id = WPSC_Current_User::$current_user->customer->id;
			}

			foreach ( WPSC_Custom_Field::$custom_fields as $cf ) {

				if (
					! class_exists( $cf->type ) ||
					$cf->type::$is_default ||
					! in_array( $cf->field, array( 'ticket', 'agentonly' ) ) ||
					( $cf->field == 'ticket' && in_array( $cf->type::$slug, WPSC_ITW_Ticket_Fields::$ignore_cft ) ) ||
					( $cf->field == 'agentonly' && in_array( $cf->type::$slug, WPSC_ITW_Agentonly_Fields::$ignore_cft ) )
				) {
					continue;
				}

				$cf->type::insert_ticket_log( $cf, $prev, $new, $current_date, $customer_id );
			}
		}

		/**
		 * Change additional recepients of a ticket
		 *
		 * @param WPSC_ticket $ticket - ticket object.
		 * @param string      $prev - previous status.
		 * @param string      $new -  new status.
		 * @param int         $customer_id - cust id.
		 * @return void
		 */
		public static function change_add_recipients( $ticket, $prev, $new, $customer_id ) {

			$current_date = self::get_log_datetime();
			$prev         = $prev ? implode( '|', $prev ) : '';
			$new          = $new ? implode( '|', $new ) : '';

			$thread = WPSC_Thread::insert(
				array(
					'ticket'       => $ticket->id,
					'customer'     => $customer_id,
					'type'         => 'log',
					'body'         => wp_json_encode(
						array(
							'slug' => 'add_recipients',
							'prev' => $prev,
							'new'  => $new,
						)
					),
					'date_created' => $current_date,
					'date_updated' => $current_date,
				)
			);
		}

		/**
		 * Change ticket subject
		 *
		 * @param WPSC_ticket $ticket - ticket object.
		 * @param string      $prev - previous status.
		 * @param string      $new -  new status.
		 * @param int         $customer_id - cust id.
		 * @return void
		 */
		public static function change_subject( $ticket, $prev, $new, $customer_id ) {

			$current_date = self::get_log_datetime();
			$prev_val     = htmlspecialchars( $prev, ENT_QUOTES );
			$new_val      = htmlspecialchars( $new, ENT_QUOTES );

			$thread = WPSC_Thread::insert(
				array(
					'ticket'       => $ticket->id,
					'customer'     => $customer_id,
					'type'         => 'log',
					'body'         => wp_json_encode(
						array(
							'slug' => 'subject',
							'prev' => $prev_val,
							'new'  => $new_val,
						)
					),
					'date_created' => $current_date,
					'date_updated' => $current_date,
				)
			);
		}

		/**
		 * Change ticket rating
		 *
		 * @param WPSC_ticket $ticket - ticket object.
		 * @param string      $prev - previous rating.
		 * @param string      $new -  new rating.
		 * @param int         $customer_id - cust id.
		 * @return void
		 */
		public static function change_rating( $ticket, $prev, $new, $customer_id ) {

			$current_date = self::get_log_datetime();

			$thread = WPSC_Thread::insert(
				array(
					'ticket'       => $ticket->id,
					'customer'     => $customer_id,
					'type'         => 'log',
					'body'         => wp_json_encode(
						array(
							'slug' => 'rating',
							'prev' => $prev,
							'new'  => $new,
						)
					),
					'date_created' => $current_date,
					'date_updated' => $current_date,
				)
			);
		}

		/**
		 * Return log datetime
		 *
		 * @return datetime
		 */
		private static function get_log_datetime() {

			return ( new DateTime() )->add( new DateInterval( 'PT1S' ) )->format( 'Y-m-d H:i:s' );
		}

		/**
		 * Change ticket usergroup
		 *
		 * @param WPSC_ticket $ticket - ticket object.
		 * @param string      $prev - previous usergroup.
		 * @param string      $new -  new usergroup.
		 * @param int         $customer_id - cust id.
		 * @return void
		 */
		public static function change_usergroup( $ticket, $prev, $new, $customer_id ) {
			$current_date = self::get_log_datetime();

			$prev_ids = array();
			foreach ( $prev as $usergroup ) {
				$prev_ids[] = $usergroup->id;
			}

			$new_ids = array();
			foreach ( $new as $usergroup ) {
				$new_ids[] = $usergroup->id;
			}

			$thread = WPSC_Thread::insert(
				array(
					'ticket'       => $ticket->id,
					'customer'     => $customer_id,
					'type'         => 'log',
					'body'         => wp_json_encode(
						array(
							'slug' => 'usergroups',
							'prev' => $prev_ids,
							'new'  => $new_ids,
						)
					),
					'date_created' => $current_date,
					'date_updated' => $current_date,
				)
			);

		}

	}
endif;

WPSC_Logger::init();
