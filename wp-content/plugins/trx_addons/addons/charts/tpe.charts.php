<?php
/**
 * Template to represent shortcode as a widget in the Elementor preview area
 *
 * Written as a Backbone JavaScript template and using to generate the live preview in the Elementor's Editor
 *
 * @package ThemeREX Addons
 * @addon Charts
 * @since v2.8.0
 */

extract( get_query_var( 'trx_addons_args_sc_charts' ) );
?><#
settings = trx_addons_elm_prepare_global_params( settings );

var id = settings._element_id ? settings._element_id + '_sc' : 'sc_charts_' + ( '' + Math.random() ).replace( '.', '' );

var total = TRX_ADDONS_STORAGE['charts_datasets_total'] || <?php echo apply_filters( 'trx_addons_filter_charts_datasets_total', TRX_ADDONS_CHARTS_DATASETS_TOTAL ); ?>;

var data = {
	type:         settings.type,
	legend:       settings.legend,
	from_zero:    settings.from_zero,
	cutout:       Math.min( 100, Math.max( 0, settings.cutout.size ) ),
	hover_offset: Math.min( 100, Math.max( 0, settings.hover_offset.size ) )
};

for (var i = 1; i <= total; i++ ) {
	if ( i == 1 || settings['dataset' + i + '_enable'] > 0 ) {
		data['dataset' + i]                   = settings['dataset' + i];
		data['dataset' + i + '_enable']       = i == 1 || settings['dataset' + i + '_enable'] > 0;
		data['dataset' + i + '_title']        = settings['dataset' + i + '_title'];
		data['dataset' + i + '_fill']         = settings['dataset' + i + '_fill'];
		data['dataset' + i + '_tension']      = settings['dataset' + i + '_tension'].size
												? Math.min( 1, Math.max( 0, settings['dataset' + i + '_tension'].size ) )
												: 0;
		data['dataset' + i + '_point_size']   = Math.min( 20, Math.max( 0, settings['dataset' + i + '_point_size'].size ) );
		data['dataset' + i + '_point_style']  = settings['dataset' + i + '_point_style'];
		data['dataset' + i + '_bg_color']     = settings['dataset' + i + '_bg_color']
												? settings['dataset' + i + '_bg_color']
												: '<?php echo apply_filters('trx_addons_filter_get_theme_accent_color', '#efa758'); ?>';
		data['dataset' + i + '_border_color'] = settings['dataset' + i + '_border_color']
												? settings['dataset' + i + '_border_color']
												: '<?php echo apply_filters('trx_addons_filter_get_theme_accent_color', '#efa758'); ?>';
		data['dataset' + i + '_border_width'] = Math.min( 10, Math.max( 0, settings['dataset' + i + '_border_width'].size ) );
		data['dataset' + i + '_border_join']  = settings['dataset' + i + '_border_join'];
	}
}

data = JSON.stringify( data );

#><div id="{{ id }}"
		class="<# print( trx_addons_apply_filters( 'trx_addons_filter_sc_classes', 'sc_charts sc_charts_type_' + settings.type, settings ) ); #>"
><#

	#><?php $element->sc_show_titles( 'sc_charts' ); ?><#

	#><div class="sc_item_content sc_charts_content">
		<canvas id="{{ id }}_canvas" class="sc_charts_canvas" data-chart-data="{{ data }}"></canvas>
	</div><#

	#><?php $element->sc_show_links('sc_charts'); ?>

</div><#

settings = trx_addons_elm_restore_global_params( settings );
#>