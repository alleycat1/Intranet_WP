<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_EN_Settings' ) ) :

	final class WPSC_EN_Settings {

		/**
		 * Set if current screen is settings page
		 *
		 * @var boolean
		 */
		public static $is_current_page;

		/**
		 * Sections for this view
		 *
		 * @var array
		 */
		private static $sections;

		/**
		 * Current section to load
		 *
		 * @var string
		 */
		public static $current_section;

		/**
		 * Initialize this class
		 */
		public static function init() {

			// Load sections for this screen.
			add_action( 'admin_init', array( __CLASS__, 'load_sections' ), 1 );

			// Humbargar modal.
			add_action( 'admin_footer', array( __CLASS__, 'humbargar_menu' ) );

			// Add current section to admin localization data.
			add_filter( 'wpsc_admin_localizations', array( __CLASS__, 'localizations' ) );

			// Register ready function.
			add_action( 'wpsc_js_ready', array( __CLASS__, 'register_js_ready_function' ) );
		}

		/**
		 * Load section (nav elements) for this screen
		 *
		 * @return void
		 */
		public static function load_sections() {

			self::$is_current_page = isset( $_REQUEST['page'] ) && $_REQUEST['page'] === 'wpsc-email-notifications' ? true : false; // phpcs:ignore

			if ( ! self::$is_current_page ) {
				return;
			}

			self::$sections = apply_filters(
				'wpsc_email_notification_page_sections',
				array(
					'general-settings'     => array(
						'slug'     => 'general_settings',
						'icon'     => 'control',
						'label'    => esc_attr__( 'General Settings', 'supportcandy' ),
						'callback' => 'wpsc_get_en_general_setting',
					),
					'ticket-notifications' => array(
						'slug'     => 'ticket_notifications',
						'icon'     => 'envelope',
						'label'    => esc_attr__( 'Ticket Notifications', 'supportcandy' ),
						'callback' => 'wpsc_get_ticket_notifications',
					),
				)
			);

			self::$current_section = isset( $_REQUEST['section'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['section'] ) ) : 'general-settings'; // phpcs:ignore
		}

		/**
		 * Add localizations to local JS
		 *
		 * @param array $localizations - localization.
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

			return $localizations;
		}

		/**
		 * UI foundation for this screen
		 *
		 * @return void
		 */
		public static function layout() {?>

			<div class="wrap">
				<hr class="wp-header-end">
				<div id="wpsc-container" style="display:none;">
					<div class="wpsc-header wpsc-setting-header-xs wpsc-visible-xs">
						<div class="wpsc-humbargar-title">
							<?php WPSC_Icons::get( self::$sections[ self::$current_section ]['icon'] ); ?>
							<label><?php echo esc_attr( self::$sections[ self::$current_section ]['label'] ); ?></label>
						</div>
						<div class="wpsc-humbargar" onclick="wpsc_toggle_humbargar();">
							<?php WPSC_Icons::get( 'bars' ); ?>
						</div>
					</div>
					<div class="wpsc-settings-page">
						<div class="wpsc-setting-section-container wpsc-hidden-xs">
							<h2><?php esc_attr_e( 'Email Notifications', 'supportcandy' ); ?></h2>
								<?php
								foreach ( self::$sections as $key => $section ) {
									$active = self::$current_section === $key ? 'active' : '';
									?>
								<div 
									class="wpsc-setting-nav <?php echo esc_attr( $key ) . ' ' . esc_attr( $active ); ?>"
									onclick="<?php echo esc_attr( $section['callback'] ) . '();'; ?>">
									<?php WPSC_Icons::get( $section['icon'] ); ?>
									<label><?php echo esc_attr( $section['label'] ); ?></label>
								</div>
									<?php
								}
								?>
						</div>
						<div class="wpsc-setting-body"></div>
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
						<?php
					endforeach;
					?>
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
	}

endif;

WPSC_EN_Settings::init();
