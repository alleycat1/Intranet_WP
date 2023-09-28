<?php
/**
 * Plugin support: Tour Master
 *
 * @package ThemeREX Addons
 * @since v1.6.38
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

// Define constants with plugin specific CPT and taxonomies
if ( ! defined( 'TRX_ADDONS_TOURMASTER_CPT_TOUR' ) )			define( 'TRX_ADDONS_TOURMASTER_CPT_TOUR', 			'tour' );
if ( ! defined( 'TRX_ADDONS_TOURMASTER_CPT_TOUR_COUPON' ) )		define( 'TRX_ADDONS_TOURMASTER_CPT_TOUR_COUPON',	'tour_coupon' );
if ( ! defined( 'TRX_ADDONS_TOURMASTER_CPT_TOUR_SERVICE' ) )	define( 'TRX_ADDONS_TOURMASTER_CPT_TOUR_SERVICE',	'tour_service' );
if ( ! defined( 'TRX_ADDONS_TOURMASTER_TAX_TOUR_CATEGORY' ) )	define( 'TRX_ADDONS_TOURMASTER_TAX_TOUR_CATEGORY',	'tour_category' );
if ( ! defined( 'TRX_ADDONS_TOURMASTER_TAX_TOUR_TAG' ) )		define( 'TRX_ADDONS_TOURMASTER_TAX_TOUR_TAG',		'tour_tag' );


if ( ! function_exists( 'trx_addons_exists_tourmaster' ) ) {
	/**
	 * Check if Tour Master plugin is installed and activated
	 *
	 * @return bool  True if plugin is installed and activated
	 */
	function trx_addons_exists_tourmaster() {
		return defined( 'TOURMASTER_LOCAL' );
	}
}

if ( ! function_exists( 'trx_addons_is_tourmaster_page' ) ) {
	/**
	 * Check if current page is any Tour Master page
	 *
	 * @return bool  True if current page is any Tour Master page
	 */
	function trx_addons_is_tourmaster_page() {
		$rez = false;
		if ( trx_addons_exists_tourmaster() ) {
			$rez = ( trx_addons_is_single() && in_array( get_query_var('post_type'), array(
																TRX_ADDONS_TOURMASTER_CPT_TOUR,
																TRX_ADDONS_TOURMASTER_CPT_TOUR_COUPON,
																TRX_ADDONS_TOURMASTER_CPT_TOUR_SERVICE
														) )
					)
					|| ( is_home() && isset( $_GET['tour-search'] ) )
					|| ( is_home() && isset( $_GET['tourmaster-payment'] ) )
					|| is_post_type_archive( TRX_ADDONS_TOURMASTER_CPT_TOUR ) 
					|| is_post_type_archive( TRX_ADDONS_TOURMASTER_CPT_TOUR_COUPON )
					|| is_post_type_archive( TRX_ADDONS_TOURMASTER_CPT_TOUR_SERVICE )
					|| is_tax( TRX_ADDONS_TOURMASTER_TAX_TOUR_CATEGORY )
					|| is_tax( TRX_ADDONS_TOURMASTER_TAX_TOUR_TAG );
		}
		return $rez;
	}
}

if ( ! function_exists( 'trx_addons_tourmaster_post_type_taxonomy' ) ) {
	add_filter( 'trx_addons_filter_post_type_taxonomy',	'trx_addons_tourmaster_post_type_taxonomy', 10, 2 );
	/**
	 * Return a 'main' taxonomy for the post type (this post_type have 2+ taxonomies)
	 * 
	 * @hooked trx_addons_filter_post_type_taxonomy
	 *
	 * @param string $tax        Taxonomy name
	 * @param string $post_type  Post type name
	 * 
	 * @return string            Taxonomy name
	 */
	function trx_addons_tourmaster_post_type_taxonomy($tax='', $post_type='') {
		if ( $post_type == TRX_ADDONS_TOURMASTER_CPT_TOUR ) {
			$tax = TRX_ADDONS_TOURMASTER_TAX_TOUR_CATEGORY;
		}
		return $tax;
	}
}

if ( ! function_exists( 'trx_addons_tourmaster_get_blog_all_posts_link' ) ) {
	add_filter( 'trx_addons_filter_get_blog_all_posts_link', 'trx_addons_tourmaster_get_blog_all_posts_link', 10, 2 );
	/**
	 * Return a link to the 'All tours' page for the breadcrumbs
	 *
	 * @hooked trx_addons_filter_get_blog_all_posts_link
	 *
	 * @param string $link  Link to the 'All posts' page
	 * @param array  $args  Additional arguments
	 * 
	 * @return string       Link to the 'All posts' page
	 */
	function trx_addons_tourmaster_get_blog_all_posts_link( $link = '', $args = array() ) {
		if ( empty( $link ) && trx_addons_is_tourmaster_page() ) {
			if ( ( $url = trx_addons_tourmaster_get_tours_page_link() ) != '' ) {
				$id = trx_addons_tourmaster_get_tours_page_id();
				$front_id = get_option( 'show_on_front' ) == 'page' ? (int)get_option( 'page_on_front' ) : 0;
				if ( $front_id == 0 || $id == 0 || $front_id != $id ) {
					$title = $id ? get_the_title( $id ) : __('Tours', 'trx_addons');
					$link = '<a href="' . esc_url( $url ) . '">' . esc_html( $title ) . '</a>';
				} else {
					$link = '#';	// To disable link
				}
			}
		}
		return $link;
	}
}

if ( ! function_exists( 'trx_addons_tourmaster_get_tours_page_id' ) ) {
	/**
	 * Return tours page ID
	 * 
	 * @trigger trx_addons_filter_get_all_posts_page_id
	 *
	 * @return int  Page ID
	 */
	function trx_addons_tourmaster_get_tours_page_id() {
		return apply_filters( 'trx_addons_filter_get_all_posts_page_id', 0, 'tourmaster' );
	}
}

if ( ! function_exists( 'trx_addons_tourmaster_get_tours_page_link' ) ) {
	/**
	 * Return tours page link
	 * 
	 * @return string  Page link
	 */
	function trx_addons_tourmaster_get_tours_page_link() {
		$id = trx_addons_tourmaster_get_tours_page_id();
		return $id > 0 ? get_permalink( $id ) : get_post_type_archive_link( TRX_ADDONS_TOURMASTER_CPT_TOUR );
	}
}

if ( ! function_exists( 'trx_addons_tourmaster_get_blog_title' ) ) {
	add_filter( 'trx_addons_filter_get_blog_title', 'trx_addons_tourmaster_get_blog_title' );
	/**
	 * Return a title of the current page (if this page is a tours page)
	 *
	 * @hooked trx_addons_filter_get_blog_title
	 *
	 * @param string $title  Blog title
	 * 
	 * @return string        Blog title
	 */
	function trx_addons_tourmaster_get_blog_title( $title = '' ) {
		if ( trx_addons_exists_tourmaster() ) {
			if ( is_post_type_archive( TRX_ADDONS_TOURMASTER_CPT_TOUR )
				|| is_post_type_archive( TRX_ADDONS_TOURMASTER_CPT_TOUR_SERVICE )
			) {
				$id = trx_addons_tourmaster_get_tours_page_id();
				$title = $id ? get_the_title( $id ) : __('Tours', 'trx_addons');
			} else if ( is_home() && isset( $_GET['tour-search'] ) ) {
				$title = __('Tour search', 'trx_addons');
			} else if ( is_home() && isset( $_GET['tourmaster-payment'] ) ) {
				$title = __('Tour payment', 'trx_addons');
			}
		}
		return $title;
	}
}


// Add shortcodes
//----------------------------------------------------------------------------

// Add shortcodes to Elementor
if ( trx_addons_exists_tourmaster() && trx_addons_exists_elementor() && function_exists('trx_addons_elm_init') ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'tourmaster/tourmaster-sc-elementor.php';
}

// Add shortcodes to VC
if ( trx_addons_exists_tourmaster() && trx_addons_exists_vc() && function_exists( 'trx_addons_vc_add_id_param' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'tourmaster/tourmaster-sc-vc.php';
}


// Demo data install
//----------------------------------------------------------------------------

// One-click import support
if ( is_admin() ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'tourmaster/tourmaster-demo-importer.php';
}

// OCDI support
if ( is_admin() && trx_addons_exists_tourmaster() && function_exists( 'trx_addons_exists_ocdi' ) && trx_addons_exists_ocdi() ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'tourmaster/tourmaster-demo-ocdi.php';
}
