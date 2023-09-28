<?php
/**
 * Template to represent shortcode as a widget in the Elementor preview area
 *
 * Written as a Backbone JavaScript template and using to generate the live preview in the Elementor's Editor
 *
 * @package ThemeREX Addons
 * @addon audio-effects
 * @since v1.0
 */
?><#
var id = settings._element_id ? settings._element_id : 'sc_audio_effects_'+(''+Math.random()).replace('.', '');
#><a id="{{ id }}" href="#" title="<?php echo esc_attr_e('Enable/Disable sounds on this site', 'trx_addons'); ?>"
	class="<# print( trx_addons_apply_filters('trx_addons_filter_sc_classes', 'sc_audio_effects sc_audio_effects_'+settings.type, settings ) ); #>"
><span></span><span></span><span></span><span></span></a>
