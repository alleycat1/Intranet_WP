<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_CF_TAGS' ) ) :

	final class WPSC_DF_TAGS {

		/**
		 * Slug for this custom field type
		 *
		 * @var string
		 */
		public static $slug = 'df_tags';

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
		public static $data_type = 'TINYTEXT NULL DEFAULT NULL';

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
		public static $ref_class = 'wpsc_ticket_tags';

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
		 * Set whether this custom field has user given custom options
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
		public static $is_search = true;

		/**
		 * Initialize the class
		 *
		 * @return void
		 */
		public static function init() {

			// Get object of this class.
			add_filter( 'wpsc_load_ref_classes', array( __CLASS__, 'load_ref_class' ) );

			// ticket search query.
			add_filter( 'wpsc_ticket_search', array( __CLASS__, 'ticket_search' ), 10, 5 );
		}

		/**
		 * Load current class to ref classes
		 *
		 * @param array $classes - array of ref classes.
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
		 * Print edit custom field properties
		 *
		 * @param WPSC_Custom_Fields $cf - custom field object.
		 * @param string             $field_class - class name of field category.
		 * @return void
		 */
		public static function get_edit_custom_field_properties( $cf, $field_class ) {

			if ( in_array( 'tl_width', $field_class::$allowed_properties ) ) :?>
				<div data-type="number" data-required="false" class="wpsc-input-group tl_width">
					<div class="label-container">
						<label for="">
							<?php echo esc_attr( wpsc__( 'Ticket list width (pixels)', 'supportcandy' ) ); ?>
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
		 * Print operators for ticket form filter
		 *
		 * @param WPSC_Custom_Field $cf - custom field object.
		 * @param array             $filter - user filters.
		 * @return void
		 */
		public static function get_operators( $cf, $filter = array() ) {
			?>

			<div class="item conditional">
				<select class="operator" onchange="wpsc_tc_get_operand(this, '<?php echo esc_attr( $cf->slug ); ?>', '<?php echo esc_attr( wp_create_nonce( 'wpsc_tc_get_operand' ) ); ?>');">                    
					<option value=""><?php echo esc_attr( wpsc__( 'Compare As', 'supportcandy' ) ); ?></option>
					<option <?php isset( $filter['operator'] ) && selected( $filter['operator'], '=' ); ?> value="="><?php echo esc_attr( wpsc__( 'Equals', 'supportcandy' ) ); ?></option>
					<option <?php isset( $filter['operator'] ) && selected( $filter['operator'], 'IN' ); ?> value="IN"><?php echo esc_attr( wpsc__( 'Matches', 'supportcandy' ) ); ?></option>
					<option <?php isset( $filter['operator'] ) && selected( $filter['operator'], 'NOT IN' ); ?> value="NOT IN"><?php echo esc_attr( wpsc__( 'Not Matches', 'supportcandy' ) ); ?></option>
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
			$tags     = WPSC_Ticket_Tags::find( array( 'items_per_page' => 0 ) )['results'];
			$unique_id   = uniqid( 'wpsc_' );
			?>
			<div class="item conditional operand single">
				<select class="operand_val_1 <?php echo esc_attr( $unique_id ); ?>" <?php echo $is_multiple ? 'multiple' : ''; ?>>
					<?php
					foreach ( $tags as $tag ) {
						$selected = '';
						if ( isset( $filter['operand_val_1'] ) && ( ( $is_multiple && in_array( $tag->id, $filter['operand_val_1'] ) ) || ( ! $is_multiple && $tag->id == $filter['operand_val_1'] ) ) ) {
							$selected = 'selected="selected"';
						}
						?>
						<option <?php echo esc_attr( $selected ); ?> value="<?php echo esc_attr( $tag->id ); ?>"><?php echo esc_attr( $tag->name ); ?></option>
						<?php
					}
					?>
				</select>
			</div>
			<script>
				jQuery('.operand_val_1.<?php echo esc_attr( $unique_id ); ?>').selectWoo();
			</script>
			<?php
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
						$args = array(
							'items_per_page' => 0,
							'search'         => $search,
							'order'          => 'ASC',
							'orderby'        => 'id',
						);
						$tags = WPSC_Ticket_Tags::find( $args )['results'];
						$ids = array_map(
							function( $tag ) {
								return $tag->id;
							},
							$tags
						);

						if ( $tags ) {
							$sql[] = 't.' . $cf->slug . ' RLIKE \'(^|[|])(' . implode( '|', $ids ) . ')($|[|])\'';
						}
					}
				}
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

			$val      = $ticket->{$cf->slug};
			$response = array();
			foreach ( $val as $option ) {
				$response[] = $option->id;
			}
			return $response ? implode( '|', $response ) : '';
		}

		/**
		 * Print generic input field for this type.
		 *
		 * @param WPSC_Custom_Field $cf - custom field object.
		 * @param mixed             $value - optional. Input field will be printed with given value.
		 * @return void
		 */
		public static function print_cf_input( $cf, $value = array() ) {

			if ( ! is_array( $value ) ) {

				$value = array_filter(
					array_map(
						function( $val ) {
							$option = new WPSC_Option( $val );
							return $option->id ? $option : '';
						},
						explode( '|', $value )
					)
				);
			}

			$tags     = WPSC_Ticket_Tags::find( array( 'items_per_page' => 0 ) )['results'];
			$unique_id = uniqid( 'wpsc_' );
			?>
			<div class="wpsc-tff wpsc-sm-12 wpsc-md-12 wpsc-lg-12 wpsc-visible wpsc-xs-12" data-cft="<?php echo esc_attr( self::$slug ); ?>">
				<div class="wpsc-tff-label">
					<span class="name"><?php echo esc_attr( $cf->name ); ?></span>
				</div>
				<select class="<?php echo esc_attr( $unique_id ); ?>" name="<?php echo esc_attr( $cf->slug ); ?>[]" multiple>
					<?php
					$val = array_map( fn( $tags)=>$tags->id, $value );
					foreach ( $tags as $tag ) {
						$selected = $val && in_array( $tag->id, $val ) ? 'selected' : ''
						?>
						<option <?php echo esc_attr( $selected ); ?> value="<?php echo esc_attr( $tag->id ); ?>"><?php echo esc_attr( $tag->name ); ?></option>
						<?php
					}
					?>
				</select>
				<script>
					jQuery('select.<?php echo esc_attr( $unique_id ); ?>').selectWoo({
						allowClear: false,
						placeholder: ""
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

			$value = isset( $_POST[ $cf->slug ] ) ? array_filter( array_map( 'intval', $_POST[ $cf->slug ] ) ) : array(); // phpcs:ignore
			return $value ? implode( '|', $value ) : '';
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
					$str = $cf->slug . ' RLIKE \'(^|[|])' . esc_sql( $val ) . '($|[|])\'';
					break;

				case 'IN':
					foreach ( $val as $index => $value ) {
						if ( $value == '' ) {
							$val[ $index ] = '^$';
						}
					}
					$str = $cf->slug . ' RLIKE \'(^|[|])(' . implode( '|', esc_sql( $val ) ) . ')($|[|])\'';
					break;

				case 'NOT IN':
					foreach ( $val as $index => $value ) {
						if ( $value == '' ) {
							$val[ $index ] = '^$';
						}
					}
					$str = $cf->slug . ' NOT RLIKE \'(^|[|])(' . implode( '|', esc_sql( $val ) ) . ')($|[|])\'';
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

			$flag   = true;
			$cf_ids = array_map( fn( $option) => $option->id, $ticket->{$cf->slug} );

			switch ( $condition['operator'] ) {

				case '=':
					$flag = in_array( $condition['operand_val_1'], $cf_ids );
					break;

				case 'IN':
					$flag = false;
					foreach ( $cf_ids as $id ) {
						if ( in_array( $id, $condition['operand_val_1'] ) ) {
							$flag = true;
							break;
						}
					}
					break;

				case 'NOT IN':
					foreach ( $cf_ids as $id ) {
						if ( in_array( $id, $condition['operand_val_1'] ) ) {
							$flag = false;
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
		 * Return default value for custom field of this type
		 *
		 * @param WPSC_Custom_Field $cf - custom field object.
		 * @return mixed
		 */
		public static function get_default_value( $cf ) {

			return '';
		}

		/**
		 * Returns printable ticket value for custom field. Can be used in export tickets, replace macros etc.
		 *
		 * @param WPSC_Custom_Field $cf - custom field object.
		 * @param WPSC_Ticket       $ticket - ticket object.
		 * @return string
		 */
		public static function get_ticket_field_val( $cf, $ticket ) {

			$tags = array_filter(
				array_map(
					fn( $option) => $option->id ? $option->name : '',
					$ticket->{$cf->slug}
				)
			);
			return $tags ? implode( ', ', $tags ) : '';
		}

		/**
		 * Print ticket value for given custom field on ticket list
		 *
		 * @param WPSC_Custom_Field $cf - custom field object.
		 * @param WPSC_Ticket       $ticket - ticket object.
		 * @return void
		 */
		public static function print_tl_ticket_field_val( $cf, $ticket ) {

			$tags = array_filter(
				array_map(
					fn( $tag) => $tag->id ? $tag : '',
					$ticket->{$cf->slug}
				)
			);
			foreach ( $tags as $tag ) {
				?>
				<div class="wpsc-tag" style="background-color: <?php echo esc_attr( $tag->bg_color ); ?>; color:<?php echo esc_attr( $tag->color ); ?>;"><?php echo esc_attr( $tag->name ); ?></div>
				<?php
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

			$val          = is_array( $val ) ? $val : array_filter( explode( '|', $val ) );
			$options      = array_filter(
				array_map(
					function( $option ) {
						if ( is_object( $option ) ) {
							return $option;
						} elseif ( $option ) {
							$option = new WPSC_Option( $option );
							return $option->id ? $option : '';
						} else {
							return '';
						}
					},
					$val
				)
			);
			$option_names = array_map( fn( $option) => $option->name, $options );
			echo $option_names ? esc_attr( implode( '|', $option_names ) ) : esc_attr__( 'None', 'supportcandy' );
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

WPSC_DF_TAGS::init();
