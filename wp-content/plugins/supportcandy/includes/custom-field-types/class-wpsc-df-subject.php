<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_DF_Subject' ) ) :

	final class WPSC_DF_Subject {

		/**
		 * Slug for this custom field type
		 *
		 * @var string
		 */
		public static $slug = 'df_subject';

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
		public static $data_type = 'TEXT NOT NULL';

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
		public static $has_char_limit = true;

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
		public static $is_visibility_conditions = true;

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

			// ticket form.
			add_action( 'wpsc_js_validate_ticket_form', array( __CLASS__, 'js_validate_ticket_form' ) );
			add_filter( 'wpsc_create_ticket_data', array( __CLASS__, 'set_create_ticket_data' ), 10, 3 );

			// create ticket data for rest api.
			add_filter( 'wpsc_rest_create_ticket', array( __CLASS__, 'set_rest_ticket_data' ), 10, 3 );

			// individual ticket.
			add_action( 'wp_ajax_wpsc_it_get_edit_subject', array( __CLASS__, 'it_get_edit_subject' ) );
			add_action( 'wp_ajax_nopriv_wpsc_it_get_edit_subject', array( __CLASS__, 'it_get_edit_subject' ) );
			add_action( 'wp_ajax_wpsc_it_set_edit_subject', array( __CLASS__, 'it_set_edit_subject' ) );
			add_action( 'wp_ajax_nopriv_wpsc_it_set_edit_subject', array( __CLASS__, 'it_set_edit_subject' ) );

			// ticket search query.
			add_filter( 'wpsc_ticket_search', array( __CLASS__, 'ticket_search' ), 10, 5 );
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
		 * Print operators for ticket form filter
		 *
		 * @param WPSC_Custom_Field $cf - custom field object.
		 * @param array             $filter - Existing filters (if any).
		 * @return void
		 */
		public static function get_operators( $cf, $filter = array() ) {?>

			<div class="item conditional">
				<select class="operator" onchange="wpsc_tc_get_operand(this, '<?php echo esc_attr( $cf->slug ); ?>', '<?php echo esc_attr( wp_create_nonce( 'wpsc_tc_get_operand' ) ); ?>');">
					<option value=""><?php esc_attr_e( 'Compare As', 'supportcandy' ); ?></option>
					<option <?php isset( $filter['operator'] ) && selected( $filter['operator'], 'LIKE' ); ?> value="LIKE"><?php esc_attr_e( 'Has Words', 'supportcandy' ); ?></option>
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

			$value = isset( $filter['operand_val_1'] ) ? stripslashes( $filter['operand_val_1'] ) : ''
			?>
			<div class="item conditional operand single">
				<textarea class="operand_val_1" placeholder="<?php esc_attr_e( 'One condition per line!', 'supportcandy' ); ?>" style="width: 100%;"><?php echo esc_attr( $value ); ?></textarea>
			</div>
			<?php
		}

		/**
		 * Check condition for this type
		 *
		 * @param array             $condition - condition data.
		 * @param WPSC_Custom_Field $cf - custom field object.
		 * @param mixed             $value - value to compare.
		 * @return boolean
		 */
		public static function is_valid( $condition, $cf, $value ) {

			$response = false;
			$value    = stripslashes( $value );
			$terms    = explode( PHP_EOL, $condition['operand_val_1'] );
			$response = false;
			foreach ( $terms as $term ) {
				$index = strpos( $value, trim( stripslashes( $term ) ) );
				if ( is_numeric( $index ) ) {
					$response = true;
					break;
				}
			}
			return $response;
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

				case 'LIKE':
					$arr = array();
					$val = explode( PHP_EOL, $val );
					foreach ( $val as $term ) {
						$term  = str_replace( '*', '%', trim( $term ) );
						$arr[] = 'CONVERT(t.' . $cf->slug . ' USING utf8) LIKE \'%' . $term . '%\'';
					}
					$str = '(' . implode( ' OR ', $arr ) . ')';
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

			$flag  = true;
			$value = stripslashes( $ticket->{$cf->slug} );
			$terms = explode( PHP_EOL, $condition['operand_val_1'] );

			switch ( $condition['operator'] ) {

				case 'LIKE':
					$flag = false;
					foreach ( $terms as $term ) {
						$index = strpos( $value, trim( stripslashes( $term ) ) );
						if ( is_numeric( $index ) ) {
							$flag = true;
							break;
						}
					}
					break;

				default:
					$flag = true;
			}

			return $flag;
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

			if ( in_array( 'subject', $allowed_search_fields ) ) {
				$sql[] = 'CONVERT(t.subject USING utf8) LIKE \'%' . $search . '%\'';
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

			$val = $cf->is_auto_fill && $cf->default_value ? stripslashes( htmlspecialchars( $cf->default_value[0] ) ) : '';

			ob_start();
			?>
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
				<input 
					type="text" 
					name="<?php echo esc_attr( $cf->slug ); ?>" 
					placeholder="<?php echo esc_attr( $cf->placeholder_text ); ?>" 
					maxlength="<?php echo $cf->char_limit ? intval( $cf->char_limit ) : ''; ?>" 
					value="<?php echo esc_attr( $val ); ?>"
					onchange="wpsc_check_tff_visibility()"
					autocomplete="off"/>
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

			return isset( $_POST[ $slug ] ) ? sanitize_text_field( wp_unslash( $_POST[ $slug ] ) ) : ''; // phpcs:ignore
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

			$value = self::get_tff_value( 'subject' );
			$cf    = WPSC_Custom_Field::get_cf_by_slug( 'subject' );

			if (
				$cf->char_limit && mb_strlen( $value ) > $cf->char_limit
			) {
				wp_send_json_error( new WP_Error( 'WPSC_DF_Subject', 'Character limit exceed!!' ), 400 );
			}

			$default_val = self::get_default_value( $cf );
			if (
				! $value && ! $default_val
			) {
				wp_send_json_error( new WP_Error( 'WPSC_DF_Subject', 'You must enter subject!!' ), 400 );
			}

			$data['subject'] = ! $value && $default_val ? $default_val : $value;
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

			$cf = WPSC_Custom_Field::get_cf_by_slug( 'subject' );
			$subject = sanitize_text_field( $request->get_param( 'subject' ) );
			$subject = $subject ? $subject : self::get_default_value( $cf );

			// check subject is present.
			if ( ! $subject ) {
				$data['errors']->add( 'req_fields_missing', 'subject is missing!', 'subject' );
				return $data;
			}

			// check char limit.
			if ( $cf->char_limit && strlen( $subject ) > $cf->char_limit ) {
				$data['errors']->add( 'char_limit', 'character limit for subject is exceeded!', 'subject' );
				return $data;
			}

			$data['subject'] = $subject;
			return $data;
		}

		/**
		 * Return slug string to be used in where condition of ticket model for this type of field
		 *
		 * @param WPSC_Custom_Field $cf - custom field object.
		 * @return string
		 */
		public static function get_sql_slug( $cf ) {

			return 't.' . $cf->slug;
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

				case 'LIKE':
					return trim( $condition['operand_val_1'] );
			}
			return false;
		}

		/**
		 * Get edit ticket subject
		 *
		 * @return void
		 */
		public static function it_get_edit_subject() {

			WPSC_Individual_Ticket::load_current_ticket();
			$current_user = WPSC_Current_User::$current_user;
			if ( ! ( $current_user->is_agent && WPSC_Individual_Ticket::has_ticket_cap( 'ctf' ) ) ) {
				wp_send_json_error( 'Something went wrong!', 401 );
			}

			$ticket = WPSC_Individual_Ticket::$ticket;
			$cf     = WPSC_Custom_Field::get_cf_by_slug( 'subject' );
			$title  = $cf->name;

			ob_start();
			?>
			<form action="#" onsubmit="return false;" class="edit-subject">
				<div class="wpsc-input-group">
					<input name="subject" type="text" value="<?php echo esc_attr( $ticket->subject ); ?>" autocomplete="off">
				</div>
				<?php do_action( 'wpsc_get_edit_subject_body' ); ?>
				<input type="hidden" name="action" value="wpsc_it_set_edit_subject">
				<input type="hidden" name="ticket_id" value="<?php echo esc_attr( $ticket->id ); ?>">
				<input type="hidden" name="_ajax_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wpsc_it_set_edit_subject' ) ); ?>">
			</form>
			<?php
			$body = ob_get_clean();

			ob_start();
			?>
			<button class="wpsc-button small primary" onclick="wpsc_it_set_edit_subject(this, <?php echo esc_attr( $ticket->id ); ?>);">
				<?php esc_attr_e( 'Submit', 'supportcandy' ); ?>
			</button>
			<button class="wpsc-button small secondary" onclick="wpsc_close_modal();">
				<?php esc_attr_e( 'Cancel', 'supportcandy' ); ?>
			</button>
			<?php
			do_action( 'wpsc_get_edit_subject' );
			$footer = ob_get_clean();

			$response = array(
				'title'  => $title,
				'body'   => $body,
				'footer' => $footer,
			);
			wp_send_json( $response );
		}

		/**
		 * Set edit subject for a ticket
		 *
		 * @return void
		 */
		public static function it_set_edit_subject() {

			if ( check_ajax_referer( 'wpsc_it_set_edit_subject', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			WPSC_Individual_Ticket::load_current_ticket();
			$current_user = WPSC_Current_User::$current_user;
			if ( ! ( $current_user->is_agent && WPSC_Individual_Ticket::has_ticket_cap( 'ctf' ) ) ) {
				wp_send_json_error( 'Something went wrong!', 401 );
			}

			$ticket = WPSC_Individual_Ticket::$ticket;
			if ( ! $ticket->is_active ) {
				wp_send_json_error( 'Something went wrong!', 400 );
			}

			$cf = WPSC_Custom_Field::get_cf_by_slug( 'subject' );

			$new = isset( $_POST['subject'] ) ? sanitize_text_field( wp_unslash( $_POST['subject'] ) ) : '';
			if ( ! $new ) {
				wp_send_json_error( 'Something went wrong!', 400 );
			}

			$prev = $ticket->subject;

			// Exit if no change.
			if ( $new == $prev ) {
				wp_die();
			}

			$ticket->subject      = $new;
			$ticket->date_updated = new DateTime();
			$ticket->save();

			do_action( 'wpsc_change_ticket_subject', $ticket, $prev, $new, $current_user->customer->id );
			wp_die();
		}

		/**
		 * Return data for this custom field while creating duplicate ticket
		 *
		 * @param WPSC_Custom_Field $cf - custom field object.
		 * @param WPSC_Ticket       $ticket - ticket object.
		 * @return mixed
		 */
		public static function get_duplicate_ticket_data( $cf, $ticket ) {

			$subject = self::get_tff_value( 'subject' );
			if ( ! $subject ) {
				wp_send_json_error( 'Something went wrong!', 400 );
			}
			return $subject;
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
					<input type="text" name="default_value" autocomplete="off" value="<?php echo esc_attr( $default_val ); ?>">
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

			if ( in_array( 'char_limit', $field_class::$allowed_properties ) ) :
				?>
				<div data-type="number" data-required="false" class="wpsc-input-group char_limit">
					<div class="label-container">
						<label for="">
							<?php esc_attr_e( 'Character limit', 'supportcandy' ); ?>
						</label>
					</div>
					<input type="number" name="char_limit" value="<?php echo intval( $cf->char_limit ); ?>" autocomplete="off">
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

			// char limit.
			if ( in_array( 'char_limit', $field_class::$allowed_properties ) ) {
				$cf->char_limit = isset( $_POST['char_limit'] ) ? intval( $_POST['char_limit'] ) : 0; //phpcs:ignore
			}

			// check whether char limit is honored by default value.
			if ( $cf->default_value && $cf->char_limit && count( $cf->default_value ) > $cf->char_limit ) {
				wp_send_json_error( 'Incorrect request!', 400 );
			}

			// placeholder!
			if ( in_array( 'placeholder_text', $field_class::$allowed_properties ) ) {
				$cf->placeholder_text = isset( $_POST['placeholder_text'] ) ? sanitize_text_field( wp_unslash( $_POST['placeholder_text'] ) ) : ''; // phpcs:ignore
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
		 * Returns printable ticket value for custom field. Can be used in export tickets, replace macros etc.
		 *
		 * @param WPSC_Custom_Field $cf - custom field object.
		 * @param WPSC_Ticket       $ticket - ticket object.
		 * @param string            $module - module name.
		 * @return string
		 */
		public static function get_ticket_field_val( $cf, $ticket, $module = '' ) {

			return apply_filters( 'wpsc_ticket_field_val_subject', $ticket->subject, $cf, $ticket, $module );
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
		 * Print given value for custom field
		 *
		 * @param WPSC_Custom_Field $cf - custom field object.
		 * @param mixed             $val - value to convert and print.
		 * @return void
		 */
		public static function print_val( $cf, $val ) {

			echo esc_attr( $val );
		}

		/**
		 * Return printable value for history log macro
		 *
		 * @param WPSC_Custom_Field $cf - custom field object.
		 * @param mixed             $val - value to convert and return.
		 * @return string
		 */
		public static function get_history_log_val( $cf, $val ) {

			return esc_attr( $val );
		}
	}
endif;

WPSC_DF_Subject::init();
