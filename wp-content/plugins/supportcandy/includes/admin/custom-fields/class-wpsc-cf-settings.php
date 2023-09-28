<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_CF_Settings' ) ) :

	final class WPSC_CF_Settings {

		/**
		 * Set if current screen is settings page
		 *
		 * @var boolean
		 */
		public static $is_current_page;

		/**
		 * Sections for this view
		 *
		 * @var array
		 */
		private static $sections;

		/**
		 * Current section to load
		 *
		 * @var string
		 */
		public static $current_section;

		/**
		 * Custom field category (e.g. ticket, agentonly, customer, etc.)
		 *
		 * @var array
		 */
		public static $fields = array();

		/**
		 * Allowed modules for custom field categories
		 *
		 * @var array
		 */
		public static $allowed_modules = array();

		/**
		 * Initialize this class
		 */
		public static function init() {

			// Load sections for this screen.
			add_action( 'admin_init', array( __CLASS__, 'load_sections' ), 1 );

			// Humbargar modal.
			add_action( 'admin_footer', array( __CLASS__, 'humbargar_menu' ) );

			// Footer scripts.
			add_action( 'admin_footer', array( __CLASS__, 'footer_scripts' ) );

			// Add current section to admin localization data.
			add_filter( 'wpsc_admin_localizations', array( __CLASS__, 'localizations' ) );

			// Register ready function.
			add_action( 'wpsc_js_ready', array( __CLASS__, 'register_js_ready_function' ) );

			// Register fields.
			add_action( 'init', array( __CLASS__, 'register_fields' ), 10 );

			// Register allowed modules.
			add_action( 'init', array( __CLASS__, 'register_allowed_modules' ), 11 );

			// Add new custom field.
			add_action( 'wp_ajax_wpsc_get_add_new_custom_field', array( __CLASS__, 'get_add_new_custom_field' ) );
			add_action( 'wp_ajax_wpsc_get_add_new_custom_field_properties', array( __CLASS__, 'get_add_new_custom_field_properties' ) );
			add_action( 'wp_ajax_wpsc_set_add_new_custom_field', array( __CLASS__, 'set_add_new_custom_field' ) );

			// Edit custom field.
			add_action( 'wp_ajax_wpsc_get_edit_custom_field', array( __CLASS__, 'get_edit_custom_field' ) );
			add_action( 'wp_ajax_wpsc_set_edit_custom_field', array( __CLASS__, 'set_edit_custom_field' ) );

			// Delete.
			add_action( 'wp_ajax_wpsc_delete_custom_field', array( __CLASS__, 'delete_custom_field' ) );
		}

		/**
		 * Load section (nav elements) for this screen
		 *
		 * @return void
		 */
		public static function load_sections() {

			self::$is_current_page = isset( $_REQUEST['page'] ) && wp_unslash( $_REQUEST['page'] ) === 'wpsc-ticket-form' ? true : false; //phpcs:ignore

			if ( ! self::$is_current_page ) {
				return;
			}

			self::$sections = apply_filters(
				'wpsc_ticket_form_page_sections',
				array(
					'ticket-form-fields' => array(
						'slug'     => 'ticket_form_fields',
						'icon'     => 'contact-form',
						'label'    => esc_attr__( 'Ticket Form Fields', 'supportcandy' ),
						'callback' => 'wpsc_get_tff',
					),
					'ticket-fields'      => array(
						'slug'     => 'ticket_fields',
						'icon'     => 'ticket-alt',
						'label'    => esc_attr__( 'Ticket Fields', 'supportcandy' ),
						'callback' => 'wpsc_get_ticket_fields',
					),
					'customer-fields'    => array(
						'slug'     => 'customer_fields',
						'icon'     => 'user-tie',
						'label'    => esc_attr__( 'Customer Fields', 'supportcandy' ),
						'callback' => 'wpsc_get_customer_fields',
					),
					'agent-only-fields'  => array(
						'slug'     => 'agent_only_fields',
						'icon'     => 'headset',
						'label'    => esc_attr__( 'Agent Only Fields', 'supportcandy' ),
						'callback' => 'wpsc_get_agent_only_fields',
					),
				)
			);

			self::$current_section = isset( $_REQUEST['section'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['section'] ) ) : 'ticket-form-fields'; //phpcs:ignore
		}

		/**
		 * Add localizations to local JS
		 *
		 * @param array $localizations - localizations.
		 * @return array
		 */
		public static function localizations( $localizations ) {

			if ( ! self::$is_current_page ) {
				return $localizations;
			}

			// Humbargar Titles.
			$localizations['humbargar_titles'] = self::get_humbargar_titles();

			// Current section.
			$localizations['current_section'] = self::$current_section;

			return $localizations;
		}

		/**
		 * UI foundation for this screen
		 *
		 * @return void
		 */
		public static function layout() {?>

			<div class="wrap">
				<hr class="wp-header-end">
				<div id="wpsc-container" style="display:none;">
					<div class="wpsc-header wpsc-setting-header-xs wpsc-visible-xs">
						<div class="wpsc-humbargar-title">
							<?php WPSC_Icons::get( self::$sections[ self::$current_section ]['icon'] ); ?>
							<label><?php echo esc_attr( self::$sections[ self::$current_section ]['label'] ); ?></label>
						</div>
						<div class="wpsc-humbargar" onclick="wpsc_toggle_humbargar();">
							<?php WPSC_Icons::get( 'bars' ); ?>
						</div>
					</div>
					<div class="wpsc-settings-page">
						<div class="wpsc-setting-section-container wpsc-hidden-xs">
							<h2><?php esc_attr_e( 'Custom Fields', 'supportcandy' ); ?></h2>
							<?php
							foreach ( self::$sections as $key => $section ) {

								$active = self::$current_section === $key ? 'active' : '';
								?>
								<div 
									class="wpsc-setting-nav <?php echo esc_attr( $key ) . ' ' . esc_attr( $active ); ?>"
									onclick="<?php echo esc_attr( $section['callback'] ) . '();'; ?>">
									<?php WPSC_Icons::get( $section['icon'] ); ?>
									<label><?php echo esc_attr( $section['label'] ); ?></label>
								</div>
								<?php
							}
							?>
						</div>
						<div class="wpsc-setting-body"></div>
					</div>
				</div>
			</div>
			<?php

			self::print_snippets();
		}

		/**
		 * Print humbargar menu in footer
		 *
		 * @return void
		 */
		public static function humbargar_menu() {

			if ( ! self::$is_current_page ) {
				return;
			}
			?>

			<div class="wpsc-humbargar-overlay" onclick="wpsc_toggle_humbargar();" style="display:none"></div>
			<div class="wpsc-humbargar-menu" style="display:none">
				<div class="box-inner">
					<div class="wpsc-humbargar-close" onclick="wpsc_toggle_humbargar();">
						<?php WPSC_Icons::get( 'times' ); ?>
					</div>
					<?php
					foreach ( self::$sections as $key => $section ) :

						$active = self::$current_section === $key ? 'active' : '';
						?>
						<div 
							class="wpsc-humbargar-menu-item <?php echo esc_attr( $key ) . ' ' . esc_attr( $active ); ?>"
							onclick="<?php echo esc_attr( $section['callback'] ) . '(true);'; ?>">
							<?php WPSC_Icons::get( $section['icon'] ); ?>
							<label><?php echo esc_attr( $section['label'] ); ?></label>
						</div>
						<?php
					endforeach;
					?>
				</div>
			</div>
			<?php
		}

		/**
		 * Humbargar mobile titles to be used in localizations
		 *
		 * @return array
		 */
		private static function get_humbargar_titles() {

			$titles = array();
			foreach ( self::$sections as $section ) {

				ob_start();
				WPSC_Icons::get( $section['icon'] );
				echo '<label>' . esc_attr( $section['label'] ) . '</label>';
				$titles[ $section['slug'] ] = ob_get_clean();
			}
			return $titles;
		}

		/**
		 * Register JS functions to call on document ready
		 *
		 * @return void
		 */
		public static function register_js_ready_function() {

			if ( ! self::$is_current_page ) {
				return;
			}
			echo esc_attr( self::$sections[ self::$current_section ]['callback'] ) . '();' . PHP_EOL;
		}

		/**
		 * Register fields (ticket, agentonly, customer, etc.)
		 *
		 * @return void
		 */
		public static function register_fields() {

			self::$fields = apply_filters( 'wpsc_custom_field_categories', array() );
		}

		/**
		 * Register allowed modules
		 *
		 * @return void
		 */
		public static function register_allowed_modules() {

			self::$allowed_modules = apply_filters(
				'wpsc_cf_allowed_modules',
				array(

					'ticket-form'   => array( 'ticket', 'customer' ),
					'ticket-list'   => array( 'ticket', 'customer', 'agentonly' ),
					'ticket-filter' => array( 'ticket', 'customer', 'agentonly' ),
					'ticket-macro'  => array( 'ticket', 'customer', 'agentonly' ),
				)
			);
		}

		/**
		 * Add new custom field form
		 *
		 * String $field - custom field (ticket, agentonly, customer, etc.).
		 *
		 * @return void
		 */
		public static function get_add_new_custom_field() {

			if ( check_ajax_referer( 'wpsc_get_add_new_custom_field', '_ajax_nonce', false ) !== 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$field = isset( $_POST['field'] ) ? sanitize_text_field( wp_unslash( $_POST['field'] ) ) : '';
			if ( ! $field ) {
				wp_send_json_error( 'Incorrect request!', 400 );
			}
			?>

			<form action="#" onsubmit="return false;" class="frm-add-new-custom-field">

				<div data-type="textfield" data-required="true" class="wpsc-input-group label">
					<div class="label-container">
						<label for="">
							<?php esc_attr_e( 'Label', 'supportcandy' ); ?> 
							<span class="required-char">*</span>
						</label>
					</div>
					<input name="label" type="text" autocomplete="off"/>
				</div>

				<div data-type="" data-required="true" class="wpsc-input-group load-after">
					<div class="label-container">
						<label for="">
							<?php esc_attr_e( 'Load after', 'supportcandy' ); ?>
						</label>
					</div>
					<select name="load-after" class="load-after">
						<option value="__TOP__">-- <?php esc_attr_e( 'TOP', 'supportcandy' ); ?> --</option>
						<?php
						foreach ( WPSC_Custom_Field::$custom_fields as $cf ) {
							if ( $cf->field != $field ) {
								continue;
							}
							?>
							<option value="<?php echo esc_attr( $cf->slug ); ?>"><?php echo esc_attr( $cf->name ); ?></option>
							<?php
						}
						?>
						<option selected value="__END__">-- <?php esc_attr_e( 'END', 'supportcandy' ); ?> --</option>
					</select>
					<script>jQuery('select.load-after').selectWoo();</script>
				</div>

				<div data-type="single-select" data-required="true" class="wpsc-input-group field-type">
					<div class="label-container">
						<label for="">
							<?php esc_attr_e( 'Select field type', 'supportcandy' ); ?> 
							<span class="required-char">*</span>
						</label>
					</div>
					<select id="wpsc-select-ticket-field" name="type">
						<option value=""></option>
						<?php
						$cf_types = apply_filters( 'wpsc_add_new_custom_field_cf_types', WPSC_Custom_Field::$cf_types, $field );
						foreach ( $cf_types as $slug => $type ) :
							?>
							<option value="<?php echo esc_attr( $slug ); ?>"><?php echo esc_attr( $type['label'] ); ?></option>
							<?php
						endforeach;
						?>
					</select>
					<script>
						jQuery('#wpsc-select-ticket-field').selectWoo({ allowClear: true, placeholder: "" });
						jQuery('#wpsc-select-ticket-field').change(function(){
							// validate custom field type.
							jQuery('div.cft-properties').remove();
							var curEl = jQuery(this);
							var cft = curEl.val();
							if (!cft) return;
							// get custome field properties.
							jQuery(this).prop('disabled', true);
							var form = jQuery('.frm-add-new-custom-field');
							form.append(supportcandy.loader_html);
							var dataform = new FormData(form[0]);
							var data = { 
								action: 'wpsc_get_add_new_custom_field_properties', 
								field: dataform.get('field'),
								cft,
								_ajax_nonce: '<?php echo esc_attr( wp_create_nonce( 'wpsc_get_add_new_custom_field_properties' ) ); ?>'
							};
							jQuery.post(supportcandy.ajax_url, data, function (response) {
								form.children().last().remove();
								form.append(response);
								curEl.prop('disabled', false);
							});
						});
					</script>
				</div>

				<input type="hidden" name="field" value="<?php echo esc_attr( $field ); ?>">
				<input type="hidden" name="action" value="wpsc_set_add_new_custom_field">
				<input type="hidden" name="_ajax_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wpsc_set_add_new_custom_field' ) ); ?>">

			</form>

			<div class="setting-footer-actions">
				<button 
					class="wpsc-button normal primary margin-right"
					onclick="wpsc_set_add_new_custom_field(this);">
					<?php esc_attr_e( 'Submit', 'supportcandy' ); ?></button>
				<button 
					class="wpsc-button normal secondary"
					onclick="jQuery('.wpsc-setting-nav.active').trigger('click');">
					<?php esc_attr_e( 'Cancel', 'supportcandy' ); ?></button>
			</div>
			<?php

			wp_die();
		}

		/**
		 * Add new custom field properties like extra-info, char-limit, etc.
		 *
		 * @return void
		 */
		public static function get_add_new_custom_field_properties() {

			if ( check_ajax_referer( 'wpsc_get_add_new_custom_field_properties', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$field = isset( $_POST['field'] ) ? sanitize_text_field( wp_unslash( $_POST['field'] ) ) : '';
			if ( ! $field ) {
				wp_send_json_error( 'Incorrect request!', 400 );
			}

			$cft = isset( $_POST['cft'] ) ? sanitize_text_field( wp_unslash( $_POST['cft'] ) ) : '';
			if ( ! $cft ) {
				wp_send_json_error( 'Incorrect request!', 400 );
			}

			?>
			<div class="cft-properties">
				<?php
				WPSC_Custom_Field::$cf_types[ $cft ]['class']::get_add_new_custom_field_properties( self::$fields[ $field ] );
				?>
			</div>
			<?php

			wp_die();
		}

		/**
		 * Set add new custom field
		 *
		 * @return void
		 */
		public static function set_add_new_custom_field() {

			if ( check_ajax_referer( 'wpsc_set_add_new_custom_field', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$field = isset( $_POST['field'] ) ? sanitize_text_field( wp_unslash( $_POST['field'] ) ) : '';
			if ( ! $field || ! in_array( $field, array_keys( self::$fields ) ) ) {
				wp_send_json_error( 'Incorrect request!', 400 );
			}

			$cf        = new WPSC_Custom_Field();
			$cf->field = $field;

			$cf->name = isset( $_POST['label'] ) ? sanitize_text_field( wp_unslash( $_POST['label'] ) ) : '';
			if ( ! $cf->name ) {
				wp_send_json_error( 'Incorrect request!', 400 );
			}

			$cf->type = isset( $_POST['type'] ) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : '';
			if ( ! $cf->type ) {
				wp_send_json_error( 'Incorrect request!', 400 );
			}

			$cf->type::set_cf_properties( $cf, self::$fields[ $field ] );

			// set load after.
			$load_after = isset( $_POST['load-after'] ) ? sanitize_text_field( wp_unslash( $_POST['load-after'] ) ) : '__END__';
			if ( $load_after != '__END__' ) {

				$count = 1;

				if ( $load_after == '__TOP__' ) {
					$cf->load_order = $count++;
					$cf->save();
				}

				foreach ( WPSC_Custom_Field::$custom_fields as $cff ) {

					if ( $cff->field != $cf->field || $cff->slug == $cf->slug ) {
						continue;
					}

					$cff->load_order = $count++;
					$cff->save();

					if ( $cff->slug == $load_after ) {
						$cf->load_order = $count++;
						$cf->save();
					}
				}
			}

			wp_die();
		}

		/**
		 * Get edit custom field screen
		 *
		 * @return void
		 */
		public static function get_edit_custom_field() {

			global $wpdb;

			if ( check_ajax_referer( 'wpsc_get_edit_custom_field', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$id = isset( $_POST['cf_id'] ) ? intval( $_POST['cf_id'] ) : 0;
			if ( ! $id ) {
				wp_send_json_error( 'Incorrect request!', 400 );
			}

			$cf = new WPSC_Custom_Field( $id );
			if ( ! $cf->id ) {
				wp_send_json_error( 'Incorrect request!', 400 );
			}
			?>

			<form action="#" onsubmit="return false;" class="frm-edit-custom-field">

				<div data-type="textfield" data-required="true" class="wpsc-input-group label">
					<div class="label-container">
						<label for="">
							<?php esc_attr_e( 'Label', 'supportcandy' ); ?> 
							<span class="required-char">*</span>
						</label>
					</div>
					<input name="label" type="text" value="<?php echo esc_attr( $cf->name ); ?>" autocomplete="off"/>
				</div>

				<div data-type="" data-required="true" class="wpsc-input-group load-after">
					<div class="label-container">
						<label for="">
							<?php esc_attr_e( 'Load after', 'supportcandy' ); ?>
						</label>
					</div>
					<select name="load-after" class="load-after">
						<option value="__TOP__">-- <?php esc_attr_e( 'TOP', 'supportcandy' ); ?> --</option>
						<?php
						$load_after = $wpdb->get_var( "SELECT slug FROM {$wpdb->prefix}psmsc_custom_fields WHERE field='{$cf->field}' AND load_order < {$cf->load_order} ORDER BY load_order DESC LIMIT 1" );
						foreach ( WPSC_Custom_Field::$custom_fields as $cff ) {
							if ( $cff->field != $cf->field || $cff == $cf ) {
								continue;
							}
							?>
							<option <?php selected( $load_after, $cff->slug ); ?> value="<?php echo esc_attr( $cff->slug ); ?>"><?php echo esc_attr( $cff->name ); ?></option>
							<?php
						}
						?>
						<option value="__END__">-- <?php esc_attr_e( 'END', 'supportcandy' ); ?> --</option>
					</select>
					<script>jQuery('select.load-after').selectWoo();</script>
				</div>
				<?php

				if ( ! $cf->type::$is_default ) :
					?>
					<div data-type="textfield" data-required="true" class="wpsc-input-group label">
						<div class="label-container">
							<label for="">
								<?php esc_attr_e( 'Field Type', 'supportcandy' ); ?> 
								<span class="required-char">*</span>
							</label>
						</div>
						<input disabled type="text" value="<?php echo esc_attr( WPSC_Custom_Field::$cf_types[ $cf->type::$slug ]['label'] ); ?>" autocomplete="off"/>
					</div>
					<?php
				endif
				?>

				<?php $cf->type::get_edit_custom_field_properties( $cf, self::$fields[ $cf->field ] ); ?>

				<input type="hidden" name="action" value="wpsc_set_edit_custom_field">
				<input type="hidden" name="cf_id" value="<?php echo esc_attr( $cf->id ); ?>">
				<input type="hidden" name="type" value="<?php echo esc_attr( $cf->type::$slug ); ?>">
				<input type="hidden" name="field" value="<?php echo esc_attr( $cf->field ); ?>">
				<input type="hidden" name="_ajax_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wpsc_set_edit_custom_field' ) ); ?>">

			</form>

			<div class="setting-footer-actions">
				<button 
					class="wpsc-button normal primary margin-right"
					onclick="wpsc_set_edit_custom_field(this);">
					<?php esc_attr_e( 'Submit', 'supportcandy' ); ?></button>
				<button 
					class="wpsc-button normal secondary"
					onclick="jQuery('.wpsc-setting-nav.active').trigger('click');">
					<?php esc_attr_e( 'Cancel', 'supportcandy' ); ?></button>
			</div>
			<?php

			wp_die();
		}

		/**
		 * Set edit custom field
		 *
		 * @return void
		 */
		public static function set_edit_custom_field() {

			global $wpdb;

			if ( check_ajax_referer( 'wpsc_set_edit_custom_field', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$id = isset( $_POST['cf_id'] ) ? intval( $_POST['cf_id'] ) : 0;
			if ( ! $id ) {
				wp_send_json_error( 'Incorrect request!', 400 );
			}

			$cf = new WPSC_Custom_Field( $id );
			if ( ! $cf->id ) {
				wp_send_json_error( 'Incorrect request!', 400 );
			}

			$cf->name = isset( $_POST['label'] ) ? sanitize_text_field( wp_unslash( $_POST['label'] ) ) : '';
			if ( ! $cf->name ) {
				wp_send_json_error( 'Incorrect request!', 400 );
			}

			$cf->type::set_cf_properties( $cf, self::$fields[ $cf->field ] );

			// set load after.
			$load_after = isset( $_POST['load-after'] ) ? sanitize_text_field( wp_unslash( $_POST['load-after'] ) ) : '__END__';
			if ( $load_after != '__END__' ) {

				$count = 1;

				if ( $load_after == '__TOP__' ) {
					$cf->load_order = $count++;
					$cf->save();
				}

				foreach ( WPSC_Custom_Field::$custom_fields as $cff ) {

					if ( $cff->field != $cf->field || $cff->slug == $cf->slug ) {
						continue;
					}

					$cff->load_order = $count++;
					$cff->save();

					if ( $cff->slug == $load_after ) {
						$cf->load_order = $count++;
						$cf->save();
					}
				}
			} else {

				$max_load_order = (int) $wpdb->get_var( "SELECT max(load_order) FROM {$wpdb->prefix}psmsc_custom_fields WHERE field='{$cf->field}'" );
				$cf->load_order = ++$max_load_order;
				$cf->save();
			}

			wp_die();
		}

		/**
		 * Delete custom feild
		 */
		public static function delete_custom_field() {

			if ( check_ajax_referer( 'wpsc_delete_custom_field', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$id = isset( $_POST['cf_id'] ) ? intval( $_POST['cf_id'] ) : 0;
			if ( ! $id ) {
				wp_send_json_error( 'Incorrect request!', 400 );
			}

			$cf = new WPSC_Custom_Field( $id );
			if ( ! $cf->id ) {
				wp_send_json_error( 'Incorrect request!', 400 );
			}

			do_action( 'wpsc_delete_custom_field', $cf );

			WPSC_Custom_Field::destroy( $id );
			wp_die();
		}

		/**
		 * Static snippets for this page
		 *
		 * @return void
		 */
		public static function print_snippets() {
			?>

			<div class="wpsc-snippets">
				<div class="wpsc-add-option">
					<div class="wpsc-option-item">
						<div class="content">
							<div class="wpsc-add-option-container">
								<input type="text" value="" autocomplete="off"/>
								<button onclick="wpsc_add_new_option(this, '<?php echo esc_attr( wp_create_nonce( 'wpsc_add_new_option' ) ); ?>');">
									<?php WPSC_Icons::get( 'check' ); ?>
								</button>
							</div>
							<div class="wpsc-edit-option-container" style="display: none;">
								<input class="edit-option-text" type="text" value="" autocomplete="off" />
								<button onclick="wpsc_set_edit_option(this, '<?php echo esc_attr( wp_create_nonce( 'wpsc_set_edit_option' ) ); ?>');">
									<?php WPSC_Icons::get( 'check' ); ?>
								</button>
								<button class="cancel" onclick="wpsc_edit_option_cancel(this);">
									<?php WPSC_Icons::get( 'times' ); ?>
								</button>
							</div>
							<div class="wpsc-option-listing-container" style="display: none;">
								<span class="sort wpsc-sort-handle"><?php WPSC_Icons::get( 'sort' ); ?></span>
								<div class="text"></div>
								<span class="edit" onclick="wpsc_edit_option(this);"><?php WPSC_Icons::get( 'edit' ); ?></span>
							</div>
						</div>
						<div class="remove-container">
							<span onclick="wpsc_remove_option_item(this)"><?php WPSC_Icons::get( 'times-circle' ); ?></span>
						</div>
					</div>
				</div>
			</div>
			<?php
		}

		/**
		 * Footer scripts (dynamic)
		 *
		 * @return void
		 */
		public static function footer_scripts() {
			?>

			<script>

				/**
				 * Set add new custom field
				 */
				function wpsc_set_add_new_custom_field(el) {

					var form = jQuery('.frm-add-new-custom-field');
					var dataform = new FormData(form[0]);
					var field = dataform.get('field');
					var fieldType = dataform.get('type');

					// general validations
					if (!wpsc_cf_settings_general_validations(form)) return;

					// check whether char-limit honored by default value
					if (
						(field == 'ticket' || field == 'agentonly') &&
						(fieldType == 'cf_textfield' || fieldType == 'cf_textarea' || fieldType == 'cf_number' || 
							fieldType == 'cf_email' || fieldType == 'cf_url')
					) {
						var defaultVal = dataform.get('default_value').trim();
						var char_limit = dataform.get('char_limit');
						if ( defaultVal && char_limit && defaultVal.length > char_limit) {
							alert('<?php esc_attr_e( 'Default value exceeds character limit!', 'supportcandy' ); ?>');
							return;
						}
					}

					var dateRange = dataform.get('date_range');
					if( 
						(field == 'ticket' || field == 'agentonly') &&
						(fieldType == 'cf_date' || fieldType == 'cf_datetime') && dateRange == 'range'){
						var defaultVal = dataform.get('default_value').trim();
						fromDate = new Date(jQuery('#from-date-range').val());
						toDate = new Date(jQuery('#to-date-range').val());
						defaultDate = new Date(defaultVal);
						if(!(Date.parse(defaultDate) <= Date.parse(toDate) && Date.parse(defaultDate) >= Date.parse(fromDate)) ){
							alert('<?php esc_attr_e( 'Default value is not in the range!', 'supportcandy' ); ?>');
							return;
						}
					}

					if (field == 'ticket' && fieldType == 'cf_html') {
						var defaultVal = dataform.get('html_text').trim();
						if(!defaultVal){
							alert(supportcandy.translations.req_fields_missing);
							return;
						}
					}

					<?php do_action( 'wpsc_js_set_add_new_custom_field' ); ?>

					jQuery(el).text(supportcandy.translations.please_wait);
					jQuery.ajax({
						url: supportcandy.ajax_url,
						type: 'POST',
						data: dataform,
						processData: false,
						contentType: false
					}).done(function (res) {
						jQuery('.wpsc-setting-nav.active').trigger('click');
					});
				}

				/**
				 * Set edit custom field
				 */
				function wpsc_set_edit_custom_field(el) {

					var form = jQuery('.frm-edit-custom-field');
					var dataform = new FormData(form[0]);
					var field = dataform.get('field');
					var fieldType = dataform.get('type');

					if ( ( fieldType == 'df_status' || fieldType == 'df_priority' || fieldType == 'df_category' ) && dataform.get('default_value') == null ) {
						alert( supportcandy.translations.req_fields_missing );
						return;
					}

					// general validations
					if (!wpsc_cf_settings_general_validations(form)) return;

					// check whether char-limit honored by default value
					if (
						(field == 'ticket' || field == 'agentonly') &&
						(fieldType == 'cf_textfield' || fieldType == 'cf_textarea' || fieldType == 'cf_number' || 
						fieldType == 'cf_email' || fieldType == 'cf_url')
					) {
						var defaultVal = dataform.get('default_value').trim();
						var char_limit = parseInt(dataform.get('char_limit'));
						if ( defaultVal && char_limit && defaultVal.length > char_limit) {
							alert('<?php esc_attr_e( 'Default value exceeds character limit!', 'supportcandy' ); ?>');
							return;
						}
					}

					var dateRange = dataform.get('date_range');
					if( 
						(field == 'ticket' || field == 'agentonly') &&
						(fieldType == 'cf_date' || fieldType == 'cf_datetime') && dateRange == 'range'){
						var defaultVal = dataform.get('default_value').trim();
						fromDate = new Date(jQuery('#from-date-range').val());
						toDate = new Date(jQuery('#to-date-range').val());
						defaultDate = new Date(defaultVal);
						if(!(Date.parse(defaultDate) <= Date.parse(toDate) && Date.parse(defaultDate) >= Date.parse(fromDate)) ){
							alert('<?php esc_attr_e( 'Default value is not in the range!', 'supportcandy' ); ?>');
							return;
						}
					}

					if (field == 'ticket' && fieldType == 'cf_html') {
						var defaultVal = dataform.get('html_text').trim();
						if(!defaultVal){
							alert(supportcandy.translations.req_fields_missing);
							return;
						}
					}

					<?php do_action( 'wpsc_js_set_edit_custom_field' ); ?>

					jQuery(el).text(supportcandy.translations.please_wait);
					jQuery.ajax({
						url: supportcandy.ajax_url,
						type: 'POST',
						data: dataform,
						processData: false,
						contentType: false
					}).done(function (res) {
						jQuery('.wpsc-setting-nav.active').trigger('click');
					});
				}

				/**
				 * General validations
				 */
				function wpsc_cf_settings_general_validations(form) {

					var flag = true;
					form.find('.wpsc-input-group').each(function(index, element){

						var inputData = jQuery(element).data();
						switch(inputData.type) {

							case 'textfield':
								var fieldValue = jQuery(this).find('input').val().trim();
								if (inputData.required && !fieldValue) {
									alert(supportcandy.translations.req_fields_missing);
									jQuery(this).find('input').focus();
									return flag = false;
								}
								break;

							case 'single-select':
								var fieldValue = jQuery(this).find('select').val();
								if (inputData.required && !fieldValue) {
									alert(supportcandy.translations.req_fields_missing);
									return flag = false;
								}
								break;

							case 'select-options':
								var fieldValue = jQuery(this).find('.wpsc-options-container .option_id').length;
								if (inputData.required && !fieldValue) {
									alert(supportcandy.translations.req_fields_missing);
									return flag = false;
								}
							break;

							case 'number':
								var fieldValue = jQuery(this).find('input').val().trim();
								if (inputData.required && !fieldValue) {
									alert(supportcandy.translations.req_fields_missing);
									return flag = false;
								}
								if (fieldValue && isNaN(fieldValue)) {
									alert('<?php esc_attr_e( 'Incorrect number value!', 'supportcandy' ); ?>');
									jQuery(this).find('input').focus();
									return flag = false;
								}
								break;

							case 'email':
								var fieldValue = jQuery(this).find('input').val().trim();
								if (inputData.required && !fieldValue) {
									alert(supportcandy.translations.req_fields_missing);
									return flag = false;
								}
								if (fieldValue && !validateEmail(fieldValue)) {
									alert('<?php esc_attr_e( 'Incorrect email!', 'supportcandy' ); ?>');
									jQuery(this).find('input').focus();
									return flag = false;
								}
								break;

							case 'url':
								var fieldValue = jQuery(this).find('input').val().trim();
								if (inputData.required && !fieldValue) {
									alert(supportcandy.translations.req_fields_missing);
									return flag = false;
								}
								if (fieldValue && !validateURL(fieldValue)) {
									alert('<?php esc_attr_e( 'Incorrect url!', 'supportcandy' ); ?>');
									jQuery(this).find('input').focus();
									return flag = false;
								}
								break;
						}
					});

					return flag;
				}
			</script>
			<?php
		}
	}
endif;

WPSC_CF_Settings::init();
