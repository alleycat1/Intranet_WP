<?php
/**
 * The template 'Style 2' to displaying related posts
 *
 * @package PUBZINNE
 * @since PUBZINNE 1.0
 */


$pubzinne_hover      = ! empty( $pubzinne_template_args['hover'] ) && ! pubzinne_is_inherit( $pubzinne_template_args['hover'] )
    ? $pubzinne_template_args['hover']
    : pubzinne_get_theme_option( 'image_hover' );

$pubzinne_template_args = get_query_var( 'pubzinne_template_args' );
$pubzinne_link        = get_permalink();
$pubzinne_post_format = get_post_format();
$pubzinne_post_format = empty( $pubzinne_post_format ) ? 'standard' : str_replace( 'post-format-', '', $pubzinne_post_format );
?><div id="post-<?php the_ID(); ?>" <?php post_class( 'related_item post_format_' . esc_attr( $pubzinne_post_format ) ); ?> data-post-id="<?php the_ID(); ?>">
	<?php
	pubzinne_show_post_featured(
		array(
			'thumb_size'    => apply_filters( 'pubzinne_filter_related_thumb_size', pubzinne_get_thumb_size( (int) pubzinne_get_theme_option( 'related_posts' ) == 1 ? 'huge' : 'big' ) ),
		)
	);
	?>
	<div class="post_header entry-header">
		<?php
		if ( in_array( get_post_type(), array( 'post', 'attachment' ) ) ) {
			?>
            <h6 class="post_title entry-title"><a href="<?php echo esc_url( $pubzinne_link ); ?>"><?php
                    if ( '' == get_the_title() ) {
                        esc_html_e( '- No title -', 'pubzinne' );
                    } else {
                        the_title();
                    }
                    ?></a></h6>
			<div class="post_meta">
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
				<a href="<?php echo esc_url( $pubzinne_link ); ?>" class="post_meta_item post_date"><?php echo wp_kses_data( pubzinne_get_date() ); ?></a>

			</div>
			<?php

            $pubzinne_template_args = is_array( $pubzinne_template_args ) ?  $pubzinne_template_args :  $pubzinne_template_args = array();
            $pubzinne_template_args['excerpt_length'] = 11;

            pubzinne_show_post_content( $pubzinne_template_args, '<div class="post_content_inner">', '</div>' );

		}
        ?>
	</div>
</div>
