<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_MS_Recaptcha' ) ) :

	final class WPSC_MS_Recaptcha {

		/**
		 * Initialize this class
		 *
		 * @return void
		 */
		public static function init() {

			// User interface.
			add_action( 'wp_ajax_wpsc_get_ms_recaptcha', array( __CLASS__, 'load_settings_ui' ) );
			add_action( 'wp_ajax_wpsc_set_ms_recaptcha', array( __CLASS__, 'save_settings' ) );
			add_action( 'wp_ajax_wpsc_reset_ms_recaptcha', array( __CLASS__, 'reset_settings' ) );

			// Print in create ticket form and my profile.
			add_action( 'wpsc_print_tff', array( __CLASS__, 'print_tff' ), 9 );
			add_action( 'wpsc_my_profile', array( __CLASS__, 'print_tff' ), 9 );

			// TFF! validation.
			add_action( 'wpsc_js_validate_ticket_form', array( __CLASS__, 'js_validate_ticket_form' ) );
		}

		/**
		 * Reset settings
		 *
		 * @return void
		 */
		public static function reset() {

			$recaptcha = apply_filters(
				'wpsc_recaptcha_settings',
				array(
					'allow-recaptcha'      => 0,
					'recaptcha-version'    => 3,
					'recaptcha-site-key'   => '',
					'recaptcha-secret-key' => '',
				)
			);
			update_option( 'wpsc-recaptcha-settings', $recaptcha );
		}

		/**
		 * Settings user interface
		 *
		 * @return void
		 */
		public static function load_settings_ui() {

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}
			$settings = get_option( 'wpsc-recaptcha-settings', array() );?>

			<form action="#" onsubmit="return false;" class="wpsc-frm-ms-recaptcha">
				<div class="wpsc-dock-container">
					<?php
					printf(
						/* translators: Click here to see the documentation */
						esc_attr__( '%s to see the documentation!', 'supportcandy' ),
						'<a href="https://supportcandy.net/docs/google-recaptcha/" target="_blank">' . esc_attr__( 'Click here', 'supportcandy' ) . '</a>'
					);
					?>
				</div>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Enable', 'supportcandy' ); ?></label>
					</div>
					<select id="wpsc-allow-recaptcha" name="allow-recaptcha">
						<option <?php selected( $settings['allow-recaptcha'], 1 ); ?> value="1"><?php esc_attr_e( 'Yes', 'supportcandy' ); ?></option>
						<option <?php selected( $settings['allow-recaptcha'], 0 ); ?> value="0"><?php esc_attr_e( 'No', 'supportcandy' ); ?></option>
					</select>
				</div>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'reCaptcha version', 'supportcandy' ); ?></label>
					</div>
					<select id="wpsc-recaptcha-version" name="recaptcha-version">
						<option <?php selected( $settings['recaptcha-version'], '2' ); ?> value="2">2</option>
						<option <?php selected( $settings['recaptcha-version'], '3' ); ?> value="3">3</option>
					</select>
				</div> 
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Site key', 'supportcandy' ); ?></label>
					</div>
					<input type="text" id="wpsc-recaptcha-site-key" name="recaptcha-site-key" value="<?php echo esc_attr( $settings['recaptcha-site-key'] ); ?>" autocomplete="off">
				</div>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Secret key', 'supportcandy' ); ?></label>
					</div>
					<input type="text" id="wpsc-recaptcha-secret-key" name="recaptcha-secret-key" value="<?php echo esc_attr( $settings['recaptcha-secret-key'] ); ?>" autocomplete="off">
				</div>   
				<?php do_action( 'wpsc_ms_recaptcha' ); ?>
				<input type="hidden" name="action" value="wpsc_set_ms_recaptcha">
				<input type="hidden" name="_ajax_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wpsc_set_ms_recaptcha' ) ); ?>">
			</form>
			<div class="setting-footer-actions">
				<button 
					class="wpsc-button normal primary margin-right"
					onclick="wpsc_set_ms_recaptcha(this);">
					<?php esc_attr_e( 'Submit', 'supportcandy' ); ?></button>
				<button 
					class="wpsc-button normal secondary"
					onclick="wpsc_reset_ms_recaptcha(this, '<?php echo esc_attr( wp_create_nonce( 'wpsc_reset_ms_recaptcha' ) ); ?>');">
					<?php esc_attr_e( 'Reset default', 'supportcandy' ); ?></button>
			</div>
			<?php
			wp_die();
		}

		/**
		 * Save settings
		 *
		 * @return void
		 */
		public static function save_settings() {

			if ( check_ajax_referer( 'wpsc_set_ms_recaptcha', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$recaptcha = apply_filters(
				'wpsc_set_recaptcha',
				array(
					'allow-recaptcha'      => isset( $_POST['allow-recaptcha'] ) ? intval( $_POST['allow-recaptcha'] ) : '0',
					'recaptcha-version'    => isset( $_POST['recaptcha-version'] ) ? intval( $_POST['recaptcha-version'] ) : '3',
					'recaptcha-site-key'   => isset( $_POST['recaptcha-site-key'] ) ? sanitize_text_field( wp_unslash( $_POST['recaptcha-site-key'] ) ) : '',
					'recaptcha-secret-key' => isset( $_POST['recaptcha-secret-key'] ) ? sanitize_text_field( wp_unslash( $_POST['recaptcha-secret-key'] ) ) : '',
				)
			);
			update_option( 'wpsc-recaptcha-settings', $recaptcha );
			wp_die();
		}

		/**
		 * Reset settings to default
		 *
		 * @return void
		 */
		public static function reset_settings() {

			if ( check_ajax_referer( 'wpsc_reset_ms_recaptcha', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}
			self::reset();
			wp_die();
		}

		/**
		 * Print ticket form field
		 *
		 * @return void
		 */
		public static function print_tff() {

			$recaptcha = get_option( 'wpsc-recaptcha-settings' );
			if ( $recaptcha['allow-recaptcha'] === 1 && $recaptcha['recaptcha-version'] == 2 && $recaptcha['recaptcha-site-key'] && $recaptcha['recaptcha-secret-key'] ) {
				?>
				<script src="https://www.google.com/recaptcha/api.js" async defer></script> <?php // phpcs:ignore ?>
				<div class="wpsc-tff recaptcha wpsc-xs-12 wpsc-sm-12 wpsc-md-12 wpsc-lg-12 required wpsc-visible" data-cft="recaptcha">
					<div class="g-recaptcha" data-sitekey="<?php echo esc_attr( $recaptcha['recaptcha-site-key'] ); ?>"></div>
				</div>
				<?php
			}
			if ( $recaptcha['allow-recaptcha'] === 1 && $recaptcha['recaptcha-version'] == 3 && $recaptcha['recaptcha-site-key'] && $recaptcha['recaptcha-secret-key'] ) {
				?>
				<script src="https://www.google.com/recaptcha/api.js?render=<?php echo esc_attr( $recaptcha['recaptcha-site-key'] ); ?>"></script> <?php // phpcs:ignore ?>
				<?php
			}
		}

		/**
		 * Validate this type field in create ticket
		 *
		 * @return void
		 */
		public static function js_validate_ticket_form() {

			$recaptcha = get_option( 'wpsc-recaptcha-settings' );
			if ( $recaptcha['allow-recaptcha'] === 1 && $recaptcha['recaptcha-version'] == 2 && $recaptcha['recaptcha-site-key'] && $recaptcha['recaptcha-secret-key'] ) :
				?>
				case 'recaptcha':
					var recaptcha = jQuery("#g-recaptcha-response").val();
					if (recaptcha === "") {
						isValid = false;
						alert("<?php esc_attr_e( 'Captcha not set!', 'supportcandy' ); ?>");
					}
					break;
				<?php
				echo PHP_EOL;
			endif;
		}

		/**
		 * Check whether captcha is valid or not.
		 * No need of nonce varification as we already done this where it is called from.
		 *
		 * @param string $action - action string.
		 * @return void
		 */
		public static function validate( $action = '' ) {

			$server_http_host = isset( $_SERVER['HTTP_HOST'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) : '';

			$recaptcha = get_option( 'wpsc-recaptcha-settings' );
			if ( $recaptcha['allow-recaptcha'] === 1 && $recaptcha['recaptcha-version'] == 2 && $recaptcha['recaptcha-site-key'] && $recaptcha['recaptcha-secret-key'] ) {

				$recaptcha_token = isset( $_POST['g-recaptcha-response'] ) ? sanitize_text_field( wp_unslash( $_POST['g-recaptcha-response'] ) ) : ''; // phpcs:ignore
				if ( ! $recaptcha_token ) {
					wp_send_json_error( new WP_Error( '001', 'Recaptcha token not set' ), 400 );
				}

				$url      = 'https://www.google.com/recaptcha/api/siteverify';
				$response = wp_remote_post(
					$url,
					array(
						'method' => 'POST',
						'body'   => array(
							'secret'   => $recaptcha['recaptcha-secret-key'],
							'response' => $recaptcha_token,
						),
					)
				);

				if (
					is_wp_error( $response ) ||
					$response['response']['code'] != 200
				) {
					wp_send_json_error( new WP_Error( '002', 'Something went wrong with recaptcha!' ), 400 );
				}

				$response = json_decode( $response['body'] );
				if ( ! (
					$response->success &&
					$response->hostname == $server_http_host
				) ) {
					wp_send_json_error( new WP_Error( '003', 'Invalid recaptcha!' ), 400 );
				}
			} elseif ( $recaptcha['allow-recaptcha'] === 1 && $recaptcha['recaptcha-version'] == 3 && $recaptcha['recaptcha-site-key'] && $recaptcha['recaptcha-secret-key'] ) {

				$recaptcha_token = isset( $_POST['g-recaptcha-response'] ) ? sanitize_text_field( wp_unslash( $_POST['g-recaptcha-response'] ) ) : ''; // phpcs:ignore
				if ( ! $recaptcha_token ) {
					wp_send_json_error( new WP_Error( '001', 'Recaptcha token not set' ), 400 );
				}

				$url      = 'https://www.google.com/recaptcha/api/siteverify';
				$response = wp_remote_post(
					$url,
					array(
						'method' => 'POST',
						'body'   => array(
							'secret'   => $recaptcha['recaptcha-secret-key'],
							'response' => $recaptcha_token,
						),
					)
				);

				if (
					is_wp_error( $response ) ||
					$response['response']['code'] != 200
				) {
					wp_send_json_error( new WP_Error( '002', 'Something went wrong with recaptcha!' ), 400 );
				}

				$response = json_decode( $response['body'] );
				if ( ! (
					$response->success &&
					$response->action == $action &&
					$response->hostname == $server_http_host
				) ) {
					wp_send_json_error( new WP_Error( '003', 'Invalid recaptcha!' ), 400 );
				}
			}
		}
	}
endif;

WPSC_MS_Recaptcha::init();
