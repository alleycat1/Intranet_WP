<?php
/**
 * Plugin support: Backstage
 *
 * @package ThemeREX Addons
 * @since v1.88.0
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

if ( ! function_exists( 'trx_addons_exists_backstage' ) ) {
	/**
	 * Check if Backstage exists and activated
	 *
	 * @return bool  True if plugin exists and activated
	 */
	function trx_addons_exists_backstage() {
		return function_exists( 'Backstage_Plugin' );
	}
}


if ( ! function_exists( 'trx_addons_backstage_is_demo' ) ) {
	/**
	 * Check if Backstage demo mode is active
	 *
	 * @return bool  True if demo mode is active
	 */
	function trx_addons_backstage_is_demo() {
		$is_demo = false;
		if ( trx_addons_exists_backstage() && ( is_customize_preview() || is_admin() ) ) {
			$user = wp_get_current_user();
			$is_demo = is_object( $user ) && ! empty( $user->data->user_login ) && 'backstage_customizer_user' == $user->data->user_login;
		}
		return $is_demo;
	}
}

if ( ! function_exists( 'trx_addons_backstage_options' ) ) {
	add_filter( 'trx_addons_filter_options', 'trx_addons_backstage_options' );
	/**
	 * Add Backstage demo settings to the ThemeREX Addons Options
	 * 
	 * @hooked trx_addons_filter_options
	 *
	 * @param array $options ThemeREX Addons Options
	 * 
	 * @return array  	  Modified options
	 */
	function trx_addons_backstage_options($options) {
		if ( trx_addons_exists_backstage() ) {
			trx_addons_array_insert_before( $options, 'theme_specific_section', array(
					'backstage_section' => array(
						"title" => esc_html__('Backstage demo', 'trx_addons'),
						"desc" => wp_kses_data( __("Backstage demo settings", 'trx_addons') ),
						'icon' => 'trx_addons_icon-customizer',
						"type" => "section"
					),
					'backstage_additional_info' => array(
						"title" => esc_html__('Backstage demo parameters', 'trx_addons'),
						"desc" => wp_kses_data( __("Settings for the Backstage plugin", 'trx_addons') ),
						"type" => "info"
					),
					'backstage_return_url' => array(
						"title" => esc_html__('URL to backstage demo',  'trx_addons'),
						"desc" => wp_kses_data( __('URL of the page for customizer demo. If empty - current page url is used',  'trx_addons') ),
						"std" => "",
						"type" => "text"
					),
				)
			);
		}
		return $options;
	}
}

if ( ! function_exists( 'trx_addons_backstage_change_return_url' ) ) {
	add_filter( 'backstage_get_customizer_link', 'trx_addons_backstage_change_return_url' );
	/**
	 * Change return URL for the Backstage demo to enable auto-login
	 *
	 * @param string $url  URL to the customizer
	 * 
	 * @return string Modified URL
	 */
	function trx_addons_backstage_change_return_url($url) {
		$return_url = trx_addons_get_option( 'backstage_return_url' );
		if ( ! empty( $return_url ) && function_exists( 'backstage_get_setting' ) ) {
			$auto_login_key = backstage_get_setting( 'auto_login_key' );
			if ( empty( $auto_login_key ) && class_exists( 'Backstage' ) ) {
				$auto_login_key = Backstage::$default_auto_login_key;
			}
			if ( ! empty( $auto_login_key ) ) {
				$auto_login_hash = wp_hash( $return_url );
				$url = add_query_arg( $auto_login_key, rawurlencode( $auto_login_hash ), remove_query_arg( $auto_login_key, $url ) );
				$url = add_query_arg( 'return_url', rawurlencode( $return_url ), remove_query_arg( 'return_url', $url ) );
			}
		}
		return $url;
	}
}

if ( ! function_exists( 'trx_addons_backstage_disallow_widgets_and_menus_in_demo' ) ) {
	add_filter( 'customize_loaded_components', 'trx_addons_backstage_disallow_widgets_and_menus_in_demo', 1000, 2 );
	/**
	 * Disallow some components (menus, widgets) for the customizer in the demo mode
	 * 
	 * @hooked customize_loaded_components
	 *
	 * @param array $components     List of components
	 * @param object $wp_customize  WP_Customize_Manager instance. Not used.
	 * 
	 * @return array                Modified list of components
	 */
	function trx_addons_backstage_disallow_widgets_and_menus_in_demo( $components, $wp_customize ) {
		if ( trx_addons_backstage_is_demo() ) {
			$components = array();
		}
		return $components;
	}
}

if ( ! function_exists( 'trx_addons_backstage_disallow_core_components_in_demo' ) ) {
	add_action( 'customize_register', 'trx_addons_backstage_disallow_core_components_in_demo', 1 );
	/**
	 * Disallow register WordPress core controls for the customizer in the demo mode.
	 * Not used now, because after remove action backstage user is unlogged after each page reloading
	 * 
	 * @hooked customize_register
	 *
	 * @param object $wp_customize  WP_Customize_Manager instance. Not used.
	 * 
	 * @return array                Modified list of components
	 */
	function trx_addons_backstage_disallow_core_components_in_demo( $wp_customize ) {
		if ( false && trx_addons_backstage_is_demo() ) {
			remove_action( 'customize_register', array( $wp_customize, 'register_controls' ) );
		}
	}
}

if ( ! function_exists( 'trx_addons_backstage_add_front_body_class_in_demo' ) ) {
	add_filter( 'body_class', 'trx_addons_backstage_add_front_body_class_in_demo', 1 );
	/**
	 * Add class 'trx_addons_customizer_demo' to the body in the demo mode
	 * 
	 * @hooked body_class
	 *
	 * @param array $classes List of classes
	 * 
	 * @return array         Modified list of classes
	 */
	function trx_addons_backstage_add_front_body_class_in_demo( $classes ) {
		if ( trx_addons_backstage_is_demo() ) {
			$classes[] = 'trx_addons_customizer_demo';
		}
		return $classes;
	}
}

if ( ! function_exists( 'trx_addons_backstage_customizer_control_js' ) ) {
	add_action( 'customize_controls_enqueue_scripts', 'trx_addons_backstage_customizer_control_js' );
	/**
	 * Enqueue customizer control scripts and styles in the demo mode. And localize some variables with messages
	 * 
	 * @hooked customize_controls_enqueue_scripts
	 */
	function trx_addons_backstage_customizer_control_js() {
		if ( trx_addons_backstage_is_demo() ) {
			wp_enqueue_style(
				'trx_addons-backstage-customizer',
				trx_addons_get_file_url( TRX_ADDONS_PLUGIN_API . 'backstage/backstage.css' ),
				array(), null
			);
			wp_enqueue_script(
				'trx_addons-backstage-customizer',
				trx_addons_get_file_url( TRX_ADDONS_PLUGIN_API . 'backstage/backstage.js' ),
				array( 'customize-controls', 'iris', 'underscore', 'wp-util' ), null, true
			);
			wp_localize_script(
				'trx_addons-backstage-customizer', 'trx_addons_customizer_vars', apply_filters(
					'trx_addons_filter_customizer_vars', array(
						'msg_refresh_preview_area' => esc_html__( "Reload preview area", 'trx_addons' ),
						'msg_welcome_hi'           => esc_html__( "Hello", 'trx_addons' ),
						'msg_welcome_text'         => esc_html__( "Here you can customize the look and feel of your website. More options become available after purchasing the theme!", 'trx_addons' ),
						'msg_welcome_button'       => esc_html__( "Get Started", 'trx_addons' ),
					)
				)
			);
		}
	}
}
