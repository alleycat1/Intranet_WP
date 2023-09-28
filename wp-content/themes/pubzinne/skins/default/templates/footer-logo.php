<?php
/**
 * The template to display the site logo in the footer
 *
 * @package PUBZINNE
 * @since PUBZINNE 1.0.10
 */

// Logo
if ( pubzinne_is_on( pubzinne_get_theme_option( 'logo_in_footer' ) ) ) {
	$pubzinne_logo_image = pubzinne_get_logo_image( 'footer' );
	$pubzinne_logo_text  = get_bloginfo( 'name' );
	if ( ! empty( $pubzinne_logo_image['logo'] ) || ! empty( $pubzinne_logo_text ) ) {
		?>
		<div class="footer_logo_wrap">
			<div class="footer_logo_inner">
				<?php
				if ( ! empty( $pubzinne_logo_image['logo'] ) ) {
					$pubzinne_attr = pubzinne_getimagesize( $pubzinne_logo_image['logo'] );
					echo '<a href="' . esc_url( home_url( '/' ) ) . '">'
							. '<img src="' . esc_url( $pubzinne_logo_image['logo'] ) . '"'
								. ( ! empty( $pubzinne_logo_image['logo_retina'] ) ? ' srcset="' . esc_url( $pubzinne_logo_image['logo_retina'] ) . ' 2x"' : '' )
								. ' class="logo_footer_image"'
								. ' alt="' . esc_attr__( 'Site logo', 'pubzinne' ) . '"'
								. ( ! empty( $pubzinne_attr[3] ) ? ' ' . wp_kses_data( $pubzinne_attr[3] ) : '' )
							. '>'
						. '</a>';
				} elseif ( ! empty( $pubzinne_logo_text ) ) {
					echo '<h1 class="logo_footer_text">'
							. '<a href="' . esc_url( home_url( '/' ) ) . '">'
								. esc_html( $pubzinne_logo_text )
							. '</a>'
						. '</h1>';
				}
				?>
			</div>
		</div>
		<?php
	}
}
