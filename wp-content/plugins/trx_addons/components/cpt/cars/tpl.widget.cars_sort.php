<?php
/**
 * The style "default" of the Widget "Cars Sort"
 *
 * @package ThemeREX Addons
 * @since v1.6.25
 */

$trx_addons_args = get_query_var('trx_addons_args_widget_cars_sort');
extract($trx_addons_args);

$cars_order = strtolower(trx_addons_get_value_gp('cars_order'));
if (empty($cars_order))
	$cars_order = 'date_desc';	//sprintf("%s_%s", $orderby, $order);


// Before widget (defined by themes)
trx_addons_show_layout($before_widget);
			
// Widget title if one was input (before and after defined by themes)
trx_addons_show_layout($title, $before_title, $after_title);
	
// Widget body
?><form action="<?php echo esc_url(get_post_type_archive_link( TRX_ADDONS_CPT_CARS_PT )); ?>" method="get"><?php
	$search_params = trx_addons_cpt_cars_query_params();
	foreach ($search_params as $k=>$v) {
		if ($k == 'cars_order') continue;
		if (is_array($v)) {
			foreach($v as $v1) {
				?><input type="hidden" name="<?php echo esc_attr($k.'_'.$v1); ?>" value="<?php echo esc_attr($v1); ?>"><?php
			}
		} else {
			?><input type="hidden" name="<?php echo esc_attr($k); ?>" value="<?php echo esc_attr($v); ?>"><?php
		}
	}
	?><select name="cars_order">
		<option value="date_asc"<?php if ($cars_order=='date_asc') echo ' selected="selected"'; ?>><?php
			esc_html_e('Date Ascending', 'trx_addons'); ?></option>
		<option value="date_desc"<?php if ($cars_order=='date_desc') echo ' selected="selected"'; ?>><?php
			esc_html_e('Date Descending', 'trx_addons'); ?></option>
		<option value="price_asc"<?php if ($cars_order == 'price_asc') echo ' selected="selected"'; ?>><?php
			esc_html_e('Price Ascending', 'trx_addons'); ?></option>
		<option value="price_desc"<?php if ($cars_order == 'price_desc') echo ' selected="selected"'; ?>><?php
			esc_html_e('Price Descending', 'trx_addons'); ?></option>
		<option value="title_asc"<?php if ($cars_order == 'title_asc') echo ' selected="selected"'; ?>><?php
			esc_html_e('Title Ascending', 'trx_addons'); ?></option>
		<option value="title_desc"<?php if ($cars_order == 'title_desc') echo ' selected="selected"'; ?>><?php
			esc_html_e('Title Descending', 'trx_addons'); ?></option>
	</select>
</form><?php

// After widget (defined by themes)
trx_addons_show_layout($after_widget);
