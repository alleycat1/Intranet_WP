<?php
/**
 * Template to represent shortcode as a widget in the Elementor preview area
 *
 * Written as a Backbone JavaScript template and using to generate the live preview in the Elementor's Editor
 *
 * @package ThemeREX Addons
 * @since v1.6.41
 */

extract(get_query_var('trx_addons_args_sc_title'));
?><#
var id = settings._element_id ? settings._element_id + '_sc' : 'sc_title_'+(''+Math.random()).replace('.', '');

#><div id="{{ id }}" class="<# print( trx_addons_apply_filters('trx_addons_filter_sc_classes', 'sc_title sc_title_' + settings.title_style, settings ) ); #>"><?php

	$element->sc_show_titles('sc_title');
	$element->sc_show_links('sc_title');
	
?></div>