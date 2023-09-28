<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_Admin' ) ) :

	final class WPSC_Admin {

		/**
		 * Initialize the class
		 *
		 * @return void
		 */
		public static function init() {

			add_action( 'admin_enqueue_scripts', array( __CLASS__, 'load_scripts' ) );
			add_action( 'admin_menu', array( __CLASS__, 'load_admin_menus' ), 10 );
		}

		/**
		 * Load JS and CSS scripts
		 *
		 * @return void
		 */
		public static function load_scripts() {

			if ( ! WPSC_Functions::is_wpsc_page() ) {
				return;
			}

			// jquery.
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'jquery-ui-core' );
			wp_enqueue_style( 'wpsc-jquery-ui', WPSC_PLUGIN_URL . 'asset/css/jquery-ui.css', array(), WPSC_VERSION );

			// TinyMCE.
			wp_enqueue_editor();

			// jQuery UI Effects.
			wp_enqueue_script( 'jquery-effects-core' );
			wp_enqueue_script( 'jquery-effects-slide' );

			// color picker.
			wp_enqueue_script( 'wp-color-picker' );
			wp_enqueue_style( 'wp-color-picker' );

			// Sortable.
			wp_enqueue_script( 'jquery-ui-sortable' );

			// Accordion.
			wp_enqueue_script( 'jquery-ui-accordion' );

			// Progrss bar.
			wp_enqueue_script( 'jquery-ui-progressbar' );

			// WPSC Scripts.
			wp_enqueue_script( 'wpsc-admin', WPSC_PLUGIN_URL . 'asset/js/admin.js', array( 'jquery' ), WPSC_VERSION, true );
			wp_localize_script( 'wpsc-admin', 'supportcandy', self::get_localization_data() );

			// WPSC Framework.
			wp_enqueue_script( 'wpsc-framework', WPSC_PLUGIN_URL . 'framework/scripts.js', array( 'jquery' ), WPSC_VERSION, true );

			if ( is_rtl() ) {
				wp_enqueue_style( 'wpsc-admin', WPSC_PLUGIN_URL . 'asset/css/admin-rtl.css', array(), WPSC_VERSION );
				wp_enqueue_style( 'wpsc-framework', WPSC_PLUGIN_URL . 'framework/style-rtl.css', array(), WPSC_VERSION );
			} else {
				wp_enqueue_style( 'wpsc-admin', WPSC_PLUGIN_URL . 'asset/css/admin.css', array(), WPSC_VERSION );
				wp_enqueue_style( 'wpsc-framework', WPSC_PLUGIN_URL . 'framework/style.css', array(), WPSC_VERSION );
			}

			// selectWoo.
			wp_enqueue_script( 'selectWoo', WPSC_PLUGIN_URL . 'asset/js/selectWoo/selectWoo.full.min.js', array( 'jquery' ), WPSC_VERSION, true );
			if ( file_exists( WPSC_ABSPATH . 'asset/js/selectWoo/i18n/' . get_locale() . '.js' ) ) {
				wp_enqueue_script( 'selectWoo-lang', WPSC_PLUGIN_URL . 'asset/js/selectWoo/i18n/' . get_locale() . '.js', array( 'jquery' ), WPSC_VERSION, true );
			}
			wp_enqueue_style( 'select2', WPSC_PLUGIN_URL . 'asset/css/select2.css', array(), WPSC_VERSION );

			// gpopover.
			wp_enqueue_script( 'gpopover', WPSC_PLUGIN_URL . 'asset/libs/gpopover/jquery.gpopover.js', array( 'jquery' ), WPSC_VERSION, true );
			wp_enqueue_style( 'gpopover', WPSC_PLUGIN_URL . 'asset/libs/gpopover/jquery.gpopover.css', array(), WPSC_VERSION );

			// jquery circle progress.
			wp_enqueue_script( 'jquery-circle-progress', WPSC_PLUGIN_URL . 'asset/libs/jquery-circle-progress/circle-progress.min.js', array( 'jquery' ), WPSC_VERSION, true );

			// flatpickr.
			wp_enqueue_script( 'flatpickr', WPSC_PLUGIN_URL . 'asset/libs/flatpickr/flatpickr.js', array( 'jquery' ), WPSC_VERSION, true );
			wp_enqueue_style( 'flatpickr', WPSC_PLUGIN_URL . 'asset/libs/flatpickr/flatpickr.min.css', array(), WPSC_VERSION );

			if ( file_exists( WPSC_ABSPATH . 'asset/libs/flatpickr/l10n/' . WPSC_Functions::get_locale_iso() . '.js' ) ) {
				wp_enqueue_script( 'flatpickr-lang', WPSC_PLUGIN_URL . 'asset/libs/flatpickr/l10n/' . WPSC_Functions::get_locale_iso() . '.js', array( 'jquery' ), WPSC_VERSION, true );
			}

			// fullcalendar.
			wp_enqueue_script( 'fullcalendar', WPSC_PLUGIN_URL . 'asset/libs/fullcalendar/lib/main.min.js', array( 'jquery' ), WPSC_VERSION, true );
			wp_enqueue_script( 'fullcalendar-locales', WPSC_PLUGIN_URL . 'asset/libs/fullcalendar/lib/locales-all.min.js', array( 'jquery' ), WPSC_VERSION, true );
			wp_enqueue_style( 'fullcalendar', WPSC_PLUGIN_URL . 'asset/libs/fullcalendar/lib/main.min.css', array(), WPSC_VERSION );

			// DataTables.
			wp_enqueue_script( 'datatables', WPSC_PLUGIN_URL . 'asset/libs/DataTables/datatables.min.js', array( 'jquery' ), WPSC_VERSION, true );
			wp_enqueue_style( 'datatables', WPSC_PLUGIN_URL . 'asset/libs/DataTables/datatables.min.css', array(), WPSC_VERSION );
		}

		/**
		 * Load admin/dashboard menus
		 *
		 * @return void
		 */
		public static function load_admin_menus() {

			$current_user = WPSC_Current_User::$current_user;
			$count_str    = '';
			if ( $current_user->is_agent && $current_user->agent->unresolved_count > 0 ) {
				$count_str = ' <span class="update-plugins wpsc-uc"><span class="plugin-count">' . $current_user->agent->unresolved_count . '</span></span>';
			}

			add_menu_page(
				esc_attr__( 'Support', 'supportcandy' ),
				esc_attr__( 'Support', 'supportcandy' ) . $count_str,
				'wpsc_agent',
				'wpsc-tickets',
				array( 'WPSC_Tickets', 'layout' ),
				'dashicons-sos',
				25
			);

			add_submenu_page(
				'wpsc-tickets',
				esc_attr__( 'Ticket List', 'supportcandy' ),
				esc_attr__( 'Tickets', 'supportcandy' ),
				'wpsc_agent',
				'wpsc-tickets',
				array( 'WPSC_Tickets', 'layout' )
			);

			add_submenu_page(
				'wpsc-tickets',
				esc_attr__( 'Customers', 'supportcandy' ),
				esc_attr__( 'Customers', 'supportcandy' ),
				'manage_options',
				'wpsc-customers',
				array( 'WPSC_Customers', 'layout' )
			);

			add_submenu_page(
				'wpsc-tickets',
				esc_attr__( 'Support Agents', 'supportcandy' ),
				esc_attr__( 'Support Agents', 'supportcandy' ),
				'manage_options',
				'wpsc-support-agents',
				array( 'WPSC_Support_Agents', 'layout' )
			);

			add_submenu_page(
				'wpsc-tickets',
				esc_attr__( 'Custom Fields', 'supportcandy' ),
				esc_attr__( 'Custom Fields', 'supportcandy' ),
				'manage_options',
				'wpsc-ticket-form',
				array( 'WPSC_CF_Settings', 'layout' )
			);

			add_submenu_page(
				'wpsc-tickets',
				esc_attr__( 'Ticket List', 'supportcandy' ),
				esc_attr__( 'Ticket List', 'supportcandy' ),
				'manage_options',
				'wpsc-ticket-list',
				array( 'WPSC_Ticket_List_Settings', 'layout' )
			);

			add_submenu_page(
				'wpsc-tickets',
				esc_attr__( 'Email Notifications', 'supportcandy' ),
				esc_attr__( 'Email Notifications', 'supportcandy' ),
				'manage_options',
				'wpsc-email-notifications',
				array( 'WPSC_EN_Settings', 'layout' )
			);

			do_action( 'wpsc_before_setting_admin_menu' );

			add_submenu_page(
				'wpsc-tickets',
				esc_attr__( 'Settings', 'supportcandy' ),
				esc_attr__( 'Settings', 'supportcandy' ),
				'manage_options',
				'wpsc-settings',
				array( 'WPSC_Settings', 'layout' )
			);

			add_submenu_page(
				'wpsc-tickets',
				esc_attr__( 'Licenses', 'supportcandy' ),
				esc_attr__( 'Licenses', 'supportcandy' ),
				'manage_options',
				'wpsc-license',
				array( 'WPSC_License', 'layout' )
			);

			add_submenu_page(
				'wpsc-tickets',
				esc_attr__( 'Pro Features', 'supportcandy' ),
				'<strong style="color: #F1C40F;">' . esc_attr__( 'Pro Features', 'supportcandy' ) . '</strong>',
				'manage_options',
				'wpsc-add-ons',
				array( 'WPSC_Addons', 'layout' )
			);

			// hidden submenu page for manual scheduled tasks.
			add_submenu_page(
				'wpsc-tickets',
				esc_attr__( 'Task Manager', 'supportcandy' ),
				null,
				'manage_options',
				'wpsc-task-manager',
				array( 'WPSC_Task_Scheduler', 'perform_manual_scheduler' )
			);
		}

		/**
		 * Get localization data for the admin scripts
		 *
		 * @return string
		 */
		private static function get_localization_data() {

			$gs            = get_option( 'wpsc-gs-general' );
			$file_settings = get_option( 'wpsc-gs-file-attachments' );
			$localizations = array(
				'ajax_url'                => admin_url( 'admin-ajax.php' ),
				'plugin_url'              => WPSC_PLUGIN_URL,
				'version'                 => WPSC_VERSION,
				'loader_html'             => WPSC_Framework::loader_html(),
				'inline_loader'           => WPSC_Framework::inline_loader(),
				'is_frontend'             => 0,
				'reply_form_position'     => $gs['reply-form-position'],
				'allowed_file_extensions' => array_map( 'trim', array_map( 'strtolower', explode( ',', $file_settings['allowed-file-extensions'] ) ) ),
				'nonce'                   => wp_create_nonce( 'general' ),
				'translations'            => array(
					'req_fields_error' => esc_attr__( 'Required fields can not be empty!', 'supportcandy' ),
					'datatables'       => array(
						'emptyTable'     => esc_attr__( 'No Records', 'supportcandy' ),
						'zeroRecords'    => esc_attr__( 'No Records', 'supportcandy' ),
						'info'           => sprintf(
							/* translators: e.g. Showing 1 to 20 of 300 records */
							esc_attr__( 'Showing %1$s to %2$s of %3$s records', 'supportcandy' ),
							'_START_',
							'_END_',
							'_TOTAL_'
						),
						'infoEmpty'      => '',
						'loadingRecords' => '',
						'processing'     => '',
						'infoFiltered'   => '',
						'search'         => esc_attr__( 'Search:', 'supportcandy' ),
						'paginate'       => array(
							'first'    => esc_attr__( 'First', 'supportcandy' ),
							'previous' => esc_attr__( 'Previous', 'supportcandy' ),
							'next'     => esc_attr__( 'Next', 'supportcandy' ),
							'last'     => esc_attr__( 'Last', 'supportcandy' ),
						),
					),
				),
				'temp'                    => array(),
				'home_url'                => home_url(),
			);

			return apply_filters( 'wpsc_admin_localizations', $localizations );
		}
	}

endif;

WPSC_Admin::init();
