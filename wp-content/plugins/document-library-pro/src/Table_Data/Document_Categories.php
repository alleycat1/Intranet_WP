<?php
namespace Barn2\Plugin\Document_Library_Pro\Table_Data;

use Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro\Data\Abstract_Table_Data;

defined( 'ABSPATH' ) || exit;
/**
 * Gets post data for the document categories column
 *
 * @package   Barn2\document-library-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Document_Categories extends Abstract_Table_Data {

	/**
	 * Constructor.
	 *
	 * @param WP_Post $post
	 * @param Table_Args $args
	 */
	public function __construct( $post, $args ) {
		parent::__construct( $post, $args->links );
	}


	/**
	 * {@inheritdoc}
	 */
	public function get_data() {
		$sep        = parent::get_separator( 'doc_categories' );
		$show_links = array_intersect( [ 'all', 'doc_categories' ], $this->links );

		return apply_filters( 'document_library_pro_data_doc_categories', parent::get_terms_for_column( 'doc_categories', $show_links, $sep ), $this->post );
	}

}
