<?php
/**
 * The style "default" of the Widget "Video list"
 *
 * @package ThemeREX Addons
 * @since v1.78.0
 */

$args = get_query_var('trx_addons_args_widget_video_list');
extract($args);

if ( is_array( $videos ) && count( $videos ) > 0 && ( ! empty( $videos[0]['video_url'] ) || ! empty( $videos[0]['video_embed'] ) ) ) {

	// Before widget (defined by themes)
	trx_addons_show_layout($before_widget);

	// Widget title if one was input (before and after defined by themes)
	trx_addons_show_layout($title, $before_title, $after_title);
		
	// Widget body
	?><div class="trx_addons_video_list trx_addons_video_list_<?php echo esc_attr( $args['controller_style'] ); ?> trx_addons_video_list_controller_<?php echo esc_attr( $args['controller_pos'] ); ?> trx_addons_video_list_controller_videos_<?php echo esc_attr( count( $videos ) ); ?>"><?php
		// Video frame
		?><div class="trx_addons_video_list_video_wrap"><?php
			do_action( 'trx_addons_action_before_single_post_video', $args );
			trx_addons_show_layout( trx_addons_get_video_layout( apply_filters( 'trx_addons_filter_get_video_layout_args', array(
				'link' => $videos[0]['video_url'],
				'embed' => $videos[0]['video_embed'],
				'autoplay' => ! empty($args['autoplay']),
				'mute' => ! empty($args['autoplay']),
				'loop' => ! empty($args['autoplay']),
				'cover' => empty($args['autoplay']) ? $videos[0]['image'] : '',
				'cover_size' => apply_filters( 'trx_addons_filter_video_list_thumb_size', 'big' ),
			), 'video_list.default' ) ) );
			trx_addons_show_layout( trx_addons_get_template_part_as_string(
				array(
					TRX_ADDONS_PLUGIN_WIDGETS . 'video_list/tpl.' . trx_addons_esc($args['controller_style']) . '-title.php',
					TRX_ADDONS_PLUGIN_WIDGETS . 'video_list/tpl.default-title.php',
				),
				'trx_addons_args_widget_video_list_title',
				array_merge( $args, $videos[0] )
			) );
			do_action( 'trx_addons_action_after_single_post_video', $args );
		?></div><?php

		// Controller (TOC)
		?><div class="trx_addons_video_list_controller_wrap<?php
			if ( ! empty($args['controller_height']) && $args['controller_pos'] == 'bottom' ) {
				echo ' ' . trx_addons_add_inline_css_class( '--sc-video-list-controller-height:' . esc_attr( trx_addons_prepare_css_value( $args['controller_height'] ) ) );
			}
		?>"><?php
			$skip_first = (int) $args['controller_link'] == 0;
			foreach ($videos as $k => $v) {
				if ( $skip_first && ! empty( $v['link'] ) ) {
					$skip_first = false;
					continue;
				}
				$video = trx_addons_get_video_layout( apply_filters( 'trx_addons_filter_get_video_layout_args', array(
							'link' => $v['video_url'],
							'embed' => $v['video_embed'],
							'cover' => ! $args['controller_autoplay'] ? $v['image'] : '',
							'cover_size' => apply_filters( 'trx_addons_filter_video_list_thumb_size', 'big' ),
							'autoplay' => $args['controller_autoplay'],
							'mute' => false,
							'loop' => true
						), 'video_list.default' ) );
				$title = trx_addons_get_template_part_as_string(
							array(
								TRX_ADDONS_PLUGIN_WIDGETS . 'video_list/tpl.' . trx_addons_esc($args['controller_style']) . '-title.php',
								TRX_ADDONS_PLUGIN_WIDGETS . 'video_list/tpl.default-title.php',
							),
							'trx_addons_args_widget_video_list_title',
							array_merge( $args, $v )
						);
				$controller_content = apply_filters( 'trx_addons_filter_video_list_controller', '', array_merge( $args, $v ) );
				if ( empty( $controller_content ) ) {
					if ( ! empty( $v['image'] ) ) {
						$controller_content .= '<div class="trx_addons_video_list_image">'
													. '<img src="' . esc_url( trx_addons_get_attachment_url( $v['image'], trx_addons_get_thumb_size( 'tiny' ) ) ) . '" alt="' . esc_attr( $v['title'] ) . '">'
													. '<span class="trx_addons_video_list_image_label">' . esc_html__( 'Now playing', 'trx_addons' ) . '</span>'
												. '</div>';
					}
					if ( ! empty( $v['subtitle'] ) || ! empty( $v['title'] ) ) {
						$controller_content .= '<div class="trx_addons_video_list_info">';
						if ( ! empty( $v['subtitle'] ) ) {
							$controller_content .= '<div class="trx_addons_video_list_subtitle">'
														. trx_addons_strwords($v['subtitle'], apply_filters( 'trx_addons_filter_video_list_controller_subtitle_length', $args['controller_pos'] == 'bottom' ? 6 : 10, $args ) )
													. '</div>';
						}
						if ( ! empty( $v['title'] ) ) {
							$controller_content .= '<h5 class="trx_addons_video_list_title">'
														. ( ! empty( $v['link'] ) ? '<a href="'.esc_url($v['link']).'">' : '')
														. trx_addons_strwords( $v['title'], apply_filters( 'trx_addons_filter_video_list_controller_title_length', $args['controller_pos'] == 'bottom' ? 6 : 10, $args ) )
														. ( ! empty( $v['link'] ) ? '</a>' : '')
													. '</h5>';
						}
						if ( ! empty($v['meta']) ) {
							$controller_content .= '<div class="trx_addons_video_list_meta">' . trim($v['meta']) . '</div>';
						}
						$controller_content .= '</div>';
					}
				}
				if ( ! empty($controller_content) ) {
					?><div class="trx_addons_video_list_controller_item<?php if ( $k == 0 ) echo ' trx_addons_video_list_controller_item_active'; ?>"
						data-video="<?php echo esc_attr( str_replace( '&', '&amp;', $video ) ); ?>"
						data-title="<?php echo esc_attr($title); ?>"
						data-autoplay="<?php echo esc_attr( (int) $args['controller_autoplay'] > 0 ? 1 : 0 ); ?>"
					>
						<?php trx_addons_show_layout( $controller_content ); ?>
						<a href="<?php echo (int) $args['controller_link'] == 1 || empty( $v['link'] ) ? '#' : esc_url( $v['link'] ); ?>" class="trx_addons_video_list_controller_item_link"></a>
					</div><?php
				}
			}
		?></div><?php
	?></div><?php

	// After widget (defined by themes)
	trx_addons_show_layout($after_widget);

}
