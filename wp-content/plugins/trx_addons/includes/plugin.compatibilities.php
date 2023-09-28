<?php
/**
 * Compatibility fixes for WordPress updates, 3rd-party plugins, etc.
 *
 * @package ThemeREX Addons
 * @since v2.1.0
 */

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }



/* WordPress 5.8+: Widgets block editor in Customize don't allow moving sections with widgets
 *                 from the panel 'Widgets' to another panel
--------------------------------------------------------------------------------------------------- */
if ( ! function_exists( 'trx_addons_disable_moving_widgets_sections_in_customizer' ) ) {
	add_filter( 'after_setup_theme', 'trx_addons_disable_moving_widgets_sections_in_customizer', 1 );
	/**
	 * Disable moving widgets sections in Сustomizer (WordPress 5.8+) to prevent the bug with the widgets block editor in the panel 'Widgets'
	 * 
	 * @hooked 'after_setup_theme'
	 */
	function trx_addons_disable_moving_widgets_sections_in_customizer() {
		if ( version_compare( get_bloginfo( 'version' ), '5.8', '>=' ) ) {
			$slug = str_replace( '-', '_', get_template() );
			add_filter( "{$slug}_filter_front_page_options", 'trx_addons_disable_moving_widgets_sections_in_customizer_callback', 10000, 1 );
		}
	}
}

if ( ! function_exists( 'trx_addons_disable_moving_widgets_sections_in_customizer_callback' ) ) {
	/**
	 * Rename sections with widgets to prevent its moving
	 * 
	 * @param array $options  Theme options
	 * 
	 * @return array 		Modified theme options
	 */
	function trx_addons_disable_moving_widgets_sections_in_customizer_callback( $options ) {
		if ( isset( $options['front_page_sections']['options'] ) && is_array( $options['front_page_sections']['options'] ) ) {
			foreach ( $options['front_page_sections']['options'] as $k => $v ) {
				if ( isset( $options["sidebar-widgets-front_page_{$k}_widgets"] ) && ! isset( $options["front_page_{$k}_widgets"] ) ) {
					trx_addons_array_insert_after( $options, "sidebar-widgets-front_page_{$k}_widgets", array(
						"front_page_{$k}_widgets" => $options["sidebar-widgets-front_page_{$k}_widgets"]
					) );
					unset( $options["sidebar-widgets-front_page_{$k}_widgets"] );
				}
				if ( ! empty( $options["front_page_{$k}_widgets_info"]['desc'] ) && is_string( $options["front_page_{$k}_widgets_info"]['desc'] ) ) {
					$options["front_page_{$k}_widgets_info"]['desc'] .= '<br>&nbsp;<br><i>' . wp_kses_data( sprintf( __( 'Attention! Since WordPress 5.8+ you are not able to select widgets for this section here, in order to do that please go to Customize - Widgets - Front page section "%s"', 'trx_addons' ), $v ) . '</i>' );
				}
			}
		}
		return $options;
	}
}


/* WordPress 6.1+: If a parameter 'depth' greater then 0 - a class 'menu-item-has-children'
*                  is not added to the submenu items
--------------------------------------------------------------------------------------------------- */
if ( ! function_exists( 'trx_addons_clear_depth_in_menu_args' ) ) {
	add_filter( str_replace( '-', '_', get_template() ) . '_filter_get_nav_menu_args', 'trx_addons_clear_depth_in_menu_args' );
	add_filter( 'trx_addons_filter_get_nav_menu_args', 'trx_addons_clear_depth_in_menu_args' );
	/**
	 * Clear 'depth' parameter in the menu args to prevent the bug with the submenu items - WordPress 6.1+
	 * If a parameter 'depth' greater then 0 - a class 'menu-item-has-children' is not added to the submenu items
	 * 
	 * @hooked 'trx_addons_filter_get_nav_menu_args'
	 * @hooked '{theme-slug}_filter_get_nav_menu_args'
	 * 
	 * @param array $args  Menu args
	 * 
	 * @return array 		Modified menu args
	 */
	function trx_addons_clear_depth_in_menu_args( $args ) {
		if ( version_compare( get_bloginfo( 'version' ), '6.1', '>=' ) ) {
			if ( ! empty( $args['depth'] ) ) {
				$args['depth'] = 0;
			}
		}
		return $args;
	}
}


/* Theme-specific fixes
--------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'trx_addons_theme_specific_post_meta_args' ) ) {
	add_filter( str_replace( '-', '_', get_template() ) . '_filter_post_meta_args', 'trx_addons_theme_specific_post_meta_args', 10, 3 );
	/**
	 * Hide a date and a comments count from the product meta in the search results streampage.
	 * 
	 * @hooked '{theme-slug}_filter_post_meta_args'
	 * 
	 * @param array  $args       Meta args
	 * @param string $blog_style Blog style. Not used
	 * @param int    $columns    Number of columns. Not used
	 */
	function trx_addons_theme_specific_post_meta_args( $args, $blog_style = '', $columns = 1 ) {
		$hide_meta_components = apply_filters( 'trx_addons_filter_post_meta_args_hide_components', array(
			'product' => array( 'date', 'date_modified', 'author', 'comments' )
		) );
		$post_type = get_post_type();
		if ( ! empty( $args['components'] ) && ! empty( $hide_meta_components[ $post_type ] ) ) {
			$args['components'] = join( ',', trx_addons_array_delete_by_value(
												array_map( 'trim', explode( ',', $args['components'] ) ),
												$hide_meta_components[ $post_type ]
											) );
		}
		return $args;
	}
}

if ( ! function_exists( 'trx_addons_theme_specific_replace_url_for_theme_rate' ) ) {
	add_filter( 'after_setup_theme', 'trx_addons_theme_specific_replace_url_for_theme_rate', 2 );
	/**
	 * Replace the URL for the theme rate
	 * 
	 * @hooked 'after_setup_theme', 2
	 */
	function trx_addons_theme_specific_replace_url_for_theme_rate() {
		$slug = strtoupper( str_replace( '-', '_', get_template() ) );
		if ( ! empty( $GLOBALS[ "{$slug}_STORAGE" ]['theme_rate_url'] ) ) {
			$GLOBALS[ "{$slug}_STORAGE" ]['theme_rate_url'] = '//themeforest.net/downloads';
		}
	}
}
