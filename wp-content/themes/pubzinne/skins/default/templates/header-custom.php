<?php
/**
 * The template to display custom header from the ThemeREX Addons Layouts
 *
 * @package PUBZINNE
 * @since PUBZINNE 1.0.06
 */

$pubzinne_header_css   = '';
$pubzinne_header_image = get_header_image();
$pubzinne_header_video = pubzinne_get_header_video();
if ( ! empty( $pubzinne_header_image ) && pubzinne_trx_addons_featured_image_override( is_singular() || pubzinne_storage_isset( 'blog_archive' ) || is_category() ) ) {
	$pubzinne_header_image = pubzinne_get_current_mode_image( $pubzinne_header_image );
}

$pubzinne_header_id = pubzinne_get_custom_header_id();
$pubzinne_header_meta = get_post_meta( $pubzinne_header_id, 'trx_addons_options', true );
if ( ! empty( $pubzinne_header_meta['margin'] ) ) {
	pubzinne_add_inline_css( sprintf( '.page_content_wrap{padding-top:%s}', esc_attr( pubzinne_prepare_css_value( $pubzinne_header_meta['margin'] ) ) ) );
}

?><header class="top_panel top_panel_custom top_panel_custom_<?php echo esc_attr( $pubzinne_header_id ); ?> top_panel_custom_<?php echo esc_attr( sanitize_title( get_the_title( $pubzinne_header_id ) ) ); ?>
				<?php
				echo ! empty( $pubzinne_header_image ) || ! empty( $pubzinne_header_video )
					? ' with_bg_image'
					: ' without_bg_image';
				if ( '' != $pubzinne_header_video ) {
					echo ' with_bg_video';
				}
				if ( '' != $pubzinne_header_image ) {
					echo ' ' . esc_attr( pubzinne_add_inline_css_class( 'background-image: url(' . esc_url( $pubzinne_header_image ) . ');' ) );
				}
				if ( is_single() && has_post_thumbnail() ) {
					echo ' with_featured_image';
				}
				if ( pubzinne_is_on( pubzinne_get_theme_option( 'header_fullheight' ) ) ) {
					echo ' header_fullheight pubzinne-full-height';
				}
				$pubzinne_header_scheme = pubzinne_get_theme_option( 'header_scheme' );
				if ( ! empty( $pubzinne_header_scheme ) && ! pubzinne_is_inherit( $pubzinne_header_scheme  ) ) {
					echo ' scheme_' . esc_attr( $pubzinne_header_scheme );
				}
				?>
">
	<?php

	// Background video
	if ( ! empty( $pubzinne_header_video ) ) {
		get_template_part( apply_filters( 'pubzinne_filter_get_template_part', 'templates/header-video' ) );
	}

	// Custom header's layout
	do_action( 'pubzinne_action_show_layout', $pubzinne_header_id );

	// Header widgets area
	get_template_part( apply_filters( 'pubzinne_filter_get_template_part', 'templates/header-widgets' ) );

	?>
</header>
