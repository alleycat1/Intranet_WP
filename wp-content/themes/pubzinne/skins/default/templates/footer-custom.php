<?php
/**
 * The template to display default site footer
 *
 * @package PUBZINNE
 * @since PUBZINNE 1.0.10
 */

$pubzinne_footer_id = pubzinne_get_custom_footer_id();
$pubzinne_footer_meta = get_post_meta( $pubzinne_footer_id, 'trx_addons_options', true );
if ( ! empty( $pubzinne_footer_meta['margin'] ) ) {
	pubzinne_add_inline_css( sprintf( '.page_content_wrap{padding-bottom:%s}', esc_attr( pubzinne_prepare_css_value( $pubzinne_footer_meta['margin'] ) ) ) );
}
?>
<footer class="footer_wrap footer_custom footer_custom_<?php echo esc_attr( $pubzinne_footer_id ); ?> footer_custom_<?php echo esc_attr( sanitize_title( get_the_title( $pubzinne_footer_id ) ) ); ?>
						<?php
						$pubzinne_footer_scheme = pubzinne_get_theme_option( 'footer_scheme' );
						if ( ! empty( $pubzinne_footer_scheme ) && ! pubzinne_is_inherit( $pubzinne_footer_scheme  ) ) {
							echo ' scheme_' . esc_attr( $pubzinne_footer_scheme );
						}
						?>
						">
	<?php
	// Custom footer's layout
	do_action( 'pubzinne_action_show_layout', $pubzinne_footer_id );
	?>
</footer><!-- /.footer_wrap -->
