<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_EN_Delete_Ticket' ) ) :

	final class WPSC_EN_Delete_Ticket extends WPSC_Email_Notifications {

		/**
		 * Slug for this event (must be unique)
		 *
		 * @var string
		 */
		private static $slug = 'delete-ticket';

		/**
		 * Ticket object
		 *
		 * @var WPSC_Ticket
		 */
		public $ticket;

		/**
		 * Initialize this class
		 */
		public static function init() {

			// process event.
			add_action( 'wpsc_delete_ticket', array( __CLASS__, 'process_event' ), 200 );
		}

		/**
		 * Load this event for email notifications
		 *
		 * @param array $events - event.
		 * @return array
		 */
		public static function load_event( $events ) {

			$events[ self::$slug ] = esc_attr__( 'Delete ticket', 'supportcandy' );
			return $events;
		}

		/**
		 * Process emails for this event
		 *
		 * @param WPSC_Ticket $ticket - ticket info.
		 * @return void
		 */
		public static function process_event( $ticket ) {

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
	}
endif;

WPSC_EN_Delete_Ticket::init();
