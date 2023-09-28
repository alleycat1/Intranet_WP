<?php
/**
 * The style "default" of the video's title in the widget "Video list"
 *
 * @package ThemeREX Addons
 * @since v1.78.0
 */

$args = get_query_var('trx_addons_args_widget_video_list_title');

$titles_content = apply_filters('trx_addons_filter_video_list_title', '', $args);
if ( empty( $titles_content ) ) {
	if ( ! empty( $args['subtitle'] ) ) {
		$titles_content .= '<div class="trx_addons_video_list_subtitle">' . trim( $args['subtitle'] ) . '</div>';
	}
	if ( ! empty( $args['title'] ) ) {
		$titles_content .= '<h3 class="trx_addons_video_list_title">'
					. ( ! empty( $args['link'] ) ? '<a href="'.esc_url( $args['link'] ).'">' : '')
					. trim( $args['title'] )
					. ( ! empty( $args['link'] ) ? '</a>' : '')
					. '</h3>';
	}
	if ( ! empty( $args['meta'] ) ) {
		$titles_content .= '<div class="trx_addons_video_list_meta">' . trim( $args['meta'] ) . '</div>';
	}
}
if ( ! empty( $titles_content ) ) {
	?><div class="trx_addons_video_list_title_wrap"><?php
		trx_addons_show_layout( $titles_content );
	?></div><?php
}
