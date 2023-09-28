<?php
/**
 * Shortcode: Google Map (Gutenberg support)
 *
 * @package ThemeREX Addons
 * @since v1.2
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}



// Gutenberg Block
//------------------------------------------------------

// Add scripts and styles for the editor
if ( ! function_exists( 'trx_addons_gutenberg_sc_googlemap_editor_assets' ) ) {
	add_action( 'enqueue_block_editor_assets', 'trx_addons_gutenberg_sc_googlemap_editor_assets' );
	function trx_addons_gutenberg_sc_googlemap_editor_assets() {
		if ( trx_addons_exists_gutenberg() && trx_addons_get_setting( 'allow_gutenberg_blocks' ) ) {
			// Scripts
			wp_enqueue_script(
				'trx-addons-gutenberg-editor-block-googlemap',
				trx_addons_get_file_url( TRX_ADDONS_PLUGIN_SHORTCODES . 'googlemap/gutenberg/googlemap.gutenberg-editor.js' ),
				trx_addons_block_editor_dependencis(),
				filemtime( trx_addons_get_file_dir( TRX_ADDONS_PLUGIN_SHORTCODES . 'googlemap/gutenberg/googlemap.gutenberg-editor.js' ) ),
				true
			);
		}
	}
}

// Block register
if ( ! function_exists( 'trx_addons_sc_googlemap_add_in_gutenberg' ) ) {
	add_action( 'init', 'trx_addons_sc_googlemap_add_in_gutenberg' );
	function trx_addons_sc_googlemap_add_in_gutenberg() {
		if ( trx_addons_exists_gutenberg() && trx_addons_get_setting( 'allow_gutenberg_blocks' ) ) {
			register_block_type(
				'trx-addons/googlemap',
				apply_filters('trx_addons_gb_map', array(
					'attributes'      => array_merge(
						array(
							'type'               => array(
								'type'    => 'string',
								'default' => 'default',
							),
							'style'              => array(
								'type'    => 'string',
								'default' => 'default',
							),
							'zoom'               => array(
								'type'    => 'string',
								'default' => '16',
							),
							'center'             => array(
								'type'    => 'string',
								'default' => '',
							),
							'width'              => array(
								'type'    => 'string',
								'default' => '100%',
							),
							'height'             => array(
								'type'    => 'string',
								'default' => '350',
							),
							'cluster'            => array(
								'type'    => 'string',
								'default' => '',
							),
							'cluster_url'     	=> array(
								'type'    => 'string',
								'default' => '',
							),
							'prevent_scroll'     => array(
								'type'    => 'boolean',
								'default' => false,
							),
							'address'            => array(
								'type'    => 'string',
								'default' => '',
							),
							'markers'            => array(
								'type'    => 'string',
								'default' => '',
							),
							// Rerender
							'reload'             => array(
								'type'    => 'string',
								'default' => '',
							),
						),
						trx_addons_gutenberg_get_param_title(),
						trx_addons_gutenberg_get_param_button(),
						trx_addons_gutenberg_get_param_id()
					),
					'render_callback' => 'trx_addons_gutenberg_sc_googlemap_render_block',
				), 'trx-addons/googlemap' )
			);
		}
	}
}

// Block render
if ( ! function_exists( 'trx_addons_gutenberg_sc_googlemap_render_block' ) ) {
	function trx_addons_gutenberg_sc_googlemap_render_block( $attributes = array() ) {
		if ( ! empty( $attributes['markers'] ) ) {
			if ( is_string( $attributes['markers'] ) ) {
				$attributes['markers'] = json_decode( $attributes['markers'], true );
			}
		}
		if ( ! empty( $attributes['markers'] ) || ! empty( $attributes['address'] )  ) {
			return trx_addons_sc_googlemap( $attributes );
		} else {
			return esc_html__( 'Add at least one marker or address', 'trx_addons' );
		}
	}
}

// Return list of allowed layouts
if ( ! function_exists( 'trx_addons_gutenberg_sc_googlemap_get_layouts' ) ) {
	add_filter( 'trx_addons_filter_gutenberg_sc_layouts', 'trx_addons_gutenberg_sc_googlemap_get_layouts', 10, 1 );
	function trx_addons_gutenberg_sc_googlemap_get_layouts( $array = array() ) {
		$array['sc_googlemap'] = apply_filters( 'trx_addons_sc_type', trx_addons_components_get_allowed_layouts( 'sc', 'googlemap' ), 'trx_sc_googlemap' );
		return $array;
	}
}


// Add shortcode's specific lists to the JS storage
if ( ! function_exists( 'trx_addons_sc_googlemap_gutenberg_sc_params' ) ) {
	add_filter( 'trx_addons_filter_gutenberg_sc_params', 'trx_addons_sc_googlemap_gutenberg_sc_params' );
	function trx_addons_sc_googlemap_gutenberg_sc_params( $vars = array() ) {

		// If editor is active now
		$is_edit_mode = trx_addons_is_post_edit();
		
		// Return list of googlemap styles
		$vars['sc_googlemap_styles'] = !$is_edit_mode ? array() : trx_addons_get_list_sc_googlemap_styles();
		
		// Return list of the googlemap animations
		$vars['sc_googlemap_animations'] = !$is_edit_mode ? array() : trx_addons_get_list_sc_googlemap_animations();

		return $vars;
	}
}
