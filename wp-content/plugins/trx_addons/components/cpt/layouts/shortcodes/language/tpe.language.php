<?php
/**
 * Template to represent shortcode as a widget in the Elementor preview area
 *
 * Written as a Backbone JavaScript template and using to generate the live preview in the Elementor's Editor
 *
 * @package ThemeREX Addons
 * @since v1.6.41
 */

extract(get_query_var('trx_addons_args_sc_layouts_language'));
?><#
var id = settings._element_id ? settings._element_id + '_sc' : 'sc_layouts_language_'+(''+Math.random()).replace('.', '');
var languages = JSON.parse('<?php
								$languages = trx_addons_exists_wpml() && function_exists('icl_get_languages')
												? icl_get_languages('skip_missing=1')
												: array();
								echo addslashes(json_encode(!empty($languages) && is_array($languages) ? $languages : array()));
							?>');
var lang_list = lang_active = '', lang_count = 0;
_.each(languages, function(lang) {
	if (lang.active) lang_active = lang;
	lang_count++;
	lang_list += "\n"
				+ '<li class="menu-item' + (lang.active ? ' current-menu-item' : '') + '">'
					+ '<a rel="alternate" hreflang="' + lang.language_code + '" href="#">'
						+ (settings.flag == 'both' || settings.flag == 'menu' 
							? '<img src="' + lang.country_flag_url + '" alt="' + lang.translated_name + '" title="' + lang.translated_name + '" />'
							: '')
						+ (settings.title_menu != 'none'
							? '<span class="menu-item-title">' + (settings.title_menu=='name' ? lang.translated_name : lang.language_code.toUpperCase()) + '</span>'
							: '')
					+ '</a>'
				+ '</li>';
});
if (lang_count > 0 && lang_active !== '') {
	#><div id="{{ id }}" class="sc_layouts_language sc_layouts_menu sc_layouts_menu_default<?php $element->sc_add_common_classes('sc_layouts_language'); ?>">
		<ul class="sc_layouts_language_menu sc_layouts_dropdown sc_layouts_menu_nav">
			<li class="menu-item menu-item-has-children">
				<a href="#"><#
					if (settings.flag == 'both' || settings.flag == 'title') {
						#><img src="<# print(lang_active.country_flag_url); #>" alt="<# print(lang_active.translated_name); #>" title="<# print(lang_active.translated_name); #>" /><#
					}
					if (settings.title_link != 'none') {
						#><span class="menu-item-title"><# print(settings.title_link=='name' ? lang_active.translated_name : lang_active.language_code.toUpperCase()); #></span><#
					}
				#></a><#
				if (lang_count > 1) {
					#><ul><# print(lang_list); #></ul><#
				}
			#></li>
		</ul>
	</div><#
}
#>