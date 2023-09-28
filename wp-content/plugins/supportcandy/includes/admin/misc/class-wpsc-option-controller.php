<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_Option_Controller' ) ) :

	final class WPSC_Option_Controller {

		/**
		 * Initialize this class
		 */
		public static function init() {

			add_action( 'wp_ajax_wpsc_add_new_option', array( __CLASS__, 'add_new' ) );
			add_action( 'wp_ajax_wpsc_set_edit_option', array( __CLASS__, 'update_option' ) );
		}

		/**
		 * Add new option
		 *
		 * @return void
		 */
		public static function add_new() {

			if ( check_ajax_referer( 'wpsc_add_new_option', '_ajax_nonce', false ) !== 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$name = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
			if ( ! $name ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$data   = array(
				'name'         => $name,
				'date_created' => ( new DateTime( 'now' ) )->format( 'Y-m-d H:m:s' ),
			);
			$option = WPSC_Option::insert( $data );

			$response = array(
				'id'   => $option->id,
				'name' => $option->name,
			);

			wp_send_json( $response );
		}

		/**
		 * Add new option
		 *
		 * @return void
		 */
		public static function update_option() {

			if ( check_ajax_referer( 'wpsc_set_edit_option', '_ajax_nonce', false ) !== 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$id = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : 0;
			if ( ! $id ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$name = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
			if ( ! $name ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$option       = new WPSC_Option( $id );
			$option->name = $name;
			$option->save();

			$response = array(
				'id'   => $option->id,
				'name' => $option->name,
			);

			wp_send_json( $response );
		}
	}
endif;

WPSC_Option_Controller::init();
