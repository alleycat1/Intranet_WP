<?php
/**
 * Add button (indicator) for Audio effects to start/stop playing
 *
 * @package ThemeREX Addons
 * @since v1.84.2
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}



// trx_sc_audio_effects
//-------------------------------------------------------------
/*
[trx_sc_audio_effects]
*/
if ( !function_exists( 'trx_addons_sc_audio_effects' ) ) {
	function trx_addons_sc_audio_effects($atts, $content=null){	
		$atts = trx_addons_sc_prepare_atts('trx_sc_audio_effects', $atts, trx_addons_sc_common_atts('id', array(
			// Individual params
			'type' => 'default',
			))
		);
		ob_start();
		trx_addons_get_template_part(array(
										TRX_ADDONS_PLUGIN_ADDONS . 'audio-effects/tpl.'.trx_addons_esc($atts['type']).'.php',
										TRX_ADDONS_PLUGIN_ADDONS . 'audio-effects/tpl.default.php'
										),
                                        'trx_addons_args_sc_audio_effects',
                                        $atts
                                    );
		$output = ob_get_contents();
		ob_end_clean();
		return apply_filters('trx_addons_sc_output', $output, 'trx_sc_audio_effects', $atts, $content);
	}
}


// Add shortcode [trx_sc_audio_effects]
if (!function_exists('trx_addons_sc_audio_effects_add_shortcode')) {
	function trx_addons_sc_audio_effects_add_shortcode() {
		add_shortcode("trx_sc_audio_effects", "trx_addons_sc_audio_effects");
	}
	add_action('init', 'trx_addons_sc_audio_effects_add_shortcode', 20);
}
