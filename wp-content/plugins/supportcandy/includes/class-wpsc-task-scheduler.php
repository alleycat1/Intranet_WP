<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

/**
 * Garbage collector class for supportcandy
 */
if ( ! class_exists( 'WPSC_Task_Scheduler' ) ) :

	final class WPSC_Task_Scheduler {

		/**
		 * Initialize this class
		 *
		 * @return void
		 */
		public static function init() {

			// schedule cron jobs.
			add_action( 'init', array( __CLASS__, 'schedule' ) );
			add_action( 'wpsc_execute_scheduled_task', array( __CLASS__, 'execute_scheduled_task' ) );

			// manual runner.
			add_action( 'admin_notices', array( __CLASS__, 'show_manual_notice' ) );
			add_action( 'wp_ajax_wpsc_initialize_scheduled_task', array( __CLASS__, 'initialize_scheduled_task' ) );
			add_action( 'wp_ajax_wpsc_run_manual_task', array( __CLASS__, 'run_manual_task' ) );
		}

		/**
		 * Schedule tasks. For example, change all ticket records after upgrade, change all customer records, etc.
		 * It will shedule tasks by FIFO method only one task at a time.
		 *
		 * @return void
		 */
		public static function schedule() {

			$task = self::get_scheduled_task();
			if ( false === $task ) {
				return;
			}

			if ( 'yes' === get_transient( 'wpsc_executing_manual_task' ) ) {
				return;
			}

			// schedule first task.
			if ( ! wp_next_scheduled( 'wpsc_execute_scheduled_task' ) ) {
				wp_schedule_single_event( time(), 'wpsc_execute_scheduled_task', array( $task ) );
			}
		}

		/**
		 * Manual runner admin notice
		 *
		 * @return void
		 */
		public static function show_manual_notice() {

			// return if current user does not have administrator previlages.
			$current_user = WPSC_Current_User::$current_user;
			if ( ! ( ! $current_user->is_guest && $current_user->user->has_cap( 'manage_options' ) ) ) {
				return;
			}

			// return if there is no task scheduled or it is not for manual.
			$task = self::get_scheduled_task();
			if ( false === $task || 0 == $task->is_manual ) {
				return;
			}

			// do not show notice on scheduler page.
			if ( isset( $_REQUEST['page'] ) && $_REQUEST['page'] == 'wpsc-task-manager' ) { // phpcs:ignore
				return;
			}

			// show notice.
			$nonce = wp_create_nonce( 'wpsc_manual_task_manager_' . $task->id );
			?>
			<div class="supportcandy scheduled-task notice notice-warning">
				<p><?php echo esc_attr( $task->warning_text ); ?></p>
				<p>
					<a href="<?php echo esc_url_raw( admin_url( 'admin.php?page=wpsc-task-manager&task=' . $task->id . '&_wpnonce=' . $nonce ) ); ?>">
						<?php echo esc_attr( $task->warning_link_text ); ?>
					</a>
				</p>
			</div>
			<?php
		}

		/**
		 * Return scheduled task if any otherwise return false
		 *
		 * @return mixed
		 */
		public static function get_scheduled_task() {

			$tasks = WPSC_Scheduled_Task::find(
				array(
					'items_per_page' => 1,
				)
			);

			return $tasks['total_items'] > 0 ? $tasks['results'][0] : false;
		}

		/**
		 * Execute current task
		 *
		 * @param WPSC_Scheduled_Task $task - task object.
		 * @return void
		 */
		public static function execute_scheduled_task( $task ) {

			if ( ! class_exists( $task->class ) || ! method_exists( $task->class, $task->method ) ) {
				return;
			}

			if ( 'yes' === get_transient( 'wpsc_executing_scheduled_task' ) ) {
				return;
			}

			set_transient( 'wpsc_executing_scheduled_task', 'yes', MINUTE_IN_SECONDS * 10 );

			call_user_func( array( $task->class, $task->method ), $task );

			delete_transient( 'wpsc_executing_scheduled_task' );
		}

		/**
		 * Perform manual scheduled task
		 *
		 * @return void
		 */
		public static function perform_manual_scheduler() {

			$task_id = isset( $_REQUEST['task'] ) ? intval( $_REQUEST['task'] ) : 0;
			check_admin_referer( 'wpsc_manual_task_manager_' . $task_id );

			?>
			<div class="wrap">
				<hr class="wp-header-end">
				<div id="wpsc-container">
					<div class="wpsc-settings-page">
						<div class="wpsc-setting-body">
							<div class="wpsc-setting-section-body">
								<div class="wpsc-pg-container">
									<i class="wpsc-pg-title"><?php esc_attr_e( 'Initializing...', 'supportcandy' ); ?></i>
									<div class="wpsc-pg">
										<div class="wpsc-pg-label">0%</div>
									</div>
								</div>
								<script>
									var progressbar = jQuery( ".wpsc-pg" );
									var progressLabel = jQuery( ".wpsc-pg-label" );
									var progressbarTitle = jQuery( ".wpsc-pg-title" );
									var settingURL = '<?php echo esc_url_raw( admin_url( 'admin.php?page=wpsc-settings&section=general-settings&tab=general' ) ); ?>';
									jQuery(document).ready( async function(){
										progressbar.progressbar({ value: 0 });
										supportcandy.temp.wpsc_initialize_scheduled_task = '<?php echo esc_attr( wp_create_nonce( 'wpsc_initialize_scheduled_task' ) ); ?>';
										wpsc_initialize_task();
									});
									function wpsc_initialize_task() {
										var data = {
											action: 'wpsc_initialize_scheduled_task',
											task_id: <?php echo intval( $task_id ); ?>,
											_ajax_nonce: supportcandy.temp.wpsc_initialize_scheduled_task
										};
										jQuery.post(supportcandy.ajax_url, data, function (response) {
											supportcandy.temp.wpsc_initialize_scheduled_task = response.nonce;
											if ( response.status == 'busy' ) {
												setTimeout(() => {
													wpsc_initialize_task();
												}, 2000);
											} else if ( response.status == 'unavailable' ) {
												window.location.href = settingURL;
											} else {
												supportcandy.temp.wpsc_run_manual_task = response.nonce;
												progressbarTitle.text( response.pbText );
												wpsc_runner( response.pages );
											}
										});
									}
									async function wpsc_runner( pages ) {
										pages = parseInt( pages ) + 1;
										for ( page=1; page<=pages; page++ ) {
											var response = await wpsc_run_manual_task();
											if ( response !== false ) {
												supportcandy.temp.wpsc_run_manual_task = response.nonce;
												let percentage = Math.round((page/pages)*100);
												if ( response.status == 'unavailable' ) {
													let percentage = 100;
													progressLabel.text( percentage + '%' );
													progressbar.progressbar({ value: percentage });
													break;
												} else {
													progressLabel.text( percentage + '%' );
													progressbar.progressbar({ value: percentage });
												}
											} else {
												break;
											}
										}
										setTimeout(() => {
											window.location.href = settingURL;
										}, 1000);
									}
									function wpsc_run_manual_task() {
										var data = {
											action: 'wpsc_run_manual_task',
											task_id: <?php echo intval( $task_id ); ?>,
											_ajax_nonce: supportcandy.temp.wpsc_run_manual_task
										};
										return new Promise( resolve => {
											jQuery.post(supportcandy.ajax_url, data, function (response) {
												resolve( response );
											}).fail(function(){
												resolve(false);
											});
										});
									}
								</script>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php
		}

		/**
		 * Initialize sceduled task callback
		 *
		 * @return void
		 */
		public static function initialize_scheduled_task() {

			if ( check_ajax_referer( 'wpsc_initialize_scheduled_task', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorized request!', 400 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( 'Unauthorized access!', 401 );
			}

			$id = isset( $_POST['task_id'] ) ? intval( $_POST['task_id'] ) : 0;
			if ( ! $id ) {
				wp_send_json_error( 'Incorrect request!', 400 );
			}

			// return if there is no task scheduled or it is not for manual or task does not match.
			$task = self::get_scheduled_task();
			if ( false === $task || 0 == $task->is_manual || $id != $task->id ) {
				wp_send_json(
					array(
						'nonce'  => '',
						'status' => 'unavailable',
					),
					200
				);
			}

			// check whether it is already running by background process.
			if ( 'yes' === get_transient( 'wpsc_executing_scheduled_task' ) ) {
				wp_send_json(
					array(
						'nonce'  => wp_create_nonce( 'wpsc_initialize_scheduled_task' ),
						'status' => 'busy',
					),
					200
				);
			}

			set_transient( 'wpsc_executing_manual_task', 'yes', MINUTE_IN_SECONDS * 10 );

			// get page count if count is 0.
			if ( $task->pages == 0 ) {
				do_action( 'wpsc_execute_scheduled_task', $task );
				$task = new WPSC_Scheduled_Task( $id );
				if ( ! $task->id ) {
					delete_transient( 'wpsc_executing_manual_task' );
					wp_send_json(
						array(
							'nonce'  => '',
							'status' => 'unavailable',
						),
						200
					);
				}
			}

			// return pages.
			wp_send_json(
				array(
					'nonce'  => wp_create_nonce( 'wpsc_run_manual_task' ),
					'status' => 'success',
					'pages'  => $task->pages,
					'pbText' => $task->progressbar_text,
				),
				200
			);
		}

		/**
		 * Run manual task callback
		 *
		 * @return void
		 */
		public static function run_manual_task() {

			if ( check_ajax_referer( 'wpsc_run_manual_task', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorized request!', 400 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( 'Unauthorized access!', 401 );
			}

			$id = isset( $_POST['task_id'] ) ? intval( $_POST['task_id'] ) : 0;
			if ( ! $id ) {
				wp_send_json_error( 'Incorrect request!', 400 );
			}

			// return if there is no task scheduled or it is not for manual or task does not match.
			$task = self::get_scheduled_task();
			if ( false === $task || 0 == $task->is_manual || $id != $task->id ) {
				delete_transient( 'wpsc_executing_manual_task' );
				wp_send_json(
					array(
						'nonce'  => '',
						'status' => 'unavailable',
					),
					200
				);
			}

			// execute task.
			do_action( 'wpsc_execute_scheduled_task', $task );

			// check whether it is finished.
			$task = new WPSC_Scheduled_Task( $id );
			if ( ! $task->id ) {
				delete_transient( 'wpsc_executing_manual_task' );
				wp_send_json(
					array(
						'nonce'  => '',
						'status' => 'unavailable',
					),
					200
				);
			}

			// return success.
			wp_send_json(
				array(
					'nonce'  => wp_create_nonce( 'wpsc_run_manual_task' ),
					'status' => 'success',
				),
				200
			);
		}
	}
endif;

WPSC_Task_Scheduler::init();
