<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_REST_Custom_Fields' ) ) :

	final class WPSC_REST_Custom_Fields {

		/**
		 * Array of custom field slugs which we do not want to expose to REST API
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
		 * Load prevent data
		 *
		 * @return void
		 */
		public static function load_prevent_data() {

			self::$prevent_data = apply_filters(
				'wpsc_rest_prevent_cf_data',
				array(
					'date_display_as',
					'date_format',
					'is_personal_info',
					'allow_ticket_form',
					'allow_my_profile',
					'tl_width',
					'load_order',
				)
			);
		}

		/**
		 * Register routes
		 *
		 * @return void
		 */
		public static function register_routes() {

			// list custom fields.
			register_rest_route(
				'supportcandy/v2',
				'/custom-fields',
				array(
					'methods'             => 'GET',
					'callback'            => array( __CLASS__, 'get_custom_fields' ),
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
							'validate_callback' => array( __CLASS__, 'validate_filter' ),
						),
					),
					'permission_callback' => 'is_user_logged_in',
				),
			);

			// list individual custom field.
			register_rest_route(
				'supportcandy/v2',
				'/custom-fields/(?P<id>\d+)',
				array(
					'methods'             => 'GET',
					'callback'            => array( __CLASS__, 'get_individual_custom_field' ),
					'args'                => array(
						'id' => array(
							'validate_callback' => array( __CLASS__, 'validate_cf_id' ),
						),
					),
					'permission_callback' => 'is_user_logged_in',
				),
			);

			// list custom field options.
			register_rest_route(
				'supportcandy/v2',
				'/custom-fields/(?P<id>\d+)/options',
				array(
					'methods'             => 'GET',
					'callback'            => array( __CLASS__, 'get_options' ),
					'args'                => array(
						'id' => array(
							'validate_callback' => array( __CLASS__, 'validate_cf_id' ),
						),
					),
					'permission_callback' => 'is_user_logged_in',
				),
			);

			// individual custom field option.
			register_rest_route(
				'supportcandy/v2',
				'/custom-fields/(?P<id>\d+)/options/(?P<option_id>\d+)',
				array(
					'methods'             => 'GET',
					'callback'            => array( __CLASS__, 'get_individual_option' ),
					'args'                => array(
						'id'        => array(
							'validate_callback' => array( __CLASS__, 'validate_cf_id' ),
						),
						'option_id' => array(
							'validate_callback' => array( __CLASS__, 'validate_option_id' ),
						),
					),
					'permission_callback' => 'is_user_logged_in',
				),
			);
		}

		/**
		 * Custom field collection
		 *
		 * @param WP_REST_Request $request - request object.
		 * @return WP_Error|WP_REST_Response
		 */
		public static function get_custom_fields( $request ) {

			$filter = $request->get_param( 'filter' );

			$filters = array(
				'items_per_page' => $request->get_param( 'per_page' ),
				'page_no'        => $request->get_param( 'page' ),
				'meta_query'     => array(
					'relation' => 'AND',
				),
			);

			switch ( $filter ) {

				case 'ticket_fields':
					$filters['meta_query'][] = array(
						'slug'    => 'field',
						'compare' => '=',
						'val'     => 'ticket',
					);
					break;

				case 'agentonly_fields':
					$filters['meta_query'][] = array(
						'slug'    => 'field',
						'compare' => '=',
						'val'     => 'agentonly',
					);
					break;

				case 'customer_fields':
					$filters['meta_query'][] = array(
						'slug'    => 'field',
						'compare' => '=',
						'val'     => 'customer',
					);
					break;

				default:
					$filters = apply_filters( 'wpsc_rest_cf_query_filter', $filters, $filter );
			}

			$custom_fields = WPSC_Custom_Field::find( $filters, false );
			$custom_fields['results'] = array_map(
				fn( $cf ) => self::modify_response( $cf ),
				$custom_fields['results']
			);

			return new WP_REST_Response( $custom_fields, 200 );
		}

		/**
		 * Single custom field
		 *
		 * @param WP_REST_Request $request - request object.
		 * @return WP_Error|WP_REST_Response
		 */
		public static function get_individual_custom_field( $request ) {

			$cf = new WPSC_Custom_Field( $request->get_param( 'id' ) );
			$data = self::modify_response( $cf->to_array() );
			return new WP_REST_Response( $data, 200 );
		}

		/**
		 * Custom field options
		 *
		 * @param WP_REST_Request $request - request object.
		 * @return WP_Error|WP_REST_Response
		 */
		public static function get_options( $request ) {

			$cf = new WPSC_Custom_Field( $request->get_param( 'id' ) );
			if ( ! $cf->type::$has_options ) {
				return new WP_Error( 'invalid_custom_field', 'This custom field does not have any options!', array( 'status' => 400 ) );
			}
			$options = $cf->get_options();
			$data = array();
			foreach ( $options as $option ) {
				$data[] = array(
					'id'   => intval( $option->id ),
					'name' => $option->name,
				);
			}
			return new WP_REST_Response( $data, 200 );
		}

		/**
		 * Single option
		 *
		 * @param WP_REST_Request $request - request object.
		 * @return WP_Error|WP_REST_Response
		 */
		public static function get_individual_option( $request ) {

			$option = new WPSC_Option( $request->get_param( 'option_id' ) );
			$data = array(
				'id'   => intval( $option->id ),
				'name' => $option->name,
			);
			return new WP_REST_Response( $data, 200 );
		}

		/**
		 * Modify custom field data appropreate for client side
		 *
		 * @param array $cf - custom field array.
		 * @return array
		 */
		public static function modify_response( $cf ) {

			$object = WPSC_Custom_Field::get_cf_by_slug( $cf['slug'] );

			foreach ( $cf as $key => $value ) {

				// remove if listed in prevent list.
				if ( in_array( $key, self::$prevent_data ) ) {
					unset( $cf[ $key ] );
					continue;
				}

				// convert has multiple values to array.
				if ( WPSC_Custom_Field::$schema[ $key ]['has_multiple_val'] ) {
					if ( $value ) {
						$cf[ $key ] = array_filter(
							array_map(
								fn( $val) => is_numeric( $val ) ? intval( $val ) : $val,
								explode( '|', $value )
							)
						);
					} else {
						$cf[ $key ] = array();
					}
					continue;
				}

				// empty date value.
				if ( $value == '0000-00-00 00:00:00' ) {
					$cf[ $key ] = '';
				}

				// cast numeric fields into integer.
				if ( is_numeric( $value ) ) {
					$cf[ $key ] = intval( $value );
				}
			}

			$cf['has_options'] = $object->type::$has_options;

			return $cf;
		}

		/**
		 * Validate filter
		 *
		 * @param string          $param - parameter value.
		 * @param WP_REST_Request $request - request object.
		 * @param string          $key - filter key.
		 * @return boolean
		 */
		public static function validate_filter( $param, $request, $key ) {

			$current_user = WPSC_Current_User::$current_user;

			$filters = apply_filters(
				'wpsc_rest_custom_field_filters',
				array( 'all', 'ticket_fields', 'agentonly_fields', 'customer_fields' )
			);

			if ( $param == 'agentonly_fields' && ! $current_user->is_agent ) {
				return false;
			}

			return in_array( $param, $filters );
		}

		/**
		 * Validate id
		 *
		 * @param string          $param - parameter value.
		 * @param WP_REST_Request $request - request object.
		 * @param string          $key - filter key.
		 * @return boolean
		 */
		public static function validate_cf_id( $param, $request, $key ) {

			$error = new WP_Error( 'invalid_id', 'Invalid custom field id', array( 'status' => 400 ) );
			$cf = new WPSC_Custom_Field( $param );
			return $cf->id ? true : $error;
		}

		/**
		 * Validate option id
		 *
		 * @param string          $param - parameter value.
		 * @param WP_REST_Request $request - request object.
		 * @param string          $key - filter key.
		 * @return boolean
		 */
		public static function validate_option_id( $param, $request, $key ) {

			$cf = new WPSC_Custom_Field( $request->get_param( 'id' ) );
			if ( ! $cf->id ) {
				return new WP_Error( 'invalid_id', 'Invalid custom field id', array( 'status' => 400 ) );
			}
			if ( ! $cf->type::$has_options ) {
				return new WP_Error( 'invalid_custom_field', 'This custom field does not have any options!', array( 'status' => 400 ) );
			}
			$option = new WPSC_Option( $param );
			if ( ! $option->id ) {
				return new WP_Error( 'invalid_option_id', 'Invalid option id', array( 'status' => 400 ) );
			}
			if ( $option->custom_field->id != $cf->id ) {
				return new WP_Error( 'invalid_option_id', 'Option does not belong to given custom field!', array( 'status' => 400 ) );
			}
			return true;
		}
	}
endif;

WPSC_REST_Custom_Fields::init();
