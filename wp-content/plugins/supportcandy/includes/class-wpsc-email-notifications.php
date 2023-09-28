<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_Email_Notifications' ) ) :

	class WPSC_Email_Notifications {

		/**
		 * From name
		 *
		 * @var string
		 */
		public $from_name;

		/**
		 * From email address
		 *
		 * @var string
		 */
		public $from_email;

		/**
		 * Reply to email address
		 *
		 * @var string
		 */
		public $reply_to;

		/**
		 * Recipient "to" addresses for email notification
		 *
		 * @var array
		 */
		public $to = array();

		/**
		 * Recipient "cc" addresses for email notification
		 *
		 * @var array
		 */
		public $cc = array();

		/**
		 * Recipient "bcc" addresses for email notification
		 *
		 * @var array
		 */
		public $bcc = array();

		/**
		 * Subject for email notification
		 *
		 * @var string
		 */
		public $subject;

		/**
		 * Body for email notification
		 *
		 * @var string
		 */
		public $body;

		/**
		 * Email notification template
		 *
		 * @var Array
		 */
		public $template;

		/**
		 * Attachments of email notification
		 *
		 * @var Array
		 */
		public $attachments = array();

		/**
		 * Blocked email array
		 *
		 * @var Array
		 */
		public static $block_emails = array();

		/**
		 * Initialize this class
		 */
		public static function init() {

			add_action( 'init', array( __CLASS__, 'block_emails_list' ), 11 );
			add_action( 'wpsc_cron_five_minute', array( __CLASS__, 'send_background_emails' ) );
			add_action( 'wpsc_run_ajax_background_process', array( __CLASS__, 'send_background_emails' ) );
		}

		/**
		 * Block emails
		 *
		 * @return void
		 */
		public static function block_emails_list() {

			$gs = get_option( 'wpsc-en-general' );
			self::$block_emails = apply_filters( 'wpsc_en_blocked_emails', $gs['blocked-emails'] );
			self::$block_emails = self::$block_emails ? array_unique( self::$block_emails ) : array();
		}

		/**
		 * Send background emails
		 *
		 * @return void
		 */
		public static function send_background_emails() {

			$gs    = get_option( 'wpsc-en-general' );
			$count = $gs['cron-email-count'];

			$emails = WPSC_Background_Email::find(
				array(
					'items_per_page' => $count,
					'orderby'        => 'priority',
					'order'          => 'ASC',
				)
			)['results'];

			foreach ( $emails as $email ) {

				$en = new self();
				$en->from_name   = $email->from_name;
				$en->from_email  = $email->from_email;
				$en->reply_to    = $email->reply_to;
				$en->to          = $email->to_email;
				$en->cc          = $email->cc_email;
				$en->bcc         = $email->bcc_email;
				$en->subject     = $email->subject;
				$en->body        = $email->body;
				$en->attachments = $email->attachments;
				$en->send();

				// delete db record.
				WPSC_Background_Email::destroy( $email->id );
			}
		}

		/**
		 * Send email for this object
		 *
		 * @return string
		 */
		public function send() {

			$en = get_option( 'wpsc-en-general' );
			// headers.
			$headers  = "From: {$this->from_name} <{$this->from_email}>\r\n";
			$headers .= "Reply-To: {$this->reply_to}\r\n";
			foreach ( $this->cc as $email ) {
				$headers .= "CC: {$email}\r\n";
			}
			foreach ( $this->bcc as $email ) {
				$headers .= "BCC: {$email}\r\n";
			}

			// attachments.
			$attachments = array();
			if ( $en['attachments-in-notification'] == 'actual-files' ) {

				$upload_dir = wp_upload_dir();
				foreach ( $this->attachments as $attachment ) :
					if ( ! $attachment->id ) {
						continue;
					}
					$filepath = $upload_dir['basedir'] . '/wpsc/temp/' . $attachment->id;
					if ( ! file_exists( $filepath ) ) {
						mkdir( $filepath, 0777, true );
					}
					$filepath .= '/' . $attachment->name;
					copy( $upload_dir['basedir'] . $attachment->file_path, $filepath );
					$attachments[] = $filepath;
				endforeach;
			}

			$headers .= "Content-Type: text/html; charset=utf-8\r\n";
			$headers .= 'In-Reply-To:<' . $this->from_email . '>';

			// send.
			$flag = wp_mail( $this->to, $this->subject, $this->body, $headers, $attachments );

			if ( $en['attachments-in-notification'] == 'actual-files' ) {

				// Remove temp files & directories.
				foreach ( $attachments as $filepath ) {
					if ( file_exists( $filepath ) ) {
						unlink( $filepath );
					}
				}
				foreach ( $this->attachments as $attachment ) {
					if ( is_dir( $upload_dir['basedir'] . '/wpsc/temp/' . $attachment->id ) ) {
						rmdir( $upload_dir['basedir'] . '/wpsc/temp/' . $attachment->id );
					}
				}
			}
			return $flag;
		}

		/**
		 * Check whether conditions matches (if any) and if settings are correct
		 *
		 * @return boolean
		 */
		public function is_valid() {

			// check if is_enable.
			if ( ! $this->template['is_enable'] ) {
				return false;
			}

			// from name & email.
			$en_general = get_option( 'wpsc-en-general' );
			if ( ! $en_general['from-name'] || ! $en_general['from-email'] ) {
				return false;
			}

			$this->from_name  = $en_general['from-name'];
			$this->from_email = $en_general['from-email'];
			$this->reply_to   = $en_general['reply-to'] ? $en_general['reply-to'] : $this->from_email;

			// subject.
			$this->set_subject();

			// body.
			$body       = WPSC_Translations::get( 'wpsc-en-tn-body-' . $this->template_key, stripslashes( $this->template['body']['text'] ) );
			$this->body = WPSC_Macros::replace( $body, $this->ticket, 'email-notification' );

			// to addreses.
			$this->set_to_addresses();

			// cc addreses.
			$this->set_cc_addresses();

			// bcc addreses.
			$this->set_bcc_addresses();

			if ( ! $this->to ) {

				$other_emails = array_merge( $this->cc, $this->bcc );
				if ( $other_emails ) {
					$this->to[] = $other_emails[0];
				} else {
					return false;
				}
			}
			// add to email address to cc (spam issue).
			if ( count( $this->to ) > 5 ) {
				$other_to_emails = array_slice( $this->to, 5 );
				$this->to = array_diff( $this->to, $other_to_emails );
				$this->cc = array_merge( $this->cc, $other_to_emails );
			}

			// check for conditions.
			if ( ! WPSC_Ticket_Conditions::is_valid( $this->template['conditions'], $this->ticket ) ) {
				return false;
			}

			return true;
		}

		/**
		 * Set subject for this email
		 *
		 * @return void
		 */
		public function set_subject() {

			$general_settings = get_option( 'wpsc-gs-general' );
			$ticket_alice     = $general_settings['ticket-alice'];

			$subject       = WPSC_Translations::get( 'wpsc-en-tn-subject-' . $this->template_key, stripslashes( $this->template['subject'] ) );
			$this->subject = '[' . $ticket_alice . $this->ticket->id . '] ' . WPSC_Macros::replace( $subject, $this->ticket );
		}

		/**
		 * Get "to" email addresses
		 *
		 * @return void
		 */
		public function set_to_addresses() {

			$current_user = WPSC_Current_User::$current_user;
			$et           = $this->template;
			$to           = array();
			$gs = get_option( 'wpsc-en-general' );

			// general recipients.
			$general_recipients = $et['to']['general-recipients'];
			foreach ( $general_recipients as $recipient ) {

				switch ( $recipient ) {

					case 'customer':
						$to[] = $this->ticket->customer->email;
						break;

					case 'assignee':
						$assinee = $this->ticket->assigned_agent;
						foreach ( $assinee as $agent ) {
							if (
								$agent->is_agentgroup ||
								! $agent->is_active
							) {
								continue;
							}
							$to[] = $agent->customer->email;
						}
						break;

					case 'add-recipients':
						$additional_recipients = $this->ticket->add_recipients ? array_map( 'trim', $this->ticket->add_recipients ) : array();
						foreach ( $additional_recipients as $email ) {
							if ( ! filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
								continue;
							}
							$to[] = $email;
						}
						break;

					case 'current-user':
						$to[] = $current_user->customer->email;
						break;

					default:
						$to = apply_filters( 'wpsc_en_get_to_addresses', $to, $recipient, $this );
				}
			}

			// agent roles.
			$agent_roles = $et['to']['agent-roles'];
			foreach ( $agent_roles as $role_id ) {
				$agents = WPSC_Agent::get_by_role( $role_id );
				foreach ( $agents as $agent ) {
					if ( ! $agent->is_active ) {
						continue;
					}
					$to[] = $agent->customer->email;
				}
			}

			// custom.
			$custom = $et['to']['custom'] ? array_map( 'trim', $et['to']['custom'] ) : array();
			foreach ( $custom as $email ) {
				if ( ! filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
					continue;
				}
				$to[] = $email;
			}

			if ( ! in_array( $et['event'], WPSC_EN_Settings_TN::$ignore_current_user ) && ! in_array( 'current-user', $general_recipients ) ) {
				$to = array_diff( $to, array( $current_user->customer->email ) );
			}

			$to = apply_filters( 'wpsc_en_to_addresses', $to, $this );
			$to = array_diff( $to, self::$block_emails );
			$this->to = array_unique( $to );
		}

		/**
		 * Get "cc" email addresses
		 *
		 * @return void
		 */
		public function set_cc_addresses() {

			$current_user = WPSC_Current_User::$current_user;
			$et           = $this->template;
			$cc           = array();
			$gs = get_option( 'wpsc-en-general' );

			// general recipients.
			$general_recipients = $et['cc']['general-recipients'];
			foreach ( $general_recipients as $recipient ) {

				switch ( $recipient ) {

					case 'customer':
						$cc[] = $this->ticket->customer->email;
						break;

					case 'assignee':
						$assinee = $this->ticket->assigned_agent;
						foreach ( $assinee as $agent ) {
							if (
								$agent->is_agentgroup ||
								! $agent->is_active
							) {
								continue;
							}
							$cc[] = $agent->customer->email;
						}
						break;

					case 'add-recipients':
						$additional_recipients = $this->ticket->add_recipients ? array_map( 'trim', $this->ticket->add_recipients ) : array();
						foreach ( $additional_recipients as $email ) {
							if ( ! filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
								continue;
							}
							$cc[] = $email;
						}
						break;

					case 'current-user':
						$cc[] = $current_user->customer->email;
						break;

					default:
						$cc = apply_filters( 'wpsc_en_get_cc_addresses', $cc, $recipient, $this );
				}
			}

			// agent roles.
			$agent_roles = $et['cc']['agent-roles'];
			foreach ( $agent_roles as $role_id ) {
				$agents = WPSC_Agent::get_by_role( $role_id );
				foreach ( $agents as $agent ) {
					if ( ! $agent->is_active ) {
						continue;
					}
					$cc[] = $agent->customer->email;
				}
			}

			// custom.
			$custom = $et['cc']['custom'] ? array_map( 'trim', $et['cc']['custom'] ) : array();
			foreach ( $custom as $email ) {
				if ( ! filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
					continue;
				}
				$cc[] = $email;
			}

			if ( ! in_array( $et['event'], WPSC_EN_Settings_TN::$ignore_current_user ) && ! in_array( 'current-user', $general_recipients ) ) {
				$cc = array_diff( $cc, array( $current_user->customer->email ) );
			}

			$cc = apply_filters( 'wpsc_en_cc_addresses', $cc, $this );
			$cc = array_diff( $cc, self::$block_emails );
			$this->cc = array_unique( $cc );
		}

		/**
		 * Get "bcc" email addresses
		 *
		 * @return void
		 */
		public function set_bcc_addresses() {

			$current_user = WPSC_Current_User::$current_user;
			$et           = $this->template;
			$bcc          = array();
			$gs = get_option( 'wpsc-en-general' );

			// general recipients.
			$general_recipients = $et['bcc']['general-recipients'];
			foreach ( $general_recipients as $recipient ) {

				switch ( $recipient ) {

					case 'customer':
						$bcc[] = $this->ticket->customer->email;
						break;

					case 'assignee':
						$assinee = $this->ticket->assigned_agent;
						foreach ( $assinee as $agent ) {
							if (
								$agent->is_agentgroup ||
								! $agent->is_active
							) {
								continue;
							}
							$bcc[] = $agent->customer->email;
						}
						break;

					case 'add-recipients':
						$additional_recipients = $this->ticket->add_recipients ? array_map( 'trim', $this->ticket->add_recipients ) : array();
						foreach ( $additional_recipients as $email ) {
							if ( ! filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
								continue;
							}
							$bcc[] = $email;
						}
						break;

					case 'current-user':
						$bcc[] = $current_user->customer->email;
						break;

					default:
						$bcc = apply_filters( 'wpsc_en_get_bcc_addresses', $bcc, $recipient, $this );
				}
			}

			// agent roles.
			$agent_roles = $et['bcc']['agent-roles'];
			foreach ( $agent_roles as $role_id ) {
				$agents = WPSC_Agent::get_by_role( $role_id );
				foreach ( $agents as $agent ) {
					if ( ! $agent->is_active ) {
						continue;
					}
					$bcc[] = $agent->customer->email;
				}
			}

			// custom.
			$custom = $et['bcc']['custom'] ? array_map( 'trim', $et['bcc']['custom'] ) : array();
			foreach ( $custom as $email ) {
				if ( ! filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
					continue;
				}
				$bcc[] = $email;
			}

			if ( ! in_array( $et['event'], WPSC_EN_Settings_TN::$ignore_current_user ) && ! in_array( 'current-user', $general_recipients ) ) {
				$bcc = array_diff( $bcc, array( $current_user->customer->email ) );
			}

			$bcc = apply_filters( 'wpsc_en_bcc_addresses', $bcc, $this );
			$bcc = array_diff( $bcc, self::$block_emails );
			$this->bcc = array_unique( $bcc );
		}
	}
endif;

WPSC_Email_Notifications::init();
