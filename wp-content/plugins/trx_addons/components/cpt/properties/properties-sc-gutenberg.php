<?php
/**
 * ThemeREX Addons Custom post type: Properties (Gutenberg support)
 *
 * @package ThemeREX Addons
 * @since v1.6.22
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}



// Gutenberg Block
//------------------------------------------------------

// Add scripts and styles for the editor
if ( ! function_exists( 'trx_addons_gutenberg_sc_properties_editor_assets' ) ) {
	add_action( 'enqueue_block_editor_assets', 'trx_addons_gutenberg_sc_properties_editor_assets' );
	function trx_addons_gutenberg_sc_properties_editor_assets() {
		if ( trx_addons_exists_gutenberg() && trx_addons_get_setting( 'allow_gutenberg_blocks' ) ) {
			// Scripts
			wp_enqueue_script(
				'trx-addons-gutenberg-editor-block-properties',
				trx_addons_get_file_url( TRX_ADDONS_PLUGIN_CPT . 'properties/gutenberg/properties.gutenberg-editor.js' ),
				trx_addons_block_editor_dependencis(),
				filemtime( trx_addons_get_file_dir( TRX_ADDONS_PLUGIN_CPT . 'properties/gutenberg/properties.gutenberg-editor.js' ) ),
				true
			);
		}
	}
}

// Block register
if ( ! function_exists( 'trx_addons_sc_properties_add_in_gutenberg' ) ) {
	add_action( 'init', 'trx_addons_sc_properties_add_in_gutenberg' );
	function trx_addons_sc_properties_add_in_gutenberg() {
		if ( trx_addons_exists_gutenberg() && trx_addons_get_setting( 'allow_gutenberg_blocks' ) ) {
			register_block_type(
				'trx-addons/properties',
				apply_filters('trx_addons_gb_map', array(
					'attributes'      => array_merge(
						array(
							'type'                    => array(
								'type'    => 'string',
								'default' => 'default',
							),
							'more_text'               => array(
								'type'    => 'string',
								'default' => esc_html__( 'Read more' ),
							),
							'pagination'              => array(
								'type'    => 'string',
								'default' => 'none',
							),
							'map_height'        => array(
								'type'    => 'number',
								'default' => 350,
							),
							'properties_type'         => array(
								'type'    => 'string',
								'default' => '0',
							),
							'properties_status'       => array(
								'type'    => 'string',
								'default' => '0',
							),
							'properties_labels'       => array(
								'type'    => 'string',
								'default' => '0',
							),
							'properties_country'      => array(
								'type'    => 'string',
								'default' => '0',
							),
							'properties_city'         => array(
								'type'    => 'string',
								'default' => '0',
							),
							'properties_neighborhood' => array(
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
					'render_callback' => 'trx_addons_gutenberg_sc_properties_render_block',
				), 'trx-addons/properties' )
			);
		}
	}
}

// Block render
if ( ! function_exists( 'trx_addons_gutenberg_sc_properties_render_block' ) ) {
	function trx_addons_gutenberg_sc_properties_render_block( $attributes = array() ) {
		return trx_addons_sc_properties( $attributes );
	}
}

// Return list of allowed layouts
if ( ! function_exists( 'trx_addons_gutenberg_sc_properties_get_layouts' ) ) {
	add_filter( 'trx_addons_filter_gutenberg_sc_layouts', 'trx_addons_gutenberg_sc_properties_get_layouts', 10, 1 );
	function trx_addons_gutenberg_sc_properties_get_layouts( $array = array() ) {
		$array['trx_sc_properties'] = apply_filters( 'trx_addons_sc_type', trx_addons_components_get_allowed_layouts( 'cpt', 'properties', 'sc' ), 'trx_sc_properties' );

		return $array;
	}
}

// Add shortcode's specific vars to the JS storage
if ( ! function_exists( 'trx_addons_gutenberg_sc_properties_params' ) ) {
	add_filter( 'trx_addons_filter_gutenberg_sc_params', 'trx_addons_gutenberg_sc_properties_params' );
	function trx_addons_gutenberg_sc_properties_params( $vars = array() ) {
		if ( trx_addons_exists_gutenberg() && trx_addons_get_setting( 'allow_gutenberg_blocks' ) ) {

			// If editor is active now
			$is_edit_mode = trx_addons_is_post_edit();

			// Properties type
			$vars['sc_properties_type']    = !$is_edit_mode ? array() : trx_addons_get_list_terms( false, TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_TYPE );
			$vars['sc_properties_type'][0] = trx_addons_get_not_selected_text( esc_html__( 'Type', 'trx_addons' ) );

			// Properties status
			$vars['sc_properties_status']    = !$is_edit_mode ? array() : trx_addons_get_list_terms( false, TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_STATUS );
			$vars['sc_properties_status'][0] = trx_addons_get_not_selected_text( esc_html__( 'Status', 'trx_addons' ) );

			// Properties label
			$vars['sc_properties_labels']    = !$is_edit_mode ? array() : trx_addons_get_list_terms( false, TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_LABELS );
			$vars['sc_properties_labels'][0] = trx_addons_get_not_selected_text( esc_html__( 'Label', 'trx_addons' ) );

			// Properties country
			$vars['sc_properties_country']    = !$is_edit_mode ? array() : trx_addons_get_list_terms( false, TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_COUNTRY );
			$vars['sc_properties_country'][0] = trx_addons_get_not_selected_text( esc_html__( 'Country', 'trx_addons' ) );

			// Properties cities
			$vars['sc_properties_states']     = !$is_edit_mode ? array() : trx_addons_get_list_terms( false, TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_STATE );
			$vars['sc_properties_states'][0]  = trx_addons_get_not_selected_text( esc_html__( 'State', 'trx_addons' ) );

			// Properties cities
			$vars['sc_properties_cities']     = !$is_edit_mode ? array() : trx_addons_get_list_terms( false, TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_CITY );
			$vars['sc_properties_cities'][0]  = trx_addons_get_not_selected_text( esc_html__( 'City', 'trx_addons' ) );

			// Properties neighborhoods
			$vars['sc_properties_neighborhoods']    = !$is_edit_mode ? array() : trx_addons_get_list_terms( false, TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_NEIGHBORHOOD );
			$vars['sc_properties_neighborhoods'][0] = trx_addons_get_not_selected_text( esc_html__( 'Neighborhood', 'trx_addons' ) );

			return $vars;
		}
	}
}
