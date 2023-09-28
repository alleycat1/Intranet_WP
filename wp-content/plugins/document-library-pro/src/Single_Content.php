<?php

namespace Barn2\Plugin\Document_Library_Pro;

use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Registerable,
	Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Service,
	Barn2\Plugin\Document_Library_Pro\Post_Type,
	Barn2\Plugin\Document_Library_Pro\Util\Options;

defined( 'ABSPATH' ) || exit;

/**
 * Filter the single template to output document details
 *
 * @package   Barn2/document-library-pro
 * @author    Barn2 Plugins <info@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Single_Content implements Registerable, Service {

	/**
	 * {@inheritdoc}
	 */
	public function register() {

		/**
		 * Determine whether to allow DLP to modify the single dlp_document post content.
		 *
		 * This modifications include:
		 * 	- Adding a document details sidebar inside the_content
		 *  - Adding the excerpt above the content.
		 *
		 * You might want to disable this if you are using a custom `single-dlp_document.php` template.
		 *
		 * @param bool $allow_modification Whether to allow modification.
		 */
		if ( ! apply_filters( 'document_library_pro_enable_single_content_customization', true ) ) {
			return;
		}

		add_filter( 'the_content', [ $this, 'filter_single_document_content' ], 15, 1 );
		add_filter( 'post_thumbnail_html', [ $this, 'remove_featured_image' ], 999, 1 );
		add_filter( 'get_the_excerpt', [ $this, 'maybe_allow_shortcodes' ], 10, 1 );
	}

	/**
	 * Allow shortcodes in the excerpt if the option is set.
	 *
	 * @param string $excerpt The excerpt.
	 * @return string The excerpt.
	 */
	public function maybe_allow_shortcodes( $excerpt ) {
		if ( isset( Options::get_shortcode_options()['shortcodes'] ) && Options::get_shortcode_options()['shortcodes'] ) {
			$excerpt = do_shortcode( $excerpt );
		}

		return $excerpt;
	}

	/**
	 * Adds the sidebar and excerpt to the main content
	 *
	 * @param string $content
	 * @return string $content
	 */
	public function filter_single_document_content( $content ) {
		if ( ! is_singular( Post_Type::POST_TYPE_SLUG ) || ! in_the_loop() || ! is_main_query() ) {
			return $content;
		}

		remove_filter( 'the_content', [ $this, 'filter_single_document_content' ], 15 );

		if ( apply_filters( 'document_library_pro_show_details_in_single_content', true )
			&& ! post_password_required()
			&& $document_details = $this->get_document_details_html() ) {
				$content = sprintf(
					'<div class="dlp-single-wrap">
						<div class="dlp-single-left">%1$s</div>
						<div class="dlp-single-right">%2$s</div>
					</div>',
					$this->get_main_content_html( $content ),
					$document_details
				);
		} else {
			$content = $this->get_main_content_html( $content );
		}

		return $content;
	}

	/**
	 * Removes the featured image from other areas so we can display in the sidebar.
	 *
	 * @param string $html
	 * @return string $html
	 */
	public function remove_featured_image( $html ) {
		global $template;

		if ( is_singular( Post_Type::POST_TYPE_SLUG ) && 'single-dlp_document.php' !== basename( $template ) ) {
			return '';
		}

		return $html;
	}

	/**
	 * Gets the main content HTML
	 *
	 * @param string $post_content
	 * @return string $main_content
	 */
	private function get_main_content_html( $post_content ) {
		if ( has_excerpt() && in_array( 'excerpt', Options::get_document_display_fields(), true ) ) {
			$main_content = sprintf( '<p class="dlp-excerpt">%1$s</p>%2$s', get_the_excerpt(), $post_content );
		} else {
			$main_content = $post_content;
		}

		return $main_content;
	}

	/**
	 * Gets the documents details HTML
	 *
	 * @return string
	 */
	private function get_document_details_html() {
		$document        = dlp_get_document( get_the_ID() );
		$display_options = Options::get_document_display_fields();

		if ( ! $document ) {
			return false;
		}

		if ( ! $document->get_download_url()
			&& ! $document->get_file_type()
			&& ! $document->get_category_list()
			&& ! $document->get_tag_list()
			&& ! has_post_thumbnail() ) {
			return false;
		}

		$options = Options::get_shortcode_options();

		ob_start();
		?>
		<div class="dlp-document-info">
			<?php if ( $document->get_download_url() ) : ?>
				<?php Frontend_Scripts::load_download_count_scripts(); ?>
				<div class="dlp-document-info-buttons">
					<?php echo $document->get_download_button( $options['link_text'], $options['link_style'], 'direct', $options['link_target'] ); ?>
					<?php
					if ( $document->is_allowed_preview_mime_type() && $options['preview'] ) :
						Frontend_Scripts::load_preview_scripts();
						?>
						<?php echo $document->get_preview_button( $options['preview_text'], $options['preview_style'], 'single' ); ?>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<div id="dlp-document-info-list">
				<?php do_action( 'document_library_pro_single_document_details_list_before' ); ?>

				<?php if ( in_array( 'file_type', $display_options, true ) && $document->get_file_type() ) : ?>
					<div class="dlp-document-file-type">
						<span class="dlp-document-info-title"><?php esc_html_e( 'File Type: ', 'document-library-pro' ); ?></span>
						<?php echo $document->get_file_type(); ?>
					</div>
				<?php endif; ?>

				<?php if ( in_array( 'doc_categories', $display_options, true ) && $document->get_category_list() ) : ?>
					<div class="dlp-document-info-categories">
						<span class="dlp-document-info-title"><?php esc_html_e( 'Categories: ', 'document-library-pro' ); ?></span>
						<?php echo $document->get_category_list(); ?>
					</div>
				<?php endif; ?>

				<?php if ( in_array( 'doc_tags', $display_options, true ) && $document->get_tag_list() ) : ?>
					<div class="dlp-document-info-tags">
						<span class="dlp-document-info-title"><?php esc_html_e( 'Tags: ', 'document-library-pro' ); ?></span>
						<?php echo $document->get_tag_list(); ?>
					</div>
				<?php endif; ?>

				<?php if ( in_array( 'doc_author', $display_options, true ) && $document->get_author_list() ) : ?>
					<div class="dlp-document-info-author">
						<span class="dlp-document-info-title"><?php esc_html_e( 'Author: ', 'document-library-pro' ); ?></span>
						<?php echo $document->get_author_list(); ?>
					</div>
				<?php endif; ?>

				<?php if ( in_array( 'custom-fields', $display_options, true ) && $custom_fields = $document->get_custom_fields_list() ) : ?>
					<?php foreach ( $custom_fields as $custom_field ){ ?>
					<div class="dlp-document-info-custom-fields">
						<span class="dlp-document-info-title"><?php echo $custom_field['label']; ?>:</span>
						<?php echo $custom_field['value']; ?>
					</div>
					<?php } ?>
				<?php endif; ?>

				<?php if ( in_array( 'download_count', $display_options, true ) && $document->get_download_count() ) : ?>
					<div class="dlp-document-info-downloads">
						<span class="dlp-document-info-title"><?php esc_html_e( 'Downloads: ', 'document-library-pro' ); ?></span>
						<?php echo $document->get_download_count(); ?>
					</div>
				<?php endif; ?>

				<?php do_action( 'document_library_pro_single_document_details_list_after' ); ?>
			</div>

			<?php if ( in_array( 'thumbnail', $display_options, true ) && has_post_thumbnail() ) : ?>
				<div class="dlp-document-info-image">
				<?php
				remove_filter( 'post_thumbnail_html', [ $this, 'remove_featured_image' ], 999 );
				the_post_thumbnail( apply_filters( 'document_library_pro_image_single_size', 'medium' ) );
				add_filter( 'post_thumbnail_html', [ $this, 'remove_featured_image' ], 999, 1 );
				?>
				</div>
			<?php endif; ?>
		</div>
		<?php

		return ob_get_clean();
	}
}
