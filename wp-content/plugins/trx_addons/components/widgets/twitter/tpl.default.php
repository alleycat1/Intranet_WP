<?php
/**
 * The style "default" of the Twitter
 *
 * @package ThemeREX Addons
 * @since v1.4.3
 */

$widget_args = get_query_var('trx_addons_args_widget_twitter');
$twitter_api = isset($widget_args['twitter_api']) ? $widget_args['twitter_api'] : 'token';
$twitter_username = isset($widget_args['twitter_username']) ? $widget_args['twitter_username'] : '';	
$twitter_count = isset($widget_args['twitter_count']) ? $widget_args['twitter_count'] : '';	
$follow = isset($widget_args['follow']) ? (int) $widget_args['follow'] : 0;
$widget_args['columns'] = $twitter_api == 'embed' ? 1 : ( $widget_args['columns'] < 1 ? $twitter_count : min($widget_args['columns'], $twitter_count) );
$widget_args['columns'] = max(1, min(12, (int) $widget_args['columns']));
if (!empty($widget_args['columns_tablet'])) $widget_args['columns_tablet'] = $twitter_api == 'embed' ? 1 : max(1, min(12, (int) $widget_args['columns_tablet']));
if (!empty($widget_args['columns_mobile'])) $widget_args['columns_mobile'] = $twitter_api == 'embed' ? 1 : max(1, min(12, (int) $widget_args['columns_mobile']));
$widget_args['slider'] = $twitter_api != 'embed' && $widget_args['slider'] > 0 && $twitter_count > $widget_args['columns'];
$widget_args['slides_space'] = max(0, (int) $widget_args['slides_space']);

?><div class="widget_content">
	<div class="sc_twitter sc_twitter_<?php
				echo esc_attr($widget_args['type']);
				?>"><?php

		if ($widget_args['slider']) {
			trx_addons_sc_show_slider_wrap_start('sc_twitter', $widget_args);
		} else if ($widget_args['columns'] > 1) {
			?><div class="sc_twitter_columns sc_item_columns <?php
				echo esc_attr(trx_addons_get_columns_wrap_class())
					. ' columns_padding_bottom'
					. esc_attr( trx_addons_add_columns_in_single_row( $widget_args['columns'], is_array($widget_args['data']) ? $widget_args['data'] : 0 ) );
			?>"><?php
		} else {
			?><div class="sc_twitter_content sc_item_content"><?php
		}	

		if ( $twitter_api == 'token' || $twitter_api == 'bearer' ) {
			$cnt = 0;

			if (is_array($widget_args['data']) && count($widget_args['data']) > 0) {
				foreach ($widget_args['data'] as $tweet) {
					//if (substr($tweet['text'], 0, 1)=='@') continue;
					if ($widget_args['slider']) {
						?><div class="slider-slide swiper-slide"><?php
					} else if ($widget_args['columns'] > 1) {
						?><div class="<?php echo esc_attr(trx_addons_get_column_class(1, $widget_args['columns'], !empty($widget_args['columns_tablet']) ? $widget_args['columns_tablet'] : '', !empty($widget_args['columns_mobile']) ? $widget_args['columns_mobile'] : '')); ?>"><?php
					}
					?>
					<div class="sc_twitter_item<?php if ( $cnt == $twitter_count - 1 ) echo ' last'; ?>">
						<div class="sc_twitter_item_icon icon-twitter"></div>
						<div class="sc_twitter_item_content"><?php
							$username = ! empty( $tweet['user']['screen_name'] ) ? $tweet['user']['screen_name'] : '';
							if ( ! empty( $username ) ) {
								?><a href="<?php echo esc_url('https://twitter.com/' . trim( $username ) ); ?>" class="username" target="_blank"><?php
									echo esc_html( '@' . $username );
								?></a> <?php
							}
							echo force_balance_tags( trx_addons_prepare_twitter_text( $tweet ) );
						?></div>
					</div>
					<?php
					if ( $widget_args['slider'] || $widget_args['columns'] > 1 ) {
						?></div><?php
					}
					if ( ++$cnt >= $twitter_count ) break;
				}
			}

		} else if ( $twitter_api == 'embed' ) {
			trx_addons_widget_twitter_show_embed_layout( $widget_args );
		}

		?></div><?php

		if ($widget_args['slider']) {
			trx_addons_sc_show_slider_wrap_end('sc_twitter', $widget_args);
		}

	?></div><?php

    if ($follow) {
        ?><a href="<?php echo esc_url('//twitter.com/'.trim($twitter_username)); ?>" class="widget_twitter_follow"><?php esc_html_e('Follow us', 'trx_addons'); ?></a><?php
    }

?></div>