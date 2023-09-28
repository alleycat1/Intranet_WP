<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_REST_Current_User' ) ) :

	final class WPSC_REST_Current_User {

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

			register_rest_route(
				'supportcandy/v2',
				'/current-user',
				array(
					'methods'             => 'GET',
					'callback'            => array( __CLASS__, 'get_current_user' ),
					'permission_callback' => 'is_user_logged_in',
				),
			);
		}

		/**
		 * Current user information
		 *
		 * @param WP_REST_Request $request - request object.
		 * @return WP_Error|WP_REST_Response
		 */
		public static function get_current_user( $request ) {

			$current_user = WPSC_Current_User::$current_user;

			$data = array(
				'name'     => $current_user->customer->name,
				'email'    => $current_user->customer->email,
				'is_agent' => $current_user->is_agent,
			);

			if ( $current_user->is_agent ) {

				$agent_roles = get_option( 'wpsc-agent-roles', array() );
				$data['agent_caps'] = $agent_roles[ $current_user->agent->role ]['caps'];
			}

			return new WP_REST_Response( $data, 200 );
		}
	}
endif;

WPSC_REST_Current_User::init();
