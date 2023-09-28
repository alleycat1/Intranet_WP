<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_REST_Agents' ) ) :

	final class WPSC_REST_Agents {

		/**
		 * Prevent data from sending
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

			add_action( 'init', array( __CLASS__, 'load_prevent_data' ) );
			add_action( 'wpsc_rest_register_routes', array( __CLASS__, 'register_routes' ) );
		}

		/**
		 * Load prevent slugs
		 *
		 * @return void
		 */
		public static function load_prevent_data() {

			self::$prevent_data = apply_filters( 'wpsc_rest_prevent_agent_data', array( 'user', 'customer', 'role', 'workload', 'unresolved_count', 'is_active' ) );
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
				'/agents',
				array(
					'methods'             => 'GET',
					'callback'            => array( __CLASS__, 'get_agents' ),
					'args'                => array(
						'page'     => array(
							'default'           => 1,
							'validate_callback' => array( 'WPSC_REST_API', 'validate_integer_value' ),
						),
						'per_page' => array(
							'default'           => 20,
							'validate_callback' => array( 'WPSC_REST_API', 'validate_integer_value' ),
						),
						'search'   => array(
							'default'           => '',
							'sanitize_callback' => 'sanitize_text_field',
						),
					),
					'permission_callback' => 'is_user_logged_in',
				),
			);

			// list individual category.
			register_rest_route(
				'supportcandy/v2',
				'/agents/(?P<id>\d+)',
				array(
					'methods'             => 'GET',
					'callback'            => array( __CLASS__, 'get_individual_agent' ),
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
		 * Agengs collection
		 *
		 * @param WP_REST_Request $request - request object.
		 * @return WP_Error|WP_REST_Response
		 */
		public static function get_agents( $request ) {

			$agents = WPSC_Agent::find(
				array(
					'page_no'        => $request->get_param( 'page' ),
					'items_per_page' => $request->get_param( 'per_page' ),
					'search'         => $request->get_param( 'search' ),
					'meta_query'     => array(
						'relation' => 'AND',
						array(
							'slug'    => 'is_active',
							'compare' => '=',
							'val'     => 1,
						),
					),
				),
				false
			);

			if ( $agents['results'] ) {
				$agents['results'] = array_map(
					fn( $agent ) => self::modify_response( $agent ),
					$agents['results']
				);
			}

			return new WP_REST_Response( $agents, 200 );
		}

		/**
		 * Single agent
		 *
		 * @param WP_REST_Request $request - request object.
		 * @return WP_Error|WP_REST_Response
		 */
		public static function get_individual_agent( $request ) {

			$agent = new WPSC_Agent( $request->get_param( 'id' ) );
			$data = self::modify_response( $agent->to_array() );
			return new WP_REST_Response( $data, 200 );
		}

		/**
		 * Modify response data appropreate for client side
		 *
		 * @param array $agent - agent array.
		 * @return array
		 */
		public static function modify_response( $agent ) {

			foreach ( $agent as $key => $value ) {

				if ( in_array( $key, self::$prevent_data ) ) {
					unset( $agent[ $key ] );
				}
			}

			return $agent;
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

			$error = new WP_Error( 'invalid_id', 'Invalid agent id', array( 'status' => 400 ) );
			$agent = new WPSC_Agent( $param );
			return $agent->id ? true : $error;
		}
	}
endif;

WPSC_REST_Agents::init();
