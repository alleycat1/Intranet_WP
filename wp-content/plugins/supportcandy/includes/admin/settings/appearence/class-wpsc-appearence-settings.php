<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_Appearence_Settings' ) ) :

	final class WPSC_Appearence_Settings {

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
			add_action( 'wp_ajax_wpsc_get_appearence_settings', array( __CLASS__, 'get_appearence_settings' ) );
		}

		/**
		 * Load tabs for this section
		 */
		public static function load_tabs() {

			self::$tabs        = apply_filters(
				'wpsc_gs_appearance_tabs',
				array(
					'general'           => array(
						'slug'     => 'general',
						'label'    => esc_attr__( 'General', 'supportcandy' ),
						'callback' => 'wpsc_get_ap_general',
					),
					'ticket-list'       => array(
						'slug'     => 'ticket_list',
						'label'    => esc_attr__( 'Ticket List', 'supportcandy' ),
						'callback' => 'wpsc_get_ap_ticket_list',
					),
					'individual-ticket' => array(
						'slug'     => 'individual_ticket',
						'label'    => esc_attr__( 'Individual Ticket', 'supportcandy' ),
						'callback' => 'wpsc_get_ap_individual_ticket',
					),
					'modal-popup'       => array(
						'slug'     => 'modal_popup',
						'label'    => esc_attr__( 'Modal Popup', 'supportcandy' ),
						'callback' => 'wpsc_get_ap_modal_popup',
					),
					'agent-collision'       => array(
						'slug'     => 'agent_collision',
						'label'    => esc_attr__( 'Agent Collision', 'supportcandy' ),
						'callback' => 'wpsc_get_ap_agent_collision',
					),
				)
			);
			self::$current_tab = isset( $_REQUEST['tab'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['tab'] ) ) : 'general'; // phpcs:ignore
		}

		/**
		 * Add localizations to local JS
		 *
		 * @param array $localizations - localization.
		 * @return array
		 */
		public static function localizations( $localizations ) {

			if ( ! ( WPSC_Settings::$is_current_page && WPSC_Settings::$current_section === 'appearence' ) ) {
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
		public static function get_appearence_settings() {

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

WPSC_Appearence_Settings::init();
