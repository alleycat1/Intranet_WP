<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_Ticket_List' ) ) :

	final class WPSC_Ticket_List {

		/**
		 * All default and saved filters for logged-in user
		 *
		 * @var array
		 */
		private static $filters;

		/**
		 * Cookie filters to be sent to JS with tickets
		 *
		 * @var array
		 */
		private static $cookie_filters;

		/**
		 * Ticket found for the filter
		 *
		 * @var array
		 */
		private static $tickets = array();

		/**
		 * Set if there is next page is available
		 *
		 * @var integer
		 */
		private static $has_next_page = false;

		/**
		 * Total number of pages
		 *
		 * @var integer
		 */
		private static $total_pages = 0;

		/**
		 * Total number of tickets found for $filters
		 *
		 * @var integer
		 */
		private static $total_items = 0;

		/**
		 * Set whether tickets to be queried is active or deleted
		 *
		 * @var integer
		 */
		public static $is_active = 1;

		/**
		 * Default filters based on logged-in user
		 *
		 * @var array
		 */
		public static $default_filters;

		/**
		 * Saved filters of logged-in user
		 *
		 * @var array
		 */
		private static $saved_filters;

		/**
		 * More settings for agent/customer view based on logged-in user
		 *
		 * @var array
		 */
		public static $more_settings;

		/**
		 * Flag to check whether current filter is numeric default filter or not
		 *
		 * @var integer
		 */
		private static $default_flag;

		/**
		 * Flag to check whether current filter is saved filter or not
		 *
		 * @var integer
		 */
		private static $saved_flag;

		/**
		 * Numeric type flag ID for current filter
		 *
		 * @var integer
		 */
		private static $filter_id = 0;

		/**
		 * Ticket list bulk actions
		 *
		 * @var array
		 */
		private static $bulk_actions = array();

		/**
		 * Initialize this class
		 *
		 * @return void
		 */
		public static function init() {

			// Get ticket list ajax request.
			add_action( 'wp_ajax_wpsc_get_ticket_list', array( __CLASS__, 'layout' ) );
			add_action( 'wp_ajax_nopriv_wpsc_get_ticket_list', array( __CLASS__, 'layout' ) );

			// Conditions allowed for custom ticket filters.
			add_filter( 'wpsc_custom_filter_conditions', array( __CLASS__, 'custom_filter_conditions' ) );

			// Get tickets ajax request.
			add_action( 'wp_ajax_wpsc_get_tickets', array( __CLASS__, 'get_tickets' ) );
			add_action( 'wp_ajax_nopriv_wpsc_get_tickets', array( __CLASS__, 'get_tickets' ) );
			add_action( 'wp_ajax_wpsc_get_tl_custom_filter', array( __CLASS__, 'get_tl_custom_filter_ui' ) );
			add_action( 'wp_ajax_nopriv_wpsc_get_tl_custom_filter', array( __CLASS__, 'get_tl_custom_filter_ui' ) );

			// Saved filters.
			add_action( 'wp_ajax_wpsc_tl_get_add_saved_filter', array( __CLASS__, 'get_add_saved_filter_ui' ) );
			add_action( 'wp_ajax_nopriv_wpsc_tl_get_add_saved_filter', array( __CLASS__, 'get_add_saved_filter_ui' ) );
			add_action( 'wp_ajax_wpsc_tl_set_add_saved_filter', array( __CLASS__, 'set_add_saved_filter' ) );
			add_action( 'wp_ajax_nopriv_wpsc_tl_set_add_saved_filter', array( __CLASS__, 'set_add_saved_filter' ) );
			add_action( 'wp_ajax_wpsc_tl_get_edit_saved_filter', array( __CLASS__, 'get_edit_saved_filter_ui' ) );
			add_action( 'wp_ajax_nopriv_wpsc_tl_get_edit_saved_filter', array( __CLASS__, 'get_edit_saved_filter_ui' ) );
			add_action( 'wp_ajax_wpsc_tl_set_edit_saved_filter', array( __CLASS__, 'set_edit_saved_filter' ) );
			add_action( 'wp_ajax_nopriv_wpsc_tl_set_edit_saved_filter', array( __CLASS__, 'set_edit_saved_filter' ) );
			add_action( 'wp_ajax_wpsc_tl_delete_saved_filter', array( __CLASS__, 'delete_saved_filter' ) );
			add_action( 'wp_ajax_nopriv_wpsc_tl_delete_saved_filter', array( __CLASS__, 'delete_saved_filter' ) );

			// Bulk actions.
			add_action( 'wp_ajax_wpsc_bulk_change_status', array( __CLASS__, 'bulk_change_status' ) );
			add_action( 'wp_ajax_nopriv_wpsc_bulk_change_status', array( __CLASS__, 'bulk_change_status' ) );
			add_action( 'wp_ajax_wpsc_set_bulk_change_status', array( __CLASS__, 'set_bulk_change_status' ) );
			add_action( 'wp_ajax_nopriv_wpsc_set_bulk_change_status', array( __CLASS__, 'set_bulk_change_status' ) );
			add_action( 'wp_ajax_wpsc_bulk_assign_agents', array( __CLASS__, 'bulk_assign_agents' ) );
			add_action( 'wp_ajax_nopriv_wpsc_bulk_assign_agents', array( __CLASS__, 'bulk_assign_agents' ) );
			add_action( 'wp_ajax_wpsc_set_bulk_assign_agent', array( __CLASS__, 'set_bulk_assign_agent' ) );
			add_action( 'wp_ajax_nopriv_wpsc_set_bulk_assign_agent', array( __CLASS__, 'set_bulk_assign_agent' ) );
			add_action( 'wp_ajax_wpsc_bulk_delete_tickets', array( __CLASS__, 'bulk_delete_tickets' ) );
			add_action( 'wp_ajax_nopriv_wpsc_bulk_delete_tickets', array( __CLASS__, 'bulk_delete_tickets' ) );
			add_action( 'wp_ajax_wpsc_bulk_restore_tickets', array( __CLASS__, 'bulk_restore_tickets' ) );
			add_action( 'wp_ajax_nopriv_wpsc_bulk_restore_tickets', array( __CLASS__, 'bulk_restore_tickets' ) );
			add_action( 'wp_ajax_wpsc_bulk_delete_tickets_permanently', array( __CLASS__, 'bulk_delete_tickets_permanently' ) );
			add_action( 'wp_ajax_nopriv_wpsc_bulk_delete_tickets_permanently', array( __CLASS__, 'bulk_delete_tickets_permanently' ) );

			// agent autocomplete assign cap access check.
			add_action( 'wp_ajax_wpsc_agent_autocomplete_bulk_assign', array( __CLASS__, 'agent_autocomplete_bulk_assign' ) );
			add_action( 'wp_ajax_nopriv_wpsc_agent_autocomplete_bulk_assign', array( __CLASS__, 'agent_autocomplete_bulk_assign' ) );

			// get updated nonce.
			add_action( 'wp_ajax_wpsc_get_nonce', array( __CLASS__, 'get_nonce' ) );

			// filter ticket list by tags.
			add_action( 'init', array( __CLASS__, 'apply_tag_custom_filter' ), 100 );
		}

		/**
		 * Ajax callback for Ticket list section
		 *
		 * @return void
		 */
		public static function layout() {

			self::load_tickets();
			self::set_bulk_actions();
			self::get_filters();
			self::get_ticket_list();
			self::print_tl_snippets();
			wp_die();
		}

		/**
		 * Ajax callback for get_tickets on ticket list with filters and page
		 *
		 * @return void
		 */
		public static function get_tickets() {

			if ( check_ajax_referer( 'general', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			self::load_tickets();
			self::set_bulk_actions();
			$filters = self::$cookie_filters;
			$filters['filters'] = isset( $filters['filters'] ) ? $filters['filters'] : '[]';

			$response = array(
				'tickets'        => self::print_tickets(),
				'filter_actions' => self::get_filter_actions(),
				'bulk_actions'   => self::get_bulk_actions(),
				'pagination_str' => self::get_pagination_str(),
				'pagination'     => array(
					'current_page'  => self::$filters['page_no'],
					'has_next_page' => self::$has_next_page,
					'total_pages'   => self::$total_pages,
					'total_items'   => self::$total_items,
				),
				'filters'        => $filters,
			);
			wp_send_json( $response );
		}

		/**
		 * Load tickets based on current filter
		 *
		 * @return void
		 */
		private static function load_tickets() {

			if ( check_ajax_referer( 'general', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			$current_user = WPSC_Current_User::$current_user;
			if ( ! $current_user->is_customer ) {
				wp_send_json_error( new WP_Error( '001', 'Unauthorized!' ), 400 );
			}

			$filters = null;

			// check whether filters are given in post.
			$filters = isset( $_POST['filters'] ) ? map_deep( wp_unslash( $_POST['filters'] ), 'sanitize_text_field' ) : array();

			// get filters from cookies.
			$tl_filters = isset( $_COOKIE['wpsc-tl-filters'] ) ? sanitize_text_field( wp_unslash( $_COOKIE['wpsc-tl-filters'] ) ) : '';

			// use cookie filter if filters are not given.
			$filters = $filters == null && $tl_filters ? json_decode( $tl_filters, true ) : $filters;

			$customer_view = get_option( 'wpsc-tl-ms-customer-view', array() );
			self::$more_settings = get_option( $current_user->is_agent ? 'wpsc-tl-ms-agent-view' : 'wpsc-tl-ms-customer-view' );

			$current_user_filters = $current_user->get_tl_filters();
			self::$default_filters = $current_user_filters['default'];
			self::$saved_filters = $current_user_filters['saved'];

			if ( ! $filters || ! self::has_filter_access( $filters ) ) {
				$filters = array(
					'filterSlug' => $current_user->is_agent ? $current_user->agent->get_default_filter() : $customer_view['default-filter'],
				);
				if ( isset( self::$default_filters[ $filters['filterSlug'] ] ) ) {
					$filters['orderby'] = self::$more_settings['default-sort-by'];
					$filters['order']   = self::$more_settings['default-sort-order'];
				}
			}

			self::$default_flag = preg_match( '/default-(\d*)$/', $filters['filterSlug'], $default_matches );
			if ( self::$default_flag ) {
				self::$filter_id = $default_matches[1];
			}

			self::$saved_flag = preg_match( '/saved-(\d*)$/', $filters['filterSlug'], $saved_matches );
			if ( self::$saved_flag ) {
				self::$filter_id = $saved_matches[1];
			}

			// Order by.
			if ( ! isset( $filters['orderby'] ) && ! self::$default_flag && isset( self::$default_filters[ $filters['filterSlug'] ] ) ) {
				$filters['orderby'] = self::$more_settings['default-sort-by'];
			}
			if ( ! isset( $filters['orderby'] ) && self::$default_flag ) {
				$filters['orderby'] = self::$default_filters[ self::$filter_id ]['sort-by'];
			}
			if ( ! isset( $filters['orderby'] ) && self::$saved_flag ) {
				$filters['orderby'] = self::$saved_filters[ self::$filter_id ]['sort-by'];
			}

			// Order.
			if ( ! isset( $filters['order'] ) && ! self::$default_flag && isset( self::$default_filters[ $filters['filterSlug'] ] ) ) {
				$filters['order'] = self::$more_settings['default-sort-order'];
			}
			if ( ! isset( $filters['order'] ) && self::$default_flag ) {
				$filters['order'] = self::$default_filters[ self::$filter_id ]['sort-order'];
			}
			if ( ! isset( $filters['order'] ) && self::$saved_flag ) {
				$filters['order'] = self::$saved_filters[ self::$filter_id ]['sort-order'];
			}

			// Search.
			$filters['search'] = isset( $filters['search'] ) ? sanitize_text_field( $filters['search'] ) : '';

			// Current page number.
			$filters['page_no'] = isset( $filters['page_no'] ) ? intval( $filters['page_no'] ) : 1;

			// Set cookie here so that front-end should have access to filters until here only.
			setcookie( 'wpsc-tl-filters', wp_json_encode( $filters ), time() + 3600 );
			self::$cookie_filters = $filters;

			$filters['items_per_page'] = self::$more_settings['number-of-tickets'];

			// System query.
			$filters['system_query'] = $current_user->get_tl_system_query( $filters );

			// Meta query.
			$meta_query = array( 'relation' => 'AND' );

			if (
				isset( self::$default_filters[ $filters['filterSlug'] ] ) ||
				( self::$default_flag && isset( self::$default_filters[ self::$filter_id ] ) ) ||
				( self::$saved_flag && isset( self::$saved_filters[ self::$filter_id ] ) ) ||
				$filters['filterSlug'] == 'custom'
			) {

				$slug = self::$default_flag || self::$saved_flag ? self::$filter_id : '';
				if ( ! $slug ) {
					$slug = $filters['filterSlug'];
				}

				$parent_slug = is_numeric( $slug ) && self::$default_flag ? self::$default_filters[ $slug ]['parent-filter'] : '';
				if ( ! $parent_slug && is_numeric( $slug ) && self::$saved_flag ) {
					$parent_slug = self::$saved_filters[ $slug ]['parent-filter'];
				}
				if ( ! $parent_slug && $slug == 'custom' ) {
					$parent_slug = $filters['parent-filter'];
				}
				if ( ! $parent_slug ) {
					$parent_slug = $slug;
				}

				// Get parent meta queries.
				$meta_query = array_merge( $meta_query, self::get_parent_meta_query( $parent_slug ) );

				if ( self::$default_flag ) {
					$meta_query = array_merge( $meta_query, WPSC_Ticket_Conditions::get_meta_query( self::$default_filters[ $slug ]['filters'] ) );
				}

				if ( self::$saved_flag ) {
					$json_str = str_replace( PHP_EOL, '\n', self::$saved_filters[ $slug ]['filters'] );
					$meta_query = array_merge( $meta_query, WPSC_Ticket_Conditions::get_meta_query( $json_str, true ) );
				}

				if ( $filters['filterSlug'] == 'custom' ) {
					$meta_query = array_merge( $meta_query, WPSC_Ticket_Conditions::get_meta_query( $filters['filters'], true ) );
				}
			}

			$filters['meta_query'] = $meta_query;
			$filters['is_active']  = self::$is_active;
			self::$filters         = $filters;

			$response            = WPSC_Ticket::find( $filters );
			self::$tickets       = $response['results'];
			self::$total_items   = intval( $response['total_items'] );
			self::$total_pages   = intval( $response['total_pages'] );
			self::$has_next_page = $response['has_next_page'];
		}

		/**
		 * Print ticket filters layout
		 *
		 * @return void
		 */
		private static function get_filters() {

			$current_user  = WPSC_Current_User::$current_user;
			$custom_fields = WPSC_Custom_Field::$custom_fields;
			$list_items    = $current_user->get_tl_list_items();

			?>
			<div class="wpsc-filter">
				<div class="wpsc-search">
					<div class="search-field">
						<?php WPSC_Icons::get( 'search' ); ?>
						<input class="wpsc-search-input" type="text" placeholder="<?php esc_attr_e( 'Search...', 'supportcandy' ); ?>" spellcheck="false" value="<?php echo esc_attr( stripslashes( self::$filters['search'] ) ); ?>" onkeyup="wpsc_tl_search_keyup(event, this);"/>
					</div>
				</div>
				<div class="wpsc-filter-container">
					<div class="wpsc-filter-item">
						<label for="wpsc-input-filter"><?php esc_attr_e( 'Filter', 'supportcandy' ); ?></label>
						<select id="wpsc-input-filter" class="wpsc-input-filter" name="filter" onchange="wpsc_tl_filter_change(this);">

							<optgroup label="<?php esc_attr_e( 'Default filters', 'supportcandy' ); ?>">
								<?php
								foreach ( self::$default_filters as $index => $filter ) :
									$selected = '';
									if ( self::$filters['filterSlug'] == $index ) {
										$selected = 'selected="selected"';
									}
									if ( ! $selected && self::$default_flag && self::$filters['filterSlug'] == 'default-' . $index ) {
										$selected = 'selected="selected"';
									}
									?>
									<option <?php echo esc_attr( $selected ); ?> value="<?php echo is_numeric( $index ) ? 'default-' . esc_attr( $index ) : esc_attr( $index ); ?>">
										<?php
										$filter_label = $filter['label'] ? WPSC_Translations::get( 'wpsc-atl-' . $index, stripslashes( $filter['label'] ) ) : stripslashes( $filter['label'] );
										echo esc_attr( $filter_label )
										?>
									</option>
									<?php
								endforeach;
								?>
							</optgroup>
							<?php

							if ( ! $current_user->is_guest ) :
								?>
								<optgroup label="<?php esc_attr_e( 'Saved filters', 'supportcandy' ); ?>">
									<?php
									foreach ( self::$saved_filters as $index => $filter ) :
										$selected = '';
										if ( self::$saved_flag && self::$filters['filterSlug'] == 'saved-' . $index ) {
											$selected = 'selected="selected"';
										}
										?>
										<option <?php echo esc_attr( $selected ); ?> value="saved-<?php echo esc_attr( $index ); ?>"><?php echo esc_attr( $filter['label'] ); ?></option>
										<?php
									endforeach;
									?>
								</optgroup>
								<?php
							endif;
							?>

							<optgroup label="<?php esc_attr_e( 'Custom filters', 'supportcandy' ); ?>">
								<option <?php selected( self::$filters['filterSlug'], 'custom' ); ?> value="custom"><?php esc_attr_e( 'Custom...', 'supportcandy' ); ?></option>
							</optgroup>

						</select>
					</div>
					<div class="wpsc-filter-item">
						<label for="wpsc-input-sort-by"><?php esc_attr_e( 'Sort By', 'supportcandy' ); ?></label>
						<select id="wpsc-input-sort-by" class="wpsc-input-sort-by" name="sort-by">
							<?php
							foreach ( $list_items as $slug ) :
								$cf = WPSC_Custom_Field::get_cf_by_slug( $slug );
								if ( ! $cf || ! $cf->type::$is_sort ) {
									continue;
								}
								?>
								<option <?php selected( $cf->slug, self::$filters['orderby'] ); ?> value="<?php echo esc_attr( $cf->slug ); ?>"><?php echo esc_attr( $cf->name ); ?></option>
								<?php
							endforeach;
							?>
						</select>
					</div>
					<div class="wpsc-filter-item">
						<select id="wpsc-input-sort-order" class="wpsc-input-sort-order" name="sort-order">
							<option <?php selected( self::$filters['order'], 'ASC' ); ?> value="ASC"><?php esc_attr_e( 'ASC', 'supportcandy' ); ?></option>
							<option <?php selected( self::$filters['order'], 'DESC' ); ?> value="DESC"><?php esc_attr_e( 'DESC', 'supportcandy' ); ?></option>
						</select>
					</div>
					<div class="wpsc-filter-submit">
						<button class="wpsc-button normal primary margin-right" onclick="wpsc_tl_apply_filter_btn_click();"><?php esc_attr_e( 'Apply', 'supportcandy' ); ?></button>
						<div class="wpsc-filter-actions">
							<?php echo self::get_filter_actions(); // phpcs:ignore?> 
						</div>
					</div>
				</div>
			</div>
			<?php
		}

		/**
		 * Get filter actions based on current filter applied
		 *
		 * @return string
		 */
		private static function get_filter_actions() {

			$actions = array(
				'reset' => array(
					'label'    => esc_attr__( 'Reset', 'supportcandy' ),
					'callback' => 'wpsc_tl_reset_filter',
				),
			);

			if ( self::$saved_flag || self::$filters['filterSlug'] == 'custom' ) {
				$actions['edit'] = array(
					'label'    => esc_attr__( 'Edit', 'supportcandy' ),
					'callback' => 'wpsc_tl_edit_filter',
				);
			}

			if ( self::$saved_flag ) {
				$actions['delete'] = array(
					'label'    => esc_attr__( 'Delete', 'supportcandy' ),
					'callback' => 'wpsc_tl_delete_saved_filter',
				);
			}

			$actions_arr = array();
			foreach ( $actions as $key => $action ) :
				ob_start();
				?>
				<span 
					class="wpsc-link"
					onclick="<?php echo esc_attr( $action['callback'] ) . '();'; ?>">
					<?php echo esc_attr( $action['label'] ); ?>
				</span>
				<?php
				$actions_arr[] = ob_get_clean();
			endforeach;

			return implode( '<div class="action-devider"></div>', $actions_arr );
		}

		/**
		 * Print ticket list layout along with first page tickets
		 *
		 * @return void
		 */
		private static function get_ticket_list() {

			$current_user    = WPSC_Current_User::$current_user;
			$pagination_html = self::get_pagination_html();
			?>

			<div class="wpsc-tickets-container">
				<div class="wpsc-bulk-actions">
					<?php
					if ( $current_user->is_agent && self::$bulk_actions ) :
						?>
						<button 
							id="wpsc-bulk-actions-btn"
							class="wpsc-button small secondary"
							type="button"
							data-popover="wpsc-bulk-actions">
							<?php esc_attr_e( 'Bulk Actions', 'supportcandy' ); ?>
							<?php WPSC_Icons::get( 'chevron-down' ); ?>
						</button>
						<div id="wpsc-bulk-actions" class="gpopover wpsc-popover-menu wpsc-ticket-bulk-actions" >
							<?php
							echo self::get_bulk_actions(); // phpcs:ignore
							?>
						</div>
						<?php
					endif;
					?>
					<button 
						id="wpsc-more-actions-btn"
						class="wpsc-button small secondary"
						type="button"
						data-popover="wpsc-more-actions">
						<?php esc_attr_e( 'List Actions', 'supportcandy' ); ?>
						<?php WPSC_Icons::get( 'chevron-down' ); ?>
					</button>
					<div id="wpsc-more-actions" class="gpopover wpsc-popover-menu">
						<?php
						$more_actions = apply_filters(
							'wpsc_admin_tl_more_actions',
							array(
								'refresh'      => array(
									'icon'     => 'sync',
									'label'    => esc_attr__( 'Refresh', 'supportcandy' ),
									'callback' => 'wpsc_get_tickets',
								),
								'auto-refresh' => array(
									'icon'     => 'history',
									'label'    => esc_attr__( 'Auto-Refresh', 'supportcandy' ),
									'callback' => 'wpsc_get_tl_auto_refresh',
								),
							)
						);
						foreach ( $more_actions as $action ) :
							?>
							<div class="wpsc-popover-menu-item" onclick="<?php echo esc_attr( $action['callback'] ) . '(\'' . esc_attr( wp_create_nonce( $action['callback'] ) ) . '\');'; ?>">
							<?php WPSC_Icons::get( $action['icon'] ); ?>
								<span><?php echo esc_attr( $action['label'] ); ?></span>
							</div>
							<?php
						endforeach;
						?>
					</div>
					<script>
						jQuery('#wpsc-more-actions-btn, #wpsc-bulk-actions-btn').gpopover({width: 200});
					</script>
					<div class="wpsc-ticket-pagination-header wpsc-hidden-xs">
						<?php echo $pagination_html; // phpcs:ignore?>
					</div>
				</div>

				<div class="wpsc-ticket-list">
					<?php echo self::print_tickets(); // phpcs:ignore?>
				</div>

				<div class="wpsc-ticket-pagination-footer">
					<?php echo $pagination_html; // phpcs:ignore?>
				</div>
				<script>
					jQuery( document ).ready(function() {
						wpsc_check_nonce();
					});
					function wpsc_check_nonce(){

						if( supportcandy.current_section != 'ticket-list' ){
							return;
						}

						var data = { action: 'wpsc_get_nonce' };
						jQuery.post(
							supportcandy.ajax_url,
							data,
							function (response) {
								supportcandy.nonce = response.general;
								setTimeout(() => {
									wpsc_check_nonce();
								}, 60000);
							}
						);
					}
				</script>
			</div>
			<?php
		}

		/**
		 * Return HTML content of pagination section
		 */
		private static function get_pagination_html() {

			$pagination_str = self::get_pagination_str();
			$btn_style      = self::$total_items <= self::$more_settings['number-of-tickets'] ? 'display:none;' : '';
			ob_start();
			?>

			<span 
				class="wpsc-pagination-btn wpsc-pagination-first wpsc-link"
				style="<?php echo esc_attr( $btn_style ); ?>"
				onclick="wpsc_tl_set_page('first');">
				<?php esc_attr_e( 'First Page', 'supportcandy' ); ?>
			</span>
			<?php
			if ( is_rtl() ) {
				?>
				<span 
					class="wpsc-pagination-btn wpsc-pagination-next wpsc-link"
					style="<?php echo esc_attr( $btn_style ); ?>"
					onclick="wpsc_tl_set_page('next');">
					<?php WPSC_Icons::get( 'chevron-right' ); ?>
				</span>
				<?php
			} else {
				?>
				<span 
					class="wpsc-pagination-btn wpsc-pagination-prev wpsc-link"
					style="<?php echo esc_attr( $btn_style ); ?>"
					onclick="wpsc_tl_set_page('prev');">
					<?php WPSC_Icons::get( 'chevron-left' ); ?>
				</span>
				<?php
			}
			?>
			<span class="wpsc-pagination-txt"><?php echo esc_attr( $pagination_str ); ?></span>
			<?php
			if ( is_rtl() ) {
				?>
				<span 
					class="wpsc-pagination-btn wpsc-pagination-prev wpsc-link"
					style="<?php echo esc_attr( $btn_style ); ?>"
					onclick="wpsc_tl_set_page('prev');">
					<?php WPSC_Icons::get( 'chevron-left' ); ?>
				</span>
				<?php
			} else {
				?>
				<span 
					class="wpsc-pagination-btn wpsc-pagination-next wpsc-link"
					style="<?php echo esc_attr( $btn_style ); ?>"
					onclick="wpsc_tl_set_page('next');">
					<?php WPSC_Icons::get( 'chevron-right' ); ?>
				</span>
				<?php
			}
			?>
			<span 
				class="wpsc-pagination-btn wpsc-pagination-last wpsc-link"
				style="<?php echo esc_attr( $btn_style ); ?>"
				onclick="wpsc_tl_set_page('last');">
				<?php esc_attr_e( 'Last Page', 'supportcandy' ); ?>
			</span>
			<?php

			return ob_get_clean();
		}

		/**
		 * Pagination string
		 *
		 * @return string
		 */
		private static function get_pagination_str() {

			if ( self::$total_items < 1 ) {
				return '';
			}

			if ( self::$total_items == 1 ) {
				return esc_attr__( '1 Ticket', 'supportcandy' );
			}

			if ( self::$total_items <= self::$more_settings['number-of-tickets'] ) {
				/* translators: %1$s: total tickets */
				return sprintf( esc_attr__( '%1$d Tickets', 'supportcandy' ), self::$total_items );
			}

			$from = ( self::$more_settings['number-of-tickets'] * ( self::$filters['page_no'] - 1 ) ) + 1;
			$to   = self::$filters['page_no'] == self::$total_pages ?
					self::$total_items :
					self::$more_settings['number-of-tickets'] * self::$filters['page_no'];

			/* translators: e.g. 1-20 of 100 Tickets */
			return sprintf( esc_attr__( '%1$d-%2$d of %3$d Tickets', 'supportcandy' ), $from, $to, self::$total_items );
		}

		/**
		 * Get tickets based on current filter
		 *
		 * @return string
		 */
		private static function print_tickets() {

			ob_start();
			$current_user = WPSC_Current_User::$current_user;
			$list_items   = $current_user->get_tl_list_items();
			?>

			<div style="overflow-x:auto;">
				<table class="wpsc-ticket-list-tbl">

					<thead>
						<tr>
							<?php

							if ( $current_user->is_agent && self::$bulk_actions ) :
								?>
								<th>
									<div class="checkbox-container">
										<?php $unique_id = uniqid( 'wpsc_' ); ?>
										<input id="<?php echo esc_attr( $unique_id ); ?>" class="wpsc-bulk-selector" type="checkbox" onchange="wpsc_bulk_select_change();"/>
										<label for="<?php echo esc_attr( $unique_id ); ?>"></label>
									</div>
								</th>
								<?php
							endif;

							foreach ( $list_items as $slug ) :
								$cf = WPSC_Custom_Field::get_cf_by_slug( $slug );
								if ( ! $cf ) {
									continue;
								}
								?>
								<th style="min-width: <?php echo esc_attr( $cf->tl_width ); ?>px;"><?php echo esc_attr( $cf->name ); ?></th>
								<?php
							endforeach;
							?>

						</tr>
					</thead>

					<tbody>
						<?php

						if ( self::$tickets ) {

							foreach ( self::$tickets as $ticket ) :
								?>
								<tr onclick="if(link)wpsc_get_individual_ticket(<?php echo esc_attr( $ticket->id ); ?>)">
									<?php
									if ( $current_user->is_agent && self::$bulk_actions ) :
										?>
										<td class="bulk-selector" onmouseover="link=false;" onmouseout="link=true;">
											<div class="wpsc-tl-item-selector">
												<div class="checkbox-container">
													<?php $unique_id = uniqid( 'wpsc_' ); ?>
													<input id="<?php echo esc_attr( $unique_id ); ?>" class="wpsc-bulk-select" type="checkbox" onchange="wpsc_bulk_item_select_change();" value="<?php echo esc_attr( $ticket->id ); ?>"/>
													<label for="<?php echo esc_attr( $unique_id ); ?>"></label>
												</div>
											</div>
										</td>
										<?php
									endif;
									foreach ( $list_items as $slug ) :
										$cf = WPSC_Custom_Field::get_cf_by_slug( $slug );
										if ( ! $cf ) {
											continue;
										}
										?>
										<td onmouseover="link=true;">
											<?php
											if ( in_array( $cf->field, array( 'ticket', 'agentonly' ) ) ) {
												$cf->type::print_tl_ticket_field_val( $cf, $ticket );
											} else {
												$cf->type::print_tl_customer_field_val( $cf, $ticket->customer );
											}
											?>
										</td>
										<?php
									endforeach;
									?>
								</tr>
								<?php
							endforeach;

						} else {

							$col_span = count( $list_items );
							if ( $current_user->is_agent && self::$bulk_actions ) {
								$col_span++;
							}
							?>
							<tr><td colspan="<?php echo esc_attr( $col_span ); ?>" style="text-align:left;"><?php esc_attr_e( 'No tickets found!', 'supportcandy' ); ?></td></tr>
							<?php

						}
						?>

					</tbody>

				</table>
			</div>
			<?php

			return ob_get_clean();
		}

		/**
		 * Ticket list snippets. It may include static modal screens, JS initializers, etc.
		 *
		 * @return void
		 */
		public static function print_tl_snippets() {

			$filters = self::$cookie_filters;
			$filter_json = isset( $filters['filters'] ) && $filters['filters'] ? $filters['filters'] : '[]';
			$filters['filters'] = $filter_json;

			$ticket_list_js_vars = array(
				'pagination' => array(
					'current_page'  => self::$filters['page_no'],
					'has_next_page' => self::$has_next_page,
					'total_pages'   => self::$total_pages,
					'total_items'   => self::$total_items,
				),
				'filters'    => $filters,
			);

			$advanced_settings = get_option( 'wpsc-tl-ms-advanced', array() );
			?>

			<div class="wpsc-tl_snippets" style="display:none;">
				<div class="auto-refresh">
					<div class="modal-header">
						<?php esc_attr_e( 'Auto-Refresh', 'supportcandy' ); ?>
					</div>
					<div class="modal-body">
						<div class="wpsc-toggle-btn">
							<div class="toggle-off" onclick="wpsc_toggle_off(this);">
								<?php esc_attr_e( 'OFF', 'supportcandy' ); ?>
							</div>
							<div class="toggle-on" onclick="wpsc_toggle_on(this);">
								<?php esc_attr_e( 'ON', 'supportcandy' ); ?>
							</div>
							<input type="hidden" value="0">
						</div>
					</div>
					<div class="modal-footer">
						<button class="wpsc-button small primary" onclick="wpsc_set_tl_auto_refresh(this);">
							<?php esc_attr_e( 'Submit', 'supportcandy' ); ?>
						</button>
						<button class="wpsc-button small secondary" onclick="wpsc_close_modal();">
							<?php esc_attr_e( 'Cancel', 'supportcandy' ); ?>
						</button>
					</div>
				</div>
				<script>
					supportcandy.ticketList = <?php echo wp_json_encode( $ticket_list_js_vars ); ?>;
					supportcandy.tl_auto_refresh = <?php echo esc_attr( $advanced_settings['auto-refresh-list-status'] ); ?>;
					if(supportcandy.tl_auto_refresh) { wpsc_tl_auto_refresh(); }
				</script>
				<?php do_action( 'wpsc_tl_snippets' ); ?>
			</div>
			<?php
		}

		/**
		 * Check whether current user has access to given filter
		 *
		 * @param array $filters - ticket filters.
		 * @return boolean
		 */
		public static function has_filter_access( $filters ) {

			$current_user = WPSC_Current_User::$current_user;

			$filter_slug = isset( $filters['filterSlug'] ) ? $filters['filterSlug'] : '';

			if ( ! $filter_slug ) {
				return false;
			}

			if ( isset( self::$default_filters[ $filter_slug ] ) ) {
				return true;
			}

			$flag = preg_match( '/default-(\d*)$/', $filter_slug, $matches );
			if ( $flag && isset( self::$default_filters[ $matches[1] ] ) ) {
				return true;
			}

			$flag = preg_match( '/saved-(\d*)$/', $filter_slug, $matches );
			if ( $flag && isset( self::$saved_filters[ $matches[1] ] ) ) {
				return true;
			}

			if ( $filter_slug == 'custom' && WPSC_Ticket_Conditions::is_valid_input_conditions( 'wpsc_custom_filter_conditions', $filters['filters'] ) ) {
				return true;
			}

			return false;
		}

		/**
		 * Convert UI filters in json format to meta query
		 *
		 * @param array $filters - ticket filters.
		 * @return array
		 */
		public static function get_meta_query( $filters ) {

			$meta_query = array();
			foreach ( $filters as $slug => $condition ) {

				$cf = WPSC_Custom_Field::get_cf_by_slug( $slug );
				if ( ! $cf ) {
					continue;
				}

				$val = $cf->type::get_meta_value( $condition );
				if ( $val === false ) {
					continue;
				}

				$meta_query[] = array(
					'slug'    => $slug,
					'compare' => $condition['operator'],
					'val'     => $val,
				);
			}
			return $meta_query;
		}

		/**
		 * Return parent meta query
		 *
		 * @param string $parent_slug - filter type.
		 * @return array
		 */
		public static function get_parent_meta_query( $parent_slug ) {

			$current_user = WPSC_Current_User::$current_user;
			$meta_query   = array();
			switch ( $parent_slug ) {

				case 'all':
					break;

				case 'unresolved':
					if ( ! isset( self::$default_filters['unresolved'] ) ) {
						break;
					}
					$meta_query[] = array(
						'slug'    => 'status',
						'compare' => 'IN',
						'val'     => self::$more_settings['unresolved-ticket-statuses'],
					);
					break;

				case 'unassigned':
					if ( ! isset( self::$default_filters['unassigned'] ) ) {
						break;
					}
					$meta_query[] = array(
						'slug'    => 'assigned_agent',
						'compare' => '=',
						'val'     => '',
					);
					break;

				case 'mine':
					if ( ! isset( self::$default_filters['mine'] ) ) {
						break;
					}
					$meta_query[] = array(
						'slug'    => 'assigned_agent',
						'compare' => '=',
						'val'     => $current_user->agent->id,
					);
					$meta_query[] = array(
						'slug'    => 'status',
						'compare' => 'IN',
						'val'     => self::$more_settings['unresolved-ticket-statuses'],
					);
					break;

				case 'closed':
					if ( ! isset( self::$default_filters['closed'] ) ) {
						break;
					}
					$ms_advanced     = get_option( 'wpsc-tl-ms-advanced' );
					$gs              = get_option( 'wpsc-gs-general' );
					$closed_statuses = array( $gs['close-ticket-status'] );
					$closed_statuses = array_merge( $closed_statuses, $ms_advanced['closed-ticket-statuses'] );
					$closed_statuses = array_unique( $closed_statuses );
					$meta_query[]    = array(
						'slug'    => 'status',
						'compare' => 'IN',
						'val'     => $closed_statuses,
					);
					break;

				case 'deleted':
					if ( isset( self::$default_filters['deleted'] ) && self::$is_active !== 0 ) {
						self::$is_active = 0;
					}
					break;

				default:
					// Break if not exists.
					if ( ! is_numeric( $parent_slug ) || ! isset( self::$default_filters[ $parent_slug ] ) ) {
						break;
					}

					$meta_query = array_merge( $meta_query, self::get_parent_meta_query( self::$default_filters[ $parent_slug ]['parent-filter'] ) );
					$meta_query = array_merge( $meta_query, WPSC_Ticket_Conditions::get_meta_query( self::$default_filters[ $parent_slug ]['filters'] ) );
			}

			return $meta_query;
		}

		/**
		 * Custom filter modal pop-up
		 *
		 * @return void
		 */
		public static function get_tl_custom_filter_ui() {

			if ( check_ajax_referer( 'general', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			$current_user = WPSC_Current_User::$current_user;
			if ( ! ( $current_user->is_agent || $current_user->is_customer ) ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$title           = esc_attr__( 'Custom filter', 'supportcandy' );
			$default_filters = get_option( $current_user->is_agent ? 'wpsc-atl-default-filters' : 'wpsc-ctl-default-filters' );
			$list_items      = $current_user->get_tl_list_items();
			$more_settings   = get_option( $current_user->is_agent ? 'wpsc-tl-ms-agent-view' : 'wpsc-tl-ms-customer-view' );

			// check whether filters are passed.
			$filters = isset( $_POST['filters'] ) ? map_deep( wp_unslash( $_POST['filters'] ), 'sanitize_text_field' ) : array();
			$custom_filters = isset( $filters['filters'] ) && WPSC_Ticket_Conditions::is_valid_input_conditions( 'wpsc_custom_filter_conditions', $filters['filters'] ) ? $filters['filters'] : '';

			ob_start();
			?>
			<form action="#" onsubmit="return false;" class="wpsc-tl-custom-filter">
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for="">
							<?php esc_attr_e( 'Parent filter', 'supportcandy' ); ?>
							<span class="required-char">*</span>
						</label>
					</div>
					<select name="parent-filter">
						<?php
						foreach ( $default_filters as $slug => $filter ) :
							if ( $slug == 'deleted' && $current_user->is_agent && ! $current_user->agent->has_cap( 'dtt-access' ) ) {
								continue;
							}
							$selected = isset( $filters['parent-filter'] ) && $filters['parent-filter'] == $slug ? 'selected="selected"' : ''
							?>
							<option <?php echo esc_attr( $selected ); ?> value="<?php echo esc_attr( $slug ); ?>"><?php echo esc_attr( $filter['label'] ); ?></option>
							<?php
						endforeach;
						?>
					</select>
				</div>
				<?php WPSC_Ticket_Conditions::print( 'custom_filters', 'wpsc_custom_filter_conditions', $custom_filters, true, __( 'Filters', 'supportcandy' ) ); ?>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for="">
							<?php esc_attr_e( 'Sort by', 'supportcandy' ); ?>
							<span class="required-char">*</span>
						</label>
					</div>
					<select name="sort-by">
						<?php
						$orderby = isset( $filters['orderby'] ) ? $filters['orderby'] : $more_settings['default-sort-by'];
						foreach ( $list_items as $slug ) :
							$cf = WPSC_Custom_Field::get_cf_by_slug( $slug );
							if ( ! $cf ) {
								continue;
							}
							if ( $cf->type::$is_sort ) :
								?>
								<option <?php selected( $orderby, $cf->slug ); ?> value="<?php echo esc_attr( $cf->slug ); ?>"><?php echo esc_attr( $cf->name ); ?></option>
								<?php
							endif;
						endforeach;
						?>
					</select>
				</div>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for="">
							<?php esc_attr_e( 'Sort order', 'supportcandy' ); ?>
							<span class="required-char">*</span>
						</label>
					</div>
					<select name="sort-order">
						<?php
						$order = isset( $filters['order'] ) ? $filters['order'] : $more_settings['default-sort-order'];
						?>
						<option <?php selected( $order, 'ASC' ); ?> value="ASC"><?php esc_attr_e( 'ASC', 'supportcandy' ); ?></option>
						<option <?php selected( $order, 'DESC' ); ?> value="DESC"><?php esc_attr_e( 'DESC', 'supportcandy' ); ?></option>
					</select>
				</div>
			</form>
			<?php
			$body = ob_get_clean();

			ob_start();
			?>
			<button class="wpsc-button small primary" onclick="wpsc_tl_apply_custom_filter(this);">
				<?php esc_attr_e( 'Apply', 'supportcandy' ); ?>
			</button>
			<?php

			if ( ! $current_user->is_guest ) :
				?>
				<button class="wpsc-button small secondary" onclick="wpsc_tl_add_saved_filter(this);">
					<?php esc_attr_e( 'Save & Apply', 'supportcandy' ); ?>
				</button>
				<?php
			endif;
			?>

			<button class="wpsc-button small secondary" onclick="<?php echo $filters ? 'wpsc_close_modal()' : 'wpsc_tl_close_custom_filter_modal()'; ?>;">
				<?php esc_attr_e( 'Cancel', 'supportcandy' ); ?>
			</button>
			<?php
			$footer = ob_get_clean();

			$response = array(
				'title'  => $title,
				'body'   => $body,
				'footer' => $footer,
			);
			wp_send_json( $response );
		}

		/**
		 * Add saved filter UI
		 *
		 * @return void
		 */
		public static function get_add_saved_filter_ui() {

			$current_user = WPSC_Current_User::$current_user;
			if ( ! $current_user->customer->user->ID ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$title = esc_attr__( 'Custom filter', 'supportcandy' );

			ob_start();
			?>
			<form action="#" onsubmit="return false;" class="wpsc-tl-add-saved-filter">
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for="">
							<?php esc_attr_e( 'Label', 'supportcandy' ); ?>
							<span class="required-char">*</span>
						</label>
					</div>
					<input class="wpsc-cf-label" type="text" name="label" autocomplete="off"/>
				</div>
			</form>
			<?php
			$body = ob_get_clean();

			ob_start();
			?>
			<button class="wpsc-button small primary" onclick="wpsc_tl_set_add_saved_filter(this,  '<?php echo esc_attr( wp_create_nonce( 'wpsc_tl_set_add_saved_filter' ) ); ?>');">
				<?php esc_attr_e( 'Save & Apply', 'supportcandy' ); ?>
			</button>
			<button class="wpsc-button small secondary" onclick="wpsc_close_modal();">
				<?php esc_attr_e( 'Cancel', 'supportcandy' ); ?>
			</button>
			<?php
			$footer = ob_get_clean();

			$response = array(
				'title'  => $title,
				'body'   => $body,
				'footer' => $footer,
			);
			wp_send_json( $response );
		}

		/**
		 * Set add saved filter
		 */
		public static function set_add_saved_filter() {

			if ( check_ajax_referer( 'wpsc_tl_set_add_saved_filter', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			$current_user = WPSC_Current_User::$current_user;
			if ( ! $current_user->customer->user->ID || ! isset( $_POST['filters'] ) ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$default_filters = $current_user->is_agent ? get_option( 'wpsc-atl-default-filters' ) : get_option( 'wpsc-atl-default-filters' );

			$label = isset( $_POST['filters']['label'] ) ? sanitize_text_field( wp_unslash( $_POST['filters']['label'] ) ) : '';
			if ( ! $label ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$parent_filter = isset( $_POST['filters']['parent-filter'] ) ? sanitize_text_field( wp_unslash( $_POST['filters']['parent-filter'] ) ) : '';
			if ( ! $parent_filter || ! isset( $default_filters[ $parent_filter ] ) ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$filters = isset( $_POST['filters']['filters'] ) ? sanitize_text_field( wp_unslash( $_POST['filters']['filters'] ) ) : '';
			if ( ! $filters || $filters == '[]' || ! WPSC_Ticket_Conditions::is_valid_input_conditions( 'wpsc_custom_filter_conditions', $filters ) ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$filters = str_replace( '\n', PHP_EOL, $filters );

			$sort_by = isset( $_POST['filters']['orderby'] ) ? sanitize_text_field( wp_unslash( $_POST['filters']['orderby'] ) ) : '';
			if ( ! $sort_by ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$sort_order = isset( $_POST['filters']['order'] ) ? sanitize_text_field( wp_unslash( $_POST['filters']['order'] ) ) : '';
			if ( ! $sort_order ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$saved_filters           = $current_user->get_saved_filters();
			$index                   = WPSC_Functions::get_tl_cf_auto_increament();
			$saved_filters[ $index ] = array(
				'label'         => $label,
				'parent-filter' => $parent_filter,
				'filters'       => $filters,
				'sort-by'       => $sort_by,
				'sort-order'    => $sort_order,
			);
			update_user_meta( $current_user->customer->user->ID, get_current_blog_id() . '-wpsc-tl-saved-filters', $saved_filters );

			$response = array( 'slug' => 'saved-' . $index );
			wp_send_json( $response );
		}

		/**
		 * Edit saved filter UI
		 *
		 * @return void
		 */
		public static function get_edit_saved_filter_ui() {

			if ( check_ajax_referer( 'general', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			$current_user = WPSC_Current_User::$current_user;
			if ( ! $current_user->customer->user->ID ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$filter_slug = isset( $_POST['filterSlug'] ) ? sanitize_text_field( wp_unslash( $_POST['filterSlug'] ) ) : '';
			if ( ! $filter_slug ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$flag = preg_match( '/saved-(\d*)$/', $filter_slug, $matches );
			if ( ! $flag ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$saved_filters = $current_user->get_saved_filters();
			$filters       = $saved_filters[ $matches[1] ];

			$title           = $filters['label'];
			$default_filters = $current_user->is_agent ? get_option( 'wpsc-atl-default-filters' ) : get_option( 'wpsc-atl-default-filters' );
			$list_items      = $current_user->get_tl_list_items();
			$more_settings   = $current_user->is_agent ? get_option( 'wpsc-tl-ms-agent-view' ) : get_option( 'wpsc-tl-ms-customer-view' );
			$custom_filters = str_replace( PHP_EOL, '\n', $filters['filters'] );

			ob_start();
			?>
			<form action="#" onsubmit="return false;" class="wpsc-tl-custom-filter">
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for="">
							<?php esc_attr_e( 'Label', 'supportcandy' ); ?>
							<span class="required-char">*</span>
						</label>
					</div>
					<input class="wpsc-cf-label" type="text" name="label" value="<?php echo esc_attr( $filters['label'] ); ?>" autocomplete="off"/>
				</div>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for="">
							<?php esc_attr_e( 'Parent filter', 'supportcandy' ); ?>
							<span class="required-char">*</span>
						</label>
					</div>
					<select name="parent-filter">
						<?php
						foreach ( $default_filters as $slug => $filter ) :
							$selected = isset( $filters['parent-filter'] ) && $filters['parent-filter'] == $slug ? 'selected="selected"' : ''
							?>
							<option <?php echo esc_attr( $selected ); ?> value="<?php echo esc_attr( $slug ); ?>"><?php echo esc_attr( $filter['label'] ); ?></option>
							<?php
						endforeach;
						?>
					</select>
				</div>
				<?php WPSC_Ticket_Conditions::print( 'custom_filters', 'wpsc_custom_filter_conditions', $custom_filters, true, __( 'Filters', 'supportcandy' ) ); ?>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for="">
							<?php esc_attr_e( 'Sort by', 'supportcandy' ); ?>
							<span class="required-char">*</span>
						</label>
					</div>
					<select name="sort-by">
						<?php
						$orderby = isset( $filters['sort-by'] ) ? $filters['sort-by'] : $more_settings['default-sort-by'];
						foreach ( $list_items as $slug ) :
							$cf = WPSC_Custom_Field::get_cf_by_slug( $slug );
							if ( ! $cf ) {
								continue;
							}
							if ( $cf->type::$is_sort ) :
								?>
								<option <?php selected( $orderby, $cf->slug ); ?> value="<?php echo esc_attr( $cf->slug ); ?>"><?php echo esc_attr( $cf->name ); ?></option>
								<?php
							endif;
						endforeach;
						?>
					</select>
				</div>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for="">
							<?php esc_attr_e( 'Sort order', 'supportcandy' ); ?>
							<span class="required-char">*</span>
						</label>
					</div>
					<select name="sort-order">
					<?php
						$order = isset( $filters['sort-order'] ) ? $filters['sort-order'] : $more_settings['default-sort-order'];
					?>
						<option <?php selected( $order, 'ASC' ); ?> value="ASC"><?php esc_attr_e( 'ASC', 'supportcandy' ); ?></option>
						<option <?php selected( $order, 'DESC' ); ?> value="DESC"><?php esc_attr_e( 'DESC', 'supportcandy' ); ?></option>
					</select>
				</div>
				<input type="hidden" name="action" value="wpsc_tl_set_edit_saved_filter"/>
				<input type="hidden" name="slug" value="<?php echo esc_attr( $matches[1] ); ?>"/>
				<input type="hidden" name="_ajax_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wpsc_tl_set_edit_saved_filter' ) ); ?>">
			</form>
			<?php
			$body = ob_get_clean();

			ob_start();
			?>
			<button class="wpsc-button small primary" onclick="wpsc_tl_set_edit_saved_filter(this);">
				<?php esc_attr_e( 'Save & Apply', 'supportcandy' ); ?>
			</button>
			<button class="wpsc-button small secondary" onclick="wpsc_tl_add_saved_filter();">
				<?php esc_attr_e( 'Save As', 'supportcandy' ); ?>
			</button>
			<button class="wpsc-button small secondary" onclick="wpsc_close_modal();">
				<?php esc_attr_e( 'Cancel', 'supportcandy' ); ?>
			</button>
			<?php
			$footer = ob_get_clean();

			$response = array(
				'title'  => $title,
				'body'   => $body,
				'footer' => $footer,
			);
			wp_send_json( $response );
		}

		/**
		 * Set edit saved filter
		 */
		public static function set_edit_saved_filter() {

			if ( check_ajax_referer( 'wpsc_tl_set_edit_saved_filter', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			$current_user = WPSC_Current_User::$current_user;
			if ( ! $current_user->customer->user->ID ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$slug = isset( $_POST['slug'] ) ? intval( $_POST['slug'] ) : 0;
			if ( ! $slug ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$saved_filters = $current_user->get_saved_filters();
			if ( ! isset( $saved_filters[ $slug ] ) ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$label = isset( $_POST['label'] ) ? sanitize_text_field( wp_unslash( $_POST['label'] ) ) : '';
			if ( ! $label ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$parent_filter = isset( $_POST['parent-filter'] ) ? sanitize_text_field( wp_unslash( $_POST['parent-filter'] ) ) : '';
			if ( ! $parent_filter ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$filters = isset( $_POST['filters'] ) ? sanitize_text_field( wp_unslash( $_POST['filters'] ) ) : '';
			if ( ! $filters || $filters == '[]' || ! WPSC_Ticket_Conditions::is_valid_input_conditions( 'wpsc_custom_filter_conditions', $filters ) ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$filters = str_replace( '\n', PHP_EOL, $filters );

			$sort_by = isset( $_POST['sort-by'] ) ? sanitize_text_field( wp_unslash( $_POST['sort-by'] ) ) : '';
			if ( ! $sort_by ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$sort_order = isset( $_POST['sort-order'] ) ? sanitize_text_field( wp_unslash( $_POST['sort-order'] ) ) : '';
			if ( ! $sort_order ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$saved_filters[ $slug ] = array(
				'label'         => $label,
				'parent-filter' => $parent_filter,
				'filters'       => $filters,
				'sort-by'       => $sort_by,
				'sort-order'    => $sort_order,
			);
			update_user_meta( $current_user->customer->user->ID, get_current_blog_id() . '-wpsc-tl-saved-filters', $saved_filters );
			wp_die();
		}

		/**
		 * Delete saved filter
		 *
		 * @return void
		 */
		public static function delete_saved_filter() {

			if ( check_ajax_referer( 'general', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			$current_user = WPSC_Current_User::$current_user;
			if ( ! $current_user->customer->user->ID ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$slug = isset( $_POST['slug'] ) ? sanitize_text_field( wp_unslash( $_POST['slug'] ) ) : '';
			if ( ! $slug ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$flag = preg_match( '/saved-(\d*)$/', $slug, $matches );
			if ( ! $flag ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$slug = $matches[1];

			$saved_filters = $current_user->get_saved_filters();
			if ( ! isset( $saved_filters[ $slug ] ) ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			unset( $saved_filters[ $slug ] );

			update_user_meta( $current_user->customer->user->ID, get_current_blog_id() . '-wpsc-tl-saved-filters', $saved_filters );
			wp_die();
		}

		/**
		 * Get change bult ticket status/categoty/priority
		 *
		 * @return void
		 */
		public static function bulk_change_status() {

			if ( check_ajax_referer( 'wpsc_bulk_change_status', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			$ticket_ids = isset( $_POST['ticket_ids'] ) ? array_filter( array_map( 'intval', $_POST['ticket_ids'] ) ) : array();
			if ( ! $ticket_ids ) {
				wp_send_json_error( 'Something went wrong!', 400 );
			}

			$widgets = get_option( 'wpsc-ticket-widget' );
			$title   = $widgets['change-status']['title'];

			$current_user = WPSC_Current_User::$current_user;

			$gs          = get_option( 'wpsc-gs-general' );
			$tl_advanced = get_option( 'wpsc-tl-ms-advanced' );
			$close_flag  = in_array( $current_user->agent->role, $gs['allow-close-ticket'] ) ? true : false;

			$statuses   = WPSC_Status::find( array( 'items_per_page' => 0 ) )['results'];
			$categories = WPSC_Category::find( array( 'items_per_page' => 0 ) )['results'];
			$priorities = WPSC_Priority::find( array( 'items_per_page' => 0 ) )['results'];

			ob_start();
			?>
			<form action="#" onsubmit="return false;" class="frm-edit-bulk-status">
				<div class="wpsc-input-group">
					<?php $cf = WPSC_Custom_Field::get_cf_by_slug( 'status' ); ?>
					<div class="label-container">
						<label for=""><?php echo esc_attr( $cf->name ); ?></label>
					</div>
					<select id="wpsc-select-bulk-ticket-status" name="status_id">
						<option value=""></option>
						<?php
						foreach ( $statuses as $status ) :
							if (
								( $status->id == $gs['close-ticket-status'] || in_array( $status->id, $tl_advanced['closed-ticket-statuses'] ) ) &&
								! $close_flag
							) {
								continue;
							}
							?>
							<option value="<?php echo esc_attr( $status->id ); ?>"><?php echo esc_attr( $status->name ); ?></option>
							<?php
						endforeach;
						?>
					</select>
					<script>
						jQuery('#wpsc-select-bulk-ticket-status').selectWoo({
							allowClear: true,
							placeholder: ""
						});
					</script>
				</div>
				<div class="wpsc-input-group">
					<?php $cf = WPSC_Custom_Field::get_cf_by_slug( 'category' ); ?>
					<div class="label-container">
						<label for=""><?php echo esc_attr( $cf->name ); ?></label>
					</div>
					<select id="wpsc-select-bulk-ticket-category" name="cat_id">
						<option value=""></option>
						<?php
						foreach ( $categories as $category ) :
							?>
							<option value="<?php echo esc_attr( $category->id ); ?>"><?php echo esc_attr( $category->name ); ?></option>
							<?php
						endforeach;
						?>
					</select>
					<script>
						jQuery('#wpsc-select-bulk-ticket-category').selectWoo({
							allowClear: true,
							placeholder: ""
						});
					</script>
				</div>
				<div class="wpsc-input-group">
					<?php $cf = WPSC_Custom_Field::get_cf_by_slug( 'priority' ); ?>
					<div class="label-container">
						<label for=""><?php echo esc_attr( $cf->name ); ?></label>
					</div>
					<select id="wpsc-select-bulk-ticket-priority" name="priority_id">
						<option value=""></option>
						<?php
						foreach ( $priorities as $priority ) :
							?>
							<option value="<?php echo esc_attr( $priority->id ); ?>"><?php echo esc_attr( $priority->name ); ?></option>
							<?php
						endforeach;
						?>
					</select>
					<script>
						jQuery('#wpsc-select-bulk-ticket-priority').selectWoo({
							allowClear: true,
							placeholder: ""
						});
					</script>
				</div>
				<?php do_action( 'wpsc_get_bulk_ticket_status_body', $ticket_ids ); ?>
				<input type="hidden" name="action" value="wpsc_set_bulk_change_status">
				<input type="hidden" name="ticket_ids" value="<?php echo esc_attr( implode( ',', $ticket_ids ) ); ?>">
				<input type="hidden" name="_ajax_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wpsc_set_bulk_change_status' ) ); ?>">
			</form>
			<?php
			do_action( 'wpsc_get_bulk_ticket_status_footer', $ticket_ids );
			$body = ob_get_clean();

			ob_start();
			?>
			<button class="wpsc-button small primary" onclick="wpsc_set_bulk_change_status(this);">
				<?php esc_attr_e( 'Submit', 'supportcandy' ); ?>
			</button>
			<button class="wpsc-button small secondary" onclick="wpsc_close_modal();">
				<?php esc_attr_e( 'Cancel', 'supportcandy' ); ?>
			</button>
			<?php
			$footer = ob_get_clean();

			$response = array(
				'title'  => $title,
				'body'   => $body,
				'footer' => $footer,
			);
			wp_send_json( $response );
		}

		/**
		 * Set change bulk statuses/category/priority
		 *
		 * @return void
		 */
		public static function set_bulk_change_status() {

			if ( check_ajax_referer( 'wpsc_set_bulk_change_status', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			$ticket_ids = isset( $_POST['ticket_ids'] ) ? array_filter( array_map( 'intval', explode( ',', sanitize_text_field( wp_unslash( $_POST['ticket_ids'] ) ) ) ) ) : array();
			if ( ! $ticket_ids ) {
				wp_send_json_error( 'Missing ticket ids', 400 );
			}

			$current_user = WPSC_Current_User::$current_user;

			$status_id   = isset( $_POST['status_id'] ) ? intval( $_POST['status_id'] ) : 0;
			$cat_id      = isset( $_POST['cat_id'] ) ? intval( $_POST['cat_id'] ) : 0;
			$priority_id = isset( $_POST['priority_id'] ) ? intval( $_POST['priority_id'] ) : 0;

			$gs          = get_option( 'wpsc-gs-general' );
			$tl_advanced = get_option( 'wpsc-tl-ms-advanced' );
			$close_flag  = in_array( $current_user->agent->role, $gs['allow-close-ticket'] ) ? true : false;

			foreach ( $ticket_ids as $ticket_id ) {

				$ticket = new WPSC_Ticket( $ticket_id );
				if ( ! $ticket->id ) {
					continue;
				}

				WPSC_Individual_Ticket::$ticket = $ticket;

				if (
					( $status_id == $gs['close-ticket-status'] || in_array( $status_id, $tl_advanced['closed-ticket-statuses'] ) ) &&
					! $close_flag
				) {
					continue;
				}

				if ( $ticket->is_active && ! ( $current_user->is_agent && WPSC_Individual_Ticket::has_ticket_cap( 'cs' ) ) ) {
					continue;
				}

				// Check status.
				if ( $status_id && $ticket->status->id != $status_id ) {
					WPSC_Individual_Ticket::change_status( $ticket->status->id, $status_id, $current_user->customer->id );
				}

				// Check category.
				if ( $cat_id && $ticket->category->id != $cat_id ) {
					WPSC_Individual_Ticket::change_category( $ticket->category->id, $cat_id, $current_user->customer->id );
				}

				// Check priority.
				if ( $priority_id && $ticket->priority->id != $priority_id ) {
					WPSC_Individual_Ticket::change_priority( $ticket->priority->id, $priority_id, $current_user->customer->id );
				}
			}
			wp_die();
		}

		/**
		 * Get edit assign agents
		 *
		 * @return void
		 */
		public static function bulk_assign_agents() {

			if ( check_ajax_referer( 'wpsc_bulk_assign_agents', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			$ticket_ids = isset( $_POST['ticket_ids'] ) ? array_filter( array_map( 'intval', $_POST['ticket_ids'] ) ) : array();
			if ( ! $ticket_ids ) {
				wp_send_json_error( 'Something went wrong!', 400 );
			}

			$widgets = get_option( 'wpsc-ticket-widget' );
			$title   = $widgets['assignee']['title'];

			ob_start();
			?>
			<form action="#" onsubmit="return false;" class="frm-bulk-assign-agent">
				<div class="wpsc-input-group" style="flex-direction:row; flex-wrap:nowrap;">
					<div class="wpsc-input-group">
						<div class="label-container">
							<label for=""><?php esc_attr_e( 'Filter by', 'supportcandy' ); ?></label>
						</div>
						<select class="agent-filter-by">
							<?php
							$filter_by = apply_filters(
								'wpsc_assignee_filter_by',
								array(
									'all' => esc_attr__( 'All', 'supportcandy' ),
								)
							);
							foreach ( $filter_by as $key => $label ) :
								?>
								<option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_attr( $label ); ?></option>
								<?php
							endforeach
							?>
						</select>
					</div>
					<div style="margin:5px;"></div>
					<div class="wpsc-input-group">
						<div class="label-container">
							<label for=""><?php esc_attr_e( 'Sort by', 'supportcandy' ); ?></label>
						</div>
						<select class="agent-sort-by">
							<option value="workload"><?php esc_attr_e( 'Workload', 'supportcandy' ); ?></option>
							<option value="name"><?php esc_attr_e( 'Name', 'supportcandy' ); ?></option>
						</select>
					</div>
				</div>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Select agent(s)', 'supportcandy' ); ?></label>
					</div>
					<select id="wpsc-select-bulk-assign-agent" multiple name="assignee[]"></select>
						<script>
						jQuery('#wpsc-select-bulk-assign-agent').selectWoo({
							ajax: {
								url: supportcandy.ajax_url,
								dataType: 'json',
								delay: 250,
								data: function (params) {
									return {
										q: params.term, // search term.
										action: 'wpsc_agent_autocomplete_bulk_assign',
										_ajax_nonce: '<?php echo esc_attr( wp_create_nonce( 'wpsc_agent_autocomplete_bulk_assign' ) ); ?>',
										filter_by: jQuery('select.agent-filter-by').val(),
										sort_by: jQuery('select.agent-sort-by').val(),
										isMultiple: 1,
									};
								},
								processResults: function (data, params) {
									var terms = [];
									if ( data ) {
										jQuery.each( data, function( id, text ) {
											terms.push( { id: text.id, text: text.title } );
										});
									}
									return {
										results: terms
									};
								},
								cache: true
							},
							escapeMarkup: function (markup) { return markup; }, // let our custom formatter work.
							minimumInputLength: 0,
							allowClear: false,
							placeholder: ""
						});
					</script>
				</div>
				<?php do_action( 'wpsc_get_bulk_assigned_agent_body', $ticket_ids ); ?>
				<input type="hidden" name="action" value="wpsc_set_bulk_assign_agent">
				<input type="hidden" name="ticket_ids" value="<?php echo esc_attr( implode( ',', $ticket_ids ) ); ?>">
				<input type="hidden" name="_ajax_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wpsc_set_bulk_assign_agent' ) ); ?>">
			</form>
			<?php
			do_action( 'wpsc_get_bulk_assigned_agent_footer' );
			$body = ob_get_clean();

			ob_start();
			?>
			<button class="wpsc-button small primary" onclick="wpsc_set_bulk_assign_agent(this);">
				<?php esc_attr_e( 'Submit', 'supportcandy' ); ?>
			</button>
			<button class="wpsc-button small secondary" onclick="wpsc_close_modal();">
				<?php esc_attr_e( 'Cancel', 'supportcandy' ); ?>
			</button>
			<?php
			$footer = ob_get_clean();

			$response = array(
				'title'  => $title,
				'body'   => $body,
				'footer' => $footer,
			);

			wp_send_json( $response );
		}

		/**
		 * Change assigned agent
		 *
		 * @return void
		 */
		public static function set_bulk_assign_agent() {

			if ( check_ajax_referer( 'wpsc_set_bulk_assign_agent', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			$ticket_ids = isset( $_POST['ticket_ids'] ) ? array_filter( array_map( 'intval', explode( ',', sanitize_text_field( wp_unslash( $_POST['ticket_ids'] ) ) ) ) ) : array();
			if ( ! $ticket_ids ) {
				wp_send_json_error( 'Missing ticket ids', 400 );
			}

			$new_ids = isset( $_POST['assignee'] ) ? array_filter( array_map( 'intval', $_POST['assignee'] ) ) : array();
			if ( ! $new_ids ) {
				wp_send_json_error( 'Something went wrong!', 400 );
			}

			foreach ( $ticket_ids as $ticket_id ) {

				$ticket = new WPSC_Ticket( $ticket_id );
				if ( ! $ticket->id ) {
					continue;
				}

				WPSC_Individual_Ticket::$ticket = $ticket;

				$current_user = WPSC_Current_User::$current_user;
				if ( $ticket->is_active && ! ( $current_user->is_agent && WPSC_Individual_Ticket::has_ticket_cap( 'aa' ) ) ) {
					continue;
				}

				$prev = $ticket->assigned_agent;

				// Check whether all new agets exists.
				$new = array();
				foreach ( $new_ids as $id ) {
					$agent = new WPSC_Agent( $id );
					$new[] = $agent;
					if ( ! $agent->id ) {
						wp_send_json_error( 'Something went wrong!', 400 );
					}
				}

				$prev_ids = array();
				foreach ( $prev as $agent ) {
					$prev_ids[] = $agent->id;
				}

				// Exit if there is no change.
				if (
					count( array_diff( $new_ids, $prev_ids ) ) === 0 &&
					count( array_diff( $prev_ids, $new_ids ) ) === 0
				) {
					wp_die();
				}

				// Change assignee.
				WPSC_Individual_Ticket::change_assignee( $prev, $new, $current_user->customer->id );
			}
			wp_die();
		}

		/**
		 * Delete ticket ajax request
		 */
		public static function bulk_delete_tickets() {

			if ( check_ajax_referer( 'wpsc_bulk_delete_tickets', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			$current_user = WPSC_Current_User::$current_user;
			if ( ! $current_user->is_agent ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			$ticket_ids = isset( $_POST['ticket_ids'] ) ? array_filter( array_map( 'intval', $_POST['ticket_ids'] ) ) : array();
			if ( ! $ticket_ids ) {
				wp_send_json_error( 'Something went wrong!', 400 );
			}

			foreach ( $ticket_ids as $ticket_id ) {

				$ticket = new WPSC_Ticket( $ticket_id );
				WPSC_Individual_Ticket::$ticket = $ticket;
				if ( ! $ticket->id || ! $ticket->is_active || ! WPSC_Individual_Ticket::has_ticket_cap( 'dtt' ) ) {
					continue;
				}

				WPSC_Individual_Ticket::delete_ticket();
			}
			wp_die();
		}

		/**
		 * Check parent filter is deleted or not
		 *
		 * @param string $slug - filter type.
		 * @return boolean
		 */
		public static function has_delete_parent_filter( $slug ) {

			// phpcs:disable
			switch ( $slug ) {

				case 'all':
					return false;
				break;

				case 'unresolved':
					return false;
				break;

				case 'mine':
					return false;
				break;

				case 'closed':
					return false;
				break;

				case 'deleted':
					return true;
				break;

				default:
					if ( ! is_numeric( $slug ) || ! isset( self::$default_filters[ $slug ] ) ) {
						return false;
					}
					$slug = self::$default_filters[ $slug ]['parent-filter'];
					self::has_delete_parent_filter( $slug );
			}
			// phpcs:enable
		}

		/**
		 * Set bulk actions for current user
		 */
		public static function set_bulk_actions() {

			$current_user = WPSC_Current_User::$current_user;
			$is_deleted = self::is_current_filter_deleted();
			$bulk_actions = array();

			if ( ! $is_deleted ) {

				if ( $current_user->is_agent && self::has_ticket_cap( 'cs' ) ) {
					$bulk_actions['change-status'] = array(
						'icon'     => 'gps-navigation',
						'label'    => esc_attr__( 'Change Status', 'supportcandy' ),
						'callback' => 'wpsc_bulk_change_status',
					);
				}

				if ( $current_user->is_agent && self::has_ticket_cap( 'aa' ) ) {
					$bulk_actions['assign-agents'] = array(
						'icon'     => 'headset',
						'label'    => esc_attr__( 'Assign Agents', 'supportcandy' ),
						'callback' => 'wpsc_bulk_assign_agents',
					);
				}

				if ( $current_user->is_agent && self::has_ticket_cap( 'dtt' ) ) {
					$bulk_actions['delete'] = array(
						'icon'     => 'trash-alt',
						'label'    => esc_attr__( 'Delete', 'supportcandy' ),
						'callback' => 'wpsc_bulk_delete_tickets',
					);
				}

				$bulk_actions = apply_filters( 'wpsc_tl_bulk_actions', $bulk_actions );

			} else {

				if ( $current_user->is_agent && self::has_ticket_cap( 'dtt' ) ) {
					$bulk_actions['restore'] = array(
						'icon'     => 'trash-restore',
						'label'    => esc_attr__( 'Restore', 'supportcandy' ),
						'callback' => 'wpsc_bulk_restore_tickets',
					);
				}

				if ( $current_user->is_agent && $current_user->user->has_cap( 'manage_options' ) ) {
					$bulk_actions['delete_permanently'] = array(
						'icon'     => 'trash-alt',
						'label'    => esc_attr__( 'Delete Permanently', 'supportcandy' ),
						'callback' => 'wpsc_bulk_delete_tickets_permanently',
					);
				}

				$bulk_actions = apply_filters( 'wpsc_tl_deleted_bulk_actions', $bulk_actions );
			}

			self::$bulk_actions = $bulk_actions;
		}

		/**
		 * Get Bulk actions
		 *
		 * @return string
		 */
		public static function get_bulk_actions() {

			$current_user = WPSC_Current_User::$current_user;
			$actions_arr = array();
			foreach ( self::$bulk_actions as $action ) :
				ob_start();
				?>
				<div class="wpsc-popover-menu-item" onclick="<?php echo esc_attr( $action['callback'] ) . '(\'' . esc_attr( wp_create_nonce( $action['callback'] ) ) . '\');'; ?>">
					<?php WPSC_Icons::get( $action['icon'] ); ?>
					<span><?php echo esc_attr( $action['label'] ); ?></span>
				</div>
				<?php
				$actions_arr[] = ob_get_clean();
			endforeach;

			return implode( '', $actions_arr );
		}

		/**
		 * Restore bulk tickets
		 *
		 * @return void
		 */
		public static function bulk_restore_tickets() {

			if ( check_ajax_referer( 'wpsc_bulk_restore_tickets', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			$current_user = WPSC_Current_User::$current_user;
			if ( ! $current_user->is_agent ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			$ticket_ids = isset( $_POST['ticket_ids'] ) ? array_filter( array_map( 'intval', $_POST['ticket_ids'] ) ) : array();
			if ( ! $ticket_ids ) {
				wp_send_json_error( 'Something went wrong!', 400 );
			}

			foreach ( $ticket_ids as $ticket_id ) {

				$ticket = new WPSC_Ticket( $ticket_id );
				WPSC_Individual_Ticket::$ticket = $ticket;
				if ( ! $ticket->id || $ticket->is_active || ! WPSC_Individual_Ticket::has_ticket_cap( 'dtt' ) ) {
					continue;
				}

				WPSC_Individual_Ticket::restore_ticket();
			}
			wp_die();
		}

		/**
		 * Delete bulk tickets permanently
		 *
		 * @return void
		 */
		public static function bulk_delete_tickets_permanently() {

			if ( check_ajax_referer( 'wpsc_bulk_delete_tickets_permanently', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			$current_user = WPSC_Current_User::$current_user;
			if ( ! $current_user->is_agent ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			$ticket_ids = isset( $_POST['ticket_ids'] ) ? array_filter( array_map( 'intval', $_POST['ticket_ids'] ) ) : array();
			if ( ! $ticket_ids ) {
				wp_send_json_error( 'Something went wrong!', 400 );
			}

			foreach ( $ticket_ids as $ticket_id ) {

				$ticket = new WPSC_Ticket( $ticket_id );
				WPSC_Individual_Ticket::$ticket = $ticket;
				if ( ! $ticket->id || $ticket->is_active || ! WPSC_Individual_Ticket::has_ticket_cap( 'dtt' ) ) {
					continue;
				}

				WPSC_Individual_Ticket::delete_permanently();
			}
			wp_die();
		}

		/**
		 * Agent ticket access for cap
		 *
		 * @param string $cap - capability type.
		 * @return boolean
		 */
		public static function has_ticket_cap( $cap ) {

			$current_user = WPSC_Current_User::$current_user;
			$flag         = false;
			if (
				$current_user->agent->has_cap( $cap . '-unassigned' ) ||
				$current_user->agent->has_cap( $cap . '-assigned-me' ) ||
				$current_user->agent->has_cap( $cap . '-assigned-others' )
			) {
				$flag = true;
			}

			return apply_filters( 'wpsc_bulk_has_ticket_cap', $flag );
		}

		/**
		 * Agent autocomplete bulk assign agents
		 *
		 * @return void
		 */
		public static function agent_autocomplete_bulk_assign() {

			if ( check_ajax_referer( 'wpsc_agent_autocomplete_bulk_assign', '_ajax_nonce', false ) !== 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			$current_user = WPSC_Current_User::$current_user;
			if ( ! ( $current_user->is_agent && self::has_ticket_cap( 'aa' ) ) ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			$filters = array();

			$filters['term']       = isset( $_GET['q'] ) ? sanitize_text_field( wp_unslash( $_GET['q'] ) ) : '';
			$filters['filter_by']  = isset( $_GET['filter_by'] ) ? sanitize_text_field( wp_unslash( $_GET['filter_by'] ) ) : 'all';
			$filters['sort_by']    = isset( $_GET['sort_by'] ) ? sanitize_text_field( wp_unslash( $_GET['sort_by'] ) ) : 'name';
			$filters['isMultiple'] = isset( $_GET['isMultiple'] ) ? intval( wp_unslash( $_GET['isMultiple'] ) ) : 0;

			$filters['isAgentgroup'] = 0;
			if ( class_exists( 'WPSC_Agentgroups' ) ) {
				$filters['isAgentgroup'] = isset( $_GET['isAgentgroup'] ) ? intval( $_GET['isAgentgroup'] ) : null;
			}

			$response = WPSC_Agent::agent_autocomplete( $filters );
			wp_send_json( $response );
		}

		/**
		 * Check whether current filter is deleted type or not
		 *
		 * @return boolean
		 */
		public static function is_current_filter_deleted() {

			$filters = self::$filters;

			$slug = self::$default_flag ? self::$filter_id : '';
			if ( ! $slug && self::$saved_flag ) {
				$slug = self::$filter_id;
			}
			if ( ! $slug ) {
				$slug = $filters['filterSlug'];
			}

			$parent_slug = is_numeric( $slug ) && self::$default_flag ? self::$default_filters[ $slug ]['parent-filter'] : '';

			if ( ! $parent_slug && is_numeric( $slug ) && self::$saved_flag ) {

				$parent_slug = self::$saved_filters[ $slug ]['parent-filter'];

			} elseif ( ! $parent_slug && $slug == 'custom' ) {

				$parent_slug = $filters['parent-filter'];

			} else {

				$parent_slug = $slug;
			}

			return self::has_delete_parent_filter( $parent_slug );
		}

		/**
		 * Get updated nonce.
		 *
		 * @return void
		 */
		public static function get_nonce() {

			$response = array(
				'general' => wp_create_nonce( 'general' ),
			);
			wp_send_json( $response );
		}

		/**
		 * Allow only allowed conditions for current user for custom ticket filter
		 *
		 * @param array $conditions - conditions to filter.
		 * @return array
		 */
		public static function custom_filter_conditions( $conditions ) {

			$current_user = WPSC_Current_User::$current_user;
			$level = $current_user->level == 'admin' ? 'agent' : $current_user->level;

			foreach ( $conditions as $slug => $item ) {

				if ( $item['type'] == 'cf' ) {

					$cf = WPSC_Custom_Field::get_cf_by_slug( $slug );
					if (
						! $cf->type::$is_filter ||
						! in_array( $cf->field, array( 'ticket', 'customer', 'agentonly' ) ) ||
						! in_array( $level, $item['levels'] )
					) {
						unset( $conditions[ $slug ] );
					}
				} else { // not custom field type.

					unset( $conditions[ $slug ] );
				}
			}

			return $conditions;
		}

		/**
		 * Filter tickets by tag
		 *
		 * @return void
		 */
		public static function apply_tag_custom_filter() {

			if ( isset( $_REQUEST['wpsc_tag'] ) && ( isset($_REQUEST['section']) && $_REQUEST['section'] == 'ticket-list' ) ) { // phpcs:ignore

				$tag = intval( $_REQUEST['wpsc_tag'] ); // phpcs:ignore
				if ( ! $tag ) {
					return;
				}

				if ( ! WPSC_Functions::is_site_admin() ) {
					return;
				}

				$custom_filters = array();

				$obj = new stdClass();
				$obj->slug = 'tags';
				$obj->operator = '=';
				$obj->operand_val_1 = $_REQUEST['wpsc_tag']; // phpcs:ignore

				$custom_filters[] = array( $obj );

				$filters = array(
					'filterSlug'    => 'custom',
					'parent-filter' => 'all',
					'filters'       => wp_json_encode( $custom_filters ),
					'orderby'       => 'date_updated',
					'order'         => 'DESC',
					'page_no'       => 1,
					'search'        => '',
				);

				setcookie( 'wpsc-tl-filters', wp_json_encode( $filters ), time() + 3600 );
			}
		}
	}
endif;

WPSC_Ticket_List::init();
