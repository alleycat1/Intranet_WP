<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_Wh_Settings' ) ) :

	final class WPSC_Wh_Settings {

		/**
		 * Initialize this class
		 *
		 * @return void
		 */
		public static function init() {

			// User interface.
			add_action( 'wp_ajax_wpsc_get_wh_settings', array( __CLASS__, 'get_wh_settings' ) );
			add_action( 'wp_ajax_wpsc_set_wh_settings', array( __CLASS__, 'set_wh_settings' ) );
			add_action( 'wp_ajax_wpsc_reset_wh_settings', array( __CLASS__, 'reset_wh_settings' ) );
		}

		/**
		 * Reset settings
		 *
		 * @return void
		 */
		public static function reset() {

			$advanced = apply_filters(
				'wpsc_wh_settings',
				array(
					'allow-agent-modify-wh'     => 0,
					'allow-agent-modify-leaves' => 0,
				)
			);
			update_option( 'wpsc-wh-settings', $advanced );
		}

		/**
		 * Settings user interface
		 *
		 * @return void
		 */
		public static function get_wh_settings() {

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}
			$settings = get_option( 'wpsc-wh-settings', array() );?>

			<form action="#" onsubmit="return false;" class="wpsc-frm-wh-settings">
				<div class="wpsc-dock-container">
					<?php
					printf(
						/* translators: Click here to see the documentation */
						esc_attr__( '%s to see the documentation!', 'supportcandy' ),
						'<a href="https://supportcandy.net/docs/working-hours/" target="_blank">' . esc_attr__( 'Click here', 'supportcandy' ) . '</a>'
					);
					?>
				</div>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Allow agent to modify working hours', 'supportcandy' ); ?></label>
					</div>
					<select name="allow-agent-modify-wh">
						<option <?php selected( $settings['allow-agent-modify-wh'], '1' ); ?> value="1"><?php esc_attr_e( 'Enable', 'supportcandy' ); ?></option>
						<option <?php selected( $settings['allow-agent-modify-wh'], '0' ); ?> value="0"><?php esc_attr_e( 'Disable', 'supportcandy' ); ?></option>
					</select>
				</div>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Allow agent to modify his leaves', 'supportcandy' ); ?></label>
					</div>
					<select name="allow-agent-modify-leaves">
						<option <?php selected( $settings['allow-agent-modify-leaves'], '1' ); ?> value="1"><?php esc_attr_e( 'Enable', 'supportcandy' ); ?></option>
						<option <?php selected( $settings['allow-agent-modify-leaves'], '0' ); ?> value="0"><?php esc_attr_e( 'Disable', 'supportcandy' ); ?></option>
					</select>
				</div>
				<?php do_action( 'wpsc_wh_settings' ); ?>
				<input type="hidden" name="action" value="wpsc_set_wh_settings">
				<input type="hidden" name="_ajax_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wpsc_set_wh_settings' ) ); ?>">
			</form>
			<div class="setting-footer-actions">
				<button 
					class="wpsc-button normal primary margin-right"
					onclick="wpsc_set_wh_settings(this);">
					<?php esc_attr_e( 'Submit', 'supportcandy' ); ?></button>
				<button 
					class="wpsc-button normal secondary"
					onclick="wpsc_reset_wh_settings(this, '<?php echo esc_attr( wp_create_nonce( 'wpsc_reset_wh_settings' ) ); ?>');">
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
		public static function set_wh_settings() {

			if ( check_ajax_referer( 'wpsc_set_wh_settings', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$advanced = apply_filters(
				'wpsc_set_ms_advanced',
				array(
					'allow-agent-modify-wh'     => isset( $_POST['allow-agent-modify-wh'] ) ? intval( $_POST['allow-agent-modify-wh'] ) : 0,
					'allow-agent-modify-leaves' => isset( $_POST['allow-agent-modify-leaves'] ) ? intval( $_POST['allow-agent-modify-leaves'] ) : 0,
				)
			);
			update_option( 'wpsc-wh-settings', $advanced );
			wp_die();
		}

		/**
		 * Reset settings to default
		 *
		 * @return void
		 */
		public static function reset_wh_settings() {

			if ( check_ajax_referer( 'wpsc_reset_wh_settings', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}
			self::reset();
			wp_die();
		}
	}
endif;

WPSC_Wh_Settings::init();
