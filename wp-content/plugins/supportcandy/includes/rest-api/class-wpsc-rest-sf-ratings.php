<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_REST_SF_Ratings' ) ) :

	final class WPSC_REST_SF_Ratings {

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

			// list ratings.
			register_rest_route(
				'supportcandy/v2',
				'/ratings',
				array(
					'methods'             => 'GET',
					'callback'            => array( __CLASS__, 'get_ratings' ),
					'permission_callback' => 'is_user_logged_in',
				),
			);

			// list individual rating.
			register_rest_route(
				'supportcandy/v2',
				'/ratings/(?P<id>\d+)',
				array(
					'methods'             => 'GET',
					'callback'            => array( __CLASS__, 'get_individual_rating' ),
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
		 * Ratings collection
		 *
		 * @param WP_REST_Request $request - request object.
		 * @return WP_Error|WP_REST_Response
		 */
		public static function get_ratings( $request ) {

			$ratings = WPSC_SF_Rating::find( array( 'items_per_page' => 0 ), false )['results'];
			$data = array();
			foreach ( $ratings as $rating ) {
				$data[] = self::modify_response( $rating );
			}
			return new WP_REST_Response( $data, 200 );
		}

		/**
		 * Single rating
		 *
		 * @param WP_REST_Request $request - request object.
		 * @return WP_Error|WP_REST_Response
		 */
		public static function get_individual_rating( $request ) {

			$rating = new WPSC_SF_Rating( $request->get_param( 'id' ) );
			$data = self::modify_response( $rating->to_array() );
			return new WP_REST_Response( $data, 200 );
		}

		/**
		 * Modify response data appropreate for client side
		 *
		 * @param array $rating - response array.
		 * @return array
		 */
		public static function modify_response( $rating ) {

			unset( $rating['load_order'] );
			$rating['id'] = intval( $rating['id'] );
			return $rating;
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

			$error = new WP_Error( 'invalid_id', 'Invalid rating id', array( 'status' => 400 ) );
			$rating = new WPSC_SF_Rating( $param );
			return $rating->id ? true : $error;
		}

	}

endif;

WPSC_REST_SF_Ratings::init();
