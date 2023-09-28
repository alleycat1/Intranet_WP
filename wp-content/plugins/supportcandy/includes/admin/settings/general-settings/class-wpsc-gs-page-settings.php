<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_GS_Page_Settings' ) ) :

	final class WPSC_GS_Page_Settings {

		/**
		 * Initialize this class
		 *
		 * @return void
		 */
		public static function init() {

			// Search WP pages.
			add_action( 'wp_ajax_wpsc_search_wp_pages', array( __CLASS__, 'search_wp_pages' ) );

			// User interface.
			add_action( 'wp_ajax_wpsc_get_gs_page_settings', array( __CLASS__, 'load_settings_ui' ) );
			add_action( 'wp_ajax_wpsc_set_gs_page_settings', array( __CLASS__, 'save_settings' ) );
			add_action( 'wp_ajax_wpsc_reset_gs_page_settings', array( __CLASS__, 'reset_settings' ) );

			// After page delete in WP.
			add_action( 'delete_post', array( __CLASS__, 'after_delete_post' ), 10, 2 );

			// WP_Query search by title.
			add_filter( 'posts_where', array( __CLASS__, 'search_wp_page_title' ), 10, 2 );
		}

		/**
		 * Reset settings
		 *
		 * @return void
		 */
		public static function reset() {

			$page_settings = apply_filters(
				'wpsc_gs_page_settings',
				array(
					'support-page'            => 0,
					'open-ticket-page'        => 0,
					'ticket-url-page'         => 'support-page',
					'new-ticket-page'         => 'default',
					'new-ticket-url'          => '',
					'user-login'              => 'default',
					'custom-login-url'        => '',
					'user-registration'       => 'disable',
					'custom-registration-url' => '',
					'otp-login'               => 1,
					'load-scripts'            => 'all-pages',
					'load-script-pages'       => array(),
				)
			);
			update_option( 'wpsc-gs-page-settings', $page_settings );
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
			$settings = get_option( 'wpsc-gs-page-settings', array() );?>

			<form action="#" onsubmit="return false;" class="wpsc-frm-gs-ps">
				<div class="wpsc-dock-container">
					<?php
					printf(
						/* translators: Click here to see the documentation */
						esc_attr__( '%s to see the documentation!', 'supportcandy' ),
						'<a href="https://supportcandy.net/docs/page-settings/" target="_blank">' . esc_attr__( 'Click here', 'supportcandy' ) . '</a>'
					);
					?>
				</div>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Support page', 'supportcandy' ); ?></label>
					</div>
					<select id="wpsc-support-page" class="wpsc-select-wp-page" name="support-page">
						<?php
						if ( $settings['support-page'] ) {
							$page = get_post( $settings['support-page'] )
							?>
							<option value="<?php echo esc_attr( $page->ID ); ?>"><?php echo esc_attr( $page->post_title ); ?></option>
							<?php
						} else {
							?>
							<option value="0"></option>
							<?php
						}
						?>
					</select>
				</div>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Open ticket page', 'supportcandy' ); ?></label>
					</div>
					<select id="wpsc-ot-page" class="wpsc-select-wp-page" name="open-ticket-page">
						<?php
						if ( $settings['open-ticket-page'] ) {
							$page = get_post( $settings['open-ticket-page'] )
							?>
							<option value="<?php echo esc_attr( $page->ID ); ?>"><?php echo esc_attr( $page->post_title ); ?></option>
							<?php
						} else {
							?>
							<option value="0"></option>
							<?php
						}
						?>
					</select>
				</div>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Ticket url page', 'supportcandy' ); ?></label>
					</div>
					<select id="wpsc-tu-page" name="ticket-url-page">
						<option <?php selected( $settings['ticket-url-page'], 'support-page' ); ?> value="support-page"><?php esc_attr_e( 'Support page', 'supportcandy' ); ?></option>
						<option <?php selected( $settings['ticket-url-page'], 'open-ticket-page' ); ?> value="open-ticket-page"><?php esc_attr_e( 'Open ticket page', 'supportcandy' ); ?></option>
					</select>
				</div>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'New ticket page', 'supportcandy' ); ?></label>
					</div>
					<select class="wpsc-login-url" name="new-ticket-page">
						<option <?php selected( $settings['new-ticket-page'], 'default' ); ?> value="default"><?php esc_attr_e( 'Support page', 'supportcandy' ); ?></option>
						<option <?php selected( $settings['new-ticket-page'], 'custom' ); ?> value="custom"><?php esc_attr_e( 'Custom', 'supportcandy' ); ?></option>
					</select>
				</div>
				<div class="wpsc-input-group" style="<?php echo $settings['new-ticket-page'] != 'custom' ? 'display:none' : ''; ?>">
					<input type="text" name="new-ticket-url" value="<?php echo esc_url( $settings['new-ticket-url'] ); ?>" placeholder="e.g. https://yourdomain.com/create-ticket" autocomplete="off">
				</div>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'User login', 'supportcandy' ); ?></label>
					</div>
					<select class="wpsc-login-url" name="user-login">
						<option <?php selected( $settings['user-login'], 'default' ); ?> value="default"><?php esc_attr_e( 'Default', 'supportcandy' ); ?></option>
						<option <?php selected( $settings['user-login'], 'wp-default' ); ?> value="wp-default"><?php esc_attr_e( 'WP Default Login', 'supportcandy' ); ?></option>
						<option <?php selected( $settings['user-login'], 'custom' ); ?> value="custom"><?php esc_attr_e( 'Custom', 'supportcandy' ); ?></option>
					</select>
				</div>
				<div class="wpsc-input-group" style="<?php echo $settings['user-login'] != 'custom' ? 'display:none' : ''; ?>">
					<input type="text" name="custom-login-url" value="<?php echo esc_url( $settings['custom-login-url'] ); ?>" placeholder="e.g. https://yourdomain.com/login" autocomplete="off">
				</div>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'User registration', 'supportcandy' ); ?></label>
					</div>
					<select class="wpsc-login-url" name="user-registration">
						<option <?php selected( $settings['user-registration'], 'disable' ); ?> value="disable"><?php esc_attr_e( 'Disable', 'supportcandy' ); ?></option>
						<option <?php selected( $settings['user-registration'], 'default' ); ?> value="default"><?php esc_attr_e( 'Default', 'supportcandy' ); ?></option>
						<option <?php selected( $settings['user-registration'], 'wp-default' ); ?> value="wp-default"><?php esc_attr_e( 'WP Default Registration', 'supportcandy' ); ?></option>
						<option <?php selected( $settings['user-registration'], 'custom' ); ?> value="custom"><?php esc_attr_e( 'Custom', 'supportcandy' ); ?></option>
					</select>
				</div>
				<div class="wpsc-input-group" style="<?php echo $settings['user-registration'] != 'custom' ? 'display:none' : ''; ?>">
					<input type="text" name="custom-registration-url" value="<?php echo esc_url( $settings['custom-registration-url'] ); ?>" placeholder="e.g. https://yourdomain.com/register" autocomplete="off">
				</div>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'OTP Login', 'supportcandy' ); ?></label>
					</div>
					<select name="otp-login">
						<option <?php selected( $settings['otp-login'], 0 ); ?> value="0"><?php esc_attr_e( 'Disable', 'supportcandy' ); ?></option>
						<option <?php selected( $settings['otp-login'], 1 ); ?> value="1"><?php esc_attr_e( 'Enable', 'supportcandy' ); ?></option>
					</select>
				</div>

				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Load scripts', 'supportcandy' ); ?></label>
					</div>
					<select class="wpsc-load-scripts" name="load-scripts">
						<option <?php selected( $settings['load-scripts'], 'all-pages' ); ?> value="all-pages"><?php esc_attr_e( 'All Pages', 'supportcandy' ); ?></option>
						<option <?php selected( $settings['load-scripts'], 'custom' ); ?> value="custom"><?php esc_attr_e( 'Custom', 'supportcandy' ); ?></option>
					</select>
				</div>
				<div class="wpsc-input-group" style="<?php echo $settings['load-scripts'] != 'custom' ? 'display:none' : ''; ?>">
					<select id="wpsc-load-script-pages" class="wpsc-select-wp-page" name="load-script-pages[]" multiple>
						<?php
						foreach ( $settings['load-script-pages'] as $page_id ) {
							?>
							<option selected value="<?php echo esc_attr( $page_id ); ?>"><?php echo esc_attr( get_the_title( $page_id ) ); ?></option>
							<?php
						}
						?>
					</select>
				</div>

				<?php do_action( 'wpsc_gs_page_settings' ); ?>

				<script>
					jQuery('.wpsc-login-url, .wpsc-load-scripts').change(function(){
						var urlInput = jQuery(this).closest('.wpsc-input-group').next();
						if (jQuery(this).val() === 'custom') {
							urlInput.show();
						} else {
							urlInput.hide();
						}
					});
					jQuery('.wpsc-select-wp-page').selectWoo({
						ajax: {
							url: supportcandy.ajax_url,
							dataType: 'json',
							delay: 250,
							data: function (params) {
								return {
									q: params.term, // search term
									page: params.page,
									action: 'wpsc_search_wp_pages',
									_ajax_nonce: '<?php echo esc_attr( wp_create_nonce( 'wpsc_search_wp_pages' ) ); ?>'
								};
							},
							processResults: function (data, params) {
								var terms = [];
								if ( data ) {
									jQuery.each( data, function( id, text ) {
										terms.push( { id: text.id, text: text.title } );
									});
								}
								return {
									results: terms
								};
							},
							cache: true
						},
						escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
						minimumInputLength: 1,
						allowClear: true,
						placeholder: ""
					});
				</script>
				<input type="hidden" name="action" value="wpsc_set_gs_page_settings">
				<input type="hidden" name="_ajax_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wpsc_set_gs_page_settings' ) ); ?>">

			</form>
			<div class="setting-footer-actions">
				<button 
					class="wpsc-button normal primary margin-right"
					onclick="wpsc_set_gs_page_settings(this);">
					<?php esc_attr_e( 'Submit', 'supportcandy' ); ?></button>
				<button 
					class="wpsc-button normal secondary"
					onclick="wpsc_reset_gs_page_settings(this, '<?php echo esc_attr( wp_create_nonce( 'wpsc_reset_gs_page_settings' ) ); ?>');">
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

			if ( check_ajax_referer( 'wpsc_set_gs_page_settings', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$new_ticket_url  = isset( $_POST['new-ticket-url'] ) && filter_var( wp_unslash( $_POST['new-ticket-url'] ), FILTER_VALIDATE_URL ) ? sanitize_text_field( wp_unslash( $_POST['new-ticket-url'] ) ) : '';
			$new_ticket_page = isset( $_POST['new-ticket-page'] ) ? sanitize_text_field( wp_unslash( $_POST['new-ticket-page'] ) ) : 'default';
			if ( $new_ticket_page == 'custom' && ! $new_ticket_url ) {
				$new_ticket_page = 'default';
			}

			$custom_login_url = isset( $_POST['custom-login-url'] ) && filter_var( wp_unslash( $_POST['custom-login-url'] ), FILTER_VALIDATE_URL ) ? sanitize_text_field( wp_unslash( $_POST['custom-login-url'] ) ) : '';
			$user_login       = isset( $_POST['user-login'] ) ? sanitize_text_field( wp_unslash( $_POST['user-login'] ) ) : 'default';
			if ( $user_login == 'custom' && ! $custom_login_url ) {
				$user_login = 'default';
			}

			$custom_registration_url = isset( $_POST['custom-registration-url'] ) && filter_var( wp_unslash( $_POST['custom-registration-url'] ), FILTER_VALIDATE_URL ) ? sanitize_text_field( wp_unslash( $_POST['custom-registration-url'] ) ) : '';
			$user_registration       = isset( $_POST['user-registration'] ) ? sanitize_text_field( wp_unslash( $_POST['user-registration'] ) ) : 'disable';
			if ( $user_registration == 'custom' && ! $custom_registration_url ) {
				$user_registration = 'disable';
			}

			$page_settings = apply_filters(
				'wpsc_set_gs_page_settings',
				array(
					'support-page'            => isset( $_POST['support-page'] ) ? intval( $_POST['support-page'] ) : 0,
					'open-ticket-page'        => isset( $_POST['open-ticket-page'] ) ? intval( $_POST['open-ticket-page'] ) : 0,
					'ticket-url-page'         => isset( $_POST['ticket-url-page'] ) ? sanitize_text_field( wp_unslash( $_POST['ticket-url-page'] ) ) : 'support-page',
					'new-ticket-page'         => $new_ticket_page,
					'new-ticket-url'          => $new_ticket_url,
					'user-login'              => $user_login,
					'custom-login-url'        => $custom_login_url,
					'user-registration'       => $user_registration,
					'custom-registration-url' => $custom_registration_url,
					'otp-login'               => isset( $_POST['otp-login'] ) ? intval( $_POST['otp-login'] ) : 0,
					'load-scripts'            => isset( $_POST['load-scripts'] ) ? sanitize_text_field( wp_unslash( $_POST['load-scripts'] ) ) : 'all-pages',
					'load-script-pages'       => isset( $_POST['load-script-pages'] ) ? array_filter( array_map( 'sanitize_text_field', wp_unslash( $_POST['load-script-pages'] ) ) ) : array(),
				)
			);
			update_option( 'wpsc-gs-page-settings', $page_settings );
			wp_die();
		}

		/**
		 * Reset settings to default
		 *
		 * @return void
		 */
		public static function reset_settings() {

			if ( check_ajax_referer( 'wpsc_reset_gs_page_settings', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}
			self::reset();
			wp_die();
		}

		/**
		 * Search WP pages
		 */
		public static function search_wp_pages() {

			if ( check_ajax_referer( 'wpsc_search_wp_pages', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$term = isset( $_GET['q'] ) ? sanitize_text_field( wp_unslash( $_GET['q'] ) ) : '';

			$args = array(
				'post_type'         => 'page',
				'post_status'       => 'publish',
				'orderby'           => array( 'title' => 'ASC' ),
				'search_wpsc_title' => $term,
			);
			$query = new WP_Query( $args );

			$response = array();
			foreach ( $query->posts as $page ) {
				$response[] = array(
					'id'    => $page->ID,
					'title' => $page->post_title,
				);
			}
			wp_send_json( $response );
		}

		/**
		 * Remove support page if already set when post is deleted
		 *
		 * @param integer $postid - post id.
		 * @param WP_Post $post - post object.
		 * @return void
		 */
		public static function after_delete_post( $postid, $post ) {

			$page_settings = get_option( 'wpsc-gs-page-settings' );

			if ( $page_settings['support-page'] == $postid ) {
				$page_settings['support-page'] = 0;
			}

			if ( $page_settings['open-ticket-page'] == $postid ) {
				$page_settings['open-ticket-page'] = 0;
			}

			update_option( 'wpsc-gs-page-settings', $page_settings );
		}

		/**
		 * Search wp posts by title.
		 *
		 * @param array $where - where array in wp_query.
		 * @param array $wp_query - wp query array.
		 * @return array
		 */
		public static function search_wp_page_title( $where, $wp_query ) {
			global $wpdb;
			$search_term = $wp_query->get( 'search_wpsc_title' );
			if ( $search_term ) {
				$where .= ' AND ' . $wpdb->posts . '.post_title LIKE \'%' . esc_sql( $wpdb->esc_like( $search_term ) ) . '%\'';
			}
			return $where;
		}
	}
endif;

WPSC_GS_Page_Settings::init();
