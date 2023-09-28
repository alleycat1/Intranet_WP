<?php
/**
 * The "Style 7" template to display the post header of the single post or attachment:
 * featured image and title are placed in the fullscreen post header, meta is inside the content
 *
 * @package PUBZINNE
 * @since PUBZINNE 1.75.0
 */

if ( is_singular( 'post' ) || is_singular( 'attachment' ) ) {
	ob_start();
	?>
	<div class="post_header_wrap post_header_wrap_in_header post_header_wrap_style_<?php
		echo esc_attr( pubzinne_get_theme_option( 'single_style' ) );
		if ( has_post_thumbnail() || str_replace( 'post-format-', '', get_post_format() ) == 'image' ) {
			echo ' with_featured_image pubzinne-full-height';
		}
	?>">
		<?php
		// Post title
		pubzinne_show_post_title_and_meta( array( 
			'show_meta' => false,
		) );
		// Featured image
		pubzinne_show_post_featured_image( array(
			'thumb_bg'  => true,
		) );
		?>
	</div>
	<?php
	$pubzinne_post_header = ob_get_contents();
	ob_end_clean();
	if ( strpos( $pubzinne_post_header, 'post_featured' ) !== false|| strpos( $pubzinne_post_header, 'post_title' ) !== false ) {
		do_action( 'pubzinne_action_before_post_header' );
		pubzinne_show_layout( $pubzinne_post_header );
		do_action( 'pubzinne_action_after_post_header' );
	}
}
