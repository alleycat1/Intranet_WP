<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_Current_User' ) ) :

	final class WPSC_Current_User {

		/**
		 * Current user object to access
		 *
		 * @var WPSC_Current_User
		 */
		public static $current_user;

		/**
		 * Login type
		 *
		 * @var string
		 */
		public static $login_type = '';

		/**
		 * Guest login type
		 *
		 * @var string
		 */
		public static $guest_login_type = '';

		/**
		 * Current user WP object
		 *
		 * @var WP_User
		 */
		public $user;

		/**
		 * Check whether user is guest
		 *
		 * @var boolean
		 */
		public $is_guest = false;

		/**
		 * Check whether user is customer or not
		 *
		 * @var boolean
		 */
		public $is_customer = false;

		/**
		 * Customer object for current user
		 *
		 * @var WPSC_Customer
		 */
		public $customer;

		/**
		 * Check whether user is an agent or not
		 *
		 * @var boolean
		 */
		public $is_agent = false;

		/**
		 * Agent object for current user
		 *
		 * @var WPSC_Agent
		 */
		public $agent;

		/**
		 * Current user level. e.g. customer, agent or admin
		 *
		 * @var string
		 */
		public $level;

		/**
		 * Initialize this class
		 *
		 * @return void
		 */
		public static function init() {

			add_action( 'init', array( __CLASS__, 'load_current_user' ) );

			// default login.
			add_action( 'wp_ajax_nopriv_wpsc_default_login', array( __CLASS__, 'check_user_login' ) );

			// default registration.
			add_action( 'wp_ajax_nopriv_wpsc_get_default_registration', array( __CLASS__, 'get_user_registration' ) );
			add_action( 'wp_ajax_nopriv_wpsc_check_username_availability', array( __CLASS__, 'check_username_availability' ) );
			add_action( 'wp_ajax_nopriv_wpsc_authenticate_registration', array( __CLASS__, 'send_registration_otp' ) );
			add_action( 'wp_ajax_nopriv_wpsc_confirm_registration', array( __CLASS__, 'register_user' ) );

			// sign-in using otp.
			add_action( 'wp_ajax_nopriv_wpsc_get_guest_sign_in', array( __CLASS__, 'get_guest_sign_in' ) );
			add_action( 'wp_ajax_nopriv_wpsc_authenticate_guest_login', array( __CLASS__, 'get_guest_sign_in_auth' ) );
			add_action( 'wp_ajax_nopriv_wpsc_confirm_guest_login', array( __CLASS__, 'confirm_guest_login' ) );

			// user registration email template.
			add_filter( 'wpsc_email_notification_page_sections', array( __CLASS__, 'registration_email_template_section' ) );

			// guest login email template.
			add_filter( 'wpsc_email_notification_page_sections', array( __CLASS__, 'guest_login_email_template_section' ) );
		}

		/**
		 * Initialize the object
		 *
		 * @param string $email - email address.
		 */
		public function __construct( $email = '' ) {

			$user = $email ? get_user_by( 'email', $email ) : new WP_User();
			if ( $user === false ) {
				$user = new WP_User();
			}
			$this->user = $user;

			// is guest.
			$this->is_guest = $this->user->ID ? false : true;

			// Set customer object.
			if ( $this->user->ID ) {

				$this->is_customer = true;
				$customer = WPSC_Customer::get_by_email( $this->user->user_email );
				if ( $customer->id ) {
					$this->customer = $customer;
				} else {
					$this->customer = WPSC_Customer::insert(
						array(
							'user'  => $this->user->ID,
							'name'  => $this->user->display_name,
							'email' => $this->user->user_email,
						)
					);
				}
			} elseif ( $email ) {

				$this->is_customer = true;
				$this->customer    = WPSC_Customer::get_by_email( $email );
			}

			// Set agent object.
			$agent = WPSC_Agent::get_by_user_id( $this->user->ID );
			if ( $agent->id && $agent->is_active ) {
				$this->is_agent = true;
				$this->agent    = $agent;
			}

			// set leval.
			if ( WPSC_Functions::is_site_admin() ) {
				$this->level = 'admin';
			} elseif ( $this->is_agent ) {
				$this->level = 'agent';
			} elseif ( $this->is_customer ) {
				$this->level = 'customer';
			} else {
				$this->level = 'none';
			}
		}

		/**
		 * Load current wpsc user
		 *
		 * @return void
		 */
		public static function load_current_user() {

			global $current_user;

			// wp logged-in user.
			$email = $current_user->ID ? $current_user->user_email : '';
			if ( $email ) {
				self::$current_user = new WPSC_Current_User( $email );
				self::$login_type   = 'registered';
				return;
			}

			// guest login.
			$gs = get_option( 'wpsc-gs-general' );

			$login_auth = isset( $_COOKIE['wpsc_guest_login_auth'] ) ? sanitize_text_field( wp_unslash( $_COOKIE['wpsc_guest_login_auth'] ) ) : '';
			$login_auth = $login_auth ? json_decode( $login_auth ) : false;

			if ( ! $login_auth ) {
				self::$current_user = new WPSC_Current_User();
				return;
			}

			$login_auth->email = $login_auth->email ? sanitize_email( $login_auth->email ) : '';
			if ( ! $login_auth->email ) {
				self::$current_user = new WPSC_Current_User();
				return;
			}

			if ( $login_auth && self::validate_guest_login( $login_auth ) ) {
				self::$current_user = new WPSC_Current_User( $login_auth->email );
				return;
			}

			self::$current_user = new WPSC_Current_User();
		}

		/**
		 * Change current user
		 *
		 * @param string $email - email string.
		 *
		 * @return string
		 */
		public static function change_current_user( $email ) {

			$current_user       = new WPSC_Current_User( $email );
			self::$current_user = $current_user;
			return self::$current_user;
		}

		/**
		 * Return ticket list filters for the user.
		 *
		 * @return array
		 */
		public function get_tl_filters() {

			$filters = array(
				'default' => array(),
				'saved'   => array(),
			);

			// default filters.
			$default_filters = get_option( $this->is_agent ? 'wpsc-atl-default-filters' : 'wpsc-ctl-default-filters' );
			foreach ( $default_filters as $index => $filter ) {

				// exclude if current user does not have access to deleted filter.
				if ( $index == 'deleted' && ! $this->agent->has_cap( 'dtt-access' ) ) {
					continue;
				}

				// exclude if filter is not enabled.
				if ( ! $filter['is_enable'] ) {
					continue;
				}

				$filters['default'][ $index ] = $filter;
			}

			// saved filters.
			$filters['saved'] = $this->get_saved_filters();

			// return filters.
			return $filters;
		}

		/**
		 * Return all saved filters for current user
		 *
		 * @return array
		 */
		public function get_saved_filters() {

			$saved_filters = ! $this->is_guest && $this->user->ID ? get_user_meta( $this->user->ID, get_current_blog_id() . '-wpsc-tl-saved-filters', true ) : array();
			return $saved_filters ? $saved_filters : array();
		}

		/**
		 * Return attachment auth for URLs created in rest api
		 *
		 * @return string
		 */
		public function get_attachment_auth() {

			$now = new DateTime();
			$diff = new DateInterval( 'PT1H' );

			$auth = get_user_meta( $this->user->ID, get_current_blog_id() . '-wpsc-rest-attachment-auth', true );
			if ( $auth ) {
				$dt = new DateTime( $auth['date'] );
				if ( $now < $dt->add( $diff ) ) {
					return $auth['key'];
				}
			}

			$auth = array(
				'key'  => WPSC_Functions::get_random_string( 12 ),
				'date' => $now->format( 'Y-m-d H:i:s' ),
			);
			update_user_meta( $this->user->ID, get_current_blog_id() . '-wpsc-rest-attachment-auth', $auth );
			return $auth['key'];
		}

		/**
		 * Get ticket list items
		 *
		 * @return array
		 */
		public function get_tl_list_items() {

			return $this->is_agent ? get_option( 'wpsc-atl-list-items' ) : get_option( 'wpsc-ctl-list-items' );
		}

		/**
		 * Get default orderby
		 *
		 * @return array
		 */
		public function get_tl_default_settings() {

			return $this->is_agent ? get_option( 'wpsc-tl-ms-agent-view' ) : get_option( 'wpsc-tl-ms-customer-view' );
		}

		/**
		 * Return system query for the current user for ticket list
		 *
		 * @param array $filters - filters.
		 * @return array
		 */
		public function get_tl_system_query( $filters ) {

			$current_user = self::$current_user;

			$adv_setting = get_option( 'wpsc-ms-advanced-settings' );
			if ( $adv_setting['public-mode'] && ! $current_user->is_agent ) {
				return $filters;
			}

			$system_query = array( 'relation' => 'OR' );

			$system_query[] = array(
				'slug'    => 'customer',
				'compare' => '=',
				'val'     => $this->customer->id,
			);

			if ( $this->is_agent ) {

				if ( $this->agent->has_cap( 'view-assigned-me' ) ) {
					$system_query[] = array(
						'slug'    => 'assigned_agent',
						'compare' => '=',
						'val'     => $this->agent->id,
					);
				}

				if ( $this->agent->has_cap( 'view-unassigned' ) ) {
					$system_query[] = array(
						'slug'    => 'assigned_agent',
						'compare' => '=',
						'val'     => '',
					);
				}

				if ( $this->agent->has_cap( 'view-assigned-others' ) ) {
					$system_query[] = array(
						'slug'    => 'assigned_agent',
						'compare' => 'NOT IN',
						'val'     => array( $this->agent->id, '' ),
					);
				}
			}

			return apply_filters( 'wpsc_tl_current_user_system_query', $system_query, $filters, $this );
		}

		/**
		 * Check login for default login form
		 *
		 * @return void
		 */
		public static function check_user_login() {

			if ( check_ajax_referer( 'wpsc_default_login', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			WPSC_MS_Recaptcha::validate( 'submit_login' );

			$username = isset( $_POST['username'] ) ? sanitize_text_field( wp_unslash( $_POST['username'] ) ) : '';
			if ( ! $username ) {
				wp_send_json_error( 'Bad request', 400 );
			}

			$password = isset( $_POST['password'] ) ? wp_unslash( $_POST['password'] ) : ''; 	// phpcs:ignore
			if ( ! $password ) {
				wp_send_json_error( 'Bad request', 400 );
			}

			$remember_me = isset( $_POST['remember_me'] ) ? true : false;

			$user = wp_signon(
				array(
					'user_login'    => $username,
					'user_password' => $password,
					'remember'      => $remember_me,
				)
			);

			$success = is_wp_error( $user ) ? 0 : 1;
			wp_send_json( array( 'success' => $success ) );
		}

		/**
		 * Get user registration
		 *
		 * @return void
		 */
		public static function get_user_registration() {

			$page_settings = get_option( 'wpsc-gs-page-settings' );
			$recaptcha     = get_option( 'wpsc-recaptcha-settings' );
			if ( $page_settings['user-registration'] !== 'default' ) {
				wp_send_json_error( __( 'Unauthorized', 'supportcandy' ), 401 );
			}?>

			<h2><?php esc_attr_e( 'Please sign up', 'supportcandy' ); ?></h2>
			<form onsubmit="return false;" class="wpsc-login wpsc-authenticate-registration">
				<input type="text" name="firstname" placeholder="<?php esc_attr_e( 'First Name', 'supportcandy' ); ?>" autocomplete="off"/>
				<input type="text" name="lastname" placeholder="<?php esc_attr_e( 'Last Name', 'supportcandy' ); ?>" autocomplete="off"/>

				<div style="margin: 0 0 5px !important;">
					<input id="wpsc-username" type="text" name="username" style="margin-bottom: 0px !important;" placeholder="<?php esc_attr_e( 'Username', 'supportcandy' ); ?>" autocomplete="off"/>
					<small id="wpsc-username-unavailable" style="color: #e84118;font-style:italic;display:none;"><?php esc_attr_e( 'Username is already taken!', 'supportcandy' ); ?></small>
					<small id="wpsc-username-available" style="color: #4cd137;font-style:italic;display:none;"><?php esc_attr_e( 'Username is available!', 'supportcandy' ); ?></small>
					<script>
						jQuery('#wpsc-username').change(function(){
							jQuery('#wpsc-username-available').hide();
							jQuery('#wpsc-username-unavailable').hide();
							var username = jQuery(this).val().trim();
							const data = { action: 'wpsc_check_username_availability', username, _ajax_nonce: '<?php echo esc_attr( wp_create_nonce( 'wpsc_check_username_availability' ) ); ?>' };
							jQuery.post(supportcandy.ajax_url, data, function (response) {
								jQuery('input[name=is_username]').val(response.isAvailable);
								if (response.isAvailable == 1) {
									jQuery('#wpsc-username-unavailable').hide();
									jQuery('#wpsc-username-available').show();
								} else {
									jQuery('#wpsc-username-available').hide();
									jQuery('#wpsc-username-unavailable').show();
								}
							});
						});
					</script>
				</div>

				<input type="text" name="email_address" placeholder="<?php esc_attr_e( 'Email Address', 'supportcandy' ); ?>" autocomplete="off"/>
				<input type="password" name="password" placeholder="<?php esc_attr_e( 'Password', 'supportcandy' ); ?>"/>
				<input type="password" name="confirm_password" placeholder="<?php esc_attr_e( 'Confirm Password', 'supportcandy' ); ?>"/>
				<?php

				// recaptcha.
				if ( $recaptcha['allow-recaptcha'] === 1 && $recaptcha['recaptcha-version'] == 2 && $recaptcha['recaptcha-site-key'] && $recaptcha['recaptcha-secret-key'] ) {
					$unique_id = uniqid( 'wpsc_' );
					?>
				<script src="https://www.google.com/recaptcha/api.js?onload=recaptchaCallback&render=explicit" async defer></script> <?php // phpcs:ignore ?>
				<div id="<?php echo esc_attr( $unique_id ); ?>" data-sitekey="" style="margin-bottom: 5px;"></div>
				<script>
					var recaptchaCallback = function() {
						var obj = jQuery('#<?php echo esc_attr( $unique_id ); ?>');
						grecaptcha.render(obj.attr("id"), {
							"sitekey" : "<?php echo esc_attr( $recaptcha['recaptcha-site-key'] ); ?>",
							"callback" : function(token) {
								obj.closest('form').find(".g-recaptcha-response").val(token);
							}
						});
					}
				</script>
					<?php
				}
				if ( $recaptcha['allow-recaptcha'] === 1 && $recaptcha['recaptcha-version'] == 3 && $recaptcha['recaptcha-site-key'] && $recaptcha['recaptcha-secret-key'] ) {
					?>
					<script src="https://www.google.com/recaptcha/api.js?render=<?php echo esc_attr( $recaptcha['recaptcha-site-key'] ); ?>"></script> <?php // phpcs:ignore ?>
					<?php
				}
				?>

				<button class="wpsc-button normal primary" onclick="wpsc_set_default_registration(this)"><?php esc_attr_e( 'Sign Up', 'supportcandy' ); ?></button>
				<button class="wpsc-button normal secondary" onclick="window.location.reload();"><?php esc_attr_e( 'Cancel', 'supportcandy' ); ?></button>
				<input type="hidden" name="action" value="wpsc_authenticate_registration"/>
				<input type="hidden" name="is_username" value="0"/>
				<input type="hidden" name="_ajax_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wpsc_authenticate_registration' ) ); ?>">
			</form>
			<?php
			wp_die();
		}

		/**
		 * Check username availability
		 *
		 * @return void
		 */
		public static function check_username_availability() {

			if ( check_ajax_referer( 'wpsc_check_username_availability', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			$page_settings = get_option( 'wpsc-gs-page-settings' );
			if ( $page_settings['user-registration'] !== 'default' ) {
				wp_send_json_error( __( 'Unauthorized', 'supportcandy' ), 401 );
			}

			$username = isset( $_POST['username'] ) ? sanitize_text_field( wp_unslash( $_POST['username'] ) ) : '';
			if ( ! $username ) {
				wp_send_json_error( 'Something went wrong', 400 );
			}

			$flag = self::is_username_available( $username );

			wp_send_json( array( 'isAvailable' => $flag ? 0 : 1 ) );
		}

		/**
		 * Send registration OTP for email authentication
		 *
		 * @return void
		 */
		public static function send_registration_otp() {

			if ( check_ajax_referer( 'wpsc_authenticate_registration', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}
			$page_settings = get_option( 'wpsc-gs-page-settings' );
			if ( $page_settings['user-registration'] !== 'default' ) {
				wp_send_json_error( __( 'Unauthorized', 'supportcandy' ), 401 );
			}

			WPSC_MS_Recaptcha::validate( 'submit_registration' );

			$firstname = isset( $_POST['firstname'] ) ? sanitize_text_field( wp_unslash( $_POST['firstname'] ) ) : '';
			if ( ! $firstname ) {
				wp_send_json_error( 'Bad request', 400 );
			}

			$lastname = isset( $_POST['lastname'] ) ? sanitize_text_field( wp_unslash( $_POST['lastname'] ) ) : '';
			if ( ! $lastname ) {
				wp_send_json_error( 'Bad request', 400 );
			}

			$username = isset( $_POST['username'] ) ? sanitize_text_field( wp_unslash( $_POST['username'] ) ) : '';
			if ( ! $username ) {
				wp_send_json_error( 'Bad request', 400 );
			}

			if ( self::is_username_available( $username ) ) {
				wp_send_json_error( 'Bad request', 400 );
			}

			$email_address = isset( $_POST['email_address'] ) && filter_var( wp_unslash( $_POST['email_address'] ), FILTER_VALIDATE_EMAIL ) ? sanitize_text_field( wp_unslash( $_POST['email_address'] ) ) : '';
			if ( ! $email_address ) {
				wp_send_json_error( 'Bad request', 400 );
			}

			$user = get_user_by( 'email', $email_address );
			if ( $user ) {
				wp_send_json_error( 'Bad request', 400 );
			}

			$password = isset( $_POST['password'] ) ? wp_unslash( $_POST['password'] ) : ''; // phpcs:ignore
			if ( ! $password ) {
				wp_send_json_error( 'Bad request', 400 );
			}

			$data = array(
				'firstname'     => $firstname,
				'lastname'      => $lastname,
				'username'      => $username,
				'email_address' => $email_address,
				'password'      => $password,
			);

			$otp = WPSC_Email_OTP::insert(
				array(
					'email'       => $email_address,
					'date_expiry' => ( new DateTime() )->add( new DateInterval( 'PT1H' ) )->format( 'Y-m-d H:i:s' ),
					'data'        => wp_json_encode( $data ),
				)
			);

			// send email notification.
			WPSC_EN_User_Reg_OTP::send_otp( $otp );
			?>

			<h2><?php esc_attr_e( 'Please sign up', 'supportcandy' ); ?></h2>
			<small style="margin: 0 0 5px;"><?php esc_attr_e( 'We have sent 6-digit one time password on your given email address.', 'supportcandy' ); ?></small>
			<form onsubmit="return false;" class="wpsc-login wpsc-confirm-registration">
				<input type="text" name="otp" autocomplete="off"/>
				<button class="wpsc-button normal primary" onclick="wpsc_confirm_registration(this)"><?php esc_attr_e( 'Submit', 'supportcandy' ); ?></button>
				<input type="hidden" name="action" value="wpsc_confirm_registration"/>
				<input type="hidden" name="otp_id" value="<?php echo esc_attr( $otp->id ); ?>">
				<input type="hidden" name="_ajax_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wpsc_confirm_registration' ) ); ?>"/>
			</form>
			<?php
			wp_die();
		}

		/**
		 * Checks whether username is available or not
		 *
		 * @param string $username - user name string.
		 * @return boolean
		 */
		public static function is_username_available( $username ) {

			$user = get_user_by( 'login', $username );
			return $user ? true : false;
		}

		/**
		 * Register user after OTP matched
		 *
		 * @return void
		 */
		public static function register_user() {

			if ( check_ajax_referer( 'wpsc_confirm_registration', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			$page_settings = get_option( 'wpsc-gs-page-settings' );
			if ( $page_settings['user-registration'] !== 'default' ) {
				wp_send_json_error( __( 'Unauthorized', 'supportcandy' ), 401 );
			}

			$verification_otp = isset( $_POST['otp'] ) ? intval( $_POST['otp'] ) : '';
			if ( ! $verification_otp ) {
				wp_send_json_error( 'Bad request', 400 );
			}

			$id = isset( $_POST['otp_id'] ) ? intval( $_POST['otp_id'] ) : '';
			if ( ! $id ) {
				wp_send_json_error( 'Bad request', 400 );
			}

			$otp = new WPSC_Email_OTP( $id );
			if ( ! $otp->id ) {
				wp_send_json_error( 'Bad request', 400 );
			}

			if ( ! $otp->is_valid( $verification_otp ) ) {
				wp_send_json( array( 'isSuccess' => 0 ) );
				wp_die();
			}

			$data = json_decode( $otp->data );

			// insert user.
			$display_name = $data->firstname . ' ' . $data->lastname;
			$user_id      = wp_insert_user(
				array(
					'user_login'   => $data->username,
					'user_pass'    => $data->password,
					'user_email'   => $data->email_address,
					'first_name'   => $data->firstname,
					'last_name'    => $data->lastname,
					'display_name' => $display_name,
					'role'         => 'subscriber',
				)
			);
			if ( is_wp_error( $user_id ) ) {
				wp_send_json( array( 'isSuccess' => 0 ) );
				wp_die();
			}

			$user = wp_signon(
				array(
					'user_login'    => $data->username,
					'user_password' => $data->password,
				)
			);
			wp_new_user_notification( $user_id, null, 'admin' );
			wp_send_json( array( 'isSuccess' => 1 ) );
		}

		/**
		 * User registrstion OTP email template section
		 *
		 * @param array $sections - section name.
		 * @return array
		 */
		public static function registration_email_template_section( $sections ) {

			$sections['registration-otp'] = array(
				'slug'     => 'registration_otp',
				'icon'     => 'unlock',
				'label'    => esc_attr__( 'User Registration OTP', 'supportcandy' ),
				'callback' => 'wpsc_get_en_user_reg_otp',
			);
			return $sections;
		}

		/**
		 * Get guest sign in screen
		 *
		 * @return void
		 */
		public static function get_guest_sign_in() {

			$gs = get_option( 'wpsc-gs-general' );
			$page_settings = get_option( 'wpsc-gs-page-settings' );
			if ( ! ( $page_settings['otp-login'] && in_array( 'guest', $gs['allow-create-ticket'] ) ) ) {
				wp_send_json_error( 'Unauthorozed', 400 );
			}
			?>

			<h2><?php esc_attr_e( 'Please sign in', 'supportcandy' ); ?></h2>
			<form onsubmit="return false;" class="wpsc-login authenticate-guest-login">
				<input type="text" name="email_address" placeholder="<?php esc_attr_e( 'Email Address', 'supportcandy' ); ?>" autocomplete="off"/>
				<button class="wpsc-button normal primary" onclick="wpsc_authenticate_guest_login(this)"><?php esc_attr_e( 'Sign In', 'supportcandy' ); ?></button>
				<button class="wpsc-button normal secondary" onclick="window.location.reload();"><?php esc_attr_e( 'Cancel', 'supportcandy' ); ?></button>
				<input type="hidden" name="action" value="wpsc_authenticate_guest_login"/>
				<input type="hidden" name="_ajax_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wpsc_authenticate_guest_login' ) ); ?>">
			</form>
			<?php
			wp_die();
		}

		/**
		 * Get OTP screen
		 *
		 * @return void
		 */
		public static function get_guest_sign_in_auth() {

			if ( check_ajax_referer( 'wpsc_authenticate_guest_login', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}
			$gs            = get_option( 'wpsc-gs-general' );
			$page_settings = get_option( 'wpsc-gs-page-settings' );
			if ( ! ( $page_settings['otp-login'] && in_array( 'guest', $gs['allow-create-ticket'] ) ) ) {
				wp_send_json_error( 'Unauthorozed', 400 );
			}

			$email_address = isset( $_POST['email_address'] ) && filter_var( wp_unslash( $_POST['email_address'] ), FILTER_VALIDATE_EMAIL ) ? sanitize_text_field( wp_unslash( $_POST['email_address'] ) ) : '';
			if ( ! $email_address ) {
				wp_send_json_error( 'Bad request', 400 );
			}

			$customer = WPSC_Customer::get_by_email( $email_address );
			if ( ! $customer->id ) {
				esc_attr_e( 'Invalid email address!', 'supportcandy' );
				wp_die();
			}

			$otp = WPSC_Email_OTP::insert(
				array(
					'email'       => $email_address,
					'date_expiry' => ( new DateTime() )->add( new DateInterval( 'P1D' ) )->format( 'Y-m-d H:i:s' ),
					'data'        => wp_json_encode(
						array(
							'email' => $email_address,
						)
					),
				)
			);

			// Send OTP for login.
			WPSC_EN_Guest_Login_OTP::send_otp( $otp );
			?>

			<h2><?php esc_attr_e( 'Please sign in', 'supportcandy' ); ?></h2>
			<small style="margin: 0 0 5px;"><?php esc_attr_e( 'We have sent 6-digit one time password on your given email address.', 'supportcandy' ); ?></small>
			<form onsubmit="return false;" class="wpsc-login wpsc-confirm-guest-login">
				<input type="text" name="otp" autocomplete="off"/>
				<button class="wpsc-button normal primary" onclick="wpsc_confirm_guest_login(this)"><?php esc_attr_e( 'Submit', 'supportcandy' ); ?></button>
				<input type="hidden" name="action" value="wpsc_confirm_guest_login"/>
				<input type="hidden" name="otp_id" value="<?php echo esc_attr( $otp->id ); ?>">
				<input type="hidden" name="_ajax_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wpsc_confirm_guest_login' ) ); ?>">
			</form>
			<?php
			wp_die();
		}

		/**
		 * Confirm guest login
		 *
		 * @return void
		 */
		public static function confirm_guest_login() {

			if ( check_ajax_referer( 'wpsc_confirm_guest_login', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			$gs            = get_option( 'wpsc-gs-general' );
			$page_settings = get_option( 'wpsc-gs-page-settings' );
			if ( ! ( $page_settings['otp-login'] && in_array( 'guest', $gs['allow-create-ticket'] ) ) ) {
				wp_send_json_error( 'Unauthorozed', 400 );
			}

			$verification_otp = isset( $_POST['otp'] ) ? intval( $_POST['otp'] ) : '';
			if ( ! $verification_otp ) {
				wp_send_json_error( 'Bad request', 400 );
			}

			$id = isset( $_POST['otp_id'] ) ? intval( $_POST['otp_id'] ) : '';
			if ( ! $id ) {
				wp_send_json_error( 'Bad request', 400 );
			}

			$otp = new WPSC_Email_OTP( $id );
			if ( ! $otp->id ) {
				wp_send_json_error( 'Bad request', 400 );
			}

			if ( ! $otp->is_valid( $verification_otp ) ) {
				wp_send_json( array( 'isSuccess' => 0 ) );
				wp_die();
			}

			$data               = json_decode( $otp->data, true );
			$data['auth_token'] = WPSC_Functions::get_random_string( 100 );
			$data['auth_type']  = 'login';
			$otp->data          = wp_json_encode( $data );
			$otp->save();

			// add customer record if not set.
			$customer = WPSC_Customer::get_by_email( $data['email'] );
			if ( ! $customer->id ) {
				$user = get_user_by( 'email', $data['email'] );
				if ( $user ) {

					WPSC_Customer::insert(
						array(
							'user'  => $user->ID,
							'name'  => $user->display_name,
							'email' => $user->user_email,
						)
					);

				} else {

					WPSC_Customer::insert(
						array(
							'user'  => 0,
							'name'  => $data['name'],
							'email' => $data['email'],
						)
					);
				}
			}

			$auth = array(
				'email' => $otp->email,
				'token' => $data['auth_token'],
			);

			setcookie( 'wpsc_guest_login_auth', wp_json_encode( $auth ), $otp->date_expiry->getTimestamp(), '/' );

			wp_send_json( array( 'isSuccess' => 1 ) );
		}

		/**
		 * Validate login auth token
		 *
		 * @param object $login_auth - login auth details.
		 * @return boolean
		 */
		public static function validate_guest_login( $login_auth ) {

			$gs            = get_option( 'wpsc-gs-general' );
			$page_settings = get_option( 'wpsc-gs-page-settings' );

			$results = WPSC_Email_OTP::find(
				array(
					'meta_query' => array(
						'relation' => 'AND',
						array(
							'slug'    => 'email',
							'compare' => '=',
							'val'     => $login_auth->email,
						),
					),
				)
			)['results'];

			if ( ! $results ) {
				return false;
			}

			$otp = $results[0];
			if ( ! $otp->id ) {
				return false;
			}

			$now  = new DateTime();
			$data = json_decode( $otp->data );

			if (
				isset( $data->auth_type ) &&
				( ( $data->auth_type == 'login' && $page_settings['otp-login'] && in_array( 'guest', $gs['allow-create-ticket'] ) ) || $data->auth_type == 'open-ticket' ) &&
				( $otp->date_expiry > $now && $data->auth_token == $login_auth->token )
			) {
				self::$login_type       = 'guest';
				self::$guest_login_type = $data->auth_type;
				return true;
			}

			return false;
		}

		/**
		 * Add guest login email template
		 *
		 * @param array $sections - section name.
		 * @return array
		 */
		public static function guest_login_email_template_section( $sections ) {

			$sections['guest-login-otp'] = array(
				'slug'     => 'guest_login_otp',
				'icon'     => 'unlock',
				'label'    => esc_attr__( 'Guest Login OTP', 'supportcandy' ),
				'callback' => 'wpsc_get_en_guest_login_otp',
			);
			return $sections;
		}

		/**
		 * Logout current user
		 *
		 * @return void
		 */
		public function logout() {

			global $current_user;

			$otp = WPSC_Email_OTP::find(
				array(
					'meta_query' => array(
						'relation' => 'AND',
						array(
							'slug'    => 'email',
							'compare' => '=',
							'val'     => $this->customer->email,
						),
					),
				)
			)['results'];

			if ( $otp ) :
				WPSC_Email_OTP::destroy( $otp[0] );
				@setcookie( 'wpsc_guest_login_auth', '', time(), '/' ); //phpcs:ignore
			endif;

			if ( $current_user->ID ) {
				wp_logout();
			}
		}
	}
endif;

WPSC_Current_User::init();
