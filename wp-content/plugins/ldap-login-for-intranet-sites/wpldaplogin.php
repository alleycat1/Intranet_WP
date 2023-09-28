<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName

/**
 * Active Directory Integration for Intranet Sites Plugin
 *
 * This plugin enables to integrate LDAP/AD Authentication and Sync with WordPress site.
 *
 * @package miniOrange_LDAP_AD_Integration
 * @subpackage Main
 */


/**
 * Plugin Name: Active Directory Integration for Intranet Sites
 * Plugin URI: https://miniorange.com
 * Description: Active Directory Integration for Intranet Sites plugin provides login to WordPress using credentials stored in your Active Directory / other LDAP Directory.
 * Author: miniOrange
 * Version: 4.1.11
 * Author URI: https://miniorange.com
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once 'mo-ldap-pages.php';
require_once 'mo-ldap-support-framework.php';
require_once 'class-mo-ldap-customer-setup.php';
require_once 'class-mo-ldap-utility.php';
require_once 'class-mo-ldap-local-config.php';
require_once 'class-mo-ldap-role-mapping.php';
require_once 'mo-ldap-licensing-plans.php';
require_once 'mo-ldap-feedback-form.php';
require_once dirname( __FILE__ ) . '/includes/lib/class-mo-ldap-account-details.php';
require_once dirname( __FILE__ ) . '/includes/lib/class-mo-ldap-config-details.php';
require_once dirname( __FILE__ ) . '/includes/lib/class-mo-ldap-plugin-constants.php';

define(
	'TAB_LDAP_CLASS_NAMES',
	maybe_serialize(
		array(
			'ldap_Login'  => 'MO_LDAP_Account_Details',
			'ldap_config' => 'MO_LDAP_Config_Details',
		)
	)
);

if ( ! class_exists( 'MO_LDAP_Local_Login' ) ) {
	/**
	 * MoLdapLocalLogin : This Class contains function for LDAP login and plugin configuration.
	 */
	class MO_LDAP_Local_Login {

		const LDAPFIELDS = 'All the fields are required. Please enter valid entries.';
		const LDAPCONN   = 'LDAP CONNECTION TEST';

		/**
		 * __construct
		 *
		 * @return void
		 */
		public function __construct() {
			$mo_ldap_login_priority = 7;
			add_option( 'mo_ldap_local_register_user', 1 );
			add_option( 'mo_ldap_local_cust', 0 );
			add_action( 'admin_menu', array( $this, 'mo_ldap_local_login_widget_menu' ) );
			add_action( 'admin_init', array( $this, 'login_widget_save_options' ) );
			add_action( 'init', array( $this, 'test_attribute_configuration' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'mo_ldap_local_settings_style' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'mo_ldap_local_settings_script' ) );
			remove_action( 'admin_notices', array( $this, 'success_message' ) );
			remove_action( 'admin_notices', array( $this, 'error_message' ) );
			register_deactivation_hook( __FILE__, array( $this, 'mo_ldap_local_deactivate' ) );
			add_action( 'show_user_profile', array( $this, 'show_user_profile' ) );

			if ( in_array( 'next-active-directory-integration/index.php', (array) get_option( 'active_plugins', array() ), true ) ) {
				$mo_ldap_login_priority = 20;
			}
			if ( strcmp( get_option( 'mo_ldap_local_enable_login' ), '1' ) === 0 ) {
				remove_filter( 'authenticate', 'wp_authenticate_username_password', 20, 3 );
				remove_filter( 'authenticate', 'wp_authenticate_email_password', 20, 3 );
				add_filter( 'authenticate', array( $this, 'ldap_login' ), $mo_ldap_login_priority, 3 );
			}

			$version_in_db = get_option( 'mo_ldap_local_current_plugin_version' );

			if ( version_compare( $version_in_db, MO_LDAP_Plugin_Constants::VERSION ) !== 0 ) {
				update_option( 'mo_ldap_local_current_plugin_version', MO_LDAP_Plugin_Constants::VERSION );
			}
			register_activation_hook( __FILE__, array( $this, 'mo_ldap_activate' ) );
			add_action( 'admin_footer', array( $this, 'ldap_feedback_request' ) );
			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'mo_ldap_local_links' ) );
		}

		/**
		 * Function mo_ldap_local_links : Returns URL links used in plugin menu
		 *
		 * @param  array $links : Default Links present in plugin menu.
		 * @return array
		 */
		public function mo_ldap_local_links( $links ) {
			$links = array_merge(
				array(
					'<a href="' . esc_url( admin_url( '?page=mo_ldap_local_login' ) ) . '">' . __( 'Settings', 'mo_ldap_local_login' ) . '</a>',
					'<a href="' . esc_url( admin_url( '?page=mo_ldap_local_login&tab=pricing' ) ) . '">' . __( 'Upgrade to Premium', 'mo_ldap_local_login&tab=pricing' ) . '</a>',
				),
				$links
			);
			return $links;
		}

		/**
		 * Function ldap_feedback_request : Return feedback form html invoked during deactivation.
		 *
		 * @return void
		 */
		public function ldap_feedback_request() {
			display_ldap_feedback_form();
		}

		/**
		 * Function show_user_profile : Show User's LDAP Profile Attribute
		 *
		 * @param  mixed $user : WordPress User Object.
		 * @return void
		 */
		public function show_user_profile( $user ) {
			if ( $this->is_administrator_user( $user ) ) {
				?>
				<h3>Extra profile information</h3>

				<table class="form-table" aria-hidden="true">

					<tr>
						<td><strong><label for="user_dn">User DN</label></strong></td>

						<td>
							<strong><?php echo esc_html( get_the_author_meta( 'mo_ldap_user_dn', $user->ID ) ); ?></strong></td>
					</tr>
				</table>

				<?php
			}
		}

		/**
		 * Function ldap_login : LDAP Login hook
		 *
		 * @param  mixed  $wpuser : WordPress user object.
		 * @param  string $username : LDAP username.
		 * @param  string $password : LDAP password.
		 * @return mixed
		 */
		public function ldap_login( $wpuser, $username, $password ) {
			if ( empty( $username ) || empty( $password ) ) {
				$error = new WP_Error();

				if ( empty( $username ) ) {
					$error->add( 'empty_username', __( '<strong>ERROR</strong>: Email field is empty.' ) );
				}

				if ( empty( $password ) ) {
					$error->add( 'empty_password', __( '<strong>ERROR</strong>: Password field is empty.' ) );
				}
				return $error;
			}

			$enable_wp_admin_login = get_option( 'mo_ldap_local_enable_admin_wp_login' );
			if ( strcmp( $enable_wp_admin_login, '1' ) === 0 && username_exists( $username ) ) {
					$user = get_user_by( 'login', $username );
				if ( $user && $this->is_administrator_user( $user ) && wp_check_password( $password, $user->data->user_pass, $user->ID ) ) {
						return $user;
				}
			}

			$mo_ldap_local_ldap_email_domain       = get_option( 'mo_ldap_local_email_domain' );
			$mo_ldap_local_ldap_username_attribute = strtolower( get_option( 'mo_ldap_local_username_attribute' ) );
			$custom_ldap_username_attribute        = strtolower( get_option( 'custom_ldap_username_attribute' ) );
			$username_list_array                   = array( 'samaccountname', 'uid' );

			$mo_ldap_config = new MO_LDAP_Local_Config();
			$auth_response  = $mo_ldap_config->ldap_login( $username, $password );

			if ( strcasecmp( $auth_response->status_message, 'LDAP_USER_BIND_SUCCESS' ) === 0 ) {

				if ( username_exists( $username ) || email_exists( $username ) ) {
					$user = get_user_by( 'login', $username );
					if ( empty( $user ) ) {
						$user = get_user_by( 'email', $username );
					}
					if ( empty( $user ) ) {
						$this->mo_ldap_report_update( $username, 'ERROR', '<strong>Login Error:</strong> Invalid Username/Password combination' );
						$error = new WP_Error();
						$error->add( 'error_fetching_user', __( '<strong>ERROR</strong>: Invalid Username/Password combination.' ) );
						return $error;
					}

					if ( get_option( 'mo_ldap_local_enable_role_mapping' ) ) {
						$new_registered_user  = false;
						$mo_ldap_role_mapping = new MO_LDAP_Role_Mapping();
						$mo_ldap_role_mapping->mo_ldap_local_update_role_mapping( $user->ID, $new_registered_user );
					}

					update_user_meta( $user->ID, 'mo_ldap_user_dn', $auth_response->user_dn, false );

					$profile_attributes = $auth_response->profile_attributes_list;

					$user_data['ID'] = $user->ID;
					if ( ! empty( $profile_attributes['mail'] ) ) {
						$user_data['user_email'] = $profile_attributes['mail'];
					}

					if ( empty( $profile_attributes['mail'] ) && ! empty( $mo_ldap_local_ldap_email_domain ) ) {
						if ( in_array( $mo_ldap_local_ldap_username_attribute, $username_list_array, true ) || in_array( $custom_ldap_username_attribute, $username_list_array, true ) ) {
							$user_data['user_email'] = $username . '@' . $mo_ldap_local_ldap_email_domain;
						}
					}

					wp_update_user( $user_data );
					return $user;
				} else {

					if ( ! get_option( 'mo_ldap_local_register_user' ) ) {
						$this->mo_ldap_report_update( $username, 'ERROR', '<strong>Login Error:</strong> Your Administrator has not enabled Auto Registration. Please contact your Administrator.' );
						$error = new WP_Error();
						$error->add( 'registration_disabled_error', __( '<strong>ERROR</strong>: Your Administrator has not enabled Auto Registration. Please contact your Administrator.' ) );
						return $error;
					} else {
						$user_password      = wp_generate_password( 10, false );
						$profile_attributes = $auth_response->profile_attributes_list;

						$email = ! empty( $profile_attributes['mail'] ) ? $profile_attributes['mail'] : '';
						if ( empty( $profile_attributes['mail'] ) && ! empty( $mo_ldap_local_ldap_email_domain ) ) {
							if ( in_array( $mo_ldap_local_ldap_username_attribute, $username_list_array, true ) || in_array( $custom_ldap_username_attribute, $username_list_array, true ) ) {
								$email = $username . '@' . $mo_ldap_local_ldap_email_domain;
							}
						}

						$userdata = array(
							'user_login' => $username,
							'user_email' => $email,
							'user_pass'  => $user_password,
						);
						$user_id  = wp_insert_user( $userdata );

						if ( ! is_wp_error( $user_id ) ) {
							$user = get_user_by( 'login', $username );

							update_user_meta( $user->ID, 'mo_ldap_user_dn', $auth_response->user_dn, false );

							if ( get_option( 'mo_ldap_local_enable_role_mapping' ) ) {
								$new_registered_user  = true;
								$mo_ldap_role_mapping = new MO_LDAP_Role_Mapping();
								$mo_ldap_role_mapping->mo_ldap_local_update_role_mapping( $user->ID, $new_registered_user );
							}

							return $user;
						} else {
							$error_string       = $user_id->get_error_message();
							$email_exists_error = 'Sorry, that email address is already used!';
							if ( email_exists( $email ) && strcasecmp( $error_string, $email_exists_error ) === 0 ) {
								$error = new WP_Error();
								$this->mo_ldap_report_update( $username, $auth_response->status_message, '<strong>Login Error:</strong> There was an error registering your account. The email is already registered, please choose another one and try again.' );
								$error->add( 'registration_error', __( '<strong>ERROR</strong>: There was an error registering your account. The email is already registered, please choose another one and try again.' ) );
								return $error;
							} else {
								$error = new WP_Error();
								$this->mo_ldap_report_update( $username, $auth_response->status_message, '<strong>Login Error:</strong> There was an error registering your account. Please try again.' );
								$error->add( 'registration_error', __( '<strong>ERROR</strong>: There was an error registering your account. Please try again.' ) );
								return $error;
							}
						}
					}
				}
			} elseif ( strcasecmp( $auth_response->status_message, 'LDAP_USER_BIND_ERROR' ) === 0 || strcasecmp( $auth_response->status_message, 'LDAP_USER_NOT_EXIST' ) === 0 ) {
				$this->mo_ldap_report_update( $username, $auth_response->status_message, '<strong>Login Error:</strong> Invalid username or password entered.' );
				$error = new WP_Error();
				$error->add( 'LDAP_USER_BIND_ERROR', __( '<strong>ERROR</strong>: Invalid username or password entered.' ) );
				return $error;
			} elseif ( strcasecmp( $auth_response->status_message, 'LDAP_ERROR' ) === 0 ) {
				$this->mo_ldap_report_update( $username, $auth_response->status_message, '<strong>Login Error:</strong> <a target="_blank" rel="noopener" href="http://php.net/manual/en/ldap.installation.php">PHP LDAP extension</a> is not installed or disabled. Please enable it.' );
				$error = new WP_Error();
				$error->add( 'LDAP_ERROR', __( '<strong>ERROR</strong>: <a target="_blank" rel="noopener" href="http://php.net/manual/en/ldap.installation.php">PHP LDAP extension</a> is not installed or disabled. Please enable it.' ) );
				return $error;
			} elseif ( strcasecmp( $auth_response->status_message, 'OPENSSL_ERROR' ) === 0 ) {
				$this->mo_ldap_report_update( $username, $auth_response->status_message, '<strong>Login Error:</strong> <a target="_blank" rel="noopener" href="http://php.net/manual/en/openssl.installation.php">PHP OpenSSL extension</a> is not installed or disabled.' );
				$error = new WP_Error();
				$error->add( 'OPENSSL_ERROR', __( '<strong>ERROR</strong>: <a target="_blank" rel="noopener" href="http://php.net/manual/en/openssl.installation.php">PHP OpenSSL extension</a> is not installed or disabled.' ) );
				return $error;
			} elseif ( strcasecmp( $auth_response->status_message, 'LDAP_PING_ERROR' ) === 0 ) {
				$this->mo_ldap_report_update( $username, $auth_response->status_message, '<strong>Login Error: </strong> LDAP server is not responding ' );
				$error = new WP_Error();
				$error->add( 'LDAP_PING_ERROR', __( '<strong>ERROR</strong>:LDAP server is not reachable. Fallback to local WordPress authentication is not supported.' ) );
			} else {
				$error = new WP_Error();
				$this->mo_ldap_report_update( $username, $auth_response->status_message, '<strong>Login Error:</strong> Unknown error occurred during authentication. Please contact your administrator.' );
				$error->add( 'UNKNOWN_ERROR', __( '<strong>ERROR</strong>: Unknown error occurred during authentication. Please contact your administrator.' ) );
				return $error;
			}
		}

		/**
		 * Function mo_ldap_local_login_widget_menu : To menu items in the plugin.
		 *
		 * @return void
		 */
		public function mo_ldap_local_login_widget_menu() {
			add_menu_page( 'LDAP/AD Login for Intranet', 'LDAP/AD Login for Intranet', 'activate_plugins', 'mo_ldap_local_login', array( $this, 'mo_ldap_local_login_widget_options' ), plugin_dir_url( __FILE__ ) . 'includes/images/miniorange_icon.png' );
			add_submenu_page( 'mo_ldap_local_login', 'LDAP/AD plugin', 'Licensing Plans', 'manage_options', 'mo_ldap_local_login&amp;tab=pricing', array( $this, 'mo_ldap_show_licensing_page' ) );

		}

		/**
		 * Function mo_ldap_local_login_widget_options : Load plugin widget options
		 *
		 * @return void
		 */
		public function mo_ldap_local_login_widget_options() {
			update_option( 'mo_ldap_local_host_name', 'https://login.xecurify.com' );
			mo_ldap_local_settings();
		}

		/**
		 * Function checkPasswordpattern
		 *
		 * @param  string $password : password pattern to be checked.
		 * @return string
		 */
		public static function check_password_pattern( $password ) {
			$pattern = '/^[(\w)*(\!\@\#\$\%\^\&\*\.\-\_)*]+$/';

			return ! preg_match( $pattern, $password );
		}

		/**
		 * Function create_customer : To register customer with miniOrange
		 *
		 * @return array
		 */
		public function create_customer() {
			$customer     = new MO_LDAP_Customer_Setup();
			$customer_key = $customer->create_customer();

			$response = array();

			if ( ! empty( $customer_key ) ) {
				$customer_key = json_decode( $customer_key, true );

				if ( strcasecmp( $customer_key['status'], 'CUSTOMER_USERNAME_ALREADY_EXISTS' ) === 0 ) {
					$api_response = $this->get_current_customer();
					if ( $api_response ) {
						$response['status'] = 'SUCCESS';
					} else {
						$response['status'] = 'ERROR';
					}
				} elseif ( strcasecmp( $customer_key['status'], 'SUCCESS' ) === 0 && strpos( $customer_key['message'], 'Customer successfully registered.' ) !== false ) {
					$this->save_success_customer_config( $customer_key['id'], $customer_key['apiKey'], $customer_key['token'], 'Thanks for registering with the miniOrange.' );
					$response['status'] = 'SUCCESS';
					return $response;
				}
				update_option( 'mo_ldap_local_password', '' );
				return $response;
			}
		}

		/**
		 * Function get_current_customer : Get current customer info.
		 *
		 * @return void
		 */
		public function get_current_customer() {
			$customer = new MO_LDAP_Customer_Setup();
			$content  = $customer->get_customer_key();

			$response = array();

			if ( ! empty( $content ) ) {
				$customer_key = json_decode( $content, true );
				if ( json_last_error() === JSON_ERROR_NONE ) {
					$this->save_success_customer_config( $customer_key['id'], $customer_key['apiKey'], $customer_key['token'], 'Your account has been retrieved successfully.' );
					update_option( 'mo_ldap_local_password', '' );
					$response['status'] = 'SUCCESS';
				} else {
					update_option( 'mo_ldap_local_message', 'You already have an account with miniOrange. Please enter a valid password.' );
					$this->show_error_message();
				}
			}
		}

		/**
		 * Function login_widget_save_options : Handler function for PHP forms.
		 *
		 * @return void
		 */
		public function login_widget_save_options() {
			$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';

			if ( ( ! empty( get_option( 'user_logs_table_exists' ) ) || strcasecmp( get_option( 'user_logs_table_exists' ), '1' ) === 0 ) && ( empty( get_option( 'mo_ldap_local_user_table_updated' ) ) || strcasecmp( get_option( 'mo_ldap_local_user_table_updated' ), 'true' ) !== 0 ) ) {
				MO_LDAP_Utility::update_user_auth_table_headers();
				update_option( 'mo_ldap_local_user_table_updated', 'true' );
			}

			if ( isset( $_POST['option'] ) && current_user_can( 'manage_options' ) ) {
				$post_option = sanitize_text_field( wp_unslash( $_POST['option'] ) );

				if ( strcmp( $post_option, 'mo_ldap_local_register_customer' ) === 0 && check_admin_referer( 'mo_ldap_local_register_customer' ) ) {

					$company = isset( $_POST['company'] ) ? sanitize_text_field( wp_unslash( $_POST['company'] ) ) : '';
					if ( empty( $company ) ) {
						$company = isset( $_SERVER['SERVER_NAME'] ) ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_NAME'] ) ) : '';
					}
					$phone            = isset( $_POST['register_phone'] ) ? sanitize_text_field( wp_unslash( $_POST['register_phone'] ) ) : '';
					$email            = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
					$password         = isset( $_POST['password'] ) ? $_POST['password'] : ''; //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash -- Should not be sanitised as Strong Passwords contains special characters
					$confirm_password = isset( $_POST['confirmPassword'] ) ? $_POST['confirmPassword'] : ''; //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash -- Should not be sanitised as Strong Passwords contains special characters
					$use_case         = isset( $_POST['usecase'] ) ? sanitize_text_field( wp_unslash( $_POST['usecase'] ) ) : '';

					if ( empty( $email ) || empty( $password ) ) {
						update_option( 'mo_ldap_local_message', self::LDAPFIELDS );
						$this->show_error_message();
						return;
					} elseif ( ! filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
						update_option( 'mo_ldap_local_message', 'Please enter a valid email address.' );
						$this->show_error_message();
						return;
					} elseif ( $this->check_password_pattern( wp_strip_all_tags( $password ) ) ) {
						update_option( 'mo_ldap_local_message', 'Minimum 6 characters should be present. Maximum 15 characters should be present. Only following symbols (!@#.$%^&*-_) should be present.' );
						$this->show_error_message();
						return;
					}

					update_option( 'mo_ldap_local_admin_company', $company );
					update_option( 'mo_ldap_local_admin_phone', $phone );
					update_option( 'mo_ldap_local_admin_email', $email );

					if ( strcmp( $password, $confirm_password ) === 0 ) {
						update_option( 'mo_ldap_local_password', $password );
						$customer = new MO_LDAP_Customer_Setup();
						$content  = $customer->check_customer();

						if ( ! empty( $content ) ) {
							$content = json_decode( $content, true );

							if ( strcasecmp( $content['status'], 'CUSTOMER_NOT_FOUND' ) === 0 ) {
								$content = $this->create_customer();
								if ( is_array( $content ) && array_key_exists( 'status', $content ) && strcasecmp( $content['status'], 'SUCCESS' ) === 0 ) {
									$pricing_url      = add_query_arg( array( 'tab' => 'pricing' ), $request_uri );
									$message          = 'Your account has been created successfully. <a href="' . esc_url( $pricing_url ) . '">Click here to see our Premium Plans</a> ';
									$registered_email = get_option( 'mo_ldap_local_admin_email' );
									$query            = 'Phone Number :' . $phone . '<br><br>Query: A new LDAP customer has been registered with miniOrange. <br><br>Use Case: ' . $use_case;
									$subject          = 'WordPress LDAP Customer Registered - ' . $registered_email;
									$customer->send_email_alert( $subject, $registered_email, $query, $company );
									update_option( 'mo_ldap_local_message', $message );
									$this->show_success_message();
									return;
								}
							} else {
								$response = $this->get_current_customer();
								if ( is_array( $response ) && array_key_exists( 'status', $response ) && strcasecmp( $response['status'], 'SUCCESS' ) === 0 ) {
									$pricing_url = add_query_arg( array( 'tab' => 'pricing' ), $request_uri );
									$message     = 'Your account has been retrieved successfully. <a href="' . esc_url( $pricing_url ) . '">Click here to see our Premium Plans</a> ';
									update_option( 'mo_ldap_local_message', $message );
									$this->show_success_message();
									return;
								}
							}
						}
					} else {
						update_option( 'mo_ldap_local_message', 'Password and Confirm password do not match.' );
						delete_option( 'mo_ldap_local_verify_customer' );
						$this->show_error_message();
						return;
					}
				} elseif ( strcmp( $post_option, 'mo_ldap_local_verify_customer' ) === 0 && check_admin_referer( 'mo_ldap_local_verify_customer' ) ) {
					$email    = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
					$password = isset( $_POST['password'] ) ? $_POST['password'] : ''; //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash -- Should not be sanitised as Strong Passwords contains special characters

					if ( empty( $email ) || empty( $_POST['password'] ) ) {
						update_option( 'mo_ldap_local_message', self::LDAPFIELDS );
						$this->show_error_message();
						return;
					}

					update_option( 'mo_ldap_local_admin_email', $email );
					update_option( 'mo_ldap_local_password', $password );

					$customer = new MO_LDAP_Customer_Setup();
					$content  = $customer->get_customer_key();

					if ( ! is_null( $content ) ) {
						$customer_key = json_decode( $content, true );
						if ( json_last_error() === JSON_ERROR_NONE ) {
							$this->save_success_customer_config( $customer_key['id'], $customer_key['apiKey'], $customer_key['token'], 'Your account has been retrieved successfully.' );
						} else {
							$message = 'Invalid username or password. Please try again.';
							update_option( 'mo_ldap_local_message', $message );
							$this->show_error_message();
						}
						update_option( 'mo_ldap_local_password', '' );
					}
				} elseif ( strcmp( $post_option, 'mo_ldap_local_enable' ) === 0 && check_admin_referer( 'mo_ldap_local_enable' ) ) {
					$enable_ldap_login = ( isset( $_POST['enable_ldap_login'] ) && strcmp( sanitize_text_field( wp_unslash( $_POST['enable_ldap_login'] ) ), 1 ) === 0 ) ? 1 : 0;

					update_option( 'mo_ldap_local_enable_login', $enable_ldap_login );
					update_option( 'mo_ldap_local_enable_admin_wp_login', $enable_ldap_login );

					if ( get_option( 'mo_ldap_local_enable_login' ) ) {
						update_option( 'mo_ldap_local_message', 'Login through your LDAP credentials has been enabled. To verify the LDAP configuration, you can <a href="' . esc_url( wp_logout_url( get_permalink() ) ) . '">Logout</a> from WordPress and login again with your LDAP credentials.' );
						$this->show_success_message();
					} else {
						update_option( 'mo_ldap_local_message', 'Login through your LDAP credentials has been disabled.' );
						$this->show_error_message();
					}
				} elseif ( strcmp( $post_option, 'user_report_logs' ) === 0 && check_admin_referer( 'user_report_logs' ) ) {

					$enable_user_report_logs = ( isset( $_POST['mo_ldap_local_user_report_log'] ) && strcmp( sanitize_text_field( wp_unslash( $_POST['mo_ldap_local_user_report_log'] ) ), 1 ) === 0 ) ? 1 : 0;

					update_option( 'mo_ldap_local_user_report_log', $enable_user_report_logs );
					$user_logs_table_exists = get_option( 'user_logs_table_exists' );
					$user_reporting         = get_option( 'mo_ldap_local_user_report_log' );
					if ( strcasecmp( $user_reporting, '1' ) === 0 && strcasecmp( $user_logs_table_exists, '1' ) !== 0 ) {
						$this->prefix_update_table();
					}
				} elseif ( strcmp( $post_option, 'mo_ldap_local_register_user' ) === 0 && check_admin_referer( 'mo_ldap_local_register_user' ) ) {

					$enable_user_auto_register = ( isset( $_POST['mo_ldap_local_register_user'] ) && strcmp( sanitize_text_field( wp_unslash( $_POST['mo_ldap_local_register_user'] ) ), 1 ) === 0 ) ? 1 : 0;

					update_option( 'mo_ldap_local_register_user', $enable_user_auto_register );
					if ( get_option( 'mo_ldap_local_register_user' ) ) {
						update_option( 'mo_ldap_local_message', 'Auto Registering users has been enabled.' );
						$this->show_success_message();
					} else {
						update_option( 'mo_ldap_local_message', 'Auto Registering users has been disabled.' );
						$this->show_error_message();
					}
				} elseif ( strcmp( $post_option, 'mo_ldap_local_save_config' ) === 0 && check_admin_referer( 'mo_ldap_local_save_config' ) ) {
					$server_name         = '';
					$dn                  = '';
					$admin_ldap_password = '';
					if ( empty( $_POST['ldap_server'] ) || empty( $_POST['dn'] ) || empty( $_POST['admin_password'] ) || empty( $_POST['mo_ldap_protocol'] ) || empty( $_POST['mo_ldap_server_port_no'] ) ) {
						update_option( 'mo_ldap_local_message', self::LDAPFIELDS );
						$this->show_error_message();
						return;
					} else {
						$ldap_protocol       = isset( $_POST['mo_ldap_protocol'] ) ? sanitize_text_field( wp_unslash( $_POST['mo_ldap_protocol'] ) ) : '';
						$port_number         = isset( $_POST['mo_ldap_server_port_no'] ) ? sanitize_text_field( wp_unslash( $_POST['mo_ldap_server_port_no'] ) ) : '';
						$server_address      = isset( $_POST['ldap_server'] ) ? sanitize_text_field( wp_unslash( $_POST['ldap_server'] ) ) : '';
						$server_name         = $ldap_protocol . '://' . $server_address . ':' . $port_number;
						$dn                  = isset( $_POST['dn'] ) ? sanitize_text_field( wp_unslash( $_POST['dn'] ) ) : '';
						$admin_ldap_password = isset( $_POST['admin_password'] ) ? $_POST['admin_password'] : ''; //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash -- Should not be sanitised as Strong Passwords contains special characters

					}

					if ( ! MO_LDAP_Utility::is_extension_installed( 'openssl' ) ) {
						update_option( 'mo_ldap_local_message', 'PHP openssl extension is not installed or disabled. Please enable it first.' );
						$this->show_error_message();
					} else {
						$directory_server_value = isset( $_POST['mo_ldap_directory_server_value'] ) ? sanitize_text_field( wp_unslash( $_POST['mo_ldap_directory_server_value'] ) ) : '';
						if ( strcasecmp( $directory_server_value, 'other' ) === 0 ) {
							$directory_server_custom_value = isset( $_POST['mo_ldap_directory_server_custom_value'] ) && ! empty( $_POST['mo_ldap_directory_server_custom_value'] ) ? sanitize_text_field( wp_unslash( $_POST['mo_ldap_directory_server_custom_value'] ) ) : 'other';
							update_option( 'mo_ldap_directory_server_custom_value', $directory_server_custom_value );
						}
						update_option( 'mo_ldap_directory_server_value', $directory_server_value );

						if ( strcasecmp( $directory_server_value, 'msad' ) === 0 ) {
							$directory_server = 'Microsoft Active Directory';
						} elseif ( strcasecmp( $directory_server_value, 'openldap' ) === 0 ) {
										$directory_server = 'OpenLDAP';
						} elseif ( strcasecmp( $directory_server_value, 'freeipa' ) === 0 ) {
													$directory_server = 'FreeIPA';
						} elseif ( strcasecmp( $directory_server_value, 'jumpcloud' ) === 0 ) {
							$directory_server = 'JumpCloud';
						} elseif ( strcasecmp( $directory_server_value, 'other' ) === 0 ) {
							$directory_server = get_option( 'mo_ldap_directory_server_custom_value' );
						} else {
							$directory_server = 'Not Configured';
						}

						update_option( 'mo_ldap_local_directory_server', $directory_server );
						update_option( 'mo_ldap_local_ldap_protocol', $ldap_protocol );
						update_option( 'mo_ldap_local_ldap_server_address', MO_LDAP_Utility::encrypt( $server_address ) );
						if ( strcmp( $ldap_protocol, 'ldap' ) === 0 ) {
							update_option( 'mo_ldap_local_ldap_port_number', $port_number );
						} elseif ( strcmp( $ldap_protocol, 'ldaps' ) === 0 ) {
							update_option( 'mo_ldap_local_ldaps_port_number', $port_number );
						}

						update_option( 'mo_ldap_local_server_url', MO_LDAP_Utility::encrypt( $server_name ) );
						update_option( 'mo_ldap_local_server_dn', MO_LDAP_Utility::encrypt( $dn ) );
						update_option( 'mo_ldap_local_server_password', MO_LDAP_Utility::encrypt( $admin_ldap_password ) );

						delete_option( 'mo_ldap_local_message' );
						update_option( 'refresh', 0 );
						$mo_ldap_config = new MO_LDAP_Local_Config();

						$content  = $mo_ldap_config->test_connection();
						$response = json_decode( $content, true );
						if ( isset( $response['statusCode'] ) && strcasecmp( $response['statusCode'], 'BIND_SUCCESS' ) === 0 ) {
							add_option( 'mo_ldap_local_save_config_status', 'VALID', '', 'no' );
							update_option( 'mo_ldap_local_message', $response['statusMessage'] );
							$this->show_success_message();
						} elseif ( isset( $response['statusCode'] ) && strcasecmp( $response['statusCode'], 'BIND_ERROR' ) === 0 ) {
							$this->mo_ldap_report_update( self::LDAPCONN, 'ERROR', '<strong>Test Connection Error: </strong>' . $response['statusMessage'] );
							update_option( 'mo_ldap_local_message', $response['statusMessage'] );
							$this->show_error_message();
						} elseif ( isset( $response['statusCode'] ) && strcasecmp( $response['statusCode'], 'PING_ERROR' ) === 0 ) {
							$this->mo_ldap_report_update( self::LDAPCONN, 'ERROR', '<strong>Test Connection Error: </strong>Cannot connect to LDAP Server. Make sure you have entered correct LDAP server hostname or IP address.' );
							update_option( 'mo_ldap_local_message', $response['statusMessage'] );
							$this->show_error_message();
						} elseif ( isset( $response['statusCode'] ) && strcasecmp( $response['statusCode'], 'LDAP_ERROR' ) === 0 ) {
							$this->mo_ldap_report_update( self::LDAPCONN, 'ERROR', '<strong>Test Connection Error: </strong>' . $response['statusMessage'] );
							update_option( 'mo_ldap_local_message', $response['statusMessage'] );
							$this->show_error_message();
						} elseif ( isset( $response['statusCode'] ) && strcasecmp( $response['statusCode'], 'OPENSSL_ERROR' ) === 0 ) {
							$this->mo_ldap_report_update( self::LDAPCONN, 'ERROR', '<strong>Test Connection Error: </strong>' . $response['statusMessage'] );
							update_option( 'mo_ldap_local_message', $response['statusMessage'] );
							$this->show_error_message();
						} elseif ( isset( $response['statusCode'] ) && strcasecmp( $response['statusCode'], 'ERROR' ) === 0 ) {
							update_option( 'mo_ldap_local_message', $response['statusMessage'] );
							$this->mo_ldap_report_update( self::LDAPCONN, 'Error', '<strong>Test Connection Error: </strong>' . $response['statusMessage'] );
							$this->show_error_message();
						}
					}
				} elseif ( strcmp( $post_option, 'mo_ldap_local_save_user_mapping' ) === 0 && check_admin_referer( 'mo_ldap_local_save_user_mapping' ) ) {

					if ( ! MO_LDAP_Utility::is_extension_installed( 'ldap' ) ) {
						update_option( 'mo_ldap_local_message', "<a target='_blank' rel='noopener' href='http://php.net/manual/en/ldap.installation.php'>PHP LDAP extension</a> is not installed or disabled. Please enable it." );
						$this->show_error_message();
						return;
					}
					delete_option( 'mo_ldap_local_user_mapping_status' );

					$search_base = isset( $_POST['search_base'] ) ? sanitize_text_field( wp_unslash( $_POST['search_base'] ) ) : '';

					if ( empty( $search_base ) ) {
						update_option( 'mo_ldap_local_message', self::LDAPFIELDS );
						add_option( 'mo_ldap_local_user_mapping_status', 'INVALID', '', 'no' );
						$this->show_error_message();
						return;
					} elseif ( strpos( $search_base, ';' ) ) {
							$message = 'You have entered multiple search bases. Multiple Search Bases are supported in the <strong>Premium version</strong> of the plugin. <a href="https://plugins.miniorange.com/wordpress-ldap-login-intranet-sites" target="_blank">Click here to upgrade</a>.';
							update_option( 'mo_ldap_local_message', $message );
							$this->show_error_message();
							return;
					}

					if ( ! MO_LDAP_Utility::is_extension_installed( 'openssl' ) ) {
						update_option( 'mo_ldap_local_message', 'PHP OpenSSL extension is not installed or disabled. Please enable it first.' );
						add_option( 'mo_ldap_local_user_mapping_status', 'INVALID', '', 'no' );
						$this->show_error_message();
					} else {
						$ldap_username_attribute        = isset( $_POST['ldap_username_attribute'] ) ? sanitize_text_field( wp_unslash( $_POST['ldap_username_attribute'] ) ) : '';
						$custom_ldap_username_attribute = isset( $_POST['custom_ldap_username_attribute'] ) ? sanitize_text_field( wp_unslash( $_POST['custom_ldap_username_attribute'] ) ) : '';

						if ( ! MO_LDAP_Utility::check_empty_or_null( $ldap_username_attribute ) ) {
							update_option( 'mo_ldap_local_username_attribute', $ldap_username_attribute );
							if ( strcasecmp( $ldap_username_attribute, 'custom_ldap_attribute' ) === 0 ) {
								update_option( 'custom_ldap_username_attribute', $custom_ldap_username_attribute );
								if ( MO_LDAP_Utility::check_empty_or_null( $custom_ldap_username_attribute ) ) {
									$directory_server_value = get_option( 'mo_ldap_directory_server_value' );
									if ( strcmp( $directory_server_value, 'openldap' ) === 0 || strcmp( $directory_server_value, 'freeipa' ) === 0 ) {
										$ldap_username_attribute = 'uid';
									} else {
										$ldap_username_attribute = 'samaccountname';
									}
								} else {
									$multiple_username_attributes = explode( ';', $custom_ldap_username_attribute );
									if ( count( $multiple_username_attributes ) > 1 ) {
										$message = 'You have entered multiple attributes for "Username Attribute" field. Logging in with multiple attributes are supported in the <strong>Premium version</strong> of the plugin. <a href="https://plugins.miniorange.com/wordpress-ldap-login-intranet-sites" target="_blank" rel="noopener">Click here to upgrade</a> ';
										update_option( 'mo_ldap_local_message', $message );
										$this->show_error_message();
										return;
									} else {
										$ldap_username_attribute = $custom_ldap_username_attribute;
									}
								}
							}
							$generated_search_filter = '(&(objectClass=*)(' . $ldap_username_attribute . '=?))';
							update_option( 'Filter_search', $ldap_username_attribute );
							update_option( 'mo_ldap_local_search_filter', MO_LDAP_Utility::encrypt( $generated_search_filter ) );
						}

						if ( strcasecmp( $ldap_username_attribute, 'custom_ldap_attribute' ) !== 0 ) {
							update_option( 'custom_ldap_username_attribute', $ldap_username_attribute );
						}
						update_option( 'mo_ldap_local_search_base', MO_LDAP_Utility::encrypt( $search_base ) );
						delete_option( 'mo_ldap_local_message' );
						$message = 'LDAP User Mapping Configuration has been saved. Please proceed for Test Authentication to verify LDAP user authentication.';
						add_option( 'mo_ldap_local_message', $message, '', 'no' );
						add_option( 'mo_ldap_local_user_mapping_status', 'VALID', '', 'no' );
						$this->show_success_message();
						update_option( 'import_flag', 1 );
					}
				} elseif ( strcmp( $post_option, 'mo_ldap_save_attribute_config' ) === 0 && check_admin_referer( 'mo_ldap_save_attribute_config' ) ) {
					$email_attribute         = isset( $_POST['mo_ldap_email_attribute'] ) ? sanitize_text_field( wp_unslash( $_POST['mo_ldap_email_attribute'] ) ) : '';
					$email_domain            = isset( $_POST['mo_ldap_email_domain'] ) ? sanitize_text_field( wp_unslash( $_POST['mo_ldap_email_domain'] ) ) : '';
					$domain_validation_regex = '/^(?:[-A-Za-z0-9]+\.)+[A-Za-z]{2,6}$/';
					if ( ! preg_match( $domain_validation_regex, $email_domain ) && ! empty( $email_domain ) ) {
						update_option( 'mo_ldap_local_message', 'Please enter the domain name in valid format' );
						$this->show_error_message();
					} else {
						update_option( 'mo_ldap_local_email_attribute', $email_attribute );
						update_option( 'mo_ldap_local_email_domain', $email_domain );
						update_option( 'mo_ldap_local_message', 'Successfully saved LDAP Attribute Configuration' );
						$this->show_success_message();
					}
				} elseif ( strcmp( $post_option, 'mo_ldap_local_enable_role_mapping' ) === 0 && check_admin_referer( 'mo_ldap_local_enable_role_mapping' ) ) {
					$enable_role_mapping = ( isset( $_POST['enable_ldap_role_mapping'] ) && strcmp( sanitize_text_field( wp_unslash( $_POST['enable_ldap_role_mapping'] ) ), '1' ) === 0 ) ? 1 : 0;
					update_option( 'mo_ldap_local_enable_role_mapping', $enable_role_mapping );

					$keep_existing_roles = isset( $_POST['keep_existing_user_roles'] ) ? sanitize_text_field( wp_unslash( $_POST['keep_existing_user_roles'] ) ) : 0;
					update_option( 'mo_ldap_local_keep_existing_user_roles', $keep_existing_roles );

					if ( isset( $_POST['mapping_value_default'] ) ) {
						update_option( 'mo_ldap_local_mapping_value_default', sanitize_text_field( wp_unslash( $_POST['mapping_value_default'] ) ) );
					}

					if ( ! get_option( 'mo_ldap_local_enable_role_mapping' ) ) {
						update_option( 'mo_ldap_local_message', 'Your default WordPress role has been saved. Please activate Enable Role Mapping to proceed further.' );
						$this->show_success_message();
					} elseif ( ! get_option( 'mo_ldap_local_keep_existing_user_roles' ) ) {
								update_option( 'mo_ldap_local_message', 'Your role mapping configuration has been saved. Existing roles will be replaced with the selected default role.' );
								$this->show_success_message();
					} else {
						update_option( 'mo_ldap_local_message', 'Your role mapping configuration has been saved. New role will be added to the existing ones.' );
						$this->show_success_message();
					}
				} elseif ( strcmp( $post_option, 'mo_ldap_local_test_auth' ) === 0 && check_admin_referer( 'mo_ldap_local_test_auth' ) ) {
					if ( ! MO_LDAP_Utility::is_extension_installed( 'ldap' ) ) {
						update_option( 'mo_ldap_local_message', "<a target='_blank' rel='noopener' href='http://php.net/manual/en/ldap.installation.php'>PHP LDAP extension</a> is not installed or disabled. Please enable it." );
						$this->show_error_message();
						return;
					}

					$server_name         = ! empty( get_option( 'mo_ldap_local_server_url' ) ) ? get_option( 'mo_ldap_local_server_url' ) : '';
					$dn                  = ! empty( get_option( 'mo_ldap_local_server_dn' ) ) ? get_option( 'mo_ldap_local_server_dn' ) : '';
					$admin_ldap_password = ! empty( get_option( 'mo_ldap_local_server_password' ) ) ? get_option( 'mo_ldap_local_server_password' ) : '';
					$search_base         = ! empty( get_option( 'mo_ldap_local_search_base' ) ) ? get_option( 'mo_ldap_local_search_base' ) : '';
					$search_filter       = ! empty( get_option( 'mo_ldap_local_search_filter' ) ) ? get_option( 'mo_ldap_local_search_filter' ) : '';

					$test_username = isset( $_POST['test_username'] ) ? sanitize_text_field( wp_unslash( $_POST['test_username'] ) ) : '';
					$test_password = isset( $_POST['test_password'] ) ? $_POST['test_password'] : ''; //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash -- Should not be sanitised as Strong Passwords contains special characters

					delete_option( 'mo_ldap_local_message' );

					if ( empty( $test_username ) || empty( $test_password ) ) {
						$this->mo_ldap_report_update( 'Test Authentication ', 'ERROR', '<strong>ERROR</strong>: All the fields are required. Please enter valid entries.' );
						add_option( 'mo_ldap_local_message', self::LDAPFIELDS );
						$this->show_error_message();
						return;
					} elseif ( empty( $server_name ) || empty( $dn ) || empty( $admin_ldap_password ) || empty( $search_base ) || empty( $search_filter ) ) {
						$this->mo_ldap_report_update( 'Test authentication', 'ERROR', '<strong>Test Authentication Error</strong>: Please save LDAP Configuration to test authentication.' );
						add_option( 'mo_ldap_local_message', 'Please save LDAP Configuration to test authentication.', '', 'no' );
						$this->show_error_message();
						return;
					}

					$mo_ldap_config = new MO_LDAP_Local_Config();
					$content        = $mo_ldap_config->test_authentication( $test_username, $test_password );
					$response       = json_decode( $content, true );

					if ( isset( $response['statusCode'] ) ) {
						if ( strcasecmp( $response['statusCode'], 'LDAP_USER_BIND_SUCCESS' ) === 0 || strcasecmp( $response['statusCode'], 'LDAP_ERROR' ) === 0 ) {
							$message = 'You have successfully configured your LDAP settings.<br>
									You can set login via directory credentials by checking the Enable LDAP Login in the <strong>Sign-In Settings Tab</strong> and then <a href="' . esc_url( wp_logout_url( get_permalink() ) ) . '">Logout</a> from WordPress and login again with your LDAP credentials.<br>';
							update_option( 'mo_ldap_local_message', $message );
							$this->show_success_message();
						} elseif ( strcasecmp( $response['statusCode'], 'LDAP_USER_BIND_ERROR' ) === 0 ) {
							$this->mo_ldap_report_update( $test_username, 'ERROR', '<strong>Test Authentication Error: </strong>' . $response['statusMessage'] );
							update_option( 'mo_ldap_local_message', $response['statusMessage'] );
							$this->show_error_message();
						} elseif ( strcasecmp( $response['statusCode'], 'LDAP_USER_SEARCH_ERROR' ) === 0 ) {
							$this->mo_ldap_report_update( $test_username, 'ERROR', '<strong>Test Authentication Error: </strong>' . $response['statusMessage'] );
							update_option( 'mo_ldap_local_message', $response['statusMessage'] );
							$this->show_error_message();
						} elseif ( strcasecmp( $response['statusCode'], 'LDAP_USER_NOT_EXIST' ) === 0 ) {
							$respone_status_message = 'Cannot find user <b>' . $test_username . '</b> in the LDAP Server.';
							$this->mo_ldap_report_update( $test_username, 'ERROR', '<strong>Test Authentication Error: </strong>' . $respone_status_message );
							update_option( 'mo_ldap_local_message', ( $response['statusMessage'] ) );
							$this->show_error_message();
						} elseif ( strcasecmp( $response['statusCode'], 'ERROR' ) === 0 ) {
							$this->mo_ldap_report_update( $test_username, 'ERROR', '<strong>Test Authentication Error: </strong>' . $response['statusMessage'] );
							update_option( 'mo_ldap_local_message', $response['statusMessage'] );
							$this->show_error_message();
						} elseif ( strcasecmp( $response['statusCode'], 'OPENSSL_ERROR' ) === 0 ) {
							$this->mo_ldap_report_update( $test_username, 'ERROR', '<strong>Test Authentication Error: </strong>' . $response['statusMessage'] );
							update_option( 'mo_ldap_local_message', $response['statusMessage'] );
							$this->show_error_message();
						} elseif ( strcasecmp( $response['statusCode'], 'LDAP_LOCAL_SERVER_NOT_CONFIGURED' ) === 0 ) {
							$this->mo_ldap_report_update( $test_username, 'ERROR', '<strong>Test Authentication Error: </strong>' . $response['statusMessage'] );
							update_option( 'mo_ldap_local_message', $response['statusMessage'] );
							$this->show_error_message();
						}
					} else {
						$this->mo_ldap_report_update( $test_username, 'ERROR', '<strong>Test Authentication Error: </strong> There was an error processing your request. Please verify the Search Base(s) and Username attribute. Your user should be present in the Search base defined.' );
						update_option( 'mo_ldap_local_message', 'There was an error processing your request. Please verify the Search Base(s) and Username attribute. Your user should be present in the Search base defined.' );
						$this->show_error_message();
					}
				} elseif ( strcasecmp( $post_option, 'mo_ldap_pass' ) === 0 && check_admin_referer( 'mo_ldap_pass' ) ) {
					update_option( 'mo_ldap_export', isset( $_POST['enable_ldap_login'] ) ? 1 : 0 );

					if ( get_option( 'mo_ldap_export' ) ) {
						update_option( 'mo_ldap_local_message', 'Service account password will be exported in encrypted fashion' );
						$this->show_success_message();
					} else {
						update_option( 'mo_ldap_local_message', 'Service account password will not be exported.' );
						$this->show_error_message();
					}
				} elseif ( strcasecmp( $post_option, 'mo_ldap_export' ) === 0 && check_admin_referer( 'mo_ldap_export' ) ) {
					$ldap_server_url = get_option( 'mo_ldap_local_server_url' );
					if ( ! empty( $ldap_server_url ) ) {

						$this->miniorange_ldap_export();
					} else {
						update_option( 'mo_ldap_local_message', 'LDAP Configuration not set. Please configure LDAP Connection settings.' );
						$this->show_error_message();
					}
				} elseif ( strcasecmp( $post_option, 'mo_ldap_authentication_report' ) === 0 && check_admin_referer( 'mo_ldap_authentication_report' ) ) {

					$ldap_server_url = get_option( 'mo_ldap_local_server_url' );

					if ( ! empty( $ldap_server_url ) ) {
						$this->miniorange_ldap_authentication_report();
					} else {
						update_option( 'mo_ldap_local_message', 'LDAP Authentication report not found' );
						$this->show_error_message();
					}
				} elseif ( strcasecmp( $post_option, 'mo_ldap_clear_authentication_report' ) === 0 && check_admin_referer( 'mo_ldap_clear_authentication_report' ) ) {
					$this->mo_ldap_clear_authentication_report();
				} elseif ( strcasecmp( $post_option, 'enable_config' ) === 0 && check_admin_referer( 'enable_config' ) ) {
					update_option( 'en_save_config', isset( $_POST['enable_save_config'] ) ? 1 : 0 );
					if ( get_option( 'en_save_config' ) ) {
						update_option( 'mo_ldap_local_message', 'Plugin configuration will be persisted upon uninstall.' );
						$this->show_success_message();
					} else {
						update_option( 'mo_ldap_local_message', 'Plugin configuration will not be persisted upon uninstall' );
						$this->show_error_message();
					}
				} elseif ( strcasecmp( $post_option, 'reset_password' ) === 0 && check_admin_referer( 'reset_password' ) ) {
					$admin_email              = get_option( 'mo_ldap_local_admin_email' );
					$customer                 = new MO_LDAP_Customer_Setup();
					$forgot_password_response = $customer->mo_ldap_local_forgot_password( $admin_email );
					if ( ! empty( $forgot_password_response ) ) {
						$forgot_password_response = json_decode( $forgot_password_response, 'true' );
						if ( strcasecmp( $forgot_password_response->status, 'SUCCESS' ) === 0 ) {
								$message = 'You password has been reset successfully and sent to your registered email. Please check your mailbox.';
								update_option( 'mo_ldap_local_message', $message );
								$this->show_success_message();
						}
					} else {
						update_option( 'mo_ldap_local_message', 'Error in request' );
						$this->show_error_message();
					}
				} elseif ( strcasecmp( $post_option, 'mo_ldap_local_enable_admin_wp_login' ) === 0 && check_admin_referer( 'mo_ldap_local_enable_admin_wp_login' ) ) {
					update_option( 'mo_ldap_local_enable_admin_wp_login', ( isset( $_POST['mo_ldap_local_enable_admin_wp_login'] ) && strcmp( sanitize_text_field( wp_unslash( $_POST['mo_ldap_local_enable_admin_wp_login'] ) ), '1' ) === 0 ) ? 1 : 0 );
					if ( get_option( 'mo_ldap_local_enable_admin_wp_login' ) ) {
						update_option( 'mo_ldap_local_message', 'Allow administrators to login with WordPress Credentials is enabled.' );
						$this->show_success_message();
					} else {
						update_option( 'mo_ldap_local_message', 'Allow administrators to login with WordPress Credentials is disabled.' );
						$this->show_error_message();
					}
				} elseif ( strcasecmp( $post_option, 'mo_ldap_local_cancel' ) === 0 && check_admin_referer( 'mo_ldap_local_cancel' ) ) {
					delete_option( 'mo_ldap_local_admin_email' );
					delete_option( 'mo_ldap_local_registration_status' );
					delete_option( 'mo_ldap_local_verify_customer' );
					delete_option( 'mo_ldap_local_email_count' );
					delete_option( 'mo_ldap_local_sms_count' );
				} elseif ( strcasecmp( $post_option, 'mo_ldap_goto_login' ) === 0 && check_admin_referer( 'mo_ldap_goto_login' ) ) {
					delete_option( 'mo_ldap_local_new_registration' );
					update_option( 'mo_ldap_local_verify_customer', 'true' );
				} elseif ( strcasecmp( $post_option, 'change_miniorange_account' ) === 0 && check_admin_referer( 'change_miniorange_account' ) ) {
					delete_option( 'mo_ldap_local_admin_customer_key' );
					delete_option( 'mo_ldap_local_admin_api_key' );
					delete_option( 'mo_ldap_local_password', '' );
					delete_option( 'mo_ldap_local_message' );
					delete_option( 'mo_ldap_local_verify_customer' );
					delete_option( 'mo_ldap_local_new_registration' );
					delete_option( 'mo_ldap_local_registration_status' );
				} elseif ( strcasecmp( $post_option, 'mo_ldap_login_send_query' ) === 0 && check_admin_referer( 'mo_ldap_login_send_query' ) ) {
					$email = isset( $_POST['inner_form_email_id'] ) ? sanitize_email( wp_unslash( $_POST['inner_form_email_id'] ) ) : '';
					$phone = isset( $_POST['inner_form_phone_id'] ) ? sanitize_text_field( wp_unslash( $_POST['inner_form_phone_id'] ) ) : '';
					$query = isset( $_POST['inner_form_query_id'] ) ? sanitize_text_field( wp_unslash( $_POST['inner_form_query_id'] ) ) : '';

					$choice = isset( $_POST['export_configuration_choice'] ) ? sanitize_text_field( wp_unslash( $_POST['export_configuration_choice'] ) ) : '';
					if ( strcasecmp( $choice, 'yes' ) === 0 ) {
						$configuration = $this->auto_email_ldap_export();
						$configuration = implode( ' <br>', $configuration );
						$query         = $query . ' ,<br><br>Plugin Configuration:<br> ' . $configuration;
					} elseif ( strcasecmp( $choice, 'no' ) === 0 ) {
						$configuration = 'Configuration was not uploaded by user';
						$query         = $query . ' ,<br><br>Plugin Configuration:<br> ' . $configuration;
					}
					$query = '[WP LDAP for Intranet (Free Plugin)]: ' . $query;
					$this->mo_ldap_send_query( $email, $phone, $query );
				} elseif ( strcasecmp( $post_option, 'mo_ldap_call_setup' ) === 0 && check_admin_referer( 'mo_ldap_call_setup' ) ) {
					$time_zone        = isset( $_POST['mo_ldap_setup_call_timezone'] ) ? sanitize_text_field( wp_unslash( $_POST['mo_ldap_setup_call_timezone'] ) ) : '';
					$call_date        = isset( $_POST['mo_ldap_setup_call_date'] ) ? sanitize_text_field( wp_unslash( $_POST['mo_ldap_setup_call_date'] ) ) : '';
					$call_time        = isset( $_POST['mo_ldap_setup_call_time'] ) ? gmDate( 'g:i A', strtotime( sanitize_text_field( wp_unslash( $_POST['mo_ldap_setup_call_time'] ) ) ) ) : '';
					$call_reason      = isset( $_POST['ldap-call-query'] ) ? sanitize_text_field( wp_unslash( $_POST['ldap-call-query'] ) ) : '';
					$call_email       = isset( $_POST['setup-call-email'] ) ? sanitize_email( wp_unslash( $_POST['setup-call-email'] ) ) : '';
					$subject          = 'WordPress LDAP/AD Request For Setup Call - ' . $call_email;
					$query            = 'Query :' . $call_reason . '<br><br> Time Zone: ' . $time_zone . '<br> <br>Date: ' . $call_date . '<br> <br>Time: ' . $call_time . '<br><br>Current Version Installed : ' . MO_LDAP_Plugin_Constants::VERSION . ' <br>';
					$company          = isset( $_POST['company'] ) ? sanitize_text_field( wp_unslash( $_POST['company'] ) ) : '';
					$feedback_reasons = new MO_LDAP_Customer_Setup();

					if ( ! is_null( $feedback_reasons ) ) {

						$submited = json_decode( $feedback_reasons->send_email_alert( $subject, $call_email, $query, $company ), true );
						if ( json_last_error() === JSON_ERROR_NONE ) {
							if ( is_array( $submited ) && array_key_exists( 'status', $submited ) && strcasecmp( $submited['status'], 'ERROR' ) === 0 ) {
								update_option( 'mo_ldap_local_message', $submited['message'] );
								$this->show_error_message();
							} else {
								if ( ! $submited ) {
									update_option( 'mo_ldap_local_message', 'Error while submitting request for the call.' );
									$this->show_error_message();
								}
							}
						}
						update_option( 'mo_ldap_local_message', 'Your request for the call has been successfully sent. An executive from the miniOrange team will soon reach out to you.' );
						$this->show_success_message();

					}
				} elseif ( strcasecmp( $post_option, 'mo_ldap_login_send_feature_request_query' ) === 0 && check_admin_referer( 'mo_ldap_login_send_feature_request_query' ) ) {
					$email = isset( $_POST['query_email'] ) ? sanitize_email( wp_unslash( $_POST['query_email'] ) ) : '';
					$phone = isset( $_POST['query_phone'] ) ? sanitize_text_field( wp_unslash( $_POST['query_phone'] ) ) : '';
					$query = isset( $_POST['query'] ) ? sanitize_text_field( wp_unslash( $_POST['query'] ) ) : '';
					$query = '[WP LDAP for Intranet (Free Plugin)]: ' . $query;
					$this->mo_ldap_send_query( $email, $phone, $query );
				}
				if ( strcasecmp( $post_option, 'mo_ldap_trial_request' ) === 0 && check_admin_referer( 'mo_ldap_trial_request' ) ) {
					if ( isset( $_POST['mo_ldap_trial_email'] ) ) {
						$email = isset( $_POST['mo_ldap_trial_email'] ) ? sanitize_email( wp_unslash( $_POST['mo_ldap_trial_email'] ) ) : '';
					}

					if ( empty( $email ) ) {
						$email = get_option( 'mo_ldap_local_admin_email' );
					}

					if ( isset( $_POST['mo_ldap_trial_plan'] ) ) {
						$trial_plan = isset( $_POST['mo_ldap_trial_plan'] ) ? sanitize_text_field( wp_unslash( $_POST['mo_ldap_trial_plan'] ) ) : '';
					}

					if ( isset( $_POST['mo_ldap_trial_description'] ) ) {
						$trial_requirements = isset( $_POST['mo_ldap_trial_description'] ) ? sanitize_textarea_field( wp_unslash( $_POST['mo_ldap_trial_description'] ) ) : '';
					}

					$phone = '';

					$license_plans = array(
						'basic-plan'                 => 'Essential Authentication Plan',
						'kerbores-ntlm'              => 'Kerberos / NTLM SSO Plan',
						'standard-plan'              => 'Advanced Syncing & Authentication Plan',
						'enterprise-plan'            => 'All Inclusive Plan',
						'multisite-basic-plan'       => 'Multisite Essential Authentication Plan',
						'multisite-kerbores-ntlm'    => 'Multisite Kerberos / NTLM SSO Plan',
						'multisite-standard-plan'    => 'Multisite Advanced Syncing & Authentication Plan',
						'enterprise-enterprise-plan' => 'Multisite All Inclusive Plan',
					);
					if ( isset( $license_plans[ $trial_plan ] ) ) {
						$trial_plan = $license_plans[ $trial_plan ];
					}
					$addons = array(
						'directory-sync'          => 'Sync Users LDAP Directory',
						'buddypress-integration'  => 'Sync BuddyPress Extended Profiles',
						'password-sync'           => 'Password Sync with LDAP Server',
						'profile-picture-map'     => 'Profile Picture Sync for WordPress and BuddyPress',
						'ultimate-member-login'   => 'Ultimate Member Login Integration',
						'page-post-restriction'   => 'Page/Post Restriction',
						'search-staff'            => 'Search Staff from LDAP Directory',
						'profile-sync'            => 'Third Party Plugin User Profile Integration',
						'gravity-forms'           => 'Gravity Forms Integration',
						'buddypress-group'        => 'Sync BuddyPress Groups',
						'memberpress-integration' => 'MemberPress Plugin Integration',
						'emember-integration'     => 'eMember Plugin Integration',
						'buddyboss-integration'   => 'BuddyBoss Profile Integration',
						'directory-search'        => 'Directory Search',
						'paid-membership-pro'     => 'Paid Membership Pro Integrator',
						'wp-groups'               => 'WP Groups Plugin Integration',
						'custom-notifications'    => 'Custom Notifications on WordPress Login page',
					);

					$addons_selected = array();
					foreach ( $addons as $key => $value ) {
						if ( isset( $_POST[ $key ] ) && strcasecmp( sanitize_text_field( wp_unslash( $_POST[ $key ] ) ), 'true' ) === 0 ) {
							$addons_selected[ $key ] = $value;
						}
					}
					$directory_access = '';
					$query            = '';
					if ( ! empty( $trial_plan ) ) {
						$query .= '<br><br>[Interested in plan] : ' . $trial_plan;
					}

					if ( ! empty( $addons_selected ) ) {
						$query .= '<br><br>[Interested in add-ons] : ';
						foreach ( $addons_selected as $key => $value ) {
							$query .= $value;
							if ( next( $addons_selected ) ) {
								$query .= ', ';
							}
						}
					}

					if ( ! empty( $trial_requirements ) ) {
						$query .= '<br><br>[Requirements] : ' . $trial_requirements;
					}

					if ( isset( $_POST['get_directory_access'] ) ) {
						$directory_access = sanitize_text_field( wp_unslash( $_POST['get_directory_access'] ) );
					}

					if ( strcasecmp( $directory_access, 'Yes' ) === 0 ) {
						$directory_access = 'Yes';
					} else {
						$directory_access = 'No';
					}
					$query .= '<br><br>[Is your LDAP server publicly accessible?] : ' . $directory_access . '';

					$query = ' [Trial: WordPress LDAP/AD Plugin]: ' . $query;
					$this->mo_ldap_send_query( $email, $phone, $query );
				}
				if ( strcasecmp( $post_option, 'mo_ldap_skip_feedback' ) === 0 && check_admin_referer( 'mo_ldap_skip_feedback' ) ) {
					deactivate_plugins( __FILE__ );
					update_option( 'mo_ldap_local_message', 'Plugin deactivated successfully.' );
					$this->deactivate_error_message();
				}
				if ( strcasecmp( $post_option, 'mo_ldap_hide_msg' ) === 0 && check_admin_referer( 'mo_ldap_hide_msg' ) ) {
					update_option( 'mo_ldap_local_multisite_message', 'true' );
				}
				if ( strcasecmp( $post_option, 'mo_ldap_feedback' ) === 0 && check_admin_referer( 'mo_ldap_feedback' ) ) {
					$user                      = wp_get_current_user();
					$message                   = 'Query :[WordPress LDAP/AD Plugin:] Plugin Deactivated: ';
					$deactivate_reason_message = array_key_exists( 'query_feedback', $_POST ) ? sanitize_textarea_field( wp_unslash( $_POST['query_feedback'] ) ) : false;

					$reply_required = '';
					if ( isset( $_POST['get_reply'] ) ) {
						$reply_required = sanitize_text_field( wp_unslash( $_POST['get_reply'] ) );
					}
					if ( empty( $reply_required ) ) {
						$reply_required = 'NO';
						$message       .= '<strong><span style="color: red;">[Follow up Needed : ' . $reply_required . ']</strong></span><br> ';
					} else {
						$reply_required = 'YES';
						$message       .= '<strong><span style="color: green;">[Follow up Needed : ' . $reply_required . ']</strong></span><br>';
					}

					if ( ! empty( $deactivate_reason_message ) ) {
						$message .= '<br>Feedback : ' . $deactivate_reason_message . '<br>';
					}

					$message .= '<br>Current Version Installed : ' . MO_LDAP_Plugin_Constants::VERSION . '<br>';

					if ( isset( $_POST['rate'] ) ) {
						$rate_value = sanitize_text_field( wp_unslash( $_POST['rate'] ) );
						$message   .= '<br>[Rating : ' . $rate_value . ']<br>';
					}

					$email   = isset( $_POST['query_mail'] ) ? sanitize_email( wp_unslash( $_POST['query_mail'] ) ) : '';
					$subject = 'WordPress LDAP/AD Plugin Feedback - ' . $email;

					if ( ! filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
						$email = get_option( 'mo_ldap_local_admin_email' );
						if ( empty( $email ) ) {
							$email = $user->user_email;
						}
					}
					$company          = isset( $_POST['company'] ) ? sanitize_text_field( wp_unslash( $_POST['company'] ) ) : '';
					$feedback_reasons = new MO_LDAP_Customer_Setup();
					if ( ! is_null( $feedback_reasons ) ) {
						$submited = json_decode( $feedback_reasons->send_email_alert( $subject, $email, $message, $company ), true );
						if ( json_last_error() === JSON_ERROR_NONE ) {
							if ( is_array( $submited ) && array_key_exists( 'status', $submited ) && strcasecmp( $submited['status'], 'ERROR' ) === 0 ) {
										update_option( 'mo_ldap_local_message', $submited['message'] );
										$this->show_error_message();
							} else {
								if ( ! $submited ) {
									update_option( 'mo_ldap_local_message', 'Error while submitting the query.' );
									$this->show_error_message();
								}
							}
						}

						deactivate_plugins( __FILE__ );
						update_option( 'mo_ldap_local_message', 'Thank you for the feedback.' );
						$this->show_success_message();
						wp_safe_redirect( 'plugins.php' );
						exit;

					}
				}
			}
		}

		/**
		 * Function mo_ldap_send_query : Send query to miniOrange Support Team
		 *
		 * @param  string $email : Email of the user asking for support.
		 * @param  string $phone : Phone Number of the user asking for support.
		 * @param  string $query : Query or Issues User Facing.
		 * @return void
		 */
		private function mo_ldap_send_query( $email, $phone, $query ) {
			$query = $query . '<br><br>[Current Version Installed] : ' . MO_LDAP_Plugin_Constants::VERSION;

			if ( MO_LDAP_Utility::check_empty_or_null( $email ) || MO_LDAP_Utility::check_empty_or_null( $query ) ) {
				update_option( 'mo_ldap_local_message', 'Please submit your query along with email.' );
				$this->show_error_message();
			} else {
				$contact_us = new MO_LDAP_Customer_Setup();
				$submited   = json_decode( $contact_us->submit_contact_us( $email, $phone, $query ), true );

				if ( isset( $submited['status'] ) && strcasecmp( $submited['status'], 'ERROR' ) === 0 ) {
					update_option( 'mo_ldap_local_message', 'There was an error in sending query. Please send us an email on <a href=mailto:info@xecurify.com><strong>info@xecurify.com</strong></a>.' );
					$this->show_error_message();
				} else {
					update_option( 'mo_ldap_local_message', 'Your query has been sent successfully. A miniOrange representative will soon reach out to you.<br>In case we dont get back to you, there might be email delivery failures. You can send us email on <a href=mailto:info@xecurify.com><strong>info@xecurify.com</strong></a> in that case.' );
					$this->show_success_message();
				}
			}
		}

		/**
		 * Function miniorange_ldap_export : Export all configurations to JSON file
		 *
		 * @return void
		 */
		private function miniorange_ldap_export() {
			$tab_class_name = maybe_unserialize( TAB_LDAP_CLASS_NAMES );

			$configuration_array = array();
			foreach ( $tab_class_name as $key => $value ) {
				$configuration_array[ $key ] = $this->mo_get_configuration_array( $value );
			}

			header( 'Content-Disposition: attachment; filename=miniorange-ldap-config.json' );
			echo wp_json_encode( $configuration_array, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
			exit;
		}


		/**
		 * Function mo_get_configuration_array
		 *
		 * @param  mixed $class_name : Sub Class required for config export.
		 * @return array
		 */
		private function mo_get_configuration_array( $class_name ) {
			$class_object  = call_user_func( $class_name . '::get_constants' );
			$mapping_count = get_option( 'mo_ldap_local_role_mapping_count' );
			$mo_array      = array();
			$mo_map_key    = array();
			$mo_map_value  = array();
			foreach ( $class_object as $key => $value ) {
				$key = strtolower( $key );

				if ( strcasecmp( $value, 'mo_ldap_local_server_url' ) === 0 || strcasecmp( $value, 'mo_ldap_local_server_password' ) === 0 || strcasecmp( $value, 'mo_ldap_local_server_dn' ) === 0 || strcasecmp( $value, 'mo_ldap_local_search_base' ) === 0 || strcasecmp( $value, 'mo_ldap_local_search_filter' ) === 0 || strcasecmp( $value, 'mo_ldap_local_Filter_Search' ) === 0 ) {
					$flag = 1;
				} else {
					$flag = 0;
				}
				if ( strcasecmp( $value, 'mo_ldap_local_mapping_key_' ) === 0 ) {
					for ( $i = 1; $i <= $mapping_count; $i++ ) {
						$mo_map_key[ $i ] = get_option( $value . $i );
					}
					$mo_option_exists = $mo_map_key;
				} elseif ( strcasecmp( $value, 'mo_ldap_local_mapping_value_' ) === 0 ) {
					for ( $i = 1; $i <= $mapping_count; $i++ ) {
						$mo_map_value[ $i ] = get_option( $value . $i );
					}
					$mo_option_exists = $mo_map_value;

				} else {
					$mo_option_exists = get_option( $value );
				}

				if ( $mo_option_exists ) {
					if ( @maybe_unserialize( $mo_option_exists ) !== false ) {//phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged -- Silencing errors to avoid error logs exported in JSON file with plugin configuration
						$mo_option_exists = maybe_unserialize( $mo_option_exists );
					}
					if ( 1 === $flag ) {
						if ( strcasecmp( $value, 'mo_ldap_local_server_password' ) === 0 && ( empty( get_option( 'mo_ldap_export' ) ) || strcasecmp( get_option( 'mo_ldap_export' ), '0' ) === 0 ) ) {
							continue;
						} elseif ( strcasecmp( $value, 'mo_ldap_local_server_password' ) === 0 && strcasecmp( get_option( 'mo_ldap_export' ), '1' ) === 0 ) {
							$mo_array[ $key ] = $mo_option_exists;
						} else {
							$mo_array[ $key ] = MO_LDAP_Utility::decrypt( $mo_option_exists );
						}
					} else {
						$mo_array[ $key ] = $mo_option_exists;
					}
				}
			}
			return $mo_array;
		}

		/**
		 * Function mo_ldap_clear_authentication_report : To delete all existing user authentication logs
		 *
		 * @return void
		 */
		private function mo_ldap_clear_authentication_report() {
			global $wpdb;
			$delete = $wpdb->query( "TRUNCATE TABLE {$wpdb->prefix}user_report" ); //phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- Changing a custom table.
			wp_cache_delete( 'mo_ldap_user_report_cache' );
			wp_cache_delete( 'mo_ldap_user_report_count_cache' );
			wp_cache_delete( 'wp_user_reports_pagination_cache' );
		}

		/**
		 * Function miniorange_ldap_authentication_report : Fetch users auth report
		 *
		 * @return void
		 */
		private function miniorange_ldap_authentication_report() {
			global $wpdb;
			$wp_user_reports_cache = wp_cache_get( 'mo_ldap_user_report_cache' );
			if ( $wp_user_reports_cache ) {
				$user_reports = $wp_user_reports_cache;
			} else {
				$user_reports = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}user_report" ); //phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- Fetching data from a custom table.
				wp_cache_set( 'mo_ldap_user_report_cache', $user_reports );
			}

			$csv_file = fopen( 'php://output', 'w' );

			if ( ! empty( $user_reports ) ) {
				$fields = array( 'ID', 'USERNAME', 'TIME', 'LDAP STATUS', 'LDAP ERROR' );
				fputcsv( $csv_file, $fields );
				foreach ( $user_reports as $user_report ) {
					$line_data = array( $user_report->id, $user_report->user_name, $user_report->time, $user_report->ldap_status, sanitize_text_field( $user_report->ldap_error ) );
					fputcsv( $csv_file, $line_data );
				}
			} else {
				$message = 'No Logs Available';
				update_option( 'mo_ldap_local_message', $message );
				$this->show_error_message();
				return;
			}

			fclose( $csv_file ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fclose -- This file should not be saved locally.
			header( 'Content-Type: text/csv' );
			header( 'Content-Disposition: attachment; filename=ldap-authentication-report.csv' );

			exit;
		}

		/**
		 * Function auto_email_ldap_export : Returns plugin configuration to be sent in support email request after user consent taken
		 *
		 * @return array
		 */
		private function auto_email_ldap_export() {
				$directory_name = get_option( 'mo_ldap_local_directory_server' );
				$server_name    = get_option( 'mo_ldap_local_server_url' ) ? MO_LDAP_Utility::decrypt( get_option( 'mo_ldap_local_server_url' ) ) : '';
				$dn             = get_option( 'mo_ldap_local_server_dn' ) ? MO_LDAP_Utility::decrypt( get_option( 'mo_ldap_local_server_dn' ) ) : '';
				$search_base    = get_option( 'mo_ldap_local_search_base' ) ? MO_LDAP_Utility::decrypt( get_option( 'mo_ldap_local_search_base' ) ) : '';
				$search_filter  = get_option( 'mo_ldap_local_search_filter' ) ? MO_LDAP_Utility::decrypt( get_option( 'mo_ldap_local_search_filter' ) ) : '';
				return array(
					'LDAP Directory Name' => 'LDAP Directory Name:  ' . $directory_name,
					'LDAP Server'         => 'LDAP Server:  ' . $server_name,
					'Service Account DN'  => 'Service Account DN:  ' . $dn,
					'Search Base'         => 'Search Base:  ' . $search_base,
					'LDAP Search Filter'  => 'LDAP Search Filter:  ' . $search_filter,
				);
		}

		/**
		 * Function test_attribute_configuration : Test LDAP attribute mapping
		 *
		 * @return void
		 */
		public function test_attribute_configuration() {
			if ( is_user_logged_in() && current_user_can( 'manage_options' ) && isset( $_REQUEST['option'] ) ) {
				if ( null !== $_REQUEST['option'] && strcasecmp( sanitize_text_field( wp_unslash( $_REQUEST['option'] ) ), 'testattrconfig' ) === 0 && isset( $_REQUEST['_wpnonce'] ) && wp_verify_nonce( sanitize_key( $_REQUEST['_wpnonce'] ), 'testattrconfig_nonce' ) ) {
					$username       = isset( $_REQUEST['user'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['user'] ) ) : '';
					$mo_ldap_config = new MO_LDAP_Local_Config();
					$mo_ldap_config->test_attribute_configuration( $username );
				} elseif ( strcasecmp( sanitize_text_field( wp_unslash( $_REQUEST['option'] ) ), 'searchbaselist' ) === 0 && wp_verify_nonce( sanitize_key( $_REQUEST['_wpnonce'] ), 'searchbaselist_nonce' ) ) {
					$mo_ldap_config = new MO_LDAP_Local_Config();
					$mo_ldap_config->show_search_bases_list();
				}
			}
		}

		/**
		 * Function save_success_customer_config : Save customer information in DB after successful login
		 *
		 * @param  string $id : User ID.
		 * @param  string $api_key : User Unique API key.
		 * @param  string $token : User unique Token.
		 * @param  string $message : Success Message to be shown on UI.
		 * @return void
		 */
		private function save_success_customer_config( $id, $api_key, $token, $message ) {
			update_option( 'mo_ldap_local_admin_customer_key', $id );
			update_option( 'mo_ldap_local_admin_api_key', $api_key );
			update_option( 'mo_ldap_local_admin_token', $token );
			update_option( 'mo_ldap_local_password', '' );
			update_option( 'mo_ldap_local_message', $message );
			delete_option( 'mo_ldap_local_verify_customer' );
			delete_option( 'mo_ldap_local_new_registration' );
			delete_option( 'mo_ldap_local_registration_status' );
			$this->show_success_message();
		}

		/**
		 * Function mo_ldap_local_settings_style : Add Style Sheets to plugin UI.
		 *
		 * @param  mixed $page : Plugin Page.
		 * @return void
		 */
		public function mo_ldap_local_settings_style( $page ) {
			if ( strcasecmp( $page, 'toplevel_page_mo_ldap_local_login' ) !== 0 ) {
				return;
			}
			wp_enqueue_style( 'mo_ldap_admin_settings_style', plugins_url( 'includes/css/mo_ldap_plugin_style_settings.min.css', __FILE__ ), array(), MO_LDAP_Plugin_Constants::VERSION );
			wp_enqueue_style( 'mo_ldap_admin_settings_phone_style', plugins_url( 'includes/css/phone.min.css', __FILE__ ), array(), MO_LDAP_Plugin_Constants::VERSION );
			wp_enqueue_style( 'mo_ldap_admin_font_awsome', plugins_url( 'includes/fonts/css/font-awesome.min.css', __FILE__ ), array(), MO_LDAP_Plugin_Constants::VERSION );
			wp_enqueue_style( 'mo_ldap_grid_layout', plugins_url( 'includes/css/mo_ldap_licensing_grid.min.css', __FILE__ ), array(), MO_LDAP_Plugin_Constants::VERSION );
		}
		/**
		 * Function mo_ldap_local_settings_script : Add style scripts
		 *
		 * @return void
		 */
		public function mo_ldap_local_settings_script() {
			if ( isset( $_GET['page'] ) && strcasecmp( sanitize_text_field( wp_unslash( $_GET['page'] ) ), 'mo_ldap_local_login' ) === 0 ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended -- fetching GET parameter for changing table layout.
				wp_enqueue_script( 'mo_ldap_admin_auth_report', plugins_url( 'includes/js/mo-ldap-auth-reports.min.js', __FILE__ ), array( 'jquery' ), MO_LDAP_Plugin_Constants::VERSION, false );
				wp_enqueue_script( 'mo_ldap_admin_settings_phone_script', plugins_url( 'includes/js/phone.min.js', __FILE__ ), array(), MO_LDAP_Plugin_Constants::VERSION, false );
				wp_register_script( 'mo_ldap_admin_settings_script', plugins_url( 'includes/js/settings_page.min.js', __FILE__ ), array( 'jquery' ), MO_LDAP_Plugin_Constants::VERSION, true );
				wp_enqueue_script( 'mo_ldap_admin_settings_script' );
			}
		}

		/**
		 * Function error_message : Add/Display error message on UI.
		 *
		 * @return void
		 */
		public function error_message() {
			$message     = get_option( 'mo_ldap_local_message' );
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
				'i'      => array(
					'class' => array(),
				),
				'button' => array(
					'id'    => array(),
					'class' => array(),
				),
			);
			$error_list  = explode( '<br>', $message );
			$wrong_icon  = plugin_dir_url( __FILE__ ) . 'includes/images/error_msg.png';
			$button      = ( count( $error_list ) > 1 ) ? "<button id='mo_ldap_local_view_more_button' class='mo_ldap_local_view_more_button'><i class='fa fa-angle-double-up'></i></button>" : '';
			echo "<div id='error' class='mo_ldap_local_message_container'>
					<div class='mo_ldap_local_message mo_ldap_error_message'>
						<div class='mo_ldap_local_message_left'>
							<img width='26px' height='26px' src='" . esc_url( $wrong_icon ) . "'/>
							<p id='mo_ldap_local_message_title' class='mo_ldap_local_message_content'>" . wp_kses( $error_list[0], $esc_allowed ) . "</p>
							<p id='mo_ldap_local_message_desc' class='mo_ldap_local_message_content_desc d-none'>" . wp_kses( $message, $esc_allowed ) . '</p>
						</div>
						' . wp_kses( $button, $esc_allowed ) . '
					</div>
				</div>';
		}

		/**
		 * Function deactivate_error_message
		 *
		 * @return void
		 */
		private function deactivate_error_message() {
			$class       = 'error';
			$message     = get_option( 'mo_ldap_local_message' );
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
			);
			echo "<div id='error' class='" . esc_attr( $class ) . "'> <p>" . wp_kses( $message, $esc_allowed ) . '</p></div>';
		}

		/**
		 * Function success_message : Show Success message on UI.
		 *
		 * @return void
		 */
		public function success_message() {
			$class        = 'updated';
			$message      = get_option( 'mo_ldap_local_message' );
			$esc_allowed  = array(
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
				'i'      => array(
					'class' => array(),
				),
				'button' => array(
					'id'    => array(),
					'class' => array(),
				),
			);
			$success_list = explode( '<br>', $message );
			$right_icon   = plugin_dir_url( __FILE__ ) . 'includes/images/success_msg.png';
			$button       = ( count( $success_list ) > 1 ) ? "<button id='mo_ldap_local_view_more_button' class='mo_ldap_local_view_more_button'><i class='fa fa-angle-double-up'></i></button>" : '';
			echo "<div id='success' class='mo_ldap_local_message_container'>
					<div class='mo_ldap_local_message mo_ldap_local_message_desc'>
						<div class='mo_ldap_local_message_left'>
							<img width='26px' height='26px' src='" . esc_url( $right_icon ) . "'/>
							<p id='mo_ldap_local_message_title' class='mo_ldap_local_message_content'>" . wp_kses( $success_list[0], $esc_allowed ) . "</p>
							<p id='mo_ldap_local_message_desc' class='mo_ldap_local_message_content_desc d-none'>" . wp_kses( $message, $esc_allowed ) . '</p>
						</div>
						' . wp_kses( $button, $esc_allowed ) . '
					</div>
				</div>';
		}

		/**
		 * Function show_success_message : Calls success_message
		 *
		 * @return void
		 */
		private function show_success_message() {
			remove_action( 'admin_notices', array( $this, 'error_message' ) );
			add_action( 'admin_notices', array( $this, 'success_message' ) );
		}

		/**
		 * Function show_error_message : Calls error_message
		 *
		 * @return void
		 */
		private function show_error_message() {
			remove_action( 'admin_notices', array( $this, 'success_message' ) );
			add_action( 'admin_notices', array( $this, 'error_message' ) );
		}

		/**
		 * Function prefix_update_table
		 *
		 * @return void
		 */
		private function prefix_update_table() {
			global $prefix_my_db_version;
			global $wpdb;
			$charset_collate = $wpdb->get_charset_collate();
			$sql             = "CREATE TABLE if not exists`{$wpdb->base_prefix}user_report` (
				  id int NOT NULL AUTO_INCREMENT,
				  user_name varchar(50) NOT NULL,
				  time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
				  ldap_status varchar(250) NOT NULL,
				  ldap_error varchar(250) ,
				  PRIMARY KEY  (id)
				) $charset_collate;";

			if ( ! function_exists( 'dbDelta' ) ) {
				require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			}

			dbDelta( $sql );

			update_option( 'user_logs_table_exists', 1 );

		}

		/**
		 * Function mo_ldap_activate : Called on plugin activation
		 *
		 * @return void
		 */
		public function mo_ldap_activate() {
			$mo_ldap_token_key = get_option( 'mo_ldap_local_customer_token' );
			$email_attr        = get_option( 'mo_ldap_local_email_attribute' );

			if ( empty( $mo_ldap_token_key ) ) {
				update_option( 'mo_ldap_local_customer_token', MO_LDAP_Utility::generate_random_string( 15 ) );
			}

			if ( empty( $email_attr ) ) {
				update_option( 'mo_ldap_local_email_attribute', 'mail' );
			}
			ob_clean();
		}

		/**
		 * Function mo_ldap_report_update : Add log to user auth report.
		 *
		 * @param  mixed $username : Username of user who attempted login.
		 * @param  mixed $status : Status of Login.
		 * @param  mixed $ldap_error : LDAP error message.
		 * @return void
		 */
		private function mo_ldap_report_update( $username, $status, $ldap_error ) {
			if ( strcmp( get_option( 'mo_ldap_local_user_report_log' ), '1' ) === 0 ) {
				global $wpdb;
				$table_name = $wpdb->prefix . 'user_report';
				$wpdb->insert( //phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- Inserting data into a custom table.
					$table_name,
					array(
						'user_name'   => $username,
						'time'        => current_time( 'mysql' ),
						'ldap_status' => $status,
						'ldap_error'  => $ldap_error,

					)
				);
				wp_cache_delete( 'mo_ldap_user_report_cache' );
				wp_cache_delete( 'mo_ldap_user_report_count_cache' );
				wp_cache_delete( 'wp_user_reports_pagination_cache' );
			}
		}


		/**
		 * Function mo_ldap_local_deactivate : Called on plugin deactivation
		 *
		 * @return void
		 */
		public function mo_ldap_local_deactivate() {
			delete_option( 'mo_ldap_local_message' );
			delete_option( 'mo_ldap_local_enable_login' );
			delete_option( 'mo_ldap_local_enable_role_mapping' );
			delete_option( 'mo_ldap_local_multisite_message' );

			wp_safe_redirect( 'plugins.php' );
		}

		/**
		 * Function is_administrator_user : Check if user is administrator.
		 *
		 * @param  object $user : WordPress user object.
		 * @return bool
		 */
		private function is_administrator_user( $user ) {
			$user_role = ( $user->roles );
			return ( ! is_null( $user_role ) && in_array( 'administrator', $user_role, true ) );
		}
	}
	$var = new MO_LDAP_Local_Login();
}
?>
