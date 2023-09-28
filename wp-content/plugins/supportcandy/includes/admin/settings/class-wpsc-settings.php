<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_Settings' ) ) :

	final class WPSC_Settings {

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
		 *
		 * @return void
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
		 * Load sections of this setting
		 *
		 * @return void
		 */
		public static function load_sections() {

			self::$is_current_page = isset( $_REQUEST['page'] ) && $_REQUEST['page'] === 'wpsc-settings' ? true : false; //phpcs:ignore

			if ( ! self::$is_current_page ) {
				return;
			}

			self::$sections = apply_filters(
				'wpsc_settings_page_sections',
				array(
					'general-settings'       => array(
						'slug'     => 'general_settings',
						'icon'     => 'control',
						'label'    => esc_attr__( 'General Settings', 'supportcandy' ),
						'callback' => 'wpsc_get_general_settings',
					),
					'ticket-categories'      => array(
						'slug'     => 'ticket_categories',
						'icon'     => 'subfolder',
						'label'    => esc_attr__( 'Ticket Categories', 'supportcandy' ),
						'callback' => 'wpsc_get_ticket_categories',
					),
					'ticket-statuses'        => array(
						'slug'     => 'ticket_statuses',
						'icon'     => 'gps-navigation',
						'label'    => esc_attr__( 'Ticket Statuses', 'supportcandy' ),
						'callback' => 'wpsc_get_ticket_statuses',
					),
					'ticket-priorities'      => array(
						'slug'     => 'ticket_priorities',
						'icon'     => 'prioritize',
						'label'    => esc_attr__( 'Ticket Priorities', 'supportcandy' ),
						'callback' => 'wpsc_get_ticket_priorities',
					),
					'miscellaneous-settings' => array(
						'slug'     => 'miscellaneous_settings',
						'icon'     => 'cogs',
						'label'    => esc_attr__( 'Miscellaneous', 'supportcandy' ),
						'callback' => 'wpsc_get_miscellaneous_settings',
					),
					'ticket-widgets'         => array(
						'slug'     => 'ticket_widgets',
						'icon'     => 'widget',
						'label'    => esc_attr__( 'Ticket Widgets', 'supportcandy' ),
						'callback' => 'wpsc_get_ticket_widget',
					),
					'rich-text-editor'       => array(
						'slug'     => 'rich_text_editor',
						'icon'     => 'font',
						'label'    => esc_attr__( 'Rich Text Editor', 'supportcandy' ),
						'callback' => 'wpsc_get_rich_text_editor',
					),
					'working-hrs'            => array(
						'slug'     => 'working_hrs',
						'icon'     => 'clock',
						'label'    => esc_attr__( 'Working Hours', 'supportcandy' ),
						'callback' => 'wpsc_get_working_hrs_settings',
					),
					'appearence'             => array(
						'slug'     => 'appearence',
						'icon'     => 'palette',
						'label'    => esc_attr__( 'Appearance', 'supportcandy' ),
						'callback' => 'wpsc_get_appearence_settings',
					),
					'ticket-tags'            => array(
						'slug'     => 'ticket_tags',
						'icon'     => 'tags',
						'label'    => esc_attr__( 'Ticket Tags', 'supportcandy' ),
						'callback' => 'wpsc_get_ticket_tags_settings',
					),
				)
			);

			self::$current_section = isset( $_REQUEST['section'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['section'] ) ) : 'general-settings'; //phpcs:ignore
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
		 * Render settings to admin
		 *
		 * @return void
		 */
		public static function layout() {
			?>
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
							<h2><?php esc_attr_e( 'Settings', 'supportcandy' ); ?></h2>
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

WPSC_Settings::init();

