<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_REST_Categories' ) ) :

	final class WPSC_REST_Categories {

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

			// list categories.
			register_rest_route(
				'supportcandy/v2',
				'/categories',
				array(
					'methods'             => 'GET',
					'callback'            => array( __CLASS__, 'get_categories' ),
					'permission_callback' => 'is_user_logged_in',
				),
			);

			// list individual category.
			register_rest_route(
				'supportcandy/v2',
				'/categories/(?P<id>\d+)',
				array(
					'methods'             => 'GET',
					'callback'            => array( __CLASS__, 'get_individual_category' ),
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
		 * Categories collection
		 *
		 * @param WP_REST_Request $request - request object.
		 * @return WP_Error|WP_REST_Response
		 */
		public static function get_categories( $request ) {

			$categories = WPSC_Category::find( array( 'items_per_page' => 0 ), false )['results'];
			$data = array();
			foreach ( $categories as $category ) {
				$data[] = self::modify_response( $category );
			}
			return new WP_REST_Response( $data, 200 );
		}

		/**
		 * Single category
		 *
		 * @param WP_REST_Request $request - request object.
		 * @return WP_Error|WP_REST_Response
		 */
		public static function get_individual_category( $request ) {

			$category = new WPSC_Category( $request->get_param( 'id' ) );
			$data = self::modify_response( $category->to_array() );
			return new WP_REST_Response( $data, 200 );
		}

		/**
		 * Modify response data appropreate for client side
		 *
		 * @param array $category - response array.
		 * @return array
		 */
		public static function modify_response( $category ) {

			unset( $category['load_order'] );
			$category['id'] = intval( $category['id'] );
			return $category;
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

			$error = new WP_Error( 'invalid_id', 'Invalid category id', array( 'status' => 400 ) );
			$category = new WPSC_Category( $param );
			return $category->id ? true : $error;
		}
	}
endif;

WPSC_REST_Categories::init();
