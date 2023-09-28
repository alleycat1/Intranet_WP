<?php
/**
 * Shortcode: Content container (Gutenberg support)
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
if ( ! function_exists( 'trx_addons_gutenberg_sc_content_editor_assets' ) ) {
	add_action( 'enqueue_block_editor_assets', 'trx_addons_gutenberg_sc_content_editor_assets' );
	function trx_addons_gutenberg_sc_content_editor_assets() {
		if ( trx_addons_exists_gutenberg() && trx_addons_get_setting( 'allow_gutenberg_blocks' ) ) {
			wp_enqueue_script(
				'trx-addons-gutenberg-editor-block-content',
				trx_addons_get_file_url( TRX_ADDONS_PLUGIN_SHORTCODES . 'content/gutenberg/content.gutenberg-editor.js' ),
				trx_addons_block_editor_dependencis(),
				filemtime( trx_addons_get_file_dir( TRX_ADDONS_PLUGIN_SHORTCODES . 'content/gutenberg/content.gutenberg-editor.js' ) ),
				true
			);
		}
	}
}

// Block register
if ( ! function_exists( 'trx_addons_sc_content_add_in_gutenberg' ) ) {
	add_action( 'init', 'trx_addons_sc_content_add_in_gutenberg' );
	function trx_addons_sc_content_add_in_gutenberg() {
		if ( trx_addons_exists_gutenberg() && trx_addons_get_setting( 'allow_gutenberg_blocks' ) ) {
			register_block_type(
				'trx-addons/content',
				apply_filters('trx_addons_gb_map', array(
					'attributes'      => array_merge(
						array(
							'type'                => array(
								'type'    => 'string',
								'default' => 'default',
							),
							'size'                => array(
								'type'    => 'string',
								'default' => 'none',
							),
							'paddings'            => array(
								'type'    => 'string',
								'default' => 'none',
							),
							'margins'             => array(
								'type'    => 'string',
								'default' => 'none',
							),
							'float'               => array(
								'type'    => 'string',
								'default' => 'none',
							),
							'align'               => array(
								'type'    => 'string',
								'default' => 'none',
							),
							'push'                => array(
								'type'    => 'string',
								'default' => 'none',
							),
							'push_hide_on_tablet' => array(
								'type'    => 'boolean',
								'default' => false,
							),
							'push_hide_on_mobile' => array(
								'type'    => 'boolean',
								'default' => false,
							),
							'pull'                => array(
								'type'    => 'string',
								'default' => 'none',
							),
							'pull_hide_on_tablet' => array(
								'type'    => 'boolean',
								'default' => false,
							),
							'pull_hide_on_mobile' => array(
								'type'    => 'boolean',
								'default' => false,
							),
							'shift_x'             => array(
								'type'    => 'string',
								'default' => 'none',
							),
							'shift_y'             => array(
								'type'    => 'string',
								'default' => 'none',
							),
							'number'              => array(
								'type'    => 'string',
								'default' => '',
							),
							'number_position'     => array(
								'type'    => 'string',
								'default' => 'br',
							),
							'number_color'        => array(
								'type'    => 'string',
								'default' => '',
							),
							'extra_bg'            => array(
								'type'    => 'string',
								'default' => 'none',
							),
							'extra_bg_mask'       => array(
								'type'    => 'string',
								'default' => 'none',
							),
							'content'       => array(
								'type'    => 'string',
								'default' => ''
							),
						),
						trx_addons_gutenberg_get_param_title(),
						trx_addons_gutenberg_get_param_button(),
						trx_addons_gutenberg_get_param_id()
					),
					'render_callback' => 'trx_addons_gutenberg_sc_content_render_block',
				), 'trx-addons/content' )
			);
		}
	}
}

// Block render
if ( ! function_exists( 'trx_addons_gutenberg_sc_content_render_block' ) ) {
	function trx_addons_gutenberg_sc_content_render_block( $attributes, $content = '' ) {
		return trx_addons_sc_content( $attributes, function_exists('trx_addons_gutenberg_is_content_built') && trx_addons_gutenberg_is_content_built( $content ) ? do_blocks( $content ) : $content );
	}
}

// Return list of allowed layouts
if ( ! function_exists( 'trx_addons_gutenberg_sc_content_get_layouts' ) ) {
	add_filter( 'trx_addons_filter_gutenberg_sc_layouts', 'trx_addons_gutenberg_sc_content_get_layouts', 10, 1 );
	function trx_addons_gutenberg_sc_content_get_layouts( $array = array() ) {
		$array['sc_content'] = apply_filters( 'trx_addons_sc_type', trx_addons_components_get_allowed_layouts( 'sc', 'content' ), 'trx_sc_content' );
		return $array;
	}
}


// Add shortcode's specific lists to the JS storage
if ( ! function_exists( 'trx_addons_sc_content_gutenberg_sc_params' ) ) {
	add_filter( 'trx_addons_filter_gutenberg_sc_params', 'trx_addons_sc_content_gutenberg_sc_params' );
	function trx_addons_sc_content_gutenberg_sc_params( $vars = array() ) {

		// If editor is active now
		$is_edit_mode = trx_addons_is_post_edit();

		// Return list of the content's widths
		$vars['sc_content_widths'] = !$is_edit_mode ? array() : trx_addons_get_list_sc_content_widths();

		// Return list of the content's paddings and margins sizes
		$vars['sc_content_paddings_and_margins'] = !$is_edit_mode ? array() : trx_addons_get_list_sc_content_paddings_and_margins();

		// Return list of the content's push and pull sizes
		$vars['sc_content_push_and_pull'] = !$is_edit_mode ? array() : trx_addons_get_list_sc_content_push_and_pull();

		// Return list of the shift sizes to move content along X- and/or Y-axis
		$vars['sc_content_shift'] = !$is_edit_mode ? array() : trx_addons_get_list_sc_content_shift();

		// Return list of the bg sizes to oversize content area
		$vars['sc_content_extra_bg'] = !$is_edit_mode ? array() : trx_addons_get_list_sc_content_extra_bg();

		// Return list of the bg mask values to color tone of the bg image
		$vars['sc_content_extra_bg_mask'] = !$is_edit_mode ? array() : trx_addons_get_list_sc_content_extra_bg_mask();

		return $vars;
	}
}
