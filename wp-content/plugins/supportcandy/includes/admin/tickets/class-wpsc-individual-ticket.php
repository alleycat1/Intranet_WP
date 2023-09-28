<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_Individual_Ticket' ) ) :

	final class WPSC_Individual_Ticket {

		/**
		 * Current ticket object (ticket model object)
		 *
		 * @var WPSC_Ticket
		 */
		public static $ticket;

		/**
		 * Viewing profile of current user of this ticket
		 *
		 * @var string
		 */
		public static $view_profile;

		/**
		 * Reply profile of current user of this ticket
		 *
		 * @var string
		 */
		public static $reply_profile;

		/**
		 * Actions for the individual ticket
		 *
		 * @var array
		 */
		private static $actions;

		/**
		 * Submit actions
		 *
		 * @var array
		 */
		private static $submit_actions;

		/**
		 * Thread actions
		 *
		 * @var array
		 */
		private static $thread_actions = array();

		/**
		 * Widget HTML displayed in two places, sidebar and body to maintain responsive behaviour.
		 * By loading their HTML only once, we have optmized load time here.
		 *
		 * @var string
		 */
		private static $widget_html;

		/**
		 * Set whether ticket url is authenticated or not.
		 *
		 * @var boolean
		 */
		public static $url_auth = false;

		/**
		 * Initialize the class
		 *
		 * @return void
		 */
		public static function init() {

			add_action( 'wp_ajax_wpsc_get_individual_ticket', array( __CLASS__, 'layout' ) );
			add_action( 'wp_ajax_nopriv_wpsc_get_individual_ticket', array( __CLASS__, 'layout' ) );

			// Submit ticket.
			add_action( 'wp_ajax_wpsc_it_add_reply', array( __CLASS__, 'add_reply' ) );
			add_action( 'wp_ajax_nopriv_wpsc_it_add_reply', array( __CLASS__, 'add_reply' ) );

			// Submit note.
			add_action( 'wp_ajax_wpsc_it_add_note', array( __CLASS__, 'add_note' ) );
			add_action( 'wp_ajax_nopriv_wpsc_it_add_note', array( __CLASS__, 'add_note' ) );

			// Submit note.
			add_action( 'wp_ajax_wpsc_it_reply_and_close', array( __CLASS__, 'it_reply_and_close' ) );
			add_action( 'wp_ajax_nopriv_wpsc_it_reply_and_close', array( __CLASS__, 'it_reply_and_close' ) );

			// Duplicate ticket.
			add_action( 'wp_ajax_wpsc_it_get_duplicate_ticket', array( __CLASS__, 'get_duplicate_ticket' ) );
			add_action( 'wp_ajax_nopriv_wpsc_it_get_duplicate_ticket', array( __CLASS__, 'get_duplicate_ticket' ) );
			add_action( 'wp_ajax_wpsc_it_set_duplicate_ticket', array( __CLASS__, 'set_duplicate_ticket' ) );
			add_action( 'wp_ajax_nopriv_wpsc_it_set_duplicate_ticket', array( __CLASS__, 'set_duplicate_ticket' ) );

			// Delete ticket.
			add_action( 'wp_ajax_wpsc_it_delete_ticket', array( __CLASS__, 'set_delete_ticket' ) );
			add_action( 'wp_ajax_nopriv_wpsc_it_delete_ticket', array( __CLASS__, 'set_delete_ticket' ) );
			add_action( 'wp_ajax_wpsc_it_ticket_restore', array( __CLASS__, 'ticket_restore' ) );
			add_action( 'wp_ajax_nopriv_wpsc_it_ticket_restore', array( __CLASS__, 'ticket_restore' ) );
			add_action( 'wp_ajax_wpsc_it_delete_permanently', array( __CLASS__, 'set_delete_ticket_permanently' ) );
			add_action( 'wp_ajax_nopriv_wpsc_it_delete_permanently', array( __CLASS__, 'set_delete_ticket_permanently' ) );

			// Thread actions.
			add_action( 'wp_ajax_wpsc_it_thread_info', array( __CLASS__, 'it_thread_info' ) );
			add_action( 'wp_ajax_nopriv_wpsc_it_thread_info', array( __CLASS__, 'it_thread_info' ) );
			add_action( 'wp_ajax_wpsc_it_get_edit_thread', array( __CLASS__, 'get_edit_thread' ) );
			add_action( 'wp_ajax_nopriv_wpsc_it_get_edit_thread', array( __CLASS__, 'get_edit_thread' ) );
			add_action( 'wp_ajax_wpsc_it_set_edit_thread', array( __CLASS__, 'set_edit_thread' ) );
			add_action( 'wp_ajax_nopriv_wpsc_it_set_edit_thread', array( __CLASS__, 'set_edit_thread' ) );
			add_action( 'wp_ajax_wpsc_it_get_thread', array( __CLASS__, 'get_thread_html' ) );
			add_action( 'wp_ajax_nopriv_wpsc_it_get_thread', array( __CLASS__, 'get_thread_html' ) );
			add_action( 'wp_ajax_wpsc_it_thread_delete', array( __CLASS__, 'delete_thread' ) );
			add_action( 'wp_ajax_nopriv_wpsc_it_thread_delete', array( __CLASS__, 'delete_thread' ) );
			add_action( 'wp_ajax_wpsc_it_view_thread_log', array( __CLASS__, 'view_thread_log' ) );
			add_action( 'wp_ajax_nopriv_wpsc_it_view_thread_log', array( __CLASS__, 'view_thread_log' ) );
			add_action( 'wp_ajax_wpsc_it_view_deleted_thread', array( __CLASS__, 'view_deleted_thread' ) );
			add_action( 'wp_ajax_nopriv_wpsc_it_view_deleted_thread', array( __CLASS__, 'view_deleted_thread' ) );
			add_action( 'wp_ajax_wpsc_it_restore_thread', array( __CLASS__, 'restore_thread' ) );
			add_action( 'wp_ajax_nopriv_wpsc_it_restore_thread', array( __CLASS__, 'restore_thread' ) );
			add_action( 'wp_ajax_wpsc_it_thread_delete_permanently', array( __CLASS__, 'thread_delete_permanently' ) );
			add_action( 'wp_ajax_nopriv_wpsc_it_thread_delete_permanently', array( __CLASS__, 'thread_delete_permanently' ) );
			add_action( 'wp_ajax_wpsc_it_thread_new_ticket', array( __CLASS__, 'it_thread_new_ticket' ) );
			add_action( 'wp_ajax_nopriv_wpsc_it_thread_new_ticket', array( __CLASS__, 'it_thread_new_ticket' ) );
			add_action( 'wp_ajax_wpsc_it_set_thread_new_ticket', array( __CLASS__, 'it_set_thread_new_ticket' ) );
			add_action( 'wp_ajax_nopriv_wpsc_it_set_thread_new_ticket', array( __CLASS__, 'it_set_thread_new_ticket' ) );

			// Load older threads.
			add_action( 'wp_ajax_wpsc_load_older_threads', array( __CLASS__, 'load_older_threads' ) );
			add_action( 'wp_ajax_nopriv_wpsc_load_older_threads', array( __CLASS__, 'load_older_threads' ) );

			// auto save reply.
			add_action( 'wp_ajax_wpsc_auto_save', array( __CLASS__, 'auto_save' ) );
			add_action( 'wp_ajax_nopriv_wpsc_auto_save', array( __CLASS__, 'auto_save' ) );
			add_action( 'wpsc_post_reply', array( __CLASS__, 'clear_auto_save' ) );
			add_action( 'wpsc_submit_note', array( __CLASS__, 'clear_auto_save' ) );
			add_action( 'wp_ajax_wpsc_clear_saved_draft_reply', array( __CLASS__, 'clear_saved_draft_reply' ) );
			add_action( 'wp_ajax_nopriv_wpsc_clear_saved_draft_reply', array( __CLASS__, 'clear_saved_draft_reply' ) );

			// agent collision.
			add_filter( 'wp_ajax_wpsc_check_live_agents', array( __CLASS__, 'check_live_agents' ) );
			add_action( 'wp_ajax_nopriv_wpsc_check_live_agents', array( __CLASS__, 'check_live_agents' ) );
		}

		/**
		 * Ajax callback function for individual ticket
		 *
		 * @return void
		 */
		public static function layout() {

			$gs = get_option( 'wpsc-gs-general' );
			self::load_current_ticket();
			self::load_actions();
			self::load_widget_html();?>
			<div class="wpsc-it-container">
				<div class="wpsc-it-body">
				<?php
					self::get_actions();
					self::get_subject();
					self::get_live_agents();
					self::get_mobile_widgets();
				if ( $gs['reply-form-position'] == 'top' ) {
					self::get_reply_section();
					self::get_thread_section();
				} else {
					self::get_thread_section();
					self::get_reply_section();
				}
				?>
				</div>
				<div class="wpsc-it-sidebar-widget-container wpsc-hidden-xs wpsc-hidden-sm">
					<?php echo self::$widget_html; // phpcs:ignore?>
				</div>
			</div>
			<div style="display:none" id="wpsc-ticket-url"><?php echo esc_url( self::$ticket->get_url() ); ?></div>
			<input type="hidden" id="wpsc-current-ticket" value="<?php echo intval( self::$ticket->id ); ?>">
			<?php
			wp_die();
		}

		/**
		 * Load current ticket using id we got from ajax request
		 * Ignore phpcs nonce issue as we already checked where it is called from.
		 *
		 * @return void
		 */
		public static function load_current_ticket() {

			$current_user = WPSC_Current_User::$current_user;

			$id = isset( $_POST['ticket_id'] ) ? intval( $_POST['ticket_id'] ) : 0; // phpcs:ignore
			if ( ! $id ) {
				wp_send_json_error( new WP_Error( '001', 'Unauthorized!' ), 401 );
			}

			$ticket = new WPSC_Ticket( $id );
			if ( ! $ticket->id ) {
				wp_send_json_error( new WP_Error( '002', 'Something went wrong!' ), 400 );
			}
			self::$ticket = $ticket;

			// url authentication.
			$auth_code = isset( $_REQUEST['auth-code'] ) ? sanitize_text_field( $_REQUEST['auth-code'] ) : ''; // phpcs:ignore
			if ( ! $auth_code ) {
				$auth_code = isset( $_REQUEST['auth_code'] ) ? sanitize_text_field( $_REQUEST['auth_code'] ) : ''; // phpcs:ignore
			}
			if ( $auth_code && $ticket->auth_code == $auth_code ) {
				self::$url_auth = true;
			}

			// Check whether view profile is a customer.
			if ( self::is_customer() || self::$url_auth ) {
				self::$view_profile = 'customer';
			}

			// Check whether view profile is an agent.
			if ( $current_user->is_agent && self::has_ticket_cap( 'view' ) ) {
				self::$view_profile = 'agent';
			}

			if ( ! self::$view_profile ) :
				?>
				<div style="align-item:center;" ><h6><?php esc_attr_e( 'Unathorized access!' ); ?></h6></div>
				<?php
				wp_die();
			endif;

			// Check if ticket is deleted and whether current user has access to deleted tickets.
			if ( ! self::$ticket->is_active && ! ( self::$view_profile == 'agent' && $current_user->agent->has_cap( 'dtt-access' ) ) ) {
				wp_send_json_error( new WP_Error( '003', 'Unauthorized!' ), 401 );
			}
		}

		/**
		 * Load actions of ticket which will be used in action bar
		 *
		 * @return void
		 */
		public static function load_actions() {

			$current_user = WPSC_Current_User::$current_user;
			$actions      = array(
				'refresh' => array(
					'label'    => esc_attr__( 'Refresh', 'supportcandy' ),
					'callback' => 'wpsc_it_ab_refresh(' . self::$ticket->id . ');',
				),
			);

			$gs          = get_option( 'wpsc-gs-general' );
			$tl_advanced = get_option( 'wpsc-tl-ms-advanced' );

			if ( self::$ticket->is_active ) {

				$close_flag = false;
				if ( $current_user->is_customer &&
					(
						( $current_user->customer->id == self::$ticket->customer->id && in_array( 'customer', $gs['allow-close-ticket'] ) ) ||
						( $current_user->is_agent && self::has_ticket_cap( 'cs' ) && in_array( $current_user->agent->role, $gs['allow-close-ticket'] ) )
					)
				) {
					$close_flag = true;
				}

				$close_flag = apply_filters( 'wpsc_it_action_close_flag', $close_flag, self::$ticket );

				if (
					$close_flag &&
					! (
						self::$ticket->status->id == $gs['close-ticket-status'] ||
						in_array( self::$ticket->status->id, $tl_advanced['closed-ticket-statuses'] )
					)
				) {
					$actions['close'] = array(
						'label'    => esc_attr__( 'Close', 'supportcandy' ),
						'callback' => 'wpsc_it_close_ticket(' . self::$ticket->id . ', \'' . esc_attr( wp_create_nonce( 'wpsc_it_close_ticket' ) ) . '\');',
					);
				}

				// Duplicate.
				if ( self::$view_profile == 'agent' && self::has_ticket_cap( 'dt' ) ) {
					$actions['duplicate'] = array(
						'label'    => esc_attr__( 'Duplicate', 'supportcandy' ),
						'callback' => 'wpsc_it_get_duplicate_ticket(' . self::$ticket->id . ', \'' . esc_attr( wp_create_nonce( 'wpsc_it_get_duplicate_ticket' ) ) . '\');',
					);
				}

				// Delete.
				if ( $current_user->is_agent && self::has_ticket_cap( 'dtt' ) ) {
					$actions['delete'] = array(
						'label'    => esc_attr__( 'Delete', 'supportcandy' ),
						'callback' => 'wpsc_it_delete_ticket(' . self::$ticket->id . ', \'' . esc_attr( wp_create_nonce( 'wpsc_it_delete_ticket' ) ) . '\');',
					);
				}

				// Copy url.
				$actions['copy'] = array(
					'label'    => esc_attr__( 'Copy URL', 'supportcandy' ),
					'callback' => 'wpsc_it_copy_url(' . self::$ticket->id . ');',
				);

			} else {

				// Restore && permenently delete.
				if ( $current_user->is_agent && self::has_ticket_cap( 'dtt' ) ) {
					$actions['restore'] = array(
						'label'    => esc_attr__( 'Restore', 'supportcandy' ),
						'callback' => 'wpsc_it_ticket_restore(' . self::$ticket->id . ', \'' . esc_attr( wp_create_nonce( 'wpsc_it_ticket_restore' ) ) . '\');',
					);
				}

				// Permanently delete.
				if ( $current_user->is_agent && $current_user->user->has_cap( 'manage_options' ) ) {
					$actions['delete-permanently'] = array(
						'label'    => esc_attr__( 'Delete Permanently', 'supportcandy' ),
						'callback' => 'wpsc_it_delete_permanently(' . self::$ticket->id . ', \'' . esc_attr( wp_create_nonce( 'wpsc_it_delete_permanently' ) ) . '\');',
					);
				}
			}

			self::$actions = apply_filters( 'wpsc_individual_ticket_actions', $actions, self::$ticket );

			if ( self::$view_profile == 'agent' ) {

				// Submit actions.
				$submit_actions = array();
				if ( self::has_ticket_cap( 'reply' ) || self::is_customer() ) {
					$submit_actions['reply'] = array(
						'icon'     => 'reply',
						'label'    => esc_attr__( 'Reply', 'supportcandy' ),
						'callback' => 'wpsc_it_add_reply(' . self::$ticket->id . ');',
					);
				}
				if ( self::has_ticket_cap( 'pn' ) ) {
					$submit_actions['private-note'] = array(
						'icon'     => 'notes',
						'label'    => esc_attr__( 'Private Note', 'supportcandy' ),
						'callback' => 'wpsc_it_add_private_note(' . self::$ticket->id . ');',
					);
				}
				if ( ( self::has_ticket_cap( 'reply' ) && self::has_ticket_cap( 'cs' ) ) &&
					! (
					self::$ticket->status->id == $gs['close-ticket-status'] ||
					in_array( self::$ticket->status->id, $tl_advanced['closed-ticket-statuses'] )
					) &&
					( $current_user->is_agent && in_array( $current_user->agent->role, $gs['allow-close-ticket'] ) )
				) {
					$submit_actions['reply-and-close'] = array(
						'icon'     => 'reply',
						'label'    => esc_attr__( 'Reply & Close', 'supportcandy' ),
						'callback' => 'wpsc_it_reply_and_close(' . self::$ticket->id . ', \'' . esc_attr( wp_create_nonce( 'wpsc_it_reply_and_close' ) ) . '\');',
					);
				}
				self::$submit_actions = apply_filters( 'wpsc_it_submit_actions', $submit_actions, self::$ticket );

				// Thread actions.
				$thread_actions = array(
					'info'       => array(
						'icon'     => 'info-circle',
						'label'    => esc_attr__( 'Info', 'supportcandy' ),
						'callback' => 'wpsc_it_thread_info',
					),
					'new-ticket' => array(
						'icon'     => 'plus-square',
						'label'    => esc_attr__( 'Create new ticket from this thread', 'supportcandy' ),
						'callback' => 'wpsc_it_thread_new_ticket',
					),
				);

				if ( self::$ticket->is_active && self::has_ticket_cap( 'eth' ) ) {
					$thread_actions['edit'] = array(
						'icon'     => 'edit',
						'label'    => esc_attr__( 'Edit', 'supportcandy' ),
						'callback' => 'wpsc_it_get_edit_thread',
					);
				}

				if ( self::$ticket->is_active && self::has_ticket_cap( 'dth' ) ) {
					$thread_actions['delete'] = array(
						'icon'     => 'trash-alt',
						'label'    => esc_attr__( 'Delete', 'supportcandy' ),
						'callback' => 'wpsc_it_thread_delete',
					);
				}
				self::$thread_actions = apply_filters( 'wpsc_it_thead_actions', $thread_actions );
			}
		}

		/**
		 * Load widget at once and save html to $widget_html
		 *
		 * @return void
		 */
		private static function load_widget_html() {

			$current_user = WPSC_Current_User::$current_user;
			$widgets = get_option( 'wpsc-ticket-widget' );
			ob_start();

			do_action( 'wpsc_before_ticket_widget', self::$ticket );

			foreach ( $widgets as $slug => $widget ) :
				if ( ! $widget['is_enable'] ) {
					continue;
				}
				if ( ! class_exists( $widget['class'] ) ) {
					continue;
				}
				$widget['class']::print_widget( self::$ticket, $widget );
			endforeach;

			do_action( 'wpsc_after_ticket_widget', self::$ticket );

			self::$widget_html = ob_get_clean();
		}

		/**
		 * Action bar where verious actions like refresh, close, duplicate, etc. are available
		 *
		 * @return void
		 */
		public static function get_actions() {

			$actions_arr = array();
			foreach ( self::$actions as $key => $action ) :
				ob_start();
				?>
				<span 
					class="wpsc-link wpsc-it-<?php echo esc_attr( $key ); ?>"
					onclick="<?php echo esc_attr( $action['callback'] ); ?>">
					<?php echo esc_attr( $action['label'] ); ?>
				</span>
				<?php
				$actions_arr[] = ob_get_clean();
			endforeach;
			?>

			<div class="wpsc-it-action-container">
				<div class="wpsc-filter-actions">
					<?php echo implode( '<div class="action-devider"></div>', $actions_arr ); // phpcs:ignore?>
				</div>
			</div>
			<?php
		}

		/**
		 * Subject bar of the ticket
		 *
		 * @return void
		 */
		public static function get_subject() {
			$gs = get_option( 'wpsc-gs-general' );
			?>
			<div class="wpsc-it-body-item wpsc-it-subject-container">
				<h2><?php echo '[' . esc_attr( $gs['ticket-alice'] ) . esc_attr( self::$ticket->id ) . '] ' . esc_attr( self::$ticket->subject ); ?></h2>
				<?php
				if ( self::$ticket->is_active && self::$view_profile == 'agent' && self::has_ticket_cap( 'ctf' ) ) :
					?>
					<span onclick="wpsc_it_get_edit_subject(<?php echo esc_attr( self::$ticket->id ); ?>)"><?php WPSC_Icons::get( 'edit' ); ?></span>
					<?php
				endif
				?>
			</div>
			<?php
		}

		/**
		 * Displayed only on sm and xs screens
		 *
		 * @return void
		 */
		public static function get_mobile_widgets() {
			?>
			<div class="wpsc-it-body-item wpsc-it-mobile-widget-container wpsc-visible-xs wpsc-visible-sm">
				<div class="wpsc-it-mob-widget-trigger-btn" onclick="wpsc_toggle_mob_it_widgets();">
					<h2><?php esc_attr_e( 'Ticket Details', 'supportcandy' ); ?></h2>
					<span class="down"><?php WPSC_Icons::get( 'chevron-down' ); ?></span>
					<span class="up" style="display:none;"><?php WPSC_Icons::get( 'chevron-up' ); ?></span>
				</div>
				<div class="wpsc-it-mob-widgets-inner-container" data-status="0" style="display: none;">
					<?php echo self::$widget_html; // phpcs:ignore?>
				</div>
			</div>
			<?php
		}

		/**
		 * Get reply section of individual tickets
		 *
		 * @return void
		 */
		public static function get_reply_section() {

			// Return if ticket is not active (deleted).
			if ( ! self::$ticket->is_active ) {
				return;
			}

			$current_user = WPSC_Current_User::$current_user;

			// URL authentication.
			if ( self::$url_auth && ! $current_user->is_customer ) {
				$gs = get_option( 'wpsc-gs-general' );
				$page_settings = get_option( 'wpsc-gs-page-settings' );
				?>
				<div class="wpsc_url_auth_sign_in wpsc-widget-body">
					<p class="wpsc_auth_header"><?php esc_attr_e( 'You must sign in to submit a reply', 'supportcandy' ); ?></p>
					<p class="wpsc_auth_link wpsc-signin-customer">
						<?php
							printf(
								/* translators: %s: Sign in */
								esc_attr__( '%s using email and password (registered user)', 'supportcandy' ),
								'<a class="wpsc-link" href="javascript:wpsc_user_sign_in();">' . esc_attr__( 'Sign in', 'supportcandy' ) . '</a>'
							);
						?>
					</p>
					<?php
					if ( $page_settings['otp-login'] && in_array( 'guest', $gs['allow-create-ticket'] ) ) {
						?>
						<p class="wpsc_auth_link wpsc-signin-guest">
							<?php
								printf(
									/* translators: %s: Sign in */
									esc_attr__( '%s using email and one time password (guest user)', 'supportcandy' ),
									'<a class="wpsc-link" href="javascript:wpsc_get_guest_sign_in();">' . esc_attr__( 'Sign in', 'supportcandy' ) . '</a>'
								);
							?>
						</p>
						<?php
					}
					?>
				</div>
				<script>

					/**
					 * Open sign in form
					 *
					 * @return void
					 */
					function wpsc_user_sign_in() {
						var url = new URL(window.location.href);
						var search_params = url.searchParams;
						search_params.delete('auth_code');
						search_params.delete('auth-code');
						url.search = search_params.toString();
						window.location.href = url.toString();
					}

					/**
					 * Get guest otp login screen
					 *
					 * @return void
					 */
					function wpsc_get_guest_sign_in() {
						jQuery('.wpsc-body').remove();
						jQuery('.wpsc-shortcode-container').html('<div class="wpsc-auth-container"><div class="auth-inner-container"></div></div>');
						jQuery('.auth-inner-container').html(supportcandy.loader_html);
						var data = { action: 'wpsc_get_guest_sign_in' };
						jQuery.post(supportcandy.ajax_url, data, function (res) {
							if (typeof(res) == "object") {
								alert(supportcandy.translations.something_wrong);
								window.location.reload();
							} else {
								jQuery('.auth-inner-container').html(res);
							}
						});
					}

					/**
					 * Send login OTP
					 *
					 * @return void
					 */
					function wpsc_authenticate_guest_login(el) {

						var dataform = new FormData(jQuery(el).closest('form')[0]);

						var email_address = dataform.get('email_address').trim();
						if ( ! email_address ) {
							alert(supportcandy.translations.req_fields_missing);
							return;
						}

						if (!validateEmail(email_address)) {
							alert(supportcandy.translations.incorrect_email);
							return;
						}

						jQuery(el).text(supportcandy.translations.please_wait);
						jQuery.ajax({
							url: supportcandy.ajax_url,
							type: 'POST',
							data: dataform,
							processData: false,
							contentType: false
						}).done(function (res) {
							if (typeof(res) == "object") {
								alert(supportcandy.translations.something_wrong);
								wpsc_get_guest_sign_in();
							} else {
								jQuery('.auth-inner-container').html(res);
							}
						}).fail(function (res) {
							alert(supportcandy.translations.something_wrong);
							window.location.reload();
						});
					}

					/**
					 * Confirm guest login auth
					 *
					 * @return void
					 */
					function wpsc_confirm_guest_login(el) {

						jQuery('.auth-inner-container').html(supportcandy.loader_html);
						var dataform = new FormData(jQuery(el).closest('form')[0]);
						jQuery.ajax({
							url: supportcandy.ajax_url,
							type: 'POST',
							data: dataform,
							processData: false,
							contentType: false
						}).done(function (res) {
							if (res.isSuccess == 1) {
								window.location.reload();
							} else {
								alert(supportcandy.translations.something_wrong);
								wpsc_get_guest_sign_in();
							}
						});
					}
				</script>
				<?php
				return;
			}

			$flag = false;

			if ( self::$view_profile == 'agent' && self::$submit_actions ) {
				$flag = true;
			}

			if ( ! $flag && self::is_customer() ) {
				$flag = true;
			}

			$gs          = get_option( 'wpsc-gs-general' );
			$ms_advanced = get_option( 'wpsc-ms-advanced-settings' );

			if (
				self::$ticket->status->id == $gs['close-ticket-status'] &&
				(
					( $current_user->is_agent && ! in_array( 'agent', $ms_advanced['allow-reply-to-close-ticket'] ) ) ||
					( ! $current_user->is_agent && ! in_array( 'customer', $ms_advanced['allow-reply-to-close-ticket'] ) )
				)
			) {
				$flag = false;
			}

			if ( ! $flag ) {
				return;
			}

			// Set reply profile.
			self::$reply_profile = 'customer';
			if ( self::$view_profile == 'agent' && self::has_ticket_cap( 'reply' ) ) {
				self::$reply_profile = 'agent';
			}

			$advanced = get_option( 'wpsc-ms-advanced-settings' );
			$tl_advanced = get_option( 'wpsc-tl-ms-advanced' );

			// check reply permission for public tickets.
			if ( ! $current_user->is_agent && $advanced['public-mode'] && ! $advanced['public-mode-reply'] && self::$ticket->customer->id != $current_user->customer->id ) {
				return;
			}

			$unique_id = uniqid( 'wpsc_' );

			$saved_reply = '';
			if ( isset( $_COOKIE['wpsc_auto_save_reply'] ) ) {
				$cookie = json_decode( sanitize_text_field( wp_unslash( $_COOKIE['wpsc_auto_save_reply'] ) ), true );
				$saved_reply = array_key_exists( self::$ticket->id, $cookie ) ? $cookie[ self::$ticket->id ] : '';
			}
			?>
			<div class="wpsc-it-body-item wpsc-it-reply-section-container">
				<form action="#" class="wpsc-reply-section" onsubmit="return false;">
					<textarea class="wpsc_textarea" id="description"><?php echo wp_kses_post( $saved_reply ); ?></textarea>
					<?php
					if ( $advanced['allow-cc'] ) :
						?>
						<input id="wpsc-it-cc" name="cc" type="text" style="border:none !important;" placeholder="<?php esc_attr_e( 'CC (comma separated list)', 'supportcandy' ); ?>" autocomplete="off">
						<?php
					endif;
					if ( $advanced['allow-bcc'] ) :
						?>
						<input id="wpsc-it-bcc" name="bcc" type="text" style="border:none !important;" placeholder="<?php esc_attr_e( 'BCC (comma separated list)', 'supportcandy' ); ?>" autocomplete="off">
						<?php
					endif;
					?>
					<input 
						class="<?php echo esc_attr( $unique_id ); ?>" 
						type="file" 
						onchange="wpsc_set_attach_multiple(this, '<?php echo esc_attr( $unique_id ); ?>', 'description_attachments')" 
						multiple
						style="display: none;"/>
					<div class="wpsc-it-editor-action-container">
						<div class="actions">
							<div class="wpsc-editor-actions">
								<?php
								if ( WPSC_Text_Editor::is_allow_attachments() ) :
									?>
									<span class="wpsc-link" onclick="wpsc_trigger_desc_attachments('<?php echo esc_attr( $unique_id ); ?>');"><?php esc_attr_e( 'Attach Files', 'supportcandy' ); ?></span>
									<?php
								endif;
								if ( $current_user->is_agent ) :
									?>
									<span class="wpsc-link" onclick="wpsc_get_macros()"><?php esc_attr_e( 'Insert Macro', 'supportcandy' ); ?></span>
									<?php
								endif;
								do_action( 'wpsc_it_editor_actions' );
								?>
							</div>
							<?php
							if ( WPSC_Text_Editor::is_attachment_notice() && WPSC_Text_Editor::is_allow_attachments() ) :
								?>
								<div class="wpsc-file-attachment-notice"><?php echo esc_attr( WPSC_Text_Editor::file_attachment_notice_text() ); ?></div>
								<?php
							endif;
							?>
							<div class="<?php echo esc_attr( $unique_id ); ?> wpsc-editor-attachment-container"></div>
						</div>
						<div class="submit-container">
							<?php

							if ( self::$view_profile == 'agent' ) {
								?>

								<button 
									id="wpsc-it-editor-submit" 
									class="wpsc-it-editor-submit wpsc-button normal primary" 
									type="button" 
									data-popover="wpsc-it-submit-actions">
									<?php esc_attr_e( 'SUBMIT AS', 'supportcandy' ); ?>
								</button>
								<div id="wpsc-it-submit-actions" class="gpopover wpsc-popover-menu">
									<?php
									foreach ( self::$submit_actions as $action ) :
										?>
										<div class="wpsc-popover-menu-item" onclick="<?php echo esc_attr( $action['callback'] ); ?>">
											<?php WPSC_Icons::get( $action['icon'] ); ?>
											<span><?php echo esc_attr( $action['label'] ); ?></span>
										</div>
										<?php
									endforeach;
									?>
								</div>
								<script>jQuery('#wpsc-it-editor-submit').gpopover({width: 200});</script>
								<?php

							} else {
								?>

								<button 
									id="wpsc-it-editor-submit" 
									class="wpsc-it-editor-submit wpsc-button normal primary" 
									type="button" 
									onclick="wpsc_it_add_reply(<?php echo esc_attr( self::$ticket->id ); ?>)">
									<?php esc_attr_e( 'Submit', 'supportcandy' ); ?>
								</button>
								<?php
								if ( (
									$current_user->is_customer &&
									( $current_user->customer->id == self::$ticket->customer->id && in_array( 'customer', $gs['allow-close-ticket'] ) ) )
									&&
									! (
										self::$ticket->status->id == $gs['close-ticket-status'] ||
										in_array( self::$ticket->status->id, $tl_advanced['closed-ticket-statuses'] )
									)
								) {
									?>
									<button 
										class="wpsc-it-editor-submit wpsc-button normal primary" 
										type="button" 
										onclick="wpsc_it_reply_and_close(<?php echo esc_attr( self::$ticket->id ); ?>, '<?php echo esc_attr( wp_create_nonce( 'wpsc_it_reply_and_close' ) ); ?>')">
										<?php esc_attr_e( 'Reply & Close', 'supportcandy' ); ?>
									</button>
									<?php
								}
							}
							?>

						</div>
					</div>
					<?php
					$recaptcha = get_option( 'wpsc-recaptcha-settings' );
					if ( $recaptcha['allow-recaptcha'] === 1 && $recaptcha['recaptcha-version'] == 3 && $recaptcha['recaptcha-site-key'] && $recaptcha['recaptcha-secret-key'] ) {
						?>
						<script src="https://www.google.com/recaptcha/api.js?render=<?php echo esc_attr( $recaptcha['recaptcha-site-key'] ); ?>"></script> <?php // phpcs:ignore ?>
						<?php
					}
					?>
				</form>
			</div>
			<script>
				<?php
				WPSC_Text_Editor::print_editor_init_scripts( 'description', 'wpsc-description' );
				do_action( 'wpsc_js_it_functions' );
				?>

				/**
				* add new reply to ticket 
				* @param {*} ticket_id 
				*/
				function wpsc_it_add_reply(ticket_id) {

					var is_tinymce = (typeof tinyMCE != "undefined") && tinyMCE.activeEditor && !tinyMCE.activeEditor.isHidden();
					var description = is_tinymce && tinymce.get('description') ? tinyMCE.get('description').getContent() : jQuery('#description').val().trim();
					if (!description) return;

					<?php if ( $advanced['reply-confirmation'] ) : ?>
						var flag = confirm(supportcandy.translations.confirm);
						if (!flag) return;
					<?php endif; ?>

					var form = jQuery('form.wpsc-reply-section')[0];
					var dataform = new FormData(form);
					dataform.append('ticket_id', ticket_id);
					dataform.append('description', description);
					dataform.append('is_editor', isWPSCEditor);
					dataform.append('action', 'wpsc_it_add_reply');
					dataform.append('_ajax_nonce', supportcandy.nonce);
					jQuery('.wpsc-body').html(supportcandy.loader_html);
					jQuery.ajax({
						url: supportcandy.ajax_url,
						type: 'POST',
						data: dataform,
						processData: false,
						contentType: false
					}).done(function (res) {
						if (is_tinymce) {
							tinyMCE.get('description').setContent('');
						}
						wpsc_run_ajax_background_process();
						wpsc_after_ticket_reply(ticket_id);
					});
				}

				/**
				 * Add private note
				 */
				function wpsc_it_add_private_note(ticket_id) {

					var is_tinymce = (typeof tinyMCE != "undefined") && tinyMCE.activeEditor && !tinyMCE.activeEditor.isHidden();
					var description = is_tinymce && tinymce.get('description') ? tinyMCE.get('description').getContent() : jQuery('#description').val().trim();
					if (!description) return;

					var form = jQuery('form.wpsc-reply-section')[0];
					var dataform = new FormData(form);
					dataform.append('ticket_id', ticket_id);
					dataform.append('description', description);
					dataform.append('is_editor', isWPSCEditor);
					dataform.append('action', 'wpsc_it_add_note');
					dataform.append('_ajax_nonce', supportcandy.nonce);
					jQuery('.wpsc-body').html(supportcandy.loader_html);
					jQuery.ajax({
						url: supportcandy.ajax_url,
						type: 'POST',
						data: dataform,
						processData: false,
						contentType: false
					}).done(function (res) {
						if (is_tinymce) {
							tinyMCE.get('description').setContent('');
						}
						wpsc_get_individual_ticket(ticket_id);
						wpsc_run_ajax_background_process();
					});
				}

				/**
				 * Reply & close ticket
				 */
				function wpsc_it_reply_and_close(ticket_id, nonce){

					var is_tinymce = (typeof tinyMCE != "undefined") && tinyMCE.activeEditor && !tinyMCE.activeEditor.isHidden();
					var description = is_tinymce && tinymce.get('description') ? tinyMCE.get('description').getContent() : jQuery('#description').val().trim();
					if (!description) {
						confirm(supportcandy.translations.req_fields_missing);
						return;
					}

					<?php if ( $advanced['reply-confirmation'] ) : ?>
						var flag = confirm(supportcandy.translations.confirm);
						if (!flag) return;
					<?php endif; ?>

					var form = jQuery('form.wpsc-reply-section')[0];
					var dataform = new FormData(form);
					dataform.append('ticket_id', ticket_id);
					dataform.append('description', description);
					dataform.append('is_editor', isWPSCEditor);
					dataform.append('action', 'wpsc_it_reply_and_close');
					dataform.append('_ajax_nonce', nonce);
					jQuery('.wpsc-body').html(supportcandy.loader_html);
					jQuery.ajax({
						url: supportcandy.ajax_url,
						type: 'POST',
						data: dataform,
						processData: false,
						contentType: false
					}).done(function (res) {
						if (is_tinymce) {
							tinyMCE.get('description').setContent('');
						}
						wpsc_run_ajax_background_process();
						wpsc_after_ticket_reply(ticket_id);
						wpsc_after_close_ticket(ticket_id);
						if ( jQuery.isFunction(jQuery.fn.wpsc_get_ticket_list) ) { wpsc_get_ticket_list();}
					});
				}

				// auto-save
				wpsc_auto_save(<?php echo esc_attr( self::$ticket->id ); ?>);
				function wpsc_auto_save( ticket_id ) {
					current_ticket = jQuery('#wpsc-current-ticket').val();
					if( current_ticket != ticket_id ){
						return;
					}
					setTimeout(() => {
						wpsc_auto_save( ticket_id );
					}, 10000);

					var is_tinymce = (typeof tinyMCE != "undefined") && tinyMCE.activeEditor && !tinyMCE.activeEditor.isHidden();
					var description = is_tinymce && tinymce.get('description') ? tinyMCE.get('description').getContent() : jQuery('#description').val().trim();
					if (!description) return;
					var data = { action: 'wpsc_auto_save', description, ticket_id };
					jQuery.post( supportcandy.ajax_url, data );
				}

				jQuery('.wpsc_textarea').keyup(function() {
					if( jQuery(this).val() == '' ) {
						ticket_id = jQuery('#wpsc-current-ticket').val();
						wpsc_clear_saved_draft_reply( ticket_id );
					}
				});
			</script>
			<?php
		}

		/**
		 * Get threads section of individual ticket
		 *
		 * @return void
		 */
		public static function get_thread_section() {

			$gs      = get_option( 'wpsc-gs-general' );
			$filters = array(
				'meta_query' => array(
					'relation' => 'AND',
					array(
						'slug'    => 'ticket',
						'compare' => '=',
						'val'     => self::$ticket->id,
					),
				),
				'orderby'    => 'id',
				'order'      => 'DESC',
			);

			$thread_types = array(
				'slug'    => 'type',
				'compare' => 'IN',
				'val'     => array( 'report', 'reply' ),
			);

			if ( self::$view_profile == 'agent' && self::has_ticket_cap( 'pn' ) ) {
				$thread_types['val'][] = 'note';
			}
			if ( self::$view_profile == 'agent' && self::has_ticket_cap( 'vl' ) ) {
				$thread_types['val'][] = 'log';
			}

			$filters['meta_query'][] = $thread_types;

			$response = WPSC_Thread::find( $filters );
			$last_id  = $response['results'][ count( $response['results'] ) - 1 ]->id;

			if ( $response['has_next_page'] && $gs['reply-form-position'] == 'bottom' ) :
				?>
				<div class="wpsc-it-body-item" style="display: flex; justify-content:center; margin-bottom: 50px;">
					<button class="wpsc-button small secondary" onclick="wpsc_load_older_threads(this, <?php echo esc_attr( self::$ticket->id ); ?>);"><?php esc_attr_e( 'Load older communications', 'supportcandy' ); ?></button>
				</div>
				<?php
			endif;
			?>

			<div class="wpsc-it-body-item wpsc-it-thread-section-container">
			<?php
			if ( $gs['reply-form-position'] == 'top' ) {
				foreach ( $response['results'] as $thread ) {
					if ( $thread->type == 'log' ) {
						self::print_log( $thread );
					} else {
						self::print_thread( $thread );
					}
				}
			} else {
				for ( $i = count( $response['results'] ) - 1; $i >= 0; $i-- ) {
					$thread = $response['results'][ $i ];
					if ( $thread->type == 'log' ) {
						self::print_log( $thread );
					} else {
						self::print_thread( $thread );
					}
				}
			}
			?>
			</div>

			<script>

				jQuery(document).find('.thread-text').each(function(){
					var height = parseInt(jQuery(this).height());
					<?php
					$advanced = get_option( 'wpsc-ms-advanced-settings', array() );
					if ( $advanced['view-more'] ) {
						?>
						if( height > 100){
							jQuery(this).height(100);
							jQuery(this).parent().find('.wpsc-ticket-thread-expander').text(supportcandy.translations.view_more);
							jQuery(this).parent().find('.wpsc-ticket-thread-expander').show();
						}
						<?php
					} else {
						?>
						jQuery(this).parent().find('.thread-text').height('auto');
						<?php
					}
					?>

				});

				supportcandy.threads = {last_thread: <?php echo esc_attr( $last_id ); ?>}
			</script>
			<?php

			if ( $response['has_next_page'] && $gs['reply-form-position'] == 'top' ) :
				?>
				<div class="wpsc-it-body-item" style="display: flex; justify-content:center; margin-bottom: 50px;">
					<button class="wpsc-button small secondary" onclick="wpsc_load_older_threads(this, <?php echo esc_attr( self::$ticket->id ); ?>);"><?php esc_attr_e( 'Load older communications', 'supportcandy' ); ?></button>
				</div>
				<?php
			endif;
		}

		/**
		 * Print thread
		 *
		 * @param WPSC_Thread $thread - thread object.
		 * @return void
		 */
		public static function print_thread( $thread ) {

			// If thread is of type "Private Note", return if current user does not have permission to view logs.
			if ( $thread->type == 'note' && ! ( self::$view_profile == 'agent' && self::has_ticket_cap( 'pn' ) ) ) {
				return;
			}

			// If thread is deleted, show only if current user has view log permission. It will show deleteted log with view content link.
			if ( ! $thread->is_active && ! ( self::$view_profile == 'agent' && self::has_ticket_cap( 'vl' ) ) ) {
				return;
			}

			$settings     = get_option( 'wpsc-gs-general' );
			$current_user = WPSC_Current_User::$current_user;
			$advanced     = get_option( 'wpsc-ms-advanced-settings', array() );

			$now           = new DateTime();
			$date          = $thread->date_created->setTimezone( wp_timezone() );
			$time_diff_str = WPSC_Functions::date_interval_highest_unit_ago( $date->diff( $now ) );
			$time_title    = wp_date( $advanced['thread-date-format'], $thread->date_created->setTimezone( wp_timezone() )->getTimestamp() );
			$time_date_str = wp_date( $advanced['thread-date-format'], $thread->date_created->setTimezone( wp_timezone() )->getTimestamp() );
			$time_str      = $advanced['thread-date-display-as'] == 'date' ? $time_date_str : $time_diff_str;

			if (
				! is_object( $thread->seen ) &&
				$current_user->customer == $thread->ticket->customer &&
				in_array( $thread->type, array( 'report', 'reply' ) )
			) {

				$today        = new DateTime();
				$thread->seen = $today->format( 'Y-m-d H:i:s' );
				$thread->save();
			}

			$classes = $thread->type == 'note' ? 'note' : 'reply';
			if ( in_array( $thread->type, array( 'report', 'reply' ) ) ) {
				$thread_user = get_user_by( 'email', $thread->customer->email );
				$user_class = $thread_user && $thread_user->has_cap( 'wpsc_agent' ) ? 'agent' : 'customer';
				$classes = $classes . ' ' . $user_class;
			}
			?>

			<div class="wpsc-thread <?php echo esc_attr( $classes ); ?> <?php echo esc_attr( $thread->id ); ?>">

				<div class="thread-avatar">
					<?php echo get_avatar( $thread->customer->email, 32 ); ?>
				</div>

				<div class="thread-body">

					<div class="thread-header">

						<div class="user-info">
							<div style="display: flex;">
								<h2 class="user-name"><?php echo esc_attr( $thread->customer->name ) . ' '; ?></h2>
								<h2>
									<small class="thread-type">
										<i>
											<?php
											switch ( $thread->type ) {

												case 'report':
													esc_attr_e( 'reported', 'supportcandy' );
													break;

												case 'reply':
													esc_attr_e( 'replied', 'supportcandy' );
													break;

												case 'note':
													esc_attr_e( 'added a note', 'supportcandy' );
													break;
											}
											?>
										</i>
									</small>
								</h2>
							</div>
							<span class="thread-time" title="<?php echo esc_attr( $time_title ); ?>"><?php echo esc_attr( $time_str ); ?></span>
						</div>
						<?php

						if ( $thread->is_active ) {
							?>
							<div class="actions">
								<?php
								foreach ( self::$thread_actions as $action ) :
									?>
									<span title="<?php echo esc_attr( $action['label'] ); ?>" onclick="<?php echo esc_attr( $action['callback'] ) . '(this, ' . esc_attr( self::$ticket->id ) . ',' . esc_attr( $thread->id ) . ', \'' . esc_attr( wp_create_nonce( $action['callback'] ) ) . '\')'; ?>">
										<?php WPSC_Icons::get( $action['icon'] ); ?>
									</span>
									<?php
								endforeach;
								?>
							</div>
							<?php
						}
						?>

					</div>

					<div class="thread-text">
						<?php
						if ( $thread->is_active ) {
							echo wp_kses_post( $thread->body );
						} else {
							$logs = $thread->get_logs();
							?>
							<i>
								<?php
								printf(
									/* translators: %1$s: customer name, %2$s: datetime */
									esc_attr__( 'This thread was deleted by %1$s on %2$s.', 'supportcandy' ),
									'<strong>' . esc_attr( $logs[0]->modified_by->name ) . '</strong>',
									esc_attr( $logs[0]->date_created->setTimezone( wp_timezone() )->format( $advanced['thread-date-format'] ) )
								);
								?>
								<a href="javascript:wpsc_it_view_deleted_thread(<?php echo intval( self::$ticket->id ); ?>, <?php echo intval( $thread->id ); ?>, '<?php echo esc_attr( wp_create_nonce( 'wpsc_it_view_deleted_thread' ) ); ?>')">
									<?php esc_attr_e( 'View thread!', 'supportcandy' ); ?>
								</a>
							</i>
							<?php
						}
						?>
					</div>

					<?php
					if ( $advanced['view-more'] ) {
						?>
						<div class="wpsc-ticket-thread-expander" onclick="wpsc_ticket_thread_expander_toggle(this);" style="display: none;">
							<?php esc_attr_e( 'View More ...', 'supportcandy' ); ?>
						</div>
						<?php
					}

					// Thread attachments.
					if ( $thread->is_active && $thread->attachments ) {
						?>
						<div class="wpsc-thread-attachments">
							<div class="wpsc-attachment-header"><?php esc_attr_e( 'Attachments:', 'supportcandy' ); ?></div>
							<?php
							foreach ( $thread->attachments as $attachment ) {
								?>
								<div class="wpsc-attachment-item">
									<?php
									$download_url = site_url( '/' ) . '?wpsc_attachment=' . $attachment->id . '&auth_code=' . $thread->ticket->auth_code;
									?>
									<a class="wpsc-link" href="<?php echo esc_attr( $download_url ); ?>" target="_blank">
									<span class="wpsc-attachment-name"><?php echo esc_attr( $attachment->name ); ?></span></a>
								</div>
								<?php
							}
							?>
						</div>
						<?php
					}

					// Thread logs.
					$logs = $thread->get_logs();
					if ( $thread->is_active && self::$view_profile == 'agent' && self::has_ticket_cap( 'vl' ) && $logs ) {
						?>
						<div class="wpsc-thread-logs">
							<?php
							foreach ( $logs as $log ) {

								$log_body = json_decode( $log->body );
								?>
								<div class="wpsc-thread-log-item">
									<?php
									switch ( $log_body->type ) {

										case 'modify':
											printf(
												/* translators: %1$s: customer name, %2$s: date time */
												esc_attr__( 'Modified by %1$s on %2$s.', 'supportcandy' ),
												'<strong>' . esc_attr( $log->modified_by->name ) . '</strong>',
												esc_attr( $log->date_created->setTimezone( wp_timezone() )->format( $advanced['thread-date-format'] ) )
											);
											?>
											<a href="javascript:wpsc_it_view_thread_log(<?php echo intval( self::$ticket->id ); ?>, <?php echo intval( $thread->id ); ?>, <?php echo intval( $log->id ); ?>, '<?php echo esc_attr( wp_create_nonce( 'wpsc_it_view_thread_log' ) ); ?>');" class="wpsc-link">
												<?php esc_attr_e( 'View change', 'supportcandy' ); ?>
											</a>
											<?php
											break;

										case 'delete':
											printf(
												/* translators: %1$s: customer name, %2$s: date time */
												esc_attr__( 'Deleted by %1$s on %2$s', 'supportcandy' ),
												'<strong>' . esc_attr( $log->modified_by->name ) . '</strong>',
												esc_attr( $log->date_created->setTimezone( wp_timezone() )->format( $advanced['thread-date-format'] ) )
											);
											break;

										case 'restore':
											printf(
												/* translators: %1$s: customer name, %2$s: date time */
												esc_attr__( 'Restored by %1$s on %2$s', 'supportcandy' ),
												'<strong>' . esc_attr( $log->modified_by->name ) . '</strong>',
												esc_attr( $log->date_created->setTimezone( wp_timezone() )->format( $advanced['thread-date-format'] ) )
											);
											break;
									}
									?>
								</div>
								<?php
							}
							?>
						</div>
						<?php
					}
					?>

				</div>

			</div>
			<?php
		}

		/**
		 * Print thread log
		 *
		 * @param WPSC_Thread $thread - thread object.
		 * @return void
		 */
		public static function print_log( $thread ) {

			if ( ! ( self::$view_profile == 'agent' && self::has_ticket_cap( 'vl' ) ) ) {
				return;
			}

			$advanced      = get_option( 'wpsc-ms-advanced-settings', array() );
			$now           = new DateTime();
			$date          = $thread->date_created->setTimezone( wp_timezone() );
			$time_diff_str = WPSC_Functions::date_interval_highest_unit_ago( $date->diff( $now ) );
			$title         = wp_date( $advanced['thread-date-format'], $thread->date_created->setTimezone( wp_timezone() )->getTimestamp() );
			$time_date_str = wp_date( $advanced['thread-date-format'], $thread->date_created->setTimezone( wp_timezone() )->getTimestamp() );
			$time_str      = $advanced['thread-date-display-as'] == 'date' ? $time_date_str : $time_diff_str;

			$body    = json_decode( $thread->body );
			$is_json = ( json_last_error() == JSON_ERROR_NONE ) ? true : false;

			if ( $is_json ) {

				$cf = WPSC_Custom_Field::get_cf_by_slug( $body->slug );
				if ( ! $cf ) {
					return;
				}
				?>
				<div class="wpsc-thread log">
					<div class="thread-avatar">
						<?php
						if ( $thread->customer ) {
							echo get_avatar( $thread->customer->email, 32 );
						} else {
							WPSC_Icons::get( 'system' );
						}
						?>
					</div>
					<div class="thread-body">
						<div class="thread-header">
							<div class="user-info">
								<div>
									<?php
									if ( $thread->customer ) {

										printf(
											/* translators: %1$s: User Name, %2$s: Field Name */
											esc_attr__( '%1$s changed the %2$s', 'supportcandy' ),
											'<strong>' . esc_attr( $thread->customer->name ) . '</strong>',
											'<strong>' . esc_attr( $cf->name ) . '</strong>'
										);

									} else {

										printf(
											/* translators: %1$s: Field Name */
											esc_attr__( 'The %1$s has been changed', 'supportcandy' ),
											'<strong>' . esc_attr( $cf->name ) . '</strong>'
										);
									}
									?>
								</div>
								<span class="thread-time" title="<?php echo esc_attr( $title ); ?>"><?php echo esc_attr( $time_str ); ?></span>
							</div>
						</div>
						<div class="wpsc-log-diff">
							<div class="lhs"><?php $cf->type::print_val( $cf, $body->prev ); ?></div>
							<div class="transform-icon">
								<?php
								if ( is_rtl() ) {
									WPSC_Icons::get( 'arrow-left' );
								} else {
									WPSC_Icons::get( 'arrow-right' );
								}
								?>
							</div>
							<div class="rhs"><?php $cf->type::print_val( $cf, $body->new ); ?></div>
						</div>
					</div>
				</div>
				<?php

			} else {

				?>
				<div class="wpsc-thread log">
					<div class="thread-avatar">
						<?php
							WPSC_Icons::get( 'system' );
						?>
					</div>
					<div class="thread-body">
						<div><?php echo wp_kses_post( $thread->body ); ?></div>
						<span class="thread-time" title="<?php echo esc_attr( $title ); ?>"><?php echo esc_attr( $time_str ); ?></span>
					</div>
				</div>
				<?php
			}
		}

		/**
		 * Check whether current user is customer or not
		 *
		 * @return boolean
		 */
		public static function is_customer() {

			$current_user = WPSC_Current_User::$current_user;
			if ( ! $current_user->is_customer ) {
				return false;
			}

			$adv_setting = get_option( 'wpsc-ms-advanced-settings' );
			if ( $adv_setting['public-mode'] && ! $current_user->is_agent ) {
				return true;
			}

			$allowed_customers = apply_filters( 'wpsc_non_agent_ticket_customers_allowed', array( self::$ticket->customer->id ), self::$ticket );
			if ( in_array( $current_user->customer->id, $allowed_customers ) ) {
				return true;
			}

			return false;
		}

		/**
		 * Agent ticket access for cap
		 *
		 * @param string $cap - capability name.
		 * @return boolean
		 */
		public static function has_ticket_cap( $cap ) {

			$current_user = WPSC_Current_User::$current_user;

			$assigned_agents = array_map(
				fn ( $agent ) => $agent->id,
				self::$ticket->assigned_agent
			);

			$flag = false;
			if (
				(
					! self::$ticket->assigned_agent &&
					$current_user->agent->has_cap( $cap . '-unassigned' )
				) ||
				(
					in_array( $current_user->agent->id, $assigned_agents ) &&
					$current_user->agent->has_cap( $cap . '-assigned-me' )
				) ||
				(
					self::$ticket->assigned_agent &&
					! in_array( $current_user->agent->id, $assigned_agents ) &&
					$current_user->agent->has_cap( $cap . '-assigned-others' )
				)
			) {
				$flag = true;
			}

			return apply_filters( 'wpsc_it_has_ticket_cap', $flag, self::$ticket, $cap );
		}

		/**
		 * Post a reply to ticket
		 *
		 * @return void
		 */
		public static function add_reply() {

			if ( check_ajax_referer( 'general', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			$gs           = get_option( 'wpsc-gs-general' );
			$current_user = WPSC_Current_User::$current_user;
			self::load_current_ticket();

			if ( ! (
				( self::$view_profile == 'agent' && self::has_ticket_cap( 'reply' ) ) ||
				( self::$view_profile == 'customer' && self::is_customer() )
			) ) {
				wp_send_json_error( __( 'Unauthorized', 'supportcandy' ), 401 );
			}

			// Set reply profile.
			self::$reply_profile = 'customer';
			if ( self::$view_profile == 'agent' && self::has_ticket_cap( 'reply' ) ) {
				self::$reply_profile = 'agent';
			}

			// Do not allow if ticket is deleted.
			if ( ! self::$ticket->is_active ) {
				wp_send_json_error( 'Something went wrong!', 400 );
			}

			$advanced = get_option( 'wpsc-ms-advanced-settings' );

			// check reply permission for public tickets.
			if ( ! $current_user->is_agent && $advanced['public-mode'] && ! $advanced['public-mode-reply'] && self::$ticket->customer->id != $current_user->customer->id ) {
				wp_send_json_error( __( 'Unauthorized', 'supportcandy' ), 401 );
			}

			// description.
			$description = isset( $_POST['description'] ) ? wp_kses_post( wp_unslash( $_POST['description'] ) ) : '';
			if ( ! $description ) {
				wp_send_json_error( 'Something went wrong!', 401 );
			}

			// replace new line with br if no text editor.
			$is_editor   = isset( $_POST['is_editor'] ) ? intval( $_POST['is_editor'] ) : 0;
			$description = ! $is_editor ? nl2br( esc_html( $description ) ) : $description;

			// replace macros only when current user is an agent & check if agent has permission to view customer email address.
			if ( $current_user->is_agent && ! in_array( $current_user->agent->role, $gs['allow-ar-thread-email'] ) ) {
				$description = str_replace( '{{customer_email}}', '', $description );
			}
			$description = $current_user->is_agent ? WPSC_Macros::replace( $description, self::$ticket ) : $description;

			// set signature if agent.
			$signature = $current_user->is_agent ? $current_user->agent->get_signature() : '';
			if ( $signature ) {
				$description .= '<br>' . $signature;
			}

			// description attachments.
			$attachments = isset( $_POST['description_attachments'] ) ? array_filter( array_map( 'intval', $_POST['description_attachments'] ) ) : array();

			// submit reply.
			$thread = WPSC_Thread::insert(
				array(
					'ticket'      => self::$ticket->id,
					'customer'    => $current_user->customer->id,
					'type'        => 'reply',
					'body'        => $description,
					'attachments' => implode( '|', $attachments ),
					'ip_address'  => WPSC_DF_IP_Address::get_current_user_ip(),
					'source'      => 'browser',
					'os'          => WPSC_DF_OS::get_user_platform(),
					'browser'     => WPSC_DF_Browser::get_user_browser(),
				)
			);

			// activate description attachments.
			foreach ( $attachments as $id ) :
				$attachment            = new WPSC_Attachment( $id );
				$attachment->is_active = 1;
				$attachment->source    = 'reply';
				$attachment->source_id = $thread->id;
				$attachment->ticket_id = self::$ticket->id;
				$attachment->save();
			endforeach;

			// activate description editor img attachments.
			if ( preg_match_all( '/' . preg_quote( home_url( '/' ), '/' ) . '\?wpsc_attachment=(\d*)/', $description, $matches ) ) {
				foreach ( $matches[1] as $id ) {
					$attachment            = new WPSC_Attachment( $id );
					$attachment->is_active = 1;
					$attachment->source_id = $thread->id;
					$attachment->ticket_id = self::$ticket->id;
					$attachment->save();
				}
			}

			self::$ticket->date_updated  = new DateTime();
			self::$ticket->last_reply_on = new DateTime();
			self::$ticket->last_reply_by = $current_user->customer->id;
			self::$ticket->save();

			do_action( 'wpsc_post_reply', $thread );
			wp_die();
		}

		/**
		 * Post a note to ticket
		 *
		 * @return void
		 */
		public static function add_note() {

			if ( check_ajax_referer( 'general', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			$gs           = get_option( 'wpsc-gs-general' );
			$current_user = WPSC_Current_User::$current_user;
			self::load_current_ticket();

			if ( ! ( self::$view_profile == 'agent' && self::has_ticket_cap( 'pn' ) ) ) {
				wp_send_json_error( __( 'Unauthorized', 'supportcandy' ), 401 );
			}

			// Do not allow if ticket is deleted.
			if ( ! self::$ticket->is_active ) {
				wp_send_json_error( 'Something went wrong!', 400 );
			}

			// description.
			$description = isset( $_POST['description'] ) ? wp_kses_post( wp_unslash( $_POST['description'] ) ) : '';
			if ( ! $description ) {
				wp_send_json_error( 'Something went wrong!', 401 );
			}

			// replace new line with br if no text editor.
			$is_editor   = isset( $_POST['is_editor'] ) ? intval( $_POST['is_editor'] ) : 0;
			$description = ! $is_editor ? nl2br( esc_html( $description ) ) : $description;

			// check if agent has permission to view customer email address.
			if ( ! in_array( $current_user->agent->role, $gs['allow-ar-thread-email'] ) ) {
				$description = str_replace( '{{customer_email}}', '', $description );
			}

			// description attachments.
			$attachments = isset( $_POST['description_attachments'] ) ? array_filter( array_map( 'intval', $_POST['description_attachments'] ) ) : array();

			// submit note.
			$thread = WPSC_Thread::insert(
				array(
					'ticket'      => self::$ticket->id,
					'customer'    => $current_user->customer->id,
					'type'        => 'note',
					'body'        => WPSC_Macros::replace( $description, self::$ticket ),
					'attachments' => implode( '|', $attachments ),
					'ip_address'  => WPSC_DF_IP_Address::get_current_user_ip(),
					'source'      => 'browser',
					'os'          => WPSC_DF_OS::get_user_platform(),
					'browser'     => WPSC_DF_Browser::get_user_browser(),
				)
			);

			// activate description attachments.
			foreach ( $attachments as $id ) :
				$attachment            = new WPSC_Attachment( $id );
				$attachment->is_active = 1;
				$attachment->source    = 'note';
				$attachment->source_id = $thread->id;
				$attachment->ticket_id = self::$ticket->id;
				$attachment->save();
			endforeach;

			// activate description editor img attachments.
			if ( preg_match_all( '/' . preg_quote( home_url( '/' ), '/' ) . '\?wpsc_attachment=(\d*)/', $description, $matches ) ) {
				foreach ( $matches[1] as $id ) {
					$attachment            = new WPSC_Attachment( $id );
					$attachment->is_active = 1;
					$attachment->source_id = $thread->id;
					$attachment->ticket_id = self::$ticket->id;
					$attachment->save();
				}
			}

			self::$ticket->date_updated = new DateTime();
			self::$ticket->save();

			do_action( 'wpsc_submit_note', $thread );
			wp_die();
		}

		/**
		 * Post a reply & close the ticket
		 *
		 * @return void
		 */
		public static function it_reply_and_close() {

			if ( check_ajax_referer( 'wpsc_it_reply_and_close', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			$gs             = get_option( 'wpsc-gs-general' );
			$tl_advanced    = get_option( 'wpsc-tl-ms-advanced' );
			$advanced       = get_option( 'wpsc-ms-advanced-settings' );
			$current_user   = WPSC_Current_User::$current_user;
			self::load_current_ticket();

			if ( ! (
				( self::$view_profile == 'agent' && self::has_ticket_cap( 'reply' ) ) ||
				( self::$view_profile == 'customer' && self::is_customer() )
			) ) {
				wp_send_json_error( __( 'Unauthorized', 'supportcandy' ), 401 );
			}

			// Set reply profile.
			self::$reply_profile = 'customer';
			if ( self::$view_profile == 'agent' && self::has_ticket_cap( 'reply' ) ) {
				self::$reply_profile = 'agent';
			}

			// Do not allow if ticket is deleted.
			if ( ! self::$ticket->is_active ) {
				wp_send_json_error( 'Something went wrong!', 400 );
			}

			// check reply permission for public tickets.
			if ( ! $current_user->is_agent && $advanced['public-mode'] && ! $advanced['public-mode-reply'] && self::$ticket->customer->id != $current_user->customer->id ) {
				wp_send_json_error( __( 'Unauthorized', 'supportcandy' ), 401 );
			}

			// description.
			$description = isset( $_POST['description'] ) ? wp_kses_post( wp_unslash( $_POST['description'] ) ) : '';
			if ( ! $description ) {
				wp_send_json_error( 'Something went wrong!', 401 );
			}

			// status change permission validation.
			$close_flag = false;
			if ( $current_user->is_customer &&
				(
					( $current_user->customer->id == self::$ticket->customer->id && in_array( 'customer', $gs['allow-close-ticket'] ) ) ||
					( $current_user->is_agent && self::has_ticket_cap( 'cs' ) && in_array( $current_user->agent->role, $gs['allow-close-ticket'] ) )
				)
			) {
				$close_flag = true;
			}

			$close_flag = apply_filters( 'wpsc_it_action_close_flag', $close_flag, self::$ticket );

			if ( ! (
				$close_flag &&
				! (
					self::$ticket->status->id == $gs['close-ticket-status'] ||
					in_array( self::$ticket->status->id, $tl_advanced['closed-ticket-statuses'] )
				)
			) ) {
				wp_send_json_error( 'Unauthorized!', 401 );
			}

			// replace new line with br if no text editor.
			$is_editor   = isset( $_POST['is_editor'] ) ? intval( $_POST['is_editor'] ) : 0;
			$description = ! $is_editor ? nl2br( esc_html( $description ) ) : $description;

			// replace macros only when current user is an agent & check if agent has permission to view customer email address.
			if ( $current_user->is_agent && ! in_array( $current_user->agent->role, $gs['allow-ar-thread-email'] ) ) {
				$description = str_replace( '{{customer_email}}', '', $description );
			}
			$description = $current_user->is_agent ? WPSC_Macros::replace( $description, self::$ticket ) : $description;

			// set signature if agent.
			$signature = $current_user->is_agent ? $current_user->agent->get_signature() : '';
			if ( $signature ) {
				$description .= '<br>' . $signature;
			}

			// description attachments.
			$attachments = isset( $_POST['description_attachments'] ) ? array_filter( array_map( 'intval', $_POST['description_attachments'] ) ) : array();

			// submit reply.
			$thread = WPSC_Thread::insert(
				array(
					'ticket'      => self::$ticket->id,
					'customer'    => $current_user->customer->id,
					'type'        => 'reply',
					'body'        => $description,
					'attachments' => implode( '|', $attachments ),
					'ip_address'  => WPSC_DF_IP_Address::get_current_user_ip(),
					'source'      => 'browser',
					'os'          => WPSC_DF_OS::get_user_platform(),
					'browser'     => WPSC_DF_Browser::get_user_browser(),
				)
			);

			// activate description attachments.
			foreach ( $attachments as $id ) :
				$attachment            = new WPSC_Attachment( $id );
				$attachment->is_active = 1;
				$attachment->source    = 'reply';
				$attachment->source_id = $thread->id;
				$attachment->ticket_id = self::$ticket->id;
				$attachment->save();
			endforeach;

			// activate description editor img attachments.
			if ( preg_match_all( '/' . preg_quote( home_url( '/' ), '/' ) . '\?wpsc_attachment=(\d*)/', $description, $matches ) ) {
				foreach ( $matches[1] as $id ) {
					$attachment            = new WPSC_Attachment( $id );
					$attachment->is_active = 1;
					$attachment->source_id = $thread->id;
					$attachment->ticket_id = self::$ticket->id;
					$attachment->save();
				}
			}

			$prev = self::$ticket->status->id;

			self::$ticket->last_reply_on = new DateTime();
			self::$ticket->last_reply_by = $current_user->customer->id;
			self::$ticket->status = $gs['close-ticket-status'];
			self::$ticket->date_closed = new DateTime( 'now' );
			self::$ticket->date_updated  = new DateTime();
			self::$ticket->save();

			$thread->ticket = self::$ticket;

			remove_action( 'wpsc_post_reply', array( 'WPSC_GS_General', 'change_ticket_status' ) );

			do_action( 'wpsc_post_reply', $thread );

			remove_action( 'wpsc_change_ticket_status', array( 'WPSC_EN_Change_Ticket_Status', 'process_event' ), 200 );

			do_action( 'wpsc_change_ticket_status', self::$ticket, $prev, self::$ticket->status->id, $current_user->customer->id );

			wp_die();
		}

		/**
		 * Change status of individual ticket
		 *
		 * @param integer $prev - privious status.
		 * @param integer $new - new status.
		 * @param integer $customer_id - customer id.
		 * @return void
		 */
		public static function change_status( $prev, $new, $customer_id ) {

			$gs                   = get_option( 'wpsc-gs-general' );
			self::$ticket->status = $new;
			$tl_advanced          = get_option( 'wpsc-tl-ms-advanced' );
			if ( $new == $gs['close-ticket-status'] || in_array( $new, $tl_advanced['closed-ticket-statuses'] ) ) {
				self::$ticket->date_closed = new DateTime( 'now' );
			} else {
				self::$ticket->date_closed = '0000-00-00 00:00:00';
			}

			self::$ticket->date_updated = new DateTime();
			self::$ticket->save();
			do_action( 'wpsc_change_ticket_status', self::$ticket, $prev, $new, $customer_id );
		}

		/**
		 * Change category of individual ticket
		 *
		 * @param integer $prev - privious category.
		 * @param integer $new - new category.
		 * @param integer $customer_id - customer id.
		 * @return void
		 */
		public static function change_category( $prev, $new, $customer_id ) {

			self::$ticket->category     = $new;
			self::$ticket->date_updated = new DateTime();
			self::$ticket->save();
			do_action( 'wpsc_change_ticket_category', self::$ticket, $prev, $new, $customer_id );
		}

		/**
		 * Change priority of individual ticket
		 *
		 * @param integer $prev - privious priority.
		 * @param integer $new - new priority.
		 * @param integer $customer_id - customer id.
		 * @return void
		 */
		public static function change_priority( $prev, $new, $customer_id ) {

			self::$ticket->priority     = $new;
			self::$ticket->date_updated = new DateTime();
			self::$ticket->save();
			do_action( 'wpsc_change_ticket_priority', self::$ticket, $prev, $new, $customer_id );
		}

		/**
		 * Change customer
		 *
		 * @param WPSC_Customer $prev - privious customer.
		 * @param WPSC_Customer $new - new customer.
		 * @param integer       $customer_id - customer id.
		 * @return void
		 */
		public static function change_raised_by( $prev, $new, $customer_id ) {

			self::$ticket->customer     = $new;
			self::$ticket->date_updated = new DateTime();
			self::$ticket->save();
			do_action( 'wpsc_change_raised_by', self::$ticket, $prev, $new, $customer_id );
		}

		/**
		 * Change assignee
		 *
		 * @param array   $prev - privious assignee.
		 * @param array   $new - new assignee.
		 * @param integer $customer_id - customer id.
		 * @return void
		 */
		public static function change_assignee( $prev, $new, $customer_id ) {

			self::$ticket->assigned_agent = $new;
			self::$ticket->prev_assignee  = $prev;
			self::$ticket->date_updated   = new DateTime();
			self::$ticket->save();
			do_action( 'wpsc_change_assignee', self::$ticket, $prev, $new, $customer_id );
		}

		/**
		 * Get duplicate ticket modal
		 *
		 * @return void
		 */
		public static function get_duplicate_ticket() {

			if ( check_ajax_referer( 'wpsc_it_get_duplicate_ticket', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			self::load_current_ticket();
			$current_user = WPSC_Current_User::$current_user;
			if ( self::$view_profile != 'agent' ) {
				wp_send_json_error( 'Something went wrong!', 401 );
			}

			$ticket = self::$ticket;
			$title  = esc_attr__( 'Duplicate', 'supportcandy' );

			ob_start();
			?>
			<form action="#" onsubmit="return false;" class="duplicate">
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Subject', 'supportcandy' ); ?></label>
					</div>
					<input name="subject" type="text" value="<?php echo esc_attr( $ticket->subject ); ?>" autocomplete="off">
				</div>
				<?php do_action( 'wpsc_it_get_duplicate_ticket', $ticket ); ?>
				<input type="hidden" name="action" value="wpsc_it_set_duplicate_ticket">
				<input type="hidden" name="ticket_id" value="<?php echo esc_attr( $ticket->id ); ?>">
				<input type="hidden" name="_ajax_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wpsc_it_set_duplicate_ticket' ) ); ?>">
			</form>
			<?php
			$body = ob_get_clean();

			ob_start();
			?>
			<button class="wpsc-button small primary" onclick="wpsc_it_set_duplicate_ticket(this, <?php echo esc_attr( $ticket->id ); ?>);">
				<?php esc_attr_e( 'Submit', 'supportcandy' ); ?>
			</button>
			<button class="wpsc-button small secondary" onclick="wpsc_close_modal();">
				<?php esc_attr_e( 'Cancel', 'supportcandy' ); ?>
			</button>
			<?php
			do_action( 'wpsc_it_get_duplicate_ticket_footer', $ticket );
			$footer = ob_get_clean();

			$response = array(
				'title'  => $title,
				'body'   => $body,
				'footer' => $footer,
			);
			wp_send_json( $response );
		}

		/**
		 * Set duplicate ticket
		 *
		 * @return void
		 */
		public static function set_duplicate_ticket() {

			if ( check_ajax_referer( 'wpsc_it_set_duplicate_ticket', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			self::load_current_ticket();
			$current_user = WPSC_Current_User::$current_user;
			if ( self::$view_profile != 'agent' && ! self::has_ticket_cap( 'dt' ) ) {
				wp_send_json_error( 'Something went wrong!', 401 );
			}

			$ticket  = self::$ticket;
			$threads = $ticket->get_threads( 1, 0, array( 'report', 'reply' ), 'date_created', 'ASC' );

			// ignore custom field types while duplicating ticket.
			$ignore_cft = apply_filters(
				'wpsc_duplicate_ticket_ignore_cft',
				array(
					'cf_html',
					'df_description',
					'df_id',
					'df_ip_address',
					'df_browser',
					'df_assigned_agent',
					'df_prev_assignee',
					'df_date_closed',
					'df_add_recipients',
					'df_ip_address',
					'df_os',
					'df_sla',
					'df_last_reply_on',
					'df_last_reply_by',
					'df_sf_date',
					'df_sf_feedback',
					'df_sf_rating',
					'df_time_spent',
					'df_usergroups',
				)
			);

			$data = array();
			foreach ( WPSC_Custom_Field::$custom_fields as $cf ) {
				if ( ! in_array( $cf->field, array( 'ticket', 'agentonly' ) ) ) {
					continue;
				}
				if ( in_array( $cf->type::$slug, $ignore_cft ) ) {
					continue;
				}
				$data[ $cf->slug ] = $cf->type::get_duplicate_ticket_data( $cf, $ticket );
			}
			$data['assigned_agent'] = '';
			$duplicate = WPSC_Ticket::insert( $data );

			foreach ( $threads as $thread ) {
				$attachments = array();
				foreach ( $thread->attachments as $attachment ) {
					$attachments[] = self::get_duplicate_attachment_id( $attachment->id );
				}
				$duplicate_thread = WPSC_Thread::insert(
					array(
						'ticket'       => $duplicate->id,
						'customer'     => $thread->customer->id,
						'type'         => $thread->type,
						'body'         => $thread->body,
						'attachments'  => $attachments ? implode( '|', $attachments ) : '',
						'ip_address'   => $thread->ip_address,
						'source'       => $thread->source,
						'os'           => $thread->os,
						'browser'      => $thread->browser,
						'date_created' => $thread->date_created->format( 'Y-m-d H:i:s' ),
						'date_updated' => $thread->date_updated->format( 'Y-m-d H:i:s' ),
					)
				);
				foreach ( $attachments as $id ) :
					$attachment            = new WPSC_Attachment( $id );
					$attachment->source    = $duplicate_thread->type;
					$attachment->source_id = $duplicate_thread->id;
					$attachment->ticket_id = $duplicate->id;
					$attachment->save();
				endforeach;

				// duplicate description attachments.
				$body_attachments = array();
				$attachment_of_body = WPSC_Attachment::find(
					array(
						'items_per_page' => 0,
						'meta_query'     => array(
							'relation' => 'AND',
							array(
								'slug'    => 'source',
								'compare' => '=',
								'val'     => 'img_editor',
							),
							array(
								'slug'    => 'source_id',
								'compare' => '=',
								'val'     => $thread->id,
							),
						),
					)
				)['results'];
				foreach ( $attachment_of_body as $img ) {
					$body_attachments[ $img->id ] = self::get_duplicate_attachment_id( $img->id );
				}
				foreach ( $body_attachments as $old_id => $id ) :
					$attachment            = new WPSC_Attachment( $id );
					$attachment->source    = 'img_editor';
					$attachment->source_id = $duplicate_thread->id;
					$attachment->ticket_id = $duplicate->id;
					$attachment->save();

					$old_url = home_url( '/' ) . '?wpsc_attachment=' . $old_id;
					$new_url = home_url( '/' ) . '?wpsc_attachment=' . $id;
					$duplicate_thread->body = str_replace( $old_url, $new_url, $duplicate_thread->body );
				endforeach;

				$duplicate_thread->save();
			}

			do_action( 'wpsc_create_new_ticket', $duplicate );

			wp_send_json( array( 'ticket_id' => $duplicate->id ) );
		}

		/**
		 * Delete ticket ajax request
		 */
		public static function set_delete_ticket() {

			if ( check_ajax_referer( 'wpsc_it_delete_ticket', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			$current_user = WPSC_Current_User::$current_user;
			if ( ! $current_user->is_agent ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			self::load_current_ticket();

			if ( ! self::$ticket->is_active || ! self::has_ticket_cap( 'dtt' ) ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			self::delete_ticket();
			wp_die();
		}

		/**
		 * Delete current ticket
		 *
		 * @return void
		 */
		public static function delete_ticket() {

			self::$ticket->is_active = 0;
			self::$ticket->date_updated = new DateTime();
			self::$ticket->save();
			do_action( 'wpsc_delete_ticket', self::$ticket );
		}

		/**
		 * Restore ticket ajax request
		 *
		 * @return void
		 */
		public static function ticket_restore() {

			if ( check_ajax_referer( 'wpsc_it_ticket_restore', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			$current_user = WPSC_Current_User::$current_user;
			if ( ! $current_user->is_agent ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			self::load_current_ticket();

			if ( self::$ticket->is_active || ! self::has_ticket_cap( 'dtt' ) ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			self::restore_ticket();
			wp_die();
		}

		/**
		 * Restore current ticket
		 *
		 * @return void
		 */
		public static function restore_ticket() {

			self::$ticket->is_active = 1;
			self::$ticket->date_updated = new DateTime();
			self::$ticket->save();
			do_action( 'wpsc_ticket_restore', self::$ticket );
		}

		/**
		 * Permanently delete ticket ajax request
		 *
		 * @return void
		 */
		public static function set_delete_ticket_permanently() {

			if ( check_ajax_referer( 'wpsc_it_delete_permanently', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			$current_user = WPSC_Current_User::$current_user;
			if ( ! $current_user->is_agent ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			self::load_current_ticket();

			if ( self::$ticket->is_active || ! self::has_ticket_cap( 'dtt' ) ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			self::delete_permanently();
			wp_die();
		}

		/**
		 * Permanently delete current ticket
		 *
		 * @return void
		 */
		public static function delete_permanently() {

			WPSC_Ticket::destroy( self::$ticket );
			do_action( 'wpsc_ticket_delete_permanently', self::$ticket->id );
		}

		/**
		 * Create ticket from thread
		 *
		 * @return void
		 */
		public static function it_thread_new_ticket() {

			if ( check_ajax_referer( 'wpsc_it_thread_new_ticket', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			self::load_current_ticket();
			$current_user = WPSC_Current_User::$current_user;
			self::load_current_ticket();

			if ( !
				self::$view_profile == 'agent'
			) {
				wp_send_json_error( __( 'Unauthorized', 'supportcandy' ), 401 );
			}

			$ticket    = self::$ticket;
			$thread_id = isset( $_POST['thread_id'] ) ? intval( $_POST['thread_id'] ) : 0;
			$title     = esc_attr__( 'Create ticket from thread', 'supportcandy' );

			ob_start();
			?>
			<form action="#" onsubmit="return false;" class="new_ticket_from_thread">
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Subject', 'supportcandy' ); ?></label>
					</div>
					<input name="subject" type="text" value="<?php echo esc_attr( $ticket->subject ); ?>" autocomplete="off">
				</div>
				<?php do_action( 'wpsc_it_thread_new_ticket', $thread_id ); ?>
				<input type="hidden" name="action" value="wpsc_it_set_thread_new_ticket">
				<input type="hidden" name="ticket_id" value="<?php echo esc_attr( $ticket->id ); ?>">
				<input type="hidden" name="thread_id" value="<?php echo esc_attr( $thread_id ); ?>">
				<input type="hidden" name="_ajax_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wpsc_it_set_thread_new_ticket' ) ); ?>">
			</form>
			<?php
			$body = ob_get_clean();

			ob_start();
			?>
			<button class="wpsc-button small primary" onclick="wpsc_it_set_thread_new_ticket(this, <?php echo esc_attr( $thread_id ); ?>, '<?php echo esc_attr( wp_create_nonce( 'wpsc_it_set_thread_new_ticket' ) ); ?>');">
				<?php esc_attr_e( 'Submit', 'supportcandy' ); ?>
			</button>
			<button class="wpsc-button small secondary" onclick="wpsc_close_modal();">
				<?php esc_attr_e( 'Cancel', 'supportcandy' ); ?>
			</button>
			<?php
			do_action( 'wpsc_it_set_thread_new_ticket_footer', $thread_id );
			$footer = ob_get_clean();

			$response = array(
				'title'  => $title,
				'body'   => $body,
				'footer' => $footer,
			);
			wp_send_json( $response );
		}

		/**
		 * Get thread info
		 *
		 * @return void
		 */
		public static function it_thread_info() {

			if ( check_ajax_referer( 'wpsc_it_thread_info', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			self::load_current_ticket();

			$current_user = WPSC_Current_User::$current_user;

			if ( ! ( self::$view_profile == 'agent' ) ) {
				wp_send_json_error( new WP_Error( '004', 'Unauthorized!' ), 401 );
			}

			$title = esc_attr__( 'Thread info', 'supportcandy' );

			$thread_id = isset( $_POST['thread_id'] ) ? intval( $_POST['thread_id'] ) : 0;
			if ( ! $thread_id ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$thread = new WPSC_Thread( $thread_id );
			if ( ! $thread->id ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			if ( self::$ticket != $thread->ticket ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$settings   = get_option( 'wpsc-gs-general', array() );
			$advanced   = get_option( 'wpsc-ms-advanced-settings', array() );
			$ip_address = $thread->ip_address ? $thread->ip_address : esc_attr__( 'Not Applicable', 'supportcandy' );
			$os         = $thread->os ? $thread->os : esc_attr__( 'Not Applicable', 'supportcandy' );
			$browser    = $thread->browser ? $thread->browser : esc_attr__( 'Not Applicable', 'supportcandy' );
			$source     = $thread->source && isset( WPSC_DF_Source::$sources[ $thread->source ] ) ? WPSC_DF_Source::$sources[ $thread->source ] : esc_attr__( 'Not Applicable', 'supportcandy' );

			ob_start();
			?>
			<div class="wpsc-thread-info">

				<div class="info-list-item">
					<div class="info-label"><?php esc_attr_e( 'Name', 'supportcandy' ); ?>:</div>
					<div class="info-val"><?php echo esc_attr( $thread->customer->name ); ?></div>
				</div>
				<?php

				if ( $current_user->is_agent && in_array( $current_user->agent->role, $settings['allow-ar-thread-email'] ) ) {
					?>
					<div class="info-list-item">
						<div class="info-label"><?php esc_attr_e( 'Email Address', 'supportcandy' ); ?>:</div>
						<div class="info-val"><?php echo esc_attr( $thread->customer->email ); ?></div>
					</div>
					<?php
				}
				?>

				<div class="info-list-item">
					<div class="info-label"><?php esc_attr_e( 'Source', 'supportcandy' ); ?>:</div>
					<div class="info-val"><?php echo esc_attr( $source ); ?></div>
				</div>

				<div class="info-list-item">
					<div class="info-label"><?php esc_attr_e( 'IP Address', 'supportcandy' ); ?>:</div>
					<div class="info-val"><?php echo esc_attr( $ip_address ); ?></div>
				</div>

				<div class="info-list-item">
					<div class="info-label"><?php esc_attr_e( 'Browser', 'supportcandy' ); ?>:</div>
					<div class="info-val"><?php echo esc_attr( $browser ); ?></div>
				</div>

				<div class="info-list-item">
					<div class="info-label"><?php esc_attr_e( 'Operating System', 'supportcandy' ); ?>:</div>
					<div class="info-val"><?php echo esc_attr( $os ); ?></div>
				</div>

				<?php
				if ( $thread->type == 'reply' && $thread->customer != $thread->ticket->customer ) {
					?>
					<div class="info-list-item">
						<div class="info-label"><?php esc_attr_e( 'Seen', 'supportcandy' ); ?>:</div>
						<div class="info-val">
							<?php echo is_object( $thread->seen ) ? esc_attr( $thread->seen->setTimezone( wp_timezone() )->format( $advanced['thread-date-format'] ) ) : esc_attr_e( 'No', 'supportcandy' ); ?>
						</div>
					</div>
					<?php
				}
				?>

			</div>
			<?php
			$body = ob_get_clean();

			ob_start();
			?>
			<button class="wpsc-button small secondary" onclick="wpsc_close_modal();">
				<?php esc_attr_e( 'Close', 'supportcandy' ); ?>
			</button>
			<?php
			$footer = ob_get_clean();

			$response = array(
				'title'  => $title,
				'body'   => $body,
				'footer' => $footer,
			);
			wp_send_json( $response );

		}

		/**
		 * Create new ticket from thread
		 *
		 * @return void
		 */
		public static function it_set_thread_new_ticket() {

			if ( check_ajax_referer( 'wpsc_it_set_thread_new_ticket', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			global $wpdb;
			self::load_current_ticket();
			$current_user = WPSC_Current_User::$current_user;

			if ( self::$view_profile != 'agent' ) {
				wp_send_json_error( 'Something went wrong!', 401 );
			}

			$ticket    = self::$ticket;
			$thread_id = isset( $_POST['thread_id'] ) ? intval( $_POST['thread_id'] ) : 0;

			$thread = new WPSC_Thread( $thread_id );
			if ( ! $thread->id ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			if ( self::$ticket != $thread->ticket ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$ignore_cft = apply_filters( 'wpsc_thread_new_ticket_ignore_cft', array( 'cf_html', 'df_description', 'df_id', 'df_ip_address', 'df_browser', 'df_assigned_agent', 'df_add_recipients', 'df_ip_address', 'df_os', 'df_prev_assignee', 'df_date_closed', 'df_sf_rating', 'df_sf_feedback', 'df_sf_date', 'df_time_spent', 'df_sla', 'df_last_reply_on', 'df_last_reply_by', 'df_usergroups' ) );
			$data       = array();
			foreach ( WPSC_Custom_Field::$custom_fields as $cf ) {

				if ( in_array( $cf->type::$slug, $ignore_cft ) || ! in_array( $cf->field, array( 'ticket', 'agentonly' ) ) ) {
					continue;
				}
				$data[ $cf->slug ] = $cf->type::get_duplicate_ticket_data( $cf, $ticket );
			}

			$new_ticket = WPSC_Ticket::insert( $data );

			$attachments = array();
			foreach ( $thread->attachments as $attachment ) {
				$attachments[] = self::get_duplicate_attachment_id( $attachment->id );
			}
			$duplicate_thread = WPSC_Thread::insert(
				array(
					'ticket'       => $new_ticket->id,
					'customer'     => $thread->customer->id,
					'type'         => $thread->type,
					'body'         => $thread->body,
					'attachments'  => $attachments ? implode( '|', $attachments ) : '',
					'ip_address'   => $thread->ip_address,
					'source'       => $thread->source,
					'os'           => $thread->os,
					'browser'      => $thread->browser,
					'date_created' => $thread->date_created->format( 'Y-m-d H:i:s' ),
					'date_updated' => $thread->date_updated->format( 'Y-m-d H:i:s' ),
				)
			);

			foreach ( $attachments as $id ) :
				$attachment            = new WPSC_Attachment( $id );
				$attachment->source    = $duplicate_thread->type;
				$attachment->source_id = $duplicate_thread->id;
				$attachment->ticket_id = $new_ticket->id;
				$attachment->save();
			endforeach;

			// duplicate description attachments.
			$body_attachments = array();
			$attachment_of_body = WPSC_Attachment::find(
				array(
					'items_per_page' => 0,
					'meta_query'     => array(
						'relation' => 'AND',
						array(
							'slug'    => 'source',
							'compare' => '=',
							'val'     => 'img_editor',
						),
						array(
							'slug'    => 'source_id',
							'compare' => '=',
							'val'     => $thread->id,
						),
					),
				)
			)['results'];
			foreach ( $attachment_of_body as $img ) {
				$body_attachments[ $img->id ] = self::get_duplicate_attachment_id( $img->id );
			}
			foreach ( $body_attachments as $old_id => $id ) :
				$attachment            = new WPSC_Attachment( $id );
				$attachment->source    = 'img_editor';
				$attachment->source_id = $duplicate_thread->id;
				$attachment->ticket_id = $new_ticket->id;
				$attachment->save();

				$old_url = home_url( '/' ) . '?wpsc_attachment=' . $old_id;
				$new_url = home_url( '/' ) . '?wpsc_attachment=' . $id;
				$duplicate_thread->body = str_replace( $old_url, $new_url, $duplicate_thread->body );
			endforeach;

			$duplicate_thread->save();

			do_action( 'wpsc_create_new_ticket', $new_ticket );

			wp_send_json( array( 'ticket_id' => $new_ticket->id ) );
		}

		/**
		 * Copy old file into new file
		 *
		 * @param integer $attachment_id - attachment id.
		 * @return new attachment id
		 */
		public static function get_duplicate_attachment_id( $attachment_id ) {

			$attachment = new WPSC_Attachment( $attachment_id );

			// Database insert array init.
			$data = array( 'name' => $attachment->name );

			$filename  = explode( '.', $attachment->name );
			$extension = strtolower( $filename[ count( $filename ) - 1 ] );
			unset( $filename[ count( $filename ) - 1 ] );
			$filename = implode( '.', $filename );
			$filename = str_replace( ' ', '_', $filename );
			$filename = str_replace( ',', '_', $filename );
			$filename = time() . '_' . preg_replace( '/[^A-Za-z0-9\-]/', '', $filename );

			// Check for image type.
			$img_extensions = array( 'png', 'jpeg', 'jpg', 'bmp', 'pdf', 'gif' );
			if ( ! in_array( $extension, $img_extensions ) ) {
				$extension .= '.txt';
			} else {
				$data['is_image'] = 1;
			}

			// Create file path.
			$today      = new DateTime();
			$upload_dir = wp_upload_dir();
			$filepath   = $upload_dir['basedir'] . '/wpsc/' . $today->format( 'Y' );
			if ( ! file_exists( $filepath ) ) {
				mkdir( $filepath, 0755, true );
			}
			$filepath .= '/' . $today->format( 'm' );
			if ( ! file_exists( $filepath ) ) {
				mkdir( $filepath, 0755, true );
			}
			$filepath .= '/' . $filename . '.' . $extension;

			$filepath_short = '/wpsc/' . $today->format( 'Y' ) . '/' . $today->format( 'm' ) . '/' . $filename . '.' . $extension;
			$data['file_path'] = $filepath_short;

			// Create time.
			$data['date_created'] = $today->format( 'Y-m-d H:i:s' );

			// copy to path.
			copy( $upload_dir['basedir'] . $attachment->file_path, $filepath );
			$data['is_active'] = 1;
			$new_attachement   = WPSC_Attachment::insert( $data );

			return $new_attachement->id;
		}

		/**
		 * Get edit thread
		 *
		 * @return void
		 */
		public static function get_edit_thread() {

			if ( check_ajax_referer( 'wpsc_it_get_edit_thread', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			self::load_current_ticket();

			if ( ! ( self::$view_profile == 'agent' && self::has_ticket_cap( 'eth' ) ) ) {
				wp_send_json_error( new WP_Error( '004', 'Unauthorized!' ), 401 );
			}

			$thread_id = isset( $_POST['thread_id'] ) ? intval( $_POST['thread_id'] ) : 0;
			if ( ! $thread_id ) {
				wp_send_json_error( new WP_Error( '005', 'Bad request!' ), 400 );
			}

			$thread = new WPSC_Thread( $thread_id );
			if ( ! $thread->id ) {
				wp_send_json_error( new WP_Error( '006', 'Bad request!' ), 400 );
			}

			if ( self::$ticket != $thread->ticket ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$current_user = WPSC_Current_User::$current_user;

			$unique_id = uniqid( 'wpsc_' )
			?>

			<div class="wpsc-edit-thread">
				<form onsubmit="return false;" class="edit-thread">
					<textarea class="wpsc_textarea" id="<?php echo esc_attr( $unique_id ); ?>"><?php echo wp_kses_post( $thread->body ); ?></textarea>
					<input class="<?php echo esc_attr( $unique_id ); ?>" type="file" onchange="wpsc_set_attach_multiple(this, '<?php echo esc_attr( $unique_id ); ?>', 'thread_attachments')" multiple style="display: none;">
					<div class="wpsc-it-editor-action-container">
						<div class="wpsc-edit-actions">
							<div class="wpsc-editor-actions">
								<?php
								if ( WPSC_Text_Editor::is_allow_attachments() ) :
									?>
									<span class="wpsc-link" onclick="wpsc_trigger_desc_attachments('<?php echo esc_attr( $unique_id ); ?>');"><?php esc_attr_e( 'Attach Files', 'supportcandy' ); ?></span>
									<?php
								endif;
								?>
							</div>
							<?php
							if ( WPSC_Text_Editor::is_attachment_notice() && WPSC_Text_Editor::is_allow_attachments() ) :
								?>
								<div class="wpsc-file-attachment-notice"><?php echo esc_attr( WPSC_Text_Editor::file_attachment_notice_text() ); ?></div>
								<?php
							endif;
							?>
							<div class="<?php echo esc_attr( $unique_id ); ?> wpsc-editor-attachment-container">
								<?php
								foreach ( $thread->attachments as $attachment ) :
									?>
									<div class="wpsc-editor-attachment upload-success">
										<div class="attachment-label"><?php echo esc_attr( $attachment->name ); ?></div>
										<div class="attachment-remove" onclick="wpsc_remove_attachment(this)">
											<?php WPSC_Icons::get( 'times' ); ?>
										</div>
										<input type="hidden" name="thread_attachments[]" value="<?php echo esc_attr( $attachment->id ); ?>">
									</div>
									<?php
								endforeach;
								?>
							</div>
						</div>
						<div class="submit-container">
							<button class="wpsc-button small primary margin-right" onclick="wpsc_it_set_edit_thread(this, '<?php echo esc_attr( $unique_id ); ?>')"><?php esc_attr_e( 'Submit', 'supportcandy' ); ?></button>
							<button class="wpsc-button small secondary" onclick="wpsc_it_get_thread(this, <?php echo esc_attr( self::$ticket->id ); ?>, <?php echo esc_attr( $thread_id ); ?>, '<?php echo esc_attr( wp_create_nonce( 'wpsc_it_get_thread' ) ); ?>')"><?php esc_attr_e( 'Cancel', 'supportcandy' ); ?></button>
						</div>
					</div>
					<input type="hidden" name="action" value="wpsc_it_set_edit_thread"/>
					<input type="hidden" name="ticket_id" value="<?php echo esc_attr( self::$ticket->id ); ?>"/>
					<input type="hidden" name="thread_id" value="<?php echo esc_attr( $thread->id ); ?>"/>
					<input type="hidden" name="_ajax_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wpsc_it_set_edit_thread' ) ); ?>">
				</form>
			</div>
			<script><?php WPSC_Text_Editor::print_editor_init_scripts( $unique_id, $unique_id . '_body' ); ?></script>
			<?php
			wp_die();
		}

		/**
		 * Set edit thread
		 *
		 * @return void
		 */
		public static function set_edit_thread() {

			if ( check_ajax_referer( 'wpsc_it_set_edit_thread', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			self::load_current_ticket();
			self::load_actions();

			if ( ! ( self::$view_profile == 'agent' && self::has_ticket_cap( 'eth' ) ) ) {
				wp_send_json_error( new WP_Error( '004', 'Unauthorized!' ), 401 );
			}

			$thread_id = isset( $_POST['thread_id'] ) ? intval( $_POST['thread_id'] ) : 0;
			if ( ! $thread_id ) {
				wp_send_json_error( new WP_Error( '005', 'Bad request!' ), 400 );
			}

			$thread = new WPSC_Thread( $thread_id );
			if ( ! $thread->id ) {
				wp_send_json_error( new WP_Error( '006', 'Bad request!' ), 400 );
			}

			if ( self::$ticket != $thread->ticket ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$thread_content = isset( $_POST['thread_content'] ) ? wp_kses_post( wp_unslash( $_POST['thread_content'] ) ) : '';
			if ( ! $thread_content ) {
				wp_send_json_error( new WP_Error( '007', 'Bad request!' ), 400 );
			}

			$thread_attachments = isset( $_POST['thread_attachments'] ) ? array_filter( array_map( 'intval', $_POST['thread_attachments'] ) ) : array();
			$new_attachments    = array();
			foreach ( $thread_attachments as $id ) {
				$attachment            = new WPSC_Attachment( $id );
				$attachment->is_active = 1;
				$attachment->ticket_id = self::$ticket->id;
				$attachment->source    = $thread->type;
				$attachment->source_id = $thread->id;
				$attachment->save();
				$new_attachments[] = $attachment;
			}

			$is_modify = false;

			// Set new thread.
			$prev_content = $thread->body;
			if ( $prev_content != $thread_content ) {

				$thread->body = $thread_content;

				// tinymce img attachments.
				if ( preg_match_all( '/' . preg_quote( home_url( '/' ), '/' ) . '\?wpsc_attachment=(\d*)/', $thread_content, $matches ) ) {

					foreach ( $matches[1] as $id ) {

						$attachment = new WPSC_Attachment( $id );
						if ( $attachment->is_active ) {
							continue;
						}

						$attachment->is_active = 1;
						$attachment->source_id = $thread->id;
						$attachment->ticket_id = self::$ticket->id;
						$attachment->save();
					}
				}

				$is_modify = true;
			}

			// Attachments.
			$prev_attachments = array_map( fn( $attachment) => $attachment->id, $thread->attachments );
			if ( array_diff( $prev_attachments, $thread_attachments ) || array_diff( $thread_attachments, $prev_attachments ) ) {
				$thread->attachments = $new_attachments;
				$is_modify           = true;
			}

			// Return if there is no change.
			if ( ! $is_modify ) {
				self::print_thread( $thread );
				wp_die();
			}

			// Set log for this change.
			WPSC_Log::insert(
				array(
					'type'         => 'thread',
					'ref_id'       => $thread->id,
					'modified_by'  => WPSC_Current_User::$current_user->customer->id,
					'body'         => wp_json_encode(
						array(
							'type' => 'modify',
							'prev' => array(
								'content'     => str_replace( PHP_EOL, '', $prev_content ),
								'attachments' => $prev_attachments,
							),
							'new'  => array(
								'content'     => str_replace( PHP_EOL, '', $thread_content ),
								'attachments' => $thread_attachments,
							),
						)
					),
					'date_created' => ( new DateTime() )->format( 'Y-m-d H:i:s' ),
				)
			);

			// Save changes to thread and print updated thread HTML.
			$thread->save();
			self::print_thread( $thread );
			wp_die();
		}

		/**
		 * Print HTML for a thread
		 *
		 * @return void
		 */
		public static function get_thread_html() {

			if ( check_ajax_referer( 'wpsc_it_get_thread', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			self::load_current_ticket();
			self::load_actions();

			if ( ! ( self::$view_profile == 'agent' && self::has_ticket_cap( 'eth' ) ) ) {
				wp_send_json_error( new WP_Error( '004', 'Unauthorized!' ), 401 );
			}

			$thread_id = isset( $_POST['thread_id'] ) ? intval( $_POST['thread_id'] ) : 0;
			if ( ! $thread_id ) {
				wp_send_json_error( new WP_Error( '005', 'Bad request!' ), 400 );
			}

			$thread = new WPSC_Thread( $thread_id );
			if ( ! $thread->id ) {
				wp_send_json_error( new WP_Error( '006', 'Bad request!' ), 400 );
			}

			self::print_thread( $thread );
			wp_die();
		}

		/**
		 * Delete thread
		 */
		public static function delete_thread() {

			if ( check_ajax_referer( 'wpsc_it_thread_delete', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			self::load_current_ticket();
			self::load_actions();

			if ( ! ( self::$view_profile == 'agent' && self::has_ticket_cap( 'dth' ) ) ) {
				wp_send_json_error( new WP_Error( '004', 'Unauthorized!' ), 401 );
			}

			$thread_id = isset( $_POST['thread_id'] ) ? intval( $_POST['thread_id'] ) : 0;
			if ( ! $thread_id ) {
				wp_send_json_error( new WP_Error( '005', 'Bad request!' ), 400 );
			}

			$thread = new WPSC_Thread( $thread_id );
			if ( ! $thread->id ) {
				wp_send_json_error( new WP_Error( '006', 'Bad request!' ), 400 );
			}

			if ( self::$ticket != $thread->ticket ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$thread->is_active = 0;
			$thread->save();

			// Set log for this change.
			WPSC_Log::insert(
				array(
					'type'         => 'thread',
					'ref_id'       => $thread->id,
					'modified_by'  => WPSC_Current_User::$current_user->customer->id,
					'body'         => wp_json_encode(
						array(
							'type' => 'delete',
						)
					),
					'date_created' => ( new DateTime() )->format( 'Y-m-d H:i:s' ),
				)
			);

			self::print_thread( $thread );
			wp_die();
		}

		/**
		 * View thread log
		 */
		public static function view_thread_log() {

			if ( check_ajax_referer( 'wpsc_it_view_thread_log', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			self::load_current_ticket();

			if ( ! ( self::$view_profile == 'agent' && self::has_ticket_cap( 'vl' ) ) ) {
				wp_send_json_error( new WP_Error( '004', 'Unauthorized!' ), 401 );
			}

			$thread_id = isset( $_POST['thread_id'] ) ? intval( $_POST['thread_id'] ) : 0;
			if ( ! $thread_id ) {
				wp_send_json_error( new WP_Error( '005', 'Bad request!' ), 400 );
			}

			$thread = new WPSC_Thread( $thread_id );
			if ( ! $thread->id ) {
				wp_send_json_error( new WP_Error( '006', 'Bad request!' ), 400 );
			}

			if ( $thread->type == 'note' && ! ( self::$view_profile == 'agent' && self::has_ticket_cap( 'pn' ) ) ) {
				wp_send_json_error( new WP_Error( '006', 'Unauthorized!' ), 401 );
			}

			$log_id = isset( $_POST['log_id'] ) ? intval( $_POST['log_id'] ) : 0;
			if ( ! $log_id ) {
				wp_send_json_error( new WP_Error( '007', 'Bad request!' ), 400 );
			}

			$log = new WPSC_Log( $log_id );
			if ( ! $log->id ) {
				wp_send_json_error( new WP_Error( '008', 'Bad request!' ), 400 );
			}

			$body  = json_decode( $log->body );
			$title = esc_attr__( 'Thread log', 'supportcandy' );

			ob_start();
			?>
			<div class="wpsc-modal-thread-log">

				<div class="wpsc-log-diff">

					<div class="lhs">
						<div>
							<div class="thread-text" style="margin-bottom:0;">
								<?php echo wp_kses_post( $body->prev->content ); ?>
							</div>
							<?php
							if ( $body->prev->attachments ) :
								?>
								<div class="wpsc-thread-attachments">
									<div class="wpsc-attachment-header"><?php esc_attr_e( 'Attachments:', 'supportcandy' ); ?></div>
									<?php
									foreach ( $body->prev->attachments as $id ) :
										$attachment = new WPSC_Attachment( $id );
										if ( ! $attachment->id ) {
											continue;
										}
										?>
										<div class="wpsc-attachment-item">
											<span class="wpsc-attachment-name"><?php echo esc_attr( $attachment->name ); ?></span>
										</div>
										<?php
									endforeach;
									?>
								</div>
								<?php
							endif;
							?>
						</div>
					</div>

					<div class="transform-icon">
						<?php
						if ( is_rtl() ) {
							WPSC_Icons::get( 'arrow-left' );
						} else {
							WPSC_Icons::get( 'arrow-right' );
						}
						?>
					</div>

					<div class="rhs">
						<div>
							<div class="thread-text" style="margin-bottom:0;">
								<?php echo wp_kses_post( $body->new->content ); ?>
							</div>
							<?php
							if ( $body->new->attachments ) :
								?>
								<div class="wpsc-thread-attachments">
									<div class="wpsc-attachment-header"><?php esc_attr_e( 'Attachments:', 'supportcandy' ); ?></div>
									<?php
									foreach ( $body->new->attachments as $id ) :
										$attachment = new WPSC_Attachment( $id );
										if ( ! $attachment->id ) {
											continue;
										}
										?>
										<div class="wpsc-attachment-item">
											<span class="wpsc-attachment-name"><?php echo esc_attr( $attachment->name ); ?></span>
										</div>
										<?php
									endforeach;
									?>
								</div>
								<?php
							endif;
							?>
						</div>
					</div>

				</div>

			</div>
			<?php
			$body = ob_get_clean();

			ob_start();
			?>
			<button class="wpsc-button small secondary" onclick="wpsc_close_modal();">
				<?php esc_attr_e( 'Close', 'supportcandy' ); ?>
			</button>
			<?php
			$footer = ob_get_clean();

			$response = array(
				'title'  => $title,
				'body'   => $body,
				'footer' => $footer,
			);
			wp_send_json( $response );
		}

		/**
		 * View deleted thread
		 *
		 * @return void
		 */
		public static function view_deleted_thread() {

			if ( check_ajax_referer( 'wpsc_it_view_deleted_thread', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			self::load_current_ticket();

			if ( ! ( self::$view_profile == 'agent' && self::has_ticket_cap( 'vl' ) ) ) {
				wp_send_json_error( new WP_Error( '004', 'Unauthorized!' ), 401 );
			}

			$thread_id = isset( $_POST['thread_id'] ) ? intval( $_POST['thread_id'] ) : 0;
			if ( ! $thread_id ) {
				wp_send_json_error( new WP_Error( '005', 'Bad request!' ), 400 );
			}

			$thread = new WPSC_Thread( $thread_id );
			if ( ! $thread->id ) {
				wp_send_json_error( new WP_Error( '006', 'Bad request!' ), 400 );
			}

			if ( $thread->type == 'note' && ! ( self::$view_profile == 'agent' && self::has_ticket_cap( 'pn' ) ) ) {
				wp_send_json_error( new WP_Error( '006', 'Unauthorized!' ), 401 );
			}

			if ( self::$ticket != $thread->ticket ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$title = $thread->customer->name;

			ob_start();
			?>
			<div class="wpsc-modal-thread-log">
				<div class="thread-text"><?php echo wp_kses_post( $thread->body ); ?></div>
				<?php
				if ( $thread->attachments ) :
					?>
					<div class="wpsc-thread-attachments">
						<div class="wpsc-attachment-header"><?php esc_attr_e( 'Attachments:', 'supportcandy' ); ?></div>
						<?php
						foreach ( $thread->attachments as $attachment ) :
							?>
							<div class="wpsc-attachment-item">
								<?php
								$download_url = home_url( '/' ) . '?wpsc_attachment=' . $attachment->id;
								?>
								<a class="wpsc-link" href="<?php echo esc_attr( $download_url ); ?>" target="_blank">
								<span class="wpsc-attachment-name"><?php echo esc_attr( $attachment->name ); ?></span></a>
							</div>
							<?php
						endforeach;
						?>
					</div>
					<?php
				endif;
				?>
			</div>
			<?php
			$body = ob_get_clean();

			ob_start();
			if ( self::$view_profile == 'agent' && self::has_ticket_cap( 'dth' ) ) :
				?>
				<button class="wpsc-button small primary" onclick="wpsc_it_restore_thread(<?php echo esc_attr( self::$ticket->id ) . ', ' . esc_attr( $thread->id ) . ', \'' . esc_attr( wp_create_nonce( 'wpsc_it_restore_thread' ) ) . '\''; ?>);">
					<?php esc_attr_e( 'Restore', 'supportcandy' ); ?>
				</button>
				<?php
			endif;
			if ( self::$view_profile == 'agent' && self::has_ticket_cap( 'dth' ) && WPSC_Functions::is_site_admin() ) :
				?>
				<button class="wpsc-button small primary" onclick="wpsc_it_thread_delete_permanently(<?php echo esc_attr( self::$ticket->id ) . ', ' . esc_attr( $thread->id ) . ', \'' . esc_attr( wp_create_nonce( 'wpsc_it_thread_delete_permanently' ) ) . '\''; ?>);">
					<?php esc_attr_e( 'Delete Permanently', 'supportcandy' ); ?>
				</button>
				<?php
			endif;
			?>
			<button class="wpsc-button small secondary" onclick="wpsc_close_modal();">
				<?php esc_attr_e( 'Close', 'supportcandy' ); ?>
			</button>
			<?php
			$footer = ob_get_clean();

			$response = array(
				'title'  => $title,
				'body'   => $body,
				'footer' => $footer,
			);
			wp_send_json( $response );
		}

		/**
		 * Restore thread
		 *
		 * @return void
		 */
		public static function restore_thread() {

			if ( check_ajax_referer( 'wpsc_it_restore_thread', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			self::load_current_ticket();
			self::load_actions();

			if ( ! ( self::$view_profile == 'agent' && self::has_ticket_cap( 'vl' ) && self::has_ticket_cap( 'dth' ) ) ) {
				wp_send_json_error( new WP_Error( '004', 'Unauthorized!' ), 401 );
			}

			$thread_id = isset( $_POST['thread_id'] ) ? intval( $_POST['thread_id'] ) : 0;
			if ( ! $thread_id ) {
				wp_send_json_error( new WP_Error( '005', 'Bad request!' ), 400 );
			}

			$thread = new WPSC_Thread( $thread_id );
			if ( ! $thread->id ) {
				wp_send_json_error( new WP_Error( '006', 'Bad request!' ), 400 );
			}

			if ( $thread->type == 'note' && ! ( self::$view_profile == 'agent' && self::has_ticket_cap( 'pn' ) ) ) {
				wp_send_json_error( new WP_Error( '007', 'Unauthorized!' ), 401 );
			}

			if ( self::$ticket != $thread->ticket ) {
				wp_send_json_error( new WP_Error( '008', 'Bad request!' ), 400 );
			}

			$thread->is_active = 1;
			$thread->save();

			// Set log for this change.
			WPSC_Log::insert(
				array(
					'type'         => 'thread',
					'ref_id'       => $thread->id,
					'modified_by'  => WPSC_Current_User::$current_user->customer->id,
					'body'         => wp_json_encode(
						array(
							'type' => 'restore',
						)
					),
					'date_created' => ( new DateTime() )->format( 'Y-m-d H:i:s' ),
				)
			);

			self::print_thread( $thread );
			wp_die();
		}

		/**
		 * Delete thread permanently
		 */
		public static function thread_delete_permanently() {

			if ( check_ajax_referer( 'wpsc_it_thread_delete_permanently', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			self::load_current_ticket();

			if ( ! ( self::$view_profile == 'agent' && self::has_ticket_cap( 'vl' ) && self::has_ticket_cap( 'dth' ) && WPSC_Functions::is_site_admin() ) ) {
				wp_send_json_error( new WP_Error( '004', 'Unauthorized!' ), 401 );
			}

			$thread_id = isset( $_POST['thread_id'] ) ? intval( $_POST['thread_id'] ) : 0;
			if ( ! $thread_id ) {
				wp_send_json_error( new WP_Error( '005', 'Bad request!' ), 400 );
			}

			$thread = new WPSC_Thread( $thread_id );
			if ( ! $thread->id ) {
				wp_send_json_error( new WP_Error( '006', 'Bad request!' ), 400 );
			}

			if ( $thread->type == 'note' && ! ( self::$view_profile == 'agent' && self::has_ticket_cap( 'pn' ) ) ) {
				wp_send_json_error( new WP_Error( '007', 'Unauthorized!' ), 401 );
			}

			if ( self::$ticket != $thread->ticket ) {
				wp_send_json_error( new WP_Error( '008', 'Bad request!' ), 400 );
			}

			WPSC_Thread::destroy( $thread );
			wp_die();
		}

		/**
		 * Load older threads
		 *
		 * @return void
		 */
		public static function load_older_threads() {

			self::load_current_ticket();
			self::load_actions();

			$last_thread = isset( $_POST['last_thread'] ) ? intval( $_POST['last_thread'] ) : 0; // phpcs:ignore
			if ( ! $last_thread ) {
				wp_send_json_error( new WP_Error( '004', 'Bad request!' ), 401 );
			}

			$gs      = get_option( 'wpsc-gs-general' );
			$filters = array(
				'meta_query' => array(
					'relation' => 'AND',
					array(
						'slug'    => 'ticket',
						'compare' => '=',
						'val'     => self::$ticket->id,
					),
					array(
						'slug'    => 'id',
						'compare' => '<',
						'val'     => $last_thread,
					),
				),
				'orderby'    => 'id',
				'order'      => 'DESC',
			);

			$thread_types = array(
				'slug'    => 'type',
				'compare' => 'IN',
				'val'     => array( 'report', 'reply' ),
			);

			if ( self::$view_profile == 'agent' && self::has_ticket_cap( 'pn' ) ) {
				$thread_types['val'][] = 'note';
			}
			if ( self::$view_profile == 'agent' && self::has_ticket_cap( 'vl' ) ) {
				$thread_types['val'][] = 'log';
			}

			$filters['meta_query'][] = $thread_types;

			$response = WPSC_Thread::find( $filters );
			$last_id  = $response['results'][ count( $response['results'] ) - 1 ]->id;

			ob_start();
			if ( $gs['reply-form-position'] == 'top' ) {
				foreach ( $response['results'] as $thread ) {
					if ( $thread->type == 'log' ) {
						self::print_log( $thread );
					} else {
						self::print_thread( $thread );
					}
				}
			} else {
				for ( $i = count( $response['results'] ) - 1; $i >= 0; $i-- ) {
					$thread = $response['results'][ $i ];
					if ( $thread->type == 'log' ) {
						self::print_log( $thread );
					} else {
						self::print_thread( $thread );
					}
				}
			}
			$threads = ob_get_clean();

			$html_response = array(
				'last_thread'   => $last_id,
				'has_next_page' => $response['has_next_page'],
				'threads'       => $threads,
			);

			wp_send_json( $html_response );
			wp_die();
		}

		/**
		 * Save reply
		 *
		 * @return void
		 */
		public static function auto_save() {

			$id = isset( $_POST['ticket_id'] ) ? intval( $_POST['ticket_id'] ) : 0; // phpcs:ignore
			if ( ! $id ) {
				wp_send_json_error( new WP_Error( '001', 'Unauthorized!' ), 401 );
			}

			$ticket = new WPSC_Ticket( $id );
			if ( ! $ticket->id ) {
				wp_send_json_error( new WP_Error( '002', 'Something went wrong!' ), 400 );
			}

			$description = isset( $_POST['description'] ) ? wp_kses_post( wp_unslash( $_POST['description'] ) ) : ''; // phpcs:ignore
			if ( ! $description ) {
				wp_send_json_error( 'Something went wrong!', 401 );
			}

			$cookie = array();
			if ( isset( $_COOKIE['wpsc_auto_save_reply'] ) ) {
				$cookie = json_decode( sanitize_text_field( wp_unslash( $_COOKIE['wpsc_auto_save_reply'] ) ), true );
			}

			$cookie[ $ticket->id ] = $description;
			$cookie = wp_json_encode( $cookie );
			setcookie( 'wpsc_auto_save_reply', $cookie, time() + 900, '/' );
		}

		/**
		 * Clear cookie after a reply
		 *
		 * @param WPSC_Thread $thread - thread object.
		 * @return void
		 */
		public static function clear_auto_save( $thread ) {

			self::delete_saved_reply( $thread->ticket->id );
		}

		/**
		 * Clear saved reply if customer leaves the screen.
		 *
		 * @return void
		 */
		public static function clear_saved_draft_reply() {

			$id = isset( $_POST['ticket_id'] ) ? intval( $_POST['ticket_id'] ) : 0; // phpcs:ignore
			if ( ! $id ) {
				wp_send_json_error( new WP_Error( '001', 'Unauthorized!' ), 401 );
			}

			$ticket = new WPSC_Ticket( $id );
			if ( ! $ticket->id ) {
				wp_send_json_error( new WP_Error( '002', 'Something went wrong!' ), 400 );
			}

			self::delete_saved_reply( $ticket->id );
			wp_die();
		}

		/**
		 * Delete saved reply from cookie.
		 *
		 * @param int $ticket_id - ticket id.
		 * @return void
		 */
		public static function delete_saved_reply( $ticket_id ) {
			if ( isset( $_COOKIE['wpsc_auto_save_reply'] ) ) {
				$cookie = json_decode( sanitize_text_field( wp_unslash( $_COOKIE['wpsc_auto_save_reply'] ) ), true );
				if ( array_key_exists( $ticket_id, $cookie ) ) {
					unset( $cookie[ $ticket_id ] );
					$cookie = wp_json_encode( $cookie );
					setcookie( 'wpsc_auto_save_reply', $cookie, time() + 900, '/' );
				}
			}
		}

		/**
		 * Show the list of live agents.
		 *
		 * @return void
		 */
		public static function get_live_agents() {

			$current_user = WPSC_Current_User::$current_user;
			$ms_advanced = get_option( 'wpsc-ms-advanced-settings' );
			if ( ! ( $current_user->is_agent && $ms_advanced['agent-collision'] ) ) {
				return;
			}
			?>
			<div class="wpsc-it-body-item wpsc-agent-collision wpsc-it-widget">
				<div class="wpsc-widget-header">
					<h2><?php esc_attr_e( 'Currently viewing', 'supportcandy' ); ?></h2>
				</div>
				<div class="wpsc-widget-body wpsc-live-agents">
				</div>
			</div>

			<script>
				wpsc_get_live_agents(<?php echo intval( $current_user->agent->id ); ?>, <?php echo intval( self::$ticket->id ); ?>);
				function wpsc_get_live_agents( agent_id, ticket_id ){

					const urlParams = new URLSearchParams(window.location.search);
					if( ! (supportcandy.current_section == 'ticket-list' && ( urlParams.has('id') || urlParams.has('ticket-id')) ) ){
						return;
					}
					var data = { action: 'wpsc_check_live_agents', agent_id, ticket_id, operation: 'check', _ajax_nonce: supportcandy.nonce };
					jQuery.post(
						supportcandy.ajax_url,
						data,
						function (response) {
							if ( response.agents ) {
								jQuery('.wpsc-live-agents').html(response.agents);
							} else {
								jQuery('.wpsc-agent-collision').remove();
							}
						}
					);
					setTimeout(
						function () {
							wpsc_get_live_agents( agent_id, ticket_id );
						},
						60000
					);
				}

				jQuery(document).ready(function(){
					// check if user close the tab or close the browser.
					jQuery(window).on('beforeunload', function(event){

						const urlParams = new URLSearchParams( window.location.search );
						if( ! (supportcandy.current_section == 'ticket-list' && ( urlParams.has('id') || urlParams.has('ticket-id')) ) ){
							return;
						}
						var agent_id = <?php echo intval( $current_user->agent->id ); ?>;
						var ticket_id = <?php echo intval( self::$ticket->id ); ?>;
						var data = { action: 'wpsc_check_live_agents', agent_id, ticket_id, operation: 'leave', _ajax_nonce: supportcandy.nonce };
						jQuery.post(
							supportcandy.ajax_url,
							data,
							function (response) {
							}
						);
					});
				});
			</script>
			<?php
		}

		/**
		 * Get list of live agents.
		 *
		 * @return void
		 */
		public static function check_live_agents() {

			if ( check_ajax_referer( 'general', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			$id = isset( $_POST['ticket_id'] ) ? intval( $_POST['ticket_id'] ) : 0; // phpcs:ignore
			if ( ! $id ) {
				wp_send_json_error( new WP_Error( '001', 'Unauthorized!' ), 401 );
			}

			$ticket = new WPSC_Ticket( $id );
			if ( ! $ticket->id ) {
				wp_send_json_error( new WP_Error( '002', 'Something went wrong!' ), 400 );
			}

			$agent_id = isset( $_POST['agent_id'] ) ? intval( $_POST['agent_id'] ) : 0; // phpcs:ignore
			if ( ! $agent_id ) {
				wp_send_json_error( new WP_Error( '001', 'Unauthorized!' ), 401 );
			}

			$agent = new WPSC_Agent( $agent_id );
			if ( ! $agent->id ) {
				wp_send_json_error( new WP_Error( '002', 'Something went wrong!' ), 400 );
			}

			$operation = isset( $_POST['operation'] ) ? esc_attr( $_POST['operation'] ) : 'check'; // phpcs:ignore

			$agents = json_decode( $ticket->live_agents, true );
			$agents = $agents ? $agents : array();

			if ( $operation == 'leave' ) {

				if ( array_key_exists( $agent->id, $agents ) ) {
					unset( $agents[ $agent->id ] );
					$agents = wp_json_encode( $agents );
					$ticket->live_agents = $agents;
					$ticket->save();
				}
				wp_die();
			}
			// check inactive agents.
			foreach ( $agents as $key => $tm ) {
				if ( $key == $agent->id ) {
					continue;
				}

				$time = DateTime::createFromFormat( 'Y-m-d H:i:s', $tm );
				$now = new DateTime();
				$interval = $now->diff( $time );
				if ( $interval->i >= 1 && $interval->s > 1 ) {
					unset( $agents[ $key ] );
				}
			}

			$agents[ $agent->id ] = ( new DateTime() )->format( 'Y-m-d H:i:s' );

			$html = '';
			foreach ( $agents as $ag_id => $tmp ) {
				if ( $ag_id == $agent->id ) {
					continue;
				}
				$agt = new WPSC_Agent( $ag_id );

				$html .= '<div class="wpsc-ac-agent"> ' .
							get_avatar( $agt->customer->email, 20 ) .
							'<span class="ac-name">' . $agt->name . '</span></div>';
			}

			$agents = wp_json_encode( $agents );
			$ticket->live_agents = $agents;
			$ticket->save();
			$response = array(
				'agents' => $html,
			);
			wp_send_json( $response );
		}
	}
endif;

WPSC_Individual_Ticket::init();
