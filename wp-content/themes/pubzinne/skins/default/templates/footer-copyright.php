<?php
/**
 * The template to display the copyright info in the footer
 *
 * @package PUBZINNE
 * @since PUBZINNE 1.0.10
 */

// Copyright area
?> 
<div class="footer_copyright_wrap
<?php
$pubzinne_copyright_scheme = pubzinne_get_theme_option( 'copyright_scheme' );
if ( ! empty( $pubzinne_copyright_scheme ) && ! pubzinne_is_inherit( $pubzinne_copyright_scheme  ) ) {
	echo ' scheme_' . esc_attr( $pubzinne_copyright_scheme );
}
?>
				">
	<div class="footer_copyright_inner">
		<div class="content_wrap">
			<div class="copyright_text">
			<?php
				$pubzinne_copyright = pubzinne_get_theme_option( 'copyright' );
			if ( ! empty( $pubzinne_copyright ) ) {
				// Replace {{Y}} or {Y} with the current year
				$pubzinne_copyright = str_replace( array( '{{Y}}', '{Y}' ), date( 'Y' ), $pubzinne_copyright );
				// Replace {{...}} and ((...)) on the <i>...</i> and <b>...</b>
				$pubzinne_copyright = pubzinne_prepare_macros( $pubzinne_copyright );
				// Display copyright
				echo wp_kses( nl2br( $pubzinne_copyright ), 'pubzinne_kses_content' );
			}
			?>
			</div>
		</div>
	</div>
</div>
