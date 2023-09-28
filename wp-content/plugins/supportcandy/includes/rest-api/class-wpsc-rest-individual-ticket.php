<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_REST_Individual_Ticket' ) ) :

	final class WPSC_REST_Individual_Ticket {

		/**
		 * Array of ticket slugs which we do not want to expose to REST API
		 *
		 * @var array
		 */
		public static $prevent_data = array();

		/**
		 * Initialize this class
		 *
		 * @return void
		 */
		public static function init() {

			add_action( 'wpsc_rest_load_ticket_prevent_data', array( __CLASS__, 'load_prevent_data' ) );
		}

		/**
		 * Load prevent data
		 *
		 * @return void
		 */
		public static function load_prevent_data() {

			$current_user = WPSC_Current_User::$current_user;
			$data = ! $current_user->is_agent ? array( 'ip_address', 'source', 'browser', 'os', 'user_type' ) : array();
			self::$prevent_data = apply_filters( 'wpsc_rest_prevent_ticket_data', $data );
		}

		/**
		 * Create new ticket
		 *
		 * @param WP_REST_Request $request - request object.
		 * @return WP_Error|WP_REST_Response
		 */
		public static function create( $request ) {

			$current_user = WPSC_Current_User::$current_user;
			$gs           = get_option( 'wpsc-gs-general' );
			$advanced     = get_option( 'wpsc-ms-advanced-settings' );

			if ( ! (
				( ! $current_user->user->ID && in_array( 'guest', $gs['allow-create-ticket'] ) ) ||
				( $current_user->is_agent && in_array( $current_user->agent->role, $gs['allow-create-ticket'] ) ) ||
				( ! $current_user->is_agent && $current_user->user->ID && in_array( 'registered-user', $gs['allow-create-ticket'] ) )
			) ) {
				return new WP_REST_Response( new WP_Error( 'unauthorized', 'You are not authorized to create ticket!' ), 401 );
			}

			// group by custom field type.
			$cfs = array();
			foreach ( WPSC_Custom_Field::$custom_fields as $cf ) {
				$cfs[ $cf->type::$slug ][] = $cf;
			}

			// collect all data to create ticket.
			$data = apply_filters(
				'wpsc_rest_create_ticket',
				array( 'errors' => new WP_Error() ),
				$request,
				$cfs
			);

			// check whether there are any errors.
			if ( $data['errors']->has_errors() ) {
				return new WP_REST_Response( $data['errors'], 400 );
			} else {
				unset( $data['errors'] );
			}

			// Seperate description from $data.
			$description = $data['description'];
			unset( $data['description'] );

			// Seperate description attachments from $data.
			$description_attachments = $data['description_attachments'];
			unset( $data['description_attachments'] );

			$data['last_reply_on'] = ( new DateTime() )->format( 'Y-m-d H:i:s' );

			// insert ticket data.
			$ticket = WPSC_Ticket::insert( $data );

			if ( ! $ticket ) {
				return new WP_REST_Response( new WP_Error( 'something_wrong', 'Something went wrong!' ), 400 );
			}

			$thread_customer = $current_user->is_agent && $current_user->customer->id != $ticket->customer->id && $advanced['raised-by-user'] == 'agent' ? $current_user->customer : $ticket->customer;
			$ticket->last_reply_by = $thread_customer->id;
			$ticket->save();

			// replace macros only when current user is an agent.
			$description = $current_user->is_agent ? WPSC_Macros::replace( $description, $ticket ) : $description;

			// set signature if agent.
			$signature = $current_user->is_agent && $current_user->customer->email == $ticket->customer->email ? $current_user->agent->get_signature() : '';
			if ( $signature ) {
				$description .= $signature;
			}

			// Create report thread.
			$thread = WPSC_Thread::insert(
				array(
					'ticket'      => $ticket->id,
					'customer'    => $thread_customer->id,
					'type'        => 'report',
					'body'        => $description,
					'attachments' => $description_attachments,
					'ip_address'  => $ticket->ip_address,
					'source'      => $ticket->source,
					'os'          => $ticket->os,
					'browser'     => $ticket->browser,
				)
			);

			do_action( 'wpsc_create_new_ticket', $ticket );

			$request = new WP_REST_Request( 'GET', '/supportcandy/v2/tickets/' . $ticket->id );
			return rest_do_request( $request );
		}

		/**
		 * Get individual ticket
		 *
		 * @param WP_REST_Request $request - request object.
		 * @return WP_Error|WP_REST_Response
		 */
		public static function get( $request ) {

			$current_user = WPSC_Current_User::$current_user;

			// triggers loading of prevent ticket data property.
			do_action( 'wpsc_rest_load_ticket_prevent_data' );

			$ticket = new WPSC_Ticket( $request->get_param( 'id' ) );
			if ( ! $ticket->id ) {
				return new WP_REST_Response( new WP_Error( 'unauthorized', 'You are not authorized!' ), 401 );
			}

			WPSC_Individual_Ticket::$ticket = $ticket;
			if ( ! (
				( $current_user->is_agent && WPSC_Individual_Ticket::has_ticket_cap( 'view' ) ) ||
				WPSC_Individual_Ticket::is_customer()
			) ) {
				return new WP_REST_Response( new WP_Error( 'unauthorized', 'You are not authorized!' ), 401 );
			}

			$data = self::modify_response( $ticket->to_array() );
			return new WP_REST_Response( $data, 200 );
		}

		/**
		 * Update/modify the ticket
		 *
		 * @param WP_REST_Request $request - request object.
		 * @return WP_Error|WP_REST_Response
		 */
		public static function update( $request ) {

			$current_user = WPSC_Current_User::$current_user;

			$ticket = new WPSC_Ticket( $request->get_param( 'id' ) );
			if ( ! $ticket->id || ! $ticket->is_active ) {
				return new WP_REST_Response( new WP_Error( 'unauthorized', 'You are not authorized!' ), 401 );
			}

			// ticket object clone to compare.
			$ticket_clone = clone $ticket;

			WPSC_Individual_Ticket::$ticket = $ticket;

			foreach ( $request->get_params() as $slug => $val ) {

				if ( in_array( $slug, array( 'id', 'description', 'description_attachments' ) ) ) {
					continue;
				}

				$cf = WPSC_Custom_Field::get_cf_by_slug( $slug );
				if ( ! $cf || ! in_array( $cf->field, array( 'ticket', 'agentonly' ) ) ) {
					continue;
				}

				// ticket fields.
				if ( $cf->field == 'ticket' ) {

					// subject.
					if ( $slug == 'subject' && $current_user->is_agent && WPSC_Individual_Ticket::has_ticket_cap( 'ctf' ) ) {

						$prev = $ticket->subject;
						$new = sanitize_text_field( $val );
						if ( ! $new || $prev == $new ) {
							continue;
						}

						$ticket->subject = $new;
						do_action( 'wpsc_change_ticket_subject', $ticket, $prev, $new, $current_user->customer->id );
					}

					// status.
					if ( $slug == 'status' && $current_user->is_agent && WPSC_Individual_Ticket::has_ticket_cap( 'cs' ) ) {

						$status = new WPSC_Status( intval( $val ) );
						if ( ! $status->id || $ticket->status == $status ) {
							continue;
						}

						WPSC_Individual_Ticket::change_status( $ticket->status->id, $status->id, $current_user->customer->id );
					}

					// priority.
					if ( $slug == 'priority' && $current_user->is_agent && WPSC_Individual_Ticket::has_ticket_cap( 'cs' ) ) {

						$priority = new WPSC_Priority( intval( $val ) );
						if ( ! $priority->id || $ticket->priority == $priority ) {
							continue;
						}

						WPSC_Individual_Ticket::change_priority( $ticket->priority->id, $priority->id, $current_user->customer->id );
					}

					// category.
					if ( $slug == 'category' && $current_user->is_agent && WPSC_Individual_Ticket::has_ticket_cap( 'cs' ) ) {

						$category = new WPSC_Category( intval( $val ) );
						if ( ! $category->id || $ticket->category == $category ) {
							continue;
						}

						WPSC_Individual_Ticket::change_category( $ticket->category->id, $category->id, $current_user->customer->id );
					}

					// customer.
					if ( $slug == 'customer' && $current_user->is_agent && WPSC_Individual_Ticket::has_ticket_cap( 'crb' ) ) {

						$customer = new WPSC_Customer( intval( $val ) );
						if ( ! $customer->id || $ticket->customer == $customer ) {
							continue;
						}

						WPSC_Individual_Ticket::change_raised_by( $ticket->customer, $customer, $current_user->customer->id );
					}

					// assignee.
					if ( $slug == 'assigned_agent' && $current_user->is_agent && WPSC_Individual_Ticket::has_ticket_cap( 'aa' ) ) {

						$new = array_unique(
							array_filter(
								array_map(
									function( $id ) {
										$agent = new WPSC_Agent( intval( $id ) );
										return $agent->id ? $agent->id : false;
									},
									explode( ',', sanitize_text_field( $val ) )
								)
							)
						);

						$prev = array_unique(
							array_filter(
								array_map(
									fn( $agent ) => $agent->id,
									$ticket->assigned_agent
								)
							)
						);

						if ( ! $new || ! ( array_diff( $prev, $new ) || array_diff( $new, $prev ) ) ) {
							continue;
						}

						$new = array_map(
							fn( $id ) => new WPSC_Agent( $id ),
							$new
						);

						WPSC_Individual_Ticket::change_assignee( $ticket->assigned_agent, $new, $current_user->customer->id );
					}

					// additional recipients.
					if (
						$slug == 'add_recipients' &&
						(
							( $current_user->is_agent && WPSC_Individual_Ticket::has_ticket_cap( 'ar' ) ) ||
							( ! $current_user->is_agent && $ticket->customer->id == $current_user->customer->id )
						)
					) {

						$recipients = array_unique( array_filter( array_map( 'sanitize_email', explode( ',', $val ) ) ) );
						$prev = $ticket->add_recipients;
						if ( ! $recipients || ! ( array_diff( $prev, $recipients ) || array_diff( $recipients, $prev ) ) ) {
							continue;
						}

						$ticket->add_recipients = $recipients;
						do_action( 'wpsc_change_ticket_add_recipients', $ticket, $prev, $recipients, $current_user->customer->id );
					}

					// ticket fields.
					if (
						class_exists( $cf->type ) &&
						$current_user->is_agent &&
						WPSC_Individual_Ticket::has_ticket_cap( 'ctf' ) &&
						! $cf->type::$is_default &&
						! in_array( $cf->type::$slug, WPSC_ITW_Ticket_Fields::$ignore_cft )
					) {

						$cf->type::set_rest_edit_ticket_cf( $ticket, $cf, $val );
						do_action( 'wpsc_change_ticket_fields', $ticket_clone, $ticket );
					}

					// handle add-on ticket fields.
					do_action( 'wpsc_rest_update_ticket_fields', $ticket, $cf, $val );

				} else { // agentonly fields.

					if (
						class_exists( $cf->type ) &&
						$current_user->is_agent &&
						WPSC_Individual_Ticket::has_ticket_cap( 'caof' ) &&
						! $cf->type::$is_default &&
						! in_array( $cf->type::$slug, WPSC_ITW_Agentonly_Fields::$ignore_cft )
					) {

						$cf->type::set_rest_edit_ticket_cf( $ticket, $cf, $val );
						do_action( 'wpsc_change_agentonly_fields', $ticket_clone, $ticket );
					}
				}
			}

			// save the changes.
			if ( $ticket != $ticket_clone ) {

				$ticket->date_updated = new DateTime();
				$ticket->save();
			}

			$request = new WP_REST_Request( 'GET', '/supportcandy/v2/tickets/' . $ticket->id );
			return rest_do_request( $request );
		}

		/**
		 * Delete the ticket
		 *
		 * @param WP_REST_Request $request - request object.
		 * @return WP_Error|WP_REST_Response
		 */
		public static function delete( $request ) {

			$current_user = WPSC_Current_User::$current_user;
			if ( ! $current_user->is_agent ) {
				return new WP_REST_Response( new WP_Error( 'unauthorized', 'You are not authorized to delete ticket!' ), 401 );
			}

			$ticket = new WPSC_Ticket( $request->get_param( 'id' ) );
			if ( ! $ticket->id ) {
				return new WP_REST_Response( new WP_Error( 'bad_request', 'Bad request!' ), 400 );
			}

			WPSC_Individual_Ticket::$ticket = $ticket;
			if ( ! WPSC_Individual_Ticket::$ticket->is_active || ! WPSC_Individual_Ticket::has_ticket_cap( 'dtt' ) ) {
				return new WP_REST_Response( new WP_Error( 'unauthorized', 'You are not authorized to delete this ticket!' ), 401 );
			}

			WPSC_Individual_Ticket::delete_ticket();
			return new WP_REST_Response( array( 'success' => true ), 200 );
		}

		/**
		 * Get ticket threads
		 *
		 * @param WP_REST_Request $request - request object.
		 * @return WP_Error|WP_REST_Response
		 */
		public static function get_threads( $request ) {

			$current_user = WPSC_Current_User::$current_user;

			$ticket = new WPSC_Ticket( $request->get_param( 'id' ) );
			if ( ! $ticket->id || ! $ticket->is_active ) {
				return new WP_REST_Response( new WP_Error( 'unauthorized', 'You are not authorized!' ), 401 );
			}

			WPSC_Individual_Ticket::$ticket = $ticket;
			if ( ! (
				( $current_user->is_agent && WPSC_Individual_Ticket::has_ticket_cap( 'view' ) ) ||
				( WPSC_Individual_Ticket::is_customer() )
			) ) {
				return new WP_REST_Response( new WP_Error( 'unauthorized', 'You are not authorized!' ), 401 );
			}

			$filters = array(
				'page_no'        => $request->get_param( 'page' ),
				'items_per_page' => $request->get_param( 'per_page' ),
				'order'          => $request->get_param( 'order' ),
				'meta_query'     => array(
					'relation' => 'AND',
					array(
						'slug'    => 'ticket',
						'compare' => '=',
						'val'     => $ticket->id,
					),
					array(
						'slug'    => 'is_active',
						'compare' => '=',
						'val'     => 1,
					),
				),
			);

			$type = array(
				'slug'    => 'type',
				'compare' => 'IN',
				'val'     => array( 'report', 'reply' ),
			);

			// private note.
			if ( $current_user->is_agent && WPSC_Individual_Ticket::has_ticket_cap( 'pn' ) ) {
				$type['val'][] = 'note';
			}

			// logs.
			if ( $current_user->is_agent && WPSC_Individual_Ticket::has_ticket_cap( 'vl' ) ) {
				$type['val'][] = 'log';
			}

			$filters['meta_query'][] = $type;
			$response = WPSC_Thread::find( $filters, false );

			return new WP_REST_Response( self::modify_thread_response( $response, $ticket, $current_user ), 200 );
		}

		/**
		 * Add new thread
		 *
		 * @param WP_REST_Request $request - request object.
		 * @return WP_Error|WP_REST_Response
		 */
		public static function add_new_thread( $request ) {

			$current_user = WPSC_Current_User::$current_user;

			$ticket = new WPSC_Ticket( $request->get_param( 'id' ) );
			if ( ! $ticket->id || ! $ticket->is_active ) {
				return new WP_REST_Response( new WP_Error( 'unauthorized', 'You are not authorized!' ), 401 );
			}

			WPSC_Individual_Ticket::$ticket = $ticket;
			$type = $request->get_param( 'type' );
			$body = $request->get_param( 'body' );
			$source = $request->get_param( 'source' );
			$attachments = $request->get_param( 'attachments' );

			if ( ! (
				( $current_user->is_agent && $type == 'reply' && WPSC_Individual_Ticket::has_ticket_cap( 'reply' ) ) ||
				( $current_user->is_agent && $type == 'note' && WPSC_Individual_Ticket::has_ticket_cap( 'pn' ) ) ||
				( WPSC_Individual_Ticket::is_customer() && $type == 'reply' )
			) ) {
				return new WP_REST_Response( new WP_Error( 'unauthorized', 'You are not authorized!' ), 401 );
			}

			// submit thread.
			$thread = WPSC_Thread::insert(
				array(
					'ticket'      => $ticket->id,
					'customer'    => $current_user->customer->id,
					'type'        => $type,
					'body'        => $body,
					'attachments' => implode( '|', $attachments ),
					'ip_address'  => WPSC_DF_IP_Address::get_current_user_ip(),
					'source'      => $source,
				)
			);

			// save attachments.
			foreach ( $attachments as $id ) {

				$attachment = new WPSC_Attachment( $id );
				$attachment->is_active = 1;
				$attachment->source    = $type;
				$attachment->source_id = $thread->id;
				$attachment->ticket_id = $ticket->id;
				$attachment->save();
			}

			$ticket->date_updated  = new DateTime();
			$ticket->last_reply_on = new DateTime();
			$ticket->last_reply_by = $current_user->customer->id;
			$ticket->save();

			if ( $type == 'reply' ) {
				do_action( 'wpsc_post_reply', $thread );
			} else {
				do_action( 'wpsc_submit_note', $thread );
			}

			$response = self::modify_thread_response( array( 'results' => array( $thread->to_array() ) ), $ticket, $current_user );
			return new WP_REST_Response( $response['results'][0], 200 );
		}

		/**
		 * Modify ticket response data appropreate for client side
		 *
		 * @param array $ticket - ticket response array.
		 * @return array
		 */
		public static function modify_response( $ticket ) {

			$current_user = WPSC_Current_User::$current_user;

			foreach ( $ticket as $slug => $value ) {

				// remove prevent ticket data.
				if ( in_array( $slug, self::$prevent_data ) ) {
					unset( $ticket[ $slug ] );
					continue;
				}

				$cf = WPSC_Custom_Field::get_cf_by_slug( $slug );

				// ignore if no custom field type class is loaded.
				if ( ! $cf ) {
					unset( $ticket[ $slug ] );
					continue;
				}

				// ignore agentonly fields for non-agent.
				if ( ! $current_user->is_agent && $cf->field == 'agentonly' ) {
					unset( $ticket[ $slug ] );
					continue;
				}

				// convert has multiple values to array.
				if ( WPSC_Ticket::$schema[ $slug ]['has_multiple_val'] ) {
					if ( $value ) {
						$ticket[ $slug ] = array_filter(
							array_map(
								fn( $val) => is_numeric( $val ) ? intval( $val ) : $val,
								explode( '|', $value )
							)
						);
					} else {
						$ticket[ $slug ] = array();
					}
					continue;
				}

				// empty date value.
				if ( $value == '0000-00-00 00:00:00' ) {
					$ticket[ $slug ] = '';
				}

				// cast numeric fields into integer.
				if ( is_numeric( $value ) ) {
					$ticket[ $slug ] = intval( $value );
				}
			}

			return apply_filters( 'wpsc_rest_modify_ticket_response', $ticket );
		}

		/**
		 * Modify thread response
		 *
		 * @param array             $response - response array.
		 * @param WPSC_Ticket       $ticket - ticket object.
		 * @param WPSC_Current_User $current_user - current user object.
		 * @return array
		 */
		public static function modify_thread_response( $response, $ticket, $current_user ) {

			if ( $response['results'] ) {

				$log_items = apply_filters(
					'wpsc_rest_allowed_thread_log_items',
					array( 'id', 'customer', 'type', 'body', 'date_created' )
				);

				$reply_items = apply_filters(
					'wpsc_rest_allowed_thread_reply_items',
					array( 'id', 'customer', 'type', 'body', 'attachments', 'seen', 'date_created' )
				);

				$response['results'] = array_map(
					function( $thread ) use ( $log_items, $reply_items ) {
						$items = $thread['type'] == 'log' ? $log_items : $reply_items;
						$temp = array();
						foreach ( $items as $item ) {
							switch ( $item ) {
								case 'body':
									if ( $thread['type'] == 'log' ) {
										$body = json_decode( $thread[ $item ], true );
										$body['prev'] = strval( $body['prev'] );
										$body['prev'] = $body['prev'] && is_array( $body['prev'] ) ? implode( ',', $body['prev'] ) : $body['prev'];
										$body['prev'] = $body['prev'] && is_numeric( strpos( '|', $body['prev'] ) ) ? str_replace( '|', ',', $body['prev'] ) : $body['prev'];
										$body['new'] = strval( $body['new'] );
										$body['new'] = $body['new'] && is_array( $body['new'] ) ? implode( ',', $body['new'] ) : $body['new'];
										$body['new'] = $body['new'] && is_numeric( strpos( '|', $body['new'] ) ) ? str_replace( '|', ',', $body['new'] ) : $body['new'];
										$temp[ $item ] = $body;
									} else {
										$temp[ $item ] = $thread[ $item ];
									}
									break;
								case 'attachments':
									$temp[ $item ] = str_replace( '|', ',', $thread[ $item ] );
									break;
								default:
									$temp[ $item ] = $thread[ $item ];
							}
						}
						return $temp;
					},
					$response['results']
				);
			}

			return $response;
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

			$error = new WP_Error( 'invalid_id', 'Invalid ticket id', array( 'status' => 400 ) );
			$ticket = new WPSC_Ticket( $param );
			return $ticket->id ? true : $error;
		}
	}
endif;

WPSC_REST_Individual_Ticket::init();
