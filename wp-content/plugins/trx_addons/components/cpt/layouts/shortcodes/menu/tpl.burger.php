<?php
/**
 * The style "burger" of the Menu
 *
 * @package ThemeREX Addons
 * @since v1.6.08
 */

$args = get_query_var('trx_addons_args_sc_layouts_menu');

// Store menu layout for the mobile menu
$trx_addons_menu = !empty($args['location']) || !empty($args['menu']) ? trx_addons_get_nav_menu($args['location'], $args['menu']) : '';
if (!empty($trx_addons_menu) && trx_addons_is_on($args['mobile_menu'])) trx_addons_sc_layouts_menu_add_to_mobile_menu($trx_addons_menu);

// Add button to open mobile menu
if (!empty($trx_addons_menu) || trx_addons_is_on($args['mobile_button'])) {
	?><div<?php if (!empty($args['id'])) echo ' id="'.esc_attr($args['id']).'"';
		?> class="sc_layouts_iconed_text sc_layouts_menu_mobile_button_burger<?php
			if (trx_addons_is_on($args['mobile_button'])) echo ' sc_layouts_menu_mobile_button';
			if (empty($trx_addons_menu)) echo ' without_menu';
			if (!empty($args['class'])) echo ' '.esc_attr($args['class']);
		?>"<?php
			if (!empty($args['css'])) echo ' style="'.esc_attr($args['css']).'"';
		?>>
		<a class="sc_layouts_item_link sc_layouts_iconed_text_link" href="#">
			<span class="sc_layouts_item_icon sc_layouts_iconed_text_icon trx_addons_icon-menu"></span>
		</a>
		<?php
		if (!empty($trx_addons_menu) && trx_addons_is_off($args['mobile_menu'])) {
			?><div class="sc_layouts_menu_popup"><?php trx_addons_show_layout($trx_addons_menu); ?></div><?php
		}
	?></div><?php
	trx_addons_sc_layouts_showed('menu_button', true);
}
