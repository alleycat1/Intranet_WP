<?php
/**
 * Theme storage manipulations
 *
 * @package PUBZINNE
 * @since PUBZINNE 1.0
 */

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) {
	exit; }

// Get theme variable
if ( ! function_exists( 'pubzinne_storage_get' ) ) {
	function pubzinne_storage_get( $var_name, $default = '' ) {
		global $PUBZINNE_STORAGE;
		return isset( $PUBZINNE_STORAGE[ $var_name ] ) ? $PUBZINNE_STORAGE[ $var_name ] : $default;
	}
}

// Set theme variable
if ( ! function_exists( 'pubzinne_storage_set' ) ) {
	function pubzinne_storage_set( $var_name, $value ) {
		global $PUBZINNE_STORAGE;
		$PUBZINNE_STORAGE[ $var_name ] = $value;
	}
}

// Check if theme variable is empty
if ( ! function_exists( 'pubzinne_storage_empty' ) ) {
	function pubzinne_storage_empty( $var_name, $key = '', $key2 = '' ) {
		global $PUBZINNE_STORAGE;
		if ( ! empty( $key ) && ! empty( $key2 ) ) {
			return empty( $PUBZINNE_STORAGE[ $var_name ][ $key ][ $key2 ] );
		} elseif ( ! empty( $key ) ) {
			return empty( $PUBZINNE_STORAGE[ $var_name ][ $key ] );
		} else {
			return empty( $PUBZINNE_STORAGE[ $var_name ] );
		}
	}
}

// Check if theme variable is set
if ( ! function_exists( 'pubzinne_storage_isset' ) ) {
	function pubzinne_storage_isset( $var_name, $key = '', $key2 = '' ) {
		global $PUBZINNE_STORAGE;
		if ( ! empty( $key ) && ! empty( $key2 ) ) {
			return isset( $PUBZINNE_STORAGE[ $var_name ][ $key ][ $key2 ] );
		} elseif ( ! empty( $key ) ) {
			return isset( $PUBZINNE_STORAGE[ $var_name ][ $key ] );
		} else {
			return isset( $PUBZINNE_STORAGE[ $var_name ] );
		}
	}
}

// Delete theme variable
if ( ! function_exists( 'pubzinne_storage_unset' ) ) {
	function pubzinne_storage_unset( $var_name, $key = '', $key2 = '' ) {
		global $PUBZINNE_STORAGE;
		if ( ! empty( $key ) && ! empty( $key2 ) ) {
			unset( $PUBZINNE_STORAGE[ $var_name ][ $key ][ $key2 ] );
		} elseif ( ! empty( $key ) ) {
			unset( $PUBZINNE_STORAGE[ $var_name ][ $key ] );
		} else {
			unset( $PUBZINNE_STORAGE[ $var_name ] );
		}
	}
}

// Inc/Dec theme variable with specified value
if ( ! function_exists( 'pubzinne_storage_inc' ) ) {
	function pubzinne_storage_inc( $var_name, $value = 1 ) {
		global $PUBZINNE_STORAGE;
		if ( empty( $PUBZINNE_STORAGE[ $var_name ] ) ) {
			$PUBZINNE_STORAGE[ $var_name ] = 0;
		}
		$PUBZINNE_STORAGE[ $var_name ] += $value;
	}
}

// Concatenate theme variable with specified value
if ( ! function_exists( 'pubzinne_storage_concat' ) ) {
	function pubzinne_storage_concat( $var_name, $value ) {
		global $PUBZINNE_STORAGE;
		if ( empty( $PUBZINNE_STORAGE[ $var_name ] ) ) {
			$PUBZINNE_STORAGE[ $var_name ] = '';
		}
		$PUBZINNE_STORAGE[ $var_name ] .= $value;
	}
}

// Get array (one or two dim) element
if ( ! function_exists( 'pubzinne_storage_get_array' ) ) {
	function pubzinne_storage_get_array( $var_name, $key, $key2 = '', $default = '' ) {
		global $PUBZINNE_STORAGE;
		if ( empty( $key2 ) ) {
			return ! empty( $var_name ) && ! empty( $key ) && isset( $PUBZINNE_STORAGE[ $var_name ][ $key ] ) ? $PUBZINNE_STORAGE[ $var_name ][ $key ] : $default;
		} else {
			return ! empty( $var_name ) && ! empty( $key ) && isset( $PUBZINNE_STORAGE[ $var_name ][ $key ][ $key2 ] ) ? $PUBZINNE_STORAGE[ $var_name ][ $key ][ $key2 ] : $default;
		}
	}
}

// Set array element
if ( ! function_exists( 'pubzinne_storage_set_array' ) ) {
	function pubzinne_storage_set_array( $var_name, $key, $value ) {
		global $PUBZINNE_STORAGE;
		if ( ! isset( $PUBZINNE_STORAGE[ $var_name ] ) ) {
			$PUBZINNE_STORAGE[ $var_name ] = array();
		}
		if ( '' === $key ) {
			$PUBZINNE_STORAGE[ $var_name ][] = $value;
		} else {
			$PUBZINNE_STORAGE[ $var_name ][ $key ] = $value;
		}
	}
}

// Set two-dim array element
if ( ! function_exists( 'pubzinne_storage_set_array2' ) ) {
	function pubzinne_storage_set_array2( $var_name, $key, $key2, $value ) {
		global $PUBZINNE_STORAGE;
		if ( ! isset( $PUBZINNE_STORAGE[ $var_name ] ) ) {
			$PUBZINNE_STORAGE[ $var_name ] = array();
		}
		if ( ! isset( $PUBZINNE_STORAGE[ $var_name ][ $key ] ) ) {
			$PUBZINNE_STORAGE[ $var_name ][ $key ] = array();
		}
		if ( '' === $key2 ) {
			$PUBZINNE_STORAGE[ $var_name ][ $key ][] = $value;
		} else {
			$PUBZINNE_STORAGE[ $var_name ][ $key ][ $key2 ] = $value;
		}
	}
}

// Merge array elements
if ( ! function_exists( 'pubzinne_storage_merge_array' ) ) {
	function pubzinne_storage_merge_array( $var_name, $key, $value ) {
		global $PUBZINNE_STORAGE;
		if ( ! isset( $PUBZINNE_STORAGE[ $var_name ] ) ) {
			$PUBZINNE_STORAGE[ $var_name ] = array();
		}
		if ( '' === $key ) {
			$PUBZINNE_STORAGE[ $var_name ] = array_merge( $PUBZINNE_STORAGE[ $var_name ], $value );
		} else {
			$PUBZINNE_STORAGE[ $var_name ][ $key ] = array_merge( $PUBZINNE_STORAGE[ $var_name ][ $key ], $value );
		}
	}
}

// Add array element after the key
if ( ! function_exists( 'pubzinne_storage_set_array_after' ) ) {
	function pubzinne_storage_set_array_after( $var_name, $after, $key, $value = '' ) {
		global $PUBZINNE_STORAGE;
		if ( ! isset( $PUBZINNE_STORAGE[ $var_name ] ) ) {
			$PUBZINNE_STORAGE[ $var_name ] = array();
		}
		if ( is_array( $key ) ) {
			pubzinne_array_insert_after( $PUBZINNE_STORAGE[ $var_name ], $after, $key );
		} else {
			pubzinne_array_insert_after( $PUBZINNE_STORAGE[ $var_name ], $after, array( $key => $value ) );
		}
	}
}

// Add array element before the key
if ( ! function_exists( 'pubzinne_storage_set_array_before' ) ) {
	function pubzinne_storage_set_array_before( $var_name, $before, $key, $value = '' ) {
		global $PUBZINNE_STORAGE;
		if ( ! isset( $PUBZINNE_STORAGE[ $var_name ] ) ) {
			$PUBZINNE_STORAGE[ $var_name ] = array();
		}
		if ( is_array( $key ) ) {
			pubzinne_array_insert_before( $PUBZINNE_STORAGE[ $var_name ], $before, $key );
		} else {
			pubzinne_array_insert_before( $PUBZINNE_STORAGE[ $var_name ], $before, array( $key => $value ) );
		}
	}
}

// Push element into array
if ( ! function_exists( 'pubzinne_storage_push_array' ) ) {
	function pubzinne_storage_push_array( $var_name, $key, $value ) {
		global $PUBZINNE_STORAGE;
		if ( ! isset( $PUBZINNE_STORAGE[ $var_name ] ) ) {
			$PUBZINNE_STORAGE[ $var_name ] = array();
		}
		if ( '' === $key ) {
			array_push( $PUBZINNE_STORAGE[ $var_name ], $value );
		} else {
			if ( ! isset( $PUBZINNE_STORAGE[ $var_name ][ $key ] ) ) {
				$PUBZINNE_STORAGE[ $var_name ][ $key ] = array();
			}
			array_push( $PUBZINNE_STORAGE[ $var_name ][ $key ], $value );
		}
	}
}

// Pop element from array
if ( ! function_exists( 'pubzinne_storage_pop_array' ) ) {
	function pubzinne_storage_pop_array( $var_name, $key = '', $defa = '' ) {
		global $PUBZINNE_STORAGE;
		$rez = $defa;
		if ( '' === $key ) {
			if ( isset( $PUBZINNE_STORAGE[ $var_name ] ) && is_array( $PUBZINNE_STORAGE[ $var_name ] ) && count( $PUBZINNE_STORAGE[ $var_name ] ) > 0 ) {
				$rez = array_pop( $PUBZINNE_STORAGE[ $var_name ] );
			}
		} else {
			if ( isset( $PUBZINNE_STORAGE[ $var_name ][ $key ] ) && is_array( $PUBZINNE_STORAGE[ $var_name ][ $key ] ) && count( $PUBZINNE_STORAGE[ $var_name ][ $key ] ) > 0 ) {
				$rez = array_pop( $PUBZINNE_STORAGE[ $var_name ][ $key ] );
			}
		}
		return $rez;
	}
}

// Inc/Dec array element with specified value
if ( ! function_exists( 'pubzinne_storage_inc_array' ) ) {
	function pubzinne_storage_inc_array( $var_name, $key, $value = 1 ) {
		global $PUBZINNE_STORAGE;
		if ( ! isset( $PUBZINNE_STORAGE[ $var_name ] ) ) {
			$PUBZINNE_STORAGE[ $var_name ] = array();
		}
		if ( empty( $PUBZINNE_STORAGE[ $var_name ][ $key ] ) ) {
			$PUBZINNE_STORAGE[ $var_name ][ $key ] = 0;
		}
		$PUBZINNE_STORAGE[ $var_name ][ $key ] += $value;
	}
}

// Concatenate array element with specified value
if ( ! function_exists( 'pubzinne_storage_concat_array' ) ) {
	function pubzinne_storage_concat_array( $var_name, $key, $value ) {
		global $PUBZINNE_STORAGE;
		if ( ! isset( $PUBZINNE_STORAGE[ $var_name ] ) ) {
			$PUBZINNE_STORAGE[ $var_name ] = array();
		}
		if ( empty( $PUBZINNE_STORAGE[ $var_name ][ $key ] ) ) {
			$PUBZINNE_STORAGE[ $var_name ][ $key ] = '';
		}
		$PUBZINNE_STORAGE[ $var_name ][ $key ] .= $value;
	}
}

// Call object's method
if ( ! function_exists( 'pubzinne_storage_call_obj_method' ) ) {
	function pubzinne_storage_call_obj_method( $var_name, $method, $param = null ) {
		global $PUBZINNE_STORAGE;
		if ( null === $param ) {
			return ! empty( $var_name ) && ! empty( $method ) && isset( $PUBZINNE_STORAGE[ $var_name ] ) ? $PUBZINNE_STORAGE[ $var_name ]->$method() : '';
		} else {
			return ! empty( $var_name ) && ! empty( $method ) && isset( $PUBZINNE_STORAGE[ $var_name ] ) ? $PUBZINNE_STORAGE[ $var_name ]->$method( $param ) : '';
		}
	}
}

// Get object's property
if ( ! function_exists( 'pubzinne_storage_get_obj_property' ) ) {
	function pubzinne_storage_get_obj_property( $var_name, $prop, $default = '' ) {
		global $PUBZINNE_STORAGE;
		return ! empty( $var_name ) && ! empty( $prop ) && isset( $PUBZINNE_STORAGE[ $var_name ]->$prop ) ? $PUBZINNE_STORAGE[ $var_name ]->$prop : $default;
	}
}
