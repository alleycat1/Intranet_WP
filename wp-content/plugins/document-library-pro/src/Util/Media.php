<?php
namespace Barn2\Plugin\Document_Library_Pro\Util;

defined( 'ABSPATH' ) || exit;

/**
 * Media Library Utilities
 *
 * @package   Barn2/document-library-pro
 * @author    Barn2 Plugins <info@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
final class Media {

	/**
	 * Removes extension from a filename
	 *
	 * @param string $file_name
	 * @return string
	 */
	public static function get_filename_without_extension( $file_name ) {
		return substr( $file_name, 0, strrpos( $file_name, '.' ) );
	}

	/**
	 * Downloads a file from a URL and attaches it to the document
	 *
	 * @param  string   $url        Attachment URL.
	 * @param  int      $document_id Document ID
	 * @return int
	 * @throws \Exception If attachment cannot be loaded.
	 */
	public static function attach_file_from_url( $url, $document_id ) {
		if ( empty( $url ) ) {
			return 0;
		}

		$file_id    = 0;
		$upload_dir = wp_upload_dir( null, false );
		$base_url   = $upload_dir['baseurl'] . '/';

		// Check if the file could already be in the Media Library
		if ( strpos( $url, $base_url ) === 0 ) {
			$file_id = self::get_attachment_id_from_url( $url );
		}

		if ( ! $file_id ) {
			// This is an external URL or not in the media library, so compare to source.
			$args = [
				'post_type'   => 'attachment',
				'post_status' => 'any',
				'fields'      => 'ids',
				'meta_query'  => [
					[
						'value' => $url,
						'key'   => '_dlp_attachment_source',
					],
				],
			];

			$file_ids = get_posts( $args );

			if ( $file_ids ) {
				$file_id = current( $file_ids );
			}
		}

		// Upload if attachment does not exists.
		if ( ! $file_id && stristr( $url, '://' ) ) {
			$upload = self::upload_document_from_url( $url );

			if ( is_wp_error( $upload ) ) {
				throw new \Exception( $upload->get_error_message(), 400 );
			}

			$file_id = self::set_uploaded_document_as_attachment( $upload, $document_id );

			// Save attachment source for future reference.
			update_post_meta( $file_id, '_dlp_attachment_source', $url );
		}

		if ( ! $file_id ) {
			/* translators: %s: document URL */
			throw new \Exception( sprintf( __( 'Unable to use document "%s".', 'document-library-pro' ), $url ), 400 );
		}

		return $file_id;
	}

	/**
	 * Upload document from URL.
	 *
	 * @param   string              $document_url File URL.
	 * @return  array|\WP_Error     Attachment data or error message.
	 */
	private static function upload_document_from_url( $document_url ) {
		$parsed_url = wp_parse_url( $document_url );

		// Check parsed URL.
		if ( ! $parsed_url || ! is_array( $parsed_url ) ) {
			return new \WP_Error(
				'document_library_import_invalid_url',
				/* translators: %s: image URL */
				sprintf( __( 'Invalid URL %s.', 'document-library-pro' ), $document_url ),
				[ 'status' => 400 ]
			);
		}

		// Ensure url and filename is valid.
		$document_url   = esc_url_raw( $document_url );
		$safe_file_name = sanitize_file_name( urldecode( basename( current( explode( '?', $document_url ) ) ) ) );

		// download_url function is part of wp-admin.
		if ( ! function_exists( 'download_url' ) ) {
			include_once ABSPATH . 'wp-admin/includes/file.php';
		}

		// maybe check dropbox links
		$document_url = self::maybe_sanitize_dropbox_link( $document_url );

		$file_array         = [];
		$file_array['name'] = $safe_file_name;

		// Download file to temp location.
		$file_array['tmp_name'] = download_url( $document_url );

		// If error storing temporarily, return the error.
		if ( is_wp_error( $file_array['tmp_name'] ) ) {
			return new \WP_Error(
				'document_library_import_invalid_remote_url',
				/* translators: %s: image URL */
				sprintf( __( 'Error getting remote document %s.', 'document-library-pro' ), $document_url ) . ' '
				/* translators: %s: error message */
				. sprintf( __( 'Error: %s', 'document-library-pro' ), $file_array['tmp_name']->get_error_message() ),
				[ 'status' => 400 ]
			);
		}

		// Do the validation and storage stuff.
		$file = wp_handle_sideload(
			$file_array,
			[
				'test_form' => false,
				'mimes'     => wp_get_mime_types(),
			],
			current_time( 'Y/m' )
		);

		if ( isset( $file['error'] ) ) {
			// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
			@unlink( $file_array['tmp_name'] );

			return new \WP_Error(
				'document_library_pro_import_invalid_document',
				sprintf(
					/* translators: %s: error message */
					__( 'Invalid document: %s', 'document-library-pro' ),
					$file['error']
				),
				[ 'status' => 400 ]
			);
		}

		return $file;
	}

	/**
	 * Set uploaded document as attachment.
	 *
	 * @param   array   $upload Upload information from wp_upload_bits.
	 * @param   int     $id Post ID. Default to 0.
	 * @return  int     Attachment ID
	 */
	private static function set_uploaded_document_as_attachment( $upload, $id = 0 ) {
		$info = wp_check_filetype( $upload['file'] );

		$attachment = [
			'post_mime_type' => $info['type'],
			'guid'           => $upload['url'],
			'post_parent'    => $id,
			'post_title'     => basename( $upload['file'] ),
			'post_content'   => '',
		];

		$attachment_id = wp_insert_attachment( $attachment, $upload['file'], $id );
		if ( ! is_wp_error( $attachment_id ) ) {
			wp_update_attachment_metadata( $attachment_id, wp_generate_attachment_metadata( $attachment_id, $upload['file'] ) );
		}

		return $attachment_id;
	}

	/**
	 * Retrieves an attachment object based on a URL
	 *
	 * @param string $attachment_url
	 * @return mixed
	 */
	private static function get_attachment_id_from_url( $attachment_url ) {
		global $wpdb;

		$attachment = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE guid=%s;", $attachment_url ) );

		if ( ! $attachment ) {
			return null;
		}

		return $attachment[0];
	}

	/**
	 * Cleans dropbox preview URLs for downloading.
	 *
	 * @param string $document_url
	 * @return string
	 */
	private static function maybe_sanitize_dropbox_link( $document_url ) {
		// check if potentially dropbox url
		if ( stripos( $document_url, 'dropbox' ) === false ) {
			return $document_url;
		}

		// Check if we have a dropbox direct url
		$direct_urls = [
			'//dl.dropbox.com/',
			'//dl.dropboxusercontent.com/'
		];

		$is_dropbox_direct_link = array_filter(
			$direct_urls,
			function ( $domain ) use ( $document_url ) {
				return stripos( $document_url, $domain ) !== false;
			}
		);

		if ( count( $is_dropbox_direct_link ) > 0 ) {
			return $document_url;
		}

		// Check if we have a dropbox normal url
		$dropbox_urls = [
			'//dropbox.com/',
			'//www.dropbox.com/',
		];

		$is_dropbox = array_filter(
			$dropbox_urls,
			function ( $domain ) use ( $document_url ) {
				return stripos( $document_url, $domain ) !== false;
			}
		);

		if ( count( $is_dropbox ) === 0 ) {
			return $document_url;
		}

		// swap ?dl=0 for ?dl=1
		if ( stripos( $document_url, '?dl=0') ) {
			$document_url = str_ireplace( '?dl=0', '?dl=1', $document_url );
		}

		// add &dl=1|?dl=1 if it doesnt contain
		if ( stripos( $document_url, '?dl=1') === false ) {
			$document_url = strpos( $document_url, '?') ? $document_url . '&dl=1' : $document_url . '?dl=1';
		}

		return $document_url;
	}

	/**
	 * Convert file url to full path.
	 *
	 * @param string $url url of the file.
	 * @return string|bool
	 */
	public static function get_file_url( $url ) {

		if ( strpos( $url, '/../' ) !== false ) {
			return false;
		}

		$upload_dir = wp_get_upload_dir();

		$file = str_replace( $upload_dir['baseurl'], $upload_dir['basedir'], $url );

		if ( file_exists( $file ) ) {
			return $file;
		}

		return false;
	}

}
