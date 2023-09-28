<?php
/**
 * Plugin support: Elegro Crypto Payment (Add Crypto payments to WooCommerce)
 *
 * @package ThemeREX Addons
 * @since v1.70.3
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

if ( ! function_exists( 'trx_addons_exists_elegro_payment' ) ) {
	/**
	 * Check if Elegro Crypto Payment is installed and activated
	 *
	 * @return bool  True if plugin is installed and activated
	 */
	function trx_addons_exists_elegro_payment() {
		return class_exists( 'WC_Elegro_Payment' );
	}
}

if ( ! function_exists( 'trx_addons_elegro_payment_add_ref' ) ) {
	add_filter( 'woocommerce_settings_api_form_fields_elegro', 'trx_addons_elegro_payment_add_ref' );
	/**
	 * Add ref to the link to the Elegro Crypto Payment plugin
	 * 
	 * @hooked woocommerce_settings_api_form_fields_elegro
	 *
	 * @param array $fields  Array of fields
	 * 
	 * @return array  Modified array of fields
	 */
	function trx_addons_elegro_payment_add_ref( $fields ) {
		if ( ! empty( $fields['listen_url']['description'] ) ) {
			$fields['listen_url']['description'] = preg_replace(
													'/href="([^"]+)"/',
													//'href="$1/auth/sign-up?ref=246218d7-a23d-444d-83c5-a884ecfa4ebd"',
													'href="$1?ref=246218d7-a23d-444d-83c5-a884ecfa4ebd"',
													$fields['listen_url']['description']
													);
		}
		return $fields;
	}
}

if ( ! function_exists( 'trx_addons_elegro_payment_filter_export_options' ) ) {
	add_filter( 'trx_addons_filter_export_options', 'trx_addons_elegro_payment_filter_export_options' );
	/**
	 * Filter export options: remove API keys from dummy data
	 * 
	 * @hooked trx_addons_filter_export_options
	 *
	 * @param array $options  Array of options
	 * 
	 * @return array  Modified array of options
	 */
	function trx_addons_elegro_payment_filter_export_options( $options ) {
		if ( isset( $options['woocommerce_elegro_settings'] ) ) {
			unset( $options['woocommerce_elegro_settings'] );
		}
		return $options;
	}
}

if ( ! function_exists( 'trx_addons_elegro_payment_filter_disable_footer_and_async' ) ) {
	add_filter( 'trx_addons_filter_skip_move_scripts_down', 'trx_addons_elegro_payment_filter_disable_footer_and_async' );
	add_filter( 'trx_addons_filter_skip_async_scripts_load', 'trx_addons_elegro_payment_filter_disable_footer_and_async' );
	/**
	 * Filter to disable move to the footer and async loading for scripts of the Elegro Crypto Payment plugin
	 * 
	 * @hooked trx_addons_filter_skip_move_scripts_down
	 * @hooked trx_addons_filter_skip_async_scripts_load
	 *
	 * @param array $list  Array of scripts
	 * 
	 * @return array  Modified array of scripts
	 */
	function trx_addons_elegro_payment_filter_disable_footer_and_async( $list ) {
		$list[] = 'widget.acceptance.elegro';
		$list[] = 'elegro-script';
		return $list;
	}
}

if ( ! function_exists( 'trx_addons_elegro_payment_load_scripts_front' ) ) {
	add_action( "wp_enqueue_scripts", 'trx_addons_elegro_payment_load_scripts_front', TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY );
	add_action( 'trx_addons_action_pagebuilder_preview_scripts', 'trx_addons_elegro_payment_load_scripts_front', 10, 1 );
	/**
	 * Enqueue custom styles and scripts
	 * 
	 * @hooked wp_enqueue_scripts
	 * @hooked trx_addons_action_pagebuilder_preview_scripts
	 * 
	 * @trigger trx_addons_action_load_scripts_front
	 * 
	 * @param bool $force  Force enqueue scripts
	 */
	function trx_addons_elegro_payment_load_scripts_front( $force = false ) {
		if ( ! trx_addons_exists_elegro_payment() ) {
			return;
		}
		trx_addons_enqueue_optimized( 'elegro_payment', $force, array(
			'need' => function_exists( 'trx_addons_exists_woocommerce' ) && trx_addons_exists_woocommerce() && ( is_cart() || is_checkout() ),
			'check' => array(
				array( 'type' => 'text', 'sc' => 'payment_method_elegro' ),
			)
		) );
	}
}

if ( ! function_exists( 'trx_addons_elegro_payment_check_in_html_output' ) ) {
//	add_filter( 'trx_addons_filter_get_menu_cache_html', 'trx_addons_elegro_payment_check_in_html_output', 10, 1 );
//	add_action( 'trx_addons_action_show_layout_from_cache', 'trx_addons_elegro_payment_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_check_page_content', 'trx_addons_elegro_payment_check_in_html_output', 10, 1 );
	/**
	 * Check if the plugin's output is present in the page content and force load scripts and styles
	 * 
	 * @hooked trx_addons_action_check_page_content
	 * 
	 * @trigger trx_addons_filter_check_in_html
	 * 
	 * @param string $content  HTML to check
	 * 
	 * @return string  Checked HTML
	 */
	function trx_addons_elegro_payment_check_in_html_output( $content = '' ) {
		if ( ! trx_addons_exists_elegro_payment() ) {
			return $content;
		}
		$args = array(
			'need'  => function_exists( 'trx_addons_exists_woocommerce' ) && trx_addons_exists_woocommerce(),
			'check' => array(
				'class=[\'"][^\'"]*payment_method_elegro'
			)
		);
		if ( trx_addons_check_in_html_output( 'elegro_payment', $content, $args ) ) {
			trx_addons_elegro_payment_load_scripts_front( true );
		}
		return $content;
	}
}
