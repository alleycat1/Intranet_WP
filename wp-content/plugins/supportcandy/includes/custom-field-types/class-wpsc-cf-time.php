<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_CF_Time' ) ) :

	final class WPSC_CF_Time {

		/**
		 * Slug for this custom field type
		 *
		 * @var string
		 */
		public static $slug = 'cf_time';

		/**
		 * Set whether this custom field type is of type date
		 *
		 * @var boolean
		 */
		public static $is_date = false;

		/**
		 * Set whether this custom field type has applicable to date range
		 *
		 * @var boolean
		 */
		public static $has_date_range = false;

		/**
		 * Set whether this custom field type has multiple values
		 *
		 * @var boolean
		 */
		public static $has_multiple_val = false;

		/**
		 * Data type for column created in tickets table
		 *
		 * @var string
		 */
		public static $data_type = 'TINYTEXT NULL DEFAULT NULL';

		/**
		 * Set whether this custom field type has reference to other class
		 *
		 * @var boolean
		 */
		public static $has_ref = false;

		/**
		 * Reference class for this custom field type so that its value(s) return with object or array of objects automatically. Empty string indicate no reference.
		 *
		 * @var string
		 */
		public static $ref_class = '';

		/**
		 * Set whether this custom field field type is system default (no fields can be created from it).
		 *
		 * @var boolean
		 */
		public static $is_default = false;

		/**
		 * Set whether this field type has extra information that can be used in ticket form, edit custom fields, etc.
		 *
		 * @var boolean
		 */
		public static $has_extra_info = true;

		/**
		 * Set whether this custom field type can accept personal info.
		 *
		 * @var boolean
		 */
		public static $has_personal_info = true;

		/**
		 * Set whether fields created from this custom field type is allowed in create ticket form
		 *
		 * @var boolean
		 */
		public static $is_ctf = true;

		/**
		 * Set whether fields created from this custom field type is allowed in ticket list
		 *
		 * @var boolean
		 */
		public static $is_list = true;

		/**
		 * Set whether fields created from this custom field type is allowed in ticket filter
		 *
		 * @var boolean
		 */
		public static $is_filter = true;

		/**
		 * Set whether fields created from this custom field type can be given character limits
		 *
		 * @var boolean
		 */
		public static $has_char_limit = false;

		/**
		 * Set whether fields created from this custom field type has custom options set in options table
		 *
		 * @var boolean
		 */
		public static $has_options = false;

		/**
		 * Set whether fields created from this custom field type can be auto-filled
		 *
		 * @var boolean
		 */
		public static $is_auto_fill = true;

		/**
		 * Set whether fields created from this custom field type can be available for ticket list sorting
		 *
		 * @var boolean
		 */
		public static $is_sort = true;

		/**
		 * Set whether fields created from this custom field type can have placeholder
		 *
		 * @var boolean
		 */
		public static $is_placeholder = true;

		/**
		 * Set whether fields created from this custom field type is applicable for visibility conditions in create ticket form
		 *
		 * @var boolean
		 */
		public static $is_visibility_conditions = false;

		/**
		 * Set whether fields created from this custom field type is applicable for macros
		 *
		 * @var boolean
		 */
		public static $has_macro = true;

		/**
		 * Set whether fields of this custom field type is applicalbe for search on ticket list page.
		 *
		 * @var boolean
		 */
		public static $is_search = false;

		/**
		 * Initialize the class
		 *
		 * @return void
		 */
		public static function init() {

			// Get object of this class.
			add_filter( 'wpsc_load_ref_classes', array( __CLASS__, 'load_ref_class' ) );

			// Set custom field type.
			add_filter( 'wpsc_cf_types', array( __CLASS__, 'add_cf_type' ), 11 );

			// ticket form.
			add_action( 'wpsc_js_validate_ticket_form', array( __CLASS__, 'js_validate_ticket_form' ) );
			add_filter( 'wpsc_create_ticket_data', array( __CLASS__, 'set_create_ticket_data' ), 10, 3 );

			// create ticket data for rest api.
			add_filter( 'wpsc_rest_create_ticket', array( __CLASS__, 'set_rest_ticket_data' ), 10, 3 );
		}

		/**
		 * Load current class to reference classes
		 *
		 * @param array $classes - Associative array of class names indexed by its slug.
		 * @return array
		 */
		public static function load_ref_class( $classes ) {

			$classes[ self::$slug ] = array(
				'class'    => __CLASS__,
				'save-key' => 'id',
			);
			return $classes;
		}

		/**
		 * Add custom field type to list
		 *
		 * @param array $cf_types - custom field types array.
		 * @return array
		 */
		public static function add_cf_type( $cf_types ) {

			$cf_types[ self::$slug ] = array(
				'label' => esc_attr__( 'Time', 'supportcandy' ),
				'class' => __CLASS__,
			);
			return $cf_types;
		}

		/**
		 * Print ticket form field
		 *
		 * @param WPSC_Custom_Field $cf - Custom field object.
		 * @param array             $tff - Array of ticket form field settings for this field.
		 * @return string
		 */
		public static function print_tff( $cf, $tff ) {

			$unique_id = uniqid( 'wpsc_' );
			$val       = '';
			if ( $cf->field == 'ticket' ) {
				$val = $cf->is_auto_fill && $cf->default_value ? $cf->default_value[0] : '';
			} else {
				$current_user = WPSC_Current_User::$current_user;
				$val          = $current_user->is_customer && $current_user->customer->{$cf->slug} ? $current_user->customer->{$cf->slug} : '';
			}

			ob_start();?>
			<div class="<?php echo esc_attr( WPSC_Functions::get_tff_classes( $cf, $tff ) ); ?>" data-cft="<?php echo esc_attr( self::$slug ); ?>">
				<div class="wpsc-tff-label">
					<span class="name"><?php echo esc_attr( $cf->name ); ?></span>
					<?php
					if ( $tff['is-required'] ) {
						?>
						<span class="required-indicator">*</span>
						<?php
					}
					?>
				</div>
				<span class="extra-info"><?php echo esc_attr( $cf->extra_info ); ?></span>
				<div class="wpsc-input-close-group">
					<input 
						id="<?php echo esc_attr( $unique_id ); ?>"
						type="text" 
						name="<?php echo esc_attr( $cf->slug ); ?>" 
						placeholder="<?php echo esc_attr( $cf->placeholder_text ); ?>" 
						value="<?php echo esc_attr( $val ); ?>"
						autocomplete="off"/>
					<span onclick="wpsc_clear_date(this);"><?php WPSC_Icons::get( 'times' ); ?></span>
				</div>
				<script>
					jQuery('#<?php echo esc_attr( $unique_id ); ?>').flatpickr({
						enableTime: true,
						noCalendar: true,
						dateFormat: "H:i",
						time_24hr: true,
						disableMobile: true,
						<?php echo $cf->date_range == 'range' ? "minTime:'" . esc_attr( $cf->start_range->format( 'H:i' ) ) . "', maxTime:'" . esc_attr( $cf->end_range->format( 'H:i' ) ) . "'" : ''; ?>
					});
				</script>
			</div>
			<?php
			return ob_get_clean();
		}

		/**
		 * Validate this type field in create ticket
		 *
		 * @return void
		 */
		public static function js_validate_ticket_form() {
			?>

			case '<?php echo esc_attr( self::$slug ); ?>':
				var val = customField.find('input').first().val().trim();
				if (customField.hasClass('required') && !val) {
					isValid = false;
					alert(supportcandy.translations.req_fields_missing);
				}
				break;
			<?php
			echo PHP_EOL;
		}

		/**
		 * Return custom field value in $_POST
		 * Ignore phpcs nonce issue as we already checked where it is called from.
		 *
		 * @param string $slug - Custom field slug.
		 * @param mixed  $cf - Custom field object or false.
		 * @return mixed
		 */
		public static function get_tff_value( $slug, $cf = false ) {

			$value = isset( $_POST[ $slug ] ) ? sanitize_text_field( wp_unslash( $_POST[ $slug ] ) ) : ''; // phpcs:ignore
			return $value && preg_match( '/\d{2}:\d{2}/', $value ) ? $value : '';
		}

		/**
		 * Return default value for custom field of this type
		 *
		 * @param WPSC_Custom_Field $cf - custom field object.
		 * @return mixed
		 */
		public static function get_default_value( $cf ) {

			return $cf->default_value ? $cf->default_value[0] : '';
		}

		/**
		 * Check and return custom field value for new ticket to be created.
		 * This function is used by filter for set create ticket form and called directly by my-profile for each applicable custom fields.
		 * Ignore phpcs nonce issue as we already checked where it is called from.
		 *
		 * @param array   $data - Array of values to to stored in ticket in an insert function.
		 * @param array   $custom_fields - Array containing all applicable custom fields indexed by unique custom field types.
		 * @param boolean $is_my_profile - Whether it or not it is created from my-profile. This function is used by create ticket as well as my-profile. Due to customer fields handling is done same way, this flag gives apportunity to identify where it being called.
		 * @return array
		 */
		public static function set_create_ticket_data( $data, $custom_fields, $is_my_profile ) {

			if ( isset( $custom_fields[ self::$slug ] ) ) {
				foreach ( $custom_fields[ self::$slug ] as $cf ) {
					$value = self::get_tff_value( $cf->slug );
					if ( $cf->field == 'ticket' ) {

						$default_val       = self::get_default_value( $cf );
						$data[ $cf->slug ] = $value ? $value : $default_val;

					} elseif ( $cf->field == 'agentonly' ) {

						$data[ $cf->slug ] = self::get_default_value( $cf );

					} elseif ( $cf->field == 'customer' && $data['customer'] != 0 ) {

						$tff = get_option( 'wpsc-tff' );
						if ( ! $is_my_profile && ! isset( $tff[ $cf->slug ] ) ) {
							continue;
						}
						$customer = new WPSC_Customer( $data['customer'] );
						$prev_val = $customer->{$cf->slug} ? $customer->{$cf->slug} : '';
						if ( $customer->{$cf->slug} != $value ) {
							$customer->{$cf->slug} = $value;
							$customer->save();

							// Set log for this change.
							WPSC_Log::insert(
								array(
									'type'         => 'customer',
									'ref_id'       => $customer->id,
									'modified_by'  => WPSC_Current_User::$current_user->customer->id,
									'body'         => wp_json_encode(
										array(
											'slug' => $cf->slug,
											'prev' => $prev_val,
											'new'  => $value,
										)
									),
									'date_created' => ( new DateTime() )->format( 'Y-m-d H:i:s' ),
								)
							);
						}
					}
				}
			}
			return $data;
		}

		/**
		 * Set create ticket data for rest api request
		 *
		 * @param array           $data - create ticket data array.
		 * @param WP_REST_Request $request - rest request object.
		 * @param array           $custom_fields - custom field objects indexed by unique custom field types.
		 * @return array
		 */
		public static function set_rest_ticket_data( $data, $request, $custom_fields ) {

			$current_user = WPSC_Current_User::$current_user;
			$tff = get_option( 'wpsc-tff' );

			if ( isset( $custom_fields[ self::$slug ] ) ) {
				foreach ( $custom_fields[ self::$slug ] as $cf ) {

					if (
						! in_array( $cf->field, array( 'ticket', 'agentonly', 'customer' ) ) ||
						( $cf->field == 'customer' && ! isset( $tff[ $cf->slug ] ) )
					) {
						continue;
					}

					$time = WPSC_Functions::sanitize_time( sanitize_text_field( $request->get_param( $cf->slug ) ) );

					if ( in_array( $cf->field, array( 'ticket', 'agentonly' ) ) ) {

						$data[ $cf->slug ] = $cf->field == 'ticket' && $time ? $time : self::get_default_value( $cf );

					} else {

						$customer = new WPSC_Customer( $data['customer'] );
						$existing_val = $customer->{$cf->slug};

						if ( $time && $time != $existing_val ) {

							$customer->{$cf->slug} = $time;
							$customer->save();

							// Set log for this change.
							WPSC_Log::insert(
								array(
									'type'         => 'customer',
									'ref_id'       => $customer->id,
									'modified_by'  => $current_user->customer->id,
									'body'         => wp_json_encode(
										array(
											'slug' => $cf->slug,
											'prev' => $existing_val,
											'new'  => $time,
										)
									),
									'date_created' => ( new DateTime() )->format( 'Y-m-d H:i:s' ),
								)
							);
						}
					}
				}
			}

			return $data;
		}

		/**
		 * Return orderby string
		 *
		 * @param WPSC_Custom_Field $cf - custom field object.
		 * @return string
		 */
		public static function get_orderby_string( $cf ) {

			return self::get_sql_slug( $cf );
		}

		/**
		 * Return slug string to be used in where condition of ticket model for this type of field
		 *
		 * @param WPSC_Custom_Field $cf - custom field object.
		 * @return string
		 */
		public static function get_sql_slug( $cf ) {

			$join_char = in_array( $cf->field, array( 'ticket', 'agentonly' ) ) ? 't.' : 'c.';
			return $join_char . $cf->slug;
		}

		/**
		 * Print generic input field for this type.
		 *
		 * @param WPSC_Custom_Field $cf - Custom field object.
		 * @param mixed             $value - optional. Input field will be printed with given value if given.
		 * @return void
		 */
		public static function print_cf_input( $cf, $value = '' ) {

			$unique_id = uniqid( 'wpsc_' );
			?>
			<div class="wpsc-tff wpsc-sm-12 wpsc-md-12 wpsc-lg-12 wpsc-visible wpsc-xs-12" data-cft="<?php echo esc_attr( self::$slug ); ?>">
				<div class="wpsc-tff-label">
					<span class="name"><?php echo esc_attr( $cf->name ); ?></span>
				</div>
				<?php
				$extra_info = stripslashes( $cf->extra_info );
				if ( $extra_info ) {
					?>
					<span class="extra-info"><?php echo esc_attr( $cf->extra_info ); ?></span>
					<?php
				}
				?>
				<div class="wpsc-input-close-group">
					<input 
						id="<?php echo esc_attr( $unique_id ); ?>"
						type="text" 
						name="<?php echo esc_attr( $cf->slug ); ?>" 
						placeholder="<?php echo esc_attr( $cf->placeholder_text ); ?>" 
						value="<?php echo esc_attr( $value ); ?>"
						autocomplete="off"/>
					<span onclick="wpsc_clear_date(this);"><?php WPSC_Icons::get( 'times' ); ?></span>
				</div>
				<script>
					jQuery('#<?php echo esc_attr( $unique_id ); ?>').flatpickr({
						enableTime: true,
						noCalendar: true,
						dateFormat: "H:i",
						time_24hr: true,
						defaultTime : null,
						disableMobile: true,
						<?php echo $cf->date_range == 'range' ? "minTime:'" . esc_attr( $cf->start_range->format( 'H:i' ) ) . "', maxTime:'" . esc_attr( $cf->end_range->format( 'H:i' ) ) . "'" : ''; ?>
					});
				</script>
			</div>
			<?php
		}

		/**
		 * Get value for generic field from post.
		 * Ignore phpcs nonce issue as we already checked where it is called from.
		 *
		 * @param WPSC_Custom_Field $cf - custom field object.
		 * @return string
		 */
		public static function get_cf_input_val( $cf ) {

			$value = isset( $_POST[ $cf->slug ] ) ? sanitize_text_field( wp_unslash( $_POST[ $cf->slug ] ) ) : ''; // phpcs:ignore
			return $value && preg_match( '/\d{2}:\d{2}/', $value ) ? $value : '';
		}

		/**
		 * Print edit ticket custom field in individual ticket
		 *
		 * @param WPSC_Custom_Field $cf - custom field object.
		 * @param WPSC_Ticket       $ticket - ticket object.
		 * @return void
		 */
		public static function print_edit_ticket_cf( $cf, $ticket ) {

			$unique_id = uniqid( 'wpsc_' );
			?>
			<div class="wpsc-tff wpsc-sm-12 wpsc-md-12 wpsc-lg-12 wpsc-visible wpsc-xs-12" data-cft="<?php echo esc_attr( self::$slug ); ?>">
				<div class="wpsc-tff-label">
					<span class="name"><?php echo esc_attr( $cf->name ); ?></span>
				</div>
				<?php
				$extra_info = stripslashes( $cf->extra_info );
				if ( $extra_info ) {
					?>
					<span class="extra-info"><?php echo esc_attr( $cf->extra_info ); ?></span>
					<?php
				}
				?>
				<div class="wpsc-input-close-group">
					<input 
						id="<?php echo esc_attr( $unique_id ); ?>"
						type="text" 
						name="<?php echo esc_attr( $cf->slug ); ?>" 
						placeholder="<?php echo esc_attr( $cf->placeholder_text ); ?>" 
						value="<?php echo esc_attr( $ticket->{$cf->slug} ); ?>"
						autocomplete="off"/>
					<span onclick="wpsc_clear_date(this);"><?php WPSC_Icons::get( 'times' ); ?></span>
				</div>
				<script>
					jQuery('#<?php echo esc_attr( $unique_id ); ?>').flatpickr({
						enableTime: true,
						noCalendar: true,
						dateFormat: "H:i",
						time_24hr: true,
						defaultTime : null,
						disableMobile: true,
						<?php echo $cf->date_range == 'range' ? "minTime:'" . esc_attr( $cf->start_range->format( 'H:i' ) ) . "', maxTime:'" . esc_attr( $cf->end_range->format( 'H:i' ) ) . "'" : ''; ?>
					});
				</script>
			</div>
			<?php
		}

		/**
		 * Set edit individual ticket for this custom field type.
		 * Ignore phpcs nonce issue as we already checked where it is called from.
		 *
		 * @param WPSC_Custom_Field $cf - custom field object.
		 * @param WPSC_Ticket       $ticket - ticket object.
		 * @return WPSC_Ticket
		 */
		public static function set_edit_ticket_cf( $cf, $ticket ) {

			$prev = $ticket->{$cf->slug} ? $ticket->{$cf->slug} : '';
			$new  = isset( $_POST[ $cf->slug ] ) ? sanitize_text_field( wp_unslash( $_POST[ $cf->slug ] ) ) : ''; // phpcs:ignore

			// Exit if there is no change.
			if ( $prev == $new ) {
				return $ticket;
			}

			// Change value.
			$ticket->{$cf->slug} = $new;
			$ticket->save();

			return $ticket;
		}

		/**
		 * Modify ticket field value of this custom field type using rest api
		 *
		 * @param WPSC_Ticket       $ticket - ticket object.
		 * @param WPSC_Custom_Field $cf - custom field.
		 * @param mixed             $value - value to be set.
		 * @return void
		 */
		public static function set_rest_edit_ticket_cf( $ticket, $cf, $value ) {

			$time = WPSC_Functions::sanitize_time( sanitize_text_field( $value ) );
			if ( $ticket->{$cf->slug} != $time ) {
				$ticket->{$cf->slug} = $time;
			}
		}

		/**
		 * Insert log thread for this custom field type change
		 *
		 * @param WPSC_Custom_Field $cf - current custom field of this type.
		 * @param WPSC_Ticket       $prev - ticket object before making any changes.
		 * @param WPSC_Ticket       $new - ticket object after making changes.
		 * @param string            $current_date - date string to be stored as create time.
		 * @param int               $customer_id - current user customer id for blame.
		 * @return void
		 */
		public static function insert_ticket_log( $cf, $prev, $new, $current_date, $customer_id ) {

			// Exit if there is no change.
			if ( $prev->{$cf->slug} == $new->{$cf->slug} ) {
				return;
			}

			$prev_val = $prev->{$cf->slug} ? $prev->{$cf->slug} : '';
			$new_val  = $new->{$cf->slug} ? $new->{$cf->slug} : '';

			$thread = WPSC_Thread::insert(
				array(
					'ticket'       => $prev->id,
					'customer'     => $customer_id,
					'type'         => 'log',
					'body'         => wp_json_encode(
						array(
							'slug' => $cf->slug,
							'prev' => $prev_val,
							'new'  => $new_val,
						)
					),
					'date_created' => $current_date,
					'date_updated' => $current_date,
				)
			);
		}

		/**
		 * Return data for this custom field while creating duplicate ticket
		 *
		 * @param WPSC_Custom_Field $cf - custom field object.
		 * @param WPSC_Ticket       $ticket - ticket object.
		 * @return mixed
		 */
		public static function get_duplicate_ticket_data( $cf, $ticket ) {

			return $ticket->{$cf->slug};
		}

		/**
		 * Print edit field for this type in edit customer info
		 *
		 * @param WPSC_Custom_field $cf - custom field object.
		 * @param WPSC_Customer     $customer - customer object.
		 * @param array             $tff - ticket form field data.
		 * @return string
		 */
		public static function print_edit_customer_info( $cf, $customer, $tff ) {

			$unique_id = uniqid( 'wpsc_' );
			$val       = $customer->id && $customer->{$cf->slug} ? $customer->{$cf->slug} : '';

			ob_start();
			?>
			<div class="<?php echo esc_attr( WPSC_Functions::get_tff_classes( $cf, $tff ) ); ?>" data-cft="<?php echo esc_attr( self::$slug ); ?>">
				<div class="wpsc-tff-label">
					<span class="name"><?php echo esc_attr( $cf->name ); ?></span>
				</div>
				<span class="extra-info"><?php echo esc_attr( $cf->extra_info ); ?></span>
				<div class="wpsc-input-close-group">
					<input 
						id="<?php echo esc_attr( $unique_id ); ?>"
						type="text" 
						name="<?php echo esc_attr( $cf->slug ); ?>" 
						placeholder="<?php echo esc_attr( $cf->placeholder_text ); ?>" 
						value="<?php echo esc_attr( $val ); ?>"
						autocomplete="off"/>
					<span onclick="wpsc_clear_date(this);"><?php WPSC_Icons::get( 'times' ); ?></span>
				</div>
				<script>
					jQuery('#<?php echo esc_attr( $unique_id ); ?>').flatpickr({
						enableTime: true,
						noCalendar: true,
						dateFormat: "H:i",
						time_24hr: true,
						disableMobile: true,
						<?php echo $cf->date_range == 'range' ? "minTime:'" . esc_attr( $cf->start_range->format( 'H:i' ) ) . "', maxTime:'" . esc_attr( $cf->end_range->format( 'H:i' ) ) . "'" : ''; ?>
					});
				</script>
			</div>
			<?php
			return ob_get_clean();
		}

		/**
		 * Print add new custom field setting properties
		 *
		 * @param string $field_class - Class name of the field.
		 * @return void
		 */
		public static function get_add_new_custom_field_properties( $field_class ) {

			if ( in_array( 'extra_info', $field_class::$allowed_properties ) ) :
				?>
				<div data-type="textfield" data-required="false" class="wpsc-input-group extra-info">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Extra info', 'supportcandy' ); ?></label>
					</div>
					<input name="extra_info" type="text" autocomplete="off" />
				</div>
				<?php
			endif;

			if ( in_array( 'default_value', $field_class::$allowed_properties ) ) :
				?>
				<div data-type="textfield" data-required="false" class="wpsc-input-group default_value">
					<div class="label-container">
						<label for="">
							<?php esc_attr_e( 'Default value', 'supportcandy' ); ?>
						</label>
					</div>
					<div class="wpsc-input-close-group">
						<input type="text" name="default_value" id="wpsc-default-val-time" autocomplete="off">
						<span onclick="wpsc_clear_date(this);"><?php WPSC_Icons::get( 'times' ); ?></span>
					</div>
					<script>
						jQuery("#wpsc-default-val-time").flatpickr({
							enableTime: true,
							noCalendar: true,
							dateFormat: "H:i",
							time_24hr: true
						});
					</script>
				</div>
				<?php
			endif;

			if ( in_array( 'is_auto_fill', $field_class::$allowed_properties ) ) :
				?>
				<div data-type="single-select" data-required="false" class="wpsc-input-group is_auto_fill">
					<div class="label-container">
						<label for="">
							<?php esc_attr_e( 'Auto-fill in ticket form', 'supportcandy' ); ?>
						</label>
					</div>
					<select name="is_auto_fill">
						<option value="0"><?php esc_attr_e( 'No', 'supportcandy' ); ?></option>
						<option value="1"><?php esc_attr_e( 'Yes', 'supportcandy' ); ?></option>
					</select>
				</div>
				<?php
			endif;

			if ( in_array( 'placeholder_text', $field_class::$allowed_properties ) ) :
				?>
				<div data-type="textfield" data-required="false" class="wpsc-input-group placeholder_text">
					<div class="label-container">
						<label for="">
							<?php esc_attr_e( 'Placeholder', 'supportcandy' ); ?>
						</label>
					</div>
					<input type="text" name="placeholder_text" autocomplete="off">
				</div>
				<?php
			endif;
			?>

			<div data-type="single-select" data-required="false" class="wpsc-input-group time-range">
				<div class="label-container">
					<label for=""><?php esc_attr_e( 'Time range', 'supportcandy' ); ?></label>
				</div>
				<select id="wpsc-time-range" name="time_range">
					<option value="all"><?php esc_attr_e( 'All', 'supportcandy' ); ?></option>
					<option value="range"><?php esc_attr_e( 'Between', 'supportcandy' ); ?></option>
				</select>
				<script>
					jQuery('#wpsc-time-range').change(function() {
						if (jQuery(this).val() == 'range') {
							jQuery('.wpsc-time-range-option').show();
						} else {
							jQuery('.wpsc-time-range-option').hide();
						}
					});
				</script>
			</div>

			<div data-type="date-select" data-required="false" class="wpsc-input-group wpsc-time-range-option" style="display:none;">
				<div class="label-container">
					<label for=""><?php esc_attr_e( 'Time in between', 'supportcandy' ); ?></label>
				</div>
				<div class="wpsc-date-range">
					<div class="wpsc-input-close-group">
						<input type="text" id="from-time-range" name="from-time-range" value="" autocomplete="off"/>
						<span onclick="wpsc_clear_date(this);"><?php WPSC_Icons::get( 'times' ); ?></span>
					</div>
					<div style="margin:5px;"></div>
					<div class="wpsc-input-close-group">
						<input type="text" id="to-time-range" name="to-time-range" value="" autocomplete="off"/>
						<span onclick="wpsc_clear_date(this);"><?php WPSC_Icons::get( 'times' ); ?></span>
					</div>
				</div>
				<script>
					jQuery("#from-time-range").flatpickr( {
						enableTime: true,
						noCalendar: true,
						dateFormat: "H:i",
						time_24hr: true
					});
					jQuery('#from-time-range').change(function() {
						var fromtime = jQuery(this).val().trim();
						jQuery('#to-time-range').flatpickr( {
							enableTime: true,
							minTime: fromtime,
							noCalendar: true,
							dateFormat: "H:i",
							time_24hr: true
						});
					});
				</script>
			</div>

			<div data-type="single-select" data-required="false" class="wpsc-input-group time-format">
				<div class="label-container">
					<label for=""><?php esc_attr_e( 'Time format', 'supportcandy' ); ?></label>
				</div>
				<select name="time_format">
					<option value="24"><?php esc_attr_e( '24 Hrs', 'supportcandy' ); ?></option>
					<option value="12"><?php esc_attr_e( '12 Hrs', 'supportcandy' ); ?></option>
				</select>
			</div>
			<?php

			if ( in_array( 'tl_width', $field_class::$allowed_properties ) ) :
				?>
				<div data-type="number" data-required="false" class="wpsc-input-group tl_width">
					<div class="label-container">
						<label for="">
							<?php esc_attr_e( 'Ticket list width (pixels)', 'supportcandy' ); ?>
						</label>
					</div>
					<input type="number" name="tl_width" autocomplete="off">
				</div>
				<?php
			endif;

			if ( in_array( 'is_personal_info', $field_class::$allowed_properties ) ) :
				?>
				<div data-type="single-select" data-required="false" class="wpsc-input-group is_personal_info">
					<div class="label-container">
						<label for="">
							<?php esc_attr_e( 'Has personal info', 'supportcandy' ); ?>
						</label>
					</div>
					<select name="is_personal_info">
						<option value="0"><?php esc_attr_e( 'No', 'supportcandy' ); ?></option>
						<option value="1"><?php esc_attr_e( 'Yes', 'supportcandy' ); ?></option>
					</select>
				</div>
				<?php
			endif;

			if ( in_array( 'allow_my_profile', $field_class::$allowed_properties ) ) :
				?>
				<div data-type="single-select" data-required="false" class="wpsc-input-group allow_my_profile">
					<div class="label-container">
						<label for="">
							<?php esc_attr_e( 'Allow in my profile?', 'supportcandy' ); ?>
						</label>
					</div>
					<select name="allow_my_profile">
						<option value="0"><?php esc_attr_e( 'No', 'supportcandy' ); ?></option>
						<option value="1"><?php esc_attr_e( 'Yes', 'supportcandy' ); ?></option>
					</select>
				</div>
				<?php
			endif;

			if ( in_array( 'allow_ticket_form', $field_class::$allowed_properties ) ) :
				?>
				<div data-type="single-select" data-required="false" class="wpsc-input-group allow_ticket_form">
					<div class="label-container">
						<label for="">
							<?php esc_attr_e( 'Allow in ticket form?', 'supportcandy' ); ?>
						</label>
					</div>
					<select name="allow_ticket_form">
						<option value="0"><?php esc_attr_e( 'No', 'supportcandy' ); ?></option>
						<option value="1"><?php esc_attr_e( 'Yes', 'supportcandy' ); ?></option>
					</select>
				</div>
				<?php
			endif;
		}

		/**
		 * Print edit custom field properties
		 *
		 * @param WPSC_Custom_Fields $cf - custom field object.
		 * @param string             $field_class - class name of field category.
		 * @return void
		 */
		public static function get_edit_custom_field_properties( $cf, $field_class ) {

			if ( in_array( 'extra_info', $field_class::$allowed_properties ) ) :
				?>
				<div data-type="textfield" data-required="false" class="wpsc-input-group extra-info">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Extra info', 'supportcandy' ); ?></label>
					</div>
					<input name="extra_info" type="text" value="<?php echo esc_attr( $cf->extra_info ); ?>" autocomplete="off" />
				</div>
				<?php
			endif;

			if ( in_array( 'default_value', $field_class::$allowed_properties ) ) :
				$default_val = $cf->default_value ? $cf->default_value[0] : '';
				?>
				<div data-type="textfield" data-required="false" class="wpsc-input-group default_value">
					<div class="label-container">
						<label for="">
							<?php esc_attr_e( 'Default value', 'supportcandy' ); ?>
						</label>
					</div>
					<div class="wpsc-input-close-group">
						<input type="text" id="wpsc-default-val-time" name="default_value" value="<?php echo esc_attr( $default_val ); ?>" autocomplete="off">
						<span onclick="wpsc_clear_date(this);"><?php WPSC_Icons::get( 'times' ); ?></span>
					</div>
					<script>
						jQuery("#wpsc-default-val-time").flatpickr({
							enableTime: true,
							noCalendar: true,
							dateFormat: "H:i",
							time_24hr: true
						});
					</script>
				</div>
				<?php
			endif;

			if ( in_array( 'is_auto_fill', $field_class::$allowed_properties ) ) :
				?>
				<div data-type="single-select" data-required="false" class="wpsc-input-group is_auto_fill">
					<div class="label-container">
						<label for="">
							<?php esc_attr_e( 'Auto-fill in ticket form', 'supportcandy' ); ?>
						</label>
					</div>
					<select name="is_auto_fill">
						<option <?php selected( $cf->is_auto_fill, '0' ); ?> value="0"><?php esc_attr_e( 'No', 'supportcandy' ); ?></option>
						<option <?php selected( $cf->is_auto_fill, '1' ); ?> value="1"><?php esc_attr_e( 'Yes', 'supportcandy' ); ?></option>
					</select>
				</div>
				<?php
			endif;
			?>

			<div data-type="single-select" data-required="false" class="wpsc-input-group time-range">
				<div class="label-container">
					<label for=""><?php esc_attr_e( 'Time range', 'supportcandy' ); ?></label>
				</div>
				<select id="wpsc-time-range" name="time_range">
					<option <?php selected( $cf->date_range, 'all' ); ?> value="all"><?php esc_attr_e( 'All', 'supportcandy' ); ?></option>
					<option <?php selected( $cf->date_range, 'range' ); ?> value="range"><?php esc_attr_e( 'Between', 'supportcandy' ); ?></option>
				</select>
				<script>
					jQuery('#wpsc-time-range').change(function() {
						if (jQuery(this).val() == 'range') {
							jQuery('.wpsc-time-range-option').show();
						} else {
							jQuery('.wpsc-time-range-option').hide();
						}
					});
				</script>
			</div>

			<div data-type="date-select" data-required="false" class="wpsc-input-group wpsc-time-range-option" style="<?php echo $cf->date_range != 'range' ? 'display:none;' : ''; ?>">
				<div class="label-container">
					<label for=""><?php esc_attr_e( 'Time in between', 'supportcandy' ); ?></label>
				</div>
				<div class="wpsc-date-range">
					<div class="wpsc-input-close-group">
						<input type="text" id="from-time-range" name="from-time-range" value="<?php echo $cf->start_range && is_object( $cf->start_range ) ? esc_attr( $cf->start_range->format( 'H:i' ) ) : ''; ?>" autocomplete="off"/>
						<span onclick="wpsc_clear_date(this);"><?php WPSC_Icons::get( 'times' ); ?></span>
					</div>
					<div style="margin:5px;"></div>
					<div class="wpsc-input-close-group">
						<input type="text" id="to-time-range" name="to-time-range" value="<?php echo $cf->end_range && is_object( $cf->end_range ) ? esc_attr( $cf->end_range->format( 'H:i' ) ) : ''; ?>" autocomplete="off"/>
						<span onclick="wpsc_clear_date(this);"><?php WPSC_Icons::get( 'times' ); ?></span>
					</div>
				</div>
				<script>
					jQuery("#from-time-range").flatpickr( {
						enableTime: true,
						noCalendar: true,
						dateFormat: "H:i",
						time_24hr: true
					});
					jQuery("#to-time-range").flatpickr( {
						enableTime: true,
						noCalendar: true,
						dateFormat: "H:i",
						time_24hr: true
					});
					jQuery('#from-time-range').change(function() {
						var fromtime = jQuery(this).val().trim();
						jQuery('#to-time-range').flatpickr( {
							enableTime: true,
							minTime: fromtime,
							noCalendar: true,
							dateFormat: "H:i",
							time_24hr: true
						});
					});
				</script>
			</div>

			<div data-type="single-select" data-required="false" class="wpsc-input-group time-format">
				<div class="label-container">
					<label for=""><?php esc_attr_e( 'Time format', 'supportcandy' ); ?></label>
				</div>
				<select name="time_format">
					<option <?php selected( $cf->time_format, '24' ); ?> value="24"><?php esc_attr_e( '24 Hrs', 'supportcandy' ); ?></option>
					<option <?php selected( $cf->time_format, '12' ); ?> value="12"><?php esc_attr_e( '12 Hrs', 'supportcandy' ); ?></option>
				</select>
			</div>
			<?php

			if ( in_array( 'placeholder_text', $field_class::$allowed_properties ) ) :
				?>
				<div data-type="textfield" data-required="false" class="wpsc-input-group placeholder_text">
					<div class="label-container">
						<label for="">
							<?php esc_attr_e( 'Placeholder', 'supportcandy' ); ?>
						</label>
					</div>
					<input type="text" name="placeholder_text" value="<?php echo esc_attr( $cf->placeholder_text ); ?>" autocomplete="off">
				</div>
				<?php
			endif;

			if ( in_array( 'tl_width', $field_class::$allowed_properties ) ) :
				?>
				<div data-type="number" data-required="false" class="wpsc-input-group tl_width">
					<div class="label-container">
						<label for="">
							<?php esc_attr_e( 'Ticket list width (pixels)', 'supportcandy' ); ?>
						</label>
					</div>
					<input type="number" name="tl_width" value="<?php echo intval( $cf->tl_width ); ?>" autocomplete="off">
				</div>
				<?php
			endif;

			if ( in_array( 'is_personal_info', $field_class::$allowed_properties ) ) :
				?>
				<div data-type="single-select" data-required="false" class="wpsc-input-group is_personal_info">
					<div class="label-container">
						<label for="">
							<?php esc_attr_e( 'Has personal info', 'supportcandy' ); ?>
						</label>
					</div>
					<select name="is_personal_info">
						<option <?php selected( $cf->is_personal_info, '0' ); ?> value="0"><?php esc_attr_e( 'No', 'supportcandy' ); ?></option>
						<option <?php selected( $cf->is_personal_info, '1' ); ?> value="1"><?php esc_attr_e( 'Yes', 'supportcandy' ); ?></option>
					</select>
				</div>
				<?php
			endif;

			if ( in_array( 'allow_my_profile', $field_class::$allowed_properties ) ) :
				?>
				<div data-type="single-select" data-required="false" class="wpsc-input-group allow_my_profile">
					<div class="label-container">
						<label for="">
							<?php esc_attr_e( 'Allow in my profile?', 'supportcandy' ); ?>
						</label>
					</div>
					<select name="allow_my_profile">
						<option <?php selected( $cf->allow_my_profile, '0' ); ?> value="0"><?php esc_attr_e( 'No', 'supportcandy' ); ?></option>
						<option <?php selected( $cf->allow_my_profile, '1' ); ?> value="1"><?php esc_attr_e( 'Yes', 'supportcandy' ); ?></option>
					</select>
				</div>
				<?php
			endif;

			if ( in_array( 'allow_ticket_form', $field_class::$allowed_properties ) ) :
				?>
				<div data-type="single-select" data-required="false" class="wpsc-input-group allow_ticket_form">
					<div class="label-container">
						<label for="">
							<?php esc_attr_e( 'Allow in ticket form?', 'supportcandy' ); ?>
						</label>
					</div>
					<select name="allow_ticket_form">
						<option <?php selected( $cf->allow_ticket_form, '0' ); ?> value="0"><?php esc_attr_e( 'No', 'supportcandy' ); ?></option>
						<option <?php selected( $cf->allow_ticket_form, '1' ); ?> value="1"><?php esc_attr_e( 'Yes', 'supportcandy' ); ?></option>
					</select>
				</div>
				<?php
			endif;
		}

		/**
		 * Set custom field properties. Can be used by add/edit custom field.
		 * Ignore phpcs nonce issue as we already checked where it is called from.
		 *
		 * @param WPSC_Custom_Field $cf - custom field object.
		 * @param string            $field_class - class of field category.
		 * @return void
		 */
		public static function set_cf_properties( $cf, $field_class ) {

			// extra info.
			if ( in_array( 'extra_info', $field_class::$allowed_properties ) ) {
				$cf->extra_info = isset( $_POST['extra_info'] ) ? sanitize_text_field( wp_unslash( $_POST['extra_info'] ) ) : ''; // phpcs:ignore
			}

			// default value.
			if ( in_array( 'default_value', $field_class::$allowed_properties ) ) {
				$default_value     = isset( $_POST['default_value'] ) ? sanitize_text_field( wp_unslash( $_POST['default_value'] ) ) : ''; // phpcs:ignore
				$cf->default_value = $default_value ? array( $default_value ) : array();
			}

			// auto fill.
			if ( in_array( 'is_auto_fill', $field_class::$allowed_properties ) ) {
				$cf->is_auto_fill = isset( $_POST['is_auto_fill'] ) ? sanitize_text_field( wp_unslash( $_POST['is_auto_fill'] ) ) : ''; // phpcs:ignore
			}

			// placeholder!
			if ( in_array( 'placeholder_text', $field_class::$allowed_properties ) ) {
				$cf->placeholder_text = isset( $_POST['placeholder_text'] ) ? sanitize_text_field( wp_unslash( $_POST['placeholder_text'] ) ) : ''; // phpcs:ignore
			}

			// time range.
			$cf->date_range = isset( $_POST['time_range'] ) ? sanitize_text_field( wp_unslash( $_POST['time_range'] ) ) : 'all'; // phpcs:ignore

			// time in between.
			$cf->start_range = isset( $_POST['from-time-range'] ) ? DateTime::createFromFormat( 'H:i', sanitize_text_field( wp_unslash( $_POST['from-time-range'] ) ) ) : ''; // phpcs:ignore
			$cf->end_range   = isset( $_POST['to-time-range'] ) && $cf->start_range ? DateTime::createFromFormat( 'H:i', sanitize_text_field( wp_unslash( $_POST['to-time-range'] ) ) ) : ''; // phpcs:ignore

			// time format.
			$cf->time_format = isset( $_POST['time_format'] ) ? intval( $_POST['time_format'] ) : 24; // phpcs:ignore

			// personal info.
			if ( in_array( 'is_personal_info', $field_class::$allowed_properties ) ) {
				$cf->is_personal_info = isset( $_POST['is_personal_info'] ) ? intval( $_POST['is_personal_info'] ) : 0; // phpcs:ignore
			}

			// my-profile.
			if ( in_array( 'allow_my_profile', $field_class::$allowed_properties ) ) {
				$cf->allow_my_profile = isset( $_POST['allow_my_profile'] ) ? intval( $_POST['allow_my_profile'] ) : 0; // phpcs:ignore
			}

			// ticket form.
			if ( in_array( 'allow_ticket_form', $field_class::$allowed_properties ) ) {
				$cf->allow_ticket_form = isset( $_POST['allow_ticket_form'] ) ? intval( $_POST['allow_ticket_form'] ) : 0; // phpcs:ignore
			}

			// tl_width!
			if ( in_array( 'tl_width', $field_class::$allowed_properties ) ) {
				$tl_width     = isset( $_POST['tl_width'] ) ? intval( $_POST['tl_width'] ) : 0; // phpcs:ignore
				$cf->tl_width = $tl_width ? $tl_width : 100;
			}

			// save!
			$cf->save();
		}

		/**
		 * Print operators for ticket form filter
		 *
		 * @param WPSC_Custom_Field $cf - custom field object.
		 * @param array             $filter - Existing filters (if any).
		 * @return void
		 */
		public static function get_operators( $cf, $filter = array() ) {
			?>

			<div class="item conditional">
				<select class="operator" onchange="wpsc_tc_get_operand(this, '<?php echo esc_attr( $cf->slug ); ?>', '<?php echo esc_attr( wp_create_nonce( 'wpsc_tc_get_operand' ) ); ?>');">
					<option value=""><?php esc_attr_e( 'Compare As', 'supportcandy' ); ?></option>
					<option <?php isset( $filter['operator'] ) && selected( $filter['operator'], '=' ); ?> value="="><?php esc_attr_e( 'Equals', 'supportcandy' ); ?></option>
					<option <?php isset( $filter['operator'] ) && selected( $filter['operator'], '<' ); ?> value="<"><?php esc_attr_e( 'Less than', 'supportcandy' ); ?></option>
					<option <?php isset( $filter['operator'] ) && selected( $filter['operator'], '<=' ); ?> value="<="><?php esc_attr_e( 'Less than or equals', 'supportcandy' ); ?></option>
					<option <?php isset( $filter['operator'] ) && selected( $filter['operator'], '>' ); ?> value=">"><?php esc_attr_e( 'Greater than', 'supportcandy' ); ?></option>
					<option <?php isset( $filter['operator'] ) && selected( $filter['operator'], '>=' ); ?> value=">="><?php esc_attr_e( 'Greater than or equals', 'supportcandy' ); ?></option>
					<option <?php isset( $filter['operator'] ) && selected( $filter['operator'], 'BETWEEN' ); ?> value="BETWEEN"><?php esc_attr_e( 'Between', 'supportcandy' ); ?></option>
				</select>
			</div>
			<?php
		}

		/**
		 * Print operators for ticket form filter
		 *
		 * @param string            $operator - condition operator on which operands should be returned.
		 * @param WPSC_Custom_Field $cf - custom field object.
		 * @param array             $filter - Exising functions (if any).
		 * @return void
		 */
		public static function get_operands( $operator, $cf, $filter = array() ) {

			$unique_id     = uniqid( 'wpsc_' );
			$is_between    = $operator == 'BETWEEN' ? true : false;
			$operand_val_1 = isset( $filter['operand_val_1'] ) ? $filter['operand_val_1'] : '';
			$operand_val_2 = isset( $filter['operand_val_2'] ) ? $filter['operand_val_2'] : '';
			?>

			<div class="item conditional operand <?php echo ! $is_between ? 'single' : ''; ?>">
				<input type="text" class="operand_val_1 <?php echo esc_attr( $unique_id ); ?>" placeholder="<?php echo $is_between ? esc_attr__( 'From time', 'supportcandy' ) : ''; ?>" value="<?php echo esc_attr( $operand_val_1 ); ?>">
			</div>
			<script>
				jQuery('.operand_val_1.<?php echo esc_attr( $unique_id ); ?>').flatpickr({
					enableTime: true,
					noCalendar: true,
					dateFormat: "H:i",
					time_24hr: true
				});
			</script>
			<?php

			if ( $is_between ) :
				?>
				<div class="item conditional operand">
					<input type="text" class="operand_val_2 <?php echo esc_attr( $unique_id ); ?>" placeholder="<?php esc_attr_e( 'To time', 'supportcandy' ); ?>" value="<?php echo esc_attr( $operand_val_2 ); ?>">
				</div>
				<script>
					jQuery('.operand_val_1.<?php echo esc_attr( $unique_id ); ?>').change(function() {
						var fromTime = jQuery(this).val().trim();
						jQuery('.operand_val_2.<?php echo esc_attr( $unique_id ); ?>').flatpickr({
							enableTime: true,
							minTime: fromTime,
							noCalendar: true,
							dateFormat: "H:i",
							time_24hr: true
						});
					});
					<?php
					if ( $operand_val_2 ) :
						?>
						jQuery('.operand_val_2.<?php echo esc_attr( $unique_id ); ?>').flatpickr({
							enableTime: true,
							noCalendar: true,
							dateFormat: "H:i",
							time_24hr: true
						});
						<?php
					endif;
					?>
				</script>
				<?php
			endif;
		}

		/**
		 * Return val field for meta query of this type of custom field
		 *
		 * @param array $condition - condition data.
		 * @return mixed
		 */
		public static function get_meta_value( $condition ) {

			$operator = $condition['operator'];
			switch ( $operator ) {

				case '=':
				case '<':
				case '<=':
				case '>':
				case '>=':
					return $condition['operand_val_1'];

				case 'BETWEEN':
					if (
						! ( isset( $condition['operand_val_1'] ) && preg_match( '/\d{2}:\d{2}/', $condition['operand_val_1'] ) ) ||
						! ( isset( $condition['operand_val_2'] ) && preg_match( '/\d{2}:\d{2}/', $condition['operand_val_2'] ) )
					) {
						return false;
					}

					return array(
						'operand_val_1' => $condition['operand_val_1'],
						'operand_val_2' => $condition['operand_val_2'],
					);
			}
			return false;
		}

		/**
		 * Parse filter and return sql query to be merged in ticket model query builder
		 *
		 * @param WPSC_Custom_Field $cf - custom field of this type.
		 * @param mixed             $compare - comparison operator.
		 * @param mixed             $val - value to compare.
		 * @return string
		 */
		public static function parse_filter( $cf, $compare, $val ) {

			$str = '';

			switch ( $compare ) {

				case '=':
					$str = self::get_sql_slug( $cf ) . ' BETWEEN \'' . esc_sql( $val ) . '\' AND \'' . esc_sql( $val ) . '\'';
					break;

				case '<':
					$str = self::get_sql_slug( $cf ) . $compare . '\'' . esc_sql( $val ) . '\' AND ' . self::get_sql_slug( $cf ) . ' !=""';
					break;

				case '>':
					$str = self::get_sql_slug( $cf ) . $compare . '\'' . esc_sql( $val ) . '\'';
					break;

				case '<=':
					$arr = array(
						self::get_sql_slug( $cf ) . $compare . '\'' . esc_sql( $val ) . '\'',
						self::get_sql_slug( $cf ) . ' BETWEEN \'' . esc_sql( $val ) . '\' AND \'' . esc_sql( $val ) . '\'',
					);
					$str = '((' . implode( ' OR ', $arr ) . ') AND ' . self::get_sql_slug( $cf ) . ' != "")';
					break;

				case '>=':
					$arr = array(
						self::get_sql_slug( $cf ) . $compare . '\'' . esc_sql( $val ) . '\'',
						self::get_sql_slug( $cf ) . ' BETWEEN \'' . esc_sql( $val ) . '\' AND \'' . esc_sql( $val ) . '\'',
					);
					$str = '(' . implode( ' OR ', $arr ) . ')';
					break;

				case 'BETWEEN':
					$str = self::get_sql_slug( $cf ) . ' BETWEEN \'' . esc_sql( $val['operand_val_1'] ) . '\' AND \'' . esc_sql( $val['operand_val_2'] ) . '\'';
					break;

				default:
					$str = '1=1';
			}

			return $str;
		}

		/**
		 * Check ticket condition
		 *
		 * @param array             $condition - array with condition data.
		 * @param WPSC_Custom_Field $cf - custom field object.
		 * @param WPSC_Ticket       $ticket - ticket object.
		 * @return boolean
		 */
		public static function is_valid_ticket_condition( $condition, $cf, $ticket ) {

			$flag = true;
			$value = $ticket->{$cf->slug};
			$value = $value ? strtotime( $value ) : '';
			if ( ! $value ) {
				return;
			}

			switch ( $condition['operator'] ) {

				case '=':
					$operand = strtotime( $condition['operand_val_1'] );
					$flag = $value == $operand ? true : false;
					break;

				case '<':
					$operand = strtotime( $condition['operand_val_1'] );
					$flag = $value < $operand ? true : false;
					break;

				case '>':
					$operand = strtotime( $condition['operand_val_1'] );
					$flag = $value > $operand ? true : false;
					break;

				case '<=':
					$operand = strtotime( $condition['operand_val_1'] );
					$flag = $value <= $operand ? true : false;
					break;

				case '>=':
					$operand = strtotime( $condition['operand_val_1'] );
					$flag = $value >= $operand ? true : false;
					break;

				case 'BETWEEN':
					$operand1 = strtotime( $condition['operand_val_1'] );
					$operand2 = strtotime( $condition['operand_val_2'] );
					$flag = $operand <= $value && $operand2 >= $value ? true : false;
					break;

				default:
					$flag = true;
			}

			return $flag;
		}

		/**
		 * Check customer condition
		 *
		 * @param array             $condition - array with condition data.
		 * @param WPSC_Custom_Field $cf - custom field object.
		 * @param WPSC_Customer     $customer - customer object.
		 * @return boolean
		 */
		public static function is_valid_customer_condition( $condition, $cf, $customer ) {

			$flag = true;
			$value = $customer->{$cf->slug};
			$value = $value ? strtotime( $value ) : '';
			if ( ! $value ) {
				return;
			}

			switch ( $condition['operator'] ) {

				case '=':
					$flag = $value == strtotime( $condition['operand_val_1'] ) ? true : false;
					break;

				case '<':
					$flag = $value < strtotime( $condition['operand_val_1'] ) ? true : false;
					break;

				case '>':
					$flag = $value > strtotime( $condition['operand_val_1'] ) ? true : false;
					break;

				case '<=':
					$flag = $value <= strtotime( $condition['operand_val_1'] ) ? true : false;
					break;

				case '>=':
					$flag = $value >= strtotime( $condition['operand_val_1'] ) ? true : false;
					break;

				case 'BETWEEN':
					$flag = strtotime( $condition['operand_val_1'] ) <= $value && strtotime( $condition['operand_val_2'] ) >= $value ? true : false;
					break;

				default:
					$flag = true;
			}

			return $flag;
		}

		/**
		 * Returns printable ticket value for custom field. Can be used in export tickets, replace macros etc.
		 *
		 * @param WPSC_Custom_Field $cf - custom field object.
		 * @param WPSC_Ticket       $ticket - ticket object.
		 * @param string            $module - module name.
		 * @return string
		 */
		public static function get_ticket_field_val( $cf, $ticket, $module = '' ) {

			$val = $ticket->{$cf->slug};
			$value = '';
			if ( $val ) {
				$date = DateTime::createFromFormat( 'H:i', $val );
				$value = $cf->time_format == 12 ? $date->format( 'h:i A' ) : $date->format( 'H:i' );
			}
			return apply_filters( 'wpsc_ticket_field_val_time', $value, $cf, $ticket, $module );
		}

		/**
		 * Print ticket value for given custom field on ticket list
		 *
		 * @param WPSC_Custom_Field $cf - custom field object.
		 * @param WPSC_Ticket       $ticket - ticket object.
		 * @return void
		 */
		public static function print_tl_ticket_field_val( $cf, $ticket ) {

			echo esc_attr( self::get_ticket_field_val( $cf, $ticket ) );
		}

		/**
		 * Print ticket value for given custom field on widget
		 *
		 * @param WPSC_Custom_Field $cf - custom field object.
		 * @param WPSC_Ticket       $ticket - ticket object.
		 * @return void
		 */
		public static function print_widget_ticket_field_val( $cf, $ticket ) {

			echo esc_attr( self::get_ticket_field_val( $cf, $ticket ) );
		}

		/**
		 * Returns printable customer value for custom field. Can be used in export tickets, replace macros etc.
		 *
		 * @param WPSC_Custom_Field $cf - custom field object.
		 * @param WPSC_Customer     $customer - customer object.
		 * @return string
		 */
		public static function get_customer_field_val( $cf, $customer ) {

			$val = $customer->{$cf->slug};
			if ( $val ) {
				$date = DateTime::createFromFormat( 'H:i', $val );
				return $cf->time_format == 12 ? $date->format( 'h:i A' ) : $date->format( 'H:i' );
			}
			return '';
		}

		/**
		 * Print customer value for given custom field on ticket list
		 *
		 * @param WPSC_Custom_Field $cf - custom field object.
		 * @param WPSC_Customer     $customer - customer object.
		 * @return void
		 */
		public static function print_tl_customer_field_val( $cf, $customer ) {

			echo esc_attr( self::get_customer_field_val( $cf, $customer ) );
		}

		/**
		 * Print customer value for given custom field on widget
		 *
		 * @param WPSC_Custom_Field $cf - custom field object.
		 * @param WPSC_Customer     $customer - customer object.
		 * @return void
		 */
		public static function print_widget_customer_field_val( $cf, $customer ) {

			echo esc_attr( self::get_customer_field_val( $cf, $customer ) );
		}

		/**
		 * Print given value for custom field
		 *
		 * @param WPSC_Custom_Field $cf - custom field object.
		 * @param mixed             $val - value to convert and print.
		 * @return void
		 */
		public static function print_val( $cf, $val ) {

			if ( $val ) {
				$date = DateTime::createFromFormat( 'H:i', $val );
				echo $cf->time_format == 12 ? esc_attr( $date->format( 'h:i A' ) ) : esc_attr( $date->format( 'H:i' ) );
			} else {
				esc_attr_e( 'None', 'supportcandy' );
			}
		}

		/**
		 * Return printable value for history log macro
		 *
		 * @param WPSC_Custom_Field $cf - custom field object.
		 * @param mixed             $val - value to convert and return.
		 * @return string
		 */
		public static function get_history_log_val( $cf, $val ) {

			ob_start();
			self::print_val( $cf, $val );
			return ob_get_clean();
		}
	}
endif;

WPSC_CF_Time::init();
