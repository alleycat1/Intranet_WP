<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_REST_Customers' ) ) :

	final class WPSC_REST_Customers {

		/**
		 * Array of customer slugs which we do not want to expose to REST API
		 *
		 * @var array
		 */
		public static $prevent_data = array();

		/**
		 * Ignore fields to modification and sends directly to client
		 *
		 * @var array
		 */
		public static $ignore_modification = array();

		/**
		 * Initialize this class
		 *
		 * @return void
		 */
		public static function init() {

			add_action( 'wpsc_rest_register_routes', array( __CLASS__, 'register_routes' ) );
		}

		/**
		 * Load class properties
		 *
		 * @return void
		 */
		public static function load_properties() {

			$current_user = WPSC_Current_User::$current_user;

			// prevent fields to send client side.
			$slugs = array( 'user', 'ticket_count' );
			if ( ! $current_user->is_agent ) {
				$slugs[] = 'email';
			}
			self::$prevent_data = apply_filters(
				'wpsc_rest_prevent_customer_data',
				$slugs
			);

			// ignore fields to mofication.
			self::$ignore_modification = apply_filters(
				'wpsc_rest_ignore_customer_data_modification',
				array( 'name', 'email' )
			);
		}

		/**
		 * Register routes
		 *
		 * @return void
		 */
		public static function register_routes() {

			// list categories.
			register_rest_route(
				'supportcandy/v2',
				'/customers',
				array(
					'methods'             => 'GET',
					'callback'            => array( __CLASS__, 'get_customers' ),
					'args'                => array(
						'per_page' => array(
							'default'           => 20,
							'validate_callback' => array( 'WPSC_REST_API', 'validate_integer_value' ),
						),
						'page'     => array(
							'default'           => 1,
							'validate_callback' => array( 'WPSC_REST_API', 'validate_integer_value' ),
						),
						'search'   => array(
							'default'           => '',
							'sanitize_callback' => 'sanitize_text_field',
						),
					),
					'permission_callback' => array( __CLASS__, 'check_permission' ),
				),
			);

			// list individual category.
			register_rest_route(
				'supportcandy/v2',
				'/customers/(?P<id>\d+)',
				array(
					'methods'             => 'GET',
					'callback'            => array( __CLASS__, 'get_individual_customer' ),
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
		 * Customer collection
		 *
		 * @param WP_REST_Request $request - request object.
		 * @return WP_Error|WP_REST_Response
		 */
		public static function get_customers( $request ) {

			self::load_properties();
			$search = $request->get_param( 'search' );
			$data = WPSC_Customer::find(
				array(
					'items_per_page' => $request->get_param( 'per_page' ),
					'page_no'        => $request->get_param( 'page' ),
					'search'         => $search,
				)
			);
			foreach ( $data['results'] as $key => $customer ) {
				$data['results'][ $key ] = self::modify_response( $customer );
			}
			return new WP_REST_Response( $data, 200 );
		}

		/**
		 * Single customer
		 *
		 * @param WP_REST_Request $request - request object.
		 * @return WP_Error|WP_REST_Response
		 */
		public static function get_individual_customer( $request ) {

			self::load_properties();
			$current_user = WPSC_Current_User::$current_user;
			$customer = new WPSC_Customer( $request->get_param( 'id' ) );

			// if customer is an agent, allowed.
			// if current user is not agent and customer is within allowed customers (considering usergroups), allowed.
			$agent = WPSC_Agent::get_by_customer( $customer );
			if ( ! $agent->id && ! $current_user->is_agent ) {
				$allowed_customers = apply_filters( 'wpsc_non_agent_user_customers_allowed', array( $customer->id ), $customer );
				if ( ! in_array( $current_user->customer->id, $allowed_customers ) ) {
					return wp_send_json_error( 'Unauthorized!', 401 );
				}
			}

			$data = self::modify_response( $customer );
			return new WP_REST_Response( $data, 200 );
		}

		/**
		 * Modify response data appropreate for client side
		 *
		 * @param WPSC_Customer $customer - response array.
		 * @return array
		 */
		public static function modify_response( $customer ) {

			$customer = $customer->to_array();
			$current_user = WPSC_Current_User::$current_user;
			$tff = get_option( 'wpsc-tff' );

			foreach ( $customer as $slug => $value ) {

				// remove prevent ticket data.
				if ( in_array( $slug, self::$prevent_data ) ) {
					unset( $customer[ $slug ] );
					continue;
				}

				// ignore modications.
				if ( in_array( $slug, self::$ignore_modification ) ) {
					continue;
				}

				// prevent if custom field is not allowed in my profile or ticket form. Applicable for non-agent.
				$cf = WPSC_Custom_Field::get_cf_by_slug( $slug );
				if ( ! $current_user->is_agent && $cf && ! $cf->type::$is_default &&
					! (
						$cf->allow_my_profile ||
						( $cf->allow_ticket_form && isset( $tff[ $slug ] ) )
					)
				) {
					unset( $customer[ $slug ] );
					continue;
				}

				// convert has multiple values to array.
				if ( WPSC_Customer::$schema[ $slug ]['has_multiple_val'] ) {
					if ( $value ) {
						$customer[ $slug ] = array_filter(
							array_map(
								fn( $val) => is_numeric( $val ) ? intval( $val ) : $val,
								explode( '|', $value )
							)
						);
					} else {
						$customer[ $slug ] = array();
					}
					continue;
				}

				// empty date value.
				if ( $value == '0000-00-00 00:00:00' ) {
					$customer[ $slug ] = '';
				}

				// cast numeric fields into integer.
				if ( is_numeric( $value ) ) {
					$customer[ $slug ] = intval( $value );
				}
			}

			return apply_filters( 'wpsc_rest_modify_customer_response', $customer );
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

			$error = new WP_Error( 'invalid_id', 'Invalid customer id', array( 'status' => 400 ) );
			$customer = new WPSC_Customer( $param );
			return $customer->id ? true : $error;
		}

		/**
		 * Check permission for these routes
		 *
		 * @return boolean
		 */
		public static function check_permission() {

			$current_user = WPSC_Current_User::$current_user;
			return $current_user->is_agent;
		}
	}
endif;

WPSC_REST_Customers::init();
