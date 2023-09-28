<?php

namespace Barn2\Plugin\Document_Library_Pro;

use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Template_Loader;

/**
 * Factory to return the template loader instance
 *
 * @package   Barn2\document-library-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Template_Loader_Factory {

	private static $template_loader = null;

	/**
	 * Get the shared template loader instance.
	 *
	 * @return Template_Loader The template loader.
	 */
	public static function create() {
		if ( null === self::$template_loader ) {
			self::$template_loader = new Templates( document_library_pro()->get_dir_path() . 'templates/' );
		}

		return self::$template_loader;
	}

}
