<?php
/**
 * The style "default" of the Portfolio
 *
 * @package ThemeREX Addons
 * @since v1.5
 */

$args        = get_query_var('trx_addons_args_sc_portfolio');

$meta        = get_post_meta(get_the_ID(), 'trx_addons_options', true);
$link        = !empty($meta['link']) ? $meta['link'] : get_permalink();

$image_id    = get_post_thumbnail_id( get_the_ID() );
if ( $image_id ) {
	$image = wp_get_attachment_image_src( $image_id, 'full' );
}

if ( empty( $args['type'] ) ) {
	$args['type'] = 'default';
}

$use_masonry = ( ! isset( $args['use_masonry'] ) && trx_addons_is_on( trx_addons_get_option( 'portfolio_use_masonry' ) ) ) || ( isset( $args['use_masonry'] ) && trx_addons_is_on( $args['use_masonry'] ) );
$use_gallery = ( ! isset( $args['use_gallery'] ) && trx_addons_is_on( trx_addons_get_option( 'portfolio_use_gallery' ) ) ) || ( isset( $args['use_gallery'] ) && trx_addons_is_on( $args['use_gallery'] ) );

$details     = '';
if ( $use_gallery ) {
	ob_start();
	trx_addons_cpt_portfolio_show_details( array(
												'meta'  => $meta,
												'class' => 'portfolio_page_details',
												'share' => true
												)
										);
	$details = ob_get_contents();
	ob_end_clean();
}

if ( ! empty($args['slider']) ) {
	?><div class="slider-slide swiper-slide"><?php
} else if ( $args['columns'] > 1 ) {
	if ( $use_masonry ) {
		?><div class="sc_portfolio_masonry_item sc_portfolio_masonry_item-1_<?php echo esc_attr( $args['columns'] ); ?>"><?php
	} else {
		?><div class="<?php echo esc_attr(trx_addons_get_column_class(1, $args['columns'], !empty($args['columns_tablet']) ? $args['columns_tablet'] : '', !empty($args['columns_mobile']) ? $args['columns_mobile'] : '')); ?>"><?php
	}
}
?>
<div data-post-id="<?php the_ID(); ?>" class="sc_portfolio_item sc_item_container post_container<?php
	if (isset($args['hide_excerpt']) && (int)$args['hide_excerpt']>0) echo ' without_content';
	?>"
	<?php trx_addons_add_blog_animation('portfolio', $args); ?>
	data-size="<?php
		if ( ! empty( $image[1] ) && ! empty( $image[2] ) ) {
			echo intval( $image[1] ) . 'x' . intval( $image[2] );}
	?>"
	data-src="<?php
		if ( ! empty( $image[0] ) ) {
			echo esc_url( $image[0] );
		}
	?>"
	<?php
	if ( $use_gallery ) {
		if ( ! empty( $meta['video'] ) ) {
			?>
			data-video="<?php
				echo esc_attr( trx_addons_get_video_layout( apply_filters( 'trx_addons_filter_get_video_layout_args', array(
					'link' => $meta['video'],
					'autoplay' => true,
					'loop' => true,
					'mute' => false,
					'show_cover' => false
				), 'portfolio.gallery' ) ) );
			?>"
			<?php
		}
		?>
		data-details="<?php
			echo esc_attr( '<div class="post_details">'
								. '<h2 class="post_title">'
									. '<a href="' . esc_url( $link ) . '">' . esc_html( get_the_title() ) . '</a>'
								. '</h2>'
								. '<div class="post_description">'
									. $details
									. ( !isset( $args['hide_excerpt'] ) || (int)$args['hide_excerpt'] == 0
										? '<div class="post_description_content">' . get_the_excerpt() . '</div>'
										: ''
										)
									. ( ! empty( $args['more_text'] )
										? '<a href="' . esc_url( $link ) . '" class="theme_button post_readmore"><span class="post_readmore_label">' . $args['more_text'] . '</span></a>'
										: ''
										)
								. '</div>'
							. '</div>'
						);
		?>"
		<?php
	}
?>>
	<?php
	// Featured image or icon
	trx_addons_get_template_part(
		'templates/tpl.featured.php',
		'trx_addons_args_featured',
		apply_filters(
			'trx_addons_filter_args_featured',
			array(
				'class'         => 'sc_portfolio_item_thumb',
				'hover'         => '!info',
				'link'          => $link,
				'thumb_size'    => apply_filters(
									'trx_addons_filter_thumb_size', 
									trx_addons_get_thumb_size(
										$use_masonry
											? ( $args['columns'] > 2 ? 'masonry' : 'masonry-big' )
											: ( $args['columns'] > 2 ? 'medium'  : 'big' )
									),
									'portfolio-default'
								),
				'thumb_only'    => empty( $meta['video'] ),
				'show_no_image' => empty( $meta['video'] ),
				'autoplay'      => ! empty( $meta['video'] ) && ! empty( $meta['video_autoplay_archive'] ),
				'video'         => empty( $meta['video'] )
										? ''
										: trx_addons_get_video_layout( apply_filters( 'trx_addons_filter_get_video_layout_args', array(
												'link' => $meta['video'],
												'autoplay' => ! empty( $meta['video_autoplay_archive'] ),
												'mute' => ! empty( $meta['video_autoplay_archive'] ),
												'loop' => ! empty( $meta['video_autoplay_archive'] ),
												'show_cover' => empty( $meta['video_autoplay_archive'] )
											), 'portfolio.default-item' ) )
			),
			'portfolio-default'
		)
	);
	?>	
</div><?php
if (!empty($args['slider']) || $args['columns'] > 1) {
	?></div><?php
}
