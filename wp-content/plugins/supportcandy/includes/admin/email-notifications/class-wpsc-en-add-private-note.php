<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_EN_Add_Private_Note' ) ) :

	final class WPSC_EN_Add_Private_Note extends WPSC_Email_Notifications {

		/**
		 * Slug for this event (must be unique)
		 *
		 * @var string
		 */
		private static $slug = 'submit-note';

		/**
		 * Ticket object
		 *
		 * @var WPSC_Ticket
		 */
		public $ticket;

		/**
		 * Thread object
		 *
		 * @var WPSC_Thread
		 */
		public $thread;

		/**
		 * Initialize this class
		 */
		public static function init() {

			// process event.
			add_action( 'wpsc_submit_note', array( __CLASS__, 'process_event' ), 200 );
		}

		/**
		 * Process emails for this event
		 * Ignore phpcs nonce issue as we already checked where it is called from.
		 *
		 * @param WPSC_Thread $thread - thread.
		 * @return void
		 */
		public static function process_event( $thread ) {

			$gs              = get_option( 'wpsc-en-general' );
			$email_templates = get_option( 'wpsc-email-templates', array() );

			foreach ( $email_templates as $key => $et ) {

				if ( $et['event'] != self::$slug ) {
					continue;
				}

				// email notification object.
				$en = new self();

				// set properties.
				$en->ticket = $thread->ticket;
				$en->thread = $thread;

				// set template.
				$en->template     = $et;
				$en->template_key = $key;

				// check whether conditions matches (if any).
				if ( ! $en->is_valid() ) {
					continue;
				}

				// phpcs:disable
				// cc emails provided in note.
				$cc = isset( $_POST['cc'] ) ? array_unique(
					array_filter(
						array_map(
							'sanitize_email',
							explode( ',', sanitize_text_field( wp_unslash( $_POST['cc'] ) ) )
						)
					)
				) : array();
				$en->cc  = array_merge( $en->cc, $cc );

				// bcc emails provided in note.
				$bcc = isset( $_POST['bcc'] ) ? array_unique(
					array_filter(
						array_map(
							'sanitize_email',
							explode( ',', sanitize_text_field( wp_unslash( $_POST['bcc'] ) ) )
						)
					)
				) : array();
				$en->bcc = array_merge( $en->bcc, $bcc );
				// phpcs:enable

				// Thread attachments.
				if ( $thread->attachments ) {
					foreach ( $thread->attachments as $attachment ) :
						$en->attachments[] = $attachment;
					endforeach;
				}

				// macro attachments.
				$en->attachments          = array_merge( $en->attachments, WPSC_Macros::$attachments );
				WPSC_Macros::$attachments = array();

				$en = apply_filters( 'wpsc_en_before_sending', $en );

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

WPSC_EN_Add_Private_Note::init();
