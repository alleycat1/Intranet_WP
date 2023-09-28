<?php
/**
 * Template functions for Document Library Pro
 *
 * @package   Barn2\document-library-pro
 * @author    Barn2 Plugins <info@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */

use Barn2\Plugin\Document_Library_Pro\Document,
	Barn2\Plugin\Document_Library_Pro\Grid\Grid_Factory,
	Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro\Table_Factory,
	Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro\Table_Args,
	Barn2\Plugin\Document_Library_Pro\Folder_Tree,
	Barn2\Plugin\Document_Library_Pro\Util\Options;

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'dlp_get_doc_library' ) ) {

	/**
	 * Retrieves a post table for the specified args. The arg names are the same as those used in the shortcode and Table_Args,
	 * as well as the addtional DLP args.
	 *
	 * @see The options documentation or See Table_Args::$default_args for the list of supported args.
	 * @param array $args The table args.
	 * @return string The data table as a HTML string.
	 */
	function dlp_get_doc_library( $args = [] ) {
		// Force our document post type
		if ( ! $args ) {
			$args = [];
		}

		$args['post_type'] = 'dlp_document';

		// Normalize with PTP table values
		$args = Options::normalize_user_arguments( $args );

		/**
		 * The final args used to build the document library (includes defaults).
		 *
		 * @param array $filled_args
		 */
		$filled_args = apply_filters( 'document_library_pro_filled_args', array_merge( Table_Args::get_site_defaults(), $args ) );

		if ( $filled_args['folders'] === true ) {
			// Create and return the folders as HTML
			$folder_tree = new Folder_Tree( $filled_args, $args );
			return $folder_tree->get_html();
		}

		if ( $filled_args['layout'] === 'table' ) {
			// Create and return the table as HTML
			$table = Table_Factory::create( $filled_args );

			return $table->get_table( 'html' );
		}

		if ( $filled_args['layout'] === 'grid' ) {
			if ( $filled_args['sort_by'] === 'custom' ) {
				$filled_args['sort_by'] = 'data';
			}

			// Create and return the grid as HTML
			$grid = Grid_Factory::create( $filled_args );

			return $grid->get_grid();
		}
	}
}

if ( ! function_exists( 'dlp_the_doc_library' ) ) {

	/**
	 * Outputs a post table for the specified args. The arg names are the same as those used in the shortcode and Table_Args,
	 * as well as the addtional DLP args.
	 *
	 * @see The options documentation or See Table_Args::$default_args for the list of supported args.
	 * @param array $args The table args.
	 */
	function dlp_the_doc_library( $args = [] ) {
		echo dlp_get_doc_library( $args ); // phpcs:ignore
	}
}


if ( ! function_exists( 'dlp_get_document' ) ) {

	/**
	 * Get a document
	 *
	 * @param   int                 $id Document ID
	 * @return  Document|false
	 */
	function dlp_get_document( $id ) {
		try {
			$document = new Document( $id );

			return $document;
		} catch ( Exception $e ) {
			return false;
		}
	}
}
