<?php

namespace Barn2\Plugin\Document_Library_Pro\Admin\Wizard\Steps;

use Barn2\Plugin\Document_Library_Pro\Dependencies\Setup_Wizard\Api;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Setup_Wizard\Step,
	Barn2\Plugin\Document_Library_Pro\Util\Options,
	Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Util as Lib_Util;

/**
 * Filters Settings Step.
 *
 * @package   Barn2/document-library-pro
 * @author    Barn2 Plugins <info@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Filters extends Step {


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
		$this->set_id( 'filters' );
		$this->set_name( esc_html__( 'Filters', 'document-library-pro' ) );
		$this->set_description( esc_html__( 'Now, choose which information will appear in the list of documents.', 'document-library-pro' ) );
		$this->set_title( esc_html__( 'Document filters', 'document-library-pro' ) );
		$this->set_hidden( true );

		$this->values = Options::get_user_shortcode_options();
	}

	/**
	 * {@inheritdoc}
	 */
	public function setup_fields() {

		$fields = [
			'filters'        => [
				'label'       => __( 'Search filters', 'document-library-pro' ),
				'type'        => 'select',
				'options'     => [
					[
						'value'   => 'false',
						'label' => __( 'Disabled', 'document-library-pro' ),
					],
					[
						'value'   => 'true',
						'label' => __( 'Show based on columns in table', 'document-library-pro' ),
					],
					[
						'value'   => 'custom',
						'label' => __( 'Custom', 'document-library-pro' )
					]
				],
				'description' => __( 'Show dropdown menus to filter by doc_categories, doc_tags, or custom taxonomy.', 'document-library-pro' ) . ' ' . Lib_Util::barn2_link( 'kb/document-library-filters/', esc_html__( 'Read more', 'document-library-pro' ), true ),
				'value'       => $this->get_layout_value(),
			],
			'filters_custom' => [
				'label'       => __( 'Custom filters', 'document-library-pro' ),
				'type'        => 'text',
				'description' => __( 'Enter the filters as a comma-separated list.', 'document-library-pro' ),
				'value'       => ! in_array( $this->values['filters'], [ 'true', 'false', true, false ], true ) ? $this->values['filters'] : '',
				'conditions'  => [
					'filters' => [
						'op'    => 'eq',
						'value' => 'custom',
					]
				]
			],
		];

		return $fields;

	}

	/**
	 * {@inheritdoc}
	 */
	public function submit( array $values ) {
		$filters        = isset( $values['filters'] ) && in_array( $values['filters'], [ 'false', 'true', 'custom' ], true ) ? $values['filters'] : 'false';
		$filters_custom = isset( $values['filters_custom'] ) ? $values['filters_custom'] : '';

		Options::update_shortcode_option(
			[
				'filters'        => $filters,
				'filters_custom' => $filters_custom,
			]
		);

		return Api::send_success_response();
	}


	/**
	 * Get the layout value.
	 *
	 * @return string
	 */
	private function get_layout_value() {

		if ( in_array( $this->values['filters'], [ 'true', true ], true ) ) {
			return 'true';
		}

		if ( in_array( $this->values['filters'], [ 'false', false ], true ) ) {
			return 'false';
		}

		return 'custom';
	}
}
