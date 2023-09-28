<?php
/**
 * Widget: Users list (Gutenberg support)
 *
 * @package ThemeREX Addons
 * @since v1.84.0
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}



// Gutenberg Block
//------------------------------------------------------

// Add scripts and styles for the editor
if ( ! function_exists( 'trx_addons_gutenberg_sc_users_editor_assets' ) ) {
	add_action( 'enqueue_block_editor_assets', 'trx_addons_gutenberg_sc_users_editor_assets' );
	function trx_addons_gutenberg_sc_users_editor_assets() {
		if ( trx_addons_exists_gutenberg() && trx_addons_get_setting( 'allow_gutenberg_blocks' ) ) {
			// Scripts
			wp_enqueue_script(
				'trx-addons-gutenberg-editor-block-users',
				trx_addons_get_file_url( TRX_ADDONS_PLUGIN_WIDGETS . 'users/gutenberg/users.gutenberg-editor.js' ),
				trx_addons_block_editor_dependencis(),
				filemtime( trx_addons_get_file_dir( TRX_ADDONS_PLUGIN_WIDGETS . 'users/gutenberg/users.gutenberg-editor.js' ) ),
				true
			);
		}
	}
}

// Block register
if ( ! function_exists( 'trx_addons_sc_users_add_in_gutenberg' ) ) {
	add_action( 'init', 'trx_addons_sc_users_add_in_gutenberg' );
	function trx_addons_sc_users_add_in_gutenberg() {
		if ( trx_addons_exists_gutenberg() && trx_addons_get_setting( 'allow_gutenberg_blocks' ) ) {
			register_block_type(
				'trx-addons/users',
				apply_filters('trx_addons_gb_map', array(
					'attributes'      => array_merge(
						array(
							'title'           => array(
								'type'    => 'string',
								'default' => '',
							),
							'type'            => array(
								'type'    => 'string',
								'default' => 'default',
							),
							'roles'           => array(
								'type'    => 'string',
								'default' => 'author',
							),
							'number'          => array(
								'type'    => 'number',
								'default' => 3,
							),
							'columns'         => array(
								'type'    => 'number',
								'default' => 0,
							),
						),
						trx_addons_gutenberg_get_param_slider(),
						trx_addons_gutenberg_get_param_title(),
						trx_addons_gutenberg_get_param_id()
					),
					'render_callback' => 'trx_addons_gutenberg_sc_users_render_block',
				), 'trx-addons/users' )
			);
		}
	}
}

// Block render
if ( ! function_exists( 'trx_addons_gutenberg_sc_users_render_block' ) ) {
	function trx_addons_gutenberg_sc_users_render_block( $attributes = array() ) {
		return trx_addons_sc_widget_users( $attributes );
	}
}

// Return list of allowed layouts
if ( ! function_exists( 'trx_addons_gutenberg_sc_users_get_layouts' ) ) {
	add_filter( 'trx_addons_filter_gutenberg_sc_layouts', 'trx_addons_gutenberg_sc_users_get_layouts', 10, 1 );
	function trx_addons_gutenberg_sc_users_get_layouts( $array = array() ) {
		$array['sc_users'] = apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('sc', 'users'), 'trx_widget_users');
		return $array;
	}
}

// Add shortcode-specific lists to the js vars
if ( ! function_exists( 'trx_addons_gutenberg_sc_users_params' ) ) {
	add_filter( 'trx_addons_filter_gutenberg_sc_params', 'trx_addons_gutenberg_sc_users_params', 10, 1 );
	function trx_addons_gutenberg_sc_users_params( $vars = array() ) {

		// If editor is active now
		$is_edit_mode = trx_addons_is_post_edit();

		$vars['sc_users_roles'] = ! $is_edit_mode ? array() : trx_addons_get_list_users_roles();
		return $vars;
	}
}
