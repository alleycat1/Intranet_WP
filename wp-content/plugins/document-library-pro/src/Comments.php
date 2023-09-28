<?php

namespace Barn2\Plugin\Document_Library_Pro;

use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Registerable,
	Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Service,
	Barn2\Plugin\Document_Library_Pro\Post_Type,
	Barn2\Plugin\Document_Library_Pro\Util\Options;

defined( 'ABSPATH' ) || exit;

/**
 * Filter the comments output to disable it if set
 *
 * @package   Barn2/document-library-pro
 * @author    Barn2 Plugins <info@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Comments implements Registerable, Service {

	private $plugin;

	/**
	 * Constructor.
	 *
	 * @param Plugin $plugin
	 */
	public function __construct( Plugin $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * {@inheritdoc}
	 */
	public function register() {
		add_filter( 'comments_open', [ $this, 'comments_open' ], 10, 2 );
		add_filter( 'comments_array', [ $this, 'hide_comments' ], 10, 2 );
		add_filter( 'comments_template', [ $this, 'comments_template' ], 10, 1 );
	}

	/**
	 * Disables the comments output if our setting is not active
	 *
	 * @param bool $open
	 * @param int $post_id
	 * @return bool $open
	 */
	public function comments_open( $open, $post_id ) {
		if ( get_post_type( $post_id ) !== Post_Type::POST_TYPE_SLUG ) {
			return $open;
		}

		$open = $this->is_comments_active();

		return $open;
	}

	/**
	 * Changes the comments_open() state
	 *
	 * @param array $comments
	 * @param int $post_id
	 * @return array $comments
	 */
	public function hide_comments( $comments, $post_id ) {
		if ( get_post_type( $post_id ) !== Post_Type::POST_TYPE_SLUG ) {
			return $comments;
		}

		if ( ! $this->is_comments_active() ) {
			$comments = [];
		}

		return $comments;
	}

	/**
	 * Outputs a blank comments template if necessary
	 *
	 * @param string $template
	 * @return string $template
	 */
	public function comments_template( $template ) {
		if ( get_post_type() !== Post_Type::POST_TYPE_SLUG ) {
			return $template;
		}

		if ( ! $this->is_comments_active() ) {
			$template = $this->plugin->get_dir_path() . 'templates/comments.php';
		}

		return $template;
	}

	/**
	 * Determines if comments should be active and displayed
	 *
	 * @return boolean
	 */
	private function is_comments_active() {
		return in_array( 'comments', Options::get_document_display_fields(), true ) && in_array( 'comments', Options::get_document_fields(), true );
	}
}
