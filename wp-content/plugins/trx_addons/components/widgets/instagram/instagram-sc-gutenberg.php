<?php
/**
 * Widget: Instagram (Gutenberg support)
 *
 * @package ThemeREX Addons
 * @since v1.6.47
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


// Gutenberg Block
//------------------------------------------------------

// Add scripts and styles for the editor
if ( ! function_exists( 'trx_addons_gutenberg_sc_instagram_editor_assets' ) ) {
	add_action( 'enqueue_block_editor_assets', 'trx_addons_gutenberg_sc_instagram_editor_assets' );
	function trx_addons_gutenberg_sc_instagram_editor_assets() {
		if ( trx_addons_exists_gutenberg() && trx_addons_get_setting( 'allow_gutenberg_blocks' ) ) {
			// Scripts
			wp_enqueue_script(
				'trx-addons-gutenberg-editor-block-instagram',
				trx_addons_get_file_url( TRX_ADDONS_PLUGIN_WIDGETS . 'instagram/gutenberg/instagram.gutenberg-editor.js' ),
				trx_addons_block_editor_dependencis(),
				filemtime( trx_addons_get_file_dir( TRX_ADDONS_PLUGIN_WIDGETS . 'instagram/gutenberg/instagram.gutenberg-editor.js' ) ),
				true
			);
		}
	}
}

// Block register
if ( ! function_exists( 'trx_addons_sc_instagram_add_in_gutenberg' ) ) {
	add_action( 'init', 'trx_addons_sc_instagram_add_in_gutenberg' );
	function trx_addons_sc_instagram_add_in_gutenberg() {
		if ( trx_addons_exists_gutenberg() && trx_addons_get_setting( 'allow_gutenberg_blocks' ) ) {
			register_block_type(
				'trx-addons/instagram',
				apply_filters('trx_addons_gb_map', array(
					'attributes'      => array_merge(
						array(
							'title'       => array(
								'type'    => 'string',
								'default' => esc_html__( 'Instagram feed', 'trx_addons' ),
							),
							'type'        => array(
								'type'    => 'string',
								'default' => 'default',
							),
							'demo'        => array(
								'type'    => 'boolean',
								'default' => 0,
							),
							'demo_thumb_size'  => array(
								'type'    => 'string',
								'default' => apply_filters( 'trx_addons_filter_thumb_size',
													trx_addons_get_thumb_size( 'avatar' ),
													'trx_addons_widget_instagram',
													array()
												),
							),
							'demo_files'  => array(
								'type'    => 'string',
								'default' => '',
							),
							'count'       => array(
								'type'    => 'number',
								'default' => 8,
							),
							'columns'     => array(
								'type'    => 'number',
								'default' => 4,
							),
							'columns_gap' => array(
								'type'    => 'number',
								'default' => 0,
							),
							'hashtag'     => array(
								'type'    => 'string',
								'default' => '',
							),
							'links'       => array(
								'type'    => 'string',
								'default' => 'instagram',
							),
							'ratio'       => array(
								'type'    => 'string',
								'default' => 'none',
							),
							'follow'      => array(
								'type'    => 'boolean',
								'default' => 0,
							),
							'follow_link' => array(
								'type'    => 'string',
								'default' => '',
							),
							// Rerender
							'reload'             => array(
								'type'    => 'string',
								'default' => '',
							),
						),
						trx_addons_gutenberg_get_param_id()
					),
					'render_callback' => 'trx_addons_gutenberg_sc_instagram_render_block',
				), 'trx-addons/instagram' )
			);
		}
	}
}

// Block render
if ( ! function_exists( 'trx_addons_gutenberg_sc_instagram_render_block' ) ) {
	function trx_addons_gutenberg_sc_instagram_render_block( $attributes = array() ) {
		return trx_addons_sc_widget_instagram( $attributes );
	}
}

// Return list of allowed layouts
if ( ! function_exists( 'trx_addons_gutenberg_sc_instagram_get_layouts' ) ) {
	add_filter( 'trx_addons_filter_gutenberg_sc_layouts', 'trx_addons_gutenberg_sc_instagram_get_layouts', 10, 1 );
	function trx_addons_gutenberg_sc_instagram_get_layouts( $array = array() ) {
		$array['sc_instagram'] = apply_filters( 'trx_addons_sc_type', trx_addons_components_get_allowed_layouts( 'widgets', 'instagram' ), 'trx_widget_instagram' );
		return $array;
	}
}

// Add shortcode's specific lists to the JS storage
if ( ! function_exists( 'trx_addons_sc_instagram_gutenberg_sc_params' ) ) {
	add_filter( 'trx_addons_filter_gutenberg_sc_params', 'trx_addons_sc_instagram_gutenberg_sc_params' );
	function trx_addons_sc_instagram_gutenberg_sc_params( $vars = array() ) {

		// If editor is active now
		$is_edit_mode = trx_addons_is_post_edit();

		// Return list of the instagram redirects
		$vars['sc_instagram_redirects'] = !$is_edit_mode ? array() : trx_addons_get_list_sc_instagram_redirects();

		// Default thumb size
		$vars['sc_instagram_demo_thumb_size'] = apply_filters( 'trx_addons_filter_thumb_size',
													trx_addons_get_thumb_size( 'avatar' ),
													'trx_addons_widget_instagram',
													array()
												);
		
		// Return list of the registered thumb sizes
		$vars['sc_instagram_thumb_sizes'] = !$is_edit_mode ? array() : trx_addons_get_list_thumbnail_sizes();

		// Return list of the image ratio
		$vars['sc_instagram_image_ratio'] = !$is_edit_mode ? array() : trx_addons_get_list_sc_image_ratio( false );

		return $vars;
	}
}
