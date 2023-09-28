<?php
/**
 * Shortcode: Super title (Gutenberg support)
 *
 * @package ThemeREX Addons
 * @since v1.6.49
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}



// Gutenberg Block
//------------------------------------------------------

// Add scripts and styles for the editor
if ( ! function_exists( 'trx_addons_gutenberg_sc_supertitle_editor_assets' ) ) {
	add_action( 'enqueue_block_editor_assets', 'trx_addons_gutenberg_sc_supertitle_editor_assets' );
	function trx_addons_gutenberg_sc_supertitle_editor_assets() {
		if ( trx_addons_exists_gutenberg() && trx_addons_get_setting( 'allow_gutenberg_blocks' ) ) {
			// Scripts
			wp_enqueue_script(
				'trx-addons-gutenberg-editor-block-supertitle',
				trx_addons_get_file_url( TRX_ADDONS_PLUGIN_SHORTCODES . 'supertitle/gutenberg/supertitle.gutenberg-editor.js' ),
				trx_addons_block_editor_dependencis(),
				filemtime( trx_addons_get_file_dir( TRX_ADDONS_PLUGIN_SHORTCODES . 'supertitle/gutenberg/supertitle.gutenberg-editor.js' ) ),
				true
			);
		}
	}
}

// Block register
if ( ! function_exists( 'trx_addons_sc_supertitle_add_in_gutenberg' ) ) {
	add_action( 'init', 'trx_addons_sc_supertitle_add_in_gutenberg' );
	function trx_addons_sc_supertitle_add_in_gutenberg() {
		if ( trx_addons_exists_gutenberg() && trx_addons_get_setting( 'allow_gutenberg_blocks' ) ) {
			register_block_type(
				'trx-addons/supertitle',
				apply_filters('trx_addons_gb_map', array(
					'attributes'      => array_merge(
						array(
							'type'          => array(
								'type'    => 'string',
								'default' => 'default',
							),
							'icon_column'   => array(
								'type'    => 'number',
								'default' => 1,
							),
							'header_column' => array(
								'type'    => 'number',
								'default' => 8,
							),
							'image'         => array(
								'type'    => 'number',
								'default' => 0,
							),
							'image_url'     => array(
								'type'    => 'string',
								'default' => '',
							),
							'icon'          => array(
								'type'    => 'string',
								'default' => '',
							),
							'icon_color'    => array(
								'type'    => 'string',
								'default' => '',
							),
							'icon_bg_color' => array(
								'type'    => 'string',
								'default' => '',
							),
							'icon_size'     => array(
								'type'    => 'string',
								'default' => '',
							),
							'items'         => array(
								'type'    => 'string',
								'default' => '',
							),
							// Rerender
							'reload'        => array(
								'type'    => 'string',
								'default' => '',
							),
						),
						trx_addons_gutenberg_get_param_id()
					),
					'render_callback' => 'trx_addons_gutenberg_sc_supertitle_render_block',
				), 'trx-addons/supertitle' )
			);
		}
	}
}

// Block render
if ( ! function_exists( 'trx_addons_gutenberg_sc_supertitle_render_block' ) ) {
	function trx_addons_gutenberg_sc_supertitle_render_block( $attributes = array() ) {
		if ( ! empty( $attributes['items'] ) ) {
			if ( is_string( $attributes['items'] ) ) {
				$attributes['items'] = json_decode( $attributes['items'], true );
			}
			return trx_addons_sc_supertitle( $attributes );
		} else {
			return esc_html__( 'Add at least one item', 'trx_addons' );
		}
	}
}

// Return list of allowed layouts
if ( ! function_exists( 'trx_addons_gutenberg_sc_supertitle_get_layouts' ) ) {
	add_filter( 'trx_addons_filter_gutenberg_sc_layouts', 'trx_addons_gutenberg_sc_supertitle_get_layouts', 10, 1 );
	function trx_addons_gutenberg_sc_supertitle_get_layouts( $array = array() ) {
		$array['sc_supertitle'] = apply_filters( 'trx_addons_sc_type', trx_addons_components_get_allowed_layouts( 'sc', 'supertitle' ), 'trx_sc_supertitle' );
		return $array;
	}
}


// Add shortcode's specific lists to the JS storage
if ( ! function_exists( 'trx_addons_sc_supertitle_gutenberg_sc_params' ) ) {
	add_filter( 'trx_addons_filter_gutenberg_sc_params', 'trx_addons_sc_supertitle_gutenberg_sc_params' );
	function trx_addons_sc_supertitle_gutenberg_sc_params( $vars = array() ) {

		// If editor is active now
		$is_edit_mode = trx_addons_is_post_edit();

		// Return list of the title types
		$vars['sc_supertitle_item_types'] = !$is_edit_mode ? array() : trx_addons_get_list_sc_supertitle_item_types();

		return $vars;
	}
}
