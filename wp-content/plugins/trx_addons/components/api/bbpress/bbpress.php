<?php
/**
 * Plugin support: BBPress and BuddyPress
 *
 * @package ThemeREX Addons
 * @since v1.5
 */

if ( ! function_exists( 'trx_addons_exists_bbpress' ) ) {
	/**
	 * Check if at least one of plugin BBPress and/or BuddyPress exists and activated
	 *
	 * @return bool  True if at least one of plugins exists and activated
	 */
	function trx_addons_exists_bbpress() {
		return class_exists( 'BuddyPress' ) || class_exists( 'bbPress' );
	}
}

if ( ! function_exists( 'trx_addons_is_bbpress_page' ) ) {
	/**
	 * Check if any BBPress or BuddyPress page is displayed
	 *
	 * @return bool  True if any BBPress or BuddyPress page is displayed
	 */
	function trx_addons_is_bbpress_page() {
		$rez = false;
		if ( trx_addons_exists_bbpress() ) {
			if ( ! is_search() ) {
				$rez = ( function_exists('is_buddypress') && is_buddypress() ) 
					|| ( function_exists('is_bbpress') && is_bbpress() )
					|| ( ! is_user_logged_in() && in_array( get_query_var('post_type'), array('forum', 'topic', 'reply') ) );
			}
		}
		return $rez;
	}
}

if ( !function_exists( 'trx_addons_bbpress_get_blog_all_posts_link' ) ) {
	add_filter( 'trx_addons_filter_get_blog_all_posts_link', 'trx_addons_bbpress_get_blog_all_posts_link', 10, 2 );
	/**
	 * Add link to the 'All posts' to the breadcrumbs for the BBPress and BuddyPress pages
	 * 
	 * @hooked trx_addons_filter_get_blog_all_posts_link
	 *
	 * @param string $link  Link to the 'All posts' or empty string
	 * @param array $args   Arguments for the link. Not used here
	 * 
	 * @return string       Link to the 'All posts' for the BBPress and BuddyPress pages
	 */
	function trx_addons_bbpress_get_blog_all_posts_link( $link = '', $args = array() ) {
		if ( $link == '' && trx_addons_is_bbpress_page() && function_exists( 'bbp_get_forum_post_type' ) ) {
			// Page exists at root slug path, so use its permalink
			$page = bbp_get_page_by_path( bbp_get_root_slug() );
			$pt = bbp_get_forum_post_type();
			$obj = get_post_type_object( $pt );
			if ( ( $url = ! empty( $page ) ? get_permalink( $page->ID ) : get_post_type_archive_link( $pt ) ) != '' ) {
				$link = '<a href="' . esc_url( $url ) . '">' . esc_html( $obj->labels->all_items ) . '</a>';
			}
		}
		return $link;
	}
}

if ( ! function_exists( 'trx_addons_bbpress_post_type_taxonomy' ) ) {
	add_filter( 'trx_addons_filter_post_type_taxonomy',	'trx_addons_bbpress_post_type_taxonomy', 10, 2 );
	/**
	 * Return taxonomy 'topic_tag' as a main taxonomy for the post type 'topic'
	 * 
	 * @hooked trx_addons_filter_post_type_taxonomy
	 *
	 * @param string $tax        Taxonomy slug for filter
	 * @param string $post_type  Post type slug
	 * 
	 * @return string            Filtered taxonomy slug
	 */
	function trx_addons_bbpress_post_type_taxonomy( $tax = '', $post_type = '' ) {
		if ( trx_addons_exists_bbpress()
			&& function_exists( 'bbp_get_topic_post_type' )
			&& $post_type == bbp_get_topic_post_type()
			&& $tax == bbp_get_topic_tag_tax_id()
		) {
			//TODO: Check if it is correct? Maybe need to use 'topic_tag' instead of ''?
			$tax = '';
		}
		return $tax;
	}
}

if ( ! function_exists( 'trx_addons_bbpress_load_scripts_front' ) ) {
	add_action( "wp_enqueue_scripts", 'trx_addons_bbpress_load_scripts_front', TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY );
	add_action( 'trx_addons_action_pagebuilder_preview_scripts', 'trx_addons_bbpress_load_scripts_front', 10, 1 );
	/**
	 * Enqueue custom styles and scripts for the front mode for the BBPress and BuddyPress pages
	 * 
	 * @hooked wp_enqueue_scripts
	 * @hooked trx_addons_action_pagebuilder_preview_scripts
	 * 
	 * @trigger trx_addons_action_load_scripts_front
	 * 
	 * @param bool $force  Force enqueue scripts
	 */
	function trx_addons_bbpress_load_scripts_front( $force = false ) {
		if ( ! trx_addons_exists_bbpress() ) {
			return;
		}
		trx_addons_enqueue_optimized( 'bbpress', $force, array(
			'need' => trx_addons_is_bbpress_page(),
			'check' => array(
				// Forums
				array( 'type' => 'sc',  'sc' => 'bbp-forum-index' ),
				array( 'type' => 'sc',  'sc' => 'bbp-forum-form' ),
				array( 'type' => 'sc',  'sc' => 'bbp-single-forum' ),
				// Topics
				array( 'type' => 'sc',  'sc' => 'bbp-topic-index' ),
				array( 'type' => 'sc',  'sc' => 'bbp-topic-form' ),
				array( 'type' => 'sc',  'sc' => 'bbp-single-topic' ),
				// Topic tags
				array( 'type' => 'sc',  'sc' => 'bbp-topic-tags' ),
				array( 'type' => 'sc',  'sc' => 'bbp-single-tag' ),
				// Replies
				array( 'type' => 'sc',  'sc' => 'bbp-reply-form' ),
				array( 'type' => 'sc',  'sc' => 'bbp-single-reply' ),
				// Views
				array( 'type' => 'sc',  'sc' => 'bbp-single-view' ),
				// Search
				array( 'type' => 'sc',  'sc' => 'bbp-search-form' ),
				array( 'type' => 'sc',  'sc' => 'bbp-search' ),
				// Account
				array( 'type' => 'sc',  'sc' => 'bbp-login' ),
				array( 'type' => 'sc',  'sc' => 'bbp-register' ),
				array( 'type' => 'sc',  'sc' => 'bbp-lost-pass' ),
				// Others
				array( 'type' => 'sc',  'sc' => 'bbp-stats' ),
				array( 'type' => 'sc',  'sc' => 'bbp-' ),
				// Gutenberg blocks
				array( 'type' => 'gb',  'sc' => 'wp:bp/' ),
				array( 'type' => 'gb',  'sc' => 'wp:bbp/' ),
				// Elementor widgets
				array( 'type' => 'elm', 'sc' => '"widgetType":"wp-widget-bp' ),
				array( 'type' => 'elm', 'sc' => '"widgetType":"wp-widget-bbp' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[bbp-' ),
			)
		) );
	}
}

if ( ! function_exists( 'trx_addons_bbpress_check_in_html_output' ) ) {
//	add_filter( 'trx_addons_filter_get_menu_cache_html', 'trx_addons_bbpress_check_in_html_output', 10, 1 );
//	add_action( 'trx_addons_action_show_layout_from_cache', 'trx_addons_bbpress_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_check_page_content', 'trx_addons_bbpress_check_in_html_output', 10, 1 );
	/**
	 * Load styles and scripts if a plugin-specific output is present in the whole page output
	 * 
	 * @hooked trx_addons_action_check_page_content
	 *
	 * @param string $content  Content to check
	 * 
	 * @return string  	  Checked content
	 */
	function trx_addons_bbpress_check_in_html_output( $content = '' ) {
		if ( ! trx_addons_exists_bbpress() ) {
			return $content;
		}
		$args = array(
			'need'  => trx_addons_exists_bbpress() && function_exists( 'bbp_get_forum_post_type' ) && function_exists( 'bbp_get_topic_post_type' ),
			'check' => array(
				// BBPress
				'class=[\'"][^\'"]*(bbpress\\-|bbp_widget_|widget_display_)',
				'<(div|section|form|table|ul)[^>]*id=[\'"][^\'"]*bbpress',
				//BuddyPress
				'class=[\'"][^\'"]*(buddypress|widget\\-bp\\-core\\-)',
				'<(div|section|form|table|ul)[^>]*id=[\'"][^\'"]*buddypress',
			)
		);
		//Blogger with BP or BBP posts
		if ( function_exists( 'bbp_get_forum_post_type' ) && function_exists( 'bbp_get_topic_post_type' ) ) {
			$args['check'][] = 'class=[\'"][^\'"]*type\\-(' . bbp_get_forum_post_type() . '|' . bbp_get_topic_post_type() . ')';
		}
		if ( trx_addons_check_in_html_output( 'bbpress', $content, $args ) ) {
			trx_addons_bbpress_load_scripts_front( true );
		}
		return $content;
	}
}

if ( ! function_exists( 'trx_addons_bbpress_filter_head_output' ) ) {
	add_filter( 'trx_addons_filter_page_head', 'trx_addons_bbpress_filter_head_output', 10, 1 );
	/**
	 * Remove plugin-specific styles if present in the page head output
	 * if CSS optimization is 'full' and plugin is not used on the current page
	 * 
	 * @hooked trx_addons_filter_page_head
	 *
	 * @param string $content  Page head output
	 * 
	 * @return string  	  Modified page head output
	 */
	function trx_addons_bbpress_filter_head_output( $content = '' ) {
		if ( ! trx_addons_exists_bbpress() ) {
			return $content;
		}
		return trx_addons_filter_head_output( 'bbpress', $content, array(
			'check' => array(
				'#<link[^>]*href=[\'"][^\'"]*/bbpress/[^>]*>#',
				'#<link[^>]*href=[\'"][^\'"]*/buddypress/[^>]*>#'
			)
		) );
	}
}

if ( ! function_exists( 'trx_addons_bbpress_filter_body_output' ) ) {
	add_filter( 'trx_addons_filter_page_content', 'trx_addons_bbpress_filter_body_output', 10, 1 );
	/**
	 * Remove plugin-specific styles and scripts if present in the page body output
	 * if CSS optimization is 'full' and plugin is not used on the current page
	 * 
	 * @hooked trx_addons_filter_page_content
	 *
	 * @param string $content  Page body output
	 * 
	 * @return string  	  Modified page body output
	 */
	function trx_addons_bbpress_filter_body_output( $content = '' ) {
		if ( ! trx_addons_exists_bbpress() ) {
			return $content;
		}
		return trx_addons_filter_body_output( 'bbpress', $content, array(
			'check' => array(
				'#<link[^>]*href=[\'"][^\'"]*/bbpress/[^>]*>#',
				'#<script[^>]*src=[\'"][^\'"]*/bbpress/[^>]*>[\\s\\S]*</script>#U',
				'#<link[^>]*href=[\'"][^\'"]*/buddypress/[^>]*>#',
				'#<script[^>]*src=[\'"][^\'"]*/buddypress/[^>]*>[\\s\\S]*</script>#U',
			)
		) );
	}
}


// Demo data install
//----------------------------------------------------------------------------

// One-click import support
if ( is_admin() ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'bbpress/bbpress-demo-importer.php';
}

// OCDI support
if ( is_admin() && trx_addons_exists_bbpress() && function_exists( 'trx_addons_exists_ocdi' ) && trx_addons_exists_ocdi() ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'bbpress/bbpress-demo-ocdi.php';
}
