<?php
/**
 * The style "default" of the Twitter
 *
 * @package ThemeREX Addons
 * @since v1.4.3
 */

$widget_args = get_query_var('trx_addons_args_widget_twitter');
$twitter_api = isset($widget_args['twitter_api']) ? $widget_args['twitter_api'] : 'token';
$twitter_username = $widget_args['twitter_username'] = isset($widget_args['twitter_username']) ? $widget_args['twitter_username'] : '';
$twitter_count = $widget_args['twitter_count'] = isset($widget_args['twitter_count']) ? $widget_args['twitter_count'] : 3;
$follow = isset($widget_args['follow']) ? (int) $widget_args['follow'] : 0;
$widget_args['columns'] = empty($widget_args['columns']) ? $twitter_count : min($widget_args['columns'], $twitter_count);
$widget_args['columns'] = max(1, min(12, (int) $widget_args['columns']));
$widget_args['slider'] = !empty($widget_args['slider']) && $twitter_count > $widget_args['columns'];
$widget_args['slides_space'] = empty($widget_args['slides_space']) ? 0 : max(0, (int) $widget_args['slides_space']);

?><div class="widget_content"><?php

	if ( $twitter_api == 'token' || $twitter_api == 'bearer' ) {
		?><ul class="sc_twitter sc_twitter_list"><?php
			$cnt = 0;
			if (is_array($widget_args['data']) && count($widget_args['data']) > 0) {
				foreach ($widget_args['data'] as $tweet) {
					//if (substr($tweet['text'], 0, 1)=='@') continue;
					?><li<?php if ( $cnt == $twitter_count - 1 ) echo ' class="last"'; ?>><?php
						$username = ! empty( $tweet['user']['screen_name'] ) ? $tweet['user']['screen_name'] : '';
						if ( ! empty( $username ) ) {
							?><a href="<?php echo esc_url('https://twitter.com/' . trim( $username ) ); ?>" class="username" target="_blank"><?php
								echo esc_html( '@' . $username );
							?></a> <?php
						}
						echo force_balance_tags( trx_addons_prepare_twitter_text( $tweet ) );
					?></li><?php
					if ( ++$cnt >= $twitter_count ) break;
				}
			}
		?></ul><?php

	} else if ( $twitter_api == 'embed' ) {
		trx_addons_widget_twitter_show_embed_layout( $widget_args );
	}

	if ($follow) {
		?><a href="<?php echo esc_url('//twitter.com/'.trim($twitter_username)); ?>" class="widget_twitter_follow"><?php esc_html_e('Follow us', 'trx_addons'); ?></a><?php
	}

?></div>