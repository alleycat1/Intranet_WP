<?php
/**
 * Plugin support: The Events Calendar (Shortcodes)
 *
 * @package ThemeREX Addons
 * @since v1.2
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


/*
[trx_sc_events id="unique_id" type="default" cat="category_slug or id" count="3" columns="3" slider="0|1"]
*/
if ( ! function_exists( 'trx_addons_sc_events' ) ) {
	/*
	 * Shortcode [trx_sc_events]
	 * 
	 * @trigger trx_addons_sc_output 
	 * 
	 * @param array $atts      Shortcode attributes
	 * @param string $content  Shortcode content
	 * 
	 * @return string          Shortcode output
	 */
	function trx_addons_sc_events( $atts, $content = null ) {	
		$atts = trx_addons_sc_prepare_atts( 'trx_sc_events', $atts, trx_addons_sc_common_atts( 'id,title,slider,query', array(
			// Individual params
			"type" => "default",
			"past" => "0",
			"more_text" => esc_html__('More info', 'trx_addons'),
		) ) );

		if ( ! empty( $atts['ids'] ) ) {
			if ( is_array( $atts['ids'] ) ) {
				$atts['ids'] = join(',', $atts['ids']);
			}
			$atts['ids'] = str_replace( array(';', ' '), array(',', ''), $atts['ids'] );
			$ids_count = count( explode( ',', $atts['ids'] ) );
			if ( empty( $atts['count'] ) || $atts['count'] >= $ids_count || empty( $atts['pagination'] ) || trx_addons_is_off( $atts['pagination'] ) ) {
				$atts['count'] = $ids_count;
			}
		}
		$atts['count'] = max(1, (int) $atts['count']);
		$atts['offset'] = max(0, (int) $atts['offset']);
		if ( empty( $atts['orderby'] ) || in_array( $atts['orderby'], array( 'date', 'post_date' ) ) ) {
			$atts['orderby'] = 'event_date';
		}
		if ( empty( $atts['order'] ) ) {
			$atts['order'] = 'asc';
		}
		$atts['slider'] = max( 0, (int)$atts['slider'] );
		if ( $atts['slider'] > 0 && (int)$atts['slider_pagination'] > 0 ) {
			$atts['slider_pagination'] = 'bottom';
		}

		add_filter( "excerpt_length", "trx_addons_sc_events_excerpt_length", 99 );

		// Load specific scripts and styles
		trx_addons_tribe_events_load_scripts_front( true );

		// Load template
		ob_start();
		trx_addons_get_template_part( array(
										TRX_ADDONS_PLUGIN_API . 'the-events-calendar/tpl.'.trx_addons_esc($atts['type']).'.php',
										TRX_ADDONS_PLUGIN_API . 'the-events-calendar/tpl.default.php'
										),
									'trx_addons_args_sc_events',
									$atts
									);
		$output = ob_get_contents();
		ob_end_clean();

		remove_filter( "excerpt_length", "trx_addons_sc_events_excerpt_length", 99 );
		
		return apply_filters('trx_addons_sc_output', $output, 'trx_sc_events', $atts, $content);
	}
}

if ( ! function_exists( 'trx_addons_sc_events_add_shortcode' ) ) {
	add_action( 'init', 'trx_addons_sc_events_add_shortcode', 20 );
	/*
	 * Add/Register shortcode [trx_sc_events]
	 * 
	 * @hooked init, 20
	 */
	function trx_addons_sc_events_add_shortcode() {
		if ( ! trx_addons_exists_tribe_events() ) {
			return;
		}
		add_shortcode( "trx_sc_events", "trx_addons_sc_events" );
	}
}

if ( ! function_exists('trx_addons_sc_events_excerpt_length' ) ) {
	// Handler of the add_filter( "excerpt_length", "trx_addons_sc_events_excerpt_length", 99 );
	/*
	 * Set excerpt length for the events
	 * 
	 * @hooked excerpt_length, 99
	 * 
	 * @trigger trx_addons_filter_sc_events_excerpt_length
	 * 
	 * @param int $length  Excerpt length
	 * 
	 * @return int         New excerpt length
	 */
	function trx_addons_sc_events_excerpt_length( $length = 0 ) {
		return apply_filters( 'trx_addons_filter_sc_events_excerpt_length', 30 );
	}
}
