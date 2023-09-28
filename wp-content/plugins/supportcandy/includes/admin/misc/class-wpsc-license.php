<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_License' ) ) :

	final class WPSC_License {

		/**
		 * Initialize this class
		 *
		 * @return void
		 */
		public static function init() {

			add_action( 'wp_ajax_wpsc_license_sync', array( __CLASS__, 'sync_licenses' ) );
		}

		/**
		 * Activate license keys for add-ons
		 *
		 * @return void
		 */
		public static function layout() {
			?>
			<div class="wrap">
				<hr class="wp-header-end">
				<div id="wpsc-container">
					<h1>
						<?php esc_attr_e( 'Licenses', 'supportcandy' ); ?>
						<button class="sync page-title-action"><?php esc_attr_e( 'Sync', 'supportcandy' ); ?></button>
					</h1>
				</div>
				<p><?php esc_attr_e( 'Activate license keys for add-ons in order to get support and updates.', 'supportcandy' ); ?></p>
				<div class="wpsc-licenses-container">
					<?php do_action( 'wpsc_licenses' ); ?>
				</div>
			</div>
			<script>
				jQuery(document).ready(function(){
					jQuery('button.sync').click(function(){
						jQuery(this).text(supportcandy.translations.please_wait);
						const data = { action: 'wpsc_license_sync' };
						jQuery.post(supportcandy.ajax_url, data, function (response) {
							window.location.reload();
						});
					});
				});
			</script>
			<?php
		}

		/**
		 * Sync licenses
		 *
		 * @return void
		 */
		public static function sync_licenses() {

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ) );
			}

			do_action( 'wpsc_license_checker' );
			wp_die();
		}
	}
endif;

WPSC_License::init();
