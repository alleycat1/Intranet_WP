<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_DF_Agent_Created' ) ) :

	final class WPSC_DF_Agent_Created {

		/**
		 * Slug for this custom field type
		 *
		 * @var string
		 */
		public static $slug = 'df_agent_created';

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
		public static $data_type = 'INT NULL';

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
		public static $ref_class = 'wpsc_agent';

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
		public static $has_extra_info = false;

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
		public static $is_ctf = false;

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

			// create ticket data from ticket form.
			add_filter( 'wpsc_create_ticket_data', array( __CLASS__, 'set_create_ticket_data' ), 5, 3 );

			// create ticket data for rest api.
			add_filter( 'wpsc_rest_create_ticket', array( __CLASS__, 'set_rest_ticket_data' ), 2, 3 );

			// agent autocomplete filter access only.
			add_action( 'wp_ajax_wpsc_agent_autocomplete_agent_created', array( __CLASS__, 'agent_autocomplete_agent_created' ) );
			add_action( 'wp_ajax_nopriv_wpsc_agent_autocomplete_agent_created', array( __CLASS__, 'agent_autocomplete_agent_created' ) );

			// rest api.
			add_filter( 'wpsc_rest_prevent_ticket_data', array( __CLASS__, 'rest_prevent_ticket_data' ) );
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

						foreach ( $filter['operand_val_1'] as $agent_id ) {

							if ( $agent_id == '0' ) {

								?>
								<option selected="selected" value="0"><?php esc_attr_e( 'None', 'supportcandy' ); ?></option>
								<?php

							} else {

								$agent = new WPSC_Agent( intval( $agent_id ) )
								?>
								<option selected="selected" value="<?php echo esc_attr( $agent->id ); ?>"><?php echo esc_attr( $agent->name ); ?></option>
								<?php
							}
						}
					}

					if ( ! $is_multiple && isset( $filter['operand_val_1'] ) ) {

						if ( $filter['operand_val_1'] == '0' ) {

							?>
							<option selected="selected" value="0"><?php esc_attr_e( 'None', 'supportcandy' ); ?></option>
							<?php

						} else {

							$agent = new WPSC_Agent( intval( $filter['operand_val_1'] ) )
							?>
							<option selected="selected" value="<?php echo esc_attr( $agent->id ); ?>"><?php echo esc_attr( $agent->name ); ?></option>
							<?php

						}
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
								action: 'wpsc_agent_autocomplete_agent_created',
								_ajax_nonce: '<?php echo esc_attr( wp_create_nonce( 'wpsc_agent_autocomplete_agent_created' ) ); ?>',
								isAgentgroup: 0
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
					minimumInputLength: 0
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

			$flag = true;

			switch ( $condition['operator'] ) {

				case '=':
					if ( ! $ticket->{$cf->slug} ) {
						$flag = $condition['operand_val_1'] == '0' ? true : false;
					} else {
						$flag = $condition['operand_val_1'] == $ticket->{$cf->slug}->id ? true : false;
					}
					break;

				case 'IN':
					if ( ! $ticket->{$cf->slug} ) {
						$flag = in_array( '0', $condition['operand_val_1'] ) ? true : false;
					} else {
						$flag = in_array( $ticket->{$cf->slug}->id, $condition['operand_val_1'] ) ? true : false;
					}
					break;

				case 'NOT IN':
					if ( ! $ticket->{$cf->slug} ) {
						$flag = ! in_array( '0', $condition['operand_val_1'] ) ? true : false;
					} else {
						$flag = ! in_array( $ticket->{$cf->slug}->id, $condition['operand_val_1'] ) ? true : false;
					}
					break;

				default:
					$flag = true;
			}

			return $flag;
		}

		/**
		 * Return default value for custom field of this type
		 *
		 * @param WPSC_Custom_Field $cf - custom field object.
		 * @return mixed
		 */
		public static function get_default_value( $cf ) {

			return 0;
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

			$current_user = WPSC_Current_User::$current_user;
			$name         = WPSC_DF_Customer_Name::get_tff_value( 'name' );
			$email        = WPSC_DF_Customer_Email::get_tff_value( 'email' );

			if ( ! $name || ! $email ) {
				wp_send_json_error( new WP_Error( 'WPSC_DF_Agent_Created', 'customer name or email not given!' ), 400 );
			}

			if ( $current_user->is_customer ) {

				$is_create_as = $current_user->customer->email != $email ? true : false;
				if (
					$is_create_as &&
					(
						! $current_user->is_agent || ! $current_user->agent->has_cap( 'create-as' )
					)
				) {
					wp_send_json_error( new WP_Error( 'WPSC_DF_Agent_Created', 'Unauthorized!' ), 401 );
				}

				$data['agent_created'] = $is_create_as ? $current_user->agent->id : 0;

			} else {

				$data['agent_created'] = 0;
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
			$is_create_as = $current_user->customer->id != $data['customer'] ? true : false;

			if (
				$is_create_as && (
					! $current_user->is_agent || ! $current_user->agent->has_cap( 'create-as' )
				)
			) {
				$data['errors']->add( 'unauthorized', 'You are not authorized to create ticket for other customer!' );
				return $data;
			}

			$data['agent_created'] = $is_create_as ? $current_user->agent->id : 0;
			return $data;
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

			return WPSC_Current_User::$current_user->agent->id;
		}

		/**
		 * Print edit custom field properties
		 *
		 * @param WPSC_Custom_Fields $cf - custom field object.
		 * @param string             $field_class - class name of field category.
		 * @return void
		 */
		public static function get_edit_custom_field_properties( $cf, $field_class ) {

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

			$agent = $ticket->{$cf->slug};
			$value = is_object( $agent ) && $agent->id ? $agent->name : '';
			return apply_filters( 'wpsc_ticket_field_val_agent_created', $value, $cf, $ticket, $module );
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

			$agent = $ticket->{$cf->slug};
			echo $agent->id ? esc_attr( $agent->name ) : esc_attr__( 'Not Applicable', 'supportcandy' );
		}

		/**
		 * Agent autocomplete for filter access
		 *
		 * @return void
		 */
		public static function agent_autocomplete_agent_created() {

			if ( check_ajax_referer( 'wpsc_agent_autocomplete_agent_created', '_ajax_nonce', false ) !== 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			$current_user = WPSC_Current_User::$current_user;
			if ( $current_user->is_agent ) {
				$filter_items = get_option( 'wpsc-atl-filter-items', array() );
			} elseif ( $current_user->is_customer && ! $current_user->is_agent ) {
				$filter_items = get_option( 'wpsc-ctl-filter-items', array() );
			}

			if ( ! ( in_array( 'agent_created', $filter_items ) || WPSC_Functions::is_site_admin() ) ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$filters = array(
				'term'       => isset( $_GET['q'] ) ? sanitize_text_field( wp_unslash( $_GET['q'] ) ) : '',
				'filter_by'  => 'all',
				'sort_by'    => 'name',
				'isMultiple' => 0,
			);

			$filters['isAgentgroup'] = 0;
			if ( class_exists( 'WPSC_Agentgroups' ) ) {
				$filters['isAgentgroup'] = isset( $_GET['isAgentgroup'] ) ? intval( $_GET['isAgentgroup'] ) : null;
			}

			$response = WPSC_Agent::agent_autocomplete( $filters );
			wp_send_json( $response );
		}

		/**
		 * Rest api filter for ticket data to prevent from sending it to client
		 *
		 * @param array $data - array of slugs to prevent from.
		 * @return array
		 */
		public static function rest_prevent_ticket_data( $data ) {

			$current_user = WPSC_Current_User::$current_user;
			if ( ! $current_user->is_agent ) {
				$list_items = $current_user->get_tl_list_items();
				if ( ! in_array( 'agent_created', $list_items ) ) {
					$data[] = 'agent_created';
				}
			}
			return $data;
		}
	}
endif;

WPSC_DF_Agent_Created::init();
