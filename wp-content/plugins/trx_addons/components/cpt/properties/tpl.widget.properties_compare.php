<?php
/**
 * The style "default" of the Widget "Properties Compare"
 *
 * @package ThemeREX Addons
 * @since v1.6.24
 */

$trx_addons_args = get_query_var('trx_addons_args_widget_properties_compare');
extract($trx_addons_args);

// Before widget (defined by themes)
trx_addons_show_layout($before_widget);
			
// Widget title if one was input (before and after defined by themes)
trx_addons_show_layout($title, $before_title, $after_title);
	
// Widget body
?><ul class="properties_compare_list<?php if (!is_array($list) || count($list) < 2) echo ' properties_compare_list_empty'; ?>"><?php
	if (is_array($list)) {
		foreach ($list as $k=>$v) {
			?><li data-property-id="<?php echo esc_attr(str_replace('id_', '', $k)); ?>" title="<?php esc_attr_e('Click to remove this property from the compare list', 'trx_addons'); ?>"><?php echo esc_html($v); ?></li><?php
		}
	}
?></ul>

<div class="properties_compare_message"><?php esc_html_e('Select 2+ properties to compare', 'trx_addons'); ?></div>

<a class="properties_compare_button sc_button" href="<?php echo esc_url(trx_addons_add_to_url(get_post_type_archive_link(TRX_ADDONS_CPT_PROPERTIES_PT), array('compare'=>1))); ?>"><?php esc_html_e('Compare', 'trx_addons'); ?></a><?php

// After widget (defined by themes)
trx_addons_show_layout($after_widget);
