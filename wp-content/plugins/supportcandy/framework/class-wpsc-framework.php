<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_Framework' ) ) :

	final class WPSC_Framework {

		/**
		 * Initialize the class
		 *
		 * @return void
		 */
		public static function init() {

			// Add framework snippets to footer for wpsc pages.
			add_action( 'wp_footer', array( __CLASS__, 'print_snippets' ) );
			add_action( 'admin_footer', array( __CLASS__, 'print_snippets' ) );

			// JS events for framework.
			add_action( 'wp_footer', array( __CLASS__, 'js_events' ) );
			add_action( 'admin_footer', array( __CLASS__, 'js_events' ) );

			// Framework dynamic css used in appearance setting.
			add_action( 'wp_footer', array( __CLASS__, 'dynamic_css' ) );
			add_action( 'admin_footer', array( __CLASS__, 'dynamic_css' ) );

			// Localization.
			add_filter( 'wpsc_admin_localizations', array( __CLASS__, 'localizations' ) );
			add_filter( 'wpsc_frontend_localizations', array( __CLASS__, 'localizations' ) );

			// Dynamic scripts.
			add_action( 'wp_footer', array( __CLASS__, 'js_frontend' ) );
			add_action( 'admin_footer', array( __CLASS__, 'js_frontend' ) );
			add_action( 'admin_footer', array( __CLASS__, 'js_backend' ) );
		}

		/**
		 * Print framework snippets to footer
		 *
		 * @return void
		 */
		public static function print_snippets() {

			// Check load scripts setting to load script on perticular page.
			$page_settings = get_option( 'wpsc-gs-page-settings' );
			if ( ! is_admin() && $page_settings['load-scripts'] == 'custom' && ! in_array( get_the_id(), $page_settings['load-script-pages'] ) ) {
				return;
			}
			?>
			<!-- Modal Pop-up -->
			<div class="wpsc-modal" style="display:none">
				<div class="overlay"></div>
				<div class="loader">
					<img 
						src="<?php echo esc_url( WPSC_PLUGIN_URL . 'asset/images/loader-white.gif' ); ?>" 
						alt="Loading...">
				</div>
				<div class="inner-container">
					<div class="modal">
						<div class="wpsc-modal-header"></div>
						<div class="wpsc-modal-body"></div>
						<div class="wpsc-modal-footer"></div>
					</div>
				</div>
			</div>
			<?php
		}

		/**
		 * Print JS events functionality for frameworks
		 *
		 * @return void
		 */
		public static function js_events() {

			// Check load scripts setting to load script on perticular page.
			$page_settings = get_option( 'wpsc-gs-page-settings' );
			if ( ! is_admin() && $page_settings['load-scripts'] == 'custom' && ! in_array( get_the_id(), $page_settings['load-script-pages'] ) ) {
				return;
			}
			?>
			<script type="text/javascript">

				// Register functions to call for responsive behaviour changes
				function wpsc_apply_responsive_styles(){

					wpsc_close_humbargar();
					wpsc_el_reset_visible();
					wpsc_el_reset_hidden();
					<?php do_action( 'wpsc_js_apply_responsive_styles' ); ?>
				}

				// Register functions to call on document ready
				function wpsc_document_ready() {
					<?php do_action( 'wpsc_js_ready' ); ?>
				}

				// after ticket reply
				function wpsc_after_ticket_reply(ticket_id) {
					<?php do_action( 'wpsc_js_after_ticket_reply' ); ?>
				}

				// after close ticket
				function wpsc_after_close_ticket(ticket_id) {
					<?php do_action( 'wpsc_js_after_close_ticket' ); ?>
				}

				// after change create as
				function wpsc_after_change_create_as() {
					wpsc_get_create_as_customer_fields('<?php echo esc_attr( wp_create_nonce( 'wpsc_get_create_as_customer_fields' ) ); ?>');
					<?php do_action( 'wpsc_js_after_change_create_as' ); ?>
				}
			</script>
			<?php
		}

		/**
		 * Loader HTML
		 *
		 * @return string - Prints HTML.
		 */
		public static function loader_html() {

			ob_start();
			?>
			<div class="wpsc-loader">
				<img src="<?php echo esc_url( WPSC_PLUGIN_URL . 'asset/images/loader.gif' ); ?>" alt="Loading..." />
			</div>
			<?php
			return ob_get_clean();
		}

		/**
		 * Inline loader
		 *
		 * @return string - Prints HTML.
		 */
		public static function inline_loader() {

			ob_start();
			?>
			<img class="wpsc-inline-loader" src="<?php echo esc_url( WPSC_PLUGIN_URL . 'asset/images/loader.gif' ); ?>" alt="Loading..." />
			<?php
			return ob_get_clean();
		}

		/**
		 * Load settings based dynamic css
		 *
		 * @return void
		 */
		public static function dynamic_css() {

			// Check load scripts setting to load script on perticular page.
			$page_settings = get_option( 'wpsc-gs-page-settings' );
			if ( ! is_admin() && $page_settings['load-scripts'] == 'custom' && ! in_array( get_the_id(), $page_settings['load-script-pages'] ) ) {
				return;
			}

			$general            = get_option( 'wpsc-ap-general' );
			$ticket_list        = get_option( 'wpsc-ap-ticket-list' );
			$individual_ticket  = get_option( 'wpsc-ap-individual-ticket' );
			$modal              = get_option( 'wpsc-ap-modal' );
			$agent_collision    = get_option( 'wpsc-ap-agent-collision' );

			?>
			<style type="text/css">
				.wpsc-modal .overlay,
				.wpsc-humbargar-overlay {
					z-index: <?php echo esc_attr( $modal['z-index'] ); ?>;
				}
				.wpsc-modal .loader,
				.wpsc-modal .inner-container,
				.wpsc-humbargar-menu {
					z-index: <?php echo esc_attr( intval( $modal['z-index'] + 1 ) ); ?>;
				}
				.select2-container--open {
					z-index: <?php echo esc_attr( intval( $modal['z-index'] + 2 ) ); ?>;
				}
				.wpsc-header {
					background-color: <?php echo esc_attr( $general['primary-color'] ); ?> !important;
				}
				.wpsc-menu-list {
					color: <?php echo esc_attr( $general['menu-link-color'] ); ?>;
				}
				.wpsc-shortcode-container {
					background-color: <?php echo esc_attr( $general['main-background-color'] ); ?> !important;
					border: 1px solid <?php echo esc_attr( $general['primary-color'] ); ?> !important;
					color: <?php echo esc_attr( $general['main-text-color'] ); ?>;
				} 
				.wpsc-humbargar, 
				.wpsc-humbargar-title {
					color: #fff !important;
				}
				.wpsc-humbargar-overlay {
					z-index: <?php echo esc_attr( intval( $modal['z-index'] ) ); ?>;
				}
				.wpsc-humbargar-menu {
					z-index: <?php echo esc_attr( intval( $modal['z-index'] + 1 ) ); ?>;
					background-color: #fff !important;
				}
				.wpsc-humbargar-menu-item:hover, 
				.wpsc-humbargar-menu-item.active,
				.wpsc-setting-nav:hover,
				.wpsc-setting-nav.active {
					background-color: <?php echo esc_attr( $general['primary-color'] ); ?> !important;
				}

				/* Ticket list */
				.wpsc-search input {
					color: #8a8a8a !important;
				}
				.wpsc-ticket-list-tbl th {
					background-color: <?php echo esc_attr( $ticket_list['list-header-background-color'] ); ?>;
					color: <?php echo esc_attr( $ticket_list['list-header-text-color'] ); ?>;
				}
				.wpsc-ticket-list-tbl tr:nth-child(even){
					background-color: <?php echo esc_attr( $ticket_list['list-item-even-background-color'] ); ?>;
					color: <?php echo esc_attr( $ticket_list['list-item-even-text-color'] ); ?>;
				}
				.wpsc-ticket-list-tbl tr:nth-child(odd){
					background-color: <?php echo esc_attr( $ticket_list['list-item-odd-background-color'] ); ?>;
					color: <?php echo esc_attr( $ticket_list['list-item-odd-text-color'] ); ?>;
				}
				.wpsc-ticket-list-tbl tbody tr:hover {
					background-color: <?php echo esc_attr( $ticket_list['list-item-hover-background-color'] ); ?>;
					color: <?php echo esc_attr( $ticket_list['list-item-hover-text-color'] ); ?>;
				}

				/* Individual Ticket */
				.wpsc-thread.reply,
				.wpsc-thread.reply h2 {
					color: <?php echo esc_attr( $individual_ticket['reply-primary-color'] ); ?>;
				}
				.wpsc-thread.reply .thread-time,
				.wpsc-thread.reply .wpsc-thread-logs {
					color: <?php echo esc_attr( $individual_ticket['reply-secondary-color'] ); ?>;
				}
				.wpsc-thread.reply .actions {
					color: <?php echo esc_attr( $individual_ticket['reply-icon-color'] ); ?>;
				}
				.wpsc-thread.note,
				.wpsc-thread.note h2 {
					color: <?php echo esc_attr( $individual_ticket['note-primary-color'] ); ?>;
				}
				.wpsc-thread.note .email-address,
				.wpsc-thread.note .thread-time,
				.wpsc-thread.note .wpsc-thread-logs {
					color: <?php echo esc_attr( $individual_ticket['note-secondary-color'] ); ?>;
				}
				.wpsc-thread.note .actions {
					color: <?php echo esc_attr( $individual_ticket['note-icon-color'] ); ?>;
				}
				.wpsc-thread.log .thread-body {
					color: <?php echo esc_attr( $individual_ticket['log-text'] ); ?>;
				}
				.wpsc-widget-header {
					background-color: <?php echo esc_attr( $individual_ticket['widget-header-bg-color'] ); ?>;
					color: <?php echo esc_attr( $individual_ticket['widget-header-text-color'] ); ?>;
				}
				.wpsc-widget-header h2 {
					color: <?php echo esc_attr( $individual_ticket['widget-header-text-color'] ); ?>;
				}
				.wpsc-widget-body {
					background-color: <?php echo esc_attr( $individual_ticket['widget-body-bg-color'] ); ?>;
					color: <?php echo esc_attr( $individual_ticket['widget-body-text-color'] ); ?>;
				}
				.wpsc-widget-body .info-list-item .info-label, .wpsc-lg-label  {
					color: <?php echo esc_attr( $individual_ticket['widget-body-label-color'] ); ?>;
				}

				/* Input fields */
				#wpsc-container input[type=text]:focus,
				#wpsc-container input[type=text],
				#wpsc-container input[type=password]:focus,
				#wpsc-container input[type=password],
				.wpsc-modal input[type=text]:focus,
				.wpsc-modal input[type=text],
				.wpsc-modal input[type=password]:focus,
				.wpsc-modal input[type=password],
				#wpsc-container select,
				#wpsc-container select:focus,
				.wpsc-modal select,
				.wpsc-modal select:focus,
				#wpsc-container textarea,
				#wpsc-container textarea:focus,
				.wpsc-modal textarea,
				.wpsc-modal textarea:focus,
				#wpsc-container .checkbox-container label:before,
				.wpsc-modal .checkbox-container label:before,
				#wpsc-container .radio-container label:before,
				.wpsc-modal .radio-container label:before {
					border: 1px solid #8a8a8a !important;
					color: #000 !important;
				}

				/* Buttons */
				.wpsc-button.primary {
					border: 1px solid <?php echo esc_attr( $general['primary-color'] ); ?> !important;
					background-color: <?php echo esc_attr( $general['primary-color'] ); ?> !important;
					color: #fff !important;
				}

				.wpsc-button.secondary {
					border: 1px solid <?php echo esc_attr( $general['primary-color'] ); ?> !important;
					background-color: #fff !important;
					color: <?php echo esc_attr( $general['primary-color'] ); ?> !important;
				}

				/* Links */
				.wpsc-link {
					color: <?php echo esc_attr( $general['link-color'] ); ?>;
				}

				/* Modal popup */
				.wpsc-modal-header {
					background-color: <?php echo esc_attr( $modal['header-bg-color'] ); ?>;
					color: <?php echo esc_attr( $modal['header-text-color'] ); ?>;
				}
				.wpsc-modal-body {
					background-color: <?php echo esc_attr( $modal['body-bg-color'] ); ?>;
					color: <?php echo esc_attr( $modal['body-text-color'] ); ?>;
				}
				.wpsc-modal-footer {
					background-color: <?php echo esc_attr( $modal['footer-bg-color'] ); ?>;
				}
				.wpsc-modal-body .info-label {
					color: <?php echo esc_attr( $modal['body-label-color'] ); ?>;
				}

				/* Misc */
				.wpsc-section-header,
				.wpsc-it-subject-container h2 {
					color: <?php echo esc_attr( $general['main-text-color'] ); ?>;
				}
				.wpsc-popover-menu-item:hover,
				.wpsc-ap-nav.active,
				.wpsc-ap-nav:hover {
					background-color: <?php echo esc_attr( $general['primary-color'] ); ?>;
				}

				/* Agent Collision */
				.wpsc-ac-agent {
					color: <?php echo esc_attr( $agent_collision['header-text-color'] ); ?>;
					background-color: <?php echo esc_attr( $agent_collision['header-bg-color'] ); ?>;
				}

				/* Ticket tags */
				.wpsc-add-ticket-tag {
					position: relative;
					display: flex;
					align-items: center;
					justify-content: center;
					width: 25px;
					height: 25px;
					color: #fff !important;
					background-color: <?php echo esc_attr( $general['primary-color'] ); ?> !important;
					border: 1px solid <?php echo esc_attr( $general['primary-color'] ); ?> !important;
					outline: none;
					border-radius: 5px;
					cursor: pointer;
					margin: 0px 0px 0px 3px;
					padding: 5px;
					box-sizing: border-box;
				}

				.wpsc-close-ticket-tag {
					position: relative;
					display: flex;
					align-items: center;
					justify-content: center;
					width: 25px;
					height: 25px;
					color: <?php echo esc_attr( $general['primary-color'] ); ?> !important;
					background-color: #fff !important;
					border: 1px solid <?php echo esc_attr( $general['primary-color'] ); ?> !important;
					outline: none;
					border-radius: 5px;
					cursor: pointer;
					margin: 0px 0px 0px 3px;
					padding: 5px;
					box-sizing: border-box;
				}

				.wpsc-ticket-tags-action {
					display: flex;
					margin: 5px 0px 10px 0px;
					flex-direction: row-reverse;
				}
			</style>
			<?php
		}

		/**
		 * Localizations for framework
		 *
		 * @param array $localizations - localization list.
		 * @return array
		 */
		public static function localizations( $localizations ) {

			$localizations['translations']['please_wait']           = esc_attr__( 'Please wait...', 'supportcandy' );
			$localizations['translations']['req_fields_missing']    = esc_attr__( 'Required fields missing!', 'supportcandy' );
			$localizations['translations']['confirm']               = esc_attr__( 'Are you sure?', 'supportcandy' );
			$localizations['translations']['something_wrong']       = esc_attr__( 'Something went wrong!', 'supportcandy' );
			$localizations['translations']['view_more']             = esc_attr__( 'View more!', 'supportcandy' );
			$localizations['translations']['view_less']             = esc_attr__( 'View less!', 'supportcandy' );
			$localizations['translations']['warning_message']       = html_entity_decode( esc_attr__( 'There is unposted text in the reply area. Are you sure you want to discard and proceed?', 'supportcandy' ), ENT_QUOTES );
			$localizations['translations']['incorrect_login']       = esc_attr__( 'Incorrect username or password!', 'supportcandy' );
			$localizations['translations']['incorrect_password']    = esc_attr__( 'Incorrect password!', 'supportcandy' );
			$localizations['translations']['unsername_unavailable'] = esc_attr__( 'Username is already taken!', 'supportcandy' );
			$localizations['translations']['incorrect_email']       = esc_attr__( 'Incorrect email address!', 'supportcandy' );
			$localizations['translations']['copy_url']              = esc_attr__( 'Ticket URL copied!', 'supportcandy' );
			$localizations['translations']['invalidEmail']          = esc_attr__( 'Invalid email address!', 'supportcandy' );
			$localizations['translations']['req_term_cond']           = esc_attr__( 'Please accept terms and conditions!', 'supportcandy' );
			$localizations['translations']['req_gdpr']           = esc_attr__( 'Please accept GDPR policy!', 'supportcandy' );
			return $localizations;
		}

		/**
		 * JS functions to print for frontend only
		 *
		 * @return void
		 */
		public static function js_frontend() {

			// Check load scripts setting to load script on perticular page.
			$page_settings = get_option( 'wpsc-gs-page-settings' );
			if ( ! is_admin() && $page_settings['load-scripts'] == 'custom' && ! in_array( get_the_id(), $page_settings['load-script-pages'] ) ) {
				return;
			}
			?>
			<script>
				<?php do_action( 'wpsc_js_frontend' ); ?>
			</script>
			<style>
				<?php do_action( 'wpsc_css_frontend' ); ?>
			</style>
			<?php
		}

		/**
		 * JS functions to print for backend only
		 *
		 * @return void
		 */
		public static function js_backend() {
			?>
			<script>
				<?php do_action( 'wpsc_js_backend' ); ?>
			</script>
			<style>
				<?php do_action( 'wpsc_css_backend' ); ?>
			</style>
			<?php
		}
	}
endif;

WPSC_Framework::init();
