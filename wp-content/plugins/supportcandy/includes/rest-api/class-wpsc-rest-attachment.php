<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_REST_Attachment' ) ) :

	final class WPSC_REST_Attachment {

		/**
		 * Initialize this class
		 *
		 * @return void
		 */
		public static function init() {

			add_action( 'init', array( __CLASS__, 'check_download_file' ), 99 );
			add_action( 'wpsc_rest_register_routes', array( __CLASS__, 'register_routes' ) );
		}

		/**
		 * Register routes
		 *
		 * @return void
		 */
		public static function register_routes() {

			// list statuses.
			register_rest_route(
				'supportcandy/v2',
				'/attachments',
				array(
					'methods'             => 'POST',
					'callback'            => array( __CLASS__, 'new_attachment' ),
					'permission_callback' => 'is_user_logged_in',
				),
			);

			// list individual status.
			register_rest_route(
				'supportcandy/v2',
				'/attachments/(?P<id>\d+)',
				array(
					'methods'             => 'GET',
					'callback'            => array( __CLASS__, 'get_individual_attachment' ),
					'args'                => array(
						'id' => array(
							'validate_callback' => array( __CLASS__, 'validate_id' ),
						),
					),
					'permission_callback' => 'is_user_logged_in',
				),
			);
		}

		/**
		 * Create new attachment
		 *
		 * @param WP_REST_Request $request - request object.
		 * @return WP_Error|WP_REST_Response
		 */
		public static function new_attachment( $request ) {

			$file_parameters = $request->get_file_params();
			if ( ! isset( $file_parameters['file'] ) ) {
				return new WP_Error( 'rest_missing_callback_param', 'Missing parameter(s): file', array( 'status' => 400 ) );
			}

			$file = $file_parameters['file'];
			$file_settings = get_option( 'wpsc-gs-file-attachments' );
			$filename      = time() . '_' . sanitize_file_name( $file['name'] );
			$extension     = strtolower( pathinfo( $filename, PATHINFO_EXTENSION ) );
			$today         = new DateTime();
			$upload_dir    = wp_upload_dir();

			// Allowed file extension.
			$allowed_file_extensions = explode( ',', $file_settings['allowed-file-extensions'] );
			$allowed_file_extensions = array_map( 'trim', $allowed_file_extensions );
			$allowed_file_extensions = array_map( 'strtolower', $allowed_file_extensions );
			if ( ! ( in_array( $extension, $allowed_file_extensions ) ) ) {
				wp_send_json_error( 'File extension not allowed!', 400 );
			}

			// Allowed file size.
			$allowed_file_size = intval( $file_settings['attachments-max-filesize'] ) * 1000000;
			if ( ! ( isset( $file['size'] ) && $file['size'] <= $allowed_file_size ) ) {
				wp_send_json_error( 'File size exceeds allowed limit!', 400 );
			}

			// Init attachment data.
			$data = array(
				'name'         => sanitize_file_name( $file['name'] ),
				'date_created' => $today->format( 'Y-m-d H:i:s' ),
			);

			// Check for image type. Add a ".txt" extension to non-image file to prevent executing uploaded files on server.
			$img_extensions = array( 'png', 'jpeg', 'jpg', 'bmp', 'pdf', 'gif' );
			if ( ! in_array( $extension, $img_extensions ) ) {
				$filename .= '.txt';
			} else {
				$data['is_image'] = 1;
			}

			// File path.
			$file_path = $upload_dir['basedir'] . '/wpsc/' . $today->format( 'Y' ) . '/' . $today->format( 'm' );
			if ( ! file_exists( $file_path ) ) {
				mkdir( $file_path, 0755, true );
			}
			$file_path .= '/' . $filename;

			$filepath_short = '/wpsc/' . $today->format( 'Y' ) . '/' . $today->format( 'm' ) . '/' . $filename;
			$data['file_path'] = $filepath_short;

			// Insert record in database.
			if ( move_uploaded_file( $file['tmp_name'], $file_path ) ) {

				$attachment = WPSC_Attachment::insert( $data );
				if ( ! $attachment->id ) {
					wp_send_json_error( 'Something went wrong, attachment record not created!', 500 );
				}
				$data = array(
					'id'   => intval( $attachment->id ),
					'name' => $attachment->name,
				);
				return new WP_REST_Response( $data, 200 );
			}

			wp_send_json_error( 'Something went wrong, file not saved!', 500 );
		}

		/**
		 * Single attachment
		 *
		 * @param WP_REST_Request $request - request object.
		 * @return WP_Error|WP_REST_Response
		 */
		public static function get_individual_attachment( $request ) {

			$current_user = WPSC_Current_User::$current_user;
			$attachment = new WPSC_Attachment( $request->get_param( 'id' ) );
			$url = home_url( '/' ) . '?wpsc_attachment=' . $attachment->id . '&user=' . $current_user->user->ID . '&auth_code=' . $current_user->get_attachment_auth();
			$data = array(
				'id'   => intval( $attachment->id ),
				'name' => $attachment->name,
				'url'  => $url,
			);
			return new WP_REST_Response( $data, 200 );
		}

		/**
		 * Validate id
		 *
		 * @param string          $param - parameter value.
		 * @param WP_REST_Request $request - request object.
		 * @param string          $key - filter key.
		 * @return boolean
		 */
		public static function validate_id( $param, $request, $key ) {

			$current_user = WPSC_Current_User::$current_user;
			$attachment = new WPSC_Attachment( $param );
			if ( ! $attachment->id || ! $attachment->is_active ) {
				return new WP_Error( 'invalid_id', 'Invalid attachment id', array( 'status' => 400 ) );
			}

			switch ( $attachment->source ) {

				case 'cf':
					if ( in_array( $cf->field, array( 'ticket', 'agentonly' ) ) ) { // ticket field.

						$ticket = new WPSC_Ticket( $attachment->ticket_id );
						if ( ! $ticket->id ) {
							return new WP_Error( 'invalid_id', 'Invalid attachment id', array( 'status' => 400 ) );
						}

						WPSC_Individual_Ticket::$ticket = $ticket;
						if ( ! (
							( $current_user->is_agent && WPSC_Individual_Ticket::has_ticket_cap( 'view' ) ) ||
							WPSC_Individual_Ticket::is_customer()
						) ) {
							return new WP_Error( 'unauthorized', 'You are not authorized to access this attachment!', array( 'status' => 401 ) );
						}
					} else { // customer field.

						$customer       = new WPSC_Customer( intval( $attachment->customer_id ) );
						$ticket_widgets = get_option( 'wpsc-ticket-widget', array() );
						$raised_by      = $ticket_widgets['raised-by'];

						if ( ! (
							$current_user->customer->id == $customer->id ||
							(
								$current_user->is_agent &&
								in_array( $current_user->agent->role, $raised_by['allowed-agent-roles'] )
							) )
						) {
							return new WP_Error( 'unauthorized', 'You are not authorized to access this attachment!', array( 'status' => 401 ) );
						}
					}
					break;

				case 'reply':
				case 'report':
					$ticket = new WPSC_Ticket( $attachment->ticket_id );
					if ( ! $ticket->id ) {
						return new WP_Error( 'invalid_id', 'Invalid attachment id', array( 'status' => 400 ) );
					}

					WPSC_Individual_Ticket::$ticket = $ticket;
					if ( ! (
						( $current_user->is_agent && WPSC_Individual_Ticket::has_ticket_cap( 'view' ) ) ||
						WPSC_Individual_Ticket::is_customer()
					) ) {
						return new WP_Error( 'unauthorized', 'You are not authorized to access this attachment!', array( 'status' => 401 ) );
					}
					break;

				case 'note':
					$ticket = new WPSC_Ticket( $attachment->ticket_id );
					if ( ! $ticket->id ) {
						return new WP_Error( 'invalid_id', 'Invalid attachment id', array( 'status' => 400 ) );
					}

					WPSC_Individual_Ticket::$ticket = $ticket;
					if ( ! (
						$current_user->is_agent && WPSC_Individual_Ticket::has_ticket_cap( 'pn' )
					) ) {
						return new WP_Error( 'unauthorized', 'You are not authorized to access this attachment!', array( 'status' => 401 ) );
					}
					break;
			}

			return true;
		}

		/**
		 * Check for rest attachment
		 *
		 * @return void
		 */
		public static function check_download_file() {

			// phpcs:disable
			if ( isset( $_REQUEST['wpsc_attachment'] ) && isset( $_REQUEST['user'] ) && isset( $_REQUEST['auth_code'] ) ) {
				$user = get_user_by( 'id', intval( $_REQUEST['user'] ) );
				if ( $user ) {
					$current_user = WPSC_Current_User::change_current_user( $user->user_email );
					if ( $_REQUEST['auth_code'] != $current_user->get_attachment_auth() ) {
						wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
					}
				}
			}
			// phpcs:enable
		}
	}
endif;

WPSC_REST_Attachment::init();
