<?php
/**
 * HTML manipulations
 *
 * @package ThemeREX Addons
 * @since v1.0
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}



/* CSS & JS
-------------------------------------------------------------------------------- */

if ( ! function_exists( 'trx_addons_get_css_from_atts' ) ) {
	/**
	 * Return string with CSS rules from the shortcode's attributes
	 * 
	 * @param array $atts    Array with shortcode's attributes
	 * @param array $allowed Array with allowed attributes
	 * 
	 * @return string        String with CSS rules
	 */
	function trx_addons_get_css_from_atts( $atts, $allowed ) {
		$css = '';
		$atts_with_units = apply_filters( 'trx_addons_filter_atts_with_units', array( 'margin', 'padding', 'border-radius', 'border-width', 'font-size', 'line-height', 'letter-spacing', 'width', 'height', 'top', 'right', 'bottom', 'left' ) );
		foreach ( $allowed as $k => $v ) {
			if ( ! empty( $v ) && ( ! empty( $atts[ $k ] ) || ! empty( $v['default'] ) ) ) {
				$rule = is_array( $v ) ? $v['rule'] : $v;
				if ( is_array( $v ) ) {
					$css .= $rule . ':' . ( ! empty( $atts[ $k ] )
											? esc_attr( in_array( $rule, $atts_with_units ) ? trx_addons_prepare_css_value( $atts[ $k ] ) : $atts[ $k ] )
											: esc_attr( in_array( $rule, $atts_with_units ) ? trx_addons_prepare_css_value( $v['default'] ) : $v['default'] )
											)
										. ';';
				} else {
					$css .= $rule . ':' . esc_attr( in_array( $rule, $atts_with_units ) ? trx_addons_prepare_css_value( $atts[ $k ] ) : $atts[ $k ] ) . ';';
				}
			}
		}
		return $css;
	}
}


if ( ! function_exists( 'trx_addons_get_css_position_from_values' ) ) {
	/**
	 * Return string with position rules (margins and dimensions) for the style attribute. If $top is array - use it as array with values.
	 * Add '!important' to the value if it starts from '!'. For example: '!10px'. Add 'px' if a unit is not present in the value.
	 * 
	 * @param string $top     top margin
	 * @param string $right   right margin
	 * @param string $bottom  bottom margin
	 * @param string $left    left margin
	 * @param string $width   width
	 * @param string $height  height
	 * 
	 * @return string         with css rules
	 */
	function trx_addons_get_css_position_from_values( $top = '', $right = '', $bottom = '', $left = '', $width = '', $height = '' ) {
		if ( ! is_array( $top ) ) {
			$top = compact( 'top','right','bottom','left','width','height' );
		}
		$output = '';
		if ( is_array( $top ) && count( $top ) > 0 ) {
			foreach ( $top as $k => $v ) {
				$imp = substr( $v, 0, 1 );
				if ( $imp == '!' ) {
					$v = substr( $v, 1 );
				}
				if ( $v != '' ) {
					$output .= ( $k == 'width'
									? 'width'
									: ( $k == 'height'
										? 'height'
										: 'margin-' . esc_attr( $k )
										)
									)
								. ':'
								. esc_attr( trx_addons_prepare_css_value( $v ) )
								. ( $imp == '!' ? ' !important' : '' )
								. ';';
				}
			}
		}
		return $output;
	}
}

if ( ! function_exists( 'trx_addons_get_css_dimensions_from_values' ) ) {
	/**
	 * Return string with dimensions rules (width and height) for the style attribute. If $width is array - use it as array with values.
	 * Add '!important' to the value if it starts from '!'. For example: '!10px'. Add 'px' if a unit is not present in the value.
	 * 
	 * @param string $width   width
	 * @param string $height  height
	 * 
	 * @return string         with css rules
	 */
	function trx_addons_get_css_dimensions_from_values( $width = '', $height = '' ) {
		if ( ! is_array( $width ) ) {
			$width = compact( 'width', 'height' );
		}
		$output = '';
		if ( is_array( $width ) && count( $width ) > 0 ) {
			foreach ( $width as $k => $v ) {
				$imp = substr( $v, 0, 1 );
				if ( $imp == '!' ) {
					$v = substr( $v, 1 );
				}
				if ( $v != '' ) {
					$output .= esc_attr( $k ) . ':' . esc_attr( trx_addons_prepare_css_value( $v ) ) . ( $imp == '!' ? ' !important' : '' ) . ';';
				}
			}
		}
		return $output;
	}
}

if ( ! function_exists( 'trx_addons_css_add_context' ) ) {
	/**
	 * Add context class to each selector in the CSS
	 *
	 * @param string $css   CSS string
	 * @param string $args  array with options:
	 * 						'context' - a string to add to the beginning of each selector
	 * 						            (if any 'context_self' is not present at the start of the selector)
	 * 						'context_self' - an array with parts of the selector replaced with 'context' (if present in the selector)
	 * 
	 * @return string 	 CSS string with context class added to each selector
	 */
	function trx_addons_css_add_context( $css, $args ) {
		$args = array_merge(
					array(
						'context' => '',
						'context_self' => array()
					),
					is_array( $args ) ? $args : array( 'context' => $args )
				);
		if ( ! empty( $args['context'] ) || ! empty( $args['context_self'] ) ) {
			$rez          = '';
			$in_comment   = false;
			$in_rule      = false;
			$in_media     = false;
			$allow        = true;
			$context      = $args['context'];
			$context_self = is_array( $args['context_self'] ) ? $args['context_self'] : array( $args['context_self'] );
			//$css          = str_replace( array( '{{', '}}' ), array( '[[', ']]' ), $css );
			for ( $i = 0; $i < strlen( $css ); $i++ ) {
				$ch = $css[ $i ];
				if ( $in_comment ) {
					$rez .= $ch;
					if ( '/' == $ch && '*' == $css[ $i - 1 ] ) {
						$in_comment = false;
						$allow      = ! $in_rule;
					}
				} elseif ( $in_rule ) {
					$rez .= $ch;
					if ( '}' == $ch ) {
						$in_rule = false;
						$allow   = ! $in_comment;
					}
				} elseif ( $in_media && '}' == $ch ) {
					$rez .= $ch;
					$in_media = false;
					$allow   = ! $in_comment;
				} else {
					if ( '/' == $ch && '*' == $css[ $i + 1 ] ) {
						$rez       .= $ch;
						$in_comment = true;
					} elseif ( '{' == $ch ) {
						$rez    .= $ch;
						$in_rule = true;
					} elseif ( ',' == $ch ) {
						$rez  .= $ch;
						$allow = true;
					} elseif ( strpos( " \t\r\n", $ch ) === false ) {
						if ( $allow ) {
							if ( '@' == $ch ) {
								$in_media = true;
								$pos_bracket = strpos( $css, '{', $i + 1 );
								$rez .= substr( $css, $i, $pos_bracket - $i + 1 );
								$i = $pos_bracket;
								continue;
							}
							$pos_comma   = strpos( $css, ',', $i + 1 );
							$pos_bracket = strpos( $css, '{', $i + 1 );
							$pos = false === $pos_comma
									? $pos_bracket
									: ( false === $pos_bracket
											? $pos_comma
											: min( $pos_comma, $pos_bracket )
										);
							$selector    = $pos > 0 ? substr( $css, $i, $pos - $i ) : '';
							$found = false;
							foreach($context_self as $self_class) {
								if ( !empty($self_class) && strpos( $selector, $self_class ) !== false && !in_array(substr($selector, strlen($self_class), 1), array('-', '_')) ) {
									$rez .= str_replace( $self_class, trim( $context ), $selector );
									$i   += strlen( $selector ) - 1;
									$found = true;
									break;
								}
							}
							if ( ! $found ) {
								$rez .= $context . trim( $ch );
							}
							$allow = false;
						} else {
							$rez .= $ch;
						}
					} else {
						$rez .= $ch;
					}
				}
			}
			//$rez = str_replace( array( '[[', ']]' ), array( '{{', '}}' ), $rez );
			$css = trx_addons_minify_css( $rez );
		}
		return $css;
	}
}

if ( ! function_exists( 'trx_addons_remove_comments' ) ) {
	/**
	 * Remove comments from string with code
	 * 
	 * @param string $str String with code
	 * 
	 * @return string String without comments
	 */
	function trx_addons_remove_comments( $str ) {
		$inString = '';
		$inRegular = 0;
		$inRegularChar = '';
		$inSingleComment = false;
		$inMultiComment = false;
		$prevChar  = '';
		$rez = '';
		for ( $i = 0; $i < strlen( $str ); $i++ ) {
			$ch = substr( $str, $i, 1 );
			if ( $inRegular ) {
				if ( $inRegularChar == ''
					&& $inRegular == 1
					&& ( in_array( $ch, array( '"', "'", '#' ) )
						|| ( $ch == '/'
							&& ! in_array( substr( $str, $i + 1, 1 ), array( '/', '*' ) ) )
							&& ( $prevChar != '/' || substr( $rez, -2, 1 ) == '\\' )
						)
				) {
					$inRegularChar = $ch;
				} else if ( ! empty( $inRegularChar ) ) {
					if ( $ch == $inRegularChar && ( $prevChar != '\\' || substr( $rez, -2, 1 ) == '\\' ) ) {
						$inRegularChar = '';
					}
				} else if ( $ch == '(' ) {
					$inRegular++;
				} else if ( $ch == ')' ) {
					$inRegular--;
				}
			}
			if ( ! empty( $inString ) ) {
				if ( $ch == $inString && ( $prevChar != "\\" || substr( $str, $i - 2, 1 ) == "\\" ) ) {
					$inString = '';
				}
				$rez .= $ch;
			} else if ( $inMultiComment ) {
				if ( $prevChar == '*' && $ch == '/' ) {
					$inMultiComment = false;
				}
			} else if ( ! $inSingleComment ) {
				if ( $ch == '('
					&& ( substr( $rez, -6 ) == 'RegExp'
						|| substr( $rez, -6 ) == '.match'
						|| substr( $rez, -8 ) == '.replace'
						)
				) {
					$inRegular = 1;
					$rez .= $ch;
				} else if ( in_array( $ch, array( '"', "'", "`" ) ) ) {
					if ( $inRegular == 0 ) {
						$inString = $ch;
					}
					$rez .= $ch;
				} else if ( $prevChar == '/' && $ch == '*' && ( $inRegular == 0 || substr( $rez, -2, 1 ) != '\\' ) ) {
					$inMultiComment = true;
					$rez = substr( $rez, 0, -1 );
				} else if ( $prevChar == '/' && $ch == '/' && ( $inRegular == 0 || substr( $rez, -2, 1 ) != '\\' ) ) {
					$inSingleComment = true;
					$rez = substr( $rez, 0, -1 );
				} else {
					$rez .= $ch;
				}
			} else if ( $ch == "\n" ) {
				$inSingleComment = false;
			}
			$prevChar = $ch;
		}
		return $rez;
	}
}

if ( ! function_exists('trx_addons_minify_css' ) ) {
	add_filter( 'trx_addons_filter_prepare_css', 'trx_addons_minify_css', 10, 2 );
	/**
	 * Minify CSS string
	 * 
	 * @hooked trx_addons_filter_prepare_css
	 * 
	 * @param string $css      String with CSS
	 * @param boolean $minify  Minify or not
	 * 
	 * @return string          Minified string
	 */
	function trx_addons_minify_css( $css, $minify = true ) {
		if ( $minify ) {
			//$css = str_ireplace('@CHARSET "UTF-8";', "", $css);
			// Remove comments
			$css = preg_replace("!/\*.*?\*/!s", "", $css);
			// Remove line breaks and spaces around its
			$css = preg_replace("/\s*[\r\n]+\s*/", "", $css);
			// Remove spaces around >, ~, :, {, }
			// Attention! Don't remove spaces around '+', because it broke rules with calc( a + b )
			$css = preg_replace("/\s*([>~:{}])\s*/", "$1", $css);
			// Remove ; before }
			$css = preg_replace("/;}/", "}", $css);
			// Remove spaces after ,
			$css = str_replace(', ', ',', $css);
			// Remove comments
			$css = preg_replace("/(\/\*[\w\'\s\r\n\*\+\,\"\-\.]*\*\/)/", "", $css);
		}
        return $css;
	}
}

if ( ! function_exists( 'trx_addons_minify_js' ) ) {
	add_filter( 'trx_addons_filter_prepare_js', 'trx_addons_minify_js', 10, 2 );
	/**
	 * Minify JS string
	 * 
	 * @hooked trx_addons_filter_prepare_js
	 * 
	 * @param string $js      String with JS
	 * @param boolean $minify  Minify or not
	 * 
	 * @return string          Minified string
	 */
	function trx_addons_minify_js( $js, $minify = true ) {
		if ( $minify ) {
			// Remove comments
			$js = trx_addons_remove_comments( $js );
			// Convert line feed to \n inside ``
			$js = preg_replace_callback(
					'/`([^`]*)`/',
					function($matches) {
						return '`' . str_replace( array("\n", "\r"), array('\n', ''), $matches[1] ) . '`';
					},
					$js
				);
			// Remove spaces before/after {}()
			$js = preg_replace('/\s+/', ' ', $js);
			$js = preg_replace('/([;}{\)\(])\s+/', '$1 ', $js);
			$js = preg_replace('/\s+([;}{\)\(])/', ' $1', $js);
			$js = preg_replace('/(else)\s+/', '$1 ', $js);
			//$js = preg_replace('/([}])\s+(else)/', '$1else', $js);
			//$js = preg_replace('/([}])\s+(var)/', '$1;var', $js);
			//$js = preg_replace('/([{};])\s+(\$)/', '$1\$', $js);
		}
		return $js;
	}
}

if ( ! function_exists( 'trx_addons_prepare_css_value' ) ) {
	/**
	 * Prepare value to use in the CSS - add 'px' to the numeric values if unit not specified
	 *
	 * @param string $val Value to prepare
	 * 
	 * @return string     Prepared value
	 */
	function trx_addons_prepare_css_value( $val ) {
		if ( '' !== $val ) {
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

if ( ! function_exists( 'trx_addons_get_columns_wrap_class' ) ) {
	/**
	 * Return classes for the columns wrapper. It can be used in the columns shortcode or in the widgets.
	 * 
	 * @param boolean $fluid   Fluid or not
	 * 
	 * @return string          Classes for the columns wrapper
	 */
	function trx_addons_get_columns_wrap_class( $fluid = false ) {
		return trx_addons_get_option( 'columns_wrap_class' ) != '' 
					? trx_addons_get_option( 'columns_wrap_class' )
						. ( $fluid && trx_addons_get_option( 'columns_wrap_class_fluid' ) != ''
							? ' ' . trx_addons_get_option( 'columns_wrap_class_fluid' )
							: ''
							) 
					: 'trx_addons_columns_wrap' . ( $fluid ? ' columns_fluid' : '' );
	}
}

if ( ! function_exists( 'trx_addons_get_column_class' ) ) {
	/**
	 * Return classes for the column. It can be used in the columns shortcode or in the widgets.
	 * 
	 * @param int $num         Number of the column
	 * @param int $all         Number of the columns in the row
	 * @param int $all_tablet  Number of the columns in the row on the tablet
	 * @param int $all_mobile  Number of the columns in the row on the mobile
	 * 
	 * @return string          Classes for the column
	 */
	function trx_addons_get_column_class( $num, $all, $all_tablet = '', $all_mobile = '' ) {
		$column_class_tpl = trx_addons_get_column_class_template();
		$column_class = str_replace( array( '$1', '$2' ), array( $num, $all ), $column_class_tpl );
		if ( ! empty( $all_tablet ) ) {
			$column_class .= ' ' . str_replace( array( '$1', '$2' ), array( $num, $all_tablet ), $column_class_tpl ) . '-tablet';
		}
		if ( ! empty( $all_mobile ) ) {
			$column_class .= ' ' . str_replace( array( '$1', '$2' ), array( $num, $all_mobile ), $column_class_tpl ) . '-mobile';
		}
		return $column_class;
	}
}

if ( ! function_exists( 'trx_addons_get_column_class_template' ) ) {
	/**
	 * Return template for the column class. It can be used in the columns shortcode or in the widgets.
	 * 
	 * @return string          Template for the column class
	 */
	function trx_addons_get_column_class_template() {
		return trx_addons_get_option('column_class') != ''
								? trx_addons_get_option('column_class')
								: 'trx_addons_column-$1_$2';
	}
}

if ( ! function_exists( 'trx_addons_parse_icons_classes' ) ) {
	/**
	 * Parse CSS-file with icons classes. Return array with classes from file
	 * 
	 * @param string $css Path to the CSS-file with icons classes
	 * 
	 * @return array Array with classes from file
	 */
	function trx_addons_parse_icons_classes( $css ) {
		$rez = array();
		if ( ! file_exists( $css ) ) {
			return $rez;
		}
		$file = trx_addons_fga( $css );
		if ( ! is_array( $file ) || count( $file ) == 0) {
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
			if ( $name != '' ) {
				$rez[] = $name;
			}
		}
		return $rez;
	}
}

if ( ! function_exists( 'trx_addons_add_property_to_link' ) ) {
	add_filter( 'style_loader_tag', 'trx_addons_add_property_to_link', 10, 3 );
	/**
	 * Add property="stylesheet" to the tag <link> in the shortcodes VC
	 * 
	 * @hooked style_loader_tag
	 * 
	 * @param string $link    Link to the CSS-file
	 * @param string $handle  Handler of the CSS-file
	 * @param string $href    URL of the CSS-file
	 * 
	 * @return string         Modified link
	 */
	function trx_addons_add_property_to_link( $link, $handle = '', $href = '' ) {
		return str_replace( '<link ', '<link property="stylesheet" ', $link );
	}
}

if ( ! function_exists( 'trx_addons_get_responsive_classes' ) ) {
	/**
	 * Return a list of the responsive classes for the shortcode's attribute
	 * 
	 * @param string $prefix   Prefix for the class name
	 * @param string $atts     Shortcode's or item's attributes
	 * @param string $param    Attribute name
	 * @param string $default  Optional. Default value. Default: ''
	 * 
	 * @return string          List of the responsive classes
	 */
	function trx_addons_get_responsive_classes( $prefix, $atts, $param, $default = '' ) {
		$list = array();
		if ( ! empty( $atts[ $param ] ) ) {
			$list[] = $prefix . $atts[ $param ];
		} else if ( ! empty( $default ) ) {
			$list[] = $prefix . $default;
		}
		$list = apply_filters( 'trx_addons_filter_responsive_classes', $list, $prefix, $atts, $param );
		return count( $list ) > 0 ? implode( ' ', $list ) : '';
	}
}


/* HTML
-------------------------------------------------------------------------------- */

if ( ! function_exists( 'trx_addons_generate_id' ) ) {
	/**
	 * Generate ID for the element
	 * 
	 * @param string $prefix Prefix for the ID
	 * 
	 * @return string ID for the element
	 */
	function trx_addons_generate_id( $prefix = '' ) {
		return $prefix . str_replace( '.', '', mt_rand() );
	}
}

if ( ! function_exists( 'trx_addons_get_tag' ) ) {
	/**
	 * Return outer tag content from html by start and end (optional)
	 * 
	 * @param string $text      Text to search
	 * @param string $tag_start Start tag
	 * @param string $tag_end   End tag
	 * 
	 * @return string           Tag content
	 */
	function trx_addons_get_tag( $text, $tag_start, $tag_end = '' ) {
		$val = '';
		if ( ( $pos_start = strpos( $text, $tag_start ) ) !== false ) {
			$pos_end = $tag_end ? strpos( $text, $tag_end, $pos_start ) : false;
			if ( $pos_end === false ) {
				$tag_end = substr( $tag_start, 0, 1 ) == '<' ? '>' : ']';
				$pos_end = strpos( $text, $tag_end, $pos_start );
			}
			$val = substr( $text, $pos_start, $pos_end + strlen( $tag_end ) - $pos_start );
		}
		return $val;
	}
}

if ( ! function_exists( 'trx_addons_get_tag_attrib' ) ) {
	/**
	 * Return a value of the specified attribute from the tag
	 * 
	 * @param string $text Text to search
	 * @param string $tag  Tag to search
	 * @param string $attr Attribute to search
	 * 
	 * @return string      Attribute value
	 */
	function trx_addons_get_tag_attrib( $text, $tag, $attr ) {
		$val = '';
		if ( ( $pos_start = strpos( $text, substr( $tag, 0, strlen( $tag ) - 1 ) ) ) !== false ) {
			$pos_end = strpos( $text, substr( $tag, -1, 1 ), $pos_start );
			$pos_attr = strpos( $text, ' ' . $attr . '=', $pos_start );
			if ( $pos_attr !== false && $pos_attr < $pos_end ) {
				$pos_attr += strlen( $attr ) + 3;
				$pos_quote = strpos( $text, substr( $text, $pos_attr - 1, 1 ), $pos_attr );
				$val = substr( $text, $pos_attr, $pos_quote - $pos_attr );
			}
		}
		return $val;
	}
}

if ( ! function_exists( 'trx_addons_set_tag_attrib' ) ) {
	/**
	 * Set a value of the specified attribute in the tag
	 * 
	 * @param string $text Text to search
	 * @param string $tag  Tag to search
	 * @param string $attr Attribute to search
	 * @param string $val  Attribute value
	 * 
	 * @return string      Modified text
	 */
	function trx_addons_set_tag_attrib( $text, $tag, $attr, $val ) {
		if ( ( $pos_start = strpos( $text, substr( $tag, 0, strlen( $tag ) - 1 ) ) ) !== false ) {
			$pos_end = strpos( $text, substr( $tag, -1, 1 ), $pos_start );
			$pos_attr = strpos( $text, $attr . '=', $pos_start );
			if ( $pos_attr !== false && $pos_attr < $pos_end ) {
				$pos_attr += strlen( $attr ) + 2;
				$pos_quote = strpos( $text, substr( $text, $pos_attr - 1, 1 ), $pos_attr );
				$text = substr( $text, 0, $pos_attr ) . trim( $val ) . substr( $text, $pos_quote );
			} else {
				$text = substr( $text, 0, $pos_end ) . ' ' . esc_attr( $attr ) . '="' . esc_attr( $val ) . '"' . substr( $text, $pos_end );
			}
		}
		return $text;
	}
}

if ( ! function_exists( 'trx_addons_parse_codes' ) ) {
	/**
	 * Replace {{ and }} to the < and > in the string (this is allow use html tags in the some shortcode parameters)
	 * 
	 * @param string $text      Text to search
	 * @param string $tag_start Start tag to replace with '<'. Default: '{{'
	 * @param string $tag_end   End tag to replace with '>'. Default: '}}'
	 * 
	 * @return string           Modified text
	 */
	function trx_addons_parse_codes( $text, $tag_start = '{{', $tag_end = '}}' ) {
		return str_replace( array( $tag_start, $tag_end ), array( '<', '>' ), $text );
	}
}

if ( ! function_exists( 'trx_addons_seo_snippets' ) ) {
	/**
	 * Add SEO markup snippets
	 *
	 * @param string $prop      itemprop
	 * @param string $type      itemtype
	 * @param string $scope     itemscope
	 */
	function trx_addons_seo_snippets( $prop, $type = '', $scope = false ) {
		static $seo_snippets = 0;
		if ($seo_snippets === 0) {
			$seo_snippets = apply_filters( 'trx_addons_filter_seo_snippets', false );
		}
		if ( $seo_snippets ) {
			if ( ! empty( $prop ) ) echo ' itemprop="'.esc_attr($prop).'"';
			if ( ! empty( $type ) ) echo ' itemtype="//schema.org/'.esc_attr(ucfirst($type)).'"';
			if ( ! empty( $scope ) || ! empty( $type ) ) echo ' itemscope="itemscope"';
		}
	}
}

if ( ! function_exists( 'trx_addons_seo_image_params' ) ) {
	/**
	 * Add SEO markup snippet for the image to the parameters 
	 *
	 * @param array $params  SEO snippets parameters
	 * 
	 * @return array 	   Modified parameters
	 */
	function trx_addons_seo_image_params( $params ) {
		static $seo_snippets = 0;
		if ( $seo_snippets === 0 ) {
			$seo_snippets = apply_filters( 'trx_addons_filter_seo_snippets', false );
		}
		if ( $seo_snippets ) {
			$params['itemprop'] = 'image';
		}
		return apply_filters( 'trx_addons_filter_seo_image_params', $params );
	}
}

if ( ! function_exists( 'trx_addons_links_to_span' ) ) {
	/**
	 * Replace all links in the string to the span with data-href attribute to hide links
	 *
	 * @param string $str String to replace
	 *
	 * @return string Modified string
	 */
	function trx_addons_links_to_span( $str ) {
		return str_replace( array( '<a ', '</a>', 'href=' ), array( '<span ', '</span>', 'data-href=' ), $str );
	}
}

if ( ! function_exists( 'trx_addons_kses_allowed_html' ) ) {
	add_filter( 'wp_kses_allowed_html', 'trx_addons_kses_allowed_html', 11, 2);
	/**
	 * Add some tags to the list of allowed tags for the WP KSES
	 *
	 * @param array $tags     Allowed tags
	 * @param string $context Context of the tags
	 * 
	 * @return array          Allowed tags list
	 */
	function trx_addons_kses_allowed_html( $tags, $context ) {
		if ( 'trx_addons_kses_content' == $context ) {
			$tags = array_merge( is_array( $tags ) ? $tags : array(), array(
				'h1'     => array( 'id' => array(), 'class' => array(), 'style' => array(), 'title' => array(), 'align' => array() ),
				'h2'     => array( 'id' => array(), 'class' => array(), 'style' => array(), 'title' => array(), 'align' => array() ),
				'h3'     => array( 'id' => array(), 'class' => array(), 'style' => array(), 'title' => array(), 'align' => array() ),
				'h4'     => array( 'id' => array(), 'class' => array(), 'style' => array(), 'title' => array(), 'align' => array() ),
				'h5'     => array( 'id' => array(), 'class' => array(), 'style' => array(), 'title' => array(), 'align' => array() ),
				'h6'     => array( 'id' => array(), 'class' => array(), 'style' => array(), 'title' => array(), 'align' => array() ),
				'p'      => array( 'id' => array(), 'class' => array(), 'style' => array(), 'title' => array(), 'align' => array() ),
				'span'   => array( 'id' => array(), 'class' => array(), 'style' => array(), 'title' => array() ),
				'div'    => array( 'id' => array(), 'class' => array(), 'style' => array(), 'title' => array(), 'align' => array() ),
				'a'      => array( 'id' => array(), 'class' => array(), 'style' => array(), 'title' => array(), 'href' => array(), 'target' => array() ),
				'b'      => array( 'id' => array(), 'class' => array(), 'style' => array(), 'title' => array() ),
				'strong' => array( 'id' => array(), 'class' => array(), 'title' => array() ),
				'i'      => array( 'id' => array(), 'class' => array(), 'style' => array(), 'title' => array() ),
				'em'     => array( 'id' => array(), 'class' => array(), 'title' => array() ),
				'img'    => array( 'id' => array(), 'class' => array(), 'style' => array(), 'title' => array(), 'src' => array(), 'width' => array(), 'height' => array(), 'alt' => array() ),
				'br'     => array( 'clear' => array() ),
				'u'      => array( 'id' => array(), 'class' => array(), 'style' => array(), 'title' => array() ),
				's'      => array( 'id' => array(), 'class' => array(), 'style' => array(), 'title' => array() ),
				'ins'    => array( 'id' => array(), 'class' => array(), 'style' => array(), 'title' => array() ),
				'del'    => array( 'id' => array(), 'class' => array(), 'style' => array(), 'title' => array(), 'aria-hidden' => array() ),
				'pre'    => array( 'id' => array(), 'class' => array(), 'style' => array(), 'title' => array() ),
				'tt'     => array( 'id' => array(), 'class' => array(), 'style' => array(), 'title' => array() ),
				'bdi'    => array( 'id' => array(), 'class' => array(), 'style' => array(), 'title' => array(), 'dir' => array() ),
				'small'  => array( 'id' => array(), 'class' => array(), 'style' => array(), 'title' => array() ),
				'big'    => array( 'id' => array(), 'class' => array(), 'style' => array(), 'title' => array() ),
				'abbr'   => array( 'id' => array(), 'class' => array(), 'style' => array(), 'title' => array() ),
			) );
		}
		return $tags;
	}
}

if ( ! function_exists( 'trx_addons_get_page_background_selector' ) ) {
	/**
	 * Return a theme-specific selector for the page background
	 * 
	 * @return string Page background selector
	 */
	function trx_addons_get_page_background_selector() {
		return apply_filters(
					'trx_addons_filter_page_background_selector',
					'body:not(.body_style_boxed) .page_content_wrap,body.body_style_boxed .page_wrap'
				);
	}
}


/* URL utilities
-------------------------------------------------------------------------------- */

if ( ! function_exists( 'trx_addons_get_hash_link' ) ) {
	/**
	 * Return internal page link: if is customize mode - return a full url, else - only a hash part of the url
	 * 
	 * @param string $hash Link hash
	 * 
	 * @return string Link
	 */
	function trx_addons_get_hash_link( $hash ) {
		if ( strpos( $hash, 'http' ) !== 0 ) {
			if ( $hash[0] != '#' ) {
				$hash = '#' . $hash;
			}
			if ( is_customize_preview() ) {
				$hash = trx_addons_get_protocol() . ':' . '//' . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] . $hash;
			}
		}
		return $hash;
	}
}

if ( ! function_exists( 'trx_addons_add_hash_to_url' ) ) {
	/**
	 * Add or replace a hash part to the url
	 * 
	 * @param string $url  URL
	 * @param string $hash Hash
	 * 
	 * @return string URL with hash
	 */
	function trx_addons_add_hash_to_url( $url, $hash ) {
		if ( ( $pos = strpos( $url, '#' ) ) !== false ) {
			$url = substr( $url, 0, $pos );
		}
		return $url . '#' . $hash;
	}
}

if ( ! function_exists( 'trx_addons_get_protocol' ) ) {
	/**
	 * Return current site protocol
	 * 
	 * @return string  http or https
	 */
	function trx_addons_get_protocol() {
		return is_ssl() ? 'https' : 'http';
	}
}

if ( ! function_exists( 'trx_addons_add_protocol' ) ) {
	/**
	 * Add a site protocol to the URL
	 * 
	 * @param string $url URL
	 * 
	 * @return string URL with protocol
	 */
	function trx_addons_add_protocol( $url ) {
		return substr( $url, 0, 2 ) == '//' ? trx_addons_get_protocol() . ':' . $url : $url;
	}
}

if ( ! function_exists( 'trx_addons_remove_protocol' ) ) {
	/**
	 * Remove a site protocol from the URL
	 * 
	 * @param string $url URL
	 * @param bool $complete Remove only protocol (false) or protocol and '//' after it (true)
	 * 
	 * @return string URL without protocol
	 */
	function trx_addons_remove_protocol( $url, $complete = false ) {
		return preg_replace( '/(http[s]?:)?' . ( $complete ? '\\/\\/' : '' ) . '/', '', $url );
	}
}

if ( ! function_exists( 'trx_addons_is_url' ) ) {
	/**
	 * Check if string is URL
	 * 
	 * @param string $url URL
	 * 
	 * @return bool True if string is URL
	 */
	function trx_addons_is_url( $url ) {
		return strpos($url, '//') === 0 || strpos($url, '://') !== false;
	}
}

if ( ! function_exists( 'trx_addons_is_external_url' ) ) {
	/**
	 * Check if string is external URL
	 * 
	 * @param string $url URL
	 * 
	 * @return bool True if string is external URL
	 */
	function trx_addons_is_external_url( $url ) {
		return trx_addons_is_url( $url ) && strpos( $url, trx_addons_remove_protocol( get_home_url(), true ) ) === false;
	}
}

if ( ! function_exists( 'trx_addons_get_current_url' ) ) {
	/**
	 * Return current page URL
	 * 
	 * @return string Current page URL
	 */
	function trx_addons_get_current_url() {
		return add_query_arg( array() );
	}
}

if ( ! function_exists( 'trx_addons_add_to_url' ) ) {
	/**
	 * Add parameters to the URL
	 * 
	 * @param string $url URL
	 * @param array $prm Parameters
	 * 
	 * @return string URL with parameters
	 */
	function trx_addons_add_to_url( $url, $prm ) {
		if ( is_array( $prm ) && count( $prm ) > 0 ) {
			$parts = explode( '?', $url );
			$params = array();
			if ( ! empty( $parts[1] ) ) {
				parse_str( $parts[1], $params );
			}
			$params = trx_addons_array_merge( $params, $prm );
			$url = $parts[0];
			$separator = '?';
			foreach( $params as $k => $v ) {
				$url .= $separator . urlencode( $k ) . '=' . urlencode( $v );
				$separator = '&';
			}
		}
		return $url;
	}
}

if ( ! function_exists( 'trx_addons_check_url' ) ) {
	/**
	 * Check if current page URL contains specified string
	 * 
	 * @param string|array $val  String or array of strings to check
	 * 
	 * @return bool True if current page URL contains specified string
	 */
	function trx_addons_check_url( $val = '' ) {
		if ( ! is_array( $val ) ) {
			$val = array( $val );
		}
		$rez = false;
		foreach	( $val as $s ) {
			$rez = strpos( $_SERVER['REQUEST_URI'], $s ) !== false;
			if ( $rez ) break;
		}
		return $rez;
	}
}

if ( ! function_exists( 'trx_addons_add_referals_to_url' ) ) {
	/**
	 * Add referals to the URL
	 *
	 * @param string $url URL
	 * @param array $referals Referals
	 *
	 * @return string URL with referals
	 */
	function trx_addons_add_referals_to_url( $url, $referals ) {
		if ( is_array( $referals ) && count( $referals ) > 0 ) {
			$prm = array();
			foreach ( $referals as $ref ) {
				if ( ! empty( $ref['url'] ) && ! empty( $ref['param'] ) && strpos( $url, $ref['url'] ) !== false ) {
					parse_str( $ref['param'], $refs );
					if ( is_array( $refs ) && count( $refs ) > 0 ) {
						$prm = array_merge( $prm, $refs );
					}
				}
			}
			$url = trx_addons_add_to_url( $url, $prm );
		}
		return $url;
	}
}

if ( ! function_exists( 'trx_addons_get_url_by_mask' ) ) {
	/**
	 * Replace URL with a new URL if it contains a substring from the mask
	 *
	 * @param string $url  URL
	 * @param array $masks Masks
	 *
	 * @return string  New URL
	 */
	function trx_addons_get_url_by_mask( $url, $masks ) {
		if ( is_array( $masks ) ) {
			foreach( $masks as $mask => $new_url ) {
				if ( strpos( $url, $mask ) !== false ) {
					$url = $new_url;
					break;
				}
			}
		}
		return $url;
	}
}

if ( ! function_exists( 'trx_addons_set_html_content_type' ) ) {
	/**
	 * Set HTML content type for the mail. Used in the trx_addons_send_mail().
	 * Call add_filter( 'wp_mail_content_type', 'trx_addons_set_html_content_type' ) before send mail
	 * and  remove_filter( 'wp_mail_content_type', 'trx_addons_set_html_content_type' ) after send mail.
	 * 
	 * @hooked 'wp_mail_content_type'
	 *
	 * @return string
	 */
	function trx_addons_set_html_content_type() {
		return 'text/html';
	}
}

if ( ! function_exists( 'trx_addons_html_decode' ) ) {
	/**
	 * Decode html-entities in the shortcode parameters
	 *
	 * @param array $prm Shortcode parameters
	 *
	 * @return array Decoded parameters
	 */
	function trx_addons_html_decode( $prm ) {
		if ( is_array( $prm ) && count( $prm ) > 0 ) {
			foreach ( $prm as $k => $v ) {
				if ( is_string( $v ) ) {
					$prm[ $k ] = wp_specialchars_decode( $v, ENT_QUOTES );
				}
			}
		}
		return $prm;
	}
}




/* GET, POST and SESSION utilities
-------------------------------------------------------------------------------- */

if ( ! function_exists( 'trx_addons_stripslashes' ) ) {
	/**
	 * Remove slashes from the string or array of strings if magic_quotes_gpc is on
	 * 
	 * @param string|array $val String or array of strings to process
	 * 
	 * @return string|array Result
	 */
	function trx_addons_stripslashes( $val ) {
		static $magic = 0;
		if ( $magic === 0 ) {
			$magic = version_compare( phpversion(), '5.4', '>=' )
					|| ( function_exists( 'get_magic_quotes_gpc' ) && get_magic_quotes_gpc() == 1 ) 
					|| ( function_exists( 'get_magic_quotes_runtime' ) && get_magic_quotes_runtime() == 1 ) 
					|| strtolower( ini_get( 'magic_quotes_sybase' ) ) == 'on';
		}
		if ( is_array( $val ) ) {
			foreach ( $val as $k => $v ) {
				$val[$k] = trx_addons_stripslashes( $v );
			}
		} else {
			$val = $magic ? stripslashes( trim( $val ) ) : trim( $val );
		}
		return $val;
	}
}

if ( ! function_exists( 'trx_addons_get_value_gp' ) ) {
	/**
	 * Return GET or POST value
	 *
	 * @param string $name Name of the GET or POST parameter
	 * @param string $defa Default value
	 *
	 * @return string Value
	 */
	function trx_addons_get_value_gp( $name, $defa = '' ) {
		if ( isset( $_GET[ $name ] ) )			$rez = $_GET[ $name ];
		else if ( isset( $_POST[ $name ] ) )	$rez = $_POST[ $name ];
		else									$rez = $defa;
		return trx_addons_stripslashes( $rez );
	}
}

if ( ! function_exists( 'trx_addons_get_value_gpc' ) ) {
	/**
	 * Return GET, POST or COOKIE value
	 *
	 * @param string $name Name of the GET, POST or COOKIE parameter
	 * @param string $defa Default value
	 *
	 * @return string Value
	 */
	function trx_addons_get_value_gpc( $name, $defa = '' ) {
		if ( isset( $_GET[ $name ] ) )		 	$rez = $_GET[ $name ];
		else if ( isset( $_POST[ $name ] ) )	$rez = $_POST[ $name ];
		else if ( isset( $_COOKIE[ $name ] ) ) 	$rez = $_COOKIE[ $name ];
		else									$rez = $defa;
		return trx_addons_stripslashes( $rez );
	}
}

if ( ! function_exists( 'trx_addons_get_value_gps' ) ) {
	/**
	 * Return GET, POST or SESSION value
	 *
	 * @param string $name Name of the GET, POST or SESSION parameter
	 * @param string $defa Default value
	 *
	 * @return string Value
	 */
	function trx_addons_get_value_gps( $name, $defa = '' ) {
		if ( isset( $_GET[ $name ] ) )			$rez = $_GET[ $name ];
		else if ( isset( $_POST[ $name ] ) )	$rez = $_POST[ $name ];
		else if ( isset( $_SESSION[ $name ] ) )	$rez = $_SESSION[ $name ];
		else									$rez = $defa;
		return trx_addons_stripslashes( $rez );
	}
}

if ( ! function_exists( 'trx_addons_set_session_value' ) ) {
	/**
	 * Set value to the session
	 *
	 * @param string $name Name of the session parameter
	 * @param string $value Value to set
	 */
	function trx_addons_set_session_value( $name, $value ) {
		if ( ! session_id() ) {
			try {
				session_start();
			} catch (Exception $e) {}
		}
		$_SESSION[ $name ] = $value;
	}
}

if ( ! function_exists( 'trx_addons_del_session_value' ) ) {
	/**
	 * Delete value from the session
	 *
	 * @param string $name Name of the session parameter
	 */
	function trx_addons_del_session_value( $name ) {
		if ( ! session_id() ) {
			try {
				session_start();
			} catch (Exception $e) {}
		}
		unset( $_SESSION[ $name ] );
	}
}

if ( ! function_exists( 'trx_addons_get_cookie' ) ) {
	/**
	 * Return COOKIE value
	 *
	 * @param string $name Name of the COOKIE parameter
	 * @param string $defa Default value
	 *
	 * @return string Value
	 */
	function trx_addons_get_cookie( $name, $defa = '' ) {
		return trx_addons_stripslashes( isset( $_COOKIE[ $name ] ) ? $_COOKIE[ $name ] : $defa );
	}
}

if ( ! function_exists( 'trx_addons_set_cookie' ) ) {
	/**
	 * Set value to the COOKIE. Wrapper for setcookie using WP constants.
	 *
	 * @param string $name    Name of the COOKIE parameter
	 * @param string $value   Value to set
	 * @param int $expire     Expiration time in seconds
	 * @param bool $secure	  Whether the cookie should only be transmitted over a secure HTTPS connection from the client.
	 * @param bool $httponly  Whether to make the cookie accessible only through the HTTP protocol.
	 */
	function trx_addons_set_cookie( $name, $value, $expire = 0, $secure = false, $httponly = false  ) {
		if ( ! headers_sent() ) {
			if ( defined( 'PHP_VERSION_ID' ) && PHP_VERSION_ID >= 70300 ) {
				$rez = setcookie(
							$name,
							$value,
							apply_filters( 'trx_addons_filter_cookie_options', array(
								'expires'  => $expire,
								'path'     => defined( 'COOKIEPATH' ) && ! empty( COOKIEPATH ) ? COOKIEPATH : '/',
								'domain'   => defined( 'COOKIE_DOMAIN' ) && ! empty( COOKIE_DOMAIN ) ? COOKIE_DOMAIN : '',	//trx_addons_get_domain_from_url( get_home_url() ),
								'secure'   => $secure,
								'httponly' => $httponly,
								'samesite' => 'None'	// Strict | Lax | None
							) )
						);
			} else {
				$rez = setcookie(
							$name,
							$value,
							$expire,
							defined( 'COOKIEPATH' ) && ! empty( COOKIEPATH ) ? COOKIEPATH : '/',
							defined( 'COOKIE_DOMAIN' ) && ! empty( COOKIE_DOMAIN ) ? COOKIE_DOMAIN : '',	//trx_addons_get_domain_from_url( get_home_url() ),
							$secure,
							$httponly
						);
			}
		} elseif ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			headers_sent( $file, $line );
			trigger_error( sprintf( __( '%1$s cookie cannot be set - headers already sent by %2$s on line %3$s', 'trx_addons' ), $name, $file, $line ), E_USER_NOTICE ); // @codingStandardsIgnoreLine
		}
	}
}


/* IP address utilities
-------------------------------------------------------------------------------- */

if ( ! function_exists( 'trx_addons_get_ip_info' ) ) {
	/**
	 * Return a cached connection info by IP address.
	 * 
	 * @param string $ip  Optional. An IP address to get info. If omitted - a current IP is used.
	 * 
	 * @return array  An array with a connection information by IP.
	 */
	function trx_addons_get_ip_info( $ip = ''  ) {
		$rez = false;
		if ( empty( $ip ) ) {
			if ( ! empty( $_SERVER['REMOTE_ADDR'] ) && $_SERVER['REMOTE_ADDR'] != '127.0.0.1' && substr( $_SERVER['REMOTE_ADDR'], 0, 8 ) != '192.168.' ) {
				$ip = $_SERVER['REMOTE_ADDR'];
			}
			if ( empty( $ip ) ) {
				$ip = get_transient( 'trx_addons_server_ip' );
			}
		}
		$ip_data = get_transient( 'trx_addons_ip_data' );
		if ( empty( $ip ) || empty( $ip_data[ $ip ] ) || ! is_array( $ip_data[ $ip ] ) ) {
			$ip_info = trx_addons_query_ip_info( $ip );
			if ( is_array( $ip_info ) ) {
				$rez = $ip_info;
				if ( empty( $ip ) && ! empty( $ip_info[ 'ip' ] ) ) {
					$ip = $ip_info[ 'ip' ];
					set_transient( 'trx_addons_server_ip', $ip, 7 * 24 * 60 * 60 );       // Store to the cache for 1 week
				}
				if ( ! empty( $ip ) ) {
					if ( ! is_array( $ip_data ) ) {
						$ip_data = array();
					}
					$ip_data[ $ip ] = $ip_info;
					set_transient( 'trx_addons_ip_data', $ip_data, 7 * 24 * 60 * 60 );       // Store to the cache for 1 week
				}
			}
		} else {
			$rez = $ip_data[ $ip ];
		}
		return $rez;
	}
}

if ( ! function_exists( 'trx_addons_query_ip_info' ) ) {
	/**
	 * Request a connection info by IP address from a free service.
	 * 
	 * @param string $ip  Optional. An IP address to get info. If omitted - a current IP is used.
	 * 
	 * @return array  An array with a connection information by IP.
	 */
	function trx_addons_query_ip_info( $ip = ''  ) {
		$rez = apply_filters( 'trx_addons_filter_query_ip_info', false, $ip );
		if ( ! $rez ) {
			$ip_data = trx_addons_fgc( '//ipwho.is/' . ( ! empty( $ip ) ? $ip . '/' : '' ) );
			if ( ! empty( $ip_data ) && substr( $ip_data, 0, 1 ) == '{' ) {
				$ip_info = json_decode( $ip_data, true );
				if ( is_array( $ip_info ) && ! empty( $ip_info['success'] ) ) {
					$rez = $ip_info;
				}
			}
		}
		return $rez;
	}
}
