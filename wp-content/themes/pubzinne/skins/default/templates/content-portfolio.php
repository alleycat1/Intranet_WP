<?php
/**
 * The Portfolio template to display the content
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

$pubzinne_post_format = get_post_format();
$pubzinne_post_format = empty( $pubzinne_post_format ) ? 'standard' : str_replace( 'post-format-', '', $pubzinne_post_format );

?><div class="
<?php
if ( ! empty( $pubzinne_template_args['slider'] ) ) {
	echo ' slider-slide swiper-slide';
} else {
	echo ( pubzinne_is_blog_style_use_masonry( $pubzinne_blog_style[0] ) ? 'masonry_item masonry_item' : 'column' ) . '-1_' . esc_attr( $pubzinne_columns );
}
?>
"><article id="post-<?php the_ID(); ?>" 
	<?php
	post_class(
		'post_item post_item_container post_format_' . esc_attr( $pubzinne_post_format )
		. ' post_layout_portfolio'
		. ' post_layout_portfolio_' . esc_attr( $pubzinne_columns )
		. ( 'portfolio' != $pubzinne_blog_style[0] ? ' ' . esc_attr( $pubzinne_blog_style[0] )  . '_' . esc_attr( $pubzinne_columns ) : '' )
		. ( is_sticky() && ! is_paged() ? ' sticky' : '' )
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

	$pubzinne_hover   = ! empty( $pubzinne_template_args['hover'] ) && ! pubzinne_is_inherit( $pubzinne_template_args['hover'] )
								? $pubzinne_template_args['hover']
								: pubzinne_get_theme_option( 'image_hover' );

	
	// Meta parts
	$pubzinne_components = ! empty( $pubzinne_template_args['meta_parts'] )
							? ( is_array( $pubzinne_template_args['meta_parts'] )
								? $pubzinne_template_args['meta_parts']
								: explode( ',', $pubzinne_template_args['meta_parts'] )
								)
							: pubzinne_array_get_keys_by_value( pubzinne_get_theme_option( 'meta_parts' ) );

	// Featured image
    $pubzinne_hover = 'dots';
    if ( 'dots' == $pubzinne_hover ) {
        $pubzinne_post_link = empty( $pubzinne_template_args['no_links'] )
            ? ( ! empty( $pubzinne_template_args['link'] )
                ? $pubzinne_template_args['link']
                : get_permalink()
            )
            : '';
        $pubzinne_target    = ! empty( $pubzinne_post_link ) && false === strpos( $pubzinne_post_link, home_url() )
            ? ' target="_blank" rel="nofollow"'
            : '';
    }
	pubzinne_show_post_featured(
		array(
			'hover'         => 'dots',
			'no_links'      => ! empty( $pubzinne_template_args['no_links'] ),
			'thumb_size'    => pubzinne_get_thumb_size(
									pubzinne_is_blog_style_use_masonry( $pubzinne_blog_style[0] )
										? (	strpos( pubzinne_get_theme_option( 'body_style' ), 'full' ) !== false || $pubzinne_columns < 3
											? 'masonry-big'
											: 'masonry'
											)
										: (	strpos( pubzinne_get_theme_option( 'body_style' ), 'full' ) !== false || $pubzinne_columns < 3
											? 'big'
											: 'med'
											)
								),
			'show_no_image' => true,
			'meta_parts'    => $pubzinne_components,
			'class'         => 'dots' == $pubzinne_hover ? 'hover_with_info' : '',
			'post_info'     => 'dots' == $pubzinne_hover
										? '<div class="post_info"><h5 class="post_title">'
											. ( ! empty( $pubzinne_post_link )
												? '<a href="' . esc_url( $pubzinne_post_link ) . '"' . ( ! empty( $target ) ? $target : '' ) . '>'
												: ''
												)
												. esc_html( get_the_title() ) 
											. ( ! empty( $pubzinne_post_link )
												? '</a>'
												: ''
												)
											. '</h5></div>'
										: '',
		)
	);
	?>
</article></div><?php
// Need opening PHP-tag above, because <article> is a inline-block element (used as column)!