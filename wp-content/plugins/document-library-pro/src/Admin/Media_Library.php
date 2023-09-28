<?php

namespace Barn2\Plugin\Document_Library_Pro\Admin;

use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Admin\Notices,
	Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Registerable,
	Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Service,
	Barn2\Plugin\Document_Library_Pro\Document,
	Barn2\Plugin\Document_Library_Pro\Post_Type,
	Barn2\Plugin\Document_Library_Pro\Taxonomies,
	Barn2\Plugin\Document_Library_Pro\Util\Media as Media_Util;

defined( 'ABSPATH' ) || exit;

/**
 * Handles the Media Library bulk action
 *
 * @package   Barn2/document-library-pro
 * @author    Barn2 Plugins <info@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Media_Library implements Registerable, Service {

	/**
	 * {@inheritdoc}
	 */
	public function register() {
		// List View - Document Download Filter
		add_action( 'restrict_manage_posts', [ $this, 'add_list_view_document_download_dropdown' ], 10, 1 );

		// List View Bulk Action - Create Documents
		add_filter( 'bulk_actions-upload', [ $this, 'add_bulk_action' ], 10, 1 );
		add_filter( 'handle_bulk_actions-upload', [ $this, 'handle_bulk_action' ], 10, 3 );
		add_action( 'load-upload.php', [ $this, 'admin_notices' ] );
	}

	/**
	 * Adds the Document Download filter dropdown to the Media Library list view.
	 *
	 * @param string $post_type
	 */
	public function add_list_view_document_download_dropdown( $post_type ) {
		if ( $post_type !== 'attachment' ) {
			return;
		}

		$document_download = filter_input( INPUT_GET, Taxonomies::DOCUMENT_DOWNLOAD_SLUG, FILTER_SANITIZE_FULL_SPECIAL_CHARS );

		?>
		<select name="<?php echo esc_attr( Taxonomies::DOCUMENT_DOWNLOAD_SLUG ); ?>" id="filter-by-dlp">
			<option<?php selected( $document_download, '' ); ?> value=""><?php esc_html_e( 'All types', 'document-library-pro' ); ?></option>
			<option<?php selected( $document_download, 'document-download' ); ?> value="document-download"><?php esc_html_e( 'Documents', 'document-library-pro' ); ?></option>
		</select>
		<?php
	}

	/**
	 * Add the bulk action for importing
	 *
	 * @param array $bulk_actions
	 * @return array $bulk_actions
	 */
	public function add_bulk_action( $bulk_actions ) {
		$bulk_actions['dlp_create_documents'] = __( 'Add to document library', 'document-library-pro' );

		return $bulk_actions;
	}

	/**
	 * Handle the importing for the bulk action
	 *
	 * @param string $redirect
	 * @param string $action
	 * @param array $object_ids
	 * @return string $redirect
	 */
	public function handle_bulk_action( $redirect, $action, $object_ids ) {
		if ( $action !== 'dlp_create_documents' ) {
			return $redirect;
		}

		if ( empty( $object_ids ) || ! is_array( $object_ids ) ) {
			return $redirect;
		}

		$documents = [];
		$successes = 0;
		$failures  = 0;

		foreach ( $object_ids as $index => $attachment_id ) {
			try {
				$documents[ $index ] = new Document(
					0,
					[
						'author' => get_current_user_id(),
						'name'   => str_replace( '-', ' ', Media_Util::get_filename_without_extension( basename( get_attached_file( $attachment_id ) ) ) ),
					]
				);

				$documents[ $index ]->set_document_link( 'file', [ 'file_id' => $attachment_id ] );

				$successes++;
			} catch ( \Exception $e ) {
				$failures++;
			}
		}

		if ( $successes > 0 ) {
			$redirect = add_query_arg(
				[
					'dlp_bulk_convert_success' => 'true',
					'successes'                => $successes,
				],
				$redirect
			);
		}

		if ( $failures > 0 ) {
			$redirect = add_query_arg(
				[
					'dlp_bulk_convert_fail' => 'true',
					'failures'              => $failures
				],
				$redirect
			);
		}

		return $redirect;
	}

	/**
	 * Add admin notices for the outcome of the bulk import
	 */
	public function admin_notices() {
		// success
		if ( isset( $_REQUEST['dlp_bulk_convert_success'] ) && $_REQUEST['dlp_bulk_convert_success'] === 'true' ) {
			$admin_notice = new Notices();
			$admin_notice->add(
				'dlp_bulk_convert_success',
				'',
				sprintf(
					/* translators: %d: Created documents count */
					_n( '%d document successfully created.', '%d documents successfully created.', absint( $_REQUEST['successes'] ), 'document-library-pro' ) .
					/* translators: %s: Documents wp-admin list url */
					__( ' <a href="%s">View documents</a>', 'document-library-pro' ),
					absint( $_REQUEST['successes'] ),
					esc_url( admin_url( 'edit.php?post_type=' . Post_Type::POST_TYPE_SLUG ) )
				),
				[
					'type'       => 'success',
					'capability' => 'upload_files',
				]
			);
			$admin_notice->boot();
		}

		// failure
		if ( isset( $_REQUEST['dlp_bulk_convert_fail'] ) && $_REQUEST['dlp_bulk_convert_fail'] === 'true' ) {
			$admin_notice = new Notices();
			$admin_notice->add(
				'dlp_bulk_convert_fail',
				'',
				sprintf(
					/* translators: %d: Failed documents count */
					_n( '%d document could not be created.', '%d documents could not be created.', absint( $_REQUEST['failures'] ), 'document-library-pro' ),
					absint( $_REQUEST['failures'] )
				),
				[
					'type'       => 'error',
					'capability' => 'upload_files',
				]
			);
			$admin_notice->boot();
		}
	}
}
