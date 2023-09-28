<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_Frontend' ) ) :

	final class WPSC_Frontend {

		/**
		 * Initialize this class
		 */
		public static function init() {

			// Load scripts.
			add_action( 'wp_enqueue_scripts', array( __CLASS__, 'load_scripts' ) );

			// ticket url redirect.
			add_action( 'init', array( __CLASS__, 'ticket_url_redirect' ) );
		}

		/**
		 * Ticket url support for old versions
		 *
		 * @return void
		 */
		public static function ticket_url_redirect() {

			if ( isset( $_REQUEST['ticket_id'] ) && isset( $_REQUEST['auth_code'] ) ) { // phpcs:ignore

				$id = isset( $_REQUEST['ticket_id'] ) ? intval( $_REQUEST['ticket_id'] ) : 0; // phpcs:ignore
				if ( ! $id ) {
					return;
				}

				$ticket = new WPSC_Ticket( $id );
				if ( ! $ticket->id ) {
					return;
				}

				wp_safe_redirect( $ticket->get_url() );
				exit;
			}
		}

		/**
		 * Load JS and CSS scripts
		 *
		 * @return void
		 */
		public static function load_scripts() {

			// Check load scripts setting to load script on perticular page.
			$page_settings = get_option( 'wpsc-gs-page-settings' );
			if ( $page_settings['load-scripts'] == 'custom' && ! in_array( get_the_id(), $page_settings['load-script-pages'] ) ) {
				return;
			}

			// jquery.
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'jquery-ui-core' );

			// TinyMCE.
			wp_enqueue_editor();

			// jQuery UI Effects.
			wp_enqueue_script( 'jquery-effects-core' );
			wp_enqueue_script( 'jquery-effects-slide' );

			// WPSC Framework.
			wp_enqueue_script( 'wpsc-framework', WPSC_PLUGIN_URL . 'framework/scripts.js', array( 'jquery' ), WPSC_VERSION, true );

			if ( is_rtl() ) {
				wp_enqueue_style( 'wpsc-framework', WPSC_PLUGIN_URL . 'framework/style-rtl.css', array(), WPSC_VERSION );
			} else {
				wp_enqueue_style( 'wpsc-framework', WPSC_PLUGIN_URL . 'framework/style.css', array(), WPSC_VERSION );
			}

			wp_localize_script( 'wpsc-framework', 'supportcandy', self::get_localization_data() );

			// selectWoo.
			wp_enqueue_script( 'wpsc-selectWoo', WPSC_PLUGIN_URL . 'asset/js/selectWoo/selectWoo.full.min.js', array( 'jquery' ), WPSC_VERSION, true );
			if ( file_exists( WPSC_ABSPATH . 'asset/js/selectWoo/i18n/' . get_locale() . '.js' ) ) {
				wp_enqueue_script( 'selectWoo-lang', WPSC_PLUGIN_URL . 'asset/js/selectWoo/i18n/' . get_locale() . '.js', array( 'jquery' ), WPSC_VERSION, true );
			}
			wp_enqueue_style( 'wpsc-select2', WPSC_PLUGIN_URL . 'asset/css/select2.css', array(), WPSC_VERSION );

			// gpopover.
			wp_enqueue_script( 'gpopover', WPSC_PLUGIN_URL . 'asset/libs/gpopover/jquery.gpopover.js', array( 'jquery' ), WPSC_VERSION, true );
			wp_enqueue_style( 'gpopover', WPSC_PLUGIN_URL . 'asset/libs/gpopover/jquery.gpopover.css', array(), WPSC_VERSION );

			// jquery circle progress.
			wp_enqueue_script( 'jquery-circle-progress', WPSC_PLUGIN_URL . 'asset/libs/jquery-circle-progress/circle-progress.min.js', array( 'jquery' ), WPSC_VERSION, true );

			// flatpickr.
			wp_enqueue_script( 'flatpickr-js', WPSC_PLUGIN_URL . 'asset/libs/flatpickr/flatpickr.js', array( 'jquery' ), WPSC_VERSION, true );
			wp_enqueue_style( 'flatpickr-css', WPSC_PLUGIN_URL . 'asset/libs/flatpickr/flatpickr.min.css', array(), WPSC_VERSION );

			if ( file_exists( WPSC_ABSPATH . 'asset/libs/flatpickr/l10n/' . WPSC_Functions::get_locale_iso() . '.js' ) ) {
				wp_enqueue_script( 'flatpickr-lang', WPSC_PLUGIN_URL . 'asset/libs/flatpickr/l10n/' . WPSC_Functions::get_locale_iso() . '.js', array( 'jquery' ), WPSC_VERSION, true );
			}

			// fullcalendar.
			wp_enqueue_script( 'fullcalendar', WPSC_PLUGIN_URL . 'asset/libs/fullcalendar/lib/main.min.js', array( 'jquery' ), WPSC_VERSION, true );
			wp_enqueue_script( 'fullcalendar-locales', WPSC_PLUGIN_URL . 'asset/libs/fullcalendar/lib/locales-all.min.js', array( 'jquery' ), WPSC_VERSION, true );
			wp_enqueue_style( 'fullcalendar', WPSC_PLUGIN_URL . 'asset/libs/fullcalendar/lib/main.min.css', array(), WPSC_VERSION );

			// DataTables.
			wp_enqueue_script( 'datatables', WPSC_PLUGIN_URL . 'asset/libs/DataTables/datatables.min.js', array( 'jquery' ), WPSC_VERSION, true );
			wp_enqueue_style( 'datatables', WPSC_PLUGIN_URL . 'asset/libs/DataTables/datatables.min.css', array(), WPSC_VERSION );
		}

		/**
		 * Get localization data for the admin scripts
		 *
		 * @return array
		 */
		private static function get_localization_data() {

			$gs            = get_option( 'wpsc-gs-general' );
			$page_settings = get_option( 'wpsc-gs-page-settings' );
			$file_settings = get_option( 'wpsc-gs-file-attachments' );

			$localizations = array(
				'ajax_url'                => admin_url( 'admin-ajax.php' ),
				'urls'                    => array(
					'support'     => $page_settings['support-page'] ? get_permalink( $page_settings['support-page'] ) : '',
					'open_ticket' => $page_settings['open-ticket-page'] ? get_permalink( $page_settings['open-ticket-page'] ) : '',
				),
				'plugin_url'              => WPSC_PLUGIN_URL,
				'version'                 => WPSC_VERSION,
				'loader_html'             => WPSC_Framework::loader_html(),
				'inline_loader'           => WPSC_Framework::inline_loader(),
				'is_frontend'             => 1,
				'is_reload'               => 1,
				'reply_form_position'     => $gs['reply-form-position'],
				'allowed_file_extensions' => array_map( 'trim', array_map( 'strtolower', explode( ',', $file_settings['allowed-file-extensions'] ) ) ),
				'nonce'                   => wp_create_nonce( 'general' ),
				'translations'            => array(
					'req_fields_error' => esc_attr__( 'Required fields can not be empty!', 'supportcandy' ),
					'datatables'       => array(
						'emptyTable'     => esc_attr__( 'No Records', 'supportcandy' ),
						'zeroRecords'    => esc_attr__( 'No Records', 'supportcandy' ),
						'info'           => sprintf(
							/* translators: e.g. Showing 1 to 20 of 300 records */
							esc_attr__( 'Showing %1$s to %2$s of %3$s records', 'supportcandy' ),
							'_START_',
							'_END_',
							'_TOTAL_'
						),
						'infoEmpty'      => '',
						'loadingRecords' => '',
						'processing'     => '',
						'infoFiltered'   => '',
						'search'         => esc_attr__( 'Search:', 'supportcandy' ),
						'paginate'       => array(
							'first'    => esc_attr__( 'First', 'supportcandy' ),
							'previous' => esc_attr__( 'Previous', 'supportcandy' ),
							'next'     => esc_attr__( 'Next', 'supportcandy' ),
							'last'     => esc_attr__( 'Last', 'supportcandy' ),
						),
					),
				),
				'temp'                    => array(),
				'home_url'                => home_url(),
			);

			return apply_filters( 'wpsc_frontend_localizations', $localizations );
		}

		/**
		 * Authentication screen
		 *
		 * @param boolean $otp_login - otp login option.
		 * @param boolean $additional_links - additional links.
		 * @return void
		 */
		public static function load_authentication_screen( $otp_login = true, $additional_links = true ) {

			$gs            = get_option( 'wpsc-gs-general' );
			$page_settings = get_option( 'wpsc-gs-page-settings' );
			$recaptcha     = get_option( 'wpsc-recaptcha-settings' );
			?>
			<div class="wpsc-auth-container">
				<div class="auth-inner-container">
					<h2><?php esc_attr_e( 'Please sign in', 'supportcandy' ); ?></h2>
					<form onsubmit="return false;" class="wpsc-login wpsc-default-login">
						<?php
						if ( $page_settings['user-login'] === 'default' ) {
							?>

							<input type="text" name="username" placeholder="<?php esc_attr_e( 'Username / Email address', 'supportcandy' ); ?>" autocomplete="off"/>
							<input type="password" name="password" placeholder="<?php esc_attr_e( 'Password', 'supportcandy' ); ?>"/>
							<div class="checkbox-container remember-me">
								<input id="wpsc-remember-me" type="checkbox" name="remember_me" value="1"/>
								<label for="wpsc-remember-me"><?php esc_attr_e( 'Remember me', 'supportcandy' ); ?></label>
							</div>
							<?php

							// recaptcha.
							if ( $recaptcha['allow-recaptcha'] === 1 && $recaptcha['recaptcha-version'] == 2 && $recaptcha['recaptcha-site-key'] && $recaptcha['recaptcha-secret-key'] ) :
								$unique_id = uniqid( 'wpsc_' )
								?>
								<script src="https://www.google.com/recaptcha/api.js?onload=recaptchaCallback&render=explicit" async defer></script> <?php // phpcs:ignore ?>
								<div id="<?php echo esc_attr( $unique_id ); ?>" style="margin-bottom: 5px;"></div>
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
							endif;
							if ( $recaptcha['allow-recaptcha'] === 1 && $recaptcha['recaptcha-version'] == 3 && $recaptcha['recaptcha-site-key'] && $recaptcha['recaptcha-secret-key'] ) :
								?>
								<script src="https://www.google.com/recaptcha/api.js?render=<?php echo esc_attr( $recaptcha['recaptcha-site-key'] ); ?>"></script> <?php // phpcs:ignore ?>
								<?php
							endif;
							?>

							<button class="wpsc-button normal primary" onclick="wpsc_submit_login_form(this)"><?php esc_attr_e( 'Sign In', 'supportcandy' ); ?></button>
							<input type="hidden" name="action" value="wpsc_default_login"/>
							<input type="hidden" name="_ajax_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wpsc_default_login' ) ); ?>"/>
							<?php

						} else {
							$host = isset( $_SERVER['HTTP_HOST'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) : '';
							$uri  = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';

							$redirect_url = ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http' ) . '://' . $host . $uri;
							$login_url    = $page_settings['user-login'] == 'custom' ? $page_settings['custom-login-url'] : wp_login_url( $redirect_url );
							?>
							<button class="wpsc-button normal primary" onclick="wpsc_custom_login( this, '<?php echo esc_url( $login_url ); ?>' )"><?php esc_attr_e( 'Sign In', 'supportcandy' ); ?></button>
							<?php

						}
						?>
					</form>

					<div class="auth-links">
						<?php

						if ( $page_settings['user-login'] === 'default' ) :
							?>
							<a class="wpsc-link wpsc-forgot-password" href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php esc_attr_e( 'Forgot your password?', 'supportcandy' ); ?></a>
							<?php
						endif;

						if ( $page_settings['user-registration'] == 'default' ) {

							$registration_url = 'javascript:wpsc_get_default_registration();';

						} elseif ( $page_settings['user-registration'] == 'wp-default' ) {

							$registration_url = wp_registration_url();

						} elseif ( $page_settings['user-registration'] == 'custom' ) {

							$registration_url = $page_settings['custom-registration-url'];
						}
						if ( $page_settings['user-registration'] !== 'disable' ) {
							?>
							<a class="wpsc-link wpsc-register" href="<?php echo esc_attr( $registration_url ); ?>"><?php esc_attr_e( 'Register now', 'supportcandy' ); ?></a>
							<?php
						}
						if ( $otp_login && $page_settings['otp-login'] && in_array( 'guest', $gs['allow-create-ticket'] ) ) :
							?>
							<a class="wpsc-link wpsc-otp-signin" href="javascript:wpsc_get_guest_sign_in();"><?php esc_attr_e( 'Sign-in using one time password', 'supportcandy' ); ?></a>
							<?php
						endif;

						if ( $additional_links && in_array( 'guest', $gs['allow-create-ticket'] ) ) :
							?>
							<a class="wpsc-link wpsc-guest-create-ticket" href="javascript:wpsc_get_guest_ticket_form();"><?php esc_attr_e( 'Create new ticket as guest', 'supportcandy' ); ?></a>
							<?php
						endif;

						if ( $additional_links && $page_settings['open-ticket-page'] ) :
							?>
							<a class="wpsc-link wpsc-otp-open-ticket" href="<?php echo esc_url( get_permalink( $page_settings['open-ticket-page'] ) ); ?>"><?php esc_attr_e( 'Open existing ticket using one time password', 'supportcandy' ); ?></a>
							<?php
						endif;
						?>
					</div>
				</div>
				<script>
					/**
					 * Submit default login form
					 */
					function wpsc_submit_login_form(el) {

						var dataform = new FormData(jQuery(el).closest('form')[0]);
						var username = dataform.get('username').trim();
						var password = dataform.get('password').trim();
						if (!username || !password) {
							alert(supportcandy.translations.req_fields_missing);
							return;
						}
						<?php
						if ( $recaptcha['allow-recaptcha'] === 1 && $recaptcha['recaptcha-version'] == 3 && $recaptcha['recaptcha-site-key'] && $recaptcha['recaptcha-secret-key'] ) {
							?>
							grecaptcha.ready(function() {
								grecaptcha.execute('<?php echo esc_attr( $recaptcha['recaptcha-site-key'] ); ?>', {action: 'submit_login'}).then(function(token) {
									dataform.append('g-recaptcha-response', token);
									wpsc_post_login_form(el, dataform);
								});
							});
							<?php
						} elseif ( $recaptcha['allow-recaptcha'] === 1 && $recaptcha['recaptcha-version'] == 2 && $recaptcha['recaptcha-site-key'] && $recaptcha['recaptcha-secret-key'] ) {
							?>
							var token = dataform.get('g-recaptcha-response');
							if (!token) {
								alert("<?php esc_attr_e( 'Captcha not set!', 'supportcandy' ); ?>");
								return;
							}
							wpsc_post_login_form(el, dataform);
							<?php
						} else {
							?>

							wpsc_post_login_form(el, dataform);
							<?php
						}
						?>
					}

					/**
					 * Submit login form
					 */
					function wpsc_post_login_form(el, dataform) {

						jQuery(el).text(supportcandy.translations.please_wait);
						jQuery.ajax({
							url: supportcandy.ajax_url,
							type: 'POST',
							data: dataform,
							processData: false,
							contentType: false
						}).done(function (response) {
							if (response.success != 1) alert(supportcandy.translations.incorrect_login);
						}).fail(function (res) {
							alert(supportcandy.translations.something_wrong);
						}).always(function () {
							window.location.reload();
						});
					}

					/**
					 * Custom login
					 */
					function wpsc_custom_login(el, url) {

						jQuery(el).text(supportcandy.translations.please_wait);
						window.location.href = url;
					}

					/**
					 * Get default registration page
					 */
					function wpsc_get_default_registration() {

						jQuery('.auth-inner-container').html(supportcandy.loader_html);
						var data = { action: 'wpsc_get_default_registration' };
						jQuery.post(supportcandy.ajax_url, data, function (response) {
							jQuery('.auth-inner-container').html(response);
						});
					}

					/**
					 * Set default registration form
					 */
					function wpsc_set_default_registration(el) {

						var dataform = new FormData(jQuery(el).closest('form')[0]);
						var firstname = dataform.get('firstname').trim();
						var lastname = dataform.get('lastname').trim();
						var username = dataform.get('username').trim();
						var email_address = dataform.get('email_address').trim();
						var password = dataform.get('password').trim();
						var confirm_password = dataform.get('confirm_password').trim();
						var isUsername = dataform.get('is_username').trim();

						if (!firstname || !lastname || !username || !email_address || !password || !confirm_password) {
							alert(supportcandy.translations.req_fields_missing);
							return;
						}

						if (!validateEmail(email_address)) {
							alert(supportcandy.translations.incorrect_email);
							return;
						}

						if (isUsername != 1) {
							alert(supportcandy.translations.unsername_unavailable);
							return;
						}

						if (password !== confirm_password) {
							alert(supportcandy.translations.incorrect_password);
							return;
						}

						<?php
						if ( $recaptcha['allow-recaptcha'] === 1 && $recaptcha['recaptcha-version'] == 3 && $recaptcha['recaptcha-site-key'] && $recaptcha['recaptcha-secret-key'] ) {
							?>
							grecaptcha.ready(function() {
								grecaptcha.execute('<?php echo esc_attr( $recaptcha['recaptcha-site-key'] ); ?>', {action: 'submit_registration'}).then(function(token) {
									dataform.append('g-recaptcha-response', token);
									wpsc_authenticate_registration(el, dataform);
								});
							});
							<?php
						} elseif ( $recaptcha['allow-recaptcha'] === 1 && $recaptcha['recaptcha-version'] == 2 && $recaptcha['recaptcha-site-key'] && $recaptcha['recaptcha-secret-key'] ) {
							?>
							var token = dataform.get('g-recaptcha-response');
							if (!token) {
								alert("<?php esc_attr_e( 'Captcha not set!', 'supportcandy' ); ?>");
								return;
							}
							wpsc_authenticate_registration(el, dataform);
							<?php
						} else {
							?>

							wpsc_authenticate_registration(el, dataform);
							<?php
						}
						?>
					}

					/**
					 * Post registration form
					 */
					function wpsc_authenticate_registration(el, dataform) {

						jQuery('.auth-inner-container').html(supportcandy.loader_html);
						jQuery.ajax({
							url: supportcandy.ajax_url,
							type: 'POST',
							data: dataform,
							processData: false,
							contentType: false
						}).done(function (res) {
							if (typeof(res) == "object") {
								alert(supportcandy.translations.something_wrong);
								wpsc_get_default_registration();
							} else {
								jQuery('.auth-inner-container').html(res);
							}
						}).fail(function (res) {
							alert(supportcandy.translations.something_wrong);
							window.location.reload();
						});
					}

					/**
					 * Register user
					 */
					function wpsc_confirm_registration(el) {

						jQuery('.auth-inner-container').html(supportcandy.loader_html);
						var dataform = new FormData(jQuery(el).closest('form')[0]);
						jQuery.ajax({
							url: supportcandy.ajax_url,
							type: 'POST',
							data: dataform,
							processData: false,
							contentType: false
						}).done(function (res) {
							if (res.isSuccess == 1) {
								window.location.reload();
							} else {
								alert(supportcandy.translations.something_wrong);
								wpsc_get_default_registration();
							}
						});
					}

					/**
					 * Get guest otp login screen
					 *
					 * @return void
					 */
					function wpsc_get_guest_sign_in() {

						jQuery('.auth-inner-container').html(supportcandy.loader_html);
						var data = { action: 'wpsc_get_guest_sign_in' };
						jQuery.post(supportcandy.ajax_url, data, function (res) {
							if (typeof(res) == "object") {
								alert(supportcandy.translations.something_wrong);
								window.location.reload();
							} else {
								jQuery('.auth-inner-container').html(res);
							}
						});
					}

					/**
					 * Send login OTP
					 *
					 * @return void
					 */
					function wpsc_authenticate_guest_login(el) {

						var dataform = new FormData(jQuery(el).closest('form')[0]);

						var email_address = dataform.get('email_address').trim();
						if ( ! email_address ) {
							alert(supportcandy.translations.req_fields_missing);
							return;
						}

						if (!validateEmail(email_address)) {
							alert(supportcandy.translations.incorrect_email);
							return;
						}

						jQuery(el).text(supportcandy.translations.please_wait);
						jQuery.ajax({
							url: supportcandy.ajax_url,
							type: 'POST',
							data: dataform,
							processData: false,
							contentType: false
						}).done(function (res) {
							if (typeof(res) == "object") {
								alert(supportcandy.translations.something_wrong);
								wpsc_get_guest_sign_in();
							} else {
								jQuery('.auth-inner-container').html(res);
							}
						}).fail(function (res) {
							alert(supportcandy.translations.something_wrong);
							window.location.reload();
						});
					}

					/**
					 * Confirm guest login auth
					 *
					 * @return void
					 */
					function wpsc_confirm_guest_login(el) {

						jQuery('.auth-inner-container').html(supportcandy.loader_html);
						var dataform = new FormData(jQuery(el).closest('form')[0]);
						jQuery.ajax({
							url: supportcandy.ajax_url,
							type: 'POST',
							data: dataform,
							processData: false,
							contentType: false
						}).done(function (res) {
							if (res.isSuccess == 1) {
								window.location.reload();
							} else {
								alert(supportcandy.translations.something_wrong);
								wpsc_get_guest_sign_in();
							}
						});
					}
				</script>
			</div>
			<?php
		}

		/**
		 * Load HTML snippets that can be used by js to load dynamically
		 *
		 * @return void
		 */
		public static function load_html_snippets() {
			?>

			<div class="wpsc-page-snippets" style="display: none;">
				<div class="wpsc-editor-attachment upload-waiting">
					<div class="attachment-label"></div>
					<div class="attachment-remove" onclick="wpsc_remove_attachment(this)">
					<?php WPSC_Icons::get( 'times' ); ?>
					</div>
					<div class="attachment-waiting"></div>
				</div>
			</div>
			<?php
		}
	}
endif;

WPSC_Frontend::init();
