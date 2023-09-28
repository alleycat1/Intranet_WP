<?php

namespace Barn2\Plugin\Document_Library_Pro\Admin\Wizard\Steps;

use Barn2\Plugin\Document_Library_Pro\Dependencies\Setup_Wizard\Api;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Setup_Wizard\Step,
	Barn2\Plugin\Document_Library_Pro\Util\Options,
	Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Util as Lib_Util;

/**
 * Table Settings Step.
 *
 * @package   Barn2/document-library-pro
 * @author    Barn2 Plugins <info@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Table extends Step {


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
		$this->set_id( 'table' );
		$this->set_name( esc_html__( 'Tables', 'document-library-pro' ) );
		$this->set_description( esc_html__( 'Now, choose which information will appear in the list of documents.', 'document-library-pro' ) );
		$this->set_title( esc_html__( 'Tables', 'document-library-pro' ) );
		$this->set_hidden( true );

		$this->values = Options::get_user_shortcode_options();
	}

	/**
	 * {@inheritdoc}
	 */
	public function setup_fields() {
		$fields = [
			'columns'             => [
				'label'       => __( 'Columns', 'document-library-pro' ),
				'description' => __( 'Enter the fields to include in your document tables.', 'document-library-pro' ) . ' ' . Lib_Util::barn2_link( 'kb/document-library-columns/', esc_html__( 'Read more', 'document-library-pro' ), true ),
				'type'        => 'text',
				'value'       => $this->values['columns'],
			],
			'accessing_documents' => [
				'label'       => __( 'Accessing documents', 'document-library-pro' ),
				'description' => __( 'How a user accesses documents from the ‘link’ column.', 'document-library-pro' ) . ' ' . Lib_Util::barn2_link( 'kb/document-library-settings/#accessing-documents', esc_html__( 'Read more', 'document-library-pro' ), true ),
				'type'        => 'select',
				'options'     => [
					[
						'value'   => 'link',
						'label' => __( 'Link to document', 'document-library-pro' ),
					],
					[
						'value'   => 'checkbox',
						'label' => __( 'Multi-select checkboxes', 'document-library-pro' ),
					],
					[
						'value'   => 'both',
						'label' => __( 'Both', 'document-library-pro' ),
					]
				],
				'value'       => $this->values['accessing_documents']
			],
			'lazy_load'           => [
				'title'       => __( 'Lazy load', 'document-library-pro' ),
				'type'        => 'checkbox',
				'label'       => __( 'Load the document table one page at a time', 'document-library-pro' ),
				'description' => __( 'Enable this if you will have lots of documents, otherwise leave it blank.', 'document-library-pro' ),
				'value'       => $this->values['lazy_load'],
			],
		];
		return $fields;
	}

	/**
	 * {@inheritdoc}
	 */
	public function submit( array $values ) {

		$accessing_documents = isset( $values['accessing_documents'] ) && in_array( $values['accessing_documents'], [ 'link', 'checkbox', 'both' ], true ) ? $values['accessing_documents'] : 'link';
		$columns             = isset( $values['columns'] ) ? $values['columns'] : '';
		$lazy_load           = isset( $values['lazy_load'] ) && $values['lazy_load'] === '1' ? true : false;

		Options::update_shortcode_option(
			[
				'accessing_documents' => $accessing_documents,
				'columns'             => $columns,
				'lazy_load'           => $lazy_load
			]
		);

		return Api::send_success_response();
	}

}
