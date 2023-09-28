<?php
/**
 * Template to represent shortcode as a widget in the Elementor preview area
 *
 * Written as a Backbone JavaScript template and using to generate the live preview in the Elementor's Editor
 *
 * @package ThemeREX Addons
 * @since v1.6.41
 */

extract(get_query_var('trx_addons_args_sc_layouts_logo'));
?><#
var site_name = site_descr = site_logo = site_logo_retina = '';
<?php
// Get site name and description
if (apply_filters('trx_addons_filter_show_site_name_as_logo', true)) {
	?>
	site_name = '<?php echo addslashes( get_bloginfo( 'name' ) ); ?>';
	site_descr = '<?php echo addslashes( str_replace( array( "\r", "\n" ), array( '', '<br>'), get_bloginfo( 'description', 'display' ) ) ); ?>';
	<?php
}
// Get logo from current theme (if empty)
$logo = apply_filters('trx_addons_filter_theme_logo', '');
if (is_array($logo)) {
	?>
	site_logo = '<?php echo addslashes(!empty($logo['logo']) ? $logo['logo'] : ''); ?>';
	site_logo_retina = '<?php echo addslashes(!empty($logo['logo_retina']) ? $logo['logo_retina'] : ''); ?>';
	<?php
} else {
	?>
	site_logo = '<?php echo addslashes($logo); ?>';
	<?php
}
?>
if (settings.logo_text != '')		site_name = settings.logo_text;
if (settings.logo_slogan != '')		site_descr = settings.logo_slogan;
if (settings.logo.url != '')		site_logo = settings.logo.url;
if (settings.logo_retina.url != '')	site_logo_retina = settings.logo_retina.url;
if (site_logo == '' && site_logo_retina != '') site_logo = site_logo_retina;
#><a href="<?php echo is_front_page() ? '#' : esc_url(home_url('/')); ?>" class="sc_layouts_logo sc_layouts_logo_{{ settings.type }}<?php
		$element->sc_add_common_classes('sc_layouts_featured');
		?>"><#
	if (site_logo != '') {
		#><img class="logo_image" src="{{ site_logo }}" alt="<# print(trx_addons_prepare_macros(site_name)); #>"><#
	} else if (site_name != '' || site_descr != '') {
		if (site_name != '#')
			print('<span class="logo_text">' + trx_addons_prepare_macros(site_name) + '</span>');
		if (site_descr != '#')
			print('<span class="logo_slogan">' + trx_addons_prepare_macros(site_descr) + '</span>');
	}
#></a>