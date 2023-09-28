<?php
/**
 * The template to display the properties archive
 *
 * @package ThemeREX Addons
 * @since v1.6.22
 */

get_header(); 

trx_addons_get_template_part(TRX_ADDONS_PLUGIN_CPT . 'properties/tpl.properties.parts.loop.php');

get_footer();
