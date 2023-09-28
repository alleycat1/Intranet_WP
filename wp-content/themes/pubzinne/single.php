<?php
/**
 * The template to display single post
 *
 * @package PUBZINNE
 * @since PUBZINNE 1.0
 */

// Full post loading
$full_post_loading        = pubzinne_get_value_gp( 'action' ) == 'full_post_loading';

// Prev post loading
$prev_post_loading        = pubzinne_get_value_gp( 'action' ) == 'prev_post_loading';
$prev_post_loading_type   = pubzinne_get_theme_option( 'posts_navigation_scroll_which_block' );

// Position of the related posts
$pubzinne_related_position = pubzinne_get_theme_option( 'related_position' );

// Type of the prev/next posts navigation
$pubzinne_posts_navigation = pubzinne_get_theme_option( 'posts_navigation' );
$pubzinne_prev_post        = false;

// Rewrite style of the single post if current post loading via AJAX and featured image and title is not in the content
if ( ( $full_post_loading 
		|| 
		( $prev_post_loading && 'article' == $prev_post_loading_type )
	) 
	&& 
	! in_array( pubzinne_get_theme_option( 'single_style' ), array( 'style-6' ) )
) {
	pubzinne_storage_set_array( 'options_meta', 'single_style', 'style-6' );
}

get_header();

while ( have_posts() ) {

	the_post();

	// Type of the prev/next posts navigation
	if ( 'scroll' == $pubzinne_posts_navigation ) {
		$pubzinne_prev_post = get_previous_post( true );         // Get post from same category
		if ( ! $pubzinne_prev_post ) {
			$pubzinne_prev_post = get_previous_post( false );    // Get post from any category
			if ( ! $pubzinne_prev_post ) {
				$pubzinne_posts_navigation = 'links';
			}
		}
	}

	// Override some theme options to display featured image, title and post meta in the dynamic loaded posts
	if ( $full_post_loading || ( $prev_post_loading && $pubzinne_prev_post ) ) {
		pubzinne_sc_layouts_showed( 'featured', false );
		pubzinne_sc_layouts_showed( 'title', false );
		pubzinne_sc_layouts_showed( 'postmeta', false );
	}

	// If related posts should be inside the content
	if ( strpos( $pubzinne_related_position, 'inside' ) === 0 ) {
		ob_start();
	}

	// Display post's content
	get_template_part( apply_filters( 'pubzinne_filter_get_template_part', 'templates/content', 'single-' . pubzinne_get_theme_option( 'single_style' ) ), 'single-' . pubzinne_get_theme_option( 'single_style' ) );

	// If related posts should be inside the content
	if ( strpos( $pubzinne_related_position, 'inside' ) === 0 ) {
		$pubzinne_content = ob_get_contents();
		ob_end_clean();

		ob_start();
		do_action( 'pubzinne_action_related_posts' );
		$pubzinne_related_content = ob_get_contents();
		ob_end_clean();

		$pubzinne_related_position_inside = max( 0, min( 9, pubzinne_get_theme_option( 'related_position_inside' ) ) );
		if ( 0 == $pubzinne_related_position_inside ) {
			$pubzinne_related_position_inside = mt_rand( 1, 9 );
		}

		$pubzinne_p_number = 0;
		$pubzinne_related_inserted = false;
		for ( $i = 0; $i < strlen( $pubzinne_content ) - 3; $i++ ) {
			if ( '<' == $pubzinne_content[ $i ] && 'p' == $pubzinne_content[ $i + 1 ] && in_array( $pubzinne_content[ $i + 2 ], array( '>', ' ' ) ) ) {
				$pubzinne_p_number++;
				if ( $pubzinne_related_position_inside == $pubzinne_p_number ) {
					$pubzinne_related_inserted = true;
					$pubzinne_content = ( $i > 0 ? substr( $pubzinne_content, 0, $i ) : '' )
										. $pubzinne_related_content
										. substr( $pubzinne_content, $i );
				}
			}
		}
		if ( ! $pubzinne_related_inserted ) {
			$pubzinne_content .= $pubzinne_related_content;
		}

		pubzinne_show_layout( $pubzinne_content );
	}

	// Comments
	do_action( 'pubzinne_action_before_comments' );
	comments_template();
	do_action( 'pubzinne_action_after_comments' );

	// Related posts
	if ( 'below_content' == $pubzinne_related_position
		&& ( 'scroll' != $pubzinne_posts_navigation || pubzinne_get_theme_option( 'posts_navigation_scroll_hide_related' ) == 0 )
		&& ( ! $full_post_loading || pubzinne_get_theme_option( 'open_full_post_hide_related' ) == 0 )
	) {
		do_action( 'pubzinne_action_related_posts' );
	}

	// Post navigation: type 'scroll'
	if ( 'scroll' == $pubzinne_posts_navigation && ! $full_post_loading ) {
		?>
		<div class="nav-links-single-scroll"
			data-post-id="<?php echo esc_attr( get_the_ID( $pubzinne_prev_post ) ); ?>"
			data-post-link="<?php echo esc_attr( get_permalink( $pubzinne_prev_post ) ); ?>"
			data-post-title="<?php the_title_attribute( array( 'post' => $pubzinne_prev_post ) ); ?>">
		</div>
		<?php
	}
}

get_footer();
