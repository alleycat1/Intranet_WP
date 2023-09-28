<?php

namespace Barn2\Plugin\Document_Library_Pro\Admin\Wizard\Steps;

use Barn2\Plugin\Document_Library_Pro\Dependencies\Setup_Wizard\Api;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Setup_Wizard\Step,
	Barn2\Plugin\Document_Library_Pro\Util\Options;

/**
 * Grid Settings Step.
 *
 * @package   Barn2/document-library-pro
 * @author    Barn2 Plugins <info@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Grid extends Step {

	/**
	 * The default or user setting
	 *
	 * @var array
	 */
	private $values;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->set_id( 'grid' );
		$this->set_name( esc_html__( 'Grid', 'document-library-pro' ) );
		$this->set_description( esc_html__( 'Now, choose which information will appear in the list of documents.', 'document-library-pro' ) );
		$this->set_title( esc_html__( 'Document Grid', 'document-library-pro' ) );
		$this->set_hidden( true );

		$this->values = $this->get_grid_content_values();
	}

	/**
	 * {@inheritdoc}
	 */
	public function setup_fields() {

		$fields = [
			'image'          => [
				'title' => __( 'Library content', 'document-library-pro' ),
				'label' => __( 'Image', 'document-library-pro' ),
				'type'  => 'checkbox',
				'value' => $this->values['image']
			],
			'title'          => [
				'label' => __( 'Title', 'document-library-pro' ),
				'type'  => 'checkbox',
				'value' => $this->values['title']
			],
			'file_type'      => [
				'label' => __( 'File type', 'document-library-pro' ),
				'type'  => 'checkbox',
				'value' => $this->values['file_type']
			],
			'file_size'      => [
				'label' => __( 'File size', 'document-library-pro' ),
				'type'  => 'checkbox',
				'value' => $this->values['file_size']
			],
			'download_count' => [
				'label' => __( 'Download count', 'document-library-pro' ),
				'type'  => 'checkbox',
				'value' => $this->values['download_count']
			],
			'doc_categories' => [
				'label' => __( 'Categories', 'document-library-pro' ),
				'type'  => 'checkbox',
				'value' => $this->values['doc_categories']
			],
			'excerpt'        => [
				'label' => __( 'Excerpt/content', 'document-library-pro' ),
				'type'  => 'checkbox',
				'value' => $this->values['excerpt']
			],
			'custom_fields'  => [
				'label' => __( 'Custom fields', 'document-library-pro' ),
				'type'  => 'checkbox',
				'value' => $this->values['custom_fields']
			],
			'link'           => [
				'label' => __( 'Document link', 'document-library-pro' ),
				'type'  => 'checkbox',
				'value' => $this->values['link']
			],
		];

		return $fields;

	}

	/**
	 * {@inheritdoc}
	 */
	public function submit( array $values ) {

		$image          = isset( $values['image'] ) && $values['image'] === '1' ? '1' : '0';
		$title          = isset( $values['title'] ) && $values['title'] === '1' ? '1' : '0';
		$file_type      = isset( $values['file_type'] ) && $values['file_type'] === '1' ? '1' : '0';
		$file_size      = isset( $values['file_size'] ) && $values['file_size'] === '1' ? '1' : '0';
		$download_count = isset( $values['download_count'] ) && $values['download_count'] === '1' ? '1' : '0';
		$doc_categories = isset( $values['doc_categories'] ) && $values['doc_categories'] === '1' ? '1' : '0';
		$excerpt        = isset( $values['excerpt'] ) && $values['excerpt'] === '1' ? '1' : '0';
		$custom_fields  = isset( $values['custom_fields'] ) && $values['custom_fields'] === '1' ? '1' : '0';
		$link           = isset( $values['link'] ) && $values['link'] === '1' ? '1' : '0';

		$grid_content = [
			'image'          => $image,
			'title'          => $title,
			'file_type'      => $file_type,
			'file_size'      => $file_size,
			'download_count' => $download_count,
			'doc_categories' => $doc_categories,
			'excerpt'        => $excerpt,
			'custom_fields'  => $custom_fields,
			'link'           => $link,
		];

		Options::update_shortcode_option( [ 'grid_content' => $grid_content ] );

		return Api::send_success_response();

	}

	/**
	 * Get the grid content value.
	 *
	 * @return []
	 */
	private function get_grid_content_values() {
		$defaults = Options::get_user_shortcode_options();

		return $defaults['grid_content'];
	}
}
