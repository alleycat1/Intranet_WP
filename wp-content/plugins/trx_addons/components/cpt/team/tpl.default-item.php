<?php
/**
 * The style "default" of the Team
 *
 * @package ThemeREX Addons
 * @since v1.2
 */

$args = get_query_var('trx_addons_args_sc_team');

$meta = (array)get_post_meta(get_the_ID(), 'trx_addons_options', true);
$link = empty($args['no_links']) ? get_permalink() : '';

if ( $args['slider'] ) {
	?><div class="slider-slide swiper-slide"><?php
} else if ( $args['columns'] > 1 ) {
	?><div class="<?php echo esc_attr(trx_addons_get_column_class(1, $args['columns'], !empty($args['columns_tablet']) ? $args['columns_tablet'] : '', !empty($args['columns_mobile']) ? $args['columns_mobile'] : '')); ?>"><?php
}
?>
<div data-post-id="<?php the_ID(); ?>" <?php
	post_class( 'sc_team_item sc_item_container post_container' . (empty($link) ? ' no_links' : '') );
	trx_addons_add_blog_animation('team', $args);
?>>
	<?php
	// Featured image
	trx_addons_get_template_part('templates/tpl.featured.php',
								'trx_addons_args_featured',
								apply_filters('trx_addons_filter_args_featured', array(
												'class' => 'sc_team_item_thumb',
												'no_links' => empty($link),
												'hover' => 'zoomin',
												'thumb_size' => apply_filters( 'trx_addons_filter_thumb_size', trx_addons_get_thumb_size( 'avatar' ), 'team-default' )
												), 'team-default')
								);
	?>
	<div class="sc_team_item_info">
		<div class="sc_team_item_header">
			<h4 class="sc_team_item_title entry-title"><?php
				if ( ! empty( $link ) ) {
					?><a href="<?php echo esc_url($link); ?>"><?php
				}
				the_title();
				if ( ! empty( $link ) ) {
					?></a><?php
				}
			?></h4>
			<?php
			if ( ! empty( $meta['subtitle'] ) ) {
				?><div class="sc_team_item_subtitle"><?php trx_addons_show_layout($meta['subtitle']);?></div><?php
			}
			?>
		</div>
		<?php
		if ( has_excerpt() ) {
			?><div class="sc_team_item_content"><?php the_excerpt(); ?></div><?php
		}
		if ( ! empty( $meta['socials'] ) ) {
			?><div class="sc_team_item_socials socials_wrap"><?php trx_addons_show_layout( trx_addons_get_socials_links_custom( $meta['socials'] ) ); ?></div><?php
		}
		if ( ! empty( $link ) && ! empty( $args['more_text'] ) ) {
			?><div class="sc_team_item_button"><a href="<?php echo esc_url( $link ); ?>" class="<?php echo esc_attr( apply_filters('trx_addons_filter_sc_item_link_classes', 'sc_button sc_button_simple', 'sc_team', $args ) ); ?>"><?php echo esc_html( $args['more_text'] ); ?></a></div><?php
		}
	?></div>
</div><?php
if ( $args['slider'] || $args['columns'] > 1 ) {
	?></div><?php
}
