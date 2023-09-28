<?php
/**
 * Debug utilities (for internal use only!)
 *
 * @package ThemeREX Addons
 * @since v1.0
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

// Short analogs for debug functions
//------------------------------------------------------------------------

if ( ! function_exists('dcl') ) {
	/**
	 * Console log - output any message to the screen (page output). If user is not logged in - output nothing.
	 * Use it to output any debug information to the page output.
	 * This function is analog of console.log() in JavaScript
	 *
	 * @param string $msg  Message to output
	 */
	function dcl( $msg ) {
		if ( ! function_exists('is_user_logged_in' ) || is_user_logged_in() ) {
			echo '<pre>' . esc_html( $msg ) . '</pre>';
		}
	}
}

if ( ! function_exists('dco') ) {
	/**
	 * Console obj - output object structure to the screen (page output). If user is not logged in - output nothing.
	 * Use it to output any object or array to the page output.
	 * This function is analog of console.log() in JavaScript.
	 * This function is analog of print_r() in PHP and its a shorthand for trx_addons_debug_dump_screen()
	 *
	 * @param mixed $var  Object to output
	 * @param int $lvl    Depth of output. -1 - all levels. Default -1
	 */
	function dco( $var, $lvl = -1 ) {
		if ( ! function_exists('is_user_logged_in' ) || is_user_logged_in() ) {
			trx_addons_debug_dump_screen( $var, $lvl );
		}
	}
}

if ( ! function_exists('dcp') ) {
	/**
	 * Console print - output object structure to the screen (page output). If user is not logged in - output nothing.
	 * Use it to output any object or array to the page output.
	 * This function is analog of console.log() in JavaScript.
	 * This function is a formatted shorthand of print_r() in PHP.
	 *
	 * @param mixed $var  Object to output
	 */
	function dcp( $var ) {
		if ( ! function_exists('is_user_logged_in' ) || is_user_logged_in() ) {
			ob_start();
			print_r( $var );
			$output = ob_get_contents();
			ob_end_clean();
			echo '<pre>' . preg_replace( '/[\s]*\([\s]*\)/', '()', str_replace( "\n\n", "\n", $output ) ) . '</pre>';
		}
	}
}

if ( ! function_exists('dcs') ) {
	/**
	 * Console stack - output calls stack to the screen (page output). If user is not logged in - output nothing.
	 * Use it to output calls stack to the page output.
	 * This function is analog of console.trace() in JavaScript.
	 * This function is analog of debug_backtrace() in PHP and its a shorthand for trx_addons_debug_calls_stack_screen()
	 *
	 * @param int $depth  Depth of the stack. -1 - all stack
	 * @param int $offset Offset of the stack. 0 - from the begin. Default 3
	 * @param bool $args  Output arguments of functions in the stack
	 */
	function dcs( $depth = -1, $offset = 3, $args = false ) {
		if ( ! function_exists('is_user_logged_in' ) || is_user_logged_in() ) {
			trx_addons_debug_calls_stack_screen( $depth, $offset, $args );
		}
	}
}

if ( ! function_exists('dcw') ) {
	/**
	 * Console WP - output WP is_... states to the screen (page output). If user is not logged in - output nothing.
	 * Use it to output WP is_... states to the page output.
	 * This function is a formatted shorthand of trx_addons_debug_dump_wp()
	 *
	 * @param mixed $q  Object to output
	 */
	function dcw( $q = null ) {
		if ( ! function_exists('is_user_logged_in' ) || is_user_logged_in() ) {
			echo '<code>' . nl2br( trx_addons_debug_dump_wp( $q ) ) . '</code>';
		}
	}
}

if ( ! function_exists('dfl') ) {
	/**
	 * File log - output any message to the file debug.log in the theme's folder.
	 * This function is useful to logging any debug information inside AJAX handlers.
	 * This function is a shorthand for trx_addons_debug_trace_message().
	 *
	 * @param string $msg  Message to output
	 */
	function dfl( $msg ) {
		trx_addons_debug_trace_message( $msg );
	}
}

if ( ! function_exists('dfo') ) {
	/**
	 * File obj - output object (array) structure to the file debug.log in the theme's folder.
	 * This function is useful to logging any debug information inside AJAX handlers.
	 * This function is a shorthand for trx_addons_debug_dump_file()
	 *
	 * @param mixed $var  Object to output
	 * @param int $lvl    Depth of output. -1 - all levels. Default -1
	 */
	function dfo( $var, $lvl = -1 ) {
		trx_addons_debug_dump_file( $var, $lvl );
	}
}

if ( ! function_exists('dfp') ) {
	/**
	 * File print - output object (array) structure to the file debug.log in the theme's folder.
	 * This function is useful to logging any debug information inside AJAX handlers.
	 * This function is a formatted shorthand of print_r() in PHP.
	 *
	 * @param mixed $var  Object to output
	 */
	function dfp( $var ) {
		ob_start();
		print_r( $var );
		$output = ob_get_contents();
		ob_end_clean();
		trx_addons_debug_trace_message( "\n" . preg_replace( '/[\s]*\([\s]*\)/gm', '()', str_replace( "\n\n", "\n", $output ) ) );
	}
}

if ( ! function_exists('dfs') ) {
	/**
	 * File stack - output calls stack to the file debug.log in the theme's folder.
	 * This function is useful to logging any debug information inside AJAX handlers.
	 * This function is analog of debug_backtrace() in PHP and its a shorthand for trx_addons_debug_calls_stack_file()
	 *
	 * @param int $depth  Depth of the stack. -1 - all stack
	 * @param int $offset Offset of the stack. 0 - from the begin. Default 3
	 * @param bool $args  Output arguments of functions in the stack
	 */
	function dfs( $depth = -1, $offset = 3, $args = false ) {
		trx_addons_debug_calls_stack_file( $depth, $offset, $args );
	}
}

if ( ! function_exists('dfw') ) {
	/**
	 * File WP - output WP is_... states to the file debug.log in the theme's folder.
	 * This function is useful to logging any debug information inside AJAX handlers.
	 * This function is a formatted shorthand of trx_addons_debug_dump_wp()
	 *
	 * @param mixed $q  Query object to output
	 */	
	function dfw( $q = null ) {
		trx_addons_debug_trace_message( trx_addons_debug_dump_wp( $q ) );
	}
}

if ( ! function_exists('ddo') ) {
	/**
	 * Dump object - return object (array) structure.
	 * This function is a shorthand of trx_addons_debug_dump_var()
	 *
	 * @param mixed $var  Object to output
	 * @param int $lvl    Depth of output. Default 0
	 * @param int $max_lvl  Max depth of output. -1 - unlimited. Default -1
	 */
	function ddo( $var, $lvl = 0, $max_lvl = -1 ) {
		return trx_addons_debug_dump_var( $var, $lvl, $max_lvl );
	}
}


// Main debug functions
//------------------------------------------------------------------------

if ( ! function_exists( 'trx_addons_debug_calls_stack' ) ) {
	/**
	 * Return calls stack as array
	 * 
	 * @param int $depth  Depth of the stack. -1 - all stack
	 * @param int $offset Offset of the stack. 0 - from the begin. Default 3
	 * @param bool $args  True - include arguments into stack
	 * 
	 * @return array    Calls stack
	 */
	function trx_addons_debug_calls_stack( $depth = -1, $offset = 3, $args = false) {
		$stack = debug_backtrace( $args
									? DEBUG_BACKTRACE_PROVIDE_OBJECT
									: DEBUG_BACKTRACE_IGNORE_ARGS,
								$depth > 0
									? $offset + $depth
									: 0
								);
		if ( $offset > 0 ) {
			array_splice( $stack, 0, $offset );
		}
		return $stack;
	}
}

if ( ! function_exists( 'trx_addons_debug_calls_stack_screen' ) ) {
	/**
	 * Output calls stack to the current page output 
	 * 
	 * @param int $depth  Depth of the stack. -1 - all stack
	 * @param int $offset Offset of the stack. 0 - from the begin. Default 3
	 * @param bool $args  True - include arguments into stack
	 */
	function trx_addons_debug_calls_stack_screen( $depth = -1, $offset = 3, $args = false ) {
		$s = trx_addons_debug_calls_stack( $depth, $offset, $args );
		trx_addons_debug_dump_screen( $s );
	}
}

if ( ! function_exists( 'trx_addons_debug_calls_stack_file' ) ) {
	/**
	 * Output calls stack to the file debug.log in the theme's folder
	 * 
	 * @param int $depth  Depth of the stack. -1 - all stack
	 * @param int $offset Offset of the stack. 0 - from the begin. Default 3
	 * @param bool $args  True - include arguments into stack
	 */
	function trx_addons_debug_calls_stack_file( $depth = -1, $offset = 3, $args = false ) {
		$s = trx_addons_debug_calls_stack( $depth, $offset, $args );
		trx_addons_debug_dump_file( $s );
	}
}

if ( ! function_exists( 'trx_addons_debug_dump_screen' ) ) {
	/**
	 * Output var's dump to the current page output
	 * 
	 * @param mixed $var   Variable to output
	 * @param int   $level Max level for recursion
	 */
	function trx_addons_debug_dump_screen( $var, $level = -1 ) {
		echo "<pre>\n" . esc_html( trx_addons_debug_dump_var( $var, 0, $level ) ) . "</pre>\n";
	}
}

if ( ! function_exists( 'trx_addons_debug_trace_message' ) ) {
	/**
	 * Output a custom message to the file debug.log inside the theme's folder
	 * 
	 * @param string $msg Message to output
	 */
	function trx_addons_debug_trace_message( $msg ) {
		trx_addons_fpc( get_stylesheet_directory() . '/debug.log', date( 'd.m.Y H:i:s' ) . " {$msg}\n", FILE_APPEND );
	}
}

if ( ! function_exists( 'trx_addons_debug_dump_file' ) ) {
	/**
	 * Output var's dump to the file debug.log inside the theme's folder
	 * 
	 * @param mixed $var   Variable to output
	 * @param int   $level Max level for recursion
	 */
	function trx_addons_debug_dump_file( $var, $level = -1 ) {
		trx_addons_debug_trace_message( "\n\n" . trx_addons_debug_dump_var( $var, 0, $level ) );
	}
}

if ( ! function_exists( 'trx_addons_debug_dump_var' ) ) {
	/**
	 * Return var's dump as string
	 * 
	 * @param mixed $var   Variable to output
	 * @param int   $level Current level for recursion
	 * @param int   $max_level Max level for recursion
	 * 
	 * @return string   Dump
	 */
	function trx_addons_debug_dump_var( $var, $level = 0, $max_level = -1 )  {
		if ( is_array( $var ) ) {
			$type = 'Array[' . count($var) . ']';
		} else if ( is_object( $var ) ) {
			$type = 'Object';
		} else {
			$type = '';
		}
		if ( $type ) {
			$rez = "{$type}\n";
			if ( $max_level < 0 || $level < $max_level ) {
				$level++;
				foreach ( $var as $k => $v ) {
					if ( is_array( $v ) && $k === "GLOBALS" ) continue;
					for ( $i = 0; $i < $level * 3; $i++ ) {
						$rez .= " ";
					}
					$rez .= $k . ' => ' .  trx_addons_debug_dump_var( $v, $level, $max_level );
				}
			}
		} else if ( is_bool( $var ) ) {
			$rez = ( $var ? 'true' : 'false' ) . "\n";
		} else if ( is_numeric( $var ) ) {
			$rez = $var . "\n";
		} else {
			$rez = '"' . $var . "\"\n";
		}
		return $rez;
	}
}

if ( ! function_exists('trx_addons_debug_dump_wp' ) ) {
	/**
	 * Output WP query and WP core functions is_xxx() to the current page output
	 *
	 * @param object $query WP query object
	 */
	function trx_addons_debug_dump_wp( $query = null ) {
		global $wp_query;
		if ( ! $query && ! empty( $wp_query ) ) {
			$query = $wp_query;
		}
		return 
			  "\naction     = " . current_action()
			. "\nadmin      = " . (int) is_admin()
			. "\najax       = " . (int) wp_doing_ajax()
			. "\nmobile     = " . (int) wp_is_mobile()
			. "\nmain_query = " . (int) is_main_query() . ( $query ? "  query=" . (int) $query->is_main_query() : '' )
			. "\nfront_page = " . (int) is_front_page() . ( $query ? "  query=" . (int) $query->is_front_page() : '' )
			. "\nhome       = " . (int) is_home()       . ( $query ? "  query=" . (int) $query->is_home() . "  query->is_posts_page=" . (int) $query->is_posts_page : '' )
			. "\nsearch     = " . (int) is_search()     . ( $query ? "  query=" . (int) $query->is_search()     : '' )
			. "\ncategory   = " . (int) is_category()   . ( $query ? "  query=" . (int) $query->is_category()   : '' )
			. "\ntag        = " . (int) is_tag()        . ( $query ? "  query=" . (int) $query->is_tag()        : '' )
			. "\ntax        = " . (int) is_tax()        . ( $query ? "  query=" . (int) $query->is_tax()        : '' )
			. "\narchive    = " . (int) is_archive()    . ( $query ? "  query=" . (int) $query->is_archive()    : '' )
			. "\nday        = " . (int) is_day()        . ( $query ? "  query=" . (int) $query->is_day()        : '' )
			. "\nmonth      = " . (int) is_month()      . ( $query ? "  query=" . (int) $query->is_month()      : '' )
			. "\nyear       = " . (int) is_year()       . ( $query ? "  query=" . (int) $query->is_year()       : '' )
			. "\nauthor     = " . (int) is_author()     . ( $query ? "  query=" . (int) $query->is_author()     : '' )
			. "\nsingular   = " . (int) trx_addons_is_singular()   . ( $query ? "  query=" . (int) $query->trx_addons_is_singular()   : '' )
			. "\npage       = " . (int) is_page()       . ( $query ? "  query=" . (int) $query->is_page()       : '' )
			. "\nsingle     = " . (int) trx_addons_is_single()     . ( $query ? "  query=" . (int) $query->trx_addons_is_single()     : '' )
			. "\nattachment = " . (int) is_attachment() . ( $query ? "  query=" . (int) $query->is_attachment() : '' )
			. "\n404        = " . (int) is_404()        . ( $query ? "  query=" . (int) $query->is_404() : '' )
			. "\n";
	}
}
