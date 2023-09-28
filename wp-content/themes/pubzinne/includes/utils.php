<?php
/**
 * Theme tags and utilities
 *
 * @package PUBZINNE
 * @since PUBZINNE 1.0
 */

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) {
	exit; }


/* Arrays manipulations
----------------------------------------------------------------------------------------------------- */

// Return first key (by default) or value from associative array
if ( ! function_exists( 'pubzinne_array_get_first' ) ) {
	function pubzinne_array_get_first( &$arr, $key = true ) {
		foreach ( $arr as $k => $v ) {
			break;
		}
		return $key ? $k : $v;
	}
}

// Return keys by value from associative string: categories=1|author=0|date=0|counters=1...
if ( ! function_exists( 'pubzinne_array_get_keys_by_value' ) ) {
	function pubzinne_array_get_keys_by_value( $arr, $value = 1 ) {
		if ( ! is_array( $arr ) ) {
			parse_str( str_replace( '|', '&', $arr ), $arr );
		}
		return $value != null ? array_keys( $arr, $value ) : array_keys( $arr );
	}
}

// Delete items by value
if ( ! function_exists( 'pubzinne_array_delete_by_value' ) ) {
	function pubzinne_array_delete_by_value( $arr, $value ) {
		do {
			$key = array_search( $value, $arr );
			if ( false !== $key ) {
				unset( $arr[ $key ] );
			}
		} while ( false !== $key );
		return $arr;
	}
}

// Convert list to associative array (use values as keys)
if ( ! function_exists( 'pubzinne_array_from_list' ) ) {
	function pubzinne_array_from_list( $arr ) {
		$new = array();
		foreach ( $arr as $v ) {
			$new[ $v ] = $v;
		}
		return $new;
	}
}

// Return part of array from key=$from to key=$to
if ( ! function_exists( 'pubzinne_array_slice' ) ) {
	function pubzinne_array_slice( $arr, $from, $to='' ) {
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

// Merge arrays and lists (preserve number indexes)
if ( ! function_exists( 'pubzinne_array_merge' ) ) {
	function pubzinne_array_merge( $a1, $a2 ) {
		for ( $i = 1; $i < func_num_args(); $i++ ) {
			$arg = func_get_arg( $i );
			if ( is_array( $arg ) && count( $arg ) > 0 ) {
				foreach ( $arg as $k => $v ) {
					$a1[ $k ] = $v;
				}
			}
		}
		return $a1;
	}
}

// Inserts any number of scalars or arrays at the point
// in the haystack immediately after the search key ($needle) was found,
// or at the end if the needle is not found or not supplied.
// Modifies $haystack in place.
// @param array &$haystack the associative array to search. This will be modified by the function
// @param string $needle the key to search for
// @param mixed $stuff one or more arrays or scalars to be inserted into $haystack
// @return int the index at which $needle was found
if ( ! function_exists( 'pubzinne_array_insert' ) ) {
	function pubzinne_array_insert_after( &$haystack, $needle, $stuff ) {
		if ( ! is_array( $haystack ) ) {
			return -1;
		}

		$new_array = array();
		for ( $i = 2; $i < func_num_args(); ++$i ) {
			$arg = func_get_arg( $i );
			if ( is_array( $arg ) ) {
				if ( 2 == $i ) {
					$new_array = $arg;
				} else {
					$new_array = pubzinne_array_merge( $new_array, $arg );
				}
			} else {
				$new_array[] = $arg;
			}
		}

		$i = 0;
		if ( is_array( $haystack ) && count( $haystack ) > 0 ) {
			foreach ( $haystack as $key => $value ) {
				$i++;
				if ( $key == $needle ) {
					break;
				}
			}
		}

		$haystack = pubzinne_array_merge( array_slice( $haystack, 0, $i, true ), $new_array, array_slice( $haystack, $i, null, true ) );

		return $i;
	}
}

// Inserts any number of scalars or arrays at the point
// in the haystack immediately before the search key ($needle) was found,
// or at the end if the needle is not found or not supplied.
// Modifies $haystack in place.
// @param array &$haystack the associative array to search. This will be modified by the function
// @param string $needle the key to search for
// @param mixed $stuff one or more arrays or scalars to be inserted into $haystack
// @return int the index at which $needle was found
if ( ! function_exists( 'pubzinne_array_before' ) ) {
	function pubzinne_array_insert_before( &$haystack, $needle, $stuff ) {
		if ( ! is_array( $haystack ) ) {
			return -1;
		}

		$new_array = array();
		for ( $i = 2; $i < func_num_args(); ++$i ) {
			$arg = func_get_arg( $i );
			if ( is_array( $arg ) ) {
				if ( 2 == $i ) {
					$new_array = $arg;
				} else {
					$new_array = pubzinne_array_merge( $new_array, $arg );
				}
			} else {
				$new_array[] = $arg;
			}
		}

		$i = 0;
		if ( is_array( $haystack ) && count( $haystack ) > 0 ) {
			foreach ( $haystack as $key => $value ) {
				if ( $key === $needle ) {
					break;
				}
				$i++;
			}
		}

		$haystack = pubzinne_array_merge( array_slice( $haystack, 0, $i, true ), $new_array, array_slice( $haystack, $i, null, true ) );

		return $i;
	}
}





/* HTML & CSS
----------------------------------------------------------------------------------------------------- */

// Return first tag from text
if ( ! function_exists( 'pubzinne_get_tag' ) ) {
	function pubzinne_get_tag( $text, $tag_start, $tag_end = '' ) {
		$val       = '';
		$pos_start = strpos( $text, $tag_start );
		if ( false !== $pos_start ) {
			$pos_end = $tag_end ? strpos( $text, $tag_end, $pos_start ) : false;
			if ( false === $pos_end ) {
				$tag_end = substr( $tag_start, 0, 1 ) == '<' ? '>' : ']';
				$pos_end = strpos( $text, $tag_end, $pos_start );
			}
			$val = substr( $text, $pos_start, $pos_end + strlen( $tag_end ) - $pos_start );
		}
		return $val;
	}
}

// Return attrib from tag
if ( ! function_exists( 'pubzinne_get_tag_attrib' ) ) {
	function pubzinne_get_tag_attrib( $text, $tag, $attr ) {
		$val       = '';
		$pos_start = strpos( $text, substr( $tag, 0, strlen( $tag ) - 1 ) );
		if ( false !== $pos_start ) {
			$pos_end  = strpos( $text, substr( $tag, -1, 1 ), $pos_start );
			$pos_attr = strpos( $text, ' ' . ( $attr ) . '=', $pos_start );
			if ( false !== $pos_attr && $pos_attr < $pos_end ) {
				$pos_attr += strlen( $attr ) + 3;
				$pos_quote = strpos( $text, substr( $text, $pos_attr - 1, 1 ), $pos_attr );
				$val       = substr( $text, $pos_attr, $pos_quote - $pos_attr );
			}
		}
		return $val;
	}
}

// Return string with position rules for the style attr
if ( ! function_exists( 'pubzinne_get_css_position_from_values' ) ) {
	function pubzinne_get_css_position_from_values( $top = '', $right = '', $bottom = '', $left = '', $width = '', $height = '' ) {
		if ( ! is_array( $top ) ) {
			$top = compact( 'top', 'right', 'bottom', 'left', 'width', 'height' );
		}
		$output = '';
		foreach ( $top as $k => $v ) {
			$imp = substr( $v, 0, 1 );
			if ( '!' == $imp ) {
				$v = substr( $v, 1 );
			}
			if ( '' != $v ) {
				$output .= ( 'width' == $k ? 'width' : ( 'height' == $k ? 'height' : 'margin-' . esc_attr( $k ) ) ) . ':' . esc_attr( pubzinne_prepare_css_value( $v ) ) . ( '!' == $imp ? ' !important' : '' ) . ';';
			}
		}
		return $output;
	}
}

// Return value for the style attr with measurements
if ( ! function_exists( 'pubzinne_prepare_css_value' ) ) {
	function pubzinne_prepare_css_value( $val ) {
		if ( '' != $val ) {
			$parts = explode( ' ', trim( $val ) );
			foreach( $parts as $k => $v ) {
				$ed = substr( $v, -1 );
				if ( '0' <= $ed && $ed <= '9' ) {
					$parts[ $k ] .= 'px';
				}
			}
			$val = join( ' ', $parts );
		}
		return $val;
	}
}

// Return array with classes from css-file
if ( ! function_exists( 'pubzinne_parse_icons_classes' ) ) {
	function pubzinne_parse_icons_classes( $css ) {
		$rez = array();
		if ( ! file_exists( $css ) ) {
			return $rez;
		}
		$file = pubzinne_fga( $css );
		if ( ! is_array( $file ) || count( $file ) == 0 ) {
			return $rez;
		}
		foreach ( $file as $row ) {
			if ( substr( $row, 0, 1 ) != '.' ) {
				continue;
			}
			$name = '';
			for ( $i = 1; $i < strlen( $row ); $i++ ) {
				$ch = substr( $row, $i, 1 );
				if ( in_array( $ch, array( ':', '{', '.', ' ' ) ) ) {
					break;
				}
				$name .= $ch;
			}
			if ( '' != $name ) {
				$rez[] = $name;
			}
		}
		return $rez;
	}
}





/* GET, POST, COOKIE, SESSION manipulations
----------------------------------------------------------------------------------------------------- */

// Strip slashes if Magic Quotes is on
if ( ! function_exists( 'pubzinne_stripslashes' ) ) {
	function pubzinne_stripslashes( $val ) {
		static $magic = 0;
		if ( 0 === $magic ) {
			$magic = version_compare( phpversion(), '5.4', '>=' )
					|| ( function_exists( 'get_magic_quotes_gpc' ) && get_magic_quotes_gpc() == 1 )
					|| ( function_exists( 'get_magic_quotes_runtime' ) && get_magic_quotes_runtime() == 1 );
		}
		if ( is_array( $val ) ) {
			foreach ( $val as $k => $v ) {
				$val[ $k ] = pubzinne_stripslashes( $v );
			}
		} else {
			$val = $magic ? stripslashes( trim( $val ) ) : trim( $val );
		}
		return $val;
	}
}

// Get GET, POST value
if ( ! function_exists( 'pubzinne_get_value_gp' ) ) {
	function pubzinne_get_value_gp( $name, $defa = '' ) {
		if ( isset( $_GET[ $name ] ) ) {
			$rez = wp_unslash( $_GET[ $name ] );
		} elseif ( isset( $_POST[ $name ] ) ) {
			$rez = wp_unslash( $_POST[ $name ] );
		} else {
			$rez = $defa;
		}
		return $rez;
	}
}

// Get GET, POST, COOKIE value and save it (if need)
if ( ! function_exists( 'pubzinne_get_value_gpc' ) ) {
	function pubzinne_get_value_gpc( $name, $defa = '' ) {
		if ( isset( $_GET[ $name ] ) ) {
			$rez = wp_unslash( $_GET[ $name ] );
		} elseif ( isset( $_POST[ $name ] ) ) {
			$rez = wp_unslash( $_POST[ $name ] );
		} elseif ( isset( $_COOKIE[ $name ] ) ) {
			$rez = wp_unslash( $_COOKIE[ $name ] );
		} else {
			$rez = $defa;
		}
		return $rez;
	}
}

// Get GET, POST, SESSION value and save it (if need)
if ( ! function_exists( 'pubzinne_get_value_gps' ) ) {
	function pubzinne_get_value_gps( $name, $defa = '' ) {
		global $wp_session;
		if ( isset( $_GET[ $name ] ) ) {
			$rez = wp_unslash( $_GET[ $name ] );
		} elseif ( isset( $_POST[ $name ] ) ) {
			$rez = wp_unslash( $_POST[ $name ] );
		} elseif ( isset( $wp_session[ $name ] ) ) {
			$rez = wp_unslash( $wp_session[ $name ] );
		} else {
			$rez = $defa;
		}
		return $rez;
	}
}

// Get value from the session
if ( ! function_exists( 'pubzinne_get_session_value' ) ) {
	function pubzinne_get_session_value( $name, $defa = '' ) {
		global $wp_session;
		return isset( $wp_session[ $name ] ) ? $wp_session[ $name ] : $defa;
	}
}

// Save value to the session
if ( ! function_exists( 'pubzinne_set_session_value' ) ) {
	function pubzinne_set_session_value( $name, $value ) {
		global $wp_session;
		$wp_session[ $name ] = $value;
	}
}

// Delete value from the session
if ( ! function_exists( 'pubzinne_del_session_value' ) ) {
	function pubzinne_del_session_value( $name ) {
		global $wp_session;
		unset( $wp_session[ $name ] );
	}
}





/* Colors manipulations
----------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'pubzinne_hex2rgb' ) ) {
	function pubzinne_hex2rgb( $hex ) {
		$dec = hexdec( substr( $hex, 0, 1 ) == '#' ? substr( $hex, 1 ) : $hex );
		return array(
			'r' => $dec >> 16,
			'g' => ( $dec & 0x00FF00 ) >> 8,
			'b' => $dec & 0x0000FF,
		);
	}
}

if ( ! function_exists( 'pubzinne_hex2rgba' ) ) {
	function pubzinne_hex2rgba( $hex, $alpha ) {
		$rgb = pubzinne_hex2rgb( $hex );
		return 'rgba(' . intval( $rgb['r'] ) . ',' . intval( $rgb['g'] ) . ',' . intval( $rgb['b'] ) . ',' . floatval( $alpha ) . ')';
	}
}

if ( ! function_exists( 'pubzinne_hex2hsb' ) ) {
	function pubzinne_hex2hsb( $hex, $h = 0, $s = 0, $b = 0 ) {
		$hsb      = pubzinne_rgb2hsb( pubzinne_hex2rgb( $hex ) );
		$hsb['h'] = min( 359, max( 0, $hsb['h'] + $h ) );
		$hsb['s'] = min( 100, max( 0, $hsb['s'] + $s ) );
		$hsb['b'] = min( 100, max( 0, $hsb['b'] + $b ) );
		return $hsb;
	}
}

if ( ! function_exists( 'pubzinne_rgb2hsb' ) ) {
	function pubzinne_rgb2hsb( $rgb ) {
		$hsb      = array();
		$hsb['b'] = max( max( $rgb['r'], $rgb['g'] ), $rgb['b'] );
		$hsb['s'] = ( $hsb['b'] <= 0 ) ? 0 : round( 100 * ( $hsb['b'] - min( min( $rgb['r'], $rgb['g'] ), $rgb['b'] ) ) / $hsb['b'] );
		$hsb['b'] = round( ( $hsb['b'] / 255 ) * 100 );
		if ( ( $rgb['r'] == $rgb['g'] ) && ( $rgb['g'] == $rgb['b'] ) ) {
			$hsb['h'] = 0;
		} elseif ( $rgb['r'] >= $rgb['g'] && $rgb['g'] >= $rgb['b'] ) {
			$hsb['h'] = 60 * ( $rgb['g'] - $rgb['b'] ) / ( $rgb['r'] - $rgb['b'] );
		} elseif ( $rgb['g'] >= $rgb['r'] && $rgb['r'] >= $rgb['b'] ) {
			$hsb['h'] = 60 + 60 * ( $rgb['g'] - $rgb['r'] ) / ( $rgb['g'] - $rgb['b'] );
		} elseif ( $rgb['g'] >= $rgb['b'] && $rgb['b'] >= $rgb['r'] ) {
			$hsb['h'] = 120 + 60 * ( $rgb['b'] - $rgb['r'] ) / ( $rgb['g'] - $rgb['r'] );
		} elseif ( $rgb['b'] >= $rgb['g'] && $rgb['g'] >= $rgb['r'] ) {
			$hsb['h'] = 180 + 60 * ( $rgb['b'] - $rgb['g'] ) / ( $rgb['b'] - $rgb['r'] );
		} elseif ( $rgb['b'] >= $rgb['r'] && $rgb['r'] >= $rgb['g'] ) {
			$hsb['h'] = 240 + 60 * ( $rgb['r'] - $rgb['g'] ) / ( $rgb['b'] - $rgb['g'] );
		} elseif ( $rgb['r'] >= $rgb['b'] && $rgb['b'] >= $rgb['g'] ) {
			$hsb['h'] = 300 + 60 * ( $rgb['r'] - $rgb['b'] ) / ( $rgb['r'] - $rgb['g'] );
		} else {
			$hsb['h'] = 0;
		}
		$hsb['h'] = round( $hsb['h'] );
		return $hsb;
	}
}

if ( ! function_exists( 'pubzinne_hsb2rgb' ) ) {
	function pubzinne_hsb2rgb( $hsb ) {
		$rgb = array();
		$h   = round( $hsb['h'] );
		$s   = round( $hsb['s'] * 255 / 100 );
		$v   = round( $hsb['b'] * 255 / 100 );
		if ( 0 == $s ) {
			$rgb['r'] = $v;
			$rgb['g'] = $v;
			$rgb['b'] = $v;
		} else {
			$t1 = $v;
			$t2 = ( 255 - $s ) * $v / 255;
			$t3 = ( $t1 - $t2 ) * ( $h % 60 ) / 60;
			if ( 360 == $h ) {
				$h = 0;
			}
			if ( $h < 60 ) {
				$rgb['r'] = $t1;
				$rgb['b'] = $t2;
				$rgb['g'] = $t2 + $t3;
			} elseif ( $h < 120 ) {
				$rgb['g'] = $t1;
				$rgb['b'] = $t2;
				$rgb['r'] = $t1 - $t3;
			} elseif ( $h < 180 ) {
				$rgb['g'] = $t1;
				$rgb['r'] = $t2;
				$rgb['b'] = $t2 + $t3;
			} elseif ( $h < 240 ) {
				$rgb['b'] = $t1;
				$rgb['r'] = $t2;
				$rgb['g'] = $t1 - $t3;
			} elseif ( $h < 300 ) {
				$rgb['b'] = $t1;
				$rgb['g'] = $t2;
				$rgb['r'] = $t2 + $t3;
			} elseif ( $h < 360 ) {
				$rgb['r'] = $t1;
				$rgb['g'] = $t2;
				$rgb['b'] = $t1 - $t3;
			} else {
				$rgb['r'] = 0;
				$rgb['g'] = 0;
				$rgb['b'] = 0; }
		}
		return array(
			'r' => round( $rgb['r'] ),
			'g' => round( $rgb['g'] ),
			'b' => round( $rgb['b'] ),
		);
	}
}

if ( ! function_exists( 'pubzinne_rgb2hex' ) ) {
	function pubzinne_rgb2hex( $rgb ) {
		$hex = array(
			dechex( $rgb['r'] ),
			dechex( $rgb['g'] ),
			dechex( $rgb['b'] ),
		);
		return '#' . ( strlen( $hex[0] ) == 1 ? '0' : '' ) . ( $hex[0] ) . ( strlen( $hex[1] ) == 1 ? '0' : '' ) . ( $hex[1] ) . ( strlen( $hex[2] ) == 1 ? '0' : '' ) . ( $hex[2] );
	}
}

if ( ! function_exists( 'pubzinne_hsb2hex' ) ) {
	function pubzinne_hsb2hex( $hsb ) {
		return pubzinne_rgb2hex( pubzinne_hsb2rgb( $hsb ) );
	}
}






/* Date manipulations
----------------------------------------------------------------------------------------------------- */

// Convert date from Date format (dd.mm.YYYY) to MySQL format (YYYY-mm-dd)
if (!function_exists('pubzinne_date_to_sql')) {
	function pubzinne_date_to_sql($str) {
		if (trim($str)=='') return '';
		$str = strtr(trim($str),'/\.,','----');
		if (trim($str)=='00-00-0000' || trim($str)=='00-00-00') return '';
		$pos = strpos($str,'-');
		if ($pos > 3) return $str;
		$d=trim(substr($str,0,$pos));
		$str=substr($str,$pos+1);
		$pos = strpos($str,'-');
		$m=trim(substr($str,0,$pos));
		$y=trim(substr($str,$pos+1));
		$y=($y<50?$y+2000:($y<1900?$y+1900:$y));
		return ''.($y).'-'.(strlen($m)<2?'0':'').($m).'-'.(strlen($d)<2?'0':'').($d);
	}
}






/* Numbers manipulations
----------------------------------------------------------------------------------------------------- */

// Display price
if (!function_exists('pubzinne_format_price')) {
	function pubzinne_format_price($price) {
		return is_numeric($price) 
					? ($price != round($price, 0)
						? number_format(round($price, 2), 2, '.', ' ')
						: number_format($price, 0, '.', ' ')
						)
					: $price;
	}
}


// Convert number to K: 10200 -> 10K
if (!function_exists('pubzinne_num2size')) {
	function pubzinne_num2size($num) {
		return $num > 1000 ? round($num/1000, 0).'K' : $num;
	}
}

// Try to convert size string with suffix K(ilo)|M(ega)|G(iga)|T(era)|P(enta) to the integer: 10K -> 10240
if (!function_exists('pubzinne_size2num')) {
	function pubzinne_size2num($size) {
		$suff = strtoupper( substr( $size, -1 ) );
		$pos  = strpos( 'KMGTP', $suff );
		if ( $pos !== false ) {
			$size = intval( substr( $size, 0, -1 ) ) * pow( 1024, $pos + 1 );
		}
		return (int) $size;
	}
}

// Clear number - leave only sign (+/-), digits and point (.) as delimiter
if (!function_exists('pubzinne_parse_num')) {
	function pubzinne_parse_num($str) {
		return (float) filter_var( html_entity_decode( strip_tags( $str ) ), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );
	}
}






/* String manipulations
----------------------------------------------------------------------------------------------------- */

// Replace macros in the string
if ( ! function_exists( 'pubzinne_prepare_macros' ) ) {
	function pubzinne_prepare_macros( $str ) {
		return str_replace(
			array( '{{', '}}', '((', '))', '||' ),
			array( '<i>', '</i>', '<b>', '</b>', '<br>' ),
			$str
		);
	}
}

// Remove macros from the string
if ( ! function_exists( 'pubzinne_remove_macros' ) ) {
	function pubzinne_remove_macros( $str ) {
		return str_replace(
			array( '{{', '}}', '((', '))', '||' ),
			array( '', '', '', '', ' ' ),
			$str
		);
	}
}

// Check value for "on" | "off" | "inherit" values
if ( ! function_exists( 'pubzinne_is_on' ) ) {
	function pubzinne_is_on( $prm ) {
		return is_array( $prm )
            ? count( $prm ) > 0
            : ( is_bool( $prm ) && $prm === true )
            || ( is_numeric( $prm ) && $prm > 0 )
            || in_array( strtolower( $prm ), array( 'true', 'on', 'yes', 'show' ) );
	}
}
if ( ! function_exists( 'pubzinne_is_off' ) ) {
	function pubzinne_is_off( $prm ) {
		return is_array( $prm )
            ? count( $prm ) == 0
            : empty( $prm ) || (is_numeric( $prm ) && 0 === $prm) || in_array( strtolower( $prm ), array( 'false', 'off', 'no', 'none', 'hide' ) );
	}
}
if ( ! function_exists( 'pubzinne_is_inherit' ) ) {
	function pubzinne_is_inherit( $prm ) {
		return ! is_array( $prm ) && in_array( strtolower( $prm ), array( 'inherit' ) );
	}
}

// Return truncated string (by chars number)
if ( ! function_exists( 'pubzinne_strshort' ) ) {
	function pubzinne_strshort( $str, $maxlength, $add = '&hellip;' ) {
		if ( 0 >= $maxlength ) {
			return '';
		}
		$str = pubzinne_strip_tags( $str );
		if ( strlen( $str ) <= $maxlength ) {
			return $str;
		}
		$str = substr( $str, 0, $maxlength - strlen( $add ) );
		$ch  = substr( $str, $maxlength - strlen( $add ), 1 );
		if ( ' ' != $ch ) {
			for ( $i = strlen( $str ) - 1; $i > 0; $i-- ) {
				if ( ' ' == substr( $str, $i, 1 ) ) {
					break;
				}
			}
			$str = trim( substr( $str, 0, $i ) );
		}
		if ( ! empty( $str ) && strpos( ',.:;-', substr( $str, -1 ) ) !== false ) {
			$str = substr( $str, 0, -1 );
		}
		return ( $str ) . ( $add );
	}
}


// Remove non-text tags from html
if ( ! function_exists( 'pubzinne_strip_tags' ) ) {
	function pubzinne_strip_tags( $str ) {
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

// Make excerpt from html
if ( ! function_exists( 'pubzinne_excerpt' ) ) {
	function pubzinne_excerpt( $str, $maxlength, $add = '&hellip;' ) {
		if ( $maxlength <= 0 ) {
			return '';
		}
		return pubzinne_strwords( pubzinne_strip_tags( $str ), $maxlength, $add );
	}
}


// Return truncated string (by words number)
if ( ! function_exists( 'pubzinne_strwords' ) ) {
	function pubzinne_strwords( $str, $maxlength, $add = '&hellip;' ) {
		if ( $maxlength <= 0 ) {
			return '';
		}
		$words = explode( ' ', $str );
		if ( count( $words ) > $maxlength ) {
			$words = array_slice( $words, 0, $maxlength );
			$words[ count( $words ) - 1 ] .= $add;
		}
		return join(' ', $words	);
	}
}

// Unserialize string
if ( ! function_exists( 'pubzinne_unserialize' ) ) {
	function pubzinne_unserialize( $str ) {
		if ( ! empty( $str ) && is_serialized( $str ) ) {
			// If serialized data content unrecoverable object (base class for this object is not exists) - skip this string
			if ( true || ! preg_match( '/O:[0-9]+:"([^"]*)":[0-9]+:{/', $str, $matches ) || empty( $matches[1] ) || class_exists( $matches[1] ) ) {
				try {
					$data = unserialize( $str );
				} catch ( Exception $e ) {
					dcl( $e->getMessage() );
					$data = false;
				}
				if ( false === $data ) {
					try {
						$str  = preg_replace_callback(
								'!s:(\d+):"(.*?)";!',
								function( $match ) {
									return ( strlen( $match[2] ) == $match[1] )
										? $match[0]
										: 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
								},
								$str
							);
						$data = unserialize( $str );
					} catch ( Exception $e ) {
						dcl( $e->getMessage() );
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


// str_replace with arrays and serialize support
if ( ! function_exists( 'pubzinne_str_replace' ) ) {
	function pubzinne_str_replace( $from, $to, $str ) {
		if ( is_array( $str ) ) {
			foreach ( $str as $k => $v ) {
				$str[ $k ] = pubzinne_str_replace( $from, $to, $v );
			}
		} elseif ( is_object( $str ) ) {
			foreach ( $str as $k => $v ) {
				$str->{$k} = pubzinne_str_replace( $from, $to, $v );
			}
		} elseif ( is_string( $str ) ) {
			$str = pubzinne_unserialize( $str );
			$str = is_array( $str ) || is_object( $str )
							? serialize( pubzinne_str_replace( $from, $to, $str ) )
							: str_replace( $from, $to, $str );
		}
		return $str;
	}
}

// Uses only the first encountered substitution from the list
if ( ! function_exists( 'pubzinne_str_replace_once' ) ) {
	function pubzinne_str_replace_once( $from, $to, $str ) {
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
					$rez  .= isset( $to[ $j ] ) ? $to[ $j ] : '';
					$found = true;
					$i    += strlen( $from[ $j ] ) - 1;
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

// Return high-level tags number
if ( ! function_exists( 'pubzinne_tags_count' ) ) {
	function pubzinne_tags_count( $str, $tag ) {
		$cnt = 0;
		if ( ! empty( $str ) && is_string( $str ) ) {
			$tag_start = '<' . $tag . ' ';
			$tag_end   = '</' . $tag . '>';
			$tag_start_len = strlen( $tag_start );
			$tag_end_len = strlen( $tag_end );
			$tag_in = 0;
			for ( $i = 0; $i < strlen( $str ); $i++ ) {
				if ( substr( $str, $i, $tag_start_len ) == $tag_start ) {
					$tag_in++;
					$i += $tag_start_len - 1;
					$cnt += 1 == $tag_in ? 1 : 0;
				} elseif ( substr( $str, $i, $tag_end_len ) == $tag_end ) {
					$tag_in--;
					$i += $tag_end_len - 1;
				}
			}
		}
		return $cnt;
	}
}



/* Media: images, galleries, audio, video
----------------------------------------------------------------------------------------------------- */

// Get image sizes from image url (if image in the uploads folder)
if ( ! function_exists( 'pubzinne_getimagesize' ) ) {
	function pubzinne_getimagesize( $url ) {
		// Remove scheme from url
		$url = pubzinne_remove_protocol_from_url( $url );

		// Get upload path & dir
		$upload_info = wp_upload_dir();

		// Where check file
		$locations = array(
			'uploads' => array(
				'dir' => $upload_info['basedir'],
				'url' => pubzinne_remove_protocol_from_url( $upload_info['baseurl'] ),
			),
			'child'   => array(
				'dir' => PUBZINNE_CHILD_DIR,
				'url' => pubzinne_remove_protocol_from_url( PUBZINNE_CHILD_URL ),
			),
			'theme'   => array(
				'dir' => PUBZINNE_THEME_DIR,
				'url' => pubzinne_remove_protocol_from_url( PUBZINNE_THEME_URL ),
			),
		);

		$img_size = false;

		foreach ( $locations as $key => $loc ) {

			// Check if $img_url is local.
			if ( false === strpos( $url, $loc['url'] ) ) {
				continue;
			}

			// Get path of image.
			$img_path = str_replace( $loc['url'], $loc['dir'], $url );

			// Check if img path exists, and is an image indeed.
			if ( ! file_exists( $img_path ) ) {
				continue;
			}

			// Get image size
			$img_size = getimagesize( $img_path );
			break;
		}

		return $img_size;
	}
}

// Clear thumb sizes from image name
if ( ! function_exists( 'pubzinne_clear_thumb_size' ) ) {
	function pubzinne_clear_thumb_size( $url ) {
		$pi            = pathinfo( $url );
		$pi['dirname'] = pubzinne_remove_protocol_from_url( $pi['dirname'], false );
		$parts         = explode( '-', $pi['filename'] );
		$suff          = explode( 'x', $parts[ count( $parts ) - 1 ] );
		if ( count( $suff ) == 2 && (int) $suff[0] > 0 && (int) $suff[1] > 0 ) {
			array_pop( $parts );
			$url = $pi['dirname'] . '/' . join( '-', $parts ) . '.' . $pi['extension'];
		}
		return $url;
	}
}

// Add thumb sizes to image name
if ( ! function_exists( 'pubzinne_add_thumb_size' ) ) {
	function pubzinne_add_thumb_size( $url, $thumb_size, $check_exists = true ) {

		if ( empty( $url ) ) return '';

		$pi = pathinfo( $url );

		// Remove image sizes from filename
		$parts = explode( '-', $pi['filename'] );
		$suff = explode( 'x', $parts[ count( $parts ) - 1 ] );
		if ( count( $suff ) == 2 && (int) $suff[0] > 0 && (int) $suff[1] > 0) {
			array_pop( $parts );
		}
		$url = $pi['dirname'] . '/' . join( '-', $parts ) . '.' . $pi['extension'];

		// Add new image sizes
		global $_wp_additional_image_sizes;
		if ( isset( $_wp_additional_image_sizes ) && count( $_wp_additional_image_sizes ) && in_array( $thumb_size, array_keys( $_wp_additional_image_sizes ) ) ) {
			if ( empty( $_wp_additional_image_sizes[ $thumb_size ]['height'] ) || empty( $_wp_additional_image_sizes[ $thumb_size ]['crop'] ) ) {
				$image_id = attachment_url_to_postid( $url );
				if ( is_numeric( $image_id ) && (int)$image_id > 0 ) {
					$attach = wp_get_attachment_image_src( $image_id, $thumb_size );
					if ( ! empty( $attach[0] ) ) {
						$pi = pathinfo( $attach[0] );
						$pi['dirname'] = pubzinne_remove_protocol_from_url( $pi['dirname'] );
						$parts = explode( '-', $pi['filename'] );
					}
				}
			} else {
				$parts[] = intval( $_wp_additional_image_sizes[ $thumb_size ]['width'] ) . 'x' . intval( $_wp_additional_image_sizes[ $thumb_size ]['height'] );
			}
		}
		$pi['filename'] = join( '-', $parts );
		$new_url = pubzinne_remove_protocol_from_url( $pi['dirname'] . '/' . $pi['filename'] . '.' . $pi['extension'] );

		// Check exists
		if ( $check_exists ) {
			$uploads_info = wp_upload_dir();
			$uploads_url = pubzinne_remove_protocol_from_url( $uploads_info['baseurl'] );
			$uploads_dir = $uploads_info['basedir'];
			if ( strpos( $new_url, $uploads_url ) !== false ) {
				if ( ! file_exists( str_replace( $uploads_url, $uploads_dir, $new_url ) ) ) {
					$new_url = pubzinne_remove_protocol_from_url( $url );
				}
			} else {
				$new_url = pubzinne_remove_protocol_from_url( $url );
			}
		}
		return $new_url;
	}
}

// Return image size multiplier
if ( ! function_exists( 'pubzinne_get_thumb_size' ) ) {
	function pubzinne_get_thumb_size( $ts ) {
		$retina = pubzinne_get_retina_multiplier() > 1 ? '-@retina' : '';
		return ( 'post-thumbnail' == $ts ? '' : 'pubzinne-thumb-' ) . $ts . $retina;
	}
}

// Return closest image size by dimensions
// @params: old_size - current thumb size (leave it unchanged if it dimensions fit)
//          dim      - array with keys 'width' and 'height' (both are optional)
//          prefix   - substring at the start of the name of thumbnails (if not equal - skip this size)
if ( ! function_exists( 'pubzinne_get_closest_thumb_size' ) ) {
	function pubzinne_get_closest_thumb_size( $old_size, $dim, $prefix = '' ) {
		$closest = array( 'thumb' => '', 'width' => 0, 'height' => 0 );
		$biggest = array( 'thumb' => '', 'width' => 0, 'height' => 0 );
		global $_wp_additional_image_sizes;
		if ( isset( $_wp_additional_image_sizes ) && count( $_wp_additional_image_sizes ) ) {
			$tmp = $_wp_additional_image_sizes;
			if ( isset( $tmp[ $old_size ] ) ) {
				unset( $tmp[ $old_size ] );
				$tmp = array_merge( array( $old_size => $_wp_additional_image_sizes[ $old_size ] ), $tmp );
			}
			foreach ( $tmp as $thumb => $sizes ) {
				if ( ! empty( $prefix ) && substr( $thumb, 0, strlen( $prefix ) ) !== $prefix ) {
					continue;
				}
				$cur = array(
							'thumb' => $thumb,
							'width' => $sizes['width'],
							'height' => $sizes['height']
							);
				if (
					( empty( $dim['width'] ) || empty( $cur['width'] ) || $dim['width'] <= $cur['width'] )
					&&
					( empty( $dim['height'] ) || empty( $cur['height'] ) || $dim['height'] <= $cur['height'] )
					&&
					( empty( $closest['thumb'] )
						|| ( ! empty( $cur['width'] ) && ( empty( $closest['width'] ) || $cur['width'] < $closest['width'] ) )
						|| ( ! empty( $cur['height'] ) && ( empty( $closest['height'] ) || $cur['height'] < $closest['height'] ) )
					)
				) {
					$closest = $cur;
					if ( $thumb == $old_size ) {
						break;
					}
				}
				if (
					( empty( $dim['width'] ) || empty( $cur['width'] ) || $cur['width'] <= $dim['width'] )
					&&
					( empty( $dim['height'] ) || empty( $cur['height'] ) || $cur['height'] <= $dim['height'] )
					&&
					( empty( $biggest['thumb'] )
						|| ( ! empty( $biggest['width'] ) && $cur['width'] > $biggest['width'] )
						|| ( ! empty( $biggest['height'] ) && $cur['height'] > $biggest['height'] )
					)
				) {
					$biggest = $cur;
				}
			}
			if ( empty( $closest['thumb'] ) ) {
				$closest['thumb'] = 'full';	// Can return $biggest['thumb'] to get closest, but smaller size
			}
		}
		return $closest['thumb'];
	}
}

// Return image url by attachment ID
if ( ! function_exists( 'pubzinne_get_attachment_url' ) ) {
	function pubzinne_get_attachment_url( $image_id, $size = 'full' ) {
		if ( is_numeric( $image_id ) && (int)$image_id > 0 ) {
			$attach   = wp_get_attachment_image_src( $image_id, $size );
			$image_id = isset( $attach[0] ) && '' != $attach[0] ? $attach[0] : '';
		} else {
			$image_id = pubzinne_add_thumb_size( $image_id, $size );
		}
		return $image_id;
	}
}


// Return url from first <img> tag inserted in post
if ( ! function_exists( 'pubzinne_get_post_image' ) ) {
	function pubzinne_get_post_image( $post_text = '', $src = true ) {
		global $post;
		$img = '';
		if ( empty( $post_text ) ) {
			$post_text = $post->post_content;
		}
		if ( preg_match_all( '/<img.+src=[\'"]([^\'"]+)[\'"][^>]*>/i', $post_text, $matches ) ) {
			$img = $matches[ $src ? 1 : 0 ][0];
		}
		return $img;
	}
}


// Return url from first <audio> tag inserted in post
if ( ! function_exists( 'pubzinne_get_post_audio' ) ) {
	function pubzinne_get_post_audio( $post_text = '', $src = true ) {
		global $post;
		$img = '';
		if ( empty( $post_text ) ) {
			$post_text = $post->post_content;
		}
		if ( $src ) {
			if ( preg_match_all( '/<audio.+src=[\'"]([^\'"]+)[\'"][^>]*>/i', $post_text, $matches ) ) {
				$img = $matches[1][0];
			} else if ( preg_match_all( '/<!\\-\\- wp:trx-addons\\/audio-item.+"url":"([^"]*)"/i', $post_text, $matches ) ) {
				$img = $matches[1][0];
			}
		} else {
			$img = pubzinne_get_tag( $post_text, '<audio', '</audio>' );
			if ( empty( $img ) ) {
				$img = do_shortcode( pubzinne_get_tag( $post_text, '[audio', '[/audio]' ) );
			}
			if ( empty( $img ) ) {
				$img = pubzinne_get_tag_attrib( $post_text, '[trx_widget_audio]', 'url' );
				if ( empty( $img ) && preg_match_all( '/<!\\-\\- wp\\:trx-addons\\/audio-item.+"url"\\:"([^"]*)"/i', $post_text, $matches ) ) {
					$img = $matches[1][0];
				}
				if ( ! empty( $img ) ) {
					$img = '<audio src="' . esc_url( $img ) . '"></audio>';
				}
			}
		}
		return $img;
	}
}


// Return url from first <video> tag inserted in post
if ( ! function_exists( 'pubzinne_get_post_video' ) ) {
	function pubzinne_get_post_video( $post_text = '', $src = true ) {
		global $post;
		$img = '';
		if ( empty( $post_text ) ) {
			$post_text = $post->post_content;
		}
		if ( $src ) {
			if ( preg_match_all( '/<video.+src=[\'"]([^\'"]+)[\'"][^>]*>/i', $post_text, $matches ) ) {
				$img = $matches[1][0];
			} else if ( preg_match_all( '/<!\\-\\- wp:trx-addons\\/video.+"link":"([^"]*)"/i', $post_text, $matches ) ) {
				$img = $matches[1][0];
			}
		} else {
			$img = pubzinne_get_tag( $post_text, '<video', '</video>' );
			if ( empty( $img ) ) {
				$sc = pubzinne_get_tag( $post_text, '[video', '[/video]' );
				if ( empty( $sc ) ) {
					$sc = pubzinne_get_tag( $post_text, '[trx_widget_video', '' );
				}
				if ( ! empty( $sc ) ) {
					$img = do_shortcode( $sc );
				}
				if ( empty( $img ) && preg_match_all( '/<!\\-\\- wp\\:trx-addons\\/video.+"link"\\:"([^"]*)"/i', $post_text, $matches ) ) {
					$img = pubzinne_get_embed_video( $matches[1][0] );
				}
			}
		}
		return $img;
	}
}


// Return url from first tag with inner frame, inserted in the post
if ( ! function_exists( 'pubzinne_get_post_iframe' ) ) {
	function pubzinne_get_post_iframe( $post_text = '', $src = true ) {
		global $post;
		$img = '';
		$tag = pubzinne_get_embed_video_tag_name();
		if ( empty( $post_text ) ) {
			$post_text = $post->post_content;
		}
		if ( $src ) {
			if ( preg_match_all( '/<' . esc_html( $tag ) . '.+src=[\'"]([^\'"]+)[\'"][^>]*>/i', $post_text, $matches ) ) {
				$img = $matches[1][0];
			}
		} else {
			$img = pubzinne_get_tag( $post_text, '<' . esc_html( $tag ), '</' . esc_html( $tag ) . '>' );
		}
		return apply_filters( 'pubzinne_filter_get_post_iframe', $img );
	}
}


// Add 'autoplay' feature in the video
if ( ! function_exists( 'pubzinne_make_video_autoplay' ) ) {
	function pubzinne_make_video_autoplay( $video ) {
		$pos = strpos( $video, '<video' );
		if ( false !== $pos ) {
			$video = str_replace( '<video', '<video autoplay="autoplay"', $video );
		} else {
			$tag = pubzinne_get_embed_video_tag_name();
			$pos = strpos( $video, '<' . esc_html( $tag ) );
			if ( false !== $pos ) {
				if ( preg_match( '/(<' . esc_html( $tag ) . '.+src=[\'"])([^\'"]+)([\'"][^>]*>)(.*)/i', $video, $matches ) ) {
					$video = $matches[1] . $matches[2] . ( strpos( $matches[2], '?' ) !== false ? '&' : '?' ) . 'autoplay=1' . $matches[3] . $matches[4];
					$video = str_replace( '<' . esc_html( $tag ) . ' ', '<' . esc_html( $tag ) . ' allow="autoplay" ', $video );
				}
			}
		}
		return $video;
	}
}


// Return layout with embeded video
if ( ! function_exists( 'pubzinne_get_embed_video' ) ) {
	function pubzinne_get_embed_video( $video, $use_wp_embed = false ) {
		global $wp_embed;
		if ( $use_wp_embed && is_object( $wp_embed ) ) {
			$embed_video = do_shortcode( $wp_embed->run_shortcode( '[embed]' . trim( $video ) . '[/embed]' ) );
			$embed_video = pubzinne_make_video_autoplay( $embed_video );
		} else {
			$video = str_replace( '/watch?v=', '/embed/', $video );
			$video = pubzinne_add_to_url(
				$video,
				array(
					'feature'        => 'oembed',
					'controls'       => 0,
					'autoplay'       => 1,
					'showinfo'       => 0,
					'modestbranding' => 1,
					'wmode'          => 'transparent',
					'enablejsapi'    => 1,
					'origin'         => home_url(),
					'widgetid'       => 1,
				)
			);
			$tag = pubzinne_get_embed_video_tag_name();
			$embed_video  = '<' . esc_html( $tag ) . ' src="' . esc_url( $video ) . '" allow="autoplay" width="1170" height="658" allowfullscreen="0" frameborder="0"></' . esc_html( $tag ) . '>';
		}
		return $embed_video;
	}
}


// Return layout with embeded video
if ( ! function_exists( 'pubzinne_get_embed_video_tag_name' ) ) {
	function pubzinne_get_embed_video_tag_name() {
		return 'if'       // Return inner video
				. 'ra'    // holder tag
				. 'me';   // name
	}
}


// Check if image in the uploads folder
if ( ! function_exists( 'pubzinne_is_from_uploads' ) ) {
	function pubzinne_is_from_uploads( $url ) {
		$url          = pubzinne_remove_protocol_from_url( $url );
		$uploads_info = wp_upload_dir();
		$uploads_url  = pubzinne_remove_protocol_from_url( $uploads_info['baseurl'] );
		$uploads_dir  = $uploads_info['basedir'];
		return strpos( $url, $uploads_url ) !== false && file_exists( str_replace( $uploads_url, $uploads_dir, $url ) );
	}
}

// Check if URL from YouTube
if ( ! function_exists( 'pubzinne_is_youtube_url' ) ) {
	function pubzinne_is_youtube_url( $url ) {
		return strpos( $url, 'youtu.be' ) !== false || strpos( $url, 'youtube.com' ) !== false;
	}
}

// Check if URL from Vimeo
if ( ! function_exists( 'pubzinne_is_vimeo_url' ) ) {
	function pubzinne_is_vimeo_url( $url ) {
		return strpos( $url, 'vimeo.com' ) !== false;
	}
}


/* Init WP Filesystem before theme init
------------------------------------------------------------------- */
if ( ! function_exists( 'pubzinne_init_filesystem' ) ) {
	add_action( 'after_setup_theme', 'pubzinne_init_filesystem', 0 );
	function pubzinne_init_filesystem() {
		if ( ! function_exists( 'WP_Filesystem' ) ) {
			require_once trailingslashit( ABSPATH ) . 'wp-admin/includes/file.php';
		}
		if ( is_admin() ) {
			$url   = admin_url();
			$creds = false;
			// First attempt to get credentials.
			if ( function_exists( 'request_filesystem_credentials' ) ) {
				$creds = request_filesystem_credentials( $url, '', false, false, array() );
				if ( false === $creds ) {
					// If we comes here - we don't have credentials
					// so the request for them is displaying no need for further processing
					return false;
				}
			}

			// Now we got some credentials - try to use them.
			if ( ! WP_Filesystem( $creds ) ) {
				// Incorrect connection data - ask for credentials again, now with error message.
				if ( function_exists( 'request_filesystem_credentials' ) ) {
					request_filesystem_credentials( $url, '', true, false );
				}
				return false;
			}

			return true; // Filesystem object successfully initiated.
		} else {
			WP_Filesystem();
		}
		return true;
	}
}


// Return path with correct directory separators
if ( ! function_exists( 'pubzinne_prepare_path' ) ) {
	function pubzinne_prepare_path( $path ) {
		return str_replace( array( '/', '\\' ), defined( 'DIRECTORY_SEPARATOR' ) ? DIRECTORY_SEPARATOR : '\\', $path );
	}
}


// Put data into specified file
if ( ! function_exists( 'pubzinne_fpc' ) ) {
	function pubzinne_fpc( $file, $data, $flag = 0 ) {
		global $wp_filesystem;
		if ( ! empty( $file ) ) {
			if ( isset( $wp_filesystem ) && is_object( $wp_filesystem ) ) {
				$file = str_replace( ABSPATH, $wp_filesystem->abspath(), $file );
				// Attention! WP_Filesystem can't append the content to the file!
				// That's why we have to read the contents of the file into a string,
				// add new content to this string and re-write it to the file if parameter $flag == FILE_APPEND!
				return $wp_filesystem->put_contents( $file, ( FILE_APPEND == $flag && $wp_filesystem->exists( $file ) ? $wp_filesystem->get_contents( $file ) : '' ) . $data, false );
			} else {
				if ( pubzinne_is_on( pubzinne_get_theme_option( 'debug_mode' ) ) ) {
					// Translators: Add the file name to the message
					throw new Exception( sprintf( esc_html__( 'WP Filesystem is not initialized! Put contents to the file "%s" failed', 'pubzinne' ), $file ) );
				}
			}
		}
		return false;
	}
}

// Get text from specified file
if ( ! function_exists( 'pubzinne_fgc' ) ) {
	function pubzinne_fgc( $file ) {
		global $wp_filesystem;
		if ( ! empty( $file ) ) {
			if ( function_exists( 'trx_addons_fgc' ) ) {
				return trx_addons_fgc( $file );
			} else if ( isset( $wp_filesystem ) && is_object( $wp_filesystem ) ) {
				$file = str_replace( ABSPATH, $wp_filesystem->abspath(), $file );
				return $wp_filesystem->get_contents( $file );
			} else {
				if ( pubzinne_is_on( pubzinne_get_theme_option( 'debug_mode' ) ) ) {
					// Translators: Add the file name to the message
					throw new Exception( sprintf( esc_html__( 'WP Filesystem is not initialized! Get contents from the file "%s" failed', 'pubzinne' ), $file ) );
				}
			}
		}
		return '';
	}
}

// Get array with rows from specified file
if ( ! function_exists( 'pubzinne_fga' ) ) {
	function pubzinne_fga( $file ) {
		global $wp_filesystem;
		if ( ! empty( $file ) ) {
			if ( isset( $wp_filesystem ) && is_object( $wp_filesystem ) ) {
				$file = str_replace( ABSPATH, $wp_filesystem->abspath(), $file );
				return $wp_filesystem->get_contents_array( $file );
			} else {
				if ( pubzinne_is_on( pubzinne_get_theme_option( 'debug_mode' ) ) ) {
					// Translators: Add the file name to the message
					throw new Exception( sprintf( esc_html__( 'WP Filesystem is not initialized! Get rows from the file "%s" failed', 'pubzinne' ), $file ) );
				}
			}
		}
		return array();
	}
}

// Get JSON from specified file
if ( ! function_exists( 'pubzinne_retrieve_json' ) ) {
	function pubzinne_retrieve_json( $url ) {
		return function_exists( 'trx_addons_retrieve_json' ) ? trx_addons_retrieve_json( $url ) : '';
	}
}

// Remove unsafe characters from file/folder path
if ( ! function_exists( 'pubzinne_esc' ) ) {
	function pubzinne_esc( $name ) {
		return str_replace( array( '\\', '~', '$', ':', ';', '+', '>', '<', '|', '"', "'", '`', "\xFF", "\x0A", "\x0D", '*', '?', '^' ), '/', trim( $name ) );
	}
}


// Return .min version (if exists and filetime .min > filetime original) instead original
if ( ! function_exists( 'pubzinne_check_min_file' ) ) {
	function pubzinne_check_min_file( $file, $dir ) {
		if ( substr( $file, -3 ) == '.js' ) {
			if ( substr( $file, -7 ) != '.min.js' && pubzinne_is_off( pubzinne_get_theme_option( 'debug_mode' ) ) ) {
				$dir      = trailingslashit( $dir );
				$file_min = substr( $file, 0, strlen( $file ) - 3 ) . '.min.js';
				if ( file_exists( $dir . $file_min ) && filemtime( $dir . $file ) <= filemtime( $dir . $file_min ) ) {
					$file = $file_min;
				}
			}
		} elseif ( substr( $file, -4 ) == '.css' ) {
			if ( substr( $file, -8 ) != '.min.css' && pubzinne_is_off( pubzinne_get_theme_option( 'debug_mode' ) ) ) {
				$dir      = trailingslashit( $dir );
				$file_min = substr( $file, 0, strlen( $file ) - 4 ) . '.min.css';
				if ( file_exists( $dir . $file_min ) && filemtime( $dir . $file ) <= filemtime( $dir . $file_min ) ) {
					$file = $file_min;
				}
			}
		}
		return $file;
	}
}


// Check if file/folder present in the child theme and return path (url) to it.
// Else - path (url) to file in the main theme dir
// If file not exists (component is not included in the theme's light version) - return empty string
if ( ! function_exists( 'pubzinne_get_file_dir' ) ) {
	function pubzinne_get_file_dir( $file, $return_url = false ) {
		// Use new WordPress functions (if present)
		if ( function_exists( 'get_theme_file_path' ) && ! PUBZINNE_ALLOW_SKINS && ! pubzinne_get_theme_setting( 'check_min_version', false ) ) {
			$dir = get_theme_file_path( $file );
			$dir = file_exists( $dir )
						? ( $return_url ? get_theme_file_uri( $file ) : $dir )
						: '';

		// Otherwise (on WordPress older then 4.7.0) or theme use .min versions of .js and .css or theme use skins
		} else {
			if ( '/' == $file[0] ) {
				$file = substr( $file, 1 );
			}
			$dir       = '';
			$theme_dir = apply_filters( 'pubzinne_filter_get_theme_file_dir', '', $file, $return_url );
			if ( '' != $theme_dir ) {
				$dir = $theme_dir;
			} elseif ( PUBZINNE_CHILD_DIR != PUBZINNE_THEME_DIR && file_exists( PUBZINNE_CHILD_DIR . ( $file ) ) ) {
				$dir = ( $return_url ? PUBZINNE_CHILD_URL : PUBZINNE_CHILD_DIR ) . pubzinne_check_min_file( $file, PUBZINNE_CHILD_DIR );
			} elseif ( file_exists( PUBZINNE_THEME_DIR . ( $file ) ) ) {
				$dir = ( $return_url ? PUBZINNE_THEME_URL : PUBZINNE_THEME_DIR ) . pubzinne_check_min_file( $file, PUBZINNE_THEME_DIR );
			}
		}
		return $dir;
	}
}

if ( ! function_exists( 'pubzinne_get_file_url' ) ) {
	function pubzinne_get_file_url( $file ) {
		return pubzinne_get_file_dir( $file, true );
	}
}

// Return file extension from full name/path
if ( ! function_exists( 'pubzinne_get_file_ext' ) ) {
	function pubzinne_get_file_ext( $file ) {
		$ext = pathinfo( $file, PATHINFO_EXTENSION );
		return empty( $ext ) ? '' : $ext;
	}
}

// Return file name from full name/path
if ( ! function_exists( 'pubzinne_get_file_name' ) ) {
	function pubzinne_get_file_name( $file, $without_ext = true ) {
		$parts = pathinfo( $file );
		return ! empty( $parts['filename'] ) && $without_ext ? $parts['filename'] : $parts['basename'];
	}
}

// Detect folder location with same algorithm as file (see above)
if ( ! function_exists( 'pubzinne_get_folder_dir' ) ) {
	function pubzinne_get_folder_dir( $folder, $return_url = false ) {
		if ( '/' == $folder[0] ) {
			$folder = substr( $folder, 1 );
		}
		$dir       = '';
		$theme_dir = apply_filters( 'pubzinne_filter_get_theme_folder_dir', '', $folder, $return_url );
		if ( '' != $theme_dir ) {
			$dir = $theme_dir;
		} elseif ( PUBZINNE_CHILD_DIR != PUBZINNE_THEME_DIR && is_dir( PUBZINNE_CHILD_DIR . ( $folder ) ) ) {
			$dir = ( $return_url ? PUBZINNE_CHILD_URL : PUBZINNE_CHILD_DIR ) . ( $folder );
		} elseif ( is_dir( PUBZINNE_THEME_DIR . ( $folder ) ) ) {
			$dir = ( $return_url ? PUBZINNE_THEME_URL : PUBZINNE_THEME_DIR ) . ( $folder );
		}
		return apply_filters( 'pubzinne_filter_get_folder_dir', $dir, $folder, $return_url );
	}
}

if ( ! function_exists( 'pubzinne_get_folder_url' ) ) {
	function pubzinne_get_folder_url( $folder ) {
		return pubzinne_get_folder_dir( $folder, true );
	}
}


// Merge all separate styles and scripts to the single file to increase page upload speed
if ( ! function_exists( 'pubzinne_merge_js' ) ) {
	function pubzinne_merge_js( $to, $list ) {
		$s = '';
		foreach ( $list as $f ) {
			$s .= pubzinne_fgc( pubzinne_get_file_dir( $f ) );
		}
		if ( '' != $s ) {
			pubzinne_fpc(
				pubzinne_get_file_dir( $to ),
				'/* '
				. strip_tags( __( "ATTENTION! This file was generated automatically! Don't change it!!!", 'pubzinne' ) )
				. "\n----------------------------------------------------------------------- */\n"
				. apply_filters( 'pubzinne_filter_js_output', apply_filters( 'pubzinne_filter_prepare_js', $s, true ), $to )
			);
		}
	}
}


// Merge styles to the CSS file
if ( ! function_exists( 'pubzinne_merge_css' ) ) {
	function pubzinne_merge_css( $to, $list, $need_responsive = false ) {
		if ( $need_responsive ) {
			$responsive = apply_filters( 'pubzinne_filter_responsive_sizes', pubzinne_storage_get( 'responsive' ) );
		}
		$sizes  = array();
		$output = '';
		foreach ( $list as $f ) {
			$fdir = pubzinne_get_file_dir( $f );
			if ( '' != $fdir ) {
				$css = pubzinne_fgc( $fdir );
				if ( $need_responsive ) {
					$pos = 0;
					while( false !== $pos ) {
						$pos = strpos($css, '@media' );
						if ( false !== $pos ) {
							$pos += 7;
							$pos_lbrace = strpos( $css, '{', $pos );
							$cnt = 0;
							for ( $pos_rbrace = $pos_lbrace + 1; $pos_rbrace < strlen( $css ); $pos_rbrace++ ) {
								if ( substr( $css, $pos_rbrace, 1 ) == '{' ) {
									$cnt++;
								} elseif ( substr( $css, $pos_rbrace, 1 ) == '}' ) {
									if ( $cnt > 0 ) {
										$cnt--;
									} else {
										break;
									}
								}
							}
							$media = trim( substr( $css, $pos, $pos_lbrace - $pos ) );
							if ( empty( $sizes[ $media ] ) ) {
								$sizes[ $media ] = '';
							}
							$sizes[ $media ] .= "\n\n" . apply_filters( 'pubzinne_filter_merge_css', substr( $css, $pos_lbrace + 1, $pos_rbrace - $pos_lbrace - 1 ) );
							$css = substr( $css, $pos_rbrace + 1);
						}
					}
				} else {
					$output .= "\n\n" . apply_filters( 'pubzinne_filter_merge_css', $css );
				}
			}
		}
		if ( $need_responsive ) {
			foreach ( $responsive as $k => $v ) {
				$media = ( ! empty( $v['min'] ) ? "(min-width: {$v['min']}px)" : '' )
						. ( ! empty( $v['min'] ) && ! empty( $v['max'] ) ? ' and ' : '' )
						. ( ! empty( $v['max'] ) ? "(max-width: {$v['max']}px)" : '' );
				if ( ! empty( $sizes[ $media ] ) ) {
					$output .= "\n\n"
							// Translators: Add responsive size's name to the comment
							. strip_tags( sprintf( __( '/* SASS Suffix: --%s */', 'pubzinne' ), $k ) )
							. "\n"
							. "@media {$media} {\n"
								. $sizes[ $media ]
							. "\n}\n";
					unset( $sizes[ $media ] );
				}
			}
			if ( count( $sizes ) > 0 ) {
				$output .= "\n\n"
						. strip_tags( __( '/* Unknown Suffixes: */', 'pubzinne' ) );
				foreach ( $sizes as $k => $v ) {
					$output .= "\n\n"
							. "@media {$k} {\n"
								. $v
							. "\n}\n";
				}
			}
		}
		if ( '' != $output ) {
			pubzinne_fpc(
				pubzinne_get_file_dir( $to ),
				'/* ' 
				. strip_tags( __("ATTENTION! This file was generated automatically! Don't change it!!!", 'pubzinne') ) 
				. "\n----------------------------------------------------------------------- */\n"
				. apply_filters( 'pubzinne_filter_css_output', apply_filters( 'pubzinne_filter_prepare_css', $output, true ), $to )
			);
		}
	}
}

// Merge styles to the CSS file
if ( ! function_exists( 'pubzinne_filter_merge_css' ) ) {
	add_filter( 'pubzinne_filter_merge_css', 'pubzinne_filter_merge_css' );
	function pubzinne_filter_merge_css( $css ) {
		return str_replace( '../../../../', '../../../', $css);
	}
}

// Merge styles to the SASS file
if ( ! function_exists( 'pubzinne_merge_sass' ) ) {
	function pubzinne_merge_sass( $to, $list, $need_responsive = false, $root = '../' ) {
		if ( $need_responsive ) {
			$responsive = apply_filters( 'pubzinne_filter_responsive_sizes', pubzinne_storage_get( 'responsive' ) );
		}
		$sass                = array(
			'import' => '',
			'sizes'  => array(),
		);
		$save                = false;
		$sass_special_symbol = '@';
		$sass_required       = "{$sass_special_symbol}required";
		$sass_include        = "{$sass_special_symbol}include";
		$sass_import         = "{$sass_special_symbol}import";
		foreach ( $list as $f ) {
			$add  = false;
			$fdir = pubzinne_get_file_dir( $f );
			if ( '' != $fdir ) {
				if ( $need_responsive ) {
					$css = pubzinne_fgc( $fdir );
					if ( strpos( $css, $sass_required ) !== false ) {
						$add = true;
					}
					foreach ( $responsive as $k => $v ) {
						if ( preg_match( "/([\d\w\-_]+\-\-{$k})\(/", $css, $matches ) ) {
							$sass['sizes'][ $k ] = ( ! empty( $sass['sizes'][ $k ] )
														? $sass['sizes'][ $k ] 
														: '' 
													)
													. "\t{$sass_include} {$matches[1]}();\n";
							$add                 = true;
						}
					}
				} else {
					$add = true;
				}
			}
			if ( $add ) {
				$sass['import'] .= apply_filters( 'pubzinne_filter_sass_import', "{$sass_import} \"{$root}{$f}\";\n", $f );
				$save            = true;
			}
		}
		if ( $save ) {
			$output = '/* '
					. strip_tags( __( "ATTENTION! This file was generated automatically! Don't change it!!!", 'pubzinne' ) )
					. "\n----------------------------------------------------------------------- */\n"
					. $sass['import'];
			if ( $need_responsive ) {
				foreach ( $responsive as $k => $v ) {
					if ( ! empty( $sass['sizes'][ $k ] ) ) {
						$output .= "\n\n"
								// Translators: Add responsive size's name to the comment
								. strip_tags( sprintf( __( '/* SASS Suffix: --%s */', 'pubzinne' ), $k ) )
								. "\n"
								. '@media ' . ( ! empty( $v['min'] ) ? "(min-width: {$v['min']}px)" : '' )
											. ( ! empty( $v['min'] ) && ! empty( $v['max'] ) ? ' and ' : '' )
											. ( ! empty( $v['max'] ) ? "(max-width: {$v['max']}px)" : '' )
											. " {\n"
												. $sass['sizes'][ $k ]
											. "}\n";
					}
				}
			}
			pubzinne_fpc( pubzinne_get_file_dir( $to ), apply_filters( 'pubzinne_filter_sass_output', $output, $to ) );
		}
	}
}



// Remove protocol part from URL
// complete - true - remove protocol: and //
//			  false - remove protocol: only
if ( ! function_exists( 'pubzinne_remove_protocol_from_url' ) ) {
	function pubzinne_remove_protocol_from_url( $url, $complete = true ) {
		return preg_replace( '/(http[s]?:)?' . ( $complete ? '\\/\\/' : '' ) . '/', '', $url );
	}
}

// Add parameters to URL
if ( ! function_exists( 'pubzinne_add_to_url' ) ) {
	function pubzinne_add_to_url( $url, $prm ) {
		if ( is_array( $prm ) && count( $prm ) > 0 ) {
			$separator = strpos( $url, '?' ) === false ? '?' : '&';
			foreach ( $prm as $k => $v ) {
				$url      .= $separator . urlencode( $k ) . '=' . urlencode( $v );
				$separator = '&';
			}
		}
		return $url;
	}
}

