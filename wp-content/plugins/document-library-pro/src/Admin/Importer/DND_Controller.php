<?php
namespace Barn2\Plugin\Document_Library_Pro\Admin\Importer;

defined( 'ABSPATH' ) || exit;
/**
 * This class is the controller for the DND uploader
 *
 * @package   Barn2/document-library-pro
 * @author    Barn2 Plugins <info@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class DND_Controller {
	/**
	 * Render the dropzone!
	 */
	public function render() {
		?>
		<h2><?php esc_html_e( 'Drag and drop file upload', 'document-library-pro' ); ?></h2>

		<p><?php esc_html_e( 'Upload one or more files, and each one will automatically be added as a new document. The filename will be used as the document title.', 'document-library-pro' ); ?></p>


		<div class="dlp-uploader-form">
			<div id="plupload-upload-ui" class="hide-if-no-js">
				<div id="drag-drop-area">
					<div class="drag-drop-inside">
						<p class="drag-drop-info"><?php esc_html_e( 'Drop files to upload', 'document-library-pro' ); ?></p>
						<p><?php esc_html_e( 'or', 'document-library-pro' ); ?></p>
						<p class="drag-drop-buttons">
							<input id="plupload-browse-button" type="button" value="<?php esc_attr_e( 'Select Files', 'document-library-pro' ); ?>" class="button" />
						</p>
					</div>
				</div>
			</div>

			<p class="max-upload-size">
				<?php
				/* translators: %s: Maximum allowed file size. */
				printf( esc_html__( 'Maximum upload file size: %s.', 'document-library-pro' ), esc_html( size_format( wp_max_upload_size() ) ) );
				?>
			</p>

			<div id="media-upload-error"></div>
			<div id="media-items"></div>
			<div id="media-file-errors"></div>
		</div>
		<?php
	}

	/**
	 * Plupload init options
	 *
	 * @return array
	 */
	public static function get_plupload_options() {
		$max_upload_size = wp_max_upload_size();

		if ( ! $max_upload_size ) {
			$max_upload_size = 0;
		}

		$options = [
			'browse_button'    => 'plupload-browse-button',
			'container'        => 'plupload-upload-ui',
			'drop_element'     => 'drag-drop-area',
			'file_data_name'   => 'dlp-upload',
			'url'              => admin_url( 'admin-ajax.php' ),
			'filters'          => [
				'max_file_size' => $max_upload_size . 'b'
			],
			'multipart_params' => [
				'action'   => 'dlp_upload_files',
				'_wpnonce' => wp_create_nonce( 'dlp-import-files' ),
			],
		];

		/*
		 * Currently only iOS Safari supports multiple files uploading,
		 * but iOS 7.x has a bug that prevents uploading of videos when enabled.
		 * See #29602.
		 */
		if ( wp_is_mobile() && strpos( $_SERVER['HTTP_USER_AGENT'], 'OS 7_' ) !== false && strpos( $_SERVER['HTTP_USER_AGENT'], 'like Mac OS X' ) !== false ) {
			$options['multi_selection'] = false;
		}

		return apply_filters( 'document_library_pro_plupload_options', $options );
	}

	/**
	 * Error messages for plupload
	 *
	 * @return array
	 */
	public static function get_plupload_l10n() {
		$uploader_l10n = [
			'queue_limit_exceeded'      => __( 'You have attempted to queue too many files.', 'document-library-pro' ),
			/* translators: %s: File name. */
			'file_exceeds_size_limit'   => __( '%s exceeds the maximum upload size for this site.', 'document-library-pro' ),
			'zero_byte_file'            => __( 'This file is empty. Please try another.', 'document-library-pro' ),
			'invalid_filetype'          => __( 'Sorry, this file type is not permitted for security reasons.', 'document-library-pro' ),
			'not_an_image'              => __( 'This file is not an image. Please try another.', 'document-library-pro' ),
			'image_memory_exceeded'     => __( 'Memory exceeded. Please try another smaller file.', 'document-library-pro' ),
			'image_dimensions_exceeded' => __( 'This is larger than the maximum size. Please try another.', 'document-library-pro' ),
			'default_error'             => __( 'An error occurred in the upload. Please try again later.', 'document-library-pro' ),
			'missing_upload_url'        => __( 'There was a configuration error. Please contact the server administrator.', 'document-library-pro' ),
			'upload_limit_exceeded'     => __( 'You may only upload 1 file.', 'document-library-pro' ),
			'http_error'                => __( 'Unexpected response from the server. The file may have been uploaded successfully. Check in the Media Library or reload the page.', 'document-library-pro' ),
			'http_error_image'          => __( 'Post-processing of the image failed likely because the server is busy or does not have enough resources. Uploading a smaller image may help. Suggested maximum size is 2500 pixels.', 'document-library-pro' ),
			'upload_failed'             => __( 'Upload failed.', 'document-library-pro' ),
			/* translators: 1: Opening link tag, 2: Closing link tag. */
			'big_upload_failed'         => __( 'Please try uploading this file with the %1$sbrowser uploader%2$s.', 'document-library-pro' ),
			/* translators: %s: File name. */
			'big_upload_queued'         => __( '%s exceeds the maximum upload size for the multi-file uploader when used in your browser.', 'document-library-pro' ),
			'io_error'                  => __( 'IO error.', 'document-library-pro' ),
			'security_error'            => __( 'Security error.', 'document-library-pro' ),
			'file_cancelled'            => __( 'File canceled.', 'document-library-pro' ),
			'upload_stopped'            => __( 'Upload stopped.', 'document-library-pro' ),
			'dismiss'                   => __( 'Dismiss', 'document-library-pro' ),
			'crunching'                 => __( 'Crunching&hellip;', 'document-library-pro' ),
			'deleted'                   => __( 'moved to the Trash.', 'document-library-pro' ),
			/* translators: %s: File name. */
			'error_uploading'           => __( '&#8220;%s&#8221; has failed to upload.', 'document-library-pro' ),
		];

		return $uploader_l10n;
	}
}
