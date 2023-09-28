<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_Appearence_Modal_Popup' ) ) :

	final class WPSC_Appearence_Modal_Popup {

		/**
		 * Initialize this class
		 *
		 * @return void
		 */
		public static function init() {

			// user interface.
			add_action( 'wp_ajax_wpsc_get_ap_modal_popup', array( __CLASS__, 'load_settings_ui' ) );
			add_action( 'wp_ajax_wpsc_set_ap_modal_popup', array( __CLASS__, 'save_settings' ) );
			add_action( 'wp_ajax_wpsc_reset_ap_modal_popup', array( __CLASS__, 'reset_settings' ) );
		}

		/**
		 * Reset settings
		 *
		 * @return void
		 */
		public static function reset() {

			update_option(
				'wpsc-ap-modal',
				array(
					'header-bg-color'   => '#fff8e5',
					'header-text-color' => '#ff8f2b',
					'body-bg-color'     => '#fff',
					'body-label-color'  => '#777',
					'body-text-color'   => '#2c3e50',
					'footer-bg-color'   => '#fff',
					'z-index'           => 900000000,
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

			$settings = get_option( 'wpsc-ap-modal' );?>

			<form action="#" onsubmit="return false;" class="wpsc-frm-ap-modal">

				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Header', 'supportcandy' ); ?></label>
					</div>
					<table class="wpsc-ap-table">
						<tr>
							<td><?php esc_attr_e( 'Background color', 'supportcandy' ); ?></td>
							<td><?php esc_attr_e( 'Text color', 'supportcandy' ); ?></td>
						</tr>
						<tr>
							<td><input class="wpsc-color-picker" type="text" name="header-bg-color" value="<?php echo esc_attr( $settings['header-bg-color'] ); ?>"></td>
							<td><input class="wpsc-color-picker" type="text" name="header-text-color" value="<?php echo esc_attr( $settings['header-text-color'] ); ?>"></td>
						</tr>
					</table>
				</div>

				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Body', 'supportcandy' ); ?></label>
					</div>
					<table class="wpsc-ap-table">
						<tr>
							<td><?php esc_attr_e( 'Background color', 'supportcandy' ); ?></td>
							<td><?php esc_attr_e( 'Label color', 'supportcandy' ); ?></td>
							<td><?php esc_attr_e( 'Text color', 'supportcandy' ); ?></td>
						</tr>
						<tr>
							<td><input class="wpsc-color-picker" type="text" name="body-bg-color" value="<?php echo esc_attr( $settings['body-bg-color'] ); ?>"></td>
							<td><input class="wpsc-color-picker" type="text" name="body-label-color" value="<?php echo esc_attr( $settings['body-label-color'] ); ?>"></td>
							<td><input class="wpsc-color-picker" type="text" name="body-text-color" value="<?php echo esc_attr( $settings['body-text-color'] ); ?>"></td>
						</tr>
					</table>
				</div>

				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Footer background color', 'supportcandy' ); ?></label>
					</div>
					<input class="wpsc-color-picker" type="text" name="footer-bg-color" value="<?php echo esc_attr( $settings['footer-bg-color'] ); ?>">
				</div>

				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'z-index', 'supportcandy' ); ?></label>
					</div>
					<input type="text" name="z-index" value="<?php echo esc_attr( $settings['z-index'] ); ?>">
				</div>

				<input type="hidden" name="action" value="wpsc_set_ap_modal_popup">
				<input type="hidden" name="_ajax_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wpsc_set_ap_modal_popup' ) ); ?>">

				<script>jQuery('.wpsc-color-picker').wpColorPicker();</script>
				<style>
					.wpsc-ap-table td {padding: 5px;}
					.wpsc-ap-table table, .wpsc-ap-table td {border: 1px solid #c3c3c3;}
				</style>

			</form>
			<div class="setting-footer-actions">
				<button 
					class="wpsc-button normal primary margin-right"
					onclick="wpsc_set_ap_modal_popup(this);">
					<?php esc_attr_e( 'Submit', 'supportcandy' ); ?></button>
				<button 
					class="wpsc-button normal secondary"
					onclick="wpsc_reset_ap_modal_popup(this, '<?php echo esc_attr( wp_create_nonce( 'wpsc_reset_ap_modal_popup' ) ); ?>');">
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

			if ( check_ajax_referer( 'wpsc_set_ap_modal_popup', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			update_option(
				'wpsc-ap-modal',
				array(
					'header-bg-color'   => isset( $_POST['header-bg-color'] ) ? sanitize_text_field( wp_unslash( $_POST['header-bg-color'] ) ) : '#fff8e5',
					'header-text-color' => isset( $_POST['header-text-color'] ) ? sanitize_text_field( wp_unslash( $_POST['header-text-color'] ) ) : '#ff8f2b',
					'body-bg-color'     => isset( $_POST['body-bg-color'] ) ? sanitize_text_field( wp_unslash( $_POST['body-bg-color'] ) ) : '#fff',
					'body-label-color'  => isset( $_POST['body-label-color'] ) ? sanitize_text_field( wp_unslash( $_POST['body-label-color'] ) ) : '#777',
					'body-text-color'   => isset( $_POST['body-text-color'] ) ? sanitize_text_field( wp_unslash( $_POST['body-text-color'] ) ) : '#2c3e50',
					'footer-bg-color'   => isset( $_POST['footer-bg-color'] ) ? sanitize_text_field( wp_unslash( $_POST['footer-bg-color'] ) ) : '#fff',
					'z-index'           => isset( $_POST['z-index'] ) ? intval( $_POST['z-index'] ) : 900000000,
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

			if ( check_ajax_referer( 'wpsc_reset_ap_modal_popup', '_ajax_nonce', false ) != 1 ) {
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

WPSC_Appearence_Modal_Popup::init();
