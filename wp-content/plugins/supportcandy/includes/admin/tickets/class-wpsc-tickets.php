<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_Tickets' ) ) :

	final class WPSC_Tickets {

		/**
		 * Set if current screen is tickets page
		 *
		 * @var boolean
		 */
		public static $is_current_page;

		/**
		 * Sections for this view
		 *
		 * @var [type]
		 */
		private static $sections;

		/**
		 * Current section to load
		 *
		 * @var [type]
		 */
		public static $current_section;

		/**
		 * Initialize this class
		 */
		public static function init() {

			// Load sections for this screen.
			add_action( 'admin_init', array( __CLASS__, 'load_sections' ) );

			// Add current section to admin localization data.
			add_filter( 'wpsc_admin_localizations', array( __CLASS__, 'localizations' ) );

			// Humbargar modal.
			add_action( 'admin_footer', array( __CLASS__, 'humbargar_menu' ) );

			// JS dynamic fucntions.
			add_action( 'wpsc_js_ready', array( __CLASS__, 'register_js_ready_function' ) );
			add_action( 'wpsc_js_after_ticket_reply', array( __CLASS__, 'js_after_ticket_reply' ) );
			add_action( 'wpsc_js_after_close_ticket', array( __CLASS__, 'js_after_close_ticket' ) );
		}

		/**
		 * Load section (nav elements) for this screen
		 *
		 * @return void
		 */
		public static function load_sections() {

			self::$is_current_page = isset( $_REQUEST['page'] ) && $_REQUEST['page'] === 'wpsc-tickets' ? true : false; // phpcs:ignore

			$current_user = WPSC_Current_User::$current_user;
			if ( ! ( $current_user->is_agent && self::$is_current_page ) ) {
				return;
			}

			$gs = get_option( 'wpsc-gs-general' );
			$ms = get_option( 'wpsc-ms-advanced-settings' );

			// allow create ticket.
			$allow_create_ticket = in_array( $current_user->agent->role, $gs['allow-create-ticket'] ) ? true : false;

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

			self::$sections        = apply_filters( 'wpsc_tickets_page_sections', $sections );
			self::$current_section = isset( $_REQUEST['section'] ) ? sanitize_text_field( $_REQUEST['section'] ) : 'ticket-list'; // phpcs:ignore
		}

		/**
		 * Add localizations to local JS
		 *
		 * @param array $localizations - localization list.
		 * @return array
		 */
		public static function localizations( $localizations ) {

			if ( ! self::$is_current_page ) {
				return $localizations;
			}

			// Humbargar Titles.
			$localizations['humbargar_titles'] = self::get_humbargar_titles();

			// Current section.
			$localizations['current_section'] = self::$current_section;

			// Current ticket id.
			if ( self::$current_section === 'ticket-list' && isset( $_REQUEST['id'] ) ) { // phpcs:ignore
				$localizations['current_ticket_id'] = intval( $_REQUEST['id'] ); // phpcs:ignore
			}

			return $localizations;
		}

		/**
		 * UI foundation for this screen
		 *
		 * @return void
		 */
		public static function layout() {

			?>
			<div class="wrap">
				<hr class="wp-header-end">
				<div id="wpsc-container" style="display:none;">
					<div class="wpsc-shortcode-container">
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
						self::load_html_snippets();
						?>
					</div>
				</div>
			</div>
			<?php
		}

		/**
		 * Print humbargar menu in footer
		 *
		 * @return void
		 */
		public static function humbargar_menu() {

			if ( ! self::$is_current_page ) {
				return;
			}

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
		 * Register JS functions to call on document ready
		 *
		 * @return void
		 */
		public static function register_js_ready_function() {

			if ( ! self::$is_current_page ) {
				return;
			}

			echo esc_attr( self::$sections[ self::$current_section ]['callback'] ) . '();' . PHP_EOL;
		}

		/**
		 * After ticket reply
		 *
		 * @return void
		 */
		public static function js_after_ticket_reply() {

			if ( ! self::$is_current_page ) {
				return;
			}

			$current_user     = WPSC_Current_User::$current_user;
			$agent_settings   = get_option( 'wpsc-tl-ms-agent-view' );
			$call_to_function = $agent_settings['ticket-reply-redirect'] == 'ticket-list' ? 'wpsc_get_ticket_list();' : 'wpsc_get_individual_ticket(ticket_id)';
			echo esc_attr( $call_to_function ) . PHP_EOL;
		}

		/**
		 * JS after close ticket
		 *
		 * @return void
		 */
		public static function js_after_close_ticket() {

			if ( ! self::$is_current_page ) {
				return;
			}
			echo 'wpsc_get_ticket_list();' . PHP_EOL;
		}

		/**
		 * Load HTML snippets that can be used by js to load dynamically
		 *
		 * @return void
		 */
		public static function load_html_snippets() {
			?>

			<div class="wpsc-page-snippets" style="display: none;">
				<div class="wpsc-editor-attachment upload-waiting">
					<div class="attachment-label"></div>
					<div class="attachment-remove" onclick="wpsc_remove_attachment(this)">
						<?php WPSC_Icons::get( 'times' ); ?>
					</div>
					<div class="attachment-waiting"></div>
				</div>
			</div>
			<?php
		}
	}
endif;

WPSC_Tickets::init();

