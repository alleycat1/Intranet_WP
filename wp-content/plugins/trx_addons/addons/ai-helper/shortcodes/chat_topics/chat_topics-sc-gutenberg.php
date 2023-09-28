<?php
/**
 * Shortcode: Chat Topics (Gutenberg support)
 *
 * @package ThemeREX Addons
 * @since v2.22.0
 */

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }

// Gutenberg Block
//------------------------------------------------------

// Add scripts and styles for the editor
if ( ! function_exists( 'trx_addons_gutenberg_sc_chat_topics_editor_assets' ) ) {
	add_action( 'enqueue_block_editor_assets', 'trx_addons_gutenberg_sc_chat_topics_editor_assets' );
	function trx_addons_gutenberg_sc_chat_topics_editor_assets() {
		if ( trx_addons_exists_gutenberg() && trx_addons_get_setting( 'allow_gutenberg_blocks' ) ) {
			wp_enqueue_script(
				'trx-addons-gutenberg-editor-block-chat-topics',
				trx_addons_get_file_url( TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/chat_topics/gutenberg/chat_topics.gutenberg-editor.js' ),
				trx_addons_block_editor_dependencis(),
				filemtime( trx_addons_get_file_dir( TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/chat_topics/gutenberg/chat_topics.gutenberg-editor.js' ) ),
				true
			);
		}
	}
}

// Block register
if ( ! function_exists( 'trx_addons_sc_chat_topics_add_in_gutenberg' ) ) {
	add_action( 'init', 'trx_addons_sc_chat_topics_add_in_gutenberg' );
	function trx_addons_sc_chat_topics_add_in_gutenberg() {
		if ( trx_addons_exists_gutenberg() && trx_addons_get_setting( 'allow_gutenberg_blocks' ) ) {
			register_block_type(
				'trx-addons/chat-topics',
				apply_filters('trx_addons_gb_map', array(
					'attributes'      => array_merge(
						array(
							'type'               => array(
								'type'    => 'string',
								'default' => 'default',
							),
							'number'             => array(
								'type'    => 'number',
								'default' => 5,
							),						
							'chat_id'           => array(
								'type'    => 'string',
								'default' => '',
							),
							'topics'            => array(
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
					'render_callback' => 'trx_addons_gutenberg_sc_chat_topics_render_block',
				), 'trx-addons/chat-topics' )
			);
		}
	}
}

// Block render
if ( ! function_exists( 'trx_addons_gutenberg_sc_chat_topics_render_block' ) ) {
	function trx_addons_gutenberg_sc_chat_topics_render_block( $attributes = array() ) {
		if ( ! empty( $attributes['topics'] ) ) {
			if ( is_string( $attributes['topics'] ) ) {
				$attributes['topics'] = json_decode( $attributes['topics'], true );
			}
		}
		return trx_addons_sc_chat_topics( $attributes );
	}
}

// Return list of allowed layouts
if ( ! function_exists( 'trx_addons_gutenberg_sc_chat_topics_get_layouts' ) ) {
	add_filter( 'trx_addons_filter_gutenberg_sc_layouts', 'trx_addons_gutenberg_sc_chat_topics_get_layouts', 10, 1 );
	function trx_addons_gutenberg_sc_chat_topics_get_layouts( $array = array() ) {
		$array['sc_chat_topics'] = apply_filters( 'trx_addons_sc_type', array( 'default' => __( 'Default', 'trx_addons' ) ), 'trx_sc_chat_topics' );
		return $array;
	}
}
