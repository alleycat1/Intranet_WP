<?php
/**
 * The style "light" of the Services
 *
 * @package ThemeREX Addons
 * @since v1.6.13
 */

$args = get_query_var('trx_addons_args_sc_services');
$number = get_query_var('trx_addons_args_item_number');

$meta = get_post_meta(get_the_ID(), 'trx_addons_options', true);
if (!is_array($meta)) $meta = array();
$meta['price'] = apply_filters( 'trx_addons_filter_custom_meta_value', !empty($meta['price']) ? $meta['price'] : '', 'price' );

$link = empty($args['no_links'])
			? (!empty($meta['link']) ? $meta['link'] : get_permalink())
			: '';

if (empty($args['id'])) $args['id'] = 'sc_services_'.str_replace('.', '', mt_rand());
if (empty($args['featured'])) $args['featured'] = 'image';
if (empty($args['featured_position'])) $args['featured_position'] = 'top';

$svg_present = false;
$price_showed = false;

if (!empty($args['slider'])) {
	?><div class="slider-slide swiper-slide"><?php
} else if ((int) $args['columns'] > 1) {
	?><div class="<?php echo esc_attr(trx_addons_get_column_class(1, $args['columns'], !empty($args['columns_tablet']) ? $args['columns_tablet'] : '', !empty($args['columns_mobile']) ? $args['columns_mobile'] : '')); ?>"><?php
}
?>
<div data-post-id="<?php the_ID(); ?>" <?php post_class( apply_filters( 'trx_addons_filter_services_item_class',
			'sc_services_item sc_item_container post_container'
			. (empty($post_link) ? ' no_links' : '')
			. (isset($args['hide_excerpt']) && (int)$args['hide_excerpt'] > 0 ? ' without_content' : ' with_content')
			. (!trx_addons_is_off($args['featured']) ? ' with_'.esc_attr($args['featured']) : '')
			. ' sc_services_item_featured_'.esc_attr($args['featured']!='none' ? $args['featured_position'] : 'none'),
			$args )
			);
	trx_addons_add_blog_animation('services', $args);
	if (!empty($args['popup'])) {
		?> data-post_id="<?php echo esc_attr(get_the_ID()); ?>"<?php
		?> data-post_type="<?php echo esc_attr(TRX_ADDONS_CPT_SERVICES_PT); ?>"<?php
	}
?>>
	<?php
	do_action( 'trx_addons_action_services_item_start', $args );
	// Featured image or icon
	if ($args['featured'] != 'none') {
		do_action( 'trx_addons_action_services_item_before_featured', $args );
		if ( has_post_thumbnail() && $args['featured']=='image') {
			trx_addons_get_template_part('templates/tpl.featured.php',
											'trx_addons_args_featured',
											apply_filters('trx_addons_filter_args_featured', array(
															'class' => 'sc_services_item_thumb',
															'thumb_size' => apply_filters('trx_addons_filter_thumb_size', trx_addons_get_thumb_size((int) $args['columns'] > 2 ? 'medium' : 'big'), 'services-light'),
															'no_links' => empty($link),
															'link' => $link,
															'post_info' => !empty($meta['price']) 
																				? '<span class="sc_services_item_price">'.trim($meta['price']).'</span>'
																				: ''
															),
															'services-light'
															)
										);
			$price_showed = true;
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
			} else if (!empty($args['icons_animation']) && ($svg = trx_addons_get_file_dir('css/icons.svg/'.trx_addons_clear_icon_name($meta['icon']).'.svg')) != '')
				$svg_present = true;
			echo !empty($link) 
				? '<a href="'.esc_url($link).'"'.(!empty($meta['link']) ? ' target="_blank"' : '') 
				: '<span';
			?> id="<?php echo esc_attr($args['id'].'_'.trim($meta['icon']).'_'.trim($number)); ?>"
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
			?>>
            <span class="service_extra_subtitle">
            <?php

            $title = get_the_title();

            $words = explode(" ", $title);
            $newwords = '';
            foreach ($words as $wordnr => $word){
                $newwords .= mb_substr($word, 0, 1);
            }
            pubzinne_show_layout(mb_substr($newwords,0,4));
            ?>

            </span>
            <?php
				if (!empty($svg)) {
					trx_addons_show_layout(trx_addons_get_svg_from_file($svg));
				} else if (!empty($img)) {
					$attr = trx_addons_getimagesize($img);
					?><img class="sc_icon_as_image" src="<?php echo esc_url($img); ?>" alt="<?php esc_attr_e('Icon', 'pubzinne'); ?>"<?php echo (!empty($attr[3]) ? ' '.trim($attr[3]) : ''); ?>><?php
				}
			echo !empty($link) 
				? '</a>'
				: '</span>';
		} else if ($args['featured']=='pictogram' && !empty($meta['image'])) {
			echo !empty($link) 
				? '<a href="'.esc_url($link).'"'.(!empty($meta['link']) ? ' target="_blank"' : '') 
				: '<span';
			?> class="sc_services_item_pictogram"><?php
			$attr = trx_addons_getimagesize($meta['image']);
			?><img src="<?php echo esc_url($meta['image']); ?>" alt="<?php esc_attr_e('Icon', 'pubzinne'); ?>"<?php echo (!empty($attr[3]) ? ' '.trim($attr[3]) : ''); ?>><?php
			echo !empty($link) 
				? '</a>' 
				: '</span>';
		} else if ($args['featured']=='number') {
			?><span class="sc_services_item_number"><?php
				printf("%02d", $number);
			?></span><?php
		}
		do_action( 'trx_addons_action_services_item_after_featured', $args );
	}
	?>	
	<div class="sc_services_item_info">
		<?php do_action( 'trx_addons_action_services_item_header_start', $args ); ?>
		<div class="sc_services_item_header"><?php
			do_action( 'trx_addons_action_services_item_header_inner_start', $args );
			$title_tag = $args['featured']=='number' ? 'h4' : 'h6';
			if ($args['featured']=='number' && $args['featured_position']=='top') {
				do_action( 'trx_addons_action_services_item_before_subtitle', $args );
				?><div class="sc_services_item_subtitle"><?php
					trx_addons_show_layout(trx_addons_get_post_terms(', ', get_the_ID(), trx_addons_get_post_type_taxonomy()));
				?></div><?php
				do_action( 'trx_addons_action_services_item_after_subtitle', $args );
			}
			do_action( 'trx_addons_action_services_item_before_title', $args );
			?>
			<<?php echo esc_html($title_tag); ?> class="sc_services_item_title entry-title<?php if (!$price_showed && !empty($meta['price'])) echo ' with_price'; ?>"><?php
				if (!empty($link)) {
					?><a href="<?php echo esc_url($link); ?>"<?php if (!empty($meta['link'])) echo ' target="_blank"'; ?>><?php
				}
				the_title();
				// Price
				if (!$price_showed && !empty($meta['price'])) {
					?><div class="sc_services_item_price"><?php trx_addons_show_layout($meta['price']); ?></div><?php
				}
				if (!empty($link)) {
					?></a><?php
				}
			?></<?php echo esc_html($title_tag); ?>><?php
			do_action( 'trx_addons_action_services_item_after_title', $args );
			if ($args['featured']!='number' || $args['featured_position']!='top') {
				do_action( 'trx_addons_action_services_item_before_subtitle', $args );
				do_action( 'trx_addons_action_services_item_after_subtitle', $args );
			}
			do_action( 'trx_addons_action_services_item_header_inner_end', $args );
		?></div><?php
		if (!isset($args['hide_excerpt']) || (int)$args['hide_excerpt']==0) {
			do_action( 'trx_addons_action_services_item_before_content', $args );
			?><div class="sc_services_item_content"><?php the_excerpt(); ?></div><?php
			do_action( 'trx_addons_action_services_item_after_content', $args );
			if (!empty($link) && !empty($args['more_text'])) {
				?><div class="sc_services_item_button sc_item_button"><a href="<?php echo esc_url($link); ?>"<?php if (!empty($meta['link'])) echo ' target="_blank"'; ?> class="<?php echo esc_attr(apply_filters('trx_addons_filter_sc_item_link_classes', 'sc_button', 'sc_services', $args)); ?>"><?php echo esc_html($args['more_text']); ?></a></div><?php
			}
		}
		do_action( 'trx_addons_action_services_item_header_end', $args );
	?></div>
	<?php do_action( 'trx_addons_action_services_item_end', $args ); ?>
</div><?php
if (!empty($args['slider']) || (int) $args['columns'] > 1) {
	?></div><?php
}
if (trx_addons_is_on(trx_addons_get_option('debug_mode')) && $svg_present) {
	wp_enqueue_script( 'vivus', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_SHORTCODES . 'icons/vivus.js'), array('jquery'), null, true );
	wp_enqueue_script( 'trx-addons-sc-icons', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_SHORTCODES . 'icons/icons.js'), array('jquery'), null, true );
}
