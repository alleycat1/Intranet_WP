<?php
/**
 * Users utilities
 *
 * @package ThemeREX Addons
 * @since v1.5
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


if ( ! function_exists( 'trx_addons_users_check_role' ) ) {
	/**
	 * Check current user (or user with specified ID) role
	 * For example: if (trx_addons_users_check_role('author')) { ... }
	 * 
	 * @param string $role  Role to check
	 * @param int $user_id  User ID. If not specified - get current user
	 * 
	 * @return bool  	 True if user has specified role
	 */
	function trx_addons_users_check_role( $role, $user_id = null ) {
		if ( is_numeric( $user_id ) ) {
			$user = get_userdata( $user_id );
		} else {
			$user = wp_get_current_user();
		}
		if ( empty( $user ) ) {
			return false;
		}
		return in_array( $role, (array) $user->roles );
	}
}



/* Login and Registration
-------------------------------------------------------------------------------- */

if ( ! function_exists( 'trx_addons_add_login_link' ) ) {
	add_action( 'trx_addons_action_login', 'trx_addons_add_login_link', 10, 2 );
	/**
	 * Display login link. If user is logged in - display user menu
	 * 
	 * @hooked trx_addons_action_login
	 * 
	 * @param array $args  Login link arguments with keys: 'text_login', 'text_logout', 'user_menu'
	 */
	function trx_addons_add_login_link( $args = array() ) {
		global $TRX_ADDONS_STORAGE;
		$TRX_ADDONS_STORAGE['login_popup'] = ! is_user_logged_in();
		if ( isset( $args['text_login'] ) && ( $args['text_login'] === false || $args['text_login'] == '#' ) ) {
			$args['text_login'] = '';
		} else if ( empty( $args['text_login'] ) ) {
			$args['text_login'] = esc_html__( 'Login or|Register', 'trx_addons' );
		}
		if ( isset( $args['text_logout'] ) && ( $args['text_logout'] === false || $args['text_logout'] == '#' ) ) {
			$args['text_logout'] = '';
		} else if ( empty( $args['text_logout'] ) ) {
			$args['text_logout'] = ! empty( $args['user_menu'] ) ? esc_html__( 'Hi,|%s', 'trx_addons' ) : esc_html__( 'Logout|%s', 'trx_addons' );
		}
		trx_addons_get_template_part( 'templates/tpl.login-link.php', 'trx_addons_args_login', $args );
	}
}

if ( ! function_exists( 'trx_addons_add_login_popup' ) ) {
	add_action( 'wp_footer', 'trx_addons_add_login_popup' );
	/**
	 * Display login popup with login and registration forms
	 * 
	 * @hooked wp_footer
	 */
	function trx_addons_add_login_popup() {
		global $TRX_ADDONS_STORAGE;
		if ( ! empty( $TRX_ADDONS_STORAGE['login_popup'] ) && ( $fdir = trx_addons_get_file_dir( 'templates/tpl.login-popup.php' ) ) != '' ) {
			if ( ! is_customize_preview() ) {
				wp_enqueue_script( 'jquery-ui-tabs', false, array( 'jquery','jquery-ui-core' ), null, true );
				wp_enqueue_script( 'jquery-effects-fade', false, array( 'jquery','jquery-effects-core' ), null, true );
			}
			include_once $fdir;
		}
	}
}
	
if ( ! function_exists( 'trx_addons_users_load_scripts_front' ) ) {
	add_action( "wp_enqueue_scripts", 'trx_addons_users_load_scripts_front', TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY );
	/**
	 * Enqueue scripts for frontend for login / registration forms
	 * 
	 * @hooked wp_enqueue_scripts
	 */
	function trx_addons_users_load_scripts_front() {
		if ( trx_addons_is_on( trx_addons_get_option( 'debug_mode' ) ) ) {
			wp_enqueue_script( 'trx_addons-login', trx_addons_get_file_url('js/trx_addons.login.js'), array('jquery'), null, true );
		}
	}
}
	
if ( ! function_exists( 'trx_addons_users_merge_scripts' ) ) {
	add_action( "trx_addons_filter_merge_scripts", 'trx_addons_users_merge_scripts' );
	/**
	 * Add login script to the list of merged scripts
	 * 
	 * @hooked trx_addons_filter_merge_scripts
	 * 
	 * @param array $list List of scripts to merge
	 * 
	 * @return array    Modified list
	 */
	function trx_addons_users_merge_scripts( $list ) {
		$list[ 'js/trx_addons.login.js' ] = true;
		return $list;
	}
}
	
if ( ! function_exists( 'trx_addons_users_localize_script' ) ) {
	add_action( "trx_addons_filter_localize_script", 'trx_addons_users_localize_script' );
	/**
	 * Add variables for the login script to the localized script
	 * 
	 * @hooked trx_addons_filter_localize_script
	 * 
	 * @param array $vars List of variables to localize
	 * 
	 * @return array    Modified list
	 */
	function trx_addons_users_localize_script( $vars ) {
		$vars['login_via_ajax'] 			= (int)trx_addons_get_option('login_via_ajax') > 0;
		$vars['double_opt_in_registration'] = (int)trx_addons_get_option( 'double_opt_in_registration' ) > 0;
		$vars['msg_login_empty'] 			= addslashes( esc_html__("The Login field can't be empty", 'trx_addons') );
		$vars['msg_login_long']				= addslashes( esc_html__('The Login field is too long', 'trx_addons') );
		$vars['msg_password_empty']			= addslashes( esc_html__("The password can't be empty and shorter then 4 characters", 'trx_addons') );
		$vars['msg_password_long']			= addslashes( esc_html__('The password is too long', 'trx_addons') );
		$vars['msg_login_success']			= addslashes( esc_html__('Login success! The page should be reloaded in 3 sec.', 'trx_addons') );
		$vars['msg_login_error']			= addslashes( esc_html__('Login failed!', 'trx_addons') );
		$vars['msg_not_agree']				= addslashes( esc_html__("Please, read and check 'Terms and Conditions'", 'trx_addons') );
		$vars['msg_email_long']				= addslashes( esc_html__('E-mail address is too long', 'trx_addons') );
		$vars['msg_email_not_valid']		= addslashes( esc_html__('E-mail address is invalid', 'trx_addons') );
		$vars['msg_password_not_equal']		= addslashes( esc_html__('The passwords in both fields are not equal', 'trx_addons') );
		$vars['msg_registration_success']	= (int)trx_addons_get_option( 'double_opt_in_registration' ) > 0
												? addslashes( esc_html__('Thank you for registering. Please confirm registration by clicking on the link in the letter sent to the specified email.', 'trx_addons') )
												: addslashes( esc_html__('Registration success! Please log in!', 'trx_addons') );
		$vars['msg_registration_error']		= addslashes( esc_html__('Registration failed!', 'trx_addons') );
		return $vars;
	}
}

if ( ! function_exists( 'trx_addons_users_registration_user' ) ) {
	add_action( 'wp_ajax_trx_addons_registration_user',			'trx_addons_users_registration_user' );
	add_action( 'wp_ajax_nopriv_trx_addons_registration_user',	'trx_addons_users_registration_user' );
	/**
	 * Registration new user via AJAX
	 * 
	 * @hooked wp_ajax_trx_addons_registration_user
	 * @hooked wp_ajax_nopriv_trx_addons_registration_user
	 */
	function trx_addons_users_registration_user() {
	
		trx_addons_verify_nonce();

		if ( (int)get_option('users_can_register') == 0 ) {
			trx_addons_forbidden();
		}

		$response = array(
			'error' => '',
			'redirect_to' => substr( $_REQUEST['redirect_to'], 0, 1024 )
		);
	
		$user_name  = sanitize_text_field( substr( $_REQUEST['user_name'], 0, 60 ) );
		$user_email = sanitize_email( substr( $_REQUEST['user_email'], 0, 60 ) );
		$user_pwd   = sanitize_text_field( substr( $_REQUEST['user_pwd'], 0, 60 ) );

		$user_data  = array(
			'user_login' => $user_name,
			'user_name'  => $user_name,
			'user_pass'  => $user_pwd,
			'user_email' => $user_email
		);
	
		// Check for empty values
		if ( empty( $user_name ) ) {
			$response['error'] = __( 'Username, email or password cannot be empty', 'trx_addons' );
		
		// Double opt-in registration (wait for click on link in the email)
		} else if ( (int) trx_addons_get_option( 'double_opt_in_registration' ) > 0 ) {
			// Check if user with same email is exists
			$exist_user = get_user_by( 'email', $user_email );
			if ( ! empty( $exist_user->ID ) ) {
				$response['error'] = __( 'User with same email already registered!', 'trx_addons' );
			}
			// Check if user with same email is exists
			if ( empty( $response['error'] ) ) {
				$exist_user = get_user_by( 'login', $user_name );
				if ( ! empty( $exist_user->ID ) ) {
					$response['error'] = __( 'User with same name already registered!', 'trx_addons' );
				}
			}
			// Store user data to the cache
			if ( empty( $response['error'] ) ) {
				$cache_key = md5( $user_email . '_' . mt_rand() );
				set_transient( "trx_addons_double_opt_in_{$cache_key}", $user_data, 24 * 60 * 60 );   // Store to the cache for 24 hours
				// Send a mail to a new user
				$link = trx_addons_add_to_url( home_url('/'), array(
					'action' => 'confirm_email',
					'code' => $cache_key
				) );
				$admin_email = sanitize_email( get_option( 'admin_email' ) );
				$subj = sprintf( wp_kses_data( __( 'Confirmation of registration on the site "%s"', 'trx_addons' ) ), get_bloginfo( 'site_name' ) );
				$msg = "\n" . sprintf( wp_kses_data( __( 'You or someone else has registered on the site "%s".', 'trx_addons' ) ), get_bloginfo( 'site_name' ) )
						. "\n\n" . sprintf( wp_kses_data( __( 'To confirm registration, follow the link (the link is valid for 24 hours): %s', 'trx_addons' ) ), '<a href="' . esc_url( $link ) . '">' . $link . '</a>' )
						. "\n\n" . wp_kses_data( __( "If you didn't - just ignore this letter.", 'trx_addons' ) );
				$head = "From: {$admin_email}\n"
						//. "Reply-To: {$admin_email}\n"
						. "Content-Type: text/html; charset=UTF-8\n";
				wp_mail( $user_email, $subj, nl2br( $msg ), $head );
			}

		// Immediately (single opt-in) registration
		} else {
			$response['error'] = trx_addons_users_add_new_user( $user_data );
		}
		trx_addons_ajax_response( $response );
	}
}

if ( ! function_exists( 'trx_addons_users_registration_confirm' ) ) {
	add_action( 'init', 'trx_addons_registration_confirm' );
	/**
	 * Confirm user registration (if double opt-in registration is used) if present a GET parameters 'action' and 'code' in the URL
	 * 
	 * @hooked init
	 */
	function trx_addons_registration_confirm() {
		$action = trx_addons_get_value_gp( 'action' );
		$hash   = str_replace( ' ', '', trx_addons_get_value_gp( 'code' ) );
		if ( $action == 'confirm_email' && ! empty( $hash ) && strlen( $hash ) == 32 ) {
			$user_data = get_transient( "trx_addons_double_opt_in_{$hash}" );
			if ( is_array( $user_data )
				&& ! empty( $user_data['user_login'] )
				&& ! empty( $user_data['user_name'] )
				&& ! empty( $user_data['user_pass'] )
				&& ! empty( $user_data['user_email'] )
			) {
				delete_transient( "trx_addons_double_opt_in_{$hash}" );
				$rez = trx_addons_users_add_new_user( $user_data );
				if ( ! empty( $rez ) && is_string( $rez ) ) {
					trx_addons_set_front_message( sprintf( __( 'Error adding new user: %s', 'trx_addons'), $rez ), 'error' );
				} else {
					trx_addons_set_front_message( __( "The user's email has been confirmed. Please login!", 'trx_addons'), 'success' );
				}
			} else {
				trx_addons_set_front_message( __( 'User data not found. Please re-register!', 'trx_addons'), 'error' );
			}
		}
	}
}

if ( ! function_exists( 'trx_addons_users_add_new_user' ) ) {
	/**
	 * Add new user to the database
	 *
	 * @param array $user_data User data
	 * 
	 * @return string Error message or empty string if success
	 */
	function trx_addons_users_add_new_user( $user_data ) {
		$rez = '';
		$id = wp_insert_user( apply_filters( 'trx_addons_filter_add_new_user', $user_data ) );
		if ( is_wp_error( $id ) ) {
			$rez = $id->get_error_message();
		} else {
			$notify = trx_addons_get_option('notify_about_new_registration');
			if ( $notify != 'no' ) {
				// Send notify to the site admin
				$admin_email = sanitize_email( get_option( 'admin_email' ) );
				if ( in_array( $notify, array( 'both', 'admin' ) ) && ! empty( $admin_email ) && is_email( $admin_email ) ) {
					$subj = sprintf( wp_kses_data( __( 'Site %1$s - New user registration: %2$s', 'trx_addons' ) ),
									get_bloginfo( 'site_name' ),
									$user_data['user_name']
									);
					$msg = "\n". wp_kses_data( __( 'New registration:', 'trx_addons' ) )
						. "\n" . wp_kses_data( __( 'Name:', 'trx_addons' ) ) . ' ' . wp_kses_data( $user_data['user_name'] )
						. "\n" . wp_kses_data( __( 'E-mail:', 'trx_addons' ) ) . ' ' . wp_kses_data( $user_data['user_email'] )
						. "\n\n............ " . wp_kses_data( get_bloginfo( 'site_name' ) ) . " (" . wp_kses_data( home_url( '/' ) ) . ") ............";
					$head = "From: {$user_data['user_email']}\n"
						//. "Reply-To: {$user_data['user_email']}\n"
						. "Content-Type: text/html; charset=UTF-8\n";
					wp_mail( $admin_email, $subj, nl2br( $msg ), $head );
				}
				// Send notify to the new user
				if ( in_array( $notify, array( 'both', 'user' ) ) && is_email( $user_data['user_email'] ) ) {
					$subj = sprintf( wp_kses_data( __( 'Welcome to the "%s"', 'trx_addons' ) ), get_bloginfo( 'site_name' ) );
					$msg = "\n". wp_kses_data( __( 'Your registration data:', 'trx_addons' ) )
						. "\n" . wp_kses_data( __( 'Name:', 'trx_addons' ) ) . ' ' . wp_kses_data( $user_data['user_name'] )
						. "\n" . wp_kses_data( __( 'E-mail:', 'trx_addons' ) ) . ' ' . wp_kses_data( $user_data['user_email'] )
						. "\n" . wp_kses_data( __( 'Login:', 'trx_addons' ) ) . ' ' . wp_kses_data( $user_data['user_name'] ) . ' ' . wp_kses_data( __( '(also you can use your email as a login)', 'trx_addons' ) )
						. "\n" . wp_kses_data( __( 'Password:', 'trx_addons' ) ) . ' ' . wp_kses_data( $user_data['user_pass'] )
						. "\n\n............ " . wp_kses_data( get_bloginfo( 'site_name' ) ) . " (<a href=\"" . wp_kses_data( home_url( '/' ) ) . "\">" . wp_kses_data( home_url( '/' ) ) . "</a>) ............";
					$head = "From: " . sanitize_email( $admin_email ) . "\n"
						//. "Reply-To: " . sanitize_email( $admin_email ) . "\n"
						. "Content-Type: text/html; charset=UTF-8\n";
					wp_mail( $user_data['user_email'], $subj, nl2br( $msg ), $head );
				}
			}
		}
		return $rez;
	}
}

if ( ! function_exists( 'trx_addons_users_login_user' ) ) {
	add_action( 'wp_ajax_trx_addons_login_user',		'trx_addons_users_login_user' );
	add_action( 'wp_ajax_nopriv_trx_addons_login_user',	'trx_addons_users_login_user' );
	/**
	 * Login user via AJAX (if 'login_via_ajax' option is 'on')
	 * 
	 * @hooked wp_ajax_trx_addons_login_user
	 * @hooked wp_ajax_nopriv_trx_addons_login_user
	 */
	function trx_addons_users_login_user() {

		if ( ! trx_addons_get_option('login_via_ajax') ) {
			return;
		}
	
		trx_addons_verify_nonce();

		$user_log = ! empty( $_REQUEST['user_log'] ) ? sanitize_text_field( substr( $_REQUEST['user_log'], 0, 60 ) ) : '';
		$user_pwd = ! empty( $_REQUEST['user_pwd'] ) ? sanitize_text_field( substr( $_REQUEST['user_pwd'], 0, 60 ) ) : '';
		$remember = ! empty( $_REQUEST['remember'] ) ? substr( $_REQUEST['remember'], 0, 7 ) == 'forever' : '';

		$response = array('error' => '');

		if ( is_email( $user_log ) ) {
			$user = get_user_by( 'email', $user_log );
			if ( $user ) {
				$user_log = $user->user_login;
			}
		}

		if ( ! empty( $user_log ) && ! empty( $user_pwd ) ) {
			$rez = wp_signon( array(
								'user_login'    => $user_log,
								'user_password' => $user_pwd,
								'remember'      => $remember
								),
							is_ssl()
							);
	
			if ( is_wp_error( $rez ) ) {
				$response['error'] = $rez->get_error_message();
			}
			if ( ! ( $rez instanceof WP_User ) ) {
				$response['error'] = esc_html__( 'Incorrect login or password!', 'trx_addons' );
			} else {
				$response['redirect_to'] = trx_addons_remove_protocol( apply_filters( 'login_redirect', 
												! empty( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : '',
												'',
												$rez
											) );
			}
		} else {
			$response['error'] = esc_html__( 'Login or password is empty!', 'trx_addons' );
		}
		trx_addons_ajax_response( $response );
	}
}



/* Add socials to the user profile
-------------------------------------------------------------------------------- */

if ( ! function_exists( 'trx_addons_users_need_options' ) ) {
	add_filter( 'trx_addons_filter_need_options', 'trx_addons_users_need_options' );
	/**
	 * Check if current screen need to load options scripts and styles
	 * 
	 * @hooked trx_addons_filter_need_options
	 *
	 * @param bool $need  Filter value
	 * 
	 * @return bool     True if current screen need to load options scripts and styles
	 */
	function trx_addons_users_need_options( $need = false ) {
		if ( ! $need ) {
			// If current screen is 'Edit User'
			$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : false;
			$need = is_object( $screen ) && in_array( $screen->id, array( 'profile', 'user-edit' ) );
		}
		return $need;
	}
}

if ( ! function_exists( 'trx_addons_users_load_scripts' ) ) {
	add_action( "admin_enqueue_scripts", 'trx_addons_users_load_scripts' );
	/**
	 * Enqueue scripts and styles for the user profile
	 * 
	 * @hooked admin_enqueue_scripts
	 */
	function trx_addons_users_load_scripts() {
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : false;
		if ( is_object( $screen ) && $screen->id == 'profile' ) {
			wp_localize_script( 'trx_addons-options', 'TRX_ADDONS_DEPENDENCIES', 
								trx_addons_get_options_dependencies( trx_addons_users_get_fields() ) );
		}
	}
}

if ( ! function_exists( 'trx_addons_users_get_meta' ) ) {
	/**
	 * Return user meta
	 *
	 * @param int $user_id User ID
	 * 
	 * @return array     User meta
	 */
	function trx_addons_users_get_meta( $user_id = 0 ) { 
		return apply_filters( 'trx_addons_filter_user_meta_load', get_the_author_meta( 'trx_addons_options', $user_id ), $user_id );
	}
}

if ( ! function_exists( 'trx_addons_users_get_fields' ) ) {
	/**
	 * Return user meta additional fields
	 * 
	 * @trigger trx_addons_filter_user_meta_fields
	 *
	 * @param int $user_id User ID
	 * 
	 * @return array     User meta fields
	 */
	function trx_addons_users_get_fields( $user_id = 0 ) { 
		return apply_filters( 'trx_addons_filter_user_meta_fields', array(
				'socials' => array(
					"title" => esc_html__("Socials", 'trx_addons'),
					"desc" => wp_kses_data( __("Clone this field group, select an icon/image, specify social network's title and provide the URL to your profile", 'trx_addons') ),
					"clone" => true,
					"std" => array(array()),
					"type" => "group",
					"fields" => array(
						"name" => array(
							"title" => esc_html__("Icon", 'trx_addons'),
							"desc" => wp_kses_data( __("Select an icon for the network", 'trx_addons') ),
							"class" => "trx_addons_column-1_5 trx_addons_new_row",
							"std" => "",
							"options" => array(),
							"style" => trx_addons_get_setting('socials_type'),
							"type" => "icons"
						),
						'title' => array(
							"title" => esc_html__('Title', 'trx_addons'),
							"desc" => wp_kses_data( __("The name of the social network. If left empty, the icon's name will be used", 'trx_addons') ),
							"class" => "trx_addons_column-2_5",
							"std" => "",
							"type" => "text"
						),
						'url' => array(
							"title" => esc_html__('URL to your profile', 'trx_addons'),
							"desc" => wp_kses_data( __("Provide a link to the profile in the chosen network", 'trx_addons') ),
							"class" => "trx_addons_column-2_5",
							"std" => "",
							"type" => "text"
						),
					)
				)
			),
			$user_id );
	}
}

if ( ! function_exists( 'trx_addons_users_add_fields' ) ) {
	add_action( 'show_user_profile', 'trx_addons_users_add_fields' );
	add_action( 'edit_user_profile', 'trx_addons_users_add_fields' );
	/**
	 * Add extra fields to the user profile
	 * 
	 * @hooked show_user_profile
	 * @hooked edit_user_profile
	 * 
	 * @param object $user User object
	 */
	function trx_addons_users_add_fields( $user ) { 
		if ( ! is_admin() || ! current_user_can( 'edit_user', $user->ID ) ) {
			return;
		}
		$options = trx_addons_users_get_meta( $user->ID );
		$meta_box = trx_addons_users_get_fields( $user->ID );
		foreach ( $meta_box as $k => $v ) {
			if ( isset( $meta_box[ $k ]['std'] ) ) {
				$meta_box[ $k ]['val'] = isset( $options[ $k ] ) ? $options[ $k ] : $meta_box[ $k ]['std'];
			}
		}
		?>
		<h2><?php esc_html_e('Social links', 'trx_addons'); ?></h2>
		<table class="form-table">
			<tr>
				<th><label><?php esc_html_e('Socials', 'trx_addons'); ?>:</label></th>
				<td>
					<div class="trx_addons_options">
						<?php trx_addons_options_show_fields( $meta_box, 'user' ); ?>
					</div>
				</td>
			</tr>
		</table>
		<?php		
	}
}

if ( ! function_exists( 'trx_addons_users_save_fields' ) ) {
	add_action( 'personal_options_update',	'trx_addons_users_save_fields' );
	add_action( 'edit_user_profile_update',	'trx_addons_users_save_fields' );
	/**
	 * Save extra fields to the user profile on user update
	 * 
	 * @hooked personal_options_update
	 * @hooked edit_user_profile_update
	 * 
	 * @trigger trx_addons_filter_user_meta_save
	 * 
	 * @param int $user_id User ID
	 */
	function trx_addons_users_save_fields( $user_id ) {
		if ( ! is_admin() || ! current_user_can( 'edit_user', $user_id ) ) {
			return;
		}
		if ( function_exists( 'check_admin_referer' ) ) {
			check_admin_referer( 'update-user_' . $user_id );
		}

		$options = array();
		$meta_box = trx_addons_users_get_fields( $user_id );
		foreach ( $meta_box as $k => $v ) {
			if ( ! isset( $v['std'] ) ) {
				continue;
			}
			$options[ $k ] = trx_addons_options_get_field_value( $k, $v );
		}
		$options = apply_filters( 'trx_addons_filter_user_meta_save', $options, $user_id );

		if ( count( $options ) > 0 ) {
			update_user_meta( $user_id, 'trx_addons_options', $options );
		}
	}
}

if ( ! function_exists('trx_addons_users_show_meta' ) ) {
	add_action( 'trx_addons_action_user_meta',	'trx_addons_users_show_meta' );
	/**
	 * Show user's socials on the frontend
	 * 
	 * @hooked trx_addons_action_user_meta
	 * 
	 * @param int $user_id User ID. If 0 - get ID of post's author
	 */
	function trx_addons_users_show_meta( $user_id = 0 ) { 
		if ( $user_id == 0 ) {
			$user_id = get_the_author_meta('ID');
		}
		$options = trx_addons_users_get_meta( $user_id );
		if ( ! empty( $options['socials'][0]['url'] ) ) {
			?><div class="socials_wrap"><?php
				trx_addons_show_layout( trx_addons_get_socials_links_custom( $options['socials'] ) );
			?></div><?php
		}
	}
}
