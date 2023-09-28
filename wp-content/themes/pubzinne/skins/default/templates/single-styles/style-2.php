<?php
/**
 * The "Style 2" template to display the post header of the single post or attachment:
 * featured image placed in the post header and title placed inside content
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
			echo ' with_featured_image';
		}
	?>">
		<?php
		// Featured image
		pubzinne_show_post_featured_image( array(
			'thumb_bg' => true,
		) );
		?>
	</div>
	<?php
	$pubzinne_post_header = ob_get_contents();
	ob_end_clean();
        if ( strpos( $pubzinne_post_header, 'post_featured' ) !== false) {
            do_action( 'pubzinne_action_before_post_header' );
            pubzinne_show_layout( $pubzinne_post_header );
            do_action( 'pubzinne_action_after_post_header' );
	}
}
