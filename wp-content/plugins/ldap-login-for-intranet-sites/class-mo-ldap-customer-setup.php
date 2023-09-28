<?php
/**
 * This file contains Class to used for all operations for customer registration and login with miniOrange.
 *
 * @package miniOrange_LDAP_AD_Integration
 * @subpackage Main
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'MO_LDAP_Customer_Setup' ) ) {
	/**
	 * MO_LDAP_Customer_Setup
	 */
	class MO_LDAP_Customer_Setup {
		const TIMEOUT      = '10000';
		const SUPPORTEMAIL = 'ldapsupport@xecurify.com';

		/**
		 * Var default_customer_key
		 *
		 * @var string
		 */
		private $default_customer_key = '16555';

		/**
		 * Var default_api_key
		 *
		 * @var string
		 */
		private $default_api_key = 'fFd2XcvTGDemZvbw1bcUesNJWEqKbbUq';

		/**
		 * Function create_customer : Register customer in miniOrange
		 *
		 * @return string
		 */
		public function create_customer() {

			$url = MO_LDAP_Plugin_Constants::MO_LDAP_HOST_NAME . '/moas/rest/customer/add';

			$email    = esc_attr( get_option( 'mo_ldap_local_admin_email' ) );
			$password = esc_attr( get_option( 'mo_ldap_local_password' ) );

			$fields       = array(
				'areaOfInterest' => 'WP LDAP for Intranet',
				'email'          => $email,
				'password'       => $password,
			);
			$field_string = wp_json_encode( $fields );

			$headers = array(
				'Content-Type'  => 'application/json',
				'charset'       => 'UTF - 8',
				'Authorization' => 'Basic',
			);
			$args    = array(
				'method'      => 'POST',
				'body'        => $field_string,
				'timeout'     => self::TIMEOUT,
				'redirection' => '5',
				'httpversion' => '1.0',
				'blocking'    => true,
				'headers'     => $headers,
			);

			$response = wp_remote_post( $url, $args );
			if ( is_wp_error( $response ) ) {
				return wp_json_encode( array( 'status' => 'ERROR' ) );
			}
			return $response['body'];
		}

		/**
		 * Function get_customer_key : Get customer key from miniOrange
		 *
		 * @return string
		 */
		public function get_customer_key() {

			$url      = MO_LDAP_Plugin_Constants::MO_LDAP_HOST_NAME . '/moas/rest/customer/key';
			$email    = sanitize_email( get_option( 'mo_ldap_local_admin_email' ) );
			$password = get_option( 'mo_ldap_local_password' );

			$fields       = array(
				'email'    => $email,
				'password' => $password,
			);
			$field_string = wp_json_encode( $fields );

			$headers = array(
				'Content-Type'  => 'application/json',
				'charset'       => 'UTF - 8',
				'Authorization' => 'Basic',
			);
			$args    = array(
				'method'      => 'POST',
				'body'        => $field_string,
				'timeout'     => self::TIMEOUT,
				'redirection' => '5',
				'httpversion' => '1.0',
				'blocking'    => true,
				'headers'     => $headers,

			);

			$response = wp_remote_post( $url, $args );
			if ( is_wp_error( $response ) ) {
				return wp_json_encode( array( 'status' => 'ERROR' ) );
			}
			return $response['body'];
		}

		/**
		 * Function submit_contact_us : Submit query to miniOrange requested by plugin user
		 *
		 * @param  mixed $q_email : Email of User.
		 * @param  mixed $q_phone : Phone number of User.
		 * @param  mixed $query : Support Query.
		 * @return string
		 */
		public function submit_contact_us( $q_email, $q_phone, $query ) {
			$url          = MO_LDAP_Plugin_Constants::MO_LDAP_HOST_NAME . '/moas/rest/customer/contact-us';
			$fname        = sanitize_text_field( get_option( 'mo_ldap_local_admin_fname' ) );
			$lname        = sanitize_text_field( get_option( 'mo_ldap_local_admin_lname' ) );
			$company_name = sanitize_text_field( get_option( 'mo_ldap_local_admin_company' ) );

			$fields       = array(
				'firstName' => $fname,
				'lastName'  => $lname,
				'company'   => $company_name,
				'email'     => $q_email,
				'ccEmail'   => self::SUPPORTEMAIL,
				'phone'     => $q_phone,
				'query'     => $query,
			);
			$field_string = wp_json_encode( $fields );

			$headers = array(
				'Content-Type'  => 'application/json',
				'charset'       => 'UTF - 8',
				'Authorization' => 'Basic',
			);
			$args    = array(
				'method'      => 'POST',
				'body'        => $field_string,
				'timeout'     => self::TIMEOUT,
				'redirection' => '5',
				'httpversion' => '1.0',
				'blocking'    => true,
				'headers'     => $headers,
			);

			$response = wp_remote_post( $url, $args );

			if ( is_wp_error( $response ) ) {
				return wp_json_encode( array( 'status' => 'ERROR' ) );
			}
			return $response['body'];
		}

		/**
		 * Function send_email_alert
		 *
		 * @param  mixed $subject : Email Subject.
		 * @param  mixed $email : User email.
		 * @param  mixed $query : Notification query.
		 * @param  mixed $company : Company name.
		 * @return string
		 */
		public function send_email_alert( $subject, $email, $query, $company ) {
			$url          = MO_LDAP_Plugin_Constants::MO_LDAP_HOST_NAME . '/moas/api/notify/send';
			$customer_key = $this->default_customer_key;
			$api_key      = $this->default_api_key;

			$current_time_in_millis = self::get_timestamp();
			$string_to_hash         = $customer_key . $current_time_in_millis . $api_key;
			$hash_value             = hash( 'sha512', $string_to_hash );
			$from_email             = $email;
			global $user;
			$user    = wp_get_current_user();
			$company = ! empty( $company ) ? sanitize_text_field( wp_unslash( $company ) ) : sanitize_text_field( isset( $_SERVER['SERVER_NAME'] ) ? wp_unslash( $_SERVER['SERVER_NAME'] ) : '' );

			$esc_allowed = array(
				'a'      => array(
					'href'  => array(),
					'title' => array(),
				),
				'br'     => array(),
				'em'     => array(),
				'strong' => array(),
				'b'      => array(),
				'h1'     => array(),
				'h2'     => array(),
				'h3'     => array(),
				'h4'     => array(),
				'h5'     => array(),
				'h6'     => array(),
				'i'      => array(),
				'span'   => array(),
			);

			$content      = '<div >First Name :' . esc_html( $user->user_firstname ) . '<br><br>Last  Name :' . esc_html( $user->user_lastname ) . '   <br><br>Company :<a href="' . esc_url( $company ) . '" target="_blank" >' . esc_html( $company ) . '</a><br><br>Email :<a href="mailto:' . esc_attr( $from_email ) . '" target="_blank">' . esc_html( $from_email ) . '</a><br><br>' . wp_kses( $query, $esc_allowed ) . '</div>';
			$fields       = array(
				'customerKey' => $customer_key,
				'sendEmail'   => true,
				'email'       => array(
					'customerKey' => $customer_key,
					'fromEmail'   => $email,
					'bccEmail'    => self::SUPPORTEMAIL,
					'fromName'    => 'miniOrange',
					'toEmail'     => self::SUPPORTEMAIL,
					'toName'      => self::SUPPORTEMAIL,
					'subject'     => $subject,
					'content'     => $content,
				),
			);
			$field_string = wp_json_encode( $fields );
			$headers      = array(
				'Content-Type'  => 'application/json',
				'Customer-Key'  => $customer_key,
				'Timestamp'     => $current_time_in_millis,
				'Authorization' => $hash_value,
			);
			$args         = array(
				'method'      => 'POST',
				'body'        => $field_string,
				'timeout'     => self::TIMEOUT,
				'redirection' => '5',
				'httpversion' => '1.0',
				'blocking'    => true,
				'headers'     => $headers,

			);

			$response = wp_remote_post( $url, $args );
			if ( is_wp_error( $response ) ) {
				return wp_json_encode( array( 'status' => 'ERROR' ) );
			}
			return $response['body'];
		}

		/**
		 * Function get_timestamp : Get current timestamp
		 *
		 * @return array
		 */
		public function get_timestamp() {
			$url      = MO_LDAP_Plugin_Constants::MO_LDAP_HOST_NAME . '/moas/rest/mobile/get-timestamp';
			$response = wp_remote_post( $url );
			if ( is_wp_error( $response ) ) {
				$current_time_in_millis = round( microtime( true ) * 1000 );
				$current_time_in_millis = number_format( $current_time_in_millis, 0, '', '' );
				return $current_time_in_millis;
			} else {
				return $response['body'];
			}
		}

		/**
		 * Function check_customer : check if customer registered in miniOrange
		 *
		 * @return string
		 */
		public function check_customer() {

			$url   = MO_LDAP_Plugin_Constants::MO_LDAP_HOST_NAME . '/moas/rest/customer/check-if-exists';
			$email = get_option( 'mo_ldap_local_admin_email' );

			$fields       = array(
				'email' => $email,
			);
			$field_string = wp_json_encode( $fields );
			$headers      = array(
				'Content-Type'  => 'application/json',
				'charset'       => 'UTF - 8',
				'Authorization' => 'Basic',
			);
			$args         = array(
				'method'      => 'POST',
				'body'        => $field_string,
				'timeout'     => self::TIMEOUT,
				'redirection' => '5',
				'httpversion' => '1.0',
				'blocking'    => true,
				'headers'     => $headers,

			);

			$response = wp_remote_post( $url, $args );
			if ( is_wp_error( $response ) ) {
				return wp_json_encode( array( 'status' => 'ERROR' ) );
			}
			return $response['body'];
		}

		/**
		 * Function mo_ldap_local_forgot_password : Recover miniOrange password
		 *
		 * @param  string $email : User miniOrange email.
		 * @return string
		 */
		public function mo_ldap_local_forgot_password( $email ) {

			$url                    = MO_LDAP_Plugin_Constants::MO_LDAP_HOST_NAME . '/moas/rest/customer/password-reset';
			$customer_key           = get_option( 'mo_ldap_local_admin_customer_key' );
			$api_key                = get_option( 'mo_ldap_local_admin_api_key' );
			$current_time_in_millis = round( microtime( true ) * 1000 );
			$string_to_hash         = $customer_key . number_format( $current_time_in_millis, 0, '', '' ) . $api_key;
			$hash_value             = hash( 'sha512', $string_to_hash );

			$fields = array(
				'email' => $email,
			);

			$field_string = wp_json_encode( $fields );
			$headers      = array(
				'Content-Type'  => 'application/json',
				'Customer-Key'  => $customer_key,
				'Timestamp'     => number_format( $current_time_in_millis, 0, '', '' ),
				'Authorization' => $hash_value,
			);
			$args         = array(
				'method'      => 'POST',
				'body'        => $field_string,
				'timeout'     => self::TIMEOUT,
				'redirection' => '5',
				'httpversion' => '1.0',
				'blocking'    => true,
				'headers'     => $headers,

			);

			$response = wp_remote_post( $url, $args );
			if ( is_wp_error( $response ) ) {
				return wp_json_encode( array( 'status' => 'ERROR' ) );
			}
			return $response['body'];
		}
	}
}
