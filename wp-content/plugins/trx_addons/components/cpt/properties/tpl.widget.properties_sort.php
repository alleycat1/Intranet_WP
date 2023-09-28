<?php
/**
 * The style "default" of the Widget "Properties Sort"
 *
 * @package ThemeREX Addons
 * @since v1.6.22
 */

$trx_addons_args = get_query_var('trx_addons_args_widget_properties_sort');
extract($trx_addons_args);

$properties_order = strtolower(trx_addons_get_value_gp('properties_order'));
if (empty($properties_order))
	$properties_order = 'date_desc';	//sprintf("%s_%s", $orderby, $order);


// Before widget (defined by themes)
trx_addons_show_layout($before_widget);
			
// Widget title if one was input (before and after defined by themes)
trx_addons_show_layout($title, $before_title, $after_title);
	
// Widget body
?><form action="<?php echo esc_url(get_post_type_archive_link( TRX_ADDONS_CPT_PROPERTIES_PT )); ?>" method="get"><?php
	$search_params = trx_addons_cpt_properties_query_params();
	foreach ($search_params as $k=>$v) {
		if ($k == 'properties_order') continue;
		if (is_array($v)) {
			foreach($v as $v1) {
				?><input type="hidden" name="<?php echo esc_attr($k.'_'.$v1); ?>" value="<?php echo esc_attr($v1); ?>"><?php
			}
		} else {
			?><input type="hidden" name="<?php echo esc_attr($k); ?>" value="<?php echo esc_attr($v); ?>"><?php
		}
	}
	?><select name="properties_order">
		<option value="date_asc"<?php if ($properties_order=='date_asc') echo ' selected="selected"'; ?>><?php
			esc_html_e('Date Ascending', 'trx_addons'); ?></option>
		<option value="date_desc"<?php if ($properties_order=='date_desc') echo ' selected="selected"'; ?>><?php
			esc_html_e('Date Descending', 'trx_addons'); ?></option>
		<option value="price_asc"<?php if ($properties_order == 'price_asc') echo ' selected="selected"'; ?>><?php
			esc_html_e('Price Ascending', 'trx_addons'); ?></option>
		<option value="price_desc"<?php if ($properties_order == 'price_desc') echo ' selected="selected"'; ?>><?php
			esc_html_e('Price Descending', 'trx_addons'); ?></option>
		<option value="title_asc"<?php if ($properties_order == 'title_asc') echo ' selected="selected"'; ?>><?php
			esc_html_e('Title Ascending', 'trx_addons'); ?></option>
		<option value="title_desc"<?php if ($properties_order == 'title_desc') echo ' selected="selected"'; ?>><?php
			esc_html_e('Title Descending', 'trx_addons'); ?></option>
	</select>
</form><?php

// After widget (defined by themes)
trx_addons_show_layout($after_widget);
