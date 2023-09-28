<?php
/**
 * Plugin support: The Events Calendar
 *
 * @package ThemeREX Addons
 * @since v1.2
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

if ( ! function_exists('trx_addons_exists_tribe_events' ) ) {
	/**
	 * Check if plugin 'The Events Calendar' is installed and activated
	 * 
	 * @return bool  True if plugin is installed and activated
	 */
	function trx_addons_exists_tribe_events() {
		return class_exists( 'Tribe__Events__Main' );
	}
}

if ( ! function_exists( 'trx_addons_is_tribe_events_page' ) ) {
	/**
	 * Check if a current page is any of Tribe Events page
	 *
	 * @return bool  True if current page is Tribe Events page
	 */
	function trx_addons_is_tribe_events_page() {
		$is = false;
		if ( trx_addons_exists_tribe_events() && ! is_search() ) {
			$is = tribe_is_event()
				|| tribe_is_event_query()
				|| tribe_is_event_category()
				|| tribe_is_event_venue()
				|| tribe_is_event_organizer();
		}
		return $is;
	}
}

if ( ! function_exists( 'trx_addons_tribe_events_load_scripts_front' ) ) {
	add_action( "wp_enqueue_scripts", 'trx_addons_tribe_events_load_scripts_front', TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY );
	add_action( 'trx_addons_action_pagebuilder_preview_scripts', 'trx_addons_tribe_events_load_scripts_front', 10, 1 );
	/**
	 * Enqueue custom styles
	 * 
	 * @hooked wp_enqueue_scripts
	 * @hooked trx_addons_action_pagebuilder_preview_scripts
	 * 
	 * @trigger trx_addons_action_load_scripts_front
	 * 
	 * @param bool $force  Force enqueue scripts and styles
	 */
	function trx_addons_tribe_events_load_scripts_front( $force = false ) {
		if ( ! trx_addons_exists_tribe_events() ) {
			return;
		}
		trx_addons_enqueue_optimized( 'tribe_events', $force, array(
			'css'  => array(
				'trx_addons-tribe_events' => array( 'src' => TRX_ADDONS_PLUGIN_API . 'the-events-calendar/the-events-calendar.css' ),
			),
			'need' => trx_addons_is_tribe_events_page(),
			'check' => array(
				array( 'type' => 'sc',  'sc' => 'trx_sc_events' ),
				//array( 'type' => 'gb',  'sc' => 'wp:trx-addons/events' ),	// This sc is not exists for GB
				array( 'type' => 'elm', 'sc' => '"widgetType":"trx_sc_events"' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[trx_sc_events' ),
			)
		) );
	}
}

if ( ! function_exists( 'trx_addons_tribe_events_load_scripts_front_responsive' ) ) {
	add_action( 'wp_enqueue_scripts', 'trx_addons_tribe_events_load_scripts_front_responsive', TRX_ADDONS_ENQUEUE_RESPONSIVE_PRIORITY );
	add_action( 'trx_addons_action_load_scripts_front_tribe_events', 'trx_addons_tribe_events_load_scripts_front_responsive', 10, 1 );
	/**
	 * Enqueue responsive styles for frontend
	 * 
	 * @hooked wp_enqueue_scripts
	 * @hooked trx_addons_action_load_scripts_front_tribe_events
	 * 
	 * @param bool $force  Force enqueue scripts and styles
	 */
	function trx_addons_tribe_events_load_scripts_front_responsive( $force = false ) {
		if ( ! trx_addons_exists_tribe_events() ) {
			return;
		}
		trx_addons_enqueue_optimized_responsive( 'tribe_events', $force, array(
			'css'  => array(
				'trx_addons-tribe_events-responsive' => array(
					'src' => TRX_ADDONS_PLUGIN_API . 'the-events-calendar/the-events-calendar.responsive.css',
					'media' => 'md'
				),
			),
		) );
	}
}

if ( ! function_exists( 'trx_addons_events_merge_styles' ) ) {
	add_filter( "trx_addons_filter_merge_styles", 'trx_addons_events_merge_styles' );
	/**
	 * Add a path of plugin specific styles to the list of files for merging
	 * 
	 * @hooked trx_addons_filter_merge_styles
	 * 
	 * @param array $list  List of styles to merge
	 * 
	 * @return array       List of styles to merge
	 */
	function trx_addons_events_merge_styles( $list ) {
		if ( trx_addons_exists_tribe_events() ) {
			$list[ TRX_ADDONS_PLUGIN_API . 'the-events-calendar/the-events-calendar.css' ] = false;
		}
		return $list;
	}
}

if ( ! function_exists( 'trx_addons_cpt_events_merge_styles_responsive' ) ) {
	add_filter( "trx_addons_filter_merge_styles_responsive", 'trx_addons_cpt_events_merge_styles_responsive' );
	/**
	 * Add a path of plugin specific responsive styles to the list of files for merging
	 * 
	 * @hooked trx_addons_filter_merge_styles_responsive
	 * 
	 * @param array $list  List of responsive styles to merge
	 * 
	 * @return array       List of responsive styles to merge
	 */
	function trx_addons_cpt_events_merge_styles_responsive( $list ) {
		if ( trx_addons_exists_tribe_events() ) {
			$list[ TRX_ADDONS_PLUGIN_API . 'the-events-calendar/the-events-calendar.responsive.css' ] = false;
		}
		return $list;
	}
}

if ( ! function_exists( 'trx_addons_tribe_events_check_in_html_output' ) ) {
//	add_filter( 'trx_addons_filter_get_menu_cache_html', 'trx_addons_tribe_events_check_in_html_output', 10, 1 );
//	add_action( 'trx_addons_action_show_layout_from_cache', 'trx_addons_tribe_events_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_check_page_content', 'trx_addons_tribe_events_check_in_html_output', 10, 1 );
	/**
	 * Check if the events calendar is in the HTML output of the current page and force load required styles and scripts if need
	 * 
	 * @hooked trx_addons_action_check_page_content
	 * 
	 * @trigger trx_addons_filter_check_in_html
	 *
	 * @param string $content  Current page content to check
	 * 
	 * @return string  	  Checked content
	 */
	function trx_addons_tribe_events_check_in_html_output( $content = '' ) {
		if ( ! trx_addons_exists_tribe_events() ) {
			return $content;
		}
		$args = array(
			'check' => array(
				'class=[\'"][^\'"]*(tribe\\-events\\-|tribe\\-common\\-)',
				'class=[\'"][^\'"]*type\\-(tribe_events|tribe_venue|tribe_organizer)',
				'class=[\'"][^\'"]*tribe_events_cat\\-',
			)
		);
		if ( trx_addons_check_in_html_output( 'tribe_events', $content, $args ) ) {
			trx_addons_tribe_events_load_scripts_front( true );
		}
		return $content;
	}
}

if ( ! function_exists( 'trx_addons_events_add_sort_order' ) ) {
	add_filter( 'trx_addons_filter_add_sort_order',	'trx_addons_events_add_sort_order', 10, 3 );
	/**
	 * Add custom sort order by a meta key '_EventStartDate' if the query is for Tribe Events and the sort order is 'event_date'
	 * 
	 * @hooked trx_addons_filter_add_sort_order
	 *
	 * @param array $q         Query parameters
	 * @param string $orderby  Sort order
	 * @param string $order    Sort order
	 * 
	 * @return array           Modified query parameters
	 */
	function trx_addons_events_add_sort_order( $q, $orderby, $order = 'desc' ) {
		if ( $orderby == 'event_date' ) {
			$q['order'] = $order;
			$q['orderby'] = 'meta_value';
			$q['meta_key'] = '_EventStartDate';
		}
		return $q;
	}
}

if ( ! function_exists( 'trx_addons_events_query_args' ) ) {
	add_filter ('trx_addons_filter_query_args',	'trx_addons_events_query_args', 10, 2 );
	/**
	 * Disable internal Tribe Events sort parameters for any shortcodes query with events post type
	 * 
	 * @hooked trx_addons_filter_query_args
	 *
	 * @param array $q         Query parameters
	 * @param array $sc        Shortcode parameters
	 * 
	 * @return array           Modified query parameters
	 */
	function trx_addons_events_query_args( $q, $sc ) {
		if ( trx_addons_exists_tribe_events() && ! empty( $q['post_type'] ) && in_array( Tribe__Events__Main::POSTTYPE, (array)$q['post_type'] ) ) {
			$q['tribe_suppress_query_filters'] = true;
		}
		return $q;
	}
}

if ( ! function_exists( 'trx_addons_events_post_type_taxonomy' ) ) {
	add_filter( 'trx_addons_filter_post_type_taxonomy',	'trx_addons_events_post_type_taxonomy', 10, 2 );
	/**
	 * Return a 'main' taxonomy name for the events post type (this post_type has 2+ taxonomies)
	 * 
	 * @hooked trx_addons_filter_post_type_taxonomy
	 *
	 * @param string $tax        Taxonomy name
	 * @param string $post_type  Post type name
	 * 
	 * @return string            Taxonomy name
	 */
	function trx_addons_events_post_type_taxonomy( $tax = '', $post_type = '' ) {
		if ( trx_addons_exists_tribe_events() && $post_type == Tribe__Events__Main::POSTTYPE ) {
			$tax = Tribe__Events__Main::TAXONOMY;
		}
		return $tax;
	}
}

if ( ! function_exists( 'trx_addons_events_get_blog_title' ) ) {
	add_filter( 'trx_addons_filter_get_blog_title', 'trx_addons_events_get_blog_title' );
	/**
	 * Return a title for the current events page
	 * 
	 * @hooked trx_addons_filter_get_blog_title
	 * 
	 * @trigger tribe_events_title
	 *
	 * @param string $title  Default title
	 * 
	 * @return string        A new title
	 */
	function trx_addons_events_get_blog_title( $title = '' ) {
		if ( trx_addons_is_tribe_events_page() ) {
			if ( function_exists( 'tribe_get_events_title' ) ) {
				if ( trx_addons_is_single() ) {
					global $wp_query;
					if ( ! empty( $wp_query->queried_object ) ) {
						$title = $wp_query->queried_object->post_title;
					}
				} else {
					$title = apply_filters( 'tribe_events_title', tribe_get_events_title( false ) );
				}
			}
		}
		return $title;
	}
}

if ( ! function_exists( 'trx_addons_events_get_blog_all_posts_link' ) ) {
	add_filter( 'trx_addons_filter_get_blog_all_posts_link', 'trx_addons_events_get_blog_all_posts_link', 10, 2 );
	/**
	 * Return a link to the all events page for the breadcrumbs
	 * 
	 * @hooked trx_addons_filter_get_blog_all_posts_link
	 * 
	 * @param string $link  Default link
	 * @param array  $args  Additional parameters
	 * 
	 * @return string       A new link
	 */
	function trx_addons_events_get_blog_all_posts_link( $link = '', $args = array() ) {
		if ( empty( $link ) && trx_addons_is_tribe_events_page() && trx_addons_is_singular( Tribe__Events__Main::POSTTYPE ) ) {
			if ( ( $url = get_post_type_archive_link( Tribe__Events__Main::POSTTYPE ) ) != '') {
				$obj = get_post_type_object(  Tribe__Events__Main::POSTTYPE );
				if ( is_object( $obj ) && ! empty( $obj->labels->all_items ) ) {
					$link = '<a href="' . esc_url( $url ) . '">' . esc_html( $obj->labels->all_items ) . '</a>';
				}
			}
		}
		return $link;
	}
}
	
if ( ! function_exists( 'trx_addons_events_google_maps_api' ) ) {
	add_filter('tribe_events_google_maps_api', 'trx_addons_events_google_maps_api' );
	/**
	 * Add Google API key (if specified on the plugin's options page) to the Tribe Events Google Maps API URL
	 * 
	 * @hooked tribe_events_google_maps_api
	 * 
	 * @param string $url  Google Maps API URL
	 * 
	 * @return string      Modified Google Maps API URL
	 */
	function trx_addons_events_google_maps_api( $url ) {
		$api_key = trx_addons_get_option( 'api_google' );
		if ( $api_key ) {
			$url = trx_addons_add_to_url( $url, array(
				'key' => $api_key
			) );
		}
		return $url;
	}
}
	
if ( ! function_exists( 'trx_addons_events_repair_spoofed_post' ) ) {
	add_action( 'wp_head', 'trx_addons_events_repair_spoofed_post', 101 );
	/**
	 * Repair the global variable $post after the Tribe Events spoofing it on priority 100!!!
	 * 
	 * @hooked wp_head
	 */
	function trx_addons_events_repair_spoofed_post() {

		if ( ! trx_addons_exists_tribe_events() ) {
			return;
		}

		// hijack this method right up front if it's a password protected post and the password isn't entered
		if ( trx_addons_is_single() && post_password_required() || is_feed() ) {
			return;
		}

		global $wp_query;
		if ( $wp_query->is_main_query() && tribe_is_event_query() && tribe_get_option( 'tribeEventsTemplate', 'default' ) != '' ) {
			if ( count( $wp_query->posts ) > 0 ) {
				$GLOBALS['post'] = $wp_query->posts[0];
			}
		}
	}
}

if ( ! function_exists( 'trx_addons_events_revslider_date' ) ) {
	add_filter( 'revslider_slide_setLayersByPostData_post', 'trx_addons_events_revslider_date', 10, 4 );
	/**
	 * Replace a post date with an event's date on RevSlider slides
	 * 
	 * @hooked revslider_slide_setLayersByPostData_post
	 * 
	 * @param array  $attr       Slide attributes
	 * @param array  $postData   Post data
	 * @param int    $sliderID   Slider ID
	 * @param object $sliderObj  Slider object
	 * 
	 * @return array             Modified slide attributes
	 */
	function trx_addons_events_revslider_date( $attr, $postData, $sliderID, $sliderObj ) {
		if ( trx_addons_exists_tribe_events()
			&& ! empty( $postData['ID'] )
			&& ! empty( $postData['post_type'] )
			&& $postData['post_type'] == Tribe__Events__Main::POSTTYPE
		) {
	        $attr['date_start'] = tribe_get_start_date( $postData['ID'], true, get_option( 'date_format' ) );
	        $attr['date_end'] = tribe_get_end_date( $postData['ID'], true, get_option( 'date_format' ) );
	        $attr['date'] = $attr['postDate'] = $attr['date_start'] . ' - ' . $attr['date_end'];
	    }
	    return $attr;
	}
}

if ( ! function_exists( 'trx_addons_events_create_empty_post_on_404' ) ) {
	add_action( 'wp_head', 'trx_addons_events_create_empty_post_on_404', 1 );
	/**
	 * Fix for Tribe Events: Create empty post on 404 page to prevent errors
	 * 
	 * @hooked wp_head
	 */
	function trx_addons_events_create_empty_post_on_404() {
		if ( trx_addons_exists_tribe_events() && is_404() && ! isset( $GLOBALS['post'] ) ) {
			$GLOBALS['post'] = new stdClass();
			$GLOBALS['post']->ID = 0;
			$GLOBALS['post']->post_type = 'unknown';
			$GLOBALS['post']->post_content = '';
		}
	}
}

if ( ! function_exists( 'trx_addons_events_fix_query_orderby_args' ) ) {
	add_filter( 'trx_addons_filter_add_sort_order_args', 'trx_addons_events_fix_query_orderby_args' );
	add_filter( 'trx_addons_filter_get_list_posts_args', 'trx_addons_events_fix_query_orderby_args' );
	/**
	 * Fix for Tribe Events: Replace an 'orderby' values 'none', 'title' in the query arguments
	 *                       to compatibility with Tribe Events Calendar v.6.0.2+
	 * 						 (a error message is appear about an undefined field name 'title' or 'none')
	 * 
	 * @hooked trx_addons_filter_add_sort_order_args
	 * @hooked trx_addons_filter_get_list_posts_args
	 * 
	 * @param array $args  Query args
	 * 
	 * @return array       Modified query args
	 */
	function trx_addons_events_fix_query_orderby_args( $args ) {
		$post_type = is_array( $args )
						? ( ! empty( $args['post_type'] ) ? $args['post_type'] : '' )
						: $args->get( 'post_type' );
		if ( trx_addons_exists_tribe_events() && $post_type == Tribe__Events__Main::POSTTYPE ) {
			$orderby = is_array( $args )
						? ( ! empty( $args['orderby'] ) ? $args['orderby'] : '' )
						: $args->get( 'orderby' );
			// Remove 'orderby' if it's equal to 'none'
			if ( $orderby == 'none' ) {
				if ( is_array($args) ) {
					unset( $args['orderby'] );
					unset( $args['order'] );
				} else {
					$args->set( 'orderby', '' );
				}

			// Replace 'title' with 'post_title'
			} else if ( $orderby == 'title' ) {
				if ( is_array($args) ) {
					$args['orderby'] = 'post_title';
				} else {
					$args->set( 'orderby', 'post_title' );
				}

			// Replace 'date' with 'post_date'
			} else if ( $orderby == 'date' ) {
				if ( is_array($args) ) {
					$args['orderby'] = 'post_date';
				} else {
					$args->set( 'orderby', 'post_date' );
				}
			}
		}
		return $args;
	}
}


// Fix: issue in The Events Calendar 5.0+: with a new design of the calendar (appears after the update 5.0)
//      any new queries before main posts loop breaks a calendar output.
//      For example: the page header uses widgets that display one or more posts.
//-------------------------------------------------------------------------------

if ( ! function_exists( 'trx_addons_events_fix_new_design_start' ) ) {
	add_action( 'trx_addons_action_before_show_layout', 'trx_addons_events_fix_new_design_start', 10, 4 );
	/**
	 * Fix for Tribe Events: If new (updated) view is used and a page template is not empty
	 * 						 ( not equal to 'Default Events Template' ) - remove Events Calendar handler from the filter 'loop_start'
	 * 						 before show a custom layout (it may contain shortcodes or widgets with a posts loop)
	 * 
	 * @hooked trx_addons_action_before_show_layout
	 * 
	 * @param string $layout_name  Layout name
	 * @param string $layout_type  Layout type
	 * @param string $template     Template name
	 * @param string $slug         Template slug
	 */
	function trx_addons_events_fix_new_design_start() {
		global $TRX_ADDONS_STORAGE;
		if ( ! isset( $TRX_ADDONS_STORAGE['events_show_layout_depth'] ) ) {
			$TRX_ADDONS_STORAGE['events_show_layout_depth'] = 1;
			if ( trx_addons_exists_tribe_events() ) {
				$opt = get_option( 'tribe_events_calendar_options' );
				// If new (updated) view is used and a page template is not empty ( not equal to 'Default Events Template' )
				if ( ! empty( $opt['views_v2_enabled'] ) && ! empty( $opt['tribeEventsTemplate'] ) ) {
					global $wp_filter;
					$TRX_ADDONS_STORAGE['events_filters'] = array(
						'loop_start' => array(
											'handler' => 'hijack_on_loop_start',
											'filters' => array()
											),
/*
						'the_post' => array(
											'handler' => 'hijack_the_post',
											'filters' => array()
											)
*/
					);
					foreach ( $TRX_ADDONS_STORAGE['events_filters'] as $f => $params ) {
						if ( ! empty( $wp_filter[ $f ] ) && ( is_array( $wp_filter[ $f ] ) || is_object( $wp_filter[ $f ] ) ) ) {
							foreach ( $wp_filter[ $f ] as $p => $cb ) {
								foreach ( $cb as $k => $v ) {
									if ( strpos( $k, $params['handler'] ) !== false ) {
										$TRX_ADDONS_STORAGE['events_filters'][$f]['filters'][$p] = array( $k => $v );
										remove_filter( $f, $v['function'], $p );
									}
								}
							}
						}
					}
				}
			}
		} else {
			$TRX_ADDONS_STORAGE['events_show_layout_depth']++;
		}
	}
}

if ( ! function_exists( 'trx_addons_events_fix_new_design_end' ) ) {
	add_action( 'trx_addons_action_after_show_layout', 'trx_addons_events_fix_new_design_end', 10, 4 );
	/**
	 * Fix for Tribe Events: Restore Events Calendar handler to the filter 'loop_start' after the custom layout is showed
	 * 
	 * @hooked trx_addons_action_after_show_layout
	 * 
	 * @param string $layout_name  Layout name
	 * @param string $layout_type  Layout type
	 * @param string $template     Template name
	 * @param string $slug         Template slug
	 */
	function trx_addons_events_fix_new_design_end() {
		global $TRX_ADDONS_STORAGE, $wp_filter;
		$TRX_ADDONS_STORAGE['events_show_layout_depth']--;
		if ( $TRX_ADDONS_STORAGE['events_show_layout_depth'] == 0 && isset( $TRX_ADDONS_STORAGE['events_filters'] ) && is_array( $TRX_ADDONS_STORAGE['events_filters'] ) ) {
			foreach ( $TRX_ADDONS_STORAGE['events_filters'] as $f => $params ) {
				if ( ! empty( $params['filters'] ) && is_array( $params['filters'] ) ) {
					foreach ( $params['filters'] as $p => $cb ) {
						foreach ( $cb as $k => $v ) {
							if ( ! isset( $wp_filter[ $f ][ $p ][ $k ] ) ) {
								add_filter( $f, $v['function'], $p, $v['accepted_args'] );
							}
						}
					}
				}
			}
			unset( $TRX_ADDONS_STORAGE['events_filters'] );
		}
	}
}


// Add shortcodes
//----------------------------------------------------------------------------

require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'the-events-calendar/the-events-calendar-sc.php';

// Add shortcodes to Elementor
if ( trx_addons_exists_tribe_events() && trx_addons_exists_elementor() && function_exists('trx_addons_elm_init') ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'the-events-calendar/the-events-calendar-sc-elementor.php';
}

// Add shortcodes to VC
if ( trx_addons_exists_tribe_events() && trx_addons_exists_vc() && function_exists( 'trx_addons_vc_add_id_param' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'the-events-calendar/the-events-calendar-sc-vc.php';
}


// Demo data install
//----------------------------------------------------------------------------

// One-click import support
if ( is_admin() ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'the-events-calendar/the-events-calendar-demo-importer.php';
}

// OCDI support
if ( is_admin() && trx_addons_exists_tribe_events() && function_exists( 'trx_addons_exists_ocdi' ) && trx_addons_exists_ocdi() ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'the-events-calendar/the-events-calendar-demo-ocdi.php';
}
