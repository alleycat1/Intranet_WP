<?php
/**
 * The style "default" of the Currency Switcher
 *
 * @package ThemeREX Addons
 * @since v1.6.14
 */

$args = get_query_var('trx_addons_args_sc_layouts_currency');

if (function_exists('trx_addons_exists_woocommerce') && trx_addons_exists_woocommerce() && class_exists('WOOCS')) {
	?><div<?php if (!empty($args['id'])) echo ' id="' . esc_attr($args['id']) . '"'; ?> class="sc_layouts_currency<?php
			trx_addons_cpt_layouts_sc_add_classes($args);
			?>"<?php
			if (!empty($args['css'])) echo ' style="' . esc_attr($args['css']) . '"';
			trx_addons_sc_show_attributes('sc_layouts_currency', $args, 'sc_wrapper');
	?>>
		<div class="menu_user_currency">
			<?php echo do_shortcode('[woocs show_flags="0"]'); ?>
		</div>
	</div><?php

	trx_addons_sc_layouts_showed('currency', true);
}
