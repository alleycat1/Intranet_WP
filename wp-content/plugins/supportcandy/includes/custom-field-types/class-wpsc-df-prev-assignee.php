<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_DF_Prev_Assignee' ) ) :

	final class WPSC_DF_Prev_Assignee {

		/**
		 * Slug for this custom field type
		 *
		 * @var string
		 */
		public static $slug = 'df_prev_assignee';

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
		 * Set whether fields created from this custom field type can be available for ticket list sorting
		 *
		 * @var boolean
		 */
		public static $is_sort = false;

		/**
		 * Set whether fields created from this custom field type can be auto-filled
		 *
		 * @var boolean
		 */
		public static $is_auto_fill = false;

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

			// agent autocomplete filter access only.
			add_action( 'wp_ajax_wpsc_agent_autocomplete_prev_assigned_agent', array( __CLASS__, 'agent_autocomplete_prev_assigned_agent' ) );
			add_action( 'wp_ajax_nopriv_wpsc_agent_autocomplete_prev_assigned_agent', array( __CLASS__, 'agent_autocomplete_prev_assigned_agent' ) );

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
		 * Return default value for custom field of this type
		 *
		 * @param WPSC_Custom_Field $cf - custom field object.
		 * @return mixed
		 */
		public static function get_default_value( $cf ) {

			return '';
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
							$agent = new WPSC_Agent( intval( $agent_id ) )
							?>
							<option selected="selected" value="<?php echo esc_attr( $agent->id ); ?>"><?php echo esc_attr( $agent->name ); ?></option>
							<?php
						}
					}

					if ( ! $is_multiple && isset( $filter['operand_val_1'] ) ) {

						$agent = new WPSC_Agent( intval( $filter['operand_val_1'] ) )
						?>
						<option selected="selected" value="<?php echo esc_attr( $agent->id ); ?>"><?php echo esc_attr( $agent->name ); ?></option>
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
								action: 'wpsc_agent_autocomplete_prev_assigned_agent',
								_ajax_nonce: '<?php echo esc_attr( wp_create_nonce( 'wpsc_agent_autocomplete_prev_assigned_agent' ) ); ?>',
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
		 * Check condition for this type
		 *
		 * @param array             $condition - condition data.
		 * @param WPSC_Custom_Field $cf - custom field object.
		 * @param mixed             $assigned_agents - value to compare.
		 * @return boolean
		 */
		public static function is_valid( $condition, $cf, $assigned_agents ) {

			$response = false;
			switch ( $condition['operator'] ) {

				case '=':
					$response = in_array( $condition['operand_val_1'], $assigned_agents );
					break;

				case 'IN':
					$flag = false;
					foreach ( $condition['operand_val_1'] as $agent ) {
						if ( in_array( $agent, $assigned_agents ) ) {
							$flag = true;
							break;
						}
					}
					$response = $flag;
					break;

				case 'NOT IN':
					$flag = true;
					foreach ( $condition['operand_val_1'] as $agent ) {
						if ( in_array( $agent, $assigned_agents ) ) {
							$flag = false;
							break;
						}
					}
					$response = $flag;
					break;
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

				case '=':
					$str = 't.' . $cf->slug . ' RLIKE \'(^|[|])' . esc_sql( $val ) . '($|[|])\'';
					break;

				case 'IN':
					foreach ( $val as $index => $value ) {
						if ( $value == '' ) {
							$val[ $index ] = '^$';
						}
					}
					$str = 't.' . $cf->slug . ' RLIKE \'(^|[|])(' . implode( '|', esc_sql( $val ) ) . ')($|[|])\'';
					break;

				case 'NOT IN':
					foreach ( $val as $index => $value ) {
						if ( $value == '' ) {
							$val[ $index ] = '^$';
						}
					}
					$str = 't.' . $cf->slug . ' NOT RLIKE \'(^|[|])(' . implode( '|', esc_sql( $val ) ) . ')($|[|])\'';
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
			$agent_ids = array_filter(
				array_map(
					fn( $agent) => $agent->id,
					$ticket->{$cf->slug}
				)
			);

			switch ( $condition['operator'] ) {

				case '=':
					if ( $condition['operand_val_1'] == 0 ) {
						$flag = ! $agent_ids ? true : false;
					} else {
						$flag = in_array( $condition['operand_val_1'], $agent_ids ) ? true : false;
					}
					break;

				case 'IN':
					if ( ! $agent_ids ) {
						$flag = in_array( '0', $condition['operand_val_1'] ) ? true : false;
					} else {

						$flag = false;
						foreach ( $agent_ids as $id ) {
							if ( in_array( $id, $condition['operand_val_1'] ) ) {
								$flag = true;
								break;
							}
						}
					}
					break;

				case 'NOT IN':
					if ( ! $agent_ids ) {
						$flag = ! in_array( '0', $condition['operand_val_1'] ) ? true : false;
					} else {

						foreach ( $agent_ids as $id ) {
							if ( in_array( $id, $condition['operand_val_1'] ) ) {
								$flag = false;
								break;
							}
						}
					}
					break;

				default:
					$flag = true;
			}

			return $flag;
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

			$agents      = $ticket->{$cf->slug};
			$agent_names = array_filter(
				array_map(
					fn( $agent) => $agent->id ? $agent->name : '',
					$agents
				)
			);
			$value = $agent_names ? implode( ', ', $agent_names ) : esc_attr__( 'None', 'supportcandy' );

			return apply_filters( 'wpsc_ticket_field_val_prev_assignee', $value, $cf, $ticket, $module );
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
		 * Agent autocomplete for filter access
		 *
		 * @return void
		 */
		public static function agent_autocomplete_prev_assigned_agent() {

			if ( check_ajax_referer( 'wpsc_agent_autocomplete_prev_assigned_agent', '_ajax_nonce', false ) !== 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			$current_user = WPSC_Current_User::$current_user;
			if ( $current_user->is_agent ) {
				$filter_items = get_option( 'wpsc-atl-filter-items', array() );
			} elseif ( $current_user->is_customer && ! $current_user->is_agent ) {
				$filter_items = get_option( 'wpsc-ctl-filter-items', array() );
			}

			if ( ! in_array( 'prev_assignee', $filter_items ) ) {
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
				if ( ! in_array( 'prev_assignee', $list_items ) ) {
					$data[] = 'prev_assignee';
				}
			}
			return $data;
		}
	}
endif;

WPSC_DF_Prev_Assignee::init();
