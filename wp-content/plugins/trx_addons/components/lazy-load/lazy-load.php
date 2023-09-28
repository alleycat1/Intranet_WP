<?php
/**
 * Image, video and audio lazy loading'
 *
 * @package ThemeREX Addons
 * @since v1.98.0
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

// Define component's subfolder
if ( ! defined( 'TRX_ADDONS_PLUGIN_LAZY_LOAD' ) ) define( 'TRX_ADDONS_PLUGIN_LAZY_LOAD', TRX_ADDONS_PLUGIN_COMPONENTS . 'lazy-load/' );


// Add component to the global list
if ( ! function_exists( 'trx_addons_lazy_load_add_to_components' ) ) {
	add_filter( 'trx_addons_components_list', 'trx_addons_lazy_load_add_to_components' );
	function trx_addons_lazy_load_add_to_components( $list = array() ) {
		$list['lazy-load'] = array(
			'title' => __( 'Lazy load for images, iframes, audio and video', 'trx_addons' )
		);
		return $list;
	}
}


// Check if module is enabled
if ( ! function_exists( 'trx_addons_lazy_load_allowed' ) ) {
	function trx_addons_lazy_load_allowed() {
		static $allowed = null;
		if ( $allowed === null ) {
			$allowed = trx_addons_components_is_allowed( 'components', 'lazy-load' );
		}
		return $allowed;
	}
}


// Return LazyLoad status
if ( ! function_exists( 'trx_addons_lazy_load_enable' ) ) {
	function trx_addons_lazy_load_enable() {
		if ( ! trx_addons_lazy_load_allowed()
			|| trx_addons_is_off( trx_addons_get_option( 'lazy_load' ) ) 
			|| trx_addons_is_preview()
			|| ( function_exists( 'ampforwp_is_amp_endpoint' ) && ampforwp_is_amp_endpoint() )
		) {
			return apply_filters( 'trx_addons_filter_allow_lazy_load', false );
		}
		return true;
	}
}


// Load scripts and styles
if ( ! function_exists( 'trx_addons_lazy_load_load_scripts' ) ) {
	add_action( "wp_enqueue_scripts", 'trx_addons_lazy_load_load_scripts', 1 );
	function trx_addons_lazy_load_load_scripts() {
		if ( trx_addons_lazy_load_enable() ) {
			// If 'debug_mode' is off - load merged styles and scripts
			if ( trx_addons_is_on( trx_addons_get_option( 'debug_mode' ) ) ) {
				wp_enqueue_style( 'trx_addons-lazy-load', trx_addons_get_file_url( TRX_ADDONS_PLUGIN_LAZY_LOAD . 'lazy-load.css' ), array(), null );
				wp_enqueue_script( 'trx_addons-lazy-load', trx_addons_get_file_url( TRX_ADDONS_PLUGIN_LAZY_LOAD . 'lazy-load.js' ), array('jquery'), null, true );
			}	
		}
	}
}

// Merge styles to the single stylesheet
if ( ! function_exists( 'trx_addons_lazy_load_merge_styles' ) ) {
	add_filter( "trx_addons_filter_merge_styles", 'trx_addons_lazy_load_merge_styles', 1 );
	function trx_addons_lazy_load_merge_styles($list) {
		if ( trx_addons_lazy_load_allowed() && trx_addons_is_on( trx_addons_get_option( 'lazy_load' ) ) ) {
			$list[ TRX_ADDONS_PLUGIN_LAZY_LOAD . 'lazy-load.css' ] = true;
		}
		return $list;
	}
}
	
// Merge specific scripts into single file
if ( ! function_exists( 'trx_addons_lazy_load_merge_scripts' ) ) {
	add_action( "trx_addons_filter_merge_scripts", 'trx_addons_lazy_load_merge_scripts', 1 );
	function trx_addons_lazy_load_merge_scripts($list) {
		if ( trx_addons_lazy_load_allowed() && trx_addons_is_on( trx_addons_get_option( 'lazy_load' ) ) ) {
			$list[ TRX_ADDONS_PLUGIN_LAZY_LOAD . 'lazy-load.js' ] = true;
		}
		return $list;
	}
}

// Add 'Lazy load' section in the ThemeREX Addons Options
if ( ! function_exists( 'trx_addons_lazy_load_options' ) ) {
	add_filter( 'trx_addons_filter_options', 'trx_addons_lazy_load_options' );
	function trx_addons_lazy_load_options( $options ) {
		// Add section 'Lazy load'
		if ( trx_addons_lazy_load_allowed() ) {
			trx_addons_array_insert_before($options, 'cache_info', array(
				'lazy_load_info'   => array(
					'title' => esc_html__( 'Lazy Loading', 'trx_addons' ),
					'desc'  => '',
					'demo'  => false,
					'type'  => 'info',
				),
				'lazy_load'        => array(
					'title' => esc_html__( 'Enable media lazy loading', 'trx_addons' ),
					'desc'  => wp_kses_data( __( 'Enable image, video, audio and iframe lazy loading', 'trx_addons' ) ),
					'std'   => 0,
					'type'  => 'switch',
				),
				'lazy_load_page'   => array(
					'title' => esc_html__( 'Number of images excluded from lazy loading on pages', 'trx_addons' ),
					'desc'  => wp_kses_data( __( 'Select the number of images to NOT preload with lazy loading on pages (in order of their appearance on the page)', 'trx_addons' ) ),
					'dependency' => array(
						'lazy_load' => array( 1 )
					),
					'std'        => 0,
					'options'    => trx_addons_get_list_range( 0, 10 ),
					'type'       => 'select',
				),
				'lazy_load_single' => array(
					'title' => esc_html__( 'Number of images excluded from lazy loading on posts', 'trx_addons' ),
					'desc'  => wp_kses_data( __( 'Select the number of images to NOT preload with lazy loading on single posts  (in order of their appearance on the page)
', 'trx_addons' ) ),
					'dependency' => array(
						'lazy_load' => array( 1 )
					),
					'std'        => 0,
					'options'    => trx_addons_get_list_range( 0, 10 ),
					'type'       => 'select',
				),
			));
		}		
		return $options;
	}
}

// Disable WP Lazy Load
if ( ! function_exists( 'trx_addons_lazy_load_setup' ) ) {
	add_action( 'after_setup_theme', 'trx_addons_lazy_load_setup', 9 );
	function trx_addons_lazy_load_setup() {
		if ( trx_addons_lazy_load_allowed() ) {
			if ( trx_addons_is_on( trx_addons_get_option( 'lazy_load' ) ) ) {
				add_filter( 'wp_lazy_loading_enabled', '__return_false' );
			}
		}
	}
}

// Add theme specified classes to the body
if ( ! function_exists( 'trx_addons_lazy_load_add_body_classes' ) ) {
	add_filter( 'body_class', 'trx_addons_lazy_load_add_body_classes' );
	function trx_addons_lazy_load_add_body_classes( $classes ) {
		if ( trx_addons_lazy_load_enable() ) {
			$classes[] = 'allow_lazy_load';
		}
		return $classes;
	}
}

// Detect blog, archive, tag, search and single post page
if ( ! function_exists( 'trx_addons_detect_blog_and_single_post' ) ) {
	function trx_addons_detect_blog_and_single_post() {
		return is_home() || is_category() || is_tag() || is_author() || is_search() || ( trx_addons_is_singular() && !is_page() );
	}
}

// Processing post's images on the blog, archive, tag, search and single post page
if ( ! function_exists( 'trx_addons_lazy_load_process_images_on_archive' ) ) {
	add_filter( 'wp_get_attachment_image', 'trx_addons_lazy_load_process_images_on_archive', 200, 5 );
	function trx_addons_lazy_load_process_images_on_archive( $html, $attachment_id, $size, $icon, $attr ) { 
		if ( trx_addons_lazy_load_enable() ) {
			if ( trx_addons_detect_blog_and_single_post() ) {
				$html = trx_addons_lazy_load_content_process_images( $html, false );
			}
		}
		return $html;
	}
}

// Processing post's sliders on the blog, archive, tag, search and single post page
if ( ! function_exists( 'trx_addons_lazy_load_process_slider_images_on_archive' ) ) {
	add_filter( 'trx_addons_filter_slider_slide', 'trx_addons_lazy_load_process_slider_images_on_archive', 200, 3 );
	function trx_addons_lazy_load_process_slider_images_on_archive( $html, $image, $args) { 
		if ( trx_addons_lazy_load_enable() ) {
			if ( trx_addons_detect_blog_and_single_post() ) {
				$html = trx_addons_lazy_load_content_process_images( $html, false );
			}
		}
		return $html;
	}
}

// WOOCOMMERCE - Processing product images
if ( ! function_exists( 'trx_addons_lazy_load_process_products_images' ) ) {
	add_filter( 'woocommerce_product_get_image', 'trx_addons_lazy_load_process_products_images', 200, 6 );
	function trx_addons_lazy_load_process_products_images( $html, $product, $size, $attr, $placeholder, $image) {
		if ( trx_addons_lazy_load_enable() ) {
			$html = trx_addons_lazy_load_content_process_images( $html, false, true, false, false, false );
		}
		return $html;
	}
}

// FOR THEME - Processing images
if ( ! function_exists( 'trx_addons_lazy_load_process_images' ) ) {
	add_filter( 'trx_addons_filter_lazy_load_process_images', 'trx_addons_lazy_load_process_images', 200, 6 );
	function trx_addons_lazy_load_process_images( $html, $preload = false, $img = true, $img2 = true, $bg_img = true, $media = true) {
		if ( trx_addons_lazy_load_enable() ) {
			$html = trx_addons_lazy_load_content_process_images( $html, $preload, $img, $img2, $bg_img, $media );
		}
		return $html;
	}
}

// Processing images in the content
if ( ! function_exists( 'trx_addons_lazy_load_content_process_images' ) ) {
	add_filter( 'the_content', 'trx_addons_lazy_load_content_process_images', 200, 1 );
	add_filter( 'trx_addons_filter_page_content', 'trx_addons_lazy_load_content_process_images' );
	function trx_addons_lazy_load_content_process_images( $content, $preload = true, $img = true, $img2 = true, $bg_img = true, $media = true ) {
		if ( trx_addons_lazy_load_enable() && ! is_admin() ) {

			// Return if tags not exists in the content
			if ( ! preg_match( '/<[a-z]+ /', $content ) ) {
				return $content;
			}

			if ( $preload ) {
				preg_match_all( '/(<img[^>]*(gif|jpg|png|swf|swc|psd|tiff|bmp|iff|jp2|jpx|jb2|jpc|xbm|wbmp)[^>]*?>)|(<[^>]* style="background-image:(\s?)url\([^\s]+\)[^>]*?>)|(<[^>]+ class="[^"]+_inline_[^"]+"[^>]*?>)/', $content, $matches ); 
				$images = array_shift( $matches ); 
				if ( $images ) {  
					$max_images = apply_filters( 'trx_addons_filter_lazy_load_skip_images_on_page', trx_addons_get_option( 'lazy_load_page' ) );
					if ( trx_addons_detect_blog_and_single_post() ) {
						$max_images = apply_filters( 'trx_addons_filter_lazy_load_skip_images_on_single', trx_addons_get_option( 'lazy_load_single' ) );
					}
					for ( $i = 0; $i < $max_images; $i++ ) {
						if ( array_key_exists( $i, $images ) ) {
							$image = $images[$i];
							if ( ! preg_match( '/( data-trx-lazyload-src=)|( data-trx-lazyload-style=)|( data-lazy-src=)/', $image ) ) {
								$new_image = $image;
								if ( preg_match( '/ class="/', $new_image ) ) {
									$new_image = str_replace( ' class="', ' class="lazyload_inited ', $new_image );
								} else {
									$new_image = str_replace( '<img ', '<img class="lazyload_inited" ', $new_image );
								}
								// Update image
								$content = str_replace( $image, $new_image, $content );
							}
						}
					}
				}
			}

			// Placeholder url
			$image_url = trx_addons_get_file_url( TRX_ADDONS_PLUGIN_LAZY_LOAD . 'images/placeholder.png' );

			// Image tag <img ...>
			if ( $img ) { 
				// Get all images
				preg_match_all( '/<img[^>]*(gif|jpg|png|swf|swc|psd|tiff|bmp|iff|jp2|jpx|jb2|jpc|xbm|wbmp)[^>]*?>/', $content, $matches ); 
				$images = array_shift( $matches ); 
				// Check exists images
				if ( $images ) {              
					foreach ( $images as $image ) {
						if (   ! preg_match( '/ class="[^"]*(lazyload_inited)|(skip-lazy)[^"]*?"/', $image )
							&& ! preg_match( '/( data-trx-lazyload-src=)|( data-lazy-src=)/', $image )
							&& ! preg_match( '/ data-lazyload="[^\s]+"|(instagram)|(no-image.jpg)|(twimg)/', $image )
							&&   preg_match( '/ src="[^\s]+"/', $image )
						) { 
							$new_image = $image;  

							// Calculate height
							$new_image = trx_addons_lazy_load_calculate_images_height( $new_image );

							// Remove srcset attribute
							$new_image = preg_replace( '/(srcset=")[^"]*?"/', '', $new_image );
							// Remove sizes attribute
							$new_image = preg_replace( '/(sizes=")[^"]*?"/', '', $new_image );
							// Create original image backup
							$new_image = str_replace( ' src=', ' data-trx-lazyload-src=', $new_image );
							// Insert image placeholder URL     
							$new_image = str_replace( '<img ', '<img src="' . esc_url( $image_url ) . '" ', $new_image );
							// Update image
							$content = str_replace( $image, $new_image, $content );
						}
					}
				}
			}

			if ( $img2 ) {
				preg_match_all( '/&lt;img(?:(?!&gt;).)*(gif|jpg|png|swf|swc|psd|tiff|bmp|iff|jp2|jpx|jb2|jpc|xbm|wbmp)(?:(?!&gt;).)*?&gt;/', $content, $matches ); 
				$images = array_shift( $matches ); 
				// Check exists images
				if ( $images ) {              
					foreach ( $images as $image ) {
						if ( ! preg_match( '/( data-trx-lazyload-src=)|( data-lazy-src=)/', $image ) ) {
							$new_image = $image;

							// Calculate height
							$new_image = str_replace( '&quot;', '"', $new_image );
							$new_image = str_replace( '&lt;', '<', $new_image );
							$new_image = trx_addons_lazy_load_calculate_images_height( $new_image );
							$new_image = str_replace( '"', '&quot;', $new_image );
							$new_image = str_replace( '<', '&lt;', $new_image );

							// Create original image backup
							$new_image = str_replace( ' src=', ' data-trx-lazyload-src=', $new_image );
							// Insert image placeholder URL     
							$new_image = str_replace( '&lt;img ', '&lt;img src=&quot;' . esc_url( $image_url ) . '&quot; ', $new_image );
							// Update image
							$content = str_replace( $image, $new_image, $content );
						}
					}
				}
			}

			// Image style="background-image"
			if ( $bg_img ) {
				// Get all background images
				preg_match_all( '/<[^>]* style="background-image:(\s?)url\([^\s]+\)[^>]*?>/', $content, $matches );
				$images = array_shift( $matches );
				// Check exists images.
				if ( $images ) {
					foreach ( $images as $image ) {
						if (   ! preg_match( '/ class="[^"]*lazyload_inited[^"]*?"/', $image )
							&& ! preg_match( '/ data-trx-lazyload-style=/', $image )
						) {
							preg_match_all('/background-image:(\s?)url\([^\s]+\);/', $image, $matches);
							$bg_image = array_shift( $matches );

							$new_image = $image;
							$new_image = str_replace( $bg_image[0], 'background-image: url(' . esc_url( $image_url ) . ');', $new_image );
							$new_image = str_replace( ' style="', ' data-trx-lazyload-style="' . $bg_image[0] . '" style="', $new_image );
							// Update image
							$content = str_replace( $image, $new_image, $content );
						}
					}
				}
			}

			// Video, audio, iframe
			if ( $media && apply_filters( 'trx_addons_filter_allow_media_lazy_load', true, $content ) ) {
				// Get all items
				preg_match_all( '/<(video|audio|iframe)[^>]*( src="[^\s]+")[^>]*?>/', $content, $matches );
				$items = array_shift( $matches );
				// Check exists videos
				if ( $items ) {                
					foreach ( $items as $item ) {
						if ( ! preg_match( '/ data-trx-lazyload-src=/', $item ) ) {                    
							// Create original item backup
							$new_item = str_replace( ' src=', ' data-trx-lazyload-src=', $item );       
							// Update item
							$content = str_replace( $item, $new_item, $content );
						}
					}
				}
			}
		}

		return $content;
	}
}

// Processing images in the content
if ( ! function_exists( 'trx_addons_lazy_load_calculate_images_height' ) ) {
	function trx_addons_lazy_load_calculate_images_height( $new_image ) {
		$sizes = array();
		preg_match( '/width="[0-9]+"/', $new_image, $x );
		preg_match( '/height="[0-9]+"/', $new_image, $y );
		if ( $x && $y ) {
			$sizes[] = (int) str_replace( '"', '', str_replace( 'width="', '', $x[0] ) );
			$sizes[] = (int) str_replace( '"', '', str_replace( 'height="', '', $y[0] ) );
		} else {
			preg_match( '/(src=")([^"]+)?"/', $new_image, $url );
			if ( $url ) {
				$image_info = trx_addons_getimagesize( $url[2] );
				if ( $image_info ) {
					$sizes = array( (int) $image_info[0], (int) $image_info[1] );
				}
			}
		}
		if ( count( $sizes ) > 1 ) {
			if ( $sizes[0] > 0 && $sizes[1] > 0 ) {
				$per = $sizes[1] / $sizes[0] * 100;   
				if ( preg_match( '/ style="/', $new_image ) ) {
					$new_image = str_replace( ' style="', ' data-trx-lazyload-height style="height: 0; padding-top: ' . $per . '%; ', $new_image );
				} else {
					$new_image = str_replace( '<img ', '<img data-trx-lazyload-height style="height: 0; padding-top: ' . $per . '%;" ', $new_image );
				}
			}
		}
		return $new_image;
	}
}