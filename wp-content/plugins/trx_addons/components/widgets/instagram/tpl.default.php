<?php
/**
 * The style "default" of the Widget "Instagram"
 *
 * @package ThemeREX Addons
 * @since v1.6.47
 */

$args = get_query_var('trx_addons_args_widget_instagram');
extract($args);

if ( empty( $ratio ) ) $ratio = 'none';

// Before widget (defined by themes)
trx_addons_show_layout($before_widget);
			
// Widget title if one was input (before and after defined by themes)
trx_addons_show_layout($title, $before_title, $after_title);

$resp = trx_addons_widget_instagram_get_recent_photos(array(
		'demo' => ! empty($demo) ? $demo : 0,
		'demo_files' => ! empty($demo_files) ? $demo_files : array(),
		'demo_thumb_size' => ! empty($demo_thumb_size) ? $demo_thumb_size : '',
		'media' => ! empty($media) ? $media : 'all',
		'hashtag' =>  ! empty($hashtag) ? $hashtag : '',
		'count' => max(1, (int) $count)
));

// Widget body
?><div class="widget_instagram_wrap<?php if ( ! empty( $type ) ) echo ' widget_instagram_type_' . esc_attr( $type ); ?>">
	<div class="widget_instagram_images widget_instagram_images_columns_<?php
				echo esc_attr( $columns );
				if ( ! empty( $columns_gap ) ) {
					echo ' ' . esc_attr( trx_addons_add_inline_css_class( 'margin-right:-'.trx_addons_prepare_css_value( $columns_gap ) ) );
				}
				?>"<?php
		// If images are not available from server side - add params to get images from client side
		if ( empty( $resp['data'] ) || ! is_array( $resp['data'] ) || count( $resp['data'] ) == 0 ) {
			global $TRX_ADDONS_STORAGE;
			if ( empty($TRX_ADDONS_STORAGE['instagram_hash']) ) $TRX_ADDONS_STORAGE['instagram_hash'] = array();
			if ( empty($TRX_ADDONS_STORAGE['instagram_hash'][$hashtag]) ) $TRX_ADDONS_STORAGE['instagram_hash'][$hashtag] = 0;
			$TRX_ADDONS_STORAGE['instagram_hash'][$hashtag]++;
			$hash = md5( $hashtag . '-' . $TRX_ADDONS_STORAGE['instagram_hash'][$hashtag] );
			set_transient( sprintf( 'trx_addons_instagram_args_%s', $hash ), $args, 60 );       // Store to the cache for 60s
			?>
			data-instagram-load="1"
			data-instagram-hash="<?php echo esc_attr( $hash ); ?>"
			data-instagram-hashtag="<?php echo esc_attr( $hashtag ); ?>"
			<?php
		}
	?>><?php
		// If images are available from server side
		if ( ! empty( $resp['data'] ) && is_array( $resp['data'] ) && count( $resp['data'] ) > 0 ) {
			$user = '';
			$total = 0;
			foreach( $resp['data'] as $v ) {
				$total++;
				if ( empty($user) && !empty($v['user']['username']) ) {
					$user = $v['user']['username'];
				}
				$class = trx_addons_add_inline_css_class(
								'width:'.round(100/$columns, 4).'%;'
								. ( ! empty( $columns_gap )
									? 'padding: 0 ' . trx_addons_prepare_css_value( $columns_gap ) . ' ' . trx_addons_prepare_css_value( $columns_gap ) . ' 0;'
									: ''
									)
								);
				$thumb_size = apply_filters( 'trx_addons_filter_instagram_thumb_size', 'standard_resolution' );
				$thumb_layout = apply_filters( 'trx_addons_filter_instagram_thumb_item',
					sprintf(
						'<div class="widget_instagram_images_item_wrap %6$s">'
							. ( $links != 'none' && ( $v['type'] != 'video' || $links == 'instagram' )
								? '<a href="%5$s"' . ( $links == 'instagram' ? ' target="_blank"' : '' )
								: '<div'
								)
							. ' title="%4$s"'
							. ' rel="magnific"'
							. ' class="widget_instagram_images_item widget_instagram_images_item_type_'.esc_attr($v['type'])
								. ( ! empty( $v['images'][$thumb_size]['url'] ) && ( $v['type'] == 'video' || $ratio != 'none' ) 	// && $links != 'none'
										? ' ' . trx_addons_add_inline_css_class('background-image:url(' . $v['images'][$thumb_size]['url'] . ');') // esc_url() is damage url from Instagram
										: ''
									)
								. '"'
							. ( $ratio != 'none' ? ' data-ratio="' . esc_attr( $ratio ) . '"' : '')
						. '>'
								. ( $v['type'] == 'video' && ! empty( $v['videos'] )
									? trx_addons_get_video_layout( apply_filters( 'trx_addons_filter_get_video_layout_args', array(
											'link' => ! empty( $v['videos'][$thumb_size]['url'] ) ? $v['videos'][$thumb_size]['url'] : '',
											'cover' => ! empty( $v['images'][$thumb_size]['url'] ) && $links != 'none'
															? $v['images'][$thumb_size]['url']
															: '',
											'show_cover' => false,	//$links != 'none',
											'popup' => $links == 'popup'
										), 'instagram.default' ) )
									: ( $ratio == 'none' ? '<img src="%1$s" width="%2$d" height="%3$d" alt="%4$s">' : '' )
									)
								. '<span class="widget_instagram_images_item_counters">'
									. ( isset( $v['likes']['count'] ) && $v['likes']['count'] >= 0
										? '<span class="widget_instagram_images_item_counter_likes trx_addons_icon-heart' . (empty($v['likes']['count']) ? '-empty' : '') . '">'
											. esc_attr($v['likes']['count'])
											. '</span>'
										: '' )
									. ( isset( $v['comments']['count'] ) && $v['comments']['count'] >= 0
										? '<span class="widget_instagram_images_item_counter_comments trx_addons_icon-comment' . (empty($v['comments']['count']) ? '-empty' : '') . '">'
											. esc_attr($v['comments']['count'])
											. '</span>'
										: '' )
								. '</span>'
							. ( $links != 'none' && ( $v['type'] != 'video' || $links == 'instagram' )
								? '</a>'
								: '</div>'
								)
						. '</div>',
						! empty( $v['images'][$thumb_size]['url'] ) ? esc_url($v['images'][$thumb_size]['url']) : '',
						! empty( $v['images'][$thumb_size]['width'] ) ? $v['images'][$thumb_size]['width'] : '',
						! empty( $v['images'][$thumb_size]['height'] ) ? $v['images'][$thumb_size]['height'] : '',
						! empty( $v['caption']['text'] ) ? esc_attr( $v['caption']['text'] ) : '',
						empty( $demo ) && $links == 'instagram'
							? esc_url( $v['link'] )
							: ( ! empty( $v['images'][$thumb_size]['url'] )
								? $v['images'][$thumb_size]['url']
								: '' ),
						$class
					),
					$v,
					$args
				);
				if ( $v['type'] == 'video' && ! empty( $v['videos'] ) ) {
					// Prevent a script Media Elements to be inited on the video
					$thumb_layout = str_replace( '<video ', '<video class="inited" ', $thumb_layout );
					// Prevent a video to be opened on click
					$thumb_layout = str_replace( 'video_hover', 'video_hover inited', $thumb_layout );
				}
				trx_addons_show_layout( $thumb_layout );
				if ( $total >= $count ) break;
			}
		} else {
			wp_enqueue_script( 'trx_addons-widget_instagram_load', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_WIDGETS . 'instagram/instagram_load.js'), array('jquery'), null, true );
		}
	?></div><?php	

	// Button 'Follow me' under images
	if ( $follow && ( ! empty( $follow_link ) || ( empty( $demo ) && ( ! empty( $hashtag ) || ! empty( $user ) ) ) ) ) {
		$url = ! empty( $follow_link )
				? esc_url( $follow_link )
				: 'https://www.instagram.com/'
						. ( ! empty( $hashtag ) && $hashtag[0] == '#'
							? 'explore/tags/' . substr( $hashtag, 1 )			// Get output by hashtag
							: trim( ! empty( $hashtag ) ? $hashtag : $user )	// Get output by username
							)
						. '/';
		?><div class="widget_instagram_follow_link_wrap"><a href="<?php echo esc_url($url); ?>"
					class="<?php echo esc_attr(apply_filters('trx_addons_filter_widget_instagram_link_classes', 'widget_instagram_follow_link sc_button', $args)); ?>"
					target="_blank"><?php
			if ( ! empty( $hashtag ) && $hashtag[0] == '#' ) {
				esc_html_e('View more', 'trx_addons');
			} else {
				esc_html_e('Follow Me', 'trx_addons');
			}
		?></a></div><?php
	}
?></div><?php	

// After widget (defined by themes)
trx_addons_show_layout($after_widget);
