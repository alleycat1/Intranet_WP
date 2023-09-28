<?php
/**
 * Widget: Banner (Gutenberg support)
 *
 * @package ThemeREX Addons
 * @since v1.0
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


// Gutenberg Block
//------------------------------------------------------

// Add scripts and styles for the editor
if ( ! function_exists( 'trx_addons_gutenberg_sc_banner_editor_assets' ) ) {
	add_action( 'enqueue_block_editor_assets', 'trx_addons_gutenberg_sc_banner_editor_assets' );
	function trx_addons_gutenberg_sc_banner_editor_assets() {
		if ( trx_addons_exists_gutenberg() && trx_addons_get_setting( 'allow_gutenberg_blocks' ) ) {
			// Scripts
			wp_enqueue_script(
				'trx-addons-gutenberg-editor-block-banner',
				trx_addons_get_file_url( TRX_ADDONS_PLUGIN_WIDGETS . 'banner/gutenberg/banner.gutenberg-editor.js' ),
				trx_addons_block_editor_dependencis(),
				filemtime( trx_addons_get_file_dir( TRX_ADDONS_PLUGIN_WIDGETS . 'banner/gutenberg/banner.gutenberg-editor.js' ) ),
				true
			);
		}
	}
}

// Block register
if ( ! function_exists( 'trx_addons_sc_banner_add_in_gutenberg' ) ) {
	add_action( 'init', 'trx_addons_sc_banner_add_in_gutenberg' );
	function trx_addons_sc_banner_add_in_gutenberg() {
		if ( trx_addons_exists_gutenberg() && trx_addons_get_setting( 'allow_gutenberg_blocks' ) ) {
			register_block_type(
				'trx-addons/banner',
				apply_filters('trx_addons_gb_map', array(
					'attributes'      => array_merge(
						array(
							'title'     => array(
								'type'    => 'string',
								'default' => '',
							),
							'image'     => array(
								'type'    => 'number',
								'default' => 0,
							),
							'image_url' => array(
								'type'    => 'string',
								'default' => '',
							),
							'link'      => array(
								'type'    => 'string',
								'default' => '',
							),
							'code'      => array(
								'type'    => 'string',
								'default' => '',
							),
							'fullwidth' => array(
								'type'    => 'boolean',
								'default' => false,
							),
							'show'      => array(
								'type'    => 'string',
								'default' => 'permanent',
							),
						),
						trx_addons_gutenberg_get_param_id()
					),
					'render_callback' => 'trx_addons_gutenberg_sc_banner_render_block',
				), 'trx-addons/banner' )
			);
		}
	}
}

// Block render
if ( ! function_exists( 'trx_addons_gutenberg_sc_banner_render_block' ) ) {
	function trx_addons_gutenberg_sc_banner_render_block( $attributes = array() ) {
		$attributes['fullwidth'] = $attributes['fullwidth'] ? 'on' : 'off';
		return trx_addons_sc_widget_banner( $attributes );
	}
}

// Add shortcode-specific lists to the js vars
if ( ! function_exists( 'trx_addons_gutenberg_sc_banner_params' ) ) {
	add_filter( 'trx_addons_filter_gutenberg_sc_params', 'trx_addons_gutenberg_sc_banner_params', 10, 1 );
	function trx_addons_gutenberg_sc_banner_params( $vars = array() ) {
		// If editor is active now
		$is_edit_mode = trx_addons_is_post_edit();

		$vars['sc_banner_show_on'] = !$is_edit_mode ? array() : trx_addons_get_list_sc_show_on();
		return $vars;
	}
}
