<?php
/**
 * The "Style 4" template to display the categories list
 *
 * Used for widget Categories List.
 *
 * @package ThemeREX Addons
 * @since v1.88.0
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
		$args['slides_min_width'] = 180;
		trx_addons_sc_show_slider_wrap_start('sc_categories_list', $args);
	}
	$i = 0;
	$buttons = array();	
	foreach ($categories as $cat) {
		$i++;
		if ($number > 0 && $i > $number) break;
		if ($args['slider']) {
			?><div class="slider-slide swiper-slide"><?php
		}
		if ($show_thumbs) {
			$image = trx_addons_get_term_image($cat->term_id, $cat->taxonomy);
			$image = ! empty($image) ? trx_addons_add_thumb_size($image, trx_addons_get_thumb_size( 'medium' ) ) : "";
			$image_small = trx_addons_get_term_image_small($cat->term_id, $cat->taxonomy);
			if ( empty( $image_small ) ) {
				$icon = trx_addons_get_term_icon($cat->term_id, $cat->taxonomy);
			}
		}
		$buttons[] = apply_filters( 'trx_addons_filter_categories_list_button_args', array(
			"type" => "default",
			"size" => "small",
			"text_align" => "none",
			"bg_image" => $show_thumbs && ! empty($image) ? $image : "",
			"image" => $show_thumbs && ! empty($image_small) ? $image_small : "",
			"icon" => $show_thumbs && empty($image_small) && ! empty( $icon ) ? $icon : "",
			"icon_position" => "left",
			"title" => $cat->name . ( $show_posts && $cat->count > 0 ? ' (' . $cat->count . ')' : ''),
			"subtitle" => "",
			"link" => get_term_link($cat->term_id, $cat->taxonomy),
			"css" => ""
		) );
		if ($args['slider']) {
			trx_addons_show_layout( trx_addons_sc_button( array( 'buttons' => $buttons ) ) );
			$buttons = array();	
			?></div><?php
		}
	}
	if ($args['slider']) {
		?></div><?php
		trx_addons_sc_show_slider_wrap_end('sc_categories_list', $args);
	} else {
		trx_addons_show_layout( trx_addons_sc_button( array( 'buttons' => $buttons ) ) );
	}
	?>
</div>
<?php			

// After widget (defined by themes)
trx_addons_show_layout($after_widget);
