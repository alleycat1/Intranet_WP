<?php

namespace Barn2\Plugin\Document_Library_Pro\Admin\Metabox;

use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Conditional,
	Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Registerable,
	Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Service,
	Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Util,
	Barn2\Plugin\Document_Library_Pro\Util\Options,
	Barn2\Plugin\Document_Library_Pro\Util\SVG_Icon,
	Barn2\Plugin\Document_Library_Pro\Post_Type,
	Barn2\Plugin\Document_Library_Pro\Document,
	Barn2\Plugin\Document_Library_Pro\Template_Loader_Factory;

defined( 'ABSPATH' ) || exit;

/**
 * Document Link - Edit Document Metabox
 *
 * @package   Barn2/document-library-pro
 * @author    Barn2 Plugins <info@barn2.com>
 * @license   GPL-3.0
 */
class Document_Link implements Registerable, Service, Conditional {
	const ID = 'document_link';

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
		add_action( 'admin_footer', [ $this, 'print_version_history_item_template' ] );
		add_action( 'add_meta_boxes', [ $this, 'register_metabox' ], 1 );
		add_action( 'save_post_' . Post_Type::POST_TYPE_SLUG, [ $this, 'save' ] );
	}

	/**
	 * Register the metabox
	 */
	public function register_metabox() {
		add_meta_box(
			self::ID,
			__( 'Document Link', 'document-library-pro' ),
			[ $this, 'render' ],
			'dlp_document',
			'side',
			'high'
		);
	}

	/**
	 * Render the metabox.
	 *
	 * @param WP_Post $post
	 */
	public function render( $post ) {
		$document = new Document( $post->ID );

		$template_loader       = Template_Loader_Factory::create();
		$version_control_mode  = Options::get_version_control_mode();
		$version_control_class = $version_control_mode !== false ? "version-control version-control-$version_control_mode" : '';
		$document_history      = $document->get_version_history();
		$button_text           = $document->get_file_id() || isset( $document_history['file'] ) && count( $document_history['file'] ) > 1 ? __( 'Replace File', 'document-library-pro' ) : __( 'Add File', 'document-library-pro' );
		$file_attached_class   = $document->get_file_id() ? ' active' : '';
		$file_details_class    = $document->get_link_type() === 'file' ? 'active' : '';
		$url_details_class     = $document->get_link_type() === 'url' ? 'active' : '';

		?>
		<label for="<?php esc_attr( self::ID ); ?>" class="howto"><?php esc_html_e( 'Upload a file or add a URL where the document is located:', 'document-library-pro' ); ?></label>

		<!-- option selector -->
		<select name="_dlp_document_link_type" id="dlp_document_link_type" class="postbox">
			<option value="none" <?php selected( $document->get_link_type(), 'none' ); ?>><?php esc_html_e( 'None', 'document-library-pro' ); ?></option>
			<option value="file" <?php selected( $document->get_link_type(), 'file' ); ?>><?php esc_html_e( 'File Upload', 'document-library-pro' ); ?></option>
			<option value="url" <?php selected( $document->get_link_type(), 'url' ); ?>><?php esc_html_e( 'A custom URL', 'document-library-pro' ); ?></option>
		</select>

		<!-- file upload -->
		<div id="dlp_file_attachment_details" class="<?php echo esc_attr( $file_details_class ); ?> <?php echo esc_attr( $version_control_class ); ?>">
			<div id="dlp_file_attached" class="<?php echo esc_attr( $file_attached_class ); ?>">
				<button type="button" id="dlp_remove_file_button">
					<span class="remove-file-icon" aria-hidden="true"></span>

					<span class="screen-reader-text">
					<?php
					/* translators: %s: File name */
					echo esc_html( sprintf( __( 'Remove file: %s', 'document-library-pro' ), $document->get_file_name() ) );
					?>
					</span>
				</button>

				<span class="dlp_file_name_text"><?php echo esc_html( $document->get_file_name() ); ?></span>
				<input id="dlp_file_name_input" type="hidden" name="_dlp_attached_file_name" value="<?php echo esc_attr( $document->get_file_name() ); ?>" />
			</div>

			<?php
			$history_type    = 'file';
			$version_history = $document_history['file'] ?? [];

			if ( $version_control_mode === 'delete' && ! empty( $version_history ) ) {
				$first_key       = array_key_first( $version_history );
				$version_history = [ $first_key => $version_history[ $first_key ] ];
			}

			$has_past_versions = ! empty( $version_history );

			// include wp_normalize_path( dirname( __DIR__ ) . '/views/html-version-history.php' );
			$template_loader->load_template( 'admin/html-version-history.php', compact( 'document', 'history_type', 'version_history', 'has_past_versions', 'version_control_mode' ) );
			?>

			<button id="dlp_add_file_button" class="button button-large"><?php echo esc_html( $button_text ); ?></button>
			<input id="dlp_file_id" type="hidden" name="_dlp_attached_file_id" value="<?php echo esc_attr( $document->get_file_id() ); ?>" />

		</div>

		<!-- direct url -->
		<div id="dlp_link_url_details" class="<?php echo esc_attr( $url_details_class ); ?> <?php echo esc_attr( $version_control_class ); ?>">
			<fieldset class="dlp-link-url">
				<?php SVG_Icon::render( 'link' ); ?>
				<input type="text" id="dlp_direct_link_input" name="_dlp_direct_link_url" value="<?php echo esc_attr( $document->get_direct_link() ); ?>" placeholder="<?php echo esc_attr( 'https://' ); ?>" />
			</fieldset>

			<?php
			$history_type      = 'url';
			$version_history   = $document_history['url'] ?? [];
			$has_past_versions = ! empty( $version_history );

			$template_loader->load_template( 'admin/html-version-history.php', compact( 'document', 'history_type', 'version_history', 'has_past_versions', 'version_control_mode' ) );
			?>

		</div>
		<?php
	}

	/**
	 * Print the underscore template for a single item in the version history list
	 */
	public function print_version_history_item_template() {
		?>
		<script type="text/html" id="tmpl-dlp-version-history-item">
			<label>
				<input
					type="radio"
					id="dlp_version-{{{data.attachment.id}}}"
					name="_dlp_version_history_{{{data.history_type}}}_selected"
					value="{{{ data.attachment.id }}}"
					data-filename="{{{data.attachment.filename}}}"
					data-filesize="{{{data.attachment.filesizeHumanReadable}}}"
					data-date="{{{data.attachment.dateFormatted}}}"
					data-timestamp="{{{data.attachment.date}}}"
					data-last_used="{{{data.attachment.lastUsed}}}"
				/>
				<input type="hidden" class="file-version" name="_dlp_version_history[{{{data.history_type}}}][{{{data.attachment.id}}}][version]" value="" />
				<input type="hidden" class="file-last-used" name="_dlp_version_history[{{{data.history_type}}}][{{{data.attachment.id}}}][last_used]" value="" />
				<a href="{{{data.href}}}" class="{{{data.history_type}}}name" aria-hidden="false" target="{{{data.target}}}">
					<span class="version-filename">{{{data.attachment.filename}}}</span>
				</a>
			</label>

			<a class="edit-version version-action" href="#dlp_version_history_list">
				<span class="dashicons dashicons-edit"></span>
			</a>

			<a class="remove-version version-action" href="#dlp_version_history_list">
				<span class="dashicons dashicons-trash"></span>
			</a>

			<dl class="dlp_version_info"></dl>

			<div class="dlp_version_label_inline_editor hidden">
				<label>
					<?php esc_html_e( 'version', 'document-library-pro' ); ?>
					<input type="text" class="version-input" value="" />
				</label>
				<a href="#dlp_version_label_inline_editor" class="hide-if-no-js button"><?php esc_html_e( 'OK', 'document-library-pro' ); ?></a>
				<a href="#dlp_version_label_inline_editor" class="hide-if-no-js button-cancel"><?php esc_html_e( 'Cancel', 'document-library-pro' ); ?></a>
			</div>
					</script>

					<script type="text/html" id="tmpl-dlp-file-version-info">
			<dt class="link-version-label"><?php esc_html_e( 'version', 'document-library-pro' ); ?></dt>
			<dd class="link-version"></dd>

			<dt class="link-size-label"><?php esc_html_e( 'size', 'document-library-pro' ); ?></dt>
			<dd class="link-size">{{{data.attachment.filesizeHumanReadable}}}</dd>

			<dt class="link-last_used-label"><?php esc_html_e( 'uploaded', 'document-library-pro' ); ?></dt>
			<dd class="link-last_used">{{{data.attachment.dateFormatted}}}</dd>
					</script>

					<script type="text/html" id="tmpl-dlp-url-version-info">
			<dt class="link-version-label"><?php esc_html_e( 'version', 'document-library-pro' ); ?></dt>
			<dd class="link-version"></dd>

			<dt class="link-size-label"><?php esc_html_e( 'size', 'document-library-pro' ); ?></dt>
			<dd class="link-size"></dd>

			<dt class="link-last_used-label"><?php esc_html_e( 'uploaded', 'document-library-pro' ); ?></dt>
			<dd class="link-last_used">{{{data.file.dateFormatted}}}</dd>
		</script>
		<?php
	}

	/**
	 * Save the metabox values
	 *
	 * @param mixed $post_id
	 */
	public function save( $post_id ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( ! isset( $_POST['_dlp_document_link_type'] ) ) {
			return;
		}

		$type            = filter_input( INPUT_POST, '_dlp_document_link_type', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$data            = [];
		$version_history = array_filter( (array) filter_input( INPUT_POST, '_dlp_version_history', FILTER_SANITIZE_FULL_SPECIAL_CHARS, FILTER_REQUIRE_ARRAY ) );

		switch ( $type ) {
			case 'url':
				$data['direct_url'] = filter_input( INPUT_POST, '_dlp_direct_link_url', FILTER_SANITIZE_URL );
				$data['file_id']    = md5( $data['direct_url'] );

				if ( isset( $version_history['url'] ) && ! isset( $version_history[ 'url' ][ $data['file_id'] ] ) ) {
					$version_history[ 'url' ][ $data['file_id'] ] = [
						'url'     => $data['direct_url'],
						'version' => '',
						'size'    => '',
					];
				}

				break;

			case 'file':
				$data['file_id']   = filter_input( INPUT_POST, '_dlp_attached_file_id', FILTER_SANITIZE_NUMBER_INT );
				$data['file_name'] = filter_input( INPUT_POST, '_dlp_attached_file_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

				break;
		}

		if ( isset( $version_history[ $type ] ) && isset( $version_history[ $type ][ $data['file_id'] ] ) ) {
			$version_history[ $type ][ $data['file_id'] ]['last_used'] = time();
		}

		try {
			$document = new Document( $post_id );
			$document->set_version_history( $version_history );
			$document->set_document_link( $type, $data );
		// phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
		} catch ( \Exception $exception ) {
			// silent
		}
	}
}
