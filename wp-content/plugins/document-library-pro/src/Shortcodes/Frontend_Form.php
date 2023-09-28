<?php

namespace Barn2\Plugin\Document_Library_Pro\Shortcodes;

use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Registerable,
	Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Service,
	Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Conditional,
	Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Util as Lib_Util;

use function Barn2\Plugin\Document_Library_Pro\document_library_pro;

/**
 * This class handles the display of the frontend submission form.
 *
 * @package   Barn2\document-library-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Frontend_Form implements Service, Registerable, Conditional {

	const SHORTCODE = 'dlp_submission_form';

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
		wp_enqueue_script( 'dlp-frontend-submission' );
		wp_enqueue_style( 'dlp-frontend-submission' );

		return document_library_pro()->get_service( 'submission_form' )->display_form();
	}

}
