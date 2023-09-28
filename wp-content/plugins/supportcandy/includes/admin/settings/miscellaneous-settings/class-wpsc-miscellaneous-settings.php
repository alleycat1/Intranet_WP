<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_Miscellaneous_Settings' ) ) :

	final class WPSC_Miscellaneous_Settings {

		/**
		 * Tabs for this section
		 *
		 * @var array
		 */
		private static $tabs;

		/**
		 * Current tab
		 *
		 * @var string
		 */
		public static $current_tab;

		/**
		 * Initialize this class
		 *
		 * @return void
		 */
		public static function init() {

			// Load tabs for this section.
			add_action( 'admin_init', array( __CLASS__, 'load_tabs' ) );

			// Add current tab to admin localization data.
			add_filter( 'wpsc_admin_localizations', array( __CLASS__, 'localizations' ) );

			// Load section tab layout.
			add_action( 'wp_ajax_wpsc_get_miscellaneous_settings', array( __CLASS__, 'get_miscellaneous_settings' ) );
		}

		/**
		 * Load tabs for this section
		 */
		public static function load_tabs() {

			self::$tabs = apply_filters(
				'wpsc_ms_tabs',
				array(
					'term-and-conditions' => array(
						'slug'     => 'term_and_conditions',
						'label'    => esc_attr__( 'Term & Conditions', 'supportcandy' ),
						'callback' => 'wpsc_get_ms_term_and_conditions',
					),
					'gdpr'                => array(
						'slug'     => 'gdpr',
						'label'    => esc_attr__( 'GDPR', 'supportcandy' ),
						'callback' => 'wpsc_get_ms_gdpr',
					),
					'recaptcha'           => array(
						'slug'     => 'recaptcha',
						'label'    => esc_attr__( 'reCaptcha', 'supportcandy' ),
						'callback' => 'wpsc_get_ms_recaptcha',
					),
					'advanced'            => array(
						'slug'     => 'advanced',
						'label'    => esc_attr__( 'Advanced', 'supportcandy' ),
						'callback' => 'wpsc_get_ms_advanced',
					),
				)
			);
			self::$current_tab = isset( $_REQUEST['tab'] ) ? sanitize_text_field( $_REQUEST['tab'] ) : 'term-and-conditions'; // phpcs:ignore
		}

		/**
		 * Add localizations to local JS
		 *
		 * @param array $localizations - localizations.
		 * @return array
		 */
		public static function localizations( $localizations ) {

			if ( ! ( WPSC_Settings::$is_current_page && WPSC_Settings::$current_section === 'miscellaneous-settings' ) ) {
				return $localizations;
			}

			// Current section.
			$localizations['current_tab'] = self::$current_tab;

			return $localizations;
		}

		/**
		 * General setion body layout
		 *
		 * @return void
		 */
		public static function get_miscellaneous_settings() {

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}?>

			<div class="wpsc-setting-tab-container">
				<?php
				foreach ( self::$tabs as $key => $tab ) {
					$active = self::$current_tab === $key ? 'active' : ''
					?>
					<button 
						class="<?php echo esc_attr( $key ) . ' ' . esc_attr( $active ); ?>"
						onclick="<?php echo esc_attr( $tab['callback'] ) . '();'; ?>">
						<?php echo esc_attr( $tab['label'] ); ?>
						</button>
					<?php
				}
				?>
			</div>

			<div class="wpsc-setting-section-body"></div>
			<?php
			wp_die();
		}
	}
endif;

WPSC_Miscellaneous_Settings::init();
