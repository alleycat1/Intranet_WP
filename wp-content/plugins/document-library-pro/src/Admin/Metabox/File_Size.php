<?php

namespace Barn2\Plugin\Document_Library_Pro\Admin\Metabox;

use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Conditional,
	Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Registerable,
	Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Service,
	Barn2\Plugin\Document_Library_Pro\Document,
	Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Util;

defined( 'ABSPATH' ) || exit;

/**
 * File Size - Edit Document Metabox
 *
 * @package   Barn2/document-library-pro
 * @author    Barn2 Plugins <info@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class File_Size implements Registerable, Service, Conditional {
	const ID = 'dlp_file_size';

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
		add_action( 'add_meta_boxes', [ $this, 'register_metabox' ] );
		add_action( 'save_post_dlp_document', [ $this, 'save' ] );
	}

	/**
	 * Register the metabox
	 */
	public function register_metabox() {
		\add_meta_box(
			self::ID,
			__( 'File Size', 'document-library-pro' ),
			[ $this, 'render_metabox' ],
			'dlp_document',
			'side',
			'default'
		);
	}

	/**
	 * Render the metabox.
	 *
	 * @param WP_Post $post
	 */
	public function render_metabox( $post ) {
		try {
			$document = new Document( $post->ID );

			?>
			<input
				type="text"
				id="dlp_file_size_input"
				name="_dlp_document_file_size"
				value="<?php echo esc_attr( $document->get_file_size() ); ?>"
				<?php echo disabled( $document->get_link_type(), 'file' ); ?>
			/>
			<?php
        // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
		} catch ( \Exception $exception ) {
			// silent
		}
	}

	/**
	 * Save the metabox values
	 *
	 * @param mixed $post_id
	 */
	public function save( $post_id ) {
        // phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( isset( $_POST['_dlp_document_file_size'] ) && $_POST['_dlp_document_link_type'] !== 'file' ) {
			$file_size = filter_input( INPUT_POST, '_dlp_document_file_size', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

			try {
				$document = new Document( $post_id );
				$document->set_file_size( $file_size );
            // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
			} catch ( \Exception $exception ) {
				// silent
			}
		}
	}
}
