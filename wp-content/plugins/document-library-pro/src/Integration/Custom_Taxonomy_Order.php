<?php
namespace Barn2\Plugin\Document_Library_Pro\Integration;

use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Registerable,
	Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Service;

defined( 'ABSPATH' ) || exit;

/**
 * Handles integration with Custom Taxonomy Order
 *
 * @package   Barn2/document-library-pro
 * @author    Barn2 Plugins <info@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Custom_Taxonomy_Order implements Registerable, Service {

	/**
	 * {@inheritdoc}
	 */
	public function register() {
		if ( ! defined( 'CUSTOMTAXORDER_VER' ) ) {
			return;
		}

		add_filter( 'document_library_pro_folder_orderby', [ $this, 'set_folder_orderby' ], 10, 1 );
	}

	/**
	 * Set the folder orderby parameter
	 *
	 * @param string $order
	 * @return string
	 */
	public function set_folder_orderby( $order ) {
		return 'term_order';
	}
}
