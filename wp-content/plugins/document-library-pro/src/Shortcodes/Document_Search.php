<?php

namespace Barn2\Plugin\Document_Library_Pro\Shortcodes;

use Barn2\Plugin\Document_Library_Pro\Search_Handler,
	Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Registerable,
	Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Service,
	Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Conditional,
	Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Util as Lib_Util;

defined( 'ABSPATH' ) || exit;

/**
 * This class handles the global doc search shortcode.
 *
 * @package   Barn2\document-library-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Document_Search implements Service, Registerable, Conditional {

	const SHORTCODE = 'doc_search';

	/**
	 * {@inheritdoc}
	 */
	public function is_required() {
		return Lib_Util::is_front_end();
	}

	/**
	 * {@inheritdoc}
	 */
	public function register() {
		add_shortcode( self::SHORTCODE, [ self::class, 'do_shortcode' ] );
	}


	/**
	 * Render the shortcode
	 *
	 * @param array $atts
	 * @param string $content
	 * @return string
	 */
	public static function do_shortcode( $atts, $content = '' ) {

		wp_enqueue_style( 'dlp-search-box' );

		$placeholder = isset( $atts['placeholder'] ) && ! empty( $atts['placeholder'] ) ? $atts['placeholder'] : esc_html__( 'Search documents...', 'document-library-pro' );
		$button_text = isset( $atts['button_text'] ) && ! empty( $atts['button_text'] ) ? $atts['button_text'] : esc_html__( 'Search', 'document-library-pro' );

		return Search_Handler::get_search_box_html( 'shortcode', $placeholder, $button_text );
	}

}
