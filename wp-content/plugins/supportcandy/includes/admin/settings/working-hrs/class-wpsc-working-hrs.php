<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_Working_Hrs' ) ) :

	final class WPSC_Working_Hrs {

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
			add_action( 'wp_ajax_wpsc_get_working_hrs_settings', array( __CLASS__, 'get_working_hrs_settings' ) );
		}

		/**
		 * Load tabs for this section
		 */
		public static function load_tabs() {

			self::$tabs        = apply_filters(
				'wpsc_wh_tabs',
				array(
					'working-hrs' => array(
						'slug'     => 'working_hrs',
						'label'    => esc_attr__( 'Working Hours', 'supportcandy' ),
						'callback' => 'wpsc_get_working_hrs',
					),
					'holidays'    => array(
						'slug'     => 'holidays',
						'label'    => esc_attr__( 'Holidays', 'supportcandy' ),
						'callback' => 'wpsc_get_holidays',
					),
					'exceptions'  => array(
						'slug'     => 'exceptions',
						'label'    => esc_attr__( 'Exceptions', 'supportcandy' ),
						'callback' => 'wpsc_get_wh_exceptions',
					),
					'settings'    => array(
						'slug'     => 'settings',
						'label'    => esc_attr__( 'Settings', 'supportcandy' ),
						'callback' => 'wpsc_get_wh_settings',
					),
				)
			);
			self::$current_tab = isset( $_REQUEST['tab'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['tab'] ) ) : 'working-hrs'; // phpcs:ignore
		}


		/**
		 * Add localizations to local JS
		 *
		 * @param array $localizations - localization list.
		 * @return array
		 */
		public static function localizations( $localizations ) {

			if ( ! ( WPSC_Settings::$is_current_page && WPSC_Settings::$current_section === 'working-hrs' ) ) {
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
		public static function get_working_hrs_settings() {

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

WPSC_Working_Hrs::init();
