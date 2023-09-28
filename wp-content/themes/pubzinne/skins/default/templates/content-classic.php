<?php
/**
 * The Classic template to display the content
 *
 * Used for index/archive/search.
 *
 * @package PUBZINNE
 * @since PUBZINNE 1.0
 */

$pubzinne_template_args = get_query_var( 'pubzinne_template_args' );

if ( is_array( $pubzinne_template_args ) ) {
	$pubzinne_columns    = empty( $pubzinne_template_args['columns'] ) ? 2 : max( 1, $pubzinne_template_args['columns'] );
	$pubzinne_blog_style = array( $pubzinne_template_args['type'], $pubzinne_columns );
} else {
	$pubzinne_blog_style = explode( '_', pubzinne_get_theme_option( 'blog_style' ) );
	$pubzinne_columns    = empty( $pubzinne_blog_style[1] ) ? 2 : max( 1, $pubzinne_blog_style[1] );
}
$pubzinne_expanded   = ! pubzinne_sidebar_present() && pubzinne_get_theme_option( 'expand_content' ) == 'expand';

$pubzinne_post_format = get_post_format();
$pubzinne_post_format = empty( $pubzinne_post_format ) ? 'standard' : str_replace( 'post-format-', '', $pubzinne_post_format );

?><div class="<?php
	if ( ! empty( $pubzinne_template_args['slider'] ) ) {
		echo ' slider-slide swiper-slide';
	} else {
		echo ( pubzinne_is_blog_style_use_masonry( $pubzinne_blog_style[0] ) ? 'masonry_item masonry_item' : 'column' ) . '-1_' . esc_attr( $pubzinne_columns );
	}
?>"><article id="post-<?php the_ID(); ?>" data-post-id="<?php the_ID(); ?>"
	<?php
	post_class(
		'post_item post_item_container post_format_' . esc_attr( $pubzinne_post_format )
				. ' post_layout_classic post_layout_classic_' . esc_attr( $pubzinne_columns )
				. ' post_layout_' . esc_attr( $pubzinne_blog_style[0] )
				. ' post_layout_' . esc_attr( $pubzinne_blog_style[0] ) . '_' . esc_attr( $pubzinne_columns )
	);
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
								: explode( ',', $pubzinne_template_args['meta_parts'] )
								)
							: pubzinne_array_get_keys_by_value( pubzinne_get_theme_option( 'meta_parts' ) );

	pubzinne_show_post_featured(
		array(
			'thumb_size' => pubzinne_get_thumb_size(
				'classic' == $pubzinne_blog_style[0]
						? ( strpos( pubzinne_get_theme_option( 'body_style' ), 'full' ) !== false
								? ( $pubzinne_columns > 2 ? 'big' : 'huge' )
								: ( $pubzinne_columns > 2
									? ( $pubzinne_expanded ? 'med' : 'small' )
									: ( $pubzinne_expanded ? 'big' : 'med' )
									)
							)
						: ( strpos( pubzinne_get_theme_option( 'body_style' ), 'full' ) !== false
								? ( $pubzinne_columns > 2 ? 'masonry-big' : 'full' )
								: ( $pubzinne_columns <= 2 && $pubzinne_expanded ? 'masonry-big' : 'masonry' )
							)
			),
			'hover'      => $pubzinne_hover,
			'meta_parts' => $pubzinne_components,
			'no_links'   => ! empty( $pubzinne_template_args['no_links'] ),
		)
	);

	// Title and post meta
	$pubzinne_show_title = get_the_title() != '';
	$pubzinne_show_meta  = count( $pubzinne_components ) > 0 && ! in_array( $pubzinne_hover, array( 'border', 'pull', 'slide', 'fade', 'info' ) );

	if ( $pubzinne_show_title && $pubzinne_post_format != 'quote' ) {

		?>
		<div class="post_header entry-header">
			<?php
			// Post title
            if ($pubzinne_post_format != 'audio') {
                if (apply_filters('pubzinne_filter_show_blog_title', true, 'classic')) {
                    do_action('pubzinne_action_before_post_title');
                    if (empty($pubzinne_template_args['no_links'])) {
                        the_title(sprintf('<h4 class="post_title entry-title"><a href="%s" rel="bookmark">', esc_url(get_permalink())), '</a></h4>');
                    } else {
                        the_title('<h4 class="post_title entry-title">', '</h4>');
                    }
                    do_action('pubzinne_action_after_post_title');
                }
            }
            // Categories
            if ( apply_filters( 'pubzinne_filter_show_blog_categories', $pubzinne_show_meta && in_array( 'categories', $pubzinne_components ), array( 'categories' ), 'classic' ) ) {
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

            // Post meta
            if ( apply_filters( 'pubzinne_filter_show_blog_meta', $pubzinne_show_meta, $pubzinne_components, 'classic' ) ) {
                if ( count( $pubzinne_components ) > 0 ) {
                    do_action( 'pubzinne_action_before_post_meta' );
                    pubzinne_show_post_meta(
                        apply_filters(
                            'pubzinne_filter_post_meta_args', array(
                            'components' => join( ',', $pubzinne_components ),
                            'seo'        => false,
                            'echo'       => true,
                        ), $pubzinne_blog_style[0], $pubzinne_columns
                        )
                    );
                    do_action( 'pubzinne_action_after_post_meta' );
                }
            }

			?>
		</div><!-- .entry-header -->
		<?php
	}

	// Post content
    if ($pubzinne_post_format != 'audio') {
        ob_start();
            if (apply_filters('pubzinne_filter_show_blog_excerpt', empty($pubzinne_template_args['hide_excerpt']) && pubzinne_get_theme_option('excerpt_length') > 0, 'classic')) {
                pubzinne_show_post_content($pubzinne_template_args, '<div class="post_content_inner">', '</div>');
            }
        $pubzinne_content = ob_get_contents();
        ob_end_clean();

        pubzinne_show_layout($pubzinne_content, '<div class="post_content entry-content">', '</div><!-- .entry-content -->');
    }
    if ( $pubzinne_post_format == 'quote'){
        ?>
        <div class="quote_meta">
            <?php
            // Categories
            if ( apply_filters( 'pubzinne_filter_show_blog_categories', $pubzinne_show_meta && in_array( 'categories', $pubzinne_components ), array( 'categories' ), 'excerpt' ) ) {
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
            // Post meta
            if ( apply_filters( 'pubzinne_filter_show_blog_meta', $pubzinne_show_meta, $pubzinne_components, 'excerpt' ) ) {
                if ( count( $pubzinne_components ) > 0 ) {
                    do_action( 'pubzinne_action_before_post_meta' );
                    pubzinne_show_post_meta(
                        apply_filters(
                            'pubzinne_filter_post_meta_args', array(
                            'components' => join( ',', $pubzinne_components ),
                            'seo'        => false,
                            'echo'       => true,
                        ), 'excerpt', 1
                        )
                    );
                    do_action( 'pubzinne_action_after_post_meta' );
                }
            }
            ?>
        </div>
        <?php
    }
	?>

</article></div><?php
// Need opening PHP-tag above, because <div> is a inline-block element (used as column)!
