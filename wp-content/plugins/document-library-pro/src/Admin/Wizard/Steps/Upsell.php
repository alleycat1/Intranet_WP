<?php

namespace Barn2\Plugin\Document_Library_Pro\Admin\Wizard\Steps;

use Barn2\Plugin\Document_Library_Pro\Dependencies\Setup_Wizard\Steps\Cross_Selling;

/**
 * Upsell Step.
 *
 * @package   Barn2/document-library-pro
 * @author    Barn2 Plugins <info@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Upsell extends Cross_Selling {
	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct();
		$this->set_name( esc_html__( 'More', 'document-library-pro' ) );
		$this->set_description( __( 'Enhance your store with these fantastic plugins from Barn2.', 'document-library-pro' ) );
		$this->set_title( esc_html__( 'Extra features', 'document-library-pro' ) );
	}
}
