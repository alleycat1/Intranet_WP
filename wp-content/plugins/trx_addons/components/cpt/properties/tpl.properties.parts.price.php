<?php
/**
 * The template's part to display the property's price
 *
 * @package ThemeREX Addons
 * @since v1.6.22
 */

$trx_addons_args = get_query_var('trx_addons_args_properties_price');
$trx_addons_meta = $trx_addons_args['meta'];

?><span class="properties_price"><?php
	if (!empty($trx_addons_meta['before_price'])) {
		?><span class="properties_price_label properties_price_before"><?php
			trx_addons_show_layout(trx_addons_prepare_macros($trx_addons_meta['before_price']));
		?></span><?php
	}
	if (!empty($trx_addons_meta['price'])) {
		?><span class="properties_price_data properties_price1"><?php
			echo esc_html(trx_addons_format_price($trx_addons_meta['price']));
		?></span><?php
	}
	if (!empty($trx_addons_meta['price']) && !empty($trx_addons_meta['price2'])) {
		?><span class="properties_price_delimiter"></span><?php
	}
	if (!empty($trx_addons_meta['price2'])) {
		?><span class="properties_price_data properties_price2"><?php
			echo esc_html(trx_addons_format_price($trx_addons_meta['price2']));
		?></span><?php
	}
	if (!empty($trx_addons_meta['after_price'])) {
		?><span class="properties_price_label properties_price_after"><?php
			trx_addons_show_layout(trx_addons_prepare_macros($trx_addons_meta['after_price']));
		?></span><?php
	}
?></span>