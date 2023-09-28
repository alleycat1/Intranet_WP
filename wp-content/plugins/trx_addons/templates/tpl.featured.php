<?php
$trx_addons_args = get_query_var('trx_addons_args_featured');

if ( ! isset($trx_addons_args['no_links'] ) ) {
	$trx_addons_args['no_links'] = false;
}

if ( ! post_password_required() ) {

	$trx_addons_featured_replaced = false;
	if ( ! isset($trx_addons_args['allow_theme_replace'] ) || $trx_addons_args['allow_theme_replace']) {
		$trx_addons_featured_replaced = apply_filters('trx_addons_filter_featured_image', false, $trx_addons_args);
	}

	if ( ! $trx_addons_featured_replaced ) {

		if ( ! empty( $trx_addons_args['singular'] ) ) {

			if ( is_attachment() ) {
				?>
				<div class="post_featured post_attachment<?php if (!empty($trx_addons_args['class'])) echo ' '.esc_attr($trx_addons_args['class']); ?>">
					<?php
					do_action('trx_addons_action_before_featured');
					echo wp_get_attachment_image( get_the_ID(), 'full' );
					?>
					<nav id="image-navigation" class="navigation image-navigation">
						<div class="nav-previous"><?php previous_image_link( false, '' ); ?></div>
						<div class="nav-next"><?php next_image_link( false, '' ); ?></div>
					</nav>
					<?php 
					do_action('trx_addons_action_after_featured');
					?>
				</div>

				<?php
				if ( has_excerpt() ) {
					?><div class="entry-caption"><?php the_excerpt(); ?></div><?php
				}

			} else if ( has_post_thumbnail() ) {
				?>
				<div class="post_featured<?php if (!empty($trx_addons_args['class'])) echo ' '.esc_attr($trx_addons_args['class']); ?>">
					<?php
					do_action('trx_addons_action_before_featured');
					the_post_thumbnail(trx_addons_get_thumb_size('big'), trx_addons_seo_image_params(array(
						//'alt' => get_the_title()
						))
					);
					do_action('trx_addons_action_after_featured');
					?>
				</div>
				<?php
			}

		} else {

			$trx_addons_post_format = str_replace('post-format-', '', get_post_format());
			$trx_addons_post_info = isset($trx_addons_args['post_info']) ? $trx_addons_args['post_info'] : '';
			$trx_addons_thumb_size = isset($trx_addons_args['thumb_size']) ? $trx_addons_args['thumb_size'] : trx_addons_get_thumb_size('big');
			$trx_addons_link = empty($trx_addons_args['no_links']) 
									? (!empty($trx_addons_args['link'])
										? $trx_addons_args['link']
										: get_permalink()
										)
									: '';
			$trx_addons_data_atts = '';
			if (!empty($trx_addons_args['data']) && is_array($trx_addons_args['data'])) {
				foreach($trx_addons_args['data'] as $k=>$v) {
					$trx_addons_data_atts .= ' data-'.esc_attr($k).'="'.esc_attr($v).'"';
				}
			}
			if ( has_post_thumbnail() ) {
				?><div class="post_featured<?php
					if (!empty($trx_addons_args['thumb_bg'])) echo ' post_featured_bg';
					if (!empty($trx_addons_args['class'])) echo ' '.esc_attr($trx_addons_args['class']);
					if (!empty($trx_addons_args['hover'])) echo ' trx_addons_hover trx_addons_hover_style_'.esc_attr($trx_addons_args['hover']);
				?>"<?php
					trx_addons_show_layout( $trx_addons_data_atts );
				?>><?php 
					do_action('trx_addons_action_before_featured');
					if (empty($trx_addons_args['thumb_bg'])) {
						the_post_thumbnail( $trx_addons_thumb_size, array( 
							//'alt' => get_the_title()
							)); 
					} else {
						$trx_addons_src = trx_addons_get_attachment_url( get_post_thumbnail_id( get_the_ID() ), $trx_addons_thumb_size );
						?><span class="post_thumb" style="background-image: url(<?php echo esc_url($trx_addons_src); ?>)"></span><?php
					}
					// Post formats over thumb
					if ($trx_addons_post_format == 'audio') {
						$trx_addons_audio = trx_addons_get_post_audio('', false);
						if (empty($trx_addons_audio))
							$trx_addons_audio = trx_addons_get_post_iframe('', false);
						if (!empty($trx_addons_audio)) {
							?><div class="post_audio"><?php trx_addons_show_layout($trx_addons_audio); ?></div><?php
						}
					}
					// Post info
					trx_addons_show_layout($trx_addons_post_info);
					if (strpos($trx_addons_post_info, 'class="post_link') === false && !empty($trx_addons_link)) {
						?><a class="post_link" href="<?php echo esc_url( $trx_addons_link ); ?>"></a><?php
					}
					// Hover
					if (!empty($trx_addons_args['hover']) && $trx_addons_post_format != 'audio') {
						?><div class="trx_addons_hover_mask"></div><?php
						if ($trx_addons_args['hover'] == 'zoomin') {
							$large_image_url = trx_addons_get_attachment_url( get_post_thumbnail_id(), trx_addons_get_thumb_size('big') );
							?><div class="trx_addons_hover_content">
								<a href="<?php echo !empty($trx_addons_link) ? esc_url($trx_addons_link) : '#'; ?>" class="trx_addons_hover_icon trx_addons_hover_icon_link"<?php if (!empty($trx_addons_args['link'])) echo ' target="_blank"'; ?>></a>
								<a href="<?php echo esc_url($large_image_url); ?>" class="trx_addons_hover_icon trx_addons_hover_icon_zoom"></a>
							</div><?php
						}
					}
					do_action('trx_addons_action_after_featured');
				?></div><?php

			} else if ($trx_addons_post_format == 'gallery') {
				if ( ($trx_addons_output = trx_addons_get_slider_layout(array('thumb_size' => $trx_addons_thumb_size))) != '' ) {
					?><div class="post_featured<?php if (!empty($trx_addons_args['class'])) echo ' '.esc_attr($trx_addons_args['class']); ?>"<?php
						trx_addons_show_layout( $trx_addons_data_atts );
					?>><?php
						do_action('trx_addons_action_before_featured');
						trx_addons_show_layout($trx_addons_output);
						trx_addons_show_layout($trx_addons_post_info);
						do_action('trx_addons_action_after_featured');
					?></div><?php
				}

			} else if ($trx_addons_post_format == 'image') {
				$trx_addons_image = trx_addons_get_post_image();
				if (!empty($trx_addons_image)) {
					?><div class="post_featured<?php if (!empty($trx_addons_args['class'])) echo ' '.esc_attr($trx_addons_args['class']); ?>"<?php
						trx_addons_show_layout( $trx_addons_data_atts );
					?>><?php
						do_action('trx_addons_action_before_featured');
						if (!empty($trx_addons_link)) {
							?><a href="<?php echo esc_url($trx_addons_link); ?>" aria-hidden="true"<?php if (!empty($trx_addons_args['link'])) echo ' target="_blank"'; ?>><?php
						}
						if (empty($trx_addons_args['thumb_bg'])) {
							?><img src="<?php echo esc_url(trx_addons_clear_thumb_size($trx_addons_image)); ?>" alt="<?php the_title_attribute(''); ?>"><?php
						} else {
							?><span class="post_thumb" style="background-image: url(<?php echo esc_url(trx_addons_clear_thumb_size($trx_addons_image)); ?>)"></span><?php
						}
						if (!empty($trx_addons_link)) {
							?></a><?php
						}
						trx_addons_show_layout($trx_addons_post_info);
						do_action('trx_addons_action_after_featured');
					?></div><?php
				}

			} else if ($trx_addons_post_format == 'video') {
				$trx_addons_video = trx_addons_get_post_video('', false);
				if (empty($trx_addons_video)) {
					$trx_addons_video = trx_addons_get_post_iframe('', false);
				}
				if (!empty($trx_addons_video)) {
					?><div class="post_featured<?php if (!empty($trx_addons_args['class'])) echo ' '.esc_attr($trx_addons_args['class']); ?>"<?php
						trx_addons_show_layout( $trx_addons_data_atts );
					?>><?php
						do_action('trx_addons_action_before_featured');
						if ( ! empty( $trx_addons_args['popup'] ) ) {
							$video = explode(
											'<!-- .sc_layouts_popup -->',
											trx_addons_get_video_layout( apply_filters( 'trx_addons_filter_get_video_layout_args', array(
												'link'  => '',
												'embed' => $trx_addons_video,
												'cover' => get_post_thumbnail_id(),
												'show_cover' => false,
												'popup' => true
											), 'featured' ) )
									);
							if ( ! empty( $video[0] ) && ! empty( $video[1] ) ) {
								trx_addons_show_layout($video[0]);
								trx_addons_show_layout($video[1]);
							}
						} else {
							trx_addons_show_layout( $trx_addons_video );
						}
						trx_addons_show_layout( $trx_addons_post_info );
						do_action('trx_addons_action_after_featured');
					?></div><?php
				}

			} else if ($trx_addons_post_format == 'audio') {
				$trx_addons_audio = trx_addons_get_post_audio('', false);
				if (empty($trx_addons_audio)) {
					$trx_addons_audio = trx_addons_get_post_iframe('', false);
				}
				if (!empty($trx_addons_audio)) {
					?><div class="post_featured<?php if (!empty($trx_addons_args['class'])) echo ' '.esc_attr($trx_addons_args['class']); ?>"<?php
						trx_addons_show_layout( $trx_addons_data_atts );
					?>><?php
						do_action('trx_addons_action_before_featured');
						trx_addons_show_layout($trx_addons_audio);
						trx_addons_show_layout($trx_addons_post_info);
						do_action('trx_addons_action_after_featured');
					?></div><?php
				}
			}

		}
	}
}
