<?php
/**
 * This file contains Class to perform operations related to User Role.
 *
 * @package miniOrange_LDAP_AD_Integration
 * @subpackage Main
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'MO_LDAP_Role_Mapping' ) ) {
	/**
	 * MO_LDAP_Role_Mapping : Contains function to perform operations on WordPress user role.
	 */
	class MO_LDAP_Role_Mapping {

		/**
		 * Function mo_ldap_local_update_role_mapping : Update User WP Role
		 *
		 * @param  mixed $user_id : WordPress user id.
		 * @param  mixed $new_registered_user : If user is registered first time.
		 * @return void
		 */
		public function mo_ldap_local_update_role_mapping( $user_id, $new_registered_user ) {
			if ( 1 === $user_id ) {
				return;
			}

			$roles               = 0;
			$wpuser              = new WP_User( $user_id );
			$default_role        = ! empty( get_option( 'mo_ldap_local_mapping_value_default' ) ) ? get_option( 'mo_ldap_local_mapping_value_default' ) : 'subscriber';
			$keep_existing_roles = get_option( 'mo_ldap_local_keep_existing_user_roles' );

			if ( 0 === $roles ) {
				if ( isset( $default_role ) ) {
					if ( strcasecmp( $keep_existing_roles, '1' ) === 0 && ! $new_registered_user ) {
						$wpuser->add_role( $default_role );
					} else {
						$wpuser->set_role( $default_role );
					}
				}
			}
		}
	}
}
