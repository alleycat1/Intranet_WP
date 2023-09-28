<?php
/**
 * Plugin support: WPBakery PageBuilder. Additional param's type 'select': dropdown, list and multiple selector
 *
 * @package ThemeREX Addons
 * @since v1.6.30
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

if ( ! function_exists( 'trx_addons_vc_params_select_init' ) ) {
	add_action( 'init', 'trx_addons_vc_params_select_init' );
	/**
	 * Add a new type 'select' to the VC shortcodes parameters
	 * 
	 * @hooked init
	 */
	function trx_addons_vc_params_select_init() {
		vc_add_shortcode_param( 'select',
								'trx_addons_vc_params_select_settings_field',
								trx_addons_get_file_url(TRX_ADDONS_PLUGIN_API . 'js_composer/params/select/select.js')
								);
	}
}

if ( ! function_exists( 'trx_addons_vc_params_select_settings_field' ) ) {
	/**
	 * Return a param's field layout for VC editor
	 * 
	 * @param array $settings  Field settings
	 * @param string $value    Field value
	 * 
	 * @return string          HTML with field's layout
	 */
	function trx_addons_vc_params_select_settings_field( $settings, $value ) {
		if ( is_array( $value ) || $value == 'Array' ) {
			$value = "";
		}
		$output = '<div class="trx_addons_vc_param_select">'
					. '<input type="hidden"'
							. ' name="' . esc_attr( $settings['param_name'] ) . '"'
							. ' class="wpb_vc_param_value wpb-textinput '
									. esc_attr( $settings['param_name'] )
									. ' '
									. esc_attr( $settings['type'] ) . '_field"'
							. ' value="' . esc_attr( $value ) . '" />'
					. '<select size="' . esc_attr( empty( $settings['size'] )
														? ( ! empty( $settings['multiple'] ) ? 5 : 1 )
														: max( 1, (int) $settings['size'] )
												) . '"'
							. ( ! empty( $settings['multiple'] ) ? ' multiple="multiple"' : '' )
							. '>';
		$value = explode( ',', $value );
		foreach ( $settings['value'] as $title => $slug ) {
			$output .= '<option value="' . esc_attr( $slug ) . '"'
								. ( in_array( $slug, $value ) ? ' selected="selected"' : '' )
								. '>'
							. esc_html( $title )
						. '</option>';
		}
		$output .= '</select></div>';
		return $output;
	}
}
