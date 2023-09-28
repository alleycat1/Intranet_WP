<?php
/**
 * The template to display the socials in the footer
 *
 * @package PUBZINNE
 * @since PUBZINNE 1.0.10
 */


// Socials
if ( pubzinne_is_on( pubzinne_get_theme_option( 'socials_in_footer' ) ) ) {
	$pubzinne_output = pubzinne_get_socials_links();
	if ( '' != $pubzinne_output ) {
		?>
		<div class="footer_socials_wrap socials_wrap">
			<div class="footer_socials_inner">
				<?php pubzinne_show_layout( $pubzinne_output ); ?>
			</div>
		</div>
		<?php
	}
}
