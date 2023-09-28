<?php
/**
 * Template to represent shortcode as a widget in the Elementor preview area
 *
 * Written as a Backbone JavaScript template and using to generate the live preview in the Elementor's Editor
 *
 * @package ThemeREX Addons
 * @since v1.6.41
 */
?><#
// Default values
settings = trx_addons_array_merge({
									'type':	'default',
									'icon':	'',
									'url':	{'url': ''},
									'anchor_id': '',
									'title':''
									}, settings);
// Check values
if (settings.anchor_id == '') settings.anchor_id = (''+Math.random()).replace('.', '');

// Anchor's tag attributes
var atts = {
	'class': "sc_anchor sc_anchor_" + settings.type,
	'data-vc-icon': trx_addons_get_settings_icon( settings.icon ),
	'data-url': settings.url.url
};

#><a id="sc_anchor_{{ settings.anchor_id }}" title="{{ settings.title }}"<#
	for (var k in atts) {
		print(' '+k+'="' + atts[k] + '"');
	}
#>></a>