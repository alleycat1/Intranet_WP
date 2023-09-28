<?php
/**
 * The style "hover" of the Services item
 *
 * @package ThemeREX Addons
 * @since v1.6.13
 */

$args = get_query_var('trx_addons_args_sc_services');
$number = get_query_var('trx_addons_args_item_number');

$meta = (array)get_post_meta(get_the_ID(), 'trx_addons_options', true);
if (!is_array($meta)) $meta = array();
$meta['price'] = apply_filters( 'trx_addons_filter_custom_meta_value', !empty($meta['price']) ? $meta['price'] : '', 'price' );

$link = empty($args['no_links'])
			? (!empty($meta['link']) ? $meta['link'] : get_permalink())
			: '';

$svg_present = false;
$image = '';
if ( has_post_thumbnail() ) {
	$image = trx_addons_get_attachment_url( 
											get_post_thumbnail_id( get_the_ID() ), 
											! empty( $args['thumb_size'] )
												? $args['thumb_size']
												: apply_filters('trx_addons_filter_thumb_size', trx_addons_get_thumb_size('masonry'), 'services-hover')
											);
}
if (empty($args['featured'])) $args['featured'] = 'icon';
if (empty($args['hide_bg_image'])) $args['hide_bg_image'] = 0;

if (!empty($args['slider'])) {
	?><div class="slider-slide swiper-slide"><?php
} else if ($args['columns'] > 1) {
	?><div class="<?php echo esc_attr(trx_addons_get_column_class(1, $args['columns'], !empty($args['columns_tablet']) ? $args['columns_tablet'] : '', !empty($args['columns_mobile']) ? $args['columns_mobile'] : '')); ?> "><?php
}
?>
<div data-post-id="<?php the_ID(); ?>" <?php post_class( apply_filters( 'trx_addons_filter_services_item_class',
			'sc_services_item sc_item_container post_container'
			. (empty($link) ? ' no_links' : '')
			. (isset($args['hide_excerpt']) && (int)$args['hide_excerpt']>0 ? ' without_content' : ' with_content')
			. (!empty($image) ? ' with_image' : '')
			. ($args['featured']=='icon' 
				? ' with_icon' 
				: ($args['featured']=='pictogram' 
					? ' with_pictogram' 
					: ($args['featured']=='number' 
						? ' with_number' 
						: ' sc_services_item_featured_none'
						)
					)
				),
			$args )
			);
	trx_addons_add_blog_animation('services', $args);
	if (!empty($args['popup'])) {
		?> data-post_id="<?php echo esc_attr(get_the_ID()); ?>"<?php
		?> data-post_type="<?php echo esc_attr(TRX_ADDONS_CPT_SERVICES_PT); ?>"<?php
	}
?>>
	<?php do_action( 'trx_addons_action_services_item_start', $args ); ?>
	<div class="sc_services_item_header <?php echo 1 == $args['hide_bg_image'] ? ' without_image' : ''; ?>"<?php if ($args['hide_bg_image']==0 && !empty($image)) echo ' style="background-image: url('.esc_url($image).');"'; ?>>
		<?php do_action( 'trx_addons_action_services_item_header_start', $args ); ?>
		<div class="sc_services_item_header_inner">
			<?php
			do_action( 'trx_addons_action_services_item_header_inner_start', $args );
			if ($args['featured'] != 'none') {
				if ($args['featured']=='icon' && !empty($meta['icon'])) {
					$svg = $img = '';
					if (trx_addons_is_url($meta['icon'])) {
						if (strpos($meta['icon'], '.svg') !== false) {
							$svg = $meta['icon'];
							$svg_present = !empty($args['icons_animation']);
						} else {
							$img = $meta['icon'];
						}
						$meta['icon'] = basename($meta['icon']);
					} else if (!empty($args['icons_animation']) && ($svg = trx_addons_get_file_dir('css/icons.svg/'.trx_addons_clear_icon_name($meta['icon']).'.svg')) != '')
						$svg_present = true;
					echo !empty($link) 
						? '<a href="'.esc_url($link).'"'.(!empty($meta['link']) && trx_addons_is_external_url($meta['link']) ? ' target="_blank"' : '') 
						: '<span';
					?>
					class="sc_services_item_icon<?php
								if ($svg_present) echo ' sc_icon_animation';
								echo !empty($svg) 
										? ' sc_icon_type_svg'
										: (!empty($img) 
											? ' sc_icon_type_images'
											: ' sc_icon_type_icons ' . esc_attr($meta['icon'])
											);
								?>"<?php
						 if (!empty($meta['icon_color'])) {
							 echo ' style="color:'.esc_attr($meta['icon_color']).'"';
						 }
					?>><?php
						if (!empty($svg)) {
							trx_addons_show_layout(trx_addons_get_svg_from_file($svg));
						} else if (!empty($img)) {
							$attr = trx_addons_getimagesize($img);
							?><img class="sc_icon_as_image" src="<?php echo esc_url($img); ?>" alt="<?php esc_attr_e('Icon', 'trx_addons'); ?>"<?php echo (!empty($attr[3]) ? ' '.trim($attr[3]) : ''); ?>><?php
						}
					echo !empty($link) 
						? '</a>' 
						: '</span>';
				} else if ($args['featured']=='pictogram' && !empty($meta['image'])) {
					echo !empty($link) 
						? '<a href="'.esc_url($link).'"'.(!empty($meta['link']) && trx_addons_is_external_url($meta['link']) ? ' target="_blank"' : '') 
						: '<span';
					?> class="sc_services_item_pictogram"><?php
					$attr = trx_addons_getimagesize($meta['image']);
					?><img src="<?php echo esc_url($meta['image']); ?>" alt="<?php esc_attr_e('Icon', 'trx_addons'); ?>"<?php echo (!empty($attr[3]) ? ' '.trim($attr[3]) : ''); ?>><?php
					echo !empty($link) 
						? '</a>' 
						: '</span>';
				} else if ($args['featured']=='number') {
					?><span class="sc_services_item_number"><?php
						printf("%02d", $number);
					?></span><?php
				}	
			}
			do_action( 'trx_addons_action_services_item_after_featured', $args );
			?>
			<h6 class="sc_services_item_title"><?php
				if (!empty($link)) {
					?><a href="<?php echo esc_url($link); ?>"<?php if (!empty($meta['link']) && trx_addons_is_external_url($meta['link'])) echo ' target="_blank"'; ?>><?php
				}
				the_title();
				if (!empty($link)) {
					?></a><?php
				}
			?></h6>
			<?php do_action( 'trx_addons_action_services_item_after_title', $args ); ?>
			<div class="sc_services_item_subtitle"><?php
				$terms = trx_addons_get_post_terms(', ', get_the_ID(), trx_addons_get_post_type_taxonomy());
				if (empty($link)) $terms = trx_addons_links_to_span($terms);
				trx_addons_show_layout($terms);
			?></div><?php
			do_action( 'trx_addons_action_services_item_after_subtitle', $args );
			if (!empty($meta['price'])) {
				?><div class="sc_services_item_price"><?php trx_addons_show_layout($meta['price']); ?></div><?php
			}
			do_action( 'trx_addons_action_services_item_header_inner_end', $args );
		?></div><?php
		do_action( 'trx_addons_action_services_item_after_header_inner', $args );
		if (!empty($link)) {
			?><a class="sc_services_item_link" href="<?php echo esc_url($link); ?>"<?php if (!empty($meta['link']) && trx_addons_is_external_url($meta['link'])) echo ' target="_blank"'; ?>></a><?php
		}
		do_action( 'trx_addons_action_services_item_header_end', $args );
	?></div><?php
	?><div class="sc_services_item_content"<?php if (!empty($image)) echo ' style="background-image: url('.esc_url($image).');"'; ?>>
		<?php do_action( 'trx_addons_action_services_item_content_start', $args ); ?>
		<div class="sc_services_item_content_inner">
			<?php do_action( 'trx_addons_action_services_item_content_inner_start', $args ); ?>
			<h6 class="sc_services_item_title"><?php
				if (!empty($link)) {
					?><a href="<?php echo esc_url($link); ?>"<?php if (!empty($meta['link']) && trx_addons_is_external_url($meta['link'])) echo ' target="_blank"'; ?>><?php
				}
				the_title();
				if (!empty($link)) {
					?></a><?php
				}
			?></h6>
			<?php do_action( 'trx_addons_action_services_item_after_content_title', $args ); ?>
			<div class="sc_services_item_subtitle"><?php trx_addons_show_layout($terms); ?></div>
			<?php do_action( 'trx_addons_action_services_item_after_content_subtitle', $args ); ?>
			<?php if (!isset($args['hide_excerpt']) || (int)$args['hide_excerpt']==0) { ?>
				<div class="sc_services_item_text"><?php the_excerpt(); ?></div>
			<?php } ?>
			<?php do_action( 'trx_addons_action_services_item_content_inner_end', $args ); ?>
		</div><?php
		do_action( 'trx_addons_action_services_item_content_end', $args );
		if (!empty($link)) {
			?><a class="sc_services_item_link" href="<?php echo esc_url($link); ?>"<?php if (!empty($meta['link']) && trx_addons_is_external_url($meta['link'])) echo ' target="_blank"'; ?>></a><?php
		}
	?></div><?php
?></div><?php
if (!empty($args['slider']) || $args['columns'] > 1) {
	?></div><?php
}
if ( $svg_present ) {
	wp_enqueue_script( 'vivus', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_SHORTCODES . 'icons/vivus.js'), array('jquery'), null, true );
	wp_enqueue_script( 'trx_addons-sc_icons', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_SHORTCODES . 'icons/icons.js'), array('jquery'), null, true );
}
