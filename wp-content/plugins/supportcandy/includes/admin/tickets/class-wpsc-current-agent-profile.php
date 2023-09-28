<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_Current_Agent_Profile' ) ) :

	final class WPSC_Current_Agent_Profile {

		/**
		 * Initialize this class
		 */
		public static function init() {

			// Get aget profile layout.
			add_action( 'wp_ajax_wpsc_get_agent_profile', array( __CLASS__, 'get_agent_profile' ) );
			add_action( 'wp_ajax_nopriv_wpsc_get_agent_profile', array( __CLASS__, 'get_agent_profile' ) );

			// General settings.
			add_action( 'wp_ajax_wpsc_ap_get_general_settings', array( __CLASS__, 'get_general_settings' ) );
			add_action( 'wp_ajax_nopriv_wpsc_ap_get_general_settings', array( __CLASS__, 'get_general_settings' ) );
			add_action( 'wp_ajax_wpsc_set_agent_settings', array( __CLASS__, 'set_agent_settings' ) );
			add_action( 'wp_ajax_nopriv_wpsc_set_agent_settings', array( __CLASS__, 'set_agent_settings' ) );

			// working hrs.
			add_action( 'wp_ajax_wpsc_ap_get_working_hrs', array( __CLASS__, 'get_working_hrs' ) );
			add_action( 'wp_ajax_nopriv_wpsc_ap_get_working_hrs', array( __CLASS__, 'get_working_hrs' ) );

			// leaves.
			add_action( 'wp_ajax_wpsc_ap_get_leaves', array( __CLASS__, 'get_leaves' ) );
			add_action( 'wp_ajax_nopriv_wpsc_ap_get_leaves', array( __CLASS__, 'get_leaves' ) );
			add_action( 'wp_ajax_wpsc_get_ap_leaves_actions', array( __CLASS__, 'get_leaves_actions' ) );
			add_action( 'wp_ajax_nopriv_wpsc_get_ap_leaves_actions', array( __CLASS__, 'get_leaves_actions' ) );
			add_action( 'wp_ajax_wpsc_set_ap_leaves_actions', array( __CLASS__, 'set_leaves_actions' ) );
			add_action( 'wp_ajax_nopriv_wpsc_set_ap_leaves_actions', array( __CLASS__, 'set_leaves_actions' ) );
		}

		/**
		 * Get current agent profile layout
		 *
		 * @return void
		 */
		public static function get_agent_profile() {

			$current_user = WPSC_Current_User::$current_user;
			if ( ! $current_user->is_agent ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$menu_tabs = array(
				'general' => array(
					'slug'     => 'general',
					'icon'     => 'control',
					'label'    => esc_attr__( 'General Settings', 'supportcandy' ),
					'callback' => 'wpsc_ap_get_general_settings',
				),
			);

			$wh_settings = get_option( 'wpsc-wh-settings' );
			if ( $wh_settings['allow-agent-modify-wh'] ) {
				$menu_tabs['working-hrs'] = array(
					'slug'     => 'working-hrs',
					'icon'     => 'clock',
					'label'    => esc_attr__( 'Working hours', 'supportcandy' ),
					'callback' => 'wpsc_ap_get_working_hrs',
				);
			}
			if ( $wh_settings['allow-agent-modify-leaves'] ) {
				$menu_tabs['leaves'] = array(
					'slug'     => 'leaves',
					'icon'     => 'calendar-times',
					'label'    => esc_attr__( 'Leaves', 'supportcandy' ),
					'callback' => 'wpsc_ap_get_leaves',
				);
			}

			$menu_tabs = apply_filters( 'wpsc_ap_menu_items', $menu_tabs );?>

			<div class="wpsc-ap-mobile-menu wpsc-visible-xs">
				<select>
					<?php
					foreach ( $menu_tabs as $menu ) :
						echo '<option value="' . esc_attr( $menu['slug'] ) . '">' . esc_attr( $menu['label'] ) . '</option>';
					endforeach;
					?>
				</select>
			</div>
			<div class="wpsc-agent-profile">
				<div class="wpsc-ap-menu-container wpsc-hidden-xs">
					<?php
					foreach ( $menu_tabs as $key => $menu ) :
						?>
						<div 
							class="wpsc-ap-nav <?php echo esc_attr( $key ); ?> <?php echo $key === 'general' ? 'active' : ''; ?>"
							onclick="<?php echo esc_attr( $menu['callback'] ) . '();'; ?>">
							<?php WPSC_Icons::get( $menu['icon'] ); ?>
							<label><?php echo esc_attr( $menu['label'] ); ?></label>
						</div>
						<?php
					endforeach;
					?>
				</div>
				<div class="wpsc-ap-menu-body"></div>
			</div>
			<script>
				jQuery('.wpsc-ap-mobile-menu select').change(function(){
					var slug = jQuery(this).val();
					jQuery('.wpsc-ap-nav.'+slug).trigger('click');
				});
				supportcandy.temp.agent_id = <?php echo esc_attr( $current_user->agent->id ); ?>;
			</script>
			<?php
			wp_die();
		}

		/**
		 * General settings
		 *
		 * @return void
		 */
		public static function get_general_settings() {

			$current_user = WPSC_Current_User::$current_user;
			$editor       = $current_user->agent->get_signature_editor();
			$editor       = $editor ? $editor : 'html';
			$rich_editing = get_user_meta( $current_user->user->ID, 'rich_editing', true );
			$rich_editing = filter_var( $rich_editing, FILTER_VALIDATE_BOOLEAN );

			if ( ! $current_user->is_agent ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$default_filters = get_option( 'wpsc-atl-default-filters' );
			$saved_filters   = $current_user->get_saved_filters();
			$default_filter  = $current_user->agent->get_default_filter();
			?>
			<h2 class="wpsc-section-header"><?php esc_attr_e( 'General Settings', 'supportcandy' ); ?></h2>
			<div class="wpsc-section-container">
				<form class="wpsc-agent-settings" onsubmit="return false;" action="#">
					<div class="wpsc-tff wpsc-xs-12 wpsc-sm-12 wpsc-md-12 wpsc-lg-12">
						<div class="wpsc-tff-label">
							<span class="name"><?php esc_attr_e( 'Signature', 'supportcandy' ); ?></span>
						</div>
						<span class="extra-info"><?php esc_attr_e( 'Signature used for emails', 'supportcandy' ); ?></span>
						<div class = "textarea-container">
							<?php
							if ( $rich_editing ) {
								?>
								<div class = "wpsc_tinymce_editor_btns">
									<div class="inner-container">
										<button class="visual wpsc-switch-editor <?php echo esc_attr( $editor ) == 'html' ? 'active' : ''; ?>" type="button" onclick="wpsc_get_tinymce(this, 'signature-html', 'signature_body');"><?php esc_attr_e( 'Visual', 'supportcandy' ); ?></button>
										<button class="text wpsc-switch-editor <?php echo esc_attr( $editor ) == 'text' ? 'active' : ''; ?>" type="button" onclick="wpsc_get_textarea(this, 'signature-html')"><?php esc_attr_e( 'Text', 'supportcandy' ); ?></button>
									</div>
								</div>
								<?php
							}
							?>
							<textarea name="signature-html" id="signature-html" class="wpsc_textarea"><?php echo esc_attr( stripslashes( $current_user->agent->get_signature() ) ); ?></textarea>
						</div>
					</div>
					<div class="wpsc-tff wpsc-xs-12 wpsc-sm-12 wpsc-md-12 wpsc-lg-12">
						<div class="wpsc-tff-label">
							<span class="name"><?php esc_attr_e( 'Default filter', 'supportcandy' ); ?></span>
						</div>
						<span class="extra-info"><?php esc_attr_e( 'Default filter for ticket list', 'supportcandy' ); ?></span>
						<select name="default-filter">
							<optgroup label="<?php esc_attr_e( 'Default filters', 'supportcandy' ); ?>">
								<?php
								foreach ( $default_filters as $index => $filter ) :
									if ( ! $filter['is_enable'] ) {
										continue;
									}
									$selected = $default_filter == $index || $default_filter == 'default-' . $index ? 'selected' : '';
									?>
									<option <?php echo esc_attr( $selected ); ?> value="<?php echo is_numeric( $index ) ? 'default-' . esc_attr( $index ) : esc_attr( $index ); ?>">
										<?php
										$filter_label = $filter['label'] ? WPSC_Translations::get( 'wpsc-atl-' . $index, stripslashes( $filter['label'] ) ) : stripslashes( $filter['label'] );
										echo esc_attr( $filter_label );
										?>
									</option>
									<?php
								endforeach;
								?>
							</optgroup>
							<optgroup label="<?php esc_attr_e( 'Saved filters', 'supportcandy' ); ?>">
								<?php
								foreach ( $saved_filters as $index => $filter ) :
									?>
									<option <?php selected( $default_filter, 'saved-' . $index ); ?> value="saved-<?php echo esc_attr( $index ); ?>"><?php echo esc_attr( $filter['label'] ); ?></option>
									<?php
								endforeach;
								?>
							</optgroup>
						</select>
					</div>
					<div class="wpsc-tff wpsc-xs-12 wpsc-sm-12 wpsc-md-12 wpsc-lg-12">
						<div class="submit-container">
							<button class="wpsc-button normal primary" onclick="wpsc_set_agent_settings(this, '<?php echo esc_attr( wp_create_nonce( 'wpsc_set_agent_settings' ) ); ?>');"><?php esc_attr_e( 'Save Changes', 'supportcandy' ); ?></button>
						</div>
					</div>
					<input type="hidden" name="action" value="wpsc_set_agent_settings">
					<input type="hidden" id="editor" name="editor" value="<?php echo esc_attr( $editor ); ?>">
					<input type="hidden" name="_ajax_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wpsc_set_agent_settings' ) ); ?>">

					<script>
						<?php
						if ( $editor == 'html' && $rich_editing ) :
							?>
							jQuery('.wpsc-switch-editor.visual').trigger('click');
							<?php
						endif;
						?>

						function wpsc_get_tinymce(el, selector, body_id){
							jQuery(el).parent().find('.text').removeClass('active');
							jQuery(el).addClass('active');
							tinymce.remove('#'+selector);
							tinymce.init({ 
								selector:'#'+selector,
								body_id: body_id,
								menubar: false,
								statusbar: false,
								height : '200',
								plugins: [
								'lists link image directionality'
								],
								image_advtab: true,
								toolbar: 'bold italic underline blockquote | alignleft aligncenter alignright | bullist numlist | rtl | link image',
								directionality: '<?php echo is_rtl() ? 'rtl' : 'ltr'; ?>',
								branding: false,
								autoresize_bottom_margin: 20,
								browser_spellcheck : true,
								relative_urls : false,
								remove_script_host : false,
								convert_urls : true,
								setup: function (editor) {
								}
							});
							jQuery('#editor').val('html');
						}
						function wpsc_get_textarea(el, selector){
							jQuery(el).parent().find('.visual').removeClass('active');
							jQuery(el).addClass('active');
							tinymce.remove('#'+selector);
							jQuery('#editor').val('text');
						}
						function wpsc_set_agent_settings(el, nonce) {
							var dataform = new FormData(jQuery('form.wpsc-agent-settings')[0]);
							var is_tinymce = (typeof tinyMCE != "undefined") && tinyMCE.activeEditor && !tinyMCE.activeEditor.isHidden();
							var signature = is_tinymce ? tinyMCE.activeEditor.getContent().trim() : jQuery('#signature-html').val();
							dataform.append('signature', signature);
							dataform.append('_ajax_nonce', nonce);
							jQuery(el).text(supportcandy.translations.please_wait);
							jQuery.ajax({
								url: supportcandy.ajax_url,
								type: 'POST',
								data: dataform,
								processData: false,
								contentType: false,
								error: function (res) {
									jQuery('.wpsc-ap-nav.general').trigger('click');
								},
								success: function (res, textStatus, xhr) {
									jQuery('.wpsc-ap-nav.general').trigger('click');
								}
							});
						}
					</script>
				</form>
			</div>
			<?php
			wp_die();
		}

		/**
		 * Set agent general settings
		 *
		 * @return void
		 */
		public static function set_agent_settings() {

			if ( check_ajax_referer( 'wpsc_set_agent_settings', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			$current_user = WPSC_Current_User::$current_user;
			if ( ! $current_user->is_agent ) {
				wp_send_json_error( new WP_Error( '001', 'Unauthorized' ), 401 );
			}

			$signature = isset( $_POST['signature'] ) ? wp_kses_post( wp_unslash( $_POST['signature'] ) ) : '';

			// replace new line with br if no text editor.
			$editor = isset( $_POST['editor'] ) ? sanitize_text_field( wp_unslash( $_POST['editor'] ) ) : 'html';
			$current_user->agent->set_signature_editor( $editor );
			$current_user->agent->set_signature( $signature );

			$default_filter = isset( $_POST['default-filter'] ) ? sanitize_text_field( wp_unslash( $_POST['default-filter'] ) ) : 'all';
			$current_user->agent->set_default_filter( $default_filter );

			wp_die();
		}

		/**
		 * Working hrs
		 *
		 * @return void
		 */
		public static function get_working_hrs() {

			$current_user = WPSC_Current_User::$current_user;
			if ( ! $current_user->is_agent ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$wh_settings = get_option( 'wpsc-wh-settings' );
			if ( ! $wh_settings['allow-agent-modify-wh'] ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}
			?>

			<div class="wpsc-ap-tab-container">
				<button class="tab working-hrs active" data-slug="working-hrs"><?php echo esc_attr__( 'Working Hours', 'supportcandy' ); ?></button>
				<button class="tab exceptions" data-slug="exceptions"><?php echo esc_attr__( 'Exceptions', 'supportcandy' ); ?></button>
			</div>
			<div class="wpsc-section-container" style="padding: 15px !important;"></div>
			<script>
				// Working hrs tab click
				jQuery('button.tab.working-hrs').click(function(){
					jQuery('button.tab.exceptions').removeClass('active');
					jQuery('button.tab.working-hrs').addClass('active');
					jQuery('.wpsc-section-container').html(supportcandy.loader_html);
					var data = { 
						action: 'wpsc_get_agent_wh_hrs',
						agent_id: supportcandy.temp.agent_id,
						_ajax_nonce: supportcandy.nonce
					};
					jQuery.post(supportcandy.ajax_url, data, function (response) {
						jQuery('.wpsc-section-container').html(response);
						// submit wh
						jQuery('.setting-footer-actions button.primary').attr('onclick', '');
						jQuery('.setting-footer-actions button.primary').click(function(){
							const form = jQuery('.wpsc-frm-agent-wh')[0];
							const dataform = new FormData(form);
							jQuery('.wpsc-section-container').html(supportcandy.loader_html);
							jQuery.ajax({
								url: supportcandy.ajax_url,
								type: 'POST',
								data: dataform,
								processData: false,
								contentType: false
							}).done(function (res) {
								jQuery('button.tab.working-hrs').trigger('click');
							});
						});
					});
				});
				// Exceptions tab click
				jQuery('button.tab.exceptions').click(function(){
					jQuery('button.tab.working-hrs').removeClass('active');
					jQuery('button.tab.exceptions').addClass('active');
					jQuery('.wpsc-section-container').html(supportcandy.loader_html);
					var data = { 
						action: 'wpsc_get_agent_wh_exceptions',
						agent_id: supportcandy.temp.agent_id,
						source: 'agent-profile',
						_ajax_nonce: supportcandy.nonce
					};
					jQuery.post(supportcandy.ajax_url, data, function (response) {
						jQuery('.wpsc-section-container').html(response);
					});
				});
			</script>
			<?php
			wp_die();
		}

		/**
		 * Leaves
		 *
		 * @return void
		 */
		public static function get_leaves() {

			$current_user = WPSC_Current_User::$current_user;
			if ( ! $current_user->is_agent ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$wh_settings = get_option( 'wpsc-wh-settings' );
			if ( ! $wh_settings['allow-agent-modify-leaves'] ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			// get non-recurring holidays.
			$non_recurring_holidays = array();
			$holidays               = WPSC_Holiday::find(
				array(
					'meta_query' => array(
						'relation' => 'AND',
						array(
							'slug'    => 'agent',
							'compare' => '=',
							'val'     => $current_user->agent->id,
						),
						array(
							'slug'    => 'is_recurring',
							'compare' => '=',
							'val'     => 0,
						),
					),
				)
			)['results'];
			foreach ( $holidays as $holiday ) {
				$non_recurring_holidays[] = $holiday->holiday->format( 'Y-m-d' );
			}

			// get recurring holidays.
			$recurring_holidays = array();
			$holidays           = WPSC_Holiday::find(
				array(
					'meta_query' => array(
						'relation' => 'AND',
						array(
							'slug'    => 'agent',
							'compare' => '=',
							'val'     => $current_user->agent->id,
						),
						array(
							'slug'    => 'is_recurring',
							'compare' => '=',
							'val'     => 1,
						),
					),
				)
			)['results'];
			foreach ( $holidays as $holiday ) {
				$recurring_holidays[] = $holiday->holiday->format( 'm-d' );
			}

			$locale = explode( '_', get_locale() );
			?>

			<h2 class="wpsc-section-header"><?php esc_attr_e( 'Leaves', 'supportcandy' ); ?></h2>
			<div class="wpsc-section-container" style="padding: 15px !important;">
				<div class="wpsc-dock-container">
					<?php
					printf(
						/* translators: Click here to see the documentation */
						esc_attr__( '%s to see the documentation!', 'supportcandy' ),
						'<a href="https://supportcandy.net/docs/working-hours/" target="_blank">' . esc_attr__( 'Click here', 'supportcandy' ) . '</a>'
					);
					?>
				</div>
				<div id="wpsc-calendar"></div>
				<script>
					supportcandy.temp.holidayList = {
						'nonRecurring': <?php echo wp_json_encode( $non_recurring_holidays ); ?>,
						'recurring': <?php echo wp_json_encode( $recurring_holidays ); ?>
					};
					var calendarEl = document.getElementById('wpsc-calendar');
					var calendar = new FullCalendar.Calendar(calendarEl, {
						initialView: 'dayGridMonth',
						selectable: true,
						locale: '<?php echo esc_attr( $locale[0] ); ?>',
						dayCellDidMount: function(args) {

							// non-recurring.
							var dateToCompare = args.date.toLocaleDateString('en-CA');
							if (jQuery.inArray(dateToCompare, supportcandy.temp.holidayList.nonRecurring) != -1) {
								jQuery(args.el).css('background-color', '#f0932b');
							}

							// recurring.
							var strArr = dateToCompare.split('-');
							if (jQuery.inArray(strArr[1] + '-' + strArr[2], supportcandy.temp.holidayList.recurring) != -1) {
								jQuery(args.el).css('background-color', '#eb4d4b');
							}

						},
						select: function(info) {

							var start = info.start;
							var end = info.end;
							end.setDate(end.getDate()-1);

							var dateSelected = [];
							do {
								var d = start.toLocaleDateString('en-CA');
								dateSelected.push(d);
								start.setDate(parseInt(start.getDate())+1);
							} while (start <= end);

							wpsc_get_ap_leaves_actions(dateSelected, '<?php echo esc_attr( wp_create_nonce( 'wpsc_get_ap_leaves_actions' ) ); ?>');
						}
					}).render();
				</script>
			</div>
			<?php
			wp_die();
		}

		/**
		 * Load leaves actions for selected dates
		 *
		 * @return void
		 */
		public static function get_leaves_actions() {

			if ( check_ajax_referer( 'wpsc_get_ap_leaves_actions', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			$current_user = WPSC_Current_User::$current_user;
			if ( ! $current_user->is_agent ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$wh_settings = get_option( 'wpsc-wh-settings' );
			if ( ! $wh_settings['allow-agent-modify-leaves'] ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$date_selected = isset( $_POST['dateSelected'] ) ? array_filter( array_map( 'sanitize_text_field', wp_unslash( $_POST['dateSelected'] ) ) ) : array();
			if ( ! $date_selected ) {
				wp_send_json_error( 'Bad Request', 401 );
			}

			$title     = esc_attr__( 'Add/Delete Holidays', 'supportcandy' );
			$unique_id = uniqid( 'wpsc_' );

			ob_start();
			?>
			<form action="#" onsubmit="return false;" class="wpsc-frm-ap-holiday-actions">

				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Action', 'supportcandy' ); ?></label>
					</div>
					<select class="<?php echo esc_attr( $unique_id ); ?>" name="holiday-action">
						<option value="add"><?php esc_attr_e( 'Add new holidays', 'supportcandy' ); ?></option>
						<option value="delete"><?php esc_attr_e( 'Delete existing holidays', 'supportcandy' ); ?></option>
					</select>
				</div>

				<div class="wpsc-input-group is-recurring">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Repeate every year', 'supportcandy' ); ?></label>
					</div>
					<select name="is-recurring">
						<option value="0"><?php esc_attr_e( 'No', 'supportcandy' ); ?></option>
						<option value="1"><?php esc_attr_e( 'Yes', 'supportcandy' ); ?></option>
					</select>
				</div>

				<input type="hidden" name="action" value="wpsc_set_ap_leaves_actions">
				<input type="hidden" name="_ajax_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wpsc_set_ap_leaves_actions' ) ); ?>">

			</form>
			<script>
				jQuery('.<?php echo esc_attr( $unique_id ); ?>').change(function(){
					if (jQuery(this).val() == 'add') {
						jQuery('.wpsc-input-group.is-recurring').show();
					} else {
						jQuery('.wpsc-input-group.is-recurring').hide();
					}
				});
			</script>
			<?php
			$body = ob_get_clean();

			ob_start();
			?>
			<button class="wpsc-button small primary" onclick="wpsc_set_ap_leaves_actions(this);">
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
		 * Set leaves actions
		 *
		 * @return void
		 */
		public static function set_leaves_actions() {

			if ( check_ajax_referer( 'wpsc_set_ap_leaves_actions', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			global $wpdb;

			$current_user = WPSC_Current_User::$current_user;
			if ( ! $current_user->is_agent ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$wh_settings = get_option( 'wpsc-wh-settings' );
			if ( ! $wh_settings['allow-agent-modify-leaves'] ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$date_selected = isset( $_POST['dateSelected'] ) ? sanitize_text_field( wp_unslash( $_POST['dateSelected'] ) ) : '';
			$date_selected = $date_selected ? array_filter( array_map( 'sanitize_text_field', explode( ',', $date_selected ) ) ) : array();
			if ( ! $date_selected ) {
				wp_send_json_error( 'Bad Request', 401 );
			}

			$action = isset( $_POST['holiday-action'] ) ? sanitize_text_field( wp_unslash( $_POST['holiday-action'] ) ) : '';
			if ( ! $action ) {
				wp_send_json_error( 'Bad Request', 401 );
			}

			$is_recurring = isset( $_POST['is-recurring'] ) ? intval( $_POST['is-recurring'] ) : '';
			if ( ! is_numeric( $is_recurring ) ) {
				wp_send_json_error( 'Bad Request', 401 );
			}

			foreach ( $date_selected as $date ) {

				$date = new DateTime( $date . ' 00:00:00' );

				// delete non-recurring record if exists.
				$wpdb->delete(
					$wpdb->prefix . 'psmsc_holidays',
					array(
						'holiday' => $date->format( 'Y-m-d H:i:s' ),
						'agent'   => $current_user->agent->id,
					)
				);

				// delete recurring record if exists.
				$wpdb->query( "DELETE FROM {$wpdb->prefix}psmsc_holidays WHERE agent=" . $current_user->agent->id . ' AND DAYOFMONTH(holiday)=' . $date->format( 'd' ) . ' AND MONTH(holiday)=' . $date->format( 'm' ) . ' AND is_recurring=1' );

				// add record.
				if ( $action == 'add' ) {
					WPSC_Holiday::insert(
						array(
							'agent'        => $current_user->agent->id,
							'holiday'      => $date->format( 'Y-m-d H:i:s' ),
							'is_recurring' => $is_recurring,
						)
					);
				}
			}

			// get non-recurring holidays.
			$non_recurring_holidays = array();
			$holidays               = WPSC_Holiday::find(
				array(
					'meta_query' => array(
						'relation' => 'AND',
						array(
							'slug'    => 'agent',
							'compare' => '=',
							'val'     => $current_user->agent->id,
						),
						array(
							'slug'    => 'is_recurring',
							'compare' => '=',
							'val'     => 0,
						),
					),
				)
			)['results'];
			foreach ( $holidays as $holiday ) {
				$non_recurring_holidays[] = $holiday->holiday->format( 'Y-m-d' );
			}

			// get recurring holidays.
			$recurring_holidays = array();
			$holidays           = WPSC_Holiday::find(
				array(
					'meta_query' => array(
						'relation' => 'AND',
						array(
							'slug'    => 'agent',
							'compare' => '=',
							'val'     => $current_user->agent->id,
						),
						array(
							'slug'    => 'is_recurring',
							'compare' => '=',
							'val'     => 1,
						),
					),
				)
			)['results'];
			foreach ( $holidays as $holiday ) {
				$recurring_holidays[] = $holiday->holiday->format( 'm-d' );
			}

			$response = array(
				'action'       => $action,
				'is_recurring' => $is_recurring,
				'holidayList'  => array(
					'nonRecurring' => $non_recurring_holidays,
					'recurring'    => $recurring_holidays,
				),
			);

			wp_send_json( $response, 200 );
		}
	}
endif;

WPSC_Current_Agent_Profile::init();
