<?php
/**
 * The style "default" of the Widget "Cars Compare"
 *
 * @package ThemeREX Addons
 * @since v1.6.25
 */

$trx_addons_args = get_query_var('trx_addons_args_widget_cars_compare');
extract($trx_addons_args);

// Before widget (defined by themes)
trx_addons_show_layout($before_widget);
			
// Widget title if one was input (before and after defined by themes)
trx_addons_show_layout($title, $before_title, $after_title);
	
// Widget body
?><ul class="cars_compare_list<?php if (!is_array($list) || count($list) < 2) echo ' cars_compare_list_empty'; ?>"><?php
	if (is_array($list)) {
		foreach ($list as $k=>$v) {
			?><li data-car-id="<?php echo esc_attr(str_replace('id_', '', $k)); ?>" title="<?php esc_attr_e('Click to remove this car from the compare list', 'trx_addons'); ?>"><?php echo esc_html($v); ?></li><?php
		}
	}
?></ul>

<div class="cars_compare_message"><?php esc_html_e('Select 2+ cars to compare', 'trx_addons'); ?></div>

<a class="cars_compare_button sc_button" href="<?php echo esc_url(trx_addons_add_to_url(get_post_type_archive_link(TRX_ADDONS_CPT_CARS_PT), array('compare'=>1))); ?>"><?php esc_html_e('Compare', 'trx_addons'); ?></a><?php

// After widget (defined by themes)
trx_addons_show_layout($after_widget);
