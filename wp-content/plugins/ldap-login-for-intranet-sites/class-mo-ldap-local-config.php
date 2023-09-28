<?php
/**
 * This file stores the configuration functions used all over the plugin.
 *
 * @package miniOrange_LDAP_AD_Integration
 * @subpackage Main
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
// Adding required files.
require_once 'class-mo-ldap-auth-response.php';
if ( ! class_exists( 'MO_LDAP_Local_Config' ) ) {
	/**
	 * MO_LDAP_Local_Config : Class for the all the plugin configuration functions.
	 */
	class MO_LDAP_Local_Config {
		/**
		 * Function ldap_login : performs ldap authentication upon login.
		 *
		 * @param string $username Username.
		 * @param string $password Password.
		 * @return object
		 */
		public function ldap_login( $username, $password ) {
			$username = stripcslashes( $username );
			$password = stripcslashes( $password );

			if ( ! MO_LDAP_Utility::is_extension_installed( 'ldap' ) ) {
				$auth_response                 = new MO_LDAP_Auth_Response();
				$auth_response->status         = false;
				$auth_response->status_message = 'LDAP_ERROR';
				$auth_response->user_dn        = '';
				return $auth_response;

			}
			if ( ! MO_LDAP_Utility::is_extension_installed( 'openssl' ) ) {
				$auth_response                 = new MO_LDAP_Auth_Response();
				$auth_response->status         = false;
				$auth_response->status_message = 'OPENSSL_ERROR';
				$auth_response->user_dn        = '';
				return $auth_response;
			}

			$ldapconn = $this->get_connection();
			if ( $ldapconn ) {
				$filter             = get_option( 'mo_ldap_local_search_filter' ) ? MO_LDAP_Utility::decrypt( get_option( 'mo_ldap_local_search_filter' ) ) : '';
				$search_base_string = get_option( 'mo_ldap_local_search_base' ) ? MO_LDAP_Utility::decrypt( get_option( 'mo_ldap_local_search_base' ) ) : '';
				$ldap_bind_dn       = get_option( 'mo_ldap_local_server_dn' ) ? MO_LDAP_Utility::decrypt( get_option( 'mo_ldap_local_server_dn' ) ) : '';
				$ldap_bind_password = get_option( 'mo_ldap_local_server_password' ) ? MO_LDAP_Utility::decrypt( get_option( 'mo_ldap_local_server_password' ) ) : '';

				$email_attribute         = strtolower( get_option( 'mo_ldap_local_email_attribute' ) );
				$search_filter_attribute = strtolower( get_option( 'Filter_search' ) );

				$attr = array();
				if ( isset( $email_attribute ) && ! empty( $email_attribute ) ) {
					array_push( $attr, $email_attribute );
				}
				if ( isset( $search_filter_attribute ) && ! empty( $search_filter_attribute ) ) {
					array_push( $attr, $search_filter_attribute );
				}

				$username = ldap_escape( $username, '', LDAP_ESCAPE_FILTER );

				$filter = str_replace( '?', $username, $filter );

				$user_search_result = null;
				$entry              = null;
				$info               = null;
				if ( get_option( 'mo_ldap_local_use_tls' ) ) {
					ldap_start_tls( $ldapconn );
				}
				@ldap_bind( $ldapconn, $ldap_bind_dn, $ldap_bind_password ); //phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged -- Used to silense LDAP error; hanlded them below using MO_LDAP_Auth_Response class
				$error_no = ldap_errno( $ldapconn );
				$err      = ldap_error( $ldapconn );
				if ( -1 === $error_no ) {
					$auth_response                 = new MO_LDAP_Auth_Response();
					$auth_response->status         = false;
					$auth_response->status_message = 'LDAP_PING_ERROR';
					$auth_response->user_dn        = '';
					return $auth_response;
				} elseif ( 0 !== strcasecmp( $err, 'success' ) ) {
					$auth_response                 = new MO_LDAP_Auth_Response();
					$auth_response->status         = false;
					$auth_response->status_message = 'LDAP_BIND_ERROR';
					$auth_response->user_dn        = '';
					return $auth_response;
				}

				if ( ldap_search( $ldapconn, $search_base_string, $filter, $attr ) ) {
					$user_search_result = ldap_search( $ldapconn, $search_base_string, $filter, $attr );
				} else {
					$auth_response                 = new MO_LDAP_Auth_Response();
					$auth_response->status         = false;
					$auth_response->status_message = 'LDAP_USER_SEARCH_ERROR';
					$auth_response->user_dn        = '';
					return $auth_response;
				}
				$info  = ldap_first_entry( $ldapconn, $user_search_result );
				$entry = ldap_get_entries( $ldapconn, $user_search_result );

				if ( $info ) {
					$user_dn = ldap_get_dn( $ldapconn, $info );
				} else {
					$auth_response                 = new MO_LDAP_Auth_Response();
					$auth_response->status         = false;
					$auth_response->status_message = 'LDAP_USER_NOT_EXIST';
					$auth_response->user_dn        = '';
					return $auth_response;
				}
				$authentication_response = $this->authenticate( $user_dn, $password );
				if ( strcasecmp( $authentication_response->status_message, 'LDAP_USER_BIND_SUCCESS' ) === 0 ) {
					$attributes_array   = array();
					$profile_attributes = array();

					unset( $attr[0] );

					$authentication_response->attribute_list = $attributes_array;

					if ( ! empty( $email_attribute ) && isset( $entry[0][ $email_attribute ][0] ) ) {
						$profile_attributes['mail'] = $entry[0][ $email_attribute ][0];
					}

					$authentication_response->profile_attributes_list = $profile_attributes;
				}
				return $authentication_response;
			} else {
				$auth_response                 = new MO_LDAP_Auth_Response();
				$auth_response->status         = false;
				$auth_response->status_message = 'ERROR';
				$auth_response->user_dn        = '';
				return $auth_response;
			}

		}

		/**
		 * Function test_connection : Test connection with ldap.
		 *
		 * @return string
		 */
		public function test_connection() {

			$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';

			if ( ! MO_LDAP_Utility::is_extension_installed( 'ldap' ) ) {
				return wp_json_encode(
					array(
						'statusCode'    => 'LDAP_ERROR',
						'statusMessage' => '<a target="_blank" rel="noopener" href="http://php.net/manual/en/ldap.installation.php">PHP LDAP extension</a> is not installed or disabled. Please enable it.',
					)
				);
			} elseif ( ! MO_LDAP_Utility::is_extension_installed( 'openssl' ) ) {
				return wp_json_encode(
					array(
						'statusCode'    => 'OPENSSL_ERROR',
						'statusMessage' => '<a target="_blank" rel="noopener" href="http://php.net/manual/en/openssl.installation.php">PHP OpenSSL extension</a> is not installed or disabled. Please enable it.',
					)
				);
			}

			delete_option( 'mo_ldap_local_server_url_status' );
			delete_option( 'mo_ldap_local_service_account_status' );
			$server_name = get_option( 'mo_ldap_local_server_url' ) ? MO_LDAP_Utility::decrypt( get_option( 'mo_ldap_local_server_url' ) ) : '';

			$ldapconn = $this->get_connection();
			if ( $ldapconn ) {
				$ldap_bind_dn       = get_option( 'mo_ldap_local_server_dn' ) ? MO_LDAP_Utility::decrypt( get_option( 'mo_ldap_local_server_dn' ) ) : '';
				$ldap_bind_password = get_option( 'mo_ldap_local_server_password' ) ? MO_LDAP_Utility::decrypt( get_option( 'mo_ldap_local_server_password' ) ) : '';
				if ( get_option( 'mo_ldap_local_use_tls' ) ) {
					ldap_start_tls( $ldapconn );

				}
				@ldap_bind( $ldapconn, $ldap_bind_dn, $ldap_bind_password ); //phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged -- Used to silense LDAP error; hanlded them below using MO_LDAP_Auth_Response class
				$error_no = ldap_errno( $ldapconn );
				$err      = ldap_error( $ldapconn );
				if ( -1 === $error_no ) {
					add_option( 'mo_ldap_local_server_url_status', 'INVALID', '', 'no' );
					add_option( 'mo_ldap_local_service_account_status', 'INVALID', '', 'no' );
					$troubleshooting_url = add_query_arg( array( 'tab' => 'troubleshooting' ), $request_uri );
					if ( strpos( $server_name, 'ldaps' ) !== false ) {
						return wp_json_encode(
							array(
								'statusCode'    => 'PING_ERROR',
								'statusMessage' => 'Cannot connect to LDAP Server. It seems that you are trying <strong>ldaps</strong> connection. <br>1. Make sure you have gone go through the configuration steps mentioned in our <a href="https://www.miniorange.com/guide-to-setup-ldaps-on-windows-server" rel="noopener" target="_blank">LDAPS document</a> to connect with LDAP server over LDAPS (LDAP over SSL:636). <br>2. Make sure you have entered correct LDAP server hostname or IP address and if there is a firewall, please open the firewall to allow incoming requests to your LDAP server from your WordPress site IP address and below specified port number.<br> You can also check our <a href=' . esc_url( $troubleshooting_url ) . '>Troubleshooting</a> steps. If you still face the same issue then contact us using the support form below.',
							)
						);
					} else {
						return wp_json_encode(
							array(
								'statusCode'    => 'PING_ERROR',
								'statusMessage' => 'Cannot connect to LDAP Server. Make sure you have entered correct LDAP server hostname or IP address. <br>If there is a firewall, please open the firewall to allow incoming requests to your LDAP server from your WordPress site IP address and below specified port number. <br>You can also check our <a href=' . esc_url( $troubleshooting_url ) . '>Troubleshooting</a> steps. If you still face the same issue then contact us using the support form below.<br>In case your WordPress site is hosted on a Shared Hosting Environment like WPEngine, Hostinger, SiteGround etc & you facing trouble with LDAP Extension then you can Contact Us from the above Support Form for help.',
							)
						);
					}
				} elseif ( strcasecmp( $err, 'success' ) !== 0 ) {
					add_option( 'mo_ldap_local_server_url_status', 'VALID', '', 'no' );
					add_option( 'mo_ldap_local_service_account_status', 'INVALID', '', 'no' );
					return wp_json_encode(
						array(
							'statusCode'    => 'BIND_ERROR',
							'statusMessage' => 'Connection to LDAP server is Successful but unable to make authenticated bind to LDAP server. Make sure you have provided correct username or password.',
						)
					);
				} else {
					add_option( 'mo_ldap_local_server_url_status', 'VALID', '', 'no' );
					add_option( 'mo_ldap_local_service_account_status', 'VALID', '', 'no' );
					return wp_json_encode(
						array(
							'statusCode'    => 'BIND_SUCCESS',
							'statusMessage' => 'Connection was established successfully and your configuration has been saved. Please configure LDAP User Mapping now.',
						)
					);
				}
			} else {
				add_option( 'mo_ldap_local_service_account_status', 'INVALID', '', 'no' );
				add_option( 'mo_ldap_local_server_url_status', 'INVALID', '', 'no' );
				$troubleshooting_url = add_query_arg( array( 'tab' => 'troubleshooting' ), $request_uri );
				return wp_json_encode(
					array(
						'statusCode'    => 'ERROR',
						'statusMessage' => 'There was an error in connecting to LDAP Server with the current settings. Make sure you have entered correct LDAP server hostname or IP address and if there is a firewall, please open the firewall to allow incoming requests to your LDAP server from your WordPress site IP address and below specified port number. You can also check our <a href=' . esc_url( $troubleshooting_url ) . '>Troubleshooting</a> steps. If you still face the same issue then contact us using the support form below.',
					)
				);
			}
		}

		/**
		 * Function test_authentication : Test authentication for the ldap user.
		 *
		 * @param string $username Username.
		 * @param string $password Password.
		 * @return string
		 */
		public function test_authentication( $username, $password ) {
			if ( ! MO_LDAP_Utility::is_extension_installed( 'ldap' ) ) {
				return wp_json_encode(
					array(
						'statusCode'    => 'LDAP_ERROR',
						'statusMessage' => '<a target="_blank" rel="noopener" href="http://php.net/manual/en/ldap.installation.php">PHP LDAP extension</a> is not installed or disabled. Please enable it.',
					)
				);
			} elseif ( ! MO_LDAP_Utility::is_extension_installed( 'openssl' ) ) {
				return wp_json_encode(
					array(
						'statusCode'    => 'OPENSSL_ERROR',
						'statusMessage' => '<a target="_blank" rel="noopener" href="http://php.net/manual/en/openssl.installation.php">PHP OpenSSL extension</a> is not installed or disabled. Please enable it.',
					)
				);
			}

			$local_server_url_status = get_option( 'mo_ldap_local_server_url_status' );
			if ( strcasecmp( $local_server_url_status, 'INVALID' ) === 0 ) {
				delete_option( 'mo_ldap_local_server_url_status' );
				delete_option( 'mo_ldap_local_service_account_status' );
				add_option( 'mo_ldap_local_server_url_status', 'INVALID', '', 'no' );
				return wp_json_encode(
					array(
						'statusCode'    => 'LDAP_LOCAL_SERVER_NOT_CONFIGURED',
						'statusMessage' => 'Make sure you have successfully configured the <strong> LDAP connection information </strong>',
					)
				);
			}
			delete_option( 'mo_ldap_local_user_mapping_status' );
			delete_option( 'mo_ldap_local_username_status' );
			delete_option( 'mo_ldap_local_password_status' );
			$auth_response = $this->ldap_login( $username, $password );
			if ( strcasecmp( $auth_response->status_message, 'LDAP_USER_BIND_SUCCESS' ) === 0 ) {
					add_option( 'mo_ldap_local_server_url_status', 'VALID', '', 'no' );
					add_option( 'mo_ldap_local_service_account_status', 'VALID', '', 'no' );
					add_option( 'mo_ldap_local_user_mapping_status', 'VALID', '', 'no' );
					add_option( 'mo_ldap_local_username_status', 'VALID', '', 'no' );
					add_option( 'mo_ldap_local_password_status', 'VALID', '', 'no' );
				return wp_json_encode(
					array(
						'statusCode'    => 'LDAP_USER_BIND_SUCCESS',
						'statusMessage' => 'You have successfully configured your LDAP settings.',
					)
				);
			} elseif ( strcasecmp( $auth_response->status_message, 'LDAP_USER_BIND_ERROR' ) === 0 ) {
					add_option( 'mo_ldap_local_user_mapping_status', 'VALID', '', 'no' );
					add_option( 'mo_ldap_local_username_status', 'VALID', '', 'no' );
					add_option( 'mo_ldap_local_password_status', 'INVALID', '', 'no' );
				return wp_json_encode(
					array(
						'statusCode'    => 'LDAP_USER_BIND_ERROR',
						'statusMessage' => 'User found in the LDAP server but entered password is invalid. Please check your password.',
					)
				);
			} elseif ( strcasecmp( $auth_response->status_message, 'LDAP_USER_SEARCH_ERROR' ) === 0 ) {
					add_option( 'mo_ldap_local_user_mapping_status', 'INVALID', '', 'no' );
					add_option( 'mo_ldap_local_username_status', 'INVALID', '', 'no' );
					add_option( 'mo_ldap_local_password_status', 'INVALID', '', 'no' );
				return wp_json_encode(
					array(
						'statusCode'    => 'LDAP_USER_SEARCH_ERROR',
						'statusMessage' => 'Error while searching user in LDAP server.',
					)
				);
			} elseif ( strcasecmp( $auth_response->status_message, 'LDAP_USER_NOT_EXIST' ) === 0 ) {
					add_option( 'mo_ldap_local_user_mapping_status', 'INVALID', '', 'no' );
					add_option( 'mo_ldap_local_username_status', 'INVALID', '', 'no' );
					add_option( 'mo_ldap_local_password_status', 'INVALID', '', 'no' );
				return wp_json_encode(
					array(
						'statusCode'    => 'LDAP_USER_NOT_EXIST',
						'statusMessage' => 'Cannot find user <strong>' . esc_attr( $username ) . '</strong> in the directory.<br>Possible reasons:<br>1. The <strong>search base</strong> DN is typed incorrectly. Please verify if that search base is present.<br>2. User is not present in that search base. The user may be present in the directory but in some other <strong>Search Base DN</strong> and you may have entered a <strong>Search Base DN</strong> where this users is not present.<br>3. <strong>Username Attribute</strong> is incorrect - User is present in the search base but the username you are trying is mapped to a different attribute in the Username Attribute. <br>E.g. You may trying with <strong>email attribute</strong> value and you may have selected <strong>samaccountname attribute</strong> in the configuration. Please make sure that the right attribute is selected in the <strong>Username Attribute</strong> (with which you want the authentication to happen).<br> 4. User is actually not present in the search base. Please make sure that the user is present and test with the right user.',
					)
				);
			} elseif ( strcasecmp( $auth_response->status_message, 'ERROR' ) === 0 ) {
					add_option( 'mo_ldap_local_user_mapping_status', 'INVALID', '', 'no' );
					add_option( 'mo_ldap_local_username_status', 'INVALID', '', 'no' );
					add_option( 'mo_ldap_local_password_status', 'INVALID', '', 'no' );
				return wp_json_encode(
					array(
						'statusCode'    => 'LDAP_USER_SEARCH_ERROR',
						'statusMessage' => 'Error while authenticating user in LDAP server.',
					)
				);
			}
		}

		/**
		 * Function get_connection : Create a connection with the ldap server.
		 *
		 * @return object
		 */
		public function get_connection() {

			if ( ! MO_LDAP_Utility::is_extension_installed( 'openssl' ) ) {
				return null;
			}

			$server_name = get_option( 'mo_ldap_local_server_url' ) ? MO_LDAP_Utility::decrypt( get_option( 'mo_ldap_local_server_url' ) ) : '';

			$ldapconn = ldap_connect( $server_name );
			if ( version_compare( PHP_VERSION, '5.3.0' ) >= 0 ) {
				ldap_set_option( $ldapconn, LDAP_OPT_NETWORK_TIMEOUT, 5 );
			}

			ldap_set_option( $ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3 );
			ldap_set_option( $ldapconn, LDAP_OPT_REFERRALS, 0 );
			return $ldapconn;
		}

		/**
		 * Function authenticate : Performs authentication of a user.
		 *
		 * @param string $user_dn Distinguished name of the user.
		 * @param string $password Password.
		 * @return object
		 */
		public function authenticate( $user_dn, $password ) {

			$ldapconn = $this->get_connection();

			if ( get_option( 'mo_ldap_local_use_tls' ) ) {
				ldap_start_tls( $ldapconn );
			}
			@ldap_bind( $ldapconn, $user_dn, $password ); //phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged -- Used to silense LDAP error; hanlded them below using MO_LDAP_Auth_Response class
			$error_no = ldap_errno( $ldapconn );
			$err      = ldap_error( $ldapconn );

			if ( -1 === $error_no ) {
				$auth_response                 = new MO_LDAP_Auth_Response();
				$auth_response->status         = false;
				$auth_response->status_message = 'LDAP_PING_ERROR';
				$auth_response->user_dn        = '';
				return $auth_response;
			} elseif ( strcasecmp( $err, 'success' ) !== 0 ) {
				$auth_response                 = new MO_LDAP_Auth_Response();
				$auth_response->status         = false;
				$auth_response->status_message = 'LDAP_USER_BIND_ERROR';
				$auth_response->user_dn        = '';
				return $auth_response;
			} else {
				$auth_response                 = new MO_LDAP_Auth_Response();
				$auth_response->status         = true;
				$auth_response->status_message = 'LDAP_USER_BIND_SUCCESS';
				$auth_response->user_dn        = $user_dn;
				return $auth_response;
			}
		}

		/**
		 * Function show_search_bases_list : Display list of all the search bases.
		 *
		 * @return void
		 */
		public function show_search_bases_list() {
			if ( ! MO_LDAP_Utility::is_extension_installed( 'openssl' ) ) {
				return;
			}

			$ldap_bind_dn       = get_option( 'mo_ldap_local_server_dn' ) ? MO_LDAP_Utility::decrypt( get_option( 'mo_ldap_local_server_dn' ) ) : '';
			$ldap_bind_password = get_option( 'mo_ldap_local_server_password' ) ? MO_LDAP_Utility::decrypt( get_option( 'mo_ldap_local_server_password' ) ) : '';

			if ( MO_LDAP_Utility::is_extension_installed( 'ldap' ) ) {

				$ldapconn = $this->get_connection();

				if ( $ldapconn ) {
					@ldap_bind( $ldapconn, $ldap_bind_dn, $ldap_bind_password ); //phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged -- Used to silense LDAP error; hanlded them below using MO_LDAP_Auth_Response class
					$check_ldap_conn = get_option( 'mo_ldap_local_service_account_status' );
					?>
				<style>
					table {
						border-collapse: collapse;
						width: 100%;
					}

					table, th, td {
						border: 1px solid black;
					}

					td {
						padding: 5px;
					}
				</style>
					<?php if ( 'VALID' === $check_ldap_conn ) { ?>
					<div style="color: #3c763d;background-color: #dff0d8; padding:2%;margin-bottom:20px;text-align:center; border:1px solid #AEDB9A; font-size:18pt;">
						List of Search Base(s)
					</div>
					<div style="display:block;text-align:center;margin-bottom:4%;"><img style="width:15%;" alt="Image not found" src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . 'includes/images/green_check.png' ); ?>"/>
					</div>
					<span><strong> &nbsp; &nbsp; Select your Search Base DN from the below Search bases list: </strong></span></br></br>

					<div style="padding:0 3%;">
					<form method="post" action="">
					<table aria-hidden="true">

						<?php

						$previous_search_bases = MO_LDAP_Utility::decrypt( get_option( 'mo_ldap_local_search_base' ) );
						$search_base_list      = array();
						$result                = ldap_read( $ldapconn, '', '(objectclass=*)', array( 'namingContexts' ) );
						$data                  = ldap_get_entries( $ldapconn, $result );
						$count                 = $data[0]['namingcontexts']['count'];
						for ( $i = 0; $i < $count; $i++ ) {
							if ( 0 === $i ) {
								$base_dn = $data[0]['namingcontexts'][ $i ];
							}
							$valuetext = $data[0]['namingcontexts'][ $i ];

							if ( strcasecmp( $valuetext, $previous_search_bases ) === 0 ) {
								echo "<tr><td><input type='radio' class='select_search_bases' name='select_ldap_search_bases[]' value='" . esc_attr( $valuetext ) . "' checked>" . esc_html( $valuetext ) . '</td></tr>';
								array_push( $search_base_list, $data[0]['namingcontexts'][ $i ] );
							} else {
								echo "<tr><td><input type='radio' class='select_search_bases' name='select_ldap_search_bases[]' value='" . esc_attr( $valuetext ) . "'>" . esc_html( $valuetext ) . '</td></tr>';
								array_push( $search_base_list, $data[0]['namingcontexts'][ $i ] );
							}
						}

						$filter      = '(|(objectclass=organizationalUnit)(&(objectClass=top)(cn=users)))';
						$search_attr = array( 'dn', 'ou' );
						$ldapsearch  = ldap_search( $ldapconn, $base_dn, $filter, $search_attr );
						$info        = ldap_get_entries( $ldapconn, $ldapsearch );

						for ( $i = 0; $i < $info['count']; $i++ ) {
							$textvalue = $info[ $i ]['dn'];
							if ( ( strcasecmp( $textvalue, $previous_search_bases ) ) === 0 ) {
								echo "<tr><td><input type='radio' class='select_search_bases' name='select_ldap_search_bases[]' value='" . esc_attr( $textvalue ) . "' checked>" . esc_html( $textvalue ) . '</td></tr>';
								array_push( $search_base_list, $info[ $i ]['dn'] );
							} else {
								echo "<tr><td><input type='radio' class='select_search_bases' name='select_ldap_search_bases[]' value='" . esc_attr( $textvalue ) . "'>" . esc_html( $textvalue ) . '</td></tr>';
								array_push( $search_base_list, $info[ $i ]['dn'] );
							}
						}
						?>
						</table><br>

					<div style="margin:3%;display:block;text-align:center;">
						<table style="border: none; width: 50%" aria-hidden="true">
							<tr style="border: none;"><td style="border: none;"> <input style="padding:1%;height:30px;width:100px;background: #0091CD none repeat scroll 0% 0%;cursor: pointer;font-size:15px;border-width: 1px;border-style: solid;border-radius: 3px;white-space: nowrap;box-sizing: border-box;border-color: #0073AA;box-shadow: 0px 1px 0px rgba(120, 200, 230, 0.6) inset;color: #FFF;" id="submitbase" type="submit" value="Submit" name="submitbase">
								</td>
								<td style="border: none;"> <input
											style="padding:1%;height:30px;width:100px;background: #0091CD none repeat scroll 0% 0%;cursor: pointer;font-size:15px;border-width: 1px;border-style: solid;border-radius: 3px;white-space: nowrap;box-sizing: border-box;border-color: #0073AA;box-shadow: 0px 1px 0px rgba(120, 200, 230, 0.6) inset;color: #FFF;"
											type="button"  id ="searchbase" value="Close" onClick="self.close();"/>
								</td></tr>
						</table>
					</div>
					</form>
						<?php

					} else {
						?>
					<div style="color: #a94442;background-color: #f2dede;padding: 15px;margin-bottom: 20px;text-align:center;border:1px solid #E6B3B2;font-size:18pt;">
						No Search Base(s) Found
					</div>
					<div style="display:block;text-align:center;margin-bottom:4%;"><img style="width:15%;" alt="Image not found" src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . 'includes/images/wrong.png' ); ?>"/>
					</div>
					<br><br><span>Please check :</span>
					<ul>
						<li>If your LDAP server configuration (LDAP server url, Username & Password) is correct.</li>
						<li>If you have successfully saved your LDAP Connection Information.</li>
					</ul><br><br>
					<div style="margin:3%;display:block;text-align:center;">
						<input
								style="margin-top: -45px; padding:1%;width:100px;background: #0091CD none repeat scroll 0% 0%;cursor: pointer;font-size:15px;border-width: 1px;border-style: solid;border-radius: 3px;white-space: nowrap;box-sizing: border-box;border-color: #0073AA;box-shadow: 0px 1px 0px rgba(120, 200, 230, 0.6) inset;color: #FFF;"
								type="button"  id ="searchbase" value="Close" onClick="self.close();"/>
					</div>
					<?php } ?></div>
					<?php
				} else {
					?>
					<div style="color: #a94442;background-color: #f2dede;padding: 15px;margin-bottom: 20px;text-align:center;border:1px solid #E6B3B2;font-size:18pt;">
						No Search Base(s) Found
					</div>
					<div style="display:block;text-align:center;margin-bottom:4%;"><img style="width:15%;" alt="Image not found" src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . 'includes/images/wrong.png' ); ?>"/>
					</div>
					<br><br><span>Please check :</span>
					<ul>
						<li>If your LDAP server configuration (LDAP server url, Username & Password) is correct.</li>
						<li>If you have successfully saved your LDAP Connection Information.</li>
					</ul><br><br>
					<div style="margin:3%;display:block;text-align:center;">
						<input
								style="margin-top: -45px; padding:1%;width:100px;background: #0091CD none repeat scroll 0% 0%;cursor: pointer;font-size:15px;border-width: 1px;border-style: solid;border-radius: 3px;white-space: nowrap;box-sizing: border-box;border-color: #0073AA;box-shadow: 0px 1px 0px rgba(120, 200, 230, 0.6) inset;color: #FFF;"
								type="button"  id ="searchbase" value="Close" onClick="self.close();"/>
					</div>
				<?php } ?></div>
					<?php
			} else {
				?>
			<h2>Search Base(s) List: </h2>
			<div style="color: #a94442;background-color: #f2dede;padding: 15px;margin-bottom: 20px;text-align:center;border:1px solid #E6B3B2;font-size:18pt;">
				No Search Base(s) Found
			</div>
			<div style="display:block;text-align:center;margin-bottom:4%;"><img style="width:15%;" alt="Image not found" src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . 'includes/images/wrong.png' ); ?>"/>
			</div>
			<br>
			<ul>
				<li><span><a target="_blank" rel="noopener" href="http://php.net/manual/en/ldap.installation.php">PHP LDAP extension</a> is not installed or is disabled. Please enable it.</span>
			</ul><br>
			<div style="margin:3%;display:block;text-align:center;">
				<input
						style="padding:1%;width:100px;background: #0091CD none repeat scroll 0% 0%;cursor: pointer;font-size:15px;border-width: 1px;border-style: solid;border-radius: 3px;white-space: nowrap;box-sizing: border-box;border-color: #0073AA;box-shadow: 0px 1px 0px rgba(120, 200, 230, 0.6) inset;color: #FFF;"
						type="button"  id ="searchbase" value="Close" onClick="self.close();"/>
			</div>
				<?php
			}
			if ( isset( $_POST['submitbase'] ) && ( isset( $_REQUEST['_wpnonce'] ) && wp_verify_nonce( sanitize_key( $_REQUEST['_wpnonce'] ), 'searchbaselist_nonce' ) ) ) {
				if ( ! empty( $_POST['select_ldap_search_bases'] ) ) {
					$search_bases = strtolower( isset( $_POST['select_ldap_search_bases'][0] ) ? sanitize_text_field( wp_unslash( $_POST['select_ldap_search_bases'][0] ) ) : '' );
					update_option( 'mo_ldap_local_search_base', MO_LDAP_Utility::encrypt( $search_bases ) );

					echo '<script>window.close();
               	window.onunload = function(){
               	window.opener.location.reload();
            	};
        		</script>';
				} else {
					echo '<span"><script> alert("You have not selected any Search Base.")</script></span>';
				}
			}
			exit();
		}

		/**
		 * Function test_attribute_configuration: Test attribute mapping.
		 *
		 * @param string $username Username.
		 * @return void
		 */
		public function test_attribute_configuration( $username ) {
			if ( ! MO_LDAP_Utility::is_extension_installed( 'openssl' ) ) {
				return;
			}

			if ( MO_LDAP_Utility::is_extension_installed( 'ldap' ) ) {
				$ldap_bind_dn       = get_option( 'mo_ldap_local_server_dn' ) ? MO_LDAP_Utility::decrypt( get_option( 'mo_ldap_local_server_dn' ) ) : '';
				$ldap_bind_password = get_option( 'mo_ldap_local_server_password' ) ? MO_LDAP_Utility::decrypt( get_option( 'mo_ldap_local_server_password' ) ) : '';

				$search_base_string = get_option( 'mo_ldap_local_search_base' ) ? MO_LDAP_Utility::decrypt( get_option( 'mo_ldap_local_search_base' ) ) : '';
				$search_bases       = explode( ';', $search_base_string );
				$search_filter      = get_option( 'mo_ldap_local_search_filter' ) ? MO_LDAP_Utility::decrypt( get_option( 'mo_ldap_local_search_filter' ) ) : '';
				$username           = ldap_escape( $username, '', LDAP_ESCAPE_FILTER );
				$search_filter      = str_replace( '?', $username, $search_filter );

				$email_attribute = strtolower( get_option( 'mo_ldap_local_email_attribute' ) );
				$attr            = array( $email_attribute );
				$ldapconn        = $this->get_connection();

				if ( $ldapconn ) {

					if ( get_option( 'mo_ldap_local_use_tls' ) ) {
						ldap_start_tls( $ldapconn );
					}
					$count_search_bases = count( $search_bases );
					$bind               = @ldap_bind( $ldapconn, $ldap_bind_dn, $ldap_bind_password ); //phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged -- Used to silense LDAP error; hanlded them below using MO_LDAP_Auth_Response class
					if ( $bind ) {

						for ( $i = 0; $i < $count_search_bases; $i++ ) {
							if ( ldap_search( $ldapconn, $search_bases[ $i ], $search_filter, $attr ) ) {
								$user_search_result = ldap_search( $ldapconn, $search_bases[ $i ], $search_filter, $attr );
								$info               = ldap_first_entry( $ldapconn, $user_search_result );
								$entry              = ldap_get_entries( $ldapconn, $user_search_result );
								if ( $info ) {
									$dn = ldap_get_dn( $ldapconn, $info );
									break;
								}
							}
						}
						?>
				<style>
					table {
						border-collapse: collapse;
						width: 100%;
					}

					table, th, td {
						border: 1px solid black;
				}

					td {
						padding: 5px;
					}
			</style>
			<h2>Attribute Mapping Test : </h2>
			<div style="font-family:Calibri;padding:0 3%;">
						<?php if ( ! empty( $dn ) ) { ?>
					<div style="color: #3c763d;background-color: #dff0d8; padding:2%;margin-bottom:20px;text-align:center; border:1px solid #AEDB9A; font-size:18pt;">
						TEST SUCCESSFUL
					</div>
					<div style="display:block;text-align:center;margin-bottom:4%;"><img style="width:15%;" alt="Image not found" src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . 'includes/images/green_check.png' ); ?>"/>
					</div>
			<table aria-hidden="true">
				<tr>
					<td style='font-weight:bold;border:2px solid #949090;padding:2%;'>User DN</td>
					<td style='padding:2%;border:2px solid #949090; word-wrap:break-word;'><?php echo isset( $dn ) ? esc_html( $dn ) : '<b style="color:red;">No such attribute found.</strong>'; ?></td>
				</tr>
							<?php
							foreach ( $attr as $attribute ) {
								?>
					<tr>
					<td style='font-weight:bold;border:2px solid #949090;padding:2%;min-width:110px;'><?php echo esc_html( $attribute ); ?></td>
					<td style='padding:2%;border:2px solid #949090; word-wrap:break-word;'>
								<?php
								if ( isset( $entry[0][ $attribute ][0] ) ) {
									if ( isset( $entry[0][ $attribute ]['count'] ) ) {
										for ( $i = 0;$i < $entry[0][ $attribute ]['count'];$i++ ) {
											echo esc_attr( $entry[0][ $attribute ][ $i ] ) . '<br>';
										}
									} else {
										echo esc_attr( $entry[0][ $attribute ][0] );
									}
								} else {
									$mo_ldap_local_ldap_email_domain       = get_option( 'mo_ldap_local_email_domain' );
									$mo_ldap_local_ldap_username_attribute = strtolower( get_option( 'mo_ldap_local_username_attribute' ) );
									$custom_ldap_username_attribute        = strtolower( get_option( 'custom_ldap_username_attribute' ) );
									$username_list_array                   = array( 'samaccountname', 'uid' );

									if ( ! empty( $mo_ldap_local_ldap_email_domain ) ) {
										if ( in_array( $mo_ldap_local_ldap_username_attribute, $username_list_array, true ) || in_array( $custom_ldap_username_attribute, $username_list_array, true ) ) {
											$default_email_id = $username . '@' . $mo_ldap_local_ldap_email_domain;
											echo "<strong style='color:red;'>Mail attribute is not set in LDAP server.</strong> <br> As per configured default email domain <span style='color:#0000ff'> " . esc_html( $mo_ldap_local_ldap_email_domain ) . "</span>, following email will be set to the user after successful login.<br> <span style='color:0000ff'> " . esc_html( $default_email_id ) . '</span>';
										} else {
											echo '<strong style="color:red;">Mail attribute is not set in LDAP server.</strong>';
										}
									} else {
										echo '<strong style="color:red;">Mail attribute is not set in LDAP server.</strong>';
									}
								}
								?>
					</td>
					</tr>
								<?php
							}
							?>
			</table>
					<div style="margin:3%;display:block;text-align:center;"><input
								style="padding:1%;width:100px;background: #0091CD none repeat scroll 0% 0%;cursor: pointer;font-size:15px;border-width: 1px;border-style: solid;border-radius: 3px;white-space: nowrap;box-sizing: border-box;border-color: #0073AA;box-shadow: 0px 1px 0px rgba(120, 200, 230, 0.6) inset;color: #FFF;"
								type="button" value="Done" onClick="self.close();"/></div>
			<?php } else { ?>
					<div style="color: #a94442;background-color: #f2dede;padding: 15px;margin-bottom: 20px;text-align:center;border:1px solid #E6B3B2;font-size:18pt;">
						TEST FAILED
					</div>
				<br><br>
				<div style="color: #a94442;font-size:14pt; margin-bottom:20px;display: flex; justify-content: center;">
							<?php
							if ( empty( $search_bases[ $i ] ) ) {
								echo 'ERROR: Please Check your LDAP User mapping configuration.';
							} else {
								echo 'ERROR: User is not found in LDAP server.';
							}
							?>

					</div>
				<br>
					<div style="margin:3%;display:block;text-align:center;"><input
								style="padding:1%;width:100px;background: #0091CD none repeat scroll 0% 0%;cursor: pointer;font-size:15px;border-width: 1px;border-style: solid;border-radius: 3px;white-space: nowrap;box-sizing: border-box;border-color: #0073AA;box-shadow: 0px 1px 0px rgba(120, 200, 230, 0.6) inset;color: #FFF;"
								type="button" value="Close" onClick="self.close();"/></div>

							<?php
			}
					} else {
						?>
				<div style="color: #a94442;background-color: #f2dede;padding: 15px;margin-bottom: 20px;text-align:center;border:1px solid #E6B3B2;font-size:18pt;">
					TEST FAILED
				</div>
				<div style="display:block;text-align:center;margin-bottom:4%;"><img style="width:15%;" alt="Image not found" src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . 'includes/images/wrong.png' ); ?>"/>
				</div>
				<br><br><span>Please check :</span>
				<ul>
					<li>If your LDAP server configuration (LDAP server url, Username & Password) is correct.</li>
					<li>If you have successfully saved your LDAP Connection Information.</li>
				</ul><br><br>
				<div style="margin:3%;display:block;text-align:center;">
					<input
							style="margin-top: -45px; padding:1%;width:100px;background: #0091CD none repeat scroll 0% 0%;cursor: pointer;font-size:15px;border-width: 1px;border-style: solid;border-radius: 3px;white-space: nowrap;box-sizing: border-box;border-color: #0073AA;box-shadow: 0px 1px 0px rgba(120, 200, 230, 0.6) inset;color: #FFF;"
							type="button"  id ="searchbase" value="Close" onClick="self.close();"/>
				</div>
					<?php } ?>

					<?php
					exit();
				} else {
					$info = false;
				}
			} else {
				?>
			<h2>Attribute Mapping Test : </h2>
			<div style="font-family:Calibri;padding:0 3%;">
				<div style="color: #a94442;background-color: #f2dede;padding: 15px;margin-bottom: 20px;text-align:center;border:1px solid #E6B3B2;font-size:18pt;">
					TEST FAILED
				</div>
			<div style="display:block;text-align:center;margin-bottom:4%;"><img style="width:15%;" alt="Image not found" src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . 'includes/images/wrong.png' ); ?>"/>
			</div>
			<br>
			<ul>
				<li><span><a target="_blank" rel="noopener" href="http://php.net/manual/en/ldap.installation.php">PHP LDAP extension</a> is not installed or is disabled. Please enable it.</span>
			</ul><br>
			<div style="margin:3%;display:block;text-align:center;">
				<input
						style="padding:1%;width:100px;background: #0091CD none repeat scroll 0% 0%;cursor: pointer;font-size:15px;border-width: 1px;border-style: solid;border-radius: 3px;white-space: nowrap;box-sizing: border-box;border-color: #0073AA;box-shadow: 0px 1px 0px rgba(120, 200, 230, 0.6) inset;color: #FFF;"
						type="button"  id ="searchbase" value="Close" onClick="self.close();"/>
			</div>
				<?php
				exit();
			}

		}
	}
}
