<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_GS' ) ) :

	final class WPSC_GS {

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
			add_action( 'wp_ajax_wpsc_get_general_settings', array( __CLASS__, 'get_general_settings' ) );
		}

		/**
		 * Load tabs for this section
		 */
		public static function load_tabs() {

			self::$tabs        = apply_filters(
				'wpsc_gs_tabs',
				array(
					'general'          => array(
						'slug'     => 'general',
						'label'    => esc_attr__( 'General', 'supportcandy' ),
						'callback' => 'wpsc_get_gs_general',
					),
					'page-settings'    => array(
						'slug'     => 'page_settings',
						'label'    => esc_attr__( 'Page Settings', 'supportcandy' ),
						'callback' => 'wpsc_get_gs_page_settings',
					),
					'file-attachments' => array(
						'slug'     => 'file_attachments',
						'label'    => esc_attr__( 'File Attachments', 'supportcandy' ),
						'callback' => 'wpsc_get_gs_file_attachments',
					),
					'thank-you-page'   => array(
						'slug'     => 'thank_you_page',
						'label'    => esc_attr__( 'Thank you page', 'supportcandy' ),
						'callback' => 'wpsc_get_gs_thankyou',
					),
				)
			);
			self::$current_tab = isset( $_REQUEST['tab'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['tab'] ) ) : 'general'; // phpcs:ignore
		}

		/**
		 * Add localizations to local JS
		 *
		 * @param array $localizations - localizations.
		 * @return array
		 */
		public static function localizations( $localizations ) {

			if ( ! ( WPSC_Settings::$is_current_page && WPSC_Settings::$current_section === 'general-settings' ) ) {
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
		public static function get_general_settings() {

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}?>

			<div class="wpsc-setting-tab-container">
				<?php
				foreach ( self::$tabs as $key => $tab ) :
					$active = self::$current_tab === $key ? 'active' : ''
					?>
					<button 
						class="<?php echo esc_attr( $key ) . ' ' . esc_attr( $active ); ?>"
						onclick="<?php echo esc_attr( $tab['callback'] ) . '();'; ?>">
						<?php echo esc_attr( $tab['label'] ); ?>
						</button>
					<?php
				endforeach;
				?>
			</div>
			<div class="wpsc-setting-section-body"></div>
			<?php
			wp_die();
		}
	}
endif;

WPSC_GS::init();
