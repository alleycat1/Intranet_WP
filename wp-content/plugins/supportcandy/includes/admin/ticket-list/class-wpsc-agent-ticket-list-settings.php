<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_Agent_Ticket_List_Settings' ) ) :

	final class WPSC_Agent_Ticket_List_Settings {

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
			add_action( 'wp_ajax_wpsc_get_agent_tl_settings', array( __CLASS__, 'get_agent_tl_settings' ) );

			// Agent ticket list items.
			add_action( 'wp_ajax_wpsc_get_agent_tl_items', array( __CLASS__, 'get_agent_tl_items' ) );
			add_action( 'wp_ajax_wpsc_get_add_agent_tl_item', array( __CLASS__, 'get_add_agent_tl_item' ) );
			add_action( 'wp_ajax_wpsc_set_add_agent_tl_item', array( __CLASS__, 'set_add_agent_tl_item' ) );
			add_action( 'wp_ajax_wpsc_delete_agent_tl_item', array( __CLASS__, 'delete_agent_tl_item' ) );
			add_action( 'wp_ajax_wpsc_get_edit_agent_tl_item', array( __CLASS__, 'get_edit_agent_tl_item' ) );
			add_action( 'wp_ajax_wpsc_set_edit_agent_tl_item', array( __CLASS__, 'set_edit_agent_tl_item' ) );

			// Agent ticket list filter items.
			add_action( 'wp_ajax_wpsc_get_agent_filter_items', array( __CLASS__, 'get_agent_filter_items' ) );
			add_action( 'wp_ajax_wpsc_get_add_atl_filter_item', array( __CLASS__, 'get_add_atl_filter_item' ) );
			add_action( 'wp_ajax_wpsc_set_add_atl_filter_item', array( __CLASS__, 'set_add_atl_filter_item' ) );
			add_action( 'wp_ajax_wpsc_delete_atl_filter_item', array( __CLASS__, 'delete_atl_filter_item' ) );
			add_action( 'wp_ajax_wpsc_get_edit_agent_filter_item', array( __CLASS__, 'get_edit_agent_filter_item' ) );
			add_action( 'wp_ajax_wpsc_set_edit_agent_filter_item', array( __CLASS__, 'set_edit_agent_filter_item' ) );

			// Agent default filters.
			add_action( 'wp_ajax_wpsc_get_atl_default_filters', array( __CLASS__, 'get_atl_default_filters' ) );
			add_action( 'wp_ajax_wpsc_get_add_atl_default_filter', array( __CLASS__, 'get_add_atl_default_filter' ) );
			add_action( 'wp_ajax_wpsc_set_add_atl_default_filter', array( __CLASS__, 'set_add_atl_default_filter' ) );
			add_action( 'wp_ajax_wpsc_get_edit_atl_default_filter', array( __CLASS__, 'get_edit_atl_default_filter' ) );
			add_action( 'wp_ajax_wpsc_set_edit_atl_default_filter', array( __CLASS__, 'set_edit_atl_default_filter' ) );
			add_action( 'wp_ajax_wpsc_delete_atl_default_filter', array( __CLASS__, 'delete_atl_default_filter' ) );
			add_action( 'wp_ajax_wpsc_sort_atl_default_filters', array( __CLASS__, 'sort_atl_default_filters' ) );

			// Delete custom field actions.
			add_action( 'wpsc_delete_custom_field', array( __CLASS__, 'delete_custom_field' ), 10, 1 );
		}

		/**
		 * Load tabs for this section
		 */
		public static function load_tabs() {

			self::$tabs = apply_filters(
				'wpsc_ticket_list_tabs',
				array(
					'list-items'      => array(
						'slug'     => 'list_items',
						'label'    => esc_attr__( 'List items', 'supportcandy' ),
						'callback' => 'wpsc_get_agent_tl_items',
					),
					'filter-items'    => array(
						'slug'     => 'filter_items',
						'label'    => esc_attr__( 'Filter items', 'supportcandy' ),
						'callback' => 'wpsc_get_agent_filter_items',
					),
					'default-filters' => array(
						'slug'     => 'default_filters',
						'label'    => esc_attr__( 'Default filters', 'supportcandy' ),
						'callback' => 'wpsc_get_atl_default_filters',
					),
				)
			);

			self::$current_tab = isset( $_REQUEST['tab'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['tab'] ) ) : 'list-items'; // phpcs:ignore
		}

		/**
		 * Add localizations to local JS
		 *
		 * @param array $localizations - localization list.
		 * @return array
		 */
		public static function localizations( $localizations ) {

			if ( ! ( WPSC_Ticket_List_Settings::$is_current_page && WPSC_Ticket_List_Settings::$current_section === 'agent-ticket-list' ) ) {
				return $localizations;
			}

			// Current section.
			$localizations['current_tab'] = self::$current_tab;

			return $localizations;
		}

		/**
		 * Agent ticket list settings
		 *
		 * @return void
		 */
		public static function get_agent_tl_settings() {

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

		/**
		 * Get agent ticket list items
		 */
		public static function get_agent_tl_items() {

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}
			$list_items = get_option( 'wpsc-atl-list-items', array() );
			?>

			<div class="wpsc-dock-container">
				<?php
				printf(
					/* translators: Click here to see the documentation */
					esc_attr__( '%s to see the documentation!', 'supportcandy' ),
					'<a href="https://supportcandy.net/docs/ticket-list-items/" target="_blank">' . esc_attr__( 'Click here', 'supportcandy' ) . '</a>'
				);
				?>
			</div>
			<table class="wpsc-atl wpsc-setting-tbl">
				<thead>
					<tr>
						<th><?php esc_attr_e( 'Field', 'supportcandy' ); ?></th>
						<th><?php esc_attr_e( 'Actions', 'supportcandy' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach ( $list_items as $slug ) {

						$cf = WPSC_Custom_Field::get_cf_by_slug( $slug );
						if ( ! $cf ) {
							continue;
						}
						?>
						<tr>
							<td><?php echo esc_attr( $cf->name ); ?></td>
							<td>
								<a href="javascript:wpsc_get_edit_agent_tl_item('<?php echo esc_attr( $slug ); ?>', '<?php echo esc_attr( wp_create_nonce( 'wpsc_get_edit_agent_tl_item' ) ); ?>');" class="wpsc-link"><?php esc_attr_e( 'Edit', 'supportcandy' ); ?></a> |
								<a href="javascript:wpsc_delete_agent_tl_item('<?php echo esc_attr( $slug ); ?>', '<?php echo esc_attr( wp_create_nonce( 'wpsc_delete_agent_tl_item' ) ); ?>');" class="wpsc-link"><?php esc_attr_e( 'Delete', 'supportcandy' ); ?></a>
							</td>
						</tr>
						<?php
					}
					?>
				</tbody>
			</table>
			<script>
				jQuery('table.wpsc-atl').DataTable({
					ordering: false,
					pageLength: 20,
					bLengthChange: false,
					columnDefs: [ 
						{ targets: -1, searchable: false },
						{ targets: '_all', className: 'dt-left' }
					],
					dom: 'Bfrtip',
					buttons: [
						{
							text: '<?php esc_attr_e( 'Add new', 'supportcandy' ); ?>',
							className: 'wpsc-button small primary',
							action: function ( e, dt, node, config ) {
								wpsc_show_modal();
								var data = { action: 'wpsc_get_add_agent_tl_item' };
								jQuery.post(
									supportcandy.ajax_url,
									data,
									function (response) {

										// Set to modal.
										jQuery( '.wpsc-modal-header' ).text( response.title );
										jQuery( '.wpsc-modal-body' ).html( response.body );
										jQuery( '.wpsc-modal-footer' ).html( response.footer );
										// Display modal.
										wpsc_show_modal_inner_container();
									}
								);
							}
						}
					],
					language: supportcandy.translations.datatables
				});
			</script>
			<?php
			wp_die();
		}

		/**
		 * Get add agent ticket list items modal UI
		 *
		 * @return void
		 */
		public static function get_add_agent_tl_item() {

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$title         = esc_attr__( 'Add new list item', 'supportcandy' );
			$custom_fields = WPSC_Custom_Field::$custom_fields;
			$list_items    = get_option( 'wpsc-atl-list-items', array() );

			ob_start();
			?>
			<form action="#" onsubmit="return false;" class="frm-add-agent-tl-items">
				<div class="wpsc-input-group field-type">
					<div class="label-container">
						<label for="">
							<?php esc_attr_e( 'Select fields', 'supportcandy' ); ?> 
							<span class="required-char">*</span>
						</label>
					</div>
					<select multiple id="wpsc-select-agent-lt-items" name="cf_id[]">
						<?php
						foreach ( $custom_fields as $cf ) {

							if (
								class_exists( $cf->type ) &&
								in_array( $cf->field, WPSC_CF_Settings::$allowed_modules['ticket-list'] ) &&
								$cf->type::$is_list &&
								! in_array( $cf->slug, $list_items )
							) {
								?>
								<option value="<?php echo esc_attr( $cf->id ); ?>"><?php echo esc_attr( $cf->name ); ?></option>
								<?php
							}
						}
						?>
					</select>
					<script>
						jQuery('#wpsc-select-agent-lt-items').selectWoo({
							allowClear: false,
							placeholder: ""
						});
					</script>
				</div>
				<input type="hidden" name="action" value="wpsc_set_add_agent_tl_item">
				<input type="hidden" name="_ajax_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wpsc_set_add_agent_tl_item' ) ); ?>">
			</form>
			<?php
			$body = ob_get_clean();

			ob_start();
			?>
			<button class="wpsc-button small primary" onclick="wpsc_set_add_agent_tl_item(this);">
				<?php esc_attr_e( 'Submit', 'supportcandy' ); ?>
			</button>
			<button class="wpsc-button small secondary" onclick="wpsc_close_modal();">
				<?php esc_attr_e( 'Cancel', 'supportcandy' ); ?>
			</button>
			<?php
			do_action( 'wpsc_get_add_agent_tl_item' );
			$footer = ob_get_clean();

			$response = array(
				'title'  => $title,
				'body'   => $body,
				'footer' => $footer,
			);
			wp_send_json( $response );
		}

		/**
		 * Set add new agent ticket list item
		 *
		 * @return void
		 */
		public static function set_add_agent_tl_item() {

			if ( check_ajax_referer( 'wpsc_set_add_agent_tl_item', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$ids = isset( $_POST['cf_id'] ) ? array_filter( array_map( 'intval', $_POST['cf_id'] ) ) : array();
			if ( ! $ids ) {
				wp_send_json_error( 'Bad request', 400 );
			}

			$list_items = get_option( 'wpsc-atl-list-items', array() );
			foreach ( $ids as $id ) {
				$cf = new WPSC_Custom_Field( $id );
				if ( ! $cf->id || ! $cf->type::$is_list ) {
					continue;
				}
				if ( ! in_array( $cf->slug, $list_items ) ) {
					$list_items[] = $cf->slug;
				}
			}
			update_option( 'wpsc-atl-list-items', $list_items );
			wp_die();
		}

		/**
		 * Delete agent ticket list items
		 *
		 * @return void
		 */
		public static function delete_agent_tl_item() {

			if ( check_ajax_referer( 'wpsc_delete_agent_tl_item', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$slug = isset( $_POST['slug'] ) ? sanitize_text_field( wp_unslash( $_POST['slug'] ) ) : 0;
			if ( ! $slug ) {
				wp_send_json_error( 'Bad request', 400 );
			}

			$cf = WPSC_Custom_Field::get_cf_by_slug( $slug );
			if ( ! $cf ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$list_items = get_option( 'wpsc-atl-list-items', array() );

			$key = array_search( $slug, $list_items );
			if ( $key !== false ) {
				unset( $list_items[ $key ] );
				$list_items = array_values( $list_items );
				update_option( 'wpsc-atl-list-items', $list_items );
				do_action( 'wpsc_delete_agent_tl_item', $slug );
			}

			wp_die();
		}

		/**
		 * Get default filter ajax callback
		 *
		 * @return void
		 */
		public static function get_atl_default_filters() {

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}
			$default_filters = get_option( 'wpsc-atl-default-filters', array() );
			?>

			<div class="wpsc-dock-container">
				<?php
				printf(
					/* translators: Click here to see the documentation */
					esc_attr__( '%s to see the documentation!', 'supportcandy' ),
					'<a href="https://supportcandy.net/docs/default-filters/" target="_blank">' . esc_attr__( 'Click here', 'supportcandy' ) . '</a>'
				);
				?>
			</div>
			<div class="wpsc-setting-cards-container ui-sortable">
				<?php
				foreach ( $default_filters as $key => $filter ) {
					$style = ! $filter['is_enable'] ? 'background-color:#eec7ca;color:#dc2222' : '';
					?>
					<div class="wpsc-setting-card" data-id="<?php echo esc_attr( $key ); ?>" style="<?php echo esc_attr( $style ); ?>" >
						<span class="wpsc-sort-handle action-btn"><?php WPSC_Icons::get( 'sort' ); ?></span>
						<span class="title">
							<?php
							$filter_label = $filter['label'] ? WPSC_Translations::get( 'wpsc-atl-' . $key, stripslashes( $filter['label'] ) ) : stripslashes( $filter['label'] );
							echo esc_attr( $filter_label )
							?>
						</span>
						<div class="actions">
							<span class="action-btn" onclick="wpsc_get_edit_atl_default_filter('<?php echo esc_attr( $key ); ?>', '<?php echo esc_attr( wp_create_nonce( 'wpsc_get_edit_atl_default_filter' ) ); ?>');"><?php WPSC_Icons::get( 'edit' ); ?></span>
							<?php
							if ( is_numeric( $key ) ) {
								?>
								<span class="action-btn" onclick="wpsc_delete_atl_default_filter(<?php echo esc_attr( $key ); ?>, '<?php echo esc_attr( wp_create_nonce( 'wpsc_delete_atl_default_filter' ) ); ?>');"><?php WPSC_Icons::get( 'trash-alt' ); ?></span>
								<?php
							}
							?>
						</div>
					</div>
					<?php
				}
				?>
			</div>
			<div class="setting-footer-actions">
				<button class="wpsc-button normal primary margin-right" onclick="wpsc_get_add_atl_default_filter();"><?php esc_attr_e( 'Add new', 'supportcandy' ); ?></button>
				<button class="wpsc-button normal secondary wpsc-save-sort-order"><?php esc_attr_e( 'Save Order', 'supportcandy' ); ?></button>
			</div>
			<script>
				var items = jQuery( ".wpsc-setting-cards-container" ).sortable({ handle: '.wpsc-sort-handle' });
				jQuery(".wpsc-save-sort-order").click(function(){
					var slugs = items.sortable( "toArray", {attribute: 'data-id'} );
					jQuery('.wpsc-setting-section-body').html(supportcandy.loader_html);
					var data = { action: 'wpsc_sort_atl_default_filters', slugs, _ajax_nonce: '<?php echo esc_attr( wp_create_nonce( 'wpsc_sort_atl_default_filters' ) ); ?>'};
					jQuery.post(supportcandy.ajax_url, data, function (response) {
						wpsc_get_atl_default_filters();
					});
				});
			</script>
			<?php
			wp_die();
		}

		/**
		 * Get add filter modal
		 *
		 * @return void
		 */
		public static function get_add_atl_default_filter() {

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$title           = esc_attr__( 'Add new default filter', 'supportcandy' );
			$custom_fields   = WPSC_Custom_Field::$custom_fields;
			$default_filters = get_option( 'wpsc-atl-default-filters' );
			$more_settings   = get_option( 'wpsc-tl-ms-agent-view' );

			ob_start();
			?>
			<form action="#" onsubmit="return false;" class="wpsc-frm-add-atl-default-filter">
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for="">
							<?php esc_attr_e( 'Label', 'supportcandy' ); ?>
							<span class="required-char">*</span>
						</label>
					</div>
					<input id="wpsc-atl-df-label" type="text" name="label" autocomplete="off"/>
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
						foreach ( $default_filters as $slug => $filter ) {
							?>
							<option value="<?php echo esc_attr( $slug ); ?>"><?php echo esc_attr( $filter['label'] ); ?></option>
							<?php
						}
						?>
					</select>
				</div>
				<?php WPSC_Ticket_Conditions::print( 'default_filters', 'wpsc_default_filter_conditions', '', true, __( 'Filters', 'supportcandy' ) ); ?>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for="">
							<?php esc_attr_e( 'Sort by', 'supportcandy' ); ?>
							<span class="required-char">*</span>
						</label>
					</div>
					<select name="sort-by">
						<?php
						foreach ( $custom_fields as $cf ) {
							if ( $cf->type::$is_sort ) {
								?>
								<option <?php selected( $more_settings['default-sort-by'], $cf->slug ); ?> value="<?php echo esc_attr( $cf->slug ); ?>"><?php echo esc_attr( $cf->name ); ?></option>
								<?php
							}
						}
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
						<option <?php selected( $more_settings['default-sort-order'], 'ASC' ); ?> value="ASC"><?php esc_attr_e( 'ASC', 'supportcandy' ); ?></option>
						<option <?php selected( $more_settings['default-sort-order'], 'DESC' ); ?> value="DESC"><?php esc_attr_e( 'DESC', 'supportcandy' ); ?></option>
					</select>
				</div>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Enable', 'supportcandy' ); ?></label>
					</div>
					<select name="is_enable">
						<option value="1"><?php esc_attr_e( 'Yes', 'supportcandy' ); ?></option>
						<option value="0"><?php esc_attr_e( 'No', 'supportcandy' ); ?></option>
					</select>
				</div>
				<input type="hidden" name="action" value="wpsc_set_add_atl_default_filter" />
				<input type="hidden" name="_ajax_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wpsc_set_add_atl_default_filter' ) ); ?>">
			</form>
			<?php
			$body = ob_get_clean();

			ob_start();
			?>
			<button class="wpsc-button small primary" onclick="wpsc_set_add_atl_default_filter(this);">
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
		 * Add default agent default filter
		 *
		 * @return void
		 */
		public static function set_add_atl_default_filter() {

			if ( check_ajax_referer( 'wpsc_set_add_atl_default_filter', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
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
			if ( ! $filters || $filters == '[]' || ! WPSC_Ticket_Conditions::is_valid_input_conditions( 'wpsc_default_filter_conditions', $filters ) ) {
				wp_send_json_error( 'Bad request', 400 );
			}

			$sort_by = isset( $_POST['sort-by'] ) ? sanitize_text_field( wp_unslash( $_POST['sort-by'] ) ) : '';
			if ( ! $sort_by ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$sort_order = isset( $_POST['sort-order'] ) ? sanitize_text_field( wp_unslash( $_POST['sort-order'] ) ) : '';
			if ( ! $sort_order ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$enable = isset( $_POST['is_enable'] ) ? intval( $_POST['is_enable'] ) : 0;

			$atl_filters           = get_option( 'wpsc-atl-default-filters' );
			$index                 = WPSC_Functions::get_tl_df_auto_increament();
			$atl_filters[ $index ] = array(
				'label'         => $label,
				'parent-filter' => $parent_filter,
				'filters'       => $filters,
				'sort-by'       => $sort_by,
				'sort-order'    => $sort_order,
				'is_enable'     => $enable,
			);
			update_option( 'wpsc-atl-default-filters', $atl_filters );

			// add string translations.
			WPSC_Translations::add( 'wpsc-atl-' . $index, $label );

			wp_die();
		}

		/**
		 * Get edit filter modal
		 *
		 * @return void
		 */
		public static function get_edit_atl_default_filter() {

			if ( check_ajax_referer( 'wpsc_get_edit_atl_default_filter', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$atl_filters = get_option( 'wpsc-atl-default-filters' );

			$fslug = isset( $_POST['slug'] ) ? sanitize_text_field( wp_unslash( $_POST['slug'] ) ) : 0;
			if ( ! $fslug || ! isset( $atl_filters[ $fslug ] ) ) {
				wp_die();
			}

			$atl_filter = $atl_filters[ $fslug ];

			$flag = is_numeric( $fslug ) ? true : false;

			$title         = esc_attr__( 'Edit default filter', 'supportcandy' );
			$custom_fields = WPSC_Custom_Field::$custom_fields;

			ob_start();
			?>
			<form action="#" onsubmit="return false;" class="wpsc-frm-edit-atl-default-filter">
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for="">
							<?php esc_attr_e( 'Label', 'supportcandy' ); ?>
							<span class="required-char">*</span>
						</label>
					</div>
					<input id="wpsc-atl-df-label" type="text" name="label" value="<?php echo esc_attr( $atl_filter['label'] ); ?>" autocomplete="off"/>
				</div>
				<?php
				if ( $flag ) {
					?>
					<div class="wpsc-input-group">
						<div class="label-container">
							<label for="">
								<?php esc_attr_e( 'Parent filter', 'supportcandy' ); ?>
								<span class="required-char">*</span>
							</label>
						</div>
						<select name="parent-filter">
							<?php
							foreach ( $atl_filters as $slug => $filter ) {
								if ( $slug == $fslug ) {
									continue;
								}
								?>
								<option <?php selected( $atl_filter['parent-filter'], $slug ); ?> value="<?php echo esc_attr( $slug ); ?>"><?php echo esc_attr( $filter['label'] ); ?></option>
								<?php
							}
							?>
						</select>
					</div>
					<?php WPSC_Ticket_Conditions::print( 'default_filters', 'wpsc_default_filter_conditions', $atl_filter['filters'], true, __( 'Filters', 'supportcandy' ) ); ?>
					<div class="wpsc-input-group">
						<div class="label-container">
							<label for="">
								<?php esc_attr_e( 'Sort by', 'supportcandy' ); ?>
								<span class="required-char">*</span>
							</label>
						</div>
						<select name="sort-by">
							<?php
							foreach ( $custom_fields as $cf ) :
								if ( $cf->type::$is_sort ) :
									?>
									<option <?php selected( $atl_filter['sort-by'], $cf->slug ); ?> value="<?php echo esc_attr( $cf->slug ); ?>"><?php echo esc_attr( $cf->name ); ?></option>
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
							<option <?php selected( $atl_filter['sort-order'], 'ASC' ); ?> value="ASC"><?php esc_attr_e( 'ASC', 'supportcandy' ); ?></option>
							<option <?php selected( $atl_filter['sort-order'], 'DESC' ); ?> value="DESC"><?php esc_attr_e( 'DESC', 'supportcandy' ); ?></option>
						</select>
					</div>
					<?php
				}
				?>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Enable', 'supportcandy' ); ?></label>
					</div>
					<select name="is_enable">
						<option <?php selected( $atl_filter['is_enable'], '1' ); ?> value="1"><?php esc_attr_e( 'Yes', 'supportcandy' ); ?></option>
						<option <?php selected( $atl_filter['is_enable'], '0' ); ?>  value="0"><?php esc_attr_e( 'No', 'supportcandy' ); ?></option>
					</select>
				</div>
				<input type="hidden" name="action" value="wpsc_set_edit_atl_default_filter" />
				<input type="hidden" name="slug" value="<?php echo esc_attr( $fslug ); ?>" />
				<input type="hidden" name="_ajax_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wpsc_set_edit_atl_default_filter' ) ); ?>">
			</form>
			<?php
			$body = ob_get_clean();

			ob_start();
			?>
			<button class="wpsc-button small primary" onclick="wpsc_set_edit_atl_default_filter(this, <?php echo esc_attr( $flag ); ?>);">
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
		 * Set edit agent default filter
		 *
		 * @return void
		 */
		public static function set_edit_atl_default_filter() {

			if ( check_ajax_referer( 'wpsc_set_edit_atl_default_filter', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$atl_filters   = get_option( 'wpsc-atl-default-filters' );
			$more_settings = get_option( 'wpsc-tl-ms-agent-view' );

			$slug = isset( $_POST['slug'] ) ? sanitize_text_field( wp_unslash( $_POST['slug'] ) ) : 0;
			if ( ! $slug || ! isset( $atl_filters[ $slug ] ) ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$label = isset( $_POST['label'] ) ? sanitize_text_field( wp_unslash( $_POST['label'] ) ) : '';
			if ( ! $label ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$enable = isset( $_POST['is_enable'] ) ? intval( $_POST['is_enable'] ) : 0;
			if ( ! $enable && $more_settings['default-filter'] == $slug ) {
				$enable = 1;
			}

			// remove string translations.
			WPSC_Translations::remove( 'wpsc-atl-' . $slug );

			if ( ! is_numeric( $slug ) ) {
				$atl_filters[ $slug ] = array(
					'label'     => $label,
					'is_enable' => $enable,
				);
				update_option( 'wpsc-atl-default-filters', $atl_filters );

				// add string translations if not numaric.
				WPSC_Translations::add( 'wpsc-atl-' . $slug, $label );

				wp_die();
			}

			$parent_filter = isset( $_POST['parent-filter'] ) ? sanitize_text_field( wp_unslash( $_POST['parent-filter'] ) ) : '';
			if ( ! $parent_filter ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$filters = isset( $_POST['filters'] ) ? sanitize_text_field( wp_unslash( $_POST['filters'] ) ) : '';
			if ( ! $filters || $filters == '[]' || ! WPSC_Ticket_Conditions::is_valid_input_conditions( 'wpsc_default_filter_conditions', $filters ) ) {
				wp_send_json_error( 'Bad request', 400 );
			}

			$sort_by = isset( $_POST['sort-by'] ) ? sanitize_text_field( wp_unslash( $_POST['sort-by'] ) ) : '';
			if ( ! $sort_by ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$sort_order = isset( $_POST['sort-order'] ) ? sanitize_text_field( wp_unslash( $_POST['sort-order'] ) ) : '';
			if ( ! $sort_order ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$atl_filters[ $slug ] = array(
				'label'         => $label,
				'parent-filter' => $parent_filter,
				'filters'       => $filters,
				'sort-by'       => $sort_by,
				'sort-order'    => $sort_order,
				'is_enable'     => $enable,
			);
			update_option( 'wpsc-atl-default-filters', $atl_filters );

			// add string translations.
			WPSC_Translations::add( 'wpsc-atl-' . $slug, $label );

			wp_die();
		}

		/**
		 * Delete agent default filter
		 *
		 * @return void
		 */
		public static function delete_atl_default_filter() {

			if ( check_ajax_referer( 'wpsc_delete_atl_default_filter', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 400 );
			}

			$atl_filters = get_option( 'wpsc-atl-default-filters' );

			$slug = isset( $_POST['slug'] ) ? intval( $_POST['slug'] ) : 0;
			if ( ! $slug || ! isset( $atl_filters[ $slug ] ) ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			unset( $atl_filters[ $slug ] );
			update_option( 'wpsc-atl-default-filters', $atl_filters );

			// remove string translations.
			WPSC_Translations::remove( 'wpsc-atl-' . $slug );
			wp_die();
		}

		/**
		 * Sort agent default filters
		 */
		public static function sort_atl_default_filters() {

			if ( check_ajax_referer( 'wpsc_sort_atl_default_filters', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$atl_filters = get_option( 'wpsc-atl-default-filters' );

			$slugs = isset( $_POST['slugs'] ) ? array_filter( array_map( 'sanitize_text_field', wp_unslash( $_POST['slugs'] ) ) ) : array();
			if ( ! $slugs ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$temp_filters = array();
			foreach ( $slugs as $slug ) {

				if ( ! isset( $atl_filters[ $slug ] ) ) {
					wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
				}
				$temp_filters[ $slug ] = $atl_filters[ $slug ];
			}

			$atl_filters_keys = array_keys( $atl_filters );
			// Verifying if slug is present in list item.
			foreach ( $slugs as $slug ) {
				if ( ! in_array( $slug, $atl_filters_keys ) ) {
					wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
				}
			}

			update_option( 'wpsc-atl-default-filters', $temp_filters );
			wp_die();
		}

		/**
		 * Get agent filter items
		 *
		 * @return void
		 */
		public static function get_agent_filter_items() {

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}
			$filter_items = get_option( 'wpsc-atl-filter-items', array() );
			?>

			<div class="wpsc-dock-container">
				<?php
				printf(
					/* translators: Click here to see the documentation */
					esc_attr__( '%s to see the documentation!', 'supportcandy' ),
					'<a href="https://supportcandy.net/docs/ticket-list-filter-items/" target="_blank">' . esc_attr__( 'Click here', 'supportcandy' ) . '</a>'
				);
				?>
			</div>
			<table class="wpsc-afl wpsc-setting-tbl">
				<thead>
					<tr>
						<th><?php esc_attr_e( 'Field', 'supportcandy' ); ?></th>
						<th><?php esc_attr_e( 'Actions', 'supportcandy' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach ( $filter_items as $key => $slug ) {
						$cf = WPSC_Custom_Field::get_cf_by_slug( $slug );
						if ( ! $cf ) {
							continue;
						}
						?>
						<tr>
							<td><?php echo esc_attr( $cf->name ); ?></td>
							<td>
								<a href="javascript:wpsc_get_edit_agent_filter_item('<?php echo esc_attr( $slug ); ?>', '<?php echo esc_attr( wp_create_nonce( 'wpsc_get_edit_agent_filter_item' ) ); ?>');" class="wpsc-link"><?php esc_attr_e( 'Edit', 'supportcandy' ); ?></a> |
								<a href="javascript:wpsc_delete_atl_filter_item('<?php echo esc_attr( $slug ); ?>', '<?php echo esc_attr( wp_create_nonce( 'wpsc_delete_atl_filter_item' ) ); ?>');" class="wpsc-link"><?php esc_attr_e( 'Delete', 'supportcandy' ); ?></a>
							</td>
						</tr>
						<?php
					}
					?>
				</tbody>
			</table>

			<script>
				jQuery('table.wpsc-afl').DataTable({
					ordering: false,
					pageLength: 20,
					bLengthChange: false,
					columnDefs: [ 
						{ targets: -1, searchable: false },
						{ targets: '_all', className: 'dt-left' }
					],
					dom: 'Bfrtip',
					buttons: [
						{
							text: '<?php esc_attr_e( 'Add new', 'supportcandy' ); ?>',
							className: 'wpsc-button small primary',
							action: function ( e, dt, node, config ) {
								wpsc_show_modal();
								var data = { action: 'wpsc_get_add_atl_filter_item' };
								jQuery.post(
									supportcandy.ajax_url,
									data,
									function (response) {

										// Set to modal.
										jQuery( '.wpsc-modal-header' ).text( response.title );
										jQuery( '.wpsc-modal-body' ).html( response.body );
										jQuery( '.wpsc-modal-footer' ).html( response.footer );
										// Display modal.
										wpsc_show_modal_inner_container();
									}
								);
							}
						}
					],
					language: supportcandy.translations.datatables
				});
			</script>
			<?php
			wp_die();
		}

		/**
		 * Add agent ticket list filter items modal UI
		 *
		 * @return void
		 */
		public static function get_add_atl_filter_item() {

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$title         = esc_attr__( 'Add New Filter Item', 'supportcandy' );
			$custom_fields = WPSC_Custom_Field::$custom_fields;
			$filter_items  = get_option( 'wpsc-atl-filter-items', array() );

			ob_start();
			?>
			<form action="#" onsubmit="return false;" class="frm-add-agent-tl-filter-items">
				<div class="wpsc-input-group field-type">
					<div class="label-container">
						<label for="">
							<?php esc_attr_e( 'Select fields', 'supportcandy' ); ?> 
							<span class="required-char">*</span>
						</label>
					</div>
					<select multiple id="wpsc-select-agent-lt-filter-items" name="agent-tl-filter-id[]">
						<?php
						foreach ( $custom_fields as $cf ) :

							if (
								class_exists( $cf->type ) &&
								in_array( $cf->field, WPSC_CF_Settings::$allowed_modules['ticket-filter'] ) &&
								$cf->type::$is_filter &&
								! in_array( $cf->slug, $filter_items )
							) {
								?>
								<option value="<?php echo esc_attr( $cf->id ); ?>"><?php echo esc_attr( $cf->name ); ?></option>
								<?php
							}

						endforeach;
						?>
					</select>
					<script>
						jQuery('#wpsc-select-agent-lt-filter-items').selectWoo({
							allowClear: false,
							placeholder: ""
						});
					</script>
				</div>
				<input type="hidden" name="action" value="wpsc_set_add_atl_filter_item">
				<input type="hidden" name="_ajax_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wpsc_set_add_atl_filter_item' ) ); ?>">
			</form>
			<?php
			$body = ob_get_clean();

			ob_start();
			?>
			<button class="wpsc-button small primary" onclick="wpsc_set_add_atl_filter_item(this);">
				<?php esc_attr_e( 'Submit', 'supportcandy' ); ?>
			</button>
			<button class="wpsc-button small secondary" onclick="wpsc_close_modal();">
				<?php esc_attr_e( 'Cancel', 'supportcandy' ); ?>
			</button>
			<?php
			do_action( 'wpsc_get_add_agent_filter_item' );
			$footer = ob_get_clean();

			$response = array(
				'title'  => $title,
				'body'   => $body,
				'footer' => $footer,
			);
			wp_send_json( $response );
		}

		/**
		 * Set new agent ticket list filter items
		 *
		 * @return void
		 */
		public static function set_add_atl_filter_item() {

			if ( check_ajax_referer( 'wpsc_set_add_atl_filter_item', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$ids = isset( $_POST['agent-tl-filter-id'] ) ? array_filter( array_map( 'intval', $_POST['agent-tl-filter-id'] ) ) : array();
			if ( ! $ids ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$filter_items = get_option( 'wpsc-atl-filter-items', array() );
			foreach ( $ids as $id ) {
				$cf = new WPSC_Custom_Field( $id );
				if ( ! $cf->id || ! $cf->type::$is_filter ) {
					continue;
				}
				if ( ! in_array( $cf->slug, $filter_items ) ) {
					$filter_items[] = $cf->slug;
				}
			}
			update_option( 'wpsc-atl-filter-items', $filter_items );
			wp_die();
		}

		/**
		 * Delete agent ticket list filter items
		 *
		 * @return void
		 */
		public static function delete_atl_filter_item() {

			if ( check_ajax_referer( 'wpsc_delete_atl_filter_item', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$slug = isset( $_POST['slug'] ) ? sanitize_text_field( wp_unslash( $_POST['slug'] ) ) : '';
			if ( ! $slug ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$cf = WPSC_Custom_Field::get_cf_by_slug( $slug );
			if ( ! $cf ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$more_settings = get_option( 'wpsc-tl-ms-agent-view' );
			if ( $more_settings['default-filter'] == $slug ) {
				wp_die();
			}

			$filter_items = get_option( 'wpsc-atl-filter-items' );
			$key          = array_search( $slug, $filter_items );
			if ( $key !== false ) {
				unset( $filter_items[ $key ] );
				$filter_items = array_values( $filter_items );
				update_option( 'wpsc-atl-filter-items', $filter_items );
				do_action( 'wpsc_delete_agent_tl_filter_item', $slug );
			}

			wp_die();
		}

		/**
		 * Remove necessery options if custom field got deleted
		 *
		 * @param WPSC_CF $cf - custom field object.
		 * @return void
		 */
		public static function delete_custom_field( $cf ) {

			$list_items = get_option( 'wpsc-atl-list-items', array() );
			$key        = array_search( $cf->slug, $list_items );
			if ( $key !== false ) {
				unset( $list_items[ $key ] );
				update_option( 'wpsc-atl-list-items', $list_items );
			}

			$filter_items = get_option( 'wpsc-atl-filter-items', array() );
			$key          = array_search( $cf->slug, $filter_items );
			if ( $key !== false ) {
				unset( $filter_items[ $key ] );
				update_option( 'wpsc-atl-filter-items', $filter_items );
			}
		}

		/**
		 * Get edit agent filter list items modal UI
		 *
		 * @return void
		 */
		public static function get_edit_agent_filter_item() {

			if ( check_ajax_referer( 'wpsc_get_edit_agent_filter_item', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$slug = isset( $_POST['slug'] ) ? sanitize_text_field( wp_unslash( $_POST['slug'] ) ) : '';
			if ( ! $slug ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$cf = WPSC_Custom_Field::get_cf_by_slug( $slug );
			if ( ! $cf ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$title        = $cf->name;
			$filter_items = get_option( 'wpsc-atl-filter-items', array() );

			// calculate load order.
			$offset     = array_search( $cf->slug, $filter_items );
			$load_after = $offset == 0 ? '__TOP__' : $filter_items[ $offset - 1 ];

			ob_start();
			?>
			<form action="#" onsubmit="return false;" class="frm-edit-agent-fl-items">
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Load after', 'supportcandy' ); ?></label>
					</div>
					<select name="load-after" class="load-after">
						<option <?php selected( $load_after, '__TOP__', true ); ?> value="__TOP__">-- <?php esc_attr_e( 'TOP', 'supportcandy' ); ?> --</option>
						<?php
						foreach ( $filter_items as $slug ) {
							$cff = WPSC_Custom_Field::get_cf_by_slug( $slug );
							if ( ! $cff || $cff == $cf ) {
								continue;
							}
							?>
							<option <?php selected( $load_after, $cff->slug, true ); ?> value="<?php echo esc_attr( $slug ); ?>"><?php echo esc_attr( $cff->name ); ?></option>
							<?php
						}
						?>
						<option value="__END__">-- <?php esc_attr_e( 'END', 'supportcandy' ); ?> --</option>
					</select>
					<script>jQuery('select.load-after').selectWoo();</script>
				</div>
				<input type="hidden" name="action" value="wpsc_set_edit_agent_filter_item">
				<input type="hidden" name="slug" value="<?php echo esc_attr( $cf->slug ); ?>">
				<input type="hidden" name="_ajax_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wpsc_set_edit_agent_filter_item' ) ); ?>">
			</form>
			<?php
			$body = ob_get_clean();

			ob_start();
			?>
			<button class="wpsc-button small primary" onclick="wpsc_set_edit_agent_filter_item(this);">
				<?php esc_attr_e( 'Submit', 'supportcandy' ); ?>
			</button>
			<button class="wpsc-button small secondary" onclick="wpsc_close_modal();">
				<?php esc_attr_e( 'Cancel', 'supportcandy' ); ?>
			</button>
			<?php
			do_action( 'wpsc_get_edit_agent_tl_item' );
			$footer = ob_get_clean();

			$response = array(
				'title'  => $title,
				'body'   => $body,
				'footer' => $footer,
			);
			wp_send_json( $response );
		}

		/**
		 * Set edit agent filter list item
		 *
		 * @return void
		 */
		public static function set_edit_agent_filter_item() {

			if ( check_ajax_referer( 'wpsc_set_edit_agent_filter_item', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$slug = isset( $_POST['slug'] ) ? sanitize_text_field( wp_unslash( $_POST['slug'] ) ) : '';
			if ( ! $slug ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$cf = WPSC_Custom_Field::get_cf_by_slug( $slug );
			if ( ! $cf ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$load_after = isset( $_POST['load-after'] ) ? sanitize_text_field( wp_unslash( $_POST['load-after'] ) ) : '__END__';

			$filter_items = get_option( 'wpsc-atl-filter-items', array() );

			// unset from list so that load after should work.
			$filter_items = array_values( array_diff( $filter_items, array( $cf->slug ) ) );

			// set load after.
			switch ( $load_after ) {

				case '__TOP__':
					$filter_items = array_merge( array( $cf->slug ), $filter_items );
					break;

				case '__END__':
					$filter_items = array_merge( $filter_items, array( $cf->slug ) );
					break;

				default:
					$load_after = WPSC_Custom_Field::get_cf_by_slug( $load_after );
					if ( ! $load_after || ! in_array( $load_after->slug, $filter_items ) ) {
						wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
					}
					$offset       = array_search( $load_after->slug, $filter_items ) + 1;
					$arr1         = array_slice( $filter_items, 0, $offset );
					$arr2         = array_slice( $filter_items, $offset );
					$filter_items = array_merge( $arr1, array( $cf->slug ), $arr2 );
					break;
			}

			update_option( 'wpsc-atl-filter-items', $filter_items );
			wp_die();
		}

		/**
		 * Get edit agent ticket list items modal UI
		 *
		 * @return void
		 */
		public static function get_edit_agent_tl_item() {

			if ( check_ajax_referer( 'wpsc_get_edit_agent_tl_item', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$slug = isset( $_POST['slug'] ) ? sanitize_text_field( wp_unslash( $_POST['slug'] ) ) : '';
			if ( ! $slug ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$cf = WPSC_Custom_Field::get_cf_by_slug( $slug );
			if ( ! $cf ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$title      = $cf->name;
			$list_items = get_option( 'wpsc-atl-list-items', array() );

			// calculate load order.
			$offset     = array_search( $cf->slug, $list_items );
			$load_after = $offset == 0 ? '__TOP__' : $list_items[ $offset - 1 ];

			ob_start();
			?>
			<form action="#" onsubmit="return false;" class="frm-edit-agent-tl-items">
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Load after', 'supportcandy' ); ?></label>
					</div>
					<select name="load-after" class="load-after">
						<option <?php selected( $load_after, '__TOP__', true ); ?> value="__TOP__">-- <?php esc_attr_e( 'TOP', 'supportcandy' ); ?> --</option>
						<?php
						foreach ( $list_items as $slug ) {
							$cff = WPSC_Custom_Field::get_cf_by_slug( $slug );
							if ( ! $cff || $cff == $cf ) {
								continue;
							}
							?>
							<option <?php selected( $load_after, $cff->slug, true ); ?> value="<?php echo esc_attr( $slug ); ?>"><?php echo esc_attr( $cff->name ); ?></option>
							<?php
						}
						?>
						<option value="__END__">-- <?php esc_attr_e( 'END', 'supportcandy' ); ?> --</option>
					</select>
					<script>jQuery('select.load-after').selectWoo();</script>
				</div>
				<input type="hidden" name="action" value="wpsc_set_edit_agent_tl_item">
				<input type="hidden" name="slug" value="<?php echo esc_attr( $cf->slug ); ?>">
				<input type="hidden" name="_ajax_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wpsc_set_edit_agent_tl_item' ) ); ?>">
			</form>
			<?php
			$body = ob_get_clean();

			ob_start();
			?>
			<button class="wpsc-button small primary" onclick="wpsc_set_edit_agent_tl_item(this);">
				<?php esc_attr_e( 'Submit', 'supportcandy' ); ?>
			</button>
			<button class="wpsc-button small secondary" onclick="wpsc_close_modal();">
				<?php esc_attr_e( 'Cancel', 'supportcandy' ); ?>
			</button>
			<?php
			do_action( 'wpsc_get_edit_agent_tl_item' );
			$footer = ob_get_clean();

			$response = array(
				'title'  => $title,
				'body'   => $body,
				'footer' => $footer,
			);
			wp_send_json( $response );
		}

		/**
		 * Set edit agent ticket list item
		 *
		 * @return void
		 */
		public static function set_edit_agent_tl_item() {

			if ( check_ajax_referer( 'wpsc_set_edit_agent_tl_item', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$slug = isset( $_POST['slug'] ) ? sanitize_text_field( wp_unslash( $_POST['slug'] ) ) : '';
			if ( ! $slug ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$cf = WPSC_Custom_Field::get_cf_by_slug( $slug );
			if ( ! $cf ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$load_after = isset( $_POST['load-after'] ) ? sanitize_text_field( wp_unslash( $_POST['load-after'] ) ) : '__END__';

			$list_items = get_option( 'wpsc-atl-list-items', array() );

			// unset from list so that load after should work.
			$list_items = array_values( array_diff( $list_items, array( $cf->slug ) ) );

			// set load after.
			switch ( $load_after ) {

				case '__TOP__':
					$list_items = array_merge( array( $cf->slug ), $list_items );
					break;

				case '__END__':
					$list_items = array_merge( $list_items, array( $cf->slug ) );
					break;

				default:
					$load_after = WPSC_Custom_Field::get_cf_by_slug( $load_after );
					if ( ! $load_after || ! in_array( $load_after->slug, $list_items ) ) {
						wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
					}
					$offset     = array_search( $load_after->slug, $list_items ) + 1;
					$arr1       = array_slice( $list_items, 0, $offset );
					$arr2       = array_slice( $list_items, $offset );
					$list_items = array_merge( $arr1, array( $cf->slug ), $arr2 );
					break;
			}

			update_option( 'wpsc-atl-list-items', $list_items );
			wp_die();
		}
	}
endif;

WPSC_Agent_Ticket_List_Settings::init();
