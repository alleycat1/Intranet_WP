<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_Ticket_Filter' ) ) :

	final class WPSC_Ticket_Filter {

		/**
		 * Initialize this class
		 */
		public static function init() {

			add_action( 'wp_ajax_wpsc_get_ticket_filter_operators', array( __CLASS__, 'get_operators' ) );
			add_action( 'wp_ajax_nopriv_wpsc_get_ticket_filter_operators', array( __CLASS__, 'get_operators' ) );

			add_action( 'wp_ajax_wpsc_get_ticket_filter_operands', array( __CLASS__, 'get_operands' ) );
			add_action( 'wp_ajax_nopriv_wpsc_get_ticket_filter_operands', array( __CLASS__, 'get_operands' ) );
		}

		/**
		 * Print applicable operators for given custom field
		 *
		 * @return void
		 */
		public static function get_operators() {

			if ( check_ajax_referer( 'wpsc_get_ticket_filter_operators', '_ajax_nonce', false ) !== 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			$current_user = WPSC_Current_User::$current_user;
			if ( ! $current_user->is_customer ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			$slug = isset( $_POST['slug'] ) ? sanitize_text_field( wp_unslash( $_POST['slug'] ) ) : '';
			if ( ! $slug ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$cf = WPSC_Custom_Field::get_cf_by_slug( $slug );
			if ( ! $cf ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$atl_filters = get_option( 'wpsc-atl-filter-items', array() );
			$ctl_filters = get_option( 'wpsc-ctl-filter-items', array() );

			// Checking whether current user has access to this filter.
			if ( ! (
				WPSC_Functions::is_site_admin() ||
				( $current_user->is_agent && in_array( $slug, $atl_filters ) ) ||
				( ! $current_user->is_agent && in_array( $slug, $ctl_filters ) )
			) ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			$cf->type::get_operators( $cf );
			wp_die();
		}

		/**
		 * Print applicable operands for given operator
		 *
		 * @return void
		 */
		public static function get_operands() {

			if ( check_ajax_referer( 'wpsc_get_ticket_filter_operands', '_ajax_nonce', false ) !== 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			$current_user = WPSC_Current_User::$current_user;
			if ( ! $current_user->is_customer ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			$id = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : 0;
			if ( ! $id ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$operator = isset( $_POST['operator'] ) ? sanitize_text_field( wp_unslash( $_POST['operator'] ) ) : '';
			if ( ! $operator ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$cf = new WPSC_Custom_Field( $id );
			if ( ! $cf->id ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			// allowed agent ticket filter ids.
			$atl_filter_ids = array_filter(
				array_map(
					fn ( $slug ) => WPSC_Custom_Field::get_cf_by_slug( $slug ) ? WPSC_Custom_Field::get_cf_by_slug( $slug )->id : '',
					get_option( 'wpsc-atl-filter-items', array() )
				)
			);

			// allowed customer ticket filter ids.
			$ctl_filter_ids = array_filter(
				array_map(
					fn ( $slug ) => WPSC_Custom_Field::get_cf_by_slug( $slug )->id,
					get_option( 'wpsc-ctl-filter-items', array() )
				)
			);

			// Checking whether current user has access to this filter.
			if ( ! (
				WPSC_Functions::is_site_admin() ||
				( $current_user->is_agent && in_array( $cf->id, $atl_filter_ids ) ) ||
				( ! $current_user->is_agent && in_array( $cf->id, $ctl_filter_ids ) )
			) ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			$cf->type::get_operands( $operator, $cf );
			wp_die();
		}
	}
endif;

WPSC_Ticket_Filter::init();
