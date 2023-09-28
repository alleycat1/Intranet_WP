<?php
/**
 * The template to display default site footer
 *
 * @package PUBZINNE
 * @since PUBZINNE 1.0.10
 */

?>
<footer class="footer_wrap footer_default
<?php
$pubzinne_footer_scheme = pubzinne_get_theme_option( 'footer_scheme' );
if ( ! empty( $pubzinne_footer_scheme ) && ! pubzinne_is_inherit( $pubzinne_footer_scheme  ) ) {
	echo ' scheme_' . esc_attr( $pubzinne_footer_scheme );
}
?>
				">
	<?php

	// Footer widgets area
	get_template_part( apply_filters( 'pubzinne_filter_get_template_part', 'templates/footer-widgets' ) );

	// Logo
	get_template_part( apply_filters( 'pubzinne_filter_get_template_part', 'templates/footer-logo' ) );

	// Socials
	get_template_part( apply_filters( 'pubzinne_filter_get_template_part', 'templates/footer-socials' ) );

	// Menu
	get_template_part( apply_filters( 'pubzinne_filter_get_template_part', 'templates/footer-menu' ) );

	// Copyright area
	get_template_part( apply_filters( 'pubzinne_filter_get_template_part', 'templates/footer-copyright' ) );

	?>
</footer><!-- /.footer_wrap -->
