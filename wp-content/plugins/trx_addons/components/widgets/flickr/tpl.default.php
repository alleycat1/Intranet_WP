<?php
/**
 * The style "default" of the Widget "Flickr"
 *
 * @package ThemeREX Addons
 * @since v1.6.10
 */

$args = get_query_var('trx_addons_args_widget_flickr');
extract($args);
		
// Before widget (defined by themes)
trx_addons_show_layout($before_widget);
			
// Widget title if one was input (before and after defined by themes)
trx_addons_show_layout($title, $before_title, $after_title);
	
// Widget body
?><div class="flickr_images flickr_columns_<?php
	echo esc_attr($flickr_columns);
	if ($flickr_columns_gap > 0) {
		echo ' ' . esc_attr(trx_addons_add_inline_css_class('margin-right:-'.trx_addons_prepare_css_value($flickr_columns_gap).' !important;'));
	}
?>"><?php
	if (!empty($flickr_username) && !empty($flickr_api_key)) {
		$flickr_count = max(1, $flickr_count);
		$flickr_cache = sprintf("trx_addons_flickr_data_%s_%d",$flickr_username, $flickr_count);
		$resp = get_transient($flickr_cache);
		if (empty($resp)) {
			$resp = trx_addons_fgc('https://api.flickr.com/services/rest'
										. '?method=flickr.people.getPhotos'
										. '&user_id='.urlencode($flickr_username)
										. '&per_page='.intval($flickr_count)
										. '&api_key='.urlencode($flickr_api_key)
										. '&format=json'
										. '&nojsoncallback=1');
			if (substr($resp, 0, 1) == '{') 
				set_transient($flickr_cache, $resp, 60*60);
		}
		if (substr($resp, 0, 1) == '{') {
			try {
				$resp = json_decode($resp, true);
			} catch(Exception $e) {
				$resp = array();
			}
			if ($resp['stat']=='ok' && !empty($resp['photos']['photo']) && is_array($resp['photos']['photo'])) {
				foreach($resp['photos']['photo'] as $v) {
					$url = sprintf('https://farm%1$s.staticflickr.com/%2$s/%3$s_%4$s', $v['farm'], $v['server'], $v['id'], $v['secret']);
					$class = trx_addons_add_inline_css_class(
								'width:'.round(100/$flickr_columns, 4).'% !important;'
								. ($flickr_columns_gap > 0
									? 'padding: 0 '.trx_addons_prepare_css_value($flickr_columns_gap).' '.trx_addons_prepare_css_value($flickr_columns_gap).' 0 !important;'
									: ''
									)
								);
					printf( '<a href="%1$s_b.jpg" title="%2$s" class="'.esc_attr($class).'">'
							. '<img src="%1$s_'.($flickr_columns < 3 ? 'b' : 'q').'.jpg" alt="%2$s" width="150" height="150">'
							. '</a>',
							$url,
							$v['title']
							);
				}
			}
		}
	}
?></div><?php	

// After widget (defined by themes)
trx_addons_show_layout($after_widget);
