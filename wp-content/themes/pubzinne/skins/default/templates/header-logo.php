<?php
/**
 * The template to display the logo or the site name and the slogan in the Header
 *
 * @package PUBZINNE
 * @since PUBZINNE 1.0
 */

$pubzinne_args = get_query_var( 'pubzinne_logo_args' );

// Site logo
$pubzinne_logo_type   = isset( $pubzinne_args['type'] ) ? $pubzinne_args['type'] : '';
$pubzinne_logo_image  = pubzinne_get_logo_image( $pubzinne_logo_type );
$pubzinne_logo_text   = pubzinne_is_on( pubzinne_get_theme_option( 'logo_text' ) ) ? get_bloginfo( 'name' ) : '';
$pubzinne_logo_slogan = get_bloginfo( 'description', 'display' );
if ( ! empty( $pubzinne_logo_image['logo'] ) || ! empty( $pubzinne_logo_text ) ) {
	?><a class="sc_layouts_logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
		<?php
		if ( ! empty( $pubzinne_logo_image['logo'] ) ) {
			if ( empty( $pubzinne_logo_type ) && function_exists( 'the_custom_logo' ) && is_numeric( $pubzinne_logo_image['logo'] ) && $pubzinne_logo_image['logo'] > 0 ) {
				the_custom_logo();
			} else {
				$pubzinne_attr = pubzinne_getimagesize( $pubzinne_logo_image['logo'] );
				echo '<img src="' . esc_url( $pubzinne_logo_image['logo'] ) . '"'
						. ( ! empty( $pubzinne_logo_image['logo_retina'] ) ? ' srcset="' . esc_url( $pubzinne_logo_image['logo_retina'] ) . ' 2x"' : '' )
						. ' alt="' . esc_attr( $pubzinne_logo_text ) . '"'
						. ( ! empty( $pubzinne_attr[3] ) ? ' ' . wp_kses_data( $pubzinne_attr[3] ) : '' )
						. '>';
			}
		} else {
			pubzinne_show_layout( pubzinne_prepare_macros( $pubzinne_logo_text ), '<span class="logo_text">', '</span>' );
			pubzinne_show_layout( pubzinne_prepare_macros( $pubzinne_logo_slogan ), '<span class="logo_slogan">', '</span>' );
		}
		?>
	</a>
	<?php
}
