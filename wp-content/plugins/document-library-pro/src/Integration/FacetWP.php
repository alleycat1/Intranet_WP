<?php
namespace Barn2\Plugin\Document_Library_Pro\Integration;

use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Registerable;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Service;
use Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro\Table_Args;

defined( 'ABSPATH' ) || exit;

/**
 * Handles integration with FacetWP
 *
 * @package   Barn2/document-library-pro
 * @author    Barn2 Plugins <info@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class FacetWP implements Registerable, Service {

	/**
	 * {@inheritdoc}
	 */
	public function register() {
		if ( ! class_exists( 'FacetWP' ) ) {
			return;
		}

		add_filter( 'facetwp_template_html', [ $this, 'render_document_library' ], 10, 2 );
	}

	/**
	 * Load the document library if template begins with 'dlp_'.
	 *
	 * @param string $output
	 * @param FacetWP_Renderer $renderer
	 * @return string
	 */
	public function render_document_library( $output, $renderer ) {
		// Check if template name starts with dlp_.
		if ( substr( $renderer->template['name'], 0, 4 ) !== 'dlp_' ) {
			return $output;
		}
		
		$output      = '';
		$layout      = Table_Args::get_site_defaults()['layout'];
		$custom_args = apply_filters( 'document_library_pro_facetwp_custom_args', [], $renderer->template['name'], $renderer );

		if ( isset( $custom_args['layout'] ) ) {
			$layout = $custom_args['layout'];
		}

		// Add posts table init script if table layout.
		if ( $layout === 'table' ) {
			$output .= $this->get_posts_table_script();
		}

		// We need to reset the post__in because get_filtered_post_ids changes it
		// and facet filters won't work with OR conditions.
		$backup_post_in = $renderer->query_args['post__in'];
		$renderer->query_args['post__in'] = [];

		// Check facetwp version to determine which version of `get_filtered_post_ids` to use.
		$filtered_post_ids = version_compare( FACETWP_VERSION, '4.0.6', '>=' ) ? $renderer->get_filtered_post_ids( $renderer->query_args ) : $renderer->get_filtered_post_ids();

		// Restore post__in backup.
		$renderer->query_args['post__in'] = $backup_post_in;

		if ( $renderer->query->found_posts < 1 ) {
			$output .= $this->get_no_documents_message( $custom_args );
		} else {
			$output .= dlp_get_doc_library(
				array_merge(
					$custom_args,
					[
						'folders'         => false,
						'filters'         => false,
						'search_box'      => false,
						'reset_button'    => false,
						'cache'           => false,
						'search_on_click' => false,
						'rows_per_page'   => $renderer->query_args['posts_per_page'],
						'sort_by'         => 'post__in',
						'include'         => implode( ',', $filtered_post_ids )
					]
				)
			);
		}

		return $output;
	}

	/**
	 * Get the no documents message.
	 *
	 * @param array $custom_args
	 * @return string
	 */
	private function get_no_documents_message( $custom_args ) {
		if ( isset( $custom_args['no_posts_filtered_message'] ) && ! empty( $custom_args['no_posts_filtered_message'] ) ) {
			return $custom_args['no_posts_filtered_message'];
		}

		$language_strings = apply_filters( 'document_library_pro_language_defaults', [ 'zeroRecords'  => __( 'No matching _POSTS_.', 'document-library-pro' ) ] );

		return $language_strings['zeroRecords'];
	}

	/**
	 * Generate script tag to re-init posts table after facet loading.
	 *
	 * @return string
	 */
	private function get_posts_table_script() {
		$script = "<script>
        ( function( $ ) {
            document.addEventListener( 'facetwp-loaded', function() {
                $( '.facetwp-template table' ).first().postsTable();
             } );
        } )( jQuery );
        </script>";

		return $script;
	}
}
