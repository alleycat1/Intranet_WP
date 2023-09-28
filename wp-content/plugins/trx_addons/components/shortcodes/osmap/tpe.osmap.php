<?php
/**
 * Template to represent shortcode as a widget in the Elementor preview area
 *
 * Written as a Backbone JavaScript template and using to generate the live preview in the Elementor's Editor
 *
 * @package ThemeREX Addons
 * @since v1.6.63
 */

extract(get_query_var('trx_addons_args_sc_osmap'));
?><#
var id = settings._element_id ? settings._element_id + '_sc' : 'sc_osmap_'+(''+Math.random()).replace('.', '');

#><div id="{{ id }}_wrap" class="<# print( trx_addons_apply_filters('trx_addons_filter_sc_classes', 'sc_osmap_wrap', settings ) ); #>">

	<?php $element->sc_show_titles('sc_osmap'); ?>

	<#
	if (settings.content != '') {
		#><div class="sc_osmap_content_wrap"><#
	}
	#><div id="{{ id }}_map" class="sc_item_content sc_map sc_osmap sc_osmap_{{ settings.type }}<# if (settings.prevent_scroll > 0) print(' sc_osmap_prevent_scroll'); #>"
			data-zoom="{{ settings.zoom.size }}"
			data-center="{{ settings.center }}"
			data-style="{{ settings.style }}"
			data-cluster-icon="{{ settings.cluster.url }}"
	><#
		var cnt = 0;
		_.each(settings.markers, function(marker) {
			cnt++;
			marker.title = _.escape(marker.title);
			marker.address = _.escape(marker.address);
			marker.description = _.escape(marker.description);
			marker.icon = _.escape(marker.icon.url);
			marker.icon_shadow = marker.icon && marker.icon_shadow ? _.escape(marker.icon_shadow.url) : '';
			marker.icon_width = marker.icon && marker.icon_width.size > 0 ? marker.icon_width.size+marker.icon_width.unit : '';
			marker.icon_height = marker.icon && marker.icon_height.size > 0 ? marker.icon_height.size+marker.icon_height.unit : '';
			#><div id="{{ id }}_{{ cnt }}" class="sc_osmap_marker"
					data-address="{{ marker.address }}"
					data-description="{{ marker.description }}"
					data-title="{{ marker.title }}"
					data-icon="{{ marker.icon }}"
					data-icon_shadow="{{ marker.icon_shadow }}"
					data-icon_width="{{ marker.icon_width }}"
					data-icon_height="{{ marker.icon_height }}"
					></div><#
		});
	#></div><#
	
	if (settings.content !== '') {
		#>
			<div class="sc_osmap_content sc_osmap_content_{{ settings.type }}">{{{ settings.content }}}</div>
		</div>
		<#
	}

	#><?php $element->sc_show_links('sc_osmap'); ?>
	
</div>