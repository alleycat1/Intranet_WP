<?php
/**
 * Shortcode: Smoke (Gutenberg support)
 *
 * @package ThemeREX Addons
 * @since v2.17.2
 */

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }



// Gutenberg Block
//------------------------------------------------------

// Add scripts and styles for the editor
if ( ! function_exists( 'trx_addons_gutenberg_sc_smoke_editor_assets' ) ) {
	add_action( 'enqueue_block_editor_assets', 'trx_addons_gutenberg_sc_smoke_editor_assets' );
	function trx_addons_gutenberg_sc_smoke_editor_assets() {
		if ( trx_addons_exists_gutenberg() && trx_addons_get_setting( 'allow_gutenberg_blocks' ) ) {
			wp_enqueue_script(
				'trx-addons-gutenberg-editor-block-smoke',
				trx_addons_get_file_url( TRX_ADDONS_PLUGIN_ADDONS . 'smoke/gutenberg/smoke.gutenberg-editor.js' ),
				trx_addons_block_editor_dependencis(),
				filemtime( trx_addons_get_file_dir( TRX_ADDONS_PLUGIN_ADDONS . 'smoke/gutenberg/smoke.gutenberg-editor.js' ) ),
				true
			);
		}
	}
}

// Block register
if ( ! function_exists( 'trx_addons_sc_smoke_add_in_gutenberg' ) ) {
	add_action( 'init', 'trx_addons_sc_smoke_add_in_gutenberg' );
	function trx_addons_sc_smoke_add_in_gutenberg() {
		if ( trx_addons_exists_gutenberg() && trx_addons_get_setting( 'allow_gutenberg_blocks' ) ) {
			register_block_type(
				'trx-addons/smoke',
				apply_filters('trx_addons_gb_map', array(
					'attributes'      => array(
						'type'               => array(
							'type'    => 'string',
							'default' => 'smoke',
						),
						'bg_color'           => array(
							'type'    => 'string',
							'default' => '#000000',
						),
						'tint_color'         => array(
							'type'    => 'string',
							'default' => '',
						),
						'smoke_curls'        => array(
							'type'    => 'number',
							'default' => 5,
						),
						'smoke_density'      => array(
							'type'    => 'number',
							'default' => 0.97,
						),
						'smoke_velocity'     => array(
							'type'    => 'number',
							'default' => 0.98,
						),
						'smoke_pressure'     => array(
							'type'    => 'number',
							'default' => 0.8,
						),
						'smoke_iterations'   => array(
							'type'    => 'number',
							'default' => 10,
						),
						'smoke_slap'         => array(
							'type'    => 'number',
							'default' => 0.6,
						),
						'use_image'          => array(
							'type'    => 'boolean',
							'default' => false,
						),
						'image'              => array(
							'type'    => 'number',
							'default' => 0,
						),
						'image_url'          => array(
							'type'    => 'string',
							'default' => '',
						),
						'image_repeat'       => array(
							'type'    => 'number',
							'default' => 5,
						),
						'cursor'             => array(
							'type'    => 'number',
							'default' => 0,
						),
						'cursor_url'         => array(
							'type'    => 'string',
							'default' => '',
						),
						'spots'              => array(
							'type'    => 'string',
							'default' => '',
						),						
						// Rerender
						'reload'             => array(
							'type'    => 'string',
							'default' => '',
						),
					),
					'render_callback' => 'trx_addons_gutenberg_sc_smoke_render_block',
				), 'trx-addons/smoke' )
			);
		}
	}
}

// Block render
if ( ! function_exists( 'trx_addons_gutenberg_sc_smoke_render_block' ) ) {
	function trx_addons_gutenberg_sc_smoke_render_block( $attributes = array() ) {
		if ( ! empty( $attributes['spots'] ) ) {
			if ( is_string( $attributes['spots'] ) ) {
				$attributes['spots'] = json_decode( $attributes['spots'], true );
			}
		}
		$is_edit_mode = trx_addons_is_post_edit();
		$placeholder = '';
		if ( $is_edit_mode ) {
			ob_start();
			$args = array_merge(
						array(
							'sc' => 'trx_sc_smoke',
							'title' => ! empty( $attributes['type'] ) ? $attributes['type'] : ''
						),
						$attributes
			);
			trx_addons_get_template_part('templates/tpl.sc_placeholder.php',
									'trx_addons_args_sc_placeholder',
									apply_filters( 'trx_addons_filter_sc_placeholder_args', $args )
								);
			$placeholder = ob_get_contents();
			ob_end_clean();
		}
		return ! $is_edit_mode ? trx_addons_sc_smoke( $attributes ) : $placeholder;
	}
}

// Add shortcode-specific lists to the js vars
if ( ! function_exists( 'trx_addons_gutenberg_sc_smoke_params' ) ) {
	add_filter( 'trx_addons_filter_gutenberg_sc_params', 'trx_addons_gutenberg_sc_smoke_params', 10, 1 );
	function trx_addons_gutenberg_sc_smoke_params( $vars = array() ) {

		// If editor is active now
		$is_edit_mode = trx_addons_is_post_edit();

		$vars['sc_smoke_types'] = ! $is_edit_mode ? array() : apply_filters( 'trx_addons_sc_type', trx_addons_smoke_list_types(), 'trx_sc_smoke' );
		$vars['sc_smoke_spot_motions'] = ! $is_edit_mode ? array() : trx_addons_get_list_sc_smoke_spot_motions();
		return $vars;
	}
}
