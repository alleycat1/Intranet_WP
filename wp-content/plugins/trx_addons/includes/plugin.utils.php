<?php
/**
 * PHP utilities
 *
 * @package ThemeREX Addons
 * @since v1.0
 */

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }



/* Arrays manipulations
----------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'trx_addons_array_get_first' ) ) {
	/**
	 * Return a first key (by default) or a value from associative array
	 * 
	 * @param array $arr  array to process
	 * @param bool  $key  return key (true) or value (false)
	 * 
	 * @return mixed      first key or value from array
	 */
	function trx_addons_array_get_first( $arr, $key = true ) {
		$rez = false;
		foreach ( $arr as $k => $v ) {
			$rez = $key ? $k : $v;
			break;
		}
		return $rez;
	}
}

if ( ! function_exists( 'trx_addons_array_get_first_key' ) ) {
	/**
	 * Return a first key from associative array
	 * 
	 * @param array $arr  array to process
	 * 
	 * @return mixed      first key from array
	 */
	function trx_addons_array_get_first_key( $arr ) {
		return trx_addons_array_get_first( $arr, true );
	}
}

if ( ! function_exists( 'trx_addons_array_get_first_value' ) ) {
	/**
	 * Return a first value from associative array
	 * 
	 * @param array $arr  array to process
	 * 
	 * @return mixed      first value from array
	 */
	function trx_addons_array_get_first_value( $arr ) {
		return trx_addons_array_get_first( $arr, false );
	}
}

if ( ! function_exists( 'trx_addons_array_get_keys_by_value' ) ) {
	/**
	 * Return keys by value from associative string: categories=1|author=0|date=0|counters=1...
	 * 
	 * @param array $arr    An array to return keys with a specified value.
	 * @param mixed $value  A value to search for. If null - return all keys from array. Default: 1.
	 * 
	 * @return array        An array of keys for value found in the array.
	 */
	function trx_addons_array_get_keys_by_value( $arr, $value = 1 ) {
		if ( ! is_array( $arr ) ) {
			parse_str( str_replace( '|', '&', $arr ), $arr );
		}
		return $value != null ? array_keys( $arr, $value ) : array_keys( $arr );
	}
}

if ( ! function_exists( 'trx_addons_array_delete_by_value' ) ) {
	/**
	 * Delete items by value from an array (any type). All entries equal to value will be removed.
	 *
	 * @param array $arr    An array to return keys with a specified value.
	 * @param mixed $value  A value to delete.
	 *
	 * @return array        A processed array without items equals to a value.
	 */
	function trx_addons_array_delete_by_value( $arr, $value ) {
		foreach( (array)$value as $v ) {
			do {
				$key = array_search( $v, $arr );
				if ( false !== $key ) {
					unset( $arr[ $key ] );
				}
			} while ( false !== $key );
		}
		return $arr;
	}
}

if ( ! function_exists( 'trx_addons_array_delete_by_subkey' ) ) {
	/**
	 * Delete items by a subkey value from an array (any type). All entries equal to value will be removed.
	 *
	 * @param array $arr    An array to return keys with a specified value.
	 * @param mixed $subkey A subkey to search for.
	 * @param mixed $value  A value to delete.
	 *
	 * @return array        A processed array without items equals to a value.
	 */
	function trx_addons_array_delete_by_subkey( $arr, $subkey, $value ) {
		foreach( (array)$value as $v ) {
			$arr = array_filter( $arr, function( $a ) use( $subkey, $v ) {
				return $a[ $subkey ] != $v;
			} );
		}
		return $arr;
	}
}

if ( ! function_exists( 'trx_addons_array_from_list' ) ) {
	/**
	 * Convert a list to associative array [ 'key' => 'value', ...] from:
	 * - scalar list ['val1', 'val2', ...] - make pairs 'val1' => 'val1', 'val2' => 'val2', ...
	 * - list of arrays [ [ 'id' => 'key', 'title' => 'value'], ...]
	 * - associative array [ 'key' => [ 'title' => 'value'], ...]
	 * 
	 * @param array $arr  array to process
	 * 
	 * @return array      associative array
	 */
	function trx_addons_array_from_list( $arr ) {
		$new = array();
		foreach ( $arr as $k => $v ) {
			if ( is_array( $v ) && ! empty( $v['title'] ) ) {
				$new[ ! empty( $v['id'] ) ? $v['id'] : $k ] = $v['title'];
			} else {
				$new[ $v ] = $v;
			}
		}
		return $new;
	}
}

if ( ! function_exists( 'trx_addons_list_from_array' ) ) {
	/**
	 * Convert an associative array [ 'key' => 'value', ... ] or [ 'key' => [ 'title' => 'value' ], ... ]
	 * to list of arrays [ ['id' => 'key', 'title' => 'value'], ... ]
	 * 
	 * @param array $arr  array to process
	 * 
	 * @return list      list of ['id' => 'key', 'title' => 'value']
	 */
	function trx_addons_list_from_array( $arr ) {
		$new = array();
		foreach ( $arr as $k => $v ) {
			$new[] = array(
				'id' => $k,
				'title' => is_array( $v ) && isset( $v['title'] ) ? $v['title'] : $v
			);
		}
		return $new;
	}
}

if ( ! function_exists( 'trx_addons_array_make_string_keys' ) ) {
	/**
	 * Convert a list or array with numeric keys to the associative array with string keys
	 * 
	 * @param array $arr  array to process
	 * 
	 * @return array      associative array with string keys
	 */
	function trx_addons_array_make_string_keys( $arr ) {
		$new = array();
		foreach ( $arr as $k => $v ) {
			$new[ "{$k} " ] = $v;
		}
		return $new;
	}
}

if ( ! function_exists( 'trx_addons_array_get_values' ) ) {
	/**
	 * Return all values from multidimensional array
	 * 
	 * @param array $arr  array to process
	 * 
	 * @return array      list of values
	 */
	function trx_addons_array_get_values( $arr ) {
		$new = array();
		foreach ( $arr as $v ) {
			if ( is_array( $v )) {
				$new = array_merge( $new, trx_addons_array_get_values( $v ) );
			} else {
				$new[] = $v;
			}
		}
		return $new;
	}
}

if ( ! function_exists( 'trx_addons_array_search' ) ) {
	/**
	 * Search value in multidimensional array by a value of specified subkey
	 * 
	 * @param array  $arr    array to process
	 * @param string $subkey subkey to search
	 * @param string $value  value to search
	 * 
	 * @return mixed         key of found element or false
	 */
	function trx_addons_array_search( $arr, $subkey, $value, $return_key = '' ) {
		foreach ( $arr as $k => $v ) {
			if ( is_array( $v ) && isset( $v[ $subkey ] ) && $v[ $subkey ] == $value ) {
				return ! empty( $return_key ) ? $v[ $return_key ] : $k;
			}
			if ( is_object( $v ) && isset( $v->$subkey ) && $v->$subkey == $value ) {
				return ! empty( $return_key ) ? $v->$return_key : $k;
			}
		}
		return false;
	}
}


if ( ! function_exists( 'trx_addons_array_slice' ) ) {
	/**
	 * Return part of array from key = $from to key = $to
	 * 
	 * @param array  $arr  array to process
	 * @param string $from start key
	 * @param string $to   end key. If empty - return all keys from $from to the end.
	 * 
	 * @return array       sliced array
	 */
	function trx_addons_array_slice( $arr, $from, $to = '' ) {
		if ( is_array( $arr ) && count( $arr ) > 0 && ( ! empty( $from ) || ! empty( $to ) ) ) {
			$arr_new  = array();
			$copy     = empty( $from );
			$from_inc = false;
			$to_inc   = false;
			if ( substr( $from, 0, 1) == '+' ) {
				$from_inc = true;
				$from     = substr( $from, 1 );
			}
			if ( substr( $to, 0, 1) == '+' ) {
				$to_inc = true;
				$to     = substr( $to, 1 );
			}
			foreach ( $arr as $k => $v ) {
				if ( ! empty( $from ) && $k == $from ) {
					$copy = true;
					if ( ! $from_inc ) {
						continue;
					}
				}
				if ( ! empty( $to ) && $k == $to ) {
					if ( $copy && $to_inc ) {
						$arr_new[ $k ] = $v;
					}
					break;
				}
				if ( $copy ) {
					$arr_new[ $k ] = $v;
				}
			}
			$arr = $arr_new;
		}
		return $arr;
	}
}

if ( ! function_exists( 'trx_addons_array_merge' ) ) {
	/**
	 * Merge arrays and lists (preserve number indexes)
	 * For example: $a = array("one", "k2"=>"two", "three");
	 *              $b = array("four", "k1"=>"five", "k2"=>"six", "seven");
	 *              $c = array_merge($a, $b);				// ["one", "k2"=>"six", "three", "four", "k1"=>"five", "seven");
	 *              $d = trx_addons_array_merge($a, $b);	// ["four", "k2"=>"six", "seven", "k1"=>"five");
	 * 
	 * @param array $a1  first array (or list) to merge
	 * @param array $a2  second array (or list) to merge
	 * 
	 * @return array     merged array
	 */
	function trx_addons_array_merge( $a1, $a2 ) {
		for ($i = 1; $i < func_num_args(); $i++){
			$arg = func_get_arg( $i );
			if ( is_array( $arg ) && count( $arg ) > 0 ) {
				foreach( $arg as $k => $v ) {
					$a1[ $k ] = $v;
				}
			}
		}
		return $a1;
	}
}

if ( ! function_exists( 'trx_addons_array_insert_after' ) ) {
	/**
	 * Inserts any number of scalars or arrays at the point in the haystack immediately after the search key ($needle) was found,
	 * or at the end if the needle is not found or not supplied.
	 * Modifies $haystack in place.
	 * 
	 * @param array  &$haystack the associative array to search. This will be modified by the function
	 * @param string $needle    the key to search for
	 * @param mixed  $stuff     one or more arrays or scalars to be inserted into $haystack
	 * 
	 * @return int              the index at which $needle was found
	 */
	function trx_addons_array_insert_after( &$haystack, $needle, $stuff ) {
		if ( ! is_array( $haystack ) ) {
			return -1;
		}
		$new_array = array();
		for ( $i = 2; $i < func_num_args(); $i++ ) {
			$arg = func_get_arg( $i );
			if ( is_array( $arg ) ) {
				if ( $i == 2 ) {
					$new_array = $arg;
				} else {
					$new_array = trx_addons_array_merge( $new_array, $arg );
				}
			} else {
				$new_array[] = $arg;
			}
		}
		$i = 0;
		if ( is_array( $haystack ) && count( $haystack ) > 0 ) {
			foreach( $haystack as $key => $value ) {
				$i++;
				if ( $key == $needle ) {
					break;
				}
			}
		}
		$haystack = is_int( $needle )
						? array_merge( array_slice( $haystack, 0, $i, true ), $new_array, array_slice( $haystack, $i, null, true ) )
						: trx_addons_array_merge( array_slice( $haystack, 0, $i, true ), $new_array, array_slice( $haystack, $i, null, true ) );
		return $i;
    }
}

if ( ! function_exists( 'trx_addons_array_insert_before' ) ) {
	/**
	 * Inserts any number of scalars or arrays at the point in the haystack immediately before the search key ($needle) was found,
	 * or at the end if the needle is not found or not supplied.
	 * Modifies $haystack in place.
	 * 
	 * @param array  &$haystack the associative array to search. This will be modified by the function
	 * @param string $needle    the key to search for
	 * @param mixed  $stuff     one or more arrays or scalars to be inserted into $haystack
	 * 
	 * @return int              the index at which $needle was found
	 */
	function trx_addons_array_insert_before( &$haystack, $needle, $stuff ) {
		if ( ! is_array( $haystack ) ) {
			return -1;
		}
		$new_array = array();
		for ( $i = 2; $i < func_num_args(); $i++ ) {
			$arg = func_get_arg( $i );
			if ( is_array( $arg ) ) {
				if ( $i == 2 ) {
					$new_array = $arg;
				} else {
					$new_array = trx_addons_array_merge( $new_array, $arg );
				}
			} else {
				$new_array[] = $arg;
			}
		}
		$i = 0;
		if ( is_array( $haystack ) && count( $haystack ) > 0 ) {
			foreach ( $haystack as $key => $value ) {
				if ( $key == $needle ) {
					break;
				}
				$i++;
			}
		}
		$haystack = is_int( $needle )
						? array_merge( array_slice( $haystack, 0, $i, true ), $new_array, array_slice( $haystack, $i, null, true ) )
						: trx_addons_array_merge( array_slice( $haystack, 0, $i, true ), $new_array, array_slice( $haystack, $i, null, true ) );
		return $i;
	}
}


/* Colors manipulations
----------------------------------------------------------------------------------------------------- */
if ( ! function_exists( 'trx_addons_hex2rgb' ) ) {
	/**
	 * Convert hex color to rgb
	 * 
	 * @param string $hex  color in hex format
	 * 
	 * @return array       array with 3 elements: r, g, b
	 */
	function trx_addons_hex2rgb( $hex ) {
		$dec = hexdec( substr( $hex, 0, 1 ) == '#' ? substr( $hex, 1 ) : $hex );
		return array(
				'r'=> $dec >> 16,
				'g'=> ( $dec & 0x00FF00 ) >> 8,
				'b'=> $dec & 0x0000FF
				);
	}
}

if ( ! function_exists( 'trx_addons_hex2rgba' ) ) {
	/**
	 * Convert hex color to rgba
	 * 
	 * @param string $hex    color in hex format
	 * @param string $alpha  alpha channel (0-1)
	 * 
	 * @return array         css string with rgba color
	 */
	function trx_addons_hex2rgba( $hex, $alpha ) {
		$rgb = trx_addons_hex2rgb( $hex );
		return 'rgba(' . $rgb['r'] . ',' . $rgb['g'] . ',' . $rgb['b'] . ',' . $alpha . ')';
	}
}

if ( ! function_exists( 'trx_addons_hex2hsb' ) ) {
	/**
	 * Convert hex color to hsb
	 * 
	 * @param string $hex  color in hex format
	 * @param string $h    hue shift (0-359). 0 - red, 120 - green, 240 - blue
	 * @param string $s    saturation shift (0-100). 0 - gray, 100 - full color
	 * @param string $b    brightness shift (0-100). 0 - black, 100 - white
	 * 
	 * @return array       array with 3 elements: h, s, b
	 */
	function trx_addons_hex2hsb( $hex, $h = 0, $s = 0, $b = 0 ) {
		$hsb = trx_addons_rgb2hsb(trx_addons_hex2rgb($hex));
		$hsb['h'] = min(359, max(0, $hsb['h'] + $h));
		$hsb['s'] = min(100, max(0, $hsb['s'] + $s));
		$hsb['b'] = min(100, max(0, $hsb['b'] + $b));
		return $hsb;
	}
}

if ( ! function_exists( 'trx_addons_rgb2hsb' ) ) {
	/**
	 * Convert array with rgb color (keys 'r', 'g', 'b') to hsb color (keys 'h', 's', 'b')
	 * 
	 * @param array $rgb  array with 3 elements: r, g, b
	 * 
	 * @return array      array with 3 elements: h, s, b
	 */
	function trx_addons_rgb2hsb( $rgb ) {
		$hsb = array();
		$hsb['b'] = max( max( $rgb['r'], $rgb['g'] ), $rgb['b'] );
		$hsb['s'] = $hsb['b'] <= 0 ? 0 : round( 100 * ( $hsb['b'] - min( min( $rgb['r'], $rgb['g'] ), $rgb['b'] ) ) / $hsb['b'] );
		$hsb['b'] = round( ( $hsb['b'] / 255 ) * 100 );
		if (      $rgb['r'] == $rgb['g'] && $rgb['g'] == $rgb['b'] ) $hsb['h'] = 0;
		else if ( $rgb['r'] >= $rgb['g'] && $rgb['g'] >= $rgb['b'] ) $hsb['h'] =       60 * ( $rgb['g'] - $rgb['b'] ) / ( $rgb['r'] - $rgb['b'] );
		else if ( $rgb['g'] >= $rgb['r'] && $rgb['r'] >= $rgb['b'] ) $hsb['h'] = 60  + 60 * ( $rgb['g'] - $rgb['r'] ) / ( $rgb['g'] - $rgb['b'] );
		else if ( $rgb['g'] >= $rgb['b'] && $rgb['b'] >= $rgb['r'] ) $hsb['h'] = 120 + 60 * ( $rgb['b'] - $rgb['r'] ) / ( $rgb['g'] - $rgb['r'] );
		else if ( $rgb['b'] >= $rgb['g'] && $rgb['g'] >= $rgb['r'] ) $hsb['h'] = 180 + 60 * ( $rgb['b'] - $rgb['g'] ) / ( $rgb['b'] - $rgb['r'] );
		else if ( $rgb['b'] >= $rgb['r'] && $rgb['r'] >= $rgb['g'] ) $hsb['h'] = 240 + 60 * ( $rgb['r'] - $rgb['g'] ) / ( $rgb['b'] - $rgb['g'] );
		else if ( $rgb['r'] >= $rgb['b'] && $rgb['b'] >= $rgb['g'] ) $hsb['h'] = 300 + 60 * ( $rgb['r'] - $rgb['b'] ) / ( $rgb['r'] - $rgb['g'] );
		else $hsb['h'] = 0;
		$hsb['h'] = round( $hsb['h'] );
		return $hsb;
	}
}

if ( ! function_exists( 'trx_addons_hsb2rgb' ) ) {
	/**
	 * Convert array with hsb color (keys 'h', 's', 'b') to rgb color (keys 'r', 'g', 'b')
	 * 
	 * @param array $hsb  array with 3 elements: h, s, b
	 * 
	 * @return array      array with 3 elements: r, g, b
	 */
	function trx_addons_hsb2rgb( $hsb ) {
		$rgb = array();
		$h = round( $hsb['h'] );
		$s = round( $hsb['s'] * 255 / 100 );
		$v = round( $hsb['b'] * 255 / 100 );
		if ( $s == 0 ) {
			$rgb['r'] = $rgb['g'] = $rgb['b'] = $v;
		} else {
			$t1 = $v;
			$t2 = ( 255 - $s ) * $v / 255;
			$t3 = ( $t1 - $t2 ) * ( $h % 60 ) / 60;
			if ( $h == 360 ) $h = 0;
			if (      $h <  60 ) { 	$rgb['r'] = $t1; $rgb['b'] = $t2; $rgb['g'] = $t2 + $t3; }
			else if ( $h < 120 ) {	$rgb['g'] = $t1; $rgb['b'] = $t2; $rgb['r'] = $t1 - $t3; }
			else if ( $h < 180 ) {	$rgb['g'] = $t1; $rgb['r'] = $t2; $rgb['b'] = $t2 + $t3; }
			else if ( $h < 240 ) {	$rgb['b'] = $t1; $rgb['r'] = $t2; $rgb['g'] = $t1 - $t3; }
			else if ( $h < 300 ) {	$rgb['b'] = $t1; $rgb['g'] = $t2; $rgb['r'] = $t2 + $t3; }
			else if ( $h < 360 ) {	$rgb['r'] = $t1; $rgb['g'] = $t2; $rgb['b'] = $t1 - $t3; }
			else {					$rgb['r'] = 0;   $rgb['g'] = 0;   $rgb['b'] = 0; }
		}
		return array(
				'r' => round( $rgb['r'] ),
				'g' => round( $rgb['g'] ),
				'b' => round( $rgb['b'] )
				);
	}
}

if ( ! function_exists( 'trx_addons_rgb2hex' ) ) {
	/**
	 * Convert array with rgb color (keys 'r', 'g', 'b') to hex color (string)
	 * 
	 * @param array $rgb  array with 3 elements: r, g, b. Each element must be in range 0..255
	 * 
	 * @return string     hex color
	 */
	function trx_addons_rgb2hex( $rgb ) {
		$hex = array(
			dechex( $rgb['r'] ),
			dechex( $rgb['g'] ),
			dechex( $rgb['b'] )
		);
		return '#' . ( strlen( $hex[0] ) == 1 ? '0' : '' ) . $hex[0]
				. ( strlen( $hex[1] ) == 1 ? '0' : '' ) . $hex[1]
				. ( strlen( $hex[2] ) == 1 ? '0' : '' ) . $hex[2];
	}
}

if ( ! function_exists( 'trx_addons_hsb2hex' ) ) {
	/**
	 * Convert array with hsb color (keys 'h', 's', 'b') to hex color (string)
	 * 
	 * @param array $hsb  array with 3 elements: h, s, b
	 * 
	 * @return string     hex color
	 */
	function trx_addons_hsb2hex( $hsb ) {
		return trx_addons_rgb2hex( trx_addons_hsb2rgb( $hsb ) );
	}
}






/* Date manipulations
----------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'trx_addons_date_to_sql' ) ) {
	/**
	 * Convert date from the format 'dd.mm.YYYY' to SQL format 'YYYY-mm-dd'
	 * 
	 * @param string $str  date in the format 'dd.mm.YYYY'
	 * 
	 * @return string      date in the format 'YYYY-mm-dd'
	 */
	function trx_addons_date_to_sql( $str ) {
		if ( trim( $str ) == '' ) {
			return '';
		}
		$str = strtr( trim( $str ), '/\.,', '----' );
		if ( trim( $str ) == '00-00-0000' || trim( $str ) == '00-00-00' ) {
			return '';
		}
		$pos = strpos( $str, '-' );
		if ( $pos > 3 ) {
			return $str;
		}
		$d = trim( substr( $str, 0, $pos ) );
		$str = substr( $str, $pos + 1 );
		$pos = strpos( $str, '-' );
		$m = trim( substr( $str, 0, $pos ) );
		$y = trim( substr( $str, $pos + 1 ) );
		$y = $y < 50
				? $y + 2000
				: ( $y < 1900
					? $y + 1900
					: $y
				);
		return '' . $y . '-' . ( strlen( $m ) < 2 ? '0' : '' ) . $m . '-' . ( strlen( $d ) < 2 ? '0' : '' ) . $d;
	}
}






/* Numbers manipulations
----------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'trx_addons_format_price' ) ) {
	/**
	 * Format price. Add thousands separator and decimal part
	 * 
	 * @param float $price  price
	 * 
	 * @return string       formatted price 
	 */
	function trx_addons_format_price( $price ) {
		$thousands_separator = apply_filters( 'trx_addons_filter_thousands_separator',
											trx_addons_exists_woocommerce()
												? get_option( 'woocommerce_price_thousand_sep', ' ' )
												: ' '
											);
		$decimals_separator  = apply_filters( 'trx_addons_filter_decimals_separator',
											trx_addons_exists_woocommerce()
												? get_option( 'woocommerce_price_decimal_sep', '.' )
												: '.'
											);
		$num_decimals        = apply_filters( 'trx_addons_filter_num_decimals',
											trx_addons_exists_woocommerce()
												? get_option( 'woocommerce_price_num_decimals', 2 )
												: 2
											);
		return apply_filters( 'trx_addons_filter_format_price',
				is_numeric( $price ) 
					? ( $price != round( $price, 0 )
						? number_format( round( $price, $num_decimals ), $num_decimals, $decimals_separator, $thousands_separator )
						: number_format( $price, 0, $decimals_separator, $thousands_separator )
						)
					: $price,
				$price
				);
	}
}

if ( ! function_exists( 'trx_addons_num2kilo' ) ) {
	/**
	 * Convert number to string with suffix K(ilo)
	 * 
	 * @param int $num  number
	 * @param int $precision  precision. Default: 0
	 * 
	 * @return string   string with suffix
	 */
	function trx_addons_num2kilo( $num, $precision = 0 ) {
		$num = intval( str_replace( ' ', '', $num ) );
		return $num > 1000 ? round( $num / 1000, $precision ) . 'K' : $num;
	}
}

if ( ! function_exists( 'trx_addons_num2size' ) ) {
	/**
	 * Convert number (size in bytes) to string with suffix B(ytes)|K(ilo)|M(ega)|G(iga)|T(era)|P(enta).
	 * For example: 10543 -> 10K
	 * 
	 * @param int $num        number
	 * @param int $precision  precision. Default: 0
	 * 
	 * @return string         string with suffix
	 */
	function trx_addons_num2size( $num, $precision = 0 ) { 
		$num   = intval( str_replace( ' ', '', $num ) );
		$units = array( 'B', 'K', 'M', 'G', 'T', 'P' ); 
		$num   = max( $num, 0 ); 
		$pow   = floor( ( $num ? log( $num ) : 0 ) / log( 1024 ) ); 
		$pow   = min( $pow, count( $units ) - 1 ); 
		$num  /= ( 1 << ( 10 * $pow ) ); 
		return round( $num, $precision ) . $units[ $pow ];
	}
}

if ( ! function_exists( 'trx_addons_size2num' ) ) {
	/**
	 * Convert string with suffix B(ytes)|K(ilo)|M(ega)|G(iga)|T(era)|P(enta) to number (size in bytes).
	 * For example: 10K -> 10240
	 * @param string $size    string with suffix
	 * 
	 * @return int            number
	 */
	function trx_addons_size2num( $size ) {
		$size = str_replace( ' ', '', $size );
		$suff = strtoupper( substr( $size, -1 ) );
		$pos  = strpos( 'KMGTP', $suff );
		if ( $pos !== false ) {
			$size = intval( substr( $size, 0, -1 ) ) * pow( 1024, $pos + 1 );
		}
		return (int)$size;
	}
}

if ( ! function_exists( 'trx_addons_parse_num' ) ) {
	/**
	 * Clear a number - leave only a sign (+/-), digits and a point (.) as a delimiter
	 * 
	 * @param string $str  string to clear
	 * 
	 * @return string      cleared string
	 */
	function trx_addons_parse_num($str) {
		return (float)filter_var( html_entity_decode( strip_tags( $str ) ), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );
	}
}






/* String manipulations
----------------------------------------------------------------------------------------------------- */

if ( ! function_exists('trx_addons_is_on') ) {
	/**
	 * Check if parameter is 'on' or 'true' or 'yes' or '1' or 'show' or true or number
	 * 
	 * @param mixed $prm  parameter to check
	 * 
	 * @return bool       true if parameter is 'on' or 'true' or '1' or 'yes'
	 */
	function trx_addons_is_on( $prm ) {
		return ( is_bool( $prm ) && $prm === true )
				|| ( is_numeric( $prm ) && $prm > 0 )
				|| in_array( strtolower( $prm ), array( '1', 'true', 'on', 'yes', 'show' )
				);
	}
}

if ( ! function_exists('trx_addons_is_off') ) {
	/**
	 * Check if parameter is 'off' or 'false' or '0' or 'no' or 'none' or 'hide' or false or empty or 0
	 * 
	 * @param mixed $prm  parameter to check
	 * 
	 * @return bool       true if parameter is 'off' or 'false' or '0' or 'no'
	 */
	function trx_addons_is_off( $prm ) {
		return empty( $prm )
				|| ( is_numeric( $prm ) && $prm === 0 )
				|| in_array( strtolower( $prm ), array( '0', 'false', 'off', 'no', 'none', 'hide' ) );
	}
}

if ( ! function_exists('trx_addons_is_inherit') ) {
	/**
	 * Check if parameter is 'inherit'
	 * 
	 * @param string $prm  parameter to check
	 * 
	 * @return bool       true if parameter is 'inherit'
	 */
	function trx_addons_is_inherit( $prm ) {
		return in_array( strtolower( $prm ), array( 'inherit' ) );
	}
}

if ( ! function_exists('trx_addons_str_replace') ) {
	/**
	 * Replace text in the string (or array of strings) with specified parameters. The function support serialized arrays.
	 * 
	 * @param string|array $from  what need to replace
	 * @param string|array $to    what need to replace to
	 * @param string|array $str   string or array of strings
	 * 
	 * @return string|array       result string or array of strings
	 */
	function trx_addons_str_replace( $from, $to, $str ) {
		if ( is_array( $str ) ) {
			foreach ( $str as $k => $v ) {
				$str[ $k ] = trx_addons_str_replace( $from, $to, $v );
			}
		} else if ( is_object( $str ) ) {
			if ( '__PHP_Incomplete_Class' !== get_class( $str ) ) {
				foreach ( $str as $k => $v ) {
					$str->{$k} = trx_addons_str_replace( $from, $to, $v );
				}
			}
		} else if ( is_string( $str ) ) {
			if ( is_serialized( $str ) ) {
				$str = serialize( trx_addons_str_replace( $from, $to, trx_addons_unserialize( $str ) ) );
			} else {
				$str = str_replace( $from, $to, $str );
			}
		}
		return $str;
	}
}

if ( ! function_exists('trx_addons_str_replace_once') ) {
	/**
	 * Replace text in the string (or array of strings) with specified parameters only once.
	 * Uses only the first encountered substitution from the list.
	 * 
	 * @param string|array $from  what need to replace
	 * @param string|array $to    what need to replace to
	 * @param string|array $str   string or array of strings
	 * 
	 * @return string|array       result string or array of strings
	 */
	function trx_addons_str_replace_once( $from, $to, $str ) {
		$rez = '';
		if ( ! is_array( $from ) ) {
			$from = array( $from );
		}
		if ( ! is_array( $to ) ) {
			$to = array( $to );
		}
		for ( $i = 0; $i < strlen( $str ); $i++ ) {
			$found = false;
			for ( $j = 0; $j < count( $from ); $j++ ) {
				if ( substr( $str, $i, strlen( $from[ $j ] ) ) == $from[ $j ] ) {
					$rez .= isset( $to[ $j ] ) ? $to[ $j ] : '';
					$found = true;
					$i += strlen( $from[ $j ] ) - 1;
					break;
				}
			}
			if ( ! $found ) {
				$rez .= $str[ $i ];
			}
		}
		return $rez;
	}
}


if ( ! function_exists( 'trx_addons_strip_tags' ) ) {
	/**
	 * Strip non-text tags and remove comments from the string.
	 *
	 * @param string|array $str  string to strip tags from
	 *
	 * @return string|array      result string
	 */
	function trx_addons_strip_tags( $str ) {
		// remove comments and any content found in the the comment area (strip_tags only removes the actual tags).
		$str = preg_replace( '#<!--.*?-->#s', '', $str );
		// remove all script and style tags
		$str = preg_replace( '#<(script|style)\b[^>]*>(.*?)</(script|style)>#is', "", $str );
		// remove br tags (missed by strip_tags)
		$str = preg_replace( '#<br[^>]*?>#', ' ', $str );
		// put a space between list items, paragraphs and headings (strip_tags just removes the tags).
		$str = preg_replace( '#</(li|p|span|h1|h2|h3|h4|h5|h6)>#', ' </$1>', $str );
		// remove all remaining html
		$str = strip_tags( $str );
		return trim( $str );
	}
}

if ( ! function_exists( 'trx_addons_strshort' ) ) {
	/**
	 * Truncate string to the certain length. If string is shorter - return it as is.
	 * 
	 * @param string $str       string to truncate
	 * @param int $maxlength    max length of the string
	 * @param string $add       what add to the end of truncated string
	 * 
	 * @return string           truncated string
	 */
	function trx_addons_strshort( $str, $maxlength, $add='&hellip;' ) {
		if ( $maxlength <= 0 ) {
			return '';
		}
		$str = trx_addons_strip_tags( $str );
		if ( $maxlength >= strlen( $str ) ) {
			return $str;
		}
		$str = substr( $str, 0, $maxlength - strlen( $add ) );
		$ch  = substr( $str, $maxlength - strlen( $add ), 1 );
		if ( $ch != ' ' ) {
			for ( $i = strlen( $str ) - 1; $i > 0; $i-- ) {
				if ( substr( $str, $i, 1 ) == ' ') {
					break;
				}
			}
			$str = trim( substr( $str, 0, $i ) );
		}
		if ( ! empty( $str ) && strpos( ',.:;-', substr( $str, -1 ) ) !== false ) {
			$str = substr( $str, 0, -1 );
		}
		return "{$str}{$add}";
	}
}

if ( ! function_exists( 'trx_addons_strwords' ) ) {
	/**
	 * Truncate string by words number. If string is shorter - return it as is.
	 * 
	 * @param string $str       string to truncate
	 * @param int $maxlength    number words to leave in the string
	 * @param string $add       what add to the end of truncated string
	 * 
	 * @return string           truncated string
	 */
	function trx_addons_strwords( $str, $maxlength, $add = '&hellip;' ) {
		if ( $maxlength <= 0 ) {
			return '';
		}
		$words = explode( ' ', trx_addons_strip_tags( $str ) );
		if ( count( $words ) > $maxlength ) {
			$words = array_slice( $words, 0, $maxlength );
			$words[ count( $words ) - 1 ] .= $add;
		}
		return join( ' ', $words	);
	}
}

if ( ! function_exists( 'trx_addons_excerpt' ) ) {
	/**
	 * Make excerpt from the html string. Strip tags and truncate a string. If string is shorter - return it as is.
	 * 
	 * @param string $str       string to truncate
	 * @param int $maxlength    max length of the string
	 * @param string $add       what add to the end of truncated string
	 * 
	 * @return string           truncated string
	 */
	function trx_addons_excerpt( $str, $maxlength, $add = '&hellip;' ) {
		if ( $maxlength <= 0 ) {
			return '';
		}
		return trx_addons_strwords( trx_addons_strip_tags( $str ), $maxlength, $add );
	}
}

if ( ! function_exists( 'trx_addons_unserialize' ) ) {
	/**
	 * Unserialize string with check for the serialized data content unrecoverable object (base class for this object is not exists)
	 * 
	 * @param string $str       string to unserialize
	 * 
	 * @return string           unserialized object 
	 */
	function trx_addons_unserialize( $str ) {
		if ( ! empty( $str ) && is_serialized( $str ) ) {
			// If serialized data content unrecoverable object (base class for this object is not exists) - skip this string
			if ( true || ! preg_match( '/O:[0-9]+:"([^"]*)":[0-9]+:{/', $str, $matches ) || empty( $matches[1] ) || class_exists( $matches[1] ) ) {
				// Attempt 1: try unserialize original string
				try {
					$data = unserialize( $str );
				} catch ( Exception $e ) {
					if ( trx_addons_is_on( trx_addons_get_option( 'debug_mode' ) ) ) {
						dcl( $e->getMessage() );
					}
					$data = false;
				}
				// Attempt 2: try unserialize original string without CR symbol '\r'
				if ( false === $data ) {
					try {
						$str2 = str_replace( "\r", "", $str );
						$data = unserialize( $str2 );
					} catch ( Exception $e ) {
						if ( trx_addons_is_on( trx_addons_get_option( 'debug_mode' ) ) ) {
							dcl( $e->getMessage() );
						}
						$data = false;
					}
				}
				// Attempt 3: try unserialize original string with modified character counters
				if ( false === $data ) {
					try {
						$str3 = preg_replace_callback(
								'!s:(\d+):"(.*?)";!',
								function( $match ) {
									return ( strlen( $match[2] ) == $match[1] )
										? $match[0]
										: 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
								},
								$str
							);
						$data = unserialize( $str3 );
					} catch ( Exception $e ) {
						if ( trx_addons_is_on( trx_addons_get_option( 'debug_mode' ) ) ) {
							dcl( $e->getMessage() );
						}
						$data = false;
					}
				}
				// Attempt 4: try unserialize original string without CR symbol '\r' with modified character counters
				if ( false === $data ) {
					try {
						$str3 = preg_replace_callback(
								'!s:(\d+):"(.*?)";!',
								function( $match ) {
									return ( strlen( $match[2] ) == $match[1] )
										? $match[0]
										: 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
								},
								$str2
							);
						$data = unserialize( $str3 );
					} catch ( Exception $e ) {
						if ( trx_addons_is_on( trx_addons_get_option( 'debug_mode' ) ) ) {
							dcl( $e->getMessage() );
						}
						$data = false;
					}
				}
				return $data;
			} else {
				return $str;
			}
		} else {
			return $str;
		}
	}
}

if ( ! function_exists( 'trx_addons_encode_settings' ) ) {
	/**
	 * Serialize and encode settings to add its to the shortcode output
	 *
	 * @param array $settings  An array with settings to encode
	 *
	 * @return string  Encoded string
	 */
	function trx_addons_encode_settings( $args ) {
		$args = serialize( $args );
		for ( $i = 0; $i < strlen( $args ); $i++ ) {
			$args[ $i ] = chr( ord( $args[ $i ] ) - ( $i % 5 ) );
		}
		return $args;
	}
}

if ( ! function_exists( 'trx_addons_decode_settings' ) ) {
	/**
	 * Decode and unserialize string with settings from the shortcode output
	 *
	 * @param string $settings  A string with encoded settings to decode
	 *
	 * @return array  An array with decoded settings
	 */
	function trx_addons_decode_settings( $args ) {
		for ( $i = 0; $i < strlen( $args ); $i++ ) {
			$args[ $i ] = chr( ord( $args[ $i ] ) + ( $i % 5 ) );
		}
		return unserialize( $args );
	}
}

if ( ! function_exists('trx_addons_url_replace') ) {
	/**
	 * Replace URL in the string with new URL (used in the ThemeREX Addons import/export).
	 * Process all variants of the URL: with and without 'http://' and with and without 'www.',
	 * with and without protocol, with and without slash at the end of the string.
	 * 
	 * @param string $from      URL to replace
	 * @param string $to        URL to replace to
	 * @param string $str       string to process
	 * 
	 * @return string           processed string
	 */
	function trx_addons_url_replace($from, $to, $str) {
		if ( substr($from, -1) == '/' ) {
			$from = substr($from, 0, strlen($from)-1);
		}
		if ( substr($to, -1) == '/' ) {
			$to = substr($to, 0, strlen($to)-1);
		}
		$from_clear = trx_addons_remove_protocol($from, true);
		$to_clear = trx_addons_remove_protocol($to, true);
		return trx_addons_str_replace(
					array(
/* 1 */					urlencode("http://{$from_clear}"),						// http%3A%2F%2Fdemo.domain%2Furl
/* 2 */					urlencode("https://{$from_clear}"),						// https%3A%2F%2Fdemo.domain%2Furl
/* 3 */					urlencode($from),										// protocol%3A%2F%2Fdemo.domain%2Furl
/* 4 */					urlencode("//{$from_clear}"),							// %2F%2Fdemo.domain%2Furl
/* 5 */					"http://{$from_clear}",									// http://demo.domain/url
/* 6 */					str_replace('/', '\\/', "http://{$from_clear}"),		// http:\/\/demo.domain\/url
/* 7 */					"https://{$from_clear}",								// https://demo.domain/url
/* 8 */					str_replace('/', '\\/', "https://{$from_clear}"),		// https:\/\/demo.domain\/url
/* 9 */					$from,													// protocol://demo.domain/url
/* 10 */				str_replace('/', '\\/', $from),							// protocol:\/\/demo.domain\/url
/* 11 */				"//{$from_clear}",										// //demo.domain/url
/* 12 */				str_replace('/', '\\/', "//{$from_clear}"),				// \/\/demo.domain\/url
/* 13 */				$from_clear,											// demo.domain/url
/* 14 */				str_replace('/', '\\/', $from_clear)					// demo.domain\/url
						),
					array(
/* 1 */					urlencode(trx_addons_get_protocol() . "://{$to_clear}"),
/* 2 */					urlencode(trx_addons_get_protocol() . "://{$to_clear}"),
/* 3 */					urlencode($to),
/* 4 */					urlencode("//{$to_clear}"),
/* 5 */					trx_addons_get_protocol() . "://{$to_clear}",
/* 6 */					str_replace('/', '\\/', trx_addons_get_protocol() . "://{$to_clear}"),
/* 7 */					trx_addons_get_protocol() . "://{$to_clear}",
/* 8 */					str_replace('/', '\\/', trx_addons_get_protocol() . "://{$to_clear}"),
/* 9 */					$to,
/* 10 */				str_replace('/', '\\/', $to),
/* 11 */				"//{$to_clear}",
/* 12 */				str_replace('/', '\\/', "//{$to_clear}"),
/* 13 */				$to_clear,
/* 14 */				str_replace('/', '\\/', $to_clear)
						),
					$str
				);
	}
}

if ( ! function_exists('trx_addons_prepare_macros') ) {
	/**
	 * Prepare macros in the string:
	 * - replace {{ and }} on <i> and </i>,
	 * -         (( and )) on <b> and </b>,
	 * -         ||        on <br>
	 * -         ^N        on <sup>N</sup>
	 * -         [name prm1=val1 prm2="val2"] on shortcode output
	 *
	 * @trigger trx_addons_filter_prepare_macros
	 * 
	 * @param string $str       string to process
	 * 
	 * @return string           processed string
	 */
	function trx_addons_prepare_macros($str) {
		if ( empty( $str ) || ! is_string( $str ) ) {
			return $str;
		}
		// Replace shortcodes
		if ( strpos( $str, '[' ) !== false && strpos( $str, ']' ) !== false ) {
			// Replace [image] and [icon] shortcodes
			$str = preg_replace_callback(
				'/([\[])([\[\]\S]+)[\s]+([^\[\]]+)?([\]])/U',
				function( $matches ) {
					static $allowed_css = '', $image_css = '', $icon_css = '';
					if ( empty( $allowed_css ) ) {
						// Allowed CSS properties as attributes in format:
						// 'attribute' => 'css_property' or 'attribute' => [ 'rule' => 'css_property', 'default' => 'default_value' ]
						$allowed_css = apply_filters( 'trx_addons_filter_prepare_macros_allowed_css', array(
								'valign' => 'vertical-align',
								'color' => 'color',
								'bgcolor' => 'background-color',
								'bdcolor' => 'border-color',
								'border' => 'border-width',
								'radius' => 'border-radius',
								'padding' => 'padding',
								'margin' => 'margin',
							),
							'common'
						);
						$image_css = apply_filters( 'trx_addons_filter_prepare_macros_allowed_css', array(
								'size'   => array( 'rule' => 'max-height', 'default' => '1em' ),
							),
							'image'
						);
						$icon_css = apply_filters( 'trx_addons_filter_prepare_macros_allowed_css', array(
								'size'   => array( 'rule' => 'font-size', 'default' => '1em' ),
							),
							'icon'
						);
					}
					if ( $matches[2] == 'image' ) {
						$atts = ! empty( $matches[3] ) ? shortcode_parse_atts( $matches[3] ) : array();
						if ( ! empty( $atts['id'] ) || ! empty( $atts['url'] ) ) {
							$atts['url'] = trx_addons_get_attachment_url(
												! empty( $atts['id'] ) ? $atts['id'] : $atts['url'],
												apply_filters(
													'trx_addons_filter_thumb_size',
													trx_addons_get_thumb_size( ! empty( $atts['thumb'] ) ? $atts['thumb'] : 'tiny' ),
													'prepare_macros',
													$atts
												)
											);
						}
						return ! empty( $atts['url'] )
								? '<img src="' . $atts['url'] . '"'
									. ( ! empty( $atts['alt'] ) ? ' alt="' . esc_attr( $atts['alt'] ) . '"' : '' )
									. ' style="'
										. trx_addons_get_css_from_atts( $atts, array_merge( $allowed_css, $image_css ) )
										. ( ! empty( $atts['css'] ) ? esc_attr( $atts['css'] ) : '' )
									. '"'
									. '>'
								: '';
					} else if ( $matches[2] == 'icon' ) {
						$atts = ! empty( $matches[3] ) ? shortcode_parse_atts( $matches[3] ) : array();
						if ( ! empty( $atts['name'] ) && substr( $atts['name'], 0, 5 ) != 'icon-' ) {
							$atts['name'] = 'icon-' . $atts['name'];
						}
						return ! empty( $atts['name'] )
								? '<span class="' . $atts['name'] . '"'
									. ' style="'
										. trx_addons_get_css_from_atts( $atts, array_merge( $allowed_css, $icon_css ) )
										. ( ! empty( $atts['css'] ) ? esc_attr( $atts['css'] ) : '' )
									. '"'
									. '></span>'
								: '';
					}
					return $matches[0];
				},
				$str
			);
			// Replace other shortcodes
			if ( strpos( $str, '[' ) !== false && strpos( $str, ']' ) !== false ) {
				$str = do_shortcode( $str );
			}
		}
		// Replace simple macros
		$str = str_replace(
			array("{{",  "}}",   "((",  "))",   "||"),
			array("<i>", "</i>", "<b>", "</b>", "<br>"),
			$str);
		$str = preg_replace('/(\^(\d+))/', '<sup>$2</sup>', $str);
		return apply_filters( 'trx_addons_filter_prepare_macros', $str );
	}
}

if ( ! function_exists('trx_addons_remove_macros') ) {
	/**
	 * Remove macros from the string: {{ and }}, (( and )), ||
	 *
	 * @trigger trx_addons_filter_remove_macros
	 * 
	 * @param string $str       string to process
	 * 
	 * @return string           processed string
	 */
	function trx_addons_remove_macros($str) {
		// Remove shortcodes
		$str = preg_replace( '/[^\[]([\[][^\[\]]+[\]])[^\]]/', '', $str );
		// Remove simple macros
		return apply_filters( 'trx_addons_filter_remove_macros',
								str_replace(
									array( '{{', '}}', '((', '))', '||', '^' ),
									array( '',   '',   '',   '',   ' ',  '' ),
									$str
							) );
	}
}

if ( ! function_exists('trx_addons_get_phone_link') ) {
	/**
	 * Return link to the phone number
	 * 
	 * @param string $str       phone number
	 * 
	 * @return string           link to the phone number
	 */
	function trx_addons_get_phone_link($str) {
		return 'tel:' . str_replace( array( ' ', '-', '(', ')', '.', ','), '', $str );
	}
}

if ( ! function_exists('trx_addons_get_initials') ) {
	/**
	 * Return initials from the string
	 * 
	 * @param string $str       string
	 * 
	 * @return string           initials
	 */
	function trx_addons_get_initials( $str ) {
		$initials = '';
		$str_array = explode( ' ', $str );
		if ( count( $str_array ) > 0 ) {
			$initials = empty( $str_array[0][0] ) ? '' : $str_array[0][0];
			$initials .= empty( $str_array[1][0] ) ? '' : $str_array[1][0];
		}
		return $initials;
	}
}

if ( ! function_exists('trx_addons_show_layout') ) {
	/**
	 * Display string with html layout (if not empty), put it between 'before' and 'after' tags.
	 * Attention! This string may contain layout formed in any plugin (widgets or shortcodes output) and not require escaping to prevent damage!
	 * 
	 * @param string $str       string to display
	 * @param string $before    tag before string
	 * @param string $after     tag after string
	 */
	function trx_addons_show_layout( $str, $before = '', $after = '' ) {
		if ( trim( $str ) != '' ) {
			printf( "%s%s%s", $before, $str, $after );
		}
	}
}

if ( ! function_exists('trx_addons_show_value') ) {
	/**
	 * Output value as email or phone or plain text
	 * 
	 * @param string $val       string to display
	 * @param string $type      type of the value: email, phone, etc.
	 */
	function trx_addons_show_value( $val, $type ) {
		if ( in_array( $type, array( 'email', 'phone' ) ) ) {
			$val = str_replace( ',', '|', $val );
		}
		$val = explode( '|', $val );
		foreach( $val as $item ) {
			$item = trim( $item );
			if ( empty( $item ) ) {
				continue;
			}
			if ( $type == 'email' ) {
				?><a href="<?php printf('mailto:%s', antispambot($item)); ?>"><?php echo antispambot($item); ?></a><?php
			} elseif ( $type == 'phone' ) {
				?><a href="<?php trx_addons_show_layout(trx_addons_get_phone_link($item)); ?>"><?php echo esc_html($item); ?></a><?php
			} else {
				echo ( count( $val ) > 1 ? '<span>' : '' ) . esc_html( $item ) . ( count( $val ) > 1 ? '</span>' : '' );
			}
		}
	}
}

if ( ! function_exists('trx_addons_get_uuid') ) {
	/**
	 * Return UUID (unique ID) v.4 (random)
	 * 
	 * @return string           UUID
	 */
	function trx_addons_get_uuid() {
		return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
			// 32 bits for "time_low"
			mt_rand(0, 0xffff), mt_rand(0, 0xffff),
			// 16 bits for "time_mid"
			mt_rand(0, 0xffff),
			// 16 bits for "time_hi_and_version",
			// four most significant bits holds version number 4
			mt_rand(0, 0x0fff) | 0x4000,
			// 16 bits, 8 bits for "clk_seq_hi_res",
			// 8 bits for "clk_seq_low",
			// two most significant bits holds zero and one for variant DCE1.1
			mt_rand(0, 0x3fff) | 0x8000,
			// 48 bits for "node"
			mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
		);
	}
}

/* Templates manipulations
----------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'trx_addons_get_template_part_as_string' ) ) {
	/**
	 * Include part of template with specified parameters and return it as string
	 * 
	 * @param string $file       template file name
	 * @param string $args_name  name of the variable with array of parameters to use in the template
	 * @param array  $args       array of parameters to use in the template
	 * @param string $cb         callback function to apply to the template output
	 * 
	 * @return string            template output
	 */
	function trx_addons_get_template_part_as_string( $file, $args_name, $args = array(), $cb = '' ) {
		$output = '';
		ob_start();
		trx_addons_get_template_part( $file, $args_name, $args, $cb );
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}	
}

if ( ! function_exists('trx_addons_get_template_part') ) {	
	/**
	 * Include part of template with specified parameters
	 * 
	 * @param string $file       template file name
	 * @param string $args_name  name of the variable with array of parameters to use in the template
	 * @param array  $args       array of parameters to use in the template
	 * @param string $cb         callback function to apply to the template output
	 */
	function trx_addons_get_template_part( $file, $args_name = '', $args = array(), $cb = '' ) {
		static $fdirs = array();
		if ( ! is_array( $file ) ) {
			$file = array( $file );
		}
		foreach ( $file as $f ) {
            if ( empty( $fdirs[ $f ] ) ) {
                $fdirs[ $f ] = ! empty( $cb ) ? $cb( $f ) : trx_addons_get_file_dir( $f );
            }
            if ( ! empty( $fdirs[ $f ] ) ) {
				if ( ! empty( $args_name ) && ! empty( $args ) ) {
					set_query_var( $args_name, apply_filters( 'trx_addons_filter_template_part_args', $args, $args_name, $file ) );
				}
				include $fdirs[ $f ];
				break;
			}
		}
	}
}

if ( ! function_exists('trx_addons_add_inline_css_class') ) {
	/**
	 * Add inline CSS to the global var and return class name to add it to the element
	 * 
	 * @param string $css       CSS to add
	 * @param string $suffix    suffix to the class name
	 * @param string $prefix    prefix to the class name
	 * 
	 * @return string           class name
	 */
	function trx_addons_add_inline_css_class( $css, $suffix = '', $prefix = '' ) {
		$class_name = trx_addons_generate_id( 'trx_addons_inline_' );
		trx_addons_add_inline_css(
			sprintf( '%s.%s%s{%s}',
				$prefix,
				$class_name,
				! empty( $suffix ) 
					? ( substr( $suffix, 0, 1 ) != ':' ? ' ' : '') . str_replace( ',', ",.{$class_name} ", $suffix )
					: '',
				$css
			)
		);
		return $class_name;
	}
}

if ( ! function_exists('trx_addons_add_inline_css') ) {
	/**
	 * Add inline CSS to the global var
	 * 
	 * @param string $css       CSS to add 
	 */
	function trx_addons_add_inline_css( $css ) {
		global $TRX_ADDONS_STORAGE;
		$TRX_ADDONS_STORAGE['inline_css'] = ( ! empty( $TRX_ADDONS_STORAGE['inline_css'] ) ? $TRX_ADDONS_STORAGE['inline_css'] : '' ) . $css;
	}
}

if ( ! function_exists('trx_addons_get_inline_css') ) {
	/**
	 * Return inline CSS from the global var
	 * 
	 * @param boolean $clear    clear storage after return
	 * 
	 * @return string           inline CSS
	 */
	function trx_addons_get_inline_css( $clear = false ) {
		global $TRX_ADDONS_STORAGE;
		$rez = '';
        if ( ! empty( $TRX_ADDONS_STORAGE['inline_css'] ) ) {
        	$rez = $TRX_ADDONS_STORAGE['inline_css'];
        	if ( $clear ) {
				$TRX_ADDONS_STORAGE['inline_css'] = '';
			}
        }
        return $rez;
	}
}

if ( ! function_exists('trx_addons_add_inline_html') ) {
	/**
	 * Add inline HTML to the global var
	 * 
	 * @param string $html      HTML to add
	 */
	function trx_addons_add_inline_html( $html ) {
		global $TRX_ADDONS_STORAGE;
		$TRX_ADDONS_STORAGE['inline_html'] = ( ! empty( $TRX_ADDONS_STORAGE['inline_html'] ) ? $TRX_ADDONS_STORAGE['inline_html'] : '' ) . $html;
	}
}

if ( ! function_exists('trx_addons_get_inline_html') ) {
	/**
	 * Return inline HTML from the global var
	 * 
	 * @return string           inline HTML
	 */
	function trx_addons_get_inline_html() {
		global $TRX_ADDONS_STORAGE;
		return ! empty( $TRX_ADDONS_STORAGE['inline_html'] ) ? $TRX_ADDONS_STORAGE['inline_html'] : '';
	}
}

if ( ! function_exists('trx_addons_set_inline_html') ) {
	/**
	 * Set (replace) inline HTML to the global var. It will be outputed before closing </body>
	 * 
	 * @param string $html      HTML to store in the global var
	 */
	function trx_addons_set_inline_html( $html ) {
		global $TRX_ADDONS_STORAGE;
		$TRX_ADDONS_STORAGE['inline_html'] = $html;
	}
}
