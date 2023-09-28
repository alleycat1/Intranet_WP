<?php
/**
 * The template to display the cars archive
 *
 * @package ThemeREX Addons
 * @since v1.6.25
 */

get_header(); 

trx_addons_get_template_part(TRX_ADDONS_PLUGIN_CPT . 'cars/tpl.cars.parts.loop.php');

get_footer();
