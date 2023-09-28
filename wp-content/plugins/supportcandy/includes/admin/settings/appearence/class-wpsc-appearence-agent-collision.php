<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_Appearence_Agent_Collision' ) ) :

	final class WPSC_Appearence_Agent_Collision {

		/**
		 * Initialize this class
		 *
		 * @return void
		 */
		public static function init() {

			// user interface.
			add_action( 'wp_ajax_wpsc_get_ap_agent_collision', array( __CLASS__, 'load_settings_ui' ) );
			add_action( 'wp_ajax_wpsc_set_ap_agent_collision', array( __CLASS__, 'save_settings' ) );
			add_action( 'wp_ajax_wpsc_reset_ap_agent_collision', array( __CLASS__, 'reset_settings' ) );
		}

		/**
		 * Reset settings
		 *
		 * @return void
		 */
		public static function reset() {

			update_option(
				'wpsc-ap-agent-collision',
				array(
					'header-bg-color'   => '#e6e6e6',
					'header-text-color' => '#2c3e50',
				)
			);
		}

		/**
		 * Get general settings
		 *
		 * @return void
		 */
		public static function load_settings_ui() {

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$settings = get_option( 'wpsc-ap-agent-collision' );?>

			<form action="#" onsubmit="return false;" class="wpsc-frm-ap-agent-collision">

				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Agent collision', 'supportcandy' ); ?></label>
					</div>
					<table class="wpsc-ap-table">
						<tr>
							<td><?php esc_attr_e( 'Text color', 'supportcandy' ); ?></td>
							<td><?php esc_attr_e( 'Background color', 'supportcandy' ); ?></td>
						</tr>
						<tr>
							<td><input class="wpsc-color-picker" type="text" name="header-text-color" value="<?php echo esc_attr( $settings['header-text-color'] ); ?>"></td>
							<td><input class="wpsc-color-picker" type="text" name="header-bg-color" value="<?php echo esc_attr( $settings['header-bg-color'] ); ?>"></td>
						</tr>
					</table>
				</div>
				<input type="hidden" name="action" value="wpsc_set_ap_agent_collision">
				<input type="hidden" name="_ajax_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wpsc_set_ap_agent_collision' ) ); ?>">

				<script>jQuery('.wpsc-color-picker').wpColorPicker();</script>
				<style>
					.wpsc-ap-table td {padding: 5px;}
					.wpsc-ap-table table, .wpsc-ap-table td {border: 1px solid #c3c3c3;}
				</style>

			</form>
			<div class="setting-footer-actions">
				<button 
					class="wpsc-button normal primary margin-right"
					onclick="wpsc_set_ap_agent_collision(this);">
					<?php esc_attr_e( 'Submit', 'supportcandy' ); ?></button>
				<button 
					class="wpsc-button normal secondary"
					onclick="wpsc_reset_ap_agent_collision(this, '<?php echo esc_attr( wp_create_nonce( 'wpsc_reset_ap_agent_collision' ) ); ?>');">
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

			if ( check_ajax_referer( 'wpsc_set_ap_agent_collision', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			update_option(
				'wpsc-ap-agent-collision',
				array(
					'header-bg-color'   => isset( $_POST['header-bg-color'] ) ? sanitize_text_field( wp_unslash( $_POST['header-bg-color'] ) ) : '#e6e6e6',
					'header-text-color' => isset( $_POST['header-text-color'] ) ? sanitize_text_field( wp_unslash( $_POST['header-text-color'] ) ) : '#2c3e50',
				)
			);

			wp_die();
		}

		/**
		 * Reset settings to default
		 *
		 * @return void
		 */
		public static function reset_settings() {

			if ( check_ajax_referer( 'wpsc_reset_ap_agent_collision', '_ajax_nonce', false ) != 1 ) {
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

WPSC_Appearence_Agent_Collision::init();
