<?php
/**
 * Widget: Audio player for Local hosted audio and Soundcloud and other embeded audio (Shortcodes)
 *
 * @package ThemeREX Addons
 * @since v1.2
 */


// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


// trx_widget_audio
//-------------------------------------------------------------
/*
[trx_widget_audio id="unique_id" title="Widget title"]
*/
if ( ! function_exists( 'trx_addons_sc_widget_audio' ) ) {
	function trx_addons_sc_widget_audio( $atts, $content = null ) {
		$atts = trx_addons_sc_prepare_atts(
			'trx_widget_audio', $atts, trx_addons_sc_common_atts( 'id', array(
				// Individual params
				'title'        => '',
				'subtitle'     => '',
				'media'        => '',
				'media_from_post' => '0',
				'next_btn'     => '1',
				'prev_btn'     => '1',
				'next_text'    => '',
				'prev_text'    => '',
				'now_text'     => '',
				'track_time'   => '1',
				'track_scroll' => '1',
				'track_volume' => '1',
			) )
		);

		if ( function_exists( 'vc_param_group_parse_atts' ) && ! is_array( $atts['media'] ) ) {
			$atts['media'] = (array) vc_param_group_parse_atts( $atts['media'] );
		}
		$output = '';
		if ( ( is_array( $atts['media'] ) && count( $atts['media'] ) > 0 ) || (int)$atts['media_from_post'] > 0 ) {
			if ( is_array( $atts['media'] ) ) {
				foreach ( $atts['media'] as $k => $v ) {
					if ( ! empty( $v['description'] ) ) {
						$atts['media'][ $k ]['description'] = preg_replace( '/\\[(.*)\\]/', '<b>$1</b>', function_exists( 'vc_value_from_safe' ) ? vc_value_from_safe( $v['description'] ) : $v['description'] );
					}
					if ( ! empty( $v['embed'] ) && function_exists( 'vc_value_from_safe' ) ) {
						$atts['media'][ $k ]['embed'] = trim( vc_value_from_safe( $v['embed'] ) );
					}
					if ( ! empty( $v['cover'] ) ) {
						$atts['media'][ $k ]['cover'] = trx_addons_get_attachment_url( $v['cover'], 'full' );
					}
				}
			}
			extract( $atts );
			$type   = 'trx_addons_widget_audio';
			$output = '';
			global $wp_widget_factory;
			if ( is_object( $wp_widget_factory ) && isset( $wp_widget_factory->widgets, $wp_widget_factory->widgets[ $type ] ) ) {
				$output = '<div' . ( $id ? ' id="' . esc_attr( $id ) . '"' : '' )
								. ' class="widget_area sc_widget_audio'
									. ( trx_addons_exists_vc() ? ' vc_widget_audio wpb_content_element' : '' )
									. ( ! empty( $class ) ? ' ' . esc_attr( $class ) : '' )
									. '"'
								. ( $css ? ' style="' . esc_attr( $css ) . '"' : '' )
							. '>';
				ob_start();
				the_widget( $type, $atts, trx_addons_prepare_widgets_args( $id ? $id . '_widget' : 'widget_audio', 'widget_audio' ) );
				$output .= ob_get_contents();
				ob_end_clean();
				$output .= '</div>';
			}
		}
		return apply_filters( 'trx_addons_sc_output', $output, 'trx_widget_audio', $atts, $content );
	}
}


// Add shortcode [trx_widget_audio]
if ( ! function_exists( 'trx_addons_sc_widget_audio_add_shortcode' ) ) {
	function trx_addons_sc_widget_audio_add_shortcode() {
		add_shortcode( 'trx_widget_audio', 'trx_addons_sc_widget_audio' );
	}
	add_action( 'init', 'trx_addons_sc_widget_audio_add_shortcode', 20 );
}
