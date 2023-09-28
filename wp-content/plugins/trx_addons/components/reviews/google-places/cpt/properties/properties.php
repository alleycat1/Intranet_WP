<?php
/**
 * Plugin support: Google Places API for the CPT Properties
 *
 * @package ThemeREX Addons
 * @since v2.7.0
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

// Add 'Google Place ID' field to the meta data of the CPT Properties (if exists)
if ( ! function_exists( 'trx_addons_google_places_add_meta_field' ) ) {
	add_filter( 'trx_addons_filter_meta_box_fields', 'trx_addons_google_places_add_meta_field', 10, 2 );
	function trx_addons_google_places_add_meta_field( $mb, $post_type ) {
		if ( defined( 'TRX_ADDONS_CPT_PROPERTIES_PT' ) && TRX_ADDONS_CPT_PROPERTIES_PT == $post_type ) {
			if ( trx_addons_reviews_enable( $post_type ) && ! isset( $mb['google_place_id'] ) ) {
				trx_addons_array_insert_after( $mb, 'location', array(
					'google_place_id' => array(
						'title'   => esc_html__( 'Google Place ID', 'trx_addons' ),
						'desc'    => wp_kses( sprintf( __( 'Paste a Google Place ID for this property. You can detect a place id %1$s here %2$s or get it by using an autocomplete while search address in the previous field.', 'trx_addons' ),
														'<a href="https://developers.google.com/places/place-id" target="_blank">',
														'</a>' ),
											'trx_addons_kses_content'
									),
						'std'     => '',
						'type'    => 'text',
					)
				) );
			}
		}
		return $mb;
	}
}

// Add tab 'Google Reviews' to the tabs list for the single property
if ( ! function_exists( 'trx_addons_google_places_add_tab' ) ) {
	add_filter( 'trx_addons_filter_single_property_tabs', 'trx_addons_google_places_add_tab', 10, 2 );
	function trx_addons_google_places_add_tab( $list, $trx_addons_meta ) {
		if ( defined( 'TRX_ADDONS_CPT_PROPERTIES_PT' )
			&& trx_addons_reviews_enable( TRX_ADDONS_CPT_PROPERTIES_PT )
			&& ! empty( $trx_addons_meta['google_place_id'] )
		) {
			trx_addons_array_insert_after( $list, 'map', array(
				'google_reviews' => __('Reviews from Google', 'trx_addons'),
			) );
		}
		return $list;
	}
}

// Add section 'Google reviews' to the single property
if ( ! function_exists( 'trx_addons_google_places_add_section' ) ) {
	add_action( 'trx_addons_action_after_single_property_section', 'trx_addons_google_places_add_section', 10, 4 );
	function trx_addons_google_places_add_section( $section, $trx_addons_tabs_id, $trx_addons_meta, $trx_addons_section_titles ) {
		if ( $section == 'description'
			&& defined( 'TRX_ADDONS_CPT_PROPERTIES_PT' )
			&& trx_addons_reviews_enable( TRX_ADDONS_CPT_PROPERTIES_PT )
			&& ! empty( $trx_addons_meta['google_place_id'] )
		) {
			$info = trx_addons_google_places_get_info( $trx_addons_meta['google_place_id'] );
			if ( is_array( $info ) && ! empty( $info['reviews'] ) ) {
				do_action( 'trx_addons_action_before_single_property_section', 'google_reviews', $trx_addons_tabs_id, $trx_addons_meta, $trx_addons_section_titles );
				?><section id="<?php echo esc_attr( $trx_addons_tabs_id . '_google_reviews'); ?>_content" class="properties_page_section properties_page_google_reviews">
					<h4 class="properties_page_section_title"><?php echo esc_html( $trx_addons_section_titles['google_reviews'] ); ?></h4><?php
					?><div class="properties_page_google_reviews_details"><?php
						trx_addons_get_template_part( TRX_ADDONS_PLUGIN_REVIEWS . 'google-places/cpt/properties/tpl.properties.parts.google-reviews.php',
														'trx_addons_args_properties_place',
														array(
															'meta' => $trx_addons_meta,
															'info' => $info
														)
													);
					?></div>
				</section><?php
				do_action( 'trx_addons_action_after_single_property_section', 'google_reviews', $trx_addons_tabs_id, $trx_addons_meta, $trx_addons_section_titles );
			}
		}
	}
}

	
// Load required styles and scripts for the frontend
if ( ! function_exists( 'trx_addons_google_places_load_scripts_front' ) ) {
	add_action( "wp_enqueue_scripts", 'trx_addons_google_places_load_scripts_front', TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY);
	add_action( "trx_addons_action_pagebuilder_preview_scripts", 'trx_addons_google_places_load_scripts_front', 10, 1 );
	function trx_addons_google_places_load_scripts_front( $force = false ) {
		if ( ! defined( 'TRX_ADDONS_CPT_PROPERTIES_PT' ) ) {
			return;
		}
		trx_addons_enqueue_optimized( 'cpt_properties-google-places', $force, array(
			'css'  => array(
				'trx_addons-cpt_properties-google-places' => array( 'src' => TRX_ADDONS_PLUGIN_REVIEWS . 'google-places/cpt/properties/properties.css' ),
			),
			'need' => function_exists( 'trx_addons_is_properties_page' ) && trx_addons_is_properties_page(),
			'check' => array(
				array( 'type' => 'sc',  'sc' => 'trx_sc_properties' ),
				array( 'type' => 'gb',  'sc' => 'wp:trx-addons/properties' ),
				array( 'type' => 'elm', 'sc' => '"widgetType":"trx_sc_properties"' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[trx_sc_properties' ),
			)
		) );
	}
}

// Enqueue responsive styles for frontend
if ( ! function_exists( 'trx_addons_google_places_load_scripts_front_responsive' ) ) {
	add_action( 'wp_enqueue_scripts', 'trx_addons_google_places_load_scripts_front_responsive', TRX_ADDONS_ENQUEUE_RESPONSIVE_PRIORITY );
	add_action( 'trx_addons_action_load_scripts_front_cpt_properties', 'trx_addons_google_places_load_scripts_front_responsive', 10, 1 );
	function trx_addons_google_places_load_scripts_front_responsive( $force = false ) {
		static $loaded = false;
		if ( ! defined( 'TRX_ADDONS_CPT_PROPERTIES_PT' ) ) {
			return;
		}
		trx_addons_enqueue_optimized_responsive( 'cpt_properties-google-places', $force, array(
			'css'  => array(
				'trx_addons-cpt_properties-google-places-responsive' => array(
					'src' => TRX_ADDONS_PLUGIN_REVIEWS . 'google-places/cpt/properties/properties.responsive.css',
					'media' => 'sm'
				),
			),
		) );
	}
}
	
// Merge shortcode's specific styles into single stylesheet
if ( ! function_exists( 'trx_addons_google_places_merge_styles' ) ) {
	add_filter("trx_addons_filter_merge_styles", 'trx_addons_google_places_merge_styles');
	function trx_addons_google_places_merge_styles( $list ) {
		if ( defined( 'TRX_ADDONS_CPT_PROPERTIES_PT' ) ) {
			$list[ TRX_ADDONS_PLUGIN_REVIEWS . 'google-places/cpt/properties/properties.css' ] = false;
		}
		return $list;
	}
}

// Merge shortcode's specific styles to the single stylesheet (responsive)
if ( !function_exists( 'trx_addons_google_places_merge_styles_responsive' ) ) {
	add_filter("trx_addons_filter_merge_styles_responsive", 'trx_addons_google_places_merge_styles_responsive');
	function trx_addons_google_places_merge_styles_responsive($list) {
		if ( defined( 'TRX_ADDONS_CPT_PROPERTIES_PT' ) ) {
			$list[ TRX_ADDONS_PLUGIN_REVIEWS . 'google-places/cpt/properties/properties.responsive.css' ] = false;
		}
		return $list;
	}
}
	
// Load styles and scripts if present in the cache of the menu
if ( !function_exists( 'trx_addons_google_places_check_in_html_output' ) ) {
	add_filter( 'trx_addons_filter_get_menu_cache_html', 'trx_addons_google_places_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_show_layout_from_cache', 'trx_addons_google_places_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_check_page_content', 'trx_addons_google_places_check_in_html_output', 10, 1 );
	function trx_addons_google_places_check_in_html_output( $content = '' ) {
		if (   ! defined( 'TRX_ADDONS_CPT_PROPERTIES_PT' )
			|| ! defined( 'TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_CITY' )
			|| ! defined( 'TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_COUNTRY' )
			|| ! defined( 'TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_TYPE' )
			|| ! defined( 'TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_STATE' )
			|| ! defined( 'TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_NEIGHBORHOOD' )
			|| ! defined( 'TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_LABELS' )
			|| ! defined( 'TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_STATUS' )
			|| ! defined( 'TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_FEATURES' )
			|| ! defined( 'TRX_ADDONS_CPT_AGENTS_PT' )
			|| ! defined( 'TRX_ADDONS_CPT_AGENTS_TAXONOMY' )
		) {
			return $content;
		}
		$args = array(
			'check' => array(
				'class=[\'"][^\'"]*sc_properties',
				'class=[\'"][^\'"]*type\\-(' . TRX_ADDONS_CPT_PROPERTIES_PT . '|' . TRX_ADDONS_CPT_AGENTS_PT . ')',
				'class=[\'"][^\'"]*(' . TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_CITY
								. '|' . TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_COUNTRY
								. '|' . TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_TYPE
								. '|' . TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_STATE
								. '|' . TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_NEIGHBORHOOD
								. '|' . TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_LABELS
								. '|' . TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_STATUS
								. '|' . TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_FEATURES
								. '|' . TRX_ADDONS_CPT_AGENTS_TAXONOMY
								. ')\\-',
			)
		);
		if ( trx_addons_check_in_html_output( 'cpt_properties-google-places', $content, $args ) ) {
			trx_addons_google_places_load_scripts_front( true );
		}
		return $content;
	}
}
