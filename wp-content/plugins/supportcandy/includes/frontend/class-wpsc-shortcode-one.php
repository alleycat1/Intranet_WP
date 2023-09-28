<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_Shortcode_One' ) ) :

	final class WPSC_Shortcode_One {

		/**
		 * Tabs available for the user
		 *
		 * @var array
		 */
		public static $sections = array();

		/**
		 * Current tab
		 *
		 * @var string
		 */
		public static $current_section = '';

		/**
		 * Allow create ticket for current user
		 *
		 * @var boolean
		 */
		public static $allow_create_ticket = false;

		/**
		 * Set whether ticket url is authenticated or not.
		 *
		 * @var boolean
		 */
		public static $url_auth = false;

		/**
		 * Initialize this class
		 */
		public static function init() {

			// load sections.
			add_action( 'init', array( __CLASS__, 'load_sections' ), 100 );

			// register shortcode.
			add_shortcode( 'supportcandy', array( __CLASS__, 'layout' ) );

			// Add current section to localization data.
			add_filter( 'wpsc_frontend_localizations', array( __CLASS__, 'localizations' ) );
		}

		/**
		 * "supportcandy" shortcode layout
		 *
		 * @param array $attrs - Shortcode attributes.
		 * @return string
		 */
		public static function layout( $attrs ) {

			$current_user = WPSC_Current_User::$current_user;

			// logout if guest login is open-ticket.
			if ( ( WPSC_Current_User::$login_type == 'guest' || WPSC_Current_User::$login_type == 'registered' ) && WPSC_Current_User::$guest_login_type == 'open-ticket' ) {
				$current_user->logout();
				echo "<script type='text/javascript'>
                        window.location.reload();
                        </script>";
			}

			// ticket URL authentication.
			$advanced = get_option( 'wpsc-ms-advanced-settings' );
			if ( ! $advanced['ticket-url-auth'] ) {

				$ticket_id = isset( $_REQUEST['ticket-id'] ) ? intval( $_REQUEST['ticket-id'] ) : 0; // phpcs:ignore
				if ( ! $ticket_id ) {
					$ticket_id = isset( $_REQUEST['ticket_id'] ) ? intval( $_REQUEST['ticket_id'] ) : 0; // phpcs:ignore
				}
				$auth_code = isset( $_REQUEST['auth-code'] ) ? sanitize_text_field( $_REQUEST['auth-code'] ) : ''; // phpcs:ignore
				if ( ! $auth_code ) {
					$auth_code = isset( $_REQUEST['auth_code'] ) ? sanitize_text_field( $_REQUEST['auth_code'] ) : ''; // phpcs:ignore
				}

				if ( $ticket_id && $auth_code ) {
					$ticket = new WPSC_Ticket( $ticket_id );
					self::$url_auth = $ticket->auth_code == $auth_code ? true : false;
				}
			}

			ob_start();?>
			<div id="wpsc-container" style="display:none;">
				<div class="wpsc-shortcode-container">
					<?php

					if ( self::$sections ) {

						// add events for this shortcode.
						add_action( 'wpsc_js_ready', array( __CLASS__, 'register_js_ready_function' ) );
						add_action( 'wpsc_js_after_ticket_reply', array( __CLASS__, 'js_after_ticket_reply' ) );
						add_action( 'wpsc_js_after_close_ticket', array( __CLASS__, 'js_after_close_ticket' ) );
						add_action( 'wp_footer', array( __CLASS__, 'humbargar_menu' ) );
						?>

						<div class="wpsc-header wpsc-hidden-xs">
							<?php
							foreach ( self::$sections as $key => $section ) :
								$active = self::$current_section === $key ? 'active' : '';
								?>
								<div class="wpsc-menu-list wpsc-tickets-nav <?php echo esc_attr( $key ) . ' ' . esc_attr( $active ); ?>" onclick="<?php echo esc_attr( $section['callback'] ) . '();'; ?>">
									<?php WPSC_Icons::get( $section['icon'] ); ?>
									<label><?php echo esc_attr( $section['label'] ); ?></label>
								</div>
								<?php
							endforeach;
							?>
							<div class="wpsc-menu-list wpsc-tickets-nav log-out" onclick="wpsc_user_logout(this, '<?php echo esc_attr( wp_create_nonce( 'wpsc_user_logout' ) ); ?>');">
								<?php WPSC_Icons::get( 'log-out' ); ?>
								<label><?php echo esc_attr__( 'Logout', 'supportcandy' ); ?></label>
							</div>
							</div>
							<div class="wpsc-header wpsc-visible-xs">
								<div class="wpsc-humbargar-title">
									<?php WPSC_Icons::get( self::$sections[ self::$current_section ]['icon'] ); ?>
									<label><?php echo esc_attr( self::$sections[ self::$current_section ]['label'] ); ?></label>
								</div>
								<div class="wpsc-humbargar" onclick="wpsc_toggle_humbargar();">
									<?php WPSC_Icons::get( 'bars' ); ?>
								</div>
							</div>
							<div class="wpsc-body"></div>
							<?php

					} elseif ( self::$url_auth ) {

						?>
						<div class="wpsc-body"></div>
						<script>
							jQuery(document).ready(function(){
								wpsc_get_individual_ticket(<?php echo intval( $ticket_id ); ?>);
							});
						</script>
						<?php

					} else {

						WPSC_Frontend::load_authentication_screen();
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
		 * Set tabs for this shortcode
		 *
		 * @return void
		 */
		public static function load_sections() {

			$current_user = WPSC_Current_User::$current_user;
			$gs           = get_option( 'wpsc-gs-general' );
			$ms = get_option( 'wpsc-ms-advanced-settings' );

			// allow create ticket.
			$allow_create_ticket = false;
			if ( $current_user->user->ID ) {

				// agent.
				if ( $current_user->is_agent && in_array( $current_user->agent->role, $gs['allow-create-ticket'] ) ) {
					$allow_create_ticket = true;
				}

				// registered user.
				if ( ! $current_user->is_agent && in_array( 'registered-user', $gs['allow-create-ticket'] ) ) {
					$allow_create_ticket = true;
				}
			} elseif ( in_array( 'guest', $gs['allow-create-ticket'] ) ) {

				// guest.
				$allow_create_ticket = true;
			}
			self::$allow_create_ticket = $allow_create_ticket;

			// return if guest user.
			if ( ! ( ! $current_user->is_guest || $current_user->is_customer ) ) {
				return;
			}

			// sections init.
			$sections = array(
				'ticket-list' => array(
					'slug'     => 'ticket_list',
					'icon'     => 'list-alt',
					'label'    => esc_attr__( 'Ticket List', 'supportcandy' ),
					'callback' => 'wpsc_get_ticket_list',
				),
			);

			// new ticket.
			if ( $allow_create_ticket ) {
				$sections['new-ticket'] = array(
					'slug'     => 'new_ticket',
					'icon'     => 'plus-square',
					'label'    => esc_attr__( 'New Ticket', 'supportcandy' ),
					'callback' => 'wpsc_get_ticket_form',
				);
			}

			// my profile.
			if ( $ms['allow-my-profile'] ) {
				$sections['my-profile'] = array(
					'slug'     => 'my_profile',
					'icon'     => 'id-card',
					'label'    => esc_attr__( 'My Profile', 'supportcandy' ),
					'callback' => 'wpsc_get_user_profile',
				);
			}

			// agent profile.
			if ( $current_user->is_agent && $ms['allow-agent-profile'] ) {
				$sections['agent-profile'] = array(
					'slug'     => 'agent_profile',
					'icon'     => 'headset',
					'label'    => esc_attr__( 'Agent Profile', 'supportcandy' ),
					'callback' => 'wpsc_get_agent_profile',
				);
			}

			self::$sections        = apply_filters( 'wpsc_shortcode_sections', $sections );
			self::$current_section = isset( $_REQUEST['wpsc-section'] ) ? sanitize_text_field( $_REQUEST['wpsc-section'] ) : 'ticket-list'; // phpcs:ignore
		}

		/**
		 * Add localizations to local JS
		 *
		 * @param array $localizations - localization list.
		 * @return array
		 */
		public static function localizations( $localizations ) {

			// Humbargar Titles.
			$localizations['humbargar_titles'] = self::get_humbargar_titles();

			// Current section.
			$localizations['current_section'] = self::$current_section;

			// Current ticket id.
			if ( self::$current_section === 'ticket-list' && isset( $_REQUEST['ticket-id'] ) ) { // phpcs:ignore
				$localizations['current_ticket_id'] = intval( $_REQUEST['ticket-id'] ); // phpcs:ignore
			}

			return $localizations;
		}

		/**
		 * Print humbargar menu in footer
		 *
		 * @return void
		 */
		public static function humbargar_menu() {

			$current_user = WPSC_Current_User::$current_user;
			?>
			<div class="wpsc-humbargar-overlay" onclick="wpsc_toggle_humbargar();" style="display:none"></div>
			<div class="wpsc-humbargar-menu" style="display:none">
				<div class="box-inner">
					<div class="wpsc-humbargar-close" onclick="wpsc_toggle_humbargar();">
						<?php WPSC_Icons::get( 'times' ); ?>
					</div>
					<?php
					foreach ( self::$sections as $key => $section ) :

						$active = self::$current_section === $key ? 'active' : '';
						?>
						<div 
							class="wpsc-humbargar-menu-item <?php echo esc_attr( $key ) . ' ' . esc_attr( $active ); ?>"
							onclick="<?php echo esc_attr( $section['callback'] ) . '(true);'; ?>">
							<?php WPSC_Icons::get( $section['icon'] ); ?>
							<label><?php echo esc_attr( $section['label'] ); ?></label>
						</div>
					<?php endforeach; ?>
					<div class="wpsc-humbargar-menu-item log-out" onclick="wpsc_user_logout(this, '<?php echo esc_attr( wp_create_nonce( 'wpsc_user_logout' ) ); ?>');">
						<?php WPSC_Icons::get( 'log-out' ); ?>
						<label><?php echo esc_attr__( 'Logout', 'supportcandy' ); ?></label>
					</div>
				</div>
			</div>
			<?php
		}

		/**
		 * Humbargar mobile titles to be used in localizations
		 *
		 * @return array
		 */
		private static function get_humbargar_titles() {

			$titles = array();
			foreach ( self::$sections as $section ) {

				ob_start();
				WPSC_Icons::get( $section['icon'] );
				echo '<label>' . esc_attr( $section['label'] ) . '</label>';
				$titles[ $section['slug'] ] = ob_get_clean();
			}
			return $titles;
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
				 * Get ticket list
				 */
				function wpsc_get_ticket_list(is_humbargar = false) {

					supportcandy.current_section = 'ticket-list';

					if (is_humbargar) wpsc_toggle_humbargar();

					if (wpsc_is_description_text()) {
						if ( !confirm(supportcandy.translations.warning_message)){
							return;
						}  else {
							var is_tinymce = (typeof tinyMCE != "undefined") && tinyMCE.activeEditor && !tinyMCE.activeEditor.isHidden();
							if (is_tinymce && tinymce.get('description')) {
								var description = tinyMCE.get('description').setContent('');
							} else {
								var description = jQuery('#description').val('');
							}
							ticket_id = jQuery('#wpsc-current-ticket').val();
							wpsc_clear_saved_draft_reply( ticket_id );
						}
					}

					var id = supportcandy.current_ticket_id;
					if (id) {
						delete supportcandy.current_ticket_id;
						wpsc_get_individual_ticket(id);
						return;
					}

					// set flag to differenciate between ticket list and individual ticket
					supportcandy.ticketListIsIndividual = false;

					jQuery('.wpsc-tickets-nav, .wpsc-humbargar-menu-item').removeClass('active');
					jQuery('.wpsc-tickets-nav.ticket-list, .wpsc-humbargar-menu-item.ticket-list').addClass('active');
					jQuery('.wpsc-humbargar-title').html(supportcandy.humbargar_titles.ticket_list);

					// set url
					var url = new URL(window.location.href);
					var search_params = url.searchParams;
					search_params.set('wpsc-section', 'ticket-list');
					search_params.delete('ticket-id');
					url.search = search_params.toString();
					window.history.replaceState({}, null, url.toString());

					jQuery('.wpsc-body').html(supportcandy.loader_html);

					var data = {
						action: 'wpsc_get_ticket_list',
						_ajax_nonce: supportcandy.nonce
					};
					search_params.forEach(function(value, key) {
						data[key] = value;
					});
					if (typeof supportcandy.ticketList != 'undefined' && typeof supportcandy.ticketList.filters != 'undefined') {
						data.filters = supportcandy.ticketList.filters;
					}
					jQuery.post(supportcandy.ajax_url, data, function (response) {
						jQuery('.wpsc-body').html(response);
						wpsc_reset_responsive_style();
					});
				}

				/**
				 * Get individual ticket
				 */
				function wpsc_get_individual_ticket(id) {

					jQuery('.wpsc-tickets-nav, .wpsc-humbargar-menu-item').removeClass('active');
					jQuery('.wpsc-tickets-nav.ticket-list, .wpsc-humbargar-menu-item.ticket-list').addClass('active');
					jQuery('.wpsc-humbargar-title').html(supportcandy.humbargar_titles.ticket_list);

					// set url
					var url = new URL(window.location.href);
					var search_params = url.searchParams;
					search_params.set('wpsc-section', 'ticket-list');
					search_params.set('ticket-id', id);
					url.search = search_params.toString();
					window.history.replaceState({}, null, url.toString());

					jQuery('.wpsc-body').html(supportcandy.loader_html);

					// set flag to differenciate between ticket list and individual ticket
					supportcandy.ticketListIsIndividual = true;

					var data = {
						action: 'wpsc_get_individual_ticket',
						ticket_id: id,
					};
					search_params.forEach(function(value, key) {
						data[key] = value;
					});
					jQuery.post(supportcandy.ajax_url, data, function (response) {
						jQuery('.wpsc-body').html(response);
						wpsc_reset_responsive_style();
					});
				}

				/**
				 * Get create ticket form
				 */
				function wpsc_get_ticket_form(is_humbargar = false) {
					<?php

					$pgsettings = get_option( 'wpsc-gs-page-settings' );
					if ( $pgsettings['new-ticket-page'] == 'custom' && $pgsettings['new-ticket-url'] ) :
						?>
						window.location = "<?php echo esc_url( $pgsettings['new-ticket-url'] ); ?>";
						return;
						<?php
					endif;
					?>

					supportcandy.current_section = 'new-ticket';

					if (is_humbargar) wpsc_toggle_humbargar();

					if (wpsc_is_description_text()) {
						if ( confirm(supportcandy.translations.warning_message)){
							current_ticket = jQuery('#wpsc-current-ticket').val();
							wpsc_clear_saved_draft_reply( current_ticket );
						} else{
							return;
						}
					}

					jQuery('.wpsc-tickets-nav, .wpsc-humbargar-menu-item').removeClass('active');
					jQuery('.wpsc-tickets-nav.new-ticket, .wpsc-humbargar-menu-item.new-ticket').addClass('active');
					jQuery('.wpsc-humbargar-title').html(supportcandy.humbargar_titles.new_ticket);

					// set url
					var url = new URL(window.location.href);
					var search_params = url.searchParams;
					search_params.set('wpsc-section', 'new-ticket');
					search_params.delete('ticket-id');
					url.search = search_params.toString();
					window.history.replaceState({}, null, url.toString());

					jQuery('.wpsc-body').html(supportcandy.loader_html);

					var data = {
						action: 'wpsc_get_ticket_form',
						_ajax_nonce: supportcandy.nonce
					};
					search_params.forEach(function(value, key) {
						data[key] = value;
					});
					jQuery.post(supportcandy.ajax_url, data, function (response) {
						jQuery('.wpsc-body').html(response);
						wpsc_reset_responsive_style();
					});
				}

				/**
				 * Get agent settings
				 */
				function wpsc_get_user_profile(is_humbargar = false) {

					supportcandy.current_section = 'my-profile';

					if (is_humbargar) wpsc_toggle_humbargar();

					jQuery('.wpsc-tickets-nav, .wpsc-humbargar-menu-item').removeClass('active');
					jQuery('.wpsc-tickets-nav.my-profile, .wpsc-humbargar-menu-item.my-profile').addClass('active');
					jQuery('.wpsc-humbargar-title').html(supportcandy.humbargar_titles.my_profile);

					// set url
					var url = new URL(window.location.href);
					var search_params = url.searchParams;
					search_params.set('wpsc-section', 'my-profile');
					search_params.delete('ticket-id');
					url.search = search_params.toString();
					window.history.replaceState({}, null, url.toString());

					jQuery('.wpsc-body').html(supportcandy.loader_html);

					var data = { action: 'wpsc_get_user_profile' };
					search_params.forEach(function(value, key) {
						data[key] = value;
					});
					jQuery.post(supportcandy.ajax_url, data, function (response) {
						jQuery('.wpsc-body').html(response);
						wpsc_reset_responsive_style();
					});
				}

				/**
				 * Get agent settings
				 */
				function wpsc_get_agent_profile(is_humbargar = false) {

					supportcandy.current_section = 'agent-profile';

					if (is_humbargar) wpsc_toggle_humbargar();

					jQuery('.wpsc-tickets-nav, .wpsc-humbargar-menu-item').removeClass('active');
					jQuery('.wpsc-tickets-nav.agent-profile, .wpsc-humbargar-menu-item.agent-profile').addClass('active');
					jQuery('.wpsc-humbargar-title').html(supportcandy.humbargar_titles.agent_profile);

					// set url
					var url = new URL(window.location.href);
					var search_params = url.searchParams;
					search_params.set('wpsc-section', 'agent-profile');
					search_params.delete('ticket-id');
					url.search = search_params.toString();
					window.history.replaceState({}, null, url.toString());

					jQuery('.wpsc-body').html(supportcandy.loader_html);

					var data = { action: 'wpsc_get_agent_profile' };
					search_params.forEach(function(value, key) {
						data[key] = value;
					});
					jQuery.post(supportcandy.ajax_url, data, function (response) {
						jQuery('.wpsc-body').html(response);
						wpsc_reset_responsive_style();
						jQuery('.wpsc-ap-nav.general').trigger('click');
					});
				}

				/**
				 * Get create ticket as guest
				 */
				function wpsc_get_guest_ticket_form() {

					jQuery('.wpsc-shortcode-container').html('<div class="wpsc-body"></div>');
					wpsc_get_ticket_form();
				}
			</script>
			<?php
		}

		/**
		 * Register JS functions to call on document ready
		 *
		 * @return void
		 */
		public static function register_js_ready_function() {

			if ( self::$sections ) {
				echo esc_attr( self::$sections[ self::$current_section ]['callback'] ) . '();' . PHP_EOL;
			}
		}

		/**
		 * After ticket reply
		 *
		 * @return void
		 */
		public static function js_after_ticket_reply() {

			$current_user = WPSC_Current_User::$current_user;

			$agent_settings    = get_option( 'wpsc-tl-ms-agent-view' );
			$customer_settings = get_option( 'wpsc-tl-ms-customer-view' );
			$redirect          = $current_user->is_agent ? $agent_settings['ticket-reply-redirect'] : $customer_settings['ticket-reply-redirect'];
			$call_to_function  = $redirect == 'ticket-list' ? 'wpsc_get_ticket_list();' : 'wpsc_get_individual_ticket(ticket_id)';
			echo esc_attr( $call_to_function ) . PHP_EOL;
		}

		/**
		 * JS after close ticket
		 *
		 * @return void
		 */
		public static function js_after_close_ticket() {

			echo 'wpsc_get_ticket_list();' . PHP_EOL;
		}
	}
endif;

WPSC_Shortcode_One::init();
