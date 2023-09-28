<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_DF_Additional_Recipients' ) ) :

	final class WPSC_DF_Additional_Recipients {

		/**
		 * Slug for this custom field type
		 *
		 * @var string
		 */
		public static $slug = 'df_add_recipients';

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
		public static $has_multiple_val = true;

		/**
		 * Data type for column created in tickets table
		 *
		 * @var string
		 */
		public static $data_type = 'TEXT NULL DEFAULT NULL';

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
		public static $is_default = true;

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
		public static $has_personal_info = false;

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
		public static $is_list = false;

		/**
		 * Set whether fields created from this custom field type is allowed in ticket filter
		 *
		 * @var boolean
		 */
		public static $is_filter = false;

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
		public static $is_auto_fill = false;

		/**
		 * Set whether fields created from this custom field type can be available for ticket list sorting
		 *
		 * @var boolean
		 */
		public static $is_sort = false;

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
		public static $is_search = true;

		/**
		 * Initialize the class
		 *
		 * @return void
		 */
		public static function init() {

			// Get object of this class.
			add_filter( 'wpsc_load_ref_classes', array( __CLASS__, 'load_ref_class' ) );

			// Ticket model.
			add_filter( 'wpsc_ticket_search', array( __CLASS__, 'ticket_search' ), 10, 5 );

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
		 * Add ticket search compatibility for fields of this custom field type.
		 *
		 * @param array  $sql - Array of sql peices that can be joined later.
		 * @param array  $filter - User filter.
		 * @param array  $custom_fields - Custom fields array applicable for search.
		 * @param string $search - search string.
		 * @param array  $allowed_search_fields - Allowed search fields.
		 * @return array
		 */
		public static function ticket_search( $sql, $filter, $custom_fields, $search, $allowed_search_fields ) {

			if ( isset( $custom_fields[ self::$slug ] ) ) {
				foreach ( $custom_fields[ self::$slug ] as $cf ) {
					if ( in_array( $cf->slug, $allowed_search_fields ) ) {
						$sql[] = 'CONVERT( t.assigned_agent USING utf8) LIKE \'%' . $search . '%\'';
					}
				}
			}

			return $sql;
		}

		/**
		 * Print ticket form field
		 *
		 * @param WPSC_Custom_Field $cf - Custom field object.
		 * @param array             $tff - Array of ticket form field settings for this field.
		 * @return string
		 */
		public static function print_tff( $cf, $tff ) {

			ob_start();?>
			<div class="<?php echo esc_attr( WPSC_Functions::get_tff_classes( $cf, $tff ) ); ?>" data-cft="<?php echo esc_attr( self::$slug ); ?>">
				<div class="wpsc-tff-label">
					<span class="name"><?php echo esc_attr( $cf->name ); ?></span>
					<?php
					if ( $tff['is-required'] ) :
						?>
						<span class="required-indicator">*</span>
						<?php
					endif;
					?>
				</div>
				<span class="extra-info"><?php echo esc_attr( $cf->extra_info ); ?></span>
				<textarea 
					type="text" 
					name="<?php echo esc_attr( $cf->slug ); ?>" 
					placeholder="<?php echo esc_attr( $cf->placeholder_text ); ?>" 
					onchange="wpsc_check_tff_visibility()"
					autocomplete="off"></textarea>
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
				var val = customField.find('textarea').first().val().trim();
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

			return isset( $_POST[ $slug ] ) ? sanitize_textarea_field( wp_unslash( $_POST[ $slug ] ) ) : ''; // phpcs:ignore
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

			if ( $is_my_profile ) {
				return;
			}

			if ( isset( $custom_fields[ self::$slug ] ) ) {
				$new = array();
				foreach ( $custom_fields[ self::$slug ] as $cf ) {

					$recipients = self::get_tff_value( $cf->slug );
					if ( ! $recipients ) {
						continue;
					}
					$recipients        = array_filter( array_map( 'sanitize_email', explode( PHP_EOL, $recipients ) ) );
					$data[ $cf->slug ] = $recipients ? implode( '|', $recipients ) : '';
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

			$cf = WPSC_Custom_Field::get_cf_by_slug( 'add_recipients' );
			$add_recipients = array_unique(
				array_filter(
					array_map(
						fn( $email ) => WPSC_Functions::sanitize_email( trim( $email ) ),
						explode( ',', sanitize_text_field( $request->get_param( 'add_recipients' ) ) )
					)
				)
			);
			$data['add_recipients'] = implode( '|', $add_recipients );
			return $data;
		}

		/**
		 * Print generic input field for this type.
		 *
		 * @param WPSC_Custom_Field $cf - Custom field object.
		 * @param mixed             $value - optional. Input field will be printed with given value if given.
		 * @return void
		 */
		public static function print_cf_input( $cf, $value = '' ) {

			$add_recipients = implode( PHP_EOL, explode( '|', $value ) );
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
				<textarea 
					name="<?php echo esc_attr( $cf->slug ); ?>" 
					rows="5"
					placeholder="<?php echo esc_attr( $cf->placeholder_text ); ?>"><?php echo esc_attr( $add_recipients ); ?></textarea>
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

			$value = isset( $_POST[ $cf->slug ] ) ? sanitize_textarea_field( wp_unslash( $_POST[ $cf->slug ] ) ) : ''; // phpcs:ignore
			$value = explode( PHP_EOL, $value );
			return $value ? implode( '|', $value ) : '';
		}

		/**
		 * Returns value of edit custom field
		 *
		 * @param WPSC_Custom_Field $cf - custom field object.
		 * @return mixed
		 */
		public static function get_edit_cf_val( $cf ) {

			// ignore phpcs nonce issue as we already checked where it is called from.
			$recipients = isset( $_POST[ $cf->slug ] ) ? sanitize_textarea_field( wp_unslash( $_POST[ $cf->slug ] ) ) : ''; // phpcs:ignore
			if ( ! $recipients ) {
				return '';
			}
			$recipients = array_filter( array_map( 'sanitize_email', explode( PHP_EOL, $recipients ) ) );
			return $recipients ? implode( '|', $recipients ) : '';
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

			// placeholder!
			if ( in_array( 'placeholder_text', $field_class::$allowed_properties ) ) {
				$cf->placeholder_text = isset( $_POST['placeholder_text'] ) ? sanitize_text_field( wp_unslash( $_POST['placeholder_text'] ) ) : ''; // phpcs:ignore
			}

			// save!
			$cf->save();

			// update widget title!
			$ticket_widgets                                   = get_option( 'wpsc-ticket-widget', array() );
			$ticket_widgets['additional-recipients']['title'] = $cf->name;
			update_option( 'wpsc-ticket-widget', $ticket_widgets );
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

			$recipients = $ticket->{$cf->slug};
			$value = $recipients ? implode( ', ', $recipients ) : '';
			return apply_filters( 'wpsc_ticket_field_val_add_recipients', $value, $cf, $ticket, $module );
		}

		/**
		 * Print ticket value for given custom field on widget
		 *
		 * @param WPSC_Custom_Field $cf - custom field object.
		 * @param WPSC_Ticket       $ticket - ticket object.
		 * @return void
		 */
		public static function print_widget_ticket_field_val( $cf, $ticket ) {

			$recipients = $ticket->{$cf->slug};
			if ( $recipients ) {
				foreach ( $recipients as $recipient ) {
					?>
					<div><?php echo esc_attr( $recipient ); ?></div>
					<?php
				}
			} else {
				esc_attr_e( 'Not Applicable', 'supportcandy' );
			}
		}

		/**
		 * Print given value for custom field
		 *
		 * @param WPSC_Custom_Field $cf - custom field object.
		 * @param mixed             $val - value to convert and print.
		 * @return void
		 */
		public static function print_val( $cf, $val ) {

			$val = is_array( $val ) ? $val : array_filter( explode( '|', $val ) );
			echo $val ? esc_attr( implode( ', ', $val ) ) : esc_attr__( 'None', 'supportcandy' );
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

		/**
		 * Return default value for custom field of this type
		 *
		 * @param WPSC_Custom_Field $cf - custom field object.
		 * @return mixed
		 */
		public static function get_default_value( $cf ) {

			return '';
		}
	}
endif;

WPSC_DF_Additional_Recipients::init();
