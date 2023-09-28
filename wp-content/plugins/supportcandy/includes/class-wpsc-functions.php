<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_Functions' ) ) :

	final class WPSC_Functions {

		/**
		 * Referance classes. Used to provide objects on runtime.
		 *
		 * @var string
		 */
		public static $ref_classes;

		/**
		 * Initialize class
		 */
		public static function init() {

			// Load ref classes.
			add_action( 'init', array( __CLASS__, 'load_ref_classes' ), 1 );
		}

		/**
		 * Load ref classes
		 */
		public static function load_ref_classes() {

			self::$ref_classes = apply_filters( 'wpsc_load_ref_classes', array() );
		}

		/**
		 * Return an object for ref class
		 *
		 * @param string $ref_class - reference class.
		 * @param string $value - value.
		 * @return object
		 */
		public static function get_object( $ref_class, $value ) {

			$object = null;

			switch ( $ref_class ) {

				case 'wp_user':
					$object = $value ? get_user_by( 'ID', $value ) : null;
					break;

				case 'datetime':
					$object = $value && $value !== '0000-00-00 00:00:00' ? DateTime::createFromFormat( 'Y-m-d H:i:s', $value ) : '';
					break;

				case 'dateinterval':
					$object = $value ? new DateInterval( $value ) : new DateInterval( 'PT0M' );
					break;

				case 'wpsc_cft':
					$object = isset( self::$ref_classes[ $value ] ) ? self::$ref_classes[ $value ]['class'] : null;
					break;

				case 'wpsc_customer':
					if ( $value && ! is_object( $object ) && isset( self::$ref_classes[ $ref_class ] ) ) {
						$class  = self::$ref_classes[ $ref_class ]['class'];
						$object = new $class( $value );
					} else {
						$object = WPSC_Customer::get_anonimus_customer();
					}
					break;

				default:
					$object = apply_filters( 'wpsc_fun_get_object', $value, $ref_class );
					if ( ! is_object( $object ) && isset( self::$ref_classes[ $ref_class ] ) ) {
						$class  = self::$ref_classes[ $ref_class ]['class'];
						$object = new $class( $value );
					}
			}

			return $object ? $object : $value;
		}

		/**
		 * Return value to be saved in model db for an abject
		 *
		 * @param string $ref_class - reference class.
		 * @param string $value - value.
		 * @return object
		 */
		public static function set_object( $ref_class, $value ) {

			$save_val = '';
			switch ( $ref_class ) {

				case 'wp_user':
					$save_val = $value->ID;
					break;

				case 'datetime':
					$save_val = $value->format( 'Y-m-d H:i:s' );
					break;

				case 'dateinterval':
					$save_val = self::date_interval_to_string( $value );
					break;

				default:
					if ( isset( self::$ref_classes[ $ref_class ] ) ) {
						$key      = self::$ref_classes[ $ref_class ]['save-key'];
						$save_val = $value->$key;
					}
			}

			return $save_val ? $save_val : $value;
		}

		/**
		 * Return search string given in model filters array
		 *
		 * @param array $filter - filter array.
		 * @return string
		 */
		public static function get_filter_search_str( $filter ) {

			return isset( $filter['search'] ) ? addslashes( trim( $filter['search'] ) ) : '';
		}

		/**
		 * Parse user filter for models which has static schema
		 *
		 * @param string $class - Class name of the model.
		 * @param array  $filters - User filters array.
		 * @return string
		 */
		public static function parse_user_filters( $class, $filters ) {

			global $wpdb;

			// Invalid filter.
			if ( ! isset( $filters['relation'] ) || count( $filters ) < 2 ) {
				return '1=1';
			}

			$relation   = $filters['relation'];
			$filter_str = array();

			foreach ( $filters as $key => $filter ) {

				// Skip if current element is relation indicator.
				if ( $key === 'relation' ) {
					continue;
				}

				// Invalid filter if it is not an array.
				if ( ! is_array( $filter ) ) {
					return '1=1';
				}

				// Call recursively if there is multi-layer filter detected.
				if ( isset( $filter['relation'] ) ) {
					$filter_str[] = self::parse_user_filters( $class, $filter );
					continue;
				}

				// Invalid filter if it does not contain slug, compare and val indexes.
				$slug    = isset( $filter['slug'] ) ? self::sanitize_sql_key( $filter['slug'] ) : false;
				$compare = isset( $filter['compare'] ) ? $filter['compare'] : false;
				$val     = isset( $filter['val'] ) || $filter['val'] == null ? $filter['val'] : false;
				if ( ! $slug || ! $compare || $val === false ) {
					return '1=1';
				}

				switch ( $compare ) {

					case '<':
					case '=':
					case '>':
					case '<=':
					case '>=':
						if ( $class::$schema[ $slug ]['has_multiple_val'] ) {
							$filter_str[] = '1=1';
							break;
						}
						$filter_str[] = $slug . ' ' . $compare . ' \'' . esc_sql( $val ) . '\'';
						break;

					case 'BETWEEN':
						$filter_str[] = $slug . ' BETWEEN \'' . esc_sql( $val[0] ) . '\' AND \'' . esc_sql( $val[1] ) . '\'';
						break;

					case 'IN':
						if ( $class::$schema[ $slug ]['has_multiple_val'] ) {

							$rlike = array();
							foreach ( $val as $match ) {

								$rlike[] = $slug . ' RLIKE \'(^|[|])' . esc_sql( $match ) . '($|[|])\'';
							}
							$filter_str[] = '( ' . implode( ' OR ', $rlike ) . ' )';

						} else {

							$filter_str[] = $slug . ' IN ( \'' . implode( '\', \'', esc_sql( $val ) ) . '\' )';
						}
						break;

					case 'NOT IN':
						if ( $class::$schema[ $slug ]['has_multiple_val'] ) {

							$rlike = array();
							foreach ( $val as $match ) {

								$rlike[] = $slug . ' NOT RLIKE \'(^|[|])' . esc_sql( $match ) . '($|[|])\'';
							}
							$filter_str[] = '( ' . implode( ' OR ', $rlike ) . ' )';

						} else {

							$filter_str[] = $slug . ' NOT IN ( \'' . implode( '\', \'', esc_sql( $val ) ) . '\' )';
						}
						break;

					case 'IS':
						$filter_str[] = $slug . ' IS NULL';
						break;

					case 'IS NOT':
						$filter_str[] = $slug . ' IS NOT NULL';
						break;

					case 'LIKE':
						$filter_str[] = $slug . ' ' . $compare . ' \'%' . esc_sql( $wpdb->esc_like( $val ) ) . '%\'';
						break;
				}
			}

			return count( $filter_str ) > 1 ?
				'( ' . implode( ' ' . $relation . ' ', $filter_str ) . ' )' :
				$filter_str[0];
		}

		/**
		 * Get order for find method of models
		 *
		 * @param array $filter - filter array.
		 * @return string
		 */
		public static function parse_order( $filter ) {

			$orderby = isset( $filter['orderby'] ) && $filter['orderby'] ?
				$filter['orderby'] : '';

			if ( ! $orderby ) {
				return '';
			}

			$order = isset( $filter['order'] ) && $filter['order'] && in_array( $filter['order'], array( 'ASC', 'DESC' ) ) ?
				$filter['order'] : 'ASC';

			$orderby_slug = isset( $filter['orderby_slug'] ) && $filter['orderby_slug'] ? $filter['orderby_slug'] : '';
			$cf = WPSC_Custom_Field::get_cf_by_slug( $orderby_slug );

			if ( $cf && $cf->type::$slug == 'cf_number' ) {

				return 'ORDER BY CAST(' . $orderby . ' AS SIGNED ) ' . $order . ' ';
			} else {

				return 'ORDER BY ' . $orderby . ' ' . $order . ' ';
			}

		}

		/**
		 * Get order for find method of models
		 *
		 * @param array $filter - filter array.
		 * @return string
		 */
		public static function parse_limit( $filter ) {

			$items_per_page = isset( $filter['items_per_page'] ) ?
				intval( $filter['items_per_page'] ) : 0;

			if ( $items_per_page == 0 ) {
				return '';
			}

			$page_no = isset( $filter['page_no'] ) && is_numeric( $filter['page_no'] ) ?
				intval( $filter['page_no'] ) : 1;

			$offset = ( $page_no - 1 ) * $items_per_page;

			return 'LIMIT ' . $offset . ', ' . $items_per_page;
		}

		/**
		 * Calculate total pages, has_next_page, results, etc. for models
		 *
		 * @param string $results - page result.
		 * @param int    $total_items - total page items.
		 * @param array  $filter - filter items.
		 * @return array
		 */
		public static function parse_response( $results, int $total_items, $filter ) {

			$items_per_page = isset( $filter['items_per_page'] ) ?
				intval( $filter['items_per_page'] ) : 0;

			if ( ! $items_per_page ) {
				return array(
					'total_items' => $total_items,
					'results'     => $results,
				);
			}

			$page_no = isset( $filter['page_no'] ) && is_numeric( $filter['page_no'] ) ?
				intval( $filter['page_no'] ) : 1;

			$total_pages = ceil( $total_items / $items_per_page );

			$has_next_page = $page_no < $total_pages ? true : false;

			return array(
				'total_items'    => $total_items,
				'items_per_page' => $items_per_page,
				'current_page'   => $page_no,
				'total_pages'    => $total_pages,
				'has_next_page'  => $has_next_page,
				'results'        => $results,
			);
		}

		/**
		 * Check whether current page is supportcandy page or not
		 * Used for loading framework.
		 * Pages like dashboard pages and where wpsc shortcode is present are considered true.
		 *
		 * @return boolean
		 */
		public static function is_wpsc_page() {

			if ( is_admin() ) {

				return isset( $_REQUEST['page'] ) && preg_match( '/wpsc-/', $_REQUEST['page'] ) ? true : false; // phpcs:ignore

			} else {

				return true;
			}
		}

		/**
		 * Check whether current use is site admin or not
		 *
		 * @return boolean
		 */
		public static function is_site_admin() {

			global $current_user;
			return $current_user->ID && $current_user->has_cap( 'manage_options' ) ? true : false;
		}

		/**
		 * Get default filter auto-increament number
		 *
		 * @return integer
		 */
		public static function get_tl_df_auto_increament() {

			$index = intval( get_option( 'wpsc-tl-df-auto-increament', 0 ) );
			update_option( 'wpsc-tl-df-auto-increament', ++$index );
			return $index;
		}

		/**
		 * Get user custom filter auto-increament number
		 *
		 * @return integer
		 */
		public static function get_tl_cf_auto_increament() {

			global $current_user;
			$index = intval( get_user_meta( $current_user->ID, get_current_blog_id() . '-wpsc-tl-cf-auto-increament', true ) );
			update_user_meta( $current_user->ID, get_current_blog_id() . '-wpsc-tl-cf-auto-increament', ++$index );
			return $index;
		}

		/**
		 * Return css classes string as per size
		 *
		 * @param WPSC_custom_field $cf - custom field.
		 * @param WPSC_TFF          $tff - ticket form field.
		 * @return string
		 */
		public static function get_tff_classes( $cf, $tff ) {

			$classes = 'wpsc-tff ' . $cf->slug . ' wpsc-xs-12 ';
			switch ( $tff['width'] ) {
				case '1/3':
					$classes .= 'wpsc-sm-4 wpsc-md-4 wpsc-lg-4 ';
					break;

				case 'half':
					$classes .= 'wpsc-sm-6 wpsc-md-6 wpsc-lg-6 ';
					break;

				case 'full':
					$classes .= 'wpsc-sm-12 wpsc-md-12 wpsc-lg-12 ';
					break;
			}
			$classes   .= $tff['is-required'] ? 'required ' : '';
			$visibility = WPSC_TFF::get_visibility( $tff, true );
			$classes   .= $visibility ? 'wpsc-hidden conditional' : 'wpsc-visible';

			return $classes;
		}

		/**
		 * Sort an array with key
		 *
		 * @param array $array - macro array.
		 * @param array $on - title.
		 * @param array $order - SORT_ASC.
		 * @return array
		 */
		public static function array_sort( $array, $on, $order = SORT_ASC ) {

			$new_array      = array();
			$sortable_array = array();

			if ( count( $array ) > 0 ) {
				foreach ( $array as $k => $v ) {
					if ( is_array( $v ) ) {
						foreach ( $v as $k2 => $v2 ) {
							if ( $k2 == $on ) {
								$sortable_array[ $k ] = $v2;
							}
						}
					} else {
						$sortable_array[ $k ] = $v;
					}
				}
				switch ( $order ) {
					case SORT_ASC:
						asort( $sortable_array );
						break;
					case SORT_DESC:
						arsort( $sortable_array );
						break;
				}
				foreach ( $sortable_array as $k => $v ) {
					$new_array[ $k ] = $array[ $k ];
				}
			}
			return $new_array;
		}

		/**
		 * Convert date from WP timezone date to UTC equivalant date
		 *
		 * @param string $date_str - date to UTC equivalant date.
		 * @return DateTime
		 */
		public static function get_utc_date_str( $date_str ) {

			$tz   = wp_timezone();
			$date = DateTime::createFromFormat( 'Y-m-d H:i:s', $date_str, $tz );
			$date->setTimezone( new DateTimeZone( '+0000' ) );
			return $date->format( 'Y-m-d H:i:s' );
		}

		/**
		 * Get new ticket url
		 *
		 * @return string
		 */
		public static function get_new_ticket_url() {

			$page_settings = get_option( 'wpsc-gs-page-settings' );
			$url           = '';

			if ( $page_settings['new-ticket-page'] == 'default' && $page_settings['support-page'] ) {

				$url = get_permalink( $page_settings['support-page'] );
				$url = add_query_arg( array( 'wpsc-section' => 'new-ticket' ), $url );

			} elseif ( $page_settings['new-ticket-page'] == 'custom' && $page_settings['new-ticket-url'] ) {

				$url = $page_settings['new-ticket-url'];
			}

			return apply_filters( 'wpsc_get_new_ticket_url', $url );
		}

		/**
		 * Load package for third-party php library without composer
		 *
		 * @param string $dir - directory path of package.
		 * @return void
		 */
		public static function load_library( $dir ) {

			$composer   = json_decode( file_get_contents( "$dir/composer.json" ), 1 ); // phpcs:ignore
			$namespaces = $composer['autoload']['psr-4'];

			// Foreach namespace specified in the composer, load the given classes.
			foreach ( $namespaces as $namespace => $classpaths ) {
				if ( ! is_array( $classpaths ) ) {
					$classpaths = array( $classpaths );
				}
				spl_autoload_register(
					function ( $classname ) use ( $namespace, $classpaths, $dir ) {
						// Check if the namespace matches the class we are looking for.
						if ( preg_match( '#^' . preg_quote( $namespace, '/' ) . '#', $classname ) ) {
							// Remove the namespace from the file path since it's psr4.
							$classname = str_replace( $namespace, '', $classname );
							$filename  = preg_replace( '#\\\\#', '/', $classname ) . '.php';
							foreach ( $classpaths as $classpath ) {
								$fullpath = $dir . '/' . $classpath . "/$filename";
								if ( file_exists( $fullpath ) ) {
									include_once $fullpath;
								}
							}
						}
					}
				);
			}
		}

		/**
		 * Create a random string
		 *
		 * @param integer $length -  random strig lenght.
		 * @return string
		 */
		public static function get_random_string( $length = 8 ) {

			$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
			$string     = '';
			for ( $i = 0; $i < $length; $i++ ) {
				$string .= $characters[ wp_rand( 0, strlen( $characters ) - 1 ) ];
			}
			return $string;
		}

		/**
		 * Perform sum of date intervals
		 *
		 * @param array $arr - date interval array.
		 * @return DateInterval
		 */
		public static function date_interval_sum( $arr ) {

			$response    = $arr[0];
			$arrau_count = count( $arr );
			for ( $i = 1; $i < $arrau_count; $i++ ) {
				$today     = new DateTime();
				$sum_today = clone $today;
				$sum_today->add( $response );
				$sum_today->add( $arr[ $i ] );
				$response = $today->diff( $sum_today );
			}
			return $response;
		}

		/**
		 * Return string representation of date interval
		 *
		 * @param DateInterval $diff - date interval.
		 * @return string
		 */
		public static function date_interval_to_string( $diff ) {

			$str = 'P';

			if ( $diff->days ) {

				$str .= $diff->format( '%aD' );

			} elseif ( $diff->d ) {

				$str .= $diff->format( '%dD' );

			}

			if ( $diff->h || $diff->i ) {

				$str .= 'T';
				$str .= $diff->h ? $diff->format( '%hH' ) : '';
				$str .= $diff->i ? $diff->format( '%iM' ) : '';
			}

			if ( $str === 'P' ) {
				$str = 'PT0M';
			}

			return $str;
		}

		/**
		 * Return readable time interval so that we can print this on user interface.
		 *
		 * @param DateInterval $diff - time interval.
		 * @return string
		 */
		public static function date_interval_to_readable( $diff ) {

			$arr = array();

			// days.
			if ( $diff->days ) {

				$arr[] = sprintf(
					/* translators: %d: number of days */
					__( '%dd', 'supportcandy' ),
					$diff->format( '%a' )
				);

			} elseif ( $diff->d ) {

				$arr[] = sprintf(
					/* translators: %d: number of days */
					__( '%dd', 'supportcandy' ),
					$diff->format( '%d' )
				);

			}

			// hours.
			if ( $diff->h ) {
				$arr[] = sprintf(
					/* translators: %d: number of hours */
					__( '%dh', 'supportcandy' ),
					$diff->format( '%h' )
				);
			}

			// minutes.
			if ( $diff->i ) {
				$arr[] = sprintf(
					/* translators: %d: number of minutes */
					__( '%dm', 'supportcandy' ),
					$diff->format( '%i' )
				);
			}

			return $arr ? implode( ' ', $arr ) : '0m';
		}

		/**
		 * Return time interval object from readable string (usually comes from user interface)
		 *
		 * @param string $str - string to date format.
		 * @return DateInterval
		 */
		public static function readable_to_date_interval( $str ) {

			// remove spaces in between.
			$str = str_replace( ' ', '', $str );

			// invalid if not given.
			if ( ! $str ) {
				return false;
			}

			// validate format.
			$flag = preg_match( '/^(\d*d)?(\d*h)?(\d*m)?$/', $str, $matches );
			if ( ! $flag ) {
				return false;
			}

			// build interval string.
			$str  = 'P';
			$str .= $matches[1] ? strtoupper( $matches[1] ) : '';
			if ( $matches[2] || $matches[3] ) {
				$str .= 'T';
				$str .= $matches[2] ? strtoupper( $matches[2] ) : '';
				$str .= $matches[3] ? strtoupper( $matches[3] ) : '';
			}

			// return dateinterval object.
			return new DateInterval( $str );
		}

		/**
		 * Return time ago string for highest unit. For example, if difference is 2hr 30min 34sec then return 2 hour ago.
		 *
		 * @param DateInterval $diff - date interval object.
		 * @return string
		 */
		public static function date_interval_highest_unit_ago( $diff ) {

			// return years if any.
			if ( $diff->y ) {
				return sprintf(
					/* translators: %d: number of years */
					__( '%d years ago', 'supportcandy' ),
					intval( $diff->format( '%y' ) )
				);
			}

			// return months if any.
			if ( $diff->m ) {
				return sprintf(
					/* translators: %d: number of months */
					__( '%d months ago', 'supportcandy' ),
					intval( $diff->format( '%m' ) )
				);
			}

			// return days if any.
			$days = $diff->days ? intval( $diff->format( '%a' ) ) : intval( $diff->format( '%d' ) );
			if ( $days ) {
				return sprintf(
					/* translators: %d: number of days */
					__( '%d days ago', 'supportcandy' ),
					$days
				);
			}

			// return hours if any.
			if ( $diff->h ) {
				return sprintf(
					/* translators: %d: number of hours */
					__( '%d hours ago', 'supportcandy' ),
					intval( $diff->format( '%h' ) )
				);
			}

			// return minutes if any.
			if ( $diff->i ) {
				return sprintf(
					/* translators: %d: number of minutes */
					__( '%d minutes ago', 'supportcandy' ),
					intval( $diff->format( '%i' ) )
				);
			}

			// return seconds if any.
			if ( $diff->s ) {
				return sprintf(
					/* translators: %d: number of seconds */
					__( '%d seconds ago', 'supportcandy' ),
					intval( $diff->format( '%s' ) )
				);
			}

			return __( 'Just now', 'supportcandy' );
		}

		/**
		 * Return day name
		 *
		 * @param int $day - week days.
		 * @return string
		 */
		public static function get_day_name( $day ) {

			$days = array(
				1 => wpsc__( 'Monday' ),
				2 => wpsc__( 'Tuesday' ),
				3 => wpsc__( 'Wednesday' ),
				4 => wpsc__( 'Thursday' ),
				5 => wpsc__( 'Friday' ),
				6 => wpsc__( 'Saturday' ),
				7 => wpsc__( 'Sunday' ),
			);
			return isset( $days[ $day ] ) ? $days[ $day ] : '';
		}

		/**
		 * Return month name
		 *
		 * @param int $month - month names.
		 * @return string
		 */
		public static function get_month_name( $month ) {

			$months = array(
				1  => wpsc__( 'January' ),
				2  => wpsc__( 'February' ),
				3  => wpsc__( 'March' ),
				4  => wpsc__( 'April' ),
				5  => wpsc__( 'May' ),
				6  => wpsc__( 'June' ),
				7  => wpsc__( 'July' ),
				8  => wpsc__( 'August' ),
				9  => wpsc__( 'September' ),
				10 => wpsc__( 'October' ),
				11 => wpsc__( 'November' ),
				12 => wpsc__( 'December' ),
			);
			return isset( $months[ $month ] ) ? $months[ $month ] : '';
		}

		/**
		 * Sanitize SQL key to allow only possible lowercase keys with joins.
		 *
		 * @param string $key - key to sanitize.
		 * @return string
		 */
		public static function sanitize_sql_key( $key ) {

			$sanitized_key = '';

			if ( is_scalar( $key ) ) {
				$key = strtolower( $key );
				if ( preg_match( '/^([a-z]{1,2}\.)?[a-z0-9_]+$/', $key ) ) {
					$sanitized_key = $key;
				}
			}

			return $sanitized_key;
		}

		/**
		 * Sanitize date string e.g. "2022-12-25" and return with datetime format
		 *
		 * @param string $date - date string to be sanitized.
		 * @return string
		 */
		public static function sanitize_date( $date ) {

			if ( ! $date ) {
				return $date;
			}

			if ( ! preg_match( '/\d{4}-\d{2}-\d{2}/', $date ) ) {
				return '';
			}

			$format = 'Y-m-d';
			$d = DateTime::createFromFormat( $format, $date );
			return $d && $d->format( $format ) == $date ? $date . ' 00:00:00' : '';
		}

		/**
		 * Sanitize date string e.g. "2022-12-25 12:00:00"
		 *
		 * @param string $date - date string to be sanitized.
		 * @return string
		 */
		public static function sanitize_datetime( $date ) {

			if ( ! $date ) {
				return $date;
			}

			if ( ! preg_match( '/\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}/', $date ) ) {
				return '';
			}

			$format = 'Y-m-d H:i';
			$d = DateTime::createFromFormat( $format, $date );
			return $d && $d->format( $format ) == $date ? $date . ':00' : '';
		}

		/**
		 * Sanitize time string e.g. "12:31"
		 *
		 * @param string $time - time string to be sanitized.
		 * @return string
		 */
		public static function sanitize_time( $time ) {

			if ( ! $time ) {
				return $time;
			}

			if ( ! preg_match( '/(\d{2}):(\d{2})/', $time, $matches ) ) {
				return '';
			}

			return intval( $matches[1] ) < 24 && intval( $matches[2] ) < 60 ? $time : '';
		}

		/**
		 * Sanitiize email string
		 *
		 * @param string $email - email string to be sanitized.
		 * @return string
		 */
		public static function sanitize_email( $email ) {

			return filter_var( $email, FILTER_VALIDATE_EMAIL ) ? $email : '';
		}

		/**
		 * Sanitiize url string
		 *
		 * @param string $url - email string to be sanitized.
		 * @return string
		 */
		public static function sanitize_url( $url ) {

			return filter_var( $url, FILTER_VALIDATE_URL ) ? $url : '';
		}

		/**
		 * Sanitize attachment id came as user input
		 *
		 * @param integer $id - attachment id.
		 * @return integer|boolean
		 */
		public static function sanitize_attachment( $id ) {

			if ( ! $id ) {
				return false;
			}

			$attachment = new WPSC_Attachment( $id );
			return $attachment->id ? $id : false;
		}

		/**
		 * Sanitize option id provided by user
		 *
		 * @param integer           $id - option id.
		 * @param WPSC_Custom_Field $cf - custom field object.
		 * @param array             $options - array of option objects.
		 * @return integer|boolean
		 */
		public static function sanitize_option( $id, $cf, $options ) {

			$option = new WPSC_Option( $id );
			$options = array_filter(
				array_map(
					fn( $option ) => $option->id ? $option->id : false,
					$options
				)
			);
			return $option->id && in_array( $option->id, $options ) ? $id : false;
		}

		/**
		 * Get current langauge iso code. Use for datepicker library.
		 *
		 * @return string
		 */
		public static function get_locale_iso() {

			$locale = substr( get_locale(), 0, 2 );
			if ( $locale == 'el' ) {
				$locale = 'gr';
			}
			return $locale;
		}
	}
endif;

WPSC_Functions::init();
