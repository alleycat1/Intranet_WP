<?php
/**
 * The style "hover" of the Widget "Video"
 *
 * @package ThemeREX Addons
 * @since v2.17.5
 */

$args = get_query_var('trx_addons_args_widget_video');
extract($args);


// Before widget (defined by themes)
trx_addons_show_layout($before_widget);
			
// Widget title if one was input (before and after defined by themes)
trx_addons_show_layout($title, $before_title, $after_title);
	
// Widget body
if ( ! empty( $link ) ) {
	if ( empty( $id ) ) {
		$id = trx_addons_generate_id( 'sc_video_' );
	}
	if ( ! empty( $cover ) ) {
		$cover = trx_addons_get_attachment_url(
					$cover, 
					apply_filters( 'trx_addons_filter_video_cover_thumb_size', trx_addons_get_thumb_size( ! empty( $cover_size ) ? $cover_size : 'masonry-big' ) )
				);
	}
	?><div id="<?php echo esc_attr( $id ); ?>" class="trx_addons_video_hover <?php echo ! empty( $cover ) ? 'with_cover' : 'without_cover'; ?>">
		<div class="trx_addons_video_media" data-ratio="<?php echo esc_attr( $ratio ); ?>">
			<?php
			if ( ! empty( $cover ) ) {
				?>
				<picture type="image" class="trx_addons_video_cover">
					<img src="<?php echo esc_url( $cover ); ?>" alt="<?php esc_attr_e("Video cover", 'trx_addons'); ?>">
					</picture>
				<?php
			}
			// Add a class 'inited' to prevent a script Media Elements to be inited on the video
			?>
			<video class="trx_addons_video_video trx_addons_noresize inited" playsinline disablepictureinpicture loop="loop"<?php
				if ( (int)$autoplay > 0 ) echo ' data-autoplay="1"';
				if ( ! isset( $mute ) || (int)$mute > 0 ) echo ' muted="muted"';
			?>>
					<source src="<?php echo esc_url( $link ); ?>" type="video/mp4" />
			</video>
		</div>
		<?php
		if ( ! empty( $subtitle ) ) {
			?><p class="trx_addons_video_subtitle"><span class="trx_addons_video_subtitle_text"><?php echo wp_kses( trx_addons_prepare_macros( $subtitle ), 'trx_addons_kses_content' ); ?></span></p><?php
		}
		?>
	</div><?php
}
	
// After widget (defined by themes)
trx_addons_show_layout($after_widget);
