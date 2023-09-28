<?php
/**
 * The "Style 1" template to display the categories list
 *
 * Used for widget Categories List.
 *
 * @package ThemeREX Addons
 * @since v1.0
 */

$args = get_query_var('trx_addons_args_widget_categories_list');

if ( empty( $args['number'] ) ) $args['number'] = count( $args['categories'] );
$args['slider'] = !empty($args['slider']) && $args['number'] > $args['columns'];
$args['slides_space'] = !empty($args['slides_space']) ? max(0, (int) $args['slides_space']) : 0;

extract($args);

// Before widget (defined by themes)
trx_addons_show_layout($before_widget);
			
// Widget title if one was input (before and after defined by themes)
trx_addons_show_layout($title, $before_title, $after_title);
	
// Widget body
?>
<div class="categories_list categories_list_style_<?php echo esc_attr($style); ?>">
	<?php 
	if ($args['slider']) {
		$args['slides_min_width'] = 220;
		trx_addons_sc_show_slider_wrap_start('sc_categories_list', $args);
	} else if ($columns > 1) {
		?><div class="categories_list_columns <?php
			echo esc_attr(trx_addons_get_columns_wrap_class())
				. ' columns_padding_bottom'
				. esc_attr( trx_addons_add_columns_in_single_row( $columns, $categories ) );
		?>"><?php
	}
	$i = 0;
	foreach ($categories as $cat) {
		$i++;
		if ($number > 0 && $i > $number) break;
		$cat_link = get_term_link($cat->term_id, $cat->taxonomy);
		if ($args['slider']) {
			?><div class="slider-slide swiper-slide"><?php
		} else if ($columns > 1) {
			?><div class="<?php echo esc_attr(trx_addons_get_column_class(1, $columns, $columns_tablet, $columns_mobile)); ?>"><?php
		}
		?>
		<div class="categories_list_item">
			<?php
			if ($show_thumbs) {
				$image = trx_addons_get_term_image_small($cat->term_id, $cat->taxonomy);
				$icon = '';
				if ( empty($image) ) {
					$icon = trx_addons_get_term_icon($cat->term_id, $cat->taxonomy);
					if ( empty($icon) || trx_addons_is_off($icon) ) {
						$image = trx_addons_get_term_image($cat->term_id, $cat->taxonomy);
					}
				}
				?><div class="categories_list_image"><?php
					if ( !empty($icon) && !trx_addons_is_off($icon) ) {
						?><span class="categories_list_icon <?php echo esc_attr($icon); ?>"></span><?php
					} else {
						$src = empty($image)
									? trx_addons_get_no_image() 
									: trx_addons_add_thumb_size( $image, trx_addons_get_thumb_size('masonry') );
						$attr = trx_addons_getimagesize($src);
						?><img src="<?php echo esc_url($src); ?>" <?php if (!empty($attr[3])) trx_addons_show_layout($attr[3]); ?> alt="<?php esc_attr_e('Category image', 'trx_addons'); ?>"><?php
					}
				?></div><?php
			}
			?><h5 class="categories_list_title"><?php echo esc_html($cat->name); ?><?php
				if ($show_posts && $cat->count > 0) { 
					?><span class="categories_list_count">(<?php echo esc_html($cat->count); ?>)</span><?php
				}
			?></h5>
			<a href="<?php echo esc_url($cat_link); ?>" class="categories_list_link"></a>
		</div>
		<?php
		if ($args['slider'] || $columns > 1) {
			?></div><?php
		}
	}
	if ($args['slider']) {
		?></div><?php
		trx_addons_sc_show_slider_wrap_end('sc_categories_list', $args);
	} else if ($columns > 1) {
		?></div><?php
	}
	?>
</div>
<?php			

// After widget (defined by themes)
trx_addons_show_layout($after_widget);
