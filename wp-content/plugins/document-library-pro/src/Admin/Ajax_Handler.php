<?php
namespace Barn2\Plugin\Document_Library_Pro\Admin;

use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Service,
	Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Registerable,
	Barn2\Plugin\Document_Library_Pro\Admin\Importer\CSV_Controller,
	Barn2\Plugin\Document_Library_Pro\Post_Type,
	Barn2\Plugin\Document_Library_Pro\Util\Util,
	Barn2\Plugin\Document_Library_Pro\Util\Media as Media_Util,
	Barn2\Plugin\Document_Library_Pro\Document;

defined( 'ABSPATH' ) || exit;

/**
 * Handles the AJAX requests for the CSV Importer and DND Importer
 *
 * @package   Barn2\document-library-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Ajax_Handler implements Service, Registerable {

	/**
	 * {@inheritdoc}
	 */
	public function register() {
		// CSV
		add_action( 'wp_ajax_dlp_document_import', [ self::class, 'document_import' ] );

		// DND
		add_action( 'wp_ajax_dlp_upload_files', [ self::class, 'dnd_upload' ] );
		add_action( 'wp_ajax_dlp_dnd_fetch', [ self::class, 'dnd_fetch' ] );
	}

	/**
	 * Ajax callback for importing one batch of documents from a CSV.
	 */
	public static function document_import() {
		global $wpdb;

		check_ajax_referer( 'dlp-csv-import', 'security' );

		if ( ! current_user_can( 'import' ) || ! isset( $_POST['file'] ) ) {
			wp_send_json_error( [ 'message' => __( 'Insufficient privileges to import documents.', 'document-library-pro' ) ] );
		}

		$file   = sanitize_text_field( wp_unslash( $_POST['file'] ) );
		$params = [
			'delimiter' => ! empty( $_POST['delimiter'] ) ? sanitize_text_field( wp_unslash( $_POST['delimiter'] ) ) : ',',
			'start_pos' => isset( $_POST['position'] ) ? absint( $_POST['position'] ) : 0,
			'mapping'   => isset( $_POST['mapping'] ) ? (array) Util::sanitize_array_recursive( wp_unslash( $_POST['mapping'] ) ) : [],
			'lines'     => apply_filters( 'document_library_pro_csv_importer_batch_size', 30 ),
			'parse'     => true,
		];

		// Log failures.
		if ( 0 !== $params['start_pos'] ) {
			$error_log = array_filter( (array) get_user_option( 'document_import_error_log' ) );
		} else {
			$error_log = [];
		}

		$importer         = CSV_Controller::get_importer( $file, $params );
		$results          = $importer->import();
		$percent_complete = $importer->get_percent_complete();
		$error_log        = array_merge( $error_log, $results['failed'], $results['skipped'] );

		update_user_option( get_current_user_id(), 'document_import_error_log', $error_log );

		if ( 100 === $percent_complete ) {
			$wpdb->delete( $wpdb->postmeta, [ 'meta_key' => '_original_id' ] );
			$wpdb->delete(
				$wpdb->posts,
				[
					'post_type'   => Post_Type::POST_TYPE_SLUG,
					'post_status' => 'importing',
				]
			);

			// Clean up orphaned data.
			$wpdb->query(
				"
				DELETE {$wpdb->postmeta}.* FROM {$wpdb->postmeta}
				LEFT JOIN {$wpdb->posts} wp ON wp.ID = {$wpdb->postmeta}.post_id
				WHERE wp.ID IS NULL
			"
			);

			// phpcs:disable WordPress.DB.PreparedSQL.NotPrepared
			$wpdb->query(
				"
				DELETE tr.* FROM {$wpdb->term_relationships} tr
				LEFT JOIN {$wpdb->posts} wp ON wp.ID = tr.object_id
				LEFT JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
				WHERE wp.ID IS NULL
				AND tt.taxonomy IN ( '" . implode( "','", array_map( 'esc_sql', get_object_taxonomies( Post_Type::POST_TYPE_SLUG ) ) ) . "' )
			"
			);
			// phpcs:enable

			// Send success.
			wp_send_json_success(
				[
					'position'   => 'done',
					'percentage' => 100,
					'url'        => add_query_arg( [ '_wpnonce' => wp_create_nonce( 'dlp-document-import' ) ], admin_url( 'admin.php?page=dlp_import_csv&step=done' ) ),
					'imported'   => count( $results['imported'] ),
					'failed'     => count( $results['failed'] ),
					'skipped'    => count( $results['skipped'] ),
				]
			);
		} else {
			wp_send_json_success(
				[
					'position'   => $importer->get_file_position(),
					'percentage' => $percent_complete,
					'imported'   => count( $results['imported'] ),
					'failed'     => count( $results['failed'] ),
					'skipped'    => count( $results['skipped'] ),
				]
			);
		}
	}

	/**
	 * AJAX callback for drag and drop document importer
	 */
	public static function dnd_upload() {
		check_ajax_referer( 'dlp-import-files' );

		try {
			$document = new Document(
				0,
				[
					'author' => get_current_user_id(),
					'name'   => str_replace( '-', ' ', Media_Util::get_filename_without_extension( $_FILES['dlp-upload']['name'] ) ),
				]
			);
		} catch ( \Exception $exception ) {
			printf(
				'<div class="error-div error">%s <strong>%s</strong></div>',
				sprintf(
					'<button type="button" class="dismiss button-link" onclick="jQuery(this).parents(\'div.media-item\').slideUp(200, function(){jQuery(this).remove();});">%s</button>',
					esc_html__( 'Dismiss', 'document-library-pro' )
				),
				sprintf(
				/* translators: %s: Name of the file that failed to upload. */
					esc_html__( '&#8220;%s&#8221; document has failed to be created.', 'document-library-pro' ),
					esc_html( $_FILES['dlp-upload']['name'] )
				)
			);
			exit;
		}

		$attachment_id = media_handle_upload( 'dlp-upload', $document->get_id() );

		if ( is_wp_error( $attachment_id ) ) {
			wp_delete_post( $document->get_id() );

			printf(
				'<div class="error-div error">%s <strong>%s</strong><br />%s</div>',
				sprintf(
					'<button type="button" class="dismiss button-link" onclick="jQuery(this).parents(\'div.media-item\').slideUp(200, function(){jQuery(this).remove();});">%s</button>',
					esc_html__( 'Dismiss', 'document-library-pro' )
				),
				sprintf(
				/* translators: %s: Name of the file that failed to upload. */
					esc_html__( '&#8220;%s&#8221; has failed to upload.', 'document-library-pro' ),
					esc_html( $_FILES['dlp-upload']['name'] )
				),
				esc_html( $attachment_id->get_error_message() )
			);
			exit;
		}

		$document->set_document_link( 'file', [ 'file_id' => $attachment_id ] );

		/** This filter is documented in /src/Document.php#L62-70 */
		$use_file_as_featured_image = apply_filters( 'document_library_pro_use_file_as_featured_image', true, $document, $attachment_id );

		if ( wp_attachment_is_image( $attachment_id ) && $use_file_as_featured_image ) {
			set_post_thumbnail( $document->get_id(), $attachment_id );
		}

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $document->get_id();
		exit;
	}

	/**
	 * Ajax callback to fetch drag and drop upload results via jQuery.load
	 */
	public static function dnd_fetch() {
		if ( isset( $_REQUEST['document_id'] ) && intval( $_REQUEST['document_id'] ) ) {
			$id = intval( $_REQUEST['document_id'] );

			try {
				$document = new Document( $id );

				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $document->get_file_icon();

				if ( current_user_can( 'edit_post', $id ) ) {
					echo '<a class="edit-attachment" href="' . esc_url( get_edit_post_link( $id ) ) . '" target="_blank">' . esc_html_x( 'Edit', 'media item', 'document-library-pro' ) . '</a>';
				} else {
					echo '<span class="edit-attachment">' . esc_html_x( 'Success', 'media item', 'document-library-pro' ) . '</span>';
				}

				echo '<div class="filename new"><span class="title">' . esc_html( wp_html_excerpt( $document->get_file_name(), 60, '&hellip;' ) ) . '</span></div>';
			} catch ( \Exception $exception ) {
				wp_die( esc_html( $exception->getMessage() ) );
			}
		}
		exit;
	}
}
