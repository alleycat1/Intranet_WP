<?php
/**
 * The Front Page template file.
 *
 * @package PUBZINNE
 * @since PUBZINNE 1.0.31
 */

get_header();

// If front-page is a static page
if ( get_option( 'show_on_front' ) == 'page' ) {

	// If Front Page Builder is enabled - display sections
	if ( pubzinne_is_on( pubzinne_get_theme_option( 'front_page_enabled' ) ) ) {

		if ( have_posts() ) {
			the_post();
		}

		$pubzinne_sections = pubzinne_array_get_keys_by_value( pubzinne_get_theme_option( 'front_page_sections' ) );
		if ( is_array( $pubzinne_sections ) ) {
			foreach ( $pubzinne_sections as $pubzinne_section ) {
				get_template_part( apply_filters( 'pubzinne_filter_get_template_part', 'front-page/section', $pubzinne_section ), $pubzinne_section );
			}
		}

		// Else if this page is blog archive
	} elseif ( is_page_template( 'blog.php' ) ) {
		get_template_part( apply_filters( 'pubzinne_filter_get_template_part', 'blog' ) );

		// Else - display native page content
	} else {
		get_template_part( apply_filters( 'pubzinne_filter_get_template_part', 'page' ) );
	}

	// Else get index template to show posts
} else {
	get_template_part( apply_filters( 'pubzinne_filter_get_template_part', 'index' ) );
}

get_footer();
