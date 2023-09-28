<?php
/**
 * Widget: Video player for Youtube, Vimeo, etc. embeded video (Gutenberg support)
 *
 * @package ThemeREX Addons
 * @since v1.1
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}



// Gutenberg Block
//------------------------------------------------------

// Add scripts and styles for the editor
if ( ! function_exists( 'trx_addons_gutenberg_sc_video_editor_assets' ) ) {
	add_action( 'enqueue_block_editor_assets', 'trx_addons_gutenberg_sc_video_editor_assets' );
	function trx_addons_gutenberg_sc_video_editor_assets() {
		if ( trx_addons_exists_gutenberg() && trx_addons_get_setting( 'allow_gutenberg_blocks' ) ) {
			// Scripts
			wp_enqueue_script(
				'trx-addons-gutenberg-editor-block-video',
				trx_addons_get_file_url( TRX_ADDONS_PLUGIN_WIDGETS . 'video/gutenberg/video.gutenberg-editor.js' ),
				trx_addons_block_editor_dependencis(),
				filemtime( trx_addons_get_file_dir( TRX_ADDONS_PLUGIN_WIDGETS . 'video/gutenberg/video.gutenberg-editor.js' ) ),
				true
			);
		}
	}
}

// Block register
if ( ! function_exists( 'trx_addons_sc_video_add_in_gutenberg' ) ) {
	add_action( 'init', 'trx_addons_sc_video_add_in_gutenberg' );
	function trx_addons_sc_video_add_in_gutenberg() {
		if ( trx_addons_exists_gutenberg() && trx_addons_get_setting( 'allow_gutenberg_blocks' ) ) {
			register_block_type(
				'trx-addons/video',
				apply_filters('trx_addons_gb_map', array(
					'attributes'      => array_merge(
						array(
							'title'     => array(
								'type'    => 'string',
								'default' => '',
							),
							'type'      => array(
								'type'    => 'string',
								'default' => 'default',
							),
							'ratio'     => array(
								'type'    => 'string',
								'default' => '16:9',
							),
							'subtitle'  => array(
								'type'    => 'string',
								'default' => '',
							),
							'cover'     => array(
								'type'    => 'number',
								'default' => 0,
							),
							'cover_url' => array(
								'type'    => 'string',
								'default' => '',
							),
							'media_from_post' => array(
								'type'    => 'boolean',
								'default' => false,
							),
							'link'      => array(
								'type'    => 'string',
								'default' => '',
							),
							'embed'     => array(
								'type'    => 'string',
								'default' => '',
							),
							'popup'     => array(
								'type'    => 'boolean',
								'default' => false,
							),
							'autoplay'  => array(
								'type'    => 'boolean',
								'default' => false,
							),
							'mute'      => array(
								'type'    => 'boolean',
								'default' => false,
							),
						),
						trx_addons_gutenberg_get_param_id()
					),
					'render_callback' => 'trx_addons_gutenberg_sc_video_render_block',
				), 'trx-addons/video' )
			);
		}
	}
}

// Block render
if ( ! function_exists( 'trx_addons_gutenberg_sc_video_render_block' ) ) {
	function trx_addons_gutenberg_sc_video_render_block( $attributes = array() ) {
		if ( ! empty( $attributes['link'] ) || ! empty( $attributes['embed'] ) || (int)$attributes['media_from_post'] > 0 ) {
			return trx_addons_sc_widget_video( $attributes );
		}
		return esc_html__( 'Block "Video" is empty', 'trx_addons' );
	}
}

// Return list of allowed layouts
if ( ! function_exists( 'trx_addons_gutenerg_sc_video_get_layouts' ) ) {
	add_filter( 'trx_addons_filter_gutenberg_sc_layouts', 'trx_addons_gutenberg_sc_video_get_layouts', 10, 1 );
	function trx_addons_gutenberg_sc_video_get_layouts( $array = array() ) {
		$array['sc_video'] = apply_filters( 'trx_addons_sc_type', trx_addons_get_list_widget_video_layouts(), 'trx_sc_video' );
		return $array;
	}
}

// Add shortcode-specific lists to the js vars
if ( ! function_exists( 'trx_addons_gutenberg_sc_video_params' ) ) {
	add_filter( 'trx_addons_filter_gutenberg_sc_params', 'trx_addons_gutenberg_sc_video_params', 10, 1 );
	function trx_addons_gutenberg_sc_video_params( $vars = array() ) {

		// If editor is active now
		$is_edit_mode = trx_addons_is_post_edit();

		$vars['sc_video_ratio'] = ! $is_edit_mode ? array() : trx_addons_get_list_sc_image_ratio( false, false );

		return $vars;
	}
}
