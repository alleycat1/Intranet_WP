<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_Shortcode_Three' ) ) :

	final class WPSC_Shortcode_Three {

		/**
		 * Set whether ticket url is authenticated or not.
		 *
		 * @var boolean
		 */
		public static $url_auth = false;

		/**
		 * Initialize this class
		 */
		public static function init() {

			// register shortcode.
			add_shortcode( 'wpsc_open_ticket', array( __CLASS__, 'layout' ) );

			// Authenticate.
			add_action( 'wp_ajax_wpsc_authenticate_open_ticket', array( __CLASS__, 'get_authenticate_open_ticket' ) );
			add_action( 'wp_ajax_nopriv_wpsc_authenticate_open_ticket', array( __CLASS__, 'get_authenticate_open_ticket' ) );
			add_action( 'wp_ajax_nopriv_wpsc_confirm_open_ticket_auth', array( __CLASS__, 'confirm_open_ticket_auth' ) );
		}

		/**
		 * Layout for this shortcode
		 *
		 * @param array $attrs - Shortcode attributes.
		 * @return string
		 */
		public static function layout( $attrs ) {

			$current_user = WPSC_Current_User::$current_user;
			$ticket_id = isset( $_REQUEST['ticket-id'] ) ? intval( $_REQUEST['ticket-id'] ) : 0; // phpcs:ignore
			if ( ! $ticket_id ) {
				$ticket_id = isset( $_REQUEST['ticket_id'] ) ? intval( $_REQUEST['ticket_id'] ) : 0; // phpcs:ignore
			}

			// ticket URL authentication.
			$advanced = get_option( 'wpsc-ms-advanced-settings' );
			if ( ! $advanced['ticket-url-auth'] ) {

				$auth_code = isset( $_REQUEST['auth-code'] ) ? sanitize_text_field( $_REQUEST['auth-code'] ) : ''; // phpcs:ignore
				if ( ! $auth_code ) {
					$auth_code = isset( $_REQUEST['auth_code'] ) ? sanitize_text_field( $_REQUEST['auth_code'] ) : ''; // phpcs:ignore
				}

				if ( $ticket_id && $auth_code ) {
					$ticket = new WPSC_Ticket( $ticket_id );
					self::$url_auth = $ticket->auth_code == $auth_code ? true : false;
				}
			}

			ob_start();?>
			<div id="wpsc-container" style="display:none;">
				<div class="wpsc-shortcode-container" style="border: none !important;">
					<?php

					// logged in.
					if ( $current_user->is_customer ) {

						if ( $ticket_id ) {

							// js events.
							add_action( 'wpsc_js_ready', array( __CLASS__, 'register_js_ready_function' ) );
							add_action( 'wpsc_js_after_ticket_reply', array( __CLASS__, 'js_after_ticket_reply' ) );
							add_action( 'wpsc_js_after_close_ticket', array( __CLASS__, 'js_after_close_ticket' ) );
							?>
							<div class="wpsc-body"></div>
							<?php

						} else {

							self::load_otp_form();
						}
					} else {

						// not logged in.
						if ( $ticket_id && ! self::$url_auth ) {

							WPSC_Frontend::load_authentication_screen( false, false );
							?>
							<div class="wpsc-form-devider">
								<span>----</span>
								<span class="label"><?php esc_attr_e( 'OR', 'supportcandy' ); ?></span>
								<span>----</span>
							</div>
							<?php
							self::load_otp_form( $ticket_id );

						} elseif ( $ticket_id && self::$url_auth ) {

							?>
							<div class="wpsc-body"></div>
							<script>
								jQuery(document).ready(function(){
									wpsc_get_individual_ticket(<?php echo intval( $ticket_id ); ?>);
								});
							</script>
							<?php

						} else {

							self::load_otp_form();
						}
					}
					?>

				</div>
				<style>
					.wpsc-it-container {
						margin: 0 !important;
					}
					.wpsc-it-reply-section-container {
						padding-left: 3px;
					}
				</style>
			</div>
			<?php
			WPSC_Frontend::load_html_snippets();
			self::load_js_functions( $ticket_id );
			return ob_get_clean();
		}

		/**
		 * Load OTP form
		 *
		 * @param integer $ticket_id - ticket id.
		 * @return void
		 */
		public static function load_otp_form( $ticket_id = 0 ) {

			$current_user = WPSC_Current_User::$current_user;
			$ticket_id    = $ticket_id ? $ticket_id : '';
			?>
			<div class="wpsc-auth-container">
				<div class="auth-inner-container">
					<h2><?php esc_attr_e( 'Open existing ticket', 'supportcandy' ); ?></h2>
					<form onsubmit="return false;" class="wpsc-login wpsc-authenticate-open-ticket">

						<input type="text" name="ticket_id" placeholder="<?php esc_attr_e( 'Ticket ID', 'supportcandy' ); ?>" value="<?php echo esc_attr( $ticket_id ); ?>" autocomplete="off"/>
						<?php

						if ( ! $current_user->is_customer ) {
							?>
							<input type="text" name="email_address" placeholder="<?php esc_attr_e( 'Email Address', 'supportcandy' ); ?>" autocomplete="off"/>
							<?php
						} else {
							?>
							<input type="hidden" name="email_address" value="<?php echo esc_attr( $current_user->customer->email ); ?>"/>
							<?php
						}
						?>

						<button class="wpsc-button normal primary" onclick="wpsc_authenticate_open_ticket(this)"><?php esc_attr_e( 'Submit', 'supportcandy' ); ?></button>
						<button class="wpsc-button normal secondary" onclick="window.location.reload();"><?php esc_attr_e( 'Cancel', 'supportcandy' ); ?></button>
						<input type="hidden" name="action" value="wpsc_authenticate_open_ticket"/>
						<input type="hidden" name="_ajax_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wpsc_authenticate_open_ticket' ) ); ?>">
					</form>
					<script>
						/**
						 * Load OTP verification
						 */
						function wpsc_authenticate_open_ticket(el) {

							const form = jQuery(el).closest('form')[0];
							const dataform = new FormData(form);

							if (!dataform.get('ticket_id').trim() || !dataform.get('email_address').trim()) {
								alert(supportcandy.translations.req_fields_missing);
								return;
							}

							var authContainer = jQuery(el).closest('.wpsc-auth-container');
							authContainer.html(supportcandy.loader_html);
							jQuery.ajax({
								url: supportcandy.ajax_url,
								type: 'POST',
								data: dataform,
								processData: false,
								contentType: false
							}).done(function (res) {
								if (typeof(res) == "object") {
									if (res.ticket_url) {
										window.location.href = res.ticket_url;
									} else {
										alert(supportcandy.translations.something_wrong);
										window.location.reload();
									}
								} else {
									authContainer.html(res);
								}
							}).fail(function (data) {
								alert(supportcandy.translations.something_wrong);
								window.location.reload();
							}); 
						}
					</script>
					<?php
					if ( $current_user->is_customer ) :
						?>
						<div style="display:flex;flex-direction:column;margin: 10px 0 0;font-size: 12px;">
							<span>
							<?php
								/* translators: %1$s: customer name */
								printf( esc_attr__( 'Logged-in as %1$s', 'supportcandy' ), '<strong>' . esc_attr( $current_user->customer->name ) . '</strong>' );
							?>
							</span>
							<a class="wpsc-link" href="javascript:wpsc_user_logout(this, '<?php echo esc_attr( wp_create_nonce( 'wpsc_user_logout' ) ); ?>');"><?php esc_attr_e( 'Log out', 'supportcandy' ); ?></a>
						</div>
						<?php
					endif
					?>
				</div>
			</div>
			<?php
		}

		/**
		 * Load js functions for this shortcode
		 *
		 * @param integer $ticket_id - ticket id.
		 * @return void
		 */
		public static function load_js_functions( $ticket_id ) {
			?>

			<script type="text/javascript">
				/**
				 * Get create ticket form
				 */
				function wpsc_get_individual_ticket() {

					jQuery('.wpsc-body').html(supportcandy.loader_html);

					if (supportcandy.is_reload != 1) {
						wpsc_scroll_top();
					} else { supportcandy.is_reload = 0 }

					var url = new URL(window.location.href);
					var search_params = url.searchParams;

					var data = { action: 'wpsc_get_individual_ticket', ticket_id: <?php echo esc_attr( $ticket_id ); ?> };
					search_params.forEach(function(value, key) {
						data[key] = value;
					});
					jQuery.post(supportcandy.ajax_url, data).done(function (response) {
						jQuery('.wpsc-body').html(response);
						wpsc_reset_responsive_style();
					}).fail(function(response){
						jQuery('.wpsc-body').html('<div style="display:flex; justify-content:center; margin:0 15px 15px; width:100%;"><?php esc_attr_e( 'Unauthorized access!', 'supportcandy' ); ?></div>');
					});
				}
			</script>
			<?php
		}

		/**
		 * JS ready function
		 *
		 * @return void
		 */
		public static function register_js_ready_function() {

			echo 'wpsc_get_individual_ticket();' . PHP_EOL;
		}

		/**
		 * Get authentication OTP screen for open ticket
		 *
		 * @return void
		 */
		public static function get_authenticate_open_ticket() {

			if ( check_ajax_referer( 'wpsc_authenticate_open_ticket', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			$ticket_id     = isset( $_POST['ticket_id'] ) ? intval( $_POST['ticket_id'] ) : 0;
			$email_address = isset( $_POST['email_address'] ) ? sanitize_text_field( wp_unslash( $_POST['email_address'] ) ) : '';
			if ( ! $ticket_id || ! $email_address || ! filter_var( $email_address, FILTER_VALIDATE_EMAIL ) ) {
				wp_send_json_error( 'Bad request', 400 );
			}

			$ticket = new WPSC_Ticket( $ticket_id );
			if ( ! $ticket->id ) {
				wp_send_json_error( 'Unauthorized request!', 401 );
			}

			WPSC_Individual_Ticket::$ticket = $ticket;

			$current_user = WPSC_Current_User::$current_user;
			if ( WPSC_Individual_Ticket::is_customer() || ( $current_user->is_agent && WPSC_Individual_Ticket::has_ticket_cap( 'view' ) ) ) {

				// ticket url.
				$page_settings = get_option( 'wpsc-gs-page-settings' );
				$url           = get_permalink( $page_settings['open-ticket-page'] );
				$ticket_url    = add_query_arg(
					array(
						'ticket-id' => $ticket_id,
					),
					$url
				);

				header( 'Content-Type: application/json' );
				echo wp_json_encode(
					array(
						'ticket_url' => $ticket_url,
					)
				);
				wp_die();
			}

			$otp = WPSC_Email_OTP::insert(
				array(
					'email'       => $email_address,
					'date_expiry' => ( new DateTime() )->add( new DateInterval( 'P1D' ) )->format( 'Y-m-d H:i:s' ),
					'data'        => wp_json_encode(
						array(
							'email'     => $email_address,
							'name'      => $ticket->customer->name,
							'ticket_id' => $ticket_id,
						)
					),
				)
			);

			// Send OTP for login.
			WPSC_EN_Guest_Login_OTP::send_otp( $otp );
			?>

			<div class="auth-inner-container">
				<h2><?php esc_attr_e( 'Open existing ticket', 'supportcandy' ); ?></h2>
				<small style="margin: 0 0 5px;"><?php esc_attr_e( 'We have sent 6-digit one time password on your given email address. Please insert it below and submit to open ticket!', 'supportcandy' ); ?></small>
				<form onsubmit="return false;" class="wpsc-login wpsc-confirm-open-ticket-auth">
					<input type="text" name="otp" autocomplete="off"/>
					<button class="wpsc-button normal primary" onclick="wpsc_confirm_open_ticket_auth(this, '<?php echo esc_attr( wp_create_nonce( 'wpsc_confirm_open_ticket_auth' ) ); ?>')"><?php esc_attr_e( 'Submit', 'supportcandy' ); ?></button>
					<input type="hidden" name="action" value="wpsc_confirm_open_ticket_auth"/>
					<input type="hidden" name="otp_id" value="<?php echo esc_attr( $otp->id ); ?>">
					<input type="hidden" name="_ajax_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wpsc_confirm_open_ticket_auth' ) ); ?>">
				</form>
				<script>
					/**
					 * Confirm OTP
					 *
					 * @return void
					 */
					function wpsc_confirm_open_ticket_auth(el, nonce) {

						const form = jQuery(el).closest('form')[0];
						const dataform = new FormData(form);

						if (!dataform.get('otp').trim()) {
							alert(supportcandy.translations.req_fields_missing);
							return;
						}

						jQuery(el).closest('.wpsc-auth-container').html(supportcandy.loader_html);
						jQuery.ajax({
							url: supportcandy.ajax_url,
							type: 'POST',
							data: dataform,
							processData: false,
							contentType: false
						}).done(function (res) {
							if (res.isSuccess == 1) {
								window.location.href = res.ticket_url;
							} else {
								alert(supportcandy.translations.something_wrong);
								window.location.reload();
							}
						});
					}
				</script>
			</div>
			<?php
			wp_die();
		}

		/**
		 * Confirm guest login
		 *
		 * @return void
		 */
		public static function confirm_open_ticket_auth() {

			if ( check_ajax_referer( 'wpsc_confirm_open_ticket_auth', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			$page_settings = get_option( 'wpsc-gs-page-settings' );

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
				echo wp_json_encode( array( 'isSuccess' => 0 ) );
				wp_die();
			}

			$data               = json_decode( $otp->data, true );
			$data['auth_token'] = WPSC_Functions::get_random_string( 100 );
			$data['auth_type']  = 'open-ticket';
			$otp->data          = wp_json_encode( $data );
			$otp->save();

			$auth = array(
				'email' => $otp->email,
				'token' => $data['auth_token'],
			);

			setcookie( 'wpsc_guest_login_auth', wp_json_encode( $auth ), $otp->date_expiry->getTimestamp(), '/' );

			$url        = get_permalink( $page_settings['open-ticket-page'] );
			$ticket_url = add_query_arg(
				array(
					'ticket-id' => $data['ticket_id'],
				),
				$url
			);

			wp_send_json(
				array(
					'isSuccess'  => 1,
					'ticket_url' => $ticket_url,
				)
			);
		}

		/**
		 * After ticket reply
		 *
		 * @return void
		 */
		public static function js_after_ticket_reply() {

			echo 'wpsc_get_individual_ticket(ticket_id)' . PHP_EOL;
		}

		/**
		 * JS after close ticket
		 *
		 * @return void
		 */
		public static function js_after_close_ticket() {

			echo 'wpsc_get_individual_ticket(ticket_id)' . PHP_EOL;
		}
	}
endif;

WPSC_Shortcode_Three::init();
