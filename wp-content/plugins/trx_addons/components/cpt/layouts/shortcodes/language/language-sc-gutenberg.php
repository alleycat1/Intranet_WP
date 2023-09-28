<?php
/**
 * Shortcode: Display WPML Language Selector (Gutenberg support)
 *
 * @package ThemeREX Addons
 * @since v1.6.18
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


// Gutenberg Block
//------------------------------------------------------

// Add scripts and styles for the editor
if ( ! function_exists( 'trx_addons_gutenberg_sc_language_editor_assets' ) ) {
	add_action( 'enqueue_block_editor_assets', 'trx_addons_gutenberg_sc_language_editor_assets' );
	function trx_addons_gutenberg_sc_language_editor_assets() {
		if ( trx_addons_exists_gutenberg() && trx_addons_get_setting( 'allow_gutenberg_blocks' ) ) {
			if ( trx_addons_exists_wpml() && function_exists( 'icl_get_languages' ) ) {
				wp_enqueue_script(
					'trx-addons-gutenberg-editor-block-language',
					trx_addons_get_file_url( TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'language/gutenberg/language.gutenberg-editor.js' ),
					trx_addons_block_editor_dependencis(),
					filemtime( trx_addons_get_file_dir( TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'language/gutenberg/language.gutenberg-editor.js' ) ),
					true
				);
			}
		}
	}
}

// Block register
if ( ! function_exists( 'trx_addons_sc_language_add_in_gutenberg' ) ) {
	add_action( 'init', 'trx_addons_sc_language_add_in_gutenberg' );
	function trx_addons_sc_language_add_in_gutenberg() {
		if ( trx_addons_exists_gutenberg() && trx_addons_get_setting( 'allow_gutenberg_blocks' ) ) {
			if ( trx_addons_exists_wpml() && function_exists( 'icl_get_languages' ) ) {
				register_block_type(
					'trx-addons/layouts-language',
					apply_filters('trx_addons_gb_map', array(
						'attributes'      => array_merge(
							array(
								'type'             => array(
									'type'    => 'string',
									'default' => 'default',
								),
								'flag'             => array(
									'type'    => 'string',
									'default' => '',
								),
								'title_link'       => array(
									'type'    => 'string',
									'default' => 'name',
								),
								'title_menu'       => array(
									'type'    => 'string',
									'default' => 'name',
								),
							),
							trx_addons_gutenberg_get_param_hide(),
							trx_addons_gutenberg_get_param_id()
						),
						'render_callback' => 'trx_addons_gutenberg_sc_language_render_block',
					), 'trx-addons/layouts-language' )
				);
			}
		}
	}
}

// Block render
if ( ! function_exists( 'trx_addons_gutenberg_sc_language_render_block' ) ) {
	function trx_addons_gutenberg_sc_language_render_block( $attributes = array() ) {
		$output = trx_addons_sc_layouts_language( $attributes );
		if ( empty( $output ) && trx_addons_is_preview( 'gutenberg' ) ) {
			return TRX_ADDONS_GUTENBERG_EDITOR_MSG_BLOCK_IS_EMPTY;
		}
		return $output;
	}
}

// Return list of allowed layouts
if ( ! function_exists( 'trx_addons_gutenberg_sc_language_get_layouts' ) ) {
	add_filter( 'trx_addons_filter_gutenberg_sc_layouts', 'trx_addons_gutenberg_sc_language_get_layouts', 10, 1 );
	function trx_addons_gutenberg_sc_language_get_layouts( $array = array() ) {
		$array['sc_language'] = apply_filters(
			'trx_addons_sc_type', array(
				'default' => esc_html__( 'Default', 'trx_addons' ),
			), 'trx_sc_layouts_language'
		);
		return $array;
	}
}

// Add shortcode's specific vars to the JS storage
if ( ! function_exists( 'trx_addons_gutenberg_sc_language_params' ) ) {
	add_filter( 'trx_addons_filter_gutenberg_sc_params', 'trx_addons_gutenberg_sc_language_params' );
	function trx_addons_gutenberg_sc_language_params( $vars = array() ) {
		if ( trx_addons_exists_gutenberg() && trx_addons_get_setting( 'allow_gutenberg_blocks' ) ) {

			// If editor is active now
			$is_edit_mode = trx_addons_is_post_edit();

			// Language positions
			$vars['sc_layouts_language_positions'] = !$is_edit_mode ? array() : trx_addons_get_list_sc_layouts_language_positions();

			// Language parts
			$vars['sc_layouts_language_parts'] = !$is_edit_mode ? array() : trx_addons_get_list_sc_layouts_language_parts();

			return $vars;
		}
	}
}