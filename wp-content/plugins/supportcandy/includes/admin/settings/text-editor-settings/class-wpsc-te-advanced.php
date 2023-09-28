<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_TE_Advanced' ) ) :

	final class WPSC_TE_Advanced {

		/**
		 * Initialize this class
		 *
		 * @return void
		 */
		public static function init() {

			// User interface.
			add_action( 'wp_ajax_wpsc_get_te_advanced', array( __CLASS__, 'load_settings_ui' ) );
			add_action( 'wp_ajax_wpsc_set_te_advanced', array( __CLASS__, 'save_settings' ) );
			add_action( 'wp_ajax_wpsc_reset_te_advanced', array( __CLASS__, 'reset_settings' ) );
		}

		/**
		 * Reset settings
		 *
		 * @return void
		 */
		public static function reset() {

			$advanced = apply_filters(
				'wpsc_te_advanced',
				array(
					'html-pasting' => 0,
				)
			);
			update_option( 'wpsc-te-advanced', $advanced );
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
			$settings = get_option( 'wpsc-te-advanced', array() );?>

			<form action="#" onsubmit="return false;" class="wpsc-frm-te-advanced">
				<div class="wpsc-dock-container">
					<?php
					printf(
						/* translators: Click here to see the documentation */
						esc_attr__( '%s to see the documentation!', 'supportcandy' ),
						'<a href="https://supportcandy.net/docs/rich-text-editor/" target="_blank">' . esc_attr__( 'Click here', 'supportcandy' ) . '</a>'
					);
					?>
				</div>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'HTML pasting', 'supportcandy' ); ?></label>
					</div>
					<select id="wpsc-te-hp" name="html-pasting">
						<option <?php selected( $settings['html-pasting'], 1 ); ?> value="1"><?php esc_attr_e( 'Enable', 'supportcandy' ); ?></option>
						<option <?php selected( $settings['html-pasting'], 0 ); ?> value="0"><?php esc_attr_e( 'Disable', 'supportcandy' ); ?></option>
					</select>
				</div>
				<?php do_action( 'wpsc_te_advanced' ); ?>
				<input type="hidden" name="action" value="wpsc_set_te_advanced">
				<input type="hidden" name="_ajax_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wpsc_set_te_advanced' ) ); ?>">
			</form>
			<div class="setting-footer-actions">
				<button 
					class="wpsc-button normal primary margin-right"
					onclick="wpsc_set_te_advanced(this);">
					<?php esc_attr_e( 'Submit', 'supportcandy' ); ?></button>
				<button 
					class="wpsc-button normal secondary"
					onclick="wpsc_reset_te_advanced(this, '<?php echo esc_attr( wp_create_nonce( 'wpsc_reset_te_advanced' ) ); ?>');">
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

			if ( check_ajax_referer( 'wpsc_set_te_advanced', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$advanced = apply_filters(
				'wpsc_set_te_advanced',
				array(
					'html-pasting' => isset( $_POST['html-pasting'] ) ? intval( $_POST['html-pasting'] ) : 1,
				)
			);
			update_option( 'wpsc-te-advanced', $advanced );
			wp_die();
		}

		/**
		 * Reset settings to default
		 *
		 * @return void
		 */
		public static function reset_settings() {

			if ( check_ajax_referer( 'wpsc_reset_te_advanced', '_ajax_nonce', false ) != 1 ) {
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

WPSC_TE_Advanced::init();
