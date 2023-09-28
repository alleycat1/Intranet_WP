<?php
namespace Barn2\Plugin\Document_Library_Pro\Grid;

use Barn2\Plugin\Document_Library_Pro\Document,
	Barn2\Plugin\Document_Library_Pro\Template_Loader_Factory,
	Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro\Table_Args,
	Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro\Util\Util;
use WP_Post;

/**
 * Handles the display of a Grid Card
 *
 * @package   Barn2\document-library-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Grid_Card {

	/**
	 * The Document.
	 *
	 * @var Document $document
	 */
	protected $document;

	private $template_loader;
	private $args;
	private $post;
	private $grid_content;
	private $links;
	private $columns;
	private $templates;

	/**
	 * Constructor.
	 *
	 * @param Document $document
	 * @param Table_Args $args
	 * @param \WP_Post $post
	 */
	public function __construct( Document $document, Table_Args $args, \WP_Post $post ) {
		$this->document     = $document;
		$this->args         = $args;
		$this->post         = $post;
		$this->grid_content = $this->args->grid_content;
		$this->links        = $this->args->links;
		$this->columns      = $this->args->grid_columns;

		$this->template_loader = Template_Loader_Factory::create();
		$this->templates       = $this->get_templates();
	}

	/**
	 * Get the Grid Card HTML.
	 *
	 * @return mixed
	 */
	public function get_html() {
		$html = '<div class="dlp-grid-card"><div class="dlp-grid-card-inner">';

		// Featured Image / File Icon
		if ( $this->grid_content['image'] ) {
			$html .= $this->templates['image'];
		}

		$content_css_classes = $this->grid_content['image'] ? 'dlp-grid-card-content' : 'dlp-grid-card-content no-image';

		$html .= sprintf( '<div class="%s">', $content_css_classes );

		ob_start();
		do_action( 'document_library_pro_grid_card_before_info', $this->document, $this->args, $this->post );
		$html .= ob_get_clean() ?? '';

		// File Type | File Size | Category List
		if ( $this->grid_content['file_type'] || $this->grid_content['file_size'] || $this->grid_content['doc_categories'] ) {
			$html .= '<div class="dlp-grid-card-info">';

			$html .= '<div class="dlp-grid-card-file-info">';

			if ( $this->grid_content['file_type'] ) {
				$html .= $this->templates['file_type'];
			}

			if ( $this->grid_content['file_size'] ) {
				$html .= $this->templates['file_size'];
			}

			if ( $this->grid_content['download_count'] ) {
				$html .= $this->templates['download_count'];
			}

			$html .= '</div>';

			if ( $this->grid_content['doc_categories'] ) {
				$html .= $this->templates['doc_categories'];
			}

			$html .= '</div>';
		}

		ob_start();
		do_action( 'document_library_pro_grid_card_before_title', $this->document, $this->args, $this->post );
		$html .= ob_get_clean() ?? '';

		// Title
		if ( $this->grid_content['title'] ) {
			$html .= $this->templates['title'];
		}

		ob_start();
		do_action( 'document_library_pro_grid_card_before_excerpt', $this->document, $this->args, $this->post );
		$html .= ob_get_clean() ?? '';

		// Excerpt
		if ( $this->grid_content['excerpt'] ) {
			$html .= $this->templates['excerpt'];
		}

		// Custom fields
		if ( isset( $this->grid_content['custom_fields'] ) && $this->grid_content['custom_fields'] ) {
			$html .= $this->templates['custom_fields'];
		}

		ob_start();
		do_action( 'document_library_pro_grid_card_before_link', $this->document, $this->args, $this->post );
		$html .= ob_get_clean() ?? '';

		// Document Link
		if ( $this->grid_content['link'] ) {
			$html .= $this->templates['link'];
		}

		ob_start();
		do_action( 'document_library_pro_grid_card_after_link', $this->document, $this->args, $this->post );
		$html .= ob_get_clean() ?? '';

		$html .= '</div></div></div>';

		return apply_filters( 'document_library_pro_grid_html', $html, $this->document, $this->args, $this->templates );
	}

	/**
	 * Get the Grid Card templates.
	 *
	 * @return mixed
	 */
	private function get_templates() {
		$templates = [
			'image'          => $this->template_loader->get_template(
				'grid-card/image.php',
				[
					'document' => $this->document,
					'image'    => $this->get_image()
				]
			),
			'title'          => $this->template_loader->get_template(
				'grid-card/title.php',
				[
					'document' => $this->document,
					'title'    => $this->get_title()
				]
			),
			'doc_categories' => $this->template_loader->get_template(
				'grid-card/categories.php',
				[
					'document'   => $this->document,
					'categories' => $this->document->get_category_list( array_intersect( [ 'all', 'doc_categories' ], $this->links ) )
				]
			),
			'file_type'      => $this->template_loader->get_template(
				'grid-card/file_type.php',
				[
					'document'  => $this->document,
					'file_type' => $this->document->get_file_type()
				]
			),
			'file_size'      => $this->template_loader->get_template(
				'grid-card/file_size.php',
				[
					'document'  => $this->document,
					'file_size' => $this->document->get_file_size()
				]
			),
			'download_count'      => $this->template_loader->get_template(
				'grid-card/download_count.php',
				[
					'document'       => $this->document,
					'download_count' => $this->document->get_download_count()
				]
			),
			'excerpt'        => $this->template_loader->get_template(
				'grid-card/excerpt.php',
				[
					'document' => $this->document,
					'content'  => $this->get_text_content()
				]
			),
			'custom_fields'        => $this->template_loader->get_template(
				'grid-card/custom_fields.php',
				[
					'document'      => $this->document,
					'custom_fields' => $this->document->get_custom_fields_list()
				]
			),
			'link'           => $this->template_loader->get_template(
				'grid-card/document_link.php',
				[
					'document' => $this->document,
					'link'     => $this->get_document_link()
				]
			),
		];

		return apply_filters( 'document_library_pro_grid_templates', $templates, $this->document, $this->args, $this->template_loader );
	}

	/**
	 * Get the Document Link.
	 *
	 * @return string
	 */
	public function get_document_link() {
		$document_links = '';

		if ( $this->args->document_link ) {
			$document_links .= $this->document->get_download_button( $this->args->link_text, $this->args->link_style, $this->args->link_destination, $this->args->link_target );
		}

		if ( $this->args->preview ) {
			$document_links .= $this->document->get_preview_button( $this->args->preview_text, $this->args->preview_style, 'grid' );
		}

		return $document_links;
	}

	/**
	 * Get the Document Title.
	 *
	 * @return string
	 */
	public function get_title() {
		if ( array_intersect( [ 'all', 'title' ], $this->links ) ) {
			$title = Util::format_post_link( $this->post );
		} else {
			$title = get_the_title( $this->post );
		}

		return $title;
	}

	/**
	 * Get the Document text content.
	 *
	 * @return string
	 */
	public function get_text_content() {

		if ( $this->post->post_excerpt ) {
			$content = apply_filters( 'the_excerpt', $this->post->post_excerpt );
		} else {
			$content = apply_filters( 'the_content', get_the_content( null, false, $this->post ) );
		}

		return $content;
	}

	/**
	 * Get the Document image of fallback icon.
	 *
	 * @return mixed
	 */
	public function get_image() {
		$attachment_id = get_post_thumbnail_id( $this->document->get_id() );

		if ( $attachment_id ) {
			// Create $atts for PhotoSwipe
			$full_src   = wp_get_attachment_image_src( $attachment_id, apply_filters( 'document_library_pro_image_full_size', 'full' ) );
			$large_src  = wp_get_attachment_image_src( $attachment_id, apply_filters( 'document_library_pro_image_large_size', 'large' ) );
			$medium_src = wp_get_attachment_image_src( $attachment_id, apply_filters( 'document_library_pro_image_medium_size', 'medium' ) );

			// Handles responsive srcset & sizes
			$srcset = in_array( $this->columns, [ '1', '2' ], true ) ? '' : "$large_src[0] $large_src[1]w, $medium_src[0] $medium_src[1]w";
			$sizes  = in_array( $this->columns, [ '1', '2' ], true ) ? '' : "(max-width: 530px) $large_src[1]px, $medium_src[1]px";

			$atts = [
				'title'                   => get_post_field( 'post_title', $attachment_id ),
				'alt'                     => trim( wp_strip_all_tags( get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) ) ),
				'sizes'                   => apply_filters( 'document_library_pro_image_grid_sizes', $sizes, $attachment_id ),
				'srcset'                  => apply_filters( 'document_library_pro_image_grid_srcset', $srcset, $attachment_id ),
				'data-caption'            => get_post_field( 'post_excerpt', $attachment_id ),
				'data-src'                => $full_src[0],
				'data-large_image'        => $full_src[0],
				'data-large_image_width'  => $full_src[1],
				'data-large_image_height' => $full_src[2],
				'class'                   => ''
			];

			// Caption fallback
			$atts['data-caption'] = empty( $atts['data-caption'] ) ? trim( esc_attr( wp_strip_all_tags( $this->post->post_title ) ) ) : $atts['data-caption'];

			// Alt fallbacks
			$atts['alt'] = empty( $atts['alt'] ) ? $atts['data-caption'] : $atts['alt'];
			$atts['alt'] = empty( $atts['alt'] ) ? $atts['title'] : $atts['alt'];
			$atts['alt'] = empty( $atts['alt'] ) ? trim( esc_attr( wp_strip_all_tags( $this->post->post_title ) ) ) : $atts['alt'];

			// Get the image to display
			$image = wp_get_attachment_image( $attachment_id, apply_filters( 'document_library_pro_image_grid_size', 'large' ), false, $atts );
		} else {
			$image = sprintf( '<div class="dlp-grid-card-featured-icon">%s</div>', $this->document->get_file_icon() );
		}

		// Wrap image with lightbox markup or post link - lightbox takes priority over the 'links' option.
		if ( $this->args->get_args()['lightbox'] && $attachment_id ) {
			$image = sprintf( '<a href="%1$s">%2$s</a>', esc_url( $full_src[0] ), $image );
		} elseif ( array_intersect( [ 'all', 'image' ], $this->links ) ) {
			$image = Util::format_post_link( $this->post, $image );
		}

		return apply_filters( 'document_library_pro_grid_image', $image, $this->post );
	}

}
