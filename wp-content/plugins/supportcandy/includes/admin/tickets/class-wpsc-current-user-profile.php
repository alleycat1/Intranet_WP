<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_Current_User_Profile' ) ) :

	final class WPSC_Current_User_Profile {

		/**
		 * Initialize this class
		 */
		public static function init() {

			// Get agent settings ajax request.
			add_action( 'wp_ajax_wpsc_get_user_profile', array( __CLASS__, 'get_user_profile' ) );
			add_action( 'wp_ajax_nopriv_wpsc_get_user_profile', array( __CLASS__, 'get_user_profile' ) );
			add_action( 'wp_ajax_wpsc_set_my_profile', array( __CLASS__, 'set_my_profile' ) );
			add_action( 'wp_ajax_nopriv_wpsc_set_my_profile', array( __CLASS__, 'set_my_profile' ) );

			// User logout.
			add_action( 'wp_ajax_wpsc_user_logout', array( __CLASS__, 'logout' ) );
			add_action( 'wp_ajax_nopriv_wpsc_user_logout', array( __CLASS__, 'logout' ) );
		}

		/**
		 * New ticket ajax callback
		 *
		 * @return void
		 */
		public static function get_user_profile() {

			$current_user = WPSC_Current_User::$current_user?>

			<form class="wpsc-my-profile" onsubmit="return false;" action="#">
				<?php

				$cf = WPSC_Custom_Field::get_cf_by_slug( 'name' )
				?>
				<div class="wpsc-tff wpsc-xs-12 wpsc-sm-12 wpsc-md-12 wpsc-lg-12">
					<div class="wpsc-tff-label">
						<span class="name"><?php echo esc_attr( $cf->name ); ?></span>
					</div>
					<span class="extra-info"><?php echo esc_attr( $cf->extra_info ); ?></span>
					<input 
						type="text" 
						name="<?php echo esc_attr( $cf->slug ); ?>" 
						value="<?php echo esc_attr( $current_user->customer->name ); ?>"
						placeholder="<?php echo esc_attr( $cf->placeholder_text ); ?>"
						autocomplete="off"/>
				</div>
				<?php

				foreach ( WPSC_Custom_Field::$custom_fields as $cf ) {
					if ( $cf->field !== 'customer' || ! $cf->allow_my_profile || in_array( $cf->slug, WPSC_DF_Customer::$ignore_customer_info_cft ) ) {
						continue;
					}
					$properties = array(
						'is-required' => 0,
						'width'       => 'full',
						'visibility'  => '',
					);
					echo $cf->type::print_tff( $cf, $properties ); // phpcs:ignore
				}

				do_action( 'wpsc_my_profile' )
				?>

				<input type="hidden" name="action" value="wpsc_set_my_profile"/>
				<input type="hidden" name="_ajax_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wpsc_set_my_profile' ) ); ?>">
			</form>

			<div class="wpsc-tff wpsc-xs-12 wpsc-sm-12 wpsc-md-12 wpsc-lg-12">
				<div class="submit-container">
					<button class="wpsc-button normal primary" onclick="wpsc_submit_my_profile(this);"><?php esc_attr_e( 'Save Changes', 'supportcandy' ); ?></button>
					<button class="wpsc-button normal secondary" onclick="wpsc_user_logout(this, '<?php echo esc_attr( wp_create_nonce( 'wpsc_user_logout' ) ); ?>')"><?php esc_attr_e( 'Logout', 'supportcandy' ); ?></button>
				</div>
			</div>

			<script>

				/**
				 * Submit My Profile form
				 *
				 * @return void
				 */
				function wpsc_submit_my_profile(el) {

					if (wpsc_is_description_text()) {
						if (!confirm(supportcandy.translations.warning_message)) {
							return;
						}
					}

					var dataform = new FormData(jQuery('form.wpsc-my-profile')[0]);
					jQuery(el).text(supportcandy.translations.please_wait);

					<?php
					$recaptcha = get_option( 'wpsc-recaptcha-settings' );
					if ( $recaptcha['allow-recaptcha'] === 1 && $recaptcha['recaptcha-version'] == 3 && $recaptcha['recaptcha-site-key'] && $recaptcha['recaptcha-secret-key'] ) {
						?>
						grecaptcha.ready(function() {
							grecaptcha.execute('<?php echo esc_attr( $recaptcha['recaptcha-site-key'] ); ?>', {action: 'submit_my_profile'}).then(function(token) {
								dataform.append('g-recaptcha-response', token);
								wpsc_post_my_profile_form(dataform);
							});
						});
						<?php
					} else {
						?>
						wpsc_post_my_profile_form(dataform);
						<?php
					}
					?>
				}

				/**
				 * Post my profile form
				 *
				 * @return void
				 */
				function wpsc_post_my_profile_form(dataform) {

					jQuery.ajax({
						url: supportcandy.ajax_url,
						type: 'POST',
						data: dataform,
						processData: false,
						contentType: false,
						error: function (res) {
							wpsc_get_user_profile();
						},
						success: function (res, textStatus, xhr) {
							wpsc_get_user_profile();
						}
					});
				}
				<?php do_action( 'wpsc_js_my_profile_functions' ); ?>
			</script>
			<?php
			wp_die();
		}

		/**
		 * Set my profile
		 *
		 * @return void
		 */
		public static function set_my_profile() {

			if ( check_ajax_referer( 'wpsc_set_my_profile', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			WPSC_MS_Recaptcha::validate( 'submit_my_profile' );

			$current_user = WPSC_Current_User::$current_user;
			if ( ! $current_user->is_customer ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$name = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
			if ( ! $name ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			if ( $current_user->customer->name != $name ) {
				$current_user->customer->name = $name;
				$current_user->customer->save();
				// Update WP User if available.
				if ( $current_user->user ) {
					wp_update_user(
						array(
							'ID'           => $current_user->user->ID,
							'display_name' => $name,
						)
					);
				}
			}

			$cfs = array();
			foreach ( WPSC_Custom_Field::$custom_fields as $cf ) {

				if ( $cf->field !== 'customer' || $cf->type::$is_default ) {
					continue;
				}
				$cfs[ $cf->type::$slug ][] = $cf;
			}

			foreach ( $cfs as $slug => $fields ) {
				WPSC_Functions::$ref_classes[ $slug ]['class']::set_create_ticket_data( array( 'customer' => $current_user->customer->id ), $cfs, true );
			}

			wp_die();
		}

		/**
		 * Logout current user
		 *
		 * @return void
		 */
		public static function logout() {

			if ( check_ajax_referer( 'wpsc_user_logout', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			$current_user = WPSC_Current_User::$current_user;
			$current_user->logout();
			wp_die();
		}
	}
endif;

WPSC_Current_User_Profile::init();
