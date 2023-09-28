<?php
/**
 * Template to represent shortcode as a widget in the Elementor preview area
 *
 * Written as a Backbone JavaScript template and using to generate the live preview in the Elementor's Editor
 *
 * @package ThemeREX Addons
 * @since v1.6.41
 */

extract( get_query_var('trx_addons_args_widget_properties_compare') );

extract( trx_addons_prepare_widgets_args( trx_addons_generate_id( 'widget_properties_compare_' ), 'widget_properties_compare' ) );

// Before widget (defined by themes)
trx_addons_show_layout($before_widget);
			
// Widget title if one was input (before and after defined by themes)
?><#
if (settings.title != '') {
	#><?php trx_addons_show_layout($before_title); ?><#
	print(settings.title);
	#><?php trx_addons_show_layout($after_title); ?><#
}

// Widget body
var list = trx_addons_get_cookie('trx_addons_properties_compare_list');
list = list && ('' + list).substr(0, 1) == '{' ? JSON.parse(list) : '';
#>
<ul class="properties_compare_list<# if (list=='') print(' properties_compare_list_empty'); #>"><#
	if (list != '') {
		_.each(list, function(v, k) {
			#><li data-property-id="<# print(k.replace('id_', '')); #>" title="<?php esc_attr_e('Click to remove this property from the compare list', 'trx_addons'); ?>">{{{ v }}}</li><#
		});
	}
#></ul>

<div class="properties_compare_message"><?php esc_html_e('Select 2+ properties to compare', 'trx_addons'); ?></div>

<a class="properties_compare_button sc_button" href="<?php echo esc_url(trx_addons_add_to_url(get_post_type_archive_link(TRX_ADDONS_CPT_PROPERTIES_PT), array('compare'=>1))); ?>"><?php esc_html_e('Compare', 'trx_addons'); ?></a>

<?php	

// After widget (defined by themes)
trx_addons_show_layout($after_widget);
