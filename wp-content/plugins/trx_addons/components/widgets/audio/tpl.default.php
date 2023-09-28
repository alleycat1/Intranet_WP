<?php
/**
 * The style "default" of the Widget "Audio"
 *
 * @package ThemeREX Addons
 * @since v1.6.10
 */

$args = get_query_var( 'trx_addons_args_widget_audio' );
extract( $args );

/* Before widget (defined by themes) */
trx_addons_show_layout( $before_widget );

/* Widget title if one was input (before and after defined by themes) */
trx_addons_show_layout( $title, $before_title, $after_title );

/* Widget subtitle */
if ( ! empty( $subtitle ) ) {
	echo '<div class="widget_subtitle">' . esc_html( $subtitle ) . '</div>';
}

/* Widget body */
if ( is_array( $media ) && count( $media ) > 0 ) {

	$wrap_class = ( '1' !== $track_time ? ' hide_time' : '' )
				. ( '1' !== $track_scroll ? ' hide_scroll' : '' )
				. ( '1' !== $track_volume ? ' hide_volume' : '' )
				. ( is_array( $media ) && count( $media ) > 1 ? ' list' : '' );

	?><div class="trx_addons_audio_wrap<?php echo esc_attr( $wrap_class ); ?>">
		<div class="trx_addons_audio_list">
		<?php
		foreach ( $media as $item ) {
			$item['url']         = array_key_exists( 'url', $item ) && ! empty( $item['url'] ) ? $item['url'] : '';
			$item['embed']       = array_key_exists( 'embed', $item ) && ! empty( $item['embed'] ) ? $item['embed'] : '';
			$item['caption']     = array_key_exists( 'caption', $item ) && ! empty( $item['caption'] ) ? $item['caption'] : '';
			$item['author']      = array_key_exists( 'author', $item ) && ! empty( $item['author'] ) ? $item['author'] : '';
			$item['description'] = array_key_exists( 'description', $item ) && ! empty( $item['description'] ) ? $item['description'] : '';
			$item['cover']       = array_key_exists( 'cover', $item ) && ! empty( $item['cover'] ) ? $item['cover'] : '';
			?>
				<div class="trx_addons_audio_player
				<?php
				echo ! empty( $item['cover'] ) ? ' with_cover' : ' without_cover';
				?>
				"
				<?php
				if ( ! empty( $item['cover'] ) ) {
					echo ' style="background-image:url(' . esc_url( $item['cover'] ) . ');"';
				}
				?>
				>
					<div class="trx_addons_audio_player_wrap">
					<?php

					if ( ! empty( $item['author'] ) || ! empty( $item['caption'] ) ) {
						?>
						<div class="audio_info">
							<?php
							if ( '#' !== $now_text && count( $media ) > 1 ) {
								echo '<h5 class="audio_now_playing">' . ( ! empty( $now_text ) ? esc_html( $now_text ) : esc_html__( 'Now Playing', 'trx_addons' ) ) . ': </h5>';
							}
							if ( ! empty( $item['author'] ) ) {
								?>
								<h6 class="audio_author"><?php echo esc_html( $item['author'] ); ?></h6>
								<?php
							}
							if ( ! empty( $item['caption'] ) ) {
								?>
								<h5 class="audio_caption"><?php echo esc_html( $item['caption'] ); ?></h5>
								<?php
							}
							if ( ! empty( $item['description'] ) ) {
								?>
								<div class="audio_description"><?php echo esc_html( $item['description'] ); ?></div>
								<?php
							}
							?>
						</div>
						<?php
					}
					?>
					<div class="audio_frame audio_<?php echo esc_attr( $item['embed'] ? 'embed' : 'local' ); ?>">
					<?php
					if ( $item['embed'] ) {
						trx_addons_show_layout( $item['embed'] );
					} elseif ( $item['url'] ) {
						$default_types = wp_get_audio_extensions();
						$type = wp_check_filetype( $item['url'], wp_get_mime_types() );
						$need_replace = false;
						if ( ! in_array( strtolower( $type['ext'] ), $default_types ) ) {
							$need_replace = true;
							$item['url_orig'] = $item['url'];
							$item['url'] .= '.mp3';
						}
						$output = do_shortcode( '[audio src="' . trim( $item['url'] ) . '"]' );
						if ( ! empty( $output ) ) {
							if ( $need_replace ) {
								$output = str_replace( $item['url'], $item['url_orig'], $output );
							}
							$output = str_replace(
											'<audio ',
											'<audio'
												. ' data-src="' . esc_url($need_replace ? $item['url_orig'] : $item['url']) . '"'
												. ' data-cover="' . esc_url($item['cover']) . '"'
												. ' data-caption="' . esc_attr($item['caption']) . '"'
												. ' data-author="' . esc_attr($item['author']) . '"'
												. ' ',
											$output );
							trx_addons_show_layout( $output );
						} else {
							?>
							<audio src="<?php echo esc_url( $item['url'] ); ?>"></audio>
							<?php
						}
					}
					?>
					</div>
				</div>
			</div>
			<?php
		}
		?>
		</div>
		<?php
		if ( count( $media ) > 1 ) {
			if ( '1' === $args['prev_btn'] || '1' === $args['next_btn'] ) {
				echo '<div class="trx_addons_audio_navigation">'
						. ( '1' === $args['prev_btn'] ? '<span class="nav_btn prev"><span class="trx_addons_icon-slider-left"></span>' . esc_html( $prev_text ) . '</span>' : '' )
						. ( '1' === $args['next_btn'] ? '<span class="nav_btn next">' . esc_html( $next_text ) . '<span class="trx_addons_icon-slider-right"></span></span>' : '' )
					. '</div>';
			}
		}
		?>
	</div><?php
}

/* After widget (defined by themes) */
trx_addons_show_layout( $after_widget );
?>
