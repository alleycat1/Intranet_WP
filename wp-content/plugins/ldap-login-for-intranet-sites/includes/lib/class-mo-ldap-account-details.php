<?php
/**
 * This file contains constant used for User Account setup.
 *
 * @package miniOrange_LDAP_AD_Integration
 * @subpackage lib
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
// Adding the required files.

require_once 'class-mo-ldap-basic-enum.php';

if ( ! class_exists( 'MO_LDAP_Account_Details' ) ) {
	/**
	 * MO_LDAP_Account_Details
	 */
	class MO_LDAP_Account_Details extends MO_LDAP_Basic_Enum {
		const ADMIN_CUSTOMER_ID = 'mo_ldap_local_admin_customer_key';
		const ADMIN_API_KEY     = 'mo_ldap_local_admin_api_key';
	}
}
