<?php
/**
 * The Sticky template to display the sticky posts
 *
 * Used for index/archive
 *
 * @package PUBZINNE
 * @since PUBZINNE 1.0
 */

$pubzinne_columns     = max( 1, min( 3, count( get_option( 'sticky_posts' ) ) ) );
$pubzinne_post_format = get_post_format();
$pubzinne_post_format = empty( $pubzinne_post_format ) ? 'standard' : str_replace( 'post-format-', '', $pubzinne_post_format );

?><div class="column-1_<?php echo esc_attr( $pubzinne_columns ); ?>"><article id="post-<?php the_ID(); ?>" 
	<?php
	post_class( 'post_item post_layout_sticky post_format_' . esc_attr( $pubzinne_post_format ) );
	pubzinne_add_blog_animation( $pubzinne_template_args );
	?>
>

	<?php
	if ( is_sticky() && is_home() && ! is_paged() ) {
		?>
		<span class="post_label label_sticky"></span>
		<?php
	}

	// Featured image
	pubzinne_show_post_featured(
		array(
			'thumb_size' => pubzinne_get_thumb_size( 1 == $pubzinne_columns ? 'big' : ( 2 == $pubzinne_columns ? 'med' : 'avatar' ) ),
		)
	);

	if ( ! in_array( $pubzinne_post_format, array( 'link', 'aside', 'status', 'quote' ) ) ) {
		?>
		<div class="post_header entry-header">
			<?php
			// Post title
			the_title( sprintf( '<h6 class="post_title entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h6>' );
			// Post meta
			pubzinne_show_post_meta( apply_filters( 'pubzinne_filter_post_meta_args', array(), 'sticky', $pubzinne_columns ) );
			?>
		</div><!-- .entry-header -->
		<?php
	}
	?>
</article></div><?php

// div.column-1_X is a inline-block and new lines and spaces after it are forbidden
