<?php
/**
 * Template to represent shortcode as a widget in the Elementor preview area
 *
 * Written as a Backbone JavaScript template and using to generate the live preview in the Elementor's Editor
 *
 * @package ThemeREX Addons
 * @since v1.6.41
 */

extract( get_query_var('trx_addons_args_widget_cars_sort') );

extract( trx_addons_prepare_widgets_args( trx_addons_generate_id( 'widget_cars_sort_' ), 'widget_cars_sort' ) );

// Before widget (defined by themes)
trx_addons_show_layout($before_widget);
			
// Widget title if one was input (before and after defined by themes)
?><#
if (settings.title != '') {
	#><?php trx_addons_show_layout($before_title); ?><#
	print(settings.title);
	#><?php trx_addons_show_layout($after_title); ?><#
}

// Widget body
#><select name="cars_order">
	<option value="date_asc"><?php esc_html_e('Date Ascending', 'trx_addons'); ?></option>
	<option value="date_desc" selected="selected"><?php esc_html_e('Date Descending', 'trx_addons'); ?></option>
	<option value="price_asc"><?php esc_html_e('Price Ascending', 'trx_addons'); ?></option>
	<option value="price_desc"><?php esc_html_e('Price Descending', 'trx_addons'); ?></option>
	<option value="title_asc"><?php esc_html_e('Title Ascending', 'trx_addons'); ?></option>
	<option value="title_desc"><?php esc_html_e('Title Descending', 'trx_addons'); ?></option>
</select><?php

// After widget (defined by themes)
trx_addons_show_layout($after_widget);
