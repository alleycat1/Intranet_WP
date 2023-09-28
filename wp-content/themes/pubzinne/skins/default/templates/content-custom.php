<?php
/**
 * The custom template to display the content
 *
 * Used for index/archive/search.
 *
 * @package PUBZINNE
 * @since PUBZINNE 1.0.50
 */

$pubzinne_template_args = get_query_var( 'pubzinne_template_args' );
if ( is_array( $pubzinne_template_args ) ) {
	$pubzinne_columns    = empty( $pubzinne_template_args['columns'] ) ? 2 : max( 1, $pubzinne_template_args['columns'] );
	$pubzinne_blog_style = array( $pubzinne_template_args['type'], $pubzinne_columns );
} else {
	$pubzinne_blog_style = explode( '_', pubzinne_get_theme_option( 'blog_style' ) );
	$pubzinne_columns    = empty( $pubzinne_blog_style[1] ) ? 2 : max( 1, $pubzinne_blog_style[1] );
}
$pubzinne_blog_id       = pubzinne_get_custom_blog_id( join( '_', $pubzinne_blog_style ) );
$pubzinne_blog_style[0] = str_replace( 'blog-custom-', '', $pubzinne_blog_style[0] );
$pubzinne_expanded      = ! pubzinne_sidebar_present() && pubzinne_get_theme_option( 'expand_content' ) == 'expand';
$pubzinne_components    = ! empty( $pubzinne_template_args['meta_parts'] )
							? ( is_array( $pubzinne_template_args['meta_parts'] )
								? join( ',', $pubzinne_template_args['meta_parts'] )
								: $pubzinne_template_args['meta_parts']
								)
							: pubzinne_array_get_keys_by_value( pubzinne_get_theme_option( 'meta_parts' ) );
$pubzinne_post_format   = get_post_format();
$pubzinne_post_format   = empty( $pubzinne_post_format ) ? 'standard' : str_replace( 'post-format-', '', $pubzinne_post_format );

$pubzinne_blog_meta     = pubzinne_get_custom_layout_meta( $pubzinne_blog_id );
$pubzinne_custom_style  = ! empty( $pubzinne_blog_meta['scripts_required'] ) ? $pubzinne_blog_meta['scripts_required'] : 'none';

if ( ! empty( $pubzinne_template_args['slider'] ) || $pubzinne_columns > 1 || ! pubzinne_is_off( $pubzinne_custom_style ) ) {
	?><div class="
		<?php
		if ( ! empty( $pubzinne_template_args['slider'] ) ) {
			echo 'slider-slide swiper-slide';
		} else {
			echo esc_attr( ( pubzinne_is_off( $pubzinne_custom_style ) ? 'column' : sprintf( '%1$s_item %1$s_item', $pubzinne_custom_style ) ) . "-1_{$pubzinne_columns}" );
		}
		?>
	">
	<?php
}
?>
<article id="post-<?php the_ID(); ?>" data-post-id="<?php the_ID(); ?>"
	<?php
	post_class(
			'post_item post_item_container post_format_' . esc_attr( $pubzinne_post_format )
					. ' post_layout_custom post_layout_custom_' . esc_attr( $pubzinne_columns )
					. ' post_layout_' . esc_attr( $pubzinne_blog_style[0] )
					. ' post_layout_' . esc_attr( $pubzinne_blog_style[0] ) . '_' . esc_attr( $pubzinne_columns )
					. ( ! pubzinne_is_off( $pubzinne_custom_style )
						? ' post_layout_' . esc_attr( $pubzinne_custom_style )
							. ' post_layout_' . esc_attr( $pubzinne_custom_style ) . '_' . esc_attr( $pubzinne_columns )
						: ''
						)
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
	// Custom layout
	do_action( 'pubzinne_action_show_layout', $pubzinne_blog_id, get_the_ID() );
	?>
</article><?php
if ( ! empty( $pubzinne_template_args['slider'] ) || $pubzinne_columns > 1 || ! pubzinne_is_off( $pubzinne_custom_style ) ) {
	?></div><?php
	// Need opening PHP-tag above just after </div>, because <div> is a inline-block element (used as column)!
}
