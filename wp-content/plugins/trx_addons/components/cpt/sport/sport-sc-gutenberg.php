<?php
/**
 * ThemeREX Addons: Sports Reviews Management (SRM).
 *                  Support different sports, championships, rounds, matches and players.
 *                  (Gutenberg support)
 *
 * @package ThemeREX Addons
 * @since v1.6.17
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}



// Gutenberg Block
//------------------------------------------------------

// Add scripts and styles for the editor
if ( ! function_exists( 'trx_addons_gutenberg_sc_matches_editor_assets' ) ) {
	add_action( 'enqueue_block_editor_assets', 'trx_addons_gutenberg_sc_matches_editor_assets' );
	function trx_addons_gutenberg_sc_matches_editor_assets() {
		if ( trx_addons_exists_gutenberg() && trx_addons_get_setting( 'allow_gutenberg_blocks' ) ) {
			// Scripts
			wp_enqueue_script(
				'trx-addons-gutenberg-editor-block-matches',
				trx_addons_get_file_url( TRX_ADDONS_PLUGIN_CPT . 'sport/gutenberg/matches.gutenberg-editor.js' ),
				trx_addons_block_editor_dependencis(),
				filemtime( trx_addons_get_file_dir( TRX_ADDONS_PLUGIN_CPT . 'sport/gutenberg/matches.gutenberg-editor.js' ) ),
				true
			);
		}
	}
}

// Block register
if ( ! function_exists( 'trx_addons_sc_matches_add_in_gutenberg' ) ) {
	add_action( 'init', 'trx_addons_sc_matches_add_in_gutenberg' );
	function trx_addons_sc_matches_add_in_gutenberg() {
		if ( trx_addons_exists_gutenberg() && trx_addons_get_setting( 'allow_gutenberg_blocks' ) ) {
			register_block_type(
				'trx-addons/matches',
				apply_filters('trx_addons_gb_map', array(
					'attributes'      => array_merge(
						array(
							'type'               => array(
								'type'    => 'string',
								'default' => 'default',
							),
							'sport'              => array(
								'type'    => 'string',
								'default' => trx_addons_get_option( 'sport_favorite' ),
							),
							'competition'        => array(
								'type'    => 'string',
								'default' => '',
							),
							'round'              => array(
								'type'    => 'string',
								'default' => '',
							),
							'main_matches'       => array(
								'type'    => 'boolean',
								'default' => false,
							),
							'position'           => array(
								'type'    => 'string',
								'default' => 'top',
							),
							'slider'             => array(
								'type'    => 'boolean',
								'default' => false,
							),
						),
						trx_addons_gutenberg_get_param_query(),
						trx_addons_gutenberg_get_param_title(),
						trx_addons_gutenberg_get_param_button(),
						trx_addons_gutenberg_get_param_id()
					),
					'render_callback' => 'trx_addons_gutenberg_sc_matches_render_block',
				), 'trx-addons/matches' )
			);
		}
	}
}

// Block render
if ( ! function_exists( 'trx_addons_gutenberg_sc_matches_render_block' ) ) {
	function trx_addons_gutenberg_sc_matches_render_block( $attributes = array() ) {
		return trx_addons_sc_matches( $attributes );
	}
}

// Return list of allowed layouts
if ( ! function_exists( 'trx_addons_gutenberg_sc_matches_get_layouts' ) ) {
	add_filter( 'trx_addons_filter_gutenberg_sc_layouts', 'trx_addons_gutenberg_sc_matches_get_layouts', 10, 1 );
	function trx_addons_gutenberg_sc_matches_get_layouts( $array = array() ) {
		$array['trx_sc_matches'] = apply_filters(
			'trx_addons_sc_type', array(
				'default' => esc_html__( 'Default', 'trx_addons' ),
			), 'trx_sc_matches'
		);

		return $array;
	}
}



// Gutenberg Block
//------------------------------------------------------

// Add scripts and styles for the editor
if ( ! function_exists( 'trx_addons_gutenberg_sc_points_editor_assets' ) ) {
	add_action( 'enqueue_block_editor_assets', 'trx_addons_gutenberg_sc_points_editor_assets' );
	function trx_addons_gutenberg_sc_points_editor_assets() {
		if ( trx_addons_exists_gutenberg() && trx_addons_get_setting( 'allow_gutenberg_blocks' ) ) {
			// Scripts
			wp_enqueue_script(
				'trx-addons-gutenberg-editor-block-points',
				trx_addons_get_file_url( TRX_ADDONS_PLUGIN_CPT . 'sport/gutenberg/points.gutenberg-editor.js' ),
				trx_addons_block_editor_dependencis(),
				filemtime( trx_addons_get_file_dir( TRX_ADDONS_PLUGIN_CPT . 'sport/gutenberg/points.gutenberg-editor.js' ) ),
				true
			);
		}
	}
}

// Block register
if ( ! function_exists( 'trx_addons_sc_points_add_in_gutenberg' ) ) {
	add_action( 'init', 'trx_addons_sc_points_add_in_gutenberg' );
	function trx_addons_sc_points_add_in_gutenberg() {
		if ( trx_addons_exists_gutenberg() && trx_addons_get_setting( 'allow_gutenberg_blocks' ) ) {
			register_block_type(
				'trx-addons/points',
				apply_filters('trx_addons_gb_map', array(
					'attributes'      => array_merge(
						array(
							'type'               => array(
								'type'    => 'string',
								'default' => 'default',
							),
							'sport'              => array(
								'type'    => 'string',
								'default' => trx_addons_get_option( 'sport_favorite' ),
							),
							'competition'        => array(
								'type'    => 'string',
								'default' => '',
							),
							'logo'               => array(
								'type'    => 'boolean',
								'default' => false,
							),
							'accented_top'       => array(
								'type'    => 'number',
								'default' => 3,
							),
							'accented_bottom'    => array(
								'type'    => 'number',
								'default' => 3,
							),
						),
						trx_addons_gutenberg_get_param_title(),
						trx_addons_gutenberg_get_param_button(),
						trx_addons_gutenberg_get_param_id()
					),
					'render_callback' => 'trx_addons_gutenberg_sc_points_render_block',
				), 'trx-addons/points' )
			);
		}
	}
}

// Block render
if ( ! function_exists( 'trx_addons_gutenberg_sc_points_render_block' ) ) {
	function trx_addons_gutenberg_sc_points_render_block( $attributes = array() ) {
		return trx_addons_sc_points( $attributes );
	}
}

// Return list of allowed layouts
if ( ! function_exists( 'trx_addons_gutenberg_sc_points_get_layouts' ) ) {
	add_filter( 'trx_addons_filter_gutenberg_sc_layouts', 'trx_addons_gutenberg_sc_points_get_layouts', 10, 1 );
	function trx_addons_gutenberg_sc_points_get_layouts( $array = array() ) {
		$array['trx_sc_points'] = apply_filters(
			'trx_addons_sc_type', array(
				'default' => esc_html__( 'Default', 'trx_addons' ),
			), 'trx_sc_points'
		);

		return $array;
	}
}



// Add shortcode's specific vars to the JS storage
if ( ! function_exists( 'trx_addons_gutenberg_sc_sport_params' ) ) {
	add_filter( 'trx_addons_filter_gutenberg_sc_params', 'trx_addons_gutenberg_sc_sport_params' );
	function trx_addons_gutenberg_sc_sport_params( $vars = array() ) {
		if ( trx_addons_exists_gutenberg() && trx_addons_get_setting( 'allow_gutenberg_blocks' ) ) {

			// If editor is active now
			$is_edit_mode = trx_addons_is_post_edit();

			$vars['sc_sport_default'] = trx_addons_get_option( 'sport_favorite' );

			// Return list of sports
			$vars['sc_sports_list'] = !$is_edit_mode ? array() : trx_addons_get_list_terms( false, TRX_ADDONS_CPT_COMPETITIONS_TAXONOMY );

			// Return list of competition
			$rounds_list                        = array();
			$vars['sc_sport_competitions_list'] = array();

			if ($is_edit_mode) {
				foreach ( $vars['sc_sports_list'] as $key => $value ) {
					$vars['sc_sport_competitions_list'][ $key ] = trx_addons_get_list_posts(
						false, array(
							'post_type'      => TRX_ADDONS_CPT_COMPETITIONS_PT,
							'taxonomy'       => TRX_ADDONS_CPT_COMPETITIONS_TAXONOMY,
							'taxonomy_value' => $key,
							'meta_key'       => 'trx_addons_competition_date',
							'orderby'        => 'meta_value',
							'order'          => 'ASC',
							'not_selected'   => false,
						)
					);

					foreach ( $vars['sc_sport_competitions_list'][ $key ] as $key2 => $value2 ) {
						$rounds_list[ $key2 ] = trx_addons_get_list_posts(
							false, array(
								'post_type'    => TRX_ADDONS_CPT_ROUNDS_PT,
								'post_parent'  => $key2,
								'meta_key'     => 'trx_addons_round_date',
								'orderby'      => 'meta_value',
								'order'        => 'ASC',
								'not_selected' => false,
							)
						);
					}
				}
			}

			// Return list of rounds
			$vars['sc_sport_rounds_list'] = array(
				'last' => esc_html__( 'Last round', 'trx_addons' ),
				'next' => esc_html__( 'Next round', 'trx_addons' ),
			);
			$vars['sc_sport_rounds_list'] = !$is_edit_mode ? array() : trx_addons_array_merge( $vars['sc_sport_rounds_list'], $rounds_list );

			// Return list of positions
			$vars['sc_sport_positions'] = !$is_edit_mode ? array() : trx_addons_get_list_sc_matches_positions();

			return $vars;
		}
	}
}
