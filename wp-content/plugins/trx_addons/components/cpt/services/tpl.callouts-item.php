<?php
/**
 * The style "default" of the Services
 *
 * @package ThemeREX Addons
 * @since v1.4
 */

$args = get_query_var('trx_addons_args_sc_services');
$number = get_query_var('trx_addons_args_item_number');

$meta = (array)get_post_meta(get_the_ID(), 'trx_addons_options', true);
$link = empty($args['no_links'])
			? (!empty($meta['link']) ? $meta['link'] : get_permalink())
			: '';
if (empty($args['featured'])) $args['featured'] = 'image';
if (empty($args['featured_position'])) $args['featured_position'] = 'top';
$svg_present = false;

$add_to_cart_link = ! empty( $meta['price'] ) 
						? apply_filters( 'trx_addons_filter_cpt_add_to_cart_link', '', 'sc_services_item_price_link', apply_filters( 'trx_addons_filter_cpt_add_to_cart_label', $meta['price'], 'services-callouts' ) )
						: '';

if (!empty($args['slider'])) {
	?><div class="slider-slide swiper-slide"><?php
} else if ($args['columns'] > 1) {
	?><div class="<?php echo esc_attr(trx_addons_get_column_class(1, $args['columns'], !empty($args['columns_tablet']) ? $args['columns_tablet'] : '', !empty($args['columns_mobile']) ? $args['columns_mobile'] : '')); ?>"><?php
}
?>
<div data-post-id="<?php the_ID(); ?>" <?php post_class( apply_filters( 'trx_addons_filter_services_item_class',
			'sc_services_item sc_item_container post_container'
			. (empty($link) ? ' no_links' : '')
			. (isset($args['hide_excerpt']) && (int)$args['hide_excerpt']>0 ? ' without_content' : ' with_content')
			. (!trx_addons_is_off($args['featured']) ? ' with_'.esc_attr($args['featured']) : '')
			. ' sc_services_item_featured_'.esc_attr($args['featured']!='none' ? $args['featured_position'] : 'none'),
			$args )
			);
	trx_addons_add_blog_animation('services', $args);
	if (!empty($args['popup'])) {
		?> data-post_id="<?php echo esc_attr(get_the_ID()); ?>"<?php
		?> data-post_type="<?php echo esc_attr(TRX_ADDONS_CPT_SERVICES_PT); ?>"<?php
	}
?>><?php
	do_action( 'trx_addons_action_services_item_start', $args );
	// Featured image
	if ($args['featured'] != 'none') {
		?><div class="sc_services_item_marker_bg"></div><?php
		if ( has_post_thumbnail() && $args['featured']=='image') {
			?><div class="sc_services_item_thumb sc_services_item_marker" style="background-image: url(<?php
				$trx_addons_attachment_src = wp_get_attachment_image_src( 
														get_post_thumbnail_id( get_the_ID() ),
														! empty( $args['thumb_size'] )
															? $args['thumb_size']
															: apply_filters('trx_addons_filter_thumb_size', trx_addons_get_thumb_size('masonry'), 'services-callouts')
														);
				if (!empty($trx_addons_attachment_src[0]))
					echo esc_url($trx_addons_attachment_src[0]);
				?>);"></div><?php
			?><div class="sc_services_item_thumb sc_services_item_marker sc_services_item_marker_back" style="background-image: url(<?php
				if (!empty($trx_addons_attachment_src[0]))
					echo esc_url($trx_addons_attachment_src[0]);
				?>);"></div><?php
		
		// Icon
		} else if ($args['featured']=='icon' && !empty($meta['icon'])) {
			$svg = $img = '';
			if (trx_addons_is_url($meta['icon'])) {
				if (strpos($meta['icon'], '.svg') !== false) {
					$svg = $meta['icon'];
					$svg_present = !empty($args['icons_animation']);
				} else {
					$img = $meta['icon'];
				}
				$meta['icon'] = basename($meta['icon']);
			} else if (!empty($args['icons_animation']) && ($svg = trx_addons_get_file_dir('css/icons.svg/'.trx_addons_clear_icon_name($meta['icon']).'.svg')) != '') {
				$svg_present = true;
			}
			echo !empty($link) 
				? '<a href="'.esc_url($link).'"'.(!empty($meta['link']) && trx_addons_is_external_url($meta['link']) ? ' target="_blank"' : '')
				: '<span';
			?>
			class="sc_services_item_icon sc_services_item_marker<?php
						if ($svg_present) echo ' sc_icon_animation';
						echo !empty($svg) 
								? ' sc_icon_type_svg'
								: (!empty($img) 
									? ' sc_icon_type_images'
									: ' sc_icon_type_icons ' . esc_attr($meta['icon'])
									);
						?>"<?php
				 if (!empty($meta['icon_color'])) {
					 echo ' style="background-color:'.esc_attr($meta['icon_color']).'"';
				 }
			?>><?php
				if (!empty($svg)) {
					trx_addons_show_layout(trx_addons_get_svg_from_file($svg));
				} else if (!empty($img)) {
					$attr = trx_addons_getimagesize($img);
					?><img class="sc_icon_as_image" src="<?php echo esc_url($img); ?>" alt="<?php esc_attr_e('Icon', 'trx_addons'); ?>"<?php echo (!empty($attr[3]) ? ' '.trim($attr[3]) : ''); ?>><?php
				}
			echo !empty($link) 
				? '</a><a href="'.esc_url($link).'"'.(!empty($meta['link']) && trx_addons_is_external_url($meta['link']) ? ' target="_blank"' : '')
				: '</span><span';
			?> class="sc_services_item_icon sc_services_item_marker sc_services_item_marker_back<?php
						echo !empty($svg)
								? ' sc_icon_type_svg'
								: (!empty($img) 
									? ' sc_icon_type_images'
									: ' sc_icon_type_icons ' . esc_attr($meta['icon'])
									);
						?>"
				 ><?php
				if (!empty($svg)) {
					trx_addons_show_layout(trx_addons_get_svg_from_file($svg));
				} else if (!empty($img)) {
					$attr = trx_addons_getimagesize($img);
					?><img class="sc_icon_as_image" src="<?php echo esc_url($img); ?>" alt="<?php esc_attr_e('Icon', 'trx_addons'); ?>"<?php echo (!empty($attr[3]) ? ' '.trim($attr[3]) : ''); ?>><?php
				}
			echo !empty($link) 
				? '</a>' 
				: '</span>';

		// Pictogram
		} else if ($args['featured']=='pictogram' && !empty($meta['image'])) {
			echo !empty($link) 
				? '<a href="'.esc_url($link).'"'.(!empty($meta['link']) && trx_addons_is_external_url($meta['link']) ? ' target="_blank"' : '') 
				: '<span';
			?> class="sc_services_item_pictogram sc_services_item_marker"><?php
			$attr = trx_addons_getimagesize($meta['image']);
			?><img src="<?php echo esc_url($meta['image']); ?>" alt="<?php esc_attr_e('Icon', 'trx_addons'); ?>"<?php echo (!empty($attr[3]) ? ' '.trim($attr[3]) : ''); ?>><?php
			echo !empty($link) 
				? '</a><a href="'.esc_url($link).'"'.(!empty($meta['link']) && trx_addons_is_external_url($meta['link']) ? ' target="_blank"' : '') 
				: '</span><span';
			?> class="sc_services_item_pictogram sc_services_item_marker sc_services_item_marker_back"><?php
			?><img src="<?php echo esc_url($meta['image']); ?>" alt="<?php esc_attr_e('Icon', 'trx_addons'); ?>"<?php echo (!empty($attr[3]) ? ' '.trim($attr[3]) : ''); ?>><?php
			echo !empty($link) 
				? '</a>' 
				: '</span>';
	
		// Number
		} else {
			?><span class="sc_services_item_number sc_services_item_marker"><?php
				printf("%02d", $number);
			?></span><?php
			?><span class="sc_services_item_number sc_services_item_marker sc_services_item_marker_back"><?php
				printf("%02d", $number);
			?></span><?php
		}
	}
	do_action( 'trx_addons_action_services_item_after_featured', $args );
	?><div class="sc_services_item_info">
		<?php do_action( 'trx_addons_action_services_item_header_start', $args ); ?>
		<div class="sc_services_item_header">
			<?php do_action( 'trx_addons_action_services_item_header_inner_start', $args ); ?>
			<h4 class="sc_services_item_title"><?php
				if (!empty($link)) {
					?><a href="<?php echo esc_url($link); ?>"<?php if (!empty($meta['link']) && trx_addons_is_external_url($meta['link'])) echo ' target="_blank"'; ?>><?php
				}
				the_title();
				// Price
				if ( ! empty( $meta['price'] ) ) {
					?><div class="sc_services_item_price"><?php
						trx_addons_show_layout( ! empty( $add_to_cart_link ) ? $add_to_cart_link : trim( $meta['price'] ) );
					?></div><?php
				}
				if (!empty($link)) {
					?></a><?php
				}
			?></h4>
			<?php do_action( 'trx_addons_action_services_item_after_title', $args ); ?>
			<div class="sc_services_item_subtitle"><?php
				$terms = trx_addons_get_post_terms(', ', get_the_ID(), trx_addons_get_post_type_taxonomy());
				if (empty($link)) $terms = trx_addons_links_to_span($terms);
				trx_addons_show_layout($terms);
			?></div>
			<?php do_action( 'trx_addons_action_services_item_header_inner_end', $args ); ?>
		</div><?php
		if (!isset($args['hide_excerpt']) || (int)$args['hide_excerpt']==0) {
			?><div class="sc_services_item_content"><?php the_excerpt(); ?></div><?php
			if (!empty($link) && !empty($args['more_text'])) {
				?><div class="sc_services_item_button sc_item_button"><a href="<?php echo esc_url($link); ?>"<?php if (!empty($meta['link']) && trx_addons_is_external_url($meta['link'])) echo ' target="_blank"'; ?> class="<?php echo esc_attr(apply_filters('trx_addons_filter_sc_item_link_classes', 'sc_button sc_button_simple', 'sc_services', $args)); ?>"><?php echo esc_html($args['more_text']); ?></a></div><?php
			}
		}
		do_action( 'trx_addons_action_services_item_header_end', $args );
	?></div>
	<?php do_action( 'trx_addons_action_services_item_end', $args ); ?>
</div><?php
if (!empty($args['slider']) || $args['columns'] > 1) {
	?></div><?php
}
if ( $svg_present ) {
	wp_enqueue_script( 'vivus', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_SHORTCODES . 'icons/vivus.js'), array('jquery'), null, true );
	wp_enqueue_script( 'trx_addons-sc_icons', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_SHORTCODES . 'icons/icons.js'), array('jquery'), null, true );
}
