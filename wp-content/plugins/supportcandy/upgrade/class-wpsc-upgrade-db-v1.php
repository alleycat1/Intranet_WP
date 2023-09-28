<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_Upgrade_DB_V1' ) ) :

	final class WPSC_Upgrade_DB_V1 {

		/**
		 * Number of posts to be processed per page
		 *
		 * @var integer
		 */
		public static $posts_per_page = 20;

		/**
		 * Initialize this class
		 *
		 * @return void
		 */
		public static function init() {

			add_action( 'init', 'wpsc_upgrade_send_admin_email' );
			add_action( 'admin_menu', array( __CLASS__, 'load_admin_menus' ), 11 );
			add_action( 'admin_enqueue_scripts', 'wpsc_upgrade_enqueue_scripts' );

			// register custom post typs for upgrade.
			add_action( 'init', 'wpsc_upgrade_register_post_type' );

			// check compatibility.
			add_action( 'wp_ajax_wpsc_authorize_v1_upgrade', array( __CLASS__, 'authorize' ) );
			add_action( 'wp_ajax_wpsc_upgrade_v1_check_compatibility', array( __CLASS__, 'check_compatibility' ) );

			// cron manager.
			add_action( 'init', array( __CLASS__, 'cron_manager' ) );
			add_action( 'wp_ajax_wpsc_v1_upgrade_status', array( __CLASS__, 'get_v1_upgrade_cron_status' ) );
			add_action( 'wpsc_upgrade_v1', array( __CLASS__, 'upgrade' ) );
		}

		/**
		 * Load upgrade screen for admin menu
		 *
		 * @return void
		 */
		public static function load_admin_menus() {

			add_menu_page(
				esc_attr__( 'Support', 'supportcandy' ),
				esc_attr__( 'Support', 'supportcandy' ),
				'manage_options',
				'wpsc-tickets',
				array( __CLASS__, 'upgrade_layout' ),
				'dashicons-sos',
				25
			);
		}

		/**
		 * Authorize upgrade by an administrator
		 *
		 * @return void
		 */
		public static function authorize() {

			if ( ! wpsc_upgrade_is_site_admin() ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			if ( check_ajax_referer( 'wpsc_authorize_v1_upgrade', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			update_option( 'wpsc_upgrade_permission_v1', 1 );
			wp_send_json( array( 'success' => true ) );
		}

		/**
		 * Upgrade layout caller
		 *
		 * @return void
		 */
		public static function upgrade_layout() {

			$upgrade_permission = get_option( 'wpsc_upgrade_permission_v1', 0 );
			?>
			<div class="wrap">
				<hr class="wp-header-end">
				<h1>SupportCandy v3.0</h1>
				<div class="wpsc-upgrade-container">
					<?php
					if ( ! $upgrade_permission ) {
						?>
						<p>SupportCandy v3.0 is a major release. This version has significant changes that may break your current workflow. Please review the below changes that may be responsible for breaking your current workflow if it is dependent on them. <strong>We strongly recommend you check this on your test or staging site before the live site.</strong></p>
						<ol>
							<li>You must update all premium add-ons (if any).</li>
							<li>If you took any paid customizations from us, it will not work. Please contact us on <a href="mailto:support@supportcandy.net">support@supportcandy.net</a> to know when you will get your customization delivered. Until then you must revert to the previous version.</li>
							<li>All hooks and filters are changed.</li>
							<li>Translations are subject to availability. <a href="https://wordpress.org/plugins/supportcandy/">Click here</a> to see list of translations available.</li>
							<li>User interface design is changed. Previous appearance settings will not work on the new interface.</li>
						</ol>
						<p>It will migrate your tickets to custom database tables to improve performance and efficiency. This process may take a while and will make <strong>irreversible</strong> changes to the database. Please create a <strong>complete backup of your website</strong> before proceeding.</p>
						<p><input type="checkbox" class="wpsc-confirm-backup"> I have secured a backup of my website data.</p>
						<p><input type="checkbox" class="wpsc-confirm-read"> I have read and understood all the points above.</p>
						<p>
							<button disabled class="button action proceed">Proceed to upgrade</button>
							<script>
								jQuery('.wpsc-confirm-backup, .wpsc-confirm-read').change(function(){
									let cb1 = jQuery('.wpsc-confirm-backup');
									let cb2 = jQuery('.wpsc-confirm-read');
									if ( cb1.is(":checked") && cb2.is(":checked") ) {
										jQuery('button.proceed').attr( 'disabled', false );
									} else {
										jQuery('button.proceed').attr( 'disabled', true );
									}
								});
							</script>
						</p>
						<h3 style="margin-top: 50px;">How do I revert this to the previous version?</h3>
						<p>If you are not ready to upgrade yet, you can revert to the previous version of SupportCandy and its add-ons with the help of the below steps:</p>
						<h4 style="margin-top: 30px;">Revert core plugin</h4>
						<p>
							<ol>
								<li><a href="https://downloads.wordpress.org/plugin/supportcandy.2.3.1.zip">Click here</a> to download the previous version, v2.3.1</li>
								<li>Go to the Plugins page in your WordPress dashboard.</li>
								<li>Deactivate and delete SupportCandy.</li>
								<li>Click Add New plugin.</li>
								<li>Click Upload Plugin.</li>
								<li>Upload the downloaded zip file in step 1.</li>
								<li>Activate</li>
							</ol>
						</p>
						<h4 style="margin-top: 30px;">Revert add-ons (if any)</h4>
						<p>
							If you have premium add-ons installed for SupportCandy, you can follow the below steps to revert them:
							<ol>
								<li><a href="https://supportcandy.net/account/" target="_blank">Click here</a> to go to the My Account page on our website.</li>
								<li>Login to your account.</li>
								<li>Click on View Details and Downloads for your recent order.</li>
								<li>Download previous versions for each add-on ( less than 3.0.0 ).</li>
								<li>Go to the Plugins page in your WordPress dashboard.</li>
								<li>Deactivate and delete each SupportCandy add-on.</li>
								<li>Click Add New and then install each add-on by uploading it.</li>
								<li>Activate each add-on.</li>
							</ol>
						</p>
						<?php
					}
					?>
				</div>
			</div>
			<script>
				<?php
				if ( $upgrade_permission ) {
					?>
					jQuery(document).ready(function(){
						wpsc_upgrade_v1_load_compatibilty();
					});
					<?php
				} else {
					?>
					jQuery('button.proceed').click(function(){
						jQuery('div.wpsc-upgrade-container').html(supportcandy.loader_html);
						var data = {
							action: 'wpsc_authorize_v1_upgrade',
							_ajax_nonce: '<?php echo esc_attr( wp_create_nonce( 'wpsc_authorize_v1_upgrade' ) ); ?>'
						};
						jQuery.post(supportcandy.ajax_url, data, function (response) {
							if ( response.success ) {
								wpsc_upgrade_v1_load_compatibilty();
							}
						});
					});
					<?php
				}
				?>
				/**
				 * Load compatibility section
				 *
				 * @return void
				 */
				function wpsc_upgrade_v1_load_compatibilty() {
					jQuery('div.wpsc-upgrade-container').html(supportcandy.loader_html);
					var data = {
						action: 'wpsc_upgrade_v1_check_compatibility',
						_ajax_nonce: '<?php echo esc_attr( wp_create_nonce( 'wpsc_upgrade_v1_check_compatibility' ) ); ?>'
					};
					jQuery.post(supportcandy.ajax_url, data, function (response) {
						jQuery('div.wpsc-upgrade-container').html(response);
					});
				}
			</script>
			<style>
				body {
					background-color: #fff;
				}
				p, ol {
					font-size: 15px;
				}
			</style>
			<?php
		}

		/**
		 * Check v3 compatiblity of current installation
		 *
		 * @return void
		 */
		public static function check_compatibility() {

			global $wpdb, $current_user;

			$upgrade_permission = get_option( 'wpsc_upgrade_permission_v1', 0 );

			if ( ! wpsc_upgrade_is_site_admin() || ! $upgrade_permission ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			if ( check_ajax_referer( 'wpsc_upgrade_v1_check_compatibility', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			$errors = array();

			// check php version.
			if ( version_compare( PHP_VERSION, '7.4.0', '<' ) ) {
				$errors[] = 'PHP version must be 7.4 and above, current version is ' . PHP_VERSION;
			}

			// check WordPress version.
			$wp_version = get_bloginfo( 'version' );
			if ( $wp_version < '5.6' ) {
				$errors[] = 'WordPress version must be 5.2 and above, current version is ' . $wp_version;
			}

			// check add-on versions.
			$installed_addons = wpsc_upgrade_get_installed_plugin_info();
			update_option( 'wpsc_upgrade_installed_addons', $installed_addons );

			$plugins_url = admin_url( 'plugins.php' );
			foreach ( $installed_addons as $addon ) {
				if ( $addon['is_installed'] && $addon['version'] < '3.0.0' ) {

					$errors[] = '<strong>' . $addon['name'] . '</strong> must be v3.0 and above. <a href="' . $plugins_url . '">Click here</a> to see updates.';

				} elseif ( $addon['is_installed'] && $addon['version'] == '3.0.0' && ! $addon['is_active'] ) {

					$errors[] = '<strong>' . $addon['name'] . '</strong> must be active in order to upgrade data related to it.';
				}
			}

			// check for customizations.
			if ( class_exists( 'WPSC_CUST' ) ) {
				$errors[] = 'Customization detected! Please contact us on <a href="mailto:support@supportcandy.net">support@supportcandy.net</a> from your registered email address in order to plan re-development of your customization. Until then, you need to use previous version of SupportCandy.';
			}

			if ( $errors ) {

				delete_option( 'wpsc_upgrade_permission_v1' );

				?>
				<p>Please fix below problems:</p>
				<ol>
					<?php
					foreach ( $errors as $error ) {
						echo '<li>' . wp_kses_post( $error ) . '</li>';
					}
					?>
				</ol>
				<?php

			} else {

				$cron_status = get_option( 'wpsc_v1_upgrade_cron_status' );
				$percentage = 100;
				if ( $cron_status['total_pages'] ) {
					$percentage = round( ( $cron_status['page'] / $cron_status['total_pages'] ) * 100, 2 );
				}
				?>
				<p>This process can take several hours to complete depending your database size.</p>
				<p>Please keep this tab open for faster upgrade. If you close this tab, you need server scheduled cron job for the site.</p>
				<i class="wpsc-progressbar-title"><?php echo esc_html( $cron_status['title'] ); ?></i>
				<div id="wpsc-progressbar"><div class="progress-label"><?php echo esc_html( $percentage ) . '%'; ?></div></div>

				<!-- Server cron setup instructions -->
				<h3 style="margin-top: 50px;">Why server scheduled cron job?</h3>
				<p>WordPress has the default WP-Cron enabled for you, which only gets executed when the website has a visitor. Therefore, it is not suitable for time-critical events like sending an email, pulling emails for email piping, scheduled backups, scheduled upgrade (like this) etc. Also, visitors may experience slowness in page loading because WP-Cron has to finish its job before it renders output for the visitor.</p>
				<h3 style="margin-top: 50px;">How to setup server scheduled cron job?</h3>
				<h4>Step 1: Disable WP-Cron</h4>
				<p>You can disable WP-Cron by adding the below line to your wp-config.php file.</p>
				<code>define('DISABLE_WP_CRON', true);</code>
				<h4>Step 2: Schedule cron on your server</h4>
				<p>To schedule cron on your server, you must log in to cPanel (or your hosting interface). Then, add a new cron job with the below command, which will execute cron every minute.</p>
				<code>*/1 * * * * wget -qO- <?php echo esc_url( site_url( '/wp-cron.php' ) ); ?> &> /dev/null</code>
				<p>The reasonable time interval is 5-15 minutes. That is */5 * * * * or */15 * * * * for Cron interval setting. The longer the cron execution time, the longer it will take to complete this upgrade. You can change the interval from every minute to 5 or 15 minutes later.</p>


				<style>
					.ui-progressbar {
						position: relative;
					}
					.progress-label {
						position: absolute;
						left: 50%;
						top: 4px;
						font-weight: bold;
						text-shadow: 1px 1px 0 #fff;
					}
				</style>
				<script>
					var progressbar = jQuery( "#wpsc-progressbar" );
					var progressLabel = jQuery( ".progress-label" );
					var progressbarTitle = jQuery( ".wpsc-progressbar-title" );
					progressbar.progressbar({ value: <?php echo intval( $percentage ); ?> });
					wpsc_v1_upgrade_status();

					/**
					 * Get cron status
					 *
					 * @return void
					 */
					function wpsc_v1_upgrade_status() {
						var data = { action: 'wpsc_v1_upgrade_status' };
						jQuery.post(supportcandy.ajax_url, data, function (response) {
							if ( response.success && response.status == 'running' ) {
								let percentage = 100;
								if ( response.total_pages ) {
									percentage = ( ( response.page/response.total_pages ) * 100 ).toFixed(2);
								}
								progressbar.progressbar({ value: parseInt( percentage ) });
								progressLabel.text( percentage + '%' );
								progressbarTitle.text( response.title );
								wpsc_v1_upgrade_status();
							} else if ( response.success && response.status == 'complete' ) {
								window.location.reload();
							}
						}).fail( function( xhr, status, error ) {
							window.location.reload();
						});
					}
				</script>
				<?php
			}

			wp_die();
		}

		/**
		 * Cron manager for upgrade
		 *
		 * @return void
		 */
		public static function cron_manager() {

			$cron_status = get_option( 'wpsc_v1_upgrade_cron_status', array() );
			if ( ! $cron_status ) {
				$cron_status = array(
					'status'      => 'running',
					'title'       => 'Installing database tables',
					'resource'    => 'db_tables',
					'page'        => 0,
					'total_pages' => 1,
				);
				update_option( 'wpsc_v1_upgrade_cron_status', $cron_status );
			}

			// Do not proceed for the first iteration via cron.
			if ( $cron_status['resource'] == 'db_tables' ) {
				return;
			}

			// Do not allow parallel process to run.
			if ( 'yes' === get_transient( 'wpsc_ui_upgrading' ) ) {
				return;
			}

			$upgrade_permission = get_option( 'wpsc_upgrade_permission_v1', 0 );
			if ( $upgrade_permission && $cron_status['status'] == 'running' && ! wp_next_scheduled( 'wpsc_upgrade_v1' ) ) {
				wp_schedule_single_event( time(), 'wpsc_upgrade_v1' );
			}
		}

		/**
		 * Return cron status for v1 upgrade
		 *
		 * @return void
		 */
		public static function get_v1_upgrade_cron_status() {

			if ( ! wpsc_upgrade_is_site_admin() ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			// Set transient.
			set_transient( 'wpsc_ui_upgrading', 'yes', MINUTE_IN_SECONDS * 2 );

			do_action( 'wpsc_upgrade_v1' );

			$cron_status = get_option( 'wpsc_v1_upgrade_cron_status' );
			$cron_status['success'] = true;
			wp_send_json( $cron_status );
		}

		/**
		 * Upgrade the resource
		 *
		 * @return void
		 */
		public static function upgrade() {

			global $wpdb;

			// Do not allow parallel process to run.
			if ( 'yes' === get_transient( 'wpsc_upgrading' ) ) {
				return;
			}

			// Set transient.
			set_transient( 'wpsc_upgrading', 'yes', MINUTE_IN_SECONDS * 2 );

			$cron_status = get_option( 'wpsc_v1_upgrade_cron_status' );
			switch ( $cron_status['resource'] ) {

				case 'db_tables':
					if ( $cron_status['page'] == $cron_status['total_pages'] ) {
						$total_fields = count( get_terms( 'wpsc_ticket_custom_fields', array( 'hide_empty' => false ) ) );
						$total_pages = $total_fields ? ceil( $total_fields / 10 ) : 0;
						$cron_status = array(
							'status'      => 'running',
							'title'       => 'Importing custom fields',
							'resource'    => 'custom_fields',
							'page'        => 0,
							'total_pages' => $total_pages,
						);
					}
					break;

				case 'custom_fields':
					if ( $cron_status['page'] == $cron_status['total_pages'] ) {
						$cron_status = array(
							'status'      => 'running',
							'title'       => 'Importing settings',
							'resource'    => 'settings',
							'page'        => 0,
							'total_pages' => 6,
						);
					}
					break;

				case 'settings':
					if ( $cron_status['page'] == $cron_status['total_pages'] ) {
						$results = new WP_Query(
							array(
								'post_type'   => 'wpsc_ticket',
								'post_status' => array( 'publish', 'trash' ),
							)
						);
						$total_pages = $results->found_posts ? ceil( $results->found_posts / 5 ) : 0;
						$cron_status = array(
							'status'      => 'running',
							'title'       => 'Importing tickets',
							'resource'    => 'tickets',
							'page'        => 0,
							'total_pages' => $total_pages,
						);
					}
					break;

				case 'tickets':
					if ( $cron_status['page'] == $cron_status['total_pages'] ) {
						$total_customers = $wpdb->get_var( 'SELECT COUNT(id) FROM ' . $wpdb->prefix . 'psmsc_customers' );
						$total_pages = $total_customers ? ceil( $total_customers / 40 ) : 0;
						$cron_status = array(
							'status'      => 'running',
							'title'       => 'Importing customers',
							'resource'    => 'customers',
							'page'        => 0,
							'total_pages' => $total_pages,
						);
					}
					break;

				case 'customers':
					if ( $cron_status['page'] == $cron_status['total_pages'] ) {
						$total_agents = $wpdb->get_var( 'SELECT COUNT(id) FROM ' . $wpdb->prefix . 'psmsc_agents' );
						$total_pages = $total_agents ? ceil( $total_agents / 20 ) : 0;
						$cron_status = array(
							'status'      => 'running',
							'title'       => 'Importing agents',
							'resource'    => 'agents',
							'page'        => 0,
							'total_pages' => $total_pages,
						);
					}
					break;

				case 'agents':
					if ( $cron_status['page'] == $cron_status['total_pages'] ) {
						WPSC_Installation::set_upgrade_complete();
						update_option(
							'wpsc_upgrade_cleanup',
							array(
								'version' => 1,
								'status'  => 'texonomy',
							)
						);
						$cron_status = array(
							'status' => 'complete',
						);
						update_option( 'wpsc_v1_upgrade_cron_status', $cron_status );
					}
					break;
			}

			if ( $cron_status['status'] == 'complete' ) {
				return;
			}

			if ( $cron_status['total_pages'] == 0 ) {
				update_option( 'wpsc_v1_upgrade_cron_status', $cron_status );
				delete_transient( 'wpsc_upgrading' );
				return;
			}

			// execute the cron.
			$page = intval( $cron_status['page'] ) + 1;

			switch ( $cron_status['resource'] ) {

				case 'db_tables':
					WPSC_Installation::create_db_tables();
					WPSC_Installation::initial_setup();
					do_action( 'wpsc_upgrade_install_addons' );
					break;

				case 'custom_fields':
					self::import_custom_fields( $page );
					break;

				case 'settings':
					self::upgrade_settings( $page );
					break;

				case 'tickets':
					self::upgrade_tickets( $page );
					break;

				case 'customers':
					self::customer_ticket_count( $page );
					break;

				case 'agents':
					self::agent_ticket_count( $page );
					break;
			}

			$cron_status['page'] = $page;
			update_option( 'wpsc_v1_upgrade_cron_status', $cron_status );

			// Delete transient.
			delete_transient( 'wpsc_upgrading' );
		}

		/**
		 * Upgrade settings
		 *
		 * @param int $page - page number.
		 * @return void
		 */
		public static function upgrade_settings( $page ) {

			switch ( $page ) {

				case 1:
					self::import_statuses();
					self::import_categories();
					self::import_priorities();
					self::import_agents();
					break;

				case 2:
					self::import_ticket_form_fields();
					self::import_ticket_list_items();
					self::import_tl_more_settings();
					self::import_en_general();
					self::import_en_ticket_notifications();
					break;

				case 3:
					self::import_general_settings();
					self::reorder_widgets();
					self::import_thankyou_settings();
					self::import_terms_and_conditions();
					self::import_advanced_settings();
					break;

				case 4:
					self::import_captcha();
					self::import_assign_agent_rules();
					self::import_automatic_close_tickets();
					self::import_canned_reply();
					self::import_report_settings();
					break;

				case 5:
					self::import_satisfaction_survey();
					self::import_email_piping();
					self::import_faq_integration();
					self::import_knowledgebase_integration();
					self::import_export_addon();
					break;

				case 6:
					self::import_woocommerce_addon();
					self::import_shedule_tickets();
					self::import_sla_settings();
					self::import_usergroup_settings();
					break;
			}
		}

		/**
		 * Upgrade tickets
		 *
		 * @param int $page - page number.
		 * @return void
		 */
		public static function upgrade_tickets( $page ) {

			$posts_per_page = 5;
			$results = get_posts(
				array(
					'post_type'      => 'wpsc_ticket',
					'post_status'    => array( 'publish', 'trash' ),
					'posts_per_page' => $posts_per_page,
					'offset'         => ( $page - 1 ) * $posts_per_page,
					'orderby'        => 'ID',
					'order'          => 'ASC',
				)
			);

			foreach ( $results as $ticket ) {
				self::import_individual_ticket( $ticket );
			}
		}

		/**
		 * Customer ticket count
		 *
		 * @param int $page - page number.
		 * @return void
		 */
		public static function customer_ticket_count( $page ) {

			global $wpdb;
			$installed_addons = get_option( 'wpsc_upgrade_installed_addons' );
			$offset = ( $page - 1 ) * 40;
			$sql = "SELECT * FROM {$wpdb->prefix}psmsc_customers ORDER BY id ASC LIMIT 40 OFFSET " . $offset;
			$customers = $wpdb->get_results( $sql );
			foreach ( $customers as $customer ) {
				wpsc_update_customer_ticket_count( $customer );
				if ( $installed_addons[7]['is_installed'] ) {
					wpsc_update_customer_usergroups( $customer );
				}
				self::import_saved_filters( $customer );
			}
		}

		/**
		 * Agent ticket count
		 *
		 * @param int $page - page number.
		 * @return void
		 */
		public static function agent_ticket_count( $page ) {

			global $wpdb;
			$offset = ( $page - 1 ) * 20;
			$sql = "SELECT * FROM {$wpdb->prefix}psmsc_agents WHERE is_agentgroup = 0 ORDER BY id ASC LIMIT 20 OFFSET " . $offset;
			$agents = $wpdb->get_results( $sql );
			foreach ( $agents as $agent ) {
				wpsc_agent_reset_unresolved_count( $agent );
				wpsc_agent_reset_workload( $agent );
				self::import_saved_filters( wpsc_get_customer_by( 'id', $agent->customer ) );
				self::import_agent_settings( $agent );
			}
		}

		/**
		 * Import saved ticket filters
		 *
		 * @param stdClass $customer - customer object.
		 * @return void
		 */
		public static function import_saved_filters( $customer ) {

			global $wpdb;

			// return if customer is guest.
			if ( ! $customer->user ) {
				return;
			}

			$cf_slug_map = get_option( 'wpsc_upgrade_cf_slug_map' );
			$options_map = get_option( 'wpsc_upgrade_cf_options_map' );
			$status_map = get_option( 'wpsc_upgrade_status_map' );
			$category_map = get_option( 'wpsc_upgrade_category_map' );
			$priority_map = get_option( 'wpsc_upgrade_priority_map' );
			$rating_map = get_option( 'wpsc_upgrade_sf_rating_map' );
			$agent_map = get_option( 'wpsc_upgrade_agent_map' );
			$old_date_format = get_option( 'wpsc_calender_date_format' );
			$parent_filter_map = array(
				'all'                 => 'all',
				'unresolved_agent'    => 'unresolved',
				'unresolved_customer' => 'unresolved',
				'unassigned'          => 'unassigned',
				'mine'                => 'mine',
				'closed'              => 'closed',
				'deleted'             => 'deleted',
			);

			$date_formats = array(
				'dd-mm-yy' => 'd-m-Y',
				'dd-yy-mm' => 'd-Y-m',
				'mm-dd-yy' => 'm-d-Y',
				'mm-yy-dd' => 'm-Y-d',
				'yy-mm-dd' => 'Y-m-d',
				'yy-dd-mm' => 'Y-d-m',
			);

			// previous filters.
			$prev_filters = get_user_meta( $customer->user, get_current_blog_id() . '_wpsc_filter', true );
			if ( ! $prev_filters ) {
				return;
			}

			// new filters.
			$index = 1;
			$new_filters = array();

			// import filters.
			foreach ( $prev_filters as $prev ) {

				$new = array(
					'label'         => $prev['save_label'],
					'parent-filter' => $parent_filter_map[ $prev['label'] ],
					'sort-by'       => $cf_slug_map[ $prev['orderby'] ],
					'sort-order'    => $prev['order'],
				);

				$filters = array();
				foreach ( $prev['custom_filter'] as $slug => $filter ) {

					if ( $slug == 's' ) {
						continue;
					}

					if ( $slug == 'id' ) {
						$slug = 'ticket_id';
					}

					$cf = wpsc_get_cf_by( 'slug', $cf_slug_map[ $slug ] );
					if ( ! $cf ) {
						continue;
					}

					switch ( $cf->type ) {

						case 'df_category':
							$filters[ $cf->slug ] = array(
								'operator'      => 'IN',
								'operand_val_1' => array_map(
									function( $prev_category ) use ( $category_map ) {
										return $category_map[ $prev_category ];
									},
									$filter
								),
							);
							break;

						case 'df_status':
							$filters[ $cf->slug ] = array(
								'operator'      => 'IN',
								'operand_val_1' => array_map(
									function( $prev_status ) use ( $status_map ) {
										return $status_map[ $prev_status ];
									},
									$filter
								),
							);
							break;

						case 'df_priority':
							$filters[ $cf->slug ] = array(
								'operator'      => 'IN',
								'operand_val_1' => array_map(
									function( $prev_priority ) use ( $priority_map ) {
										return $priority_map[ $prev_priority ];
									},
									$filter
								),
							);
							break;

						case 'df_customer_name':
						case 'df_customer_email':
							$slug = $cf->type == 'df_customer_name' ? 'name' : 'email';
							$sql = "SELECT * FROM {$wpdb->prefix}psmsc_customers WHERE " . $slug . " IN( '" . implode( "', '", $filter ) . "' )";
							$customers = $wpdb->get_results( $sql );
							if ( ! $customers ) {
								break;
							}
							if ( isset( $filters['customer'] ) ) {

								$filters['customer']['operand_val_1'] = array_unique(
									array_merge(
										$filters['customer']['operand_val_1'],
										array_map(
											function( $cust ) {
												return $cust->id;
											},
											$customers
										)
									)
								);

							} else {

								$filters['customer'] = array(
									'operator'      => 'IN',
									'operand_val_1' => array_map(
										function( $cust ) {
											return $cust->id;
										},
										$customers
									),
								);
							}
							break;

						case 'df_assigned_agent':
						case 'df_agent_created':
							$filters[ $cf->slug ] = array(
								'operator'      => 'IN',
								'operand_val_1' => array_map(
									function( $prev_agent ) use ( $agent_map ) {
										return $agent_map[ $prev_agent ];
									},
									$filter
								),
							);
							break;

						case 'df_date_created':
						case 'cf_date':
							if ( ! ( $filter['from'] && $filter['to'] ) ) {
								break;
							}
							$from = DateTime::createFromFormat( $date_formats[ $old_date_format ], $filter['from'] );
							$to = DateTime::createFromFormat( $date_formats[ $old_date_format ], $filter['to'] );
							if ( ! ( $from && $to ) ) {
								break;
							}
							$filters[ $cf->slug ] = array(
								'operator'      => 'BETWEEN',
								'operand_val_1' => $from->format( 'Y-m-d' ),
								'operand_val_2' => $to->format( 'Y-m-d' ),
							);
							break;

						case 'df_sf_rating':
							$filters[ $cf->slug ] = array(
								'operator'      => 'IN',
								'operand_val_1' => array_map(
									function( $prev_rating ) use ( $rating_map ) {
										return $rating_map[ $prev_rating ];
									},
									$filter
								),
							);
							break;

						case 'df_id':
						case 'cf_textfield':
						case 'cf_email':
						case 'cf_number':
						case 'cf_url':
							$filters[ $cf->slug ] = array(
								'operator'      => 'IN',
								'operand_val_1' => implode( '^^', $filter ),
							);
							break;

						case 'cf_checkbox':
						case 'cf_multi_select':
						case 'cf_radio_button':
						case 'cf_single_select':
							$filters[ $cf->slug ] = array(
								'operator'      => 'IN',
								'operand_val_1' => array_map(
									function ( $prev_option ) use ( $options_map, $cf ) {
										return $options_map[ $cf->id ][ $prev_option ];
									},
									$filter
								),
							);
							break;

						case 'cf_woo_product':
						case 'cf_woo_order':
							$filters[ $cf->slug ] = array(
								'operator'      => 'IN',
								'operand_val_1' => $filter,
							);
							break;
					}
				}

				$new['filters'] = wp_json_encode( $filters );

				$new_filters[ $index++ ] = $new;
			}

			// import Visibility conditions.
			foreach ( $new_filters as $slug => $properties ) {
				if ( ! $properties['filters'] ) {
					continue;
				}
				$new_filters[ $slug ]['filters'] = WPSC_SC_Upgrade::upgrade_condition( $properties['filters'], 'AND' );
			}
			update_user_meta( $customer->user, get_current_blog_id() . '-wpsc-tl-saved-filters', $new_filters );
			update_user_meta( $customer->user, get_current_blog_id() . '-wpsc-tl-cf-auto-increament', ++$index );
		}

		/**
		 * Import agent settings
		 *
		 * @param stdClass $agent - agent object.
		 * @return void
		 */
		public static function import_agent_settings( $agent ) {

			$signature = get_user_meta( $agent->user, 'wpsc_agent_signature', true );
			if ( ! $signature ) {
				update_user_meta( $agent->user, get_current_blog_id() . '_wpsc_email_signature', $signature );
			}
		}

		/**
		 * Import statuses
		 *
		 * @return void
		 */
		public static function import_statuses() {

			global $wpdb;
			$wpdb->query( 'DELETE FROM ' . $wpdb->prefix . 'psmsc_statuses' );
			$wpdb->query( 'ALTER TABLE ' . $wpdb->prefix . 'psmsc_statuses AUTO_INCREMENT = 1' );

			$statuses = get_terms(
				array(
					'taxonomy'   => 'wpsc_statuses',
					'hide_empty' => false,
					'orderby'    => 'meta_value_num',
					'order'      => 'ASC',
					'meta_query' => array( 'order_clause' => array( 'key' => 'wpsc_status_load_order' ) ),
				)
			);

			$string_translations = get_option( 'wpsc-string-translation' );
			$map = array();
			$load_order = 1;
			foreach ( $statuses as $prev ) {

				$wpdb->insert(
					$wpdb->prefix . 'psmsc_statuses',
					array(
						'name'       => $prev->name,
						'color'      => get_term_meta( $prev->term_id, 'wpsc_status_color', true ),
						'bg_color'   => get_term_meta( $prev->term_id, 'wpsc_status_background_color', true ),
						'load_order' => $load_order++,
					)
				);

				$string_translations[ 'wpsc-status-' . $wpdb->insert_id ] = $prev->name;
				$map[ $prev->term_id ] = $wpdb->insert_id;
			}

			update_option( 'wpsc-string-translation', $string_translations );
			update_option( 'wpsc_upgrade_status_map', $map );
		}

		/**
		 * Import categories
		 *
		 * @return void
		 */
		public static function import_categories() {

			global $wpdb;
			$wpdb->query( 'DELETE FROM ' . $wpdb->prefix . 'psmsc_categories' );
			$wpdb->query( 'ALTER TABLE ' . $wpdb->prefix . 'psmsc_categories AUTO_INCREMENT = 1' );

			$categories = get_terms(
				array(
					'taxonomy'   => 'wpsc_categories',
					'hide_empty' => false,
					'orderby'    => 'meta_value_num',
					'order'      => 'ASC',
					'meta_query' => array( 'order_clause' => array( 'key' => 'wpsc_category_load_order' ) ),
				)
			);

			$string_translations = get_option( 'wpsc-string-translation' );
			$map = array();
			$load_order = 1;
			foreach ( $categories as $prev ) {

				$wpdb->insert(
					$wpdb->prefix . 'psmsc_categories',
					array(
						'name'       => $prev->name,
						'load_order' => $load_order++,
					)
				);

				$string_translations[ 'wpsc-category-' . $wpdb->insert_id ] = $prev->name;
				$map[ $prev->term_id ] = $wpdb->insert_id;
			}

			update_option( 'wpsc-string-translation', $string_translations );
			update_option( 'wpsc_upgrade_category_map', $map );
		}

		/**
		 * Import priorities
		 *
		 * @return void
		 */
		public static function import_priorities() {

			global $wpdb;
			$wpdb->query( 'DELETE FROM ' . $wpdb->prefix . 'psmsc_priorities' );
			$wpdb->query( 'ALTER TABLE ' . $wpdb->prefix . 'psmsc_priorities AUTO_INCREMENT = 1' );

			$priorities = get_terms(
				array(
					'taxonomy'   => 'wpsc_priorities',
					'hide_empty' => false,
					'orderby'    => 'meta_value_num',
					'order'      => 'ASC',
					'meta_query' => array( 'order_clause' => array( 'key' => 'wpsc_priority_load_order' ) ),
				)
			);

			$string_translations = get_option( 'wpsc-string-translation' );
			$map = array();
			$load_order = 1;
			foreach ( $priorities as $prev ) {

				$wpdb->insert(
					$wpdb->prefix . 'psmsc_priorities',
					array(
						'name'       => $prev->name,
						'color'      => get_term_meta( $prev->term_id, 'wpsc_priority_color', true ),
						'bg_color'   => get_term_meta( $prev->term_id, 'wpsc_priority_background_color', true ),
						'load_order' => $load_order++,
					)
				);

				$string_translations[ 'wpsc-priority-' . $wpdb->insert_id ] = $prev->name;
				$map[ $prev->term_id ] = $wpdb->insert_id;
			}

			update_option( 'wpsc-string-translation', $string_translations );
			update_option( 'wpsc_upgrade_priority_map', $map );
		}

		/**
		 * Import custom fields
		 *
		 * @param int $page - current page number.
		 * @return void
		 */
		public static function import_custom_fields( $page ) {

			global $wpdb;

			// Disable InnoDB strict mode if enabled to avoid limit of number of columns in tables.
			$strict_mode = $wpdb->get_var( "SHOW VARIABLES LIKE 'innodb_strict_mode'", 1 );
			if ( $strict_mode == 'ON' ) {
				$wpdb->query( "SET innodb_strict_mode = 'OFF'" );
			}

			// Change load order of all existing custom fields to higher number.
			if ( $page == 1 ) {
				$wpdb->query( 'UPDATE ' . $wpdb->prefix . 'psmsc_custom_fields SET load_order=1000 WHERE id NOT IN(1,2,7)' );
			}

			$texonomy = 'wpsc_ticket_custom_fields';
			$offset = ( $page - 1 ) * 10;
			$args = array(
				'hide_empty' => false,
				'number'     => 10,
				'offset'     => $offset,
				'orderby'    => 'term_id',
				'order'      => 'ASC',
			);

			$fields = get_terms( $texonomy, $args );
			$string_translations = get_option( 'wpsc-string-translation' );
			$installed_addons = get_option( 'wpsc_upgrade_installed_addons' );
			$term_id_map = get_option( 'wpsc_upgrade_cf_term_id_map', array() );
			$slug_map = get_option( 'wpsc_upgrade_cf_slug_map', array() );
			$load_order = get_option( 'wpsc_upgrade_cf_load_order', 8 );
			foreach ( $fields as $prev ) {

				$label = get_term_meta( $prev->term_id, 'wpsc_tf_label', true );
				$extra_info = get_term_meta( $prev->term_id, 'wpsc_tf_extra_info', true );
				$type = get_term_meta( $prev->term_id, 'wpsc_tf_type', true );

				if ( intval( $type ) === 0 ) {

					switch ( $prev->slug ) {

						case 'ticket_id':
							$term_id_map[ $prev->term_id ] = 1;
							$slug_map[ $prev->slug ] = 'id';
							break;

						case 'ticket_status':
							$term_id_map[ $prev->term_id ] = 7;
							$slug_map[ $prev->slug ] = 'status';
							break;

						case 'customer_name':
							$wpdb->update(
								$wpdb->prefix . 'psmsc_custom_fields',
								array(
									'name'       => $label,
									'extra_info' => $extra_info,
									'load_order' => $load_order++,
								),
								array( 'id' => 3 )
							);
							$term_id_map[ $prev->term_id ] = 3;
							$slug_map[ $prev->slug ] = 'name';
							$string_translations[ 'wpsc-cf-name-' . $wpdb->insert_id ] = $label;
							break;

						case 'customer_email':
							$wpdb->update(
								$wpdb->prefix . 'psmsc_custom_fields',
								array(
									'name'       => $label,
									'extra_info' => $extra_info,
									'load_order' => $load_order++,
								),
								array( 'id' => 4 )
							);
							$term_id_map[ $prev->term_id ] = 4;
							$slug_map[ $prev->slug ] = 'email';
							$string_translations[ 'wpsc-cf-name-' . $wpdb->insert_id ] = $label;
							break;

						case 'ticket_subject':
							$wpdb->update(
								$wpdb->prefix . 'psmsc_custom_fields',
								array(
									'name'       => $label,
									'extra_info' => $extra_info,
									'load_order' => $load_order++,
								),
								array( 'id' => 5 )
							);
							$term_id_map[ $prev->term_id ] = 5;
							$slug_map[ $prev->slug ] = 'subject';
							$string_translations[ 'wpsc-cf-name-' . $wpdb->insert_id ] = $label;
							break;

						case 'ticket_description':
							$wpdb->update(
								$wpdb->prefix . 'psmsc_custom_fields',
								array(
									'name'       => $label,
									'extra_info' => $extra_info,
									'load_order' => $load_order++,
								),
								array( 'id' => 6 )
							);
							$term_id_map[ $prev->term_id ] = 6;
							$slug_map[ $prev->slug ] = 'description';
							$string_translations[ 'wpsc-cf-name-' . $wpdb->insert_id ] = $label;
							break;

						case 'ticket_category':
							$wpdb->update(
								$wpdb->prefix . 'psmsc_custom_fields',
								array(
									'name'       => $label,
									'extra_info' => $extra_info,
									'load_order' => $load_order++,
								),
								array( 'id' => 9 )
							);
							$term_id_map[ $prev->term_id ] = 9;
							$slug_map[ $prev->slug ] = 'category';
							$string_translations[ 'wpsc-cf-name-' . $wpdb->insert_id ] = $label;
							break;

						case 'ticket_priority':
							$wpdb->update(
								$wpdb->prefix . 'psmsc_custom_fields',
								array(
									'name'       => $label,
									'extra_info' => $extra_info,
									'load_order' => $load_order++,
								),
								array( 'id' => 8 )
							);
							$term_id_map[ $prev->term_id ] = 8;
							$slug_map[ $prev->slug ] = 'priority';
							$string_translations[ 'wpsc-cf-name-' . $wpdb->insert_id ] = $label;
							break;

						case 'assigned_agent':
							$wpdb->update(
								$wpdb->prefix . 'psmsc_custom_fields',
								array(
									'name'       => $label,
									'load_order' => $load_order++,
								),
								array( 'id' => 10 )
							);
							$term_id_map[ $prev->term_id ] = 10;
							$slug_map[ $prev->slug ] = 'assigned_agent';
							$string_translations[ 'wpsc-cf-name-' . $wpdb->insert_id ] = $label;
							break;

						case 'date_created':
							$wpdb->update(
								$wpdb->prefix . 'psmsc_custom_fields',
								array(
									'name'       => $label,
									'load_order' => $load_order++,
								),
								array( 'id' => 11 )
							);
							$term_id_map[ $prev->term_id ] = 11;
							$slug_map[ $prev->slug ] = 'date_created';
							$string_translations[ 'wpsc-cf-name-' . $wpdb->insert_id ] = $label;
							break;

						case 'date_updated':
							$wpdb->update(
								$wpdb->prefix . 'psmsc_custom_fields',
								array(
									'name'       => $label,
									'load_order' => $load_order++,
								),
								array( 'id' => 12 )
							);
							$term_id_map[ $prev->term_id ] = 12;
							$slug_map[ $prev->slug ] = 'date_updated';
							$string_translations[ 'wpsc-cf-name-' . $wpdb->insert_id ] = $label;
							break;

						case 'agent_created':
							$wpdb->update(
								$wpdb->prefix . 'psmsc_custom_fields',
								array(
									'name'       => $label,
									'load_order' => $load_order++,
								),
								array( 'id' => 13 )
							);
							$term_id_map[ $prev->term_id ] = 13;
							$slug_map[ $prev->slug ] = 'agent_created';
							$string_translations[ 'wpsc-cf-name-' . $wpdb->insert_id ] = $label;
							break;

						case 'sf_rating':
							$id = $wpdb->get_var( "SELECT id from {$wpdb->prefix}psmsc_custom_fields WHERE slug = 'rating'" );
							if ( $id ) {
								$wpdb->update(
									$wpdb->prefix . 'psmsc_custom_fields',
									array(
										'name'       => $label,
										'load_order' => $load_order++,
									),
									array( 'id' => $id )
								);
								$term_id_map[ $prev->term_id ] = $id;
								$slug_map[ $prev->slug ] = 'rating';
								$string_translations[ 'wpsc-cf-name-' . $wpdb->insert_id ] = $label;
							}
							break;

						case 'sla':
							$id = $wpdb->get_var( "SELECT id from {$wpdb->prefix}psmsc_custom_fields WHERE slug = 'sla'" );
							if ( $id ) {
								$wpdb->update(
									$wpdb->prefix . 'psmsc_custom_fields',
									array(
										'name'       => $label,
										'load_order' => $load_order++,
									),
									array( 'id' => $id )
								);
								$term_id_map[ $prev->term_id ] = $id;
								$slug_map[ $prev->slug ] = 'sla';
								$string_translations[ 'wpsc-cf-name-' . $wpdb->insert_id ] = $label;
							}
							break;
					}
				} else {

					$personal_info = get_term_meta( $prev->term_id, 'wpsc_tf_personal_info', true );
					$field = get_term_meta( $prev->term_id, 'agentonly', true ) == 1 ? 'agentonly' : 'ticket';

					switch ( intval( $type ) ) {

						case 1:
							$response = wpsc_upgrade_insert_custom_field(
								array(
									'name'             => $label,
									'extra_info'       => $extra_info,
									'field'            => $field,
									'type'             => 'cf_textfield',
									'is_personal_info' => $personal_info,
									'load_order'       => $load_order++,
								)
							);
							$wpdb->query( "ALTER TABLE {$wpdb->prefix}psmsc_tickets ADD " . $response['slug'] . ' TINYTEXT NULL' );
							$term_id_map[ $prev->term_id ] = $response['id'];
							$slug_map[ $prev->slug ] = $response['slug'];
							$string_translations[ 'wpsc-cf-name-' . $response['id'] ] = $label;
							$string_translations[ 'wpsc-cf-exi-' . $response['id'] ] = $extra_info;
							break;

						case 2:
							$response = wpsc_upgrade_insert_custom_field(
								array(
									'name'             => $label,
									'extra_info'       => $extra_info,
									'field'            => $field,
									'type'             => 'cf_single_select',
									'is_personal_info' => $personal_info,
									'load_order'       => $load_order++,
								)
							);
							$wpdb->query( "ALTER TABLE {$wpdb->prefix}psmsc_tickets ADD " . $response['slug'] . ' TINYTEXT NULL' );
							$option_strings = self::import_options( $prev, $response['id'] );
							$string_translations = array_merge( $string_translations, $option_strings );
							$term_id_map[ $prev->term_id ] = $response['id'];
							$slug_map[ $prev->slug ] = $response['slug'];
							$string_translations[ 'wpsc-cf-name-' . $response['id'] ] = $label;
							$string_translations[ 'wpsc-cf-exi-' . $response['id'] ] = $extra_info;
							break;

						case 3:
							$response = wpsc_upgrade_insert_custom_field(
								array(
									'name'             => $label,
									'extra_info'       => $extra_info,
									'field'            => $field,
									'type'             => 'cf_checkbox',
									'is_personal_info' => $personal_info,
									'load_order'       => $load_order++,
								)
							);
							$wpdb->query( "ALTER TABLE {$wpdb->prefix}psmsc_tickets ADD " . $response['slug'] . ' TINYTEXT NULL' );
							$option_strings = self::import_options( $prev, $response['id'] );
							$string_translations = array_merge( $string_translations, $option_strings );
							$term_id_map[ $prev->term_id ] = $response['id'];
							$slug_map[ $prev->slug ] = $response['slug'];
							$string_translations[ 'wpsc-cf-name-' . $response['id'] ] = $label;
							$string_translations[ 'wpsc-cf-exi-' . $response['id'] ] = $extra_info;
							break;

						case 4:
							$response = wpsc_upgrade_insert_custom_field(
								array(
									'name'             => $label,
									'extra_info'       => $extra_info,
									'field'            => $field,
									'type'             => 'cf_radio_button',
									'is_personal_info' => $personal_info,
									'load_order'       => $load_order++,
								)
							);
							$wpdb->query( "ALTER TABLE {$wpdb->prefix}psmsc_tickets ADD " . $response['slug'] . ' TINYTEXT NULL' );
							$option_strings = self::import_options( $prev, $response['id'] );
							$string_translations = array_merge( $string_translations, $option_strings );
							$term_id_map[ $prev->term_id ] = $response['id'];
							$slug_map[ $prev->slug ] = $response['slug'];
							$string_translations[ 'wpsc-cf-name-' . $response['id'] ] = $label;
							$string_translations[ 'wpsc-cf-exi-' . $response['id'] ] = $extra_info;
							break;

						case 5:
							$response = wpsc_upgrade_insert_custom_field(
								array(
									'name'             => $label,
									'extra_info'       => $extra_info,
									'field'            => $field,
									'type'             => 'cf_textarea',
									'is_personal_info' => $personal_info,
									'load_order'       => $load_order++,
								)
							);
							$wpdb->query( "ALTER TABLE {$wpdb->prefix}psmsc_tickets ADD " . $response['slug'] . ' LONGTEXT NULL' );
							$term_id_map[ $prev->term_id ] = $response['id'];
							$slug_map[ $prev->slug ] = $response['slug'];
							$string_translations[ 'wpsc-cf-name-' . $response['id'] ] = $label;
							$string_translations[ 'wpsc-cf-exi-' . $response['id'] ] = $extra_info;
							break;

						case 6:
							$response = wpsc_upgrade_insert_custom_field(
								array(
									'name'             => $label,
									'extra_info'       => $extra_info,
									'field'            => $field,
									'type'             => 'cf_date',
									'is_personal_info' => $personal_info,
									'load_order'       => $load_order++,
								)
							);
							$wpdb->query( "ALTER TABLE {$wpdb->prefix}psmsc_tickets ADD " . $response['slug'] . ' DATETIME NULL DEFAULT NULL' );
							$term_id_map[ $prev->term_id ] = $response['id'];
							$slug_map[ $prev->slug ] = $response['slug'];
							$string_translations[ 'wpsc-cf-name-' . $response['id'] ] = $label;
							$string_translations[ 'wpsc-cf-exi-' . $response['id'] ] = $extra_info;
							break;

						case 7:
							$response = wpsc_upgrade_insert_custom_field(
								array(
									'name'             => $label,
									'extra_info'       => $extra_info,
									'field'            => $field,
									'type'             => 'cf_url',
									'is_personal_info' => $personal_info,
									'load_order'       => $load_order++,
								)
							);
							$wpdb->query( "ALTER TABLE {$wpdb->prefix}psmsc_tickets ADD " . $response['slug'] . ' TINYTEXT NULL' );
							$term_id_map[ $prev->term_id ] = $response['id'];
							$slug_map[ $prev->slug ] = $response['slug'];
							$string_translations[ 'wpsc-cf-name-' . $response['id'] ] = $label;
							$string_translations[ 'wpsc-cf-exi-' . $response['id'] ] = $extra_info;
							break;

						case 8:
							$response = wpsc_upgrade_insert_custom_field(
								array(
									'name'             => $label,
									'extra_info'       => $extra_info,
									'field'            => $field,
									'type'             => 'cf_email',
									'is_personal_info' => $personal_info,
									'load_order'       => $load_order++,
								)
							);
							$wpdb->query( "ALTER TABLE {$wpdb->prefix}psmsc_tickets ADD " . $response['slug'] . ' TINYTEXT NULL' );
							$term_id_map[ $prev->term_id ] = $response['id'];
							$slug_map[ $prev->slug ] = $response['slug'];
							$string_translations[ 'wpsc-cf-name-' . $response['id'] ] = $label;
							$string_translations[ 'wpsc-cf-exi-' . $response['id'] ] = $extra_info;
							break;

						case 9:
							$response = wpsc_upgrade_insert_custom_field(
								array(
									'name'             => $label,
									'extra_info'       => $extra_info,
									'field'            => $field,
									'type'             => 'cf_number',
									'is_personal_info' => $personal_info,
									'load_order'       => $load_order++,
								)
							);
							$wpdb->query( "ALTER TABLE {$wpdb->prefix}psmsc_tickets ADD " . $response['slug'] . ' TINYTEXT NULL' );
							$term_id_map[ $prev->term_id ] = $response['id'];
							$slug_map[ $prev->slug ] = $response['slug'];
							$string_translations[ 'wpsc-cf-name-' . $response['id'] ] = $label;
							$string_translations[ 'wpsc-cf-exi-' . $response['id'] ] = $extra_info;
							break;

						case 10:
							$response = wpsc_upgrade_insert_custom_field(
								array(
									'name'             => $label,
									'extra_info'       => $extra_info,
									'field'            => $field,
									'type'             => 'cf_file_attachment_multiple',
									'is_personal_info' => $personal_info,
									'load_order'       => $load_order++,
								)
							);
							$wpdb->query( "ALTER TABLE {$wpdb->prefix}psmsc_tickets ADD " . $response['slug'] . ' TINYTEXT NULL' );
							$term_id_map[ $prev->term_id ] = $response['id'];
							$slug_map[ $prev->slug ] = $response['slug'];
							$string_translations[ 'wpsc-cf-name-' . $response['id'] ] = $label;
							$string_translations[ 'wpsc-cf-exi-' . $response['id'] ] = $extra_info;
							break;

						case 11:
							if ( $installed_addons[5]['is_installed'] ) {
								$response = wpsc_upgrade_insert_custom_field(
									array(
										'name'             => $label,
										'extra_info'       => $extra_info,
										'field'            => $field,
										'type'             => 'cf_woo_product',
										'is_personal_info' => $personal_info,
										'load_order'       => $load_order++,
									)
								);
								$wpdb->query( "ALTER TABLE {$wpdb->prefix}psmsc_tickets ADD " . $response['slug'] . ' BIGINT NULL DEFAULT 0' );
								$term_id_map[ $prev->term_id ] = $response['id'];
								$slug_map[ $prev->slug ] = $response['slug'];
								$string_translations[ 'wpsc-cf-name-' . $response['id'] ] = $label;
								$string_translations[ 'wpsc-cf-exi-' . $response['id'] ] = $extra_info;
							}
							break;

						case 12:
							if ( $installed_addons[5]['is_installed'] ) {
								$response = wpsc_upgrade_insert_custom_field(
									array(
										'name'             => $label,
										'extra_info'       => $extra_info,
										'field'            => $field,
										'type'             => 'cf_woo_order',
										'is_personal_info' => $personal_info,
										'load_order'       => $load_order++,
									)
								);
								$wpdb->query( "ALTER TABLE {$wpdb->prefix}psmsc_tickets ADD " . $response['slug'] . ' BIGINT NULL DEFAULT 0' );
								$term_id_map[ $prev->term_id ] = $response['id'];
								$slug_map[ $prev->slug ] = $response['slug'];
								$string_translations[ 'wpsc-cf-name-' . $response['id'] ] = $label;
								$string_translations[ 'wpsc-cf-exi-' . $response['id'] ] = $extra_info;
							}
							break;
					}
				}
			}

			update_option( 'wpsc-string-translation', $string_translations );
			update_option( 'wpsc_upgrade_cf_term_id_map', $term_id_map );
			update_option( 'wpsc_upgrade_cf_slug_map', $slug_map );
			update_option( 'wpsc_upgrade_cf_load_order', $load_order );
		}

		/**
		 * Import options for custom field
		 *
		 * @param mixed $prev - previous custom field object.
		 * @param int   $id - new custom field id.
		 * @return void
		 */
		public static function import_options( $prev, $id ) {

			global $wpdb;
			$string_translations = get_option( 'wpsc-string-translation' );
			$options = get_term_meta( $prev->term_id, 'wpsc_tf_options', true );
			$load_order = 1;
			$options_map = get_option( 'wpsc_upgrade_cf_options_map', array() );
			foreach ( $options as $name ) {
				$wpdb->insert(
					$wpdb->prefix . 'psmsc_options',
					array(
						'name'         => $name,
						'date_created' => ( new DateTime( 'now' ) )->format( 'Y-m-d H:m:s' ),
						'custom_field' => $id,
						'load_order'   => $load_order++,
					)
				);
				$string_translations[ 'wpsc-option-' . $wpdb->insert_id ] = $name;
				$options_map[ $id ][ $name ] = $wpdb->insert_id;
			}
			update_option( 'wpsc-string-translation', $string_translations );
			update_option( 'wpsc_upgrade_cf_options_map', $options_map );
		}

		/**
		 * Import agent and agentgroups
		 *
		 * @return void
		 */
		public static function import_agents() {

			global $wpdb;

			// removing agent role cap.
			$agents = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}psmsc_agents" );
			foreach ( $agents as $agent ) {
				$user = get_user_by( 'id', $agent->user );
				if ( $user ) {
					$user->remove_cap( 'wpsc_agent' );
				}
			}

			$wpdb->query( 'DELETE FROM ' . $wpdb->prefix . 'psmsc_agents' );
			$wpdb->query( 'ALTER TABLE ' . $wpdb->prefix . 'psmsc_agents AUTO_INCREMENT = 1' );
			$wpdb->query( 'DELETE FROM ' . $wpdb->prefix . 'psmsc_customers' );
			$wpdb->query( 'ALTER TABLE ' . $wpdb->prefix . 'psmsc_customers AUTO_INCREMENT = 1' );

			// import agent roles.
			$new = array();
			$prev = get_option( 'wpsc_agent_role' );
			foreach ( $prev as $key => $role ) {
				$new[ $key ] = array(
					'label' => $role['label'],
					'caps'  => array(
						'view-unassigned'       => $role['view_unassigned'] == 1 ? true : false, // View ticket.
						'view-assigned-me'      => $role['view_assigned_me'] == 1 ? true : false,
						'view-assigned-others'  => $role['view_assigned_others'] == 1 ? true : false,
						'reply-unassigned'      => $role['reply_unassigned'] == 1 ? true : false, // Reply ticket.
						'reply-assigned-me'     => $role['reply_assigned_me'] == 1 ? true : false,
						'reply-assigned-others' => $role['reply_assigned_others'] == 1 ? true : false,
						'pn-unassigned'         => true, // Private notes.
						'pn-assigned-me'        => true,
						'pn-assigned-others'    => true,
						'aa-unassigned'         => $role['assign_unassigned'] == 1 ? true : false, // Assignee.
						'aa-assigned-me'        => $role['assign_assigned_me'] == 1 ? true : false,
						'aa-assigned-others'    => $role['assign_assigned_others'] == 1 ? true : false,
						'cs-unassigned'         => $role['cng_tkt_sts_unassigned'] == 1 ? true : false, // Change status.
						'cs-assigned-me'        => $role['cng_tkt_sts_assigned_me'] == 1 ? true : false,
						'cs-assigned-others'    => $role['cng_tkt_sts_assigned_others'] == 1 ? true : false,
						'ctf-unassigned'        => $role['cng_tkt_field_unassigned'] == 1 ? true : false, // Change ticket fields.
						'ctf-assigned-me'       => $role['cng_tkt_field_assigned_me'] == 1 ? true : false,
						'ctf-assigned-others'   => $role['cng_tkt_field_assigned_others'] == 1 ? true : false,
						'caof-unassigned'       => $role['cng_tkt_ao_unassigned'] == 1 ? true : false, // Change agent only fields.
						'caof-assigned-me'      => $role['cng_tkt_ao_assigned_me'] == 1 ? true : false,
						'caof-assigned-others'  => $role['cng_tkt_ao_assigned_others'] == 1 ? true : false,
						'crb-unassigned'        => $role['cng_tkt_rb_unassigned'] == 1 ? true : false, // Change raised by.
						'crb-assigned-me'       => $role['cng_tkt_rb_assigned_me'] == 1 ? true : false,
						'crb-assigned-others'   => $role['cng_tkt_rb_assigned_others'] == 1 ? true : false,
						'eth-unassigned'        => $role['delete_unassigned'] == 1 ? true : false, // Edit thread.
						'eth-assigned-me'       => $role['delete_assigned_me'] == 1 ? true : false,
						'eth-assigned-others'   => $role['delete_assigned_others'] == 1 ? true : false,
						'dth-unassigned'        => $role['delete_unassigned'] == 1 ? true : false, // Delete thread.
						'dth-assigned-me'       => $role['delete_assigned_me'] == 1 ? true : false,
						'dth-assigned-others'   => $role['delete_assigned_others'] == 1 ? true : false,
						'vl-unassigned'         => true, // View logs.
						'vl-assigned-me'        => true,
						'vl-assigned-others'    => true,
						'dtt-unassigned'        => $role['delete_unassigned'] == 1 ? true : false, // Delete ticket.
						'dtt-assigned-me'       => $role['delete_assigned_me'] == 1 ? true : false,
						'dtt-assigned-others'   => $role['delete_assigned_others'] == 1 ? true : false,
						'ar-unassigned'         => true, // Additional recipients.
						'ar-assigned-me'        => true,
						'ar-assigned-others'    => true,
						'dt-unassigned'         => true, // Duplicate ticket.
						'dt-assigned-me'        => true,
						'dt-assigned-others'    => true,
						'backend-access'        => true, // Dashboard support menu access.
						'create-as'             => true, // Create ticket on others behalf.
						'dtt-access'            => true, // Deleted ticket access.
						'eci-access'            => true, // Edit customer info.
					),
				);
			}
			update_option( 'wpsc-agent-roles', $new );

			$map = array();
			$agents = get_terms(
				array(
					'taxonomy'   => 'wpsc_agents',
					'hide_empty' => false,
				)
			);

			// import agents.
			foreach ( $agents as $prev ) {

				$is_agentgroup = get_term_meta( $prev->term_id, 'agentgroup', true );
				if ( $is_agentgroup ) {
					continue;
				}

				$user_id = get_term_meta( $prev->term_id, 'user_id', true );
				$user = get_user_by( 'id', $user_id );
				if ( ! $user->ID ) {
					continue;
				}

				// create customer record.
				$wpdb->insert(
					$wpdb->prefix . 'psmsc_customers',
					array(
						'user'  => $user_id,
						'name'  => $user->display_name,
						'email' => $user->user_email,
					)
				);

				// insert agent record.
				$wpdb->insert(
					$wpdb->prefix . 'psmsc_agents',
					array(
						'user'      => $user_id,
						'customer'  => $wpdb->insert_id,
						'role'      => get_term_meta( $prev->term_id, 'role', true ),
						'name'      => $user->display_name,
						'is_active' => 1,
					)
				);

				// adding agent role cap.
				$user->add_cap( 'wpsc_agent' );

				// agent working hrs.
				$agent_id = $wpdb->insert_id;
				for ( $i = 1; $i <= 7; $i++ ) {
					$wpdb->insert(
						$wpdb->prefix . 'psmsc_working_hrs',
						array(
							'agent'      => $agent_id,
							'day'        => $i,
							'start_time' => '00:00:00',
							'end_time'   => '23:59:59',
						)
					);
				}

				$map[ $prev->term_id ] = $agent_id;
			}

			// import agentgroups.
			$installed_addons = get_option( 'wpsc_upgrade_installed_addons' );
			if ( $installed_addons[16]['is_installed'] ) {

				foreach ( $agents as $prev ) {

					$is_agentgroup = get_term_meta( $prev->term_id, 'agentgroup', true );
					if ( ! $is_agentgroup ) {
						continue;
					}

					// insert agent record for the group.
					$wpdb->insert(
						$wpdb->prefix . 'psmsc_agents',
						array(
							'name'          => $prev->name,
							'is_agentgroup' => 1,
							'is_active'     => 1,
						)
					);
					$map[ $prev->term_id ] = $wpdb->insert_id;

					// get members of the group.
					$group_user_ids = get_term_meta( $prev->term_id, 'agentgroup_user_id' );
					$member_ids = array();
					foreach ( $group_user_ids as $user_id ) {
						$member_ids[] = $map[ $user_id ];
					}

					// create agentgroup record.
					$wpdb->insert(
						$wpdb->prefix . 'psmsc_agentgroups',
						array(
							'agent_id'    => $wpdb->insert_id,
							'name'        => $prev->name,
							'agents'      => implode( '|', $member_ids ),
							'supervisors' => implode( '|', $member_ids ),
						)
					);
				}
			}

			update_option( 'wpsc_upgrade_agent_map', $map );
		}

		/**
		 * Import ticket form field settings
		 *
		 * @return void
		 */
		public static function import_ticket_form_fields() {

			$form_fields = array();
			$cf_map = get_option( 'wpsc_upgrade_cf_term_id_map' );
			$options_map = get_option( 'wpsc_upgrade_cf_options_map' );
			$category_map = get_option( 'wpsc_upgrade_category_map' );
			$priority_map = get_option( 'wpsc_upgrade_priority_map' );
			$fields = get_terms(
				array(
					'taxonomy'   => 'wpsc_ticket_custom_fields',
					'hide_empty' => false,
					'orderby'    => 'meta_value_num',
					'meta_key'   => 'wpsc_tf_load_order',
					'order'      => 'ASC',
					'meta_query' => array(
						array(
							'key'     => 'agentonly',
							'value'   => '0',
							'compare' => '=',
						),
					),
				)
			);

			foreach ( $fields as $field ) {

				$status = get_term_meta( $field->term_id, 'wpsc_tf_status', true );
				if ( $status == 0 ) {
					continue;
				}

				$is_required = get_term_meta( $field->term_id, 'wpsc_tf_required', true );

				$width = get_term_meta( $field->term_id, 'wpsc_tf_width', true );
				switch ( $width ) {

					case '1/2':
						$width = 'half';
						break;

					case '1':
						$width = 'full';
				}

				$visibility = array();
				$prev_visibility = get_term_meta( $field->term_id, 'wpsc_tf_visibility', true );
				if ( $prev_visibility ) {
					foreach ( $prev_visibility as $condition ) {
						$condition = explode( '--', $condition );
						$cf = wpsc_get_cf_by( 'id', $cf_map[ $condition[0] ] );
						switch ( $cf->slug ) {

							case 'category':
								if ( isset( $visibility[ $cf->slug ] ) ) {
									$visibility[ $cf->slug ]['operand_val_1'][] = $category_map[ $condition[1] ];
								} else {
									$visibility[ $cf->slug ] = array(
										'operator'      => 'IN',
										'operand_val_1' => array(
											$category_map[ $condition[1] ],
										),
									);
								}
								break;

							case 'priority':
								if ( isset( $visibility[ $cf->slug ] ) ) {
									$visibility[ $cf->slug ]['operand_val_1'][] = $priority_map[ $condition[1] ];
								} else {
									$visibility[ $cf->slug ] = array(
										'operator'      => 'IN',
										'operand_val_1' => array(
											$priority_map[ $condition[1] ],
										),
									);
								}
								break;

							default:
								if ( isset( $visibility[ $cf->slug ] ) ) {
									$visibility[ $cf->slug ]['operand_val_1'][] = $options_map[ $cf->id ][ $condition[1] ];
								} else {
									$visibility[ $cf->slug ] = array(
										'operator'      => 'IN',
										'operand_val_1' => array(
											$options_map[ $cf->id ][ $condition[1] ],
										),
									);
								}
						}
					}
				}

				$cf = wpsc_get_cf_by( 'id', $cf_map[ $field->term_id ] );
				$form_fields[ $cf->slug ] = array(
					'is-required' => $is_required,
					'width'       => $width,
					'relation'    => 'OR',
					'visibility'  => $visibility ? wp_json_encode( $visibility ) : '{}',
				);
			}

			// import tff conditions.
			foreach ( $form_fields as $slug => $properties ) {
				if ( ! $properties['visibility'] ) {
					continue;
				}
				$form_fields[ $slug ]['visibility'] = WPSC_SC_Upgrade::upgrade_condition( $properties['visibility'], $properties['relation'] );
			}
			update_option( 'wpsc-tff', $form_fields );
		}

		/**
		 * Import agent and customer ticket list and filter items
		 *
		 * @return void
		 */
		public static function import_ticket_list_items() {

			$map = get_option( 'wpsc_upgrade_cf_term_id_map' );
			// import agent ticket list.
			$items = array();
			$fields = get_terms(
				array(
					'taxonomy'   => 'wpsc_ticket_custom_fields',
					'hide_empty' => false,
					'orderby'    => 'meta_value_num',
					'meta_key'   => 'wpsc_tl_agent_load_order',
					'order'      => 'ASC',
					'meta_query' => array(
						'relation' => 'AND',
						array(
							'key'     => 'wpsc_allow_ticket_list',
							'value'   => '1',
							'compare' => '=',
						),
						array(
							'key'     => 'wpsc_agent_ticket_list_status',
							'value'   => '1',
							'compare' => '=',
						),
					),
				)
			);
			foreach ( $fields as $field ) {
				$cf = wpsc_get_cf_by( 'id', $map[ $field->term_id ] );
				$items[] = $cf->slug;
			}
			update_option( 'wpsc-atl-list-items', $items );
			// import agent filter items.
			$items = array();
			$fields = get_terms(
				array(
					'taxonomy'   => 'wpsc_ticket_custom_fields',
					'hide_empty' => false,
					'orderby'    => 'meta_value_num',
					'meta_key'   => 'wpsc_filter_agent_load_order',
					'order'      => 'ASC',
					'meta_query' => array(
						'relation' => 'AND',
						array(
							'key'     => 'wpsc_allow_ticket_filter',
							'value'   => '1',
							'compare' => '=',
						),
						array(
							'key'     => 'wpsc_agent_ticket_filter_status',
							'value'   => '1',
							'compare' => '=',
						),
					),
				)
			);
			foreach ( $fields as $field ) {
				$cf = wpsc_get_cf_by( 'id', $map[ $field->term_id ] );
				if ( $cf->slug == 'name' ) {
					$items[] = 'customer';
				} else {
					$items[] = $cf->slug;
				}
			}
			update_option( 'wpsc-atl-filter-items', $items );
			// import customer list items.
			$items = array();
			$fields = get_terms(
				array(
					'taxonomy'   => 'wpsc_ticket_custom_fields',
					'hide_empty' => false,
					'orderby'    => 'meta_value_num',
					'meta_key'   => 'wpsc_tl_customer_load_order',
					'order'      => 'ASC',
					'meta_query' => array(
						'relation' => 'AND',
						array(
							'key'     => 'wpsc_allow_ticket_list',
							'value'   => '1',
							'compare' => '=',
						),
						array(
							'key'     => 'wpsc_customer_ticket_list_status',
							'value'   => '1',
							'compare' => '=',
						),
					),
				)
			);
			foreach ( $fields as $field ) {
				$cf = wpsc_get_cf_by( 'id', $map[ $field->term_id ] );
				$items[] = $cf->slug;
			}
			update_option( 'wpsc-ctl-list-items', $items );
			// import customer filter items.
			$items = array();
			$fields = get_terms(
				array(
					'taxonomy'   => 'wpsc_ticket_custom_fields',
					'hide_empty' => false,
					'orderby'    => 'meta_value_num',
					'meta_key'   => 'wpsc_filter_customer_load_order',
					'order'      => 'ASC',
					'meta_query' => array(
						'relation' => 'AND',
						array(
							'key'     => 'wpsc_allow_ticket_filter',
							'value'   => '1',
							'compare' => '=',
						),
						array(
							'key'     => 'wpsc_customer_ticket_filter_status',
							'value'   => '1',
							'compare' => '=',
						),
					),
				)
			);
			foreach ( $fields as $field ) {
				$cf = wpsc_get_cf_by( 'id', $map[ $field->term_id ] );
				if ( $cf->slug == 'name' ) {
					$items[] = 'customer';
				} else {
					$items[] = $cf->slug;
				}
			}
			update_option( 'wpsc-ctl-filter-items', $items );
		}

		/**
		 * Import ticket list more settings
		 *
		 * @return void
		 */
		public static function import_tl_more_settings() {

			$cf_map = get_option( 'wpsc_upgrade_cf_term_id_map' );
			$status_map = get_option( 'wpsc_upgrade_status_map' );
			// agent view.
			$aview = get_option( 'wpsc-tl-ms-agent-view' );
			$orderby = get_option( 'wpsc_tl_agent_orderby' );
			$orderby = get_term_by( 'slug', $orderby, 'wpsc_ticket_custom_fields' );
			$orderby = wpsc_get_cf_by( 'id', $cf_map[ $orderby->term_id ] );
			$aview['default-sort-by'] = $orderby->slug;
			$aview['default-sort-order'] = get_option( 'wpsc_tl_agent_orderby_order' );
			$aview['number-of-tickets'] = get_option( 'wpsc_tl_agent_no_of_tickets' );
			$aview['unresolved-ticket-statuses'] = array_filter(
				array_unique(
					array_map(
						function( $status_id ) use ( $status_map ) {
							return $status_map[ $status_id ];
						},
						get_option( 'wpsc_tl_agent_unresolve_statuses', array() )
					)
				)
			);
			update_option( 'wpsc-tl-ms-agent-view', $aview );
			// customer view.
			$cview = get_option( 'wpsc-tl-ms-customer-view' );
			$orderby = get_option( 'wpsc_tl_customer_orderby' );
			$orderby = get_term_by( 'slug', $orderby, 'wpsc_ticket_custom_fields' );
			$orderby = wpsc_get_cf_by( 'id', $cf_map[ $orderby->term_id ] );
			$cview['default-sort-by'] = $orderby->slug;
			$cview['default-sort-order'] = get_option( 'wpsc_tl_customer_orderby_order' );
			$cview['number-of-tickets'] = get_option( 'wpsc_tl_customer_no_of_tickets' );
			$cview['unresolved-ticket-statuses'] = array_filter(
				array_unique(
					array_map(
						function( $status_id ) use ( $status_map ) {
							return $status_map[ $status_id ];
						},
						get_option( 'wpsc_tl_customer_unresolve_statuses', array() )
					)
				)
			);
			update_option( 'wpsc-tl-ms-customer-view', $cview );
		}

		/**
		 * Import email notifications general settings
		 *
		 * @return void
		 */
		public static function import_en_general() {

			$settings = get_option( 'wpsc-en-general', array() );
			$settings['from-name'] = get_option( 'wpsc_en_from_name' );
			$settings['from-email'] = get_option( 'wpsc_en_from_email' );
			$settings['reply-to'] = get_option( 'wpsc_en_reply_to' );
			$settings['blocked-emails'] = get_option( 'wpsc_en_ignore_emails', array() );
			update_option( 'wpsc-en-general', $settings );
		}

		/**
		 * Import ticket notifications
		 *
		 * @return void
		 */
		public static function import_en_ticket_notifications() {

			$cf_map = get_option( 'wpsc_upgrade_cf_term_id_map' );
			$options_map = get_option( 'wpsc_upgrade_cf_options_map' );
			$status_map = get_option( 'wpsc_upgrade_status_map' );
			$category_map = get_option( 'wpsc_upgrade_category_map' );
			$priority_map = get_option( 'wpsc_upgrade_priority_map' );

			$new_templates = array();
			$event_map = wpsc_get_en_event_map();
			$email_templates = get_terms(
				array(
					'taxonomy'   => 'wpsc_en',
					'hide_empty' => false,
					'orderby'    => 'ID',
					'order'      => 'ASC',
				)
			);

			$string_translations = get_option( 'wpsc-string-translation' );
			foreach ( $email_templates as $template ) {

				$type = get_term_meta( $template->term_id, 'type', true );
				$subject = get_term_meta( $template->term_id, 'subject', true );
				$body = get_term_meta( $template->term_id, 'body', true );

				// conditions.
				$conditions = array();
				$prev_conditions = get_term_meta( $template->term_id, 'conditions', true );
				if ( $prev_conditions ) {
					foreach ( $prev_conditions as $key => $val ) {
						$cf = wpsc_get_cf_by( 'id', $cf_map[ $key ] );
						switch ( $cf->slug ) {

							case 'status':
								$conditions['cf_status'] = array(
									'operator'      => 'IN',
									'operand_val_1' => array_map(
										function ( $status ) use ( $status_map ) {
											return $status_map[ $status ];
										},
										$val
									),
								);
								break;

							case 'category':
								$conditions['cf_category'] = array(
									'operator'      => 'IN',
									'operand_val_1' => array_map(
										function( $category ) use ( $category_map ) {
											return $category_map[ $category ];
										},
										$val
									),
								);
								break;

							case 'priority':
								$conditions['cf_priority'] = array(
									'operator'      => 'IN',
									'operand_val_1' => array_map(
										function ( $priority ) use ( $priority_map ) {
											return $priority_map[ $priority ];
										},
										$val
									),
								);
								break;

							default:
								$conditions[ 'cf_' . $cf->slug ] = array(
									'operator'      => 'IN',
									'operand_val_1' => array_map(
										function( $option ) use ( $options_map, $cf ) {
											return $options_map[ $cf->id ][ $option ];
										},
										$val
									),
								);
						}
					}
				}

				$subject = wpsc_upgrade_macros( $subject );
				$body = wpsc_upgrade_macros( $body );
				$et = array(
					'title'      => $template->name,
					'event'      => $event_map[ $type ],
					'is_enable'  => 1,
					'subject'    => $subject,
					'body'       => array(
						'text'   => $body,
						'editor' => 'html',
					),
					'to'         => array(
						'general-recipients' => array(),
						'agent-roles'        => array(),
						'custom'             => array(),
					),
					'cc'         => array(
						'general-recipients' => array(),
						'agent-roles'        => array(),
						'custom'             => array(),
					),
					'bcc'        => wpsc_en_extract_bcc( $template ),
					'relation'   => 'AND',
					'conditions' => $conditions ? wp_json_encode( $conditions ) : '{}',
				);

				if ( count( $new_templates ) == 0 ) {
					$new_templates[1] = $et;
				} else {
					$new_templates[] = $et;
				}
				$template_id = array_key_last( $new_templates );
				$string_translations[ 'wpsc-en-tn-subject-' . $template_id ] = $subject;
				$string_translations[ 'wpsc-en-tn-body-' . $template_id ] = $body;
			}

			update_option( 'wpsc-string-translation', $string_translations );

			// Email notifications.
			foreach ( $new_templates as $index => $properties ) {
				if ( ! $properties['conditions'] ) {
					continue;
				}
				$new_templates[ $index ]['conditions'] = WPSC_SC_Upgrade::upgrade_condition( $properties['conditions'], $properties['relation'] );
			}
			update_option( 'wpsc-email-templates', $new_templates );
		}

		/**
		 * Import general settings
		 *
		 * @return void
		 */
		public static function import_general_settings() {

			global $wpdb;
			$gs = get_option( 'wpsc-gs-general' );
			$page_settings = get_option( 'wpsc-gs-page-settings' );
			$file_attachments = get_option( 'wpsc-gs-file-attachments' );
			$guest_rich_text = get_option( 'wpsc-te-guest-user' );
			$advanced = get_option( 'wpsc-ms-advanced-settings', array() );
			$roles = get_option( 'wpsc-agent-roles' );
			$status_map = get_option( 'wpsc_upgrade_status_map' );
			$category_map = get_option( 'wpsc_upgrade_category_map' );
			$priority_map = get_option( 'wpsc_upgrade_priority_map' );
			// support page.
			$page_settings['support-page'] = get_option( 'wpsc_support_page_id' );
			// default ticket status.
			$default_value = get_option( 'wpsc_default_ticket_status' );
			$default_value = $default_value ? $status_map[ $default_value ] : '1';
			$wpdb->update(
				$wpdb->prefix . 'psmsc_custom_fields',
				array( 'default_value' => $default_value ),
				array( 'id' => 7 )
			);
			// default ticket category.
			$default_value = get_option( 'wpsc_default_ticket_category' );
			$default_value = $default_value ? $category_map[ $default_value ] : '';
			$wpdb->update(
				$wpdb->prefix . 'psmsc_custom_fields',
				array( 'default_value' => $default_value ),
				array( 'id' => 9 )
			);
			// default ticket priority.
			$default_value = get_option( 'wpsc_default_ticket_priority' );
			$default_value = $default_value ? $priority_map[ $default_value ] : '';
			$wpdb->update(
				$wpdb->prefix . 'psmsc_custom_fields',
				array( 'default_value' => $default_value ),
				array( 'id' => 8 )
			);
			// ticket status after customer reply.
			$status = get_option( 'wpsc_ticket_status_after_customer_reply' );
			$gs['ticket-status-after-customer-reply'] = $status ? $status_map[ $status ] : '';
			// ticket status after agent reply.
			$status = get_option( 'wpsc_ticket_status_after_agent_reply' );
			$gs['ticket-status-after-agent-reply'] = $status ? $status_map[ $status ] : '';
			// close ticket status.
			$status = get_option( 'wpsc_close_ticket_status' );
			$gs['close-ticket-status'] = $status ? $status_map[ $status ] : '';
			// allow customer to close ticket.
			$customer = get_option( 'wpsc_allow_customer_close_ticket', 1 );
			$allow_close_ticket = $customer ? array( 'customer' ) : array();
			foreach ( $roles as $key => $role ) {
				$allow_close_ticket[] = $key;
			}
			$gs['allow-close-ticket'] = $allow_close_ticket;
			// reply form position.
			$position = get_option( 'wpsc_reply_form_position' );
			$gs['reply-form-position'] = $position == 1 ? 'top' : 'bottom';
			// ticket alice.
			$gs['ticket-alice'] = get_option( 'wpsc_ticket_alice' );
			// Attachment max filesize.
			$file_attachments['attachments-max-filesize'] = get_option( 'wpsc_attachment_max_filesize' );
			// allow guest tickets.
			$allow_create_ticket = array( 'registered-user' );
			if ( get_option( 'wpsc_allow_guest_ticket' ) == 1 ) {
				$allow_create_ticket[] = 'guest';
			}
			foreach ( $roles as $key => $role ) {
				$allow_create_ticket[] = $key;
			}
			$gs['allow-create-ticket'] = $allow_create_ticket;
			// enable otp login if guest tickets are allowed.
			if ( in_array( 'guest', $gs['allow-create-ticket'] ) ) {
				$page_settings['otp-login'] = 1;
			}
			// allow rich text editor for guest user.
			$guest_rich_text['enable'] = get_option( 'wpsc_allow_tinymce_in_guest_ticket', 0 );
			// allow reply to closed ticket after SUP-483.
			$allow_reply_to_closed = intval( get_option( 'wpsc_reply_to_close_ticket', 1 ) );
			if ( ! $allow_reply_to_closed ) {
				$options = $advanced['allow-reply-to-close-ticket'];
				unset( $options[0] );
				$advanced['allow-reply-to-close-ticket'] = $options;
			}
			// default login.
			$default_login = get_option( 'wpsc_default_login_setting', 1 );
			switch ( intval( $default_login ) ) {
				case 1:
					$page_settings['user-login'] = 'default';
					break;
				case 2:
					$page_settings['user-login'] = 'wp-default';
					break;
				case 3:
					$page_settings['user-login'] = 'custom';
					$page_settings['custom-login-url'] = get_option( 'wpsc_custom_login_url' );
					break;
			}
			// user registration.
			$user_registration = get_option( 'wpsc_user_registration', 0 );
			if ( $user_registration == 0 ) {
				$page_settings['user-registration'] = 'disable';
			} else {
				$registration_method = get_option( 'wpsc_user_registration_method', 1 );
				switch ( intval( $registration_method ) ) {
					case 1:
						$page_settings['user-registration'] = 'default';
						break;
					case 2:
						$page_settings['user-registration'] = 'wp-default';
						break;
					case 3:
						$page_settings['user-registration'] = 'custom';
						$page_settings['custom-registration-url'] = get_option( 'wpsc_custom_registration_url' );
						break;
				}
			}
			// update options.
			update_option( 'wpsc-gs-general', $gs );
			update_option( 'wpsc-gs-page-settings', $page_settings );
			update_option( 'wpsc-gs-file-attachments', $file_attachments );
			update_option( 'wpsc-te-guest-user', $guest_rich_text );
			update_option( 'wpsc-ms-advanced-settings', $advanced );
		}

		/**
		 * Re-order widgets as per old records
		 *
		 * @return void
		 */
		public static function reorder_widgets() {

			$installed_addons = get_option( 'wpsc_upgrade_installed_addons' );
			$new = get_option( 'wpsc-ticket-widget', array() );
			$prev = get_terms(
				array(
					'taxonomy'   => 'wpsc_ticket_widget',
					'hide_empty' => false,
					'orderby'    => 'meta_value_num',
					'order'      => 'ASC',
					'meta_query' => array( 'order_clause' => array( 'key' => 'wpsc_ticket_widget_load_order' ) ),
				)
			);
			$map = array(
				'status'            => 'change-status',
				'raised-by'         => 'raised-by',
				'assign-agent'      => 'assignee',
				'ticket-fields'     => 'ticket-fields',
				'agent-only-fields' => 'agentonly-fields',
				'rating'            => 'rating',
				'woo-order'         => 'woo-order',
			);

			$string_translations = get_option( 'wpsc-string-translation' );
			$reordered = array();
			foreach ( $prev as $widget ) {
				$flag = true;
				switch ( $widget->slug ) {
					case 'rating':
						if ( ! $installed_addons[15]['is_installed'] ) {
							$flag = false;
						}
						break;
					case 'woo-order':
						if ( ! $installed_addons[5]['is_installed'] ) {
							$flag = false;
						}
						break;
				}
				if ( $flag ) {
					$reordered[ $map[ $widget->slug ] ] = $new[ $map[ $widget->slug ] ];
					unset( $new[ $map[ $widget->slug ] ] );
				}
			}

			foreach ( $reordered as $key => $widget ) {
				$string_translations[ 'wpsc-twt-' . $key ] = $widget['title'];
			}

			foreach ( $new as $key => $widget ) {
				$reordered[ $key ] = $widget;
			}
			update_option( 'wpsc-string-translation', $string_translations );
			update_option( 'wpsc-ticket-widget', $reordered );
		}

		/**
		 * Thank you page import
		 *
		 * @return void
		 */
		public static function import_thankyou_settings() {

			$string_translations = get_option( 'wpsc-string-translation' );

			$thankyou_html = wpsc_upgrade_macros( get_option( 'wpsc_thankyou_html' ) );
			$thankyou_url = get_option( 'wpsc_thankyou_url' );
			if ( $thankyou_url ) {
				$action = 'url';
			} else {
				$action = 'text';
			}

			update_option(
				'wpsc-gs-thankyou-page-settings',
				array(
					'action-agent'      => $action,
					'action-customer'   => $action,
					'html-agent'        => $thankyou_html,
					'html-customer'     => $thankyou_html,
					'page-url-agent'    => $thankyou_url,
					'page-url-customer' => $thankyou_url,
					'editor-agent'      => 'html',
					'editor-customer'   => 'html',
				)
			);

			$string_translations['wpsc-thankyou-html'] = $thankyou_html;
			$string_translations['wpsc-thankyou-html-agent'] = $thankyou_html;

			update_option( 'wpsc-string-translation', $string_translations );
		}

		/**
		 * Import terms and conditions
		 *
		 * @return void
		 */
		public static function import_terms_and_conditions() {

			$string_translations = get_option( 'wpsc-string-translation' );
			$tc = get_option( 'wpsc-term-and-conditions' );
			$tandc_text = get_option( 'wpsc_terms_and_conditions_html', $tc['tandc-text'] );
			$tc['allow-term-and-conditions'] = intval( get_option( 'wpsc_terms_and_conditions', $tc['allow-term-and-conditions'] ) );
			$tc['tandc-text'] = $tandc_text;
			$string_translations['wpsc-term-and-conditions'] = $tandc_text;
			update_option( 'wpsc-term-and-conditions', $tc );

			$gdpr = get_option( 'wpsc-gdpr-settings' );
			$gdpr_text = get_option( 'wpsc_gdpr_html', $gdpr['gdpr-text'] );
			$gdpr['allow-gdpr'] = intval( get_option( 'wpsc_set_in_gdpr', $gdpr['allow-gdpr'] ) );
			$gdpr['gdpr-text'] = $gdpr_text;
			$gdpr['personal-data-retention-time'] = intval( get_option( 'wpsc_personal_data_retention_period_time', $gdpr['personal-data-retention-time'] ) );
			$unit_map = array(
				'days'   => 'days',
				'months' => 'month',
				'years'  => 'year',
			);
			$unit = get_option( 'wpsc_personal_data_retention_period_unit' );
			$gdpr['personal-data-retention-unit'] = isset( $unit_map[ $unit ] ) ? $unit_map[ $unit ] : 'days';
			$string_translations['wpsc-gdpr'] = $gdpr_text;

			update_option( 'wpsc-string-translation', $string_translations );
			update_option( 'wpsc-gdpr-settings', $gdpr );
		}

		/**
		 * Import advanced settings
		 *
		 * @return void
		 */
		public static function import_advanced_settings() {

			$advanced = get_option( 'wpsc-ms-advanced-settings' );
			$agent_editor = get_option( 'wpsc-te-agent' );
			$reg_user_editor = get_option( 'wpsc-te-registered-user' );
			$guest_editor = get_option( 'wpsc-te-guest-user' );
			// Guest can upload files.
			$guest_editor['allow-attachments'] = intval( get_option( 'wpsc_guest_can_upload_files', $guest_editor['allow-attachments'] ) );
			// public mode.
			$advanced['public-mode'] = intval( get_option( 'wpsc_ticket_public_mode', $advanced['public-mode'] ) );
			// Allow Reply Confirmation.
			$advanced['reply-confirmation'] = intval( get_option( 'wpsc_allow_reply_confirmation', $advanced['reply-confirmation'] ) );
			// Tinymce toolbar.
			$wpsc_toolbar = get_option( 'wpsc_tinymce_toolbar' );
			if ( $wpsc_toolbar !== false ) {
				$old = get_option( 'wpsc_tinymce_toolbar_active' );
				$new = array();
				foreach ( $old as $key ) {
					$new[] = $wpsc_toolbar[ $key ]['value'];
				}
				$agent_editor['toolbar'] = $new;
				$reg_user_editor['toolbar'] = $new;
				$guest_editor['toolbar'] = $new;
			}
			// Thread Date Format.
			$display_as = get_option( 'wpsc_thread_date_format' );
			$advanced['thread-date-display-as'] = $display_as == 'string' ? 'diff' : 'date';
			$advanced['thread-date-format'] = get_option( 'wpsc_thread_date_time_format', $advanced['thread-date-format'] );
			// Do not notify owner.
			$advanced['do-not-notify-owner'] = get_option( 'wpsc_do_not_notify_setting', $advanced['do-not-notify-owner'] );
			// update options.
			update_option( 'wpsc-ms-advanced-settings', $advanced );
			update_option( 'wpsc-te-agent', $agent_editor );
			update_option( 'wpsc-te-registered-user', $reg_user_editor );
			update_option( 'wpsc-te-guest-user', $guest_editor );
		}

		/**
		 * Import captcha
		 *
		 * @return void
		 */
		public static function import_captcha() {

			if (
				get_option( 'wpsc_captcha' ) == 1 &&
				get_option( 'wpsc_recaptcha_type', 1 ) == 0
			) {
				$recaptcha = get_option( 'wpsc-recaptcha-settings' );
				$recaptcha['allow-recaptcha'] = 1;
				$recaptcha['recaptcha-version'] = 2;
				$recaptcha['recaptcha-site-key'] = get_option( 'wpsc_get_site_key', '' );
				$recaptcha['recaptcha-secret-key'] = get_option( 'wpsc_get_secret_key', '' );
				update_option( 'wpsc-recaptcha-settings', $recaptcha );
			}
		}

		/**
		 * Import assign agent rules.
		 *
		 * @return void
		 */
		public static function import_assign_agent_rules() {

			global $wpdb;
			$installed_addons = get_option( 'wpsc_upgrade_installed_addons' );
			if ( ! $installed_addons[10]['is_installed'] ) {
				return;
			}

			$cf_map = get_option( 'wpsc_upgrade_cf_term_id_map' );
			$options_map = get_option( 'wpsc_upgrade_cf_options_map' );
			$category_map = get_option( 'wpsc_upgrade_category_map' );
			$priority_map = get_option( 'wpsc_upgrade_priority_map' );
			$status_map = get_option( 'wpsc_upgrade_status_map' );

			// import settings.
			$general = get_option( 'wpsc-aar-general-settings' );
			$general['auto-assign-agent'] = get_option( 'wpsc_assign_auto_responder', 0 );
			update_option( 'wpsc-aar-general-settings', $general );

			// import rules.
			$agent_map = get_option( 'wpsc_upgrade_agent_map' );
			$new_rules = array();
			$prev_rules = get_terms(
				array(
					'taxonomy'   => 'wpsc_caa',
					'hide_empty' => false,
				)
			);

			foreach ( $prev_rules as $prev ) {

				$prev_agents = get_term_meta( $prev->term_id, 'agent_ids', true );
				$new_agents = array_map(
					function( $old_id ) use ( $wpdb, $agent_map ) {
						return $wpdb->get_row( "SELECT * from {$wpdb->prefix}psmsc_agents WHERE id = " . $agent_map[ $old_id ] );
					},
					$prev_agents
				);
				$agents = array();
				$agentgroups = array();
				foreach ( $new_agents as $agent ) {
					if ( $agent->is_agentgroup ) {
						$agentgroups[] = $agent->id;
					} else {
						$agents[] = $agent->id;
					}
				}

				$conditions = array();
				$prev_conditions = get_term_meta( $prev->term_id, 'conditions', true );
				if ( $prev_conditions ) {

					foreach ( $prev_conditions as $key => $val ) {

						$cf = wpsc_get_cf_by( 'id', $cf_map[ $key ] );
						switch ( $cf->slug ) {

							case 'status':
								$conditions['cf_status'] = array(
									'operator'      => 'IN',
									'operand_val_1' => array_map(
										function( $status ) use ( $status_map ) {
											return $status_map[ $status ];
										},
										$val
									),
								);
								break;

							case 'category':
								$conditions['cf_category'] = array(
									'operator'      => 'IN',
									'operand_val_1' => array_map(
										function( $category ) use ( $category_map ) {
											return $category_map[ $category ];
										},
										$val
									),
								);
								break;

							case 'priority':
								$conditions['cf_priority'] = array(
									'operator'      => 'IN',
									'operand_val_1' => array_map(
										function( $priority ) use ( $priority_map ) {
											return $priority_map[ $priority ];
										},
										$val
									),
								);
								break;

							default:
								$conditions[ 'cf_' . $cf->slug ] = array(
									'operator'      => 'IN',
									'operand_val_1' => array_map(
										function( $option ) use ( $options_map, $cf ) {
											return $options_map[ $cf->id ][ $option ];
										},
										$val
									),
								);
						}
					}
				}

				$rule = array(
					'title'               => $prev->name,
					'agents'              => implode( '|', $agents ),
					'assign_method'       => 'assign_all',
					'agentgroups'         => implode( '|', $agentgroups ),
					'assign_group_method' => 'assign_all_groups',
					'relation'            => 'AND',
					'conditions'          => $conditions ? wp_json_encode( $conditions ) : '{}',
				);

				if ( $new_rules ) {
					$new_rules[] = $rule;
				} else {
					$new_rules[1] = $rule;
				}
			}

			// Assigned agent rules filters.
			foreach ( $new_rules as $index => $properties ) {
				$new_rules[ $index ]['conditions'] = WPSC_SC_Upgrade::upgrade_condition( $properties['conditions'], $properties['relation'] );
			}

			update_option( 'wpsc-aar-rules', $new_rules );
		}

		/**
		 * Import automatic close tickets add-on
		 *
		 * @return void
		 */
		public static function import_automatic_close_tickets() {

			$installed_addons = get_option( 'wpsc_upgrade_installed_addons' );
			if ( ! $installed_addons[0]['is_installed'] ) {
				return;
			}

			// import settings.
			$string_translations = get_option( 'wpsc-string-translation' );
			$gs = get_option( 'wpsc-gs-general' );
			$status_map = get_option( 'wpsc_upgrade_status_map' );
			$settings = get_option( 'wpsc-atc-settings' );
			$settings['statuses-enabled'] = array_map(
				function( $status_id ) use ( $status_map ) {
					return $status_map[ $status_id ];
				},
				get_option( 'wpsc_tl_statuses', array() )
			);
			$settings['age'] = get_option( 'wpsc_atc_age', '0' );
			$settings['close-status'] = $gs['close-ticket-status'];
			update_option( 'wpsc-atc-settings', $settings );

			// import warning email.
			$subject = wpsc_upgrade_macros( get_option( 'wpsc_atc_subject', '' ) );
			$body = wpsc_upgrade_macros( get_option( 'wpsc_atc_email_body', '' ) );
			$templates = array(
				array(
					'title'       => 'Default template',
					'days-before' => get_option( 'wpsc_atc_waring_email_age', 3 ),
					'subject'     => $subject,
					'body'        => $body,
					'editor'      => 'html',
				),
			);
			$string_translations['wpsc-act-subject-0'] = $subject;
			$string_translations['wpsc-act-body-0'] = $body;
			update_option( 'wpsc-string-translation', $string_translations );
			update_option( 'wpsc-atc-et', $templates );
		}

		/**
		 * Import canned reply addon
		 *
		 * @return void
		 */
		public static function import_canned_reply() {

			global $wpdb;

			$installed_addons = get_option( 'wpsc_upgrade_installed_addons' );
			if ( ! $installed_addons[1]['is_installed'] ) {
				return;
			}

			// reset data.
			$wpdb->query( 'DELETE FROM ' . $wpdb->prefix . 'psmsc_cr_categories' );
			$wpdb->query( 'ALTER TABLE ' . $wpdb->prefix . 'psmsc_cr_categories AUTO_INCREMENT = 1' );
			$wpdb->query( 'DELETE FROM ' . $wpdb->prefix . 'psmsc_canned_reply' );
			$wpdb->query( 'ALTER TABLE ' . $wpdb->prefix . 'psmsc_canned_reply AUTO_INCREMENT = 1' );

			// import canned reply categories.
			$wpdb->insert(
				$wpdb->prefix . 'psmsc_cr_categories',
				array(
					'name' => 'Uncategorized',
				)
			);
			$categories = get_terms(
				array(
					'taxonomy'   => 'wpsc_canned_reply_categories',
					'hide_empty' => false,
				)
			);

			$string_translations = get_option( 'wpsc-string-translation' );
			$map = array();
			foreach ( $categories as $prev ) {
				$wpdb->insert(
					$wpdb->prefix . 'psmsc_cr_categories',
					array(
						'name' => $prev->name,
					)
				);
				$string_translations[ 'wpsc-cr-category-' . $wpdb->insert_id ] = $prev->name;
				$map[ $prev->term_id ] = $wpdb->insert_id;
			}

			// import canned reply.
			$response = get_posts(
				array(
					'post_type'      => 'wpsc_canned_reply',
					'post_status'    => 'publish',
					'orderby'        => 'date',
					'order'          => 'ASC',
					'posts_per_page' => -1,
				)
			);
			foreach ( $response as $prev ) {
				$prev_categories = get_the_terms( $prev->ID, 'wpsc_canned_reply_categories' );
				$categories = '1';
				if ( is_array( $prev_categories ) ) {
					$categories = implode(
						'|',
						array_map(
							function( $category ) use ( $map ) {
								return $map[ $category->term_id ];
							},
							$prev_categories
						),
					);
				}
				$author = wpsc_get_customer_by( 'user_id', $prev->post_author );
				$visibility = get_post_meta( $prev->ID, 'wpsc_agent_visibility', true );
				$wpdb->insert(
					$wpdb->prefix . 'psmsc_canned_reply',
					array(
						'title'        => $prev->post_title,
						'author'       => $author->id,
						'body'         => wpautop( wpsc_upgrade_macros( $prev->post_content ) ),
						'categories'   => $categories,
						'visibility'   => $visibility ? 'public' : 'private',
						'date_created' => $prev->post_date,
					)
				);
			}
			update_option( 'wpsc-string-translation', $string_translations );
		}

		/**
		 * Import report settings
		 *
		 * @return void
		 */
		public static function import_report_settings() {

			$installed_addons = get_option( 'wpsc_upgrade_installed_addons' );
			if ( ! $installed_addons[6]['is_installed'] ) {
				return;
			}

			$agent_roles = get_option( 'wpsc-agent-roles' );
			foreach ( $agent_roles as $index => $role ) {
				$role['caps']['view-reports'] = $index == 1 ? true : false;
				$agent_roles[ $index ] = $role;
			}
			update_option( 'wpsc-agent-roles', $agent_roles );
		}

		/**
		 * Import satisfaction survey
		 *
		 * @return void
		 */
		public static function import_satisfaction_survey() {

			global $wpdb;

			$installed_addons = get_option( 'wpsc_upgrade_installed_addons' );
			if ( ! $installed_addons[15]['is_installed'] ) {
				return;
			}

			// reset data.
			$wpdb->query( 'DELETE FROM ' . $wpdb->prefix . 'psmsc_sf_ratings' );
			$wpdb->query( 'ALTER TABLE ' . $wpdb->prefix . 'psmsc_sf_ratings AUTO_INCREMENT = 1' );

			// import ratings.
			$ratings = get_terms(
				array(
					'taxonomy'   => 'wpsc_sf_rating',
					'hide_empty' => false,
					'orderby'    => 'meta_value_num',
					'order'      => 'ASC',
					'meta_query' => array( 'order_clause' => array( 'key' => 'load_order' ) ),
				)
			);

			$string_translations = get_option( 'wpsc-string-translation' );
			$map = array();
			$load_order = 1;
			$confirmation_text = get_option( 'wpsc_sf_thankyou_text', '' );
			foreach ( $ratings as $prev ) {
				$bg_color = get_term_meta( $prev->term_id, 'color', true );
				$wpdb->insert(
					$wpdb->prefix . 'psmsc_sf_ratings',
					array(
						'name'              => $prev->name,
						'color'             => '#FFFFFF',
						'bg_color'          => $bg_color,
						'confirmation_text' => $confirmation_text,
						'load_order'        => $load_order++,
					)
				);

				$string_translations[ 'wpsc-rating-name-' . $wpdb->insert_id ] = $prev->name;
				$string_translations[ 'wpsc-rating-ct-' . $wpdb->insert_id ] = $confirmation_text;
				$map[ $prev->term_id ] = $wpdb->insert_id;
			}
			update_option( 'wpsc_upgrade_sf_rating_map', $map );

			// import settings.
			$settings = get_option( 'wpsc-sf-general-setting' );
			$settings['survey-page'] = get_option( 'wpsc_sf_page', 0 );
			update_option( 'wpsc-sf-general-setting', $settings );

			// import email template.
			$subject = wpsc_upgrade_macros( get_option( 'wpsc_sf_subject', '' ) );
			$body = wpsc_upgrade_macros( get_option( 'wpsc_sf_email_body', '' ) );
			$days_after = get_option( 'wpsc_sf_age', '0' );
			if ( get_option( 'wpsc_sf_age_unit', 'd' ) == 'h' ) {
				$days_after = 1;
			}
			$new = array(
				array(
					'title'      => 'Default template',
					'days-after' => $days_after,
					'subject'    => $subject,
					'body'       => $body,
					'editor'     => 'html',
				),
			);
			$string_translations['wpsc-sf-et-subject-0'] = $subject;
			$string_translations['wpsc-sf-et-body-0'] = $body;
			update_option( 'wpsc-string-translation', $string_translations );
			update_option( 'wpsc-sf-et', $new );
		}

		/**
		 * Import email piping add-on settings
		 *
		 * @return void
		 */
		public static function import_email_piping() {

			$installed_addons = get_option( 'wpsc_upgrade_installed_addons' );
			if ( ! $installed_addons[3]['is_installed'] ) {
				return;
			}

			// import settings.
			$general = get_option( 'wpsc-ep-general-settings' );
			$general['connection'] = get_option( 'wpsc_ep_piping_type', 'imap' );

			$block_emails = array_merge( $general['block-emails'], get_option( 'wpsc_ep_block_emails', array() ) );
			$general['block-emails'] = array_filter( array_map( 'trim', $block_emails ) );

			$block_subject = array_merge( $general['block-subject'], explode( PHP_EOL, get_option( 'wpsc_ep_block_subject', '' ) ) );
			$general['block-subject'] = array_filter( array_map( 'trim', $block_subject ) );

			$allowed_users = get_option( 'wpsc_ep_allowed_user', 0 );
			$general['allowed-users'] = $allowed_users ? 'anyone' : 'registered';
			$general['time-frequency'] = get_option( 'wpsc_ep_cron_execution_time', 5 );
			$general['body-reference'] = get_option( 'wpsc_ep_email_type', 'text' );
			update_option( 'wpsc-ep-general-settings', $general );

			// import connection settings.
			if ( $general['connection'] == 'imap' ) {

				$imap = array(
					'email-address'        => get_option( 'wpsc_ep_imap_email_address', '' ),
					'password'             => get_option( 'wpsc_ep_imap_email_password', '' ),
					'encryption'           => get_option( 'wpsc_ep_imap_encryption', 'ssl' ),
					'incoming-mail-server' => get_option( 'wpsc_ep_imap_incoming_mail_server', '' ),
					'port'                 => get_option( 'wpsc_ep_imap_port', '' ),
					'is_active'            => 0,
					'last-error'           => '',
				);

				// check connection.
				$encryption_text = '';
				if ( $imap['encryption'] == 'none' ) {
					$encryption_text = 'novalidate-cert';
				} elseif ( $imap['encryption'] == 'ssl' ) {
					$encryption_text = 'imap/ssl/novalidate-cert';
				}
				$conn = @imap_open( '{' . $imap['incoming-mail-server'] . ':' . $imap['port'] . '/' . $encryption_text . '}INBOX', $imap['email-address'], $imap['password'] ); // phpcs:ignore
				if ( $conn ) {
					$imap['is_active'] = 1;
				} else {
					$imap['last-error'] = imap_last_error();
				}

				update_option( 'wpsc-ep-imap-settings', $imap );

			} else { // gmail.

				$gmail = array(
					'email-address' => get_option( 'wpsc_ep_email_address', '' ),
					'client-id'     => get_option( 'wpsc_ep_client_id', '' ),
					'client-secret' => get_option( 'wpsc_ep_client_secret', '' ),
					'is-active'     => 0,
					'last-error'    => '',
					'refresh-token' => get_option( 'wpsc_ep_refresh_token', '' ),
					'history-id'    => get_option( 'wpsc_ep_historyId', '' ),
				);
				if (
					$gmail['email-address'] &&
					$gmail['client-id'] &&
					$gmail['client-secret'] &&
					$gmail['refresh-token'] &&
					$gmail['history-id']
				) {
					$gmail['is-active'] = 1;
				}
				update_option( 'wpsc-ep-gmail-settings', $gmail );
			}

			// import email piping rules.
			$cf_map = get_option( 'wpsc_upgrade_cf_term_id_map' );
			$category_map = get_option( 'wpsc_upgrade_category_map' );
			$priority_map = get_option( 'wpsc_upgrade_priority_map' );
			$new_rules = array();

			$fields = get_terms(
				array(
					'taxonomy'   => 'wpsc_ticket_custom_fields',
					'hide_empty' => false,
					'meta_query' => array(
						'relation' => 'AND',
						array(
							'key'     => 'agentonly',
							'value'   => array( 0, 1 ),
							'compare' => 'IN',
						),
						array(
							'key'     => 'wpsc_tf_type',
							'value'   => '0',
							'compare' => '>',
						),
					),
				)
			);

			$email_piping_rules = get_terms(
				array(
					'taxonomy'   => 'wpsc_ep_rules',
					'hide_empty' => false,
					'orderby'    => 'meta_value_num',
					'order'      => 'ASC',
					'meta_query' => array( 'order_clause' => array( 'key' => 'wpsc_en_rule_load_order' ) ),
				)
			);

			foreach ( $email_piping_rules as $prev ) {

				$rule = array(
					'title' => $prev->name,
				);

				// condition.
				$forwarding_address = get_term_meta( $prev->term_id, 'wpsc_ep_to_address', true );
				$forwarding_address = $forwarding_address ? $forwarding_address : array();
				$rule['forwarding-address'] = array_filter( array_map( 'trim', $forwarding_address ) );

				$has_words = get_term_meta( $prev->term_id, 'wpsc_ep_has_words', true );
				$has_words = $has_words ? $has_words : array();
				$rule['has-words'] = array_filter( array_map( 'trim', $has_words ) );

				// category and priority.
				$category = get_term_meta( $prev->term_id, 'ticket_category', true );
				if ( $category ) {
					$rule['category'] = $category_map[ $category ];
				}
				$priority = get_term_meta( $prev->term_id, 'ticket_priority', true );
				if ( $priority ) {
					$rule['priority'] = $priority_map[ $priority ];
				}

				// custom fields.
				foreach ( $fields as $field ) {

					$type = get_term_meta( $field->term_id, 'wpsc_tf_type', true );
					$cf = wpsc_get_cf_by( 'id', $cf_map[ $field->term_id ] );

					switch ( $type ) {

						case 1:
						case 5:
						case 7:
						case 8:
						case 9:
							$val = get_term_meta( $prev->term_id, $field->slug, true );
							if ( $val ) {
								$rule[ $cf->slug ] = $val ? $val : '';
							}
							break;

						case 2:
						case 4:
							$options_map = get_option( 'wpsc_upgrade_cf_options_map' );
							$val = get_term_meta( $prev->term_id, $field->slug, true );
							if ( $val ) {
								$rule[ $cf->slug ] = $options_map[ $cf->id ][ $val ];
							}
							break;

						case 3:
							$options_map = get_option( 'wpsc_upgrade_cf_options_map' );
							$val = get_term_meta( $prev->term_id, $field->slug );
							if ( $val ) {
								$rule[ $cf->slug ] = implode(
									'|',
									array_filter(
										array_map(
											function( $name ) use ( $options_map, $cf ) {
												return $name ? $options_map[ $cf->id ][ $name ] : false;
											},
											$val
										)
									)
								);
							}
							break;

						case 6:
							$val = get_term_meta( $prev->term_id, $field->slug, true );
							if ( $val ) {
								$val = new DateTime( $val );
								$rule[ $cf->slug ] = $val->format( 'Y-m-d' );
							}
					}
				}

				if ( $new_rules ) {
					$new_rules[] = $rule;
				} else {
					$new_rules[1] = $rule;
				}
			}

			update_option( 'wpsc-ep-pipe-rules', $new_rules );
		}

		/**
		 * Import FAQ integration add-on settings
		 *
		 * @return void
		 */
		public static function import_faq_integration() {

			$installed_addons = get_option( 'wpsc_upgrade_installed_addons' );
			if ( ! $installed_addons[11]['is_installed'] ) {
				return;
			}

			$map = array(
				'WPSC_Ultimate_FAQ' => 'ultimate_faq',
				'WPSC_Acronix_FAQ'  => 'acronix_faq',
			);
			$prev = get_option( 'wpsc_select_faq_set', '' );
			if ( $prev ) {
				update_option(
					'wpsc-faq-settings',
					array(
						'faq' => $map[ $prev ],
					)
				);
			}
		}

		/**
		 * Import knowledgebase integration
		 *
		 * @return void
		 */
		public static function import_knowledgebase_integration() {

			$installed_addons = get_option( 'wpsc_upgrade_installed_addons' );
			if ( ! $installed_addons[12]['is_installed'] ) {
				return;
			}

			$map = array(
				'WPSC_PrassApp_Knowledgbase'  => 'pressapp_kb',
				'WPSC_WP_Knowledgbase'        => 'wp_kb',
				'WPSC_HelpGuru_Knowledgbase'  => 'ht_kb',
				'WPSC_BasePress_Knowledgbase' => 'basepress_kb',
				'WPSC_BWL_Knowledgbase'       => 'bwl_kb',
				'WPSC_Echo_Knowledgbase'      => 'echo_kb',
			);
			$prev = get_option( 'wpsc_select_knowledgbase_set', '' );
			if ( $prev ) {
				update_option(
					'wpsc-kb-settings',
					array(
						'knowledgebase' => $map[ $prev ],
					)
				);
			}
		}

		/**
		 * Import export add-on settings
		 *
		 * @return void
		 */
		public static function import_export_addon() {

			$installed_addons = get_option( 'wpsc_upgrade_installed_addons' );
			if ( ! $installed_addons[2]['is_installed'] ) {
				return;
			}

			// visibility.
			$new_visibility = array();
			$prev_visibility = get_option( 'wpsc_selected_user_roll_data', array() );
			foreach ( $prev_visibility as $visibility ) {
				if ( $visibility == 'customer' ) {
					$new_visibility[] = 'registered-user';
					$new_visibility[] = 'guest';
					continue;
				}
				$new_visibility[] = $visibility;
			}

			update_option(
				'wpsc-export-roles',
				array(
					'allow-export-ticket' => $new_visibility,
				)
			);

			// export fields.
			$cf_slug_map = get_option( 'wpsc_upgrade_cf_slug_map' );
			$prev_items = get_option( 'wpsc_export_ticket_list', array() );
			$new_items = array();
			foreach ( $prev_items as $slug ) {
				$new_items[] = $cf_slug_map[ $slug ];
			}
			update_option( 'wpsc-agent-export-settings', $new_items );
			update_option( 'wpsc-register-export-settings', $new_items );
		}

		/**
		 * Import woocommerce settings
		 *
		 * @return void
		 */
		public static function import_woocommerce_addon() {

			$installed_addons = get_option( 'wpsc_upgrade_installed_addons' );
			if ( ! $installed_addons[5]['is_installed'] ) {
				return;
			}

			$string_translations = get_option( 'wpsc-string-translation' );
			// settings.
			$tab_lable = get_option( 'wpsc_dashboard_support_tab_label', '' );
			$help_lable = get_option( 'wpsc_order_help_button_label', '' );
			$settings = get_option( 'wpsc-woo-settings' );
			$settings['dashboard-support-tab'] = intval( get_option( 'wpsc_dashboard_support_tab', 1 ) );
			$settings['dashboard-support-tab-label'] = $tab_lable;
			$settings['order-help-button'] = intval( get_option( 'wpsc_order_help_button', 0 ) );
			$settings['order-help-button-label'] = $help_lable;
			update_option( 'wpsc-woo-settings', $settings );

			$string_translations['wpsc-woo-dashboard-tab-label'] = $tab_lable;
			$string_translations['wpsc-woo-order-help-button-label'] = $help_lable;
			update_option( 'wpsc-string-translation', $string_translations );
		}

		/**
		 * Import schedule tickets
		 *
		 * @return void
		 */
		public static function import_shedule_tickets() {

			global $wpdb;
			$installed_addons = get_option( 'wpsc_upgrade_installed_addons' );
			if ( ! $installed_addons[14]['is_installed'] ) {
				return;
			}

			$cf_map = get_option( 'wpsc_upgrade_cf_term_id_map' );
			$category_map = get_option( 'wpsc_upgrade_category_map' );
			$priority_map = get_option( 'wpsc_upgrade_priority_map' );
			$new_rules = array();

			$fields = get_terms(
				array(
					'taxonomy'   => 'wpsc_ticket_custom_fields',
					'hide_empty' => false,
					'meta_query' => array(
						'relation' => 'AND',
						array(
							'key'     => 'agentonly',
							'value'   => array( 0, 1 ),
							'compare' => 'IN',
						),
						array(
							'key'     => 'wpsc_tf_type',
							'value'   => '0',
							'compare' => '>',
						),
					),
				)
			);

			$scheule_tickets = get_terms(
				array(
					'taxonomy'   => 'wpsc_schedule_tickets',
					'hide_empty' => false,
				)
			);

			foreach ( $scheule_tickets as $prev ) {

				$time_unit = get_term_meta( $prev->term_id, 'repeat_every_time_unit', true );
				$recurrence = get_term_meta( $prev->term_id, 'no_of_recurrence', true );
				$start_date = new DateTime( get_term_meta( $prev->term_id, 'start_date', true ) . ' 00:00:00' );

				$rule = array(
					'title'                          => $prev->name,
					'recurrence-period'              => 'daily',
					'daily-recurrence-type'          => 'daily-every-day',
					'daily-x-days'                   => 1,
					'daily-x-work-days'              => 1,
					'weekly-x-weeks'                 => 1,
					'weekly-days'                    => array( 1, 2, 3, 4, 5 ),
					'monthly-recurrence-type'        => 'monthly-day-number',
					'monthly-day-number-x-months'    => 1,
					'monthly-day-number-day'         => 1,
					'monthly-week-number-x-months'   => 1,
					'monthly-week-number-occurrence' => 1,
					'monthly-week-number-day'        => 1,
					'yearly-recurrence-type'         => 'yearly-day-number',
					'yearly-day-number-x-years'      => 1,
					'yearly-day-number-day'          => 1,
					'yearly-day-number-month'        => 1,
					'yearly-week-number-x-years'     => 1,
					'yearly-week-number-occurrence'  => 1,
					'yearly-week-number-day'         => 1,
					'yearly-week-number-month'       => 1,
					'starts-on'                      => ( new DateTime() )->format( 'Y-m-d' ),
					'ends-on'                        => 'no-end-date',
					'ends-after-times'               => 10,
					'end-date'                       => '',
					'ticket-count'                   => 0,
				);

				if ( $time_unit == 'days' ) {

					$rule['recurrence-period'] = 'daily';
					$rule['daily-recurrence-type'] = 'daily-every-day';
					$rule['daily-x-days'] = intval( $recurrence );

				} else {

					$rule['recurrence-period'] = 'monthly';
					$rule['monthly-recurrence-type'] = 'monthly-day-number';
					$rule['monthly-day-number-x-months'] = intval( $recurrence );
					$rule['monthly-day-number-day'] = $start_date->format( 'd' );
				}

				$rule['starts-on'] = $start_date->format( 'Y-m-d' );
				$rule['ends-on'] = 'no-end-date';

				// customer.
				$customer_name = get_term_meta( $prev->term_id, 'customer_name', true );
				$customer_email = get_term_meta( $prev->term_id, 'customer_email', true );
				$customer_id = $wpdb->get_var( "SELECT * FROM {$wpdb->prefix}psmsc_customers WHERE email = '" . $customer_email . "'" );
				if ( ! $customer_id ) {
					$user = get_user_by( 'email', $customer_email );
					if ( $user ) {
						$wpdb->insert(
							$wpdb->prefix . 'psmsc_customers',
							array(
								'user'  => $user->ID,
								'name'  => $user->display_name,
								'email' => $user->user_email,
							)
						);
					} else {
						$wpdb->insert(
							$wpdb->prefix . 'psmsc_customers',
							array(
								'user'  => 0,
								'name'  => $customer_name,
								'email' => $customer_email,
							)
						);
					}
					$customer_id = $wpdb->insert_id;
				}
				$rule['customer'] = $customer_id;

				// subject, description, category & priority.
				$rule['subject'] = get_term_meta( $prev->term_id, 'ticket_subject', true );
				$rule['description'] = get_term_meta( $prev->term_id, 'ticket_description', true );
				$category = get_term_meta( $prev->term_id, 'ticket_category', true );
				if ( $category ) {
					$rule['category'] = $category_map[ $category ];
				}
				$priority = get_term_meta( $prev->term_id, 'ticket_priority', true );
				if ( $priority ) {
					$rule['priority'] = $priority_map[ $priority ];
				}

				// custom fields.
				foreach ( $fields as $field ) {

					$type = get_term_meta( $field->term_id, 'wpsc_tf_type', true );
					$cf = wpsc_get_cf_by( 'id', $cf_map[ $field->term_id ] );

					switch ( $type ) {

						case 1:
						case 5:
						case 7:
						case 8:
						case 9:
							$val = get_term_meta( $prev->term_id, $field->slug, true );
							if ( $val ) {
								$rule[ $cf->slug ] = $val ? $val : '';
							}
							break;

						case 2:
						case 4:
							$options_map = get_option( 'wpsc_upgrade_cf_options_map' );
							$val = get_term_meta( $prev->term_id, $field->slug, true );
							if ( $val ) {
								$rule[ $cf->slug ] = $options_map[ $cf->id ][ $val ];
							}
							break;

						case 3:
							$options_map = get_option( 'wpsc_upgrade_cf_options_map' );
							$val = get_term_meta( $prev->term_id, $field->slug );
							if ( $val ) {
								$rule[ $cf->slug ] = implode(
									'|',
									array_filter(
										array_map(
											function( $name ) use ( $options_map, $cf ) {
												return $name ? $options_map[ $cf->id ][ $name ] : false;
											},
											$val
										)
									)
								);
							}
							break;

						case 6:
							$val = get_term_meta( $prev->term_id, $field->slug, true );
							if ( $val ) {
								$val = new DateTime( $val );
								$rule[ $cf->slug ] = $val->format( 'Y-m-d' );
							}
					}
				}

				if ( ! $new_rules ) {
					$new_rules[1] = $rule;
				} else {
					$new_rules[] = $rule;
				}
			}

			update_option( 'wpsc-st-rules', $new_rules );
		}

		/**
		 * Import SLA settings
		 *
		 * @return void
		 */
		public static function import_sla_settings() {

			$installed_addons = get_option( 'wpsc_upgrade_installed_addons' );
			if ( ! $installed_addons[4]['is_installed'] ) {
				return;
			}

			$cf_map = get_option( 'wpsc_upgrade_cf_term_id_map' );
			$options_map = get_option( 'wpsc_upgrade_cf_options_map' );
			$category_map = get_option( 'wpsc_upgrade_category_map' );
			$priority_map = get_option( 'wpsc_upgrade_priority_map' );
			$status_map = get_option( 'wpsc_upgrade_status_map' );

			// settings.
			$settings = get_option( 'wpsc-sla-general-settings' );
			$settings['out-sla-bg-color'] = get_option( 'wpsc_out_sla_color', $settings['out-sla-bg-color'] );
			$settings['out-sla-text-color'] = '#ffffff';
			$settings['in-sla-bg-color'] = get_option( 'wpsc_in_sla_color', $settings['in-sla-bg-color'] );
			$settings['in-sla-text-color'] = '#ffffff';
			update_option( 'wpsc-sla-general-settings', $settings );

			// sla policies.
			$policies = get_terms(
				array(
					'taxonomy'   => 'wpsc_sla',
					'hide_empty' => false,
					'orderby'    => 'meta_value_num',
					'order'      => 'ASC',
					'meta_query' => array( 'order_clause' => array( 'key' => 'load_order' ) ),
				)
			);

			$new_policies = array();
			$count = 0;
			$map = array();

			foreach ( $policies as $prev ) {

				$time_unit_map = array(
					'minutes' => 'minute',
					'hours'   => 'hour',
					'days'    => 'day',
					'months'  => 'month',
					'years'   => 'year',
				);
				$time = get_term_meta( $prev->term_id, 'time', true );
				$time_unit = get_term_meta( $prev->term_id, 'time_unit', true );

				$conditions = array();
				$prev_conditions = get_term_meta( $prev->term_id, 'conditions', true );
				if ( $prev_conditions ) {

					foreach ( $prev_conditions as $key => $val ) {

						$cf = wpsc_get_cf_by( 'id', $cf_map[ $key ] );
						switch ( $cf->slug ) {

							case 'status':
								$conditions['cf_status'] = array(
									'operator'      => 'IN',
									'operand_val_1' => array_map(
										function( $status ) use ( $status_map ) {
											return $status_map[ $status ];
										},
										$val
									),
								);
								break;

							case 'category':
								$conditions['cf_category'] = array(
									'operator'      => 'IN',
									'operand_val_1' => array_map(
										function( $category ) use ( $category_map ) {
											return $category_map[ $category ];
										},
										$val
									),
								);
								break;

							case 'priority':
								$conditions['cf_priority'] = array(
									'operator'      => 'IN',
									'operand_val_1' => array_map(
										function( $priority ) use ( $priority_map ) {
											return $priority_map[ $priority ];
										},
										$val
									),
								);
								break;

							default:
								$conditions[ 'cf_' . $cf->slug ] = array(
									'operator'      => 'IN',
									'operand_val_1' => array_map(
										function( $option ) use ( $options_map, $cf ) {
											return $options_map[ $cf->id ][ $option ];
										},
										$val
									),
								);
						}
					}
				}

				$policy = array(
					'title'          => $prev->name,
					'time'           => $time,
					'time-unit'      => $time_unit_map[ $time_unit ],
					'calculate-from' => 'date_updated',
					'relation'       => 'AND',
					'conditions'     => $conditions ? wp_json_encode( $conditions ) : '{}',
				);

				$new_policies[ ++$count ] = $policy;
				$map[ $prev->term_id ] = $count;
			}

			// SLA policy filters.
			foreach ( $new_policies as $index => $properties ) {
				$new_policies[ $index ]['conditions'] = WPSC_SC_Upgrade::upgrade_condition( $properties['conditions'], $properties['relation'] );
			}

			update_option( 'wpsc-sla-policies', $new_policies );
			update_option( 'wpsc_upgrade_sla_policy_map', $map );
		}

		/**
		 * Import usergroup settings
		 *
		 * @return void
		 */
		public static function import_usergroup_settings() {

			global $wpdb;
			$installed_addons = get_option( 'wpsc_upgrade_installed_addons' );
			if ( ! $installed_addons[7]['is_installed'] ) {
				return;
			}

			$category_map = get_option( 'wpsc_upgrade_category_map' );
			$usergroups = get_terms(
				array(
					'taxonomy'   => 'wpsc_usergroup_data',
					'hide_empty' => false,
				)
			);

			foreach ( $usergroups as $prev ) {

				$members = array();
				$prev_members = get_term_meta( $prev->term_id, 'wpsc_usergroup_userid' );
				foreach ( $prev_members as $user_id ) {
					$customer_id = $wpdb->get_var( "SELECT id FROM {$wpdb->prefix}psmsc_customers WHERE user = " . $user_id );
					if ( ! $customer_id ) {
						$user = get_user_by( 'id', $user_id );
						$wpdb->insert(
							$wpdb->prefix . 'psmsc_customers',
							array(
								'user'  => $user_id,
								'name'  => $user->display_name,
								'email' => $user->user_email,
							)
						);
						$customer_id = $wpdb->insert_id;
					}
					$members[] = $customer_id;
				}

				$supervisors = array();
				$prev_supervisors = get_term_meta( $prev->term_id, 'wpsc_usergroup_supervisor_id' );
				foreach ( $prev_supervisors as $user_id ) {
					$supervisors[] = $wpdb->get_var( "SELECT id FROM {$wpdb->prefix}psmsc_customers WHERE user = " . $user_id );
				}

				$category = get_term_meta( $prev->term_id, 'wpsc_usergroup_category', true );

				$wpdb->insert(
					$wpdb->prefix . 'psmsc_usergroups',
					array(
						'name'        => $prev->name,
						'members'     => implode( '|', $members ),
						'supervisors' => implode( '|', $supervisors ),
						'category'    => $category ? $category_map[ $category ] : '',
					)
				);
			}
		}

		/**
		 * Import single ticket
		 *
		 * @param WP_Post $ticket - ticket post object.
		 * @return void
		 */
		public static function import_individual_ticket( $ticket ) {

			global $wpdb;

			$installed_addons = get_option( 'wpsc_upgrade_installed_addons' );

			// destroy existing ticket if already imported.
			$ticket_id = $wpdb->get_var( "SELECT id FROM {$wpdb->prefix}psmsc_tickets WHERE id = " . get_post_meta( $ticket->ID, 'ticket_id', true ) );
			if ( $ticket_id ) {
				wpsc_destroy_existing_ticket( $ticket_id );
			}

			$fields = get_terms(
				array(
					'taxonomy'   => 'wpsc_ticket_custom_fields',
					'hide_empty' => false,
					'orderby'    => 'term_id',
					'order'      => 'ASC',
				)
			);

			$cf_map = get_option( 'wpsc_upgrade_cf_term_id_map' );
			$data = array();

			foreach ( $fields as $prev ) {

				$type = get_term_meta( $prev->term_id, 'wpsc_tf_type', true );

				if ( ! isset( $cf_map[ $prev->term_id ] ) || $prev->slug == 'ticket_description' ) {
					continue;
				}

				$cf = wpsc_get_cf_by( 'id', $cf_map[ $prev->term_id ] );

				if ( intval( $type ) === 0 ) {

					switch ( $prev->slug ) {

						case 'ticket_id':
						case 'customer_name':
						case 'customer_email':
						case 'ticket_subject':
						case 'date_created':
						case 'date_updated':
							$data[ $cf->slug ] = get_post_meta( $ticket->ID, $prev->slug, true );
							break;

						case 'ticket_status':
							$map = get_option( 'wpsc_upgrade_status_map' );
							$val = get_post_meta( $ticket->ID, $prev->slug, true );
							$data[ $cf->slug ] = $map[ $val ];
							break;

						case 'ticket_category':
							$map = get_option( 'wpsc_upgrade_category_map' );
							$val = get_post_meta( $ticket->ID, $prev->slug, true );
							$data[ $cf->slug ] = $map[ $val ];
							break;

						case 'ticket_priority':
							$map = get_option( 'wpsc_upgrade_priority_map' );
							$val = get_post_meta( $ticket->ID, $prev->slug, true );
							$data[ $cf->slug ] = $map[ $val ];
							break;

						case 'agent_created':
							$map = get_option( 'wpsc_upgrade_agent_map' );
							$val = get_post_meta( $ticket->ID, $prev->slug, true );
							$data[ $cf->slug ] = $val ? $map[ $val ] : 0;
							break;

						case 'assigned_agent':
							$map = get_option( 'wpsc_upgrade_agent_map' );
							$val = array_filter(
								array_map(
									function( $agent_id ) use ( $map ) {
										return $agent_id ? $map[ $agent_id ] : '';
									},
									get_post_meta( $ticket->ID, $prev->slug )
								)
							);
							$data[ $cf->slug ] = implode( '|', $val );
							break;

						case 'sf_rating':
							$map = get_option( 'wpsc_upgrade_sf_rating_map' );
							$val = get_post_meta( $ticket->ID, $prev->slug, true );
							if ( is_numeric( $val ) ) {
								$data[ $cf->slug ] = $map[ $val ];
							}
							break;

						case 'sla':
							$val = get_post_meta( $ticket->ID, $prev->slug, true );
							if ( $val && $val != '3099-01-01 00:00:00' ) {
								$policy_map = get_option( 'wpsc_upgrade_sla_policy_map' );
								$data[ $cf->slug ] = $val;
								$data['sla_policy'] = $policy_map[ get_post_meta( $ticket->ID, 'sla_term', true ) ];
								$data['od_email'] = get_post_meta( $ticket->ID, 'wpsp_out_of_sla_email_send', true );
							}
							break;
					}
				} else {

					switch ( intval( $type ) ) {

						case 1:
						case 5:
						case 6:
						case 7:
						case 8:
						case 9:
						case 11:
						case 12:
							$data[ $cf->slug ] = get_post_meta( $ticket->ID, $prev->slug, true );
							break;

						case 2:
						case 4:
							$options = wpsc_get_cf_options( $cf->id );
							$val = get_post_meta( $ticket->ID, $prev->slug, true );
							foreach ( $options as $option ) {
								if ( $option->name == $val ) {
									$data[ $cf->slug ] = $option->id;
									break;
								}
							}
							break;

						case 3:
							$options = wpsc_get_cf_options( $cf->id );
							$prev_val = get_post_meta( $ticket->ID, $prev->slug );
							$new_val = array();
							foreach ( $options as $option ) {
								if ( in_array( $option->name, $prev_val ) ) {
									$new_val[] = $option->id;
								}
							}
							$data[ $cf->slug ] = implode( '|', $new_val );
							break;

						case 10:
							$prev_val = array_filter( get_post_meta( $ticket->ID, $prev->slug ) );
							$new_val = array();
							$upload_dir   = wp_upload_dir();
							foreach ( $prev_val as $item ) {
								$term_meta = get_term_meta( $item );
								if ( ! $term_meta ) {
									continue;
								}
								$wpdb->insert(
									$wpdb->prefix . 'psmsc_attachments',
									array(
										'name'         => $term_meta['filename'][0],
										'file_path'    => '/wpsc/' . $term_meta['save_file_name'][0],
										'is_image'     => $term_meta['is_image'][0],
										'is_active'    => 1,
										'date_created' => $term_meta['time_uploaded'][0],
										'source'       => 'cf',
										'source_id'    => $cf->id,
										'ticket_id'    => $data['id'],
									)
								);
								$new_val[] = $wpdb->insert_id;
							}
							$data[ $cf->slug ] = implode( '|', $new_val );
							break;
					}
				}
			}

			// customer record if not exists.
			$customer = wpsc_import_customer( $data['name'], $data['email'] );
			$data['customer'] = $customer->id;
			unset( $data['name'] );
			unset( $data['email'] );

			// user type.
			$data['user_type'] = $customer->user ? 'registered' : 'guest';

			// deleted.
			$data['is_active'] = $ticket->post_status == 'publish' ? 1 : 0;

			// Auth code.
			$data['auth_code'] = get_post_meta( $ticket->ID, 'ticket_auth_code', true );

			// create ticket record.
			$wpdb->insert(
				$wpdb->prefix . 'psmsc_tickets',
				$data
			);

			$ticket = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}psmsc_tickets WHERE id = " . $wpdb->insert_id );

			// import ticket threads.
			self::import_ticket_threads( $ticket );
		}

		/**
		 * Import ticket threads
		 *
		 * @param stdClass $ticket - ticket object.
		 * @return void
		 */
		public static function import_ticket_threads( $ticket ) {

			global $wpdb;
			$gs = get_option( 'wpsc-gs-general' );
			$tl_advanced = get_option( 'wpsc-tl-ms-advanced' );
			$installed_addons = get_option( 'wpsc_upgrade_installed_addons' );

			$threads = get_posts(
				array(
					'post_type'      => 'wpsc_ticket_thread',
					'post_status'    => 'publish',
					'orderby'        => 'date',
					'order'          => 'ASC',
					'posts_per_page' => -1,
					'meta_query'     => array(
						'relation' => 'AND',
						array(
							'key'     => 'ticket_id',
							'value'   => $ticket->id,
							'compare' => '=',
						),
					),
				)
			);

			// ticket update data.
			$ticket_data = array();

			// last reply thread.
			$last_thread = null;

			// first response delay.
			$frd = 0;

			// average response delay.
			$ard = 0;

			// communication gap.
			$cg = 1;

			// report temp variables.
			$count = 0;
			$last_reply_by = 'customer';
			$last_reply_time = new DateTime( $ticket->date_created );
			$delay = 0;

			foreach ( $threads as $thread ) {

				$thread_type = get_post_meta( $thread->ID, 'thread_type', true );

				if ( in_array( $thread_type, array( 'report', 'reply', 'note' ) ) ) {

					// thread_customer import.
					$customer_name  = get_post_meta( $thread->ID, 'customer_name', true );
					$customer_email = get_post_meta( $thread->ID, 'customer_email', true );
					$customer = wpsc_import_customer( $customer_name, $customer_email );

					// create thread record.
					$wpdb->insert(
						$wpdb->prefix . 'psmsc_threads',
						array(
							'ticket'       => $ticket->id,
							'customer'     => $customer->id,
							'type'         => $thread_type,
							'body'         => $thread->post_content,
							'date_created' => $thread->post_date,
							'date_updated' => $thread->post_date,
						)
					);
					$new_thread = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}psmsc_threads WHERE id = " . $wpdb->insert_id );

					// Calculate response delays.
					if ( $new_thread->type == 'reply' ) {
						$cg++;
						$current_reply_by = 'customer';
						if ( $ticket->customer != $new_thread->customer && wpsc_is_agent( $new_thread->customer ) ) {
							$current_reply_by = 'agent';
						}
						if ( $last_reply_by == 'customer' && $current_reply_by == 'agent' ) {
							$count++;
							$diff = ceil( abs( $last_reply_time->getTimestamp() - ( new DateTime( $new_thread->date_created ) )->getTimestamp() ) / 60 );
							if ( $count == 1 ) {
								$frd = $diff;
							}
							$delay += $diff;
						}
						if ( $last_reply_by == 'agent' && $current_reply_by == 'customer' ) {
							$last_reply_time = new DateTime( $new_thread->date_created );
						}
						$last_reply_by = $current_reply_by;
					}

					if ( $new_thread->type == 'report' || $new_thread->type == 'reply' ) {
						$last_thread = $new_thread;
					}

					// Handle attachments.
					$prev = get_post_meta( $thread->ID, 'attachments', true );
					$new = array();
					$upload_dir   = wp_upload_dir();
					foreach ( $prev as $item ) {

						$term_meta = get_term_meta( $item );
						if ( ! $term_meta ) {
							continue;
						}

						$wpdb->insert(
							$wpdb->prefix . 'psmsc_attachments',
							array(
								'name'         => $term_meta['filename'][0],
								'file_path'    => '/wpsc/' . $term_meta['save_file_name'][0],
								'is_image'     => $term_meta['is_image'][0],
								'is_active'    => 1,
								'date_created' => $term_meta['time_uploaded'][0],
								'source'       => $thread_type,
								'source_id'    => $new_thread->id,
								'ticket_id'    => $ticket->id,
							)
						);
						$new[] = $wpdb->insert_id;
					}
					// update attachments to db.
					$wpdb->update(
						$wpdb->prefix . 'psmsc_threads',
						array(
							'attachments' => implode( '|', $new ),
							'body'        => self::import_uploaded_thread_images( $new_thread->body, $new_thread, $ticket ),
						),
						array( 'id' => $new_thread->id )
					);

				} elseif ( $thread_type == 'log' ) {

					$wpdb->insert(
						$wpdb->prefix . 'psmsc_threads',
						array(
							'ticket'       => $ticket->id,
							'customer'     => 0,
							'type'         => 'log',
							'body'         => $thread->post_content,
							'date_created' => $thread->post_date,
							'date_updated' => $thread->post_date,
						)
					);

				} else {

					// Add ticket feedback (satisfaction survey).
					if ( $installed_addons[15]['is_installed'] && $thread_type == 'feedback' ) {
						$ticket_data['sf_feedback'] = $thread->post_content;
						$ticket_data['sf_date'] = $thread->post_date;
					}
				}
			}

			// date_closed was not available in v1 so date_updated is considered as date closed for closed tickets.
			if ( $ticket->status == $gs['close-ticket-status'] || in_array( $ticket->status, $tl_advanced['closed-ticket-statuses'] ) ) {
				$ticket_data['date_closed'] = $ticket->date_updated;
			}

			// last reply by and last reply on.
			if ( $last_thread ) {
				$ticket_data['last_reply_by'] = $last_thread->customer;
				$ticket_data['last_reply_on'] = $last_thread->date_created;
			}

			// report add-on.
			if ( $installed_addons[6]['is_installed'] ) {
				$ticket_data['frd'] = $frd;
				if ( $count ) {
					$ticket_data['ard'] = ceil( $delay / $count );
				}
				$ticket_data['cg'] = $cg;
				if ( $ticket->status == $gs['close-ticket-status'] || in_array( $ticket->status, $tl_advanced['closed-ticket-statuses'] ) ) {
					$ticket_data['cd'] = ceil( abs( ( new DateTime( $ticket->date_created ) )->getTimestamp() - ( new DateTime( $ticket->date_closed ) )->getTimestamp() ) / 60 );
				}
			}

			// insert sf_date if only rating is available.
			if ( $ticket->rating && ! isset( $ticket_data['sf_date'] ) ) {
				$date_closed = isset( $ticket->date_closed ) ? $ticket->date_closed : '';
				$date_closed = ! $date_closed && isset( $ticket_data['date_closed'] ) ? $ticket_data['date_closed'] : $date_closed;
				if ( $date_closed ) {
					$ticket_data['sf_date'] = $date_closed;
				}
			}

			// update ticket data.
			$wpdb->update(
				$wpdb->prefix . 'psmsc_tickets',
				$ticket_data,
				array( 'id' => $ticket->id )
			);
		}

		/**
		 * Import uploaded thread images
		 *
		 * @param string   $thread_content - thread html.
		 * @param stdClass $thread - thread object.
		 * @param stdClass $ticket - ticket object.
		 * @return string
		 */
		public static function import_uploaded_thread_images( $thread_content, $thread, $ticket ) {

			global $wpdb;
			$upload_dir = wp_upload_dir();
			$regex = 'src="' . preg_quote( $upload_dir['baseurl'], '/' ) . '\/wpsc\/(.*?)"';
			preg_match_all( '/' . $regex . '/', $thread_content, $matches );
			if ( $matches[1] ) {
				$count = count( $matches[1] );
				for ( $i = 0; $i < $count; $i++ ) {

					$file_path = $upload_dir['basedir'] . '/wpsc/' . $matches[1][ $i ];
					if ( ! file_exists( $file_path ) ) {
						continue;
					}

					$file_path = str_replace( $upload_dir['basedir'], '', $file_path );
					$file_name = basename( $file_path );
					$file_url = $upload_dir['baseurl'] . '/wpsc/' . $matches[1][ $i ];

					$wpdb->insert(
						$wpdb->prefix . 'psmsc_attachments',
						array(
							'name'         => $file_name,
							'file_path'    => $file_path,
							'is_image'     => 1,
							'is_active'    => 1,
							'date_created' => ( new DateTime() )->format( 'Y-m-d H:i:s' ),
							'source'       => 'img_editor',
							'source_id'    => $thread->id,
							'ticket_id'    => $ticket->id,
						)
					);

					$thread_content = str_replace(
						$file_url,
						home_url( '/' ) . '?wpsc_attachment=' . $wpdb->insert_id,
						$thread_content
					);
				}
			}
			return $thread_content;
		}
	}

endif;

WPSC_Upgrade_DB_V1::init();

require_once __DIR__ . '/functions.php';
