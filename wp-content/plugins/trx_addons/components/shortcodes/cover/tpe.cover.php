<#
/**
 * Template to represent shortcode as Widget in the Elementor preview area
 *
 * Written as a Backbone JavaScript template and used to generate the live preview.
 *
 * @package ThemeREX Addons
 * @since v1.86.0
 */
#><a href="{{ settings.url.url }}"
	class="sc_cover sc_cover_{{ settings.type }}"
	data-place="{{ settings.place }}"<#
	if (settings.url.is_external == 'on') print(' target="_blank"');
	if (settings.url.nofollow == 'on') print(' rel="nofollow"');
	if (settings.css) print(' style="' + settings.css + '"');
#>></a>
