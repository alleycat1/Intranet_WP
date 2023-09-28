<?php
/**
 * Template to represent shortcode as a widget in the Elementor preview area
 *
 * Written as a Backbone JavaScript template and using to generate the live preview in the Elementor's Editor
 *
 * @package ThemeREX Addons
 * @since v1.6.41
 */

extract(get_query_var('trx_addons_args_sc_countdown'));
?><#
var link_color = '<?php echo apply_filters('trx_addons_filter_get_theme_accent_color', '#efa758'); ?>';
if (settings.count_restart && settings.date_time_restart != '') {
	var tmp = settings.date_time_restart.split(':').slice(-4);
	settings.date = tmp.length == 4 ? tmp[0] : '';
	settings.time = tmp.slice(-3).join(':');
} else if ( !settings.count_restart && settings.date_time != '') {
	var tmp = settings.date_time.split(' ');
	settings.date = tmp[0];
	if (tmp.length > 1) settings.time = tmp[1];
} else {
	var dt = new Date();
	settings.date = dt.getFullYear + '-' + (dt.getMonth() < 9 ? '0' : '') + (dt.getMonth()+1) + '-' + (dt.getDate() < 10 ? '0' : '') + dt.getDate();
	settings.time = '00:00:00';
}
var id = settings._element_id ? settings._element_id + '_sc' : 'sc_countdown_'+(''+Math.random()).replace('.', '');

#><div id="{{ id }}" class="<# print( trx_addons_apply_filters('trx_addons_filter_sc_classes', 'sc_countdown sc_countdown_'+settings.type+(!trx_addons_is_off(settings.align) ? ' align'+settings.align : ''), settings ) ); #>"
		data-date="{{ settings.date }}"
		data-time="{{ settings.time }}"
		data-count-to="{{ settings.count_to }}"
		data-count-restart="{{ settings.count_restart }}"
>
	<?php $element->sc_show_titles('sc_countdown'); ?>

    <div class="sc_countdown_content sc_item_content">

    	<div class="sc_countdown_inner"><?php
	
			// Days
			?><div class="sc_countdown_item sc_countdown_days"><#
                if (settings.type == 'circle') { 
					#><canvas id="{{ id }}_days"
                    			width="90" height="90"
                    			data-max-value="366"
                                data-color="{{ link_color }}"></canvas><#
				}
				#>
				<span class="sc_countdown_digits"><span></span><span></span><span></span></span>
				<span class="sc_countdown_label"><?php esc_html_e('Days', 'trx_addons'); ?></span>
			 </div><?php
			
			// Separator
			?><div class="sc_countdown_separator">:</div><?php
			 
			// Hours
			?><div class="sc_countdown_item sc_countdown_hours"><#
				if (settings.type == 'circle') { 
					#><canvas id="{{ id }}_hours"
                    			width="90" height="90"
                                data-max-value="24"
                                data-color="{{ link_color }}"></canvas><#
				}
				#>
				<span class="sc_countdown_digits"><span></span><span></span></span>
				<span class="sc_countdown_label"><?php esc_html_e('Hours', 'trx_addons'); ?></span>
			</div><?php
			
			// Separator
			?><div class="sc_countdown_separator">:</div><?php
			
			// Minutes
			?><div class="sc_countdown_item sc_countdown_minutes"><#
                if (settings.type == 'circle') {
					#><canvas id="{{ id }}_minutes"
                    			width="90" height="90"
                                data-max-value="60"
                                data-color="{{ link_color }}"></canvas><#
				}
				#>
				<span class="sc_countdown_digits"><span></span><span></span></span>
				<span class="sc_countdown_label"><?php esc_html_e('Minutes', 'trx_addons'); ?></span>
			</div><?php
			
			// Separator
			?><div class="sc_countdown_separator">:</div><?php
		
			// Seconds
			?><div class="sc_countdown_item sc_countdown_seconds"><#
                if (settings.type == 'circle') {
					#><canvas id="{{ id }}_seconds"
                    			width="90" height="90"
                                data-max-value="60"
                                data-color="{{ link_color }}"></canvas><#
				}
				#>
				<span class="sc_countdown_digits"><span></span><span></span></span>
				<span class="sc_countdown_label"><?php esc_html_e('Seconds', 'trx_addons'); ?></span>
			</div><?php
			
			// Placeholder
			?><div class="sc_countdown_placeholder hide"></div>

         </div>
		
	</div>
	
	<?php $element->sc_show_links('sc_countdown'); ?>

</div>