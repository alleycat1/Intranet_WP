<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_EN_Settings_GS' ) ) :

	final class WPSC_EN_Settings_GS {

		/**
		 * Initialize this class
		 */
		public static function init() {

			add_action( 'wp_ajax_wpsc_get_en_general_setting', array( __CLASS__, 'get_engs' ) );
			add_action( 'wp_ajax_wpsc_set_en_general', array( __CLASS__, 'save_settings' ) );
			add_action( 'wp_ajax_wpsc_reset_en_general', array( __CLASS__, 'reset_settings' ) );
		}

		/**
		 * Install this setting. To be called from installation.
		 *
		 * @return void
		 */
		public static function reset() {

			$general = apply_filters(
				'wpsc_en_general',
				array(
					'from-name'                   => '',
					'from-email'                  => '',
					'reply-to'                    => '',
					'cron-email-count'            => 5,
					'blocked-emails'              => array(),
					'attachments-in-notification' => 'actual-files',
				)
			);
			update_option( 'wpsc-en-general', $general );
		}

		/**
		 * Get ticket form fields
		 *
		 * @return void
		 */
		public static function get_engs() {

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}
			$settings = get_option( 'wpsc-en-general', array() );?>

			<div class="wpsc-setting-header">
				<h2><?php esc_attr_e( 'General Settings', 'supportcandy' ); ?></h2>
			</div>
			<div class="wpsc-setting-section-body">
				<div class="wpsc-dock-container">
					<?php
					printf(
						/* translators: Click here to see the documentation */
						esc_attr__( '%s to see the documentation!', 'supportcandy' ),
						'<a href="https://supportcandy.net/docs/email-notification-settings/" target="_blank">' . esc_attr__( 'Click here', 'supportcandy' ) . '</a>'
					);
					?>
				</div>
				<form action="#" onsubmit="return false;" class="wpsc-frm-en-general">
					<div class="wpsc-input-group">
						<div class="label-container">
							<label for=""><?php esc_attr_e( 'From name', 'supportcandy' ); ?></label>
						</div>
						<input type="text" name="wpsc-from-name" value="<?php echo esc_attr( $settings['from-name'] ); ?>" autocomplete="off"/>
					</div>
					<div class="wpsc-input-group">
						<div class="label-container">
							<label for=""><?php esc_attr_e( 'From email', 'supportcandy' ); ?></label>
						</div>
						<input type="text" name="wpsc-from-email" value="<?php echo esc_attr( $settings['from-email'] ); ?>" autocomplete="off"/>
					</div>
					<div class="wpsc-input-group">
						<div class="label-container">
							<label for=""><?php esc_attr_e( 'Reply to', 'supportcandy' ); ?></label>
						</div>
						<input type="text" name="wpsc-reply-to" value="<?php echo esc_attr( $settings['reply-to'] ); ?>" autocomplete="off"/>
					</div>
					<div class="wpsc-input-group">
						<div class="label-container">
							<label for=""><?php esc_attr_e( 'Number of emails per cron job (background emails)', 'supportcandy' ); ?></label>
						</div>
						<input type="text" name="wpsc-cron-email-count" value="<?php echo esc_attr( $settings['cron-email-count'] ); ?>" autocomplete="off"/>
					</div>
					<div class="wpsc-input-group">
						<div class="label-container">
							<label for=""><?php esc_attr_e( 'Blocked emails (one per line)', 'supportcandy' ); ?></label>
						</div>
						<?php $blocked_emails = $settings['blocked-emails'] ? implode( PHP_EOL, $settings['blocked-emails'] ) : ''; ?>
						<textarea name="wpsc-blocked-emails" rows="5"><?php echo esc_attr( $blocked_emails ); ?></textarea>
					</div>
					<div class="wpsc-input-group">
						<div class="label-container">
							<label for=""><?php esc_attr_e( 'Attachments in notifications', 'supportcandy' ); ?></label>
						</div>
						<select name="attachments-in-notification">
							<option <?php selected( $settings['attachments-in-notification'], 'actual-files' ); ?> value="actual-files"><?php esc_attr_e( 'Actual files', 'supportcandy' ); ?></option>
							<option <?php selected( $settings['attachments-in-notification'], 'file-links' ); ?> value="file-links"><?php esc_attr_e( 'File links', 'supportcandy' ); ?></option>
							<option <?php selected( $settings['attachments-in-notification'], 'disable' ); ?> value="disable"><?php esc_attr_e( 'Disable', 'supportcandy' ); ?></option>
						</select>
					</div>
					<?php do_action( 'wpsc_en_general' ); ?>
					<input type="hidden" name="action" value="wpsc_set_en_general">
					<input type="hidden" name="_ajax_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wpsc_set_en_general' ) ); ?>">
				</form>
				<div class="setting-footer-actions">
					<button 
						class="wpsc-button normal primary margin-right"
						onclick="wpsc_set_en_general(this);">
						<?php esc_attr_e( 'Submit', 'supportcandy' ); ?></button>
					<button 
						class="wpsc-button normal secondary"
						onclick="wpsc_reset_en_general(this);">
						<?php esc_attr_e( 'Reset default', 'supportcandy' ); ?></button>
				</div>
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

			if ( check_ajax_referer( 'wpsc_set_en_general', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$blocked_emails = isset( $_POST['wpsc-blocked-emails'] ) ? sanitize_textarea_field( wp_unslash( $_POST['wpsc-blocked-emails'] ) ) : '';
			$blocked_emails = array_filter( array_map( 'sanitize_text_field', explode( PHP_EOL, $blocked_emails ) ) );

			$general = apply_filters(
				'wpsc_set_en_general',
				array(
					'from-name'                   => isset( $_POST['wpsc-from-name'] ) ? sanitize_text_field( wp_unslash( $_POST['wpsc-from-name'] ) ) : '',
					'from-email'                  => isset( $_POST['wpsc-from-email'] ) ? sanitize_text_field( wp_unslash( $_POST['wpsc-from-email'] ) ) : '',
					'reply-to'                    => isset( $_POST['wpsc-reply-to'] ) ? sanitize_text_field( wp_unslash( $_POST['wpsc-reply-to'] ) ) : 1,
					'cron-email-count'            => isset( $_POST['wpsc-cron-email-count'] ) && sanitize_text_field( wp_unslash( $_POST['wpsc-cron-email-count'] ) ) ? intval( $_POST['wpsc-cron-email-count'] ) : 5,
					'blocked-emails'              => $blocked_emails,
					'attachments-in-notification' => isset( $_POST['attachments-in-notification'] ) ? sanitize_text_field( wp_unslash( $_POST['attachments-in-notification'] ) ) : 'actual-files',
				)
			);
			update_option( 'wpsc-en-general', $general );
			wp_die();
		}

		/**
		 * Reset settings to default
		 *
		 * @return void
		 */
		public static function reset_settings() {

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}
			self::reset();
			wp_die();
		}
	}
endif;

WPSC_EN_Settings_GS::init();
