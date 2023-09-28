<?php
/**
 * The template to display breadcrumbs
 *
 * @package ThemeREX Addons
 * @since v1.6.08
 */

if ( ($trx_addons_breadcrumbs = trx_addons_get_breadcrumbs()) != '') {
	?><div class="breadcrumbs"><?php trx_addons_show_layout($trx_addons_breadcrumbs); ?></div><?php
}
