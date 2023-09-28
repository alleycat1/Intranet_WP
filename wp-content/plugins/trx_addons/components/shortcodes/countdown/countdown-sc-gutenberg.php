<?php
/**
 * Shortcode: Countdown (Gutenberg support)
 *
 * @package ThemeREX Addons
 * @since v1.4.3
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}



// Gutenberg Block
//------------------------------------------------------

// Add scripts and styles for the editor
if ( ! function_exists( 'trx_addons_gutenberg_sc_countdown_editor_assets' ) ) {
	add_action( 'enqueue_block_editor_assets', 'trx_addons_gutenberg_sc_countdown_editor_assets' );
	function trx_addons_gutenberg_sc_countdown_editor_assets() {
		if ( trx_addons_exists_gutenberg() && trx_addons_get_setting( 'allow_gutenberg_blocks' ) ) {
			// Scripts
			wp_enqueue_script(
				'trx-addons-gutenberg-editor-block-countdown',
				trx_addons_get_file_url( TRX_ADDONS_PLUGIN_SHORTCODES . 'countdown/gutenberg/countdown.gutenberg-editor.js' ),
				trx_addons_block_editor_dependencis(),
				filemtime( trx_addons_get_file_dir( TRX_ADDONS_PLUGIN_SHORTCODES . 'countdown/gutenberg/countdown.gutenberg-editor.js' ) ),
				true
			);
		}
	}
}

// Block register
if ( ! function_exists( 'trx_addons_sc_countdown_add_in_gutenberg' ) ) {
	add_action( 'init', 'trx_addons_sc_countdown_add_in_gutenberg' );
	function trx_addons_sc_countdown_add_in_gutenberg() {
		if ( trx_addons_exists_gutenberg() && trx_addons_get_setting( 'allow_gutenberg_blocks' ) ) {
			register_block_type(
				'trx-addons/countdown',
				apply_filters('trx_addons_gb_map', array(
					'attributes'      => array_merge(
						array(
							'type'               => array(
								'type'    => 'string',
								'default' => 'default',
							),
							'align'              => array(
								'type'    => 'string',
								'default' => 'none',
							),
							'count_restart'      => array(
								'type'    => 'boolean',
								'default' => true,
							),
							'count_to'           => array(
								'type'    => 'boolean',
								'default' => true,
							),
							'date'               => array(
								'type'    => 'string',
								'default' => '',
							),
							'time'               => array(
								'type'    => 'string',
								'default' => '',
							),
							'date_time_restart'  => array(
								'type'    => 'string',
								'default' => '',
							),
							// Reload block - hidden option
							'reload'             => array(
								'type'    => 'string',
								'default' => '',
							),
						),
						trx_addons_gutenberg_get_param_title(),
						trx_addons_gutenberg_get_param_button(),
						trx_addons_gutenberg_get_param_id()
					),
					'render_callback' => 'trx_addons_gutenberg_sc_countdown_render_block',
				), 'trx-addons/countdown' )
			);
		}
	}
}

// Block render
if ( ! function_exists( 'trx_addons_gutenberg_sc_countdown_render_block' ) ) {
	function trx_addons_gutenberg_sc_countdown_render_block( $attributes = array() ) {
		return trx_addons_sc_countdown( $attributes );
	}
}

// Return list of allowed layouts
if ( ! function_exists( 'trx_addons_gutenberg_sc_countdown_get_layouts' ) ) {
	add_filter( 'trx_addons_filter_gutenberg_sc_layouts', 'trx_addons_gutenberg_sc_countdown_get_layouts', 10, 1 );
	function trx_addons_gutenberg_sc_countdown_get_layouts( $array = array() ) {
		$array['sc_countdown'] = apply_filters( 'trx_addons_sc_type', trx_addons_components_get_allowed_layouts( 'sc', 'countdown' ), 'trx_sc_countdown' );
		return $array;
	}
}
