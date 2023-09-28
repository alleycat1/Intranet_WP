<?php
/**
 * This file contains class with plugin constants to be used in the plugin.
 *
 * @package miniOrange_LDAP_AD_Integration
 * @subpackage lib
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
// Adding the required files.
require_once 'class-mo-ldap-basic-enum.php';

if ( ! class_exists( 'MO_LDAP_Plugin_Constants' ) ) {
	/**
	 * MO_LDAP_Plugin_Constants
	 */
	class MO_LDAP_Plugin_Constants extends MO_LDAP_Basic_Enum {
		const VERSION           = '4.1.11';
		const MO_LDAP_HOST_NAME = 'https://login.xecurify.com';
	}
}
