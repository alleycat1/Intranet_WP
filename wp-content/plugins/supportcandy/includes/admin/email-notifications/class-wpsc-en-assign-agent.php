<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_EN_Assign_Agent' ) ) :

	final class WPSC_EN_Assign_Agent extends WPSC_Email_Notifications {

		/**
		 * Slug for this event (must be unique)
		 *
		 * @var string
		 */
		private static $slug = 'change-assignee';

		/**
		 * Ticket object
		 *
		 * @var WPSC_Ticket
		 */
		public $ticket;

		/**
		 * Array of agent model
		 *
		 * @var array
		 */
		public $prev;

		/**
		 * Array of agent model
		 *
		 * @var array
		 */
		public $new;

		/**
		 * Initialize this class
		 */
		public static function init() {

			// process event.
			add_action( 'wpsc_change_assignee', array( __CLASS__, 'process_event' ), 200, 4 );

			// filter for previous assignee.
			add_filter( 'wpsc_en_get_to_addresses', array( __CLASS__, 'add_prev_assignee' ), 10, 3 );
			add_filter( 'wpsc_en_get_cc_addresses', array( __CLASS__, 'add_prev_assignee' ), 10, 3 );
			add_filter( 'wpsc_en_get_bcc_addresses', array( __CLASS__, 'add_prev_assignee' ), 10, 3 );
		}

		/**
		 * Process emails for this event
		 *
		 * @param WPSC_Ticket $ticket -ticket info.
		 * @param array       $prev - array of agent models.
		 * @param array       $new - array of agent models.
		 * @param integer     $customer_id - ID of customer model.
		 * @return void
		 */
		public static function process_event( $ticket, $prev, $new, $customer_id ) {

			$gs              = get_option( 'wpsc-en-general' );
			$email_templates = get_option( 'wpsc-email-templates', array() );

			foreach ( $email_templates as $key => $et ) {

				if ( $et['event'] != self::$slug ) {
					continue;
				}

				// email notification object.
				$en = new self();

				// set properties.
				$en->ticket = $ticket;
				$en->prev   = $prev;
				$en->new    = $new;

				// set template.
				$en->template     = $et;
				$en->template_key = $key;

				// check whether conditions matches (if any).
				if ( ! $en->is_valid() ) {
					continue;
				}

				$en = apply_filters( 'wpsc_en_before_sending', $en );

				// send an email.
				WPSC_Background_Email::insert(
					array(
						'from_name'  => $en->from_name,
						'from_email' => $en->from_email,
						'reply_to'   => $gs['reply-to'],
						'subject'    => $en->subject,
						'body'       => $en->body,
						'to_email'   => implode( '|', $en->to ),
						'cc_email'   => implode( '|', $en->cc ),
						'bcc_email'  => implode( '|', $en->bcc ),
						'priority'   => 2,
					)
				);
			}
		}

		/**
		 * Check for previous assignee general recipient option
		 *
		 * @param array                    $gr - general recepients.
		 * @param string                   $recipient - recipient.
		 * @param WPSC_Email_Notifications $en - email notification.
		 * @return array
		 */
		public static function add_prev_assignee( $gr, $recipient, $en ) {

			if ( $recipient != 'prev-assignee' ) {
				return $gr;
			}

			foreach ( $en->prev as $agent ) {
				if ( ! $agent->is_active ) {
					continue;
				}
				$gr[] = $agent->customer->email;
			}
			return $gr;
		}
	}
endif;

WPSC_EN_Assign_Agent::init();
