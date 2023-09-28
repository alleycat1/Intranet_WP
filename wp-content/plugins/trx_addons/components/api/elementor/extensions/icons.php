<?php
/**
 * Elementor extension: 'Icon' parameter
 *
 * @package ThemeREX Addons
 * @since v2.18.4
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

if ( ! function_exists( 'trx_addons_get_icon_param' ) ) {
	/**
	 * Return array with parameters for 'Icon' parameter.
	 * The internal icons selector is used if the plugin's setting 'icons_selector' is 'internal'.
	 * Otherwise - the Elementor's icons selector is used.
	 * 
	 * @trigger trx_addons_filter_elementor_add_icon_param
	 *
	 * @param string $name           Name of the parameter
	 * @param boolean $only_socials  If true, return only socials icons
	 * @param string $style          Style of the icons: images | icons | svg
	 * 
	 * @return array                 Array with parameters for 'Icon' parameter
	 */
	function trx_addons_get_icon_param( $name = 'icon', $only_socials = false, $style = '' ) {
		$idx = $name != 'icon' ? $name : 0;
		if ( trx_addons_get_setting( 'icons_selector' ) == 'internal' ) {
			if ( empty( $style ) ) {
				$style = $only_socials ? trx_addons_get_setting( 'socials_type' ) : trx_addons_get_setting( 'icons_type' );
			}

			$is_edit_mode = trx_addons_elm_is_edit_mode();

			$params = array(
							$idx => array(
								'name' => $name,
								'type' => 'trx_icons',
								'label' => __( 'Icon', 'trx_addons' ),
								'label_block' => false,
								'default' => '',
								'options' => ! $is_edit_mode ? array() : trx_addons_get_list_icons( $style ),
								'style' => $style
							)
						);
		} else {
			$params = array(
							$idx => array(
								'name' => $name,
								'type' => \Elementor\Controls_Manager::ICON,
								'label' => __( 'Icon', 'trx_addons' ),
								'label_block' => false,
								'default' => '',
							)
						);
		}
		return apply_filters( 'trx_addons_filter_elementor_add_icon_param', $params, $only_socials, $style );
	}
}

if ( ! function_exists( 'trx_addons_is_elementor_icon' ) ) {
	/**
	 * Check if the icon is Elementor's icon
	 *
	 * @param string $icon  Icon name
	 * 
	 * @return boolean      True if the icon is Elementor's icon
	 */
	function trx_addons_is_elementor_icon( $icon ) {
		$icon = trx_addons_elementor_get_settings_icon( $icon );
		return ! empty( $icon ) && strpos( $icon, 'fa ' ) !== false;
	}
}

if ( ! function_exists( 'trx_addons_elementor_get_settings_icon' ) ) {
	/**
	 * Get icon from Elementor's settings. After Elementor v.2.6.0 the icon is stored in array with 'icon' key
	 *
	 * @param string|array $icon  Icon name
	 * 
	 * @return string             Icon name
	 */
	function trx_addons_elementor_get_settings_icon( $icon ) {
		return is_array( $icon )
						? ( ! empty( $icon['icon'])
							? $icon['icon']
							: ''
							)
						: $icon;
	}
}

if ( ! function_exists( 'trx_addons_elementor_set_settings_icon' ) ) {
	/**
	 * Set icon for Elementor's settings. After Elementor v.2.6.0 the icon is stored in array with 'icon' key
	 *
	 * @param string|array $icon  Icon name
	 * 
	 * @return string|array       Icon name
	 */
	function trx_addons_elementor_set_settings_icon( $icon ) {
		return defined( 'ELEMENTOR_VERSION' ) && version_compare( ELEMENTOR_VERSION, '2.6.0', '>=' )
					? array( 'icon' => $icon )
					: $icon;
	}
}
