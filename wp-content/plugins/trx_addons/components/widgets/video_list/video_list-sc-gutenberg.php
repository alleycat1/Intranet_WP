<?php
/**
 * Widget: Video list for Youtube, Vimeo, etc. embeded video (Gutenberg support)
 *
 * @package ThemeREX Addons
 * @since v1.78.0
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}



// Gutenberg Block
//------------------------------------------------------

// Add scripts and styles for the editor
if ( ! function_exists( 'trx_addons_gutenberg_sc_video_list_editor_assets' ) ) {
	add_action( 'enqueue_block_editor_assets', 'trx_addons_gutenberg_sc_video_list_editor_assets' );
	function trx_addons_gutenberg_sc_video_list_editor_assets() {
		if ( trx_addons_exists_gutenberg() && trx_addons_get_setting( 'allow_gutenberg_blocks' ) ) {
			// Scripts
			wp_enqueue_script(
				'trx-addons-gutenberg-editor-block-video-player',
				trx_addons_get_file_url( TRX_ADDONS_PLUGIN_WIDGETS . 'video_list/gutenberg/video_list.gutenberg-editor.js' ),
				trx_addons_block_editor_dependencis(),
				filemtime( trx_addons_get_file_dir( TRX_ADDONS_PLUGIN_WIDGETS . 'video_list/gutenberg/video_list.gutenberg-editor.js' ) ),
				true
			);
		}
	}
}

// Block register
if ( ! function_exists( 'trx_addons_sc_video_list_add_in_gutenberg' ) ) {
	add_action( 'init', 'trx_addons_sc_video_list_add_in_gutenberg' );
	function trx_addons_sc_video_list_add_in_gutenberg() {
		if ( trx_addons_exists_gutenberg() && trx_addons_get_setting( 'allow_gutenberg_blocks' ) ) {
			register_block_type(
				'trx-addons/video-player',
				apply_filters('trx_addons_gb_map', array(
					'attributes' => array_merge(
						array(
							'title'     		=> array(
								'type'    => 'string',
								'default' => '',
							),
							'autoplay'	        => array(
								'type'    => 'boolean',
								'default' => false,
							),
							'post_type'			=> array(
								'type'    => 'string',
								'default' => 'post',
							),
							'taxonomy'			=> array(
								'type'    => 'string',
								'default' => 'category',
							),
							'category'			=> array(
								'type'    => 'string',
								'default' => '',
							),
							'controller_style'	=> array(
								'type'    => 'string',
								'default' => 'default',
							),
							'controller_pos'	=> array(
								'type'    => 'string',
								'default' => 'right',
							),
							'controller_height'	=> array(
								'type'    => 'string',
								'default' => '',
							),
							'controller_autoplay'	=> array(
								'type'    => 'boolean',
								'default' => true,
							),
							'controller_link'	=> array(
								'type'    => 'boolean',
								'default' => true,
							),
						),
						trx_addons_gutenberg_get_param_query( array( 'columns' => false ) ),
						trx_addons_gutenberg_get_param_id()
					),
					'render_callback' => 'trx_addons_gutenberg_sc_video_list_render_block',
				), 'trx-addons/video-player' )
			);
		}
	}
}

// Block render
if ( ! function_exists( 'trx_addons_gutenberg_sc_video_list_render_block' ) ) {
	function trx_addons_gutenberg_sc_video_list_render_block( $attributes = array() ) {
		return trx_addons_sc_widget_video_list( $attributes );
	}
}

// Add shortcode-specific lists to the js vars
if ( ! function_exists( 'trx_addons_gutenberg_sc_video_list_params' ) ) {
	add_filter( 'trx_addons_filter_gutenberg_sc_params', 'trx_addons_gutenberg_sc_video_list_params', 10, 1 );
	function trx_addons_gutenberg_sc_video_list_params( $vars = array() ) {

		// If editor is active now
		$is_edit_mode = trx_addons_is_post_edit();

		$vars['sc_video_list_controller_styles']  = !$is_edit_mode ? array() : trx_addons_get_list_sc_video_list_controller_styles();
		$vars['sc_video_list_controller_positions'] = !$is_edit_mode ? array() : trx_addons_get_list_sc_video_list_controller_positions();
		return $vars;
	}
}
