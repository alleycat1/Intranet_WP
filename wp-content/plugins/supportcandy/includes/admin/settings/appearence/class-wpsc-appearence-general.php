<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_Appearence_General' ) ) :

	final class WPSC_Appearence_General {

		/**
		 * Initialize this class
		 *
		 * @return void
		 */
		public static function init() {

			// user interface.
			add_action( 'wp_ajax_wpsc_get_ap_general', array( __CLASS__, 'load_settings_ui' ) );
			add_action( 'wp_ajax_wpsc_set_ap_general', array( __CLASS__, 'save_settings' ) );
			add_action( 'wp_ajax_wpsc_reset_ap_general', array( __CLASS__, 'reset_settings' ) );
		}

		/**
		 * Reset default settings
		 *
		 * @return void
		 */
		public static function reset() {

			update_option(
				'wpsc-ap-general',
				array(
					'primary-color'         => '#313042',
					'menu-link-color'       => '#fff',
					'main-background-color' => '#fff',
					'main-text-color'       => '#2c3e50',
					'link-color'            => '#2271b1',
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

			$settings = get_option( 'wpsc-ap-general' );?>

			<form action="#" onsubmit="return false;" class="wpsc-frm-ap-general">

				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Primary color', 'supportcandy' ); ?></label>
					</div>
					<input class="wpsc-color-picker" type="text" name="primary-color" value="<?php echo esc_attr( $settings['primary-color'] ); ?>">
				</div>

				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Menu color', 'supportcandy' ); ?></label>
					</div>
					<input class="wpsc-color-picker" type="text" name="menu-link-color" value="<?php echo esc_attr( $settings['menu-link-color'] ); ?>">
				</div>

				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Main container', 'supportcandy' ); ?></label>
					</div>
					<table class="wpsc-ap-table">
						<tr>
							<td><?php esc_attr_e( 'Background color', 'supportcandy' ); ?></td>
							<td><?php esc_attr_e( 'Text color', 'supportcandy' ); ?></td>
						</tr>
						<tr>
							<td><input class="wpsc-color-picker" type="text" name="main-background-color" value="<?php echo esc_attr( $settings['main-background-color'] ); ?>"></td>
							<td><input class="wpsc-color-picker" type="text" name="main-text-color" value="<?php echo esc_attr( $settings['main-text-color'] ); ?>"></td>
						</tr>
					</table>
				</div>

				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Link color', 'supportcandy' ); ?></label>
					</div>
					<input class="wpsc-color-picker" type="text" name="link-color" value="<?php echo esc_attr( $settings['link-color'] ); ?>">
				</div>

				<input type="hidden" name="action" value="wpsc_set_ap_general">
				<input type="hidden" name="_ajax_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wpsc_set_ap_general' ) ); ?>">

				<script>jQuery('.wpsc-color-picker').wpColorPicker();</script>
				<style>
					.wpsc-ap-table td {padding: 5px;}
					.wpsc-ap-table table, .wpsc-ap-table td {border: 1px solid #c3c3c3;}
				</style>

			</form>
			<div class="setting-footer-actions">
				<button 
					class="wpsc-button normal primary margin-right"
					onclick="wpsc_set_ap_general(this);">
					<?php esc_attr_e( 'Submit', 'supportcandy' ); ?></button>
				<button 
					class="wpsc-button normal secondary"
					onclick="wpsc_reset_ap_general(this, '<?php echo esc_attr( wp_create_nonce( 'wpsc_reset_ap_general' ) ); ?>');">
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

			if ( check_ajax_referer( 'wpsc_set_ap_general', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$primary_color = isset( $_POST['primary-color'] ) ? sanitize_text_field( wp_unslash( $_POST['primary-color'] ) ) : '';
			if ( ! $primary_color ) {
				wp_send_json_error( 'Bad request', 400 );
			}

			$menu_link_color = isset( $_POST['menu-link-color'] ) ? sanitize_text_field( wp_unslash( $_POST['menu-link-color'] ) ) : '';
			if ( ! $primary_color ) {
				wp_send_json_error( 'Bad request', 400 );
			}

			$main_background_color = isset( $_POST['main-background-color'] ) ? sanitize_text_field( wp_unslash( $_POST['main-background-color'] ) ) : '';
			if ( ! $main_background_color ) {
				wp_send_json_error( 'Bad request', 400 );
			}

			$main_text_color = isset( $_POST['main-text-color'] ) ? sanitize_text_field( wp_unslash( $_POST['main-text-color'] ) ) : '';
			if ( ! $main_text_color ) {
				wp_send_json_error( 'Bad request', 400 );
			}

			$link_color = isset( $_POST['link-color'] ) ? sanitize_text_field( wp_unslash( $_POST['link-color'] ) ) : '';
			if ( ! $link_color ) {
				wp_send_json_error( 'Bad request', 400 );
			}

			update_option(
				'wpsc-ap-general',
				array(
					'primary-color'         => $primary_color,
					'menu-link-color'       => $menu_link_color,
					'main-background-color' => $main_background_color,
					'main-text-color'       => $main_text_color,
					'link-color'            => $link_color,
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

			if ( check_ajax_referer( 'wpsc_reset_ap_general', '_ajax_nonce', false ) != 1 ) {
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

WPSC_Appearence_General::init();
