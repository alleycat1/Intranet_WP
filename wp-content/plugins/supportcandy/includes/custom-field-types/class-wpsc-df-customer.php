<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_DF_Customer' ) ) :

	final class WPSC_DF_Customer {

		/**
		 * Slug for this custom field type
		 *
		 * @var string
		 */
		public static $slug = 'df_customer';

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
		 * Set whether this custom field type has multiple values
		 *
		 * @var boolean
		 */
		public static $has_multiple_val = false;

		/**
		 * Set whether this custom field type has reference to other class
		 *
		 * @var boolean
		 */
		public static $has_ref = true;

		/**
		 * Reference class for this custom field type so that its value(s) return with object or array of objects automatically. Empty string indicate no reference.
		 *
		 * @var string
		 */
		public static $ref_class = 'wpsc_customer';

		/**
		 * Data type for column created in customer table
		 *
		 * @var string
		 */
		public static $data_type = 'BIGINT NOT NULL';

		/**
		 * Set whether fields created from this custom field type is allowed in create ticket form
		 *
		 * @var boolean
		 */
		public static $is_ctf = false;

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
		public static $is_placeholder = false;

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
		public static $has_macro = false;

		/**
		 * Set whether fields of this custom field type is applicalbe for search on ticket list page.
		 *
		 * @var boolean
		 */
		public static $is_search = true;

		/**
		 * Set ignore customer info custom field types.
		 *
		 * @var array
		 */
		public static $ignore_customer_info_cft = array();

		/**
		 * Initialize the class
		 *
		 * @return void
		 */
		public static function init() {

			// Get object of this class.
			add_filter( 'wpsc_load_ref_classes', array( __CLASS__, 'load_ref_class' ) );

			// create ticket data from ticket form.
			add_filter( 'wpsc_create_ticket_data', array( __CLASS__, 'set_create_ticket_data' ), 6, 3 );

			// create ticket data for rest api.
			add_filter( 'wpsc_rest_create_ticket', array( __CLASS__, 'set_rest_ticket_data' ), 1, 3 );

			// Ticket model.
			add_filter( 'wpsc_ticket_joins', array( __CLASS__, 'ticket_join' ), 8, 2 );

			// ticket search query.
			add_filter( 'wpsc_ticket_search', array( __CLASS__, 'ticket_search' ), 10, 5 );

			// Ignore customer info cft.
			add_action( 'init', array( __CLASS__, 'ignore_customer_info_cft' ) );

			// Customer filter autocomplete.
			add_action( 'wp_ajax_wpsc_customer_filter_autocomplete', array( __CLASS__, 'customer_filter_autocomplete' ) );

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
					<option <?php isset( $filter['operator'] ) && selected( $filter['operator'], '=' ); ?> value="="><?php esc_attr_e( 'Equals', 'supportcandy' ); ?></option>
					<option <?php isset( $filter['operator'] ) && selected( $filter['operator'], 'IN' ); ?> value="IN"><?php esc_attr_e( 'Matches', 'supportcandy' ); ?></option>
					<option <?php isset( $filter['operator'] ) && selected( $filter['operator'], 'NOT IN' ); ?> value="NOT IN"><?php esc_attr_e( 'Not Matches', 'supportcandy' ); ?></option>
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

			$is_multiple = $operator !== '=' ? true : false;
			$unique_id   = uniqid( 'wpsc_' );
			?>
			<div class="item conditional operand single">
				<select class="operand_val_1 <?php echo esc_attr( $unique_id ); ?>" <?php echo $is_multiple ? 'multiple' : ''; ?>>
					<?php

					if ( $is_multiple && isset( $filter['operand_val_1'] ) ) {

						foreach ( $filter['operand_val_1'] as $customer_id ) {
							$customer = new WPSC_Customer( intval( $customer_id ) )
							?>
							<option selected="selected" value="<?php echo esc_attr( $customer->id ); ?>"><?php echo esc_attr( $customer->name ); ?></option>
							<?php
						}
					}

					if ( ! $is_multiple && isset( $filter['operand_val_1'] ) ) {

						$customer = new WPSC_Customer( intval( $filter['operand_val_1'] ) )
						?>
						<option selected="selected" value="<?php echo esc_attr( $customer->id ); ?>"><?php echo esc_attr( $customer->name ); ?></option>
						<?php
					}
					?>

				</select>
			</div>
			<script>
				jQuery('.operand_val_1.<?php echo esc_attr( $unique_id ); ?>').selectWoo({
					ajax: {
						url: supportcandy.ajax_url,
						dataType: 'json',
						delay: 250,
						data: function (params) {
							return {
								q: params.term, // search term
								page: params.page,
								action: 'wpsc_customer_filter_autocomplete',
								_ajax_nonce: '<?php echo esc_attr( wp_create_nonce( 'wpsc_customer_filter_autocomplete' ) ); ?>'
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
					escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
					minimumInputLength: 1
				});
			</script>
			<?php
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
					$str = 't.' . $cf->slug . '=\'' . esc_sql( $val ) . '\'';
					break;

				case 'IN':
					$str = 'CONVERT(t.' . $cf->slug . ' USING utf8) IN(\'' . implode( '\', \'', esc_sql( $val ) ) . '\')';
					break;

				case 'NOT IN':
					$str = 'CONVERT(t.' . $cf->slug . ' USING utf8) NOT IN(\'' . implode( '\', \'', esc_sql( $val ) ) . '\')';
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

			if ( ! is_object( $ticket->{$cf->slug} ) ) {
				return false;
			}

			$flag = true;

			switch ( $condition['operator'] ) {

				case '=':
					$flag = $ticket->{$cf->slug}->id == $condition['operand_val_1'] ? true : false;
					break;

				case 'IN':
					$flag = in_array( $ticket->{$cf->slug}->id, $condition['operand_val_1'] );
					break;

				case 'NOT IN':
					$flag = ! in_array( $ticket->{$cf->slug}->id, $condition['operand_val_1'] );
					break;

				default:
					$flag = true;
			}

			return $flag;
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

			$current_user = WPSC_Current_User::$current_user;
			$name         = WPSC_DF_Customer_Name::get_tff_value( 'name' );
			$email        = WPSC_DF_Customer_Email::get_tff_value( 'email' );

			$customer = self::get_customer_record( $name, $email );
			if ( ! $customer->id ) {
				wp_send_json_error( new WP_Error( '001', 'something went wrong!' ), 500 );
			}

			$data['customer'] = $customer->id;
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

			$name = $request->get_param( 'name' );
			$email = $request->get_param( 'email' );

			// check name and email are present.
			if ( ! $name || ! $email ) {
				$data['errors']->add( 'req_fields_missing', 'name or email is missing!', 'name, email' );
				return $data;
			}

			// validate email address.
			if ( ! filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
				$data['errors']->add( 'invalid_data', 'invalid email address!', 'email' );
				return $data;
			}

			// get customer object.
			$customer = self::get_customer_record( $name, $email );
			if ( ! $customer->id ) {
				$data['errors']->add( 'unknown', 'Something went wrong!' );
				return $data;
			}

			$data['customer'] = $customer->id;
			return $data;
		}

		/**
		 * Add SQL joins to ticket model for this field type
		 *
		 * @param array $joins - array of join string that can be imploded later.
		 * @param array $filter - user filter.
		 * @return array
		 */
		public static function ticket_join( $joins, $filter ) {

			global $wpdb;
			$joins[] = 'LEFT JOIN ' . $wpdb->prefix . 'psmsc_customers c ON t.customer = c.id';
			return $joins;
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

			if ( in_array( 'customer', $allowed_search_fields ) ) {
				$sql[] = 'CONVERT(c.name USING utf8) LIKE \'%' . $search . '%\'';
				$sql[] = 'CONVERT(c.email USING utf8) LIKE \'%' . $search . '%\'';
			}
			return $sql;
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
				case 'IN':
				case 'NOT IN':
					return $condition['operand_val_1'];
			}
			return false;
		}

		/**
		 * Return data for this custom field while creating duplicate ticket
		 *
		 * @param WPSC_Custom_Field $cf - custom field object.
		 * @param WPSC_Ticket       $ticket - ticket object.
		 * @return mixed
		 */
		public static function get_duplicate_ticket_data( $cf, $ticket ) {

			return $ticket->customer->id;
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

			return apply_filters( 'wpsc_ticket_field_val_customer', $ticket->customer->name, $cf, $ticket, $module );
		}

		/**
		 * Print given value for custom field
		 *
		 * @param WPSC_Custom_Field $cf - custom field object.
		 * @param mixed             $val - value to convert and print.
		 * @return void
		 */
		public static function print_val( $cf, $val ) {

			$customer = is_object( $val ) ? $val : new WPSC_Customer( $val );
			echo $customer->id ? esc_attr( $customer->name ) : esc_attr__( 'None', 'supportcandy' );
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
		 * Set ignore customer info custom field type.
		 *
		 * @return void
		 */
		public static function ignore_customer_info_cft() {

			self::$ignore_customer_info_cft = apply_filters( 'wpsc_ignore_customer_info_cft', array( 'name', 'email', 'usergroups' ) );
		}

		/**
		 * Customer autocomplete callback
		 *
		 * @return void
		 */
		public static function customer_filter_autocomplete() {

			if ( check_ajax_referer( 'wpsc_customer_filter_autocomplete', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			$current_user = WPSC_Current_User::$current_user;
			if ( $current_user->is_agent ) {
				$filter_items = get_option( 'wpsc-atl-filter-items', array() );
			} elseif ( $current_user->is_customer && ! $current_user->is_agent ) {
				$filter_items = get_option( 'wpsc-ctl-filter-items', array() );
			}

			if ( ! ( in_array( 'customer', $filter_items ) || WPSC_Functions::is_site_admin() ) ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$term = isset( $_GET['q'] ) ? sanitize_text_field( wp_unslash( $_GET['q'] ) ) : '';

			$customers = WPSC_Customer::customer_autocomplete( $term );
			wp_send_json( $customers );
		}

		/**
		 * Get customer record for creating new ticket. Apply setting like register user if not exists.
		 *
		 * @param string $name - name of the customer.
		 * @param string $email - email of the customer.
		 * @return WPSC_Customer
		 */
		public static function get_customer_record( $name, $email ) {

			$advanced = get_option( 'wpsc-ms-advanced-settings' );
			$customer = WPSC_Customer::get_by_email( $email );
			$user = get_user_by( 'email', $email );

			if ( ! $user && $advanced['register-user-if-not-exist'] ) {

				$user_id = wp_insert_user(
					array(
						'user_pass'    => null,
						'user_login'   => $email,
						'user_email'   => $email,
						'display_name' => $name,
					)
				);

				if ( ! is_wp_error( $user_id ) ) {
					wp_new_user_notification( $user_id, null, 'both' );
					$customer = WPSC_Customer::get_by_email( $email );
				}
			}

			if ( ! $customer->id ) {

				$customer = WPSC_Customer::insert(
					array(
						'user'  => $user ? $user->ID : 0,
						'name'  => $name,
						'email' => $email,
					)
				);
			}

			return $customer;
		}
	}
endif;

WPSC_DF_Customer::init();
