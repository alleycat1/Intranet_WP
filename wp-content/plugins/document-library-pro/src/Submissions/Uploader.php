<?php
namespace Barn2\Plugin\Document_Library_Pro\Submissions;

use WP_Error;

/**
 * Responsible for handling uploads and formatting data just before the upload.
 *
 * @package   Barn2\document-library-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Uploader {

	/**
	 * List of files submitted.
	 *
	 * @var array
	 */
	private $file_data;

	/**
	 * Initialize the class.
	 *
	 * @param array $file_data
	 */
	public function __construct( $file_data ) {
		$this->file_data = $file_data;
	}

	/**
	 * Prepares a formatted array so that we can then upload the files later.
	 *
	 * @return array
	 */
	public function prepare_uploaded_files() {
		$file_data       = $this->file_data;
		$files_to_upload = [];

		if ( is_array( $file_data['name'] ) ) {
			foreach ( $file_data['name'] as $file_data_key => $file_data_value ) {
				if ( $file_data['name'][ $file_data_key ] ) {
					$type              = wp_check_filetype( $file_data['name'][ $file_data_key ] ); // Map mime type to one WordPress recognises.
					$files_to_upload[] = [
						'name'     => $file_data['name'][ $file_data_key ],
						'type'     => $type['type'],
						'tmp_name' => $file_data['tmp_name'][ $file_data_key ],
						'error'    => $file_data['error'][ $file_data_key ],
						'size'     => $file_data['size'][ $file_data_key ],
					];
				}
			}
		} else {
			$type              = wp_check_filetype( $file_data['name'] ); // Map mime type to one WordPress recognises.
			$file_data['type'] = $type['type'];
			$files_to_upload[] = $file_data;
		}

		return $files_to_upload;
	}

	/**
	 * Execute the upload of files.
	 *
	 * @param array $file Array of $_FILE data to upload
	 * @param array $args Optional file arguments.
	 * @return object details of the uploaded file.
	 */
	public function upload_file( $file, $args = [] ) {
		global $dlp_upload, $dlp_uploading_file;

		include_once ABSPATH . 'wp-admin/includes/file.php';
		include_once ABSPATH . 'wp-admin/includes/media.php';

		$args = wp_parse_args(
			$args,
			[
				'file_key'           => '',
				'file_label'         => '',
				'allowed_mime_types' => '',
			]
		);

		$dlp_upload         = true;
		$dlp_uploading_file = $args['file_key'];
		$uploaded_file      = new \stdClass();
		if ( '' === $args['allowed_mime_types'] ) {
			$allowed_mime_types = get_allowed_mime_types();
		} else {
			$allowed_mime_types = $args['allowed_mime_types'];
		}

		/**
		 * Filter file configuration before upload.
		 *
		 * @param array $file               Array of $_FILE data to upload.
		 * @param array $args               Optional file arguments.
		 * @param array $allowed_mime_types Array of allowed mime types from field config or defaults.
		 */
		$file = apply_filters( 'dlp_upload_file_pre_upload', $file, $args, $allowed_mime_types );

		if ( is_wp_error( $file ) ) {
			return $file;
		}

		if ( ! in_array( $file['type'], $allowed_mime_types, true ) ) {
			$allowed_file_extensions = implode( ', ', str_replace( '|', ', ', array_keys( $allowed_mime_types ) ) );

			if ( $args['file_label'] ) {
				// translators: %1$s is the file field label; %2$s is the file type; %3$s is the list of allowed file types.
				return new WP_Error( 'upload', sprintf( __( '"%1$s" (filetype %2$s) needs to be one of the following file types: %3$s', 'document-library-pro' ), $args['file_label'], $file['type'], $allowed_file_extensions ) );
			} else {
				// translators: %s is the list of allowed file types.
				return new WP_Error( 'upload', sprintf( __( 'Uploaded files need to be one of the following file types: %s', 'document-library-pro' ), $allowed_file_extensions ) );
			}
		} else {
			$upload = wp_handle_upload( $file, apply_filters( 'dlp_handle_upload_overrides', [ 'test_form' => false ] ) );
			if ( ! empty( $upload['error'] ) ) {
				return new WP_Error( 'upload', $upload['error'] );
			} else {
				$uploaded_file->url       = $upload['url'];
				$uploaded_file->file      = $upload['file'];
				$uploaded_file->name      = basename( $upload['file'] );
				$uploaded_file->type      = $upload['type'];
				$uploaded_file->size      = $file['size'];
				$uploaded_file->extension = substr( strrchr( $uploaded_file->name, '.' ), 1 );
			}
		}

		$dlp_upload         = false;
		$dlp_uploading_file = '';

		return $uploaded_file;
	}

}
