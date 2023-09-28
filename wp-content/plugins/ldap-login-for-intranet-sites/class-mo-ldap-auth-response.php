<?php
/**
 * This file contains Class used for all LDAP Auth response structure
 *
 * @package miniOrange_LDAP_AD_Integration
 * @subpackage Main
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'MO_LDAP_Auth_Response' ) ) {
	/**
	 * MO_LDAP_Auth_Response : Standard used for all LDAP response
	 */
	class MO_LDAP_Auth_Response {

		/**
		 * Var status
		 *
		 * @var mixed
		 */
		public $status;

		/**
		 * Var status_message
		 *
		 * @var mixed
		 */
		public $status_message;

		/**
		 * Var user_dn
		 *
		 * @var mixed
		 */
		public $user_dn;

		/**
		 * Var attribute_list
		 *
		 * @var mixed
		 */
		public $attribute_list;

		/**
		 * Var profile_attributes_list
		 *
		 * @var mixed
		 */
		public $profile_attributes_list;
	}
}
