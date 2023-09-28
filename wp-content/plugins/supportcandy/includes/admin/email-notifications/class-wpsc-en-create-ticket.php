<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_EN_Create_Ticket' ) ) :

	final class WPSC_EN_Create_Ticket extends WPSC_Email_Notifications {

		/**
		 * Slug for this event (must be unique)
		 *
		 * @var string
		 */
		private static $slug = 'create-ticket';

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
			add_action( 'wpsc_create_new_ticket', array( __CLASS__, 'process_event' ), 200 );
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

				// Ticket attachments.
				$thread = $ticket->get_description_thread();
				if ( $thread && $thread->attachments ) {
					foreach ( $thread->attachments as $attachment ) :
						$en->attachments[] = $attachment;
					endforeach;
				}

				// macro attachments.
				$en->attachments          = array_merge( $en->attachments, WPSC_Macros::$attachments );
				WPSC_Macros::$attachments = array();

				$en = apply_filters( 'wpsc_en_before_sending', $en );

				$advanced = get_option( 'wpsc-ms-advanced-settings' );
				if ( $advanced['do-not-notify-owner'] && isset( $_POST['notify_owner'] ) ) { // phpcs:ignore

					if ( in_array( $ticket->customer->email, $en->to ) ) {
						unset( $en->to[ array_search( $ticket->customer->email, $en->to ) ] );
					}
					if ( in_array( $ticket->customer->email, $en->cc ) ) {
						unset( $en->cc[ array_search( $ticket->customer->email, $en->cc ) ] );
					}
					if ( in_array( $ticket->customer->email, $en->bcc ) ) {
						unset( $en->bcc[ array_search( $ticket->customer->email, $en->bcc ) ] );
					}
				}

				if ( ! $en->to ) {

					$other_emails = array_merge( $en->cc, $en->bcc );
					if ( $other_emails ) {
						$en->to[] = $other_emails[0];
					} else {
						continue;
					}
				}

				// send an email.
				$attachment_ids = array();
				foreach ( $en->attachments as $attachment ) {
					$attachment_ids[] = $attachment->id;
				}
				WPSC_Background_Email::insert(
					array(
						'from_name'   => $en->from_name,
						'from_email'  => $en->from_email,
						'reply_to'    => $gs['reply-to'],
						'subject'     => $en->subject,
						'body'        => $en->body,
						'to_email'    => implode( '|', $en->to ),
						'cc_email'    => implode( '|', $en->cc ),
						'bcc_email'   => implode( '|', $en->bcc ),
						'attachments' => implode( '|', $attachment_ids ),
						'priority'    => 1,
					)
				);
			}
		}
	}
endif;

WPSC_EN_Create_Ticket::init();
