<?php
/**
 * 'Band' template to display the content
 *
 * Used for index/archive/search.
 *
 * @package PUBZINNE
 * @since PUBZINNE 1.71.0
 */

$pubzinne_template_args = get_query_var( 'pubzinne_template_args' );

$pubzinne_columns       = 1;

$pubzinne_expanded      = ! pubzinne_sidebar_present() && pubzinne_get_theme_option( 'expand_content' ) == 'expand';

$pubzinne_post_format   = get_post_format();
$pubzinne_post_format   = empty( $pubzinne_post_format ) ? 'standard' : str_replace( 'post-format-', '', $pubzinne_post_format );

if ( is_array( $pubzinne_template_args ) ) {
	$pubzinne_columns    = empty( $pubzinne_template_args['columns'] ) ? 1 : max( 1, $pubzinne_template_args['columns'] );
	$pubzinne_blog_style = array( $pubzinne_template_args['type'], $pubzinne_columns );
	if ( ! empty( $pubzinne_template_args['slider'] ) ) {
		?><div class="slider-slide swiper-slide">
		<?php
	} elseif ( $pubzinne_columns > 1 ) {
		?>
		<div class="column-1_<?php echo esc_attr( $pubzinne_columns ); ?>">
		<?php
	}
}
?>
<article id="post-<?php the_ID(); ?>" data-post-id="<?php the_ID(); ?>"
	<?php
	post_class( 'post_item post_item_container post_layout_band post_format_' . esc_attr( $pubzinne_post_format ) );
	pubzinne_add_blog_animation( $pubzinne_template_args );
	?>
>
	<?php

	// Sticky label
	if ( is_sticky() && ! is_paged() ) {
		?>
		<span class="post_label label_sticky"></span>
		<?php
	}

	// Featured image
	$pubzinne_hover      = ! empty( $pubzinne_template_args['hover'] ) && ! pubzinne_is_inherit( $pubzinne_template_args['hover'] )
							? $pubzinne_template_args['hover']
							: pubzinne_get_theme_option( 'image_hover' );
	$pubzinne_components = ! empty( $pubzinne_template_args['meta_parts'] )
							? ( is_array( $pubzinne_template_args['meta_parts'] )
								? $pubzinne_template_args['meta_parts']
								: array_map( 'trim', explode( ',', $pubzinne_template_args['meta_parts'] ) )
								)
							: pubzinne_array_get_keys_by_value( pubzinne_get_theme_option( 'meta_parts' ) );
	pubzinne_show_post_featured(
		array(
			'no_links'   => ! empty( $pubzinne_template_args['no_links'] ),
			'hover'      => $pubzinne_hover,
			'meta_parts' => $pubzinne_components,
			'thumb_bg'   => true,
			'thumb_size' => pubzinne_get_thumb_size( 
								in_array( $pubzinne_post_format, array( 'gallery', 'audio', 'video' ) )
									? ( strpos( pubzinne_get_theme_option( 'body_style' ), 'full' ) !== false
										? 'full'
										: ( $pubzinne_expanded 
											? 'big' 
											: 'med'
											)
										)
									: 'masonry-big'
								)
		)
	);

	?><div class="post_content_wrap"><?php

		// Title and post meta
		$pubzinne_show_title = get_the_title() != '';
		$pubzinne_show_meta  = count( $pubzinne_components ) > 0 && ! in_array( $pubzinne_hover, array( 'border', 'pull', 'slide', 'fade', 'info' ) );
		if ( $pubzinne_show_title ) {
			?>
			<div class="post_header entry-header">
				<?php
				// Categories
				if ( apply_filters( 'pubzinne_filter_show_blog_categories', $pubzinne_show_meta && in_array( 'categories', $pubzinne_components ), array( 'categories' ), 'band' ) ) {
					do_action( 'pubzinne_action_before_post_category' );
					?>
					<div class="post_category">
						<?php
						pubzinne_show_post_meta( apply_filters(
															'pubzinne_filter_post_meta_args',
															array(
																'components' => 'categories',
																'seo'        => false,
																'echo'       => true,
																),
															'hover_' . $pubzinne_hover, 1
															)
											);
						?>
					</div>
					<?php
					$pubzinne_components = pubzinne_array_delete_by_value( $pubzinne_components, 'categories' );
					do_action( 'pubzinne_action_after_post_category' );
				}
				// Post title
				if ( apply_filters( 'pubzinne_filter_show_blog_title', true, 'band' ) ) {
					do_action( 'pubzinne_action_before_post_title' );
					if ( empty( $pubzinne_template_args['no_links'] ) ) {
						the_title( sprintf( '<h4 class="post_title entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h4>' );
					} else {
						the_title( '<h4 class="post_title entry-title">', '</h4>' );
					}
					do_action( 'pubzinne_action_after_post_title' );
				}
				?>
			</div><!-- .post_header -->
			<?php
		}

		// Post content
		if ( ! isset( $pubzinne_template_args['excerpt_length'] ) && ! in_array( $pubzinne_post_format, array( 'gallery', 'audio', 'video' ) ) ) {
			$pubzinne_template_args['excerpt_length'] = 30;
		}
		if ( apply_filters( 'pubzinne_filter_show_blog_excerpt', empty( $pubzinne_template_args['hide_excerpt'] ) && pubzinne_get_theme_option( 'excerpt_length' ) > 0, 'band' ) ) {
			?>
			<div class="post_content entry-content">
				<?php
				// Post content area
				pubzinne_show_post_content( $pubzinne_template_args, '<div class="post_content_inner">', '</div>' );
				?>
			</div><!-- .entry-content -->
			<?php
		}
		// Post meta
		if ( apply_filters( 'pubzinne_filter_show_blog_meta', $pubzinne_show_meta, $pubzinne_components, 'band' ) ) {
			if ( count( $pubzinne_components ) > 0 ) {
				do_action( 'pubzinne_action_before_post_meta' );
				pubzinne_show_post_meta(
					apply_filters(
						'pubzinne_filter_post_meta_args', array(
							'components' => join( ',', $pubzinne_components ),
							'seo'        => false,
							'echo'       => true,
						), 'band', 1
					)
				);
				do_action( 'pubzinne_action_after_post_meta' );
			}
		}
		// More button
		if ( apply_filters( 'pubzinne_filter_show_blog_readmore', ! $pubzinne_show_title, 'band' ) ) {
			if ( empty( $pubzinne_template_args['no_links'] ) ) {
				do_action( 'pubzinne_action_before_post_readmore' );
				pubzinne_show_post_more_link( $pubzinne_template_args, '<p>', '</p>' );
				do_action( 'pubzinne_action_after_post_readmore' );
			}
		}
		?>
	</div>
</article>
<?php

if ( is_array( $pubzinne_template_args ) ) {
	if ( ! empty( $pubzinne_template_args['slider'] ) || $pubzinne_columns > 1 ) {
		?>
		</div>
		<?php
	}
}
