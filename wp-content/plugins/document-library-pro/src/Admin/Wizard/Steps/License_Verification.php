<?php

namespace Barn2\Plugin\Document_Library_Pro\Admin\Wizard\Steps;

use Barn2\Plugin\Document_Library_Pro\Dependencies\Setup_Wizard\Steps\Welcome;

/**
 * Welcome / License Step.
 *
 * @package   Barn2/document-library-pro
 * @author    Barn2 Plugins <info@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class License_Verification extends Welcome {

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct();
		$this->set_title( esc_html__( 'Welcome to Document Library Pro', 'document-library-pro' ) );
		$this->set_name( esc_html__( 'Welcome', 'document-library-pro' ) );
		$this->set_description( esc_html__( 'Start displaying documents in no time.', 'document-library-pro' ) );
		$this->set_tooltip( esc_html__( 'Use this setup wizard to quickly configure the most popular options for your document libraries. You can change these options later on the plugin settings page or by relaunching the setup wizard. You can also override these options in the shortcode for individual libraries.', 'document-library-pro' ) );
	}
}
