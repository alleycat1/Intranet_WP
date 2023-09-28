<?php
/**
 * Widget: Audio player for Local hosted audio and Soundcloud and other embeded audio (Gutenberg support)
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
if ( ! function_exists( 'trx_addons_gutenberg_sc_audio_editor_assets' ) ) {
	add_action( 'enqueue_block_editor_assets', 'trx_addons_gutenberg_sc_audio_editor_assets' );
	function trx_addons_gutenberg_sc_audio_editor_assets() {
		if ( trx_addons_exists_gutenberg() && trx_addons_get_setting( 'allow_gutenberg_blocks' ) ) {
			// Scripts
			wp_enqueue_script(
				'trx-addons-gutenberg-editor-block-audio',
				trx_addons_get_file_url( TRX_ADDONS_PLUGIN_WIDGETS . 'audio/gutenberg/audio.gutenberg-editor.js' ),
				trx_addons_block_editor_dependencis(),
				filemtime( trx_addons_get_file_dir( TRX_ADDONS_PLUGIN_WIDGETS . 'audio/gutenberg/audio.gutenberg-editor.js' ) ),
				true
			);
		}
	}
}

// Block register
if ( ! function_exists( 'trx_addons_sc_audio_add_in_gutenberg' ) ) {
	add_action( 'init', 'trx_addons_sc_audio_add_in_gutenberg' );
	function trx_addons_sc_audio_add_in_gutenberg() {
		if ( trx_addons_exists_gutenberg() && trx_addons_get_setting( 'allow_gutenberg_blocks' ) ) {
			register_block_type(
				'trx-addons/audio',
				apply_filters('trx_addons_gb_map', array(
					'attributes'      => array_merge(
						array(
							'title'        => array(
								'type'    => 'string',
								'default' => '',
							),
							'subtitle'     => array(
								'type'    => 'string',
								'default' => '',
							),
							'next_btn'     => array(
								'type'    => 'boolean',
								'default' => true,
							),
							'prev_btn'     => array(
								'type'    => 'boolean',
								'default' => true,
							),
							'next_text'    => array(
								'type'    => 'string',
								'default' => '',
							),
							'prev_text'    => array(
								'type'    => 'string',
								'default' => '',
							),
							'now_text'     => array(
								'type'    => 'string',
								'default' => '',
							),
							'track_time'   => array(
								'type'    => 'boolean',
								'default' => true,
							),
							'track_scroll' => array(
								'type'    => 'boolean',
								'default' => true,
							),
							'track_volume' => array(
								'type'    => 'boolean',
								'default' => true,
							),
							'media'        => array(
								'type'    => 'string',
								'default' => '',
							),
							'media_from_post' => array(
								'type'    => 'boolean',
								'default' => false,
							),
							// Rerender
							'reload'             => array(
								'type'    => 'string',
								'default' => '',
							),
						),
						trx_addons_gutenberg_get_param_id()
					),
					'render_callback' => 'trx_addons_gutenberg_sc_audio_render_block',
				), 'trx-addons/audio' )
			);
		}
	}
}

// Block render
if ( ! function_exists( 'trx_addons_gutenberg_sc_audio_render_block' ) ) {
	function trx_addons_gutenberg_sc_audio_render_block( $attributes = array() ) {
		$attributes['next_btn'] = $attributes['next_btn'] ? '1' : '0';
		$attributes['prev_btn'] = $attributes['prev_btn'] ? '1' : '0';
		$attributes['track_time'] = $attributes['track_time'] ? '1' : '0';
		$attributes['track_scroll'] = $attributes['track_scroll'] ? '1' : '0';
		$attributes['track_volume'] = $attributes['track_volume'] ? '1' : '0';
		$attributes['media_from_post'] = $attributes['media_from_post'] ? '1' : '0';
		if ( ! empty( $attributes['media'] ) || (int)$attributes['media_from_post'] > 0 ) {
			if ( is_string( $attributes['media'] ) ) {
				$attributes['media'] = json_decode( $attributes['media'], true );
			}
			return trx_addons_sc_widget_audio( $attributes );
		}
		return esc_html__( 'Add at least one item', 'trx_addons' );
	}
}
