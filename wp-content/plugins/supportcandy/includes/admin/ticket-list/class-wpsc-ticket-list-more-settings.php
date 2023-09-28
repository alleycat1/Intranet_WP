<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_Ticket_List_More_Settings' ) ) :

	final class WPSC_Ticket_List_More_Settings {

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

			// Agent ticket list settings.
			add_action( 'wp_ajax_wpsc_get_tl_more_settigns', array( __CLASS__, 'get_tl_more_settigns' ) );

			// Agent view settings.
			add_action( 'wp_ajax_wpsc_tl_ms_get_agent_view', array( __CLASS__, 'tl_ms_get_agent_view' ) );
			add_action( 'wp_ajax_wpsc_tl_ms_set_agent_view', array( __CLASS__, 'tl_ms_set_agent_view' ) );
			add_action( 'wp_ajax_wpsc_tl_ms_reset_agent_view', array( __CLASS__, 'tl_ms_reset_agent_view' ) );

			// Customer view settings.
			add_action( 'wp_ajax_wpsc_tl_ms_get_customer_view', array( __CLASS__, 'tl_ms_get_customer_view' ) );
			add_action( 'wp_ajax_wpsc_tl_ms_set_customer_view', array( __CLASS__, 'tl_ms_set_customer_view' ) );
			add_action( 'wp_ajax_wpsc_tl_ms_reset_customer_view', array( __CLASS__, 'tl_ms_reset_customer_view' ) );

			// Advanced settings.
			add_action( 'wp_ajax_wpsc_tl_ms_get_advanced', array( __CLASS__, 'tl_ms_get_advanced' ) );
			add_action( 'wp_ajax_wpsc_tl_ms_set_advanced', array( __CLASS__, 'tl_ms_set_advanced' ) );
			add_action( 'wp_ajax_wpsc_tl_ms_reset_advanced', array( __CLASS__, 'tl_ms_reset_advanced' ) );
		}

		/**
		 * Get default agent view
		 *
		 * @return void
		 */
		public static function tl_ms_default_agent() {

			$view = apply_filters(
				'wpsc_tl_ms_agent_view',
				array(
					'default-sort-by'            => 'date_updated',
					'default-sort-order'         => 'DESC',
					'number-of-tickets'          => 20,
					'unresolved-ticket-statuses' => array( 1, 2, 3 ),
					'default-filter'             => 'all',
					'ticket-reply-redirect'      => 'no-redirect',
				)
			);
			update_option( 'wpsc-tl-ms-agent-view', $view );
		}

		/**
		 * Get default customer view
		 *
		 * @return void
		 */
		public static function tl_ms_default_customer() {

			$view = apply_filters(
				'wpsc_tl_ms_customer_view',
				array(
					'default-sort-by'            => 'date_updated',
					'default-sort-order'         => 'DESC',
					'number-of-tickets'          => 20,
					'unresolved-ticket-statuses' => array( 1, 2, 3 ),
					'default-filter'             => 'all',
					'ticket-reply-redirect'      => 'no-redirect',
				)
			);
			update_option( 'wpsc-tl-ms-customer-view', $view );
		}

		/**
		 * Get default advanced settings
		 *
		 * @return void
		 */
		public static function tl_ms_default_advanced() {

			$advanced_settings = apply_filters(
				'wpsc_tl_ms_advanced',
				array(
					'closed-ticket-statuses'   => array( 4 ),
					'auto-refresh-list-status' => 0,
				)
			);
			update_option( 'wpsc-tl-ms-advanced', $advanced_settings );
		}

		/**
		 * Load tabs for this section
		 */
		public static function load_tabs() {

			self::$tabs        = apply_filters(
				'wpsc_agent_ticket_list_tabs',
				array(
					'agent-view'    => array(
						'slug'     => 'agent_view',
						'label'    => esc_attr__( 'Agent view', 'supportcandy' ),
						'callback' => 'wpsc_tl_ms_get_agent_view',
					),
					'customer-view' => array(
						'slug'     => 'customer_view',
						'label'    => esc_attr__( 'Customer view', 'supportcandy' ),
						'callback' => 'wpsc_tl_ms_get_customer_view',
					),
					'advanced'      => array(
						'slug'     => 'advanced_view',
						'label'    => esc_attr__( 'Advanced', 'supportcandy' ),
						'callback' => 'wpsc_tl_ms_get_advanced',
					),
				)
			);
			self::$current_tab = isset( $_REQUEST['tab'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['tab'] ) ) : 'agent-view'; // phpcs:ignore
		}

		/**
		 * Add localizations to local JS
		 *
		 * @param array $localizations - localization list.
		 * @return array
		 */
		public static function localizations( $localizations ) {

			if ( ! ( WPSC_Ticket_List_Settings::$is_current_page && WPSC_Ticket_List_Settings::$current_section === 'more-settings' ) ) {
				return $localizations;
			}

			// Current section.
			$localizations['current_tab'] = self::$current_tab;

			return $localizations;
		}

		/**
		 * Customer ticket list settings
		 *
		 * @return void
		 */
		public static function get_tl_more_settigns() {

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

		/**
		 * Get agent view settings
		 */
		public static function tl_ms_get_agent_view() {

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$statuses        = WPSC_Status::find( array( 'items_per_page' => 0 ) )['results'];
			$custom_fields   = WPSC_Custom_Field::$custom_fields;
			$list_tems       = get_option( 'wpsc-atl-list-items' );
			$agent_view      = get_option( 'wpsc-tl-ms-agent-view', array() );
			$default_filters = get_option( 'wpsc-atl-default-filters' );
			$list_tags       = apply_filters(
				'wpsc_list_item_tags',
				array(
					'status'   => esc_attr__( 'Status', 'supportcandy' ),
					'priority' => esc_attr__( 'Priority', 'supportcandy' ),
				)
			);
			?>

			<form action="#" onsubmit="return false;" class="wpsc-frm-tl-ms-agent-view">
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Default sort by', 'supportcandy' ); ?></label>
					</div>
					<select id="wpsc-agent-default-sort-by" name="default-sort-by">
						<?php
						foreach ( $list_tems as $slug ) :
							$cf = WPSC_Custom_Field::get_cf_by_slug( $slug );
							if ( ! $cf ) {
								continue;
							}
							if ( $cf->type::$is_sort ) :
								?>
								<option <?php selected( $cf->slug, $agent_view['default-sort-by'] ); ?>value="<?php echo esc_attr( $cf->slug ); ?>"><?php echo esc_attr( $cf->name ); ?></option>
								<?php
							endif;
						endforeach;
						?>
					</select>
				</div>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Default sort order', 'supportcandy' ); ?></label>
					</div>
					<select id="wpsc-agent-default-sort-order" name="default-sort-order">
						<option <?php selected( $agent_view['default-sort-order'], 'DESC' ); ?> value="DESC"><?php esc_attr_e( 'DESC', 'supportcandy' ); ?></option>
						<option <?php selected( $agent_view['default-sort-order'], 'ASC' ); ?> value="ASC"><?php esc_attr_e( 'ASC', 'supportcandy' ); ?></option>
					</select>
				</div>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Number of tickets', 'supportcandy' ); ?></label>
					</div>
					<input type="number" id="wpsc-agent-number-of-tickets" name="number-of-tickets" value="<?php echo esc_attr( $agent_view['number-of-tickets'] ); ?>" />
				</div>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Unresolved statuses', 'supportcandy' ); ?></label>
					</div>
					<select id="wpsc-agent-unresolved-ticket-statuses" multiple="multiple" name="unresolved-ticket-statuses[]">
						<?php
						foreach ( $statuses as $status ) :
							$selected = in_array( $status->id, $agent_view['unresolved-ticket-statuses'] ) ? 'selected="selected"' : '';
							?>
							<option <?php echo esc_attr( $selected ); ?> value="<?php echo esc_attr( $status->id ); ?>"><?php echo esc_attr( $status->name ); ?></option>
							<?php
						endforeach;
						?>
					</select>
					<script>
						jQuery('#wpsc-agent-unresolved-ticket-statuses').selectWoo({
							allowClear: false,
							placeholder: ""
						});
					</script>
				</div>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Default filter', 'supportcandy' ); ?></label>
					</div>
					<select name="default-filter">
						<optgroup label="<?php esc_attr_e( 'Default filters', 'supportcandy' ); ?>">
							<?php
							foreach ( $default_filters as $index => $filter ) :
								$selected = $agent_view['default-filter'] == $index || $agent_view['default-filter'] == 'default-' . $index ? 'selected="selected"' : '';
								$index    = is_numeric( $index ) ? 'default-' . $index : $index;
								?>
								<option <?php echo esc_attr( $selected ); ?> value="<?php echo esc_attr( $index ); ?>">
									<?php
									$filter_label = $filter['label'] ? WPSC_Translations::get( 'wpsc-atl-' . $index, stripslashes( $filter['label'] ) ) : stripslashes( $filter['label'] );
									echo esc_attr( $filter_label )
									?>
								</option>
								<?php
							endforeach;
							?>
						</optgroup>
					</select>  
				</div>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Ticket reply redirect', 'supportcandy' ); ?></label>
					</div>
					<select id="wpsc-ticket-reply-redirect" name="ticket-reply-redirect">
						<option <?php selected( $agent_view['ticket-reply-redirect'], 'ticket-list' ); ?> value="ticket-list"><?php esc_attr_e( 'Ticket list', 'supportcandy' ); ?></option>
						<option <?php selected( $agent_view['ticket-reply-redirect'], 'no-redirect' ); ?> value="no-redirect"><?php esc_attr_e( 'No redirect', 'supportcandy' ); ?></option>
					</select>
				</div>
				<?php do_action( 'wpsc_tl_ms_agent_view' ); ?>
				<input type="hidden" name="action" value="wpsc_tl_ms_set_agent_view">
				<input type="hidden" name="_ajax_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wpsc_tl_ms_set_agent_view' ) ); ?>">
			</form>

			<div class="setting-footer-actions">
				<button 
					class="wpsc-button normal primary margin-right"
					onclick="wpsc_tl_ms_set_agent_view(this);">
					<?php esc_attr_e( 'Submit', 'supportcandy' ); ?></button>
				<button 
					class="wpsc-button normal secondary"
					onclick="wpsc_tl_ms_reset_agent_view(this, '<?php echo esc_attr( wp_create_nonce( 'wpsc_tl_ms_reset_agent_view' ) ); ?>');">
					<?php esc_attr_e( 'Reset default', 'supportcandy' ); ?></button>
			</div>
			<?php
			do_action( 'wpsc_tl_ms_get_agent_footer' );
			wp_die();
		}

		/**
		 * Save agent view settings
		 *
		 * @return void
		 */
		public static function tl_ms_set_agent_view() {

			if ( check_ajax_referer( 'wpsc_tl_ms_set_agent_view', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			global $wpdb;
			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$av_settings = get_option( 'wpsc-tl-ms-agent-view' );

			$view_settings = apply_filters(
				'wpsc_tl_ms_agent_view',
				array(
					'default-sort-by'            => isset( $_POST['default-sort-by'] ) ? sanitize_text_field( wp_unslash( $_POST['default-sort-by'] ) ) : 'date_created',
					'default-sort-order'         => isset( $_POST['default-sort-order'] ) ? sanitize_text_field( wp_unslash( $_POST['default-sort-order'] ) ) : 'DESC',
					'number-of-tickets'          => isset( $_POST['number-of-tickets'] ) ? intval( $_POST['number-of-tickets'] ) : 20,
					'unresolved-ticket-statuses' => isset( $_POST['unresolved-ticket-statuses'] ) ? array_filter( array_map( 'intval', $_POST['unresolved-ticket-statuses'] ) ) : array( 1, 2, 3 ),
					'default-filter'             => isset( $_POST['default-filter'] ) ? sanitize_text_field( wp_unslash( $_POST['default-filter'] ) ) : 'all',
					'ticket-reply-redirect'      => isset( $_POST['ticket-reply-redirect'] ) ? sanitize_text_field( wp_unslash( $_POST['ticket-reply-redirect'] ) ) : 'no-redirect',
				)
			);
			update_option( 'wpsc-tl-ms-agent-view', $view_settings );

			// schedule reset agent counts.
			if (
				array_diff( $av_settings['unresolved-ticket-statuses'], $view_settings['unresolved-ticket-statuses'] ) ||
				array_diff( $view_settings['unresolved-ticket-statuses'], $av_settings['unresolved-ticket-statuses'] )
			) {
				$wpdb->update(
					$wpdb->prefix . 'psmsc_agents',
					array(
						'workload'         => null,
						'unresolved_count' => null,
					),
					array(
						'is_agentgroup' => 0,
						'is_active'     => 1,
					),
				);
				update_option( 'wpsc-unresolved-reset-status', 0 );
			}
			wp_die();
		}

		/**
		 * Reset settings to default
		 *
		 * @return void
		 */
		public static function tl_ms_reset_agent_view() {

			if ( check_ajax_referer( 'wpsc_tl_ms_reset_agent_view', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}
			self::tl_ms_default_agent();
			wp_die();
		}

		/**
		 * Get customer view settings
		 */
		public static function tl_ms_get_customer_view() {

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$statuses        = WPSC_Status::find( array( 'items_per_page' => 0 ) )['results'];
			$custom_fields   = WPSC_Custom_Field::$custom_fields;
			$list_tems       = get_option( 'wpsc-ctl-list-items' );
			$customer_view   = get_option( 'wpsc-tl-ms-customer-view', array() );
			$default_filters = get_option( 'wpsc-ctl-default-filters', array() );
			?>

			<form action="#" onsubmit="return false;" class="wpsc-frm-tl-ms-customer-view">
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Default sort by', 'supportcandy' ); ?></label>
					</div>
					<select id="wpsc-customer-default-sort-by" name="default-sort-by">
						<?php
						foreach ( $list_tems as $slug ) :
							$cf = WPSC_Custom_Field::get_cf_by_slug( $slug );
							if ( ! $cf ) {
								continue;
							}
							if ( $cf->type::$is_sort ) :
								?>
								<option <?php selected( $cf->slug, $customer_view['default-sort-by'] ); ?>value="<?php echo esc_attr( $cf->slug ); ?>"><?php echo esc_attr( $cf->name ); ?></option>
								<?php
							endif;
						endforeach;
						?>
					</select>
				</div>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Default sort order', 'supportcandy' ); ?></label>
					</div>
					<select id="wpsc-customer-default-sort-order" name="default-sort-order">
						<option <?php selected( $customer_view['default-sort-order'], 'DESC' ); ?> value="DESC"><?php esc_attr_e( 'DESC', 'supportcandy' ); ?></option>
						<option <?php selected( $customer_view['default-sort-order'], 'ASC' ); ?> value="ASC"><?php esc_attr_e( 'ASC', 'supportcandy' ); ?></option>
					</select>
				</div>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Number of tickets', 'supportcandy' ); ?></label>
					</div>
					<input type="number" id="wpsc-customer-number-of-tickets" name="number-of-tickets" value="<?php echo esc_attr( $customer_view['number-of-tickets'] ); ?>" />
				</div>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Unresolved statuses', 'supportcandy' ); ?></label>
					</div>
					<select id="wpsc-customer-unresolved-ticket-statuses" multiple="multiple" name="unresolved-ticket-statuses[]">
						<?php
						foreach ( $statuses as $status ) :
							$selected = in_array( $status->id, $customer_view['unresolved-ticket-statuses'] ) ? 'selected="selected"' : '';
							?>
							<option <?php echo esc_attr( $selected ); ?> value="<?php echo esc_attr( $status->id ); ?>"><?php echo esc_attr( $status->name ); ?></option>
							<?php
						endforeach;
						?>
					</select>
					<script>
						jQuery('#wpsc-customer-unresolved-ticket-statuses').selectWoo({
							allowClear: false,
							placeholder: ""
						});
					</script>
				</div>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Default filter', 'supportcandy' ); ?></label>
					</div>
					<select name="default-filter">
						<optgroup label="<?php esc_attr_e( 'Default filters', 'supportcandy' ); ?>">
							<?php
							foreach ( $default_filters as $index => $filter ) :
								$selected = $customer_view['default-filter'] == $index || $customer_view['default-filter'] == 'default-' . $index ? 'selected="selected"' : '';
								$index    = is_numeric( $index ) ? 'default-' . $index : $index;
								?>
								<option <?php echo esc_attr( $selected ); ?> value="<?php echo esc_attr( $index ); ?>">
									<?php
										$filter_label = $filter['label'] ? WPSC_Translations::get( 'wpsc-ctl-' . $index, stripslashes( $filter['label'] ) ) : stripslashes( $filter['label'] );
										echo esc_attr( $filter_label )
									?>
								</option>
								<?php
							endforeach;
							?>
						</optgroup>
					</select>  
				</div>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Ticket reply redirect', 'supportcandy' ); ?></label>
					</div>
					<select id="wpsc-ticket-reply-redirect" name="ticket-reply-redirect">
						<option <?php selected( $customer_view['ticket-reply-redirect'], 'ticket-list' ); ?> value="ticket-list"><?php esc_attr_e( 'Ticket list', 'supportcandy' ); ?></option>
						<option <?php selected( $customer_view['ticket-reply-redirect'], 'no-redirect' ); ?> value="no-redirect"><?php esc_attr_e( 'No redirect', 'supportcandy' ); ?></option>
					</select>
				</div>
				<?php do_action( 'wpsc_tl_ms_customer_view' ); ?>
				<input type="hidden" name="action" value="wpsc_tl_ms_set_customer_view">
				<input type="hidden" name="_ajax_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wpsc_tl_ms_set_customer_view' ) ); ?>">
			</form>

			<div class="setting-footer-actions">
				<button 
					class="wpsc-button normal primary margin-right"
					onclick="wpsc_tl_ms_set_customer_view(this);">
					<?php esc_attr_e( 'Submit', 'supportcandy' ); ?></button>
				<button 
					class="wpsc-button normal secondary"
					onclick="wpsc_tl_ms_reset_customer_view (this, '<?php echo esc_attr( wp_create_nonce( 'wpsc_tl_ms_reset_customer_view' ) ); ?>');">
					<?php esc_attr_e( 'Reset default', 'supportcandy' ); ?></button>
			</div>
			<?php
			do_action( 'wpsc_tl_ms_get_customer_footer' );
			wp_die();
		}

		/**
		 * Save customer view settings
		 *
		 * @return void
		 */
		public static function tl_ms_set_customer_view() {

			if ( check_ajax_referer( 'wpsc_tl_ms_set_customer_view', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$view_settings = apply_filters(
				'wpsc_tl_ms_customer_view',
				array(
					'default-sort-by'            => isset( $_POST['default-sort-by'] ) ? sanitize_text_field( wp_unslash( $_POST['default-sort-by'] ) ) : 'date_created',
					'default-sort-order'         => isset( $_POST['default-sort-order'] ) ? sanitize_text_field( wp_unslash( $_POST['default-sort-order'] ) ) : 'DESC',
					'number-of-tickets'          => isset( $_POST['number-of-tickets'] ) ? intval( $_POST['number-of-tickets'] ) : 20,
					'unresolved-ticket-statuses' => isset( $_POST['unresolved-ticket-statuses'] ) ? array_filter( array_map( 'intval', $_POST['unresolved-ticket-statuses'] ) ) : array(),
					'default-filter'             => isset( $_POST['default-filter'] ) ? sanitize_text_field( wp_unslash( $_POST['default-filter'] ) ) : 'all',
					'ticket-reply-redirect'      => isset( $_POST['ticket-reply-redirect'] ) ? sanitize_text_field( wp_unslash( $_POST['ticket-reply-redirect'] ) ) : 'no-redirect',
				)
			);
			update_option( 'wpsc-tl-ms-customer-view', $view_settings );
			wp_die();
		}

		/**
		 * Reset settings to default
		 *
		 * @return void
		 */
		public static function tl_ms_reset_customer_view() {

			if ( check_ajax_referer( 'wpsc_tl_ms_reset_customer_view', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}
			self::tl_ms_default_customer();
			wp_die();
		}

		/**
		 * Get advanced settings
		 *
		 * @return void
		 */
		public static function tl_ms_get_advanced() {

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}
			$statuses          = WPSC_Status::find( array( 'items_per_page' => 0 ) )['results'];
			$advanced_settings = get_option( 'wpsc-tl-ms-advanced', array() );
			?>

			<form action="#" onsubmit="return false;" class="wpsc-frm-advanced-settings">
				<div class="wpsc-dock-container">
					<?php
					printf(
						/* translators: Click here to see the documentation */
						esc_attr__( '%s to see the documentation!', 'supportcandy' ),
						'<a href="https://supportcandy.net/docs/ticket-list-advance-settings/" target="_blank">' . esc_attr__( 'Click here', 'supportcandy' ) . '</a>'
					);
					?>
				</div>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Closed statuses', 'supportcandy' ); ?></label>
					</div>
					<select id="wpsc-closed-ticket-statuses" multiple="multiple" name="closed-ticket-statuses[]">
						<?php
						foreach ( $statuses as $status ) :
							$selected = in_array( $status->id, $advanced_settings['closed-ticket-statuses'] ) ? 'selected="selected"' : '';
							?>
							<option <?php echo esc_attr( $selected ); ?> value="<?php echo esc_attr( $status->id ); ?>"><?php echo esc_attr( $status->name ); ?></option>
							<?php
						endforeach;
						?>
					</select>
					<script>
						jQuery('#wpsc-closed-ticket-statuses').selectWoo({
							allowClear: false,
							placeholder: ""
						});
					</script>
				</div>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Auto refresh default status', 'supportcandy' ); ?></label>
					</div>
					<select name="wpsc-auto-refresh-list-status">
						<option <?php selected( $advanced_settings['auto-refresh-list-status'], '1' ); ?> value="1"><?php esc_attr_e( 'On', 'supportcandy' ); ?></option>
						<option <?php selected( $advanced_settings['auto-refresh-list-status'], '0' ); ?> value="0"><?php esc_attr_e( 'Off', 'supportcandy' ); ?></option>
					</select>
				</div>
				<?php do_action( 'wpsc_tl_ms_advanced_settings' ); ?>
				<input type="hidden" name="action" value="wpsc_tl_ms_set_advanced">
				<input type="hidden" name="_ajax_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wpsc_tl_ms_set_advanced' ) ); ?>">
			</form>

			<div class="setting-footer-actions">
				<button 
					class="wpsc-button normal primary margin-right"
					onclick="wpsc_tl_ms_set_advanced(this);">
					<?php esc_attr_e( 'Submit', 'supportcandy' ); ?></button>
				<button 
					class="wpsc-button normal secondary"
					onclick="wpsc_tl_ms_reset_advanced(this, '<?php echo esc_attr( wp_create_nonce( 'wpsc_tl_ms_reset_advanced' ) ); ?>');">
					<?php esc_attr_e( 'Reset default', 'supportcandy' ); ?></button>
			</div>
			<?php
			do_action( 'wpsc_get_add_more_settings_advanced' );
			wp_die();
		}

		/**
		 * Set advanced settings
		 *
		 * @return void
		 */
		public static function tl_ms_set_advanced() {

			if ( check_ajax_referer( 'wpsc_tl_ms_set_advanced', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$advanced_settings = apply_filters(
				'wpsc_tl_ms_advanced',
				array(
					'closed-ticket-statuses'   => isset( $_POST['closed-ticket-statuses'] ) ? array_filter( array_map( 'intval', $_POST['closed-ticket-statuses'] ) ) : array( 4 ),
					'auto-refresh-list-status' => isset( $_POST['wpsc-auto-refresh-list-status'] ) ? intval( $_POST['wpsc-auto-refresh-list-status'] ) : 0,
				)
			);
			update_option( 'wpsc-tl-ms-advanced', $advanced_settings );
			wp_die();
		}

		/**
		 * Reset settings to default
		 *
		 * @return void
		 */
		public static function tl_ms_reset_advanced() {

			if ( check_ajax_referer( 'wpsc_tl_ms_reset_advanced', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}
			self::tl_ms_default_advanced();
			wp_die();
		}
	}
endif;

WPSC_Ticket_List_More_Settings::init();
