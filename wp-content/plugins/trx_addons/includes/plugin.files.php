<?php
/**
 * File system manipulations
 *
 * @package ThemeREX Addons
 * @since v1.0
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

//define( 'TRX_ADDONS_REMOTE_USER_AGENT', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:84.0) Gecko/20100101 Firefox/84.0' );
define( 'TRX_ADDONS_REMOTE_USER_AGENT', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:84.0) Gecko/20100101 Firefox/84.0 AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2623.112 Safari/537.36' );


/* Optimized enqueue scripts and styles
------------------------------------------------------------------------------------- */

if ( ! function_exists( 'trx_addons_enqueue_styles' ) ) {
	/**
	 * Enqueue a list of styles
	 *
	 * @param array $list - styles to enqueue
	 */
	function trx_addons_enqueue_styles( $list, $sc ) {
		if ( is_array( $list ) ) {
			foreach( $list as $handle => $data ) {
				$lib_url = trx_addons_get_file_url( $data['src'] );
				if ( $lib_url ) {
					wp_enqueue_style(
						$handle,
						$lib_url,
						! empty( $data['deps'] ) ? (array)$data['deps'] : array(),
						! empty( $data['ver'] ) ? $data['ver'] : null,
						! empty( $data['media'] ) ? trx_addons_media_for_load_css_responsive( str_replace( '_', '-', $sc ), $data['media'] ) : 'all'
					);
				}
			}
		}
	}
}

if ( ! function_exists( 'trx_addons_enqueue_scripts' ) ) {
	/**
	 * Enqueue a list of scripts
	 *
	 * @param array $list - scripts to enqueue
	 */
	function trx_addons_enqueue_scripts( $list, $sc ) {
		if ( is_array( $list ) ) {
			foreach( $list as $handle => $data ) {
				$lib_url = trx_addons_get_file_url( $data['src'] );
				if ( $lib_url ) {
					wp_enqueue_script(
						$handle,
						$lib_url,
						! empty( $data['deps'] ) ? (array)$data['deps'] : array(),
						! empty( $data['ver'] ) ? $data['ver'] : null,
						isset( $data['footer'] ) ? $data['footer'] : true
					);
				}
			}
		}
	}
}

if ( ! function_exists( 'trx_addons_enqueue_optimized' ) ) {
	/**
	 * Enqueue styles and scripts only if a shortcode (widget) is used on the page or 'Optimize CSS and JS loading' option is off
	 * 
	 * @param string $sc - shortcode (widget) slug
	 * @param bool $force - force enqueue styles and scripts
	 * @param array $args - arguments with styles and scripts to enqueue
	 */
	function trx_addons_enqueue_optimized( $sc, $force, $args ) {
		static $loaded = array(), $loaded2 = array();
		$debug       = trx_addons_is_on( trx_addons_get_option( 'debug_mode' ) );
		$optimize    = ! trx_addons_is_off( trx_addons_get_option( 'optimize_css_and_js_loading' ) );
		$preview_elm = trx_addons_is_preview( 'elementor' );
		$preview_gb  = trx_addons_is_preview( 'gutenberg' );
		$theme_full  = current_theme_supports( 'styles-and-scripts-full-merged' );
		$need        = empty( $loaded[ $sc ] ) && ( ! $preview_elm || $debug ) && ! $preview_gb && $optimize && (
						$force === true
							|| ( $preview_elm && $debug )
							|| ! empty( $args['need'] )
							|| ( ! empty( $args['check'] ) && trx_addons_sc_check_in_content( array(
									'sc' => $sc,
									'entries' => $args['check']
								) ) )
						);
		// Enqueue external libraries (if need)
		if ( empty( $loaded2[ $sc ] ) && ! empty( $args['lib'] ) && ( ! $optimize || $need || $preview_elm ) ) {
			$loaded2[ $sc ] = true;
			if ( ! empty( $args['lib']['css'] ) ) {
				trx_addons_enqueue_styles( $args['lib']['css'], $sc );
			}
			if ( ! empty( $args['lib']['js'] ) ) {
				trx_addons_enqueue_scripts( $args['lib']['js'], $sc );
			}
			if ( ! empty( $args['lib']['callback'] ) ) {
				$args['lib']['callback']();
			}
		}
		// Enqueue styles and scripts
		if ( empty( $loaded[ $sc ] ) && ! $preview_gb && ( ( ! $optimize && $debug ) || ( $optimize && $need ) ) ) {
			$loaded[ $sc ] = true;
			if ( ! empty( $args['css'] ) ) {
				trx_addons_enqueue_styles( $args['css'], $sc );
			}
			if ( ! empty( $args['js'] ) ) {
				trx_addons_enqueue_scripts( $args['js'], $sc );
			}
			if ( ! empty( $args['callback'] ) ) {
				$args['callback']();
			}
			do_action( 'trx_addons_action_load_scripts_front', $force, str_replace( '-', '_', $sc ) );
		}
		if ( empty( $loaded[ $sc ] ) && $preview_elm && $optimize && ! $debug && ! $theme_full ) {
			do_action( 'trx_addons_action_load_scripts_front', false, str_replace( '-', '_', $sc ), 2 );
		}
	}
}


if ( ! function_exists( 'trx_addons_enqueue_optimized_responsive' ) ) {
	/**
	 * Enqueue responsive styles only if shortcode (widget) used on the page or 'Optimize CSS and JS loading' option is off
	 * 
	 * @param string $sc - shortcode (widget) slug
	 * @param bool $force - force enqueue styles and scripts
	 * @param array $args - arguments with styles and scripts to enqueue
	 */
	function trx_addons_enqueue_optimized_responsive( $sc, $force, $args ) {
		static $loaded = array();
		if ( empty( $loaded[ $sc ] ) && (
			current_action() == 'wp_enqueue_scripts' && trx_addons_need_frontend_scripts( $sc )
			||
			current_action() != 'wp_enqueue_scripts' && $force === true
			)
		) {
			$loaded[ $sc ] = true;
			if ( ! isset( $args['need'] ) || $args['need'] ) {
				if ( ! empty( $args['css'] ) ) {
					trx_addons_enqueue_styles( $args['css'], $sc );
				}
				if ( ! empty( $args['js'] ) ) {
					trx_addons_enqueue_scripts( $args['js'], $sc );
				}
				if ( ! empty( $args['callback'] ) ) {
					$args['callback']();
				}
			}
		}
	}
}

if ( ! function_exists( 'trx_addons_check_in_html_output' ) ) {
	/**
	 * Check if any regular expression from the list exists in the HTML output
	 *
	 * @param string $sc		Shortcode (widget) slug
	 * @param string $content   Page content to check
	 * @param array $args		Options for check:
	 * 							- check - array of regular expressions to check
	 * 							- need - additional check if need to load styles and scripts
	 * 
	 * @return bool  		 True if any regular expression from the list exists in the HTML output
	 */
	function trx_addons_check_in_html_output( $sc, $content, $args ) {
		$rez = false;
		if ( ( ! isset( $args['need'] ) || $args['need'] )
			&& ! trx_addons_need_frontend_scripts( $sc )
			&& ! trx_addons_is_off( trx_addons_get_option( 'optimize_css_and_js_loading' ) )
		) {
			$args['check'] = apply_filters( 'trx_addons_filter_check_in_html', $args['check'], $sc );
			if ( ! empty( $args['check'] ) && is_array( $args['check'] ) ) {
				foreach ( $args['check'] as $item ) {
					if ( preg_match( "#{$item}#", $content, $matches ) ) {
						$rez = true;
						break;
					}
				}
			}
		}
		return $rez;
	}
}

if ( ! function_exists( 'trx_addons_filter_head_output' ) ) {
	/**
	 * Remove plugin-specific styles from the page head output if optimize CSS loading is 'full'
	 *
	 * @param string $sc		Shortcode (widget) slug
	 * @param string $content   Page head content
	 * @param array $args		Options for removing styles and scripts:
	 * 							- check - regular expressions to check in the page body
	 * 							- allow - allow remove styles and scripts
	 * 
	 * @return string  	        Modified page head content
	 */
	function trx_addons_filter_head_output( $sc, $content, $args ) {
		if ( ( ! isset( $args['allow'] ) || $args['allow'] )
			&& trx_addons_get_option( 'optimize_css_and_js_loading' ) == 'full'
			&& ! trx_addons_is_preview()
			&& ! trx_addons_need_frontend_scripts( $sc )
			&& apply_filters( 'trx_addons_filter_remove_3rd_party_styles', true, $sc )
		) {
			$args['check'] = apply_filters( 'trx_addons_filter_check_in_page_head', $args['check'], $sc );
			if ( ! empty( $args['check'] ) && is_array( $args['check'] ) ) {
				foreach ( $args['check'] as $item ) {
					$content = preg_replace( $item, '', $content );
				}
			}
		}
		return $content;
	}
}

if ( ! function_exists( 'trx_addons_filter_body_output' ) ) {
	/**
	 * Remove plugin-specific styles from the page body output if optimize CSS loading is 'full'
	 * 
	 * @param string $sc		Shortcode (widget) slug
	 * @param string $content   Page body content
	 * @param array $args		Options for removing styles and scripts:
	 * 							- check - regular expressions to check in the page body
	 * 							- allow - allow remove styles and scripts
	 * 
	 * @return string  	        Modified page body content
	 */
	function trx_addons_filter_body_output( $sc, $content, $args ) {
		if ( ( ! isset( $args['allow'] ) || $args['allow'] )
			&& trx_addons_get_option( 'optimize_css_and_js_loading' ) == 'full'
			&& ! trx_addons_is_preview()
			&& ! trx_addons_need_frontend_scripts( $sc )
			&& apply_filters( 'trx_addons_filter_remove_3rd_party_styles', true, $sc )
		) {
			$args['check'] = apply_filters( 'trx_addons_filter_check_in_page_head', $args['check'], $sc );
			if ( ! empty( $args['check'] ) && is_array( $args['check'] ) ) {
				foreach ( $args['check'] as $item ) {
					$content = preg_replace( $item, '', $content );
				}
			}
		}
		return $content;
	}
}


/* Enqueue common scripts and styles
------------------------------------------------------------------------------------- */

if ( ! function_exists( 'trx_addons_enqueue_slider' ) ) {
	/**
	 * Enqueue slider scripts and styles (Swiper or Elastistack)
	 * 
	 * @param string $engine - slider engine: 'swiper' or 'elastistack'
	 */
	function trx_addons_enqueue_slider( $engine = 'swiper' ) {
		static $loaded = array(
			'swiper' => false,
			'elastistack' => false
		);

		// Load once and only in the frontend
		if ( ! empty( $loaded[ $engine ] ) || strpos( trx_addons_get_current_url(), '/wp-admin/post.php' ) !== false ) {
			return;
		}

		$loaded[ $engine ] = true;

		if ( $engine == 'swiper' ) {
			$v8 = trx_addons_exists_elementor() && trx_addons_elm_is_experiment_active( 'e_swiper_latest' ) ? 'v8/' : '';
			wp_enqueue_style(  'swiper', trx_addons_get_file_url('js/swiper/' . $v8 . 'swiper.min.css'), array(), null );
			wp_enqueue_script( 'swiper', trx_addons_get_file_url('js/swiper/' . $v8 . 'swiper.min.js'), array(), null, true );
		} else if ( $engine == 'elastistack' ) {
			wp_enqueue_script( 'modernizr', trx_addons_get_file_url('js/elastistack/modernizr.custom.js'), array(), null, true );
			wp_enqueue_script( 'draggabilly', trx_addons_get_file_url('js/elastistack/draggabilly.pkgd.min.js'), array(), null, true );
			wp_enqueue_script( 'elastistack', trx_addons_get_file_url('js/elastistack/elastistack.js'), array(), null, true );
		}
	}
}

if ( ! function_exists( 'trx_addons_enqueue_slider_pagebuilder_preview_scripts' ) ) {
	add_action( "trx_addons_action_pagebuilder_preview_scripts", 'trx_addons_enqueue_slider_pagebuilder_preview_scripts', 10, 1 );
	/**
	 * Enqueue all slider scripts and styles (Swiper and Elastistack) in the pagebuilder preview mode
	 * 
	 * @hooked trx_addons_action_pagebuilder_preview_scripts
	 * 
	 * @param string $editor - editor name. Not used
	 */
	function trx_addons_enqueue_slider_pagebuilder_preview_scripts( $editor = '' ) {
		trx_addons_enqueue_slider('swiper');
		trx_addons_enqueue_slider('elastistack');
	}
}

if ( ! function_exists( 'trx_addons_enqueue_slider_sc_output' ) ) {
	add_filter( 'trx_addons_sc_output', 'trx_addons_enqueue_slider_sc_output', 10, 4 );
	/**
	 * Enqueue slider scripts and styles (Swiper or Elastistack) if present in the shortcode output
	 * 
	 * @hooked trx_addons_sc_output
	 * 
	 * @param string $output   shortcode output
	 * @param string $sc       shortcode name
	 * @param array $atts      shortcode attributes
	 * @param string $content  shortcode content
	 * 
	 * @return string          shortcode output
	 */
	function trx_addons_enqueue_slider_sc_output( $output, $sc, $atts, $content ) {
		if ( strpos( $output, 'slider_swiper' ) !== false ) {
			trx_addons_enqueue_slider('swiper');
		} else if ( strpos( $output, 'slider_elastistack' ) !== false ) {
			trx_addons_enqueue_slider('elastistack');
		}
		return $output;
	}
}

if ( ! function_exists( 'trx_addons_enqueue_popup' ) ) {
	/**
	 * Enqueue popup scripts and styles (PrettyPhoto or Magnific Popup)
	 *
	 * @param string $engine - popup engine: 'pretty' or 'magnific'
	 */
	function trx_addons_enqueue_popup( $engine = 'magnific' ) {
		// Load only in the frontend
		if ( strpos( trx_addons_get_current_url(), '/wp-admin/post.php' ) !== false ) {
			return;
		}
		if ( $engine == 'pretty' ) {
			wp_enqueue_style(  'prettyphoto',	 trx_addons_get_file_url('js/prettyphoto/css/prettyPhoto.css'), array(), null );
			wp_enqueue_script( 'prettyphoto',	 trx_addons_get_file_url('js/prettyphoto/jquery.prettyPhoto.min.js'), array('jquery'), 'no-compose', true );
		} else if ( $engine == 'magnific' ) {
			wp_enqueue_style(  'magnific-popup', trx_addons_get_file_url('js/magnific/magnific-popup.min.css'), array(), null );
			wp_enqueue_script( 'magnific-popup', trx_addons_get_file_url('js/magnific/jquery.magnific-popup.min.js'), array('jquery'), null, true );
		}
	}
}

if ( ! function_exists( 'trx_addons_enqueue_wp_color_picker' ) ) {
	/**
	 * Enqueue WP Color Picker
	 */
	function trx_addons_enqueue_wp_color_picker() {
	    wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'iris', admin_url( 'js/iris.min.js' ), array( 'jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch' ), null, true );
		wp_enqueue_script( 'wp-color-picker', admin_url( 'js/color-picker.min.js' ), array( 'iris' ), null, true );
		wp_localize_script( 'wp-color-picker', 'wpColorPickerL10n', array(
			'clear' => __( 'Clear', 'trx_addons' ),
			'defaultString' => __( 'Default', 'trx_addons' ),
			'pick' => __( 'Select Color', 'trx_addons' ),
			'current' => __( 'Current Color', 'trx_addons' ),
		) );
	
	}
}

if ( ! function_exists( 'trx_addons_enqueue_googlemap' ) ) {
	/**
	 * Enqueue Google map scripts
	 */
	function trx_addons_enqueue_googlemap() {
		$api_key = trx_addons_get_option('api_google');
		if ( trx_addons_is_on( trx_addons_get_option( 'api_google_load' ) ) && ! empty( $api_key ) ) {	
			$places_key = function_exists( 'trx_addons_google_places_api_key' ) ? trx_addons_google_places_api_key() : '';
			$params = array(
				// Add 'callback' to the URL to prevent a warning from Googlemap API
				'callback' => 'trx_addons_googlemap_loaded'
			);
			if ( ! empty( $api_key ) ) {
				$params['key'] = $api_key;
			}
			if ( ! empty( $places_key ) ) {
				$params['libraries'] = 'places';
			}
			$url = 'https://maps.googleapis.com/maps/api/js';
			if ( count( $params ) > 0 ) {
				$url = trx_addons_add_to_url( $url, $params );
			}
			wp_enqueue_script( 'google-maps', $url, array(), null, true );
		}
	}
}

if ( ! function_exists( 'trx_addons_enqueue_osmap' ) ) {
	/**
	 * Enqueue OpenStreet map scripts and styles
	 */
	function trx_addons_enqueue_osmap() {
		if ( trx_addons_is_on( trx_addons_get_option('api_openstreet_load') ) ) {
			// LeaFlet OSM lib 
			wp_enqueue_style(  'openstreet-maps', 'https://unpkg.com/leaflet@1.4.0/dist/leaflet.css', array(), null );
			wp_enqueue_script( 'openstreet-maps', 'https://unpkg.com/leaflet@1.4.0/dist/leaflet.js', array(), null, true );
			// Geocoder Control
			wp_enqueue_style(  'openstreet-maps-geocoder', 'https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css', array(), null );
			wp_enqueue_script( 'openstreet-maps-geocoder', 'https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js', array(), null, true );
			// Clustering
			wp_enqueue_style(  'openstreet-maps-cluster', 'https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css', array(), null );
			wp_enqueue_style(  'openstreet-maps-cluster-default', 'https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css', array(), null );
			wp_enqueue_script( 'openstreet-maps-cluster', 'https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js', array(), null, true );
			// Mapbox GL
			if ( trx_addons_get_option('api_openstreet_tiler') == 'vector' ) {
				wp_enqueue_style(  'openstreet-maps-mapbox-gl', 'https://cdn.maptiler.com/mapbox-gl-js/v0.53.0/mapbox-gl.css', array(), null );
				wp_enqueue_script( 'openstreet-maps-mapbox-gl', 'https://cdn.maptiler.com/mapbox-gl-js/v0.53.0/mapbox-gl.js', array(), null, true );
				wp_enqueue_script( 'openstreet-maps-mapbox-gl-leaflet', 'https://cdn.maptiler.com/mapbox-gl-leaflet/latest/leaflet-mapbox-gl.js', array(), null, true );
			}
		}
	}
}

if ( ! function_exists( 'trx_addons_enqueue_select2' ) ) {
	/**
	 * Enqueue select2 scripts and styles
	 */
	function trx_addons_enqueue_select2() {
		wp_enqueue_style(  'select2', trx_addons_get_file_url('js/select2/select2.min.css'), array(), null );
		wp_enqueue_script( 'select2', trx_addons_get_file_url('js/select2/select2.min.js'), array('jquery'), null, true );
	}
}

if ( ! function_exists( 'trx_addons_enqueue_masonry' ) ) {
	/**
	 * Enqueue masonry scripts
	 */
	function trx_addons_enqueue_masonry() {
		static $once = true;
		// Load only in the frontend
		if ( strpos( trx_addons_get_current_url(), '/wp-admin/post.php' ) !== false ) {
			return;
		}
		if ( $once ) {
			$once = false;
			wp_enqueue_script( 'imagesloaded' );
			wp_enqueue_script( 'masonry' );
			trx_addons_lazy_load_off();
		}
	}
}

if ( ! function_exists( 'trx_addons_enqueue_tweenmax' ) ) {
	/**
	 * Enqueue TweenMax script
	 */
	function trx_addons_enqueue_tweenmax( $args = array() ) {
		$args = array_merge( array(
									'ScrollTo' => false,
									'ScrollTrigger' => false
								),
								$args
							);
		// Load only in the frontend
		if ( strpos( trx_addons_get_current_url(), '/wp-admin/post.php' ) !== false ) {
			return;
		}
		wp_enqueue_script( 'tweenmax', trx_addons_get_file_url('js/tweenmax/tweenmax.min.js'), array(), null, true );
		if ( ! empty( $args['ScrollTo'] ) ) {
			wp_enqueue_script( 'tweenmax-plugin-scrollto', trx_addons_get_file_url('js/tweenmax/ScrollTo.min.js'), array(), null, true );
		}
		if ( ! empty( $args['ScrollTrigger'] ) ) {
			wp_enqueue_script( 'tweenmax-plugin-scrolltrigger', trx_addons_get_file_url('js/tweenmax/ScrollTrigger.min.js'), array(), null, true );
		}
	}
}

if ( ! function_exists( 'trx_addons_enqueue_scroll_magic' ) ) {
	/**
	 * Enqueue ScrollMagic script
	 */
	function trx_addons_enqueue_scroll_magic() {
		// Load only in the frontend
		if ( strpos( trx_addons_get_current_url(), '/wp-admin/post.php' ) !== false ) {
			return;
		}
		wp_enqueue_script( 'scroll-magic', trx_addons_get_file_url( 'js/tweenmax/ScrollMagic.js' ), array(), null, true );
		wp_enqueue_script( 'animation-gsap', trx_addons_get_file_url( 'js/tweenmax/animation.gsap.js' ), array(), null, true );
	}
}

if ( ! function_exists( 'trx_addons_enqueue_parallax' ) ) {
	/**
	 * Enqueue parallax scripts
	 */
	function trx_addons_enqueue_parallax() {
		trx_addons_enqueue_tweenmax();	// Must be first!
		trx_addons_enqueue_scroll_magic();
	}
}


/* Merge scripts and styles
------------------------------------------------------------------------------------- */

if ( ! function_exists( 'trx_addons_merge_js' ) ) {
	/**
	 * Merge separate scripts from the list to the single file to increase page upload speed
	 * 
	 * @param string $to	target file to save merged scripts
	 * @param array $list	list of the files with JS to merge
	 */
	function trx_addons_merge_js( $to, $list ) {
		$s = '';
		foreach ( $list as $f ) {
			$s .= trx_addons_fgc( trx_addons_get_file_dir( $f ) );
		}
		if ( $s != '') {
			$file_dir = trx_addons_get_file_dir( $to );
			if ( empty( $file_dir ) && strpos( $to, '-full.js' ) !== false ) {
				$file_dir = trx_addons_get_file_dir( str_replace( '-full.js', '.js', $to ) );
				if ( ! empty( $file_dir ) ) {
					$file_dir = str_replace( '.js', '-full.js', $file_dir );
				}
			}
			trx_addons_fpc( $file_dir,
				'/* ' 
				. strip_tags( __("ATTENTION! This file was generated automatically! Don't change it!!!", 'trx_addons') ) 
				. "\n----------------------------------------------------------------------- */\n"
				. apply_filters( 'trx_addons_filter_js_output', trx_addons_minify_js( $s ), $to )
			);
		}
	}
}

if ( ! function_exists( 'trx_addons_merge_css' ) ) {
	/**
	 * Merge separate styles from the list to the single file to increase page upload speed
	 * 
	 * @param string $to				target file to save merged styles
	 * @param array $list				list of the files with CSS to merge
	 * @param bool $need_responsive		need to add responsive styles
	 */
	function trx_addons_merge_css( $to, $list, $need_responsive = false ) {
		global $TRX_ADDONS_STORAGE;
		$responsive = $TRX_ADDONS_STORAGE['responsive'];
		if ( $need_responsive ) {
			$responsive = apply_filters( 'trx_addons_filter_responsive_sizes', $responsive );
		}
		$sizes  = array();
		$output = '';
		foreach ( $list as $f ) {
			$fdir = trx_addons_get_file_dir( $f );
			if ( '' != $fdir ) {
				$css = trx_addons_fgc( $fdir );
				if ( $need_responsive ) {
					$pos = 0;
					while( false !== $pos ) {
						$pos = strpos( $css, '@media' );
						if ( false !== $pos ) {
							$pos += 7;
							$pos_lbrace = strpos( $css, '{', $pos );
							$cnt = 0;
							$in_comment = false;
							for ( $pos_rbrace = $pos_lbrace + 1; $pos_rbrace < strlen( $css ); $pos_rbrace++ ) {
								if ( $in_comment ) {
									if ( substr( $css, $pos_rbrace, 2 ) == '*/' ) {
										$pos_rbrace++;
										$in_comment = false;
									}
								} else if ( substr( $css, $pos_rbrace, 2 ) == '/*' ) {
									$pos_rbrace++;
									$in_comment = true;
								} else if ( substr( $css, $pos_rbrace, 1 ) == '{' ) {
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
							$sizes[ $media ] .= "\n\n" . apply_filters( 'trx_addons_filter_merge_css', substr( $css, $pos_lbrace + 1, $pos_rbrace - $pos_lbrace - 1 ) );
							$css = substr( $css, $pos_rbrace + 1);
						}
					}
				} else {
					$output .= "\n\n" . apply_filters( 'trx_addons_filter_merge_css', $css );
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
							. strip_tags( sprintf( __( '/* SASS Suffix: --%s */', 'trx_addons' ), $k ) )
							. "\n"
							. "@media {$media} {\n"
								. $sizes[ $media ]
							. "\n}\n";
					unset( $sizes[ $media ] );
				}
			}
			if ( count( $sizes ) > 0 ) {
				$output .= "\n\n"
						. strip_tags( __( '/* Unknown Suffixes: */', 'trx_addons' ) );
				foreach ( $sizes as $k => $v ) {
					$output .= "\n\n"
							. "@media {$k} {\n"
								. $v
							. "\n}\n";
				}
			}
		}
		if ( $output != '') {
			$file_dir = trx_addons_get_file_dir( $to );
			if ( empty( $file_dir ) && strpos( $to, '-full.css' ) !== false ) {
				$file_dir = trx_addons_get_file_dir( str_replace( '-full.css', '.css', $to ) );
				if ( ! empty( $file_dir ) ) {
					$file_dir = str_replace( '.css', '-full.css', $file_dir );
				}
			}
			trx_addons_fpc( $file_dir,
				'/* ' 
				. strip_tags( __("ATTENTION! This file was generated automatically! Don't change it!!!", 'trx_addons') ) 
				. "\n----------------------------------------------------------------------- */\n"
				. apply_filters( 'trx_addons_filter_css_output', trx_addons_minify_css( $output ), $to )
			);
		}
	}
}

if ( ! function_exists( 'trx_addons_merge_sass' ) ) {
	/**
	 * Merge all separate SASS files into one single file
	 * 
	 * @param string $to			target file to save result
	 * @param array $list			list of files to merge
	 * @param bool $need_responsive	need to add responsive styles
	 * @param string $root			root folder to get files
	 */
	function trx_addons_merge_sass( $to, $list, $need_responsive = false, $root = '../' ) {
		global $TRX_ADDONS_STORAGE;
		$responsive = $TRX_ADDONS_STORAGE['responsive'];
		if ( $need_responsive ) {
			$responsive = apply_filters('trx_addons_filter_responsive_sizes', $responsive);
		}
		$sass = array(
			'import' => '',
			'sizes'  => array()
			);
		$save = false;
		foreach ( $list as $f ) {
			$add = false;
			if ( ( $fdir = trx_addons_get_file_dir( $f ) ) != '' ) {
				if ( $need_responsive ) {
					$css = trx_addons_fgc( $fdir );
					if ( strpos( $css, '@required') !== false ) {
						$add = true;
					}
					foreach ( $responsive as $k => $v ) {
						if ( preg_match( "/([\d\w\-_]+\-\-{$k})\(/", $css, $matches ) ) {
							$sass['sizes'][$k] = ( ! empty( $sass['sizes'][$k] ) ? $sass['sizes'][$k] : '' ) . "\t@include {$matches[1]}();\n";
							$add = true;
						}
					}
				} else {
					$add = true;
				}
			}
			if ( $add ) {
				$sass['import'] .= apply_filters( 'trx_addons_filter_sass_import', "@import \"{$root}{$f}\";\n", $f );
				$save = true;
			}
		}
		if ( $save ) {
			$output = '/* ' 
					. strip_tags( __("ATTENTION! This file was generated automatically! Don't change it!!!", 'trx_addons') ) 
					. "\n----------------------------------------------------------------------- */\n"
					. $sass['import'];
			if ( $need_responsive ) {
				foreach ( $responsive as $k => $v ) {
					if ( ! empty( $sass['sizes'][$k] ) ) {
						$output .= "\n\n"
								. strip_tags( sprintf( __("/* SASS Suffix: --%s */", 'trx_addons'), $k) )
								. "\n"
								. "@media " . ( ! empty( $v['min'] ) ? "(min-width: {$v['min']}px)" : '' )
											. ( ! empty( $v['min'] ) && !empty($v['max']) ? ' and ' : '' )
											. ( ! empty( $v['max'] ) ? "(max-width: {$v['max']}px)" : '' )
											. " {\n"
												. $sass['sizes'][$k]
											. "}\n";
					}
				}
			}
			trx_addons_fpc(
				trx_addons_get_file_dir( $to ),
				apply_filters( 'trx_addons_filter_sass_output', $output, $to )
			);
		}
	}
}


/* Process loading scripts and styles
------------------------------------------------------------------------------------- */
if ( ! function_exists( 'trx_addons_process_styles' ) ) {
	add_filter('style_loader_tag', 'trx_addons_process_styles', 10, 4);
	/**
	 * Process styles when they are loaded
	 * 
	 * @hooked style_loader_tag
	 * 
	 * @param string $tag		HTML link tag
	 * @param string $handle	Style handle
	 * @param string $href		Style URL
	 * @param string $media		Style media
	 * 
	 * @return string			Modified HTML link tag
	 */
	function trx_addons_process_styles( $tag, $handle = '', $href = '', $media = '' ) {
		return apply_filters( 'trx_addons_filter_process_styles', $tag, $handle, $href, $media );
	}
}

if ( ! function_exists( 'trx_addons_process_scripts' ) ) {
	add_filter('script_loader_tag', 'trx_addons_process_scripts', 10, 3);
	/**
	 * Process scripts when they are loaded
	 * 
	 * @hooked script_loader_tag
	 * 
	 * @param string $tag		HTML link tag
	 * @param string $handle	Script handle
	 * @param string $href		Script URL
	 * 
	 * @return string			Modified HTML link tag
	 */
	function trx_addons_process_scripts( $tag, $handle = '', $href = '' ) {
		return apply_filters( 'trx_addons_filter_process_scripts', $tag, $handle, $href );
	}
}


/* Check if file/folder present in the child theme and return path (url) to it. 
   Else - path (url) to file in the main theme dir
------------------------------------------------------------------------------------- */
if ( ! function_exists( 'trx_addons_get_file_dir' ) ) {	
	/**
	 * Return file (or folder) path (or url) in the child theme (if present) or in the main theme
	 * 
	 * @param string $file		File name (or path relative to the theme folder)
	 * @param bool $return_url	Return URL (true) or path (false)
	 * 
	 * @return string			File path (or url)
	 */
	function trx_addons_get_file_dir( $file, $return_url = false ) {
		if ($file[0]=='/') $file = substr($file, 1);
		$theme_dir = get_template_directory().'/'.TRX_ADDONS_PLUGIN_BASE.'/';
		$theme_url = get_template_directory_uri().'/'.TRX_ADDONS_PLUGIN_BASE.'/';
		$child_dir = get_stylesheet_directory().'/'.TRX_ADDONS_PLUGIN_BASE.'/';
		$child_url = get_stylesheet_directory_uri().'/'.TRX_ADDONS_PLUGIN_BASE.'/';
		$dir = '';
		if (($filtered_dir = apply_filters('trx_addons_filter_get_theme_file_dir', '', TRX_ADDONS_PLUGIN_BASE.'/'.($file), $return_url)) != '')
			$dir = $filtered_dir;
		else if ($theme_dir != $child_dir && file_exists(($child_dir).($file)))
			$dir = ($return_url ? $child_url : $child_dir) . trx_addons_check_min_file($file, $child_dir);
		else if (file_exists(($theme_dir).($file)))
			$dir = ($return_url ? $theme_url : $theme_dir) . trx_addons_check_min_file($file, $theme_dir);
		else if (file_exists(TRX_ADDONS_PLUGIN_DIR . ($file)))
			$dir = ($return_url ? TRX_ADDONS_PLUGIN_URL : TRX_ADDONS_PLUGIN_DIR) . trx_addons_check_min_file($file, TRX_ADDONS_PLUGIN_DIR);
		return apply_filters( 'trx_addons_filter_get_file_dir', $dir, $file, $return_url );
	}
}

if ( ! function_exists( 'trx_addons_get_file_url' ) ) {	
	/**
	 * Return file url in the child theme (if present) or in the main theme
	 * 
	 * @param string $file		File name (or path relative to the theme folder)
	 * 
	 * @return string			File url
	 */
	function trx_addons_get_file_url( $file ) {
		return trx_addons_get_file_dir( $file, true );
	}
}

if ( ! function_exists( 'trx_addons_get_file_ext' ) ) {	
	/**
	 * Return file extension from full name/path
	 * 
	 * @param string $file		File name (or path relative to the theme folder)
	 * 
	 * @return string			File extension
	 */
	function trx_addons_get_file_ext( $file ) {
		if ( strpos( $file, '?' ) !== false ) {
			$file = substr( $file, 0, strpos( $file, '?' ) );
		}
		$ext = pathinfo( $file, PATHINFO_EXTENSION );
		return empty( $ext ) ? '' : $ext;
	}
}

if ( ! function_exists( 'trx_addons_get_file_name' ) ) {	
	/**
	 * Return file name from full name/path
	 *
	 * @param string $file			File name (or path relative to the theme folder)
	 * @param bool $without_ext		Remove extension from file name
	 * 
	 * @return string				File name
	 */
	function trx_addons_get_file_name( $file, $without_ext = true ) {
		if ( strpos( $file, '?' ) !== false ) {
			$file = substr( $file, 0, strpos( $file, '?' ) );
		}
		$parts = pathinfo($file);
		return !empty($parts['filename']) && $without_ext ? $parts['filename'] : $parts['basename'];
	}
}

if ( ! function_exists( 'trx_addons_get_domain_from_url' ) ) {
	/**
	 * Return domain part from URL
	 * 
	 * @param string $url		URL
	 * 
	 * @return string			Domain
	 */
	function trx_addons_get_domain_from_url( $url ) {
		if ( ( $pos = strpos( $url, '//' ) ) !== false ) {
			$url = substr( $url, $pos + 2 );
		}
		if ( ( $pos = strpos( $url, '/' ) ) !== false ) {
			$url = substr( $url, 0, $pos );
		}
		return $url;
	}
}

if ( ! function_exists( 'trx_addons_get_folder_dir' ) ) {	
	/**
	 * Return folder path (or url) in the child theme (if present) or in the main theme
	 * 
	 * @param string $folder	Folder name (or path relative to the theme folder)
	 * @param bool $return_url	Return URL (true) or path (false)
	 * 
	 * @return string			Folder path (or url)
	 */
	function trx_addons_get_folder_dir( $folder, $return_url = false ) {
		if ( $folder[0] == '/' ) {
			$folder = substr( $folder, 1 );
		}
		$theme_dir = get_template_directory().'/'.TRX_ADDONS_PLUGIN_BASE.'/';
		$theme_url = get_template_directory_uri().'/'.TRX_ADDONS_PLUGIN_BASE.'/';
		$child_dir = get_stylesheet_directory().'/'.TRX_ADDONS_PLUGIN_BASE.'/';
		$child_url = get_stylesheet_directory_uri().'/'.TRX_ADDONS_PLUGIN_BASE.'/';
		$dir = '';
		if ( ( $filtered_dir = apply_filters( 'trx_addons_filter_get_theme_folder_dir', '', TRX_ADDONS_PLUGIN_BASE . '/' . $folder, $return_url ) ) != '' ) {
			$dir = $filtered_dir;
		} else if ( $theme_dir != $child_dir && is_dir( $child_dir . $folder ) ) {
			$dir = ( $return_url ? $child_url : $child_dir ) . $folder;
		} else if ( is_dir( $theme_dir . $folder ) ) {
			$dir = ( $return_url ? $theme_url : $theme_dir ) . $folder;
		} else if ( is_dir( TRX_ADDONS_PLUGIN_DIR . $folder ) ) {
			$dir = ( $return_url ? TRX_ADDONS_PLUGIN_URL : TRX_ADDONS_PLUGIN_DIR ) . $folder;
		}
		return apply_filters( 'trx_addons_filter_get_folder_dir', $dir, $folder, $return_url );
	}
}

if ( ! function_exists( 'trx_addons_get_folder_url' ) ) {	
	/**
	 * Return folder url in the child theme (if present) or in the main theme
	 * 
	 * @param string $folder	Folder name (or path relative to the theme folder)
	 * 
	 * @return string			Folder url
	 */
	function trx_addons_get_folder_url( $folder ) {
		return trx_addons_get_folder_dir( $folder, true );
	}
}

if ( ! function_exists( 'trx_addons_check_min_file' ) ) {	
	/**
	 * Return file name with '.min' before extension ( if .min version exists and filetime .min > filetime original) instead original
	 *
	 * @param string $file		File name (or path relative to the theme folder)
	 * @param string $dir		Directory to check file
	 * 
	 * @return string			File name with '.min' before extension
	 */
	function trx_addons_check_min_file( $file, $dir = '' ) {
		if ( empty( $dir ) ) {
			$dir = dirname( $file );
		}
		if ( substr( $file, -3 ) == '.js' ) {
			if ( substr( $file, -7 ) != '.min.js' && trx_addons_is_off( trx_addons_get_option( 'debug_mode', false, false ) ) ) {
				$dir = trailingslashit( $dir );
				$file_min = substr( $file, 0, strlen( $file ) - 3 ) . '.min.js';
				if ( file_exists( $dir . $file_min ) && filemtime( $dir . $file ) <= filemtime( $dir . $file_min ) ) {
					$file = $file_min;
				}
			}
		} else if ( substr( $file, -4 ) == '.css' ) {
			if ( substr( $file, -8 ) != '.min.css'  && trx_addons_is_off( trx_addons_get_option( 'debug_mode', false, false ) ) ) {
				$dir = trailingslashit( $dir );
				$file_min = substr( $file, 0, strlen( $file ) - 4 ) . '.min.css';
				if ( file_exists( $dir . $file_min ) && filemtime( $dir . $file ) <= filemtime( $dir . $file_min ) ) {
					$file = $file_min;
				}
			}
		}
		return $file;
	}
}



/* Init WP Filesystem before the plugins and theme init
   Attention! WordPress is not recommended to use this class for regular file operations.
   Below is a message from WordPress "Theme Check" plugin:
   - WP_Filesystem sould only be used for theme upgrade operations, not for all file operations.
   - Consider using file_get_contents(), scandir() or glob()
------------------------------------------------------------------- */
if ( ! function_exists( 'trx_addons_init_filesystem' ) ) {
	add_action( 'after_setup_theme', 'trx_addons_init_filesystem', 0 );
	/**
	 * Init WP Filesystem before the plugins and theme init.
	 * Attention! WordPress is not recommended to use the class WP_Filesystem for regular file operations.
	 * 
	 * @hooked after_setup_theme
	 * 
	 * @param bool $force Force init WP Filesystem
	 */
	function trx_addons_init_filesystem( $force = false ) {
		if ( TRX_ADDONS_USE_WP_FILESYSTEM || $force ) {
			if ( ! function_exists('WP_Filesystem') ) {
				require_once trailingslashit( ABSPATH ) . 'wp-admin/includes/file.php';
			}
			if ( is_admin() ) {
				$url = admin_url();
				$creds = false;
				// First attempt to get credentials.
				if ( function_exists( 'request_filesystem_credentials' ) && false === ( $creds = request_filesystem_credentials( $url, '', false, false, array() ) ) ) {
					// If we comes here - we don't have credentials
					// so the request for them is displaying no need for further processing
					return false;
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
		}
		return true;
	}
}

if ( ! function_exists( 'trx_addons_fpc' ) ) {	
	/**
	 * Put data to the specified file
	 *
	 * @param string $file		File name (or path relative to the theme folder)
	 * @param string $data		Data to put into the file
	 * @param int $flag			Flag to modify the behavior of the function
	 * 
	 * @return int				Number of bytes that were written to the file, or false on failure.
	 */
	function trx_addons_fpc( $file, $data, $flag = 0 ) {
		if ( TRX_ADDONS_USE_WP_FILESYSTEM ) {
			global $wp_filesystem;
			if ( ! empty( $file ) ) {
				if ( isset( $wp_filesystem ) && is_object( $wp_filesystem ) ) {
					$file = str_replace(ABSPATH, $wp_filesystem->abspath(), $file);
					// Attention! WP_Filesystem can't append the content to the file!
					if ( $flag == FILE_APPEND && $wp_filesystem->exists( $file ) && ! trx_addons_is_url( $file ) ) {
						// If it is a existing local file and we need to append data -
						// use native PHP function to prevent large consumption of memory
						return file_put_contents( $file, $data, $flag );
					} else {
						// In other case (not a local file or not need to append data or file not exists)
						// That's why we have to read the contents of the file into a string,
						// add new content to this string and re-write it to the file if parameter $flag == FILE_APPEND!
						return $wp_filesystem->put_contents( $file,
															( $flag == FILE_APPEND && $wp_filesystem->exists($file)
																? $wp_filesystem->get_contents( $file )
																: ''
																)
															. $data,
															false );
					}
				} else {
					if ( trx_addons_is_on( trx_addons_get_option( 'debug_mode', false, false ) ) ) {
						throw new Exception( sprintf( esc_html__( 'WP Filesystem is not initialized! Put contents to the file "%s" failed', 'trx_addons' ), $file ) );
					}
				}
			}
		} else {
			if ( ! empty( $file ) ) {
				$file = trx_addons_prepare_path( $file );
				return file_put_contents( $file, $data, $flag );
			}
		}
		return false;
	}
}

if ( ! function_exists( 'trx_addons_fgc' ) ) {
	/**
	 * Get content of the specified file
	 *
	 * @param string $file		File name (or path relative to the theme folder)
	 * @param bool $unpack		Unpack data after get it from file
	 * 
	 * @return string			Content of the file
	 */
	function trx_addons_fgc( $file, $unpack = false ) {
		$tmp_cont = '';
		if ( ! empty( $file ) ) {
			if ( TRX_ADDONS_USE_WP_FILESYSTEM ) {
				global $wp_filesystem;
				if ( isset( $wp_filesystem ) && is_object( $wp_filesystem ) ) {
					$file = str_replace( ABSPATH, $wp_filesystem->abspath(), $file );
					$tmp_cont = trx_addons_is_url( $file ) //&& ! $allow_url_fopen 
									? trx_addons_remote_get( $file ) 
									: $wp_filesystem->get_contents( $file );
				} else {
					if ( trx_addons_is_on( trx_addons_get_option( 'debug_mode', false, false ) ) ) {
						throw new Exception( sprintf( esc_html__( 'WP Filesystem is not initialized! Get contents from the file "%s" failed', 'trx_addons' ), $file ) );
					}
				}
			} else {
				if ( trx_addons_is_url( $file ) ) { //&& ! $allow_url_fopen 
					$tmp_cont = trx_addons_remote_get( $file );
				} else {
					$file = trx_addons_prepare_path( $file );
					if ( file_exists( $file ) ) {
						$tmp_cont = file_get_contents( $file );
					}
				}
			}
		}
		if ( ! empty( $tmp_cont ) && $unpack && trx_addons_get_file_ext( $file ) == 'zip' ) {
			$tmp_name = 'tmp-'.rand().'.zip';
			$tmp = wp_upload_bits( $tmp_name, null, $tmp_cont );
			if ( $tmp['error'] ) {
				$tmp_cont = '';
			} else {
				trx_addons_unzip_file( $tmp['file'], dirname( $tmp['file'] ) );
				$file_name = trailingslashit( dirname( $tmp['file'] ) ) . basename( $file, '.zip' ) . '.txt';
				$tmp_cont = trx_addons_fgc( $file_name );
				unlink( $tmp['file'] );
				unlink( $file_name );
			}
		}
		return $tmp_cont;
	}
}

if ( ! function_exists( 'trx_addons_fga' ) ) {
	/**
	 * Get array with rows from specified file
	 *
	 * @param string $file		File name (or path relative to the theme folder)
	 * 
	 * @return array			Array with rows from the file
	 */
	function trx_addons_fga( $file ) {
		if ( ! empty( $file ) ) {
			if ( TRX_ADDONS_USE_WP_FILESYSTEM ) {
				global $wp_filesystem;
				if ( isset( $wp_filesystem ) && is_object( $wp_filesystem ) ) {
					$file = str_replace( ABSPATH, $wp_filesystem->abspath(), $file );
					return $wp_filesystem->get_contents_array( $file );
				} else {
					if ( trx_addons_is_on( trx_addons_get_option( 'debug_mode', false, false ) ) ) {
						throw new Exception( sprintf( esc_html__( 'WP Filesystem is not initialized! Get rows from the file "%s" failed', 'trx_addons' ), $file ) );
					}
				}
			} else {
				$file = trx_addons_prepare_path( $file );
				if ( file_exists( $file ) ) {
					return file( $file );
				}
			}
		}
		return array();
	}
}

if ( ! function_exists( 'trx_addons_mkdir' ) ) {
	/**
	 * Create folder
	 *
	 * @param string $path		Path to folder
	 * 
	 * @return bool				True if success
	 */
	function trx_addons_mkdir( $path ) {
		if ( ! empty( $path ) ) {
			if ( TRX_ADDONS_USE_WP_FILESYSTEM ) {
				global $wp_filesystem;
				if ( isset( $wp_filesystem ) && is_object( $wp_filesystem ) ) {
					$path = str_replace( ABSPATH, $wp_filesystem->abspath(), $path );
					if ( ! $wp_filesystem->is_dir( $path ) ) {
						if ( ! $wp_filesystem->mkdir( $path, FS_CHMOD_DIR ) ) {
							if ( trx_addons_is_on( trx_addons_get_option( 'debug_mode' ) ) ) {
								// Translators: Add the file name to the message
								throw new Exception( sprintf( esc_html__( 'Create a folder "%s" failed', 'trx_addons' ), $path ) );
							}
						} else {
							return true;
						}
					} else {
						return true;
					}
				} else {
					if ( trx_addons_is_on( trx_addons_get_option( 'debug_mode' ) ) ) {
						// Translators: Add the file name to the message
						throw new Exception( sprintf( esc_html__( 'WP Filesystem is not initialized! Create a folder "%s" failed', 'trx_addons' ), $path ) );
					}
				}
			} else {
				$path = trx_addons_prepare_path( $path );
				if ( ! is_dir( $path ) ) {
					if ( ! wp_mkdir_p( $path ) ) {
						if ( trx_addons_is_on( trx_addons_get_option( 'debug_mode' ) ) ) {
							// Translators: Add the file name to the message
							throw new Exception( sprintf( esc_html__( 'Create a folder "%s" failed', 'trx_addons' ), $path ) );
						}
					} else {
						return true;
					}
				} else {
					return true;
				}
			}
		}
		return false;
	}
}

if ( ! function_exists( 'trx_addons_unlink' ) ) {
	/**
	 * Delete file or folder. If folder is specified - delete all files and subfolders
	 *
	 * @param string $path		Path to file or folder
	 * @param bool $recursive	Recursive delete. Deprecated since 2.3.0
	 * @param string $type		Type of the path: 'f' - file, 'd' - folder. Deprecated since 2.3.0
	 * 
	 * @return bool				True if success
	 */
	function trx_addons_unlink( $path, $recursive = true, $type = 'd' ) {
		if ( ! empty( $path ) ) {
			if ( TRX_ADDONS_USE_WP_FILESYSTEM ) {
				global $wp_filesystem;
				if ( isset( $wp_filesystem ) && is_object( $wp_filesystem ) ) {
					$path = str_replace( ABSPATH, $wp_filesystem->abspath(), $path );
					return $wp_filesystem->delete( $path, true, $wp_filesystem->is_file( $path ) ? 'f' : 'd' );
				} else {
					if ( trx_addons_is_on( trx_addons_get_option( 'debug_mode' ) ) ) {
						// Translators: Add the file name to the message
						throw new Exception( sprintf( esc_html__( 'WP Filesystem is not initialized! Delete a file/folder "%s" failed', 'trx_addons' ), $path ) );
					}
				}
			} else {
				$path = trx_addons_prepare_path( $path );
				if ( is_dir( $path ) ) {
					$files = scandir( $path,  SCANDIR_SORT_NONE );
					foreach ( $files as $file ) {
						if ( $file != "." && $file != ".." ) {
							trx_addons_unlink( "$path/$file" );
						}
					}
					rmdir( $path );
					return true;
				} else if ( file_exists( $path ) ) {
					unlink( $path );
					return true;
				}
			}
		}
		return false;
	}
}

if ( ! function_exists( 'trx_addons_copy' ) ) {
	/**
	 * Copy file or folder. If folder is specified - copy all files and subfolders
	 *
	 * @param string $src		Path to source file or folder
	 * @param string $dst		Path to destination file or folder
	 * 
	 * @return bool				True if success
	 */
	function trx_addons_copy( $src, $dst ) {
		if ( ! empty( $src ) && ! empty( $dst ) ) {
			if ( TRX_ADDONS_USE_WP_FILESYSTEM && function_exists( 'copy_dir' ) ) {
				$src = trx_addons_prepare_path( $src );
				$dst = trx_addons_prepare_path( $dst );
				return copy_dir( $src, $dst );
			} else {
				trx_addons_unlink( $dst );
				if ( is_dir( $src ) ) {
					if ( ! is_dir( $dst ) ) {
						trx_addons_mkdir( $dst );
					}
					$files = scandir( $src, SCANDIR_SORT_NONE );
					foreach ( $files as $file ) {
						if ( $file != "." && $file != ".." ) {
							trx_addons_copy( "$src/$file", "$dst/$file" );
						}
					}
					return true;
				} else if ( file_exists( $src ) ) {
					return copy( $src, $dst );
				}
			}
		}
		return false;
	}
}

if ( ! function_exists( 'trx_addons_list_files' ) ) {
	/**
	 * Get list of files in the folder
	 *
	 * @param string $path			Path to folder
	 * @param int $recursive_levels	Recursive levels
	 * 
	 * @return array				List of files
	 */
	function trx_addons_list_files( $path, $recursive_levels = 1 ) {
		$list = array();
		if ( ! empty( $path ) ) {
			if ( function_exists( 'list_files' ) ) {
				$path = trx_addons_prepare_path( $path );
				return list_files( $path, max( 1, $recursive_levels ) );
			} else {
				if ( is_dir( $path ) ) {
					$files = scandir( $path );	//, SCANDIR_SORT_NONE
					foreach ( $files as $file ) {
						if ( $file != "." && $file != ".." ) {
							if ( is_dir( "$path/$file" ) ) {
								if ( $recursive_levels > 1 ) {
									$list = array_merge( $list, trx_addons_list_files( "$path/$file", $recursive_levels - 1 ) );
								}
							} else {
								$list[] = "$path/$file";
							}
						}
					}
				} else if ( file_exists( $path ) ) {
					$list[] = $path;
				}
			}
		}
		return $list;
	}
}

if ( ! function_exists( 'trx_addons_remote_get' ) ) {	
	/**
	 * Get remote file content via HTTP GET request
	 *
	 * @param string $file		Remote file URL
	 * @param array $args		Additional arguments for wp_remote_get() function
	 * 
	 * @return string			Remote file content
	 */
	function trx_addons_remote_get( $file, $args = array() ) {
		$args = array_merge(
					array(
						'method'     => 'GET',
						'timeout'    => -1,
						'user-agent' => TRX_ADDONS_REMOTE_USER_AGENT
					),
					is_array( $args ) ? $args : array( 'timeout' => $args )
				);
		// Set timeout as half of the PHP execution time
		if ( $args['timeout'] < 1 ) {
			$args['timeout'] = round( 0.5 * max( 30, function_exists( 'ini_get' ) ? ini_get( 'max_execution_time' ) : 30 ) );
		}
		// Add current protocol (if not specified)
		$file = trx_addons_add_protocol( $file );
		// Do request and get a response
		$response = wp_remote_get( $file, $args );
		// Save last error to the globals
		global $TRX_ADDONS_STORAGE;
		$TRX_ADDONS_STORAGE['last_remote_error'] = is_wp_error( $response ) ? $response->get_error_message() : '';
		// Check the response code and return response body if OK
		return ! is_wp_error( $response ) && isset( $response['response']['code'] ) && $response['response']['code'] == 200
					? $response['body']
					: '';
	}
}

if ( ! function_exists( 'trx_addons_remote_post' ) ) {
	/**
	 * Get remote file content via HTTP POST request
	 *
	 * @param string $file		Remote file URL
	 * @param array $args		Additional arguments for wp_remote_post() function
	 * @param array $vars		Additional variables to send in the request body
	 * 
	 * @return string			Remote file content
	 */
	function trx_addons_remote_post( $file, $args = array(), $vars = array() ) {
		$args = array_merge(
					array(
						'method'     => 'POST',
						'timeout'    => -1,
						'user-agent' => TRX_ADDONS_REMOTE_USER_AGENT
					),
					is_array( $args ) ? $args : array( 'timeout' => $args )
				);
		// Add variables to the request body
		if ( is_array( $vars ) && count( $vars ) > 0 ) {
			$args['body'] = $vars;
		}
		// Set timeout as half of the PHP execution time
		if ( $args['timeout'] < 1 ) {
			$args['timeout'] = round( 0.5 * max( 30, ini_get( 'max_execution_time' ) ) );
		}
		// Add current protocol (if not specified)
		$file = trx_addons_add_protocol( $file );
		// Do request and get a response
		$response = wp_remote_post( $file, $args );
		// Check the response code and return response body if OK
		return ! is_wp_error( $response ) && isset( $response['response']['code'] ) && $response['response']['code'] == 200
					? $response['body']
					: '';
	}
}

if ( ! function_exists( 'trx_addons_remote_curl' ) ) {	
	/**
	 * Get remote file content via cURL
	 *
	 * @param string $file			Remote file URL
	 * @param array $vars			Additional variables to send in the request body
	 * @param array $args			Additional arguments of the request
	 * @param array $curl_options	Additional options for curl_setopt_array() function
	 * 
	 * @return string				Remote file content
	 */
	function trx_addons_remote_curl( $file, $vars = array(), $args = array(), $curl_options = array() ) {
		$response = '';
		if ( function_exists( 'curl_init' ) ) {
			// Init connection
			$ch = curl_init();
			// If inited - prepare request
			if ( $ch > 0 ) {
				$file = trx_addons_add_protocol( $file );
				// Default options
				$defa = array(
							CURLOPT_URL            => $file,
							CURLOPT_USERAGENT      => TRX_ADDONS_REMOTE_USER_AGENT,
							CURLOPT_RETURNTRANSFER => 1,
							CURLOPT_FOLLOWLOCATION => 1,
							CURLOPT_MAXREDIRS      => 5,
							CURLOPT_AUTOREFERER    => 1,
							CURLOPT_SSL_VERIFYPEER => 0,
							CURLOPT_SSL_VERIFYHOST => 0,
							);
				// Enable SSL if need
//				if ( strpos( $file, 'https://' ) === 0 ) {
//					$defa[ CURLOPT_SSLVERSION ] = 3;
//				}
				// Add timeout
				$timeout = ! empty( $args['timeout'] ) ? $args['timeout'] : -1;
				if ( $timeout < 1 ) {
					$timeout = round( 0.5 * max( 30, ini_get( 'max_execution_time' ) ) );
				}
				$defa[CURLOPT_TIMEOUT] = $timeout;
				$defa[CURLOPT_CONNECTTIMEOUT] = max( 10, min( 30, round( $timeout / 2 ) ) );
				// Add method
				$method = ! empty( $args['method'] ) ? $args['method'] : '';
				if ( $method == 'put' ) {
					$defa[CURLOPT_CUSTOMREQUEST] = 'PUT';
					$defa[CURLOPT_PUT] = 1;
				} else if ( $method == 'post' || ( empty( $method ) && ! empty( $vars ) ) ) {
					$defa[CURLOPT_CUSTOMREQUEST] = 'POST';
					$defa[CURLOPT_POST] = 1;
				}
				// Add proxy
				if ( ! empty( $args['proxy'] ) ) {
					$defa[CURLOPT_PROXY] = $args['proxy'];
//					$defa[CURLOPT_PROXYTYPE] = CURLPROXY_HTTP;
//					$defa[CURLOPT_HTTPPROXYTUNNEL] = 0;
//					$defa[CURLOPT_HEADER] = 0;
//					$defa[CURLOPT_ENCODING] = '';
					// Add user and password (if specified) as 'user:pwd'
					if ( ! empty( $args['proxy_user_pwd'] ) ) {
						$defa[CURLOPT_PROXYUSERPWD] = $args['proxy_user_pwd'];
					}
				}
				// Add headers
				if ( ! empty( $args['headers'] ) && is_array( $args['headers'] ) && count( $args['headers'] ) > 0 ) {
					$defa[CURLOPT_HTTPHEADER] = $args['headers'];
				}
				// Add data fields (query arguments)
				if ( is_array( $vars ) && count( $vars ) > 0 ) {
					$defa[CURLOPT_POSTFIELDS] = $method == 'put'
													? http_build_query( $vars )
													: $vars;
				} else if ( ! is_array( $vars ) && ! empty( $vars ) ) {
					$defa[CURLOPT_POSTFIELDS] = $vars;
				}
				// Add native cURL options
				foreach ( $curl_options as $k => $v ) {
					$defa[$k] = $v;
				}
				// Set options
				curl_setopt_array( $ch, $defa );
				// Do request and get a response
				$response = curl_exec( $ch );
				// Check the response code
				$response_code = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
				// If failure - set global variable with error code and message
				if ( $response_code < 200 || $response_code > 299 ) {
					$GLOBALS['trx_addons_last_curl_error'] = curl_errno( $ch ) > 0
																? curl_errno( $ch ) . ' (' . curl_error( $ch ) . ')'
																: $response_code;
					$response = '';
				}
				// Close connection
				curl_close($ch);
			}
		}
		return $response;
	}
}

if ( ! function_exists( 'trx_addons_unzip_file' ) ) {
	/**
	 * Init a $wp_filesystem ( if need ) and unzip file
	 * 
	 * @param string $zip  path to zip file
	 * @param string $dest path to destination folder
	 * 
	 * @return bool  	true if success
	 */
	function trx_addons_unzip_file( $zip, $dest ) {
		global $wp_filesystem;
		if ( empty( $wp_filesystem ) || ! is_object( $wp_filesystem ) ) {
			trx_addons_init_filesystem( true );
		}
		return unzip_file( $zip, $dest );
	}
}

if ( ! function_exists( 'trx_addons_retrieve_json' ) ) {	
	add_filter( 'trx_addons_filter_retrieve_json', 'trx_addons_retrieve_json' );
	/**
	 * Get JSON from specified url via HTTP (cURL) and return object or null
	 * 
	 * @hooked trx_addons_filter_retrieve_json
	 * 
	 * @param string $url	URL to get JSON
	 * 
	 * @return object|null	JSON object or null
	 */
	function trx_addons_retrieve_json( $url ) {
		$data = '';
		$resp = trim( trx_addons_remote_get( $url ) );
		if ( in_array( substr( $resp, 0, 1 ), array( '{', '[' ) ) ) {
			$data = json_decode( $resp, true );
		}
		return $data;
	}
}

if ( ! function_exists( 'trx_addons_esc' ) ) {	
	/**
	 * Remove unsafe characters from file/folder path
	 * 
	 * @param string $name		File/folder path
	 * 
	 * @return string			Safe path
	 */
	function trx_addons_esc( $name ) {
		return str_replace(
					array( '\\', '~', '$', ':', ';', '+', '>', '<', '|', '"', "'", '`', "\xFF", "\x0A", "\x0D", '*', '?', '^' ),
					defined( 'DIRECTORY_SEPARATOR' ) ? DIRECTORY_SEPARATOR : '/',
					trim( $name )
				);
	}
}

if ( ! function_exists('trx_addons_prepare_path')) {	
	/**
	 * Replace '\' with '/' in the file/folder path
	 * 
	 * @param string $name		File/folder path
	 * 
	 * @return string			Safe path
	 */
	function trx_addons_prepare_path( $name ) {
		return str_replace( '\\', defined( 'DIRECTORY_SEPARATOR' ) ? DIRECTORY_SEPARATOR : '/', trim( $name ) );
	}
}

if ( ! function_exists( 'trx_addons_url_to_local_path' ) ) {	
	/**
	 * Convert URL to local path
	 * 
	 * @param string $url		URL to convert
	 * 
	 * @return string			Local path
	 */
	function trx_addons_url_to_local_path( $url ) {
		$path = '';
		// Remove scheme from url
		$url = trx_addons_remove_protocol( $url );
		// Get upload path & dir
		$upload_info = wp_upload_dir();
		// Where check file
		$locations = array(
			'uploads' => array(
				'dir' => $upload_info['basedir'],
				'url' => trx_addons_remove_protocol($upload_info['baseurl'])
				),
			'child' => array(
				'dir' => get_stylesheet_directory(),
				'url' => trx_addons_remove_protocol(get_stylesheet_directory_uri())
				),
			'theme' => array(
				'dir' => get_template_directory(),
				'url' => trx_addons_remove_protocol(get_template_directory_uri())
				)
			);
		// Find a file in locations
		foreach( $locations as $key => $loc ) {
			// Check if $url is in location
			if ( false === strpos( $url, $loc['url'] ) ) continue;
			// Get a path from the URL
			$path = str_replace( $loc['url'], $loc['dir'], $url );
			// Check if a file exists
			if ( file_exists( $path ) ) {
				break;
			}
			$path = '';
		}
		return $path;
	}
}
