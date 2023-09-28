<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: //codex.wordpress.org/Template_Hierarchy
 *
 * @package PUBZINNE
 * @since PUBZINNE 1.0
 */

$pubzinne_template = apply_filters( 'pubzinne_filter_get_template_part', pubzinne_blog_archive_get_template() );

if ( ! empty( $pubzinne_template ) && 'index' != $pubzinne_template ) {

	get_template_part( $pubzinne_template );

} else {

	pubzinne_storage_set( 'blog_archive', true );

	get_header();

	if ( have_posts() ) {

		// Query params
		$pubzinne_stickies  = is_home() ? get_option( 'sticky_posts' ) : false;
		$pubzinne_post_type = pubzinne_get_theme_option( 'post_type' );
		$pubzinne_args      = array(
								'blog_style'     => pubzinne_get_theme_option( 'blog_style' ),
								'post_type'      => $pubzinne_post_type,
								'taxonomy'       => pubzinne_get_post_type_taxonomy( $pubzinne_post_type ),
								'parent_cat'     => pubzinne_get_theme_option( 'parent_cat' ),
								'posts_per_page' => pubzinne_get_theme_option( 'posts_per_page' ),
								'sticky'         => pubzinne_get_theme_option( 'sticky_style' ) == 'columns'
															&& is_array( $pubzinne_stickies )
															&& count( $pubzinne_stickies ) > 0
															&& get_query_var( 'paged' ) < 1
								);

		pubzinne_blog_archive_start();

		do_action( 'pubzinne_action_blog_archive_start' );

		if ( is_author() ) {
			do_action( 'pubzinne_action_before_page_author' );
			get_template_part( apply_filters( 'pubzinne_filter_get_template_part', 'templates/author-page' ) );
			do_action( 'pubzinne_action_after_page_author' );
		}

		if ( pubzinne_get_theme_option( 'show_filters' ) ) {
			do_action( 'pubzinne_action_before_page_filters' );
			pubzinne_show_filters( $pubzinne_args );
			do_action( 'pubzinne_action_after_page_filters' );
		} else {
			do_action( 'pubzinne_action_before_page_posts' );
			pubzinne_show_posts( array_merge( $pubzinne_args, array( 'cat' => $pubzinne_args['parent_cat'] ) ) );
			do_action( 'pubzinne_action_after_page_posts' );
		}

		do_action( 'pubzinne_action_blog_archive_end' );

		pubzinne_blog_archive_end();

	} else {

		if ( is_search() ) {
			get_template_part( apply_filters( 'pubzinne_filter_get_template_part', 'templates/content', 'none-search' ), 'none-search' );
		} else {
			get_template_part( apply_filters( 'pubzinne_filter_get_template_part', 'templates/content', 'none-archive' ), 'none-archive' );
		}
	}

	get_footer();
}
