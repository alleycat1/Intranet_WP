<?php
/**
 * The template to display the background video in the header
 *
 * @package PUBZINNE
 * @since PUBZINNE 1.0.14
 */
$pubzinne_header_video = pubzinne_get_header_video();
$pubzinne_embed_video  = '';
if ( ! empty( $pubzinne_header_video ) && ! pubzinne_is_from_uploads( $pubzinne_header_video ) ) {
	if ( pubzinne_is_youtube_url( $pubzinne_header_video ) && preg_match( '/[=\/]([^=\/]*)$/', $pubzinne_header_video, $matches ) && ! empty( $matches[1] ) ) {
		?><div id="background_video" data-youtube-code="<?php echo esc_attr( $matches[1] ); ?>"></div>
		<?php
	} else {
		?>
		<div id="background_video"><?php pubzinne_show_layout( pubzinne_get_embed_video( $pubzinne_header_video ) ); ?></div>
		<?php
	}
}
