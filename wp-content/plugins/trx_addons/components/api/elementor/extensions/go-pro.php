<?php
/**
 * Elementor extension: Replace "Go Pro" links
 *
 * @package ThemeREX Addons
 * @since v2.18.4
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

define( 'ELEMENTOR_GO_PRO_METHOD', 'link' );	// ref  - add URL param ref=xxx
												// link - replace all go_pro URLs to new link
define( 'ELEMENTOR_GO_PRO_REF',  '2496' );
define( 'ELEMENTOR_GO_PRO_LINK', 'https://be.elementor.com/visit/?bta=2496&nci=5383&brand=elementor&utm_campaign=theme' );	// https://trk.elementor.com/2496


// Change "Go Pro" links
//----------------------------------------------

if ( ! function_exists( 'trx_addons_elm_change_gopro_plugins' ) && defined( 'ELEMENTOR_PLUGIN_BASE' ) ) {
	add_filter( 'plugin_action_links_' . ELEMENTOR_PLUGIN_BASE, 'trx_addons_elm_change_gopro_plugins', 11 );
	/**
	 * Add referal attribute to the "Go Pro" link in the plugins list
	 * 
	 * @hooked plugin_action_links_{ELEMENTOR_PLUGIN_BASE}
	 * 
	 * @param array $links  An array of plugin action links
	 * 
	 * @return array  	A modified array of plugin action links
	 */
	function trx_addons_elm_change_gopro_plugins( $links ) {
		if ( ! empty( $links['go_pro'] ) && preg_match( '/href="([^"]*)"/', $links['go_pro'], $matches ) && ! empty( $matches[1] ) ) {
			$links['go_pro'] = ELEMENTOR_GO_PRO_METHOD == 'link'
								? str_replace( $matches[1], ELEMENTOR_GO_PRO_LINK, $links['go_pro'] )
								: str_replace( $matches[1], trx_addons_add_to_url( $matches[1], array( 'ref' => ELEMENTOR_GO_PRO_REF ) ), $links['go_pro'] );
		}
		return $links;
	}
}

if ( ! function_exists( 'trx_addons_elm_change_gopro_dashboard' ) ) {
	add_filter( 'elementor/admin/dashboard_overview_widget/footer_actions', 'trx_addons_elm_change_gopro_dashboard', 11 );
	/**
	 * Add referal attribute to the "Go Pro" link in the dashboard widget
	 * 
	 * @hooked elementor/admin/dashboard_overview_widget/footer_actions
	 * 
	 * @param array $actions  An array of dashboard footer actions
	 * 
	 * @return array  	A modified array of dashboard footer actions
	 */
	function trx_addons_elm_change_gopro_dashboard( $actions ) {
		if ( ! empty( $actions['go-pro']['link'] ) ) {
			$actions['go-pro']['link'] = ELEMENTOR_GO_PRO_METHOD == 'link'
											? ELEMENTOR_GO_PRO_LINK
											: trx_addons_add_to_url( $actions['go-pro']['link'], array( 'ref' => ELEMENTOR_GO_PRO_REF ) );
		}
		return $actions;
	}
}

if ( ! function_exists( 'trx_addons_elm_change_gopro_menu' ) ) {
	add_filter( 'wp_redirect', 'trx_addons_elm_change_gopro_menu', 11, 2 );
	/**
	 * Add referal attribute to the "Go Pro" link in the admin menu and in the admin bar.
	 * Also replace all go_pro URLs to new link on redirect to the Elementor Pro page
	 * 
	 * @hooked wp_redirect
	 * 
	 * @param string $link  A link to redirect
	 * @param int $status  A redirect status. Not used
	 * 
	 * @return string  	A modified link to redirect
	 */
	function trx_addons_elm_change_gopro_menu( $link, $status = 0 ) {
		if ( strpos( $link, '//elementor.com/pro/' ) !== false || strpos( $link, '//go.elementor.com/' ) !== false ) {
			$link = ELEMENTOR_GO_PRO_METHOD == 'link'
								? ELEMENTOR_GO_PRO_LINK
								: trx_addons_add_to_url( $link, array( 'ref' => ELEMENTOR_GO_PRO_REF ) );
		}
		return $link;
	}
}

if ( ! function_exists( 'trx_addons_elm_change_gopro_control' ) ) {
	add_action( 'elementor/element/before_section_end', 'trx_addons_elm_change_gopro_control', 10, 3 );
	/**
	 * Replace all go_pro URLs to new link in the Elementor controls inside the Elementor editor
	 * 
	 * @hooked elementor/element/before_section_end
	 * 
	 * @param object $element  An Elementor element object
	 * @param string $section_id  A section ID
	 * @param array $args  An array of arguments
	 */
	function trx_addons_elm_change_gopro_control( $element, $section_id, $args ) {
		if ( ! is_object( $element ) ) {
			return;
		}
		$el_name = $element->get_name();
		if ( $section_id == 'section_custom_css_pro') {
			$control = $element->get_controls( 'custom_css_pro' );
			if ( ! empty( $control['raw'] )
				&& (
					strpos( $control['raw'], '//elementor.com/pro/' ) !== false
					|| strpos( $control['raw'], '//go.elementor.com/' ) !== false
				)
			) {
				$control['raw'] = preg_replace_callback(
					'~href="([^"]*)"~',
					function( $matches ) {
						return 'href="' . ( ELEMENTOR_GO_PRO_METHOD == 'link'
											? ELEMENTOR_GO_PRO_LINK
											: trx_addons_add_to_url( $matches[1], array('ref' => ELEMENTOR_GO_PRO_REF) )
											)
										. '"';
					},
					$control['raw']
				);
				$element->update_control( 'custom_css_pro', array(
									'raw' => $control['raw']
								) );
			}
		}
	}
}

if ( ! function_exists('trx_addons_elm_change_gopro_url_in_config') ) {
	add_filter( 'elementor/editor/localize_settings', 'trx_addons_elm_change_gopro_url_in_config' );
	/**
	 * Replace all go_pro URLs to new link in the Elementor config
	 * 
	 * @hooked elementor/editor/localize_settings
	 * 
	 * @param array $config  An array of Elementor config
	 * 
	 * @return array  	A modified array of Elementor config
	 */
	function trx_addons_elm_change_gopro_url_in_config( $config ) {
		if ( is_array( $config ) ) {
			foreach( $config as $k => $v ) {
				if ( is_array( $v ) ) {
					$config[ $k ] = trx_addons_elm_change_gopro_url_in_config( $v );
				} else if ( is_string( $v )
							&& strpos( $v, ' ' ) === false
							&& strpos( $v, '<' ) === false
							&& strpos( $v, '>' ) === false
							&& strpos( $v, '://' ) !== false
							&& strpos( $v, 'elementor.com/' ) !== false
				) {
					$config[ $k ] = ELEMENTOR_GO_PRO_METHOD == 'link'
										? ELEMENTOR_GO_PRO_LINK
										: trx_addons_add_to_url( $v, array( 'ref' => ELEMENTOR_GO_PRO_REF ) );
				}
			}
		}
		return $config;
	}
}

if ( ! function_exists('trx_addons_elm_change_gopro_url_in_js') ) {
	add_filter( 'trx_addons_filter_localize_script', 'trx_addons_elm_change_gopro_url_in_js' );
	add_filter( 'trx_addons_filter_localize_script_admin', 'trx_addons_elm_change_gopro_url_in_js' );
	/**
	 * Add variables to JS to replace go_pro URLs to new link via JS in frontend and backend
	 * 
	 * @hooked trx_addons_filter_localize_script
	 * @hooked trx_addons_filter_localize_script_admin
	 * 
	 * @param array $vars  An array of JS variables
	 * 
	 * @return array  	A modified array of JS variables
	 */
	function trx_addons_elm_change_gopro_url_in_js( $vars ) {
		if ( ! isset( $vars['add_to_links_url'] ) ) {
			$vars['add_to_links_url'] = array();
		}
		$args = array(
			//'page' => 'admin.php?page=elementor',
			'mask' => 'elementor.com/',
		);
		if ( ELEMENTOR_GO_PRO_METHOD == 'link' ) {
			$args['link'] = ELEMENTOR_GO_PRO_LINK;
		} else {
			$args['args'] = array( 'ref' => ELEMENTOR_GO_PRO_REF );
		}
		$vars['add_to_links_url'][] = $args;
		return $vars;
	}
}
