<?php
/**
 * The style "simple" of the Testimonials
 *
 * @package ThemeREX Addons
 * @since v1.4.3
 */

$args = get_query_var('trx_addons_args_sc_testimonials');

$meta = (array)get_post_meta(get_the_ID(), 'trx_addons_options', true);
			
if ($args['slider']) {
	?><div class="slider-slide swiper-slide"><?php
} else if ($args['columns'] > 1) {
	?><div class="<?php echo esc_attr(trx_addons_get_column_class(1, $args['columns'], !empty($args['columns_tablet']) ? $args['columns_tablet'] : '', !empty($args['columns_mobile']) ? $args['columns_mobile'] : '')); ?>"><?php
}
?>
<div data-post-id="<?php the_ID(); ?>" class="sc_testimonials_item sc_item_container post_container">
	<div class="sc_testimonials_item_content"><?php
		if ( has_excerpt() ) {
			the_excerpt();
		} else {
			the_content();
		}
	?></div>
	<div class="sc_testimonials_item_author">
		<div class="sc_testimonials_item_author_data">
			<h4 class="sc_testimonials_item_author_title"><?php the_title(); ?></h4>
			<?php
			if ( ! empty( $meta['subtitle'] ) ) {
				?><div class="sc_testimonials_item_author_subtitle"><?php echo esc_html($meta['subtitle']); ?></div><?php
			}
			if ( (int) $args['rating'] == 1 && ! empty( $meta['rating'] ) ) {
				?><div class="sc_testimonials_item_author_rating"><?php trx_addons_testimonials_show_rating($meta['rating']); ?></div><?php
			}
			?>
		</div>
	</div>
</div>
<?php
if ($args['slider'] || $args['columns'] > 1) {
	?></div><?php
}
