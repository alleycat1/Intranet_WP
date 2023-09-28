<?php
/**
 * Template to represent shortcode as a widget in the Elementor preview area
 *
 * Written as a Backbone JavaScript template and using to generate the live preview in the Elementor's Editor
 *
 * @package ThemeREX Addons
 * @since v1.6.41
 */

if (function_exists('trx_addons_exists_woocommerce') && trx_addons_exists_woocommerce() && class_exists('WOOCS')) {
	extract(get_query_var('trx_addons_args_sc_layouts_currency'));
	?><#
	var id = settings._element_id ? settings._element_id + '_sc' : 'sc_layouts_cart_'+(''+Math.random()).replace('.', '');

	#><div id="{{ id }}" class="sc_layouts_currency<?php $element->sc_add_common_classes('sc_layouts_currency'); ?>">
		<div class="menu_user_currency">
			<?php echo do_shortcode('[woocs show_flags="0"]'); ?>
		</div>
	</div><?php
}
