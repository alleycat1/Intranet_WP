<?php
/**
 * The style "default" of the WPML Language Selector
 *
 * @package ThemeREX Addons
 * @since v1.6.18
 */

$args = get_query_var('trx_addons_args_sc_layouts_language');

?><div<?php if (!empty($args['id'])) echo ' id="' . esc_attr($args['id']) . '"'; 
	?> class="sc_layouts_language sc_layouts_menu sc_layouts_menu_default sc_layouts_menu_no_collapse<?php
		trx_addons_cpt_layouts_sc_add_classes($args);
		?>"<?php
		if (!empty($args['css'])) echo ' style="' . esc_attr($args['css']) . '"'; 
		trx_addons_sc_show_attributes('sc_layouts_language', $args, 'sc_wrapper');
?>><?php
	$languages = trx_addons_exists_wpml() && function_exists('icl_get_languages')
					? icl_get_languages('skip_missing=1')
					: array(
							'en' => array(
								'active' => true,
								'language_code' => 'en',
								'country_flag_url' => trx_addons_get_file_url(TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'language/flags/en.png'),
								'translated_name' => __( 'English', 'trx_addons' ),
								'url' => '#'
								),
							'de' => array(
								'active' => false,
								'language_code' => 'de',
								'country_flag_url' => trx_addons_get_file_url(TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'language/flags/de.png'),
								'translated_name' => __( 'Deutsch', 'trx_addons' ),
								'url' => '#'
								),
							'es' => array(
								'active' => false,
								'language_code' => 'es',
								'country_flag_url' => trx_addons_get_file_url(TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'language/flags/es.png'),
								'translated_name' => __( 'Español', 'trx_addons' ),
								'url' => '#'
								),
							);
	if (!empty($languages) && is_array($languages)) {
		$lang_list = '';
		$lang_active = '';
		foreach ($languages as $lang) {
			$lang = apply_filters( 'trx_addons_filter_sc_language_data', $lang );
			if ($lang['active']) $lang_active = $lang;
			$lang_list .= "\n"
				.'<li class="menu-item'.($lang['active'] ? ' current-menu-item' : '').'"><a rel="alternate" hreflang="' . esc_attr($lang['language_code']) . '" href="' . esc_url(apply_filters('WPML_filter_link', $lang['url'], $lang)) . '">'
					. (in_array($args['flag'], array('both', 'menu')) 
						? '<img src="' . esc_url($lang['country_flag_url']) . '" alt="' . esc_attr($lang['translated_name']) . '" title="' . esc_attr($lang['translated_name']) . '" />'
						: '')
					. ($args['title_menu'] != 'none'
						? '<span class="menu-item-title">' . esc_html($args['title_menu']=='name' ? $lang['translated_name'] : strtoupper($lang['language_code'])) . '</span>'
						: '')
				.'</a></li>';
		}
		if ($lang_active !== '') {
			?>
			<ul class="sc_layouts_language_menu sc_layouts_dropdown sc_layouts_menu_nav sc_layouts_menu_no_collapse">
				<li class="menu-item menu-item-has-children">
					<a href="#"><?php
						if (in_array($args['flag'], array('both', 'title'))) {
							?><img src="<?php echo esc_url($lang_active['country_flag_url']); ?>" alt="<?php echo esc_attr($lang_active['translated_name']); ?>" title="<?php echo esc_attr($lang_active['translated_name']); ?>" /><?php
						}
						if ($args['title_link'] != 'none') {
							?><span class="menu-item-title"><?php echo esc_html($args['title_link']=='name' ? $lang_active['translated_name'] : strtoupper($lang_active['language_code'])); ?></span><?php
						}
					?></a><?php
					if (count($languages) > 1) {
						?><ul><?php trx_addons_show_layout($lang_list); ?></ul><?php
					}
					?>
				</li>
			</ul>
			<?php
		}
	}
?></div><?php

trx_addons_sc_layouts_showed('language', true);
