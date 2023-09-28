<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_REST_Tickets' ) ) :

	final class WPSC_REST_Tickets {

		/**
		 * Initialize this class
		 *
		 * @return void
		 */
		public static function init() {

			add_action( 'wpsc_rest_register_routes', array( __CLASS__, 'register_routes' ) );
		}

		/**
		 * Register routes
		 *
		 * @return void
		 */
		public static function register_routes() {

			// create new ticket.
			register_rest_route(
				'supportcandy/v2',
				'/tickets',
				array(
					'methods'             => 'POST',
					'callback'            => array( 'WPSC_REST_Individual_Ticket', 'create' ),
					'permission_callback' => 'is_user_logged_in',
				)
			);

			// list tickets.
			register_rest_route(
				'supportcandy/v2',
				'/tickets',
				array(
					'methods'             => 'GET',
					'callback'            => array( __CLASS__, 'get_tickets' ),
					'args'                => array(
						'per_page' => array(
							'default'           => 20,
							'validate_callback' => array( 'WPSC_REST_API', 'validate_integer_value' ),
						),
						'page'     => array(
							'default'           => 1,
							'validate_callback' => array( 'WPSC_REST_API', 'validate_integer_value' ),
						),
						'filter'   => array(
							'default'           => 'all',
							'validate_callback' => array( __CLASS__, 'validate_ticket_filter' ),
						),
						'orderby'  => array(
							'default'           => 'date_updated',
							'validate_callback' => array( __CLASS__, 'validate_filter_orderby' ),
						),
						'order'    => array(
							'default'           => 'DESC',
							'validate_callback' => function( $param, $request, $key ) {
								return in_array( strtoupper( $param ), array( 'ASC', 'DESC' ) );
							},
							'sanitize_callback' => function( $param, $request, $key ) {
								return strtoupper( $param );
							},
						),
						'search'   => array(
							'default'           => '',
							'sanitize_callback' => function( $param, $request, $key ) {
								return sanitize_text_field( $param );
							},
						),
					),
					'permission_callback' => 'is_user_logged_in',
				)
			);

			// get individual ticket.
			register_rest_route(
				'supportcandy/v2',
				'/tickets/(?P<id>\d+)',
				array(
					'methods'             => 'GET',
					'callback'            => array( 'WPSC_REST_Individual_Ticket', 'get' ),
					'args'                => array(
						'id' => array(
							'validate_callback' => array( 'WPSC_REST_Individual_Ticket', 'validate_id' ),
						),
					),
					'permission_callback' => 'is_user_logged_in',
				),
			);

			// update individual ticket.
			register_rest_route(
				'supportcandy/v2',
				'/tickets/(?P<id>\d+)/update-fields',
				array(
					'methods'             => 'POST',
					'callback'            => array( 'WPSC_REST_Individual_Ticket', 'update' ),
					'args'                => array(
						'id' => array(
							'validate_callback' => array( 'WPSC_REST_Individual_Ticket', 'validate_id' ),
						),
					),
					'permission_callback' => 'is_user_logged_in',
				),
			);

			// delete individual ticket.
			register_rest_route(
				'supportcandy/v2',
				'/tickets/(?P<id>\d+)',
				array(
					'methods'             => 'DELETE',
					'callback'            => array( 'WPSC_REST_Individual_Ticket', 'delete' ),
					'args'                => array(
						'id' => array(
							'validate_callback' => array( 'WPSC_REST_Individual_Ticket', 'validate_id' ),
						),
					),
					'permission_callback' => 'is_user_logged_in',
				),
			);

			// thread listing.
			register_rest_route(
				'supportcandy/v2',
				'/tickets/(?P<id>\d+)/threads',
				array(
					'methods'             => 'GET',
					'callback'            => array( 'WPSC_REST_Individual_Ticket', 'get_threads' ),
					'args'                => array(
						'per_page' => array(
							'default'           => 20,
							'validate_callback' => array( 'WPSC_REST_API', 'validate_integer_value' ),
						),
						'page'     => array(
							'default'           => 1,
							'validate_callback' => array( 'WPSC_REST_API', 'validate_integer_value' ),
						),
						'order'    => array(
							'default'           => 'DESC',
							'validate_callback' => function( $param, $request, $key ) {
								return in_array( strtoupper( $param ), array( 'ASC', 'DESC' ) );
							},
							'sanitize_callback' => function( $param, $request, $key ) {
								return strtoupper( $param );
							},
						),
					),
					'permission_callback' => 'is_user_logged_in',
				)
			);

			// add new thread.
			register_rest_route(
				'supportcandy/v2',
				'/tickets/(?P<id>\d+)/threads',
				array(
					'methods'             => 'POST',
					'callback'            => array( 'WPSC_REST_Individual_Ticket', 'add_new_thread' ),
					'args'                => array(
						'type'        => array(
							'required'          => true,
							'validate_callback' => function( $param, $request, $key ) {
								return in_array( strtolower( $param ), array( 'reply', 'note' ) );
							},
							'sanitize_callback' => function( $param, $request, $key ) {
								return strtolower( $param );
							},
						),
						'body'        => array(
							'required'          => true,
							'sanitize_callback' => function( $param, $request, $key ) {
								$is_html = $param != wp_strip_all_tags( $param ) ? true : false;
								return $is_html ? wp_kses_post( $param ) : str_replace( PHP_EOL, '<br />', $param );
							},
						),
						'source'      => array( // we can use this api in some another api like slack, telegram, etc. where source is different.
							'default'           => 'rest-api',
							'sanitize_callback' => function( $param, $request, $key ) {
								return sanitize_text_field( $param );
							},
						),
						'attachments' => array(
							'default'           => array(),
							'sanitize_callback' => function( $param, $request, $key ) {
								return array_unique(
									array_filter(
										array_map(
											fn( $attachment ) => WPSC_Functions::sanitize_attachment( intval( $attachment ) ),
											explode( ',', sanitize_text_field( $param ) )
										)
									)
								);
							},
						),
					),
					'permission_callback' => 'is_user_logged_in',
				)
			);

			// ticket list filters.
			register_rest_route(
				'supportcandy/v2',
				'/tickets/filters',
				array(
					'methods'             => 'GET',
					'callback'            => array( __CLASS__, 'get_filters' ),
					'permission_callback' => 'is_user_logged_in',
				),
			);

			// ticket list orderby filters.
			register_rest_route(
				'supportcandy/v2',
				'/tickets/filters/orderby',
				array(
					'methods'             => 'GET',
					'callback'            => array( __CLASS__, 'get_orderby_fields' ),
					'permission_callback' => 'is_user_logged_in',
				),
			);

			// ticket list items.
			register_rest_route(
				'supportcandy/v2',
				'/tickets/list-items',
				array(
					'methods'             => 'GET',
					'callback'            => array( __CLASS__, 'get_list_items' ),
					'permission_callback' => 'is_user_logged_in',
				),
			);

			// ticket form fields.
			register_rest_route(
				'supportcandy/v2',
				'/tickets/form-fields',
				array(
					'methods'             => 'GET',
					'callback'            => array( __CLASS__, 'get_form_fields' ),
					'permission_callback' => 'is_user_logged_in',
				),
			);
		}

		/**
		 * Ticket list collection
		 *
		 * @param WP_REST_Request $request - request object.
		 * @return WP_Error|WP_REST_Response
		 */
		public static function get_tickets( $request ) {

			$current_user = WPSC_Current_User::$current_user;

			// triggers loading of prevent ticket data property.
			do_action( 'wpsc_rest_load_ticket_prevent_data' );

			// We need to use functions from WPSC_Ticket_List class. Initialize required static fields.
			$current_user_filters = $current_user->get_tl_filters();
			WPSC_Ticket_List::$default_filters = $current_user_filters['default'];
			WPSC_Ticket_List::$more_settings = $current_user->get_tl_default_settings();

			// initialize filters.
			$filters = array(
				'items_per_page' => $request->get_param( 'per_page' ),
				'page_no'        => $request->get_param( 'page' ),
				'orderby'        => $request->get_param( 'orderby' ),
				'order'          => $request->get_param( 'order' ),
				'search'         => $request->get_param( 'search' ),
			);

			// system query.
			$filters['system_query'] = $current_user->get_tl_system_query( $filters );

			// meta query.
			$meta_query = array( 'relation' => 'AND' );
			if ( preg_match( '/saved-(\d*)$/', $request->get_param( 'filter' ), $matches ) ) {

				// parent meta query.
				$meta_query = array_merge(
					$meta_query,
					WPSC_Ticket_List::get_parent_meta_query( $current_user_filters['saved'][ $matches[1] ]['parent-filter'] )
				);

				// filter meta query.
				$filters_str = $current_user_filters['saved'][ $matches[1] ]['filters'];
				$filters_str = str_replace( '^^', '\n', $filters_str );
				$meta_query  = array_merge( $meta_query, WPSC_Ticket_Conditions::get_meta_query( $filters_str, true ) );
			} elseif ( preg_match( '/default-(\d*)$/', $request->get_param( 'filter' ), $matches ) ) {

				// parent meta query.
				$meta_query = array_merge(
					$meta_query,
					WPSC_Ticket_List::get_parent_meta_query( $current_user_filters['default'][ $matches[1] ]['parent-filter'] )
				);

				// filter meta query.
				$filters_arr = json_decode( html_entity_decode( $current_user_filters['default'][ $matches[1] ]['filters'] ), true );
				$filters_str = $current_user_filters['saved'][ $matches[1] ]['filters'];
				$filters_str = str_replace( '^^', '\n', $filters_str );
				$meta_query  = array_merge( $meta_query, WPSC_Ticket_Conditions::get_meta_query( $filters_str, true ) );
			} else {

				$meta_query = array_merge(
					$meta_query,
					WPSC_Ticket_List::get_parent_meta_query( $request->get_param( 'filter' ) )
				);
			}

			// set meta query.
			$filters['meta_query'] = $meta_query;

			// is active.
			$filters['is_active'] = WPSC_Ticket_List::$is_active;

			// get tickets.
			$tickets = WPSC_Ticket::find( $filters, false );
			$tickets['results'] = array_map(
				fn( $ticket ) => WPSC_REST_Individual_Ticket::modify_response( $ticket ),
				$tickets['results']
			);

			return new WP_REST_Response( $tickets, 200 );
		}

		/**
		 * Ticket list filters
		 *
		 * @param WP_REST_Request $request - request object.
		 * @return WP_Error|WP_REST_Response
		 */
		public static function get_filters( $request ) {

			$current_user = WPSC_Current_User::$current_user;
			$current_user_filters = $current_user->get_tl_filters();
			$tl_default_settings = $current_user->get_tl_default_settings();
			$data = array(
				'default' => array(),
				'saved'   => array(),
			);

			// default filtes.
			foreach ( $current_user_filters['default'] as $index => $filter ) {

				if ( is_numeric( $index ) ) {

					$data['default'][ 'default-' . $index ] = array(
						'slug'    => 'default-' . $index,
						'label'   => $filter['label'],
						'orderby' => $filter['sort-by'],
						'order'   => $filter['sort-order'],
					);

				} else {

					$data['default'][ $index ] = array(
						'slug'    => $index,
						'label'   => $filter['label'],
						'orderby' => $tl_default_settings['default-sort-by'],
						'order'   => $tl_default_settings['default-sort-order'],
					);
				}
			}

			// saved filters.
			foreach ( $current_user_filters['saved'] as $index => $filter ) {

				$data['saved'][ 'saved-' . $index ] = array(
					'slug'    => 'saved-' . $index,
					'label'   => $filter['label'],
					'orderby' => $filter['sort-by'],
					'order'   => $filter['sort-order'],
				);
			}

			return new WP_REST_Response( $data, 200 );
		}

		/**
		 * Ticket list orderby filters
		 *
		 * @param WP_REST_Request $request - request object.
		 * @return WP_Error|WP_REST_Response
		 */
		public static function get_orderby_fields( $request ) {

			$current_user = WPSC_Current_User::$current_user;
			$list_items = $current_user->get_tl_list_items();

			$data = array();
			foreach ( $list_items as $slug ) {

				$cf = WPSC_Custom_Field::get_cf_by_slug( $slug );
				if ( ! $cf ) {
					continue;
				}

				$data[ $slug ] = $cf->name;
			}

			return new WP_REST_Response( $data, 200 );
		}

		/**
		 * Ticket list items route
		 *
		 * @param WP_REST_Request $request - request object.
		 * @return WP_Error|WP_REST_Response
		 */
		public static function get_list_items( $request ) {

			$current_user = WPSC_Current_User::$current_user;
			$list_items = $current_user->get_tl_list_items();

			$data = array();
			foreach ( $list_items as $slug ) {

				$cf = WPSC_Custom_Field::get_cf_by_slug( $slug );
				if ( ! $cf ) {
					continue;
				}

				$data[ $slug ] = WPSC_REST_Custom_Fields::modify_response( $cf );
			}

			return new WP_REST_Response( $data, 200 );
		}

		/**
		 * Ticket list items route
		 *
		 * @param WP_REST_Request $request - request object.
		 * @return WP_Error|WP_REST_Response
		 */
		public static function get_form_fields( $request ) {

			$current_user = WPSC_Current_User::$current_user;
			$tff = get_option( 'wpsc-tff' );

			$data = array();
			foreach ( $tff as $slug => $value ) {

				$cf = WPSC_Custom_Field::get_cf_by_slug( $slug );
				if ( ! $cf ) {
					continue;
				}

				if (
					isset( $value['allowed_user'] ) &&
					(
						( $current_user->is_agent && ! in_array( $value['allowed_user'], array( 'agent', 'both' ) ) ) ||
						( ! $current_user->is_agent && ! in_array( $value['allowed_user'], array( 'customer', 'both' ) ) )
					)
				) {
					continue;
				}

				$data[ $slug ] = WPSC_REST_Custom_Fields::modify_response( $cf );
				$data[ $slug ]['is-required'] = $value['is-required'];
			}

			return new WP_REST_Response( $data, 200 );
		}

		/**
		 * Validate ticket filter
		 *
		 * @param string          $param - parameter value.
		 * @param WP_REST_Request $request - request object.
		 * @param string          $key - filter key.
		 * @return boolean
		 */
		public static function validate_ticket_filter( $param, $request, $key ) {

			$current_user = WPSC_Current_User::$current_user;
			$current_user_filters = $current_user->get_tl_filters();

			// system defaults.
			if ( isset( $current_user_filters['default'][ $param ] ) ) {
				return true;
			}

			// custom defaults.
			if ( preg_match( '/default-(\d*)$/', $param, $matches ) && isset( $current_user_filters['default'][ $matches[1] ] ) ) {
				return true;
			}

			// saved filters.
			if ( preg_match( '/saved-(\d*)$/', $param, $matches ) && isset( $current_user_filters['saved'][ $matches[1] ] ) ) {
				return true;
			}

			return false;
		}

		/**
		 * Validate ticket filter orderby slug
		 *
		 * @param string          $param - parameter value.
		 * @param WP_REST_Request $request - request object.
		 * @param string          $key - filter key.
		 * @return boolean
		 */
		public static function validate_filter_orderby( $param, $request, $key ) {

			$current_user = WPSC_Current_User::$current_user;
			$list_items = $current_user->get_tl_list_items();
			$flag = false;
			foreach ( $list_items as $slug ) {
				$cf = WPSC_Custom_Field::get_cf_by_slug( $slug );
				if ( ! $cf || ! $cf->type::$is_sort ) {
					continue;
				}
				if ( $cf->slug == $param ) {
					$flag = true;
					break;
				}
			}
			return $flag;
		}
	}
endif;

WPSC_REST_Tickets::init();
