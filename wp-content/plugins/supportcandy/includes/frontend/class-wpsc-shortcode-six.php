<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_Shortcode_Six' ) ) :

	final class WPSC_Shortcode_Six {

		/**
		 * Initialize this class
		 */
		public static function init() {

			// register shortcode.
			add_shortcode( 'wpsc_agent_profile', array( __CLASS__, 'layout' ) );
		}

		/**
		 * Layout for this shortcode
		 *
		 * @param array $attrs - Shortcode attributes.
		 * @return string
		 */
		public static function layout( $attrs ) {

			$current_user = WPSC_Current_User::$current_user;

			// logout if guest login is open-ticket.
			if ( WPSC_Current_User::$login_type == 'guest' && WPSC_Current_User::$guest_login_type == 'open-ticket' ) {
				$current_user->logout();
			}

			// return empty if logged in user is not an agent.
			if ( $current_user->is_customer && ! $current_user->is_agent ) {
				return '';
			}

			ob_start();?>
			<div id="wpsc-container" style="display:none;">
				<div class="wpsc-shortcode-container">
					<?php

					// logged in agent as agent we checked above.
					if ( $current_user->is_customer ) {
						// js events.
						add_action( 'wpsc_js_ready', array( __CLASS__, 'register_js_ready_function' ) );
						?>
						<div class="wpsc-body"></div>
						<?php
					} else {
						// Not logged in.
						WPSC_Frontend::load_authentication_screen( true, false );
					}
					?>

				</div>
			</div>
			<?php
			WPSC_Frontend::load_html_snippets();
			self::load_js_functions();
			return ob_get_clean();
		}

		/**
		 * Load js functions for this shortcode
		 *
		 * @return void
		 */
		public static function load_js_functions() {
			?>

			<script type="text/javascript">

				/**
				 * Get create ticket form
				 */
				function wpsc_get_agent_profile(is_humbargar = false) {

					jQuery('.wpsc-body').html(supportcandy.loader_html);

					if (supportcandy.is_reload != 1) {
						wpsc_scroll_top();
					} else { supportcandy.is_reload = 0 }

					var url = new URL(window.location.href);
					var search_params = url.searchParams;

					var data = { action: 'wpsc_get_agent_profile'};
					search_params.forEach(function(value, key) {
						data[key] = value;
					});
					jQuery.post(supportcandy.ajax_url, data).done(function (response) {
						jQuery('.wpsc-body').html(response);
						wpsc_reset_responsive_style();
						jQuery('.wpsc-ap-nav.general').trigger('click');
					}).fail(function(response){
						jQuery('.wpsc-body').html('<div style="display:flex; justify-content:center; margin:0 15px 15px; width:100%;"><?php esc_attr_e( 'Unauthorized access!', 'supportcandy' ); ?></div>');
					});
				}
			</script>
			<?php
		}

		/**
		 * JS ready function
		 *
		 * @return void
		 */
		public static function register_js_ready_function() {

			echo 'wpsc_get_agent_profile();' . PHP_EOL;
		}
	}
endif;

WPSC_Shortcode_Six::init();
