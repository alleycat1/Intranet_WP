<?php

namespace Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro\Data;

use Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro\Util\Util;

/**
 * Gets post data for the image column.
 *
 * @package   Barn2\posts-table-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Post_Image extends Abstract_Table_Data {

	private $image_size;
	private $lightbox;

	public function __construct( $post, $links = '', $image_size = '', $lightbox = false ) {
		parent::__construct( $post, $links );

		$this->image_size = $image_size ?: 'thumbnail';
		$this->lightbox   = $lightbox;
	}

	public function get_data() {
		$image         = '';
		$attachment_id = get_post_thumbnail_id( $this->post );

		if ( 0 === $attachment_id && wp_attachment_is_image( $this->post ) ) {
			$attachment_id = $this->post->ID;
		}

		$full_size_src = $attachment_id ? wp_get_attachment_image_src( $attachment_id, apply_filters( 'document_library_pro_image_full_size', 'full' ) ) : false;

		if ( $full_size_src ) {
			$atts = [
				'title'                   => get_post_field( 'post_title', $attachment_id ),
				'alt'                     => trim( wp_strip_all_tags( get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) ) ),
				'data-caption'            => get_post_field( 'post_excerpt', $attachment_id ),
				'data-src'                => $full_size_src[0],
				'data-large_image'        => $full_size_src[0],
				'data-large_image_width'  => $full_size_src[1],
				'data-large_image_height' => $full_size_src[2],
				'class'                   => ''
			];

			// Caption fallback
			$atts['data-caption'] = empty( $atts['data-caption'] ) ? trim( esc_attr( wp_strip_all_tags( $this->post->post_title ) ) ) : $atts['data-caption'];

			// Alt fallbacks
			$atts['alt'] = empty( $atts['alt'] ) ? $atts['data-caption'] : $atts['alt'];
			$atts['alt'] = empty( $atts['alt'] ) ? $atts['title'] : $atts['alt'];
			$atts['alt'] = empty( $atts['alt'] ) ? trim( esc_attr( wp_strip_all_tags( $this->post->post_title ) ) ) : $atts['alt'];

			// Get the image
			$image = wp_get_attachment_image( $attachment_id, $this->image_size, false, $atts );
			$image = apply_filters( 'document_library_pro_data_image_before_link', $image, $this->post );

			$wrapper_width = Util::get_image_size_width( $this->image_size );
			$wrapper_class = 'posts-table-image-wrapper';

			// Maybe wrap image with lightbox or link markup.
			if ( $this->lightbox ) {
				$image = '<a href="' . esc_url( $full_size_src[0] ) . '">' . $image . '</a>';

				$wrapper_class .= ' posts-table-gallery__image';
			} elseif ( array_intersect( [ 'all', 'image' ], $this->links ) ) {
				$image = Util::format_post_link( $this->post, $image );
			}

			$wrapper_style = $wrapper_width ? sprintf( 'width:%upx;', $wrapper_width ) : '';
			$thumbnail_src = wp_get_attachment_image_src( $attachment_id, $this->image_size );

			$image = '<div style="' . esc_attr( $wrapper_style ) . '" data-thumb="' . esc_attr( esc_url( $thumbnail_src[0] ) ) . '" class="' . esc_attr( $wrapper_class ) . '">' . $image . '</div>';
		}

		return apply_filters( 'document_library_pro_data_image', $image, $this->post );
	}

}
