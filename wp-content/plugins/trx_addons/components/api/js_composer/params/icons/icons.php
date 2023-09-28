<?php
/**
 * Plugin support: WPBakery PageBuilder. Additional param's type 'icons': dropdown or inline list with images or font icons
 *
 * @package ThemeREX Addons
 * @since v1.6.24
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

if ( ! function_exists( 'trx_addons_vc_params_icons_init' ) ) {
	add_action( 'init', 'trx_addons_vc_params_icons_init' );
	/**
	 * Add a new type 'icons' to the VC shortcodes parameters
	 * 
	 * @hooked init
	 */
	function trx_addons_vc_params_icons_init() {
		vc_add_shortcode_param( 'icons',
								'trx_addons_vc_params_icons_settings_field',
								trx_addons_get_file_url(TRX_ADDONS_PLUGIN_API . 'js_composer/params/icons/icons.js')
								);
	}
}

if ( ! function_exists( 'trx_addons_vc_params_icons_settings_field' ) ) {
	/**
	 * Return a param's field layout for VC editor.
	 * Attention! This param's type need 'value' list as normal associative array 'key' => 'value', not in VC-style 'value' => 'key'.
	 * Option 'style' => 'icons' | 'images'.
	 * Option 'mode' => 'inline' | 'dropdown'.
	 * Option 'return' => 'slug' | 'full'.
	 * 
	 * @param array $settings  Field settings
	 * @param string $value    Field value
	 * 
	 * @return string          HTML with field's layout
	 */
	function trx_addons_vc_params_icons_settings_field( $settings, $value ) {
		if ( is_array( $value ) || $value == 'Array' ) {
			$value = "";
		}
		if ( empty( $settings['mode'] ) ) {
			$settings['mode'] = 'dropdown';
		}
		if ( empty( $settings['return'] ) ) {
			$settings['return'] = 'full';
		}
		$output = '<div class="trx_addons_vc_param_icons">'
						. '<input type="hidden"'
								. ' name="'.esc_attr($settings['param_name']).'"'
								. ' class="wpb_vc_param_value wpb-textinput '
										. esc_attr($settings['param_name'])
										. ' '
										. esc_attr($settings['type']).'_field"'
								. ' value="'.esc_attr($value).'" />'
						. ( $settings['mode'] == 'dropdown'
							? '<span class="trx_addons_icon_selector'
											. ($settings['style']=='icons' && !empty($value) ? ' '.esc_attr($value) : '')
											. '"'
									. ' title="'.esc_attr__('Select icon', 'trx_addons').'"'
									. ' data-style="'.esc_attr($settings['style']).'"'
									. (in_array($settings['style'], array('images', 'svg')) && !empty($value) 
											? ' style="background-image: url('.esc_url($settings['return']=='slug' 
																						? $settings['value'][$value] 
																						: $value)
																			.');"' 
											: '')
								. '></span>'
							: ''
							)
					. '<div class="trx_addons_list_icons trx_addons_list_icons_'.esc_attr($settings['mode']).'">'
					. ( $settings['mode'] == 'dropdown'
						? '<input type="text" class="trx_addons_list_icons_search" placeholder="'.esc_attr__('Search icon ...', 'trx_addons').'">'
						: ''
						)
					. '<div class="trx_addons_list_icons_wrap">'
						. '<div class="trx_addons_list_icons_inner">';
		foreach ( $settings['value'] as $slug => $icon ) {
			$output .= '<span class="'
								. esc_attr( $settings['style'] == 'icons' ? $icon : $slug )
								. ( ( $settings['return'] == 'full' ? $icon : $slug ) == $value ? ' trx_addons_active' : '' )
								. '"'
							. ' title="' . esc_attr( $slug ) . '"'
							. ' data-icon="' . esc_attr( $settings['return'] == 'full' ? $icon : $slug ) . '"'
							. ( in_array( $settings['style'], array('images', 'svg') )
								? ' style="background-image: url(' . esc_url( $icon ) . ');"'
								: ''
								)
							. '>'
								. ( $settings['mode'] != 'dropdown' ? '<i>' . esc_html( $slug ) . '</i>'  : '' )
							. '</span>';
		}
		$output .= '</div>'
				. '</div>'
			. '</div>'
		. '</div>';
		return $output;
	}
}
