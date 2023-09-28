<?php
namespace Barn2\Plugin\Document_Library_Pro;

use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Template_Loader;

/**
 * Template loader for vanilla WordPress.
 *
 * @package   Barn2\document-library-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Templates implements Template_Loader {

	private $template_path;
	private $default_path;

	/**
	 * Constructor.
	 *
	 * @param string $default_path
	 */
	public function __construct( $default_path = '' ) {
		$this->template_path = 'dlp_templates/';
		$this->default_path  = $default_path ? trailingslashit( $default_path ) : '';
	}

	/**
	 * Retrieves a template.
	 *
	 * @param string $template_name
	 * @param array $args
	 * @return string
	 */
	public function get_template( $template_name, array $args = [] ) {
		ob_start();
		$this->load_template( $template_name, $args );
		return ob_get_clean();
	}

	/**
	 * Load a template.
	 *
	 * @param string|null $template_name
	 * @param array $args
	 */
	public function load_template( $template_name = null, array $args = [] ) {
		if ( is_null( $template_name ) ) {
			return;
		}

		$templates     = [];
		$template_name = $this->expand_template( $template_name );
		$templates[]   = "{$template_name}.php";

		$template = locate_template(
			[
				trailingslashit( $this->get_template_path() ) . $template_name,
				$template_name
			],
			false
		);

		if ( ! $template ) {
			$template = $this->get_default_path() . $template_name;
		}

		if ( ! $template ) {
			return;
		}

		if ( $args ) {
			extract( $args ); // phpcs:ignore WordPress.PHP.DontExtract.extract_extract
		}

		include $template;
	}

	/**
	 * Load a template.
	 *
	 * @param string|null $template_name
	 * @param array $args
	 */
	public function load_template_once( $template_name = null, array $args = [] ) {
		if ( is_null( $template_name ) ) {
			return;
		}

		$templates     = [];
		$template_name = $this->expand_template( $template_name );
		$templates[]   = "{$template_name}.php";

		$template = locate_template(
			[
				trailingslashit( $this->get_template_path() ) . $template_name,
				$template_name
			],
			false
		);

		if ( ! $template ) {
			$template = $this->get_default_path() . $template_name;
		}

		if ( ! $template ) {
			return;
		}

		if ( $args ) {
			extract( $args ); // phpcs:ignore WordPress.PHP.DontExtract.extract_extract
		}

		include $template;
	}

	/**
	 * Get the template path (for themes).
	 *
	 * @return string
	 */
	public function get_template_path() {
		return $this->template_path;
	}

	/**
	 * Get the default path (in the plugin).
	 *
	 * @return string
	 */
	public function get_default_path() {
		return $this->default_path;
	}

	/**
	 * Expands the template if necessary.
	 *
	 * @param string $template_name
	 * @return string
	 */
	private function expand_template( $template_name ) {
		/*
		 * If the template ends with a folder rather than a PHP file, we expand the template name using the
		 * terminating folder to build the full template name.
		 * E.g. /my-templates/cool/ becomes /my-templates/cool/cool.php
		 */
		if ( '.php' !== substr( $template_name, -4 ) ) {
			$template_name  = rtrim( $template_name, '/ ' );
			$last_backslash = strrpos( $template_name, '/' );

			if ( false !== $last_backslash ) {
				$last_folder   = substr( $template_name, $last_backslash + 1 );
				$template_name = "{$template_name}/{$last_folder}.php";
			} else {
				$template_name = "{$template_name}/{$template_name}.php";
			}
		}

		return $template_name;
	}

}
