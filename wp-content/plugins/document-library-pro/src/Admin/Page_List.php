<?php

namespace Barn2\Plugin\Document_Library_Pro\Admin;

use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Registerable,
	Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Service,
	Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Conditional,
	Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Util;

defined( 'ABSPATH' ) || exit;

/**
 * Handles functionality on the Pages list table screen
 *
 * @package   Barn2/document-library-pro
 * @author    Barn2 Plugins <info@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Page_List implements Registerable, Service, Conditional {

	/**
	 * {@inheritdoc}
	 */
	public function is_required() {
		return Util::is_admin();
	}

	/**
	 * {@inheritdoc}
	 */
	public function register() {
		add_filter( 'display_post_states', [ $this, 'display_post_states' ], 10, 2 );
	}

	/**
	 * Add a post display state for the document library page
	 *
	 * @param array     $post_states An array of post display states.
	 * @param \WP_Post  $post        The current post object.
	 */
	public function display_post_states( $post_states, $post ) {
		if ( $this->get_page_id( 'document_page' ) === $post->ID ) {
			$post_states['dlp_document_library_page'] = __( 'Document Library Page', 'document-library-pro' );
		}

		if ( $this->get_page_id( 'search_page' ) === $post->ID ) {
			$post_states['dlp_document_search_page'] = __( 'Document Search Results Page', 'document-library-pro' );
		}

		return $post_states;
	}

	/**
	 * Returns the document page ID
	 *
	 * @param string $page_key
	 * @return int $page_id
	 */
	private function get_page_id( $page_key ) {
		$page_id = get_option( "dlp_$page_key" );

		return $page_id ? absint( $page_id ) : -1;
	}

}
