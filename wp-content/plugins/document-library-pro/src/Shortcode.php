<?php

namespace Barn2\Plugin\Document_Library_Pro;

use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Registerable,
	Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Service,
	Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Conditional,
	Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Util as Lib_Util,
	Barn2\Plugin\Document_Library_Pro\Util\Options;

defined( 'ABSPATH' ) || exit;

/**
 * This class handles the doc library shortcode.
 *
 * @package   Barn2\document-library-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Shortcode implements Service, Registerable, Conditional {

	const SHORTCODE = 'doc_library';

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
	 * Handles our document library shortcode.
	 *
	 * @param array $atts The attributes passed in to the shortcode
	 * @param string $content The content passed to the shortcode (not used)
	 * @return string The shortcode output
	 */
	public static function do_shortcode( $atts, $content = '' ) {
		if ( ! self::can_do_shortcode() ) {
			return '';
		}

		// Return the table as HTML
		return apply_filters( 'document_library_pro_shortcode_output', dlp_get_doc_library( $atts ) );
	}

	/**
	 * Determin if shortcode can be output.
	 *
	 * @return bool
	 */
	private static function can_do_shortcode() {
		// Don't run in the search results page.
		if ( is_search() && in_the_loop() && ! apply_filters( 'document_library_pro_run_in_search', false ) ) {
			return false;
		}

		return true;
	}
}
