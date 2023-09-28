<?php
/**
 * ThemeREX Addons Custom post type: Services (Gutenberg support)
 *
 * @package ThemeREX Addons
 * @since v1.4
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


// Gutenberg Block
//------------------------------------------------------

// Add scripts and styles for the editor
if ( ! function_exists( 'trx_addons_gutenberg_sc_services_editor_assets' ) ) {
	add_action( 'enqueue_block_editor_assets', 'trx_addons_gutenberg_sc_services_editor_assets' );
	function trx_addons_gutenberg_sc_services_editor_assets() {
		if ( trx_addons_exists_gutenberg() && trx_addons_get_setting( 'allow_gutenberg_blocks' ) ) {
			// Scripts
			wp_enqueue_script(
				'trx-addons-gutenberg-editor-block-services',
				trx_addons_get_file_url( TRX_ADDONS_PLUGIN_CPT . 'services/gutenberg/services.gutenberg-editor.js' ),
				trx_addons_block_editor_dependencis(),
				filemtime( trx_addons_get_file_dir( TRX_ADDONS_PLUGIN_CPT . 'services/gutenberg/services.gutenberg-editor.js' ) ),
				true
			);
		}
	}
}

// Block register
if ( ! function_exists( 'trx_addons_sc_services_add_in_gutenberg' ) ) {
	add_action( 'init', 'trx_addons_sc_services_add_in_gutenberg' );
	function trx_addons_sc_services_add_in_gutenberg() {
		if ( trx_addons_exists_gutenberg() && trx_addons_get_setting( 'allow_gutenberg_blocks' ) ) {
			register_block_type(
				'trx-addons/services',
				apply_filters('trx_addons_gb_map', array(
					'attributes'      => array_merge(
						array(
							'type'               => array(
								'type'    => 'string',
								'default' => 'default',
							),
							'tabs_effect'        => array(
								'type'    => 'string',
								'default' => 'fade',
							),
							'featured'           => array(
								'type'    => 'string',
								'default' => 'image',
							),
							'featured_position'  => array(
								'type'    => 'string',
								'default' => 'top',
							),
							'thumb_size'	     => array(
								'type'    => 'string',
								'default' => '',
							),
							'no_links'           => array(
								'type'    => 'boolean',
								'default' => false,
							),
							'more_text'          => array(
								'type'    => 'string',
								'default' => esc_html__( 'Read more' ),
							),
							'pagination'         => array(
								'type'    => 'string',
								'default' => 'none',
							),
							'hide_excerpt'       => array(
								'type'    => 'boolean',
								'default' => false,
							),
							'no_margin'          => array(
								'type'    => 'boolean',
								'default' => false,
							),
							'icons_animation'    => array(
								'type'    => 'boolean',
								'default' => false,
							),
							'hide_bg_image'      => array(
								'type'    => 'boolean',
								'default' => false,
							),
							'popup'              => array(
								'type'    => 'boolean',
								'default' => false,
							),
							'post_type'          => array(
								'type'    => 'string',
								'default' => TRX_ADDONS_CPT_SERVICES_PT,
							),
							'taxonomy'           => array(
								'type'    => 'string',
								'default' => TRX_ADDONS_CPT_SERVICES_TAXONOMY,
							),
							'cat'                => array(
								'type'    => 'string',
								'default' => '0',
							),
						),
						trx_addons_gutenberg_get_param_query(),
						trx_addons_gutenberg_get_param_slider(),
						trx_addons_gutenberg_get_param_title(),
						trx_addons_gutenberg_get_param_button(),
						trx_addons_gutenberg_get_param_id()
					),
					'render_callback' => 'trx_addons_gutenberg_sc_services_render_block',
				), 'trx-addons/services' )
			);
		}
	}
}

// Block render
if ( ! function_exists( 'trx_addons_gutenberg_sc_services_render_block' ) ) {
	function trx_addons_gutenberg_sc_services_render_block( $attributes = array() ) {
		return trx_addons_sc_services( $attributes );
	}
}

// Return list of allowed layouts
if ( ! function_exists( 'trx_addons_gutenberg_sc_services_get_layouts' ) ) {
	add_filter( 'trx_addons_filter_gutenberg_sc_layouts', 'trx_addons_gutenberg_sc_services_get_layouts', 10, 1 );
	function trx_addons_gutenberg_sc_services_get_layouts( $array = array() ) {
		$array['trx_sc_services'] = apply_filters( 'trx_addons_sc_type', trx_addons_components_get_allowed_layouts( 'cpt', 'services', 'sc' ), 'trx_sc_services' );
		return $array;
	}
}

// Add shortcode's specific vars to the JS storage
if ( ! function_exists( 'trx_addons_gutenberg_sc_services_params' ) ) {
	add_filter( 'trx_addons_filter_gutenberg_sc_params', 'trx_addons_gutenberg_sc_services_params' );
	function trx_addons_gutenberg_sc_services_params( $vars = array() ) {
		if ( trx_addons_exists_gutenberg() && trx_addons_get_setting( 'allow_gutenberg_blocks' ) ) {

			$vars['CPT_SERVICES_PT']       = TRX_ADDONS_CPT_SERVICES_PT;
			$vars['CPT_SERVICES_TAXONOMY'] = TRX_ADDONS_CPT_SERVICES_TAXONOMY;

			// If editor is active now
			$is_edit_mode = trx_addons_is_post_edit();

			// Return list of the featured elements in services
			$vars['sc_services_featured'] = !$is_edit_mode ? array() : trx_addons_get_list_sc_services_featured();

			// Return list of positions of the featured element in services
			$vars['sc_services_featured_positions'] = !$is_edit_mode ? array() : trx_addons_get_list_sc_services_featured_positions();

			// Return list of tabs effects in services
			$vars['sc_services_tabs_effects'] = !$is_edit_mode ? array() : trx_addons_get_list_sc_services_tabs_effects();

			// Return list of registered thumb sizes
			$vars['sc_services_thumb_sizes']  = ! $is_edit_mode ? array() : array_merge( array( '' => __( 'Default', 'trx_addons' ) ), trx_addons_get_list_thumbnail_sizes() );

			return $vars;
		}
	}
}
