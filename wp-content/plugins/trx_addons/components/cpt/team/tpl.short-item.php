<?php
/**
 * The style "short" of the Team
 *
 * @package ThemeREX Addons
 * @since v1.4.3
 */

$args = get_query_var('trx_addons_args_sc_team');

$meta = (array)get_post_meta(get_the_ID(), 'trx_addons_options', true);
$link = empty($args['no_links']) ? get_permalink() : '';

if ($args['slider']) {
	?><div class="slider-slide swiper-slide"><?php
} else if ($args['columns'] > 1) {
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
								apply_filters( 'trx_addons_filter_args_featured', array(
												'allow_theme_replace' => false,
												'class' => 'sc_team_item_thumb',
												'no_links' => empty($link),
												'hover' => ! empty($meta['socials']) ? 'info' : 'zoomin',
												'thumb_size' => apply_filters( 'trx_addons_filter_thumb_size', trx_addons_get_thumb_size('avatar'), 'team-short', $args ),
												'post_info' => apply_filters( 'trx_addons_filter_post_info',
																! empty($meta['socials']) 
																	? '<div class="trx_addons_hover_content"><div class="sc_team_item_socials socials_wrap trx_addons_hover_info">'.trim(trx_addons_get_socials_links_custom($meta['socials'])).'</div></div>'
																	: '',
																'team-short', $args )
												), 'team-short', $args )
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
	</div>
</div>
<?php
if ( $args['slider'] || $args['columns'] > 1 ) {
	?></div><?php
}
